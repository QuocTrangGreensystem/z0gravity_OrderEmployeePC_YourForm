<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Issue List');



/* PhpExcel Set function */

Class PhpExcelSet {

    protected $_sheet = null;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct(PHPExcel_Worksheet $activeSheet) {
        $this->_sheet = & $activeSheet;
    }

    /**
     * Set alignment.
     *
     * @return void
     */
    public function align($range) {
        $this->_sheet->getStyle($range)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    /**
     * Set cell range value and style.
     *
     * @return void
     */
    public function value($range, $value = null, $style = array()) {
        $this->_sheet->setCellValue($range, $value);
        if ($style) {
            if (is_string($style)) {
                $style = array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $style)
                );
                $this->_sheet->getStyle($range)->getFill()->applyFromArray($style);
            } else {
                $this->_sheet->getStyle($range)->applyFromArray($style);
            }
        }
    }

    /**
     * Set cell range value and style.
     *
     * @return void
     */
    public function border($range, $style = array()) {
        $default = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        $this->_sheet->getStyle($range)->applyFromArray(array_merge($default, $style));
    }

}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);

$fieldset = array(
    array(
        'name' => __('Blocking', true),
        'path' => 'ProjectIssue.project_issue_color_id',
        'width' => 10,
    ),
    array(
        'name' => __('Issue', true),
        'path' => 'ProjectIssue.project_issue_problem',
        'width' => 30,
    ),
    array(
        'name' => __('Severity', true),
        'path' => 'ProjectIssueSeverity.issue_severity',
        'width' => 30,
    ),
    array(
        'name' => __('Status', true),
        'path' => 'ProjectIssueStatus.issue_status',
        'width' => 30,
    ),
    array(
        'name' => __('Assign to', true),
        'path' => 'Employee',
        'width' => 30,
    ),
    array(
        'name' => __('Actions Related', true),
        'path' => 'ProjectIssue.issue_action_related',
        'width' => 30,
    ),
    array(
        'name' => __('Delivery Date', true),
        'path' => 'ProjectIssue.delivery_date',
        'width' => 20,
    ),
    array(
        'name' => __('Created Date', true),
        'path' => 'ProjectIssue.date_open',
        'width' => 20,
    ),
    array(
        'name' => __('Date closing', true),
        'path' => 'ProjectIssue.date_issue_close',
        'width' => 20,
    )
);

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
$PhpExcel->value('A2', __('No.', true));

$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $PhpExcel->value($colName . '2', __($_fieldset['name'], true));
    $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
}
$PhpExcel->border('A2:' . $colName . '2', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));

$rIndex = 3;
$no = 1;
$colMax = $colName;
foreach ($projectIssues as $projectIssue) {
    $issueId = $projectIssue['ProjectIssue']['id'];
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectIssue, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'ProjectIssue.date_issue_close' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'ProjectIssue.delivery_date' : {
                    $date = !empty($projectIssue['ProjectIssue']['delivery_date']) ? $projectIssue['ProjectIssue']['delivery_date'] : '';
                    $curent = time();
                    if( file_exists('./img/extjs/icon-triangle.png') && !empty($projectIssue['ProjectIssue']['project_issue_status_id']) && !empty($issueStatus[$projectIssue['ProjectIssue']['project_issue_status_id']]) && ($issueStatus[$projectIssue['ProjectIssue']['project_issue_status_id']] != 'CLOS') && !empty($date) && ($curent > strtotime($date)) ){
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Date Red');
                        $objDrawing->setDescription('Date Red');
                        $objDrawing->setPath('./img/extjs/icon-triangle.png');
                        $objDrawing->setCoordinates($colName . $rIndex);
                        $objDrawing->setWidth(20);
                        $objDrawing->setHeight(22);
                        $objDrawing->setOffsetX(10);
                        $objDrawing->setWorksheet($activeSheet);
                    }
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'ProjectIssue.date_open' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'Employee' : {
                    $_output = '';
                    if (!empty($listReference[$issueId])) {
                        foreach ($listReference[$issueId] as $value) {
                            $_output .= $_output != '' ? ', ' : '';
                            if($value['is_profit_center'] == 0){
                                $_output .= !empty($value['reference_id']) && !empty($listEmployee[$value['reference_id']]) ? $listEmployee[$value['reference_id']] : '';
                            } else {
                                $_output .= !empty($value['reference_id']) && !empty($listProfit[$value['reference_id']]) ? $listProfit[$value['reference_id']] : '';
                            }
                        }
                    }
                    break;
                }
            case 'ProjectIssue.project_issue_color_id' : {
                $color = !empty($projectIssueColor[$_output]) ? $projectIssueColor[$_output] : $colorDefault;
                $color = str_replace('#', '', $color);
                $_output = '';
                $PhpExcel->border($colName . $rIndex, array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => $color)
                    )));
                break;
            }
            case 'ProjectIssueSeverity.issue_severity' : {
                $severityId = $projectIssue['ProjectIssue']['project_issue_severity_id'];
                if(!empty($severityColor[$severityId])){
                    $PhpExcel->border($colName . $rIndex, array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array('rgb' => str_replace('#', '', $severityColor[$severityId]))
                        )));
                }
                break;
            }
        }
        $PhpExcel->value($colName . $rIndex, (string) $_output);
    }
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    );
    $PhpExcel->align('A' . $rIndex . ":" . $colName . $rIndex);
    $PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex, array(
    'fill' => array(),
    'font' => array(
        'size' => 11,
        'bold' => false,
        )
    ));
    $rIndex++;
}
//exit();
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_issue_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
