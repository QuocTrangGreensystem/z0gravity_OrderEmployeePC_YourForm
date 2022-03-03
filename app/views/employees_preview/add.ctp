<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->css('dropzone.min'); ?>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('strength'); ?>
<?php echo $html->css(array('context/jquery.contextmenu')); ?>
<?php echo $html->css(array('preview/employee-edit')); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php echo $html->script('jquery.form'); ?>
<?php echo $html->script('jquery.ui.touch-punch.min'); ?>
<?php echo $html->script('dropzone.min'); 
	$company_id = $employee_info['Company']['id'];
	// ob_clean();
	// debug($default_user_profile);
	// exit;
?>

<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content wd-user-edit">
            <div class="wd-list-project">
                <div class="wd-title">
					<div class="wd-row">
						<div class=" wd-col wd-col-md-4 header-title">
							<div class="heading-back"><a href="javascript:window.history.back();" ><i class="icon-arrow-left"></i><span><?php __('Back');?></span></a></div>
							<h2 class="wd-t1">
								<?php echo __("New Employee", true); ?>
								
							</h2>
						</div>
						<div class=" wd-col wd-col-md-8 wd-text-right">
							<div class="wd-title-actions">
								<a href="" id="reset" class="wd-btn-reset">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a-reset{fill:none;}.b-reset{fill:#bcbcbc;fill-rule:evenodd;}</style></defs><rect class="a-reset" width="24" height="24"/><path class="b-reset" d="M10.873,18.575l0,.89c0,.484-.35.675-.776.426L7.163,18.182a.48.48,0,0,1,0-.9l2.957-1.744c.424-.25.768-.062.766.423l0,.8A7.044,7.044,0,0,0,16.2,10a6.936,6.936,0,0,0-2.715-5.488c-.885-.652.465-1.967,1.387-1.156A8.653,8.653,0,0,1,18,10,8.829,8.829,0,0,1,10.873,18.575ZM10.837,2.759,7.908,4.468c-.427.249-.775.058-.776-.426l0-.813A7.047,7.047,0,0,0,1.8,10a6.931,6.931,0,0,0,2.69,5.467c.91.673.01,2.427-1.437,1.113A8.65,8.65,0,0,1,0,10,8.83,8.83,0,0,1,7.12,1.419l0-.886c0-.485.342-.673.766-.423l2.957,1.744A.48.48,0,0,1,10.837,2.759Z" transform="translate(3 1.999)"/></svg>
								</a>
								<a href="javascript:void(0);" class="wd-btn-save"><?php echo __('Save', true) ?></a>
							</div>
						</div>
					</div>
                </div>
				<div class="wd-panel">
					<div class="wd-container" id="wd-fragment-1">
						<?php echo sprintf($this->Session->flash(), '<a href="javascript:;" onclick="$(\'#list-dialog\').dialog(\'open\');">Project/activity list</a>.'); ?>
						<?php
						echo $this->Form->create('Employee', array(
							'id' => 'EmployeeAddForm', 
							'url' => array('controller' => 'employees_preview', 
								'action' => 'add'
							),'type' => 'POST'
							));
						
						App::import("vendor", "str_utility");
						$str_utility = new str_utility();
						?>
						<fieldset>
						<div class="wd-row">
							<div class=" wd-col wd-col-md-4">
								<div class="wd-left-inner">
									<div class="wd-row">
										<div class=" wd-col wd-col-md-12">
											<div class="wd-field">
												<p class="wd-input-title wd-no-margin-top"><?php __("First Name"); ?></p>
												<?php echo $this->Form->input('first_name', array('div' => false, 'label' => false)); ?>
											</div>
											<div class="wd-field">
												<p class="wd-input-title"><?php __("Last name"); ?></p>
												<?php echo $this->Form->input('last_name', array('div' => false, 'label' => false)); ?>	
											</div>
										</div>
									</div>
									<div class="wd-row">
										<div class=" wd-col wd-col-md-12">
										<div class="wd-field">
											<p class="wd-input-title"><?php __("Email"); ?></p>
											<?php echo $this->Form->input('email', array('div' => false, 'label' => false, 'autocomplete' => 'off')); ?>
										</div>
										<div class="wd-field">
											<p class="wd-input-title"><?php __("Password"); ?></p>
											<input type="password" style="display: none;" id="password-breaker" name="password-breaker" />
											<?php echo $this->Form->input('password', array('div' => false, 'rel' => 'no-history', 'label' => false, "value" => $default_user_profile['password'])); ?>
										</div>
										
										<!-- Start Role --> 
											<div class="wd-field">
												<p class="wd-input-title"><?php __("Role") ?></p>
												<select name="data[Employee][role_id]" id="EmployeeRoleId">
													<option value=""><?php echo __("---Select role---") ?></option>
														<?php
														if(!empty($profile_name)){
															foreach ($profile_name as $key => $value) {
																$roles['profile_' . $key] = $value;
															}
														}
														foreach ($roles as $key => $value) {
															if( $is_pm && ($key == 2 || $key == 'ws') )continue;
															
															if($key == 3){
																echo "<option selected value='" . $key . "'>" . __($value, true) . "</option>";
															}else{
																echo "<option value='" . $key . "'>" . __($value, true) . "</option>";
															}
														}
													?>
												</select>
												
											</div>
											<div class="wd-row">
												<div class="wd-col wd-col-md-6">
													<div class="wd-field">
														 <p class="wd-input-title"><?php __("Work phone") ?></p>
														<?php  echo $this->Form->input('work_phone', array('div' => false, 'label' => false));  ?>
													</div>
												</div>
												<div class="wd-col wd-col-md-6">
													<p class="wd-input-title" style="color: #FFF"><?php __("Actif") ?></p>
													<div class="wd-field wd-field-switch">
														<?php echo $this->Form->input('actif', array(
																'type' => 'checkbox',
																'class' => 'hidden',
																'label' => '<span class="wd-btn-switch"><span></span></span>',
																'checked'=> true,
																'div' => array(
																	'class' => 'wd-input wd-checkbox-switch',
																	'title' => __('Actif',true),
																),
																'type' => 'checkbox', 
															));?>
														<p class="wd-input-title"><?php __("Actif") ?></p>
													</div>
												</div>
											</div> 
											<div class="wd-row">
												<div class="wd-col wd-col-md-6">
													<div class="wd-field">
														<p class="wd-input-title"><?php __("Start date") ?></p>
														<?php echo $this->Form->input('start_date', array('type' => 'text', 'div' => false, 'label' => false, 'value' => date('d-m-Y',time()))); ?>
														<span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
													</div>
												</div>
												<div class="wd-col wd-col-md-6">
													<div class="wd-field">
														 <p class="wd-input-title"><?php __("End date") ?></p>
														<?php echo $this->Form->input('end_date', array('type' => 'text', 'div' => false, 'label' => false)); ?>
														<span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
													</div>
												</div>
											</div> 
											<div class="wd-field">
												<p class="wd-input-title"><?php __("ID") ?></p>
												<?php echo $this->Form->input('code_id', array('type' => 'text', 'div' => false, 'label' => false)); ?>
											</div> 
											<!-- Start Profit center -->
											<div class="wd-field">
												<?php
												$arr_new = array();
												$arr_tam = array();
												foreach ($profitCenters as $key => $value) {
													if (strtolower($value) == 'default') {
														$arr_new[$key] = $value;
													} else {
														$arr_tam[$key] = $value;
													}
												}
												?>
												<p class="wd-input-title"><?php echo __('Profit Center') ?></p>
												<?php
												if (!empty($this->data['ProjectEmployeeProfitFunctionRefer']))
													$value = $this->data['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']; else
													$value = "";
												echo $this->Form->input('profit_center_id', array('value' => $value,
													'type' => 'select',
													'div' => false,
													'label' => false,
													"options" => $arr_new + $arr_tam));
												?>
											</div>
											<!-- ticket -->
											<?php if( $enableTicket ) : ?>
											<div class="wd-field">
												<p class="wd-input-title"><?php echo __('Ticket') ?></p>
												<?php
												echo $this->Form->input('ticket_profile_id', array(
													'type' => 'select',
													'div' => false,
													'label' => false,
													"empty" => __("-- Select -- ", true),
													"options" => $listTicketProfiles));
												?>
											</div>
											<?php endif; ?>
											<!-- end -->
										</div>
									</div>
								</div>
							</div>
							<div class=" wd-col wd-col-md-8">
								<div class="wd-block wd-block-1 wd-block-manager">
									<div class="wd-row">
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field wd-field-switch create_a_project">
													<?php echo $this->Form->input('create_a_project', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['create_a_project']) ? true : false,
															'name' => 'data[create_a_project]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Create a project',true),
															), 
															'type' => 'checkbox', 
															'id' => 'create_a_project'
														));?>
													<p class="wd-input-title"><?php __("Create a project") ?></p>
												</div>
												<div class="wd-field wd-field-switch delete_a_project">
													<?php echo $this->Form->input('delete_a_project', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['delete_a_project']) ? true : false,
															'name' => 'data[delete_a_project]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Delete a project',true),
															), 
															'type' => 'checkbox',
															'id' => 'delete_a_project'
														));?>
													<p class="wd-input-title"><?php __("Delete a project") ?></p>
												</div>
												<div class="wd-field wd-field-switch change_status_project">
													<?php echo $this->Form->input('change_status_project', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['change_status_project']) ? true : false,
															'name' => 'data[change_status_project]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Can change the status opportunity/in progress',true),
															), 
															'type' => 'checkbox', 
															'id' => 'change_status_project'
														));?>
													<p class="wd-input-title"><?php __("Can change the status opportunity/in progress") ?></p>
												</div>
												<div class="wd-field wd-field-switch update_your_form">
													<?php echo $this->Form->input('update_your_form', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['update_your_form']) ? true : false,
															'name' => 'data[update_your_form]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Update Your form',true),
															), 
															'type' => 'checkbox', 
															'id' => 'update_your_form'
														));?>
													<p class="wd-input-title"><?php __("Update Your form") ?></p>
												</div>
												<div class="wd-field sl_budget">
													<?php
													$budget_list = array(
														__('Not Display Budget', true),
														__('Readonly Budget', true),
														__('Update Budget', true),
													);
													echo $this->Form->input('sl_budget', array(
														'type' => 'select',
														'div' => false,
														'label' => false,
														"empty" => __("-- Select -- ", true),
														'value' => !empty($default_user_profile['sl_budget']) ? $default_user_profile['sl_budget'] : 0,
														'name' => 'data[sl_budget]',
														'class' => 'wd-budget',
														"options" => $budget_list)); ?>
												</div>
												
											</div>
										</div>
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field wd-field-switch see_all_projects">
													<?php echo $this->Form->input('see_all_projects', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'name' => 'data[see_all_projects]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('See All Project',true),
															), 
															'type' => 'checkbox', 
															'id' => 'see_all_projects'
														));?>
													<p class="wd-input-title"><?php __("See All Project") ?></p>
												</div>
												<div class="wd-field wd-field-switch control_resource">
													<?php echo $this->Form->input('control_resource', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['control_resource']) ? true : false,
															'name' => 'data[control_resource]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Allow managing resources?',true),
															), 
															'type' => 'checkbox', 
															'id' => 'control_resource'
														));?>
													<p class="wd-input-title"><?php __("Allow managing resources?") ?></p>
												</div>
												<?php if(!empty($companyConfigs['display_activity_forecast'])) : ?>
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('can_see_forecast', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['can_see_forecast']) ? true : false,
															'name' => 'data[can_see_forecast]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Can see the forecast',true),
															), 
															'type' => 'checkbox', 
															'id' => 'can_see_forecast'
														));?>
													<p class="wd-input-title"><?php __("Can see the forecast") ?></p>
												</div>
												<?php endif; ?>
												<?php if(!empty($enabled_menus['communications'])) : ?>
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('can_communication', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['can_communication']) ? true : false,
															'name' => 'data[can_communication]',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Can communicate communication',true),
															), 
															'type' => 'checkbox', 
															'id' => 'can_communication'
														));?>
													<p class="wd-input-title"><?php __("Can communicate communication") ?></p>
												</div>
												<?php endif; ?>
											</div>
										</div>
									</div> 
								</div>
								<div class="wd-block wd-block-2">
									<div class="wd-row">
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('email_receive', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['email_receive']) ? true : false,
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Authorize z0 Gravity email',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title"><?php __("Authorize z0 Gravity email") ?></p>
												</div>
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('activate_copy', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['activate_copy']) ? true : false,
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Activate COPY in timesheet',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title wd-input-title-duplicate"><?php __("Activate COPY in timesheet") ?></p><a href="javascript:void(0)" style="vertical-align: middle; margin-left: 10px" class="duplicate-timesheet" title="<?php __('Copy Forecast')?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><defs><style>.a-copy{fill:none;}.b-copy{fill:#eecd9d;fill-rule:evenodd;}</style></defs><rect class="a-copy" width="16" height="16"/><path class="b-copy" d="M13.125,0H4.375A.875.875,0,0,0,3.5.875V3.5H.875A.875.875,0,0,0,0,4.375v8.75A.875.875,0,0,0,.875,14h8.75a.875.875,0,0,0,.875-.875V10.5h2.625A.875.875,0,0,0,14,9.625V.875A.875.875,0,0,0,13.125,0ZM9.188,12.688H1.313V4.813H3.5V9.625a.875.875,0,0,0,.875.875H9.188Zm3.5-3.5H4.813V1.313h7.875Z" transform="translate(1 1)"/></svg></a>
												</div>
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('is_enable_popup', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['is_enable_popup']) ? true : false,
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Enable popup',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title"><?php __("Enable popup") ?></p>
												</div>
											</div>
										</div>
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('auto_timesheet', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['auto_timesheet']) ? true : false,
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Auto validate timesheet',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title"><?php __("Auto validate timesheet") ?></p>
												</div>
												
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('auto_absence', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['auto_absence']) ? true : false,
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Auto validate absence',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title"><?php __("Auto validate absence") ?></p>
												</div>												
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('auto_by_himself', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($default_user_profile['auto_by_himself']) ? true : false,
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Auto validate by himself',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title"><?php __("Auto validate by himself") ?></p>
												</div>
												
											</div>
										</div>
									</div> 
								</div>
								<div class="wd-block wd-block-3">
									<div class="wd-row">
										<div class="wd-col wd-col-md-3">
											<div class="wd-block-inner">
												<div class="wd-field">
													<p class="wd-input-title"><?php __("Type of Contract") ?></p>
													<?php
													echo $this->Form->input('contract_type_id', array(
														'type' => 'select',
														'div' => false,
														'label' => false,
														"empty" => __("-- Select -- ", true),
														"options" => $contract_types)); ?>
												</div>
											</div>
										</div>
										<div class="wd-col wd-col-md-5">
											<div class="wd-block-inner">		
												<p class="wd-input-title"><?php __("External") ?></p>
												<?php $opt = array(__('Internal', true), __('External', true));
													if($manage_multiple_resource){
														$opt[] = __('Multiple resource', true);
													}
													echo $this->Form->input('external', array(
														'options' => $opt,
														'div' => false,
														'label' => false,
													));?>
											</div>
										</div>
										<div class="wd-col wd-col-md-4">
											<div class="wd-block-inner">
												<p id="label-external" class="wd-input-title" class="enterpriseCompany"><?php __("External company") ?></p>
												<?php echo $this->Form->input('external_id', array("class" => 'enterpriseCompany', "empty" => __("-- Select -- ", true), 'options' => $externals, 'div' => false, 'label' => false)); ?>
											</div>
										</div>
									</div> 
								</div>
								<div class="wd-block wd-block-4">
									<div class="wd-row">
										<div class="wd-col wd-col-md-3">
											<div class="wd-block-inner">
												<div class="wd-field">
													<p class="wd-input-title"><?php echo __('Capacity') ?>/<?php echo __('Year') ?></p>
													 <?php echo $this->Form->input('capacity_by_year', array(
															'type' => 'text',
															'div' => false,
															'value' => !empty($default_user_profile['capacity_by_year']) ? $default_user_profile['capacity_by_year'] : '',
															'label' => false)); ?>
												</div>
												<div class="wd-field">
													<p class="wd-input-title"><?php __("Average Daily Rate") ?></p>
													 <?php  echo $this->Form->input('tjm', array(
															'type' => 'text',
															'value' => !empty($default_user_profile['tjm']) ? $default_user_profile['tjm'] : '',
															'div' => false,
															'label' => false)); ?>
												</div>
											</div>
										</div>
										<div class="wd-col wd-col-md-9">
											<div class="wd-row">
												<div class="wd-col wd-col-md-6">
													<div class="wd-block-inner">
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID3") ?></p>
															<?php echo $this->Form->input('id3', array('div' => false, 'label' => false)); ?>
														</div>
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID4") ?></p>
															<?php echo $this->Form->input('id4', array('div' => false, 'label' => false)); ?>
														</div>
													</div>
												</div>
												<div class="wd-col wd-col-md-6">
													<div class="wd-block-inner">
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID5") ?></p>
															 <?php echo $this->Form->input('id5', array('div' => false, 'label' => false)); ?>
														</div>
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID6") ?></p>
															<?php echo $this->Form->input('id6', array('div' => false, 'label' => false)); ?>
														</div>
													</div>
												</div>
												<div class="wd-col wd-col-md-12">
													<div class="wd-block-inner">		
														<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID2") ?></p>
														<?php echo $this->Form->input('identifiant', array('type' => 'text', 'div' => false, 'label' => false)); ?>
													</div>
												</div>
											</div>
										</div>
										
									</div> 
								</div>
								<div class="wd-block wd-block-5">
									<div class="wd-row">
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field">
													<p class="wd-input-title"><?php __("City") ?></p>
													<?php echo $this->Form->input('city_id', array('div' => false, "options" => $cities, 'label' => false)); ?>
												</div>
											</div>
										</div>
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field">
													<p class="wd-input-title"><?php __("Country"); ?></p>
													<?php echo $this->Form->input('country_id', array('div' => false, 'label' => false)); ?>
							
												</div>
											</div>
										</div>
										
									</div> 
								</div>
						   </div>
						</div>
						
						</fieldset>
						<?php echo $this->Form->end() ?>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $validation->bind("Employee", array('form' => '#EmployeeAddForm')); ?>
<?php echo $html->script('validateDate'); ?>
<script>
	var default_user_profile = <?php echo json_encode($default_user_profile);?>;
    $("select[name='data[Employee][project_function_id]']").multiSelect({noneSelected: 'Select skills', oneOrMoreSelected: '*', selectAll: false });
    function isNotEmpty(elementId,cpr){
        var date = $("#"+elementId).val();
        if(date==''){
            var endDate = $("#"+elementId);
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            endDate.addClass("form-error");
            switch (elementId) {
                case "EmployeeRoleId":
                    parentElem.append('<div class="error-message2"><?php echo h(__("This field is not blank.", true)); ?></div>');
                    break;
                case "EmployeeCompanyId":
                    parentElem.append('<div class="error-message1"><?php echo h(__("This field is not blank.", true)); ?></div>');
                    break;
                case "EmployeeProfitCenterId":
                    parentElem.append('<div class="error-message"><?php echo h(__("This field is not blank.", true)); ?></div>');
                    break;
                default:
                    parentElem.append('<div class="error-message"><?php echo h(__("This field is not blank.", true)); ?></div>');
                }
                return false;
            }
            return true;
        }
        function reset_form(){
            removeMessage();
            $("input").val('');
            $("#EmployeeCompanyId").val('');
            $("#EmployeeRoleId").val('');
            $("#EmployeeCityId").attr({disabled:"true"});
            $("#EmployeeCountryId").attr({disabled:"true"});
            $("#EmployeeCityId").html("<option></option>");
            $("#EmployeeCountryId").html("<option></option>");
        }
        $('#btnSave').click(function(){
            removeMessage();
            var v1=false,v2=false,v3=false,v4=false,v5=true;
            v1=isNotEmpty('EmployeeCompanyId');
            v2=isNotEmpty('EmployeeRoleId');
            v3=isNotEmpty('EmployeeFirstName');
            v4=isNotEmpty('EmployeeLastName');
            v6=true;
           
            if($("#EmployeeStartDate").val()){
                var endDate = $("#EmployeeStartDate");
                var parentElem = endDate.parent();
                if(!isDate('EmployeeStartDate')){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("Invalid Date (Valid format is dd-mm-yyyy).", true)); ?></div>');
                    v5= false;
                }else if(isDate('EmployeeEndDate') && compareDate('EmployeeStartDate','EmployeeEndDate') > 0 ){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("The end date must be greater than start date.", true)); ?></div>');
                    v5= false;
                }
            }
            if(v5 && $("#EmployeeEndDate").val()){
                var endDate = $("#EmployeeEndDate");
                var parentElem = endDate.parent();
                if(!isDate('EmployeeEndDate')){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("Invalid Date (Valid format is dd-mm-yyyy).", true)); ?></div>');
                    v5= false;
                }else if(isDate('EmployeeStartDate') && compareDate('EmployeeStartDate','EmployeeEndDate') > 0 ){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("The end date must be greater than start date.", true)); ?></div>');
                    v5= false;
                }
            }
            <?php if( $security['SecuritySetting']['complex_password']): ?>
            var valid = $('#EmployeePassword').data('plugin_strength').valid;
            if( !valid ){
                var endDate = $("#EmployeePassword");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                endDate.addClass("form-error");
                return false;
            }
            <?php endif ?>
            return v1&&v2&&v3&&v4&&v5&&v6;
        });

        function removeMessage(){
            $("#flashMessage").hide();
            $('div.error-message').remove();
            $('div.error-message1').remove();
            $('div.error-message2').remove();
            $('div.error-message3').remove();
            $("div.wd-input,input,select").removeClass("form-error");
            check_phase_exist = false;
        }
        $("#EmployeeCompanyId").change(function(){
            var id = $(this).val();
            $("#EmployeeCityId").removeAttr('disabled');
            $("#EmployeeCountryId").removeAttr('disabled');
            $.ajax({
                url: '<?php echo $html->url('/employees/get_city/') ?>' + id,
                beforeSend: function() { $("#EmployeeCityId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#EmployeeCityId").html(data);
                }
            });
            $.ajax({
                url: '<?php echo $html->url('/employees/get_country/') ?>' + id,
                beforeSend: function() { $("#EmployeeCountryId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#EmployeeCountryId").html(data);
                }
            });
            $.ajax({
                url: '<?php echo $html->url('/employees/get_profit_center/') ?>' + id,
                beforeSend: function() { $("#EmployeeProfitCenterId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#EmployeeProfitCenterId").html(data);
                }
            });
            $.ajax({
                url: '<?php echo $html->url('/employees/get_skill/') ?>' + id,
                beforeSend: function() {
                    $('#skillBox').html('');
                },
                success: function(data) {
                    $('#skillBox').append(data);
                    $("select[name='data[Employee][project_function_id]']").multiSelect({noneSelected: 'Select skills', oneOrMoreSelected: '*', selectAll: false });
                    $('#EmployeeProjectFunctionId').find('span').text('<?php __('Select skills'); ?>');
                    $('#skillBox .multiSelectOptions label').each(function(){
                        if($(this).find('input').is(':checked')){
                            $(this).removeAttr('class');
                            $(this).find('input').removeAttr('checked');
                        }
                    });
                }
            });
        });

        $(document).ready(function () {
             $("#EmployeeStartDate, #EmployeeEndDate").datepicker({
				dateFormat      : 'dd-mm-yy'
			});
            var company_id = <?php echo json_encode($company_id);?>;
            if(company_id=="") {
                $("#EmployeeCityId").html("<option></option>");
                $("#EmployeeCountryId").html("<option></option>");
            } else {
                $("#EmployeeCityId").removeAttr('disabled');
                $("#EmployeeCountryId").removeAttr('disabled');
                $.ajax({
                    url: '<?php echo $html->url('/employees/get_city/') ?>' + company_id,
                    beforeSend: function() { $("#EmployeeCityId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#EmployeeCityId").html(data);
                    }
                });
                $.ajax({
                    url: '<?php echo $html->url('/employees/get_country/') ?>' + company_id,
                    beforeSend: function() { $("#EmployeeCountryId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#EmployeeCountryId").html(data);
                    }
                });
            }
        });
        $('#EmployeeWorkPhone').keypress(function(){
            var rule = /^\+?([0-9]*)$/;
            var x=$.trim($('#EmployeeWorkPhone').val());
            $('#EmployeeWorkPhone').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("Number or +", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeMobilePhone').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeMobilePhone').val());
            $('#EmployeeMobilePhone').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Mobile phone must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeHomePhone').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeHomePhone').val());
            $('#EmployeeHomePhone').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeHomePhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Home phone must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeHomePhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeFax').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeFax').val());
            $('#EmployeeFax').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeFax");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Fax number must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeFax");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeePostCode').change(function(){
            var rule = /^([0-9.]*)$/;
            var x=$.trim($('#EmployeePostCode').val());
            $('#EmployeePostCode').val(x);
            $('div.error-message3').remove();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeePostCode");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message3">'+"<?php echo h(__("The Postcode must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeePostCode");
                fomrerror.removeClass("form-error");
                $('div.error-message3').remove();
            }
        });
        $("#EmployeeCodeId").change(function(){
            var code_id = $.trim($(this).val());
            //code_id = '';
            if(code_id){
                $.ajax({
                    url:  '<?php echo $html->url('/employees/check_code_id/') ?>' + code_id,
                    beforeSend: function() {
                        $("#EmployeeCodeId").after("<span id='check' style='line-height:24px'><?php echo h(__('Checking...', true)) ?></span>");
                        $("#EmployeeCodeId").removeClass("form-error");
                        $("#EmployeeCodeId").parent().find(".error-message").remove();
                        $("#EmployeeCodeId .error").remove();
                    },
					dataType: 'json',
                    success: function(data) {
                        if(data){
                            var parentElem = $("#EmployeeCodeId").parent();
                            $("#EmployeeCodeId").addClass("form-error");
                            parentElem.addClass("error");
                            var alert_html = '';
							$.each(data, function(id, name){
								alert_html += '<a href ="/employees_preview/edit/'+ id +'/'+ <?php echo json_encode($company_id) ?> +'" title="'+ name +'" target="_blank" >'+ name +'</a>'
							});
                            parentElem.addClass("error");
                            parentElem.append('<div class="error-message wd-error-message-code">'+ alert_html +'</div>');
                        }else{
                            $('#btnSave').removeAttr('disabled');
                        }
                        $("#check").remove();
                    }
                })
            }
        });
        $('#EmployeeActif').change(function(){
            var _sl = $(this).find('option:selected').val();
            if(_sl == 0){
                var currentDate = <?php echo json_encode(date('d-m-Y', time()));?>;
                $('#EmployeeEndDate').val(currentDate);
            }
        });

<?php
if( $security['SecuritySetting']['complex_password'] ):
    $rules = array(
        sprintf(__('Minimum characters of %s', true), $security['SecuritySetting']['password_min_length'])
    );
    if( $security['SecuritySetting']['password_special_characters'] ){
        $messages = array_fill(0, 3, '<img src="'.$html->url('/img/test-fail-icon.png').'" alt="">');
        $messages[] = '<img src="'.$html->url('/img/test-pass-icon.png').'" alt="">';
        $rules[] = __('Uppercase letters (A-Z)', true);
        $rules[] = __('Lowercase letters (a-z)', true);
        $rules[] = __('Base 10 digits (0-9)', true);
        $rules[] = __('Non-alphanumeric characters (for example, !, $, #, %)', true);
        $valid = array(0, 0, 0, 1);
    } else {
        $messages = array_fill(0, 4, '<img src="'.$html->url('/img/test-pass-icon.png').'" alt="">');
        $valid = array(1, 1, 1, 1);
    }
?>
    //init complexify
    var el = $('#EmployeePassword').strength({
        minLength : <?php echo $security['SecuritySetting']['password_min_length'] ?>,
        text : <?php echo json_encode($messages) ?>,
        valid : <?php echo json_encode($valid) ?>,
        textPrefix : '',
        lengthError : '<?php printf(__('At least %s characters', true), $security['SecuritySetting']['password_min_length']) ?>',
<?php
    if( $security['SecuritySetting']['password_ban_list'] ):
        $rule = __('Password should not contain the user&#39;s first or last name', true);
        $rules[] = $rule;
?>
        banListError : '<?php echo $rule ?>',
        banList : function(){
            var list = [],
                fn = $('#EmployeeFirstName').val(),
                ln = $('#EmployeeLastName').val();
            if( fn.length )list.push(fn);
            if( ln.length )list.push(ln);
            return list;
        },
<?php endif ?>
        validWhenEmpty : true,
        emptyError: '<?php printf(__('At least %s characters', true), $security['SecuritySetting']['password_min_length']) ?>'
    });
    var _tit = <?php echo json_encode(__('Password rules', true));?>;
    el.tooltip({
        maxHeight : 500,
        maxWidth : 400,
        type : ['top','left'],
        content: '<div class="password-rule"><h4>' + _tit + '</h4><ul><li>- ' + (<?php echo json_encode($rules) ?>).join('</li><li>- ') + '</li></ul></div>'
    });
<?php
endif;
?>
    var adminSeeAllProjects = <?php echo json_encode($adminSeeAllProjects);?>;
    var EPM_see_the_budget = <?php echo json_encode($EPM_see_the_budget);?>;
    var list_profile_name = <?php echo json_encode($list_profile_name) ?>;
    var default_user_profile = <?php echo json_encode($default_user_profile) ?>;
    var can_control_resource = function(){
        if( $('#EmployeeRoleId').val() != 2 && $('#EmployeeRoleId').val() != 4){
            $('.wd-block-manager').show();
            $('.see_all_projects').hide();
            $('.see_budget').hide();
            $('.update_budget').hide();
            $('.sl_budget').hide();
            $('.control_resource').show();
            if(!adminSeeAllProjects){
                $('.see_all_projects').show();
            }
            if(EPM_see_the_budget){
                $('.see_budget').show();
                $('.update_budget').show();
                $('.sl_budget').show();
            }
            $('.update_your_form').show();
            $('.create_a_project').show();
            $('.delete_a_project').show();
            <?php if( !empty($enabled_menus['communications'])){?>
				$('.can_communication').show();
			<?php }else{ ?> 
				$('.can_communication').hide();
			<?php } ?> 
            <?php if(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast']){?>
				$('.can_see_forecast').show();
			<?php }else{ ?> 
				$('.can_see_forecast').hide();
			<?php } ?> 
			// neu dang chon role Profile Project Manager thi moi chay cac dong duoi.
            var tempId = $('#EmployeeRoleId').val();
            var _tempId = tempId.split("_");
            if(_tempId[0] && _tempId[1] && _tempId[0] == 'profile'){
                $('.see_budget').hide();
                $('.update_budget').hide();
                $('.sl_budget').hide();
                if(list_profile_name[_tempId[1]]){
                    var value = list_profile_name[_tempId[1]];
					// kiem tra setting trong admin.
                    if(value['can_change_status_project'] && value['can_change_status_project'] == 1){
                        $('#change_status_project').prop('checked', true);
                    } else {
                        $('#change_status_project').prop('checked', false);
                    }
                    if(value['can_create_project'] && value['can_create_project'] == 1){
                        $('#create_a_project').prop('checked', true);
                    } else {
                        $('#create_a_project').prop('checked', false);
                    }
                    if(value['can_delete_project'] && value['can_delete_project'] == 1){
                        $('#delete_a_project').prop('checked', true);
                    } else {
                        $('#delete_a_project').prop('checked', false);
                    }
                    if(value['can_communication'] && value['can_communication'] == 1){
                        $('#can_communication').prop('checked', true);
                    } else {
                        $('#can_communication').prop('checked', false);
                    }
                    if(value['create_resource'] && value['create_resource'] == 1){
                        $('#control_resource').prop('checked', true);
                    } else {
                        $('#control_resource').prop('checked', false);
                    }
					//them dieu kien de uncheck khi tao user voi role Profile Project Manager.
					if(!value['update_your_form']){
						$('#update_your_form').prop('checked', false);
						$('.update_your_form').hide();
					}
                }
            }else{
				if(tempId == 3){
					
					if(default_user_profile['change_status_project'] && default_user_profile['change_status_project'] == 1){
                        $('#change_status_project').prop('checked', true);
                    } else {
                        $('#change_status_project').prop('checked', false);
                    }
                    if(default_user_profile['create_a_project'] && default_user_profile['create_a_project'] == 1){
                        $('#create_a_project').prop('checked', true);
                    } else {
                        $('#create_a_project').prop('checked', false);
                    }
                    if(default_user_profile['delete_a_project'] && default_user_profile['delete_a_project'] == 1){
                        $('#delete_a_project').prop('checked', true);
                    } else {
                        $('#delete_a_project').prop('checked', false);
                    }
                    if(default_user_profile['can_communication'] && default_user_profile['can_communication'] == 1){
                        $('#can_communication').prop('checked', true);
                    } else {
                        $('#can_communication').prop('checked', false);
                    }
                    if(default_user_profile['control_resource'] && default_user_profile['control_resource'] == 1){
                        $('#control_resource').prop('checked', true);
                    } else {
                        $('#control_resource').prop('checked', false);
                    }
					
					if(default_user_profile['update_your_form'] && default_user_profile['update_your_form'] == 1){
                        $('#update_your_form').prop('checked', true);
                    } else {
                        $('#update_your_form').prop('checked', false);
                    }
				}
			}
        } else {
            $('.wd-block-manager').hide();
        }
    }
    $('#EmployeeRoleId').change(can_control_resource);
    can_control_resource();
    /**
     * Show/Hide Enterprise Company
     */
    function showHideEnterprise(val){
        if(val == 1){
            $('.enterpriseCompany').show();
            $('#label-external').show();
            $('#gExternal').addClass('groupExternal');
            $('#gExternal').removeClass('noGroupExternal');
            $('#actif label').css('width', '12%');
            $('#gLableExternal label:first-child').css('margin-top', '10px');
        } else {
            $('.enterpriseCompany').hide();
            $('#label-external').hide();
            $('#gExternal').addClass('noGroupExternal');
            $('#gExternal').removeClass('groupExternal');
            $('#actif label').css('width', '14%');
            $('#gLableExternal label:first-child').css('margin-top', '0');
        }
    }
    showHideEnterprise($('#EmployeeExternal').val());
    $('#EmployeeExternal').change(function(){
        var val = $(this).val();
        showHideEnterprise(val);
    });
    $('#EmployeeExternal').click(function(){
        if($('#EmployeeExternal').val() == 2){
            $('#multiResource').show();
        } else {
            $('#multiResource').hide();
        }
        if($('#EmployeeExternal').val() == 1){
            if( $('#EmployeeExternalId').val() == ''){
                $('#btnSave').hide();
            }
            $('#label-external').show();
            $('#EmployeeExternalId').click(function(){
                if( $('#EmployeeExternalId').val() == ''){
                    $('#btnSave').hide();
                } else {
                    $('#btnSave').show();
                }
            });
        } else {
            $('#label-external').hide();
        }
        if($('#EmployeeExternal').val() != 1){
            $('#label-external').hide();
            $('#btnSave').show();
        }
    });
    $('#EmployeeCapacityInHour, #EmployeeCapacityInMinute').keyup(function(){
        var val1 = $('#EmployeeCapacityInHour').val() ? $('#EmployeeCapacityInHour').val() : 0,
            val2 = $('#EmployeeCapacityInMinute').val() ? $('#EmployeeCapacityInMinute').val() : 0,
            val = parseFloat(parseFloat(val1*60) + parseFloat(val2));
        $('#EmployeeTotalMinutes').val(val);
    });
    var fill_more_than_capacity_day = <?php echo json_encode($fill_more_than_capacity_day) ?>;
    $('#EmployeeCapacityInHour').keypress(function(e){
        var key = e.keyCode ? e.keyCode : e.which;
        if(!key || key == 8 || key == 13){return;}
        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
        var _val = parseFloat(val, 10);
        if(fill_more_than_capacity_day){
            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[0-9]{2})?$/.test(val) || !(_val >= 0 && _val <= 8))){
                e.preventDefault();
                return false;
            }
        } else {
            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[0-9]{2})?$/.test(val) || !(_val >= 0 && _val <= 24))){
                e.preventDefault();
                return false;
            }
        }
    });
    $('#EmployeeCapacityInMinute').keypress(function(e){
        var key = e.keyCode ? e.keyCode : e.which;
        if(!key || key == 8 || key == 13){return;}
        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
        var _val = parseFloat(val, 10);
        var val0 = $('#EmployeeCapacityInHour').val(),
            val1 = fill_more_than_capacity_day ? 8 : 24;
        if( val0 == val1 ){
            return false;
        } else {
            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[0-9]{2})?$/.test(val) || !(_val >= 0 && _val <= 59))){
                e.preventDefault();
                return false;
            }
        }
    });
    $('#EmployeeCapacityInHour').blur(function(){
        var val0 = $('#EmployeeCapacityInHour').val(),
            val1 = fill_more_than_capacity_day ? 8 : 24;
        if(val0 == val1){
            $('#EmployeeCapacityInMinute').val('0');
        }
    });
    $('#EmployeeTotalMinutes').prop( "disabled", true );
	$('.wd-btn-save').on('click', function(e){
		$('#EmployeeAddForm').submit();
	});
	/* Auto comlete for email */
	$(window).ready(function(){
		$('.wd-content').find('input').trigger('change');
		$('#EmployeeDefault').addClass('ready');
	});
	$('#EmployeeFirstName, #EmployeeLastName').on('change keyup', function(){
		if( $('#EmployeeEmail').hasClass('edited') ) return;
		var email = '';
		var first_name = $('#EmployeeFirstName').val();
		email += ( default_user_profile.first_name == "1") ? first_name : '';
		var first_letter = first_name ? first_name[0] : '';
		email += ( default_user_profile.first_letter_first_name == "1") ? first_letter : '';
		email += default_user_profile.sperator;
		var last_name = $('#EmployeeLastName').val();
		email += ( default_user_profile.last_name == "1") ? last_name : '';
		first_letter = last_name ? last_name[0] : '';
		email += ( default_user_profile.first_letter_last_name == "1") ? first_letter : '';
		email += default_user_profile.domain_name;
		$('#EmployeeEmail').val(email).trigger('change');
		
	});
	/* END Auto comlete for email */
</script>