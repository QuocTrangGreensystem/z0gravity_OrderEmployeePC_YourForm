<?php
set_time_limit(0);
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

$extra = array();
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
$onlysummary = !$showType && !$resource;
$is_resource = !$showType && $resource;
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$list = array();
foreach($staffings as $ss){
    $list[] = str_replace('pc-', '', $ss['id']);
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
    'priority' => $priority,
    'list' => json_encode($list)
);

if( $onlysummary )$args['only_sum'] = 1;
if( $is_resource )$args['is_resource'] = 1;
$extra = $this->requestAction(array('controller' => 'new_staffing', 'action' => 'index', '?' => $args));


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
    if( $dateType != 'month' && in_array($val[0], array('theo_capacity', 'theo_fte')) )continue;
    if( $val[2] ){
        $summaryTitle[$val[0]] = $val[1];
        $itemTitle[$val[0]] = $val[1];
    }
}

$allItems = array(
    'validated' => __('Workload', true),
    'consumed' => __('Consumed', true),
    'notValidated' => __('Days not validated', true),
    'employee' => __('Employee', true)
);
foreach($settings as $key => $val ){
    $allItems[$val[0]] = $val[1];
}


$startDataRow = 2 + count($allItems);
$itemCount = count($itemTitle);

$years = array();

$maps = array_flip(array_keys($allItems));
$ignoreSum = array('employee');

$sum = $parent = array();
$row = $startDataRow + 1;
$multi = count($staffings) > 1 ? true : false;

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
    if( $dateType == 'week' )
        $activeSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($_yIndex))->setWidth(15);
    if( $dateType == 'day' )
        $activeSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($_yIndex))->setWidth(12);
    ++$_yIndex;
}

foreach($staffings as $staffing){
    $PhpExcel->value('A' . $row, $staffing['name']);
    $data = $staffing['data'];
    $startRow = $row;
    $pcid = preg_replace('/[^0-9]+/', '', $staffing['id']);
    $data2 = isset($extra[$pcid]) ? $extra[$pcid] : array();
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
                case 'validated':
                    $val = isset($data[$time][$key]) ? $data[$time][$key] : 0;
                    //$sum[$time][$key] = isset($sum[$time][$key]) ? $sum[$time][$key] + $val : $val;
                break;
                case 'consumed':
                    $val = isset($data[$time][$key]) ? $data[$time][$key] : 0;
                    //$sum[$time][$key] = isset($sum[$time][$key]) ? $sum[$time][$key] + $val : $val;
                break;
                case 'notValidated':
                    $val = sprintf('=%s-%s', PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['capacity']), PHPExcel_Cell::stringFromColumnIndex($col) . ($startRow + $maps['consumed']));
                break;
                case 'capacity':
                case 'working':
                case 'absence':
                    $val = isset($data2[$key][$time]) ? $data2[$key][$time] : 0;
                break;
                case 'theo_capacity':
                    $val = isset($data2['capacity_theoretical'][$time]) ? $data2['capacity_theoretical'][$time] : 0;
                break;
                case 'employee':
                    $val = isset($data2['resource'][$time]) ? $data2['resource'][$time] : 0;
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
            if( $key != 'working' ){
                //
                if( $multi && $staffing['level'] == 0 ){
                    $parent[$time][$key] = isset($parent[$time][$key]) ? $parent[$time][$key] + $val : $val;
                }
                else $sum[$time][$key] = isset($sum[$time][$key]) ? $sum[$time][$key] + $val : $val;
            }
            else 
                $sum[$time]['working'] = isset($sum[$time]['working']) ? max($val, $sum[$time]['working']) : $val;
        }
        if( !isset($itemTitle[$key]) )$PhpExcel->hideRow($row);
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
    if( $summary == 99 )$PhpExcel->hideRow($startRow, $row);
    $row++;
}
//build Summary
$row = 2;
$PhpExcel->value('A' . $row, __('Summary', true));
$startRow = $row;
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
            if( $multi && $key != 'working' )
                $PhpExcel->value($cell, isset($parent[$time][$key]) ? $parent[$time][$key] : 0);
            else $PhpExcel->value($cell, isset($sum[$time][$key]) ? $sum[$time][$key] : 0);
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
$objWriter->save('php://output');
if (!empty($tmpFile)) {
    unlink($tmpFile);
}
exit;
?>