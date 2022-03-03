<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Risk List');



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
        'name' => __('Risk/Opportunity', true),
        'path' => 'ProjectRisk.project_risk',
        'width' => 30,
    ),
    array(
        'name' => __('Severity', true),
        'path' => 'ProjectRiskSeverity.risk_severity',
        'width' => 30,
    ),
    array(
        'name' => __('Occurrence', true),
        'path' => 'ProjectRiskOccurrence.risk_occurrence',
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
        'name' => __('Actions Manage', true),
        'path' => 'ProjectRisk.actions_manage_risk',
        'width' => 30,
    ),
    array(
        'name' => __('Date closing', true),
        'path' => 'ProjectRisk.risk_close_date',
        'width' => 20,
    )
);

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
$PhpExcel->value('A1', __('No.', true));

$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $PhpExcel->value($colName . '1', __($_fieldset['name'], true));
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
$no = 1;
$colMax = $colName;
foreach ($projectRisks as $projectRisk) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectRisk, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'ProjectRisk.risk_close_date' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'Employee' : {
                    if (!empty($_output)) {
                        $_output = sprintf('%s %s', $_output['first_name'], $_output['last_name']);
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
header('Content-Disposition: attachment;filename="projects_risk_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');