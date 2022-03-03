<?php
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity',
        'jquery.multiSelect',
        'projects',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit'
    ));
    echo $this->Html->script(array(
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid_custom',
        'history_filter',
        'jquery.multiSelect',
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drop-2.0.min',
        'slick_grid/plugins/slick.rowselectionmodel',
        'slick_grid/plugins/slick.rowmovemanager',
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity',
        'jquery.ui.touch-punch.min'
    ));
    echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .start-date-conflict{
        color: #0783EF !important;
    }

    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    p {
        margin-bottom: 10px;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
    .slick-viewport-right{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_phase_plans', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <?php
                        if($projectName['Project']['category'] == 2){
                            if($employee_info['Role']['name'] == 'admin' || $employee_info['Role']['name'] == 'pm'){
                    ?>
                    <a href="<?php echo $html->url("/project_phase_plans/time_reset/" . $projectName['Project']['id']) ?>" title="<?php __('reset real to plan') ?>"><img src="/img/time-reset-1.png" /></a>
                    <a href="<?php echo $html->url("/project_phase_plans/time_reset_plan_real/" . $projectName['Project']['id']) ?>" title="<?php __('reset plan to real') ?>"><img src="/img/time-reset-2.png" /></a>
                    <?php
                            }
                        }
                    ?>
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"></a>
                    <a href="javascript:;" class="export-excel-icon-all" style="margin-right:5px;" id="export-button" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    <?php if(($canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) : ?>
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px;" onclick="addNewPhaseButton();" title="<?php __('Add an item') ?>"></a>
                    <?php endif; ?>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                    <?php if($employee_info['Role']['name'] == 'admin'): ?>
                    <a target="_blank" href="<?php echo $this->Html->url('/project_phases/index/ajax') ?>" class="button-setting"></a>
                    <?php endif; ?>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                    <div id="flashMessagePleaseWait" class="message success" style="display: none;">
                        Please wait ...
                    </div>
                    <div id="flashMessageSaveSuccess" class="message success" style="display: none;">
                        SAVED!
                    </div>
                </div>
                <p><?php __('In the column #, drag and drop the phase to reorder') ?></p>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%;">
                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden;">
                </div>

            </div>
            <?php echo $this->element('grid_status'); ?>
        </div></div>
        </div>
    </div>
</div>
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
//unhidable
$_columns[] = array(
    'id' => 'no.',
    'field' => 'no.',
    'name' => '#',
    'width' => 40,
    'sortable' => false,
    'resizable' => false,
    'noFilter' => 1,
    'behavior' => 'selectAndMove',
    'cssClass' => 'slick-cell-move-handler'
);
if($displayParst){
    $_columns[] = array(
        'id' => 'project_part_id',
        'field' => 'project_part_id',
        'name' => __('Part', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DateValidate.partValue'
    );
}
$columns = array(
    array(
        'id' => 'project_planed_phase_id',
        'field' => 'project_planed_phase_id',
        'name' => __('Phase', true),
        'width' => 140,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBoxCustom',
        'validator' => 'DateValidate.planedPhase'
    ),
    array(
        'id' => 'phase_planed_start_date',
        'field' => 'phase_planed_start_date',
        'name' => __('Plan start date', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.startDate',
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
    array(
        'id' => 'phase_planed_end_date',
        'field' => 'phase_planed_end_date',
        'name' => __('Plan end date', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.endDate',
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    )
);
$columns = array_merge($_columns, $columns);
$optionalColumns = array(
    'kpi' => array(
        'id' => 'kpi',
        'field' => 'kpi',
        'name' => __('KPI', true),
        'width' => 80,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.Color',
        'noFilter' => 1,
    ),
    'planed_duration' => array(
        'id' => 'planed_duration',
        'field' => 'planed_duration',
        'name' => __('Duration', true),
        'width' => 90,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValue',
        'validator' => 'DateValidate.durationValue',
        'formatter' => 'Slick.Formatters.numberValue'
    ),
    'predecessor' => array(
        'id' => 'predecessor',
        'field' => 'predecessor',
        'name' => __('Predecessor', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox2',
        'validator' => 'DateValidate.predecessor'
    ),
    'phase_real_start_date' => array(
        'id' => 'phase_real_start_date',
        'field' => 'phase_real_start_date',
        'name' => __('Real start date', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.rstartDate'
    ),
    'phase_real_end_date' => array(
        'id' => 'phase_real_end_date',
        'field' => 'phase_real_end_date',
        'name' => __('Real end date', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.rendDate'
    ),
    'project_phase_status_id' => array(
        'id' => 'project_phase_status_id',
        'field' => 'project_phase_status_id',
        'name' => __('Status', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    'color' => array(
        'id' => 'color',
        'field' => 'color',
        'name' => __('Color', true),
        'width' => 60,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.Color',
        'noFilter' => 1,
    ),
    'ref1' => array(
        'id' => 'ref1',
        'field' => 'ref1',
        'name' => __('Ref 1', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.Text',
        //'noFilter' => 1,
    ),
    'ref2' => array(
        'id' => 'ref2',
        'field' => 'ref2',
        'name' => __('Ref 2', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.Text',
        //'noFilter' => 1,
    ),
    'ref3' => array(
        'id' => 'ref3',
        'field' => 'ref3',
        'name' => __('Ref 3', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.Text',
        //'noFilter' => 1,
    ),
    'ref4' => array(
        'id' => 'ref4',
        'field' => 'ref4',
        'name' => __('Ref 4', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.Text',
        //'noFilter' => 1,
    ),
    'profile_id' => array(
        'id' => 'profile_id',
        'field' => 'profile_id',
        'name' => __('Profile', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    'progress' => array(
        'id' => 'progress',
        'field' => 'progress',
        'name' => __('% Achieved', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.percentValues'
    )
);

$settings = $this->requestAction('/project_phases/getFields');
foreach($settings as $setting){
    list($key, $show) = explode('|', $setting);
    if( $show == 0 )continue;
    if( $key == 'progress' && !$manuallyAchievement )continue;
    if( $key == 'profile_id' && !$activateProfile )continue;
    if( isset($optionalColumns[$key]) )$columns[] = $optionalColumns[$key];
}

$columns[] = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => false,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}
$i = 1;
$dataView = array();
$predecessors = array();
$Model = ClassRegistry::getObject('ProjectPhasePlan');
foreach ($projectPhasePlans as $projectPhasePlan) {
    $data = array(
        'id' => $projectPhasePlan['ProjectPhasePlan']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['project_planed_phase_id'] = $projectPhasePlan['ProjectPhasePlan']['project_planed_phase_id'];
    $data['project_phase_status_id'] = $projectPhasePlan['ProjectPhasePlan']['project_phase_status_id'];
    $data['project_part_id'] = $projectPhasePlan['ProjectPhasePlan']['project_part_id'];
    $data['planed_duration'] = $projectPhasePlan['ProjectPhasePlan']['planed_duration'];
    $data['predecessor'] = $projectPhasePlan['ProjectPhasePlan']['predecessor'];
    $data['weight'] = $projectPhasePlan['ProjectPhasePlan']['weight'];
    $data['ref1'] = $projectPhasePlan['ProjectPhasePlan']['ref1'];
    $data['ref2'] = $projectPhasePlan['ProjectPhasePlan']['ref2'];
    $data['ref3'] = $projectPhasePlan['ProjectPhasePlan']['ref3'];
    $data['ref4'] = $projectPhasePlan['ProjectPhasePlan']['ref4'];
    $data['profile_id'] = $projectPhasePlan['ProjectPhasePlan']['profile_id'];
    $data['progress'] = $projectPhasePlan['ProjectPhasePlan']['progress'];

    $data['phase_planed_start_date'] = $str_utility->convertToVNDate($projectPhasePlan['ProjectPhasePlan']['phase_planed_start_date']);
    $data['phase_planed_end_date'] = $str_utility->convertToVNDate($projectPhasePlan['ProjectPhasePlan']['phase_planed_end_date']);
    //$data['phase_real_start_date'] = $str_utility->convertToVNDate($projectPhasePlan['ProjectPhasePlan']['phase_planed_start_date']);
    //$data['phase_real_end_date'] = $str_utility->convertToVNDate($projectPhasePlan['ProjectPhasePlan']['phase_planed_end_date']);
    $data['phase_real_start_date'] = $str_utility->convertToVNDate($projectPhasePlan['ProjectPhasePlan']['phase_real_start_date']);
    $data['phase_real_end_date'] = $str_utility->convertToVNDate($projectPhasePlan['ProjectPhasePlan']['phase_real_end_date']);
    $data['color'] = (!empty($projectPhasePlan["ProjectPhase"]["color"])) ? $projectPhasePlan["ProjectPhase"]["color"] : '#004380';
    $data['action.'] = '';

    if (isset($projectPhases1[$data['project_planed_phase_id']])) {
        $predecessors[$data['id']] = $projectPhases1[$data['project_planed_phase_id']]
                . (isset($projectParts[$data['project_part_id']]) ? ' (' . $projectParts[$data['project_part_id']] . ')' : '');
    }
    $data['kpi'] = $projectPhasePlan['ProjectPhasePlan']['phase_planed_end_date'] < $projectPhasePlan['ProjectPhasePlan']['phase_real_end_date'] ? '#F00' : '#0F0';
    $dataView[] = $data;
}
asort($predecessors);
$selectMaps = array(
    'predecessor' => $predecessors,
    'project_planed_phase_id' => $projectPhases1,
    'project_part_id' => $projectParts,
    'project_phase_status_id' => $projectPhaseStatuses,
    'profile_id' => $profiles
);

$projectName['Project']['start_date'] = $str_utility->convertToVNDate($projectName['Project']['start_date']);
$projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['end_date']);
if ($projectName['Project']['end_date'] == "" || $projectName['Project']['end_date'] == '0000-00-00') {
    $projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['planed_end_date']);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true),
    'Update the dates of the phases linked to this phase?' => __('Update the dates of the phases linked to this phase?', true)
);

$i=0;
$projectPhasesTemp = array();
foreach($projectPhases as $key => $value){
    $projectPhasesTemp[$i]=array('key'=>$key,'value'=>$value);
    $i++;
}
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%4$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s', '%3$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
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
    var DateValidate = {};

    (function($){

        $(function(){
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
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var countTaskOfPhases = <?php echo json_encode($countTaskOfPhases); ?>;
            var phaseDefaults = <?php echo json_encode($phaseDefaults);?>;
            var currentPhase = <?php echo json_encode($currentPhase) ?>;
            var phases = <?php echo json_encode($projectPhases) ?>;
            var $this = SlickGridCustom;
            var listPreAfterSave = [];
            $.extend($this,{
                saveData : {},
                onApplyValue : function(item, args){
                    var weight = 0;
                    var items = args.grid.getData().getItems();
                    $.each(items, function(i, v){
                        if( v.weight && v.weight > weight ){
                            weight = v.weight;
                        }
                    });
                    $.extend(item , {
                        weight : weight + 1
                    });
                },
                canModified : <?php echo json_encode((!empty($canModified) && !$_isProfile )|| ($_isProfile && $_canWrite)); ?>,
                i18n : <?php echo json_encode($i18n); ?>,
                // weekday name
                weekday : ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                // days off during the week, sunday is zero
                week : {
                    0 : true,1 : false, 2: false, 3: false, 4: false, 5: false, 6: true
                },
                months : {},
                parsePredecessor : function(args, callback){
                    var data = args.grid.getData();
                    var count = data.getLength();
                    for(var i = 0; i < count;i++){
                        if(callback.call(data,i, data.getItem(i)) === false){
                            break;
                        }
                    }
                },
                updateData : function(args,row){
                    if(!$.isEmptyObject($this.saveData)){
                        $.extend(args.item, $this.saveData);
                        args.grid.updateRow(row);
                        $this.saveData = {};
                    }
                },
                // onBeforeEdit: function(args){
                // 	if( args.item && typeof phases[args.item.project_planed_phase_id] == 'undefined' ){
                // 		alert(<?php echo json_encode(__('This phase is deactivated', true)) ?>);
                // 		return false;
                // 	}
                // 	return true;
                // },
                onCellChange: function(args){
                    if(args.item.phase_planed_start_date){
                        if(!args.item.phase_planed_end_date){
                            // xu ly plan end date + 1, real end date + 1
                            var planStartDate = args.item.phase_planed_start_date;
                            planStartDate = planStartDate.split('-');
                            var newDay = parseInt(planStartDate[0]) + 1,
                                newMonth = parseInt(planStartDate[1]) - 1;
                            var PlanEndDate = new Date(planStartDate[2], newMonth, newDay);
                            var _day = PlanEndDate.getDate() < 10 ? '0' + PlanEndDate.getDate() : PlanEndDate.getDate();
                            var _month = (PlanEndDate.getMonth()+1 < 10) ? '0' + (PlanEndDate.getMonth()+1) : PlanEndDate.getMonth()+1;
                            PlanEndDate = _day + '-' + _month + '-' + PlanEndDate.getFullYear();
                            args.item.phase_planed_end_date = PlanEndDate;
                        }
                    } else {
                        if(args.item.phase_planed_end_date){
                            // xu ly plan end date - 1, real end date - 1
                            var planEndDate = args.item.phase_planed_end_date;
                            planEndDate = planEndDate.split('-');
                            var newDay = parseInt(planEndDate[0]) - 1,
                                newMonth = parseInt(planEndDate[1]) - 1;
                            var PlanStartDate = new Date(planEndDate[2], newMonth, newDay);
                            var _day = PlanStartDate.getDate() < 10 ? '0' + PlanStartDate.getDate() : PlanStartDate.getDate();
                            var _month = (PlanStartDate.getMonth()+1 < 10) ? '0' + (PlanStartDate.getMonth()+1) : PlanStartDate.getMonth()+1;
                            PlanStartDate = _day + '-' + _month + '-' + PlanStartDate.getFullYear();
                            args.item.phase_planed_start_date = PlanStartDate;
                        }
                    }
                    $this.updateData(args , args.row);
                    //26-10-2013 huythang38
                    if (args.item.phase_real_start_date == '' || (!countTaskOfPhases[args.item.id] || countTaskOfPhases[args.item.id] == 0)) {
                        args.item.phase_real_start_date = args.item.phase_planed_start_date;
                    };
                    if (args.item.phase_real_end_date == '' || (!countTaskOfPhases[args.item.id] || countTaskOfPhases[args.item.id] == 0)) {
                        args.item.phase_real_end_date = args.item.phase_planed_end_date;
                    };
                    if ($this.getTime(args.item.phase_planed_end_date) < $this.getTime(args.item.phase_real_end_date)) {
                        args.item.kpi = '#F00';
                    }
                    else{
                        args.item.kpi = '#0F0';
                    };
                    if(args && args.item && args.item.profile_id == ''){
                        var phaseId = args.item.project_planed_phase_id ? args.item.project_planed_phase_id : 0;
                        args.item.profile_id = phaseDefaults[phaseId] ? phaseDefaults[phaseId] : '';
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
                },
                onAfterSave : function(result,args){
                    if(!result){
                        return;
                    }
                    if(args.item.id){
                        var lot = $this.selectMaps.project_part_id[args.item.project_part_id];
                        $this.selectMaps.predecessor[args.item.id] =
                            $this.selectMaps.project_planed_phase_id[args.item.project_planed_phase_id]
                            + (lot ? (' ('+lot+')') : '');
                    }
                    switch(args.column.field){
                        case 'project_planed_phase_id':{
                                $this.parsePredecessor(args, function(i,data){
                                    if(args.item.id == data.predecessor){
                                        args.grid.updateRow(this.getRowById(data.id));
                                    }
                                });
                                break;
                            }
                    }
                    if(listPreAfterSave.length != 0){
                        $.each(args.grid.getData().getItems(), function(i, data){
                            $.each(listPreAfterSave, function(ix, value){
                                if(data.id == ix){
                                    data.predecessor = value;
                                    args.grid.updateRow(args.grid.getData().getRowById(data.id));
                                }
                            });
                        });
                    }
                },
                getDay : function(day,month,year){
                    return new Date(year, month -1, day).getDay();
                },
                getDayOfMonth : function(month,year){
                    return new Date(year, month, 0).getDate();
                },
                parseDuration : function(start , value , callback , increase){
                    var time = '',
                        off = '',
                        s = start.split("-"),
                        y = parseInt(s[2] ,10),
                        m = parseInt(s[1] ,10),
                        d = parseInt(s[0] ,10);
                    while(value > 0){
                        off = $this.week[$this.getDay(d,m,y)] || ($this.months[m] && $.inArray(d,$this.months[m]) != -1);
                        value += (callback(off) || 0);
                        time = [d,m,y];
                        if(increase){
                            d++;
                            if(d>$this.getDayOfMonth(m,y)){
                                m++;
                                d =1;
                            }
                            if(m > 12){
                                m = 1;
                                y++;
                            }
                        }else{
                            d--;
                            if(d < 1){
                                m--;
                                if(m < 1){
                                    m = 12;
                                    y--;
                                }
                                d = $this.getDayOfMonth(m,y);
                            }
                        }
                        value--;
                    }
                    return time ? $.datepicker.formatDate( $this.dateFormat, new Date(time[2], time[1] -1, time[0]) ) : '';
                },
                getDuration : function(start , end){
                    var duration = 0, increase = true, max = $this.toDay($this.getTime(end) - $this.getTime(start));
                    if(max < 0){
                        max *= -1;
                        increase = false;
                    }
                    $this.parseDuration(start, max + 1 , function(offDay){
                        if(!offDay){
                            duration++;
                        }
                    } , increase);
                    return increase ? duration : - duration;
                },
                setDuration : function(start , value){
                    var increase = true;
                    value = parseInt(value ,10);
                    if(value < 0){
                        value *= -1;
                        increase = false;
                    }
                    return $this.parseDuration(start, value , function(offDay){
                        return offDay ? 1 : 0;
                    } , increase);
                }
            });
            var parsePredecessor = function(args , result){
                if(args.item.predecessor && args.item.predecessor != 'null'){
                    var data = args.grid.getData().getItemById(args.item.predecessor);
                    if(!data || $this.getTime(args.item.phase_planed_start_date) <= $this.getTime(data.phase_planed_end_date)){
                        $.extend(result,{
                            valid : false,
                            message : $this.t('The data entered have conflict with linked phases. If you want to unlink it, <a href="#">click here</a>.'),
                            callback : function(){
                                var $link = this.tip.tooltip('widget').find('a');
                                $link.unbind().bind('click' , function(){
                                    args.item.predecessor = '';
                                    args.grid.eval('getEditorLock().commitCurrentEdit();');
                                    return false;
                                });
                            }
                        });
                        return false;
                    }
                }
                return true;
            };
            var saveChangedData = function(args , data, isCheck){
                $.ajax({
                    url : '<?php echo $html->url(array('action' => 'sync', $projectName['Project']['id'])); ?>' + '/' + isCheck,
                    cache : false,
                    type : 'POST',
                    data : {
                        data : data
                    },
                    dataType : 'json',
                    success : function(data){
                        if(data.end_date){
                            projectName.end_date = data.end_date;
                        }
                    }
                });
                args.grid.eval('getEditorLock().commitCurrentEdit();');
            }
            /**
             * Ham de quy cac Phase
             */
            var recuPhases = function(Phases, item, result){
                var listPres = [];
                var _Phases = [];
                var _Items = [];
                if(item.length != 0){
                    $.each(Phases, function(i, data){
                        $.each(item, function(ix, value){
                            if(data.predecessor && data.predecessor != null && data.predecessor == value.id){
                                var _datas = {};
                                var plan_start = $this.setDuration(value.phase_planed_end_date , 2);
                                var plan_end = $this.setDuration(plan_start, data.planed_duration);
                                if(countTaskOfPhases[data.id] == 0 || countTaskOfPhases[data.id] == null || data.phase_real_start_date == null || data.phase_real_end_date == null){
                                     result[data.id] = {
                                        id: data.id,
                                        phase_planed_start_date: plan_start,
                                        phase_planed_end_date: plan_end,
                                        phase_real_start_date: plan_start,
                                        phase_real_end_date: plan_end,
                                        predecessor: data.predecessor
                                    };
                                    Phases[i].phase_planed_start_date = plan_start;
                                    Phases[i].phase_planed_end_date = plan_end;
                                    Phases[i].phase_real_start_date = plan_start;
                                    Phases[i].phase_real_end_date = plan_end;
                                } else {
                                    result[data.id] = {
                                        id: data.id,
                                        phase_planed_start_date: plan_start,
                                        phase_planed_end_date: plan_end,
                                        predecessor: data.predecessor
                                    };
                                    Phases[i].phase_planed_start_date = plan_start;
                                    Phases[i].phase_planed_end_date = plan_end;
                                }
                                _Items.push(data);
                            }
                        });
                    });
                    recuPhases(Phases, _Items, result);
                }
                return result;
            }

            var parseSuccessor = function(args, item , result , trigger){
                if(!args.item.id){
                    return;
                }
                var phase = [] ,_item = $.extend({} , args.item);
                var duration = $this.getDuration(item.phase_planed_end_date,args.item.phase_planed_end_date);
                if(duration == 1 || duration == 0){
                    return;
                }
                var isCheck = false;
                $.each(args.grid.getData().getItems(), function(i, data){
                    if(_item.id && _item.id == data.predecessor){
                        isCheck = true;
                    }
                });
                var rows = args.grid.getData().getRowById(item.id);
                if(isCheck == true){
                    $.extend(result , {
                        valid : false,
                        message : $this.t('Update the dates of the phases linked to this phase?')
                            + ' <br /><a href="#"><?php __('No') ?></a>'
                            +'&nbsp;&nbsp;&nbsp;<a href="#"><?php __('Yes') ?></a>',
                        callback : function(){
                            var $link = this.tip.tooltip('widget').find('a');
                            $link.first().unbind().bind('click' , function(){
                                phase = [];
                                $.each(args.grid.getData().getItems(), function(i, data){
                                    if(item.id && item.id == data.predecessor){
                                        data.predecessor = '';
                                        args.grid.updateRow(args.grid.getData().getRowById(data.id));
                                        phase.push({id: data.id , predecessor : ''});
                                    }
                                });
                                saveChangedData(args , phase, 'no');
                            });
                            $link.last().unbind().bind('click' , function(){
                                phase = [];
                                var iItem = [];
                                iItem.push(_item);
                                var listPres = recuPhases(args.grid.getData().getItems(), iItem, []);
                                $.each(args.grid.getData().getItems(), function(i, data){
                                    if(listPres[data.id]){
                                        data.phase_planed_start_date = listPres[data.id].phase_planed_start_date ? listPres[data.id].phase_planed_start_date : '';
                                        data.phase_planed_end_date = listPres[data.id].phase_planed_end_date ? listPres[data.id].phase_planed_end_date : '';
                                        if((listPres[data.id].phase_real_start_date && listPres[data.id].phase_real_start_date == null) || countTaskOfPhases[data.id] == 0 || countTaskOfPhases[data.id] == null){
                                            data.phase_real_start_date = listPres[data.id].phase_real_start_date ? listPres[data.id].phase_real_start_date : '';
                                        }
                                        if((listPres[data.id].phase_real_end_date && listPres[data.id].phase_real_end_date == null) || countTaskOfPhases[data.id] == 0 || countTaskOfPhases[data.id] == null){
                                            data.phase_real_end_date = listPres[data.id].phase_real_end_date ? listPres[data.id].phase_real_end_date : '';
                                        }
                                        args.grid.updateRow(args.grid.getData().getRowById(data.id));
                                        if(listPres[data.id].predecessor){
                                            listPreAfterSave[data.id] = listPres[data.id].predecessor;
                                        }
                                        data.predecessor = '';
                                        phase.push({
                                            id: data.id,
                                            phase_planed_start_date: data.phase_planed_start_date,
                                            phase_planed_end_date: data.phase_planed_end_date,
                                            phase_real_start_date: data.phase_real_start_date,
                                            phase_real_end_date: data.phase_real_end_date
                                        });
                                    }
                                });
                                saveChangedData(args , phase, 'yes');
                            });
                        }
                    });
                }
            };

            var checkPredecessor = function(args,x,dt){
                var result = true;
                $this.parsePredecessor(args , function(i, data){
                    if(data.predecessor == x){
                        if( data.id == dt){
                            result = false;
                        }else{
                            result = checkPredecessor(args , data.id , dt);
                        }
                        return result;
                    }
                });
                return result;
            };

            DateValidate.durationValue = function(value,args){
                var _value = parseInt(value, 10);
               // max = $this.getDuration(args.item.phase_planed_start_date||projectName['start_date'],projectName['end_date']);
                var result = {
                    //valid : /^([1-9]|[1-9][0-9]*)$/.test(value) && _value > 0 && _value <= max,
                    //message : $this.t('Duration must between %1$s and %2$s days.' , 1 , max)
                    valid : /^([1-9]|[1-9][0-9]*)$/.test(value) && _value > 0,
                    message : $this.t('Duration must larger %1$s' , 0)
                };

                var item = $.extend({},args.item);

                if(result.valid){
                    args.item.planed_duration = value;
                    if(args.item.phase_planed_start_date){
                        value = $this.setDuration(args.item.phase_planed_start_date,_value);
                        if((result = DateValidate.endDate(value, args , 'only')).valid){
                            args.item.phase_planed_end_date = value;
                        }
                    }else if(args.item.phase_planed_end_date){
                        value = $this.setDuration(args.item.phase_planed_end_date,-_value);
                        if((result = DateValidate.startDate(value, args, 'only')).valid){
                            args.item.phase_planed_start_date = value;
                        }
                    }
                }

                if(!result.valid){
                    $.extend(args.item , item);
                }

                return result;
            };

            DateValidate.predecessor = function(value,args){
                var result = {
                    valid :true,
                    message : $this.t('The data entered have conflict with this project, You can not link it.')
                }, data = args.grid.getData().getItemById(value);
                if(data){

                    if(args.item.id && value
                        && !checkPredecessor(args,args.item.id , value)){
                        return {
                            valid :false,
                            message : $this.t('You are trying to link a phase to another phase that has a series of links back the first phase.')
                        };
                    }

                    this.saveData = {};
                    $this.saveData.phase_planed_start_date = $this.setDuration(data.phase_planed_end_date,2);
                    if(args.item.planed_duration){
                        $this.saveData.phase_planed_end_date = $this.setDuration($this.saveData.phase_planed_start_date,args.item.planed_duration);
                    }else if(args.item.phase_planed_end_date){
                        $this.saveData.planed_duration = $this.getDuration($this.saveData.phase_planed_start_date,args.item.phase_planed_end_date);
                        if($this.saveData.planed_duration <= 0){
                            $this.saveData.planed_duration = '';
                        }
                    }
                    var item = $.extend({},args.item);
                    $.extend(args.item, $this.saveData);
                    if( (args.item.phase_planed_start_date && !(result = DateValidate.startDate(args.item.phase_planed_start_date,args , 'only')).valid) ||
                        ( args.item.phase_planed_end_date && !(result = DateValidate.endDate(args.item.phase_planed_end_date,args , 'only')).valid)){
                        this.saveData = {};
                    }
                }else{
                    result.valid = false;
                }
                if(!result.valid){
                    $.extend(args.item , item);
                }
                return result;
            };

            DateValidate.startDate = function(value,args , trigger){
                var _value = $this.getTime(value),end ='';
                if(!args.item.planed_duration && args.item.phase_planed_end_date){
                    end = args.item.phase_planed_end_date;
                }
                if(!args.item.phase_planed_end_date){
                    $_valid = true;
                } else {
                    $_valid = _value <= $this.getTime(args.item.phase_planed_end_date);
                }
                //$_valid = true;
                var result = {
                    valid : $_valid,
                    //message: $this.t('')
                    message : $this.t('Phase plan start date value must smaller %1$s', args.item.phase_planed_end_date)
                };
                if(trigger === true){
                    return result;
                }
                var item = $.extend({},args.item);
                args.item.phase_planed_start_date = value;
                if(result.valid){
                    if(trigger != 'only'){
                       if(typeof args.item.planed_duration == 'object'){
                           args.item.planed_duration = JSON.stringify(args.item.planed_duration);
                       }
                       if(args.item.planed_duration == null || !args.item.planed_duration || args.item.planed_duration == 'null'){
                         args.item.planed_duration = 0;
                       }
                        if(parseInt(args.item.planed_duration) != 0){
                            //value = $this.setDuration(args.item.phase_planed_start_date,_value);
                            value = $this.setDuration(value,args.item.planed_duration);
                            if((result = DateValidate.endDate(value, args , true)).valid){
                                args.item.phase_planed_end_date = value;
                            }
                            if(args.item.phase_planed_end_date){
                                //args.item.planed_duration = $this.getDuration(value, args.item.phase_planed_end_date);
                            }
                        }else if(args.item.phase_planed_end_date){
                            args.item.planed_duration = $this.getDuration(value,args.item.phase_planed_end_date);
                        }
                    }
                }
                if(result.valid && parsePredecessor(args, result)){
                    parseSuccessor(args , item,  result , trigger);
                }
                if(!result.valid){
                    $.extend(args.item , item);
                }
                return result;
            };
            DateValidate.endDate = function(value,args , trigger){
                var _value = $this.getTime(value),start = '';
                if(!args.item.planed_duration && args.item.phase_planed_start_date){
                    start = args.item.phase_planed_start_date;
                }
                if(!args.item.phase_planed_start_date){
                    $_valid = true;
                } else {
                    $_valid = _value >= $this.getTime(args.item.phase_planed_start_date);
                }
                var result = {
                    //valid : _value >= $this.getTime(start||projectName['start_date']) && _value <= $this.getTime(projectName['end_date']),
                   // message : $this.t('Phase plan end date value must between %1$s and %2$s' ,start||projectName['start_date'], projectName['end_date'])
                    valid : $_valid,
                    message : $this.t('Phase plan end date value must larger %1$s', args.item.phase_planed_start_date)
                };
                if(trigger === true){
                    return result;
                }
                var item = $.extend({},args.item);
                args.item.phase_planed_end_date = value;
                if(result.valid){
                    // var dx = $this.setDuration(value , 1);
                    // if(dx!=value){
                    // 	// console.log('co vao day ko');
                    // 	result = {
                    // 		valid : false,
                    // 		message : $this.t('The date %s is off day. Please choose a another date. Suggested : %s' ,value, dx)
                    // 	}
                    // }else
                    if(trigger != 'only'){
                        if(args.item.planed_duration){
                            //value = $this.setDuration(value,-args.item.planed_duration);
//                            if((result = DateValidate.startDate(value, args , true)).valid){
//                                args.item.phase_planed_start_date = value;
//                            }
                            if(args.item.phase_planed_start_date){
                                args.item.planed_duration = $this.getDuration(args.item.phase_planed_start_date,value);
                            }
                        }else if(args.item.phase_planed_start_date){
                            args.item.planed_duration = $this.getDuration(args.item.phase_planed_start_date,value);
                        }
                    }
                }
                if(result.valid && parsePredecessor(args, result)){
                    parseSuccessor(args , item,  result , trigger);
                }
                if(!result.valid){
                    $.extend(args.item , item);
                }
                return result;
            };
            DateValidate.rstartDate = function(value,args){
                var _value = $this.getTime(value),end = args.item.phase_real_end_date;
                return {
                    valid : _value >= $this.getTime(projectName['start_date']) && _value <= $this.getTime(end || projectName['end_date']),
                    message : $this.t('Phase start date must between %1$s and %2$s' ,projectName['start_date'], end || projectName['end_date'])
                };
            };
            DateValidate.rendDate = function(value,args){
                var _value = $this.getTime(value),start = args.item.phase_real_start_date;
                return {
                    valid : _value >= $this.getTime(start || projectName['start_date']) && _value <= $this.getTime(projectName['end_date']),
                    message : $this.t('This date value must between %1$s and %2$s' ,start || projectName['start_date'], projectName['end_date'])
                };
            };
            DateValidate.planedPhase = function(value,args){
                var result = true;
                var getValue = function(value){
                    if(value == 'null' || !value || value == '0'){
                        return  '';
                    }
                    return value;
                }
                $.each(args.grid.getData().getItems(), function(undefined,row){
                    if(row.id != args.item.id && value == row.project_planed_phase_id &&
                        getValue(row.project_part_id) == getValue(args.item.project_part_id)){
                        return result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('This phase of project is exist.')
                };
            };

            DateValidate.partValue = function(value,args){
                var item = $.extend({},args.item);
                args.item.project_part_id = value;
                var result =  {
                    valid : !args.item.project_planed_phase_id ||
                        DateValidate.planedPhase(args.item.project_planed_phase_id , args).valid,
                    message : $this.t('You cannot change project part because conflict phase name.')
                };
                if(!result.valid){
                    $.extend(args.item , item);
                }
                return result;
            };

            var actionTemplate =  $('#action-template').html(), orderTemplate = $('#order-template').html();
            $.extend(Slick.Formatters,{
                percentValues : function(row, cell, value, columnDef, dataContext){
                    var val = number_format(value, 2, ',', ' ');
                    var _html = '<div style="position: relative; text-align: center"><div style="position: absolute; width: '+value+'%; height: 100%; background-color: rgb(77, 255, 130);"></div><span style="position: relative">'+val+' %</span></div>';
                    return _html;
                    // return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span>%', columnDef, dataContext);
                },
                numberValue : function(row, cell, value, columnDef, dataContext){
                    value = value ? value : '';
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span>', columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    // if( dataContext.project_planed_phase_id == currentPhase )
                    // 	return Slick.Formatters.HTMLData(row, cell, '', columnDef, dataContext);
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.project_planed_phase_id,$this.selectMaps['project_planed_phase_id'][dataContext.project_planed_phase_id]), columnDef, dataContext);
                },
                Order : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,orderTemplate);
                },
                Color : function(row, cell, value, columnDef, dataContext){
                    var colorField = columnDef.field
                    if(colorField == 'kpi') {
                        dataColor = dataContext.kpi;
                    } else {
                        dataColor = dataContext.color;
                    }
                    return Slick.Formatters.HTMLData(row, cell,
                    $this.t('<div style="width: 20px; height: 20px; background-color: %s; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>',dataColor), columnDef, dataContext);
                },
                DateTime : function(row, cell, value, columnDef, dataContext){
                    return '<div style="float: right"><span style="text-align: right">' + value + '</span></div>';
                }
            });

            $.extend(Slick.Editors,{
                 numericValueBudget : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 10).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val) || !(val >= 0 && val <= 100)){
                            e.preventDefault();
                            return false;
                        }
                    });
                },
                selectBox2 : function(args){
                    $.extend(this, new Slick.Editors.selectBox(args));
                    this.input.find('[value="' + args.item.id + '"]').remove();
                },
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

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;

            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.createSelectCustom =  function(data,empty){
                var o = '';
                if(empty){
                    o+= '<option selected="selected" value="">' + empty + '</option>';
                }
                dataTemp=<?php echo json_encode($projectPhasesTemp);?>;
                $.each(dataTemp , function(i,v){
                    o += '<option value="'+dataTemp[i]['key']+'">' + dataTemp[i]['value'] + '</option>';
                });
                return '<select>'+ o + '</select>';
            };
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                planed_duration : {defaulValue : ''},
                project_planed_phase_id : {defaulValue : '' , allowEmpty : false},
                project_phase_status_id : {defaulValue : ''},
                project_part_id : {defaulValue : ''},
                phase_planed_start_date : {defaulValue : ''},
                phase_planed_end_date : {defaulValue : ''},
                phase_real_start_date : {defaulValue : ''},
                phase_real_end_date : {defaulValue : '' },
                predecessor : {defaulValue : '' },
                weight : {defaulValue : 0 },
                ref1 : {defaulValue : '' },
                ref2 : {defaulValue : '' },
                ref3 : {defaulValue : '' },
                ref4 : {defaulValue : '' },
                kpi : {defaulValue : ''},
                profile_id : {defaulValue : ''},
                progress : {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';

            var dataGrid = $this.init($('#project_container'),data,columns, {
                frozenColumn: 4
            });
			$('#project_container').data('slickgrid',dataGrid);			
            dataGrid.setSortColumns('weight' , true);
            dataGrid.setSelectionModel(new Slick.RowSelectionModel());
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
            $(dataGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
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
                    url : '<?php echo $html->url('/project_phase_plans/order/' . $projectName['Project']['id']) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                })
                dataGrid.resetActiveCell();
                var dataView = dataGrid.getDataView();
                dataView.beginUpdate();
                //if set data via grid.setData(), the DataView will get removed
                //to prevent this, use DataView.setItems()
                dataView.setItems(data);
                //dataView.setFilter(filter);
                //updateFilter();
                dataView.endUpdate();
                // dataGrid.getDataView.setData(data);
                dataGrid.setSelectedRows(selectedRows);
                dataGrid.render();
            });

            dataGrid.registerPlugin(moveRowsPlugin);
            dataGrid.onDragInit.subscribe(function (e, dd) {
                // prevent the grid from cancelling drag'n'drop by default
                e.stopImmediatePropagation();
            });

            //for exporting
            //2014-20-12
            $('#export-button').click(function(){
                var length = dataGrid.getDataLength(),
                    list = [];
                for(var i = 0; i<length; i++){
                    var item = dataGrid.getDataItem(i);
                    list.push(item.id);
                }
                $('#export-item-list')
                .val(list.join(','))
                .closest('form')
                .submit();
            })

            //
            var checkProcess;
            (function(){

                if(!$this.canModified){
                    return;
                }
                var saveData = {},timeoutID;

                var updateSort = function(){
                    checkProcess=true;
                    $.ajax({
                        url : '<?php echo $html->url(array('action' => 'order', $projectName['Project']['id'])); ?>',
                        cache : false,
                        type : 'POST',
                        data : {
                            data : $.extend({},saveData)
                        },
                        beforeSend:function(){
                            $("#flashMessagePleaseWait").show();
                        },
                        success:function(html) {
                            $("#flashMessagePleaseWait").hide();
                            $("#flashMessageSaveSuccess").show();
                            setTimeout(function(){
                                $("#flashMessageSaveSuccess").hide();
                            },2500);
                            checkProcess=false;
                        },
                        complete:function(){

                        }
                    });
                    saveData = {};
                };

                var getRow = function($element){
                    dataGrid._args = $element.get(0);
                    return dataGrid.eval('getRowFromNode(self._args)');
                };

                var changeClass = function($element){
                    if($element.hasClass('even')){
                        $element.removeClass('even').addClass('odd');
                    } else{
                        $element.removeClass('odd').addClass('even');
                    }
                    return $element;
                }

                var  toggleElement = function($s , $d){
                    //s la prev, d la current
                    var sdata = dataGrid.getDataItem(getRow($s));
                    var ddata = dataGrid.getDataItem(getRow($d));
                    var t = $d.css('top');

                    changeClass($d).css('top' , $s.css('top'));
                    changeClass($s).css('top' , t);

                    var w = ddata.weight;
                    ddata.weight = sdata.weight;
                    sdata.weight = w;

                    saveData[sdata.id] = sdata.weight;
                    saveData[ddata.id] = ddata.weight;
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(updateSort , 500);
                    $s.stop().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                        $s.animate({backgroundColor:'#FFF'}, 'slow' , function(){
                            $s.css('backgroundColor' , '');
                        });
                    });
                };

                $('.phase-order-handler span.wd-up').live('click' , function(){
                    if(checkProcess) {
                        alert('<?php __('System is busy!');?>');
                        return false;
                    } else {
                        var $element = $(this).closest('.slick-row');
                        var $swaper = $element.prev('.slick-row');
                        if ($swaper.length > 0 && $swaper.offset().top < $element.offset().top) {
                            toggleElement($element,$swaper);
                            $swaper.before($element);
                        }
                    }
                });

                $('.phase-order-handler span.wd-down').live('click' , function(){
                    if(checkProcess) {
                        alert('<?php __('System is busy!');?>');
                        return false;
                    } else {
                        var $element = $(this).closest('.slick-row');
                        var $swaper = $element.next('.slick-row');
                        if ($swaper.length > 0 && $swaper.offset().top > $element.offset().top) {
                            toggleElement($element,$swaper);
                            $swaper.after($element);
                        }
                    }
                });
                //ControlGrid = $this.init($('#project_container'),data,columns);
                addNewPhaseButton = function(){
                    dataGrid.gotoCell(data.length, 1, true);
                }

            })();

        });
        function setupScroll(){
            $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
            $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
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
			$('.multiselect-filter input').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			dataGrid = $('#project_container').data('slickgrid');
			dataGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');
			setTimeout(function(){
                location.reload();
            }, 500);
        }
    })(jQuery);
</script>
