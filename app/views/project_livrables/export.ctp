<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Deliverables List');



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
        'name' => __('Name', true),
        'path' => 'ProjectLivrable.name',
        'width' => 30,
    ),
    array(
        'name' => __('Project Code', true),
        'path' => 'project_code',
        'width' => 30,
    ),
    array(
        'name' => __('Deliverable', true),
        'path' => 'ProjectLivrableCategory.livrable_cat',
        'width' => 30,
    ),
    array(
        'name' => __('Version', true),
        'path' => 'ProjectLivrable.version',
        'width' => 20,
    ),
    array(
        'name' => __('Status', true),
        'path' => 'ProjectStatus.name',
        'width' => 20,
    ),
    array(
        'name' => __('', true),
        'path' => 'ProjectLivrable.livrable_progression',
        'width' => 15,
    ),
    array(
        'name' => __('Actor', true),
        'path' => 'ProjectLivrableActor',
        'width' => 30,
    ),
    array(
        'name' => __('Date', true),
        'path' => 'ProjectLivrable.livrable_date_modify',
        'width' => 15,
    ),
    array(
        'name' => __('Time', true),
        'path' => 'ProjectLivrable.livrable_time_modify',
        'width' => 15,
    ),
    array(
        'name' => __('Unique Id', true),
        'path' => 'ProjectLivrable.id',
        'width' => 30,
    ),
    array(
        'name' => __('First and last name', true),
        'path' => 'ProjectLivrable.employee_modify',
        'width' => 30,
    ),
    array(
        'name' => __('Team', true),
        'path' => 'ProjectLivrable.team',
        'width' => 20,
    ),
);

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
// $PhpExcel->value('A1', __('No.', true));

$cIndex = 0;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $PhpExcel->value($colName . '1', $_fieldset['name']);
    $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
}
$PhpExcel->border('A1:' . $colName . '1', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));

$rIndex = 2;
$colMax = $colName;
foreach ($projectLivrables as $projectLivrable) {
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 0;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectLivrable, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'ProjectLivrable.livrable_date_delivery' :
            case 'ProjectLivrable.livrable_date_delivery_planed' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'ProjectLivrableActor' : {
                    if (!empty($_output)) {
                        $_output = implode(', ', Set::combine($_output, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')));
                    } else {
                        $_output = '';
                    }
                    break;
                }
            case 'Employee' : {
                    if (!empty($_output)) {
                        $_output = sprintf('%s %s', $_output['first_name'], $_output['last_name']);
                    }
                    break;
                }
            case 'ProjectLivrable.upload_date' : {
                    $_output = !empty($_output) ? date('d-m-Y H:i:s', $_output) : '';
                    break;
                }
            case 'project_code' : {
                    $_output = $projectCode['Project']['project_code_1'];
                    break;
                }
            case 'ProjectLivrable.livrable_progression' : {
                    $_output = $_output . ' %';
                    break;
                }
            case 'ProjectLivrable.livrable_date_modify' : {
                    $_output = date('d-m-Y', $projectLivrable['ProjectLivrable']['updated']);
                    break;
                }
            case 'ProjectLivrable.livrable_time_modify' : {
                    $_output = date('H:i:s', $projectLivrable['ProjectLivrable']['updated']);
                    break;
                }
            case 'ProjectLivrable.employee_modify' : {
                    $_output = !empty($projectLivrable['ProjectLivrable']['employee_id_upload']) && !empty($listEm[$projectLivrable['ProjectLivrable']['employee_id_upload']]) ? $listEm[$projectLivrable['ProjectLivrable']['employee_id_upload']] : '';
                    break;
                }
            case 'ProjectLivrable.team' : {
                    $_output = !empty($projectLivrable['ProjectLivrable']['employee_id_upload']) && !empty($listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]) && !empty($listPc[$listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]]) ? $listPc[$listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]] : '';
                    break;
                }
            case 'ProjectLivrable.version' : {
                    $_output = !empty($_output) && $_output != 'null' ? $_output : '';
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
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_deliverables_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
