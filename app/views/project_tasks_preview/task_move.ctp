<?php 

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
echo $html->script(array(
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

?>
<style>
	.wd-table-2019 .slick-headerrow-column, .wd-table-2019 .slick-viewport .slick-row .slick-cell, .wd-moveline.slick-cell-move-handler svg{
		padding: 0;
	}
	#layout #dialogDetailValue{
		transform: translate(-50%, -50%);
		z-index: 99999;
		min-width: auto;
	}
	.wd-table-2019 .slick-viewport .slick-row .slick-cell .toggle img{
		margin-top: 10px;
		padding: 0 10px;
	}
	.wd-table-2019 .slick-row.active{
		border-color: #2E85C6;
		border-top-width: 1px;
	}
	.wd-table-2019 .grid-canvas .slick-row.active .slick-cell{
		border-color: #2E85C6;
	}
	.wd-table-2019 .slick-row:hover {
		background-color: #e2eff8;
	}
	#dialogDetailValue:after{
		content: '';
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: rgba(255,255,255,0.75) url(/img/business/wait-1.gif) center no-repeat;
		background-size: 40px;
		z-index: 0;
		opacity: 0;
		transition: all 0.2s ease;
		visibility: hidden;
	}
	#dialogDetailValue.loading:after{
		z-index: 20;
		opacity: 1;
		visibility: visible;
	}
</style>
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
$_sl_columns = array(
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
		'cssClass' => 'wd-moveline slick-cell-move-handler',
		'formatter' => 'Slick.Formatters.moveLine',
		'ignoreExport' => true
	),
	'task_title' => array(
		'id' => 'task_title',
		'field' => 'task_title',
		'name' => __('Task', true),
		'sortable' => false,
		'width' => 350,
		'resizable' => true,
		'formatter' => 'Slick.Formatters.taskNameFormatter',
	),
);

?>

<div class="wd-table-2019 wd-popup-table" id="popup_container" style="width: 100%;height: 600px;">
</div>
<script>
var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
var $this = SlickGridCustom;
$.extend($this,{
	canModified: true,
	onCellChange: function(args){
		return true;
	},
});

$this.fields = {
	id : {defaulValue : 0},
	task_title : {defaulValue : '', allowEmpty : false},
};
// function update_sli_height(){
	// wdPopupTable = $('.wd-popup-table');
	// var heightTable = $(window).height() - wdPopupTable.offset().top - 80;
	// wdPopupTable.height(heightTable);
	// if( SlickGridCustom.getInstance() ) SlickGridCustom.getInstance().resizeCanvas();
// }
// $(window).resize(function(){
	// update_sli_height();
// });

var data_popup = <?php echo json_encode($list_tasks); ?>;
$.extend(Slick.Formatters,{
	moveLine: function(row, cell, value, columnDef, dataContext){
		var _icon = '';
		if(dataContext["depth"] >= 3 && dataContext["is_phase"] != 1){
			_icon = _menu_svg;
		}
		return _icon;
	},
	taskNameFormatter: function(row, cell, value, columnDef, dataContext){
		if (value == null || value == undefined || dataContext === undefined) { return ""; }
		  value = value.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
		  var spacer = "<span style='display:inline-block;height:1px;width:" + (15 * (dataContext["depth"] - 1)) + "px'></span>";
		  var icon = '<img src="/img/new-icon/icon-minus.jpg" class="collapse"  alt="expand" />';
		  if(dataContext["depth"] > 2){
			  icon = '<img src="/img/new-icon/icon-bland.jpg"  alt="expand" />';
		  }
		  if(dataContext["is_nct"] === '1'){
			  icon = '<img src="/img/new-icon/icon-bland-blue.png"  alt="expand" />';
		  }
		  if(dataContext["is_part"] === '1' || dataContext["is_phase"] === '1'){
			  icon = '<img src="/img/new-icon/icon-minus.jpg" class="collapse" alt="expand" />';
		  }
		  if (dataContext._collapsed) {
			  icon = '<img src="/img/new-icon/icon-plus.jpg" class="collapse"  alt="expand" />';
		  }
		  var idx = dataContext['no.'];
		  if (data_popup[idx + 1] && data_popup[idx + 1].depth > data_popup[idx].depth) {
			if (dataContext._collapsed) {
			    return spacer + " <span class='toggle'>"+ icon +"</span>&nbsp;" + value;
			} else {
			   return spacer + " <span class='toggle'>"+ icon +"</span>&nbsp;" + value;
			}
		  } else {
			  return spacer + " <span class='toggle'>"+ icon +"</span>&nbsp;" + value;
		  }
		  
	}
});

function myFilter(item) {
	if (typeof item.parent_id !== 'undefined') {
		var parent = dataView.getItemById(item.parent_id);
		while (parent) {
		  if (parent._collapsed ) {
			  return false;
		  }
		  parent = dataView.getItemById(parent.parent_id);
		}
	}
	return true;
}
// var data_popup = <?php echo json_encode($list_tasks); ?>;
var _sl_columns = <?php echo jsonParseOptions(array_values($_sl_columns), array('editor', 'formatter', 'validator')); ?>;

var slick_options = {
	showHeaderRow: false,
	rowHeight: 40,
	forceFitColumns: true,
}
var dataView;
// initialize the model
dataView = new Slick.Data.DataView({ inlineFilters: true });
dataView.beginUpdate();
dataView.setItems(data_popup);
dataView.setFilter(myFilter);
dataView.endUpdate();
// var dataPopupGrid = $this.init($('#popup_container'), dataView, _sl_columns, slick_options);
var dataPopupGrid = new Slick.Grid($('#popup_container'), dataView, _sl_columns, slick_options);
dataPopupGrid.onClick.subscribe(function (e, args) {
	if ($(e.target).hasClass("collapse")) {
	  var item = dataView.getItem(args.row);
      if (item) {
        if (!item._collapsed) {
          item._collapsed = true;
        } else {
          item._collapsed = false;
        }

        dataView.updateItem(item.id, item);
      }
      e.stopImmediatePropagation();
	}
});
  // wire up model events to drive the grid
  dataView.onRowCountChanged.subscribe(function (e, args) {
    dataPopupGrid.updateRowCount();
    dataPopupGrid.render();
  });

  dataView.onRowsChanged.subscribe(function (e, args) {
    dataPopupGrid.invalidateRows(args.rows);
    dataPopupGrid.render();
  });

dataPopupGrid.setSelectionModel(new Slick.RowSelectionModel());
var moveRowsPlugin = new Slick.RowMoveManager({
	cancelEditOnDrag: true
});

moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, args) {
});

moveRowsPlugin.onMoveRows.subscribe(function (e, args) {
});

function refreshViewAfterDrag(insertBefore, rows, type = 'before'){
	var extractedRows = [], left, right;
	var popupTasks = dataView.getItems();
	if(rows.length > 0){
		var nextItem = popupTasks[rows[0] + 1];
		if(typeof nextItem !== 'undefined' && parseInt(popupTasks[rows[0]].depth) == 3){
			var goNext = true;
			var i = 1;
			var index = rows[0];
			while(goNext == true){
				index++;
				nextItem = popupTasks[index];
				if(typeof nextItem !== 'undefined' && parseInt(nextItem.depth) == 4){
					rows[i] =  rows[i - 1] + 1;
					i++;
					
				}else{
					goNext = false;
				}
			}
			
		}
	}
	if(type == 'append'){
		popupTasks[rows[0]].depth = parseInt(popupTasks[insertBefore - 1].depth) + 1;
	}
	// if(type == 'after'){
		// insertBefore = insertBefore - 1;
	// }
	if(type == 'after' || type == 'before'){
		var _depth = 3;
		if(typeof popupTasks[insertBefore - 1] !== 'undefined'){
			_depth = popupTasks[insertBefore - 1].depth;
			if(popupTasks[insertBefore - 1].is_phase == '1' || popupTasks[insertBefore - 1].is_parent == '1'){
				_depth = parseInt(popupTasks[insertBefore - 1].depth) + 1;
			}
		}
		popupTasks[rows[0]].depth = _depth;
	}
    left = popupTasks.slice(0, insertBefore);
    right = popupTasks.slice(insertBefore, popupTasks.length);

    rows.sort(function(a,b) { return a-b; });

    for (var i = 0; i < rows.length; i++) {
      extractedRows.push(popupTasks[rows[i]]);
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

    popupTasks = left.concat(extractedRows.concat(right));
    var selectedRows = [];
    for (var i = 0; i < rows.length; i++)
      selectedRows.push(left.length + i);

    dataPopupGrid.resetActiveCell();
	// var dataPopupView = dataPopupGrid.getDataView();
    dataView.setItems(popupTasks);
	dataPopupGrid.setData(dataView)
    dataPopupGrid.setSelectedRows(selectedRows);
    dataPopupGrid.render();
}
function saveAfterDrag(idCurrentNode, idOverNode, typeDrag, overNodeIsPhase){
	var tree = Ext.getCmp('pmstreepanel');
	$('#dialogDetailValue').addClass('loading');
	tree.setLoading(i18n("Please wait"));
	$.ajax({
		url: "/project_tasks/drapDrop/" ,
		type: "POST",
		dataType: 'json',
		data: {
			idCurrentNode : idCurrentNode,
			idOverNode : idOverNode,
			type : typeDrag,
			overNodeIsPhase : overNodeIsPhase
		},
		success: function (response, opts) {
			tree.clearFilter();
			tree.handleDnD(response.responseText);
			tree.applyFilters();
			tree.setLoading(false);
			$('#dialogDetailValue').removeClass('loading');
		}
	});
}
dataPopupGrid.registerPlugin(moveRowsPlugin);
var _canvas = dataPopupGrid.getCanvasNode();
dataPopupGrid.onDragStart.subscribe(function (e, dd) {
	if( !canModify )return false;
    var cell = dataPopupGrid.getCellFromEvent(e);
	var _dataGridView = dataView.getItems();
    if (!cell) {
      return;
    }
    dd.row = cell.row;
    if (!_dataGridView[dd.row]) {
      return;
    }
    if (Slick.GlobalEditorLock.isActive()) {
      return;
    }
    e.stopImmediatePropagation();
    dd.mode = "append";

    var selectedRows = dataPopupGrid.getSelectedRows();
    if (!selectedRows.length || $.inArray(dd.row, selectedRows) == -1) {
      selectedRows = [dd.row];
      dataPopupGrid.setSelectedRows(selectedRows);
    }
	var item_selected = dataView.getItem(dd.row);
	if ( item_selected.is_root == '1' || item_selected.is_part == '1' || item_selected.is_phase == '1') {	
		e.stopPropagation();
		return false;
	}
    dd.rows = selectedRows;
    dd.count = selectedRows.length;
	var txt_alert = "Drag '"+ item_selected.task_title +"' to a Task or Phase";
	if(parseInt(item_selected.hasUsed) > 0){
		txt_alert = 'This task is in used/ has consumed.';
	}
	
	if(item_selected.is_parent == '1'){
		txt_alert = "Drag '"+ item_selected.task_title +"' to a Phase";
	}
	
    var proxy = $("<span></span>")
        .css({
          position: "absolute",
          display: "inline-block",
          padding: "4px 10px",
          background: "#e0e0e0",
          border: "1px solid gray",
          "z-index": 9999999,
          "-moz-border-radius": "8px",
          "-moz-box-shadow": "2px 2px 6px silver"
        })
        .text(txt_alert)
        .appendTo("body");
	var rowHeight = dataPopupGrid.getOptions().rowHeight;
    dd.helper = proxy;
	dd.selectionProxy = $("<div class='slick-reorder-proxy'/>")
          .css("position", "absolute")
          .css("zIndex", "99999")
          .css("width", $(_canvas).innerWidth())
          .css("height", rowHeight * selectedRows.length)
          .appendTo(_canvas);

     dd.guide = $("<div class='slick-reorder-guide'/>")
          .css("position", "absolute")
         .css("zIndex", "99998")
          .css("width", $(_canvas).innerWidth())
          .css("top", -1000)
          .appendTo(_canvas);
	dd.insertBefore = -1;
    return proxy;
});

dataPopupGrid.onDrag.subscribe(function (e, dd) {
	var top = e.pageY - $(_canvas).offset().top;
	var rowHeight = dataPopupGrid.getOptions().rowHeight;
	// Chia row thanh 2 phan: tren (1%), duoi (99%)
	var topRowHeight = 0.01 * rowHeight;
	var midRowHeight = 0.99 * rowHeight;
	
    var insertBefore = Math.max(0, Math.min(Math.round(top / dataPopupGrid.getOptions().rowHeight), dataView.getLength()));

    if (insertBefore !== dd.insertBefore) {
        var eventData = {
           "rows": dd.selectedRows,
          "insertBefore": insertBefore
        };
	}
	var item_edit = dataView.getItem(dd.row).depth;
	dd.helper.css({top: e.pageY + 5, left: e.pageX + 5});
	if ( item_edit > 2 || insertBefore >= 2 || dd.row == insertBefore || dd.row == insertBefore - 1) {	
		dd.selectionProxy.css("top", top - 5);
		var posDrag = (insertBefore * rowHeight) - top;
		if (posDrag <= topRowHeight ) {
		   dd.guide.show();
		   dd.guide.css("top", insertBefore * rowHeight);
		   dd.mode = 'before';
		}else {
			dd.mode = 'append';
			dd.guide.hide();
		}
		var dataPopupLength = dataView.getLength();
		if(insertBefore >= dataPopupLength){
			dd.mode = 'after';
			dd.guide.show();
		}
	}
	dd.insertBefore = insertBefore;
});

dataPopupGrid.onDragEnd.subscribe(function (e, dd) {
    dd.helper.remove();
    dd.selectionProxy.remove();
    dd.guide.remove();
	var type = dd.mode;
	var insertBefore = dd.insertBefore;
	if(type == 'append' || type == 'after'){
		insertBefore = dd.insertBefore - 1;
	}	
	//dcn = data of current node
	//don = data of over node
	var dcn = dataView.getItem(dd.row);
	var don = dataView.getItem(insertBefore);
	var next_don = dataView.getItem(insertBefore - 1);
	var next_ItemDrag = dataView.getItem(dd.row + 1);

	if (insertBefore < 2 || insertBefore == dd.row || parseInt(dcn.hasUsed) > 0 || don.is_root == '1' || (type== 'append' && don.is_part == '1') || (dcn.is_parent == '1' && don.is_phase != '1') || insertBefore == 0 || next_don.is_part == '1' || (type == 'append' && (parseFloat(don.depth) > (parseFloat(don.depth_phase) + 1)))) {
		e.stopPropagation();
		return false;
	}
	// NCT task
	if( dcn.is_nct == '1' && don.is_phase != '1' && type == 'append'){
		e.stopPropagation();
		return false;
	}
	if( don.is_nct == '1' && type == 'append' ){
		e.stopPropagation();
		return false;
	}
	
	// predecessor
	if( dcn.predecessor == '1'){
		e.stopPropagation();
		return false;
	}
	
	var overNodeIsPhase = 0; 
	var idOverNode = don.id;
	if(don.depth == 2){
		idOverNode = don.phase_id;
		overNodeIsPhase = 1;
	}
	refreshViewAfterDrag(dd.insertBefore, dd.rows, type);
	saveAfterDrag(dcn.id, idOverNode, type, overNodeIsPhase);
});

</script>
