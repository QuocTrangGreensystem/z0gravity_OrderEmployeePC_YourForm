<?php
$langCode = Configure::read('Config.langCode');
$versionModel = ClassRegistry::init('Version');
$ProfitCenter = ClassRegistry::init('ProfitCenter');
$ProfitCenterManagerBackup = ClassRegistry::init('ProfitCenterManagerBackup');
$display_my_assistant = Configure::read('Config.displayAssistant');
$avatar_assistant = Configure::read('Config.avatar_assistant');
// $enable_newdesign = Configure::read('Config.enable_newdesign');
$employee_info = $this->Session->read("Auth.employee_info");
// debug(  $enable_newdesign); exit;
?>
<!DOCTYPE html>
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
		<?php echo $html->css('common.css?ver=2.1');
        echo $html->script('jquery.min'); 
        echo $html->script('modernizr.custom'); 
        echo $html->script('jquery-ui.min2'); 
        echo $html->script('jquery.ui.core'); 
        echo $html->script('jquery.bt'); 
        echo $html->script('jquery.cookie'); 
        echo $html->script('z0.history'); 
        echo $html->script('z0.main.js?ver=1.6');
        echo $html->script('green/tooltip'); 
		echo $html->script('bignumber/bignumber.min');
        echo $html->css('green/tooltip');
		echo $html->css('fonts');
		echo $html->css('simple-line-icons/css/simple-line-icons');
		
        if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1){
            echo $html->css('preview/header');
            echo $html->css('preview/tab-admin');
            echo $html->css('preview/slickgrid');
            echo $html->css('preview/layout.css?ver=1.3');
            echo $html->css('preview/datepicker-new');
            echo $html->css('preview/alert');
        }elseif($enable_newdesign){
			// echo $html->css('preview/header');
            echo $html->css('preview/tab-admin');
            echo $html->css('preview/slickgrid');
            echo $html->css('preview/layout.css?ver=1.3');
            echo $html->css('preview/datepicker-new');
            echo $html->css('preview/alert');
		}
        ?>
        <script>
            var Azuree = {
                isMobile: <?php echo $isMobile ? 'true' : 'false' ?>,
                isTouch: <?php echo $isTouch ? 'true' : 'false' ?>,
                mobileEnabled: <?php echo $mobileEnabled ? 'true' : 'false' ?>,
                language: <?php echo json_encode($langCode) ?>,
                editTask: <?php echo json_encode(__('Edit', true)) ?>,
                root: <?php echo json_encode($this->Html->url('/')) ?>,
                employeeAvatar_link : "<?php echo $this->Html->url('/img/avatar/%ID%.png'); ?>",
                dropzone_option : {
                    dictRemoveFile: "<?php __('Remove file');?>",
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
			var listEmployeeName = <?php echo json_encode( !empty($listEmployeeName) ? $listEmployeeName : array()); ?>;
			var employeeAvatar_link = "<?php echo $this->Html->url('/img/avatar/%ID%.png'); ?>";
            // filter_render dung cho tat ca man hinh
            var filter_render = <?php echo json_encode($filter_render); ?>;
        </script>
        <?php
        $params = array(
			'controller' => $this->params['controller'],
			'action' => $this->params['action'],
			'named' => $this->params['named'],
			'pass' => $this->params['pass'],
			'plugin' => $this->params['plugin'],
			'url' => $this->params['url'],
		);
        $here = Router::reverse($params); $mobile = strpos($here, '?') !== false ? $here . '&mobile=1' : $here . '?mobile=1';
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
                background: url(/img/new-icon/close.png) no-repeat;
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
                margin-top: -10px;
                margin-right: 20px;
                width: 15px;
                height: 15px;
            }
            .assistant-cancel:hover{
                background: url(/img/new-icon/close-blue.png) no-repeat;    
            }
            .assistant-tittle label{
                display: inline-block;
                color: #a1a1a1;
                font-family: inherit;
                margin-left: 0;
                font-size: 14px;
                color: #424242;
                font-weight: bold;
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
                font-size: 13px;
                font-family: "Open Sans"; 
                margin-bottom: 12px;
            }
            .assistant label{
                display: inline-block;
                vertical-align: top;
                padding-left: 5px;
            }
            .assistant span{
                text-align: right;
                color: #424242;
                font-size: 13px;
                float: right;
				font-weight: bold;
            }
            .assistant label a{
                cursor: pointer;
                text-decoration: none;
                margin-left: 0;
                font-size: 14px;
                color: #424242;
                font-family: "Open Sans";
            }
            .assistant input{
                margin-left: 0;
            }
			.assistant:hover label a,
			.assistant:hover span{
			   color: #5487FF; 
			}
            .assistant input {
                position: absolute;
                opacity: 0;
                z-index: 3;
                cursor: pointer;
            }
            .assistant input + p {
                position: relative;
                cursor: pointer;
                padding: 0;
                display: inline-block;
                margin: 0;
            }
            .assistant input + p:before {
                content: '';
                margin-right: 10px;
                display: inline-block;
                vertical-align: text-top;
                width: 14px;
                height: 14px;
                background: white;
                z-index: 2;
                position: relative;
                top: 3px;
                left: 3px;
                border-radius: 2px;
                cursor: pointer;
            }
            .assistant input:checked + p:before {
                background: #5584FF;
              }
            .assistant input:disabled + p {
                color: #b8b8b8;
                cursor: auto;
              }
            .assistant input:disabled + p:before {
                box-shadow: none;
                background: #ddd;
              }
            .assistant input + p:after {
                content: '';
                position: absolute;
                width: 18px;
                height: 18px;
                background: white;
                border: 1px solid #E1E6E8;
                box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
                left: 0;
                z-index: 1;
                border-radius: 3px;
                cursor: pointer;
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
                background: #fff;
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
           /* .wd-content-left{
                float: left;
                width: 15%;
            }
            .wd-content-right{
                display: block;
                margin-left: 20%;
            }*/
            #dialog_my_assistant{
                padding: 0;
                padding-left: 20px;
                padding-right: 20px;
				overflow: auto;
            }
            .custom-dialog .ui-dialog-titlebar{
                height: 30px;
                z-index: 999999;
            }
            .new-version-in-old .wd-layout > .wd-main-content > .wd-tab > .wd-panel{
                margin-left: auto;
                margin-right: auto;
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
            .assistant-dialog{
                z-index: 2;
                text-align: left;
                box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
                border-radius: 3px;
                background-color: #fff;
                z-index: 9;
                border: none;
            }
            .assistant-tittle{
                margin-bottom: 10px;
            }
            .assistant-tittle span{
                float: right;
            }
            body .ui-dialog.assistant-dialog .ui-dialog-titlebar{
                background-color: transparent;
            }
        </style>
        <?php echo $this->element("style/custom_css");  ?>
    </head>
    <body>
        <div id="wd-container-header-main">
            <div id="wd-container-header">
                <div class="wd-header-layout">
                    <?php
                    $version = $versionModel->find('first',array('conditions'=>array('Version.is_current_version'=>1),'fields'=>'name'));
					$employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
                    if( empty($version) ){
                        $version = $versionModel->find('first',array('fields'=>'name', 'order' => array('updated' => 'DESC'), 'limit' => 1));
                    }
                    $_domain = $_SERVER['SERVER_NAME'];
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
                    if( (!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1)){
                        echo $this->element("header-preview");
                    } else { 
						echo $this->element("main_menu");
                    } ?>  
                </div>
            </div>

        </div>
        <div id="layout" class=" <?php if(empty($employee_info['Color']['is_new_design']) && $enable_newdesign) echo 'new-version-in-old';?>">

            <!-- main -->
            <?php echo $content_for_layout; ?>
            <!-- main.end -->
        </div>
        <?php 
        if((!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1)){ } else { ?>
        <div id="dialog_my_assistant" class="buttons loading-mark" style="display: none;">
            <!-- <a href="javascript:void(0)" style="top: 20px;position: absolute;float: right;right: 0;" class="assistant-cancel"><?php __("Cancel") ?></a> -->
            <div class="me-assistant">
                <div class="wd-content-title">
                    <span style="" style=""><?php echo __('ME', true) ?></span>
                </div>
                <div id="assistant_my_task" class="assistant-content" style="display: none; clear: both; margin-top: 5px">
                    <div class="wd-content-right">
                        <div class="assistant_avatar assistant-tittle"><label><?php echo __('MY TASKS', true) ?></label></div>
                        <div class="in_progress assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/1') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                        <div class="late assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/2') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                        <div class="overload assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/3'); ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                    </div>
                </div>
                <div id="assistant_my_timesheet" class="assistant-content" style="display: none; clear: both;">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('MY TIMESHEETS', true) ?></label></div>
                        <div class="late assistant"><input type="checkbox" checked="checked" /><p></p><label></label><span></span></div>
                    </div>
                </div>
                <div id="assistant_my_project" class="assistant-content" style="display: none; clear: both;">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('MY PROJECTS', true) ?></label><span></span></div>
                        <div class="in_progress assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/4') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                        <div class="late assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/5') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                        <div class="overload assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/6') ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                    </div>
                </div>
            </div>
            <div class="my-team-assistant" style="display: none">
                <div id="assistant_my_team" class="assistant-content" style="display: none; clear: both; margin-top: 5px">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('TASKS OF MY TEAM', true) ?></label><span></span></div>
                        <div class="in_progress assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/7') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                        <div class="late assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/8') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                        <div class="overload assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/9') ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                    </div>
                </div>
				<?php $enableRMS = $this->Session->read('enableRMS');
				if($enableRMS == true) : ?>
					<div id="assistant_to_validate" class="assistant-content" style="display: none; clear: both;">
						<div class="wd-content-right">
							<div class="assistant-tittle"><label><?php echo __('TO VALIDATE', true) ?></label></div>
							<div class="timesheet assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/activity_forecasts/to_validate?profit=' . $pId) ?>" ><?php echo __('Timesheet', true); ?></a></label><span></span></div>
							<div class="absence assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/absence_requests/manage/year/true/?profit=' . $pId) ?>" ><?php echo __('Absence', true); ?></a></label><span></span></div>
						</div>
					</div>
					<div id="assistant_late" class="assistant-content" style="display: none; clear: both;">
						<div class="wd-content-right" style="padding-bottom: 2%">
							<div class="assistant-tittle"><label><?php echo __('LATE', true) ?></label></div>
							<div class="timesheet assistant"><input type="checkbox" checked="checked" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/activity_forecasts/not_sent_yet?profit=' . $pId) ?>" ><?php echo __('Timesheet', true); ?></a></label><span></span></div>
						</div>
					</div>
				<?php endif; ?>
            </div>
        </div>
        <?php }
        if($this->params['url']['url'] != 'team_workloads/plus') :
        ?>
        <?php echo $html->script('common.js?ver=1.4'); ?>
        <?php endif; ?>
        <?php
        if((!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1)){ } else {
        $curYear = date('Y');
        ?>
        <script type="text/javascript">
			// Update by viet Nguyen ticket #693
			// filter_render dung cho tat ca man hinh
			// Hien thi data filter sau khi init table slickgrid
			var my_assistants = JSON.parse(<?php echo json_encode( ( !empty($my_assistants) && ($my_assistants != 'null')) ? $my_assistants : "[]");?>);
			var	myAssis = new $.z0.data(my_assistants);
            var pId = <?php echo json_encode($pId) ?>;
            var curYear = <?php echo json_encode($curYear) ?>;
            // var savePositionAssistant;
			function toggleAssistant(){
				if( $( "#dialog_my_assistant" ).dialog( "isOpen" )){
					$( "#dialog_my_assistant" ).dialog( "close" );
					return;
				}
				$('.z0g-header').find('.burger, .z0g-header-inner').removeClass('active');
				$.ajax({
					url : '<?php echo $html->url(array('controller' => 'my_assistants', 'action' => 'getMyAssistant')); ?>',
					type: 'POST',
					dataType: 'json',
					beforeSend: function(){
						$('.assistant-dialog').find('.loading-mark').addClass('loading');
						$("#dialog_my_assistant").dialog('option',{title:''}).dialog('open');
					},
					success: function(data) {
						// my task
						// savePositionAssistant = data['position'];
						// console.log(data);
						if(data['displayOverload'] == 0){
							$('.overload').addClass('hidden');
						}
						if(data['my_task']){
							$('#assistant_my_task').show();
							var selector = $('#assistant_my_task');
							$.each(data['my_task'], function(index, value){
								selector.find('.' + index).find('span').html(value['number']);
								if(value['follow'] == 1){
									selector.find('.' + index).find('input').prop("checked", true);
								}
							});
						}
						// my timesheet
						if(data['my_timesheet']){
							$('#assistant_my_timesheet').show();
							var selector = $('#assistant_my_timesheet');
							$.each(data['my_timesheet'], function(index, value){
								if(index == 'week'){
									if(value != 0){
										var _url = "<?php echo $this->Html->url('/') ?>activity_forecasts/request/?week=" + value + "&year="+curYear+"&profit="+pId;
									} else {
										var _url = "<?php echo $this->Html->url('/') ?>activity_forecasts/request";
									}
									var _html = '<a target="_blank" href="'+_url+'" ><?php echo __("Late", true); ?></a>';
									selector.find('.late').find('label').html(_html);
								} else {
									selector.find('.' + index).find('span').html(value['number']);
									if(value['follow'] == 1){
										selector.find('.' + index).find('input').prop("checked", true);
									}
								}
							});
						}
						// my project
						if(data['my_project']){
							$('#assistant_my_project').show();
							var selector = $('#assistant_my_project');
							$.each(data['my_project'], function(index, value){
								if(index == 'count'){
									selector.find('.assistant-tittle').find('span').html(value);
								} else {
									selector.find('.' + index).find('span').html(value['number']);
									if(value['follow'] == 1){
										selector.find('.' + index).find('input').prop("checked", true);
									}
								}
							});
						}
						// my team
						if(data['my_team']){
							$('.my-team-assistant').show();
							$('#assistant_my_team').show();
							var selector = $('#assistant_my_team');
							$.each(data['my_team'], function(index, value){
								if(index == 'count'){
									selector.find('.assistant-tittle').find('span').html(value);
								} else {
									selector.find('.' + index).find('span').html(value['number']);
									if(value['follow'] == 1){
										selector.find('.' + index).find('input').prop("checked", true);
									}
								}
							});
						}
						// to validated
						if(data['to_validated']){
							$('.my-team-assistant').show();
							$('#assistant_to_validate').show();
							var selector = $('#assistant_to_validate');
							$.each(data['to_validated'], function(index, value){
								selector.find('.' + index).find('span').html(value['number']);
								if(value['follow'] == 1){
									selector.find('.' + index).find('input').prop("checked", true);
								}
							});
						}
						// late
						if(data['late']){
							$('#assistant_late').show();
							$('.my-team-assistant').show();
							var selector = $('#assistant_late');
							$.each(data['late'], function(index, value){
								selector.find('.' + index).find('span').html(value['number']);
								if(value['follow'] == 1){
									selector.find('.' + index).find('input').prop("checked", true);
								}
							});
						}
						setSizePopupAssistants();
						myAssis.set('select', 1);
						$.z0.History.save('my_assistants', myAssis);
						// $("#dialog_my_assistant").dialog('option',{title:''}).dialog('open');
					},
					complete: function(){						
						$('.assistant-dialog').find('.loading-mark').removeClass('loading');
					},
				});
			}
			$(window).ready(function(){
				var checked = myAssis.get('select', '');
				$('#nav-assitant').click(function(){
					toggleAssistant();
				});
				$(".assistant-cancel").click(function(){
					myAssis.set('select', 0);
					$.z0.History.save('my_assistants', myAssis);
					$("#dialog_my_assistant").dialog('close');
				});
				if(checked == 1){
					setTimeout( function(){
						$("#nav-assitant").trigger("click");
					}, 2500);
				}
			});
            if($('.ui-dialog-titlebar').attr('id') == 'ui-dialog-title-dialog_my_assistant'){
                $(this).css("display", n)
            }
			function setSizePopupAssistants(){
				var wrapper = $('#dialog_my_assistant');
				if(wrapper.length > 0){
					var height_content = 81 + $('.me-assistant').outerHeight() + $('.my-team-assistant').outerHeight();
					var height_popup = $('.me-assistant').outerHeight() + $('.my-team-assistant').outerHeight() + 10;
					if( $(window).height() < height_content){
						height_popup = $(window).height() - wrapper.offset().top;
					}
					wrapper.css({
						height: height_popup
					});
				}
			};
			$(window).resize(function () {
				setSizePopupAssistants();
			});
            
			function assitant_dialog_position(){
				var _position	= {
					my	: 'right top',
					at	: 'right bottom',
					of	: '#nav-assitant',
					collision : 'fit'
				};
				if( $('.z0g-header').find('.z0g-header-inner').hasClass('active') || $('.z0g-header').find('.z0g-header-inner').is(':hidden') ){
					_position	= {
						my	: 'right top',
						at	: 'left bottom',
						of	: '#header_padding_assitant',
						collision : 'fit'
					};
				}
				return _position;
			}
            var assitant_dialog = $('#dialog_my_assistant').dialog({
                autoOpen    : false,
                autoHeight  : true,
                modal       : false,
                width       : '240',
                draggable   : false,
				// resizable	: false,
                dialogClass : "custom-dialog assistant-dialog",
				position	: assitant_dialog_position(),
                show : {
					effect: 'slideToggle',
					duration: 200,
				},
				create: function(event, ui){
					$('.assistant-dialog').find('.loading-mark').addClass('loading');
				},
                open : function(event, ui){
                },
				beforeClose: function( event, ui ) {
					myAssis.set('select', 0);
					$.z0.History.save('my_assistants', myAssis);
				}
            });
			$(window).on('resize', function(){
                if( $('#dialog_my_assistant').dialog( "isOpen")){
    				$('#dialog_my_assistant').dialog({
    					position	: assitant_dialog_position(),
    				});
                }
            // $('.z0g-header').find('.burger, .z0g-header-inner').removeClass('active');
			});
            function editFollow(_id, _class, key){
                var checked = $('#' + _id).find('.' + _class).find('input').is(":checked");
                $.ajax({
                    url : '<?php echo $html->url(array('controller' => 'my_assistants', 'action' => 'editFollow')); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        key: key,
                        checked: checked
                    }
                });
            }
        </script>
        <?php } ?>
    </body>
</html>
