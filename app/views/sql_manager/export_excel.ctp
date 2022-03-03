<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Sql Result');



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

    $col = 0;
    foreach ($columns as $key => $value) {

        $colName = PHPExcel_Cell::stringFromColumnIndex($col);
        $PhpExcel->value($colName . '1', $value);
        $col++;
    }
    $PhpExcel->border('A1:' . $colName . '1', array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '5fa1c4')
        ),
        'font' => array(
            'size' => 12,
            'bold' => true,
            'color' => array(
                'rgb' => 'FFFFFF'
    ))));

    //set value cell body
    $objPHPExcel->getActiveSheet()->fromArray($datas, null, 'A2');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_end_clean();
    ob_start();
    header('Content-Type: application/vnd.ms-excel');
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="sql_results_' . date('H_i_s_d_m_Y') . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');
?>
