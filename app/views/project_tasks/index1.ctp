<?php //echo $html->script('jquery.dataTables');                                                                                                                                                                                        ?>
<?php //echo $html->css('jquery.ui.custom');                                                                                                                                                                                        ?>

<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php //echo $html->css('slick_grid/smoothness/jquery-ui-1.8.16.custom'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables'); ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
</style>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .select-employees{
        margin: 3px;
    }
    .select-employees td{
        overflow: hidden;
        padding: 2px 5px;
    }
    .multiSelectOptions{

    }
    .select-employees .no-employee .input label{
        font-weight: bold !important;
    }
    .select-employees label{
        display: inline !important;
        width: auto !important;
    }
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_tasks', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
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
                    <h2 class="wd-t1"><?php echo sprintf(__("Project Task List of %s", true), $projectName['Project']['project_name']); ?></h2>					
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Vision Project') ?></span></a>
                    <a href="javascript:void(0);" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:400px;">
                
                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">
                    
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
        </div>
    </div>
</div>
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
        'id' => 'project_planed_phase_id',
        'field' => 'project_planed_phase_id',
        'name' => __('Phase', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'task_title',
        'field' => 'task_title',
        'name' => __('Task', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'task_priority_id',
        'field' => 'task_priority_id',
        'name' => __('Priority', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'task_status_id',
        'field' => 'task_status_id',
        'name' => __('Status', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'task_completed',
        'field' => 'task_completed',
        'name' => __('Completed', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors. ',
        'formatter' => 'Slick.Formatters.percentValue'
    ),
    array(
        'id' => 'employee_id',
        'field' => 'employee_id',
        'name' => __('Assign to', true),
        'width' => 280,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox',
        'formatter' => 'Slick.Formatters.selectBox'
    ),
    array(
        'id' => 'task_start_date',
        'field' => 'task_start_date',
        'name' => __('Start date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'task_end_date',
        'field' => 'task_end_date',
        'name' => __('End date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.endDate'
    ),
    /*array(
        'id' => 'task_real_end_date',
        'field' => 'task_real_end_date',
        'name' => __('Real end date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.endDate'
    ),*/
    array(
        'id' => 'estimated',
        'field' => 'estimated',
        'name' => __('Estimated workload', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValue'
    ),
    array(
        'id' => 'consumed',
        'field' => 'consumed',
        'name' => __('Consumed', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1, 
        'formatter' => 'Slick.Formatters.HyperlinkCellFormatter'       
        //'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'remain',
        'field' => 'remain',
        'name' => __('Remain', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1
        //'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array(
    'project_planed_phase_id' => $projectPhases,
    'task_priority_id' => $projectPriorities,
    'task_status_id' => $projectStatus,
    'employee_id' => $employees,
);
foreach ($projectTasks as $projectTask) {
    $data = array(
        'id' => $projectTask['ProjectTask']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['project_planed_phase_id'] = $projectTask['ProjectTask']['project_planed_phase_id'];
    $data['task_priority_id'] = $projectTask['ProjectTask']['task_priority_id'];
    $data['task_status_id'] = $projectTask['ProjectTask']['task_status_id'];
    $data['task_title'] = $projectTask['ProjectTask']['task_title'];
    
    $estimated = isset($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
    $consumed = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
    if($consumed != 0){
        $_cacu = round(($consumed*100)/$estimated, 2);
        if($_cacu > 100){
           $conpleted = 100; 
        } else {
            $conpleted = $_cacu;
        }
    }
    else {$conpleted = 0;}
    
    $data['task_completed'] = (string) (isset($projectTask['ProjectTask']['consumed']) || isset($projectTask['ProjectTask']['estimated']))? $conpleted : 0;
    $data['estimated'] = (string) $projectTask['ProjectTask']['estimated'];
    $data['consumed'] = (string) (isset($projectTask['ProjectTask']['consumed']))? $projectTask['ProjectTask']['consumed'] : 0;
    $_remain = $projectTask['ProjectTask']['estimated']-$projectTask['ProjectTask']['consumed'];
    if($_remain < 0){
        $remains = 0;
    } else {
        $remains = $_remain;
    }
    $data['remain'] = (string) (isset($projectTask['ProjectTask']['consumed']) || isset($projectTask['ProjectTask']['estimated']))? ($remains) : 0;
    
    if (isset($projectTask['ProjectTask']['ProjectTaskEmployeeRefer'])) {

        foreach ($projectTask['ProjectTask']['ProjectTaskEmployeeRefer'] as $key => $taskRefer) {
            $data['employee_id'][] = $taskRefer['employee_id'];
        }
    } else {
        $data['employee_id'] = array();
    }

    $data['task_start_date'] = $str_utility->convertToVNDate($projectTask['ProjectTask']['task_start_date']);
    $data['task_end_date'] = $str_utility->convertToVNDate($projectTask['ProjectTask']['task_end_date']);
    //$data['task_real_end_date'] = $str_utility->convertToVNDate($projectTask['ProjectTask']['task_real_end_date']);

    $data['action.'] = '';
    
    $dataView[] = $data;
}
$projectName['Project']['start_date'] = $str_utility->convertToVNDate($projectName['Project']['start_date']);
$projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['end_date']);
if ($projectName['Project']['end_date'] == "" || $projectName['Project']['end_date'] == '0000-00-00') {
    $projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['planed_end_date']);
}
foreach ($phaseInfo as &$phase) {
    $phase['phase_planed_start_date'] = $str_utility->convertToVNDate($phase['phase_planed_start_date']);
    $phase['phase_planed_end_date'] = $str_utility->convertToVNDate($phase['phase_planed_end_date']);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true),
    'Remain' => __('Rest à Faire', true)
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
    var DateValidate = {};
    
    (function($){
        
        $(function(){
            
            var $this = SlickGridCustom,
            phaseInfo =  <?php echo json_encode($phaseInfo); ?>;
            
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }
            var backup = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
        
            DateValidate.startDate = function(value){
                value = getTime(value);
                return {
                    valid : value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']),
                    message : $this.t('Phase start date must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date'])
                };
            }
            DateValidate.endDate = function(value,args){
                value = getTime(value);
                return {
                    valid : value >= getTime(args.item.task_start_date) && value <= getTime(projectName['end_date']),
                    message : $this.t('This date value must between %1$s and %2$s' ,args.item.task_start_date, projectName['end_date'])
                };
            }
            
            var  urlConsumed = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var actionTemplate =  $('#action-template').html();
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,$this.selectMaps['project_planed_phase_id'][dataContext.project_planed_phase_id]), columnDef, dataContext);
                },
                 HyperlinkCellFormatter : function(row, cell, value, columnDef, dataContext) { 
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlConsumed, value || '',dataContext.id), columnDef, dataContext);
                }
            });
            
            
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;

            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_planed_phase_id : {defaulValue : '' , allowEmpty : false},
                task_priority_id : {defaulValue : '' , allowEmpty : false},
                task_status_id : {defaulValue : '' , allowEmpty : false},
                //task_assign_to : {defaulValue : projectName['project_manager_id'] , allowEmpty : false},
                employee_id : {defaulValue : ''},
                task_title : {defaulValue : '' , allowEmpty : false},
                //task_completed : {defaulValue : 0},
                task_start_date : {defaulValue : '' , allowEmpty : false},
                task_end_date : {defaulValue : '' , allowEmpty : false, required : ['task_start_date']},
                //task_real_end_date : {defaulValue : '' , required : ['task_start_date']},
                estimated : {defaulValue : ''}
            };
            
            $this.onCellChange = function(args){
                if(args.item){
                    var phase = args.item.project_planed_phase_id;
                    if(phaseInfo[phase] && phaseInfo[phase].phase_planed_start_date  
                        && !args.item.task_start_date && DateValidate.startDate(phaseInfo[phase].phase_planed_start_date , args).valid ){
                        args.item.task_start_date =  phaseInfo[phase].phase_planed_start_date;
                    }
                    if(phaseInfo[phase] && args.item.task_start_date && phaseInfo[phase].phase_planed_end_date
                        && !args.item.task_end_date && DateValidate.endDate(phaseInfo[phase].phase_planed_end_date,args).valid ){
                        args.item.task_end_date =  phaseInfo[phase].phase_planed_end_date;
                    }
                    var estiMated = args.item.estimated ? args.item.estimated : 0;
                    var conSumed = args.item.consumed ? args.item.consumed : 0;
                    if(conSumed){args.item.task_completed = ((conSumed*100)/estiMated).toFixed(2);}
                    else {args.item.task_completed = 0;}
                    args.item.remain = estiMated-conSumed;
                    args.grid.updateRow(args.row);
                }
                return true;
            }
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns);

        });
    })(jQuery);
</script>