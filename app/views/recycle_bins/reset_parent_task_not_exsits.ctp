<?php
	echo $this->Html->css(array(
		'slick_grid/slick.grid_v2',
		'slick_grid/slick.pager',
		'slick_grid/slick.common_v2',
		'slick_grid/slick.edit',
		'preview/grid-project',
		'preview/tab-admin',
		'layout_admin_2019',
		'layout_2019',
		'jquery.multiSelect',
	));
	echo $this->Html->script(array(
		'slick_grid/lib/jquery-ui-1.8.16.custom.min',
		'slick_grid/lib/jquery.event.drop-2.0.min',
		'slick_grid/lib/jquery.event.drag-2.2',
		'slick_grid/slick.core',
		'slick_grid/slick.dataview',
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
		'jquery.multiSelect',
	));
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
	/* Column for Slick grid */
	$canModified = !empty( $is_sas);
	$_columns = array(
		'no.' => array(
			'id' => 'no.',
			'field' => 'no.',
			'name' => 'No.',
			'width' => 40,
			'noFilter' => 1,
			'sortable' => false,
			'resizable' => false,
			'minWidth' => 40,
			'maxWidth' => 40,
		),
		'project_name' => array(
			'id' => 'project_name',
			'field' => 'project_name',
			'name' => __('project name', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'project_planed_phase_id' => array(
			'id' => 'project_planed_phase_id',
			'field' => 'project_planed_phase_id',
			'name' => __('project_planed_phase_id', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'project_planed_phase_id' => array(
			'id' => 'project_planed_phase_id',
			'field' => 'project_planed_phase_id',
			'name' => __('project_planed_phase_id', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'task_title' => array(
			'id' => 'task_title',
			'field' => 'task_title',
			'name' => __('task_title', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'parent_id' => array(
			'id' => 'parent_id',
			'field' => 'parent_id',
			'name' => __('parent_id', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'task_start_date' => array(
			'id' => 'task_start_date',
			'field' => 'task_start_date',
			'name' => __('task_start_date', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'task_end_date' => array(
			'id' => 'task_end_date',
			'field' => 'task_end_date',
			'name' => __('task_end_date', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'created' => array(
			'id' => 'created',
			'field' => 'created',
			'name' => __('created', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'updated' => array(
			'id' => 'updated',
			'field' => 'updated',
			'name' => __('updated', true),
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		)
	);
	if( empty($show_company)) {
		unset( $_columns['company_id']);
	}
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
	$i = 1;
	$dataView = array();
	if(!empty($project_parent_task_notexsit)){
		foreach ($project_parent_task_notexsit as $index => $parent_task){
			$task = $parent_task['ProjectTask'];
			$class = null;
			$row_data = array(
				'id' => $task['id'],
				'project_name' => $project_name[$task['project_id']],
				'project_planed_phase_id' => $task['project_planed_phase_id'],
				'task_title' => $task['task_title'],
				'parent_id' => $task['parent_id'],
				'task_start_date' => $task['task_start_date'],
				'task_end_date' => $task['task_end_date'],
				'created' => $task['created'],
				'updated' => $task['updated'],
				'no.' => $i++,
			);
			$dataView[] = $row_data;
		}
	}
	
?>
<style>
	.wd-panel .wd-section{
		display: block;
		width: 100%;
	}
	.wd-tab .wd-content{
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
	}
	.wd-table-container #project_container{
		width: 100%;
		float: none;
	}
	.wd-list-project .wd-tab .wd-panel{
		padding: 20px;
	}
	.slick-viewport .slick-row .slick-cell.grid-action{
		padding: 0;
		border-top:0;
	}
	.grid-action .wd-actions{
		margin: 0;;
	}
	.grid-action .wd-actions .wd-btn{
		width: 40px;
		height: 40px;
		float:left;
	}
	.btn.add-field{
		position: absolute;
		width: 50px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		border-radius: 50%;
		z-index: 5;
		top: -25px;
		margin: 0;
		right: 15px;
		box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
		background-color: #247FC3;
	}
	.btn.add-field:before{
		content: '';
		background-color: #fff;
		position: absolute;
		width: 2px;
		height: 20px;
		top: calc( 50% - 10px);
		left: calc( 50% - 1px);
	}
	.btn.add-field:after{
		content: '';
		position: absolute;
		background-color: #fff;
		width: 20px;
		height: 2px;
		left: calc( 50% - 10px);
		top: calc( 50% - 1px);
	}
	.grid-canvas .editor-text{
		display: block;
		margin: 0;
		box-sizing: border-box;
	}
	#wd-container-main.wd-project-admin .wd-layout {
		padding: 0 !important;
	}
	.wd-layout .wd-main-content .wd-tab{
		margin: 0;
		padding:40px;
	}
	.wd-content .wd-table-container{
		position: relative;
		border: none;
	}
	.wd-table-container .wd-table-2019 .slick-row .slick-cell .ui-combobox input{
		height: 38px;
	}
	.wd-table-container .wd-table-2019 .slick-row.active{
		z-index: 11;
	}
	/* width */
	body ::-webkit-scrollbar {
		width: 4px;
		height: 4px;
	}

	/* Track */
	body ::-webkit-scrollbar-track {
		box-shadow: inset 0 0 5px #F2F5F7; 
		border-radius: 5px;
		background-color: #fff;
	}

	/* Handle */
	body ::-webkit-scrollbar-thumb {
		background: #C6CCCF;; 
		border-radius: 5px;
	}
	#sb-company, #delete-all{
		border: 1px solid #363636;
		line-height: 26px;
		border-radius: 3px;
	}
	.multiSelect .selected-item{
		    font-style: normal;
	}
	#sl-company.multiSelect.loading{
		    background: #fff url(/img/loading_check.gif) center right 0px no-repeat !important;
	}
	.wd-table .wd-actions .wd-btn.wd-loading{
		background: #fff url(/img/loading_check.gif) center center no-repeat !important;
	}
	#delete-all.loading{
		background: #fff url(/img/loading_check.gif) center right no-repeat !important;
		padding-right: 40px;
	
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section clearfix" id="wd-fragment-1">
                            <div class="wd-content">
								<h3 class="wd-t3">&nbsp;</h3>
								<div id="message-place">
									<?php
									App::import("vendor", "str_utility");
									$str_utility = new str_utility();
									echo $this->Session->flash();
									?>
								</div>
								<div class="wd-table-container">
									<div class="wd-table wd-table-2019" id="project_container"  style="height: 800px">
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
var $this = SlickGridCustom;
$.extend($this,{
	i18n : <?php echo json_encode($i18n); ?>,
	canModified: true,
});
$this.fields = {
	id : {defaulValue : 0},
	name : {defaulValue : '', allowEmpty : false},
};

$.extend(Slick.Formatters,{
	requestAction: function(row, cell, value, columnDef, dataContext){
		var _html = '<div class="wd-actions wd-bt-big">';
		_html += '<a class="wd-btn btn-delete wd-hover-advance-tooltip" title="' + $this.t('Delete') + '" data-id = "' + dataContext.id +'" data-path="' + dataContext.name +'" onclick="deletePath.call(this)">' + $this.t('Delete') + '</a>';			
		_html += '</div>';
		return _html;
	}
});
var data = <?php echo json_encode($dataView); ?>;
var columns = <?php echo jsonParseOptions(array_values($_columns), array('editor', 'formatter', 'validator')); ?>;
var slick_options = {
	showHeaderRow: true,
	editable: true,
	enableAddRow: true,
	headerRowHeight: 40,
	rowHeight: 40,
}
var ControlGrid = $this.init($('#project_container'),data,columns, slick_options);
$(document).ready(function(){
	$('#sl-company').multiSelect({
		noneSelected:'Select the company to keep the files', 
		appendTo : $('body'),
		oneOrMoreSelected: '*',
		selectAll: true,
		cssClass: 'slickgrid-multiSelect',
	});
});
function deletePath(){
	var path = $(this).data('path');
	var path_id = $(this).data('id');
	var _this = $(this);
	_this.addClass('wd-loading');
	$.ajax({
		url : '/recycle_bins/unsetPath',
		type: 'POST',
		data: {
			path: path
		},
		dataType: 'JSON',
		success : function(result){
			var i = 0;
			var new_data = [];
			$.each( data, function(index, value){
				if(value['id'] !== path_id){
					new_data[i++] = value;
				}
			});
			data = new_data;
			ControlGrid.setData(data);
			ControlGrid.invalidate();
			_this.removeClass('wd-loading');
		}
	});
}
function deleteAll(_this){
	_this.addClass('loading');
	$.ajax({
		url : '/recycle_bins/unsetAllPath',
		type: 'POST',
		data: {
			path: data
		},
		dateType: 'JSON',
		success : function(respons){
			// data = [];
			// ControlGrid.setData(data);
			// ControlGrid.invalidate();
			getFileOfCompany();
			_this.removeClass('loading');
			if(data.length > 0) deleteAll(_this);
		}
	});
}
function getFileOfCompany(){
	var company_selected = $('#sl-company').find('.selected-item');
	var company_id = [];
	$.each(company_selected, function(n,e){
		company_id[n] = $(e).data('id');
	});
	if(company_id.length > 0){
		$('#sl-company').addClass('loading');
		$.ajax({
			url : '/recycle_bins/delete_files',
			type: 'POST',
			data: {
				company_id: company_id
			},
			async: false,
			success : function(respons){
				var paths = JSON.parse(respons);
				var ajaxView = [];
				var i = 0;
				$.each( paths, function(index, path){
					ajaxView[i] = {
						'no.':  i + 1,
						'id': i + 1,
						'name': path
					}
					i++;
				});
				data = ajaxView;
				if(data.length > 0) $('#delete-all').show();
				ControlGrid.setData(data);
				ControlGrid.invalidate();
				$('#sl-company').removeClass('loading');
			}
		});
	}
}
$('#delete-all').on('click', function(){
	var _this = $(this);
	deleteAll(_this);
});
$('#sb-company').on('click', function(){
	getFileOfCompany();
});
</script>
