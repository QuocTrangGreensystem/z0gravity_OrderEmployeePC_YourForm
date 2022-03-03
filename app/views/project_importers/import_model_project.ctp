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
	'slick_grid/slick.grid.origin',
	'slick_grid_custom',
	'slick_grid/slick.grid.activity',
	'jquery.scrollTo',
	'jquery.multiSelect',
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
}?>	

<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>

<div id="wd-container-main" class="wd-project-index">
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
								<h2 class="wd-t3"><?php __('Import projects', true) ?></h2>
									<?php
									echo $this->Session->flash();
									echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
										'url' => array('controller' => 'project_importers', 'action' => 'import_model_project')));
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
												<input type="file" name="FileField[csv_file_attachment]" />
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
												<button type="button" id="import-project" class="gradient" href="#"><?php echo __('Import projects') ?></button>
											</div>
											
										</div>
										<div style="clear:both; margin: 15px 0;color: #008000; font-style:italic;"><?php __('Allowed file type') ?>: *.xlsx</div>
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
		$data['MetaData'] = array();
		$dataView[] = $data;
	}
}
$i18n = array(
	'-- Any --' => __('-- Any --', true),
	'Delete' => __('Delete', true),
);
$default_matching = array(
	0 => 'project_name',
	1 => 'project_manager_id',
	2 => 'project_amr_program_id',
	3 => 'model_name',
	4 => 'start_date'
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
?>
<script type="text/javascript">
var matching_fields = <?php echo json_encode($matching_fields); ?>;
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
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend($this,{
			canModified: true,
		});
		
        var data = <?php echo json_encode($dataView); ?>;
        var matching_fields = <?php echo json_encode($matching_fields); ?>;
        var original_text = <?php echo json_encode($original_text); ?>;
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
			if(length > 0) run_import();
		});
		renderHeaderProjectField();
		var curr = 0;
		var listProjects;// = ControlGrid.getData().getItems();
		var length;// = allItem.length;
		var _time = '';
		var each_run = 50;
		function run_import(){
			$('#uploadForm').slideUp();
			var list_submit = [];
			for(var i=0 ; ((i < each_run) && (curr < length)); i++){
				pr = ControlGrid.getData().getItemById(curr);
				if( pr) list_submit.push(pr);
				curr++;
			}
			if( list_submit.length ){
				slickGridRowStatus(list_submit, 'loading');
				$.ajax({
					url : '/project_importers/save_import_model_projects',
					type: 'POST',
					dataType: 'json',
					data: {
						data: JSON.stringify(list_submit),
					}, 
					beforeSend: function(){
						
					},
					success: function(res){
						if( res.success){
							slickGridRowStatus(res.success, false);
							if( length >= curr){
								setTimeout( function(){
									run_import();
								}, 150);
							}else{
								
							}
						}else{
							
						}
					},
					complete: function(){
						
					},
					error: function(){
						
					},			
				});
			}else{
				$.ajax({
					url : '/recycle_bins/generateAvatar',
					type: 'POST',
					dataType: 'json',
				});
			}
		}
		function slickGridRowStatus( datas, cssClasses = ''){
			var goRow = 0;
			$.each(datas, function(key, val){
				var row_id;
				var rowClass;
				if(cssClasses){
					row_id = val['id'];
					rowClass = cssClasses;
					// goRow = val['id'];
				}else{
					row_id = key;
					rowClass = val ? 'success' : 'error';
					goRow = key;
				}
				
				var row = ControlGrid.getData().getItemById(row_id);
				row.MetaData.cssClasses = rowClass;
				ControlGrid.updateRow(row);
			});
			ControlGrid.invalidate();
			ControlGrid.render();
			if(goRow > 0) ControlGrid.scrollRowIntoView(goRow, true);
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
			var mandatory_fields = ['project_name','project_manager_id', 'model_named', 'project_amr_program_id'];
			$.each(columns, function(index, value){
				field = $(ControlGrid.getHeaderRowColumn(value['id'])).find('select').val();
				var idx = $.inArray(field, mandatory_fields);
				if (idx !== -1 || field == 'start_date') {
					mandatory_fields.splice(idx, 1);
					list_matching[value['id']] = field;
					matching_columns[n++] = {
						'id':  field,
						'field': field,
						'name': original_text[field] ? original_text[field] : '',
						'width': 270,
						'sortable': true,
						'resizable': true,
					};
				}
			});
			if(mandatory_fields.length > 0){
				alert('Please matching field ' + original_text[mandatory_fields[0]]);
                return false;
			}
			// save matching in to history_filter table
			if(list_matching.length > 0){
				step = 'done2';
				$.ajax({
					type: 'POST',
					url: '/project_importers/save_setting_matching_fields',
					data: {
						path: 'model_project_importers_setting_matching_fields',
						params: list_matching,
					},
					cache: false,
					success: function (respon) {
						// /console.log(respon);
					}
				});
			}
			yn_matching = ['yes', 'no', 'non', 'oui'];
			$.each(data, function(index, values){
				matching_datas[index] = {
					'MetaData': [],
					'no.':  index,
					'id': index,
				}
				$.each(list_matching, function(key, field_name){
					if(field_name && typeof field_name !== 'undefined'){
						val = values[key];
						if(field_name == 'start_date'){
							val = val.toString();
							var regE = new RegExp("[a-zA-Z]");
							if(val.match(regE)){
								val  = '';
							}else{
								var regE2 = new RegExp("^([0-9]{4}$)");
								var regE3 = new RegExp("^([0-9]{2})(\/|-|\.)([0-9]{4}$)");
								var regE4 = new RegExp("^([0-9]{2})(\/|-|\.)([0-9]{2})(\/|-|\.)([0-9]{4}$)");
								if (val.match(regE2)) {
									val = '01/01/'+ val;
								}else if(val.match(regE3)){
									val = '01/'+ val;
								}else if(val.match(regE4)){
									val = val;
								}else{
									val = '';
								}
							}
						}
						matching_datas[index][field_name] = val;
					}
				});
				// if the program is empty then the category is Opportunity because when linked the family/program must not empty
				
				if(typeof matching_datas[index]['project_amr_program_id'] === 'undefined' || matching_datas[index]['project_amr_program_id'] == '') matching_datas[index]['category'] = 'Opportunity';
				
			});
			
			if(matching_datas.length > 0){
				ControlGrid = $this.init($('#project_container'),matching_datas,matching_columns, get_grid_option());
				$('.wd-step-import').removeClass('current');
				$('.wd-action-import').find('.wd-third-step').addClass('current');
			}
			
		});
	})
})(jQuery);

</script>
<script type="text/javascript">
	(function($){
		$('#download-sample').click(function(){
			location.href = '<?php echo $this->Html->url('/shared/sample-projects.csv') ?>'
		});
		function submitForm(type,task){
			$('#import-task').val(task);
			$('#import-type').val(type);
			$('#import-form').submit();
		}
		
		$('#submit-create-export').click(function(){
			submitForm('Create','export');
		});
		$('#submit-create-do').click(function(){
			submitForm('Create','do');
		});
		
		$('#submit-update-export').click(function(){
			submitForm('Update','export');
		});
		$('#submit-update-do').click(function(){
			submitForm('Update','do');
		});
		
		$('#submit-error-export').click(function(){
			submitForm('Error','export');
		});
		$('#submit-export-all').click(function(){
			submitForm('Create,Update','do');
		});
		
		
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