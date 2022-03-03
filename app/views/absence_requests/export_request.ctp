<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Synthesis');


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
	public function border($range, $style = array(), $right = false) {
		$default = array(
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				)
			),
	    	'alignment' => array(
	    		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	    	)
		);
		if( $right )
			$default['borders']['right'] = array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'FF0000')
			);
		$this->_sheet->getStyle($range)->applyFromArray(array_merge($default, $style));
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


//$activeSheet->mergeCells('B1:E1');
$PhpExcel->value('A1', $profit['name'], array(
	'font' => array(
		'bold' => true,
		'size' => 16
	),
	'alignment' => array(
		'wrap' => true,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
));

$col = 0;

$activeSheet->getRowDimension(1)->setRowHeight(25);
$activeSheet->getRowDimension(2)->setRowHeight(21);
$activeSheet->getRowDimension(3)->setRowHeight(21);

//set width for column No. & resource
$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
$activeSheet->getColumnDimension($colName)->setWidth(10);
$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
$activeSheet->getColumnDimension($colName)->setWidth(50);

$PhpExcel->header('A3', '');

//build header: no, resource, absences list/type
//merge cell for [No, resource]
$activeSheet->mergeCells('A2:A3');
$activeSheet->mergeCells('B2:B3');

$PhpExcel->header('A2', __('No.', true));
$PhpExcel->header('B2', __('Resource', true));

//draw absences list
//pr($absences);
$row = 2;
foreach($absences as $id => $absence){
	$colName = PHPExcel_Cell::stringFromColumnIndex($col);
	$colName2 = PHPExcel_Cell::stringFromColumnIndex($col+1);
	$colName3 = PHPExcel_Cell::stringFromColumnIndex($col+2);
	//merge
	$activeSheet->mergeCells($colName . $row . ':' . $colName3 . $row);
	//draw name of absence
	$output = $absence['type'];
	if($absence['begin']!='0000-00-00'){
        $beginD = explode('-',$absence['begin']);
        $startD = strtotime($beginD[2].'-'.$beginD[1].'-'.date('Y',$_start));
        if($startD>strtotime(date('m/d').'/'.date('Y',$_start))){
            $currentY = date('Y',$_start)-1;
            $startD = strtotime($beginD[2].'-'.$beginD[1].'-'.$currentY);
        }
        $startE = strtotime("+1 year", $startD);
        $startE = strtotime("-1 day", $startE);
        $output .= sprintf(__(' (from %s to %s)', true), date('d/m/Y',$startD), date('d/m/Y',$startE));
    }
    $PhpExcel->header($colName . $row, $output);
	//draw types of absence
	//row+1
	$row2 = $row + 1;
	$PhpExcel->header($colName . $row2, __('Validated', true));
	$PhpExcel->header($colName2 . $row2, __('Waiting', true));
	$PhpExcel->header($colName3 . $row2, __('Remain', true));
	$activeSheet->getColumnDimension($colName)->setWidth(15);
	$activeSheet->getColumnDimension($colName2)->setWidth(15);
	$activeSheet->getColumnDimension($colName3)->setWidth(15);
	$col += 3;
}

//draw data for each resource
asort($employees);
$i = 1;
$row = 4;

foreach($employees as $id => $name){
    $activeSheet->getRowDimension($row)->setRowHeight(21);
	$col = 0;
	//draw no.
	$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
	$PhpExcel->header($colName . $row, $i);
	//draw employee name
	$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
	$PhpExcel->header($colName . $row, strip_tags($name), false);

	//now draw data for each absence column
	foreach($absences as $absence){
		if (isset($_absences[$id][$absence['id']])) {
			$absence = $_absences[$id][$absence['id']];
		}
		//validated
		$validated = 0;
		if (isset($requests[$id][$absence['id']])) {
			$validated = $requests[$id][$absence['id']];
		}
		//
		$waiting = 0;
		if (isset($waitings[$id][$absence['id']])) {
			$waiting = $waitings[$id][$absence['id']];
		}
		//
		$remain = 0;
		if ($absence['total']) {  
			$remain = $absence['total']; 
			if (isset($requests[$id][$absence['id']])) {  
				$remain -= $requests[$id][$absence['id']];
			}
			if (isset($waitings[$id][$absence['id']])) {
				$remain -= $waitings[$id][$absence['id']];
			}
		}
		//draw data
		$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
		$PhpExcel->value($colName . $row, $validated);
		$PhpExcel->border($colName . $row);

		$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
		$PhpExcel->value($colName . $row, $waiting);
		$PhpExcel->border($colName . $row);

		$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
		$PhpExcel->value($colName . $row, $remain);
		//apply red border on the left
		$PhpExcel->border($colName . $row, array(), 1);
	}
	$row++;
	$i++;
}
$activeSheet->mergeCells('A1:' . $colName . '1');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
ob_end_clean();
ob_start();
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="absences_'.Inflector::slug($profit['name']).'_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
?>