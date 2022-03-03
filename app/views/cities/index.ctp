<?php
// ob_clean();
		// debug( $cities); exit;
	echo $this->Html->css(array(
		'slick_grid/slick.grid_v2',
		'slick_grid/slick.pager',
		'slick_grid/slick.common_v2',
		'slick_grid/slick.edit',
		'preview/grid-project',
		// 'preview/slickgrid',
		'codemirror',
		'preview/tab-admin',
		'layout_admin_2019',
		'layout_2019'
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
	$canModified = !empty( $is_sas);
	$show_company = ($employee_info['Employee']['is_sas'] || empty($employee_info['Employee']['company_id'])); // SAS or SAS login by admin
	// $show_company = ($is_sas || (($employee_info["Role"]["name"] == "admin") && ($employee_info["Company"]["parent_id"] == "")));
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
		'name' => array(
			'id' => 'name',
			'field' => 'name',
			'name' => __('Name', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			'cssClass' => 'wd-grey-background',
			'minWidth' => 100,
		),
		'code' => array(
			'id' => 'code',
			'field' => 'code',
			'name' => __('Code', true),
			'width' => 250,
			'editor' => 'Slick.Editors.textBox',
			'sortable' => true,
			'resizable' => true,
			'minWidth' => 70,
		),
		'company_id' => array(
			'id' => 'company_id',
			'field' => 'company_id',
			'name' => __('Company', true),
			'width' => 250,
			'editor' => 'Slick.Editors.selectBox',
			'sortable' => true,
			'resizable' => true,
			'minWidth' => 100,
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
	if( empty($show_company)) {
		unset( $_columns['company_id']);
	}
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
	$i = 1;
	$selectMaps = array(
		'company_id' => $company_names,
	);
	$dataView = array();
	foreach ($cities as $city){
		$class = null;
		if ($city['City']['company_id'] != "") {
			$company_id_save = $city['City']['company_id'];
			$company_name = $companies[$company_id_save];
			if ($parent_companies[$company_id_save] != "") {
				$company_name = $companies[$parent_companies[$company_id_save]] . " --> " . $company_name;
			}
		}
		else { $company_name = " ";}
		$row_data = array(
			'id' => $city['City']['id'],
			'no.' => $i++,
			'name' => $city['City']['name'],
			'code' => $city['City']['code'],
			'company_id' => $company_id_save,
			'company_name' => $company_name,
		);
		$dataView[] = $row_data;
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
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section clearfix" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
								<h3 class="wd-t3">&nbsp;</h3>
								<div id="message-place">
									<?php
									App::import("vendor", "str_utility");
									$str_utility = new str_utility();
									echo $this->Session->flash();
									?>
								</div>
								<div class="wd-title wd-hide">
								</div>
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
    </div>
</div>
<script>
HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
var selectMaps = <?php echo json_encode($selectMaps); ?>;
var current_company = <?php echo json_encode( !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : ''); ?>;
var $this = SlickGridCustom;
$.extend($this,{
	i18n : <?php echo json_encode($i18n); ?>,
	selectMaps : selectMaps,
	canModified: true,
	delete_link: "<?php echo $this->Html->url(array('controller' => 'cities', 'action' => 'delete', '%ID%')); ?>",
	onCellChange: function(args){
		return true;
	},
	url: "<?php echo $html->url(array('controller' => 'cities','action' => 'update')); ?>",
});
$this.fields = {
	id : {defaulValue : 0},
	name : {defaulValue : '', allowEmpty : false},
	code : {defaulValue : '', allowEmpty : true},
	company_id : {defaulValue : current_company, allowEmpty : false},
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
		var _html = '<div class="wd-actions wd-bt-big">';
		_html += '<a class="wd-btn wd-hover-advance-tooltip" title="' + $this.t('Delete') + '" href="' + $this.delete_link.replace('%ID%' , dataContext.id) +'" onclick="return confirm(\'' + $this.t('Delete?') + '\');">' + $this.t('Delete') + '</a>';			
		_html += '</div>';
		return _html;
	}
});
var  data = <?php echo json_encode($dataView); ?>;
var columns = <?php echo jsonParseOptions(array_values($_columns), array('editor', 'formatter', 'validator')); ?>;
var slick_options = {
	showHeaderRow: true,
	editable: true,
	enableAddRow: true,
	headerRowHeight: 40,
	rowHeight: 40,
}
dataGrid = $this.init($('#project_container'),data,columns, slick_options);
var dataView = dataGrid.getDataView();

addNewItem = function(){
	dataGrid.gotoCell(data.length, 1, true);
};
</script>
