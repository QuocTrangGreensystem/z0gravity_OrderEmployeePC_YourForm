<?php 
	echo $this->Html->css(array(
		'slick_grid/slick.grid_v2',
		'slick_grid/slick.pager',
		'slick_grid/slick.common_v2',
		'slick_grid/slick.edit',
		'preview/grid-project',
		'preview/slickgrid',
		'preview/tab-admin',
		'layout_admin_2019'
	));
	echo $this->Html->script(array(
		'responsive_table',
		'history_filter',
		'slick_grid/lib/jquery-ui-1.8.16.custom.min',
		'slick_grid/lib/jquery.event.drop-2.0.min',
		'slick_grid/lib/jquery.event.drag-2.2',
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
		'jquery.ui.touch-punch.min',
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
	$canModified = true;
	$_columns = array(
		'selected' => array(
			'id' => 'selected',
			'field' => 'selected',
			'name' => '<div class="checker"><span class=""></span></div></div></div>',
			'name' => '',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'formatter' => 'Slick.Formatters.rowSelected',
			'noFilter' => true,
			'cssClass' => 'grid-action no-padding',
		),
		'project_name' => array(
			'id' => 'project_name',
			'field' => 'project_name',
			'name' => __('Project name', true),
			// 'cssClass' => 'wd-grey-background',
			'width' => 500,
			'sortable' => true,
			'resizable' => true,
		),
		'start_date' => array(
			'id' => 'start_date',
			'field' => 'start_date',
			'name' => __('Start date', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'datatype' => 'datetime'
		),
		'end_date' => array(
			'id' => 'end_date',
			'field' => 'end_date',
			'name' => __('End date', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'datatype' => 'datetime'
		),
		'estimated' => array(
			'id' => 'estimated',
			'field' => 'estimated',
			'name' => __('Workload', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
		),
		'consumed' => array(
			'id' => 'consumed',
			'field' => 'consumed',
			'name' => __('Consumed', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
		),
		'deleted' => array(
			'id' => 'deleted',
			'field' => 'deleted',
			'name' => '',
			'width' => 42,
			'sortable' => false,
			'resizable' => false,
			'formatter' => 'Slick.Formatters.requestAction',
			'cssClass' => 'grid-action no-padding',
		),
	);
	$i = 1;
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Deltete' => __('Deltete', true),
		'Select all' => __('Select all', true),
		'Unselect all' => __('Unselect all', true),
		'delete_project' => __('Do you want to delete project %s</br><strong>%s</strong>', true),
	);
	$dataView = array();
	foreach( $project_archived as $key => $data){
		$data['start_date'] = date('d-m-Y', strtotime($data['start_date']));
		$data['end_date'] = date('d-m-Y', strtotime($data['end_date']));
		$data['selected'] = 0;
		$data['MetaData'] = array(
			'cssClasses' =>  'unselected',
		);
		$data['deleted'] = false;
		// $data['deleted'] = rand(0,1); // for tester
		$dataView[] = $data;
	}
	$selects = array('selected' => array(
		0 => 'Unselected',
		1 => 'Selected',
	));
	$svg_icon = array(
		'trash' => '<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 40 40"><g transform="translate(-1627.041 -485.28)"><rect class="a" width="40" height="40" transform="translate(1627.041 485.28)"/><path class="trash" d="M3.6,16a1.483,1.483,0,0,1-1.426-1.532V2.542H.391A.406.406,0,0,1,0,2.122.406.406,0,0,1,.391,1.7H4V.976A.946.946,0,0,1,4.911,0h6.045a.946.946,0,0,1,.909.976V1.7h3.744a.407.407,0,0,1,.391.42.407.407,0,0,1-.391.42H13.693V14.468A1.483,1.483,0,0,1,12.267,16Zm-.644-1.532a.671.671,0,0,0,.644.693h8.667a.671.671,0,0,0,.644-.693V2.542H2.956ZM4.785.976V1.7h6.3V.976A.132.132,0,0,0,10.956.84H4.911A.132.132,0,0,0,4.785.976Zm5.458,11.993V5.261a.4.4,0,1,1,.791,0v7.708a.4.4,0,1,1-.791,0Zm-2.69,0V5.261a.4.4,0,1,1,.791,0v7.708a.4.4,0,1,1-.791,0Zm-2.71,0V5.261a.4.4,0,1,1,.79,0v7.708a.4.4,0,1,1-.79,0Z" transform="translate(1639.042 497.28)"/></g></svg>',
		'ok' => '<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="40" height="40" transform="translate(317 66)"/><path class="ok" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
	);
?>
<style>
	svg .a{fill:none;} svg .ok{fill:#6EAF79;stroke:rgba(0,0,0,0);} svg .trash{fill:#f05352;stroke:rgba(0,0,0,0);}
	.slick-viewport .slick-row .slick-cell.no-padding{
		padding: 0;
	}
	.row-actions{
		display: block;
		text-align: center;
	}
	.row-actions .row-action{
		height: 38px;
		width: 40px;
		display: inline-block;
		line-height: 38px;
	}
	/* Checkbox */
		div.checker{
			width: 20px;
			height: 20px;
			line-height: 20px;
			display: inline;
		}
		div.checker span{
			width: 18px;
			height: 18px;
			border: 1px solid #E1E6E8;
			background: #fff;
			border-radius: 2px;
			display: inline-block;
			position: relative;
			cursor: pointer;
		}
		div.checker span:before {
			content: '';
			width: 12px;
			height: 12px;
			background: white;
			border-radius: 2px;
			display: block;
			top: 2px;
			left: 2px;
			top: 3px;
			left: 3px;
			position: relative;			
		}
		div.checker:hover span,
		div.checker span.checked{
			box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
		}
		div.checker span.checked:before{
			background-color: #5584FF;
		}
		div.checker span.disabled{
			background-color: #efefef;
		}
		div.checker span.checked.disabled:before{
			background-color: #d9d9d9;
		}
	/* END Checkbox */
	.wd-layout .wd-tab .wd-panel{
		border: none;
		padding: 40px;
		background: #fff;
	}
	.wd-table .slick-viewport .slick-row.selected{
		background: #DFE8F6;
	}
	#wd-container-main .wd-title .btn#table-expand:before{
		content: '' !important;
		width: 100%;
		height: 100%;
		left: 0;
		top:0;
		position: absolute;
		background: url(/img/new-icon/expand.png) center center no-repeat; 
	}
	#wd-container-main .wd-title .btn:hover{
		background-color: #247FC3;
		color: #fff;
	}
	.delete-btn{
		cursor: pointer;
	}
	
	/* dialog */
		.wd-submit{
			padding-left: 10px;
			padding-right: 10px;
			margin-top: 15px;
		}
		.wd-submit .btn-form-action {
			font-size: 14px;
			line-height: 22px;
			font-weight: 600;
			text-transform: uppercase;
			color: #fff;
			border: none;
			min-width: 120px;
			max-width: 250px;
			overflow: hidden;
			white-space: no-wrap;
			text-overflow: ellipsis;
			height: 40px;
			line-height: 40px;
			padding: 0;
			background-color: #C6CCCF;
			transition: all 0.3s ease;
			border-radius: 3px;
			text-decoration: none;
			background-size: 250%;
			background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#217FC2 64%,#217FC2 100%);
			display: inline-block;
			text-align: center;
			font-size: 14px;
			font-weight: 400;
			cursor: pointer;
			text-align: center;
		}

		.wd-submit .btn-form-action.btn-ok {
			background: #217FC2;
			float: right;
		}
		.wd-submit .btn-form-action:hover{
			background-color: #217FC2;
			background-position: right center;
		}
		.wd-dialog-2019 .ui-dialog-buttonset{
			text-align: left;
		}
	/* END dialog */
	.wd-title .btn.btn-right-big{
		background: #6EAF79;
		float: right;
		width: auto;
		color: white;
		font-size: 13px;
		font-weight: 600;
		border-radius: 3px;
		line-height: 40px;
		border: none;
		padding: 0 10px;
	}
	.row-actions .loading img{
		vertical-align: middle;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-tab"> 
                <div class="wd-panel">
					<div class="wd-list-project">
					<div class="wd-title">
						<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
						<a href="javascript:void(0);" class="btn btn-fullscreen hide-on-mobile" id="table-expand" onclick="expandTable();" title="<?php __("Expand"); ?>"></a>
						<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="<?php __('Collapse table') ?>" style="display: none;"></a>
						<a href="javascript:void(0);" class="btn btn-delete-big btn-right-big" id="btn-delete-project" style="display: none;"><?php __('Delete');?></a>
					</div>
					<div id="message-place">
						<?php
						App::import("vendor", "str_utility");
						$str_utility = new str_utility();
						echo $this->Session->flash();
						?>
					</div>
					<div class="wd-table-container" style="width:100%;">
						<div class="wd-table wd-table-2019" id="project_container" style="width:100%; height: 400px;">
						</div>
					</div>				
				</div>
				</div> 
			</div> 
		</div> 
	</div> 
</div> 

<script type="text/javascript">
	HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	var wdTable = $('.wd-table');
	var $this = SlickGridCustom;
	var updateLink = '';
	var ControlGrid;
	var selects = <?php echo json_encode($selects); ?>;
	var canModified = <?php echo json_encode($canModified); ?>;
	var svg_icon = <?php echo json_encode($svg_icon); ?>;
	(function($){
		$.extend($this,{
			i18n : <?php echo json_encode($i18n); ?>,
			selectMaps : selects,
			canModified: canModified,
			delete_link: "<?php echo $this->Html->url(array('action' => 'delete', '%ID%')); ?>",
		});
		$.extend(Slick.Formatters,{
			rowSelected: function(row, cell, value, columnDef, dataContext){
				var is_checked = value ? 'checked' : 'uncheck';
				if( dataContext.deleted){
					is_checked = 'checked disabled';
				}
				var _html = '<div class="row-actions"><div class="row-action"><div class="checker"><span class="' + is_checked + '"></span></div></div></div>';
				return _html;
			},
			requestAction: function (row, cell, value, columnDef, dataContext) {
				var _html = '<div class="row-actions">';
                if(value == 'loading'){
					_html += '<span class="loading"></span>';
				}else if( value){
					return '<p>' + svg_icon['ok'] + '</p>';
				}else{
					_html += '<p class="delete-btn" title="' + $this.t('Delete') + '">' + svg_icon['trash'] + '</p>';
					
				}
				_html += '</div>';
				return _html;
            },
		});
		var  data = <?php echo json_encode($dataView); ?>;
		var columns = <?php echo jsonParseOptions($_columns, array('editor', 'formatter', 'validator')); ?>;
		ControlGrid = $this.init($('#project_container'),data,columns, {
			enableCellNavigation: false,
			enableColumnReorder: false,
			showHeaderRow: true,
			editable: false,
			enableAddRow: false,
			headerRowHeight: 40,
			rowHeight: 40,
		});
		
		$(window).on('resize', function(){
			ControlGrid.resizeCanvas();
		});
		ControlGrid.onClick.subscribe(function(e, args) {
			// console.log( args);
			var _columns = args.grid.getColumns();
			var _field = _columns[args.cell]['field'];
			
			if(_field == "selected"){ // Checkbox select
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
				var _selected = get_list_selected();
				if( _selected.length) {
					if($('#btn-delete-project').is(':hidden')) $('#btn-delete-project').fadeIn();
				}
				else $('#btn-delete-project').fadeOut();				
			}
			
			if(_field == "deleted"){
				var item = args.grid.getData().getItem(args.row);
				if( item.deleted){ return;}
				// HUYNH Ajax delete here 
				var p_id = item.id;
				var p_name = item.project_name;
				// var title = $this.t('Delete');
				// var content = $this.t('delete_project', p_id, p_name);
				// var buttonModel = 'WD_TWO_BUTTON';
				wdConfirmIt({
					title: $this.t('Delete'),
					content: $this.t('delete_project', p_id, p_name),
					buttonModel: 'WD_TWO_BUTTON',
					buttonText: [
						'<?php __('Yes');?>',
						'<?php __('No');?>'
					],
				}, function(){
					delete_project(p_id);
				}, function(){
					console.log('cancel');
				});
				
				
				
			}
		});
		ControlGrid.onHeaderClick.subscribe(function(e, args) {
			console.log( args);
			var _field = args.column.field;
			if(_field == "selected"){ 
				// Checkbox select
				var grid = args.grid;
				var items = args.grid.getData().getItems();
				var has_uncheck = false;
				$.each( items, function( i, item){
					if( item.deleted){ return;}
					has_uncheck = has_uncheck || (!item.selected);
				});
				var is_selected = has_uncheck ? 1 : 0; // Neu co o nao chua check thi check all
				$.each( items, function( i, item){
					item.selected = is_selected;
					if(is_selected){
						item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/unselected/g, 'selected');
					}else{
						item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/selected/g, 'unselected');
					}
				});
				grid.invalidate();
				grid.render();
				var _selected = get_list_selected();
				if( _selected.length) {
					if($('#btn-delete-project').is(':hidden')) $('#btn-delete-project').fadeIn();
				}
				else $('#btn-delete-project').fadeOut();					
			}
			
		});
		function get_list_selected(){
			var items = ControlGrid.getData().getItems();
			var _selected = [];
			$.each(items, function( i, v){
				if( v.selected && !v.deleted) _selected.push(v.id);
			});
			return _selected;
		}
		resetFilter = function () {
			$('.input-filter').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			dataGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');

		}
		expandTable = function(){
			$('#wd-container-main').addClass('fullScreen');
			$('#table-collapse').show();
			$('#table-expand').hide();
			$('ul.wd-item').hide();
			$(window).trigger('resize');
		}
		collapse_table = function(){
			$('#wd-container-main').removeClass('fullScreen');
			$('#table-collapse').hide();
			$('#table-expand').show();
			$('ul.wd-item').show();
			$(window).trigger('resize');
		}
		history_reset = function () {
			var check = false;
			$('.multiselect-filter').each(function (val, ind) {
				var text = '';
				if ($(ind).find('input').length != 0) {
					text = $(ind).find('input').val();
				} else {
					text = $(ind).find('span').html();
					if (text == "<?php __('-- Any --');?>" || text == '-- Any --') {
						text = '';

					}
				}
				if (text != '') {
					$(ind).css('border', 'solid 1px #E9E9E9');
					check = true;
				} else {
					$(ind).css('border', 'none');
				}
			});
			if (!check) {
				$('#reset-filter').addClass('hidden');
			} else {
				$('#reset-filter').removeClass('hidden');
			}
		}
		function delete_project(p_id){
			var item = ControlGrid.getData().getItemById(p_id);
			$.ajax({
				url : '/project_utilities/delete_projects/' + p_id,
				type: 'POST',
				// async: false,
				beforeSend: function(){
					item.deleted = 'loading';
					$this.update_after_edit(item.id, item);	
				},
				success: function(res){
					if( res == 'success'){
						item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/selected/g, 'unselected');
						item.selected = 0;
						item.deleted = 1;
						$this.update_after_edit(item.id, item);
					}
				},
				
			});	
				
				
		}
		$(window).ready(function(){
			ControlGrid.resizeCanvas();
			$('#btn-delete-project').on('click', function(){
				var _selected = get_list_selected();
				
				if(_selected.length > 0){
					var i = 0;
					var len = _selected.length;
					function delete_batch(p_id){
						var item = ControlGrid.getData().getItemById(p_id);
						$.ajax({
							url : '/project_utilities/delete_projects/' + p_id,
							type: 'POST',
							// async: false,
							beforeSend: function(){
								item.deleted = 'loading';
								$this.update_after_edit(item.id, item);	
							},
							success: function(res){
								if( res == 'success'){
									item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/selected/g, 'unselected');
									item.selected = 0;
									item.deleted = 1;
									$this.update_after_edit(item.id, item);
								}
								i++;
								if ( i<len){
									delete_batch(_selected[i]);
								}
							},
							
						});
					}
					delete_batch(_selected[i]);					
				}
			});
		});
	})(jQuery);
		
</script>