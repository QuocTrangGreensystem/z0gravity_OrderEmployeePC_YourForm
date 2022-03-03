<?php echo $html->css('jquery.dataTables'); ?>
<?php
$arg = $this->passedArgs;

$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = !empty($arg['?']['type']) ? strtolower(trim($arg['?']['type'])) : 'month';
?>
<?php echo $html->css('gantt_v2_1_ajax'); ?>
<?php echo $html->css('gantt'); ?>

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
echo $html->script(array('jquery.mCustomScrollbar'));
?>
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
    .gantt-side{
        width:600px !important;
    }
	#mcs1_container{
		margin-left:600px ;
	}
    .scrollFullScreen{
        top:inherit !important;
        left:inherit !important;
        position:fixed !important;
        bottom:0 !important; 
        right:0 !important;
    }
</style>
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <div style="float: left;">
                        <?php
                        echo $this->Form->create('Display', array('type' => 'get', 'url' => array_merge(array(
                                'controller' => 'projects',
                                'action' => 'projects_vision'
                            ))));
                        ?>
                        <div id="gantt-display">
                            <label class="title"><?php __('Display real time'); ?> </label>
                            <?php
                            echo $this->Form->input('display', array(
                                'rel' => 'no-history',
                               // 'onchange' => 'jQuery(this).closest(\'form\').submit();',
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
                    <?php /* <h2 class="wd-t1"><?php echo __("Project List", true); ?></h2> */ ?>
                    <!-- <a href="javascript:void(0);" id="add_project" class="wd-add-project"><span><?php __('Add Project') ?></span></a> -->
                    <a href="#" onclick="SubmitDataExport();return false;" class="btn btn-excel"><span><?php __('Export Excel') ?></span></a>
                    <a href="javascript:;" onclick="expandScreen();" class="btn btn-fullscreen"></a>	
                </div>
                <div class="gantt-switch">
                    <?php echo $this->Html->link(__('Date', true), Set::merge($arg, array('?' => array('type' => 'date'))), array('class' => $type == 'date' ? 'gantt-switch-current' : '')); ?>
                    <?php echo $this->Html->link(__('Week', true), Set::merge($arg, array('?' => array('type' => 'week'))), array('class' => $type == 'week' ? 'gantt-switch-current' : '')); ?>
                    <?php echo $this->Html->link(__('Month', true), Set::merge($arg, array('?' => array('type' => 'month'))), array('class' => $type == 'month' ? 'gantt-switch-current' : '')); ?>
                    <?php echo $this->Html->link(__('Year', true), Set::merge($arg, array('?' => array('type' => 'year'))), array('class' => $type == 'year' ? 'gantt-switch-current' : '')); ?>
                </div>
                <div id="GanttChartDIV">
                    <?php
                    $rows = 0;
                    $start = $end = 0;
                    $data = array();
                    foreach ($projects as $project) {
                        $_data = array(
							'program' => $project['ProAmr']['amr_program'],
							'category' => $project['Project']['category'],
                            'name' => $this->Html->link($project['Project']['project_name'], '/projects/edit/' . $project['Project']['id'], array('target' => '_blank')),
                            'phase' => array(),
                        );
                        if (!empty($project['ProjectPhasePlan'])) {
                            foreach ($project['ProjectPhasePlan'] as $phace) {
                                $_phase = array(
									'id' => $phace['id'].','.$project['Project']['id'],
                                    'name' => $phace['ProjectPhase']['name'],
                                    'start' => $this->Gantt->toTime($phace['phase_planed_start_date']),
                                    'end' => $this->Gantt->toTime($phace['phase_planed_end_date']),
                                    'rstart' => $this->Gantt->toTime($phace['phase_real_start_date']),
                                    'rend' => $this->Gantt->toTime($phace['phase_real_end_date']),
                                    'color' => $phace['ProjectPhase']['color'] ? $phace['ProjectPhase']['color'] : '#004380'
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
                    //pr(date('Y-m-d',$start));
                    //pr(date('Y-m-d',$end));
                    //pr($projects);
                    //exit();
                    unset($projects, $project, $_data, $_phase, $phase);

                    if (empty($start) || empty($end)) {
                        echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                    } else {

                        $this->Gantt->create($type, $start, $end, array(), false);
                        foreach ($data as $value) {
                            $rows++;
                            if (empty($value['phase'])) {
                                $this->Gantt->drawLine(__('no data exit', true), 0, 0, 0, 0, '#ffffff');
                            } else {
                                foreach ($value['phase'] as $node) {
                                    $color = '#004380';
                                    if (!empty($node['color'])) {
                                        $color = $node['color'];
                                    }
                                    if (!$display) {
                                        $node['rstart'] = $node['rend'] = '';
                                    }
                                    $this->Gantt->drawLine($node['name'], $node['start'], $node['end'], $node['rstart'], $node['rend'], $color, false, $node['id']);
                                }
                            }
							$status='';
							if($value['category']==1)
							{
								$status = __('In progress', true);
							}
                            elseif($value['category']==2)
							{
								$status = __('Opportunity', true);
							}
							elseif($value['category']==3)
							{
								$status = __('Archived', true);
							}
							elseif($value['category']==4)
							{
								$status = __('Model', true);
							}
                            $this->Gantt->drawEnd($value['name'],true,$value['program'],$status);
                        }
                        $this->Gantt->end();
                    }
                    ?>
                    <div style="clear: both;"></div>
                </div>
                <div class="paging-wrapper" style="overflow: hidden;padding-top: 10px;">
                    <div style="float: left;">
                        <div style="float: left;">
                            <?php
                            $text = '';
                            $hasPaging = !empty($paginator) && ($paginator->hasPrev() || $paginator->hasNext());
                            if ($hasPaging) {
                                $text = __('<span class="paging-page"> Page</span>', true);
                            }
                            echo $this->Form->create('Paginate', array('inputDefaults' => array('div' => false, 'label' => false), 'type' => 'get', 'url' => '/' . $this->params['url']['url']));
                            echo sprintf(__('Show %s projects.', true) . $text, $this->Form->input('limit', array('style' => 'border:1px solid #ccc;padding:3px;', 'onchange' => 'this.form.submit();', 'selected' => $limit, 'options' => array(10 => '10', 25 => '25', 50 => '50'))));
							$argTemp = $arg;
                            unset($arg["?"]['limit']);
                            foreach ($arg["?"] as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $v) {
                                        echo $this->Form->hidden($key, array('name' => $key . '[]', 'value' => $v));
                                    }
                                    continue;
                                }
                                echo $this->Form->hidden($key, array('value' => $value));
                            }
                            echo $this->Form->end();
                            ?>
                        </div>
                        <?php if ($hasPaging) : ?>
                            <div class="paging" id="pagination" style="float: left;">
                                <?php
                                $paginator->options(array('url' => $argTemp));
                                echo $paginator->first("prev", array('class' => 'prev'));
                                echo $paginator->numbers(array('separator' => '&nbsp;'));
                                echo $paginator->last("next", array('class' => 'next'));
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ADD CODE BY VINGUYEN 05/06/2014 -->
<?php echo $this->element('dialog_detail_value') ?>
<!-- END -->
<?php
echo $this->Form->create('Export', array('url' => array_merge($arg, array('controller' => 'projects', 'action' => 'export_vision')), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('rows', array('value' => $rows));
echo $this->Form->end();
?>
<script type="text/javascript">
	function showPhaseDetail(phase,project)
	{
		var type = '<?php echo $type; ?>';
		var data = 'type='+type+'&phase='+phase+'&ajax=1';
		//var data = 'type='+type+'&phase='+phase;
		$.ajax({
			url: '/project_phase_plans/phase_vision/'+project+'?'+data,
			data: data,
			async: false, 
			type:'POST',
			success:function(datas) {
				jQuery('.dragger_container').css({'z-index':10});
				var wh=jQuery(window).height();
				var ww=jQuery(window).width();
				jQuery('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0,'overflow':'auto','width':ww-100});
				if(wh<768){
					jQuery('#dialogDetailValue').css({'overflow':'auto'});
					jQuery('#contentDialog').css({'max-height':600,'width':'auto'});
				} else {
					jQuery('#contentDialog').css({'max-height':'none','width':'auto'});
				}
				
				jQuery('#contentDialog').html(datas);
				
				jQuery('#AjaxGanttChartDIV .gantt-child').show();
				setTimeout(function(){
					showMe();
					jQuery('.gantt-line .gantt-d30').show();
					var wGantt = jQuery('#AjaxGanttChartDIV table.gantt').width();
					jQuery('#AjaxGanttChartDIV .customScrollBox').width(wGantt);
					jQuery('#AjaxGanttChartDIV .container').width(wGantt);
					jQuery('#AjaxGanttChartDIV .content').width(wGantt);
					jQuery('#ajaxScroll').width(wGantt);
				},100);
			}
		});
		/*var url = '<?php echo $this->Html->url(array('controller' => 'project_phase_plans', 'action' => 'phase_vision')); ?>';
		url = url+'/'+project+'?'+data;
		console.log(url);
		window.location.href = url;*/
	}

	$(document).ready(function () {
        var today = new Date('<?php echo date('Y-m-d') ?>');

        var type = <?php echo json_encode($type) ?>;
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
            var ratio = ( $("#mcs_container .container").width()/2 + $col.position().left ) / container.width();
            if( ratio > 1 )ratio = 1;
            var left = 0 - Math.round(ratio * max);
            var scroll = Math.round(ratio * (dragger_container.width() - dragger_container.children(".dragger.ui-draggable").width()));
            $("#mcs1_container .container").css('left', left + 'px');
            dragger_container.children(".dragger.ui-draggable").css('left', scroll + 'px');
        }
		$(window).trigger('resize');
	});
    function SubmitDataExport(){
        expandForExport();
        $('#GanttChartDIV').html2canvas({
            afterCanvas: function(){
                collapseAfterExport();
            }
        });
    }
    function expandForExport(){
        //expandScreen();
        //expand right gantt
        $('#wd-container-main .wd-layout, #x-scroll').css('overflow', 'visible');
    }
    function collapseAfterExport(){
        $('#wd-container-main .wd-layout, #x-scroll').css('overflow', 'hidden');
        //$('#x-scroll').css('overflow', 'hidden');
    }
	function expandScreen(){
		$(window).trigger('resize');
		$('#GanttChartDIV').addClass('fullScreen');
		$('#collapse').show();
		$('.dragger_container').addClass('scrollFullScreen');
		$(window).trigger('resize');
	}
	function collapseScreen(){
		$(window).trigger('resize');
		$('#GanttChartDIV').removeClass('fullScreen');
		$('#collapse').hide();

		$('.dragger_container').removeClass('scrollFullScreen');
		$(window).trigger('resize');
	}
	//EXPAND TREE
	$(document).keyup(function(e) {
		if (window.event)
		{
			var value = window.event.keyCode;
		}
		else
			var value=e.which;
		if (value == 27) { collapseScreen(); }   
	});
    $(".radio input:radio[name=display]").click(function(){
        var linkSelected = $(location).attr('href');
        var n = linkSelected.search("display=");
        if(n<0){
            linkSelected = linkSelected+"&display=0";
        }
        if($('input:radio[name=display]:checked').val()==1){
            linkSelected = linkSelected.replace("display=0", "display=1");
        }else{
            linkSelected = linkSelected.replace("display=1", "display=0");
        } 
        window.location.href = linkSelected;
    });
</script>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<div id="collapse" style="padding:4px; cursor:pointer; background-color:#FFF; color:#F00; display:none; position:absolute; top:0; right:0; z-index:9999999999" onclick="collapseScreen();" >
    <button class="btn btn-esc"></button>
</div>