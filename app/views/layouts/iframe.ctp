<?php
$langCode = Configure::read('Config.langCode');
$versionModel = ClassRegistry::init('Version');
$employee_info = $this->Session->read("Auth.employee_info");
$og_title = '';
if( !empty($articles[0]) ){
	$l_article = $articles[0]['ProjectCommunication'];
	$og_title = !empty($companyConfigs['communication_title']) ? $companyConfigs['communication_title'] : (!empty($l_article['communication_title']) ? $l_article['communication_title'] : '');
}elseif( !empty( $article) ){
	$l_article = $article['ProjectCommunication'];
	$og_title = !empty($l_article['communication_title']) ? $l_article['communication_title'] : '';
}
$og_image = !empty($l_article['image']) ? $l_article['image'] : ( !empty($l_article) ? 'image' . $l_article['id'] . '.jpg' : '' );
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" class="page-login pulic-iframe">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="title" content="z0 Gravity : Logiciel de gestion projet pour tous!">
        <meta name="description" content="z0 Gravity est un logiciel de gestion de projets simple et accessible qui permet de gérer les plannings, les budgets et les ressources humaines de vos projets, quelle que soit leur complexité">
        <meta name="keywords" content="PM, Project Management, z0 GRAVITY, project, management, software, collaborative, online, to-do list, planning, schedule, wiki pages, share documents, Communication">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name = "robots" content="all">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">		
		<meta property="og:url" content="<?php echo $this->Html->url();?>">
	<?php if( !empty($l_article)){?>
		<meta property="og:title" content="<?php echo $og_title; ?>" >
		<meta property="og:description" content="" >
		<meta property="og:image" content="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'attachment', $l_article['project_id'], $l_article['id'], $og_image)); ?>">
	<?php } else { ?> 
		<meta property="og:title" content="<?php __("Project management | z0 GRAVITY :: PROJECTS") ?>" >
	<?php } ?> 
        <title><?php __("Project management | z0 GRAVITY :: PROJECTS") ?></title>
		<link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
        <?php
        echo $html->script('jquery.min'); 
        echo $html->script('modernizr.custom'); 
        echo $html->script('jquery-ui.min2'); 
        echo $html->script('jquery.ui.core'); 
        echo $html->script('jquery.bt'); 
        echo $html->script('jquery.cookie'); 
		echo $this->Html->css(array(
			'simple-line-icons/css/simple-line-icons',
			'common.css?ver=2.1',
			'preview/layout',
			'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap',
		));
        $here = Router::reverse($this->params);
		?>
    </head>
	<style>
		#layout{
			min-height: 100%;
			height:auto;
		}
		body{
			color: #000;
		}
		/* width */
		::-webkit-scrollbar {
			width: 4px;
			height: 4px;
		}

		/* Track */
		::-webkit-scrollbar-track {
			box-shadow: inset 0 0 5px #F2F5F7; 
			border-radius: 5px;
			background-color: #fff;
		}

		/* Handle */
		::-webkit-scrollbar-thumb {
			background: #C6CCCF;; 
			border-radius: 5px;
		}

		html{
			scrollbar-face-color: #C6CCCF	; 
			scrollbar-highlight-color: #F2F5F7;
			scrollbar-shadow-color: #ffffff; 
			scrollbar-3dlight-color: #ffffff;
			scrollbar-arrow-color: #C6CCCF; 
			scrollbar-track-color: #F2F5F7;
			scrollbar-darkshadow-color: #F2F5F7;
		}
	</style>
    <body>
        <div id="layout">
            <!-- main -->
            <?php 
			echo $content_for_layout; 
			?>
            <!-- main.end -->
        </div>
    </body>
</html>
