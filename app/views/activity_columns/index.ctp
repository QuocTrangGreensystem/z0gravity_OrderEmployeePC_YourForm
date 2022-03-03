<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
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
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php echo $html->script('slick_grid/slick.editors'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script(array('slick_grid_custom')); ?>
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
        'width' => 45,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'original',
        'field' => 'original',
        'name' => __('Original', true),
        'width' => 200,
        'sortable' => false,
        'resizable' => true
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Translate', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'calculate',
        'field' => 'calculate',
        'name' => __('Calculate', true),
        'width' => 200,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DateValidate.calculate',
        'formatter' => 'Slick.Formatters.Action'
    ),
    array(
        'id' => 'description',
        'field' => 'description',
        'name' => __('Description', true),
        'width' => 200,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textArea'
    ),
    array(
        'id' => 'display',
        'field' => 'display',
        'name' => __('Display', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'weight',
        'field' => 'weight',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Order'
    )
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'display' => array('no' => __('No', true), 'yes' => __('Yes', true))
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();
$listDatas = array(
    'workload_y' => __('Workload', true) . ' ' . date('Y', time()),
    'workload_last_one_y' => __('Workload', true) . ' ' . (date('Y', time()) - 1),
    'workload_last_two_y' => __('Workload', true) . ' ' . (date('Y', time()) - 2),
    'workload_last_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) - 3),
    'workload_next_one_y' => __('Workload', true) . ' ' . (date('Y', time()) + 1),
    'workload_next_two_y' => __('Workload', true) . ' ' . (date('Y', time()) + 2),
    'workload_next_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) + 3),
    'consumed_y' => __('Consumed', true) . ' ' . date('Y', time()),
    'consumed_last_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 1),
    'consumed_last_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 2),
    'consumed_last_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 3),
    'consumed_next_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 1),
    'consumed_next_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 2),
    'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3)
);
$_listDatas = array_keys($listDatas);
foreach ($activityColumns as $key => $column) {
    $data = array(
        'id' => $key,
        'company_id' => $company_id,
        'no.' => 'C' . $column['code'],
        'MetaData' => array()
    );

    $data['original'] = (string) $originalColumn[$key]['name'];
    $data['display'] = $column['display'] ? 'yes' : 'no';
    $data['weight'] = (int) $column['weight'];
    $data['calculate'] = $column['calculate'];
    $data['name'] = !empty($listDatas) && !empty($listDatas[$key]) ? (string)$listDatas[$key] : (string)$column['name'];
    $data['description'] = (string) $column['description'];

    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay" rel="%s">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
    </div>
</div>
<script type="text/javascript">
	var wdTable = $('.wd-table');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 45;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			heightViewPort = heightTable - 72;
			wdTable.find('.slick-viewport').height(heightViewPort);
			console.log( heightViewPort, "   ");
		}
	}
    var DateValidate = {};
    (function($){
        
        $(function(){
            var $this = SlickGridCustom,maxIndex = <?php echo json_encode($i); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            var orderTemplate = $('#order-template').html();
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.normal(row, cell, value ? value : '' , columnDef, dataContext);
                },
                Order : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(orderTemplate , row));
                }
            });
            DateValidate.calculate = function(value){
                var result = /^[a-z0-9+\-*/.() ]+$/gi.test(value);
                try{
                    result && eval('(' + value.replace(/C\d+/gi , '1') + ');');
                }catch(ex){
                    result = false;
                };
                return {
                    valid : result,
                    message : $this.t('Syntax error, You cant use basic formula and ID of column for Formula. For Example : (C15+C16)*10/20')
                };
            };
            var listDatas = <?php echo !empty($_listDatas) ? json_encode($_listDatas) : json_encode(array());?>;
            $this.onBeforeEdit = function(args){
                if($.inArray(args.item.id, listDatas) != -1 && args.column.id == 'name'){
                    return false;
                }
                if(args.column.id == 'calculate' && args.item && args.item.calculate === false){
                    return false;
                }
                return true;
            };

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false ,maxLength : 50},
                description : {defaulValue : '',maxLength : 255},
                calculate : {defaulValue : '',maxLength : 255},
                display : {defaulValue : '' },
                weight : {defaulValue : 0 }
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns, {
                enableAddRow : false
            });
			set_slick_table_height();
            dataGrid.setSortColumn('weight' , true);
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
                        url : '<?php echo $html->url(array('action' => 'order', $companyName['Company']['id'])); ?>',
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