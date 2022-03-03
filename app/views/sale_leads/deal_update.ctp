<?php
    echo $html->script(array(
        'jquery.validation.min',
        'jquery.dataTables',
        'validateDate',
        'jquery.multiSelect',
        'jshashtable-2.1',
        'jquery.numberformatter-1.2.3',
        'jquery.maxlength-min',
        'multipleUpload/plupload.full.min',
        'multipleUpload/jquery.plupload.queue',
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
        'jquery.dataTables',
        'multipleUpload/jquery.plupload.queue',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
        'business'
    ));
    echo $this->element('dialog_projects');
    $disabled = '';
    if($modifySaleDeal == 'false'){
        $disabled = 'disabled';
    }
?>
<style>
    fieldset div textarea{height: auto !important;}
    #employee-place{
        float: left;
        width: 63%;
    }
    #employee-place .ui-combobox, #employee-place-2 .ui-combobox{
        width: 100%;
    }
    .pch_log_system{
        clear: both;
        border: 1px solid #0cb0e0;
        min-height: 70px;
    }
    .pch_log_avatar_content{
        float: left;
        border: none;
    }
    .pch_log_avatar .pch_log_avatar_content{
        padding: 5px;
        border: 1px solid #bbb;
        border-radius: 3px;
        margin: 5px;
    }
    .pch_log_avatar_content img{
        width: 35px;
        height: 35px;
        margin-top: 5px;
        margin-right: 10px;
        padding: 5px;
        border: 1px solid #bbb;
        border-radius: 3px;
    }
    .pch_log_body{
        margin-top: 10px;
    }
    .input_disabled{
        border: none;
        min-width: 13%;
        color: #08c;
        font-size: 13px;
    }
    .pch_log_description{
        margin-top: 5px;
        width: 98%;
        height: 16px;
        padding: 5px;
        background: #fff;
        border: 1px solid transparent;
        transition: border-color 0.5s;
    }
    .pch_log_description input:focus{
        border: 1px solid #08c;
        height: 25px;
    }
    .pch_log_description input{
        height: 20px;
        width: 95%;
        border: none;
        padding-left: 5px;
    }
    #pch_log_system_content{
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<div id="action-template" style="display: none;">
    <div id="product_action">
        <a class="pch_add_invoice" href="javascript:void(0);" onclick="addInvoice('<?php echo h('%2$s'); ?>', '<?php echo h('%3$s'); ?>', '<?php echo h('%5$s'); ?>');" title="<?php echo __('Add Invoice', true);?>">&nbsp;</a>
        <a class="pch_add_expense" href="javascript:void(0);" onclick="addExpense('<?php echo h('%2$s'); ?>', '<?php echo h('%3$s'); ?>');" title="<?php echo __('Add Expense', true);?>">&nbsp;</a>
        <!--a class="pch_delete" href="<?php //echo $this->Html->url(array('action' => 'delete_production', '%1$s', '%2$s', '%3$s')); ?>" onclick="return confirm('<?php //echo h(sprintf(__('Delete?', true), '%4$s')); ?>');" title="<?php //echo __('Delete Product', true);?>">&nbsp;</a-->
    </div>
</div>
<!-- avatar_popup -->
<div id="avatar_popup" style="display:none;" title="Avatar" class="buttons">
    <?php
    echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'sale_leads', 'action' => 'update_avatar', $company_id, $id)
    ));
    echo $this->Form->input('id', array('type' => 'hidden'));
    ?>
    <div class="wd-input">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/business/img-1.png"/><input type="file" id="textAvatar" name="FileField[attachment]" /></li>
        </ul>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="avatar_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End avatar_popup -->
<!-- pch_message_popup -->
<div id="pch_delete_invoice_popup" style="display:none;" title="<?php echo __('Delete All Invoices?', true);?>" class="buttons">
    <ul class="type_buttons" style="padding-right: 60px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="pch_delete_invoice_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
</div>
<!-- End pch_message_popup -->
<!-- pch_message_popup -->
<div id="pch_delete_expenses_popup" style="display:none;" title="<?php echo __('Delete Expenses?', true);?>" class="buttons">
    <ul class="type_buttons" style="padding-right: 60px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="pch_delete_expenses_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
</div>
<!-- End pch_message_popup -->
<!-- add_invoice_popup -->
<div id="add_invoice_popup" style="display:none; position: relative;" title="<?php echo __('Invoice', true);?>" class="buttons">
    <div id="pch_product_top">
        <input type="hidden" id="textID" />
        <div class="pch_input">
            <label><?php echo __('Product', true);?></label>
            <input type="text" id="textProduct" readonly="readonly" class="input_disabled" />
        </div>
        <div class="pch_input">
            <label><?php echo __('Start of billing', true);?></label>
            <input type="text" id="textStartOfBilling" />
        </div>
        <div class="pch_input">
            <label><?php echo __('Amount due', true);?></label>
            <input type="text" id="textAmountDue" readonly="readonly" class="input_disabled" />
            <span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;"><?php echo $saleCurrency;?></span>
        </div>
        <div class="pch_input">
            <label><?php echo __('Billing period', true);?></label>
            <select id="selectBillingPeriod">
                <?php
                    $billings = array('Manually', 'Annual', 'Biannual', 'Quarterly', 'Monthly');
                    foreach($billings as $idBilling => $value){
                        echo '<option value="' . $idBilling . '">' . $value . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="pch_input">
            <label><?php echo __('Number of payment', true);?></label>
            <input type="text" id="textNumberOfPayment" value="1"/>
        </div>
        <div class="pch_input">
            <label><?php echo __('Amount Due/Invoice', true);?></label>
            <input type="text" id="textAmountDueInvoice" readonly="readonly" class="input_disabled" />
            <span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;"><?php echo $saleCurrency;?></span>
        </div>
    </div>
    <div id="pch_action">
        <a id="deleteAllInvoice" href="javascript:void(0);" class="ch-button-delete" title="<?php echo __('Delete All Invoice', true);?>" style="margin: 10px 53px 10px 14px;"><span><?php echo __('Delete Invoice', true);?></a>
        <a id="createdInvoice" href="javascript:void(0);" class="ch-button-add-invoice" title="<?php echo __('Create Invoice', true);?>" style="margin: 10px 0;"><span><?php echo __('Create Invoice', true);?></span></a>
    </div>
    <hr style="clear: both;"/>
    <div id="pch_invoice">
        <p id="pch_message"></p>
        <div class="pch_input">
            <label><?php echo __('Reference', true);?></label>
        </div>
        <div class="pch_input">
            <label><?php echo __('Product', true);?></label>
        </div>
        <div class="pch_input">
            <label><?php echo __('Due Date', true);?></label>
        </div>
        <div class="pch_input">
            <label><?php echo __('Amount Due', true);?></label>
        </div>
        <div id="pch_invoice_content"></div>
    </div>
    <div id="pch_invoice_total" style="<?php echo ($language === 'fre') ? 'width: 580px;' : '';?>">
        <div class="pch_input">
            <label style="<?php echo ($language === 'fre') ? 'margin-left: 80px !important;' : '';?>"><?php echo __('Amount Due/Total Billed', true);?></label>
            <p style="font-size: 13px;font-weight: bold;color: #000;float: left;margin-right: 8px;padding-top: 5px;"><?php echo __('Check', true);?></p>
            <input type="text" id="textTotalAmountAndBilled" readonly="readonly" class="input_disabled" />
            <span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;float: left;margin-top: 3px;"><?php echo $saleCurrency;?></span>
            <a href="javascript:void(0);" id="totalBilledCheckbox" ></a>
        </div>
    </div>
    <div id="pch_waiting_load">
        <p></p>
    </div>
</div>
<!-- End add_invoice_popup -->
<!-- add_expenses_popup -->
<div id="add_expenses_popup" style="display:none; position: relative;" title="<?php echo __('Expenses', true);?>" class="buttons">
    <div id="pch_product_top" style="margin-left: 165px !important;">
        <input type="hidden" id="textProductID" />
        <div class="pch_input pch_input_expenses">
            <label><?php echo __('Achievement start date', true);?></label>
            <input type="text" id="txtAchievementStartDate" onchange="validatedAchievement('false');" />
        </div>
        <div class="pch_input pch_input_expenses">
            <label><?php echo __('Achievement end date', true);?></label>
            <input type="text" id="txtAchievementEndDate" onchange="validatedAchievement('false');" />
            <p style="clear: both; display: none; padding-left: 3px;padding-top: 7px;color: red;font-size: 12px;font-style: italic;" class="pch_message_error"><?php echo __('The end date must be greater than start date', true);?></p>
        </div>
        <div class="pch_input pch_input_expenses">
            <label><?php echo __('Number of month of achievement', true);?></label>
            <input type="text" id="txtNumberOfMonthOfAchievement" readonly="readonly" class="input_disabled" value="0" />
        </div>
    </div>
    <div class="pch_input pch_input_expenses" style="margin-left: 165px; clear: both;">
        <label><?php echo __('Date go live', true);?></label>
        <input type="text" id="txtDateGoLive" style="width: 240px;" onchange="validatedAchievement('true');" />
    </div>
    <div id="pch_action">
        <a id="createdExpenses" href="javascript:void(0);" class="ch-button-add-invoice" title="<?php echo __('Add Expenses', true);?>" style="margin: -32px 150px 10px 0;"><span><?php echo __('Add Expenses', true);?></span></a>
    </div>
    <hr style="clear: both;"/>
    <div id="pch_expense">
        <p id="pch_message"></p>
        <div class="pch_input pch_input_reference">
            <label><?php echo __('Reference', true);?></label>
        </div>
        <div class="pch_input" style="width: 120px;">
            <label><?php echo __('Product', true);?></label>
        </div>
        <div class="pch_input" style="width: 150px;">
            <label><?php echo __('Name of expense', true);?></label>
        </div>
        <div class="pch_input" style="width: 150px;">
            <label><?php echo __('Type of expense', true);?></label>
        </div>
        <div class="pch_input" style="width: 150px;">
            <label><?php echo __('CAPEX/OPEX', true);?></label>
        </div>
        <div class="pch_input" style="width: 66px;">
            <label><?php echo __('Number', true);?></label>
        </div>
        <div class="pch_input" style="width: 43px;">
            <label><?php echo __('Unit', true);?></label>
        </div>
        <div class="pch_input" style="width: 130px;">
            <label><?php echo __('Unit cost', true);?></label>
        </div>
        <div class="pch_input" style="width: 150px;">
            <label><?php echo __('Amount due', true);?></label>
        </div>
        <div id="pch_expense_content"></div>
    </div>
    <div id="pch_expenses_total">
        <div class="pch_input">
            <label><?php echo __('Total CAPEX', true);?></label>
            <input type="text" id="txtTotalCapex" readonly="readonly" class="input_disabled" />
            <span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;margin-top: 3px;"><?php echo $saleCurrency;?></span>
        </div>
        <div class="pch_input">
            <label><?php echo __('Total OPEX', true);?></label>
            <input type="text" id="txtTotalOpex" readonly="readonly" class="input_disabled" />
            <span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;margin-top: 3px;"><?php echo $saleCurrency;?></span>
        </div>
    </div>
    <div id="pch_waiting_load_2">
        <p></p>
    </div>
</div>
<!-- End add_expenses_popup -->
<div id="wd-container-main" class="wd-project-detail">
    <?php echo $this->element("project_top_menu"); ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-title">
                <fieldset style="float: left;">
                    <div class="wd-submit" style="overflow: hidden;margin: 0; width: 400px;">
                        <input onclick="if(validateForm()){jQuery('#wd-fragment-1 form:first').submit();};" class="wd-save"/>
                    </div>
                </fieldset>
            </div>
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2"><?php __("Deal Details") ?></h2>
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('SaleLead', array(
                            'enctype' => 'multipart/form-data',
                            'url' => array('controller' => 'sale_leads', 'action' => 'deal_update', $company_id, $id)
                        ));
                        echo $this->Form->input('id');
                        App::import("vendor", "str_utility");
                        $str_utility = new str_utility();
                        $idOfLeadDefault = !empty($id) ? $id : $lastIdOfSaleLead;
                        ?>
                        <fieldset>
                            <div class="wd-scroll-form" style="height:auto;">
                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="deal_status"><?php __("Deal Status") ?></label>
                                        <?php
                                            $dealStatus = array('Open', 'Archived', 'Revewal');
                                            echo $this->Form->input('deal_status', array('div' => false, 'label' => false,
                                                'disabled' => $disabled,
                                                "options" => $dealStatus));
                                            echo $this->Form->input('deal_status_tmp', array('type' => 'hidden'));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="code" style="color: #F00808;"><?php __("ID");?></label>
                                        <?php
                                        echo $this->Form->input('code', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'readonly' => 'readonly',
                                            'disabled' => $disabled,
                                            'value' => !empty($id) && !empty($this->data['SaleLead']['code']) ? $this->data['SaleLead']['code'] : $idOfLeadDefault . '-',
                                            'onkeyup' => 'return checkDefaultValue(event, "' . $idOfLeadDefault .'-", "SaleLeadCode");',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="name" style="color: #F00808;"><?php __("Name");?></label>
                                        <?php
                                        echo $this->Form->input('name', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'readonly' => 'readonly',
                                            'disabled' => $disabled,
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="order_number"><?php __("Order Number");?></label>
                                        <?php
                                        echo $this->Form->input('order_number', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'readonly' => 'readonly',
                                            'disabled' => $disabled,
                                            'value' => !empty($id) && !empty($this->data['SaleLead']['order_number']) ? $this->data['SaleLead']['order_number'] : $idOfLeadDefault . '-',
                                            'onkeyup' => 'return checkDefaultValue(event, "' . $idOfLeadDefault .'-", "SaleLeadOrderNumber");',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="customer_id"><?php __("Customer ID");?></label>
                                        <?php
                                        echo $this->Form->input('customer_id', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'disabled' => $disabled,
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                    <div class="wd-input" style="position: relative;">
                                        <label for="sales_price"><?php __("Sales Price");?></label>
                                        <?php
                                        echo $this->Form->input('sales_price', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'readonly' => 'readonly',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                        <span class="span_euro" style="position: absolute; font-size: 16px; font-weight: bold; top: 6px; margin-left: 4px;"><?php echo $saleCurrency;?></span>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input ">
                                        <label for="last-name"><?php __("Customer") ?></label>
                                        <span id="employee-place" data-onlySale="<?php echo $company_id;?>"></span>
                                        <?php if($roles === 'admin' || (!empty($saleRoles) && ($saleRoles == 1 || $saleRoles == 2))):?>
                                        <a href="<?php echo $this->Html->url(array('controller' => 'sale_customers', 'action' => 'update', 'customer', 'cus', $company_id));?>" class="ch-button-add" target="_blank" title="<?php echo __('Add Customer', true);?>"></a>
                                        <?php endif;?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="sale_customer_contact_id"><?php __("Contact") ?></label>
                                        <?php
                                            echo $this->Form->input('sale_customer_contact_id', array('div' => false, 'label' => false,
                                                'disabled' => $disabled,
                                                "options" => array(), 'empty' => __("--Select--", true))); ?>
                                        <?php if($roles === 'admin' || (!empty($saleRoles) && ($saleRoles == 1 || $saleRoles == 2))):?>
                                        <a href="<?php echo $this->Html->url(array('controller' => 'sale_customer_contacts', 'action' => 'index'));?>" class="ch-button-add" target="_blank" title="<?php echo __('Add Contact', true);?>"></a>
                                        <?php endif;?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="salesman" style="color: #F00808;"><?php __("Salesman") ?></label>
                                        <div class="multiselect">
                                            <a href="" class="wd-combobox"></a>
                                            <div id="wd-data-project" class="salesman">
                                                <?php
                                                    if(!empty($employees)):
                                                        foreach($employees as $employId => $employName):
                                                ?>
                                                <div class="salesman wd-data-manager wd-group-<?php echo $employId;?>">
                                                    <p class="salesman wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('salesman', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'salesman',
                                                                'name' => 'data[salesman][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="salesman" style="padding-left: 5px;"><?php echo $employName;?></span>
                                                    </p>
                                                    <p class="salesman wd-backup" style="float: right; margin: -27px 0; padding-right: 5px;">
                                                        <?php
                                                            echo $this->Form->input('is_backup', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'disabled' => 'disabled',
                                                                'class' => 'salesman',
                                                                'name' => 'data[is_backup][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="salesman" style="padding-left: 5px;">Backup</span>
                                                    </p>
                                                </div>
                                                <?php
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wd-input">
                                        <label for="manager_deal" style="color: #F00808;"><?php __("Deal Manager") ?></label>
                                        <div class="multiselect">
                                            <a href="" class="wd-combobox-2"></a>
                                            <div id="wd-data-project-2" class="manager_deal">
                                                <?php
                                                    if(!empty($employOfCompanies)):
                                                        foreach($employOfCompanies as $employId => $employName):
                                                ?>
                                                <div class="manager_deal wd-data-manager wd-group-<?php echo $employId;?>">
                                                    <p class="manager_deal wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('manager_deal', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'manager_deal',
                                                                'name' => 'data[manager_deal][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="manager_deal" style="padding-left: 5px;"><?php echo $employName;?></span>
                                                    </p>
                                                    <p class="manager_deal wd-backup" style="float: right; margin: -27px 0; padding-right: 5px;">
                                                        <?php
                                                            echo $this->Form->input('is_backup_deal_manager', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'disabled' => 'disabled',
                                                                'class' => 'manager_deal',
                                                                'name' => 'data[is_backup_deal_manager][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="manager_deal" style="padding-left: 5px;">Backup</span>
                                                    </p>
                                                </div>
                                                <?php
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="deal_start_date"><?php __("Deal Start Date") ?></label>
                                        <?php
                                        echo $this->Form->input('deal_start_date', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => $disabled,
                                            'onchange' => 'validatedDealDate();',
                                            'value' => !empty($id) && !empty($this->data['SaleLead']['deal_start_date']) ? date('d/m/Y', $this->data['SaleLead']['deal_start_date']) : '',
                                            "style" => "padding: 6px 2px; width: 62%"));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="deal_end_date"><?php __("Deal End Date") ?></label>
                                        <?php
                                        echo $this->Form->input('deal_end_date', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => $disabled,
                                            'onchange' => 'validatedDealDate();',
                                            'value' => !empty($id) && !empty($this->data['SaleLead']['deal_end_date']) ? date('d/m/Y', $this->data['SaleLead']['deal_end_date']) : '',
                                            "style" => "padding: 6px 2px; width: 62%"));
                                        ?>
                                        <p style="clear: both; display: none; padding-left: 145px;padding-top: 7px;color: red;font-size: 12px;font-style: italic;" class="pch_message_error_deal"><?php echo __('The end date must be greater than start date', true);?></p>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="deal_renewal_date"><?php __("Deal Renewal Date") ?></label>
                                        <?php
                                        echo $this->Form->input('deal_renewal_date', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => $disabled,
                                            'value' => !empty($id) && !empty($this->data['SaleLead']['deal_renewal_date']) ? date('d/m/Y', $this->data['SaleLead']['deal_renewal_date']) : '',
                                            "style" => "padding: 6px 2px; width: 62%"));
                                        ?>
                                        <p style="clear: both; display: none; padding-left: 145px;padding-top: 7px;color: red;font-size: 12px;font-style: italic;" class="pch_message_error_deal"><?php echo __('The end date must be greater than start date', true);?></p>
                                    </div>
                                </div>
                                <hr style="clear: both;" />
                                <div class="wd-input wd-area wd-none">
                                    <label><?php __("Description") ?></label>
                                    <?php
                                    echo $this->Form->input('description', array('type' => 'textarea',
                                        'div' => false, 'label' => false,
                                        'disabled' => $disabled,
                                        "style" => "padding: 6px 2px; width: 81.2%")); ?>
                                </div>
                            </div>
                        </fieldset>
                        <?php if(!empty($id)):?>
                        <hr style="clear: both;" />
                        <div class="wd-title">
                            <?php if($roles === 'admin' || (!empty($saleRoles) && ($saleRoles == 1 || $saleRoles == 2))):?>
                            <!--a href="<?php //echo $this->Html->url(array('controller' => 'sale_settings', 'action' => 'index', 'lead_product'));?>" class="ch-button-add-invoice" style="margin-right:15px; color: #fff;" target="_blank"><span><?php //echo __('Add Product')?></span></a-->
                            <?php endif;?>
                            <?php if($disabled == ''):?>
                            <!--a href="javascript:void(0);" id="" class="ch-button-add-invoice" style="margin-right:15px; color: #fff;" onclick="addProductionLine();"><span><?php //echo __('Add Line')?></span></a-->
                            <?php endif;?>
                        </div>
                        <div class="wd-table" id="project_container" style="margin-top: 8px; margin-bottom: 70px;; width:99%; height: 220px; clear: both;">

                        </div>
                        <hr style="clear: both;" />
                        <?php if($disabled == ''):?>
                        <div class="wd-title">
                            <a href="javascript:void(0);" id="add-activity" class="wd-add-project" style="margin-right:15px;" onclick="addLogSaleLead();"><span></span></a>
                        </div>
                        <?php endif;?>
                        <div id="pch_log">
                            <!-- <div class="pch_log_system pch_log_system_header">
                                <div class="pch_log_name">
                                    <label><?php echo __('Employee/Time')?></label>
                                </div>
                                <div class="pch_log_description">
                                    <label><?php echo __('Description')?></label>
                                </div>
                                <div class="pch_log_avatar">
                                    <label><?php echo __('Avatar')?></label>
                                </div>
                            </div> -->
                            <div id="pch_log_system_content">
                                <?php
                                    if(!empty($saleLeadLogs)){
                                        $LogHtml = '';
                                        $_relsLog = 1;
                                        if(!empty($disabled)){
                                            $_disable = 'disabled="' . $disabled . '"';
                                        } else {
                                            $_disable = '';
                                        }
                                        foreach($saleLeadLogs as $idLog => $saleLeadLog){
                                            $_onchange = 'onchange=\'updateLogSystem("' . $_relsLog . '", "' . $saleLeadLog['id'] . '");\'';
                                            $_onclick = 'onclick=\'updateAvatarLogSystem("' . $_relsLog .  '", "' . $saleLeadLog['id'] . '");\'';
                                            $linkAvatar = $this->UserFile->avatar($saleLeadLog['employee_id']);
                                            $LogHtml .= '<div class="pch_log_system" rels="' . $_relsLog . '">' .
                                                '<div class="pch_log_avatar pch_log_avatar_content">' .
                                                    '<img id="logAvatar_' . $_relsLog . '" src="' . $linkAvatar . '" ' . $_onclick . '/>' .
                                                '</div>' .
                                                '<div class="pch_log_body">'.
                                                    '<div class="pch_log_name">' .
                                                        '<input id="logName_' . $_relsLog . '" readonly="readonly" class="input_disabled" value="' . $saleLeadLog['name'] . '" />' .
                                                    '</div>' .
                                                    '<div class="pch_log_description">' .
                                                        '<input id="logDes_' . $_relsLog . '" type="text" value="' . $saleLeadLog['description'] . '" ' . $_onchange . ' ' . $_disable . ' />' .
                                                    '</div>' .
                                                '</div>'.
                                            '</div>';
                                        }
                                        echo $LogHtml;
                                    }
                                ?>
                            </div>
                        </div>
                        <fieldset>
                            <div class="wd-scroll-form" style="height:auto;">
                                <hr style="clear: both;" />
                                <div class="wd-left-content">
                                    <div class="wd-input wd-calendar" rels="<?php echo Configure::read('Config.language'); ?>" id="languageTranslationAudit">
                                        <label for="attachments" style="width: 58px;"><?php __("Estimate") ?></label>
                                        <p style="padding-top: 5px;color: #F00808; font-size: 13px; font-style: italic;"><?php echo __('"Add files" and then "Start Upload"', true);?></p>
                                    </div>
                                    <div id="uploaderEstimate" class="wd-input wd-calendar" style="margin-top: -10px;">
                                        <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input wd-calendar" rels="<?php echo Configure::read('Config.language'); ?>" id="languageTranslationAudit">
                                        <label for="attachments" style="width: 40px;"><?php __("Order") ?></label>
                                        <p style="padding-top: 5px;color: #F00808; font-size: 13px; font-style: italic;"><?php echo __('"Add files" and then "Start Upload"', true);?></p>
                                    </div>
                                    <div id="uploaderOrder" class="wd-input wd-calendar" style="margin-top: -10px;">
                                        <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <?php endif;?>
                        </form>
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

/**
 * Contact
 */
$columns = array(
    array(
        'id' => 'sale_setting_lead_product',
        'field' => 'sale_setting_lead_product',
        'name' => __('Product', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DataValidator.isUnique',
        //'formatter' => 'Slick.Formatters.forecastValue'
    ),
    array(
        'id' => 'number',
        'field' => 'number',
        'name' => __('Number', true),
        'width' => 60,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.numberRight'
        //'validator' => 'DataValidator.isUniqueLastName',
    ),
    array(
        'id' => 'price',
        'field' => 'price',
        'name' => __('Price', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.decimalNumber'
        //'validator' => 'DataValidator.isUniqueEmail',
    ),
    array(
        'id' => 'number_of_year',
        'field' => 'number_of_year',
        'name' => __('Number Of Year', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.numberRight'
        //'validator' => 'DataValidator.isUniqueLastName',
    ),
    array(
        'id' => 'total',
        'field' => 'total',
        'name' => __('Total', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.decimalNumber'
    ),
    array(
        'id' => 'discount_rate',
        'field' => 'discount_rate',
        'name' => __('Discount Rate', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.percentNumber'
    ),
    array(
        'id' => 'amount_due',
        'field' => 'amount_due',
        'name' => __('Amount Due', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.decimalNumber'
    ),
    array(
        'id' => 'reference',
        'field' => 'reference',
        'name' => __('Reference', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.calculateNumber'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'sale_setting_lead_product' => !empty($saleSettings[7]) ? $saleSettings[7] : array()
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$totalSalesLeadProducts = array(
    'totalPrice' => 0,
    'totalTotal' => 0,
    'totalAmount' => 0
);
if(!empty($saleLeadProducts)){
    $totalPrice = $totalTotal = $totalAmount = 0;
    foreach($saleLeadProducts as $saleLeadProduct){
        $data = array(
            'id' => $saleLeadProduct['id'],
            'no.' => $i++,
            'key' => $i++,
            'MetaData' => array()
        );
        $data['company_id'] = (string) $saleLeadProduct['company_id'];
        $data['sale_lead_id'] = (string) $saleLeadProduct['sale_lead_id'];
        $data['sale_setting_lead_product'] = (string) $saleLeadProduct['sale_setting_lead_product'];
        $data['number'] = (string) $saleLeadProduct['number'];
        $data['price'] = (string) $saleLeadProduct['price'];
        $data['number_of_year'] = (string) $saleLeadProduct['number_of_year'];
        $data['total'] = (string) $saleLeadProduct['total'];
        $data['discount_rate'] = (string) $saleLeadProduct['discount_rate'];
        $data['amount_due'] = (string) $saleLeadProduct['amount_due'];
        $data['reference'] = (string) $saleLeadProduct['reference'];

        $totalPrice += $saleLeadProduct['price'];
        $totalTotal += $saleLeadProduct['total'];
        $totalAmount += $saleLeadProduct['amount_due'];

        $data['action.'] = '';
        $dataView[] = $data;
    }
    $totalSalesLeadProducts = array(
        'totalPrice' => $totalPrice,
        'totalTotal' => $totalTotal,
        'totalAmount' => $totalAmount
    );
}
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
$customerId = !empty($this->data['SaleLead']['sale_customer_id']) ? $this->data['SaleLead']['sale_customer_id'] : 0;
?>
<script>
    var DataValidator = {};
    var idOfLead = <?php echo json_encode($idOfLeadDefault);?>;
    var modifySaleDeal = <?php echo json_encode($modifySaleDeal);?>;
    (function($){
        $(function(){
            /**
             * Khai bao bien
             */
            var $this = SlickGridCustom,
            saleLeadFileOfEstimates = <?php echo !empty($saleLeadFiles['estimate']) ? json_encode($saleLeadFiles['estimate']) : json_encode(array());?>,
            saleLeadFileOfOrders = <?php echo !empty($saleLeadFiles['order']) ? json_encode($saleLeadFiles['order']) : json_encode(array());?>,
            company_id = <?php echo json_encode($company_id);?>,
            idOfLeadGetUrl = <?php echo !empty($id) ? $id : 0;?>,
            valContact = <?php echo !empty($this->data['SaleLead']['sale_customer_contact_id']) ? $this->data['SaleLead']['sale_customer_contact_id'] : 0;?>,
            salesman = $('#wd-data-project').find('.wd-data-manager'),
            dealManager = $('#wd-data-project-2').find('.wd-data-manager'),
            saleExpenses = <?php echo json_encode($saleExpenses);?>,
            language = <?php echo json_encode($language);?>,
            companyName = <?php echo json_encode($companyName);?>,
            employeeLoginId = <?php echo json_encode($employeeLoginId);?>,
            totalSalesLeadProducts = <?php echo json_encode($totalSalesLeadProducts);?>,
            saleCurrency = <?php echo json_encode($saleCurrency);?>,
            dealManagers = <?php echo json_encode($dealManagers);?>,
            salesManDatas = <?php echo json_encode($salesMans);?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified = true;
            var $customer = null, customerId = <?php echo json_encode($customerId);?>;
            function loadEmployee(id){
                if($customer){
                    $customer.combobox('destroy');
                    $customer.prop('disabled',true).show();
                }
                $customer = null;
                if(!id){
                    return;
                }
                $.ajax({
                    cache : true,
                    url : <?php echo json_encode($this->Html->url(array('action' => 'get_customer'))); ?> + '/' + company_id,
                    success : function(data){
                        var $place = $('#employee-place');
                        //var $text = $place.find('select :selected').clone();
                        $place.html(data);
                        $customer = $place.find('select').val(customerId).combobox();
                    }
                });
            }
            loadEmployee(company_id);
            if(idOfLeadGetUrl != 0){
                var actionTemplate =  $('#action-template').html();
                $.extend(Slick.Formatters,{
                    Action : function(row, cell, value, columnDef, dataContext){
                        var _name = $this.selectMaps.sale_setting_lead_product[dataContext.sale_setting_lead_product] ? $this.selectMaps.sale_setting_lead_product[dataContext.sale_setting_lead_product] : '';
                        return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, dataContext.company_id, dataContext.sale_lead_id,
                        dataContext.id, _name, dataContext.amount_due
                        ), columnDef, dataContext);
                    },
                    decimalNumber : function(row, cell, value, columnDef, dataContext){
                        if(columnDef.id === 'price'){
                            return '<span class="row-number">' + number_format(value, 2, '.', ' ') + ' ' + saleCurrency + '</span>';
                        }
                        return '<span class="row-disabled row-number">' + number_format(value, 2, '.', ' ') + ' ' + saleCurrency + '</span>';
                    },
                    numberRight : function(row, cell, value, columnDef, dataContext){
                        return '<span class="row-number">' + value + '</span>';
                    },
                    percentNumber : function(row, cell, value, columnDef, dataContext){
                        return '<span class="row-number">' + value + ' %</span>';
                    },
                    calculateNumber : function(row, cell, value, columnDef, dataContext){
                        return '<span class="row-disabled">' + value + '</span>';
                    },
                });;
                DataValidator.isUnique = function(value,args){
                    var result = true;
                    $.each(args.grid.getData().getItems() , function(undefined,dx){
                        if(dx.sale_setting_lead_product.toLowerCase() == value.toLowerCase()){
                            result = false;
                        }
                    });
                    return {
                        valid : result,
                        message : $this.t('The Product has already been exist.')
                    };
                }
                var  data = <?php echo json_encode($dataView); ?>;
                var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
                $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
                $this.onCellChange = function(args){
                    $('.row-disabled').parent().addClass('row-disabled-custom');
                    $('.row-number').parent().addClass('row-number-custom');
                    args.item.number = (args.item.number == '') ? 1 : args.item.number;
                    args.item.price = (args.item.price == '') ? 0 : args.item.price;
                    args.item.number_of_year = (args.item.number_of_year == '') ? 1 : args.item.number_of_year;
                    args.item.discount_rate = (args.item.discount_rate == '') ? 0 : args.item.discount_rate;
                    args.item.total = args.item.number * args.item.price * args.item.number_of_year;
                    args.item.amount_due = number_format(args.item.total - ((args.item.total * args.item.discount_rate)/100), 2, '.', '');
                    args.item.reference = idOfLead + '-' + (args.row+1);
                    var totalPrice = totalTotal = totalAmount = 0;
                    $.each(data, function(index, value){
                        totalPrice += parseFloat(value.price);
                        totalTotal += parseFloat(value.total);
                        totalAmount += parseFloat(value.amount_due);
                    });
                    var _valUpdateSalePrice = number_format(totalAmount, 2, '.', ' ');
                    $('#SaleLeadSalesPrice').val(_valUpdateSalePrice);
                    totalPrice = number_format(totalPrice, 2, '.', ' ') + ' ' + saleCurrency;
                    totalTotal = number_format(totalTotal, 2, '.', ' ') + ' ' + saleCurrency;
                    totalAmount = number_format(totalAmount, 2, '.', ' ') + ' ' + saleCurrency;
                    $('#total_price p').html(totalPrice);
                    $('#total_total p').html(totalTotal);
                    $('#total_amount p').html(totalAmount);
                    return true;
                }
                $this.fields = {
                    id : {defaulValue : 0},
                    company_id : {defaulValue : '<?php echo $company_id; ?>'},
                    sale_lead_id: {defaulValue : '<?php echo $id ? $id : 0; ?>'},
                    sale_setting_lead_product: {defaulValue : '', allowEmpty : false},
                    number: {defaulValue : ''},
                    price: {defaulValue : ''},
                    number_of_year: {defaulValue : ''},
                    total: {defaulValue : ''},
                    discount_rate: {defaulValue : ''},
                    amount_due: {defaulValue : ''},
                    reference: {defaulValue : ''}
                };
                $this.url =  '<?php echo $html->url(array('action' => 'update_production')); ?>';
                if(modifySaleDeal == 'false'){
                    ControlGrid = $this.init($('#project_container'), data, columns, {
                        enableAddRow : false,
                        editable: false
                    });
                } else {
                    ControlGrid = $this.init($('#project_container'), data, columns, {
                        enableAddRow : false
                    });
                }
                var _ids = 999999999999;
                addProductionLine = function(){
                    var newRow = {
                        id: _ids++,
                        company_id: <?php echo $company_id; ?>,
                        sale_lead_id: <?php echo $id ? $id : 0; ?>,
                        sale_setting_lead_product : '',
                        number : '',
                        price : '',
                        number_of_year : '',
                        total: '',
                        discount_rate : '',
                        amount_due : '',
                        reference : '',
                    };
                    var rowData = ControlGrid.getData().getItems();
                    ControlGrid.invalidateRow(rowData.length);
                    rowData.splice(rowData.length, 0, newRow);
                    ControlGrid.getData().setItems(rowData);
                    ControlGrid.render();
                    ControlGrid.scrollRowIntoView(rowData.length, false);
                    ControlGrid.gotoCell(rowData.length-1, 0, true);
                };
                var cols = ControlGrid.getColumns();
                var numCols = cols.length;
                var gridW = 0;
                for (var i=0; i<numCols; i++) {
                    gridW += cols[i].width;
                }
                ControlGrid.onScroll.subscribe(function(args, e, scope){
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
                    $('.row-disabled').parent().addClass('row-disabled-custom');
                    $('.row-number').parent().addClass('row-number-custom');
    			});
                header =
                    '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">'
                        + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom"><p>Total</p></div>'
                        + '<div class="ui-state-default slick-headerrow-column l1 r1 wd-row-custom"></div>'
                        + '<div class="ui-state-default slick-headerrow-column l2 r2 wd-row-custom" id="total_price"><p>' + number_format(totalSalesLeadProducts.totalPrice, 2, '.', ' ') + ' ' + saleCurrency + '</p></div>'
                        + '<div class="ui-state-default slick-headerrow-column l3 r3 wd-row-custom"></div>'
                        + '<div class="ui-state-default slick-headerrow-column l4 r4 wd-row-custom" id="total_total"><p>' + number_format(totalSalesLeadProducts.totalTotal, 2, '.', ' ') + ' ' + saleCurrency + '</p></div>'
                        + '<div class="ui-state-default slick-headerrow-column l5 r5 wd-row-custom"></div>'
                        + '<div class="ui-state-default slick-headerrow-column l6 r6 wd-row-custom" id="total_amount"><p>' + number_format(totalSalesLeadProducts.totalAmount, 2, '.', ' ') + ' ' + saleCurrency + '</p></div>'
                        + '<div class="ui-state-default slick-headerrow-column l7 r7 wd-row-custom"></div>'
                        + '<div class="ui-state-default slick-headerrow-column l8 r8 wd-row-custom"></div>'
                  + '</div>';
                $('.slick-header-columns').after(header);
                $('.row-disabled').parent().addClass('row-disabled-custom');
                $('.row-number').parent().addClass('row-number-custom');
            }
            $('#SaleLeadSalesPrice').val(number_format(totalSalesLeadProducts.totalAmount, 2, '.', ' '));
            /**
             * Save Sales Price Of Lead
             */
            var valOfSalesPrice = $('#SaleLeadSalesPrice').val();
            if(valOfSalesPrice && idOfLeadGetUrl){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_sale_lead_price')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        id: idOfLeadGetUrl,
                        sales_price: valOfSalesPrice
                    },
                    success:function(data) {
                    }
                });
            }
            /**
             * Function Filter
             */
            var initMenuFilter = function($menu, $check){
                if($check === true){
                    var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else {
                    var $filter = $('<div class="context-menu-filter-2"><span><input type="text" rel="no-history"></span></div>');
                }
                $menu.before($filter);

                var timeoutID = null, searchHandler = function(){
                    var val = $(this).val();
                    var te = $($menu).find('.wd-data-manager .wd-data span').html();

                    $($menu).find('.wd-data-manager .wd-data span').each(function(){
                        var $label = $(this).html();
                        $label = $label.toLowerCase();
                        val = val.toLowerCase();
                        if(!val.length || $label.indexOf(val) != -1 || !val){
                            $(this).parent().css('display', 'block');
                            $(this).parent().next().css('display', 'block');
                        } else{
                            $(this).parent().css('display', 'none');
                            $(this).parent().next().css('display', 'none');
                        }
                    });
                };

                $filter.find('input').click(function(e){
                    e.stopImmediatePropagation();
                }).keyup(function(){
                    var self = this;
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(function(){
                        searchHandler.call(self);
                    } , 200);
                });

            };
            initMenuFilter($('#wd-data-project'), true);
            initMenuFilter($('#wd-data-project-2'), false);
            $('.context-menu-filter, .context-menu-filter-2').css('display', 'none');
            $('.context-menu-filter').css('display', 'none');
            $('.wd-combobox').click(function(){
                if(modifySaleDeal == 'false'){
                    return false;
                }
                var checked = $(this).attr('checked');
                $('#wd-data-project-2').css('display', 'none');
                $('.context-menu-filter-2').css('display', 'none');
                $('.wd-combobox-2').removeAttr('checked');
                if(checked){
                    $('#wd-data-project').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter').css('display', 'none');
                } else {
                    $('#wd-data-project').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '27.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project div:first-child').css('padding-top', '20px');
                }
                return false;
            });
            $('.wd-combobox-2').click(function(){
                if(modifySaleDeal == 'false'){
                    return false;
                }
                var checked = $(this).attr('checked');
                $('#wd-data-project').css('display', 'none');
                $('.context-menu-filter').css('display', 'none');
                $('.wd-combobox').removeAttr('checked');
                if(checked){
                    $('#wd-data-project-2').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-2').css('display', 'none');
                } else {
                    $('#wd-data-project-2').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-2').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '27.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-2 div:first-child').css('padding-top', '20px');
                }
                return false;
            });
            $('html').click(function(e){
                if($(e.target).attr('class') && (
                ($(e.target).attr('class').split(' ')[0] && ($(e.target).attr('class').split(' ')[0] == 'salesman' || $(e.target).attr('class').split(' ')[0] == 'manager_deal')) ||
                $(e.target).attr('class') == 'context-menu-filter' ||
                $(e.target).attr('class') == 'context-menu-filter-2'
                )){
                    //do nothing
                } else {
                    $('.context-menu-filter, .context-menu-filter-2').css('display', 'none');
                    $('#wd-data-project, #wd-data-project-2').css('display', 'none');
                    $('.wd-combobox, .wd-combobox-2').removeAttr('checked');
                }
            });
            /**
             * Remove element to array
             */
            jQuery.removeFromArray = function(value, arr) {
                return jQuery.grep(arr, function(elem, index) {
                    return elem !== value;
                });
            };
            /**
             * Phan chon cac phan tu trong combobox cua salesman
             */
            var $ids = [];
            salesman.each(function(){
                var data = $(this).find('.wd-data');
                var backup = $(this).find('.wd-backup');
                /**
                 * When load data
                 */
                var valList = $(data).find('#SaleLeadSalesman').val();
                var valListBackup = $(backup).find('#SaleLeadIsBackup').val();
                if(salesManDatas){
                    $.each(salesManDatas, function(employId, isBackup){
                        isBackup = (isBackup == 1) ? employId : 0;
                        if(valList == employId){
                            $(data).find('#SaleLeadSalesman').attr('checked', 'checked');
                            $(backup).find('#SaleLeadIsBackup').removeAttr('disabled');
                            $('a.wd-combobox').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                        }
                        if(valListBackup == isBackup){
                            $(backup).find('#SaleLeadIsBackup').attr('checked', 'checked');
                            $('a.wd-combobox .wd-bk-'+valListBackup).append('(B)');
                        }
                        $ids.push(employId);
                    });
                }
                /**
                 * When click in checkbox
                 */
                $(data).find('#SaleLeadSalesman').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $(backup).find('#SaleLeadIsBackup').removeAttr('disabled');
                        $ids.push(_datas);
                        $('a.wd-combobox').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids = jQuery.removeFromArray(_datas, $ids);
                        $(backup).find('#SaleLeadIsBackup').attr('disabled', 'disabled');
                        $('a.wd-combobox').find('.wd-dt-' +_datas).remove();
                        $('a.wd-combobox').find('.wd-em-' +_datas).remove();
                        $(backup).find('#SaleLeadIsBackup').removeAttr('checked');
                    }
                    if($ids.length > 1){
                        for(var i = 0; i < $ids.length; i++){
                            var _bkup = $(backup).find('#SaleLeadIsBackup').val();
                            if($ids[i] != $ids[0] && $ids[i] == _bkup){
                                $(backup).find('#SaleLeadIsBackup').attr('checked', 'checked');
                                $('a.wd-combobox .wd-bk-'+_bkup).append('(B)');
                            }
                        }
                    }
                });
                /**
                 * When click in checkbox BACKUP
                 */
                $(backup).find('#SaleLeadIsBackup').click(function(){
                    var _bkup = $(backup).find('#SaleLeadIsBackup').val();
                    if($(this).is(':checked')) {
                        $('a.wd-combobox .wd-bk-'+_bkup).append('(B)');
                    } else {
                        $('a.wd-combobox').find('.wd-bk-' +_bkup).remove();
                        $('a.wd-combobox .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
                    }
                });
            });
            /**
             * Phan chon cac phan tu trong combobox cua deal manager
             */
            var $ids = [];
            dealManager.each(function(){
                var data = $(this).find('.wd-data');
                var backup = $(this).find('.wd-backup');
                /**
                 * When load data
                 */
                var valList = $(data).find('#SaleLeadManagerDeal').val();
                var valListBackup = $(backup).find('#SaleLeadIsBackupDealManager').val();
                if(dealManagers){
                    $.each(dealManagers, function(employId, isBackup){
                        isBackup = (isBackup == 1) ? employId : 0;
                        if(valList == employId){
                            $(data).find('#SaleLeadManagerDeal').attr('checked', 'checked');
                            $(backup).find('#SaleLeadIsBackupDealManager').removeAttr('disabled');
                            $('a.wd-combobox-2').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                        }
                        if(valListBackup == isBackup){
                            $(backup).find('#SaleLeadIsBackupDealManager').attr('checked', 'checked');
                            $('a.wd-combobox-2 .wd-bk-'+valListBackup).append('(B)');
                        }
                        $ids.push(employId);
                    });
                }
                /**
                 * When click in checkbox
                 */
                $(data).find('#SaleLeadManagerDeal').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $(backup).find('#SaleLeadIsBackupDealManager').removeAttr('disabled');
                        $ids.push(_datas);
                        $('a.wd-combobox-2').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids = jQuery.removeFromArray(_datas, $ids);
                        $(backup).find('#SaleLeadIsBackupDealManager').attr('disabled', 'disabled');
                        $('a.wd-combobox-2').find('.wd-dt-' +_datas).remove();
                        $('a.wd-combobox-2').find('.wd-em-' +_datas).remove();
                        $(backup).find('#SaleLeadIsBackupDealManager').removeAttr('checked');
                    }
                    if($ids.length > 1){
                        for(var i = 0; i < $ids.length; i++){
                            var _bkup = $(backup).find('#SaleLeadIsBackupDealManager').val();
                            if($ids[i] != $ids[0] && $ids[i] == _bkup){
                                $(backup).find('#SaleLeadIsBackupDealManager').attr('checked', 'checked');
                                $('a.wd-combobox-2 .wd-bk-'+_bkup).append('(B)');
                            }
                        }
                    }
                });
                /**
                 * When click in checkbox BACKUP
                 */
                $(backup).find('#SaleLeadIsBackupDealManager').click(function(){
                    var _bkup = $(backup).find('#SaleLeadIsBackupDealManager').val();
                    if($(this).is(':checked')) {
                        $('a.wd-combobox-2 .wd-bk-'+_bkup).append('(B)');
                    } else {
                        $('a.wd-combobox-2').find('.wd-bk-' +_bkup).remove();
                        $('a.wd-combobox-2 .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
                    }
                });
            });
            /**
             * Khi thay doi Customer, Lay cac contact thuoc customer
             * Set data contact if co customer
             */
            if(customerId){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_contact')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        sale_customer_id: customerId,
                        company_id: company_id
                    },
                    success:function(data) {
                        if(data){
                            $('#SaleLeadSaleCustomerContactId').html(data);
                            $('#SaleLeadSaleCustomerContactId').val(valContact);
                        }
                    }
                });
            }
            /**
             * Chang ID And Settup Date
             */
            $('#SaleLeadStatus').change(function(){
                if($(this).val() == 1){
                    $('#SaleLeadOrderNumber').val(idOfLead + '-O');
                    $('#SaleLeadOrderNumber').attr('readonly', 'readonly');
                } else {
                    $('#SaleLeadOrderNumber').val(idOfLead + '-');
                    $('#SaleLeadOrderNumber').removeAttr('readonly');
                }
            });
            /**
             * Set time paris
             */
            setAndGetTimeOfParis = function(){
                //var _date = new Date().toLocaleString('en-US', {timeZone: 'Europe/Paris'}); // khong dung dc tren IE
                var _date = new Date(); // Lay Ngay Gio Thang Nam Hien Tai
                /**
                 * Lay Ngay Gio Chuan Cua Quoc Te
                 */
                var _day = _date.getUTCDate();
                var _month = _date.getUTCMonth() + 1;
                var _year = _date.getUTCFullYear();
                var _hours = _date.getUTCHours();
                var _minutes = _date.getUTCMinutes();
                var _seconds = _date.getUTCSeconds();
                var _miniSeconds = _date.getUTCMilliseconds();
                /**
                 * Tinh gio cua nuoc Phap
                 * Nuoc Phap nhanh hon 2 gio so voi gio Quoc te.
                 */
                _hours = _hours + 2;
                if(_hours > 24){
                    _day = _day + 1;
                    if(_day > daysInMonth(_month, _year)){
                        _month = _month + 1;
                        if(_month > 12){
                            _year = _year + 1;
                        }
                    }
                }
                _day = _day < 10 ? '0'+_day : _day;
                _month = _month < 10 ? '0'+_month : _month;
                return _hours + ':' + _minutes + ' ' + _day + '/' + _month + '/' + _year;
            };
            /**
             * Change SaleLeadDealRenewalDate
             */
            checkRenewalDate = function(){
                var _val = $('#SaleLeadDealRenewalDate').val().split('/');
                _val = new Date(_val[2] + '-' + _val[1] + '-' + _val[0]).getTime();
                var _date = setAndGetTimeOfParis();
                _date = _date.split(' ')[1].split('/');
                _date = new Date(_date[2] + '-' + _date[1] + '-' + _date[0]).getTime();
                if(_val > _date){
                    $('#SaleLeadDealRenewalDate').removeClass('renewal');
                    $('#SaleLeadDealStatus').removeAttr('disabled');
                    $('#SaleLeadDealStatus').removeClass('input_disabled');

                } else {
                    $('#SaleLeadDealRenewalDate').addClass('renewal');
                    $('#SaleLeadDealStatus').val(2);
                    $('#SaleLeadDealStatus').attr('disabled', 'disabled');
                    $('#SaleLeadDealStatus').addClass('input_disabled');
                }
                $('#SaleLeadDealStatusTmp').val($('#SaleLeadDealStatus').val());
            }
            checkRenewalDate();
            $('#SaleLeadDealRenewalDate').change(function(){
                checkRenewalDate();
            });
            $('#SaleLeadDealStatus').change(function(){
                $('#SaleLeadDealStatusTmp').val($(this).val());
            });
            /**
             * Change Name
             */
            $('#SaleLeadName').change(function(){
                $('#SaleLeadCode').val(idOfLead + '-' + $(this).val());
            });
            /**
             * Format Number Of Goal And Sales Price
             */
            var valGold = $('#SaleLeadGoal').val(),
            valSalePrice = $('#SaleLeadSalesPrice').val();
            $('#SaleLeadGoal').val(number_format(valGold, 2, '.', ' '));
            $('#SaleLeadSalesPrice').val(number_format(valSalePrice, 2, '.', ' '));
            $('#SaleLeadGoal').click(function(){
                var _val = $(this).val();
                $(this).val(number_format(_val, 2, '.', ''));
            });
            $('#SaleLeadGoal').blur(function(){
                var _val = $(this).val();
                $(this).val(number_format(_val, 2, '.', ' '));
            });
            /**
             * DatePicker
             */
            $("#SaleLeadDealStartDate, #SaleLeadDealEndDate, #textStartOfBilling, #txtAchievementStartDate, #txtAchievementEndDate, #txtDateGoLive, #SaleLeadDealRenewalDate").datepicker({
                //showOn          : 'button',
                buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
                buttonImageOnly : true,
                dateFormat      : 'dd/mm/yy'
            });
            /**
             * Dialog
             */
            $('#pch_delete_invoice_popup, #pch_delete_expenses_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 200,
                height      : 100
            });
            $('#add_invoice_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 980,
                height      : 650
            });
            $('#add_expenses_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 1100,
                height      : 650
            });
            $('#avatar_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 460,
                height      : 150
            });
            /**
             * Lay du lieu cho product va invoice
             */
            getDataInvoiceAndProduct = function(sale_lead_id, sale_lead_product_id){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'get_data_production_popup')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        id: sale_lead_product_id,
                        sale_lead_id: sale_lead_id,
                        sale_lead_product_id: sale_lead_product_id,
                        company_id: company_id
                    },
                    beforeSend:function(){

                    },
                    success:function(data) {
                        var _product = data.SaleLeadProduct ? data.SaleLeadProduct : '';
                        var _amountDue = 0;
                        if(_product.length != 0){
                            var _name = $this.selectMaps.sale_setting_lead_product[_product.sale_setting_lead_product] ? $this.selectMaps.sale_setting_lead_product[_product.sale_setting_lead_product] : '';
                            _amountDue = number_format(_product.amount_due, 2, '.', ' ');
                            $('#textID').val(sale_lead_product_id);
                            $('#textProduct').val(_name);
                            $('#textStartOfBilling').val(_product.start_of_billing);
                            $('#textAmountDue').val(number_format(_product.amount_due, 2, '.', ' '));
                            $('#selectBillingPeriod').val(_product.billing_period);
                            $('#textNumberOfPayment').val(_product.number_of_payment ?  _product.number_of_payment : 1);
                            var _amountDueCal = parseFloat(number_format(_product.amount_due, 2, '.', ''));
                            var _numberCal = _product.number_of_payment ? parseFloat(_product.number_of_payment) : 1;
                            var _amountDueInvoice = (_amountDueCal/_numberCal).toFixed(2);
                            $('#textAmountDueInvoice').val(number_format(_amountDueInvoice, 2, '.', ' '));
                            $('#createdInvoice').attr('onclick', 'createdInvoice("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + _product.sale_setting_lead_product + '", "' + _product.reference + '", "' + _name + '", "' + _product.start_of_billing + '", "' + _product.amount_due + '", "' + _product.number_of_payment + '", "' + _product.amount_due_invoice + '", "' + _product.billing_period + '");');
                            $('#deleteAllInvoice').attr('onclick', "deleteInvoice('" + sale_lead_id + "', '" + sale_lead_product_id + "')");
                        }
                        var _invoices = data.Invoice ? data.Invoice : '';
                        if(_invoices.length != 0){
                            var invoiceHtml = '';
                            var j = 1;
                            var _totalBilled = 0;
                            $.each(_invoices, function(idOfVoi, val){
                                var _name = $this.selectMaps.sale_setting_lead_product[val.sale_setting_lead_product] ? $this.selectMaps.sale_setting_lead_product[val.sale_setting_lead_product] : '';
                                var _dueDate = new Date(val.due_date * 1000);
                                var _day = _dueDate.getDate();
                                var _month = parseFloat(_dueDate.getMonth()) + 1;
                                var _year = _dueDate.getFullYear();
                                _day = _day < 10 ? '0'+_day : _day;
                                _month = _month < 10 ? '0'+_month : _month;
                                _dueDate = _day + '/' + _month + '/' + _year;
                                _totalBilled += parseFloat(number_format(val.amount_due, 2, '.', ''));
                                var _onchange = 'onchange=\'updateInvoices("' + val.reference + '", ' + idOfVoi + ');\'';
                                var _onclick = 'onclick=\'clickInvoices("' + val.reference + '", ' + idOfVoi + ');\'';
                                var _onblur = 'onblur=\'blurInvoices("' + val.reference + '", ' + idOfVoi + ');\'';
                                var disabled = '';
                                if(modifySaleDeal == 'false'){
                                    disabled = 'disabled="disabled"';
                                }
                                invoiceHtml += '<div class="pch_invoice_content_gourp_' + (j++) + '">' +
                                    '<div class="pch_input">' +
                                        '<input type="text" value="' + val.reference + '" readonly="readonly" class="input_disabled"/>' +
                                    '</div>' +
                                    '<div class="pch_input">' +
                                        '<input type="text" value="' + _name + '" readonly="readonly" class="input_disabled"/>' +
                                    '</div>' +
                                    '<div class="pch_input">' +
                                        '<input type="text" class="invoiDueDatePicker" id="invoiDueDate_' + val.reference + '" value="' + _dueDate + '" ' + _onchange + ' ' + disabled + '/>' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_last">' +
                                        '<input type="text" rels="' + idOfVoi + '" class="invoiAmountDue" id="invoiAmountDue_' + val.reference + '" value="' + val.amount_due + '" ' + _onchange + ' ' + _onclick + ' ' + _onblur + ' ' + disabled + '/>' +
                                        '<span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;"> ' + saleCurrency + '</span>' +
                                    '</div>' +
                                '</div>';
                            });
                            if(_totalBilled == number_format(_amountDue, 2, '.', '')){
                                $('#totalBilledCheckbox').addClass('wd-update wd-update-default');
                            } else {
                                $('#totalBilledCheckbox').removeClass('wd-update wd-update-default');
                            }
                            _totalBilled = _totalBilled ? number_format(_totalBilled, 2, '.', ' ') : 0;
                            $('#pch_invoice_content').html('');
                            $('#pch_invoice_content').append(invoiceHtml);
                            $('#textTotalAmountAndBilled').val(_totalBilled + '/' + _amountDue);
                            $('#textNumberOfPayment, #textStartOfBilling').attr('readonly', 'readonly');
                            $('#selectBillingPeriod, #textStartOfBilling').attr('disabled', 'disabled');
                            $('#selectBillingPeriod, #textNumberOfPayment, #textStartOfBilling').addClass('input_disabled');
                            $(".invoiDueDatePicker").datepicker({
                                //showOn          : 'button',
                                buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
                                buttonImageOnly : true,
                                dateFormat      : 'dd/mm/yy'
                            });
                        } else {
                            $('#pch_invoice_content').html('');
                            $('#textTotalAmountAndBilled').val(0 + '/' + _amountDue);
                            $('#textNumberOfPayment, #textStartOfBilling').removeAttr('readonly');
                            $('#selectBillingPeriod, #textStartOfBilling').removeAttr('disabled');
                            $('#selectBillingPeriod, #textNumberOfPayment, #textStartOfBilling').removeClass('input_disabled');
                        }
                        if(modifySaleDeal == 'false'){
                            $('#selectBillingPeriod, #textStartOfBilling, #textNumberOfPayment').attr('disabled', 'disabled');
                        }
                        setTimeout(function(){
                            $('#pch_waiting_load').fadeOut();
                        }, 200);
                    }
                });
            }
            /**
             * Popup Add Invoice
             */
            addInvoice = function(sale_lead_id, sale_lead_product_id, amount_due){
                var _amountDue = number_format(amount_due, 2, '.', '');
                if(parseFloat(_amountDue) > 0){
                    $('#pch_waiting_load').fadeIn();
                    $('#add_invoice_popup').dialog("open");
                    setTimeout(function(){
                        getDataInvoiceAndProduct(sale_lead_id, sale_lead_product_id);
                    }, 100);
                }

            };
            /**
             * Ham lay ngay cua 1 thang
             */
            daysInMonth = function(iMonth, iYear){
                return new Date(iYear, iMonth, 0).getDate();
            }
            /**
             * Tao moi Invoice o Popup
             */
            createdInvoice = function(sale_lead_id, sale_lead_product_id, sale_setting_lead_product, reference, nameProduct, start_of_billing, amount_due, number_of_payment, amount_due_invoice, billing_period){
                if(modifySaleDeal == 'false'){
                    return false;
                }
                var checkHaveInvoice = $('#pch_invoice_content').html();
                if(checkHaveInvoice){
                    return false;
                }
                if(start_of_billing && number_of_payment && !isNaN(number_of_payment)){
                    var invoiceHtml = '';
                    var _start = '';
                    var _amount = 0;
                    var _totalAmount = 0;
                    var _totalBilled = 0;
                    var _listInvoice = {};
                    for(var i = 1; i <= number_of_payment; i++){
                        var _mountDue = parseFloat(number_format(amount_due, 2, '.', ''));
                        if(i == number_of_payment){
                            _amount = _mountDue - _totalAmount;
                            _amount = number_format(_amount, 2, '.', ' ');
                        } else {
                            _amount = number_format((_mountDue/number_of_payment).toFixed(2), 2, '.', ' ');
                            _totalAmount += parseFloat(number_format(_amount, 2, '.', ''));
                        }
                        _totalBilled += parseFloat(number_format(_amount, 2, '.', ''));
                        _start = _start ? _start : start_of_billing;

                        invoiceHtml += '<div class="pch_invoice_content_gourp_' + i + '">' +
                            '<div class="pch_input">' +
                                '<input type="text" value="' + reference + '-FAC' + i + '" readonly="readonly" class="input_disabled"/>' +
                            '</div>' +
                            '<div class="pch_input">' +
                                '<input type="text" value="' + nameProduct + '" readonly="readonly" class="input_disabled"/>' +
                            '</div>' +
                            '<div class="pch_input">' +
                                '<input type="text" class="invoiDueDatePicker" id="invoiDueDate_' + reference + '-FAC' + i + '" value="' + _start + '"/>' +
                            '</div>' +
                            '<div class="pch_input pch_input_last">' +
                                '<input type="text" class="invoiAmountDue" id="invoiAmountDue_' + reference + '-FAC' + i + '" value="' + _amount + '"/>' +
                                '<span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px;"> ' + saleCurrency + '</span>' +
                            '</div>' +
                        '</div>';
                        /**
                         * BUILD DATA USING SAVE LIST INVOICE
                         */
                        _listInvoice[i] = {
                            company_id: company_id,
                            sale_lead_id: sale_lead_id,
                            sale_lead_product_id: sale_lead_product_id,
                            sale_setting_lead_product: sale_setting_lead_product,
                            reference: reference + '-FAC' + i,
                            due_date: _start,
                            amount_due: _amount
                        };
                        _start = _start.split('/');
                        var _date = _start[0] ? parseInt(_start[0]) : '';
                        var _month = _start[1] ? parseInt(_start[1]) : '';
                        var _year = _start[2] ? parseInt(_start[2]) : '';
                        if(billing_period == 1){ // hang nam
                            _year++;
                        } else if(billing_period == 2){ // 6 thang
                            for(var j = 1; j <= 6; j++){
                                _month++;
                                if(_month && _month > 12){
                                    _month = 1;
                                    _year++;
                                }
                            }
                        } else if(billing_period == 3){ // hang quy: 3 thang
                            for(var j = 1; j <= 3; j++){
                                _month++;
                                if(_month && _month > 12){
                                    _month = 1;
                                    _year++;
                                }
                            }
                        } else if(billing_period == 4){ // hang thang: 1 thang
                            _month += 1;
                            if(_month && _month > 12){
                                _month = 1;
                                _year++;
                            }
                        } else { //auto tang 1 ngay
                            _date += 1;
                            var _totalDayOfMonth = daysInMonth(_month, _year);
                            if(_date > _totalDayOfMonth){
                                _date = 1;
                                _month++;
                            }
                            if(_month > 12){
                                _month = 1;
                                _year++;
                            }
                        }
                        _date = _date < 10 ? '0'+_date : _date;
                        _month = _month < 10 ? '0'+_month : _month;
                        _start = _date + '/' + _month + '/' + _year;
                    }
                    if(_totalBilled == number_format(amount_due, 2, '.', '')){
                        $('#totalBilledCheckbox').addClass('wd-update wd-update-default');
                    } else {
                        $('#totalBilledCheckbox').removeClass('wd-update wd-update-default');
                    }
                    _totalBilled = _totalBilled ? number_format(_totalBilled, 2, '.', ' ') : 0;
                    $('#pch_invoice_content').append(invoiceHtml);
                    $('#textTotalAmountAndBilled').val(_totalBilled + '/' + amount_due);
                    $('#textNumberOfPayment, #textStartOfBilling').attr('readonly', 'readonly');
                    $('#selectBillingPeriod, #textStartOfBilling').attr('disabled', 'disabled');
                    $('#selectBillingPeriod, #textNumberOfPayment, #textStartOfBilling').addClass('input_disabled');
                    $(".invoiDueDatePicker").datepicker({
                        //showOn          : 'button',
                        buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
                        buttonImageOnly : true,
                        dateFormat      : 'dd/mm/yy'
                    });
                    setTimeout(function(){
                        saveInvoicePop(_listInvoice);
                    }, 100);
                }
                return false;
            };
            /**
             * Delete Invoice In Popup
             */
            deleteInvoice = function(sale_lead_id, sale_lead_product_id){
                if(modifySaleDeal == 'false'){
                    return false;
                }
                var checkInvoice = $('#pch_invoice_content').html();
                if(!checkInvoice){
                    return false;
                }
                $('#pch_delete_invoice_popup').dialog("open");
                $('.cancel').click(function(){
                    $('#pch_delete_invoice_popup').dialog("close");
                });
                $('#pch_delete_invoice_popup_submit').click(function(){
                    $('#pch_delete_invoice_popup').dialog("close");
                    $('#pch_invoice_content').html('');
                    $('#textTotalAmountAndBilled').val(0 + '/' + $('#textAmountDue').val());
                    $('#textNumberOfPayment, #textStartOfBilling').removeAttr('readonly');
                    $('#selectBillingPeriod, #textStartOfBilling').removeAttr('disabled');
                    $('#selectBillingPeriod, #textNumberOfPayment, #textStartOfBilling').removeClass('input_disabled');
                    $('#totalBilledCheckbox').removeClass('wd-update wd-update-default');
                    $('#pch_waiting_load').fadeIn();
                    setTimeout(function(){
                        $.ajax({
                            url: '<?php echo $html->url(array('action' => 'delete_data_invoices')); ?>',
                            async: false,
                            type : 'POST',
                            dataType : 'json',
                            data: {
                                sale_lead_id: sale_lead_id,
                                sale_lead_product_id: sale_lead_product_id,
                                company_id: company_id
                            },
                            beforeSend:function(){

                            },
                            success:function(data) {
                                setTimeout(function(){
                                    $('#pch_waiting_load').fadeOut();
                                }, 200);
                            }
                        });
                    }, 200);
                });
            }
            /**
             * Click And Blur Amount Due In Invoice
             */
            clickInvoices = function(key, invoice_id){
                $('#invoiAmountDue_' + key).val(number_format($('#invoiAmountDue_' + key).val(), 2, '.', ''));
            };
            blurInvoices = function(key, invoice_id){
                $('#invoiAmountDue_' + key).val(number_format($('#invoiAmountDue_' + key).val(), 2, '.', ' '));
            };
            /**
             * Update Invoice In Popup
             */
            updateInvoices = function(key, invoice_id){
                $('#invoiAmountDue_' + key).val(number_format($('#invoiAmountDue_' + key).val(), 2, '.', ' '));
                var _splitKey = key.split('FAC');
                var _numberOld = parseInt(_splitKey[1]) - 1;
                var _keyOld = _splitKey[0] + 'FAC' + _numberOld;
                var _numberNext = parseInt(_splitKey[1]) + 1;
                var _keyNext = _splitKey[0] + 'FAC' + _numberNext;
                var _dueDateOld = $('#invoiDueDate_' + _keyOld).val() ? $('#invoiDueDate_' + _keyOld).val() : 0;
                var _dueDateCurent = $('#invoiDueDate_' + key).val() ? $('#invoiDueDate_' + key).val() : 0;
                var _dueDateNext = $('#invoiDueDate_' + _keyNext).val() ? $('#invoiDueDate_' + _keyNext).val() : 0;
                /**
                 * Xu ly ngay/thang/nam cua truoc va sau ngay hien tai
                 */
                if(_dueDateOld != 0){
                    _dueDateOld = _dueDateOld.split('/');
                    _dueDateOld = new Date(_dueDateOld[2] + '-' + _dueDateOld[1] + '-' + _dueDateOld[0]).getTime();
                }
                if(_dueDateCurent != 0){
                    _dueDateCurent = _dueDateCurent.split('/');
                    _dueDateCurent = new Date(_dueDateCurent[2] + '-' + _dueDateCurent[1] + '-' + _dueDateCurent[0]).getTime();
                }
                if(_dueDateNext != 0){
                    _dueDateNext = _dueDateNext.split('/');
                    _dueDateNext = new Date(_dueDateNext[2] + '-' + _dueDateNext[1] + '-' + _dueDateNext[0]).getTime();
                }
                if(_dueDateOld > _dueDateCurent || (_dueDateNext != 0 &&_dueDateCurent > _dueDateNext)){
                    $('#pch_message').html('<?php echo __('Date Invalid!');?>');
                    $('#pch_message').css({
                        'margin-left': '400px',
                        'display': 'block'
                    });
                    $('#invoiDueDate_' + key).focus();
                    $('#invoiDueDate_' + key).css({
                        'background-color': 'rgb(255, 118, 118)',
                        'color': '#000'
                    });
                    setTimeout(function(){
                        $('#invoiDueDate_' + key).css('background-color', '#fff');
                    }, 2000);
                    setTimeout(function(){
                        $('#pch_message').css('display', 'none');
                    }, 3000);
                    return false;
                }
                /**
                 * Kiem tra Amount Due.
                 * Neu Row dang focus la row cuoi cung thi khong lam gi ca.
                 * Neu row dang focus khong phai la row cuoi cung. Thi row cuoi cung =  tong amount due tru di tong cac row tren
                 */
                var _amountDueInProduct = parseFloat(number_format($('#textAmountDue').val(), 2, '.', ''));
                if(_splitKey[1] != $('#textNumberOfPayment').val()){
                    var totalAmountDueExceptLastId = 0;
                    var lastId = 'invoiAmountDue_' + _splitKey[0] + 'FAC' + $('#textNumberOfPayment').val();
                    $('#pch_invoice_content').find('.invoiAmountDue').each(function(){
                        if($(this).attr('id') != lastId){
                            totalAmountDueExceptLastId += parseFloat(number_format($(this).val(), 2, '.', ''));
                        }
                    });
                    $('#' + lastId).val(number_format(_amountDueInProduct - totalAmountDueExceptLastId, 2, '.', ''));
                }
                var _totalAmountOfInvoice = 0;
                $('#pch_invoice_content').find('.invoiAmountDue').each(function(){
                    _totalAmountOfInvoice += parseFloat(number_format($(this).val(), 2, '.', ''));
                });
                $('#textTotalAmountAndBilled').val(number_format(_totalAmountOfInvoice, 2, '.', ' ') + '/' + number_format(_amountDueInProduct, 2, '.', ' '));
                if(_totalAmountOfInvoice == _amountDueInProduct){
                    $('#totalBilledCheckbox').addClass('wd-update wd-update-default');
                } else {
                    $('#totalBilledCheckbox').removeClass('wd-update wd-update-default');
                }
                if(_totalAmountOfInvoice > _amountDueInProduct){
                    $('#pch_message').html('<?php echo __('Amount Due Invalid!');?>');
                    $('#pch_message').css({
                        'margin-left': '600px',
                        'display': 'block'
                    });
                    $('#invoiAmountDue_' + key).focus();
                    $('#invoiAmountDue_' + key).css({
                        'background-color': 'rgb(255, 118, 118)',
                        'color': '#000'
                    });
                    setTimeout(function(){
                        $('#invoiAmountDue_' + key).css('background-color', '#fff');
                    }, 2000);
                    setTimeout(function(){
                        $('#pch_message').css('display', 'none');
                    }, 3000);
                    return false;
                }
                /**
                 * Update record Invoice in Popup
                 */
                setTimeout(function(){
                    updateInvoicePop(invoice_id, $('#invoiDueDate_' + key).val(), $('#invoiAmountDue_' + key).val(), '#invoiDueDate_' + key, '#invoiAmountDue_' + key);
                }, 100);
                if(_splitKey[1] != $('#textNumberOfPayment').val()){
                    var _inLastId = _splitKey[0] + 'FAC' + $('#textNumberOfPayment').val();
                    setTimeout(function(){
                        updateInvoicePop($('#invoiAmountDue_' + _inLastId).attr('rels'), $('#invoiDueDate_' + _inLastId).val(), $('#invoiAmountDue_' + _inLastId).val(), '#invoiDueDate_' + _inLastId, '#invoiAmountDue_' + _inLastId);
                    }, 100);
                }
            };
            /**
             * Update record Invoice in Popup
             */
            updateInvoicePop = function(id, dueDate, amountDue, idDueDate, idAmountDue){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_data_invoice', 'true')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        id: id,
                        due_date: dueDate,
                        amount_due: amountDue
                    },
                    beforeSend:function(){
                        $(idDueDate).addClass('pch_loading loading_input');
                        $(idAmountDue).addClass('pch_loading loading_input');
                        $(idDueDate).css('color', 'rgb(218, 215, 215)');
                        $(idAmountDue).css('color', 'rgb(218, 215, 215)');
                    },
                    success:function(data) {
                        setTimeout(function(){
                            $(idDueDate).removeClass('pch_loading loading_input');
                            $(idAmountDue).removeClass('pch_loading loading_input');
                            $(idDueDate).css('color', '#3BBD43');
                            $(idAmountDue).css('color', '#3BBD43');
                        }, 200);
                    }
                });
            }
            /**
             * Save Invoice In Popup
             */
            saveInvoicePop = function(invoices){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_data_invoice')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        data: invoices
                    },
                    beforeSend:function(){

                    },
                    success:function(data) {
                        var invoices = data ? data.Invoice : '';
                        if(invoices){
                            $.each(invoices, function(reference, id){
                                $('#invoiDueDate_'+reference).attr('onchange', 'updateInvoices("' + reference + '", ' + id + ');');
                                $('#invoiAmountDue_'+reference).attr('onchange', 'updateInvoices("' + reference + '", ' + id + ');');
                                $('#invoiAmountDue_'+reference).attr('onclick', 'clickInvoices("' + reference + '", ' + id + ');');
                                $('#invoiAmountDue_'+reference).attr('onblur', 'blurInvoices("' + reference + '", ' + id + ');');
                                $('#invoiAmountDue_'+reference).attr('rels', id);
                            });
                        }
                    }
                });
            }
            /**
             * Save Product In Popup
             */
            saveProductPop = function(){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_production_popup')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        id: $('#textID').val(),
                        start_of_billing: $('#textStartOfBilling').val(),
                        amount_due: $('#textAmountDue').val(),
                        billing_period: $('#selectBillingPeriod').val(),
                        number_of_payment: $('#textNumberOfPayment').val(),
                        amount_due_invoice: $('#textAmountDueInvoice').val()
                    },
                    beforeSend:function(){
                        $('#textStartOfBilling').addClass('pch_loading');
                        $('#selectBillingPeriod').addClass('pch_loading');
                        $('#textNumberOfPayment').addClass('pch_loading');
                        $('#textStartOfBilling, #selectBillingPeriod, #textNumberOfPayment').css('color', 'rgb(218, 215, 215)');
                    },
                    success:function(data) {
                        setTimeout(function(){
                            $('#textStartOfBilling').removeClass('pch_loading');
                            $('#selectBillingPeriod').removeClass('pch_loading');
                            $('#textNumberOfPayment').removeClass('pch_loading');
                            $('#textStartOfBilling, #selectBillingPeriod, #textNumberOfPayment').css('color', '#3BBD43');
                        }, 200);
                        var _product = data.SaleLeadProduct ? data.SaleLeadProduct : '';
                        if(_product){
                            var _name = $this.selectMaps.sale_setting_lead_product[_product.sale_setting_lead_product] ? $this.selectMaps.sale_setting_lead_product[_product.sale_setting_lead_product] : '';
                            $('#createdInvoice').attr('onclick', 'createdInvoice("' + _product.sale_lead_id + '", "' + _product.id + '", "' + _product.sale_setting_lead_product + '", "' + _product.reference + '", "' + _name + '", "' + _product.start_of_billing + '", "' + _product.amount_due + '", "' + _product.number_of_payment + '", "' + _product.amount_due_invoice + '", "' + _product.billing_period + '");');
                            $('#deleteAllInvoice').attr('onclick', "deleteInvoice('" + _product.sale_lead_id + "', '" + _product.id + "')");
                        }

                    }
                });
            };
            /**
             * Change start billing, billing period, number of payment in popup of invoices
             */
            $('#textStartOfBilling').change(function(){
                setTimeout(function(){
                    saveProductPop();
                }, 100);
                return false;
            });
            $('#selectBillingPeriod').change(function(){
                setTimeout(function(){
                    saveProductPop();
                }, 100);
                return false;
            });
            $('#textNumberOfPayment').change(function(){
                var _amountDue = number_format($('#textAmountDue').val(), 2, '.', '');
                var _number = $(this).val() ? $(this).val() : 0;
                var _amountDueInvoice = 0;
                if(_number != 0){
                    _amountDueInvoice = _amountDue/_number;
                }
                $('#textAmountDueInvoice').val(number_format(_amountDueInvoice, 2, '.', ' '));
                setTimeout(function(){
                    saveProductPop();
                }, 100);
                return false;
            });
            /**
             * Popup Add Expenses
             */
            addExpense = function(sale_lead_id, sale_lead_product_id){
                $('#add_expenses_popup').dialog("open");
                $('#txtAchievementStartDate').datepicker("hide");
                $('#pch_waiting_load_2').fadeIn();
                setTimeout(function(){
                    getDataInvoiceAndProductOfExpenses(sale_lead_id, sale_lead_product_id);
                }, 100);
            };
            /**
             * Lay du lieu cho product va invoice
             */
            getDataInvoiceAndProductOfExpenses = function(sale_lead_id, sale_lead_product_id){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'get_data_production_expense_popup')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        id: sale_lead_product_id,
                        sale_lead_id: sale_lead_id,
                        sale_lead_product_id: sale_lead_product_id,
                        company_id: company_id
                    },
                    beforeSend:function(){

                    },
                    success:function(data) {
                        var _product = data.SaleLeadProduct ? data.SaleLeadProduct : '';
                        var _amountDue = 0;
                        if(_product.length != 0){
                            var _name = $this.selectMaps.sale_setting_lead_product[_product.sale_setting_lead_product] ? $this.selectMaps.sale_setting_lead_product[_product.sale_setting_lead_product] : '';
                            $('#textProductID').val(sale_lead_product_id);
                            $('#txtAchievementStartDate').val(_product.achievement_start_date);
                            $('#txtAchievementEndDate').val(_product.achievement_end_date);
                            $('#txtNumberOfMonthOfAchievement').val(_product.number_of_month_of_achievement);
                            $('#txtDateGoLive').val(_product.date_go_live);
                            $('#createdExpenses').attr('onclick', 'createdExpenses("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + _product.sale_setting_lead_product + '", "' + _product.reference + '", "' + _name + '");');
                            if(modifySaleDeal == 'false'){
                                $('#txtAchievementStartDate, #txtAchievementEndDate, #txtDateGoLive').attr('disabled', 'disabled');
                            }
                        }
                        var _expenses = data.Expense ? data.Expense : '';
                        var expensesHtml = '';
                        if(_expenses.length != 0){
                            var rels = 1;
                            var totalCapex = 0;
                            var totalOpex = 0;
                            var disabled = '';
                            if(modifySaleDeal == 'false'){
                                disabled = 'disabled="disabled"';
                            }
                            $.each(_expenses, function(idEx, val){
                                var _onchange = 'onchange=\'updateExpenses("' + val.sale_lead_id + '", "' + val.sale_lead_product_id + '", "' + val.sale_setting_lead_product + '", "' + val.reference + '", "' + rels + '", "' + val.id + '");\'';
                                var _onclick = 'onclick=\'clickExpenses("' + rels + '");\'';
                                var _onblur = 'onblur=\'blurExpenses("' + rels + '");\'';
                                var _onclickDelete = 'onclick=\'deleteExpenses("' + rels + '", "' + val.id + '");\'';
                                var _name = $this.selectMaps.sale_setting_lead_product[val.sale_setting_lead_product] ? $this.selectMaps.sale_setting_lead_product[val.sale_setting_lead_product] : '';
                                val.unit = (language === "eng") ? saleExpenses[val.sale_expense_id].unit_us : saleExpenses[val.sale_expense_id].unit_fr;
                                if(val.capex_opex === 'CAPEX'){
                                    totalCapex += parseFloat(number_format(val.amount_due, 2, '.', ''));
                                } else {
                                    totalOpex += parseFloat(number_format(val.amount_due, 2, '.', ''));
                                }
                                expensesHtml +=
                                '<div class="pch_expense_content_gourp_' + rels + '" rels="'+rels+'">' +
                                    '<div class="pch_input pch_input_reference">' +
                                        '<input type="text" value="' + val.reference + '" readonly="readonly" class="input_disabled" />' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_product">' +
                                        '<input type="text" value="' + _name + '" readonly="readonly" class="input_disabled" />' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_name">' +
                                        '<input type="text" value="' + val.name + '" id="exName_' + rels + '" ' + _onchange + ' ' + disabled + ' />' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_name">' +
                                        '<select id="exTypeOf_' + rels + '" ' + _onchange + ' ' + disabled + '>';
                                        var selectBoxOfType = '<option value="">--Select--</option>';
                                        if(saleExpenses){
                                            $.each(saleExpenses, function(index, value){
                                                if(val.sale_expense_id == index){
                                                    selectBoxOfType += '<option value="' + index + '" selected="selected">' + value['name'] + '</option>';
                                                } else {
                                                    selectBoxOfType += '<option value="' + index + '">' + value['name'] + '</option>';
                                                }
                                            });
                                        }
                                        expensesHtml += selectBoxOfType;
                                    expensesHtml +=
                                        '</select>' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_capex">' +
                                        '<input id="exCapex_' + rels + '" type="text" value="' + val.capex_opex + '" readonly="readonly" class="input_disabled" />' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_number">' +
                                        '<input id="exNumber_' + rels + '" type="text" value="' + val.number + '" ' + _onchange + ' ' + disabled + ' />' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_unit">' +
                                        '<input id="exUnit_' + rels + '" type="text" value="' + val.unit + '" readonly="readonly" class="input_disabled" />' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_unit_cost">' +
                                        '<input id="exUnitCost_' + rels + '" type="text" value="' + val.unit_cost + '" ' + _onchange + ' ' + _onclick + ' ' + _onblur + ' ' + disabled + ' style="float: left;" />' +
                                        '<span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px; float: left; margin-top: 5px;"> ' + saleCurrency + '</span>' +
                                    '</div>' +
                                    '<div class="pch_input pch_input_amount_due">' +
                                        '<input id="exAmountDue_' + rels + '" type="text" value="' + val.amount_due + '" readonly="readonly" class="input_disabled ' + val.capex_opex.toLocaleLowerCase() + '" style="float: left;" />' +
                                        '<span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px; float: left; margin-top: 5px;"> ' + saleCurrency + '</span>' +
                                    '</div>' +
                                    '<div class="pch_action_expense">' +
                                        '<a id="exDelete_' + rels + '" class="wd-hover-advance-tooltip" href="javascript:void(0);" ' + _onclickDelete + '>Delete</a>' +
                                    '</div>' +
                                '</div>';
                                rels++;
                            });
                        }
                        $('#pch_expense_content').html('');
                        $('#pch_expense_content').append(expensesHtml);
                        $('#txtTotalCapex').val(number_format(totalCapex, 2, '.', ' '));
                        $('#txtTotalOpex').val(number_format(totalOpex, 2, '.', ' '));
                        setTimeout(function(){
                            $('#pch_waiting_load_2').fadeOut();
                        }, 100);
                    }
                });
            }
            /**
             * Validation Start Date And End Date Of Expenses Popup
             */
            monthDiff = function(start, end){
                var months;
                if(start[1] == end[1]){
                    months = 1;
                } else {
                    months = (end[2] - start[2]) * 12;
                    months = parseInt(months) - parseInt(start[1]);
                    months = parseInt(months) + parseInt(end[1]) + 1;
                }
                return months <= 0 ? 0 : months;
            }
            validatedAchievement = function(checkChangDateGoLive){
                var _start = $("#txtAchievementStartDate").val().split('/');
                var startDate = _start;
                _start = new Date(_start[2] + '-' + _start[1] + '-' + _start[0]).getTime();

                var _end = $("#txtAchievementEndDate").val().split('/');
                var endDate = _end;
                _end = new Date(_end[2] + '-' + _end[1] + '-' + _end[0]).getTime();

                if(_start <= _end){
                    $('#txtAchievementEndDate').removeClass('border_error');
                    $('.pch_message_error').css('display', 'none');
                    var numberOfMonthOfAchievement = monthDiff(startDate, endDate);
                    $('#txtNumberOfMonthOfAchievement').val(numberOfMonthOfAchievement);
                    var _date = parseInt(endDate[0]) + 1;
                    var _month = parseInt(endDate[1]);
                    var _year = parseInt(endDate[2]);
                    var _totalDayOfMonth = daysInMonth(_month, _year);
                    if(_date > _totalDayOfMonth){
                        _date = 1;
                        _month++;
                    }
                    if(_month > 12){
                        _month = 1;
                        _year++;
                    }
                    _date = _date < 10 ? '0'+_date : _date;
                    _month = _month < 10 ? '0'+_month : _month;
                    if(checkChangDateGoLive === 'false'){
                        $('#txtDateGoLive').val(_date + '/' + _month + '/' + _year);
                    }
                    setTimeout(function(){
                        saveProductInExpensePop();
                    }, 200);
                } else {
                    $('.pch_message_error').css('display', 'block');
                    $('#txtAchievementEndDate').addClass('border_error');
                    $('#txtNumberOfMonthOfAchievement').val(0);
                    return false;
                }
            }
            /**
             * Validation Deal Start Date And Deal End Date
             */
            validatedDealDate = function(){
                var _start = $("#SaleLeadDealStartDate").val().split('/');
                var startDate = _start;
                _start = new Date(_start[2] + '-' + _start[1] + '-' + _start[0]).getTime();

                var _end = $("#SaleLeadDealEndDate").val().split('/');
                var endDate = _end;
                _end = new Date(_end[2] + '-' + _end[1] + '-' + _end[0]).getTime();

                if(_start <= _end){
                    $('#SaleLeadDealEndDate').removeClass('border_error');
                    $('.pch_message_error_deal').css('display', 'none');
                    return false;
                } else {
                    $('.pch_message_error_deal').css('display', 'block');
                    $('#SaleLeadDealEndDate').addClass('border_error');
                    return false;
                }
            }
            /**
             * Save Product In Popup
             */
            saveProductInExpensePop = function(){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_production_expenses_popup')); ?>',
                    async: false,
                    type : 'POST',
                    dataType : 'json',
                    data: {
                        id: $('#textProductID').val(),
                        achievement_start_date: $('#txtAchievementStartDate').val(),
                        achievement_end_date: $('#txtAchievementEndDate').val(),
                        number_of_month_of_achievement: $('#txtNumberOfMonthOfAchievement').val(),
                        date_go_live: $('#txtDateGoLive').val()
                    },
                    beforeSend:function(){
                        $('#txtAchievementStartDate').addClass('pch_loading');
                        $('#txtAchievementEndDate').addClass('pch_loading');
                        $('#txtDateGoLive').addClass('pch_loading');
                        $('#txtAchievementStartDate, #txtAchievementEndDate, #txtDateGoLive').css('color', 'rgb(218, 215, 215)');
                    },
                    success:function(data) {
                        setTimeout(function(){
                            $('#txtAchievementStartDate').removeClass('pch_loading');
                            $('#txtAchievementEndDate').removeClass('pch_loading');
                            $('#txtDateGoLive').removeClass('pch_loading');
                            $('#txtAchievementStartDate, #txtAchievementEndDate, #txtDateGoLive').css('color', '#3BBD43');
                        }, 200);
                    }
                });
            };
            /**
             * Add Expenses
             */
            createdExpenses = function(sale_lead_id, sale_lead_product_id, sale_setting_lead_product, reference, nameProduct){
                if(modifySaleDeal == 'false'){
                    return false;
                }
                var selectBoxOfType = '<option value="">--Select--</option>';
                if(saleExpenses){
                    $.each(saleExpenses, function(index, value){
                        selectBoxOfType += '<option value="' + index + '">' + value['name'] + '</option>';
                    });
                }
                var rels = $('#pch_expense_content').children().last().attr('rels');
                rels = rels ? parseInt(rels) + 1 : 1;
                var _onchange = 'onchange=\'updateExpenses("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + sale_setting_lead_product + '", "' + reference + '", "' + rels + '", "' + -1 + '");\'';
                var _onclick = 'onclick=\'clickExpenses("' + rels + '");\'';
                var _onblur = 'onblur=\'blurExpenses("' + rels + '");\'';
                var _onclickDelete = 'onclick=\'deleteExpenses("' + rels + '", "' + -1 + '");\'';
                var expensesHtml =
                '<div class="pch_expense_content_gourp_' + rels + '" rels="'+rels+'">' +
                    '<div class="pch_input pch_input_reference">' +
                        '<input type="text" value="' + reference + '" readonly="readonly" class="input_disabled" />' +
                    '</div>' +
                    '<div class="pch_input pch_input_product">' +
                        '<input type="text" value="' + nameProduct + '" readonly="readonly" class="input_disabled" />' +
                    '</div>' +
                    '<div class="pch_input pch_input_name">' +
                        '<input type="text" value="" id="exName_' + rels + '" ' + _onchange + ' />' +
                    '</div>' +
                    '<div class="pch_input pch_input_name">' +
                        '<select id="exTypeOf_' + rels + '" ' + _onchange + '>' +
                            selectBoxOfType +
                        '</select>' +
                    '</div>' +
                    '<div class="pch_input pch_input_capex">' +
                        '<input id="exCapex_' + rels + '" type="text" value="" readonly="readonly" class="input_disabled" />' +
                    '</div>' +
                    '<div class="pch_input pch_input_number">' +
                        '<input id="exNumber_' + rels + '" type="text" value="1" ' + _onchange + ' />' +
                    '</div>' +
                    '<div class="pch_input pch_input_unit">' +
                        '<input id="exUnit_' + rels + '" type="text" value="" readonly="readonly" class="input_disabled" />' +
                    '</div>' +
                    '<div class="pch_input pch_input_unit_cost">' +
                        '<input id="exUnitCost_' + rels + '" type="text" value="0" ' + _onchange + ' ' + _onclick + ' ' + _onblur + ' style="float: left;"/>' +
                        '<span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px; float: left; margin-top: 5px;"> ' + saleCurrency + '</span>' +
                    '</div>' +
                    '<div class="pch_input pch_input_amount_due">' +
                        '<input id="exAmountDue_' + rels + '" type="text" value="0" readonly="readonly" class="input_disabled" style="float: left;" />' +
                        '<span class="span_euro" style="font-size: 16px; font-weight: bold;color: #000; margin-left: -15px; float: left; margin-top: 5px;"> ' + saleCurrency + '</span>' +
                    '</div>' +
                    '<div class="pch_action_expense">' +
                        '<a id="exDelete_' + rels + '" class="wd-hover-advance-tooltip" href="javascript:void(0);" ' + _onclickDelete + '>Delete</a>' +
                    '</div>' +
                '</div>';
                $('#pch_expense_content').append(expensesHtml);
                $('#exName_' + rels).focus();
            };
            /**
             * Update Expenses
             */
            clickExpenses = function(key){
                $('#exUnitCost_' + key).val(number_format($('#exUnitCost_' + key).val(), 2, '.', ''));
            };
            blurExpenses = function(key){
                $('#exUnitCost_' + key).val(number_format($('#exUnitCost_' + key).val(), 2, '.', ' '));
            };
            updateExpenses = function(sale_lead_id, sale_lead_product_id, sale_setting_lead_product, reference, key, idOfExpenses){
                var _valTypeOf = $('#exTypeOf_' + key).val();
                $('#exCapex_' + key).val(saleExpenses[_valTypeOf] && saleExpenses[_valTypeOf].capex_opex ? saleExpenses[_valTypeOf].capex_opex.toLocaleUpperCase() : '');
                if(language === 'eng'){
                    $('#exUnit_' + key).val(saleExpenses[_valTypeOf] && saleExpenses[_valTypeOf].unit_us ? saleExpenses[_valTypeOf].unit_us.toLocaleUpperCase() : '');
                } else {
                    $('#exUnit_' + key).val(saleExpenses[_valTypeOf] && saleExpenses[_valTypeOf].unit_fr ? saleExpenses[_valTypeOf].unit_fr.toLocaleUpperCase() : '');
                }
                $('#exUnitCost_' + key).val(number_format($('#exUnitCost_' + key).val(), 2, '.', ' '));
                var _number = $('#exNumber_' + key).val() ? parseFloat($('#exNumber_' + key).val()) : 0;
                var _unitCost = $('#exUnitCost_' + key).val() ? parseFloat(number_format($('#exUnitCost_' + key).val(), 2, '.', '')) : 0;
                var _amountDue = _number * _unitCost;
                $('#exAmountDue_' + key).removeClass('capex opex');
                $('#exAmountDue_' + key).addClass(saleExpenses[_valTypeOf] && saleExpenses[_valTypeOf].capex_opex ? saleExpenses[_valTypeOf].capex_opex.toLocaleLowerCase() : '');
                $('#exAmountDue_' + key).val(number_format(_amountDue, 2, '.', ' '));
                var _totalCapex = 0;
                var _totalOpex = 0;
                $('#pch_expense_content').find('.capex').each(function(){
                    _totalCapex += parseFloat(number_format($(this).val(), 2, '.', ''));
                });
                $('#pch_expense_content').find('.opex').each(function(){
                    _totalOpex += parseFloat(number_format($(this).val(), 2, '.', ''));
                });
                $('#txtTotalCapex').val(number_format(_totalCapex, 2, '.', ' '));
                $('#txtTotalOpex').val(number_format(_totalOpex, 2, '.', ' '));
                if($('#exName_' + key).val() && $('#exTypeOf_' + key).val()){
                    setTimeout(function(){
                        $.ajax({
                            url: '<?php echo $html->url(array('action' => 'update_data_expenses')); ?>',
                            async: false,
                            type : 'POST',
                            dataType : 'json',
                            data: {
                                id: idOfExpenses,
                                company_id: company_id,
                                sale_lead_id: sale_lead_id,
                                sale_lead_product_id: sale_lead_product_id,
                                sale_setting_lead_product: sale_setting_lead_product,
                                reference: reference,
                                name: $('#exName_' + key).val(),
                                sale_expense_id: $('#exTypeOf_' + key).val(),
                                capex_opex: $('#exCapex_' + key).val(),
                                number: $('#exNumber_' + key).val(),
                                unit: $('#exUnit_' + key).val(),
                                unit_cost: $('#exUnitCost_' + key).val(),
                                amount_due: $('#exAmountDue_' + key).val()
                            },
                            beforeSend:function(){
                                $('#exName_' + key).addClass('pch_loading loang_input_name');
                                $('#exTypeOf_' + key).addClass('pch_loading');
                                $('#exNumber_' + key).addClass('pch_loading loang_input_number');
                                $('#exUnitCost_' + key).addClass('pch_loading loang_input_unit_cost');
                                $('#exName_' + key).css('color', 'rgb(218, 215, 215)');
                                $('#exTypeOf_' + key).css('color', 'rgb(218, 215, 215)');
                                $('#exNumber_' + key).css('color', 'rgb(218, 215, 215)');
                                $('#exUnitCost_' + key).css('color', 'rgb(218, 215, 215)');
                            },
                            success:function(data) {
                                var _idOfExpenses = data;
                                $('#exName_' + key).removeAttr('onchange');
                                $('#exName_' + key).attr('onchange', 'updateExpenses("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + sale_setting_lead_product + '", "' + reference + '", "' + key + '", "' + _idOfExpenses + '");');
                                $('#exTypeOf_' + key).removeAttr('onchange');
                                $('#exTypeOf_' + key).attr('onchange', 'updateExpenses("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + sale_setting_lead_product + '", "' + reference + '", "' + key + '", "' + _idOfExpenses + '");');
                                $('#exNumber_' + key).removeAttr('onchange');
                                $('#exNumber_' + key).attr('onchange', 'updateExpenses("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + sale_setting_lead_product + '", "' + reference + '", "' + key + '", "' + _idOfExpenses + '");');
                                $('#exUnitCost_' + key).removeAttr('onchange');
                                $('#exUnitCost_' + key).attr('onchange', 'updateExpenses("' + sale_lead_id + '", "' + sale_lead_product_id + '", "' + sale_setting_lead_product + '", "' + reference + '", "' + key + '", "' + _idOfExpenses + '");');
                                $('#exDelete_' + key).removeAttr('onclick');
                                $('#exDelete_' + key).attr('onclick', 'deleteExpenses("' + key + '", "' + _idOfExpenses + '");');
                                setTimeout(function(){
                                    $('#exName_' + key).removeClass('pch_loading loang_input_name');
                                    $('#exTypeOf_' + key).removeClass('pch_loading');
                                    $('#exNumber_' + key).removeClass('pch_loading loang_input_number');
                                    $('#exUnitCost_' + key).removeClass('pch_loading loang_input_unit_cost');
                                    $('#exName_' + key).css('color', '#3BBD43');
                                    $('#exTypeOf_' + key).css('color', '#3BBD43');
                                    $('#exNumber_' + key).css('color', '#3BBD43');
                                    $('#exUnitCost_' + key).css('color', '#3BBD43');
                                }, 200);
                            }
                        });
                    }, 200);
                }
            };
            /**
             * Xoa Mot Expenses
             */
            deleteExpenses = function(key, idOfExpenses){
                if(modifySaleDeal == 'false'){
                    return false;
                }
                $('#pch_delete_expenses_popup').dialog("open");
                $('.cancel').click(function(){
                    $('#pch_delete_expenses_popup').dialog("close");
                });
                $('#pch_delete_expenses_popup_submit').click(function(){
                    $('#pch_delete_expenses_popup').dialog("close");
                    $('#pch_expense_content').find('div.pch_expense_content_gourp_' + key).remove();
                    if(idOfExpenses != -1){
                        setTimeout(function(){
                            $.ajax({
                                url: '<?php echo $html->url(array('action' => 'delete_data_expenses')); ?>',
                                async: false,
                                type : 'POST',
                                dataType : 'json',
                                data: {
                                    id: idOfExpenses
                                },
                                beforeSend:function(){

                                },
                                success:function(data) {
                                    setTimeout(function(){
                                        //$('#pch_waiting_load_2').fadeOut();
                                    }, 200);
                                }
                            });
                        }, 200);
                    }
                });
            };
            /**
             * Multiple Upload
             */
            var uploader = $("#uploaderEstimate").pluploadQueue({
                runtimes : 'html5, html4',
                url : "/sale_leads/upload/"+company_id+"/"+idOfLead+'/estimate',
                chunk_size : '10mb',
                rename : true,
                dragdrop: true,
                filters : {
                    max_file_size : '10mb',
                    mime_types: [
                        {title : "Files", extensions : "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"}
                    ]
                },
                init: {
            		PostInit: function(up) {
                        up.idOfLead = idOfLead;
                        up.company_id = company_id;
                        up.linkedAction = '/sale_leads/attachment/estimate/';
                        up.checkDeal = '?deal=true';
                        var hideLinkDelete = linkDownMargin = '';
                        if(modifySaleDeal == 'false'){
                            up.disableBrowse(true);
                            hideLinkDelete = 'style="display:none;"';
                            linkDownMargin = 'style="margin-right:25px;"';
                        }
          		        if(saleLeadFileOfEstimates && Object.keys(saleLeadFileOfEstimates).length > 0){
          		            up.auditFiles = saleLeadFileOfEstimates;
          		            var tmpHtml = '';
                            $.each(saleLeadFileOfEstimates, function(ind, val){
                                var hrefDownload = '/sale_leads/attachment/estimate/'+company_id+'/'+idOfLead+'/'+val.id+'/download?deal=true';
                                var hrefDelete = '/sale_leads/attachment/estimate/'+company_id+'/'+idOfLead+'/'+val.id+'/delete?deal=true';
                                tmpHtml +=
                                '<li id="' + val.id + '" class="plupload_done">' +
            						'<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                                    '<div class="plupload_file_action_modify">' +
                                    '<a class="download-attachment" ' +linkDownMargin+ ' href="' +hrefDownload+ '" rels=' + val.id + '>Download</a>' +
                                    '<a class="delete-attachment" ' +hideLinkDelete+ ' href="' +hrefDelete+ '" rels=' + val.id + '>Delete</a></div>' +
            						'<div class="plupload_file_action"><a href="#" style="display: block;"></a></div>' +
            						'<div class="plupload_file_status">' + 100 + '%</div>' +
            						'<div class="plupload_file_size">' + plupload.formatSize(val.size) + '</div>' +
            						'<div class="plupload_clearer">&nbsp;</div>' +
            					'</li>';
                            });
                            $('#uploaderEstimate_filelist').html(tmpHtml);
                        }
            		}
            	}
            });
            var uploader = $("#uploaderOrder").pluploadQueue({
                runtimes : 'html5, html4',
                url : "/sale_leads/upload/"+company_id+"/"+idOfLead+'/order',
                chunk_size : '10mb',
                rename : true,
                dragdrop: true,
                filters : {
                    max_file_size : '10mb',
                    mime_types: [
                        {title : "Files", extensions : "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"}
                    ]
                },
                init: {
            		PostInit: function(up) {
                        up.idOfLead = idOfLead;
                        up.company_id = company_id;
                        up.linkedAction = '/sale_leads/attachment/order/';
                        up.checkDeal = '?deal=true';
                        var hideLinkDelete = linkDownMargin = '';
                        if(modifySaleDeal == 'false'){
                            up.disableBrowse(true);
                            hideLinkDelete = 'style="display:none;"';
                            linkDownMargin = 'style="margin-right:25px;"';
                        }
                        if(saleLeadFileOfOrders && Object.keys(saleLeadFileOfOrders).length > 0){
          		            up.auditFiles = saleLeadFileOfOrders;
          		            var tmpHtml = '';
                            $.each(saleLeadFileOfOrders, function(ind, val){
                                var hrefDownload = '/sale_leads/attachment/order/'+company_id+'/'+idOfLead+'/'+val.id+'/download?deal=true';
                                var hrefDelete = '/sale_leads/attachment/order/'+company_id+'/'+idOfLead+'/'+val.id+'/delete?deal=true';
                                tmpHtml +=
                                '<li id="' + val.id + '" class="plupload_done">' +
            						'<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                                    '<div class="plupload_file_action_modify">' +
                                    '<a class="download-attachment" ' +linkDownMargin+ ' href="' +hrefDownload+ '" rels=' + val.id + '>Download</a>' +
                                    '<a class="delete-attachment" ' +hideLinkDelete+ ' href="' +hrefDelete+ '" rels=' + val.id + '>Delete</a></div>' +
            						'<div class="plupload_file_action"><a href="#" style="display: block;"></a></div>' +
            						'<div class="plupload_file_status">' + 100 + '%</div>' +
            						'<div class="plupload_file_size">' + plupload.formatSize(val.size) + '</div>' +
            						'<div class="plupload_clearer">&nbsp;</div>' +
            					'</li>';
                            });
                            $('#uploaderOrder_filelist').html(tmpHtml);
                        }
            		}
            	}
            });
            /**
             * Add log system of sale lead
             */
            addLogSaleLead = function(){
                //var rels = $('#pch_log_system_content').children().last().attr('rels');
                var rels = $('#pch_log_system_content').children().length;
                rels = rels ? parseInt(rels) + 1 : 1;
                var _name = <?php echo json_encode($employeeLoginName);?>;
                var _avatarOfEmployeeLogin = <?php echo json_encode($avatarEmployeeLogin);?>;
                var _date = setAndGetTimeOfParis();
                _name += ' ' + _date;
                var _onchange = 'onchange=\'updateLogSystem("' + rels + '", "' + -1 + '");\'';
                var _onclick = 'onclick=\'updateAvatarLogSystem("' + rels + '", "' + -1 + '");\'';
                var avatar = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', employeeLoginId);
                var logSystemHtml =
                    '<div class="pch_log_system" rels="' + rels + '">' +
                        '<div class="pch_log_name">' +
                            '<input id="logName_' + rels + '" readonly="readonly" class="input_disabled" value="' + _name + '" />' +
                        '</div>' +
                        '<div class="pch_log_description">' +
                            '<input id="logDes_' + rels + '" type="text" ' + _onchange + '/>' +
                        '</div>' +
                        '<div class="pch_log_avatar pch_log_avatar_content">' +
                            '<img id="logAvatar_' + rels + '" src="' + avatar + '" ' + _onclick + ' />' +
                        '</div>' +
                    '</div>';
                $('#pch_log_system_content').prepend(logSystemHtml);
                $('#logDes_' + rels).focus();
            };
            /**
             * Save Log
             */
            updateLogSystem = function(key, idOfLog){
                if($('#logDes_' + key).val()){
                    setTimeout(function(){
                        $.ajax({
                            url: '<?php echo $html->url(array('action' => 'update_data_log')); ?>',
                            async: false,
                            type : 'POST',
                            dataType : 'json',
                            data: {
                                id: idOfLog,
                                company_id: company_id,
                                employee_id: employeeLoginId,
                                sale_lead_id: idOfLead,
                                name: $('#logName_' + key).val(),
                                description: $('#logDes_' + key).val()
                            },
                            beforeSend:function(){
                                $('#logDes_' + key).addClass('pch_loading');
                                $('#logDes_' + key).css('color', 'rgb(218, 215, 215)');
                            },
                            success:function(data) {
                                var _idOfLog = data;
                                $('#logDes_' + key).removeAttr('onchange');
                                $('#logDes_' + key).attr('onchange', 'updateLogSystem("' + key + '", "' + _idOfLog + '");');
                                $('#logAvatar_' + key).removeAttr('onchange');
                                $('#logAvatar_' + key).attr('onclick', 'updateAvatarLogSystem("' + key + '", "' + _idOfLog + '");');
                                setTimeout(function(){
                                    $('#logDes_' + key).removeClass('pch_loading');
                                    $('#logDes_' + key).css('color', '#3BBD43');
                                }, 200);
                            }
                        });
                    }, 200);
                }
            };
            /**
             * Update Avatar Of Log
             */
            var globalIdOfLog = 0;
            var globalKey = 0;
            updateAvatarLogSystem = function(key, idOfLog){
                return false; // ham nay bo.khong su dung nua.Lay avatar cua thang log o employee
                if(modifySaleDeal == 'false'){
                    return false;
                }
                $('#textAvatar').val('');
                if(idOfLog == -1){
                    return false;
                } else {
                    $('#avatar_popup').dialog("open");
                    globalIdOfLog = idOfLog;
                    globalKey = key;
                }
            }
            function getDoc(frame) {
                var doc = null;
                // IE8 cascading access check
                try {
                    if (frame.contentWindow) {
                        doc = frame.contentWindow.document;
                    }
                } catch(err) {}
                if (doc) { // successful getting content
                    return doc;
                }
                try { // simply checking may throw in ie8 under ssl or mismatched protocol
                    doc = frame.contentDocument ? frame.contentDocument : frame.document;
                } catch(err) {
                    // last attempt
                    doc = frame.document;
                }
                return doc;
            }
            $('#uploadForm').submit(function(e){
                if(window.FormData !== undefined){
                    var formData = new FormData($(this)[0]);
                    var formURL = $(this).attr("action");
                    setTimeout(function(){
                        $.ajax({
                            url: formURL,
                	        type: 'POST',
                			data:  formData,
                			mimeType:"multipart/form-data",
                            async: false,
                            cache: false,
                			contentType: false,
                    	    cache: false,
                        	processData: false,
                            success: function (data) {
                                var nameAvatar = JSON.parse(data);
                                if(nameAvatar){
                                    var link = <?php $this->UserFile->imagejs() ?>.replace('{path}', 'sale_leads/logs/'+companyName+'/'+idOfLead+'/'+nameAvatar);
                                } else {
                                    var link = '/img/business/avatar.gif';
                                }
                                $('#logAvatar_' + globalKey).attr('src', link);
                            }
                        });
                    }, 200);
                    e.preventDefault(); //Prevent Default action.
                } else {
                    var formObj = $(this);
                    //generate a random id
                    var iframeId = 'unique' + (new Date().getTime());
                    //create an empty iframe
                    var iframe = $('<iframe src="javascript:false;" name="'+iframeId+'" />');
                    //hide it
                    iframe.hide();
                    //set form target to iframe
                    formObj.attr('target',iframeId);
                    //Add iframe to body
                    iframe.appendTo('body');
                    iframe.load(function(e){
                        var doc = getDoc(iframe[0]);
                        var docRoot = doc.body ? doc.body : doc.documentElement;
                        var data = docRoot.innerHTML;
                        //data is returned from server.
                        var nameAvatar = JSON.parse(data);
                        if(nameAvatar){
                            var link = <?php $this->UserFile->imagejs() ?>.replace('{path}', 'sale_leads/logs/'+companyName+'/'+idOfLead+'/'+nameAvatar);
                        } else {
                            var link = '/img/business/avatar.gif';
                        }
                        $('#logAvatar_' + globalKey).attr('src', link);
                    });
                }
            });
            $("#avatar_popup_submit").click(function(){
                $('#UploadId').val(globalIdOfLog);
                $("#uploadForm").submit(); //Submit the form
                $("#avatar_popup").dialog("close");
            });
            $('.cancel').click(function(){
                $("#avatar_popup").dialog("close");
            });
            /**
             * Ham kiem tra va in lai chuoi mac dinh
             */
            checkDefaultValue = function(e, defaults, element){
                var _default = defaults;
                valKey = e.which;
                var _val = $('#'+element).val();
                _checkVal = _val.substr(0, _default.length);
                _val = _val.substr(_default.length);
                if(valKey==8){
				    if(_checkVal!=_default){
					   $('#'+element).val(_default);
					   return false;
				    }
			     }
			     if(_checkVal!=_default){
				    $('#'+element).val(_default);
			     } else{
				    $('#'+element).val(_default+_val);
			     }
            };
            /**
             * Khong cho click nut delete o production
             */
            $('.pch_delete').click(function(){
                return false;
            });
            // END
        });
    })(jQuery);
    /**
     * Validation Form Update Audit Mission
     */
    function validateForm(){
        var flag = true;
        $("#flashMessage").hide();
        $('div.error-message').remove();
        $("div.wd-input input, select").removeClass("form-error");
        if($('#SaleLeadName').val() == ''){
            var element = $("#SaleLeadName");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Name is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#SaleLeadCode').val() == '' || $('#SaleLeadCode').val() == idOfLead+'-'){
            var element = $("#SaleLeadCode");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The ID is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('.wd-combobox').html() == ''){
            var element = $(".wd-combobox");
            element.addClass("form-error");
            var parentElem = element.parent();
            element.addClass("error");
            parentElem.append('<div class="error-message" style="padding-left: 0px !important; margin-left: -1px;">'+"<?php __("The Salesman is not Blank!") ?>"+'</div>');
            flag = false;
        }
        return flag;
    }
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
    /**
     * Format Number
     */
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
</script>
