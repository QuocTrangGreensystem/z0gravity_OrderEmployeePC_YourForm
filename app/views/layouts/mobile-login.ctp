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
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
        <link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
		<title><?php __("z0 Gravity :: Login") ?></title>
		<?php echo $html->css(array('main', 'common_z0g')); ?>
		<!-- <?php echo $html->css('mobile/bootstrap.min'); ?>
		<?php echo $html->css('mobile/AdminLTE'); ?>
		<?php echo $html->css('mobile/skins/skin-blue'); ?>
		<?php echo $html->css('mobile/common'); ?>
		<?php echo $html->css('mobile/login'); ?> -->
		<?php echo $html->script('jquery-1.11.3.min'); ?>
	</head>

	<?php
	$langCode = Configure::read('Config.langCode');
	?>

	<body class="<?php echo $langCode; ?> skin-blue">
		<!-- logo -->
		<!-- <header class="container-fluid" id="logo">
			<div class="row">
				<h1>
					<a href="<?php echo $this->Html->url('/') ?>">Z0</a>
				</h1>
			</div>
		</header> -->
		<div class="container-fluid" id="container">
			<?php
				$versionModel = ClassRegistry::init('Version');
				$version = $versionModel->find('first',array('conditions'=>array('Version.is_current_version'=>1),'fields'=>'name'));
				if( empty($version) ){
					$version = $versionModel->find('first',array('fields'=>'name', 'order' => array('updated' => 'DESC'), 'limit' => 1));
				}
				$employee_info = $this->Session->read('Auth.employee_info');
				$is_sas = $employee_info['Employee']['is_sas'];
				$role = "";
				if ($is_sas != 1) {
					$role = $employee_info['Role']['name'];
				}
			?>
			<!-- main -->
			<?php echo $content_for_layout; ?>
		</div>
		<!-- footer -->
		<!-- <footer id="footer">
			<a href="http://azuree-app.com/" target="_blank" class="wd-global-logo"><img alt="AZURÉE" src="<?php echo $html->url("/img/front/logo_footer.png") ?>" /></a>
			Copyright &copy; 2012-<?php echo date('Y') ?>. Version <?php echo $version['Version']['name'];?>. All rights reserved.
		</footer> -->

		<?php echo $html->script('modernizr.custom'); ?>
		<?php echo $html->script('common'); ?>
		<?php echo $html->script('bootstrap.min'); ?>
		<!--[if IE]><?php echo $html->script('excanvas.compiled.js'); ?><![endif]-->
		<?php echo $html->script('jquery.bt.js'); ?>
	</body>
</html>
