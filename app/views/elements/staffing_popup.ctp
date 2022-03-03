<?php 
echo $this->Html->css(array(
	'add_popup'
 ));
 echo $html->script(array(
        'jquery.multiSelect',
		));
 // Function này chỉ phù hợp với vision staffing popup. Cẩn thận khi dùng cho các phần khác
function vs_multiSelect($_this, $args){
	$fieldName = !empty( $args['fieldName']) ? $args['fieldName'] : 'multiselect';
	$fielData = !empty( $args['fielData']) ? $args['fielData'] : array();
	$textHolder = !empty( $args['textHolder']) ? $args['textHolder'] : __('Select');
	$pc = !empty( $args['pc']) ? $args['pc'] : array();
	$id = !empty( $args['id']) ? $args['id'] : 'multiselect-'.$fieldName;
	$cotentField = '';
	$cotentField = '<div class="wd-multiselect multiselect" id="'.$id.'" >
	<a href="javascript:void(0);" class="wd-combobox wd-project-manager"><p style="position: absolute; color: #c6cccf">'. $textHolder .'</p></a>
	<div class="wd-combobox-content '. $fieldName .'" style="display: none;">
	<div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="'. __('Search', true) .'" rel="no-history"></span></div><div class="option-content">';
	$i = 1;
	foreach($fielData as $idPm => $namePm):
		$avatar = '<img src="' . $_this->UserFile->avatar($idPm) . '" alt="'. $namePm .'"/>';
		$cotentField .= '<div class="wd-multisel-item wd-data-manager wd-group-' . $idPm . '">
			<p class="wd-data">' .
				$_this->Form->input($fieldName, array(
					'label' => false,
					'div' => false,
					'type' => 'checkbox',
					'name' => $fieldName .'[]',
					'id' => $fieldName .$i,
					'value' => $idPm)) .'
				<span class="option-name" style="padding-left: 5px;">' . $namePm . '</span>
			</p>
		</div>';
		$i++;
	endforeach;
	if(!empty($pc)): 
		foreach($pc as $idPc => $namePc):
			$cotentField .= '<div class="wd-multisel-item wd-data-manager wd-group-' . $idPc . '">
				<p class="wd-data">'.
					$_this->Form->input($fieldName, array(
						'label' => false,
						'div' => false,
						'type' => 'checkbox',
						'name' => $fieldName .'[]',
						'value' => $idPc,
						'id' => $fieldName .$i,
					)) .'
					<span class="option-name" style="padding-left: 5px;">' . $namePc . '</span>
				</p>
			</div>';
			$i++;
		endforeach; 
	endif;
	$cotentField .= '</div></div></div>';
	return $cotentField;
}
$milestoneCheckStaffing = false;
if(!empty($companyConfigs['milestone_check_staffing'])){
	$milestoneCheckStaffing = $companyConfigs['milestone_check_staffing'];
}
$milestoneCheckStaffing = $milestoneCheckStaffing ? ( __('Last check', true).' '.date('d/m/Y H:i', $milestoneCheckStaffing) ) : __('Check Staffing', true);
?>
	<div id="dialog_activity_vision_staffing_news" class="wd-full-popup" style="display: none;">
		<div class="wd-popup-inner">
			<div class="template-popup loading-mark loading wd-popup-container">
				<div class="wd-popup-head clearfix">
					<h4 style="width:calc( 100% - 140px);" class="active"><?php __('Vision Staffing');?></h4>
					<a style="right: 70px;height: 70px;position: absolute;width: 70px;padding: 25px;" target="_blank" href="<?php echo $this->Html->url('/guides/staffing/le_staffing_et_plan_de_charge_global.htm') ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
						<defs>
						<style>
							.cls-1 {
								fill: #666;
								fill-rule: evenodd;
							}
						</style>
						</defs>
						<path id="help" class="cls-1" d="M1960,40a10,10,0,1,1,10-10A10,10,0,0,1,1960,40Zm0-18.667A8.667,8.667,0,1,0,1968.66,30,8.667,8.667,0,0,0,1960,21.333Zm2.04,8.192q-0.15.146-.39,0.374c-0.16.152-.3,0.284-0.41,0.4a2.539,2.539,0,0,0-.27.286,1.379,1.379,0,0,0-.27.909v0.66h-1.66V31.255a2.13,2.13,0,0,1,.14-0.873,3.544,3.544,0,0,1,.61-0.755l1.07-1.07a1.272,1.272,0,0,0,.34-0.909,1.255,1.255,0,0,0-.35-0.9,1.231,1.231,0,0,0-.91-0.359,1.325,1.325,0,0,0-.93.344,1.347,1.347,0,0,0-.43.917h-1.78a3.024,3.024,0,0,1,1.02-2.046,3.251,3.251,0,0,1,2.18-.741,3.1,3.1,0,0,1,2.12.711,2.488,2.488,0,0,1,.83,1.988,2.246,2.246,0,0,1-.49,1.467C1962.28,29.26,1962.13,29.427,1962.04,29.525Zm-2.15,3.71a1.14,1.14,0,0,1,.8.315,1.027,1.027,0,0,1,.34.763,1.048,1.048,0,0,1-.34.77,1.084,1.084,0,0,1-.79.323,1.136,1.136,0,0,1-.8-0.316,1.015,1.015,0,0,1-.33-0.762,1.04,1.04,0,0,1,.33-0.77A1.07,1.07,0,0,1,1959.89,33.235Z" transform="translate(-1950 -20)"/>
						</svg>
					</a>
					<a href="javascript:void(0)" class="wd-close-popup" onclick="cancel_popup(this);"><img title="close" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
				</div>
				<div class="template-popup-content wd-popup-content">
					<?php echo $this->Form->create('ActivityVStaffing', array(
						'type' => 'GET', 
						'id' => 'form_activity_vision_staffing_news', 
						'target' => '_staffing', 
						'class' => 'form-style-2019', 
						'url' => array(
							'controller' => 'activity_tasks_preview',
							'action' => 'visions_staffing'
						),					
					)); 
					?>
					<p style="color: red" class="alert-message"></p>
					<div class="form-group status-inline" id="newvs_showby">
						<p class="status"><?php __('Show staffing by'); ?></p>
						<div class="radio-group inline-div">
							<?php 
							$dataType=2;
							$staffingBy = array();
							if( !empty($companyConfigs['show_switch_activity_staffing'])){
								$staffingBy[0] = __("Activity", true);
							}
							$staffingBy[2] = __("Project", true);
							$staffingBy[1] = __("Profit center", true);
							
							foreach($staffingBy as $type => $label){
								echo '<div class="input wd-input wd-radio-button">';
								echo $this->Form->radio('type', array(
									$type => $label
								), array(
									'name' => 'type',
									'fieldset' => false,
									'legend' => false,
									'rel' => 'no-history',
									'value' => $dataType
								));
								echo '</div>';
								
							}
							?>
						</div>
						<?php echo $this->Form->input('summary', array(
								'type'=> 'hidden',
								'value' => 1,
							));
							echo $this->Form->input('show_na', array(
								'type'=> 'hidden',
								'value' => 1,
							));
						?>
					</div>
					<div class="form-group" id="select-month-year">
						<?php 
							echo $this->Form->input('aDateType', array(
								'type'=> 'hidden',
								'value' => 3, // show by month
							));
						?>
						<div class="status-inline select-start">
							<p class="status"><?php __('From'); ?></p>
							<div class="inline-div">
							<div class="wd-row space-10">
								<div class="wd-col wd-col-md-6">
									<?php 
									$_start = !empty($_start) ? $_start : time();
									if(isset($arrGetUrl['aStartMonth'])) $smonth=$arrGetUrl['aStartMonth'];
									else $smonth= date('m', $_start);
									if(isset($arrGetUrl['aStartYear'])) $syear=$arrGetUrl['aStartYear'];
									else $syear= date('Y', $_start);
									echo $this->Form->month('smonth', $smonth, array(
										'empty' => false,
										'rel' => 'no-history',
										'id' => 'ActivityVStaffingStartMonth',
										'name' => 'aStartMonth',
									));?>
								</div>
								<div class="wd-col wd-col-md-6">
									<?php 
									echo $this->Form->year('syear', date('Y', $_start) - 10, date('Y', $_start) + 10, $syear, array(
										'empty' => false,
										'rel' => 'no-history',
										'id' => 'ActivityVStaffingStartYear',
										'name' => 'aStartYear',
									));
									?>
								</div>
							</div>
							</div>
						</div>
						<div class="select-end status-inline">
							<p class="status"><?php __('To'); ?></p>
							<div class="inline-div">
							<div class="wd-row space-10">
								<div class="wd-col wd-col-md-6">
									<?php 
									$_end = !empty($_start) ? $_start : time();
									if(isset($arrGetUrl['aEndMonth'])) $emonth=$arrGetUrl['aEndMonth'];
									else $emonth= date('m', $_end);
									if(isset($arrGetUrl['aEndYear'])) $eyear=$arrGetUrl['aEndYear'];
									else $eyear=  date('Y', $_end);
									echo $this->Form->month('emonth', $emonth, array(
										'empty' => false,
										'rel' => 'no-history',
										'id' => 'ActivityVStaffingEndMonth',
										'name' => 'aEndMonth',
									));?>
								</div>
								<div class="wd-col wd-col-md-6">
								<?php 
									echo $this->Form->year('eyear', date('Y', $_end) - 10, date('Y', $_end) + 10, $eyear, array(
										'empty' => false,
										'rel' => 'no-history',
										'id' => 'ActivityVStaffingEndYear',
										'name' => 'aEndYear',
									));
									
								?>
								</div>
							</div>
							</div>
						</div>
					</div>
					<div class="form-group" id="select-resource">
						<?php echo $this->Form->hidden('selectPCAll', array('value' => 'true')); ?>
						<div class="wd-input wd-area wd-none jq-multiselect-custom" id="filter_vs_profitCenter">
							<?php 	
							$menuListProfitCenters = isset($menuListProfitCenters) ? $menuListProfitCenters : array();
							?>
							<p class="status"><?php __("Profit Center"); ?></p>
							<select name="aPC[]" id="aProfitCenter" multiple="multiple" class="">
								<?php foreach( $menuListProfitCenters as $_id => $_pc){?>
									<option value="<?php echo $_id;?>"><?php echo $_pc;?></option>
								<?php } ?> 
							</select>
						</div>
						<div class="wd-input wd-area wd-none" id="filter_vs_employee">
							<?php 	
							$menuListEmployees = isset($employeeRorProfitCenterList) ? $employeeRorProfitCenterList : array();
							echo vs_multiSelect($this, array(
								'fieldName' => 'aEmployee',
								'id' => 'ActivityVStaffingEmployee',
								'fielData' => $menuListEmployees ,
								'textHolder' => __("Employee", true),
							));
							?>
						</div>
					</div>
					<div class="wd-row wd-submit-row">
						<div class="wd-col-xs-12">
							<div class="wd-submit">
								<button type="submit" class="btn-form-action btn-ok btn-right" id="vs_btnSave">
									<span><?php __('OK') ?></span>
								</button>
								<a class="btn-form-action btn-right btn-reset icon-btn" id="vs_reset_button" href="javascript:void(0);" onclick="">
									<span><i class="icon-refresh"></i></span>
								</a>
								<a class="btn-form-action btn-cancel" id="vs_cancel_button" href="javascript:void(0);" onclick="cancel_popup(this);">
									<span><?php echo __("Cancel", true); ?></span>
								</a>
							</div>
						</div>
					</div>
					<?php echo $this->Form->end();?>
				</div>
			</div>
		</div>
	</div>
		
<style>
	#dialog_activity_vision_staffing_news .status{
		margin-bottom: 10px;
	}
	#dialog_activity_vision_staffing_news .form-group,
	#dialog_activity_vision_staffing_news .form-group .status-inline{
		margin-bottom: 20px;
	}
	#dialog_activity_vision_staffing_news .status-inline >.inline-div{
		display: inline-block;
		margin-right: 0;
		width: calc( 100% - 130px);
		vertical-align: top;
	}
	#dialog_activity_vision_staffing_news .status-inline >.status{
		width: 110px;
		display: inline-block;
		margin-right: 16px;
		vertical-align: top;
	}
	#dialog_activity_vision_staffing_news .status-inline >:last-child{
		margin-right: 0px;
	}
	#select-month-year .status{
		line-height: 50px;
	}
	span#ui-dialog-title-wdConfimDialog {
		color: #666;
		font-weight: 600;
		font-size: 14px;
		line-height: 40px;
		height: 40px;
		text-overflow: ellipsis;
		overflow: hidden;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		display: -webkit-box;
		max-width: 480px;
	}
	p.alert-message.text-center.form-message.request-message {
		padding: 0;
		line-height: 28px;
	}
	span.ui-button-text {
		color: #fff;
	}
</style>
<script>
//1
// var vs_NewFilter = new $.z0.data({});
$(function () {
	var vs_NewFilter;
	var vs_selectPC_timeout = 0;
	var vs_loading = 1;
	$('#aProfitCenter').multiSelect({
		selectAll: false,
		name: 'aPC',
		noneSelected: '<?php __('Profit Center');?>',
		oneOrMoreSelected: '*',
		loadingClass : 'loading',
		loadingText : 'Loading...',
		// appendTo: 'body'
	}, function(input_element){
		var _multiSelect = $(this);
		check_pcAll();
		if( _multiSelect.hasClass('disabled') || _multiSelect.hasClass('waiting')){
			clearTimeout(vs_selectPC_timeout);
			return;
		}
		clearTimeout(vs_selectPC_timeout);
		vs_selectPC_timeout = setTimeout(function () {
			vs_updateResourceByPC();
		}, 2000);
	});
	$('#dialog_activity_vision_staffing_news form').submit(function (evt) {
		var _multiSelect = $(this).find('#aProfitCenter');
		if( _multiSelect.hasClass('disabled') || _multiSelect.hasClass('waiting')){
			evt.preventDefault();
		}
	});
	var pc_MultiSel = $('#aProfitCenter');
	var resourceMultiSel = $('#ActivityVStaffingEmployee');
	function init_vs_multiselect(elm){
		var listMultiSelect;
		if( $(elm).hasClass('wd-multiselect') ){
			listMultiSelect = $(elm);
		}else{
			return console.error('Error');
		}
		listMultiSelect.find('.wd-data').unbind('click');
		listMultiSelect.find('.wd-combobox .circle-name span').unbind('click');
		listMultiSelect.each(function(){
			// Khai báo biến
			var _mulsel = $(this);
			var wddata = $(this).find('.wd-data');
			var _form = $(this).closest('.form-style-2019');
			var _this_id = $(this).prop('id');
			var area_append = $(this).closest('.multiselect').find('.wd-combobox');
			var data_value = [];
			if( _mulsel.closest('.wd-input').hasClass('required') &&  (_mulsel.find('.multiselect_required').length == 0) ){
				$('<input type="text" value="" class="multiselect_required" required="required"/>').insertAfter(area_append);
			}
			var multiselect_required = _mulsel.find('.multiselect_required:first');
			
			// empty data
			_mulsel.removeClass('has-val');
			area_append.find('a.circle-name').remove();
			_mulsel.find('.wd-combobox-content :checkbox').prop('checked', false);
			_mulsel.find('.wd-data.checked').removeClass('checked');
			multiselect_required.val('');
			area_append.unbind('click').on('click', function(e){
				e.preventDefault();
				if( !(_mulsel.hasClass('disabled') || _mulsel.hasClass('waiting'))){
					$(this).closest('.wd-multiselect').find('.wd-combobox-content').toggle();
				}
			});
			if( _this_id.length){
				var _function = _this_id.replace(/-/g, '_') + 'onChange';
				if( typeof window[_function] == 'function'){
					window[_function](_this_id);
				}
			}
			
			var wd_mul_checked = function(_mulsel){
				var checkboxs = _mulsel.find('input[type="checkbox"]');
				$.each(checkboxs, function( ind, checkbox){
					var check_box = $(checkbox);
					if(check_box.is(':checked')){
						check_box.closest('.wd-data').addClass('checked');
					}else{
						check_box.closest('.wd-data').removeClass('checked');
					}
				});				
			}
			wddata.on('click',function(e){
				// e.preventDefault();
				var checkbox = $(this).find('input[type="checkbox"]');
				var checked = checkbox.is(":checked");
				checkbox.prop("checked",!checked);
				var _datas = checkbox.val();
				if(checkbox.is(':checked')){
					var employee_id = checkbox.val();
					var title = $(this).find('.option-name').html();
					data_value.push(_datas);
					_mulsel.addClass('has-val');
					multiselect_required.val('1');
					area_append.append('<a class="circle-name" data-id = "'+ employee_id +'" title="' + title + '"><img width="35" height="35" src="'+ js_avatar(employee_id) +'" title="'+ title +'"></a>');
				}else{
					data_value = jQuery.removeFromArray(_datas, data_value);
					area_append.find('a[data-id = "'+ _datas + '"]').remove();
					if(!(area_append.find('.circle-name').length)){  
						_mulsel.removeClass('has-val'); multiselect_required.val('');
					}
				}
				wd_mul_checked(_mulsel);
				if( _this_id.length){
					var _function = _this_id.replace(/-/g, '_') + 'onChange';
					// console.log( _function, typeof eval(_function));
					if( typeof eval(_function) == 'function'){
						eval(_function)(_this_id);
					}
				}
			});
			wddata.find(':checkbox').on('change',function(e){
				var checkbox = $(this);
				var _row = checkbox.closest('.wd-data');
				var _datas = checkbox.val();
				if(checkbox.is(':checked')){
					var circle_name = _row.find('.circle-name').html();
					var title = _row.find('.circle-name').attr('title');
					data_value.push(_datas);
					_mulsel.addClass('has-val');
					multiselect_required.val('1');
					area_append.append('<a class="circle-name" title="' + title + '">' + circle_name + '</a>');
				}else{
					data_value = jQuery.removeFromArray(_datas, data_value);
					area_append.find('span[data-id = "'+ _datas + '"]').closest('.circle-name').remove();
					if(!(area_append.find('.circle-name').length)){  
						_mulsel.removeClass('has-val'); multiselect_required.val('');
					}
				}
				wd_mul_checked(_mulsel);
				if( _this_id.length){
					var _function = _this_id.replace(/-/g, '_') + 'onChange';
					// console.log( _function, typeof eval(_function));
					if( typeof eval(_function) == 'function'){
						eval(_function)(_this_id);
					}
				}
			});
			area_append.on('click', '.circle-name span', function(e){
				e.stopPropagation();
				var eid = $(this).data('id');
				$(this).closest('.circle-name').remove();
				wddata.find('input[type="checkbox"][value = "'+ eid +'"]').prop("checked", false);
				data_value = jQuery.removeFromArray(eid, data_value);
				if(!(area_append.find('.circle-name').length)) {
					_mulsel.removeClass('has-val'); 
					multiselect_required.val('');
				}
				wd_mul_checked(_mulsel);
				if( _this_id.length){
					var _function = _this_id.replace(/-/g, '_') + 'onChange';
					// console.log( _function, typeof eval(_function));
					if( typeof eval(_function) == 'function'){
						eval(_function)(_this_id);
					}
				}
			});
			
			multiselect_required.on('keydown', function(){ $(this).val('')});
			multiselect_required.on('focusout', function(){ $(this).val('')});
			var timeoutID;
			$('.wd-input-search').keyup(function(e){
				var _this = $(this);
				clearTimeout(timeoutID);
				timeoutID = setTimeout(function(){
					var val = $.trim(_this.val()).toLowerCase();
					_this.closest('.wd-combobox-content').find('.wd-data').each(function(){
						var label = $(this).find('.option-name').html().toLowerCase();
						if(!val.length || label.indexOf(val) != -1 || !val){
							$(this).closest('.wd-data-manager').css('display', 'block');
						} else{
							$(this).closest('.wd-data-manager').css('display', 'none');
						}
					});
				} , 200);
			});
		});
	}
	function check_pcAll(){
		// uncheck all / check all
		// console.log( pc_MultiSel);
		if( pc_MultiSel.multiSelectIsSelectAll() || pc_MultiSel.multiSelectIsSelectNone()){
			$('#ActivityVStaffingSelectPCAll').val(true);
		}else{
			$('#ActivityVStaffingSelectPCAll').val(false);
		}
	}
	function check_showShowby(){
		if(vs_loading) return false;
		// console.log( 'check show');
		var checkShowby = $('#newvs_showby').find('input:checked');
		var pc_MultiSel = $('#aProfitCenter');
		if( checkShowby.length){
			var _showby = checkShowby.val();
			if (_showby == 5) { //Profile
				hide_vs_multisel($('#ActivityVStaffingEmployee'));
				pc_MultiSel.multiSelectDisable();
				$('#ActivityVStaffingSelectPCAll').val(true);
			} else { //Profit center ||Activity
				pc_MultiSel.multiSelectEnable();
				if( _showby == 0 || _showby == 2){ //Activity
					show_vs_multisel($('#ActivityVStaffingEmployee'));
				}
				if( _showby == 1){
					hide_vs_multisel($('#ActivityVStaffingEmployee'));
				}
				var pc_MultiSel = $(pc_MultiSel);
				
				// check_pcAll();
			}
		}
	}
	function enable_vs_multisel(multiSelect_element) {
		multiSelect_element.removeClass('waiting');
	}
	// Disable event
	function disable_vs_multisel (multiSelect_element) {
		multiSelect_element.addClass('waiting');
	}
	// hide and disable all option
	function hide_vs_multisel (multiSelect_element) {
		multiSelect_element.addClass('disabled').closest('.wd-input').hide();
		multiSelect_element.find('input').each( function(){
			$(this).prop('disabled', true);
		});
	}
	function show_vs_multisel (multiSelect_element) {
		multiSelect_element.removeClass('disabled').closest('.wd-input').show();
		multiSelect_element.find('input').each( function(){
			$(this).prop('disabled', false);
		});
	}
	
	function vs_get_list_assign(multiSelect_element, return_data){
		var _list_selected = multiSelect_element.find(':checkbox:checked');
		var employee_selected = []; 
		if( _list_selected.length){
			$.each( _list_selected, function(ind, emp){
				if( return_data == 'list_id'){
					employee_selected.push($(emp).val());
				}else{
					var key = $(emp).val();
					var val = $(emp).next('.option-name').text();
					employee_selected.push({id: key, name: val});
				}
			});
		}
		return employee_selected;
	}
	function vs_set_assigned($elm, datas) {
		disable_vs_multisel($elm);
        $elm.find('.wd-combobox >.circle-name').remove();
        $elm.find('.wd-data input[type="checkbox"]').prop('checked', false).trigger('change');
        $elm.find('.wd-input-search').val('').trigger('change');
        $.each(datas, function (ind, data) {
			$elm.find('input[value="' + data + '"]').prop('checked', true).trigger('change');      
        });
		enable_vs_multisel($elm);
		
    }
	function vs_updateResourceByPC(){
		var pc_MultiSel = $('#aProfitCenter');
		if( pc_MultiSel.hasClass('disabled') || pc_MultiSel.hasClass('waiting')) return;
		var resourceMultiSel = $('#ActivityVStaffingEmployee');
		list = pc_MultiSel.multiSelectGetValues();
		$.ajax({
            url: '<?php echo $html->url('/activities/get_employee_by_pc/') ?>',
            cache: true,
			type: 'GET',
            data: {
                data: list,
            },
            dataType: 'json',
            beforeSend: function () {
                resourceMultiSel.addClass('loading');
                pc_MultiSel.addClass('loading');
				disable_vs_multisel(resourceMultiSel);
				pc_MultiSel.addClass('waiting');
            },
            success: function (data) {
                if ($.isArray(data.pc)) {
                    data.pc = $.mergeArrayUnique(list, data.pc);
                } else {
                    var pc = [];
                    $.each(data.pc, function (i, v) {
                        pc.push(v);
                    });
                    data.pc = $.mergeArrayUnique(list, pc);
                }
				pc_MultiSel.multiSelectSetValues(data.pc);			
				// draw resourceMultiSel
				var _html = '';
				var data_list = $.sortObjectByValue(data.list);
				$.each(data_list, function(i, v){
					var e_id = v[0];
					var e_name = v[1];
					_html += '<div class="wd-multisel-item wd-data-manager wd-group-' + e_id + '">';
					_html += '<p class="wd-data">';
					// _html += '<a class="circle-name" title="' + e_name + '"><span data-id="' + e_id + '">';
					// _html += '<img width = 35 height = 35 src="'+  js_avatar(e_id ) +'" title = "'+ e_name +'" />';
					// _html += '</span></a>';
					_html += '<input type="checkbox" name="aEmployee[]" value="' + e_id + '" id="ActivityVStaffingEmployee-'+ e_id +'">';
					_html += '<span class="option-name" style="padding-left: 5px;">' + e_name + '</span>';
					_html += '</p> </div>';
				});
				if( !_html) _html = '<div class="wd-multisel-item ><p style="color: #c6cccf">' + '<?php __('No resources found');?>' + '</p></div>';
				resourceMultiSel.find('.option-content').html(_html);
				init_vs_multiselect(resourceMultiSel);
				// END draw resourceMultiSel
				
            },
            complete: function () {
                resourceMultiSel.removeClass('loading');
                pc_MultiSel.removeClass('loading');
                enable_vs_multisel(resourceMultiSel);
				pc_MultiSel.removeClass('waiting');
            }
        });
	}
	function set_data_vs_popup(data){
		var viewBy = data.get('view_by', 0);
		$(':input[name="type"][value="' + viewBy + '"]').prop('checked', true);
		// date
		var today = new Date(),
			month = today.getMonth() + 1,
			sm = data.get('smonth', month < 10 ? '0' + month : month),
			sy = data.get('syear', today.getFullYear()),
			em = data.get('emonth', month < 10 ? '0' + month : month),
			ey = data.get('eyear', today.getFullYear());
		$('#ActivityVStaffingStartMonth').val(sm);
		$('#ActivityVStaffingStartYear').val(sy);
		$('#ActivityVStaffingEndMonth').val(em);
		$('#ActivityVStaffingEndYear').val(ey);
		// PC multi-select
		var pc = data.get('pc', []);
		var pc_multi_sel = $('#aProfitCenter');
		pc_multi_sel.multiSelectSetValues(pc);
		vs_updateResourceByPC();	
		// Display/hide element 
		vs_loading = 0;
		check_showShowby();
	}
	function vs_loadHistory(){
		$.z0.History.load('vs_NewFilter', function (data) {
			vs_NewFilter = data;
			// Show Staffing by
			set_data_vs_popup(data);
			$('#dialog_activity_vision_staffing_news').find('.loading-mark').removeClass('loading');
			vs_loading = 0;
		});
	}
	/* END Functions */
	
	/* INIT */
	$('#form_activity_vision_staffing_news .wd-multiselect').addClass('waiting');
	init_vs_multiselect('#form_activity_vision_staffing_news .wd-multiselect');
	
	
	/* EVENT*/
	$('#newvs_showby').find(':radio').on('change', function(){
		check_showShowby();
	});
	function ActivityVStaffingEmployeeonChange(element_id){
		return 1;
	}
	function aProfitCenteronChange(element_id){
		if( $('#' + element_id).hasClass('disabled') || $('#' + element_id).hasClass('waiting')){
			clearTimeout(vs_selectPC_timeout);
			return;
		}
		clearTimeout(vs_selectPC_timeout);
		vs_selectPC_timeout = setTimeout(function () {
			vs_updateResourceByPC();
		}, 1000);
	}
	milestoneCheckStaffing =  <?php echo json_encode($milestoneCheckStaffing) ?>;
	$(window).ready(function(){
		$("#activity_vision_staffing_news").on('click', function () {
			vs_loading = 1;
			if (companyConfigs['run_staffing_before_display_staffing'] == 1) {
				wdConfirmIt({
					title: milestoneCheckStaffing,
					content: <?php echo json_encode( __('Display vision staffing', true));?>,
					width: ( Azuree && Azuree.language && Azuree.language == 'fr') ? 480 : 400,
					buttonModel: 'WD_TWO_BUTTON',
					buttonText: [
						'<?php __('With check');?>',
						'<?php __('Without check');?>'
					],
				},function(){
					$('#dialog_activity_vision_staffing_news').find('.loading-mark').addClass('loading');
					show_full_popup('#dialog_activity_vision_staffing_news',{width: 580});
					checkStaffings = function (value) {
						$.ajax({
							url: '<?php echo $html->url(array('controller' => 'projects', 'action' => 'checkStaffings')); ?>',
							async: true,
							type: 'POST',
							dataType: 'json',
							data: {
								first: value
							},
							beforeSend: function () {
								$('#loading_w').show();
							},
							success: function (data) {
								progress = data['progress'];
								if (data['done'] == false) {
									setValueProgress1(progress);
									checkStaffings(2);
								} else {
									clearInterval(auto);
									$('#loading_w').hide();
									vs_loadHistory();
								}
							}
						});
					}
					setStatusSystem1();
					checkStaffings(1);
				},function(){
					$('#dialog_activity_vision_staffing_news').find('.loading-mark').addClass('loading');
					show_full_popup('#dialog_activity_vision_staffing_news',{width: 580});
					vs_loadHistory();
				});
            } else {
				$('#dialog_activity_vision_staffing_news').find('.loading-mark').addClass('loading');
				show_full_popup('#dialog_activity_vision_staffing_news',{width: 580});
                vs_loadHistory();
            }
		});
		$('#form_activity_vision_staffing_news').on('submit', function(){
			var _form = $('#form_activity_vision_staffing_news');
			//get form data
			var data = _form.serializeArray().reduce(function(obj, item) {
				if(item.name.indexOf('[]') != '-1'){
					var key = item.name;
					key = key.replace('[]','');
					if( !obj[key]) obj[key] = [];
					if( item.value != '0') obj[key].push(item.value);
				}else{
					obj[item.name] = item.value;
				}
				return obj;

			}, {});
			var _showby = 'type' in data ? data.type : 0;
			vs_NewFilter.set('view_by', _showby);
			var smonth = 'aStartMonth' in data ? data.aStartMonth : '';
			vs_NewFilter.set('smonth', smonth);
			var syear = 'aStartYear' in data ? data.aStartYear : '';
			vs_NewFilter.set('syear', syear);
			var emonth = 'aEndMonth' in data ? data.aEndMonth : '';
			vs_NewFilter.set('emonth', emonth);
			var eyear = 'aEndYear' in data ? data.aEndYear : '';
			vs_NewFilter.set('eyear', eyear);
			var aPC = 'aPC' in data ? data.aPC : '';
			vs_NewFilter.set('pc', aPC);
			var aEmployee = 'aEmployee' in data ? data.aEmployee : '';
			vs_NewFilter.set('emps', aEmployee);
			$.z0.History.save('vs_NewFilter', vs_NewFilter);
		});
		$("#vs_reset_button").on('click', function () {
			vs_loading = 1;
			set_data_vs_popup(new $.z0.data({}));
		});
	});
});
</script>