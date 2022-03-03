<?php echo $html->css(array('jquery.multiSelect','slick_grid/slick.grid_v2','slick_grid/slick.pager','slick_grid/slick.common_v2','slick_grid/slick.edit')); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
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
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
.slick-viewport-left{
    overflow-x: hidden !important;
    overflow-y: auto;
}
.slick-viewport-right{
    overflow: hidden !important;
}
.wd-tab .wd-panel{
	border: none;
}
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
<?php
    echo $this->Form->create('Category');
    if(!empty($activities_activated)){
        $op = ($activities_activated == 1) ? 'selected="selected"' : '';
        $in = ($activities_activated == 2) ? 'selected="selected"' : '';
        $ar = ($activities_activated == 3) ? 'selected="selected"' : '';
        $arch = ($activities_activated == 4) ? 'selected="selected"' : '';
    }
?>
<?php
    echo $this->Form->end();
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title wd-activity-actions">
                    <h2 class="wd-t1"><?php echo __("Activity management", true); ?></h2>
                    <select style="float: none; padding: 6px" class="wd-customs" id="FilterStatusActivity">
                        <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("Activated", true)?></option>
                        <option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Not Activated", true)?></option>
                        <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Activated & Not Activated", true)?></option>
                        <option value="4" <?php echo isset($arch) ? $arch : '';?>><?php echo  __("Archived", true)?></option>
                    </select>
                    <?php echo $this->element('multiSortHtml'); ?>
                    <a href="javascript:void(0);" id="add-activity" class="btn btn-text" onclick="addActivities();">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>
                    </a>
                    <!-- <a href="#" id="export-submitplus" class="export-excel-icon-all" title="<?php __('Export Excel')?>"><span><?php __('Export +') ?></span></a> -->
                    <a href="<?php echo $this->Html->url(array('action' => 'export_excel_index'));?>" id="export-table" class="btn btn-excel"><i class="icon-layers"></i></a>
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV"  title="<?php __('Import CSV Activity')?>"><span><?php __('Import CSV') ?></span></a>
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV_Activity_Task"  title="<?php __('Import CSV Activity Task')?>"><span><?php __('Import CSV Activity Task') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
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
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden;">

                </div>
                <?php echo $this->element('grid_status'); ?>
            </div></div></div>
        </div>
    </div>
</div>
<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV Activity" class="buttons" style="display: none">
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
<div id="dialog_import_CSV_activity_task" title="Import CSV Activity Task" class="buttons" style="display: none">
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
<?php
echo $this->element('dialog_projects');
echo $html->script(array(
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
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
    'responsive_table.js',
    'slick_grid/slick.grid.activity',
    'slick_grid/plugins/slick.dataexporter',
));
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

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => $activityColumn['name']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'validator' => 'DataValidator.isUnique',
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'long_name',
        'field' => 'long_name',
        'name' => $activityColumn['long_name']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'short_name',
        'field' => 'short_name',
        'name' => $activityColumn['short_name']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'family_id',
        'field' => 'family_id',
        'name' => $activityColumn['family_id']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'subfamily_id',
        'field' => 'subfamily_id',
        'name' => $activityColumn['subfamily_id']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'budget_customer_id',
        'field' => 'budget_customer_id',
        'name' => __('Customer', true),
        'width' => 280,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'project_manager_id',
        'field' => 'project_manager_id',
        'name' => __('Project Manager', true),
        'width' => 280,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.GetProjectManager',
        'formatter' => 'Slick.Formatters.GetProjectManager'
    ),
    array(
        'id' => 'accessible_profit',
        'field' => 'accessible_profit',
        'name' => $activityColumn['accessible_profit']['name'],
        'width' => 280,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBoxCustom', //MODIFY BY VINGUYEN 16/05/2014
        //'editor' => 'Slick.Editors.mselectBox',
        'formatter' => 'Slick.Formatters.selectBox'
    ),
    array(
        'id' => 'pms',
        'field' => 'pms',
        'name' => $activityColumn['pms']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DataValidator.pms'
    ),
    array(
        'id' => 'project',
        'field' => 'project',
        'name' => __('Project', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.HyperlinkCellFormatter'
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'linked_profit',
        'field' => 'linked_profit',
        'name' => $activityColumn['linked_profit']['name'],
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        //'editor' => 'Slick.Editors.selectBox'
        'editor' => 'Slick.Editors.selectBoxCustom' //MODIFY BY VINGUYEN 16/05/2014
    ),
    /*
    array(
        'id' => 'consumed_current_year',
        'field' => 'consumed_current_year',
        'name' => $activityColumn['consumed_current_year']['name'],
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.HyperlinkCellFormatterConsumed'
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'workload_current_year',
        'field' => 'workload_current_year',
        'name' => __('Workload Current Year', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        //'formatter' => 'Slick.Formatters.HyperlinkCellFormatterConsumed'
        //'editor' => 'Slick.Editors.selectBox'
    ),
    */
    array(
        'id' => 'code1',
        'field' => 'code1',
        'name' => $activityColumn['code1']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'code2',
        'field' => 'code2',
        'name' => $activityColumn['code2']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'code3',
        'field' => 'code3',
        'name' => $activityColumn['code3']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'start_date',
        'field' => 'start_date',
        'name' => $activityColumn['start_date']['name'],
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DataValidator.startDate'
    ),
    array(
        'id' => 'end_date',
        'field' => 'end_date',
        'name' => $activityColumn['end_date']['name'],
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DataValidator.endDate'
    ),
    //array(
//        'id' => 'consumed',
//        'field' => 'consumed',
//        'name' => $activityColumn['consumed']['name'],
//        'width' => 100,
//        'sortable' => true,
//        'resizable' => true,
//        'formatter' => 'Slick.Formatters.ActionConsumed'
//    ),
//    array(
//        'id' => 'workload',
//        'field' => 'workload',
//        'name' => $activityColumn['workload']['name'],
//        'width' => 100,
//        'sortable' => true,
//        'resizable' => true
//    ),
//    array(
//        'id' => 'overload',
//        'field' => 'overload',
//        'name' => $activityColumn['overload']['name'],
//        'width' => 100,
//        'sortable' => true,
//        'resizable' => true
//    ),
//    array(
//        'id' => 'completed',
//        'field' => 'completed',
//        'name' => $activityColumn['completed']['name'],
//        'width' => 100,
//        'sortable' => true,
//        'resizable' => true
//    ),
//    array(
//        'id' => 'remain',
//        'field' => 'remain',
//        'name' => $activityColumn['remain']['name'],
//        'width' => 100,
//        'sortable' => true,
//        'resizable' => true
//    ),
    array(
        'id' => 'import_code',
        'field' => 'import_code',
        'name' => $activityColumn['import_code']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'validator' => 'DataValidator.isUniqueImportCode',
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code4',
        'field' => 'code4',
        'name' => $activityColumn['code4']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code5',
        'field' => 'code5',
        'name' => $activityColumn['code5']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code6',
        'field' => 'code6',
        'name' => $activityColumn['code6']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code7',
        'field' => 'code7',
        'name' => $activityColumn['code7']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code8',
        'field' => 'code8',
        'name' => $activityColumn['code8']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code9',
        'field' => 'code9',
        'name' => $activityColumn['code9']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
     array(
        'id' => 'code10',
        'field' => 'code10',
        'name' => $activityColumn['code10']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'activated',
        'field' => 'activated',
        'name' => __('Activated', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    )
);
// if actif(open), when display = 1, show $col.
foreach($activityColumn as $key => $column){
    if($key == 'actif'){
        if($column['display'] == '1'){
            $col[] = array(
                'id' => 'actif',
                'field' => 'actif',
                'name' => __($column['name'], true),
                'width' => 120,
                'sortable' => true,
                'resizable' => true,
                'editor' => 'Slick.Editors.selectBox'
            );
        }
    }
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
        'ignoreExport' => true,
        'formatter' => 'Slick.Formatters.Action'
        );
// merge $columns, $col, $act
if(!empty($col)){
    $columns = array_merge($columns, $col, $act);
} else {
    $columns = array_merge($columns, $act);
}
$i = 1;
$dataView = array();
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
    $data['name'] = (string) $activity['Activity']['name'];
    $data['long_name'] = (string) $activity['Activity']['long_name'];
    $data['short_name'] = (string) $activity['Activity']['short_name'];
    $data['family_id'] = (string) $activity['Activity']['family_id'];
    $data['subfamily_id'] = (string) $activity['Activity']['subfamily_id'];
    $data['pms'] = $activity['Activity']['pms'] ? 'yes' : 'no';
    $data['project'] = (string) $activity['Activity']['project'];
    $data['budget_customer_id'] = (string) $activity['Activity']['budget_customer_id'];
    //$data['consumed_current_year'] = (string) !empty($dataOfCurrentYears[$activity['Activity']['id']]['consumed']) ? $dataOfCurrentYears[$activity['Activity']['id']]['consumed'] : '';
    //$data['workload_current_year'] = (string) !empty($dataOfCurrentYears[$activity['Activity']['id']]['workload']) ? $dataOfCurrentYears[$activity['Activity']['id']]['workload'] : '';

    $data['activated'] = ($activity['Activity']['activated'] == 2) ? 'arch' : ($activity['Activity']['activated'] ? 'yes' : 'no');
    $data['accessible_profit'] = (array) Set::classicExtract($activity['AccessibleProfit'], '{n}.profit_center_id');
    $data['linked_profit'] = (string) !empty($activity['LinkedProfit']) ? $activity['LinkedProfit']['profit_center_id'] : '';
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
    $data['code1'] = (string) $activity['Activity']['code1'];
    $data['code2'] = (string) $activity['Activity']['code2'];
    $data['code3'] = (string) $activity['Activity']['code3'];
    $data['code4'] = (string) $activity['Activity']['code4'];
    $data['code5'] = (string) $activity['Activity']['code5'];
    $data['code6'] = (string) $activity['Activity']['code6'];
    $data['code7'] = (string) $activity['Activity']['code7'];
    $data['code8'] = (string) $activity['Activity']['code8'];
    $data['code9'] = (string) $activity['Activity']['code9'];
    $data['code10'] = (string) $activity['Activity']['code10'];
    $data['import_code'] = (string) $activity['Activity']['import_code'];
    $data['consumed'] = isset($sumActivities[$data['id']]) ? $sumActivities[$data['id']] : 0;

    //if($activity['Activity']['pms'] == 0){
//        $data['workload'] = isset($dataFromActivityTasks[$data['id']]['workload']) ? $dataFromActivityTasks[$data['id']]['workload'] : 0;
//        $data['overload'] = isset($dataFromActivityTasks[$data['id']]['overload']) ? $dataFromActivityTasks[$data['id']]['overload'] : 0;
//        $data['completed'] = isset($dataFromActivityTasks[$data['id']]['completed']) ? $dataFromActivityTasks[$data['id']]['completed'].'%' : '0%';
//        $data['remain'] = isset($dataFromActivityTasks[$data['id']]['remain']) ? $dataFromActivityTasks[$data['id']]['remain'] : 0;
//    } else {
//        $data['workload'] = isset($dataFromProjectTasks[$data['id']]['workload']) ? $dataFromProjectTasks[$data['id']]['workload'] : 0;
//        $data['overload'] = isset($dataFromProjectTasks[$data['id']]['overload']) ? $dataFromProjectTasks[$data['id']]['overload'] : 0;
//        $data['completed'] = isset($dataFromProjectTasks[$data['id']]['completed']) ? $dataFromProjectTasks[$data['id']]['completed'].'%' : '0%';
//        $data['remain'] = isset($dataFromProjectTasks[$data['id']]['remain']) ? $dataFromProjectTasks[$data['id']]['remain'] : 0;
//    }
    $data['start_date'] = $activity['Activity']['start_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $activity['Activity']['start_date'])) : '';
    $data['end_date'] = $activity['Activity']['end_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $activity['Activity']['end_date'])) : '';
    $data['actif'] = $activity['Activity']['actif'] ? 'yes' : 'no';
    $data['action.'] = '';
    $dataView[] = $data;
}
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);

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
//heightTable = (heightTable < 500) ? 500 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
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
            var  urlDetail = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var  urlProject = <?php echo json_encode(urldecode($this->Html->link('%2$s', array('controller' => $defaultProjectScreen['controller'], 'action' => $defaultProjectScreen['action'], '%1$s')))); ?>;
            // For validate date
            var actionTemplate =  $('#action-template').html(),backup = {};
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };

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
                HyperlinkCellFormatterConsumed : function(row, cell, value, columnDef, dataContext) {
                    var customUrl = dataContext.id + '?start='+startCurrentYear+'&end='+endCurrentYear;
                    if(parseFloat(value) == 0){
                        value = '';
                    }
                    return Slick.Formatters.HTMLData(row, cell, $this.t(urlConsumedCurrentYear, value || '', customUrl), columnDef, dataContext);
                },
                GetProjectManager : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['project_manager_id'][val]);
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
                code4 : {defaulValue : ''},
                code5 : {defaulValue : ''},
                code6 : {defaulValue : ''},
                code7 : {defaulValue : ''},
                code8 : {defaulValue : ''},
                code9 : {defaulValue : ''},
                code10 : {defaulValue : ''},
                import_code : {defaulValue : ''},
                c44: {defaulValue : ''},
                backupPM: {defaulValue : ''},
                project: {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
            var exporter = new Slick.DataExporter('/activities/export_excel_index');
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
                        window.location = ('/activities/index/'+company_id+'?activities_activated=' +id);
                    }
                });
            });
        });
        function setupScroll(){
            $("#scrollTopAbsenceContent").width($(".grid-canvas-left:first").width()+50);
            $("#scrollTopAbsence").width($(".slick-viewport-left:first").width());
        }
        setTimeout(function(){
            setupScroll();
        }, 2500);
        $("#scrollTopAbsence").scroll(function () {
            $(".slick-viewport-left:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });
        $(".slick-viewport-left:first").scroll(function () {
            $("#scrollTopAbsence").scrollLeft($(".slick-viewport-left:first").scrollLeft());
        });
        history_reset = function(){
            setupScroll();
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
