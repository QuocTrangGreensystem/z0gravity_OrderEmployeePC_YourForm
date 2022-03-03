<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="Project management software | AZURÉE. AZURÉE is an enterprise project management system that has all tools for managing projects online.">
		<meta name="keywords" content="AZURÉE, Project Management, AZURÉE, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
		<meta name="author" content="Global SI - Green System Solutions">
		<meta name="language" content="US, FR">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF-8">
        <link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
		<title><?php __("z0 Gravity :: Project Management") ?></title>

		<?php echo $html->css('mobile/bootstrap'); ?>
		<?php echo $html->css('mobile/AdminLTE'); ?>
		<?php echo $html->css('mobile/alertify.min'); ?>
		<?php echo $html->css('mobile/themes/bootstrap'); ?>
		<?php echo $html->css('mobile/skins/skin-blue'); ?>
		<?php echo $html->css('mobile/common'); ?>
		<?php echo $html->css('prettify'); ?>
		<?php echo $html->css('datepicker'); ?>

		<?php echo $html->script('jquery-1.11.3.min'); ?>
        <?php echo $html->script('jquery.cookie'); ?>
        <?php echo $html->script('jquery.browser'); ?>
		<?php echo $html->script('modernizr.custom'); ?>
		<?php echo $html->script('common'); ?>
		<?php echo $html->script('bootstrap.min'); ?>
		<!--[if IE]><?php echo $html->script('excanvas.compiled.js'); ?><![endif]-->
		<?php echo $html->script('jquery.bt'); ?>
		<!-- alertify -->
		<?php echo $html->script('alertify.min'); ?>
		<!-- ajax form -->
		<?php echo $html->script('jquery.form'); ?>
		<?php echo $html->script('jquery-ui.min'); ?>
		<?php echo $html->script('prettify'); ?>
		<?php echo $html->script('bootstrap-datepicker'); ?>
		<?php echo $html->script('app'); ?>
		<?php echo $html->script('common'); ?>
        <?php echo $html->script('newDesign/sound'); ?>
		<?php echo $html->css('common'); ?>
        <?php echo $html->script('modernizr.custom'); ?>
        <?php echo $html->script('jquery.ui.core'); ?>
        <?php echo $html->script('jquery.cookie'); ?>
        <?php echo $html->script('z0.history'); ?>
        <?php echo $html->script('green/tooltip'); ?>
        <?php echo $html->css('green/tooltip'); ?>
		<script>
		$.fn.datepicker.dates['en'] = {
			days: <?php echo json_encode(array(__('Sunday', true), __('Monday', true), __('Tuesday', true), __('Wednesday', true), __('Thursday', true), __('Friday', true), __('Saturday', true))) ?>,
			daysShort: <?php echo json_encode(array(__('Sun', true), __('Mon', true), __('Tues', true), __('Wed', true), __('Thu', true), __('Fri', true), __('Sat', true))) ?>,
			daysMin: <?php echo json_encode(array(__('Su', true), __('Mo', true), __('Tu', true), __('We', true), __('Th', true), __('Fr', true), __('Sa', true))) ?>,
			months: <?php echo json_encode(array(__('January', true), __('February', true), __('Marsh', true), __('April', true), __('May', true), __('June', true), __('July', true),  __('August', true),  __('September', true),  __('October', true),  __('November', true),  __('December', true))) ?>,
			monthsShort: <?php echo json_encode(array(__('Jan', true), __('Feb', true), __('Mar', true), __('Apr', true), __('May', true), __('Jun', true), __('Jul', true),  __('Aug', true),  __('Sep', true),  __('Oct', true),  __('Nov', true),  __('Dec', true))) ?>,
			today: <?php echo json_encode(__('Today', true)) ?>,
			clear: <?php echo json_encode(__('Clear', true)) ?>,
			format: "dd/mm/yyyy",
			titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
			weekStart: 1
		};
		alertify.defaults.glossary.title = '&nbsp;';
		alertify.defaults.glossary.ok = '<?php __('OK') ?>';
		alertify.defaults.glossary.cancel = '<?php __('Cancel') ?>';
		</script>
	</head>
<?php
$langCode = Configure::read('Config.langCode');
$versionModel = ClassRegistry::init('Version');
$version = $versionModel->find('first',array('conditions'=>array('Version.is_current_version'=>1),'fields'=>'name'));
if( empty($version) ){
	$version = $versionModel->find('first',array('fields'=>'name', 'order' => array('updated' => 'DESC'), 'limit' => 1));
}
$employee_info = $this->Session->read('Auth.employee_info');
$companyNameLogin = !empty($employee_info['Company']['dir']) ? strtolower($employee_info['Company']['dir']) : '';
$employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
$avatarLogins = !empty($employee_info['Employee']['avatar_resize']) ? $employee_info['Employee']['avatar_resize'] : '';
$urlAvatar = $html->url('/img/front/no-photo-small.png');
if(!empty($avatarLogins)){
	// $urlAvatar = $html->url('/user_files/avatar/' . $companyNameLogin . '/' . $employeeIdLogin . '/' . $avatarLogins);
	$urlAvatar = $this->UserFile->avatar($employeeIdLogin, 'large');
}
$_domain = $_SERVER['SERVER_NAME'];


$AppStatusProject = $this->Session->read('App.status_oppor');
$employee_info = $this->Session->read('Auth.employee_info');
$is_sas = $employee_info['Employee']['is_sas'];
$role = ($is_sas != 1) ? $employee_info['Role']['name'] : '';
$employee_id = empty($employee_id) ? $employee_info['Employee']['id'] : $employee_id;
$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
$category = 0;
$check_budget_actis = $check_budget_actis_AC = $activatedRevieWhenClickDetail = $activatedViewWhenClickDetail = false;
$adminAudits = $createMenus = array();
$seeMenuAudit = $this->Session->read('seeMenuAudit');
$seeMenuBusiness = $this->Session->read('seeMenuBusiness');
$enablePMS = $this->Session->read('enablePMS');
$enableRMS = $this->Session->read('enableRMS');
$enableAudit = $this->Session->read('enableAudit');
$enableReport = $this->Session->read('enableReport');
$enableBusines = $this->Session->read('enableBusines');
$actedActiView = $this->Session->read('ActedActiFunc');
$actedActiView = !empty($actedActiView) ? $actedActiView : 'review';

//profit center
$canManageResource = $role == 'pm' && $employee_info['CompanyEmployeeReference']['control_resource'];

$conds = array('manager_id' => $employee_id, 'manager_backup_id' => $employee_id);
if( $canManageResource )$conds['id'] = $employee_info['Employee']['profit_center_id'];
$hasManager = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profit = ClassRegistry::init('ProfitCenter')->find('first', array(
	'recursive' => -1,
	'conditions' => array('OR' => $conds)))));
if($hasManager){
	$profit = array_shift($profit);
}

$hasManagerMyDiary = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profitMyDiary = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
	'recursive' => -1,
	'conditions' => array('employee_id' => $employee_id)))));
if($hasManagerMyDiary){
	$profitMyDiary = array_shift($profitMyDiary);
}
$profitMyDiary = !empty($profitMyDiary['profit_center_id']) ? $profitMyDiary['profit_center_id'] : '';
if(empty($profit['id'])){
	$profit['id'] = $employee_info['Employee']['profit_center_id'];
}
?>

	<body class="<?php echo $langCode; ?> skin-blue">
		<div class="wrapper">
			<header class="main-header">
				<!-- Logo -->
				<a href="<?php echo $this->Html->url('/') ?>" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><b>0</b>G</span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"><?php echo $this->Html->image('/img_z0g/logo-small.png') ?></span>
				</a>
				<!-- Header Navbar: style can be found in header.less -->
				<nav class="navbar navbar-static-top" role="navigation">
					<!-- Sidebar toggle button-->
					<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
					</a>
					<!-- Navbar Right Menu -->
					<div class="navbar-custom-menu mobile-custom-menu">

						<?php if ($this->params['controller']=='absence_requests'&&$this->params['action']=='mobile-index') {
						?>
							<?php
							echo $this->Form->create('Control', array(
								'type' => 'get',
								'url' => '/' . Router::normalize($this->here),
								'class' => 'form-inline'
							));
							?>
								<div class="form-group-menu">
									<?php
										echo $this->element('week_absence');
										if($isManage){
											echo $this->Form->hidden('id', array('value' => $this->params['url']['id']));
											echo $this->Form->hidden('profit', array('value' => $this->params['url']['profit']));
										}
									?>

									<!-- <div class="form-group-menu">
										<?php
										echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false, 'class' => 'form-control input-sm'));
										?>
									</div>
									<div class="form-group-menu">
										<?php
										echo $this->Form->month('month', date('m', $_start), array('empty' => false, 'class' => 'form-control input-sm'));
										echo $this->Form->hidden('get_path', array('value' => $getDataByPath));
										?>

									</div> -->
									<a id="calendar" href="javascript:void(0);" class="btn btn-menu btn-sm"><i class="glyphicon glyphicon-calendar"></i></a>
									<!-- <button type="submit" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-chevron-right"></i></button> -->
									<?php if (empty($requestMessage) && !$isManage) : ?>
										 <button type="button" id="send-email" class="btn btn-success btn-sm" title="<?php __('Send request message')?>"><i class="glyphicon glyphicon-envelope"></i></button>
									<?php endif; ?>
								</div>
								<script>
									$(function(){
										var d = new Date($(".table-request tbody tr:first").attr("data-date")*1000);
										$('#calendar').datepicker('setDate', d);
										$("#calendar").datepicker('update');
										$('#calendar').datepicker().on('changeDate', function(ev){
											var currentUrl = updateQueryStringParameter(location.href,'week',getWeekNumber(Date.parse($('#calendar').data('date')))[1]);
											currentUrl = updateQueryStringParameter(currentUrl,'year',getWeekNumber(Date.parse($('#calendar').data('date')))[0]);
											location.href = currentUrl;
											$('#calendar').datepicker('hide');
										});
									});
								</script>
							<?php
							echo $this->Form->end();
							?>
						<?php
							} else if ($this->params['controller']=='activity_forecasts'&&$this->params['action']=='mobile-request') {
						?>
						<?php
						echo $this->Form->create('Control', array(
							'type' => 'get',
							'url' => '/' . Router::normalize($this->here),
							'class' => 'form-inline'
						));
						?>
							<div class="form-group-menu">
								<?php
									echo $this->element('week_activity');
									$idManageCheck = null;
									if($isManage){
										echo $this->Form->hidden('id', array('value' => $this->params['url']['id']));
										$idManageCheck = $this->params['url']['id'];
										echo $this->Form->hidden('profit', array('value' => $this->params['url']['profit']));
									}
								?>
								<a id="calendar" href="javascript:void(0);" class="btn btn-menu btn-sm"><i class="glyphicon glyphicon-calendar"></i></a>
								<a id="open-favourite" href="javascript:void(0);" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-star"></i></a>
								<a id="open-filter" href="javascript:void(0);"class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i></a>
							</div>
						<?php echo $this->Form->end() ?>
						<script>
						var buildMenu_url = '<?php echo $html->url(array('action' => 'contextMenu', $idManageCheck)); ?>';
						var refresh_url = '<?php echo $html->url(array('action' => 'cleanupCacheMenu', $idManageCheck)); ?>';

						$('#absence-prev').addClass('btn btn-menu btn-sm').html('<i class="glyphicon glyphicon-arrow-left"></i>');
						$('#absence-next').addClass('btn btn-menu btn-sm').html('<i class="glyphicon glyphicon-arrow-right"></i>');
						$('.currentWeek').html(" ");
						$('.currentWeek').css("margin","0px");
						</script>
						<script>
							$(function(){
								var profit = <?php echo $profit['id'];?>;
								var employeeId = <?php echo $employeeName['id'];?>;
								var d = new Date($(".table-request tbody tr:first").attr("data-date")*1000);
								$('#calendar').datepicker('setDate', d);
								$("#calendar").datepicker('update');
								$('#calendar').datepicker().on('changeDate', function(ev){
									var currentUrl = updateQueryStringParameter(location.href,'week',getWeekNumber(Date.parse($('#calendar').data('date')))[1]);
									currentUrl = updateQueryStringParameter(currentUrl,'year',getWeekNumber(Date.parse($('#calendar').data('date')))[0]);
									currentUrl = updateQueryStringParameter(currentUrl,'profit',profit);
									currentUrl = updateQueryStringParameter(currentUrl,'id',employeeId);
									location.href = currentUrl;
									$('#calendar').datepicker('hide');
								});
							});
						</script>
						<?php
							} elseif ($this->params['controller']=='activity_forecasts'&&$this->params['action']=='mobile-my_diary') {
						?>
						<?php
						echo $this->Form->create('Control', array(
							'type' => 'get',
							'url' => '/' . Router::normalize($this->here),
							'class' => 'form-inline'
						));
						?>
							<div class="form-group-menu">
								<?php
									echo $this->element('week_activity');
								?>
								<a id="calendar" href="javascript:void(0);" class="btn btn-menu btn-sm"><i class="glyphicon glyphicon-calendar"></i></a>
							</div>
							<script>
								$(function(){
									var d = new Date($(".table-request tbody tr:first").attr("data-date")*1000);
									$('#calendar').datepicker('setDate', d);
									$("#calendar").datepicker('update');
									$('#calendar').datepicker().on('changeDate', function(ev){
										var currentUrl = updateQueryStringParameter(location.href,'week',getWeekNumber(Date.parse($('#calendar').data('date')))[1]);
										currentUrl = updateQueryStringParameter(currentUrl,'year',getWeekNumber(Date.parse($('#calendar').data('date')))[0]);
										location.href = currentUrl;
										$('#calendar').datepicker('hide');
									});
								});
							</script>
						<?php echo $this->Form->end() ?>
						<?php

							}
						?>
					</div>
					<div class="navbar-custom-menu mobile-popup-menu">
						<?php if ($this->params['controller']=='absence_requests'&&$this->params['action']=='mobile-index') { ?>
						<div class="dropdown">
							<button id="request-menu" class="btn btn-request text-white" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="glyphicon glyphicon-th-list"></i>
							</button>
							<ul class="dropdown-menu left-position" aria-labelledby="request-menu" id="menu">
								<li><a href="javascript:;" class="action text-primary" data-action="add-comment"><?php __('Add a comment') ?></a></li>
								<li><a href="javascript:;" class="action text-danger" data-action="remove-request"><?php __('Remove request') ?></a></li>
								<li role="separator" class="divider"></li>
<?php
foreach($absences as $absence):
	$tag = isset($absence['request']) && $absence['request'] ? ($absence['request'] . '/' . ($absence['total'] ? $absence['total'] : 'NA')) : '';
	if( $tag )$tag = ' <span>(' . $tag . ')</span>';
?>
									<li><a href="javascript:;" class="action" data-document="<?php echo isset($absence['document']) ? $absence['document'] : '' ?>" data-action="add-request" data-absence-id="<?php echo $absence['id'] ?>"><?php echo $absence['print'] ?><?php echo $tag ?></a></li>
<?php
endforeach;
?>
							</ul>
						</div>
						<?php } else if ($this->params['controller']=='activity_forecasts'&&$this->params['action']=='mobile-request') {
						?>
							<div class="form-group-menu">
								<?php if (empty($isManage) || ($isManage && ($requestConfirm == -1 || $requestConfirm == 1))) : ?>
									<?php if (($requestConfirm == -1 || $requestConfirm == 1) && $typeSelect != 'year'): ?>
										<?php if($activateCopy == 1):?>
											<a href="#" id="copy_forecast" data-toggle="modal" data-target="#modal-copy-forecast" class="copy-timesheet" title="<?php __('Copy Forecast')?>"><span><?php __('Copy Forecast'); ?></span></a>
										<?php endif; ?>
										<a href="javascript:void(0)" id="submit-request-all-top" class="send-for-validate send-for-validate-top" title="<?php __('Send')?>"><span><?php __('Request validate'); ?></span></a>
										<a href="javascript:void(0)" class="" id="refresh_menu" title="<?php __('Refresh Menu')?>"><span><?php __('Refresh Menu') ?></span></a>
									<?php endif; ?>
								<?php else : ?>
									<?php
										$employee_info = $this->Session->read('Auth.employee_info');
										$is_sas = $employee_info['Employee']['is_sas'];
										if ($is_sas != 1) {
											$role = $employee_info['Role']['name'];
										}
									$canManageResource = $employee_info['CompanyEmployeeReference']['role_id'] == 3 && $employee_info['CompanyEmployeeReference']['control_resource'];
									?>
									<?php if ($requestConfirm != 2 && $typeSelect != 'year') : ?>
										<a href="javascript:;" id="submit-request-ok-top" class="btn btn-menu-success btn-sm" title="<?php __('Validate Requested')?>"><i class="glyphicon glyphicon-ok"></i></a>
									<?php endif; ?>
									<?php if($requestConfirm == 0 && $typeSelect != 'year'):?>
										<a href="javascript:;" id="submit-request-no-top" class="btn btn-menu-danger btn-sm" title="<?php __('Reject Requested')?>"><i class="glyphicon glyphicon-remove"></i></a>
									<?php endif;
									?>
									<?php if($requestConfirm == 2 && ( $role == "admin" || $canManageResource || ( $isPCManager && $role == 'pm' ) ) && $typeSelect != 'year'):?>
										<a href="javascript:;" id="submit-request-no-top" class="btn btn-menu-danger btn-sm" title="<?php __('Reject Requested')?>"><i class="glyphicon glyphicon-remove"></i></a>
									<?php endif;?>
								<?php endif; ?>
							</div>
						<?php } else if ($this->params['controller']=='activity_forecasts'&&$this->params['action']=='mobile-my_diary') { ?>
							<?php $urlExport =str_replace('my_diary','export_my_diary',$_SERVER['REQUEST_URI']);?>
							<a href="<?php echo $this->Html->url($urlExport);?>" title="<?php __('Export OutLook')?>"><img src="<?php echo $this->Html->url('/img/outlook.png') ?>" alt="" /></a>
						<?php } ?>
					</div>
				</nav>
			</header>
			<aside class="main-sidebar" style="background: rgba(4, 60, 95, 1);">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar" style="height: auto;">
					<!-- Sidebar user panel -->
					<div class="user-panel">
						<div class="pull-left image">
							<img src="<?php echo $urlAvatar ?>" class="img-circle" alt="User Image">
						</div>
						<div class="pull-left info">
							<p><?php echo isset($fullname) ? $fullname : '' ?></p>
							<ul class="list-inline" style="">
								<li class="<?php echo ($langCode == 'en' ? 'selected' : ''); ?> lang"><a href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" title="English"><?php echo $html->image('/img_z0g/en.png') ?></a></li>
								<li class="<?php echo ($langCode == 'fr' ? 'selected' : ''); ?> lang"><a href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" title="French"><?php echo $html->image('/img_z0g/fr.png') ?></a></li>
							</ul>
						</div>
					</div>
					<ul class="sidebar-menu">
						<li><a href="<?php echo $this->here . Router::queryString(array('mobile' => $mobileEnabled ? 0 : 1)) ?>" class="text-primary"><i class="glyphicon glyphicon-home"></i></a></li>
						<li class="treeview <?php if( in_array($currentAction, array('projects/index', 'project_amrs/index_plus')) )echo 'active' ?>">
							<a href="#">
								<i class="glyphicon glyphicon-briefcase"></i> <span><?php __('Projects') ?></span> <i class="glyphicon glyphicon-chevron-down pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li <?php if( isset($this->params['url']['cate']) && $this->params['url']['cate'] == 1 )echo 'class="active"' ?>><a href="<?php echo $this->Html->url('/projects/?cate=1') ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('Projects') ?></a></li>
								<li <?php if( isset($this->params['url']['cate']) && $this->params['url']['cate'] == 2 )echo 'class="active"' ?>><a href="<?php echo $this->Html->url('/projects/?cate=2') ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('Opportunity') ?></a></li>
							</ul>
						</li>
						<li class="treeview <?php if( in_array($currentAction, array('absence_requests/index', 'absence_requests/manage')) )echo 'active' ?>">
							<a href="#">
								<i class="glyphicon glyphicon-user"></i> <span><?php __('Absence') ?></span> <i class="glyphicon glyphicon-chevron-down pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li <?php if( $currentAction == 'absence_requests/index' )echo 'class="active"' ?>><a href="<?php echo $this->Html->url('/absence_requests/index/week') ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('Request') ?></a></li>
							<?php if ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager) : ?>
								<li><a href="<?php echo $this->Html->url('/absence_requests/manage/year/true/?year=' . date('Y') . '&profit=' . $profit['id']) ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('To Validate') ?></a></li>
							<?php endif ?>
							</ul>
						</li>
						<li class="treeview <?php if( in_array($currentAction, array('activity_forecasts/request', 'activity_forecasts/response')) )echo 'active' ?>">
							<a href="#">
								<i class="glyphicon glyphicon-calendar"></i> <span><?php __('Activity') ?></span> <i class="glyphicon glyphicon-chevron-down pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li <?php if( $currentAction == 'activity_forecasts/request' )echo 'class="active"' ?>><a href="<?php echo $this->Html->url('/activity_forecasts/request?profit=' . $profit['id']) ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('Request') ?></a></li>
								<li><a href="<?php echo $this->Html->url('/activity_forecasts/to_validate?profit=' . $profit['id']) ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('To Validate') ?></a></li>
								<!-- <li <?php if( $currentAction == 'activity_forecasts/response' )echo 'class="active"' ?>><a href="<?php echo $this->Html->url('/activity_forecasts/response?profit=' . $profit['id']) ?>"><i class="glyphicon glyphicon-unchecked"></i> <?php __('Validation') ?></a></li> -->
							</ul>
						</li>
						<li<?php if( $currentAction == 'activity_forecasts/my_diary' )echo ' class="active"' ?>>
							<a href="<?php echo $this->Html->url('/activity_forecasts/my_diary?profit=' . $profit['id']) ?>">
								<i class="glyphicon glyphicon-list-alt"></i> <span><?php __('My Diary') ?></span>
							</a>
						</li>
						<li><a href="<?php echo $this->Html->url('/logout') ?>" class="text-danger"><i class="glyphicon glyphicon-off"></i> <?php __('Sign out') ?></a></li>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>

			<div class="layout" style="min-height: 921px">
				<?php echo $content_for_layout; ?>
			</div>
			<!-- footer -->
			<footer class="main-footer">
				<a href="http://azuree-app.com/" target="_blank" class="pull-left" style="margin-right: 5px"><img alt="AZURÉE" src="<?php echo $html->url("/img/front/logo_footer.png") ?>" /></a>
				Copyright &copy; 2012-<?php echo date('Y') ?>. Version <?php echo $version['Version']['name'];?>. All rights reserved.
			</footer>
		</div>
	</body>
</html>
