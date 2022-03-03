<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('slick_grid/slick.grid_v2'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common_v2'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;    
        }
    </style>
<![endif]-->
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'activities', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<style type='text/css'>
.KO{ color:#F00}
</style>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Activity review", true); ?></h2>                    
                    <a href="#" id="export-submit" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export') ?></span></a> 
                     <!--ADD CODE BY VINGUYEN 07/05/2014--->
					<?php echo $this->element('multiSortHtml'); ?>
                    <!--END-->
                    <!--a href="#" id="add_vision_staffing_news" class="wd-add-project" style="margin-right:5px; "><span><?php //__('Vision staffing+') ?></span></a -->                    
                    <?php
                        echo $this->Form->create('Category');
                        if(!empty($activities_activated)){
                            $op = ($activities_activated == 1) ? 'selected="selected"' : '';
                            $in = ($activities_activated == 2) ? 'selected="selected"' : '';
                            $ar = ($activities_activated == 3) ? 'selected="selected"' : '';
                            //$arch = ($activities_activated == 4) ? 'selected="selected"' : '';
                        }
                    ?>
                        <select style="margin-right:11px; width:13.5% !important; padding: 6px;" class="wd-customs" id="FilterStatusActivity">
                            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("Activated", true)?></option>
                            <option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Not Activated", true)?></option>
                            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Activated & Not Activated", true)?></option>
                            <!--option value="4" <?php //echo isset($arch) ? $arch : '';?>><?php //echo  __("Archived", true)?></option-->
                        </select>
                    <?php
                        echo $this->Form->end();
                    ?> 
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden; margin-top: 30px;">

                </div>
                <?php echo $this->element('grid_status'); ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php echo $html->script('slick_grid/slick.editors'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script(array('slick_grid_custom')); ?>
<?php echo $html->script('responsive_table.js'); ?>
<?php

function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        ));

//unset($activityColumn['accessible_profit'], $activityColumn['linked_profit']);
$map = array();
$columnAlignRight = array(
    'price', 'md', 'raf', 'forecast', 'avancement', 'real_price', 'c23', 'c24', 'c25', 'c26', 'c27',
    'c28', 'c29', 'c31', 'c32', 'c33', 'c34', 'c35', 'c36', 'c37', 'c38', 'c39', 
    'completed', 'remain', 'total_costs_var', 'internal_costs_var', 'external_costs_var', 
    'external_costs_progress', 'assign_to_profit_center', 'assign_to_employee',
);
$columnAlignRightAndEuro = array(
    'sales_sold', 'sales_to_bill', 'sales_billed', 'sales_paid', 'total_costs_budget', 'total_costs_forecast',
    'total_costs_engaged', 'total_costs_remain', 'internal_costs_budget', 'internal_costs_forecast', 'internal_costs_engaged',
    'internal_costs_remain', 'external_costs_budget', 'external_costs_forecast', 'external_costs_ordered',
    'external_costs_remain', 'external_costs_progress_euro', 'internal_costs_average'
);
$columnAlignRightAndManDay = array(
    'internal_costs_budget_man_day', 'sales_man_day', 'total_costs_man_day', 'internal_costs_forecasted_man_day', 'external_costs_man_day',
    'workload_y', 'workload_last_one_y',
    'workload_last_two_y', 'workload_last_thr_y', 'workload_next_one_y', 'workload_next_two_y', 'workload_next_thr_y',
    'consumed_y', 'consumed_last_one_y', 'consumed_last_two_y', 'consumed_last_thr_y', 'consumed_next_one_y', 'consumed_next_two_y',
    'consumed_next_thr_y', 'workload', 'overload'
);
$columnNotCalculationConsumed = array(
    'id', 'no.', 'MetaData', 'name', 'long_name', 'short_name', 'code2', 'code1', 'family_id', 'subfamily_id', 'actif',
    'code4','code5', 'code6', 'code7', 'code8', 'code9', 'code10',
    'pms', 'accessible_profit', 'linked_profit', 'code3', 'start_date', 'end_date', 'c42', 'c40',
    'raf', 'c30', 'c44', 'completed', 'total_costs_var', 'internal_costs_var', 'external_costs_var', 'assign_to_profit_center', 
    'assign_to_employee', 'external_costs_progress', 'activated', 'action.', 'budget_customer_id', 'project_manager_id', 'import_code'
);
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
//ADD FIELDS TO MODIFY. ADD CODE BY VINGUYEN 2015/06/13
$FIELDS = array();
foreach ($activityColumn as $key => $column) {
    $map['C' . $column['code']] = $key;
    if (empty($column['display'])) {
        continue;
    }
    $editor = array();
    if (in_array($key, array('raf', 'price', 'md'))) {
        $editor = array(
            'editor' => 'Slick.Editors.numericValue'                        
        );
    } elseif ($column['code'] >= 32 && $column['code'] <= 39 || $column['code']==41) {
        $editor = array(
            'editor' => 'Slick.Editors.decimalValue'
        );
    }elseif(($column['code'] >= 40 && $column['code'] <= 45) || ($column['code'] >= 87 && $column['code'] <= 93)){
        $editor = array(
            'editor' => 'Slick.Editors.textBox'
        );
    }
    elseif ($key === 'consumed' || $key === 'consumed_current_year' || $key === 'consumed_current_month') {
        $editor = array(
            'formatter' => 'Slick.Formatters.Action'
        );
    } elseif ($key === 'name') {
        $editor = array(
            'formatter' => 'Slick.Formatters.HyperlinkCellFormatter'
        );
    } elseif ($key === 'actif'){
        $editor = array(
            'editor' => 'Slick.Editors.selectBox'
        );
    } elseif ($key === 'budget_customer_id'){
        $editor = array(
            'editor' => 'Slick.Editors.selectBox'
        );
    } elseif ($key === 'project_manager_id'){
        $editor = array(
            'editor' => 'Slick.Editors.GetProjectManager',
            'formatter' => 'Slick.Formatters.GetProjectManager'
        );
    }
    if(in_array($key, $columnAlignRight)){
        $editor['formatter'] = 'Slick.Formatters.numberVal';
    }
    if(in_array($key, $columnAlignRightAndEuro)){
        $editor['formatter'] = 'Slick.Formatters.numberValEuro';
    }
    if(in_array($key, $columnAlignRightAndManDay)){
        $editor['formatter'] = 'Slick.Formatters.numberValManDay';
    }
	//ADD FIELDS TO MODIFY. ADD CODE BY VINGUYEN 2015/06/13
	if(isset($editor['editor']))
	{
		$FIELDS[] = $key;
	}
	//END 2015/06/13
    $columns[] = array_merge(array(
        'id' => $key,
        'field' => $key,
        'name' => !empty($listDatas) && !empty($listDatas[$key]) ? $listDatas[$key] : __($column['name'], true),
        'code' => 'C' . $column['code'],
        'calculate' => $column['calculate'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true), $editor);
	
}
$_activited[] = array(
        'id' => 'activated',
        'field' => 'activated',
        'name' => __('Activated', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    );
$columns =  array_merge($columns, $_activited);
$i = 1;
$totalHeaders = array();
$dataView = array();
$selectMaps = array(
    'actif' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'pms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'budget_customer_id' => $budgetCustomers,
    'project_manager_id' => $projectManagers
);
$i18n = array(
    'M.D' => __('M.D', true)
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
foreach ($activities as $activity) {
    $data = array(
        'id' => $activity['Activity']['id'],
        'no.' => $i++,
        'backupPM' => array(),
        'project_manager_id' => array(),
        'MetaData' => array()
    );
    foreach ($activityColumn as $key => $column) {
        $data[$key] = '';
        if ($column['calculate'] === false && isset($activity['Activity'][$key])) {
            $data[$key] = (string) $activity['Activity'][$key];
            if ($key === 'actif' || $key === 'pms') {
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
    
    $data['activated'] = $activity['Activity']['activated'] ? 'yes' : 'no';
    $data['actif'] = $activity['Activity']['actif'] ? 'yes' : 'no';
    $data['budget_customer_id'] = (string) $activity['Activity']['budget_customer_id'];
    if(!empty($activity['project_manager_id'])){
        foreach ($activity['project_manager_id'] as $value) {
            if(!empty($value['project_manager_id'])){
                $data['project_manager_id'][] = $value['project_manager_id'];
                $data['backupPM'][$value['project_manager_id']] = !empty($value['is_backup']) ? "1" : "0";
            }
        }
    } else {
        $data['project_manager_id'] = array();
    }
    if (isset($sumEmployees[$data['id']])) {
        foreach ($sumEmployees[$data['id']] as $id => $val) {
            $reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
            $data['real_price'] += $val * $reals;
        }
    } else {
        $data['real_price'] = 0;
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
    // consumed of current year and consumed of current month
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
 
    // Read the calculate formular and get the object
    foreach ($activityColumn as $key => $column) {
        if (empty($column['calculate'])) {
            if(in_array($key, $columnNotCalculationConsumed)){
                //do nothing
            } else {
                $val = $data[$key] ? $data[$key] : 0;
                if(!isset($totalHeaders[$key])){
                    $totalHeaders[$key] = 0;
                }
                $totalHeaders[$key] += $val;
            } 
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
        if(in_array($key, $columnNotCalculationConsumed)){
            //do nothing
        } else {
            $val = $data[$key] ? $data[$key] : 0;
            if(!isset($totalHeaders[$key])){
                $totalHeaders[$key] = 0;
            }
            $totalHeaders[$key] += $val;
        } 
    }
    $data['action.'] = '';
    $dataView[] = $data;
}
//debug($totalHeaders);
//exit;
$viewManDay = __('M.D', true);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    function number_format (number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
    var DataValidator = {};
    (function($){
        
        $(function(){
            var $this = SlickGridCustom;
            var  activityColumn = <?php echo json_encode($activityColumn); ?>;
            var  viewManDay = <?php echo json_encode($viewManDay); ?>;
            var  map = <?php echo json_encode($map); ?>;
            var  url = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var  urlActivityTask = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('controller' => 'activity_tasks', 'action' => 'index', '%2$s')))); ?>;
            $this.onApplyValue = function(item){
                $.extend(item, {backupPM : []})
            };
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            
			//ADD FIELDS TO MODIFY. ADD CODE BY VINGUYEN 2015/06/13
			$FIELDS = <?php echo json_encode($FIELDS); ?>;
			$activityColumns = {};
			$.each($FIELDS,function(_ind,_field){
				$activityColumns[_field] = {defaulValue : ''};
			});
            $this.fields = {
                id : {defaulValue : 0},
                price : {defaulValue : ''},
                md : {defaulValue : ''},
                actif : {defaulValue : ''},
                raf : {defaulValue : ''},
                c32: {defaulValue : ''},
                c33: {defaulValue : ''},
                c34: {defaulValue : ''},
                c35: {defaulValue : ''},
                c36: {defaulValue : ''},
                c37: {defaulValue : ''},
                c38: {defaulValue : ''},
                c39: {defaulValue : ''},
				c40: {defaulValue : ''},
                c41: {defaulValue : ''},
                c42: {defaulValue : ''},
                c43: {defaulValue : ''},
                c44: {defaulValue : ''},
				c45: {defaulValue : ''},
                activated : {defaulValue : ''},
                budget_customer_id : {defaulValue : ''},
                backupPM: {defaulValue : ''},
                project_manager_id : {defaulValue : ''}
            };
            $.extend($this.fields,$activityColumns);
            $.extend(Slick.Editors,{
                decimalValue  : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 10).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));	
                        if(val != '0' && !/^(([\-1-9][0-9]{0,6})|(0))(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
							$(e.currentTarget).addClass('KO');
                            return false;
                        }
						else
						{
							$(e.currentTarget).removeClass('KO');
						}
                    });
                },
                GetProjectManager : function(args){
                    var $options,hasChange = false,isCreated = false;
                    var defaultValue = [];
                    var scope = this;
                    var preload = function(){
                        // Get Ajax Employee
                        var getEmployee = function(){
                            if(scope.getValue(true) === true){
                                return;
                            }
                            scope.setValue(true);
                            $.ajax({
                                url : '<?php echo $html->url(array('action' => 'get_project_manager')); ?>',
                                cache :  false,
                                type : 'GET',
                                success : function(data){
                                    $options.html(data);
                                    scope.input.html('');
                                    scope.setValue(defaultValue);
                                    scope.input.select();
                                    // filter menu
                                    initMenuFilter($('.select-employees'));
                                    
                                    $options.find('.id-hander').each(function(){
                                        var $el =  $(this);
                                        $.each(defaultValue, function(undefined,v){
                                            if($el.val() == v){
                                                $el.prop('checked' , true);
                                                if(args.item.backupPM[v] == '1'){
                                                    $el.closest('tr').find('.bk-hander').prop('checked' , true);
                                                }
                                                return false;
                                            }
                                        });
                                    }).add($options.find('.bk-hander')).click(function(){
                                        var list = [],c = args.column.id ,
                                        $hander = $(this).closest('tr').find('.id-hander');
                                        args.item.backupPM = {};
                                        //args.item.project_manager_id = {};
                                        if(this.checked && !$hander.is(':checked')){
                                            $hander.prop('checked' , true);
                                        }
                                        //var count = 0;
                                        $options.find('.id-hander:checked').each(function(){
                                            var $el =  $(this),id= $el.val();
                                            list.push(id);
                                            //args.item.project_manager_id[count] = id;
                                            args.item.backupPM[id] = $el.closest('tr').find('.bk-hander:checked').length > 0 ? 1 : 0;
                                            //count++;
                                        });
                                        args.item[c] = list;
                                        hasChange = true;
                                        scope.setValue(list);
                                    });
                                    $options.find('.no-employee input').click(function(){
                                        getEmployee();
                                    });
                                }
                            });
                        };
                        getEmployee();
                    };
                    $.extend(this, new BaseSlickEditor(args));
                    
                    $options = $('<div class="multiSelectOptionCustoms" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px;"></div>').appendTo('body');
                    var hideOption = function(){
                        scope.input.removeClass('active').removeClass('hover');
                        $options.css({
                            visibility:'hidden',
                            display : 'none'
                        });
                    }
                    this.input = $('<a href="javascript:void(0);" class="multiSelect"></a>')
                    .appendTo(args.container)
                    .hover( function() {
                        scope.input.addClass('hover');
                    }, function() {
                        scope.input.removeClass('hover');
                    })
                    .click( function(e) {
                        // Show/hide on click
                        if(scope.input.hasClass('active')) {
                            hideOption();
                        } else {
                            var offset = scope.input.addClass('active').offset();
                            $options.css({
                                top:  offset.top + scope.input.outerHeight() + 'px',
                                left: offset.left + 'px',
                                visibility:'visible',
                                display : 'block'
                            });
                            if(scope.input.width() < 320){
                                $options.width(320);
                            }
                        }
                        e.stopPropagation();
                        return false;
                    });
                     
                    $(document).click( function(event) {
                        if(!($(event.target).parents().andSelf().is('.multiSelectOptionCustoms'))){
                            hideOption();
                        }
                    });
                    
                    var destroy = this.destroy;
                    this.destroy = function () {
                        $options.remove();
                        destroy.call(this, $.makeArray(arguments));
                    };

                    this.getValue = function (val) {
                        if(this.input.html() == 'Loading ...'){
                            if(val ==true){
                                return true;
                            }
                            return '';
                        }
                        return this.input.html().split(',');
                    };

                    this.setValue = function (val) {
                        if(val === true){
                            val = 'Loading ...';
                        }else{
                            val = Slick.Formatters.GetProjectManager(null,null,val, args.column, args.item);
                        }
                        this.input.html(val);
                    };

                    this.loadValue = function (item) {
                        defaultValue = item[args.column.field] || "";
                    };

                    this.serializeValue = function () {
                        if(!isCreated){
                            this.loadValue(args.item);
                            preload();
                        }
                        return scope.getValue();
                    };
                    
                    var applyValue = this.applyValue;
                    this.applyValue = function (item, state) {
                        if($.isEmptyObject(item)){
                            applyValue.call(this, item , state);
                        }
                        $.extend(item ,args.item , true);
                    };

                    this.isValueChanged = function () {
                        return (hasChange == true);
                    };

                    this.validate = function () {
                        var option = $this.fields[args.column.id] || {};
                        var result = {
            				valid: true,
            				message: typeof option.message != 'undefined' ? option.message : $this.t('This information is not blank!')
            			},val = this.getValue();
            			if(option.allowEmpty === false && !val.length && !this.isCreate){
            				result.valid = false;
            			}
            			if(result.valid && val.length){
            				if(option.maxLength && val.length > option.maxLength){
            					result = {
            						valid: false,
            						message: $this.t('Please enter must be no larger than %s characters long.' , option.maxLength)
            					};
            				}else if($.isFunction(args.column.validator)){
            					result = args.column.validator.call(this, val, args);
            				}
            			}
            			if(!result.valid && result.message){
            				this.tooltip(result.message , result.callback);
            			}
            			return result;
                    };

                    this.focus();
                }
            });
            $this.onCellChange = function(args){
                $.each (activityColumn , function(key){
                    if(!this.calculate){
                        return;
                    }
                    
                    var cal = this.calculate;
                    if (this.match) {
                        $.each(this.match , function(e){
                            var val = args.item[map[this]] ? parseFloat(args.item[map[this]] , 10) : 0;
                            cal = cal.replace(new RegExp(this , 'g'),val);
                        });
                    }
                    cal = eval('(' + cal + ');');
                    
                    if(!$.isNumeric(cal)){
                        cal = 0;
                    }else if( Math.floor(args.item[key]) != args.item[key]){
                        cal = Number(cal).toFixed(2);
                    }
                    args.item[key]= cal;
                });
                return true;
            };
            var startCurrentYear = <?php echo json_encode('01-01-'.date('Y', time()));?>;
            var endCurrentYear = <?php echo json_encode('31-12-'.date('Y', time()));?>;
            var startMonthYear = <?php echo json_encode('01-'.date('m', time()).'-'.date('Y', time()));?>;
            var endMonthYear = <?php echo json_encode('31-'.date('m', time()).'-'.date('Y', time()));?>;
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    if(columnDef.id == 'consumed_current_year'){
                        var customUrl = dataContext.id + '?start='+startCurrentYear+'&end='+endCurrentYear;
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + $this.t(url,value || '',customUrl) + '</span>', columnDef, dataContext);
                    } else if(columnDef.id == 'consumed_current_month'){
                        var customUrl2 = dataContext.id + '?start='+startMonthYear+'&end='+endMonthYear;
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + $this.t(url,value || '',customUrl2) + '</span>', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + $this.t(url,value || '',dataContext.id) + '</span>', columnDef, dataContext);
                    }
                },
                HyperlinkCellFormatter : function(row, cell, value, columnDef, dataContext) { 
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlActivityTask,value || '', dataContext.id), columnDef, dataContext);
                    //return "<a href='" + value + "'>" + value + "</a>"; 
                },
                numberVal : function(row, cell, value, columnDef, dataContext){    
                    value = value ? value : 0;
                    var icon = '';
                    if(columnDef.id == 'completed' || columnDef.id =='total_costs_var' || columnDef.id == 'internal_costs_var' 
                    || columnDef.id == 'external_costs_var' || columnDef.id == 'external_costs_progress'
                    || columnDef.id == 'assign_to_profit_center' || columnDef.id =='assign_to_employee'
                    ){
                        icon = '%';
                    }
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
        		},
                numberValEuro : function(row, cell, value, columnDef, dataContext){ 
                    value = value ? value : 0;
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' &euro;</span>', columnDef, dataContext);
        		},
                numberValManDay : function(row, cell, value, columnDef, dataContext){   
                    value = value ? value : 0;
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +viewManDay+ '</span> ', columnDef, dataContext);
        		},
                GetProjectManager : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['project_manager_id'][val] + (dataContext.backupPM[val] == '1' ? backupText.text : '')); 
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                }                            
            });
            
            var initMenuFilter = function($menu){
                var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                $menu.before($filter);
        
                var timeoutID = null, searchHandler = function(){
                    var val = $(this).val();
                    var te = $($menu).find('tbody tr td.wd-employ-data div label').html();
                    
                    $($menu).find('tbody tr td.wd-employ-data div label').each(function(){
                        var $label = $(this).html();
                        $label = $label.toLowerCase();
                        val = val.toLowerCase();
                        if(!val.length || $label.indexOf(val) != -1 || !val){
                            //$(this).parent().css('display', 'block');
                            //$(this).parent().next().css('display', 'block');
                            $(this).parent().parent().parent().removeClass('wd-displays');
                        } else{
                            //$(this).parent().parent().parent().css('display', 'none');
                            //$(this).parent().next().css('display', 'none');
                            $(this).parent().parent().parent().addClass('wd-displays');
                        }
                    });
                };
        
                $filter.find('input').click(function(e){
                    e.stopImmediatePropagation();
                }).keyup(function(){
                    var self = this;
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(function(){
                        searchHandler.call(self);
                    } , 200);
                });
        
            };
            
            $this.canModified =  <?php echo json_encode($canModified); ?>;
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro); ?>;
            $this.columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay); ?>;
            $this.columnNotCalculationConsumed = <?php echo json_encode($columnNotCalculationConsumed); ?>;
            var totalHeaders = <?php echo json_encode($totalHeaders);?>;
            
            $this.url =  '<?php echo $html->url(array('action' => 'update_review')); ?>';
            var ControlGrid = $this.init($('#project_container'),data,columns , {
                enableAddRow : true,
                showHeaderRow : true
            });
            
            var Columns = ControlGrid.getColumns();
           
            //var maxWidth = ControlGrid.getDataItem(1) ? ControlGrid.getDataItem(1).name.length : 1;
//            var width_long_name = ControlGrid.getDataItem(1) ? ControlGrid.getDataItem(1).long_name.length : 1;
//            var width_family = 0 ;
//            var width_subfamily = 0;
//            //console.log(ControlGrid.getDataItem(1));
//            var i,j;
//            for (i =1 ; i < ControlGrid.getDataLength() ; i++){
//                var widthName = ControlGrid.getDataItem(i) ? ControlGrid.getDataItem(i).name.length : 1;
//               if(widthName > maxWidth)
//                    maxWidth = widthName;
//               var widthLongName = ControlGrid.getDataItem(i) ? ControlGrid.getDataItem(i).long_name.length : 1;
//               if(widthLongName > width_long_name)
//                    width_long_name = widthLongName;
//               
//            }
//            $.each($this.selectMaps.family_id , function(_k ,_v){
//                if(_v.length > width_family){
//                    width_family = _v.length;
//                }
//            })
//            $.each($this.selectMaps.subfamily_id , function(__k , __v){
//                if(__v.length > width_subfamily){
//                    width_subfamily = __v.length;
//                }
//            })
            
            //if (Columns && columns.length > 0) {
//                Columns[1].width = maxWidth * 8;
//                Columns[2].width = width_long_name * 4;
//                Columns[4].width = width_family * 7;
//                Columns[5].width = width_subfamily * 7;
//                ControlGrid.applyColumnHeaderWidths();
//                ControlGrid.applyColumnWidths();
//            }
            HistoryFilter.setVal = function(name, value){
                var $data = $("[name='"+name+"']").each(function(){
                    var $element = $(this);
                    if($element.is(':checkbox') || $element.is(':radio')){
                        if(!$.isArray(value)){
                            value = [value];
                        }
                        $element.prop('checked', $.inArray($element.val(), value) != -1);
                    }else{
                        $element.val(value);
                        $element.keypress();
                    }
                    $element.data('__auto_trigger' , true);
                    $element.change();
                });
                var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                return $data.length > 0;
            }
            /**
             * Calculation width of grid.
             */
            var cols = ControlGrid.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            ControlGrid.onScroll.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onSort.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onColumnsResized.subscribe(function (e, args) {
				var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('.row-number').parent().addClass('row-number-custom');
			});
            if(columns){
                var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">';
                $.each(columns, function(index, value){
                    var idOfHeader = value.id;
                    var valOfHeader = (totalHeaders[value.id] || totalHeaders[value.id] == 0) ? totalHeaders[value.id] : '';
                    
                    if(value.id === 'sales_man_day'
                    || value.id === 'total_costs_man_day'
                    || value.id === 'internal_costs_forecasted_man_day'
                    || value.id === 'external_costs_man_day'
                    || value.id === 'internal_costs_budget_man_day'
                    ){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
                    } else if(value.id === 'sales_sold' || value.id === 'sales_to_bill'
                    || value.id === 'sales_billed' || value.id === 'sales_paid'
                    || value.id === 'total_costs_budget' || value.id === 'total_costs_forecast'
                    || value.id === 'total_costs_engaged' || value.id === 'total_costs_remain'
                    || value.id === 'internal_costs_budget' || value.id === 'internal_costs_forecast'
                    || value.id === 'internal_costs_engaged' || value.id === 'internal_costs_remain'
                    || value.id === 'external_costs_budget' || value.id === 'external_costs_forecast'
                    || value.id === 'external_costs_ordered' || value.id === 'external_costs_remain'
                    || value.id === 'external_costs_progress_euro'
                    || value.id === 'internal_costs_average'
                    ){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' &euro;';
                    } else {
                        if(valOfHeader){
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ');
                        }
                    }
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l'+index;
                    var right = 'r'+index;
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom '+left+' '+right+'" id="'+idOfHeader+'"><p>'+valOfHeader+'</p></div>';
                    
                });
                headerConsumed += '</div>';
                $('.slick-header-columns').after(headerConsumed);
            }
            
        });
        /* Slow performace here */
        /* @todo: fix performance @huupc */
        $("#add_vision_staffing_news").live('click',function(){
            $("#dialog_vision_staffing_news").dialog('option',{title:'Vision Staffing+ Filter'}).dialog('open');
        });

        $(".cancel").live('click',function(){
            $("#dialog_vision_staffing_news").dialog('close');
        });
        
        $("#ok_sum").click(function(){
            $("#form_vision_staffing_news").submit();
        });
        
        $('#ProjectProjectGanttId0,#ProjectProjectGanttId1').click(function(){
            if($('#ProjectProjectGanttId1').prop('checked')){
                $('#display-real-time').show().find('input').prop('disabled' , false);
            }else{
                $('#display-real-time').hide().find('input').prop('disabled' , true);
            }
            
        }).filter(':checked').click();
        
        $('#ProjectProjectGanttIdNews0, #ProjectProjectGanttIdNews1').click(function(){
            if($('#ProjectProjectGanttIdNews1').prop('checked')){
                $('#display-real-time-news').show().find('input').prop('disabled' , false);
            }else{
                $('#display-real-time-news').hide().find('input').prop('disabled' , true);
            }
            
        }).filter(':checked').click();
        
    })(jQuery);
    
jQuery('document').ready(function(){
    var company_id = <?php echo json_encode($company_id);?>;
    $('#FilterStatusActivity').change(function(){
        $('#FilterStatusActivity option').each(function(){
            if($(this).is(':selected')){
                var id = $('#FilterStatusActivity').val();
                window.location = ('/activities/review/'+company_id+'?activities_activated=' +id);
            }
        });
    });
});
</script>
<?php
echo $this->Form->create('ExportVision', array('url' => array('controller' => 'activity_tasks', 'action' => 'export_system'), 'type' => 'file'));
echo $this->Form->hidden('showType');
echo $this->Form->hidden('summary');
echo $this->Form->hidden('from');
echo $this->Form->hidden('to');
echo $this->Form->hidden('activated');
echo $this->Form->hidden('activityName');
echo $this->Form->hidden('family');
echo $this->Form->hidden('subFamily');
echo $this->Form->hidden('profit_center');
echo $this->Form->hidden('employee');
echo $this->Form->end();
?>