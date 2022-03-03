<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;    
        }
    </style>
<![endif]-->
<?php echo $html->css(array('jquery.multiSelect','slick_grid/slick.grid_v2','slick_grid/slick.pager','slick_grid/slick.common_v2','slick_grid/slick.edit')); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<script type="text/javascript">

    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'activities', 'action' => 'export_list')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
     <?php
    echo $this->Form->create('Exportplus', array(
        'type' => 'POST',
        'url' => array('controller' => 'activities', 'action' => 'export_listplus')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-listplus'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Activity management", true); ?></h2>
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV_Activity_Task" style="margin-right:5px; "  title="<?php __('Import CSV Activity Task')?>"><span><?php __('Import CSV Activity Task') ?></span></a>
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" style="margin-right:5px; "  title="<?php __('Import CSV Activity')?>"><span><?php __('Import CSV') ?></span></a>
                    <!--a href="#" id="export-submit" class="wd-add-project" style="margin-right:5px; "><span><?php //__('Export') ?></span></a-->
                    <!--a href="#" id="export-submitplus" class="export-excel-icon-all" style="margin-right:5px; " title="<?php //__('Export Excel')?>"><span><?php //__('Export +') ?></span></a-->
                    <a href="javascript:void(0);" id="add-activity" class="wd-add-project" style="margin-right:5px;" onclick="addActivities();"><span><?php __('Activity') ?></span></a>
                    <!--ADD CODE BY VINGUYEN 07/05/2014--->
					<?php echo $this->element('multiSortHtml'); ?>
                    <!--END-->
                    <?php
                        echo $this->Form->create('Category');
                        if(!empty($activities_activated)){
                            $op = ($activities_activated == 1) ? 'selected="selected"' : '';
                            $in = ($activities_activated == 2) ? 'selected="selected"' : '';
                            $ar = ($activities_activated == 3) ? 'selected="selected"' : '';
                        }
                    ?>
                    	<select style="margin-right:11px;  padding: 6px;" class="wd-customs" id="FilterStatusActivityPersonalize">
                            <option value="0"><?php echo  __("--Select--", true);?></option>
                            <?php echo $personalizeList; ?>
                        </select>
                        <select style="margin-right:11px;  padding: 6px;" class="wd-customs" id="FilterStatusActivity">
                            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("Activated", true)?></option>
                            <option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Not Activated", true)?></option>
                            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Activated & Not Activated", true)?></option>
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
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                </div>
                <?php echo $this->element('grid_status'); ?>
            </div>
        </div>
    </div>
</div>
<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV Activity" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'activities', 'action' => 'import')));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(Allowed file type: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import -->
<!-- dialog_import_activity_task -->
<div id="dialog_import_CSV_activity_task" title="Import CSV Activity Task" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadFormTask', 'type' => 'file', 'target' => '_blank',
        'url' => array('controller' => 'activity_tasks', 'action' => 'import_csv_task')));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileFieldTask[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(Allowed file type: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit-task" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="errorTask"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import -->
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
//ADD BY VINGUYEN 16/05/2014
$i=0;
$profitCentersTemp = array();
foreach($profitCenters as $key=>$value)
{
	$profitCentersTemp[$i]=array('key'=>$key,'value'=>$value);
	$i++;
}
//END
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
$no = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ));
// if actif(open), when display = 1, show $col.

$columnAlignRight = array(
    'price', 'md', 'raf', 'forecast', 'avancement', 'real_price', 'c23', 'c24', 'c25', 'c26', 'c27',
    'c28', 'c29', 'c31', 'c32', 'c33', 'c34', 'c35', 'c36', 'c37', 'c38', 'c39', 
    'workload', 'overload', 'completed', 'remain', 'total_costs_var', 'internal_costs_var', 'external_costs_var', 
    'external_costs_progress', 'assign_to_profit_center', 'assign_to_employee','workload_current_year'
);
$columnAlignRightAndEuro = array(
    'sales_sold', 'sales_to_bill', 'sales_billed', 'sales_paid', 'total_costs_budget', 'total_costs_forecast',
    'total_costs_engaged', 'total_costs_remain', 'internal_costs_budget', 'internal_costs_forecast', 'internal_costs_engaged',
    'internal_costs_remain', 'external_costs_budget', 'external_costs_forecast', 'external_costs_ordered',
    'external_costs_remain', 'external_costs_progress_euro', 'internal_costs_average'
);
$columnAlignRightAndManDay = array(
    'internal_costs_budget_man_day', 'sales_man_day', 'total_costs_man_day', 'internal_costs_forecasted_man_day', 'external_costs_man_day'
);
$columnNotCalculationConsumed = array(
    'id', 'no.', 'MetaData', 'name', 'long_name', 'short_name', 'code2', 'code1', 'family_id', 'subfamily_id', 'actif', 
    'pms', 'accessible_profit', 'linked_profit', 'code3', 'start_date', 'end_date', 'c42', 'c40',
    'raf', 'c30', 'c44', 'completed', 'total_costs_var', 'internal_costs_var', 'external_costs_var', 'assign_to_profit_center', 
    'assign_to_employee', 'external_costs_progress', 'activated', 'action.', 'budget_customer_id', 'project_manager_id', 'import_code'
);
$columnEditor=array('long_name','short_name','code1','code2','code3');
$columnSelectBox=array('family_id','subfamily_id','budget_customer_id','activated');
foreach($activityColumn as $key => $column){
	if(isset($column['calculate'])&&!empty($column['calculate']))
	{
		$_calculate=$column['calculate'];
	}
	else
	{
		$_calculate='';
	}
	if(isset($column['code'])&&!empty($column['code']))
	{
		$_code=$column['code'];
	}
	else
	{
		$_code='';
	}
	$map['C' . $_code] = $key;
	$editor = array();
	if ($key == 'name') {
	 	$editor = array(
			'validator' => 'DataValidator.isUnique',
	        'editor' => 'Slick.Editors.textBox',
			'formatter' => 'Slick.Formatters.HyperlinkCellFormatterActivity'
		);
	} 
    else if($key == 'pms'){
		$editor = array(
			'editor' => 'Slick.Editors.selectBox',
			'validator' => 'DataValidator.pms'
		);
	}
    else if($key == 'start_date'){
		$editor=array(
			'editor' => 'Slick.Editors.datePicker',
			'validator' => 'DataValidator.startDate'
		);
	}
    else if($key == 'end_date'){
		$editor=array(
			'editor' => 'Slick.Editors.datePicker',
			'validator' => 'DataValidator.endDate'
		);
	}
    else if($key == 'import_code'){
		$editor=array(
			'validator' => 'DataValidator.isUniqueImportCode',
			'editor' => 'Slick.Editors.textBox'
   		);
	}
	else if($key == 'accessible_profit'){
		$editor = array(
			'editor' => 'Slick.Editors.mselectBoxCustom',
			'formatter' => 'Slick.Formatters.selectBox'
		);
	}
	elseif ($key == 'project_manager_id'){
		$editor = array(
			'editor' => 'Slick.Editors.GetProjectManager',
			'formatter' => 'Slick.Formatters.GetProjectManager'
		);
	}
	else if($key == 'linked_profit'){
		$editor = array(
			'editor' => 'Slick.Editors.selectBoxCustom'
		);
	}
	else if($key == 'project'){
		$editor = array(
			'formatter' => 'Slick.Formatters.HyperlinkCellFormatter'
		);
	}
	else if (in_array($key,$columnSelectBox)) {
		$editor=array(
			'editor' => 'Slick.Editors.selectBox'
		);
	}
	else if ($_code >= 32 && $_code <= 39) {
		$editor = array(
			'editor' => 'Slick.Editors.decimalValue'
		);
	}
	else if(($_code >= 40 && $_code <= 45)||(in_array($key,$columnEditor))){
		$editor = array(
			'editor' => 'Slick.Editors.textBox' 
		);
	}
	else if ($key === 'consumed' || $key === 'consumed_current_year' || $key === 'consumed_current_month') {
		$editor = array(
			'formatter' => 'Slick.Formatters.HyperlinkCellFormatterConsumed'
		);
	}
	else if((isset($column['month'])&&!empty($column['month']))||in_array($key, $columnAlignRight)){
		$editor = array(
			'formatter' => 'Slick.Formatters.numberVal'
		);
	}
	else if(in_array($key,$columnAlignRightAndEuro))
	{
		$editor = array(
			'formatter' => 'Slick.Formatters.numberValEuro'
		);
	}
	else if(in_array($key,$columnAlignRightAndManDay))
	{
		$editor = array(
			'formatter' => 'Slick.Formatters.numberValManDay'
		);
	}
	/*if(isset($column['month'])&&!empty($column['month']))
	{
		$col = array(
			'id' => $key,
			'field' => $key,
			'name' => __($column['name'], true).' '.__($column['month'], true),
			'code' => 'C' . $_code,
			'calculate' => $_calculate,
			'width' => 120,
			'sortable' => true,
			'resizable' => true
		);
	}
	else
	{*/
		$col = array(
			'id' => $key,
			'field' => $key,
			'name' => __($column['name'], true),
			'code' => 'C' . $_code,
			'calculate' => $_calculate,
			'width' => 120,
			'sortable' => true,
			'resizable' => true
		);
	//}
	$cols[]=array_merge($col,$editor);
}
// action activities index
$act[] =  array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        );
// merge $columns, $col, $act
if(!empty($cols)){
    $columns = array_merge($no,$cols, $act);
} else {
    $columns = array_merge($no, $act);
}
$i = 1;
$dataView = array();
$selectMaps = array(
    'pms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'actif' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'linked_profit' => $profitCenters,
    'accessible_profit' => $profitCenters,
    'project' => $projects,
    'budget_customer_id' => $budgetCustomers,
    'project_manager_id' => $projectManagers
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
        if (isset($column['calculate'] )&&$column['calculate'] === false && isset($activity['Activity'][$key])) {
            $data[$key] = (string) $activity['Activity'][$key];
            if ($key === 'actif' || $key === 'pms') {
                $data[$key] = $data[$key] ? 'yes' : 'no';
            }
        }
    }
	if(isset($data['start_date']))
	$data['start_date'] = $data['start_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $data['start_date'])) : '';
	if(isset($data['end_date']))
    $data['end_date'] = $data['end_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $data['end_date'])) : '';
    $data['consumed'] = isset($sumActivities[$data['id']]) ? $sumActivities[$data['id']] : 0;
	
    $data['actif'] = $activity['Activity']['actif'] ? 'yes' : 'no';
    $data['name'] = (string) $activity['Activity']['name'];
    $data['long_name'] = (string) $activity['Activity']['long_name'];
    $data['short_name'] = (string) $activity['Activity']['short_name'];
    $data['family_id'] = (string) $activity['Activity']['family_id'];
    $data['subfamily_id'] = (string) $activity['Activity']['subfamily_id'];
    $data['pms'] = $activity['Activity']['pms'] ? 'yes' : 'no';
    $data['project'] = (string) $activity['Activity']['project'];
    $data['budget_customer_id'] = (string) $activity['Activity']['budget_customer_id'];
	
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
			if(isset($data['real_price']))
            $data['real_price'] += $val * $reals;
			else
			$data['real_price'] = $val * $reals;
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
    $data['consumed_current_year'] = (string) !empty($dataOfCurrentYears[$activity['Activity']['id']]['consumed']) ? $dataOfCurrentYears[$activity['Activity']['id']]['consumed'] : '';
    $data['workload_current_year'] = (string) !empty($dataOfCurrentYears[$activity['Activity']['id']]['workload']) ? $dataOfCurrentYears[$activity['Activity']['id']]['workload'] : '';
	//workload and consumed by month
	if(isset($dataOfCurrentYears[$activity['Activity']['id']]))
	{
		$monthArray=array('jan','feb','mar','apr','may','june','july','aug','sept','oct','nov','dec');
		foreach($monthArray as $val)
		{
			//if(!empty($dataOfCurrentYears[$activity['Activity']['id']]['workload_'.$val])&&isset($dataOfCurrentYears[$activity['Activity']['id']]['workload_'.$val]))
			$data['workload_'.$val]=$dataOfCurrentYears[$activity['Activity']['id']]['workload_'.$val];
			//if(!empty($dataOfCurrentYears[$activity['Activity']['id']]['consumed_'.$val])&&isset($dataOfCurrentYears[$activity['Activity']['id']]['consumed_'.$val]))
			$data['consumed_'.$val]=$dataOfCurrentYears[$activity['Activity']['id']]['consumed_'.$val];
		}
	}
	//
    
    $data['activated'] = $activity['Activity']['activated'] ? 'yes' : 'no';
	if(isset($data['accessible_profit'])&&!empty($data['accessible_profit']))
    $data['accessible_profit'] = (array) Set::classicExtract($activity['AccessibleProfit'], '{n}.profit_center_id');
    $data['linked_profit'] = (string) !empty($activity['LinkedProfit']) ? $activity['LinkedProfit']['profit_center_id'] : '';
    
    $data['code1'] = (string) $activity['Activity']['code1'];
    $data['code2'] = (string) $activity['Activity']['code2'];
    $data['code3'] = (string) $activity['Activity']['code3'];
    $data['import_code'] = (string) $activity['Activity']['import_code'];
    $data['consumed'] = isset($sumActivities[$data['id']]) ? $sumActivities[$data['id']] : 0;
   
	foreach ($activityColumn as $key => &$column) {
        if(in_array($key, $columnNotCalculationConsumed)){
            //do nothing
        } else {
            $val = $data[$key] ? $data[$key] : 0;
            if(!isset($totalHeaders[$key])){
                $totalHeaders[$key] = 0;
            }
            $totalHeaders[$key] += $val;
        } 
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
}//exit;
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
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
            var $this = SlickGridCustom,families = <?php echo json_encode($mapFamilies); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.accessible_profit_sort = <?php echo json_encode($profitCentersTemp); ?>;
            $this.canModified =  true;
            $this.onApplyValue = function(item){
                $.extend(item, {backupPM : []})
            };
			var  urlActivityTask = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('controller' => 'activity_tasks', 'action' => 'index', '%2$s')))); ?>;
            var  urlDetail = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var  urlProject = <?php echo json_encode(urldecode($this->Html->link('%2$s', array('controller' => 'projects', 'action' => 'edit', '%1$s')))); ?>;
            // For validate date
            var actionTemplate =  $('#action-template').html(),backup = {};
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
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
                project_manager_id : {defaulValue : ''},
            };
			var  activityColumn = <?php echo json_encode($activityColumn); ?>;
            var  viewManDay = <?php echo json_encode($viewManDay); ?>;
            
            DataValidator.startDate = function(value , args){
                var _value = $this.getTime(value),end = args.item.end_date;
                return {
                    valid : !end || _value <= $this.getTime(end),
                    message : $this.t('The date must be smaller than or equal to %s' , end)
                };
            };
            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.name.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Activity has already been exist.')
                };
            }
            DataValidator.isUniqueImportCode = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.import_code.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Import Code has already been exist.')
                };
            }
            DataValidator.endDate = function(value , args){
                var _value = $this.getTime(value),start = args.item.start_date;
                return {
                    valid : !start || _value >= $this.getTime(start),
                    message : $this.t('The date must be greater than or equal to %s' , start)
                };
            };
            
            DataValidator.pms = function(value , args){
                function countActivityTask() {
                    var _id = args.item.id;
                    var href = "/activities/getAllActivityTask/" +_id;
                    var result="";
                    $.ajax({
                      url: href,
                      async: false, 
                      success:function(data) {
                         result = data; 
                      }
                   });
                   return result;
                }
                var _countActivityTask = countActivityTask();
                var _name = args.item.name;
                var _valid = true;
                var _mesa = '';
                if(args.item.project && args.item.project != ''){
                    _valid = false;
                    _mesa = $this.t('You can not set PMS = No for Activity %s Because It have linked Project!' , _name);
                } else if(_countActivityTask > 0){
                    _valid = false;
                    _mesa = $this.t('You can not set PMS = Yes for Activity %s Because It already had tasks!' , _name);
                }
                return {
                    valid : _valid,
                    message : _mesa
                };
            };
            var projects = <?php echo json_encode($projects); ?>;
            var startCurrentYear = <?php echo json_encode('01-01-'.date('Y', time()));?>;
            var endCurrentYear = <?php echo json_encode('31-12-'.date('Y', time()));?>;
            var urlConsumedCurrentYear = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            $.extend(Slick.Formatters,{
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
                            return false;
                        }
                    });
                },
                ActionConsumed : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlDetail,value || '',dataContext.id), columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                },
                HyperlinkCellFormatter : function(row, cell, value, columnDef, dataContext) { 
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlProject, value || '', projects[dataContext.project] || ''), columnDef, dataContext);
                },
				HyperlinkCellFormatterActivity : function(row, cell, value, columnDef, dataContext) { 
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlActivityTask, value || '',dataContext.id || ''), columnDef, dataContext);
                },
                HyperlinkCellFormatterConsumed : function(row, cell, value, columnDef, dataContext) { 
					var customUrl = dataContext.id + '?start='+startCurrentYear+'&end='+endCurrentYear;
                    if(parseFloat(value) == 0){
                        value = '';
                    }
                    return Slick.Formatters.HTMLData(row, cell, $this.t(urlConsumedCurrentYear, value || '', customUrl), columnDef, dataContext);
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
                },
            });
            $.extend(Slick.Editors,{
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
                },
                //MODIFY BY VINGUYEN 16/05/2014
				selectBoxCustom : function(args){
					this.isCreated = false;
					$.extend(this, new BaseSlickEditor(args));
					this.input = $($this.createSelectCustom($this.selectMaps[args.column.id] ,$this.t('-- Any --')))
					.appendTo(args.container).attr('rel','no-history').addClass('editor-select');
					
					var serializeValue = this.serializeValue;
					this.serializeValue = function(){
						if(!this.isCreated){
							this.input.combobox();
							this.tooltip(this.input.next().find('input'));
							this.isCreated = true;
						}
						return serializeValue.apply(this,$.makeArray(arguments));
					}
					var reset = this.reset;
					this.reset = function(){
						this.input.autocomplete('search', '');
						this.input.next().find('input').val($this.selectMaps[args.column.id][this.defaultValue]);
						reset.apply(this, $.makeArray(arguments));
					}
					this.destroy = function(){
						this.tooltip();
						this.input.combobox('destroy');
						this.input.remove();
					}
					this.focus();
					this.focus = function(){
						this.input.next().find('input').focus();
					}
					if($.isEmptyObject(args.item) && $this.fields[args.column.field] && typeof $this.fields[args.column.field].defaulValue != 'undefined'){
						this.setValue($this.fields[args.column.field].defaulValue);
					}
				},
				mselectBoxCustom : function(args){
					var multiSelect;
					//$this.selectMaps[args.column.id]
					$.extend(this, new BaseSlickEditor(args));
					this.input = $($this.createSelectCustom($this.selectMaps[args.column.id] ,$this.t('-- Any --')))
					.appendTo(args.container).attr('multiple','multiple').addClass('editor-select');
					!$.isEmptyObject(args.item) && this.loadValue(args.item);
					
					
					this.input.multiSelect({
						noneSelected: $this.t('-- Any --'), 
						appendTo : $('body'),
						oneOrMoreSelected: '*',
						selectAll: false
					});
					this.tooltip(multiSelect = $(args.container).find('a'));
					multiSelect.data("multiSelectOptions").find('input').attr('rel' , 'no-history');
						
					var destroy = this.destroy;
					
					this.destroy = function(){
						multiSelect.multiSelectDestroy();
						destroy.apply(this , $.makeArray(arguments));
					}
						
					this.isValueChanged = function(){
						return this.getValue().join(',') != (this.defaultValue || []).join(',');
					}
						
					this.getValue = function(){
						return multiSelect.data("multiSelectOptions").find('input:checked').map(function(){
							return $(this).val();
						}).get();
					}
						
					this.focus();
					this.focus = function(){
						multiSelect.focus();
					}
						
				}
				//END
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
            $this.onBeforeEdit = function(args){
                var result = true;
                if(args.column.field == 'subfamily_id' && args.item){
                    backup = $.extend({} , $this.selectMaps['subfamily_id']);
                    $this.selectMaps['subfamily_id'] = {};
                    $.each(families , function(){
                        if(this.parent_id == args.item.family_id){
                            $this.selectMaps['subfamily_id'][this.id] = this.name;
                        }
                    });
                }
                return result;
            };
            //MODIFY BY VINGUYEN 16/05/2014
			$this.createSelectCustom =  function(data,empty){
				var o = '';
				if(empty){
					o+= '<option selected="selected" value="">' + empty + '</option>';
				}
				dataTemp=<?php echo json_encode($profitCentersTemp);?>;
				$.each(dataTemp , function(i,v){
					o += '<option value="'+dataTemp[i]['key']+'">' + dataTemp[i]['value'] + '</option>';
				});
				return '<select>'+ o + '</select>';
			};
			//END
            $this.onCellChange = function(args){
                var result = true;
                if(args.column.field == 'family_id' && args.item && args.item.subfamily_id){
                    if(!families[args.item.subfamily_id] || families[args.item.subfamily_id].parent_id != args.item.family_id){
                        args.item.subfamily_id = '';
                    }
                }
                return result;
            };
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                name : {defaulValue : '' , allowEmpty : false , maxLength : 40},
                long_name : {defaulValue : '' , maxLength : 60},
                short_name : {defaulValue : '' , allowEmpty : false, maxLength : 30},
                family_id : {defaulValue : '' , allowEmpty : false},
                subfamily_id : {defaulValue : '' , required : ['family_id']},
                budget_customer_id : {defaulValue : ''},
                accessible_profit : {defaulValue : ''},
                project_manager_id : {defaulValue : ''},
                linked_profit : {defaulValue : ''},
                start_date : {defaulValue : ''},
                end_date : {defaulValue : ''},
                pms : {defaulValue : ''},
                actif : {defaulValue : ''},
                activated : {defaulValue : ''},
                code1 : {defaulValue : ''},
                code2 : {defaulValue : ''},
                code3 : {defaulValue : ''},
                import_code : {defaulValue : ''},
                c44: {defaulValue : ''},
                backupPM: {defaulValue : ''},
                project: {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';

            ControlGrid = $this.init($('#project_container'),data,columns);
            //var Columns = ControlGrid.getColumns();
//            var maxWidth = ControlGrid.getDataItem(1) ? ControlGrid.getDataItem(1).name.length : 1;
//            var i,j;
//            for (i =1 ; i < ControlGrid.getDataLength() ; i++){
//               var k = ControlGrid.getDataItem(i) ?ControlGrid.getDataItem(i).name.length : 1;
//               if(k > maxWidth)
//                    maxWidth = k;
//            }
//            if (Columns && columns.length > 0) {
//                Columns[1].width = maxWidth * 8;
//                ControlGrid.applyColumnHeaderWidths();
//                ControlGrid.applyColumnWidths();
//            }
			
            $('#dialog_import_CSV, #dialog_import_CSV_activity_task').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 360,
                height      : 125
            });
            $("#import_CSV").click(function(){
                $('.wd-input').show();
                $('#loading').hide();
                $("input[name='FileField[csv_file_attachment]']").val("");
                $(".error-message").remove();
                $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
                $(".type_buttons").show();
                $('#dialog_import_CSV').dialog("open");
            
            });
            $("#import_CSV_Activity_Task").click(function(){
                $('.wd-input').show();
                $('#loading').hide();
                $("input[name='FileFieldTask[csv_file_attachment]']").val("");
                $(".error-message").remove();
                $("input[name='FileFieldTask[csv_file_attachment]']").removeClass("form-error");
                $(".type_buttons").show();
                $('#dialog_import_CSV_activity_task').dialog("open");
            
            });
            $("#import-submit").click(function(){
                $(".error-message").remove();
                $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
                if($("input[name='FileField[csv_file_attachment]']").val()){
                    var filename = $("input[name='FileField[csv_file_attachment]']").val();
                    var valid_extensions = /(\.csv)$/i;   
                    if(valid_extensions.test(filename)){ 
                        $('#uploadForm').submit();
                    }
                    else{
                        $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                        jQuery('<div>', {
                            'class': 'error-message',
                            text: 'Incorrect type file'
                        }).appendTo('#error');
                    }
                }else{
                    jQuery('<div>', {
                        'class': 'error-message',
                        text: 'Please choose a file!'
                    }).appendTo('#error');
                }
            });
            
            $("#import-submit-task").click(function(){
                $(".error-message").remove();
                $("input[name='FileFieldTask[csv_file_attachment]']").removeClass("form-error");
                if($("input[name='FileFieldTask[csv_file_attachment]']").val()){
                    var filename = $("input[name='FileFieldTask[csv_file_attachment]']").val();
                    var valid_extensions = /(\.csv)$/i;   
                    if(valid_extensions.test(filename)){ 
                        $('#uploadFormTask').submit();
                    }
                    else{
                        $("input[name='FileFieldTask[csv_file_attachment]']").addClass("form-error");
                        jQuery('<div>', {
                            'class': 'error-message',
                            text: 'Incorrect type file'
                        }).appendTo('#errorTask');
                    }
                    $("#dialog_import_CSV_activity_task").dialog("close");
                }else{
                    jQuery('<div>', {
                        'class': 'error-message',
                        text: 'Please choose a file!'
                    }).appendTo('#errorTask');
                }
            });
             
            $(".cancel").live('click',function(){
                $("#dialog_data_CSV").dialog("close");
                $("#dialog_import_CSV").dialog("close");
                $("#dialog_import_CSV_activity_task").dialog("close");
            });
            var company_id = <?php echo json_encode($company_id);?>;
            $('#FilterStatusActivity').change(function(){
                $('#FilterStatusActivity option').each(function(){
                    if($(this).is(':selected')){
                        var id = $('#FilterStatusActivity').val();
                        listPersonalizeView(id);
                    }
                });
            });
			function listPersonalizeView(status){
                $.ajax({
                    url: '/activities/getPersonalizedViews/' + status,
                    async: false,  
                    beforeSend: function(){
                        //$('#FilterStatusActivityPersonalize').html('Please waiting...');
                    },
                    success:function(datas) {
                        var datas = JSON.parse(datas);
						$('#FilterStatusActivityPersonalize').find("option").remove();
                        $('#FilterStatusActivityPersonalize').html(datas);
                    }
                });
            }
			$('#FilterStatusActivityPersonalize').change(function(){
                $('#FilterStatusActivityPersonalize option').each(function(){
					var status = $('#FilterStatusActivity').val();
                    if($(this).is(':selected')){
						var view = $('#FilterStatusActivityPersonalize').val();
                        window.location = ('/activities/manage/'+company_id+'?activities_activated=' +status+'&view=' +view);
                    }
                });
            });
			//END
        });
        
    })(jQuery);
</script>