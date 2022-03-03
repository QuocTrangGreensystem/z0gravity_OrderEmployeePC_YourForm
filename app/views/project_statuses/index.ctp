<?php echo $html->script('jquery.validation.min'); 
echo $html->script(array(
	'history_filter',
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
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/lib/jquery.event.drop-2.0.min',
));

echo $html->css(array(
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
	'preview/layout',
	
));
?>
<?php echo $html->css('jquery.ui.custom'); 
echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
	.wd-panel .wd-section{
		display: block;
		width: 100%;
	}
	.wd-tab .wd-content{
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
		position: relative;
		padding-top: 20px;
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
		top: 0;
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
	.wd-dialog-2019 .dialog-content{
		padding-top: 0;
	}
	.dialog-content .value-block-content{
		overflow: auto;
		max-height: 400px;
	}
	.wd-dialog-2019 .dialog-content .wd-submit-row{
		margin-top: 15px;
	}
	.slick-viewport .slick-row .slick-cell.wd-moveline{
        padding: 0;
    }
    .wd-moveline.slick-cell-move-handler svg{
        padding: 0;
    }
    .slick-cell-move-handler {
        cursor: move;
    }
</style>
<?php
	$employee_info = $this->Session->read("Auth.employee_info");
	$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
	if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
		$isAdminSas = 1;
	}else{
		$isAdminSas = 0;
	}
	if(!empty($employee_info['CompanyEmployeeReference'])){
		$current_company = $employee_info['CompanyEmployeeReference']['company_id'];
	}else{
		$current_company = '';
	}
?>
<div id="dialog_skip_value" class="buttons dialog_skip_value" style="display: none;">
	<div class="dialog-content loading-mark">
		<div class="wd-row">
			<div class="wd-col wd-col-lg-6">
				<h1 class="h1-value"><?php __('Projects');?></h1>
				<div id="project-reference" >
				</div>
			</div>
			<div class="wd-col wd-col-lg-6">
				<h1 class="h1-value"><?php __('Project tasks');?></h1>
				<div id="project-task-reference">
				</div>
			</div>
		</div>
		<div class="wd-row wd-submit-row">
			<div class="wd-col-xs-12">
				<div class="wd-submit">
					<a class="btn-form-action btn-cancel" id="cancel-special" href="javascript:void(0);" onclick="cancel_dialog(this);">
						<?php __('Cancel');?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
			
				<div class="wd-title"></div>
				<?php
					App::import("vendor", "str_utility");
					$str_utility = new str_utility();
					$status = array(
						'IP' => __('In Progress', true),
						'CL' => __('Closed', true)
					);
				?>
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
								<div id="message-place">
									<?php echo $this->Session->flash();	?>
								</div>
								<a href="javascript:void(0);" class="btn add-field" id="add_request" style="margin-right:5px;" title="Add an item" onclick="addNewStatus();"></a>
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
<div id="action-template" style="display: none;">
	<div class="action-menu">
		<a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true))); ?>');" class="action-menu-item" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s')); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20.03 20">
		  <defs>
			<style>
			  .cls-1 {
				fill: #666;
				fill-rule: evenodd;
			  }
			</style>
		  </defs>
		  <path id="suppr" class="cls-1" d="M6644.04,275a0.933,0.933,0,0,1-.67-0.279l-8.38-8.374-8.38,8.374a0.954,0.954,0,1,1-1.35-1.347l8.38-8.374-8.38-8.374a0.954,0.954,0,0,1,1.35-1.347l8.38,8.374,8.38-8.374a0.933,0.933,0,0,1,.67-0.279,0.953,0.953,0,0,1,.67,1.626L6636.33,265l8.38,8.374A0.953,0.953,0,0,1,6644.04,275Z" transform="translate(-6624.97 -255)"/>
		</svg>
		</a>
		
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
	/* Column for Slick grid */
	$canModified = !empty( $is_sas);
	$columns = array(
        'moveline' => array(
            'id' => 'moveline',
            'field' => 'moveline',
            'name' => '',
            'width' => 40,
            'noFilter' => 1,
            'sortable' => false,
            'resizable' => false,
            'behavior' => 'selectAndMove',
            'cssClass' => 'wd-moveline slick-cell-move-handler',
            'formatter' => 'Slick.Formatters.moveLine'
		),
		'name' => array(
			'id' => 'name',
			'field' => 'name',
			'name' => __('Name', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			// 'cssClass' => 'wd-grey-background',
		),
		'status' => array(
			'id' => 'status',
			'field' => 'status',
			'name' => __('Status', true),
			'width' => 250,
			'editor' => 'Slick.Editors.selectBox',
			'sortable' => true,
			'resizable' => true
		),
		'company_id' => array(
			'id' => 'company_id',
			'field' => 'company_id',
			'name' => __('Company', true),
			'width' => 250,
			'editor' => 'Slick.Editors.selectBox',
			'sortable' => true,
			'resizable' => true
		),
		'display' => array(
			'id' => 'display',
			'field' => 'display',
			'name' => __('Display', true),
			'width' => 120,
			'editor' => 'Slick.Editors.selectBox',
			'sortable' => true,
			'resizable' => true
		),
		'actions' => array(
			'id' => 'actions',
			'field' => 'actions',
			'name' => '',
			'width' => 40,
			'minWidth' => 40,
			'maxWidth' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.requestAction',
			'cssClass' => 'grid-action',
		),
	);
	if($isAdminSas == 0) {
		unset( $columns['company_id']);
	}
	$selectMaps = array(
		'company_id' => $company_names,
		'status' => array('IP' => __('In Progress', true), 'CL' => __('Closed', true)),
		'display' => array( 1 => __('Yes', true), 0 => __('No', true)),
	);
	$dataView = array();
	$i = 1;
	foreach ($projectStatuses as $projectStatus){
		if ($projectStatus['ProjectStatus']['company_id'] != "") {
			$company_id_save = $projectStatus['ProjectStatus']['company_id'];
			$company_name = $company_names[$company_id_save];
			if ($parent_companies[$company_id_save] != "") {
				$company_name = $company_names[$parent_companies[$company_id_save]] . " --> " . $company_name;
			}
		}
		else { $company_name = " ";}
		$row_data = array(
			'id' => $projectStatus['ProjectStatus']['id'],
			'no' => $i++,
			'name' => $projectStatus['ProjectStatus']['name'],
			'status' => ($projectStatus['ProjectStatus']['status'] == 'IP') ? 'IP' : 'CL',
            'display' => $projectStatus['ProjectStatus']['display'] ? $projectStatus['ProjectStatus']['display'] : '0',
			'company_id' => $company_id_save,
			'company_name' => $company_name,
            'moveline' => '',
		);
		$dataView[] = $row_data;
	}
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
?>
<script>
HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
var selectMaps = <?php echo json_encode($selectMaps); ?>;
var current_company = <?php echo json_encode( !empty($current_company) ? $current_company : ''); ?>;
var $this = SlickGridCustom;
function get_grid_option(){
	var _option ={
		showHeaderRow: true,
		frozenColumn: '',
		enableAddRow: true,   
		rowHeight: 40,
		forceFitColumns: true,
		topPanelHeight: 40,
		headerRowHeight: 40
	};

	if( $(window).width() > 992 ){
		return _option;
	}
	else{
		_option.frozenColumn = '';
		_option.forceFitColumns = false;
		return _option;
	}
}
function default_item(){
	var _default = {
		id : {defaulValue : 0},
		name : {defaulValue : '', allowEmpty : false},
		company_id : {defaulValue : current_company, allowEmpty : false},
		display : {defaulValue : '1', allowEmpty : false},
		status : {defaulValue : "IP", allowEmpty : false},
	};
	if( typeof ControlGrid != 'undefined'){
		var items = ControlGrid.getData().getItems();
		_default['no'] = {defaulValue : items.length + 1};
	}
	return  _default;
}
function wd_oncellchange_callback(row_item){
	$this.fields = default_item();
}
var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
(function($){
	$(function(){
		var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend($this,{
			selectMaps : selectMaps,
			canModified: true,
		});
		$.extend(Slick.Formatters,{
			requestAction : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id));
			},
            moveLine: function(row, cell, value, columnDef, dataContext){
                return _menu_svg;
            },			
		});
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
        $this.fields = default_item();
        ControlGrid.setSelectionModel(new Slick.RowSelectionModel());
        var moveRowsPlugin = new Slick.RowMoveManager({
            cancelEditOnDrag: true
        });
        moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, data) {
            for (var i = 0; i < data.rows.length; i++) {
                    // no point in moving before or after itself
                if (data.rows[i] == data.insertBefore || data.rows[i] == data.insertBefore - 1) {
                    e.stopPropagation();
                    return false;
                }
            }
        });
        //fire after row move completed
        moveRowsPlugin.onMoveRows.subscribe(function (e, args) {
            var extractedRows = [], left, right;
            var rows = args.rows;
            var insertBefore = args.insertBefore;
            left = data.slice(0, insertBefore);
            right = data.slice(insertBefore, data.length);
            rows.sort(function(a,b) { return a-b; });
            for (var i = 0; i < rows.length; i++) {
                extractedRows.push(data[rows[i]]);
            }
            rows.reverse();
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                if (row < insertBefore) {
                    left.splice(row, 1);
                } else {
                    right.splice(row - insertBefore, 1);
                }
            }
            data = left.concat(extractedRows.concat(right));

            var selectedRows = [];
            for (var i = 0; i < rows.length; i++)
                selectedRows.push(left.length + i);

            //update no.
            var orders = { data : {} };
            for(var i = 0; i < data.length; i++){
                data[i]['no.'] = (i+1);
                data[i].weight = (i+1);
                orders.data[data[i].id] = (i+1);
            }

            //ajax call
            $.ajax({
                url : '<?php echo $html->url('/project_statuses/saveOrder/') ?>',
                type : 'POST',
                data : orders,
                success : function(){
                },
                error: function(){
                    location.reload();
                }
            });
            ControlGrid.resetActiveCell();
            var dataView = ControlGrid.getDataView();
            dataView.beginUpdate();
            //if set data via grid.setData(), the DataView will get removed
            //to prevent this, use DataView.setItems()
            dataView.setItems(data);
            //dataView.setFilter(filter);
            //updateFilter();
            dataView.endUpdate();
            // dataGrid.getDataView.setData(data);
            ControlGrid.setSelectedRows(selectedRows);
            ControlGrid.render();
        });

        ControlGrid.registerPlugin(moveRowsPlugin);
        ControlGrid.onDragInit.subscribe(function (e, dd) {
            // prevent the grid from cancelling drag'n'drop by default
            e.stopImmediatePropagation();
        });
        		
		addNewStatus = function(){
			ControlGrid.gotoCell(data.length, 1, true);
		};
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
	});
})(jQuery);
 $(document).ready(function() {
	var createDialog = function(){
		$('#dialog_skip_value').dialog({
			position    :'center',
			autoOpen    : false,
			autoHeight  : true,
			modal       : true,
			create: function( event, ui ) {
				$('#dialog_skip_value').closest('.ui-dialog').addClass('wd-dialog-2019');
			},
			width       : 1120,
			maxHeight	: parseInt($(window).height() * 0.8),
			open : function(e){
				var $dialog = $(e.target);
				$dialog.dialog({open: $.noop});
			},
			title: '<?php __('All project reference');?>'
		});
		createDialog = $.noop;
	}
	 $("body").on('click', '#open-reference-popup', function(){
            var id = $(this).data('id');
			var projectRefers = [];
			$.ajax({
				url: '/project_statuses/projectStatusRefers/'+ id,
				async: false,
				dataType: 'json',
				success:function(data) {
				   projectRefers = data;
				}
			});
		
			var _html_projects = '';
			
			
			if(!$.isEmptyObject(projectRefers.projects))
			{
				if( _html_projects) _html_projects += '<hr />';
				_html_projects += '<div class="value-block">';
				_html_projects += '<div class="value-block-content"><ul>';
				$.each(projectRefers.projects, function(index, value){
					_html_projects += '<li><a class="text-value" target="_blank" href="<?php echo $html->url("/projects/your_form/") ?>'+ index + '">'+value+'</a></li>';
				});
				_html_projects += '</ul></div></div>';
			}
			$("#project-reference").html(_html_projects);
			var _html_tasks = '';
			if(!$.isEmptyObject(projectRefers.projectTasks))
			{
				if( _html_tasks) _html_tasks += '<hr />';
				_html_tasks += '<div class="value-block">';
				_html_tasks += '<div class="value-block-content"><ul>';
				for (project_id  in projectRefers.projectTasks){
					var list_tasks = projectRefers.projectTasks[project_id];
					for (task_id in list_tasks){
						var task = list_tasks[task_id];
						_html_tasks += '<li><a class="text-value" target="_blank" href="<?php echo $html->url('/project_tasks/index/') ?>' + project_id + '/?id='+ task_id +'">'+task['task_title']+'</a></li>';
					}
				}
				_html_tasks += '</ul></div></div>';
			}			
			
			
			$("#project-task-reference").html(_html_tasks);
            createDialog();
            $("#dialog_skip_value").dialog('open');
        });
 });
</script>