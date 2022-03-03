<style>
	.wd-input-select {
		display: inline-block !important;
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
</style>
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
));

echo $html->css(array(
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
	
));
?>
<?php echo $html->css('jquery.ui.custom'); 
echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('green/colorpicker'); ?>
<?php echo $html->script('green/colorpicker'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
// echo $html->script('jquery.dataTables');
// echo $html->css('jquery.dataTables');
// echo $html->script('jquery.simpleColor');
echo $html->css('preview/tab-admin');
echo $html->css('layout_admin_2019');
$employee_info = $this->Session->read("Auth.employee_info");
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
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
	.wd-input-select {
		display: inline-block !important;
	}
</style>
<?php
if(!empty($employee_info['CompanyEmployeeReference'])){
	$current_company = $employee_info['CompanyEmployeeReference']['company_id'];
}else{
	$current_company = '';
}
?>
<div id="wd-container-main" class="wd-project-admin">
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
                                <h2 class="wd-t3"></h2>
								<?php if($isAdminSas == 1){?>
									<div class="gr-settings">
										<div class="wd-input-select">
											<label style="width:250px;margin-top: 7px;"><?php echo __('Activate family linked to a program', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('activate_family_linked_program', array(
													'div' => false,
													'label' => false,
													'onchange' => "editMe('activate_family_linked_program', this.value);",
													"class" => "activate_family_linked_program",
													"default" => &$companyConfigs['activate_family_linked_program'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select" style="">
											<label style="width:320px;margin-top: 7px;"><?php echo __('Opportunity to In progress without validation', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('opportunity_to_in_progress_without_validation', array(
													'div' => false,
													'label' => false,
													'onchange' => "editMe('opportunity_to_in_progress_without_validation', this.value);",
													"class" => "opportunity_to_in_progress_without_validation",
													"default" => &$companyConfigs['opportunity_to_in_progress_without_validation'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div><br /><br />
									</div>
								<?php }?>
								<div id="message-place">
									<?php echo $this->Session->flash();	?>
								</div>
								<a href="javascript:void(0);" class="btn add-field" id="add_request" style="margin-right:5px;top: 90px;right: 50px;" title="Add an item" onclick="addNewItem();"></a>
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
		'no' => array(
			'id' => 'no',
			'field' => 'no',
			'name' => '',
			'width' => 40,
			'noFilter' => 1,
			'sortable' => false,
			'resizable' => false
		),
		'amr_program' => array(
			'id' => 'amr_program',
			'field' => 'amr_program',
			'name' => __('Program/Portfolio', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			'cssClass' => 'wd-grey-background',
		),
		'color' => array(
			'id' => 'color',
			'field' => 'color',
			'name' => __('Color', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.colorPicker',
			'formatter' => 'Slick.Formatters.colorBox'
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
	);
	$dataView = array();
	$i = 1;
	foreach ($projectAmrPrograms as $projectAmrProgram){
		if ($projectAmrProgram['ProjectAmrProgram']['company_id'] != "") {
			$company_id_save = $projectAmrProgram['ProjectAmrProgram']['company_id'];
			$company_name = $company_names[$company_id_save];
			if ($parent_companies[$company_id_save] != "") {
				$company_name = $company_names[$parent_companies[$company_id_save]] . " --> " . $company_name;
			}
		}
		else { $company_name = " ";}
		$row_data = array(
			'id' => $projectAmrProgram['ProjectAmrProgram']['id'],
			'no' => $i++,
			'amr_program' => $projectAmrProgram['ProjectAmrProgram']['amr_program'],
			'family_id' => !empty($projectAmrProgram['ProjectAmrProgram']['family_id']) ? $projectAmrProgram['ProjectAmrProgram']['family_id'] : 0,
			'company_id' => $company_id_save,
			'company_name' => $company_name,
			'sub_family_id' => !empty($projectAmrProgram['ProjectAmrProgram']['sub_family_id']) ? $projectAmrProgram['ProjectAmrProgram']['sub_family_id'] : 0,
			'color' => !empty($projectAmrProgram['ProjectAmrProgram']['color']) ? $projectAmrProgram['ProjectAmrProgram']['color'] : '#004380',
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
		var colorTemplate = '<div style="width: 20px; height: 20px; background-color: %s; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
		$.extend(Slick.Formatters,{
			colorBox : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(colorTemplate,value), columnDef, dataContext);
			},
			requestAction : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id));
			},
			
		});
		$.extend(Slick.Editors, {
			colorPicker:  function(args){
				this.isCreated = false;
				$.extend(this, new Slick.Editors.textBox(args));
				this.input.attr('maxlength' , 7).prop('readonly' , true);
				
				
				var serializeValue = this.serializeValue;
				this.serializeValue = function(){
					if(!this.isCreated){
						this.input.miniColors();
						this.input.parent().css('overflow', 'visible').find('.miniColors-triggerWrap').insertBefore(this.input);
						this.isCreated = true;
						this.focus();
					}
					return serializeValue.apply(this,$.makeArray(arguments));
				}
				
				var destroy = this.destroy;
				this.destroy = function(){
					this.input.miniColors('destroy');
					destroy.apply(this, $.makeArray(arguments));
				}
			}
		});
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            amr_program : {defaulValue : '', allowEmpty : false},
            family_id : {defaulValue : '', allowEmpty : true},
            sub_family_id : {defaulValue : '', allowEmpty : true},
            company_id : {defaulValue : current_company, allowEmpty : false},
            color : {defaulValue : '#004380', allowEmpty : false},
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

<script>
// oTable = $('#table-list-admin_wrapper').dataTable( {
    // "sScrollY": "150px",
    // "sDom": 'R<"H"lfr>t<"F"ip>',
    // "bJQueryUI": true,
    // "sPaginationType": "full_numbers"
// } );
// $('.panel_form_1 .content_1').hide();
// $('.panel_form_1 .trigger_1').bind('click', function(){
    // $("#click_trigger").val("0");
    // $('.content_1').slideToggle();
// });
// $("#ProjectAmrProgramAmrProgram, #ProjectAmrProgramCompanyId, #ProjectAmrProgramFamilyId").click(function(){
    // $('.panel_form_1 .content_1').hide();
// })
// $(document).ready(function(){
    // $("#color_1").attr("style",'width: 100%; height: 27px; background-color: #004380') ;
    // $("#color_value").val('#004380');
    // $('.trigger_lv_1').simpleColor({
        // cellWidth: 9,
        // cellHeight: 9,
        // border: '1px solid #333333',
        // buttonClass: 'button'
    // });

    // $('input#alert_button').click( function() {
        // alert($('input.simple_color')[0].value);
    // });
// });
<?php if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"])))) { ?>
        $("#reset_form_1").click(function(){
            $("#ProjectAmrProgramAmrProgram").val('');
            $("#ProjectAmrProgramId").val('');
            $("#CityId").val("");
            $("#title_form_update_city").html("<?php __("Add a new project AMR program") ?>");
            $("#CityName").val("");
            $("#flashMessage").hide();
            $(".error-message").hide();
            $("div.wd-input,input,select").removeClass("form-error")
        });
<?php } ?>
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
    function editMe(field,value)
    {
        $(loading).insertAfter('#'+field);
        var data = field+'/'+value;
        $.ajax({
            url: '/company_configs/editMe/',
            data: {
                data : { value : value, field : field }
            },
            type:'POST',
            success:function(data) {
                $('#'+field).removeClass('KO');
                $('#loadingElm').remove();
                location.reload();
            }
        });
    }
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });
    // get sub family
    function listSubFamily(check, subId){
        $('#btnSave').hide();
        var familyId = '';
        $('#ProjectAmrProgramFamilyId option').each(function(){
            if($(this).is(':selected')){
                familyId = $('#ProjectAmrProgramFamilyId').val();
            }
        });
        if(familyId != ''){
            $.ajax({
                url: '/projects/getSubFamily/' + familyId,
                async: true,
                beforeSend: function(){

                },
                success:function(datas) {
                    var datas = JSON.parse(datas);
                    $('#ProjectAmrProgramSubFamilyId').html(datas);
                    $('#btnSave').show();
                    if(check == true){
                        $("#ProjectAmrProgramSubFamilyId").val(subId);
                    }
                }
            });
        } else {
            $('#ProjectAmrProgramSubFamilyId').html('<option value>-- Select --</option>');
            $('#btnSave').show();
        }
    }
    function editCity(project_city_id,company_id, family_id, color){
        var project_city_amr_program = $('#name-' + project_city_id).text();
        $("#ProjectAmrProgramId").val(project_city_id);
        $("#ProjectAmrProgramAmrProgram").val(project_city_amr_program);
        $("#ProjectAmrProgramCompanyId").val(company_id);
        $("#ProjectAmrProgramFamilyId").val(family_id);
        $("#color_1").attr("style",'width: 100%; height: 27px; background-color: '+color+'');
        $("#color_value").val(color);
        //listSubFamily(true, sub_family_id);
        $("#title_form_update_city").html("<?php __("Edit the project AMR program") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $("#ProjectAmrProgramAmrProgram").val('');
        $("#ProjectAmrProgramCompanyId").val('');
        $("#ProjectAmrProgramId").val('');
        $("#ProjectAmrProgramFamilyId").val('');
        $("#ProjectAmrProgramSubFamilyId").val('');
        $("#CityId").val("");
        $("#color_1").attr("style",'width: 100%; height: 27px; background-color: #004380') ;
        $("#color_value").val('#004380');
        $("#title_form_update_city").html("<?php __("Add a new project AMR program") ?>");
        $("#CityName").val("");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    });
</script>