<?php echo $html->css(array('gantt', 'demo_table','project_staffing_visions')); ?>
<?php echo $html->script('history_filter'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'month';
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

<?php
echo $html->script(array('html2canvas', 'jquery.html2canvas'));
echo $html->css('jquery.mCustomScrollbar');
echo $html->script(array('jquery-ui.min', 'jquery.easing.1.3', 'jquery.mCustomScrollbar'));
?>
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <div style="float: left;">
                        <?php
                        echo $this->Form->create('Display', array('type' => 'get', 'url' => array_merge(array(
                                'controller' => 'project_staffings',
                                'action' => 'visions_staffing'
                            ))));
                        ?>
                        <div id="gantt-display">
                            <label class="title"><?php __('Display real time'); ?> </label>
                            <?php
                            echo $this->Form->input('display', array(
                                'rel' => 'no-history',
                                'onchange' => 'jQuery(this).closest(\'form\').submit();',
                                'value' => $display,
                                'options' => array(__('No', true), __('Yes', true)),
                                'type' => 'radio', 'legend' => false, 'fieldset' => false
                            ));
                            foreach ($arg["?"] as $key => $val) {
                                if ($key == 'display') {
                                    continue;
                                }
                                echo $this->Form->hidden($key, array('value' => $val));
                            }
                            ?>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                    <?php
                    /*
                    <a href="#" onclick="SubmitDataExport();return false;" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                    */
                    ?>
                </div>
                <p><?php //echo  __('Workload(*) = Workload + Overload', true);?></p>
                <div id="GanttChartDIV">
                    <?php
                    $rows = 0;
                    $start = $end = 0;
                    $data = $projectId = $conditions = array();
                    foreach ($projects as $project) {
                        $_data = array(
                            'name' => $project['Project']['project_name'],
                            'phase' => array(),
                        );
                        $projectId[$project['Project']['id']] = $project['Project']['project_name'];
                        if (!empty($project['ProjectPhasePlan'])) {
                            foreach ($project['ProjectPhasePlan'] as $phace) {
                                $_phase = array(
                                    'name' => !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '',
                                    'start' => $this->GanttVs->toTime($phace['phase_planed_start_date']),
                                    'end' => $this->GanttVs->toTime($phace['phase_planed_end_date']),
                                    'rstart' => $this->GanttVs->toTime($phace['phase_real_start_date']),
                                    'rend' => $this->GanttVs->toTime($phace['phase_real_end_date']),
                                    'color' => !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380'
                                );
                                if ($_phase['rstart'] > 0) {
                                    $_start = min($_phase['start'], $_phase['rstart']);
                                } else {
                                    $_start = $_phase['start'];
                                }
                                if (!$start || ($_start > 0 && $_start < $start)) {
                                    $start = $_start;
                                }
                                $_end = max($_phase['end'], $_phase['rend']);
                                if (!$end || $_end > $end) {
                                    $end = $_end;
                                }
                                $_data['phase'][] = $_phase;
                            }
                        }
                        $data[] = $_data;
                    }
//                    pr(date('Y-m-d',$start));
//                    pr(date('Y-m-d',$end));
//                    pr($projects);
//                    exit();

                    unset($projects, $project, $_data, $_phase, $phase);



                    $summary = isset($this->params['url']['summary']) ? $this->params['url']['summary'] : false;
                    $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;

                    if ($showType != 1) {
                        unset($_filter['profit_center_id']);
                    }
                    $conditions = array_merge($_filter, array(
                        'NOT' => array('project_function_id' => null),
                        'project_id' => array_keys($projectId)));


                    if (empty($start) || empty($end)) {
                        echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                    } else {
                        $this->GanttVs->create($type, &$start, &$end, array(), false);
                        $staffings = array();
                        // change in here
                        if(!empty($staffingss)){
                            $staffings = $staffingss;
                        }
                        foreach ($data as $value) {
                            $rows++;
                            if (empty($value['phase'])) {
                                $this->GanttVs->drawLine(__('no data exit', true), 0, 0, 0, 0, '#ffffff');
                            } else {
                                foreach ($value['phase'] as $node) {
                                    $color = '#004380';
                                    if (!empty($node['color'])) {
                                        $color = $node['color'];
                                    }
                                    if (!$display) {
                                        $node['rstart'] = $node['rend'] = '';
                                    }
                                    $this->GanttVs->drawLine($node['name'], $node['start'], $node['end'], $node['rstart'], $node['rend'], $color);
                                }
                            }
                            if($showGantt){
                                $this->GanttVs->drawEnd($value['name']);
                            }
                        }
                        echo $this->Html->scriptBlock('GanttData = ' . $this->GanttVs->drawStaffing($staffings, $summary, $showType, &$activityType));
                        $this->GanttVs->end();
                    }
                    ?>
                    <div style="clear: both;"></div>
                </div>
                <?php
                if (!empty($start) && !empty($end) && empty($staffings)) {
                    echo $this->Html->tag('h1', __('No data exist to create staffing', true), array('style' => 'color:red'));
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Form->create('Export', array('url' => array('controller' => 'project_staffings', 'action' => 'export_system'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('rows', array('value' => $rows));
echo $this->Form->hidden('start', array('value' => $start));
echo $this->Form->hidden('end', array('value' => $end));
echo $this->Form->hidden('summary', array('value' => $summary));
echo $this->Form->hidden('showGantt', array('value' => $showGantt));
echo $this->Form->hidden('showType', array('value' => $showType));
echo $this->Form->hidden('conditions', array('value' => serialize($conditions)));
echo $this->Form->hidden('projectId', array('value' => serialize($projectId)));
echo $this->Form->hidden('months', array('value' => serialize($this->Gantt->getMonths())));
echo $this->Form->hidden('displayFields', array('value' => '0'));
echo $this->Form->end();
?>

<!-- Dialog Export -->
<div id="dialog_vision_export" class="buttons" title="<?php __("Display Fields") ?>" style="display: none;">
    <fieldset>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="input">
                <?php
                echo $this->Form->input('displayFields', array(
                    'div' => false,
                    'label' => false,
                    'name' => 'displayFields',
                    'empty' => false,
                    'id' => 'dialog_vision_export_fields',
                    'multiple' => 'checkbox',
                    'hiddenField' => false,
                    // 'style' => 'margin-right:11px; width:52% !important',
                    'value' => 0,
                    "options" => array(
                        0 => __("All fields", true),
                        'estimation' => __('Estimation', true),
                        'validated' => __('Validated', true),
                        'remains' => __('Postponed', true),
                        'consumed' => __('Consumed', true),
                        'forecast' => __('Forecast', true)
                        )));
                ?>   							
            </div>
        </div>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel" id="no_port"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_port"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- Dialog Export -->

<script type="text/javascript">
    function SubmitDataExport(){
        var $ = jQuery;
        $('#dialog_vision_export').dialog({
            position    :'center',
            autoOpen    : true,
            autoHeight  : true,
            modal       : true,
            width       : 300,
            open : function(e){
                //var $dialog = $(e.target);
            }
        });
        $('#no_port').click(function(){
            $('#dialog_vision_export').dialog('close');
        });
        $('#ok_port').click(function(){
            var input = $('#dialog_vision_export input[name="displayFields[]"]').filter(':checked');
            if(!input.length){
                alert('<?php echo h(__('Please choose a fields to export' , true)); ?>');
            }else{
                $('#dialog_vision_export').dialog('close');
                //var rs = [];
                //$.each(input);
                $('#ExportDisplayFields').val($.map(input.get() , function(v){return $(v).val();}).join(','));
                $('#GanttChartDIV').html2canvas();
            }
        });
        $('#DisplayFields0').click(function(){
            $(this).closest('.input').find('input').prop('checked' , $(this).is(':checked'));
        });
    }
    (function($){
        var na = '<?php echo $this->Gantt->na; ?>';
        /**
         * Convert to float
         * 
         * @param val
         * @param dp
         * 
         * return mixed na or float number 
         */
        var toFloat = function (val,dp){
            if(dp && (val.length == 0 || val == na)){
                return na;
            }
            val =  Number(parseFloat(val || '0').toFixed(1));
            return (isNaN(val) || val <= 0)  ? 0 : val;
        }
        /**
         * Attach grid element event
         *
         */
        GanttCallback = function($list, $gantt){ 
            $gantt.find('.gantt-input').each(function(){
                var  $element =  $(this);
                var val = toFloat($element.html() , true);
                //$element.html(val);
                if(val != na && val > 0){
                    $element.addClass('gantt-unzero');
                }
            });
        }
        
        var mgLeft = $('#mcs_container').find('.container').width();
        $('.gantt-chart-0').css('margin-left', mgLeft-3);

    })(jQuery);
</script>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>