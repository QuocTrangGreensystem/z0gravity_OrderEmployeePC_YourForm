<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management | AZURÉE. AZURÉE is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="PM, Project Management, AZURÉE, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta charset="UTF-8">
        <title><?php __("Project management | AZURÉE :: ADMINISTRATOR") ?></title>
				<!-- administrators file -->
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
        <?php echo $html->css('common'); ?>
        <?php echo $html->script('jquery-1.6.2.min'); ?>
        <?php echo $html->script('modernizr.custom'); ?>
        <?php echo $html->script('jquery-ui.min'); ?>
        <?php echo $html->script('jquery.ui.core'); ?>
    </head>
    <body>
        <div id="wd-container">	
            <div id="wd-header">
                <?php echo $this->element("administrator_top_menu") ?>
                <div class="wd-login">
                    <?php echo $this->element("login_user_info"); ?>
                </div>
            </div>
            <div id="wd-main-contant">
                <div class="wd-tab" style="width: 1100px;">
                    <ul class="wd-item">
                        <li><a href="#wd-fragment-1"><?php __("Employees") ?></a></li>
                        <li><a href="#wd-fragment-2"><?php __("Projects") ?></a></li>
                    </ul>

                    <div class="wd-panel">
                        <?php echo $content_for_layout; ?>
                    </div>
                </div>
                <input type="hidden" id="admin_tab_index" />
            </div>	
        </div>
        <?php //echo $html->script('common');?>
        <script>
   
     
        </script>
    </body>
</html>