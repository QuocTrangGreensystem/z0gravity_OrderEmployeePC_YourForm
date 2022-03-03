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
.wd-budget-chart #chartContainerEx {
    min-width: 100%;
    position: relative;
    margin-bottom: -28px;
}
</style>
<div class="wd-widget project-progress-line-widget">
    <div class="wd-widget-inner">
        <div class="widget_content">
            <div class="progress-line">
                <div class="amrs-wrap">
                    <div id ="progress-line-ex" class="progress-line-inner">
                        <div id='chartContainerEx' style="width:<?php echo ($countLineExter * 50); ?>px; height: <?php echo (!empty($chartHeight) ? $chartHeight : 240);?>px">
                        </div>
                    </div>
                    <span id="chartContainerEx-left" class="scroll-progress scroll-progress-left"></span>
                    <span id="chartContainerEx-right" class="scroll-progress scroll-progress-right"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
	$unit = __('$', true);
	$currency_ex = !empty($budget_settings) ? $budget_settings :  $unit;
?>
<div id="jqx-format-tooltip-ex" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true); ?>: <span>{budget} <?php echo $currency_ex;?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true); ?>: <span>{forecast}<?php echo $currency_ex;?></span></li>
		<li class="ordered"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true); ?>: <span>{ordered}<?php echo $currency_ex;?></span></li>
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
	$("#chartContainerEx-right, #chartContainerEx-left").click(function() {
		var dir = this.id=="chartContainerEx-right" ? '+=' : '-=' ;
		$(this).closest(".amrs-wrap").find('.progress-line-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
	});
	
})(jQuery);
// prepare jqxChart externals settings
var ex_maxValue    = <?php echo json_encode($max_externals); ?>,
	currency    = <?php echo json_encode($currency_ex); ?>,
	ex_dataSets    = <?php echo !empty($dataset_externals) ? json_encode(array_values($dataset_externals)) :json_encode(array()) ?>,
	type    = <?php echo !empty($type) ? json_encode($type) : json_encode('') ?>;
var tooltip = 	
function caculate(value){
   value = value.from - value.to;
   return  Math.round(value * 100) / 100;

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

var ex_settings = {
	title: "",
	description: '',
	padding: { left: 20, top: 20, right: 20, bottom: 20 },
	titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
	source: ex_dataSets,
	showBorderLine: false,
	isFormatTooltip: true,
	enableAnimations: true,
	showLegend: true,
	toolTipFormatFunction : function(a, b, data, d){
		var template = $('#jqx-format-tooltip-ex').html();
		var val = {};
		val.budget = number_format(ex_dataSets[b]['budget'], 2, ',', ' ');
		val.ordered = number_format(ex_dataSets[b]['ordered'], 2, ',', ' ');
		val.forecast = number_format(ex_dataSets[b]['forecast'], 2, ',', ' ');
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
	seriesGroups:
		[
			{
				type: 'splinearea',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: ex_maxValue,
					unitInterval: ex_maxValue,
					description: '',
					displayValueAxis: false,
				},
				series: [
						{ dataField: 'ordered', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2', tooltip: ''},
						{ dataField: 'budget', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79',tooltip: ''},
						{ dataField: 'forecast', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352', tooltip: ''}

					]
			},
		]
};
// END prepare jqxChart externals settings
$(document).ready(function () {
    $('#progress-line-ex').stop().animate({scrollLeft: $('#chartContainerEx').width()}, 200);
    
    // setup the chart
    $('#chartContainerEx').jqxChart(ex_settings);
});
</script>