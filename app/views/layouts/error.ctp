<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management software | AZURÉE. AZURÉE is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="PM, Project Management, AZURÉE, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
        <title><?php __("Project management | AZURÉE:: Error") ?></title>
		<!-- Error file -->
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
	<?php echo $html->css('common'); ?>
    <?php echo $html->script('jquery-1.6.2.min'); ?>
</head>
<body>
<div id="wd-container-header">
	<div class="wd-layout">
		<h1 class="wd-logo"><a href="<?php echo $html->url('/') ?>"><?php __('Project manager')?></a></h1>
		<div class="wd-login">
			<ul>
			<!--	<li><div class="wd-image wd-photo"><a href="#"><img src="<?php echo $html->url('/img/front/no-photo-small.png'); ?>" alt="photo" /></a></div><div class="wd-name"><a href="<?php echo $html->url("/employees/my_profile")?>" class="wd-user"><?php echo $fullname?></a></div></li> -->
				<li><a href="<?php echo $html->url("/logout")?>" class="wd-sign-out"><?php __("Sign out")?></a></li>
			</ul>
		</div>
	</div>
</div>

<!-- main -->
<?php echo $content_for_layout; ?>                           
<!-- main.end -->	

<div id="wd-container-footer">
	<div class="wd-layout">
		<p class="wd-copy"><?php __('Copyright &copy; 2012-2013. All rights reserved.')?></p>
	</div>
</div>
</body>
</html>