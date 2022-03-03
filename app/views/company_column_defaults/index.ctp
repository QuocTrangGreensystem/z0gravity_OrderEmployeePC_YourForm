<?php
echo $this->Html->css(array(
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'slick_grid/slick.edit',
	'preview/tab-admin',
	'layout_admin_2019'
));
echo $this->Html->script(array(
	'jquery.multiSelect',
    'history_filter',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
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
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'responsive_table',
    'jquery.ui.touch-punch.min'
));
echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
	.slick-cell .multiSelect {
		width: auto; 
		display: block;
		overflow: hidden; 
		text-overflow: ellipsis;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
							<h2 class="wd-t3"><?php __(' ') ?></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$employeeInfo = $this->Session->read('Auth.employee_info');
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
		'name' => '',
		'width' => 40,
		'noFilter' => 1,
		'sortable' => true,
		'resizable' => false,
        'noFilter' => 1,
	),
	array(
		'id' => 'column_name',
		'field' => 'column_name',
		'name' => __('Column name', true),
		'width' => 200,
		'sortable' => true,
		'resizable' => true,
	),
	array(
		'id' => 'width',
		'field' => 'width',
		'name' => __(' Size ', true),
		'width' => 82,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
		'validator' => 'DataValidator.isUnique'
    )
);
/*
* Mang nay dung de hien thi ra man hinh
*/
$widthProgram = !empty($defaultColumns['Project.project_amr_program_id']) ? $defaultColumns['Project.project_amr_program_id'] : 200;
$widthSubProgram = !empty($defaultColumns['Project.project_amr_sub_program_id']) ? $defaultColumns['Project.project_amr_sub_program_id'] : 200;
$widthProjectName = !empty($defaultColumns['Project.project_name']) ? $defaultColumns['Project.project_name'] : 300;
$widthProjectManager = !empty($defaultColumns['Project.project_manager_id']) ? $defaultColumns['Project.project_manager_id'] : 150;
$widthWeather = !empty($defaultColumns['ProjectAmr.weather']) ? $defaultColumns['ProjectAmr.weather'] : 100;
$widthToDo = !empty($defaultColumns['ProjectAmr.todo']) ? $defaultColumns['ProjectAmr.todo'] : 400;
$widthProgress = !empty($defaultColumns['ProjectAmr.project_amr_progression']) ? $defaultColumns['ProjectAmr.project_amr_progression'] : 400;
$widthDone = !empty($defaultColumns['ProjectAmr.done']) ? $defaultColumns['ProjectAmr.done'] : 400;
$widthAmount = !empty($defaultColumns['ProjectBudgetSyn.Amount€']) ? $defaultColumns['ProjectBudgetSyn.Amount€'] : 50;
$widthComment = !empty($defaultColumns['ProjectAmr.comment']) ? $defaultColumns['ProjectAmr.comment'] : 400;
$widthBudget = !empty($defaultColumns['ProjectAmr.budget']) ? $defaultColumns['ProjectAmr.budget'] : 400;
$widthRisk = !empty($defaultColumns['ProjectAmr.project_amr_risk_information']) ? $defaultColumns['ProjectAmr.project_amr_risk_information'] : 150;
$widthIssue = !empty($defaultColumns['ProjectAmr.project_amr_problem_information']) ? $defaultColumns['ProjectAmr.project_amr_problem_information'] : 150;
$widthCustomerPointOfView = !empty($defaultColumns['ProjectAmr.customer_point_of_view']) ? $defaultColumns['ProjectAmr.customer_point_of_view'] : 150;
$widthInformation = !empty($defaultColumns['ProjectAmr.project_amr_solution']) ? $defaultColumns['ProjectAmr.project_amr_solution'] : 400;
$widthPlanningStatus = !empty($defaultColumns['ProjectAmr.planning_weather']) ? $defaultColumns['ProjectAmr.planning_weather'] : 150;
$widthRiskStatus = !empty($defaultColumns['ProjectAmr.risk_control_weather']) ? $defaultColumns['ProjectAmr.risk_control_weather'] : 150;
$widthIssueStatus = !empty($defaultColumns['ProjectAmr.issue_control_weather']) ? $defaultColumns['ProjectAmr.issue_control_weather'] : 400;
$widthTrend = !empty($defaultColumns['ProjectAmr.rank']) ? $defaultColumns['ProjectAmr.rank'] : 150;
$widthScope = !empty($defaultColumns['ProjectAmr.project_amr_scope']) ? $defaultColumns['ProjectAmr.project_amr_scope'] : 400;
$widthSchedule = !empty($defaultColumns['ProjectAmr.project_amr_schedule']) ? $defaultColumns['ProjectAmr.project_amr_schedule'] : 400;
$widthResource = !empty($defaultColumns['ProjectAmr.project_amr_resource']) ? $defaultColumns['ProjectAmr.project_amr_resource'] : 400;
$widthTechnical = !empty($defaultColumns['ProjectAmr.project_amr_technical']) ? $defaultColumns['ProjectAmr.project_amr_technical'] : 400;
$widthBudgetComment = !empty($defaultColumns['ProjectAmr.project_amr_budget_comment']) ? $defaultColumns['ProjectAmr.project_amr_budget_comment'] : 400;
$widthBudgetWeather = !empty($defaultColumns['ProjectAmr.budget_weather']) ? $defaultColumns['ProjectAmr.budget_weather'] : 150;
$widthScopeWeather = !empty($defaultColumns['ProjectAmr.scope_weather']) ? $defaultColumns['ProjectAmr.scope_weather'] : 150;
$widthScheduleWeather = !empty($defaultColumns['ProjectAmr.schedule_weather']) ? $defaultColumns['ProjectAmr.schedule_weather'] : 150;
$widthResourceWeather = !empty($defaultColumns['ProjectAmr.resources_weather']) ? $defaultColumns['ProjectAmr.resources_weather'] : 150;
$widthTechnicalWeather = !empty($defaultColumns['ProjectAmr.technical_weather']) ? $defaultColumns['ProjectAmr.technical_weather'] : 150;

$defaultFields = array(
	0 => array(
		'column_name' => __d(sprintf($_domain, 'Details'), 'Program',true),
		'field_name' => 'Project.project_amr_program_id',
		'width' => $widthProgram,
		'company_id' => $company_id
	),
	1 => array(
		'column_name' => __d(sprintf($_domain, 'Details'), 'Sub program',true),
		'field_name' => 'Project.project_amr_sub_program_id',
		'width' => $widthSubProgram,
		'company_id' => $company_id
	),
	2 => array(
		'column_name' => __d(sprintf($_domain, 'Details'), 'Project Name',true),
		'field_name' => 'Project.project_name',
		'width' => $widthProjectName,
		'company_id' => $company_id
	),
	3 => array(
		'column_name' => __d(sprintf($_domain, 'Details'), 'Project Manager',true),
		'field_name' => 'Project.project_manager_id',
		'width' => $widthProjectManager,
		'company_id' => $company_id
	),
	4 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Weather',true),
		'field_name' => 'ProjectAmr.weather',
		'width' => $widthWeather,
		'company_id' => $company_id
	),
	5 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'To Do',true),
		'field_name' => 'ProjectAmr.todo',
		'width' => $widthToDo,
		'company_id' => $company_id
	),
	6 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Progress',true),
		'field_name' => 'ProjectAmr.project_amr_progression',
		'width' => $widthProgress,
		'company_id' => $company_id
	),
	7 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Done',true),
		'field_name' => 'ProjectAmr.done',
		'width' => $widthDone,
		'company_id' => $company_id
	),
	8 => array(
		'column_name' => __d(sprintf($_domain, 'Project_Task'), 'Amount €',true),
		'field_name' => 'ProjectBudgetSyn.Amount€',
		'width' => $widthAmount,
		'company_id' => $company_id
	),
	9 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Comment',true),
		'field_name' => 'ProjectAmr.comment',
		'width' => $widthComment,
		'company_id' => $company_id
	),
	10 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Budget',true),
		'field_name' => 'ProjectAmr.budget',
		'width' => $widthBudget,
		'company_id' => $company_id
	),
	11 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Risk',true),
		'field_name' => 'ProjectAmr.project_amr_risk_information',
		'width' => $widthRisk,
		'company_id' => $company_id
	),
	12 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Issue',true),
		'field_name' => 'ProjectAmr.project_amr_problem_information',
		'width' => $widthIssue,
		'company_id' => $company_id
	),
	13 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Customer Point Of View',true),
		'field_name' => 'ProjectAmr.customer_point_of_view',
		'width' => $widthCustomerPointOfView,
		'company_id' => $company_id
	),
	14 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Information',true),
		'field_name' => 'ProjectAmr.project_amr_solution',
		'width' => $widthInformation,
		'company_id' => $company_id
	),
	15 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Planning status',true),
		'field_name' => 'ProjectAmr.planning_weather',
		'width' => $widthPlanningStatus,
		'company_id' => $company_id
	),
	16 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Risk status',true),
		'field_name' => 'ProjectAmr.risk_control_weather',
		'width' => $widthRiskStatus,
		'company_id' => $company_id
	),
	17 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Issue status',true),
		'field_name' => 'ProjectAmr.issue_control_weather',
		'width' => $widthIssueStatus,
		'company_id' => $company_id
	),
	18 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Trend',true),
		'field_name' => 'ProjectAmr.rank',
		'width' => $widthTrend,
		'company_id' => $company_id
	),
	19 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Scope',true),
		'field_name' => 'ProjectAmr.project_amr_scope',
		'width' => $widthScope,
		'company_id' => $company_id
	),
	20 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Schedule',true),
		'field_name' => 'ProjectAmr.project_amr_schedule',
		'width' => $widthSchedule,
		'company_id' => $company_id
	),
	21 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Resource',true),
		'field_name' => 'ProjectAmr.project_amr_resource',
		'width' => $widthResource,
		'company_id' => $company_id
	),
	22 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Technical',true),
		'field_name' => 'ProjectAmr.project_amr_technical',
		'width' => $widthTechnical,
		'company_id' => $company_id
	),
	23 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Budget comment',true),
		'field_name' => 'ProjectAmr.project_amr_budget_comment',
		'width' => $widthBudgetComment,
		'company_id' => $company_id
	),
	24 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Budget weather',true),
		'field_name' => 'ProjectAmr.budget_weather',
		'width' => $widthBudgetWeather,
		'company_id' => $company_id
	),
	25 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Scope weather',true),
		'field_name' => 'ProjectAmr.scope_weather',
		'width' => $widthScopeWeather,
		'company_id' => $company_id
	),
	26 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Schedule weather',true),
		'field_name' => 'ProjectAmr.schedule_weather',
		'width' => $widthScheduleWeather,
		'company_id' => $company_id
	),
	27 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Resource weather',true),
		'field_name' => 'ProjectAmr.resources_weather',
		'width' => $widthResourceWeather,
		'company_id' => $company_id
	),
	28 => array(
		'column_name' => __d(sprintf($_domain, 'KPI'), 'Technical weather',true),
		'field_name' => 'ProjectAmr.technical_weather',
		'width' => $widthTechnicalWeather,
		'company_id' => $company_id
	)
);
$i = 1;
$dataView = array();
$selectMaps = array();
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$data=array();
foreach ($defaultFields as $defaultField) {
    $data = array(
		'id' => $i,
        'no.' => $i++,
		'company_id' => $company_id,
    );
	$data['column_name'] = $defaultField['column_name'];	
	$data['field_name'] = $defaultField['field_name'];	
	$data['width'] = $defaultField['width'];
    $dataView[] = $data;
}
$i18n = array(
	'-- Any --' => __('-- Any --', true)
);
?>
<script type="text/javascript">
	// var wdTable = $('.wd-table');
	// function set_table_height(){
		// if ( !wdTable.length ) return;
		// var heightTable = $(window).height() - wdTable.offset().top - 40;
		// wdTable.css({
			// height: heightTable,
		// });
	// }
	// $(document).on('ready', function(){
		// set_table_height();
	// });
	// $(window).on('resize', function(){
		// set_table_height();
	// });
	// set_table_height();
	
	var wdTable = $('.wd-table');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 45;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			heightViewPort = heightTable - 72;
			wdTable.find('.slick-viewport').height(heightViewPort);
			console.log( heightViewPort, "   ");
		}
	}
	
	var DataValidator = {};
	(function($){
        
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
		DataValidator.isUnique = function(value,args){
			var result = true;
			$.each(args.grid.getData().getItems() , function(undefined,dx){
				if(args.item.id && args.item.id == dx.id){
					return true;
				}
				return (result);
			});
			return {
				valid : result,
			};
        }
		var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
				column_name : {defaulValue : '', allowEmpty : false},
				field_name : {defaulValue : '', allowEmpty : false},
                width : {defaulValue : '' , allowEmpty : false, maxLength: 32},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns);
		});
    })(jQuery);	
</script>
			