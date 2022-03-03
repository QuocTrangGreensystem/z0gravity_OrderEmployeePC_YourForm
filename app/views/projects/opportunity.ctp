<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->script('jquery.scrollTo'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid_v2'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common_v2'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php
$typeGantt = isset($confirmGantt['type']) ? $confirmGantt['type'] : 0;
$typeStatus = array('month', 'date', 'week', 'year');
$typeGantt = $typeStatus[$typeGantt];
if($viewGantt):
    if($typeGantt != 'year'):
?>
<style>
    .gantt-head{
        height: 36px !important;
		line-height:36px;
    }
	.gantt-num{
		line-height:36px;
	}
	#project_container{
		margin-right:-5px;
	}
</style>
<?php
    endif;
?>
<style>
    .slick-viewport{
        overflow-y: hidden !important;
    }
</style>
<?php
    //echo $html->css('gantt_v2_1_ajax');
    echo $html->css('gantt_v2_1');
    echo $html->script(array('html2canvas', 'jquery.html2canvasClone'));
    echo $html->css('jquery.mCustomScrollbar');
    echo $html->script(array('jquery.easing.1.3', 'jquery.mCustomScrollbarClone', 'jquery.ui.touch-punch.min'));
endif;
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'].'/oppor' ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style type="text/css">
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
    #project_container{
        overflow: visible !important;
    }
    #import-csv{
        background: url("<?php echo $this->Html->webroot('img/import.jpg'); ?>") no-repeat;
        display: block;
        width: 32px;
        float: right;
        margin-left: 8px;
        padding-bottom: 16px;
    }
    #import-csv:hover {
        background: url("<?php echo $this->Html->webroot('img/import_hover.jpg'); ?>") no-repeat;
        display: block;
        width: 32px;
        float: right;
        margin-left: 8px;
        padding-bottom: 16px;
    }
    #import-csv span {
        text-indent: -9999px;
        display: block;
    }
    .ui-dialog{font-size:11px;}.ui-widget{font-family:Arial,sans-serif;}#dialog_import_CSV label{color:#000;}.buttons ul.type_buttons{padding-right:10px!important;}.type_buttons .error-message{background-color:#FFF;clear:both;color:#D52424;display:block;width:212px;padding:5px 0 0;}.form-error{border:1px solid #D52424;color:#D52424;}
    .gantt-chart{
        margin-left: 0px;
    }
    .gantt-week .gantt-node td,
    .gantt-node td.gantt-d31,
    .gantt-node td.gantt-d30,
    .gantt-node td.gantt-d29,
    .gantt-node td.gantt-d28{
        border-width: 0 1px 0 0;
        /*height: 29px;*/
        height: 0px !important;
    }
    .gantt-head td{
        background-color: #06427A !important;
        height: 36px;
        vertical-align: middle;
    }
    .gantt-node table div {
        padding: 0;
        height: 32px;
    }
    .slick-headerrow-columns {
        height: 33px !important
    }
    #wd-header-custom{
        border: none !important;
    }
    #mcs1_container{

    }
    #mcs1_container .customScrollBox{


    }
    .twoTable{
        margin-top: -21px;
        overflow: auto;
    }
    .hiddenGantt{
        display: none !important;
    }
    .gantt-line-s{
        top: 18px;
    }
    .gantt-line-n{
        top: 5px;
    }
    #AjaxGanttChartDIV .gantt-node table div{
        height: 20px !important;
    }
    #AjaxGanttChartDIV .gantt-line-n{
        top: 10px;
    }
    #AjaxGanttChartDIV .gantt-line-desc{
        top: 4px;
    }
    #AjaxGanttChartDIV .gantt-line-s{
        top: 26px;
    }
    /*
    #mcs1_container{
        height: 680px;
        overflow-y: hidden;
    }
    .gantt{
        margin-top: 103px;
    }
    .gantt tbody{
        position: relative;
    }
    .gantt tbody tr:nth-child(1){
        top: 0px;
        width: 100%;
    }
    .gantt tbody tr:nth-child(2){
        position: absolute;
        top: 37px;
    }
    .gantt tbody tr:nth-child(3){
        position: absolute;
        top: 70px;
    }
    */
    .stone-detail-hide{
        display: none;
    }
    .stone-detail-show{

    }
</style>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<?php
    if($viewGantt && !empty($confirmGantt) && $confirmGantt['stones']):
?>
<style>
    .gantt-node table div {
        padding: 0;
        height: 42px !important;
    }
    .slick-header-column.ui-state-default{
        height: 22px !important;
    }
    .slick-headerrow-columns{
        height: 42px !important;
    }
    .slick-row{
        height: 43px !important;
    }
    .slick-cell{
        height: 39px !important;
        line-height: 42px !important;
    }
    .slick-headerrow .slick-headerrow-column input, .slick-headerrow .slick-headerrow-column select{
        padding: 10px 3px !important;
    }
    .wd-bt-big, a.wd-edit{
        margin-top: 10px !important;
    }
</style>
<?php endif;?>
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
$cates = array(
    1 => __("In progress", true),
    2 => __("Opportunity", true),
    3 => __("Archived", true),
    4 => __("Model", true)
);
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false
        ));
$columnAlignRight = array(
    'ProjectBudgetSyn.total_costs_var', 'ProjectBudgetSyn.internal_costs_var', 'ProjectBudgetSyn.external_costs_var',
    'ProjectBudgetSyn.external_costs_progress', 'ProjectBudgetSyn.assign_to_profit_center', 'ProjectBudgetSyn.assign_to_employee',
    'ProjectAmr.budget', 'ProjectAmr.project_amr_cost_control_id',
    'ProjectAmr.project_amr_organization_id', 'ProjectAmr.project_amr_plan_id', 'ProjectAmr.project_amr_perimeter_id',
    'ProjectAmr.project_amr_risk_control_id', 'ProjectAmr.project_amr_problem_control_id',
    'Project.created_value', 'Project.budget',
    'Project.number_1', 'Project.number_2', 'Project.number_3', 'Project.number_4', 'Project.number_5', 'Project.number_6', 'Project.number_7', 'Project.number_8'
);
$columnAlignRightAndEuro = array(
    'ProjectBudgetSyn.sales_sold', 'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid',
    'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast', 'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain',
    'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
    'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.external_costs_budget', 'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered',
    'ProjectBudgetSyn.external_costs_remain', 'ProjectBudgetSyn.external_costs_progress_euro', 'ProjectBudgetSyn.internal_costs_average',
    'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
    'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget',
    'Project.price_1', 'Project.price_2', 'Project.price_3', 'Project.price_4', 'Project.price_5', 'Project.price_6'
);
$columnAlignRightAndManDay = array(
    'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
    'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_man_day', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_man_day','ProjectAmr.delay',
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
    'ProjectBudgetSyn.external_costs_man_day', 'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.internal_costs_average','ProjectAmr.delay',
    'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
    'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget', 'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y',
    'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y',
    'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y',
    'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload',
    'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
    'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y',
    'Project.price_1', 'Project.price_2', 'Project.price_3', 'Project.price_4', 'Project.price_5', 'Project.price_6',
    'Project.number_1', 'Project.number_2', 'Project.number_3', 'Project.number_4', 'Project.number_5', 'Project.number_6', 'Project.number_7', 'Project.number_8'
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
    'provisional_budget_md' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional M.D", true),
    'provisional_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . date('Y', time()),
    'provisional_last_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 1),
    'provisional_last_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 2),
    'provisional_last_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 3),
    'provisional_next_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 1),
    'provisional_next_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 2),
    'provisional_next_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 3)
);
foreach ($fieldset as $_fieldset) {
    if (!empty($noProjectManager) && $_fieldset['key'] == 'Project.project_manager_id') {
        continue;
    }
    $financeFieldsKey = array_keys($financeFields);
    if(in_array($_fieldset['key'],$financeFieldsKey)){
        $fieldName = __($financeFields[$_fieldset['key']], true);
    } else if( strpos($_fieldset['key'], 'Project.') !== false ){
        if( $_fieldset['key'] == 'Project.category' ) {
            $fieldName = __($_fieldset['name'], true);
        } else {
            $fieldName = substr($_fieldset['name'], 0, 1) == '*' ? __(substr($_fieldset['name'], 1), true) : __d(sprintf($_domain, 'Details'), $_fieldset['name'], true);
        }
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
        case 'Project.project_name': {
            // start 21/10/2013 huythang
                $_column['formatter'] = 'linkFormatter';
                $_column['width'] = 240;
            // start 21/10/2013 huythang
                break;
            }
        // case 'ProjectBudgetSyn.provisional_budget_md':
        //          $_column['datatype'] = 'number';
        //     break;
        case 'Project.freeze_time':
        case 'Project.start_date':
        case 'Project.planed_end_date':
        case 'Project.created':
        case 'ProjectAmr.created':
        case 'Project.end_date':
        case 'Project.date_1':
        case 'Project.date_2':
        case 'Project.date_3':
        case 'Project.date_4': {
                $_column['datatype'] = 'datetime';
                break;
            }
        case 'ProjectAmr.weather':
        case 'ProjectAmr.rank':
        case 'ProjectAmr.cost_control_weather':
        case 'ProjectAmr.planning_weather':
        case 'ProjectAmr.risk_control_weather':
        case 'ProjectAmr.organization_weather':
        case 'ProjectAmr.perimeter_weather':
        case 'ProjectAmr.issue_control_weather':
        case 'ProjectAmr.customer_point_of_view': {
                $_column['width'] = 80;
                $_column['formatter'] = 'Slick.Formatters.ImageData';
                $_column['sorter'] = 'weatherSorter';
                break;
            }
        // start 23/10/2013 huythang
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
        {
            $_column['formatter'] = 'floatFormatter';
            break;
        }
        // end 23/10/2013 huythang
        //Added by QN 2015/1/24
        case 'Project.is_freeze':
        case 'Project.is_staffing':
        case 'Project.off_freeze':
        case 'Project.project_copy':
        case 'Project.yn_1':
        case 'Project.yn_2':
        case 'Project.yn_3':
        case 'Project.yn_4':
            $_column['formatter'] = 'yesNoFormatter';
            break;
        case 'Project.project_priority_id':
            $_column['formatter'] = 'selectFormatter';
            $_column['width'] = 120;
        break;
        // default:
        //     $_column['formatter'] = 'Slick.Formatters.HTMLData';
        //     break;
    }
    if(in_array($_fieldset['key'], $columnAlignRight)){
        $_column['formatter'] = 'numberVal';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndEuro)){
        $_column['formatter'] = 'numberValEuro';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndManDay)){
        $_column['formatter'] = 'numberValManDay';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndPercent)){
        $_column['formatter'] = 'numberValPercent';
        $_column['datatype'] = 'number';
    }
    $columns[] = $_column;
}
$columns[] = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'formatter' => 'Slick.Formatters.HTMLData'
);
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
        'DataSet' => array(
            'tm' => $project['Project']['technical_manager_id'],
            'cb' => $project['Project']['chief_business_id'],
            'pm' =>  $project['Project']['project_manager_id']
        )
    );
    //Added by QN on 2015/01/24
    $project['Project']['project_copy_id'] = isset($project['Project']['project_copy_id']) && isset($projectCopies[ $project['Project']['project_copy_id'] ]) ? $projectCopies[ $project['Project']['project_copy_id'] ] : '';
    $project['Project']['freeze_by'] = isset($project['Project']['freeze_by']) && isset($freezers[ $project['Project']['freeze_by'] ]) ? $freezers[ $project['Project']['freeze_by'] ] : '';
    //Added by QN on 2015/02/05
    if( isset($project['Project']['updated']) )$project['Project']['updated'] = $project['Project']['last_modified'] ? $project['Project']['last_modified'] : $project['Project']['updated'];
    $projectID = $project['Project']['id'];
    $workload = !empty($sumWorload[$projectID]) ? $sumWorload[$projectID] : 0;
    $overload = !empty($sumOverload[$projectID]) ? $sumOverload[$projectID]: 0;
    $project['ProjectAmr'][0]['manual_consumed'] = isset($manualData[$projectID]) ? $manualData[$projectID] : 0;
    $project['ProjectBudgetSyn'][0]['workload'] = $workload;
    $project['ProjectBudgetSyn'][0]['overload'] = $overload;
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
    $totalInter = !empty($budgetSyns[$projectID]['internal_costs_budget_man_day']) ? $budgetSyns[$projectID]['internal_costs_budget_man_day'] : 0;
    $project['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] = $totalInter;
    $externalManday = !empty($budgetSyns[$projectID]['external_costs_man_day']) ? $budgetSyns[$projectID]['external_costs_man_day'] : 0;
    $project['ProjectBudgetSyn'][0]['provisional_budget_md'] = $totalInter + $externalManday;

    foreach ($fieldset as $_fieldset) {
        if (is_array($_fieldset['path'])) {
            $_output = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
        } else {
            $_output = (string) Set::classicExtract($project, $_fieldset['path']);
        }
        switch ($_fieldset['key']) {
            case 'Project.project_name': {
                // start 21/10/2013 huythang
                    // $_output = $this->Html->link(__($project["Project"]["project_name"], true), array('action' => 'edit', $project['Project']['id']), array());
                $_output = $project["Project"]["project_name"];
                // end 21/10/2013 huythang
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
            case 'ProjectAmr.issue_control_weather': {
                    if( $_output ){
                        $data['DataSet'][$_fieldset['key']] = $_output;
                        $_output = $this->Html->url('/img/' . $_output . '.png');
                    }
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
                    //$selects[$_fieldset['key']][$project['ProjectPriority']['id']] = $_output;
                    break;
                }
            case 'Project.project_status_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectStatus']['id'];
                    $selects[$_fieldset['key']][$project['ProjectStatus']['id']] = $_output;
                    break;
                }
            case 'Project.project_manager_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['Employee']['id'];
                    $selects[$_fieldset['key']][$project['Employee']['id']] = $_output;
                    if ($_output) {
                        $projectManagers[$project['Employee']['last_name'] . '_' . ($eindex++)] = array($project['Employee']['id'], $_output);
                    }
                    break;
                }
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
            case 'Project.list_1':
            case 'Project.list_2':
            case 'Project.list_3':
            case 'Project.list_4':
                $_key = explode('.', $_fieldset['key']);
                $_key = $_key[1];
                if( isset($datasets[$_key][$_output]) ){
                    $id = $_output;
                    $data['DataSet'][$_fieldset['key']] = $id;
                    $_output = $datasets[$_key][$id];
                    $selects[$_fieldset['key']][$id] = $_output;
                } else {
                    $_output = '';
                }
            break;
            case 'Project.date_1':
            case 'Project.date_2':
            case 'Project.date_3':
            case 'Project.date_4':
                $_output = $str_utility->convertToVNDate($_output);
                break;
            case 'Project.category':
                $_output = $cates[$_output];
                break;
            case 'Project.bool_1':
            case 'Project.bool_2':
            case 'Project.bool_3':
            case 'Project.bool_4':
                $_output = $_output ? 1 : 0;
                $selects[$_fieldset['key']][$_output] = $_output;
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
    $data['action.'] = '<div style="margin: 0 auto !important; width: 54px;">' . $this->Html->link(__('Edit', true), array(
                'action' => 'edit', $project['Project']['id']), array('class' => 'wd-edit'));
    if($employee['Role']['name'] == 'pm' && $employee['Employee']['delete_a_project'] != 1){
        //eo lam cm gi het
    } else {
        $data['action.'] .= '<div class="wd-bt-big">' . $this->Html->link(__('Delete', true), array(
                    'action' => 'delete', $project['Project']['id'], '?' => array('cate' => $cate)), array(
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
                /*
                $_phase = array(
    				'id' => $phace['id'],
                    'name' => !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '',
                    'start' => $this->Gantt->toTime($phace['phase_planed_start_date']),
                    'end' => $this->Gantt->toTime($phace['phase_planed_end_date']),
                    'rstart' => $this->Gantt->toTime($phace['phase_real_start_date']),
                    'rend' => $this->Gantt->toTime($phace['phase_real_end_date']),
                    'color' => !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380'
                );
                */
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
$selects['Project.project_priority_id'] = $priorities;
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
                    ?>
                        <select style="margin-right: 5px; width: auto !important; padding: 6px; float: none;" class="wd-customs" id="CategoryStatus">
                            <option value="0"><?php echo  __("--Select--", true);?></option>
                        </select>
                    <?php
                        echo $this->Form->end();
                    ?>
                    <a href="<?php echo $this->Html->url('/projects/map/') ?>" id="map-icon" class="btn btn-globe"></a>
                    <?php if( $isTablet ): ?>
                        <?php
                        if($employee['Employee']['create_a_project'] != 1 && $employee['Role']['id'] == 3){
                            }else{ ?>
                                <a href="javascript:void(0);" id="add_project" class="btn-text btn-blue">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Add Project') ?></span>
                                </a>
                        <?php } ?>
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
                        <?php
                            if($employee['Employee']['create_a_project'] != 1 && $employee['Role']['id'] == 3){
                                }else{ ?>
                                    <a href="javascript:void(0);" id="add_project" class="btn-text btn-blue">
                                        <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                        <span><?php __('Add Project') ?></span>
                                    </a>
                    <?php } endif ?>
                </div>
                <?php
                echo $this->Session->flash();
                ?>
                <div class="wd-table project-list" id="project_container" style="width: <?php echo ($viewGantt == true) ? '50%' : '100%'?>; float: left" rels="<?php echo ($viewGantt && !empty($confirmGantt) && $confirmGantt['stones']) ? 'yes' : 'no';?>">

                </div>
                <?php if($viewGantt):?>
                <div id="GanttChartDIV" style="width:49%; float: right; margin-top: -0.6px; transition: width 2s; -webkit-transition: width 2s;">
                    <?php
                            $rows = $display = 0;
                            if (empty($ganttStart) || empty($ganttEnd)) {
                                echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                            } else {
                                $this->Gantt->create($typeGantt, $ganttStart, $ganttEnd, array(), false, true, 'd-m-Y', true);
                                if($typeGantt == 'year'){
                                    $this->Gantt->drawEnd('', true, '', 'In progress');
                                }
                                $this->Gantt->drawEnd('', true, '', 'In progress');
                                $this->Gantt->end(true, true);
                                $this->Gantt->create($typeGantt, $ganttStart, $ganttEnd, array(), false, true, 'd-m-Y', true, true);
                                $colors = array('#16168E', '#9D3F6E', '#179393');
                                $numColor = 0;
                                foreach ($gantts as $value) {
                                    $rows++;
                                    if (empty($value['phase'])) {
                                        //$this->Gantt->drawLineProject(__('no data exit', true), 0, 0, 0, 0, '#ffffff');
                                    } else {
                                        foreach ($value['phase'] as $node) {
                                            $color = '#004380';
                                            if (!empty($node['color'])) {
                                                $color = $node['color'];
                                            }
                                            if(!empty($confirmGantt) && !$confirmGantt['initial']){
                                                $node['start'] = $node['end'] = '';
                                            }
                                            if (!empty($confirmGantt) && !$confirmGantt['real']) {
                                                $node['rstart'] = $node['rend'] = '';
                                            }
                                            /**
                                             * Tinh completed for phase
                                             */
                                            $_pId = !empty($value['project_id']) ? $value['project_id'] : 0;
                                            $completed = 0;
                                            if(!empty($phases) && !empty($phases[$_pId])){ //&& !empty($phases[$_pId][$node['id']])){
                                                $ds = $phases[$_pId];
                                                $workload = !empty($ds['workload']) ? $ds['workload'] : 0;
                                                $consumed = !empty($ds['workload']) ? $ds['consumed'] : 0;
                                                if($workload == 0){
                            						$completed = 0;
                            					} else{
                            						$completed = round((($consumed*100)/$workload), 2);
                            					}
                                            }
                                            $color = $colors[$numColor];
                                            $ston = !empty($stones[$_pId]) && !empty($confirmGantt) && ($confirmGantt['stones']) ? $stones[$_pId] : array();
                                            $this->Gantt->drawLineProject($value['id'], $value['name'], $node['start'], $node['end'], $node['rstart'], $node['rend'], $color, $completed, $ston);
                                        }
                                    }
                                    $numColor++;
                                    if($numColor == 3){
                                        $numColor = 0;
                                    }
                                    $status = 'In progress';
                                    $this->Gantt->drawEnd($value['name'], true, $value['program'], $status, $value['project_id']);
                                }
                                $this->Gantt->end(true, true, true, false);
                            }
                    ?>
                </div>
                <?php endif;?>
                <div id="pager" style="clear:both;width:100%;height:36px;<?php echo ($viewGantt == true) ? '' : 'float: left;';?>;"></div>
            </div>
        </div>
    </div>
</div>
<!-- ADD CODE BY VINGUYEN 05/06/2014 -->
<?php echo $this->element('dialog_detail_value') ?>
<!-- END --->
<?php echo $this->element('dialog_projects') ?>

<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'projects', 'action' => 'export_project', $viewId)));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->

<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script('responsive_table.js'); ?>
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
                            <?php /*
                              <br/>
                              <?php
                              echo $this->Form->radio('project_staffing_id', array(4 => __("Profit center & Function", true)), array(
                              'name' => 'type',
                              'fieldset' => false,
                              'legend' => false,
                              'value' => '0'));
                              ?>
                             *
                             */ ?>
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
                    // 'style' => 'margin-right:11px; width:52% !important',
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
                <label for="project-manager"><?php __d(sprintf($_domain, 'Details'), "Project Manager") ?></label>
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
                    "options" => $projectManagers));
                ?>
            </div>
            <div class="wd-input">
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
                            <?php
                            //echo $this->Form->radio('project_staffing_id', array(2 => __("Profit center & Project", true)), array(
//                                'name' => 'type',
//                                'fieldset' => false,
//                                'disabled' => 'disabled',
//                                'legend' => false,
//                                'value' => '0'));
                            ?>
                            <?php
                            //echo $this->Form->radio('project_staffing_id', array(3 => __("Function & Project", true)), array(
//                                'name' => 'type',
//                                'fieldset' => false,
//                                'disabled' => 'disabled',
//                                'legend' => false,
//                                'value' => '0'));
                            ?>
                            <?php /*
                              <br/>
                              <?php
                              echo $this->Form->radio('project_staffing_id', array(4 => __("Profit center & Function", true)), array(
                              'name' => 'type',
                              'fieldset' => false,
                              'legend' => false,
                              'value' => '0'));
                              ?>
                             *
                             */ ?>
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
                <label for="program"><?php __("Program") ?></label>
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
                <label for="project-manager"><?php __d(sprintf($_domain, 'Details'), "Project Manager") ?></label>
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
                    "options" => $projectManagers));
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
                <label for="program"><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
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
                <label for="project-manager"><?php __d(sprintf($_domain, 'Details'), "Project Manager") ?></label>
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
                    "options" => $projectManagers));
                ?>
            </div>
            <div class="wd-input">
                <label for="status"><?php __d(sprintf($_domain, 'Details'), "Status"); $in = 'selected="selected"'; ?></label>
                <select name="project_status_id" style="margin-right:11px; width:8.8%% !important; padding: 6px;" class="wd-customs" id="ProjectProjectStatusId_port">
                    <option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Opportunity", true)?></option>
                </select>
                <?php
                /*echo $this->Form->input('project_status_id', array(
                    'type' => 'select',
                    'name' => 'vision_status',
                    'id' => 'ProjectProjectStatusId_port',
                    'div' => false,
                    'multiple' => false,
                    'hiddenField' => false,
                    'label' => false,
                    'style' => 'width:69% !important',
                    "empty" => __("--", true),
                    "options" => $project_statuses));
				*/

                ?>
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
    var isPM = <?php echo json_encode($isPM) ?>,
        editable = <?php echo json_encode($editable) ?>,
        employeeId = <?php echo json_encode($employee_info['Employee']['id']) ?>;
    var dataViewGG = {};
    var listTopRow = {};
    var heightNewRow = {};
    var gridGG = {};
    var  viewManDay = <?php echo json_encode($viewManDay); ?>;
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
        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' +  number_format(value, 2, ',', ' ') + '</span>', columnDef, dataContext);
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
    var listPriorities = <?php echo json_encode($priorities) ?>;

    var selectFormatter = function(row, cell, value, columnDef, dataContext){
        var html;
        var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
        if( !isPM || editable[dataContext.id] || xyz ){
            html = '<select style="padding: 2px 5px" onchange="updatePriority.call(this, \'' + dataContext.id + '\', ' + cell + ')"><option value="0">--</option>';
            for(var i in listPriorities){
                html += '<option value="' + i + '" ' + (i == dataContext.DataSet['Project.project_priority_id'] ? 'selected' : '') + '>' + listPriorities[i] + '</option>';
            }
            html += '</select>';
        } else {
            html = dataContext['Project.project_priority_id'];
        }
        return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
    };
    function updatePriority(id, cell){
        var t = $(this).prop('disabled', true).css('background-color', '#eee');
        var v = $(this).val();
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updatePriority') ?>',
            type: 'POST',
            data: {
                data: {
                    id: id,
                    project_priority_id: v
                }
            },
            complete: function(){
                //update grid
                var item = dataView.getItemById(id);
                dataView.beginUpdate();
                item.DataSet['Project.project_priority_id'] = v;
                item['Project.project_priority_id'] = listPriorities[v] ? listPriorities[v] : '';
                dataView.updateItem(id, item);
                dataView.endUpdate();
                //update UI
                t.prop('disabled', false).css('background-color', '#fff');

                //get header cell
                var $input = $('.slick-headerrow-column.l' + cell + ' .multiSelect');
                var o = $input.data('config');
                utility.syncFilter($input.data("multiSelectOptions").find('input:checked'), o.column);
            }
        });
    }
    var imagePriorities = {
        sun: 0,
        cloud: 1,
        rain: 2,
        up: 3,
        down: 4,
        mid: 5
    };

    function weatherSorter(a, b){
        return imagePriorities[a.DataSet[sortcol]] > imagePriorities[b.DataSet[sortcol]] ? 1 : -1;
    }
    /* begin render table*/
    var dataView,sortcol,triggger = false,grid,$sortColumn,$sortOrder;
    var data = <?php echo json_encode($dataView); ?>;
    var selects = <?php echo json_encode($selects); ?>;
    var columns = <?php echo jsonParseOptions($columns, array('formatter', 'sorter')); ?>;
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
        headerRowHeight: 30,
        rowHeight: (viewStone && viewGantt) ? 43 : 33
    };
    var columnFilters = {};
    var $parent = $('#project_container');
    var timeOutId = null;
    var utility = {};
    (function($){
        $(function () {

            function resetView(){
				return false;
                $('.slick-viewport .grid-canvas .slick-row').children('div').find('a').each(function(){
                    var isClass = $(this).attr('class').split(' ')[0];
                    var $row = $('.'+isClass).parent().parent();
                    if($row.hasClass('stone-detail-show')){
                        $row.removeClass('stone-detail-show');
                        $('.'+isClass).parent().parent().find('div.slick-cell').each(function(){
                            $(this).css('cssText', 'height: 39px !important; line-height: 42px !important;');
                            $(this).find('.wd-edit').css('cssText', 'margin-top: 10px !important;');
                            $(this).find('.wd-bt-big').css('cssText', 'margin-top: 10px !important;');
                        });
                    }
                });
                // close all project other project clicking
                var $tableGantt = $('.twoTable div.gantt-chart-wrapper table');
                if($tableGantt){
                    $tableGantt.find('tr div.gantt-line').each(function(){
                        //var totalDiv = $(this).children('div').length;
                        //var ind = (totalDiv && totalDiv == 3) ? 2 : 3;
						var $i = 0;
					var ind = 0 ;
					$(this).children('div').each(function(index, element) {
						$i++;
                        var classTmp = $(this).attr('class');
						if(classTmp.indexOf("st-detail"))
						{
							ind = $i;
						}
                    });
                        if($(this).children().eq(ind).hasClass('stone-detail-hide')){
                            var $elem = $(this).children();
                            var _classRow = $elem.parent().parent().parent().attr('class').split(' ')[1];
                            $elem.eq(ind).removeClass('stone-detail-hide');
                            $elem.eq(ind+1).addClass('stone-detail-hide');
                            $elem.eq(ind+1).removeClass('stone-detail-show');
                            $('.'+_classRow).find('td.gantt-node div.gantt-line table tbody tr td').each(function(){
                                $(this).find('div').css('cssText', 'height: 42px !important;');
                            });
                        }
                    });
                }
                // close row of datagridview
                if(listTopRow){
                    $.each(listTopRow, function(id, val){
                        $('#'+id).css('top', val + 'px');
                    });
                }
                listTopRow = {};
            }
            var syncFilter = utility.syncFilter = function($input,column, delay){
                resetView();
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
                    var listProject = new Array();
                    for(var i = 0; i < length ; i++){
                        $.each(dataView.getItem(i), function(key, val){
                            if($.inArray(key, columnCalculationConsumeds) != -1){
                                if(!listSumTop[key]){
                                    listSumTop[key] = 0;
                                }
                                val = val ? val : 0;
                                listSumTop[key] += parseFloat(val);
                            }
                            if(key === 'id'){
                                listProject.push('pr-'+val);
                            }
                        });
                    }
                    $('.twoTable table.gantt tr').each(function(){
                        if($(this).attr('class')){
                            var _getClass = $(this).attr('class').split(' ')[1];
                            if($.inArray(_getClass, listProject) != -1){
                                $('.'+_getClass).removeClass('hiddenGantt');
                            } else {
                                $('.twoTable table.gantt tr.'+_getClass).addClass('hiddenGantt');
                            }
                        }
                    });
                    for (var i = 0; i < columns.length; i++){
                        var column = columns[i];
                        var idOfHeader = column.id;
                        var valOfHeader = (listSumTop[column.id] || listSumTop[column.id] == 0) ? listSumTop[column.id] : '';
                        if($.inArray(idOfHeader, columnAlignRightAndManDay) != -1){
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
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
                    // end tinh consumed of top, after filter and refresh grid
                    sortCustomList();
                },delay || 200);
            }

            function updateHeaderRow(widthGrid) {
                $sortOrder = $("<input type=\"text\" style=\"display:none\" name=\""+ $parent.attr('id') +".SortOrder\" />")
                .appendTo($parent);
                $sortColumn = $("<input type=\"text\" style=\"display:none\" name=\""+ $parent.attr('id') +".SortColumn\" />")
                .appendTo($parent).change(function(){
                    triggger = true;
                    var index = grid.getColumnIndex($sortColumn.val());
                    grid.setSortColumns([{
                            sortAsc : $sortOrder.val() != 'asc',
                            columnId : $sortColumn.val()
                        }]);

                    // console.log($sortOrder.val());
                    // console.log($sortColumn.children.val());

                    $parent.find('.slick-header-columns').children().eq(index)
                    .find('.slick-sort-indicator').click();
                });
                if(viewGantt){
                    var headerWidth = widthGrid;
                    var container = $('.wd-list-project').width();
                    if(headerWidth >= (parseFloat(container)/2)){
                        headerWidth = parseFloat(container)/2;
                    }
                    $('#project_container').css('width', headerWidth+'px');
                    var widthGantt = parseFloat(container) - parseFloat(headerWidth) - 3;
                    $('#GanttChartDIV').css('width', widthGantt+'px');
                    $('.dragger_container').css('width', widthGantt+'px');
					if($display_all_name_of_milestones == 1)
					{
						$('#MilestonesCheck').trigger('click');
						$('#MilestonesCheck').attr('checked','checked');
					}
                }
                var $input;
                var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+widthGrid+'px">';
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
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l'+i;
                    var right = 'r'+i;
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom '+left+' '+right+'" id="'+idOfHeader+'"><p>'+valOfHeader+'</p></div>';

                    if (column.id === "no." || column.id === "action." || checkWeatherColumn) {
                        noFilterInput = true;
                    }
                    if(!noFilterInput){
                        var header = grid.getHeaderRowColumn(column.id),isSelect = false;
                        $(header).empty();
                        if(selects[column.id]){
                            isSelect = true;
                            $input = $(selects[column.id]);
                            delete selects[column.id];
                        }else{
                            $input = $("<input type=\"text\" />");
                        }
                        $('<div class="multiselect-filter"></div>').append($input.attr('id',column.field).attr('name',column.field).data('column',column.id)
                        .val(columnFilters[column.id])).appendTo(header);
                        if(isSelect){
                            $input.multiSelect({
                                column : column.id,
                                noneSelected: '<?php __("--"); ?>',
                                appendTo : $('body'),
                                oneOrMoreSelected: '*',selectAll: false },function($e){
                                var o = this.data("config");
                                syncFilter(this.data("multiSelectOptions").find('input:checked'),o.column);
                            });
                        }
                    }
                    $("<input type=\"text\" style=\"display:none\" name=\""+ column.field +".Resize\" />")
                    .data('columnIndex',i).appendTo($parent).change(function(){
                        var $element = $(this);
                        columns[$element.data('columnIndex')].width = Number($element.val());
                        grid.eval('applyColumnHeaderWidths();updateCanvasWidth(true);');
                    });
                }
                headerConsumed += '</div>';
                HistoryFilter.parse();
                widthGrid += 1000;
                $('.slick-header-columns').css('width', widthGrid+'px');
                $('.slick-header-columns').after(headerConsumed);
            }
            function comparer_number(a,b) {
                var x = a[sortcol], y = b[sortcol];
                return (x == y ? 0 : (x > y ? 1 : -1));
            }
            function comparer(a,b) {
                var x = a[sortcol] + '', y = b[sortcol] + '';
                x = x.toLowerCase();
                y = y.toLowerCase();
                if( $.isFunction(String.localeCompare) ){
                    return x.localeCompare(y);
                }
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
                    d  = "1/1/1970";
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
            function filter(item) {
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

            dataView = new Slick.Data.DataView();
            grid = new Slick.Grid($parent, dataView, columns, options);
            gridGG = grid;
            dataView.onRowCountChanged.subscribe(function (e, args) {
                grid.updateRowCount();
                grid.render();
            });
            dataView.onRowsChanged.subscribe(function (e, args) {
                grid.invalidateRows(args.rows);
                grid.render();
            });
            var sortCustomList = function(){
				if(!viewGantt)
				{
					return false;
				}
                dataView.refresh();
                // tinh consumed of top, after filter and refresh grid
                var length = dataView.getLength();
                var _listProjectSorts = new Array();
                for(var i = 0; i < length ; i++){
                    $.each(dataView.getItem(i), function(key, val){
                        if(key === 'id'){
                            _listProjectSorts.push('pr-'+val);
                        }
                    });
                }
                var temp = setTimeout(function(){
                    var $tableGantt = $('.twoTable div.gantt-chart-wrapper table:nth-child(2)');
                    var newGantt = new Array();
                    if(_listProjectSorts){
                        $.each(_listProjectSorts, function(key, val){
                            newGantt += $tableGantt.find('.'+val)[0].outerHTML;
                            $tableGantt.find('.'+val).remove();
                        });
                        $tableGantt.children('tbody').prepend(newGantt);
                    }
					syncSizeOfRow();
					clearTimeout(temp);
                } , 500);
            }
            sortCustomList();
			var syncSizeOfRow = function(){
				if(viewGantt)
				{
					grid.invalidate();
					grid.render();
					var $canvas = $(grid.getCanvasNode()), $allRows = $canvas.find('.slick-row');
					var countElm = $allRows.length - 1;
					var topGeneral = 0;
					$($allRows).each(function($index, $node) {
						var $id = $node.id;
						var $class = $id.replace('row','pr');
						$height = $('.'+$class).height();
						topGeneral = $('.'+$class).position().top ;
						$height = parseInt($height);
						$heightCell = $height - 4;
						$('#'+$id).find('div.slick-cell').css('cssText', 'height: ' + $heightCell + 'px !important; line-height: ' + $heightCell + 'px !important;');
						$('#'+$id).css('cssText', 'top: '+topGeneral+'px; ');
					});
				}
			}
            $(grid.getHeaderRow()).find(":input").live("change keyup", function () {
                var $element = $(this);
                syncFilter($element , $element.data('column') , 500);
            });

            grid.onSort.subscribe(function(e, args) {
                resetView();
                sortcol = args.sortCol.field;
                var cols = args.sortCols;
                if (args.sortCol.datatype=="datetime"){
                    dataView.sort(comparer_date, args.sortAsc);
                }
                else if(args.sortCol.datatype == "number")
                    {
                        //alert(1231);
                        dataView.sort(comparer_number, args.sortAsc);
                    }
                    else
                    {
                        if( typeof args.sortCol.sorter != 'undefined' ){
                            dataView.sort(args.sortCol.sorter, args.sortAsc);
                        }
                        else{
                            dataView.sort(comparer, args.sortAsc);
                            // isAsc = args.sortAsc;
                            // grid.invalidateAllRows();
                            // grid.render();
                        }
                    }
                if(triggger){
                    triggger = false;
                    return;
                }
                $sortOrder.val(args.sortAsc ? 'asc' : 'desc').change();
                $sortColumn.val(args.sortCol.id).change();
                sortCustomList();
            });
            function msieversion() {
                var ua = window.navigator.userAgent;
                var msie = ua.indexOf("MSIE ");
                if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
                    return true;
                    // If Internet Explorer, return version number
                    //alert(parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
                } else {
                    return false;
                    //alert('otherbrowser');
                    // If another browser, return 0
                }
            }
			grid.onScroll.subscribe(function(args, e, scope){
                if(viewGantt){
				    var checkIE = msieversion();
                    if(checkIE == true){
                        clearTimeout($.data(this, 'scrollTimer'));
                        $.data(this, 'scrollTimer', setTimeout(function(){
                            syncSizeOfRow();
        					$(".slick-viewport").scrollTop($('.twoTable').scrollTop());
        					$('.twoTable').scrollTop($(".slick-viewport").scrollTop());
        					$(".slick-viewport").height($(".twoTable").height());
                        }, 250));
                    } else {
                        syncSizeOfRow();
    					$(".slick-viewport").scrollTop($('.twoTable').scrollTop());
    					$('.twoTable').scrollTop($(".slick-viewport").scrollTop());
    					$(".slick-viewport").height($(".twoTable").height());
                    }
				}
            });
            $('.twoTable').scroll(function(){
                $(".slick-viewport").scrollTop($('.twoTable').scrollTop());
				var temp = setTimeout(function(){
					$('.slick-viewport').scrollTop($('.twoTable').scrollTop()+10);
					clearTimeout(temp);
				},100);
            });
			//ADD CODE BY VINGUYEN 05/06/2014
			var counter;
            var enable_popup = <?php echo !empty($employee_info['Employee']['is_enable_popup']) ? 1 : 0 ?>;
            var see_budget = <?php echo !empty($employee_info['CompanyEmployeeReference']['see_budget']) ? 1 : 0 ?>;
            var update_budget = <?php echo !empty($employee_info['Employee']['update_budget']) ? 1 : 0 ?>;
            var isPm = <?php echo !empty($employee_info['Role']['name']) && $employee_info['Role']['name'] == 'pm' ? 1 : 0 ?>;
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
					   if(!see_budget && isPm){
					       return;
					    }
						var field=columns[cell.cell].field.substr(0,16);
						if(field=='ProjectBudgetSyn'&&columns[cell.cell].field!='ProjectBudgetSyn.assign_to_profit_center'&&columns[cell.cell].field!='ProjectBudgetSyn.assign_to_employee')
						{
							clearInterval(counter);
							jQuery(e.target).addClass('hoverCell');
							counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectBudgetSyn(e,me.id)}, 2000);
						}
					}
				});
				function showProjectCreatedVals(e,id){

					jQuery.ajax({
					url : "/project_created_vals/ajax/"+id,
					type: "GET",
					data: data,
					cache: false,
					success: function (html) {
						jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0,'overflow':'hidden'});
						var wh=jQuery(window).height();
                        if(wh<768){
                            jQuery('#dialogDetailValue').css({'overflow':'auto'});
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
						jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':20,'overflow':'auto'});
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
    						jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':20,'overflow':'auto'});
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
			//END
            var Columns = grid.getColumns();
            /**
             * Calculation width of grid.
             */
            var cols = grid.getColumns();
            var numCols = cols.length;
            if(numCols <= 70){
                $('#pager').css('margin-top', '30px');
            }
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            function resizeSetColumnHeader(){
                for (var i = 0; i < columns.length; i++) {
                    if(columns[i].previousWidth != columns[i].width){
                        $('input[name="' + columns[i].field + '.Resize"]').val(columns[i].width).change();
                    }
                }
				var _cols = grid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                if(viewGantt){
                    var headerWidth = _gridW;
                    var container = $('.wd-list-project').width();
                    if(headerWidth >= (parseFloat(container)/2)){
                        headerWidth = parseFloat(container)/2;
                    }
                    $('#project_container').css('width', headerWidth+'px');
                    var widthGantt = parseFloat(container) - parseFloat(headerWidth) - 3;
                    $('#GanttChartDIV').css('width', widthGantt+'px');
                    $('.dragger_container').css('width', widthGantt+'px');
					if($display_all_name_of_milestones == 1)
					{
						$('#MilestonesCheck').trigger('click');
						$('#MilestonesCheck').attr('checked','checked');
					}
                }
                $('#wd-header-custom').css('width', _gridW);
                _gridW += 1000;
                $('.slick-header-columns').css('width', _gridW+'px');
            }
            resizeSetColumnHeader();
            grid.onColumnsResized.subscribe(function (e, args) {
                resizeSetColumnHeader();
			});
            HistoryFilter.setVal = function(name, value){
                var _cols = grid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                if(viewGantt){
                    var headerWidth = _gridW;
                    var container = $('.wd-list-project').width();
                    if(headerWidth >= (parseFloat(container)/2)){
                        headerWidth = parseFloat(container)/2;
                    }
                    $('#project_container').css('width', headerWidth+'px');
                    var widthGantt = parseFloat(container) - parseFloat(headerWidth) - 3;
                    $('#GanttChartDIV').css('width', widthGantt+'px');
                    $('.dragger_container').css('width', widthGantt+'px');
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
            updateHeaderRow(gridW);

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
			 dataViewGG = dataView;
            //grid.autosizeColumns();
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
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 500,
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

        var count = 0;
        var personDefault = <?php echo json_encode($personDefault);?>;
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
                        var content = '<option value="0" ' + selected + '><?php echo  __("------- Select -------", true);?></option>';
                        if(personDefault == false){
                            content += '<option value="-1" ' + selectDefined + '><?php echo  __("------- Predefined", true);?></option>';
                        } else {
                            content += '<option value="-2" ' + selectDefault + '><?php echo  __("------- Default", true);?></option>';
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
        var checkStatus = <?php echo json_encode($checkStatus);?>;
        listProjectStautus(2, checkStatus);
        $('#CategoryStatus').change(function(){
            $('#CategoryStatus option').each(function(){
                var viewId = '';
                if($(this).is(':selected')){
                    viewId = $('#CategoryStatus').val();
                    if(viewId != 0){
                        if(viewId == -1 || viewId == -2){
                            window.location = ('/projects/index/' +viewId+ '?cate=' +2);
                        } else {
                            window.location = ('/projects/index/' +viewId+ '?cate=' +2);
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
        var heightViewPort = $(window).height() - 401;
        heightViewPort = (heightViewPort < 140) ? 140 : heightViewPort;
        $('.twoTable').css({
            height: heightViewPort
            });
        $(window).resize(function(){
            var cols = gridGG.getColumns();
            var _cols = gridGG.getColumns();
            var _numCols = cols.length;
            var _gridW = 0;
            for (var i=0; i<_numCols; i++) {
                _gridW += _cols[i].width;
            }
            if(viewGantt){
                var headerWidth = _gridW;
                var container = $('.wd-list-project').width();
                if(headerWidth >= (parseFloat(container)/2)){
                    headerWidth = parseFloat(container)/2;
                }
                $('#project_container').css('width', headerWidth+'px');
                var widthGantt = parseFloat(container) - parseFloat(headerWidth) - 3;
                $('#GanttChartDIV').css('width', widthGantt+'px');
                $('.dragger_container').css('width', widthGantt+'px');
				if($display_all_name_of_milestones == 1)
				{
					$('#MilestonesCheck').trigger('click');
					$('#MilestonesCheck').attr('checked','checked');
				}
            }

            $('#wd-header-custom').css('width', _gridW);
            _gridW += 1000;
            $('.slick-header-columns').css('width', _gridW+'px');

            var _heightViewPort = $(window).height() - 390;
            _heightViewPort = (_heightViewPort < 140) ? 140 : _heightViewPort;
            $('.twoTable').css({
                height: _heightViewPort
            });
        });
    })(jQuery);
    //center current date
    var today = new Date('<?php echo date('Y-m-d') ?>');

    var type = '<?php echo $typeGantt ?>';
    $(document).ready(function(){

        switch(type){
            case 'year':
            case 'month':
                var $col = $('#month_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
            case 'week':
                var $col = $('#week_<?php echo date('W') ?>_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
            default:
                var $col = $('#date_' + today.getDate() + '_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
        }
        if( $col.length ){
            var container = $("#mcs1_container .container");
            var dragger_container = $('.dragger_container:visible');
            var max = container.width() - dragger_container.width()/2;
            var ratio = $col.position().left / container.width();
            if( ratio > 1 )ratio = 1;
            var left = 0 - Math.round(ratio * max);
            var scroll = Math.round(ratio * (dragger_container.width() - dragger_container.children(".dragger.ui-draggable").width()));
            container.css('left', left + 'px');
            dragger_container.children(".dragger.ui-draggable").css('left', scroll + 'px');
        }
		$(window).trigger('resize');
    });
    function showPhaseDetail(e, project)
	{
	    var e = window.event || e;
        if(e.stopPropagation) {
            e.stopPropagation();
        } else {
            e.returnValue = false;
        }
		var type = <?php echo json_encode($typeGantt);?>;
		var data = 'type='+type+'&ajax=1&call=1';
		//var data = 'type='+type+'&phase='+phase;
		$.ajax({
			url: '/project_phase_plans/phase_vision/'+project+'?'+data,
			data: data,
			async: false,
			type:'POST',
			success:function(datas) {
				jQuery('.dragger_container').css({'z-index':10});
				var wh=jQuery(window).height();
				var ww=jQuery(window).width();
				jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0,'overflow':'auto','width':ww-100, 'height':wh-100, 'z-index': 99999});
				if(wh<768){
					jQuery('#dialogDetailValue').css({'overflow':'auto'});
					jQuery('#contentDialog').css({'max-height':600,'width':'auto'});
				} else {
					jQuery('#contentDialog').css({'max-height':900,'width':'auto'});
				}

				jQuery('#contentDialog').html(datas);

				jQuery('#AjaxGanttChartDIV .gantt-child').show();
				setTimeout(function(){
					showMe();
					jQuery('.gantt-line .gantt-d30').show();
					var wGantt = jQuery('#AjaxGanttChartDIV table.gantt').width();
					jQuery('#AjaxGanttChartDIV .customScrollBox').width(wGantt);
					jQuery('#AjaxGanttChartDIV .container').width(wGantt);
					jQuery('#AjaxGanttChartDIV .content').width(wGantt);
					jQuery('#ajaxScroll').width(wGantt);
                    //move scroll
                    switch(type){
                        case 'year':
                        case 'month':
                            var $tar = '#month_' + (today.getMonth() + 1) + '_' + today.getFullYear();
                        break;
                        case 'week':
                            var $tar = '#week_<?php echo date('W') ?>_' + (today.getMonth() + 1) + '_' + today.getFullYear();
                        break;
                        default:
                            var $tar = '#date_' + today.getDate() + '_' + (today.getMonth() + 1) + '_' + today.getFullYear();
                        break;
                    }
                    var target = jQuery('#dialogDetailValue').find($tar);
                    if( target.length ){
                        jQuery('#dialogDetailValue').scrollTo( target, true, null );
                    }
                    //$('#GanttChartDIV .gantt-line .gantt-msi').each(function(){
//                        var $element = $(this);
//                        $element.css('top' , '26px');
//                    });
                    $('#GanttChartDIV .gantt-line div').each(function(){
                        var checkClass = $(this).attr('class');
                        if(checkClass){
                            checkClass = checkClass.split(' ')[1];
                            if(checkClass && checkClass === 'stone-detail-show'){
                                var stoneDetail = checkClass.split(' ')[0];
                                var classRow = $(stoneDetail).parent().parent().parent().attr('class').split(' ')[1];
                                // set top stones
                                var bandwidth = $('.'+classRow).width();
                                var stack =  [], height = 16, icon = 16;
                                var maxTop = 0;
                                $(stoneDetail).children('div').each(function(){
                                    var $element = $(this);
                                    var $span = $element.find('span');
                                    var left = parseFloat($element.css('left').toString().replace('px', ''));
                                    //left = ((left * bandwidth) / 100).toFixed(2);
                                    var width = $span.width();
                                    var row = 0;

                                    if(left+width+icon >= bandwidth ){
                                        left -= (width + icon) * 2;
                                        $span.css('marginLeft' , - (width + icon ));
                                    }
                                    $(stack).each(function(k,v){
                                        if(left >= v){
                                            return false;
                                        }
                                        row++;
                                    });
                                    stack[row] = left+width+icon;
                                    var _top = 26+(row* height);
                                    if(_top >= maxTop){
                                        maxTop = _top;
                                    }
                                    $element.css('top' , _top);
                                });
                            }
                            return false;
                        }
                    });
				},100);
			}
		});
  }
    function showStonesDetails(classRow, id){
		if(checkShowFullMistones == true) return false;
        var _viewStone = <?php echo !empty($confirmGantt) && isset($confirmGantt['stones']) ? json_encode($confirmGantt['stones']) : json_encode(false);?>;
        if(!_viewStone){
            return false;
        }
        // close all project other project clicking
        var $tableGantt = $('.twoTable div.gantt-chart-wrapper table:nth-child(2)');
        $tableGantt.find('tr div.gantt-line').each(function(){
            //var totalDiv = $(this).children('div').length;
            //var ind = (totalDiv && totalDiv == 3) ? 2 : 3;
			var $i = 0;
					var ind = 0 ;
					$(this).children('div').each(function(index, element) {
						$i++;
                        var classTmp = $(this).attr('class');
						if(classTmp.indexOf("st-detail"))
						{
							ind = $i;
						}
                    });
            if($(this).children().eq(ind).hasClass('stone-detail-hide')){
                var $elem = $(this).children();
                var _classRow = $elem.parent().parent().parent().attr('class').split(' ')[1];
                if(_classRow != classRow){
                    $elem.eq(ind).removeClass('stone-detail-hide');
                    $elem.eq(ind+1).addClass('stone-detail-hide');
                    $elem.eq(ind+1).removeClass('stone-detail-show');
                    $('.'+_classRow).find('td.gantt-node div.gantt-line table tbody tr td').each(function(){
                        $(this).find('div').css('cssText', 'height: 42px !important;');
                    });
                }
            }
        });
        var stoneIndex = $('.st-index-'+id);
        var stoneDetail = $('.st-detail-'+id);
        if(stoneDetail.hasClass('stone-detail-hide')){
            // hide stone index
            stoneIndex.addClass('stone-detail-hide');
            if(stoneDetail.html() != ''){
                stoneDetail.removeClass('stone-detail-hide');
                // show stone detail
                stoneDetail.addClass('stone-detail-show');
                // set top stones
                var bandwidth = $('.'+classRow).width();
                var stack =  [], height = 16, icon = 16;
                var maxTop = 0;
                stoneDetail.children('div').each(function(){
                    var $element = $(this);
                    var $span = $element.find('span');
                    var left = parseFloat($element.css('left').toString().replace('px', ''));
                    //left = ((left * bandwidth) / 100).toFixed(2);
                    var width = $span.width();
                    var row = 0;

                    if(left+width+icon >= bandwidth ){
                        left -= (width + icon) * 2;
                        $span.css('marginLeft' , - (width + icon ));
                    }
                    $(stack).each(function(k,v){
                        if(left >= v){
                            return false;
                        }
                        row++;
                    });
                    stack[row] = left+width+icon;
                    var _top = 26+(row* height);
                    if(_top >= maxTop){
                        maxTop = _top;
                    }
                    $element.css('top' , _top);
                });
                $('.'+classRow).find('td.gantt-node div.gantt-line table tbody tr td').each(function(){
                    var _height = parseFloat(maxTop)+26;
                    $(this).find('div').css('cssText', 'height: ' + _height + 'px !important;');
                });
                // Edit height of datagridview
                // close div off gantt.
                var checkRow = 1;
                var _listTopRow = {};
                $('.slick-viewport .grid-canvas .slick-row').children('div').find('a').each(function(){
                    var isClass = $(this).attr('class').split(' ')[0];
                    var $row = $('.'+isClass).parent().parent();
                    if($row.hasClass('stone-detail-show')){
                        $row.removeClass('stone-detail-show');
                        $('.'+isClass).parent().parent().find('div.slick-cell').each(function(){
                            $(this).css('cssText', 'height: 39px !important; line-height: 42px !important;');
                            $(this).find('.wd-edit').css('cssText', 'margin-top: 10px !important;');
                            $(this).find('.wd-bt-big').css('cssText', 'margin-top: 10px !important;');
                        });
                    }
                    $row.attr('id', 'row-top-'+checkRow);
                    _listTopRow['row-top-'+checkRow] = parseFloat($row.css('top'));
                    checkRow++;
                });
                if($.isEmptyObject(listTopRow)){
                    listTopRow = _listTopRow;
                }
                // close row of datagridview
                if(listTopRow){
                    $.each(listTopRow, function(id, val){
                        $('#'+id).css('top', val + 'px');
                    });
                }
                //open div click
                var topCurrentClick = 0;
                var heightGV = 0;
                $('.slick-viewport .grid-canvas .slick-row').children('div').find('a').each(function(){
                    var isClass = $(this).attr('class').split(' ')[0];
                    var isProject = isClass ? isClass.split('-')[2] : 0;
                    if(isProject == id){
                        var $row = $('.'+isClass).parent().parent();
                        if(!$row.hasClass('stone-detail-show')){
                            var _heightGV = parseFloat(maxTop)+23;
                            var marginAction = (_heightGV/2)-10;
                            $row.addClass('stone-detail-show');
                            $('.'+isClass).parent().parent().find('div.slick-cell').each(function(){
                                $(this).css('cssText', 'height: ' + _heightGV + 'px !important; line-height: ' + _heightGV + 'px !important;');
                                $(this).find('.wd-edit').css('cssText', 'margin-top: ' + marginAction + 'px !important;');
                                $(this).find('.wd-bt-big').css('cssText', 'margin-top: ' + marginAction + 'px !important;');
                            });
                            // tim tat cac cac div co top lon hon hien tai
                            topCurrentClick = parseFloat($row.css('top'));
                            heightGV = _heightGV;
                        }
                        return false;
                    }
                });
                // edit top of row in grid
                var topOfRow = {};
                $('.slick-viewport .grid-canvas .slick-row').each(function(){
                    var _id = $(this).attr('id');
                    var _isTop = parseFloat($(this).css('top'));
                    if(_isTop > topCurrentClick){
                        topOfRow[parseFloat(_isTop)] = _id;
                    }
                });
                if(topOfRow){
                    var check = 0;
                    var nextTopModel = 0;
                    $.each(topOfRow, function(val, ind){
                        if(check == 0){
                            val = parseFloat(topCurrentClick) + parseFloat(heightGV) + 4;
                            nextTopModel = val;
                            $('#'+ind).css('top', val + 'px');
                        } else {
                            nextTopModel = nextTopModel + 43;
                            $('#'+ind).css('top', nextTopModel + 'px');
                        }
                        check++;
                    });
                }
            } else {
                $('.slick-viewport .grid-canvas .slick-row').children('div').find('a').each(function(){
                    var isClass = $(this).attr('class').split(' ')[0];
                    var $row = $('.'+isClass).parent().parent();
                    if($row.hasClass('stone-detail-show')){
                        $row.removeClass('stone-detail-show');
                        $('.'+isClass).parent().parent().find('div.slick-cell').each(function(){
                            $(this).css('cssText', 'height: 39px !important; line-height: 42px !important;');
                            $(this).find('.wd-edit').css('cssText', 'margin-top: 10px !important;');
                            $(this).find('.wd-bt-big').css('cssText', 'margin-top: 10px !important;');
                        });
                    }
                });
                // close row of datagridview
                if(listTopRow){
                    $.each(listTopRow, function(id, val){
                        $('#'+id).css('top', val + 'px');
                    });
                }
            }
        } else {
            // show stone index
            stoneIndex.removeClass('stone-detail-hide');
            stoneDetail.addClass('stone-detail-hide');
            // show stone detail
            stoneDetail.removeClass('stone-detail-show');
            $('.'+classRow).find('td.gantt-node div.gantt-line table tbody tr td').each(function(){
                $(this).find('div').css('cssText', 'height: 42px !important;');
            });
            // Edit height of datagridview
            $('.slick-viewport .grid-canvas .slick-row').children('div').find('a').each(function(){
                var isClass = $(this).attr('class').split(' ')[0];
                var isProject = isClass ? isClass.split('-')[2] : 0;
                if(isProject == id){
                    var $row = $('.'+isClass).parent().parent();
                    if($row.hasClass('stone-detail-show')){
                        $row.removeClass('stone-detail-show');
                        $('.'+isClass).parent().parent().find('div.slick-cell').each(function(){
                            $(this).css('cssText', 'height: 39px !important; line-height: 42px !important;');
                            $(this).find('.wd-edit').css('cssText', 'margin-top: 10px !important;');
                            $(this).find('.wd-bt-big').css('cssText', 'margin-top: 10px !important;');
                        });
                        var topCurrent = $row.css('top');
                        var topNext = parseFloat(topCurrent) + 43;
                        $row.next().css('top', topNext + 'px');
                    }
                    return false;
                }
            });
            // close row of datagridview
            if(listTopRow){
                $.each(listTopRow, function(id, val){
                    $('#'+id).css('top', val + 'px');
                });
            }
        }
    }
	var checkShowFullMistones = false;
    var $display_all_name_of_milestones = <?php echo isset($confirmGantt['display_all_name_of_milestones']) && $confirmGantt['display_all_name_of_milestones'] ? 1 : 0 ; ?>;
    $(document).ready(function(){
        $('#MilestonesCheck').click(function(){
			$('.twoTable').scrollTop(10);
			$('.twoTable').scrollTop(0);
            var $tableGanttPublic = $('.twoTable div.gantt-chart-wrapper table:nth-child(2)');
            if($(this).is(':checked')){
				checkShowFullMistones = true ;
                $tableGanttPublic.find('tr div.gantt-line').each(function(){
                   // var totalDiv = $(this).children('div').length;
                    //var ind = (totalDiv && totalDiv == 3) ? 2 : 3;
					var $i = 0;
					var ind = 0 ;
					$(this).children('div').each(function(index, element) {
						$i++;
                        var classTmp = $(this).attr('class');
						if(classTmp.indexOf("st-detail"))
						{
							ind = $i;
						}
                    });
                    var $elem = $(this).children();
                    $elem.eq(ind).addClass('stone-detail-hide');
                    $elem.eq(ind+1).removeClass('stone-detail-hide');
                    $elem.eq(ind+1).addClass('stone-detail-show');
                    // set top stones
                    var isClass = $elem.eq(ind+1).attr('class').split(' ')[0];
                    var classRow = $('.'+isClass).parent().parent().parent().attr('class').split(' ')[1];
                    var bandwidth = $('.'+classRow).width();
                    var stack =  [], height = 16, icon = 16;
                    var maxTop = 0;
                    var idRow = classRow.split('-')[1];
                    $elem.eq(ind+1).children('div').each(function(){
                        var $element = $(this);
                        var $span = $element.find('span');
                        var left = parseFloat($element.css('left').toString().replace('px', ''));
                        var width = $span.width();
                        var row = 0;

                        if(left+width+icon >= bandwidth ){
                            left -= (width + icon) * 2;
                            $span.css('marginLeft' , - (width + icon ));
                        }
                        $(stack).each(function(k,v){
                            if(left >= v){
                                return false;
                            }
                            row++;
                        });
                        stack[row] = left+width+icon;
                        var _top = 26+(row* height);
                        if(_top >= maxTop){
                            maxTop = _top;
                        }
                        $element.css('top' , _top);
                    });
                    $('.'+classRow).find('td.gantt-node div.gantt-line table tbody tr td').each(function(){
                        var _height = parseFloat(maxTop)+26;
                        if($elem.eq(ind).html() != '' || $elem.eq(ind+1).html() != ''){
                            $(this).find('div').css('cssText', 'height: ' + _height + 'px !important;');
                        } else {
                            $(this).find('div').css('cssText', 'height: 42px !important;');
                        }

                    });

                    var _heightGV = parseFloat(maxTop)+23;
                    var marginAction = (_heightGV/2)-10;
                    var _row = dataViewGG.getRowById(idRow);
                    var hey = 0;
                    if($elem.eq(ind).html() == '' && $elem.eq(ind+1).html() == ''){
                        hey = 39;
                        _heightGV = 42;
                        marginAction = 10;
                        //$(this).css('cssText', 'height: 39px !important; line-height: 42px !important;');
                        //$(this).find('.wd-edit').css('cssText', 'margin-top: 10px !important;');
                        //$(this).find('.wd-bt-big').css('cssText', 'margin-top: 10px !important;');
                    } else {
                        hey = (_heightGV-2);
                        //$(this).css('cssText', 'height: ' + (_heightGV-2) + 'px !important; line-height: ' + _heightGV + 'px !important;');
                        //$(this).find('.wd-edit').css('cssText', 'margin-top: ' + marginAction + 'px !important;');
                        //$(this).find('.wd-bt-big').css('cssText', 'margin-top: ' + marginAction + 'px !important;');
                    }
                    heightNewRow[_row] = {
                        'id': 'row-'+idRow,
                        'idData': idRow,
                        'row': _row,
                        'height': hey,
                        'line': _heightGV,
                        'margin': marginAction
                    };
                });
                resetGrid();
            } else {
				checkShowFullMistones = false;
                $tableGanttPublic.find('tr div.gantt-line').each(function(){
                   // var totalDiv = $(this).children('div').length;
                    //var ind = (totalDiv && totalDiv == 3) ? 2 : 3;
					var $i = 0;
					var ind = 0 ;
					$(this).children('div').each(function(index, element) {
						$i++;
                        var classTmp = $(this).attr('class');
						if(classTmp.indexOf("st-detail"))
						{
							ind = $i;
						}
                    });
                    var $elem = $(this).children();
                    $elem.eq(ind).removeClass('stone-detail-hide');
                    $elem.eq(ind+1).addClass('stone-detail-hide');
                    $elem.eq(ind+1).removeClass('stone-detail-show');
                    var isClass = $elem.eq(ind+1).attr('class').split(' ')[0];
                    var classRow = $('.'+isClass).parent().parent().parent().attr('class').split(' ')[1];
                    $('.'+classRow).find('td.gantt-node div.gantt-line table tbody tr td').each(function(){
                        $(this).find('div').css('cssText', 'height: 42px !important;');
                    });
                });
                heightNewRow = {};
            }
       });
        $('#map-icon').click(function(){
            var length = dataView.getLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(dataView.getItem(i).id);
            }
            if( !list.length )return false;
            location.href = '<?php echo $this->Html->url('/projects/map/2/') ?>' + list.join('-');
            return false;
        });
    });
	/*setTimeout(function(){
		$(".slick-viewport").height($(".twoTable").height());
	},2000);*/
    function resetGrid(){
        if(heightNewRow){
            $.each(heightNewRow, function(row, value){
                var _idRow = value.id ? value.id : 0;
                var id = value.idData ? value.idData : 0;
                var _row = dataViewGG.getRowById(id);
                var lastRow = _row - 1;
                var height = value.height ? value.height : 0;
                var line = value.line ? value.line : 0;
                var margin = value.margin ? value.margin : 0;

                $('#'+_idRow).find('div.slick-cell').css('cssText', 'height: ' + height + 'px !important; line-height: ' + line + 'px !important;');
                $('#'+_idRow).find('div.slick-cell').find('.wd-edit').css('cssText', 'margin-top: ' + margin + 'px !important;');
                $('#'+_idRow).find('div.slick-cell').find('.wd-bt-big').css('cssText', 'margin-top: ' + margin + 'px !important;');
                if(lastRow == -1 || row == 0){
                    $('#'+_idRow).css('top', '0px');
                } else {
                    var heightRowLast = heightNewRow[lastRow] && heightNewRow[lastRow].line ? heightNewRow[lastRow].line : 0;
                    var topRowLast = heightNewRow[lastRow] && heightNewRow[lastRow].id ? heightNewRow[lastRow].id : '';
                    topRowLast = topRowLast ? $('#'+topRowLast).css('top') : 0;
                    topRowLast = topRowLast ? parseFloat(topRowLast) : 0;
                    var _top = topRowLast + heightRowLast + 3;
                    $('#'+_idRow).css('top', _top + 'px');
                }
            });
        }
    }
	if($display_all_name_of_milestones == 1)
	{
		setTimeout(function(){
			$('#MilestonesCheck').trigger('click');
			$('#MilestonesCheck').attr('checked','checked');
		},2000);
	}

</script>
<?php
echo $this->Form->create('ExportVision', array('url' => array('controller' => 'project_staffings', 'action' => 'export_system'), 'type' => 'file'));
//echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
//echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
//echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
//echo $this->Form->hidden('rows', array('value' => $rows));
//echo $this->Form->hidden('start', array('value' => $start));
//echo $this->Form->hidden('end', array('value' => $end));
echo $this->Form->hidden('showGantt');
echo $this->Form->hidden('showType');
echo $this->Form->hidden('summary');
echo $this->Form->hidden('program');
echo $this->Form->hidden('sub_program');
echo $this->Form->hidden('manager');
echo $this->Form->hidden('status');
echo $this->Form->hidden('profit_center');
echo $this->Form->hidden('function');
//echo $this->Form->hidden('conditions', array('value' => serialize($conditions)));
//echo $this->Form->hidden('projectId', array('value' => serialize($projectId)));
//echo $this->Form->hidden('months', array('value' => serialize($this->GanttVs->getMonths())));
//echo $this->Form->hidden('displayFields', array('value' => '0'));
echo $this->Form->end();
?>
