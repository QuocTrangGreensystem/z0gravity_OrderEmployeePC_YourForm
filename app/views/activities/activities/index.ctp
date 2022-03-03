<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php //echo $html->css('slick_grid/smoothness/jquery-ui-1.8.16.custom');                                                               ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables');                                                               ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_parts', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <ul class="wd-item">
                        <li class="wd-current"><a href="<?php echo $html->url('/cities/') ?>"><?php echo __('Employees', true) ?></a></li>
                        <li><a href="<?php echo $html->url('/project_phases/') ?>"><?php __('Projects') ?></a></li>
                    </ul>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"><?php echo sprintf(__("Activity management of %s", true), $companyName['Company']['company_name']); ?></h2>
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
                                <?php echo $this->element('grid_status'); ?>
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
        'name' => $activityColumn['name']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'long_name',
        'field' => 'long_name',
        'name' => $activityColumn['long_name']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'short_name',
        'field' => 'short_name',
        'name' => $activityColumn['short_name']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'family_id',
        'field' => 'family_id',
        'name' => $activityColumn['family_id']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'subfamily_id',
        'field' => 'subfamily_id',
        'name' => $activityColumn['subfamily_id']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'accessible_profit',
        'field' => 'accessible_profit',
        'name' => $activityColumn['accessible_profit']['name'],
        'width' => 280,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox',
        'formatter' => 'Slick.Formatters.selectBox'
    ),
    array(
        'id' => 'pms',
        'field' => 'pms',
        'name' => $activityColumn['pms']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'linked_profit',
        'field' => 'linked_profit',
        'name' => $activityColumn['linked_profit']['name'],
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'code1',
        'field' => 'code1',
        'name' => $activityColumn['code1']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'code2',
        'field' => 'code2',
        'name' => $activityColumn['code2']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'code3',
        'field' => 'code3',
        'name' => $activityColumn['code3']['name'],
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'start_date',
        'field' => 'start_date',
        'name' => $activityColumn['start_date']['name'],
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DataValidator.startDate'
    ),
    array(
        'id' => 'end_date',
        'field' => 'end_date',
        'name' => $activityColumn['end_date']['name'],
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DataValidator.endDate'
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
$selectMaps = array(
    'pms' => array('true' => __('Yes', true), 'false' => __('No', true)),
    'actif' => array('true' => __('Yes', true), 'false' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'linked_profit' => $profitCenters,
    'accessible_profit' => $profitCenters
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($activities as $activity) {
    $data = array(
        'id' => $activity['Activity']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $activity['Activity']['name'];
    $data['long_name'] = (string) $activity['Activity']['long_name'];
    $data['short_name'] = (string) $activity['Activity']['short_name'];
    $data['family_id'] = (string) $activity['Activity']['family_id'];
    $data['subfamily_id'] = (string) $activity['Activity']['subfamily_id'];
    $data['pms'] = $activity['Activity']['pms'] ? 'true' : 'false';
    $data['actif'] = $activity['Activity']['actif'] ? 'true' : 'false';
    $data['accessible_profit'] = (array) Set::classicExtract($activity['AccessibleProfit'], '{n}.profit_center_id');
    $data['linked_profit'] = (string) Set::classicExtract($activity['LinkedProfit'], '0.profit_center_id');

    $data['code1'] = (string) $activity['Activity']['code1'];
    $data['code2'] = (string) $activity['Activity']['code2'];
    $data['code3'] = (string) $activity['Activity']['code3'];

    $data['start_date'] = $activity['Activity']['start_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $activity['Activity']['start_date'])) : '';
    $data['end_date'] = $activity['Activity']['end_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $activity['Activity']['end_date'])) : '';

    $data['action.'] = '';

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
<script type="text/javascript">
    var DataValidator = {};
    (function($){
        
        $(function(){
            var $this = SlickGridCustom,families = <?php echo json_encode($mapFamilies); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html(),backup = {};
            DataValidator.startDate = function(value , args){
                var _value = $this.getTime(value),end = args.item.end_date;
                return {
                    valid : !end || _value <= $this.getTime(end),
                    message : $this.t('The date must be smaller than or equal to %s' , end)
                };
            };
            DataValidator.endDate = function(value , args){
                var _value = $this.getTime(value),start = args.item.start_date;
                return {
                    valid : !start || _value >= $this.getTime(start),
                    message : $this.t('The date must be greater than or equal to %s' , start)
                };
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                }
            });
            $this.onBeforeEdit = function(args){
                var result = true;
                if(args.column.field == 'subfamily_id' && args.item){
                    backup = $.extend({} , $this.selectMaps['subfamily_id']);
                    $this.selectMaps['subfamily_id'] = {};
                    $.each(families , function(){
                        if(this.parent_id == args.item.family_id){
                            $this.selectMaps['subfamily_id'][this.id] = this.name;
                        }
                    });
                }
                return result;
            };
            $this.onCellChange = function(args){
                var result = true;
                if(args.column.field == 'family_id' && args.item && args.item.subfamily_id){
                    if(!families[args.item.subfamily_id] || families[args.item.subfamily_id].parent_id != args.item.family_id){
                        args.item.subfamily_id = '';
                    }
                }
                return result;
            };
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false , maxLength : 32},
                long_name : {defaulValue : '' , maxLength : 32},
                short_name : {defaulValue : '' , allowEmpty : false, maxLength : 12},
                family_id : {defaulValue : '' , allowEmpty : false},
                subfamily_id : {defaulValue : '' , required : ['family_id']},
                accessible_profit : {defaulValue : ''},
                linked_profit : {defaulValue : ''},
                start_date : {defaulValue : ''},
                end_date : {defaulValue : ''},
                pms : {defaulValue : ''},
                actif : {defaulValue : ''},
                code1 : {defaulValue : ''},
                code2 : {defaulValue : ''},
                code3 : {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns);
        
        });
        
    })(jQuery);
</script>