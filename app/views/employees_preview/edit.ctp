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
<?php echo $html->script('dropzone.min'); ?>
<?php //echo $html->script('history_filter'); ?>
<?php
    if($this->data['Employee']['update_budget'] == 1 && $see_budget == 1) {
        $sl_budget = 2;
    }
    elseif($this->data['Employee']['update_budget'] == 0 && $see_budget == 1) {
        $sl_budget = 1;
    } else {
        $sl_budget = 0;
    }
?>
<script type="text/javascript">
    // HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
    // HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees_preview', 'action' => 'history_filter')); ?>';
</script>
<?php
$abc = array();
$def = array();
if (!empty($this->data['ProjectEmployeeProfitFunctionRefer'])) {
    foreach($this->data['ProjectEmployeeProfitFunctionRefer'] as $v){
        $abc[] = $v['function_id'];
        if( isset($projectFunctions[ $v['function_id'] ]) )
            $def[] = $projectFunctions[ $v['function_id'] ];
    }
}
$icon_svg = array(
	'icon_view' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a-eye{fill:none;}.b-eye{fill:#fff;fill-rule:evenodd;}</style></defs><rect class="a-eye" width="24" height="24"/><path class="b-eye" d="M10,16C4.476,16,.556,10.857,0,8,.556,5.143,4.476,0,10,0s9.444,5.143,10,8C19.444,10.857,15.524,16,10,16ZM10,1.714C5.639,1.714,2.394,5.681,1.717,8c.677,2.32,3.922,6.286,8.283,6.286S17.606,10.32,18.283,8C17.606,5.681,14.361,1.714,10,1.714Zm0,9.715A3.382,3.382,0,0,1,6.667,8a3.334,3.334,0,1,1,6.666,0A3.382,3.382,0,0,1,10,11.429Zm0-5.143A1.715,1.715,0,1,0,11.667,8,1.693,1.693,0,0,0,10,6.286Z" transform="translate(2 4)"/></svg>',
	'icon_view_off' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a-eye-off{fill:none;}.b-eye-off{fill:#fff;}</style></defs><rect class="a-eye-off" width="24" height="24"/><path class="b-eye-off" d="M16849.152,18677.551l0,0,1.047-1.812a9.019,9.019,0,0,0,4.719-2.541,9.315,9.315,0,0,0,2.363-3.645,8.581,8.581,0,0,0-1.773-3.012l.883-1.527a10.152,10.152,0,0,1,2.6,4.539,10.554,10.554,0,0,1-3.086,5.025,10.109,10.109,0,0,1-6.756,2.975Zm-3.131-.51v0a11.392,11.392,0,0,1-4.811-3.391,9.73,9.73,0,0,1-2.211-4.1,10.6,10.6,0,0,1,3.139-5.068,10.05,10.05,0,0,1,6.859-2.93,9.381,9.381,0,0,1,5.051,1.518l-.85,1.473a7.8,7.8,0,0,0-4.2-1.273c-4.5,0-7.662,4.16-8.283,6.281a9.928,9.928,0,0,0,6.15,5.965l-.717,1.24a1.3,1.3,0,0,0-.127.283Zm2.342-4.121h0a3.44,3.44,0,0,1,.635-6.8,3.292,3.292,0,0,1,2.57,1.244l-.973,1.686a1.677,1.677,0,1,0-1.6,2.213,1.634,1.634,0,0,0,.34-.035l-.975,1.688Z" transform="translate(-16837 -18657.553)"/><rect class="b-eye-off" width="2" height="22" rx="1" transform="translate(18.267 1.948) rotate(30)"/></svg>',
	
);
//message popup
$line_one = '<div class="line_one">'. __('Be careful', true) .'</div>'; 
$line_two = '<div class="line_two">'. __('The following information will be replaced by anonymous information ( First name, Last Name, Email, picture ) , the phone number will be deleted', true) .'</div>'; 
$line_three = '<div class="line_three">'. __('These information will cannot  not be retrieved', true) .'</div>'; 
$anonymous = !empty($employee_date['Employee']['anonymous']) ? $employee_date['Employee']['anonymous'] : 0;
// ob_clean();
// debug($anonymous);
// exit;
?>
<style>
	.wd-checkbox-switch .wd-update .icon_anony{
		position: relative;
		background: #2f9206b8;
	}
	.wd-checkbox-switch .wd-update .icon_anony_off{
		position: relative;
		float: right;
		left: 2px;
		background: #f51b057d;
	}
	.wd-col.wd-col-md-4.header-title {
		padding-left: 0px;
		padding-right: 0px;
	}
	@media (min-width: 992px){
		.header-title {
			width: auto;
		}
		.wd-text-right{
			width: auto;
		}
	}
	.wd-title >* {
		width: auto;
	}
	.wd-col.wd-col-md-8.wd-text-right {
		padding-right: 0px;
		padding-left: 0px;
	}
	p.wd-input-title {
		padding-left: 5px;
		text-align: left;
	}
	<?php if($anonymous == 1){?>
		.wd-checkbox-switch .wd-update .icon_anony{
			display: none;
		}
		.wd-checkbox-switch .wd-update .icon_anony_off{
			display: block;
		}
	<?php }else{?>
		.wd-checkbox-switch .wd-update .icon_anony{
			display: block;
		}
		.wd-checkbox-switch .wd-update .icon_anony_off{
			display: none;
		}
	<?php }?>
</style>
<div id="list-dialog" title="<?php __('List') ?>">
	<?php if( isset($projects) ){?>
		<h3><?php __('Projects') ?></h3>
		<ul><?php foreach($projects as $p){ ?>
				<li><a class="itemx" href="<?php echo $this->Html->url('/project_tasks/index/' . $p['P']['id']) ?>" target="_blank"><?php echo $p['P']['project_name'] ?></a></li><?php } ?>
		</ul>
	<?php } if( isset($activities) ){ ?>
		<h3><?php __('Activities') ?></h3>
		<ul> <?php foreach($activities as $p){ ?>
		<li><a class="itemx" href="<?php echo $this->Html->url('/activity_tasks/index/' . $p['P']['id']) ?>" target="_blank"><?php echo $p['P']['name'] ?></a></li>
		<?php } ?>
		</ul><?php } ?>
</div>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content wd-user-edit">
            <div class="wd-list-project">
                <div class="wd-title">
					<div class="wd-row">
						<div class=" wd-col wd-col-md-4 header-title">
							<div class="heading-back"><a href="javascript:window.history.back();" ><i class="icon-arrow-left"></i><span><?php __('Back');?></span></a></div>
							<h2 class="wd-t1">
								<?php echo __("Employee Detail", true); ?>
								<?php if( $readOnly ): ?><span><?php echo __('(Read only)') ?></span><?php endif ?>
							</h2>
						</div>
						<div class=" wd-col wd-col-md-8 wd-text-right">
							<div class="wd-field wd-anonymous-switch">
								<div id = "switch-activated" class="wd-checkbox-switch">
									<a class = "wd-update">
										<span class="icon_anony"><?php echo $icon_svg['icon_view'];?></span>
										<span class="icon_anony_off"><?php echo $icon_svg['icon_view_off'];?></span>
										<input type="hidden" name="activated" data-company-id="<?php echo $this->data['Employee']['company_id']?>" data-employee-id = '<?php echo $this->data['Employee']['id']?>'>
									</a>
								</div>
								<p class="wd-input-title"><?php __("Anonymous") ?></p>
							</div>
							<div class="wd-current-employee-info">
								<span><?php echo __('Created on')?> <?php echo date('d-m-Y', $this->data['Employee']['created']); ?><?php if(!empty($this->data['Employee']['last_login'])){ echo ' - '; echo __('Last connection on')?> <?php echo date('d-m-Y', $this->data['Employee']['last_login']); }?></span>
								<p><?php echo __('Last modified on')?> <?php echo date('d-m-Y', $this->data['Employee']['updated']); ?> <?php if(!empty($this->data['Employee']['update_by']) && !empty($employees[$this->data['Employee']['update_by']])){ echo __('by')?> <img width="24" height="24" class="circle" src="<?php echo $this->UserFile->avatar($this->data['Employee']['update_by']);?>" alt="<?php echo $employees[$this->data['Employee']['update_by']]; ?>">  <?php echo $employees[$this->data['Employee']['update_by']];?></p> <?php } ?>
							</div>
							<?php if(!empty($employees)) : 
								asort($employees);
								?>
								<div class="wd-title-employees">
									<p class="employee-current" style="display:inline-block">
										<img width="24" height="24" class="circle avatar_current" src="<?php echo $this->UserFile->avatar($employee_id);?>" alt="<?php echo $employees[$employee_id]; ?>">
										<p class="empName_current">
											<?php echo $employees[$employee_id]; ?>
										</p>
									</p>
									<div class="wd-option-employee" style="display: none">
										<div class="context-employee-filter"><input type="text" class="wd-input-search" placeholder="<?php echo __('Search', true) ?>" rel="no-history"></div>
										<ul class="option-content">
											<?php 
												unset($employees[$employee_id]);
												foreach($employees as $e_id => $employee_name){?>
													<li class="employee-item" data-id="<?php echo $e_id;?>"><img width="24" height="24" class="circle" src="<?php echo $this->UserFile->avatar($e_id);?>" alt="<?php echo $employee_name; ?>"> <span><?php echo $employee_name ?></span></li>
												<?php }
											?>
										</u>
									</div>
								</div>
							<?php endif; ?>
							<?php if( !$readOnly ): ?>
							<div class="wd-title-actions">
								<a href="" id="reset" class="wd-btn-reset">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a-reset{fill:none;}.b-reset{fill:#bcbcbc;fill-rule:evenodd;}</style></defs><rect class="a-reset" width="24" height="24"/><path class="b-reset" d="M10.873,18.575l0,.89c0,.484-.35.675-.776.426L7.163,18.182a.48.48,0,0,1,0-.9l2.957-1.744c.424-.25.768-.062.766.423l0,.8A7.044,7.044,0,0,0,16.2,10a6.936,6.936,0,0,0-2.715-5.488c-.885-.652.465-1.967,1.387-1.156A8.653,8.653,0,0,1,18,10,8.829,8.829,0,0,1,10.873,18.575ZM10.837,2.759,7.908,4.468c-.427.249-.775.058-.776-.426l0-.813A7.047,7.047,0,0,0,1.8,10a6.931,6.931,0,0,0,2.69,5.467c.91.673.01,2.427-1.437,1.113A8.65,8.65,0,0,1,0,10,8.83,8.83,0,0,1,7.12,1.419l0-.886c0-.485.342-.673.766-.423l2.957,1.744A.48.48,0,0,1,10.837,2.759Z" transform="translate(3 1.999)"/></svg>
								</a>
								<a href="javascript:void(0);" class="wd-btn-save"><?php echo __('Save', true) ?></a>
							</div>
							<?php endif; ?>
						</div>
					</div>
                </div>
				<div class="wd-panel">
					<div class="wd-container" id="wd-fragment-1">
						<?php echo sprintf($this->Session->flash(), '<a href="javascript:;" onclick="$(\'#list-dialog\').dialog(\'open\');">Project/activity list</a>.'); ?>
						<?php
						echo $this->Form->create('Employee', array(
							'id' => 'EmployeeEditForm', 
							'url' => array('controller' => 'employees_preview', 
								'action' => 'edit', 
								$employee_id, 
								$company_id
							),
							'type' => 'POST'
							));
						echo $this->Form->hidden('readOnly', array('value' => $readOnly));
						// echo $validation->bind("Employee", array('form' => '#EmployeeEditForm')); 
						echo $this->Form->input('id');
						App::import("vendor", "str_utility");
						$str_utility = new str_utility();
						foreach (array('start_date', 'end_date') as $d) {
							if (isset($this->data['Employee'][$d]) && $this->data['Employee'][$d] == '0000-00-00') {
								$this->Form->data['Employee'][$d] = '';
							} elseif (!empty($this->data['Employee'][$d])) {
								$this->Form->data['Employee'][$d] = date('d-m-Y', strtotime($this->data['Employee'][$d]));
							}
						}
						?>
						<fieldset>
						<div class="wd-row">
							<div class=" wd-col wd-col-md-4">
								<div class="wd-left-inner">
									<div class="wd-row">
										<div class=" wd-col wd-col-md-4">
											<div id="ch_avatar" class="avatar_emp <?php if($anonymous == 1) echo 'anonymous-img';?>  ">
												<?php
												$linkAvatar = $this->UserFile->avatar($id, 'avatar');
													//$linkAvatar = $this->UserFile->avatar($id, 'large');
												?>
												<img class="avatar_current" id="avatar_current" src="<?php echo $linkAvatar;?>" />
												<?php if( !$readOnly ): ?>
												<div class="ch-edit">
													<a class="wd-avatar-edit" href="javascript:void(0);" id="edit_avatar_employee"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a-photo{fill:none;}.b-photo{fill:#fff;fill-rule:evenodd;}</style></defs><rect class="a-photo" width="24" height="24"/><path class="b-photo" d="M15,14.2H1a1.008,1.008,0,0,1-1-1.015V3.043A1.008,1.008,0,0,1,1,2.028H3.5L5.5,0h5l2,2.028H15a1.008,1.008,0,0,1,1,1.015V13.185A1.008,1.008,0,0,1,15,14.2ZM14.5,3.55H11.878l-2-2.029H6.122l-2,2.029H1.5v9.128h13ZM8,4.057a3.55,3.55,0,1,1-3.5,3.55A3.526,3.526,0,0,1,8,4.057ZM8,9.635A2.029,2.029,0,1,0,6,7.607,2.017,2.017,0,0,0,8,9.635Z" transform="translate(4 5)"/></svg></a>
												</div>
												<?php endif ?>
											</div>
										</div>
										<div class=" wd-col wd-col-md-8">
											<div class="wd-field">
												<p class="wd-input-title wd-no-margin-top"><?php __("First Name"); ?></p>
												<?php echo $this->Form->input('first_name', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>
											</div>
											<div class="wd-field">
												<p class="wd-input-title"><?php __("Last name"); ?></p>
												<?php echo $this->Form->input('last_name', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>	
											</div>
										</div>
									</div>
									<div class="wd-row">
										<div class=" wd-col wd-col-md-12">
										<div class="wd-field">
											<p class="wd-input-title"><?php __("Email"); ?></p>
											<?php echo $this->Form->input('email', array('div' => false, 'label' => false, 'autocomplete' => 'off', 'rel' => 'no-history')); ?>
										</div>
										<div class="wd-field">
											<p class="wd-input-title"><?php __("Password"); ?></p>
											<input type="password" style="display: none;" id="password-breaker" name="password-breaker" />
											<?php echo $this->Form->input('password', array('div' => false, 'label' => false, "value" => "", 'rel' => 'no-history')); ?>
										</div>
										<!-- Start Role --> 
										<div class="wd-field">
											<p class="wd-input-title"><?php __("Role") ?></p>
											<?php
											if(!empty($profile_name)){
												foreach ($profile_name as $key => $value) {
													$roles['profile_' . $key] = $value;
												}
											}
											if($employee_info['Employee']['id'] == $id && $employeeLogin['Employee']['is_sas'] == 0){
												foreach ($roles as $key => $value) {
													//dont allow the pm to select company admin role
													if(!empty($this->data['Employee']['profile_account']) && 'profile_' . $this->data['Employee']['profile_account'] == $key){
														echo "<input disabled value='". __($value, true) ."' />";
														continue;
													}
													if( $is_pm && ($key == 2 || $key == 'ws') )continue;
													if ($role_id == $key && empty($this->data['Employee']['profile_account'])) {echo "<input disabled value='". __($value, true) ."' />"; continue; }
												}
												?>
												<input type="hidden" id="EmployeeRoleId" name="data[Employee][role_id]" value="<?php echo $role_id;  ?>" />
												<?php
											} else {
												if( $readOnly ): ?>
												<span style="display: inline-block; margin-top: 6px;">
													<?php __($roles[2]) ?>
												</span>
												<?php else: ?>
												<select  rel="no-history" name="data[Employee][role_id]" id="EmployeeRoleId" <?php if( $employee_info['Employee']['id'] == $id && $employeeLogin['Employee']['is_sas'] == 0)echo 'readonly' ?>>
													<option value=""><?php echo __("---Select role---") ?></option>
													<?php
													foreach ($roles as $key => $value) {
														//dont allow the pm to select company admin role
														if( $is_pm && $key == 2 )continue;
														if ( ($key == 'ws' && $this->data['Employee']['ws_account']) || (!$this->data['Employee']['ws_account'] && $role_id == $key) ){
															echo "<option selected value='" . $key . "'>" . __($value, true) . "</option>";
														} else if(!empty($this->data['Employee']['profile_account']) && 'profile_' . $this->data['Employee']['profile_account'] == $key) {
															echo "<option selected value='" . $key . "'>" . __($value, true) . "</option>";
														} else {
															echo "<option value='" . $key . "'>" . __($value, true) . "</option>";
														}
													}
													?>
												</select>
												<?php endif ?>
												<?php } ?>
											</div>
											<!-- End Role --> 
											<div class="wd-row">
												<div class="wd-col wd-col-md-6">
													<div class="wd-field">
														 <p class="wd-input-title"><?php __("Work phone") ?></p>
														<?php  echo $this->Form->input('work_phone', array('div' => false, 'label' => false, 'rel' => 'no-history'));  ?>
													</div>
												</div>
												<div class="wd-col wd-col-md-6">
													<p class="wd-input-title" style="color: #FFF"><?php __("Actif") ?></p>
													<div class="wd-field wd-field-switch">
														<?php echo $this->Form->input('actif', array(
																'type' => 'checkbox',
																'class' => 'hidden',
																'label' => '<span class="wd-btn-switch"><span></span></span>',
																'rel' => 'no-history',
																'checked'=> !empty($this->data['Employee']['actif']) ? true : false,
																'div' => array(
																	'class' => 'wd-input wd-checkbox-switch',
																	'title' => __('Actif',true),
																),
																'type' => 'checkbox', 
															));?>
														<p class="wd-input-title"><?php __("Actif") ?></p>
													</div>
												</div>
												<?php if(!empty($companyConfigs['company_two_factor_auth'])) { ?>
												<div class="wd-col wd-col-md-12">
													<div class="wd-field wd-field-switch two_factor_auth" style="margin: 10px 0px 5px;">
														<?php echo $this->Form->input('two_factor_auth', array(
																'type' => 'checkbox',
																'class' => 'hidden',
																'label' => '<span class="wd-btn-switch"><span></span></span>',
																'checked'=> !empty($this->data['Employee']['two_factor_auth']) ? true : false,
																'div' => array(
																	'class' => 'wd-input wd-checkbox-switch',
																	'title' => __('Two Factor Authentication',true),
																), 
																'type' => 'checkbox', 
																'id' => 'two_factor_auth'
															));?>
														<p class="wd-input-title"><?php __("Two Factor Authentication") ?></p>
														<a href="javascript:void(0);" id="two_factor_auth_reset" class="wd-input-title wd-inline-block wd-btn-reset" title="<?php echo __('Delete the information of Two Factor Authentication',true);?>"<?php echo empty($this->data['Employee']['two_factor_auth']) ? ' style="display: none;"'  : '';?>><i class="icon icon-refresh"></i><?php echo __('Reset', true);?></a>
													</div>
												</div>
												<?php } ?>
											</div> 
											<div class="wd-row">
												<div class="wd-col wd-col-md-6">
													<div class="wd-field">
														<p class="wd-input-title"><?php __("Start date") ?></p>
														<?php echo $this->Form->input('start_date', array('type' => 'text', 'div' => false, 'label' => false, 'rel' => 'no-history')); ?>
														<span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
													</div>
												</div>
												<div class="wd-col wd-col-md-6">
													<div class="wd-field">
														 <p class="wd-input-title"><?php __("End date") ?></p>
														<?php echo $this->Form->input('end_date', array('type' => 'text', 'div' => false, 'label' => false, 'rel' => 'no-history')); ?>
														<span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
													</div>
												</div>
											</div> 
											<div class="wd-field">
												<p class="wd-input-title"><?php __("ID") ?></p>
												<?php echo $this->Form->input('code_id', array('type' => 'text', 'div' => false, 'label' => false, 'rel' => 'no-history')); ?>
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
													'rel' => 'no-history',
													"options" => $arr_new + $arr_tam));
												echo $this->Form->input('oldPc', array('type' => 'hidden', 'value' => $oldProfitCenter));
												?>
												
												<!-- Hidden fields !-->
												 <input type="hidden" name="data[Employee][refer_id]" value="<?php echo $refer_id ?>" />
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
													'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['create_a_project']) ? true : false,
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['delete_a_project']) ? true : false,
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['change_status_project']) ? true : false,
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['update_your_form']) ? true : false,
															'rel' => 'no-history',
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
														'value' => $sl_budget,
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
															'checked'=> !empty($see_all_projects) ? true : false,
															'name' => 'data[see_all_projects]',
															'rel' => 'no-history',
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
															'checked'=> !empty($control_resource) ? true : false,
															'name' => 'data[control_resource]',
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['can_see_forecast']) ? true : false,
															'name' => 'data[can_see_forecast]',
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['can_communication']) ? true : false,
															'name' => 'data[can_communication]',
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['email_receive']) ? true : false,
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['activate_copy']) ? true : false,
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['is_enable_popup']) ? true : false,
															'rel' => 'no-history',
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
												<?php if( $role_id < 4 ): ?>
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('auto_timesheet', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($this->data['Employee']['auto_timesheet']) ? true : false,
															'rel' => 'no-history',
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
															'checked'=> !empty($this->data['Employee']['auto_absence']) ? true : false,
															'rel' => 'no-history',
															'div' => array(
																'class' => 'wd-input wd-checkbox-switch',
																'title' => __('Auto validate absence',true),
															), 
															'type' => 'checkbox', 
														));?>
													<p class="wd-input-title"><?php __("Auto validate absence") ?></p>
												</div>
												
												<?php endif; ?>
												
												<div class="wd-field wd-field-switch">
													<?php echo $this->Form->input('auto_by_himself', array(
															'type' => 'checkbox',
															'class' => 'hidden',
															'label' => '<span class="wd-btn-switch"><span></span></span>',
															'checked'=> !empty($this->data['Employee']['auto_by_himself']) ? true : false,
															'rel' => 'no-history',
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
														'rel' => 'no-history',
														'selected'=> !empty($this->data['Employee']['contract_type_id']) ? $this->data['Employee']['contract_type_id'] : 0,
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
														'selected'=> !empty($this->data['Employee']['external']) ? $this->data['Employee']['external'] : 0,
														'rel' => 'no-history',
														'label' => false,
													));?>
											</div>
										</div>
										<div class="wd-col wd-col-md-4">
											<div class="wd-block-inner">
												<p id="label-external" class="wd-input-title" class="enterpriseCompany"><?php __("External company") ?></p>
												<?php echo $this->Form->input('external_id', array("class" => 'enterpriseCompany', "empty" => __("-- Select -- ", true), 'options' => $externals, 'div' => false, 'label' => false, 'rel' => 'no-history')); ?>
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
															'label' => false,
															'rel' => 'no-history')); ?>
												</div>
												<div class="wd-field">
													<p class="wd-input-title"><?php __("Average Daily Rate") ?></p>
													 <?php  echo $this->Form->input('tjm', array(
															'type' => 'text',
															'div' => false,
															'label' => false,
															'rel' => 'no-history')); ?>
												</div>
											</div>
										</div>
										<div class="wd-col wd-col-md-9">
											<div class="wd-row">
												<div class="wd-col wd-col-md-6">
													<div class="wd-block-inner">
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID3") ?></p>
															<?php echo $this->Form->input('id3', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>
														</div>
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID4") ?></p>
															<?php echo $this->Form->input('id4', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>
														</div>
													</div>
												</div>
												<div class="wd-col wd-col-md-6">
													<div class="wd-block-inner">
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID5") ?></p>
															 <?php echo $this->Form->input('id5', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>
														</div>
														<div class="wd-field">
															<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID6") ?></p>
															<?php echo $this->Form->input('id6', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>
														</div>
													</div>
												</div>
												<div class="wd-col wd-col-md-12">
													<div class="wd-block-inner">		
														<p class="wd-input-title"><?php __d(sprintf($_domain, 'Resource'), "ID2") ?></p>
														<?php echo $this->Form->input('identifiant', array('type' => 'text', 'div' => false, 'label' => false, 'rel' => 'no-history')); ?>
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
													<?php echo $this->Form->input('city_id', array('div' => false, "options" => $cities, 'label' => false, 'rel' => 'no-history')); ?>
												</div>
											</div>
										</div>
										<div class="wd-col wd-col-md-6">
											<div class="wd-block-inner">
												<div class="wd-field">
													<p class="wd-input-title"><?php __("Country"); ?></p>
													<?php echo $this->Form->input('country_id', array('div' => false, 'label' => false, 'rel' => 'no-history')); ?>
							
												</div>
											</div>
										</div>
										
									</div> 
								</div>
						   </div>
						</div>
						
						</fieldset>
						<?php echo $this->Form->end() ?>
					
						<?php if( !$readOnly && !empty($dates)): ?>
						<div id="multiResource" class="wd-tab">
							<ul class="wd-item">
								<li class="wd-current"><a href="<?php echo "#tabs".$first; ?>"><?php echo $first; ?></a></li>
								<?php foreach ($dates as $key => $_date) {
									if($key != $first){?>
								<li><a href="<?php echo "#tabs".$key; ?>"><?php echo $key; ?></a></li>
								<?php } } ?>
							</ul>
							<div class="content-area">
								<?php foreach ($dates as $key => $_date){?>
									<?php
									$_class = 'inactive';
									if($key == $first){
										$_class = 'active';
									}
									?>
									<div id="<?php echo "tabs".$key; ?>" class="<?php echo $_class;?>">
									<h3 class="totalOfYear"><?php echo __('Total: ');?><span class="total-<?php echo $key;?>"><?php echo !empty($sumYearOfMutis[$key]) ? $sumYearOfMutis[$key] : 0;?></span></h3>
									<?php foreach ($_date as $k => $date) {
										$temp = $date;
										?>
								<div id="absence-wrapper" class="wrap-<?php echo date('m-Y', array_shift($temp));?>">
									<table id="absence-fixed">
										<thead>
											<tr>
												<th>
													<span><?php switch ($k) {
														case '01':
															echo __('January', true);
															break;
														case '02':
															echo __('February', true);
															break;
														case '03':
															echo __('March', true);
															break;
														case '04':
															echo __('April', true);
															break;
														case '05':
															echo __('May', true);
															break;
														case '06':
															echo __('June', true);
															break;
														case '07':
															echo __('July', true);
															break;
														case '08':
															echo __('August', true);
															break;
														case '09':
															echo __('September', true);
															break;
														case '10':
															echo __('October', true);
															break;
														case '11':
															echo __('November', true);
															break;
														default:
															echo __('December', true);
															break;
													} ?></span>
												</th>
												<th style="width:40px;"><?php echo __('Total', true);?></th>
											</tr>
										</thead>
										<tbody id="absence-table-fixed"    >
											<tr>
												<td class="sum-month" style="width: 70px;">
													<?php $i = 0;
													foreach ($date as $value){
														if($i == 0){
															$start = $value;
														}
														$i ++;
													}?>
													<input type="text" class="input_value_multi" id="<?php echo 'id'.$start; ?>" onchange="updateValueMultiResource(<?php echo $id; ?>, <?php echo $start; ?>, <?php echo $value; ?>, <?php echo $start; ?>, this.value, '<?php echo 'wrap-' . date('m-Y', $start);?>', '<?php echo 'cell-' . date('m-Y', $start);?>', '<?php echo date('m', $start);?>', '<?php echo date('Y', $start);?>')"
														value="<?php
															//set value for by date
															if(!empty($employee_multi[strtotime('01-'.$k.'-'.$key)]['by_day'])){
																echo number_format($employee_multi[strtotime('01-'.$k.'-'.$key)]['by_day'], 2, '.', ' ');
															}else if(!empty($employee_multi[strtotime('03-'.$k.'-'.$key)]['by_day'])){
																echo number_format($employee_multi[strtotime('03-'.$k.'-'.$key)]['by_day'], 2, '.', ' ');
															}else if(!empty($employee_multi[strtotime('05-'.$k.'-'.$key)]['by_day'])){
																echo number_format($employee_multi[strtotime('05-'.$k.'-'.$key)]['by_day'], 2, '.', ' ');
															}else{
																echo '0.00';
															}
														?>">
												</td>
												<td style="width:50px;" class="total-<?php echo date('m-Y', $start);?> wrap-<?php echo date('Y', $start);?>"><?php echo !empty($sumMonthOfMutis[date('m-Y', $start)]) ? number_format($sumMonthOfMutis[date('m-Y', $start)], 2, '.', ' ') : '0.00';?></td>
											</tr>
										</tbody>
									</table>
									<div id="absence-scroll">
										<table id='absence'>
											<thead>
												<tr>
													<?php
													foreach ($date as $value) {
														echo "<th>";
														echo date('d',$value);
														echo ' ';
														echo __(date('M', $value));
														echo "</th>";
													}
													?>
												</tr>
											</thead>
											<tbody id="absence-table" class="ui-selectable">
												<tr>
													<?php
													foreach ($date as $value) {
														if( strtolower(date('l', $value)) == 'saturday' || strtolower(date('l', $value)) == 'sunday' ){
															echo "<td class = 'ct' style='min-width: 70px;'>"."</td>";
														}else if (!empty($days) && in_array($value, $days)){
															echo "<td style='min-width: 70px; background-color: #ffff00; text-align: center;'>".__('Holiday', true)."</td>";
														}else{
													?>
														<td style ="min-width: 70px;">
															<input type="text" rel="no-history" id="<?php echo $value; ?>" class="<?php echo $start; ?> <?php echo 'cell-' . date('m-Y', $start);?> input_value_multi" onchange="updateValue(<?php echo $id; ?>, <?php echo $value; ?>, this.value, '<?php echo 'wrap-' . date('m-Y', $start);?>', '<?php echo 'cell-' . date('m-Y', $start);?>', '<?php echo date('m', $start);?>', '<?php echo date('Y', $start);?>')"
															value="<?php echo !empty($employee_multi[$value]['value']) ? number_format($employee_multi[$value]['value'], 2, '.', ' ') : '0.00'; ?>" />
														</td>
													<?php
														}
													}
													 ?>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<?php }?> </div> <?php }?>
							</div>
						</div>
					 <?php endif ?>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<div id="dialog_skip_value" class="buttons" style="display: none;">
    <div id="dialog_skip_value_contents">
        <div id="show-value" >
        </div>
        <h2 id="title-absences"><?php echo __('Modify the end date even if the resource has absences ?', true); ?></h2>
        <div id="show-absence" >
        </div>
    </div>
    <div style="margin-top: 20px;text-align: right;">
        <button class="btn-default" id="btn-yeschange"><?php echo __("Yes", true) ?></button>
        <button id="btn-nochange" class="btn-default"><?php echo __("No", true) ?></button>
    </div>
</div>
<div id='modal_dialog_confirm' style="display: none">
    <div class='title'>
    </div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnYes' />
    <input class="cancel-new-project" type='button' value='<?php echo __('Cancel', true) ?>' id='btnNo' />
</div>
<!-- avatar_popup -->
 <div class="wd-popup-container">
	 <div class="wd-popup">
		<?php
		echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
			'url' => array('controller' => 'employees', 'action' => 'update_avatar', $company_id, $id)
		));
		?>
		<div class="trigger-upload">
			<div id="upload-popup" method="post" action="/employees/update_avatar/<?php echo $company_id; ?>/<?php echo $id; ?>" class="dropzone" value="" >
			</div>
		</div>
		<ul class="actions">
			<li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
			<li><button type="submit" id="avatar_popup_submit" class="new" href="#"><?php echo __('Submit') ?></button></li>
		</ul>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<!-- End avatar_popup -->
<?php echo $html->script('validateDate'); ?>
<script language="javascript">
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
    var arrPosition=$('#EmployeePassword').position();
    var widthIt = $('#EmployeePassword').width();
    var leftMargin = widthIt+arrPosition.left;
    var id = <?php echo $id; ?>;
    var codeStart = <?php echo json_encode(__('Start date is not validate', true));?>;
    var codeEnd = <?php echo json_encode(__('End date is not validate', true));?>;
    var codeErroStartDay = <?php echo json_encode(__('There is a workload before the start date', true));?>;
    var codeErroEndDay = <?php echo json_encode(__('There is a workload after the end date', true));?>;
	var employeeData = <?php echo json_encode($this->data['Employee']);?>;
	var companyEmployeeRefer = <?php echo json_encode($this->data['CompanyEmployeeReference']);?>;
    // $('#ch_avatar').css({'left':leftMargin+'px'});
    $(window).resize(function(e) {
        var arrPosition=$('#EmployeePassword').position();
        var widthIt = $('#EmployeePassword').width();
        var leftMargin = widthIt+arrPosition.left;
        // $('#ch_avatar').css({'left':leftMargin+'px'});
    });
    <?php if( $readOnly ): ?>
    $('#EmployeeEditForm input, #EmployeeEditForm select').prop('disabled', true);
    <?php endif ?>
    $("select[name='data[Employee][project_function_id]']").multiSelect({noneSelected: 'Select skills', oneOrMoreSelected: '*', selectAll: false });

    function reset_form(){
        removeMessage();
    }
    if($('#EmployeeExternal').val() == 2){
        $('#multiResource').show();
    }else {
        $('#multiResource').hide();
    }
	$('#two_factor_auth').on('change', function(){ $('#two_factor_auth_reset').toggle($(this).is(':checked'));});
	$('#two_factor_auth_reset').on('click', function(){ 
		var _this = $(this);
		wdConfirmIt({
			title: _this.text(),
			content: _this.attr('title'),
			width: 380,
			buttonModel: 'WD_TWO_BUTTON',
			buttonText: [
				'<?php __('Yes');?>',
				'<?php __('No');?>'
			],
		},function(){//yes
			$.ajax({
				url: '<?php echo $html->url(array('controller' => 'employees_preview', 'action' => 'reset2fa', $this->data['Employee']['id'])); ?>',
				dataType: 'json',
				beforeFilter: function() {
					_this.addClass('loading');
				},
				success: function (data) {
					if( $('#flashMessage').length) $('#flashMessage').remove();
					var _flash = $('<div id="flashMessage" class="message success" style="display: none;"><a href="#" class="close">x</a></div>');
					if( data.message){
						$('#wd-fragment-1').prepend(_flash.prepend(data.message));
						_flash.slideDown(300, function(){ setTimeout( function(){ _flash.slideUp();}, 1500)});
					}
					_this.removeClass('loading').hide();
				}
			});
		});
	});
    $('#EmployeeExternal').click(function(){
        if($('#EmployeeExternal').val() == 2){
            $('#multiResource').show();
        } else {
            $('#multiResource').hide();
        }
        if($('#EmployeeExternal').val() == 1){
            if( $('#EmployeeExternalId').val() == ''){
                // $('#label-external').show();
                $('#btnSave').hide();
            }
            $('#label-external').show();
            $('#EmployeeExternalId').click(function(){
                if( $('#EmployeeExternalId').val() == ''){
                    // $('#label-external').show();
                    $('#btnSave').hide();
                } else {
                    // $('#label-external').hide();
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
    if($('#EmployeeExternal').val() == 1 && $('#EmployeeExternalId').val() == ''){
        $('#label-external').show();
        $('#btnSave').hide();
        $('#EmployeeExternalId').click(function(){
            if( $('#EmployeeExternalId').val() == ''){
                // $('#label-external').show();
                $('#btnSave').hide();
            } else {
                // $('#label-external').hide();
                $('#btnSave').show();
            }
        });
    }
    $('#EmployeeStartDate').on('change', function(){
        if($('#EmployeeExternal').val() == 2 && ( $('#EmployeeStartDate').val() == '00-00-0000' || $('#EmployeeStartDate').val() == '')){
            $('#valueStartDate').show();
            $('#valueStartDate').css('color', 'red');
            $('#valueStartDate').html(codeStart +'!');
        }else{
            $('#valueStartDate').hide();
        }
        if($('#EmployeeExternal').val() == 2){
            $.ajax({
                url:'<?php echo $html->url(array('action' => 'checkStartDate')); ?>',
                type: 'POST',
                data: {
                    id: id,
                    date: $('#EmployeeStartDate').val()
                },
                success: function(a) {
                    if(a == 'true'){
                        $('#valueStartDate').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                        $('#valueStartDate').show();
                        $('#valueStartDate').css('color', 'red');
                        $('#valueStartDate').html(codeErroStartDay +'!');
                        $('#btnSave').hide();
                    } else {
                        $("#valueStartDate").hide();
                        $('#btnSave').show();
                    }
                }
            });
        }
    });
    var createDialogThree = function(){
            $('#dialog_skip_value').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 550,
                open : function(e){
                    var $dialog = $(e.target);
                    $dialog.dialog({open: $.noop});
                }
            });
            createDialogTwo = $.noop;
        }
    var pre_endate;
    var next_enddate;
    $('#EmployeeEndDate').on('focus', function(){
        pre_endate = $('#EmployeeEndDate').val();
    });
    function check_end_date(id_employee, end_date){
        var result = 1;
		
        $.ajax({
            url:'<?php echo $html->url(array('action' => 'checkChangeEndDate')); ?>',
            type: 'POST',
            async: false,
            data: {
                id: id_employee,
                date: end_date
            },
            success: function(a) {
                if(a == 'false'){
                    result = 0;
                }
            }
        });
        return result;
    }
    $('#EmployeeEndDate').on('change', function(){
        next_enddate = $('#EmployeeEndDate').val();
		$('#EmployeeEndDate').addClass('wd-loading');
        var allowModifyEndDate = check_end_date(id, $('#EmployeeEndDate').val());
        if(allowModifyEndDate == 1) {
            if($('#EmployeeExternal').val() == 2 && ( $('#EmployeeEndDate').val() == '00-00-0000' || $('#EmployeeEndDate').val() == '')){
                $('#valueEndDate').show();
                $('#valueEndDate').css('color', 'red');
                $('#valueEndDate').html(codeEnd +'!');
            } else {
                $('#valueEndDate').hide();
            }
            if($('#EmployeeExternal').val() == 2){
                $.ajax({
                    url:'<?php echo $html->url(array('action' => 'checkEndDate')); ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        date: $('#EmployeeEndDate').val()
                    },
                    success: function(a) {
                        if(a == 'true'){
                            $('#valueEndDate').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                            $('#valueEndDate').show();
                            $('#valueEndDate').css('color', 'red');
                            $('#valueEndDate').html(codeErroEndDay +'!');
                            $('#btnSave').hide();
                        } else {
                            $("#valueEndDate").hide();
                            $('#btnSave').show();
                        }
                    }
                });
            }
        } else {
            $("#show-value").html('');
            $("#show-absence").html('');
            $.ajax({
                url:'<?php echo $html->url(array('action' => 'getChangeEndDate')); ?>',
                type: 'POST',
                async: false,
                dataType: 'json',
                data: {
                    id: id,
                    date: $('#EmployeeEndDate').val()
                },
                success: function(data) {
                    if( !$.isEmptyObject(data.activities) ){
                        $.each(data.activities, function(index, value){
                            $("#show-value").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/activity_tasks/index/') ?>' + data.activities[index].ActivityTask.activity_id + '/?id='+ data.activities[index].ActivityTask.id +'" style="color:blue;font-size:13px">'+ data.activities[index].ActivityTask.name +', </a>, ');
                        });
                    }
                    if(!$.isEmptyObject(data.projects)) {
                        $.each(data.projects, function(index, value){
                            $("#show-value").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/project_tasks/index/') ?>'+ data.projects[index].ProjectTask.project_id +'/?id='+ data.projects[index].ProjectTask.id + '" style="color:blue;font-size:13px">'+data.projects[index].ProjectTask.task_title+', </a>');
                        });
                   }
                   if(!$.isEmptyObject(data.absences)){
                       $("#title-absences").show();
                       $.each(data.absences, function(index, value){
                           //data.absences[index].AbsenceRequest.date;
                            var date = new Date(parseInt(data.absences[index].AbsenceRequest.date) * 1000);
                            var month = date.getMonth() + 1;
                            var year = date.getFullYear();
                            if(data.absences[index].AbsenceAm.name){
                                $("#show-absence").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/absence_requests/index/month?id=') ?>'+ data.absences[index].Employee.id +'&profit='+ data.absences[index].Employee.profit_center_id + '&month='+ month + '&year=' + year + '&get_path=0' + '" style="color:blue;font-size:13px">' + data.absences[index].AbsenceAm.print + ', </a>');
                            }
                            if(data.absences[index].AbsencePm.name){
                                $("#show-absence").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/absence_requests/index/month?id=') ?>'+ data.absences[index].Employee.id +'&profit='+ data.absences[index].Employee.profit_center_id + '&month='+ month + '&year=' + year + '&get_path=0' + '" style="color:blue;font-size:13px">' + data.absences[index].AbsencePm.print + ', </a>');
                            }
                       });
                   } else {
                       $("#title-absences").hide();
                   }
                    if(!$.isEmptyObject(data.activities) || !$.isEmptyObject(data.projects) || !$.isEmptyObject(data.absences)){
                        createDialogThree();
                        $("#dialog_skip_value").dialog('option',{title:'<h2 style="font-size:12px;"><?php echo h(__('Modify the end date even if the resource has task(s) assigned ?', true)); ?></h2>'}).dialog('open');
                        $('#EmployeeEndDate').val(pre_endate);
                    }
                }
            });
        }
		$('#EmployeeEndDate').removeClass('wd-loading');
    });
    $("#btn-yeschange").click(function(){
        $('#EmployeeEndDate').val(next_enddate);
        $('#dialog_skip_value').dialog('close');
    });
    $("#btn-nochange").click(function(){
        $('#EmployeeEndDate').val(pre_endate);
        $('#dialog_skip_value').dialog('close');
    });

    function updateValue(id, date, value, wrap, cell, month, year){
        var _id = '#'+date;
        $(_id).addClass('pch_loading loading_input');
        $(_id).css({
           'padding-left': '0px',
           'color': 'rgb(218, 215, 215)'
        });
        setTimeout(function(){
            $.ajax({
                url :'<?php echo $html->url(array('action' => 'update_value_multi_resource')); ?>',
                //async : false,
                type : 'POST',
                dataType : 'json',
                data: {
                    id: id,
                    date: date,
                    value: value
                },
                beforeSend: function(){

                },
                success: function(data){
                    var sumMonth = 0;
                    $('.' + wrap).find('.' + cell).each(function(){
                        var val = $(this).val();
                        val = val ? val : 0;
                        sumMonth += parseFloat(val);
                    });
                    $('.total-' + month + '-' + year).html(number_format(sumMonth, 2, '.', ' '));
                    var sumYear = 0;
                    $('#tabs'+year).find('.wrap-' + year).each(function(){
                        var val = $(this).html();
                        val = val ? parseFloat(val) : 0;
                        sumYear += val;
                    });
                    $('.total-' + year).html(number_format(sumYear, 2, '.', ' '));
                    setTimeout(function(){
                        $(_id).removeClass('pch_loading loading_input');;
                        $(_id).css('color', '#3BBD43');
                    }, 200);
                }
            });
        }, 200);
    }
    // viet lai
    function _updateValueMultiResource(id, start, end, idOf, by_day, wrap, cell, month, year){
        var _id = '#id'+idOf;
        var _class ='.'+idOf;
        $(_class).val($(_id).val());
        $(_id).addClass('pch_loading loading_input');
        $(_id).css('color', 'rgb(218, 215, 215)');
        setTimeout(function(){
            $.ajax({
                url :'<?php echo $html->url(array('action' => 'update_multi_resource')); ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    id: id,
                    start: start,
                    end: end,
                    by_day: by_day
                },
                beforeSend: function(){
                },
                success: function(data){
                    $('#btnSave').show();
                    var sumMonth = 0;
                    $('.' + wrap).find('.' + cell).each(function(){
                        var val = $(this).val();
                        val = val ? val : 0;
                        sumMonth += parseFloat(val);
                    });
                    $('.total-' + month + '-' + year).html(number_format(sumMonth, 2, '.', ' '));
                    var sumYear = 0;
                    $('#tabs'+year).find('.wrap-' + year).each(function(){
                        var val = $(this).html();
                        val = val ? parseFloat(val) : 0;
                        sumYear += val;
                    });
                    $('.total-' + year).html(number_format(sumYear, 2, '.', ' '));
                    setTimeout(function(){
                        $(_id).removeClass('pch_loading loading_input');
                        $(_class).removeClass('pch_loading loading_input');
                        $(_id).css('color', '#3BBD43');
                        $(_class).css('color', '#3BBD43');
                    }, 200);
                }
            });
        }, 500);
    };
    function updateValueMultiResource(id, start, end, idOf, by_day, wrap, cell, month, year){
        var _id = '#id'+idOf;
        var _class ='.'+idOf;
        $('.title').html('<?php echo __('Confirm ?') ?>');
        var dialog = $('#modal_dialog_confirm').dialog();
        $('#btnYes').click(function() {
            dialog.dialog('close');
            _updateValueMultiResource(id, start, end, idOf, by_day, wrap, cell, month, year);
        });
        $('#btnNo').click(function() {
            dialog.dialog('close');
            $('#btnSave').show();
        });
    };
    $(document).ready(function () {
        $('.wd-tab .wd-item a').on('click', function (e) {
            var currentAttrValue = jQuery(this).attr('href');
            $('.wd-tab ' + currentAttrValue).slideDown(400).siblings().slideUp(400);
            $(this).parent('li').addClass('wd-current').siblings().removeClass('wd-current');
            e.preventDefault();
        });
    });
    $('.input_value_multi').keypress(function(){
        $('#btnSave').hide();
    });
    //---end---
    $(function(){
        $("#EmployeeStartDate, #EmployeeEndDate").datepicker({
            dateFormat      : 'dd-mm-yy'
        });
    });
    var oldProfitCenter = <?php echo json_encode($oldProfitCenter); ?>;
        $('#btnSave').click(function(){
        removeMessage();
        var v1=false,v2=false,v3=true,v4=true,v5=true,v6=false;
        v1=isNotEmpty('EmployeeCompanyId');
        v2=isNotEmpty('EmployeeRoleId');
    
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
        var newPC = $('#EmployeeProfitCenterId').val();
        if(newPC == oldProfitCenter){
            v6 = true;
            //do nothing
        } else {
            if(v1&&v2&&v3&&v4&&v5){
                createDialog();
                $("#confirm_when_change_pc").dialog('open');
                $('#ok_attach').click(function(){
                    v6 = true;
                    $('#EmployeeEditForm').submit();
                });
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
    function isNotEmpty(elementId,cpr){
        var date = $("#"+elementId).val();
        if(date==''){
            if ((elementId != "EmployeeCompanyId") && (elementId != "EmployeeRoleId")&& (elementId != "EmployeeProfitCenterId")) return false;
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
        function removeMessage(){
            $("#flashMessage").hide();
            $('div.error-message').remove();
            $('div.error-message1').remove();
            $('div.error-message2').remove();
            $('div.error-message3').remove();
            $("div.wd-input,input,select").removeClass("form-error");
            check_phase_exist = false;
        };
        $('#EmployeeWorkPhone').keypress(function(){
            var rule = /^\+?([0-9]*)$/;
            var x=$('#EmployeeWorkPhone').val();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("Number or +", true)) ?>"+'</div>');
            } else{
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
		
        $('#EmployeeMobilePhone').keypress(function(){
            var rule = /^\+?([0-9]*)$/;
            var x=$('#EmployeeMobilePhone').val();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Mobile phone must be a number ", true)) ?>"+'</div>');
            } else{
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeHomePhone').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeeHomePhone').val();
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
        $('#EmployeeFax').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeeFax').val();
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
        $('#EmployeePostCode').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeePostCode').val();
            $('div.error-message').remove();
            $('div.error-message3').remove();
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
        var ar = <?php echo json_encode($abc) ?>;
        var tx = "";
        for (i = 0; i < ar.length; i++) {
            $("input[name='EmployeeProjectFunctionId[]'][value='" + ar[i] + "']").attr("checked","checked");
            $("input[name='EmployeeProjectFunctionId[]'][value='" + ar[i] + "']").parent().addClass("checked");
            tx += $("input[name='EmployeeProjectFunctionId[]'][value='" + ar[i] + "']").parent().text() + ",";
        }
        if (tx.length > 0) {
            tx = tx.substr(0, tx.length - 1);
            $("#EmployeeProjectFunctionId span").html(tx);
        }
        $("#EmployeeCodeId").change(function(){
            var code_id = $.trim($(this).val());
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
							var alert_html = '';
							$.each(data, function(id, name){
								alert_html += '<a href ="/employees_preview/edit/'+ id +'/'+ <?php echo json_encode($company_id) ?> +'" title="'+ name +'" target="_blank" >'+ name +'</a>'
							});
                            parentElem.addClass("error");
                            parentElem.append('<div class="error-message wd-error-message-code">'+ alert_html +'</div>');
                        } else {
                            $('#btnSave').removeAttr('disabled');
                        }
                        $("#check").remove();
                    }
                })
            }
        })
        $('#edit_avatar_employee').click(function(){
			$("#uploadForm").trigger('reset');
			$('.wd-popup-container .wd-popup').toggleClass('open');
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
       
        /* table .end */
        var createDialog = function(){
            $('#confirm_when_change_pc').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 400,
                open : function(e){
                    var $dialog = $(e.target);
                    $dialog.dialog({open: $.noop});
                }
            });
            createDialog = $.noop;
        }
        $(".cancel").live('click',function(){
            $("#confirm_when_change_pc").dialog('close');
            $("#avatar_popup").dialog("close");
        });
        $('#EmployeeActif').change(function(){
            var _sl = $(this).find('option:selected').val();
            if(_sl == 0){
                var currentDate = <?php echo json_encode(date('d-m-Y', time()));?>;
                var endDate = $('#EmployeeEndDate').val();
                if(endDate == ''){
                    $('#EmployeeEndDate').val(currentDate);
                }
            }
        });
        $('#list-dialog').dialog({
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 400
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
        validWhenEmpty : true
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
    var control_resource = <?php echo (!empty($control_resource) ? 1 : 0); ?>;
    var create_a_project = <?php echo json_encode($this->data['Employee']['create_a_project']);?>;
    var change_status_project = <?php echo json_encode($this->data['Employee']['change_status_project']);?>;
    var delete_a_project = <?php echo json_encode($this->data['Employee']['delete_a_project']);?>;
    var can_communication = <?php echo json_encode($this->data['Employee']['can_communication']);?>;
    var can_see_forecast = <?php echo json_encode($this->data['Employee']['can_see_forecast']);?>;
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
            $('.change_status_project').show();
            $('.delete_a_project').show();
			// neu dang chon role Profile Project Manager thi moi chay cac dong duoi.
            var tempId = $('#EmployeeRoleId').val();
            var _tempId = tempId.split("_");
            if(_tempId[0] && _tempId[1] && _tempId[0] == 'profile'){
                $('.see_budget').hide();
                $('.update_budget').hide();
                $('.sl_budget').hide();
                // $('.update_your_form').hide();
                if(list_profile_name[_tempId[1]]){
                    var value = list_profile_name[_tempId[1]];
					// kiem tra setting trong admin.
                    if((value['can_change_status_project'] && value['can_change_status_project'] == 1) || (change_status_project == 1 )){
                        $('#change_status_project').prop('checked', true);
                    } else {
                        $('#change_status_project').prop('checked', false);
                    }
                    if((value['can_create_project'] && value['can_create_project'] == 1) || (create_a_project == 1 )){
                        $('#create_a_project').prop('checked', true);
                    } else {
                        $('#create_a_project').prop('checked', false);
                    }
                    if((value['can_delete_project'] && value['can_delete_project'] == 1) || (delete_a_project == 1 )){
                        $('#delete_a_project').prop('checked', true);
                    } else {
                        $('#delete_a_project').prop('checked', false);
                    }
                    if((value['can_communication'] && value['can_communication'] == 1)){
                        $('#can_communication').prop('checked', true);
                    } else {
                        $('#can_communication').prop('checked', false);
                    }
                    if((value['can_see_forecast'] && value['can_see_forecast'] == 1)){
                        $('#can_see_forecast').prop('checked', true);
                    } else {
                        $('#can_see_forecast').prop('checked', false);
                    }
                    if((value['create_resource'] && value['create_resource'] == 1) || (control_resource == 1 )){
                        $('#control_resource').prop('checked', true);
                    } else {
                        $('#control_resource').prop('checked', false);
                    }
					//them dieu kien de uncheck khi edit user voi role Profile Project Manager.
					if(!value['update_your_form']){
						$('#update_your_form').prop('checked', false);
						$('.update_your_form').hide();
					}
                }
            }else{
					
				if(tempId == 3){
					if((employeeData['change_status_project'] && employeeData['change_status_project'] == 1) || (change_status_project == 1 )){
                        $('#change_status_project').prop('checked', true);
                    } else {
                        $('#change_status_project').prop('checked', false);
                    }
                    if((employeeData['create_a_project'] && employeeData['create_a_project'] == 1) || (create_a_project == 1 )){
                        $('#create_a_project').prop('checked', true);
                    } else {
                        $('#create_a_project').prop('checked', false);
                    }
                    if((employeeData['delete_a_project'] && employeeData['delete_a_project'] == 1) || (delete_a_project == 1 )){
                        $('#delete_a_project').prop('checked', true);
                    } else {
                        $('#delete_a_project').prop('checked', false);
                    }
                    if((employeeData['can_communication'] && employeeData['can_communication'] == 1)){
                        $('#can_communication').prop('checked', true);
                    } else {
                        $('#can_communication').prop('checked', false);
                    }
                    if((employeeData['can_see_forecast'] && employeeData['can_see_forecast'] == 1)){
                        $('#can_see_forecast').prop('checked', true);
                    } else {
                        $('#can_see_forecast').prop('checked', false);
                    }
                    if(control_resource == 1 ){
                        $('#control_resource').prop('checked', true);
                    } else {
                        $('#control_resource').prop('checked', false);
                    }
					if((employeeData['update_your_form'] && employeeData['update_your_form'] == 1)){
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
    var update_budget = <?php echo json_encode($this->data['Employee']['update_budget']);?>;
    var update_your_form = <?php echo json_encode($this->data['Employee']['update_your_form']);?>;
  
    var see_budget = <?php echo $see_budget ?>;
	$('#EmployeeRoleId').change(can_control_resource);
    can_control_resource();
    window.onload = function() {
        $('#EmployeePassword').val('');
    };
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
    // firt run calulator capacity in hour/day.
    var capacity_hour = <?php echo json_encode($capacity_hour) ?>,
        capacity_minutes = <?php echo json_encode($capacity_minutes) ?>,
        fill_more_than_capacity_day = <?php echo json_encode($fill_more_than_capacity_day) ?>,
        val = parseFloat(parseFloat(capacity_hour*60) + parseFloat(capacity_minutes));
    $('#EmployeeTotalMinutes').val(val);
    $('#EmployeeCapacityInHour, #EmployeeCapacityInMinute').keyup(function(){
        var val1 = $('#EmployeeCapacityInHour').val() ? $('#EmployeeCapacityInHour').val() : 0,
            val2 = $('#EmployeeCapacityInMinute').val() ? $('#EmployeeCapacityInMinute').val() : 0,
            val = parseFloat(parseFloat(val1*60) + parseFloat(val2));
        $('#EmployeeTotalMinutes').val(val);
    });
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
    $(document).ready(function() {
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
    $('#EmployeeTotalMinutes').prop( "disabled", true );
	$('.wd-btn-save').on('click', function(e){
		$('#EmployeeEditForm').submit();
	});
	$('.wd-title-employees').click(function(e){
		$(this).find('.wd-option-employee').toggle();
	}).find('li.employee-item').click(function(e){
		e.preventDefault();
		var parent_ele = $(this).closest('.wd-title-employees');
		var $label = $(this).html();
		var id = $(this).data('id');
		if(id){
			parent_ele.addClass('wd-loading');
			parent_ele.find('.employee-current').empty().append($label);
			parent_ele.find('.wd-option-employee').hide();
			var linkRequest = '/employees_preview/edit/',
			company_id = <?php echo json_encode($company_id) ?>;
			linkRequest = linkRequest + id + '/' + company_id;
			window.location.href = linkRequest;
		}
	});
	$('body').on('click', function(e){
		if(!($(e.target).parents().andSelf().is('.wd-title-employees'))){
			$('.wd-title-employees').find('.wd-option-employee').hide();
		}
		
	});
	 var timeoutID = null, searchHandler = function(){
		var val = $(this).val();
		$('.wd-title-employees .option-content').find('.employee-item span').each(function(){
			var $label = $(this).html();
			$label = $label.toLowerCase();
			val = val.toLowerCase();
			if(!val.length || $label.indexOf(val) != -1 || !val){
				$(this).parent().css('display', 'block');
			} else{
				$(this).parent().css('display', 'none');
			}
		});
	};

	$('.context-employee-filter').find('input').click(function(e){
		e.stopImmediatePropagation();
	}).keyup(function(){
		var self = this;
		clearTimeout(timeoutID);
		timeoutID = setTimeout(function(){
			searchHandler.call(self);
		} , 200);
	});
	
	Dropzone.autoDiscover = false;
    
    $(function() {
        var popupDropzone = new Dropzone("#upload-popup",{
            maxFiles: 1,
            autoProcessQueue: false,
            addRemoveLinks: true,
        });
        popupDropzone.on("success", function(file) {
            popupDropzone.removeFile(file);
        });
        popupDropzone.on("queuecomplete", function(file) {
            location.reload();
        });
        $('#uploadForm').on('submit', function(e){
            $('#uploadForm').parent('.wd-popup').addClass('loading');

            if(popupDropzone.files.length){
                e.preventDefault();
                popupDropzone.processQueue();
            }
        });
        popupDropzone.on('sending', function(file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $('#uploadForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
	$('.wd-popup').on( 'click', '.cancel', function(e){
		e.preventDefault();
		console.log('cancel');
		$('.wd-popup-container .wd-popup').toggleClass('open');
	});
	var switch_activated = $('#switch-activated');
	line_one = <?php echo json_encode($line_one) ?>;
	line_two = <?php echo json_encode($line_two) ?>;
	line_three = <?php echo json_encode($line_three) ?>;
	anonymous = <?php echo json_encode($anonymous) ?>;
	if(switch_activated.length > 0){
		$('#switch-activated').on('click', function(){
			var _this = $(this);
			var _input = _this.find('input');
			console.log(anonymous);
			if(anonymous != 0){
				anonymous = 0;
				_this.find('.icon_anony').css({'display': 'block'});
				_this.find('.icon_anony_off').css({'display': 'none'});
				$('.wd-row').find('.avatar_emp').removeClass('anonymous-img');
				$.ajax({
					url: '<?php echo $html->url(array('controller' => 'employees_preview', 'action' => 'updateEmpAnonymous')); ?>',
					async: true,
					type: 'POST',
					dataType: 'json',
					data: {
						company_id: _input.data('company-id'),
						employee_id: _input.data('employee-id'),
						anonymous : 0
					},
					success: function (data) {
					}
				});
			}else{
				wdConfirmIt({
					title: '',
					content: line_one + '</br>'+ line_two + '</br>'+ line_three,
					width: 500,
					buttonModel: 'WD_TWO_BUTTON',
					buttonText: [
						'<?php __('Yes');?>',
						'<?php __('No');?>'
					],
				},function(){//yes
					anonymous = 1;
					_this.find('.icon_anony').css({'display': 'none'});
					_this.find('.icon_anony_off').css({'display': 'block'});
					$('.wd-row').find('.avatar_emp').addClass('anonymous-img');
					$.ajax({
						url: '<?php echo $html->url(array('controller' => 'employees_preview', 'action' => 'updateEmpAnonymous')); ?>',
						async: true,
						type: 'POST',
						dataType: 'json',
						data: {
							company_id: _input.data('company-id'),
							employee_id: _input.data('employee-id'),
							anonymous : 1
						},
						success: function (data) {
							$('#EmployeeFirstName').val(data.first_name);
							$('#EmployeeLastName').val(data.last_name);
							$('#EmployeeEmail').val(data.email);
							$('#EmployeeWorkPhone').val(data.work_phone);
							$(".avatar_current").attr("src",js_avatar(_input.data('employee-id')));
							$('.empName_current').html(data.first_name +' ' + data.last_name);
						}
					});
				},function(){//no
					// _this.find('.wd-update').removeClass('anonymous-checked');
					// _this.find('.avatar_emp').removeClass('anonymous-img');
				});
			}
		});
	}
	
</script>
<!-- dialog_attachement_or_url -->
<div id="confirm_when_change_pc" class="buttons" style="display: none;">
    <p style="font-size: 13px; color: black; padding-left: 10px;"><?php echo __('You try to modify data being used. If you confirm, the information concerning employee will be changed.', true);?></p>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("No") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Yes') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->

