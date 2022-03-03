<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php // color picker         ?>
<?php echo $html->css('green/colorpicker'); ?>
<?php echo $html->script('green/colorpicker'); ?>

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
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'description',
        'field' => 'description',
        'name' => __('Description', true),
        'width' => 350,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textArea'
    ),
    array(
        'id' => 'color',
        'field' => 'color',
        'name' => __('Color', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.colorPicker',
        'formatter' => 'Slick.Formatters.colorBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();

$Model = ClassRegistry::getObject('ResponseConstraint');

foreach ($Model->getOptions() as $constraint => $response) {
    $data = array(
        'id' => $constraint,
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) isset($responseConstraints[$constraint]['name']) ? $responseConstraints[$constraint]['name'] : $response['name'];
    $data['description'] = (string) isset($responseConstraints[$constraint]['description']) ? $responseConstraints[$constraint]['description'] : '';
    $data['color'] = (string) isset($responseConstraints[$constraint]['color']) ? $responseConstraints[$constraint]['color'] : $response['color'];

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
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want set "%s" on default ?', true), '%3$s')); ?>');" class="wd-update" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var DateValidate = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            
            var actionTemplate =  $('#action-template').html();
            var colorTemplate = '<div style="width: 20px; height: 20px; background-color: %s; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
            $.extend(Slick.Formatters,{
                colorBox : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(colorTemplate,value), columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id, dataContext.company_id , dataContext.name), columnDef, dataContext);
                }
            });
            $.extend(Slick.Editors, {
                colorPicker:  function(args){
                    this.isCreated = false;
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 7).prop('readonly' , true);
                    
                    
                    var serializeValue = this.serializeValue;
                    this.serializeValue = function(){
                        if(!this.isCreated){
                            this.input.miniColors();
                            this.input.parent().css('overflow', 'visible').find('.miniColors-triggerWrap').insertBefore(this.input);
                            this.isCreated = true;
                            this.focus();
                        }
                        return serializeValue.apply(this,$.makeArray(arguments));
                    }
                    
                    var destroy = this.destroy;
                    this.destroy = function(){
                        this.input.miniColors('destroy');
                        destroy.apply(this, $.makeArray(arguments));
                    }
                }
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false},
                color : {defaulValue : '', allowEmpty : false},
                description : {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns , {
                enableAddRow : false,
                showHeaderRow : false
            });
        });
    })(jQuery);
</script>