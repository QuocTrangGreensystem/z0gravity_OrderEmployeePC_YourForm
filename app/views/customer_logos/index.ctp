<?php
// ob_clean();
		// debug( $customer_logo); exit;
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
		'layout_2019',
		'dropzone.min',
		'preview/customer_logo',
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
		 'dropzone.min',
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
		'logo_name' => array(
			'id' => 'logo_name',
			'field' => 'logo_name',
			'name' => __('Name', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'wd-grey-background',
			'formatter' => 'Slick.Formatters.logoDisplay',
			'minWidth' => 100,
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

	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
	$i = 1;
	$selectMaps = array(
		'company_id' => $company_names,
	);
	$dataView = array();
	foreach ($customer_logo as $logo){
		
		$row_data = array(
			'id' => $logo['CustomerLogo']['id'],
			'no.' => $i++,
			'logo_name' => $logo['CustomerLogo']['logo_name'],
			'company_id' => $logo['CustomerLogo']['company_id'],
		);
		$dataView[] = $row_data;
	}
?>
<style>
img {
    vertical-align: middle;
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
									<div class="wd-table-container">
										<div class="wd-popup-container">
											<div class="wd-popup">
												<?php
												echo $this->Form->create('popupUpload', array(
													'type' => 'POST',
													'url' => array('controller' => $this->params['controller'], 'action' => 'upload')));
												?>
												<p class="input-title"><?php echo __("Company", true); ?></p>
												<?php echo $this->Form->input('company_id', array(
													'class' => 'not_save_history',
													'type' => 'select',
													'id' => 'company_id', 
													'options' => $companies,
													'rel' => 'no-history',
													'empty'=>__("Select a company", true),
													'label' => false,
												)); ?>
												<p class="input-title"><?php echo __("Customer logo", true); ?></p>
												<div id="popup_template_attach" >
													<div class="trigger-upload"><div id="upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'add_logo' ));?>" class="dropzone" value="" >
													</div></div>
												</div>
												<?php
												echo $this->Form->end(__('Save', true));
												?>
											</div>
											<a href="javascript:void(0);" class="btn add-field" id="add_request" style="margin-right:5px;" title="Add an item" onclick="addNewItem();" ></a>
										</div>
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
	delete_link: "<?php echo $this->Html->url(array('action' => 'delete', '%ID%')); ?>",
	logo_link: "<?php echo $this->Html->url(array('action' => 'attachment', '%ID%', '?' => array('sid' => $api_key)), true); ?>",
	onCellChange: function(args){
		return true;
	},
	url: "<?php echo $html->url(array('controller' => 'customer_logos','action' => 'update')); ?>",
});
$this.fields = {
	id : {defaulValue : 0},
	logo_name : {defaulValue : '', allowEmpty : false},
	company_id : {defaulValue : '', allowEmpty : true},
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
	logoDisplay: function(row, cell, value, columnDef, dataContext){
		dataContext.id
		var _html = '<img src="' + $this.logo_link.replace('%ID%' , dataContext.id) +'"" />' + '<span style="margin-left: 15px">' + value + '</span>';
		return _html;
	},
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
	rowHeight: 70,
}
dataGrid = $this.init($('#project_container'),data,columns, slick_options);
var dataView = dataGrid.getDataView();

addNewItem = function(){
	$('.wd-popup-container').toggleClass('open');
};

Dropzone.autoDiscover = false;
$(function() {
	var popupDropzone = new Dropzone("#upload-popup",{
		maxFiles: 1,
		autoProcessQueue: false,
		addRemoveLinks: true,
		acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png",
	});
	popupDropzone.on("success", function(file) {
		popupDropzone.removeFile(file);
	});
	popupDropzone.on("queuecomplete", function(file) {
		location.reload();
	});
	$('#popupUploadIndexForm').on('submit', function(e){
		$('#popupUploadIndexForm').parent('.wd-popup').addClass('loading')
		$('#popupUploadName').val($('#newDocName').val());
		$('#popupUploadUrl').val($('#newDocURL').val());;

		if(popupDropzone.files.length){
			e.preventDefault();
			popupDropzone.processQueue();
		}
	});
	popupDropzone.on('sending', function(file, xhr, formData) {
		// Append all form inputs to the formData Dropzone will POST
		var data = $('#popupUploadIndexForm').serializeArray();
		$.each(data, function(key, el) {
			formData.append(el.name, el.value);
		});
	});
});
</script>
