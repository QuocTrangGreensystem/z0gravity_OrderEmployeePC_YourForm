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
<?php echo $html->css('jquery.ui.custom'); 
echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('green/colorpicker'); ?>
<?php echo $html->script('green/colorpicker'); ?>
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
                        <?php
                        if($view != 'ajax'){
                            echo $this->element("admin_sub_top_menu");
                        }
                        ?>
                        <div class="wd-panel">
                            <div class="wd-section" id="wd-fragment-1">
                                <?php
                                if($view != 'ajax'){
                                    echo $this->element('administrator_left_menu');
                                }
                                ?>
                                <div class="wd-content">
									<div id="message-place">
										<?php echo $this->Session->flash();	?>
									</div>
									<div class="wd-input-select">
                                        <label><?php echo __("Activate Manually achievement", true)?></label>
                                        <?php
                                            $option = array(__('No', true), __('Yes', true));
                                            echo $this->Form->input('manually_achievement', array(
                                                'div' => false,
                                                'label' => false,
                                                'onchange' => "editMe('manually_achievement', this.value);",
                                                "class" => "manually_achievement",
                                                "default" => &$companyConfigs['manually_achievement'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
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
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay" rel="%s">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
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
	$is_sas = !empty($employee_info['Employee']['is_sas']);
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
			// 'cssClass' => 'wd-grey-background', //cmt vi mat icon pencil
		)
	);
	if($activateProfile){
		$_columns = array(
			'profile_id' => array(
				'id' => 'profile_id',
				'field' => 'profile_id',
				'name' => __('Profile', true),
				'width' => 250,
				'editor' => 'Slick.Editors.selectBox',
				'sortable' => true,
				'resizable' => true
			)
		);
		$columns = array_merge($columns,$_columns);
	}
	$_columns = array(
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
		'tjm' => array(
			'id' => 'tjm',
			'field' => 'tjm',
			'name' => __('Average Daily Rate', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			// 'cssClass' => 'wd-grey-background',
		)
	);
	$columns = array_merge($columns,$_columns);
	if($isAdminSas == 1){
		$_columns = array(
			'company_id' => array(
				'id' => 'company_id',
				'field' => 'company_id',
				'name' => __('Company', true),
				'width' => 250,
				'editor' => 'Slick.Editors.selectBox',
				'sortable' => true,
				'resizable' => true
			)
		);
		$columns = array_merge($columns,$_columns);
	}
	$_columns = array(
		'add_when_create_project' => array(
			'id' => 'add_when_create_project',
			'field' => 'add_when_create_project',
			'name' => __('Add in a new project', true),
			'width' => 260,
			'editor' => 'Slick.Editors.selectBox',
			'sortable' => true,
			'resizable' => true
		),
		'activated' => array(
			'id' => 'activated',
			'field' => 'activated',
			'name' => __('Activated', true),
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
	$columns = array_merge($columns,$_columns);
	$selectMaps = array(
		'company_id' => $companies,
		'add_when_create_project' => $options,
		'activated' => $options,
		'profile_id' => $profiles,
	);
	$dataView = array();
	$i = 1;
	foreach ($projectPhases as $projectPhase){
		if ($projectPhase['ProjectPhase']['company_id'] != "") {
			$company_id_save = $projectPhase['ProjectPhase']['company_id'];
			$company_name = $companies[$company_id_save];
			if ($parent_companies[$company_id_save] != "") {
				$company_name = $companies[$parent_companies[$company_id_save]] . " --> " . $company_name;
			}
		}
		else { $company_name = " ";}
		$row_data = array(
			'id' => $projectPhase['ProjectPhase']['id'],
			'no' => $i++,
			'name' => $projectPhase['ProjectPhase']['name'],
			'profile_id' => !empty($projectPhase['ProjectPhase']['profile_id']) ? $projectPhase['ProjectPhase']['profile_id'] : '',
			'color' => $projectPhase['ProjectPhase']['color'],
			'phase_order' => $projectPhase['ProjectPhase']['phase_order'],
			'tjm' => !empty($projectPhase['ProjectPhase']['tjm']) ? $projectPhase['ProjectPhase']['tjm'] : '',
			'add_when_create_project' => $projectPhase['ProjectPhase']['add_when_create_project'] ? $projectPhase['ProjectPhase']['add_when_create_project'] : '0',
			'activated' => $projectPhase['ProjectPhase']['activated'] ? $projectPhase['ProjectPhase']['activated'] : '0',
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
var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
(function($){
	$(function(){
		var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var current_company = <?php echo json_encode( !empty($current_company) ? $current_company : ''); ?>;
		var actionTemplate =  $('#action-template').html();
		var orderTemplate = $('#order-template').html();
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
			Order : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(orderTemplate , row));
			},
			moveLine: function(row, cell, value, columnDef, dataContext){
				return _menu_svg;
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
            name : {defaulValue : '', allowEmpty : false},
            company_id : {defaulValue : current_company, allowEmpty : false},
            add_when_create_project : {defaulValue : 0, allowEmpty : true},
            activated : {defaulValue : 1, allowEmpty : true},
            tjm : {defaulValue : '', allowEmpty : true},
            phase_order : {defaulValue : '', allowEmpty : true},
            color : {defaulValue : '#004380', allowEmpty : false},
            profile_id : {defaulValue : '', allowEmpty : true},
        };
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
		
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
				url : '<?php echo $html->url('/project_phases/get_order_up/') ?>',
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
<?php if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"])))) { ?>
        $("#reset_form_1").click(function(){
            $("#ProjectPhaseId").val("");
            $("#ProjectPhaseName").val("");
            $("#title_form_update_city").html("<?php __("Add a new project phase") ?>");
            $("#flashMessage").hide();
            $(".error-message").hide();
            $("div.wd-input,input,select").removeClass("form-error");
        });
<?php } ?>
    function reset_form() {

    }
    $("#btnSave").click(function(){
        $('.panel_form_1 .content_1').hide();
        $("#flashMessage").hide();
    });

    function editProjectPhase(project_phase_id, project_phase_name,company_id,color,tjm,activated,profile_id){
        $('.panel_form_1 .content_1').hide();
        $("#ProjectPhaseId").val(project_phase_id);
        $("#ProjectPhaseName").val(project_phase_name);
        $("#ProjectPhaseCompanyId").val(company_id);
        $("#ProjectPhaseActivated").val(activated);
        $('#ProjectPhaseProfileId').val(profile_id);
        $("#color_1").attr("style",'width: 100%; height: 27px; background-color: '+color+'') ;
        $("#color_value").val(color);
        $("#ProjectPhaseTjm").val(tjm);
        $("#title_form_update_city").html("<?php __("Edit this project phase") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $('.panel_form_1 .content_1').hide();
        $("#ProjectPhaseCompanyId").val('');
        $("#ProjectPhaseId").val("");
        $("#ProjectPhaseName").val("");
        $("#ProjectPhaseProfileId").val('');
        $("#color_1").attr("style",'width: 100%; height: 27px; background-color: #004380') ;
        $("#color_value").val('#004380');       
        $("#ProjectPhaseTjm").val('');
        $("#title_form_update_city").html("<?php __("Add a new project phase") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    });

    
    $('.panel_form_1 .content_1').hide();
    $('.panel_form_1 .trigger_1').bind('click', function(){
        $("#click_trigger").val("0");
        $('.content_1').slideToggle();
    });
    $("#ProjectPhaseName, #ProjectPhaseCompanyId").click(function(){
        $('.panel_form_1 .content_1').hide();
    })
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
            }
        });
    }
</script>