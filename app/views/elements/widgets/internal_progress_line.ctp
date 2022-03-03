<style>
.progress-line{
    box-shadow: none;
}
.amrs-wrap{
    padding: 0;
}
.progress-line .amrs-wrap{
    padding-top: 0;
}
.scroll-progress-left{
    left: 0;
}
.scroll-progress-right{
    right: 0;
}
.progress-line .progress-line-inner{
    width: 95%;
    margin: auto;
}
.jqx-rc-all.jqx-button{
    z-index: 9
}
.project-progress-line-widget.fullScreen .progress-line .amrs-wrap{
    display: inline-block;
    position: relative;
}
#tblChart tbody tr:hover td{
	background-color: transparent;
}
#svgChart .jqx-chart-axis-text{
	font-size: 11px;
    color: #666666;
    fill: #666666;
	font-family: "Open Sans";
}
.wd-budget-chart #chartContainerIn {
    min-width: 100%;
    position: relative;
    margin-bottom: -28px;
}
</style>
<?php
$unit = __('$', true);
$currency_in = !empty($budget_settings) ? $budget_settings :  $unit;
?>
<div class="wd-widget project-progress-line-widget">
    <div class="wd-widget-inner">
        <div class="widget_content">
            <div class="progress-line loading-mark">
                <div class="amrs-wrap">
                    <div id ="progress-line-in" class="progress-line-inner">
                        <div id='chartContainerIn' style="width:<?php echo ($countLineIn * 50); ?>px; height: <?php echo (!empty($chartHeight) ? $chartHeight : 240);?>px">
                        </div>
                    </div>
                    <span id="chartContainerIn-left" class="scroll-progress scroll-progress-left"></span>
                    <span id="chartContainerIn-right" class="scroll-progress scroll-progress-right"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="jqx-format-tooltip-in" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true); ?>: <span>{budget} <?php echo $currency_in;?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true); ?>: <span>{forecast}<?php echo $currency_in;?></span></li>
		<li class="ordered"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true); ?>: <span>{ordered}<?php echo $currency_in;?></span></li>
	</ul>
</div>
<div id="jqx-format-tooltip-in-md" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true); ?>: <span>{budget} <?php __('M.D');?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true); ?>: <span>{forecast}<?php __('M.D');?></span></li>
		<li class="ordered"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true); ?>: <span>{ordered}<?php __('M.D');?></span></li>
	</ul>
</div>
<script type="text/javascript">
function wd_progress_line_expand(_element){
    var _this = $(_element);
    var _wg_container = _this.closest('.wd-widget');
    _wg_container.addClass('fullScreen');
    _wg_container.closest('li').addClass('wd_on_top');
    _this.hide();
    _wg_container.find('.primary-object-collapse').show();
    
}
function wd_progress_line_collapse(_element){
    var _this = $(_element);
    var _wg_container = _this.closest('.wd-widget');
    _wg_container.removeClass('fullScreen');
    _wg_container.closest('li').removeClass('wd_on_top');
    _this.hide();
    _wg_container.find('.primary-object-expand').show();
}
(function($){
	$("#chartContainerIn-right, #chartContainerIn-left").click(function() {
		var dir = this.id=="chartContainerIn-right" ? '+=' : '-=' ;
		$(this).closest(".amrs-wrap").find('.progress-line-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
	});
	
})(jQuery);
// prepare internals jqxChart settings
var inter_maxValue    = <?php echo json_encode($max_internal_euro); ?>,
	dataset_internals    = <?php echo !empty($dataset_internals) ? json_encode(array_values($dataset_internals)) :json_encode(array()) ?>,
	type    = <?php echo !empty($type) ? json_encode($type) : json_encode('') ?>;
var inter_maxValue_price	= <?php echo json_encode($max_internal_euro); ?>;
var inter_maxValue_md		= <?php echo json_encode($max_internal_md); ?>;

var view_Euro = "<?php printf(__('View %s', true), $currency_in);?>";
var view_MD = "<?php printf(__('View %s', true), __('M.D', true));?>";
function caculate(value){
   value = value.from - value.to;
   return  Math.round(value * 100) / 100 ;
}
function escapeRegExp(str) {
	return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}
function replace(obj, str){
	$.each(obj, function(key, value){
		str = replaceAll('{' + key + '}', value, str);
	});
	return str;
}
function replaceAll(find, replace, str) {
  return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}
var internal_progress_setting = {
	title: "",
	description: '',
	padding: { left: 20, top: 20, right: 20, bottom: 20 },
	titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
	source: dataset_internals,
	showBorderLine: false,
	enableAnimations: true,
	showLegend: true,
	isFormatTooltip: true,
	toolTipFormatFunction : function(a, b, data, d){
		var template = $('#jqx-format-tooltip-in').html();
		var val = {};
		val.budget = number_format(dataset_internals[b]['budget_price'], 2, ',', ' ');
		val.ordered = number_format(dataset_internals[b]['consumed_price'], 2, ',', ' ');
		val.forecast = number_format(dataset_internals[b]['validated_price'], 2, ',', ' ');
		var n = replace(val, template);
		return n;
	},
	categoryAxis:
		{
			dataField: 'date_format',
			description: '',
			showGridLines: true,
			color: "#DDDDDD",
		},
	colorScheme: 'scheme02',
	seriesGroups: [
		{
			type: 'line',
			showLabels: false,//default
			valueAxis:
			{
				axisSize: 'auto',
				minValue: 0,
				maxValue: inter_maxValue,
				unitInterval: inter_maxValue,
				description: '',
				displayValueAxis: false,
				valuesOnTicks: true
			},
			dashStyle: '5,5',
			series: [
					{ dataField: 'validated_price', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352'},
				]
		},
		{
			type: 'splinearea',
			showLabels: false,//default
			valueAxis:
			{
				axisSize: 'auto',
				minValue: 0,
				maxValue: inter_maxValue,
				unitInterval: inter_maxValue,
				description: '',
				displayValueAxis: false
			},
			series: [
					{ dataField: 'consumed_price', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2'},
					{ dataField: 'budget_price', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79'},
					// { dataField: 'validated_price', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352'},

				]
		},
	]
};
function draw_internal_chart(elm){
	var _this = $(elm);
	$('.progress-line.loading-mark').addClass('loading');
	if( _this.is(':checked')){ // Euro 
		var inter_pie_container = $('.chard-internal');
		if( inter_pie_container.length){
			inter_pie_container.find('.progress-values-price').removeClass('wd-hide');
			inter_pie_container.find('.progress-values-md').addClass('wd-hide');
		}
		_this.parent().prop('title', view_MD);
		internal_progress_setting.toolTipFormatFunction = function(a, b, data, d){
			var template = $('#jqx-format-tooltip-in').html();
			var val = {};
			val.budget = number_format(dataset_internals[b]['budget_price'], 2, ',', ' ');
			val.ordered = number_format(dataset_internals[b]['consumed_price'], 2, ',', ' ');
			val.forecast = number_format(dataset_internals[b]['validated_price'], 2, ',', ' ');
			var n = replace(val, template);
			return n;
		};
		internal_progress_setting.seriesGroups = [
			{
				type: 'line',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: inter_maxValue,
					unitInterval: inter_maxValue,
					description: '',
					displayValueAxis: false,
					valuesOnTicks: true
				},
				dashStyle: '5,5',
				series: [
						{ dataField: 'validated_price', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352'},
					]
			},
			{
				type: 'splinearea',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: inter_maxValue,
					unitInterval: inter_maxValue,
					description: '',
					displayValueAxis: false
				},
				series: [
					{ dataField: 'consumed_price', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2'},
					{ dataField: 'budget_price', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79'},
					// { dataField: 'validated_price', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352'},

				]
			},
		];
	}else{ // View MD
		var inter_pie_container = $('.chard-internal');
		if( inter_pie_container.length){
			inter_pie_container.find('.progress-values-price').addClass('wd-hide');
			inter_pie_container.find('.progress-values-md').removeClass('wd-hide');
		}
		_this.parent().prop('title', view_Euro);
		internal_progress_setting.toolTipFormatFunction = function(a, b, data, d){
			var template = $('#jqx-format-tooltip-in-md').html();
			var val = {};
			val.budget = number_format(dataset_internals[b]['budget_md'], 2, ',', ' ');
			val.ordered = number_format(dataset_internals[b]['consumed'], 2, ',', ' ');
			val.forecast = number_format(dataset_internals[b]['validated'], 2, ',', ' ');
			var n = replace(val, template);
			return n;
		};
		internal_progress_setting.seriesGroups = [
			{
				type: 'splinearea',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: inter_maxValue_md,
					unitInterval: inter_maxValue_md,
					description: '',
					displayValueAxis: false
				},
				series: [
					{ dataField: 'validated', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352'}

				]
			},
			{
				type: 'splinearea',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: inter_maxValue_md,
					unitInterval: inter_maxValue_md,
					description: '',
					displayValueAxis: false
				},
				series: [
					{ dataField: 'consumed', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2'},
					{ dataField: 'budget_md', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79'},

				]
			}
		];
	}
	setTimeout( function(){
		$('#chartContainerIn').jqxChart(internal_progress_setting);
		console.log( internal_progress_setting);
		$('.progress-line.loading-mark').removeClass('loading');
	}, 200);
}
// END prepare internals jqxChart settings

$(document).ready(function () {
    $('#progress-line-in').stop().animate({scrollLeft: $('#chartContainerIn').width()}, 200);
	// $('#chartContainerIn').jqxChart(internal_progress_setting);
	$('#InternalChartSwitch').on('change', function(){
		draw_internal_chart(this);
	});
	if( $('#InternalChartSwitch').length){
		draw_internal_chart('#InternalChartSwitch');
	}else{
		$('#chartContainerIn').jqxChart(internal_progress_setting);
	}
});
</script>