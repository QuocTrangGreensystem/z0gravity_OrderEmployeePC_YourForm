<?php
    echo $html->script(array(
        'jquery.multiSelect',
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
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom'
    ));
    echo $html->css(array(
        'jquery.multiSelect',
        'slick_grid/slick.grid',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'slick_grid/slick.edit'
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
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">
                                </div>
                                <div id="pager" style="width:100%;height:36px; overflow: hidden;">
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
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name_eng',
        'field' => 'name_eng',
        'name' => __('Name (English)', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUnique'
    ),
    array(
        'id' => 'name_fre',
        'field' => 'name_fre',
        'name' => __('Name (French)', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUniqueFre'
    ),
    array(
        'id' => 'parent_id',
        'field' => 'parent_id',
        'name' => __('Parent', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'description',
        'field' => 'description',
        'name' => __('Description', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textArea'
    ),
    array(
        'id' => 'display',
        'field' => 'display',
        'name' => __('Display', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'default_screen',
        'field' => 'default_screen',
        'name' => __('Default screen', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
);
if($modifyScreen === 'YES'){
    $columns[] = array(
        'id' => 'controllers',
        'field' => 'controllers',
        'name' => __('Controller', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    );
    $columns[] = array(
        'id' => 'functions',
        'field' => 'functions',
        'name' => __('Function', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    );
}
$columns[] = array(
    'id' => 'weight',
    'field' => 'weight',
    'name' => __('Order', true),
    'width' => 60,
    'sortable' => true,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Order'
);
if(!empty($SAS)){
    $columns[] = array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    );
}
$i = 1;
$dataView = array();
$selectMaps = array(
    'parent_id' => array(),
    'display' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'default_screen' => array('yes' => __('Yes', true), 'no' => __('No', true))
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();

if(!empty($menus)){
    foreach ($menus as $menu) {
        $data = array(
            'id' => $menu['id'],
            'company_id' => $company_id,
            'no.' => $i++,
            'MetaData' => array()
        );
        $data['name_eng'] = (string) $menu['name_eng'];
        $data['name_fre'] = (string) $menu['name_fre'];
        $data['parent_id'] = (string) $menu['parent_id'];
        $data['description'] = (string) $menu['description'];
        $data['parent_id'] = (string) $menu['parent_id'];
        if ($menu['parent_id']) {
            $selectMaps['parent_id'][$menu['parent_id']] = $menus[$menu['parent_id']]['name_eng'];
        }
        $data['display'] = $menu['display'] ? 'yes' : 'no';
        $data['default_screen'] = $menu['default_screen'] ? 'yes' : 'no';
        $data['controllers'] = (string) $menu['controllers'];
        $data['functions'] = (string) $menu['functions'];
        $data['weight'] = (int) $menu['weight'];
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
            var model = <?php echo json_encode($model);?>;
            var modifyScreen = <?php echo json_encode($modifyScreen);?>;

            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.name_eng.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Name English has already been exist.')
                };
            }

            DataValidator.isUniqueFre = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.name_fre.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Name French has already been exist.')
                };
            }

            $this.onCellChange = function(args){
                if(args.item && args.item.display == ''){
                    args.item.display = 'yes'
                }
                args.item.weight = args.item.weight ? args.item.weight : data.length;
                if(args.grid.getData().getItems()){
                    $.each(args.grid.getData().getItems(), function(index, val){
                        if(val.default_screen === 'yes' && val.id != args.item.id){
                            var _rowParent = args.grid.getData().getRowById(val.id);
                            args.grid.getData().getItems()[_rowParent].default_screen = 'no';
                            args.grid.updateRow(_rowParent);
                        }
                    });
                }
                return true;
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, dataContext.id,
                    dataContext.company_id, dataContext.name_eng, model), columnDef, dataContext);
                },
                Percentage : function(row, cell, value, columnDef, dataContext){
                    return value + ' %';
                },
                Order : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(orderTemplate , row));
                }
            });
            $this.onBeforeEdit = function(args){
                var result = true;
                if(args.column.field == 'parent_id'){
                    backup = $.extend({} , $this.selectMaps['parent_id']);
                    $this.selectMaps['parent_id'] = {};
                    $.each(args.grid.getData().getItems() , function(undefined,dx){
                        if(args.item && args.item.id && args.item.id == dx.parent_id){
                            return (result = false);
                        }
                        if(dx.id && !dx.parent_id && (!args.item || args.item.id != dx.id)){
                            $this.selectMaps['parent_id'][dx.id] = dx.name_eng;
                        }
                    });
                }
                if((modifyScreen === 'NO') && (args.column.field == 'controllers' || args.column.field == 'functions')){
                    return false;
                }
                return result;
            };

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name_eng : {defaulValue : '' , allowEmpty : false, maxLength: 125},
                name_fre : {defaulValue : '' , allowEmpty : false, maxLength: 125},
                parent_id : {defaulValue : ''},
                display : {defaulValue : ''},
                default_screen : {defaulValue : ''},
                description : {defaulValue : ''},
                controllers : {defaulValue : ''},
                functions : {defaulValue : ''},
                weight : {defaulValue : 0 }
            };
            var _enableAddRow = false;
            if(modifyScreen === 'YES'){
                _enableAddRow = true;
            }
            $this.url =  '<?php echo $html->url(array('action' => 'update', $model)); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns , {
                enableAddRow : _enableAddRow,
                showHeaderRow : false
            });
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
