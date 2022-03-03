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
    // 'dropzone.min',
    'preview/component'
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
	'progresspie/jquery-progresspiesvg-min',
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
$menu = $this->requestAction('/menus/getMenu/project_budget_externals/index');
$langCode = Configure::read('Config.langCode');
if( $langCode == 'fr' )$langCode .= 'e';
else if( $langCode == 'en' )$langCode .= 'g';
$canModified = (($modifyBudget == true && !$_isProfile) || ($_isProfile && $_canWrite)) ? true : false;
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
<!-- dialog_attachement_or_url -->
<!--  -->
<style>
	#new-external{
		top: 45px;
	}
	.wd-title{
		min-height: 40px;
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
		height: 155px;
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
<div id="template_upload" class="template_upload" style="height: auto; width: 320px;">
    <div class="heading">
        <h4><?php echo __('File upload(s)', true)?></h4>
        <span class="close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div class="wd-popup">
        <?php 
        echo $this->Form->create('Upload', array(
            'type' => 'POST',
            'url' => array('controller' => 'project_budget_externals_preview','action' => 'upload', $projectName['Project']['id'])));
            ?>
            <div class="trigger-upload"><div id="upload-popup" method="post" action="/project_budget_externals_preview/upload/<?php echo $projectName['Project']['id']; ?>"  value="" >
            </div></div>
            <?php echo $this->Form->input('url', array(
                'class' => 'not_save_history',
                'label' => array(
                    'class' => 'label-has-sub',
                    'text' =>__('URL',true),
                    'data-text' => __('(optionnel)', true),
                    ),
                'type' => 'text',
                'id' => 'newDocURL', 
                'placeholder' => __('https://', true)));
            ?>                    
            <input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="file-upoad">
        <?php echo $this->Form->end(); ?>
    </div>
    <ul class="actions" style="">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Validate') ?></a></li>
    </ul>
</div>
<div class="light-popup"></div>

<!-- dialog_attachement_or_url.end -->
<div id="dialog_auto_task" class="buttons" style="display: none;">
    <?php
        echo $this->Form->create('ProjectBudgetExternal', array('type' => 'file','url' => array('controller' => 'project_budget_externals', 'action' => 'add_auto',$project_id)
    )); ?>
        <div id = "popup-add-task">

        </div>
    <?php
        echo $this->Form->end();
    ?>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach_auto_task"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_evolutions', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin project-external">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
				<?php
					$exterBudgetProgress = !empty($valBudgetSynsExternal['external_costs_budget']) ? (float)$valBudgetSynsExternal['external_costs_budget'] : 0;
					$exterForecastProgress = !empty($valBudgetSynsExternal['external_costs_forecast']) ? (float)$valBudgetSynsExternal['external_costs_forecast'] : 0;
					$exterOrderedProgress = !empty($valBudgetSynsExternal['external_costs_ordered']) ? (float)$valBudgetSynsExternal['external_costs_ordered'] : 0;
					$exterVarProgress = !empty($exterBudgetProgress) ? ($exterForecastProgress/$exterBudgetProgress)*100 : 0;
				?>
				<div class="chard-content">
					<div class="wd-row space5">
						<div class="wd-col wd-col-lg-6">
							<div class="chard-external wd-budget-chart clear-fix">
									<div class="budget-progress-circle">
										<div id="progress-circle-internal" class="step wd-progress-pie" data-val="<?php echo $exterVarProgress; ?>"></div>
									</div>
								<div class="progress-values">
									<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'External_Cost'), 'External Cost', true)?></h3>
									<div class ="progress-value progress-budget"><p><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true)?></p><span><?php echo number_format($exterBudgetProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
									<div class ="progress-value progress-forecast"><p><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true)?></p><span><?php echo number_format($exterForecastProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
									<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true)?></p><span><?php echo number_format($exterOrderedProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
								</div>
							</div>
						</div>
						<div class="wd-col wd-col-lg-6">
							<div class="graph-internal wd-budget-chart">
							<?php
								$file = 'widgets'.DS.'external_progress_line';
								$options = array(
									'type' => 'monthyear',
									'chartHeight' => '200'
								);
								echo $this->element($file, $options);
							?>
						</div>
						</div>
					</div>
				</div>
			</div></div>
			<div class="wd-tab"><div class="wd-panel">
			<div class="wd-list-project">
				<div class="wd-title">
					<a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable();" title="<?php __("Expand"); ?>"></a>
					<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
					<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="<?php __('Collapse table') ?>" style="display: none;"></a>
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
					<a href="javascript:void(0);" class="btn btn-plus-green" id="new-external" title="<?php __('Add an external cost') ?>" onclick="addExternalCost();">
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
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Name', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBoxCustom'
    ),
    array(
        'id' => 'order_date',
        'field' => 'order_date',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Order date', true),
        'width' => 150,
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
        'id' => 'budget_provider_id',
        'field' => 'budget_provider_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Provider', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'budget_type_id',
        'field' => 'budget_type_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Type', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'capex_id',
        'field' => 'capex_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'CAPEX/OPEX', true),
        'width' => 190,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'budget_erro',
        'field' => 'budget_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Budget €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'forecast_erro',
        'field' => 'forecast_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'var_erro',
        'field' => 'var_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Var', true),
        'width' => 80,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValues'
        //'editor' => 'Slick.Editors.numericValue'
    ),
    array(
        'id' => 'ordered_erro',
        'field' => 'ordered_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'remain_erro',
        'field' => 'remain_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Remain €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.selectBox'
    ),
    // array(
    //     'id' => 'actionaddtask',
    //     'field' => 'actionaddtask',
    //     'name' => __d(sprintf($_domain, 'External_Cost'), '&nbsp', true),
    //     'width' => 40,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.ActionAddTask'
    // ),
    array(
        'id' => 'man_day',
        'field' => 'man_day',
        'name' => __d(sprintf($_domain, 'External_Cost'), $md, true),
        'width' => 140,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudgetEdit',
        'formatter' => 'Slick.Formatters.manDayValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'special_consumed',
        'field' => 'special_consumed',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Consumed', true),
        'width' => 140,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
       // 'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.consumedValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'progress_md',
        'field' => 'progress_md',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Progress %', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericProgressEdit',
        'formatter' => 'Slick.Formatters.percentValues'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'progress_erro',
        'field' => 'progress_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Progress €', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'opex_calculated',
        'field' => 'opex_calculated',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'OPEX €', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'capex_calculated',
        'field' => 'capex_calculated',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'CAPEX €', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'file_attachement',
        'field' => 'file_attachement',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Attachement or URL', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.Attachement',
        'formatter' => 'Slick.Formatters.Attachement'
    ),
    array(
        'id' => 'reference',
        'field' => 'reference',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'reference2',
        'field' => 'reference2',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference 2', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    //CHANGED HERE
    array(
        'id' => 'reference3',
        'field' => 'reference3',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference 3', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'reference4',
        'field' => 'reference4',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference 4', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'expected_date',
        'field' => 'expected_date',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Expected date', true),
        'width' => 150,
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
        'id' => 'due_date',
        'field' => 'due_date',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Due date', true),
        'width' => 150,
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
        'id' => 'profit_center_id',
        'field' => 'profit_center_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Profit Center', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
     array(
        'id' => 'funder_id',
        'field' => 'funder_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Funder', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    // array(
    //     'id' => 'action.',
    //     'field' => 'action.',
    //     'name' => __('Action', true),
    //     'width' => 70,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.Action'
    // )
);

$settings = $this->requestAction('/translations/getSettings',
    array('pass' => array(
        'External_Cost',
        array('external_cost')
    )
));

//su dung setting de sort lai $columns
$columns = Set::combine($columns, '{n}.field', '{n}');
$new = array();
foreach ($settings as $field => $value) {
    if( isset($columns[$field]) ){
        if( $field == 'man_day' ){
            $new[] = array(
                'id' => 'actionaddtask',
                'field' => 'actionaddtask',
                'name' => '&nbsp',
                'width' => 40,
                'sortable' => false,
                'resizable' => true,
                'noFilter' => 1,
                'formatter' => 'Slick.Formatters.ActionAddTask'
            );
        }
        $new[] = $columns[$field];
    }
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
    'budget_provider_id' => $budgetProviders,
    'budget_type_id' => $budgetTypes,
    'capex_id' =>  array('capex' => __('CAPEX', true), 'opex' => __('OPEX', true)),
    'profit_center_id' => $profits,
    'funder_id' => $funders
);
$capex_opex = array(
	'opex', 
	'capex', 
);
foreach ($budgetExternals as $key=>$budgetExternal) {
    $data = array(
        'id' => $budgetExternal['ProjectBudgetExternal']['id'],
        'project_id' => $budgetExternal['ProjectBudgetExternal']['project_id'],
        'activity_id' => $activityLinked,
        'no.' => $i++
    );
    $_id = $budgetExternal['ProjectBudgetExternal']['id'];
    $data['name'] = (string) $budgetExternal['ProjectBudgetExternal']['name'];
    $data['order_date'] = $str_utility->convertToVNDate($budgetExternal['ProjectBudgetExternal']['order_date']);

    $data['budget_provider_id'] = (string) $budgetExternal['ProjectBudgetExternal']['budget_provider_id'];
    $data['budget_type_id'] = (string) $budgetExternal['ProjectBudgetExternal']['budget_type_id'];
	$data['capex_id'] = '';
	// #2541 capex_opex can be select, default null
	if( isset($budgetExternal['ProjectBudgetExternal']['capex_id'])){
		$data['capex_id'] = @$capex_opex[$budgetExternal['ProjectBudgetExternal']['capex_id']];
	}
    $data['budget_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['budget_erro'];
    $data['ordered_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['ordered_erro'];
    $data['remain_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['remain_erro'];
    $totalconsumed = !empty($taskExternals[$_id]['consumed']) ? $taskExternals[$_id]['consumed'] : '';
    $data['special_consumed'] = (string) $totalconsumed;
    $totalManday = !empty($taskExternals[$_id]) && !empty($taskExternals[$_id]['maday']) ? $taskExternals[$_id]['maday'] : '';
    $progress_md = ($totalManday == 0 || $totalManday == '') ? 0 : round(($totalconsumed/$totalManday) * 100, 10);
    if(!empty($totalconsumed) && !empty($budgetExternal['ProjectBudgetExternal']['man_day'])){
        $data['progress_md'] = !empty($progress_md) ? $progress_md : '';
    } else {
        $data['progress_md'] = ($budgetExternal['ProjectBudgetExternal']['progress_md'] != 0) ? $budgetExternal['ProjectBudgetExternal']['progress_md'] : $progress_md;
    }
    $data['progress_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['progress_erro'];
    $data['man_day'] = (string) $budgetExternal['ProjectBudgetExternal']['man_day'];
    $data['file_attachement'] = (string) $budgetExternal['ProjectBudgetExternal']['file_attachement'];
    $data['format'] = (string) $budgetExternal['ProjectBudgetExternal']['format'];
    $data['reference'] = (string) $budgetExternal['ProjectBudgetExternal']['reference'];
    $data['reference2'] = (string) $budgetExternal['ProjectBudgetExternal']['reference2'];
    //CHANGED HERE
    $data['reference3'] = (string) $budgetExternal['ProjectBudgetExternal']['reference3'];
    $data['reference4'] = (string) $budgetExternal['ProjectBudgetExternal']['reference4'];
    $data['expected_date'] = $str_utility->convertToVNDate($budgetExternal['ProjectBudgetExternal']['expected_date']);
    $data['due_date'] = $str_utility->convertToVNDate($budgetExternal['ProjectBudgetExternal']['due_date']);
    //END
    $data['profit_center_id'] = $budgetExternal['ProjectBudgetExternal']['profit_center_id'];
    $data['funder_id'] = $budgetExternal['ProjectBudgetExternal']['funder_id'];

    //calculated
    $ordered_erro = !empty($budgetExternal['ProjectBudgetExternal']['ordered_erro']) ? $budgetExternal['ProjectBudgetExternal']['ordered_erro'] : 0;
    $remain_erro = !empty($budgetExternal['ProjectBudgetExternal']['remain_erro']) ? $budgetExternal['ProjectBudgetExternal']['remain_erro'] : 0;
    $budget_erro = !empty($budgetExternal['ProjectBudgetExternal']['budget_erro']) ? $budgetExternal['ProjectBudgetExternal']['budget_erro'] : 0;
    $progress_md = $data['progress_md'];

    $forecast_erro = $ordered_erro+$remain_erro;
    if($budget_erro == 0){
        $var_erro = (0-1)*100;
    } else {
        $var_erro = (($forecast_erro/$budget_erro)-1)*100;
    }

    $data['forecast_erro'] = $forecast_erro;
    $data['var_erro'] = round($var_erro, 2);
    $data['progress_erro'] = ($ordered_erro*$progress_md)/100;
	if($budgetExternal['ProjectBudgetExternal']['capex_id'] === 1){
        $data['opex_calculated'] = 0;
        $data['capex_calculated'] = $ordered_erro+$remain_erro;
    } elseif($budgetExternal['ProjectBudgetExternal']['capex_id'] === 0){
        $data['opex_calculated'] = $ordered_erro+$remain_erro;
        $data['capex_calculated'] = 0;
    } else {
		// ob_clean(); debug( $budgetExternal['ProjectBudgetExternal']); exit;
	}


    $data['action.'] = '';
	$data['moveline'] = '';
    $data['actionaddtask'] = '';

    $dataView[] = $data;
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
<div id="action-template-add-task" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big" style="margin-left:2px;">
            <a href="javascript:;" onclick="loadTemplateAddTask(this.id);" id="add_task_auto-<?php echo '%1$s';?>-<?php echo '%2$s';?>" class="pch_add_phase wd-hover-advance-tooltip" href="#">Add task</a>
        </div>
    </div>
</div>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
var project_id = <?php echo json_encode($project_id); ?>;
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
    var budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
    var DateValidate = {},ControlGrid,IuploadComplete = function(json){
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
    function loadTemplateAddTask(id){
        $('#dialog_auto_task').dialog({
            //position    :['top',125],
            autoOpen    : false,
            autoHeight  : false,
            modal       : true,
            width       : 800,
            height      : 600,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialogAuto = $.noop;
        $("#dialog_auto_task").dialog('open');
        var _id = id.split('-');

        var href = "/project_budget_externals/add_auto_task/"+parseInt(_id[1])+'/'+parseInt(_id[2]);
        $('#popup-add-task').html("<img src=<?php echo $this->Html->url('/img/loading_check.gif');?> />");
        $.ajax({
            url :href,
            cache : false,
            type : 'POST',
            success : function(data){
                $('#popup-add-task').html(data);
            }
        });
    }
   
	var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-phase{fill:none;}.b-phase{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-phase" width="40" height="40" transform="translate(-4 -4)"/><path class="b-phase" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
    (function($){

        $(function(){
            var $this = SlickGridCustom, gridControl;


            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = ControlGrid.getDataItem(row);
                if(data && confirm($this.t('Are you sure you want to delete attachement : %s'
                , data['file_attachement']))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['file_attachement'] = '';
                    ControlGrid.updateRow(row);
                }
                return false;
            });

            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

            var actionTemplate =  $('#action-template').html(),
            actionTemplateAddTask =  $('#action-template-add-task').html(),
            attachmentTemplate =  $('#attachment-template').html(),
            attachmentURLTemplate =  $('#attachment-template-url').html();

            $.extend(Slick.Formatters,{
                Attachement : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 2){
                            value = $this.t(attachmentTemplate,dataContext.id,row);
                        }
                        if(dataContext.format == 1){
                            value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.file_attachement,row);
                        }
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.name), columnDef, dataContext);
                },
                ActionAddTask : function(row, cell, value, columnDef, dataContext){
                    if(dataContext.man_day!=''){
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplateAddTask,dataContext.id,
                        dataContext.project_id,dataContext.name), columnDef, dataContext);
                    }else{
                        return Slick.Formatters.HTMLData(row, cell, '', columnDef, dataContext);
                    }
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + budget_settings + '</span>', columnDef, dataContext);
                },
                consumedValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                   return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> '+viewManDay, columnDef, dataContext);
                },
                percentValues : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span>%', columnDef, dataContext);
                },
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell,'<span class="row-number">' + value + '</span> ' + viewManDay, columnDef, dataContext);
                },
				moveLine: function(row, cell, value, columnDef, dataContext){
					return _menu_svg;
				},
                DateTime : function(row, cell, value, columnDef, dataContext){
                    return '<div class="cell-data" style="float: right;"><span style="text-align: right">' + value + '</span></div>';
                }
            });

            $.extend(Slick.Editors,{
                Attachement : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
                    this.input = $("<a href='#' data-id="+ args.item.id +" id='action-attach-url'></a><div class='browse'></div>")
                    .appendTo(args.container).attr('rel','no-history').addClass('editor-text');
                    
                    this.focus();
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
                            if(!/^[\-]?([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
                                e.preventDefault();
                                return false;
                            }
                        });
                },
                numericValueBudgetEdit : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    if(args.item.progress_md>0){
                        this.input.attr('disabled','disabled');
                        this.input.css('background','none');
                        this.input.css('background-color','#F5F5F5');
                    }else{
                        this.input.attr('maxlength' , 18).keypress(function(e){
                            var key = e.keyCode ? e.keyCode : e.which;
                            if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                                return;
                            }
                            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                            ///^[\-+]??$/
                            //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
                            if(!/^[\-]?([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
                                e.preventDefault();
                                return false;
                            }
                        });
                    }
                },
                numericProgressEdit : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    if(args.item.man_day!=0){
                        this.input.attr('disabled','disabled');
                        this.input.css('background','none');
                        this.input.css('background-color','#F5F5F5');
                    }else{
                        this.input.attr('maxlength' , 18).keypress(function(e){
                            var key = e.keyCode ? e.keyCode : e.which;
                            if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                                return;
                            }
                            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                            ///^[\-+]??$/
                            //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
                            if(!/^[\-]?([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
                                e.preventDefault();
                                return false;
                            }
                        });
                    }
                },
                textBoxCustom : function(args){
                    $.extend(this, new BaseSlickEditor(args));
                    this.input = $("<input type='text' placeholder='New External Cost'/>")
                    .appendTo(args.container).attr('rel','no-history').addClass('editor-text placeholder');
                    this.focus();
                },
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var capexTypes = <?php echo json_encode($capexTypes); ?>;
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
                order_date : {defaulValue : ''},
                budget_provider_id : {defaulValue : ''},
                budget_type_id : {defaulValue : ''},
                capex_id : {defaulValue : ''},
                profit_center_id : {defaulValue : 0},
                funder_id : {defaulValue : 0},
                budget_erro : {defaulValue : ''},
                ordered_erro : {defaulValue : ''},
                remain_erro : {defaulValue : ''},
                progress_erro : {defaulValue : ''},
                progress_md : {defaulValue : ''},
                special_consumed : {defaulValue : ''},
                man_day : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                reference : {defaulValue : ''},
                reference2 : {defaulValue : ''},
                //CHANGED HERE
                reference3 : {defaulValue : ''},
                reference4 : {defaulValue : ''},
                expected_date : {defaulValue : ''},
                due_date : {defaulValue : ''},
                activity_id : {defaulValue : activityLinked}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.onBeforeEdit = function(args){
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').parent().addClass('rowCurrentEdit');
                //$('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
                    return false;
                }
                return true;
            }
            ControlGrid = $this.init($('#project_container'),data,columns, {
                // frozenColumn: 1,
                rowHeight: 42,
                headerRowHeight: 42,                
                // forceFitColumns: true
            });
			ControlGrid.setSelectionModel(new Slick.RowSelectionModel());
			$(window).resize(function(){
				reGrid();
			});

            var _budgetErros = _orderedErros = _remainErros = _forecastErros = _varErros = _progressErros = _opexCalcul = _capexCalcul = _manDay = _progressMd = SpecialConsumed =0;
            var totalRecord = 0;
            $.each(data, function(ind, vl){
                _budgetErros += vl.budget_erro ? parseFloat(vl.budget_erro) : 0;
                SpecialConsumed += vl.special_consumed ? parseFloat(vl.special_consumed) : 0;
                _orderedErros += vl.ordered_erro ? parseFloat(vl.ordered_erro) : 0;
                _remainErros += vl.remain_erro ? parseFloat(vl.remain_erro) : 0;
                _forecastErros += parseFloat(vl.forecast_erro);
                _progressErros += parseFloat(vl.progress_erro);
                _opexCalcul += vl.opex_calculated ? parseFloat(vl.opex_calculated) : 0;
                _capexCalcul += vl.capex_calculated ? parseFloat(vl.capex_calculated) : 0;
                _manDay += vl.man_day ? parseFloat(vl.man_day) : 0;
                _progressMd += vl.progress_md ? parseFloat(vl.progress_md) : 0;
                totalRecord++;
            });
            //_progressMd = (totalRecord == 0) ? '0%' : (_progressMd/totalRecord).toFixed(2) + '%';
            var budgetEuro = _budgetErros;
            var forecastEuros = _forecastErros;
            _budgetErros = _budgetErros.toFixed(2);
            _progressErros = _progressErros.toFixed(2);
            _progressMd = (_orderedErros == 0) ? '0%' : ((_progressErros/_orderedErros)*100).toFixed(2) + '%';
            var _calCulVarErro;
            if(_budgetErros == 0){
                _calCulVarErro = (0-1)*100;
            } else {
                _calCulVarErro = ((_forecastErros/_budgetErros)-1)*100;
            }
            _calCulVarErro = _calCulVarErro.toFixed(2);
            _varErros = number_format(_calCulVarErro, 2, ',', ' ') + '%';
            _budgetErros = number_format(_budgetErros, 2, ',', ' ') + ' '+ budget_settings;
            SpecialConsumed = number_format(SpecialConsumed, 2, ',', ' ') + ' ' + viewManDay;
            _forecastErros = number_format(_forecastErros, 2, ',', ' ')+ ' '+ budget_settings;
            _orderedErros = number_format(_orderedErros, 2, ',', ' ') + ' '+ budget_settings;
            _remainErros = number_format(_remainErros, 2, ',', ' ')+ ' '+ budget_settings;
            _progressErros = number_format(_progressErros, 2, ',', ' ')+ ' '+ budget_settings;
            _opexCalcul = number_format(_opexCalcul, 2, ',', ' ') + ' '+ budget_settings;
            _capexCalcul = number_format(_capexCalcul, 2, ',', ' ') + ' '+ budget_settings;
            _manDay = number_format(_manDay, 2, ',', ' ') + ' ' + viewManDay;
            ControlGrid.onSort.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onScroll.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
            });
            $this.onCellChange = function(args){
				if( args.column.field == "budget_type_id"){
					args.item.capex_id = capexTypes[args.item.budget_type_id] ? 'capex' : 'opex';
				}
                $('.row-number').parent().addClass('row-number-custom');
                if(args.item){
					var budget_erro = args.item.budget_erro ? parseFloat(args.item.budget_erro) : 0;
                    var forecast_erro = args.item.forecast_erro ? parseFloat(args.item.forecast_erro) : 0;
                    var ordered_erro = args.item.ordered_erro ? parseFloat(args.item.ordered_erro) : 0;
                    var remain_erro = args.item.remain_erro ? parseFloat(args.item.remain_erro) : 0;
                    var progress_md = args.item.progress_md ? parseFloat(args.item.progress_md) : 0;
                    var _var_erro;

                    args.item.forecast_erro = ordered_erro+remain_erro;
                    if(budget_erro == 0){
                        _var_erro = -100;
                    } else {
                        _var_erro = (((ordered_erro+remain_erro)/budget_erro)-1)*100;
                    }
                    args.item.var_erro = _var_erro.toFixed(2);
                    args.item.progress_erro = ((ordered_erro*progress_md)/100).toFixed(2);

                    if(args.item.capex_id == 'capex'){
                        args.item.opex_calculated = 0;
                        args.item.capex_calculated = ordered_erro+remain_erro;
                    } 
					if( args.item.capex_id == 'opex') {
                        args.item.opex_calculated = ordered_erro+remain_erro;
                        args.item.capex_calculated = 0;
                    }
                    _budgetErros = _orderedErros = _remainErros = _forecastErros = _progressErros = _opexCalcul = _capexCalcul = _manDay = _progressMd = 0;
                    totalRecord = 0;
                    $.each(data, function(ind, vl){
                        _budgetErros += vl.budget_erro ? parseFloat(vl.budget_erro) :0;
                        _orderedErros += vl.ordered_erro ? parseFloat(vl.ordered_erro) : 0;
                        _remainErros += vl.remain_erro ? parseFloat(vl.remain_erro) : 0;
                        _forecastErros += parseFloat(vl.forecast_erro);
                        _progressErros += parseFloat(vl.progress_erro);
                        _opexCalcul += vl.opex_calculated ? parseFloat(vl.opex_calculated) : 0;
                        _capexCalcul += vl.capex_calculated ? parseFloat(vl.capex_calculated) : 0;
                        _manDay += vl.man_day ? parseFloat(vl.man_day) : 0;
                        _progressMd += vl.progress_md ? parseFloat(vl.progress_md) : 0;
                        totalRecord++;
                    });
                    //_progressMd = (totalRecord == 0) ? '0%' : (_progressMd/totalRecord).toFixed(2) + '%';
                    _forecastErros = Number(_forecastErros.toFixed(2));
                    _budgetErros = Number(_budgetErros.toFixed(2));
                    _progressErros = Number(_progressErros.toFixed(2));
                    _progressMd = (_orderedErros == 0) ? '0%' : ((_progressErros/_orderedErros)*100).toFixed(2) + '%';
                    var progressVar = 0;
					if(_budgetErros == 0){
                        _calCulVarErro = (0-1)*100;
                    } else {
                        _calCulVarErro = ((_forecastErros/_budgetErros)-1)*100;
						progressVar = parseInt((_forecastErros/_budgetErros)*100);
                    }
					
                    _calCulVarErro = _calCulVarErro.toFixed(2);
                    // if(_calCulVarErro > 0){
                        // $('#gs-var-erro').parent().addClass('invai-var');
                    // } else {
                        // $('#gs-var-erro').parent().removeClass('invai-var');
                    // }
                    _varErros = number_format(_calCulVarErro, 2, ',', ' ') + '%';
                    _budgetErros = number_format(_budgetErros, 2, ',', ' ') + ' '+ budget_settings;
                    SpecialConsumed = number_format(SpecialConsumed, 2, ',', ' ') + ' ' + viewManDay;
                    _forecastErros = number_format(_forecastErros, 2, ',', ' ') + ' '+ budget_settings;
                    _orderedErros = number_format(_orderedErros, 2, ',', ' ') + ' '+ budget_settings;
                    _remainErros = number_format(_remainErros, 2, ',', ' ') + ' '+ budget_settings;
                    _opexCalcul = number_format(_opexCalcul, 2, ',', ' ') + ' '+ budget_settings;
                    _capexCalcul = number_format(_capexCalcul, 2, ',', ' ') + ' '+ budget_settings;
                    _progressErros = number_format(_progressErros, 2, ',', ' ') + ' '+ budget_settings;
                    _manDay = number_format(_manDay, 2, ',', ' ') + ' ' + viewManDay;
                    $('#gs-budget-erro').html(_budgetErros);
                    $('.progress-values .progress-budget span').html(_budgetErros);
                    $('#gs-ordered-erro').html(_orderedErros);
                    $('.progress-values .progress-engaged span').html(_orderedErros);
                    $('#gs-remain-erro').html(_remainErros);
                    $('#gs-forecast-erro').html(_forecastErros);
                    $('.progress-values .progress-forecast span').html(_forecastErros);
                    $('#gs-var-erro').html(_varErros);
                    $('#gs-progress-erro').html(_progressErros);
                    $('#gs-opex-calcul').html(_opexCalcul);
                    $('#gs-capex-calcul').html(_capexCalcul);
                    $('#gs-man-day').html(_manDay);
                    $('#gs-progress-manDay').html(_progressMd);
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
            addExternalCost = function(){
                ControlGrid.gotoCell(data.length, 1, true);
            }
            // Chuyen header sang mau xanh la cay
            var headers = $('.slick-header-columns').get(1).children;
            $.each(headers, function(index, val){
                if(headers[index].id.indexOf('man_day') != -1 || headers[index].id.indexOf('special_consumed') != -1 ){ //headers[index].id.indexOf('actionaddtask') != -1 ||
                    $('#'+headers[index].id).addClass('gs-custom-cell-md-header');
                    $('.gs-custom-cell-md-header').css('border-right','none');
                    $('.l11').css('border-right-style','none');
                }
            });
            var cols = ControlGrid.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
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
            $(ControlGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
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
                    url : '<?php echo $html->url('/project_budget_externals_preview/order/' . $projectName['Project']['id']) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                })
                ControlGrid.resetActiveCell();
                var dataView = ControlGrid.getDataView();
                dataView.beginUpdate();
                dataView.setItems(data);
                dataView.endUpdate();
                ControlGrid.setSelectedRows(selectedRows);
                ControlGrid.render();
            });

            ControlGrid.registerPlugin(moveRowsPlugin);
            ControlGrid.onDragInit.subscribe(function (e, dd) {
                e.stopImmediatePropagation();
            });
			ControlGrid.onColumnsResized.subscribe(function (e, args) {
				setupScroll();
			});
			
            var settings = <?php echo json_encode(array_keys($settings)); ?>;
            var _header = {
                name: {id: 'gs-name', val: 'Total', cl: 'gsEuro'},
                budget_erro: {id: 'gs-budget-erro',val: _budgetErros, cl: 'gsEuro'},
                forecast_erro: {id: 'gs-forecast-erro',val: _forecastErros, cl: 'gsEuro'},
                var_erro: {id: 'gs-var-erro',val: _varErros, cl: 'gsEuro'},
                ordered_erro: {id: 'gs-ordered-erro',val: _orderedErros, cl: 'gsEuro'},
                remain_erro: {id: 'gs-remain-erro',val: _remainErros, cl: 'gsEuro'},
                man_day: {id: 'gs-man-day',val: _manDay, cl: 'gsMd'},
                special_consumed: {id: 'gs-special-consumed',val: SpecialConsumed, cl: 'gsMd'},
                progress_md: {id: 'gs-progress-manDay',val: _progressMd, cl: 'gsEuro'},
                progress_erro: {id: 'gs-progress-erro',val: _progressErros, cl: 'gsEuro'},
                opex_calculated: {id: 'gs-opex-calcul',val: _opexCalcul, cl: 'gsEuro'},
                capex_calculated: {id: 'gs-capex-calcul',val: _capexCalcul, cl: 'gsEuro'}
            };
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
					if(field != 'var_erro'){
						$(ControlGrid.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p>');
					}else{
						$(ControlGrid.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p><span id="bg_color" style="width:'+_varErros+'; background-color:'+$bgColorVar+'" ></span>');
					}
                }
            });
            $('.gsMd').parent().addClass('gs-custom-cell-md');
            if(forecastEuros > budgetEuro){
				$('.hoz_line').addClass('red-internal');
				$('.ver_line').addClass('red-internal');
			}else{
				$('.hoz_line').addClass('green-internal');
				$('.ver_line').addClass('green-internal');
			}
            /* table .end */
            var createDialog = function(){
                $('#dialog_attachement_or_url').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialog = $.noop;
            }

            $("#action-attach-url").live('click',function(){
                $('#template_upload').addClass('show');
                $('.light-popup').addClass('show');
            });
            $(".close-popup, .cancel").live('click',function(){
                $('#template_upload').removeClass('show');
                $('.light-popup').removeClass('show');
            });
            $('body').on('click', function(e){
                if( !$(e.target).hasClass('template_upload') && !$('.template_upload').find(e.target).length){
                    $('#template_upload').removeClass('show');
                    $('.light-popup').removeClass('show');
                }
            });
             /* add auto task */
            var createDialogAuto = function(){
                $('#dialog_auto_task').dialog({
                    //position    :['top',125],
                    autoOpen    : false,
                    autoHeight  : false,
                    modal       : true,
                    width       : 800,
                    height      : 600,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialogAuto = $.noop;
            }


            $(".cancel").live('click',function(){
                $("#dialog_attachement_or_url").dialog('close');
                $("#dialog_auto_task").dialog('close');
            });
            $("#ok_attach").live('click',function(){
                id = $('input[name="data[Upload][id]"]').data('id');
                var form = $("#UploadIndexForm");
                // form.find('input[name="data[Upload][id]"]').val(id);
                form.submit();
                 
            });
            $('#action-attach-url').live('click',function(){
                id = $(this).data('id');
                var form = $("#UploadIndexForm");
                form.find('input[name="data[Upload][id]"]').val(id);
            });
            $("#gs-url").click(function(){
                $(this).addClass('gs-url-add');
                $('#gs-attach').addClass('gs-attach-remove');
                $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
                $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $("#gs-attach").click(function(){
                $(this).removeClass('gs-attach-remove');
                $('#gs-url').removeClass('gs-url-add');
                $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
                $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('.row-number').parent().addClass('row-number-custom');
            // custom fucntion for new dessign

            reGrid = function(){
                ControlGrid.resizeCanvas();
                function setupScroll(){
                    //$("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
                    $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
                    $("#scrollTopAbsence").css({
                        'margin': '0px 0px 10px 0px',
                        'margin-left' : $(".grid-canvas-left:first").width(),
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
            expandTable = function(){
                $('.wd-list-project').addClass('fullScreen');
                reGrid();
                $('#table-collapse').show();
                $('#table-expand').hide();
            }
            
            collapse_table = function(){
                $('.wd-list-project').removeClass('fullScreen');
                reGrid();
                $('#table-collapse').hide();
                $('#table-expand').show();
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
	function wd_oncellchange_callback(data){
		var cont = $('#progress-line-ex');
		cont.addClass('loading');
		$.ajax({
			url: '/project_budget_synthesis/progress_line/' + project_id + '/ajax',
			type: 'get',
			dataType: 'json',
			success: function(res){
				update_data_external(res);
				
			},
			complete: function(){
				cont.removeClass('loading');
			}
		});
	}
	function update_data_external(synthesys_data){
		ex_maxValue = synthesys_data.max_externals;
		ex_dataSets = synthesys_data.dataset_externals;
		ex_settings.source = ex_dataSets;
		ex_settings.seriesGroups[0]['valueAxis']['maxValue'] = ex_maxValue;
		ex_settings.seriesGroups[0]['valueAxis']['unitInterval'] = ex_maxValue;
		$('#chartContainerex').width( 50 * synthesys_data.countLineExter);
		$('#chartContainerEx').jqxChart(ex_settings);
	}
	
</script>
<style>
div < .row-number{
    text-align:right;
}
</style>
<script>
   
    //format float number
    function number_format(number, decimals, dec_point, thousands_sep) {
      // Strip all characters but numerical ones.
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
    // submit form
    $('#ok_attach_auto_task').click(function(){
        var form = $("#ProjectBudgetExternalIndexForm");
        form.submit();
    });
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
	var pie_progress = $('#progress-circle-internal');
	var progress_width = pie_progress.width();
	// console.log(progress_width);
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
					// textContent: ('<?php __('Progress');?>').toUpperCase(),
					textContent: '',
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
</script>

