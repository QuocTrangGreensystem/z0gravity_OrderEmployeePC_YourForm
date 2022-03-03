<?php echo $html->script('draw-progress');
echo $html->css(array('gantt_v2.css?ver=1.3','project_staffing_visions')); 
$this->GanttV3Preview->setTextDomain($_domain);
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'month';
$line = array();
$displayTeamPlus = !empty($companyConfigs['display_staffing_team_plus']) ? $companyConfigs['display_staffing_team_plus'] : 0;
$showAllPicture = !empty($companyConfigs['display_picture_all_resource']) ? $companyConfigs['display_picture_all_resource'] : 0;?>
<!--[if lt IE 9]>
<style>.fixed-height{ height:23px !important;}</style>
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
<style>
.fixed-height{ height:22px !important;}
/*ADD CODE BY VINGUYEN 06/08/2014*/
.gantt-title.gantt-head td div{
    width:60px !important;
}
.gantt-title.gantt-head .gantt-name div{
    width:220px !important;
    padding:0 !important;
}
.gantt-node.gantt-child .gantt-name{
    width:200px !important;
}
.gantt-title.gantt-head .gantt-func div{
    width:156px !important;
    padding:0 !important;
}
.gantt-line-p, .gantt-line-s, .gantt-line-n{
    top: 15px !important;
}
.gantt-line-desc{
    top: 6px !important;
}
.gantt-staff .gantt-node td,
.gantt-staff .gantt-node .gantt-summary td {
    background: #f0f0f0;
}
.gantt-month span{
    margin-top: -15px;
    margin-left: 3px;
}
.gantt-group {
    border-top: 1px solid #0cb0e0 !important;
}
.gantt-staff .gantt-node .gantt-group td,
.gantt-staff .gantt-node td.gantt-group {
    background-color: #e0e0e0;
}
.gantt-group-end .fixedHeightStaffing td {
    border-bottom: 1px solid #0cb0e0 !important;
}
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
#mcs1_container{
	border-right: none;
}
#mcs1_container .gantt-content-wrapper tr.gantt-staff .gantt-node td{
	border-top-width: 1px;
}
#mcs1_container .gantt-chart-wrapper tr.gantt-staff  .gantt-node .gantt-title.gantt-head + tr td{
	border-top-width: 0px;
}
div#gantt-budget-id {
	white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    padding: 0;
    width: 76px !important;
}
#layout{
	background-color: #f2f5f7;
}
.gantt-side .gantt-list{
	border-left: 0;
}
.wd-layout > .wd-main-content > .wd-tab > .wd-panel{
	padding-bottom: 20px;
	padding-right: 20px;
}
#wd-fragment-1{
	padding-right: 20px;
	overflow: auto;
}
</style>
<?php
echo $html->css('jquery.mCustomScrollbar');
echo $html->css('preview/project_staffings');
echo $html->script(array('html2canvas.js?ver=1.3', 'jquery.html2canvas_v2.js?ver=1.3'));
echo $html->script(array('jquery.easing.1.3', 'jquery.mCustomScrollbar'));
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <div class="wd-panel">

                    <?php  if (isset($view_id))  echo $this->element('project_tab_view'); 
					echo $this->Form->create('Display', array('type' => 'get', 'url' => array_merge(array(
							'controller' => 'project_staffings',
							'action' => 'visions', $projectName['Project']['id']
						))));
					?>
                    <div class="wd-title">
                        <!-- <a href="javascript:void(0)" class="wd-add-project" id="import_CSV" style="margin-right:5px; display: none;"><span><?php //__('Import CSV') ?></span></a> -->
						<?php
						$_options = array(
							'employee' => __("Employees", true),
							'profit' => __("Profit Centers", true),
							'profit_plus' => __("Profit Centers+", true),
						);
						// if( !empty($companyConfigs['activate_profile'])) $_options['profile'] = __("Profile", true);
						echo $this->Form->input('category', array(
							'rel' => 'no-history',
							'type' => 'select',
							'options' => $_options,
							'selected' => $staffingCate,
							'class' => 'wd-customs',
							'id' => 'CategoryCategory',
							'label' => false
						));
						unset( $_options);
						?>
						<a href="#" id="export-staffing" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
						<?php 
						if( $staffingCate == 'employee' || $staffingCate == 'profit'){
							echo $this->Form->input('show_resource_overload', array(
								'rel' => 'no-history',
								'type' => 'checkbox',
								// 'label' => __('Display team(s) or resource(s) with overload',true),
								'label' => false,
								'checked'=> false,
								'id' => 'ShowOnlyResourceOverload',
								'div' => array(
									'class' => 'wd-input wd-checkbox-switch wd_custom_color',
									'title' => __('Display team(s) or resource(s) with overload', true),
									),
								'type' => 'checkbox', 
							));
						}
						?>
                        <div>
                            <?php echo $this->element("checkStaffingBuilding") ?>
                            <div id="gantt-display">
                                <?php
                                    $display = 0;
                                    echo $this->Form->input('display', array(
                                        'onchange' =>'removeLine(this);',
                                        'value' => $display,
                                        'options' => array(
                                            //__('Initial schedule', true),
                                            //__('Real Time', true)
                                        ),
                                        'type' => 'radio', 'legend' => false, 'fieldset' => false
                                    ));
                                 ?>

                            </div>

                        </div>
                    </div>
					<?php echo $this->Form->end(); ?>
                    <div class="wd-section " id="wd-fragment-1">
                        <?php echo $this->Session->flash(); ?>
                        <div class="wdcontent">
                            <h2 class="vision-project wd-hide" style="color: orange"><?php echo sprintf(__('%s', true), $projectName['Project']['project_name']); ?></h2>
                            <p><?php //echo  __('Workload(*) = Workload + Overload', true);?></p>
                            <div id="GanttChartDIV" class="<?php echo $staffingCate; ?>">

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
                                        $stones[] = array($_start, $p['project_milestone'], $p['validated']);
                                    }
                                }

                                $unsetFields = array(
                                    'rstart' => 'phase_real_start_date',
                                    'rend' => 'phase_real_end_date'
                                );
                                $convtFields = array(
                                    'start' => 'phase_real_start_date',
                                    'end' => 'phase_real_end_date',
                                );
                                $convtFields = array_merge($convtFields, $unsetFields);
                                $unsetFields = array();



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
                                        $_phaseDate = $this->GanttV3Preview->toTime($phasePlan['ProjectPhasePlan'][$field]);
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
                                if(!empty($minMaxDate)){
                                    if($minMaxDate['min_date'] <= $start){
                                        $start = $minMaxDate['min_date'];
                                    }
                                    if($minMaxDate['max_date'] >= $end){
                                        $end = $minMaxDate['max_date'];
                                    }
                                }
                                if(!empty($minMax)){
                                    $min = !empty($minMax['min_date']) ? strtotime($minMax['min_date']) : 0;
                                    $max = !empty($minMax['max_date']) ? strtotime($minMax['max_date']) : 0;
                                    if($min <= $start){
                                        $start = $min;
                                    }
                                    if($max >= $end){
                                        $end = $max;
                                    }
                                }
                                if(!empty($staffingss)){
                                    $dates = Set::classicExtract($staffingss, '{n}.data.{n}.date');
                                    $rDates = array();
                                    foreach($dates as $date){
                                        foreach($date as $_date){
                                            $rDates[] = $_date;
                                        }
                                    }
                                    $rDates = !empty($rDates) ? array_unique($rDates) : array();
                                    $minDateStaffings = !empty($rDates) ? min($rDates) : time();
                                    if(!empty($minDateStaffings)){
                                        if($minDateStaffings <= $start){
                                            $start = $minDateStaffings;
                                        }
                                    }
                                    $maxDateStaffings = !empty($rDates) ? max($rDates) : time();
                                    if(!empty($minDateStaffings)){
                                        if($maxDateStaffings >= $end){
                                            $end = $maxDateStaffings;
                                        }
                                    }
                                }
                                if(isset($startDateFilter) && $start > $startDateFilter){
                                    $start = $startDateFilter;
                                }
                                if( isset($endDateFilter) && $end < $endDateFilter){
                                    $end = $endDateFilter;
                                }
                                // pr(date('Y-m-d', $start));
                                // pr(date('Y-m-d', $end));
                                //pr($nodes);
                                // exit();
                                unset($phasePlans, $phasePlan);
                                if (empty($start) || empty($end)) {
                                    echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true));
                                } else {
                                    // if( $staffingCate != 'profit_plus' ){

                                    // }
                                    $this->GanttV3Preview->create($type, $start, $end, $staffingCate == 'profit_plus' ? array() : $stones, true, 'd-m-Y', $staffingCate == 'profit_plus');
                                    if( $staffingCate != 'profit_plus' ){
                                        $line = array();
                                        foreach ($nodes as $node) {
                                            $rows++;
                                            $this->GanttV3Preview->draw($node['id'], $node['name']
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
                                                    $this->GanttV3Preview->draw($child['id'], $child['name']
                                                                    , $child['predecessor'], $child['start']
                                                                    , $child['end'], $child['start'], $child['end'], $child['color'], 'child-child', $child['completed'], $child['assign']);

                                                     if(!empty($child['children'])){
                                                        foreach ($child['children'] as $child) {
                                                            $rows++;
                                                            $this->GanttV3Preview->draw($child['id'], $child['name']
                                                                    , $child['predecessor'], $child['start']
                                                                    , $child['end'], $child['start'], $child['end'], $child['color'], 'child-child', $child['completed'], $child['assign']);
                                                             if(!empty($child['children'])){
                                                                foreach ($child['children'] as $child) {
                                                                    $rows++;

                                                                    $this->GanttV3Preview->draw($child['id'], $child['name']
                                                                            , $child['predecessor'], $child['start']
                                                                            , $child['end'], $child['start'], $child['end'], $child['color'], 'child-child', $child['completed'], $child['assign']);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
									//comment theo y/c 2 ticket 585
									// if( !empty( $staffingss)){
										// $lastID = count($staffingss) - 1;
										// $nE = $staffingss[$lastID];
										// unset($staffingss[$lastID]);
										// $staffingss = Set::sort( $staffingss, '{n}.name', 'asc');
										// $staffingss[] = $nE;
									// }
									//end ticket 585
                                    echo $this->Html->scriptBlock('GanttData = ' . $this->GanttV3Preview->drawStaffing2($staffingss, $employee_info, $budgetMdTeams, $freezeTeams, $staffingCate != 'profit_plus', $showType, $displayTeamPlus, $staffingCate == 'profile', $showAllPicture, $staffingCate == 'profit_plus'));

                                    if (empty($staffingss)) {
                                        echo $this->Html->tag('h1', __('No data exist to create staffing', true), array('style' => 'color:red'));
                                    }
                                    $this->GanttV3Preview->end();
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
<?php

echo $this->Form->create('Export', array('url' => array_merge($arg, array('controller' => 'project_staffings', 'action' => 'export_visions')), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('project', array('value' => $projectName['Project']['project_name']));
echo $this->Form->hidden('rows', array('value' => $rows));
echo $this->Form->hidden('start', array('value' => $start));
echo $this->Form->hidden('end', array('value' => $end));
echo $this->Form->hidden('project_id', array('value' => $project_id));
echo $this->Form->hidden('months', array('value' => serialize($this->GanttV3Preview->getMonths())));
echo $this->Form->hidden('displayFields', array('value' => '0'));
echo $this->Form->end();
?>
<?php
$i18n = array(
    'Budget M.D' => __('Budget M.D', true),
	'view_all' => __('Display All', true),
	'view_overload' => __('Display team(s) or resource(s) with overload', true),
);
echo $this->element('dialog_projects');
$_start = date('Y', $start);
$year = range($_start, $_start + (date('Y', $end) - $_start));
?>
<!-- Dialog Export -->
<div id="dialog_vision_export" class="buttons" title="<?php __("Display Fields") ?>" style="display: none;">
    <fieldset>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="input">
                <?php
                $options = array();
                if($showType == 0){ // Employee
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload(*)', true),
                        'consumed' => __('Consumed', true),
                        'capacity' => __('Capacity', true),
                        'absence' => __('Absence', true),
                        'totalWorkload' => __('Total Workload', true),
                        'assignEm' => __('% Assigned to employee', true)
                    );
                } elseif( $showType == 1){ // PC
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload(*)', true),
                        'consumed' => __('Consumed', true),
                        'capacity' => __('Capacity', true),
                        'absence' => __('Absence', true),
                        'totalWorkload' => __('Total Workload', true),
                        'assignPc' => __('% Assigned to profit center', true)
                    );
                } elseif( $showType == 3 ){ // PC +
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload(*)', true),
                        'consumed' => __('Consumed', true),
                    );
                } else { //profile/function 
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload(*)', true),
                        'consumed' => __('Consumed', true),
                        'remains' => __('remains', true)
                    );
                }
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
                    "options" => $options
                ));
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

<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV file" class="buttons" style="display: none;">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'project_staffings', 'action' => 'import', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" rel="no-history" />
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
<script type="text/javascript">
// var wdTable = $('.wd-panel');
var wdTable = $('#wd-fragment-1');
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
    $('.gantt-line-s').hide();
    $('.caseline-s').hide();
    var isPlus = <?php echo json_encode($staffingCate == 'profit_plus') ?>;
    var line = <?php echo json_encode($line)?>;
    var i18n = <?php echo json_encode($i18n)?>;
    //$.each(line, function(i, v){
        //$('#line-n-'+i).css('border','1px dashed' + v);
    //});
    var $onClickPhaseIds = <?php echo json_encode($onClickPhaseIds); ?>;
    $.each($onClickPhaseIds, function(index, values){
        $.each(values, function(key, val){
            $('.wd-'+val).css('display', 'none');
            $('.line-'+val).css('display', 'none');
        });
    });
	$('.gantt_clears').remove();
    $('.gantt-primary tr').toggle(function(){
        var classPhase = $(this).attr("class") ? $(this).attr("class").split(' ')[1] : '';
        if(classPhase != ''){
            if($onClickPhaseIds[classPhase]){
                $.each($onClickPhaseIds[classPhase], function(index, value){
                    $('.wd-'+value).fadeToggle(1000);
                    $('.line-'+value).fadeToggle(1000);
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            $('.wd-'+$vl).slideUp();
                            $('.line-'+$vl).slideUp();
                        });
                    }
                });
            }
        }
        $('.gantt_clears').remove();
        Gantt.rdraw();
    }, function(){
        var classPhase = $(this).attr("class") ? $(this).attr("class").split(' ')[1] : '';
		console.log(classPhase);
        if(classPhase != ''){
            if($onClickPhaseIds[classPhase]){
                $.each($onClickPhaseIds[classPhase], function(index, value){
                    $('.wd-'+value).fadeToggle(200);
                    $('.wd-'+value).hide();
                    $('.line-'+value).fadeToggle(200);
                    $('.line-'+value).hide();
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            $('.wd-'+$vl).slideUp();
                            $('.line-'+$vl).slideUp();
                        });
                    }
                });
            }
        }
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });
    function removeLine(current){
        if($(current).val() == 0){
            $('.gantt-line-n').show();
            $('.gantt-line-desc').show();
            $('.caseline-n').show();
            $('.gantt-line-s').hide();
            //$('.gantt-line-desc').hide();
            $('.caseline-s').hide();
        }else{
            $('.gantt-line-n').hide();
            $('.caseline-n').hide();
            $('.gantt-line-s').show();
            $('.caseline-s').show();
        }
        Gantt.rdraw();
    }
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
                var dragger_container = $('.dragger_container:visible');
                $("#mcs1_container .container").css('left', '0px');
                dragger_container.children(".dragger.ui-draggable").css('left', '0px');
                $('#GanttChartDIV').html2canvas();
            }
        });
        $('#DisplayFields0').click(function(){
            $(this).closest('.input').find('input').prop('checked' , $(this).is(':checked'));
        });
        $('#DisplayFields0').attr('checked', 'checked');
        if($('#DisplayFields0').is(':checked')){
            $('#DisplayFieldsValidated').attr('checked', 'checked');
            $('#DisplayFieldsConsumed').attr('checked', 'checked');
            $('#DisplayFieldsAbsence').attr('checked', 'checked');
            $('#DisplayFieldsRemains').attr('checked', 'checked');
            $('#DisplayFieldsCapacity').attr('checked', 'checked');
            $('#DisplayFieldsTotalWorkload').attr('checked', 'checked');
            $('#DisplayFieldsAssignEm').attr('checked', 'checked');
            $('#DisplayFieldsAssignTo').attr('checked', 'checked');
            $('#DisplayFieldsAssignPc').attr('checked', 'checked');
            //$(this).closest('.input').find('input').prop('checked' , $(this).is(':checked'));
        }
    }

    (function($){

        $(document).ready(function(){
			setHeightContentHead();
            function resizeScroll(){
                var today = new Date('<?php echo date('Y-m-d') ?>');

                var type = 'month';
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
                    var ratio = ( $("#mcs_container .container").width() + $col.position().left ) / container.width();
                    if( ratio > 1 )ratio = 1;
                    var left = 0 - Math.round(ratio * max);
                    var scroll = Math.round(ratio * (dragger_container.width() - dragger_container.children(".dragger.ui-draggable").width()));
                    $("#mcs1_container .container").css('left', '0px');
                    dragger_container.children(".dragger.ui-draggable").css('left', '0px');
                    var _width = $('#mcs1_container').width()-4;
                    var _right = $(window).width() - parseInt( $('#mcs1_container').width() + $('#mcs1_container').offset().left);
                    $(dragger_container).css({'position': 'fixed', 'left': 'auto', 'right': _right, 'width': _width});
                }
            }
            resizeScroll();
            $(window).trigger('resize');
            $(window).resize(function(){
                resizeScroll();
            });
            // $('.wd-panel').scroll(function(){
            //     resizeScroll();
            // });
            $.ajaxSetup({
                cache: false
            });
            var na = '<?php echo $this->GanttSt->na; ?>';
            var yl = <?php echo json_encode($year); ?>;
            var url = '<?php echo $this->Html->url(array('action' => 'update_staffing_demo', $project_id)); ?>';
            var employees = '<?php echo $this->Html->url(array('action' => 'employees')); ?>';
            var regNum = /^(([\-1-9][0-9]{0,2})|(0))(\.[0-9]{0,1})?$/;
            /**
             * Auto identify
             *
             * @param prefix
             *
             * return identify
             */
            $.fn.identify = function(prefix) {
                var i = 0,id='';
                this.each(function() {
                    id = $(this).attr('id');
                    if(id) return false;
                    do {
                        i++;
                        id = prefix + '_' + i;
                    } while($('#' + id).length > 0);
                    $(this).attr('id', id);
                });
                return id;
            };
            /**
             * Convert to float
             *
             * @param val
             * @param dp
             *
             * return mixed na or float number
             */
            var toFloat = function (val,dp){
                //if(dp && (val.length == 0 || val == na || !regNum.test(val))){
//                    return na;
//                }
                val =  Number(parseFloat(val || '0').toFixed(1));
                if(val < 0){
                    val = 0;
                }
                return !regNum.test(val)  ? 0 : val;
            }
            /**
             * Get input collections
             *
             * @param $list
             * @param rel
             * @param node
             *
             * return object list input
             */
            var getInput = function($list,rel,node){
                return {
                    e:$list.find('[rel="e-'+rel+'"] '+node),
                    v:$list.find('[rel="v-'+rel+'"] '+node),
                    c:$list.find('[rel="c-'+rel+'"] '+node),
                    r:$list.find('[rel="r-'+rel+'"] '+node),
                    f:$list.find('[rel="f-'+rel+'"] '+node)
                };
            }
            /**
             * Set forecast highlight
             *
             * @param $list
             * @param method
             *
             * return object list input
             */
            var setHighlight = function($input, method){
                var kclass = '';
                if(Number($input.f[method]()) > Number($input.v[method]())){
                    kclass = 'gantt-invalid';
                }
                $input.f.closest('td').removeClass('gantt-invalid').addClass(kclass);
            }
            /**
             * Synchronous vertical data
             *
             * @param $list
             * @param val
             * @param rel
             *
             * return object list input
             */
            var syncVertical = function($list,rel){
                var $input = getInput($list,rel,'input');
                var $sum = getInput($('.gantt-chart').find('[rel="summary"]'),rel,'div');
                var val,val2;

                var forecast = 0;
                if(toFloat($input.c.val() , true) != na){
                    forecast = Number($input.c.val()) + Number($input.r.val());
                }else{
                    forecast = Number($input.r.val());
                }
                $input.f.val(forecast);

                $.each($input , function(k,$v){
                    val = getIncrease.call($v,rel);
                    val2 = toFloat($sum[k].html() , true);

                    if(val2 == na && val != na){
                        val2 = 0;
                    }else if(val2 != na && val == na){
                        val = 0;
                    }

                    $sum[k].html(toFloat(val2 + val));
                    if(k == 'f'){
                        return true;
                    }

                    val = toFloat($v.val() , true);
                    if(val !=  na && val != 0){
                        $input[k].parent().addClass('gantt-unzero');
                    }else{
                        $v.val(k == 'c' && val != '0' ? na : 0);
                        $input[k].parent().removeClass('gantt-unzero');
                    }
                });

                setHighlight($input,'val');
                setHighlight($sum,'html');
                return $input;
            }
            /**
             * Synchronous horizontal data
             *
             * @param $list
             * @param $chart
             * @param rel
             * @param node
             *
             * return void
             */
            var syncHorizontal = function($list,$chart,rel,node){
                var c,v,has,
                method = node == 'input' ? 'val':'html',$input;

                $.each(['e','v','c','r','f'], function(undefined,k){
                    c = 0, has = false;
                    for(var i = 1; i<=12 ;i++){
                        v = $chart.find('[rel="'+k+'-'+rel+ '-' + i + '"] '+ node)[method]();

                        if(v != na){
                            has = true;
                        }
                        c += toFloat(v);
                    }
                    c = parseFloat(c).toFixed(1);
                    $list.find('[rel="'+k+'-'+rel+'"] div').html(!has ? na : c);

                    c = 0, has = false;
                    for(i = 0; i< yl.length ;i++){
                        v = $list.find('[rel="'+k+'-' + yl[i] + '"] div').html();
                        if(v !=  na){
                            has = true;
                        }
                        c += toFloat(v);
                    }
                    c = parseFloat(c).toFixed(1);
                    $list.find('[rel="'+k+'-total"] div').html(!has ? na : c);
                });
                setHighlight(getInput($list,rel,'div'),'html');
                setHighlight(getInput($list,'total','div'),'html');
            }
            /**
             * Update grid element event
             *
             * @param $element
             *
             * return void
             */
            var getIncrease =function(val){
                var val = this.data('_value'),
                _val = toFloat(this.val() , true);
                this.data('_value',_val);
                switch(true){
                    case (val == na && _val != na) :
                        val = _val;
                        break
                    case (val != na && _val == na) :
                        val = - val;
                        break
                    case (val != na && _val != na) :
                        val = _val - val;
                        break
                    default :
                        val = na;
                }
                return val;
            }
            /**
             * Update grid element event
             *
             * @param $element
             *
             * return void
             */
            var updateElement =function($element){
                var num = $element.closest('table').attr('rel');
                var check = $element.closest('table').attr('check');
                var rel = String($element.attr('rel')).split('-');
                var type = rel.shift();
                var year = rel[0];
                rel = rel.join('-');
                var $input = syncVertical($element.closest('.gantt-staff'),rel);
                // syncHorizontal($('.gantt-list').find('[rel="list-'+num+'"]'),$('.gantt-chart').find('[rel="'+num+'"]'),year,'input');
                syncHorizontal($('.gantt-list').find('[rel="list-summary"]'),$('.gantt-chart').find('[rel="summary"]'),year,'div');

                var consumed = toFloat($input.c.val() , true);
                if(consumed == na){
                    consumed = '';
                }
                $.ajax({
                    type:'POST',
                    url:url,
                    data:{
                        data:{
                            estimation:toFloat($input.e.val()),
                            //validated:toFloat($input.v.val()),
                            //consumed : consumed,
                            //remains:toFloat($input.r.val()),
                            date:rel+'-1',
                            name:num,
                            is_check: check
                        }
                    },
                    cache: false,
                    success:function(content){
                        //alert(content);
                    }
                });
            };
            /**
             * Update function employees grid element event
             *
             * @param type
             * @param data
             * @param $target
             * @param $element
             *
             * return void
             */
            var validateInput = function(type,rel,$target,$element){
                var value = Number(this.val());
                var validated = 100;
                this.removeClass('gantt-invalid');
                if(rel =='manday'){
                    validated = Number($element.find('[rel="'+type.charAt(0)+'-total"] div').html());
                }
                var max = 0;
                $target.find('[rel="'+type+'-'+rel+'"] input').not(this).each(function(){
                    max += Number($(this).val());
                });

                if(max+value > validated){
                    value = Math.max(0,validated - max);
                    alert('<?php echo h(__('The value is not to be greater than %1$s, valuable suggestion %2$s', true)); ?>'.replace('%1$s',validated).replace('%2$s',value));
                    this.addClass('gantt-invalid');
                    return false;
                }
                this.val(value);
                return true;
            }
            /**
             * Attach dialog employees grid element event
             *
             * return void
             */
            var attachDialog = function($list){
                var $showType = <?php echo json_encode($showType);?>;
                if($showType == false){
                    $list.find('table').not('.gantt-summary').find('.gantt-name div').click(function(){
                    var $element = $(this), identify = $element.identify('gantt-name');
                    var employee_name = $element.find('span').html();
                    var start = <?php echo json_encode($startEmployees);?>;
                    var end = <?php echo json_encode($endEmployees);?>;
                    var id_employee = $element.attr('rel') ? $element.attr('rel') : 0;
                    // ten employee
                    $(".gs-name-header span").html(employee_name);
                    $("#tb-popup-content .popup-header .employee_name").html(employee_name);
                    // chen title cua avai
                    var startDate = start[0] + '-' + start[1] + '-' + start[2];
                    var endDate = end[0] + '-' + end[1] + '-' + end[2];
                    //$(".gs-header-content-name span").html(inforTasks.name);
                    $(".gs-header-content-start span").html(startDate);
                    $(".gs-header-content-end span").html(endDate);
                    //$(".gs-header-content-work span").html(inforTasks.workload);
                    if(id_employee == 999999999){
                        return false;
                    }
                    // phan content
                    function getVocationDetail(){
                        var result = [];
                        var _startDate = start[2] + '-' + start[1] + '-' + start[0];
                        var _endDate = end[2] + '-' + end[1] + '-' + end[0];
                        $.ajax({
                            data : {data : {projectId : <?php echo $project_id; ?>}},
                            type : 'POST',
                            url : '/project_staffings/getVocationDetailByMonth/' + id_employee + '/' + _startDate + '/' + _endDate,
                            async: false,
                            dataTye: 'json',
                            success: function(data){
                                data = JSON.parse(data);
                                result = data;
                            },
                            error: function(message){
                            }
                        });
                        return result;
                    }
                    var datas = getVocationDetail();
                    var widthDivRight = 0;
                    function init(){
                        var headers = avais = vocs = work = '';
                        var totalCount = totalVacation = 0;
                        if(datas.vocation){
                            headers += '<tr>';
                            $.each(datas.vocation, function(index, values){
                                var count = 0;
                                $.each(values, function(ind, val){
                                    count++;
                                    totalCount++;
                                    totalVacation += parseFloat(val);
                                });
                                headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                            });
                            headers += '</tr><tr>';
                            avais += '<tr id="total-avai-popup">';
                            vocs += '<tr id="total-vocs-popup">';
                            work += '<tr id="total-workload-popup">';
                            $.each(datas.vocation, function(index, values){
                                $.each(values, function(ind, val){
                                    ind = ind.split('-');
                                    var keyWl = index+'-'+ind[1]+'-'+ind[0];
                                    ind = ind[0]+'-'+datas.dayMaps[ind[1]];
                                    widthDivRight += 50;
                                    headers += '<td><div class="text-center">' + ind + '</div></td>';
                                    var _vais = '';
                                    if(val == 1){
                                        _vais = 0;
                                    }
                                    avais += '<td><div id="avai-' + keyWl + '">' + _vais + '</div></td>';
                                    vocs += '<td><div id="vocs-' + keyWl + '">' + val + '</div></td>';
                                    work += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                                });
                            });
                            headers += '</tr>';
                            avais += '</tr>';
                            vocs += '</tr>';
                            work += '</tr>';
                        }
                        $(".popup-header-2").html(headers);
                        $(".popup-availa-2").html(avais);
                        $(".popup-vaication-2").html(vocs);
                        $(".popup-workload-2").html(work);

                        // phan detail cua task
                        var listTaskDisplay = '';
                        var valTaskDisplay = '';
                        var totalWorkload = [];
                        if(datas.dataDetail){
                            if(datas.dataDetail.project){
                                $.each(datas.dataDetail.project, function(project_id, values){
                                    var project_name = datas.groupNames.project[project_id] ? datas.groupNames.project[project_id] : '';
                                    listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">' + project_name + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                    valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                    var stt = 0;
                                    $.each(values, function(pTask_id, value){
                                        valTaskDisplay += '<tr>';
                                        stt++;
                                        var priorities = datas.priority.project[pTask_id] ? datas.priority.project[pTask_id] : '';
                                        var projectTaskName = datas.groupNameTasks.project[pTask_id] ? datas.groupNameTasks.project[pTask_id] : '';
                                        listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;&nbsp;'+ stt +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                        $.each(datas.vocation, function(index, values){
                                            $.each(values, function(ind, val){
                                                ind = ind.split('-');
                                                ind = index+'-'+ind[1]+'-'+ind[0];
                                                var _value = value[ind] ? value[ind] : 0;
                                                if(val == 1){
                                                    _value = 0;
                                                }
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[ind]){
                                                    totalWorkload[ind] = 0;
                                                }
                                                totalWorkload[ind] += _value;
                                            });
                                        });
                                        valTaskDisplay += '</tr>';
                                    });
                                });
                            }
                            if(datas.dataDetail.activity){
                                $.each(datas.dataDetail.activity, function(activity_id, values){
                                    var activity_name = datas.groupNames.activity[activity_id] ? datas.groupNames.activity[activity_id] : '';
                                    listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">' + activity_name + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                    valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                    var stt = 0;
                                    $.each(values, function(aTask_id, value){
                                        valTaskDisplay += '<tr>';
                                        stt++;
                                        var priorities = datas.priority.activity[aTask_id] ? datas.priority.activity[aTask_id] : '';
                                        var activityTaskName = datas.groupNameTasks.activity[aTask_id] ? datas.groupNameTasks.activity[aTask_id] : '';
                                        listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;&nbsp;'+ stt +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                        $.each(datas.vocation, function(index, values){
                                            $.each(values, function(ind, val){
                                                ind = ind.split('-');
                                                ind = index+'-'+ind[1]+'-'+ind[0];
                                                var _value = value[ind] ? value[ind] : 0;
                                                if(val == 1){
                                                    _value = 0;
                                                }
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[ind]){
                                                    totalWorkload[ind] = 0;
                                                }
                                                totalWorkload[ind] += _value;
                                            });
                                        });
                                        valTaskDisplay += '</tr>';
                                    });
                                });
                            }
                        }
                        var totalWorkloads = totalAvais = 0;
                        $('#total-workload-popup').find('td div').each(function(){
                            var getId = $(this).attr('id');
                            var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(3) : 0;
                            totalWorkloads += parseFloat(getTotalWl);
                            var getAvais = 1 - getTotalWl;
                            if (!isNaN(getAvais) && getAvais.toString().indexOf('.') != -1){
                                getAvais = getAvais.toFixed(3);
                            }
                            var vocs = $('#total-vocs-popup').find('#vocs-'+getId).html();
                            if(vocs == 1){
                                getAvais = 0;
                            }
                            totalAvais += parseFloat(getAvais);
                            $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                            $('#'+getId).html(getTotalWl);
                        });
                        totalWorkloads = totalWorkloads.toFixed(3);
                        totalAvais = totalAvais.toFixed(3);
                        if(totalAvais < 0){
                            totalAvais = 0;
                        }
                        $('#total-availability, .gs-header-content-avai span').html(totalAvais);
                        $('#total-vacation').html(totalVacation);
                        $('#total-workload').html(totalWorkloads);

                        $(".popup-task-detail").html(listTaskDisplay);
                        $(".popup-task-detail-2").html(valTaskDisplay);

                    }
                    function initMonth(){
                        $('.gs-popup-changeview li').removeClass('ch-current').find('a').removeClass('ch-current');
                        $('#filter_month').addClass('ch-current');
                        $('#filter_month').parent('li').addClass('ch-current');
                        $('#gs-popup-content').attr('class','month_view');
                        var headers = working = dayOff = capacity = avais = workload = overload = '';
                        var totalCount = totalWorkingDay = totalDayOff = totalCapacity = totalOverload = 0;
                        widthDivRight = 0;
                        if(datas.MonthVocations){
                            headers += '<tr>';
                            $.each(datas.MonthVocations, function(index, values){
                                var count = 0;
                                $.each(values, function(ind, val){
                                    count++;
                                    totalCount++;
                                    totalDayOff += parseFloat(val);
                                });
                                headers += '<td colspan="' + count + '" class="text-center year relative"><span class="abs-title-year">' + index + '</span></td>';
                            });
                            headers += '</tr><tr>';
                            working += '<tr id="total-working-popup">';
                            dayOff += '<tr id="total-dayOff-popup">';
                            capacity += '<tr id="total-capacity-popup">';
                            workload += '<tr id="total-workload-popup">';
                            avais += '<tr id="total-avai-popup">';
                            overload += '<tr id="total-over-popup">';

                            $.each(datas.MonthVocations, function(index, values){
                                if(values){
                                    $.each(values, function(ind, val){
                                        var keyWl = index+'-'+ind;
                                        var theWorking = datas.MonthWorkingDays[index][ind] ?  datas.MonthWorkingDays[index][ind] : 0;
                                        totalWorkingDay += parseFloat(theWorking);
                                        widthDivRight += 50;
                                        headers += '<td class="month"><div class="text-center">' + ind + '</div></td>';
                                        working += '<td><div id="working-' + keyWl + '">' + theWorking + '</div></td>';
                                        dayOff += '<td><div id="dayOff-' + keyWl + '">' + val + '</div></td>';
                                        var _capa = parseFloat(theWorking) - parseFloat(val);
                                        totalCapacity += parseFloat(_capa);
                                        capacity += '<td><div id="capacity-' + keyWl + '">' + _capa + '</div></td>';
                                        workload += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                                        avais += '<td><div id="avai-' + keyWl + '">' + 0 + '</div></td>';
                                        overload += '<td><div id="over-' + keyWl + '">' + 0 + '</div></td>';
                                    });
                                }
                            });
                            headers += '</tr>';
                            working += '</tr>';
                            dayOff += '</tr>';
                            capacity += '</tr>';
                            workload += '</tr>';
                            avais += '</tr>';
                            overload += '</tr>';
                        }
                        $(".popup-header-2").html(headers);
                        $(".popup-working-2").html(working);
                        $(".popup-dayOff-2").html(dayOff);
                        $(".popup-capacity-2").html(capacity);
                        $(".popup-workload-2").html(workload);
                        $(".popup-availa-2").html(avais);
                        $(".popup-over-2").html(overload);

                        // phan detail cua task
                        var listTaskDisplay = '';
                        var valTaskDisplay = '';
                        var totalWorkload = [];
                        var listSumFamily = [];
                        var totalFamily = [];
                        if(datas.listMonthDatas){
                            $.each(datas.listMonthDatas, function(idFamily, values){
                                var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                                listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                                //valTaskDisplay += '<tr class="family-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                valTaskDisplay += '<tr class="family-group">';
                                $.each(datas.MonthVocations, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = index+'-'+ind;
                                        valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+ind+'">&nbsp;</div></td>';
                                    });
                                });
                                valTaskDisplay += '</tr>';
                                var sttActivity = 0;


                                $.each(values, function(idGlobal, value){
                                    sttActivity++;
                                    idGlobal = idGlobal.split('-');
                                    if(idGlobal[0] === 'ac'){
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;

                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityActivityTasks[idTask] ? datas.PriorityActivityTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var activityTaskName = datas.NameActivityTasks[idTask] ? datas.NameActivityTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.MonthVocations, function(index, values){
                                                $.each(values, function(ind, val){
                                                    ind = index+'-'+ind;
                                                    var _value = valTask[ind] ? valTask[ind] : 0;
                                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                    if(!totalWorkload[ind]){
                                                        totalWorkload[ind] = 0;
                                                    }
                                                    totalWorkload[ind] += _value;
                                                    if(!listSumFamily[idFamily+'-'+ind]){
                                                        listSumFamily[idFamily+'-'+ind] = 0;
                                                    }
                                                    listSumFamily[idFamily+'-'+ind] += _value;
                                                    if(!totalFamily[idFamily]){
                                                        totalFamily[idFamily] = 0;
                                                    }
                                                    totalFamily[idFamily] += _value;
                                                });
                                            });
                                            valTaskDisplay += '</tr>';

                                        });
                                    } else if(idGlobal[0] === 'pr'){
                                        var projectName = datas.ListProjects[idGlobal[1]] ? datas.ListProjects[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;

                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityProjectTasks[idTask] ? datas.PriorityProjectTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var projectTaskName = datas.NameProjectTasks[idTask] ? datas.NameProjectTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.MonthVocations, function(index, values){
                                                $.each(values, function(ind, val){
                                                    ind = index+'-'+ind;
                                                    var _value = valTask[ind] ? valTask[ind] : 0;
                                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                    if(!totalWorkload[ind]){
                                                        totalWorkload[ind] = 0;
                                                    }
                                                    totalWorkload[ind] += _value;
                                                    if(!listSumFamily[idFamily+'-'+ind]){
                                                        listSumFamily[idFamily+'-'+ind] = 0;
                                                    }
                                                    listSumFamily[idFamily+'-'+ind] += _value;
                                                    if(!totalFamily[idFamily]){
                                                        totalFamily[idFamily] = 0;
                                                    }
                                                    totalFamily[idFamily] += _value;
                                                });
                                            });
                                            valTaskDisplay += '</tr>';

                                        });
                                    } else {
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group">';
                                        $.each(datas.MonthVocations, function(index, values){
                                            $.each(values, function(ind, val){
                                                ind = index+'-'+ind;
                                                var _value = value[ind] ? value[ind] : 0;
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[ind]){
                                                    totalWorkload[ind] = 0;
                                                }
                                                totalWorkload[ind] += _value;
                                                if(!listSumFamily[idFamily+'-'+ind]){
                                                    listSumFamily[idFamily+'-'+ind] = 0;
                                                }
                                                listSumFamily[idFamily+'-'+ind] += _value;
                                                if(!totalFamily[idFamily]){
                                                    totalFamily[idFamily] = 0;
                                                }
                                                totalFamily[idFamily] += _value;
                                            });
                                        });
                                        valTaskDisplay += '</tr>';

                                    }
                                });
                            });
                        }
                        var totalWorkloads = totalAvais = 0;
                        $('#total-workload-popup').find('td div').each(function(){
                            var getId = $(this).attr('id');
                            var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                            totalWorkloads += parseFloat(getTotalWl);
                            $('#'+getId).html(getTotalWl);
                            var getCapacity = $('#capacity-'+getId).html();
                            var getAvais = parseFloat(getCapacity) - parseFloat(getTotalWl);
                            //if(getAvais < 0){getAvais = 0;}
                            totalAvais += parseFloat(getAvais);
                            if (getAvais.toString().indexOf('.') != -1){
                                getAvais = getAvais.toFixed(2);
                            }
                            var getOver = 0;
                            if(parseFloat(getAvais)<0){
                                getOver = parseFloat(getAvais)*(-1);
                                getAvais = 0;
                            }else
                            {
                                getOver = 0;
                            }
                            $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                            $('#total-over-popup').find('#over-'+getId).html(getOver);
                        });
                        totalWorkloads = totalWorkloads.toFixed(2);
                        totalAvais = totalAvais.toFixed(2);
                        if(totalAvais < 0){
                            totalOverload = totalAvais*(-1);
                            totalAvais = 0;
                        }else{
                            totalOverload = 0;
                        }
                        $('#total-workingDay').html(totalWorkingDay);
                        $('#total-dayOff').html(totalDayOff);
                        $('#total-capacity').html(totalCapacity);
                        $('#total-workload').html(totalWorkloads);
                        $('#total-availability, .gs-header-content-avai span').html(totalAvais);
                        $('#total-overload, .gs-header-content-over span').html(totalOverload);

                        $(".popup-task-detail").html(listTaskDisplay);
                        $(".popup-task-detail-2").html(valTaskDisplay);

                        $('.popup-task-detail-2').find('.family-group td div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('fam-', '');
                            var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                        $('.popup-task-detail').find('td.ch-fam div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('total-fam-', '');
                            var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).css('text-align', 'right');
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                    }
                    //init();
                    initMonth();

                    //filter
                    $("#filter_year").click(function(e){
                        $('.gs-popup-changeview li').removeClass('ch-current').find('a').removeClass('ch-current');
                        $('#filter_year').parent('li').addClass('ch-current');
                        $('#gs-popup-content').attr('class','year_view');
                        $(this).addClass('ch-current');
                        var headers = working = dayOff = capacity = avais = workload = overload = '';
                        var totalCount = totalWorkingDay = totalDayOff = totalCapacity = totalOverload = 0;
                        widthDivRight = 0;
                        if(datas.YearVocations){
                            headers += '<tr class="popup-header">';
                            working += '<tr id="total-working-popup">';
                            dayOff += '<tr id="total-dayOff-popup">';
                            capacity += '<tr id="total-capacity-popup">';
                            workload += '<tr id="total-workload-popup">';
                            avais += '<tr id="total-avai-popup">';
                            overload += '<tr id="total-over-popup">';
                            $.each(datas.YearVocations, function(index, values){
                                totalCount++;
                                totalDayOff += parseFloat(values);
                                var theWorkingYear = datas.YearWorkingDays[index] ?  datas.YearWorkingDays[index] : 0;
                                totalWorkingDay += parseFloat(theWorkingYear);
                                headers += '<td class="text-center">' + index + '</td>';
                                working += '<td><div id="working-' + index + '">' + theWorkingYear + '</div></td>';
                                dayOff += '<td><div id="dayOff-' + index + '">' + values + '</div></td>';
                                var _capa = parseFloat(theWorkingYear) - parseFloat(values);
                                totalCapacity += parseFloat(_capa);
                                capacity += '<td><div id="capacity-' + index + '">' + _capa + '</div></td>';
                                workload += '<td><div id="' + index + '">' + 0 + '</div></td>';
                                avais += '<td><div id="avai-' + index + '">' + 0 + '</div></td>';
                                overload += '<td><div id="over-' + index + '">' + 0 + '</div></td>';
                                widthDivRight += 50;
                            });
                            headers += '</tr>';
                            working += '</tr>';
                            dayOff += '</tr>';
                            capacity += '</tr>';
                            workload += '</tr>';
                            avais += '</tr>';
                            overload += '</tr>';
                        }
                        $(".popup-header-2").html(headers);
                        $(".popup-working-2").html(working);
                        $(".popup-dayOff-2").html(dayOff);
                        $(".popup-capacity-2").html(capacity);
                        $(".popup-workload-2").html(workload);
                        $(".popup-availa-2").html(avais);
                        $(".popup-over-2").html(overload);

                        // phan detail cua task
                        var listTaskDisplay = '';
                        var valTaskDisplay = '';
                        var totalWorkload = [];
                        var listSumFamily = [];
                        var totalFamily = [];
                        if(datas.listYearDatas){
                            $.each(datas.listYearDatas, function(idFamily, values){
                                var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                                listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                                //valTaskDisplay += '<tr class="family-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                valTaskDisplay += '<tr class="family-group">';
                                $.each(datas.YearVocations, function(index, values){
                                    valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+index+'">&nbsp;</div></td>';
                                });
                                valTaskDisplay += '</tr>';

                                var sttActivity = 0;
                                $.each(values, function(idGlobal, value){
                                    sttActivity++;
                                    idGlobal = idGlobal.split('-');
                                    if(idGlobal[0] === 'ac'){
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;

                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityActivityTasks[idTask] ? datas.PriorityActivityTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var activityTaskName = datas.NameActivityTasks[idTask] ? datas.NameActivityTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.YearVocations, function(index, values){
                                                var _value = valTask[index] ? valTask[index] : 0;
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[index]){
                                                    totalWorkload[index] = 0;
                                                }
                                                totalWorkload[index] += _value;
                                                if(!listSumFamily[idFamily+'-'+index]){
                                                    listSumFamily[idFamily+'-'+index] = 0;
                                                }
                                                listSumFamily[idFamily+'-'+index] += _value;

                                                if(!totalFamily[idFamily]){
                                                    totalFamily[idFamily] = 0;
                                                }
                                                totalFamily[idFamily] += _value;
                                            });
                                            valTaskDisplay += '</tr>';

                                        });
                                    } else if(idGlobal[0] === 'pr'){
                                        var projectName = datas.ListProjects[idGlobal[1]] ? datas.ListProjects[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;

                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityProjectTasks[idTask] ? datas.PriorityProjectTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var projectTaskName = datas.NameProjectTasks[idTask] ? datas.NameProjectTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.YearVocations, function(index, values){
                                                var _value = valTask[index] ? valTask[index] : 0;
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[index]){
                                                    totalWorkload[index] = 0;
                                                }
                                                totalWorkload[index] += _value;
                                                if(!listSumFamily[idFamily+'-'+index]){
                                                    listSumFamily[idFamily+'-'+index] = 0;
                                                }
                                                listSumFamily[idFamily+'-'+index] += _value;

                                                if(!totalFamily[idFamily]){
                                                    totalFamily[idFamily] = 0;
                                                }
                                                totalFamily[idFamily] += _value;
                                            });
                                            valTaskDisplay += '</tr>';

                                        });
                                    } else {
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group">';
                                        $.each(datas.YearVocations, function(index, values){
                                            var _value = value[index] ? value[index] : 0;
                                            valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                            if(!totalWorkload[index]){
                                                totalWorkload[index] = 0;
                                            }
                                            totalWorkload[index] += _value;
                                            if(!listSumFamily[idFamily+'-'+index]){
                                                listSumFamily[idFamily+'-'+index] = 0;
                                            }
                                            listSumFamily[idFamily+'-'+index] += _value;

                                            if(!totalFamily[idFamily]){
                                                totalFamily[idFamily] = 0;
                                            }
                                            totalFamily[idFamily] += _value;
                                        });
                                        valTaskDisplay += '</tr>';

                                    }
                                });
                            });
                        }
                        var totalWorkloads = totalAvais = 0;
                        $('#total-workload-popup').find('td div').each(function(){
                            var getId = $(this).attr('id');
                            var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                            totalWorkloads += parseFloat(getTotalWl);
                            $('#'+getId).html(getTotalWl);
                            var getCapacity = $('#capacity-'+getId).html();
                            var getAvais = parseFloat(getCapacity) - parseFloat(getTotalWl);
                            //if(getAvais < 0){getAvais = 0;}
                            totalAvais += parseFloat(getAvais);
                            if (getAvais.toString().indexOf('.') != -1){
                                getAvais = getAvais.toFixed(2);
                            }
                            var getOver = 0;
                            if(parseFloat(getAvais)<0){
                                getOver = parseFloat(getAvais)*(-1);
                                getAvais = 0;
                            }else
                            {
                                getOver = 0;
                            }
                            $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                            $('#total-over-popup').find('#over-'+getId).html(getOver);
                        });
                        totalWorkloads = totalWorkloads.toFixed(2);
                        totalAvais = totalAvais.toFixed(2);
                        if(totalAvais < 0){
                            totalOverload = totalAvais*(-1);
                            totalAvais = 0;
                        }
                        else{
                            totalOverload = 0;
                        }
                        $('#total-workingDay').html(totalWorkingDay);
                        $('#total-dayOff').html(totalDayOff);
                        $('#total-capacity').html(totalCapacity);
                        $('#total-workload').html(totalWorkloads);
                        $('#total-availability, .gs-header-content-avai span').html(totalAvais);
                        $('#total-overload, .gs-header-content-over span').html(totalOverload);

                        $(".popup-task-detail").html(listTaskDisplay);
                        $(".popup-task-detail-2").html(valTaskDisplay);

                        $('.popup-task-detail-2').find('.family-group td div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('fam-', '');
                            var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                        $('.popup-task-detail').find('td.ch-fam div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('total-fam-', '');
                            var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).css('text-align', 'right');
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                        configPopup(widthDivRight);
                        return false;
                    });
                    $("#filter_month").click(function(e){
                        //filter year
                        $('#filter_year').removeClass('ch-current');
                        initMonth();
                        configPopup(widthDivRight);
                        return false;
                    });
                    $("#filter_week").click(function(e){
                        //filter year

                        return false;
                    });
                    $("#filter_date").click(function(e){
                        //filter year
                        init();
                        configPopup(widthDivRight);
                        return false;
                    });
                    // config cho phan hien thi popup
                    function configPopup(withRight){
                        var lWidth = $(window).width();
                        var DialogFull = Math.round((95*lWidth)/100);
                        var header = Math.round((93*lWidth)/100);
                        var marginTile = Math.round((22*lWidth)/100);
                        var tableLeft = Math.round((35*lWidth)/100);
                        var tableRight = Math.round((56.7*lWidth)/100);
                        var tableRightContent = Math.round((70*lWidth)/100);
                        if(withRight <= tableRightContent){
                            tableRightContent = withRight;
                        }
                        $('#gs-popup-header, #gs-popup-content').width(header);
                        $('.gs-name-header').css('margin-left', marginTile);
                        $('.table-left').width(tableLeft);
                        $('.table-right').width(tableRight);
                        $('#tb-popup-content-2').width(tableRightContent);
                        var lHeight =  $(window).height();
                        var DialogFullHeight = Math.round((80*lHeight)/100);
                        $( "#showdetail" ).dialog({
                            modal: true,
                            width: DialogFull,
                            height: DialogFullHeight,
                            zIndex: 9999999
                        });

                        var dialog_top = $('#gs-popup-content').closest('.ui-dialog').offset().top;
                        var table_top = $('#gs-popup-content').offset().top;                        
                        //var heightDetail = Math.round((48*lHeight)/100);
                        var heightDetail = DialogFullHeight - ( table_top - dialog_top ) - 20;
                        console.log( DialogFullHeight, dialog_top, table_top, heightDetail);
                        $('#gs-popup-content').height(heightDetail);


                        //HOVER ROW
                        $('#tb-popup-content-2 tr').hover(function(){
                            var index=this.rowIndex;
                            if(this.parentNode.className=='popup-header')
                                return false;
                            if(index == ''||index == 0||index == 1)
                                return false;
                            if($('#filter_year').hasClass('ch-current'))
                            {
                                //do nothing
                            }
                            else
                            {
                                index=index-1;
                            }

                            var elm=document.getElementById("tb-popup-content").rows[index];
                            elm.className+=" highlight";
                            this.className+=" highlight";
                        });
                        $('#tb-popup-content-2 tr').mouseleave(function(){
                            var index=this.rowIndex;
                            if(this.parentNode.className=='popup-header')
                                return false;
                            if(index == ''||index == 0||index == 1)
                                return false;
                            if($('#filter_year').hasClass('ch-current'))
                            {
                                //do nothing
                            }
                            else
                            {
                                index=index-1;
                            }
                            var elm=document.getElementById("tb-popup-content").rows[index];
                            elm.className=elm.className.split('highlight').join(" ");
                            this.className=this.className.split('highlight').join(" ");
                        });
                        $('#tb-popup-content tr').hover(function(){
                            var index=this.rowIndex;
                            if(index == ''||index == 0)
                                return false;
                            if($('#filter_year').hasClass('ch-current'))
                            {
                                //do nothing
                            }
                            else
                            {
                                index=index+1;
                            }

                            var elm=document.getElementById("tb-popup-content-2").rows[index];
                            //elm.addClass('highlight');
                            elm.className+=" highlight";
                            this.className+=" highlight";
                        });
                        $('#tb-popup-content tr').mouseleave(function(){
                            var index=this.rowIndex;
                            if(index == ''||index == 0)
                                return false;
                            if($('#filter_year').hasClass('ch-current'))
                            {
                                //do nothing
                            }
                            else
                            {
                                index=index+1;
                            }

                            var elm=document.getElementById("tb-popup-content-2").rows[index];
                            //elm.addClass('highlight');
                            elm.className=elm.className.split('highlight').join(" ");
                            this.className=this.className.split('highlight').join(" ");
                        });

                        //equal height tr
                        $('.popup-task-detail tr').each(function(i){
                            var left = $(this),
                                right = $('.popup-task-detail-2 tr').eq(i);
                            var l = left.height(), r = right.height();
                            if( l > r ){
                                right.height(l);
                            } else if( l < r ) {
                                left.height(r);
                            }
                        });
                    }
                    //END
                    configPopup(widthDivRight);
                });
                }
            }

            var pressNumber = function(e){
                var key = e.keyCode ? e.keyCode : e.which;
                if(!key || key == 8 || key == 13){
                    return;
                }
                var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                if(val != '0' && !regNum.test(val)){
                    e.preventDefault();
                    return false;
                }
            }
            /**
             * Attach grid element event
             *
             */
            var canModified = '<?php echo $canModified ?>';
            if(canModified){

                $('#dialog_import_CSV').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 360,
                    height      : 125
                });

                $("#import_CSV").show().click(function(){
                    $("input[name='FileField[csv_file_attachment]']").val("");
                    $(".error-message").remove();
                    $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
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
            }

            $(".cancel").live('click',function(){
                $("#dialog_data_CSV").dialog("close");
                $("#dialog_import_CSV").dialog("close");
            });

            GanttCallback = function($list, $gantt){
                $gantt.find('.gantt-input').each(function(){
                    var  $element =  $(this);
                    //var val = toFloat($element.html() , true);
                    var val = $element.html();
                    var $input = $('<input type="text" maxlength="5" value="'+ val +'" />').prop('readonly' , canModified);
                    $element.html($input);
                    $input.data('_value' , val);
                    $input.change(function(){
                        updateElement.call($input,$element.parent());
                    }).keypress(pressNumber).focus(function(){
                        $(this).select();
                    });
                    if(val != na && val > 0){
                        //$element.addClass('gantt-unzero');
                    }
                });
				if( $('#ShowOnlyResourceOverload').is(':checked') && ($gantt.first().find('.gantt-total-workload .gantt-invalid').length==0) ){
					// console.log( $gantt );
					$list.first().hide();
					$gantt.first().hide();
				}
                attachDialog($list);
            };
            GanttDone = function(){
                $('div[data-children]').each(function(){
                    // prepend image
                    var img = $('<img src="/img/front/add.gif" class="gantt-image" />');
                    var list = $(this).data('children').split(','),
                        selector = [];
                    var last = list[list.length-1];
                    $(this).prepend(img);
                    for(var i in list){
                        selector.push('table[rel="list-' + list[i] + '"]', 'table[rel="' + list[i] + '"]');
                    }
                    selector = selector.join(',');
                    img.on('click', function(){
                        $(selector).toggle();
                    });
                    var table = $(this).closest('table').addClass('gantt-group');
                    // apply style
                    table.find('.wd-workload td').addClass('gantt-group');
                    // apply right style
                    var mid = $(this).data('pc-id');
                    $('table[rel="' + mid + '"] tr:first td').addClass('gantt-group');
                    $('table[rel="' + mid + '"]').addClass('gantt-group');

                    // left bottom tr
                    $('table[rel="list-' + mid + '"] .fixedHeightStaffing td').addClass('gantt-group-header-end');
                    // right bottom tr
                    $('table[rel="' + mid + '"] .fixedHeightStaffing td').addClass('gantt-group-header-end');

                    // apply last style
                    // left
                    $('table[rel="list-' + last + '"]').addClass('gantt-group-end');
                    // right
                    $('table[rel="' + last + '"]').addClass('gantt-group-end');

                    // bind the image
                });
				
				// Edit for milestone 
				console.log( 'Gantt Done' );
				var gantt_ms = $('.gantt-line .gantt-msi');
				var _conf = 0;
				max_height = 0;
				gantt_ms.each(function(){
					// var _this = $(this).find('span');
					var _this = $(this);
					_this.css('margin-left', -( _this.width()/2 - 13) );
					var _top = parseInt($(this).css('top'));
					var _topi = _top+10;
					$(this).children('i').css('top',' -' + _topi + 'px');
					gantt_ms.each(function(){
						var _comp_ms = $(this);
						if( _this.data('index') > _comp_ms.data('index')){
							
							if( ( ( _this.offset().left >= _comp_ms.offset().left ) && ( _this.offset().left <= (_comp_ms.offset().left + _comp_ms.width()) ) )||( _this.offset().left < _comp_ms.offset().left ) && ( _this.offset().left + _this.width() >= _comp_ms.offset().left   )  ){
								_conf++;
								_this.css( 'top', 18*_conf-_top + 'px');

							}else{
								_conf = 0;
							}
						}
					}); 
					// max_height = Math.max(max_height, $(this).height()); 
				});
				// $('.gantt-ms .gantt-line').height( max_height+10 );
				// $('.gantt-list-primary .gantt-ms .gantt-line').height(max_height+7);
            };
        });
		$('#ShowOnlyResourceOverload').on('change', function(e){
			var _this = $(this);
			var _wdinput = _this.closest('.wd-checkbox-switch');
			_wdinput.addClass('loading');
			setTimeout( function(){
				var _checked = _this.is(':checked');
				var _left_container = $('#mcs_container').find('.gantt-content-wrapper .gantt-staff');
				var _right_container = $('#mcs1_container').find('.gantt-content-wrapper .gantt-staff');
				if( _checked){
					if( _right_container.length)
					$.each(_right_container, function(i, _staff){
						if( !i) return;
						var _row = $(_staff);
						if( !(_row.find('.gantt-total-workload .gantt-invalid').length) ){
							console.log(_staff, _left_container.eq(i));
							$(_row).hide();
							_left_container.eq(i).hide();
						}
					});
					_wdinput.prop('title', i18n.view_all);
				}else{
					_left_container.show();
					_right_container.show();
					_wdinput.prop('title', i18n.view_overload);
				}
				_wdinput.removeClass('loading');
			}, 100);
		});
        var _projectId = '<?php echo $project_id; ?>';
        $('#CategoryCategory').change(function(){
            $('#CategoryCategory option').each(function(){
                if($(this).is(':selected')){
                    var id = $('#CategoryCategory').val();
                    window.location = ('<?php echo $html->url('/') ?>project_staffings_preview/visions/'+_projectId+'/'+id);
                }
            });
        });
        var mgLeft = $('#mcs_container').find('.container').width();
        $('.gantt-chart-1').css('margin-left', mgLeft-3);

        

    })(jQuery);
</script>
<!-- tool tip -->
<script type="text/javascript">

var tooltipTemplate = $('#tooltip-template').html();
    // build the tool-tip on mouse over
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
			+'<p class="progress"><?php __('Progress'); ?>&nbsp;&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-comp').html()+'%</span></p>'
            +'<p class="resource"><?php __('Resource'); ?>&nbsp;:&nbsp;&nbsp;<span style="font-weight: bold;">'+ Datas.find('.hover-data-assign').html()+'</span></p>'
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
        // orthewise destroy the tooltip when mouse leaved
        $(this).tooltip('destroy');
    });
$('#export-staffing').on('click', function(){
    if( isPlus ){
        doExport();
    } else {
        SubmitDataExport();
    }
    return false;
});

function doExport(){
    var header =[], body = [];
    // get headers
    // left header
    $('.gantt-header:first td').each(function(){
        var def = {
            value: $(this).text(),
            width: 15
        };
        if( $(this).hasClass('gantt-name') ){
            def.width = 30;
        }
        header.push(def);
    });
    // right header
    $('.gantt-primary:first tr.gantt-num td').each(function(i){
        var text = $(this).text() + '-' + $('.gantt-primary tr.gantt-head td').eq(i).text();
        header.push({
            width: 15,
            value: text
        });
    });
    // data
    $('.gantt-left').each(function(i){
        var block = {
            name: '',
            rows: []
        };
        var row = 0;
        if( i > 0 ){
            block.name = $(this).find('td.gantt-name:first').text();
        }
        else {
            block.name = $(this).find('td.gantt-name:eq(1)').text();
        }
        var isGroup = $(this).hasClass('gantt-group');
        $(this).find('tr:not(.gantt-title,.fixedHeightStaffing)').each(function(j){
            var columns = [];
            // td
            $(this).find('td:not(.gantt-name)').each(function(){
                var text = $.trim($(this).text());
                if( !text.length ){
                    text = $.trim($(this).find('input').val());
                }
                // markup
                if( parseFloat(text) == 0 ){
                    text = '';
                }
                if( $(this).hasClass('gantt-invalid') ){
                    columns.push({
                        value: text,
                        type: 'HasBG',
                        bg: 'FC5C6F',
                        color: 'FFFFFF'
                    });
                } else {
                    if( $.isNumeric(text) ){
                        columns.push({
                            type: 'decimal',
                            value: text
                        });
                    } else {
                        columns.push(text);
                    }
                }
            });
            if( columns.length ){
                block.rows[row++] = columns;
            }
        });
        body.push(block);
    });
    // right
    $('.gantt-right').each(function(i){
        var isGroup = $(this).hasClass('gantt-group');
        $(this).find('tr:not(.gantt-title,.fixedHeightStaffing)').each(function(j){
            var columns = [];
            // td
            $(this).find('td:not(.gantt-name)').each(function(){
                var text = $.trim($(this).text());
                if( !text.length ){
                    text = $.trim($(this).find('input').val());
                }
                // markup
                if( parseFloat(text) == 0 ){
                    text = '';
                }
                if( $(this).hasClass('gantt-invalid') ){
                    columns.push({
                        value: text,
                        type: 'HasBG',
                        bg: 'FC5C6F',
                        color: 'FFFFFF'
                    });
                } else {
                    if( $.isNumeric(text) ){
                        columns.push({
                            type: 'decimal',
                            value: text
                        });
                    } else {
                        columns.push(text);
                    }
                }
            });
            if( columns.length ){
                body[i].rows[j] = body[i].rows[j].concat(columns);
            }
        });
    });
    $('#export-team-data').val(JSON.stringify({
        header: header,
        body: body
    })).closest('#export-team-plus').submit();
}

$('.category-view').text( $('#CategoryCategory :selected').text() );
// set height content heading - gantt

function setHeightContentHead(){
	var contentLeft = $('#mcs_container');
	var contentRight = $('#mcs1_container');
	console.log(contentLeft, contentRight);
	// if(contentLeft.lenght && contentRight.lenght){
		heightContentLeft = contentLeft.find('.gantt-head-content').height();
		heightContentRight = contentRight.find('.gantt-chart-content').height();
		console.log(heightContentLeft, heightContentRight);
		if(heightContentLeft > heightContentRight){
			 contentRight.find('.gantt-chart-content').height(heightContentLeft + 1);
		}else{
			contentLeft.find('.gantt-head-content').height(heightContentRight);
		}
	// }
}
// setHeightContentHead();
</script>
<form style="display: none" id="export-team-plus" method="post" action="<?php echo $this->Html->url(array('action' => 'export_team_plus')) ?>">
    <input type="hidden" name="data[data]" id="export-team-data">
</form>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
<div id="showdetail">
    <div id="gs-popup-header">
        <div class="gs-header-title">
            <p class="gs-name-header"><?php __('Availability');?> : <span>ten employee</span></p>
        </div>
        <br clear="all"  />
        <div class="gs-header-content">
            <p class="gs-header-content-start"><?php __('Start Date');?> : <span>ten start</span></p>
            <p class="gs-header-content-end"><?php __('End Date');?> : <span>ten end</span></p>
            <p class="gs-header-content-avai"><?php __('Availability');?> : <span>ten avai</span></p>
            <p class="gs-header-content-over"><?php __('Overload');?> : <span>overload</span></p>
        </div>

        <ul class="gs-popup-changeview">
            <li><a href="javascript:void(0);" id="filter_month"><?php echo __("Month", true)?></a></li>
            <li><a href="javascript:void(0);" id="filter_year"><?php echo __("Year", true)?></a></li>
        </ul>
    </div>
    <div id="gs-popup-content">
        <div class="table-left">
            <table id="tb-popup-content">
                <thead>
                    <tr class="popup-header">
                        <td style="width: 450px;" class="relative name"><span class="employee_name abs-title"></span></td> 
                        <td style="width: 90px;"><div class="text-center"><?php __('Priority');?></div></td>
                        <td style="width: 60px;"><div class="text-center"><?php __('Total') ?></div></td>
                    </tr>
                    <tr >
                        <td class="popup-header-group popup-header-group-working-day"><div><?php __('Working Day');?></div></td>
                        <td>&nbsp;</td>
                        <td id="total-workingDay" class="text-right">&nbsp;</td>
                    </tr>
                    <tr >
                        <td class="popup-header-group popup-header-group-day-off"><div><?php __('Absence');?></div></td>
                        <td>&nbsp;</td>
                        <td id="total-dayOff" class="text-right">&nbsp;</td>
                    </tr>
                    <tr >
                        <td class="popup-header-group popup-header-group-capacity"><div><?php __('Capacity');?></div></td>
                        <td>&nbsp;</td>
                        <td id="total-capacity" class="text-right">&nbsp;</td>
                    </tr>
                    <tr >
                        <td class="popup-header-group popup-header-group-workload"><div><?php __('Workload');?></div></td>
                        <td>&nbsp;</td>
                        <td id="total-workload" class="text-right">&nbsp;</td>
                    </tr>

                    <tr >
                        <td class="popup-header-group popup-header-group-availability"><div><?php __('Availability');?></div></td>
                        <td>&nbsp;</td>
                        <td id="total-availability" class="text-right">&nbsp;</td>
                    </tr>

                    <tr >
                        <td class="popup-header-group popup-header-group-overload"><div><?php __('Overload');?></div></td>
                        <td>&nbsp;</td>
                        <td id="total-overload" class="text-right">&nbsp;</td>
                    </tr>
                </thead>

                <tbody class="popup-task-detail">

                </tbody>
            </table>
        </div>
        <div class="table-right">
            <table id="tb-popup-content-2">
                <thead class="popup-header-2">

                </thead>
                <tbody class="popup-working-2">

                </tbody>
                <tbody class="popup-dayOff-2">

                </tbody>
                <tbody class="popup-capacity-2">

                </tbody>
                <tbody class="popup-workload-2">

                </tbody>

                <tbody class="popup-availa-2">

                </tbody>

                <tbody class="popup-over-2">

                </tbody>

                <tbody class="popup-task-detail-2">

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl" class="non-actask">

    </dl>
</div>
