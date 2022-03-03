
    <style type='text/css'>
        .wd-tab .wd-panel{
			border: none;
		}
		#reset-filter:hover i{
			color: #217FC2;
		}
    </style>

<?php
    echo $html->css(array('jquery.multiSelect','slick_grid/slick.grid_v2','slick_grid/slick.pager','slick_grid/slick.common_v2','slick_grid/slick.edit'));
    echo $html->script(array(
        'jquery.multiSelect', 'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom',
        'history_filter',
        'slick_grid/plugins/slick.dataexporter',
    ));
    echo $this->element('dialog_projects');
    App::import("vendor", "str_utility");
    $str_utility = new str_utility();
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
	/**
	* Update by QUANNGUYEN 19/02/2019
	*/
    $columnAlignRight = array(
        'price', 'md', 'raf', 'forecast', 'avancement', 'real_price', 'c23', 'c24', 'c25', 'c26', 'c27', 'c41', 'c43',
        'c28', 'c29', 'c31', 'c32', 'c33', 'c34', 'c35', 'c36', 'c37', 'c38', 'c39', 'consumed', 'consumed_current_year', 'import_code',
        'workload', 'overload', 'completed', 'remain', 'total_costs_var', 'internal_costs_var', 'external_costs_var',
        'external_costs_progress', 'assign_to_profit_center', 'assign_to_employee','workload_current_year', 'workload_jan',
        'consumed_jan', 'workload_feb', 'consumed_feb', 'workload_mar', 'consumed_mar', 'workload_apr', 'consumed_apr',
        'workload_may', 'consumed_may', 'workload_june', 'consumed_june', 'workload_july', 'consumed_july', 'workload_aug',
        'consumed_aug', 'workload_sept', 'consumed_sept', 'workload_oct', 'consumed_oct', 'workload_nov', 'consumed_nov',
        'workload_dec', 'consumed_dec', 'workload_first', 'workload_second', 'workload_third', 'workload_fourth', 'workload_firsts', 'workload_seconds', 'consumed_first', 'consumed_second', 'consumed_third', 'consumed_fourth', 'consumed_firsts', 'consumed_seconds'
    );
    $columnAlignRightAndEuro = array(
        'sales_sold', 'sales_to_bill', 'sales_billed', 'sales_paid', 'total_costs_budget', 'total_costs_forecast',
        'total_costs_engaged', 'total_costs_remain', 'internal_costs_budget', 'internal_costs_forecast', 'internal_costs_engaged',
        'internal_costs_remain', 'external_costs_budget', 'external_costs_forecast', 'external_costs_ordered',
        'external_costs_remain', 'external_costs_progress_euro', 'internal_costs_average'
    );
	/**
	 * Update by QUANNGUYEN 12/02/2019
	 */
    $columnAlignRightAndManDay = array(
        'internal_costs_budget_man_day', 'sales_man_day', 'total_costs_man_day', 'internal_costs_forecasted_man_day', 'external_costs_man_day',
        'workload_y', 'workload_last_one_y', 'workload_last_two_y', 'workload_last_thr_y',
        'workload_next_one_y', 'workload_next_two_y', 'workload_next_thr_y',
        'consumed_y', 'consumed_last_one_y', 'consumed_last_two_y', 'consumed_last_thr_y', 'consumed_next_one_y', 'consumed_next_two_y',
        'consumed_next_thr_y', 'manual_consumed', 'consumed', 'consumed_jan', 'consumed_feb', 'consumed_mar', 'consumed_apr',
        'consumed_may', 'consumed_june', 'consumed_july', 'consumed_aug', 'consumed_sept', 'consumed_oct', 'consumed_nov',
        'consumed_dec', 'workload', 'workload_jan', 'workload_feb', 'workload_mar', 'workload_apr', 'workload_may', 'workload_june', 'workload_july', 'workload_aug', 'workload_sept', 'workload_oct', 'workload_nov', 'workload_dec', 'workload_first', 'workload_second', 'workload_third', 'workload_fourth', 'workload_firsts', 'workload_seconds', 'consumed_first', 'consumed_second', 'consumed_third', 'consumed_fourth', 'consumed_firsts', 'consumed_seconds'
    );	
	/**
	 * End Update 19/02/2019
	 */	
    $columnNotCalculationConsumed = array(
        'id', 'no.', 'MetaData', 'name', 'long_name', 'short_name', 'code2', 'code1', 'family_id', 'subfamily_id', 'actif',
        'code4','code5', 'code6', 'code7', 'code8', 'code9', 'code10',
        'pms', 'accessible_profit', 'linked_profit', 'code3', 'start_date', 'end_date', 'c42', 'c40',
        'raf', 'c30', 'c44', 'completed', 'total_costs_var', 'internal_costs_var', 'external_costs_var', 'assign_to_profit_center',
        'assign_to_employee', 'external_costs_progress', 'activated', 'action.', 'budget_customer_id', 'project_manager_id', 'import_code', 'project'
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
    $columnEditor = array('long_name', 'short_name', 'code1', 'code2', 'code3');
    $columnSelectBox = array('family_id', 'subfamily_id', 'budget_customer_id', 'activated');
?>
<script type="text/javascript">
    HistoryFilter.auto = false;
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>_';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
    // HistoryFilter.afterLoad = function(){
    //     resizeHandler();
    // }
</script>
<style>
.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
.hasLoading{
    /*text-indent: 100%;*/
    background: url('<?php echo $this->Html->webroot('img/front/waiting-dots.gif'); ?>') no-repeat scroll 0% 105% transparent !important;
    color: #fff;
}
.row-number{
    float: right !important;
}
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
.slick-viewport{
    overflow-x: hidden !important;
    overflow-y: auto;
}
#wd-container-main {
	max-width: 1920px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project ">
                <div class="wd-title wd-activity-actions">
                    <h2 class="wd-t1"><?php echo __("Activity management", true); ?></h2>
                    <?php
                        echo $this->Form->create('Category', array('style' => 'float: left'));
                        if(!empty($actiManage)){
                            $op = ($actiManage == 1) ? 'selected="selected"' : '';
                            $in = ($actiManage == 2) ? 'selected="selected"' : '';
                            $ar = ($actiManage == 3) ? 'selected="selected"' : '';
                            $arch = ($actiManage == 4) ? 'selected="selected"' : '';
                        }
                    ?>
                        <select style="margin-right: 5px; padding: 6px;" class="wd-customs" id="FilterStatusActivityPersonalize">
                            <option value="0"><?php echo  __("--Select--", true);?></option>
                            <?php echo $personalizeList; ?>
                        </select>
                        <select style="margin-right: 5px; padding: 6px;" class="wd-customs" id="FilterStatusActivity">
                            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("Activated", true)?></option>
                            <option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Not Activated", true)?></option>
                            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Activated & Not Activated", true)?></option>
                            <option value="4" <?php echo isset($arch) ? $arch : '';?>><?php echo  __("Archived", true)?></option>
                        </select>
                    <?php
                        echo $this->Form->end();
                    ?>
                    <!--ADD CODE BY VINGUYEN 07/05/2014-->
                    <?php //echo $this->element('multiSortHtml'); ?>
                    <!--END-->
					<!--UPDATE CODE BY QUANNGUYEN 11/02/2019-->
                    <!--<a href="javascript:void(0);" id="add-activity" class="btn-text" onclick="addActivities();">
                         <i class="icon-event"></i>
                        <span><?php //__('Activity') ?></span>
                    </a>-->
                    <a href="<?php echo $this->Html->url(array('action' => 'export_excel_manage'));?>" id="export-table" class="btn btn-excel"><i class="icon-layers"></i></a>
                    <!--<a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV"  title="<?php //__('Import CSV Activity')?>"><span><?php //__('Import CSV') ?></span></a>-->
                    <!--<a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV_Activity_Task"  title="<?php //__('Import CSV Activity Task')?>"><span><?php //__('Import CSV Activity Task') ?></span></a>-->
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"><i class="icon-refresh"></i></a>
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden; margin-top: 30px;">

                </div>
                <?php echo $this->element('grid_status'); ?>
            </div></div></div>
        </div>
    </div>
</div>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
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
<?php echo $html->script('responsive_table.js'); ?>
<?php
$map = !empty($allActivityColumn) ? Set::combine($allActivityColumn, '{s}.codeTmp', '{s}.key') : array();
foreach($activityColumn as $key => $column){
    if(isset($column['calculate']) && !empty($column['calculate'])){
        $_calculate = $column['calculate'];
    } else{
        $_calculate = '';
    }
    if(isset($column['code']) && !empty($column['code'])){
        $_code = $column['code'];
    } else{
        $_code = '';
    }
    //$map['C' . $_code] = $key;
    $editor = array();
    if ($key == 'name') {
         $editor = array(
            'validator' => 'DataValidator.isUnique',
            // 'editor' => 'Slick.Editors.textBox', // Email: Z0G 15/2/2019: Enhancements activity/view
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
    else if ($column['code'] >= 32 && $column['code'] <= 39 || $column['code'] == 41) {
        $editor = array(
            'editor' => 'Slick.Editors.decimalValue'
        );
    }
    else if(($_code >= 40 && $_code <= 45) || ($_code >= 87 && $_code <= 93) ||(in_array($key, $columnEditor))){
        $editor = array(
            'editor' => 'Slick.Editors.textBox'
        );
    }
	/**
	 * Update by QUANNGUYEN 13/02/2019
	 */
    //else if ($key === 'consumed' ){
		// $editor = array(
			// 'cssClass' => 'row-number-custom',
		// );
	//}
    else if ($key === 'consumed' || $key === 'consumed_current_year' || $key === 'consumed_current_month') {
        $editor = array(
            'formatter' => 'Slick.Formatters.HyperlinkCellFormatterConsumed',
			'cssClass' => 'row-number-custom',
        );
    }
	/**
	 * End Update by QUANNGUYEN 13/02/2019
	 */
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
    $name = !empty($listDatas) && !empty($listDatas[$key]) ? $listDatas[$key] : __($column['name'], true);
    if( $key == 'manual_consumed' )$name = __d(sprintf($_domain, 'Project_Task'), $column['name'], true);
    $col = array(
        'id' => $key,
        'field' => $key,
        'name' => $name,
        'code' => 'C' . $_code,
        'calculate' => $_calculate,
        'width' => isset($history['columnWidth'][$key]) ? (int) $history['columnWidth'][$key] : 120,
        'sortable' => true,
        'resizable' => true
    );
    $cols[]=array_merge($col,$editor);
}
$no[] = array(
    'id' => 'no.',
    'field' => 'no.',
    'name' => '#',
    'width' => isset($history['columnWidth']['no.']) ? (int) $history['columnWidth']['no.'] : 40,
    'sortable' => true,
    'resizable' => false,
    'noFilter' => 1
);
$act[] =  array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => isset($history['columnWidth']['action.']) ? (int) $history['columnWidth']['action.'] : 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'ignoreExport' => true,
    'formatter' => 'Slick.Formatters.Action'
);
if(!empty($cols)){
    $columns = array_merge($no,$cols, $act);
} else {
    $columns = array_merge($no, $act);
}
$i = 1;
$dataView = array();
$totalHeaders = array();
$selectMaps = array(
    'pms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'actif' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true), 'arch' => __('Archived', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'linked_profit' => $profitCenters,
    'accessible_profit' => $profitCenters,
    'project' => $projects,
    'budget_customer_id' => $budgetCustomers,
    'project_manager_id' => $projectManagers
);
/**
 * Translation M.D, J.H trên header màn hình Activity/View
 * Update by QUANNGUYEN 12/02/2019
 */
$viewManDay = __('M.D', true);
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
	'M.D' => $viewManDay
);

/* End */

$employee_info = $this->Session->read('Auth.employee_info');
$is_sas = $employee_info['Employee']['is_sas'];
$role = ($is_sas != 1) ? $employee_info['Role']['name'] : '';
$activityListIds = array();
foreach ($activities as $activity) {
    $data = array(
        'id' => $activity['Activity']['id'],
        'no.' => $i++,
        'backupPM' => array(),
        'project_manager_id' => array(),
        'MetaData' => array()
    );
    $id = $activity['Activity']['id'];
    $data['name'] = (string) $activity['Activity']['name'];
    $data['long_name'] = (string) $activity['Activity']['long_name'];
    $data['short_name'] = (string) $activity['Activity']['short_name'];
    $data['family_id'] = (string) $activity['Activity']['family_id'];
    $data['subfamily_id'] = (string) $activity['Activity']['subfamily_id'];
    $data['pms'] = $activity['Activity']['pms'] ? 'yes' : 'no';
    $data['code1'] = (string) $activity['Activity']['code1'];
    $data['code2'] = (string) $activity['Activity']['code2'];
    $data['code3'] = (string) $activity['Activity']['code3'];
    $data['import_code'] = (string) $activity['Activity']['import_code'];
    $data['start_date'] = !empty($activity['Activity']['start_date']) ? date('d-m-Y', $activity['Activity']['start_date']) : '';
    $data['end_date'] = !empty($activity['Activity']['end_date']) ? date('d-m-Y', $activity['Activity']['end_date']) : '';
    $data['actif'] = $activity['Activity']['actif'] ? 'yes' : 'no';
    $data['c32'] = (string) $activity['Activity']['c32'];
    $data['c33'] = (string) $activity['Activity']['c33'];
    $data['c34'] = (string) $activity['Activity']['c34'];
    $data['c35'] = (string) $activity['Activity']['c35'];
    $data['c36'] = (string) $activity['Activity']['c36'];
    $data['c37'] = (string) $activity['Activity']['c37'];
    $data['c38'] = (string) $activity['Activity']['c38'];
    $data['c39'] = (string) $activity['Activity']['c39'];
    $data['c40'] = (string) $activity['Activity']['c40'];
    $data['c41'] = (string) $activity['Activity']['c41'];
    $data['c42'] = (string) $activity['Activity']['c42'];
    $data['c43'] = (string) $activity['Activity']['c43'];
    $data['c44'] = (string) $activity['Activity']['c44'];
    $data['c45'] = (string) $activity['Activity']['c45'];
    $data['project'] = (string) $activity['Activity']['project'];
    $data['budget_customer_id'] = (string) $activity['Activity']['budget_customer_id'];
    $data['code4'] = (string) $activity['Activity']['code4'];
    $data['code5'] = (string) $activity['Activity']['code5'];
    $data['code6'] = (string) $activity['Activity']['code6'];
    $data['code7'] = (string) $activity['Activity']['code7'];
    $data['code8'] = (string) $activity['Activity']['code8'];
    $data['code9'] = (string) $activity['Activity']['code9'];
    $data['code10'] = (string) $activity['Activity']['code10'];
    $data['activated'] = $activity['Activity']['activated'] ? 'yes' : 'no';
    $data['manual_consumed'] = isset($manualData[$id]) ? $manualData[$id] : 0;
    $data['accessible_profit'] = (array) isset($activityProfitRefer[$activity['Activity']['id']]) && !empty($activityProfitRefer[$activity['Activity']['id']]) ? array_merge($activityProfitRefer[$activity['Activity']['id']]) : array();
    $data['linked_profit'] = (string) !empty($linkedProfitRefer[$activity['Activity']['id']]) ? $linkedProfitRefer[$activity['Activity']['id']] : '';
    $data['project_manager_id'] = !empty($activity['Activity']['project_manager_id']) ? array($activity['Activity']['project_manager_id']) : array();
    $data['backupPM'] = !empty($activity['Activity']['project_manager_id']) ? array($activity['Activity']['project_manager_id'] => 0) : array();
    if(isset($listManger[$activity['Activity']['id']]) && !empty($listManger[$activity['Activity']['id']])){
        $backupManager = $listManger[$activity['Activity']['id']];
        $data['backupPM'] = $data['backupPM'] + $backupManager;
        $data['project_manager_id'] = array_merge($data['project_manager_id'], array_keys($backupManager));
    }
    $data['action.'] = '';
    $dataView[] = $data;
    $activityListIds[] = $activity['Activity']['id'];
}
$i=0;
$profitCentersTemp = array();
foreach($profitCenters as $key=>$value){
    $profitCentersTemp[$i]=array('key'=>$key,'value'=>$value);
    $i++;
}
$is_newDesign = !empty($employee_info['Color']['is_new_design']) ? $employee_info['Color']['is_new_design'] : 0;
?>
<script type="text/javascript">
    // var wdTable = $('.wd-table');
	// var is_newDesign = <?php echo json_encode($is_newDesign) ?>;
    // var heightTable = $(window).height() - wdTable.offset().top - 40;
    // heightTable = (heightTable < 500) ? 500 : heightTable;

    // wdTable.find('.slick-viewport').css({
        // height: heightTable - 120,
    // });
    // $(window).resize(function(){
		// if(!is_newDesign){
			// location.reload();
		// }
        // heightTable = $(window).height() - wdTable.offset().top - 240;
        // wdTable.find('.slick-viewport').css({
            // height: heightTable,
        // });
    // });

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
			clearInterval(wdTable);
		}
	}	
	
    var ControlGrid;
    // manual history filter
    var _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>;
    var historyData = new $.z0.data(_history);
    var historyPath = <?php echo json_encode($this->params['url']['url']) ?>;
    function resizeHandler(){
        var _cols = ControlGrid.getColumns();
        var _numCols = _cols.length;
        var _gridW = 0;
        var columnWidth = {};
        for (var i=0; i<_numCols; i++) {
            _gridW += _cols[i].width;
            columnWidth[_cols[i].id] = _cols[i].width;
        }
        $('#wd-header-custom').css('width', _gridW);

        historyData.set('columnWidth', columnWidth);
        // call save here
        // **
        saveFilter();
    }
    function sortHandler(ev, info){
        // if( info ){
        //     var columnSort = [{
        //         columnId: info.sortCol.id,
        //         sortAsc: info.sortAsc
        //     }];
        //     historyData.set('columnSort', columnSort);
        // } else {
        //     historyData.set('columnSort', ev);
        // }
        // // call save here
        // // **
        // saveFilter();
    }

    function applyFilter(){
        // apply filters
        // sorter
        // var sorter = historyData.get('columnSort', []);
        // if( sorter ){
        //     ControlGrid.sort(sorter);
        // }
        // // header fields
        // var filters = historyData.get('filters', {});
        // HistoryFilter.data = filters;
        // HistoryFilter.parse();
        HistoryFilter.init();
        // var header = ControlGrid.getHeaderRow();
        // $(header).find(':input[name][rel!="no-history"][type!="file"]').on('change', function(){
        //     var e = $(this);
        //     var name = e.attr("name");
        //     var val = (HistoryFilter.getVal(e, "radio") || HistoryFilter.getVal(e, name, "checkbox") || e.val());
        //     filters[name] = val;
        //     historyData.set('filters', filters);

        //     saveFilter();
        // });
    }
    var saveTimer;
    function saveFilter(){
        clearTimeout(saveTimer);
        saveTimer = setTimeout(function(){
            $.z0.History.save(historyPath, historyData);
        }, 750);
    }
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
    var activityHasLoading = new Array();
    (function($){
        $(function(){
            var $this = SlickGridCustom,families = <?php echo json_encode($mapFamilies); ?>;
            var $role = <?php echo json_encode($role);?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.accessible_profit_sort = <?php echo json_encode($profitCentersTemp); ?>;
            if($role === 'admin'){
                $this.canModified =  true;
            } else {
                $this.canModified =  false;
            }

            $this.onApplyValue = function(item){
                $.extend(item, {backupPM : []})
            };
            var  urlActivityTask = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('controller' => 'activity_tasks', 'action' => 'index', '%2$s')))); ?>;
            var  urlProjectTask = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('controller' => 'project_tasks', 'action' => 'index', '%2$s')))); ?>;
			console.log( 'urlProjectTask: ', urlProjectTask);
            var  urlDetail = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var  urlProject = <?php echo json_encode(urldecode($this->Html->link('%2$s', array('controller' => $defaultProjectScreen['controller'], 'action' => $defaultProjectScreen['action'], '%1$s')))); ?>;
            // For validate date
            var actionTemplate =  $('#action-template').html(),backup = {};
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            var  activityColumn = <?php echo json_encode($activityColumn); ?>;
            var  viewManDay = <?php echo json_encode($viewManDay); ?>;
            var projects = <?php echo json_encode($projects); ?>;
            var startCurrentYear = <?php echo json_encode('01-01-'.date('Y', time()));?>;
            var endCurrentYear = <?php echo json_encode('31-12-'.date('Y', time()));?>;
            var urlConsumedCurrentYear = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var activityListIds = <?php echo json_encode($activityListIds); ?>;
            var employeeName = <?php echo json_encode($employeeName); ?>;
            var map = <?php echo json_encode($map); ?>;


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
					if( dataContext.project != ''){
						return Slick.Formatters.HTMLData(row, cell,$this.t(urlProjectTask, value || '',dataContext.project || ''), columnDef, dataContext);
					}
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
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
                    }
                },
                numberValEuro : function(row, cell, value, columnDef, dataContext){
                    value = value ? value : 0;
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' &euro;</span>', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + number_format(value, 2, ',', ' ') + ' &euro;</span>', columnDef, dataContext);
                    }
                },
                numberValManDay : function(row, cell, value, columnDef, dataContext){
                    value = value ? value : 0;
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' + '</span> ', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + number_format(value, 2, ',', ' ') + ' ' + '</span> ', columnDef, dataContext);
                    }
                },
                GetProjectManager : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['project_manager_id'][val] + (dataContext.backupPM[val] == '1' ? backupText.text : ''));
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                }
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
                        if(e.stopPropagation) {
                            e.stopPropagation();
                        } else {
                            e.returnValue = false;
                        }
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
            $this.onCellChange = function(args){
                var result = true;
                if(args && args.column && args.column.field == 'family_id' && args.item && args.item.subfamily_id){
                    if(!families[args.item.subfamily_id] || families[args.item.subfamily_id].parent_id != args.item.family_id){
                        args.item.subfamily_id = '';
                    }
                }
                return result;
            };
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro); ?>;
            $this.columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay); ?>;
            $this.columnNotCalculationConsumed = <?php echo json_encode($columnNotCalculationConsumed); ?>;
            var totalHeaders = <?php echo json_encode($totalHeaders);?>;
            $this.moduleAction = 'activity_view_';
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
                raf : {defaulValue : ''},
                import_code : {defaulValue : ''},
                backupPM: {defaulValue : ''},
                project: {defaulValue : ''},
                md : {defaulValue : ''},
                price : {defaulValue : ''},
                manual_consumed : {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';

            ControlGrid = $this.init($('#project_container'),data,columns);
            var exporter = new Slick.DataExporter('/activities/export_excel_manage');
            ControlGrid.registerPlugin(exporter);
            $('#export-table').click(function(){
                exporter.submit();
                return false;
            });
            $(ControlGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
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
                        window.location = ('/activities/manage/'+company_id+'?actiManage=' +status+'&view=' +view);
                    }
                });
            });
            /*
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
            }*/
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
                //$('.hasLoading').parent().addClass('hasLoadings');
                //$('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onSort.subscribe(function(args, e, scope){

            });
            ControlGrid.onColumnsResized.subscribe(function (e, args) {
                resizeHandler();
            });
            /**
             * Add Column
             */
            if(columns){
                var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">';
                $.each(columns, function(_index, value){
                    var idOfHeader = 'activity_view_' + value.id;
                    var valOfHeader = '';
                    if(totalHeaders && (totalHeaders[value.id] || totalHeaders[value.id] == 0)){
                        valOfHeader = totalHeaders[value.id];
                    }
                    if($.inArray(value.id, $this.columnAlignRightAndManDay) != -1){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
                    } else if($.inArray(value.id, $this.columnAlignRightAndEuro) != -1){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' &euro;';
                    } else {
                        if(valOfHeader){
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ');
                        }
                    }
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l'+_index;
                    var right = 'r'+_index;
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom '+left+' '+right+'" id="'+idOfHeader+'"><p>'+valOfHeader+'</p></div>';
                });
                headerConsumed += '</div>';
                $('.slick-header-columns').after(headerConsumed);
            }
            var dataView = ControlGrid.getDataView();
            function ajaxRequestDataForActivity(dataSend){
                setTimeout(function(){
                    $.ajax({
                        url: '<?php echo $html->url(array('action' => 'handleDataOfActivity')); ?>',
                        //async: false,
                        type : 'POST',
                        dataType : 'json',
                        data: {
                            'activityIds' : JSON.stringify(dataSend),
                            'activityColumn' : JSON.stringify(activityColumn),
                            'employeeName' : JSON.stringify(employeeName),
                            'map' : JSON.stringify(map)
                        },
                        success:function(data) {
                            if(data){
                                dataView.beginUpdate();
                                $.each(data, function(acId, acVal){
                                    var row = ControlGrid.getData().getRowById(acId);
                                    var item = ControlGrid.getData().getItem(row);
                                    if( typeof item != 'object' )return;
                                    var extdata = $.extend(true, item, acVal);
                                    dataView.updateItem(acId, extdata);
                                    setTimeout(function(){
                                        // ControlGrid.updateRow(row);
                                        $('#row-' + acId).find('div span').removeClass('hasLoading');
                                        activityHasLoading.push(acId);
                                    }, 50);
                                });
                                setTimeout(function(){
                                    dataView.endUpdate();
                                    $('#name').trigger('keyup');
                                }, 50);
                            }
                            setTimeout(function(){
                                if(activityListIds && activityListIds.length !=0){
                                    var _sendActivity = [];
                                    if(activityListIds.length >= 500){
                                        _sendActivity = activityListIds.splice(0, 500);
                                    } else {
                                        _sendActivity = activityListIds;
                                        activityListIds = [];
                                        applyFilter();
                                    }
                                    ajaxRequestDataForActivity(_sendActivity);
                                } else {
                                    applyFilter();
                                }
                            }, 50);
                        }
                    });
                }, 50);
            }
            if(activityListIds && activityListIds.length !=0){
                var sendActivity = [];
                if(activityListIds.length >= 500){
                    sendActivity = activityListIds.splice(0, 500);
                } else {
                    sendActivity = activityListIds;
                    activityListIds = [];
                }
                ajaxRequestDataForActivity(sendActivity);
            }
        });
        function setupScroll(){
            $("#scrollTopAbsenceContent").width($(".grid-canvas").width()+50);
            $("#scrollTopAbsence").width($(".slick-viewport").width());
        }
        setTimeout(function(){
            setupScroll();
        }, 2500);
        $("#scrollTopAbsence").scroll(function () {
            $(".slick-viewport").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });
        $(".slick-viewport").scroll(function () {
            $("#scrollTopAbsence").scrollLeft($(".slick-viewport").scrollLeft());
        });
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
            // setTimeout(function(){
                // location.reload();
            // }, 500);
			$.ajax({
				url : '/employees/history_filter',
				type: 'POST',
				data: {
					data: {
						path: HistoryFilter.here,						
					}
				},
				success : function(respons){
					var _data =  $.parseJSON(respons);
					$.each(_data, function( _index, _val){
						if( _index.indexOf('Resize') == -1){
							 _data[_index]='';
						}
						
					});
					HistoryFilter.stask = _data;
					HistoryFilter.send();
					setTimeout(function(){
						location.reload();
					}, 500);
				}
			});
        }
    })(jQuery);
</script>
