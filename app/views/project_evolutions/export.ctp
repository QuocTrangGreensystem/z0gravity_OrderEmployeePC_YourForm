<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Evolution List');



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
        'name' => __('Evolution', true),
        'path' => 'ProjectEvolution.project_evolution',
        'width' => 30,
    ),
    array(
        'name' => __('Type Evolution', true),
        'path' => 'ProjectEvolutionType.project_type_evolution',
        'width' => 30,
    ),
    array(
        'name' => __('Required By', true),
        'path' => 'ProjectEvolution.evolution_applicant',
        'width' => 20,
    ),
    array(
        'name' => __('Date validated', true),
        'path' => 'ProjectEvolution.evolution_date_validated',
        'width' => 15,
    ),
    array(
        'name' => __('Validated By', true),
        'path' => 'ProjectEvolution.evolution_validator',
        'width' => 20,
    ),
    array(
        'name' => __('Impact', true),
        'path' => 'ProjectEvolutionImpactRefer',
        'width' => 30,
    ),
    array(
        'name' => __('Budget', true),
        'path' => 'ProjectEvolution.supplementary_budget',
        'width' => 15,
    ),
    array(
        'name' => __('ManDay', true),
        'path' => 'ProjectEvolution.man_day',
        'width' => 15,
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
$PhpExcel->align('A1:' . $colName . '1');
$PhpExcel->border('A1:' . $colName . '1', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '185790')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));

$rIndex = 2;
$no = 1;
$colMax = $colName;
foreach ($projectEvolutions as $projectEvolution) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectEvolution, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'ProjectEvolution.evolution_date_validated' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'ProjectEvolution.evolution_applicant' :
            case 'ProjectEvolution.evolution_validator' : {
                    if (!empty($employees[$_output])) {
                        $_output = $employees[$_output];
                    }
                    break;
                }
            case 'ProjectEvolutionImpactRefer' : {
                    if (!empty($_output)) {
                        $_output = implode(', ', Set::classicExtract($_output, '{n}.ProjectEvolutionImpact.evolution_impact'));
                    } else {
                        $_output = '';
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
header('Content-Disposition: attachment;filename="projects_evolution_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');