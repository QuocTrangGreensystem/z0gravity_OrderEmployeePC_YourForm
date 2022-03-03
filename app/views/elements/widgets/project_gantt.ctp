<?php
/* Input
 * @param: created_values
 totalValue
 sumOfTypeVal
 sumSelectedOfTypeVals
 dataProjectCreatedValsComment
*/
// ob_clean(); debug($projectPlanName); exit;
$widget_title = !empty( $widget_title) ? $widget_title : __('Gantt++', true);
// if( empty($created_values) )return;
$phasePlans = !empty($phasePlans) ? $phasePlans : array();
// $type= "week";

echo $html->script('preview/define_limit_date');
echo $html->css('jquery.dataTables');
echo $html->css('jquery.mCustomScrollbar');
echo $html->css('preview/project_task');
echo $html->css('gantt_v2_1');
echo $html->css('preview/project_task_gantt');
?>
<div class="wd-widget wd-gantt-widget">
	<div class="wd-widget-inner">
		<div class="widget-title wd-hide">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
			<div class="widget-action">
				<a href="javascript:void(0);" onclick="wd_gantt_collapse(this)" class="wd-widget-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
			</div>
		</div>
		<div class="widget_content">
			<div id="GanttChartDIV" class ="mini-gantt loading">
				
			</div>
		<a href="javascript:void(0);" onclick="wd_gantt_expand()" class="wd-gantt-expand"></a>
		</div>
	</div>
</div>

<script>
	var project_id  = <?php echo $project_id; ?>;
	var dump;
	$(document).ready(function(){
		
		if( !$('#GanttChartDIV').hasClass('loaded') ){
			$.ajax({
				type: "POST",
				// url: '/project_tasks_preview/index/'+ project_id,
				url: '/project_amrs_preview/wd_project_gantt/'+ project_id,
				data: {},
				success: function(_respon){
					dump = _respon;
					if(_respon){
						$('#GanttChartDIV').html(_respon).removeClass('loading').addClass('loaded');
						$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
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
					}
				}
			});
		}
	});
	
	function wd_gantt_expand() {
		var _this = $('#GanttChartDIV');
		var _wg_container =  _this.closest('.wd-widget');
		_wg_container.addClass('fullScreen');
		_wg_container.closest('li').addClass('wd_on_top');
		_wg_container.find('.wd-gantt-expand').hide();
		_wg_container.find('.wd-widget-collapse').show();
		var wrapper = _wg_container.find('.gantt-chart-wrapper');
		var h_wrapper = $(window).height() - 80;
		wrapper.css('max-height', h_wrapper > 800 ? h_wrapper : 800);
        _this.removeClass('mini-gantt');
		_wg_container.find('.widget-title.wd-hide').show();
		_this.closest('.wd-col').css('width', '100%').siblings().hide();
		_this.closest('.wd-row').siblings().hide();
		$('#wd-container-header-main, .wd-indidator-header').hide();
		$('#layout').addClass('widget-expand');
        $(window).trigger('resize');
        // if( typeof destroyresizable == 'function') destroyresizable();
    }
    function wd_gantt_collapse() {
        var _this = $('#GanttChartDIV');
		var _wg_container =  _this.closest('.wd-widget');
		_wg_container.removeClass('fullScreen');
		_wg_container.closest('li').removeClass('wd_on_top');
		_wg_container.find('.wd-gantt-expand').show();
		_wg_container.find('.wd-widget-collapse').hide();
		var wrapper = _wg_container.find('.gantt-chart-wrapper');
		wrapper.css('max-height', '');
        _this.addClass('mini-gantt');		
		_wg_container.find('.widget-title.wd-hide').hide();
		_this.closest('.wd-col').css('width', '').siblings().show();
		_this.closest('.wd-row').siblings().show();
		$('#wd-container-header-main, .wd-indidator-header').show();
		$('#layout').removeClass('widget-expand');
		$('#expand').show();
		$('#table-collapse').hide();
        $(window).trigger('resize');
		// if( typeof initresizable == 'function') initresizable();
    }
    function filterEmployee(text,e){
        $('li[rel="li-employee"]').each(function(index, element) {
            str=$(this).html();
            str=str.toLowerCase();
            text=text.toLowerCase();
            elm=$(this).attr('id');
            if(str.indexOf(text)==-1) {
                $(this).hide();
                $('div[rel='+elm+']').hide();
            } else {
                $(this).show();
                $('div[rel='+elm+']').show();
            }
        });
    }
</script>	
<style>
	.wd-gantt-widget .widget_content{
		overflow: hidden;		
	}
	#GanttChartDIV {
		margin-bottom: 0;
	}
	#GanttChartDIV tr.gantt-ms td{
		display: block;
	}
	#GanttChartDIV .scroll-right{
		right: 0;
	}
	#GanttChartDIV .scroll-left{
		left: 0;
	}
	.wd-gantt-expand{
		position: absolute;
		width: 38px;
		height: 38px;
		background: transparent url('/img/new-icon/expand.png') center no-repeat;
	    right: 20px;
		bottom: 37px;
		z-index: 9;
		border: 1px solid #E1E6E8;
		border-radius: 3px;
	}
	.wd-gantt-expand:hover{
		background-color: rgba(36, 127, 195, 0.80);
	}
	.wd-gantt-widget .widget_content{
		position: relative;
		padding: 10px;
	}
	#mcs1_container.gantt-chart{
		height: inherit;
	}
	.gantt-chart-wrapper .gantt-ms{
		border-top: 1px solid #ddd;
	}
</style>
