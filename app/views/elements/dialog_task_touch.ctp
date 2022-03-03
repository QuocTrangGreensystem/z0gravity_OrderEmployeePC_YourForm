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
	.wd-layout-heading ul{
		 width: 100%;
	}
	#template_upload.show, .light-popup.show{
	     opacity: 0.9;
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
$touch_i18n = array(
    'Drag %TITLE% to a Task or Phase' => __('Drag %TITLE% to a Task or Phase', true),
    'Drag %TITLE% to a Phase' => __('Drag %TITLE% to a Phase', true),
    'This task is in used/ has consumed' => __('This task is in used/ has consumed', true),
);
?>

<div class="wd-table-2019 wd-popup-table" id="popup_container" style="width: 100%;height: 600px;">
</div>
<script>
var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
var touch_i18n = <?php echo json_encode($touch_i18n); ?>;
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

var data_popup = getDataView();
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
		  if(dataContext["is_nct"] == 1){
			  icon = '<img src="/img/new-icon/icon-bland-blue.png"  alt="expand" />';
		  }
		  if(dataContext["is_part"] == 1 || dataContext["is_phase"] === 1){
			  icon = '<img src="/img/new-icon/icon-minus.jpg" class="collapse" alt="expand" />';
		  }
		  if(dataContext.hasChild == 1){
				icon = '<img src="/img/new-icon/icon-minus.jpg" class="collapse"  alt="expand" />';  
		  }
		  if (dataContext._collapsed == 1 && dataContext.hasChild == 1) {
			  icon = '<img src="/img/new-icon/icon-plus.jpg" class="collapse"  alt="expand" />';
		  }
		  var idx = dataContext['no.'];
		  if (data_popup[idx + 1] && data_popup[idx + 1].depth > data_popup[idx].depth) {
			if (dataContext._collapsed == 1) {
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
	if (typeof item !== 'undefined') {
		var parent = dataView.getItemById(item.parent_id);
		while (parent) {
		  if (parent._collapsed == 1) {
			  return false;
		  }
		  parent = dataView.getItemById(parent.parent_id);
		}
		return true;
	}
	return false;
}
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
			item._collapsed = 1;
        } else {
			item._collapsed = 0;
        }
        
      }
	  dataView.updateItem(item.id, item);
      e.stopImmediatePropagation();
	}
});

function getChildItem(item_id){
	var tree = Ext.getCmp('pmstreepanel');
	var index = tree.getStore().findBy(function (record) {
	   return record.data.id == item_id;
	});

	var childNode = tree.getStore().getData().getAt(index).childNodes;
	var listChild = [];
	if(childNode.length > 0){
		listChild = formatDataView(childNode);
	}
	return listChild;
}
var ext_tasks = [];
var ext_index = 0;
function secusiveTask(node){
	if(node.length > 0){
		$.each(node, function(i, it){
			ext_tasks[ext_index] = it.data;
			if(it.childNodes.length > 0){
				ext_tasks[ext_index]['hasChild'] = 1;
			}
			ext_index++;
			if(it.childNodes.length > 0){
				secusiveTask(it.childNodes);
			}
		});
	}else{
		ext_tasks[ext_index++] = node.data;
	}
}
function getDataView(){
	ext_index = 0;
	var tree = Ext.getCmp('pmstreepanel');
	var store_tasks = tree.getStore().getData().items[0];
	ext_tasks[ext_index++] = store_tasks.data;
	if( store_tasks.childNodes.length > 0){
		$.each(store_tasks.childNodes, function(i, it){
			ext_tasks[ext_index] = it.data;
			if(it.childNodes.length > 0){
				ext_tasks[ext_index]['hasChild'] = 1;
			}
			ext_index++;
			if(it.childNodes.length > 0){
				secusiveTask(it.childNodes);
			}
			
		});
	}
	return formatDataView(ext_tasks);
}
function formatDataView(nodes){
	var list_child = [];
	if(nodes.length > 0){
		var depth_phase = 2;
		$.each( nodes, function(i, it){
			var is_root = (it['id'] == 'root') ? 1 : 0;
			list_child[i] = {};
			list_child[i]['id'] = it['id'];
			list_child[i]['task_title'] = it['task_title'];
			list_child[i]['depth'] = it['depth'];
			list_child[i]['phase_id'] = it['phase_id'] ? it['phase_id'] : 0;
			list_child[i]['hasUsed'] = it['hasUsed'] ? it['hasUsed'] : 0;
			list_child[i]['parent_id'] = it['parent_id'];
			var is_parent = 0;
			if(is_root == 0){
				if(it['children'] && it['children'].length > 0){
					is_parent = 1;
				}
			}
			list_child[i]['is_parent'] = is_parent;
			list_child[i]['is_root'] = is_root;
			list_child[i]['is_part'] = it['is_part'] ? 1 : 0;
			list_child[i]['is_nct'] = it['is_nct'];
			list_child[i]['predecessor'] = it['predecessor'];
			list_child[i]['is_phase'] = it['is_phase'] ? 1 : 0;
			list_child[i]['_collapsed'] = (it['expanded'] && it['expandable']) ? 0 : 1;
			list_child[i]['hasChild'] = it['hasChild'] ? 1 : 0;
			if(it['is_phase']){
				depth_phase = it['depth'];
			}
			list_child[i]['depth_phase'] = depth_phase;
		});
	}
	
	return list_child;
}
  // wire up model events to drive the grid
  dataView.onRowCountChanged.subscribe(function (e, args) {
	dataView.setFilter(myFilter);
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
	// console.log(item_selected);
	if ( item_selected.is_root == 1 || item_selected.is_part == 1 || item_selected.is_phase == 1) {	
		e.stopPropagation();
		return false;
	}
	
    dd.rows = selectedRows;
    dd.count = selectedRows.length;
	var txt_alert = touch_i18n['Drag %TITLE% to a Task or Phase'].replace('%TITLE%', item_selected.task_title );
	
	if(parseInt(item_selected.hasUsed) > 0){
		txt_alert = touch_i18n['This task is in used/ has consumed'];
	}
	
	if(item_selected.is_parent == '1'){
		txt_alert = touch_i18n['Drag %TITLE% to a Phase'].replace('%TITLE%', item_selected.task_title );
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
	
    var insertPosition = Math.max(0, Math.min(Math.round(top / dataPopupGrid.getOptions().rowHeight), dataView.getLength()));
	
	posDragDrop = getMapingPosition(insertPosition, dd.row);

	insertBefore = posDragDrop.posOverDrag;
	dd.rowPos = posDragDrop.posSelected;

    if (insertBefore !== dd.insertBefore) {
        var eventData = {
           "rows": dd.selectedRows,
          "insertBefore": insertBefore
        };
	}
	var dataItems = dataView.getItems();
	var item_edit = dataItems[dd.rowPos].depth;
	dd.helper.css({top: e.pageY + 5, left: e.pageX + 5});
	if ( item_edit > 2 || insertBefore >= 2 || dd.rowPos == insertBefore || dd.rowPos == insertBefore - 1) {	
		dd.selectionProxy.css("top", top - 5);
		var posDrag = (insertPosition * rowHeight) - top;
		if (posDrag <= topRowHeight ) {
		   dd.guide.show();
		   dd.guide.css("top", insertPosition * rowHeight);
		   dd.mode = 'before';
		}else {
			dd.mode = 'append';
			dd.guide.hide();
		}
		if(insertBefore >= dataItems.length){
			dd.mode = 'after';
			dd.guide.show();
		}
	}
	// case collapse and type = apppend
	
	if(dd.mode == 'append'){
		var realPosInsert = dataView.getItem(insertPosition - 1);
		if(typeof realPosInsert !== 'undefined' && realPosInsert._collapsed == 1){
			dd.mode = 'after';
		}
	} 
	dd.insertBefore = insertBefore;
});

dataPopupGrid.onDragEnd.subscribe(function (e, dd) {
    dd.helper.remove();
    dd.selectionProxy.remove();
    dd.guide.remove();
	var dataItems = dataView.getItems();
	var type = dd.mode;
	
	var insertBefore = dd.insertBefore;
	
	if(type == 'after'){
		insertBefore = dd.insertBefore - 1;
	}
		
	//dcn = data of current node
	//don = data of over node
	var dcn = dataItems[dd.rowPos];
	var don = dataItems[insertBefore];
	var next_don = dataItems[insertBefore - 1];
	var next_ItemDrag = dataItems[dd.rowPos + 1];
	
	if (insertBefore < 2 || insertBefore == dd.rowPos || parseInt(dcn.hasUsed) > 0 || don.is_root == 1 || (type== 'append' && don.is_part == 1) || (dcn.is_parent == 1 && don.is_phase != 1) || insertBefore == 0 || next_don.is_part == 1 || (type == 'append' && (don.depth_phase - don.depth > 1))) {
		e.stopPropagation();
		return false;
	}
	// NCT task
	if( dcn.is_nct == 1 && don.is_phase != 1 && type == 'append'){
		e.stopPropagation();
		return false;
	}
	if( don.is_nct == 1 && type == 'append' ){
		e.stopPropagation();
		return false;
	}
	
	// predecessor
	if( dcn.predecessor == 1){
		e.stopPropagation();
		return false;
	}
	
	var overNodeIsPhase = 0; 
	var idOverNode = don.id;
	if(don.depth == 2){
		idOverNode = don.phase_id;
		overNodeIsPhase = 1;
	}
	
	refreshViewAfterDrag(dd.insertBefore, dd.rowPos, type);
	
	// case move to last item
	if(dataItems.length == insertBefore + 1){
		insertBefore = insertBefore + 1;
	}
	
	var c_parent_id = don.parent_id;
	if(type == 'append'){
		c_parent_id = don.id;
	}else if(type == 'after' || type == 'before'){
		c_parent_id = don.parent_id;
		if(type == 'after' && (don.is_phase == 1 || don.is_parent == 1)){
			c_parent_id = don.id;
		}
		if(don.is_phase != 1 && don.is_part != 1){
			c_parent_id = don.parent_id;
		}else{
			if(dataItems[insertBefore - 1].is_phase == 1 || dataItems[insertBefore - 1].is_parent == 1){
				c_parent_id = dataItems[insertBefore - 1].id;
			}
			if(type == 'before'){
				if(typeof dataItems[insertBefore - 1] !== 'undefined' && (dataItems[insertBefore - 1].is_phase == 1 || dataItems[insertBefore - 1].is_parent == 1)){
					c_parent_id = dataItems[insertBefore - 1].id;
				}else{
					c_parent_id = dataItems[insertBefore - 1].parent_id;
				}
			}
		}
	}
	dcn.parent_id = c_parent_id;
	dataView.updateItem(dcn.id, dcn);
	
	// Check parent collapse.
	parentNode = dataView.getItemById(c_parent_id);
	if(typeof parentNode !== 'undefined' && parentNode._collapsed == 1){
		parentNode._collapsed = 0;
		dataView.updateItem(parentNode.id, parentNode);		
	}
	
	// if insert before phase
	if((don.is_phase == 1 || don.is_part == 1) && type == "before"){
		if(dataItems[insertBefore - 1].is_phase != 1){
			type = 'after';
			overNodeIsPhase = 0;
			idOverNode = dataItems[insertBefore - 1].id;
		}else{
			type = 'append';
			overNodeIsPhase = 1;
			idOverNode = dataItems[insertBefore - 1].phase_id;
		}
	}
	saveAfterDrag(dcn.id, idOverNode, type, overNodeIsPhase);
});

function refreshViewAfterDrag(insertBefore, drow, type = 'before'){
	var extractedRows = [], left, right;
	var popupTasks = dataView.getItems();
	var drows = [];
	drows[0] = drow;
	var nextItem = popupTasks[drows[0] + 1];
	if(typeof nextItem !== 'undefined' && parseInt(popupTasks[drows[0]].depth) == 3){
		var goNext = true;
		var i = 1;
		var index = drows[0];
		while(goNext == true){
			index++;
			nextItem = popupTasks[index];
			if(typeof nextItem !== 'undefined' && parseInt(nextItem.depth) == 4){
				drows[i] =  drows[i - 1] + 1;
				i++;
				
			}else{
				goNext = false;
			}
		}
		
	}
	if(type == 'append'){
		popupTasks[drows[0]].depth = parseInt(popupTasks[insertBefore - 1].depth) + 1;
	}
	if(popupTasks.length == insertBefore + 1){
		insertBefore = insertBefore + 1;
	}
	if(type == 'after' || type == 'before'){
		var _depth = 3;
		if(typeof popupTasks[insertBefore] !== 'undefined' && popupTasks[insertBefore].is_part != 1 && popupTasks[insertBefore].is_phase != 1 && type != 'after'){
			_depth = popupTasks[insertBefore].depth;
		}else if(typeof popupTasks[insertBefore - 1] !== 'undefined'){
			_depth = popupTasks[insertBefore - 1].depth;
			if(popupTasks[insertBefore - 1].is_phase == 1 || popupTasks[insertBefore - 1].is_parent == 1){
				_depth = parseInt(popupTasks[insertBefore - 1].depth) + 1;
			}
		}
		popupTasks[drows[0]].depth = _depth;
	}
    left = popupTasks.slice(0, insertBefore);
    right = popupTasks.slice(insertBefore, popupTasks.length);

    drows.sort(function(a,b) { return a-b; });

    for (var i = 0; i < drows.length; i++) {
      extractedRows.push(popupTasks[drows[i]]);
    }
    drows.reverse();
	
    for (var i = 0; i < drows.length; i++) {
      var row = drows[i];
      if (row < insertBefore) {
        left.splice(row, 1);
      } else {
        right.splice(row - insertBefore, 1);
      }
    }

    popupTasks = left.concat(extractedRows.concat(right));
    var selectedRows = [];
    for (var i = 0; i < drows.length; i++)
      selectedRows.push(left.length + i);

    dataPopupGrid.resetActiveCell();
	// var dataPopupView = dataPopupGrid.getDataView();
	// console.log(popupTasks)
    dataView.setItems(popupTasks);
	// dataPopupGrid.setData(dataView)
    // dataPopupGrid.setSelectedRows(selectedRows);
    // dataPopupGrid.render();
}
var valueLastUpdate;
function saveAfterDrag(idCurrentNode, idOverNode, typeDrag, overNodeIsPhase){
	$('#dialogDetailValue').addClass('loading');
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
			$('#dialogDetailValue').removeClass('loading');
			valueLastUpdate = response;
		}
	});
}
function getMapingPosition(insertBefore, selectedPosition){
	var popupTasks = dataView.getItems();
	var posSelected = selectedPosition;
	var dragItem = dataView.getItem(posSelected);
	var dataLength = dataView.getLength();
	if(insertBefore >= dataLength){
		insertBefore = dataLength - 1;
	}
	// case collapse and type = apppend
	var overDrag = dataView.getItem(insertBefore);
	if(typeof dragItem === 'undefined'){
		e.stopPropagation();
		return false;
	}
	var posOverDrag = insertBefore;
	var clp = 0, d = 0;
	$.each( popupTasks, function(i, val){
		if(dragItem['id'] == val.id){
			posSelected = i;
		}
		if(overDrag['id'] == val.id){
			posOverDrag = i;
		}
	});
	return {posSelected, posOverDrag};
}
$('#dialogDetailValue').on('click','#closePopup', function(){
	if(!$.isEmptyObject(valueLastUpdate)){
		var tree = Ext.getCmp('pmstreepanel');
		tree.setLoading(i18n("Please wait"));
		tree.clearFilter();
		tree.handleDnD(valueLastUpdate.responseText);
		tree.applyFilters();
		Ext.Ajax.on('requestcomplete', function(){
            tree.setLoading(false);
        });
		// set empty value
	    valueLastUpdate = {};
	}
});
</script>
