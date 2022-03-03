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
$activeSheet->setTitle(__('Team Workload', true));



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

    function number($row, $cell){
        $this->_sheet->getStyle($cell)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $this->_sheet->getRowDimension($row)->setRowHeight(50);
    }

    function middle($range){
        $this->_sheet->getStyle($range)->getAlignment()
            ->setWrapText(true)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    function em($range){
        $this->_sheet->getStyle($range)->getFont()->setItalic($range, true);
    }
    function b($range){
        $this->_sheet->getStyle($range)->getFont()->setBold($range, true);
    }

    function task($range){
        $this->_sheet->getStyle($range)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->_sheet->getStyle($range)->applyFromArray(array(
            'font'  => array(
                //'bold'  => true,
                'color' => array('rgb' => '013d74')
            )
        ));
    }
}
function x_number($value){
    $value = explode(',', str_replace('[number]', '', $value));
    if( !empty($value[2])){
        $val = $value[0] . "\n" . $value[1] . "\n" . $value[2];
    }else{
        $val = $value[0] . "\n" . $value[1];
    }
    return $val;
}


// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);

//build header
$row = $startRow = 2;
$maxCol = count($data['header']) - 1;

//set width
$activeSheet->getColumnDimension('A')->setWidth(50);
$activeSheet->getColumnDimension('B')->setWidth(30);
$activeSheet->getColumnDimension('C')->setWidth(40);
$activeSheet->getColumnDimension('D')->setWidth(15);
$activeSheet->getRowDimension(2)->setRowHeight(30);

//fill header
foreach($data['header'] as $header){
    $col = 0;
    foreach($header as $hrow){
        $colName = PHPExcel_Cell::stringFromColumnIndex($col);
        $cell = $colName . $row;
        if( $row == $startRow ){
            $hrow = str_replace('[label]', '', $hrow);
            $PhpExcel->header($cell, $hrow);
            // if( $col == 0 ){
            //     $activeSheet->mergeCells($cell . ':C' . $row);
            // }
            if( $col > 3 ){
                $activeSheet->getColumnDimension($colName)->setWidth(12);
            }
        } else {
            if( strpos($hrow, '[label]') !== false ){
                //style cho dam len
                $PhpExcel->b($cell);
                //xoa
                $hrow = str_replace('[label]', '', $hrow);
                //merge
                if( $col == 0 ){
                    $activeSheet->mergeCells($cell . ':C' . $row);
                }
            } else if( strpos($hrow, '[number]') !== false ){
                //dual value workload,consume
                if( strpos($hrow, ',') !== false ){
                    $hrow = x_number($hrow, $activeSheet);
                    $PhpExcel->number($row, $cell);
                } else {
                    $hrow = str_replace('[number]', '', $hrow);
                    $PhpExcel->alignRight($cell);
                }
            }
            $PhpExcel->middle($cell);
            $PhpExcel->border($cell);
            if($col >= 3 && $col <= 5){
                $PhpExcel->b($cell);
            }
            $PhpExcel->value($cell, $hrow);
        }
        $col++;
    }
    $row++;
}

//fill data
$row++;

foreach($data['data'] as $project){
    $titleColName = PHPExcel_Cell::stringFromColumnIndex(0);
    $titleCell = $titleColName . $row;
    $startRow = $row;   //store row start
    //build title
    //remove marker [..]
    $projectName = preg_replace('!^(\[\w+\]\s)(.*)!', '$2', $project['name']);
    $PhpExcel->b($titleCell);
    $PhpExcel->value($titleCell, $projectName);
    //apply style
    $activeSheet->getStyle($titleCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $max = count($project['rows']) - 1;
    $i = 0;
    $count = 0;

    foreach($project['rows'] as $rows){

        $col = 1;
        foreach($rows as $value){
            $cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            //priority
            if( strpos($value, '[priority]') !== false ){
                $value = str_replace('[priority]', '', $value);
                //
                if( $col == 1 ){
                    $PhpExcel->em($cell);
                } else if( $col == 2 ){
                    //merge
                    $activeSheet->mergeCells('B' . $row . ':C' . $row);
                }
            }
            //phase
            else if( strpos($value, '[phase]') !== false ){
                $value = str_replace('[phase]', '', $value);
                //
                if( $col == 1 ){
                    $PhpExcel->b($cell);
                }
                if( $i == $max || $value != '' ){
                    $start = $row - $count - 1;
                    $end = $row - 1;
                    if( $start < $end ){
                        $activeSheet->mergeCells('B' . $start . ':B' . $end);
                    }
                    $count = 0;
                }
                else {
                    $count++;
                }
            }
            //task
            else if( strpos($value, '[task]') !== false ){
                $value = str_replace('[task]', '', $value);
                $PhpExcel->task($cell);
            }
            else if( strpos($value, '[number]') !== false ){
                $value = x_number($value, $activeSheet);
                $PhpExcel->number($row, $cell);
            }

            $PhpExcel->middle($cell);
            $PhpExcel->border($cell);
            if($col >= 3 && $col <= 5){
                $PhpExcel->b($cell);
            }
            $PhpExcel->value($cell, $value);

            $col++;
        }
        $row++;
        $i++;
    }
    //merge project name
    $activeSheet->mergeCells($titleCell . ':' . $titleColName . ($row-1));
    $activeSheet->getStyle($titleCell . ':' . $titleColName . ($row-1))->getAlignment()->setWrapText(true);
    $PhpExcel->border($titleCell . ':' . $titleColName . ($row-1));

    $row++;
}
// die;
//saving section
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="team_workload_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
