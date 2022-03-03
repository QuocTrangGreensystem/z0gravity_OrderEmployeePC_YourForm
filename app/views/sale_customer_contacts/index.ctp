<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php if($canModifyContact):?>
                <div class="wd-title">
                    <a href="javascript:void(0);" id="add-activity" class="btn btn-plus" onclick="addNewSalesButton();"><span></span></a>
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
        'id' => 'sale_customer_id',
        'field' => 'sale_customer_id',
        'name' => __('Company/Customer/Provider', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DataValidator.isUniqueCustomer',
        'formatter' => 'Slick.Formatters.goToUpdate'
    ),
    array(
        'id' => 'first_name',
        'field' => 'first_name',
        'name' => __('First Name', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'validator' => 'DataValidator.isUnique',
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'last_name',
        'field' => 'last_name',
        'name' => __('Last Name', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUniqueLastName'
    ),
    array(
        'id' => 'phone',
        'field' => 'phone',
        'name' => __('Phone', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'email',
        'field' => 'email',
        'name' => __('E-Mail', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUniqueEmail'
    ),
    array(
        'id' => 'in_charge_of',
        'field' => 'in_charge_of',
        'name' => __('In Charge Of', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 50,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'sale_customer_id' => !empty($saleCustomers) ? $saleCustomers : array()
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($saleContacts)){
    foreach($saleContacts as $saleContact){
        foreach($saleContact as $value){
            $data = array(
                'id' => $value['id'],
                'no.' => $i++,
                'MetaData' => array()
            );
            $data['sale_customer_id'] = (string) $value['sale_customer_id'];
            $data['first_name'] = (string) $value['first_name'];
            $data['last_name'] = (string) $value['last_name'];
            $data['phone'] = (string) $value['phone'];
            $data['email'] = (string) $value['email'];
            $data['in_charge_of'] = (string) $value['in_charge_of'];
            $data['action.'] = '';
            $dataView[] = $data;
        }
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
    <!--a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php //echo $this->Html->url(array('controller' => 'sale_customers', 'action' => 'update', 'contact', '%1$s', '%2$s')); ?>">Edit</a-->
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s', '%4$s')); ?>">Delete</a>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            createdSaleContact = <?php echo json_encode($createdSaleContact);?>,
            updatedSaleContact = <?php echo json_encode($updatedSaleContact);?>,
            company_id = <?php echo json_encode($company_id);?>,
            typeOfCustomer = <?php echo json_encode($typeOfCustomer);?>,
            urlUpdateCustomer = <?php echo json_encode(urldecode($this->Html->link('%4$s', array('controller' => 'sale_customers', 'action' => 'update', 'contact', '%1$s', '%2$s', '%3$s')))); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified = <?php echo json_encode($canModifyContact);?>;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            DataValidator.isUniqueCustomer = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if((args.item.last_name && args.item.last_name.toLowerCase() == dx.last_name.toLowerCase()) 
                    && (args.item.first_name && args.item.first_name.toLowerCase() == dx.first_name.toLowerCase())
                    && (dx.sale_customer_id.toLowerCase() == value.toLowerCase())){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('The First Name/Last Name in Customer has already been exist.')
                };
            }
            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if((args.item.last_name && args.item.last_name.toLowerCase() == dx.last_name.toLowerCase()) 
                    && (args.item.sale_customer_id && args.item.sale_customer_id.toLowerCase() == dx.sale_customer_id.toLowerCase())
                    && (dx.first_name.toLowerCase() == value.toLowerCase())){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('The First Name/Last Name in Customer has already been exist.')
                };
            }
            DataValidator.isUniqueLastName = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if((args.item.first_name && args.item.first_name.toLowerCase() == dx.first_name.toLowerCase())
                    && (args.item.sale_customer_id && args.item.sale_customer_id.toLowerCase() == dx.sale_customer_id.toLowerCase()) 
                    && (dx.last_name.toLowerCase() == value.toLowerCase())){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('The First Name/Last Name in Customer has already been exist.')
                };
            }
            DataValidator.isUniqueEmail = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.email.toLowerCase() != value.toLowerCase()));
                });
                var _message = $this.t('The Email has already been exist.');
                var email = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
                if(!email.test(value.toLowerCase())){
                    _message = $this.t('Email Not Valid!');
                    result = false;
                }
                return {
                    valid : result,
                    message : _message
                };
            }
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, company_id,
                    dataContext.sale_customer_id, dataContext.first_name + ' ' + dataContext.last_name, dataContext.id), columnDef, dataContext);
                },
                goToUpdate : function(row, cell, value, columnDef, dataContext){
                    value = $this.selectMaps.sale_customer_id[value] ? $this.selectMaps.sale_customer_id[value] : '';
                    var _type = typeOfCustomer[dataContext.sale_customer_id] ? typeOfCustomer[dataContext.sale_customer_id] : 'cus';
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlUpdateCustomer, _type, company_id, dataContext.sale_customer_id, value), columnDef, dataContext);
                }
            });;
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>'},
                sale_customer_id: {defaulValue : '', allowEmpty : false},
                first_name: {defaulValue : '', allowEmpty : false},
                last_name: {defaulValue : '', allowEmpty : false},
                email: {defaulValue : ''},
                phone: {defaulValue : ''},
                in_charge_of: {defaulValue : ''},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
            addNewSalesButton = function(){
                ControlGrid.gotoCell(data.length, 1, true);
            }
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