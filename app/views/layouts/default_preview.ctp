<?php
$langCode = Configure::read('Config.langCode');
$versionModel = ClassRegistry::init('Version');
$ProfitCenter = ClassRegistry::init('ProfitCenter');
$ProfitCenterManagerBackup = ClassRegistry::init('ProfitCenterManagerBackup');
$display_my_assistant = Configure::read('Config.displayAssistant');
$avatar_assistant = Configure::read('Config.avatar_assistant');
?><!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" class="page-login">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management | z0 GRAVITY. z0 GRAVITY is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="PM, Project Management, z0 GRAVITY, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
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
        <?php echo $html->css('common'); ?>
        <?php echo $html->script('jquery.min'); ?>
        <?php echo $html->script('modernizr.custom'); ?>
        <?php echo $html->script('jquery-ui.min2'); ?>
        <?php echo $html->script('jquery.ui.core'); ?>
        <?php echo $html->script('jquery.bt'); ?>
        <?php echo $html->script('jquery.cookie'); ?>
        <?php echo $html->script('z0.history'); ?>
        <?php echo $html->script('green/tooltip'); ?>
        <?php echo $html->css('green/tooltip'); ?>
        <?php echo $html->css('simple-line-icons/css/simple-line-icons');?>
       
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php echo $html->css('preview/header'); ?>
        <script>
			// Update by viet Nguyen ticket #693
			// filter_render dung cho tat ca man hinh
			// Hien thi data filter sau khi init table slickgrid
			var filter_render = <?php echo json_encode($filter_render); ?>;
            var Azuree = {
                isMobile: <?php echo $isMobile ? 'true' : 'false' ?>,
                isTouch: <?php echo $isTouch ? 'true' : 'false' ?>,
                mobileEnabled: <?php echo $mobileEnabled ? 'true' : 'false' ?>,
                language: <?php echo json_encode($langCode) ?>,
                editTask: <?php echo json_encode(__('Edit', true)) ?>,
                root: <?php echo json_encode($this->Html->url('/')) ?>,
                employeeAvatar_link : "<?php echo $this->Html->url('/img/avatar/%ID%.png'); ?>",
                dropzone_option : {
                    dictRemoveFile: '<?php __('Remove file');?>',
                    imageSrc: "/img/new-icon/draganddrop.png",
                    dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders');?>",
                }
            };
            $.datepicker.regional.en = {
                closeText: <?php echo json_encode(__('OK', true)) ?>,
                prevText: <?php echo json_encode(__('Previous', true)) ?>,
                nextText: <?php echo json_encode(__('Next', true)) ?>,
                currentText: <?php echo json_encode(__('Today', true)) ?>,
                dayNames: <?php echo json_encode(array(__('Sunday', true), __('Monday', true), __('Tuesday', true), __('Wednesday', true), __('Thursday', true), __('Friday', true), __('Saturday', true))) ?>,
                dayNamesShort: <?php echo json_encode(array(__('Sun', true), __('Mon', true), __('Tues', true), __('Wed', true), __('Thu', true), __('Fri', true), __('Sat', true))) ?>,
                dayNamesMin: <?php echo json_encode(array(__('Su', true), __('Mo', true), __('Tu', true), __('We', true), __('Th', true), __('Fr', true), __('Sa', true))) ?>,
                monthNames: <?php echo json_encode(array(__('January', true), __('February', true), __('March', true), __('April', true), __('May', true), __('June', true), __('July', true),  __('August', true),  __('September', true),  __('October', true),  __('November', true),  __('December', true))) ?>,
                monthNamesShort: <?php echo json_encode(array(__('Jan', true), __('Feb', true), __('Mar', true), __('Apr', true), __('May', true), __('Jun', true), __('Jul', true),  __('Aug', true),  __('Sep', true),  __('Oct', true),  __('Nov', true),  __('Dec', true))) ?>,
                //dateFormat: "dd/mm/yyyy",
                firstDay: 1
            };
            $.datepicker.setDefaults( $.datepicker.regional.en );
			var employeeAvatar_link = "<?php echo $this->Html->url('/img/avatar/%ID%.png'); ?>";
        </script>
        <?php
        $here = Router::reverse($this->params); $mobile = strpos($here, '?') !== false ? $here . '&mobile=1' : $here . '?mobile=1';
        $mobile = str_replace('to_validated', 'manage', $mobile);//fix absence-to valaidate
        ?>
        <?php if( $isMobile ): ?>
        <?php echo $html->css('azuree-mobile'); ?>
        <?php endif ?>
        <style>
            .third-nav {
                position: absolute;
                top: 3.5em;
                right: 5.5em;
                display: inline-block;
            }
            .third-nav:before {
                content: url("../img_z0g/light.png");
                pointer-events: none;
                position: absolute;
                transform: translate(-38%, -30%);
                z-index: 2;
            }
            .third-nav__item:nth-child(1) {
                top: -1.475em;
                left: -8.375em;
            }
            .third-nav__item:nth-child(2) {
                top: -1.475em;
                left: -5.25em;
            }
            .third-nav__item:nth-child(3) {
                top: -1.475em;
                left: 0.95em;
            }
            .third-nav__item:nth-child(4) {
                top: -1.475em;
                left: -2.175em;
            }
            .third-nav__item:nth-child(5) {
                top: -1.475em;
                left: -7.375em;
            }
            .name-employee{
                float: right;
                margin-right: 45px;
                color:#fff;
                margin-top: -25px;
            }
            .assistant-cancel{
                background: url(/img_z0g/icon_cancel_z0g.png) no-repeat !important;
                width: 22px;
                height: 22px;
                padding: 0;
                text-indent: -9999px;
                -webkit-border-radius: 3px;
                color: #fff;
                display: block;
                font-weight: bolder;
                line-height: 25px;
                text-shadow: #333 1px 1px;
                white-space: nowrap;
                float: right;
                margin-top: -7px;
                margin-right: 5px;
            }
            .assistant-tittle label{
                display: inline-block;
                font-size: 120%;
                color: #a1a1a1;
                border-bottom: 1px solid #0cb0e0;
                font-family: inherit;
                width: 79%;
                margin-left: -3%;
            }
            .assistant-tittle span{
                font-size: 13px;
                color: #ea0000;
            }
            .assistant-content{
                color: #000;
                padding-bottom: 5px;
            }
            .assistant{
                font-family: releway-regular;
                font-size: 13px;
            }
            .assistant label{
                display: inline-block;
                color: #0cb0e0;
                margin: -3%;
                width: 82%;
            }
            .assistant span{
                color: #a1a1a1;
                display: inline-block;
                width: 10%;
            }
            .assistant label a{
                cursor: pointer;
                color: #a1a1a1;
                text-decoration: none
            }
            .assistant input{
            }
            .assistant-tittle img{
                float: right;
                margin-right: 10px;
                border-radius: 5px;
            }
            .background-assistant{
                background-color: #9fceec;
                padding: 5px;
                clear: both;
                min-height: 40px;
            }
            .me-assistant{
                border: 1px solid #ddd;
                margin: 3%;
                background: #fff;
                padding-left: 3%;
            }
            .assistant-content img{
                width: 100%;
                height: 100%;
                padding-right: 5px;
                margin-top: -3%;
            }
            .wd-content-title img{
                float: left;
                height: 10%;
                width: 10%;
                margin-left: 1%;
            }
            .wd-content-title{
                clear: both;
                display: none;
            }
            .wd-content-title span{
                color: #5a9cc5;
                font-size: 160%;
                font-weight: bold;
                margin-left: 3%;
                position: absolute;
                margin-top: 2.5%;
            }
            .wd-content-left{
                float: left;
                width: 15%;
            }
            .wd-content-right{
                display: block;
                margin-left: 20%;
            }
            .custom-dialog{
                min-width: 300px;
            }
            #dialog_my_assistant{
                padding: 0 !important;
            }
            .custom-dialog .ui-dialog-titlebar{
                height: 45px;
                z-index: 999999;
            }
            <?php if ($isMobile && !$isTablet): ?>
            /*    .third-nav__item:nth-child(1) {
                    top: -1.775em;
                    left: -12.975em;
                }
                .third-nav__item:nth-child(2) {
                    top: -1.775em;
                    left: -15.95em;
                }
                .third-nav__item:nth-child(3) {
                    top: 0.55em;
                    left: -16.575em;
                }
                .third-nav__item:nth-child(4) {
                    top: 0.55em;
                    left: -13.55em;
                }
                .third-nav__item:nth-child(5) {
                    top: -1.85em;
                    left: -18.375em;
                }
                .third-nav {
                    top: 3.5em;
                    right: -8.5em;
                }
                .name-employee{
                    margin-top: -10px;
                }
                .custom-dialog{
                    width: 80%;
                }*/
            <?php endif; ?>
            .third-nav__item {
                display: block;
                text-align: center;
                text-decoration: none;
            }
            .third-nav__item {
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.4);
                border-radius: 12%;
                width: 3em;
                height: 3em;
                padding: 0.5em;
                position: absolute;
                cursor: pointer;
                transition: all 1s;
                box-sizing: border-box;
            }
            .third-nav__item:after {
                pointer-events: none;
                content: url("../img_z0g/light-small.png");
                opacity: 0;
                position: absolute;
                left: 50%;
                top: 50%;
                -webkit-transition: opacity 0.2s;
                -moz-transition: opacity 0.2s;
                transition: opacity 0.2s;
                -webkit-transform: translateX(-50%) translateY(-50%);
                -moz-transform: translateX(-50%) translateY(-50%);
                -ms-transform: translateX(-50%) translateY(-50%);
                -o-transform: translateX(-50%) translateY(-50%);
                transform: translateX(-50%) translateY(-50%);
            }
            .third-nav__item-text {
                font-weight: bold;
                font-size: 1.0625em;
            }
            .third-nav__item > * {
                width: 100%;
                height: 100%;
                vertical-align: middle;
            }
            .third-nav__item:hover {
                background: linear-gradient(to right, rgba(13, 60, 110, 0.8), rgba(8, 102, 140, 0.8));
                border: 0;
                z-index: 1;
                text-decoration: none;
            }
            #wd-container-header-main #wd-container-header h1.wd-logo a{
                text-indent: 68px;
                color: #fff;
            }
            .third-nav a i{
                font-size: 18px; line-height: 22px; color: #fff;
            }
            .third-nav a:hover{
                color: #fff;
            }
        </style>
        <?php echo $this->element("style/custom_css");  ?>
    </head>
    <body>

        <div id="wd-container-header-main">
            <div id="wd-container-header">
                <div class="wd-layout">
                    <?php
                    $version = $versionModel->find('first',array('conditions'=>array('Version.is_current_version'=>1),'fields'=>'name'));
                    if( empty($version) ){
                        $version = $versionModel->find('first',array('fields'=>'name', 'order' => array('updated' => 'DESC'), 'limit' => 1));
                    }
                    $employee_info = $this->Session->read('Auth.employee_info');
                    $companyNameLogin = !empty($employee_info['Company']['dir']) ? strtolower($employee_info['Company']['dir']) : '';
                    $employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
                    $avatarLogins = !empty($employee_info['Employee']['avatar_resize']) ? $employee_info['Employee']['avatar_resize'] : '';
                    $urlAvatar = $this->UserFile->avatar($employeeIdLogin);
                    $_domain = $_SERVER['SERVER_NAME'];
                    $employeeName = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';

                    $manager = $ProfitCenter->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'manager_id' => $employeeIdLogin
                        ),
                        'fields' => array('id', 'id')
                    ));
                    $manager_backup = $ProfitCenterManagerBackup->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'employee_id' => $employeeIdLogin
                        ),
                        'fields' => array('profit_center_id', 'profit_center_id')
                    ));
                    $_list_profit_center_managers = array_unique(array_merge($manager, $manager_backup));
                    $pId = !empty($_list_profit_center_managers) ? array_shift($_list_profit_center_managers) : '';

                    $companyName = !empty($employee_info['Company']['company_name']) ? $employee_info['Company']['company_name'] : '';
                    echo $this->element("header-preview"); ?>
                    
                    
                </div>
            </div>

        </div>
        <div id="layout">

            <!-- main -->
            <?php echo $content_for_layout; ?>
            <!-- main.end -->
        </div>
        <div id="wd-container-footer">
            <div class="wd-layout">
                <p>
                    <a href="http://azuree-app.com/" target="_blank" class="wd-global-logo">
                        <img alt="AZURÉE" src="<?php echo $html->url("/img/front/logo_footer.png") ?>" />
                        </a>Copyright &copy; 2012-<?php echo date('Y') ?>. Version <span class="update-version-default"><?php echo $version['Version']['name'];?></span>. All rights reserved.
                        <?php if( $isMobile ): ?><a href="<?php echo $mobile ?>"><?php __('Mobile version') ?></a> | <a href="<?php echo $this->Html->url('/') ?>"><?php __('Home') ?></a><?php endif ?>
                </p>
            </div>
        </div>

        <?php
        if($this->params['url']['url'] != 'team_workloads/plus') :
        ?>
        <?php echo $html->script('common'); ?>
        <?php endif; ?>
        <?php echo $html->script('newDesign/sound'); ?>
       
        
    </body>
</html>
