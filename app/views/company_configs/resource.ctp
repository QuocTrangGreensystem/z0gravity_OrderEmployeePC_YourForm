<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
 .slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}.wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}.wd-input-select label{display:inline-block;min-width:375px;}.wd-save{float:left;background:url(../img/front/bg-submit-save.png) no-repeat left top;cursor:pointer;height:33px;width:82px;border:none;font-size:0;}
#avatar_assistant {
	width: 80px;
}
.wd-list-project .wd-tab .wd-content label {
	margin-top:10px;
}
<?php 
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
}
?>
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
                                            echo $this->Form->create('Project', array('url' => array('controller' => 'company_configs', 'action' => 'resourceSettings')));
                                        ?>
                                        <div class="wd-input-select">
											<label><?php echo __('Display external resources in organigram chart', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('display_external_in_organization', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'display_external_in_organization',
													'onchange' => "editMe('display_external_in_organization', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['display_external_in_organization'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
                                        <div class="wd-input-select">
											<label><?php echo __('Manage multiple resource', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('manage_multiple_resource', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'manage_multiple_resource',
													'onchange' => "editMe('manage_multiple_resource', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['manage_multiple_resource'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
                                        <div class="wd-input-select">
											<label><?php echo __('Display picture of manager (organigram screen)', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('display_picture_manager', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'display_picture_manager',
													'onchange' => "editMe('display_picture_manager', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['display_picture_manager'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
                                        <div class="wd-input-select">
											<label><?php echo __('Display picture of all resources', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('display_picture_all_resource', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'display_picture_all_resource',
													'onchange' => "editMe('display_picture_all_resource', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['display_picture_all_resource'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
                                        <div class="wd-input-select">
											<label><?php echo __('Display my assistant', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('display_my_assistant', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'display_my_assistant',
													'onchange' => "editMe('display_my_assistant', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['display_my_assistant'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
                                        <div class="wd-input-select">
											<label><?php echo __('Avatar of assistant', true)?></label>
											<?php
												$option = array(__('Female', true), __('Male', true));
												echo $this->Form->input('avatar_assistant', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'avatar_assistant',
													'onchange' => "editMe('avatar_assistant', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['avatar_assistant'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<?php if(!empty($isAdminSas)){ ?>
                                        <div class="wd-input-select">
											<label><?php echo __('New screen resource', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('new_edit_employee', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'new_edit_employee',
													'onchange' => "editMe('new_edit_employee', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['new_edit_employee'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
										<?php } ?>
                                        <div class="wd-input-select">
											<label><?php echo __('Share a dashboard', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('share_a_dashboard', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'share_a_dashboard',
													'onchange' => "editMe('share_a_dashboard', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['share_a_dashboard'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
										</div>
                                        <div class="wd-input-select">
											<label><?php echo __('Manage project setting', true)?></label>
											<?php
												$option = array(__('No', true), __('Yes', true));
												echo $this->Form->input('manage_project_setting', array(
													'div' => false,
													'label' => false,
                                                    'id' => 'manage_project_setting',
													'onchange' => "editMe('manage_project_setting', this.value);",
													"class" => "wd-select-box",
													"default" => &$companyConfigs['manage_project_setting'],
													"options" => $option,
													"rel" => "no-history"
												));
											?>
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
</script>
