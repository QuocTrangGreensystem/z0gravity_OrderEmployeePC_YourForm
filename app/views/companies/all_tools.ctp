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
#accordion h3.head-title:after {
	content: none !important;
}
</style>
<?php
	$employee_info = $this->Session->read('Auth.employee_info');
	$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
	$is_admin = isset($employee_info['Role']['name']) && $employee_info['Role']['name'] =='admin';
?>
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
                                <div class="wd-table" id="accordion">
									<?php if($is_sas){?>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/companies/index_plus'); ?>"><?php __("Delete multi company"); ?></a></h3>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/companies/remove_sub_task_nct'); ?>"><?php __("Delete sub sub task in Project Archived"); ?></a></h3>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/companies/remove_project_task_normal'); ?>"><?php __("Delete normal task in Project Archived"); ?></a></h3>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/companies/remove_project_task_nct'); ?>"><?php __("Delete nct task in Project Archived"); ?></a></h3>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/companies/remove_nct_task_no_workload_no_asigned'); ?>"><?php __("Delete NTC task with a workload = 0 with no resource assigned for Archived project"); ?></a></h3>
									<?php }?>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/recycle_bins/deleteTimesheetValidatedZeroValue'); ?>"><?php __("Delete timesheet validated with zero value"); ?></a></h3>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/recycle_bins/deleteAttachmentFileOfTaskNotExists'); ?>"><?php __("Delete attachment file of task not exists"); ?></a></h3>
									<?php if($is_sas){?>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/recycle_bins/update_login_history'); ?>"><?php __("Update login history"); ?></a></h3>
									<?php }?>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/recycle_bins/generateAvatar'); ?>"><?php __("Generate Avatar"); ?></a></h3>
									<?php if($is_sas){?>
										<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/recycle_bins/updateProjectBudgetSyn'); ?>"><?php __("Sync Budget Internal"); ?></a></h3>
									<?php } ?> 
									<?php if(!$is_sas && $is_admin){?>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/cleanup/'); ?>"><?php __("Cleanup data"); ?></a></h3>
									<?php }?>
									<h3 class="head-title "><a target="_blank" href="<?php echo $html->url('/recycle_bins/update_data_activity_id'); ?>"><?php __("Update data activity"); ?></a></h3>
								
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>