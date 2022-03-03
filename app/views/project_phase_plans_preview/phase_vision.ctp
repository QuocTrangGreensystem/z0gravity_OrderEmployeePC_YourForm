<?php
echo $html->css('jquery.dataTables');
echo $html->css('gantt_v2_1');
echo $html->script(array('html2canvas', 'jquery.html2canvas_v2'));
echo $html->css('jquery.mCustomScrollbar');
echo $html->script(array('jquery.easing.1.3',  'jquery.mCustomScrollbar'));
echo $html->script('draw-progress');
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
echo $html->css('preview/project_task_gantt');
echo $html->css('preview/project_task');
$language = Configure::read('Config.language');
?>


<style type="text/css">
    #gantt-display{
        overflow: hidden;
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
    .gantt-month span {
        margin-top: -16px;
        margin-left: 5px;
    }
    .wd-tab .wd-panel,.wd-tab .wd-content{
        padding: 0;
    }
    .wd-title{
        margin-bottom: 20px;
		overflow: visible;
    }
    #mcs1_container table.gantt .gantt-num {
        display: table-row;
    }
    body{
        overflow: inherit;
    }
	.wd-title a.btn{
		width: 40px;
	}
	.wd-title a.btn, .wd-title select{
		vertical-align: top;
		box-sizing: border-box;
		height: 40px;
		line-height: 38px;
		padding: 0;
		border: 1px solid #E1E6E8;
		color: #666;
	}
	#DisplayPhaseVisionForm{
		padding-bottom: 0;
	}
	div.checker span{
		box-sizing: border-box;
		width: 20px;
		height: 20px;
	}
	div.checker span:before{
		top: 3px;
		left: 3px;
		box-sizing: border-box;
	}
	.wd-title select{
		padding-left: 7px;
		padding-right: 20px;
	}
	.btn.btn-table-collapse{
		position: inherit;
	}
	.wd-title select{
		background: url(/img/new-icon/down.png) no-repeat 95% center #fff;
		-webkit-appearance: none;
		-moz-appearance: none;
		-ms-appearance: none;
		-o-appearance: none;
		appearance: none;
	}
	.wd-title select::-ms-expand{
		display: none;
	}
	.wd-dropdown{
		vertical-align: top;
	}
	body #layout{
		background: #f2f5f7;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
             <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab">
                <?php
                if (isset($view_id))
                    echo $this->element('project_tab_view');
                    ?>
                <div class="wd-panel">
                    <div class="wd-title">
						<div class="gantt-switch">
							<div id="gantt-display">
								<?php
								$displayplan = $display = 1;
								$chk = ($displayplan == 1) ? true : false;
								$chkreal = ($display == 1) ? true : false;
								?>
								<div class="wd-check-box">
									<label class="ck-initial"><div class="checker" id="initial-schedule"><span class="checkbox <?php echo $chk ? 'checked' : ''; ?>"><input type="hidden" rel="no-history" name="displayplan" id="displayplan" class="checkbox" value="<?php echo intval($chk);?>" style="opacity: 0;" onchange="removeLine(this,'n')"></span></div><p class="ck-title">
									<?php 
										if(!empty($lanPhase)){
											if($language == 'eng'){
												echo __('Initial schedule');
												echo ' '.'('.$lanPhase['Menu']['name_eng'].')';
											} else{
												echo __('Initial schedule');
												echo ' '.'('.$lanPhase['Menu']['name_fre'].')';
											}
										}else{
											__('Initial schedule');
										}
									?></p></label>
								</div>
								<div class="wd-check-box">
									<label><div class="checker" id="real-time"><span class="checkbox <?php echo $chkreal ? 'checked' : ''; ?>"><input type="hidden" rel="no-history" name="displayreal" id="displayreal" class="checkbox" value="<?php echo intval($chkreal);?>" style="opacity: 0;" onchange="removeLine(this,'s')"></span></div><p class="ck-title">
									<?php
										if(!empty($lanTask)){
											if($language == 'eng'){
												echo __('Real Time');
												echo ' '.'('.$lanTask['Menu']['name_eng'].')';
											} else{
												echo __('Real Time');
												echo ' '.'('.$lanTask['Menu']['name_fre'].')';
											}
										}else{
											__('Real Time');
										}
									?></p></label>
								</div>
							</div>
							
						</div>
                        <a href="#" onclick="SubmitDataExport();return false;" class="btn export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                        <a href="javascript:void(0);" id="hide_all" class="btn btn-text btn-add" style="display: none;">
                            <i class='icon-minus icons'></i>
                        </a>
                        <a href="javascript:void(0);" id="display_all" class="btn btn-add">
                            <i class='icon-plus icons'></i>
                        </a>
                        <a href="javascript:void(0);" onclick="expandTaskScreen();" class="btn btn-fullscreen hide-on-mobile" id="expand"></a>
                        <a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapseTaskScreen();" title="Collapse Tasks Screen" style="display: none;"></a>
						<?php 
							$stones = array();
							$start = $end = 0;
							$rows = 3;
							if (!empty($projectName['ProjectMilestone'])) {
								foreach ($projectName['ProjectMilestone'] as $p) {
									if( empty($p['milestone_date']) || ($p['milestone_date'] =='0000-00-00')) continue;
									$_start = strtotime($p['milestone_date']);
									if (!$start || $_start < $start) {
										$start = $_start;
									} elseif (!$end || $_start > $end) {
										$end = $_start;
									}
									$stones[$p['part_id'] ? $p['part_id'] : 0][] = array($_start, $p['project_milestone'], $p['validated']);
								}
							}
						 ?>
						 
						 <?php $gantt_views = array(
							'date' => __('View by Date', true),
							'week' => __('View by Week', true),
							'month' => __('View by Month', true),
							'year' => __('View by Year', true),
							'2years' => sprintf( __('View by %s Years', true), 2),
							'3years' => sprintf( __('View by %s Years', true), 3),
							'4years' => sprintf(__('View by %s Years', true), 4),
							'5years' => sprintf(__('View by %s Years', true), 5),
							'10years' => sprintf(__('View by %s Years', true), 10),
						); ?>
						 
						<div class="wd-dropdown">
							<span class="selected">
								<?php echo $gantt_views[$type];?>
							</span>
							<span class="wd-caret"></span>
							<ul class="popup-dropdown">
								<li>
									 <?php echo $this->Html->link(__('Date', true), Set::merge($arg, array('?' => array('type' => 'date'))), array('class' => $type == 'date' ? 'gantt-switch-current active' : '', 'data-text' =>  $gantt_views['date']));;?>
								</li>
								<li>
									 <?php echo $this->Html->link(__('Week', true), Set::merge($arg, array('?' => array('type' => 'week'))), array('class' => $type == 'week' ? 'gantt-switch-current active' : '', 'data-text' =>  $gantt_views['week'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('Month', true), Set::merge($arg, array('?' => array('type' => 'month'))), array('class' => $type == 'month' ? 'gantt-switch-current active' : '', 'data-text' =>  $gantt_views['month'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('Year', true), Set::merge($arg, array('?' => array('type' => 'year'))), array('class' => $type == 'year' ? 'gantt-switch-current active' : '', 'data-text' =>  $gantt_views['year'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('2 Years', true), Set::merge($arg, array('?' => array('type' => '2years'))), array('class' => ($type == '2years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden', 'data-text' =>  $gantt_views['2years'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('3 Years', true), Set::merge($arg, array('?' => array('type' => '3years'))), array('class' => ($type == '3years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden', 'data-text' =>  $gantt_views['3years'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('4 Years', true), Set::merge($arg, array('?' => array('type' => '4years'))), array('class' => ($type == '4years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden', 'data-text' =>  $gantt_views['4years'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('5 Years', true), Set::merge($arg, array('?' => array('type' => '5years'))), array('class' => ($type == '5years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden', 'data-text' =>  $gantt_views['5years'])); ?>
								</li>
								<li>
									<?php echo $this->Html->link(__('10 Years', true), Set::merge($arg, array('?' => array('type' => '10years'))), array('class' => ($type == '10years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden', 'data-text' =>  $gantt_views['10years'])); ?>
								</li>
							</ul>
						</div>
						
                    </div>
                    <div class="wd-section" id="wd-fragment-1">
                        <div class="wd-content">
                            <div id="GanttChartDIV">
                             <?php
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
                                            $projectTask['rstart'] = !empty($projectTask['rstart']) && $projectTask['rstart']>0 ? $projectTask['rstart'] : $_phase['start'];
                                            if($_phase['id'] == $projectTask['project_part_id']){
                                                if(!empty($projectTask['children'])){
                                                    foreach($projectTask['children'] as $k => $vl){
                                                        $projectTask['children'][$k]['color'] = $projectTask['color'];
                                                        $projectTask['children'][$k]['start'] = !empty($vl['start']) && $vl['start']>0 ? $vl['start'] : $projectTask['start'];
                                                        $projectTask['children'][$k]['rstart'] = !empty($vl['rstart']) && $vl['rstart']>0 ? $vl['rstart'] : $projectTask['rstart'];
                                                        $projectTask['children'][$k]['end'] = !empty($vl['end']) && $vl['end']>0 ? $vl['end'] : $projectTask['start'];
                                                        $projectTask['children'][$k]['rend'] = !empty($vl['rend']) && $vl['rend']>0 ? $vl['rend'] : $projectTask['rend'];
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
									
                                    $this->GanttV2Preview->create($type, $start, $end, isset($stones[0]) ? $stones[0] : array(), false);
                                    $line = array();
									$nodes['empty'] = array();
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
                                                        , $child['end'], $child['rstart'], $child['rend'], $child['color'], 'child' , $child['completed'], $child['assign']);
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
	var project_id = <?php echo json_encode($projectName['Project']['id']); ?>;
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
    function collapseTaskScreen() {
        $('#table-collapse').hide();
        $('#expand').show();
        $('.wd-panel').removeClass('treeExpand');
        $('.body').removeClass('is_treeExpand');
        isFull = false;
        $(window).trigger('resize');
    }

    function expandTaskScreen() {
        $('.wd-panel').addClass('treeExpand');
        $('.body').addClass('is_treeExpand');
        $('#table-collapse').show();
        $('#expand').hide();
        isFull = true;
        $(window).trigger('resize');
    }
     function initresizable(){
        var _max_height = 0;
        $('#GanttChartDIV .gantt-chart-wrapper >.gantt-primary >tbody >tr').each(function(){
            _max_height += $(this).is(":visible") ? $(this).height() : 0;
        });
        _max_height += 15;
        $('#mcs1_container').resizable({
            handles: "s",
            maxHeight: _max_height,
            minHeight: 235 ,
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
                            $('#display_all').css('display', 'inline-block');
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
                            $('#display_all').css('display', 'inline-block');
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
        var initDate = Datas.find('.hover-data-comp').html();
        var class_pro ='';
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
    $('.wd-check-box').on('click', function(){
        var _sp = $(this).find('span');
        _sp.toggleClass('checked');
        $(this).find('input.checkbox').val( parseInt(_sp.hasClass('checked') ? '1' : '0'));

        $(this).find('input.checkbox').trigger('onchange');
    });
	$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
    function removeLine(checkboxObject,type){
        if($(checkboxObject).val() == 1){
            if(type=="n"){
                $('.gantt-line-desc').removeClass('padding-line');
				$('.gantt-line-n').show();
                $('.caseline-n').show();
				if($('#displayreal').val() == 1) $('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
            };
            if(type=="s"){
				$('.gantt-line-s').show();
                $('.caseline-s').show();
				$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
            };
			
        }else{
            if(type=="n"){
                if(!$('#displayreal').attr("checked")){
					$('.gantt-line-n').hide();
                    $('.caseline-n').hide();
					if($('#displayreal').val() == 1)  $('.gantt-line-desc.gantt-line-s').show();
                }
            }
            if(type=="s"){
                if(!$('#displayplan').attr("checked")){
					$('.gantt-line-s').hide();
                    $('.caseline-s').hide();
					// if($('#displayplan').val() == 1)  $('.gantt-line-desc.gantt-line-s').show();
                }
            }
        }
	}
	function changeGantDisplay(elm){
		 var selected = $(elm).find("option:selected").val();
		 $('#GanttChartDIV').addClass('loading');
		 if(selected){
			 $.ajax({
				type: "POST",
				url: '/project_phase_plans_preview/saveHistoryByAjax/'+ selected,
				data: {},
				success: function(_result){
					console.log(_result);
				}
			});
			 $.ajax({
				type: "POST",
				url: '/project_amrs_preview/wd_project_gantt/'+ project_id + '/'+ selected,
				data: {},
				success: function(_respon){
					dump = _respon;
					if(_respon){
						$('#GanttChartDIV').html(_respon).removeClass('loading');
					}
				}
			});
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
		var gant_line = $('#mcs1_container').find('.gantt-line');
		$.each(gant_line, function(i, e){
			var line_s = $(e).find('.gantt-line-s').length;
			var line_n = $(e).find('.gantt-line-n').length;
			if((line_n == 0 && line_s > 0) || (line_n > 0 && line_s == 0) ){
				$(e).addClass('gantt-one-line');
			}else if(line_n == 0 && line_s == 0){
				$(e).addClass('gantt-no-line');
			}
		});
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
