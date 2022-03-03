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
	'slick_grid/slick.edit',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
	
));
?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
	.wd-list-project .wd-tab .wd-content label{
		width: fit-content !important;
		display: inline-block !important;
		margin-top: 10px;
		float: none !important;
	}
	.wd-input-select select{
		float: none !important;
		display: inline-block !important;
	}
	.wd-input-select {
		display: inline-block !important;
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
	.action-menu{
		text-align: center;
	}
	.action-menu-item:hover svg .cls-1{
		fill: #F05352;
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
	.wd-panel .wd-section{
		display: block;
		width: 100%;
	}
	.wd-tab .wd-content{
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
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
	.slick-cell.l0.r0 {
		text-align: center;
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
if (($is_sas == 1) || ($employee_info["Role"]["name"] == "admin")) {
?>
	<div id="wd-container-main" class="wd-project-admin">
		<?php echo $this->element("project_top_menu") ?>
		<div class="wd-layout">

			<div class="wd-main-content">
				<div class="wd-list-project">
					<div class="wd-title">
					</div>
					<?php
					App::import("vendor", "str_utility");
					$str_utility = new str_utility();
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
									<a href="javascript:void(0);" class="btn add-field" id="add_item" style="margin-right:5px;top: 100px;right: 50px;" title="Add an item" onclick="addNewItem();"></a>
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
<?php } else { ?>
    <div align="center">
        <br/>
        <strong>You do not have permission to access.</strong>
        <br/>
        <br/>
    </div>
<?php } ?>
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
	$options = array(
		1 => __('Yes', true),
		0 => __('No', true),
	);
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
		'no' => array(
			'id' => 'no',
			'field' => 'no',
			'name' => '',	
			'width' => 40,
			'noFilter' => 1,
			'sortable' => false,
			'resizable' => false
		),
		'phase_status' => array(
			'id' => 'phase_status',
			'field' => 'phase_status',
			'name' => __('Phase Status', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			// 'cssClass' => 'wd-grey-background',
		),
		'company_id' => array(
			'id' => 'company_id',
			'field' => 'company_id',
			'name' => __('Company', true),
			'width' => 300,
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
		'company_id' => $companies,
		'display' => array( 1 => __('Yes', true), 0 => __('No', true)),
	);
	$dataView = array();
	$i = 1;
	foreach ($projectPhaseStatuses as $projectPhaseStatuse){
		if ($projectPhaseStatuse['ProjectPhaseStatus']['company_id'] != "") {
			$company_id_save = $projectPhaseStatuse['ProjectPhaseStatus']['company_id'];
			$company_name = $companies[$company_id_save];
			if ($parent_companies[$company_id_save] != "") {
				$company_name = $companies[$parent_companies[$company_id_save]] . " --> " . $company_name;
			}
		}
		else { $company_name = " ";}
		$row_data = array(
			'id' => $projectPhaseStatuse['ProjectPhaseStatus']['id'],
			'no' => $i++,
			'phase_status' => $projectPhaseStatuse['ProjectPhaseStatus']['phase_status'],
			'display' => $projectPhaseStatuse['ProjectPhaseStatus']['display'] ? 1 : 0,			
			'company_id' => $company_id_save,
			'company_name' => $company_name
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
				
			});
			var data = <?php echo json_encode($dataView); ?>;
			var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
			$this.fields = {
				id : {defaulValue : 0},
				phase_status : {defaulValue : '', allowEmpty : false},
				display : {defaulValue : '', allowEmpty : false},
				company_id : {defaulValue : current_company, allowEmpty : false}
			};
			ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
			addNewItem = function(){
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
</script>