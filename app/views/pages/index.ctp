<?php
$employee_info = $this->Session->read('Auth.employee_info');
$is_sas = $employee_info['Employee']['is_sas'];
$role = "";
if ($is_sas != 1) {
    $role = $employee_info['Role']['name'];
}
if(empty($employee_id)){
    $employee_id = $employee_info['Employee']['id'];
}
$is_new_design = 0;
if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1){
  $is_new_design = 1;
}
$hasManager = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profit = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
    'recursive' => -1,
    'conditions' => array('employee_id' => $employee_id)))));
if($hasManager){
    $profit = array_shift($profit);
}
$profit = !empty($profit['profit_center_id']) ? $profit['profit_center_id'] : '';
$this->is_pm = isset($employee_info['Role']['id']) && $employee_info['Role']['name'] =='pm';
$pm_control_resource = isset($employee_info['Role']['id']) && $employee_info['Role']['name'] =='pm' && $employee_info['CompanyEmployeeReference']['control_resource'] == '1';
$checkSeeResource = isset($employee_info['Role']['id']) && ($employee_info['Role']['name'] != "admin") && ($employee_info['Role']['name'] != "hr") && !$pm_control_resource;

?>
<style>
#wd-nav ul li {
    display: inline-block;
    float: none;
    margin-bottom: 35px;
}
.home-btn {
    float: left;
    margin: 25px;
}
.home-btn-panel {
    width: 100%;
    margin: 10% auto;
    padding: 0 15%;
}
.page-login:after{
  display: none;
}
<?php if( $isMobile || $isTablet ): ?>
#wd-nav ul.first-menu {
    display: inline-block;
    width: auto;
}
#wd-nav ul li {
    margin: 0 10px 20px 10px;
}
#wd-nav ul li a {
    font-size: 1.1em;
}
<?php endif ?>
</style>
<div class="home-btn-panel" style="margin: auto;">
    <?php if($is_sas || (!$is_sas && $role !='conslt' && $enableBusines == true)):?>
        <a class="home-btn" href="<?php echo $html->url('/sale_leads/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-business.svg" role="presentation" />
          </div>
          <span class="home-btn__text"><?php __("Business") ?></span>
        </a>
    <?php endif; ?>
    <?php if($is_sas || (!$is_sas && $enablePMS == true && ($role !='conslt'))):
    $opp = ($is_new_design == 1) ? $html->url('/projects_preview?cate=2') : $html->url('/projects?cate=2');
    $inp = ($is_new_design == 1) ? $html->url('/projects_preview?cate=1') : $html->url('/projects?cate=1');

    ?>
        <a class="home-btn" href="<?php echo $opp; ?> ">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-oppor.svg" role="presentation" />
          </div>
          <span class="home-btn__text"><?php __("Opportunity") ?></span>
        </a>
        <a class="home-btn" href="<?php echo $inp; ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-project.svg" role="presentation" />
          </div>
          <span class="home-btn__text"><?php __("Projects") ?></span>
        </a>
    <?php endif; ?>
    <?php if (!$checkSeeResource) : ?>
        <a class="home-btn" href="<?php echo $html->url('/employees/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-resources.svg" role="presentation" />
          </div>
          <span class="home-btn__text"><?php __("Employees") ?></span>
        </a>
	 <?php endif; ?>
	<?php if ($this->is_pm) : ?>
        <a class="home-btn" href="<?php echo $html->url('/user_views/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-views.svg" role="presentation" />
          </div>
          <span class="home-btn__text"><?php __("Personalized") ?></span>
        </a>
    <?php endif; ?>
    <?php if ($is_sas || $role == "admin") : ?>
        <a class="home-btn" href="<?php echo $html->url('/administrators/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-admin.svg" role="presentation" />
          </div>
          <span class="home-btn__text"><?php __("Administration") ?></span>
        </a>
    <?php endif; ?>
    <?php if($is_sas || (!$is_sas && $enableAudit == true && $seeMenuAudit == true)):?>
        <a class="home-btn" href="<?php echo $html->url('/audit_missions/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-audit.svg" role="presentation" width="50px"/>
          </div>
          <span class="home-btn__text"><?php __("Audit") ?></span>
        </a>
    <?php endif; ?>
    <?php if($is_sas || (!$is_sas && $enableRMS == true)):?>
        <a class="home-btn" href="<?php echo $html->url('/activity_forecasts/request/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-activity.svg" role="presentation" width="50px"/>
          </div>
          <span class="home-btn__text"><?php __("Activity") ?></span>
        </a>
    <?php endif; 
		$display_absence_tab = isset($companyConfigs['display_absence_tab']) ? $companyConfigs['display_absence_tab'] : 1;
		if($display_absence_tab){ 
		?>
        <a class="home-btn" href="<?php echo $html->url('/absence_requests/index/month') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-absence.svg" role="presentation" width="50px"/>
          </div>
          <span class="home-btn__text"><?php __("Absence") ?></span>
        </a>
		<?php } ?>
        <a class="home-btn" href="<?php echo $html->url("/activity_forecasts/my_diary?profit=" . $profit) ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-diary.svg" role="presentation" width="50px"/>
          </div>
          <span class="home-btn__text"><?php __("My Diary") ?></span>
        </a>
    <?php if($is_sas || (!$is_sas && $role !='conslt' && $enableReport == true)): ?>
		<?php 
			$report_url = $html->url('/reports/');
			$report_tab = '';
			if(!$is_sas) {
				$list_report = ClassRegistry::init('SqlManagerEmployee')->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' =>$employee_info['Company']['id'],
						'employee_id' => $employee_info['Employee']['id'], 
					),
					'fields' => array('id', 'sql_manager_id'),
				));
				$count_report = count($list_report);
				if($count_report == 1){
					$sql_manager_id = array_values($list_report);
					$report_url = $html->url(array('controller' => 'reports', 'action' => 'viewReport', $sql_manager_id[0]));
					$report_tab = 'target="_blank"';
				}
			}
		?>
        <a class="home-btn" href="<?php echo $report_url; ?>"  <?php echo $report_tab; ?>>
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-report.svg" role="presentation" width="50px"/>
          </div>
		  
          <span class="home-btn__text"><?php __("Report") ?></span>
        </a>
    <?php endif; ?>
    <?php if($is_sas || (!$is_sas && $role !='conslt' && $enableZogMsgs == true)):?>
<!--        <a class="home-btn" href="">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-z0g.svg" role="presentation" width="50px"/>
          </div>
          <span class="home-btn__text"><?php // __("ZogMsg") ?></span>
        </a>-->
    <?php endif; ?>
    <?php if($is_sas || (!$is_sas && $enableTicket == true)):?>
        <a class="home-btn" href="<?php echo $html->url('/tickets/') ?>">
          <div class="home-btn__icon-wrapper">
            <img class="home-btn__icon" alt="" src="/img_z0g/icon-index-ticket.svg" role="presentation" width="50px"/>
          </div>
          <span class="home-btn__text"><?php __("Tickets") ?></span>
        </a>
    <?php endif; ?>
</div>
