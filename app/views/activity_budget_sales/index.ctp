<?php //echo $html->script('jquery.dataTables');                                                                                                                                                                                           ?>
<?php //echo $html->css('jquery.ui.custom');                                                                                                                                                                                           ?>

<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php //echo $html->css('slick_grid/smoothness/jquery-ui-1.8.16.custom'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables'); ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .grid-canvas .slick-cell.editable{
        overflow : visible;
    }
    .delete-attachment,.download-attachment{
        background-repeat: no-repeat;
        display: inline-block;
        width: 16px;
        height: 16px;
        vertical-align: middle;
        margin: 0 5px;
        overflow: hidden;
        color: transparent !important;
        text-indent: -999px;
    }
    .delete-attachment{
        background-image: url("<?php echo $this->webroot . 'img/delete.png'; ?>");
    }
    .download-attachment{
        background-image: url("<?php echo $this->webroot . 'img/download.png'; ?>");
    }
    #upload-template,#upload-template form{
        position: relative;
    }
    #upload-template .file{
        background: url("<?php echo $this->webroot . 'img/browse.png'; ?>") no-repeat top right;
        height: 27px;
        width: 99px;
        float: left;
        background-position: 0 0px;
    }
    #upload-template .file-uploading{
        background-position: 0 -54px !important;
        cursor: pointer;
    }
    #upload-template .file:hover{
        background-position: 0 -27px;
    }
    .browse {
        position: absolute;
        top: 0;
        left: 60px;
        width: 20px;
        height: 27px;
        background: url("<?php echo $this->webroot . 'img/ajax-loader.gif'; ?>") no-repeat center center !important;
        display: none;
    }
    .slick-cell .file input{
        position: absolute;
        top : 0;
        left : 0;
        width: 99px;
        height: 27px;
        opacity: 0;
        filter: alpha(opacity = 0);
    }
    .slick-cell .text input{
        position: absolute;
        top: 0;
        left: 99px;
        width: 46px;
        height: 27px;
        opacity: 0;
        filter: alpha(opacity = 0);
    }
    .url-attachment{
        background: url("<?php echo $this->webroot . 'img/link_budget.png'; ?>") no-repeat top right;
        height: 27px;
        width: 46px;
        float: left;
        background-position: 0 0px;
        text-indent: -100px;
    }
    .url-attachment:hover{
        background-position: 0 -27px;
    }
    #action-attach-url{
        background: url("<?php echo $this->webroot . 'img/browse_budget.png'; ?>") no-repeat top right;
        height: 27px;
        width: 140px;
        float: left;
        background-position: 0 0px;
    }
    #action-attach-url:hover{
        background-position: 0 -27px;
    }
    #upload-template .file-uploading{
        background-position: 0 -54px !important;
        cursor: pointer;
    }
    #upload-template .text:hover{
        background-position: 0 -27px;
        cursor: auto;
    }
    #gs-attach{
        background: url("<?php echo $this->webroot . 'img/front/bt-check-box.png'; ?>") no-repeat top right;
        height: 16px;
        width: 16px;
        background-position: -64px 0px;
        position: absolute;
        top: 55px;
        right: 21px;
        cursor: pointer;
    }
    .gs-attach-remove{
        background-position: 0px 0px !important;
    }
    #gs-url{
        background: url("<?php echo $this->webroot . 'img/front/bt-check-box.png'; ?>") no-repeat top right;
        height: 16px;
        width: 16px;
        background-position: 0 0px;
        position: absolute;
        top: 95px;
        right: 21px;
        cursor: pointer;
    }
    .gs-url-add{
        background-position: -64px 0px !important;
    }
    #contextMenu{
        border: 1px solid #F5F5F5;
        padding: 6px;
        background: url("<?php echo $this->webroot . 'img/front/add_invoi.png'; ?>") no-repeat top left #4BB312;
        background-position: 5px;
    }
    .new_invoice_custom{
        padding-left: 27px;
        font-weight: bold;
        font-size: 12px;
        text-decoration: none;
    }
    .new_invoice_custom:hover{
        text-decoration: none;
        cursor: pointer;
    }
    .new_invoi_button{
        overflow: hidden;
        margin-bottom: 10px;
        /*
        border: 1px solid #F5F5F5;
        background: url("<?php //echo $this->webroot . 'img/front/add_invoi.png'; ?>") no-repeat top left #4BB312;
        background-position: 5px;*/
    }
    .gs-add-invoi{
        background: url("<?php echo $this->webroot . 'img/front/bg-add-project.png'; ?>") no-repeat left top;
        height: 33px;
        display: block;
        float: right;
        padding-left: 27px;
        line-height: 33px;
        color: #fff !important;
        text-decoration: none;
    }
    .gs-add-invoi span{
        background: url("<?php echo $this->webroot . 'img/front/bg-add-project-right.png'; ?>") no-repeat right top;
        height: 33px;
        display: block;
        padding: 0 15px 0 2px;
    }
    .gs-add-invoi:hover{background-position:left -33px; text-decoration: none;}
    .gs-add-invoi:hover span{background-position:right -33px; text-decoration: none;}
</style>
<div id="action-template-new-invoice" style="display: none;">
    <div>
        <a onclick="contextMenuButton(%1$s, %2$s);" href="javascript:void(0);" class="gs-add-invoi">
            <span><?php echo __('Invoice', true);?></span>
        </a>
    </div>
</div>
<div id="contextMenu" style="display: none; position: absolute; z-index: 1000;">
    <a id="new_invoice" class="new_invoice_custom"><?php echo __('New Invoice', true);?></a>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php 
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url', 
                'url' => array('controller' => 'activity_budget_sales', 'action' => 'upload')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachement") ?></label>
                <p id="gs-attach"></p>
                <?php
                echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                echo $this->Form->hidden('id_invoice', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                ?>
                <?php
                echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
                    'name' => 'FileField[attachment]',
                    'label' => false,
                    'class' => 'update_attach_class',
                    'rel' => 'no-history'));
                ?>   							
            </div>
            <div class="wd-input">
                <label for="url"><?php __("Url") ?></label>
                <p id="gs-url"></p>
                <?php
                echo $this->Form->input('url', array('type' => 'text',
                    'label' => false,
                    'class' => 'update_url',
                    'disabled' => 'disabled',
                    'rel' => 'no-history'));
                ?> 
            </div>
            <p style="color: black;margin-left: 146px; font-size: 12px; font-style: italic;">
                <strong>Ex:</strong> 
                www.example.com
            </p>
        </div>
        <?php
        echo $this->Form->end();
        ?>  
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    //echo $this->Form->create('Export', array(
//        'type' => 'POST',
//        'url' => array('controller' => 'project_evolutions', 'action' => 'export', $projectName['Project']['id'])));
//    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
//    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __d(sprintf($_domain, 'Sales'), "Sales", true) . ': ' . $activityName['Activity']['name']; ?></h2>
                    <a href="javascript:void(0);" class="wd-add-project" id="add-new-sales" style="margin-right:5px;" onclick="addNewSalesButton();"><span><?php __d(sprintf($_domain, 'Sales'), 'Sales') ?></span></a>		
                    <a href="javascript:void(0);" class="btn btn-reset-filter" id="clean-filters" style="margin-right:5px;" title="Clean filters"></a>

                    <?php /*
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                    */ ?>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%; height: 430px;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden; margin-top: 30px;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
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
    // array(
    //     'id' => 'no.',
    //     'field' => 'no.',
    //     'name' => '#',
    //     'width' => 40,
    //     'sortable' => false,
    //     'resizable' => false,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.nameSales'
    // ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __d(sprintf($_domain, 'Sales'), 'Name', true),
        'width' => 160,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBoxCustom',
        'validator' => 'DateValidate.name',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
    // array(
    //     'id' => 'new_invoice',
    //     'field' => 'new_invoice',
    //     'name' => __(' ', true),
    //     'width' => 95,
    //     'sortable' => false,
    //     'resizable' => false,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.ActionNewInvoice'
    // ),
    array(
        'id' => 'budget_customer_id',
        'field' => 'budget_customer_id',
        'name' => __d(sprintf($_domain, 'Sales'), 'Customer', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DateValidate.customer',
        'formatter' => 'Slick.Formatters.Customers'
    ),
    array(
        'id' => 'order_date',
        'field' => 'order_date',
        'name' => __d(sprintf($_domain, 'Sales'), 'Order date', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.orderDate',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
    // array(
    //     'id' => 'name_invoi',
    //     'field' => 'name_invoi',
    //     'name' => __(' ', true),
    //     'width' => 150,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'editor' => 'Slick.Editors.textBoxCustom',
    //     'formatter' => 'Slick.Formatters.nameSales'
    // ),
    array(
        'id' => 'sold',
        'field' => 'sold',
        'name' => __d(sprintf($_domain, 'Sales'), 'Sold €', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue',
    ),
    array(
        'id' => 'billed',
        'field' => 'billed',
        'name' => __d(sprintf($_domain, 'Sales'), 'To Bill €', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
    ),
    array(
        'id' => 'billed_check',
        'field' => 'billed_check',
        'name' => __d(sprintf($_domain, 'Sales'), 'Billed €', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.selectBox',
        'formatter' => 'Slick.Formatters.erroValue'
        //'formatter' => 'Slick.Formatters.Customers'
        //'editor' => 'Slick.Editors.numericValue',
        //'formatter' => 'Slick.Formatters.erroValue'
    ),
    array(
        'id' => 'paid',
        'field' => 'paid',
        'name' => __d(sprintf($_domain, 'Sales'), 'Paid €', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
    ),
    array(
        'id' => 'man_day',
        'field' => 'man_day',
        'name' => __d(sprintf($_domain, 'Sales'), 'M.D', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
     array(
        'id' => 'due_date',
        'field' => 'due_date',
        'name' => __d(sprintf($_domain, 'Sales'), 'Due Date', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
     array(
        'id' => 'effective_date',
        'field' => 'effective_date',
        'name' => __d(sprintf($_domain, 'Sales'), 'Effective Date', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
    array(
        'id' => 'reference',
        'field' => 'reference',
        'name' => __d(sprintf($_domain, 'Sales'), 'Reference', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.nameSales'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'reference2',
        'field' => 'reference2',
        'name' => __d(sprintf($_domain, 'Sales'), 'Reference 2', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.nameSales'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
     array(
        'id' => 'file_attachement',
        'field' => 'file_attachement',
        'name' => __d(sprintf($_domain, 'Sales'), 'Attachement or URL', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        'editor' => 'Slick.Editors.Attachement',
        'formatter' => 'Slick.Formatters.Attachement'
    ),
    array(
        'id' => 'justification',
        'field' => 'justification',
        'name' => __d(sprintf($_domain, 'Sales'), 'Justification', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.nameSales'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    // array(
    //     'id' => 'action.',
    //     'field' => 'action.',
    //     'name' => __d(sprintf($_domain, 'Sales'), 'Action', true),
    //     'width' => 70,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.Action'
    // )
);


$settings = $this->requestAction('/translations/getSettings',
    array('pass' => array(
        'Sales',
        array('sales')
    )
));
//su dung setting de sort lai $columns
$columns = Set::combine($columns, '{n}.field', '{n}');
$new = array();
foreach ($settings as $field => $value) {
    if( isset($columns[$field]) ){
        $new[] = $columns[$field];
        if( $field == 'name' ){
            $new[] = array(
                'id' => 'new_invoice',
                'field' => 'new_invoice',
                'name' => __(' ', true),
                'width' => 95,
                'sortable' => false,
                'resizable' => false,
                'noFilter' => 1,
                'formatter' => 'Slick.Formatters.ActionNewInvoice'
            );
            $new[] = array(
                'id' => 'name_invoi',
                'field' => 'name_invoi',
                'name' => __(' ', true),
                'width' => 150,
                'sortable' => false,
                'resizable' => true,
                'noFilter' => 1,
                'editor' => 'Slick.Editors.textBoxCustom',
                'formatter' => 'Slick.Formatters.nameSales'
            );
        }
    }
}
$columns = $new;
array_push($columns, array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
));
array_unshift($columns, array(
    'id' => 'no.',
    'field' => 'no.',
    'name' => '#',
    'width' => 40,
    'sortable' => true,
    'resizable' => false,
    'noFilter' => 1,
));


$i = 1;
$dataView = array();
$selectMaps = array(
    'budget_customer_id' => $budgetCustomers
);
foreach ($budgetSales as $budgetSale) {
    $data = array(
        'id' => $budgetSale['id'],
        'activity_id' => $budgetSale['activity_id'],
        'project_id' => $projectLinked
    );
    if(isset($budgetSale['id_invoice']) && !empty($budgetSale['id_invoice'])){
        $data['no.'] = '';
    } else {
        $data['no.'] = $i++;
    }
    if(isset($budgetSale['project_budget_sale_id']) && !empty($budgetSale['project_budget_sale_id'])){
        $data['id_invoice'] = $budgetSale['id_invoice'];
        $data['name_invoi'] = (string) $budgetSale['name_invoi'];
        $data['project_budget_sale_id'] = (string) $budgetSale['project_budget_sale_id'];
        $data['billed'] = (string) $budgetSale['billed'];
        $data['paid'] = (string) $budgetSale['paid'];
        $data['due_date'] = $str_utility->convertToVNDate($budgetSale['due_date']);
        $data['effective_date'] = $str_utility->convertToVNDate($budgetSale['effective_date']);
        $data['sold'] = '';
        $data['man_day'] = '';
        $data['billed_check'] = (string) $budgetSale['billed_check'];
    } else {
        $data['name'] = (string) $budgetSale['name'];
        $data['budget_customer_id'] = (string) $budgetSale['budget_customer_id'];
        $data['order_date'] = $str_utility->convertToVNDate($budgetSale['order_date']);
        $data['sold'] = (string) $budgetSale['sold'];
        $data['man_day'] = (string) $budgetSale['man_day'];
        $data['billed'] = (string) !empty($billed[$budgetSale['id']]) ? $billed[$budgetSale['id']] : 0;
        $data['paid'] = (string) !empty($paid[$budgetSale['id']]) ? $paid[$budgetSale['id']] : 0;
        $data['billed_check'] = (string) !empty($billed_check[$budgetSale['id']]) ? $billed_check[$budgetSale['id']] : 0;
    }
    $data['format'] = (string) $budgetSale['format'];
    $data['reference'] = (string) $budgetSale['reference'];
    $data['reference2'] = (string) $budgetSale['reference2'];
    $data['file_attachement'] = (string) $budgetSale['file_attachement'];
    $data['justification'] = (string) $budgetSale['justification'];
      
    $data['action.'] = '';
    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true)
);
$viewManDay = __('M.D', true);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s', '%4$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '%3$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '%3$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '%4$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<script type="text/javascript">
    var DateValidate = {},ControlGrid,IuploadComplete = function(json){ 
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
	var budgetCurrency = <?php echo json_encode($bg_currency);?>;
    (function($){
        
        $(function(){
            var $this = SlickGridCustom, gridControl;
            
            
            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = ControlGrid.getDataItem(row);
                if(data && confirm($this.t('Are you sure you want to delete attachement : %s'
                , data['file_attachement']))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['file_attachement'] = '';
                    ControlGrid.updateRow(row);
                }
                return false;
            });
            
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var activityName = <?php echo json_encode($activityName['Activity']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }
            var getTimeFomat = function(value){
                value = value.split("-");
                return (parseInt(value[2])+'/'+parseInt(value[1])+'/'+parseInt(value[0])).toString();
            }
            var actionTemplate =  $('#action-template').html(),
            attachmentTemplate =  $('#attachment-template').html(),
            attachmentURLTemplate =  $('#attachment-template-url').html(),
            actionTemplateNewInvoice = $('#action-template-new-invoice').html();
            
            $.extend(Slick.Formatters,{
                Attachement : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 2){
                            if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                                value = $this.t(attachmentTemplate,dataContext.id_invoice,row, 'invoice');
                            } else {
                                value = $this.t(attachmentTemplate,dataContext.id,row, 'sale');
                            }
                        }
                        if(dataContext.format == 1){
                            if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                                value = $this.t(attachmentURLTemplate,dataContext.id_invoice,dataContext.file_attachement,row, 'invoice');
                            } else {
                                value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.file_attachement,row, 'sale');
                            }
                        }
                    }
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                    } else {
                       return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent">' + value + '</span>', columnDef, dataContext);
                    }
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id_invoice,
                            dataContext.activity_id,dataContext.name_invoi, 'invoice'), columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                            dataContext.activity_id,dataContext.name, 'sale'), columnDef, dataContext);
                    }
                },
                ActionNewInvoice : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return '<span class="row-disabled"></span>';
                    } else {
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplateNewInvoice,row,
                            cell), columnDef, dataContext);
                    }
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        if(value == '' || !value){
                            if(columnDef.id == 'sold' || columnDef.id == 'billed_check'){
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-disabled row-number"> </span>', columnDef, dataContext);
                            } else {
                                return Slick.Formatters.HTMLData(row, cell, ' ', columnDef, dataContext);
                            }
                        } else {
                            value = number_format(value, 2, ',', ' ');
                            if(columnDef.id == 'billed_check'){
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number row-disabled">' + value + '</span> '+ budgetCurrency, columnDef, dataContext);
                            } else {
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> €', columnDef, dataContext);
                            }
                        }
                    } else {
                        if(value == '' || !value){
                            value = '';
                        } else {
                            value = number_format(value, 2, ',', ' ') + ' '+ budgetCurrency;
                        }
                        if(columnDef.id == 'billed' || columnDef.id == 'paid' || columnDef.id == 'billed_check'){
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-disabled row-number">' + value + '</span>', columnDef, dataContext);
                        } else {
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent row-number">' + value + '</span>', columnDef, dataContext);
                        }
                    }
        		},
                percentValues : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
        			return Slick.Formatters.HTMLData(row, cell, value + '%', columnDef, dataContext);
        		},
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        if(value == '' || !value){
                            if(columnDef.id == 'man_day'){
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-disabled"> </span>', columnDef, dataContext);
                            } else {
                                return Slick.Formatters.HTMLData(row, cell, ' ', columnDef, dataContext);
                            }
                        } else {
                            value = number_format(value, 2, ',', ' ');
                            return Slick.Formatters.HTMLData(row, cell, value + ' ' + viewManDay, columnDef, dataContext);
                        }
                    } else {
                        if(value == ''){
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent row-number">' + '' + '</span>', columnDef, dataContext);
                        } else {
                            value = number_format(value, 2, ',', ' ');
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent row-number">' + value + '</span>' + ' ' + viewManDay, columnDef, dataContext);
                        }
                    }
                    
        		},
                nameSales : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        if(value == '' || !value){
                            value = '';
                        }
                        if(columnDef.id == 'name' || columnDef.id == 'order_date' || columnDef.id == 'no.'){
                            return '<span class="row-disabled">' + value + '</span>';
                        } else {
                            if(columnDef.id == 'due_date'){
                                if(value == ''){
                                    return '';
                                } else {
                                    var currentDate = new Date().getTime();
                                    var dueDate = new Date(getTimeFomat(value)).getTime();
                                    if(currentDate > dueDate && dataContext.effective_date == ''){
                                        return '<span style="color: red;">' + value + '</span>';
                                    } else {
                                        return '<span style="color: black;">' + value + '</span>';;
                                    }
                                }
                            } else {
                                return value;
                            }
                        }
                    } else {
                        if(!value){
                            value = '';
                        }
                        if(columnDef.id == 'name_invoi' || columnDef.id == 'due_date' || columnDef.id == 'effective_date'){
                            return '<span class="row-disabled">' + value + '</span>';
                        } else {
                            return '<span class="row-parent">' + value + '</span>';
                        }
                    }
                },
                Customers : function(row, cell, value, columnDef, dataContext){
        			var _value = [];
        			value && value != '0' && value != 'null' && $.each( (value = value || []) && $.isArray(value) ? value : [value], function(i,val){
        				_value.push($this.selectMaps[columnDef.id][val] || val);
        			});
                    if((dataContext.project_budget_sale_id && dataContext.project_budget_sale_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-disabled">' + _value.join(', ') + '</span>', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent">' + _value.join(', ') + '</span>', columnDef, dataContext);
                    }
        		},
            });
            
            $.extend(Slick.Editors,{
                Attachement : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
        			this.input = $("<a href='#' id='action-attach-url'></a><div class='browse'></div>")
        			.appendTo(args.container).attr('rel','no-history').addClass('editor-text');
                    $("#ok_attach").click(function(){
                        //self.input[0].remove();
                        $('#action-attach-url').css('display', 'none');
                        $('.browse').css('display', 'block');
                        $("#dialog_attachement_or_url").dialog('close');
                        
                        var form = $("#form_dialog_attachement_or_url");
                        if(args.item.project_budget_sale_id || args.item.id_invoice){
                            form.find('input[name="data[Upload][id]"]').val(args.item.id);
                            form.find('input[name="data[Upload][id_invoice]"]').val(args.item.id_invoice);
                        } else {
                            form.find('input[name="data[Upload][id]"]').val(args.item.id);
                            form.find('input[name="data[Upload][id_invoice]"]').val('');
                        }
                        form.submit();
                    });
                    this.focus();
                },
                numericValueBudget : function(args){
        			$.extend(this, new Slick.Editors.textBox(args));
        			this.input.attr('maxlength' , 10).keypress(function(e){
        				var key = e.keyCode ? e.keyCode : e.which;
        				if(!key || key == 8 || key == 13){
        					return;
        				}
        				var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        ///^[\-+]??$/
                        //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
        				if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
        					e.preventDefault();
        					return false;
        				}
        			});
        		},
                textBoxCustom : function(args){
                    var text_holder = ' ';
                    if(args.column.id == 'name'){
                        text_holder = 'New Sale';
                    } else if(args.column.id == 'name_invoi'){
                        text_holder = 'New Invoice';
                    } else {
                        text_holder = ' ';
                    }
        			$.extend(this, new BaseSlickEditor(args));
        			this.input = $("<input type='text' placeholder='" +text_holder+ "'/>")
        			.appendTo(args.container).attr('rel','no-history').addClass('editor-text placeholder');
        			this.focus();
        		},
            });
            
            DateValidate.name = function(value, args){
                var result = true;
                $.each(args.grid.getData().getItems(), function(undefined,row){
                    var namAll = '';
                    if(row.name){
                        namAll = row.name.toLowerCase();
                    }
                    if(value.toLowerCase() == namAll && row.order_date == args.item.order_date && row.budget_customer_id == args.item.budget_customer_id){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('This customer, name and order date is exist.')
                };
            };
            DateValidate.customer = function(value, args){
                var result = true;
                $.each(args.grid.getData().getItems(), function(undefined,row){
                    if(value == row.budget_customer_id && row.order_date == args.item.order_date && row.name.toLowerCase() == args.item.name.toLowerCase()){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('This customer, name and order date is exist.')
                };
            };
            DateValidate.orderDate = function(value, args){
                var result = true;
                $.each(args.grid.getData().getItems(), function(undefined,row){
                    if(value == row.order_date && row.budget_customer_id == args.item.budget_customer_id && row.name.toLowerCase() == args.item.name.toLowerCase()){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('This customer, name and order date is exist.')
                };
            };
            
            var  data = <?php echo json_encode($dataView); ?>;
            //var capexTypes = <?php //echo json_encode($capexTypes); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: false      
            };
            var projectLinked = <?php echo json_encode($projectLinked);?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                // field save dung chung cho 2 table project budget sales & invoices
                id : {defaulValue : 0},
                id_invoice: {defaulValue : 0},
                activity_id : {defaulValue : activityName['id'], allowEmpty : false},
                reference : {defaulValue : ''},
                reference2 : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                justification : {defaulValue : ''},
                project_id : {defaulValue : projectLinked},
                // field save cho project budget sales
                name : {defaulValue : '' , allowEmpty : false},
                budget_customer_id : {defaulValue : ''},
                order_date : {defaulValue : ''},
                sold : {defaulValue : ''},
                man_day : {defaulValue : ''},
                // field save cho project budget invoices
                project_budget_sale_id : {defaulValue : ''},
                name_invoi : {defaulValue : '' , allowEmpty : false},
                billed : {defaulValue : ''},
                paid : {defaulValue : ''},
                due_date : {defaulValue : ''},
                effective_date : {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.onBeforeEdit = function(args){
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').parent().addClass('rowCurrentEdit');
                $('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                if(args.item && args.item.project_budget_sale_id){
                    $this.fields.name.allowEmpty = true;
                    $this.fields.name_invoi.allowEmpty = false;
                    if(args.column.field == 'name'
                        || args.column.field == 'budget_customer_id'
                        || args.column.field == 'order_date'
                        || args.column.field == 'sold'
                        || args.column.field == 'man_day'
                    ){
                        return false;
                    }
                    if(args.column.field == 'file_attachement' && (args.item['name_invoi'] == '' || args.item['file_attachement'])){
                        return false;
                    }
                } else {
                    $this.fields.name.allowEmpty = false;
                    $this.fields.name_invoi.allowEmpty = true;
                    if(args.column.field == 'name_invoi'
                        || args.column.field == 'billed'
                        || args.column.field == 'paid'
                        || args.column.field == 'due_date'
                        || args.column.field == 'effective_date'
                    ){
                        return false;
                    }
                    if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
                        return false;
                    }
                }
                return true;
            }
            ControlGrid = $this.init($('#project_container'),data,columns,options);
            var dataView = new Slick.Data.DataView();
            var _soldEuros = _billedEuros = _paidEuros = _manDays = _billedCheckEuros = 0;
            $.each(data, function(ind, vl){
                if((vl.id_invoice && vl.id_invoice != 0) || vl.project_budget_sale_id){
                    _billedEuros += vl.billed ? parseFloat(vl.billed) : 0;
                    _paidEuros += vl.paid ? parseFloat(vl.paid) : 0;
                    _billedCheckEuros += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                } else {
                    _soldEuros += vl.sold ? parseFloat(vl.sold) : 0;
                    _manDays += vl.man_day ? parseFloat(vl.man_day) : 0;
                }
            });           
            _soldEuros = number_format(_soldEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            _billedEuros = number_format(_billedEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            _billedCheckEuros = number_format(_billedCheckEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            _paidEuros = number_format(_paidEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            _manDays = number_format(_manDays, 2, ',', ' ') + ' ' + viewManDay;
            
            $this.onCellChange = function(args){
                $('.row-parent').parent().addClass('row-parent-custom');
                $('.row-disabled').parent().addClass('row-disabled-custom');
                $('.row-number').parent().addClass('row-number-custom');
                if(args.item){
                    if(args.item.effective_date && args.item.effective_date != ''){
                        args.item.billed_check = args.item.billed;
                    } else {
                        args.item.billed_check = '';
                    }
                    _soldEuros = _billedEuros = _paidEuros = _manDays = _billedCheckEuros = 0;
                    var billed = [];
                    var paid = [];
                    var billed_check = [];
                    $.each(data, function(ind, vl){
                        if((vl.id_invoice && vl.id_invoice != 0) || vl.project_budget_sale_id){
                            _billedEuros += vl.billed ? parseFloat(vl.billed) : 0;
                            _paidEuros += vl.paid ? parseFloat(vl.paid) : 0;
                            _billedCheckEuros += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                            if(vl.project_budget_sale_id){
                                if(!billed[vl.project_budget_sale_id]){
                                    billed[vl.project_budget_sale_id] = 0;
                                }
                                billed[vl.project_budget_sale_id] += vl.billed ? parseFloat(vl.billed) : 0;
                                if(!paid[vl.project_budget_sale_id]){
                                    paid[vl.project_budget_sale_id] = 0;
                                }
                                paid[vl.project_budget_sale_id] += vl.paid ? parseFloat(vl.paid) : 0;
                                if(!billed_check[vl.project_budget_sale_id]){
                                    billed_check[vl.project_budget_sale_id] = 0;
                                }
                                billed_check[vl.project_budget_sale_id] += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                             }
                        } else {
                            _soldEuros += vl.sold ? parseFloat(vl.sold) : 0;
                            _manDays += vl.man_day ? parseFloat(vl.man_day) : 0;
                        }
                    });
                    _soldEuros = number_format(_soldEuros, 2, ',', ' ') + ' '+ budgetCurrency;
                    _billedEuros = number_format(_billedEuros, 2, ',', ' ') + ' '+ budgetCurrency;
                    _billedCheckEuros = number_format(_billedCheckEuros, 2, ',', ' ') + ' '+ budgetCurrency;
                    _paidEuros = number_format(_paidEuros, 2, ',', ' ') + ' '+ budgetCurrency;
                    _manDays = number_format(_manDays, 2, ',', ' ') + ' ' + viewManDay;
                    $('#gs-sold-euro p').html(_soldEuros);
                    $('#gs-billed-euro p').html(_billedEuros);
                    $('#gs-billed-check-euro p').html(_billedCheckEuros);
                    $('#gs-paid-euro p').html(_paidEuros);
                    $('#gs-man-day p').html(_manDays);
                    if(args.item.project_budget_sale_id || (args.item.id_invoice && args.item.id_invoice != 0)){
                        var _rowParent = args.grid.getData().getRowById(args.item.project_budget_sale_id);
                        args.grid.getData().getItems()[_rowParent].billed = billed[args.item.project_budget_sale_id] ? billed[args.item.project_budget_sale_id] : 0;
                        args.grid.getData().getItems()[_rowParent].paid = paid[args.item.project_budget_sale_id] ? paid[args.item.project_budget_sale_id] : 0;
                        args.grid.getData().getItems()[_rowParent].billed_check = billed_check[args.item.project_budget_sale_id] ? billed_check[args.item.project_budget_sale_id] : 0;
                        args.grid.updateRow(_rowParent);
                    }
                }
                return true;
            }
            var _ids = 999999999999;
            contextMenuButton = function(row, cell){
                var currentRows = ControlGrid.getData().getItems()[row];
                var newRow = {
                    id: _ids++, 
                    id_invoice: 0,
                    activity_id: activityName['id'],
                    project_id : projectLinked,
                    project_budget_sale_id : currentRows.id,
                    name_invoi : '',
                    billed : '',
                    billed_check: '',
                    paid : '',
                    due_date : '',
                    effective_date : '',
                    reference : '',
                    reference2 : '',
                    file_attachement : '',
                    justification : '',
                    sold: '',
                    man_day: '',
                };
                var rowData = ControlGrid.getData().getItems();
                var currentCell = row;
                if(currentCell == rowData.length-1){
                    currentCell = currentCell+1;
                } else {
                    for(var i = currentCell; i <= rowData.length-1; i++){
                        currentCell++;
                        if(rowData[currentCell] && !rowData[currentCell].project_budget_sale_id){
                            break;
                        }
                    }
                } 
                ControlGrid.invalidateRow(row);
                rowData.splice(currentCell, 0, newRow);             
                ControlGrid.getData().setItems(rowData);
                ControlGrid.render();
                ControlGrid.scrollRowIntoView(currentCell, false);
                $('.row-parent').parent().addClass('row-parent-custom');
                $('.row-disabled').parent().addClass('row-disabled-custom');
                $('.row-number').parent().addClass('row-number-custom');
                ControlGrid.gotoCell(currentCell, 5, true);
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').parent().addClass('rowCurrentEdit');
                $('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
            } 
            addNewSalesButton = function(){
                ControlGrid.gotoCell(data.length, 1, true);
            }
            /* Phan comment lai la phan menu chuot phai....khi click chuot phai thi new moi 1 invoice
            var _gridView;
            $this.onContextMenu = function(gridView){
                 _gridView = gridView;
                var cell = gridView.grid.getCellFromEvent(gridView.record);
                var currentRows = gridView.grid.getData().getItems()[cell.row];
                if(!currentRows){
                    return;
                } else {
                    if(currentRows.project_budget_sale_id){
                        return;
                    }
                }
                $('#contextMenu')
                    .data("row", cell.row)
                    .css("top", gridView.record.pageY)
                    .css("left", gridView.record.pageX)
                    .show();
                $("body").one("click", function () {
                    $('#contextMenu').hide();
                });
            }     
            $("#contextMenu").click(function(e) {
                var _grids = _gridView.grid;
                var cell = _gridView.grid.getCellFromEvent(_gridView.record);
                var currentRows = _gridView.grid.getData().getItems()[cell.row];
                var newRow = {
                    id: _ids++, 
                    id_invoice: 0,
                    project_id: projectName['id'],
                    project_budget_sale_id : currentRows.id,
                    name_invoi : '',
                    billed : '',
                    billed_check: '',
                    paid : '',
                    due_date : '',
                    effective_date : '',
                    reference : '',
                    reference2 : '',
                    file_attachement : '',
                    justification : '',
                    sold: '',
                    man_day: '',
                };
                var rowData = _grids.getData().getItems();
                var currentCell = cell.row;
                if(currentCell == rowData.length-1){
                    currentCell = currentCell+1;
                } else {
                    for(var i = currentCell; i <= rowData.length-1; i++){
                        currentCell++;
                        if(rowData[currentCell] && !rowData[currentCell].project_budget_sale_id){
                            break;
                        }
                    }
                }
                _grids.invalidateRow(cell.row);
                rowData.splice(currentCell, 0, newRow);             
                _grids.getData().setItems(rowData);
                _grids.render();
                _grids.scrollRowIntoView(currentCell, false);
                $('.row-parent').parent().addClass('row-parent-custom');
                $('.row-disabled').parent().addClass('row-disabled-custom');
                $('.row-number').parent().addClass('row-number-custom');
                _grids.gotoCell(currentCell, 5, true);
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').parent().addClass('rowCurrentEdit');
                $('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
            });
            */
            // Chuyen header sang mau xanh la cay
            var headers = $('.slick-header-columns').get(0).children;
            $.each(headers, function(index, val){
                if(headers[index].id.indexOf('man_day') != -1){
                    $('#'+headers[index].id).addClass('gs-custom-cell-md-header');
                }
            });
            var cols = ControlGrid.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            // khi keo scroll thi to mau cac cell
            ControlGrid.onScroll.subscribe(function(args, e, scope){
                $('.row-parent').parent().addClass('row-parent-custom');
                $('.row-disabled').parent().addClass('row-disabled-custom');
                $('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onColumnsResized.subscribe(function (e, args) {			 
				var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
			});
           // ui-state-default slick-headerrow-column
            var settings = <?php echo json_encode(array_keys($settings)); ?>;
            var general = '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>';
            var hd = {
                name : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" style="text-align: left;"><p>Total</p></div>',
                sold : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-sold-euro"><p>' +_soldEuros+ '</p></div>',
                billed : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-billed-euro"><p>' +_billedEuros+ '</p></div>',
                billed_check : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-billed-check-euro"><p>' +_billedCheckEuros+ '</p></div>',
                paid : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-paid-euro"><p>' +_paidEuros+ '</p></div>',
                man_day : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-md" id="gs-man-day"><p>' +_manDays+ '</p></div>'
            };
            var header = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">'
                        + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>';
            var i = 1;
            $.each(settings, function(sadasd, field){
                var classes = 'l' + i + ' r' + i;
                if( hd[field] ){
                    header += hd[field].replace('%s', classes);
                    if( field == 'name' ){
                        //them 2 field invoice & name_invoice
                        header += general.replace('%s', 'l' + ++i + ' r' + i);
                        header += general.replace('%s', 'l' + ++i + ' r' + i);
                    }
                } else {
                    header += general.replace('%s', classes);
                }
                ++i;
            });
            header += '<div class="ui-state-default slick-headerrow-column l'+ i +' r'+i+' wd-row-custom wd-custom-cell gs-custom-cell-erro"></div></div>';
            // header =
            //     '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">'
            //         + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l1 r1 wd-row-custom wd-custom-cell gs-custom-cell-erro" style="text-align: left;"><p>Total</p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l2 r2 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l3 r3 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l4 r4 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l5 r5 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l6 r6 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-sold-euro"><p>' +_soldEuros+ '</p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l7 r7 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-billed-euro"><p>' +_billedEuros+ '</p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l8 r8 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-billed-check-euro"><p>' +_billedCheckEuros+ '</p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l9 r9 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-paid-euro"><p>' +_paidEuros+ '</p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l10 r10 wd-row-custom wd-custom-cell gs-custom-cell-md" id="gs-man-day"><p>' +_manDays+ '</p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l11 r11 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l12 r12 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l13 r13 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l14 r14 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l15 r15 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l16 r16 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //         + '<div class="ui-state-default slick-headerrow-column l17 r17 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
            //   + '</div>';
            $('.slick-header-columns').after(header);
            
            /* table .end */
            var createDialog = function(){
                $('#dialog_attachement_or_url').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialog = $.noop;
            }
            
            $("#action-attach-url").live('click',function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            
            $(".cancel").live('click',function(){
                $("#dialog_attachement_or_url").dialog('close');
            });
            $("#gs-url").click(function(){
                $(this).addClass('gs-url-add');
                $('#gs-attach').addClass('gs-attach-remove');
                $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
                $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $("#gs-attach").click(function(){
                $(this).removeClass('gs-attach-remove');
                $('#gs-url').removeClass('gs-url-add');
                $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
                $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
             function setupScroll(){
                $("#scrollTopAbsenceContent").width($(".grid-canvas").width()+50);
                $("#scrollTopAbsence").width($(".slick-viewport").width());
            }
            setTimeout(function(){
                setupScroll();
            }, 2500);
            $("#scrollTopAbsence").scroll(function () {
                $(".slick-viewport").scrollLeft($("#scrollTopAbsence").scrollLeft());
            });
            $(".slick-viewport").scroll(function () {
                $("#scrollTopAbsence").scrollLeft($(".slick-viewport").scrollLeft());
            });
        });
        
    })(jQuery);
</script>
<script>
    //format float number
    var path = <?php echo json_encode($this->params['url']['url']); ?>;
    function number_format(number, decimals, dec_point, thousands_sep) {
      // http://kevin.vanzonneveld.net
      // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +     bugfix by: Michael White (http://getsprink.com)
      // +     bugfix by: Benjamin Lupton
      // +     bugfix by: Allan Jensen (http://www.winternet.no)
      // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
      // +     bugfix by: Howard Yeend
      // +    revised by: Luke Smith (http://lucassmith.name)
      // +     bugfix by: Diogo Resende
      // +     bugfix by: Rival
      // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
      // +   improved by: davook
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +      input by: Jay Klehr
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +      input by: Amir Habibi (http://www.residence-mixte.com/)
      // +     bugfix by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Theriault
      // +      input by: Amirouche
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // *     example 1: number_format(1234.56);
      // *     returns 1: '1,235'
      // *     example 2: number_format(1234.56, 2, ',', ' ');
      // *     returns 2: '1 234,56'
      // *     example 3: number_format(1234.5678, 2, '.', '');
      // *     returns 3: '1234.57'
      // *     example 4: number_format(67, 2, ',', '.');
      // *     returns 4: '67,00'
      // *     example 5: number_format(1000);
      // *     returns 5: '1,000'
      // *     example 6: number_format(67.311, 2);
      // *     returns 6: '67.31'
      // *     example 7: number_format(1000.55, 1);
      // *     returns 7: '1,000.6'
      // *     example 8: number_format(67000, 5, ',', '.');
      // *     returns 8: '67.000,00000'
      // *     example 9: number_format(0.9, 0);
      // *     returns 9: '1'
      // *    example 10: number_format('1.20', 2);
      // *    returns 10: '1.20'
      // *    example 11: number_format('1.20', 4);
      // *    returns 11: '1.2000'
      // *    example 12: number_format('1.2000', 3);
      // *    returns 12: '1.200'
      // *    example 13: number_format('1 000,50', 2, '.', ' ');
      // *    returns 13: '100 050.00'
      // Strip all characters but numerical ones.
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
    $(document).ready(function(){
        $('#clean-filters').click(function(){
            $.ajax({
                url: '/activity_budget_externals/clean_filters/',
                type: 'POST',
                data: {
                    path: path,
                },
                dataType: 'json',
                success: function(data){
                    location.reload();
                }
            });
            
        });
    });
</script>


<style type="text/css">
    #wd-header-custom{
        height: 30px; 
        border: 1px solid #E0E0E0; 
        border-bottom: none;
        border-right: none !important;
    }
    .wd-row-custom{
        margin-right: -7px;
    }
    .wd-row-custom p{
        padding-top: 5px;
        font-weight: bold;
    }
    .wd-custom-cell{
        background: none !important;
        border-right: none !important;
        /*width: 100%;*/
    }
    .slick-viewport{
        /*height: 76% !important;*/
    }
    #project_container{
        overflow: visible !important;
    }
    .gs-custom-cell-erro{
        background-color: #95B3D7 !important;
    }
    .gs-custom-cell-md-header{
        background: #75923C !important;
    }
    .gs-custom-cell-md{
        background-color: #C2D69A !important;
    }
    .cl-average-daily-rate{
        padding: 7px;
        margin-left: -12px;
        width: 105px;
    }
    .color-rate-loading{
        color: #ccc;
    }
    .color-rate-success{
        color: #3BBD43;
    }
    .color-rate-error{
        color: #F71230;
    }
    .slick-row.odd{
        background: #FFF !important;
    }
    .row-parent-custom{
        background-color: #EAF1FA;
    }
    .row-disabled-custom{
        background-color: #FAFAFA !important;
    }
    .row-number-custom{
        text-align: right;
    }
    .row-current-edit{
        border-top: 1px solid #004482 !important;
        border-bottom: 1px solid #004482 !important;
        box-shadow: 0px 0px 3px #004482;
        /*
        zoom: 1;
        filter: progid:DXImageTransform.Microsoft.DropShadow(OffX=0, OffY=0, Color=#00FF3D),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=0),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=90),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=180),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=270),
                progid:DXImageTransform.Microsoft.Chroma(Color='#ffffff');
        
        filter: 
            progid:DXImageTransform.Microsoft.DropShadow(OffX=0, OffY=0, Color=#00FF3D),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=0),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=90),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=180),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=270);
        
        
        -ms-filter: 
        "progid:DXImageTransform.Microsoft.Shadow(Strength=15, Direction=0, Color='#00FF3D')",
        "progid:DXImageTransform.Microsoft.Shadow(Strength=15, Direction=180, Color='#00FF3D')"
        ;
        */
    }
</style>