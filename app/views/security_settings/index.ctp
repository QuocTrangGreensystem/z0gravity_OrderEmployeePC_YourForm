<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-select-box{
        border: #d8d8d8 solid 1px;
        padding: 6px;
    }
    .wd-input-select{
        padding-left: 40px;
        padding-top: 10px;
        font-weight: bold;
    }
    .wd-input-select label{
        display: inline-block;
        min-width: 375px;
    }
    .list {
        margin: 10px 0 0 20px;
        list-style: circle !important;
        font-weight: normal;
    }
    .list li {
        padding: 3px 0;
    }
	.wd-input-select:after {
		content: '';
		display: block;
		clear: both;
	}
	.wd-input-select label{
		margin-top: 10px;
	}
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
                                <div class="wd-table" id="project_container">
                                    <div id="wd-select">
                                        <?php   
                                            $datas = array(
                                                    0 => __("No", true),
                                                    1 => __("Yes", true),
                                                );
                                            echo $this->Form->create('setSecurity', array('url' => array('controller' => 'security_settings', 'action' => 'index')));
                                        ?>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Authorize save login/password in cookie", true)?></label>
                                            <?php
                                                echo $this->Form->input('cookie', array(
                                                    'div' => false, 
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setSecurity['SecuritySetting']['cookie'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Authorize multi click", true)?></label>
                                            <?php
                                                echo $this->Form->input('multi_click', array(
                                                    'div' => false, 
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setSecurity['SecuritySetting']['multi_click'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Enable password complexity check", true)?></label>
                                            <?php
                                                echo $this->Form->input('complex_password', array(
                                                    'div' => false, 
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    'id' => 'complex-password',
													'style' => 'width: 80px;',
                                                    "default" => !empty($setSecurity['SecuritySetting']['complex_password']) ? $setSecurity['SecuritySetting']['complex_password'] : 0,
                                                    "options" => array(
                                                            1 => __("Enable", true),
                                                            0 => __("Disable", true)
                                                        )
                                                    ));
                                            ?>
                                        </div>
										
                                        <div id="extend-complex" style="display: none">
                                            <div class="wd-input-select">
                                                <label><?php echo __("Password minimum length", true) ?></label>
                                                <?php
                                                    echo $this->Form->input('password_min_length', array(
                                                        'div' => false, 
                                                        'label' => false,
                                                        "class" => "wd-select-box",
                                                        "value" => !empty($setSecurity['SecuritySetting']['password_min_length']) ? $setSecurity['SecuritySetting']['password_min_length'] : '',
														"style" => "margin-top:10px;"
                                                    ));
                                                ?>
                                            </div>
                                            <div class="wd-input-select">
                                                <label style="margin-top:0px;width:400px;" ><?php echo __("Password should not contain the user's first or last name", true) ?></label>
                                                <?php
                                                    echo $this->Form->checkbox('password_ban_list', array(
                                                        'div' => false, 
                                                        'label' => false,
                                                        'checked' => !empty($setSecurity['SecuritySetting']['password_ban_list']) && $setSecurity['SecuritySetting']['password_ban_list'] == 1 ? true : false
                                                    ));
                                                ?>
                                            </div>
                                            <div class="wd-input-select">
                                                <label style="margin-top:0px;width:400px;" ><?php echo __("Password must contain characters from these categories:", true) ?></label>
                                                <?php
                                                    echo $this->Form->checkbox('password_special_characters', array(
                                                        'div' => false, 
                                                        'label' => false,
                                                        'checked' => !empty($setSecurity['SecuritySetting']['password_special_characters']) && $setSecurity['SecuritySetting']['password_special_characters'] == 1 ? true : false
                                                    ));
                                                ?>
                                                <ul class="list">
                                                    <li>​<?php __('Uppercase letters (A-Z)') ?></li>
                                                    <li><?php __('Lowercase letters (a-z)') ?></li>
                                                    <li><?php __('Base 10 digits (0-9)') ?></li>
                                                    <li><?php __('Non-alphanumeric characters (for example, !, $, #, %)') ?></li>
                                                </ul>
                                            </div>
											
                                        </div>
										<div class="wd-input-select">
										<label><?php echo __('Delete archived project with consumed', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('delete_archived_project_consumed', array(
													'div' => false,
													'label' => false,
													'id' => 'delete_archived_project_consumed',
													'onchange' => "editMe('delete_archived_project_consumed', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['delete_archived_project_consumed'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select">
										<label><?php echo __('Projects import from models', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('can_import_model_project', array(
													'div' => false,
													'label' => false,
													'id' => 'can_import_model_project',
													'onchange' => "editMe('can_import_model_project', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['can_import_model_project']) ? $companyConfigs['can_import_model_project'] : 0,
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select">
										<label><?php echo __('Projects import', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('can_import_project', array(
													'div' => false,
													'label' => false,
													'id' => 'can_import_project',
													'onchange' => "editMe('can_import_project', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['can_import_project']) ? $companyConfigs['can_import_project'] : 0,
													
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select">
										<label><?php echo __('Import Tasks', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('can_import_task', array(
													'div' => false,
													'label' => false,
													'id' => 'can_import_task',
													'onchange' => "editMe('can_import_task', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['can_import_task']) ? $companyConfigs['can_import_task'] : 0,
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select">
										<label><?php echo __('Import Tasks by Excel', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('can_import_task_by_excel', array(
													'div' => false,
													'label' => false,
													'id' => 'can_import_task_by_excel',
													'onchange' => "editMe('can_import_task_by_excel', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['can_import_task_by_excel']) ? $companyConfigs['can_import_task_by_excel'] : 0,
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select">
										<label><?php echo __('SSO', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('enable_sso', array(
													'div' => false,
													'label' => false,
													'id' => 'enable_sso',
													'onchange' => "editMe('enable_sso', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['enable_sso']) ? $companyConfigs['enable_sso'] : 0,
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select">
										<label><?php echo __('Manage field on project detail', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('can_manage_your_form_field', array(
													'div' => false,
													'label' => false,
													'id' => 'can_manage_your_form_field',
													'onchange' => "editMe('can_manage_your_form_field', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['can_manage_your_form_field']) ? $companyConfigs['can_manage_your_form_field'] : 0,
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										
										<div class="wd-input-select">
											<?php
												$option = array(
													0 => __('No two-factor authentication', true), 
													'send_mail' => __('Send the authentication code by e-mail', true),
													// 'microsoft_auth' => __('Microsoft Authenticator', true),
													// 'google_auth' => __('Google Authenticator', true),
													'2fa_app' => __('Microsoft/Google Authenticator', true),
												);
												echo $this->Form->input('company_two_factor_auth', array(
													'div' => false,
													'label' => false,
													'id' => 'company_two_factor_auth',
													'onchange' => "editMe('company_two_factor_auth', this.value);",
													"class" => "wd-select-box wd-select-no-title",
													"default" => &$companyConfigs['company_two_factor_auth'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<div class="wd-input-select wd-input-otp-duration" style="display: <?php echo !empty($companyConfigs['company_two_factor_auth']) ? 'block' : 'none'; ?>">
											<label><?php echo __('OTP duration', true)?></label>
											<?php
												$option = array(
													'day' => __('1 day', true), 
													'week' => __('1 week', true),
													'month' => __('1 month', true),
												);
												echo $this->Form->input('otp_duration_expired', array(
													'div' => false,
													'label' => false,
													'id' => 'otp_duration_expired',
													'onchange' => "editMe('otp_duration_expired', this.value);",
													"class" => "wd-select-box",
													"default" => !empty($companyConfigs['otp_duration_expired']) ? $companyConfigs['otp_duration_expired'] : 'week',
													"options" => $option,
													"rel" => "no-history",
													"style"=>"width: 80px;"
												));
											?>
										</div>
                                        <div class="wd-submit" style="margin-left: 38px; margin-top: 10px;">
                                            <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                        </div>
                                        <?php
                                            echo $this->Form->end();
                                        ?> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
function editMe(field,value) {
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
			if(value == 0){
				$('h3.head-title.'+field).hide();
			}else{
				$('h3.head-title.'+field).show();
			}
			if( field == 'company_two_factor_auth'){
				if(value == 'send_mail' ) $('.wd-input-otp-duration').show();
				else $('.wd-input-otp-duration').hide();
			}
			$('#loadingElm').remove();
		}
	});
}
(function($){
    var el = $('#complex-password').change(function(){
        var val = $(this).val();
        if( val == '0' ){
            $('#extend-complex').hide().find('input').prop('disabled', true);
        } else $('#extend-complex').show().find('input').prop('disabled', false);

    });
    var val = el.val();
    if( val == '0' ){
        $('#extend-complex').hide().find('input').prop('disabled', true);
    } else $('#extend-complex').show().find('input').prop('disabled', false);
	
	
	
})(jQuery);
</script>