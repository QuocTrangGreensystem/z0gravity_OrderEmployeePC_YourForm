
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .ui-dialog {font-size: 11px}
    .ui-widget {
        font-family: Arial,sans-serif;
    }
    #dialog_import_CSV label{color:black}
    .buttons ul.type_buttons {padding-right: 10px !important}
    .type_buttons .error-message {
        background-color: #FFFFFF;
        clear: both;
        color: #D52424;
        display: block;
        padding: 5px 0 0 0px;
        width: 212px;
    }
    .form-error {
        border: 1px solid #D52424;
        color: #D52424;
    }
    /* New Filter */
    .wd-customs{
        border: solid 1px #c0c0c0;
        float: right;
        margin-top: 2px;
    }
</style>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;    
        }
    </style>
<![endif]-->

<?php //echo $html->script('jquery.dataTables');                                                                                                                                                                                   ?>
<?php //echo $html->css('jquery.ui.custom');                                                                                                                                                                                    ?>

<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid_v2'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common_v2'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables'); ?>

<script type="text/javascript">

    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .pagination-page{
        float: right;
        margin-top: 10px;
        margin-right: 25px;
    }
    .pagination-page span a{
        color: white !important;
    }
    .pagination-page span.current{
        background-color: white;
        padding: 2px 6px;
        color: black;
        font-weight: bold;
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
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Activity management", true); ?></h2>
                    <?php /* <a href="<?php echo $this->Html->url(array('action' => 'review', $companyName['Company']['id'])); ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export') ?></span></a> */ ?>
                    <a href="javascript:void(0)" class="wd-add-project" id="import_CSV" style="margin-right:5px; "><span><?php __('Import CSV') ?></span></a>
                    <a href="#" id="export-submit" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export') ?></span></a>
                    <a href="#" id="export-submitplus" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export +') ?></span></a>
                    <?php
                        echo $this->Form->create('Category');
                        if(!empty($activities_activated)){
                            $op = ($activities_activated == 1) ? 'selected="selected"' : '';
                            $in = ($activities_activated == 2) ? 'selected="selected"' : '';
                            $ar = ($activities_activated == 3) ? 'selected="selected"' : '';
                        }
                    ?>
                        <select style="margin-right:11px; width:13.5% !important; padding: 6px;" class="wd-customs" id="FilterStatusActivity">
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
                <div class="wd-table" id="project_container" style="width:100%">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">
                    <div class="pagination-page">
                    <!-- Shows the page numbers -->
                    <?php echo $this->Paginator->numbers(); ?>
                    <!-- Shows the next and previous links -->
                    <?php echo $this->Paginator->prev('« Previous', null, null, array('class' => 'disabled')); ?>
                    <?php echo $this->Paginator->next('Next »', null, null, array('class' => 'disabled')); ?>
                    <!-- prints X of Y, where X is current page and Y is number of pages -->
                    <span>|</span>
                    <?php echo $this->Paginator->counter(); ?>
                    </div>
                </div>
                <?php echo $this->element('grid_status'); ?>
            </div>
        </div>
    </div>
</div>
<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV file" class="buttons">
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
        'id' => 'accessible_profit',
        'field' => 'accessible_profit',
        'name' => $activityColumn['accessible_profit']['name'],
        'width' => 280,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox',
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
        'editor' => 'Slick.Editors.selectBox'
    ),
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
    array(
        'id' => 'consumed',
        'field' => 'consumed',
        'name' => $activityColumn['consumed']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.ActionConsumed'
    ),
    array(
        'id' => 'workload',
        'field' => 'workload',
        'name' => $activityColumn['workload']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'overload',
        'field' => 'overload',
        'name' => $activityColumn['overload']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'completed',
        'field' => 'completed',
        'name' => $activityColumn['completed']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'remain',
        'field' => 'remain',
        'name' => $activityColumn['remain']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true
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
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'linked_profit' => $profitCenters,
    'accessible_profit' => $profitCenters,
    'project' => $projects
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($activities as $activity) {
    $data = array(
        'id' => $activity['Activity']['id'],
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $activity['Activity']['name'];
    $data['long_name'] = (string) $activity['Activity']['long_name'];
    $data['short_name'] = (string) $activity['Activity']['short_name'];
    $data['family_id'] = (string) $activity['Activity']['family_id'];
    $data['subfamily_id'] = (string) $activity['Activity']['subfamily_id'];
    $data['pms'] = $activity['Activity']['pms'] ? 'yes' : 'no';
    $data['project'] = (string) $activity['Activity']['project'];
    
    $data['activated'] = $activity['Activity']['activated'] ? 'yes' : 'no';
    $data['accessible_profit'] = (array) Set::classicExtract($activity['AccessibleProfit'], '{n}.profit_center_id');
    $data['linked_profit'] = (string) Set::classicExtract($activity['LinkedProfit'], '0.profit_center_id');

    $data['code1'] = (string) $activity['Activity']['code1'];
    $data['code2'] = (string) $activity['Activity']['code2'];
    $data['code3'] = (string) $activity['Activity']['code3'];
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
    var DataValidator = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom,families = <?php echo json_encode($mapFamilies); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            var  urlDetail = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var  urlProject = <?php echo json_encode(urldecode($this->Html->link('%2$s', array('controller' => 'projects', 'action' => 'edit', '%1$s')))); ?>;
            // For validate date
            var actionTemplate =  $('#action-template').html(),backup = {};
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
                }
            });
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
                accessible_profit : {defaulValue : ''},
                linked_profit : {defaulValue : ''},
                start_date : {defaulValue : ''},
                end_date : {defaulValue : ''},
                pms : {defaulValue : ''},
                actif : {defaulValue : ''},
                activated : {defaulValue : ''},
                code1 : {defaulValue : ''},
                code2 : {defaulValue : ''},
                code3 : {defaulValue : ''},
                c44: {defaulValue : ''},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
            var Columns = ControlGrid.getColumns();
            var maxWidth = ControlGrid.getDataItem(1).name.length;
            var i,j;
            for (i =1 ; i < ControlGrid.getDataLength() ; i++){
               var k = ControlGrid.getDataItem(i).name.length;
               if(k > maxWidth)
                    maxWidth = k;
            }
            if (Columns && columns.length > 0) {
                Columns[1].width = maxWidth * 8;
                ControlGrid.applyColumnHeaderWidths();
                ControlGrid.applyColumnWidths();
            }
            $('#dialog_import_CSV').dialog({
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
             
            $(".cancel").live('click',function(){
                $("#dialog_data_CSV").dialog("close");
                $("#dialog_import_CSV").dialog("close");
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
        
    })(jQuery);
</script>