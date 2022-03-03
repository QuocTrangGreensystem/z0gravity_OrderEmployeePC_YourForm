<style type="text/css">
	#wd-container-footer{
		display: none;
	}
	body{
		overflow: hidden;
	}
	.slick-viewport-right{
		overflow-x: hidden !important;
		overflow-y: auto;
	}
	.slick-viewport-left{
		overflow: hidden !important;
	}
</style>
<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
	'preview/project_decisions'
));
echo $this->Html->script(array(
    'history_filter',
    'jquery.multiSelect',
    //'responsive_table',
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
echo $this->element('dialog_projects');
$menu = $this->requestAction('/menus/getMenu/project_budget_purchases/index');
$canModified = (($modifyBudget == true && !$_isProfile) || ($_isProfile && $_canWrite)) ? true : false;
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<div id="action-template-new-invoice" style="display: none;">
    <div>
        <a onclick="contextMenuButton(%1$s, %2$s);" href="javascript:void(0);" class="btn btn-plus-green" title="<?php echo __('Add an invoice', true);?>">
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
                'url' => array('controller' => 'project_budget_purchases', 'action' => 'upload')
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
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_budget_purchases', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
					<?php if( $canModified){?>
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px;" onclick="addNewSalesButton();" title="<?php __('Add an order') ?>"></a>
					<?php } ?> 
                   <!--  <a id="export-submitplus" href="javascript:void(0);" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel+ ') ?></span></a> -->
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
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_phase_plans', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
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
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$columns = array(
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __d(sprintf($_domain, 'Purchase'), 'Name', true),
        'width' => 160,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBoxCustom',
        'validator' => 'DateValidate.name',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
    array(
        'id' => 'budget_customer_id',
        'field' => 'budget_customer_id',
        'name' => __d(sprintf($_domain, 'Purchase'), 'Customer', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'Order date', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.orderDate',
        'formatter' => 'Slick.Formatters.Purchases'
    ),
    array(
        'id' => 'sold',
        'field' => 'sold',
        'name' => __d(sprintf($_domain, 'Purchase'), 'Sold €', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'To Bill €', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'Billed €', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
    ),
    array(
        'id' => 'paid',
        'field' => 'paid',
        'name' => __d(sprintf($_domain, 'Purchase'), 'Paid €', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), $md, true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'Due Date', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'Effective Date', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'Reference', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
    array(
        'id' => 'reference2',
        'field' => 'reference2',
        'name' => __d(sprintf($_domain, 'Purchase'), 'Reference 2', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
     array(
        'id' => 'file_attachement',
        'field' => 'file_attachement',
        'name' => __d(sprintf($_domain, 'Purchase'), 'Attachement or URL', true),
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
        'name' => __d(sprintf($_domain, 'Purchase'), 'Justification', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.nameSales'
    ),
);


$settings = $this->requestAction('/translations/getSettings',
    array('pass' => array(
        'Purchase',
        array('Purchase')
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
                'width' => 45,
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
    'name' => __(' ', true),
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
        'project_id' => $budgetSale['project_id'],
        'activity_id' => $activityLinked
    );
    if(isset($budgetSale['id_invoice']) && !empty($budgetSale['id_invoice'])){
        $data['no.'] = '';
    } else {
        $data['no.'] = $i++;
    }
    if(isset($budgetSale['project_budget_purchase_id']) && !empty($budgetSale['project_budget_purchase_id'])){
        $data['id_invoice'] = $budgetSale['id_invoice'];
        $data['name_invoi'] = (string) $budgetSale['name_invoi'];
        $data['project_budget_purchase_id'] = (string) $budgetSale['project_budget_purchase_id'];
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
$viewManDay = __($md, true);
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
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 550) ? 550 : heightTable;
    wdTable.css({
        height: heightTable,
    });
    $(window).resize(function(){
        heightTable = $(window).height() - wdTable.offset().top - 40;
        //heightTable = (heightTable < 550) ? 550 : heightTable;
        wdTable.css({
            height: heightTable,
        });
    });
    var budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
    var DateValidate = {},ControlGrid,IuploadComplete = function(json){
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
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
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
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
                            if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                                value = $this.t(attachmentTemplate,dataContext.id_invoice,row, 'invoice');
                            } else {
                                value = $this.t(attachmentTemplate,dataContext.id,row, 'sale');
                            }
                        }
                        if(dataContext.format == 1){
                            if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                                value = $this.t(attachmentURLTemplate,dataContext.id_invoice,dataContext.file_attachement,row, 'invoice');
                            } else {
                                value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.file_attachement,row, 'sale');
                            }
                        }
                    }
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                    } else {
                       return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent">' + value + '</span>', columnDef, dataContext);
                    }
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id_invoice,
                            dataContext.project_id,dataContext.name_invoi, 'invoice'), columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                            dataContext.project_id,dataContext.name, 'sale'), columnDef, dataContext);
                    }
                },
                ActionNewInvoice : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        return '<span class="row-disabled"></span>';
                    } else {
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplateNewInvoice,row,
                            cell), columnDef, dataContext);
                    }
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
                        if(value == '' || !value){
                            if(columnDef.id == 'sold' || columnDef.id == 'billed_check'){
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-disabled row-number"> </span >', columnDef, dataContext);
                            } else {
                                return Slick.Formatters.HTMLData(row, cell, ' ', columnDef, dataContext);
                            }
                        } else {
                            value = number_format(value, 2, ',', ' ');
                            if(columnDef.id == 'billed_check'){
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number row-disabled">' + value + ' ' + budget_settings + '</span> ', columnDef, dataContext);
                            } else {
                                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value +  ' ' + budget_settings +  '</span> ', columnDef, dataContext);
                            }
                        }
                    } else {
                        if(value == '' || !value){
                            value = '';
                        } else {
                            value = number_format(value, 2, ',', ' ') + ' ' + budget_settings;
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
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
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
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
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
                    if((dataContext.project_budget_purchase_id && dataContext.project_budget_purchase_id != '') || (dataContext.id_invoice && dataContext.id_invoice != 0)){
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
                        if(args.item.project_budget_purchase_id || args.item.id_invoice){
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
                        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
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
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: true,
                frozenColumn: 1
            };
            var activityLinked = <?php echo json_encode($activityLinked);?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                // field save dung chung cho 2 table project budget sales & invoices
                id : {defaulValue : 0},
                id_invoice: {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                reference : {defaulValue : ''},
                reference2 : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                justification : {defaulValue : ''},
                activity_id : {defaulValue : activityLinked},
                // field save cho project budget sales
                name : {defaulValue : '' , allowEmpty : false},
                budget_customer_id : {defaulValue : ''},
                order_date : {defaulValue : ''},
                sold : {defaulValue : ''},
                man_day : {defaulValue : ''},
                // field save cho project budget invoices
                project_budget_purchase_id : {defaulValue : ''},
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
                //$('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                if(args.item && args.item.project_budget_purchase_id){
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
            ControlGrid = $this.init($('#project_container'),data,columns,{
				frozenColumn: 1,
				rowHeight: 40,
				headerRowHeight: 40, 
			});
            var dataView = new Slick.Data.DataView();
            var _soldEuros = _billedEuros = _paidEuros = _manDays = _billedCheckEuros = 0;
            $.each(data, function(ind, vl){
                if((vl.id_invoice && vl.id_invoice != 0) || vl.project_budget_purchase_id){
                    _billedEuros += vl.billed ? parseFloat(vl.billed) : 0;
                    _paidEuros += vl.paid ? parseFloat(vl.paid) : 0;
                    _billedCheckEuros += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                } else {
                    _soldEuros += vl.sold ? parseFloat(vl.sold) : 0;
                    _manDays += vl.man_day ? parseFloat(vl.man_day) : 0;
                }
            });
            _soldEuros = number_format(_soldEuros, 2, ',', ' ') + ' '+ budget_settings;
            _billedEuros = number_format(_billedEuros, 2, ',', ' ') + ' '+ budget_settings;
            _billedCheckEuros = number_format(_billedCheckEuros, 2, ',', ' ')+ ' '+ budget_settings;
            _paidEuros = number_format(_paidEuros, 2, ',', ' ') + ' '+ budget_settings;
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
                        if((vl.id_invoice && vl.id_invoice != 0) || vl.project_budget_purchase_id){
                            _billedEuros += vl.billed ? parseFloat(vl.billed) : 0;
                            _paidEuros += vl.paid ? parseFloat(vl.paid) : 0;
                            _billedCheckEuros += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                            if(vl.project_budget_purchase_id){
                                if(!billed[vl.project_budget_purchase_id]){
                                    billed[vl.project_budget_purchase_id] = 0;
                                }
                                billed[vl.project_budget_purchase_id] += vl.billed ? parseFloat(vl.billed) : 0;
                                if(!paid[vl.project_budget_purchase_id]){
                                    paid[vl.project_budget_purchase_id] = 0;
                                }
                                paid[vl.project_budget_purchase_id] += vl.paid ? parseFloat(vl.paid) : 0;
                                if(!billed_check[vl.project_budget_purchase_id]){
                                    billed_check[vl.project_budget_purchase_id] = 0;
                                }
                                billed_check[vl.project_budget_purchase_id] += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                             }
                        } else {
                            _soldEuros += vl.sold ? parseFloat(vl.sold) : 0;
                            _manDays += vl.man_day ? parseFloat(vl.man_day) : 0;
                        }
                    });
                    _soldEuros = number_format(_soldEuros, 2, ',', ' ') + ' '+ budget_settings;
                    _billedEuros = number_format(_billedEuros, 2, ',', ' ')+ ' '+ budget_settings;
                    _billedCheckEuros = number_format(_billedCheckEuros, 2, ',', ' ') + ' '+ budget_settings;
                    _paidEuros = number_format(_paidEuros, 2, ',', ' ') + ' '+ budget_settings;
                    _manDays = number_format(_manDays, 2, ',', ' ') + ' ' + viewManDay;
                    $('#gs-sold-euro').html(_soldEuros);
                    $('#gs-billed-euro').html(_billedEuros);
                    $('#gs-billed-check-euro').html(_billedCheckEuros);
                    $('#gs-paid-euro').html(_paidEuros);
                    $('#gs-man-day').html(_manDays);
                    if(args.item.project_budget_purchase_id || (args.item.id_invoice && args.item.id_invoice != 0)){
                        var _rowParent = args.grid.getData().getRowById(args.item.project_budget_purchase_id);
                        args.grid.getData().getItems()[_rowParent].billed = billed[args.item.project_budget_purchase_id] ? billed[args.item.project_budget_purchase_id] : 0;
                        args.grid.getData().getItems()[_rowParent].paid = paid[args.item.project_budget_purchase_id] ? paid[args.item.project_budget_purchase_id] : 0;
                        args.grid.getData().getItems()[_rowParent].billed_check = billed_check[args.item.project_budget_purchase_id] ? billed_check[args.item.project_budget_purchase_id] : 0;
                        args.grid.updateRow(_rowParent);
                    }
                }
                var columns = args.grid.getColumns(),
                    col, cell = args.cell;
                do {
                    cell++;
                    if( columns.length == cell )break;
                    col = columns[cell];
                } while (typeof col.editor == 'undefined');

                if( cell < columns.length ){
                    args.grid.gotoCell(args.row, cell, true);
                } else {
                    //end of row
                    try {
                        args.grid.gotoCell(args.row + 1, 0);
                    } catch(ex) {}
                }
                return true;
            }
            var _ids = 999999999999;
            contextMenuButton = function(row, cell){
                var currentRows = ControlGrid.getData().getItems()[row];
                var newRow = {
                    id: _ids++,
                    id_invoice: 0,
                    project_id: projectName['id'],
                    activity_id : activityLinked,
                    project_budget_purchase_id : currentRows.id,
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
                        if(rowData[currentCell] && !rowData[currentCell].project_budget_purchase_id){
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
                    if(currentRows.project_budget_purchase_id){
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
                    project_budget_purchase_id : currentRows.id,
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
                        if(rowData[currentCell] && !rowData[currentCell].project_budget_purchase_id){
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
            var headers = $('.slick-header-columns').get(1).children;
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
            var settings = <?php echo json_encode(array_keys($settings)); ?>;
            var _header = {
                name: {id: 'gs-name', val: 'Total', cl: 'gsEuro'},
                sold: {id: 'gs-sold-euro',val: _soldEuros, cl: 'gsEuro'},
                billed: {id: 'gs-billed-euro',val: _billedEuros, cl: 'gsEuro'},
                billed_check: {id: 'gs-billed-check-euro',val: _billedCheckEuros, cl: 'gsEuro'},
                paid: {id: 'gs-paid-euro',val: _paidEuros, cl: 'gsEuro'},
                man_day: {id: 'gs-man-day',val: _manDays, cl: 'gsMd'}
            };
            $.each(settings, function(index, field){
                if(_header[field]){
                    $(ControlGrid.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p>');
                }
            });
            $('.gsMd').parent().addClass('gs-custom-cell-md');
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
        });

    })(jQuery);
</script>
<script>
    //format float number
    function number_format(number, decimals, dec_point, thousands_sep) {
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
    function setupScroll(){
        $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
        $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
    }
    setTimeout(function(){
        setupScroll();
    }, 2500);
    $("#scrollTopAbsence").scroll(function () {
        $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
    $(".slick-viewport-right:first").scroll(function () {
        $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right:first").scrollLeft());
    });
</script>
<script>
    $('#export-submitplus').click(function(){
        var length = ControlGrid.getDataLength(),
            list = [];
        for(var i = 0; i<length; i++){
            var item = ControlGrid.getDataItem(i);
            list.push(item.id);
        }
        $('#export-item-list')
        .val(list.join(','))
        .closest('form')
        .submit();
    });
</script>
<style type="text/css">
#wd-header-custom{height:30px;border:1px solid #E0E0E0;border-bottom:none;border-right:none!important;}
.slick-headerrow-columns div{margin-right:-7px;}.slick-headerrow-columns div p{padding-top:5px;font-weight:400;text-align: center;}#project_container{overflow:visible!important;}
.slick-headerrow-columns div{background-color:#b5d2e2;}
.gs-custom-cell-md-header{background:#75923C!important;}.gs-custom-cell-md{background-color:#C2D69A!important;}.cl-average-daily-rate{margin-left:-12px;width:135px;padding:7px;}.color-rate-loading{color:#ccc;}.color-rate-success{color:#3BBD43;}.color-rate-error{color:#F71230;}.slick-row.odd{background:#FFF!important;}.row-parent-custom{background-color:#EAF1FA;}.row-disabled-custom{background-color:#FAFAFA!important;}.row-number-custom{text-align:right;}.row-current-edit{border-top:1px solid #004482!important;border-bottom:1px solid #004482!important;box-shadow:0 0 3px #004482;}
</style>
