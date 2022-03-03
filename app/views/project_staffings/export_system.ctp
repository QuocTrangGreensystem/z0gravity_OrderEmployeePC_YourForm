<?php
$staffings = !empty($staffingss) ? $staffingss : array();
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

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
    
$_showTypeName = '';
switch ($showType) {
    case 1: {
            $_showTypeName = __('Profit centers', true);
            break;
        }
    case 0: {
            $_showTypeName = __('Skills', true);
            break;
        }
    default: {
            $_showTypeName = __('Projects', true);
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
}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);

$year = array();
// Default staffing data
if($showType == 5 || $showType == 0){
    $default = array(
        'validated' => 0,
        'consumed' => null
    );
} else {
    $default = array(
        'validated' => 0,
        'consumed' => null,
        'employee' => 0,
        'capacity' => 0
    );
}

$yearStart = date('Y', $start);
$_yearList = range($yearStart, $yearStart + (date('Y', $end) - $yearStart));
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
$_yIndex = 0;
foreach (array_merge(array($_showTypeName, null), $_yearList, array(__('Total', true))) as $y) {
    if ($y) {
        $PhpExcel->align(PHPExcel_Cell::stringFromColumnIndex($_yIndex));
    }
    $PhpExcel->value(PHPExcel_Cell::stringFromColumnIndex($_yIndex++) . '1', $y, $headStyle);
    if($start == 0){
        $PhpExcel->value('A' . (2), 'No data exist!');
    }
}
// Set column width
$activeSheet->getColumnDimension('B')->setWidth(20);
$activeSheet->getColumnDimension('A')->setWidth(30);

$_yearCount = count($_yearList);
$_col = $_colDetail = $_yearCount + 3;
if($showType == 5 || $showType == 0){
    $_title = array(
        0 => array('validated', __('Workload(*)', true)),
        1 => array('consumed', __('Consumed', true))
    );
} else {
    $_title = array(
        0 => array('validated', __('Workload(*)', true)),
        1 => array('consumed', __('Consumed', true)),
        2 => array('employee', __('Employee', true)),
        3 => array('capacity', __('Capacity', true))
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

$sumCount = count($staffings);
$summaryFx = '(IF(%1$s = "'.$this->GanttVs->na.'" , 0, %1$s))';
if(!empty($months)){
    foreach ($months as $data) {
        list(, $m, $y) = $data;
        if (!isset($yearDetails[$y])) {
            $yearDetails[$y] = 0;
        }
        $yearDetails[$y]++;
        $_colName = PHPExcel_Cell::stringFromColumnIndex($_col++);
        $PhpExcel->align($_colName);
        $PhpExcel->value($_colName . '1', "$m-$y", $headStyle);
    }
    $datas = array();
    $_row = 2;
    $minDate = !empty($months) ? $months[0] : '';
    $maxDate = !empty($months) ? end($months) : '';
    $minDate = !empty($minDate) ? strtotime('01-'.$minDate[1].'-'.$minDate[2]) : 0;
    $maxDate = !empty($maxDate) ? strtotime('01-'.$maxDate[1].'-'.$maxDate[2]) : 0;
    if(!empty($staffings)){
        $wlSummary = $csSummary = array();
        $wlYearSums = $csYearSums = array();
        $wlTotalSums = $csTotalSums = 0;
        foreach($staffings as $keys => $staffing){
            $bgcolor = 'FBFCCA';
            $workloadYear = $cosumnedYear = array();
            if(!empty($staffing['data'])){
                foreach($staffing['data'] as $time => $value){
                    if($minDate <= $time && $time <= $maxDate){
                        $year = date('Y', $time);
                        if(!isset($workloadYear[$year])){
                            $workloadYear[$year] = 0;
                        }
                        $workloadYear[$year] += !empty($value['validated']) ? $value['validated'] : 0;
                        if(!isset($cosumnedYear[$year])){
                            $cosumnedYear[$year] = 0;
                        }
                        $cosumnedYear[$year] += !empty($value['consumed']) ? $value['consumed'] : 0;
                        
                        if(!isset($wlSummary[$time])){
                            $wlSummary[$time] = 0;
                        }
                        $wlSummary[$time] += !empty($value['validated']) ? $value['validated'] : 0;
                        
                        if(!isset($csSummary[$time])){
                            $csSummary[$time] = 0;
                        }
                        $csSummary[$time] += !empty($value['consumed']) ? $value['consumed'] : 0;
                    }
                }
            } 
            $cols = $_colDetail;
            foreach($months as $month){
                list(, $m, $y) = $month;
                $_day = strtotime('01-'.$m.'-'.$y);
                $workload = !empty($staffing['data'][$_day]['validated']) ? $staffing['data'][$_day]['validated'] : 0;
                $consumed = !empty($staffing['data'][$_day]['consumed']) ? $staffing['data'][$_day]['consumed'] : 0;
                $_wlSums = !empty($wlSummary[$_day]) ? $wlSummary[$_day] : 0;
                $_csSums = !empty($csSummary[$_day]) ? $csSummary[$_day] : 0;
                $_colNameDetail = PHPExcel_Cell::stringFromColumnIndex($cols++);
                if($keys === 'summary'){
                    $bgcolor = 'D2F7D9';
                    $PhpExcel->value($_colNameDetail . ($_row), $_wlSums);
                    $PhpExcel->value($_colNameDetail . ($_row+1), $_csSums);
                } else {
                    $PhpExcel->value($_colNameDetail . ($_row), $workload);
                    $PhpExcel->value($_colNameDetail . ($_row+1), $consumed);
                }
                
            }
            $colYear = 2;
            $colTotal = count($_yearList) + $colYear;
            $wlTotal = $csTotal = 0;
            foreach($_yearList as $_years){
                // tinh tong workload theo tung nam theo tung project
                $wlYear = !empty($workloadYear[$_years]) ? $workloadYear[$_years] : 0;
                // tinh summary workload theo tung nam cua tat ca project
                if(!isset($wlYearSums[$_years])){
                    $wlYearSums[$_years] = 0;
                }
                $wlYearSums[$_years] += $wlYear;
                // tinh tong workload cua 1 project
                $wlTotal += $wlYear;
                // tinh tong workload cua tat ca project
                $wlTotalSums += $wlYear;
                // tinh tong consumed theo tung nam theo tung project
                $csYear = !empty($cosumnedYear[$_years]) ? $cosumnedYear[$_years] : 0;
                // tinh summary consumed theo tung nam cua tat ca project
                if(!isset($csYearSums[$_years])){
                    $csYearSums[$_years] = 0;
                }
                $csYearSums[$_years] += $csYear;
                // tinh tong consumed cua 1 project
                $csTotal += $csYear;
                // tinh tong consumed cua tat ca project
                $csTotalSums += $csYear;
                $_colNameYear = PHPExcel_Cell::stringFromColumnIndex($colYear++);
                if($keys === 'summary'){
                    $bgcolor = 'D2F7D9';
                    $PhpExcel->value($_colNameYear . ($_row), $wlYearSums[$_years]);
                    $PhpExcel->value($_colNameYear . ($_row + 1), $csYearSums[$_years]);
                } else {
                    $PhpExcel->value($_colNameYear . ($_row), $wlYear);
                    $PhpExcel->value($_colNameYear . ($_row + 1), $csYear);
                }
                
            }
            $_colNameTotal = PHPExcel_Cell::stringFromColumnIndex($colTotal);
            if($keys === 'summary'){
                $bgcolor = 'D2F7D9';
                $PhpExcel->value($_colNameTotal . ($_row), $wlTotalSums);
                $PhpExcel->value($_colNameTotal . ($_row + 1), $csTotalSums);
            } else {
                $PhpExcel->value($_colNameTotal . ($_row), $wlTotal);
                $PhpExcel->value($_colNameTotal . ($_row + 1), $csTotal);
            }
            
            // title
            foreach ($_title as $k => $v) {
                 $PhpExcel->value('B' . ($_row + $k), $v[1]);
            }
            $PhpExcel->value('A' . ($_row), $staffing['name']);
            $activeSheet->mergeCells('A' . ($_row) . ':A' . ($_row + 1));
            $_row += 3;
        }
    }
}
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_vision_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
if (!empty($tmpFile)) {
    unlink($tmpFile);
}
exit;
?>