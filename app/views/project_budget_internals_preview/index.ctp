<style type="text/css">
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
</style>
<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'preview/project_budget_external',
));
echo $this->Html->script(array(
    'history_filter',
    'jquery.multiSelect',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min',
	'progresspie/jquery-progresspiesvg-min.js',
	'dashboard/jqx-all',
	'dashboard/jqxchart_preview',
	'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider'
));
echo $this->element('dialog_projects');
$menu = $this->requestAction('/menus/getMenu/project_budget_internals/index');
$langCode = Configure::read('Config.langCode');
if( $langCode == 'fr' )$langCode .= 'e';
else if( $langCode == 'en' )$langCode .= 'g';
$canModified = (($modifyBudget == true && !$_isProfile) || ($_isProfile && $_canWrite)) ? true : false;

$average_cost_default = isset($companyConfigs['average_cost_default']) ? $companyConfigs['average_cost_default'] : 1;
$budget_euro_fill_manual = isset($companyConfigs['budget_euro_fill_manual']) ? $companyConfigs['budget_euro_fill_manual'] : 0;
$average_euro_fill_manual = isset($companyConfigs['average_euro_fill_manual']) ? $companyConfigs['average_euro_fill_manual'] : 0;

?>

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
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
	input#gs-average-daily-rate {
		height: 24px;
		margin-top: 4px;
		line-height: 30px;
		border: #f2f5f7;
		font-weight: 600;
		text-align: right;
		display: inline-block;
		margin-left: auto;
	}
	span#icon_budget {
		float: right;
		line-height: 40px;
		margin-right: -3px;
		display: inline-block;
	}
	.row-number {
		float: right !important;
	}
	.wd-title{
		min-height: 40px;
	}
	#new-internal{
		top: 45px;
	}
	.wd-tab > .wd-panel{
		padding: 16px !important;
	}
	.progress-values .wd-t1 {
		max-width: 200px;
		color: #242424;
		font-size: 24px;
		font-weight: 700;
	}
	.progress-values .progress-value{
		display: flex;
		justify-content: space-between;
	}
	.progress-value p {
		/* width: 160px; */
		display: inline-block;
		font-weight: 600;
	}
	.progress-value span {
		/* width: calc(100% - 165px);*/
		display: inline-block;
		text-align: right;
		color: black;
	}
	#project-budget-table tr {
		height: 40px;
		vertical-align: middle;
		font-size: 14px;
	}
	.wd-table .budget-title th {
		text-align: left;
		vertical-align: middle;
	}
	#project-budget-table tr td, #project-budget-table tr th {
		border: 1px solid #E9E9E9;
		padding-right: 5px;
	}
	.bg-var-green{
		background-color: #75AF7E !important;
	}
	.bg-var-red{
		background-color: #DB414F !important;
	}
	.green-internal{
		background-color: #75AF7E !important;
	}
	.red-internal{
		background-color: #DB414F !important;
	}
	.btn-plus-green span {
		background-color: #DB414F;
		position: absolute;
		width: 2px;
		height: 20px;
		top: calc( 50% - 10px);
		left: calc( 50% - 1px);
		display: block;
	}
	.btn-plus-green span.hoz_line {
		width: 20px;
		height: 2px;
		left: calc( 50% - 10px);
		top: calc( 50% - 1px);
	}
	#new-external:before, #new-internal:before, #new-external:after, #new-internal:after{
		background-color: transparent;
	}
	#new-external, #new-internal{
		background-color: #fff;
	}
	span#bg_color {
		height: 50%;
		position: absolute;
		top: 20px;
		z-index: -1;
		left: 0px;
	}
	.progress-line .progress-line-inner {
		width: calc(100% - 40px);
		margin: auto;
		height: 160px;
		overflow: hidden;
	}
	@media(max-width: 1200px){
		.chard-internal{
			margin-bottom: 40px;
		}
	}
	.wd-moveline.slick-cell-move-handler svg {
		padding-top: unset;
		padding: 0 !important;
	}
	.slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
	.filterer {
		background: none !important;
	}
	.slick-cell.wd-moveline.slick-cell-move-handler {
		padding: 0;
	}
	.ui-datepicker .ui-datepicker-buttonpane {
		display: none;
	}
</style>
<?php 
$unit = __('$', true);
$budget_settings = !empty($budget_settings) ? $budget_settings :  $unit;
$switch_view = isset($switch_view) ? $switch_view : 0;// 0: MD / 1: Euro
$switch_title = array(
	sprintf(__('View %s', true), $budget_settings),
	sprintf(__('View %s', true), __('M.D', true))
);
// ob_clean(); debug( $valBudgetSynsInternal); exit;
// ob_clean(); debug( $valBudgetSyns); exit;
$switch_title = $switch_title[$switch_view];
$interBudgetPrice = (float)$valBudgetSynsInternal['internal_costs_budget'];
$interEngagedPrice = $useManualConsumed ? ($engagedErro) : ( (float)$valBudgetSynsInternal['internal_costs_engaged']);
$interBudgetMD = $valBudgetSynsInternal['internal_costs_budget_man_day'];
// $interForecastMD = $valBudgetSynsInternal['internal_costs_forecasted_man_day'];
$interForecastMD = $getDataProjectTasks['workload'] ? $getDataProjectTasks['workload'] : 0;
$interEngagedMD = $getDataProjectTasks['consumed'] ? $getDataProjectTasks['consumed'] : 0;
$interForecastPrice = $useManualConsumed ? (($getDataProjectTasks['consumed'] + $getDataProjectTasks['remain']) * $valBudgetSyns) : ((float)$valBudgetSynsInternal['internal_costs_forecast']);
$interVarProgress = !empty($interBudgetPrice) ? ($interForecastPrice/$interBudgetPrice)*100 : 0;
?>
<div id="wd-container-main" class="wd-project-admin project-external">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel"><div class="wd-list-project">
				<div class="wd-title">
					<a href="javascript:void(0);" class="btn btn-expand" onclick="expandTable(this);" title="<?php __("Expand"); ?>"></a>
					<a href="javascript:void(0);" class="btn btn-table-collapse"onclick="collapse_table(this);" title="<?php __('Collapse table') ?>" style="display: none;"></a>
					<div class="wd-input wd-checkbox-switch" title="<?php echo $switch_title;?>"><input type="checkbox" name="data[switch_view]" id="InternalChartSwitch" value="1" <?php if( $switch_view) echo 'checked="checked"';?> style="display: none;"><label for="InternalChartSwitch"><span class="wd-btn-switch"><span></span></span></label></div>
				</div>
				<div class="chard-content">
				<div class="wd-row space5">
					<div class="wd-col wd-col-lg-6">
						<div class="chard-internal wd-budget-chart clear-fix">
							<div class="budget-progress-circle" >
								<div id="progress-circle-internal" class="step wd-progress-pie" data-val="<?php echo $interVarProgress; ?>"></div>
							</div>
							<div class="progress-values progress-values-price">
								<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost', true)?></h3>
								<div class ="progress-value progress-budget"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true)?></p><span><?php echo number_format($interBudgetPrice, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
								<div class ="progress-value progress-forecast"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true)?></p><span><?php echo number_format($interForecastPrice, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
								<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true)?></p><span><?php echo number_format($interEngagedPrice, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
							</div>
							<div class="progress-values progress-values-md wd-hide">
								<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost', true)?></h3>
								<div class ="progress-value progress-budget"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true)?></p><span><?php echo number_format($interBudgetMD, 2, ',', ' ') . ' ' .__('M.D', true);?></span></div>
								<div class ="progress-value progress-forecast"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true)?></p><span><?php echo number_format($interForecastMD, 2, ',', ' ') . ' ' .__('M.D', true);?></span></div>
								<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true)?></p><span><?php echo number_format($interEngagedMD, 2, ',', ' ') . ' ' .__('M.D', true);?></span></div>
							</div>
						</div>
					</div>
					<div class="wd-col wd-col-lg-6">
						<div class="graph-internal wd-budget-chart">
							<?php
								$file = 'widgets'.DS.'internal_progress_line';
								$options = array(
									'type' => 'monthyear',
									'chartHeight' => '200', // 140px + 28px,
									'show_switch' => 1
								);
								echo $this->element($file, $options);
							?>
						</div>
					</div>
				</div>
				</div>
			</div></div></div>
				
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable(this);" title="<?php __("Expand"); ?>"></a>
					<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                    <a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table(this);" title="<?php __('Collapse table') ?>" style="display: none;"></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
				<?php if( $canModified ){?>
					<a href="javascript:void(0);" class="btn btn-plus-green" id="new-internal" onclick="addInternalCost();" title="<?php __('Add an internal cost') ?>">
						<span class='ver_line'></span>
						<span class='hoz_line'></span>
					</a>
				<?php } ?> 
                <div class="wd-table" id="project_container" style="width:100%; height: 430px;">

                </div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
              </div></div>

        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_budget_internals', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>

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

$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$columns = array(
    // array(
    //     'id' => 'no.',
    //     'field' => 'no.',
    //     'name' => '#',
    //     'width' => 40,
    //     'sortable' => true,
    //     'resizable' => false,
    //     'noFilter' => 1,
    // ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Name', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBoxCustom'
    ),
    array(
        'id' => 'validation_date',
        'field' => 'validation_date',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Validation date', true),
        'width' => 120,
        'minWidth' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'datatype' => 'datetime',
		'cssClass' => "wd-slick-date",
        'formatter' => 'Slick.Formatters.DateTime'
    ),
    array(
        'id' => 'budget_erro',
        'field' => 'budget_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => ($budget_euro_fill_manual  == 1) ? 'Slick.Editors.numericValueBudget' : 0,
        'formatter' => 'Slick.Formatters.erroValue'
    ),
    array(
        'id' => 'forecast_erro',
        'field' => 'forecast_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'var_percent',
        'field' => 'var_percent',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Var % (€)', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'engaged_erro',
        'field' => 'engaged_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'remain_erro',
        'field' => 'remain_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Remain €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'budget_md',
        'field' => 'budget_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'forecast_md',
        'field' => 'forecast_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'var_percent_md',
        'field' => 'var_percent_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Var % (' . ' M.D)', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'engaged_md',
        'field' => 'engaged_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'remain_md',
        'field' => 'remain_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Remain M.D', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
	array(
        'id' => 'average',
        'field' => 'average',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Average daily rate €', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        'editor' => ($average_cost_default == 1) ? 'Slick.Editors.numericValueBudget' : 0,
        'formatter' => 'Slick.Formatters.erroValue'
    ),
     array(
        'id' => 'profit_center_id',
        'field' => 'profit_center_id',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Profit Center', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
     array(
        'id' => 'funder_id',
        'field' => 'funder_id',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Funder', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    // array(
    //     'id' => 'action.',
    //     'field' => 'action.',
    //     'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Action', true),
    //     'width' => 70,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.Action'
    // )
);
$settings = $this->requestAction('/translations/getSettings',
    array('pass' => array(
        'Internal_Cost',
        array('internal_cost')
    )
));
// //su dung setting de sort lai $columns
$columns = Set::combine($columns, '{n}.field', '{n}');
$new = array();
foreach ($settings as $field => $value) {
    if( isset($columns[$field]) )
        $new[] = $columns[$field];
}
$columns = $new;
array_push($columns, array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    ));
array_unshift($columns, array(
        'id' => 'moveline',
		'field' => 'moveline',
		'name' => '',
		'width' => 40,
		'minWidth' => 40,
		'maxWidth' => 40,
		'sortable' => false,
		'resizable' => false,
		'noFilter' => 1,
		'behavior' => 'selectAndMove',
		'cssClass' => 'wd-moveline slick-cell-move-handler',
		'formatter' => 'Slick.Formatters.moveLine'
    ));


$i = 1;
$dataView = array();
$selectMaps = array(
    'profit_center_id' => $profits,
    'funder_id' => $funders
);
$count = 0;
$totalAverages = 0;
foreach ($budgets as $budget) {
    $data = array(
        'id' => $budget['ProjectBudgetInternalDetail']['id'],
        'project_id' => $budget['ProjectBudgetInternalDetail']['project_id'],
        'activity_id' => $activityLinked,
        'no.' => $i++
    );
    $data['name'] = (string) $budget['ProjectBudgetInternalDetail']['name'];
    $data['validation_date'] = $str_utility->convertToVNDate($budget['ProjectBudgetInternalDetail']['validation_date']);
    $data['budget_md'] = $budget['ProjectBudgetInternalDetail']['budget_md'];
    $budget_md = !empty($budget['ProjectBudgetInternalDetail']['budget_md']) ? $budget['ProjectBudgetInternalDetail']['budget_md'] : 0;
	
    $averages = isset($budget['ProjectBudgetInternalDetail']['average']) ? $budget['ProjectBudgetInternalDetail']['average'] : 0;
	
	// Edit by Viet Nguyen 09/01/2020
	// Ticket #526
	// if option 2: budget_euro_fill_manual is true
	// 1) BUDGET € is not calculated , Budget € is filled manually 
	// 2) AVERAGE DAILY RATE = BUDGET € / BUDGET M.D
	
	$data['budget_erro'] = isset($budget['ProjectBudgetInternalDetail']['budget_erro']) ? $budget['ProjectBudgetInternalDetail']['budget_erro'] : ($averages*$budget_md);
	
    $data['average'] = $averages;
	$totalAverages += $averages;
    $data['profit_center_id'] = $budget['ProjectBudgetInternalDetail']['profit_center_id'];
    $data['funder_id'] = $budget['ProjectBudgetInternalDetail']['funder_id'];
    $data['action.'] = '';
	$data['moveline'] = '';
	$count++;
    $dataView[] = $data;
}
if($count == 0){
    $totalAverages = 0;
} else {
    $totalAverages = round($totalAverages/$count, 2);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true)
);
$viewManDay = __($md, true);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
var average_cost_default = <?php echo json_encode($average_cost_default); ?>;
var budget_euro_fill_manual = <?php echo json_encode($budget_euro_fill_manual); ?>;
var average_euro_fill_manual = <?php echo json_encode($average_euro_fill_manual); ?>;
var consumed_used_timesheet = <?php echo json_encode($consumed_used_timesheet); ?>;
var is_use_tjm_project = <?php echo json_encode($is_use_tjm_project); ?>;
var valBudgetSyns = <?php echo json_encode($valBudgetSyns); ?>;
var budget_settings = <?php echo json_encode($budget_settings ? $budget_settings : '&euro;'); ?>; // €, $,...
var project_id = <?php echo json_encode($project_id); ?>;
var useManualConsumed  = <?php echo json_encode(intval($useManualConsumed)); ?>;
var getDataProjectTasks = <?php echo json_encode($getDataProjectTasks); ?>;
//heightTable = (heightTable < 550) ? 550 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 550) ? 550 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
	var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-phase{fill:none;}.b-phase{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-phase" width="40" height="40" transform="translate(-4 -4)"/><path class="b-phase" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
    var DateValidate = {};
    (function($){

        $(function(){
            var $this = SlickGridCustom, gridControl;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

			var _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>;
            var historyData = new $.z0.data(_history);
            var historyPath = <?php echo json_encode($this->params['url']['url']) ?>;
			$this.isResizing = 0;

            $.extend(Slick.Editors,{
                forecastValue : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 18).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(!/^([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                },
                numericValueBudget : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 18).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        ///^[\-+]??$/
                        //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
                        if(val == '0' || !/^[\-]?([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                },
                textBoxCustom : function(args){
                    $.extend(this, new BaseSlickEditor(args));
                    this.input = $("<input type='text' placeholder='New Internal Cost'/>")
                    .appendTo(args.container).attr('rel','no-history').addClass('editor-text placeholder');
                    this.focus();
                },
            });


            DateValidate.startDate = function(value){
                value = getTime(value);
                if(projectName['start_date'] == ''){
                    _valid = true;
                    _message = '';
                    //_message = $this.t('Start-Date or End-Date of Project are missing. Please input these data before full-field this date-time field.');
                } else {
                    //_valid = value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']);
                    //_message = $this.t('Date closing must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date']);
                    _valid = value >= getTime(projectName['start_date']);
                    _message = $this.t('Date closing must larger %1$s' ,projectName['start_date']);
                }
                return {
                    valid : _valid,
                    message : _message
                };
            }
            DateValidate.budgetBox = function(value){
                return {
                    valid : /^([1-9]|[1-9][0-9]*)$/.test(value) && parseInt(value ,10) > 0,
                    message : $this.t('<?php echo __('The Budget must be a number and greater than 0', true)?>')
                };
            }

            var actionTemplate =  $('#action-template').html();
            //kiem tra lai dat vi tri bien dung khong?
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.name), columnDef, dataContext);
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + budget_settings + '</span>', columnDef, dataContext);
                },
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + viewManDay + '</span> ', columnDef, dataContext);
                },
				moveLine: function(row, cell, value, columnDef, dataContext){
					return _menu_svg;
				},
                DateTime : function(row, cell, value, columnDef, dataContext){
                    return '<div class="cell-data" style="float: right;"><span style="text-align: right">' + value + '</span></div>';
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: true,
                frozenColumn: 1
            };
            var activityLinked = <?php echo json_encode($activityLinked); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false},
                validation_date : {defaulValue : ''},
                budget_md : {defaulValue : ''},
                budget_erro : {defaulValue : ''},
                average : {defaulValue : ''},
                profit_center_id : {defaulValue : 0},
                funder_id : {defaulValue : 0},
                activity_id: {defaulValue : activityLinked}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update_detail')); ?>';
            gridControl = $this.init($('#project_container'),data,columns, {
                // frozenColumn: 1,
                rowHeight: 42,
                headerRowHeight: 42,         
            });
			gridControl.setSelectionModel(new Slick.RowSelectionModel());
			$(window).resize(function(){
				reGrid();
			});
            $this.onBeforeEdit = function(args){
                //if(args.column.field == 'average_daily_rate'){
//                    return false;
//                }
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').closest('div').parent().addClass('rowCurrentEdit');
                //$('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                return true;
            }
			
			var totalAverages = <?php echo json_encode($totalAverages); ?>;
			<?php if(isset($engagedErroEmp)){?>
				var engagedErroEmp = <?php echo json_encode($engagedErroEmp); ?>;
			<?php }else{?>
				var engagedErroEmp = 0;
			<?php }?>
			<?php if(isset($engagedErroProfile)){?>
				var engagedErroProfile = <?php echo json_encode($engagedErroProfile); ?>;
			<?php }else{?>
				var engagedErroProfile = 0;
			<?php }?>
			<?php if(isset($engagedErroTeam)){?>
				var engagedErroTeam = <?php echo json_encode($engagedErroTeam); ?>;
			<?php }else{?>
				var engagedErroTeam = 0;
			<?php }?>
			<?php if(isset($totalValProject)){?>
				var totalValProject = <?php echo json_encode($totalValProject); ?>;
			<?php }else{?>
				var totalValProject = 0;
			<?php }?>
			
            var getDataProjectTasks = <?php echo json_encode($getDataProjectTasks); ?>;
			
            var externalBudgets = <?php echo json_encode($externalBudgets); ?>;
            var externalConsumeds = <?php echo json_encode($externalConsumeds); ?>;
            var remain_md = getDataProjectTasks.remain ? (parseFloat(getDataProjectTasks.remain)).toFixed(2) : 0;
            var consumed_md = getDataProjectTasks.consumed ? (getDataProjectTasks.consumed).toFixed(2) : 0;
            var forecast_md = getDataProjectTasks.workload ? (parseFloat(getDataProjectTasks.workload)).toFixed(2) : 0;
            var engaged_erro = <?php echo json_encode($engagedErro); ?>;
            engaged_erro = engaged_erro.toFixed(2);
            var _engaged_erro = engaged_erro;
            var _budgetMds = _budgetErros = 0;
            $.each(data, function(ind, vl){
                _budgetMds += vl.budget_md ? parseFloat(vl.budget_md) : 0;
                _budgetErros += parseFloat(vl.budget_erro);
            });
			var budgetEuro = _budgetErros;
			var _totalAverages = valBudgetSyns.toFixed(2);
			var remain_erro = (remain_md*valBudgetSyns).toFixed(2);
			var forecast_erro = (parseFloat(engaged_erro)+parseFloat(remain_erro)).toFixed(2);
            var _forecast_erro = forecast_erro;
			var _varMds = _varErros = 0;
            if(_budgetMds != 0){
                _varMds = ((forecast_md/_budgetMds - 1)*100).toFixed(2);
            }
            if(_budgetErros != 0){
                _varErros = ((forecast_erro/_budgetErros - 1)*100).toFixed(2);
            }
            _budgetMds = number_format(_budgetMds, 2, ',', ' ') + ' ' + viewManDay;
            _budgetErros = number_format(_budgetErros, 2, ',', ' ') +' ' + budget_settings;
            forecast_erro = number_format(forecast_erro, 2, ',', ' ') +' ' + budget_settings;
            engaged_erro = number_format(engaged_erro, 2, ',', ' ')+' ' + budget_settings;
            remain_erro = number_format(remain_erro, 2, ',', ' ')+' ' + budget_settings;
			var _varErrosResize = _varErros;
            // _varErros = number_format(_varErros, 2, ',', ' ') + ' %';
            _varMds = number_format(_varMds, 2, ',', ' ') + ' %';
            _totalAverages = number_format(_totalAverages, 2, ',', ' ');
			if( average_euro_fill_manual == 0 ){
				_totalAverages = _totalAverages+' ' + budget_settings;
			}
            gridControl.onSort.subscribe(function (e, args) {
                $('.row-number').parent().addClass('row-number-custom');
            });
            gridControl.onScroll.subscribe(function (e, args) {
                $('.row-number').parent().addClass('row-number-custom');
            });
            $this.onCellChange = function(args){
                $('.row-number').parent().addClass('row-number-custom');
                if(args.item){
					var _isBudgetMd = args.item.budget_md ? parseFloat(args.item.budget_md) : 0;
					var _averages = args.item.average ? parseFloat(args.item.average) : 0;
					if(budget_euro_fill_manual == 0){
						args.item.budget_erro = (_isBudgetMd*_averages);
					}else{
						args.item.average = args.item.budget_erro / args.item.budget_md, 2;
					}
					var _budgetMds = _budgetErros = _total = count = 0;
					var listItems = args.grid.getData().getItems(); 
					if( listItems.length){
						count = listItems.length;
						$.each(listItems, function(ind, vl){
							_budgetMds += vl.budget_md ? parseFloat(vl.budget_md) : 0;
							_budgetErros += (vl.budget_erro) ? parseFloat(vl.budget_erro) : 0;
							_total += (vl.budget_md && vl.budget_md != 0 ) ? parseFloat(vl.budget_erro / vl.budget_md) : 0;
						});
					}
					_budgetErros = Number(_budgetErros.toFixed(2));
					if( average_cost_default == 0 ){
						var _totalAverages = (_budgetMds != 0) ? (_budgetErros / _budgetMds).toFixed(2) : 0;
					}else{
						var total = count ? (_total / count) : 0;
						var _totalAverages = total;
					}
					if( useManualConsumed){
						engaged_erro = (_totalAverages * consumed_md).toFixed(2);
					}else{
						if((consumed_used_timesheet == 1) && (is_use_tjm_project == 1)){
							engaged_erro = (_totalAverages * consumed_md).toFixed(2);
						}else if(consumed_used_timesheet == 1){
							engaged_erro = _engaged_erro;
						}else{
							engaged_erro = ((_totalAverages * totalValProject) + engagedErroEmp + engagedErroProfile + engagedErroTeam).toFixed(2);
						}
					}
					remain_erro = (remain_md*_totalAverages).toFixed(2);
					forecast_erro = Number( (parseFloat(engaged_erro) + parseFloat(remain_erro)).toFixed(2));
					
					_varMds = _varErros = 0;
					if(_budgetMds != 0){
						_varMds = ((forecast_md/_budgetMds - 1)*100).toFixed(2);
					}
					var progressVar = 0;
					if(_budgetErros != 0){
						_varErros = (((parseFloat(forecast_erro))/_budgetErros - 1)*100).toFixed(2);
						progressVar = parseInt((forecast_erro/_budgetErros)*100);
					}
					var _varErrosResize = _varErros;
					totalBudgetEuros = _budgetErros;
					totalVarEuros = _varErros;
					totalEngagedEuro = engaged_erro;
					totalBudgetMds = _budgetMds;
					totalForecastMd = forecast_md;
					totalVarMds = _varMds;
					totalEngagedMd = consumed_md;
					totalRemainMd = remain_md;
					totalAverDailyRate = _totalAverages;
					
					_budgetMds = number_format(_budgetMds, 2, ',', ' ') + ' ' + viewManDay;
					_budgetErros = number_format(_budgetErros, 2, ',', ' ') +' ' + budget_settings;
					_varErros = number_format(_varErros, 2, ',', ' ') + ' %';
					_varMds = number_format(_varMds, 2, ',', ' ') + ' %';
					totalRemainEuro = (remain_md*_totalAverages);
					totalForecastEuro = (parseFloat(engaged_erro)+parseFloat(remain_erro));
					_totalAverages = number_format(_totalAverages, 2, ',', ' ') +' ' + budget_settings;
					forecast_erro = number_format(forecast_erro, 2, ',', ' ') +' ' + budget_settings;
					remain_erro = number_format(remain_erro, 2, ',', ' ') +' ' + budget_settings;
					$('#gs-budget-md').html(_budgetMds);
					$('.progress-values-md .progress-budget span').html(_budgetMds);
					$('#gs-budget-erro').html(_budgetErros);
					$('.progress-values-price .progress-budget span').html(_budgetErros);
					$('#gs-var-md').html(_varMds);
					$('#gs-var-erro').html(_varErros);
					$('#gs-average-daily-rate').html(_totalAverages);
					$('#gs-remain-erro').html(remain_erro);
					$('#gs-forecast-erro').html(forecast_erro);
					$('.progress-values-price .progress-forecast span').html(forecast_erro);
					engaged_erro = number_format(engaged_erro, 2, ',', ' ') +' ' + budget_settings;
					$('#gs-engaged-erro').html(engaged_erro);
					$('.progress-values-price .progress-engaged span').html(engaged_erro);
					
					updateChart(progressVar);
                }
                var columns = args.grid.getColumns(),
                    col, cell = args.cell;
                do {
                    cell++;
                    if( columns.length == cell )break;
                    col = columns[cell];
                } while (typeof col.editor == 'undefined');

                if( cell < columns.length ){
                    args.grid.gotoCell(args.row, cell, true);
                } else {
                    //end of row
                    try {
                        args.grid.gotoCell(args.row + 1, 0);
                    } catch(ex) {}
                }
                return true;
				
            }
			// end on cell change
			
            addInternalCost = function(){
                gridControl.gotoCell(data.length, 1, true);
            }
            // Chuyen header sang mau xanh la cay
            var headers = $('.slick-header-columns').get(1).children;
            $.each(headers, function(index, val){
                var id = headers[index].id;
                if( id.indexOf('md') != -1 ){
                    $('#'+id).addClass('gs-custom-cell-md-header');
                }
            });
            var cols = gridControl.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            gridControl.onColumnsResized.subscribe(function (e, args) {
                var _cols = gridControl.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('.row-number').parent().addClass('row-number-custom');
            });
            $('.row-number').parent().addClass('row-number-custom');
			
			var moveRowsPlugin = new Slick.RowMoveManager({
                cancelEditOnDrag: true
            });
            moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, data) {
                for (var i = 0; i < data.rows.length; i++) {
                        // no point in moving before or after itself
                    if (data.rows[i] == data.insertBefore || data.rows[i] == data.insertBefore - 1) {
                        e.stopPropagation();
                        return false;
                    }
                }
                return true;
            });
            $(gridControl.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
            //fire after row move completed
            moveRowsPlugin.onMoveRows.subscribe(function (e, args) {
                var extractedRows = [], left, right;
                var rows = args.rows;
                var insertBefore = args.insertBefore;
                left = data.slice(0, insertBefore);
                right = data.slice(insertBefore, data.length);
                rows.sort(function(a,b) { return a-b; });
                for (var i = 0; i < rows.length; i++) {
                    extractedRows.push(data[rows[i]]);
                }
                rows.reverse();
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    if (row < insertBefore) {
                        left.splice(row, 1);
                    } else {
                        right.splice(row - insertBefore, 1);
                    }
                }
                data = left.concat(extractedRows.concat(right));

                var selectedRows = [];
                for (var i = 0; i < rows.length; i++)
                    selectedRows.push(left.length + i);

                //update no.
                var orders = { data : {} };
                for(var i = 0; i < data.length; i++){
                    data[i]['no.'] = (i+1);
                    data[i].weight = (i+1);
                    orders.data[data[i].id] = (i+1);
                }
                //ajax call
                $.ajax({
                    url : '<?php echo $html->url('/project_budget_internals_preview/order/' . $projectName['Project']['id']) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                })
                gridControl.resetActiveCell();
                var dataView = gridControl.getDataView();
                dataView.beginUpdate();
                //if set data via grid.setData(), the DataView will get removed
                //to prevent this, use DataView.setItems()
                dataView.setItems(data);
                //dataView.setFilter(filter);
                //updateFilter();
                dataView.endUpdate();
                // gridControl.getDataView.setData(data);
                gridControl.setSelectedRows(selectedRows);
                gridControl.render();
            });

            gridControl.registerPlugin(moveRowsPlugin);
            gridControl.onDragInit.subscribe(function (e, dd) {
                // prevent the grid from cancelling drag'n'drop by default
                e.stopImmediatePropagation();
            });
			gridControl.onColumnsResized.subscribe(function (e, args) {
				setupScroll();
			});
			
            var _header = {
                name: {id: 'gs-name', val: 'Total', cl: 'gsEuro'},
                budget_erro: {id: 'gs-budget-erro',val: _budgetErros, cl: 'gsEuro'},
                forecast_erro: {id: 'gs-forecast-erro',val: forecast_erro, cl: 'gsEuro'},
                var_percent: {id: 'gs-var-erro',val: number_format(_varErros, 2, ',', ' ') + ' %', cl: 'gsEuro'},
                engaged_erro: {id: 'gs-engaged-erro',val: engaged_erro, cl: 'gsEuro'},
                remain_erro: {id: 'gs-remain-erro',val: remain_erro, cl: 'gsEuro'},
                budget_md: {id: 'gs-budget-md',val: _budgetMds, cl: 'gsMd'},
                forecast_md: {id: 'gs-forecast-md',val: number_format(forecast_md, 2, ',', ' ') + ' ' + viewManDay, cl: 'gsMd'},
                var_percent_md: {id: 'gs-var-md',val: _varMds, cl: 'gsMd'},
                engaged_md: {id: 'gs-consumed-md',val: number_format(consumed_md, 2, ',', ' ')+ ' ' + viewManDay, cl: 'gsMd'},
                remain_md: {id: 'gs-remain-md',val: number_format(remain_md, 2, ',', ' ')+ ' ' + viewManDay, cl: 'gsMd'},
                average: {id: 'gs-average-daily-rate',val: _totalAverages, cl: 'gsEuro'},
            };
            var settings = <?php echo json_encode(array_keys($settings)); ?>;
			//Ticket 526 background cho header colum Var.
			_varErros = parseFloat(_varErros);
			$bgColorVar = '#75AF7E !important';
			if(_varErros > 100){
				_varErros = 100;
				$bgColorVar = '#DB414F !important';
			}else if(_varErros < 0){
				_varErros = 100 + _varErros;
			}else{
				$bgColorVar = '#DB414F !important';
			}
			_varErros = _varErros + '%';
            $.each(settings, function(index, field){
                if(_header[field]){
					if((field == 'average') && (average_euro_fill_manual != 0 )){
						$(gridControl.getHeaderRowColumn(field)).html('<input id="' +_header[field].id+ '" class="' +_header[field].cl+ '" value ="' +_header[field].val+ '" /><span id="icon_budget">'+ budget_settings+ '</span>');
					}else if((field == 'var_percent')){
						$(gridControl.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p><span id="bg_color" style="width:'+_varErros+'; background-color:'+$bgColorVar+'" ></span>');
					}else{
						$(gridControl.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p>');
					}
                }
            });
            $('.gsMd').parent().addClass('gs-custom-cell-md');
			if(_forecast_erro > budgetEuro){
				$('.hoz_line').addClass('red-internal');
				$('.ver_line').addClass('red-internal');
			}else{
				$('.hoz_line').addClass('green-internal');
				$('.ver_line').addClass('green-internal');
			}
            $('#export-submitplus').click(function(){
                        var length = gridControl.getDataLength(),
                            list = [];
                        for(var i = 0; i<length; i++){
                            var item = gridControl.getDataItem(i);
                            list.push(item.id);
                        }
                        $('#export-item-list')
                        .val(list.join(','))
                        .closest('form')
                        .submit();

            });
            // custom fucntion for new dessign

            reGrid = function(){
                gridControl.resizeCanvas();
                function setupScroll(){
                    //$("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
                    $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
                    $("#scrollTopAbsence").css({
                        'margin': '0px 0px 10px 0px',
                        'margin-left' : $(".grid-canvas-left:first").width(),
                        // 'margin-bottom': '10',
                    });
                }
                setTimeout(function(){
                    setupScroll();
                }, 400);
                $("#scrollTopAbsence").scroll(function () {
                    $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
                });
                $(".slick-viewport-right:first").scroll(function () {
                    $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right:first").scrollLeft());
                });
            }
            expandTable = function(elm){
                $(elm).closest('.wd-list-project').addClass('fullScreen');
                reGrid();
                $(elm).closest('.wd-list-project').find('.btn-table-collapse').show();
                $(elm).closest('.wd-list-project').find('.btn-expand').hide(); 
            }
            
            collapse_table = function(elm){
                $(elm).closest('.wd-list-project').removeClass('fullScreen');
                reGrid();
                $(elm).closest('.wd-list-project').find('.btn-table-collapse').hide();
                $(elm).closest('.wd-list-project').find('.btn-expand').show(); 
            }
			
			$this.onColumnsResized = function(args){
				if( $this.isResizing) return true;
				$this.isResizing = 1;
				var grid = args.grid;
				var options = grid.getOptions();
				var column_change = {};
				// get column with changed
				var _columns = grid.getColumns();
				$.each( _columns, function(i, column){
					// moi lan resize co the co nhieu column bi anh huong, o day chi check cho 1 column
					if (column.width != column.previousWidth) {
						column_change = column;
					}
				});
				var list_column_willchange = {};
				if( column_change.width ){
					var width = column_change.width;
					var _field = column_change.field.split('_');
					if( _field[1]){
						// change column width for the columns with same name
						var price_fields = ['budget', 'forecast', 'engaged', 'remain'];
						$.each(_columns, function(i, column){
							var field = column.field.split('_');
							if( field[1] == _field[1] || ( ($.inArray(field[1], price_fields) != -1) && ($.inArray(_field[1], price_fields) != -1) ) ){
								$('[data-columnindex="' + _columns[i].field + '"]').val(width).trigger('change');
							}
						});
						draw_sub_header();
					}
				}
				grid.eval('applyColumnHeaderWidths();updateCanvasWidth(true);');
				$this.isResizing = 0;
				return true;
			}
			function draw_sub_header(){
				// Chuyen header sang mau xanh la cay
				var headers = $('.slick-header-columns').get(1).children;
				$.each(headers, function(index, val){
					var id = headers[index].id;
					if( id.indexOf('md') != -1 ){
						$('#'+id).addClass('gs-custom-cell-md-header');
					}
				});
				var _header = {
					name: {id: 'gs-name', val: 'Total', cl: 'gsEuro'},
					budget_erro: {id: 'gs-budget-erro',val: _budgetErros, cl: 'gsEuro'},
					forecast_erro: {id: 'gs-forecast-erro',val: forecast_erro, cl: 'gsEuro'},
					var_percent: {id: 'gs-var-erro',val: _varErros, cl: 'gsEuro'},
					engaged_erro: {id: 'gs-engaged-erro',val: engaged_erro, cl: 'gsEuro'},
					remain_erro: {id: 'gs-remain-erro',val: remain_erro, cl: 'gsEuro'},
					budget_md: {id: 'gs-budget-md',val: _budgetMds, cl: 'gsMd'},
					forecast_md: {id: 'gs-forecast-md',val: number_format(forecast_md, 2, ',', ' ') + ' ' + viewManDay, cl: 'gsMd'},
					var_percent_md: {id: 'gs-var-md',val: _varMds, cl: 'gsMd'},
					engaged_md: {id: 'gs-consumed-md',val: number_format(consumed_md, 2, ',', ' ')+ ' ' + viewManDay, cl: 'gsMd'},
					remain_md: {id: 'gs-remain-md',val: number_format(remain_md, 2, ',', ' ')+ ' ' + viewManDay, cl: 'gsMd'},
					average: {id: 'gs-average-daily-rate',val: _totalAverages, cl: 'gsEuro'},
				};
				var settings = <?php echo json_encode(array_keys($settings)); ?>;
				//Ticket 526 background cho header colum Var.
				var _varErrosDisplay = _varErrosNotResize = _varErrosResize;
				$bgColorVar = '#75AF7E !important';
				if(_varErrosResize > 100){
					_varErrosDisplay = 100;
					$bgColorVar = '#DB414F !important';
				}else if(_varErrosResize < 0){
					_varErrosDisplay = 100 + parseFloat(_varErrosResize);
				}else{
					$bgColorVar = '#DB414F !important';
				}
				$.each(settings, function(index, field){
					if(_header[field]){
						if((field == 'average') && (average_euro_fill_manual != 0 )){
							$(gridControl.getHeaderRowColumn(field)).html('<input id="' +_header[field].id+ '" class="' +_header[field].cl+ '" value ="' +_header[field].val+ '" /><span id="icon_budget">'+ budget_settings+ '</span>');
						}else if((field == 'var_percent')){
							_varErrosNotResize = _varErrosNotResize + '%';
							$(gridControl.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_varErrosNotResize+ '</p><span id="bg_color" style="width:'+_varErrosDisplay+'%'+'; background-color:'+$bgColorVar+'" ></span>');
						}else{
							$(gridControl.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p>');
						}
					}
				});
				$('.gsMd').parent().addClass('gs-custom-cell-md');
			}
			history_reset = function(){
				var check = false;
				$('.slick-header-sortable').each(function(val, ind){
					var text = '';
					if(($('.slick-header-column-sorted').length != 0)||($('.slick-sort-indicator-asc').length != 0)||($('.slick-sort-indicator-desc').length != 0)){
						text = '1';
					}
					if( text != '' ){
						check = true;
					} else {
					}
				});
				if(!check){
					$('#reset-filter').addClass('hidden');
				} else {
					$('#reset-filter').removeClass('hidden');
				}
			}
			resetFilter = function(){
				$('.slick-header-column').removeClass('slick-header-column-sorted');
				$('.slick-sort-indicator').removeClass('slick-sort-indicator-asc');
				$('.slick-sort-indicator').removeClass('slick-sort-indicator-desc');
				
				$('input[name="project_container.SortOrder"]').val('').trigger('change');
				$('input[name="project_container.SortColumn"]').val('').trigger('change');
				gridControl.setSortColumn('name', false);
				$('.slick-header-columns').children().eq(0).trigger('click');
				gridControl.setSortColumn();		
			}
        });
		
		function updateChart(percent){
			// percent = Math.min(100, Math.max(0, percent));
			// console.log( percent);
			pie_progress.data('val', percent).progressPie();
			return;
		}
    })(jQuery);
    function setupScroll(){
        $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
        $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
		$("#scrollTopAbsence").css({
			'margin': '0px 0px 10px 0px',
			'margin-left' : $(".grid-canvas-left:first").width(),
		});
    }
    setTimeout(function(){
        setupScroll();
    }, 2500);
    $("#scrollTopAbsence").scroll(function () {
        $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
    $(".slick-viewport-right:first").scroll(function () {
        $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right:first").scrollLeft());
    });
	
	$( document ).ready(function() {
		$('#gs-average-daily-rate').on('change', function(){
			average_val = $(this).val();
			if(average_val){
				average_val = parseFloat(average_val.replace(' ', ''));
				$.ajax({
					url : <?php echo json_encode($html->url(array('action' => 'update_average_daily'))); ?>,
					data: {
						average_val: average_val,	
						project_id: <?php echo json_encode($project_id); ?>, 
					},
					cache : false,
					type : 'POST',
					dataType: 'json',
					async: false,
					success: function(data){
						if(data == true){
							var budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
							<?php if(isset($engagedErroEmp)){?>
								var engagedErroEmp = <?php echo json_encode($engagedErroEmp); ?>;
							<?php }else{?>
								var engagedErroEmp = 0;
							<?php }?>
							<?php if(isset($engagedErroProfile)){?>
								var engagedErroProfile = <?php echo json_encode($engagedErroProfile); ?>;
							<?php }else{?>
								var engagedErroProfile = 0;
							<?php }?>
							<?php if(isset($engagedErroTeam)){?>
								var engagedErroTeam = <?php echo json_encode($engagedErroTeam); ?>;
							<?php }else{?>
								var engagedErroTeam = 0;
							<?php }?>
							<?php if(isset($totalValProject)){?>
								var totalValProject = <?php echo json_encode($totalValProject); ?>;
							<?php }else{?>
								var totalValProject = 0;
							<?php }?>
							var getDataProjectTasks = <?php echo json_encode($getDataProjectTasks); ?>;
							var externalBudgets = <?php echo json_encode($externalBudgets); ?>;
							var externalConsumeds = <?php echo json_encode($externalConsumeds); ?>;
							var remain_md = getDataProjectTasks.remain ? (parseFloat(getDataProjectTasks.remain)).toFixed(2) : 0;
							var consumed_md = getDataProjectTasks.consumed ? (getDataProjectTasks.consumed).toFixed(2) : 0;
							var engaged_erro = <?php echo json_encode($engagedErro); ?>;
							engaged_erro = engaged_erro.toFixed(2);
							var _engaged_erro = engaged_erro;
							var forecast_md = getDataProjectTasks.workload ? (parseFloat(getDataProjectTasks.workload)).toFixed(2) : 0;
							
							var slickGrid = SlickGridCustom.getInstance();
							var dataView = slickGrid.getData();
							var _new_data = dataView.getItems();
							var totalBudgetErro = totalForecastErro = totalRemainErro = 0;
							_budgetMds = 0;
							$.each( _new_data, function( ind, item){
							   _new_data[ind]['average'] = average_val;
							   _new_data[ind]['budget_erro'] = average_val * _new_data[ind]['budget_md'];
							   _budgetMds += (_new_data[ind]['budget_md']) ? parseFloat(_new_data[ind]['budget_md']) : 0;
							   totalBudgetErro += _new_data[ind]['budget_erro'];
							});
							
							if((consumed_used_timesheet == 1) && (is_use_tjm_project == 1)){
								engaged_erro = (average_val * consumed_md).toFixed(2);
							}else if(consumed_used_timesheet == 1){
								engaged_erro = _engaged_erro;
							}else{
								engaged_erro = ((average_val * totalValProject) + engagedErroEmp + engagedErroProfile + engagedErroTeam).toFixed(2);
							}
							totalEngagedEuro = engaged_erro;
							
							totalRemainErro = (remain_md*average_val).toFixed(2);
							_totalRemainErro = totalRemainErro;
							
							totalForecastErro = (parseFloat(engaged_erro)+parseFloat(totalRemainErro)).toFixed(2);
							var _totalForecastErro = totalForecastErro;
							var _totalBudgetErro = totalBudgetErro;
							totalBudgetErro = number_format(totalBudgetErro, 2, ',', ' ')+' ' + budget_settings;
							totalRemainErro = number_format(totalRemainErro, 2, ',', ' ')+' ' + budget_settings;
							totalForecastErro = number_format(totalForecastErro, 2, ',', ' ')+' ' + budget_settings;
							
							_varMds = _varErros = 0;
							if(_totalBudgetErro != 0){
								_varErros = ((_totalForecastErro/_totalBudgetErro - 1)*100).toFixed(2);
							}
							var totalVarEuros = _varErros;
							_varErros = number_format(_varErros, 2, ',', ' ') + ' %';
							
							$('#gs-budget-erro').html(totalBudgetErro);
							$('#gs-remain-erro').html(totalRemainErro);
							$('#gs-forecast-erro').html(totalForecastErro);
							$('#gs-var-erro').html(_varErros);
							engaged_erro = number_format(engaged_erro, 2, ',', ' ') +' ' + budget_settings;
							$('#gs-engaged-erro').html(engaged_erro);
							_totalBudgetErro = _totalBudgetErro.toFixed(2);
							dataView.setItems(_new_data);
							dataView.endUpdate();
							slickGrid.invalidate();
							slickGrid.render();
							$.ajax({
								url : <?php echo json_encode($html->url(array('action' => 'update_value_project_budget_sync'))); ?>,
								data: {
									totalBudgetEuros: _totalBudgetErro,	
									totalForecastEuro: _totalForecastErro,	
									totalVarEuros: totalVarEuros,	
									totalEngagedEuro: totalEngagedEuro,	
									totalRemainEuro: _totalRemainErro,	
									totalBudgetMds: _budgetMds,	
									totalForecastMd: forecast_md,	
									// totalVarMds: totalVarMds,	
									totalEngagedMd: consumed_md,	
									totalRemainMd: remain_md,	
									totalAverDailyRate: average_val,	
									project_id: <?php echo json_encode($project_id); ?>, 
								},
								cache : false,
								type : 'POST',
								dataType: 'json',
								async: false,
								success: function(data){
								}
							});
						}
					}
				});
			}
			
		});
	});
	var pie_progress = $('#progress-circle-internal');
	var progress_width = pie_progress.width();
	pie_progress.addClass('wd-progress-pie').setupProgressPie({
		size: progress_width ? progress_width : 140,
		strokeWidth: 8,
		ringWidth: 8,
		ringEndsRounded: true,
		strokeColor: "#e0e0e0",
		color: function(value){
			var red = 'rgba(233, 71, 84, 1)';
			var green = 'rgba(110, 175, 121, 1)';
			return pie_progress.data('val') > 100 ? red : green;
		},
		valueData: "val",
		contentPlugin: "progressDisplay",
		contentPluginOptions: {
			fontFamily: 'Open Sans',
			multiline: [
				{
					cssClass: "progresspie-progressText",
					fontSize: 11,
					textContent: '', //('<?php __('Progress');?>').toUpperCase(),
					color: '#ddd',
				},
				{
					cssClass: "progresspie-progressValue",
					fontSize: 28,
					textContent: '%s%' ,
					color: function(value){
						var red = 'rgba(233, 71, 84, 1)';
						var green = 'rgba(110, 175, 121, 1)';
						return pie_progress.data('val') > 100 ? red : green;
					}
				},
			],
			fontSize: 28
		},
		animate: {
			dur: "1.5s"
		}
	}).progressPie();
	function wd_oncellchange_callback(data){
		// console.log(data);
		var cont = $('#progress-line-in');
		cont.addClass('loading');
		$.ajax({
			url: '/project_budget_synthesis/progress_line/' + project_id + '/ajax',
			type: 'get',
			dataType: 'json',
			success: function(res){
				update_data_internal(res);
				draw_internal_chart('#InternalChartSwitch');
			},
			complete: function(){
				cont.removeClass('loading');
			}
		});
	}
	function update_data_internal(synthesys_data){
		inter_maxValue = synthesys_data.max_internal_euro;
		inter_maxValue_price = synthesys_data.max_internal_euro;
		inter_maxValue_md = synthesys_data.max_internal_md;
		dataset_internals = synthesys_data.dataset_internals;
		internal_progress_setting.source = dataset_internals;
		$('#chartContainerIn').width( 50 * synthesys_data.countLineIn);
	}
</script>