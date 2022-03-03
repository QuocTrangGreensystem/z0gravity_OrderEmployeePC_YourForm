<?php
// debug($projectName);
// debug($articles);
// exit;
	App::import("vendor", "str_utility");
	$str_utility = new str_utility();
	echo $this->Html->css(array(
		'slick_grid/slick.grid.activity',
		'jquery.multiSelect',
		'slick_grid/slick.grid_v2',
		'slick_grid/slick.pager',
		'slick_grid/slick.common_v2',
		'slick_grid/slick.edit',
		'preview/layout',
		'layout_2019',
		'preview/datepicker-new',
		'project_article',
	));
	echo $this->Html->script(array(
		'history_filter',
		// 'responsive_table',
		'slick_grid/lib/jquery-ui-1.8.16.custom.min',
		'slick_grid/lib/jquery.event.drop-2.0.min',
		'slick_grid/lib/jquery.event.drag-2.2',
		'slick_grid/slick.core',
		'slick_grid/slick.dataview',
		// 'slick_grid/controls/slick.pager',
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
		// 'jquery.ui.touch-punch.min',
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
	$canModified = true;
	// $canModified = false;
	$_columns = array(
		'no.' => array(
			'id' => 'no.',
			'field' => 'no.',
			'name' => '',
			'width' => 40,
			'noFilter' => 1,
			'sortable' => false,
			'resizable' => false,
			'minWidth' => 40,
			'maxWidth' => 40,
		),
		'article_title' => array(
			'id' => 'article_title',
			'field' => 'article_title',
			'name' => __('Title', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'start_date' => array(
			'id' => 'start_date',
			'field' => 'start_date',
			'name' => __('Start date', true),
			'width' => 150,
			'minWidth' => 150,
			'maxWidth' => 150,
			'sortable' => true,
			'resizable' => false,
			'editor' => 'Slick.Editors.datePicker',
			'cssClass' => "wd-slick-date",
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		'end_date' => array(
			'id' => 'end_date',
			'field' => 'end_date',
			'name' => __('End date', true),
			'width' => 150,
			'minWidth' => 150,
			'maxWidth' => 150,
			'sortable' => true,
			'resizable' => false,
			'editor' => 'Slick.Editors.datePicker',
			'cssClass' => "wd-slick-date",
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		'status' => array(
			'id' => 'status',
			'field' => 'status',
			'name' => __('Status', true),
			'width' => 100,
			'minWidth' => 100,
			'maxWidth' => 120,
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-article-validate slick-validate',
			'formatter' => 'Slick.Formatters.validateSwitch'
		),
		'public_date' => array(
			'id' => 'public_date',
			'field' => 'public_date',
			'name' => __('Public date', true),
			'width' => 150,
			'minWidth' => 150,
			'maxWidth' => 150,
			'sortable' => true,
			'resizable' => false,
			'editor' => 'Slick.Editors.datePicker',
			'cssClass' => "wd-slick-date",
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		'publisher' => array(
			'id' => 'publisher',
			'field' => 'publisher',
			'name' => __('Publisher', true),
			'width' => 150,
			'minWidth' => 150,
			'maxWidth' => 350,
			'sortable' => true,
			'resizable' => false,
			'editor' => 'Slick.Editors.textBox',
			'cssClass' => 'wd-grey-background',
		),
		'actions' => array(
			'id' => 'actions',
			'field' => 'actions',
			'name' => '',
			'width' => 121,
			'minWidth' => 121,
			'maxWidth' => 121,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.requestAction',
			'cssClass' => 'grid-action',
		),
		'placeholder' => array()
	);
	if( $canModified) unset( $_columns['placeholder']);
	$i = 1;
	$selectMaps = array(
		'status' => array(
			__('Closed', true),
			__('In Progress', true),
		),
	);
	$dataView = array();
	$i = 1;
	$datetime_colum = array();
	foreach($_columns as $column){
		if( isset($column['datatype']) && $column['datatype'] == 'datetime')
			$datetime_colum[] = $column['field'];
	}
	foreach ($articles as $article){
		$class = null;
		$row = $article['ProjectArticle'];
		$row['no.'] = $i++;
		if( empty($row['article_title'])) $row['article_title'] = $projectName['Project']['project_name'];
		if( empty($row['publisher'])) $row['publisher'] = $article['Employee']['fullname'];
		foreach($row as $col => $cel){
			if($cel && in_array($col,$datetime_colum)){
				$row[$col] = date('d-m-Y', strtotime($cel));
			}
		}
		$dataView[] = $row;
	}
	$i18n = array(
		'Delete' => __('Delete', true),
		'Edit' => __('Edit', true),
		'View' => __('View', true),
		'Copied' => __('Copied', true),
	);
	$icons = array(
		'edit' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
			<path id="EDIT" class="svg-b" d="M6593.86,260.788c-0.75.767-11.93,11.826-12.4,12.3a0.7,0.7,0,0,1-.27.157c-0.75.224-5.33,1.7-5.37,1.719a0.693,0.693,0,0,1-.2.03,0.625,0.625,0,0,1-.44-0.184,0.636,0.636,0,0,1-.16-0.642c0.01-.044,1.37-4.478,1.64-5.477a0.627,0.627,0,0,1,.16-0.285s11.99-12,12.34-12.343a3.636,3.636,0,0,1,2.42-1.056,3.186,3.186,0,0,1,2.23.981,3.347,3.347,0,0,1,1.18,2.356A3.455,3.455,0,0,1,6593.86,260.788Zm-17.36,12.665c1.21-.39,3.11-1.045,3.97-1.322a3.9,3.9,0,0,0-.94-1.565,4.037,4.037,0,0,0-1.78-1.087C6577.46,270.444,6576.85,272.274,6576.5,273.453Zm3.92-3.789a4.95,4.95,0,0,1,1.07,1.6c2.23-2.2,7.88-7.791,10.33-10.231a3.894,3.894,0,0,0-1.02-1.875,3.944,3.944,0,0,0-1.84-1.1c-2.41,2.409-8.16,8.167-10.37,10.372A5.418,5.418,0,0,1,6580.42,269.664Zm12.51-12.755a1.953,1.953,0,0,0-1.35-.63,2.415,2.415,0,0,0-1.53.69c-0.01.011-.06,0.055-0.09,0.09a5.419,5.419,0,0,1,1.73,1.194,5.035,5.035,0,0,1,1.14,1.763,1.343,1.343,0,0,0,.12-0.119,2.311,2.311,0,0,0,.78-1.534A2.168,2.168,0,0,0,6592.93,256.909Z" transform="translate(-6575 -255)"/>
		</svg>',
		'grid' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" style="top:5px;position:relative;"><path id="grid" data-name="Mode Thumbs" class="svg-b" d="M361,40V31h9v9h-9Zm8-8h-7v7h7V32Zm-8-12h9v9h-9V20ZM350,31h9v9h-9V31Zm0-11h9v9h-9V20Z" transform="translate(-350 -20)"></path></svg>',
		'embed' => '
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" style="top:5px; position:relative;">
			  <rect id="Canvas" width="18" height="18" fill="#ff13dc" opacity="0"/>
			  <path class="svg-b" d="M8.226,14.473a.5.5,0,0,1-.483.371H7.227a.5.5,0,0,1-.472-.666L9.786,2.693a.5.5,0,0,1,.483-.37h.5a.5.5,0,0,1,.472.665Z" fill="#707070"/>
			  <path class="svg-b" d="M17.746,9.53l-4.095,4.16a.5.5,0,0,1-.713,0l-.446-.453a.5.5,0,0,1,0-.7L15.971,9,12.492,5.464a.5.5,0,0,1,0-.7l.446-.454a.5.5,0,0,1,.713,0l4.095,4.16A.76.76,0,0,1,17.746,9.53Z" fill="#707070"/>
			  <path  class="svg-b" d="M.254,8.47l4.1-4.161a.5.5,0,0,1,.713,0l.446.454a.5.5,0,0,1,0,.7L2.029,9l3.479,3.535a.5.5,0,0,1,0,.7l-.446.453a.5.5,0,0,1-.713,0L.254,9.53a.76.76,0,0,1,0-1.06Z" fill="#707070"/>
			</svg>'
	);
?>

<style>
	.wd-title .btn.btn-table-collapse{
		position: relative;
		float: right;
		top: 0;
		right: 0;
	}
	svg .svg-b{
		fill: #666;
		transition: 0.1s ease;
	}
	.wd-actions svg{
		width: 14px;
		height: 14px;
		padding: 13px;
	}
	.wd-actions .wd-btn{
		float:left;
	}
	.btn.add-field {
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
	.wd-table .wd-actions a{
		color: #7b7b7b;
	}
	.wd-table .wd-actions a:hover{
		color: #fff;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="wd-list-project">
	<div class="wd-title">
		<a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable(this);" title="<?php __('Expand');?>"></a>
		<a href="<?php echo $this->Html->url(array('controller' => 'project_articles','action' => 'communication', $project_id)); ?>" id="grid-icon" class="btn btn-grid" title="<?php echo __('Grid', true)?>"><?php echo $icons['grid']?></a>
		<a href="javascript:void(0);" id="copy-embed-icon" class="btn btn-embed" title="<?php echo __('Embed code', true)?>"><?php echo $icons['embed']?></a>
		<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
		<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table(this);" title="<?php __('Collapse table');?>" style="display: none;"></a>
	</div>
	<div id="message-place">
		<?php
		echo $this->Session->flash();
		?>
	</div>
	<!-- <p><?php //__('In the column #, drag and drop to reorder') ?></p> -->
	<div class="wd-table-container">
		<a href="javascript:void(0);" class="btn add-field" id="add_request" style="margin-right:5px;" title="Add an item" onclick="addNewItem();" ></a>
		<div class="wd-table wd-table-2019" id="project_container">
		</div>
	</div>	

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
var embed_code = '<?php echo '<iframe src="'. $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'communication', $project_id)) .'" width="1280" height="540" frameborder="0" allowfullscreen></iframe>';?>';
var i18n = <?php echo json_encode($i18n); ?>;
(function($){
	var selectMaps = <?php echo json_encode($selectMaps); ?>;
	var canModified = <?php echo json_encode($canModified); ?>;
	var icons = <?php echo json_encode($icons); ?>;
	var project_id = <?php echo json_encode($project_id); ?>;
	// var canModified = true;
	var $this = SlickGridCustom;
	$.extend($this,{
		i18n : <?php echo json_encode($i18n); ?>,
		selectMaps : selectMaps,
		canModified: canModified,
		delete_link: "<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'delete', '%ID%')); ?>",
		view_link: "<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'view', '%ID%')); ?>",
		edit_link: "<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'edit', '%ID%')); ?>",
		onCellChange: function(args){
			return true;
		},
		url: "<?php echo $html->url(array('controller' => $this->params['controller'],'action' => 'update')); ?>",
	});
	$this.fields = {
		id : {defaulValue : 0},
		article_title : {defaulValue : '', allowEmpty : false},
		start_date : {defaulValue : '', allowEmpty : true},
		end_date : {defaulValue : '', allowEmpty : false},
		status : {defaulValue : 0, allowEmpty : true},
		public_date : {defaulValue : '', allowEmpty : false},
		publisher : {defaulValue : '', allowEmpty : false},
		project_id : {defaulValue : project_id, allowEmpty : false},
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
	resetFilter = function () {
		$('.input-filter').val('').trigger('change');
		$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
		dataGrid.setSortColumn();
		$('input[name="project_container.SortOrder"]').val('').trigger('change');
		$('input[name="project_container.SortColumn"]').val('').trigger('change');
	}
	$.extend(Slick.Formatters,{
		requestAction: function(row, cell, value, columnDef, dataContext){
			var _html = '<div class="wd-actions clearfix">';
			_html += '<a class="wd-btn btn-view" title="' + $this.t('View') + '" href="' + $this.view_link.replace('%ID%' , dataContext.id) +'"><i class="icon-eye"></i></a>';			
			_html += '<a class="wd-btn btn-edit" title="' + $this.t('Edit') + '" href="' + $this.edit_link.replace('%ID%' , dataContext.id) +'">' + icons['edit'] + '</a>';			
			_html += '<a class="wd-btn btn-delete" title="' + $this.t('Delete') + '" href="' + $this.delete_link.replace('%ID%' , dataContext.id) +'" onclick="return confirm(\'' + $this.t('Delete?') + '\');"></a>';			
			_html += '</div>';
			return _html;
		},
		validateSwitch: function(row, cell, value, columnDef, dataContext){
			// return '';
			// console.log(value, dataContext);
			// console.log(value);
			if( $this.isExporting){
				return $this.t(value);
			}
			active_class = (value == '1') ? 'validated' : '';
			loading_class = (dataContext.status_loading == '1') ? ' loading' : '';
			return '<a data-itemid="' + dataContext.id + '" class = "wd-switch '+ active_class + loading_class +'" title = ""><input type="hidden" name="activated" data-value="'+ value +'" data-id = ""></a>';
		},
	});
	var  data = <?php echo json_encode($dataView); ?>;
	var columns = <?php echo jsonParseOptions(array_values($_columns), array('editor', 'formatter', 'validator')); ?>;
	var slick_options = {
		showHeaderRow: true,
		enableAddRow: true,
		headerRowHeight: 40,
		rowHeight: 40,
	}
	dataGrid = $this.init($('#project_container'),data,columns, slick_options);
	var dataView = dataGrid.getDataView();
	dataGrid.onClick.subscribe(function(e, args){
		// console.log( args);
		var grid = args.grid;
		var columns = grid.getColumns();
		var _row = args['row'];
		var _cell = args['cell'];
		column = columns[_cell];
		if( column.id == 'status'){
			var item = grid.getData().getItem(_row);
			// console.log( item.status, typeof item.status);
			var new_status = (parseInt(item.status) == 0) ? '1' : '0';
			item.status = new_status;
			console.log( item, grid.getData().getItems());
			grid.eval('trigger(self.onCellChange, {row: ' + _row + ',cell: ' + _cell + ',item: ' + JSON.stringify(item) + '});');
		}
	});
	addNewItem = function(){
		location.href= '<?php echo $this->Html->url( array(
			'controller' => $this->params['controller'],
			'action' => 'add',
			$project_id
		));?>';
		// dataGrid.gotoCell(data.length, 1, true);
	};
})(jQuery);
expandTable = function(elm){
	$(elm).closest('.wd-layout').addClass('fullScreen');
	$(elm).closest('.wd-list-project').find('.btn-table-collapse').show();
	$(elm).closest('.wd-list-project').find('.btn-expand').hide(); 
	$(window).trigger('resize');
}

collapse_table = function(elm){
	$(elm).closest('.wd-layout').removeClass('fullScreen');
	$(elm).closest('.wd-list-project').find('.btn-table-collapse').hide();
	$(elm).closest('.wd-list-project').find('.btn-expand').show(); 
	$(window).trigger('resize');
}
history_reset = function(){
	var check = false;
	$('.slick-header-sortable').each(function(val, ind){
		var text = '';
		if(($('.slick-header-column-sorted').length != 0)||($('.slick-sort-indicator-asc').length != 0)||($('.slick-sort-indicator-desc').length != 0)){
			text = '1';
		}
		if( text != '' ){
			check = true;
		} else {
		}
	});
	if(!check){
		$('#reset-filter').addClass('hidden');
	} else {
		$('#reset-filter').removeClass('hidden');
	}
}
if( Clipboard.isSupported()){
	var copied_timeout=0;
	$('#copy-embed-icon').on('click', function(){
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(embed_code).select();
		$temp[0].setSelectionRange(0, 99999);
		document.execCommand("copy"); /*For mobile devices*/
		$temp.remove();
		var tmp = '<div id="flashMessage" class="message success">' + i18n['Copied'] + '<a href="#" class="close">x</a></div>';
		$('#message-place').html(tmp);
		// $('.embed-copied').show();
		clearTimeout(copied_timeout);
		copied_timeout = setTimeout(function(){
			$('message-place').find('#flashMessage').fadeOut();
		}, 3000)
	});
}else{
	$('.copy-embed-icon').hide();
}
</script>
