<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
/**
 * Chuyen fil CSV sang file excelt de export
 */

$savePart = $part.$filename;
$objReaderCSV = PHPExcel_IOFactory::createReader('CSV');
$objReaderCSV->setInputEncoding('UTF-8');
$objPHPExcel = $objReaderCSV->load($savePart);

/**
 * Ghi tieu de
 */
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = &$objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Activity Export');
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
$activityExports = $fieldset;
$cIndex = 0;
$rIndex = 1;
foreach($fieldset as $key => $name){
	$colName = PHPExcel_Cell::stringFromColumnIndex($cIndex);
	$PhpExcel->value($colName . $rIndex, __($name, true));
	$width= 'auto';
	switch($key){
		case 'profit_center': {$width= 55; break;};
		case 'task_name': {$width= 55; break;};
		case 'message': {$width = 100; break;};
		case 'week_message': {$width = 100; break;};
	}
	if( $width == 'auto'){
		$activeSheet->getColumnDimension($colName)->setAutoSize(true);
	}else{
		$activeSheet->getColumnDimension($colName)->setWidth($width);
	}
	$PhpExcel->align('A1:' . $colName . '1');
    $PhpExcel->border('A1:' . $colName . '1', array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array('rgb' => '5fa1c4')
		),
		'font' => array(
			'bold' => true,
			'color' => array(
				'rgb' => 'FFFFFF'
		)))
	);
	$cIndex++;
}


$fileNameExport = str_replace('csv', 'xls', $filename);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($part.$fileNameExport);
$data = array(
	'result' => true,
	'downloadURL' => $this->Html->url('/shared/'. $employeeLoginId . '/' . $fileNameExport),
	'message' => '',
);
die( json_encode($data));
exit;