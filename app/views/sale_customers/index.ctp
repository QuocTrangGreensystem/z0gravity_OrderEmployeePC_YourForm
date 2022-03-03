<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php if($canModifyCustomer):?>
                <div class="wd-title">
                    <a href="<?php echo $this->Html->url(array('action' => 'update', 'customer', $category, $company_id));?>" id="add-activity" class="btn btn-plus"><span></span></a>
                </div>
                <?php endif;?>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                </div>
                <?php //echo $this->element('grid_status'); ?>
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
$nameColumn = 'Company/Customer';
if($category == 'pro'){
    $nameColumn = 'Provider';
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
        'name' => __($nameColumn, true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.goToUpdate'
    ),
    array(
        'id' => 'sale_setting_customer_status',
        'field' => 'sale_setting_customer_status',
        'name' => __('Status', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'sale_setting_customer_industry',
        'field' => 'sale_setting_customer_industry',
        'name' => __('Industry', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'phone',
        'field' => 'phone',
        'name' => __('Phone', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'sale_setting_customer_country',
        'field' => 'sale_setting_customer_country',
        'name' => __('Country', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'email',
        'field' => 'email',
        'name' => __('Email', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'sale_setting_customer_status' => !empty($saleSettings[0]) ? $saleSettings[0] : array(),
    'sale_setting_customer_industry' => !empty($saleSettings[1]) ? $saleSettings[1] : array(),
    'sale_setting_customer_country' => !empty($saleSettings[3]) ? $saleSettings[3] : array()
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($salesCustomers)){
    foreach($salesCustomers as $salesCustomer){
        $data = array(
            'id' => $salesCustomer['id'],
            'no.' => $i++,
            'MetaData' => array()
        );
        $data['name'] = (string) $salesCustomer['name'];
        $data['sale_setting_customer_status'] = (string) $salesCustomer['sale_setting_customer_status'];
        $data['sale_setting_customer_industry'] = (string) $salesCustomer['sale_setting_customer_industry'];
        $data['phone'] = (string) $salesCustomer['phone'];
        $data['sale_setting_customer_country'] = (string) $salesCustomer['sale_setting_customer_country'];
        $data['email'] = (string) $salesCustomer['email'];
        $data['action.'] = '';
        $dataView[] = $data;
    }
}
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'update', 'customer', '%1$s', '%2$s', '%3$s')); ?>">Edit</a>
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%4$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s', '%3$s')); ?>">Delete</a>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            company_id = <?php echo json_encode($company_id);?>,
            category = <?php echo json_encode($category);?>,
            urlUpdateCustomer = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('controller' => 'sale_customers', 'action' => 'update', 'customer', $category, '%1$s', '%2$s')))); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, category, company_id,
                    dataContext.id, dataContext.name), columnDef, dataContext);
                },
                goToUpdate : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlUpdateCustomer, company_id, dataContext.id, value), columnDef, dataContext);
                }
            });;
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.canModified = <?php echo json_encode($canModifyCustomer);?>;
            $this.fields = {
                id : {defaulValue : 0}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
        });
    })(jQuery);
</script>
<?php 
    echo $html->css(array(
        'jquery.multiSelect',   
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
        'audit'
    )); 
    echo $html->script(array(
        'history_filter',
        'jquery.multiSelect',
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
        'slick_grid_custom',
        'responsive_table.js'
    ));
    echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
</style>