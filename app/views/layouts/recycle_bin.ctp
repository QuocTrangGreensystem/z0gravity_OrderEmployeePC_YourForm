<?php
$langCode = Configure::read('Config.langCode');
$versionModel = ClassRegistry::init('Version');
$employee_info = $this->Session->read("Auth.employee_info");
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" class="page-login">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management | z0 GRAVITY. z0 GRAVITY is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="PM, Project Management, z0 GRAVITY, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<!-- default file -->
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
        <?php
            if( in_array($this->params['controller'], array('project_tasks', 'activity_tasks')) && $this->params['action'] == 'index' ):
                $width = $isMobile ? '1280' : 'device-width';
        ?>
        <meta name="viewport" content="width=<?php echo $width ?>, initial-scale=1, maximum-scale=1, user-scalable=no">
        <?php elseif( !$isMobile ): ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
        <?php else: ?>
        <!-- <meta name="viewport" content="width=1024"> -->
        <?php endif ?>
		
        <title><?php __("Project management | z0 GRAVITY :: PROJECTS") ?></title>
		<link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
        <?php
        echo $html->script('jquery.min'); 
        echo $html->script('modernizr.custom'); 
        echo $html->script('jquery-ui.min2'); 
        echo $html->script('jquery.ui.core'); 
        echo $html->script('jquery.bt'); 
        echo $html->script('jquery.cookie'); 
		echo $html->css('common');
		echo $html->css('simple-line-icons/css/simple-line-icons');
        ?>
		
        <?php
        $here = Router::reverse($this->params);
		?>
    </head>
	<style>
		body{
			color: #111111;
		}
	</style>
    <body>
        <div id="layout">
            <!-- main -->
            <?php echo $content_for_layout; ?>
            <!-- main.end -->
        </div>
    </body>
</html>
