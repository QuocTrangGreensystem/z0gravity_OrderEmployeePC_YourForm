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
$hasManager = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profit = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
    'recursive' => -1,
    'conditions' => array('employee_id' => $employee_id)))));
if($hasManager){
    $profit = array_shift($profit);
}
$profit = !empty($profit['profit_center_id']) ? $profit['profit_center_id'] : '';
?>
<style>
.content-wrapper {
	background: #002d51;
	background-position: center 0;
}
#wd-nav {
	padding-top:40px;
	display:inline;
}
#wd-nav-2 {
	display:none;
}
#wd-nav ul{
	overflow: hidden;
}
#wd-nav li {
	list-style:none;
	float:left;
	width:131px;
	text-align:center;
	margin-right:30px;
}
.last-menu {
	width:775px;
	margin:10px auto;
	padding-left:0px;
}
.last-menu li:last-child {
	margin-right:0px !important;
}
.first-menu {
	width:936px;
	margin:30px auto;
	padding-left:0px;
}
.first-menu li:last-child {
	margin-right:0px !important;
}
.row-menu {
	padding-left:15px;
	clear:both;
}
.menu-item {
	padding:5px;
	float:left;
	width:50%;
	/*background:#fff;*/
}
.menu-item img {
	width:60px;
    height:60px;
    border-radius: 0.4em;
    border: 0.25em solid rgba(255, 255, 255, 0.4);
    padding: 0.7em;
}
.menu-item img:hover{
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    background: linear-gradient(to right, rgba(26, 107, 188, 0.99) 15%, rgba(47, 218, 244, 0.99) 100%);
    border-color: #00fff7;
}
.menu-item .wd-name {
    margin-left: 5px;
    font-size: 0.75em;
    color: #fff;
}
#wd-nav li img {
	width:131px;
	height:auto;
}
.wd-name {
	width:131px;
    margin-top: 15px;
	font-weight: bolder;
	color: #99b4cd;
	font-size: 1.3em;
	text-transform: capitalize;
}
@media screen and (max-width: 1226px) {
    #wd-nav li {
		width:100px;
		margin-right:25px;
	}
	#wd-nav li img{
		width:100px;
		height:auto;
	}
	.wd-name {
		width:100px;
	}
	.first-menu {
		width:725px;
	}
	.last-menu {
		width:600px;
	}
	.wd-name {
		font-size: 1.1em;
	}
}
@media screen and (max-width: 1005px) {
    #wd-nav li {
		width:75px;
		margin-right:20px;
	}
	#wd-nav li img{
		width:75px;
		height:auto;
	}
	.wd-name {
		width:75px;
	}
	.first-menu {
		width:550px;
	}
	.last-menu {
		width:455px;
	}
	.wd-name {
		font-size: 0.9em;
	}
}
@media screen and (max-width: 550px) {
    #wd-nav {
		display:none;
	}
	#wd-nav-2 {
		display:inline;
		background-color:#000;
		width:100%;
	}
}
</style>
<div id="wd-nav">
	<div class="row">
		<ul class="<?php echo (($is_sas || $role == "admin") ? 'first-menu' : ''); ?>">
			<?php if($is_sas || (!$is_sas && $seeMenuBusiness == true)):?>
			<li><a href="<?php echo $html->url('/sale_leads/') ?>"><img src="/img_z0g/icon-index-business.svg" alt="Business"/><span class="wd-name"><?php __("Business") ?></span></a></li>
			<?php endif; ?>
			<?php if($is_sas || (!$is_sas && $enablePMS == true && $role !='conslt')):?>
			<li><a href="<?php echo $html->url('/projects?cate=2') ?>"><img src="/img_z0g/icon-index-oppor.svg" alt="Opportunity"/><span class="wd-name"><?php __("Opportunity") ?></span></a></li>
			<li><a href="<?php echo $html->url('/projects?cate=1') ?>"><img src="/img_z0g/icon-index-project.svg" alt="Projects"/><span class="wd-name"><?php __("Projects") ?></span></a></li>
			<?php endif; ?>
            <?php if($is_sas || (!$is_sas && ($role !='conslt') && $enableZogMsgs == true)):?>
                <li><a href="<?php echo $html->url('/zog_msgs/') ?>"><img src="/img_z0g/icon-index-z0g.svg" alt="ZogMsg"/><span class="wd-name"><?php __("ZogMsg") ?></span></a></li>
            <?php endif; ?>
			<?php if ($is_sas || $role !='conslt') : ?>
			<li><a href="<?php echo $html->url('/employees/') ?>"><img src="/img_z0g/icon-index-resources.svg" alt="Employees"/><span class="wd-name"><?php __("Employees") ?></span></a></li>
			<li><a href="<?php echo $html->url('/user_views/') ?>"><img src="/img_z0g/icon-index-views.svg" alt="Personalized"/><span class="wd-name"><?php __("Personalized") ?></span></a></li>
			<?php endif; ?>
			<?php if ($is_sas || $role == "admin") : ?>
				<li><a href="<?php echo $html->url('/administrators/') ?>"><img src="/img_z0g/icon-index-admin.svg" alt="Administration"/><span class="wd-name"><?php __("Administration") ?></span></a></li>
			<?php endif; ?>
            <?php if($is_sas || (!$is_sas && $enableTicket == true)):?>
                <li><a href="<?php echo $html->url('/tickets/') ?>"><img src="/img_z0g/icon-index-ticket.svg" alt="Tickets"/><span class="wd-name"><?php __("Tickets") ?></span></a></li>
            <?php endif; ?>
		</ul>
	</div>
	<div class="row">
		<ul class="last-menu">
            <?php if($is_sas || (!$is_sas && $enableReport == true )):?>
			<li><a href="<?php echo $html->url('/reports/') ?>"><img src="/img_z0g/icon-index-report.svg" alt="Report"/><span class="wd-name"><?php __("Report") ?></span></a></li>
            <?php endif; ?>
            <?php if($is_sas || (!$is_sas && $enableAudit == true && $seeMenuAudit == true)):?>
			<li><a href="<?php echo $html->url('/audit_missions/') ?>"><img src="/img_z0g/icon-index-audit.svg" alt="Audit"/><span class="wd-name"><?php __("Audit") ?></span></a></li>
			<?php endif; ?>
			<?php if($is_sas || (!$is_sas && $enableRMS == true)):?>
			<li><a href="<?php echo $html->url('/activity_forecasts/request/') ?>"><img src="/img_z0g/icon-index-activity.svg" alt="Activity"/><span class="wd-name"><?php __("Activity") ?></span></a></li>
			<li><a href="<?php echo $html->url('/employee_absences/index/'. $employee_info['Employee']['id']) ?>"><img src="/img_z0g/icon-index-absence.svg" alt="Absence"/><span class="wd-name"><?php __("Absence") ?></span></a></li>
			<li><a href="<?php echo $html->url("/activity_forecasts/my_diary?profit=" . $profit) ?>"><img src="/img_z0g/icon-index-diary.svg" alt="My Diary"/><span class="wd-name"><?php __("My Diary") ?></span></a></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<div id="wd-nav-2">
	<?php if($is_sas || (!$is_sas && $enableBusines == true && $seeMenuBusiness == true)):?>
	<div class="menu-item"><a href="<?php echo $html->url('/sale_leads/') ?>"><img src="/img_z0g/icon-index-business.svg" alt="Business"/><span class="wd-name"><?php __("Business") ?></span></a></div>
	<?php endif; ?>
	<?php if($is_sas || (!$is_sas && $enablePMS == true && $role !='conslt')):?>
	<div class="menu-item"><a href="<?php echo $html->url('/projects?cate=2') ?>"><img src="/img_z0g/icon-index-oppor.svg" alt="Opportunity"/><span class="wd-name"><?php __("Opportunity") ?></span></a></div>
	<div class="menu-item"><a href="<?php echo $html->url('/projects?cate=1') ?>"><img src="/img_z0g/icon-index-project.svg" alt="Projects"/><span class="wd-name"><?php __("Projects") ?></span></a></div>
	<?php endif; ?>
    <?php if($is_sas || (!$is_sas && ($role !='conslt') && $enableZogMsgs == true)):?>
        <div class="menu-item"><a href="<?php echo $html->url('/zog_msgs/') ?>"><img src="/img_z0g/icon-index-z0g.svg" alt="ZogMsg"/><span class="wd-name"><?php __("ZogMsg") ?></span></a></div>    <?php endif; ?>
	<?php if ($is_sas || $role !='conslt') : ?>
	<div class="menu-item"><a href="<?php echo $html->url('/employees/') ?>"><img src="/img_z0g/icon-index-resources.svg" alt="Employees"/><span class="wd-name"><?php __("Employees") ?></span></a></div>
	<div class="menu-item"><a href="<?php echo $html->url('/user_views/') ?>"><img src="/img_z0g/icon-index-views.svg" alt="Personalized"/><span class="wd-name"><?php __("Personalized") ?></span></a></div>
	<?php endif; ?>
	<?php if ($is_sas || $role == "admin") : ?>
		<div class="menu-item"><a href="<?php echo $html->url('/administrators/') ?>"><img src="/img_z0g/icon-index-admin.svg" alt="Administration"/><span class="wd-name"><?php __("Administration") ?></span></a></div>
	<?php endif; ?>
	<div class="menu-item"><a href="<?php echo $html->url('/reports/') ?>"><img src="/img_z0g/icon-index-report.svg" alt="Report"/><span class="wd-name"><?php __("Report") ?></span></a></div>
	<?php if($is_sas || (!$is_sas && $enableAudit == true && $seeMenuAudit == true)):?>
	<div class="menu-item"><a href="<?php echo $html->url('/audit_missions/') ?>"><img src="/img_z0g/icon-index-audit.svg" alt="Audit"/><span class="wd-name"><?php __("Audit") ?></span></a></div>
	<?php endif; ?>
	<?php if($is_sas || (!$is_sas && $enableRMS == true)):?>
	<div class="menu-item"><a href="<?php echo $html->url('/activity_forecasts/request/') ?>"><img src="/img_z0g/icon-index-activity.svg" alt="Activity"/><span class="wd-name"><?php __("Activity") ?></span></a></div>
	<div class="menu-item"><a href="<?php echo $html->url('/absence_requests/') ?>"><img src="/img_z0g/icon-index-absence.svg" alt="Absence"/><span class="wd-name"><?php __("Absence") ?></span></a></div>
	<div class="menu-item"><a href="<?php echo $html->url("/activity_forecasts/my_diary?profit=" . $profit) ?>"><img src="/img_z0g/icon-index-diary.svg" alt="My Diary"/><span class="wd-name"><?php __("My Diary") ?></span></a></div>
	<?php endif; ?>
    <?php if($is_sas || (!$is_sas && $enableTicket == true)):?>
        <div class="menu-item"><a href="<?php echo $html->url("/tickets/") ?>"><img src="/img_z0g/icon-index-ticket.svg" alt="Tickets"/><span class="wd-name"><?php __("Tickets") ?></span></a></div>
    <?php endif; ?>

</div>
