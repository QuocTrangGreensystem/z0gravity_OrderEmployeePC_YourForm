<?php
echo $html->css('jquery.dataTables');
echo $html->css('gantt_v2_1');
echo $html->script(array('html2canvas', 'jquery.html2canvas_v2'));
echo $html->css('jquery.mCustomScrollbar');
echo $html->css('preview/project_task_gantt');
echo $html->script(array('jquery.easing.1.3',  'jquery.mCustomScrollbar'));
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
// $default = $isMobile || $isTablet ? 'year' : 'month';
// $type = !empty($arg['?']['type']) ? strtolower(trim($arg['?']['type'])) : $default;
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
    .gantt-ms td {
        border-bottom: 1px solid #ccc;
    }
    .gantt-chart-wrapper{
        min-height: 600px;
    }
    .gantt-month span {
        margin-top: -16px;
        margin-left: 5px;
    }
	.btn.btn-add{
		position: relative;
	}
	.btn.btn-add p{
		position: absolute;
		background-color: #424242;
		width: 20px;
		height: 2px;
		left: calc( 50% - 10px);
		top: calc( 50% - 1px);
	}
	.btn.btn-add p+p{
		top: calc( 50% - 10px);
		left: calc( 50% - 1px);
		width: 2px;
		height: 20px;
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
				<a href="javascript:void(0);" id="display_all" class="btn btn-text btn-add" title="<?php __('Display All') ?>">
					<p class="line"></p>
					<p class="line"></p>
				</a>
				<a href="javascript:void(0);" id="hide_all" class="btn btn-text btn-add" style="display: none;" title="<?php __('Hide All') ?>">
					<p class="line"></p>
				</a>
                <a href="#" onclick="SubmitDataExport();return false;" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
            </div>
            <div class="wd-tab">
                <?php
                if (isset($view_id))
                    echo $this->element('project_tab_view');
                    ?>
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <div class="wd-content">
                            <h2 style="color: #ffb250" class="vision-project"><?php echo sprintf(__('Phase plans: %s', true), $projectName['Project']['project_name']); ?></h2>
                            <div class="gantt-switch">
                                <?php echo $this->Html->link(__('Date', true), Set::merge($arg, array('?' => array('type' => 'date'))), array('class' => $type == 'date' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('Week', true), Set::merge($arg, array('?' => array('type' => 'week'))), array('class' => $type == 'week' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('Month', true), Set::merge($arg, array('?' => array('type' => 'month'))), array('class' => $type == 'month' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('Year', true), Set::merge($arg, array('?' => array('type' => 'year'))), array('class' => $type == 'year' ? 'gantt-switch-current' : '')); ?>
                                <?php echo $this->Html->link(__('2 Years', true), Set::merge($arg, array('?' => array('type' => '2years'))), array('class' => ($type == '2years' ? 'gantt-switch-current' : '') . ' x-year')); ?>
                                <?php echo $this->Html->link(__('3 Years', true), Set::merge($arg, array('?' => array('type' => '3years'))), array('class' => ($type == '3years' ? 'gantt-switch-current' : '') . ' x-year')); ?>
                                <?php echo $this->Html->link(__('4 Years', true), Set::merge($arg, array('?' => array('type' => '4years'))), array('class' => ($type == '4years' ? 'gantt-switch-current' : '') . ' x-year')); ?>
                                <?php echo $this->Html->link(__('5 Years', true), Set::merge($arg, array('?' => array('type' => '5years'))), array('class' => ($type == '5years' ? 'gantt-switch-current' : '') . ' x-year')); ?>
                                <?php echo $this->Html->link(__('10 Years', true), Set::merge($arg, array('?' => array('type' => '10years'))), array('class' => ($type == '10years' ? 'gantt-switch-current' : '') . ' x-year')); ?>
                            </div>
                            <div id="GanttChartDIV">
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
                                        $stones[$p['part_id'] ? $p['part_id'] : 0][] = array($_start, $p['project_milestone'], $p['validated']);
                                    }
                                }
                            //     $convtFields = array(
                            //        'start' => 'phase_planed_start_date',
                            //        'end' => 'phase_planed_end_date',
                            //    );
                                $unsetFields = array(
                                    'rstart' => 'phase_real_start_date',
                                    'rend' => 'phase_real_end_date'
                                );
                            //    if ($display) {
                            //        $convtFields = array_merge($convtFields, $unsetFields);
                            //        $unsetFields = array();
                            //    }
                                $Initials = array(
                                    'start' => 'phase_planed_start_date',
                                    'end' => 'phase_planed_end_date'
                                );
                            //     $RealTimes = array(
                            //        'start' => 'phase_real_start_date',
                            //        'end' => 'phase_real_end_date'
                            //    );
                                $RealTimes = array(
                                    'rstart' => 'phase_real_start_date',
                                    'rend' => 'phase_real_end_date'
                                );
                                $ChangDateWhenRealTimeNulls = array(
                                    'phase_real_start_date' => 'phase_planed_start_date',
                                    'phase_real_end_date' => 'phase_planed_end_date'
                                );
                                $convtFields = array_merge($RealTimes, $Initials);
                            //     if($display){
                            //        $convtFields = $RealTimes;
                            //    } else {
                            //        $convtFields = $Initials;
                            //    }
                                $nodes = array();
                                foreach ($phasePlans as $phasePlan) {
                                    $part = $phasePlan['ProjectPhasePlan']['project_part_id'];
                                    $_phase = array(
                                        'id' => $phasePlan['ProjectPhasePlan']['id'],
                                        'project_part_id' => $part,
                                        'name' => $phasePlan['ProjectPhase']['name'],
                                        'predecessor' => $phasePlan['ProjectPhasePlan']['predecessor'],
                                        'color' => $phasePlan['ProjectPhase']['color'] ? $phasePlan['ProjectPhase']['color'] : '#004380',
                                        'assign' => ''
                                    );
                                    foreach ($unsetFields as $key => $field) {
                                        $_phase[$key] = 0;
                                    }
                                    foreach ($convtFields as $key => $field) {
                                        $_phaseDate = !empty($phasePlan['ProjectPhasePlan'][$field]) ? $phasePlan['ProjectPhasePlan'][$field] : $phasePlan['ProjectPhasePlan'][$ChangDateWhenRealTimeNulls[$field]];
                                        $_phaseDate = $this->GanttV2->toTime($_phaseDate);
                                        $_phase[$key] = $_phaseDate;
                                    }
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
                                    $_phase['completed'] = isset($phaseCompleted[$_phase['id']]) ? $phaseCompleted[$_phase['id']]['completed'] : 0;
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
                                                'completed' => isset($taskCompleted[$part]) ? $taskCompleted[$part]['completed'] : 0,
                                                'assign' => ''
                                            );
                                            foreach ($unsetFields as $key => $field) {
                                                $nodes[$part][$key] = 0;
                                            }
                                        }
                                        foreach ($convtFields as $key => $field) {
                                            $_started = strpos($key, 'start');
                                            if (!isset($nodes[$part][$key]) || ($_phase[$key] > 0 &&
                                                    ( ($_started === false && $_phase[$key] > $nodes[$part][$key])
                                                    || ($_started !== false && ($nodes[$part][$key] == 0 || $_phase[$key] < $nodes[$part][$key])) ))) {
                                                $nodes[$part][$key] = $_phase[$key];
                                            }
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
                                    //$_starts = mktime(0, 0, 0, date("m", $start)-1, date("d", $start), date("Y", $start));
                                    //$this->GanttV2->create($type, $_starts, $end, $stones , false);
                                    $this->GanttV2->create($type, $start, $end, isset($stones[0]) ? $stones[0] : array(), false);
                                    $line = array();
                                    foreach ($nodes as $nodeId => $node) {
                                        $rows++;
                                        $this->GanttV2->draw($node['id'], $node['name']
                                                , $node['predecessor'], $node['start']
                                                , $node['end'], $node['rstart'], $node['rend'], $node['color']
                                                , !empty($node['children']) ? 'parent' : 'child'
                                                , $node['completed']
                                                , $node['assign']
                                                );
                                        $line[$node['id']] = $node['color'];
                                        if (!empty($node['children'])) {
                                            foreach ($node['children'] as $child) {
                                                $rows++;
                                                $this->GanttV2->draw($child['id'], $child['name']
                                                        , $child['predecessor'], $child['start']
                                                        , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child', $child['completed'], $child['assign']);
                                                if(!empty($child['children'])){
                                                    foreach ($child['children'] as $child) {
                                                        $rows++;
                                                        $this->GanttV2->draw($child['id'], $child['name']
                                                                , $child['predecessor'], $child['start']
                                                                , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child-child', $child['completed'], $child['assign']);
                                                        if(!empty($child['children'])){
                                                            foreach ($child['children'] as $child) {
                                                                $rows++;
                                                                $this->GanttV2->draw($child['id'], $child['name']
                                                                        , $child['predecessor'], $child['start']
                                                                        , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child-child', $child['completed'], $child['assign']);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //draw milestones belong to part
                                        if( strpos($node['id'], 'pt-') !== false ){
                                            if( isset($stones[$nodeId]) ){
                                                $this->GanttV2->drawMilestones($stones[$nodeId]);
                                            }
                                        }
                                    }
                                    $this->GanttV2->end();
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
        // var dragger_container = $('.dragger_container:visible');
        // $("#mcs1_container .container").css('left', '0px');
        // dragger_container.children(".dragger.ui-draggable").css('left', '0px');
        $('#wd-container-main .wd-layout, #mcs1_container .customScrollBox').css('overflow', 'visible');
        var cleft = parseFloat($('#mcs1_container .container').css('left'));
        $('#mcs1_container .container').css('left', '0px');
        $('#GanttChartDIV').html2canvas({
            afterCanvas: function(){
                $('#wd-container-main .wd-layout, #mcs1_container .customScrollBox').css('overflow', 'hidden');
                $('#mcs1_container .container').css('left', (isNaN(cleft) ? cleft : 0) + 'px');
            }
        });
    }
    var line = <?php echo json_encode($line)?>;
   // $.each(line, function(i, v){
       // $('#line-n-'+i).css('border','1px dashed' + v);
    //});
    var $onClickPhaseIds = <?php echo json_encode($onClickPhaseIds); ?>;
    $.each($onClickPhaseIds, function(index, values){
        $.each(values, function(key, val){
            $('.wd-'+val).css('display', 'none');

        });
    });
    $('#display_all').click(function(){
        $(this).css('display', 'none');
        $('#hide_all').css('display', 'inline-block');
        $.each($onClickPhaseIds, function(index, values){
            $.each(values, function(key, val){
                $('.wd-'+val).css('display', 'inline-block');
            });
        });
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });
    $('#hide_all').click(function(){
        $(this).css('display', 'none');
        $('#display_all').css('display', 'inline-block');
        $.each($onClickPhaseIds, function(index, values){
            $.each(values, function(key, val){
                $('.wd-'+val).css('display', 'none');
            });
        });
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });
    $('.gantt-primary tr').toggle(function(){
        var classPhase = $(this).attr("class") ? $(this).attr("class").split(' ')[1] : '';
        if(classPhase != ''){
            if($onClickPhaseIds[classPhase]){
                $.each($onClickPhaseIds[classPhase], function(index, value){
                    $('.wd-'+value).fadeToggle(1000);
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            $('.wd-'+$vl).slideUp();
                            $('#hide_all').css('display', 'none');
                            $('#display_all').css('display', 'block');
                        });
                    }
                });
            }
        }
        $('.gantt_clears').remove();
        Gantt.rdraw();
    }, function(){
        var classPhase = $(this).attr("class") ? $(this).attr("class").split(' ')[1] : '';
        if(classPhase != ''){
            if($onClickPhaseIds[classPhase]){
                $.each($onClickPhaseIds[classPhase], function(index, value){
                    $('.wd-'+value).fadeToggle(200);
                    $('.wd-'+value).hide();
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            $('.wd-'+$vl).slideUp();
                            $('#hide_all').css('display', 'none');
                            $('#display_all').css('display', 'block');
                        });
                    }
                });
            }
        }
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });

    var tooltipTemplate = $('#tooltip-template').html();
    // build the tool-tip on mouse over
    $(document).on('mouseenter','div.hover-tooltip' , function(e){
        // on moust enter
        var $el         = $(this);
        var idHove = $el.attr("id") ? $el.attr("id").split(' ')[0] : '';

        var Datas = $('#'+idHove).find('div#hover-data');
        var content = (
            tooltipTemplate,
            '<p style="font-weight: bold;">'+ Datas.find('.hover-data-name').html()+'</p>'
            +'<p style="padding-top: 6px"><?php __('Start Date'); ?>&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-start').html()+'</span></p>'
            +'<p style="padding-top: 4px"><?php __('End Date'); ?>&nbsp;&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-end').html()+'</span></p>'
            +'<p style="padding-top: 4px"><?php __('Progress'); ?>&nbsp;&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-comp').html()+'%</span></p>'
            +'<p style="padding-top: 4px;"><?php __('Resource'); ?>&nbsp;:&nbsp;&nbsp;<span style="font-weight: bold;">'+ Datas.find('.hover-data-assign').html()+'</span></p>'
        );
        $el.tooltip({
            maxWidth : 400,
            maxHeight : 300,
            openEvent : 'xtip-show',
            closeEvent : 'xtip-hide',
            content: content
        }).trigger('xtip-show',e);
    }).on('click mouseleave','div.hover-tooltip' , function(){
        //orthewise destroy the tooltip when mouse leaved
        $(this).tooltip('destroy');
    });
    function removeLine(checkboxObject,type){
        if(checkboxObject.checked){
            if(type=="n"){
                $('.gantt-line-n').show();
                $('.caseline-n').show();
                $('.gantt-line-desc').removeClass('padding-line');
            };
            if(type=="s"){
                $('.gantt-line-s').show();
                $('.caseline-s').show();
            };
            $('.gantt-line-desc').show();
        }else{
            if(type=="n"){
                if(!$('#displayreal').attr("checked")){
                    $('.gantt-line-n').hide();
                    $('.caseline-n').hide();
                    $('.gantt-line-desc').addClass('padding-line');
                }
            }
            if(type=="s"){
                if(!$('#displayplan').attr("checked")){
                    $('.gantt-line-s').hide();
                    $('.caseline-s').hide();
                }
            }
            $('.gantt-line-desc').show();
        }
    }
    jQuery(document).ready(function($) {
        var today = new Date('<?php echo date('Y-m-d') ?>');
        var startYear = <?php echo date('Y', $start) ?>, endYear = <?php echo date('Y', $end) ?>;
        var type = '<?php echo $type ?>';
        if( endYear - startYear < 2 ){
             $('.x-year').hide();
        }
        switch(type){
            case 'year':
            case 'month':
                var $col = $('#month_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
            case 'week':
                var $col = $('#week_<?php echo date('W') ?>_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
            default:
                var $col = $('#date_' + today.getDate() + '_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
        }
        if( $col.length ){
            var container = $("#mcs1_container .container");
            var dragger_container = $('.dragger_container:visible');
            var max = container.width() - dragger_container.width();
            var ratio = $col.position().left / container.width();
            if( ratio > 1 )ratio = 1;
            var left = 0 - Math.round(ratio * max);
            var scroll = Math.round(ratio * (dragger_container.width() - dragger_container.children(".dragger.ui-draggable").width()));
            $("#mcs1_container .container").css('left', left + 'px');
            dragger_container.children(".dragger.ui-draggable").css('left', scroll + 'px');
        }
        $(window).trigger('resize');
    });
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
