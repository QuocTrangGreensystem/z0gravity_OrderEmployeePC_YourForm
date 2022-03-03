<?php
echo $html->script(array('html2canvas', 'jquery.html2canvas_v2'));
echo $html->css('preview/project_task_gantt');

$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
// $type = !empty($arg['?']['type']) ? strtolower(trim($arg['?']['type'])) : 'year';
?>
<style>
    .gantt-msi span{
        margin-left: 2px !important;
    }
    .hideGantt{
        display: none !important;
    }
    .gantt-ms td {
        border-bottom: 1px solid #ccc;
    }
</style>
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
        $stones[$p['part_id'] ? $p['part_id'] : 0][] = array($_start, $p['project_milestone'], $p['validated']);
    }
}

$unsetFields = array(
    'rstart' => 'phase_real_start_date',
    'rend' => 'phase_real_end_date'
);

$Initials = array(
    'start' => 'phase_planed_start_date',
    'end' => 'phase_planed_end_date'
);

$RealTimes = array(
    'rstart' => 'phase_real_start_date',
    'rend' => 'phase_real_end_date'
);
$ChangDateWhenRealTimeNulls = array(
    'phase_real_start_date' => 'phase_planed_start_date',
    'phase_real_end_date' => 'phase_planed_end_date'
);
$convtFields = array_merge($RealTimes, $Initials);

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
        $_phaseDate = $this->GanttV2Preview->toTime($_phaseDate);
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
    //$this->GanttV2Preview->create($type, $_starts, $end, $stones , false);
    $this->GanttV2Preview->create($type, $start, $end,  isset($stones[0]) ? $stones[0] : array(), false);
    $line = array();
	$nodes['empty'] = array();
    foreach ($nodes as $nodeId => $node) {
       
        $rows++;
		if(!empty($node)){
			$splitID = explode('-', $node['id']);
			$viewPhase = true;
			if(!empty($splitID[0]) && $splitID[0] === 'task' && $callProjects){
				$viewPhase = false;
			}
			$this->GanttV2Preview->draw($node['id'], $node['name']
                , $node['predecessor'], $node['start']
                , $node['end'], $node['rstart'], $node['rend'], $node['color']
                , !empty($node['children']) ? 'parent' : 'child'
                , $node['completed']
                , $node['assign']
                , $viewPhase
                );
		}else{
			// draw line empty to display the milestones
			$this->GanttV2Preview->draw('milestone', null, null, null, null, null, null, null, 'parent', null, null);
		}
        if(!empty($node)) $line[$node['id']] = $node['color'];
        if (!empty($node['children'])) {
            foreach ($node['children'] as $child) {
                $splitID = explode('-', $child['id']);
                $viewPhase = true;
                if(!empty($splitID[0]) && $splitID[0] === 'task' && $callProjects){
                    $viewPhase = false;
                }
                $rows++;
                $this->GanttV2Preview->draw($child['id'], $child['name']
                        , $child['predecessor'], $child['start']
                        , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child', $child['completed'], $child['assign'], $viewPhase);
                if(!empty($child['children'])){
                    foreach ($child['children'] as $child) {
                        $splitID = explode('-', $child['id']);
                        $viewPhase = true;
                        if(!empty($splitID[0]) && $splitID[0] === 'task' && $callProjects){
                            $viewPhase = false;
                        }
                        $rows++;
                        $this->GanttV2Preview->draw($child['id'], $child['name']
                                , $child['predecessor'], $child['start']
                                , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child-child', $child['completed'], $child['assign'], $viewPhase);
                        if(!empty($child['children'])){
                            foreach ($child['children'] as $child) {
                                $splitID = explode('-', $child['id']);
                                $viewPhase = true;
                                if(!empty($splitID[0]) && $splitID[0] === 'task' && $callProjects){
                                    $viewPhase = false;
                                }
                                $rows++;
                                $this->GanttV2Preview->draw($child['id'], $child['name']
                                        , $child['predecessor'], $child['start']
                                        , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child-child', $child['completed'], $child['assign'], $viewPhase);
                            }
                        }
                    }
                }
            }
        }
        //draw milestones belong to part
        if(!empty($node) && strpos($node['id'], 'pt-') !== false ){
            if( isset($stones[$nodeId]) ){
                $this->GanttV2Preview->drawMilestones($stones[$nodeId]);
            }
        }
    }
    $this->GanttV2Preview->end();
}
?>
<div style="clear: both"></div>
<div id="ajaxScroll">

</div>
</div>
<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl" class="non-actask">

    </dl>
</div>
<script type="text/javascript">
    var line = <?php echo json_encode($line)?>;
    var $onClickPhaseIds = <?php echo json_encode($onClickPhaseIds); ?>;
    $.each($onClickPhaseIds, function(index, values){
        $.each(values, function(key, val){
            $('.wd-'+val).addClass('hideGantt');
        });
    });
    $('#display_all').click(function(){
        $(this).addClass('hideGantt');
        $('#hide_all').removeClass('hideGantt');
        $.each($onClickPhaseIds, function(index, values){
            $.each(values, function(key, val){
                $('.wd-'+val).removeClass('hideGantt');
            });
        });
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });
    $('#hide_all').click(function(){
        $(this).addClass('hideGantt');
        $('#display_all').removeClass('hideGantt');
        $.each($onClickPhaseIds, function(index, values){
            $.each(values, function(key, val){
                $('.wd-'+val).addClass('hideGantt');
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
                    $('.wd-'+value).removeClass('hideGantt');
                    //$('.wd-'+value).fadeToggle(1000);
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            //$('.wd-'+$vl).slideUp();
                            $('#hide_all').addClass('hideGantt');
                            $('#display_all').removeClass('hideGantt');
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
                    //$('.wd-'+value).fadeToggle(200);
                    $('.wd-'+value).addClass('hideGantt');
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            //$('.wd-'+$vl).slideUp();
                            $('#hide_all').addClass('hideGantt');
                            $('#display_all').removeClass('hideGantt');
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
	// $('.gantt-line-desc.gantt-line-n').show();
	// $('.gantt-line-desc.gantt-line-s').hide();
	$('.gantt-line-s').siblings('.gantt-line-desc.gantt-line-n').show();
	$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
	console.log('zoz');
    // function removeLine(checkboxObject,type){
        // if(checkboxObject.checked){
            // if(type=="n"){
                // $('.gantt-line-n').show();
                // $('.caseline-n').show();
                // $('.gantt-line-desc').removeClass('padding-line');
            // };
            // if(type=="s"){
                // $('.gantt-line-s').show();
                // $('.caseline-s').show();
            // };
            // $('.gantt-line-desc').show();
        // }else{
            // if(type=="n"){
                // if(!$('#displayreal').attr("checked")){
                    // $('.gantt-line-n').hide();
                    // $('.caseline-n').hide();
                    // $('.gantt-line-desc').addClass('padding-line');
                // }
            // }
            // if(type=="s"){
                // if(!$('#displayplan').attr("checked")){
                    // $('.gantt-line-s').hide();
                    // $('.caseline-s').hide();
                // }
            // }
            // $('.gantt-line-desc').show();
        // }
    // }
</script>
