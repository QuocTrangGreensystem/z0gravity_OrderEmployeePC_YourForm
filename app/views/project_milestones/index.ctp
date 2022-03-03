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
    'history_filter',
    'jquery.multiSelect',
    // 'responsive_table',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
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
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min'
));
echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    p {
        margin-bottom: 10px;
    }
    .milestone-mi{
        background-image: url("/img/mi.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    .milestone-green{
        background-image: url("/img/mi-green.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    .milestone-blue{
        background-image: url("/img/mi-blue.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    .milestone-orange{
        background-image: url("/img/mi-orange.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
    .slick-viewport-right{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_milestones', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"></a>
                    <a href="javascript:void(0);" class="export-excel-icon-all" id="export-submit" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px;" onclick="addNewMilestonesButton();" title="<?php __('Add an item') ?>"></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <p><?php __('In the column #, drag and drop to reorder') ?></p>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%;height:410px;">
                </div>
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
$_columns[] = array(
    'id' => 'no.',
    'field' => 'no.',
    'name' => '#',
    'width' => 40,
    'sortable' => false,
    'resizable' => false,
    'noFilter' => 1,
    'behavior' => 'selectAndMove',
    'cssClass' => 'slick-cell-move-handler'
);
if($displayParst){
    $_columns[] = array(
        'id' => 'part_id',
        'field' => 'part_id',
        'name' => __('Part', true),
        'width' => 200,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        //'formatter' => 'Slick.Formatters.ColorMile'
    );
}
$columns = array(
    array(
        'id' => 'project_milestone',
        'field' => 'project_milestone',
        'name' => __('Milestone', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.iconColor'
    ),
    array(
        'id' => 'milestone_date',
        'field' => 'milestone_date',
        'name' => __('Date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate',
        //'formatter' => 'Slick.Formatters.ColorMile'
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    )
);
// them alert colums.
$listkey = array();
if(!empty($listAlert)){
    foreach ($listAlert as $_id => $_name) {
        $num = $numberAlert[$_id];
        $listkey[$_id] = 'alert_' . $_id;
        $columns[] = array(
            'id' => 'alert_' . $_id,
            'field' => 'alert_' . $_id,
            'name' => $_name,
            'width' => 150,
            'sortable' => false,
            'resizable' => true,
        );
    }
}
$columns[] = array(
    'id' => 'validated',
    'field' => 'validated',
    'name' => __('Validated', true),
    'width' => 150,
    'sortable' => false,
    'resizable' => true,
    'editor' => 'Slick.Editors.selectBox',
    //'formatter' => 'Slick.Formatters.ColorMile'
);
$columns[] = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
$columns = array_merge($_columns, $columns);
$i = 1;
foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}
$dataView = array();
$parts = array('0' => '&nbsp;') + $parts;
$selectMaps = array(
    'validated' => array("no" => __('No', true), "yes"=>__('Yes', true)),
    'part_id' => $parts
);

foreach ($projectMilestones as $projectMilestone) {
    $data = array(
        'id' => $projectMilestone['ProjectMilestone']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['project_milestone'] = $projectMilestone['ProjectMilestone']['project_milestone'];
    $data['milestone_date'] = $str_utility->convertToVNDate($projectMilestone['ProjectMilestone']['milestone_date']);
    $data['validated'] = $projectMilestone['ProjectMilestone']['validated'] ? 'yes' : 'no';
    $data['part_id'] = $projectMilestone['ProjectMilestone']['part_id'];
    $data['weight'] = $projectMilestone['ProjectMilestone']['weight'];
    $data['action.'] = '';
    foreach ($listkey as $key => $value) {
        $date = $str_utility->convertToVNDate($projectMilestone['ProjectMilestone']['milestone_date']);
        $num = $numberAlert[$key];
        $t = '-' . $num . ' day';
        $data[$value] = date('d-m-Y', strtotime($t, strtotime($date)));
    }
    $dataView[] = $data;
}
$projectName['Project']['start_date'] = $str_utility->convertToVNDate($projectName['Project']['start_date']);
$projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['end_date']);
if ($projectName['Project']['end_date'] == "" || $projectName['Project']['end_date'] == '0000-00-00') {
    $projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['planed_end_date']);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Milestone date must between %s and %s' => __('Milestone date must between %s and %s', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
function set_table_height(){
	if ( !wdTable.length ) return;
	var heightTable = $(window).height() - wdTable.offset().top - 40;
	//heightTable = (heightTable < 550) ? 550 : heightTable;
	wdTable.css({
		height: heightTable,
	});
}
$(document).on('ready', function(){
	console.log( 'ready');
    set_table_height();
});
$(window).on('resize', function(){
	console.log( 'resize');
    set_table_height();
});
set_table_height();
    var DateValidate = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile )|| ($_isProfile && $_canWrite)); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var listkey = <?php echo json_encode($listkey) ?>;
            var numberAlert = <?php echo json_encode($numberAlert) ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }
            DateValidate.startDate = function(value){
                value = getTime(value);
                if(projectName['start_date'] == ''){
                    _valid = true;
                    _message = '';
                    //_message = $this.t('Start-Date or End-Date of Project are missing. Please input these data before full-field this date-time field.');
                } else {
                    //_valid = value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']);
                    //_message = $this.t('Date closing must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date']);
                    _valid = value >= getTime(projectName['start_date']);
                    _message = $this.t('Milestone date must larger %1$s' ,projectName['start_date']);
                }
                return {
                    valid : _valid,
                    message : _message
                };
            }
            var actionTemplate =  $('#action-template').html();
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.project_milestone), columnDef, dataContext);
                },
                ColorMile : function(row, cell, value, columnDef, dataContext){
                    var rightnow = new Date();
                    var dateConvert = dataContext.milestone_date.split("-");
                    var dateCheck = new Date(dateConvert[2]+'-'+dateConvert[1]+'-'+dateConvert[0]);
                    if((dateCheck<rightnow)&&(dataContext.validated=='no')){
                        if(columnDef.id == 'validated'){
                            return '<span style="color: blue;">' + $this.selectMaps.validated[value] + '</span>';
                        }
                        return '<span style="color: blue;">' + value + '</span>';
                    }
                    if((dateCheck>=rightnow)&&(dataContext.validated=='no')){
                        if(columnDef.id == 'validated'){
                            return '<span style="color: red;">' + $this.selectMaps.validated[value] + '</span>';
                        }
                        return '<span style="color: red;">' + value + '</span>';
                    }
                    if(dataContext.validated=='yes'){
                        if(columnDef.id == 'validated'){
                            return '<span style="color: green;">' + $this.selectMaps.validated[value] + '</span>';
                        }
                        return '<span style="color: green;">' + value + '</span>';
                    }
                },
                iconColor: function(row, cell, value, columnDef, dataContext){
                    var rightnow = new Date();
                    var dateConvert = dataContext.milestone_date.split("-");
                    var dateCheck = new Date(dateConvert[2]+'-'+dateConvert[1]+'-'+dateConvert[0]);
                    if(dataContext.validated=='yes'){
                        return '<i class="milestone-green">&nbsp</i><span>' + value + '</span>';
                    } else {
                        if (dateCheck < rightnow) {
                            return '<i class="milestone-mi">&nbsp</i><span>' + value + '</span>';
                        } else if(dateCheck > rightnow) {
                            return '<i class="milestone-blue">&nbsp</i><span>' + value + '</span>';
                        } else {
                            return '<i class="milestone-orange">&nbsp</i><span>' + value + '</span>';
                        }
                    }
                }
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.onCellChange = function(args){
                if(args.item && args.item.validated == ''){
                    args.item.validated = 'no'
                }
                if(args.item && args.item.milestone_date != ''){
                    var columnId = args.column.id;
                    columnId = columnId.substring(0, 3);
                    var date = args.item.milestone_date;
                    $.each(listkey, function(_id, _key){
                        var val = numberAlert[_id];
                        var _date;
                        _date = diffDate(date, val);
                        args.item[_key] = _date;
                    });
                }
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
                return true;
            };
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_milestone : {defaulValue : '' , allowEmpty : false},
                milestone_date : {defaulValue : '', allowEmpty : false},
                validated : {defaulValue : ''},
                part_id : {defaulValue: 0},
                weight: {defaulValue: 0}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            dataGrid = $this.init($('#project_container'),data,columns, {
                frozenColumn: 2
            });

            dataGrid.setSortColumns('weight' , true);
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
            $(dataGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
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
                    url : '<?php echo $html->url('/project_milestones/order/' . $projectName['Project']['id']) ?>',
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
            // add new colum grid
            //ControlGrid = $this.init($('#project_container'),data,columns);
            addNewMilestonesButton = function(){
                dataGrid.gotoCell(data.length, 1, true);
            };
			$(window).on('resize', function(){
				dataGrid.resizeCanvas();
			});
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
                if( text != '' ){
                    $(ind).css('border', 'solid 2px orange');
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
            // HistoryFilter.stask = '{}';
            // HistoryFilter.send();
            // $('.multiselect-filter').each(function(val, ind){
                // if($(ind).find('input').length != 0){
                    // $(ind).find('input').val('');
                // } else {
                    // $(ind).find('span').html("<?php __('-- Any --');?>");
                // }
                // $(ind).css('border', 'none');
                // $('#reset-filter').addClass('hidden');
            // });
            // setTimeout(function(){
                // location.reload();
            // }, 500);
			$('.multiselect-filter input').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			dataGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');
        }
    })(jQuery);
    // minus date - val (val is number).
    function diffDate(date, val){
        var d = date.split('-');
        var newdate = new Date(d[2], d[1]-1, d[0]);
        newdate.setDate(newdate.getDate() - val); // minus the date
        var nd = new Date(newdate);
        var dd = nd.getDate() < 10 ? '0' + nd.getDate() : nd.getDate();
        var mm = nd.getMonth() < 10 ? '0' + (nd.getMonth() + 1) : nd.getMonth() + 1;
        var yyyy = nd.getFullYear();
        var _date = dd + '-' + mm + '-' + yyyy;
        return _date;
    }
    function setupScroll(){
        $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
        $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
    }
    setTimeout(function(){
        setupScroll();
    }, 1000);
    $("#scrollTopAbsence").scroll(function () {
        $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
    $(".slick-viewport-right:first").scroll(function () {
        $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right:first").scrollLeft());
    });
</script>
