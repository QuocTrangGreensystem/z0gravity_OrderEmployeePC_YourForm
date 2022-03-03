<?php echo $html->script('jquery.validation.min'); 
echo $html->script(array(
	'history_filter',
	'slick_grid/lib/jquery-ui-1.8.16.custom.min',
	'slick_grid/lib/jquery.event.drag-2.0.min',
	// 'slick_grid/lib/jquery.event.drag-2.2',
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
<?php echo $html->script('jquery.validation.min'); ?>
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
	.slick-viewport .slick-row .slick-cell.wd-moveline{
		padding: 0;
	}
	.wd-moveline.slick-cell-move-handler svg{
		padding: 0;
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
					<div class="wd-tab">
						<?php echo $this->element("admin_sub_top_menu");?>
						<div class="wd-panel">
							<div class="wd-section" id="wd-fragment-1">
								<?php echo $this->element('administrator_left_menu', array(
									'translationPages' => $pages,
									'currentPage' => $currentPage
								)) ?>
								<div class="wd-content">
									<div id="message-place">
										<?php echo $this->Session->flash();	?>
									</div>
									<!--<a href="javascript:void(0);" class="btn add-field" id="add_item" style="margin-right:5px;top: 100px;right: 50px;" title="Add an item" onclick="addNewItem();"></a>-->
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
<div class="msg">Updating...</div>
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
		'moveline' => array(
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
			'formatter' => 'Slick.Formatters.moveLine'
		),
		'original_text' => array(
			'id' => 'original_text',
			'field' => 'original_text',
			'name' => __(' ', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true
		),
		'english' => array(
			'id' => 'english',
			'field' => 'english',
			'name' => __('English', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			// 'cssClass' => 'wd-grey-background',
		),
		'french' => array(
			'id' => 'french',
			'field' => 'french',
			'name' => __('French', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
			// 'cssClass' => 'wd-grey-background',
		),
		'display' => array(
			'id' => 'display',
			'field' => 'display',
			'name' => __('Display', true),
			'width' => 120,
			'editor' => 'Slick.Editors.selectBox',
			'sortable' => true,
			'resizable' => true
		)
	);
	unset( $columns['no']);
	if($isAdminSas == 0) {
		unset( $columns['company_id']);
	}
	$selectMaps = array(
		'company_id' => $current_company,
		'display' => array( 'yes' => __('Yes', true), 'no' => __('No', true)),
	);
	$dataView = array();
	$i = 1;
	foreach ($data as $field){
		$entries = Set::combine($field['TranslationEntry'], '{n}.code', '{n}', '{n}.company_id');
		$entryEn = !empty($entries[$current_company]['eng']) ? $entries[$current_company]['eng'] : '';
		$entryFr = !empty($entries[$current_company]['fre']) ? $entries[$current_company]['fre'] : '';
		$row_data = array(
			'id' => $field['Translation']['id'],
			'no' => $i++,
			'original_text' => $field['Translation']['original_text'],		
			'english' => !empty($entryEn)? $entryEn['text'] : '',
			'french' => !empty($entryFr) ? $entryFr['text'] : '',
			'display' => $field['TranslationSetting']['show'] ? 'yes' : 'no',
			'company_id' => $current_company
		);
		$row_data['moveline'] = '';
		$dataView[] = $row_data;
	}
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
?>
<script>
	var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });
    function editfield(e, id, text){
        $("#TranslationId").val(id);
        $("#original_text").val(text);
        $(e).parent().parent().find('[name=aaa]').each(function(){
            var code = $(this).prop('id').replace('_' + id, '');
            $('#text_' + code).val($(this).val());
        });
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset-form").click(function(){
        $("#TranslationSaveForm input[type='text']").val('');
        $("#original_text").val('');
        $('#TranslationId').val('');

        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");
    });

    <?php if( in_array($currentPage, $dragDropPages) ): ?>

    $('tbody tr').each(function(){
        var me = $(this);
        if( me.find('select').val() == 0 ){
            me.addClass('row-hidden');
        }
    });

    $('.select-display').each(function(){
        var me = $(this);
        me.change(function(){
            var tr = me.parent().children('span');
            me.prop('disabled', true);
            tr.show();
            $.ajax({
                url: '<?php echo $html->url('/translations/saveSetting') ?>',
                type: 'POST',
                data: {
                    data: {
                        id : me.data('setting-id'),
                        translate: me.data('translate'),
                        show : me.val()
                    }
                },
                success: function(data){
                    // console.log(data);
                    tr.hide();
                    me.prop('disabled', false);
                    if( me.val() == 1 ){
                        me.parent().parent().removeClass('row-hidden');
                    } else {
                        me.parent().parent().addClass('row-hidden');
                    }
                },
                error: function(){
                    location.reload();
                }
            });
        });
    });

    var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

    var tbody = $("#table-list-admin tbody").sortable({
        helper: fixHelperModified,
        stop: function(e, ui) {
            $('.msg').show('fast');
            var t = $(this).sortable('disable');
            //update current order
            var result = {data : {
                TranslationSetting : []
                }
            };
            $('tr', ui.item.parent()).each(function (i) {
                var tr = $(this);
                var index = tr.children('td.index');
                //update index
                index.html(i);
                tr.data('order', i);
                //console.log( tr.data('id'), tr.data('order') );
                //get orders
                result.data.TranslationSetting.push({
                    id : tr.data('id'),
                    company_id: <?php echo $company_id ?>,
                    translation_id: tr.data('translate'),
                    setting_order : tr.data('order')
                })
            });
            //saving order
            $.ajax({
                url : '<?php echo $html->url('/translations/saveOrder') ?>',
                type: 'POST',
                data: result,
                success: function(data){
                    $('.msg').hide('fast');
                    t.sortable('enable');
                },
                error: function(){
                    location.reload();
                }
            });
        },
        handle: '.index',
        cancel: '.unsortable',
        opacity: 0.8
    });
    $('.index').disableSelection();
    <?php endif ?>
</script>
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
			enableAddRow: false,  //disable line cuoi cung. 
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
				moveLine: function(row, cell, value, columnDef, dataContext){
					return _menu_svg;
				},
			});
			var data = <?php echo json_encode($dataView); ?>;
			var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
			$this.fields = {
				id : {defaulValue : 0},
				original_text : {defaulValue : '', allowEmpty : false},
				english : {defaulValue : '', allowEmpty : true},
				french : {defaulValue : '', allowEmpty : true},
				display : {defaulValue : '', allowEmpty : false},
				company_id : {defaulValue : current_company, allowEmpty : false}
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
					url : '<?php echo $html->url('/admin_task/saveOrder/') ?>',
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
				dataView.endUpdate();
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