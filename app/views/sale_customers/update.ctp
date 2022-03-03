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
    )); 
    echo $html->css(array(
        'jquery.multiSelect',
        'jquery.dataTables',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
        'business'
    ));
    echo $this->element('dialog_projects');
?>
<style>
    fieldset div textarea{height: auto !important;}
</style>
<!-- group_information_popup -->
<div id="group_information_popup" style="display:none;" title="Information" class="buttons">
    <div class="wd-input">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/business/face-1.png"/><input type="text" id="textFacebook" /></li>
            <li><img src="/img/business/google-1.png"/><input type="text" id="textGoogle" /></li>
            <li><img src="/img/business/twitter-1.png"/><input type="text" id="textTwitter" /></li>
            <li><img src="/img/business/viadeo-1.png"/><input type="text" id="textViadeo" /></li>
            <li><img src="/img/business/linkedin-1.png"/><input type="text" id="textLinked" /></li>
        </ul>
        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
            <strong>Ex:</strong> 
            www.example.com
        </p>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="information_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
</div>
<!-- End group_information_popup -->
<!-- web_popup -->
<div id="web_popup" style="display:none;" title="Website" class="buttons">
    <div class="wd-input">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/business/web-1.png"/><input type="text" id="textWeb" /></li>
        </ul>
        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
            <strong>Ex:</strong> 
            www.example.com
        </p>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="web_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
</div>
<!-- End web_popup -->
<!-- avatar_popup -->
<div id="avatar_popup" style="display:none;" title="Avatar" class="buttons">
    <?php
    echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'sale_customers', 'action' => 'update_avatar', $company_id, $id)
    ));
    ?>
    <div class="wd-input">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/business/img-1.png"/><input type="file" id="textAvatar" name="FileField[attachment]" /></li>
        </ul>
        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
            <strong>Size:</strong> 
            170 x 206
        </p>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="avatar_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End avatar_popup -->
<div id="action-template" style="display: none;">
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%5$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_iban', '%1$s', '%2$s', '%3$s', '%4$s', '%5$s')); ?>">Delete</a>
    </div>
</div>
<div id="action-template-contact" style="display: none;">
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%5$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_contact', '%1$s', '%2$s', '%3$s', '%4$s', '%5$s')); ?>">Delete</a>
    </div>
</div>
<div id="action-iban-default" style="display: none;" style="margin: 0 auto !important; width: 54px;">
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('"%s"?', true), '%6$s')); ?>');" class="wd-update" style="margin-left: -5px;" href="<?php echo $this->Html->url(array('action' => 'update_iban_default', '%1$s', '%2$s', '%3$s', '%4$s', '%5$s', '%6$s')); ?>">Delete</a>
    </div>
</div>
<div id="action-iban-default-select" style="display: none;" style="margin: 0 auto !important; width: 54px;">
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('"%s"?', true), '%6$s')); ?>');" class="wd-update wd-update-default" style="margin-left: -5px;" href="<?php echo $this->Html->url(array('action' => 'update_iban_default', '%1$s', '%2$s', '%3$s', '%4$s', '%5$s', '%6$s')); ?>">Delete</a>
    </div>
</div>
<div id="wd-container-main" class="wd-project-detail"> 
    <?php echo $this->element("project_top_menu"); ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-title">
                <fieldset style="float: left;">
                    <div class="wd-submit" style="overflow: hidden;margin: 0; width: 400px;">
                        <?php if($created == true && $updated == true):?>
                        <button onclick="if(validateForm()){jQuery('#wd-fragment-1 form:first').submit();};" class="btn-text btn-green">
                            <img src="<?php echo $this->Html->url('/img/ui/blank-save.png') ?>" />
                            <span><?php __('Save') ?></span>
                        </button>
                        <?php endif;?>
                        <?php
                            $nameBack = 'Back';
                            $linkBack = $this->Html->url(array('controller' => 'sale_customers', 'action' => 'index', $category));
                            if($type === 'contact'){
                                $nameBack = 'Back';
                                $linkBack = $this->Html->url(array('controller' => 'sale_customer_contacts', 'action' => 'index'));
                            }
                        ?>
                        <a href="<?php echo $linkBack;?>" class="btn-text">
                            <img src="<?php echo $this->Html->url('/img/ui/blank-back.png') ?>" />
                            <span><?php echo __($nameBack) ?></span>
                        </a>
                    </div>   
                </fieldset>
            </div>
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2"><?php 
                        $nameDetail = 'Customer Details';
                        if($category == 'pro'){
                            $nameDetail = 'Provider Details';
                        }
                        echo __($nameDetail);
                        ?></h2>
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('SaleCustomer', array(
                            'enctype' => 'multipart/form-data',
                            'url' => array('controller' => 'sale_customers', 'action' => 'update', $type, $category, $company_id, $id)
                        ));
                        echo $this->Form->input('id');
                        App::import("vendor", "str_utility");
                        $str_utility = new str_utility();
                        ?>
                        <fieldset>
                            <div class="wd-scroll-form" style="height:auto;">
                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="sale_setting_customer_status"><?php __("Status") ?></label>
                                        <?php 
                                            echo $this->Form->input('sale_setting_customer_status', array('div' => false, 'label' => false, 
                                                "options" => !empty($saleSettings[0]) ? $saleSettings[0] : array())); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="name" style="color: #F00808;"><?php __("Name");?></label>
                                        <?php
                                        echo $this->Form->input('name', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-area wd-none">
                                        <label style="color: #F00808;"><?php __("Address") ?></label>
                                        <?php 
                                        echo $this->Form->input('address', array('type' => 'textarea', 
                                            'div' => false, 'label' => false, 'rows' => '3',
                                            "style" => "padding: 6px 2px; width: 62.5%")); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="sale_setting_customer_country" style="color: #F00808;"><?php __("Country") ?></label>
                                        <?php 
                                            echo $this->Form->input('sale_setting_customer_country', array('div' => false, 'label' => false, 
                                                'value' => 65,'value' => !empty($this->data['SaleCustomer']['sale_setting_customer_country']) ? $this->data['SaleCustomer']['sale_setting_customer_country'] : 65,
                                                "options" => !empty($saleSettings[3]) ? $saleSettings[3] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="vat_number"><?php __("VAT Number");?></label>
                                        <?php
                                        echo $this->Form->input('vat_number', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="regis_number"><?php __("Registration Number");?></label>
                                        <?php
                                        echo $this->Form->input('regis_number', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="email"><?php __("Email");?></label>
                                        <?php
                                        echo $this->Form->input('email', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="phone"><?php __("Phone");?></label>
                                        <?php
                                        echo $this->Form->input('phone', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input">
                                        <div id="ch_avatar">
                                            <?php
                                                $linkAvatar = '/img/business/avatar.gif';
                                                if(!empty($this->data['SaleCustomer']['logo'])){
                                                    $linkAvatar = $this->UserFile->image('business/customers/'.$id.'/'.$this->data['SaleCustomer']['logo']);
                                                }
                                            ?>
                                            <img src="<?php echo $linkAvatar;?>"/>
                                        </div>
                                        <?php if(!empty($id) && $created == true && $updated == true):?>
                                        <div class="ch-edit" style="margin-top: -25px;">
                                            <a class="wd-edit" href="javascript:void(0);" id="edit_avatar">Edit</a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wd-input">
                                        <ul id="group-infor-address">
                                            <?php
                                                $linkedFacebook = !empty($this->data['SaleCustomer']['facebook']) ? $this->data['SaleCustomer']['facebook'] : 'javascript:void(0);';
                                                $linkedGoogle = !empty($this->data['SaleCustomer']['google']) ? $this->data['SaleCustomer']['google'] : 'javascript:void(0);';
                                                $linkedTwitter = !empty($this->data['SaleCustomer']['twitter']) ? $this->data['SaleCustomer']['twitter'] : 'javascript:void(0);';
                                                $linkedViadeo = !empty($this->data['SaleCustomer']['viadeo']) ? $this->data['SaleCustomer']['viadeo'] : 'javascript:void(0);';
                                                $linkedLinkedin = !empty($this->data['SaleCustomer']['linkedin']) ? $this->data['SaleCustomer']['linkedin'] : 'javascript:void(0);';
                                            ?>
                                            <li><a href="http://<?php echo $linkedFacebook?>" target="_blank" id="linkFB"><img src="/img/business/face-1.png"/></a></li>
                                            <li><a href="http://<?php echo $linkedGoogle?>" target="_blank" id="linkGG"><img src="/img/business/google-1.png"/></a></li>
                                            <li><a href="http://<?php echo $linkedTwitter?>" target="_blank" id="linkTW"><img src="/img/business/twitter-1.png"/></a></li>
                                            <li><a href="http://<?php echo $linkedViadeo?>" target="_blank" id="linkVD"><img src="/img/business/viadeo-1.png"/></a></li>
                                            <li><a href="http://<?php echo $linkedLinkedin?>" target="_blank" id="linkLI"><img src="/img/business/linkedin-1.png"/></a></li>
                                        </ul>
                                        <?php if(!empty($id) && $created == true && $updated == true):?>
                                        <div class="ch-edit" style="padding-top: 5px;">
                                            <a class="wd-edit" href="javascript:void(0);" id="edit_group_infor">Edit</a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wd-input">
                                        <?php
                                            $Website = !empty($this->data['SaleCustomer']['website']) ? $this->data['SaleCustomer']['website'] : 'Website';
                                            $linked = !empty($this->data['SaleCustomer']['website']) ? $this->data['SaleCustomer']['website'] : 'javascript:void(0);';
                                        ?>
                                        <a href="http://<?php echo $linked;?>" target="_blank" class="link-wed">
                                            <?php echo  $Website?>
                                        </a>
                                        <?php if(!empty($id) && $created == true && $updated == true):?>
                                        <div class="ch-edit" style="margin-top: -25px;">
                                            <a class="wd-edit" href="javascript:void(0);" id="edit_link">Edit</a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="sale_setting_customer_industry"><?php __("Industry") ?></label>
                                        <?php 
                                            echo $this->Form->input('sale_setting_customer_industry', array('div' => false, 'label' => false, 
                                                "options" => !empty($saleSettings[1]) ? $saleSettings[1] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input" style="position: relative;">
                                        <label for="annual_revenu"><?php __("Business Sale");?></label>
                                        <?php
                                        echo $this->Form->input('annual_revenu', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                        <span class="span_euro" style="position: absolute; font-size: 16px; font-weight: bold; top: 6px; margin-left: 4px;"><?php echo $saleCurrency;?></span>
                                    </div>
                                </div>
                                <h4 style="color: #2EADBE; font-size: 15px; clear: both;"><?php __('Invoice Information');?></h4>
                                <hr style="clear: both;" />
                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="sale_setting_customer_payment" style="color: #F00808;"><?php __("Payment delay type") ?></label>
                                        <?php 
                                            echo $this->Form->input('sale_setting_customer_payment', array('div' => false, 'label' => false, 
                                                "options" => !empty($saleSettings[2]) ? $saleSettings[2] : array())); ?>
                                    </div>
                                    <div class="wd-input" style="position: relative;">
                                        <label for="payment_delay" style="color: #F00808;"><?php __("Payment delay") ?></label>
                                        <?php
                                        echo $this->Form->input('payment_delay', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'value' => !empty($this->data['SaleCustomer']['payment_delay']) ? $this->data['SaleCustomer']['payment_delay'] : '45',
                                            "style" => "padding: 6px 2px; width: 5%;"));
                                        ?>
                                        <p id="group-plus">
                                            <span id="ch_cong"><img src="/img/business/cong-1.png"/></span>
                                            <span id="ch_tru"><img src="/img/business/tru-1.png"/></span>
                                        </p>
                                    </div>
                                    <div class="wd-input">
                                        <label for="invoice_sale_setting_customer_country"><?php __("Country") ?></label>
                                        <?php 
                                            echo $this->Form->input('invoice_sale_setting_customer_country', array('div' => false, 'label' => false, 
                                                'value' => !empty($this->data['SaleCustomer']['invoice_sale_setting_customer_country']) ? $this->data['SaleCustomer']['invoice_sale_setting_customer_country'] : 65,
                                                "options" => !empty($saleSettings[3]) ? $saleSettings[3] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input wd-area wd-none">
                                        <label><?php __("Invoice Address") ?></label>
                                        <?php 
                                        echo $this->Form->input('invoice_address', array('type' => 'textarea', 
                                            'div' => false, 'label' => false, 'rows' => '3',
                                            "style" => "padding: 6px 2px; width: 62.5%")); ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <?php if(!empty($id) && $created == true && $updated == true):?>
                                    <div class="wd-title">
                                        <a href="javascript:void(0);" id="add-activity" class="btn btn-plus" style="margin-right:15px;" onclick="addCustomerIban();"><span></span></a>
                                    </div>
                                    <?php endif;?>
                                    <div class="wd-table" id="project_container" style="margin-top: 8px; width:620px; height: <?php echo (!empty($id) && $created == true && $updated == true) ? '150px' : '186px';?>; clear: both; border: 1px solid #E0E0E0; overflow-y: scroll !important;">
                                        <div class="grid_ban grid_ban_header">
                                            <div class="iban_defaults"><span>#</span></div>
                                            <div class="iban_bic"><span><?php echo __('BIC', true);?></span></div>
                                            <div class="iban_iban"><span><?php echo __('IBAN', true);?></span></div>
                                            <div class="iban_action"><span><?php echo __('Action', true);?></span></div>
                                        </div>
                                        <div class="width-header"></div>
                                        <?php 
                                            if(!empty($saleIbans)):
                                                $reCheck = 0;
                                                foreach($saleIbans as $saleIban):
                                                    $message = __('Do you want to choose ' .$saleIban['iban']. ' as Default IBAN?', true);
                                                    $checkDefault = 'false';
                                                    $class = '';
                                                    if($saleIban['defaults'] == 1){
                                                        $message = __('Are you sure destroy ' .$saleIban['iban']. ' the Default IBAN?', true);
                                                        $checkDefault = 'true';
                                                        $class = ' wd-update-default';
                                                    }
                                                    $messageDelete = __('Are you sure you want to delete '.$saleIban['iban'].'?', true);
                                                    $reCheck++;
                                        ?>
                                        <div class="grid_ban" rels="<?php echo $reCheck;?>">
                                            <div class="iban_defaults iban_defaults_<?php echo $reCheck;?>">
                                                <a onclick="return confirm('<?php echo $message; ?>');" class="wd-update <?php echo $class;?>" href="<?php echo $this->Html->url(array('action' => 'update_iban_default', $type, $category, $company_id, $id, $saleIban['id'], $checkDefault)); ?>">Delete</a>
                                            </div>
                                            <div class="iban_bic iban_bic_<?php echo $reCheck;?>"><input type="text" maxlength="11" id="txtBIC_<?php echo $reCheck;?>" value="<?php echo $saleIban['bic'];?>" onchange="updateSaleIban('<?php echo $reCheck;?>', '<?php echo $saleIban['id'];?>');" /></div>
                                            <div class="iban_iban iban_iban_<?php echo $reCheck;?>"><input type="text" maxlength="34" id="txtIBAN_<?php echo $reCheck;?>" value="<?php echo $saleIban['iban'];?>" onchange="updateSaleIban('<?php echo $reCheck;?>', '<?php echo $saleIban['id'];?>');" /></div>
                                            <div class="iban_action iban_action_<?php echo $reCheck;?>">
                                                <a onclick="return confirm('<?php echo $messageDelete; ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_iban', $type, $category, $company_id, $id, $saleIban['id'])); ?>">Delete</a>
                                            </div>
                                        </div>
                                        <?php 
                                                endforeach;
                                            endif;
                                        ?>
                                    </div>
                                    <div id="hiddenGridOne">
                                        <p><?php echo __('Only Modify When Created Customer', true);?></p>
                                    </div>
                                </div>
                                <h4 style="color: #2EADBE; font-size: 15px; clear: both;"><?php __('Contact');?></h4>
                                <hr style="clear: both;" />
                            </div>
                        </fieldset>
                        </form>
                        <?php if(!empty($id) && $created == true && $updated == true):?>
                        <div class="wd-title">
                            <a href="javascript:void(0);" id="add-activity" class="btn btn-plus" style="margin-right:15px;" onclick="addNewSalesButton();"><span></span></a>
                        </div>
                        <?php endif;?>
                        <div class="wd-table" id="project_container_contact" style="margin-top: 8px; width:99%; height: 220px; clear: both; border-bottom: 1px solid #E0E0E0;">

                        </div>
                        <div id="hiddenGridTwo">
                            <p><?php echo __('Only Modify When Created Customer', true);?></p>
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

/**
 * Contact
 */
$columns = array(
    array(
        'id' => 'first_name',
        'field' => 'first_name',
        'name' => __('First Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUnique',
        //'formatter' => 'Slick.Formatters.forecastValue'
    ),
    array(
        'id' => 'last_name',
        'field' => 'last_name',
        'name' => __('Last Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUniqueLastName',
    ),
    array(
        'id' => 'email',
        'field' => 'email',
        'name' => __('E-Mail', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUniqueEmail',
    ),
    array(
        'id' => 'phone',
        'field' => 'phone',
        'name' => __('Phone', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.textBox',
    ),
    array(
        'id' => 'in_charge_of',
        'field' => 'in_charge_of',
        'name' => __('In Charge Of', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.textBox',
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 60,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.ActionContact'
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array();
App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($saleContacts)){
    foreach($saleContacts as $saleContact){
        $data = array(
            'id' => $saleContact['id'],
            'no.' => $i++,
            'MetaData' => array()
        );
        $data['first_name'] = (string) $saleContact['first_name'];
        $data['last_name'] = (string) $saleContact['last_name'];
        $data['email'] = (string) $saleContact['email'];
        $data['phone'] = (string) $saleContact['phone'];
        $data['in_charge_of'] = (string) $saleContact['in_charge_of'];
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
<script>
    var DataValidator = {};
    (function($){
        $(function(){
            /**
             * IBAN
             */
            var $this = SlickGridCustom,
            company_id = <?php echo json_encode($company_id);?>,
            companyName = <?php echo json_encode($companyName);?>,
            created = <?php echo json_encode($created);?>,
            updated = <?php echo json_encode($updated);?>,
            createdSaleContact = <?php echo json_encode($createdSaleContact);?>,
            updatedSaleContact = <?php echo json_encode($updatedSaleContact);?>,
            paymentTypes = <?php echo json_encode($paymentTypes);?>,
            type = <?php echo json_encode($type);?>,
            category = <?php echo json_encode($category);?>,
            sale_customer_id = <?php echo $id ? $id : 0;?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified = (createdSaleContact == true && updatedSaleContact == true) ? true : false;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var actionTemplateContact = $('#action-template-contact').html();
            var actionIbanDefaultHtml = $('#action-iban-default').html();
            var actuonIbanDefaultHtmlSelect = $('#action-iban-default-select').html();            
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, type, category, company_id,
                    sale_customer_id, dataContext.id, dataContext.iban), columnDef, dataContext);
                },
                ActionContact: function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplateContact, type, category, company_id,
                    sale_customer_id, dataContext.id, dataContext.first_name + ' ' + dataContext.last_name), columnDef, dataContext);
                },
                ActionIbanDefault: function(row, cell, value, columnDef, dataContext){
                    var check = 'false';
                    var message = 'Do you want to choose ' + dataContext.iban + ' as Default IBAN?';
                    var htmlCustom = actionIbanDefaultHtml;
                    if(dataContext.defaults == 1){
                        htmlCustom = actuonIbanDefaultHtmlSelect;
                        check = 'true';
                        message = 'Are you sure destroy ' + dataContext.iban + ' as Default IBAN?';
                    }
                    return Slick.Formatters.HTMLData(row, cell,$this.t(htmlCustom, type, category, company_id,
                    sale_customer_id, dataContext.id, check, message), columnDef, dataContext);
                }
            });;
            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.last_name && args.item.last_name.toLowerCase() == dx.last_name.toLowerCase() && dx.first_name.toLowerCase() == value.toLowerCase()){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('The First Name/Last Name has already been exist.')
                };
            }
            DataValidator.isUniqueLastName = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.first_name && args.item.first_name.toLowerCase() == dx.first_name.toLowerCase() && dx.last_name.toLowerCase() == value.toLowerCase()){
                        result = false;
                    }
                });
                return {
                    valid : result,
                    message : $this.t('The First Name/Last Name has already been exist.')
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
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>'},
                sale_customer_id: {defaulValue : '<?php echo $id ? $id : 0; ?>'},
                first_name: {defaulValue : '', allowEmpty : false},
                last_name: {defaulValue : '', allowEmpty : false},
                email: {defaulValue : ''},
                phone: {defaulValue : ''},
                in_charge_of: {defaulValue : ''},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update_contact')); ?>';
            ControlGrid = $this.init($('#project_container_contact'), data, columns);
            addNewSalesButton = function(){
                ControlGrid.gotoCell(data.length, 0, true);
            }
            addCustomerIban = function(){
                var rels = $('#project_container').children().last().attr('rels');
                rels = rels ? parseInt(rels) + 1 : 1;
                var htmlIban = '<div class="grid_ban" rels="'+rels+'">' +
                                    '<div class="iban_defaults iban_defaults_'+rels+' created_iban"></div>' +
                                    '<div class="iban_bic iban_bic_'+rels+'""><input type="text" maxlength="11" id="txtBIC_'+rels+'" value="" onchange="updateSaleIban('+rels+', -1)" /></div>' +
                                    '<div class="iban_iban iban_iban_'+rels+'""><input type="text" maxlength="34" id="txtIBAN_'+rels+'" value="" onchange="updateSaleIban('+rels+', -1)" /></div>' + 
                                    '<div class="iban_action iban_action_'+rels+'"></div>' +
                                '</div>';
                $('#project_container').append(htmlIban);
                $('#txtBIC_' + rels).focus();
            }
            updateSaleIban = function(recheck, id){
                var txtBIC = $('#txtBIC_'+recheck).val();
                var txtIBAN = $('#txtIBAN_'+recheck).val();
                
                if(txtBIC != '' && txtIBAN != ''){
                    $.ajax({
                        url: '<?php echo $html->url(array('action' => 'update_iban')); ?>',
                        async: false, 
                        type : 'POST',
                        dataType : 'json', 
                        data: {
                            bic: txtBIC,
                            iban: txtIBAN, 
                            company_id: company_id,
                            sale_customer_id: sale_customer_id,
                            id: id
                        },
                        beforeSend:function(){
                            $('#txtBIC_'+recheck).addClass('loading');
                            $('#txtIBAN_'+recheck).addClass('loading');
                        },
                        success:function(data) {
                            if(data && data.action === 'created'){
                                /**
                                 * Add Button Defaul
                                 */
                                var _masseges = '<?php echo h(sprintf(__('Do you want to choose "%s" as Default IBAN?', true), '%1$s'));?>';
                                _messages = $this.t(_masseges, data.iban);
                                var onClick  = "return confirm('"+_messages+"')";
                                var _href = '<?php echo $this->Html->url(array('action' => 'update_iban_default', $type, $category, $company_id, $id)); ?>';
                                $('.iban_defaults_'+recheck).html('<a onclick="'+onClick+'" class="wd-update" href="' + _href + '/' + data.id + '/false"></a>');
                                /**
                                 * Add Button Delete
                                 */
                                var _deleteMS = '<?php echo h(sprintf(__('Delete?', true), '%1$s'));?>';
                                _deleteMS = $this.t(_deleteMS, data.iban);
                                var onClickDelete  = "return confirm('"+_deleteMS+"')";
                                var _href = '<?php echo $this->Html->url(array('action' => 'delete_iban', $type, $category, $company_id, $id)); ?>';
                                $('.iban_action_'+recheck).html('<a onclick="'+onClickDelete+'" class="wd-hover-advance-tooltip" href="' + _href + '/' + data.id + '">Delete</a>');
                                /**
                                 * Edit Onchang In input of BIC
                                 */
                                $('.iban_bic_'+recheck).find('input').removeAttr('onchange');
                                $('.iban_bic_'+recheck).find('input').attr('onchange', 'updateSaleIban(' + recheck + ', ' + data.id + ');');
                                /**
                                 * Edit Onchang In Input OF IBAN
                                 */
                                $('.iban_iban_'+recheck).find('input').removeAttr('onchange');
                                $('.iban_iban_'+recheck).find('input').attr('onchange', 'updateSaleIban(' + recheck + ', ' + data.id + ');');
                            }
                            setTimeout(function(){
                                $('#txtBIC_'+recheck).css('color', '#3BBD43');
                                $('#txtIBAN_'+recheck).css('color', '#3BBD43');
                                $('#txtBIC_'+recheck).removeClass('loading');
                                $('#txtIBAN_'+recheck).removeClass('loading');
                            }, 200);
                        }
                    });
                } else {
                    $('#txtBIC_'+recheck).css('color', '#1362D8');
                    $('#txtIBAN_'+recheck).css('color', '#1362D8');
                }
            }
            /**
             * Control Group Information Contact
             */
            $('#group_information_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 460,
                height      : 290
            });
            if(typeof String.prototype.trim !== 'function') {
              String.prototype.trim = function() {
                return this.replace(/^\s+|\s+$/g, ''); 
              }
            }
            $('#edit_group_infor').click(function(){
                var FB = ($('#linkFB').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkFB').attr('href').replace('http://', '').trim();
                var GT = ($('#linkGG').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkGG').attr('href').replace('http://', '').trim();
                var TW = ($('#linkTW').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkTW').attr('href').replace('http://', '').trim();
                var VD = ($('#linkVD').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkVD').attr('href').replace('http://', '').trim();
                var LI = ($('#linkLI').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkLI').attr('href').replace('http://', '').trim();
                $('#textFacebook').val(FB);
                $('#textGoogle').val(GT);
                $('#textTwitter').val(TW);
                $('#textViadeo').val(VD);
                $('#textLinked').val(LI);
                $('#group_information_popup').dialog("open");
            });
            $('#information_popup_submit').click(function(){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_group_infor')); ?>',
                    async: false, 
                    type : 'POST',
                    dataType : 'json', 
                    data: {
                        facebook: $('#textFacebook').val(),
                        google: $('#textGoogle').val(),
                        twitter: $('#textTwitter').val(),
                        viadeo: $('#textViadeo').val(),
                        linkedin: $('#textLinked').val(), 
                        company_id: company_id,
                        id: sale_customer_id
                    },
                    success:function(data) {
                        $('#linkFB').attr('href', 'http://' + data.facebook);
                        $('#linkGG').attr('href', 'http://' + data.google);
                        $('#linkTW').attr('href', 'http://' + data.twitter);
                        $('#linkVD').attr('href', 'http://' + data.viadeo);
                        $('#linkLI').attr('href', 'http://' + data.linkedin);
                        $("#group_information_popup").dialog("close");
                    }
                });
            });
            /**
             * Control Link Web
             */
            $('#web_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 460,
                height      : 150
            });
            $('#edit_link').click(function(){
                var WEB = ($('.link-wed').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('.link-wed').attr('href').replace('http://', '').trim();
                $('#textWeb').val(WEB);
                $('#web_popup').dialog("open");
            });
            $('#web_popup_submit').click(function(){
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'update_website')); ?>',
                    async: false, 
                    type : 'POST',
                    dataType : 'json', 
                    data: {
                        website: $('#textWeb').val(),
                        company_id: company_id,
                        id: sale_customer_id
                    },
                    success:function(data) {
                        var nameWeb = data ? data : 'Website';
                        var linkedWeb = data ? data : 'javascript:void(0);';
                        $('.link-wed').attr('href', 'http://' + linkedWeb);
                        $('.link-wed').html(nameWeb);
                        $("#web_popup").dialog("close");
                    }
                });
            });
            /**
             * Control Avatar
             */
            $('#avatar_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 460,
                height      : 150
            });
            $('#edit_avatar').click(function(){
                $('#avatar_popup').dialog("open");
            });
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
                            window.location = ('/sale_customers/update/customer/' + category + '/' + company_id + '/' + sale_customer_id);
                            var nameAvatar = JSON.parse(data);
                            if(nameAvatar){
                                var link = <?php $this->UserFile->imagejs() ?>.replace('{path}', 'business/customers/'+companyName+'/'+sale_customer_id+'/'+nameAvatar);
                            } else {
                                var link = '/img/business/avatar.gif';
                            }
                            $('#ch_avatar').find('img').attr('src', link);
                        }
                    });
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
                        window.location = ('/sale_customers/update/customer/' + category + '/' + company_id + '/' + sale_customer_id);
                        var doc = getDoc(iframe[0]);
                        var docRoot = doc.body ? doc.body : doc.documentElement;
                        var data = docRoot.innerHTML;
                        //data is returned from server.
                        var nameAvatar = JSON.parse(data);
                        if(nameAvatar){
                            var link = <?php $this->UserFile->imagejs() ?>.replace('{path}', 'business/customers/'+companyName+'/'+sale_customer_id+'/'+nameAvatar);
                        } else {
                            var link = '/img/business/avatar.gif';
                        }
                        $('#ch_avatar').find('img').attr('src', link);
                    });
                }
            });
            $("#avatar_popup_submit").click(function(){
                $("#uploadForm").submit(); //Submit the form
                $("#avatar_popup").dialog("close");
            });
            /**
             * Close Popup
             */
            $(".cancel").live('click',function(){
                $("#group_information_popup").dialog("close");
                $("#web_popup").dialog("close");
                $("#avatar_popup").dialog("close");
            });
            /**
             * Control Payment Delay
             */
            $('#ch_cong').click(function(){
                var val = $('#SaleCustomerPaymentDelay').val();
                val = parseInt(val) + 1;
                $('#SaleCustomerPaymentDelay').val(val);
            });
            $('#ch_tru').click(function(){
                var val = $('#SaleCustomerPaymentDelay').val();
                val = parseInt(val) - 1;
                $('#SaleCustomerPaymentDelay').val(val);
            });
            /**
             * Control dau grid
             */
            if(sale_customer_id == 0 || !sale_customer_id){
                $("#project_container").mouseover(function(){
                    $('#hiddenGridOne').fadeIn();
                });
                $("#hiddenGridOne").mouseout(function(){
                    $('#hiddenGridOne').fadeOut();
                });
                $("#project_container_contact").mouseover(function(){
                    $('#hiddenGridTwo').fadeIn();
                });
                $("#hiddenGridTwo").mouseout(function(){
                    $('#hiddenGridTwo').fadeOut();
                });
            }
            /**
             * Change Address invoice By Address Company
             * Change Contry Invoice By Contry Company
             */
            $('#SaleCustomerAddress').change(function(){
                var addressOfCompany = $(this).val();
                $('#SaleCustomerInvoiceAddress').val(addressOfCompany);
                //if($('#SaleCustomerInvoiceAddress').val() == ''){
//                    $('#SaleCustomerInvoiceAddress').val(addressOfCompany);
//                } 
            });
            $('#SaleCustomerSaleSettingCustomerCountry').change(function(){
                var countryOfCompany = $(this).val();
                $('#SaleCustomerInvoiceSaleSettingCustomerCountry').val(countryOfCompany);
                //if($('#SaleCustomerInvoiceSaleSettingCustomerCountry').val() == '' || $('#SaleCustomerInvoiceSaleSettingCustomerCountry').val() == '--Select--'){
//                    $('#SaleCustomerInvoiceSaleSettingCustomerCountry').val(countryOfCompany);
//                } 
            });
            /**
             * Change Payment Delay Type
             */
            $('#SaleCustomerSaleSettingCustomerPayment').change(function(){
                var name = $(this).val();
                if(name == paymentTypes){
                    $('#SaleCustomerPaymentDelay').val(1);
                    $('#SaleCustomerPaymentDelay').attr('readonly', 'readonly');
                    $('#ch_cong, #ch_tru').css('display', 'none');
                } else {
                    $('#SaleCustomerPaymentDelay').val(45);
                    $('#SaleCustomerPaymentDelay').removeAttr('readonly');
                    $('#ch_cong, #ch_tru').css('display', 'block');
                }
            });
            /**
             * Check Payment Delay Type = payment on receipt or = a réception when not modify Payment Delay
             */
            var namePayType = $('#SaleCustomerSaleSettingCustomerPayment').val();
            if(namePayType == paymentTypes){
                $('#SaleCustomerPaymentDelay').val(1);
                $('#SaleCustomerPaymentDelay').attr('readonly', 'readonly');
                $('#ch_cong, #ch_tru').css('display', 'none');
            }
            /**
             * Check allow modify grid iban
             */
            if(created == true && updated == true){
                // do nothing
            } else {
                $('.iban_bic').find('input').attr('readonly', 'readonly');
                $('.iban_iban').find('input').attr('readonly', 'readonly');
                $('.iban_defaults a').click(function(){
                    return false;
                });
                $('.iban_action a').click(function(){
                    return false;
                });
            }
            /**
             * Format Number Of Chiffre Affaire
             */
            var valChiffre = $('#SaleCustomerAnnualRevenu').val();
            $('#SaleCustomerAnnualRevenu').val(number_format(valChiffre, 2, '.', ' '));
            $('#SaleCustomerAnnualRevenu').click(function(){
                valChiffre = $('#SaleCustomerAnnualRevenu').val();
                $('#SaleCustomerAnnualRevenu').val(number_format(valChiffre, 0, '.', ''));
            });
            $('#SaleCustomerAnnualRevenu').blur(function(){
                valChiffre = $('#SaleCustomerAnnualRevenu').val();
                $('#SaleCustomerAnnualRevenu').val(number_format(valChiffre, 2, '.', ' '));
            });
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
        if($('#SaleCustomerName').val() == ''){
            var element = $("#SaleCustomerName");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Name is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#SaleCustomerAddress').val() == ''){
            var element = $("#SaleCustomerAddress");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Address is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#SaleCustomerSaleSettingCustomerCountry').val() == '' || $('#SaleCustomerSaleSettingCustomerCountry').val() == '--Select--'){
            var element = $("#SaleCustomerSaleSettingCustomerCountry");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Country is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#SaleCustomerSaleSettingCustomerPayment').val() == '' || $('#SaleCustomerSaleSettingCustomerPayment').val() == '--Select--'){
            var element = $("#SaleCustomerSaleSettingCustomerPayment");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Payment Delay Type is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#SaleCustomerPaymentDelay').val() == ''){
            var element = $("#SaleCustomerPaymentDelay");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Payment Delay is not Blank!") ?>"+'</div>');
            flag = false;
        }
        var email = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
        if($('#SaleCustomerEmail').val() != '' && !email.test($('#SaleCustomerEmail').val())){
            var element = $("#SaleCustomerEmail");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("Email Not Valid!") ?>"+'</div>');
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