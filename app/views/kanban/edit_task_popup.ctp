<?php 
/* CHU Y: 
* File nay chua include jquery-ui.multidatespicker.js do thu vien nay bi loi khi include 2 lan
* Neu page nao su dung popup nay thi phai include thu vien vao page do. Khong duoc include o day vi man hinh Task se loi
*/
App::import("vendor", "str_utility");
$str_utility = new str_utility();
echo $html->script(array(
	'jquery.multiSelect',
	// 'jshashtable-2.1',
	// 'tinymce/tinymce.min',
	'easytabs/jquery.easytabs.min',
	// 'jquery-ui.multidatespicker',
	// 'dropzone.min',
	'common',
));
echo $html->css(array(
    // 'add_popup', // da include trong project task index
    'jquery.multiSelect',
	// 'dropzone.min',
	// 'preview/datepicker-new'
    // 'multipleUpload/jquery.plupload.queue',
));
function multiSelect($_this, $fieldName, $fielData, $textHolder, $pc = ''){	
	ob_start();
    ?>
	<div class="wd-multiselect multiselect popupnct_nct_list_assigned">
		<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
			<p class="placeholder">
				<?php echo $textHolder;?>
			</p>
		</a>
		<div class="wd-combobox-content <?php echo $fieldName ;?>" style="display: none;">
			<div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="<?php __('Search');?>" rel="no-history"></span></div>
			<div class="option-content">
					<?php 
					foreach($fielData as $idPm => $namePm){
						$avatar = '<img src="' . $_this->UserFile->avatar(.$idPm) . '" alt="'. $namePm .'"/>';
						?>
						<div class="projectManager wd-data-manager wd-group-<?php echo $idPm ;?>">
							<p class="projectManager wd-data">
								<a class="circle-name"  title="<?php echo $namePm ;?>"><span data-id = "<?php echo $idPm ;?>"> <?php echo $avatar ;?></span></a>
								<?php 
								echo $_this->Form->input($fieldName, array(
									'label' => false,
									'div' => false,
									'type' => 'checkbox',
									'name' => 'data['. $fieldName .'][]',
									'value' => $idPm)) ;?>
									
								<span class="option-name" style="padding-left: 5px;"><?php echo $namePm;?> </span>
							</p>
						</div>
					<?php }
					if(!empty($pc)){
						foreach($pc as $idPm => $namePm){
							?>
							<div class="projectManager wd-data-manager wd-group-<?php echo $idPm ;?>">
								<p class="projectManager wd-data">
									<a class="circle-name" title="<?php echo  $namePm ;?>"><span data-id = "<?php echo $idPm ;?>-1"><i class="icon-people"></i></span></a>
										<?php 
										echo  $_this->Form->input($fieldName, array(
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
<div class="add-popup-container">

<div id="template_add_task" class="wd-full-popup" style="display: none;" >
	<div class="wd-popup-inner">
		<div class="template-popup loading-mark wd-popup-container"  id="tab-popup-container">
			<div class="wd-popup-head clearfix">
				<ul class="tabPopup" style="margin: 0; padding: 0;">
					<?php if( !empty($canModified)) { ?>
						<li class="liPopup" ><a href="#NewTask"> <?php __('Add new task');?>  </a></li>
					<?php } ?> 
					
				</ul>
				<a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
				
			</div>
			<div class="template-popup-content wd-popup-content">
				
				<!--
				-- Create New Task	
				-->
				
				<?php if( !empty($canModified)) { ?>
				<div id="NewTask" class="popup-tab">
					<?php 
					echo $this->Form->create('ProjectTask', array(
						'type' => 'POST',
						'url' => array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup'),
						'class' => 'form-style-2019',
						'data-reload' => 0,
						'id' => 'ProjectTaskAddPopupForm'
					));
					?>
					<div class="form-head clearfix">
						<h4 class="desc"><?php __('Task Description'); ?></h4>
						<?php if( empty($companyConfigs['create_ntc_task']) ) {?>
							<a href="javascript:void(0);" class="right-link" onclick="cancel_popup(this); show_full_popup('#template_add_nct_task', {width: 'inherit'});">
								<?php __('Add a non-continuous task');?>
							</a>
						<?php }?>
					</div>
					<p style="color: #217FC2" class="form-message"></p>
					<p style="color: red" class="alert-message"></p>
					<?php 
					echo $this->Form->input('return', array(
						'data-return' => 'form-return',
						'type'=> 'hidden',
						'value' => $this->Html->url(),
						
					));
					echo $this->Form->input('run_staffing', array(
						'data-return' => 'form-return',
						'type'=> 'hidden',
						'value' => 1,
						
					));
					// debug( $projectName); exit;
					// echo $this->Form->input('project_id', array(
						// 'type'=> 'select',
						// 'id' => 'toProject',
						// 'label' => __d(sprintf($_domain, 'Details'), 'Project Name', true),
						// 'options' => array( $project_id => $projectName['Project']['project_name']),
						// 'required' => true,
						// 'rel' => 'no-history',
						// 'selected' => $project_id,
						// 'div' => array(
							// 'class' => 'wd-input label-inline has-val'
						// )
					// ));
					echo $this->Form->input('project_id', array(
						'type'=> 'hidden',
						'rel' => 'no-history',
						'value' => $project_id
					));
					$phases = !empty($listPhases) ? Set::combine($listPhases, '{n}.id', '{n}.name') : array();
					echo $this->Form->input('project_planed_phase_id', array(
						'type'=> 'select',
						'id' => 'toPhase',
						'label' => __('Phase', true),
						'options' => $phases,
						'required' => true,
						'rel' => 'no-history',
						'onchange' => 'popupTask_phaseOnChange(this);',
						'div' => array(
							'class' => 'wd-input label-inline required has-val'
						)
					));
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
								<div class="wd-multiselect multiselect popupnct_nct_list_assigned" id="popupnct_nct_list_assigned">
									<a href="javascript:void(0);" class="wd-combobox wd-project-manager disable">
										<p>
											<?php __('Assign to');?>
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
								'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
								'options' => $listAllStatus,
								'required' => true,
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline has-val'
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
									'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
									// 'required' => true,
									'class' => 'wd-date',
									// 'onchange'=> 'newTask_validated(this);',
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
								'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
								// 'required' => true,
								'class' => 'wd-date',
								'autocomplete' => 'off',
								// 'onchange'=> 'newTask_validated(this);',
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
					<div class="wd-row wd-submit-row">
						<div class="wd-col-xs-12">
							<div class="wd-submit">
								<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
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
			</div>
		</div>
	</div>
</div>

<!-- 
<div id='popupnct_modal_dialog_alert' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Select at least a resource or a team', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='popupnct_btnNoAL' />
</div>
<div id='popupnct_modal_dialog_alert1' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Please enter task name', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='popupnct_btnNoAL1' />
</div>
<div id='popupnct_modal_dialog_alert2' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Select a start date, end date', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='popupnct_btnNoAL2' />
</div>
-->
<!--
-- Create New NCT Task	
-->
<div id="template_add_nct_task" class="wd-full-popup autosize-popup" style="display: none;" >
	<div class="wd-popup-inner">
		<div class="add_nct_task-popup loading-mark wd-popup-container">
			<div class="wd-popup-head clearfix">
				<div class="popup-back"><a href="javascript:void(0);" onclick="back_to_last_popup(this);"><i class="icon-arrow-left"></i><span> <?php __('Back');?> </span></a></div>
				
				<a style="right: 70px;height: 70px;position: absolute;padding: 25px;" target="_blank" href="<?php echo $this->Html->url('/guides/tache_non_continue') ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
					<defs>
					<style>
						.cls-1 {
							fill: #666;
							fill-rule: evenodd;
						}
					</style>
					</defs>
					<path id="help" class="cls-1" d="M1960,40a10,10,0,1,1,10-10A10,10,0,0,1,1960,40Zm0-18.667A8.667,8.667,0,1,0,1968.66,30,8.667,8.667,0,0,0,1960,21.333Zm2.04,8.192q-0.15.146-.39,0.374c-0.16.152-.3,0.284-0.41,0.4a2.539,2.539,0,0,0-.27.286,1.379,1.379,0,0,0-.27.909v0.66h-1.66V31.255a2.13,2.13,0,0,1,.14-0.873,3.544,3.544,0,0,1,.61-0.755l1.07-1.07a1.272,1.272,0,0,0,.34-0.909,1.255,1.255,0,0,0-.35-0.9,1.231,1.231,0,0,0-.91-0.359,1.325,1.325,0,0,0-.93.344,1.347,1.347,0,0,0-.43.917h-1.78a3.024,3.024,0,0,1,1.02-2.046,3.251,3.251,0,0,1,2.18-.741,3.1,3.1,0,0,1,2.12.711,2.488,2.488,0,0,1,.83,1.988,2.246,2.246,0,0,1-.49,1.467C1962.28,29.26,1962.13,29.427,1962.04,29.525Zm-2.15,3.71a1.14,1.14,0,0,1,.8.315,1.027,1.027,0,0,1,.34.763,1.048,1.048,0,0,1-.34.77,1.084,1.084,0,0,1-.79.323,1.136,1.136,0,0,1-.8-0.316,1.015,1.015,0,0,1-.33-0.762,1.04,1.04,0,0,1,.33-0.77A1.07,1.07,0,0,1,1959.89,33.235Z" transform="translate(-1950 -20)"/>
					</svg>
				</a>
				
				<a href="javascript:void(0);" class="close_template_add_nct_task wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
			</div>
			<div class="add_nct_task-popup-content wd-popup-content">
				<?php
				echo $this->Form->create('NCTTask', array(
						'type' => 'POST',
						'url' => array('controller' => 'project_tasks', 'action' => 'saveNcTask'),
						'class' => 'form-style-2019',
						'id' => 'NCTTaskAddPopupForm',
						'data-reload' => 0,
					));
				
				?>
				<!--
				<div class="form-head clearfix">
					<h4 class="desc"><?php __('Task Description'); ?></h4>
				</div>
				-->
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
							'data-return' => 'form-return',
							'type'=> 'hidden',
							'value' => 1,
							
						));
						// echo $this->Form->input('id', array(
							// 'type'=> 'select',
							// 'id' => 'popupnct-project_id',
							// 'label' => __d(sprintf($_domain, 'Details'), 'Project Name', true),
							// 'options' => array( $project_id => $projectName['Project']['project_name']),
							// 'required' => true,
							// 'selected' => $project_id,
							// 'rel' => 'no-history',
							// 'div' => array(
								// 'class' => 'wd-input label-inline required has-val'
							// )
						// ));
						echo $this->Form->input('id', array(
							'type'=> 'hidden',
							'rel' => 'no-history',
							'value' => $project_id
						));
						$phases = !empty($listPhases) ? Set::combine($listPhases, '{n}.id', '{n}.name') : array();
						echo $this->Form->input('task.project_planed_phase_id', array(
							'type'=> 'select',
							'id' => 'popupnct-phase',
							'label' => __('Phase', true),
							'options' => $phases,
							'required' => true,
							'onchange'=> 'nct_phaseOnChange(this);',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required has-val'
							)
						));
						echo $this->Form->input('task.task_title', array(
							'type'=> 'text',
							'id' => 'popupnct-name',
							'label' => __('Task name', true),
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required'
							)
						));
						?>
						<div class="wd-input wd-area wd-none required">
							<div class="wd-multiselect multiselect popupnct_nct_list_assigned" id="multiselect-popupnct-pm">
								<a href="javascript:void(0);" class="wd-combobox wd-project-manager disable">
									<p>
										<?php __('Assign to');?>
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
									'id' => 'popupnct-status',
									'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
									'options' => $listAllStatus,
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline required has-val'
									)
								));
								
								?>
							</div>
							<div class="wd-col wd-col-sm-6">
							<?php 
								$periods = array( __('Day',true), __('Week',true), __('Month',true)/* , __('Period',true )*/);
								echo $this->Form->input('type', array(
									'type'=> 'select',
									'id' => 'popupnct-range-type',
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
										'id' => 'popupnct-start-date',
										'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
										// 'required' => true,
										'class' => 'wd-date',
										'onchange'=> 'popupnct_validated(this);',
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
										'id' => 'popupnct-end-date',
										'label' =>  __d(sprintf($_domain, 'Details'), 'End Date', true),
										// 'required' => true,
										'class' => 'wd-date',
										'autocomplete' => 'off',
										'onchange'=> 'popupnct_validated(this);',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline required'
										)
									));
									?>
									<div class="wd-icon">
										<a href="javascript:void(0);" id="popupnct_create-range" class="btn"><i class="icon-reload"></i></a>
									</div>
								</div>
							</div>
						</div>
						<div class="wd-row">
							<div class="wd-col wd-col-lg-6">
								<?php 
								echo $this->Form->input('task.profile_id', array(
									'type'=> 'select',
									'id' => 'popupnct-profile',
									'label' => __('Profile', true),
									'options' => $projectProfiles,
									'required' => false,
									'rel' => 'no-history',
									'empty' => '',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
								echo $this->Form->input('task.task_priority_id', array(
									'type'=> 'select',
									'id' => 'popupnct-priority',
									'label' => __('Priority', true),
									'options' => $projectPriorities,
									'required' => false,
									'rel' => 'no-history',
									'empty' => '',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
								));
								if( !empty($adminTaskSetting['Milestone'])){
									echo $this->Form->input('task.milestone_id', array(
										'type'=> 'select',
										'id' => 'popupnct-milestone',
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
										'id' => 'popupnct-unit-price',
										'label' => __('Unit Price', true),
										'required' => false,
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
								} ?>
								<div class="input-icon-container">
									<?php
									echo $this->Form->input('popupnct_per-workload', array(
										'type'=> 'text',
										'id' => 'popupnct_per-workload',
										'label' => __('Workload', true),
										'required' => false,
										'class' => 'wd-date',
										'autocomplete' => 'off',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
									?>
									<div class="wd-icon">
										<a href="javascript:void(0);" id="popupnct_fill-workload" class="btn">
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
												'id' => 'popupnct-period-start-date',
												'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
												'required' => false,
												'class' => 'wd-date text popupnct-period-input-calendar popupnct-date-period',
												'autocomplete' => 'off',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
											echo $this->Form->input('period_end_date', array(
												'type'=> 'text',
												'id' => 'popupnct-period-end-date',
												'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
												'required' => false,
												'class' => 'wd-date text popupnct-period-input-calendar popupnct-date-period',
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
								<div class="input-icon-container" id="popupnct-range-picker-container">
									<div id="popupnct-range-picker" class="popupnct-range-picker"></div>
									<div class="wd-input-group">
									</div>
									<div class="wd-icon">
										<a id="popupnct_add-range" href="javascript:;" class="btn-text popupnct-range-picker">
											<i class="icon-reload"></i>
										</a>
									</div>
								</div>
								<div class="input-icon-container" id="popupnct_date-list-container">
									<div class="wd-input-group">
										<div id="popupnct_date-list" class="start-end"></div>
									</div>
									<div class="wd-icon">
										<a id="popupnct_add-date" href="javascript:;" class="btn-text date-picker">
											<i class="icon-reload"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div id="popup_nct_template_attach" class="wd-hide">
							<div class="heading">
							</div> 
							<div class="trigger-upload">
								<div id="wd-popupnct-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks', 'action' => 'saveNcTask')); ?>" class="dropzone" value="" >
								</div>
							</div>
						</div>
					</div>
					
					<div class="wd-inline-col wd-col-autosize">
						<div id="popupnct_assign-list">
							<table id="popupnct-assign-table" class="nct-assign-table">
								<thead>
									<tr>
										<td class="bold base-cell null-cell">&nbsp;</td>
										<td class="base-cell" id="popupnct_consumed-column"><?php __('Consumed'); ?> (<?php __('In Used'); ?>)</td>
										<td class="base-cell row-action"></td>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<td class="base-cell"><?php __('Total') ?></td>
										<td class="base-cell" id="popupnct_total-consumed">0</td>
										<td class="base-cell row-action"></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="wd-row wd-submit-row">
					<div class="wd-col-xs-12">
						<div class="wd-submit">
							<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
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
<script>
$('#tab-popup-container').easytabs({
	'tabs' : '.tabPopup .liPopup',
});
var text_by = <?php echo json_encode(  __('by', true) ) ?>;
var text_modified = <?php echo json_encode( __('Modified', true) ) ?>;
var isTablet = <?php echo json_encode($isTablet) ?>;
var isMobile = <?php echo json_encode($isMobile) ?>;
var curent_time = <?php echo json_encode(time()) ?>;
var popup_listPhases = <?php echo json_encode($listPhases) ?>;
var project_id = <?php echo json_encode($project_id) ?>;
var canModified = <?php echo json_encode($canModified) ?>;

Dropzone.autoDiscover = false;
$("#newTaskEndDay, #newTaskStartDay, #newEmpStartDay, #newEmpEndDay").datepicker({
	dateFormat      : 'dd-mm-yy'
});


/* for Add new Task */

	var adminTaskSetting = <?php echo json_encode($adminTaskSetting);?>;
	var newtask_date_validated = 1;
    function newTask_validated(_this){
        var st_date = $('#newTaskStartDay').val();
        var en_date = $('#newTaskEndDay').val();
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');

        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).addClass('invalid');
            newtask_date_validated = 0;
        }else{
            $('#newTaskStartDay').removeClass('invalid');
            $('#newTaskEndDay').removeClass('invalid');
            newtask_date_validated = 1;
        }
    };
	var popupnct_newtask_date_validated = 1
    function popupnct_validated(_this){
        var st_date = $('#popupnct-start-date').val();
        var en_date = $('#popupnct-end-date').val();
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');

        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).addClass('invalid');
            popupnct_newtask_date_validated = 0;
        }else{
            $('#popupnct-start-date').removeClass('invalid');
            $('#popupnct-end-date').removeClass('invalid');
            popupnct_newtask_date_validated = 1;
        }
    };
	
	function disable_all_option(){
		$('#toPhase').val('').prop( "disabled", true );
		$('#popupnct_nct_list_assigned .option-content').empty();
		init_multiselect('#template_add_task .wd-multiselect, #template_add_nct_task .wd-multiselect');
		
	}
	function popupTask_phaseOnChange(){
		var _project_id = project_id;
		var _phase_id = $('#toPhase').val();
		if( _phase_id == '') {
			$('#newTaskStartDay').datepicker('setDate', '');
			$('#newTaskEndDay').datepicker('setDate', '');
		}	
		if(_project_id && _phase_id){
			var phase = popup_listPhases[_phase_id];
			var start_date = phase.phase_real_start_date ? new Date(phase.phase_real_start_date) : '';
			var end_date = phase.phase_real_end_date ? new Date(phase.phase_real_end_date) : '';
			$('#newTaskStartDay').datepicker('setDate', start_date);
			$('#newTaskEndDay').datepicker('setDate', end_date);
		}
		$('#newTaskStartDay').trigger('change');
		$('#newTaskEndDay').trigger('change');
	}
	function ajaxGetResources(_project_id){
		$('#popupnct_nct_list_assigned').addClass('loading');
		$('#popupnct_nct_list_assigned >.wd-combobox a.circle-name').remove();
		$('#popupnct_nct_list_assigned >.wd-combobox p').show();
		var _cont = $('#popupnct_nct_list_assigned .option-content');
		_cont.empty();
		$.ajax({
			url: '/projects/getTeamEmployees/'+_project_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
				if( data.success == true){
					var _cont = $('#popupnct_nct_list_assigned .option-content');
					var _html = '';
					$.each(data.data, function(ind, emp){
						emp = emp.Employee;
						_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + '">';
						_html += '<p class="projectManager wd-data">';
						_html += '<a class="circle-name" title="' + emp.name + '"><span data-id="' + emp.id + '-' + emp.is_profit_center + '">';
						if( emp.is_profit_center){
							_html += '<i class="icon-people"></i>';
						}else{
							_html += '<img width = 35 height = 35 src="'+  js_avatar(emp.id ) +'" title = "'+ emp.name +'" />';
							
						}
						_html += '</span></a>';
						_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
						_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
						_html += '</p> </div>';
					});
					_cont.html(_html);
					init_multiselect('#template_add_task .wd-multiselect, #template_add_nct_task .wd-multiselect');
					$('#popupnct_nct_list_assigned').removeClass('loading');
				}
			}
		});
	}
	ajaxGetResources(project_id);
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
	var popupnct_list_assigned = {};
	var popupnct_per_workload = 0;
	function loadHolidays(year){
		if( typeof Holidays[year] == 'undefined' ){
			$.ajax({
				url: '<?php echo $this->Html->url('/') ?>holidays/getYear/' + year,
				dataType: 'json',
				success: function(data){
					Holidays[year] = data;
					popupnct_applyDisableDays();
				}
			});
		}
	}
	function popupnct_applyDisableDays(){
		hlds = [];
		$.each(Holidays, function(year, list){
			hlds = hlds.concat(list);
		});
		if( hlds.length ){
			$('#popupnct_date-list').multiDatesPicker('addDates', hlds, 'disabled');
		}
	}
	function popupnct_refreshPicker(){
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		if( start && end && start <= end ){
			$('#popupnct_date-list').datepicker('setDate', start);
			if( popupnct_list_assigned.length > 0 ){
				$('#popupnct_add-date').removeClass('disabled');
				$('#popupnct_add-range').removeClass('disabled');
			}
			popupnct_resetRange();
			$('#popupnct-period-start-date, #popupnct-period-end-date').prop('disabled', false);
		}
		else {
			$('#popupnct_add-date').addClass('disabled');
			$('#popupnct_add-range').addClass('disabled');
			$('#popupnct-period-start-date, #popupnct-period-end-date').prop('disabled', true);
		}
		if( !start ) year = new Date().getFullYear();
		else year = start.getFullYear();
		loadHolidays(year);
		popupnct_resetPicker();
		$('#popupnct_date-list').datepicker('refresh');
		$('#popupnct-range-picker').datepicker('refresh');
	}
	//if date in disabled fields, reject
	function isValidDate(d, start, end){
		var date = d.getDay();
		return d >= start && d <= end && Workdays[date] == 1;
	}
	function popupnct_getValidDate(){
		var result = [];
		var dates = $('#popupnct_date-list').multiDatesPicker('getDates', 'object');
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		for(var i = 0; i < dates.length; i++){
			if( isValidDate(dates[i], start, end) ){
				var date = dates[i].getDate(),
					month = dates[i].getMonth() + 1;
				result.push( (date < 10 ? '0' + date : date) + '-' + (month < 10 ? '0' + month : month) + '-' + dates[i].getFullYear() );
			}
		}
		return result;
	}
	function popupnct_draw_employee_columns(){
		$('.ntc-employee-col').remove();
		if(popupnct_list_assigned.length == 0){
			return;
		}
		$.each(popupnct_list_assigned, function(ind, emp){
			//QuanNV update by 08/06/2019
			var id = '';
			var is_profit_center = '';
			if(emp){
				var employee = (emp.id).split('-');
				id = employee[0];
				is_profit_center = employee[1]==1 ? 1 : 0;
				console.log(employee, is_profit_center)
			}
			var name = emp.name;
			var cell = $('.cell-' + id);
			//add header
			var _avt = '<span class="circle-name" title="' + name + '" data-id="' + id + '-' + is_profit_center + '">';
			if( is_profit_center == 1 ){
				_avt += '<i class="icon-people"></i>';
			}else{
				_avt += '<img width = 35 height = 35 src="'+  js_avatar(id ) +'" title = "'+ name +'" />';	
			}
			_avt += '</span>';
			id = is_profit_center ? (id + '-1') : (id + '-0');
			var html = '<td class="value-cell header-cell cell-' + id + ' ntc-employee-col" id="col-' + id + '" data-id="' + id + '">' + _avt + '</td>';
						
			//End update
			
			$(html).insertBefore('#popupnct_consumed-column');
			//add content
			$('.popupnct-date').each(function(){
				var ciu = $(this).parent().find('.ciu-cell'),
					date = $(this).text();
					date = $(this).prop('id').replace('date-', '');;
				var _id = id.split('-');
				var e_id=_id[0],
					e_ispc= _id[1] ? 1 : 0,
					ip_name = 'data[workloads][' + date + '][' + id + ']';
				$('<td class="value-cell cell-' + id + ' ntc-employee-col"><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="ntc-employee-col p_workload p_workload-' + id + ' ntc-employee-col" data-id="' + id + '" value="0" name="' + ip_name + '[estimated]" onchange="popupnct_changeTotal(this)" data-ref/></td>').insertBefore(ciu);
			});
			bindNctKeys();
			//add footer
			$('<td class="value-cell cell-' + id + ' ntc-employee-col" id="popupnct_foot-' + id + '" data-id="' + id + '">0.00</td></tr>').insertBefore('#popupnct_total-consumed');
			$.each( $('.wd-row-inline'), function (i, _row){
				var _row = $(_row);
				var _width = 0;
				$.each( _row.children(), function( j, _col){
					_width += $(_col).width()+41;
				});
				_row.width(_width);
			});
			
		});
		popupnct_refreshPicker();
		popupnct_isValidList();
		
	}
	Date.prototype.format = function(format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, this);
    }
	function nct_disable_all_option(){
		$('#popupnct-phase').val('').prop( "disabled", true );
		$('#multiselect-popupnct-pm .option-content').empty();
		init_multiselect('#template_add_task .wd-multiselect, #template_add_nct_task .wd-multiselect');
		
	}
	function nct_ajaxGetResources_Milestone(_project_id){
		$('#multiselect-popupnct-pm').addClass('loading');
		$('#multiselect-popupnct-pm >.wd-combobox a.circle-name').remove();
		$('#multiselect-popupnct-pm >.wd-combobox p').show();
		var _cont = $('#multiselect-popupnct-pm .option-content');
		_cont.empty();
		$.ajax({
			url: '/projects/getTeamEmployees/'+_project_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
				if( data.success == true){
					var _cont = $('#multiselect-popupnct-pm .option-content');
					var _html = '';
					$.each(data.data, function(ind, emp){
						emp = emp.Employee;
						_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + '">';
						_html += '<p class="projectManager wd-data">';
						_html += '<a class="circle-name" title="' + emp.name + '"><span data-id="' + emp.id + '-' + emp.is_profit_center + '">';
						if( emp.is_profit_center){
							_html += '<i class="icon-people"></i>';
						}else{
							_html += '<img width = 35 height = 35 src="'+  js_avatar( emp.id ) +'" title = "'+ emp.name +'" />';
							
						}
						_html += '</span></a>';
						_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
						_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
						_html += '</p> </div>';
					});
					_cont.html(_html);
					init_multiselect('#template_add_task .wd-multiselect, #template_add_nct_task .wd-multiselect');
				}
			},
			complete: function(){
				$('#multiselect-popupnct-pm').removeClass('loading');
			}			
		});
		if( adminTaskSetting.Milestone == "1"){
			$('#popupnct-milestone').addClass('loading');
			$('#popupnct-milestone').html('');
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
						$('#popupnct-milestone').html(sel);
					}
				},
				complete: function(){
					$('#popupnct-milestone').removeClass('loading');
				}
			});
		}
	}
	nct_ajaxGetResources_Milestone(project_id);
	function nct_phaseOnChange(){
		var _project_id = project_id;
		var _phase_id = $('#popupnct-phase').val();
		if( _phase_id == '') {
			$('#popupnct-start-date').datepicker('setDate', '');
			$('#popupnct-end-date').datepicker('setDate', '');
		}	
		if(_project_id && _phase_id){
			var phase = popup_listPhases[_phase_id];
			var start_date = phase.phase_real_start_date ? new Date(phase.phase_real_start_date) : '';
			var end_date = phase.phase_real_end_date ? new Date(phase.phase_real_end_date) : '';
			$('#popupnct-start-date').datepicker('setDate', start_date);
			$('#popupnct-end-date').datepicker('setDate', end_date);
		}
		$('#popupnct-start-date').trigger('change');
		$('#popupnct-end-date').trigger('change');
		if(typeof popupnct_refreshPicker == 'function') popupnct_refreshPicker();
	}
	function popupnct_selectRange(){
        var val = parseInt($('#popupnct-range-type').val());
        switch(val){
            case 1:
            case 2:
                $('#popupnct_date-list-container').hide();
                $('#popupnct-range-picker-container').show();
                $('.period-input').hide();

            break;
            case 3:
                $('#popupnct_date-list-container').hide();
                $('#popupnct-range-picker-container').hide();
                $('.period-input').show();
            break;
            default:
                $('#popupnct_date-list-container').show();
                $('#popupnct-range-picker-container').hide();
                $('.period-input').hide();
            break;
        }
        if( val ==3){
            $('#popupnct_create-range').hide();
        } else {
            $('#popupnct_create-range').show();
        }
        popupnct_resetRange();
        if( !$('#popupnct-assign-table tbody tr').length ){
            $('#popupnct-assign-table tfoot .value-cell').text('0.00');
            $('#popupnct-total-workload').val('0.00');
        }
    }
	popupnct_selectRange();
    function popupnct_removeRowCall(e){
        var cols = $(e).parent().parent().find('input');
        cols.each(function(){
            var me = $(this);
            var id = me.data('id');
            var original = parseFloat($('#popupnct_foot-' + id).text());
            $('#popupnct_foot-' + id).text((original - parseFloat(me.val())).toFixed(2));
        });
        $(e).parent().parent().remove();
        //add to deletion list
        listDeletion.push('date:' + $(e).parent().parent().find('.popupnct-date').text());
        popupnct_refreshPicker();
        popupnct_isValidList();
    }
    function popupnct_removeRow(e){
        //check if has in used / consumed
        popupnct_removeRowCall(e);
    }
    function popupnct_changeTotal(e){
        //check here
        var me = $(e);
        if( !me.length )return;
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#popupnct-range-type').val());
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
        $('.' + 'p_workload-' + id).each(function(){
            total += parseFloat($(this).val());
        });
        $('#popupnct_foot-' + id).text(total.toFixed(2));
        me.data('old', newVal);
        popupnct_isValidList();
    }
    function popupnct_isValidList(){
        var total = 0;
        $('#popupnct_assign-list tfoot .value-cell').each(function(){
            total += parseFloat($(this).text());
        });
        $('#popupnct-total-workload').val(total.toFixed(2));
    }
    function popupnct_minMaxDate(){
        var min, max;
        var type = $('#popupnct-range-type').val();
        if( type != 0 ){
            return [$('#popupnct-start-date').datepicker('getDate'), $('#popupnct-end-date').datepicker('getDate')];
        }
        $('.popupnct-date').each(function(){
            var text = $(this).text();
            var value = $.datepicker.parseDate('dd-mm-yy', text);
            if( !min || min > value)
                min = value;
            if( !max || max < value)
                max = value;
        });
        return [min, max];
    }
    function popupnct_isValidList(){
        var range = popupnct_minMaxDate(),
            start = $('#popupnct-start-date').datepicker('getDate'),
            end = $('#popupnct-end-date').datepicker('getDate');
        if( range[0] < start || range[1] > end )return false;
        return true;
    }
    function popupnct_selectCurrentRange(){
        $('#popupnct-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
    function popupnct_unhightlightRange(){
        $('#popupnct-range-picker').find('.ui-state-highlight').removeClass('ui-state-highlight');
        //$('#popupnct-range-picker').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    }
	function popupnct_resetRange(){
        var start = $('#popupnct-start-date').datepicker('getDate');
        if( start ){
            //reset range picker
            $('#popupnct-range-picker').datepicker('setDate', start);
        }
        popupnct_unhightlightRange();
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
	$('#popupnct-period-start-date').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			var canSelect = d >= start && Workdays[date] == 1 && d <= end;
			return [canSelect, '', ''];
		},
		onSelect: function(dateText, inst) {
			var type = parseInt($('#popupnct-range-type').val());
			var date = $(this).datepicker('getDate');
			startDate = date;
		},
	});
	$('#popupnct-period-end-date').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#popupnct-period-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			if( start && end){
				var canSelect = d >= start && Workdays[date] == 1 && d <= end;
				return [canSelect, '', ''];
			}
			return [false];
		},
		onSelect: function(dateText, inst) {
			var type = parseInt($('#popupnct-range-type').val());
			var date = $(this).datepicker('getDate');
			endDate = date;
		},
	});
	$('#popupnct-range-type').change(function(){
		//xoa het tat ca workload
		$('#popupnct-assign-table tbody').html('');
		popupnct_selectRange();
	});
	$('#popupnct_reset-range').click(function(){
		popupnct_resetRange();
	});
	$('#popupnct_add-range').click(function(){
		//check neu co assign thi moi them date
		if( popupnct_list_assigned.length == 0 ){
			return;
		}
		if( startDate && endDate ){
			_popupnct_addRange(startDate, endDate);
		}
		$('.popupnct-period-input-calendar').datepicker('setDate', null);
	});
	$('#popupnct_add-date').click(function(){
		if( $(this).hasClass('disabled') )return;
		var dates = popupnct_getValidDate();
		//check neu co assign thi moi them date
		if( popupnct_list_assigned.length == 0 ){
			return;
		}
		for(var i in dates){
			var date = dates[i];
			if( date ){
				var invalid = false;
				$('.popupnct-date').each(function(){
					var value = $(this).text();
					if( value == date )invalid = true;
				});
				if( !invalid ){
					//add new row
					var html = '<tr>';
					html += '<td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + date + '</td>';
					$('.header-cell').each(function(){
						var col = $(this);
						var id = col.prop('id').replace('popupnct_col-', '');
						var hide = '';
						if( !col.is(':visible') )hide = 'style="display: none"';
						var _id = id.split('-');
						var e_id=_id[0],
							e_ispc= _id[1] ? 1 : 0,
							ip_name = 'data[workloads][' + date + '][' + id + ']';
						html += '<td class="ntc-employee-col value-cell cell-' + id + '" ' + hide + '><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="ntc-employee-col p_workload p_workload-' + id + '" data-id="' + id + '" name="' + ip_name + '[estimated]" value="0" onchange="popupnct_changeTotal(this)" data-ref/></td>';
					});
					html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td>';
					html += '<td class="row-action"><a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a></td>';
					html += '</tr>';
					$('#popupnct-assign-table tbody').append(html);
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
		//$('#popupnct_date-list').multiDatesPicker('resetDates');
	});
	$('#popupnct_create-range').click(function(){
		/*
		if( popupnct_list_assigned.length == 0 ){
			var dialog = $('#popupnct_modal_dialog_alert').dialog();
			$('#popupnct_btnNoAL').click(function() {
				dialog.dialog('close');
			});
			return;
		}
		var dateType = parseInt($('#popupnct-range-type').val());
		//available for week and month and day.
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		if(!start || !end){
			var dialog = $('#popupnct_modal_dialog_alert2').dialog();
			$('#popupnct_btnNoAL2').click(function() {
				dialog.dialog('close');
			});
			return false;
		}
		*/
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		if( popupnct_list_assigned.length == 0 || !start || !end ){
			// $(this).closest('form')[0].reportValidity(); only support by Chrome and Firefox
			$(this).closest('form').find(':submit').click();
			return false;
		}else addRange(start, end);
		popupnct_addRange(start, end);
	});
	$('#popupnct_per-workload').bind("cut copy paste",function(e) {
		e.preventDefault();
	});
	$('#popupnct_per-workload, #popupnct-end-date').on('keypress', function(e){
		if( e.key == "Enter"){
			e.preventDefault();
			$(this).closest('.input-icon-container').find('.wd-icon .btn:first').click();
		}
	});
	$('#popupnct_fill-workload').on('click', function(){
		var val = parseFloat($('#popupnct_per-workload').val());
		if( isNaN(val) )return;
		if( $('#popupnct-range-type').val() == '0' && val > 1 )return;
		$('#popupnct-assign-table tbody .value-cell input.p_workload').each(function(){
			console.log( val);
			$(this).val(val).data('old', val).trigger('change');
		});
		popupnct_per_workload = val;
		//popupnct_isValidList();
	});
	$('#popupnct-unit-price').keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		var _val = parseFloat(val, 10);
		if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}
	});
	$('#popupnct_date-list').multiDatesPicker({
		dateFormat: 'dd-mm-yy',
		separator: ',',
		//numberOfMonths: [1,3],
		beforeShowDay: function(d){
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
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
	$('#popupnct-range-picker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		onSelect: function(dateText, inst) {
			var type = parseInt($('#popupnct-range-type').val());
			var date = $(this).datepicker('getDate');
			var curStart = $('#popupnct-start-date').datepicker('getDate'),
				curEnd = $('#popupnct-end-date').datepicker('getDate');
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
			if( curStart > startDate ){
				//$('#popupnct-start-date').datepicker('setDate', startDate);
			}
			if( curEnd < endDate ){
				//$('#popupnct-end-date').datepicker('setDate', endDate);
			}
			var start = dateString(startDate);
			var end = dateString(endDate);
			$('#popupnct_date-list').multiDatesPicker('resetDates');
			popupnct_selectCurrentRange();
		},
		beforeShowDay: function(date) {
			var cssClass = '';
			var canSelect = true;
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			if( !start || !end )canSelect = false;
			else canSelect = date >= start && date <= end;
			if(date >= startDate && date <= endDate)
				cssClass = 'ui-state-highlight';
			return [canSelect, cssClass];
		},
		onChangeMonthYear: function(year, month, inst) {
			popupnct_selectCurrentRange();
		}
	});
	$('#popupnct_per-workload').keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		var _val = parseFloat(val, 10);
		if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}
	});
	
	function popupnct_get_list_assign(_this_id){
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
	function multiselect_popupnct_pmonChange(_this_id){
		popupnct_list_assigned = popupnct_get_list_assign( _this_id);
		popupnct_draw_employee_columns();
	}
	function popupnct_resetPicker(){
        if( $('#popupnct-range-type').val() == 0 ){
            $('#popupnct_date-list').multiDatesPicker('resetDates');
            $('.popupnct-date').each(function(){
                var value = $(this).text();
                $('#popupnct_date-list').multiDatesPicker('addDates', [$.datepicker.parseDate('dd-mm-yy', value)]);
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
            // popupnct_changeTotal(this);
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
	function popupnct_addRange(start, end, reset){
        if( reset ){
            //xoa het tat ca workload
            $('#popupnct-assign-table tbody').html('');
            popupnct_selectRange();
        }
        var type = parseInt($('#popupnct-range-type').val());
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
                if( ($.inArray(_curDate, Holidays[_curYear]) == -1) && popupnct_isValidDateForNctTaskDate(date.date) ){
                    _popupnct_addRange(date.date, date.date);
                }
            } else {
                if( !minDate || minDate >= date.start ){
                    minDate = new Date(date.start);
                }
                if( !maxDate || maxDate <= date.end ){
                    maxDate = new Date(date.end);
                }
                _popupnct_addRange(date.start, date.end);
            }
        });
        $('#popupnct-start-date').datepicker('setDate', minDate);
        $('#popupnct-end-date').datepicker('setDate', maxDate);
    }
    function popupnct_isValidDateForNctTaskDate(d){
        var date = d.getDay();
        return Workdays[date] == 1;
    }
    function _popupnct_addRange(start, end){
        var date = dateString(start) + '_' + dateString(end);
        var type = parseInt($('#popupnct-range-type').val());
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
            var html = '<tr><td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + rowName + '</td>';
            $('.header-cell').each(function(){
                var col = $(this);
                var id = col.prop('id').replace('popupnct_col-', '');
                var hide = '';
                if( !col.is(':visible') )hide = 'style="display: none"';
				var _id = id.split('-');
				var e_id=_id[0],
					e_ispc= _id[1] ? 1 : 0,
					ip_name = 'data[workloads][' + date + '][' + id + ']';
                html += '<td class="ntc-employee-col value-cell cell-' + id + '" ' + hide + '><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="ntc-employee-col p_workload p_workload-' + id + '" data-id="' + id + '"  name="' + ip_name + '[estimated]" value="0" onchange="popupnct_changeTotal(this)" data-ref/></td>';
            });
            html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td>';
			html += '<td class="row-action"><a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a></td>';
			html += '</tr>';
            $('#popupnct-assign-table tbody').append(html);
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
	$('#popupnct-start-date').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			return [Workdays[date] == 1, '', ''];
		},
		onSelect: function(){
			//showPicker();
			popupnct_refreshPicker();
		}
	});
	$('#popupnct-end-date').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#popupnct-start-date').datepicker('getDate'),
				check = Workdays[date] == 1;
			if( start != null ){
				check = check && start <= d;
			}
			return [ check, '', ''];
		},
		beforeShow: function(e, i){
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $(e).datepicker('getDate');
			if( !end && start ){
				return {defaultDate: start};
			}
		},
		onSelect: function(){
			//showPicker();
			popupnct_refreshPicker();
		}
	});
	function ProjectTaskAddPopupForm_afterSubmit(form_id, data){
		console.log( form_id, data );
		var form = $('#' + form_id);
		var msg = form.find('.form-message');
		var err_msg = form.find('.alert-message');
		if( data.success == true){
			
			location.reload();
			// err_msg.empty();
			// msg.text('<?php __('Saved');?>');
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
			location.reload();
		}else{
			msg.empty();
			err_msg.text(data.message);
		}
		
	}
	/* END NCT task */
	
/* END for Add new Task */
	jQuery.removeFromArray = function(value, arr) {
        return jQuery.grep(arr, function(elem, index) {
            return elem !== value;
        });
    };
	init_multiselect('#template_add_task .wd-multiselect, #template_add_nct_task .wd-multiselect');
	
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
				console.log( _reload);
				var _Dropzone = new Dropzone(_tag, {
					acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
					imageSrc: "/img/new-icon/draganddrop.png",
					dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders');?>",
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
					console.log( validate_function); 
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
		show_full_popup('#template_add_task');
	}
	// wd_radio_button();
	$(document).ready(function(){
		popupTask_phaseOnChange();
		nct_phaseOnChange();
		if( typeof re_init_afterload == 'function' ) re_init_afterload();
	});
</script>