<?php 
App::import("vendor", "str_utility");
$str_utility = new str_utility();
echo $html->script(array(
	'jquery.multiSelect',
	// 'jshashtable-2.1',
	// 'tinymce/tinymce.min',
	'easytabs/jquery.easytabs.min',
	'jquery-ui.multidatespicker',
	'dropzone.min',
	// 'common',
));
echo $html->css(array(
    'add_popup',
    'jquery.multiSelect',
	'dropzone.min',
	// 'preview/datepicker-new'
    // 'multipleUpload/jquery.plupload.queue',
));
$gapi = GMapAPISetting::getGAPI();
$show_workload = !(empty($adminTaskSetting['Workload'])) ? $adminTaskSetting['Workload'] : 0;
function multiSelect($_this, $fieldName, $fielData, $textHolder, $pc = ''){	
	ob_start();
    ?>
	<div class="wd-multiselect multiselect multiselect-pm">
		<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
			<p class="placeholder">
				<?php echo $textHolder;?>
			</p>
		</a>
		<div class="wd-combobox-content <?php echo $fieldName ;?>" style="display: none;">
			<div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="<?php __('Search');?>" rel="no-history"></span></div>
			<div class="option-content">
					<?php 
					foreach($fielData as $idPm => $e){
						$namePm = $e['full_name'];
						$actif = (int)$e['actif'];
						if( !$actif) continue;
						$avatar = '<img src="' . $_this->UserFile->avatar($idPm) . '" alt="'. $namePm .'"/>';
						?>
						<div class="projectManager wd-data-manager wd-group-<?php echo $idPm ;?>  actif-<?php echo $actif;?> ">
							<p class="projectManager wd-data">
								<?php 
								echo $_this->Form->input($fieldName, array(
									'label' => false,
									'div' => false,
									'type' => 'checkbox',
									'name' => 'data['. $fieldName .'][]',
									'value' => $idPm,
									'id' => 'ProjectProjectEmployeeManager'.$idPm,
								)) ;?>
									
								<span class="option-name" style="padding-left: 5px;"><?php echo $namePm;?> </span>
							</p>
						</div>
					<?php }
					if(!empty($pc)){
						foreach($pc as $idPm => $namePm){
							?>
							<div class="projectManager wd-data-manager wd-group-<?php echo $idPm ;?>">
								<p class="projectManager wd-data">
									<?php echo  $_this->Form->input($fieldName, array(
											'label' => false,
											'div' => false,
											'type' => 'checkbox',
											'name' => 'data['. $fieldName .'][]',
											'value' => $idPm . '-1'
										));						
										?>
									<span class="option-name" style="padding-left: 5px;"><?php echo  $namePm;?></span>
								</p>
							</div>
						<?php }
					};
			?></div>
		</div>
	</div>
    <?php return ob_get_clean();
}?>
<?php if(empty($canAddMoreMax)){?>
	<style>
		.add-resource {
			color: red;
		}
		.add-resource.active {
			border-top: 4px solid red;
		}
		.add-resource.active a {
			color: red;
		}
	</style>
<?php }?>
<div class="add-popup-container">

<div id="template_add_task_prj" class="wd-full-popup" style="display: none;" >
	<div class="wd-popup-inner">
		<div class="template-popup loading-mark wd-popup-container"  id="tab-popup-container">
			<div class="wd-popup-head clearfix">
				<ul class="tabPopup" style="margin: 0; padding: 0;">
					<?php if( !empty($canAddProject)) { ?>
						<li class="liPopup" ><a href="#NewProject"><?php __('Create a project');?> </a></li>
					<?php } 
					if( !empty($canAddTask)) { ?>
						<li class="liPopup" ><a href="#NewTask"> <?php __('Add new task');?>  </a></li>
					<?php } 
					if( !empty($canAddEmployee)) { 
						if(empty($canAddMoreMax)){?>
							<li class="liPopup add-resource" ><a href="#NewResource" title="<?php echo __("All your licences are used", true);?>"> <?php __('Add resource');?>   </a></li>
						<?php }else{?>
							<li class="liPopup" ><a href="#NewResource"> <?php __('Add resource');?>  </a></li>
						<?php }?>
					<?php } ?>
				</ul>
				<a href="javascript:void(0);" class="close_template_add_task_prj wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
				
			</div>
			<div class="template-popup-content wd-popup-content">
				<!--
				-- Create New Project	
				-->
				<?php if( !empty($canAddProject)) { ?>
				<div id="NewProject" class="popup-tab">
					<?php 
					echo $this->Form->create('Project', array(
						'type' => 'POST',
						'url' => array('controller' => 'projects', 'action' => 'add_new_project_popup'),
						'class' => 'form-style-2019',
						'id' => 'ProjectAddPopupForm',
						'data-reload' => 1,
						'data-open-new-link' => 1,
						)
					);

					?>
					<div class="form-head clearfix">
						<h4 class="desc"><?php __d(sprintf($_domain, 'Details'), 'Project details') ?></h4>
						<a href="javascript:void(0);" class="right-link" onclick="cancel_popup(this); show_full_popup('#template_add_project_model');"><?php __('From a model');?></a>
					</div>
					<p style="color: #217FC2" class="form-message"></p>
					<p style="color: red" class="alert-message"></p>
					<?php 
					echo $this->Form->input('open_deatail', array(
						'type'=> 'hidden',
						'value' => 1,						
					));
					echo $this->Form->input('project_name', array(
						'type'=> 'text',
						'id' => 'newPrjProjectName',
						'label' => __d(sprintf($_domain, 'Details'), 'Project Name', true),
						'required' => true,
						'onchange' => 'validateProjectName(this)',
						'rel' => 'no-history',
						'div' => array(
							'class' => 'wd-input label-inline'
						)
					));
					?>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<div class="wd-input wd-area wd-none required">
							<?php 	
								echo multiSelect($this, 'project_employee_manager', $_employees['pm'], __d(sprintf($_domain, 'Details'), 'Project Manager', true));
								?>
							</div>
						</div>
						<div class="wd-col wd-col-sm-6">
							<?php 				
								echo $this->Form->input('project_amr_program_id', array(
									'type'=> 'select',
									'id' => 'newPrjProgram',
									'label' => __d(sprintf($_domain, 'Details', true), 'Program', true),
									'required' => true,
									'rel' => 'no-history',
									'empty' => '',
									'options' => $listAllPrograms,
									'div' => array(
										'class' => 'wd-input label-inline required'
									)
								));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-12">
						<?php 				
							echo $this->Form->input('address', array(
								'type'=> 'text',
								'id' => 'newPrjStatusAddress',
								'label' => __('Address', true),
								'required' => false,
								// 'onchange' => 'validateAddress(this)',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
							));			
							// echo $this->Form->input('latlng', array(
								// 'type'=> 'hidden',
								// 'id' => 'newPrjStatusLatlng',
								// 'rel' => 'no-history',
							// ));	
							?>
						</div>
					</div>
					<div id="popupproject_template_attach" >
						<div class="heading">
						</div> 
						<div class="trigger-upload">
							<div id="wd-prj-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'add_new_project_popup')); ?>" class="dropzone" value="" >
							</div>
						</div>
					</div>
					<div class="wd-row wd-submit-row">
						<div class="wd-col-xs-12">
							<div class="wd-submit">
								<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSaveProject">
									<span><?php __('Create your project') ?></span>
								</button>
								<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
									<?php echo __("Cancel", true); ?></span>
								</a>
							</div>
						</div>
					</div>
					<?php echo $this->Form->end(); ?>
				</div>
				<?php } ?>
				<!--
				-- Create New Task	
				-->
				
				<?php if( !empty($canAddTask)) { ?>
				<div id="NewTask" class="popup-tab">
					<?php 
					echo $this->Form->create('ProjectTask', array(
						'type' => 'POST',
						'url' => array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup'),
						'class' => 'form-style-2019',
						'data-reload' => 0,
						'id' => 'ProjectTaskAddPopupForm'
						)
					);

					?>
					<div class="form-head clearfix">
						<h4 class="desc"><?php __('Task Description'); ?></h4>
						<?php if(empty($companyConfigs['create_ntc_task']) ){?>
						<a href="javascript:void(0);" class="right-link" onclick="cancel_popup(this); show_full_popup('#template_add_nct_task', {width: 'inherit'});">
							<?php __('Add a non-continuous task');?>
						</a>
						<?php }?>
					</div>
					<p style="color: #217FC2" class="form-message"></p>
					<p style="color: red" class="alert-message"></p>
					<?php 
					if( $show_workload) { ?>
						<div class="wd-row">
							<div class="wd-col wd-col-md-7">
					<?php } 
					echo $this->Form->input('return', array(
						'data-return' => 'form-return',
						'type'=> 'hidden',
						'value' => $this->Html->url(),
						
					));
					echo $this->Form->input('run_staffing', array(
						'type'=> 'hidden',
						'value' => 1,						
					));
					echo $this->Form->input('project_id', array(
						'type'=> 'select',
						'id' => 'toProject',
						'label' => __d(sprintf($_domain, 'Details'), 'Project Name', true),
						'options' => $listProjectbyPM,
						'required' => true,
						'rel' => 'no-history',
						'onchange'=> 'popupTask_projectOnChange(this);',
						'empty' => '',
						'div' => array(
							'class' => 'wd-input label-inline'
						)
					));
					?>
					<div class="required label-inline wd-input">
						<label for="toPhase"> <?php __('Phase');?></label>
						<select name="data[ProjectTask][project_planed_phase_id]" id="toPhase" required="1" rel ="no-history" disabled onchange="popupTask_phaseOnChange(this);">
							<option value=""></option>
						</select>
					</div>
					<?php
					echo $this->Form->input('task_title', array(
						'type'=> 'text',
						'id' => 'newTaskName',
						'label' => __('Task name', true),
						'required' => true,
						'rel' => 'no-history',
						'div' => array(
							'class' => 'wd-input label-inline'
						)
					));
					?>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<div class="wd-input wd-area wd-none">
								<!-- <label><?php __('Assign to');?></label> -->
								<div class="wd-multiselect multiselect multiselect-pm" id="multiselect-pm">
									<a href="javascript: void(0);" class="wd-combobox wd-project-manager disable">
										<p>
											<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
										</p>
									</a>
									<div class="wd-combobox-content task_assign_to_id" style="display:none;">
										<div class="context-menu-filter">
											<span>
												<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
											</span>
										</div>
										<div class="option-content"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="wd-col wd-col-sm-6">
						<?php 
							echo $this->Form->input('task_status_id', array(
								'type'=> 'select',
								'id' => 'newTaskStatus',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
								'options' => $listAllStatus,
								'required' => true,
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
							));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<div class="wd-input wd-area wd-none">
							<?php 				
								echo $this->Form->input('task_start_date', array(
									'type'=> 'text',
									'id' => 'newTaskStartDay',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
									// 'required' => true,
									'class' => 'wd-date',
									'onchange'=> 'validated(this);',
									'autocomplete' => 'off',
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
								?>
							</div>
						</div>
						<div class="wd-col wd-col-sm-6">
						<?php 				
							echo $this->Form->input('task_end_date', array(
								'type'=> 'text',
								'id' => 'newTaskEndDay',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
								// 'required' => true,
								'class' => 'wd-date',
								'autocomplete' => 'off',
								'onchange'=> 'validated(this);',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
							));
							?>
						</div>
					</div>
					<div id="popup_task_template_attach" >
						<div class="heading">
						</div> 
						<div class="trigger-upload">
							<div id="wd-task-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup')); ?>" class="dropzone" value="" >
							</div>
						</div>
					</div>
					<?php if( $show_workload) { ?>
						</div>
						<div class="wd-col wd-col-md-5">
							<div class="task-workload">
								<table id="task_assign_table" class="nct-assign-table">
									<thead>
										<tr>
											<td class="bold base-cell null-cell"><?php __('Resources');?> </td>
											<td class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
									<tfoot>
										<tr>
											<td class="base-cell"><?php __('Total') ?></td>
											<td class="base-cell total-consumed" id="total-c_consumed">0</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="wd-row wd-submit-row">
						<div class="wd-col-xs-12">
							<div class="wd-submit">
								<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSaveTask">
									<span><?php __('create your task') ?></span>
								</button>
								<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
									<?php echo __("Cancel", true); ?></span>
								</a>
							</div>
						</div>
					</div>
					<?php echo $this->Form->end(); ?>
				</div>
				<?php } ?>
				
				
				<?php if( !empty($canAddEmployee)) { ?>
				<!--
				-- Create New Resource	
				-->
				<div id="NewResource" class="popup-tab">
					<?php 
						echo $this->Form->create('Employee', array(
							'type' => 'POST',
							'url' => array('controller' => 'employees', 'action' => 'add'),
							'class' => 'form-style-2019',
							'data-reload' => 0,
							)
						);

					?>
					<div class="form-head clearfix">
						<h4 class="desc"><?php __('Resourcce Description'); ?></h4>
					</div>
					<p style="color: #217FC2" class="form-message"></p>
					<p style="color: red" class="alert-message"></p>
					<?php 
						echo $this->Form->input('return', array(
							'data-return' => 'form-return',
							'type'=> 'hidden',
							'value' => $this->Html->url(),
						));
					?>
					
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<?php
								echo $this->Form->input('first_name', array(
									'type'=> 'text',
									'id' => 'newEmpFirstname',
									'label' => __('First name', true),
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
							?>
						</div>
						<div class="wd-col wd-col-sm-6">
							<?php
								echo $this->Form->input('last_name', array(
									'type'=> 'text',
									'id' => 'newEmpLastname',
									'label' => __('Last name', true),
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<?php
								foreach($roles as $key => $val){
									$roles[$key] = __($val, true);
								}
								echo $this->Form->input('role_id', array(
									'type'=> 'select',
									'id' => 'newEmpRole',
									'label' => __('Role', true),
									'required' => true,
									'rel' => 'no-history',
									'options' => $roles,
									'selected' => 3,
									'div' => array(
										'class' => 'wd-input label-inline required'
									)
								));
							?>
						</div>
						<div class="wd-col wd-col-sm-6">
							<?php
								echo $this->Form->input('code_id', array(
									'type'=> 'text',
									'id' => 'newEmpCode',
									'label' => __('Unique identifier', true),
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<?php 
								echo $this->Form->input('email', array(
									'type'=> 'text',
									'id' => 'newEmpEmail',
									'label' => __('E-mail', true),
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline required'
									)
								));
							?>
						</div>
						<div class="wd-col wd-col-sm-6">
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
								echo $this->Form->input('profit_center_id', array(
									'type'=> 'select',
									'id' => 'newEmpPC',
									'label' => __('Profit Center', true),
									'options' => $arr_new + $arr_tam,
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<?php 
								echo $this->Form->input('capacity_by_year', array(
									'type'=> 'text',
									'id' => 'newEmpCapacity',
									'label' => __('Capacity', true). '/' . __('Year', true) ,
									// 'required' => true,
									'rel' => 'no-history',
									'value' => $default_user_profile['capacity_by_year'],
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
							?>
						</div>
						<div class="wd-col wd-col-sm-6">
							<?php 
								echo $this->Form->input('tjm', array(
									'type'=> 'text',
									'id' => 'newEmpTJM',
									'label' => __('Average Daily Rate', true),
									'required' => true,
									'rel' => 'no-history',
												'value' => $default_user_profile['tjm'],
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-6">
							<div class="wd-input wd-area wd-none">
							<?php 				
								echo $this->Form->input('start_date', array(
									'type'=> 'text',
									'id' => 'newEmpStartDay',
									'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
									'required' => true,
									'class' => 'wd-date',
									'onchange'=> 'newEmp_validated(this);',
									'autocomplete' => 'off',
									'rel' => 'no-history',
									'value' => date('d-m-Y',time()),
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
								?>
							</div>
						</div>
						<div class="wd-col wd-col-sm-6">
						<?php 				
							echo $this->Form->input('end_date', array(
								'type'=> 'text',
								'id' => 'newEmpEndDay',
								'label' =>  __d(sprintf($_domain, 'Details'), 'End Date', true),
								// 'required' => true,
								'class' => 'wd-date',
								'autocomplete' => 'off',
								'onchange'=> 'newEmp_validated(this);',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
							));
							?>
						</div>
					</div>
					<!--QuanNV update actif. 13/07/2019-->
					<div class="wd-row hidden">
						<?php 
								echo $this->Form->input('actif', array(
									'type'=> 'text',
									'id' => 'actif',
									'value' => '1',
									'label' => __('Actif', true),
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline hidden'
									)
								));
							?>
					</div>
					<div class="wd-row wd-submit-row">
						<div class="wd-col-xs-12">
							<div class="wd-submit">
								<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSaveEmployee">
									<span><?php __('Create the resource') ?></span>
								</button>
								<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
									<?php echo __("Cancel", true); ?></span>
								</a>
							</div>
						</div>
					</div>
					<?php echo $this->Form->end(); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<div id="template_add_project_model" class="wd-full-popup" style="display: none;" >
	<div class="wd-popup-inner">
		<div class="project_model-popup loading-mark wd-popup-container">
			<div class="wd-popup-head clearfix">
				<div class="popup-back"><a href="javascript:void(0);" onclick="back_to_last_popup(this);"><i class="icon-arrow-left"></i><span> <?php __('Back');?> </span></a></div>
			</div>
			<div class="project_model-popup-content wd-popup-content">
				<?php if(!empty( $model_projects)){?>
					<div id="produp">
						<?php echo $form->create("Project",array(
							'action'=>'duplicate',
							'enctype' => 'multipart/form-data',
							'class' => 'form-style-2019',
							'id' => 'ModelProject'
						));?>
						
						<div class="form-head clearfix">
							<h4 class="desc"><?php __('Duplicate a project from a template'); ?></h4>
						</div>
						<div class="model-table">
							<p class="p_name"><?php __('Project name') ?></p>
							<ul class="list-model-project">
						<?php 
						$i = 1; 
						foreach ($model_projects as $project){ ?>
							<li class="project">
								<div class="num"><?php echo $i; ?></div>
								<div class="name"><?php echo $project['Project']['project_name'];?></div>
								<div class="radioselect">
									<div class="input wd-input wd-radio-button">
										<input type="radio" value="<?php echo $project['Project']['id'] ?>|<?php echo $project['Project']['project_name']?>" name="data[Project][duplicate]" id="ModelSelect-<?php echo $project['Project']['id'];?>"/>
										<label for="ModelSelect-<?php echo $project['Project']['id'];?>">
										</label>
									</div>
								</div>
							</li>
						<?php $i++;
						} ?> 
							</ul>
						</div>
						<div class="wd-row wd-submit-row">
							<div class="wd-col-xs-12">
								<div class="wd-submit">
									<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSaveDuplicateProject">
										<span><?php __('duplicate your project') ?></span>
									</button>
									<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
										<?php echo __("Cancel", true); ?></span>
									</a>
								</div>
							</div>
						</div>						
						<?php echo $form->end()?>
					</div>
					 
				<?php }else{ ?>
					<div class="empty-model" style="height: 40px; margin: 40px 0; text-align: center;">
						<p class="empty-text"><?php __('No model project found');?> </p>
					</div>
				<?php } ?> 
				
			</div>
		</div>
	</div>
</div>
<?php if( $canAddTask){?>
<!--
-- Create New NCT Task	
-->
<div id="template_add_nct_task" class="wd-full-popup autosize-popup" style="display: none;" >
	<div class="wd-popup-inner">
		<div class="add_nct_task-popup loading-mark wd-popup-container">
			<div class="wd-popup-head clearfix">
				<div class="popup-back"><a href="javascript:void(0);" onclick="back_to_last_popup(this);"><i class="icon-arrow-left"></i><span> <?php __('Back');?> </span></a></div>
			</div>
			<div class="add_nct_task-popup-content wd-popup-content">
				<?php
				echo $this->Form->create('NCTTask', array(
						'type' => 'POST',
						'url' => array('controller' => 'project_tasks', 'action' => 'saveNcTask'),
						'class' => 'form-style-2019',
						'data-reload' => 0,
						'id' => 'NCTTaskAddPopupForm'
					));
				
				?>
				<div class="form-head clearfix">
					<h4 class="desc"><?php __('Task Description'); ?></h4>
				</div>
				<p style="color: #217FC2" class="form-message"></p>
				<p style="color: red" class="alert-message"></p>
				<div class="wd-row wd-row-inline">
					<div class="wd-inline-col wd-col-530">
						<?php
						echo $this->Form->input('return', array(
							'data-return' => 'form-return',
							'type'=> 'hidden',
							'value' => $this->Html->url(),
							
						));
						echo $this->Form->input('run_staffing', array(
							'type'=> 'hidden',
							'value' => 1,						
						));
						echo $this->Form->input('id', array(
							'type'=> 'select',
							'id' => 'nct-project_id',
							'label' => __('Project name', true),
							'options' => $listProjectbyPM,
							'required' => true,
							'rel' => 'no-history',
							'onchange'=> 'nct_projectOnChange(this);',
							'empty' => '',
							'div' => array(
								'class' => 'wd-input label-inline required'
							)
						));
						echo $this->Form->input('task.project_planed_phase_id', array(
							'type'=> 'select',
							'id' => 'nct-phase',
							'label' => __('Phase', true),
							'empty' => '',
							'required' => true,
							'onchange'=> 'nct_phaseOnChange(this);',
							'disabled' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required'
							)
						));
						echo $this->Form->input('task.task_title', array(
							'type'=> 'text',
							'id' => 'nct-name',
							'label' => __('Task name', true),
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required'
							)
						));
						?>
						<div class="wd-input wd-area wd-none required">
							<div class="wd-multiselect multiselect multiselect-pm" id="multiselect-nct-pm">
								<a href="javascript: void(0);" class="wd-combobox wd-project-manager disable">
									<p>
										<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
									</p>
								</a>
								<div class="wd-combobox-content task_assign_to_id" style="display:none;">
									<div class="context-menu-filter">
										<span>
											<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
										</span>
									</div>
									<div class="option-content"></div>
								</div>
							</div>
						</div>
						<div class="wd-row">
							<div class="wd-col wd-col-sm-6">
								<?php 
								echo $this->Form->input('task.task_status_id', array(
									'type'=> 'select',
									'id' => 'nct-status',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
									'options' => $listAllStatus,
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline required'
									)
								));
								?>
							</div>
							<div class="wd-col wd-col-sm-6">
								<?php 
								$periods = array( __('Day',true), __('Week',true), __('Month',true)/* , __('Period',true )*/);
								echo $this->Form->input('type', array(
									'type'=> 'select',
									'id' => 'nct-range-type',
									'label' => __('Period', true),
									'options' => $periods,
									'required' => true,
									'rel' => 'no-history',
									'selected' => 2,
									'div' => array(
										'class' => 'wd-input label-inline has-val required'
									)
								));
								?>
							</div>
						</div>
						<div class="wd-row">
							<div class="wd-col wd-col-sm-6">
								<div class="wd-input wd-area wd-none">
								<?php 				
									echo $this->Form->input('task.task_start_date', array(
										'type'=> 'text',
										'id' => 'nct-start-date',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
										// 'required' => true,
										'class' => 'wd-date',
										'onchange'=> 'validated(this);',
										'autocomplete' => 'off',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline required'
										)
									));
									?>
								</div>
							</div>
							<div class="wd-col wd-col-sm-6">
								<div class="input-icon-container">
									<?php 				
									echo $this->Form->input('task.task_end_date', array(
										'type'=> 'text',
										'id' => 'nct-end-date',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
										// 'required' => true,
										'class' => 'wd-date',
										'autocomplete' => 'off',
										'onchange'=> 'validated(this);',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline required'
										)
									));
									?>
									<div class="wd-icon">
										<a href="javascript:void(0);" id="create-range" class="btn"><i class="icon-reload"></i></a>
									</div>
								</div>
							</div>
						</div>
						<div class="wd-row">
							<div class="wd-col wd-col-lg-6">
								<?php 
								if( !empty($adminTaskSetting['Profile'])){
									echo $this->Form->input('task.profile_id', array(
										'type'=> 'select',
										'id' => 'nct-profile',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Profile', true),
										'options' => $projectProfiles,
										'required' => false,
										'rel' => 'no-history',
										'empty' => '',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
								}
								if( !empty($adminTaskSetting['Priority'])){
									echo $this->Form->input('task.task_priority_id', array(
										'type'=> 'select',
										'id' => 'nct-priority',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Priority', true),
										'options' => $projectPriorities,
										'required' => false,
										'rel' => 'no-history',
										'empty' => '',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
								}
								if( !empty($adminTaskSetting['Milestone'])){
									echo $this->Form->input('task.milestone_id', array(
										'type'=> 'select',
										'id' => 'nct-milestone',
										'label' => __('Milestone', true),
										'options' => '',
										'required' => false,
										'rel' => 'no-history',
										'empty' => '',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
								}
								if( !empty($adminTaskSetting['Unit Price'])){ 
									echo $this->Form->input('task.unit_price', array(
										'type'=> 'text',
										'id' => 'nct-unit-price',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Unit Price', true),
										'required' => false,
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
								} 
								?>
								<div class="input-icon-container">
									<?php
									echo $this->Form->input('per-workload', array(
										'type'=> 'text',
										'id' => 'per-workload',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
										'required' => false,
										'class' => 'wd-date',
										'autocomplete' => 'off',
										// 'onchange'=> 'validated(this);',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
									?>
									<div class="wd-icon">
										<a href="javascript:void(0);" id="fill-workload" class="btn">
											<i class="icon-reload"></i>
										</a>
									</div>
								</div>
							</div>
							<div class="wd-col wd-col-lg-6 selectDate-container">
								<div class="input-icon-container">
									<div class="wd-input-group">
										<div class="period-input" style="display: non e;">
											<?php
											echo $this->Form->input('period_start_date', array(
												'type'=> 'text',
												'id' => 'nct-period-start-date',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
												'required' => false,
												'class' => 'wd-date text period-input-calendar nct-date-period',
												'autocomplete' => 'off',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
											echo $this->Form->input('period_end_date', array(
												'type'=> 'text',
												'id' => 'nct-period-end-date',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
												'required' => false,
												'class' => 'wd-date text period-input-calendar nct-date-period',
												'autocomplete' => 'off',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
											?>
										</div>
									</div>
								</div>
								<div class="input-icon-container" id="nct-range-picker-container">
									<div id="nct-range-picker" class="range-picker"></div>
									<div class="wd-input-group">
									</div>
									<div class="wd-icon">
										<a id="add-range" href="javascript:;" class="btn-text range-picker">
											<i class="icon-reload"></i>
										</a>
									</div>
								</div>
								<div class="input-icon-container" id="date-list-container">
									<div class="wd-input-group">
										<div id="date-list" class="start-end"></div>
									</div>
									<div class="wd-icon">
										<a id="add-date" href="javascript:;" class="btn-text date-picker">
											<i class="icon-reload"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="wd-col">
							<div id="popup_nct_template_attach" class="wd-hide">
								<div class="heading">
								</div> 
								<div class="trigger-upload">
									<div id="wd-nct-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks', 'action' => 'saveNcTask')); ?>" class="dropzone" value="" >
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="wd-inline-col wd-col-autosize">
						<div class="wd-row">
							<div class="wd-col">
								<div id="assign-list">
									<table id="nct-assign-table" class="nct-assign-table">
										<thead>
											<tr>
												<td class="bold base-cell null-cell">&nbsp;</td>
												<td class="base-cell" id="abcxyz"><?php __('Consumed'); ?> (<?php __('In Used'); ?>)</td>
												<td class="base-cell row-action"></td>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<td class="base-cell"><?php __('Total') ?></td>
												<td class="base-cell" id="total-consumed">0</td>
												<td class="base-cell row-action"></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="wd-row wd-submit-row">
					<div class="wd-col-xs-12">
						<div class="wd-submit">
							<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSaveNCTTask">
								<span><?php __('create your task') ?></span>
							</button>
							<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
								<?php echo __("Cancel", true); ?></span>
							</a>
						</div>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
				
			</div>
		</div>
	</div>
</div>
</div>

<!--
-- END Create New NCT Task	
-->
<?php } ?>  
<script>
var show_workload = <?php echo json_encode($show_workload); ?>; 
if( $('#tab-popup-container').find( '.tabPopup .liPopup').length){
	$('#tab-popup-container').easytabs({
		'tabs' : '.tabPopup .liPopup',
	});
}
$('#tab-popup-container').bind('easytabs:before', function() {
	$('.wd-popup-content').css('overflow', 'hidden');
});
$('#tab-popup-container').bind('easytabs:after', function() {
	$('.wd-popup-content').css('overflow', '');
});
$('#tab-popup-container').bind('easytabs:midTransition', function() {
    if( $('#NewTask').hasClass('active')){
		var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_add_task_prj', {width: popup_width});
	}else{
		var popup_width = 580;
		show_full_popup( '#template_add_task_prj', {width: popup_width});
	}
});

var text_by = <?php echo json_encode(  __('by', true) ) ?>;
var text_modified = <?php echo json_encode( __('Modified', true) ) ?>;
var isTablet = <?php echo json_encode($isTablet) ?>;
var isMobile = <?php echo json_encode($isMobile) ?>;
var curent_time = <?php echo json_encode(time()) ?>;

Dropzone.autoDiscover = false;
$("#newTaskEndDay, #newTaskStartDay, #newEmpStartDay, #newEmpEndDay").datepicker({
	dateFormat      : 'dd-mm-yy'
});

/* For Add project Tab*/
<?php if( $canAddProject) { ?>
	var listAllProjects = <?php echo json_encode( !empty($listAllProjects) ? array_values($listAllProjects) : array() )?>;
	
	function validateProjectName(elm){
		var _this = $(elm);
		var val = _this.val();
		_this.addClass('loading').removeClass('invalid');
		if( $.inArray(val, listAllProjects) != -1){
			_this.addClass('invalid');
			_this.closest('form').find('.alert-message').html("<?php __('The project already exists');?>");
		}else{
			_this.closest('form').find('.alert-message').html('');
		}
		_this.removeClass('loading');
		
	}	
	function validate_ProjectAddPopupForm(){
		var validate = true;
		var _form = $('#ProjectAddPopupForm');
		// get PM list
		var has_pm_selected = 0;
		var _PMs = _form.find( 'input[name="data[project_employee_manager][]"]' );
		var _mul_tag = _PMs.first().closest('.wd-multiselect').find('.wd-combobox');
		
		$.each(_PMs, function(ind, pm){
			if( $(pm).is(':checked') ){
				has_pm_selected = 1;
			}
		});
		if( !has_pm_selected) {
			_mul_tag.addClass('invalid');
			_form.find('.alert-message').html('<?php __('Please select at least one Project manager');?>');
		}
		else{
			_mul_tag.removeClass('invalid');
			_form.find('.alert-message').html('');
		}
		 var invalid_input = $('#ProjectAddPopupForm').find('.invalid');
		 if (invalid_input.length) validate = false;
		 
		 return validate;
	}
<?php } ?>
/* End For Add project Tab*/

/* for Add new Employee */
<?php if( $canAddEmployee) { ?>
	var default_user_profile = <?php echo json_encode($default_user_profile);?>;
	var newEmp_date_validated = 1;
    function newEmp_validated(_this){
        var st_date = $('#newEmpStartDay').val();
        var en_date = $('#newEmpEndDay').val();
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');

        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).addClass('invalid');
            newEmp_date_validated = 0;
        }else{
            $('#newEmpStartDay').removeClass('invalid');
            $('#newEmpEndDay').removeClass('invalid');
            newEmp_date_validated = 1;
        }
    };
	$('#newEmpFirstname, #newEmpLastname').on('change keyup', function(){
		if( $('#newEmpEmail').hasClass('edited') ) return;
		var email = '';
		var first_name = $('#newEmpFirstname').val();
		email += ( default_user_profile.first_name == "1") ? first_name : '';
		var first_letter = first_name ? first_name[0] : '';
		email += ( default_user_profile.first_letter_first_name == "1") ? first_letter : '';
		email += default_user_profile.sperator;
		var last_name = $('#newEmpLastname').val();
		email += ( default_user_profile.last_name == "1") ? last_name : '';
		first_letter = last_name ? last_name[0] : '';
		email += ( default_user_profile.first_letter_last_name == "1") ? first_letter : '';
		email += default_user_profile.domain_name;
		$('#newEmpEmail').val(email).trigger('change');
		
	});
<?php } ?>
/* END for Add new Employee */


/* for Add new Task */

<?php if( $canAddTask) { ?>	

	var listPhases = <?php echo json_encode($listPhases) ?>;
	var adminTaskSetting = <?php echo json_encode($adminTaskSetting);?>;
	var newtaskIndexForm_date_validated = 1;
    function validated(_this){
        var st_date = $('#newTaskStartDay').val();
        var en_date = $('#newTaskEndDay').val();
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');

        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).addClass('invalid');
            newtaskIndexForm_date_validated = 0;
        }else{
            $('#newTaskStartDay').removeClass('invalid');
            $('#newTaskEndDay').removeClass('invalid');
            newtaskIndexForm_date_validated = 1;
        }
    };
	
	function disable_all_option(){
		$('#toPhase').val('').prop( "disabled", true );
		$('#multiselect-pm .option-content').empty();
		init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
		
	}
	function popupTask_phaseOnChange(){
		var _project_id = $('#toProject').val();
		var _phase_id = $('#toPhase').val();
		if( _phase_id == '') {
			$('#newTaskStartDay').datepicker('setDate', '');
			$('#newTaskEndDay').datepicker('setDate', '');
			return;
		}	
		if(_project_id && _phase_id){
			var phase = listPhases[_project_id][_phase_id];
			var start_date = phase.phase_real_start_date ? phase.phase_real_start_date : '';
			var end_date = phase.phase_real_end_date ? phase.phase_real_end_date : '';
			$('#newTaskStartDay').datepicker('setDate', start_date);
			$('#newTaskEndDay').datepicker('setDate', end_date);
		}
		$('#newTaskStartDay').trigger('change');
		$('#newTaskEndDay').trigger('change');
	}
	function popupTask_projectOnChange(){
		var _project_id = $('#toProject').val();
		if( _project_id == '') {
			disable_all_option();
			return;
		}
		var _options = '';
		_options += '<option value=""></option>';
		$('#toPhase').addClass('loading').prop( "disabled", false ).val('');
		if( listPhases[_project_id]){
			$.each( listPhases[_project_id], function (pid, phase){
				_options += '<option value="' + pid + '" data-projectid="' + _project_id + '">' + phase.name + '</option>';
			});
		}
		$('#toPhase').html(_options );
		$('#toPhase').removeClass('loading');
		ajaxGetResources(_project_id);
		
	}
	function ajaxGetResources(_project_id){
		$('#multiselect-pm').addClass('loading');
		$('#multiselect-pm >.wd-combobox a.circle-name').remove();
		$('#multiselect-pm >.wd-combobox p').show();
		var _cont = $('#multiselect-pm .option-content');
		_cont.empty();
		$.ajax({
			url: '/projects/getTeamEmployees/'+_project_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
				if( data.success == true){
					var _cont = $('#multiselect-pm .option-content');
					var _html = '';
					$.each(data.data, function(ind, emp){
						emp = emp.Employee;
						var e_actif = (typeof emp.actif !== "undefined") ? emp.actif : 1;
						_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + ' actif-'+ e_actif +'">';
						_html += '<p class="projectManager wd-data">';
						_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
						_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
						_html += '</p> </div>';
					});
					_cont.html(_html);
					init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
					$('#multiselect-pm').removeClass('loading');
				}
			}
		});
	}
	/* NCT task */
	var startDate, endDate;
    var listDeletion = [];
    var Holidays = {};  //get by ajax
    var Workdays = <?php echo json_encode($workdays) ?>;
	var monthName = <?php echo json_encode(array(
		__('January', true),
		__('February', true),
		__('March', true),
		__('April', true),
		__('May', true),
		__('June', true),
		__('July', true),
		__('August', true),
		__('September', true),
		__('October', true),
		__('November', true),
		__('December', true)
	)); ?>;
	var nct_list_assigned = {};
	var per_workload = 0;
	function loadHolidays(year){
		if( typeof Holidays[year] == 'undefined' ){
			$.ajax({
				url: '<?php echo $this->Html->url('/') ?>holidays/getYear/' + year,
				dataType: 'json',
				success: function(data){
					Holidays[year] = data;
					applyDisableDays();
				}
			});
		}
	}
	function applyDisableDays(){
		hlds = [];
		$.each(Holidays, function(year, list){
			hlds = hlds.concat(list);
		});
		if( hlds.length ){
			$('#date-list').multiDatesPicker('addDates', hlds, 'disabled');
		}
	}
	function refreshPicker(){
		var start = $('#nct-start-date').datepicker('getDate'),
			end = $('#nct-end-date').datepicker('getDate');
		if( start && end && start <= end ){
			$('#date-list').datepicker('setDate', start);
			if( nct_list_assigned.length > 0 ){
				$('#add-date').removeClass('disabled');
				$('#add-range').removeClass('disabled');
			}
			resetRange();
			$('#nct-period-start-date, #nct-period-end-date').prop('disabled', false);
		}
		else {
			$('#add-date').addClass('disabled');
			$('#add-range').addClass('disabled');
			$('#nct-period-start-date, #nct-period-end-date').prop('disabled', true);
		}
		if( !start ) year = new Date().getFullYear();
		else year = start.getFullYear();
		loadHolidays(year);
		resetPicker();
		$('#date-list').datepicker('refresh');
		$('#nct-range-picker').datepicker('refresh');
	}
	//if date in disabled fields, reject
	function isValidDate(d, start, end){
		var date = d.getDay();
		return d >= start && d <= end && Workdays[date] == 1;
	}
	function getValidDate(){
		var result = [];
		var dates = $('#date-list').multiDatesPicker('getDates', 'object');
		var start = $('#nct-start-date').datepicker('getDate'),
			end = $('#nct-end-date').datepicker('getDate');
		for(var i = 0; i < dates.length; i++){
			if( isValidDate(dates[i], start, end) ){
				var date = dates[i].getDate(),
					month = dates[i].getMonth() + 1;
				result.push( (date < 10 ? '0' + date : date) + '-' + (month < 10 ? '0' + month : month) + '-' + dates[i].getFullYear() );
			}
		}
		return result;
	}
	function draw_employee_columns(){
		$('.ntc-employee-col').remove();
		if(nct_list_assigned.length == 0){
			return;
		}
		$.each(nct_list_assigned, function(ind, emp){
			//QuanNV update by 08/06/2019
			var id = '';
			var is_profit_center = '';
			if(emp){
				var employee = (emp.id).split('-');
				id = employee[0];
				is_profit_center = employee[1]==1 ? 1 : 0;
			}
			
			var name = emp.name;
			var cell = $('.cell-' + id);
			//add header
			var _avt = '<span style="margin-bottom: 0px;margin-top: 10px;" class="circle-name" title="' + name + '" data-id="' + id + '-' + is_profit_center + '">';
			if(is_profit_center == 1){
				_avt += '<i class="icon-people"></i>';
			}else{
				_avt += '<img width = 35 height = 35 src="'+  js_avatar(id ) +'" title = "'+ name +'" />';	
			}
			_avt += '</span>';
			_avt += '<span class="header-name" title="' + name + '" >'+ name.replace(/^PC \/ /, '') +'</span>';
			id = is_profit_center ? (id + '-1') : (id + '-0');
			var html = '<td class="value-cell header-cell cell-' + id + ' ntc-employee-col" id="col-' + id + '" data-id="' + id + '">' + _avt + '</td>';
			
			//End Update
			
			$(html).insertBefore('#abcxyz');
			//add content
			$('.nct-date').each(function(){
				var ciu = $(this).parent().find('.ciu-cell'),
					date = $(this).text();
					date = $(this).prop('id').replace('date-', '');;
				var _id = id.split('-');
				var e_id=_id[0],
					e_ispc=_id[1]==1 ? 1: 0,
					ip_name = 'data[workloads][' + date + '][' + id + ']';
				$('<td class="value-cell cell-' + id + ' ntc-employee-col"><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="ntc-employee-col workload workload-' + id + ' ntc-employee-col" data-id="' + id + '" value="0" name="' + ip_name + '[estimated]" onchange="changeTotal(this)" data-ref/></td>').insertBefore(ciu);
			});
			bindNctKeys();
			//add footer
			$('<td class="value-cell cell-' + id + ' ntc-employee-col" id="foot-' + id + '" data-id="' + id + '">0.00</td></tr>').insertBefore('#total-consumed');
			$.each( $('.wd-row-inline'), function (i, _row){
				var _row = $(_row);
				var _width = 0;
				$.each( _row.children(), function( j, _col){
					_width += $(_col).width()+41;
				});
				_row.width(_width);
			});
			
		});
		refreshPicker();
		calculateTotal();
	}
	Date.prototype.format = function(format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, this);
    }
	function nct_disable_all_option(){
		$('#nct-phase').val('').prop( "disabled", true );
		$('#multiselect-nct-pm .option-content').empty();
		init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
		
	}
	function nct_ajaxGetResources_Milestone(_project_id){
		$('#multiselect-nct-pm').addClass('loading');
		$('#multiselect-nct-pm >.wd-combobox a.circle-name').remove();
		$('#multiselect-nct-pm >.wd-combobox p').show();
		var _cont = $('#multiselect-nct-pm .option-content');
		_cont.empty();
		$.ajax({
			url: '/projects/getTeamEmployees/'+_project_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
				if( data.success == true){
					var _cont = $('#multiselect-nct-pm .option-content');
					var _html = '';
					$.each(data.data, function(ind, emp){
						emp = emp.Employee;
						var e_actif = (typeof emp.actif !== "undefined") ? emp.actif : 1;
						_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + ' actif-'+ e_actif +'">';
						_html += '<p class="projectManager wd-data">';
						_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
						_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
						_html += '</p> </div>';
					});
					_cont.html(_html);
					init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
				}
			},
			complete: function(){
				$('#multiselect-nct-pm').removeClass('loading');
			}			
		});
		if( adminTaskSetting.Milestone == "1"){
			$('#nct-milestone').addClass('loading');
			$('#nct-milestone').html('');
			$.ajax({
				url: '/project_milestones/get_list_milestone/'+_project_id,
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if( data.length){
						var sel = '<option value=""></option>';
						$.each(data, function(ind, mil){
							sel += '<option value="'+ mil.ProjectMilestone.id +'">' + mil.ProjectMilestone.project_milestone+ '</option>';
						});
						$('#nct-milestone').html(sel);
					}
				},
				complete: function(){
					$('#nct-milestone').removeClass('loading');
				}
			});
		}
	}
	function nct_projectOnChange(){
		var _project_id = $('#nct-project_id').val();
		if( _project_id == '') {
			nct_disable_all_option();
			return;
		}
		var _options = '';
		_options += '<option value=""></option>';
		$('#nct-phase').addClass('loading').prop( "disabled", false ).val('');
		if( listPhases[_project_id]){
			$.each( listPhases[_project_id], function (pid, phase){
				_options += '<option value="' + pid + '" data-projectid="' + _project_id + '">' + phase.name + '</option>';
			});
		}
		$('#nct-phase').html(_options );
		$('#nct-phase').removeClass('loading');
		nct_ajaxGetResources_Milestone(_project_id);
	}
	function nct_phaseOnChange(){
		var _project_id = $('#nct-project_id').val();
		var _phase_id = $('#nct-phase').val();
		if( _phase_id == '') {
			$('#nct-start-date').datepicker('setDate', '');
			$('#nct-end-date').datepicker('setDate', '');
		}	
		if(_project_id && _phase_id){
			var phase = listPhases[_project_id][_phase_id];
			var start_date = phase.phase_real_start_date ? phase.phase_real_start_date : '';
			var end_date = phase.phase_real_end_date ? phase.phase_real_end_date : '';
			$('#nct-start-date').datepicker('setDate', start_date);
			$('#nct-end-date').datepicker('setDate', end_date);
		}
		$('#nct-start-date').trigger('change');
		$('#nct-end-date').trigger('change');
		if(typeof refreshPicker == 'function') refreshPicker();
	}
	/* OLD 
	function selectRange(){
        var val = parseInt($('#nct-range-type').val());
        switch(val){
            case 1:
            case 2:
                $('#date-list-container').hide();
                $('#nct-range-picker-container').show();
                $('.period-input').hide();

            break;
            case 3:
                $('#date-list-container').hide();
                $('#nct-range-picker-container').hide();
                $('.period-input').show();
            break;
            default:
                $('#date-list-container').show();
                $('#nct-range-picker-container').hide();
                $('.period-input').hide();
            break;
        }
        if( val ==3){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        resetRange();
        if( !$('#nct-assign-table tbody tr').length ){
            $('#nct-assign-table tfoot .value-cell').text('0.00');
            $('#nct-total-workload').val('0.00');
        }
    } */
	/*
	* #390 13-08-2019  RE: Ticket #390 «INCIDENT/ANOMALIE» «task screen» «EN COURS DE DEV» «Développeur» «z0 Gravity»
	* hide all calendar picker except date
	*/
	function selectRange(){
        var val = parseInt($('#nct-range-type').val());
        switch(val){
            case 0:
				$('#date-list-container').show();
                $('#nct-range-picker-container').hide();
                $('.period-input').hide();
				break;
            default:
                $('#date-list-container').hide();
                $('#nct-range-picker-container').hide();
                $('.period-input').hide();
				break;
        }
        if( val ==3){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        resetRange();
        if( !$('#nct-assign-table tbody tr').length ){
            $('#nct-assign-table tfoot .value-cell').text('0.00');
            $('#nct-total-workload').val('0.00');
        }
    }
	selectRange();
    function removeRowCall(e){
        var cols = $(e).parent().parent().find('input');
        cols.each(function(){
            var me = $(this);
            var id = me.data('id');
            var original = parseFloat($('#foot-' + id).text());
            $('#foot-' + id).text((original - parseFloat(me.val())).toFixed(2));
        });
        $(e).parent().parent().remove();
        //add to deletion list
        listDeletion.push('date:' + $(e).parent().parent().find('.nct-date').text());
        refreshPicker();
        calculateTotal();
    }
    function removeRow(e){
        //check if has in used / consumed
        removeRowCall(e);
    }
    function changeTotal(e){
        //check here
        var me = $(e);
        if( !me.length )return;
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#nct-range-type').val());
        if( (isNaN(newVal) || newVal < 0 ) || (type == 0 && newVal > 1) ){
            if( type == 0 )
                alert('<?php __('Enter value between 0 and 1') ?>');
            else alert('<?php __('Please enter value >= 0') ?>');
            me.val(old);
            me.focus();
            return;
        }
        var id = me.data('id');
        var total = 0;
        $('.' + 'workload-' + id).each(function(){
            total += parseFloat($(this).val());
        });
        $('#foot-' + id).text(total.toFixed(2));
        me.data('old', newVal);
        calculateTotal();
    }
    function calculateTotal(){
        var total = 0;
        $('#assign-list tfoot .value-cell').each(function(){
            total += parseFloat($(this).text());
        });
        $('#nct-total-workload').val(total.toFixed(2));
    }
    function minMaxDate(){
        var min, max;
        var type = $('#nct-range-type').val();
        if( type != 0 ){
            return [$('#nct-start-date').datepicker('getDate'), $('#nct-end-date').datepicker('getDate')];
        }
        $('.nct-date').each(function(){
            var text = $(this).text();
            var value = $.datepicker.parseDate('dd-mm-yy', text);
            if( !min || min > value)
                min = value;
            if( !max || max < value)
                max = value;
        });
        return [min, max];
    }
    function isValidList(){
        var range = minMaxDate(),
            start = $('#nct-start-date').datepicker('getDate'),
            end = $('#nct-end-date').datepicker('getDate');
        if( range[0] < start || range[1] > end )return false;
        return true;
    }
    function selectCurrentRange(){
        $('#nct-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
    function unhightlightRange(){
        $('#nct-range-picker').find('.ui-state-highlight').removeClass('ui-state-highlight');
        //$('#nct-range-picker').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    }
	function resetRange(){
        var start = $('#nct-start-date').datepicker('getDate');
        if( start ){
            //reset range picker
            $('#nct-range-picker').datepicker('setDate', start);
        }
        unhightlightRange();
        startDate = null;
        endDate = null;
    }
	function dateDiff(date1, date2) {
        date1.setHours(0);
        date1.setMinutes(0, 0, 0);
        date2.setHours(0);
        date2.setMinutes(0, 0, 0);
        var datediff = Math.abs(date1.getTime() - date2.getTime()); // difference
        return parseInt(datediff / (24 * 60 * 60 * 1000), 10); //Convert values days and return value
    }

    function dateString(date, format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, date);
    }

    function toRowName(date){
        //parse date from task
        var part = date.split('_');
        switch(part[0]){
            case '1':
            case '3':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                d = part[2].split('-');
                var end = new Date(d[2], d[1]-1, d[0]);
                return dateString(start, 'dd/mm') + ' - ' + dateString(end, 'dd/mm/yy');
            case '2':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                return monthName[start.getMonth()] + ' ' + d[2];
            default:
                return dateString(new Date(part[1]));
        }
    }
	$('#nct-period-start-date').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#nct-start-date').datepicker('getDate'),
				end = $('#nct-end-date').datepicker('getDate');
			var canSelect = d >= start && Workdays[date] == 1 && d <= end;
			return [canSelect, '', ''];
		},
		onSelect: function(dateText, inst) {
			var type = parseInt($('#nct-range-type').val());
			var date = $(this).datepicker('getDate');
			startDate = date;
		},
	});
	$('#nct-period-end-date').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#nct-period-start-date').datepicker('getDate'),
				end = $('#nct-end-date').datepicker('getDate');
			if( start && end){
				var canSelect = d >= start && Workdays[date] == 1 && d <= end;
				return [canSelect, '', ''];
			}
			return [false];
		},
		onSelect: function(dateText, inst) {
			var type = parseInt($('#nct-range-type').val());
			var date = $(this).datepicker('getDate');
			endDate = date;
		},
	});
	$('#nct-range-type').change(function(){
		//xoa het tat ca workload
		$('#nct-assign-table tbody').html('');
		selectRange();
	});
	$('#reset-range').click(function(){
		resetRange();
	});
	$('#add-range').click(function(){
		//check neu co assign thi moi them date
		if( nct_list_assigned.length == 0 ){
			return;
		}
		if( startDate && endDate ){
			_addRange(startDate, endDate);
		}
		$('.period-input-calendar').datepicker('setDate', null);
	});
	$('#add-date').click(function(){
		if( $(this).hasClass('disabled') )return;
		var dates = getValidDate();
		//check neu co assign thi moi them date
		if( nct_list_assigned.length == 0 ){
			return;
		}
		for(var i in dates){
			var date = dates[i];
			if( date ){
				var invalid = false;
				$('.nct-date').each(function(){
					var value = $(this).text();
					if( value == date )invalid = true;
				});
				if( !invalid ){
					//add new row
					var html = '<tr>';
					html += '<td id="date-' + date + '" class="nct-date" style="text-align: left">' + date + '</td>';
					$('.header-cell').each(function(){
						var col = $(this);
						var id = col.prop('id').replace('col-', '');
						var hide = '';
						if( !col.is(':visible') )hide = 'style="display: none"';
						var _id = id.split('-');
						var e_id=_id[0],
							e_ispc=_id[1]==1 ? 1 : 0,
							ip_name = 'data[workloads][' + date + '][' + id + ']';
						html += '<td class="ntc-employee-col value-cell cell-' + id + '" ' + hide + '><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="ntc-employee-col workload workload-' + id + '" data-id="' + id + '" name="' + ip_name + '[estimated]" value="0" onchange="changeTotal(this)" data-ref/></td>';
					});
					html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td>';
					html += '<td class="row-action"><a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
					html += '</tr>';
					$('#nct-assign-table tbody').append(html);
				}
			}
		}
		bindNctKeys();
		$.each( $('.wd-row-inline'), function (i, _row){
			var _row = $(_row);
			var _width = 0;
			$.each( _row.children(), function( j, _col){
				_width += $(_col).width()+41;
			});
			_row.width(_width);
		});
		//$('#date-list').multiDatesPicker('resetDates');
	});
	$('#create-range').click(function(){
		/*
		if( nct_list_assigned.length == 0 ){
			var dialog = $('#modal_dialog_alert').dialog();
			$('#btnNoAL').click(function() {
				dialog.dialog('close');
			});
			return;
		}
		var dateType = parseInt($('#nct-range-type').val());
		//available for week and month and day.
		var start = $('#nct-start-date').datepicker('getDate'),
			end = $('#nct-end-date').datepicker('getDate');
		if(!start || !end){
			var dialog = $('#modal_dialog_alert2').dialog();
			$('#btnNoAL2').click(function() {
				dialog.dialog('close');
			});
			return false;
		}
		*/
		var start = $('#nct-start-date').datepicker('getDate'),
			end = $('#nct-end-date').datepicker('getDate');
		if( nct_list_assigned.length == 0 || !start || !end ){
			// $(this).closest('form')[0].reportValidity(); only support by Chrome and Firefox
			$(this).closest('form').find(':submit').click();
			return false;
		}else addRange(start, end);
	});
	$('#per-workload').bind("cut copy paste",function(e) {
		e.preventDefault();
	});
	$('#per-workload, #nct-end-date').on('keypress', function(e){
		if( e.key == "Enter"){
			e.preventDefault();
			$(this).closest('.input-icon-container').find('.wd-icon .btn:first').click();
		}
	});
	$('#fill-workload').click(function(){
		var val = parseFloat($('#per-workload').val());
		if( isNaN(val) )return;
		if( $('#nct-range-type').val() == '0' && val > 1 )return;
		$('#nct-assign-table tbody .value-cell input.workload').each(function(){
			$(this).val(val).data('old', val).trigger('change');
		});
		per_workload = val;
		//calculateTotal();
	});
	$('#nct-unit-price').keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		var _val = parseFloat(val, 10);
		if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}
	});
	$('#date-list').multiDatesPicker({
		dateFormat: 'dd-mm-yy',
		separator: ',',
		//numberOfMonths: [1,3],
		beforeShowDay: function(d){
			var start = $('#nct-start-date').datepicker('getDate'),
				end = $('#nct-end-date').datepicker('getDate');
			if( !start || !end || start > end ){
				return [false, '', ''];
			}
			//var date = d.getDay();
			return [ isValidDate(d, start, end) ,'', ''];
		},
		onChangeMonthYear: function(year){
			loadHolidays(year);
		}
	});
	$('#nct-range-picker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		onSelect: function(dateText, inst) {
			var type = parseInt($('#nct-range-type').val());
			var date = $(this).datepicker('getDate');
			var curStart = $('#nct-start-date').datepicker('getDate'),
				curEnd = $('#nct-end-date').datepicker('getDate');
			if( type == 1 ){
				startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
				endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
			} else if(type == 2 || type == 0){
				startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
				endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
			}else {
				startDate = curStart;
				endDate = curEnd ;
			}
			//điều chỉnh lại start date / end date của 2 cái input
			if( curStart > startDate ){
				//$('#nct-start-date').datepicker('setDate', startDate);
			}
			if( curEnd < endDate ){
				//$('#nct-end-date').datepicker('setDate', endDate);
			}
			var start = dateString(startDate);
			var end = dateString(endDate);
			$('#date-list').multiDatesPicker('resetDates');
			selectCurrentRange();
		},
		beforeShowDay: function(date) {
			var cssClass = '';
			var canSelect = true;
			var start = $('#nct-start-date').datepicker('getDate'),
				end = $('#nct-end-date').datepicker('getDate');
			if( !start || !end )canSelect = false;
			else canSelect = date >= start && date <= end;
			if(date >= startDate && date <= endDate)
				cssClass = 'ui-state-highlight';
			return [canSelect, cssClass];
		},
		onChangeMonthYear: function(year, month, inst) {
			selectCurrentRange();
		}
	});
	$('#per-workload').keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		var _val = parseFloat(val, 10);
		if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}
	});
	
	function get_list_assign(_this_id){
		var multiSelect = $('#' + _this_id);
		var _list_selected = multiSelect.find(':checkbox:checked');
		var employee_selected = []; 
		if( _list_selected.length){
			$.each( _list_selected, function(ind, emp){
				var key = $(emp).val();
				var val = $(emp).next('.option-name').text();
				employee_selected.push({id: key, name: val});
			});
		}
		return employee_selected;
	}
	function multiselect_nct_pmonChange(_this_id){
		nct_list_assigned = get_list_assign( _this_id);
		draw_employee_columns();
	}
	function resetPicker(){
        if( $('#nct-range-type').val() == 0 ){
            $('#date-list').multiDatesPicker('resetDates');
            $('.nct-date').each(function(){
                var value = $(this).text();
                $('#date-list').multiDatesPicker('addDates', [$.datepicker.parseDate('dd-mm-yy', value)]);
            });
        }
    }
	function bindNctKeys(){
        var length = $('[data-ref]').length;
        $('[data-ref]').off('focus').on('focus', function(){
            var f = $(this).data('focused');
            if( f )return;
            $(this).data('focused', 1);
            $(this).select();
        }).on('blur', function(){
            $(this).data('focused', 0);
            // changeTotal(this);
        })
        .off('keydown').on('keydown', function(e){
            //tab key
            var index = $('[data-ref]').index(this);
            if( e.keyCode == 13 ){
                if( $(this).closest('td').next().hasClass('ciu-cell') ){
                    $(this).closest('tr').next().find('input:first').focus();
                } else {
                    $(this).closest('td').next().find('input').focus();
                }
                e.preventDefault();
            }
        });
    }
	
	/*
		@date: js date object
		@day:
			0 = sunday
			1 = monday..
			6 = saturday
		@return: new date object
	*/
    function getDayOfWeek(date, day) {
        var d = new Date(date),
            cday = d.getDay(),
            diff = d.getDate() - cday + day;
        return new Date(d.setDate(diff));
    }
    function getListDay(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    // start: monday,
                    date: new Date(sm.getFullYear(), sm.getMonth(), sm.getDate())
                };
            }
            current = new Date(sm.getFullYear(), sm.getMonth(), sm.getDate()+1);
        }
        return list;
    }
    function getListWeek(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var monday = getDayOfWeek(current, 1),
                currentDate = monday.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: monday,
                    end: getDayOfWeek(current, 5)
                };
            }
            current = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+7);
        }
        return list;
    }
    function getListMonth(start, end){
        var list = {};
        var current = new Date(start);
        end = new Date(end.getFullYear(), end.getMonth() + 1, 0);
        while( current <= end ){
            current.setDate(1);
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: sm,
                    end: new Date(sm.getFullYear(), sm.getMonth() + 1, 0)
                };
            }
            current.setMonth(current.getMonth() + 1);
        }
        return list;
    }
	function addRange(start, end, reset){
        if( reset ){
            //xoa het tat ca workload
            $('#nct-assign-table tbody').html('');
            selectRange();
        }
        var type = parseInt($('#nct-range-type').val());
        var list;
        if( type == 0 ) {
            list = getListDay(start, end)
        } else if (type == 1) {
            list = getListWeek(start, end)
        } else {
            list = getListMonth(start, end)
        }
        // var list = type == 1 ? getListWeek(start, end) : getListMonth(start, end),
        var minDate, maxDate;
        $.each(list, function(key, date){
            if(type == 0 ){
                if( !minDate || minDate >= date.date ){
                    minDate = new Date(date.date);
                }
                if( !maxDate || maxDate <= date.date ){
                    maxDate = new Date(date.date);
                }
                var _curYear = date.date.format('yy'),
                    _curDate = date.date.format('dd-mm-yy')
                if(typeof Holidays[_curYear] == 'undefined'){
                    loadHolidays(_curYear);
                }else if(Holidays[_curYear].length == 0){
					loadHolidays(_curYear);
				}				
                if( ($.inArray(_curDate, Holidays[_curYear]) == -1) && isValidDateForNctTaskDate(date.date) ){
                    _addRange(date.date, date.date);
                }
            } else {
                if( !minDate || minDate >= date.start ){
                    minDate = new Date(date.start);
                }
                if( !maxDate || maxDate <= date.end ){
                    maxDate = new Date(date.end);
                }
                _addRange(date.start, date.end);
            }
        });
        $('#nct-start-date').datepicker('setDate', minDate);
        $('#nct-end-date').datepicker('setDate', maxDate);
    }
    function isValidDateForNctTaskDate(d){
        var date = d.getDay();
        return Workdays[date] == 1;
    }
    function _addRange(start, end){
        var date = dateString(start) + '_' + dateString(end);
        var type = parseInt($('#nct-range-type').val());
        var rowName;
        if(type == 0){
            date = dateString(start);
            rowName = start.format('dd-mm-yy');
        } else if (type == 1) {
            rowName = start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        }else{
            rowName = monthName[start.getMonth()] + ' ' + start.getFullYear();
        }
        // var rowName = type == 2 ? monthName[start.getMonth()] + ' ' + start.getFullYear() : start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        var invalid = $('#date-' + date).length ? true : false;
        if( !invalid ){
            //add new row
            var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: left">' + rowName + '</td>';
            $('.header-cell').each(function(){
                var col = $(this);
                var id = col.prop('id').replace('col-', '');
                var hide = '';
                if( !col.is(':visible') )hide = 'style="display: none"';
				var _id = id.split('-');
				var e_id=_id[0],
					e_ispc= _id[1]==1 ? 1 : 0,
					ip_name = 'data[workloads][' + date + '][' + id + ']';
                html += '<td class="ntc-employee-col value-cell cell-' + id + '" ' + hide + '><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="ntc-employee-col workload workload-' + id + '" data-id="' + id + '"  name="' + ip_name + '[estimated]" value="0" onchange="changeTotal(this)" data-ref/></td>';
            });
            html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td>';
			html += '<td class="row-action"><a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
			html += '</tr>';
            $('#nct-assign-table tbody').append(html);
            bindNctKeys();
			$.each( $('.wd-row-inline'), function (i, _row){
				var _row = $(_row);
				var _width = 0;
				$.each( _row.children(), function( j, _col){
					_width += $(_col).width()+41;
				});
				_row.width(_width);
			});
        }
    }
	$('#nct-start-date').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			return [Workdays[date] == 1, '', ''];
		},
		onSelect: function(){
			//showPicker();
			refreshPicker();
		}
	});
	$('#nct-end-date').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#nct-start-date').datepicker('getDate'),
				check = Workdays[date] == 1;
			if( start != null ){
				check = check && start <= d;
			}
			return [ check, '', ''];
		},
		beforeShow: function(e, i){
			var start = $('#nct-start-date').datepicker('getDate'),
				end = $(e).datepicker('getDate');
			if( !end && start ){
				return {defaultDate: start};
			}
		},
		onSelect: function(){
			//showPicker();
			refreshPicker();
		}
	});
	function ProjectAddPopupForm_afterSubmit(form_id, data){
		console.log( form_id, data );
		if( data.success =='success'){
			var data = data.data;
			if( 'redirect' in data){
				location.href = data.redirect;
			}else{
				location.reload();
			}
		}
	}
	function ProjectTaskAddPopupForm_afterSubmit(form_id, data){
		console.log( form_id, data );
		var form = $('#' + form_id);
		var msg = form.find('.form-message');
		var err_msg = form.find('.alert-message');
		if( data.success =='true'){
			err_msg.empty();
			msg.text('<?php __('Saved');?>');
			// $.ajax({
				// url: '/project_tasks/staffingWhenUpdateTask/' + data.data.project_id,
				// type: 'GET',
				// dataType: 'json',
				// beforeSend: function(){
					// var _msg = '';
					// _msg += '<img src="/img/loading_check.gif" alt="Loading"/>';
					// _msg += '<?php __('Staffing system is running');?>';
					// msg.append( $('<span class="loadding-msg">' + _msg + '</span>'));
				// },
				// success: function(data) {
					
				// },
				// complete: function(){
					// msg.find('.loadding-msg').remove();
					// if( 'redirect' in data){
						// location.href = data.redirect;
					// }else{
						// location.reload();
					// }
				// }
			// });	
		}else{
			msg.empty();
			err_msg.text(data.message);
		}
	}
	function NCTTaskAddPopupForm_afterSubmit(form_id, data){
		var form = $('#' + form_id);
		var msg = form.find('.form-message');
		var err_msg = form.find('.alert-message');
		if( data.result == true){
			err_msg.empty();
			msg.text('<?php __('Saved');?>');
			console.log(data);
			// $.ajax({
				// url: '/project_tasks/staffingWhenUpdateTask/' + data.data.project_id,
				// type: 'GET',
				// dataType: 'json',
				// beforeSend: function(){
					// var _msg = '';
					// _msg += '<img src="/img/loading_check.gif" alt="Loading"/>';
					// _msg += '<?php __('Staffing system is running');?>';
					// msg.append( $('<span class="loadding-msg">' + _msg + '</span>'));
				// },
				// success: function(data) {
					
				// },
				// complete: function(){
					// msg.find('.loadding-msg').remove();
				// },
			// });
			$('#nct-name').val('').trigger('change');
			$('#nct-priority').val('').trigger('change');
			$('#per-workload').val('').trigger('change');
			init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
		}else{
			msg.empty();
			err_msg.text(data.message);
		}
	}
	/* END NCT task */
	
<?php } ?>
/* END for Add new Task */

/* for Add new Employee */
	var e_form = $('#EmployeeAddPopupForm');
	e_form.on('submit', function(e){
		e_form.closest('.loading-mark').addClass('loading');
		var err_msg = $(this).closest('form').find('.alert-message');
		var msg = $(this).closest('form').find('.form-message');
		var validate = true;
		msg.empty();
		err_msg.empty();
		var f_id = e_form.prop('id');
		var _reload = e_form.data('reload');
		if( typeof _reload == 'undefined') _reload = 0;
		var validate_function = 'validate_' + f_id;
		if( typeof window[validate_function] == 'function'){
			validate = window[validate_function]();
		}
		if( !validate){
			e.preventDefault();
			e_form.closest('.loading-mark').removeClass('loading');
			return;
		}
		if( !_reload){
			e.preventDefault();
			console.log( 'ajax');
			$.ajax({
				url: e_form.prop('action'),
				type: 'POST',
				dataType: 'json',
				data: e_form.serialize(),
				success: function(data) {
					if( data.success = 'success'){
						err_msg.empty();
						msg.text(data.message);
					}else{
						msg.empty();
						err_msg.text(data.message);
					}
				},
				complete: function(){
					e_form[0].reset();
					e_form.closest('.loading-mark').removeClass('loading');
				}
			
			})
		}
		
	});
/* END for Add new Employee */

	// $('.wd-combobox').on('click', function(e){
 //        e.preventDefault();
 //        $(this).closest('.wd-multiselect').find('.wd-combobox-content').toggle();
 //    });
	
 //    $('body').on('click', function(e){
	// 	var _multisels = $('.wd-full-popup .wd-multiselect');
	// 	$.each( _multisels, function( ind, _multisel){
	// 		_target = $(e.target);
	// 		_multisel = $(_multisel);
	// 		if( _multisel.find( e.target).length  || (_multisel.length == _target.length && _multisel.length == _multisel.filter(_target).length) ) return;
	// 		else{
	// 			_multisel.find('.wd-combobox-content').fadeOut('300');
	// 		}
	// 	});
	// });
	jQuery.removeFromArray = function(value, arr) {
        return jQuery.grep(arr, function(elem, index) {
            return elem !== value;
        });
    };
	init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
	
	function popup_dropzone(){
		// return;
		var all_Dropzone = $('.form-style-2019').find('.dropzone');
		if( all_Dropzone.length) $.each( all_Dropzone, function(ind, _tag){
			$(function() {
				var _form = $(_tag).closest('.form-style-2019');
				var _form_id = _form.prop('id');
				var _reload = _form.data('reload');
				var _redirect = _form.data('open-new-link');
				if( typeof _reload == 'undefined') _reload = 0;
				if( typeof _redirect == 'undefined') _redirect = 0;
				var _Dropzone = new Dropzone(_tag, {
					acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
					imageSrc: "/img/new-icon/draganddrop.png",
					dictDefaultMessage: "<?php __('Drag and Drop your picture 300*200');?>",
					autoProcessQueue: false,
					addRemoveLinks: true,  
					maxFiles: 1,
					dictRemoveFile: '<?php __('Remove file');?>',
				});
				_Dropzone.on("queuecomplete", function(file) {
					if(_reload && !_redirect) location.reload();
					else _form[0].reset();
					_form.closest('.loading-mark').removeClass('loading');
				});
				_Dropzone.on("success", function(file) {
					_Dropzone.removeFile(file);
					var _function = _form_id.replace(/-/g, '_') + '_afterSubmit';
					console.log( _function);
					_form.closest('.loading-mark').removeClass('loading');
					if( typeof window[_function] == 'function'){
						var _xhr = '';
						var respon = '';
						if ('xhr' in file)
							_xhr = file.xhr;
						if ('responseText' in _xhr)
							respon = _xhr.responseText;
						data = JSON.parse(respon);
						window[_function](_form_id, data);
					}
				});
				_form.on('submit', function(e){
					_form.closest('.loading-mark').addClass('loading');
					var msg = $(this).closest('form').find('.alert-message');
					var validate = true;
					msg.empty();
					var validate_function = 'validate_' + _form.prop('id');
					if( typeof window[validate_function] == 'function'){
						validate = window[validate_function]();
					}
					if( !validate){
						e.preventDefault();
						_form.closest('.loading-mark').removeClass('loading');
						return;
					}
					// return;
					if(_Dropzone.files.length){
						e.preventDefault();
						console.log( 'upload file');
						_Dropzone.processQueue();
					}else{
						if( !_reload){
							e.preventDefault();
							
							console.log( 'ajax');
							$.ajax({
								url: _form.prop('action'),
								type: 'POST',
								dataType: 'json',
								data: _form.serialize(),
								success: function(data) {
									var _function = _form_id.replace(/-/g, '_') + '_afterSubmit';
									console.log( _function);
									if( typeof window[_function] == 'function'){
										window[_function](_form_id, data);
									}
								},
								complete: function(){
									_form.closest('.loading-mark').removeClass('loading');
								}
							
							})
						}
						else{
							console.log( 'submit');
						}
					}
					
				});
				_Dropzone.on('sending', function(file, xhr, formData) {
					// Append all form inputs to the formData Dropzone will POST
					var data = _form.serializeArray();
					$.each(data, function(key, el) {
						formData.append(el.name, el.value);
					});
				});
			});
		});
	}
	popup_dropzone();
	function back_to_last_popup(elm){
		// var popup = $(elm);
		cancel_popup(elm);
		var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_add_task_prj', {width: popup_width});
	}
	if( typeof re_init_afterload == 'function' ) re_init_afterload();
	// wd_radio_button();
	/*
	* Workload table
	* init: list_assigned, c_task_data, show_workload
	*/
	var c_task_data = {}; // Continous task data 
	var list_assigned = {}; // Continous task data 
	// var show_workload = <?php echo json_encode($show_workload); ?>; 
	function template_add_task_normal_showed(){
		init_multiselect('#template_add_task_prj .wd-multiselect, #template_add_nct_task .wd-multiselect');
	}
	function c_calcTotal(){
		$.each( $('.task-workload .nct-assign-table'), function( ind, elm){
			var _total = 0;
			$.each( $(elm).find('.c_workload'), function(ind, inp){
				_total += $(inp).val() ? parseFloat($(inp).val()) : 0;
			});
			$(elm).find('.total-consumed').text( _total.toFixed(2) );
		});
	}
	function draw_c_workload_table(elm){
		if( !elm) return;
		if(list_assigned.length == 0){
			elm.find('.nct-assign-table tbody').empty();
			return;
		}
		var _data_assigned = {};
		if(c_task_data && c_task_data.assigned){
			$.each(c_task_data.assigned, function(ind, emp){
				var key = emp.reference_id + '-' + emp.is_profit_center;
				_data_assigned[key] = emp;
			});
		}
		$.each( elm.find('.nct-assign-table .c_workload'), function(ind, cell){
			var id = $(cell).data('id');
			if( !(id in _data_assigned) ){
				$(cell).closest('tr').remove();
			}
		});
		$.each(list_assigned, function(ind, emp){
			var id = emp.id,
				name = emp.name;
			var employee = (emp.id).split('-');
			var e_id = employee[0];
			var is_profit_center = employee[1]==1 ? 1 : 0;
			
			var tag = '.c_workload-' + id;
			if( $(tag).length == 0){
				var _avt = '<span style="margin-bottom: 0px;margin-top: 10px;" class="circle-name" title="' + name + '" data-id="' + id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 35 height = 35 src="'+  js_avatar(e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
				}
				_avt += '</span>';
				_avt += '<span class="header-name" title="' + name + '" >'+ name.replace(/^PC \/ /, '') +'</span>';
				
				var res_col = '<td class="col_employee" >' + _avt + '</td>';
				
				var e_workload = (id in _data_assigned) ? _data_assigned[id]['estimated'] : 0;
				e_workload = parseFloat(e_workload).toFixed(2);
				var ip_name = 'data[workloads][' + id + ']';
				var val_col = '<td style="vertical-align: middle;" class="col_workload"><input type="text" id="val-' + id + '" class="c_workload c_workload-' + id + '"  id="c_workload-' + id + '" data-id="' + id + '" value="'+ e_workload +'" name="' + ip_name + '[estimated]" onkeyup="c_calcTotal(this)"/></td>';
				var _html = '<tr class="workload-row workload-row-' + id + ' ">' + res_col + val_col + '</tr>';
				elm.find('.nct-assign-table tbody').append( _html);
			}
		});
		c_calcTotal();
	}
	function get_list_assign(_this_id){
		var multiSelect = $('#' + _this_id);
		var _list_selected = multiSelect.find(':checkbox:checked');
		var employee_selected = []; 
		if( _list_selected.length){
			$.each( _list_selected, function(ind, emp){
				var key = $(emp).val();
				var val = $(emp).next('.option-name').text();
				employee_selected.push({id: key, name: val});
			});
		}
		return employee_selected;
	}
	function multiselect_pmonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
	}
	function cancel_popup_template_add_task_prj(){
		c_task_data = {};
		list_assigned = {};
		$('#new_task_assign_table').find('tbody').empty();
	}
	/*
	* END Workload table
	*/
</script>