<?php echo $html->script('jquery.validation.min'); 
echo $html->script(array(
	'slick_grid/lib/jquery-ui-1.8.16.custom.min',
	'slick_grid/lib/jquery.event.drag-2.0.min',
	'slick_grid/slick.core',
	'slick_grid/slick.dataview',
	'slick_grid/controls/slick.pager',
	'slick_grid/slick.formatters',
	'slick_grid/plugins/slick.cellrangedecorator',
	'slick_grid/plugins/slick.cellrangeselector',
	'slick_grid/plugins/slick.cellselectionmodel',
	'slick_grid/plugins/slick.rowselectionmodel',
	'slick_grid/plugins/slick.rowmovemanager',
	'slick_grid/slick.editors',
	'slick_grid/plugins/slick.dataexporter',
	'slick_grid_custom',
	'slick_grid/slick.grid.activity',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/lib/jquery.event.drop-2.0.min',
));

echo $html->css(array(
	'jquery.multiSelect',
	'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
	'preview/slickgrid',
	'preview/project-import'));
?>
<?php echo $html->css('jquery.ui.custom'); 
echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
}
$list_project = array($project_id);
?>	

<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
	.slick-row.selected{
		background: transparent;
	}
	.slick-viewport .slick-row .slick-cell.no-padding.active{
		border-left: 1px solid #E9E9E9;
	}
	#delete-task-switch{
		display: inline-block;
	}
	.wd-tab .wd-panel h2.wd-t3{
	    z-index: 3;
		display: block;
		position: relative;
	}
</style>
<div id="wd-container-main" class="wd-project-index">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-tab wd-list-project">
				<div class="wd-panel">
					<div id="flashMessage" class="message error" style="display: none;">
						<span></span>
						<a href="#" class="close">x</a>
					</div>
					<div class="wd-section" id="wd-fragment-1">
						<div class="wd-content">
							<h2 class="wd-t3"> 
								<a href="javascript:void(0);" style="display: none" class="btn export-excel-icon-all hide-on-mobile hidden-print" id="export-submit" title="<?php echo __('Export Excel')?>"><span><?php echo __('Export Excel') ?></span></a>
								<div id="delete-task-switch" class="hidden-print">
									<?php
									echo $this->Form->input('delete_task_exist', array(
										'type' => 'checkbox',
										'label' => false,
										'checked'=> false,
										'rel' => 'no-history',
										'id' => 'delete_task_exist',
										'div' => array(
											'class' => 'wd-input wd-checkbox-switch',
											'title' => __('Detele exiting tasks before the import',true),
											),
										'type' => 'checkbox', 
									));
									?>
								</div>
							</h2>
								<?php
								echo $this->Session->flash();
								echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
									'url' => array('controller' => 'project_tasks_preview', 'action' => 'import_excel/'.$project_id )));
								?>
								<div class="wd-input">
									<div class="wd-action-import">
										<?php 
										$step1 = '';
										$step2 = '';
										if(!empty($data_format)){
											$step2 = 'current';
										}else{
											$step1 = 'current';
										}
										?>
										<div class="wd-step-import wd-first-step <?php echo $step1; ?>">
											<span class="wd-step-number">1</span>
											<input type="file" name="FileField[csv_file_attachment]" accept=".xlsx"/>
											<button type="submit" id="import-submit" class="gradient" onclick="return false;" href="#"><?php echo __('Submit') ?></button>
											
										</div>
										<div class="wd-step-import wd-second-step <?php echo $step2; ?>">
											<span class="wd-step-number">2</span>
											<?php // if(!empty($data_format)) { ?>
												<button type="button" id="matching-field" class="gradient" href="#"><?php echo __('Matching fields') ?></button>
											<?php //} ?>
										</div>
										<div class="wd-step-import wd-third-step">
											<span class="wd-step-number">3</span>
											<button type="button" id="import-project" class="gradient" href="#"><?php echo __('Import tasks') ?></button>
										</div>
										
									</div>
									<div style="clear:both; margin: 15px 0;color: #008000; font-style:italic;"><?php __('Please select a file to upload') ?></div>
									<div id="error"></div>
								</div>
								<?php echo $this->Form->end(); ?>
								<div class="wd-table wd-table-2019" id="project_container" style="width: 100%">
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}

$columns = array();
if(!empty($data_columns)){
	$columns['id'] = array(
		'id' => 'no.',
		'field' => 'no.',
		'name' => '#',
		'width' => 40,
		'noFilter' => 1,
		'sortable' => false,
		'resizable' => false,
		'cssClass' => 'cell-text-center',
	);
	foreach($data_columns as $key => $name){
		$columns[$key] = array(
			'id' => $key,
			'field' => $key,
			'name' => __(!empty($name) ? $name : '', true),
			'width' => 170,
			'sortable' => true,
			'resizable' => true,
		);
	}
};
$dataView = array();
$i = 0;

if(!empty($data_format)){
	foreach ($data_format as $key => $value) {
		$data = $value;
		$data['no.'] = $key;
		$data['id'] = $i++;
		$data['MetaData'] =  array(
			'cssClasses' =>  'unselected',
		);
		$dataView[] = $data;
	}
}
$i18n = array(
	'-- Any --' => __('-- Any --', true),
	'Delete' => __('Delete', true),
	'Task of sub task doesn\'t exist' => __('Task of sub task doesn\'t exist', true),
	'Phase not created' => __('Phase not created', true),
	'Part not created' => __('Part not created', true),
	'Phase empty' => __('Phase empty', true),
	'Task empty' => __('Task empty', true),
	'The matching with phase column was not found' => __('The matching with phase column was not found', true),
	'The matching with task column was not found' => __('The matching with task column was not found', true),
	'Phase not exist in this project' => __('Phase not exist in this project', true),
	'Value has to be >=0' => __('Value has to be >=0', true),
	'Format date DD-MM-YYYY' => __('Format date DD-MM-YYYY', true),
	'Status doesn\'t exist' => __('Status doesn\'t exist', true),
	'Milestone doesn\'t exist' => __('Milestone doesn\'t exist', true),
	'The task name already exists' => __('The task name already exists', true),
	'The sub task name already exists' => __('The sub task name already exists', true),
	'User or team doesn\'t exist' => __('User or team doesn\'t exist', true),
	'End date must be > start date' => __('End date must be > start date', true),
	'Predecessor doesn\'t exist' => __('Predecessor doesn\'t exist', true),
	'Task of sub task not in Phase' => __('Task of sub task not in Phase', true),
	'Date must be > current date - 2 years and < current date + 10 years' => __('Date must be > current date - 2 years and < current date + 10 years', true),
	'Select all' => __('Select all', true),
	'Unselect all' => __('Unselect all', true),
);
$default_matching = array(
	1 => 'project_planed_part_text',
	2 => 'project_planed_phase_text',
	3 => 'task',
	5 => 'status',
	7 => 'start_date',
	8 => 'duration',
	9 => 'end_date',
	10 => 'assigned_to'
	
);
$matching_fields = array();
if(!empty($setting_matching)){
	foreach($setting_matching as $key => $value){
		if($value != 'undefined'){
			$matching_fields[$key] = $value;
		}
	}
}else{
	$matching_fields = $default_matching;
}
$listEmployeeRefer = !empty($listEmployee) ? Set::classicExtract($listEmployee, '{n}.name') : array();
$projectStatusRefer = !empty($projectStatus) ? Set::classicExtract($projectStatus, '{n}.name') : array();
$projectMilestonesRefer = !empty($projectMilestones) ? Set::classicExtract($projectMilestones, '{n}.project_milestone') : array();
$partPlanRefer = !empty($partPlan) ? Set::classicExtract($partPlan, '{n}.title') : array();
$phasePlanRefer = !empty($phasePlan) ? Set::classicExtract($phasePlan, '{n}.name') : array();
$task_name_refer = !empty($task_name) ? Set::classicExtract($task_name, '{n}.task_title') : array();
$task_phase_plan_refer = !empty($task_name) ? Set::combine($task_name, '{n}.id', '{n}.project_planed_phase_id') : array();

$originalText = array();
foreach($original_text as $key => $val){
	$originalText[$key] = __d(sprintf($_domain, 'Project_Task'), $val, true);
}
?>
<script type="text/javascript">
var i18n = <?php echo json_encode($i18n); ?>;
function get_grid_option(){
	var _option ={
		showHeaderRow: true,
		frozenColumn: '',
		enableAddRow: true,   
		rowHeight: 40,
		topPanelHeight: 40,
		headerRowHeight: 40
	};

	if( $(window).width() > 992 ){
		return _option;
	}
	else{
		_option.frozenColumn = '';
		return _option;
	}
}
(function($){
	$(function(){
		var $this = SlickGridCustom;
		var step = 'done1';
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend($this,{
			canModified: true,
		});
        var data = <?php echo json_encode($dataView); ?>;
        var matching_fields = <?php echo json_encode($matching_fields); ?>;
        var project_id = <?php echo json_encode($project_id); ?>;
        var list_project = <?php echo json_encode($list_project); ?>;
        var original_text = <?php echo json_encode($originalText); ?>;
        var task_name = <?php echo json_encode($task_name); ?>;
        var task_name_refer = <?php echo json_encode($task_name_refer); ?>;
        var listEmployee = <?php echo json_encode($listEmployee); ?>;
        var listEmployeeRefer = <?php echo json_encode($listEmployeeRefer); ?>;
        var projectPhase = <?php echo json_encode($projectPhase); ?>;
        var projectStatus = <?php echo json_encode($projectStatus); ?>;
        var projectStatusRefer = <?php echo json_encode($projectStatusRefer); ?>;
        var task_phase_plan_refer = <?php echo json_encode($task_phase_plan_refer); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
		
		function update_table_height(){
			wdTable = $('.wd-table');
			var heightTable = $(window).height() - wdTable.offset().top - 80;
			wdTable.height(heightTable);
			if( SlickGridCustom.getInstance() ) SlickGridCustom.getInstance().resizeCanvas();
		}
		$(window).resize(function(){
			update_table_height();
		});
		update_table_height();
		
		$('#import-project').on('click', function(){
			if(step != 'done2') return;
			listProjects = ControlGrid.getData().getItems();
			length = ControlGrid.getDataLength();
			var has_delete = $('input[name="data[delete_task_exist]"]:checked');
			has_delete = has_delete.length > 0 ? 1 : 0;
			if(has_delete == 1 && length > 0){
				run_delete_task_exist();
			}else{
				if(length > 0) run_import();
			}
		});
		renderHeaderProjectField();
		var curr = 0;
		var listProjects;// = ControlGrid.getData().getItems();
		var length;// = allItem.length;
		var _time = '';
		var each_run = 0;
		var is_success = false;
		var current_task = [];
		var current_phase_refer = [];
		var current_task_refer = [];
		var parent_task_refer = [];
		var t = 0;
		function run_delete_task_exist(){
			if(project_id){
				$.ajax({
					url : '/project_tasks/deleteTaskWhenImport',
					type: 'POST',
					dataType: 'json',
					data: {
						data: list_project
					},
					success:function(res){
						if(res.success){
							run_import();
						}
					},
				});
			}
		}
		function run_import(){
			$('#uploadForm').slideUp();
			var task_import;
			for(var i=0 ; ( !task_import && (curr < length)); i++){
				task = ControlGrid.getData().getItem(curr);
				if( task && task['can_save']){
					task_import = task;
					if($.trim(task['parent_name'])){
						var index_parent_name = current_task.indexOf($.trim(task['parent_name']));
						if(current_task_refer[index_parent_name]){
							task_import['parent_id'] = current_task_refer[index_parent_name]['id'];
						}
					}
					if($.trim(task['predecessor_name'])){
						var index_predec_name = current_task.indexOf($.trim(task['predecessor_name']));
						if(current_task_refer[index_predec_name]){
							task_import['predecessor'] = current_task_refer[index_predec_name]['id'];
						}
					}
				}
				curr++;
			}
			var has_delete = $('input[name="data[delete_task_exist]"]:checked');
			if( task_import){
				slickGridRowStatus(task_import, 'loading');
				var fn_name = task_import['update'] ? 'updateTaskJson' : 'createTaskJson';
				$.ajax({
					url : '/project_tasks/importUpdateTask',
					type: 'POST',
					dataType: 'json',
					data: {
						data: task_import,
						has_delete: has_delete.length > 0 ? 1 : 0,
					}, 
					beforeSend: function(){
						
					},
					success: function(res){
						if( res.success){
							is_success = true;
							var result = res.message;
							current_task_refer[each_run] = [];
							current_task_refer[each_run]['id'] = result['ProjectTask']['id'];
							current_task_refer[each_run]['task_title'] = result['ProjectTask']['task_title'];
							// if(result['ProjectTask']['parent_id']){
								// if(typeof parent_task_refer[result['ProjectTask']['parent_id']] == 'undefined') parent_task_refer[result['ProjectTask']['parent_id']] = [];
								// parent_task_refer[result['ProjectTask']['parent_id']].push(result['ProjectTask']['task_title']);
								// console.log(parent_task_refer);
							// }
							each_run++;
							slickGridRowStatus(result, false);
							
						}else{
							slickGridRowStatus(task_import, 'error');
						}
						if( length >= curr){
							setTimeout( function(){
								run_import();
							}, 150);
						}
					},
					complete: function(){
						if(length == curr && is_success){
							$('#export-submit').show();
							$.ajax({
								url : '/project_tasks/staffingWhenUpdateTask/' + project_id,
								type: 'POST',
								dataType: 'json',
							});
						}
					},
					error: function(){
						
					},			
				});
			}else{
				if(length == curr && is_success){
					$('#export-submit').show();
					$.ajax({
						url : '/project_tasks/staffingWhenUpdateTask/' + project_id,
						type: 'POST',
						dataType: 'json',
					});
				}
			}
		}
		function slickGridRowStatus( datas, cssClasses = ''){
			var goRow = 0;
			if(datas){
				var row_id;
				var rowClass;
				if(cssClasses){
					row_id = datas['no.'];
					rowClass = cssClasses;
					// goRow = val['id'];
				}else{
					row_id = datas['ProjectTask']['no.'];
					rowClass = 'success';
					goRow = datas['ProjectTask']['no.'];
					refreshDataView(datas);
				}
				
				var row = ControlGrid.getData().getItem(row_id);
				row.MetaData.cssClasses = rowClass;
				ControlGrid.updateRow(row);
			}
			ControlGrid.invalidate();
			ControlGrid.render();
			if(goRow > 0) ControlGrid.scrollRowIntoView(goRow, true);
		}
		function refreshDataView(datas){
			var dataView = ControlGrid.getData();
			dataView.beginUpdate();
			var _new_data = dataView.getItems();
			$.each(_new_data, function (ind, item) {
				if (item.id == datas['ProjectTask']['no.']) {
					if(datas['ProjectTask']['task_start_date']){
						task_start_date = datas['ProjectTask']['task_start_date'].split('-');
						if(task_start_date.length > 0){
							item['start_date'] = task_start_date[2] +'-'+ task_start_date[1] +'-'+  task_start_date[0];
						}
					}
					if(datas['ProjectTask']['task_end_date']){
						task_end_date = datas['ProjectTask']['task_end_date'].split('-');
						if(task_end_date.length > 0){
							item['end_date'] = task_end_date[2] +'-'+ task_end_date[1] +'-'+  task_end_date[0];
						}
					}
					item['duration'] = datas['ProjectTask']['duration'] == 0 ? 1 : datas['ProjectTask']['duration'];
					_new_data[ind] = item;
				}
			});
			dataView.setItems(_new_data);
			dataView.endUpdate();
		}
		var field_selected = [];
		function renderHeaderProjectField(){
			var output_select_header = '<select class="select_header"> <option>- Select -</option>';
			$.each(original_text, function(key, name){
				if(key) output_select_header += '<option value="'+ key +'">'+ name +'</option>';
			});
			output_select_header += '</select>';
			var columns = SlickGridCustom.getInstance().getColumns();
			$.each(columns, function(index, value){
				if(value['id'] != 'no.'){
					header = ControlGrid.getHeaderRowColumn(value['id']);
					$(header).html('<div id="gs-value-' + value['id'] + '" class="row-number date-value gs-header-row-value" >' + output_select_header + '</div>');
				}
				if(matching_fields[value['id']]){
					$('#gs-value-' + value['id']).find('option[value="'+ matching_fields[value['id']] +'"]').prop("selected", true);
					$('select option[value="'+ matching_fields[value['id']] +'"]').prop('disabled', 'disabled');
				}
			});
		}
		$('.gs-header-row-value select').on('focus', function () {
			prev_val = $(this).val();
		}).change(function(){
			var val = $(this).val();
			if(prev_val != 0 && prev_val != val){
				field_selected.splice( $.inArray(prev_val,field_selected),1);
			}
			field_selected.push(val);
			$('.gs-header-row-value select option').removeAttr('disabled');
			$.each(field_selected, function(ind, value){
				$('select option[value="'+ value +'"]').prop('disabled', 'disabled');
			});
			
		});
		// function jsonMatchingOptions(options){
			// var output = [];
			// var m = 0;
			// $.each(options, function(key, values) {
				// var out = {};
				// var n = 0;
				// $.each(values, function(i, val) {
					// out[i] = val;
				// });
				// output[key] = out;
			// });
			// return output;
		// }
		$("#matching-field").click(function(){
			if(step != 'done1') return;
			var columns = SlickGridCustom.getInstance().getColumns();
			var list_matching = [];
			var matching_columns = [];
			var matching_datas = [];
			matching_columns[0] = {
				'id': 'no.',
				'field': 'no.',
				'name': '#',
				'width': 40,
				'noFilter':  1,
				'sortable': false,
				'resizable': false,
				'cssClass': 'cell-text-center',
			};
			n = 1;
			$.each(columns, function(index, value){
				field = $(ControlGrid.getHeaderRowColumn(value['id'])).find('select').val();
				if(field != 0 && value['id'] != 'no.' && field != 'undefined' && field != '- Select -'){
					list_matching[value['id']] = field;
					matching_columns[n++] = {
						'id':  field,
						'field': field,
						'name': original_text[field] ? original_text[field] : '',
						'width': 170,
						'sortable': true,
						'resizable': true,
						'formatter': function(row, cell, value, columnDef, dataContext){
							var html = '';
							if(value){
								html = '<div>'+ value +'</div>';
							}
							return html;
						},
					};
				}
			});
			if(list_matching.length > 0){
				if(list_matching.indexOf('project_planed_phase_text') == -1){
					$('#flashMessage').find('span').html(i18n['The matching with phase column was not found']);
					$('#flashMessage').show();
					return;
				}else{
					$('#flashMessage').hide();
				}
				if(list_matching.indexOf('task') == -1){
					$('#flashMessage').find('span').html(i18n['The matching with task column was not found']);
					$('#flashMessage').show();
					return;
				}else{
					$('#flashMessage').hide();
				}
			}
			// save matching in to history_filter table
			if(list_matching.length > 0){
				step = 'done2';
				$.ajax({
					type: 'POST',
					url: '/project_tasks_preview/save_setting_matching_fields',
					data: {
						path: 'tasks_importer_setting_matching_fields',
						params: list_matching,
					},
					cache: false,
					success: function (respon) {
					}
				});
			}
			var n = 0;
			
			var current_child_task = [];
			var ct = 0;
			$.each(data, function(index, values){
				var matching_item = {
					'MetaData': [],
					'no.':  index,
					'id': index,
					'estimated': 0,
					'parent_id': 0,
					'task_status_id': projectStatus[0]['id'], // default status
				}
				var has_value = false;
				var can_save = true;
				$.each(list_matching, function(key, field_name){
					val = $.trim(values[key]);
					var field_key = field_name;
					if(field_name && val && typeof field_name !== 'undefined'){
						if(field_name == 'project_planed_phase_text'){
							if(projectPhase.indexOf(val) == -1){
								val = '<span class="wp-error" title="'+ i18n['Phase not created'] +'">'+ val +'</span>';
								can_save = false;
							}
						}
						if(field_name == 'predecessor_name'){
							if(task_name_refer.indexOf(val) > -1 || current_task.indexOf(val) > -1){
								task_index = task_name_refer.indexOf(val);
								if(task_name[task_index]){
									matching_item['predecessor'] = task_name[task_index]['id'];
								}else if(current_task.indexOf(val) > -1){
									matching_item['predecessor_name'] = val;
								}else{
									val = '<span class="wp-error" title="'+ i18n['Predecessor doesn\'t exist'] +'">'+ val +'</span>';
									can_save = false;
								}
								
							}else{
								
							}
						}
						if(field_name == 'task'){
							field_key = 'task_title';
						}
						if(field_name == 'sub_task' ){
							field_key = 'task_title';
							var index_task = list_matching.indexOf('task');
							var key_task = '';
							if(index_task == -1){
								val = '<span class="wp-error" title="'+ i18n['Task of sub task doesn\'t exist'] +'">'+ val +'</span>';
								can_save = false;
							}
						}
						if(field_name == 'manual_consumed' || field_name == 'amount_€' || field_name == 'workload'){
							if(field_name == 'workload') field_key = 'estimated'; 
							if( isNaN(val) || parseFloat(val) < 0){
								val = '<span class="wp-error" title="'+ i18n['Value has to be >=0'] +'">'+ val +'</span>';
								can_save = false;
							}
						}
						if(field_name == 'status'){
							field_key = 'task_status_text';
							if(val && projectStatusRefer.indexOf(val) == -1){
								val = '<span class="wp-error" title="'+ i18n['Status doesn\'t exist'] +'">'+ val +'</span>';
								can_save = false;
							}else{
								var s_index = projectStatusRefer.indexOf(val);
								matching_item['task_status_id'] = projectStatus[s_index]['id'];
							}
						}
						if(field_name == 'milestone'){
							field_key = 'milestone_text';
						}
						if(field_name == 'assigned_to'){
							field_key = 'task_assign_to_text';
							if(val){
								var assign_to = val.split(',');
								var is_exist = true;
								if(assign_to.length > 0){
									matching_item['task_assign_to_id'] = [];
									matching_item['is_profit_center'] = [];
									$.each(assign_to, function(i, name){
										if(listEmployeeRefer.indexOf($.trim(name)) == -1){
											is_exist = false;
										}else{
											var e_index = listEmployeeRefer.indexOf($.trim(name));
											matching_item['task_assign_to_id'][i] = listEmployee[e_index]['id'];
											matching_item['is_profit_center'][i] = listEmployee[e_index]['is_profit_center'];
										}
									});
								}
								if(!is_exist){
									val = '<span class="wp-error" title="'+ i18n['User or team doesn\'t exist '] +'">'+ val +'</span>';
									can_save = false;
								}
							}
						}
						// if(field_name.substr(0, 5) == 'date_' || field_name == 'start_date' || field_name == 'end_date'){
						if(field_name == 'start_date' || field_name == 'end_date'|| field_name == 'initial_start_date'|| field_name == 'initial_end_date'){
							date_key = 'task_'+field_name;
							if(field_name == 'initial_start_date') field_key = 'initial_task_start_date';
							if(val){
								val = val.toString();
								var regE = new RegExp("[a-zA-Z]");
								if(val.match(regE)){
									val = '<span class="wp-error" title="'+ i18n['Format date DD-MM-YYYY'] +'">'+ val +'</span>';
									can_save = false;
								}else{
									var regE4 = new RegExp("^([0-9]{2})(\/|-|\.)([0-9]{2})(\/|-|\.)([0-9]{4}$)");
									if(val.match(regE4)){
										_date = val.split('-');
										var min_date = new Date(parseInt(_date[2]) + 2,_date[1],_date[0]);
										var max_date = new Date(_date[2] - 10,_date[1],_date[0]);
										var c_date = new Date();
										if(max_date > c_date || min_date < c_date){
											val = '<span class="wp-error" title="'+ i18n['Date must be > current date - 2 years and < current date + 10 years'] +'">'+ val +'</span>';
											can_save = false;
										}else {
											if(field_name == 'end_date'){
												var index_start = list_matching.indexOf('start_date');
												if(index_start > -1 && values[index_start]){
													 var _start_date = values[index_start];
													if(_start_date.match(regE4)){
														_start_date = _start_date.split('-');
														var _start = new Date(_start_date[2],_start_date[1],_start_date[0]);
														var _end_date = new Date(_date[2],_date[1],_date[0]);
														if(_start > _end_date){
															val = '<span class="wp-error" title="'+ i18n['End date must be > start date'] +'">'+ val +'</span>';
															can_save = false;
														}
													}
												}
											}
											if(field_name == 'initial_end_date'){
												date_key = 'initial_task_end_date';
												var index_initial_start = list_matching.indexOf('initial_start_date');
												if(index_initial_start > -1 && values[index_initial_start]){
													 var _initial_start_date = values[index_initial_start];
													if(_initial_start_date.match(regE4)){
														_initial_start_date = _initial_start_date.split('-');
														var _initial_start = new Date(_initial_start_date[2],_initial_start_date[1],_initial_start_date[0]);
														var _initial_end_date = new Date(_date[2],_date[1],_date[0]);
														if(_initial_start > _initial_end_date){
															val = '<span class="wp-error" title="'+ i18n['End date must be > start date'] +'">'+ val +'</span>';
															can_save = false;
														}
													}
												}
											}
										}
										matching_item[date_key] = _date[2]+'-'+_date[1]+'-'+_date[0];
									}else{
										val = '<span class="wp-error" title="'+ i18n['Format date DD-MM-YYYY'] +'">'+ val +'</span>';
										can_save = false;
									}
								}
							}
						}
						matching_item[field_key] = val;
						matching_item[field_name] = val;
						has_value = true;
					}
				});
				if(has_value){
					matching_item['can_save'] = can_save;
					matching_item['project_id'] = project_id;
					if(can_save){
						current_task[t++] = matching_item['task_title'];						
						if(!$.trim(matching_item['project_planed_phase_text'])){
							// Phase - Check phase empty
							val = '<span class="wp-error wp-cell-empty" title="'+ i18n['Phase empty'] +'"></span>';
							matching_item['can_save'] = false;
							matching_item['project_planed_phase_text'] = val;
						}
						if(!$.trim(matching_item['task'])){
							// Task - Check task empty
							val = '<span class="wp-error wp-cell-empty" title="'+ i18n['Task empty'] +'"></span>';
							matching_item['can_save'] = false;
							matching_item['task'] = val;
						}
					}
					matching_datas[n++] = matching_item;
				}
			});
			if(matching_datas.length > 0){
				console.log(matching_datas);
				ControlGrid = $this.init($('#project_container'),matching_datas,matching_columns, get_grid_option());
				$('.wd-step-import').removeClass('current');
				$('.wd-action-import').find('.wd-third-step').addClass('current');
				
				var exporter = new Slick.DataExporter('/project_tasks_preview/export_excel_index', function beforeSubmit(_form){
					var export_data = export_parse(true);
					$('#slick-data-exporter').find('input').val(export_data);
				});
				function export_parse(json){
					var length = ControlGrid.getDataLength(),
						columns = ControlGrid.getColumns(),
						data = [];
					for(var i = 0; i < length; i++){
						var item = ControlGrid.getDataItem(i);
						if(item.can_save && item.MetaData.cssClasses == 'success'){
							var cols = [];
							// column
							for(var j = 0; j < columns.length; j++){
								var column = columns[j],
									field = column.field,
									value;
								if( typeof column.ignoreExport != 'undefined' && column.ignoreExport ){
									continue;
								}
								if( typeof column.exportFormatter == 'function' ){
									value = column.exportFormatter.call(ControlGrid, item[field], item);
								} else if ( typeof column.isSelected != 'undefined' && column.isSelected ){
									var f = ControlGrid.getFormatter(i, column);
									value = $.trim(
										$('<div />').append(
											f(i, j, item[field], column, item)
										).find(':selected').html()
									);
								} else if( typeof column.isImage != 'undefined' && column.isImage ){
									var f = ControlGrid.getFormatter(i, column);
									var img = $.trim(
										$('<div />').append(
											f(i, j, item[field], column, item)
										).find('.change-image').data('image')
									);
									value = 'image:' + img;
								} else {
									var f = ControlGrid.getFormatter(i, column);
									value = $.trim(
										$('<div />').append(
											f(i, j, item[field], column, item)
										).text()
									);
								}
								// remove j.H, MD,... in projects.
								if(typeof controller != 'undefined' && controller == 'projects'){
									value = value.replace(/ J.H/g, "").replace(/ M.D/g, "").replace(/ €/g, "");
								}
								// check markup
								if( typeof column.type != 'undefined' ){
									cols.push({
										type: column.type,
										value: value,
										define: column
									});
								} else {
									// append to cols
									cols.push(value);
								}
							}
							// append to data
							data.push(cols);
						}
					}
					var header = [];
					for( var i = 0; i < columns.length; i++){
						var column = columns[i];
						if( typeof column.ignoreExport != 'undefined' && column.ignoreExport ){
							continue;
						}
						var headerName = column.nameExport ? column.nameExport : column.name;
						header.push(headerName);
					}
					var ret = {
						header: header,
						body: data
					};
					if( json ){
						return JSON.stringify(ret);
					}
					return ret;
				}
				ControlGrid.registerPlugin(exporter);
				ControlGrid.onClick.subscribe(function(e, args) {
					var _columns = args.grid.getColumns();
					var _field = _columns[args.cell]['field'];
					if(_field == "selected"){ 
						var item = args.grid.getData().getItem(args.row);
						if( item.deleted){ return;}
						is_selected = item.selected;
						item.selected = is_selected ? 0 : 1;
						if(item.selected){
							item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/unselected/g, 'selected');
						}else{
							item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/selected/g, 'unselected');
						}
						$this.update_after_edit(item.id, item);
						// var _selected = get_list_selected();
						// if( _selected.length) {
							// console.log(_selected);
						// }			
					}
				});
				$('#export-submit').click(function () {
					$this.isExporting = 1;
					exporter.submit();
					$this.isExporting = 0;
					return false;
				});
			}
			
		});
		
	})
})(jQuery);

</script>
<script type="text/javascript">
	(function($){
	
		$("#import-submit").click(function(){
			$(".error-message").remove();
			$("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
			if($("input[name='FileField[csv_file_attachment]']").val()){
				var filename = $("input[name='FileField[csv_file_attachment]']").val();
				var valid_extensions = /(\.csv)$/i;   
				// if(valid_extensions.test(filename)){ 
					$('#uploadForm').submit();
					return true;
				// }
				// else{
					// $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
					// jQuery('<div>', {
						// 'class': 'error-message',
						// html: '<?php __('Incorrect file type') ?>'
					// }).appendTo('#error');
				// }
			}else{
				jQuery('<div>', {
					'class': 'error-message',
					html: '<?php __('Please choose a file!') ?>'
				}).appendTo('#error');
			}
			return false;
		});
		$(document).ready(function(){
			$('.select_header1').multiSelect({
				noneSelected:'Matching to', 
				appendTo : $('body'),
				oneOrMoreSelected: '*',
				selectAll: false,
				cssClass: 'slickgrid-multiSelect',
				oneSelected: true,
			});
		});
	})(jQuery);
</script>