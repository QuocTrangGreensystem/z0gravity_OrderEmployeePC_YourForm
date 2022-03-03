<?php
echo $this->Html->css(array(
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'gantt_v2_1',
    '/js/qtip/jquery.qtip',
    'preview/grid-project',
    'preview/projects',
    'preview/slickgrid',
));
echo $this->Html->script(array(
    'jquery.form',
    'jquery.multiSelect',
    'jquery.scrollTo',
    'history_filter',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/slick.editors',
    'slick_grid_custom',
    'qtip/jquery.qtip',
    'slick_grid/plugins/slick.dataexporter',
    'draw-progress'
    //'responsive_table.js'
));
echo $html->script(array(
    // 'jquery.multiSelect',
    'jshashtable-2.1',
    // 'tinymce/tinymce.min',
));
echo $this->element('dialog_detail_value');
echo $this->element('dialog_projects');
if($viewGantt){
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity'
    ));
    echo $this->Html->script(array(
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity'
    ));
} else {
    echo $this->Html->css(array(
        'slick_grid/slick.grid',
    ));
    echo $this->Html->script(array(
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.grid'
    ));
}
$cates = array(
    1 => __("In progress", true),
    2 => __("Opportunity", true),
    3 => __("Archived", true),
    4 => __("Model", true)
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$unit = !empty($employee['Company']['unit']) ? $employee['Company']['unit'] : 'M.D';
$i18n = array(
	'-- Any --' => __('-- Any --', true),
    'M.D' => __($unit, true),
	'minute' => __('cmMinute', true),
	'hour' => __('cmHour', true),
	'day' => __('cmDay', true),
	'month' => __('cmMonth', true),
	'year' => __('cmYear', true),
	'minutes' => __('cmMinutes', true),
	'hours' => __('cmHours', true),
	'days' => __('cmDays', true),
	'months' => __('cmMonths', true),
	'years' => __('cmYears', true),
	'startday' => __('Start date', true),
	'enddate' => __('End date', true),
	'progress' => __('Progress', true),
	'resource' => __('Resource', true),
	'date' => __('Date', true),
	'sun' => __('Sun', true),
	'cloud' => __('Cloud', true),
	'rain' => __('Rain', true),
	'fair' => __('Fair', true),
	'furry' => __('Furry', true),
	'mid' => __('Mid', true),
	'up' => __('Up', true),
	'down' => __('Down', true),
);
$viewManDay = __($unit, true);

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
$cates = array(1 => __("In progress", true), 2 => __("Opportunity", true), 3 => __("Archived", true), 4 => __("Model", true));
$columnAlignRight = array(
    'ProjectBudgetSyn.total_costs_var', 'ProjectBudgetSyn.internal_costs_var', 'ProjectBudgetSyn.external_costs_var',
    'ProjectBudgetSyn.external_costs_progress', 'ProjectBudgetSyn.assign_to_profit_center', 'ProjectBudgetSyn.assign_to_employee',
    'ProjectAmr.budget', 'ProjectAmr.project_amr_cost_control_id',
    'ProjectAmr.project_amr_organization_id', 'ProjectAmr.project_amr_plan_id', 'ProjectAmr.project_amr_perimeter_id',
    'ProjectAmr.project_amr_risk_control_id', 'ProjectAmr.project_amr_problem_control_id',
    'Project.created_value', 'Project.budget', 'ProjectBudgetSyn.ManualConsumed', 'ProjectBudgetSyn.InUsed',
    'Project.number_1', 'Project.number_2', 'Project.number_3', 'Project.number_4', 'Project.number_5', 'Project.number_6', 'Project.number_7', 'Project.number_8', 'Project.number_9',
    'Project.number_10', 'Project.number_11', 'Project.number_12', 'Project.number_13', 'Project.number_14', 'Project.number_15', 'Project.number_16', 'Project.number_17', 'Project.number_18'
);
$columnAlignRight = array_merge($columnAlignRight, $financeFields);
$columnAlignRightAndEuro = array(
    'ProjectBudgetSyn.sales_sold', 'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid',
    'ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid',
    'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast', 'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain',
    'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
    'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.external_costs_budget', 'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered',
    'ProjectBudgetSyn.external_costs_remain', 'ProjectBudgetSyn.external_costs_progress_euro', 'ProjectBudgetSyn.internal_costs_average',
    'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
    'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget',
    'Project.price_1', 'Project.price_2', 'Project.price_3', 'Project.price_4', 'Project.price_5', 'Project.price_6', 'Project.price_7', 'Project.price_8', 'Project.price_9', 'Project.price_10',
    'Project.price_11', 'Project.price_12', 'Project.price_13', 'Project.price_14', 'Project.price_15', 'Project.price_16', 'ProjectBudgetSyn.Workload€', 'ProjectBudgetSyn.Consumed€', 'ProjectBudgetSyn.Remain€', 'ProjectBudgetSyn.Amount€', 'ProjectBudgetSyn.Estimated€',
    'ProjectBudgetSyn.UnitPrice', 'ProjectBudgetSyn.%progressorder€',
);
$columnAlignRightAndEuro = array_merge($columnAlignRightAndEuro, $finEuros, $finTwoPlusEuros);
$columnAlignRightAndManDay = array(
    'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectBudgetSyn.internal_costs_engaged_md', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
    'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_man_day', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_man_day', 'ProjectAmr.delay',
    'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload', 'ProjectBudgetSyn.ManualConsumed', 'ProjectBudgetSyn.InUsed',
    'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y', 'ProjectBudgetSyn.consumed',
    'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y',
    'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y', 'ProjectBudgetSyn.Initialworkload', 'ProjectBudgetSyn.Remain', 'ProjectBudgetSyn.Consumed', 'ProjectBudgetSyn.Workload', 'ProjectBudgetSyn.Overload',
    'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
    'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y'
);
$columnAlignRightAndPercent = array('ProjectBudgetSyn.roi', 'ProjectBudgetSyn.Completed', 'ProjectBudgetSyn.%progressorder');
$columnAlignRightAndPercent = array_merge($columnAlignRightAndPercent, $finPercents, $finTwoPlusPercent);
$columnCalculationConsumeds = array(
    'ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid',
    'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectBudgetSyn.internal_costs_engaged_md', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
    'ProjectAmr.engaged','ProjectAmr.variance','ProjectAmr.forecasted','ProjectBudgetSyn.sales_sold', 'ProjectBudgetSyn.Workload€', 'ProjectBudgetSyn.Consumed€', 'ProjectBudgetSyn.Remain€', 'ProjectBudgetSyn.Amount€', 'ProjectBudgetSyn.Estimated€',
    'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid', 'ProjectBudgetSyn.Consumed', 'ProjectBudgetSyn.Remain', 'ProjectBudgetSyn.Workload', 'ProjectBudgetSyn.Initialworkload', 'ProjectBudgetSyn.UnitPrice', 'ProjectBudgetSyn.Overload',
    'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast', 'ProjectBudgetSyn.ManualConsumed', 'ProjectBudgetSyn.InUsed', 'ProjectBudgetSyn.%progressorder€',
    'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain', 'ProjectBudgetSyn.total_costs_man_day',
    'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
    'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_budget',
    'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered', 'ProjectBudgetSyn.external_costs_remain',
    'ProjectBudgetSyn.external_costs_man_day', 'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.internal_costs_average',
    'ProjectAmr.delay', 'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
    'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget', 'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y',
    'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y',
    'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y', 'ProjectBudgetSyn.consumed',
    'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload',
    'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
    'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y',
    'Project.price_1', 'Project.price_2', 'Project.price_3', 'Project.price_4', 'Project.price_5', 'Project.price_6', 'Project.price_7', 'Project.price_8', 'Project.price_9', 'Project.price_10',
    'Project.price_11', 'Project.price_12', 'Project.price_13', 'Project.price_14', 'Project.price_15', 'Project.price_16',
    'Project.number_1', 'Project.number_2', 'Project.number_3', 'Project.number_4', 'Project.number_5', 'Project.number_6', 'Project.number_7', 'Project.number_8', 'Project.number_9', 'Project.number_10',
    'Project.number_11', 'Project.number_12', 'Project.number_13', 'Project.number_14', 'Project.number_15', 'Project.number_16', 'Project.number_17', 'Project.number_18'
);
$columnCalculationConsumeds = array_merge($columnCalculationConsumeds, $finEuros, $finTwoPlusEuros);
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
    'provisional_budget_md' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . __($unit, true),
    'provisional_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . date('Y', time()),
    'provisional_last_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 1),
    'provisional_last_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 2),
    'provisional_last_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 3),
    'provisional_next_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 1),
    'provisional_next_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 2),
    'provisional_next_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 3)
);
$columnNoFilters = array(
    'no.', 'ProjectAmr.customer_point_of_view', 'action.'
);
$columnSelected = array(
    'Project.project_manager_id', 'Project.project_phase_id', 'Project.project_priority_id', 'Project.project_status_id', 'Project.project_type_id',
    'Project.project_sub_type_id', 'Project.project_amr_program_id', 'Project.project_amr_sub_program_id', 'Project.list_1', 'Project.list_2',
    'Project.list_3', 'Project.list_4', 'Project.list_5', 'Project.list_6', 'Project.list_7', 'Project.list_8', 'Project.list_9', 'Project.list_10',
    'Project.list_11', 'Project.list_12', 'Project.list_13', 'Project.list_14', 'Project.bool_1', 'Project.bool_2', 'Project.bool_3', 'Project.bool_4', 'Project.list_muti_1', 'Project.list_muti_2',
    'Project.list_muti_3', 'Project.list_muti_4', 'Project.list_muti_5', 'Project.list_muti_6', 'Project.list_muti_7', 'Project.list_muti_8', 'Project.list_muti_9', 'Project.list_muti_10', 'ProjectAmr.weather', 'ProjectAmr.cost_control_weather', 'ProjectAmr.planning_weather', 'ProjectAmr.risk_control_weather', 'ProjectAmr.organization_weather', 'ProjectAmr.issue_control_weather', 'ProjectAmr.rank', 'ProjectAmr.resources_weather'
);
$columnNotCalculationConsumed = array(
    'no.', 'Project.project_name', 'Project.long_project_name', 'Project.project_code_1', 'Project.project_code_2', 'Project.project_manager_id',
    'Project.technical_manager_id', 'Project.project_phase_id', 'Project.project_priority_id', 'Project.project_status_id', 'Project.start_date',
    'Project.end_date', 'Project.primary_objectives', 'Project.project_objectives', 'Project.issues', 'Project.constraint', 'Project.remark', 'Project.company_id',
    'Project.project_type_id', 'Project.project_sub_type_id', 'Project.chief_business_id', 'Project.project_amr_program_id', 'Project.project_amr_sub_program_id',
    'Project.copy_number', 'Project.complexity_id', 'Project.created_value', 'Project.activity_id', 'Project.created', 'Project.category', 'Project.update_by_employee',
    'Project.project_copy', 'Project.project_copy_id', 'Project.budget_customer_id', 'Project.is_freeze', 'Project.freeze_by', 'Project.freeze_time', 'Project.off_freeze',
    'Project.last_modified', 'Project.free_1', 'Project.free_2', 'Project.free_3', 'Project.free_4', 'Project.free_5', 'Project.functional_leader_id', 'Project.uat_manager_id', 'Project.address',
    'Project.date_1', 'Project.date_2', 'Project.date_3', 'Project.date_4', 'Project.date_5', 'Project.date_6', 'Project.date_7', 'Project.date_8', 'Project.date_9', 'Project.date_10', 'Project.date_11', 'Project.date_12', 'Project.date_13', 'Project.date_14',
    'Project.list_1', 'Project.list_2', 'Project.list_3', 'Project.list_4', 'Project.list_5', 'Project.list_6', 'Project.list_7', 'Project.list_8', 'Project.list_9', 'Project.list_10', 'Project.list_11', 'Project.list_12', 'Project.list_13', 'Project.list_14',
    'Project.yn_1', 'Project.yn_2', 'Project.yn_3', 'Project.yn_4', 'Project.yn_5', 'Project.yn_6', 'Project.yn_7', 'Project.yn_8', 'Project.yn_9',
    'Project.date_mm_yy_1', 'Project.date_mm_yy_2', 'Project.date_mm_yy_3', 'Project.date_mm_yy_4', 'Project.date_mm_yy_5', 'Project.date_yy_1', 'Project.date_yy_2', 'Project.date_yy_3', 'Project.date_yy_4', 'Project.date_yy_5',
    'Project.bool_1', 'Project.bool_2', 'Project.bool_3', 'Project.bool_4', 'Project.activated', 'Project.team',
    'ProjectAmr.budget', 'ProjectAmr.project_amr_progression', 'ProjectAmr.project_amr_risk_information', 'ProjectAmr.project_amr_problem_information',
    'ProjectAmr.project_amr_solution', 'ProjectAmr.project_amr_solution_description', 'ProjectAmr.created', 'ProjectAmr.updated', 'ProjectAmr.rank',
    'ProjectAmr.weather', 'ProjectAmr.cost_control_weather', 'ProjectAmr.planning_weather', 'ProjectAmr.risk_control_weather','ProjectAmr.resources_weather',
    'ProjectAmr.organization_weather', 'ProjectAmr.issue_control_weather', 'ProjectAmr.customer_point_of_view', 'ProjectAmr.done', 'ProjectAmr.todo', 'ProjectAmr.comment',
    'ProjectBudgetSyn.internal_costs_var', 'ProjectBudgetSyn.external_costs_var', 'ProjectBudgetSyn.external_costs_progress', 'ProjectBudgetSyn.total_costs_var',
    'ProjectBudgetSyn.assign_to_profit_center', 'ProjectBudgetSyn.assign_to_employee', 'ProjectBudgetSyn.roi', 'ProjectBudgetSyn.Completed', 'ProjectBudgetSyn.%progressorder', 'action.', 'Project.list_muti_1', 'Project.list_muti_2',
    'Project.list_muti_3', 'Project.list_muti_4', 'Project.list_muti_5', 'Project.list_muti_6', 'Project.list_muti_7', 'Project.list_muti_8', 'Project.list_muti_9', 'Project.list_muti_10', 'Project.next_milestone_in_day', 'Project.next_milestone_in_week',
);
$columnNotCalculationConsumed = array_merge($columnNotCalculationConsumed, $finPercents, $finTwoPlusPercent);
?>
<style>
    .slick-header .slick-header-column{
        padding: 10px 5px !important;
        border-right: 1px solid #fff !important;
    }
    .slick-pane-top {
        top: 69px !important;
    }
    .slick-pane-right .slick-cell,
    .slick-pane-right .slick-headerrow-column {
        border-right-color: #aaa;
        border-left: 0;
    }
    .slick-header-columns-right .slick-header-column:nth-child(2n+1){
        background: rgba(255,255,255,0.1) !important;
    }
    #show_count_task{
        text-align: center;
        font-size: 18px;
    }
    .ok-new-project, .cancel-new-project{
        width: 90px;
        border: none;
        height: 27px;
        border-radius: 5px;
    }
    .ok-new-project:hover, .cancel-new-project:hover{
        cursor: pointer;
    }
    #btnNoAL{
        margin-left: 190px;
    }
    #modal_dialog_alert{
        min-height: 60px !important;
    }
    #modal_dialog_confirm{
        min-height: 40px !important;
        margin-left: 50px;
    }
    #dialogDetailValue{
        min-width: 200px !important;
        top: 20%;
        left: 20%;
        /*max-width: 1400px;
        max-height: 600px;*/
        overflow: hidden !important;
    }
    #contentDialog img{
        max-width: 100%;
        max-height: 600px;
    }
    .wd-tab .wd-panel{
        padding: 0;
    }
    .btn-grid{
        width: 32px;
        height: 32px;
        line-height: 37px;
        text-align: center;
        font-size: 20px;
        color: #424242;
    }
    .btn-grid:hover{
        text-decoration: none;
    }
    body {
        overflow: hidden;
    }
    #layout{
        min-height: 740px;
    }
    .wd-title{
        margin-top: 10px;
    }
    #wd-container-main .wd-layout{
        padding: 0;
        margin-left: 20px;
    }
    #wd-container-footer{
        display: none;
    }
    #dialogDetailValue:hover{
        cursor: move;
    }
    #wd-fragment-2:hover{
        cursor:auto;
    }
    .wd-table .slick-headerrow-column{
        background:  transparent;
    }
    #template_logs #content_comment .log-progress{
		height: auto;
	}
	#template_logs #content_comment #update-comment{
		border: 1px solid #E1E6E8;  
		background-color: #FFFFFF;  
		box-shadow: 0 0 10px 1px rgba(29,29,27,0.06); 
		width: calc( 100% - 20px ); 
		padding: 10px;
		resize: none;
		max-height: 150px;
		height: 98px;
	}
	#template_logs #content_comment .comment{
		margin-bottom: 15px;
	}
	#template_logs #content_comment{
		height: 100%;
		margin-top: -14px;
		position: relative;
		padding-bottom: 14px;
	}
	#template_logs .content-logs{
		height: calc( 100% - 200px);
		overflow-y: auto;
	}
	#content_comment.loading:before{
		content: '';
		position: absolute;
		width: 100%;
		height: 100%;
		background-color: rgba(255, 255, 255, 0.9);
		left: 0;
		top: 0;
		z-index: 2;
	}
	#content_comment.loading:after{
		content: '';
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
		background: url(/img/business/wait-1.gif) no-repeat center center;
		z-index: 3;
		display: block;
		background-size: cover;
		width: 50px;
		height: 50px;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<?php
$words = $this->requestAction('/translations/getByPage', array('pass' => array('KPI')));
// ob_clean(); 
foreach ($fieldset as $_fieldset) {
    if (!empty($noProjectManager) && $_fieldset['key'] == 'Project.project_manager_id') {
        continue;
    }
    $financeFieldsKey = array_keys($financeFields);
    if(in_array($_fieldset['key'],$financeFieldsKey)){
        $fieldName = __($financeFields[$_fieldset['key']], true);
        $ff = explode('.', $_fieldset['key']);
        if($ff[0] == 'ProjectFinancePlus'){
            $saveFieldName = explode(' ', $fieldName);
            if(is_numeric($saveFieldName[2])){
                $fieldName = $saveFieldName[0] . ' ' . $saveFieldName[1] . ' (Y)';
                $fieldName = __d(sprintf($_domain, 'Finance'), $fieldName, true);
                $fieldName = str_replace('(Y)', $saveFieldName[2], $fieldName);
            } else {
                $fieldName = $fieldName;
                $fieldName = __d(sprintf($_domain, 'Finance'), $fieldName, true);
            }
        } else if($ff[0] == 'ProjectFinance'){
            $fieldName = __d(sprintf($_domain, 'Finance'), $fieldName, true);
        } else if($ff[0] == 'ProjectFinanceTwoPlus'){
            $saveFieldName = explode(' (', $fieldName);
            if(!empty($saveFieldName[1])) $saveFieldName[1] = str_replace(')', '', $saveFieldName[1]);
            if( !empty($saveFieldName[1]) && is_numeric($saveFieldName[1]) ){
                $fieldName = __d(sprintf($_domain, 'Finance_2'), $saveFieldName[0], true) . ' ' . $saveFieldName[1];
            } else {
                $fieldName = __d(sprintf($_domain, 'Finance_2'), $fieldName, true);
            }
        }
    } else if( strpos($_fieldset['key'], 'Project.') !== false ){
        // debug($_fieldset['key']);
        if($_fieldset['key'] == 'Project.category'){
            $fieldName = __($_fieldset['name'], true);
        } else if($_fieldset['key'] == 'Project.next_milestone_in_day'){
            $fieldName = __d(sprintf($_domain, 'Details'), 'Next milestone in day', true);
        } else if($_fieldset['key'] == 'Project.next_milestone_in_week'){
            $fieldName = __d(sprintf($_domain, 'Details'), 'Next milestone in week', true);
        } else {
            $fieldName = substr($_fieldset['name'], 0, 1) == '*' ? __(substr($_fieldset['name'], 1), true) : __d(sprintf($_domain, 'Details'), $_fieldset['name'], true);
            if( substr($_fieldset['key'], 0, 18)  == 'Project.list_muti_'){
                $k = str_replace('List Muti ', '', $_fieldset['name']);
                $fieldName = __d(sprintf($_domain, 'Details'), 'List(multiselect) ' . $k, true);
            }
        }
    } else if( strpos($_fieldset['key'], 'ProjectAmr.') !== false && in_array($_fieldset['name'], $words) ){
        $fieldName = __d(sprintf($_domain, 'KPI'), $_fieldset['name'], true);
        if($_fieldset['key'] == 'ProjectAmr.project_amr_solution'){
            $fieldName = __d(sprintf($_domain, 'KPI'), 'Comment', true);
        }
    } else if(in_array($_fieldset['key'], array('ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid'))){
        if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_sold'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'Sold €', true);
        } else if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_to_bill'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'To Bill €', true);
        } else if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_billed'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'Billed €', true);
        } else if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_paid'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'Paid €', true);
        }
    }
    // get fieldName for Project Task.
    else if( $_fieldset['key'] == 'ProjectAmr.manual_consumed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), $_fieldset['name'], true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Workload€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Workload €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Consumed€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Consumed €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Remain€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Remain €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Amount€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Amount €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Estimated€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Estimated €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Initialworkload' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Initial workload', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.UnitPrice' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Unit Price', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Overload' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Overload', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.ManualConsumed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Completed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Completed', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.InUsed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'In Used', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.%progressorder' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), '% progress order', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.%progressorder€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), '% progress order €', true);
    } else if( in_array($_fieldset['key'], array('ProjectBudgetSyn.Consumed', 'ProjectBudgetSyn.Remain', 'ProjectBudgetSyn.Workload')) ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'),  $_fieldset['name'], true);
    } else {
        $fieldName = __($_fieldset['name'], true);
        $ff = explode('.', $_fieldset['key']);
        if( substr($ff[1], 0, 5) == 'sales' ){
            $fieldName = __d(sprintf($_domain, 'Sales'), $_fieldset['name'], true);
        } else if ( substr($ff[1], 0, 8) == 'internal' ) {
            if($_fieldset['key'] == 'ProjectBudgetSyn.internal_costs_engaged_md'){
                $_fieldset['name'] = 'Engaged M.D';
            }
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
            $_column['formatter'] = 'Slick.Formatters.linkFormatter';
            $_column['width'] = 150;
            break;
        case 'Project.project_manager_id':
            $_column['formatter'] = 'Slick.Formatters.avaResource';
            $_column['cssClass'] = 'text-hover-show';
            break;
        case 'Project.freeze_time':
        case 'Project.start_date':
        case 'Project.planed_end_date':
        case 'Project.end_date':
        case 'ProjectAmr.created':
        case 'Project.created':
        case 'Project.date_1':
        case 'Project.date_2':
        case 'Project.date_3':
        case 'Project.date_4':
        case 'Project.date_5':
        case 'Project.date_6':
        case 'Project.date_7':
        case 'Project.date_8':
        case 'Project.date_9':
        case 'Project.date_10':
        case 'Project.date_11':
        case 'Project.date_12':
        case 'Project.date_13':
        case 'Project.date_14':
            $_column['datatype'] = 'datetime';
            break;
        case 'ProjectAmr.rank':
        case 'ProjectAmr.cost_control_weather':
        case 'ProjectAmr.planning_weather':
        case 'ProjectAmr.risk_control_weather':
        case 'ProjectAmr.organization_weather':
        case 'ProjectAmr.perimeter_weather':
        case 'ProjectAmr.issue_control_weather':
        case 'ProjectAmr.customer_point_of_view':
            $_column['width'] = 200;
            $_column['isImage'] = true;
            $_column['formatter'] = 'Slick.Formatters.ImageData';
            $_column['sorter'] = 'weatherSorter';
            break;
        case 'ProjectAmr.weather':
            $_column['width'] = 75;
            $_column['isImage'] = true;
            $_column['formatter'] = 'Slick.Formatters.ImageData';
            $_column['sorter'] = 'weatherSorter';
            break;
		case 'ProjectAmr.budget_weather':
		case 'ProjectAmr.resources_weather':
        case 'ProjectAmr.scope_weather':
        case 'ProjectAmr.schedule_weather':
        case 'ProjectAmr.resource_weather':
        case 'ProjectAmr.technical_weather':
            $_column['width'] = 75;
            $_column['isImage'] = true;
            $_column['formatter'] = 'Slick.Formatters.ImageDataNew';
            $_column['sorter'] = 'weatherSorter';
            break;
        case 'ProjectAmr.comment':
        case 'ProjectAmr.project_amr_risk_information':
        case 'ProjectAmr.project_amr_problem_information':
        case 'ProjectAmr.done':
        case 'ProjectAmr.todo':
        case 'ProjectAmr.project_amr_budget_comment':
        case 'ProjectAmr.project_amr_scope':
        case 'ProjectAmr.project_amr_schedule':
        case 'ProjectAmr.project_amr_resource':
        case 'ProjectAmr.project_amr_technical':
        case 'ProjectAmr.project_amr_solution':
            $_column['editor'] = 'Slick.Editors.text';
            $_column['formatter'] = 'Slick.Formatters.text';
            break;
        case 'Project.project_amr_progression':
        case 'Project.md_forecasted':
        case 'Project.md_validated':
        case 'Project.md_engaged':
        case 'Project.md_variance':
        case 'ProjectAmr.project_amr_progression':
        case 'ProjectAmr.md_validated':
        case 'ProjectAmr.md_engaged':
        case 'ProjectAmr.internal_costs_engaged_md':
        case 'ProjectAmr.md_variance':
        case 'ProjectAmr.md_forecasted':
        case 'ProjectAmr.validated':
        case 'ProjectAmr.engaged':
        case 'ProjectAmr.forecasted':
        case 'ProjectAmr.variance':
        case 'ProjectAmr.manual_consumed':
        case 'ProjectBudgetSyn.Workload€':
        case 'ProjectBudgetSyn.Consumed€':
        case 'ProjectBudgetSyn.Remain€':
        case 'ProjectBudgetSyn.Amount€':
        case 'ProjectBudgetSyn.Estimated€':
        case 'ProjectBudgetSyn.Remain':
        case 'ProjectBudgetSyn.Workload':
        case 'ProjectBudgetSyn.Initialworkload':
        case 'ProjectBudgetSyn.UnitPrice':
        case 'ProjectBudgetSyn.%progressorder€':
        case 'ProjectBudgetSyn.Consumed':
            $_column['formatter'] = 'Slick.Formatters.floatFormatter';
            break;
        case 'Project.is_freeze':
        case 'Project.is_staffing':
        case 'Project.off_freeze':
        case 'Project.project_copy':
            $_column['formatter'] = 'Slick.Formatters.yesNoFormatter';
            break;
        case 'Project.project_priority_id':
            $_column['isSelected'] = true;
            $_column['formatter'] = 'Slick.Formatters.selectFormatter';
            $_column['width'] = 120;
            break;
        case 'Project.project_type_id':
        case 'Project.project_sub_type_id':
        case 'Project.project_amr_program_id':
        case 'Project.project_amr_sub_program_id':
            $_column['isSelected'] = true;
            $_column['formatter'] = 'Slick.Formatters.selectBoxFormatter';
            $_column['width'] = 150;
            break;
        case 'Project.next_milestone_in_day':
        case 'Project.next_milestone_in_week':
            $_column['formatter'] = 'Slick.Formatters.nextMilestone';
            break;
    }
    if(in_array($_fieldset['key'], $columnAlignRight)){
        $_column['formatter'] = 'Slick.Formatters.numberVal';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndEuro)){
        $_column['formatter'] = 'Slick.Formatters.numberValEuro';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndManDay)){
        $_column['formatter'] = 'Slick.Formatters.numberValManDay';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndPercent)){
        $_column['formatter'] = 'Slick.Formatters.numberValPercent';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnNoFilters)){
        $_column['noFilter'] = 1;
    }
    $columns[] = $_column;
}

// debug($projects);
// exit;
$i = 1;
$dataView = array();
$selects = array(
    'Project.yn_1' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_2' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_3' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_4' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_5' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_6' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_7' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_8' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_9' => array('yes' => __('Yes', true), 'no' => __('No', true))
);
$projectManagers = array();
$eindex = 0;
$totalHeaders = array();
$notAdminPm =  $employee['Role']['name'] == 'conslt';

$exception = array('Project.complexity_id');
$gantts = $stones = array();
$ganttStart = $ganttEnd = 0;

foreach ($projects as $project) {
    $ProjectEmployeeManager = array();
    if(!empty($project['ProjectEmployeeManager'])) $ProjectEmployeeManager = $project['ProjectEmployeeManager'];
    // ob_clean();
    // debug($projects);
    // exit;
    $pEM = array();

    if(!empty($ProjectEmployeeManager)){
        foreach ($ProjectEmployeeManager as $key => $value) {
            $pEM[] = $value['project_manager_id'];
        }
    }
    $data = array(
        'id' => $project['Project']['id'],
        'no.' => $i++,
        'DataSet' => array(
            'tm' => $project['Project']['technical_manager_id'],
            'cb' => $project['Project']['chief_business_id'],
            'pm' =>  $project['Project']['project_manager_id'],
            'pEM' => $pEM,
        )
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
    if(isset($project['ProjectAmr'])){
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
    $project['ProjectBudgetSyn'][0]['internal_costs_engaged_md'] = $consumed;
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
    $totalInter = !empty($internalBudgets[$projectID]) ? $internalBudgets[$projectID] : 0;
    $project['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] = $totalInter;
    $sold = !empty($budgetSyns[$projectID]['sales_sold']) ? $budgetSyns[$projectID]['sales_sold'] : 0;
    $totalIE = $totalExter + $totalInter;
    $totalB = $externalBudget+$internalBudget;
    $project['ProjectBudgetSyn'][0]['roi'] = ($totalB != 0) ? round((($sold-$totalB)/$totalB)*100, 2) : 0;
    $project['Project']['project_copy_id'] = isset($project['Project']['project_copy_id']) && isset($projectCopies[$project['Project']['project_copy_id']]) ? $projectCopies[ $project['Project']['project_copy_id'] ] : '';
    $project['Project']['freeze_by'] = isset($project['Project']['freeze_by']) && isset($freezers[$project['Project']['freeze_by']]) ? $freezers[ $project['Project']['freeze_by'] ] : '';

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
    if(!empty($finances[$projectID])){
        $totalBudgetInv = $totalAvanInv = $totalBudgetFon = $totalAvanFon = 0;
        $percentYearInvs = $percentYearFons = array();
        foreach($finances[$projectID] as $key => $fins){
            $project['ProjectFinancePlus'][0][$key] = $fins;
            if(!empty($key)){
                $key = explode('_', $key);
                if(!empty($key[0]) && $key[0] == 'inv'){
                    if(!isset($percentYearInvs[$key[2]][$key[1]])){
                        $percentYearInvs[$key[2]][$key[1]] = 0;
                    }
                    $percentYearInvs[$key[2]][$key[1]] += $fins;
                    if(!empty($key[1]) && $key[1] == 'budget'){
                        $totalBudgetInv += $fins;
                    } else {
                        $totalAvanInv += $fins;
                    }
                } else {
                    if(!isset($percentYearFons[$key[2]][$key[1]])){
                        $percentYearFons[$key[2]][$key[1]] = 0;
                    }
                    $percentYearFons[$key[2]][$key[1]] += $fins;
                    if(!empty($key[1]) && $key[1] == 'budget'){
                        $totalBudgetFon += $fins;
                    } else {
                        $totalAvanFon += $fins;
                    }
                }
            }
        }
        if(!empty($percentYearInvs)){
            foreach($percentYearInvs as $year => $percentYearInv){
                $bud = !empty($percentYearInv['budget']) ? $percentYearInv['budget'] : 0;
                $ava = !empty($percentYearInv['avancement']) ? $percentYearInv['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                if($per > 100){
                    $per = 100;
                } elseif($per < 0) {
                    $per = 0;
                }
                $project['ProjectFinancePlus'][0]['inv_percent_'. $year] = $per;
            }
        }
        if(!empty($percentYearFons)){
            foreach($percentYearFons as $year => $percentYearFon){
                $bud = !empty($percentYearFon['budget']) ? $percentYearFon['budget'] : 0;
                $ava = !empty($percentYearFon['avancement']) ? $percentYearFon['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                if($per > 100){
                    $per = 100;
                } elseif($per < 0) {
                    $per = 0;
                }
                $project['ProjectFinancePlus'][0]['fon_percent_'. $year] = $per;
            }
        }
        $totalPercentInv = ($totalBudgetInv == 0) ? 0 : $totalAvanInv/$totalBudgetInv*100;
        if($totalPercentInv > 100){
            $totalPercentInv = 100;
        } elseif($totalPercentInv < 0) {
            $totalPercentInv = 0;
        }
        $totalPercentFon = ($totalBudgetFon == 0) ? 0 : $totalAvanFon/$totalBudgetFon*100;
        if($totalPercentFon > 100){
            $totalPercentFon = 100;
        } elseif($totalPercentFon < 0) {
            $totalPercentFon = 0;
        }
        $project['ProjectFinancePlus'][0]['inv_budget'] = !empty($totalBudgetInv) ? $totalBudgetInv : '';
        $project['ProjectFinancePlus'][0]['inv_avancement'] = !empty($totalAvanInv) ? $totalAvanInv : '';
        $project['ProjectFinancePlus'][0]['inv_percent'] = !empty($totalPercentInv) ? $totalPercentInv : '';
        $project['ProjectFinancePlus'][0]['fon_budget'] = !empty($totalBudgetFon) ? $totalBudgetFon : '';
        $project['ProjectFinancePlus'][0]['fon_avancement'] = !empty($totalAvanFon) ? $totalAvanFon : '';
        $project['ProjectFinancePlus'][0]['fon_percent'] = !empty($totalPercentFon) ? $totalPercentFon : '';
    }
    if(!empty($financesTwoPlus[$projectID])){
        foreach ($financesTwoPlus[$projectID] as $key => $value) {
            $project['ProjectFinanceTwoPlus'][0][$key] = $value;
        }
        $project['ProjectFinanceTwoPlus'][0]['last_estimated'] = !empty($project['ProjectFinanceTwoPlus'][0]['last_estimated']) ? $project['ProjectFinanceTwoPlus'][0]['last_estimated'] : 0;
        $project['ProjectFinanceTwoPlus'][0]['budget_revised'] = !empty($project['ProjectFinanceTwoPlus'][0]['budget_revised']) ? $project['ProjectFinanceTwoPlus'][0]['budget_revised'] : 0;
        // dr-de.
        $project['ProjectFinanceTwoPlus'][0]['dr_de'] = $project['ProjectFinanceTwoPlus'][0]['last_estimated'] - $project['ProjectFinanceTwoPlus'][0]['budget_revised'];
        $project['ProjectFinanceTwoPlus'][0]['percent'] = $project['ProjectFinanceTwoPlus'][0]['budget_revised'] != 0 ? $project['ProjectFinanceTwoPlus'][0]['last_estimated']/$project['ProjectFinanceTwoPlus'][0]['budget_revised']*100 : 0;
        $_startFinanceTwoPlus = $startFinanceTwoPlus;
        while ($_startFinanceTwoPlus <= $endFinanceTwoPlus) {
            $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus] = !empty($project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus]) ? $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus] : 0;
            $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus] = !empty($project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus]) ? $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus] : 0;
            // dr-de & dr/de.
            $project['ProjectFinanceTwoPlus'][0]['dr_de_' . $_startFinanceTwoPlus] = $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus] - $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus];
            $project['ProjectFinanceTwoPlus'][0]['percent_' . $_startFinanceTwoPlus] = $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus] != 0 ? $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus]/$project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus]*100 : 0;
            $_startFinanceTwoPlus++;
        }
    }
    foreach ($fieldset as $_fieldset) {
        if (is_array($_fieldset['path'])) {
            $_outputName = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
            if(in_array($_fieldset['key'], $columnSelected)){
                $_key = explode('.', $_fieldset['key']);
                $_output = !empty($project['Project'][$_key[1]]) ? $project['Project'][$_key[1]] : '';
            } else {
                $_output = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
            }
        } else {
            $_outputName = $_output = (string) Set::classicExtract($project, $_fieldset['path']);
            if(in_array($_fieldset['key'], $columnSelected)){
                $_key = explode('.', $_fieldset['key']);
                $_path = explode('.', $_fieldset['path']);
                $_output = !empty($project[$_path[0]]['id']) ? $project[$_path[0]]['id'] : '';
            }
        }
        switch ($_fieldset['key']) {
            case 'Project.project_name': {
                    $_output = $project["Project"]["project_name"];
                    break;
                }
            // case 'Project.read_access': {
            //         $_output = $project["ProjectEmployeeManager"]["project_manager_id"];
            //         break;
            //     }
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
            case 'ProjectAmr.customer_point_of_view': 
			case 'ProjectAmr.budget_weather':
			case 'ProjectAmr.scope_weather':
			case 'ProjectAmr.schedule_weather':
			case 'ProjectAmr.resource_weather':
			case 'ProjectAmr.technical_weather': {
                    if( $_outputName ){
                        $data['DataSet'][$_fieldset['key']] = $_outputName;
						$selects[$_fieldset['key']][$_outputName] = $i18n[$_outputName];
                        $_output = $_outputName;
                    }
                    break;
                }
            case 'Project.project_amr_sub_program_id':
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectAmrSubProgram']['id'];
                    $selects[$_fieldset['key']][$project['ProjectAmrSubProgram']['id']] = $_outputName;
                    break;
            case 'Project.project_amr_program_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectAmrProgram']['id'];
                    $selects[$_fieldset['key']][$project['ProjectAmrProgram']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_manager_id': {
                    $data['DataSet'][$_fieldset['key']] = $project['Project']['project_manager_id'];
					foreach($project['Project']['project_manager_id'] as $key => $id){
						if( isset( $list_avatar[$id]['full_name'])){
							$selects[$_fieldset['key']][$id] = $list_avatar[$id]['full_name'];
						}
					}
                    break;
                }
            // case 'Project.read_access': {
            //         if(!empty($pEM)){
            //             foreach( $pEM as $key => $value) {
            //                 $data['DataSet'][$_fieldset['key']] = $value;
            //                 $selects[$_fieldset['key']][$value] = $_outputName;
            //             }
            //         }
            //         break;
            //     }
            case 'Project.functional_leader_id':
            case 'Project.uat_manager_id':
                    $managerId = $_output;
                    $_output = isset($projectManagersOption[$managerId]) ? $projectManagersOption[$managerId] : '';
                    $data['DataSet'][$_fieldset['key']] = $managerId;
                    if( $_output )$selects[$_fieldset['key']][$managerId] = $_output;
                    break;
            case 'Project.project_type_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectType']['id'];
                    $selects[$_fieldset['key']][$project['ProjectType']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_sub_type_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectSubType']['id'];
                    $selects[$_fieldset['key']][$project['ProjectSubType']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_phase_id' : {
                    $data[$_fieldset['key']] = !empty($project['ProjectPhaseCurrent']) ? (array) Set::classicExtract($project['ProjectPhaseCurrent'], '{n}.project_phase_id') : array();
                    break;
                }
            case 'Project.list_muti_1' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_1'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_2' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_2'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_3' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_3'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_4' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_4'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_5' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_5'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_6' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_6'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_7' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_7'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_8' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_8'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_9' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_9'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_10' : {
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_10'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.project_priority_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectPriority']['id'];
                    break;
                }
            case 'Project.project_status_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectStatus']['id'];
                    $selects[$_fieldset['key']][$project['ProjectStatus']['id']] = $_outputName;
                    break;
                }
            case 'ProjectAmr.project_amr_risk_information' : {
                    if(empty($_output)){
                        if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectRisk'])){
                            $_output = !empty($logGroups[$project['Project']['id']]['ProjectRisk']['description']) ? $logGroups[$project['Project']['id']]['ProjectRisk'] : '';
                        }
                    }
                    break;
                }
            case 'ProjectAmr.project_amr_problem_information' : {
                    if(empty($_output)){
                        if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectIssue'])){
                            $_output = !empty($logGroups[$project['Project']['id']]['ProjectIssue']['description']) ? $logGroups[$project['Project']['id']]['ProjectIssue']: '';
                        }
                    }
                    break;
                }
            case 'ProjectAmr.project_amr_solution' : {
                    if(empty($_output)){
                        if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectAmr'])){
                            $_output = !empty($logGroups[$project['Project']['id']]['ProjectAmr']['description']) ? $logGroups[$project['Project']['id']]['ProjectAmr'] : '';
                        }
                    }
                    break;
                }
            case 'ProjectAmr.done':
                if( isset($logs[ $project['Project']['id'] ]['Done']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Done'];
                }
                break;
            case 'ProjectAmr.todo':
                if( isset($logs[ $project['Project']['id'] ]['ToDo']) ){
                    $_output = $logs[ $project['Project']['id'] ]['ToDo'];
                }
                break;
            case 'ProjectAmr.comment':
                if( isset($logs[ $project['Project']['id'] ]['ProjectAmr']) ){
                    $_output = $logs[ $project['Project']['id'] ]['ProjectAmr'];

                    //ob_clean(); debug($_output); exit;
                }
                break;
			case 'ProjectAmr.project_amr_budget_comment':
                if( isset($logs[ $project['Project']['id'] ]['Budget']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Budget'];
					$_output['current'] = time();
                }
                break;
            case 'ProjectAmr.project_amr_scope':
                if( isset($logs[ $project['Project']['id'] ]['Scope']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Scope'];
					$_output['current'] = time();
                }
                break;
            case 'ProjectAmr.project_amr_resource':
                if( isset($logs[ $project['Project']['id'] ]['Resources']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Resources'];
					$_output['current'] = time();
                }
                break;
            case 'ProjectAmr.project_amr_schedule':
                if( isset($logs[ $project['Project']['id'] ]['Schedule']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Schedule'];
					$_output['current'] = time();
                }
                break;
            case 'ProjectAmr.project_amr_technical':
                if( isset($logs[ $project['Project']['id'] ]['Technical']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Technical'];
					$_output['current'] = time();
                }
                break;
            case 'Project.list_1':
            case 'Project.list_2':
            case 'Project.list_3':
            case 'Project.list_4':
            case 'Project.list_5':
            case 'Project.list_6':
            case 'Project.list_7':
            case 'Project.list_8':
            case 'Project.list_9':
            case 'Project.list_10':
            case 'Project.list_11':
            case 'Project.list_12':
            case 'Project.list_13':
            case 'Project.list_14':
                $_key = explode('.', $_fieldset['key']);
                $_key = $_key[1];
                $_name = '';
                if( isset($datasets[$_key][$_outputName]) ){
                    $_name = $datasets[$_key][$_outputName];
                    $selects[$_fieldset['key']][$_outputName] = $_name;
                }
                $_output = $_outputName;
                break;
            case 'Project.bool_1':
            case 'Project.bool_2':
            case 'Project.bool_3':
            case 'Project.bool_4':
                $_output = !empty($_outputName) ? $_outputName : 'zero';
            break;
            case 'Project.date_1':
            case 'Project.date_2':
            case 'Project.date_3':
            case 'Project.date_4':
            case 'Project.date_5':
            case 'Project.date_6':
            case 'Project.date_7':
            case 'Project.date_8':
            case 'Project.date_9':
            case 'Project.date_10':
            case 'Project.date_11':
            case 'Project.date_12':
            case 'Project.date_13':
            case 'Project.date_14':
                $_output = $str_utility->convertToVNDate($_outputName);
                break;
            case 'Project.category':
                $_output = $cates[$_outputName];
                break;
            case 'Project.yn_1':
            case 'Project.yn_2':
            case 'Project.yn_3':
            case 'Project.yn_4':
            case 'Project.yn_5':
            case 'Project.yn_6':
            case 'Project.yn_7':
            case 'Project.yn_8':
            case 'Project.yn_9':
                $_output = $_outputName ? 'yes' : 'no';
                break;
            case 'Project.team':
                $_output = $listPc[$_output];
                break;
            case 'ProjectBudgetSyn.Workload€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Workload€'] : 0;
                break;
            case 'ProjectBudgetSyn.Consumed€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Consumed€'] : 0;
                break;
            case 'ProjectBudgetSyn.Remain€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Remain€'] : 0;
                break;
            case 'ProjectBudgetSyn.Amount€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Amount€'] : 0;
                break;
            case 'ProjectBudgetSyn.Estimated€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Estimated€'] : 0;
                break;
            case 'ProjectBudgetSyn.Consumed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Consumed'] : 0;
                break;
            case 'ProjectBudgetSyn.Remain':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Remain'] : 0;
                break;
            case 'ProjectBudgetSyn.Workload':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Workload'] : 0;
                break;
            case 'ProjectBudgetSyn.Initialworkload':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Initialworkload'] : 0;
                break;
            case 'ProjectBudgetSyn.UnitPrice':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['UnitPrice'] : 0;
                break;
            case 'ProjectBudgetSyn.Overload':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Overload'] : 0;
                break;
            case 'ProjectBudgetSyn.ManualConsumed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['ManualConsumed'] : 0;
                break;
            case 'ProjectBudgetSyn.InUsed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['InUsed'] : 0;
                break;
            case 'ProjectBudgetSyn.Completed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Completed'] : 0;
                break;
            case 'ProjectBudgetSyn.%progressorder':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['%progressorder'] : 0;
                break;
            case 'ProjectBudgetSyn.%progressorder€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['%progressorder€'] : 0;
                break;
            case 'Project.next_milestone_in_day':
                $_output = $listNextMilestoneByDay[$projectID];
                break;
            case 'Project.next_milestone_in_week':
                $_output = $listNextMilestoneByWeek[$projectID];
                break;
            case 'ProjectBudgetSyn.purchases_sold':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_sold']) ? $Purchase[$projectID]['purchases_sold'] : 0;
                break;
            case 'ProjectBudgetSyn.purchases_to_bill':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_to_bill']) ? $Purchase[$projectID]['purchases_to_bill'] : 0;
                break;
            case 'ProjectBudgetSyn.purchases_billed':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_billed']) ? $Purchase[$projectID]['purchases_billed'] : 0;
                break;
            case 'ProjectBudgetSyn.purchases_paid':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_paid']) ? $Purchase[$projectID]['purchases_paid'] : 0;
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
        $notValueFromProjectTable = array('Project.project_phase_id', 'Project.list_muti_1', 'Project.list_muti_2', 'Project.list_muti_3', 'Project.list_muti_4', 'Project.list_muti_5', 'Project.list_muti_6', 'Project.list_muti_7', 'Project.list_muti_8', 'Project.list_muti_9', 'Project.list_muti_10');
        if(!in_array($_fieldset['key'], $notValueFromProjectTable)){
            $data[$_fieldset['key']] = $_output;
        }
    }
    $data['action.'] = '';
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
                $stones[$project['Project']['id']][date('Y', $_start)][] = array(date('d-m-Y', $_start), $p['project_milestone'], $p['validated']);
            }
        }
        if (!empty($project['ProjectPhasePlan'])) {
            $_phase['start'] = $_phase['end'] = $_phase['rstart'] = $_phase['rend'] = 0;
            foreach ($project['ProjectPhasePlan'] as $phace) {
                /**
                 * Set start, end, real start, real end.
                 */
                if(isset($_phase['start']) && !empty($_phase['start']) && $_phase['start'] != 0){
                    $date = $this->Gantt->toTime($phace['phase_planed_start_date']);
                    if(($date <= $_phase['start']) && $date != 0){
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
                    if(($date <= $_phase['rstart']) && $date != 0){
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
            $completed = 0;
            $comPlan = '';
            if(!empty($phases) && !empty($phases[$projectID])){ //&& !empty($phases[$_pId][$node['id']])){
                $ds = $phases[$projectID];
                $workload = !empty($ds['workload']) ? $ds['workload'] : 0;
                $consumed = !empty($ds['consumed']) ? $ds['consumed'] : 0;
                if($workload == 0){
                    $completed = 0;
                } else{
                    $completed = round((($consumed*100)/$workload), 2);
                }
                if($_phase['start'] != 0 && $_phase['end'] != 0){
                    $datediff = $_phase['end'] - $_phase['start'];
                    $datediff = floor($datediff/(60*60*24));
                    $comPlan = round(($datediff*$completed)/100, 0);
                    $comPlan = strtotime("+$comPlan days", $_phase['start']);
                }
                if($_phase['rstart'] != 0 && $_phase['rend'] != 0){
                    $datediff = $_phase['rend'] - $_phase['rstart'];
                    $datediff = floor($datediff/(60*60*24));
                    $comReal = round(($datediff*$completed)/100, 0);
                    $comReal = strtotime("+$comReal days", $_phase['rstart']);
                }
            }
            $gantts[$projectID] = array(
                'start' => ($_phase['start'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['start']),
                'end' => ($_phase['end'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['end']),
                'rstart' => ($_phase['rstart'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rstart']),
                'rend' => ($_phase['rend'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rend']),
                'comPlan' => !empty($comPlan) ? date('d-m-Y', $comPlan) : '',
                'comReal' => !empty($comReal) ? date('d-m-Y', $comReal) : '',
                'completed' => $completed
            );
        }
    }
    // ob_clean();
    // debug( $data);
    // exit;
    $dataView[] = $data;
}
// exit;
/**
 * Gantt
 */
$leftColumns = count($columns);
$columnsOfGantt = array();
if($viewGantt){
    $yStart = !empty($confirmGantt['from']) ? $confirmGantt['from'] : date('Y', $ganttStart);
    $yEnd = !empty($confirmGantt['to']) ? $confirmGantt['to'] : date('Y', $ganttEnd);
    while($yStart <= $yEnd){
        $columnsOfGantt[] = 'Gantt' . $yStart;
        $columns[] = array(
            'id' => 'Gantt' . $yStart,
            'field' => 'Gantt' . $yStart,
            'name' => __($yStart, true),
            'width' => 365,
            'sortable' => false,
            'resizable' => false,
            'noFilter' => 1,
            'formatter' => 'Slick.Formatters.GanttCustom'
        );
        $yStart++;
    }
    $columnsOfGantt[] = 'action.';
}
if( !($isMobileOnly || $isTablet) || (($isMobileOnly || $isTablet) && !$viewGantt) ){
    $columns[] = array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 165,
        'sortable' => false,
        'resizable' => true,
        'ignoreExport' => true,
        'cssClass' => 'grid-action',
        'formatter' => 'Slick.Formatters.Action',
        'noFilter' => 1
    );
}

$container_width = 0;
foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
	$container_width += $columns[$key]['width'];
}
$selects['Project.project_priority_id'] = $priorities;
$selects['Project.bool_1'] = array('1' => '1', 'zero' => '0');
$selects['Project.bool_2'] = array('1' => '1', 'zero' => '0');
$selects['Project.bool_3'] = array('1' => '1', 'zero' => '0');
$selects['Project.bool_4'] = array('1' => '1', 'zero' => '0');
$selects['Project.project_phase_id'] = $ProjectPhases;
$selects['Project.list_muti_1'] = !empty($datasets['list_muti_1']) ? $datasets['list_muti_1'] : array();
$selects['Project.list_muti_2'] = !empty($datasets['list_muti_2']) ? $datasets['list_muti_2'] : array();
$selects['Project.list_muti_3'] = !empty($datasets['list_muti_3']) ? $datasets['list_muti_3'] : array();
$selects['Project.list_muti_4'] = !empty($datasets['list_muti_4']) ? $datasets['list_muti_4'] : array();
$selects['Project.list_muti_5'] = !empty($datasets['list_muti_5']) ? $datasets['list_muti_5'] : array();
$selects['Project.list_muti_6'] = !empty($datasets['list_muti_6']) ? $datasets['list_muti_6'] : array();
$selects['Project.list_muti_7'] = !empty($datasets['list_muti_7']) ? $datasets['list_muti_7'] : array();
$selects['Project.list_muti_8'] = !empty($datasets['list_muti_8']) ? $datasets['list_muti_8'] : array();
$selects['Project.list_muti_9'] = !empty($datasets['list_muti_9']) ? $datasets['list_muti_9'] : array();
$selects['Project.list_muti_10'] = !empty($datasets['list_muti_10']) ? $datasets['list_muti_10'] : array();
ksort($projectManagers);
$projectManagers = Set::combine(array_values($projectManagers), '{n}.0', '{n}.1');
$selectMaps = $selects;

?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-project-filter">
                    <?php
                        echo $this->Form->create('Category', array('style' => 'display: inline-block'));
                        $href = '';
                        $href = $this->params['url'];
                        if(!empty($appstatus)){
                            $op = ($appstatus == 1) ? 'selected="selected"' : '';
                            $ar = ($appstatus == 3) ? 'selected="selected"' : '';
                            $md = ($appstatus == 4) ? 'selected="selected"' : '';
                            $io = ($appstatus == 5) ? 'selected="selected"' : '';
                            $io2 = ($appstatus == 6) ? 'selected="selected"' : '';
                        }
                    ?>
                        <select class="wd-customs" id="CategoryStatus" rel="no-history" style="display:none;">
                            <option value="0"><?php echo  __("--Select--", true);?></option>
                        </select>
                        <div class="persionalize-container" id="persionalize">
                            <div class="component">
                                <div class="cn-wrapper" id="cn-wrapper">
                                    <ul class="circular-nav">

                                    </ul>
                                    <ul class="dropdown wd-hide">
                                        <li class="dropdown-item add-new"><a href="<?php echo $html->url(array('controller' => 'user_views')); ?>" > <?php __('Add new'); ?><span class="add"></span></a></li>
                                    </ul>
                                    <a href="javascript:void(0);" class="open-dropdown wd-hide"><img src="/img/new-icon/menu-white.png"></a>
                                    
                                </div>
                                <span href="javascript:void(0);" class="cn-button" id="cn-button"><?php __('Persionalize view');?></span>
                            </div>
                        </div>

                        <?php if($cate != 2):
                        // ob_clean();
                        // debug($this->params['url']['cate']);
                        // exit;
                        $list_cates = array(
                            '1' => array(
                                'cate'=>1,
                                'name'=>  __('En cours', true),
                                'long_name'=>  __('En cours', true),
                                'icon'=> '/img/new-icon/icon_inprogress.png',
                                ),
                            '6' => array(
                                'cate'=>6,
                                'name'=>  __('Opport.', true),
                                'long_name'=>  __('Opportunity', true),
                                'icon'=> '/img/new-icon/icon_oppotr.png',
                                ),
                            '3' => array(
                                'cate'=>3,
                                'name'=>  __('Archivé', true),
                                'long_name'=>  __('Archivé', true),
                                'icon'=> '/img/new-icon/icon_archive.png',
                                ),
                            '4' => array(
                                'cate'=>4,
                                'name'=>  __('Modèle', true),
                                'long_name'=>  __('Modèle', true),
                                'icon'=> '/img/new-icon/icon_model.png',
                                ),
							'5' => array(
                                'cate'=> 5,
                                'name'=>  __("In progress + Opportunity", true),
                                'long_name'=>  __("In progress + Opportunity", true),
                                'icon'=> '',
                                ),
							
                        );
                        ?>
                        <div class="circular-menu-container" id="CategoryView">
                            <div class="component">
                                <div class="cn-wrapper" id="cv-wrapper">
                                    <ul class="circular-nav">
                                        <?php 
										$index = 0;
										$continue = 0;
                                        foreach ($list_cates as $cate_item){
											if( $index >= 4) { $continue = 1; break;}
                                            $_active = ( isset($appstatus) && $cate_item['cate'] == $appstatus ) ? 'activated' : '';
                                            ?>
                                            <li data-value="<?php echo $cate_item['cate'];?> " class="item <?php echo $_active;?>"><a href="<?php echo $html->url(array(implode('/',$this->params['pass']),'?' => 'cate='.$cate_item['cate'])) ?>"><img src="<?php echo $cate_item['icon'];?>"><span><?php  echo $cate_item['name'] ?></span></a></li>
											<?php $index++; ?>
                                        <?php } ?>
                                    </ul>
									<?php if( $continue){
										?>
										<ul class="dropdown wd-hide">
											<?php
											$index = 0;											
											foreach ($list_cates as $cate_item){
												if( $index >=4){ ?>
													<li class="dropdown-item item">
														<a href="<?php echo $html->url(array(implode('/',$this->params['pass']),'?' => 'cate='.$cate_item['cate'])) ?>"><span><?php  echo $cate_item['name'] ?></span></a>
													</li>
												<?php }
												$index++;
											} ?>
										</ul>
										
									<?php }
									if($continue){ ?>
										<a href="javascript:void(0);" class="open-dropdown wd-hide"><img src="/img/new-icon/menu-white.png"></a>
									<?php } else{ ?>
										<a href="javascript:void(0);" class="open-dropdown circular-button"></a>
									<?php }?>
                                    
                                </div>
                                <?php
                                $_text = __('Category view', true);
                                if( isset( $appstatus ) ){
                                    if( isset( $list_cates[$appstatus]) ){
                                        $_text = $list_cates[$appstatus]['long_name'];
                                    }
                                }
                                ?>
                                <span class="cn-button" id="cc-button"><?php echo $_text;?></span>
                            </div>
                        </div>
                        <?php endif;?>
						<a href="javascript:void(0);" class="btn btn-text reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Delete the filter') ?>">
							<i class="icon-refresh"></i>
						</a>	
                        <div class="wd-sumrow" onclick="openSumrow.call(this);">
                            <img title="Sum row"  src="<?php echo $html->url('/img/new-icon/xichma.png'); ?>"/>
                            <img class="active" title="Sum row"  src="<?php echo $html->url('/img/new-icon/xichma-blue.png'); ?>"/>
                        </div> 
                    <?php
                        echo $this->Form->end();
                    ?>
                    <div class="open-filter-form" onclick="openFilter();">
                        <img title="header-bottom"  src="<?php echo $html->url('/img/new-icon/search.png'); ?>"/><span>Rechercher ...</span>
                    </div>
                    <div class="search-filter">
                    <span class="close-filter"><img title="Close filter"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
                    <?php
                        echo $this->Form->create('Filter', array('style' => 'display: inline-block'));
                        $href = '';
                        $href = $this->params['url']; ?>
                            <?php if(!empty($listProgramFields)){?>
                                <select class="wd-customs" id="project-program" rel="no-history">
                                    <option value=""><?php echo  __("Type de projet", true)?></option>
                                   <?php  foreach ($listProgramFields as $key => $value) {?>
                                        <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                                   <?php  } ?>
                                </select>
                            <?php } ?>
                            <input type="text" name="project-name" placeholder="Project">
                            <select class="wd-customs" id="weather" rel="no-history">
                                <option value=""><?php echo  __("Météo", true)?></option>
                                <option value="sun"><?php echo  __("Sun", true)?></option>
                                <option value="rain"><?php echo  __("Rain", true)?></option>
                                <option value="cloud"><?php echo  __("Cloud", true)?></option>
                            </select>
                            <?php 
                            if(!empty($listProjectManager)){?>
                                <select class="wd-customs" id="project-manager" rel="no-history">
                                    <option value=""><?php echo  __("Chef de projet", true)?></option>
                                   <?php  foreach ($listPMFields as $key => $value) {?>
                                        <option value="<?php echo $value['id'] ?>"><?php echo $value['first_name'] .' '. $value['last_name']; ?></option>
                                   <?php  } ?>
                                </select>
                            <?php } ?>
                            
                            <input type="text" name="avancement" placeholder="Avancement">

                            <a class="search-button"><img title="header-bottom"  src="<?php echo $html->url('/img/new-icon/search.png'); ?>"/></a>
                        <?php
                        echo $this->Form->end();
                    ?>
                    </div>
                </div>
                <div class="wd-title" style="display: none">
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
                            $io2 = ($appstatus == 6) ? 'selected="selected"' : '';
                        }
                    ?>
                        <select style="margin-right:5px; width: auto !important; padding: 6px; float: none" class="wd-customs" id="CategoryStatus" rel="no-history">
                            <option value="0"><?php echo  __("--Select--", true);?></option>
                        </select>
                        <?php if($cate != 2):?>
                        <select style="margin-right:5px; width: auto !important; padding: 6px; float: none" class="wd-customs" id="CategoryCategory" rel="no-history">
                            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("In progress", true)?></option>
                            <option value="6" <?php echo isset($io2) ? $io2 : '';?>><?php echo  __("Opportunity", true)?></option>
                            <option value="5" <?php echo isset($io) ? $io : '';?>><?php echo  __("In progress + Opportunity", true)?></option>
                            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Archived", true)?></option>
                            <option value="4" <?php echo isset($md) ? $md : '';?>><?php echo  __("Model", true)?></option>
                        </select>
                        <?php endif;?>
                    <?php
                        echo $this->Form->end();
                    ?>
                    <!-- <a href="javascript:void(0);" class="btn btn-excel" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a> -->
                    <a href="<?php echo $this->Html->url(array('action' => 'export_excel_index'));?>" id="export-table" class="btn btn-excel"></a>
                    <?php if(($employee['Employee']['create_a_project'] == 1 && empty($profileName)) || (!empty($profileName) && ($profileName['ProfileProjectManager']['can_create_project'] == 1)) || ($employee['Role']['id'] == 2)){ ?>
                            <a href="javascript:void(0);" id="add_project" class="btn-text btn-blue">
                                <i class="icon-plus"></i>
                                <?php if(!isset($companyConfigs['add_proroject_full_icon']) || $companyConfigs['add_proroject_full_icon'] == 1) { ?>
                                <span><?php __('Add Project') ?></span>
                                <?php } ?>
                            </a>
                    <?php } ?>
                    <a href="<?php echo $this->Html->url('/user_views/') ?>" target="_blank" class="button-setting"></a>
                    <?php if(!isset($companyConfigs['display_project_global']) || $companyConfigs['display_project_global'] == 1) { ?>
                    <a href="<?php echo $this->Html->url('/projects/map/') ?>" id="map-icon" class="btn btn-globe"></a>
                    <?php } ?>
                    <?php if(!isset($companyConfigs['display_project_grid']) || $companyConfigs['display_project_grid'] == 1) { ?>
                    <a href="<?php echo $this->Html->url('/projects/index_plus') ?>" id="grid-icon" class="btn btn-grid"><i class="icon-grid"></i></a>
                    <?php } ?>
                    <?php if( $isTablet ): ?>
                        <!--?php if(($employee['Employee']['create_a_project'] == 1 && empty($profileName)) || (!empty($profileName) && ($profileName['ProfileProjectManager']['can_create_project'] == 1)) || ($employee['Role']['id'] == 2)){ ?>
                                <a href="javascript:void(0);" id="add_project" class="btn-text btn-blue">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Add Project') ?></span>
                                </a-->
                        <?php //}
                            if($showTaskVision){
                        ?>
                                <a href="javascript:void(0);" id="vision_task" class="btn-text btn-blue">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Vision task') ?></span>
                                </a>
                                <?php } ?>
                                <?php
                                if($displayExpectation){
                                ?>
                                    <a href="javascript:void(0);" id="expectation_screen" class="btn-text btn-blue">
                                        <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                        <span><?php __('Vision Expectation') ?></span>
                                    </a>
                                <?php } ?>
                    <?php elseif( !$isMobileOnly ): ?>
                        <?php echo $this->element('multiSortHtml'); ?>
                        <?php if(!isset($companyConfigs['dispaly_vision_staffing_new']) || $companyConfigs['dispaly_vision_staffing_new'] == 1) { ?>
                            <a href="javascript:void(0);" id="add_vision_staffing_news" class="btn-text">
                                <img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
                                <span><?php __('Vision staffing+') ?></span>
                            </a>
                        <?php } ?>
                        <?php if(!isset($companyConfigs['display_vision_portfolio']) || $companyConfigs['display_vision_portfolio'] == 1) { ?>
                            <a href="javascript:void(0);" id="add_vision_portfolio" class="btn-text">
                                <img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
                                <span><?php __('Vision portfolio') ?></span>
                            </a>
                        <?php } ?>
                        <?php if(!isset($companyConfigs['display_portfolio']) || $companyConfigs['display_portfolio'] == 1) { ?>
                            <a href="javascript:void(0);" id="add_portfolio" class="btn-text">
                                <img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
                                <span><?php __('Portfolio') ?></span>
                            </a>
                        <?php } ?>
                            <?php if($showTaskVision){ ?>
                                <a href="javascript:void(0);" id="vision_task" class="btn-text btn-blue">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Vision task') ?></span>
                                </a>
                            <?php } ?>
                            <?php
                            if($displayExpectation){
                            ?>
                                <a href="javascript:void(0);" id="expectation_screen" class="btn-text btn-blue">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Vision Expectation') ?></span>
                                </a>
                            <?php } ?>
                        <?php endif ?>
                        <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                </div>
                <?php
                echo $this->Session->flash();
                ?>
                <?php if(empty($_GET['view'])){ ?>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <?php } ?>
                <br clear="all"  />
                <div class="wd-table project-list" id="project_container" style="width: <?php echo $container_width + 20; ?>px" rels="<?php echo ($viewGantt && !empty($confirmGantt) && $confirmGantt['stones']) ? 'yes' : 'no';?>">

                </div>
                <div id="pager" style="clear:both;width:100%;height:0px;"></div>
            </div>
        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'projects', 'action' => 'export_project', $viewId)));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
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
            <!-- <div class="wd-input">
                <label for="project-read-access"><?php __d(sprintf($_domain, 'Read Access'),"Read Access") ?></label>
                <?php
                echo $this->Form->input('project_read_access', array(
                    'type' => 'select',
                    'name' => 'read_access',
                    'div' => false,
                    'label' => false,
                    'multiple' => false,
                    'hiddenField' => false,
                    "empty" => __("--", true),
                    'style' => 'width:69% !important',
                    "options" => $pEM));
                ?>
            </div> -->
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
                                'value' => '0'
                            ));
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
                                'value' => '0'
                            ));
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
                                'value' => '0'
                            ));
                            ?>
                            <?php
                            echo $this->Form->radio('project_staffing_id', array( 1 => __("Profit center", true)), array(
                                'name' => 'type',
                                'fieldset' => false,
                                'legend' => false,
                                'value' => '0'
                            ));
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
                                'value' => '0'
                            ));
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
                                'value' => '0'
                            ));
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
                    "options" => $project_arm_programs
                ));
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
                    "options" => array()
                ));
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
                    "options" => $projectManagersOption
                ));
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
                    "options" => $project_statuses
                ));
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
                    "options" => $profit_centers
                ));
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
                    "options" => $project_functions
                ));
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
                    "options" => $project_arm_programs
                ));
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
                    "options" => array()
                ));
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
                    "options" => $projectManagersOption
                ));
                ?>
            </div>
            <div class="wd-input">
                <label for="status"><?php __d(sprintf($_domain, 'Details'), "Status") ?></label>
                <select name="project_status_id" style="margin-right:11px; width:8.8%% !important; padding: 6px;" class="wd-customs" id="ProjectProjectStatusId_port">
                    <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("In progress", true)?></option>
                    <!--option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Opportunity", true)?></option-->
                    <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Archived", true)?></option>
                    <option value="4" <?php echo isset($md) ? $md : '';?>><?php echo  __("Model", true)?></option>
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
<div id="dialog_vision_task" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_task')); ?>
        <div class="wd-input" id="" style="overflow: visible">
            <label for="">&nbsp;</label>
            <?php
            echo $this->Form->input('category', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameCategory',
                'id' => 'category-id',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => $cates
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Status Of Project") ?></label>
            <?php
            echo $this->Form->input('status_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameStatusProject',
                'id' => 'statusProject',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectStatus) ? $projectStatus : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
            <?php
            echo $this->Form->input('program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameProgramProject',
                'id' => 'programProject',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectProgram) ? $projectProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Sub program") ?></label>
            <?php
            echo $this->Form->input('sub_program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameSubProgramProject',
                'id' => 'subProgramProject',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectSubProgram) ? $projectSubProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Status of task") ?></label>
            <?php
            echo $this->Form->input('status_task', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameStatusTask',
                'id' => 'statusTask',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectStatus) ? $projectStatus : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Priority of task") ?></label>
            <?php
            echo $this->Form->input('priority_task', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameProrityTask',
                'id' => 'prorityTask',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($priorities) ? $priorities : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Task") ?></label>
            <?php
            echo $this->Form->input('task_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameTaskProject',
                'id' => 'taskProject',
                'multiple' => false,
                'type' =>'text',
                'hiddenField' => false,
                'style' => 'width: 289px !important; border: 1px solid #aaa;'
                ));
            ?>
        </div>
        <div class="wd-input" id="export-assign-team-div" style="overflow: visible">
            <label for=""><?php __("Assigned Team") ?></label>
            <?php
            echo $this->Form->input('assigned_team', array('div' => false, 'label' => false,
                'type' => 'select',
                'name' => 'assignedTeam',
                'id' => 'export-assign-team',
                'div' => false,
                'multiple' => true,
                'hiddenField' => false,
                'label' => false,
                "empty" => __("-- Any --", true),
                'style' => 'width: 300px !important',
                "options" => !empty($listProfitCenter) ? $listProfitCenter : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="export-assign-employee-div" style="overflow: visible">
            <label for=""><?php __("Assigned Resources") ?></label>
            <?php
            echo $this->Form->input('assigned_resources', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'assignedResources',
                'id' => 'export-assign-employee',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "empty" => __("-- Any --", true),
                "options" => array(),
                "options" => !empty($listEmployee) ? $listEmployee : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>

        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('Start Date'); ?></label>
            <?php
            echo $this->Form->input('start_date_vision', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'startDateVision',
                'name' => 'nameStartDateVision',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
        </div>
        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('End Date'); ?></label>
            <?php
            echo $this->Form->input('end_date_vision', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'endDateVision',
                'name' => 'nameEndDateVision',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Project Code 1") ?></label>
            <?php
            echo $this->Form->input('code_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameCodeProject',
                'id' => 'codeProject',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($_listProjectCode) ? $_listProjectCode : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Project Code 2") ?></label>
            <?php
            echo $this->Form->input('code_project_1', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameCodeProject1',
                'id' => 'codeProject1',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($_listProjectCode1) ? $_listProjectCode1 : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label><?php echo __("Milestone", true) ?></label>
            <?php
            echo $this->Form->input('milestone', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameMilestone',
                'id' => 'milestone',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($_milestone) ? $_milestone : array(),
                ));
            ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class=" type_buttons" style="padding-right: 10px !important">
        <p id="show_count_task"></p>
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="full_screen" id="full_screen_vision" title="<?php __('Full Screen')?>"><span><?php __('Full Screen') ?></span></a></li>
        <li><a href="javascript:void(0)" class="export" id="ok_sum_vision" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_sum_team"><?php __('RESET') ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_export_file_team" style="display: none;"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="dialog_vision_expectation" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_expectation')); ?>
        <div class="wd-input" id="" style="overflow: visible">
            <label for="">&nbsp;</label>
            <?php
            echo $this->Form->input('category', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'cateExpec',
                'id' => 'CatePExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => $cates
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Status Of Project") ?></label>
            <?php
            echo $this->Form->input('status_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'statusExpec',
                'id' => 'StatusPExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectStatus) ? $projectStatus : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
            <?php
            echo $this->Form->input('program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'programExpec',
                'id' => 'ProgramPExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectProgram) ? $projectProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Sub program") ?></label>
            <?php
            echo $this->Form->input('sub_program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'subproExpec',
                'id' => 'SubProgramPExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectSubProgram) ? $projectSubProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Expectations") ?></label>
            <?php
            echo $this->Form->input('expectations', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameExpec',
                'id' => 'nameExpec',
                'multiple' => false,
                'type' =>'text',
                'hiddenField' => false,
                'style' => 'width: 289px !important; border: 1px solid #aaa;'
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Assigned Team") ?></label>
            <?php
            echo $this->Form->input('assigned_team', array('div' => false, 'label' => false,
                'type' => 'select',
                'name' => 'assignedTeam',
                'id' => 'AssignTeamExpec',
                'div' => false,
                'multiple' => true,
                'hiddenField' => false,
                'label' => false,
                "empty" => __("-- Any --", true),
                'style' => 'width: 300px !important',
                "options" => !empty($listProfitCenter) ? $listProfitCenter : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Assigned Resources") ?></label>
            <?php
            echo $this->Form->input('assigned_resources', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'assignedResources',
                'id' => 'AssignRessourceExpec',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "empty" => __("-- Any --", true),
                "options" => array(),
                "options" => !empty($listEmployee) ? $listEmployee : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Screen") ?></label>
            <?php
            echo $this->Form->input('screen', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'screen',
                'id' => 'ScreenExpec',
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                // 'multiple' => true,
                "options" => !empty($screenExpec) ? $screenExpec : array()
                ));
            ?>
        </div>
        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('Start Date'); ?></label>
            <?php
            echo $this->Form->input('start_date_expec', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'startDateExpec',
                'name' => 'start',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
        </div>
        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('End Date'); ?></label>
            <?php
            echo $this->Form->input('end_date_expec', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'endDateExpec',
                'name' => 'end',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="full_screen" id="full_screen_expectation" title="<?php __('Full Screen')?>"><span><?php __('Full Screen') ?></span></a></li>
        <!-- <li><a href="javascript:void(0)" class="export" id="ok_sum_vision" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a></li> -->
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_vision_expec"><?php __('RESET') ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_export_file_team" style="display: none;"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="contextMenu-project" style="display: none;"></div>
<div id='modal_dialog_confirm' style="display: none">
    <div class='title'></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnYes' />
    <input class="cancel-new-project" type='button' value='<?php echo __('Cancel', true) ?>' id='btnNo' />
</div>
<div id="template_logs" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    
</div>
<?php
$myAvatar = '';
$avatarEmploys = $comment_avatar = $is_avatar = array();
foreach ($listAvata as $key => $value) {
    $avatarEmploys[$key] = $this->UserFile->avatar($value);
    $is_avatar[$key] = $checkAvatar[$value];
    if($value == $employee_id){
        $myAvatar = $avatarEmploys[$key];
    }
    $key = explode('-', $key);
    $comment_avatar[$key[1]] =  $this->UserFile->avatar($value);
}
$text_modified = __('Modified', true);
$text_by = __('by', true);
$_linkDashboard = '';
if( !empty($screenDashboard)){ 
	foreach($screenDashboard as $screen => $screen_val){
		$_linkDashboard = array();
		$_lang = $employee_info['Employee']['language'];
		$_title = ($_lang == 'fr') ? $screen_val['name_fre'] : (($_lang == 'en') ? $screen_val['name_eng'] :  __('Dashboard', true));
		$_linkDashboard['link'] =  $html->url(array('controller' => $screen_val['controllers'], 'action' => $screen_val['functions']));
		$_linkDashboard['title'] =  $_title;
		if($screen == 'indicator') break;
	}
}
?>
<script type="text/javascript">
    var mx = 'J F M A M J J A S O N D'.split(' ');
    var DataValidator = {};
    var ControlGrid;
    var columnsOfGantt = <?php echo json_encode($columnsOfGantt);?>;
    var today = new Date('<?php echo date('Y-m-d') ?>');
    var viewGantt = <?php echo json_encode($viewGantt);?>;
    var listPriorities = <?php echo json_encode($priorities) ?>;
    var listProjectType = <?php echo json_encode($listprojectTypes) ?>;
    var listProjectSubType = <?php echo json_encode($listprojectSubTypes) ?>;
    var projectProgram = <?php echo json_encode($projectProgram) ?>;
    var projectSubProgram = <?php echo json_encode($projectSubProgram) ?>;
    var utility = {};
    var _linkEdit = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
    var _linkDashboard = <?php echo json_encode($_linkDashboard);?>;
    var _linkKanban = <?php echo json_encode($html->url('/kanban/index'));?>;
    var _linkAdmin = <?php echo json_encode($html->url('/project_phases/'));?>;
    var _linkDelete = <?php echo json_encode($html->url('/projects/delete'));?>;
    var controller = 'projects_preview';
    var update_your_form = <?php echo json_encode($employee['Employee']['update_your_form']) ?>;
    var savePosition = <?php echo $savePosition ?>;
    var listEmployeeManager = <?php echo json_encode($listEmployeeManager) ?>;
    var employee_id = <?php echo json_encode($employee_id) ?>;
    var avatarEmploys = <?php echo json_encode($avatarEmploys) ?>;
    var checkAvatar = <?php echo json_encode($checkAvatar) ?>;
    var comment_avatar = <?php echo json_encode($comment_avatar) ?>;
    var listEmp = <?php echo json_encode($listEmp) ?>;
    var is_avatar = <?php echo json_encode($is_avatar) ?>;
    var myAvatar = <?php echo json_encode($myAvatar) ?>;
    var wdTable = $('.wd-table');
    var $checkDisplayProfileScreen = <?php echo json_encode($checkDisplayProfileScreen) ?>;
    var text_by = <?php echo json_encode($text_by) ?>;
    var text_modified = <?php echo json_encode($text_modified) ?>;
    var heightTable = $(window).height() - wdTable.offset().top - 20;
	var isTablet = <?php echo json_encode($isTablet) ?>;
    var isMobile = <?php echo json_encode($isMobile) ?>;
	var listProjectOfPM = <?php echo json_encode($listProjectOfPM); ?>;
	var employeeHasAvatar = <?php echo json_encode( $employeeHasAvatar); ?>;
	var list_avatar = <?php echo json_encode( $list_avatar); ?>;
    wdTable.css({
        height: heightTable,
    });
    $(window).resize(function(){
        var heightTable = $(window).height() - wdTable.offset().top - 20;
        wdTable.css({
            height: heightTable,
        });        
        var header_table_height = 0;
        $('.slick-pane-header').each(function(){
            _height = $(this).height();
            header_table_height = Math.max( header_table_height, _height);
        });
        var header_row_height = $('.slick-headerrow:first').height();
        var header_row_columns_height = $('.slick-headerrow-columns:first').height();
        wdTable.find('.slick-viewport').css({
            height: heightTable - header_table_height - header_row_height - header_row_columns_height - 5,
        });
        if( $(window).width() > 992){
            $('.search-filter').css('left',0-($('.wd-project-filter').position().left)-parseInt($('.wd-project-filter').css('padding-left')));
        }else{
            $('.search-filter').css('left','');
        }
    });
    function resizeHandler(){
        var _cols = ControlGrid.getColumns();
        var _numCols = _cols.length;
        var _gridW = 0;
        for (var i=0; i<_numCols; i++) {
            if($.inArray(_cols[i].id, columnsOfGantt) != -1 && viewGantt){
                // do nothing
                 _gridW += _cols[i].width;
            } else {
                _gridW += _cols[i].width;
            }
        }
        $('#wd-header-custom').css('width', _gridW);
        $('#project_container').css('width', _gridW + 20);

    }
    function daysInMonth(month, year) {
        return parseInt(new Date(year, month, 0).getDate());
    }
    (function($){
        var $this = SlickGridCustom,
            urlUpdateCustomer = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('action' => 'update', '%1$s', '%2$s')))); ?>,
            gantts = <?php echo json_encode($gantts);?>,
            stones = <?php echo json_encode($stones);?>,
            isPM = <?php echo json_encode($isPM) ?>,
            editable = <?php echo json_encode($editable) ?>,
            employeeId = <?php echo json_encode($employee_info['Employee']['id']) ?>,
            dataView,
            sortcol,
            triggger = false,
            grid,
            $sortColumn,
            $sortOrder,
            data = <?php echo json_encode($dataView); ?>,
            selects = <?php echo json_encode($selects); ?>,
            budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>,
            columns = <?php echo jsonParseOptions($columns, array('formatter', 'sorter')); ?>,
            totalHeaders = <?php echo json_encode($totalHeaders);?>,
            typeGantt = type = 'year',
            viewStone = <?php echo !empty($confirmGantt) && isset($confirmGantt['stones']) ? json_encode($confirmGantt['stones']) : json_encode(false);?>,
            viewInitial = <?php echo !empty($confirmGantt) && isset($confirmGantt['initial']) ? json_encode($confirmGantt['initial']) : json_encode(false);?>,
            viewReal = <?php echo !empty($confirmGantt) && isset($confirmGantt['real']) ? json_encode($confirmGantt['real']) : json_encode(false);?>,
            columnFilters = {},
            $parent = $('#project_container'),
            timeOutId = null,
            dataViewGG = {},
            listTopRow = {},
            heightNewRow = {},
            gridGG = {},
            viewManDay = <?php echo json_encode($viewManDay); ?>,
            actionTemplate =  $('#action-template').html(),
            backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            },
            imagePriorities = {sun: 0, cloud: 1, rain: 2, up: 3, down: 4, mid: 5},
            counter,
            enable_popup = <?php echo !empty($employee_info['Employee']['is_enable_popup']) ? 1 : 0 ?>,
            see_budget = <?php echo !empty($employee_info['CompanyEmployeeReference']['see_budget']) ? 1 : 0 ?>,
            update_budget = <?php echo !empty($employee_info['Employee']['update_budget']) ? 1 : 0 ?>,
            isPm = <?php echo !empty($employee_info['Role']['name']) && $employee_info['Role']['name'] == 'pm' ? 1 : 0 ?>,
            isAdmin = <?php echo !empty($employee_info['Role']['name']) && $employee_info['Role']['name'] == 'admin' ? 1 : 0 ?>,
            count = 0,
            personDefault = <?php echo json_encode($personDefault);?>,
            appstatus = <?php echo json_encode($appstatus);?>,
            checkStatus = <?php echo json_encode($checkStatus);?>,
            cate_id = appstatus ? appstatus : 1,
            cate = <?php echo json_encode($cate);?>,
            viewId = '',
            checkShowFullMistones = false,
            listColorNextMil = <?php echo json_encode($listColorNextMil); ?>,
            $display_all_name_of_milestones = <?php echo isset($confirmGantt['display_all_name_of_milestones']) && $confirmGantt['display_all_name_of_milestones'] ? 1 : 0 ; ?>;

        function weatherSorter(a, b){
            return imagePriorities[a.DataSet[sortcol]] > imagePriorities[b.DataSet[sortcol]] ? 1 : -1;
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
                        var content = '<option value="0" ' + selected + '><?php echo  __("------- Select -------", true);?></option>';
                        if(personDefault == false){
                            content += '<option value="-1" ' + selectDefined + '><?php echo  __("-- Default", true);?></option>';
                        } else {
                            content += '<option value="-2" ' + selectDefault + '><?php echo  __("-- Default", true);?></option>';
                        }
                        $.each(datas, function(ind, val){
                            var selected = '';
                            if(view_id == ind && view_id != null && view_id != -2 && view_id != -1 && view_id != 0){
                                selected = 'selected="selected"';
                            }
                            content += '<option value="' +ind+ '" ' + selected + '>' + val + '</option>';
                        });
                        $('#CategoryStatus').html(content);


                        var list_icon = [
                            '/img/new-icon/pilotage.png',
                            '/img/new-icon/planing.png',
                            '/img/new-icon/budget.png',
                            '/img/new-icon/risques.png',

                        ];
                        var _list_cnav = _list_drop = '';
                        var index = 0;
                        $.each(datas, function(ind, val){
                            var activated = '';
                            if(view_id == ind && view_id != null && view_id != -2 && view_id != -1 && view_id != 0){
                                activated = 'activated';
                            }
                            if(activated) $('#cn-button').text(val);

                            if( index < 4){
                                _list_cnav += '<li data-value="' +ind+ '" class="item ' + activated + '"><a href="/projects_preview/index/' +ind+ '?cate=' + cate + '"><img src="' + list_icon[index] + '"><span>' + val + '</span></a></li>';
                            }else{
                                _list_drop += '<li data-value="' +ind+ '" class="item ' + activated + '"><a href="/projects_preview/index/' +ind+ '?cate=' + cate + '"><span>' + val + '</span></a></li>';
                            }
                            index++;
                        });
                        $('#cn-wrapper .circular-nav').append(_list_cnav);
                        $('#cn-wrapper .dropdown').prepend(_list_drop);

                        $('#CategoryStatus').html(content);
                    }
                });
            }
        }
        listProjectStautus(appstatus, checkStatus);
        function initPersionalize(){
            var button = $('#cn-button'),
            wrapper = $('#persionalize .component');
            // var menu_ext_button = $('.cn-wrapper .open-dropdown');
			var menu_ext_button = wrapper.find('.open-dropdown');
            // var menu_ext = menu_ext_button.closest('cn-wrapper').find('.dropdown:first');

            button.on('click', function(e){
                e.preventDefault();
                wrapper.toggleClass('persionalize-active').removeClass('menu-open');
            });
            menu_ext_button.on('click', function(){
                wrapper.toggleClass('menu-open');
            });

            $('body').on('click', function(e){
                if( !$(e.target).hasClass('persionalize-container') && !$('.persionalize-container').find(e.target).length){
                    wrapper.removeClass('persionalize-active').removeClass('menu-open');
                }
            });


        }
        function initCategoryView(){
            var button = $('#cc-button'),
            wrapper = $('#CategoryView .component');
			var menu_ext_button = wrapper.find('.open-dropdown');
            // var menu_ext = menu_ext_button.closest('cn-wrapper').find('.dropdown:first');

            button.on('click', function(e){
                e.preventDefault();
                wrapper.toggleClass('persionalize-active').removeClass('menu-open');

            });
			menu_ext_button.on('click', function(){
                wrapper.toggleClass('menu-open');
            });
            $('body').on('click', function(e){
                if( !$(e.target).hasClass('circular-menu-container') && !$('.circular-menu-container').find(e.target).length){
                    wrapper.removeClass('persionalize-active').removeClass('menu-open');
                }
            });

        }
        initPersionalize();
        initCategoryView();

        function showProjectCreatedVals(e,id){
            jQuery.ajax({
                url : "/project_created_vals_preview/ajax/"+id,
                type: "GET",
                data: data,
                cache: false,
                success: function (html) {
                    jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0});
                    var wh=jQuery(window).height();
                    if (wh < 768) {
                        // jQuery('#dialogDetailValue').css({'min-width':'200px !important'});
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
                    jQuery('#dialogDetailValue').css({'padding':20,'padding-top':10});
                    jQuery('#contentDialog').css({'max-height':600,'width':960});
                    jQuery('#contentDialog').html(html);
                    jQuery(e.target).removeClass('hoverCell');
                    jQuery(e.target).removeClass('loading');
                    showMe();
                    clearInterval(counter);
                }
            });
        }
        function showProjectGlobalViews(e, id){
            jQuery.ajax({
                url : "/project_global_views/ajax/"+id,
                type: "GET",
                data: data,
                cache: false,
                success: function (html) {
                    var dump = $('<div />').append(html);
                    if( dump.children('.error').length == 1 ){
                        //do nothing
                    } else if ( dump.children('#attachment-type').val() ) {
                        if(savePosition && savePosition != 'undefined'){
                            var top = (savePosition.top > 0 && savePosition.top < 1000)? savePosition.top : 0;
                            var left = (savePosition.left > 0 && savePosition.left < 1950) ? savePosition.left : 0;
                            jQuery('#dialogDetailValue').css({'top': top + 'px','left': left + 'px'});
                        } else {
                            jQuery('#dialogDetailValue').css({'top':"15%",'left':'20%'});
                        }
                        jQuery('#contentDialog').html(html);
                        showMe();
                    }
                    jQuery(e.target).removeClass('hoverCell');
                    jQuery(e.target).removeClass('loading');
                    clearInterval(counter);
                }
            });
        }

        function showProjectFinanceGlobalViews(e, id, _cat){
            var _this = $(e.target);
            $.ajax({
                url : "/project_finances_preview/ajax/"+id,
                type: "GET",
                cache: false,
                data: {
                    cat: _cat
                },
                success: function (html) {
                    _this.removeClass('loading').removeClass('hoverCell');
                     $('#dialogDetailValue').css({
                        'padding-top':0,
                        'padding-bottom':0,
                    });
                    var wh= $(window).height();
                    if (wh < 768) {
                         $('#contentDialog').css({'max-height':600,'width':'auto'});
                    } else {
                         $('#contentDialog').css({'max-height':'none','width':'auto'});
                    }
                    showMe();
                    $('#contentDialog').html(html);
                    clearInterval(counter);
                }
            });
        }
        function totalDate(start, end){
            /**
             * Tinh tong so ngay
             */
            start = new Date(start).getTime();
            end = new Date(end).getTime();
            var totalDays = Math.abs(parseInt(start) - parseInt(end));
            totalDays = Math.ceil(totalDays/(1000 * 3600 * 24));
            return totalDays;
        }
        function DrawGantt(projectId, start, end, comPlan, currentColumn, color, type, projectName, completed){
            var _width = 0,
                _top = 5,
                _left = 0,
                _background = 'none',
                _completedWidth = 0,
                result = '';
            start = start.split('-');
            end = end.split('-');
            var saveSt = start.slice(), saveEn = end.slice();
            /**
             * Draw
             */
            if(currentColumn < start[2] || currentColumn > end[2]){
                // don't draw anything
            } else {
                // Tong so ngay cua 1 nam
                var _totalDateOfYear = totalDate(currentColumn + '-01-01', currentColumn + '-12-31');
                // Tong so ngay tu ngay bat dau den cuoi nam
                var _totalStartToYear = totalDate(start[2] + '-' + start[1] + '-' + start[0], currentColumn + '-12-31');
                // Tong so ngay de ve diem bat dau
                var _startLine = Math.abs(parseInt(_totalStartToYear) - parseInt(_totalDateOfYear));
                _startLine = (_startLine*100)/_totalDateOfYear;
                // Tong so ngay tu ngay bat dau cua nam den ngay cuoi cung cua gantt
                var _totalStartToEndGantt = totalDate(currentColumn + '-01-01', end[2] + '-' + end[1] + '-' + end[0]);
                var _endLine = (_totalStartToEndGantt*100)/_totalDateOfYear;
                // Tinh end line cho cac truong hop start va end trong cung 1 nam.
                var _totalStartToEndGanttNotStartAtJanuary = totalDate(start[2] + '-' + start[1] + '-' + start[0], end[2] + '-' + end[1] + '-' + end[0]);
                var _endLineNotStartAtJanuary = (_totalStartToEndGanttNotStartAtJanuary*100)/_totalDateOfYear;
                if(start[2] == end[2]){
                    _width += _endLineNotStartAtJanuary;
                    _left += _startLine;
                } else {
                    if(start[2] == currentColumn){
                        _width += _endLine;
                        _left += _startLine;
                    } else if(currentColumn < end[2]){
                        _width += 100;
                    } else {
                        _width += _endLine;
                    }
                }
                if(type == 's'){

                } else {
                    _top = 16;
                    _background = color + " url('/img/line-n.png') center repeat-x !important;";
                }
                if(comPlan){
                    comPlan = comPlan.split('-');
                    if(currentColumn == comPlan[2]){
                        /**
                         * Tinh tong so ngay
                         */
                        var _start = start[2] + '-' + start[1] + '-' + start[0],
                            _end = end[2] + '-' + end[1] + '-' + end[0];
                        if(start[2] < comPlan[2]){
                            _start = comPlan[2] + '-01-01';
                        }
                        if(end[2] > comPlan[2]){
                            _end = comPlan[2] + '-12-31';
                        }
                        _start = new Date(_start).getTime();
                        _end = new Date(_end).getTime();
                        var totalDays = Math.abs(parseInt(_start) - parseInt(_end));
                        totalDays = Math.ceil(totalDays/(1000 * 3600 * 24));
                        /*
                         * Tinh so ngay completed
                         */
                        var _com = new Date(comPlan[2] + '-' + comPlan[1] + '-' + comPlan[0]).getTime();
                        _com = Math.abs(parseInt(_start) - parseInt(_com));
                        _com = Math.ceil(_com/(1000 * 3600 * 24));
                        _completedWidth = (_com*100)/totalDays;
                    } else if(currentColumn < comPlan[2]){
                        _completedWidth = 100;
                    } else {
                        _completedWidth = 0;
                    }
                    if(completed <= 0){
                        _completedWidth = 0;
                    }
                }
                completed = (completed > 100) ? 100 : completed;
                completed = (completed < 0) ? 0 : completed;
                var dataTooltip = '<div class="hover-data" style="display: none">' +
                                    '<p class="hover-data-name">'+projectName+'</p>' +
                                    '<p class="hover-data-start">'+ saveSt[0] + '/' + saveSt[1] + '/' + saveSt[2] +'</p>' +
                                    '<p class="hover-data-end">'+ saveEn[0] + '/' + saveEn[1] + '/' + saveEn[2] +'</p>' +
                                    '<p class="hover-data-comp">'+completed+'%</p>' +
                                    '<p class="hover-data-assign"></p>' +
                                '</div>';
                result += '<div id="line-' +type+ '-' +projectId+ '" onclick="showPhaseDetail(event, '+projectId+')"class="gantt-line-' +type+ ' hover-tooltip-cus" style="position: absolute; background: '+_background+'; top: '+_top+'px; left: ' +_left+'%; border: 1px solid ' + color + '; width:' +_width+ '%;">' +
                            dataTooltip + '<em style="background-color: ' +color+ '; height: ' + (type == 's' ? '5' : '6') + 'px; display: block; width:' +_completedWidth+ '%;"></em>' +
                        '</div>';
            }
            return result;
        }
        var _role = <?php echo json_encode($employee['Role']['name']);?>;
        var _allowDeleteProject = <?php echo json_encode($employee['Employee']['delete_a_project']);?>;
        var _allowDeleteProfjectByProfile = <?php echo !empty($profileName) ? json_encode($profileName['ProfileProjectManager']['can_delete_project']) : 0;?>;
        $.extend(Slick.Formatters,{
            Action : function(row, cell, value, columnDef, dataContext){

                var linkProjectName = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
                value = '<div class="wd-actions">';
				if (_linkDashboard) {
                    value += '<a target="_blank" href="' + _linkDashboard['link'] + '/' + dataContext['id'] + '?view=new" class="wd-dashboard" title="' + _linkDashboard['title'] + '"></a>';
                }
				value += '<a href="'+ _linkKanban +'/'+ dataContext['id'] +'?view=new" class="wd-kanban"></a><a href="'+ linkProjectName +'/'+ dataContext['id'] +'" class="wd-edit"></a>';
                if(_role == 'admin' || (_role == 'pm' && _allowDeleteProject == 1 && $.inArray(dataContext['id'].toString(), listProjectOfPM) != -1)){
					value += '<a href="#" onclick="dialog_confirm('+ dataContext['id'] +')" class="wd-hover-advance-tooltip"></a>';
                }
                value += '</div>';
                return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
            },
            text : function(row, cell, value, columnDef, dataContext){
				var _pid = dataContext.id;
                var _colum = columnDef.id;
                var _h = (viewStone) ? 40 : 42;
				var name = '';
				var circle_name = '';
                var s_name = '';
				if( 'update_by_employee' in Object(value) ){
					name = [];
					var s_name = value.update_by_employee.split(" ");
					name[0] = s_name[0][0] + s_name[1][0];
					name[1] = value.update_by_employee;
					if( $.inArray( value.employee_id ,employeeHasAvatar) !== -1){
						circle_name = '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%',value.employee_id) +'" title = "'+ value.update_by_employee +'" />';
					}else{
						circle_name = '<span class="circle-name" style="width: 30px; height: 30px; line-height: 30px; font-size: 14px;" title = "'+ value.update_by_employee +'">'+ name[0] +'</span>';
					}
				}
                var model = 'ProjectAmr';
                var field = 'comment';
                if(_colum == 'ProjectAmr.comment'){
                } else if(_colum == 'ProjectAmr.project_amr_solution'){
                    field = 'project_amr_solution';
                } else if(_colum == 'ProjectAmr.done'){
                    model = 'Done';
                    field = 'done';
                } else if(_colum == 'ProjectAmr.project_amr_scope'){
                    model = 'Scope';
                    field = 'project_amr_scope';
                } else if(_colum == 'ProjectAmr.project_amr_budget_comment'){
                    model = 'Budget';
                    field = 'project_amr_budget_comment';
                } else if(_colum == 'ProjectAmr.project_amr_schedule'){
                    model = 'Schedule';
                    field = 'project_amr_schedule';
                } else if(_colum == 'ProjectAmr.project_amr_resource'){
                    model = 'Resources';
                    field = 'project_amr_resource';
                } else if(_colum == 'ProjectAmr.project_amr_technical'){
                    model = 'Technical';
                    field = 'project_amr_technical';
                } else if(_colum == 'ProjectAmr.project_amr_problem_information'){
                    model = 'ProjectIssue';
                    field = 'project_amr_problem_information';
                } else if(_colum == 'ProjectAmr.project_amr_risk_information'){
                    model = 'ProjectRisk';
                    field = 'project_amr_risk_information';
                } else {
                    model = 'ToDo';
                    field = 'todo';
                }
                if(circle_name){
                   return '<span class="project-manager-name circle-name" style="width: 30px; height: 30px; line-height: 30px; font-size: 14px;float: left;" title = "'+name[1]+'">'+circle_name+'</span><p class="hover-popup">' + value.description + '</p><p class="open-popup"  onmouseover="openPopupText.call(this)" onmouseout="closePopupText.call(this);" data-model = '+model+' data-field = '+field+' data-id = '+_pid+' onclick="updateText.call(this);">' + value.description + '</p>';
                }else{
					return '<p class="open-popup" data-model = '+model+' data-field = '+field+' data-id = '+_pid+' onclick="updateText.call(this);">&nbsp;</p>';
				}
				
				return '<p>' + value + '</p>';
            },
            GanttCustom : function(row, cell, value, columnDef, dataContext){
                var _color = (row%2 == 0) ? '#0099cc' : '#f05656';
                var projectId = dataContext.id ? dataContext.id : 0,
                    projectName = dataContext['Project.project_name'] ? dataContext['Project.project_name'] : '';
                var currentColumn = columnDef.id.substring(5);
                value = '';
                //draw curent month
                //draw all months
                total = totalDate(currentColumn + '-01-01', currentColumn + '-12-31') + 1;
                for(var i = 0; i < 12; i++){
                    m = i+1;
                    m = m < 10 ? '0' + m : m;
                    days = daysInMonth(m, currentColumn);
                    layerWidth = days / total * 100;
                    offsetLeft = totalDate(currentColumn + '-01-01', currentColumn + '-' + m + '-01') / total * 100;
                    if( currentColumn == today.getFullYear() && today.getMonth() == i ){
                        value += '<div class="gantt-current-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"></div>';
                    } else {
                        value += '<div class="gantt-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"></div>';
                    }
                }
                if(gantts[projectId]){
                    var start = gantts[projectId].start ? gantts[projectId].start : '',
                    end = gantts[projectId].end ? gantts[projectId].end : '',
                    rstart = gantts[projectId].rstart ? gantts[projectId].rstart : '',
                    rend = gantts[projectId].rend ? gantts[projectId].rend : '',
                    comPlan = gantts[projectId].comPlan ? gantts[projectId].comPlan : 0,
                    comReal = gantts[projectId].comReal ? gantts[projectId].comReal : 0,
                    completed = gantts[projectId].completed ? gantts[projectId].completed : 0;
                    if(start != '00-00-0000' && end != '00-00-0000' && viewInitial){
                        value += DrawGantt(projectId, start, end, comPlan, currentColumn, _color, 'n', projectName, completed); // n is plan
                    }
                    if(rstart != '00-00-0000' && rend != '00-00-0000' && viewReal){
                        value += DrawGantt(projectId, rstart, rend, comReal, currentColumn, _color, 's', projectName, completed); // s is real
                    }
                }
                if(viewStone && stones[projectId] && stones[projectId][currentColumn]){
                    /**
                     * Tinh tong so ngay
                     */
                    var _start = new Date(currentColumn + '-01-01').getTime();
                    var _end = new Date(currentColumn + '-12-31').getTime();
                    var totalDays = Math.abs(parseInt(_start) - parseInt(_end));
                    totalDays = Math.ceil(totalDays/(1000 * 3600 * 24));
                    /**
                     * tinh vi tri cua milestone
                     */
                    var stoneHtml = '';
                    $.each(stones[projectId][currentColumn], function(index, value){
                        var _date = value[0] ? value[0].split('-') : '';
                        var _leftStone = new Date(_date[2] + '-' + _date[1] + '-' + _date[0]).getTime();
                        _leftStone = Math.abs(parseInt(_start) - parseInt(_leftStone));
                        _leftStone = Math.ceil(_leftStone/(1000 * 3600 * 24));
                        _leftStone = (_leftStone*100)/totalDays;
                        if(value[2] == 1){
                            stoneHtml += '<div class="gantt-msi gantt-msi-green gantt-ms" style="left:' +_leftStone+ '%;top:26px">';
                        } else {
                            var dStone = new Date(_date[2] + '-' + _date[1] + '-' + _date[0]).getTime();
                            if(today.getTime() > dStone){
                                stoneHtml += '<div class="gantt-msi gantt-ms" style="left:' +_leftStone+ '%;top:26px">';
                            } else if(today.getTime() < dStone){
                                stoneHtml += '<div class="gantt-msi gantt-msi-blue gantt-ms" style="left:' +_leftStone+ '%;top:26px">';
                            } else {
                                stoneHtml += '<div class="gantt-msi gantt-msi-orange gantt-ms" style="left:' +_leftStone+ '%;top:26px">';
                            }
                        }
                        stoneHtml += '<div class="hover-stone" style="display:none;">'+
                                        '<p class="hover-stone-name">' +value[1]+ '</p>'+
                                        '<p class="hover-stone-date">' +_date[0] + '/' + _date[1] + '/' + _date[2]+ '</p>'+
                                    '</div>'+
                                '<i></i><span></span></div><br />';
                    });
                    value += '<div class="st-index-'+projectId+'">' + stoneHtml + '</div>';
                }
                return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
            },
            selectFormatter : function(row, cell, value, columnDef, dataContext){
                var html;
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                //old logic. !isPM || editable[dataContext.id] || xyz
                if( !isPM || (isPM && update_your_form) ){
                    html = '<select style="padding: 2px 15px 2px 5px" rel="no-history" onchange="updatePriority.call(this, \'' + dataContext.id + '\', ' + cell + ')"><option value="0">--</option>';
                    for(var i in listPriorities){
                        html += '<option value="' + i + '" ' + (i == dataContext.DataSet['Project.project_priority_id'] ? 'selected' : '') + '>' + listPriorities[i] + '</option>';
                    }
                    html += '</select>';
                } else {
                    html = listPriorities[dataContext['Project.project_priority_id']] ? listPriorities[dataContext['Project.project_priority_id']] : '';
                }
                return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
            },
            selectBoxFormatter : function(row, cell, value, columnDef, dataContext){
                var html;
                var type, keys;
                if(columnDef.field == 'Project.project_type_id'){
                    type = listProjectType;
                    keys = 'Project.project_type_id';
                }else if ( columnDef.field == 'Project.project_sub_type_id' ){
                    type = listProjectSubType;
                    keys = 'Project.project_sub_type_id';
                } else if ( columnDef.field == 'Project.project_amr_program_id' ){
                    type = projectProgram;
                    keys = 'Project.project_amr_program_id';
                } else if ( columnDef.field == 'Project.project_amr_sub_program_id' ) {
                    type = projectSubProgram;
                    keys = 'Project.project_amr_sub_program_id';
                } else {
                    return;
                }
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                if( !isPM || (isPM && update_your_form) ){
                    html = '<select style="padding: 2px 15px 2px 5px" rel="no-history" data-key="'+keys+'" onchange="updateTypeAndProgram.call(this, \'' + dataContext.id + '\', ' + cell + ')"><option value="0">--</option>';
                    for(var i in type){
                        html += '<option value="' + i + '" ' + (i == dataContext.DataSet[keys] ? 'selected' : '') + '>' + type[i] + '</option>';
                    }
                    html += '</select>';
                } else {
                    html = type[dataContext[keys]] ? type[dataContext[keys]] : '';
                }
                return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
            },
            ImageData: function(row, cell, value, columnDef, dataContext){
                var dataSet = dataContext.DataSet;
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                var _html = '';
                if(value){
					folder = 'new-icon/';
					 if(columnDef.field == 'ProjectAmr.rank'){
						 folder += 'project_rank/';
					 }
                    _html += '<center><img alt="'+ $this.i18n[dataSet[columnDef.field]] +'"  title="'+ $this.i18n[dataSet[columnDef.field]] +'" src="/img/'+ folder + value + '.png"></center>';
                } else {
                    _html += '<span>&nbsp</span>';
                }
                if( !isPM || editable[dataContext.id] || xyz ){
                    return Slick.Formatters.HTMLData(row, cell, '<div class="change-image" data-id="'+ dataContext.id +'" data-image="'+ dataSet[columnDef.field] +'" data-key="'+ columnDef.field +'" onclick="updateWeathers.call(this)">'+_html+'</div>', columnDef, dataContext);
                } else {
                    return Slick.Formatters.HTMLData(row, cell, '<div>'+_html+'</div>', columnDef, dataContext);
                }
            },
            ImageDataNew: function(row, cell, value, columnDef, dataContext){
                var dataSet = dataContext.DataSet;
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                var _html = '';
				
                if(value){
                    _html += '<center><img alt="'+ $this.i18n[dataSet[columnDef.field]] +'"  title="'+ $this.i18n[dataSet[columnDef.field]] +'" src="/img/new-icon/'+ value +'.png"></center>';
                } else {
                    _html += '<span>&nbsp</span>';
                }
                if( !isPM || editable[dataContext.id] || xyz ){
                    return Slick.Formatters.HTMLData(row, cell, '<div class="change-image" data-new = "yes" data-id="'+ dataContext.id +'" data-image="'+ dataSet[columnDef.field] +'" data-key="'+ columnDef.field +'" onclick="updateWeathers.call(this)">'+_html+'</div>', columnDef, dataContext);
                } else {
                    return Slick.Formatters.HTMLData(row, cell, '<div>'+_html+'</div>', columnDef, dataContext);
                }
            },
            yesNoFormatter : function(row, cell, value, columnDef, dataContext){
                return Slick.Formatters.HTMLData(row, cell, value == '1' ? '<?php __('Yes') ?>' : '<?php __('No') ?>', columnDef, dataContext);
            },
            floatFormatter : function(row, cell, value, columnDef, dataContext){
                value = value ? value : 0;
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' +  number_format(value, 2, ',', ' ') + '</span>', columnDef, dataContext);
            },
            numberVal : function(row, cell, value, columnDef, dataContext){
                value = value ? value : 0;
                var icon = '';
                if(columnDef.id == 'ProjectBudgetSyn.assign_to_employee' || columnDef.id == 'ProjectBudgetSyn.assign_to_profit_center'
                || columnDef.id == 'ProjectBudgetSyn.total_costs_var' || columnDef.id == 'ProjectBudgetSyn.internal_costs_var'
                || columnDef.id == 'ProjectBudgetSyn.external_costs_var' || columnDef.id =='ProjectBudgetSyn.external_costs_progress'
                ){
                    icon = '%';
                }
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
            },
            numberValEuro : function(row, cell, value, columnDef, dataContext){
                value = value ? value : 0;
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ')  + ' ' + budget_settings + ' </span>', columnDef, dataContext);
            },
            numberValManDay : function(row, cell, value, columnDef, dataContext){
                value = value ? value : 0;
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +viewManDay+ '</span> ', columnDef, dataContext);
            },
            numberValPercent : function(row, cell, value, columnDef, dataContext){
                value = value ? value : 0;
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +'%'+ '</span> ', columnDef, dataContext);
            },
            linkFormatter : function(row, cell, value, columnDef, dataContext){
                var idPr = dataContext.id ? dataContext.id : 0;
                if($checkDisplayProfileScreen == 2){
                    return '<a href="#" class="show_message_profile project-is-' + idPr + '">' + value + '</a>';
                }
                var linkProjectName = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
                return '<a href=' + linkProjectName +'/'+ dataContext['id'] + ' class="project-is-' + idPr + '">' + value + '</a>';
            },
            avaResource: function (row, cell, value, columnDef, dataContext){
                avatar = '';
				$.each(value, function(key, val){
					if( 'tag' in Object(list_avatar[val])){
						avatar += list_avatar[val]['tag'];
					}
				});
                return avatar;
            },
            nextMilestone: function (row, cell, value, columnDef, dataContext){
                var projectId = dataContext.id ? dataContext.id : 0;
                if(projectId != 0 && listColorNextMil[projectId]){
                    return '<div style="text-align: center; background-color: '+listColorNextMil[projectId]+'">'+value+'</div>';
                }
                return '<div style="text-align: center;">'+value+'</div>'
            }
        });

        var  data = <?php echo json_encode($dataView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        var  leftColumns = <?php echo json_encode($leftColumns);?>;
        $this.canModified =  true;
        $this.fields = {
            id : {defaulValue : 0},
            'ProjectAmr.comment' : {defaulValue : ''},
            'ProjectAmr.project_amr_solution' : {defaulValue : ''},
            'ProjectAmr.project_amr_risk_information' : {defaulValue : ''},
            'ProjectAmr.project_amr_problem_information' : {defaulValue : ''},
            'ProjectAmr.done' : {defaulValue : ''},
            'ProjectAmr.todo' : {defaulValue : ''},
        };
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        $this.columnCalculationConsumeds = <?php echo json_encode($columnCalculationConsumeds);?>;
        $this.columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro);?>;
        $this.columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay);?>;
        $this.columnAlignRightAndPercent = <?php echo json_encode($columnAlignRightAndPercent);?>;
        $this.columnNotCalculationConsumed = <?php echo json_encode($columnNotCalculationConsumed);?>;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        var options = {
            headerRowHeight: 40,
            enableAddRow: false,
            rowHeight: (viewStone) ? 45 : 40,
            gantt: true,
            frozenColumn: leftColumns - 1
        };
        ControlGrid = $this.init($('#project_container'),data,columns,options);
        var exporter = new Slick.DataExporter('/projects/export_excel_index');
        ControlGrid.registerPlugin(exporter);

        $('#export-table').click(function(){
            exporter.submit();
            return false;
        });
        var _gridView;
        $this.onContextMenu = function(gridView){
             _gridView = gridView;
            var cell = gridView.grid.getCellFromEvent(gridView.record);
            var currentRows = gridView.grid.getData().getItems()[cell.row];
            if(!currentRows){
                return;
            } else {
                if(currentRows.project_budget_sale_id){
                    return;
                }
            }
            $('#contextMenu-project')
                .data("row", cell.row)
                .css("top", gridView.record.pageY)
                .css("left", gridView.record.pageX)
                .show();
            $("body").one("click", function () {
                $('#contextMenu-project').hide();
            });
            // $('#contextMenu-project').on( "mousedown", t);
        }
        ControlGrid.onMouseLeave.subscribe(function(e, args) {
            jQuery(e.target).removeClass('hoverCell');
            clearInterval(counter);
        });
        ControlGrid.onMouseEnter.subscribe(function(e, args) {
            if(!enable_popup)return;
            var cell = args.grid.getCellFromEvent(e);
            var me = args.grid.getData().getItem(cell.row);
            var column_subj = columns[cell.cell].field.split('.')[0];


           // alert(columns[cell.cell].field);
            if(columns[cell.cell].field=='Project.created_value'){
                clearInterval(counter);
                jQuery(e.target).addClass('hoverCell');
                counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectCreatedVals(e,me.id)}, 2000);
            } else if(columns[cell.cell].field=='Project.project_name'){
                clearInterval(counter);
                jQuery(e.target).addClass('hoverCell');
                counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectGlobalViews(e,me.id)}, 2000);
            }else if(column_subj=='ProjectFinancePlus'){
                clearInterval(counter);
                jQuery(e.target).addClass('hoverCell');
                var  _cat = '';
                if (columns[cell.cell].field.indexOf("inv") >= 0) _cat = 'investissement';
                if (columns[cell.cell].field.indexOf("fon") >= 0) _cat = 'fonctionnement';
                counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectFinanceGlobalViews(e,me.id, _cat)}, 2000);
            } else {
                if(!see_budget && isPm){
                    return;
                }
                var field=columns[cell.cell].field.substr(0,16);
                if(field=='ProjectBudgetSyn'&&columns[cell.cell].field!='ProjectBudgetSyn.assign_to_profit_center'&&columns[cell.cell].field!='ProjectBudgetSyn.assign_to_employee'){
                    clearInterval(counter);
                    jQuery(e.target).addClass('hoverCell');
                    counter = setInterval(function(){jQuery(e.target).addClass('loading');showProjectBudgetSyn(e,me.id)}, 2000);
                }
            }
        });
        ControlGrid.onScroll.subscribe(function(args, e, scope){
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
        });
        ControlGrid.onSort.subscribe(function(args, e, scope){
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
        });
        ControlGrid.onColumnsResized.subscribe(function (e, args) {
            resizeHandler();
            setupScroll();
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
        });
        var dataView = ControlGrid.getDataView();
        dataView.onRowCountChanged.subscribe(function (e, args) {
                var _leng = ControlGrid.getDataLength();
                var _data='';
                var _i = 0;
                for(_i = 0; _i < _leng; _i++){
                    if( _data) _data += '-' + ControlGrid.getDataItem(_i).id;
                    else _data += ControlGrid.getDataItem(_i).id;
                }
                $.ajax({
                    type:'POST',
                    url: '/projects_preview/save_project_list_filter',
                    data:{
                        path : 'project_list_filter',
                        params : _data,
                    },
                    cache: false,
                    success:function(respon){
                        // /console.log(respon);
                    }
                });
            });
        $(ControlGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
            var text = $(this).val();
            if( text != '' ){
                $(this).parent().css('border', 'solid 2px orange');
            } else {
                $(this).parent().css('border', 'none');
            }
        });
        /**
         * Calculation width of grid.
         */
        var cols = ControlGrid.getColumns();
        var numCols = cols.length;
        var gridW = 0;
        for (var i=0; i<numCols; i++) {
            if($.inArray(cols[i].id, columnsOfGantt) != -1 && viewGantt){
                // do nothing
            } else {
                gridW += cols[i].width;
            }
        }
        if(columns){
            var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">';
            $.each(columns, function(index, value){
                var idOfHeader = value.id;
                var valOfHeader = (totalHeaders[idOfHeader] || totalHeaders[idOfHeader] == 0) ? totalHeaders[idOfHeader] : '';
                if($.inArray(idOfHeader, columnsOfGantt) != -1 && viewGantt){
                    //do nothing
                } else {
                    if($.inArray(idOfHeader, $this.columnAlignRightAndManDay) != -1){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
                    } else if($.inArray(idOfHeader, $this.columnAlignRightAndEuro) != -1){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') +  ' ' + budget_settings;
                    } else {
                        if(valOfHeader){
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ');
                        }
                    }
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l'+index;
                    var right = 'r'+index;
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom '+left+' '+right+'" id="'+idOfHeader+'"><p>'+valOfHeader+'</p></div>';
                }
            });
            headerConsumed += '</div>';
            if(viewGantt){
                var headerConsumedRight = '<div id="wd-header-custom-right" class="slick-headerrow-columns"></div>';
                $('.slick-header-columns-left').after(headerConsumed);
                $('.slick-header-columns-right').after(headerConsumedRight);
                /*
                    layerWidth = 100/12;
                    for(var i = 0; i < 12; i++){
                        offsetLeft = i * layerWidth;
                        headerConsumed += '<div class="gantt-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"></div>';
                    }
                */
               $('.slick-pane.slick-pane-top.slick-pane-right .slick-headerrow-column:not(:last)').each(function(j){
                    var me = $(this);
                    var year = $('.slick-pane-right .slick-header-column').eq(j).text();
                    total = totalDate(year + '-01-01', year + '-12-31') + 1;
                    for(var i = 0; i < 12; i++){
                        m = i+1;
                        m = m < 10 ? '0' + m : m;
                        days = daysInMonth(m, year);
                        layerWidth = days / total * 100;
                        offsetLeft = totalDate(year + '-01-01', year + '-' + m + '-01') / total * 100;
                        me.append('<div class="gantt-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"><span>' + mx[i] + '</span></div>');
                    }
               });
            } else {
                $('.slick-header-columns').after(headerConsumed);
            }
        }
        /* table .end */
        var createDialog = function(){
            $("#ProjectProjectAmrProgramId_port").multiSelect({
                noneSelected: '<?php __("--"); ?>',
                url :'<?php echo $html->url('/projects/get_sub_program/') ?>',
                update : "#ProjectProjectAmrSubProgramId_port",
                loadingClass : 'wd-disable',
                loadingText : 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });

            $("#ProjectProjectAmrProgramId").multiSelect({
                noneSelected: '<?php __("--"); ?>',
                url :'<?php echo $html->url('/projects/get_sub_program/') ?>',
                update : "#ProjectProjectAmrSubProgramId",
                loadingClass : 'wd-disable',
                loadingText : 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });

            $("#ProjectProjectAmrProgramId_sum").multiSelect({
                noneSelected: '<?php __("--"); ?>',
                url :'<?php echo $html->url('/projects/get_sub_program/') ?>',
                update : "#ProjectProjectAmrSubProgramId_sum",
                loadingClass : 'wd-disable',
                loadingText : 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });

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
                    //HistoryFilter.parse();
                }
            });

            $('#dialog_vision_task').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 500,
                show : function(e){
                },
                open : function(e){
                }
            });
            createDialog = $.noop;

            $('#dialog_vision_expectation').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 500,
                show : function(e){
                },
                open : function(e){
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
        $("#vision_task").live('click',function(){
            createDialog();
            $("#dialog_vision_task").dialog('option',{title:''}).dialog('open');
        });
        $(".cancel").live('click',function(){
            $("#dialog_vision_task").dialog('close');
        });
        $("#expectation_screen").live('click',function(){
            createDialog();
            $("#dialog_vision_expectation").dialog('option',{title:''}).dialog('open');
        });
        $(".cancel").live('click',function(){
            $("#dialog_vision_expectation").dialog('close');
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
        $('#CategoryCategory').change(function(){
            location.href = '<?php echo $this->Html->url('/projects_preview/index/-1') ?>?cate=' + $(this).val();
        });
        $('#CategoryStatus').change(function(){
            $('#CategoryStatus option').each(function(){
                if($(this).is(':selected')){
                    viewId = $('#CategoryStatus').val();
                    if(viewId != 0){
                        if(viewId == -1 || viewId == -2){
                            window.location = ('/projects_preview/index/' +viewId+ '?cate=' +cate);
                        } else {
                            window.location = ('/projects_preview/index/' +viewId+ '?cate=' +cate);
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
        if($display_all_name_of_milestones == 1){
            setTimeout(function(){
                $('#MilestonesCheck').trigger('click');
                $('#MilestonesCheck').attr('checked','checked');
            },2000);
        }

        $('#map-icon').click(function(){
            var cat = $('#CategoryCategory').val();
            var length = ControlGrid.getDataLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(ControlGrid.getData().getItem(i).id);
            }
            if( !list.length )return false;
            location.href = '<?php echo $this->Html->url('/projects/map/') ?>' + cat + '/' + list.join('-');
            return false;
        });
        $('#grid-icon').click(function(){
            var cat = $('#CategoryCategory').val();
            var length = ControlGrid.getDataLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(ControlGrid.getData().getItem(i).id);
            }
            if( !list.length )return false;
            location.href = '<?php echo $this->Html->url('/projects/index_plus/') ?>' + cat + '/' + list.join('-');
            return false;
        });
        $('#map-icon-2').click(function(){
            var cat = $('#CategoryCategory').val();
            var length = ControlGrid.getDataLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(ControlGrid.getData().getItem(i).id);
            }
            if( !list.length ){
                location.href = '<?php echo $this->Html->url('/projects/map/') ?>';
                return false;
            }
            location.href = '<?php echo $this->Html->url('/projects/map/') ?>' + cat + '/' + list.join('-');
            return false;
        });
        $('#grid-icon-2').click(function(){
            var cat = $('#CategoryCategory').val();
            var length = ControlGrid.getDataLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(ControlGrid.getData().getItem(i).id);
            }
            if( !list.length ){
                location.href = '<?php echo $this->Html->url('/projects/index_plus/') ?>';
                return false;
            }
            location.href = '<?php echo $this->Html->url('/projects/index_plus/') ?>' + cat + '/' + list.join('-');
            return false;
        });
        <?php echo $this->element('multiSortJs'); ?>;
        $('#export-submit').click(function(){
            var length = ControlGrid.getDataLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(ControlGrid.getData().getItem(i).id);
            }
            $('#export-item-list').val(list.join(',')).closest('form').submit();
        });
        $('#add_portfolio').click(function(){
            var length = ControlGrid.getDataLength();
            var list = [];
            for(var i = 0; i < length ; i++){
                list.push(ControlGrid.getData().getItem(i).id);
            }
            var urlPortfolio = '<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'projects_vision', $appstatus)); ?>';
            urlPortfolio = urlPortfolio+'/'+list.join('-');
            window.location.href = urlPortfolio;
        });
        HistoryFilter.setVal = function(name, value){
            //setupScroll();
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
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
        $('.row-parent').parent().addClass('row-parent-custom');
        $('.row-disabled').parent().addClass('row-disabled-custom');
        $('.row-number').parent().addClass('row-number-custom');
        //scroll to curent year
        var container = $('.slick-viewport.slick-viewport-top.slick-viewport-right'),
            item = $('.gantt-current-month:eq(0)');
        if(container.length && item.length){
            container.scrollTo(item, true, false);
        }
        history_reset = function(){
            var check = false;
            $('.multiselect-filter').each(function(val, ind){
                var text = '';
                if($(ind).find('input').length != 0){
                    text = $(ind).find('input').val();
                } else {
                    text = $(ind).find('span').html();
                    if( text == "<?php __('-- Any --');?>" || text == '-- Any --'){
                        text = '';
                    }
                }
                if( text != '' ){
                    $(ind).css('border', 'solid 2px orange');
                    check = true;
                } else {
                    $(ind).css('border', 'none');
                }
            });
            if(!check){
                $('#reset-filter').addClass('hidden');
            } else {
                $('#reset-filter').removeClass('hidden');
            }
        }
        resetFilter = function(){
            // HistoryFilter.stask = '{}';
            // HistoryFilter.send();
            // $('.multiselect-filter').each(function(val, ind){
                // if($(ind).find('input').length != 0){
                    // $(ind).find('input').val('');
                // } else {
                    // $(ind).find('span').html("<?php __('-- Any --');?>");
                // }
                // $(ind).css('border', 'none');
                // $('#reset-filter').addClass('hidden');
            // });
            // resizeHandler();
            // $.ajax({
                // type:'POST',
                // url: '/projects_preview/save_project_list_filter',
                // data:{
                    // path : 'project_list_filter',
                    // params : {},
                // },
                // cache: false,
                // success:function(respon){
                // }
            // });
            // setTimeout(function(){
                // location.reload();
            // }, 500);
			$('.input-filter').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			ControlGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');
        }
    })(jQuery);
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

    function dialog_confirm(pjId) {
        // event.preventDefault();
        $('.title').html('');
        var dialog = $('#modal_dialog_confirm').dialog();
        $('#btnYes').click(function() {
            dialog.dialog('close');
            // event.stopPropagation();
            delete_project(pjId);
        });
        $('#btnNo').click(function() {
            dialog.dialog('close');
        });
    }
    function delete_project(pjId){
        $.ajax({
            type: 'POST',
            url: _linkDelete +'/'+ pjId,
            data: {
                ajax: true
            },
            dataType: "json",
            success:function(datas) {
                location.reload();
            },
            error:function(){
                location.reload();
            }
        });
    }
    function showPhaseDetail(e, project){
        var e = window.event || e;
        if(e.stopPropagation) {
            e.stopPropagation();
        } else {
            e.returnValue = false;
        }
        var type = 'year';
        var data = 'type='+type+'&ajax=1&call=1';
        $.ajax({
            url: '/project_phase_plans_preview/phase_vision/'+project+'?'+data,
            data: data,
            async: false,
            type:'POST',
            success:function(datas) {
                jQuery('.dragger_container').css({'z-index':10});
                var wh=jQuery(window).height();
                var ww=jQuery(window).width();
                jQuery('#dialogDetailValue').css({'top':"20%",'left':'5%'});
                jQuery('#dialogDetailValue').css({'padding-top':20, 'padding-bottom':5, 'width': 'auto', 'max-width': '90%', 'overflow': 'auto'});
                // if(wh<768){
                //     jQuery('#contentDialog').css({'max-height':600,'width':'auto'});
                // } else {
                //     jQuery('#contentDialog').css({'max-height':900,'width':'auto'});
                // }
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
                    var gantt_ms = $('.gantt-line .gantt-msi');
                    var _conf = 0;
                    max_height = 0;
                    gantt_ms.each(function(){
                        var _this = $(this).find('span');
                        _this.css('margin-left', -( _this.width()/2 - 13) );
                        var _top = parseInt($(this).css('top'));
                        var _topi = _top+10;
                        // console.log(_top, _topi);
                        $(this).children('i').css('top',' -' + _topi + 'px');
                        gantt_ms.each(function(){
                            var _comp_ms = $(this).find('span');
                            if( _this.data('index') > _comp_ms.data('index')){
                                
                                if( ( _this.offset().left >= _comp_ms.offset().left ) && ( _this.offset().left <= (_comp_ms.offset().left + _comp_ms.width()) ) ){
                                    _conf++;
                                    _this.css( 'top', 12*_conf-_top + 'px');

                                }else{
                                    _conf = 0;
                                }
                            }
                        }); 
                        max_height = Math.max(max_height, _this.parent().height()); 
                    });
                    $('.gantt-ms .gantt-line').height(max_height+10);

                },100);
            }
        });
    }
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
                var dataView = ControlGrid.getDataView();
                //update grid
                var row = ControlGrid.getData().getRowById(id);
                var item = ControlGrid.getData().getItem(row);
                dataView.beginUpdate();
                item.DataSet['Project.project_priority_id'] = v;
                item['Project.project_priority_id'] = listPriorities[v] ? listPriorities[v] : '';
                dataView.updateItem(id, item);
                dataView.endUpdate();
                t.prop('disabled', false).css('background-color', '#fff');
                var $input = $('.slick-headerrow-column.l' + cell + ' .multiSelect');
                var o = $input.data('config');
                $('#Project.project_name').trigger('change');
                //utility.syncFilter($input.data("multiSelectOptions").find('input:checked'), o.column);
            }
        });
    }
    // update updateTypeAndProgram
    function updateTypeAndProgram(id, cell){
        var t = $(this).prop('disabled', true).css('background-color', '#eee');
        var v = $(this).val();
        var keys = $(this).data("key");
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updateTypeAndProgram') ?>',
            type: 'POST',
            data: {
                data: {
                    project_id: id,
                    data_id: v,
                    keys: keys
                }
            },
            complete: function(){
                var dataView = ControlGrid.getDataView();
                //update grid
                if(keys == 'Project.project_type_id'){
                    type = listProjectType;
                } else if ( keys == 'Project.project_sub_type_id' ){
                    type = listProjectSubType;
                } else if ( keys == 'Project.project_amr_program_id' ){
                    type = projectProgram;
                } else if ( keys == 'Project.project_amr_sub_program_id' ) {
                    type = projectSubProgram;
                }
                var row = ControlGrid.getData().getRowById(id);
                var item = ControlGrid.getData().getItem(row);
                dataView.beginUpdate();
                item.DataSet[keys] = v;
                item[keys] = type[v] ? type[v] : '';
                dataView.updateItem(id, item);
                dataView.endUpdate();
                t.prop('disabled', false).css('background-color', '#fff');
                var $input = $('.slick-headerrow-column.l' + cell + ' .multiSelect');
                var o = $input.data('config');
                $('#Project.project_name').trigger('change');
                //utility.syncFilter($input.data("multiSelectOptions").find('input:checked'), o.column);
            }
        });
    }
    // hover open popup text
	var _timeout;
    function openPopupText(){
		$('.hover-popup').removeClass('open');
		$('.slick-cell').removeClass('active');
		$(this).closest('.slick-cell').addClass('active  more-style');
		$(this).closest('.slick-cell').find('.hover-popup').addClass('open');
    }
    function closePopupText(){
		clearTimeout(_timeout);
		var elm = this;
		_timeout = setTimeout(function(){
			$(elm).closest('.slick-cell').removeClass('active  more-style');
			$(elm).closest('.slick-cell').find('.hover-popup').removeClass('open');
		}, 300);
        
    }
    // End hover open popup text
	
    // update weather
    // function updateWeathers(){
    //         var t = $(this),
    //             checked = false,
    //             image = t.data('image'),
    //             key = t.data('key'),
    //             id = t.data('id'),
    //             _html = '';
    //         _html += '<div class="wd-popup-weather"><div class="wd-input wd-weather-list-dd"><ul>';
    //         if(key == 'ProjectAmr.rank'){
    //             _html += '<li><input id="weather-up" style="margin-top: 8px" value="up" class="weather weather-up" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/up.svg"></li>';
    //             _html += '<li><input id="weather-mid" style="margin-top: 8px" value="mid" class="weather weather-mid" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/mid.svg"></li>';
    //             _html += '<li><input id="weather-down" style="margin-top: 8px" value="down" class="weather weather-down" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/down.svg"></li>';
    //         }else{
    //             _html += '<li><input id="weather-sun" style="margin-top: 8px" value="sun" class="weather weather-sun" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/new-icon/sun.png"></li>';
    //             _html += '<li><input id="weather-cloud" style="margin-top: 8px" value="cloud" class="weather weather-cloud" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/new-icon/cloud.png"></li>';
    //             _html += '<li><input id="weather-rain" style="margin-top: 8px" value="rain" class="weather weather-rain" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/new-icon/rain.png"></li>';
    //         }
    //         _html += '</ul></div></div>';
    //         $(this).append(_html);
    //         $(this).find('#weather-' + image).attr("checked", "checked");
    //         $('.weather').click(function(){
    //             var val = $(this).val();
    //             // update weather
    //             if(val != image){
    //                 $.ajax({
    //                     url: '/projects/updateWeather/',
    //                     type: 'POST',
    //                     data: {
    //                         data: {
    //                             project_id : id,
    //                             key: key,
    //                             val: val
    //                         }
    //                     },
    //                     success: function(){
    //                         // image = val;
    //                         checked = true;
    //                         t.data('image', val);
    //                         t.find('#weather-' + val).attr("checked", "checked");
    //                         t.html('<div class="change-image" data-id="'+ id +'" data-image="'+ image +'" data-key="'+ key+'"><center><img src="<?php echo $this->Html->url('/') ?>img/new-icon/'+val+'.png"></center></div>');
    //                     }
    //                 });
    //             }
    //         });
    //         // $(this).mouseleave(function(){
    //         //     _image = t.data('image');
    //         //     if( !checked ){
    //         //         if (_image === 'undefined'){
    //         //             $(this).html('<span>&nbsp</span>');
    //         //         } else {
    //         //             $(this).html('<center><img src="<?php echo $this->Html->url('/') ?>img/new-icon/'+_image+'.png"></center>');
    //         //         }
    //         //     }
    //         // });
    //     }
    /* Edit by huynh 11/07/2018
    -*/
    function updateWeathers(){
        var t = $(this),
            checked = false,
            image = t.data('image'),
            key = t.data('key'),
            id = t.data('id'),
			isNew = t.data('new'),
            _html = '', 
			_ex_class= '';
		if(isNew){
			_ex_class= 'fit-content';
		}
		$(this).closest('.slick-cell').addClass('active  more-style');
        _html += '<div class="wd-popup-weather '+ _ex_class +'"><div class="wd-input wd-weather-list-dd"><ul>';
        if(key == 'ProjectAmr.rank'){ 
            _html += '<li id="weather-up" data-value="up" class="weather weather-up"> <img style="float: none" title="Up" src="<?php echo $this->Html->url('/') ?>img/new-icon/project_rank/up.png"</li>';
            _html += '<li id="weather-mid" data-value="mid" class="weather weather-mid"> <img style="float: none" title="Mid" src="<?php echo $this->Html->url('/') ?>img/new-icon/project_rank/mid.png"</li>';
            _html += '<li id="weather-down" data-value="down" class="weather weather-down"> <img style="float: none" title="Down" src="<?php echo $this->Html->url('/') ?>img/new-icon/project_rank/down.png"</li>';
        }else{
            _html += '<li id="weather-sun" data-value="sun" class="weather weather-sun"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/new-icon/sun.png"</li>';
			if(isNew){
				_html += '<li id="weather-fair" data-value="fair" class="weather weather-fair"> <img style="float: none" title="Fair" src="<?php echo $this->Html->url('/') ?>img/new-icon/fair.png"</li>';
			}
            _html += '<li id="weather-cloud" data-value="cloud" class="weather weather-cloud"> <img style="float: none" title="Cloud" src="<?php echo $this->Html->url('/') ?>img/new-icon/cloud.png"</li>';
			if(isNew){
				_html += '<li id="weather-furry" data-value="furry" class="weather weather-furry"> <img style="float: none" title="Furry" src="<?php echo $this->Html->url('/') ?>img/new-icon/furry.png"</li>';
			}
            _html += '<li id="weather-rain" data-value="rain" class="weather weather-rain"> <img style="float: none" title="Rain" src="<?php echo $this->Html->url('/') ?>img/new-icon/rain.png"</li>';
        }
        _html += '</ul></div></div>';
        if (!$(this).find('.wd-popup-weather').length) {
            var _this = $(this);
            _this.append(_html);
            var _table_top = _this.closest('.slick-viewport').offset().top;
            var _this_top = _this.find('.wd-popup-weather ul').offset().top;
            if (_this_top < _table_top)
                _this.find('.wd-popup-weather ul').css('top', '38px');
			if( _this.closest('.slick-pane').length){
				var _view_right = _this.closest('.slick-pane').offset().left + _this.closest('.slick-pane').width();
				var _view_left = _this.closest('.slick-pane').offset().left;
			}else{
				var _view_right = _this.closest('.slick-viewport').offset().left + _this.closest('.slick-viewport').width();
				var _view_left = _this.closest('.slick-viewport').offset().left;
			}
            var _popup_right = _this.find('.wd-popup-weather ul').offset().left + _this.find('.wd-popup-weather ul').width();
            var _popup_left = _this.find('.wd-popup-weather ul').offset().left;
            if (_popup_left < _view_left) {
                _this.find('.wd-popup-weather ul').css({
                    'left': '0',
                    'transform': 'none'
                });
            } else if (_popup_right > _view_right) {
                _this.find('.wd-popup-weather ul').css({
                    'left': 'inherit',
                    'transform': 'none',
                    'right': '0'

                });
            } else {
                _this.find('.wd-popup-weather ul').css({
                    'left': '',
                    'right': '0',
                    'transform': ''
                });
            }
        }
        $(this).find('#weather-' + image).addClass('selected');
        $('.weather').click(function(){
           if(! $(this).hasClass('selected')){
                var val = $(this).data('value');
                t.addClass('wt-loading');
                // update weather
                if(val != image){
                    $.ajax({
                        url: '/projects/updateWeather/',
                        type: 'POST',
                        data: {
                            data: {
                                project_id : id,
                                key: key,
                                val: val
                            }
                        },
                        success: function(){
                            // image = val;
                            checked = true;
                            t.data('image', val);
                            t.find('#weather-' + val).addClass('selected');
                            t.html('<div class="change-image" data-id="'+ id +'" data-image="'+ image +'" data-key="'+ key+'"><center><img src="<?php echo $this->Html->url('/') ?>img/new-icon/'+val+'.png"></center></div>');
                            t.removeClass('wt-loading');
                        }
                    });
                }
            }
        });
        $(this).mouseleave(function(){
            _image = t.data('image');
            if( !checked ){
                if (_image === 'undefined'){
                   $(this).html('<span>&nbsp</span>');
                } else {
                    $(this).html('<center><img src="<?php echo $this->Html->url('/') ?>img/new-icon/'+_image+'.png"></center>');
                }
            }
        });
    }
    //qtip
    var i18n = <?php echo json_encode(array(
        'startday' => __('Start date', true),
        'enddate' => __('End date', true),
        'progress' => __('Progress', true),
        'resource' => __('Resource', true),
        'date' => __('Date', true),
    )) ?>;
    $('#project_container').on('mouseenter', '.hover-tooltip-cus', function(ev){
        var selector = $(this);
        var data = {
            title: selector.find('.hover-data-name').text(),
            start: selector.find('.hover-data-start').text(),
            end: selector.find('.hover-data-end').text(),
            progress: selector.find('.hover-data-comp').text(),
            resource: selector.find('.hover-data-assign').text()
        }
        selector.qtip({
            overwrite: false,
            show: {
                solo: true,
                event: ev.type, // Use the same event type as above
                ready: true // Show immediately - important!
            },
            hide: 'click mouseleave',
            content: {
                text: function(e, api){
                    var content = $('<dl class="hover-content"></dl>');
                    content.append('<dt>' + i18n.startday + '</dt>');
                    content.append('<dd>' + data.start + '</dd>');
                    content.append('<dt>' + i18n.enddate + '</dt>');
                    content.append('<dd>' + data.end + '</dd>');
                    content.append('<dt>' + i18n.progress + '</dt>');
                    content.append('<dd>' + data.progress + '</dd>');
                    content.append('<dt>' + i18n.resource + '</dt>');
                    content.append('<dd>' + data.resource + '</dd>');
                    return content;
                },
                title: data.title
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                target: 'mouse',
                adjust: {
                    mouse: false
                }
            },
            style: {
                classes: 'qtip-shadow qtip-xlight'
            }
        });
    });
    $('#project_container').on('mouseenter click', '.gantt-msi', function(ev){
        var selector = $(this);
        var data = {
            title: selector.find('.hover-stone-name').text(),
            date: selector.find('.hover-stone-date').text()
        }
        selector.qtip({
            overwrite: false,
            show: {
                solo: true,
                event: ev.type, // Use the same event type as above
                ready: true // Show immediately - important!
            },
            hide: 'mouseleave',
            content: {
                text: function(e, api){
                    var content = $('<dl class="hover-content"></dl>');
                    content.append('<dt>' + i18n.date + '</dt>');
                    content.append('<dd>' + data.date + '</dd>');
                    return content;
                },
                title: data.title
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                target: 'mouse',
                adjust: {
                    mouse: false
                }
            },
            style: {
                classes: 'qtip-shadow qtip-xlight'
            }
        });
    });
    function updateText() {
        id = $(this).data("id");
        model = $(this).data("model");
        field = $(this).data("field");
        var _html = '';
        var latest_update = '';

        var popup = $('#template_logs');
        $.ajax({
            url: '/projects_preview/getComment',
            type: 'POST',
            data: {
                id: id,
                model: model,
            },
            dataType: 'json',
            success: function(data) {
                _html = '<p class="project-name">'+data['project_name']+'</p>';
                html = '<div id="content-comment-id">';
                if (data) {
                    latest_update = '';
                    _html += '<div class="comment"><textarea onfocusout="updateTextComment.call(this)" data-id = '+ id +' data-field = '+ field +' data-model = '+ model +' cols="30" rows="6" id="update-comment"></textarea></div>';
					_html += '<div class="content-logs">';
					if( data[0]){
						latest_update = text_modified + ' ' + data[0]['updated'] + ' ' + text_by + ' ' +data[0]['update_by_employee'];
						var i = 0;
						$.each(data, function(ind, _data) {
							
							if(_data && ('id' in Object(_data)) ){
								name = ava_src = '';
								comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
								date = _data['updated'];
								// if(i == 0){
									// _html += '<div class="content content-'+ i++ +'"><p>'+ date +'</p><div class="comment"><textarea onfocusout="updateTextComment.call(this)" data-id = '+ id +' data-field = '+ field +' cols="30" rows="6" id="update-comment"></textarea></div></div>';
								// }
								if( $.inArray( _data['employee_id'] ,employeeHasAvatar !== '-1') ){
                                    ava_src += '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%',_data['employee_id'] ) +'" title = "'+ _data['name'] +'" />';
                                }else{
									name = _data['name'].split(" ");
									ava_src += '<span class="circle-name" style="width: 30px; height: 30px; line-height: 30px; font-size: 14px;" title = "'+_data['name']+'">'+name[0]+''+name[1]+'</span>';

								}
								 _html += '<div class="content content-'+ i++ +'"><div class="avatar">'+ ava_src +'</div><div class="item-content"><p>'+ date +'</p><div class="comment">'+ comment +'</div></div></div>';
							} 
							i++;                       
						});
					}
					_html += '</div>';
                }
				
                var class_pro ='';
                if(data['initDate'] > 50) class_pro = 'late-progress';
                _html_progress ="<div class='log-progress'><div class='project-progress "+ class_pro +"'><p class='progress-full'>" + draw_line_progress(data['initDate']) + "</p><p>"+ data['initDate'] +"%</p></div>";
                _html+= _html_progress;
                // _html += '<div class="log-progress"> <div class="project-progress"><p class="progress-full"><span class="progress-content" data style="width: '+data['initDate']+'%"></span><span>'+data['initDate']+'%</span></p></div>';
                _html += '<div class="logs-info"><p class="update-by-employee">'+ latest_update +'</p></div></div>';
                $('#content_comment').html(_html);
                
                var createDialog2 = function(){
                    $('#template_logs').dialog({
                        position    :'center',
                        autoOpen    : false,
                        height      : 420,
                        modal       : true,
                        width       : (isTablet || isMobile) ?  320 : 520,
                        minHeight   : 50,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog2 = $.noop;
                }
                createDialog2();
                $("#template_logs").dialog('option',{title:''}).dialog('open');
                
            }
        });
       
    };
    // add dialog vision task
    $(function(){
        $("#startDateVision, #endDateVision").datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        });
    });
    var multiStatusProject = $('#category-id').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiStatusProject = $('#statusProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiProgramProject = $('#programProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilSubProgramProject = $('#subProgramProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilStatusTask = $('#statusTask').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilProrityTask = $('#prorityTask').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    $('#export-assign-employee').multipleSelect({
        // minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>',
        oneOrMoreSelected: '*',
        selectAll: false
    });
    var mutilCodeProject = $('#codeProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var mutilCodeProject1 = $('#codeProject1').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var milestone = $('#milestone').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var timeout3;
    var multiAssign = $('#export-assign-team').multipleSelect({
        minimumCountSelected: 0,
        position: 'top',
        placeholder: '<?php __("-- Any --") ?>',
        onClick: function(view){
            clearTimeout(timeout3);
            timeout3 = setTimeout(function(){
                updatePcAndResource(multiAssign, $('#export-assign-employee'), view.complete);
            }, 1000);
        }
    });
    var totalPC = <?php echo isset($listProfitCenter) ? json_encode(count($listProfitCenter)) : 0 ?>;
    function updatePcAndResource(loader, filler, callback){
        var placeholder = filler.multipleSelect('getPlaceholder'),
            list = loader.multipleSelect('getSelects');
        $.ajax({
            url: '<?php echo $html->url('/activities/get_employee_for_profit_center/') ?>',
            cache : true,
            data: {
                data: list
            },
            dataType: 'json',
            beforeSend: function(){
                placeholder.addClass('loading');
                loader.multipleSelect('disable');
                loader.multipleSelect('disableCheckboxes');
            },
            success: function(data){
                //update filler
                filler.html(data.html);
                filler.multipleSelect('refresh');
                //update loader
                if( $.isArray(data.pc) ){
                    data.pc = $.merge(list, data.pc);
                } else {
                    var pc = [];
                    $.each(data.pc, function(i, v){
                        pc.push(v);
                    });
                    data.pc = $.merge(list, pc);
                }
                loader.multipleSelect('setSelects', data.pc);
                if( $.isFunction(callback) ){
                    callback(loader, filler, data);
                }
            },
            complete: function(){
                placeholder.removeClass('loading');
                loader.multipleSelect('enable');
                loader.multipleSelect('enableCheckboxes');
                //set select pc all
                var instance = loader.multipleSelect('getInstance');
                var total = instance.$selectItems.filter(':checked').length;
                if(totalPC == total){
                    $('#ActivitySelectPCAll').val('true');
                } else {
                    $('#ActivitySelectPCAll').val('false');
                }
            }
        });
    }
    var updateTextComment = function(){
        var text = $(this).val(),
        field = $(this).data("field"),
		model = $(this).data("model"),
        id = $(this).data("id");
        if(text != ''){
            var _html = '';
             $('#update-comment').closest('#content_comment').addClass('loading');
            $.ajax({
                url: '/projects_preview/update',
                type: 'POST',
                data: {
                    data:{
                        id: id,
                        text: text,
                        field: field,
						model: model,
                    }
                },
                dataType: 'json',
                success: function(data) {
                    _html = _log_progress = '';
                    // html = '<div id="content-comment-id">';
                    if (data) {
                        latest_update = text_modified + ' ' + data[0]['updated'] + ' ' + text_by + ' ' +data[0]['update_by_employee'];
                        i = 1;
                        $.each(data, function(ind, _data) {                            
                            if(_data && ('id' in Object(_data)) ){
                                name = ava_src = '';
                                comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
                                date = _data['updated'];
                                if( $.inArray( _data['employee_id'] ,employeeHasAvatar !== '-1') ){
                                    ava_src += '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%',_data['employee_id'] ) +'" title = "'+ _data['name'] +'" />';
                                }else{
                                    name = _data['name'].split(" ");
                                    ava_src += '<span class="circle-name" style="width: 30px; height: 30px; line-height: 30px; font-size: 14px;" title = "'+_data['name']+'">'+name[0]+''+name[1]+'</span>';

                                }
                                
                                _html += '<div class="content content-'+ i++ +'"><div class="avatar">'+ ava_src +'</div><div class="item-content"><p>'+ date +'</p><div class="comment">'+ comment +'</div></div></div>';
                            }                       
                        });
                    } else {
                        _html += '';
                    }
                    var class_pro ='';
                    if(latest_update) _log_progress += '<p class="update-by-employee">'+ latest_update +'</p>';
                    // $('#content_comment').html(_html);
                    $('#template_logs .content-logs').empty().append(_html);
					$('#update-comment').val('');
                    $('#template_logs .log-progress .logs-info').empty().append(_log_progress);
                    $('#update-comment').closest('#content_comment').removeClass('loading');
					/* Update to grid */
					if( data){
						var dataView = ControlGrid.getDataView();
						var row = ControlGrid.getData().getRowById(id);
						// var this_item = dataView.getItemById(id);
						this_item = dataView.getItemById(id);
						dataView.beginUpdate();
						item_name = 'ProjectAmr.'+field;
						this_item[item_name] = data[0];
						id = id.toString();
						dataView.updateItem(id, this_item);
						dataView.endUpdate();
						ControlGrid.render();
					}
					/* End Update to grid */
                }
            });
        }
    };
    $("#reset_sum_team").click(function(){
        $('#category-id').multipleSelect('setSelects', []);
        $('#statusProject').multipleSelect('setSelects', []);
        $('#programProject').multipleSelect('setSelects', []);
        $('#subProgramProject').multipleSelect('setSelects', []);
        $('#statusTask').multipleSelect('setSelects', []);
        $('#prorityTask').multipleSelect('setSelects', []);
        $('#taskProject').val('');
        $('#export-assign-team').multipleSelect('setSelects', []);
        $('#export-assign-employee').multipleSelect('setSelects', []);
        $('#codeProject').multipleSelect('setSelects', []);
        $('#codeProject1').multipleSelect('setSelects', []);
        $('#milestone').multipleSelect('setSelects', []);
        $('#startDateVision').val('');
        $('#endDateVision').val('');
        vsFilter.set('nameCategory', []);
        vsFilter.set('nameStatusProject', []);
        vsFilter.set('nameProgramProject', []);
        vsFilter.set('nameSubProgramProject', []);
        vsFilter.set('nameStatusTask', []);
        vsFilter.set('nameProrityTask', []);
        vsFilter.set('assignedTeam', []);
        vsFilter.set('assignedResources', []);
        vsFilter.set('nameCodeProject', []);
        vsFilter.set('milestone', []);
        vsFilter.set('nameTaskProject', '');
        vsFilter.set('nameStartDateVision', '');
        vsFilter.set('nameEndDateVision', '');
        return false;
    });
    // submit vision task.
    var vsFilter = {};
    $.z0.History.load('vision_task', function(data){
        vsFilter = data;
        var nameCategory = data.get('nameCategory', []);
        $('#category-id').multipleSelect('setSelects', nameCategory);
        var nameStatusProject = data.get('nameStatusProject', []);
        $('#statusProject').multipleSelect('setSelects', nameStatusProject);
        var nameProgramProject = data.get('nameProgramProject', []);
        $('#programProject').multipleSelect('setSelects', nameProgramProject);
        var nameSubProgramProject = data.get('nameSubProgramProject', []);
        $('#subProgramProject').multipleSelect('setSelects', nameSubProgramProject);
        var nameStatusTask = data.get('nameStatusTask', []);
        $('#statusTask').multipleSelect('setSelects', nameStatusTask);
        var nameProrityTask = data.get('nameProrityTask', []);
        $('#prorityTask').multipleSelect('setSelects', nameProrityTask);
        var nameTaskProject = data.get('nameTaskProject');
        $('#taskProject').val(nameTaskProject);
        var assignedTeam = data.get('assignedTeam', []);
        $('#export-assign-team').multipleSelect('setSelects', assignedTeam);
        var assignedResources = data.get('assignedResources', []);
        $('#export-assign-employee').multipleSelect('setSelects', assignedResources);
        var nameCodeProject = data.get('nameCodeProject', []);
        $('#codeProject').multipleSelect('setSelects', nameCodeProject);
        var nameCodeProject1 = data.get('nameCodeProject1', []);
        $('#codeProject1').multipleSelect('setSelects', nameCodeProject1);
        var nameMilestone = data.get('nameMilestone', []);
        $('#milestone').multipleSelect('setSelects', nameMilestone);
        var nameStartDateVision = data.get('nameStartDateVision');
        $('#startDateVision').val(nameStartDateVision);
        var nameEndDateVision = data.get('nameEndDateVision');
        $('#endDateVision').val(nameEndDateVision);
    });
    var urlExport = <?php echo json_encode($this->Html->url(array('controller' => 'projects', 'action' => 'export_vision_task')))?>;
    var urlTaskVision = <?php echo json_encode($this->Html->url(array('controller' => 'projects', 'action' => 'tasks_vision')))?>;
    $("#ok_sum_vision").click(function(){
        vsFilter.set('nameCategory', $('#category-id').multipleSelect('getSelects'));
        vsFilter.set('nameStatusProject', $('#statusProject').multipleSelect('getSelects'));
        vsFilter.set('nameProgramProject', $('#programProject').multipleSelect('getSelects'));
        vsFilter.set('nameSubProgramProject', $('#subProgramProject').multipleSelect('getSelects'));
        vsFilter.set('nameStatusTask', $('#statusTask').multipleSelect('getSelects'));
        vsFilter.set('nameProrityTask', $('#prorityTask').multipleSelect('getSelects'));
        vsFilter.set('nameTaskProject', $('#taskProject').val());
        vsFilter.set('assignedTeam', $('#export-assign-team').multipleSelect('getSelects'));
        vsFilter.set('assignedResources', $('#export-assign-employee').multipleSelect('getSelects'));
        vsFilter.set('nameStartDateVision', $('#startDateVision').val());
        vsFilter.set('nameEndDateVision', $('#endDateVision').val());
        vsFilter.set('nameCodeProject', $('#codeProject').multipleSelect('getSelects'));
        vsFilter.set('nameCodeProject1', $('#codeProject1').multipleSelect('getSelects'));
        vsFilter.set('nameMilestone', $('#milestone').multipleSelect('getSelects'));
        //save filter
        $.z0.History.save('vision_task', vsFilter);
        //submit
        $('#form_vision_task').attr('action', urlExport);
        setTimeout(function(){
            $("#form_vision_task").submit();
        }, 750);
    });
    var task_not_found = <?php echo json_encode(__('Task not found', true)); ?>,
    task_found = <?php echo json_encode(__(' task found', true)); ?>,
    tasks_found = <?php echo json_encode(__(' tasks found', true)); ?>;
    $('#startDateVision , #endDateVision').change(function(){
        var _taskName = $('#taskProject').val(),
            _start = $('#startDateVision').val(),
            _end = $('#endDateVision').val();
            checkTaskForTaskVision(_taskName, _start, _end);
    });
    $('#taskProject').focusout(function(){
        var _taskName = $('#taskProject').val(),
            _start = $('#startDateVision').val(),
            _end = $('#endDateVision').val();
            checkTaskForTaskVision(_taskName, _start, _end);
    });
    function checkTaskForTaskVision(_taskName, _start, _end){
        $.ajax({
            url: '<?php echo $html->url('/projects/check_task_for_task_vision/') ?>',
            data: {
                task: _taskName,
                start: _start,
                end: _end
            },
            type: 'POST',
            dataType: 'json',
            success: function(result){
                if(result > 0){
                    var t = (result > 1) ? tasks_found : task_found;
                    $('#show_count_task').text(result + t).css('color', 'blue');
                } else {
                    $('#show_count_task').text(task_not_found).css('color', 'red');
                }
            }
        });
    }
    function openSumrow(){
        $(this).toggleClass('active');
        var parent = $(this).closest('.wd-list-project');
        parent.find('.slick-header #wd-header-custom').toggle();
    }
    $('#full_screen_vision').click(function(){
        vsFilter.set('nameCategory', $('#category-id').multipleSelect('getSelects'));
        vsFilter.set('nameStatusProject', $('#statusProject').multipleSelect('getSelects'));
        vsFilter.set('nameProgramProject', $('#programProject').multipleSelect('getSelects'));
        vsFilter.set('nameSubProgramProject', $('#subProgramProject').multipleSelect('getSelects'));
        vsFilter.set('nameStatusTask', $('#statusTask').multipleSelect('getSelects'));
        vsFilter.set('nameProrityTask', $('#prorityTask').multipleSelect('getSelects'));
        vsFilter.set('nameTaskProject', $('#taskProject').val());
        vsFilter.set('assignedTeam', $('#export-assign-team').multipleSelect('getSelects'));
        vsFilter.set('assignedResources', $('#export-assign-employee').multipleSelect('getSelects'));
        vsFilter.set('nameStartDateVision', $('#startDateVision').val());
        vsFilter.set('nameEndDateVision', $('#endDateVision').val());
        vsFilter.set('nameCodeProject', $('#codeProject').multipleSelect('getSelects'));
        vsFilter.set('nameCodeProject1', $('#codeProject1').multipleSelect('getSelects'));
        vsFilter.set('nameMilestone', $('#milestone').multipleSelect('getSelects'));
        //save filter
        $.z0.History.save('vision_task', vsFilter);
        $('#form_vision_task').attr('action', urlTaskVision);
        setTimeout(function(){
            $("#form_vision_task").submit();
        }, 750);
    });
    // dialog expectation.
    $(function(){
        $("#startDateExpec, #endDateExpec").datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        });
    });
    var multiStatusProject = $('#CatePExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiStatusProject = $('#StatusPExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiProgramProject = $('#ProgramPExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilSubProgramProject = $('#SubProgramPExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    $('#AssignRessourceExpec').multipleSelect({
        // minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>',
        oneOrMoreSelected: '*',
        selectAll: false
    });
    var timeout3;
    var multiAssign = $('#AssignTeamExpec').multipleSelect({
        minimumCountSelected: 0,
        position: 'top',
        placeholder: '<?php __("-- Any --") ?>',
        onClick: function(view){
            clearTimeout(timeout3);
            timeout3 = setTimeout(function(){
                updatePcAndResource(multiAssign, $('#AssignRessourceExpec'), view.complete);
            }, 1000);
        }
    });
    var totalPC = <?php echo isset($listProfitCenter) ? json_encode(count($listProfitCenter)) : 0 ?>;
    $("#reset_vision_expec").click(function(){
        $('#CatePExpec').multipleSelect('setSelects', []);
        $('#StatusPExpec').multipleSelect('setSelects', []);
        $('#ProgramPExpec').multipleSelect('setSelects', []);
        $('#SubProgramPExpec').multipleSelect('setSelects', []);
        $('#nameExpec').val('');
        $('#AssignTeamExpec').multipleSelect('setSelects', []);
        $('#AssignRessourceExpec').multipleSelect('setSelects', []);
        $('#ScreenExpec').val('');
        $('#startDateExpec').val('');
        $('#endDateExpec').val('');
        vsFilter.set('cateExpec', []);
        vsFilter.set('StatusPExpec', []);
        vsFilter.set('ProgramPExpec', []);
        vsFilter.set('SubProgramPExpec', []);
        vsFilter.set('AssignTeamExpec', []);
        vsFilter.set('AssignRessourceExpec', []);
        vsFilter.set('screen', '');
        vsFilter.set('startDateExpec', '');
        vsFilter.set('endDateExpec', '');
        return false;
    });
    // submit vision task.
    var vsFilter = {};
    $.z0.History.load('vision_expectaion', function(data){
        vsFilter = data;
        var nameCategory = data.get('cateExpec', []);
        $('#CatePExpec').multipleSelect('setSelects', nameCategory);
        var nameStatusProject = data.get('StatusPExpec', []);
        $('#StatusPExpec').multipleSelect('setSelects', nameStatusProject);
        var nameProgramProject = data.get('ProgramPExpec', []);
        $('#ProgramPExpec').multipleSelect('setSelects', nameProgramProject);
        var nameSubProgramProject = data.get('SubProgramPExpec', []);
        $('#SubProgramPExpec').multipleSelect('setSelects', nameSubProgramProject);
        var nameTaskProject = data.get('nameExpec');
        $('#nameExpec').val(nameTaskProject);
        var assignedTeam = data.get('AssignTeamExpec', []);
        $('#AssignTeamExpec').multipleSelect('setSelects', assignedTeam);
        var assignedResources = data.get('AssignRessourceExpec', []);
        $('#AssignRessourceExpec').multipleSelect('setSelects', assignedResources);
        var scr = data.get('screen', []);
        $('#ScreenExpec').val(scr);
        var nameStartDateVision = data.get('startDateExpec');
        $('#startDateExpec').val(nameStartDateVision);
        var nameEndDateVision = data.get('endDateExpec');
        $('#endDateExpec').val(nameEndDateVision);
    });
    var urlExpectation = <?php echo json_encode($this->Html->url(array('controller' => 'project_expectations', 'action' => 'vision')))?>;
    $("#full_screen_expectation").click(function(){
        vsFilter.set('cateExpec', $('#CatePExpec').multipleSelect('getSelects'));
        vsFilter.set('StatusPExpec', $('#StatusPExpec').multipleSelect('getSelects'));
        vsFilter.set('ProgramPExpec', $('#ProgramPExpec').multipleSelect('getSelects'));
        vsFilter.set('SubProgramPExpec', $('#SubProgramPExpec').multipleSelect('getSelects'));
        vsFilter.set('nameExpec', $('#nameExpec').val());
        vsFilter.set('AssignTeamExpec', $('#AssignTeamExpec').multipleSelect('getSelects'));
        vsFilter.set('AssignRessourceExpec', $('#AssignRessourceExpec').multipleSelect('getSelects'));
        vsFilter.set('startDateExpec', $('#startDateExpec').val());
        vsFilter.set('endDateExpec', $('#endDateExpec').val());
        vsFilter.set('screen', $('#ScreenExpec').val());
        //save filter
        $.z0.History.save('vision_expectaion', vsFilter);
        //submit
        $('#form_vision_expectation').attr('action', urlExpectation);
        setTimeout(function(){
            $("#form_vision_expectation").submit();
        }, 750);
    });
    //scroll to curent year
    var container = $('.slick-viewport.slick-viewport-top.slick-viewport-right'),
        item = $('.gantt-current-month:eq(0)');

    if(container.length && item.length){
        container.scrollTo(item, true, false);
    }
    var $companyConfigs = <?php echo json_encode($companyConfigs) ?>;
    if($companyConfigs['display_muti_sort'] == 0){
        $('#btnCont').hide();
    }
    function setupScroll(){
        if($('.slick-header-right').length != 0){
            var right = $('.slick-header-right').width();

            $("#scrollTopAbsenceContent").width($('.grid-canvas-right').width()+50);
            $("#scrollTopAbsence").width(right);
        } else {
            var x = 0;
            $(".grid-canvas").each(function(val, ind){
                x += $(ind).width();
            });
            $("#scrollTopAbsenceContent").width(x+50);
            $("#scrollTopAbsence").width($("#project_container").width());
        }
    }
    $("#scrollTopAbsence").scroll(function () {
        if($('.slick-header-right').length != 0){
            $('.slick-viewport-right').scrollLeft($("#scrollTopAbsence").scrollLeft());
        } else {
            $(".slick-viewport").scrollLeft($("#scrollTopAbsence").scrollLeft());
        }
    });
    $(".slick-viewport").scroll(function () {
        if($('.slick-header-right').length != 0){
            $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right").scrollLeft());
        } else {
            $("#scrollTopAbsence").scrollLeft($(".slick-viewport").scrollLeft());
        }
    });
    //setupScroll();
    $('.multiselect-filter').find('a').each(function(val, index){
        $(index).attr('href', '');
    });
    $("#dialogDetailValue").draggable({
        cancel: "#wd-fragment-2, #wd-tab-content, .dialog_no_drag",
        stop: function(event, ui){
            var position = $("#dialogDetailValue").position();
            $.ajax({
                url : "/projects/savePopupPositions",
                type: "POST",
                data: {
                    top: position.top,
                    left: position.left
                }
            });
            savePosition = position;
        }
    });
    $('.show_message_profile').click(function(){
        alert('<?php echo __("With your profile, you cannot see project screen", true); ?>');
    });
    function openFilter(){
        var project_filter = $('.open-filter-form').closest('.wd-project-filter');
        project_filter.find('.search-filter').toggleClass('active');
        if( $(window).width() > 992){
            $('.search-filter').css('left',0-($('.wd-project-filter').position().left)-parseInt($('.wd-project-filter').css('padding-left')));
        }else{
            $('.search-filter').css('left','');
        }
        $('body').find('.wd-project-admin').toggleClass('active');
    }
    $('.close-filter').click(function(){
        $(this).closest('.search-filter').toggleClass('active');
        $('body').find('.wd-project-admin').toggleClass('active');
    });
    var header_table_height = 0;
    $('.slick-header').each(function(){
        _height = $(this).height();
        header_table_height = Math.max( header_table_height, _height);
    });
    var header_row_height = $('.slick-headerrow:first').height();
    wdTable.find('.slick-viewport').css({
        height: heightTable - header_table_height - header_row_height - 5,
    });

    $(window).ready(function(){
        setTimeout(function(){
            if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
                $('#addProjectTemplate').addClass('loading');
                $.ajax({
                    url : "/projects_preview/add_popup/",
                    type: "GET",
                    cache: false,
                    success: function (html) {
                        $('#addProjectTemplate').empty().html(html);
                        $('#add-form').trigger('reset');
                        $(window).trigger('resize');
                        $('#addProjectTemplate').addClass('loaded');
                        $('#addProjectTemplate').removeClass('loading');
                    }
                });
            }
        }, 2000);
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
