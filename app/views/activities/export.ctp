<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'user_view.xls');
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array(
            'memoryCacheSize' => '256MB',
            'cacheTime' => 600,
            'max_execution_time' => 600,
            'max_input_time' => 600,
            'memory_limit' => '512M'
            );
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Activity Review');



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

$fieldset = array();



unset($activityColumn['accessible_profit'], $activityColumn['linked_profit']);
$map = array();
foreach ($activityColumn as $key => $column) {
    $map['C' . $column['code']] = $key;
    if (empty($column['display'])) {
        continue;
    }
    $fieldset[] = array(
        'name' => $column['name'],
        'path' => 'Activity.' . $key,
        'width' => 30
    );
}
$fieldset[] = array(
        'name' => __('Activated', true),
        'path' => 'Activity.activated',
        'width' => 30,
    );
$selectMaps = array(
    'actif' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'pms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true))
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
$PhpExcel->value('A1', __('No.', true));
$listDatas = array(
    'workload_y' => __('Workload', true) . ' ' . date('Y', time()),
    'workload_last_one_y' => __('Workload', true) . ' ' . (date('Y', time()) - 1),
    'workload_last_two_y' => __('Workload', true) . ' ' . (date('Y', time()) - 2),
    'workload_last_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) - 3),
    'workload_next_one_y' => __('Workload', true) . ' ' . (date('Y', time()) + 1),
    'workload_next_two_y' => __('Workload', true) . ' ' . (date('Y', time()) + 2),
    'workload_next_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) + 3),
    'consumed_y' => __('Consumed', true) . ' ' . date('Y', time()),
    'consumed_last_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 1),
    'consumed_last_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 2),
    'consumed_last_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 3),
    'consumed_next_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 1),
    'consumed_next_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 2),
    'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3)
);
$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $fieldList = trim(str_replace('Activity.', '', $_fieldset['path']));
    $names = !empty($listDatas) && !empty($listDatas[$fieldList]) ? $listDatas[$fieldList] : __($_fieldset['name'], true);
    $PhpExcel->value($colName . '1', $names);
    $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
}
$PhpExcel->align('A1:' . $colName . '1');
$PhpExcel->border('A1:' . $colName . '1', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '185790')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
         ),
         'size' => 11,
    )));

$rIndex = 2;
$no = 1;
$colMax = $colName;
foreach ($activities as $activity) {

    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;

    foreach ($activityColumn as $key => $column) {
        $data[$key] = '';
        if (@$column['calculate'] === false && isset($activity['Activity'][$key])) {
            $data[$key] = (string) $activity['Activity'][$key];
            if ($key === 'actif' || $key === 'pms' || $key === 'activated') {
                $data[$key] = $data[$key] ? 'yes' : 'no';
            }
        }
    }
    $data['start_date'] = $data['start_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $data['start_date'])) : '';
    $data['end_date'] = $data['end_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $data['end_date'])) : '';
    $data['consumed'] = isset($sumActivities[$data['id']]) ? $sumActivities[$data['id']] : 0;
    if($activity['Activity']['pms'] == 0){
        $data['workload'] = isset($dataFromActivityTasks[$data['id']]['workload']) ? $dataFromActivityTasks[$data['id']]['workload'] : 0;
        $data['overload'] = isset($dataFromActivityTasks[$data['id']]['overload']) ? $dataFromActivityTasks[$data['id']]['overload'] : 0;
        $data['completed'] = isset($dataFromActivityTasks[$data['id']]['completed']) ? $dataFromActivityTasks[$data['id']]['completed'].'%' : '0%';
        $data['remain'] = isset($dataFromActivityTasks[$data['id']]['remain']) ? $dataFromActivityTasks[$data['id']]['remain'] : 0;
    } else {
        $data['workload'] = isset($dataFromProjectTasks[$data['id']]['workload']) ? $dataFromProjectTasks[$data['id']]['workload'] : 0;
        $data['overload'] = isset($dataFromProjectTasks[$data['id']]['overload']) ? $dataFromProjectTasks[$data['id']]['overload'] : 0;
        $data['completed'] = isset($dataFromProjectTasks[$data['id']]['completed']) ? $dataFromProjectTasks[$data['id']]['completed'].'%' : '0%';
        $data['remain'] = isset($dataFromProjectTasks[$data['id']]['remain']) ? $dataFromProjectTasks[$data['id']]['remain'] : 0;
    }
    if (isset($sumEmployees[$data['id']])) {
        foreach ($sumEmployees[$data['id']] as $id => $val) {
            $tmp = isset($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
            $data['real_price'] += $val * $tmp;
        }
    }
    // display du lieu budget
    //sales
    $data['sales_sold'] = !empty($budgets[$data['id']]['sales_sold']) ? $budgets[$data['id']]['sales_sold'] : 0;
    $data['sales_to_bill'] = !empty($budgets[$data['id']]['sales_to_bill']) ? $budgets[$data['id']]['sales_to_bill'] : 0;
    $data['sales_billed'] = !empty($budgets[$data['id']]['sales_billed']) ? $budgets[$data['id']]['sales_billed'] : 0;
    $data['sales_paid'] = !empty($budgets[$data['id']]['sales_paid']) ? $budgets[$data['id']]['sales_paid'] : 0;
    $data['sales_man_day'] = !empty($budgets[$data['id']]['sales_man_day']) ? $budgets[$data['id']]['sales_man_day'] : 0;
    //internal costs
    $data['internal_costs_budget'] = !empty($budgets[$data['id']]['internal_costs_budget']) ? $budgets[$data['id']]['internal_costs_budget'] : 0;
    $data['internal_costs_budget_man_day'] = !empty($budgets[$data['id']]['internal_costs_budget_man_day']) ? $budgets[$data['id']]['internal_costs_budget_man_day'] : 0;
    $data['internal_costs_average'] = !empty($budgets[$data['id']]['internal_costs_average']) ? $budgets[$data['id']]['internal_costs_average'] : 0;
    $data['internal_costs_engaged'] = $data['real_price'];
    $data['internal_costs_forecasted_man_day'] = $data['remain'] + $data['consumed'];
    $_average = !empty($budgets[$data['id']]['internal_costs_average']) ? $budgets[$data['id']]['internal_costs_average'] : 0;
    $data['internal_costs_remain'] = round($data['remain']*$_average, 2);
    $data['internal_costs_forecast'] = round($data['internal_costs_engaged'] + $data['internal_costs_remain'], 2);
    $data['internal_costs_var'] = ($data['internal_costs_budget'] == 0) ? '-100%' : round((($data['internal_costs_forecast']/$data['internal_costs_budget']) - 1)*100, 2).'%';
    //external costs
    $data['external_costs_budget'] = !empty($budgets[$data['id']]['external_costs_budget']) ? $budgets[$data['id']]['external_costs_budget'] : 0;
    $data['external_costs_forecast'] = !empty($budgets[$data['id']]['external_costs_forecast']) ? $budgets[$data['id']]['external_costs_forecast'] : 0;
    $data['external_costs_var'] = !empty($budgets[$data['id']]['external_costs_var']) ? $budgets[$data['id']]['external_costs_var']. ' %' : '0 %';
    $data['external_costs_ordered'] = !empty($budgets[$data['id']]['external_costs_ordered']) ? $budgets[$data['id']]['external_costs_ordered'] : 0;
    $data['external_costs_remain'] = !empty($budgets[$data['id']]['external_costs_remain']) ? $budgets[$data['id']]['external_costs_remain'] : 0;
    $data['external_costs_man_day'] = !empty($budgets[$data['id']]['external_costs_man_day']) ? $budgets[$data['id']]['external_costs_man_day'] : 0;
    $data['external_costs_progress'] = !empty($budgets[$data['id']]['external_costs_progress']) ? $budgets[$data['id']]['external_costs_progress'] : 0;
    $data['external_costs_progress_euro'] = !empty($budgets[$data['id']]['external_costs_progress_euro']) ? $budgets[$data['id']]['external_costs_progress_euro'] : 0;
    //total costs
    $data['total_costs_budget'] = $data['internal_costs_budget'] + $data['external_costs_budget'];
    $data['total_costs_forecast'] = $data['internal_costs_forecast'] + $data['external_costs_forecast'];
    $data['total_costs_engaged'] = $data['internal_costs_engaged'] + $data['external_costs_ordered'];
    $data['total_costs_remain'] = $data['internal_costs_remain'] + $data['external_costs_remain'];
    $data['total_costs_man_day'] = $data['internal_costs_forecasted_man_day'] + $data['external_costs_man_day'];
    $data['total_costs_var'] = ($data['total_costs_budget'] == 0) ? '-100%' : round((($data['total_costs_forecast']/$data['total_costs_budget'])-1)*100, 2). '%';
    $tWorkload = $data['workload'] + $data['overload'];
    $assgnPc = !empty($assignProfitCenters[$data['id']]) ? $assignProfitCenters[$data['id']] : 0;
    $data['assign_to_profit_center'] = ($tWorkload == 0) ? '0%' : ((round(($assgnPc/$tWorkload)*100, 2) > 100)? '100%' : round(($assgnPc/$tWorkload)*100, 2).'%');
    $assgnEmploy = !empty($assignEmployees[$data['id']]) ? $assignEmployees[$data['id']] : 0;
    $data['assign_to_employee'] = ($tWorkload == 0) ? '0%' : ((round(($assgnEmploy/$tWorkload)*100, 2) > 100)? '100%' : round(($assgnEmploy/$tWorkload)*100, 2).'%');
    if(!empty($activity['project_manager_id'])){
        foreach ($activity['project_manager_id'] as $value) {
            if(!empty($value['project_manager_id'])){
                $data['project_manager_id'][$value['project_manager_id']] = !empty($value['is_backup']) ? "1" : "0";
            }
        }
    } else {
        $data['project_manager_id'] = array();
    }
    // consumed of current year and consumed of current month
    $data['consumed_current_year'] = !empty($consumedOfYear[$data['id']]) ? $consumedOfYear[$data['id']]: 0;
    $data['consumed_current_month'] = !empty($consumedOfMonth[$data['id']]) ? $consumedOfMonth[$data['id']]: 0;

    $data['consumed_current_year'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.$currentYears]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.$currentYears]: 0;
    $data['consumed_current_month'] = !empty($consumedOfMonth[$data['id']]) ? $consumedOfMonth[$data['id']]: 0;
    $data['workload_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.$currentYears]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.$currentYears]: 0;
    $data['workload_last_one_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears-1)]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears-1)]: 0;
    $data['workload_last_two_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears-2)]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears-2)]: 0;
    $data['workload_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears-3)]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears-3)]: 0;
    $data['workload_next_one_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears+1)]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears+1)]: 0;
    $data['workload_next_two_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears+2)]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears+2)]: 0;
    $data['workload_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears+3)]) ? $consumedAndWorkloadForActivities[$data['id']]['workload_'.($currentYears+3)]: 0;

    $data['consumed_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.$currentYears]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.$currentYears]: 0;
    $data['consumed_last_one_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears-1)]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears-1)]: 0;
    $data['consumed_last_two_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears-2)]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears-2)]: 0;
    $data['consumed_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears-3)]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears-3)]: 0;
    $data['consumed_next_one_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears+1)]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears+1)]: 0;
    $data['consumed_next_two_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears+2)]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears+2)]: 0;
    $data['consumed_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears+3)]) ? $consumedAndWorkloadForActivities[$data['id']]['consumed_'.($currentYears+3)]: 0;



    foreach ($activityColumn as $key => &$column) {
        if (empty($column['calculate'])) {
            continue;
        }
        if (!isset($column['_match'])) {
            preg_match_all('/C\d+/i', $column['calculate'], $column['match']);
            $column['match'] = array_unique($column['match'][0]);
        }
        $cal = $column['calculate'];
        if (!empty($column['match'])) {
            foreach ($column['match'] as $k) {
                $cal = str_replace($k, isset($data[$map[$k]]) ? floatval($data[$map[$k]]) : 0, $cal);
            }
        }
        $data[$key] = @eval("return ($cal);");
        if (!is_numeric($data[$key])) {
            $data[$key] = 0;
        } elseif (is_float($data[$key])) {
            $data[$key] = round($data[$key], 2);
        }
    }
    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract(array('Activity' => $data), $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'Activity.family_id' : {
                    $_output = isset($families[$_output]) ? $families[$_output] : '';
                    break;
                }
            case 'Activity.subfamily_id' : {
                    $_output = isset($subfamilies[$_output]) ? $subfamilies[$_output] : '';
                    break;
                }
            case 'Activity.md' : {
                    $_output = floatval($_output);
                    break;
                }
            case 'Activity.activated' : {
                    $_output = $activity['Activity']['activated'] ? 'YES' : 'NO';
                    break;
                }
            case 'Activity.budget_customer_id' : {
                    $_output = isset($budgetCustomers[$_output]) ? $budgetCustomers[$_output] : '';
                    break;
                }
            case 'Activity.project_manager_id' : {
                    $list = array();
                    foreach ($_output as $employeeId => $isBackup) {
                        $managers = !empty($projectManagers[$employeeId]) ? $projectManagers[$employeeId] : '';
                        $list[] = sprintf('%s%s', $managers, $isBackup ? '(B)' : '');
                    }
                    $_output = implode(', ', $list);
                    break;
                }
        }
        $PhpExcel->value($colName . $rIndex, (string) $_output);
    }
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    );
    $PhpExcel->align('A' . $rIndex . ":" . $colName . $rIndex);
    $PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex, array(
    'fill' => array(

    ),
    'font' => array(
        'size' => 11,
        'bold' => false,
        )
    ));
    $rIndex++;
}

//exit();
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="activity_review_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
