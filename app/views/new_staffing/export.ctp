<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'gantt.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Staffing');



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


    public function alignRight($range) {
        $this->_sheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    public function borderT($range){
        $this->_sheet->getStyle($range)->applyFromArray(array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '000000')
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '000000')
                )
            )
        ));
    }

    public function header($range, $value = '', $center = true)
    {
        //background: 004381 | 
        //border: 185790 | 24,87,144
        $this->_sheet->setCellValue($range, $value);
        $this->_sheet->getStyle($range)->applyFromArray(array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '5fa1c4')
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '004381')
            ),
            'alignment' => array(
                'wrap' => true,
                'horizontal' => $center ? PHPExcel_Style_Alignment::HORIZONTAL_CENTER : PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF')
            )
        ));
    }
}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);

//build header
$row = 2;
$maxCol = count($data['header']) - 1;

//set width
$activeSheet->getColumnDimension('A')->setWidth(50);
$activeSheet->getRowDimension(2)->setRowHeight(25);

$col = 0;
//fill data
foreach($data['header'] as $header){
    $colName = PHPExcel_Cell::stringFromColumnIndex($col);
    $cell = $colName . $row;
    if( $col == 1 ){
        $activeSheet->getColumnDimension($colName)->setWidth(20);
    } else if( $col > 1 ){
        $activeSheet->getColumnDimension($colName)->setWidth(12);
        $PhpExcel->align($cell);
    }
    $PhpExcel->header($cell, $header);
    $col++;
}

//build data
$row = 3;   //start row
foreach($data['data'] as $title => $records){
    $titleColName = PHPExcel_Cell::stringFromColumnIndex(0);
    $titleCell = $titleColName . $row;
    $startRow = $row;   //store row start
    //build title
    //remove marker [..]
    $title = preg_replace('!^(\[\w+\]\s)(.*)!', '$2', $title);
    $PhpExcel->value($titleCell, $title);
    //apply style
    $activeSheet->getStyle($titleCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //build rows
    foreach($records as $record){
        //build cell
        $col = 1;
        $isResource = false;
        foreach($record as $value){
            $colName = PHPExcel_Cell::stringFromColumnIndex($col);
            $cell = $colName . $row;
            if( substr($value, 0, 10) == '[resource]' ){
                $isResource = true;
            }
            //normal style
            $PhpExcel->border($cell);
            //format cell, except isResource flag
            if( is_numeric($value) ){
                if( !$isResource ){
                    $activeSheet->getStyle($cell)->getNumberFormat()->setFormatCode('###0.00');
                }
            } else if( !empty($value) ) {
                //styling cell and remove flag content
                $value = str_replace(array('[resource]', '[name]'), '', $value);
                //bolder style
                $PhpExcel->borderT($cell);
            }
            if( strpos($value, '%') !== false ){
                $value = floatval($value) / 100;
                $activeSheet->getStyle($cell)->getNumberFormat()->applyFromArray( 
                    array( 
                        'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                    )
                );
            }
            //print cell
            $PhpExcel->value($cell, $value);
            $col++;
        }
        $row++;
    }
    //merge the title
    $activeSheet->mergeCells($titleCell . ':' . $titleColName . ($row-1));
    $PhpExcel->borderT($titleCell . ':' . $titleColName . ($row-1));
    //apply bold
    $activeSheet->getStyle($titleCell . ':' . $titleColName . ($row-1))->applyFromArray(
        array(
            'font'  => array(
                'bold'  => true,
            )
        )
    );
    $row++;
}


//saving section
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="staffing_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');