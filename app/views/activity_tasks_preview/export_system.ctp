<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

/*
*/
$extra = array();
if( !(!$summary && !$isCheck) ){
	if( $dateType == 'month' ){
	    $ddasdad = new DateTime();
	    $ddasdad->setTimestamp($endDateFilter);
	    $ddasdad->modify('last day of this month');
	    $ed = $ddasdad->format('d-m-Y');
	    $st = date('d-m-Y', $startDateFilter);
	} else {
	    $ed = $_GET['aEndDate'];
	    $st = $_GET['aStartDate'];
	}
	$family = isset($_GET['aFamily']) ? implode(',', $_GET['aFamily']) : '';
	$subfamily = isset($_GET['aSub']) ? implode(',', $_GET['aSub']) : '';
	$activity = isset($_GET['aName']) ? implode(',', $_GET['aName']) : '';
	$customer = isset($_GET['aCustomer']) ? implode(',', $_GET['aCustomer']) : '';
	$pc = isset($_GET['aPC']) ? implode(',', $_GET['aPC']) : '';
	$resource = isset($_GET['aEmployee']) ? implode(',', $_GET['aEmployee']) : '';
	$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
	$onlysummary = !$showType && !$resource;
	$is_resource = !$showType && $resource;
	if( $is_resource ){
		$resource = array();
		foreach ($staffings as $key => $value) {
			if( !empty($value['isEmployee']) ){
				$resource[] = $value['employee_id'];
			}
		}
		$resource = implode(',', $resource);
	}
	$args = array(
		'type' => $showType,
		'view_by' => $dateType,
		'start_date' => $st,
		'end_date' => $ed,
		'summary' => $summary,
		'family' => $family,
		'subfamily' => $subfamily,
		'activity' => $activity,
		'customer' => $customer,
		'pc' => $pc,
		'resource' => $resource,
		'priority' => $priority
	);
	if( $onlysummary )$args['only_sum'] = 1;
    if( $is_resource )$args['is_resource'] = 1;
	$extra = $this->requestAction(array('controller' => 'new_staffing', 'action' => 'index', '?' => $args));
}

/*

*/

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

$settings = array(
	'staffing_by_pc_display_absence' => array('absence', __('Absence', true), isset($companyConfigs['staffing_by_pc_display_absence']) ? $companyConfigs['staffing_by_pc_display_absence'] : 0),
	'staffing_by_pc_display_real_capacity' => array('capacity', __('Real capacity', true), isset($companyConfigs['staffing_by_pc_display_real_capacity']) ? $companyConfigs['staffing_by_pc_display_real_capacity'] : 0),
	'staffing_by_pc_display_real_fte' => array('real_fte', __('FTE +/- Real', true), isset($companyConfigs['staffing_by_pc_display_real_fte']) ? $companyConfigs['staffing_by_pc_display_real_fte'] : 0),
	'staffing_by_pc_display_theoretical_capacity' => array('theo_capacity', __('Theoretical capacity', true), isset($companyConfigs['staffing_by_pc_display_theoretical_capacity']) ? $companyConfigs['staffing_by_pc_display_theoretical_capacity'] : 0),
	'staffing_by_pc_display_theoretical_fte' => array('theo_fte', __('FTE +/- Theoretical', true), isset($companyConfigs['staffing_by_pc_display_theoretical_fte']) ? $companyConfigs['staffing_by_pc_display_theoretical_fte'] : 0),
	'staffing_by_pc_display_working_day' => array('working', __('Working day', true), isset($companyConfigs['staffing_by_pc_display_working_day']) ? $companyConfigs['staffing_by_pc_display_working_day'] : 0),
);

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'gantt.xls');
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array(
			'memoryCacheSize' => '256MB',
			'cacheTime' => 600,
			'max_execution_time' => 600,
			'max_input_time' => 600,
			'memory_limit' => '512M'
			);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
$objPHPExcel->removeSheetByIndex(0);
// Replace sheet2 and change title
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Staffing');
$objPHPExcel->getActiveSheet()->getStyle('A2:AZ0')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$_showTypeName = '';
switch ($showType) {
	case 1: {
			$_showTypeName = __('Profit Center', true);
			break;
		}
	case 0: {
			$_showTypeName = __('Activity', true);
			break;
		}
	default: {
			$_showTypeName = __('Profile', true);
			break;
		}
}
// display summary ?
$displaySummary = $summary;

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
	 * Set alignment.
	 *
	 * @return void
	 */
	public function alignRight($range) {
		$this->_sheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	}
	 /**
	 * Set alignment.
	 *
	 * @return void
	 */
	public function alignLeft($range) {
		$this->_sheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	}
	public function border($range){
		$this->_sheet->getStyle($range)->applyFromArray(array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			)
		));
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
	/**
	 * Set cell range value and style.
	 *
	 * @return void
	 */
	public function value($range, $value = null, $style = array(), $decimal = true) {
		$this->_sheet->setCellValue($range, $value);
		if( $decimal && (is_numeric($value) || substr($value, 0, 1) == '=') )$this->_sheet->getStyle($range)->getNumberFormat()->setFormatCode('###0.00');
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
	public function summary($count, $col, $start ,$tmpl = '%s', $showType) {
		$result = array();
		if($showType == 1){
			for ($i = 1; $i < $count; $i++) {
				$result[] = sprintf($tmpl, $col . $start);
				$start+=5;
			}
		} else {
			for ($i = 1; $i < $count; $i++) {
				$result[] = sprintf($tmpl, $col . $start);
				$start+=3;
			}
		}
		return '=(' . implode('+', $result) . ')';
	}
	public function hideRow($row, $rowEnd = 0){
		if( $rowEnd >= $row ){
			foreach (range($row, $rowEnd) as $r) {
				$this->_sheet->getRowDimension($r)->setVisible(false);
			}
		} else {
			$this->_sheet->getRowDimension($row)->setVisible(false);
		}
	}
}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);

$yearList = range(date('Y', $start), date('Y', $end));
$yearCount = count($yearList);

// Set title of staffing
$headStyle = array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => '5fa1c4')
	),
	'font' => array(
		'bold' => true,
		'color' => array(
			'rgb' => 'FFFFFF'
		)
	)
);

//header:
//Name | category (workload, consumed, ...) | Year list | Total
$_yIndex = 0;
foreach (array_merge(array($_showTypeName, null), $yearList, array(__('Total', true))) as $y) {
	if ($y) {
		$PhpExcel->alignRight(PHPExcel_Cell::stringFromColumnIndex($_yIndex));
	}
	$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($_yIndex) . '1', $y, $headStyle, false);
	if($start == 0){
		$PhpExcel->value('A' . (2), 'No data exist!');
	}
	++$_yIndex;
}
// Set column width
$activeSheet->getColumnDimension('A')->setWidth(50);
if($showType == 5 && $isCheck == false){
	$activeSheet->getColumnDimension('B')->setWidth(25);
} else {
	$activeSheet->getColumnDimension('B')->setWidth(25);
}
$PhpExcel->alignLeft('A');
$PhpExcel->alignLeft('B');
$activeSheet->getStyle('A')->applyFromArray(array(
	'font' => array(
		'bold' => true
	)
));
/********************
*   SUMMARY TITLE   *
********************/
$showBudget = (int) isset($companyConfigs['budget_team']) ? $companyConfigs['budget_team'] : 0;
if( $showType == 0 ){
	//khi co profit duoc chon
	if( $isCheck == 2 ){
		$itemTitle = array(
			'validated' => __('Workload', true),
			'consumed' => __('Consumed', true)
		);
		$summaryTitle = array();
		if( $showBudget )$summaryTitle['budget'] = __('Budget', true);
		$summaryTitle = array_merge($summaryTitle, array(
			'validated' => __('Workload', true),
			'consumed' => __('Consumed', true),
			'employee' => __('Employee', true)
		));
		foreach($settings as $key => $val ){
			if( $val[2] ){
				$summaryTitle[$val[0]] = $val[1];
			}
		}
	}
	//neu employee dc chon, showType = 1
	else if( $isCheck == 1 ){
		$itemTitle = array(
			'validated' => __('Workload', true),
			'consumed' => __('Consumed', true),
			'notValidated' => __('Days not validated', true)
		);
		$summaryTitle = array(
			'validated' => __('Workload', true),
			'consumed' => __('Consumed', true),
			'notValidated' => __('Days not validated', true)
		);
		foreach($settings as $key => $val ){
			if( $val[2] && !in_array($val[0], array('real_fte', 'theo_capacity', 'theo_fte')) ){
				$summaryTitle[$val[0]] = $val[1];
				$itemTitle[$val[0]] = $val[1];
			}
		}
	}
	//ko co employee && pc nao duoc chon
	else {
		$itemTitle = array();
		if( $showBudget )$itemTitle['budget'] = __('Budget', true);
		$itemTitle = array_merge($itemTitle, array(
			'validated' => __('Workload', true),
			'consumed' => __('Consumed', true)
		));
		$summaryTitle = array();
		if( $showBudget )$summaryTitle['budget'] = __('Budget', true);
		$summaryTitle = array_merge($summaryTitle, array(
			'validated' => __('Workload', true),
			'consumed' => __('Consumed', true),
			'notValidated' => __('Days not validated', true),
			'employee' => __('Employee', true)
		));
		//absence + working days
		if( !empty($companyConfigs['staffing_by_pc_display_absence']) ){
			$summaryTitle['absence'] = __('Absence', true);
		}
		if( !empty($companyConfigs['staffing_by_pc_display_working_day']) ){
			$summaryTitle['working'] = __('Working', true);
		}
	}
}
//display by PC
else if( $showType == 1 ){
	$itemTitle = array(
		'validated' => __('Workload', true),
		'consumed' => __('Consumed', true),
		'notValidated' => __('Days not validated', true),
		'employee' => __('Employee', true)
	);
	$summaryTitle = array(
		'validated' => __('Workload', true),
		'consumed' => __('Consumed', true),
		'notValidated' => __('Days not validated', true),
		'employee' => __('Employee', true)
	);
	foreach($settings as $key => $val ){
		if( $val[2] ){
			$summaryTitle[$val[0]] = $val[1];
			$itemTitle[$val[0]] = $val[1];
		}
	}
}

//for employees
$subItems = array(
	'validated' => __('Workload', true),
	'consumed' => __('Consumed', true)
);

$allItems = array(
	'budget' => __('Budget', true),
	'validated' => __('Workload', true),
	'consumed' => __('Consumed', true),
	'notValidated' => __('Days not validated', true),
	'employee' => __('Employee', true),
	//'theo_employee' => __('Employee Theoretical', true),
);
foreach($settings as $key => $val ){
	$allItems[$val[0]] = $val[1];
}

$startDataRow = 2 + count($allItems);
$itemCount = count($itemTitle);

$years = array();

$maps = array_flip(array_keys($allItems));
$ignoreSum = array('employee');

$sum = array();
$row = $startDataRow + 1;

$list = array();
switch($dateType){
	case 'day':
		$list = $listDays;
	break;
	case 'week':
		$list = $listWeeks;
	break;
	default:
		$list = $months;
	break;
}
//build header
foreach($list as $date){
	if( $dateType == 'month' ){
        $year = $date[2];
        $val = $date[1] . '-' . $date[2];
    } else {
        $year = date('Y', $date);
        if( $dateType == 'week' )
        	$val = 'w' . date('W-m/Y', $date);
        else $val = date('d-m-Y', $date);
    }

	if( !isset($years[$year]) ){
		$years[$year]['start'] = $_yIndex;
	}

	$years[$year]['end'] = $_yIndex;

	$PhpExcel->alignRight(PHPExcel_Cell::stringFromColumnIndex($_yIndex));
	$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($_yIndex) . '1', $val, $headStyle);
	if( $dateType != 'month' )
		$activeSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($_yIndex))->setWidth(15);
	++$_yIndex;
}

foreach($staffings as $staffing){
	//new requirement
	$checkEmp = $isCheck == 1 && empty($staffing['isEmployee']);
	$checkPc = ( $isCheck == 2 || $isCheck === false ) && empty($staffing['isFamily']);

	$PhpExcel->value('A' . $row, $staffing['name']);
	$data = $staffing['data'];
	$startRow = $row;
	$data2 = array();
	if( !empty($staffing['isEmployee']) ){
		$eid = preg_replace('/[^0-9]+/', '', $staffing['id']);
		$data2 = !empty($extra[$eid]) ? $extra[$eid] : array();
	}
	foreach($allItems as $key => $item){
		$PhpExcel->value('B' . $row, $item);
		$PhpExcel->border('B' . $row);
		$col = $yearCount + 3;
		//data each month
		foreach($list as $date){
            if( $dateType == 'month' ){
                $time = strtotime($date[2] . '-' . $date[1] . '-01');
                $year = $date[2];
            } else {
                $time = $date;
                $year = date('Y', $date);
            }
			switch($key){
				case 'budget':
					$val = isset($data[$time][$key]) ? $data[$time][$key] : 0;
					//if( !empty($staffing['isFamily']) || !empty($staffing['isSubFamily']) )
						$sum[$time][$key] = isset($sum[$time][$key]) ? $sum[$time][$key] + $val : $val;
				break;
				case 'validated':
					$val = isset($data[$time][$key]) ? $data[$time][$key] : 0;
					if( !$checkEmp && !$checkPc )$sum[$time][$key] = isset($sum[$time][$key]) ? $sum[$time][$key] + $val : $val;
				break;
				case 'consumed':
					$val = isset($data[$time]['consumed']) ? $data[$time]['consumed'] : 0;
					if( !$checkEmp && !$checkPc )$sum[$time][$key] = isset($sum[$time][$key]) ? $sum[$time][$key] + $val : $val;
				break;
				case 'notValidated':
					$val = sprintf('=%s-%s', PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['capacity']), PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['consumed']));
				break;
				case 'capacity':
					$val = isset($data2[$key][$time]) ? $data2[$key][$time] : 0;
				break;
				case 'theo_capacity':
					$val = isset($data2['capacity_theoretical'][$time]) ? $data2['capacity_theoretical'][$time] : 0;
				break;
				case 'employee':
					$val = isset($data2['resource'][$time]) ? $data2['resource'][$time] : 0;
				break;
				case 'working':
					$val = isset($data2[$key][$time]) ? $data2[$key][$time] : 0;
				break;
				case 'absence':
					$val = isset($data2[$key][$time]) ? $data2[$key][$time] : 0;
				break;
				case 'theo_fte':
					$capacity = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['theo_capacity']);
					$working = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['working']);
					$workload = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['validated']);
					$val = sprintf('=IF(%1$s=0, 0, ROUND((%2$s - %3$s) / %1$s, 2))', $working, $workload, $capacity);
				break;
				case 'real_fte':
					$capacity = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['capacity']);
					$working = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['working']);
					$workload = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['validated']);
					$val = sprintf('=IF(%1$s=0, 0, ROUND((%2$s - %3$s) / %1$s, 2))', $working, $workload, $capacity);
				break;
			}
			$cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
			$PhpExcel->value($cell, $val);
			$PhpExcel->border($cell);
			++$col;
			//hide if setting is 0
		}
		if( !isset($itemTitle[$key]) )$PhpExcel->hideRow($row);
		if( $key == 'budget' && (!empty($staffing['isFamily']) || !empty($staffing['isSubfamily'])) ){
			$activeSheet->getRowDimension($row)->setVisible(true);
		} else if( $checkEmp || $checkPc ){
			if( !isset($subItems[$key]) )$PhpExcel->hideRow($row);
			else $activeSheet->getRowDimension($row)->setVisible(true);
		}
		if( in_array($key, $ignoreSum) ){
            $row++;
            continue;
        }
		//sum each year
		$col = 2;
		foreach($yearList as $year){
			if( $key == 'theo_fte' || $key == 'real_fte' ){
				$start = PHPExcel_Cell::stringFromColumnIndex($years[$year]['start']);
				$end = PHPExcel_Cell::stringFromColumnIndex($years[$year]['end']);
				$working = sprintf('SUM(%s:%s)', $start . ($startRow+$maps['working']), $end . ($startRow+$maps['working']));
				$workload = sprintf('SUM(%s:%s)', $start . ($startRow+$maps['validated']), $end . ($startRow+$maps['validated']));
				$_key = $key == 'theo_fte' ? 'theo_capacity' : 'capacity';
				$capacity = sprintf('SUM(%s:%s)', $start . ($startRow+$maps[$_key]), $end . ($startRow+$maps[$_key]));
				$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($col) . $row, sprintf('=IF(%1$s=0, 0, ROUND((%2$s - %3$s) / %1$s, 2))', $working, $workload, $capacity));
				$PhpExcel->border(PHPExcel_Cell::stringFromColumnIndex($col) . $row);
			} else {
				$from = PHPExcel_Cell::stringFromColumnIndex($years[$year]['start']) . $row;
				$to = PHPExcel_Cell::stringFromColumnIndex($years[$year]['end']) . $row;
				$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($col) . $row, sprintf('=SUM(%s:%s)', $from, $to));
				$PhpExcel->border(PHPExcel_Cell::stringFromColumnIndex($col) . $row);
			}
			++$col;
		}
		//sum total
		$from = PHPExcel_Cell::stringFromColumnIndex(2) . $row;
		$to = PHPExcel_Cell::stringFromColumnIndex(2 + $yearCount - 1) . $row;
		$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($col) . $row, sprintf('=SUM(%s:%s)', $from, $to));
		$PhpExcel->borderT(PHPExcel_Cell::stringFromColumnIndex($col) . $row);

		$row++;
	}
	//merge the name
	$activeSheet->mergeCells(sprintf('A%d:A%d', $startRow, $row-1));
	$PhpExcel->border(sprintf('A%d:A%d', $startRow, $row-1));
	//hide the row if showSummary = 99
	if( $summary == 99 )$PhpExcel->hideRow($startRow, $row);
	$row++;
}

//build Summary
$row = 2;
$PhpExcel->value('A' . $row, __('Summary', true));
$startRow = $row;
$newDataStaffings = isset($extra['summary']) ? $extra['summary'] : array();
foreach($allItems as $key => $item){
	$PhpExcel->value('B' . $row, $item);
	$PhpExcel->border('B' . $row);
	$col = $yearCount + 3;
	foreach($list as $date){
        if( $dateType == 'month' ){
            $time = strtotime($date[2] . '-' . $date[1] . '-01');
            $year = $date[2];
        } else {
            $time = $date;
            $year = date('Y', $date);
        }
		$cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
		if($key == 'theo_fte' || $key == 'real_fte' ){
			$capacity = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps[$key == 'theo_fte' ? 'theo_capacity' : 'capacity']);
			$working = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['working']);
			$workload = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['validated']);
			$PhpExcel->value($cell, sprintf('=IF(%1$s=0, 0, ROUND((%2$s - %3$s) / %1$s, 2))', $working, $workload, $capacity));
		}
		else if( $key == 'notValidated' ){
			$capacity = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['capacity']);
			$consumed = PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['consumed']);
			$PhpExcel->value($cell, sprintf('=%s-%s', $capacity, $consumed));
		}
		else {
			if( $key == 'theo_capacity' )$k = 'capacity_theoretical';
			else if( $key == 'employee' )$k = 'resource';
			else $k = $key;
			$PhpExcel->value($cell, isset($sum[$time][$key]) ? $sum[$time][$key] : (isset($newDataStaffings[$k][$time]) ? $newDataStaffings[$k][$time] : 0));
		}
		$PhpExcel->border($cell);
		++$col;
	}
	if( !isset($summaryTitle[$key]) )$PhpExcel->hideRow($row);
	if( in_array($key, $ignoreSum) ){
		$row++;
		continue;
	}
	$col = 2;
	foreach($yearList as $year){
		if( $key == 'theo_fte' || $key == 'real_fte' ){
			$start = PHPExcel_Cell::stringFromColumnIndex($years[$year]['start']);
			$end = PHPExcel_Cell::stringFromColumnIndex($years[$year]['end']);
			$working = sprintf('SUM(%s:%s)', $start . ($startRow+$maps['working']), $end . ($startRow+$maps['working']));
			$workload = sprintf('SUM(%s:%s)', $start . ($startRow+$maps['validated']), $end . ($startRow+$maps['validated']));
			$_key = $key == 'theo_fte' ? 'theo_capacity' : 'capacity';
			$capacity = sprintf('SUM(%s:%s)', $start . ($startRow+$maps[$_key]), $end . ($startRow+$maps[$_key]));
			$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($col) . $row, sprintf('=IF(%1$s=0, 0, ROUND((%2$s - %3$s) / %1$s, 2))', $working, $workload, $capacity));
			$PhpExcel->border(PHPExcel_Cell::stringFromColumnIndex($col) . $row);
		} else {
			$from = PHPExcel_Cell::stringFromColumnIndex($years[$year]['start']) . $row;
			$to = PHPExcel_Cell::stringFromColumnIndex($years[$year]['end']) . $row;
			$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($col) . $row, sprintf('=SUM(%s:%s)', $from, $to));
			$PhpExcel->border(PHPExcel_Cell::stringFromColumnIndex($col) . $row);
		}
		++$col;
	}
	//sum total
	$from = PHPExcel_Cell::stringFromColumnIndex(2) . $row;
	$to = PHPExcel_Cell::stringFromColumnIndex(2 + $yearCount - 1) . $row;
	$PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($col) . $row, sprintf('=SUM(%s:%s)', $from, $to));
	$PhpExcel->borderT(PHPExcel_Cell::stringFromColumnIndex($col) . $row);
	$row++;
}

//merge the name
$activeSheet->mergeCells(sprintf('A%d:A%d', $startRow, $row-1));
$PhpExcel->border(sprintf('A%d:A%d', $startRow, $row-1));
if( $summary == 0 )$PhpExcel->hideRow($startRow, $row);

// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="activities_vision_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->setPreCalculateFormulas(false);
$objWriter->save('php://output');

exit;
?>
