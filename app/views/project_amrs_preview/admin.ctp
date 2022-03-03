<?php 	echo $html->script('jquery.validation.min'); ?>
<?php 	echo $html->script('jquery.dataTables'); ?>
<?php 	echo $html->css('jquery.dataTables');
		echo $html->css('layout_2019');
		echo $html->css('preview/project_amr');
		echo $html->css('preview/dashboard-setting');
?>
<?php 	echo $html->css('jquery.ui.custom'); 
		echo $this->Html->script(array('gridster/jquery.gridster.min'));
		echo $this->Html->css(array('gridster/jquery.gridster.min'));

?>
<style>
	.wd-layout-setting{
		display: block;
		position: inherit;
		margin-left: 0;
	}
	.wd-list-project .wd-tab .wd-content label{
		font-weight: 400;
	}
	.wd-tab .wd-aside-left{
		height: inherit;
	}
	.wd-layout .wd-main-content .wd-tab {
		margin-left: auto;
		margin-right: auto;
		max-width: 1920px;
	}
	
	.select .dropdown .wd-custom-checkbox.wd-custom-checkbox-2 label span.status-name{
		 width: 100px;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
								<div class="wd-layout-setting">
									
									<div class="wd-layout-title">
										<h4><?php echo __('Display settings', true); ?></h4>
										<p><?php echo __('Drag and drop to re-arrange the display', true); ?></p>
									</div>
									<div id ="layout-setting" class="layout-setting gridster">
										<ul>
											<?php
											 foreach ($list_widgets as $key => $value) {
												$class_status = ($value['display'] == 1) ? '' : 'disabled';
												$class_show = ($value['show'] == 1) ? '' : 'disabled';
											 ?>
											<li  data-widget= "<?php echo $value['widget']; ?>" data-row="<?php echo $value['row']; ?>" data-col="<?php echo $value['col']; ?>" data-sizex="<?php echo $value['sizex']; ?>" data-sizey="<?php echo $value['sizey']; ?>" class="<?php echo $class_show; ?>">
												<p class="layout-name"><?php echo __($value['name'], true); ?></p>
												<ul>
													<li class="df-show <?php echo $class_show; ?>">
														<a href="javascript:void(0);" id="st-show<?php echo $key;?>" data-show = '<?php echo $value['show']; ?>' title="<?php echo __('Display')?>"></a>
													</li>
													<li class="df-display <?php echo $class_status; ?>">
														<a href="javascript:void(0);" id="st-display<?php echo $key;?>" data-display = '<?php echo $value['display']; ?>' title="<?php echo __('Display')?>"></a>
													</li>
												</ul>
												<?php
													if($value['widget'] == 'project_task' && !empty($task_status)){
														echo '<div class="select"><label class="title">'. __('Status', true) .'</label><div class="dropdown noOrdered">';
														$options = !empty($value['task_status']) ? Set::combine( $value['task_status'], '{n}.status_id', '{n}') : array();
														foreach ($task_status as $status_id => $name) {
															$is_display = isset($options[$status_id]['status_display']) ? $options[$status_id]['status_display'] : 1;
															$is_default = isset($options[$status_id]['default']) ? $options[$status_id]['default'] : 0;
														?>
															<div class="wd-check-box wd-input wd-custom-checkbox wd-custom-checkbox-2">
																<label>
																	<input type="checkbox" data-status-id="<?php echo $status_id ?>" class="checkbox" data-status-display= '<?php echo $is_display; ?>' <?php if($is_display) echo 'checked="checked"';?> />
																	<span class="wd-checkbox"></span>
																	<span class="status-name"><?php echo $name ?></span>
																</label>
																<label>
																	<input type="radio" data-status-id="<?php echo $status_id ?>" class="checkbox-2" data-status-default="<?php echo $is_default; ?>" <?php if($is_default) echo 'checked="checked"';?> name="indicator_task_default_status" style="display:none;"/>
																	<span class="wd-checkbox-2"></span>
																</label>
															</div>
														<?php }
														echo '</div></div>';
													}
													if($value['widget'] == 'project_risk' && !empty($issueStatus)){
														echo '<div class="select"><label class="title">'. __('Status', true) .'</label><div class="dropdown noOrdered">';
														$options = !empty($value['options']) ? Set::combine( $value['options'], '{n}.id', '{n}.display') : array();
														foreach ($issueStatus as $status_id => $name) {
															$is_display = isset($options[$status_id]) ? $options[$status_id] : 1;
														?>
															<div class="wd-check-box wd-input wd-custom-checkbox wd-custom-checkbox">
																<label>
																	<input type="checkbox" data-status-id="<?php echo $status_id ?>" class="checkbox" data-status-display= '<?php echo $is_display; ?>' <?php if($is_display) echo 'checked="checked"';?> />
																	<span class="wd-checkbox"></span>
																	<span class="status-name"><?php echo $name ?></span>
																</label>
															</div>
														<?php }
													}
													if($value['widget'] == 'project_synthesis'){
														echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
														$options = array(
															'ProjectRisk' =>  __('Risks', true),
															'ProjectAmr' => __('Synthesis Comment', true),
															'ProjectIssue' => __('Issues', true),
															'Done' =>  __('Synthesis Done', true),
														);
														foreach ($value['options'] as $key => $opt) {
															$opt_display = isset($opt['model_display']) ? $opt['model_display'] : 1;
															$model_id = $opt['model'];
															$opt_name = $options[$model_id];
														?>
															<div class="wd-check-box wd-input wd-custom-checkbox">
																<label><input type="checkbox" data-model="<?php echo $model_id ?>" class="checkbox" data-model-display= '<?php echo $opt_display; ?>' <?php if($opt_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $opt_name ?></span></label>
															</div>
														<?php }
														echo '</div></div>';
													}
													if($value['widget'] == 'project_status'){
														echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
														$options = array(
															'Scope' =>  __d(sprintf($_domain, 'KPI'), "Scope", null),
															'Schedule' =>  __d(sprintf($_domain, 'KPI'), "Schedule", null),
															'Budget' =>  __d(sprintf($_domain, 'KPI'), "Budget", null),
															'Resources' =>  __d(sprintf($_domain, 'KPI'), "Resources", null),
															'Technical' =>  __d(sprintf($_domain, 'KPI'), "Technical", null),
														);
														foreach ($value['options'] as $key => $opt) {
															$opt_display = isset($opt['model_display']) ? $opt['model_display'] : 1;
															$model_id = $opt['model'];
															$opt_name = $options[$model_id];
														?>
															<div class="wd-check-box wd-input wd-custom-checkbox">
																<label><input type="checkbox" data-model="<?php echo $model_id ?>" class="checkbox" data-model-display= '<?php echo $opt_display; ?>' <?php if($opt_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $opt_name ?></span></label>
															</div>
														<?php }
														echo '</div></div>';
													}
													if($value['widget'] == 'project_synthesis_budget'){
														echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
														$options = array(
															'SynthesisBudget' =>  __('Synthesis Budget', true),
															'BudgetInternal' =>  __d(sprintf($_domain, 'Internal_Cost'), "Internal Cost", null),
															'BudgetExternal' =>  __d(sprintf($_domain, 'External_Cost'), "External Cost", null)
														);
														foreach ($value['options'] as $key => $opt) {
															$opt_display = isset($opt['model_display']) ? $opt['model_display'] : 1;
															$model_id = $opt['model'];
															$opt_name = $options[$model_id];
														?>
															<div class="wd-check-box wd-input wd-custom-checkbox">
																<label><input type="checkbox" data-model="<?php echo $model_id ?>" class="checkbox" data-model-display= '<?php echo $opt_display; ?>' <?php if($opt_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $opt_name ?></span></label>
															</div>
														<?php }
														echo '</div></div>';
													}
													if($value['widget'] == 'project_budget'){
														echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
														$options = array(
															'inv' =>  __d(sprintf($_domain, 'Finance'), "Budget Investment", null),
															'fon' =>  __d(sprintf($_domain, 'Finance'), "Budget Operation", null),
															'finaninv' =>  __d(sprintf($_domain, 'Finance'), "Finance Investment", null),
															'finanfon' =>  __d(sprintf($_domain, 'Finance'), "Finance Operation", null)
														);
														foreach ($value['options'] as $key => $opt) {
															$opt_display = isset($opt['model_display']) ? $opt['model_display'] : 1;
															$model_id = $opt['model'];
															$opt_name = $options[$model_id];
														?>
															<div class="wd-check-box wd-input wd-custom-checkbox">
																<label><input type="checkbox" data-model="<?php echo $model_id ?>" class="checkbox" data-model-display= '<?php echo $opt_display; ?>' <?php if($opt_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $opt_name ?></span></label>
															</div>
														<?php }
														echo '</div></div>';
													}

												?>
											</li>
											<?php } ?>
										</ul>
									</div>
									<div class="wd-submit">
										<button onclick="submitSetting(this);return false;" class="btn-form-action btn-ok" id="btnSave">
											<span><?php echo __('Save', true); ?></span>
										</button>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.df-display').on('click', function(){
		if( $(this).prev('.df-show').hasClass('disabled')) return;
        _display = $(this).find('a').data('display');
        if(_display == 0){
            _display = 1;
			$(this).removeClass('disabled');
        }else{
            _display = 0;
            $(this).addClass('disabled');
        }
        $(this).find('a').data('display',_display);
    });
	$('.df-show').on('click', function(){
		_show = $(this).find('a').data('show');
		if(_show == 0){
            _display = 1;
			$(this).removeClass('disabled');
			$(this).closest('li.gs-w').removeClass('disabled');
        }else{
            _display = 0;
            $(this).addClass('disabled');
            $(this).closest('li.gs-w').addClass('disabled');
			$(this).next().addClass('disabled');
			$(this).next().find('a').data('display',_display);
        }
        $(this).find('a').data('show',_display);
	});
	function submitSetting(_this){
		if($(_this).hasClass('loading')) return;
		$(_this).addClass('loading');
		var _wid_item = $('#layout-setting > ul > li');
		var _data = {};
		i = 0;
		_wid_item.each(function(){
            _row = $(this).data('row');
            _col = $(this).data('col');
            _sizex = $(this).data('sizex');
            _sizey = $(this).data('sizey');
            _widget = $(this).data('widget');
			_display = $(this).find('.df-display a').data('display');
			_show = $(this).find('.df-show a').data('show');
			// console.log(_show);
            // _data[i] = _widget +'|row_'+_row +'-col_'+_col+'-sizex_'+_sizex+'-sizey_'+_sizey+ '-show_'+_show+'|'+ _display;
			_data[i] = {
				widget: _widget,
				row: _row,
				col: _col,
				sizex: _sizex,
				sizey: _sizey,
				display: _display,
				show: _show
			};
            if( _widget == 'project_task'){
                var _checkbox = $(this).find('.wd-check-box');
				var count_default = 0;
				var first_enable = -1;
				var j = 0;
				_data[i]['task_status'] = {};
                _checkbox.each(function(){
                    status_id = $(this).find(':checkbox:first').data('status-id');
                    status_display = ( $(this).find(':checkbox:first').is(':checked') ? 1 : 0);
					if((first_enable == -1) && status_display) first_enable = j;
                    status_default = ( $(this).find('.checkbox-2').is(':checked') ? 1 : 0);
					count_default += status_default;
					_data[i]['task_status'][j] = {
						status_id: status_id,
						status_display: status_display,
						'default': status_default
					};
					j++;
                });
				if( !count_default && (first_enable != -1)) _data[i]['task_status'][first_enable]['default'] = 1;
            } else if( _widget == 'project_risk'){
				var _checkbox = $(this).find(':checkbox');
				if( _checkbox.length){
					var j = 0;
					_data[i]['options'] = {};
					_checkbox.each(function(){
						var _this = $(this);
						var id = _this.data('status-id');
						var is_display = ( _this.is(':checked') ? 1 : 0);
						_data[i]['options'][j++] = {
							id: id,
							display: is_display,
						}
					});
				}
			} else{
                var _checkbox = $(this).find('.wd-check-box');
				if( _checkbox.length){
					var j = 0;
					_data[i]['options'] = {};
					_checkbox.each(function(){
						var model = $(this).find('input').data('model');
						var model_display = ( $(this).find(':checkbox:first').is(':checked') ? 1 : 0);
						// opt_syn += model+'_'+model_display+'-';
						_data[i]['options'][j++] = {
							model: model,
							model_display: model_display,
						}
					});
					// _data[i] += opt_syn;
				}
            }
            i++;
        });
		// console.log(_data);
		// return;
		$.ajax({
			url: '/project_amrs_preview/save_dashboard_setting/',
			type : 'POST',
			data:  { data: _data},
			dataType: 'json',
			success: function(response) {
			},
			complete: function(){
				$(_this).removeClass('loading');
			}
	  
		});
	}
	var ly_setting;
    $( document ).ready(function() {
		ly_setting = $(".layout-setting > ul").gridster({
            namespace: '#layout-setting',
            widget_base_dimensions: [290, 140],
            widget_margins: [10, 10],
            cols : 2,
            max_cols: 2,
            resize: {
                enabled: true
            }
        }).data('gridster');
		$('#layout-setting .dropdown:not(.noOrdered)').sortable();
		$('#layout-setting .dropdown:not(.noOrdered)').bind('mousedown',function() {
			ly_setting.disable();
		});
		$('#layout-setting .dropdown:not(.noOrdered)').bind('mousemove',function() {
			ly_setting.enable();
		});
      
        $('.dropdown').find(':checkbox').on('change', function(){
            var _this = $(this);
			if (_this.is(':checked')) {
                _this.data('status-display', 1);
                _this.data('status-default', 1);
                _this.data('model-display', 1);
            }else{
                _this.data('status-display', 0);
                _this.data('status-default', 0);
                _this.data('model-display', 0);
            }
			var _default = _this.closest('.wd-check-box').find('.checkbox-2');
			if(_default.length && _this.hasClass('checkbox')){
				if(_this.is(':checked')){
					_default.prop('disabled', false);
				}else{
					_default.data('status-default', 0).prop('checked', false).prop('disabled', true);
				}
			}
        });
		
	});
</script>