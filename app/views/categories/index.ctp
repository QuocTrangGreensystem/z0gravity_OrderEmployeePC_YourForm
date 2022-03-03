<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php //echo $html->css('slick_grid/smoothness/jquery-ui-1.8.16.custom');                    ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables');                    ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-input-select{
        margin-bottom: 3px;
    }
    .wd-input-select label{
        font-size: 13px;
        font-weight: bold;
        padding-right: 20px;
        margin-top: 12px;
        display: block;
        float: left;
    }
    .wd-input-select select{
        padding: 5px;
        float: left;
        border: 1px solid rgb(179, 179, 179);
    }
    .gr-settings{
        width: 500px;
    }
    .gr-settings .wd-input-select select{
        float: right;
    }
</style>
<style>
    .select-display {
        width: 100%;
        padding: 3px 0;
    }
    .display td,
    .display th {
        vertical-align: middle;
    }
    .wd-overlay {
        position: relative;
    }
    .wd-overlay span {
        display: block;
        position: absolute;
        left: 45%;
        top: 10px;
        width: 16px;
        height: 16px;
        background: url(<?php echo $html->url('/img/ajax-loader.gif') ?>) no-repeat;
        display: none;
    }
    .index {
        cursor: move;
    }
    .display td {
        background: #fff;
    }
    .msg {
        position: fixed;
        top: 40%;
        left: 40%;
        width: 20%;
        background: #fff;
        padding: 10px;
        border: 10px solid #eee;
        border-radius: 6px;
        display: none;
        color: #000;
        text-align: center;
    }
    .unsortable .index {
        cursor: default;
    }
    .row-hidden td {
        background: #ffc;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"><?php //echo sprintf(__("Budget Settings management of %s", true), $companyName['Company']['company_name']); ?></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;">

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
<?php echo $this->element('dialog_projects') ?>
<?php
echo $this->Html->script(array(
    'history_filter',
    'jquery.multiSelect',
    'responsive_table',
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
?>
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
        'id' => 'category',
        'field' => 'category',
        'name' => __('Category', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'code_1',
        'field' => 'code_1',
        'name' => __('Code 1', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'code_2',
        'field' => 'code_2',
        'name' => __('Code 2', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();

foreach ($categories as $categorie) {
    $data = array(
        'id' => $categorie['Category']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['category'] = (string) $categorie['Category']['category'];
    $data['code_1'] = (string) $categorie['Category']['code_1'];
    $data['code_2'] = (string) $categorie['Category']['code_2'];

    $dataView[] = $data;
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
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
    function editMe(field,value)
    {
        $(loading).insertAfter('#'+field);
        var data = field+'/'+value;
        $.ajax({
            url: '/company_configs/editMe/',
            data: {
                data : { value : value, field : field }
            },
            type:'POST',
            success:function(data) {
                $('#'+field).removeClass('KO');
                $('#loadingElm').remove();
            }
        });
    }
    (function($){

        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();

            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    var _html = '';
                    _html = $this.t(actionTemplate,dataContext.id, dataContext.company_id,dataContext.name);
                    return Slick.Formatters.HTMLData(row, cell, _html, columnDef, dataContext);
                }
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                category : {defaulValue : '' , allowEmpty : false, maxLength: 255},
                code_1 : {defaulValue : '', maxLength : 100},
				code_2 : {defaulValue : '', maxLength : 100}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns);

        });
    })(jQuery);
</script>
