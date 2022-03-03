<?php
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity',
        'jquery.multiSelect',
        'projects',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit'
    ));
    echo $this->Html->script(array(
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid_custom',
        'history_filter',
        'jquery.multiSelect',
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drop-2.0.min',
        'slick_grid/plugins/slick.rowselectionmodel',
        'slick_grid/plugins/slick.rowmovemanager',
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity',
        'jquery.ui.touch-punch.min'
    ));
    echo $this->element('dialog_projects');
	$canModified = (!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite);
?>
<?php echo $html->css('preview/layout'); ?>
<?php echo $html->css('preview/slickgrid'); ?>
<?php echo $html->css('preview/project-team'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-pane.slick-pane-header.slick-pane-right {
	}
	.slick-pane.slick-pane-header.slick-pane-left {
		background-color: #247FC3;
	}
	.ui-state-default .slick-header-column {
		background-color: #247FC3;
	}
	.wd-hover-advance-tooltip {
		margin-left: 10px;
	}
</style>
<!-- export excel  -->
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_parts', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="<?php echo $html->url("/project_phase_plans_preview/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"><span><?php __('Gantt+') ?></span></a>
                    <a href="javascript:void(0);" class="export-excel-icon-all" id="export-submit" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="height: 400px">
                </div>
				<?php if( $canModified){ ?>
					<div class="wd-popup-container">
						<div class="wd-popup"></div>
						<a class="add-new-item" href="javascript:void(0);" onclick="addNewPartButton();"><img title="Add an item" src="/img/new-icon/add.png"></a>
					</div>
				<?php } ?>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
        </div>
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
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        'cssClass' => 'text-center slick-cell-move-handler'
    ),
    array(
        'id' => 'title',
        'field' => 'title',
        'name' => __('Part', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'validator' => 'DateValidate.isUnique',
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'description',
        'field' => 'description',
        'name' => __('Description', true),
        'width' => 450,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textArea'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __(' ', true),
        'width' => 50,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}
$i = 1;
$dataView = array();
$selectMaps = array();
foreach ($projectParts as $projectPart) {
    $data = array(
        'id' => $projectPart['ProjectPart']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['title'] = $projectPart['ProjectPart']['title'];
    $data['description'] = $projectPart['ProjectPart']['description'];
    // $data['weight'] = $projectPart['ProjectPart']['weight'];
    $data['action.'] = '';
    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Already exist' => __('Already exist', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-actions">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>"></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    function update_table_height(){
		var ControlGrid = SlickGridCustom.getInstance();
		var wdTable = $('.wd-layout').find('.wd-table');
		
		console.log('update_table_height');
		var mql = window.matchMedia('printer');
		var z_printing = window.isZPrinting||false;
		if( mql.matches || z_printing){
			layout_for_print = true;
			console.log('printer');
			if( ControlGrid) ControlGrid.set_full_height();
		}else{
			console.log('Not a printer');
			layout_for_print = false;
			console.log( $(window).height());
			var heightTable = $(window).height() - wdTable.offset().top - 80;
			wdTable.css({
				height: heightTable,
			});
			console.log( heightTable);
			if( ControlGrid) ControlGrid.resizeCanvas(); 
			// setupScroll();
		}
	}
	update_table_height();
	$(window).resize(function () {
		update_table_height();
    });
    var DateValidate = {};
    (function($){
        var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
        // For validate date
        var projectName = <?php echo json_encode($projectName['Project']); ?>;
        var actionTemplate =  $('#action-template').html();

        DateValidate.isUnique = function(value,args){
            var result = true;
            $.each(args.grid.getData().getItems() , function(undefined,dx){
                if(args.item.id && args.item.id == dx.id){
                    return true;
                }
                return (result = (dx.title.toLowerCase() != value.toLowerCase()));
            });
            return {
                valid : result,
                message : $this.t('Already exist')
            };
        }
        $.extend(Slick.Formatters,{
            Action : function(row, cell, value, columnDef, dataContext){
                return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                dataContext.project_id,dataContext.title), columnDef, dataContext);
            }
        });
        var  data = <?php echo json_encode($dataView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            project_id : {defaulValue : projectName['id'], allowEmpty : false},
            title : {defaulValue : '' , allowEmpty : false},
            description : {defaulValue : ''},
            weight : {defaulValue : 0 }
        };
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        var dataGrid = $this.init($('#project_container'), data, columns, {
            frozenColumn: 1
        });
        dataGrid.setSortColumns('weight' , true);
        //drag N drop 2014/20/12
        //added by QN
        dataGrid.setSelectionModel(new Slick.RowSelectionModel());
        $('.row-number').parent().addClass('row-number-custom');
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
            return true;
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
                url : '<?php echo $html->url('/project_parts/order/' . $projectName['Project']['id']) ?>',
                type : 'POST',
                data : orders,
                success : function(){
                },
                error: function(){
                    location.reload();
                }
            });
            dataGrid.resetActiveCell();
            var dataView = dataGrid.getDataView();
            dataView.beginUpdate();
            //if set data via grid.setData(), the DataView will get removed
            //to prevent this, use DataView.setItems()
            dataView.setItems(data);
            //dataView.setFilter(filter);
            //updateFilter();
            dataView.endUpdate();
            // dataGrid.getDataView.setData(data);
            dataGrid.setSelectedRows(selectedRows);
            dataGrid.render();
        });
        dataGrid.registerPlugin(moveRowsPlugin);
        dataGrid.onDragInit.subscribe(function (e, dd) {
            // prevent the grid from cancelling drag'n'drop by default
            e.stopImmediatePropagation();
        });
        dataGrid.onCellChange.subscribe(function(e, args){
            var columns = args.grid.getColumns(),
                col, cell = args.cell;
            do {
                cell++;
                if( columns.length == cell )break;
                col = columns[cell];
            } while (typeof col.editor == 'undefined');

            if( cell < columns.length ){
                args.grid.gotoCell(args.row, cell, true);
            } else {
                //end of row
                try {
                    args.grid.gotoCell(args.row + 1, 0);
                } catch(ex) {}
            }
        });
		addNewPartButton = function(){
			dataGrid.gotoCell(data.length, 1, true);
		}
    })(jQuery);
</script>
