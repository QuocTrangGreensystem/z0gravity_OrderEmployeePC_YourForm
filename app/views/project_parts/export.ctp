<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Parts List');



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
        $this->_sheet->getCell($range)->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
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
        'name' => __('Part', true),
        'path' => 'ProjectPart.title',
        'width' => 50,
    ),
    array(
        'name' => __('Description', true),
        'path' => 'ProjectPart.description',
        'width' => 70,
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
        'startcolor' => array('rgb' => '185790')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));

$rIndex = 3;
$no = 1;
$colMax = $colName;
foreach ($projectParts as $projectPart) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectPart, $_fieldset['path']);
        $PhpExcel->value($colName . $rIndex, (string) $_output);
    }
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    );
    $PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex);
    $rIndex++;
}
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_parts_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');