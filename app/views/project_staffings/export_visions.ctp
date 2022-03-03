<?php
/* 
Non affectée(s) day xuong duoi cung neu khong phai fix code 
*/
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'gantt.xls');

// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName(__('Vision Project', true));
$objDrawing->setDescription(__('Vision Project GanttSt chart', true));
$objDrawing->setImageResource($image);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight($height);
$objDrawing->setCoordinates('A2');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()->setTitle('Gantt chart'));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', sprintf(__('Staffing Project of %s', true), $project));
/*
// insert logo
$gdImage = imagecreatefromjpeg('img' . DS . 'front' . DS . 'global-logo.jpg');

$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Global logo');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$rows += 5;
$objDrawing->setCoordinates('D' . $rows);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()->setTitle('GanttSt chart'));
*/

$objPHPExcel->setActiveSheetIndex(1);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Staffing');

$staffings = $staffingss;
$has_na = 0;
$allWorkload = 0;
foreach($staffings as $staffing){
	if( $staffing['id'] === '999999999') $has_na = 1;
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
	public function summary($showType, $count, $col, $start, $tmpl = '%s', $_downRow = 0) {
		$result = array();
		for ($i = 1; $i < $count; $i++) {
			$result[] = sprintf($tmpl, $col . $start);
			$start += $_downRow;
		}
        //pr('=(' . implode('+', $result) . ')');
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
//$_yearList = range($yearStart, $yearStart + (date('Y', $end) - $yearStart));
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
$sumCount = count($staffings) - $has_na; // - not affect
$downRow = !empty($displayFields) ? count($displayFields) + 1 : 0;
$totalRowFields = array(); // fields will be displayed on total rows
if( isset($displayFields['validated'])) $totalRowFields[] = 'validated';
if( isset($displayFields['consumed'])) $totalRowFields[] = 'consumed';
$naFields = array(); // fields will be displayed on Not effect rows
if( isset($displayFields['validated'])) $naFields[] = 'validated';
if( isset($displayFields['assignEm'])) $naFields[] = 'assignEm';
if( isset($displayFields['assignPc'])) $naFields[] = 'assignPc';
foreach ($months as $data) {
	list(, $m, $y) = $data;

	if (!isset($yearDetails[$y])) {
		$yearDetails[$y] = 0;
	}
	$yearDetails[$y]++;

	$_colName = PHPExcel_Cell::stringFromColumnIndex($_col++);
	// $_row = 2 + $downRow;
	/* #616 only show consumed and workload for summary */ 
	$_row = $displaySummary ? ( count($totalRowFields) + 3 ) : 2; 

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

	$tmpFields = array();
	while (list($key, $staffing) = each($staffings)) {
		$is_summary = ($key === 'summary');
		$is_na = ($staffing['id'] === '999999999');
		if (!isset($year[$key][$y])) {
			$year[$key][$y] = $default;
		}
		$input = $default;
		if (isset($staffing['data'][$date])) {
			$input = array_merge($input, $staffing['data'][$date]);
		}

		$fcolor = $this->GanttSt->parseData($input, $is_summary ? null : true) ? 'FC5C6F' : '68A8CA';
		$bgcolor = '';

		$assigns = ($allWorkload == 0) ? 0 : round(($input['validated']/$allWorkload)*100, 2);
        if($showType == 0){
            $input['assignEm'] = $assigns;
        } else {
            $input['assignPc'] = $assigns;
        }
		if ($is_summary) {
            $tmp_row = $_row;
            $_row = 2;
            for($i = 0; $i < count($totalRowFields); $i++){
                $field = $totalRowFields[$i];
                $summaryFx = ($field == 'consumed') ? $summaryFx : '%s';
				$_sumCount = ($field != 'validated') ? $sumCount : ($sumCount + 1 );
				$bg_color = (  ($input[$field] && $input[$field] != $this->GanttSt->na) ? $cl : $bgcolor);
                $PhpExcel->value($_colName . ($_row + $i), $PhpExcel->summary($showType, $_sumCount, $_colName, $_row + $i + 3, $summaryFx, $downRow), $bg_color);
            }
            $_row = $tmp_row;
		} else {
		    $tmpFields = array_keys($displayFields);
			$_downRow = $downRow;
			if($is_na){
				$tmpFields = $naFields;
				$_downRow = count($tmpFields) + 1;
			}
            for($i = 0; $i < ($_downRow-1); $i++){
                $fields = array_shift($tmpFields);
                $cl = ($fields == 'totalWorkload') ? $fcolor : '68A8CA';
                $PhpExcel->value($_colName . ($_row + $i), $input[$fields], ($input[$fields] && $input[$fields] != $this->GanttSt->na) ? $cl : $bgcolor);
            }
		}
        $tmp_row_two = $_row;
        if($is_summary){
            $_row = 2;
        }
		// title
        $count = 0;		
		if( $is_summary){
			$tmpFields = $totalRowFields;
		}elseif($is_na){
			$tmpFields = $naFields;
		}else{
			$tmpFields = array_keys($displayFields);
		}
		if( count($tmpFields)){
			foreach ($_title as $k => $v) {
				if(in_array($v[0], $tmpFields)){
					if( $is_na && ( $v[0] == 'assignEm' || $v[0] == 'assignPc' )){
						$v[1] = __('% not affected', true);
					}
					$PhpExcel->value('B' . ($_row + $count), $v[1], $bgcolor);
					$count++;
				}
			}
			$PhpExcel->value('A' . ($_row), $staffing['name'], $bgcolor);
			$activeSheet->mergeCells('A' . ($_row) . ':A' . ($_row + count($tmpFields) - 1 ));
			$_row = $tmp_row_two;
			$_row += $downRow;

			foreach ($default as $k => $v) {
				$year[$key][$y][$k] += $input[$k];
				if ($displaySummary) {
					$staffings['summary']['data'][$date][$k] += $input[$k];
				}
			}
		}
	}
	// exit;
}
// Draw summary detail staffing: Total, Totaal by year
/* #616 only show consumed and workload for summary */ 
$_row = $displaySummary ? ( count($totalRowFields) + 3 ) : 2; 

$_yearCol = PHPExcel_Cell::stringFromColumnIndex($_yearCount + 1);
$_lastYear = null;
foreach ($staffings as $key => $staffing) {
	$is_summary = ($key === 'summary');
	$is_na = ($staffing['id'] === '999999999');
	$bgcolor = '';
	$total = $default;
	$bgcolorValidatedTotal = $bgcolorConsumedTotal = $bgcolorRemainTotal = $bgcolorAbsenceTotal = $bgcolorCapacityTotal = $bgcolorTotalWorkloadTotal = $bgColorLineLastTotal = '';
	$_yIndex = 2;
	$_col = $_yearCount + 3;
	$_downRow = ( ($is_summary) ? (count($totalRowFields) + 1) : ( ($is_na ) ? (count($naFields) + 1) : $downRow ));
    if($is_summary){
		$tmp_row = $_row;
        $_row = 2;
	}
	foreach ($year[$key] as $_y => $_year) {
		$bgcolorValidated = $bgcolorConsumed = $bgcolorRemain = $bgcolorAbsence = $bgcolorCapacity = $bgcolorTotalWorkload = $bgColorLineLast = '';
		$fcolor = $this->GanttSt->parseData($_year) ? 'FC5C6F' : '';
		if($is_summary){
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
			$bgcolorAbsence = '68A8CA';
			if(empty($bgcolorAbsenceTotal)){
				$bgcolorAbsenceTotal = $bgcolorAbsence;
			}
		}
		if(!empty($_year['remains']) && $_year['remains'] > 0){
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
        for($i = 0; $i < ($_downRow-1); $i++){
            $PhpExcel->value($_colName . ($_row + $i), '=SUM(' . $_monthName[0] . ($_row + $i) . ':' . $_monthName[1] . ($_row + $i) . ')', '68A8CA');
        }
		foreach ($default as $k => $v) {
			$total[$k] += $_year[$k];
		}
	}

	$_colName = PHPExcel_Cell::stringFromColumnIndex($_yIndex);

	$fcolor = $this->GanttSt->parseData($total) ? 'FC5C6F' : '';
	if($is_summary){
		$fcolor = $this->GanttSt->parseData($_year) ? 'FC5C6F' : '';
	}
    for($i = 0; $i < ($_downRow-1); $i++){
        $PhpExcel->value($_colName . ($_row + $i), '=SUM(C' . ($_row + $i) . ':' . $_yearCol . ($_row + $i) . ')', '68A8CA');
    }
    $_row += $downRow;
    if($is_summary){
        $_row = $tmp_row;
	}
}
// die;
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_visions_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit;
?>