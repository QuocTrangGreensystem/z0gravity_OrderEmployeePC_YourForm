<?php 
echo $html->script(array('jquery.easing.1.3','jquery.mCustomScrollbar'));
echo $html->script(array('jquery.html2canvas_v2'));
?>	

<?php echo $html->script('preview/define_limit_date'); ?>
<!-- Gantt Start -->

	<?php $stones = array();
	$start = $end = 0;
	$rows = 3;
	if (!empty($projectPlanName['ProjectMilestone'])) {
		foreach ($projectPlanName['ProjectMilestone'] as $p) {
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
							//Ticket 1165, loi hien thi initial cua sub task.
							$projectTask['children'][$k]['start'] = !empty($vl['start']) && $vl['start']>0 ? $vl['start'] : 0;
							$projectTask['children'][$k]['end'] = !empty($vl['end']) && $vl['end']>0 ? $vl['end'] : 0;
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
	$line = array();
	$nodes['empty'] = array();
	if (empty($start) || empty($end)) {
		echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true));
	} else {
		$this->GanttV2Preview->create($type, $start, $end, isset($stones[0]) ? $stones[0] : array(), false);
		foreach ($nodes as $nodeId => $node) {
			$rows++;
			if(!empty($node)){
				$this->GanttV2Preview->draw($node['id'], $node['name']
					, $node['predecessor'], $node['start']
					, $node['end'], $node['rstart'], $node['rend'], $node['color']
					, !empty($node['children']) ? 'parent' : 'child'
					, $node['completed']
					, $node['assign']
					);
			}else{
				// draw line empty to display the milestones
				$this->GanttV2Preview->draw('milestone', null, null, null, null, null, null, null, 'parent', null, null);
			}
			if(!empty($node)) $line[$node['id']] = $node['color'];
			if (!empty($node['children'])) {
				foreach ($node['children'] as $child) {
					$rows++;
					$this->GanttV2Preview->draw($child['id'], $child['name']
							, $child['predecessor'], $child['start']
							, $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child', $child['completed'], $child['assign']);
					if(!empty($child['children'])){
						foreach ($child['children'] as $child) {
							$rows++;
							$this->GanttV2Preview->draw($child['id'], $child['name']
									, $child['predecessor'], $child['start']
									, $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child-child', $child['completed'], $child['assign']);
							if(!empty($child['children'])){
								foreach ($child['children'] as $child) {
									$rows++;
									$this->GanttV2Preview->draw($child['id'], $child['name']
											, $child['predecessor'], $child['start']
											, $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child-child', $child['completed'], $child['assign']);
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
	<span onclick="onPrevous()" class="scroll-progress scroll-left"><i class="icon-arrow-left"></i></span>
	<span onclick="onNext()" class="scroll-progress scroll-right"><i class="icon-arrow-right"></i></span>

<!-- Gantt.end -->

<style>
#mcs1_container.gantt-chart{
	margin: 0;
}

</style>
<script>
	var line = <?php echo json_encode($line)?>;
    var $onClickPhaseIds = <?php echo json_encode($onClickPhaseIds); ?>;
    $.each($onClickPhaseIds, function(index, values){
        $.each(values, function(key, val){
            $('.wd-'+val).css('display', 'none');

        });
    });
	function onNext() {
        var customScrollBox_container = $(".customScrollBox .container");
        var thePos = customScrollBox_container.position().left;
        var container = $('.customScrollBox .container').width();
        var customScrollBox = $('.customScrollBox').width();
        var left = (customScrollBox - container);
        if( (left - thePos) > -200 ){
            customScrollBox_container.css("left", left);
        }else{
            customScrollBox_container.stop().animate({left: "-=" + 200});
        }
    }
    function onPrevous() {
        var customScrollBox_container = $(".customScrollBox .container");
        var thePos = customScrollBox_container.position().left;
        if(thePos < 0 && thePos < -200) customScrollBox_container.stop().animate({left: "+=" + 200});
        if(thePos > -200) customScrollBox_container.css("left", "0");
    }
	var tooltipTemplate = $('#tooltip-template').html();
	$(document).on('mouseenter','div.hover-tooltip' , function(e){
        // on moust enter
        var $el         = $(this);
        var idHove = $el.attr("id") ? $el.attr("id").split(' ')[0] : '';

        var Datas = $('#'+idHove).find('div#hover-data');
        var initDate = Datas.find('.hover-data-comp').html();
        var class_pro = '';
        if(initDate > 50) class_pro = 'late-progress';
        var _html_progress ="<div class='task-progress'><div class='project-progress "+ class_pro +"'><p class='progress-full'>" + draw_line_progress(initDate) + "</p></div></div>";
        var content = (
            tooltipTemplate,
            '<p class="title">'+ Datas.find('.hover-data-name').html()+'</p>'
            +'<p class="start-date"><?php __('Start Date'); ?>&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-start').html()+'</span></p>'
            +'<p class="end-date"><?php __('End Date'); ?>&nbsp;&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-end').html()+'</span></p>'
            + _html_progress
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
	$('.gantt_clears').remove();
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
	function initresizable(){
        var _max_height = 0;
        $('#GanttChartDIV .gantt-chart-wrapper >.gantt-primary >tbody >tr').each(function(){
            _max_height += $(this).is(":visible") ? $(this).height() : 0;
        });
        _min_height = Math.min(100,_max_height);
        if( _max_height < 235) $('#mcs1_container').css('height', _max_height);
        $('#mcs1_container').resizable({
            handles: "s",
            maxHeight: _max_height,
            minHeight: _min_height ,
            resize: function(e, ui){
                var _max_height = 0;
                $('#GanttChartDIV .gantt-chart-wrapper >.gantt-primary >tbody >tr').each(function(){
                    _max_height += $(this).is(":visible") ? $(this).height() : 0;
                });
                _min_height = Math.min(235,_max_height);
                 $('#mcs1_container').resizable("option", 'maxHeight', _max_height);

            }
        });
        $(window).trigger('resize');

    }
    function destroyresizable(){
        $('#mcs1_container').resizable("destroy");
        $('#mcs1_container').css({
            width: '',
            height:''
        });
    }
    // initresizable();

    // fix milestone chong len nhau
    function fix_milestone(){
       
		$('#GanttChartDIV .gantt-chart-wrapper .gantt-ms .gantt-line').height(48);
        
    } 
	setTimeout( function(){
		fix_milestone();
		$(window).trigger('resize');
	}, 1500);
    // fix_milestone();
	// $(window).ready(function(){
        // fix_milestone();
    // });

</script>