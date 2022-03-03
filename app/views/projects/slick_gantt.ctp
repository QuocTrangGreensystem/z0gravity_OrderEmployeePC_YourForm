<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->script('jquery.scrollTo'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css(array(
	'slick_grid/slick.grid.activity',
	'jquery.multiSelect',
	'projects',
	'slick_grid/slick.grid_v2',
	'slick_grid/slick.pager',
	'slick_grid/slick.common_v2',
	'slick_grid/slick.edit'
)); ?>
<?php
$typeGantt = isset($confirmGantt['type']) ? $confirmGantt['type'] : 0;
$typeStatus = array('month', 'date', 'week', 'year');
$typeGantt = $typeStatus[$typeGantt];
if($viewGantt):
	//echo $html->css('gantt_v2_1_ajax');
	echo $html->css('gantt_v2_1');
	// echo $html->script(array('html2canvas', 'jquery.html2canvasClone'));
	echo $html->script(array('jquery.easing.1.3', 'jquery.ui.touch-punch.min'));
endif;
?>
<script type="text/javascript">
	HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
	HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	function random(min, max) {
		return (Math.random() * (maximum - minimum + 1) ) << 0;
	}
	var coord = /^\s*(\-?[0-9]+\.[0-9]+)\s*,\s*(\-?[0-9]+\.[0-9]+)\s*$/;
	var gapi = ['AIzaSyA0KkqSSeKk3wOgkDZQcf9Vy_SiNOqFluc'];
</script>
<style>
	.slick-headerrow-columns.custom-headerrow {
		height: 30px;
	}
	.custom-headerrow p {
		line-height: 30px;
		text-align: right;
	}
	.hoverCell:hover{ color:#013D74; font-weight:bold;}
    .buttons .multiSelect {width: 323px !important;}
    .buttons .multiSelect span{width: 317px !important;}
    /* New Filter */
    .wd-customs{
        border: solid 1px #c0c0c0;
        float: right;
        margin-top: 2px;
    }
    .row-number{
        float: right;
    } 
    #wd-header-custom{
        height: 30px; 
        border: 1px solid #E0E0E0; 
        border-bottom: none;
        border-right: none !important;
    }
    .wd-row-custom{
        margin-right: -7px;
        text-align: right;
    }
    .wd-row-custom p{
        padding-top: 5px;
        font-weight: bold;
    }
    .wd-custom-cell{
        background: none !important;
        border-right: none !important;
        text-align: right;
    }
</style>
<!--[if lt IE 8]>
	<style type='text/css'>
		#wd-table{
			height: 400px;
		}
	</style>
<![endif]-->

<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$employeeInfo = $this->Session->read('Auth.employee_info');
if ($employeeInfo["Employee"]['is_sas'] == 0 && $employeeInfo["Role"]["name"] == "conslt") {
	echo '<style type="text/css">.wd-bt-big,.wd-add-project{display:none !important;}</style>';
}

$words = $this->requestAction('/translations/getByPage', array('pass' => array('KPI')));

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
		'noFilter' => 1
	));
$columnAlignRight = array(
	'ProjectBudgetSyn.total_costs_var', 'ProjectBudgetSyn.internal_costs_var', 'ProjectBudgetSyn.external_costs_var', 
	'ProjectBudgetSyn.external_costs_progress', 'ProjectBudgetSyn.assign_to_profit_center', 'ProjectBudgetSyn.assign_to_employee',
	'ProjectAmr.budget', 'ProjectAmr.project_amr_cost_control_id',
	'ProjectAmr.project_amr_organization_id', 'ProjectAmr.project_amr_plan_id', 'ProjectAmr.project_amr_perimeter_id',
	'ProjectAmr.project_amr_risk_control_id', 'ProjectAmr.project_amr_problem_control_id',
	'Project.category', 'Project.created_value', 'Project.budget'
);
$columnAlignRight = array_merge($columnAlignRight,$financeFields);
$columnAlignRightAndEuro = array(
	'ProjectBudgetSyn.sales_sold', 'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid', 
	'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast', 'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain', 
	'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
	'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.external_costs_budget', 'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered',
	'ProjectBudgetSyn.external_costs_remain', 'ProjectBudgetSyn.external_costs_progress_euro', 'ProjectBudgetSyn.internal_costs_average',
	'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
	'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget'
);
$columnAlignRightAndManDay = array(
	'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
	'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_man_day', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_man_day', 'ProjectAmr.delay',
	'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload',
	'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y', 
	'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y', 
	'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y',
	'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
	'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y'
);
$columnAlignRightAndPercent = array('ProjectBudgetSyn.roi');
$columnCalculationConsumeds = array(
	'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
	'ProjectAmr.engaged','ProjectAmr.variance','ProjectAmr.forecasted','ProjectBudgetSyn.sales_sold',
	'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid', 
	'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast',
	'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain', 'ProjectBudgetSyn.total_costs_man_day',
	'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
	'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_budget',
	'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered', 'ProjectBudgetSyn.external_costs_remain',
	'ProjectBudgetSyn.external_costs_man_day', 'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.internal_costs_average',
	'ProjectAmr.delay', 'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
	'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget', 'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y',
	'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y', 
	'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y', 
	'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload',
	'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
	'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y'
	//'ProjectAmr.validated', 'ProjectAmr.budget', 'Project.budget'
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
	'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3),
	'provisional_y' => __('Budget Provisional', true) . ' ' . date('Y', time()),
	'provisional_last_one_y' => __('Budget Provisional', true) . ' ' . (date('Y', time()) - 1),
	'provisional_last_two_y' => __('Budget Provisional', true) . ' ' . (date('Y', time()) - 2),
	'provisional_last_thr_y' => __('Budget Provisional', true) . ' ' . (date('Y', time()) - 3),
	'provisional_next_one_y' => __('Budget Provisional', true) . ' ' . (date('Y', time()) + 1),
	'provisional_next_two_y' => __('Budget Provisional', true) . ' ' . (date('Y', time()) + 2),
	'provisional_next_thr_y' => __('Budget Provisional', true) . ' ' . (date('Y', time()) + 3)
);

foreach ($fieldset as $_fieldset) {
	if (!empty($noProjectManager) && $_fieldset['key'] == 'Project.project_manager_id') {
		continue;
	}
	$financeFieldsKey = array_keys($financeFields);
	if(in_array($_fieldset['key'],$financeFieldsKey)){
		$fieldName = __($financeFields[$_fieldset['key']], true);
	} else if( strpos($_fieldset['key'], 'Project.') !== false ){
		$fieldName = substr($_fieldset['name'], 0, 1) == '*' ? __(substr($_fieldset['name'], 1), true) : __d(sprintf($_domain, 'Details'), $_fieldset['name'], true);
	} else if( strpos($_fieldset['key'], 'ProjectAmr.') !== false && in_array($_fieldset['name'], $words) ){
		$fieldName = __d(sprintf($_domain, 'KPI'), $_fieldset['name'], true);
	} else if( $_fieldset['key'] == 'ProjectAmr.manual_consumed' ){
		$fieldName = __d(sprintf($_domain, 'Project_Task'), $_fieldset['name'], true);
	} else {
		$fieldName = __($_fieldset['name'], true);
		//sales, in/ex
		$ff = explode('.', $_fieldset['key']);
		if( substr($ff[1], 0, 5) == 'sales' ){
			$fieldName = __d(sprintf($_domain, 'Sales'), $_fieldset['name'], true);
		} else if ( substr($ff[1], 0, 8) == 'internal' ) {
			$fieldName = __d(sprintf($_domain, 'Internal_Cost'), $_fieldset['name'], true);
		} else if ( substr($ff[1], 0, 8) == 'external' ) {
			$fieldName = __d(sprintf($_domain, 'External_Cost'), $_fieldset['name'], true);
		}
	}
	$_fieldListKey = trim(str_replace('ProjectBudgetSyn.', '', $_fieldset['key']));
	$_column = array(
		'id' => $_fieldset['key'],
		'field' => $_fieldset['key'],
		'name' => !empty($listDatas) && !empty($listDatas[$_fieldListKey]) ? $listDatas[$_fieldListKey] : $fieldName,
		'width' => 150,
		'sortable' => true,
		'resizable' => true
	);
	switch ($_fieldset['key']) {
		case 'Project.project_name':
				$_column['formatter'] = 'linkFormatter';
				$_column['width'] = 150;
			break;
		case 'Project.project_amr_program_id':
				$_column['width'] = 150;
			break;
		case 'Project.freeze_time':
		case 'Project.start_date':
		case 'Project.planed_end_date':
		case 'Project.end_date':
		// case 'Project.last_modified':
		case 'ProjectAmr.created':
		case 'Project.created':
		// case 'ProjectAmr.updated':
				$_column['datatype'] = 'datetime';
			break;
		case 'ProjectAmr.weather':
		case 'ProjectAmr.rank':
		case 'ProjectAmr.cost_control_weather':
		case 'ProjectAmr.planning_weather':
		case 'ProjectAmr.risk_control_weather':
		case 'ProjectAmr.organization_weather':
		case 'ProjectAmr.perimeter_weather':
		case 'ProjectAmr.issue_control_weather':
		case 'ProjectAmr.customer_point_of_view':
				$_column['width'] = 80;
				$_column['formatter'] = 'Slick.Formatters.ImageData';
			break;
		case 'Project.project_amr_progression':
		case 'Project.md_forecasted':
		case 'Project.md_validated':
		case 'Project.md_engaged':
		case 'Project.md_variance':
		case 'ProjectAmr.project_amr_progression':
		case 'ProjectAmr.md_validated':
		case 'ProjectAmr.md_engaged':
		case 'ProjectAmr.md_variance':
		case 'ProjectAmr.md_forecasted':
		case 'ProjectAmr.validated':
		case 'ProjectAmr.engaged':
		case 'ProjectAmr.forecasted':
		case 'ProjectAmr.variance':
		case 'ProjectAmr.manual_consumed':
			$_column['formatter'] = 'floatFormatter';
			break;
		case 'Project.is_freeze':
		case 'Project.is_staffing':
		case 'Project.off_freeze':
		case 'Project.project_copy':
			$_column['formatter'] = 'yesNoFormatter';
			break;
	}
	if(in_array($_fieldset['key'], $columnAlignRight)){
		$_column['formatter'] = 'numberVal';
	}
	if(in_array($_fieldset['key'], $columnAlignRightAndEuro)){
		$_column['formatter'] = 'numberValEuro';
	}
	if(in_array($_fieldset['key'], $columnAlignRightAndManDay)){
		$_column['formatter'] = 'numberValManDay';
	}
	if(in_array($_fieldset['key'], $columnAlignRightAndPercent)){
		$_column['formatter'] = 'numberValPercent';
	}
	$columns[] = $_column;
}
if( !($isMobileOnly || $isTablet) || (($isMobileOnly || $isTablet) && !$viewGantt) ){
	$columns[] = array(
		'id' => 'action.',
		'field' => 'action.',
		'name' => __('Action', true),
		'width' => 70,
		'sortable' => false,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.HTMLData',
		'noFilter' => 1
	);
}
$i = 1;
$dataView = array();
$selects = array();
$projectManagers = array();
$eindex = 0;
$totalHeaders = array();
$notAdmin = $employee['Role']['name'] == 'pm' || $employee['Role']['name'] == 'conslt';

$exception = array('Project.complexity_id');
$gantts = $stones = array();
$ganttStart = $ganttEnd = 0;
foreach ($projects as $project) {
	$data = array(
		'id' => $project['Project']['id'],
		'no.' => $i++,
		'DataSet' => array()
	);
	$projectID = $project['Project']['id'];
	$activityID = $project['Project']['activity_id'];
	$category = $project['Project']['category'];
	$workload = !empty($sumWorload[$projectID]) ? $sumWorload[$projectID] : 0;
	$previous = !empty($sumPrevious[$projectID]) ? $sumPrevious[$projectID] : 0;
	$overload = !empty($sumOverload[$projectID]) ? $sumOverload[$projectID]: 0;
	$consumed = !empty($activityID) && !empty($sumActivities[$activityID]) ? $sumActivities[$activityID] : 0;
	$remainSPCs = isset($sumRemainSpecials[$projectID]) ? $sumRemainSpecials[$projectID] : 0;
	$remains = isset($sumRemains[$projectID]) ? $sumRemains[$projectID] : 0;
	$remains = $remains - $remainSPCs;
	$workload = $workload + $previous;
	$progress = 0;
	if(($workload + $overload) == 0){
		$progress = 0;
	} else {
		$com = round(($consumed*100)/($workload + $overload), 2);
		if($com > 100){
			$progress = 100;
		} else {
			$progress = $com;
		}
	}
	$mdForecasted = !empty($projectEvolutions[$projectID]) ? $projectEvolutions[$projectID] : 0;
	if(!empty($project['ProjectAmr'])){
		$project['ProjectAmr'][0]['project_amr_progression'] = $progress;
		$project['ProjectAmr'][0]['md_validated'] = $consumed;
		$project['ProjectAmr'][0]['md_engaged'] = $remains; 
		$project['ProjectAmr'][0]['md_forecasted'] = $mdForecasted;
		$project['ProjectAmr'][0]['md_variance'] = round($remains + $consumed - $mdForecasted, 2);
		$project['ProjectAmr'][0]['validated_currency_id'] = !empty($project['ProjectAmr'][0]['validated_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['validated_currency_id']] : '';
		$project['ProjectAmr'][0]['engaged_currency_id'] = !empty($project['ProjectAmr'][0]['engaged_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['engaged_currency_id']] : '';
		$project['ProjectAmr'][0]['forecasted_currency_id'] = !empty($project['ProjectAmr'][0]['forecasted_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['forecasted_currency_id']] : '';
		$project['ProjectAmr'][0]['variance_currency_id'] = !empty($project['ProjectAmr'][0]['variance_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['variance_currency_id']] : '';
		$phaseDelays = !empty($projectPhasePlans[$projectID]) ? $projectPhasePlans[$projectID] : array();
		$phasePlans = !empty($phaseDelays['MaxEndDatePlan']) && $phaseDelays['MaxEndDatePlan'] != '0000-00-00' ? strtotime($phaseDelays['MaxEndDatePlan']) : 0;
		$phaseReals = !empty($phaseDelays['MaxEndDateReal']) && $phaseDelays['MaxEndDateReal'] != '0000-00-00' ? strtotime($phaseDelays['MaxEndDateReal']) : 0;
		$project['ProjectAmr'][0]['delay'] = intval(($phaseReals - $phasePlans)/86400);
		$project['ProjectAmr'][0]['manual_consumed'] = isset($manualData[$projectID]) ? $manualData[$projectID] : 0;
	}
	$engagedErro = 0;
	if(!empty($activityID) && !empty($sumEmployees[$activityID])) {
		foreach ($sumEmployees[$activityID] as $id => $val) {
			$reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
			$engagedErro += $val * $reals;
		}
	}
	$average = !empty($budgetSyns[$projectID]['internal_costs_average']) ? $budgetSyns[$projectID]['internal_costs_average'] : 0;
	$internalBudget = !empty($budgetSyns[$projectID]['internal_costs_budget']) ? $budgetSyns[$projectID]['internal_costs_budget'] : 0;
	$internalForecastManday = $consumed + $remains;
	$internalRemain = round($remains*$average, 2);
	$internalForecast = round($engagedErro + ($remains*$average), 2);
	$internalCostsVar = ($internalBudget == 0) ? '-100%' : round(((($engagedErro + ($remains * $average))/$internalBudget)-1)*100, 2) . '%';
	$externalBudget = !empty($budgetSyns[$projectID]['external_costs_budget']) ? $budgetSyns[$projectID]['external_costs_budget'] : 0;
	$externalForecast = !empty($budgetSyns[$projectID]['external_costs_forecast']) ? $budgetSyns[$projectID]['external_costs_forecast'] : 0;
	$externalOrdered = !empty($budgetSyns[$projectID]['external_costs_ordered']) ? $budgetSyns[$projectID]['external_costs_ordered'] : 0;
	$externalRemain = !empty($budgetSyns[$projectID]['external_costs_remain']) ? $budgetSyns[$projectID]['external_costs_remain'] : 0;
	$externalManday = !empty($budgetSyns[$projectID]['external_costs_man_day']) ? $budgetSyns[$projectID]['external_costs_man_day'] : 0;
	if($category == 2){
		$project['ProjectBudgetSyn'][0]['sales_to_bill'] = 0;
		$project['ProjectBudgetSyn'][0]['sales_billed'] = 0;
		$project['ProjectBudgetSyn'][0]['sales_paid'] = 0;
		$project['ProjectBudgetSyn'][0]['external_costs_forecast'] = 0;
		$project['ProjectBudgetSyn'][0]['external_costs_ordered'] = 0;
		$project['ProjectBudgetSyn'][0]['external_costs_remain'] = 0;
		$project['ProjectBudgetSyn'][0]['external_costs_var'] = '0%';
		$internalForecastManday = 0;
		$internalRemain = 0;
		$internalForecast = 0;
		$engagedErro = 0;
		$consumed = 0;
		$remain = 0;
		$externalForecast = 0;
		$externalOrdered = 0;
		$externalRemain = 0;
		
	}
	
	$project['ProjectBudgetSyn'][0]['workload'] = $workload;
	$project['ProjectBudgetSyn'][0]['overload'] = $overload;
	
	$project['ProjectBudgetSyn'][0]['internal_costs_engaged'] = $engagedErro;
	$project['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] = $consumed + $remains;
	$project['ProjectBudgetSyn'][0]['internal_costs_remain'] = round($remains*$average, 2);
	$project['ProjectBudgetSyn'][0]['internal_costs_forecast'] = round($engagedErro + ($remains*$average), 2);
	$project['ProjectBudgetSyn'][0]['internal_costs_var'] = ($category == 2) ? '0%' : $internalCostsVar;
	$project['ProjectBudgetSyn'][0]['total_costs_budget'] = $internalBudget + $externalBudget;
	$project['ProjectBudgetSyn'][0]['total_costs_forecast'] = $internalForecast + $externalForecast;
	$project['ProjectBudgetSyn'][0]['total_costs_engaged'] = $engagedErro + $externalOrdered;
	$project['ProjectBudgetSyn'][0]['total_costs_remain'] = $internalRemain + $externalRemain;
	$project['ProjectBudgetSyn'][0]['total_costs_man_day'] = $internalForecastManday + $externalManday;
	$totalForecast = !empty($project['ProjectBudgetSyn'][0]['total_costs_forecast']) ? $project['ProjectBudgetSyn'][0]['total_costs_forecast'] : 0;
	$totalBudget = !empty($project['ProjectBudgetSyn'][0]['total_costs_budget']) ? $project['ProjectBudgetSyn'][0]['total_costs_budget'] : 0;
	$totalVar = ($totalBudget == 0) ? '-100%' : round((($totalForecast/$totalBudget) - 1)*100, 2).'%';
	$project['ProjectBudgetSyn'][0]['total_costs_var'] = ($category == 2) ? '0%' : $totalVar;
	$tWorkload = $workload + $overload;
	$assgnPc = !empty($staffingSystems['profit_center']) && !empty($staffingSystems['profit_center'][$projectID]) ? $staffingSystems['profit_center'][$projectID] : 0;
	$assgnEmploy = !empty($staffingSystems['employee']) && !empty($staffingSystems['employee'][$projectID]) ? $staffingSystems['employee'][$projectID] : 0;
	$project['ProjectBudgetSyn'][0]['assign_to_profit_center'] = ($tWorkload == 0) ? '0%' : ((round(($assgnPc/$tWorkload)*100, 2) > 100)? '100%' : round(($assgnPc/$tWorkload)*100, 2).'%');
	$project['ProjectBudgetSyn'][0]['assign_to_employee'] = ($tWorkload == 0) ? '0%' : ((round(($assgnEmploy/$tWorkload)*100, 2) > 100)? '100%' : round(($assgnEmploy/$tWorkload)*100, 2).'%');
	
	$totalExter = !empty($budgetSyns[$projectID]['external_costs_budget']) ? $budgetSyns[$projectID]['external_costs_budget'] : 0;
	$totalInter = !empty($budgetSyns[$projectID]['internal_costs_budget_man_day']) ? $budgetSyns[$projectID]['internal_costs_budget_man_day'] : 0;
	$sold = !empty($budgetSyns[$projectID]['sales_sold']) ? $budgetSyns[$projectID]['sales_sold'] : 0;
	$totalIE = $totalExter + $totalInter;
	$totalB = $externalBudget+$internalBudget;
	$project['ProjectBudgetSyn'][0]['roi'] = ($totalB != 0) ? round((($sold-$totalB)/$totalB)*100, 2) : 0;

	//Added by QN on 2015/01/24
	$project['Project']['project_copy_id'] = isset($project['Project']['project_copy_id']) && isset($projectCopies[$project['Project']['project_copy_id']]) ? $projectCopies[ $project['Project']['project_copy_id'] ] : '';
	$project['Project']['freeze_by'] = isset($project['Project']['freeze_by']) && isset($freezers[$project['Project']['freeze_by']]) ? $freezers[ $project['Project']['freeze_by'] ] : '';
	
	//Added by QN on 2015/02/05
	if(isset($project['Project']['updated']))$project['Project']['updated'] = $project['Project']['last_modified'] ? $project['Project']['last_modified'] : $project['Project']['updated'];

	$project['ProjectBudgetSyn'][0]['workload_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.$currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.$currentYears]: 0;
	$project['ProjectBudgetSyn'][0]['workload_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-1)]: 0;
	$project['ProjectBudgetSyn'][0]['workload_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-2)]: 0;
	$project['ProjectBudgetSyn'][0]['workload_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-3)]: 0;
	$project['ProjectBudgetSyn'][0]['workload_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+1)]: 0;
	$project['ProjectBudgetSyn'][0]['workload_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+2)]: 0;
	$project['ProjectBudgetSyn'][0]['workload_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+3)]: 0;
	
	$project['ProjectBudgetSyn'][0]['consumed_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.$currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.$currentYears]: 0;
	$project['ProjectBudgetSyn'][0]['consumed_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-1)]: 0;
	$project['ProjectBudgetSyn'][0]['consumed_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-2)]: 0;
	$project['ProjectBudgetSyn'][0]['consumed_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-3)]: 0;
	$project['ProjectBudgetSyn'][0]['consumed_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+1)]: 0;
	$project['ProjectBudgetSyn'][0]['consumed_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+2)]: 0;
	$project['ProjectBudgetSyn'][0]['consumed_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+3)]: 0;
	
	$project['ProjectBudgetSyn'][0]['provisional_y'] = !empty($provisionals[$projectID]['provisional_'.$currentYears]) ? $provisionals[$projectID]['provisional_'.$currentYears]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_last_one_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears-1)]) ? $provisionals[$projectID]['provisional_'.($currentYears-1)]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_last_two_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears-2)]) ? $provisionals[$projectID]['provisional_'.($currentYears-2)]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_last_thr_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears-3)]) ? $provisionals[$projectID]['provisional_'.($currentYears-3)]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_next_one_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears+1)]) ? $provisionals[$projectID]['provisional_'.($currentYears+1)]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_next_two_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears+2)]) ? $provisionals[$projectID]['provisional_'.($currentYears+2)]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_next_thr_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears+3)]) ? $provisionals[$projectID]['provisional_'.($currentYears+3)]: 0;
	$project['ProjectBudgetSyn'][0]['provisional_budget_md'] = $totalInter + $externalManday;
	
	foreach ($fieldset as $_fieldset) {
		if (is_array($_fieldset['path'])) {
			$_output = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
		} else {
			$_output = (string) Set::classicExtract($project, $_fieldset['path']);
		}
		switch ($_fieldset['key']) {
			case 'Project.project_name': {
					$_output = $project["Project"]["project_name"];
					break;
				}
			case 'Project.freeze_time':
			case 'ProjectAmr.created':
			case 'Project.created':
				if( !$_output )$_output = '';
					else $_output = date('d-m-Y', $_output);
				break;
			case 'Project.last_modified':
			case 'ProjectAmr.updated':
					if( !$_output )$_output = '';
					else $_output = date('Y-m-d H:i:s', $_output);
				break;
			case 'Project.start_date':
			case 'Project.planed_end_date':
			case 'Project.end_date': {
					$_output = $str_utility->convertToVNDate($_output);
					break;
				}
			case 'ProjectAmr.rank':
			case 'ProjectAmr.weather':
			case 'ProjectAmr.cost_control_weather':
			case 'ProjectAmr.planning_weather':
			case 'ProjectAmr.risk_control_weather':
			case 'ProjectAmr.organization_weather':
			case 'ProjectAmr.perimeter_weather':
			case 'ProjectAmr.issue_control_weather':
			case 'ProjectAmr.customer_point_of_view': {
					if( $_output )
						$_output = $this->Html->url('/img/' . $_output . '.png');
					break;
				}
			case 'Project.project_amr_sub_program_id':
					$data['DataSet'][$_fieldset['key']] = $project['ProjectAmrSubProgram']['id'];
					$selects[$_fieldset['key']][$project['ProjectAmrSubProgram']['id']] = $_output;
					break;
			case 'Project.project_amr_program_id' : {
					$data['DataSet'][$_fieldset['key']] = $project['ProjectAmrProgram']['id'];
					$selects[$_fieldset['key']][$project['ProjectAmrProgram']['id']] = $_output;
					break;
				}
			case 'Project.project_manager_id' : {
					$data['DataSet'][$_fieldset['key']] = $project['Employee']['id'];
					$selects[$_fieldset['key']][$project['Employee']['id']] = $_output;
					break;
				}
			case 'Project.project_type_id' : {
					$data['DataSet'][$_fieldset['key']] = $project['ProjectType']['id'];
					$selects[$_fieldset['key']][$project['ProjectType']['id']] = $_output;
					break;
				}
			case 'Project.project_phase_id' : {
					$data['DataSet'][$_fieldset['key']] = $project['ProjectPhase']['id'];
					$selects[$_fieldset['key']][$project['ProjectPhase']['id']] = $_output;
					break;
				}
			case 'Project.project_priority_id' : {
					$data['DataSet'][$_fieldset['key']] = $project['ProjectPriority']['id'];
					$selects[$_fieldset['key']][$project['ProjectPriority']['id']] = $_output;
					break;
				}
			case 'Project.project_status_id' : {
					$data['DataSet'][$_fieldset['key']] = $project['ProjectStatus']['id'];
					$selects[$_fieldset['key']][$project['ProjectStatus']['id']] = $_output;
					break;
				}
			case 'Project.functional_leader_id':
			case 'Project.uat_manager_id':
					$managerId = $_output;
					$_output = isset($projectManagersOption[$managerId]) ? $projectManagersOption[$managerId] : '';
					$data['DataSet'][$_fieldset['key']] = $managerId;
					if( $_output )$selects[$_fieldset['key']][$managerId] = $_output;
				break;
			case 'ProjectAmr.project_amr_risk_information' : {
					if(empty($_output)){
						if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectRisk'])){
							$_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectRisk'], '{n}.description');
							$_output = implode(', ', $_output);
						}
					}
					break;
				}
			case 'ProjectAmr.project_amr_problem_information' : {
					if(empty($_output)){
						if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectIssue'])){
							$_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectIssue'], '{n}.description');
							$_output = implode(', ', $_output);
						}
					}
					break;
				}
			case 'ProjectAmr.project_amr_solution' : {
					if(empty($_output)){
						if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectAmr'])){
							$_output = !empty($logGroups[$project['Project']['id']]['ProjectAmr']['description']) ? $logGroups[$project['Project']['id']]['ProjectAmr']['description'] : '';
						}
					}
					break;
				}
			case 'ProjectAmr.done':
				if( isset($logs[ $project['Project']['id'] ]['Done']) ){
					$_output = $logs[ $project['Project']['id'] ]['Done']['description'];
				}
			break;
			case 'ProjectAmr.todo':
				if( isset($logs[ $project['Project']['id'] ]['ToDo']) ){
					$_output = $logs[ $project['Project']['id'] ]['ToDo']['description'];
				}
			break;
		}
		if( !in_array($_fieldset['key'], $exception) ){
			if (is_numeric($_output)) {
				if (strpos($_output, '.')) {
					$_output = floatval($_output);
				} else {
					$_output = (int)$_output;
				}
			}elseif (preg_match("/^(-){0,5}( ){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/i", (string)$_output) == 1) {
				$_output = str_replace(' ', '', $_output);
				$_output = str_replace(',', '', $_output);
				$_output = floatval($_output);
			}
		}
		if(in_array($_fieldset['key'], $columnCalculationConsumeds)){
			$val = $_output ? $_output : 0;
			if(!isset($totalHeaders[$_fieldset['key']])){
				$totalHeaders[$_fieldset['key']] = 0;
			}
			$totalHeaders[$_fieldset['key']] += $val;
		}
		$data[$_fieldset['key']] = $_output;
	}
	$data['action.'] = '<div style="margin: 0 auto !important; width: 54px;">' . $this->Html->link(__('Edit', true), 
		$html->url('/' . $ACLController . '/' . $ACLAction . '/' . $project['Project']['id'])
		, array('class' => 'wd-edit'));
	if (!$notAdmin || $project['Employee']['id'] == $employee['Employee']['id']) {
		$data['action.'] .= '<div class="wd-bt-big">' . $this->Html->link(__('Delete', true), array(
					'action' => 'delete', $project['Project']['id']), array(
					'class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $project['Project']['project_name'])) . '</div>';
	}
	$data['action.'].= '</div>';
	/**
	 * 
	 */
	if($viewGantt){
		/**
		 * Add milestones
		 */
		if(!empty($project['ProjectMilestone'])){
			foreach ($project['ProjectMilestone'] as $p) {
				$_start = strtotime($p['milestone_date']);
				if (!$ganttStart || $_start < $ganttStart) {
					$ganttStart = $_start;
				} elseif (!$ganttEnd || $_start > $ganttEnd) {
					$ganttEnd = $_start;
				}
				$stones[$project['Project']['id']][] = array($_start, $p['project_milestone'], $p['validated']);
			}
		}
		$_gantt = array(
			'id' => !empty($project['Project']['id']) ? $project['Project']['id'] : '',
			'program' => !empty($project['ProjectAmrProgram']['amr_program']) ? $project['ProjectAmrProgram']['amr_program'] : '',
			'category' => !empty($project['Project']['category']) ? $project['Project']['category'] : '',
			'name' => !empty($project['Project']['project_name']) ? $project['Project']['project_name'] : '',
			'project_id' => !empty($project['Project']['id']) ? $project['Project']['id'] : 0,
			'phase' => array(),
		);
		if (!empty($project['ProjectPhasePlan'])) {
			$_phase['start'] = $_phase['end'] = $_phase['rstart'] = $_phase['rend'] = 0;
			foreach ($project['ProjectPhasePlan'] as $phace) {
				/**
				 * Set start, end, real start, real end.
				 */
				if(isset($_phase['start']) && !empty($_phase['start']) && $_phase['start'] != 0){
					$date = $this->Gantt->toTime($phace['phase_planed_start_date']);
					if($date <= $_phase['start']){
						$_phase['start'] = $date;
					}
				} else {
					$_phase['start'] = $this->Gantt->toTime($phace['phase_planed_start_date']);
				}
				if(isset($_phase['end']) && !empty($_phase['end']) && $_phase['end'] != 0){
					$date = $this->Gantt->toTime($phace['phase_planed_end_date']);
					if($date >= $_phase['end']){
						$_phase['end'] = $date;
					}
				} else {
					$_phase['end'] = $this->Gantt->toTime($phace['phase_planed_end_date']);
				}
				if(isset($_phase['rstart']) && !empty($_phase['rstart']) && $_phase['rstart'] != 0){
					$date = $this->Gantt->toTime($phace['phase_real_start_date']);
					if($date <= $_phase['rstart']){
						$_phase['rstart'] = $date;
					}
				} else {
					$_phase['rstart'] = $this->Gantt->toTime($phace['phase_real_start_date']);
				}
				if(isset($_phase['rend']) && !empty($_phase['rend']) && $_phase['rend'] != 0){
					$date = $this->Gantt->toTime($phace['phase_real_end_date']);
					if($date >= $_phase['rend']){
						$_phase['rend'] = $date;
					}
				} else {
					$_phase['rend'] = $this->Gantt->toTime($phace['phase_real_end_date']);
				}
				$_phase['id'] = $phace['id'];
				$_phase['name'] = !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '';
				$_phase['color'] = !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380';

				if ($_phase['rstart'] > 0) {
					$_start = min($_phase['start'], $_phase['rstart']);
				} else {
					$_start = $_phase['start'];
				}
				if (!$ganttStart || ($_start > 0 && $_start < $ganttStart)) {
					$ganttStart = $_start;
				}
				$_end = max($_phase['end'], $_phase['rend']);
				if (!$ganttEnd || $_end > $ganttEnd) {
					$ganttEnd = $_end;
				}
				$_gantt['phase'][0] = $_phase;
			}
		}
		$gantts[] = $_gantt; 
	}
	$dataView[] = $data;
}

foreach ($selects as &$select) {
	asort($select);
	$select = $this->Form->select('test', $select, null, array('empty' => __('--', true), 'secure' => false));
}
ksort($projectManagers);
$projectManagers = Set::combine(array_values($projectManagers), '{n}.0', '{n}.1');
?>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-title">
					<div class="check-box-stones" style="display:none">
						<?php
							echo $this->Form->input('milestones.check', array(
									'id' => 'MilestonesCheck',
									'style' => 'float:left;', 
									'type' => 'checkbox',
									'div' => false,
									'label' => false,
									'rel' => 'no-history'
							));
						?>
						<p style="float:left;font-weight:bold"><?php echo __("Display all name of milestones", true); ?></p>
					</div>
					
					<?php
						echo $this->Form->create('Category', array('style' => 'display: inline-block'));
						$href = '';
						$href = $this->params['url'];
						if(!empty($appstatus)){
							$op = ($appstatus == 1) ? 'selected="selected"' : '';
							$ar = ($appstatus == 3) ? 'selected="selected"' : '';
							$md = ($appstatus == 4) ? 'selected="selected"' : '';
							$io = ($appstatus == 5) ? 'selected="selected"' : '';
						}
					?>
						<select style="margin-right:5px; width: auto !important; padding: 6px; float: none" class="wd-customs" id="CategoryStatus">
							<option value="0"><?php echo __("--Select--", true);?></option>
						</select>
						<select style="margin-right:5px; width: auto !important; padding: 6px; float: none" class="wd-customs" id="CategoryCategory">
							<option value="1" <?php echo isset($op) ? $op : '';?>><?php echo __("In progress", true)?></option>
							<option value="5" <?php echo isset($io) ? $io : '';?>><?php echo __("In progress + Opportunity", true)?></option>
							<option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo __("Archived", true)?></option>
							<option value="4" <?php echo isset($md) ? $md : '';?>><?php echo __("Model", true)?></option>
						</select>
					<?php
						echo $this->Form->end();
					?>
					<a href="<?php echo $this->Html->url('/projects/map/') ?>" id="map-icon" class="btn btn-globe"></a>
					<?php if( $isTablet ): ?>
						<a href="javascript:void(0);" id="add_project" class="btn-text btn-green">
							<img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
							<span><?php __('Add Project') ?></span>
						</a>
					<?php elseif( !$isMobileOnly ): ?>
						<?php echo $this->element('multiSortHtml'); ?>
						<a href="javascript:void(0);" id="add_vision_staffing_news" class="btn-text">
							<img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
							<span><?php __('Vision staffing+') ?></span>
						</a>
						<a href="javascript:void(0);" id="add_vision_portfolio" class="btn-text">
							<img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
							<span><?php __('Vision portfolio') ?></span>
						</a>
						<a href="javascript:void(0);" id="add_portfolio" class="btn-text">
							<img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
							<span><?php __('Portfolio') ?></span>
						</a>
						<a href="javascript:void(0);" class="btn btn-excel" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
						<a href="javascript:void(0);" id="add_project" class="btn-text">
							<img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
							<span><?php __('Add Project') ?></span>
						</a>
					<?php endif ?>
				</div>
				<?php
				echo $this->Session->flash();
				?>
				<div class="wd-table" id="project_container" style="width: 100%" rels="<?php echo ($viewGantt && !empty($confirmGantt) && $confirmGantt['stones']) ? 'yes' : 'no';?>">
				
				</div>
				<div id="pager" style="clear:both;width:100%;height:36px;"></div>
			</div>
		</div>
	</div>
</div>
<!-- ADD CODE BY VINGUYEN 05/06/2014 -->
<?php echo $this->element('dialog_detail_value') ?>
<!-- END -->
<?php echo $this->element('dialog_projects') ?>
<!-- export excel -->
<fieldset style="display: none;">
	<?php
	echo $this->Form->create('Export', array(
		'type' => 'POST',
		'url' => array('controller' => 'projects', 'action' => 'export_project', $viewId)));
	echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
	echo $this->Form->end();
	?>
</fieldset>
<!-- /export excel -->
<?php echo $html->script(array(
	'slick_grid/lib/jquery-ui-1.8.16.custom.min',
	'slick_grid/lib/jquery.event.drag-2.0.min',
	'slick_grid/slick.core',
	'slick_grid/slick.dataview',
	'slick_grid/controls/slick.pager',
	'slick_grid/slick.formatters',
	'slick_grid/slick.grid',
	'slick_grid_custom',
	'slick_grid/slick.grid.activity',
	'responsive_table.js'
)) ?>
<?php $viewManDay = __('M.D', true);?>
<!-- dialog_vision_staffing -->
<div id="dialog_vision_staffing" class="buttons" style="display: none;">
	<fieldset>
		<?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_staffing', 'url' => array('controller' => 'project_staffings', 'action' => 'project'))); ?>
		<div style="height:auto;" class="wd-scroll-form">
			<div class="wd-left-content">

				<fieldset class="fieldset">
					<legend class="legend"><?php __('Visibility settings'); ?></legend>
					<div class="wd-input">
						<label for="status"><?php __('Show Gantt chart'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_gantt_id', array(__("No", true), __("Yes", true)), array(
								'name' => 'gantt',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input" id="display-real-time" style="display: none;">
						<label for="status"><?php __('Display real time'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_display_id', array(__("No", true), __("Yes", true)), array(
								'name' => 'display',
								'fieldset' => false,
								'legend' => false,
								'disabled' => true,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input">
						<label for="status"><?php __('Show staffing by'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_staffing_id', array(5 => __("Project", true)), array(
								'name' => 'type',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
							<?php
							echo $this->Form->radio('project_staffing_id', array(__("Function", true), __("Profit center", true)), array(
								'name' => 'type',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
							<br/>
							<?php
							echo $this->Form->radio('project_staffing_id', array(2 => __("Profit center & Project", true)), array(
								'name' => 'type',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
							<br/>
							<?php
							echo $this->Form->radio('project_staffing_id', array(3 => __("Function & Project", true)), array(
								'name' => 'type',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input">
						<label for="status"><?php __('Show Summary'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_summary_id', array(__("No", true), __("Yes", true)), array(
								'name' => 'summary',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="wd-input">
				<label for="program"><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
				<?php
				echo $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false,
					"empty" => __("--", true),
					'name' => 'program',
					'multiple' => true,
					'hiddenField' => false,
					"options" => $project_arm_programs));
				?>
			</div>
			<div class="wd-input">
				<label for="program"><?php __("Sub Program") ?></label>
				<?php
				echo $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,
					'style' => 'width:69% !important',
					'name' => 'sub_program',
					'hiddenField' => false,
					'multiple' => false,
					'empty' => __('--', true),
					"class" => "wd-disable",
					"options" => array()));
				?>
			</div>
			<div class="wd-input">
				<label for="project-manager"><?php __d(sprintf($_domain, 'Details'),"Project Manager") ?></label>
				<?php
				echo $this->Form->input('project_manager_id', array(
					'type' => 'select',
					'name' => 'pm',
					'div' => false,
					'label' => false,
					'multiple' => false,
					'hiddenField' => false,
					"empty" => __("--", true),
					'style' => 'width:69% !important',
					"options" => $projectManagersOption));
				?>
			</div>
			<div class="wd-input">
				<label for="status"><?php __d(sprintf($_domain, 'Details'),"Status") ?></label>
				<?php
				echo $this->Form->input('project_status_id', array(
					'type' => 'select',
					'name' => 'status',
					'div' => false,
					'multiple' => false,
					'hiddenField' => false,
					'label' => false,
					'style' => 'width:69% !important',
					"empty" => __("--", true),
					"options" => $project_statuses));
				?>
			</div>
			<div class="wd-input">
				<label for="status"><?php __("Profit center") ?></label>
				<?php
				echo $this->Form->input('profit_center_id', array(
					'type' => 'select',
					'name' => 'profit',
					'div' => false,
					'multiple' => true,
					'label' => false,
					'hiddenField' => false,
					'style' => 'width:69% !important',
					"empty" => __("--", true),
					"options" => $profit_centers));
				?>
			</div>
			<div class="wd-input">
				<label for="status"><?php __("Function") ?></label>
				<?php
				echo $this->Form->input('project_function_id', array(
					'type' => 'select',
					'name' => 'func',
					'div' => false,
					'multiple' => true,
					'label' => false,
					'hiddenField' => false,
					'style' => 'width:69% !important',
					"empty" => __("--", true),
					"options" => $project_functions));
				?>
			</div>

		</div>
		<?php echo $this->Form->end(); ?>
	</fieldset>
	<div style="clear: both;"></div>
	<ul class="type_buttons" style="padding-right: 10px !important">
		<li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
		<li><a href="javascript:void(0)" class="new" id="ok"><?php __('OK') ?></a></li>
	</ul>
</div>
<!-- dialog_vision_staffing.end -->
<!-- dialog_vision_staffing++++++++++++++++++ -->
<div id="dialog_vision_staffing_news" class="buttons" style="display: none;">
	<fieldset>
		<?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_staffing_news', 'url' => array('controller' => 'project_staffings', 'action' => 'visions_staffing'))); ?>
		<div style="height:auto;" class="wd-scroll-form">
			<div class="wd-left-content">

				<fieldset class="fieldset">
					<legend class="legend"><?php __('Visibility settings'); ?></legend>
					<div class="wd-input">
						<label for="status"><?php __('Show Gantt chart'); ?></label>
						<div id="show_gantt">
							<?php
							echo $this->Form->radio('project_gantt_id_news', array(__("No", true), __("Yes", true)), array(
								'name' => 'gantt',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input" id="display-real-time-news" style="display: none;">
						<label for="status"><?php __('Display real time'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_display_id_news', array(__("No", true), __("Yes", true)), array(
								'name' => 'display',
								'fieldset' => false,
								'legend' => false,
								'disabled' => true,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input" id="show_by">
						<label for="status"><?php __('Show staffing by'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_staffing_id', array(5 => __("Project", true)), array(
								'name' => 'type',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
							<?php
							echo $this->Form->radio('project_staffing_id', array( 1 => __("Profit center", true)), array(
								'name' => 'type',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input" id="show_summary">
						<label for="status"><?php __('Show Summary'); ?></label>
						<div>
							<?php
							echo $this->Form->radio('project_summary_id', array(__("No", true), __("Yes", true)), array(
								'name' => 'summary',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
					<div class="wd-input">
						<label for="status"><?php __('File'); ?></label>
						<div class="is-check-file">
							<?php
							echo $this->Form->radio('project_file', array(__("No", true), __("Yes", true)), array(
								'name' => 'pr_file',
								'rel' => 'no-history',
								'fieldset' => false,
								'legend' => false,
								'value' => '0'));
							?>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="wd-input" id="filter_program">
				<label for="program"><?php __d(sprintf($_domain, 'Details'),"Program") ?></label>
				<?php
				echo $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false,
					"empty" => __("--", true),
					'name' => 'program',
					'id' => 'ProjectProjectAmrProgramId_sum',
					'multiple' => true,
					'hiddenField' => false,
					// 'style' => 'margin-right:11px; width:52% !important',
					"options" => $project_arm_programs));
				?>
			</div>
			<div class="wd-input" id="filter_sub_program">
				<label for="program"><?php __("Sub Program") ?></label>
				<?php
				echo $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,
					'style' => 'width:69% !important',
					'name' => 'sub_program',
					'id' => 'ProjectProjectAmrSubProgramId_sum',
					'hiddenField' => false,
					'multiple' => false,
					'empty' => __('--', true),
					"class" => "wd-disable",
					"options" => array()));
				?> 
			</div>
			<div class="wd-input" id="filter_manager">
				<label for="project-manager"><?php __d(sprintf($_domain, 'Details'),"Project Manager") ?></label>
				<?php
				echo $this->Form->input('project_manager_id', array(
					'type' => 'select',
					'name' => 'pm',
					'div' => false,
					'label' => false,
					'multiple' => false,
					'hiddenField' => false,
					"empty" => __("--", true),
					'style' => 'width:69% !important',
					"options" => $projectManagersOption));
				?>
			</div>
			<div class="wd-input" id="filter_status">
				<label for="status"><?php __d(sprintf($_domain, 'Details'), "Status") ?></label>
				<?php
				echo $this->Form->input('project_status_id', array(
					'type' => 'select',
					'name' => 'status',
					'div' => false,
					'multiple' => false,
					'hiddenField' => false,
					'label' => false,
					'style' => 'width:69% !important',
					"empty" => __("--", true),
					"options" => $project_statuses));
				?>
			</div>
			<div class="wd-input" id="filter_profitCenter" style="display:none">
				<label for="status"><?php __("Profit center") ?></label>
				<?php
				echo $this->Form->input('profit_center_id', array(
					'type' => 'select',
					'name' => 'profit',
					'div' => false,
					'multiple' => true,
					'label' => false,
					'hiddenField' => false,
					'style' => 'width:69% !important',
					"empty" => __("--", true),
					"options" => $profit_centers));
				?>
			</div>
			<div class="wd-input" id="filter_function" style="display:none">
				<label for="status"><?php __("Function") ?></label>
				<?php
				echo $this->Form->input('project_function_id', array(
					'type' => 'select',
					'name' => 'func',
					'div' => false,
					'multiple' => true,
					'label' => false,
					'hiddenField' => false,
					'style' => 'width:69% !important',
					"empty" => __("--", true),
					"options" => $project_functions));
				?>
			</div>

		</div>
		<?php echo $this->Form->end(); ?>
	</fieldset>
	<div style="clear: both;"></div>
	<ul class="type_buttons" style="padding-right: 10px !important">
		<li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
		<li><a href="javascript:void(0)" class="new" id="ok_sum"><?php __('OK') ?></a></li>
		<li><a href="javascript:void(0)" class="new" id="ok_export_file" style="display: none;"><?php __('OK') ?></a></li>
	</ul>
</div>
<!-- dialog_vision_staffing+++++++++++++++++.end -->
<!-- dialog_vision_portfolio -->
<div id="dialog_vision_portfolio" class="buttons" style="display: none;">
	<fieldset>
		<?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_portfolio', 'url' => array('action' => 'projects_vision', $appstatus))); ?>
		<div style="height:auto;" class="wd-scroll-form">
			<div class="wd-input">
				<label for="program"><?php __d(sprintf($_domain, 'Details'),"Program") ?></label>
				<?php
				echo $this->Form->input('project_amr_program_id', array(
					'div' => false,
					'label' => false,
					"empty" => __("--", true),
					'name' => 'vision_program',
					'id' => 'ProjectProjectAmrProgramId_port',
					'multiple' => true,
					'hiddenField' => false,
					// 'style' => 'margin-right:11px; width:52% !important',
					"options" => $project_arm_programs));
				?>							
			</div>
			<div class="wd-input">
				<label for="program"><?php __("Sub Program") ?></label>
				<?php
				echo $this->Form->input('project_amr_sub_program_id', array(
					'div' => false,
					'label' => false,
					'style' => 'width:69% !important',
					'name' => 'vision_sub_program',
					'id' => 'ProjectProjectAmrSubProgramId_port',
					'hiddenField' => false,
					'multiple' => false,
					'empty' => __('--', true),
					"class" => "wd-disable",
					"options" => array()));
				?> 
			</div>
			<div class="wd-input">
				<label for="project-manager"><?php __d(sprintf($_domain, 'Details'),"Project Manager") ?></label>
				<?php
				echo $this->Form->input('project_manager_id', array(
					'type' => 'select',
					'name' => 'vision_pm',
					'id' => 'ProjectProjectManagerId_port',
					'div' => false,
					'label' => false,
					'multiple' => false,
					'hiddenField' => false,
					"empty" => __("--", true),
					'style' => 'width:69% !important',
					"options" => $projectManagersOption));
				?>
			</div>
			<div class="wd-input">
				<label for="status"><?php __d(sprintf($_domain, 'Details'), "Status") ?></label>
				<select name="project_status_id" style="margin-right:11px; width:8.8%% !important; padding: 6px;" class="wd-customs" id="ProjectProjectStatusId_port">
					<option value="1" <?php echo isset($op) ? $op : '';?>><?php echo __("In progress", true)?></option>
					<!--option value="2" <?php echo isset($in) ? $in : '';?>><?php echo __("Opportunity", true)?></option-->
					<option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo __("Archived", true)?></option>
					<option value="4" <?php echo isset($md) ? $md : '';?>><?php echo __("Model", true)?></option>
				</select>
			</div>
		</div>
		<?php
		echo $this->Form->end();
		?>
	</fieldset>
	<div style="clear: both;"></div>
	<ul class="type_buttons" style="padding-right: 10px !important">
		<li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
		<li><a href="javascript:void(0)" class="new" id="ok_port"><?php __('OK') ?></a></li>
	</ul>
</div>
<!-- dialog_vision_portfolio.end -->
<script type="text/javascript">
	var listTopRow = {};
	var heightNewRow = {};
	var viewManDay = <?php echo json_encode($viewManDay); ?>;
	var viewGantt = <?php echo json_encode($viewGantt);?>;
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
	var yesNoFormatter = function( row, cell, value, columnDef, dataContext ) {
		return Slick.Formatters.HTMLData(row, cell, value == '1' ? '<?php __('Yes') ?>' : '<?php __('No') ?>', columnDef, dataContext);
	}
	var floatFormatter = function ( row, cell, value, columnDef, dataContext ) {
		value = value ? value : 0;
		return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '</span>', columnDef, dataContext);
	};
	var numberVal = function(row, cell, value, columnDef, dataContext){
		value = value ? value : 0;
		var icon = '';
		if(columnDef.id == 'ProjectBudgetSyn.assign_to_employee' || columnDef.id == 'ProjectBudgetSyn.assign_to_profit_center'
		|| columnDef.id == 'ProjectBudgetSyn.total_costs_var' || columnDef.id == 'ProjectBudgetSyn.internal_costs_var'
		|| columnDef.id == 'ProjectBudgetSyn.external_costs_var' || columnDef.id =='ProjectBudgetSyn.external_costs_progress'
		){
			icon = '%';
		}
		return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
	}
	var numberValEuro = function(row, cell, value, columnDef, dataContext){
		value = value ? value : 0;
		return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' &euro;</span>', columnDef, dataContext);
	}
	var numberValManDay = function(row, cell, value, columnDef, dataContext){
		value = value ? value : 0;
		return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +viewManDay+ '</span> ', columnDef, dataContext);
	}
	var numberValPercent = function(row, cell, value, columnDef, dataContext){
		value = value ? value : 0;
		return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +'%'+ '</span> ', columnDef, dataContext);
	}
	var linkFormatter = function ( row, cell, value, columnDef, dataContext ) {
		var idPr = dataContext.id ? dataContext.id : 0;
		var linkProjectName = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
		return '<a href=' + linkProjectName +'/'+ dataContext['id'] + ' class="project-is-' + idPr + '">' + value + '</a>';
	};
	/* begin render table*/
	var dataView,
		sortcol,
		triggger = false,
		$this = SlickGridCustom,
		grid,
		$sortColumn,
		$sortOrder;
	var data = <?php echo json_encode($dataView); ?>;
	var selects = <?php echo json_encode($selects); ?>;
	var columns = <?php echo jsonParseOptions($columns, array('formatter')); ?>;
	var totalHeaders = <?php echo json_encode($totalHeaders);?>;
	var columnCalculationConsumeds = <?php echo json_encode($columnCalculationConsumeds);?>;
	var columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro);?>;
	var columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay);?>;
	var columnAlignRightAndPercent = <?php echo json_encode($columnAlignRightAndPercent);?>;
	var typeGantt = <?php echo json_encode($typeGantt);?>;
	var viewStone = <?php echo !empty($confirmGantt) && isset($confirmGantt['stones']) ? json_encode($confirmGantt['stones']) : json_encode(false);?>;
	var options = {
		enableCellNavigation: false,
		enableColumnReorder: false,
		showHeaderRow: true,
		editable: false,
		enableAddRow: false,
		asyncEditorLoading : false,
		headerRowHeight: 60,
		rowHeight: 43,
		frozenColumn: -1
	};
	var columnFilters = {};
	var $parent = $('#project_container');
	var timeOutId = null;
	var syncFilter = function($input,column, delay){
		var result = [];
		$input.each(function(){
			result.push($.trim(this.value).toLowerCase());
		});
		columnFilters[grid.getColumnIndex(column)] = result;
		clearTimeout(timeOutId);
		timeOutId = setTimeout(function(){
			dataView.refresh();
			// tinh consumed of top, after filter and refresh grid
			var length = dataView.getLength();
			var listSumTop = new Array();
			for(var i = 0; i < length ; i++){
				$.each(dataView.getItem(i), function(key, val){
					if($.inArray(key, columnCalculationConsumeds) != -1){
						if(!listSumTop[key]){
							listSumTop[key] = 0;
						}
						val = val ? val : 0;
						listSumTop[key] += parseFloat(val);
					}
				});
			}
			for (var i = 0; i < columns.length; i++){
				var column = columns[i];
				var idOfHeader = column.id;
				var valOfHeader = typeof listSumTop[idOfHeader] != 'undefined' ? listSumTop[idOfHeader] : '';
				if($.inArray(idOfHeader, columnAlignRightAndManDay) != -1){
					valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' + viewManDay;
				} else if($.inArray(idOfHeader, columnAlignRightAndEuro) != -1){
					valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' &euro;';
				} else {
					if(valOfHeader){
						valOfHeader = number_format(valOfHeader, 2, ',', ' ');
					}
				}
				idOfHeader = idOfHeader.replace('.', '_');
				$('#'+idOfHeader+' p').html(valOfHeader);
			}
		}, delay || 200);
	}
	/*
	chia ra 2 truong hop:
	1. view gantt, frozen toan bo left columns
	2. non-gantt, display nhu cu (function phia tren)
	*/
	function createHeaderRow(){
		$sortOrder = $("<input type=\"text\" style=\"display:none\" name=\""+ $parent.attr('id') +".SortOrder\" />").appendTo($parent);
		$sortColumn = $("<input type=\"text\" style=\"display:none\" name=\""+ $parent.attr('id') +".SortColumn\" />")
			.appendTo($parent).change(function(){
				triggger = true;
				var index = grid.getColumnIndex($sortColumn.val());
				grid.setSortColumns([{
					sortAsc : $sortOrder.val() != 'asc',
					columnId : $sortColumn.val()
				}]);
				$parent.find('.slick-header-columns').children().eq(index).find('.slick-sort-indicator').click();
			});
		var $input;
		var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns">';
		//slick-pane slick-pane-header slick-pane-left
		var leftHeader = $('<div class="slick-headerrow-columns headerrow-columns-left custom-headerrow" style="width: auto"></div>'),
			rightHeader = $('<div class="slick-headerrow-columns headerrow-columns-right custom-headerrow" style="width: auto"></div>');
		for (var i = 0; i < columns.length; i++) {
			var noFilterInput = false, column = columns[i];
			var checkWeatherColumn = (column.id === "ProjectAmr.weather") || (column.id === "ProjectAmr.cost_control_weather") || (column.id === "ProjectAmr.rank");
			checkWeatherColumn = checkWeatherColumn || (column.id === "ProjectAmr.planning_weather");
			checkWeatherColumn = checkWeatherColumn || (column.id === "ProjectAmr.risk_control_weather");
			checkWeatherColumn = checkWeatherColumn || (column.id === "ProjectAmr.organization_weather");
			checkWeatherColumn = checkWeatherColumn || (column.id === "ProjectAmr.perimeter_weather");
			checkWeatherColumn = checkWeatherColumn || (column.id === "ProjectAmr.issue_control_weather");
			
			var idOfHeader = column.id;
			var valOfHeader = (totalHeaders[column.id] || totalHeaders[column.id] == 0) ? totalHeaders[column.id] : '';
			if($.inArray(idOfHeader, columnAlignRightAndManDay) != -1){
				valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
			} else if($.inArray(idOfHeader, columnAlignRightAndEuro) != -1){
				valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' &euro;';
			} else {
				if(valOfHeader){
					valOfHeader = number_format(valOfHeader, 2, ',', ' ');
				}
			}
			if( valOfHeader )valOfHeader = '<p>' + valOfHeader + '</p>';
			idOfHeader = idOfHeader.replace('.', '_');
			var left = 'l'+i;
			var right = 'r'+i;
			if( i <= options.frozenColumn ){
				leftHeader.append('<div class="ui-state-default slick-headerrow-column '+left+' '+right+'" id="'+idOfHeader+'">'+valOfHeader+'</div>');
			} else {
				rightHeader.append('<div class="ui-state-default slick-headerrow-column '+left+' '+right+'" id="'+idOfHeader+'">'+valOfHeader+'</div>');
			}

			if (column.id === "no." || column.id === "action." || checkWeatherColumn) {
				noFilterInput = true;
			}
			if(!noFilterInput){
				var header = grid.getHeaderRowColumn(column.id), isSelect = false;
				$(header).empty();
				if(selects[column.id]){
					isSelect = true;
					$input = $(selects[column.id]);
					delete selects[column.id];
				}else{
					$input = $("<input type=\"text\" />");
				}
				$('<div class="multiselect-filter"></div>').append(
					$input.attr('id',column.field).attr('name',column.field).data('column',column.id).val(columnFilters[column.id])
				).appendTo(header);
				if(isSelect){
					$input.multiSelect({
						column : column.id,
						noneSelected: '<?php __("--"); ?>',
						appendTo : $('body'),
						oneOrMoreSelected: '*',
						selectAll: false
					}, function($e){
						var o = this.data("config");
						syncFilter(this.data("multiSelectOptions").find('input:checked'), o.column);
					});
				}
				//  else {
				// 	$input.change(function(){
				// 		syncFilter($input, column.id);
				// 	});
				// }
			}
		}
		HistoryFilter.parse();
		if( options.frozenColumn > -1 ){
			$('.slick-pane.slick-pane-top.slick-pane-left .slick-headerrow-columns.slick-headerrow-columns-left').prepend(leftHeader);
			$('.slick-pane.slick-pane-top.slick-pane-right .slick-headerrow-columns.slick-headerrow-columns-right').prepend(rightHeader);
		} else {
			//single column
			$('.slick-pane.slick-pane-top.slick-pane-left .slick-headerrow-columns.slick-headerrow-columns-left').prepend(rightHeader);
		}
	}

	function comparer(a,b) {
		var x = a[sortcol], y = b[sortcol];
		return (x == y ? 0 : (x > y ? 1 : -1));
	}

	function comparer_date(a,b) {
		var arr;
		if (typeof(a[sortcol]) === "undefined" || a[sortcol]==""){
			c = "1/1/1970";
		}
		else{
			arr = a[sortcol].split("-");
			c = arr[1]+"/"+arr[0]+"/"+arr[2];
		}
		if (typeof(b[sortcol]) === "undefined" || b[sortcol]==""){
			d = "1/1/1970";
		}else{
			arr = b[sortcol].split("-");
			d = arr[1]+"/"+arr[0]+"/"+arr[2];
		}
		var c = new Date(c),
		d = new Date(d);
		return (c.getTime() - d.getTime());
	} 

	function _filter(v,d,is){
		//var d = d.toString();
		if($.isArray(d)){
			if($.inArray(v, d) != -1){
				return true;
			}
		}else if ((is && d == v) || (!is && d.toString().toLowerCase().indexOf(v) != -1)) {
			return true;
		}
		return false;
	}
	
	function filter(item) { // false: hide, true: show
		var result = true;
		$.each(columnFilters , function(i,data){
			var c = grid.getColumns()[i].field;
			var d = item.DataSet[c] || item[c];
			result = true;
			if(data.length > 0){
				if($.isArray(data)){
					result = false;
					$.each(data,function(undefined,v){
						if((result = _filter(v,d,item.DataSet[c]))){
							return false;
						}
					});
				}else{
					result = !_filter(v,d,item.DataSet[c]);
				}
			}
			if(!result){
				return false;
			}
		});
		return result;
	}
	
	// dataView = new Slick.Data.DataView();
	grid = $this.init($parent, data, columns, options);

	dataView = grid.getDataView();

	dataView.onRowCountChanged.subscribe(function (e, args) {
		grid.updateRowCount();
		grid.render();
	});
	dataView.onRowsChanged.subscribe(function (e, args) {
		grid.invalidateRows(args.rows);
		grid.render();
	});
	
	grid.onSort.subscribe(function(e, args) {
		
		sortcol = args.sortCol.field;
		var cols = args.sortCols;
		if (args.sortCol.datatype=="datetime"){
			dataView.sort(comparer_date, args.sortAsc);
		}
		else{
			dataView.sort(comparer, args.sortAsc);
			// isAsc = args.sortAsc;
			// grid.invalidateAllRows();
			// grid.render();
		}
		if(triggger){
			triggger = false;
			return;
		}
		// $sortOrder.val(args.sortAsc ? 'asc' : 'desc').change();
		// $sortColumn.val(args.sortCol.id).change();
	});
	var sessionTop = 0;
	var beforeElm = [];
	var nodeCanvas = [];
	
	//ADD CODE BY VINGUYEN 05/06/2014
	var counter;
	var enable_popup = <?php echo !empty($employee_info['Employee']['is_enable_popup']) ? 1 : 0 ?>;
	grid.onMouseLeave.subscribe(function(e, args) {
		jQuery(e.target).removeClass('hoverCell');
		clearInterval(counter);
	});
		
	grid.onMouseEnter.subscribe(function(e, args) {
		if( !enable_popup )return;
		var cell = args.grid.getCellFromEvent(e);
		var me = dataView.getItem(cell.row);

		if(columns[cell.cell].field=='Project.created_value')
		{
			clearInterval(counter);
			jQuery(e.target).addClass('hoverCell');
			counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectCreatedVals(e,me.id)}, 2000);
		}
		else if(columns[cell.cell].field=='Project.project_name')
		{
			clearInterval(counter);
			jQuery(e.target).addClass('hoverCell');
			counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectGlobalViews(e,me.id)}, 2000);
		}
		else
		{
			var field=columns[cell.cell].field.substr(0,16);
			if(field=='ProjectBudgetSyn'&&columns[cell.cell].field!='ProjectBudgetSyn.assign_to_profit_center'&&columns[cell.cell].field!='ProjectBudgetSyn.assign_to_employee')
			{
				clearInterval(counter);
				jQuery(e.target).addClass('hoverCell');
				counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectBudgetSyn(e,me.id)}, 2000);
			}
		}
	});
	//END
	var cols = grid.getColumns();
	// function resizeSetColumnHeader(){
	// 	for (var i = 0; i < columns.length; i++) {
	// 		if(columns[i].previousWidth != columns[i].width){
	// 			$('input[name="' + columns[i].field + '.Resize"]').val(columns[i].width).change();
	// 		}
	// 	}
	// 	var _cols = grid.getColumns();
	// 	var _numCols = cols.length;
	// 	var _gridW = 0;
	// 	for (var i=0; i<_numCols; i++) {
	// 		_gridW += _cols[i].width;
	// 	}				
	// 	$('#wd-header-custom').css('width', _gridW);
	// 	_gridW += 1000;
	// 	$('.slick-header-columns').css('width', _gridW+'px');
	// }
	// resizeSetColumnHeader();
	// grid.onColumnsResized.subscribe(function (e, args) {
	// 	resizeSetColumnHeader();
	// });
	HistoryFilter.setVal = function(name, value){
		var _cols = grid.getColumns();
		var _numCols = cols.length;
		var _gridW = 0;
		for (var i=0; i<_numCols; i++) {
			_gridW += _cols[i].width;
		}
		
		$('#wd-header-custom').css('width', _gridW);
		_gridW += 1000;
		$('.slick-header-columns').css('width', _gridW+'px');
		
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
		return $data.length > 0;
	}
	//ADD CODE BY VINGUYEN 16/05/2014---------
	<?php echo $this->element('multiSortJs'); ?>
	//END
	dataView.beginUpdate();
	dataView.setItems(data);
	dataView.setFilter(filter);
	dataView.endUpdate();
	createHeaderRow();
	// Export excel ----------------------
	$('#export-submit').click(function(){
		var length = dataView.getLength();
		var list = [];
		for(var i = 0; i < length ; i++){
			list.push(dataView.getItem(i).id);
		}
		$('#export-item-list').val(list.join(',')).closest('form').submit();
	});
	$('#add_portfolio').click(function(){
		var length = dataView.getLength();
		var list = [];
		for(var i = 0; i < length ; i++){
			list.push(dataView.getItem(i).id);
		}
		var urlPortfolio = '<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'projects_vision', $appstatus)); ?>';
		urlPortfolio = urlPortfolio+'/'+list.join('-');
		window.location.href = urlPortfolio;
	});
	/* table .end */
	var createDialog = function(){
		$("#ProjectProjectAmrProgramId_port").multiSelect({
			noneSelected: '<?php __("--"); ?>', 
			url :'<?php echo $html->url('/projects/get_sub_program/') ?>',
			update : "#ProjectProjectAmrSubProgramId_port",
			loadingClass : 'wd-disable',
			loadingText : 'Loading...',
			oneOrMoreSelected: '*', selectAll: false });
		$("#ProjectProjectAmrProgramId").multiSelect({
			noneSelected: '<?php __("--"); ?>', 
			url :'<?php echo $html->url('/projects/get_sub_program/') ?>',
			update : "#ProjectProjectAmrSubProgramId",
			loadingClass : 'wd-disable',
			loadingText : 'Loading...',
			oneOrMoreSelected: '*', selectAll: false });
		$("#ProjectProjectAmrProgramId_sum").multiSelect({
			noneSelected: '<?php __("--"); ?>', 
			url :'<?php echo $html->url('/projects/get_sub_program/') ?>',
			update : "#ProjectProjectAmrSubProgramId_sum",
			loadingClass : 'wd-disable',
			loadingText : 'Loading...',
			oneOrMoreSelected: '*', selectAll: false });
		$('#dialog_vision_portfolio, #dialog_vision_staffing, #dialog_vision_staffing_news').dialog({
			position :'center',
			autoOpen : false,
			autoHeight : true,
			modal : true,
			width : 500,
			open : function(e){
				var $dialog = $(e.target);
				$dialog.find('select').not('#ProjectProjectAmrProgramId,#ProjectProjectAmrProgramId_port,#ProjectProjectAmrProgramId_sum').multiSelect({
					noneSelected: '<?php __("--"); ?>', 
					oneOrMoreSelected: '*', selectAll: false });
				$dialog.dialog({open: $.noop});
				HistoryFilter.parse();
			}
		});
		createDialog = $.noop;
	}
	$("#add_vision_staffing").live('click',function(){
		createDialog();
		$("#dialog_vision_staffing").dialog('option',{title:'Vision Staffing Filter'}).dialog('open');
	});
	$("#add_vision_staffing_news").live('click',function(){
		createDialog();
		$("#dialog_vision_staffing_news").dialog('option',{title:'Vision Staffing+ Filter'}).dialog('open');
	});
	$("#add_vision_portfolio").live('click',function(){
		createDialog();
		$("#dialog_vision_portfolio").dialog('option',{title:'Vision Portfolio Filter'}).dialog('open');
	});

	$(".cancel").live('click',function(){
		$("#dialog_vision_portfolio, #dialog_vision_staffing, #dialog_vision_staffing_news").dialog('close');
	});
	$("#ok").click(function(){
		$("#form_vision_staffing").submit();
	});
	$("#ok_sum").click(function(){
		$("#form_vision_staffing_news").submit();
	});
	$("#ok_port").click(function(){
		$("#form_vision_portfolio").submit();
	});

	var count = 0;
	var personDefault = <?php echo json_encode($personDefault);?>;
	var appstatus = <?php echo json_encode($appstatus);?>;
	var checkStatus = <?php echo json_encode($checkStatus);?>;
	listProjectStautus(appstatus, checkStatus);
	var cate_id = appstatus ? appstatus : 1;
	$('#CategoryCategory').change(function(){
		location.href = '<?php echo $this->Html->url('/projects/index/-1') ?>?cate=' + $(this).val();
	});
	$('#CategoryStatus').change(function(){
		$('#CategoryStatus option').each(function(){
			var viewId = '';
			if($(this).is(':selected')){
				viewId = $('#CategoryStatus').val();
				if(viewId != 0){
					if(viewId == -1 || viewId == -2){
						window.location = ('/projects/index/' +viewId+ '?cate=' +cate_id);
					} else {
						window.location = ('/projects/index/' +viewId+ '?cate=' +cate_id);
					}
				}
			}
		});
	});
	$('#ProjectProjectFile0').click(function(){
		$('#ok_sum').show();
		$('#ok_export_file').hide();
	});
	
	$('#ProjectProjectFile1').click(function(){
		$('#ok_sum').hide();
		$('#ok_export_file').show();
	});
	$('#ok_export_file').click(function(){
		$("#dialog_vision_staffing_news").dialog('close');
	});
	$('#ok_export_file').click(function(){
		var showGantt = $('#show_gantt').find('input:checked').val();
		var realTime = $('#display-real-time-news').find('input:checked').val();
		var showBy = $('#show_by').find('input:checked').val();
		var showSum = $('#show_summary').find('input:checked').val();
		var program = [];
		$('#filter_program div label').each(function(){
			if($(this).find('input').is(':checked')){
				var _val = $(this).find('input:checked').val();
				program.push(_val);
			} 
		});
		var subProgram = [];
		$('#filter_sub_program div label').each(function(){
			if($(this).find('input').is(':checked')){
				var _val = $(this).find('input:checked').val();
				subProgram.push(_val);
			} 
		});
		var manager = [];
		$('#filter_manager div label').each(function(){
			if($(this).find('input').is(':checked')){
				var _val = $(this).find('input:checked').val();
				manager.push(_val);
			} 
		});
		var status = [];
		$('#filter_status div label').each(function(){
			if($(this).find('input').is(':checked')){
				var _val = $(this).find('input:checked').val();
				status.push(_val);
			} 
		});
		var profitCenter = [];
		$('#filter_profitCenter div label').each(function(){
			if($(this).find('input').is(':checked')){
				var _val = $(this).find('input:checked').val();
				profitCenter.push(_val);
			} 
		});
		var func = [];
		$('#filter_function div label').each(function(){
			if($(this).find('input').is(':checked')){
				var _val = $(this).find('input:checked').val();
				func.push(_val);
			} 
		});
		$('#ExportVisionShowGantt').val(showGantt);
		$('#ExportVisionShowType').val(showBy);
		$('#ExportVisionSummary').val(showSum);
		$('#ExportVisionProgram').val(program);
		$('#ExportVisionSubProgram').val(subProgram);
		$('#ExportVisionManager').val(manager);
		$('#ExportVisionStatus').val(status);
		$('#ExportVisionProfitCenter').val(profitCenter);
		$('#ExportVisionFunction').val(func);
		$('#ExportVisionIndexForm').submit();
	});

	function showProjectCreatedVals(e,id){
		
		jQuery.ajax({
		url : "/project_created_vals/ajax/"+id,
		type: "GET",
		data: data,
		cache: false,
		success: function (html) {
			jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0});
			var wh=jQuery(window).height();
			if(wh<768){
				//jQuery('#dialogDetailValue').css({'overflow':'auto'});
				jQuery('#contentDialog').css({'max-height':600,'width':'auto'});
			} else {
				jQuery('#contentDialog').css({'max-height':'none','width':'auto'});
			}
			jQuery('#contentDialog').html(html);
			jQuery(e.target).removeClass('hoverCell');
			jQuery(e.target).removeClass('loading');
			showMe();
			clearInterval(counter);
			}		
		});
	}
	function showProjectBudgetSyn(e,id){
		jQuery.ajax({
		url : "/project_budget_synthesis/ajax/"+id,
		type: "GET",
		data: data,
		cache: false,
		success: function (html) {
			jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':20});
			jQuery('#contentDialog').css({'max-height':600,'width':960});
			jQuery('#contentDialog').html(html);
			jQuery(e.target).removeClass('hoverCell');
			jQuery(e.target).removeClass('loading');
			showMe();
			clearInterval(counter);
			}		
		});
	}
	function showProjectGlobalViews(e,id){
		jQuery.ajax({
		url : "/project_global_views/ajax/"+id,
		type: "GET",
		data: data,
		cache: false,
		success: function (html) {
			var dump = $('<div />').append(html);
			if( dump.children('.error').length == 1 ){}
			else if( dump.children('#attachment-type').val() ){
				jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':20});
				jQuery('#contentDialog').css({'max-height':600,'width':900});
				jQuery('#contentDialog').html(html);
				showMe();
			}
			jQuery(e.target).removeClass('hoverCell');
			jQuery(e.target).removeClass('loading');
			clearInterval(counter);
		}		
		});
	}
	function listProjectStautus(id, view_id){
		if(id != ''){
			$.ajax({
				url: '/projects/getPersonalizedViews/' + id,
				async: false,
				beforeSend: function(){
					$('#CategoryStatus').html('Please waiting...');
				},
				success:function(datas) {
					var datas = JSON.parse(datas);
					var selected = selectDefined = selectDefault = '';
					if(view_id != null){
						if(view_id == 0){
							selected = 'selected="selected"';
						} else if(view_id == -1){
							selectDefined = 'selected="selected"';
						} else if(view_id == -2){
							selectDefault = 'selected="selected"';
						}
					}
					var content = '<option value="0" ' + selected + '><?php echo __("------- Select -------", true);?></option>';
					if(personDefault == false){
						content += '<option value="-1" ' + selectDefined + '><?php echo __("-- Default", true);?></option>';
					}
					 else {
						content += '<option value="-2" ' + selectDefault + '><?php echo __("-- Default", true);?></option>';
					}
					$.each(datas, function(ind, val){
						var selected = '';
						if(view_id == ind && view_id != null && view_id != -2 && view_id != -1 && view_id != 0){
							selected = 'selected="selected"';
						}
						content += '<option value="' +ind+ '" ' + selected + '>' + val + '</option>';
					});
					$('#CategoryStatus').html(content);
				}
			});
		}
	}
	// function resetGrid(){
	// 	if(heightNewRow){
	// 		$.each(heightNewRow, function(row, value){
	// 			var _idRow = value.id ? value.id : 0;
	// 			var id = value.idData ? value.idData : 0;
	// 			var _row = dataView.getRowById(id);
	// 			var lastRow = _row - 1;
	// 			var height = value.height ? value.height : 0;
	// 			var line = value.line ? value.line : 0;
	// 			var margin = value.margin ? value.margin : 0;
				
	// 			$('#'+_idRow).find('div.slick-cell').css('cssText', 'height: ' + height + 'px !important; line-height: ' + line + 'px !important;');
	// 			$('#'+_idRow).find('div.slick-cell').find('.wd-edit').css('cssText', 'margin-top: ' + margin + 'px !important;');
	// 			$('#'+_idRow).find('div.slick-cell').find('.wd-bt-big').css('cssText', 'margin-top: ' + margin + 'px !important;');
	// 			if(lastRow == -1 || row == 0){
	// 				$('#'+_idRow).css('top', '0px');
	// 			} else {
	// 				var heightRowLast = heightNewRow[lastRow].line ? heightNewRow[lastRow].line : 0; 
	// 				var topRowLast = heightNewRow[lastRow].id ? heightNewRow[lastRow].id : '';
	// 				topRowLast = topRowLast ? $('#'+topRowLast).css('top') : 0;
	// 				topRowLast = topRowLast ? parseFloat(topRowLast) : 0;
	// 				var _top = topRowLast + heightRowLast + 3;
	// 				$('#'+_idRow).css('top', _top + 'px');
	// 			}
	// 		});
	// 	}
	// }

	$('#map-icon').click(function(){
		var cat = $('#CategoryCategory').val();
		location.href = '<?php echo $this->Html->url('/projects/map/') ?>' + cat;
		return false;
	});

</script>
<?php
	echo $this->Form->create('ExportVision', array('url' => array('controller' => 'project_staffings', 'action' => 'export_system'), 'type' => 'file'));
	echo $this->Form->hidden('showGantt');
	echo $this->Form->hidden('showType');
	echo $this->Form->hidden('summary');
	echo $this->Form->hidden('program');
	echo $this->Form->hidden('sub_program');
	echo $this->Form->hidden('manager');
	echo $this->Form->hidden('status');
	echo $this->Form->hidden('profit_center');
	echo $this->Form->hidden('function');
	echo $this->Form->end();
?>