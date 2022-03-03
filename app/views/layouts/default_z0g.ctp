<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="page-login">
    <style>
        .name-employee span{
            float: right;
            margin-top: 26px;
            font-size: 13px;
        }
        .main-content {
            position: absolute;
            margin-top: 10%;
            width: 100%;
        }
        .third-nav__item:nth-child(5) {
            top: -2.55em;
            left: -5.375em;
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
                width: 16px;
                height: 16px;
                text-align: center;
                color: #fff;
                border-radius: 50%;
                font-size: 12px;
                background-color: #999;
                float: right;
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
                width: 20px;
                height: 20px;
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
            display: none;
            clear: both;
        }
        .wd-content-title span{
            color: #5a9cc5;
            font-size: 160%;
            font-weight: bold;
            margin-left: 3%;
            position: absolute;
            margin-top: 2.5%;
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
        .assistant-dialog *{
            box-sizing: border-box;
        }
        .assistant-tittle{
            margin-bottom: 10px;
        }
        .assistant-tittle span{
            float: right;
            margin-right: 5px;
        }
        body .ui-dialog.assistant-dialog .ui-dialog-titlebar{
            background-color: transparent;
        }
        #dialog_my_assistant{
            padding: 0;
            padding-left: 20px;
            padding-right: 20px;
        }
        .custom-dialog .ui-dialog-titlebar{
            height: 20px;
            z-index: 999999;
        }
        <?php if( $isMobile && !$isTablet ): ?>
        .name-employee span{
            margin-top: 50px;
        }
        .custom-dialog{
            width: 80%;
        }
        <?php endif;?>
    </style>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="US, FR" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
        <?php
            if( in_array($this->params['controller'], array('project_tasks', 'activity_tasks')) && $this->params['action'] == 'index' ):
                $width = $isMobile ? '1280' : 'device-width';
        ?>
        <meta name="viewport" content="width=<?php echo $width ?>, initial-scale=1, maximum-scale=1, user-scalable=no">
        <?php else: ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php endif ?>
        <?php
        $here = Router::reverse($this->params); $mobile = strpos($here, '?') !== false ? $here . '&mobile=1' : $here . '?mobile=1';
        $mobile = str_replace('to_validated', 'manage', $mobile);//fix absence-to valaidate
        ?>
        <?php if( $isMobile ): ?>
        <?php echo $html->css('azuree-mobile'); ?>
        <?php endif ?>
        <!--[if IE]>
        <style type="text/css">
            .btn-text:active {
                top: 0;
            }
            .btn-text:active span {
                top: -1px;
                left: -1px;
            }
        </style>
        <![endif]-->
        <title><?php __("Project management :: z0 GRAVITY") ?></title>
        <?php
            echo $html->script(array(
                'jquery.min',
                'modernizr.custom',
                'jquery-ui.min2',
                'jquery.ui.core',
                'jquery.bt',
                'jquery.cookie',
                'z0.history',
                'green/tooltip'
            ));
            echo $html->css(array('main', 'common_z0g', 'green/tooltip'));
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
        ?>
        <script>
            document.createElement( "picture" );
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
        <?php echo $this->element("style/custom_css");  ?>
    </head>
    <?php
        $langCode = Configure::read('Config.langCode');
        $display_my_assistant = Configure::read('Config.displayAssistant');
        $avatar_assistant = Configure::read('Config.avatar_assistant');
        $employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
        $employeeName = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
        $urlAvatar = $this->UserFile->avatar($employeeIdLogin);
        $ProfitCenter = ClassRegistry::init('ProfitCenter');
        $ProfitCenterManagerBackup = ClassRegistry::init('ProfitCenterManagerBackup');

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
    ?>
    <body class="<?php echo $langCode; ?>">
        <picture>
            <source srcset="/img_z0g/logo-big.svg" type="image/svg+xml" media="(min-width: 960px)" />
            <source srcset="/img_z0g/logo-big.png" type="image/png" media="(min-width: 960px)" />
            <source srcset="/img_z0g/logo-small.svg" type="image/svg+xml" />
            <img srcset="/img_z0g/logo-small.png" alt="Logo z�ro gravity" />
        </picture>
        <nav class="third-nav">
            <a class="third-nav__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="<?php echo $this->Html->url('/employees/my_profile') ?>">
                <img src="<?php echo $urlAvatar ?>" alt="login">
            </a>
            <a class="third-nav__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" target="_blank" href="<?php echo $this->Html->url('/guides') ?>">
                <img src="/img_z0g/help.svg" alt="help">
            </a>
            <a class="third-nav__item third-nav__item--highlight" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="<?php echo $this->Html->url('/logout') ?>">
                <img src="/img_z0g/exit.svg" alt="exit">
            </a>
            <?php if( $langCode == 'vi'): ?>
            <a class="third-nav__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>">
                <span class="third-nav__item-text">vi</span>
            </a>
            <?php elseif($langCode == 'en'): ?>
            <a class="third-nav__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>">
                <span class="third-nav__item-text">en</span>
            </a>
            <?php else: ?>
            <a class="third-nav__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="<?php echo $this->here . Router::queryString(array('hl' => 'vi') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>">
                <span class="third-nav__item-text">fr</span>
            </a>
            <?php endif; ?>
            <?php if($display_my_assistant == 1): ?>
            <p class="third-nav__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="" id="nav-assitant">
                <img src="/img_z0g/my-assitant.png" alt="assitant">
            </p>
            <?php endif; ?>
        </nav>
        <div class="name-employee">
            <span><b><?php echo $employeeName ?></b></span>
        </div>
        <div class="main-content__wrapper">
            <main class="main-content">
                <?php echo $content_for_layout; ?>
            </main>
        </div>
        <div id="dialog_my_assistant" class="buttons" style="display: none;">
            <a href="javascript:void(0)" style="top: 20px;position: absolute;float: right;right: 0;" class="assistant-cancel"><?php __("Cancel") ?></a>
            <div class="me-assistant">
                <div class="wd-content-title">
                    <span style="" style=""><?php echo __('ME', true) ?></span>
                </div>
                <div id="assistant_my_task" class="assistant-content" style="display: none; clear: both; margin-top: 5px">
                    <div class="wd-content-right">
                        <div class="assistant_avatar assistant-tittle"><label><?php echo __('MY TASKS', true) ?></label></div>
                        <div class="in_progress assistant"><input type="checkbox" onclick="editFollow('assistant_my_task', 'in_progress', 'task_in_progress')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/1') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                        <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_task', 'late', 'task_late')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/2') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                        <div class="overload assistant"><input type="checkbox" onclick="editFollow('assistant_my_task', 'overload', 'task_overload')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/3'); ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                    </div>
                </div>
                <div id="assistant_my_timesheet" class="assistant-content" style="display: none; clear: both;">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('MY TIMESHEETS', true) ?></label></div>
                        <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_timesheet', 'late', 'timesheet_late')" /><p></p><label></label><span></span></div>
                    </div>
                </div>
                <div id="assistant_my_project" class="assistant-content" style="display: none; clear: both;">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('MY PROJECTS', true) ?></label><span></span></div>
                        <div class="in_progress assistant"><input type="checkbox" onclick="editFollow('assistant_my_project', 'in_progress', 'project_in_progress')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/4') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                        <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_project', 'late', 'project_late')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/5') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                        <div class="overload assistant"><input type="checkbox" onclick="editFollow('assistant_my_project', 'overload', 'project_overload')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/6') ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                    </div>
                </div>
            </div>
            <div class="my-team-assistant" style="display: none">
                <div id="assistant_my_team" class="assistant-content" style="display: none; clear: both; margin-top: 5px">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('TASKS OF MY TEAM', true) ?></label><span></span></div>
                        <div class="in_progress assistant"><input type="checkbox" onclick="editFollow('assistant_my_team', 'in_progress', 'team_in_progress')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/7') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                        <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_team', 'late', 'team_late')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/8') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                        <div class="overload assistant"><input type="checkbox" onclick="editFollow('assistant_my_team', 'overload', 'team_overload')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects/tasks_vision_new/9') ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                    </div>
                </div>
                <div id="assistant_to_validate" class="assistant-content" style="display: none; clear: both;">
                    <div class="wd-content-right">
                        <div class="assistant-tittle"><label><?php echo __('TO VALIDATE', true) ?></label></div>
                        <div class="timesheet assistant"><input type="checkbox" onclick="editFollow('assistant_to_validate', 'timesheet', 'timesheet_validate')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/activity_forecasts/to_validate?profit=' . $pId) ?>" ><?php echo __('Timesheet', true); ?></a></label><span></span></div>
                        <div class="absence assistant"><input type="checkbox" onclick="editFollow('assistant_to_validate', 'absence', 'absence_validate')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/absence_requests/manage/year/true/?profit=' . $pId) ?>" ><?php echo __('Absence', true); ?></a></label><span></span></div>
                    </div>
                </div>
                <div id="assistant_late" class="assistant-content" style="display: none; clear: both;">
                    <div class="wd-content-right" style="padding-bottom: 2%">
                        <div class="assistant-tittle"><label><?php echo __('LATE', true) ?></label></div>
                        <div class="timesheet assistant"><input type="checkbox" onclick="editFollow('assistant_late', 'late', 'timesheet_late_team')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/activity_forecasts/not_sent_yet?profit=' . $pId) ?>" ><?php echo __('Timesheet', true); ?></a></label><span></span></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    $curYear = date('Y');
        echo $html->script(array(
            //'newDesign/svg4everybody',
//                'newDesign/html5shiv',
//                'newDesign/jquery',
            //'newDesign/webfontloader',
            //'newDesign/polyfill',
//                'newDesign/svg_utils',
//                'newDesign/non-scaling-stroke-polyfill',
            'newDesign/togglable',
//                'newDesign/main_flow',
//                'newDesign/col-menu',
//                'newDesign/task-list',
            'newDesign/sound',
//                'newDesign/prism',
            'newDesign/picturefill',
            //'newDesign/webfonts',
            // 'common_z0g',
        ));
    ?>
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
    <script>
        var myAssis;
        var pId = <?php echo json_encode($pId) ?>;
        var curYear = <?php echo json_encode($curYear) ?>;
        $.z0.History.load('my_assistants', function(data){
            myAssis = data;
        });
        $('#nav-assitant').click(function(){
            $.ajax({
                url : '<?php echo $html->url(array('controller' => 'my_assistants', 'action' => 'getMyAssistant')); ?>',
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    // my task
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
                        $('.my-team-assistant').show();
                        $('#assistant_late').show();
                        var selector = $('#assistant_late');
                        $.each(data['late'], function(index, value){
                            selector.find('.' + index).find('span').html(value['number']);
                            if(value['follow'] == 1){
                                selector.find('.' + index).find('input').prop("checked", true);
                            }
                        });
                    }
                    myAssis.set('select', 1);
                    $.z0.History.save('my_assistants', myAssis);
                    $("#dialog_my_assistant").dialog('option',{title:''}).dialog('open');
                }
            });
        });
        $(".assistant-cancel").live('click',function(){
            myAssis.set('select', 0);
            $.z0.History.save('my_assistants', myAssis);
            $("#dialog_my_assistant").dialog('close');
        });
        $('#dialog_my_assistant').dialog({
            position    :'right',
            autoOpen    : false,
            autoHeight  : true,
            modal       : false,
            width       : '240',
            dialogClass : "custom-dialog assistant-dialog",
            show : function(e){
            },
            open : function(e){
            }
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
    </body>
</html>
