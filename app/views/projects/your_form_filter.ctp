<?php
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity',
        'jquery.multiSelect',
        'projects',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
        'select2.min'
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
        'jquery.ui.touch-punch.min',
        'select2.min'
    ));
    echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-tab .wd-aside-left{width: 300px !important;}
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    p {
        margin-bottom: 10px;
    }
    body{
        color: #000;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 30px;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="<?php echo $this->Html->url('/projects/your_form_plus/' . $project_id) ?>" class="btn btn-back"><span><?php __('Back') ?></span></a>
                </div>
                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <div class="wd-content">
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <p><?php __('In the column #, drag and drop to reorder') ?></p>
                                <div class="wd-table" id="project_container" style="width:760px;height:700px; overflow: auto;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        'name' => __('Order', true),
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        'cssClass' => 'slick-cell-move-handler'
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Widget', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
    ),
    array(
        'id' => 'display',
        'field' => 'display',
        'name' => __('Display', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => false,
        // 'editor' => 'Slick.Editors.selectBox',
        'formatter' => 'Slick.Formatters.selectBoxFormatter'
    ),
    array(
        'id' => 'page_break',
        'field' => 'page_break',
        'name' => __('Page break', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => false,
        // 'editor' => 'Slick.Editors.selectBox',
        'formatter' => 'Slick.Formatters.selectBoxPageFormatter'
    ),
    array(
        'id' => 'status_display',
        'field' => 'status_display',
        'name' => __('Status', true),
        'width' => 400,
        'sortable' => false,
        'resizable' => true,
        'selectable' => false,
        // 'editor' => 'Slick.Editors.selectBoxS'
        'formatter' => 'Slick.Formatters.selectFormatter'
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'display' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'page_break' => array('yes' => __('Yes', true), 'no' => __('No', true))
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($yourFromFilters)){
    foreach ($yourFromFilters as $yourFromFilter) {
        $yourFromFilter = $yourFromFilter['YourFormFilter'];
        $data = array(
            'id' => $yourFromFilter['id'],
            'employee_id' => $employee_id,
            'no.' => $i++,
            'MetaData' => array()
        );
        $data['widget'] = __($yourFromFilter['widget'], true);
        $data['name'] = __($yourFromFilter['name'], true);
        $data['display'] = $yourFromFilter['display'] ? 'yes' : 'no';
        $data['page_break'] = $yourFromFilter['page_break'] ? 'yes' : 'no';
        $data['weight'] = $yourFromFilter['weight'];
        $dataView[] = $data;
    }
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s', '%4$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay" rel="%s">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
    </div>
</div>
<div id="select-status-template" style="display: none">
    <select id="js-select-status-multiple" onchange="updateSelectStatus('project_task')" multiple="multiple" style="width: 100%;">
        <?php foreach ($projectStatus as $key => $sta) {
            $selected = in_array($key, $statusFilters['project_task']) ? 'selected' : '';
        ?>
        <option <?php echo $selected; ?> value="<?php echo $key ?>"><?php echo $sta ?></option>
        <?php } ?>
    </select>
</div>
<div id="select-risk-template" style="display: none">
    <select id="js-select-risk-multiple" onchange="updateSelectStatus('risk')" multiple="multiple" style="width: 100%;">
        <?php foreach ($projectIssueStatus as $key => $sta) {
            $selected = in_array($key, $statusFilters['risk']) ? 'selected' : '';
        ?>
        <option <?php echo $selected; ?> value="<?php echo $key ?>"><?php echo $sta ?></option>
        <?php } ?>
    </select>
</div>
<div id="select-issue-template" style="display: none">
    <select id="js-select-issue-multiple" onchange="updateSelectStatus('issue')" multiple="multiple" style="width: 100%;">
        <?php foreach ($projectIssueStatus as $key => $sta) {
            $selected = in_array($key, $statusFilters['issue']) ? 'selected' : '';
        ?>
        <option <?php echo $selected; ?> value="<?php echo $key ?>"><?php echo $sta ?></option>
        <?php } ?>
    </select>
</div>
<div id="select-finance_plus-template" style="display: none">
    <select id="js-select-finance_plus-multiple" onchange="updateSelectStatus('finance_plus')" style="width: 100%;">
        <?php
            $selected = !empty($statusFilters['finance_plus']) ? $statusFilters['finance_plus'][0] : 0;
        ?>
        <option <?php echo ( $selected == 0 ) ? 'selected' :''; ?> value="0"><?php echo __('Dashboard and Graph', true) ?></option>
        <option <?php echo ( $selected == 1) ? 'selected' :''; ?> value="1"><?php echo __('Graph only', true) ?></option>
    </select>
</div>
<div id="select-global_view-template" style="display: none">
    <select id="js-select-global_view-multiple" onchange="updateSelectStatus('global_view')" style="width: 100%;">
        <?php
            $selected = !empty($statusFilters['global_view']) ? $statusFilters['global_view'][0] : 1;
        ?>
        <option <?php echo ( $selected == 0 ) ? 'selected' :''; ?> value="0"><?php echo __('Real size', true) ?></option>
        <option <?php echo ( $selected == 1) ? 'selected' :''; ?> value="1"><?php echo __('Small size', true) ?></option>
    </select>
</div>

<div id="select-finance_two_plus-template" style="display: none">
    <select id="js-select-finance_two_plus-multiple" onchange="updateSelectStatus('finance_two_plus')" style="width: 100%;">
        <?php
            $selected = !empty($statusFilters['finance_two_plus']) ? $statusFilters['finance_two_plus'][0] : 0;
        ?>
        <option <?php echo ( $selected == 0 ) ? 'selected' :''; ?> value="0"><?php echo __('Dashboard and Graph', true) ?></option>
        <option <?php echo ( $selected == 1) ? 'selected' :''; ?> value="1"><?php echo __('Graph only', true) ?></option>
    </select>
</div>
<script type="text/javascript">
//$(".js-example-basic-multiple").select2();
    var DataValidator = {};
    (function($){

        $(function(){
            /**
             * Tim 1 phan tu trong mang
             */
            function GetObjectValueIndex(obj, keyToFind) {
                var i = 0, key;
                for (key in obj) {
                    var val = obj[key] ? obj[key] : 0;
                    if (val == keyToFind) {
                        return i;
                    }
                    i++;
                }
                return null;
            };

            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            var $select2Status = '<script type="text\/javascript">$("#js-select-status-multiple").select2()<\/script>';
            var $select2Issue = '<script type="text\/javascript">$("#js-select-issue-multiple").select2();<\/script>';
            var $select2Risk = '<script type="text\/javascript">$("#js-select-risk-multiple").select2()<\/script>';
            var $select2Finance = '<script type="text\/javascript">$("#js-select-finance_plus-multiple").select2()<\/script>';
            var $select2FinanceTwoPlus = '<script type="text\/javascript">$("#js-select-finance_two_plus-multiple").select2()<\/script>';
            var $select2Global = '<script type="text\/javascript">$("#js-select-global_view-multiple").select2()<\/script>';
            var selectStatusTemp = $('#select-status-template').html() + $select2Status;
            var selectIssueTemp = $('#select-issue-template').html() + $select2Issue;
            var selectRiskTemp = $('#select-risk-template').html() + $select2Risk;
            var selectFinanceTemp = $('#select-finance_plus-template').html() + $select2Finance;
            var selectFinanceTwoPlusTemp = $('#select-finance_two_plus-template').html() + $select2FinanceTwoPlus;
            var selectGlobalTemp = $('#select-global_view-template').html() + $select2Global;

            var orderTemplate = $('#order-template').html();

            $this.onCellChange = function(args){
                return true;
            };
            $.extend(Slick.Formatters,{
                Order : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(orderTemplate , row));
                },
                selectFormatter : function(row, cell, value, columnDef, dataContext){
                    if(dataContext.widget == 'project_task'){
                        return selectStatusTemp;
                    } else if(dataContext.widget == 'issue'){
                        // return selectIssueTemp;
                        return Slick.Formatters.HTMLData(row, cell,$this.t(selectIssueTemp , row));
                    } else if(dataContext.widget == 'risk'){
                        return selectRiskTemp;
                    } else if(dataContext.widget == 'finance_plus'){
                        return selectFinanceTemp;
                    } else if(dataContext.widget == 'finance_two_plus'){
                        return selectFinanceTwoPlusTemp;
                    } else if(dataContext.widget == 'global_view'){
                        return selectGlobalTemp;
                    }
                    return '';
                },
                selectBoxFormatter : function(row, cell, value, columnDef, dataContext){
                    var html = '';
                    html = '<select style="padding: 10px 8px" rel="no-history" onchange="updateDisplayFilter.call(this, \'' + dataContext.id + '\', ' + cell+ ')">';
                    html += '<option value="1" ' + (value == 'yes' ? 'selected' : '') + '>' + <?php echo json_encode(__('Yes', true)) ?> + '</option>';
                    html += '<option value="0" ' + (value != 'yes' ? 'selected' : '') + '>' + <?php echo json_encode(__('No', true)) ?> + '</option>';
                    html += '</select>';

                    return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
                },
                selectBoxPageFormatter : function(row, cell, value, columnDef, dataContext){
                    var html = '';
                    html = '<select style="padding: 10px 8px; width: 90px;" rel="no-history" onchange="updatePageFilter.call(this, \'' + dataContext.id + '\', ' + cell + ')">';
                    html += '<option value="1" ' + (value == 'yes' ? 'selected' : '') + '>' + <?php echo json_encode(__('Yes', true)) ?> + '</option>';
                    html += '<option value="0" ' + (value != 'yes' ? 'selected' : '') + '>' + <?php echo json_encode(__('No', true)) ?> + '</option>';
                    html += '</select>';
                   
                    return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);

                }
            });
            $this.onBeforeEdit = function(args){
                return true;
            }
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                employee_id : {defaulValue : '<?php echo $employee_id; ?>', allowEmpty : false},
                widget : {defaulValue : ''},
                display : {defaulValue : ''},
                weight : {defaulValue : 0 }
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update_your_form_filter')); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns , {
                enableAddRow : false,
                showHeaderRow : false,
                frozenColumn: 1,
                rowHeight: 43,
            });
            dataGrid.setSortColumn('weight' , true);
            dataGrid.setSelectionModel(new Slick.RowSelectionModel());
            var moveRowsPlugin = new Slick.RowMoveManager({
                cancelEditOnDrag: true
            });
            moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, data) {
                for (var i = 0; i < data.rows.length; i++) {
                        // no point in moving before or after itself
                    if (data.rows[i] == data.insertBefore || data.rows[i] == data.insertBefore - 1) {
                        if(e.stopPropagation) {
                            e.stopPropagation();
                        } else {
                            e.returnValue = false;
                        }
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
                    url : '<?php echo $html->url('/projects/order_your_form_filter/' . $employee_id) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                })
                dataGrid.resetActiveCell();
                dataGrid.setData(data);
                dataGrid.setSelectedRows(selectedRows);
                dataGrid.render();
            });

            dataGrid.registerPlugin(moveRowsPlugin);
            dataGrid.onDragInit.subscribe(function (e, dd) {
                // prevent the grid from cancelling drag'n'drop by default
                e.stopImmediatePropagation();
            });


            $('[name="project_container.SortOrder"],[name="project_container.SortColumn"]').remove();
            var $colSort = $(dataGrid.getHeaderRow()).closest('.ui-widget').find('.slick-header-column-sorted .slick-column-name');
            var sortData = function(){
                $.each(dataGrid.getSortColumns() , function(){
                    this.sortAsc = !this.sortAsc;
                });
                $colSort.data('autoTrigger', true)
                $colSort.click();
            };
            $colSort.click(function(e){
                if(!$colSort.data('autoTrigger')){
                    e.stopImmediatePropagation();
                    return false;
                }
                $colSort.data('autoTrigger',false);
            });


            //
            (function(){

                if(!$this.canModified){
                    return;
                }
                var saveData = {},timeoutID;

                var updateSort = function(){
                    $.ajax({
                        url : '<?php echo $html->url(array('action' => 'order_your_form_filter', $employee_id)); ?>',
                        cache : false,
                        type : 'POST',
                        data : {
                            data : $.extend({},saveData)
                        }
                    });
                    saveData = {};
                };

                var getRow = function(row){
                    var $el = $(dataGrid.getCellNode(row, 1));
                    if($el.length){
                        return $el.parent();
                    }
                    return false;
                };

                var  toggleElement = function(s , d){
                    var sdata = dataGrid.getDataItem(s);
                    var ddata = dataGrid.getDataItem(d);

                    var w = ddata.weight;
                    ddata.weight = sdata.weight;
                    sdata.weight = w;

                    saveData[sdata.id] = sdata.weight;
                    saveData[ddata.id] = ddata.weight;

                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(updateSort , 1500);
                    sortData();
                    var $s = getRow(d);
                    $s.stop().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                        $s.animate({backgroundColor:'#FFF'}, 'slow' , function(){
                            $s.css('backgroundColor' , '');
                        });
                    });
                };

                $('.phase-order-handler span.wd-up').live('click' , function(){
                    var row = Number($(this).parent().attr('rel'));
                    if (getRow(row - 1)) {
                        toggleElement(row,row - 1);
                    }
                });

                $('.phase-order-handler span.wd-down').live('click' , function(){
                    var row = Number($(this).parent().attr('rel'));
                    if (getRow(row + 1)) {
                        toggleElement(row,row + 1);
                    }
                });
            })();
        });
    })(jQuery);
    function updateSelectStatus(widget){
        var data = [],
            t,
            i = 0;
        if(widget == 'project_task'){
            t = $('#js-select-status-multiple');
            $('#js-select-status-multiple option:selected').each(function(){
                data[i] = $(this).val();
                i++;
            });
        } else if(widget == 'risk'){
            $('#js-select-risk-multiple option:selected').each(function(){
                data[i] = $(this).val();
                i++;
            });
        } else if(widget == 'finance_plus'){
            $('#js-select-finance_plus-multiple option:selected').each(function(){
                data[i] = $(this).val();
                i++;
            });
        } else if(widget == 'finance_two_plus'){
            $('#js-select-finance_two_plus-multiple option:selected').each(function(){
                data[i] = $(this).val();
                i++;
            });
        } else if(widget == 'global_view'){
            $('#js-select-global_view-multiple option:selected').each(function(){
                data[i] = $(this).val();
                i++;
            });
        } else {
            $('#js-select-issue-multiple option:selected').each(function(){
                data[i] = $(this).val();
                i++;
            });
        }
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updateSelectStatus') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                widget: widget,
                data: data
            },
            complete: function(){
                $(t).next().css('color', '#3bbd43');
                if(widget == 'finance_plus'){
                    $('#select2-js-select-finance_plus-multiple-container').css('color', '#3bbd43');
                }
                if(widget == 'finance_two_plus'){
                    $('#select2-js-select-finance_two_plus-multiple-container').css('color', '#3bbd43');
                }
                if(widget == 'global_view'){
                    $('#select2-js-select-global_view-multiple-container').css('color', '#3bbd43');
                }
            }
        });
    }
    function updateDisplayFilter(id, cell){
        var t = $(this);
        var v = $(this).val();
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updateDisplayFilter') ?>',
            type: 'POST',
            data: {
                data: {
                    id: id,
                    display: v,
                    col: 'display'
                }
            },
            complete: function(){
                $(t).css('color', '#3bbd43');
            }
        });
    }
    function updatePageFilter(id, cell){
        var t = $(this);
        var v = $(this).val();
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updateDisplayFilter') ?>',
            type: 'POST',
            data: {
                data: {
                    id: id,
                    display: v,
                    col: 'page_break'
                }
            },
            complete: function(){
                $(t).css('color', '#3bbd43');
            }
        });
    }
</script>
