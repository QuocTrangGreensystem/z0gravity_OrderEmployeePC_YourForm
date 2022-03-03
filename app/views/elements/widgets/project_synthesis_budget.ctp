<?php echo $html->css(array(
    'preview/project_finance_index_plus',
	'preview/project_budget_external'
));
echo $html->script(array(
    'history_filter',
	'progresspie/jquery-progresspiesvg-min',
	'dashboard/jqx-all',
	'dashboard/jqxchart_preview',
	'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider'
));
$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
$titleMenu = ClassRegistry::init('Menu')->find('first', array(
	'recursive' => -1,
	'conditions' => array(
		'company_id' => $company_id,
		'model' => 'project',
		'controllers' => 'project_budget_synthesis',
		'functions' => 'index'
	),
	'fields' => array('name_eng','name_fre')
));
$langCode = Configure::read('Config.langCode');
$language = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
$widget_title = !empty( $widget_title) ? $widget_title : $titleMenu['Menu'][$language];
$widget_title = !empty( $widget_title) ? $widget_title : __('Synthesis Budget', true); 
$define_colors = array(
	'blue' => '#217FC2', // Blue
	'red' => '#E94754', // Red
	'green' => '#6EAF79', // Green
);
$bootstrap_column = 12;
$col = count( array_filter( $options));
if( ($col == 0) && count( $options)){
	foreach( $options as &$m){
		$m = 1;
	}
	$col = count( $options);
} 
if( $widget_sizex == 2) $bootstrap_column /= $col;
$class_col = 'wd-col wd-col-md-'. $bootstrap_column ;
$defaultVals = array(
	'internal_costs_budget' => 0,
	'external_costs_budget' => 0,
	'internal_costs_forecast' => 0,
	'external_costs_forecast' => 0,
	'internal_costs_var' => 0,
	'external_costs_var' => 0,
	'internal_costs_engaged' => 0,
	'external_costs_ordered' => 0
);
$valBudgetSyns = array_merge($defaultVals, $valBudgetSyns);
$valBudgetSyns['external_costs_engaged'] = $valBudgetSyns['external_costs_ordered'];
$valBudgetSyns['total_costs_budget'] = $valBudgetSyns['internal_costs_budget'] +$valBudgetSyns['external_costs_budget'];
$valBudgetSyns['total_costs_forecast'] = $valBudgetSyns['internal_costs_forecast'] + $valBudgetSyns['external_costs_forecast'];
$valBudgetSyns['total_costs_var'] = $valBudgetSyns['internal_costs_var'] = $valBudgetSyns['external_costs_var'];
$valBudgetSyns['total_costs_engaged'] = $valBudgetSyns['internal_costs_engaged'] +$valBudgetSyns['external_costs_engaged'];
$data = array();

foreach( array_filter( $options) as $index => $value){
	$key = $value['model'];
	// $key = $value[];
	switch($key){
		case 'SynthesisBudget':
			$key = 'total';
			$data[$key]['title'] = __d(sprintf($_domain, 'Total_Cost'), 'Total Costs', true);
			$data[$key]['t_domain'] = 'Total_Cost';
			break;
		case 'BudgetInternal':
			$key = 'internal';
			$data[$key]['title'] = __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost', true);
			$data[$key]['t_domain'] = 'Internal_Cost';
			break;
		case 'BudgetExternal':
			$key = 'external';
			$data[$key]['title'] = __d(sprintf($_domain, 'External_Cost'), 'External Cost', true);
			$data[$key]['t_domain'] = 'External_Cost';
			$data[$key]['text_engaged'] = __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true);
			break;
	}
	$data[$key] += array(
		'budget' => $valBudgetSyns[$key.'_costs_budget'],
		'text_budget' => __d(sprintf($_domain, $data[$key]['t_domain']), 'Budget €', true),
		'forecast' => $valBudgetSyns[$key.'_costs_forecast'],
		'text_forecast' => __d(sprintf($_domain, $data[$key]['t_domain']), 'Forecast €', true),
		'engaged' => $valBudgetSyns[$key.'_costs_engaged'],
	);
	if( !isset($data[$key]['text_engaged'])) $data[$key]['text_engaged'] =  __d(sprintf($_domain, $data[$key]['t_domain']), 'Engaged €', true);
}
foreach( array_filter( $options) as $index => $value){
	$key = $value['model'];
	switch($key){
		case 'SynthesisBudget':
			$key = 'total';
			if($value['model_display'] == 0 ) unset($data[$key]);
			break;
		case 'BudgetInternal':
			$key = 'internal';
			if($value['model_display'] == 0 ) unset($data[$key]);
			break;
		case 'BudgetExternal':
			$key = 'external';
			if($value['model_display'] == 0 ) unset($data[$key]);
			break;
	}
}
$show_chart = isset($show_chart) ? $show_chart : 0;

//Ticket #1057
$unit = __('$', true);
$currency_budget = !empty($currency_budget) ? $currency_budget :  $unit;
$switch_inter_md = isset($filter_render['switch_inter_md']) ?$filter_render['switch_inter_md'][0] : 0;// 0: MD / 1: Euro
$switch_title = array(
	sprintf(__('View %s', true), $currency_budget),
	sprintf(__('View %s', true), __('M.D', true))
);
$switch_title = $switch_title[$switch_inter_md];
?>
<div class="wd-widget project-synthesis-budget-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
			<div class="widget-action">
			
				<div class="wd-input wd-checkbox-switch" title="<?php __('Toggle chart');?>"><input type="checkbox" name="data[show_chart]" id="SynthesisShowChart" value="1" <?php if( $show_chart) echo 'checked="checked"';?> style="display: none;"><label for="SynthesisShowChart"><span class="wd-btn-switch"><span></span></span></label></div>
				
				<a href="javascript:void(0);" onclick="wd_project_synthesis_budget_expand(this)" class="primary-object-expand"><img src="/img/new-icon/expand_white.png"></a>
				<a href="javascript:void(0);" onclick="wd_project_synthesis_budget_collapse(this)" class="primary-object-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
			</div>
        </div>
        <div class="widget_content loading-mark color-background wg-sizex-<?php echo $widget_sizex;?>">
            <div id="widget-synthesis-budget">
                <div class="wd-row count-col-<?php echo $col;?>">
				<?php foreach($data as $key => $val){ 
					$val['budget'] = (float) $val['budget'];
					$prg = !empty($val['budget']) ? number_format($val['forecast'] / $val['budget'] * 100, 2, '.', '') : 100;
					$progress_euro = '';
					if( $key == 'internal' ){
						$progress_euro = 'progress_euro';//dat class de hidden khi switch
						$lang_budget = __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true);
						$lang_forecast = __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true);
						$lang_engaged = __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true);
						$lang_budget_md = __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true);
						$lang_forecast_md = __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true);
						$lang_engaged_md = __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true);
					}else{
						$lang_budget = __d(sprintf($_domain, 'External_Cost'), 'Budget €', true);
						$lang_forecast = __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true);
						$lang_engaged = __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true);
					}
				?> 
					<div class="<?php echo $class_col. ' ' . $key;?>">
						
						<?php if($key == 'internal'){ ?>
							<div class="wd-input wd-checkbox-switch" title="<?php echo $switch_title;?>"><input type="checkbox" name="switch_inter_md" id="InternalChartSwitch" value="1" <?php if( $switch_inter_md) echo 'checked="checked"';?> style="display: none;"><label for="InternalChartSwitch"><span class="wd-btn-switch"><span></span></span></label></div>
						<?php }?>
						
						<div class="column-chart">
						<!-- PIE -->
						<div class="chard-<?php echo $key;?> wd-budget-chart clear-fix">
							<div class="budget-progress-circle">
								<div id="progress-circle-<?php echo $key;?>" class="wd-progress-pie" data-val="<?php echo $prg;?>"></div>
							</div>
							<div class="progress-values <?php echo $progress_euro;?>">
								<h3 class="wd-t1"><?php echo $val['title'];?></h3>
								<div class ="progress-value progress-budget"><p><?php echo $lang_budget;?></p><span><?php echo number_format($val['budget'], 2, ',', ' ') . ' ' .$currency_budget;?></span></div>
								<div class ="progress-value progress-forecast"><p><?php echo $lang_forecast;?></p><span><?php echo number_format($val['forecast'], 2, ',', ' ') . ' ' .$currency_budget;?></span></div>
								<div class ="progress-value progress-engaged"><p><?php echo $lang_engaged;?></p><span><?php echo number_format($val['engaged'], 2, ',', ' ') . ' ' .$currency_budget;?></span></div>
							</div>
							<?php if($key == 'internal'){ ?>
								<div class="progress-values progress_md wd-hide">
									<h3 class="wd-t1"><?php echo $val['title'];?></h3>
									<div class ="progress-value progress-budget"><p><?php echo $lang_budget_md;?></p><span><?php echo number_format($valBudgetSyns['internal_costs_budget_man_day'], 2, ',', ' ') . ' ' .__('M.D', true);?></span></div>
									<div class ="progress-value progress-forecast"><p><?php echo $lang_forecast_md;?></p><span><?php echo number_format($valBudgetSyns['internal_costs_forecasted_man_day'], 2, ',', ' ') . ' ' .__('M.D', true);?></span></div>
									<div class ="progress-value progress-engaged"><p><?php echo $lang_engaged_md;?></p><span><?php echo number_format($getDataProjectTasks['consumed'], 2, ',', ' ') . ' ' .__('M.D', true);?></span></div>
								</div>
							<?php }?>
						</div>
						
						<!-- Line -->
						<div class="graph-<?php echo $key;?> wd-budget-graph">	
							<div class="progress-line-inner">
								<div id="chart-<?php echo $key; ?>-container" class="chart-container"> </div>
							</div>
							<!-- 
							<span id="chart-<?php echo $key; ?>-left" class="scroll-progress scroll-progress-left"></span>
							<span id="chart-<?php echo $key; ?>-right" class="scroll-progress scroll-progress-right"></span>
							-->
						</div>
						
						<!-- Tooltip -->
						<div class="jqx-format-tooltip template-<?php echo $key;?>-popup" style="display: none; height: 50px">
							<ul class="content-tooltip">
								<li class="budget"><?php echo $val['text_budget']; ?>: <span>{budget} <?php echo $currency_budget;?></span></li>
								<li class="forecast"><?php echo $val['text_forecast']; ?>: <span>{forecast}<?php echo $currency_budget;?></span></li>
								<li class="engaged"><?php echo $val['text_engaged'];; ?>: <span>{engaged}<?php echo $currency_budget;?></span></li>
							</ul>
						</div>
						</div>
					</div>
				<?php } ?> 
                </div>
            </div>
        </div>
    </div>	
</div>
<div style="clear: both"></div>

<style type="text/css">
	.progress-value p{
		margin-bottom: 0;
	}
	.project-synthesis-budget-widget .progress-value p {
		font-size: 14px;
		font-weight: 400;
		line-height: 36px;
		display: inline-block;
	}
	.project-synthesis-budget-widget .wd-budget-graph{
		position: relative;
		display: none;
		overflow-y: auto;
	}
	.project-synthesis-budget-widget .chart-container{
		height: 200px;
		min-width: 100%;
	}
	.chart-container table tbody tr:hover td{
		background: inherit;
	}
	.project-synthesis-budget-widget .column-chart {
		padding: 20px;
		box-shadow: 0 0 10px 0px #d9dbdc5c;
		animation:  0.3s ease;
		border-radius: 2px;
	}
	.project-synthesis-budget-widget .column-chart:hover {
		box-shadow: 1px 1px 10px 0px #7778795c;
		border-radius:  8px;
	}
	.project-synthesis-budget-widget.fullScreen .wd-row.count-col-3 .wd-col {
		width:  33.333333%;
	}
	.project-synthesis-budget-widget.fullScreen .wd-row.count-col-2 .wd-col {
		width:  50%;
	}
	.wd-input.wd-checkbox-switch label .wd-btn-switch {
		height: 20px;
	}
	.wd-col.internal{ position: relative;}
	.internal .wd-input.wd-checkbox-switch {
		position: absolute;
		top: 20px;
		right: 20px;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<script type="text/javascript">
(function($){
	var project_id = <?php echo json_encode($project_id);?>;
	var wg_container = $('.project-synthesis-budget-widget');
	var currency_budget = <?php echo json_encode($currency_budget);?>;
	var man_day = "<?php echo __('M.D', true);?>";
	var pie_progress = wg_container.find('.wd-progress-pie');
	var progress_width = 124;
	var wgSynData_show_chart = <?php echo json_encode($show_chart);?>;
	var wgSynData_switch_view = <?php echo json_encode($switch_inter_md);?>;
	var wgSynData = {};
	var internal_chart_changed = false;
	pie_progress.addClass('wd-progress-pie').setupProgressPie({
		size: progress_width ? progress_width : 140,
		strokeWidth: 8,
		ringWidth: 8,
		ringEndsRounded: true,
		strokeColor: "#e0e0e0",
		color: function(value){
			var red = 'rgba(233, 71, 84, 1)';
			var green = 'rgba(110, 175, 121, 1)';
			return pie_progress.data('val') > 100 ? red : green;
		},
		valueData: "val",
		contentPlugin: "progressDisplay",
		contentPluginOptions: {
			fontFamily: 'Open Sans',
			multiline: [
				/*{
					cssClass: "progresspie-progressText",
					fontSize: 11,
					textContent: '', //('<?php __('Progress');?>').toUpperCase(),
					color: '#ddd',
				}, */
				{
					cssClass: "progresspie-progressValue",
					fontSize: 28,
					textContent: '%s%' ,
					color: function(value){
						var red = 'rgba(233, 71, 84, 1)';
						var green = 'rgba(110, 175, 121, 1)';
						return pie_progress.data('val') > 100 ? red : green;
					}
				},
			],
			fontSize: 28
		},
		animate: {
			dur: "1.5s"
		}
	}).progressPie();
	
	wg_container.find(".scroll-progress").click(function() {
		var _this = $(this);
		var dir = _this.hasClass('scroll-progress-right') ? '+=' : '-=' ;
		$(this).closest(".amrs-wrap").find('#progress-line').stop().animate({scrollLeft: dir+'500'}, 1000);
	});
	$(document).ready(function () {
		if( wgSynData_switch_view) wgSwitchView('#InternalChartSwitch');
		$('#InternalChartSwitch').on('change', function(){
			wgSwitchView('#InternalChartSwitch');
		});
		if( wgSynData_show_chart) wgToggleChart('#SynthesisShowChart');
		$('#SynthesisShowChart').on('change', function(){
			wgToggleChart(this);
		});
	});
	function wdToggleChart(elm){
		console.error('toggle');
		var _this = $(elm);
		var chart_container = $('.wd-budget-graph .progress-line-inner');
		if( _this.is(':checked')){
			$.each( chart_container, function (i, chart){
				var _this = $(this);
				_this.show().animate( {
					height: 204
				}, {
					complete: function(){
						_this.jqxChart('update');
						_this.closest('.progress-line').find('.scroll-progress').show();
						_this.parent().animate({scrollLeft: _this.width()},200);
					},
					duration: 200,
				});
			});
			
		}else{
			chart_container.closest('.progress-line').find('.scroll-progress').hide();
			chart_container.animate( {
				height: 0
			}, {
				complete: function(){
					chart_container.hide();
				},
				duration: 200,
			});
		}
	}
	function wgToggleChart(elm){
		if( 'dataSyns' in wgSynData){
			wdToggleChart(elm);
			if( internal_chart_changed) redraw_internal_progress_line(wgSynData);
		}else{
			wg_container.find('.loading-mark').addClass('loading');
			setTimeout( function(){
				$.ajax({
					url: '/project_budget_synthesis/progress_line/' + project_id + '/ajax',
					type: 'get',
					dataType: 'json',
					success: function(res){
						wgSynData = res;
						wgSynData.switch_inter_md = wgSynData_switch_view;
						wdToggleChart(elm);
						draw_all_progress_line(wgSynData);
					},
					complete: function(){
						wg_container.find('.loading-mark').removeClass('loading');
					}
				});
			}, 50);	
		}
	};
	function redraw_internal_progress_line(wgSynData){
		if( !wg_container.find('.graph-internal').length) return;
		if(wgSynData.switch_inter_md == 1){
			draw_progress_line( 'internal',  wg_container.find('.graph-internal'), wgSynData.dataset_internals, wgSynData.max_internal_md, wgSynData.countLineIn, wgSynData.switch_inter_md);
		} else {
			draw_progress_line( 'internal',  wg_container.find('.graph-internal'), wgSynData.dataset_internals, wgSynData.max_internal_euro, wgSynData.countLineIn, wgSynData.switch_inter_md);
		}
		internal_chart_changed = false;
	}
	function wgSwitchView(elm){
		internal_chart_changed = true;
		wgSynData_switch_view = $('#InternalChartSwitch').is(':checked');
		wgSynData.switch_inter_md = wgSynData_switch_view ? 1 : 0;
		wg_container.find('.progress_md').toggleClass('wd-hide', !wgSynData_switch_view);
		wg_container.find('.progress_euro').toggleClass('wd-hide', wgSynData_switch_view);
		if( 'dataSyns' in wgSynData){
			if( $('#SynthesisShowChart').is(':checked')){ redraw_internal_progress_line(wgSynData);}
		}
	};
	
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
	function draw_all_progress_line(wgSynData){
		if( wg_container.find('.graph-total').length){
			draw_progress_line( 'total', wg_container.find('.graph-total'), wgSynData.dataSyns, wgSynData.manDayMaxSyn, wgSynData.countLineSyn, wgSynData.switch_inter_md);
		}
		if(wgSynData.switch_inter_md == 1){
			if( wg_container.find('.graph-internal').length){				
				draw_progress_line( 'internal',  wg_container.find('.graph-internal'), wgSynData.dataset_internals, wgSynData.max_internal_md, wgSynData.countLineIn, wgSynData.switch_inter_md);
			}
		} else {
			if( wg_container.find('.graph-internal').length){
				draw_progress_line( 'internal',  wg_container.find('.graph-internal'), wgSynData.dataset_internals, wgSynData.max_internal_euro, wgSynData.countLineIn, wgSynData.switch_inter_md);
			}
		}
		if( wg_container.find('.graph-external').length){
			draw_progress_line( 'external', wg_container.find('.graph-external'), wgSynData.dataset_externals, wgSynData.max_externals, wgSynData.countLineExter, wgSynData.switch_inter_md);
		}
	}
	function draw_progress_line(type, container, dataSets, maxheight, count, switch_inter_md){
		var source = [];
		$.each(dataSets, function( date, data){
			var item = {};
			item.date_format = data.date_format;
			switch(type){
				case 'total':
					item.budget = data.budget;
					item.forecast = data.forecast;
					item.engaged = data.engared;					
				break;
				case 'internal':
					if(switch_inter_md == 1){
						item.budget = data.budget_md;
						item.forecast = data.validated;
						item.engaged = data.consumed;
					} else {
						item.budget = data.budget_price;
						item.forecast = data.validated_price;
						item.engaged = data.consumed_price;
					}
				break;
				case 'external':
					item.budget = data.budget;
					item.forecast = data.forecast;
					item.engaged = data.ordered;					
				break;
			}
			source.push(item);
		});
		var settings = {
			title: "",
			description: '',
			padding: { left: 33, top: 20, right: 25, bottom: 0 },
			titlePadding: { left: 90, top: 20, right: 0, bottom: 0 },
			source: source,
			showBorderLine: false,
			isFormatTooltip: true,
			enableAnimations: true,
			showLegend: true,
			toolTipFormatFunction : function(a, b, data, d){
				var template = container.next('.jqx-format-tooltip').html();
				if(type == 'internal' && switch_inter_md == 1){
					template = replaceAll(currency_budget, man_day, template);
				}
				var val = {};
				val.budget = number_format(source[b]['budget'], 2, ',', ' ');
				val.forecast = number_format(source[b]['forecast'], 2, ',', ' ');
				val.engaged = number_format(source[b]['engaged'], 2, ',', ' ');
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
							maxValue: maxheight,
							unitInterval: maxheight,
							description: '',
							displayValueAxis: false,
						},
						series: [
								{ dataField: 'engaged', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2', tooltip: ''},
								{ dataField: 'budget', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79',tooltip: ''},
								{ dataField: 'forecast', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352', tooltip: ''}

							]
					},
				]
		};
		if(type != 'external'){
			settings.seriesGroups = [
				{
					type: switch_inter_md ? 'splinearea' : 'line',
					showLabels: false,//default
					valueAxis:
					{
						axisSize: 'auto',
						minValue: 0,
						maxValue: maxheight,
						unitInterval: maxheight,
						description: '',
						displayValueAxis: false,
						valuesOnTicks: true
					},
					dashStyle: switch_inter_md ? false : '5.5',
					series: [
						{ dataField: 'forecast', lineWidth: 2, displayText: '<?php echo __('Forecast', true);?>', labelOffset: {x: 0, y: 0}, color: '#F05352', tooltip: ''}
					]
				},
				{
					type: 'splinearea',
					showLabels: false,//default
					valueAxis:
					{
						axisSize: 'auto',
						minValue: 0,
						maxValue: maxheight,
						unitInterval: maxheight,
						description: '',
						displayValueAxis: false,
					},
					series: [
						{ dataField: 'budget', lineWidth: 2, displayText: '<?php echo __('Budget', true);?>', labelOffset: {x: 0, y: 0}, color: '#6EAF79',tooltip: ''},
						{ dataField: 'engaged', lineWidth: 2, displayText: '<?php echo __('Engaged', true);?>', labelOffset: {x: 0, y: 0}, color: '#217FC2', tooltip: ''},

					]
				},
			];
		}
		var pr_graph = container.show().find('.chart-container').first();
		pr_graph.width( count * 40);
		pr_graph.jqxChart(settings);
		// container.stop().animate({scrollLeft: pr_graph.width()}, 200);
	}
})(jQuery);

function wd_project_synthesis_budget_expand(_element){
	var _this = $(_element);
	var _wg_container = _this.closest('.wd-widget');
	_wg_container.addClass('fullScreen');
	_wg_container.closest('li').addClass('wd_on_top');
	_this.hide();
	_wg_container.find('.primary-object-collapse').show();
	_this.closest('.wd-col').css('width', '100%').siblings().hide();
	_this.closest('.wd-row').siblings().hide();
	$('#wd-container-header-main, .wd-indidator-header').hide();
	$('#layout').addClass('widget-expand');
	
}
function wd_project_synthesis_budget_collapse(_element){
	var _this = $(_element);
	var _wg_container = _this.closest('.wd-widget');
	_wg_container.removeClass('fullScreen');
	_wg_container.closest('li').removeClass('wd_on_top');
	_this.hide();
	_wg_container.find('.primary-object-expand').show();
	_this.closest('.wd-col').css('width', '').siblings().show();
	_this.closest('.wd-row').siblings().show();
	$('#wd-container-header-main, .wd-indidator-header').show();
	$('#layout').removeClass('widget-expand');
	$('#expand').show();
	$('#table-collapse').hide();
}
</script>
