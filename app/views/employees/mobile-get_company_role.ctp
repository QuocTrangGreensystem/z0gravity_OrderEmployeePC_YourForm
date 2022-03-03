<?php

$langCode = Configure::read('Config.langCode');
?>
<?php echo $this->Form->create('Employee', array('action' => 'get_company_role')); ?>
<div class="page-login__panel">
    <div class="absolute-center">
        <div class="box box-login effect7">
            <div class="page-login__logo" style="margin-bottom: 15px;">
                <a href="<?php echo $html->url('/') ?>"><img src="/img_z0g/logo-big.svg" alt="zero gravity"/></a>
                <p style="text-align: center; margin-top: 30px; font-size: 0.9em;"><?php  __("Welcome $fullname, please choose your company & role to continues")?></p>
                <?php echo $session->flash();?>
            </div>
            <?php if ($employee_info["Employee"]["is_sas"] == 1) { ?>
                <p style="margin-left: 30%;">
                    <input type="checkbox" id="admin_is_sas" value="1" name="data[Employee][is_sas]" class="checkbox" />
                    <label class="checkbox__label" for="admin_is_sas">
                        <!--Put the label in a span with the "checkbox__text" class-->
                        <span class="checkbox__text"><?php __("Admin SAS") ?></span>
                        <span class="checkbox__tick"></span>
                    </label>
                </p>
            <?php } ?>
            <?php $flash = $session->flash(); if( $flash ): ?>
            <div class="form-group">
                <?php echo $flash ?>
            </div>
            <?php endif ?>
            <div class="select" style="width: 65%; margin-bottom: -30px;">
              <select name="data[Employee][company_role]" id="company_role" style="width: 148%; margin-bottom: 40px;">
                <option value=""><?php __("Choose your company role"); ?></option>
                <?php
        		if($employee_info["Employee"]["is_sas"]==1) {
        			if (!empty($companies)) {
        				if (count($companies) == 2)
        					$select_c = "";
        				else
        					$select_c = "selected=\"selected\"";
        				foreach ($companies as $key=>$ref) {
        					echo "<option $select_c value='" . $key . "'>" . $ref . "</option>";
        				}
        			}
        		} else {
        			if (!empty($employee_all_info)) {
        				if (count($employee_all_info) == 2)
        					$select_c = "";
        				else
        					$select_c = "selected=\"selected\"";
        				foreach ($employee_all_info as $ref) {
        					echo "<option $select_c value='" . $ref['CompanyEmployeeReference']['id'] . "'>" . $ref['Company']['company_name'] . " - " . $ref['Role']['desc'] . "</option>";
        				}
        			}
        		}
                ?>
              </select>
            </div>
            <button class="page-login__login-btn btn btn--fancy"><?php __("Continue") ?></button>
            <a href="<?php echo $this->Html->url('/logout'); ?>" class="page-login__lost-password" style="margin-left: 42%;"><?php __("Sign out") ?></a>
        </div>
    </div>
</div>
<?php echo $this->Form->end() ?>
<?php echo $html->script('jquery.valid'); ?>
<script>
    $("#admin_is_sas").click(function(){
        if ($(this).attr("checked")){
            $("#company_role").val("");
            $("#company_role").attr("disabled", "disabled");
        }else{
            $("#company_role").removeAttr("disabled");
        }
    })
</script>
