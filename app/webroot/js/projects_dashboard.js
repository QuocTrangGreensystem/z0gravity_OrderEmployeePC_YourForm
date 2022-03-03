// var ProjectsDashboard;
( function($){
	'use strict';
	// it only does '%s', and return '' when arguments are undefined
    var sprintf = function (str) {
        var args = arguments,
            flag = true,
            i = 1;

        str = str.replace(/%s/g, function () {
            var arg = args[i++];

            if (typeof arg === 'undefined') {
                flag = false;
                return '';
            }
            return arg;
        });
        return flag ? str : '';
    };
	var progress_euro_max = 0;
    var progress_md_max = 0;
    var progress_external_euro_max = 0;
    var progress_synthesis_euro_max = 0;
	var synthesis_chart_fields = ['budget_price', 'forecast_price', 'consumed_price'];
	var internal_chart_fields = ['budget_price', 'validated_price', 'consumed_price'];
	var internalMD_chart_fields = ['budget_md', 'validated', 'consumed'];
	var external_chart_fields = ['budget_price', 'forecast_price', 'ordered_price'];
	var data_internal = [];
	var data_internal_md = [];
	var data_external_euro = [];
	var data_synthesis_euro = [];
	var data_finance = [];
	var data_status = [];
	var data_tasks_late = [];
	var data_project_yesno = [];
	var data_test = [];
	var m_circle_colors = [];
	var field_number = {};
	var field_yesno = {};
	var test_data = {};
	var progress_inv_max = 0;
    var progress_finaninv_max = 0;
    var progress_fon_max = 0;
    var progress_finanfon_max = 0;
	var _time = new Date();
	var finan_curent_selected = _time.getFullYear();
	var ProjectsDashboard = function($element, options){
		var dashboard = this;
		dashboard.ready = true;
		dashboard.has_change = false;
		dashboard.el = $element;
		if( typeof $element == 'undefined' || !$element.length){
			console.warn('Empty element ');
		}
		var _def = {
			display: true,
			classes: '',
		};
		dashboard.options = $.extend({}, _def, options);
		if( 'ready' in dashboard.options) dashboard.ready = dashboard.options.ready;
		dashboard.display = dashboard.options.display;
		dashboard.share_popup = $(dashboard.options.share_popup);
		if( !dashboard.share_popup.length){
			console.warn('Empty share_popup element ');
		}
		dashboard.$parent = $( sprintf(
			'<div id="dash-projects-dashboard" class="dashboard-projects %s" />',
			+ dashboard.options.classes
			));
		if( dashboard.el) {
			dashboard.el = dashboard.el.first().prepend(dashboard.$parent);
		}
		dashboard.projectIds = dashboard.options.projectIds || { };
		dashboard.current_employee = dashboard.options.current_employee || { };
		dashboard.all_widget = dashboard.options.all_widget || { };
		dashboard.enable_widget = dashboard.options.enable_widget || [];
		dashboard.list_select_yourform = dashboard.options.list_select_yourform || [];
		dashboard.list_yesno_yourform = dashboard.options.list_yesno_yourform || [];
		dashboard.list_number_yourform = dashboard.options.list_number_yourform || [];
		dashboard.list_multiselect_yourform = dashboard.options.list_multiselect_yourform || [];
		dashboard.list_plan_capacity = dashboard.options.list_plan_capacity || [];
		dashboard.data = {};
		dashboard.data.sumActivities = dashboard.options.sumActivities || {};
		dashboard.multi_dashboard_container = dashboard.options.multi_dashboard_container || '';
		dashboard.dashboard_histories = dashboard.options.dashboard_histories || [];
		dashboard.all_widget_default = dashboard.options.all_widget_default || [];
		dashboard.def_name = 'Dashboard';
		dashboard.history_path = 'dashboard_history';
		dashboard.before_init = dashboard.options.before_init || '';
		this.init();
		return this;
	}
	ProjectsDashboard.prototype = {
        constructor: ProjectsDashboard,	
		init: function(){
			var dashboard = this;
			var extend_function = {};
			if( typeof dashboard.before_init == 'function') dashboard.before_init(dashboard);
			$.each(dashboard.all_widget, function( key, widgets){
				$.each(widgets, function( widget_name, widget_setting){
					var func_get = 'get_data_' + widget_name;
					if( typeof dashboard[func_get] !== 'function'){
						extend_function[func_get] = function(){
							// console.warn('Function ProjectsDashboard.' + func_get + ' is not defined');
							return [];
						}
					}
					var func_draw = 'draw_' + widget_name;
					if( typeof dashboard[func_draw] !== 'function'){
						extend_function[func_draw] = function(data){
							// console.warn('Function ProjectsDashboard.' + func_draw + ' is not defined');
							return '';
						}
					}
				});
			});
			if( !$.isEmptyObject(extend_function) ) $.extend( ProjectsDashboard.prototype, extend_function);
			this.after_init();
			// this.drawAll();
		},
		get: function($key){
			if( typeof this[$key] == 'undefined') return '';
			return this.recusive_copy_object(this[$key]);
		},
        getInstance: function(){
            return this;
        },
		set: function($key, $val){
			this[$key] = $val;
			return $val;
		},
		get_list_widget_enable: function(){
			return this.enable_widget;
		},
		get_data_widget: function(widget_name){
			// phat trien sau
		},
		drawAll: function(){
			// console.error( 'draw');
			var dashboard = this;
			if( !dashboard.ready) return;
			// if( typeof dashboard.before_draw == 'function') dashboard.before_draw();
			dashboard.$parent.empty();
			if( !dashboard.display) return;
			dashboard.get_displayed_projects();
			if( dashboard.multi_dashboard_container){
				var multi_display_container = $(dashboard.multi_dashboard_container);
				if( dashboard.display && multi_display_container.find('.dash_multi_dashboard_select').length) multi_display_container.find('.dash_multi_dashboard_select').show();
			}
			var acti_dashboard = dashboard.get_dashboard_acti();
			if( $.isEmptyObject(dashboard.all_widget)) {
				dashboard.all_widget = dashboard.get('all_widget_default');
			}
			var dashboard_histories = dashboard.get('dashboard_histories');
			if( acti_dashboard in dashboard_histories){
				var _dash = dashboard_histories[acti_dashboard];
				if( _dash.employee_id != dashboard.current_employee) {
					dashboard.el.find('.projects-dashboard-setting').hide();
				}else{
					dashboard.el.find('.projects-dashboard-setting').show(); 
				}
			}
			
			
			$.each(dashboard.all_widget, function( key, widgets){
				dashboard.$parent.append($('<div class="dash-widget-row ' + key + '"><div class = "dash-widget-row-inner"></div></div>'));
				$.each(widgets, function( widget_name, widget_setting){
					if($.inArray( widget_name, dashboard.enable_widget ) != -1 ){
						var func_get = 'get_data_' + widget_name;
						var data = dashboard[func_get]();
						var func_draw = 'draw_' + widget_name;
						var _html = dashboard[func_draw](data, widget_setting);
						var wg = dashboard.$parent.find('.dash-widget-item.' + widget_name ).first();
						if( !wg.length) dashboard.$parent.find('.dash-widget-row.' + key).children().append($('<div class="dash-widget-item ' + widget_setting.classes + ' ' + widget_name + '" ' + (widget_setting.id ? ('id="' + widget_setting.id + '"') : '') + '/>'));
						wg = dashboard.$parent.find('.dash-widget-item.' + widget_name ).first();
						wg.html( _html );
					}
				});
			});
			dashboard.async_draw();
			dashboard.drag_event();
			dashboard.edit_number_event();
			dashboard.selected_pc_forecast();
			dashboard.selected_date_budget();
			dashboard.el.removeClass('loading');
			$(window).trigger('resize');
		},
		async_draw: function(){
			var dashboard = this;
			this.draw_progress_budget_circle($('#dash-external-circle'));
			this.draw_progress_budget_circle($('#dash-widget-circle'));
			this.draw_progress_budget_circle($('#dash-widget-circle-md'));
			this.draw_progress_budget_circle($('#dash-syns-circle'));
			if(field_number){
				$.each(field_number, function( key, field_name){
					var attr_field = $('#dash-widget-circle-' + field_name);
					if(attr_field.length > 0) dashboard.draw_progress_budget_circle(attr_field);
				});
			}
			this.draw_progress_multi_circle($('#circle-task-graph'), data_status, false);
			// this.draw_progress_tasks();
			this.draw_progress_multi_circle($('#circle-progress-task-graph'), data_tasks_late, true);
			if(field_yesno){
				$.each(field_yesno, function( key, field_name){
					var yesno_field = $('#circle-project-yesno-graph-' + field_name);
					data_test[0] = test_data[field_name];
					data_test[1] = 100 - test_data[field_name];
					if(yesno_field.length > 0) dashboard.draw_progress_multi_circle((yesno_field),data_test,true);
				});
			}
			this.draw_progress_budget_line($('#chartContainerEx'), data_external_euro, progress_external_euro_max, external_chart_fields, $('#jqx-format-tooltip-ex'));
			this.draw_progress_budget_line($('#chartContainerIn'), data_internal, progress_euro_max, internal_chart_fields, $('#jqx-format-tooltip-in'));
			
			this.draw_progress_budget_line($('#chartContainerInMD'), data_internal_md, progress_md_max, internalMD_chart_fields, $('#jqx-format-tooltip-in-md'));
			this.draw_progress_budget_line($('#chartContainerSynth'), data_synthesis_euro, progress_synthesis_euro_max, synthesis_chart_fields, $('#jqx-format-tooltip-synth'));
			this.draw_async_progress_chart();
			this.draw_progress_coulumn_finance('#chartFinanaceInv', 'inv', progress_inv_max);
			this.draw_progress_coulumn_finance('#chartFinanaceFon', 'fon', progress_fon_max);
			this.draw_progress_coulumn_finance('#chartFinanaceFinanInv', 'finaninv', progress_finaninv_max);
			this.draw_progress_coulumn_finance('#chartFinanaceFinanFon', 'finanfon', progress_finanfon_max);
			
			// Load data forecast
			if((dashboard.list_plan_capacity).length > 0){
				$.each((dashboard.list_plan_capacity), function( index, key){
					var _input = $('select[name="'+ key +'"]');
					
					if(_input.length > 0){
						var _plan_id = _input.val();
						dashboard.ajaxGetPCForecast(_plan_id, _input);
					}
				});
			}
			var plan_pc = $('.dash_plan_capacity').find('.pc_forecast');
	
			if(plan_pc.length > 0){
				plan_pc.multiSelect({
					noneSelected: i18n['Select team'], 
					appendTo : $('body'),
					oneOrMoreSelected: '*',
					selectAll: false,
					cssClass: 'plane-select slickgrid-multiSelect',
					oneSelected: true,
					noHistory: true,
				},function(){
					var _input = this;
					var _plan_id = _input.find('.selected-item').data('id');
					if(typeof _plan_id !== 'undefined' ){
						var list_pc_manager = dashboard.data.Company.ProfitCenterManager;
						var avatar = typeof list_avatar[list_pc_manager[_plan_id]] !== 'undefined' ? list_avatar[list_pc_manager[_plan_id]]['tag'] : '';
						$(this).closest('.dash-widget-title').find('.manager-avatar').empty().append(avatar);
						dashboard.saveHistory();
						dashboard.ajaxGetPCForecast(_plan_id, _input);
					}
				});
			}
		},
		draw_progress_coulumn_finance: function(_element, type, maxValue){
			maxValue = maxValue || 500;
			if( typeof data_finance !== 'undefined' && data_finance.length > 0 && maxValue > 0){
				var field1 = type + '_budget';
				var field2 = type + '_avancement';	
				var settings = {
					title: "",
					description: "",
					padding: { left: 5, top: 5, right: 5, bottom: 5 },
					titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
					source: data_finance,
					defaultUnit: budget_settings,
					categoryAxis:
					{
						dataField: 'year',
						showGridLines: false,
					},
					showLegend: false,
					colorScheme: 'scheme02',
					seriesGroups:
						[
							{
								type: 'column',
								columnsGapPercent: 20,
								seriesGapPercent: 0,
								columnsMaxWidth: 25,
								customGradient: [
									[0, 1, 1],
									[100, 1.2, 1]
								],
								
								valueAxis:
								{
									minValue: 0,
									maxValue: maxValue,
									unitInterval: Math.round(maxValue / 4),
									// formatSettings: { prefix: '$', thousandsSeparator: ',' },
									formatFunction: function (value) {
										var txtValueAxis = '';
										if(value >= 1000000){
											txtValueAxis = Math.round(value / 1000000) + 'M' + budget_settings;
										}else{
											txtValueAxis = value + budget_settings;
										}
										return txtValueAxis;
									}
									
								},
								series: [{ 
										dataField: field1, 
										color: '#F2A673',
										showLabels: false,
									},
									{ 
										dataField: field2, 
										color: '#79B2DA', 
										customGradient: [
											[0, 1, 1],
											[100, 1.2, 1]
										]
								}]
							}
						]
				};
				$(_element).jqxChart(settings);
			}
		},
		draw_progress_budget_line: function(chartElement, dataset, maxHeight, chartFields, temTooltip){
			if( !chartElement.length) return;
			if( typeof dataset !== 'undefined' &&  dataset.length > 0 && maxHeight > 0){
				var len = dataset.length;
				var budget_progress_setting = {
					title: "",
					description: '',
					padding: { left: 20, top: 20, right: 20, bottom: 20 },
					titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
					source: dataset,
					showBorderLine: false,
					enableAnimations: true,
					showLegend: true,
					isFormatTooltip: true,
					toolTipFormatFunction : function(a, b, data, d){
						var template = temTooltip.html();
						var val = {};
						val.budget = number_format(dataset[b][chartFields[0]], 2, ',', ' ');
						val.forecast = number_format(dataset[b][chartFields[1]], 2, ',', ' ');
						val.consumed = number_format(dataset[b][chartFields[2]], 2, ',', ' ');
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
							type: 'splinearea',
							showLabels: false,//default
							valueAxis:
							{
								axisSize: 'auto',
								minValue: 0,
								maxValue: maxHeight,
								unitInterval: maxHeight,
								description: '',
								displayValueAxis: false,
								valuesOnTicks: true
							},
							series: [
									{ dataField: chartFields[2], lineWidth: 2, labelOffset: {x: 0, y: 0}, color: '#217FC2'},
								]
						},
						{
							type: 'splinearea',
							showLabels: false,//default
							valueAxis:
							{
								axisSize: 'auto',
								minValue: 0,
								maxValue: maxHeight,
								unitInterval: maxHeight,
								description: '',
								displayValueAxis: false
							},
							series: [
									{ dataField: chartFields[0], lineWidth: 2, labelOffset: {x: 0, y: 0}, color: '#A7CEAE'},
									{ dataField: chartFields[1], lineWidth: 2, labelOffset: {x: 0, y: 0}, color: '#F2A673'},

								]
						},
					]
				};
				chartElement.jqxChart(budget_progress_setting);
			}
		},
		draw_progress_budget_circle: function(pie_progress){
			var progress_width = pie_progress.width();
			var color_outer = pie_progress.data('color-outer') ? pie_progress.data('color-outer') : '#79B2DA';
			var pie_content_setting = [{
				cssClass: "progresspie-progressValue",
				fontSize: 24,
				textContent: pie_progress.data('outer') ,
				color: color_outer,
			}];
			if(typeof pie_progress.data('middle') !== 'undefined'){
				pie_content_setting.push({
					cssClass: "progresspie-progressInnerValue",
					fontSize: 18,
					textContent: pie_progress.data('middle'),
					color: "#F2A673",
				});
			}
			var pie_setting = {
				size: progress_width ? progress_width : 140,
				strokeWidth: 8,
				ringWidth: 8,
				ringEndsRounded: true,
				strokeColor: "#F3F3F3",
				color: color_outer,
				valueData: 'outer',
				contentPlugin: "progressMultiDisplay",
				contentPluginOptions: {
					fontFamily: 'Open Sans',
					multiline: pie_content_setting,
					fontSize: 26
				},
				animate: {
					dur: "1.5s"
				}
			};
			if(typeof pie_progress.data('middle') !== 'undefined'){
				pie_setting.inner = {
					size: progress_width - 25 ? progress_width - 25 : 115,
					ringWidth: 7,
					strokeWidth: 7,
					color: "#F2A673",
					strokeColor: "#F3F3F3",
					ringEndsRounded: true,
					valueData: 'middle',
				};
			}
			pie_progress.addClass('wd-progress-pie').setupProgressPie(pie_setting).progressPie();
	
		},
		draw_progress_1_multi_circle_tasks: function(pie_progress, dataField){
			if(data_status.length > 0) {
				var n = 0;
				var filed_format = [];
				var line_colors = [];
				
				filed_format[0]['status'] = 'CL';
				filed_format[0]['percent'] = dataField['CL'];
				line_colors[0] = "#c34a2e";
				
				filed_format[1]['status'] = 'IP';
				filed_format[1]['percent'] = dataField['IP'];
				line_colors[1] = "#6EAF79";
				
				var source =
				{
					datatype: "array",
					datafields: [
						{ name: 'status' },
						{ name: 'percent' }
					],
					localdata: filed_format
				};
				var dataAdapter = new $.jqx.dataAdapter(source);
				$.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: line_colors });
				
				var settings = {
					title: "",
					description: "",
					enableAnimations: true,
					showLegend: false,
					showToolTips: false,
					source: dataAdapter,
					defaultUnit: '',
					colorScheme: 'myScheme',
					seriesGroups:
						[
							{
								type: 'donut',
								customGradient: [
									[1, 1, 1],
									[1, 1, 1]
								],
								borderLineColor: '#FFFFFFF',
								series:
									[
										{
											dataField: 'percent',
											displayText: 'status',
											labelRadius: 45,
											initialAngle: 15,
											radius: 60,
											innerRadius: 48,
											showLabels: false,
											centerOffset: 0,
											formatSettings: { sufix: '%'},
										}
									]
							}
						]
				};
				
				pie_progress.jqxChart(settings);
			}
		},
		options_inner_setting: function (progress_setting, opts){
			if(!progress_setting.inner){
				progress_setting.inner = opts;
				return progress_setting;
			}
			this.options_inner_setting(progress_setting.inner, opts);
		},
		
		buildOptInner: function(statuses, i, progress_width){
			var line_colors = ['#79B2DA','#D3D3D3','#6eb07a'];
			var stro_Color = ['#F6FAFD','#F3F3F3','#EFF6F0'];
			var _status = statuses[i];
			var wStroke = 7;
			var wRing = 7;
			var inner_opt = {
				size: progress_width - 25,
				strokeWidth: wStroke--,
				ringWidth: wRing--,
				ringEndsRounded: true,
				strokeColor:  stro_Color[i%3],
				color: line_colors[i%3],
				valueData: 'status-'+_status['status_id'],
			};
			i++;
			if( i < statuses.length) {
				inner_opt.inner = this.buildOptInner(statuses, i, progress_width - 25);
			}
			return inner_opt;
		},
		draw_progress_multi_circle: function(pie_progress, dataField, hard_color = false){
			if(dataField.length > 0) {
				var n = 0;
				var filed_format = [];
				var line_colors = [];
				for (var status_id in dataField) {
					if(typeof filed_format[n] === 'undefined') filed_format[n] = {};
					filed_format[n]['status_id'] = status_id;
					filed_format[n]['percent'] = dataField[status_id];
					line_colors[n] = m_circle_colors[status_id] == '#888888' ? '#D3D3D3' : m_circle_colors[status_id];
					n++;
				}
				if(hard_color) line_colors = ['#F59796', '#75B37F', '#6eb07a'];
				var color_scheme = hard_color ? 'myScheme_hard' : 'myScheme';
				// prepare chart data as an array
				var source =
				{
					datatype: "array",
					datafields: [
						{ name: 'status_id' },
						{ name: 'percent' }
					],
					localdata: filed_format
				};

				var dataAdapter = new $.jqx.dataAdapter(source);
				
				$.jqx._jqxChart.prototype.colorSchemes.push({ name: color_scheme, colors: line_colors });
				
				// prepare jqxChart settings
				var settings = {
					
				};

				// setup the chart
				pie_progress.jqxChart({
					title: "",
					description: "",
					enableAnimations: true,
					showLegend: false,
					showToolTips: false,
					source: dataAdapter,
					defaultUnit: '',
					colorScheme: color_scheme,
					seriesGroups:
						[
							{
								type: 'donut',
								customGradient: [
									[1, 1, 1],
									[1, 1, 1]
								],
								borderLineColor: '#FFFFFFF',
								series:
									[
										{
											dataField: 'percent',
											displayText: 'status_id',
											labelRadius: 45,
											initialAngle: 15,
											radius: 60,
											innerRadius: 53,
											showLabels: false,
											centerOffset: 0,
											formatSettings: { sufix: '%'},
										}
									]
							}
						]
				});
			}
		},
		
		refresh: function(){
			var dashboard = this;
			if( !dashboard.ready) return;
			if( typeof dashboard.get_displayed_projects == 'function')
				dashboard.project_ids = dashboard.get_displayed_projects();
			if( dashboard.el ) 
				dashboard.drawAll();
		},
		refresh_number_widget: function( field, val){
			var dashboard = this;
			var wg_name = 'dash_' + field;
			// update data
			if(typeof projectTarget == 'undefined'){
				var projectTarget = {};
			}
			projectTarget['target_'+field] = val;
			var data = dashboard.get_data_number(field);
			if(projectTarget['target_'+field]['value'] != data['target']['value']){
				data['target']['value'] = projectTarget['target_'+field]['value'];
				if(data['target']['value'] != 0){
					data['target_percent'] = Math.round(calculatePercent(data['sum'], data['target']['value']));
				}
			}
			var wg_setting;
			$.each( dashboard.all_widget, function( row, widgets){
				$.each( widgets, function(k, v){
					if( k == wg_name) wg_setting = v;
				});
			});
			// draw html 
			var _res_html = dashboard.draw_number_field(data, wg_setting);
			var wd_container = $('#dash_'+ field);
			if(wd_container.length > 0){ 
				wd_container.empty().append(_res_html);
			}
			
			// Draw js
			var elem_circle = $('#dash-widget-circle-'+ field);
			if(elem_circle.length > 0){ 
				// elem_circle.attr("data-outer", target_percent);
				dashboard.draw_progress_budget_circle(elem_circle);
			}
			
			// init event
			dashboard.edit_number_event();
			
		},
		refresh_widget_progress_line: function(){
			var dashboard = this;
			var enable_widget = dashboard.enable_widget;
			var wg_setting = {};
			$.each( ['dash_internalbudgeteuro', 'dash_internalbudgetmd', 'dash_externalbudget', 'dash_synthesis'], function(i, widget_name){
				if($.inArray( widget_name, enable_widget) != -1){
					$.each( dashboard.all_widget, function( row, widgets){
						$.each( widgets, function(k, v){
							if( k == widget_name) wg_setting = v;
						});
					});
					var func_get = 'get_data_' + widget_name; 
					var data = dashboard[func_get]();
					var func_draw = 'draw_' + widget_name;
					var _html = dashboard[func_draw](data, wg_setting);
					var wg = dashboard.$parent.find('.dash-widget-item.' + widget_name ).first();
					wg.html( _html );
				}
			});
			this.draw_progress_budget_circle($('#dash-external-circle'));
			this.draw_progress_budget_circle($('#dash-widget-circle'));
			this.draw_progress_budget_circle($('#dash-widget-circle-md'));
			this.draw_progress_budget_circle($('#dash-syns-circle'));
			this.draw_progress_budget_line($('#chartContainerEx'), data_external_euro, progress_external_euro_max, external_chart_fields, $('#jqx-format-tooltip-ex'));
			this.draw_progress_budget_line($('#chartContainerIn'), data_internal, progress_euro_max, internal_chart_fields, $('#jqx-format-tooltip-in'));
			this.draw_progress_budget_line($('#chartContainerInMD'), data_internal_md, progress_md_max, internalMD_chart_fields, $('#jqx-format-tooltip-in-md'));
			this.draw_progress_budget_line($('#chartContainerSynth'), data_synthesis_euro, progress_synthesis_euro_max, synthesis_chart_fields, $('#jqx-format-tooltip-synth'));
			$(window).trigger('resize');
		},
		refresh_pc_forecast_widget: function( field, val){
			var dashboard = this;
			var wg_name = 'dash_' + field;
			var data = dashboard.get_data_dash_pc_forecast(val);
			var wg_setting;
			$.each( dashboard.all_widget, function( row, widgets){
				$.each( widgets, function(k, v){
					if( k == wg_name) wg_setting = v;
				});
			});
			var date_selected = $('#date_'+field).val();
			var pc_id = $('#'+field).find('.selected-item').data('id');
			var _res_html = dashboard.render_content_profit_center_estimated(data['pc_forecasts'], pc_id, date_selected);
			var wd_inner_content = $('#dash_'+ field).find('.dash-widget-inner');
			
			if(wd_inner_content.length > 0 && _res_html){ 
				wd_inner_content.empty().append(_res_html);
			}
			// scroll to current year. Comment theo yeu cau ticket #1185
			// wd_inner_content.scrollLeft(0);
			// dashboard.scrollToCurrentYear($('#dash_'+field));
			
			// init event
			dashboard.selected_pc_forecast();
		},
		destroy: function(){
			this.el.removeClass(this.options.classes);
			this.$parent.remove();
		},
		get_displayed_projects: function() {
			var dashboard = this;
			return dashboard.get('project_ids');
		},
		get_data_dash_late_milestones: function(){
			var dashboard = this;
			var res = [];
			var ms_data = dashboard.data.ProjectMilestones;
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in ms_data){
				if( 'milestone_late' in ms_data[id]){
					var it = dashboard.recusive_copy_object(ms_data[id]['milestone_late']);
					var pr = dashboard.data['Projects'][id]['Project'];
					it['project_name'] = pr['project_name'];
					res.push(it);
				}
				}
			});
			var data = $.orderObjectBySpecialKey( res, 'milestone_date', 'string', true);
			return data;
		},
		get_data_dash_next_milestones: function(){
			var dashboard = this;
			var res = [];
			var ms_data = dashboard.data.ProjectMilestones;
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in ms_data){
				if( 'next_milestone' in ms_data[id]){
					var it = dashboard.recusive_copy_object(ms_data[id]['next_milestone']);
					var pr = dashboard.data['Projects'][id]['Project'];
					it['project_name'] = pr['project_name'];
					res.push(it);
				}
				}
			});
			var data = $.orderObjectBySpecialKey( res, 'milestone_date', 'string', true);
			return data;
		},
		get_data_dash_phase: function(){
			var dashboard = this;
			var res = {
				content: {
					title: enableWidgets['phase'],
					plan:  SlickGridCustom.t('plan'),
					real:  SlickGridCustom.t('real'),
					plan_end_date:  SlickGridCustom.t('plan_end_date'),
					real_end_date:  SlickGridCustom.t('real_end_date'),
					plan_start_date:  SlickGridCustom.t('plan_start_date'),
					real_start_date:  SlickGridCustom.t('real_start_date'),
					late_project:  SlickGridCustom.t('late_project'),
					min_start_date_plan: '',
					min_start_date_real: '',
					max_end_date_plan: '',
					max_end_date_real: '',
					phase_diff: '',
					late: 0
				},
				data: []
			};
			var min_start_date_plan = '0',
				min_start_date_real = '0',
				max_end_date_plan = '0',
				max_end_date_real = "0";
			var min_plan_id = 0,
				min_real_id = 0,
				max_plan_id = 0,
				max_real_id = 0;
			var data = [];
			var projectPhasePlans = dashboard.data.projectPhasePlans;
			var ProjectProgress = dashboard.data.ProjectProgress;
			$.each( dashboard.get('project_ids'), function( i, id) {
				var item = [];
				var pr = dashboard.data['Projects'][id]['Project'];
				
				item['project_id'] = id;
				item['project_name'] = pr['project_name'];
				if( id in projectPhasePlans){
					var p = projectPhasePlans[id];
					item['end_date_plan'] = p['max_end_date_plan'];
					item['end_date_real'] = p['max_end_date_real'];
					item['phase_end_diff'] = p['diff'] + SlickGridCustom.t('d');
					item['project_progress'] = ((id in ProjectProgress) ? ProjectProgress[id]['Completed'] : '0') + '%';
					item['start_date_plan'] =  p['min_start_date_plan'];
					item['start_date_real'] =  p['min_start_date_real'];
					
					if( (min_start_date_plan == "0") || (min_start_date_plan > p['MinStartDatePlan'] )){
						min_start_date_plan = p['MinStartDatePlan'];
						min_plan_id = id;
					}
					if(( min_start_date_real == "0") || (min_start_date_real > p['MinStartDateReal'] )){
						min_start_date_real = p['MinStartDateReal'];
						min_real_id = id;
					}
					if( max_end_date_plan < p['MaxEndDatePlan'] ){
						max_end_date_plan = p['MaxEndDatePlan'];
						max_plan_id = id;
					}
					if( max_end_date_real < p['MaxEndDateReal'] ){
						max_end_date_real = p['MaxEndDateReal'];
						max_real_id = id;
					}
				}
				data.push(item);
			});
			res.data = $.orderObjectBySpecialKey(data, 'phase_end_diff', 'number', false);
			if( min_plan_id ) res.content.min_start_date_plan = projectPhasePlans[min_plan_id]['min_start_date_plan'];
			if( min_real_id ) res.content.min_start_date_real = projectPhasePlans[min_real_id]['min_start_date_real'];
			if( max_plan_id ) res.content.max_end_date_plan = projectPhasePlans[max_plan_id]['max_end_date_plan'];
			if( max_real_id ) {
				res.content.max_end_date_real = projectPhasePlans[max_real_id]['max_end_date_real'];
				max_end_date_real = new Date(max_end_date_real);
				max_end_date_plan = new Date(max_end_date_plan);
				var diff_date = max_end_date_real - max_end_date_plan;
				diff_date /= ( 1000 * 3600 * 24);
				res.content.phase_diff = diff_date + SlickGridCustom.t('d');
				$.each( res.data, function( i, pr_data){
					if(pr_data.phase_end_diff) res.data[i]['phase_diff'] = pr_data.phase_end_diff;
				});
				if(max_end_date_real > max_end_date_plan) res.content.late = 1;
			}
			return res;
		},
		format_data_budget_finance_plus: function(){
			var dashboard = this;
			var financeplus = dashboard.data.financeplus.values;
			var res = {};
			var _time = new Date();
			var y = finan_curent_selected;
			while(y <= _time.getFullYear()){
				res[y] = {
					finaninv_avancement: 0,
					finaninv_budget: 0,
					fon_avancement: 0,
					fon_budget: 0,
					year: y
				};
				y++;
			}
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in financeplus){
					var project_finance = financeplus[id];
					$.each( project_finance , function( key, data){
						for (var year in data) {
							if(typeof res[year] === 'undefined') res[year] = {};
							if(typeof res[year][key] === 'undefined') res[year][key] = 0;
							res[year]['year'] = year;
							res[year][key] += parseFloat(data[year]);
						}
					});
				}
			});
			return res;
		},
		get_data_budget_finance_plus: function(type){
			var dashboard = this;
			var avan_key = type+'_avancement';
			var budg_key = type+'_budget';
			var res = dashboard.format_data_budget_finance_plus();
			var progress_max = 0;
			var result = [];
			result['data'] = [];
			result['progress_max'] = 0;
			for (var year in res) {
				result['data'].push(res[year]);
				if(typeof res[year][avan_key] === 'undefined') res[year][avan_key] = 0;
				if(typeof res[year][budg_key] === 'undefined') res[year][budg_key] = 0;
				progress_max = Math.max(res[year][budg_key],res[year][avan_key], progress_max);
			}
			result['progress_max'] = progress_max;
			return result;
		},
		get_data_dash_financeplusinv: function(){
			var dashboard = this;
			var result = dashboard.get_data_budget_finance_plus('inv');
			progress_inv_max = result['progress_max'];
			var data = result['data'];
			return data;
		},
		get_data_dash_financeplusfinaninv: function(){
			var dashboard = this;
			var result = dashboard.get_data_budget_finance_plus('finaninv');
			progress_finaninv_max = result['progress_max'];
			var data = result['data'];
			return data;
		},
		get_data_dash_financeplusfon: function(){
			var dashboard = this;
			var result = dashboard.get_data_budget_finance_plus('fon');
			progress_fon_max = result['progress_max'];
			var data = result['data'];
			return data;
		},
		get_data_dash_financeplusfinanfon: function(){
			var dashboard = this;
			var result = dashboard.get_data_budget_finance_plus('finanfon');
			progress_finanfon_max = result['progress_max'];
			var data = result['data'];
			return data;
		},
		get_data_dash_progress_all_tasks: function(){
			var dashboard = this;
			var summary_tasks = dashboard.data.summary_tasks;
			var CompanyStatus = dashboard.data.Company.ProjectStatus;
			var res = {
				count_status: 0,
				task_status: {},
			};
			$.each( CompanyStatus, function( id, name){
				res.task_status[id] = 0;
			});
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in summary_tasks){
					var p_task = summary_tasks[id];
					$.each( p_task.count_by_stt, function( stt, count){
						res['count_status'] += count;
						if( !( stt in res['task_status'])) res['task_status'][stt] = 0;
						res['task_status'][stt] += count;
					});	
				}
			});
			return res;
		},
		get_data_dash_count_tasks: function(){
			var dashboard = this;
			var summary_tasks = dashboard.data.summary_tasks;
			var res = {
				count_tasks: 0,
				late_tasks: 0,
				intime_task: 0,
			};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in summary_tasks){
					var p_task = summary_tasks[id];
					res['count_tasks'] += (p_task.count_task || 0);
					res['late_tasks'] += (p_task.count_task_late|| 0);
					res['intime_task'] += (p_task.count_task_intime|| 0);
				}
			});
			return res;
		},
		get_data_dash_progress_tasks: function(){
			var dashboard = this;
			var summary_tasks = dashboard.data.summary_tasks;
			var res = {
				count_tasks: 0,
				task_status: {
					CL: 0, // Cho nay Quan dat ten nhu cai qq / count_task_late
					IP: 0  // count_task_intime
				},
			};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in summary_tasks){
					var p_task = summary_tasks[id];
					res['count_tasks'] += (p_task.count_task || 0);
					res['task_status']['CL'] += (p_task.count_task_late|| 0);
					res['task_status']['IP'] += (p_task.count_task_intime|| 0);
				}
			});
			return res;
		},
		calculatePercent: function (a, b) {
			if(typeof a === 'undefined') a = 0;
			if(typeof b === 'undefined') b = 0;
			if(b == 0){
				if(a > 0) return 100;
				else return 0;
			}else{
				return 100 * a / b;
			}
			
		},
		get_data_dash_internalbudgeteuro: function(){
			var dashboard = this;
			var res = {};
			res['budget_euro'] = 0;
			res['forecast_euro'] = 0;
			res['consumed_euro'] = 0;
			res['count_project'] = 0;
			res['count_project_exceed_budget'] = 0;
			res['progress_line'] = [];
			var budget_syns = dashboard.data.ProjectBudgetSyns;
			var sum_line = [];
			var dataset_internals = {};
			if( 'dataset_internals' in dashboard.data) dataset_internals = dashboard.data.dataset_internals;
			$.each( dashboard.get('project_ids'), function( i, project_id) {
				var project_line = {};
				if( project_id in budget_syns){
					var budget_syn = budget_syns[project_id];
					var item = {};
					var budget_euro = budget_syn['internal_costs_budget'] ?  parseFloat(budget_syn['internal_costs_budget']): 0;
					var forecast_euro = budget_syn['internal_costs_forecast'] ? parseFloat(budget_syn['internal_costs_forecast']) : 0;
					var consumed_euro = budget_syn['internal_costs_engaged'] ? parseFloat(budget_syn['internal_costs_engaged']) : 0;
					res['count_project'] += 1;
					res['budget_euro'] += budget_euro;
					res['forecast_euro'] += forecast_euro;
					res['consumed_euro'] += consumed_euro;					
					if( project_id in dataset_internals) project_line = dataset_internals[project_id];
					if( forecast_euro > budget_euro){
						res['count_project_exceed_budget'] += 1;
					}
					$.each( project_line , function( index, data){
						var _date = data['date'];
						if(typeof _date !== 'undefined') {
							// _date = 'd_' + _date;
							if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
							sum_line[_date]['date'] = _date;
							sum_line[_date]['date_format'] = data['date_format'];
							var budget_price = data['budget_price'] ? data['budget_price'] : 0;
							var forecast_price = data['validated_price'] ? data['validated_price'] : 0;
							var consumed_price = data['consumed_price'] ? data['consumed_price'] : 0;
							if(typeof sum_line[_date]['budget_price'] === 'undefined') sum_line[_date]['budget_price'] = 0;
							if(typeof sum_line[_date]['validated_price'] === 'undefined') sum_line[_date]['validated_price'] = 0;
							if(typeof sum_line[_date]['consumed_price'] === 'undefined') sum_line[_date]['consumed_price'] = 0;
							sum_line[_date]['budget_price'] += parseFloat(budget_price);
							sum_line[_date]['validated_price'] += parseFloat(forecast_price);
							sum_line[_date]['consumed_price'] += parseFloat(consumed_price);
						}
					});
				}
			});
			var i = 0;
			progress_euro_max = 0;
			internal_chart_fields = ['budget_price', 'validated_price', 'consumed_price'];
			for (var key in sum_line) {
				if(i == 0) res['progress_line'][i] = sum_line[key];
				else{
					var j = i - 1;
					if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
					res['progress_line'][i]['date'] = sum_line[key]['date'];
					res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
					res['progress_line'][i]['budget_price'] = sum_line[key]['budget_price'];
					res['progress_line'][i]['validated_price'] = sum_line[key]['validated_price'];
					res['progress_line'][i]['consumed_price'] =  sum_line[key]['consumed_price'];
					progress_euro_max = Math.max(sum_line[key]['budget_price'],sum_line[key]['validated_price'], sum_line[key]['consumed_price'], progress_euro_max);
				}
				i++;
				
			};
			progress_euro_max =  Math.round( progress_euro_max * 1.1, 2);
			res['percent_consumed_euro'] = dashboard.calculatePercent(res['consumed_euro'], res['forecast_euro']);
			res['percent_forecast_euro'] = dashboard.calculatePercent(res['forecast_euro'], res['budget_euro']);
			res['budget_euro'] = number_format(res['budget_euro'], 2, ',', ' ') + ' ' + budget_settings;
			res['forecast_euro'] = number_format(res['forecast_euro'], 2, ',', ' ') + ' ' + budget_settings;
			res['consumed_euro'] = number_format(res['consumed_euro'], 2, ',', ' ') + ' ' + budget_settings;
			return res;
		},
		get_data_dash_internalbudgetmd: function(){
			var dashboard = this;
			var res = {};
			res['budget_md'] = 0;
			res['forecast_md'] = 0;
			res['consumed_md'] = 0;
			res['count_project'] = 0;
			res['count_project_exceed_budget'] = 0;
			res['progress_line'] = [];
			var sum_line = {};
			var budget_syns = dashboard.data.ProjectBudgetSyns;
			var projects = dashboard.data.Projects;
			var consumeds = dashboard.data.sumActivities; 
			var dataActivityTaskManual = {};
			if( dashboard.data.useManualConsumed){
				var dataActivityTaskManual = dashboard.data.dataActivityTaskManual;
			}
			var dataset_internals = {};
			if( 'dataset_internals' in dashboard.data) dataset_internals = dashboard.data.dataset_internals;
			$.each( dashboard.get('project_ids'), function( i, project_id) {
				var project_line = {};
				
				if( project_id in budget_syns){
					var budget_syn = budget_syns[project_id];
					var activity_id = projects[project_id]['Project']['activity_id'];
					var item = [];
					res['count_project'] += 1;
					var budget_md = budget_syn['internal_costs_budget_man_day'] ? parseFloat(budget_syn['internal_costs_budget_man_day']) : 0;
					var forecast_md = budget_syn['internal_costs_forecasted_man_day'] ? parseFloat(budget_syn['internal_costs_forecasted_man_day']) : 0;
					var consumed_md = 0;
					if( dashboard.data.useManualConsumed){
						consumed_md = (project_id in dataActivityTaskManual) ? parseFloat( dataActivityTaskManual[project_id]['consumed']) : 0;
					}else{
						consumed_md = consumeds[activity_id] ? parseFloat(consumeds[activity_id]) : 0;
					}
					res['budget_md'] += budget_md;
					res['forecast_md'] += forecast_md;
					res['consumed_md'] += consumed_md;
					
					if( project_id in dataset_internals) project_line = dataset_internals[project_id];
					if(forecast_md > consumed_md){
						res['count_project_exceed_budget'] += 1;
					}
					$.each( project_line , function( index, data){
						var _date = data['date'];
						if(typeof _date !== 'undefined') {
							// _date = 'd_' + _date;
							if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
							sum_line[_date]['date'] = _date;
							sum_line[_date]['date_format'] = data['date_format'];
							var budget_price = data['budget_md'] ? data['budget_md'] : 0;
							var forecast_price = data['validated'] ? data['validated'] : 0;
							var consumed_price = data['consumed'] ? data['consumed'] : 0;
							if(typeof sum_line[_date]['budget_md'] === 'undefined') sum_line[_date]['budget_md'] = 0;
							if(typeof sum_line[_date]['validated'] === 'undefined') sum_line[_date]['validated'] = 0;
							if(typeof sum_line[_date]['consumed'] === 'undefined') sum_line[_date]['consumed'] = 0;
							sum_line[_date]['budget_md'] += parseFloat(budget_price);
							sum_line[_date]['validated'] += parseFloat(forecast_price);
							sum_line[_date]['consumed'] += parseFloat(consumed_price);
						}
					});
				}
			});
			var i = 0;
			progress_md_max = 0;
			internalMD_chart_fields = ['budget_md', 'validated', 'consumed'];
			for (var key in sum_line) {
				if(i == 0) res['progress_line'][i] = sum_line[key];
				else{
					var j = i - 1;
					if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
					res['progress_line'][i]['date'] = sum_line[key]['date'];
					res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
					res['progress_line'][i]['budget_md'] = sum_line[key]['budget_md'];
					res['progress_line'][i]['validated'] = sum_line[key]['validated'];
					res['progress_line'][i]['consumed'] =  sum_line[key]['consumed'];
					progress_md_max = Math.max(sum_line[key]['budget_md'],sum_line[key]['validated'], sum_line[key]['consumed'], progress_md_max);
				}
				i++;
				
			};
			progress_md_max =  Math.round( progress_euro_max * 1.2, 2);
			res['percent_consumed_md'] = calculatePercent(res['consumed_md'], res['forecast_md']);
			res['percent_forecast_md'] = calculatePercent(res['forecast_md'], res['budget_md']);
			res['budget_md'] = number_format(res['budget_md'], 2, ',', ' ') + ' ' + $this.t('M.D');
			res['forecast_md'] = number_format(res['forecast_md'], 2, ',', ' ') + ' ' + $this.t('M.D');
			res['consumed_md'] = number_format(res['consumed_md'], 2, ',', ' ') + ' ' + $this.t('M.D');
			return res;
		},
		get_data_dash_externalbudget: function(){
			var dashboard = this;
			var res = [];
			res['budget_euro'] = 0;
			res['forecast_euro'] = 0;
			res['ordered_euro'] = 0;
			res['count_project'] = 0;
			res['count_project_exceed_budget'] = 0;
			res['progress_line'] = [];
			var sum_line = [];
			var budget_syns = dashboard.data.ProjectBudgetSyns;
			var dataset_externals = {};
			if( 'dataset_externals' in dashboard.data) dataset_externals = dashboard.data.dataset_externals;
			$.each( dashboard.get('project_ids'), function( i, project_id) {
				var project_line = {};
				if( project_id in budget_syns){
					var budget_syn = budget_syns[project_id];
					var item = {};
					res['count_project'] += 1;
					var budget_euro = budget_syn['external_costs_budget'] ?  parseFloat(budget_syn['external_costs_budget']): 0;
					var forecast_euro = budget_syn['external_costs_forecast'] ?  parseFloat(budget_syn['external_costs_forecast']): 0;
					var ordered_euro = budget_syn['external_costs_ordered'] ?  parseFloat(budget_syn['external_costs_ordered']): 0;
					
					res['budget_euro'] += budget_euro;
					res['forecast_euro'] += forecast_euro;
					res['ordered_euro'] += ordered_euro;
					if( project_id in dataset_externals) project_line = dataset_externals[project_id];
					project_line
					if(forecast_euro > ordered_euro){
						res['count_project_exceed_budget'] += 1;
					}
					for (var key in project_line) {
						var data = project_line[key];
						var _date = data['date'];
						if(typeof _date !== 'undefined') {
							// _date = 'd_' + _date;
							if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
							sum_line[_date]['date'] = _date;
							sum_line[_date]['date_format'] = data['date_format'];
							var budget_price = data['budget'] ? data['budget'] : 0;
							var forecast_price = data['forecast'] ? data['forecast'] : 0;
							var ordered_price = data['ordered'] ? data['ordered'] : 0;
							if(typeof sum_line[_date]['budget_price'] === 'undefined') sum_line[_date]['budget_price'] = 0;
							if(typeof sum_line[_date]['forecast_price'] === 'undefined') sum_line[_date]['forecast_price'] = 0;
							if(typeof sum_line[_date]['ordered_price'] === 'undefined') sum_line[_date]['ordered_price'] = 0;
							sum_line[_date]['budget_price'] += parseFloat(budget_price);
							sum_line[_date]['forecast_price'] += parseFloat(forecast_price);
							sum_line[_date]['ordered_price'] += parseFloat(ordered_price);
						}
					}
				}
			});
			external_chart_fields = ['budget_price', 'forecast_price', 'ordered_price'];
			var i = 0;
			progress_external_euro_max = 0;
			for (var key in sum_line) {
				if(i == 0) res['progress_line'][i] = sum_line[key];
				else{
					if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
					res['progress_line'][i]['date'] = sum_line[key]['date'];
					res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
					res['progress_line'][i]['budget_price'] = sum_line[key]['budget_price'];
					res['progress_line'][i]['forecast_price'] = sum_line[key]['forecast_price'];
					res['progress_line'][i]['ordered_price'] =  sum_line[key]['ordered_price'];
					progress_external_euro_max = Math.max(sum_line[key]['budget_price'],sum_line[key]['forecast_price'], sum_line[key]['ordered_price'], progress_external_euro_max);
				}
				i++;
			};
			progress_external_euro_max =  Math.round( progress_external_euro_max * 1.1, 2);
			res['percent_ordered_euro'] = calculatePercent(res['ordered_euro'], res['forecast_euro']);
			res['percent_forecast_euro'] = calculatePercent(res['forecast_euro'], res['budget_euro']);
			res['budget_euro'] = number_format(res['budget_euro'], 2, ',', ' ') + ' ' + budget_settings;
			res['forecast_euro'] = number_format(res['forecast_euro'], 2, ',', ' ') + ' ' + budget_settings;
			res['ordered_euro'] = number_format(res['ordered_euro'], 2, ',', ' ') + ' ' + budget_settings;
			return res;
		},
		get_data_dash_synthesis: function(){
			var dashboard = this;
			var res = [];
			res['budget_euro'] = 0;
			res['forecast_euro'] = 0;
			res['consumed_euro'] = 0;
			res['count_project'] = 0;
			res['count_project_exceed_budget'] = 0;
			res['progress_line'] = [];
			var sum_line = {};
			var budget_syns = dashboard.data.ProjectBudgetSyns;
			var dataset_externals = {};
			if( 'dataset_externals' in dashboard.data) dataset_externals = dashboard.data.dataset_externals;
			var dataset_internals = {};
			if( 'dataset_internals' in dashboard.data) dataset_internals = dashboard.data.dataset_internals;
			var syns_progress_keys = {};
			if( 'syns_progress_keys' in dashboard.data) syns_progress_keys = dashboard.data.syns_progress_keys;
			$.each( dashboard.get('project_ids'), function( i, project_id) {
				if( project_id in budget_syns){
					var budget_syn = budget_syns[project_id];
					var item = {};
					res['count_project'] += 1;
					//budget_euro
					var internal_budget_euro = budget_syn['internal_costs_budget'] ?  parseFloat(budget_syn['internal_costs_budget']): 0;
					var external_budget_euro = budget_syn['external_costs_budget'] ?  parseFloat(budget_syn['external_costs_budget']): 0;
					var budget_euro = internal_budget_euro + external_budget_euro;
					//forecast_euro
					var internal_costs_forecast = budget_syn['internal_costs_forecast'] ? parseFloat(budget_syn['internal_costs_forecast']) : 0;
					var external_costs_forecast = budget_syn['external_costs_forecast'] ?  parseFloat(budget_syn['external_costs_forecast']): 0;
					var forecast_euro = internal_costs_forecast + external_costs_forecast;
					//consumed_euro
					var internal_costs_engaged = budget_syn['internal_costs_engaged'] ? parseFloat(budget_syn['internal_costs_engaged']) : 0;
					var external_costs_ordered = budget_syn['external_costs_ordered'] ?  parseFloat(budget_syn['external_costs_ordered']): 0;
					var consumed_euro = internal_costs_engaged + external_costs_ordered;
					
					res['budget_euro'] += budget_euro;
					res['forecast_euro'] += forecast_euro;
					res['consumed_euro'] += consumed_euro;
					if(forecast_euro > consumed_euro){
						res['count_project_exceed_budget'] += 1;
					}
					var data_external = {};
					if( project_id in dataset_externals) data_external = dataset_externals[project_id];
					
					var data_internal = {};
					if( project_id in dataset_internals) data_internal = dataset_internals[project_id];
					
					var data_total = {};
					if( project_id in syns_progress_keys ) data_total = syns_progress_keys[project_id];
					
					var lastInter = {
						'budget' : 0,
						'forecast' : 0,
						'engared' : 0,
					};
					var lastEx = {
						'budget' : 0,
						'forecast' : 0,
						'engared' : 0,
					};
					for (var key in data_total) {
						var _date = data_total[key];
						if(typeof _date !== 'undefined') {
							if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
							sum_line[_date]['date'] = _date;
							sum_line[_date]['date_format'] = data_internal[_date] ? data_internal[_date]['date_format'] : data_external[_date]['date_format'];
							
							if( data_internal[_date] &&  data_internal[_date]['budget_price']) lastInter['budget'] = data_internal[_date]['budget_price'];
							if( data_external[_date] &&  data_external[_date]['budget']) lastEx['budget'] = data_external[_date]['budget'];
							var budget_price = lastEx['budget'] + lastInter['budget'];
							
							if( data_internal[_date] &&  data_internal[_date]['validated_price']) lastInter['forecast'] = data_internal[_date]['validated_price'];
							if( data_external[_date] &&  data_external[_date]['forecast']) lastEx['forecast'] = data_external[_date]['forecast'];
							var forecast_price = lastEx['forecast'] + lastInter['forecast'];
							
							if( data_internal[_date] &&  data_internal[_date]['consumed_price']) lastInter['engared'] = data_internal[_date]['consumed_price'];
							if( data_external[_date] &&  data_external[_date]['ordered']) lastEx['engared'] = data_external[_date]['ordered'];
							var consumed_price = lastEx['engared'] + lastInter['engared'];
							
							if(typeof sum_line[_date]['budget_price'] === 'undefined') sum_line[_date]['budget_price'] = 0;
							if(typeof sum_line[_date]['forecast_price'] === 'undefined') sum_line[_date]['forecast_price'] = 0;
							if(typeof sum_line[_date]['consumed_price'] === 'undefined') sum_line[_date]['consumed_price'] = 0;
							sum_line[_date]['budget_price'] += parseFloat(budget_price);
							sum_line[_date]['forecast_price'] += parseFloat(forecast_price);
							sum_line[_date]['consumed_price'] += parseFloat(consumed_price);
						}
					}
				}
			});
			var i = 0;
			progress_synthesis_euro_max = 0;
			synthesis_chart_fields = ['budget_price', 'forecast_price', 'consumed_price'];
			for (var key in sum_line) {
				if(i == 0) res['progress_line'][i] = sum_line[key];
				else{
					var j = i - 1;
					if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
					res['progress_line'][i]['date'] = sum_line[key]['date'];
					res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
					res['progress_line'][i]['budget_price'] = sum_line[key]['budget_price'];
					res['progress_line'][i]['forecast_price'] = sum_line[key]['forecast_price'];
					res['progress_line'][i]['consumed_price'] =  sum_line[key]['consumed_price'];
					progress_synthesis_euro_max = Math.max(sum_line[key]['budget_price'],sum_line[key]['forecast_price'], sum_line[key]['consumed_price'], progress_synthesis_euro_max);
				}
				i++;
				
			};
			progress_synthesis_euro_max =  Math.round( progress_synthesis_euro_max * 1.1, 2);
			res['percent_consumed_euro'] = calculatePercent(res['consumed_euro'], res['forecast_euro']);
			res['percent_forecast_euro'] = calculatePercent(res['forecast_euro'], res['budget_euro']);
			res['budget_euro'] = number_format(res['budget_euro'], 2, ',', ' ') + ' ' + budget_settings;
			res['forecast_euro'] = number_format(res['forecast_euro'], 2, ',', ' ') + ' ' + budget_settings;
			res['consumed_euro'] = number_format(res['consumed_euro'], 2, ',', ' ') + ' ' + budget_settings;
			return res;
		},
		get_data_dash_count_projects: function(){
			var dashboard = this;
			var projects = dashboard.data.Projects;
			var res = {};
			if(typeof default_category != 'undefined') {
				switch( default_category){
					case '5':
						res[1] = 0;
						res[2] = 0;
						break;
					case '6':
						default_category = '2';
					default: 
						res[default_category] = 0;
				}
			}
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in projects){
					var cat = projects[id]['Project']['category'];
					if(typeof res[cat] === 'undefined') res[cat]=0;
					res[cat]++;
				}
			});
			return res;
		},
		get_data_dash_list_weather: function(){
			var dashboard = this;
			var weather = {};
			var keys = ['sun', 'cloud', 'rain', 'sum'];
			$.each( keys , function( index, key){
				weather[key] = 0;
			});
			var projects = dashboard.data.Projects;
			var res = {};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in projects){
					var key = projects[id]['ProjectAmr']['weather'];
					weather['sum']++;
					if( !key) key = 'sun';
					if(key) weather[key]++;
				}
			});
			return weather;
		},
		get_data_dash_sum_progress: function(){
			var dashboard = this;
			var project_progress_method = companyConfigs.project_progress_method||'consumed';
			dashboard.project_progress_method = project_progress_method;
			var projectProgress = dashboard.data.ProjectProgress;
			var progress = 100;
			var projects = dashboard.get('project_ids');
			switch( project_progress_method) {
				case 'count_close_task':
					var total_task = 0, cl_task = 0;
					$.each( projects, function( i,p){
						if( projectProgress[p]){
							total_task += parseFloat(projectProgress[p]['TotalTask']);
							cl_task += parseFloat(projectProgress[p]['ClosedTask']);
						}
					});
					if( total_task == 0 ) progress = 100;
					else progress = cl_task * 100 / total_task;	
					break;
				case 'workload_of_close_task':
					var total_workload = 0, cl_workload = 0;
					$.each( projects, function( i,p){
						if( projectProgress[p]){
							total_workload += parseFloat(projectProgress[p]['TotalWorkload']);
							cl_workload += parseFloat(projectProgress[p]['ClosedWorkload']);
						}
					});
					if( total_workload == 0 ) progress = 100;
					else progress = cl_workload * 100 / total_workload;	
					break;
				case 'manual':
					if( projects.length){
						progress = 0;
						$.each( projects, function( i,p){
							progress += parseFloat(projectProgress[p]['Completed']);
						});
						progress /= projects.length;
					}
					break;
				case 'no_progress':
					
					break;
				case 'consumed':
				default:
					var s_consumed = 0, s_workload = 0;
					$.each( projects, function( i,p){
						if( projectProgress[p]){
							s_consumed += parseFloat(projectProgress[p]['Consumed']);
							s_workload += parseFloat(projectProgress[p]['Workload']);
						}
					});
					if( s_workload == 0 ) progress = s_consumed ? 100 : 0;
					else progress = s_consumed * 100 / s_workload;			
			}
			dashboard.progress = progress;
			return progress;
		},
		get_data_dash_progress_chart: function(){
			var dashboard = this;
			var projects = dashboard.get('project_ids');
			var projectProgress = dashboard.data.ProjectProgress;
			var project_progress_method = companyConfigs.project_progress_method||'consumed';
			dashboard.project_progress_method = project_progress_method;
			var progress = 0;
			var step = 10;
			var key = 0;
			var max = 100;
			var data = {};
			while( key <= max){
				data[key] = 0;
				$.each( projects, function( i,p){
					var _progress = projectProgress[p]['Completed'];
					_progress = Math.min( 100, Math.max( _progress, 0));
					if( (_progress >= key) && (_progress < (key+step))){
						data[key] = data[key] + 1;
					}
				});
				key += step;
			}
			return data;
		},
		get_data_project_amr_program_id: function(){
			var dashboard = this;
			var programs = {};
			programs['sum'] = 0;
			var projects = dashboard.data.Projects;
			var programs = dashboard.Company.ProjectAmrProgram;
			var res = {};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in projects){
					programs['sum']++;
					var program_id = projects[id]['Project']['project_amr_program_id'];
					if( ! programs[program_id]){
						programs[program_id] = [];
						programs[program_id]['count'] = 0;
					}
					programs[program_id]['count']++;
					programs[program_id]['name'] = programs[program_id];
				}
			});
			return programs;
		},
		get_data_dash_project_managers: function(){
			var dashboard = this;
			var ProjectEmployeeManager = dashboard.data.ProjectEmployeeManager;
			var project_managers = {};
			project_managers['sum'] = 0;
			var project_ids = dashboard.get('project_ids');
			var dataLength = project_ids.length;
			$.each(project_ids, function( i, id) {
				if( id in ProjectEmployeeManager){
					var manager_ids = ProjectEmployeeManager[id];
					if(manager_ids.length > 0){
						$.each(manager_ids, function(key, manager_id){
							if(! (manager_id in project_managers)){
								project_managers[manager_id	] = [];
								project_managers[manager_id]['count'] = 0;
								project_managers['sum']++;
							}
							if(typeof list_avatar[manager_id] !== 'undefined' && typeof list_avatar[manager_id]['full_name'] !== 'undefined'){
								project_managers[manager_id]['count']++;
								project_managers[manager_id]['name'] = list_avatar[manager_id]['full_name'];
								project_managers[manager_id]['manager_id'] = manager_id;
							}
						});
						
					}
				}
			});
			$.each( project_managers, function( k, v){
				if( k != 'sum'){
					project_managers[k]['percent'] =  number_format((project_managers[k]['count'] * 100) / dataLength, 1);
				}
			});
			return project_managers;
		},
		get_data_dash_profit_center_estimated: function(){
			console.error('get_data_dash_profit_center_estimated');
			console.log( 'working'); // HuynhFunction nay khong dung nua
			return [];
		},
		get_data_dash_pc_forecast: function(pc_forecast){
			var pc_estimated = [];
			var pc_selected = [];
			var dashboard = this;
			var list_pc_manager = dashboard.data.Company.ProfitCenterManager;
			$.each( dashboard.get('project_ids'), function( i, project_id) {
				if( project_id in pc_forecast){
					var project_estimated = pc_forecast[project_id];
					for (var pc_id in project_estimated) {
						pc_selected = pc_id;
						if(typeof pc_estimated[pc_id] === 'undefined') pc_estimated[pc_id] = {};
						for (var date in project_estimated[pc_id]) {
							if(typeof pc_estimated[pc_id][date] === 'undefined') pc_estimated[pc_id][date] = [];
							if(typeof pc_estimated[pc_id][date]['value'] === 'undefined') pc_estimated[pc_id][date]['value'] = 0;
							var text_month = project_estimated[pc_id][date]['month_text'];
							pc_estimated[pc_id][date]['text_month'] = project_estimated[pc_id][date]['month_text'];
							pc_estimated[pc_id][date]['value'] += project_estimated[pc_id][date]['value'] ? parseFloat(project_estimated[pc_id][date]['value']) : 0;
							pc_estimated[pc_id][date]['capcity'] = project_estimated[pc_id][date]['capacity'] ? parseFloat(project_estimated[pc_id][date]['capacity']) : 0;
						}
					}
				}
			});
			var data = {};
			data['pc_forecasts'] = pc_selected ? pc_estimated[pc_selected] : {};
			data['pc_manager'] = typeof list_avatar[list_pc_manager[pc_selected]] !== 'undefined' ? list_avatar[list_pc_manager[pc_selected]] : '';
			return data;
		},
		get_data_select : function(key){
			var dashboard = this;
			var projects = dashboard.data.Projects;
			var company_data = dashboard.data.maps['dash_' + key];
			var data = {};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in projects){
					var key_id = projects[id]['Project'][key];
					if( key_id != undefined && key_id != '0'){
						if( !data[key_id]){
							data[key_id] = [];
							data[key_id]['count'] = 0;
							data[key_id]['key_id'] = key_id;
						}
						data[key_id]['count']++;
						data[key_id]['name'] = company_data[key_id];
					}
				}
			});			
			var sorted = $.orderObjectBySpecialKey(data, 'count', 'number', false);
			return sorted;
		},
		get_data_yesno : function(key){
			var dashboard = this;
			var projects = dashboard.data.Projects;
			var res = {};
			res['no'] = 0;
			res['yes'] = 0;
			res['count'] = 0;
			res['field'] = key;
			//dashboard.get('project_ids') chi lay project dang hien thi (ke ca dang filter)
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in projects){
					var key_id = projects[id]['Project'][key];
					if( key_id != undefined && key_id != '0'){
						res['yes'] ++;
						res['count']++;
					}else{
						res['no'] ++;
						res['count']++;
					}
				}
			});
			return res;
		},
		get_data_number: function(key){
			var dashboard = this;
			var k = 'target_' + key;
			var projectTarget = {};
			if( k in dashboard.data.Company.ProjectTarget) projectTarget = dashboard.data.Company.ProjectTarget[k];
			var projects = dashboard.data.Projects;
			var data = {
				sum: 0,
				target: projectTarget,
				field: key
			};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( id in projects){
					if( projects[id]['Project'][key])
						data['sum'] += parseFloat(projects[id]['Project'][key]);
				}
			});
			var target_value = data['target']['value'] ? data['target']['value'] : 0;
			data.target_percent = calculatePercent(data.sum, target_value);
			return data;
		},
		get_data_multiselect: function(key){
			var dashboard = this;
			var project_multi_sel = dashboard.data.multi_fields[key];
			var company_data = dashboard.data.maps['dash_' + key];
			var data = {};
			$.each( dashboard.get('project_ids'), function( i, id) {
				if( (!$.isEmptyObject(project_multi_sel)) && (id in project_multi_sel)){
					var key_ids = project_multi_sel[id];
					$.each( key_ids, function( id, key_id){
						if( key_id != undefined && key_id != '0'){
							if( !data[key_id]){
								data[key_id] = [];
								data[key_id]['count'] = 0;
								data[key_id]['key_id'] = key_id;
								data[key_id]['name'] = company_data[key_id];
							}
							data[key_id]['count']++;
						}
					});
				}
			});			
			var sorted = $.orderObjectBySpecialKey(data, 'count', 'number', false);
			return sorted;
		},
		draw_dash_count_projects: function(data, wg_setting){
			var wg_name = 'count_projects';
			var dashboard = this;
			if( typeof data == 'undefined') data = wg_setting['data'];
			var _html = '';
			var sum = _count_project;
			_html += '<div class="dash-widget-title">';
			_html += '<span class="title-icon">' + wg_setting.icon + '</span>';
			_html += '<p class="dashtitle">' + wg_setting.title + '</p><p class="dashtitle-number">'+  number_format( sum, 0, ',', ' ') +'</p>';
			_html += '</div>';
			_html += '<div class="dash-widget-content">';
			if(typeof data[2] !== 'undefined') _html += '<div class="number-project number-opp-project"><p class="text-cat">'+ i18n["Opportunity"]+'</p>' + number_format( data[2], 0, ',', ' ') +'<span class="percent-cat">'+ number_format(data[2] * 100 / sum, 1) +'%</span></div>';
			if(typeof data[1] !== 'undefined') _html += '<div class="number-project number-in-project"><p class="text-cat">'+ i18n["In progress"]+'</p>' + number_format( data[1], 0, ',', ' ') +'<span class="percent-cat">'+ number_format(data[1] * 100 / sum, 1) +'%</span></div>';
			if(typeof data[3] !== 'undefined') _html += '<div class="number-project number-in-project"><p class="text-cat">'+ i18n["Archived"]+'</p>' + number_format( data[3], 0, ',', ' ') +'<span class="percent-cat">'+ number_format(data[3] * 100 / sum, 1) +'%</span></div>';
			if(typeof data[4] !== 'undefined') _html += '<div class="number-project number-in-project"><p class="text-cat">'+ i18n["Model"]+'</p>' + number_format( data[4], 0, ',', ' ') +'<span class="percent-cat">'+ number_format(data[4] * 100 / sum, 1) +'%</span></div>';
			_html += '</div>';
			return _html;
		},
		draw_dash_count_tasks: function(data, wg_setting){
			var wg_name = 'count_tasks';
			var wg_setting;
			var progress_intime = Math.min( Math.max( (data['intime_task'] * 100 / data['count_tasks']), 0), 100);
			progress_intime = parseInt( progress_intime);
			var progress_late = Math.min( Math.max( (data['late_tasks'] * 100 / data['count_tasks']), 0), 100);
			progress_late = parseInt(progress_late);
			if( typeof data == 'undefined') data = wg_setting['data'];
			var _html = '';
			_html += '<div class="dash-widget-title">';
			_html += '<span class="title-icon">' + wg_setting.icon + '</span>';
			_html += '<p class="dashtitle">' + wg_setting.title + '</p>';
			_html += '</div>';
			_html += '<div class="dash-widget-content">';
			
			_html += '<div class="number-tasks number-intime-tasks">';
			_html += '<div class="number-tasks-up">';
			_html += '<div class="p-title"><p class="text-cat">'+ i18n["In time"]+'</p></div>';
			_html += '<div class="project-item-progress progress-intime">';
			_html += '<div class="wd-progress-slider green-line' + '" data-value="' + progress_intime + '">';
			_html += '<div class="wd-progress-holder"><div class="wd-progress-line-holder"></div></div>';
			_html += '<div class="wd-progress-value-line" style="width: '+ progress_intime+'%;"></div>';
			_html += ' </div> </div></div>';
			_html += '<div class="number-tasks-down">';
			_html += '<div class="sp-count"><span class="count-intime">'+ number_format( data['intime_task'], 0, ',', ' ')+'</span></div>';
			_html += '<div class="sp-percent"><span class="percent-intime">'+ number_format(data['intime_task'] * 100 / data['count_tasks'], 0) +'%</span></div>';
			_html += '</div></div>';
			
			_html += '<div class="number-tasks number-late-tasks">';
			_html += '<div class="number-tasks-up">';
			_html += '<div class="p-title"><p class="text-cat">'+ i18n["Late"]+'</p></div>';
			_html += '<div class="project-item-progress progress-late">';
			_html += '<div class="wd-progress-slider red-line' + '" data-value="' + progress_late + '">';
			_html += '<div class="wd-progress-holder"><div class="wd-progress-line-holder"></div></div>';
			_html += '<div class="wd-progress-value-line" style="width: '+ progress_late+'%;"></div>';
			_html += ' </div> </div></div>';
			_html += '<div class="number-tasks-down">';
			_html += '<div class="sp-count"><span class="count-late">'+ number_format( data['late_tasks'], 0, ',', ' ')+'</span></div>';
			_html += '<div class="sp-percent"><span class="percent-late">'+ number_format(data['late_tasks'] * 100 / data['count_tasks'], 0) +'%</span></div>';
			_html += '</div></div>';
			
			_html += '</div>';
			return _html;
		},
		draw_dash_progress_tasks: function(data, wg_setting){
			data_tasks_late = [];
			if(typeof data['task_status'] === 'undefined') return '';
			
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="progress-circle-task">';
			
			var sta_percent_CL = Math.round(calculatePercent(data['task_status']['CL'], data['count_tasks']));
			data_tasks_late[0] = sta_percent_CL;
			
			var sta_percent_IP = Math.round(calculatePercent(data['task_status']['IP'], data['count_tasks']));
			data_tasks_late[1] = sta_percent_IP;
			
			_html_content += '<div class="progress-circle-task-inner dash-progress-circle"><div class="circle-wrapper"><span style= "color: #F59796" class="ip-percent">'+ sta_percent_CL +'%</span><div id="circle-progress-task-graph" width = 135  style="height: 140px; "></div></div></div>';
			
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Late'] +'</span><p class="dash-budget-percent forecast" style="background-color:#f698977d;color: #f69897;margin-left: auto;">'+ data['task_status']['CL'] +'</p><p style="color: #f69897;width:40px;">'+ sta_percent_CL +'%</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['In time'] +'</span><p class="dash-budget-percent consumed" style="background-color:#75b37f87;color: #75b37f;margin-left: auto;">'+ data['task_status']['IP'] +'</p><p style="color: #75b37f;width:40px;">'+ sta_percent_IP+'%</p></div>';
			_html_content += '</div>';
			
			_html_content += '</div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_dash_list_weather: function(data, wg_setting){
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			var _html_content = '<div class="dash-widget-content">';
			var weathers = ['sun', 'cloud', 'rain'];
			$.each( weathers , function( index, weather){
				_html_content += '<div class="dash-item-weather '+ weather +'">';
				_html_content += dashboard_icons[weather];
				_html_content += '<div class="item-weather-content">';
				_html_content += '<p class="weather-text">'+ i18n[weather] +'</p>';
				_html_content += '<div><p class="weather-number" >'+ data[weather] +'</p></div>';
				_html_content += '</div><p class="weather-percent" >'+ number_format(data[weather] * 100 / data["sum"], 1) +'%</p></div>';
			});
			_html_content += '</div>';
			return _html_head + _html_content;		
		},
		draw_dash_sum_progress: function(data, wg_setting){
			if( this.project_progress_method == 'no_progress') return '';
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			var _html_content = '<div class="dash-widget-content">';
			var data_display = Math.min( Math.max( data, 0), 100);
			data_display = parseInt( data_display);
			_html_content += '<div class="dash_progress_number' + (data > 100 ? ' red-line' : ' green-line') + '"> ' + data_display + '%</div>';
			_html_content += '<div class="project-item-progress">';
			_html_content += '<div class="wd-progress-slider' + (data > 100 ? ' red-line' : ' green-line') + '" data-value="' + data + '">';
			_html_content += '<div class="wd-progress-holder"><div class="wd-progress-line-holder"></div></div>';
			_html_content += '<div class="wd-progress-value-line" style="width: '+ data_display+'%;"></div>';
			_html_content += ' </div> </div>';
			_html_content += '</div>';
			return _html_head + _html_content;		
		},
		draw_dash_progress_chart: function(data, wg_setting){
			var projects = this.project_ids;
			var len = 10, max = 0;
			if( !$.isEmptyObject(projects)) len = projects.length;
			var chart_data = [];
			$.each( data, function( progress, number){
				max = Math.max( max, number);
			});
			var n_max = parseInt(max * 1.2);
			if( n_max == max) n_max++;
			$.each( data, function( progress, number){
				var it = {
					progress: progress + '%',
					count: number,
					left: n_max - number
				}
				chart_data.push( it );
			});
			this.progress_chart_data = {
				source: chart_data,
				max: n_max
			};	
			if( this.project_progress_method == 'no_progress') return '';
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner"><div class="dash_progress_chart_container"><div class="dash_progress_chart_inner"></div></div></div></div>';
			return _html_head + _html_content;
		},
		draw_async_progress_chart: function(){
			var dashboard = this;
			if( $.inArray( 'dash_progress_chart', dashboard.enable_widget) == -1) return;
			var project_ids = dashboard.project_ids;
			var len = 10;
			if( !$.isEmptyObject(project_ids)) len = project_ids.length;
			var data = dashboard.progress_chart_data.source;
			var max = dashboard.progress_chart_data.max;
			var settings = {
				title: "",
				description: '',
				source: data,
				showBorderLine: false,
				enableAnimations: true,
				showLegend: false,
				showToolTips: false,
				isFormatTooltip: false,
				defaultUnit: '',
				categoryAxis:
					{
						dataField: 'progress',
						description: '',
						showGridLines: false,
						textOffset: {
							x: 0,
							y: 10
						},
					},
				colorScheme: 'z0G_bar',
				seriesGroups: [
					{
						type: 'stackedcolumn',
						showLabels: false,//default
						customGradient: [
							[0, 1, 1],
							[100, 1.2, 1]
						],
						valueAxis:
						{
							minValue: 0,
							maxValue: max,
							unitInterval: 1,
							displayValueAxis: false,
							valuesOnTicks: true,
						},
						series: [
							{ dataField: 'count', displayText: 'Consumed', color: '#7ab2da', columnsMaxWidth: 40, showLabels: true,
								customLabelFunction: function( instance, seriesGroups, series, field, options){
									var o = instance._getFormattedValue(seriesGroups, series, field);
									var p = instance.renderer.measureText(o, 0, {
										"class": "jqx-chart-label-text"
									});
									var x = parseInt( (options.x + (options.width - p.width) / 2));
									var f_h = options.y + options.height;
									var y = parseInt( ( f_h - p.height) /2);
									var n = instance.renderer.text(o, x, y, p.width, p.height, 0, {}, false, "center", "center");
									instance.renderer.attr(n, {
										"class": "jqx-chart-label-text"
									});
									if (instance._isVML) {
										instance.renderer.removeElement(n);
										instance.renderer.getContainer()[0].appendChild(n)
									}
									return n
								},
							},
							{ dataField: 'left', displayText: 'Consumed', color: '#f6fafd', noHoverEff: true, customGradient: [
									[0, 1, 1],
									[100,1, 1]
								]
							}
						]
					},
				]
			};
			var pr_chart = dashboard.$parent.find('.dash_progress_chart_inner');
			pr_chart.jqxChart(settings);
		},
		draw_project_amr_program_id: function(datas, wg_setting){
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dashtitle-number">'+  datas.sum +'</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			$.each( datas , function( index, data){
				if(typeof data == 'object'){
					var percent = number_format(data.count * 100 / datas.sum, 1) + '%';
					_html_content += '<div class="dash-program-item"><div class="program-item-content">';
							_html_content += '<span class="program-progres" style ="width: '+ percent +'"></span>';
							_html_content += '<span class="program-item-name">'+ data.name +'</span>';
							_html_content += '<p class="program-item-right">';
								_html_content += '<span class="program-item-count">'+ data.count +'</span>';
								_html_content += '<span class="program-item-percent"><span>'+ percent +'</span></span>';
							_html_content += '</p>';
						_html_content += '</div>';
					_html_content += '</div>';
				}
			});
			_html_content += '</div></div>';
			return _html_head + _html_content;		
		},
		draw_dash_late_milestones: function(data, wg_setting){
			var wg_name = 'count_projects';
			var dashboard = this;
			if( typeof data == 'undefined') data = wg_setting['data'];
			var _html = '';
			_html += '<div class="dash-widget-title">';
			_html += '<span class="title-icon">' + wg_setting.icon + '</span>';
			_html += '<p class="dashtitle">' + wg_setting.title + '</p>';
			_html += '<p class="dashtitle-number">' + data.length + '</p>';
			_html += '</div>';
			_html += '<div class="dash-widget-content"><div class="dash-widget-inner">';
			var item = '<div class="dash-milestone-item"><div class="milestone-item-content"><p class="milestone-item-project">%s</p><p class="milestone-item-name"><span class="item-name-text">%s</span><span class="item-date"> - %s</span></p></div></div>';

			$.each( data, function( i, pr_data){
				_html += sprintf(item, pr_data.project_name,  pr_data['project_milestone'],  pr_data.date);
			});
			_html += '</div></div>';
			return _html;
		},
		draw_dash_next_milestones: function(data, wg_setting){
			var wg_name = 'count_projects';
			var dashboard = this;
			if( typeof data == 'undefined') data = wg_setting['data'];
			var _html = '';
			_html += '<div class="dash-widget-title">';
			_html += '<span class="title-icon">' + wg_setting.icon + '</span>';
			_html += '<p class="dashtitle">' + wg_setting.title + '</p>';
			_html += '<p class="dashtitle-number">' + data.length + '</p>';
			_html += '</div>';
			_html += '<div class="dash-widget-content"><div class="dash-widget-inner">';
			var item = '<div class="dash-milestone-item"><div class="milestone-item-content"><p class="milestone-item-project">%s</p><p class="milestone-item-name"><span class="item-name-text">%s</span><span class="item-date"> - %s</span></p></div></div>';
			$.each( data, function( i, pr_data){
				_html += sprintf(item, pr_data.project_name,  pr_data['project_milestone'],  pr_data.date);
			});
			_html += '</div></div>';
			return _html;
		},
		draw_dash_phase: function(data, wg_setting){
			/*
content:
	max_end_date_plan: "31-05-2021"
	max_end_date_real: "31-05-2021"
	min_start_date_plan: "27-09-2018"
	min_start_date_real: "01-09-2018"
	plan_end_date: "Plan end date"
	plan_start_date: "Plan start date"
	real_end_date: "Real end date"
	real_start_date: "Real start date"
	title: "GANTT"

data: 
	0:
		end_date_plan: "31-05-2021"
		end_date_real: "31-05-2021"
		phase_end_diff: "0d"
		project_id: 2885
		project_name: "Exosquelette d'aide aux activités"
		project_progress: "54%"
		start_date_plan: "27-09-2018"
		start_date_real: "01-09-2018"
*/
			if( $.isEmptyObject(data))  return '';
			if( $.isEmptyObject(data.data))  return '';
			var wg_name = 'list_phase';
			var dashboard = this;
			if( typeof data == 'undefined') data = wg_setting['data'];
			var _html_head = '', _html_content = '';
			var late_percent = '';
			var title = (data.content && data.content.title) ? data.content.title :  wg_setting.title ; 
			_html_content += '<div class="dash-widget-content"><div class="dash-widget-inner">';
			var phase_diff = data.content.phase_diff;
			var _class = 'total-row';
			_class += (data.content.late > 0 ? " red-color" : ' green-color');
			var item = '';
			
			// plan
			item += '<div class="dashboard-phase-row ' + _class + '">';
			item += '<div class="wg-project_title" data-project_id="total"></div>';
			item += '<div class="wd-value-compare clear fix">';
			item += '<div class="wg-phase-dates wg-phase-start wd-value-left">';
			item += '<div class="wg-phase-date wg-phase-plan wd-value-left" title="' + data.content.plan_start_date + '"><p class="wg-label">' + data.content.plan + '</p><span class="wg-value">' + data.content.min_start_date_plan  + '</span></div>';
			item += '</div>';
			item  += '<div class="wd-progress-slider"></div>';
			item += '<div class="wg-phase-dates wg-phase-end wd-value-right">';
			item += '<div class="wg-phase-date wg-phase-plan" title="' + data.content.plan_end_date  + '"><span class="wg-value">' + data.content.max_end_date_plan  + '<span class="wd-value-total">' + '' + '</span></span></div>';
			item += '</div>';
			item +=  '</div></div>';
			
			//real
			item += '<div class="dashboard-phase-row ' + _class + '">';
			item += '<div class="wg-project_title" data-project_id="total"></div>';
			item += '<div class="wd-value-compare clear fix">';
			item += '<div class="wg-phase-dates wg-phase-start wd-value-left">';
			item += '<div class="wg-phase-date wg-phase-real wd-value-left" title="' + data.content.real_start_date + '"><p class="wg-label">' + data.content.real + '</p><span class="wg-value">' + data.content.min_start_date_real  + '</span></div>';
			item += '</div>';
			item  += dashboard.draw_progress(dashboard.progress || 0);
			item += '<div class="wg-phase-dates wg-phase-end wd-value-right">';
			item += '<div class="wg-phase-date wg-phase-real" title="' + data.content.real_end_date  + '"><span class="wg-value">' + data.content.max_end_date_real + '<span class="wd-value-total">' + phase_diff + '</span></span></div>';
			item += '</div>';
			item +=  '</div></div>';
			
			_html_content += item;
			var count_late = 0;	
			$.each( data.data, function( i, pr_data){
				var is_late = parseInt( pr_data.phase_end_diff) > 0;
				var _class = is_late ? "red-color" : '';
				count_late += (is_late) ? 1: 0;
				var p_id = pr_data.project_id;
				pr_data.start_date_plan = (pr_data.start_date_plan != undefined) ? pr_data.start_date_plan : ' ';
				pr_data.start_date_real = (pr_data.start_date_real != undefined) ? pr_data.start_date_real : ' ';
				pr_data.end_date_real = (pr_data.end_date_real != undefined) ? pr_data.end_date_real : ' ';
				pr_data.phase_end_diff = (pr_data.phase_end_diff != undefined) ? pr_data.phase_end_diff : ' ';
				pr_data.end_date_plan = (pr_data.end_date_plan != undefined) ? pr_data.end_date_plan : ' ';
				
				var item = '<div class="dashboard-phase-row ' + _class + '" data-project_id="' + p_id + '">';
				item += '<div class="wg-project_title" data-project_id="' + p_id + '">' + pr_data.project_name + '</div>';
				item += '<div class="wd-value-compare clear  fix">';
				
				item += '<div class="wg-phase-dates wg-phase-start wd-value-left">';
				item += '<div class="wg-phase-date wg-phase-plan" title="' + data.content.plan_start_date + '"><p class="wg-label">' + data.content.plan + '</p><span class="wg-value">' + pr_data.start_date_plan  + '<span class="wd-value-total">' + '' + '</span></span></div>';
				item += '<div class="wg-phase-real" title="' + data.content.real_start_date + '"><p class="wg-label">' + data.content.real + '</p><span class="wg-value">' + pr_data.start_date_real  + '<span class="wd-value-total">' + '' + '</span></span></div>';
				item += '</div>';
				
				item  += dashboard.draw_progress(pr_data.project_progress);
				
				item += '<div class="wg-phase-dates wg-phase-end wd-value-right">';
				item += '<div class="wg-phase-date wg-phase-plan" title="' + data.content.plan_end_date  + '"><p class="wg-label">' + data.content.plan + '</p><span class="wg-value">' + pr_data.end_date_plan  + '<span class="wd-value-total">' + '' + '</span></span></div>';
				item += '<div class="wg-phase-real" title="' + data.content.real_end_date  + '"><p class="wg-label">' + data.content.real + '</p><span class="wg-value">' + pr_data.end_date_real  + '<span class="wd-value-total">' + pr_data.phase_end_diff + '</span></span></div>';
				item += '</div>';
				
				item +=  '</div></div>';
				_html_content += item;
			});
			_html_content += '</div></div>';
			late_percent = Math.round(count_late * 100 / data.data.length);
			_html_head += '<div class="dash-widget-title">';
			_html_head += '<span class="title-icon">' + wg_setting.icon + '</span>';
			_html_head += '<p class="dashtitle">' + title + '</p>';
			_html_head += '<p class="dashtitle-textright">' + data.content.late_project + '</p>';
			_html_head += '<p class="dashtitle-number">'+ count_late +' - '+ late_percent +'%</p>';
			_html_head += '</div>';
			
			return _html_head + _html_content;
		},
		draw_select_financeplus: function(){
			var dashboard = this;
			var selectYear = '<select name="budget-finance-select-year" rel="no-history" class="budget-date-select">';
			var minYear = dashboard.data.financeplus.fin_min_year;
			var maxYear = dashboard.data.financeplus.fin_max_year;
			for(var n = minYear; n <= maxYear; n++){
				var selected = (n == finan_curent_selected) ? 'selected' : '';
				selectYear +='<option value='+ n +' '+ selected +'>'+ n +'</option>';
			}
			selectYear +='</select>';
			return selectYear;
		},
		draw_dash_financeplusinv: function(data, wg_setting){
			data_finance = data;
			var _width_progress = 360;
			if(typeof data !== 'undefined'){
				_width_progress = data.length * 60 < 360 ? 360 : data.length * 60;
			}
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p>'+ this.draw_select_financeplus() +'<p class="progress-legend"><span class="legend-dot legend-dot-budget"></span><span class="legend-text">'+ i18n['Budget'] +'</span><span class="legend-dot legend-dot-engaged"></span><span class="legend-text">'+ i18n['Engaged'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			// progress column
			_html_content += '<div class="dash-progress-column"><div class="progress-column-inner"><div id="chartFinanaceInv"  style ="height: 150px; width: '+ _width_progress +'px"></div></div>';
			_html_content += '</div></div>';
			
			return _html_head + _html_content;	
		},
		draw_dash_financeplusfon: function(data, wg_setting){
			data_finance = data;
			var _width_progress = 360;
			if(typeof data !== 'undefined'){
				_width_progress = data.length * 60 < 360 ? 360 : data.length * 60;
			}
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p>'+ this.draw_select_financeplus() +'<p class="progress-legend"><span class="legend-dot legend-dot-budget"></span><span class="legend-text">'+ i18n['Budget'] +'</span><span class="legend-dot legend-dot-engaged"></span><span class="legend-text">'+ i18n['Engaged'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			// progress column
			_html_content += '<div class="dash-progress-column"><div class="progress-column-inner"><div id="chartFinanaceFon" style ="height: 150px; width: '+ _width_progress +'px"></div></div>';
			_html_content += '</div></div>';
			
			return _html_head + _html_content;	
		},
		draw_dash_financeplusfinaninv: function(data, wg_setting){
			data_finance = data;
			var _width_progress = 360;
			if(typeof data !== 'undefined'){
				_width_progress = data.length * 60 < 360 ? 360 : data.length * 60;
			}
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p>'+ this.draw_select_financeplus() +'<p class="progress-legend"><span class="legend-dot legend-dot-budget"></span><span class="legend-text">'+ i18n['Budget'] +'</span><span class="legend-dot legend-dot-engaged"></span><span class="legend-text">'+ i18n['Engaged'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			// progress column
			_html_content += '<div class="dash-progress-column"><div class="progress-column-inner"><div id="chartFinanaceFinanInv" style ="height: 150px; width: '+ _width_progress +'px"></div></div>';
			_html_content += '</div></div>';
			
			return _html_head + _html_content;	
		},
		draw_dash_financeplusfinanfon: function(data, wg_setting){
			data_finance = data;
			var _width_progress = 360;
			if(typeof data !== 'undefined'){
				_width_progress = data.length * 60 < 360 ? 360 : data.length * 60;
			}
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p>'+ this.draw_select_financeplus() +'<p class="progress-legend"><span class="legend-dot legend-dot-budget"></span><span class="legend-text">'+ i18n['Budget'] +'</span><span class="legend-dot legend-dot-engaged"></span><span class="legend-text">'+ i18n['Engaged'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			// progress column
			_html_content += '<div class="dash-progress-column"><div class="progress-column-inner"><div id="chartFinanaceFinanFon" style ="height: 150px;  width: '+ _width_progress +'px"></div></div>';
			_html_content += '</div></div>';
			
			return _html_head + _html_content;	
		},
		draw_dash_internalbudgeteuro: function(data, wg_setting){
			data_internal = data.progress_line;
			var _width = data_internal ? (data_internal.length) * 50 : 120;
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dash-title-budget-percent">'+ data.count_project_exceed_budget + ' - ' + Math.round((data.count_project_exceed_budget/data.count_project)*100) +'%<span>'+ i18n['Off budget projects'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="dash-progress-circle">';
				_html_content += '<div id = "dash-widget-circle" data-outer="'+ ( data.percent_consumed_euro || 0 )+'" data-middle="'+ ( data.percent_forecast_euro || 0 ) +'" ></div>';				
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Internal Budget €'] +'</span><p>'+ data.budget_euro +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Internal Forecast €'] +'</span><p class="dash-budget-percent forecast"><span class="dash-tooltip">'+ i18n['Internal Forecast €'] +'/'+ i18n['Internal Budget €'] +'</span>'+ Math.round(data.percent_forecast_euro || 0) +'%</p><p>'+ data.forecast_euro +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Internal Engaged €'] +'</span><p class="dash-budget-percent consumed"><span class="dash-tooltip">'+ i18n['Internal Engaged €'] +'/'+ i18n['Internal Forecast €'] +'</span>'+ Math.round(data.percent_consumed_euro || 0) +'%</p><p>'+ data.consumed_euro +'</p></div>';
			_html_content += '</div>';
			
			// progress line
			_html_content += '<div class="dash-progress-line"><div class="progress-line-inner"><div id="chartContainerIn" style ="height: 200px; width: '+ _width +'px"></div></div>';
			_html_content += '<span id="chartContainerIn-left" class="scroll-progress scroll-progress-left"></span><span id="chartContainerIn-right" class="scroll-progress scroll-progress-right"></span></div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_dash_internalbudgetmd: function(data, wg_setting){
			data_internal_md = data.progress_line;
			var _width = data_internal_md ? (data_internal_md.length) * 50 : 120;
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dash-title-budget-percent">'+ data.count_project_exceed_budget + ' - ' + Math.round((data.count_project_exceed_budget/data.count_project)*100) +'%<span>'+ i18n['Off budget projects'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="dash-progress-circle">';
				_html_content += '<div id = "dash-widget-circle-md" data-outer="'+ ( data.percent_consumed_md || 0 )+'" data-middle="'+ ( data.percent_forecast_md || 0 ) +'" ></div>';				
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Internal Budget M.D'] +'</span><p>'+ data.budget_md +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Internal Forecast M.D'] +'</span><p class="dash-budget-percent forecast"><span class="dash-tooltip">'+ i18n['Internal Forecast M.D'] +'/'+ i18n['Internal Budget M.D'] +'</span>'+ Math.round(data.percent_forecast_md || 0) +'%</p><p>'+ data.forecast_md +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Internal Engaged M.D'] +'</span><p class="dash-budget-percent consumed"><span class="dash-tooltip">'+ i18n['Internal Engaged M.D'] +'/'+ i18n['Internal Forecast M.D'] +'</span>'+ Math.round(data.percent_consumed_md || 0) +'%</p><p>'+ data.consumed_md +'</p></div>';
			_html_content += '</div>';
			
			// progress line
			_html_content += '<div class="dash-progress-line"><div class="progress-line-inner"><div id="chartContainerInMD" style ="height: 200px; width: '+ _width +'px"></div></div>';
			_html_content += '<span id="chartContainerIn-left" class="scroll-progress scroll-progress-left"></span><span id="chartContainerIn-right" class="scroll-progress scroll-progress-right"></span></div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_dash_externalbudget: function(data, wg_setting){
			data_external_euro = data.progress_line;
			var _width = data_external_euro ? (data_external_euro.length) * 50 : 120;
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dash-title-budget-percent">'+ data.count_project_exceed_budget + ' - ' + Math.round((data.count_project_exceed_budget/data.count_project)*100) +'%<span>'+ i18n['Off budget projects'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="dash-progress-circle">';
				_html_content += '<div id = "dash-external-circle" data-outer="'+ ( data.percent_ordered_euro || 0 )+'" data-middle="'+ ( data.percent_forecast_euro || 0 ) +'" ></div>';				
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['External Budget Euro'] +'</span><p>'+ data.budget_euro +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['External Forecast Euro'] +'</span><p class="dash-budget-percent forecast"><span class="dash-tooltip">'+ i18n['External Forecast Euro'] +'/'+ i18n['External Budget Euro'] +'</span>'+ Math.round(data.percent_forecast_euro || 0) +'%</p><p>'+ data.forecast_euro +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['External Ordered Euro'] +'</span><p class="dash-budget-percent consumed"><span class="dash-tooltip">'+ i18n['External Ordered Euro'] +'/'+ i18n['External Forecast Euro'] +'</span>'+ Math.round(data.percent_ordered_euro || 0) +'%</p><p>'+ data.ordered_euro +'</p></div>';
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-line"><div class="progress-line-inner"><div id="chartContainerEx" style ="height: 200px; width: '+ _width +'px"></div></div>';
			_html_content += '<span id="chartContainerEx-left" class="scroll-progress scroll-progress-left"></span><span id="chartContainerEx-right" class="scroll-progress scroll-progress-right"></span></div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_dash_synthesis: function(data, wg_setting){
			data_synthesis_euro = data.progress_line;
			var _width = data_synthesis_euro ? (data_synthesis_euro.length) * 50 : 120;
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dash-title-budget-percent">'+ data.count_project_exceed_budget + ' - ' + Math.round((data.count_project_exceed_budget/data.count_project)*100) +'%<span>'+ i18n['Off budget projects'] +'</span></p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="dash-progress-circle">';
				_html_content += '<div id = "dash-syns-circle" data-outer="'+ ( data.percent_consumed_euro || 0 )+'" data-middle="'+ ( data.percent_forecast_euro || 0 ) +'" ></div>';				
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['BudgetEuro'] +'</span><p>'+ data.budget_euro +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['ForecastEuro'] +'</span><p class="dash-budget-percent forecast"><span class="dash-tooltip">'+ i18n['ForecastEuro'] +'/'+ i18n['BudgetEuro'] +'</span>'+ Math.round(data.percent_forecast_euro || 0) +'%</p><p>'+ data.forecast_euro +'</p></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Synthesis Engaged €'] +'</span><p class="dash-budget-percent consumed"><span class="dash-tooltip">'+ i18n['Synthesis Engaged €'] +'/'+ i18n['ForecastEuro'] +'</span>'+ Math.round(data.percent_consumed_euro || 0) +'%</p><p>'+ data.consumed_euro +'</p></div>';
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-line"><div class="progress-line-inner"><div id="chartContainerSynth" style ="height: 200px; width: '+ _width +'px"></div></div>';
			_html_content += '<span id="chartContainerSyn-left" class="scroll-progress scroll-progress-left"></span><span id="chartContainerSyn-right" class="scroll-progress scroll-progress-right"></span></div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_dash_progress_all_tasks: function(data, wg_setting){
			data_status = [];
			var colors = ['#217FC2','#6EAF79','#F2A673'];
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="progress-circle-task">';
			_html_content += '<div class="dash-progress-content">';
			var n = 0;
			// Defined status colors
			var line_colors = [];
			for (var status_id in typeTaskStatus) {
				if(typeof typeTaskStatus[status_id] !== 'undefined'){
					if(typeTaskStatus[status_id] == 'CL') line_colors[status_id] = '#888888';
					else{
						line_colors[status_id] = colors[n++%3]
					}
				}
			}
			m_circle_colors = line_colors;
			var color_background = ['#d4d4d4cc','#BCD8ED','#96c39d80'];
			var i = 0;
			var percentIP = 0;
			for (var status_id in data['task_status']) {
				if(typeof projectStatus[status_id] !== 'undefined'){
					var sta_percent = Math.round(calculatePercent(data['task_status'][status_id], data['count_status']));
					data_status[status_id] = sta_percent;
					if(typeTaskStatus[status_id] == 'IP') percentIP += sta_percent;
					
					_html_content += '<div class="dash-internal-item"><span style="color:'+ line_colors[status_id] +'">'+ projectStatus[status_id] +'</span><p class="dash-budget-percent forecast" style="background-color:'+ color_background[i] +';color:'+ line_colors[status_id] +'" >'+ data['task_status'][status_id] +'</p><p style="color:'+ line_colors[status_id] +';float: right;width: 40px;">'+ sta_percent +'%</p></div>';
					i++;
				}
				
			}
			_html_content += '</div>';
			_html_content += '<div class="progress-circle-task-inner dash-progress-circle"><div class="circle-wrapper"><span class="ip-percent">'+ percentIP +'%</span><div id="circle-task-graph" width = 135  style="height: 140px; "></div></div></div></div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_progress: function(pr_data){
			var _progress = pr_data || '0%';
			var _val = parseFloat( _progress);
			var _display_val = parseInt( Math.max(0, Math.min(_val, 100)));
			var _class = _val> 100 ? 'red-line' : 'green-line';
			var res = '';
			res += '<div class="wd-progress-slider ' + _class + '" data-value="' + _val + '">';
				res += '<div class="wd-progress-holder">';
					res += '<div class="wd-progress-line-holder"></div>';
				res += '</div>';
				res += '<div class="wd-progress-value-line" style="width: ' + _display_val + '%;"></div>';
				res += '<div class="wd-progress-value-text">';
					res += '<div class="wd-progress-value-inner">';
						res += '<div class="wd-progress-number" style="left: ' + _display_val + '%;">';
							res += '<div class="text">' + _display_val + '%</div> ';
							res += '<input class="input-progress wd-hide" value="' + _val + '">';
			res += '</div></div></div></div>';
			return res;
		},
		draw_dash_project_managers: function(datas, wg_setting){
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dashtitle-number">'+  datas.sum +'</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			var data_sort = sortObjectByNumber(datas);
			$.each( data_sort , function( index, data){
				
				if(typeof data !== 'undefined' && typeof data.manager_id !== 'undefined'){
					var textProject = data.count > 1 ? i18n['projects'] : i18n['project'];
					_html_content += '<div class="dash-pm-item"><div class="pm-item-content">';
						_html_content += '<div class="pm-item-avatar"><img src="/img/avatar/'+ data.manager_id +'.png" alt="'+ data.name +'"/></div>';
						_html_content += '<div class="pm-item-text"><p class="pm-item-name">'+ data.name +'</p><p class="pm-item-number-project">'+ data.count +' '+ textProject +'</p></div>';
						_html_content += '<div class="pm-item-percent">'+ data.percent +'%</div>';
					_html_content += '</div></div>';
				}
			});
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_project_status_id: function(data, wg_setting){
			return this.draw_select_field(data, wg_setting);
		},
		draw_select_field: function(data, wg_setting){
			var count = 0;
			$.each( data, function(i,v){
				if(typeof v.name !== 'undefined') count += v.count;
			});
			var sum = this.project_ids.length;
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="dashtitle-number">'+  count +'</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			$.each( data , function( index, item){
				if(typeof item == 'object' && typeof item.name !== 'undefined'){
					var percent = number_format(item.count * 100 / sum, 1) + '%';
					var item_name = typeof item.name !== 'undefined' ? (item.name).replaceAll('&nbsp;', '').replaceAll('|-', '') : '';
					_html_content += '<div class="dash-program-item"><div class="program-item-content">';
							_html_content += '<span class="program-progres" style ="width: '+ percent +'"></span>';
							_html_content += '<span class="program-item-name">'+ item_name +'</span>';
							_html_content += '<p class="program-item-right">';
								_html_content += '<span class="program-item-count">'+ item.count +'</span>';
								_html_content += '<span class="program-item-percent"><span>'+ percent +'</span></span>';
							_html_content += '</p>';
						_html_content += '</div>';
					_html_content += '</div>';
				}
			});
			_html_content += '</div></div>';
			return _html_head + _html_content;		
		},
		draw_dash_profit_center_estimated: function(data, wg_setting, key){
			var dashboard = this;
			var list_pc_manager = dashboard.data.Company.ProfitCenterManager;
			var Pcs = dashboard.data.Company.PlanPC;
			var pc_selected = 0;
			var date_selected = year_current;
			// History filter
			var acti = dashboard.get_dashboard_acti();
			var dashboard_histories = dashboard.dashboard_histories;
			var name = dashboard.def_name + ' ' + (acti+1);
			if( !$.isEmptyObject(dashboard_histories[acti])){
				var active_dash = dashboard_histories[acti]['dashboard_data'];
				if(typeof active_dash['planed_forecast'] !== 'undefined'){
					var histories = active_dash['planed_forecast'];
					date_selected = typeof histories['date_'+ key] !== 'undefined' ? histories['date_'+ key] : date_selected;
					pc_selected = typeof histories[key] !== 'undefined' ? histories[key] : pc_selected;
				}
			}
			var pc_forecast = data['pc_forecasts'];
			
			var avatar = '';
			if(typeof list_pc_manager[pc_selected] !== 'undefined' && typeof (list_avatar[list_pc_manager[pc_selected]]) !== 'undefined' && typeof (list_avatar[list_pc_manager[pc_selected]]['tag']) !== 'undefined'){
				avatar = typeof list_avatar[list_pc_manager[pc_selected]] !== 'undefined' ? list_avatar[list_pc_manager[pc_selected]]['tag'] : '';
			}
			
			var _pc_select = '<select name="'+ key +'" id = "'+ key +'" class="pc_forecast">';
			_pc_select += '<option value="">' + i18n['Select team'] + '</option>';
			$.each( Pcs , function( key, pcVal){
				var atr_selected = pc_selected == pcVal['id'] ? 'selected' : '';
				_pc_select += '<option value ="'+ pcVal['id'] +'" '+ atr_selected +'>'+ pcVal['name'] +'</option>';
			});
			_pc_select += '</select>';
			var forecast_date_select = '<input name="date_'+ key +'" id = "date_'+ key +'" class="forecast-date-select" value = '+ date_selected +' />';
			
			var _html_head = '<div class="dash_plan_capacity"><div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p><p class="manager-avatar">'+ avatar +'</p><p class="select-pc">'+forecast_date_select + _pc_select +'</p></div>';
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner"><ul class="list-pc-estimated">';
			_html_content += dashboard.render_forecast_template_empty(month_i18n, date_selected);
			_html_content += '</ul></div></div></div>';
			return _html_head + _html_content;	
		},
		render_forecast_template_empty: function(list_months, date_selected){
			var _html_content = '';
			var month_selected = date_selected.split('/')[0];
			var x = month_selected;
			var n = 0;
			$.each( list_months , function( key, date){
				n++;
				if(n >= x){
					_html_content += '<li><div class="pc-estimated-item"><div class="pc-estimated-column"><p class="pc-estimated-content">0%<span>0/0</span></p></div><span class="month-text">'+ date +'.</span></div></li>';
				}
			});
			var m = 0;
			$.each( list_months , function( key, date){
				m++;
				if(m < x){
					_html_content += '<li><div class="pc-estimated-item"><div class="pc-estimated-column"><p class="pc-estimated-content">0%<span>0/0</span></p></div><span class="month-text">'+ date +'.</span></div></li>';
				}
			});
			return _html_content;
		},
		render_content_profit_center_estimated: function(estimated, pc_id, date_selected){
			var dashboard = this;
			var _html_content = '<ul class="list-pc-estimated">';
			if(typeof estimated === 'undefined'){
				_html_content += dashboard.render_forecast_template_empty(month_i18n, date_selected);
			}else{
				var class_current = '';
				$.each( estimated , function( date, value){
					var text_month = value['text_month'].split('-');
					var text_column = month_i18n[text_month[0]]+'.';
					if(value['text_month'] == month_scroll){
						class_current="current_forcast";
					}
					var ex_action = 'target="_blank" href="/activity_forecasts_preview/manages/month/'+ pc_id +'/'+ date +'" ';
					var forecast_percent = Math.round(calculatePercent(value['value'], value['capcity']));
					var over_class = (value['value'] > value['capcity']) ? 'over-item' : '';
					forecast_percent = (forecast_percent > 100) ? 100 : forecast_percent ;
					_html_content += '<li class="'+ class_current +'"><div class="pc-estimated-item"><a '+ ex_action +' class="pc-estimated-column"><span class="bg-percent '+ over_class +'" style="height: '+forecast_percent+'%"></span><p class="pc-estimated-content">'+ forecast_percent +'%<span>'+value['value'].toFixed(1)+'/'+ value['capcity'] +'</span></p></a><span class="month-text">'+ text_column +'</span></div></li>';
				});
			}
			_html_content += '</ul>';
			
			return _html_content;
		}, 
		draw_yesno_field: function(data, wg_setting){
			data_project_yesno = [];
			if(data['count'] == 0){
				var percent_no = 0;
				var percent_yes = 0;
			}
			var percent_no = Math.round(calculatePercent(data['no'], data['count']));
			data_project_yesno[0] = percent_no;
			var percent_yes = Math.round(calculatePercent(data['yes'], data['count']));
			data_project_yesno[1] = percent_yes;
			
			test_data[data.field] = percent_no;
			
			field_yesno[data.field] = data.field;
			
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			
			var _html_content = '<div class="dash-widget-content"><div class="dash-widget-inner">';
			_html_content += '<div class="progress-circle-task">';
			
			_html_content += '<div class="progress-circle-task-inner dash-progress-circle"><div class="circle-wrapper"><span style= "color: #F59796" class="ip-percent">'+ percent_no +'%</span><div id="circle-project-yesno-graph-'+ data.field +'" width = 135  style="height: 140px; "></div></div></div>';
			
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item"><span style="color: #f69897;">'+ i18n['No'] +'</span><p class="dash-budget-percent forecast" style="background-color:#f698977d;color: #f69897;margin-left: auto;">'+ data['no'] +'</p><p style="color: #f69897;width:40px;">'+ percent_no +'%</p></div>';
				_html_content += '<div class="dash-internal-item"><span style="color: #75b37f;">'+ i18n['Yes'] +'</span><p class="dash-budget-percent consumed" style="background-color:#75b37f87;color: #75b37f;margin-left: auto;">'+ data['yes'] +'</p><p style="color: #75b37f;width:40px;">'+ percent_yes+'%</p></div>';
			_html_content += '</div>';
			
			_html_content += '</div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;	
		},
		draw_number_field: function(data, wg_setting){
			var num_sum = number_format(data.sum, 2, ',', ' ');
			var project_target = data.target;
			var num_target = number_format(project_target['value'], 2, ',', ' ');
			if((data.field).indexOf('price_') != -1){
				num_target = num_target  + ' ' + budget_settings;
				num_sum = num_sum  + ' ' + budget_settings;
			}
			var updated_by = typeof project_target['updated'] !== 'undefined'  ? i18n['Updated by'] + ' '+ project_target['employee'] + ' ' + project_target['updated'] : '';
			var wd_tooltip = typeof project_target['updated'] !== 'undefined' ? '<span class="dash-tooltip">'+ updated_by +'</span>' : '';
			var over_class = (data.sum > project_target['value']) ? 'dash-cible-over' : '';
			var color_outer = (data.sum > project_target['value']) ? '#F05352' : '#79B2DA';
			field_number[data.field] = data.field;
			var _html_head = '<div class="dash-widget-title"><span class="title-icon">' + wg_setting.icon + '</span><p class="dashtitle">' + wg_setting.title + '</p></div>';
			var _html_content = '<div class="dash-widget-content dash-widget-content-number" ><div class="dash-widget-inner">';
			_html_content += '<div class="dash-progress-circle">';			
				_html_content += '<div id = "dash-widget-circle-'+ data.field +'" data-color-outer = "'+color_outer+'" data-outer="'+ ( data.target_percent || 0 )+'" style="width: 125px" ></div>';				
			_html_content += '</div>';
			_html_content += '<div class="dash-progress-content">';
				_html_content += '<div class="dash-internal-item dash-cible-item '+over_class+'">'+ wd_tooltip+'<span>'+ i18n['Target'] +'</span><p class="cible-value">'+ num_target +'</p><span class="dash-input-editor"><input data-type= "'+ data.field +'" class="dash-cible" type="number" name="target-'+data.field+'" min="0" value = '+ project_target['value'] +'><input type="hidden" name="'+data.field+'" value = '+ data.sum +'><a href="javascript:void(0);" class="cible-open-input">'+ dashboard_icons['pen'] +'</a></span></div>';
				_html_content += '<div class="dash-internal-item"><span>'+ i18n['Total'] +'</span><p>'+ num_sum +'</p></div>';
			_html_content += '</div>';
			_html_content += '</div></div>';
			return _html_head + _html_content;			
		},
		define_yourform_get_func: function(){
			var dashboard = this;
			var list_select_yourform = dashboard.list_select_yourform;
			$.each ( list_select_yourform, function( i, n){
				var func = 'get_data_dash_' + n;
				dashboard[func] = function(){
					return dashboard.get_data_select(n);
				}
			});
			var list_yesno_yourform = dashboard.list_yesno_yourform;
			$.each ( list_yesno_yourform, function( i, n){
				var func = 'get_data_dash_' + n;
				dashboard[func] = function(){
					return dashboard.get_data_yesno(n);
				}
			});
			var list_number_yourform = dashboard.list_number_yourform;
			$.each ( list_number_yourform, function( i, n){
				var func = 'get_data_dash_' + n;
				dashboard[func] = function(){
					return dashboard.get_data_number(n);
				}
			});
			var list_multiselect_yourform = dashboard.list_multiselect_yourform;
			$.each ( list_multiselect_yourform, function( i, n){
				var func = 'get_data_dash_' + n;
				dashboard[func] = function(){
					return dashboard.get_data_multiselect(n);
				}
			});
		},
		define_yourform_draw_func: function(){
			var dashboard = this;
			var draw_your_form = {};
			var list_select_yourform = dashboard.list_select_yourform;
			$.each ( list_select_yourform, function( i, n){
				var func = 'draw_dash_' + n;
				dashboard[func] = function(data, wg_setting){
					return dashboard.draw_select_field(data, wg_setting);
				}
			});
			
			// yes/no x
			var list_yesno_yourform = dashboard.list_yesno_yourform;
			$.each ( list_yesno_yourform, function( i, n){
				var func = 'draw_dash_' + n;
				dashboard[func] = function(data, wg_setting){
					return dashboard.draw_yesno_field(data, wg_setting);
				}
			});
			
			// number_x, price_x
			var list_number_yourform = dashboard.list_number_yourform;
			$.each ( list_number_yourform, function( i, n){
				var func = 'draw_dash_' + n;
				dashboard[func] = function(data, wg_setting){
					return dashboard.draw_number_field(data, wg_setting);
				}
			});
			// plan_capacity_x
			var list_plan_capacity = dashboard.list_plan_capacity;
			$.each ( list_plan_capacity, function( i, n){
				var func = 'draw_dash_' + n;
				dashboard[func] = function(data, wg_setting){
					return dashboard.draw_dash_profit_center_estimated(data, wg_setting, n);
				}
			});
		},
		after_init: function(){
			this.define_yourform_get_func();
			this.define_yourform_draw_func();
			this.display_multi_dashboard();
			this.setting_event();
			this.init_sortable();
		},
		display_multi_dashboard: function(){
			if( ! this.multi_dashboard_container) return;
			var container = $(this.multi_dashboard_container);
			if( !container.length) return;
			var dropdown = this.el.find('.dash_multi_dashboard_select');
			container.append( dropdown[0].outerHTML );
			dropdown.remove();
			// var html = '';
			// html += '<div class="wd-dropdown dashboard_select_dropdown">';
			// html += '<div class="wd-dropdown dashboard_select_dropdown">';
			// html += '</div>';
			// html += '</div>';
			if( this.display) container.find('.dash_multi_dashboard_select').show();
			this.multi_dashboard_event();
			
		},
		get_display_default: function(){
			var dashboard = this;
			var display = [];
			// $.each( dashboard.all_widget_default, function( row, widgets){
				// $.each(widgets, function(n, st){
					// display.push(n);
				// });
			// });
			return display;
		},
		recusive_copy_object: function(obj){
			var dashboard = this;
			if( typeof obj != 'object') return obj;
			if( $.isArray(obj)){
				var new_obj = [];
				$.each(obj, function (i,v){
					if( typeof v == 'object') new_obj.push( dashboard.recusive_copy_object(v));
					else new_obj.push(v);
				});
				return new_obj;
			}else{
				var new_obj = {};
				$.each(obj, function (i,v){
					if( typeof v == 'object') new_obj[i] = dashboard.recusive_copy_object(v);
					else new_obj[i] = v;
				});
				return new_obj;
			}
		},
		get_order_default: function(){
			var dashboard = this;
			var order = {};
			$.each( dashboard.all_widget_default, function( row, widgets){
				order[row] = [];
				$.each(widgets, function(n, st){
					order[row].push(n);
				});
			});
			return order;
		},
		edit_number_event: function(){
			var dashboard = this;
			dashboard.$parent.find('.cible-open-input').off('click').on('click', function(e){
				var _this = $(this);
				_this.closest('.dash-cible-item').toggleClass('active');
			});
			$('body').on('click', function(e){
				var cont = dashboard.$parent.find('.dash-cible-item')
				if((!cont.hasClass('active')) || (cont.hasClass('loading')) || $(e.target).is(cont) || cont.find(e.target).length) return;
				cont.removeClass('active');
			});
			dashboard.$parent.find('input.dash-cible').off('change').on('change', function(e){
				var _this = $(this);
				var _parent_input = _this.closest('.dash-cible-item');
				_parent_input.addClass('loading');
				$.ajax({
					url: '/projects/updateProjectTarget',
					type: 'post',
					dataType: 'json',
					data: {
						type: _this.data('type'),
						value: _this.val()
					},
					beforeSend: function(){
						_parent_input.addClass('loading');
					},
					success: function(res){
						if( res != 0){
							dashboard.refresh_number_widget(_this.data('type'),res);
							_parent_input.removeClass('active');
							
						}else{;
							location.reload();
						}
					},
					error: function(){
						location.reload();
					},
					complete: function(){
						_parent_input.removeClass('loading');
					}
				});
				
			});
		},
		selected_pc_forecast: function(){
			var dashboard = this;
			dashboard.$parent.find('.forecast-date-select').off('change').on('change', function(e){
				var date_selected = $(this).val();
				if(date_selected){
					var _input_date = $('.forecast-date-select');
					if(_input_date.length > 0){
						$.each(_input_date, function(n, e){
							if($(e).length > 0 && date_selected != $(e).val()){
								$(e).val(date_selected);
								$(e).trigger('change');
							}
						});
					}
				}
				var plan_pc = $(this).closest('.dash-widget-title').find('.multiSelect');
				var pc_id = plan_pc.find('.selected-item').data('id');
				dashboard.ajaxGetPCForecast(pc_id, plan_pc);
			});
			$(".forecast-date-select").datepicker({
				changeYear: true,
				changeMonth: true,
				dateFormat: 'dd/yy',
			});
		},
		selected_date_budget: function(){
			var dashboard = this;
			// if()
			dashboard.$parent.find('.budget-date-select').off('change').on('change', function(e){
				var year = $(this).val();
				var ele_container = dashboard.$parent.find('.budget-date-select').closest('.dash-widget-item');
				if(ele_container.hasClass('.loading')) return;
				 $('.budget-date-select').val(year);
				$.ajax({
					url: '/projects/getFinanceData',
					type: 'post',
					dataType: 'json',
					data: {
						year: year,
						projectIds: projectIds,
					},
					beforeSend: function(){
						ele_container.addClass('loading');
					},
					success: function(res){
						if( res.result === 'success'){
							finan_curent_selected = year;
							dashboard.data.financeplus.values = res.data;
							console.log( res.data);
							if($('#chartFinanaceInv').length > 0){
								var data_inv = dashboard.get_data_budget_finance_plus('inv');
								var progress_inv_max = data_inv['progress_max'];
								data_finance = data_inv['data'];
								dashboard.draw_progress_coulumn_finance('#chartFinanaceInv', 'inv', progress_inv_max);
							}
							if($('#chartFinanaceFon').length > 0){
								var data_fon = dashboard.get_data_budget_finance_plus('fon');
								var progress_fon_max = data_fon['progress_max'];
								data_finance = data_fon['data'];
								dashboard.draw_progress_coulumn_finance('#chartFinanaceFon', 'fon', progress_fon_max);
							}
							if($('#chartFinanaceFinanInv').length > 0){
								var data_finaninv = dashboard.get_data_budget_finance_plus('finaninv');
								var progress_finaninv_max = data_finaninv['progress_max'];
								data_finance = data_finaninv['data'];
								dashboard.draw_progress_coulumn_finance('#chartFinanaceFinanInv', 'finaninv', progress_finaninv_max);
							}
							if($('#chartFinanaceFinanFon').length > 0){
								var data_finanfon = dashboard.get_data_budget_finance_plus('finanfon');
								var progress_finanfon_max = data_finanfon['progress_max'];
								data_finance = data_finanfon['data'];
								dashboard.draw_progress_coulumn_finance('#chartFinanaceFinanFon', 'finanfon', progress_finanfon_max);
							}
						}else{
							// location.reload();
						}
					},
					error: function(){
						// location.reload();
					},
					complete: function(){
						ele_container.removeClass('loading');
					}
				});
				// var plan_pc = $(this).closest('.dash-widget-title').find('.multiSelect');
				// var pc_id = plan_pc.find('.selected-item').data('id');
				// dashboard.ajaxGetPCForecast(pc_id, plan_pc);
			});
		},
		ajaxGetPCForecast: function(pc_id, _input){
			var dashboard = this;
			var year = _input.closest('.dash-widget-title').find('.forecast-date-select').val();
			var ele_container = _input.closest('.dash_plan_capacity');
			var wd_name = _input.attr('id');
			if(pc_id == 0) return;
			if(projectIds.length == 0) return;
			$.ajax({
				url: '/projects/getPCForecast',
				type: 'post',
				dataType: 'json',
				data: {
					pc_id: pc_id,
					year: year,
					projectIds: projectIds,
				},
				beforeSend: function(){
					ele_container.addClass('loading');
				},
				success: function(res){
					if( res.result === 'success'){
						dashboard.refresh_pc_forecast_widget(wd_name, res.data);
					}else{
						location.reload();
					}
				},
				error: function(){
					location.reload();
				},
				complete: function(){
					ele_container.removeClass('loading');
				}
			});
		},
		scrollToCurrentYear: function(_wd_element){
			var current_element = $(_wd_element).find('.current_forcast:first');
			if(current_element.length > 0){
				var scroll_to = $(current_element).position().left;
				$(_wd_element).find('.dash-widget-inner').animate({scrollLeft: scroll_to}, 200);
			}
		},
		setting_event: function(){
			var dashboard = this;
			var acti = dashboard.get_dashboard_acti();
			var dashboard_histories = dashboard.get('dashboard_histories');
			if( acti in dashboard_histories){
				var _dash = dashboard_histories[acti];
				if( _dash.employee_id != dashboard.current_employee) {
					dashboard.el.find('.projects-dashboard-setting').hide();
				}
				else{ dashboard.el.find('.projects-dashboard-setting').show(); }
			}
			$('#dashBtnSave').off('click').on('click', function(e){
				dashboard.saveHistory();
				dashboard.refresh();
			});
			function setting_block_height(){
				var popup = dashboard.el.find('.projects-dashboard-setting');
				var scroll = popup.find('.scroll-container');
				if( scroll.length){
					scroll.css('max-height', $(window).height() - scroll.offset().top - popup.find('.wd-submit').outerHeight(true) - 20);
				}
			}
			$(window).on('resize', setting_block_height);
			dashboard.el.find('.dashboard-setting-toggle').on('click', function(){
				var cont = $(this).parent();
				cont.toggleClass('active');
				if( cont.hasClass('active')){
					var inner = cont.find('.dash-setting-inner');
					inner.css('max-height', $(window).height() - inner.offset().top);
					cont.css({
						width: inner.outerWidth(),
						height: inner.outerHeight()
					});
				}else{
					cont.css({
						width: '',
						height: ''
					});
				}
			});
			$('body').on('click', function(e){
				var cont = dashboard.el.find('.projects-dashboard-setting:first');
				if( (!cont.hasClass('active')) || $(e.target).is(cont) || cont.find(e.target).length) return;
				cont.removeClass('active').css({
					width: '',
					height: ''
				});
			});
		},
		init_sortable: function(){
			var dashboard = this;
			dashboard.el.find('.projects-dashboard-setting .sortable-container').sortable({
				containment: 'parent',
				cursor : 'pointer',
				placeholder: "wd-sortable-placeholder",
				stop: function( event, ui ) {
					dashboard.get_widget_order();
				}
			});
		},
		multi_dashboard_event: function(){
			var dashboard = this;
			var container = $(this.multi_dashboard_container);
			var dropdown = container.find('.dashboard_select_dropdown');
			dropdown.find('.button_edit').off('click').on('click', function(e){
				var _this = $(this).addClass('wd-hide');
				var li = _this.closest('li').addClass('editing');
				var val = li.find('.dashboard-item').hide().data('text');
				li.find('.button_ok').removeClass('wd-hide');
				var _input = li.find('.input_edit');
				_input.val(val).removeClass('wd-hide').focus();
				_input.off('focusout').on('focusout', function(e){
					var _this = $(this).addClass('wd-hide');
					li.removeClass('editing');
					setTimeout( function(){
						li.find('.button_ok').addClass('wd-hide');
					}, 300);
					li.find('.button_edit').removeClass('wd-hide');
					li.find('.dashboard-item').show();
					
				});
				_input.off('keydown').on('keydown', function(e){
					if( e.keyCode == 13){
						$(this).closest('li').find('.button_ok').trigger('click');
					}
					return;
				});
			});
			dropdown.find('.dashboard-item-holder').off('click').on('click', function(e){
				$(this).closest('li').find('.button_edit').trigger('click');
			});
			dropdown.find('.button_share').off('click').on('click', function(e){
				var _this = $(this);
				dashboard.has_change = false;
				var li = _this.closest('li').addClass('share_open');
				var _dash_id = li.data('value');
				var dashboard_histories = dashboard.get('dashboard_histories');
				var _dash = dashboard_histories[_dash_id];
				var popup = dashboard.share_popup;
				var popup_title = popup.find('.dialog-title').text();
				// _dash
				popup.find('#share_dashboard_id').val(_dash.id);
				popup.find('.share_types').find('.radio_share_type :radio').val([(_dash.share_type)]);
				var _checked = popup.find('.share_types').find('.radio_share_type :radio').filter(':checked');
					_checked.closest('li').addClass('selected').siblings().removeClass('selected');
				dashboard.show_share_resources(_dash.share_type == 'resource');
				var multi_display_container = $(dashboard.multi_dashboard_container).find('.dash_multi_dashboard_select');
				popup.dialog({
					title: popup_title,
					position    :{my: "left top", at: "right top", of: _this.closest('li')},
					autoOpen    : true,
					closeOnEscape: false,
					modal       : true,
					draggable: false,
					open : function(e){
						popup.closest('.ui-dialog').addClass('ui-dialog-share-dashboard').css('left', parseInt(popup.closest('.ui-dialog').css('left')) + 4 );
						popup.dialog('option', 'position')
						multi_display_container.find('.dashboard_select_dropdown').addClass('keep-open');
						var overlay = $('body').children('.ui-widget-overlay');
						overlay.css('background', 'transparent');
						overlay.on('click', function(e){
							popup.dialog('close');
							var _parent = multi_display_container.find('.dashboard_select_dropdown');
							if( _parent.hasClass('keep-open')) _parent.addClass('open');
						});
					},
					beforeClose: function(e, ui){
						li.removeClass('share_open');
						dashboard.update_share_dashboard();
					},
					close: function(e, ui){
						var _parent = multi_display_container.find('.dashboard_select_dropdown');
						setTimeout( function(){
							if( _parent.hasClass('keep-open')) _parent.addClass('open').removeClass('keep-open');
						}, 200);
					},

				});
				dashboard.share_popup = popup;
				
				popup.find('.share_types').find('.radio_share_type :radio').off('change').on('change', function(e){
					dashboard.has_change = true;
					var _checked = popup.find('.share_types').find('.radio_share_type :radio').filter(':checked');
					_dash.share_type = _checked.val();
					_checked.closest('li').addClass('selected').siblings().removeClass('selected');
					dashboard.show_share_resources(_dash.share_type == 'resource');
				});
			});
			dropdown.find('.button_delete').off('click').on('click', function(e){
				var _this = $(this).addClass('wd-hide');
				var li = _this.closest('li');
				var key = li.find('.dashboard-item').data('value');
				var new_dashboards = {};
				var dashboard_histories = dashboard.dashboard_histories;
				var i = 0;
				$.each(dashboard_histories, function(k,v){
					if( k != key){
						new_dashboards[k] = v;
						i++;
					}
				});
				if( i< 10){
					dropdown.find('li.dash_add_new').show();
				}
				dashboard.dashboard_histories = new_dashboards;
				li.hide();
				setTimeout( function(){
					li.remove();
					dashboard.multi_dashboard_event();
				}, 100);
				$.ajax({
					url: '/project_dashboards/delete_dashboard/' + key,
					type: 'get',
					dataType: 'json',
					success: function(res){
						if( res.result == 'success'){
						}else{
							console.error(res);
						}
					}
				});
			});
			dropdown.find('.button_ok').off('click').on('click', function(e){
				// return;
				var _this = $(this).addClass('wd-hide');
				var li = _this.closest('li');
				var key = li.find('.dashboard-item').data('value');
				var is_new = li.hasClass('dash_add_new');
				var is_active = li.hasClass('active');
				var _input = li.find('.input_edit');
				var _text = _input.val();
				var _count = 0;
				$.each(dashboard.dashboard_histories, function(_k, _v){
					// if( _v['employee_id'] == dashboard.current_employee) 
						_count++;
				});
				$.ajax({
					url: '/project_dashboards/update_dashboard',
					type: 'post',
					dataType: 'json',
					data:{
						data: {
							dashboard_data: {
								display: dashboard.get_dashboard_display(key),
								order: dashboard.get_dashboard_order(key),
								name: _text,
							},
							id: key,
							// share_type: dashboard.get_share_type(key),
							// share_resource: dashboard.get_share_resource(key),
						}
						
					},
					success: function(res){
						var dashboard_histories = dashboard.get('dashboard_histories');

						if( res.result == 'success'){
							var data = res.data;
							key = data['id'];
							
							var _class = 'dashboard-item dashboard-item-' + key;
							_class += is_active ? ' active' : '';
							var _html = '<li class="' + _class + '" data-value="' + key + '">';
							_html += '<a href="javascript:void(0);" class="' + _class + '" data-text="' + _text + '" data-value="' + key + '">' + _text + '</a>';
							_html += '<span class="button_edit" >' + dashboard_icons['edit'] + '</span>';
							_html += '<input type="text" class="wd-hide input_edit" value="' + _text + '" data-key="' + key + '" />';
							_html += '<span class="button_ok wd-hide" >' + dashboard_icons['ok'] + '</span>';
							_html += '<span class="button_share button_share_nobody ' + (data.share_type == 'nobody' ? '' : 'wd-hide') + '">' + dashboard_icons['eye_close'] + '</span>';
							_html += '<span class="button_share button_share_resource ' + (data.share_type != 'nobody' ? '' : 'wd-hide') + '">' + dashboard_icons['eye'] + '</span>';
							_html += '<span class="button_delete" href="javascript: void(0);">' + dashboard_icons['delete'] + '</span>';
							_html += '</li>';
							
							if( is_new){
								var new_html = '';
								new_html +='<li class="dash_add_new"' + (_count > 9 ? ' style="display: none"' : '') + '">';
								new_html +='<span class="dashboard-item dashboard-item-holder" data-text="' + dashboard.def_name + ' ' + (_count+1) + '"  data-value="" >&nbsp;</span>';
								new_html +='<input type="text" class="dash_add_new wd-hide input_edit" value="' + dashboard.def_name + ' ' + (_count+1) + '" data-key="' + _count + '" />';
								new_html +='<span class="button_edit" >' + dashboard_icons['add'] + '</span>';
								new_html +='<span class="button_ok wd-hide" >' + dashboard_icons['ok'] + '</span>';
								new_html +='</li>';
								
								li.parent().append( $(_html) );
								li.parent().append( new_html );
								li.hide();
								setTimeout( function(){
									li.remove();
									dashboard.multi_dashboard_event();
								}, 100);
							}else{
								setTimeout( function(){
									li.html( $(_html).html() );
									if( is_active) wd_dropdown_setvalue(dropdown, key, false);
									dashboard.multi_dashboard_event();
								}, 100);
							}
							dashboard_histories[key] = data;
							dashboard.dashboard_histories = dashboard_histories;
						}else{
							console.error(res);
						}
					}
				});
				// var dashboard_histories = dashboard.get('dashboard_histories');
			});
			dropdown.find('.wd-dropdown-seleted').off('change').on('change', function(){
				var _this = $(this);
				var acti = dashboard.get_dashboard_acti();
				var dashboards = dashboard.get('dashboard_histories');
				if( acti in dashboards){
					dashboard.dashboard_histories['acti'] = acti;
					dashboard.update_data_from_history(dashboards[acti]['dashboard_data']);
					var _dash = dashboard.dashboard_histories[acti];
					if( _dash.employee_id != dashboard.current_employee) dashboard.el.find('.projects-dashboard-setting').hide();
					else{ dashboard.el.find('.projects-dashboard-setting').show(); }
					dashboard.reDrawSetting();
					if( dashboard.enable_widget.length > 10){
						dashboard.el.addClass('loading-mark loading');
						setTimeout( function(){
							dashboard.refresh();
							dashboard.el.removeClass('loading-mark loading');
						}, 50);
					}else{
						dashboard.refresh();
					}
					setTimeout( function(){
						$.ajax({
							url: '/project_dashboards/update_acti_dashboard/' + acti,
							dataType: 'json',
							type: 'get',
							success: function(res){
								if( res.result == 'success'){
								}else{
									console.error(res);
								}
							}
						});
					}, 400);
				}	
			});
			
		},
		drag_event: function(){
			var dashboard = this;
			var sliders = $('.dash-widget-row');
			$.each( sliders, function( i, tag){
				var slider = $(tag);
				var isDown = false;
				var startX;
				var scrollLeft;

				slider.off('mousedown').on('mousedown', function(e){
				  isDown = true;
				  slider.addClass('scrolling');
				  startX = e.pageX - slider.offset().left;
				  scrollLeft = slider.scrollLeft();
				});
				slider.off('mouseleave mouseup').on('mouseleave mouseup', function(e){
				  isDown = false;
				  slider.removeClass('scrolling');
				});
				slider.off('mousemove').on('mousemove', function(e){
				  if(!isDown) return;
				  e.preventDefault();
				  var x = e.pageX - slider.offset().left;
				  var walk = (x - startX) * 3; //scroll-fast
				  slider.scrollLeft(scrollLeft - walk);
				});
			});
		},
		reDrawSetting: function(){
			var dashboard = this;
			var order = dashboard.all_widget;
			var setting_elm = dashboard.el.find('.projects-dashboard-setting');
			setting_elm.find('.sortable-container').sortable('destroy');
			$.each(order, function( row, widgets){
				var cont = setting_elm.find('.' + row + '-headband .sortable-container');
				$.each( widgets, function( widget_name, widget_setting){
					var it = cont.children('.' + widget_name);
					if( $.inArray( widget_name, dashboard.enable_widget) != -1 ){
						it.find(':checkbox').prop('checked', true);
					}else{
						it.find(':checkbox').prop('checked', false);
					}
					it.appendTo(cont);
				});
			});
			dashboard.init_sortable();
		},
		get_dashboard_acti: function(){
			var multi_display_container = $(this.multi_dashboard_container);
			var val = 0;
			if( multi_display_container.find('.dash_multi_dashboard_select').length )
				val = multi_display_container.find('.dash_multi_dashboard_select').find('.wd-dropdown-seleted').val();
			return parseInt(val);
		},
		get_widget_display: function(){
			var display = [];
			var checked = this.el.find('.projects-dashboard-setting').find('input.dash-widget-enable:checked');
			// if( !checked.length) checked = this.el.find('.projects-dashboard-setting').find('input.dash-widget-enable');
			if( checked.length){
				$.each( checked, function(i, tag){
					display.push($(tag).attr('name'));
				});
			}
			return display;
		},
		get_widget_order: function(){
			var order = {};
			$.each( this.el.find('.projects-dashboard-setting').find('.setting-block'), function(i, b){
				var block = $(b);
				var row = block.data('row');
				order[row] = [];
				$.each( block.find('input.dash-widget-enable'), function(i, tag){
					order[row].push($(tag).attr('name'));
				});
			});
			return order;
		},
		get_value_selected: function(){
			var dashboard = this;
			var selected = {};
			if((dashboard.list_plan_capacity).length > 0){
				$.each((dashboard.list_plan_capacity), function( index, key){
					var _select_pc = $('#'+ key + '.multiSelect').find('.selected-item');
					var _select_date = $('input[name="date_'+ key +'"]');
					if(_select_pc.length > 0) selected[key] = _select_pc.data('id');
					if(_select_date.length > 0) selected['date_'+ key] = _select_date.val();
				});
			}
			return selected;
		},
		get_dashboard_order : function(dash_id){
			var dashboard = this;
			var dashboard_histories = dashboard.get('dashboard_histories');
			if( dash_id == 'default') dash_id = dashboard_histories['acti'];
			if( dash_id && (dash_id in dashboard_histories)){
				return  dashboard_histories[dash_id]['dashboard_data']['order'];
			}else{
				return dashboard.get_order_default();
			}
		},
		get_dashboard_display : function(dash_id){
			var dashboard = this;
			var dashboard_histories = dashboard.get('dashboard_histories');
			if( dash_id == 'default') dash_id = dashboard_histories['acti'];
			if( dash_id && (dash_id in dashboard_histories)){
				return  dashboard_histories[dash_id]['dashboard_data']['display'];
			}else{
				return dashboard.get_display_default();
			}
		},
		serialize:function (mixed_value) {
			var dashboard = this;
            var _utf8Size = function (str) {
                var size = 0,
                i = 0,
                l = str.length,
                code = '';
                for (i = 0; i < l; i++) {
                    code = str.charCodeAt(i);
                    if (code < 0x0080) {
                        size += 1;
                    } else if (code < 0x0800) {
                        size += 2;
                    } else {
                        size += 3;
                    }
                }
                return size;
            };
            var _getType = function (inp) {
                var type = typeof inp,
                match;
                var key;
                if (type === 'object' && !inp) {
                    return 'null';
                }
                if (type === "object") {
                    if (!inp.constructor) {
                        return 'object';
                    }
                    var cons = inp.constructor.toString();
                    match = cons.match(/(\w+)\(/);
                    if (match) {
                        cons = match[1].toLowerCase();
                    }
                    var types = ["boolean", "number", "string", "array"];
                    for (key in types) {
                        if (cons == types[key]) {
                            type = types[key];
                            break;
                        }
                    }
                }
                return type;
            };
            var type = _getType(mixed_value);
            var val, ktype = '';
            switch (type) {
                case "function":
                    val = "";
                    break;
                case "boolean":
                    val = "b:" + (mixed_value ? "1" : "0");
                    break;
                case "number":
                    val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
                    break;
                case "string":
                    val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
                    break;
                case "array":    case "object":
                    val = "a";
                    var count = 0;
                    var vals = "";
                    var okey;
                    var key;
                    for (key in mixed_value) {
                        if (mixed_value.hasOwnProperty(key)) {
                            ktype = _getType(mixed_value[key]);
                            if (ktype === "function") {
                                continue;
                            }

                            okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                            vals += dashboard.serialize(okey) + dashboard.serialize(mixed_value[key]);
                            count++;
                        }
                    }
                    val += ":" + count + ":{" + vals + "}";
                    break;
                case "undefined":
                // Fall-through
                default:
                    // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
                    val = "N";
                    break;
            }
            if (type !== "object" && type !== "array") {
                val += ";";
            }
            return val;
        },
		sendHistory: function(){
			var dashboard = this;
			var acti = dashboard.get_dashboard_acti();
			var dashboard_histories = dashboard.dashboard_histories;
			var data = dashboard_histories[acti];
			$.ajax({
				url: '/project_dashboards/update_dashboard',
				type: 'post',
				dataType: 'json',
				data:{
					data: data					
				},
				success: function(res){
					if( res.result == 'success'){
						

					}else{
						console.error(res);
					}
				}
			});
		},
		saveHistory: function(){
			var dashboard = this;
			var acti = dashboard.get_dashboard_acti();
			var dashboard_histories = dashboard.dashboard_histories;
			var name = '';
			if( !$.isEmptyObject(dashboard_histories[acti])){
				if( $.isEmptyObject(dashboard_histories[acti]['dashboard_data'])) dashboard_histories[acti]['dashboard_data'] = {};
				if( 'name' in dashboard_histories[acti]['dashboard_data']) name = dashboard_histories[acti]['dashboard_data']['name'];
			}
			if( !name) {
				var _count = 0;
				$.each(dashboard_histories, function(_k, _v){
					// if( _v['employee_id'] == dashboard.current_employee) 
						_count++;
				});
				name = dashboard.def_name + ' ' + (_count+1);
			}
			dashboard_histories[acti]['dashboard_data'] = {
				display: dashboard.get_widget_display(),
				order: dashboard.get_widget_order(),
				name: name,
				planed_forecast: dashboard.get_value_selected(),
			};
			dashboard.update_data_from_history(dashboard_histories[acti]['dashboard_data']);
			dashboard.dashboard_histories = dashboard_histories;
			dashboard.sendHistory();
		},
		update_data_from_history: function( acti_dashboard ){
			var dashboard = this;
			if( $.isEmptyObject(acti_dashboard)) acti_dashboard = {}
			var order = ('order' in acti_dashboard) ? acti_dashboard['order'] : {
					'upper': {},
					'lower': {}
				},
				display = ('display' in acti_dashboard) ? acti_dashboard['display'] : {},
				all_widget = {},
				wg_display = [];
			var company_widgets = [];
			$.each(dashboard.all_widget_default, function(f, ws){
				$.each(ws, function(k,v){
					company_widgets.push(k);
				});
			});
			// order
			dashboard.all_widget = {};
			if($.isEmptyObject(order)){
				dashboard.all_widget = dashboard.get('all_widget_default');
			}else{
				$.each(order, function(row, widgets){
					all_widget[row]= {};
					$.each(widgets, function(i, widget){
						if($.inArray(widget, company_widgets) != -1){
							all_widget[row][widget] = {};
							Object.assign(all_widget[row][widget] , dashboard.all_widget_default[row][widget]);
						}
					});
				});
				Object.assign(dashboard.all_widget, all_widget);
			}
			
			//display
			$.each( display, function(i,v){
				if($.inArray(v, company_widgets) != -1){
					wg_display.push(v);
				}
			});
			// if( !$.isEmptyObject(display)) 
			dashboard.enable_widget = wg_display;
		},
		show_share_resources: function(is_display){
			var dashboard = this;
			var popup = dashboard.share_popup;
			var item_share_resource = popup.find('.select-share_resource');
			if( (typeof is_display !='undefined') && (is_display == true)){
				item_share_resource.show();
				var dashboard_histories = dashboard.dashboard_histories;
				var selected = popup.find('#share_dashboard_id').val();
				var _dash = {};
				if( selected in dashboard_histories) _dash = dashboard_histories[selected];
				multiselect_setval(popup.find('.multiselect-share_resource'), _dash.share_resource);
			}else{
				item_share_resource.hide();
			}
		},
		update_share_dashboard: function(){
			var dashboard = this;
			if( !dashboard.has_change) return;
			var popup = dashboard.share_popup;
			var _form = popup.find('form:first');
			var _dash_id = $('#share_dashboard_id').val();
			var _share_type = _form.find('.radio_share_type :radio').filter(':checked').val();
			dashboard.dashboard_histories[_dash_id]['share_type'] = _share_type;

			var item_share_resource = popup.find('.select-share_resource').find(':checked'); 
			var _checked = [];
			if( item_share_resource.length){
				$.each( item_share_resource, function(i, _t){
					_checked.push( $(_t).val() );
				});
			}
			dashboard.dashboard_histories[_dash_id]['share_resource'] = _checked;
			var data = new FormData(_form[0]);
			$.ajax({
				url: '/project_dashboards/update_dashboard',
				dataType: 'json',
				data: data,
				contentType: false,
				processData: false,
				type: 'POST',
				success: function(res){
					if( res.result == 'success'){
						var data = res.data;
						var _dash_id = data.id;
						dashboard.dashboard_histories[_dash_id] = data;
						var multi_display_container = $(dashboard.multi_dashboard_container).find('.dash_multi_dashboard_select');
						var li = multi_display_container.find('li.dashboard-item-' + _dash_id);
						if(data.share_type == 'nobody'){
							li.find('.button_share_nobody').removeClass('wd-hide');
							li.find('.button_share_resource').addClass('wd-hide');
						}else{
							li.find('.button_share_nobody').addClass('wd-hide');
							li.find('.button_share_resource').removeClass('wd-hide');
						}

					}else{
						console.error(res);
					}
				}
			});
		},
	};
	window.ProjectsDashboard = ProjectsDashboard;
	
})(jQuery);