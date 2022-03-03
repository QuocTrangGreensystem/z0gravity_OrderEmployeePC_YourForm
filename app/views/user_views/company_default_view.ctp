<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
	'projects',
    'slick_grid/slick.grid_v2',
	'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
	'preview/tab-admin',
	'layout_admin_2019'
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
echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
	.multiSelect {
		width: 323px !important;
	}
	.multiSelect span{
		width: 317px !important;
	}
	body{
		overflow: hidden;
	}
	.slick-cell {
		padding-left:4px;
		padding-right:4px;
		line-height: 38px;
	}
	.wd-bt-switch{
		overflow: hidden;
		margin: 8px;
		margin-right: 5px;
		float: none;
	}
	.slick-sort-indicator{
		display:inline-block;
		width:8px;
		height:5px;
		margin-left:4px
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
	#sub-nav {
		display:none;
	}
	div#project_container{
		margin-top: 20px;
	}
	/*
	.slick-viewport {
		height: auto !important;
		min-height: 450px;
	}
	*/
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;">
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
$employeeInfo = $this->Session->read('Auth.employee_info');
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
$columns = array(
	array(
		'id' => 'no.',
		'field' => 'no.',
		'name' => '',
		'width' => 40,
		'cssClass' => 'text-center',
		'noFilter' => 1,
		'sortable' => true,
		'resizable' => false,
		'editable' => false
	),
	array(
		'id' => 'name',
		'field' => 'name',
		'name' => __('View name', true),
		'width' => 200,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.HTMLData',
		'editable' => false
	),
	array(
		'id' => 'description',
		'field' => 'description',
		'name' => __('Description', true),
		'width' => 300,
		'sortable' => true,
		'resizable' => true,
		'editable' => false
	),
	array(
		'id' => 'created_date',
		'field' => 'created_date',
		'name' => __('Created date', true),
		'width' => 120,
		'datatype' => 'datetime',
		'sortable' => true,
		'resizable' => true,
		'editable' => false
	),
	array(
		'id' => 'employee',
		'field' => 'employee',
		// 'name' => __('Author', true) . ' / ' . __('Created date', true),
		'name' => __('Author', true),
		'width' => 80,
		'sortable' => true,
		'resizable' => true,
		// 'formatter' => 'Slick.Formatters.HTMLData',
		'editable' => false
	),
	array(
		'id' => 'progress_view',
		'field' => 'progress_view',
		'name' => __('In progress view', true),
		'width' => 120,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.CompanyDefaultSwitch',
		'editable' => false
	),
	array(
		'id' => 'oppor_view',
		'field' => 'oppor_view',
		'name' => __('Opportunity view', true),
		'width' => 120,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.CompanyDefaultSwitch',
		'editable' => false
	),
	array(
		'id' => 'archived_view',
		'field' => 'archived_view',
		'name' => __('Archived view', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.CompanyDefaultSwitch',
		'editable' => false
	),
	array(
		'id' => 'model_view',
		'field' => 'model_view',
		'name' => __('Model view', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.CompanyDefaultSwitch',
		'editable' => false
	),
	array(
		'id' => 'default_mobile',
		'field' => 'default_mobile',
		'name' => __('Default mobile view', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.CompanyDefaultSwitch',
		'editable' => false
	),
	array(
		'id' => 'default_view',
		'field' => 'default_view',
		'name' => __('Default views', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.CompanyDefaultSwitch',
		'editable' => false
	),
	array(
		'id' => 'action.',
		'field' => 'action.',
		'name' => '',//__('Action', true),
		'width' => 82,
		'minWidth' => 82,
		'maxWidth' => 82,
		'sortable' => false,
		'noFilter' => 1,
		'resizable' => false,
		'cssClass' => 'wd-action-column',
		'formatter' => 'Slick.Formatters.HTMLData',
		'editable' => false
    )
);
$i = 1;
$dataView = array();
foreach ($publicViews as $userView) {
	$vid = $userView['UserView']['id'];
    $data = array(
        'id' => $userView['UserView']['id'],
        'no.' => $i++,
    );
	$data['user_view_id'] = $userView['UserView']['id'] ? $userView['UserView']['id'] : 0;
	$data['name'] = $userView['UserView']['name'];
    $data['description'] = $userView['UserView']['description'];
	$data['created_date'] = $str_utility->convertToVNDate($userView['UserView']['created_date']);
    $data['employee'] = sprintf('%1$s %2$s', $userView['Employee']['first_name'], $userView['Employee']['last_name']);
    $data['progress_view'] = !empty($conpanyDefaultView[$vid]['progress_view']) ? 1 : 0;
    $data['oppor_view'] = !empty($conpanyDefaultView[$vid]['oppor_view']) ? 1 : 0;
    $data['archived_view'] = !empty($conpanyDefaultView[$vid]['archived_view']) ? 1 : 0;
    $data['model_view'] = !empty($conpanyDefaultView[$vid]['model_view']) ? 1 : 0;
    $data['default_mobile'] = !empty($conpanyDefaultView[$vid]['default_mobile']) ? 1 : 0;
    $data['default_view'] = !empty($conpanyDefaultView[$vid]['default_view']) ? 1 : 0;
	$data['action.'] = '<div class="userview-grid-action">' . $this->Html->link(__('', true), array(
		'controller' => 'user_views_preview', 'action' => 'edit', $userView['UserView']['id']), array('class' => 'wd-edit-view')) . '<div class="wd-bt-big">' . $this->Html->link(__('Delete', true), array(
		'action' => 'delete',$model, $userView['UserView']['id']), array(
		'class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $userView['UserView']['name'])) . '</div></div>';			
    $dataView[] = $data;
}
 // ob_clean();debug($dataView);exit;
$i18ns = array(
	'Set as default view' => __('Set as default view', true),
	'Set as mobile view' => __('Set as mobile view', true)
);
?>
<script type="text/javascript">
	var wdTable = $('.wd-table');
	var updateLink = <?php echo json_encode($this->Html->url(array(
		'controller' => 'user_views',
		'action' => 'update_default_config'
	)));?>;
	var model = <?php echo json_encode($model);?>;
	function set_table_height(){
		if ( !wdTable.length ) return;
		var heightTable = $(window).height() - wdTable.offset().top - 40;
		if( heightTable < 300){
			$('body').css('overflow', 'auto');
			wdTable.css('height', 400);
		}else{
			$('body').css('overflow', 'hidden');
			wdTable.css('height', heightTable);
		}
	}
	$(document).on('ready', function(){
		set_table_height();
	});
	$(window).on('resize', function(){
		set_table_height();
	});
	set_table_height();
	var dataGrid;
	(function($){
        $(function(){
            var $this = SlickGridCustom;
			$this.i18n = <?php echo json_encode($i18ns); ?>;
			$this.canModified = true;
			$.extend(Slick.Formatters,{
                CompanyDefaultSwitch : function(row, cell, value, columnDef, dataContext){
                    return '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-switch"><a href="'+ updateLink +'" class="wd-update ' + ( (value == '1') ? 'wd-update-default' : '' ) + '" data-field="'+columnDef.field+'" data-viewid="'+ dataContext.id +'" data-model="'+ model +'"></a></div></div>';
                },
			});
			var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
			var data = <?php echo json_encode( $dataView);?>;
			 var options = {
                enableCellNavigation: false,
                enableColumnReorder: false,
                showHeaderRow: true,
                editable: false,
                enableAddRow: false,
                headerRowHeight: 40,
                rowHeight: 40,
            };
			var parent = $('#project_container');
			dataGrid = $this.init(parent,data,columns, options);
			// console.log( dataGrid);
			parent.data('slickgrid',dataGrid);
			var dataView = dataGrid.getDataView();
			$(window).on('resize', function(){
				dataGrid.resizeCanvas();
			});
			$('body').on('click', '.wd-bt-switch .wd-update', function(e){
				e.preventDefault();
				var _this = $(this);
				var viewID = _this.data('viewid');
				var field = _this.data('field');
				var _url = _this.attr('href');
				_this.addClass('sw-loading');
				$.ajax({
					url: _url,
					type: 'POST',
					dataType: 'json',
					data: {
						data: {
							'field': field,
							'viewID': viewID,
							'model': model
						}
					},
					success: function(respon){
						if( respon.result = "success"){
							respon.data.CompanyViewDefault.id = respon.data.CompanyViewDefault.user_view_id;
							$this.update_after_edit(respon.data.CompanyViewDefault.id, respon.data.CompanyViewDefault);
							$.each( respon.defaultData, function( ind, defaultData){
								defaultData = defaultData.CompanyViewDefault;
								defaultData.id = defaultData.user_view_id;
								$this.update_after_edit(defaultData.id, defaultData);
							});
						}else{
							location.reload();
						}
						_this.removeClass('sw-loading');
					},
					error: function(){
						location.reload();
					}
				});
			});
		});
		
    })(jQuery);
</script>
			