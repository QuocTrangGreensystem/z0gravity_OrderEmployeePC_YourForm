<?php
	echo $this->Html->css(array(
		// 'projects',
		'slick_grid/slick.grid_v2',
		'slick_grid/slick.pager',
		'slick_grid/slick.common_v2',
		'slick_grid/slick.edit',
		'preview/grid-project',
		'preview/projects',
		'preview/slickgrid',
		'preview/layout',
		'layout_2019'
	));
	echo $this->Html->script(array(
		'history_filter',
		'responsive_table',
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
		'slick_grid/slick.grid',
		'slick_grid_custom',
		'slick_grid/slick.grid.activity',
		'jquery.ui.touch-punch.min'
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
	
	$_columns = array();
	
	$_columns[] = array(
        'id' => 'request_name',
        'field' => 'request_name',
        'name' => __('Name', true),
        'width' => 250,
		'cssClass' => 'wd-grey-background',
        'sortable' => true,
        'resizable' => true,
    );
	$_columns[] = array(
        'id' => 'desc',
        'field' => 'desc',
        'name' => __('Description', true),
        'width' => 500,
        'sortable' => true,
        'resizable' => true,
    );
	if ($employee_info['Employee']['is_sas'] == 1) {
		$_columns[] = array(
			'id' => 'company',
			'field' => 'company',
			'name' => __('Company', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
		);
		$_columns[] = array(
			'id' => 'resource',
			'field' => 'resource',
			'name' => __('Resource', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.avaResource',
		);
	}
	$_columns[] = array(
        'id' => 'action',
        'field' => 'action',
        'name' => '',
        // 'name' => __('Action', true),
        'width' => 50,
        'resizable' => false,
		'noFilter' => 1,
		'formatter' => 'Slick.Formatters.requestAction',
		'cssClass' => 'grid-action',
    );
	// debug( $dataView); exit;
	$i = 1;
		
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Excute' => __('Excute', true),
	);
	$selects = array('resource' => array());
	foreach( $dataView as $key => $val){
		foreach($val['resource'] as $eID => $eName){
			$selects['resource'][$eID] = $eName;
		}
		foreach($val['company'] as $cID => $cName){
			$selects['company'][$cID] = $cName;
		}
		$dataView[$key]['resource'] = array_keys($dataView[$key]['resource']);
		$dataView[$key]['company'] = array_keys($dataView[$key]['company']);
	}
?>
<style>
.wd-list-project {
    margin-top: 0;
}
.wd-title{
	min-height: 40px;
}
.btn.btn-fullscreen:before {
    content: '' !important;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    position: absolute;
    background: url(/img/new-icon/expand.png) center center no-repeat;
}
.btn.btn-table-collapse{
	top: 0;
	right: 0;
}
.wd-title{
	position: relative;
}
.slick-headerrow-columns .multiselect-filter{
	padding-top: 0;
	margin-top: 4px;
	height: 30px;
} 
.slick-header-sortable .slick-sort-indicator{
	float: right;
	background: transparent url('/img/new-icon/sort_able.png') center no-repeat;
	height: 100%;
	margin: 0 3px;
}
.slick-header-sortable .slick-sort-indicator.slick-sort-indicator-desc{
	background-image: url('/img/new-icon/sort_able_desc.png');
}
.slick-header-sortable .slick-sort-indicator.slick-sort-indicator-asc{
	background-image: url('/img/new-icon/sort_able_asc.png');
}
#wd-container-main {
    background-color: inherit;
}
.wd-table-2019 .slick-headerrow .slick-headerrow-column input.input-filter, .wd-table-2019 .slick-headerrow .slick-headerrow-column select, .wd-table-2019 .slick-headerrow .slick-headerrow-column a.multiSelect{
	background: transparent;
}

</style>
<div id="wd-container-main" class="wd-project-admin">
	<div class="wd-layout">
		<div class="wd-main-content">
		<?php echo $this->element("project_top_menu") ?>
			<div class="wd-tab"><div class="wd-panel">
				<div class="wd-list-project">
					<div class="wd-title">
						<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
						<a href="javascript:void(0);" class="btn btn-fullscreen hide-on-mobile" id="table-expand" onclick="expandTable();" title="<?php __("Expand"); ?>"></a>
						<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="<?php __('Collapse table') ?>" style="display: none;"></a>
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
			</div></div>
		</div>
	</div>
</div>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	var wdTable = $('.wd-table');
	var $this = SlickGridCustom;
	var updateLink = '';
	var dataGrid;
	var selects = <?php echo json_encode($selects); ?>;
	var excuteLink = <?php echo json_encode($this->Html->url(array('action' => 'viewReport', '%ID' ))); ?>;
	(function($){
		$.extend($this,{
			i18n : <?php echo json_encode($i18n); ?>,
			selectMaps : selects,
			canModified: true,
			// Phần này admin không có quyền edit nhưng mà trong slick_grid_custom.js remove column cuối nếu không có quyền canModified
		});
		$.extend(Slick.Formatters,{
			requestAction: function(row, cell, value, columnDef, dataContext){
				return '<div class="wd-actions"><a href="' + excuteLink.replace('%ID', dataContext.id) + '" class="wd-dashboard" title="' + $this.t('Excute') + '" target="_blank"></a></div>';
			},
			avaResource: function (row, cell, value, columnDef, dataContext) {
                var avatar = '<div class="list-avatars">';
                // avatar = '';
                $.each(value, function (i, val) {
                    avatar += '<span class="circle-name" title="' + $this.selectMaps[columnDef.id][val] + '" data-id="' + val + '"><img alt="avatar" src="' + employeeAvatar_link.replace( '%ID%', val) + '" /></span>'
                });
				avatar += '</div>';
				return avatar;
            },
		});
		var  data = <?php echo json_encode($dataView); ?>;
		// var  data = '';
		var columns = <?php echo jsonParseOptions($_columns, array('editor', 'formatter', 'validator')); ?>;
		
		dataGrid = $this.init($('#project_container'),data,columns, {
			enableCellNavigation: false,
			enableColumnReorder: false,
			showHeaderRow: true,
			editable: false,
			enableAddRow: false,
			headerRowHeight: 40,
			rowHeight: 40,
		});
		var dataView = dataGrid.getDataView();
		$(window).on('resize', function(){
			dataGrid.resizeCanvas();
		});
	})(jQuery);
	function postExcute(field, value) {
		//post code excute with field "requireID" for idSql and "requireSql" for sql code
		// add new "viewIframe", "viewIframeText"
		if( field == 'viewIframeText' || field == 'requireSql'){
			value = value.replace(/\n/g, ' ');
			// value = value.replace(/\'/g, '\"');
			console.log( value);
			var data = {};
			data[field] = value;
			$.ajax({
				type: "POST",
				url: '/reports/excutesql',
				data: data,
				dataType: 'html',
				success: function(data){
					// console.log( data);
					$('#result_sql').empty().append( $(data));
					resultDialog.dialog('open');
				},
			});
		}else{
			
			var form = $('<form id="sql_submit" action="/reports/excutesql" method="post">' +
					'<input type="hidden" name="' + field + '" value=\'' + value + '\' />' +
					'</form>');
			$('body').append(form);
			$(form).submit();
		}
	}
	resetFilter = function () {
		$('.input-filter').val('').trigger('change');
		$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
		dataGrid.setSortColumn();
		$('input[name="project_container.SortOrder"]').val('').trigger('change');
		$('input[name="project_container.SortColumn"]').val('').trigger('change');

	}
	function reGrid(){
		dataGrid = $this.getInstance();
		dataGrid.resizeCanvas();
	}
	expandTable = function(){
		$('#wd-container-main').addClass('fullScreen');
		$('#table-collapse').show();
		$('#table-expand').hide();
		$(window).trigger('resize');
	}
	collapse_table = function(){
		$('#wd-container-main').removeClass('fullScreen');
		$('#table-collapse').hide();
		$('#table-expand').show();
		$(window).trigger('resize');
	}
	function set_table_height(){
		if ( !wdTable.length ) return;
		var heightTable = $(window).height() - wdTable.offset().top - 80;
		heightTable = heightTable > 300 ? heightTable : 300;
		wdTable.css({
			height: heightTable,
		});
	}
	$(document).on('ready', function(){
		set_table_height();
		reGrid();
	});
	$(window).on('resize', function(){
		set_table_height();
		reGrid();
	});
	set_table_height();
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
</script>