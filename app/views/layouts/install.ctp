<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management software | YourPMStrategy.COM. YourPMStrategy.COM is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="PM, Project Management, Project Visum, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta charset="UTF-8">
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">		
        <link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
        <title><?php __("Project management software | youpmstrategy.com :: PROJECTS") ?></title>
        <?php echo $html->css('common'); ?>
        <?php echo $html->script('jquery.min.js'); ?>
        <?php echo $html->script('modernizr.custom'); ?>
        <?php echo $html->script('jquery-ui.min'); ?>
        <?php echo $html->script('jquery.ui.core'); ?>
        <?php echo $html->script('jquery.bt.js'); ?>
        <?php echo $html->script(array('green/tooltip')); ?>
        <?php echo $html->css('green/tooltip'); ?>
    </head>
    <body>
        <div id="layout">
            <div id="wd-container-header-main">
                <div id="wd-container-header">
                    <div class="wd-layout">
                        <h1 class="wd-logo"><a href="<?php echo $html->url('#') ?>"><?php __('Project manager') ?></a></h1>
                        <div class="wd-login">
                            <h1 class="wd-logo" style="font-size: 28px; line-height: normal !important;"><?php __('Install Projects Management Strategic') ?></h1>
                        </div>
                    </div>
                </div>

            </div>

            <!-- main -->
            <?php echo $content_for_layout; ?>                           
            <!-- main.end -->
        </div>
        <div id="wd-container-footer">
            <div class="wd-layout">
                <p>
                    <a href="http://azuree-app.com/" target="_blank" class="wd-global-logo">
                        <img alt="AZURÉE" src="<?php echo $html->url("/img/front/logo_footer.png") ?>" />					
						</a>Copyright &copy; 2012-<?php echo date('Y') ?>. Version <?php echo !empty($version['Version']['name']) ? $version['Version']['name'] : '';?>. All rights reserved.					
                </p>			
            </div>
        </div>

        <?php echo $html->script('common'); ?>

        <!--[if IE]>
        <script>
        $(function(){
            $("input[placeholder]").each(function(){
        
                if ($(this).val() == $(this).attr("placeholder")){
                     $(this).css("color","#ababab");
                     $(this).val('');}
                else $(this).css("color", "#000");
                 $(this).focus(function(){
                    if ($(this).val() == $(this).attr("placeholder")) {
                        $(this).val('');
                    }
                 });
                 $(this).blur(function(){
                    if ($(this).val() == $(this).attr("placeholder")) {
                        $(this).css("color","#ababab");
                        $(this).val('');
                    }else{
                        if ($(this).val().length == 0) {
                            $(this).val($(this).attr("placeholder"));
                            $(this).css("color","#ababab");
                        }else{
                            $(this).css("color", "#000");
                        }
                    }
                 });
            }); 
        });
        </script>
        <![endif]-->
    </body>
</html>