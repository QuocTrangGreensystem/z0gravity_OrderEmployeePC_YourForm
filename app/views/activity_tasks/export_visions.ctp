<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'gantt.xls');

//$gdImage = imagecreatefrompng($tmpFile);
//// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
//$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
//$objDrawing->setName(__('Vision Project', true));
//$objDrawing->setDescription(__('Vision Project GanttSt chart', true));
//$objDrawing->setImageResource($gdImage);
//$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
//$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
//$objDrawing->setHeight($height);
//$objDrawing->setCoordinates('A2');
//$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', sprintf(__('Staffing Project of %s', true), $project));
//
//// insert logo
//$gdImage = imagecreatefromjpeg('img' . DS . 'front' . DS . 'global-logo.jpg');
//
//$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
//$objDrawing->setName('Global logo');
//$objDrawing->setImageResource($gdImage);
//$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
//$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
//$rows += 5;
//$objDrawing->setCoordinates('D' . $rows);
//$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()->setTitle('GanttSt chart'));
//

$objPHPExcel->setActiveSheetIndex(1);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Staffing');


$allWorkload = 0;
foreach($staffings as $staffing){
	if(!empty($staffing['data'])){
		foreach($staffing['data'] as $values){
			$allWorkload += !empty($values['validated']) ? $values['validated'] : 0;
		}
	}
}
$displaySummary = true;

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
		$this->_sheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
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
	public function summary($count, $col, $start, $tmpl = '%s') {
		$result = array();
		for ($i = 1; $i < $count; $i++) {
			$result[] = sprintf($tmpl, $col . $start);
			$start+=7;
		}
		return '=(' . implode('+', $result) . ')';
	}
}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);

$year = array();

// Default staffing data
if($showType == 0){
	$_titles = 'Employees';
	$default = array(
		'validated' => 0,
		'consumed' => null,
		'capacity' => 0,
		'absence' => 0,
		'totalWorkload' => 0,
		'assignEm' => 0
	);
} elseif($showType == 1){
	$_titles = 'Profit centers';
	$default = array(
		'validated' => 0,
		'consumed' => null,
		'capacity' => 0,
		'absence' => 0,
		'totalWorkload' => 0,
		'assignPc' => 0
	);
} else {
	$_titles = 'Skills';
	$default = array(
		'validated' => 0,
		'consumed' => null,
		'remains' => 0
	);
}

if (!empty($displayFields)) {
	$displayFields = explode(',', $displayFields);
} else {
	$displayFields = array();
}
$displayDefault = array_keys($default);
$displayFields = empty($displayFields) || in_array('0', $displayFields) ? array_keys($default) : $displayFields;
$displayFields = array_flip($displayFields);

$yearStart = date('Y', $start);
$_yearList = array_unique(Set::classicExtract($months, '{n}.2'));
// Set title of staffing
$_yIndex = 0;
foreach (array_merge(array(__($_titles, true), null), $_yearList, array(__('Total', true))) as $y) {
	if ($y) {
		$PhpExcel->align(PHPExcel_Cell::stringFromColumnIndex($_yIndex));
	}
	$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($_yIndex++) . '1', $y, array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => '5fa1c4')
	),
	'font' => array(
		'bold' => true,
		'color' => array(
			'rgb' => 'FFFFFF'
	))));
}

// Set column width
$activeSheet->getColumnDimension('B')->setWidth(30);
$activeSheet->getColumnDimension('A')->setWidth(30);

$_yearCount = count($_yearList);
$_col = $_yearCount + 3;

if($showType == 0){
	$_title = array(
		array('validated', __('Workload', true)),
		array('consumed', __('Consumed', true)),
		array('capacity', __('Capacity', true)),
		array('absence', __('Absence', true)),
		array('totalWorkload', __('Total Workload', true)),
		array('assignEm', __('% Assigned to employee', true))
	);
} elseif($showType == 1){
	$_title = array(
		array('validated', __('Workload', true)),
		array('consumed', __('Consumed', true)),
		array('capacity', __('Capacity', true)),
		array('absence', __('Absence', true)),
		array('totalWorkload', __('Total Workload', true)),
		array('assignPc', __('% Assigned to profit center', true))
	);
} else {
	$_title = array(
		array('validated', __('Workload', true)),
		array('consumed', __('Consumed', true)),
		array('remains', __('Remains', true))
	);
}

if ($displaySummary) {
	$staffings['summary'] = array(
		'id' => 'summary',
		'name' => __('Summary', true),
		'func' => 0,
		'data' => array()
	);
}

// Draw editable staffing
$yearDetails = array();
$summaryFx = '(IF(%1$s = "' . $this->GanttSt->na . '" , 0, %1$s))';

$sumCount = count($staffings);
foreach ($months as $data) {
	list(, $m, $y) = $data;

	if (!isset($yearDetails[$y])) {
		$yearDetails[$y] = 0;
	}
	$yearDetails[$y]++;

	$_colName = PHPExcel_Cell::stringFromColumnIndex($_col++);
	$_row = 2;

	$PhpExcel->align($_colName);
	$PhpExcel->value($_colName . '1', "$m-$y", array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => '5fa1c4')
	),
	'font' => array(
		'bold' => true,
		'color' => array(
			'rgb' => 'FFFFFF'
	))));

	$date = strtotime($y . '-' . $m . '-1');

	if ($displaySummary) {
		$staffings['summary']['data'][$date] = $default;
	}

	reset($staffings);


	while (list($key, $staffing) = each($staffings)) {
		if (!isset($year[$key][$y])) {
			$year[$key][$y] = $default;
		}
		$input = $default;
		if (isset($staffing['data'][$date])) {
			$input = array_merge($input, $staffing['data'][$date]);
		}

		$fcolor = $this->GanttSt->parseData($input, $key === 'summary' ? null : true) ? 'FC5C6F' : '68A8CA';
		$bgcolor = '';

		$assigns = ($allWorkload == 0) ? 0 : round(($input['validated']/$allWorkload)*100, 2);
		if ($key === 'summary') {
			$bgcolor = '';
			
			$PhpExcel->value($_colName . ($_row), $PhpExcel->summary($sumCount, $_colName, 2), ($input['validated'] && $input['validated'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor );
			$PhpExcel->value($_colName . ($_row + 1), $PhpExcel->summary($sumCount, $_colName, 3, $summaryFx), ($input['consumed'] && $input['consumed'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor );
			$PhpExcel->value($_colName . ($_row + 2), $PhpExcel->summary($sumCount, $_colName, 4), ($input['capacity'] && $input['capacity'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor );
			$PhpExcel->value($_colName . ($_row + 3), $PhpExcel->summary($sumCount, $_colName, 5), ($input['absence'] && $input['absence'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor );
			$PhpExcel->value($_colName . ($_row + 4), $PhpExcel->summary($sumCount, $_colName, 6), ($input['totalWorkload'] && $input['totalWorkload'] != $this->GanttSt->na) ? $fcolor : $bgcolor );
			$PhpExcel->value($_colName . ($_row + 5), $PhpExcel->summary($sumCount, $_colName, 7), ($assigns && $assigns != $this->GanttSt->na) ? '68A8CA' : $bgcolor );
		} else {
			$PhpExcel->value($_colName . ($_row), $input['validated'], ($input['validated'] && $input['validated'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor);
			$PhpExcel->value($_colName . ($_row + 1), $input['consumed'], ($input['consumed'] && $input['consumed'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor);
			$PhpExcel->value($_colName . ($_row + 2), $input['capacity'], ($input['capacity'] && $input['capacity'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor);
			$PhpExcel->value($_colName . ($_row + 3), $input['absence'], ($input['absence'] && $input['absence'] != $this->GanttSt->na) ? '68A8CA' : $bgcolor);
			$PhpExcel->value($_colName . ($_row + 4), $input['totalWorkload'], ($input['totalWorkload'] && $input['totalWorkload'] != $this->GanttSt->na) ? $fcolor : $bgcolor);
			$PhpExcel->value($_colName . ($_row + 5), $assigns, ($assigns && $assigns != $this->GanttSt->na) ? '68A8CA' : $bgcolor);
		}
		// title
		foreach ($_title as $k => $v) {
			$PhpExcel->value('B' . ($_row + $k), $v[1], $bgcolor);
			if (!isset($displayFields[$v[0]])) {
				$activeSheet->getRowDimension($_row + $k)->setVisible(false);
			}
		}

		$PhpExcel->value('A' . ($_row), $staffing['name'], $bgcolor);
		$activeSheet->mergeCells('A' . ($_row) . ':A' . ($_row + 5));
		$_row+=7;
		
		foreach ($default as $k => $v) {
			if($k === 'assignEm' || $k === 'assignPc'){
				$input[$k] = $assigns;
			}
			$year[$key][$y][$k] += $input[$k];
			if ($displaySummary) {
				$staffings['summary']['data'][$date][$k] += $input[$k];
			}
		}
	}
}

// Draw summary detail staffing
$_row = 2;
$_yearCol = PHPExcel_Cell::stringFromColumnIndex($_yearCount + 1);

$_lastYear = null;
foreach ($staffings as $key => $staffing) {
	$bgcolor = $key === 'summary' ? '' : '';
	$total = $default;
	$bgcolorValidatedTotal = $bgcolorConsumedTotal = $bgcolorRemainTotal = $bgcolorCapacityTotal = $bgcolorTotalWorkloadTotal = $bgColorLineLastTotal = '';
	$_yIndex = 2;
	$_col = $_yearCount + 3;
	foreach ($year[$key] as $_y => $_year) {
		$bgcolorValidated = $bgcolorConsumed = $bgcolorRemain = $bgcolorCapacity = $bgcolorTotalWorkload = $bgColorLineLast = '';
		$fcolor = $this->GanttSt->parseData($_year) ? 'FC5C6F' : '';
		if($key === 'summary'){
			$fcolor = $this->GanttSt->parseData($_year) ? 'FC5C6F' : '';
		}
		if(!empty($_year['validated']) && $_year['validated'] > 0){
			$bgcolorValidated = '68A8CA';
			if(empty($bgcolorValidatedTotal)){
				$bgcolorValidatedTotal = $bgcolorValidated;
			}
		}
		if(!empty($_year['consumed']) && ($_year['consumed'] != $this->GanttSt->na || $_year['consumed'] != 0)){
			$bgcolorConsumed = '68A8CA';
			if(empty($bgcolorConsumedTotal)){
				$bgcolorConsumedTotal = $bgcolorConsumed;
			}
		}
		if(!empty($_year['absence']) && $_year['absence'] > 0){
			$bgcolorRemain = '68A8CA';
			if(empty($bgcolorRemainTotal)){
				$bgcolorRemainTotal = $bgcolorRemain;
			}
		}
		if(!empty($_year['capacity']) && $_year['capacity'] > 0){
			$bgcolorCapacity = '68A8CA';
			if(empty($bgcolorCapacityTotal)){
				$bgcolorCapacityTotal = $bgcolorCapacity;
			}
		}
		if(!empty($_year['totalWorkload']) && $_year['totalWorkload'] > 0){
			$bgcolorTotalWorkload = '68A8CA';
			if(empty($bgcolorTotalWorkloadTotal)){
				$bgcolorTotalWorkloadTotal = $bgcolorTotalWorkload;
			}
		}
		if(!empty($_year['assignEm']) && $_year['assignEm'] > 0){
			$bgColorLineLast = '68A8CA';
			if(empty($bgColorLineLastTotal)){
				$bgColorLineLastTotal = $bgColorLineLast;
			}
		}
		if(!empty($_year['assignPc']) && $_year['assignPc'] > 0){
			$bgColorLineLast = '68A8CA';
			if(empty($bgColorLineLastTotal)){
				$bgColorLineLastTotal = $bgColorLineLast;
			}
		}
		$_colName = PHPExcel_Cell::stringFromColumnIndex($_yIndex++);
		if ($_lastYear != $_y) {
			$_lastYear = $_y;
			$_monthName = array(PHPExcel_Cell::stringFromColumnIndex($_col), PHPExcel_Cell::stringFromColumnIndex(($_col+=$yearDetails[$_y]) - 1));
		}
		$PhpExcel->value($_colName . ($_row), '=SUM(' . $_monthName[0] . ($_row) . ':' . $_monthName[1] . ($_row) . ')', $bgcolorValidated);
		$PhpExcel->value($_colName . ($_row + 1), '=SUM(' . $_monthName[0] . ($_row + 1) . ':' . $_monthName[1] . ($_row + 1) . ')', $bgcolorConsumed);
		$PhpExcel->value($_colName . ($_row + 2), '=SUM(' . $_monthName[0] . ($_row + 2) . ':' . $_monthName[1] . ($_row + 2) . ')', $bgcolorCapacity);
		$PhpExcel->value($_colName . ($_row + 3), '=SUM(' . $_monthName[0] . ($_row + 3) . ':' . $_monthName[1] . ($_row + 3) . ')', $bgcolorRemain);
		$PhpExcel->value($_colName . ($_row + 4), '=SUM(' . $_monthName[0] . ($_row + 4) . ':' . $_monthName[1] . ($_row + 4) . ')', $bgcolorTotalWorkload);
		$PhpExcel->value($_colName . ($_row + 5), '=SUM(' . $_monthName[0] . ($_row + 5) . ':' . $_monthName[1] . ($_row + 5) . ')', $bgColorLineLast);
		foreach ($default as $k => $v) {
			$total[$k] += $_year[$k];
		}
	}

	$_colName = PHPExcel_Cell::stringFromColumnIndex($_yIndex);

	$fcolor = $this->GanttSt->parseData($total) ? 'FC5C6F' : '';
	if($key === 'summary'){
		$fcolor = $this->GanttSt->parseData($_year) ? 'FC5C6F' : '';
	}
	$PhpExcel->value($_colName . ($_row), '=SUM(C' . $_row . ':' . $_yearCol . $_row . ')', $bgcolorValidatedTotal);
	$PhpExcel->value($_colName . ($_row + 1), '=SUM(C' . ($_row + 1) . ':' . $_yearCol . ($_row + 1) . ')', $bgcolorConsumedTotal);
	$PhpExcel->value($_colName . ($_row + 2), '=SUM(C' . ($_row + 2) . ':' . $_yearCol . ($_row + 2) . ')', $bgcolorCapacityTotal);
	$PhpExcel->value($_colName . ($_row + 3), '=SUM(C' . ($_row + 3) . ':' . $_yearCol . ($_row + 3) . ')', $bgcolorRemainTotal);
	$PhpExcel->value($_colName . ($_row + 4), '=SUM(C' . ($_row + 4) . ':' . $_yearCol . ($_row + 4) . ')', $bgcolorTotalWorkloadTotal);
	$PhpExcel->value($_colName . ($_row + 5), '=SUM(C' . ($_row + 5) . ':' . $_yearCol . ($_row + 5) . ')', $bgColorLineLastTotal);
	$_row+=7;
}
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="activitys_visions_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
unlink($tmpFile);
exit;
?>