<?php echo $html->script(array(
	'slick_grid/lib/jquery-ui-1.8.16.custom.min',
	'slick_grid/lib/jquery.event.drag-2.0.min',
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
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
.wd-overlay span {
	display: block;
	position: absolute;
	left: 45%;
	top: 10px;
	width: 16px;
	height: 16px;
	background: url(<?php echo $html->url('/img/ajax-loader.gif') ?>) no-repeat;
	display: none;
}
.wd-overlay{
	vertical-align: middle;
	text-align: center;
	position: relative;
}
.wd-overlay select{
	padding: 5px;
}
.btn.add-field{
	position: absolute;
	width: 50px;
	height: 50px;
	line-height: 50px;
	text-align: center;
	border-radius: 50%;
	z-index: 5;
	top: -14px;
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
 .wd-table-container{
	 position: relative;
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
</style>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
}
if (($is_sas == 1) || ($employee_info["Role"]["name"] == "admin")) {
    ?>
    <div id="wd-container-main" class="wd-project-admin">
        <div class="wd-layout">
            <div class="wd-main-content">
                <div class="wd-list-project">
                    <div class="wd-title">
                                    <!--<h2 class="wd-t1"><?php echo __("Project Phases Listing", true); ?></h2>-->
                    </div>
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    //debug()
                    ?>
                    <div class="wd-tab">
                        <?php echo $this->element("admin_sub_top_menu");?>
                        <div class="wd-panel">
                            <div class="wd-section" id="wd-fragment-1">
                                <?php echo $this->element('administrator_left_menu') ?>
                                <div class="wd-content">
                                    <h2 class="wd-t3"></h2>
                                    <ul class="wd-item-v">
                                        <li class="<?php echo ($type == 'financial') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'project_created_values', 'action' => 'index', "/financial")); ?>"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Financial', true); ?></a></li>
                                        <li class="<?php echo ($type == 'business') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'project_created_values', 'action' => 'index', "/business")); ?>"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Business Process', true); ?></a></li>
                                        <li class="<?php echo ($type == 'customer') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'project_created_values', 'action' => 'index', "/customer")); ?>"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Customer', true); ?></a></li>
                                        <li class="<?php echo ($type == 'learning') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'project_created_values', 'action' => 'index', "/learning")); ?>"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Learning & Growth', true); ?></a></li>
                                    </ul>
                                    <?php
                                    switch ($type) {
                                        case 'financial':
                                            $text = (__d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our stakeholders?', true));
                                            $color = "color:#79B810";
                                            break;
                                        case 'business':
                                            $text = (__d(sprintf($_domain, 'Created_Value'), 'What business processes must the project excel at?', true));
                                            $color = "color:#0D6993";
                                            break;
                                        case 'customer':
                                            $text = (__d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our customers?', true));
                                            $color = "color: #8C0E8B";
                                            break;
                                        case 'learning':
                                            $text = (__d(sprintf($_domain, 'Created_Value'), 'How can we sustain our ability to change and Improve?', true));
                                            $color = "color: #A9B300";
                                            break;
                                        default:
                                            $text = '';
                                            $color = '';
                                            break;
                                    }
                                    ?>
                                    <div style="border:1px solid #CCC; padding: 5px"><strong style="font-style: italic; padding-left: 5px ;<?php echo $color ?>"><?php __($text) ?></strong></div>
                                    <div id="message-place">
										<?php echo $this->Session->flash();	?>
									</div>
									<div class="wd-table-container">
										
										<a href="javascript:void(0);" class="btn add-field" id="add_country" style="margin-right:5px;" title="Add an item" onclick="addNewItem();" ></a>
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
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true))); ?>');" class="action-menu-item" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s/%2$s')); ?>">
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
	$columns = array(
		array(
			'id' => 'moveline',
			'field' => 'moveline',
			'name' => '',
			'width' => 40,
			'minWidth' => 40,
			'maxWidth' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 0,
			'behavior' => 'selectAndMove',
			'cssClass' => 'wd-moveline slick-cell-move-handler',
			'formatter' => 'Slick.Formatters.moveLine',
			'ignoreExport' => true
		),
		'description' => array(
			'id' => 'description',
			'field' => 'description',
			'name' => __('Description', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
		),
		'block_name' => array(
			'id' => 'block_name',
			'field' => 'block_name',
			'name' => __('Block name', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
		),
		'next_block' =>array(
			'id' => 'next_block',
			'field' => 'next_block',
			'name' => __('Next block', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.selectBox',
		),
		'value' =>array(
			'id' => 'value',
			'field' => 'value',
			'name' => __('Value', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.numericValueCreated',
		),
		'company_id' => array(
			'id' => 'company_id',
			'field' => 'company_id',
			'name' => __('Company', true),
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
	if($isAdminSas != 1){
		unset( $columns['company_id']);
	}
	$i = 1;
	$dataView = array();

	$selectMaps = array(
		'company_id' => $companies,
		'next_block' => $options,
	);

	$i = 1;

	$current_type_value = !empty($this->params['pass'][0]) ? $this->params['pass'][0] : 'financial';
	foreach ($createdValues as $key => $createdValue) {
		$data = array(
			'id' => $createdValue['ProjectCreatedValue']['id'],
			'no.' => $i++,
			'MetaData' => array()
		);
		
		$data['description'] = $createdValue['ProjectCreatedValue']['description'];
		$data['block_name'] = $createdValue['ProjectCreatedValue']['block_name'];
		$data['next_block'] = $createdValue['ProjectCreatedValue']['next_block'];
		$data['value'] = $createdValue['ProjectCreatedValue']['value'];
		$data['company_id'] = $createdValue['ProjectCreatedValue']['company_id'];
		$data['value_order'] = $createdValue['ProjectCreatedValue']['value_order'];
		$data['type_value'] = $createdValue['ProjectCreatedValue']['type_value'];
		$current_type_value = $createdValue['ProjectCreatedValue']['type_value'];
		$data['moveline'] = '';
		$data['action.'] = '';
		$dataView[] = $data;
	}
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
?>
<script type="text/javascript">
var timeoutID;
var selectMaps = <?php echo json_encode($selectMaps); ?>;
function get_grid_option(){
	var _option ={
		showHeaderRow: true,
		frozenColumn: '',
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
var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
(function($){
	$(function(){
		var $this = SlickGridCustom;
		var current_company = <?php echo json_encode( !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : ''); ?>;
		var current_type_value = <?php echo json_encode($current_type_value); ?>;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend($this,{
			selectMaps : selectMaps,
			canModified: true,
		});
		$.extend(Slick.Formatters,{
			Action : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id, current_type_value));
			},
			moveLine: function(row, cell, value, columnDef, dataContext){
				return _menu_svg;
			},
		});
		$.extend(Slick.Editors,{
			numericValueCreated : function(args){
				$.extend(this, new Slick.Editors.textBox(args));
				this.input.attr('maxlength' , 10).keypress(function(e){
					var key = e.keyCode ? e.keyCode : e.which;
					if(!key || key == 8 || key == 13){
						return;
					}
					var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
					///^[\-+]??$/
					//&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
					if( !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
						e.preventDefault();
						return false;
					}
				});
			},
		});
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            description : {defaulValue : '', allowEmpty : false},
            block_name : {defaulValue : ''},
            next_block : {defaulValue : ''},
            value : {defaulValue : '', allowEmpty : false},
            type_value : {defaulValue : current_type_value},
            value_order : {defaulValue : 0},
            company_id : {defaulValue : current_company, allowEmpty : false},
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
				url : '<?php echo $html->url('/project_created_values/get_order_up/') ?>',
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
    oTable = $('#table-list-admin_wrapper').dataTable( {
        "sScrollY": "150px",
        "sDom": 'R<"H"lfr>t<"F"ip>',
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"//,
        //"sDom": 'lfrtip'
    } );
<?php if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"])))) { ?>
        $("#reset_form_1").click(function(){
            $("#ProjectPhaseId").val("");
            $("#ProjectPhaseName").val("");
            $("#title_form_update_city").html("<?php __("Add a new created value") ?>");
            $("#flashMessage").hide();
            $(".error-message").hide();
            $("div.wd-input,input,select").removeClass("form-error");
        });
<?php } ?>
    function reset_form() {

    }
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });

    function editProjectPhase(project_created_id, project_desc, block_name ,value,company_id,lang){
        $("#ProjectCreatedValueId").val(project_created_id);
        $("#ProjectCreatedValueDescription").val(project_desc);
        $("#text_block_name").val(block_name);
        $("#ProjectCreatedValueValue").val(value);
        $("#ProjectCreatedValueCompanyId").val(company_id);
        //$("#ProjectCreatedValueLanguage").val(lang);
        $("#title_form_update_city").html("<?php __("Edit this created value") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $("#ProjectCreatedValueId").val("");
        $("#ProjectCreatedValueDescription").val("");
        $("#ProjectCreatedValueValue").val("");
        $("#ProjectCreatedValueCompanyId").val("");
        //$("#ProjectCreatedValueLanguage").val("");
        $("#title_form_update_city").html("<?php __("Add a new created value") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    });

    var spamClick = (function($){
        var objLast = null;
        var intTimeout = null;
        var isFirstClick = true;
        return {
            registerEvent : function(groupLink, timeout) {
                spamClick.isFirstClick = true;
                groupLink.live('click', function(event){
                    if (spamClick.isFirstClick) {
                        spamClick.setObj(this);
                        // H?y s? ki?n click c�c link spam
                        clearTimeout(spamClick.getTimeout());
                        // Th?c thi link click cu?i c�ng
                        spamClick.setTimeout(setTimeout("spamClick.execute()", timeout));
                        event.stopImmediatePropagation();
                        return false;
                    } else {
                        spamClick.isFirstClick = true;
                    }
                });
            },
            execute : function() {
                if (this.isFirstClick) {
                    this.isFirstClick = false;
                    $(this.objLast).click();
                }
            },
            setObj : function(obj) {
                this.objLast = obj;
            },
            setTimeout : function(timeout) {
                this.intTimeout = timeout;
            },
            getTimeout : function() {
                return this.intTimeout;
            }
        }
    })(jQuery);
    spamClick.registerEvent($('.wd-up'), 500);
    spamClick.registerEvent($('.wd-down'), 500);
    $(".wd-up").live("click",function(){
        move_up($(this).parent().parent().parent());
    })
    $(".wd-down").live("click",function(){
        move_down($(this).parent().parent().parent());
    })

    function move_up(obj) {
        var upper = $(obj).prev();
        if (upper.length > 0) {
            var order_phase_up = $(obj).find('.order-phase').val();
            var order_value_up = $(obj).find('.wd-up').attr('rel');
            var arr_tmp_up = order_value_up.split("|");
            var phase_id_up = arr_tmp_up[0];
            var company_id_up = arr_tmp_up[1];
            var url = "<?php echo $html->url("/project_created_values/get_order_up/"); ?>";

            var order_phase_0 =  $(upper).before();
            var order_phase_down =   $(order_phase_0).find('.order-phase').val();
            var order_value_down = $(order_phase_0).find('.wd-up').attr('rel');
            var arr_tmp_down = order_value_down.split("|");
            phase_id_down = arr_tmp_down[0];
            company_id_down = arr_tmp_down[1];
            if(company_id_down != company_id_up){
                alert("<?php __("Could not sort because this item is fisrt of this company") ?>");
                return false;
            }
            $(upper).before($(obj).clone());
            // highlight
            $(upper).prev().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                $(this).animate({backgroundColor:'#FFF'}, 'slow');
            });


            $(obj).remove();
            if(order_phase_down !='' && order_phase_up == ''){
                order_phase_up = order_phase_down;
                order_phase_down  = 2;
            }
            if(order_phase_up == ''){ order_phase_up = 2; }
            if(order_phase_down == ''){ order_phase_down = 1; }
            if(order_phase_up == order_phase_down){
                if(order_phase_up != '1'){
                    order_phase_down --;
                    order_phase_up;
                }else{
                    order_phase_down;
                    order_phase_up++;
                }
            }

            $(order_phase_0).find('.order-phase').val(order_phase_up);
            $('.overlay input[rel="'+phase_id_up+"|"+company_id_up+'"]').each(function(){
                $(this).val(order_phase_down);
            });
            $.post(url, {
                created_id_up:phase_id_up,
                company_id_up: company_id_up,
                value_order_up:order_phase_up,
                created_id_down:phase_id_down,
                company_id_down: company_id_down,
                value_order_down:order_phase_down
            },
            function(data1){
                //if(data1){
                //    var kq = data1.split("|");
                //    var up = kq[0];
                //    var down = kq[1];
                //}
            });
        }else{
            // switch category
            var curCat = $(obj).parent();
            var index = $(".wd-selected-new").index(curCat);
            var prevCat = $(".wd-selected-new:eq(" + (index - 1) + ")");
            if (prevCat.length > 0) {
                var ob = $(prevCat).append($(obj).clone());
                $(obj).remove();
                $(prevCat).find("p:last").animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                    $(this).animate({backgroundColor:'#FFF'}, 'slow');
                });
            }
        }


    }

    function move_down(obj) {
        var prever = $(obj).next();
        if (prever.length > 0) {
            var order_phase_up = $(obj).find('.order-phase').val();
            var order_value_up = $(obj).find('.wd-up').attr('rel');
            var arr_tmp_up = order_value_up.split("|");
            var phase_id_up = arr_tmp_up[0];
            var company_id_up = arr_tmp_up[1];



            var order_phase_0 =  $(prever).after();
            var order_phase_down =   $(order_phase_0).find('.order-phase').val();
            var order_value_down = $(order_phase_0).find('.wd-up').attr('rel');
            var arr_tmp_down = order_value_down.split("|");
            phase_id_down = arr_tmp_down[0];
            company_id_down = arr_tmp_down[1];

            if(company_id_down != company_id_up){
                alert("<?php __("Could not sort because this item is last of this company") ?>");
                return false;
            }
            var url = "<?php echo $html->url("/project_created_values/get_order_down/"); ?>";
            $(prever).after($(obj).clone());

            // highlight
            $(prever).next().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                $(this).animate({backgroundColor:'#FFF'}, 'slow');
            });
            $(obj).remove();

            if(order_phase_down !='' && order_phase_up == ''){
                order_phase_up = order_phase_down;
                order_phase_down  = 2;
            }
            if(order_phase_up == ''){ order_phase_up = 2; }
            if(order_phase_down == ''){ order_phase_down = 1; }
            if(order_phase_up == order_phase_down){
                if(order_phase_up != '1'){
                    order_phase_down --;
                    order_phase_up;
                }else{
                    order_phase_down;
                    order_phase_up++;
                }
            }


            $(order_phase_0).find('.order-phase').val(order_phase_up);
            $('.overlay input[rel="'+phase_id_up+"|"+company_id_up+'"]').each(function(){
                $(this).val(order_phase_down);
            });
            $.post(url, {
                created_id_up:phase_id_up,
                company_id_up: company_id_up,
                value_order_up:order_phase_up,
                created_id_down:phase_id_down,
                company_id_down: company_id_down,
                value_order_down:order_phase_down
            }, function(data1){
                //if(data1){
                //    var kq = data1.split("|");
                //    var up = kq[0];
                //    var down = kq[1];
                //
                //}
            });
        }else{
            // switch category
        }
    }
	$('.select-nextblock').each(function(){
		var me = $(this);
		me.change(function(){
			var tr = me.parent().children('span');
			tr.show();
			$.ajax({
				url: '<?php echo $html->url('/project_created_values/saveSettingNextBlock') ?>',
				type: 'POST',
				data: {
					data: {
						id : me.data('id'),
						next_block : me.val()
					}
				},
				success: function(data){
					tr.hide();
				},
				error: function(){
					location.reload();
				}
			});
		});
	});
</script>
