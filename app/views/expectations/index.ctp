<?php
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity',
        'jquery.multiSelect',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
		'preview/tab-admin',
		'layout_admin_2019'
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
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drop-2.0.min',
        'slick_grid/plugins/slick.rowselectionmodel',
        'slick_grid/plugins/slick.rowmovemanager',
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity',
    ));
?>
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
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <p><?php __('In the column #, drag and drop to reorder') ?></p>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

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
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        'cssClass' => 'slick-cell-move-handler'
    ),
    array(
        'id' => 'original_text',
        'field' => 'original_text',
        'name' => __('Original text', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
    ),
    // array(
    //     'id' => 'english',
    //     'field' => 'english',
    //     'name' => __('English', true),
    //     'width' => 150,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'editor' => 'Slick.Editors.textBox',
    // ),
    // array(
    //     'id' => 'france',
    //     'field' => 'france',
    //     'name' => __('French', true),
    //     'width' => 150,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'editor' => 'Slick.Editors.textBox',
    // ),
    array(
        'id' => 'display',
        'field' => 'display',
        'name' => __('Display', true),
        'width' => 110,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    )
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'display' => array('yes' => __('Yes', true), 'no' => __('No', true)),
);
if(!empty($fields)){
    foreach ($fields as $_field) {
        $field = $_field['Expectation'];
        $data = array(
            'id' => $field['id'],
            'company_id' => $company_id,
            'no.' => $i++,
            'MetaData' => array()
        );
        if(strpos($field['original_text'], 'lert_') == 1){
            $data['original_text'] = !empty($nameAlert[$field['original_text']]) ? $nameAlert[$field['original_text']] : $field['original_text'];
        } else {
            $data['original_text'] = !empty($translations[$field['original_text']]) ? $translations[$field['original_text']] : $field['original_text'];
        }
        $data['display'] = $field['display'] ? 'yes' : 'no';
        $data['action.'] = '';
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
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay" rel="%s">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
    </div>
</div>
<script type="text/javascript">
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
            // For validate date
            //var actionTemplate =  $('#action-template').html();
            var actionTemplate =  $('#action-template').html(),backup = $.extend({} , $this.selectMaps['parent_id']);
            var orderTemplate = $('#order-template').html();

            $this.onCellChange = function(args){
                return true;
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, dataContext.id,
                    dataContext.company_id, dataContext.name_eng, model), columnDef, dataContext);
                },
            });

            $this.onBeforeEdit = function(args){
                return true;
            }

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                display : {defaulValue : ''},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns , {
                enableAddRow : false,
                showHeaderRow : false,
                frozenColumn: 1
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
                    url : '<?php echo $html->url('/expectations/order/' . $company_id) ?>',
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
                        url : '<?php echo $html->url(array('action' => 'order', $company_id)); ?>',
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
</script>
