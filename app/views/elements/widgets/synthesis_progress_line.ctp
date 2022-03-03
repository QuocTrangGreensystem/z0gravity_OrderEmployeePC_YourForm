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
.wd-budget-chart #chartContainerSyn {
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
                    <div id ="progress-line-total" class="progress-line-inner">
                        <div id='chartContainerSyn' style="width:<?php echo ($countLineSyn * 50); ?>px; height: <?php echo (!empty($chartHeight) ? $chartHeight : 240);?>px">
                        </div>
                    </div>
                    <span id="chartContainerSyn-left" class="scroll-progress scroll-progress-left"></span>
                    <span id="chartContainerSyn-right" class="scroll-progress scroll-progress-right"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
	$unit = __('M.D', true);
	$currency_syn = !empty($budget_settings) ? $budget_settings :  $unit;
?>
<div id="jqx-format-tooltip-total" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true); ?>: <span>{budget} <?php echo $currency_syn;?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true); ?>: <span>{forecast}<?php echo $currency_syn;?></span></li>
		<li class="ordered"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true); ?>: <span>{ordered}<?php echo $currency_syn;?></span></li>
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
	$("#chartContainerSyn-right, #chartContainerSyn-left").click(function() {
		var dir = this.id=="chartContainerSyn-right" ? '+=' : '-=' ;
		$(this).closest(".amrs-wrap").find('.progress-line-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
	});
	
})(jQuery);
$(document).ready(function () {
    $('#progress-line-total').stop().animate({scrollLeft: $('#chartContainerSyn').width()}, 200);
    // prepare jqxChart settings
    var manDayMaxSyn    = <?php echo json_encode($manDayMaxSyn); ?>,
        currency    = <?php echo json_encode($currency_syn); ?>,
        dataSyns    = <?php echo !empty($dataSyns) ? json_encode(array_values($dataSyns)) :json_encode(array()) ?>,
        type    = <?php echo !empty($type) ? json_encode($type) : json_encode('') ?>;
	var tooltip = 	
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
	
    var settings = {
        title: "",
        description: '',
        padding: { left: 20, top: 20, right: 20, bottom: 20 },
        titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
        source: dataSyns,
		showBorderLine: false,
		isFormatTooltip: true,
		enableAnimations: true,
		showLegend: true,
		toolTipFormatFunction : function(a, b, data, d){
			var template = $('#jqx-format-tooltip-total').html();
			var val = {};
			val.budget = number_format(dataSyns[b]['budget'], 2, ',', ' ');
			val.ordered = number_format(dataSyns[b]['engared'], 2, ',', ' ');
			val.forecast = number_format(dataSyns[b]['forecast'], 2, ',', ' ');
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
        seriesGroups:[
			{
				type: 'line',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: manDayMaxSyn,
					unitInterval: manDayMaxSyn,
					description: '',
					displayValueAxis: false,
					valuesOnTicks: true
				},
				dashStyle: '5,5',
				series: [ { dataField: 'forecast', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352', tooltip: ''} ]
			},
			{
				type: 'splinearea',
				showLabels: false,//default
				valueAxis:
				{
					axisSize: 'auto',
					minValue: 0,
					maxValue: manDayMaxSyn,
					unitInterval: manDayMaxSyn,
					description: '',
					displayValueAxis: false,
				},
				series: [
						{ dataField: 'engared', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2', tooltip: ''},
						{ dataField: 'budget', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79',tooltip: ''},
						// { dataField: 'forecast', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352', tooltip: ''}

					]
			},
		]
    };
    // setup the chart
    $('#chartContainerSyn').jqxChart(settings);
});
</script>