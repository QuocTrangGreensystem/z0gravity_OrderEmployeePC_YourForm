<?php
echo $html->script(array('html2canvas', 'jquery.html2canvas_v2'));
echo $html->script(array('dxhtml/dhtmlxgantt', 'dxhtml/ext/dhtmlxgantt_tooltip'));
echo $html->css(array('dxhtml/dhtmlxgantt'));
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
// $type = !empty($arg['?']['type']) ? strtolower(trim($arg['?']['type'])) : 'year';
?>
<!--[if lt IE 9]>
<?php echo $html->script('flash_canvas/flashcanvas'); ?>
<script type="text/javascript">
    var _createElement = document.createElement;
    document.createElement = function(n){
        var element = _createElement.call(this,n);
        if(n=="canvas"){
            document.getElementById("target").appendChild(element);
            FlashCanvas.initElement(element);
        }
        return element;
    };
</script>
<div id="target" style="position: absolute; top: -10000px;left: -999999px;"></div>
<![endif]-->
<style type="text/css">
    #gantt-display{
        overflow: hidden;
        padding-top: 10px;
    }
    #gantt-display .input{
        float: left;
    }
    #gantt-display .input input{
        vertical-align: middle;
    }
    #gantt-display .input label{
        padding: 0 7px;
    }
    #gantt-display .title{
        float: left;
        font-weight: bold;
        padding-right: 10px;
    }
    .export-excel-icon-all{
             background: url("<?php echo $this->Html->webroot('img/export.jpg'); ?>") no-repeat;
            display: block;
            width: 32px;
            float: right;
            margin-left: 8px;
            padding-bottom: 16px;
    }
    .export-excel-icon-all:hover{
         background: url("<?php echo $this->Html->webroot('img/export_hover.jpg'); ?>") no-repeat;
        display: block;
        width: 32px;
        float: right;
        margin-left: 8px;
        padding-bottom: 16px;
    }
    .export-excel-icon-all span{
        text-indent: -9999px;
        display: block;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-title">
                <div style="float: left;">
                    <?php
                    echo $this->Form->create('Display', array('type' => 'get', 'url' => array_merge(array(
                            'controller' => 'project_phase_plans',
                            'action' => 'phase_vision', $projectName['Project']['id']
                        ))));
                    ?>
                    <div id="gantt-display">
                        <label class="title"><?php //__('Display real time'); ?> </label>
                        <?php
                    //     echo $this->Form->input('display', array(
                    //        'onchange' => 'jQuery(this).closest(\'form\').submit();',
                    //        'value' => $display,
                    //        'options' => array(__('Initial schedule', true), __('Real Time', true)),
                    //        'type' => 'radio', 'legend' => false, 'fieldset' => false
                    //    ));
                    //    foreach ($arg["?"] as $key => $val) {
                    //        if ($key == 'display') {
                    //            continue;
                    //        }
                    //        echo $this->Form->hidden($key, array('value' => $val));
                    //    }
                        ?>
                        <label class="title" style="float: left; padding-right: 10px;"><?php __('Initial schedule'); ?> </label>
                        <?php
                            $displayplan = $display = 1;
                            $chk = ($displayplan == 1) ? true : false;
                            $chkreal = ($display == 1) ? true : false;
                            echo $this->Form->input('displayplan', array(
                                'rel' => 'no-history',
                                'onchange' => 'removeLine(this,"n");',
                                'value' => $displayplan,
                                'label' => '',
                                'class' => 'inputcheckbox',
                                'type' => 'checkbox', 'legend' => false, 'fieldset' => false, 'checked' => $chk
                            ));?>
                            <label class="title" style="float: left; padding-right: 10px;"><?php __('Real Time'); ?> </label>
                            <?php
                            echo $this->Form->input('displayreal', array(
                                'rel' => 'no-history',
                                'onchange' => 'removeLine(this,"s");',
                                'value' => $display,
                                'label' => '',
                                'class' => 'inputcheckbox',
                                'type' => 'checkbox', 'legend' => false, 'fieldset' => false ,'checked' => $chkreal
                            ));
                        ?>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
                <a href="#" onclick="SubmitDataExport();return false;" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                <a href="javascript:void(0);" id="display_all" class="wd-add-project" style="margin-right:5px; "><span><?php __('Display All') ?></span></a>
                <a href="javascript:void(0);" id="hide_all" class="wd-add-project" style="margin-right:5px; display: none;"><span><?php __('Hide All') ?></span></a>
            </div>
            <div class="wd-tab">
                <?php
                if (isset($view_id))
                    echo $this->element('project_tab_view');
                else
                //echo $this->element('project_tab')

                    ?>
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <div class="wd-content">
                            <h2 class="vision-project"><?php echo sprintf(__('Phase plans: %s', true), $projectName['Project']['project_name']); ?></h2>
                            <div class="gantt-switch">
                                <?php echo $this->Html->link(__('Date', true), Set::merge($arg, array('?' => array('type' => 'date'))), array('class' => $type == 'date' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('Week', true), Set::merge($arg, array('?' => array('type' => 'week'))), array('class' => $type == 'week' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('Month', true), Set::merge($arg, array('?' => array('type' => 'month'))), array('class' => $type == 'month' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('Year', true), Set::merge($arg, array('?' => array('type' => 'year'))), array('class' => $type == 'year' ? 'gantt-switch-current' : '')); ?>
                            </div>
                            <div id="AjaxGanttChartDIV">
                                <?php
                                $stones = array();

                                $start = $end = 0;
                                $rows = 3;
                                if (!empty($projectName['ProjectMilestone'])) {
                                    foreach ($projectName['ProjectMilestone'] as $p) {
                                        $_start = strtotime($p['milestone_date']);
                                        if (!$start || $_start < $start) {
                                            $start = $_start;
                                        } elseif (!$end || $_start > $end) {
                                            $end = $_start;
                                        }
                                        $stones[] = array($_start, $p['project_milestone'], $p['validated']);
                                    }
                                }
                                $nodes = array();
                                $links = array();
                                foreach ($phasePlans as $phasePlan) {
                                    $part = $phasePlan['ProjectPhasePlan']['project_part_id'];
                                    $start_time = !empty($phasePlan['ProjectPhasePlan']['phase_real_start_date']) ? $phasePlan['ProjectPhasePlan']['phase_real_start_date'] : $phasePlan['ProjectPhasePlan']['phase_planed_start_date'];
                                    $_phase = array(
                                        'id' => $phasePlan['ProjectPhasePlan']['id'],
                                        'project_part_id' => $part,
                                        'text' => $phasePlan['ProjectPhase']['name'],
                                        'duration' => $phasePlan['ProjectPhasePlan']['planed_duration'],
                                        'start_unix' => $this->Time->toUnix($start_time),
                                        'start_date' => $this->Time->format('Y-m-d', $start_time),
                                        'predecessor' => $phasePlan['ProjectPhasePlan']['predecessor'],
                                        'color' => $phasePlan['ProjectPhase']['color'] ? $phasePlan['ProjectPhase']['color'] : '#004380',
                                        'assign' => ''
                                    );
                                    $_phase['progress'] = isset($phaseCompleted[$_phase['id']]) ? $phaseCompleted[$_phase['id']]['completed']/100 : 0;
                                    if(!empty($projectTasks)){
                                        foreach($projectTasks as $projectTask){
                                            $projectTask['color'] = $_phase['color'];
                                            $projectTask['start'] = !empty($projectTask['start']) && $projectTask['start']>0 ? $projectTask['start'] : $_phase['start'];
                                            if($_phase['id'] == $projectTask['project_part_id']){
                                                if(!empty($projectTask['children'])){
                                                    foreach($projectTask['children'] as $k => $vl){
                                                        $projectTask['children'][$k]['color'] = $projectTask['color'];
                                                        $projectTask['children'][$k]['start'] = !empty($vl['start']) && $vl['start']>0 ? $vl['start'] : $projectTask['start'];
                                                        $projectTask['children'][$k]['end'] = !empty($vl['end']) && $vl['end']>0 ? $vl['end'] : $projectTask['start'];
                                                        $projectTask['children'][$k]['progress'] = $projectTask['completed']/100;
                                                        //$projectTask['children'][$k]['duration'] = $projectTask['duration'];
                                                    }
                                                }
                                                $_phase['children'][] = $projectTask;
                                            }
                                        }
                                    }
                                    if ($part) {
                                        if (empty($nodes[$part])) {
                                            $nodes[$part] = array(
                                                'id' => 'pt-' . $part,
                                                'name' => isset($parts[$part]) ? __('Part', true) .': '. $parts[$part] : __('Unknown', true),
                                                'predecessor' => '',
                                                'color' => '#000',
                                                'progress' => isset($taskCompleted[$part]) ? $taskCompleted[$part]['completed']/100 : 0,
                                                'assign' => ''
                                            );
                                        }
                                        $nodes[$part]['children'][] = $_phase;
                                    } else {
                                        $nodes[$_phase['id']] = $_phase;
                                    }
                                }
                                unset($phasePlans, $phasePlan);
                                if (empty($start) || empty($end)) {
                                    echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true));
                                } else {
                                    pr($nodes);
                                }
                                ?>
                                <div style="clear: both"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl" class="non-actask">
    </dl>
</div>
<?php
echo $this->Form->create('Export', array('url' => array_merge($arg, array('controller' => 'project_phase_plans', 'action' => 'export_vision')), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('project', array('value' => $projectName['Project']['project_name']));
echo $this->Form->hidden('rows', array('value' => $rows));
echo $this->Form->end();
?>
<script type="text/javascript">
    function SubmitDataExport(){
       //$('#GanttChartDIV').html2canvas();
    }
</script>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<style>
    .padding-line{
        padding-top: 10px;
    }
</style>
