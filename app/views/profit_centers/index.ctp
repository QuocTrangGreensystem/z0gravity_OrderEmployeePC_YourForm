<?php 
echo $html->script('jquery.validation.min'); 
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
));

echo $html->css(array(
	'slick_grid/slick.grid.activity',
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
	'jquery.ui.custom',
	'slick_grid/slick.edit',
	'preview/tab-admin',
	'layout_admin_2019',
	'preview/layout',
));

$employee_info = $this->Session->read("Auth.employee_info");
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
}

?>
<style>

.action-menu{
	text-align: center;
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
.wd-tab .wd-content {
    width: calc( 100% - 340px);
    float: left;
    overflow: visible;
	position: relative;
}
.action-menu-item:hover svg .cls-1{
	fill: #F05352;
 }
.slick-header-sortable .slick-sort-indicator{
	background-size: inherit;
}
.slick-row .slick-cell .circle-name{
	width: 35px;
	height: 35px;
	line-height: 35px;
	position: relative;
    top: 1px;
}   
.slick-headerrow-columns .slick-headerrow-column.ui-state-default .multiselect-filter .multiSelect{
	background-position: 95%;
}
.wd-title {
	margin-top: 20px;
}
.wd-title a.btn{
	height: 40px;
    width: 40px;
    line-height: 42px;
    display: none;
    border: 1px solid #E1E6E8;
    background-color: #FFFFFF;
    border-radius: 3px;
    padding: 0;
    box-sizing: border-box;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    color: #666;
}
.wd-title a.btn:hover{
	background-color: #247FC3;
    color: #fff;
}
.wd-title a.btn i{
	font-size: 18px;
}
.wd-form-container{
	padding: 10px 20px;
}
.upload-file-description{
	color: #008000;
	font-style:italic;
}
#download-file-template:before{
	content: "\e083";
}
.wd-panel .wd-title a:hover:before {
	color: #fff;
}
</style>
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
								<div class="wd-title">
									<?php if( !$is_sas){?>
										<a class="btn" href="<?php echo $html->url('/profit_centers/import') ?>" id="import-csv" title="<?php echo __('Import csv', true); ?>"><i class="icon-layers"></i></a>
									<?php } ?> 
									<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" title="<?php __('Reset filter') ?>"></a>
                                    <a class="btn" href="<?php echo $html->url(array('controller' => 'profit_centers', 'action' => 'organization?link=' . $this->params['controller'] . "/" . $this->params['action'] . "|Profit Center")) ?>" class="wd-organization" title="<?php echo __('Organization chart', true) ?>"><i class="icon-organization"></i></a>
								</div>
								<div style="position: relative">
									<a href="javascript:void(0);" class="btn add-field" id="add_country" style="margin-right:5px;" title="Add an item" onclick="addNewCountry();" ></a>
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
<div id="dialog_skip_value" class="buttons dialog_skip_value" style="display: none;">
	<div class="dialog-content loading-mark">
	<?php echo $this->Form->create('MoveData', array(
		'id' => 'MoveData',
		'url' => array('controller' => 'profit_centers', 'action' => 'movedata')
		
	)); 
	echo $this->Form->input('oldpc', array(
		'name' => 'data[oldpc]', 
		'type' => 'hidden',
		'id' => 'moveDataOldPC'
	));
	?>
	<div class="form-alert" style="display: none"><p><?php __('Please select one Profit Center to move data');?></p></div>
    <div class="wd-row">
		<div class="wd-col wd-col-lg-6">
			<div id="show-value" >

			</div>
		</div>
		<div class="wd-col wd-col-lg-6">
			<div id="reassign-to">
				<h1 class="h1-value"><?php __('Assigned all tasks to');?></h1>
				<ul>
					<?php //ob_clean(); debug( $tree); exit; ?>
					<?php foreach ($tree as $key => $value) { 
						$name = explode("|", $value['name']); 
						$pc_id = $name[1];
						$pc_name = $name[0];
						$parentpc = $name[2];
						$parentpc = explode( '-', $parentpc);
						$class = 'pc'; 
						if( !empty( $parentpc[1])) $class .= ' child-of-'.$parentpc[0];
						?>
						<li>
							<div class=" wd-input wd-radio-button">
								<input type="radio" name="data[newpc]" value="<?php echo $pc_id;?>" id="moveDataNewPC-<?php echo $pc_id;?>" class="<?php echo $class;?>"/>
								<label for="moveDataNewPC-<?php echo $pc_id;?>"><?php echo $pc_name;?></label>
							</div>
						</li>
					<?php }?>
				</ul>
			</div>
		</div>
    </div>
	<div class="wd-row wd-submit-row">
		<div class="wd-col-xs-12">
			<div class="wd-submit">
				<button href="javascript:void(0);" class="btn-form-action btn-ok btn-right" id="submitMovePC"><span><?php __('Save');?></span>
				</button>
				<!-- <a href="javascript:void(0);" class="btn-form-action btn-ok btn-right" id="save-special">
					<span><?php __('Save');?></span>
				</a> -->
				<a class="btn-form-action btn-cancel" id="cancel-special" href="javascript:void(0);" onclick="cancel_dialog(this);">
					<?php __('Cancel');?>
				</a>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end();?>
	</div>
</div>
<div id="dialog_import_CSV" title="<?php __('Import CSV file') ?>" class="buttons" style="display:none">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'profit_centers', 'action' => 'import')));
    ?>
    <div class="wd-form-container">
		<div class="wd-input">
			<!-- <label><?php echo __('File:') ?></label> -->
			<input type="file" name="FileField[csv_file_attachment]" accept=".csv"/>
			<div class="upload-file-description">(<?php __('Allowed file type') ?>: *.csv)</div>
		</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)" title="<?php __('Close')?>"><?php echo __('Close') ?></a></li>
        <li><a id="download-file-template" class="download ok" href="<?php echo $this->Html->url(array('action' => 'get_import_template'));?>" title="<?php __('Download template file')?>"></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#" title="<?php __('Submit')?>"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<?php 
$i18n = array(
	'deletePC' 	=> __('Are you sure you want to delete "%s"?', true),
	'yes'		=> __('Yes', true),
	'no' 		=> __('No', true),
	'cancel' 	=> __('Cancel', true)
);
?>
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
$columns = array(
	array(
		'id' => 'no.',
		'field' => 'no.',
		'name' => '#',
		'width' => 40,
		'noFilter' => 1,
		'sortable' => false,
		'resizable' => false,
		'cssClass' => 'cell-text-center',
	),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
		'cssClass' => 'wd-grey-background',
    ),
    array(
        'id' => 'parent_id',
        'field' => 'parent_id',
        'name' => __('Parent', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.singleSelectBox',
    ),
   'company_id' => array(
        'id' => 'company_id',
        'field' => 'company_id',
        'name' => __('Company', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.singleSelectBox',
    ),
    array(
        'id' => 'manager_id',
        'field' => 'manager_id',
        'name' => __('Manager', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.singleSelectBox',
		'formatter' => 'Slick.Formatters.formatterManager',
    ),
	array(
        'id' => 'manager_backup',
        'field' => 'manager_backup',
        'name' => __('Backup Manager', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.mselectBox',
		'formatter' => 'Slick.Formatters.formatterBKManager',
    ),
	array(
        'id' => 'analytical',
        'field' => 'analytical',
        'name' => __('Analytical', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
    ),
	array(
        'id' => 'tjm',
        'field' => 'tjm',
        'name' => __('Average Daily Rate', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.numericValue',
    ),
	array(
        'id' => 'profile_id',
        'field' => 'profile_id',
        'name' => __('Profile', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.selectBox',
    ),
	'action.' => array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => '',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action',
		'cssClass' => 'cell-text-center',
    )
);
if(!$is_sas){
	unset( $columns['company_id']);
}
$i = 1;
$dataView = array();

$selectMaps = array(
	'company_id' => $companies,
	'parent_id' => $profits,
	'profile_id' => $profile,
	'manager_id' => $employees,
	'manager_backup' => $employees,
);

$i = 1;
foreach ($tree as $key => $value) {
	$node_level = substr_count($value['name'], '--');
	$name = explode("|", $value['name']);
	if (!empty($name[2])) {
		$parent = explode("-", $name[2]);
		if (!empty($parent[1])) {
			$parent[1] = $parent[1];
			$parent_id = $parent[0];
			$parent[0] = " class=\"child-of-ex3-node-{$parent[0]}\"";
		} else {
			$parent[1] = $parent[0];
			$parent[0] = " class=\"\"";
			$parent_id = "";
		}
	} else {
		$parent = '';
		$parent[1] = '';
		$parent[0] = " class=\"\"";
		$parent_id = "";
	}
	if (!empty($value['parent']))
		$parent_name = $value['parent'];
	else
		$parent_name = '';
    $data = array(
        'id' => $key,
		'no.' => $i++,
        'MetaData' => array()
    );
	
	$data['name'] = str_replace('--', '', $name[0]);
	
	//  Company   
	$company = explode("|", $value['company']);   
	
	$data['company_id'] = $company[1];
	
	$manager = explode("|", $value['manager']); 
	$backupIds = $backupNames = array();
	if(!empty($backupOfProfitcenters[$name[1]])){
		foreach($backupOfProfitcenters[$name[1]] as $pIds){
			$backupIds[] = $pIds;
			if(!empty($managerBackupLists[$pIds])){
				$backupNames[] = $managerBackupLists[$pIds];
			}
		}
	}
	$data['parent_id'] = $parent_id;
	$data['manager_id'] = $manager[1];
	$data['manager_backup'] = $backupIds;
	$data['analytical'] =  $value['analytical'];
	$data['tjm'] =  !empty( $value['tjm']) ? $value['tjm'] : '';
	$data['profile_id'] =  !empty( $value['profile_id']) ? $value['profile_id'] : '';
	$data['action.'] = !empty( $value['profile_id']) ? $profile[$value['profile_id']] : '';
	$dataView[] = $data;
}
$i18n = array(
	'-- Any --' => __('-- Any --', true),
	'Delete' => __('Delete', true),
);

?>
<script type="text/javascript">
HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
var timeoutID;
var selectMaps = <?php echo json_encode($selectMaps); ?>;
var employees = <?php echo json_encode($employees); ?>;
function get_grid_option(){
	var _option ={
		showHeaderRow: true,
		frozenColumn: 1,
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
		var current_company = <?php echo json_encode( !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : ''); ?>;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend($this,{
			selectMaps : selectMaps,
			canModified: true,
		});
		$.extend(Slick.Formatters,{
			formatterManager : function (row, cell, value, columnDef, dataContext) {
                avatar = '';
				if(value && value != '0'){
					avatar +='<a class="circle-name" title="'+ employees[value] +'"><img width="35" height="35" class="circle" src="'+ js_avatar(value) +'" alt="avatar"></a>';
				}
                return avatar;
            },
			formatterBKManager : function (row, cell, value, columnDef, dataContext) {
                avatar = '';
				if(value){
					$.each(value, function (key, val) {
						avatar +='<a class="circle-name" title="'+ employees[val] +'"><img width="35" height="35" class="circle" src="'+ js_avatar(val) +'" alt="avatar"></a>';
					});
				}
                return avatar;
            },
			Action : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id));
			},
			
		});
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            name : {defaulValue : '', allowEmpty : false},
            company_id : {defaulValue : current_company, allowEmpty : false},
            parent_id : {defaulValue : ''},
            manager_id : {defaulValue : ''},
            manager_backup : {defaulValue : ''},
            analytical : {defaulValue : ''},
            tjm : {defaulValue : ''},
            profile_id : {defaulValue : ''},
        };
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
		ControlGrid.setSelectionModel(new Slick.RowSelectionModel());
		addNewCountry = function(){
			ControlGrid.gotoCell(data.length, 1, true);
		};
		function update_table_height(){
			wdTable = $('.wd-table');
			var heightTable = $(window).height() - wdTable.offset().top - 40;
			wdTable.height(heightTable);
			if( SlickGridCustom.getInstance() ) SlickGridCustom.getInstance().resizeCanvas();
		}
		$(window).resize(function(){
			update_table_height();
		});
		update_table_height();
	});
	history_reset = function(){
		var check = false;
		$('.multiselect-filter').each(function(val, ind){
			var text = '';
			if($(ind).find('input').length != 0){
				text = $(ind).find('input').val();
			} else {
				text = $(ind).find('span').html();
				if( text == "<?php __('-- Any --');?>" || text == '-- Any --'){
					text = '';
				}
			}
			if(($('.slick-header-column-sorted').length != 0)||($('.slick-sort-indicator-asc').length != 0)){
				text = '1';
			}
			if( text != '' ){
				// $(ind).css('border', 'solid 2px orange');
				check = true;
			} else {
				$(ind).css('border', 'none');
			}
		});
		if(!check){
			$('#reset-filter').addClass('hidden');
		} else {
			$('#reset-filter').removeClass('hidden');
		}
	}
	resetFilter = function(){
		$('.multiselect-filter input').val('').trigger('change');
		$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
		$('input[name="project_container.SortOrder"]').val('').trigger('change');
		$('input[name="project_container.SortColumn"]').val('').trigger('change');
		ControlGrid.setSortColumn('no.', false);
		$('.slick-header-columns').children().eq(0).trigger('click'); // for first column
		ControlGrid.setSortColumn();		
	}
})(jQuery);

</script>


<script>
    var $ids = [];
    var projectEmployeeManager = $('#wd-data-project').find('.wd-data-manager');
	var PCassociate = [];
	var reloadOnFinish = 0;

	var i18n = <?php echo json_encode($i18n);?>;
    $(document).ready(function() {
        var createDialog = function(){
            $('#dialog_skip_value').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
				create: function( event, ui ) {
					$('#dialog_skip_value').closest('.ui-dialog').addClass('wd-dialog-2019');
					$('#MoveData').on('submit', function(e){
						e.preventDefault();
						submit_move_data(this);
					});
					$('#MoveData').find('input[type="radio"]').on('change', function(){
						$('#MoveData').find('.form-alert:visible').slideUp();
					});
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
		if( window.location.hash ) $(window.location.hash).addClass('focus');
        $("#message-place").on('click', '#list_delete', function(){
            var id = $(this).attr('data_id');
			$('#moveDataOldPC').val(id);
            $("#show-value").html('');
			$('#reassign-to').find('input[type="radio"]').prop('checked', false).prop('disabled', false);
			$('#reassign-to').find('input[value="' + id + '"]').prop('disabled', true);
			disable_child_pc( id );
			
			if( $.isEmptyObject(PCassociate[id])){
				$.ajax({
					url: '/profit_centers/profitCenterIsAjax/'+ id,
					async: false,
					dataType: 'json',
					success:function(data) {
					  PCassociate[id] = data;
					}
				});
			}  
			data = PCassociate[id];
			var _html = '';
			if( !$.isEmptyObject(data.activities) )
			{
				if( _html) _html += '<hr />';
				_html += '<div class="value-block">';
				_html += '<h1 class="h1-value"><?php __('Activity') ?></h1>';
				_html += '<div class="value-block-content">';
				$.each(data.activities, function(index, value){
					_html += '<a class="text-value" target="_blank" href="<?php echo $html->url('/activity_tasks/index/') ?>' + index + '" >' + value + '</a>';
				});
				_html += '</div></div>';
			}
			if(!$.isEmptyObject(data.employees))
			{
				if( _html) _html += '<hr />';
				_html += '<div class="value-block">';
				_html += '<h1 class="h1-value"><?php __('Employee') ?></h1>';
				_html += '<div class="value-block-content">';
				$.each(data.employees, function(index, value){
					_html += '<a class="text-value" target="_blank" href="<?php echo $html->url("/employees/edit/") ?>' + index + '">'+value+'</a>';
				});
				_html += '</div></div>';
			}
			if(!$.isEmptyObject(data.projects))
			{
				if( _html) _html += '<hr />';
				_html += '<div class="value-block">';
				_html += '<h1 class="h1-value"><?php __('Project') ?></h1>';
				_html += '<div class="value-block-content">';
				$.each(data.projects, function(index, value){
					_html += '<a class="text-value" target="_blank" href="<?php echo $html->url("/project_teams/index/") ?>'+ index + '">'+value+'</a>';
				});
				_html += '</div></div>';
			}
			if(!$.isEmptyObject(data.activitytasks))
			{
				if( _html) _html += '<hr />';
				_html += '<div class="value-block">';
				_html += "<h1 class='h1-value'><?php __('Activity tasks') ?></h1>";
				_html += '<div class="value-block-content">';
				$.each(data.activitytasks, function(index, value){
					_html += '<a class="text-value" target="_blank" href="<?php echo $html->url('/activity_tasks/index/') ?>' + value.activity_id + '/?id='+ index +'">'+value.name+'</a>';
				});
				_html += '</div></div>';
			}
			if(!$.isEmptyObject(data.projectasks))
			{
				if( _html) _html += '<hr />';
				_html += '<div class="value-block">';
				_html += "<h1 class='h1-value'><?php __('Project tasks') ?></h1>";
				_html += '<div class="value-block-content">';
				$.each(data.projectasks, function(index, value){
					_html += '<a class="text-value" target="_blank" href="<?php echo $html->url('/project_tasks/index/') ?>' + value.project_id + '/?id='+ index +'">'+value.task_title+'</a> ';
				});
				_html += '</div></div>';
			}			
			if(!$.isEmptyObject(data.childPC))
			{
				if( _html) _html += '<hr />';
				_html += '<div class="value-block">';
				_html += "<h1 class='h1-value'><?php __('Child Profit Center') ?></h1>";
				_html += '<div class="value-block-content">';
				$.each(data.childPC, function(index, value){
					_html += '<a class="text-value" target="_blank" href="<?php echo $html->url('/profit_centers/index/') ?>' + '#ex3-node-'+ index +'">'+value+'</a> ';
				});
				_html += '</div></div>';
			}
			
			$("#show-value").html(_html);
            createDialog();
            $("#dialog_skip_value").dialog('open');
        });
       
        //create select box backup manager
        var initMenuFilter = function($menu){
            var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
            $menu.before($filter);

            var timeoutID = null, searchHandler = function(){
                var val = $(this).val();
                var te = $($menu).find('.wd-data-manager .wd-data span').html();

                $($menu).find('.wd-data-manager .wd-data span').each(function(){
                    var $label = $(this).html();
                    $label = $label.toLowerCase();
                    val = val.toLowerCase();
                    if(!val.length || $label.indexOf(val) != -1 || !val){
                        $(this).parent().css('display', 'block');
                        $(this).parent().next().css('display', 'block');
                    } else{
                        $(this).parent().css('display', 'none');
                        $(this).parent().next().css('display', 'none');
                    }
                });
            };

            $filter.find('input').click(function(e){
                e.stopImmediatePropagation();
            }).keyup(function(){
                var self = this;
                clearTimeout(timeoutID);
                timeoutID = setTimeout(function(){
                    searchHandler.call(self);
                } , 200);
            });

        };
        initMenuFilter($('#wd-data-project'));
        $('.context-menu-filter').css('display', 'none');
        $('.wd-combobox').click(function(){
            var checked = $(this).attr('checked');
            if(checked){
                $('#wd-data-project').css('display', 'none');
                $(this).removeAttr('checked');
                $('.context-menu-filter').css('display', 'none');
            } else {
                $('#wd-data-project').css('display', 'block');
                $(this).attr('checked', 'checked');
                $('.context-menu-filter').css({
                    'display': 'block',
                    'width': '100%',
                    'z-index': 2
                });
                $('#wd-data-project div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('html').click(function(e){
            if($(e.target).attr('class') &&
            (
                ( $(e.target).attr('class').split(' ')[0] &&
                    (
                        $(e.target).attr('class').split(' ')[0] == 'backupManager'
                    )
                ) ||
            $(e.target).attr('class') == 'context-menu-filter'
            )){
                //do nothing
            } else {
                $('.context-menu-filter').css('display', 'none');
                $('#wd-data-project').css('display', 'none');
                $('.wd-combobox').removeAttr('checked');
            }
        });
        /**
         * Remove element to array
         */
        jQuery.removeFromArray = function(value, arr) {
            return jQuery.grep(arr, function(elem, index) {
                return elem !== value;
            });
        };
        projectEmployeeManager.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When click in checkbox
             */
            $(data).find('.backupManager:checkbox').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids.push(_datas);
                    $('a.wd-combobox').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids = jQuery.removeFromArray(_datas, $ids);
                    $('a.wd-combobox').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox').find('.wd-em-' +_datas).remove();
                }
            });
        });
		$('.delete-pc').on('click', function(e){
			e.preventDefault();
			var _this = $(this);
			var pc_id = _this.data('pc-id');
			console.log( 'Delete?');
			wdConfirmIt({
				title: _this.attr('title') + '?',
				content: i18n.deletePC.replace('%s', _this.data('pc-name')),
				buttonModel: 'WD_TWO_BUTTON',
				buttonText: [i18n.yes, i18n.cancel]
			},function(){ // functionIfYes
				deletePC(pc_id);
			}, function(){
				if( reloadOnFinish ) location.reload();
			});
		});
    });
	function disable_child_pc( parent_id){
		console.log( parent_id);
		var _cont = $('#reassign-to');
		var _child = _cont.find( 'input.child-of-' + parent_id);
		if( _child.length){
			$.each(_child, function( i, tag){
				var _this = $(tag);
				var id = _this.val();
				_this.prop('disabled', true);
				disable_child_pc(id);
			});
		}
	}
	function submit_move_data(elm){
		var _this = $(elm);
		$('#submitMovePC').prop('disabled', true);
		var data = new FormData(_this[0]);
		if( !_this.find('input[name="data[newpc]"]:checked').length){
			_this.find('.form-alert:hidden').show();
			return;
		}
		_this.closest('.loading-mark').addClass('loading');
		_this.find(':submit').prop('disabled', true);
		setTimeout( function(){ // wait for animation loaded
			$.ajax({
				url: _this.attr('action'),
				type: _this.attr('method'),
				mimeType:"multipart/form-data",
				async: false,
				cache: false,
				contentType: false,
				dataType: 'json',
				cache: false,
				processData: false,
				data: data,
				beforeSend: function(){
					
				},
				complete: function(){
					_this.closest('.loading-mark').removeClass('loading');
					$('#submitMovePC').prop('disabled', false);
				},
				error: function(){
					_this.closest('.loading-mark').removeClass('loading');
					$('#submitMovePC').prop('disabled', false);
				},
				success: function(response){
					PCassociate = [];
					$("#dialog_skip_value").dialog('close');
					$(window).trigger('resize');
					if( response.result == 'success'){
						$('#message-place').empty();
						reloadOnFinish = 1;
						var _id = $('#moveDataOldPC').val();
						$('.delete-pc[data-pc-id="' + _id + '"]').click();
					}else{
						location.reload();
					}
				}
			});
		}, 50);
	};
	function  deletePC(id){
		var _url = '<?php echo $this->Html->url(array('controller' => 'profit_centers', 'action' => 'delete'));?>/' + id;
		if( reloadOnFinish ) {
			window.location.href = _url;
			return;
		}
		$.ajax({
			url: _url,
			type: 'get',
			dataType: 'json',
			beforeSend: function(){
				$('.wd-content').addClass('loading-mark').addClass('loading');
			},
			success: function(response){
				console.log( response);
				if( response.result =='success'){
					removePCFromView(id);
					$('#message-place').html(response.message);
					return;
				}
				if( response.result =='failed'){
					if( typeof response.data == "object"){
						PCassociate[id] = response.data;
						$('#message-place').html(response.message).show();
						$(window).trigger('resize');
					}else{
						location.reload();
					}
				}
			},
			error: function(){
				$('.wd-content').removeClass('loading');
				location.reload();
			},
			complete: function(){
				$('.wd-content').removeClass('loading');
			},
		});
	}
	function removePCFromView(id){
		var elm = $('#ex3-node-' + id);
		if( elm.length) elm.slideToggle(300, function(){ 
			elm.remove();
			$('#message-place').show()
		});
	}

	<?php if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"])))) { ?>
			$("#reset_form_1").click(function(){
				$("#ProfitCenterId").val("");
				$("#title_form_update_city").html("<?php __("Add a new profit center") ?>");
				$("#ProfitCenterName").val("");
				$("#flashMessage").hide();
				$(".error-message").hide();
				$("div.wd-input input, select").removeClass("form-error");
				$(".error").css('backgroundColor','#ffffff');
			});
	<?php } ?>

    ;(function($){

        var $employee = null;
        function loadEmployee(id){
            if($employee){
                $employee.combobox('destroy');
                $employee.prop('disabled',true).show();
            }
            $employee = null;
            if(!id){
                return;
            }
            $.ajax({
                cache : true,
                url : <?php echo json_encode($this->Html->url(array('action' => 'get_employees'))); ?> + '/' + id,
                success : function(data){
                    var $place = $('#employee-place');
                    var $text = $place.find('select :selected').clone();
                    $place.html(data);
                    $employee = $place.find('select').val($text.val()).combobox();
                }
            });
        }
        var $employeeBackup = null;
        function loadEmployeeBackup(id){
            if($employeeBackup){
                $employeeBackup.combobox('destroy');
                $employeeBackup.prop('disabled',true).show();
            }
            $employeeBackup = null;
            if(!id){
                return;
            }
            $.ajax({
                cache : true,
                url : <?php echo json_encode($this->Html->url(array('action' => 'get_employee_backup'))); ?> + '/' + id,
                success : function(data){
                    var $place = $('#employee-place-2');
                    var $text = $place.find('select :selected').clone();
                    $place.html(data);
                    $employeeBackup = $place.find('select').val($text.val()).combobox();
                }
            });
        }

        $('#ProfitCenterCompanyId').change(function(){
            loadEmployee($(this).val());
            //loadEmployeeBackup($(this).val());
        });
        $('#ProfitCenterCompanyId').change();

    })(jQuery);



    function editProfit(id, parent_id, profir_name, company_id, manager_id, analy, tjm, listIdOfPcs){
        if(parent_id){
            $("#ProfitCenterParentId").val(parent_id);
        }else{
            $("#ProfitCenterParentId").val("");
        }
        $("#ProfitCenterId").val(id);
        $("#ProfitCenterName").val(profir_name);
        $("#ProfitCenterCompanyId").val(company_id).change();
        $("#ProfitCenterManagerId").val(manager_id);
        listIdOfPcs = listIdOfPcs ? listIdOfPcs.toString().split(',') : '';
        $('a.wd-combobox').html('');
        $ids = [];
        $('#wd-data-project').find('div p input').removeAttr('checked');
        projectEmployeeManager.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('.backupManager:checkbox').val();
            if(listIdOfPcs){
                $.each(listIdOfPcs, function(idPlan, idPhase){
                    idPhase = parseInt(idPhase);
                    if(valList == idPhase){
                        $(data).find('.backupManager:checkbox').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids.push(idPhase);
                        }
                    } else {
                        //$(data).find('.backupManager:checkbox').removeAttr('checked');
                    }
                });
            }
        });

        $("#ProfitCenterAnalytical").val(analy);
        $("#ProfitCenterTjm").val(tjm);
        $("#title_form_update_city").html("<?php __("Edit this profit center") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");
        $(".error").css('backgroundColor','#ffffff');
    }

    $("#reset_form").click(function(){
        $("#ProfitCenterCompanyId").val('').change();
        $("#ProfitCenterId").val("");
        $("#ProfitCenterManagerId").val("");
        $("#ProfitCenterManagerBackupId").val("");
        $("#ProfitCenterParentId").val("");
        $("#ProfitCenterAnalytical").val("");
        $("#ProfitCenterTjm").val("");
        $("#title_form_update_city").html("<?php __("Add a new profit center") ?>");
        $("#ProfitCenterName").val("");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");
        $(".error").css('backgroundColor','#ffffff');
    });
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });

    $('#dialog_import_CSV').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        // height      : auto
    });
    $("#import-csv").click(function(){
        //$('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[csv_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
        $('#dialog_import_CSV').dialog("open");
        return false;
    });
    $("#import-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[csv_file_attachment]']").val()){
            var filename = $("input[name='FileField[csv_file_attachment]']").val();
            var valid_extensions = /(\.csv)$/i;
            if(valid_extensions.test(filename)){
                $('#uploadForm').submit();
            }
            else{
                $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error');
            }
        }else{
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error');
        }
    });

    $(".cancel").live('click',function(){
        $("#dialog_data_CSV").dialog("close");
        $("#dialog_import_CSV").dialog("close");
    });
</script>

