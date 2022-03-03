<?php 
echo $html->script('jquery.multiSelect');
echo $html->css('jquery.multiSelect');

$langCode = Configure::read('Config.langCode');
$ProfitCenter = ClassRegistry::init('ProfitCenter');
$ProfitCenterManagerBackup = ClassRegistry::init('ProfitCenterManagerBackup');
$display_my_assistant = Configure::read('Config.displayAssistant');
$avatar_assistant = Configure::read('Config.avatar_assistant');

$companyName = !empty($employee_info['Company']['company_name']) ? $employee_info['Company']['company_name'] : ''; 
$first_name = !empty($employee_info['Employee']['first_name']) ? $employee_info['Employee']['first_name'] : '';
$last_name = !empty($employee_info['Employee']['last_name']) ? $employee_info['Employee']['last_name'] : '';
$employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
$urlAvatar = $this->UserFile->avatar($employeeIdLogin);
$display_my_assistant = Configure::read('Config.displayAssistant');
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
$role = ($is_sas != 1) ? $employee_info['Role']['name'] : '';
$checkSeeResource = isset($employee_info['Role']['id']) && ($employee_info['Role']['name'] != "admin") && ($employee_info['Role']['name'] != "hr") && ($employee_info['Role']['name'] != "pm");
$grid_url_rollback = ClassRegistry::init('HistoryFilter')->find('first', array(
    'recursive' => -1,
    'fields' => array('id', 'params'),
    'conditions' => array(
        'path' => 'project_grid_url',
        'employee_id' => $employee_info['Employee']['id']
    )
));
$grid_url_rollback = !empty($grid_url_rollback) ? $grid_url_rollback['HistoryFilter']['params'] : '';
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
$projectId = !empty($_list_profit_center_managers) ? array_shift($_list_profit_center_managers) : '';
$backupManagers = ClassRegistry::init('ProfitCenterManagerBackup')->find('list', array(
    'recursive' => -1,
    'conditions' => array('employee_id' => $employeeIdLogin),
    'fields' => array('profit_center_id', 'profit_center_id')
));
$canManageResource = $role == 'pm' && $employee_info['CompanyEmployeeReference']['control_resource'];
$conds = array(
    'manager_id' => $employeeIdLogin,
    'id' => $backupManagers
);

if( $canManageResource && !empty($myPC)) $conds['id'][] = $myPC['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
$profit = ClassRegistry::init('ProfitCenter')->find('first', array(
    'recursive' => -1,
    'conditions' => array('OR' => $conds)));
$hasManager = (!empty($employeeIdLogin) && !empty($employee_info['Company']['id']) && ($profit));
if($hasManager){
    $profit = array_shift($profit);
}
$hasManagerMyDiary = (!empty($employeeIdLogin) && !empty($employee_info['Company']['id']) && ($profitMyDiary = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
    'recursive' => -1,
    'conditions' => array('employee_id' => $employeeIdLogin)))));
if($hasManagerMyDiary){
    $profitMyDiary = array_shift($profitMyDiary);
}
$profitMyDiary = !empty($profitMyDiary['profit_center_id']) ? $profitMyDiary['profit_center_id'] : '';

?> 
<div class="header-top">
    <h1 class="wd-logo">
        <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 498 499" style="max-height: 40px;"><defs>
            <style>.cls-1{fill:#1d2a34;}.cls-2{fill:#217fc2;}</style>
            </defs><title>z0g_icon</title><g id="Calque_2" data-name="Calque 2"><g id="Calque_2-2" data-name="Calque 2"><path class="cls-1" d="M497.44,233.31H464q.76,9,.78,18.27c0,119.22-96.62,215.87-215.8,215.87A215.06,215.06,0,0,1,95.59,403.38L72.93,426A248.16,248.16,0,0,0,249,499c137.52,0,249-111.52,249-249.08C498,244.33,497.8,238.8,497.44,233.31Z"/><path class="cls-2" d="M415,232.48H249v33.21H397.48a149.45,149.45,0,1,1-42.84-122.28l23.48-23.49A182,182,0,0,0,249,66.42c-95.25,0-173.45,73-181.84,166.06H33.83C42.31,121,135.4,33.21,249,33.21A215.06,215.06,0,0,1,401.59,96.44L425.07,73A248.16,248.16,0,0,0,249,0C111.48,0,0,111.52,0,249.08c0,5.59.2,11.12.56,16.61h66.6c8.39,93.1,86.59,166.06,181.84,166.06,100.85,0,182.6-81.78,182.6-182.67,0-5.59-.27-11.13-.76-16.6Z"/></g></g>
        </svg> -->
             
        <a href="<?php echo $html->url('/') ?>"><?php echo $companyName ?></a>
    </h1>
    <!-- <div class="wd-login"> -->
    <nav class="third-nav-preview">
        <a class="nav-preview__item nav-item__avatar" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" href="<?php echo $this->Html->url('/employees_preview/my_profile?view=new') ?>">
            <div class="employee-name">
                <span><?php echo $first_name ?></span>
                <span><?php echo $last_name ?></span>
            </div>
            <div class="img-inner"><img src="<?php echo $urlAvatar ?>" alt="<?php __('Login');?>"></div>
        </a>
        <?php if($display_my_assistant == 1): ?>
        <div class='item__assitant' style = "display: inline-block">
        <a class="nav-preview__item nav-item__assitant"  href="#" id="nav-assitant" title="<?php __('Assitant');?>"  >
            <img src="<?php echo $html->url('/img/new-icon/notification.png'); ?>"/>
        </a>
            <div id="dialog_my_assistant" class="buttons" style="display: none">
                <div class="me-assistant">
                    <div class="wd-content-title">
                        <img style="" src="/img_z0g/avatar-me.svg" />
                        <span style="" style=""><?php echo __('ME', true) ?></span>
                    </div>
                    <div id="assistant_my_task" class="assistant-content">
                        <div class="wd-content-assistant">
                            <div class="assistant_avatar assistant-tittle"><label><?php echo __('MY TASKS', true) ?></label></div>
                            <div class="in_progress assistant"><input type="checkbox" onclick="editFollow('assistant_my_task', 'in_progress', 'task_in_progress')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/1') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                            <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_task', 'late', 'task_late')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/2') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                            <div class="overload assistant"><input type="checkbox" onclick="editFollow('assistant_my_task', 'overload', 'task_overload')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/3'); ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                        </div>
                    </div>
                    <div id="assistant_my_timesheet" class="assistant-content">
                        <div class="wd-content-assistant">
                            <div class="assistant-tittle"><label><?php echo __('MY TIMESHEETS', true) ?></label></div>
                            <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_timesheet', 'late', 'timesheet_late')" /><p></p><label></label><span></span></div>
                        </div>
                    </div>
                    <div id="assistant_my_project" class="assistant-content">
                        <div class="wd-content-assistant">
                            <div class="assistant-tittle"><label><?php echo __('MY PROJECTS', true) ?></label><span></span></div>
                            <div class="in_progress assistant"><input type="checkbox" onclick="editFollow('assistant_my_project', 'in_progress', 'project_in_progress')" /> <p></p> <label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/4') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                            <div class="late assistant"><input type="checkbox" onclick="editFollow('assistant_my_project', 'late', 'project_late')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/5') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                            <div class="overload assistant"><input type="checkbox" onclick="editFollow('assistant_my_project', 'overload', 'project_overload')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/6') ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                        </div>
                    </div>
                </div>
                <div class="my-team-assistant" style="display: none">
                    <div class="wd-content-title">
                        <span style=""><?php echo __('MY TEAM', true) ?></span>
                    </div>
                    <div id="assistant_my_team" class="assistant-content" style="display: none; clear: both; margin-top: 5px">
                        <div class="wd-content-assistant">
                            <div class="assistant-tittle"><label><?php echo __('TASKS OF MY TEAM', true) ?></label><span></span></div>
                            <div class="in_progress assistant"><input type="checkbox" onclick="editFollow('assistant_my_team', 'in_progress', 'team_in_progress')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/7') ?>" ><?php echo __('In progress', true); ?></a></label><span></span></div>
                            <div class="late assistant"><p></p><input type="checkbox" onclick="editFollow('assistant_my_team', 'late', 'team_late')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/8') ?>" ><?php echo __('Late', true); ?></a></label><span></span></div>
                            <div class="overload assistant"><input type="checkbox" onclick="editFollow('assistant_my_team', 'overload', 'team_in_progress')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/projects_preview/tasks_vision_new/9') ?>" ><?php echo __('Overload', true); ?></a></label><span></span></div>
                        </div>
                    </div>
					<?php $enableRMS = $this->Session->read('enableRMS');
						if($enableRMS == true) : ?>
                    <div id="assistant_to_validate" class="assistant-content" style="display: none; clear: both;">
                        <div class="wd-content-assistant">
                            <div class="assistant-tittle"><label><?php echo __('TO VALIDATE', true) ?></label></div>
                            <div class="timesheet assistant"><input type="checkbox" onclick="editFollow('assistant_to_validate', 'timesheet', 'timesheet_validate')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/activity_forecasts/to_validate?profit=' . $projectId) ?>" ><?php echo __('Timesheet', true); ?></a></label><span></span></div>
                            <div class="absence assistant"><input type="checkbox" onclick="editFollow('assistant_to_validate', 'absence', 'absence_validate')" /><p></p><label><a target="_blank" href="<?php echo $this->Html->url('/absence_requests/manage/year/true/?profit=' . $projectId) ?>" ><?php echo __('Absence', true); ?></a></label><span></span></div>
                        </div>
                    </div>
                    <div id="assistant_late" class="assistant-content" style="display: none; clear: both;">
                        <div class="wd-content-assistant" style="padding-bottom: 2%">
                            <div class="assistant-tittle"><label><?php echo __('LATE', true) ?></label></div>
                            <div class="timesheet assistant">
                                <input type="checkbox" onclick="editFollow('assistant_late', 'late', 'timesheet_late_team')" /><p></p>
                                <label><a target="_blank" href="<?php echo $this->Html->url('/activity_forecasts/not_sent_yet?profit=' . $projectId) ?>" ><?php echo __('Timesheet', true); ?></a></label><span></span></div>
                        </div>
                    </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <a class="nav-preview__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" target="_blank" href="<?php echo $html->url("/activity_forecasts/my_diary?profit=" . $profitMyDiary); ?>" id="nav-guide" title="<?php __('My Diary');?>">
            <img src="<?php echo $html->url('/img/new-icon/agenda.png'); ?>"/>
        </a>
        <a class="nav-preview__item" data-hover-sound-src_rermoved="/img_z0g/glass.mp3" target="_blank" href="<?php echo $this->Html->url('/guides/?view=new') ?>" id="nav-guide" title="<?php __('Help');?>">
            <img  src="<?php echo $html->url('/img/new-icon/question.png'); ?>"/>
        </a>
        <?php $langCode = Configure::read('Config.langCode'); ?>
        <div class="nav-preview__item">
            <p class="burger burger-account">
                <span class="menu-icon">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </span>
            </p>
            <ul class="nav-account">
                <li><a href="/employees_preview/my_profile?view=new" ><span class="icon-user"></span><?php __('My Profile'); ?></a></li>
                <li><a href="/logout"><span class="icon-power"></span> <?php __('Sign out'); ?></a></li>
                <li><a href="?hl=fr" class="<?php echo ($langCode == 'fr' ? 'lang-active' : ''); ?>"><img title="France"  src="<?php echo $html->url('/img/new-icon/fr.png'); ?>"/></a><a href="?hl=en" class="<?php echo ($langCode == 'en' ? 'lang-active' : ''); ?>"><img title="English"  src="<?php echo $html->url('/img/new-icon/en.png'); ?>"/></a><a href="" class="<?php echo ($langCode == 'vn' ? 'lang-active' : ''); ?>"><img title="Viet Nam"  src="<?php echo $html->url('/img/new-icon/vn.png'); ?>"/></a></li>
            </ul>

        </div>
    </nav>
    <div style="clear: both"></div>
</div>
<div class="header-bottom">
    <div class="header-bottom-image">
        <?php if(!empty($employee_info['Color']['attachment_background'])){
            $link = $this->Html->url(array('controller' => 'colors', 'action' => 'attachment', $employee_info['Company']['id'], 'attachment_background' , '?' => array('sid' => $api_key)), true);
        } else $link = $html->url('/img/new-icon/img-header.png');
        ?>
        <img title="header-bottom"  src="<?php echo $link ?>"/>
        <?php 
        /* Remove Header Weather */
        echo '';
        //echo $this->element('header_weather');
        ?>

    </div>
    <?php
        $flag_class = "";
        if($this->params['controller'] == 'projects_preview' && ($this->params['action'] == 'index' || $this->params['action'] == 'index_plus')){
                $flag_class ="has-addButton";
        }?>
    <div class="header-bottom-action <?php echo $flag_class; ?>">
        <ul class="header-list-action">
            <li><a href="javascript:;" class="open-menu" onclick="openMenu();"><img title="Menu"  src="<?php echo $html->url('/img/new-icon/list-black.png'); ?>"/><img title="Menu"  src="<?php echo $html->url('/img/new-icon/list-light.png'); ?>"/></a>
                 <?php echo $this->element("main_menu_preview"); ?>
            </li>
            <?php if(!isset($companyConfigs['display_project_global']) || $companyConfigs['display_project_global'] == 1) { ?>
                <li><a href="<?php echo '/projects/map/' . $grid_url_rollback ?>?view=new" class="btn-globe"><img title="Map"  src="<?php echo $html->url('/img/new-icon/map-black.png'); ?>"/><img title="Map"  src="<?php echo $html->url('/img/new-icon/map-light.png'); ?>"/></a></li>
            <?php } ?>
            <?php if($is_sas || (!$is_sas && $role =='admin')){ ?>
              <li><a href="<?php echo '/administrators/?view=new'?>"><img title="Config"  src="<?php echo $html->url('/img/new-icon/config-black.png'); ?>"/><img title="Config"  src="<?php echo $html->url('/img/new-icon/cofig-white.png'); ?>"/></a></li>
            <?php } ?>
            <?php if(!$checkSeeResource){ ?>
                <li><a href="<?php echo '/employees/?view=new'?>"><img title="Export"  src="<?php echo $html->url('/img/new-icon/export-black.png'); ?>"/><img title="Export"  src="<?php echo $html->url('/img/new-icon/export-light.png'); ?>"/></a></li>
            <?php } ?>
            <?php if(isset($project_id)){ ?>
                <li><a href="/project_dependencies/index/<?php echo $project_id; ?>" title="<?php echo __('Dependency', true); ?>"><i class="icon-share"></i></a></li>
            <?php  } ?>  
            <?php 
                $is_list_active = $this->params['controller'] == 'projects_preview' && $this->params['action'] == 'index';
                $is_grid_active = $this->params['controller'] == 'projects_preview' && $this->params['action'] == 'index_plus';
            ?>

            <li>
                <a href="<?php echo '/projects_preview/index_plus/' . $grid_url_rollback ?>?view=new"  class="icon-action <?php echo $is_grid_active ? 'active-blue' : ''; ?>">
                    <img title="<?php __('Grid'); ?>" class="action_icon" src="<?php echo $html->url('/img/new-icon/grid.png'); ?>"/>
                    <img title="<?php __('Grid'); ?>" class="action_icon_active" src="<?php echo $html->url('/img/new-icon/grid-blue.png'); ?>"/>
                </a>
            </li>
            <li>
                <a href="<?php echo '/projects_preview/?view=new'?>" class="icon-action <?php echo $is_list_active ? 'active-blue' : ''; ?>">
                    <img title="<?php __('Project List');?>" class="action_icon" src="<?php echo $html->url('/img/new-icon/list.png'); ?>"/>
                    <img title="<?php __('Project List');?>" class="action_icon_active" src="<?php echo $html->url('/img/new-icon/list-blue.png'); ?>"/>
                </a>
            </li>
        </ul>
        <?php
        $not_add_icon = 'not-add-icon';
        if($this->params['controller'] == 'projects_preview' && ($this->params['action'] == 'index' || $this->params['action'] == 'index_plus')){ 
                $not_add_icon = '';
         } ?>
        <ul class="header-project-layout <?php echo $not_add_icon; ?>">
            <li>
                <span class="active-image">
                    <a href="javascript:;" onclick="openHeader();">
                        <img title="header-bottom"  src="<?php echo $html->url('/img/new-icon/active-blue.png'); ?>"/>
                        <img title="header-bottom"  src="<?php echo $html->url('/img/new-icon/active-dark.png'); ?>"/>
                    </a>
                </span>
            </li>
            
            <?php if($this->params['controller'] == 'projects_preview' && ($this->params['action'] == 'index' || $this->params['action'] == 'index_plus')){
                if(($employee_info['Employee']['create_a_project'] == 1 && empty($profileName)) || (!empty($profileName) && ($profileName['ProfileProjectManager']['can_create_project'] == 1)) || ($employee_info['Role']['id'] == 2)){ ?>
            <li class="add-project"><a href="javascript:;" onclick="addNewProject();"><img title="<?php __('Add new project');?>"  src="<?php echo $html->url('/img/new-icon/add.png'); ?>"/></a></li>
            <?php }
            } ?>
        </ul>
        <div style="clear: both"></div>
    </div>
    <?php echo $this->element("project_header"); ?>
</div>
<div id="addProjectTemplate" style="width: 280px;display: none;">
</div>
<?php //echo $this->element('dialog_detail_value') ?>
 <?php
    $curYear = date('Y');
    ?>
<script type="text/javascript">
    function openHeader(){
        var header_bottom = $('.active-image').closest('.header-bottom');
        header_bottom.find('.header-bottom-image').toggleClass('active');
        $('.active-image').toggleClass('active');
    }
    function openMenu(){
        var open_menu = $('.open-menu').toggleClass('active');
        $('#wd-top-nav').toggleClass('active');
    }
    function addNewProject(){
        $('#addProjectTemplate').toggleClass('open');
        $('.add-project a').toggleClass('active');
        $(window).trigger('resize');
        if( $('#addProjectTemplate').hasClass('open') && !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
            $('#addProjectTemplate').addClass('loading');
            $.ajax({
                url : "/projects_preview/add_popup/",
                type: "GET",
                cache: false,
                success: function (html) {
                    $('#addProjectTemplate').empty().html(html);
                    $(window).trigger('resize');
                    $('#add-form').trigger('reset');
                    $('#addProjectTemplate').addClass('loaded');
                    $('#addProjectTemplate').removeClass('loading');
                }
            });
        }
    }
    var myAssis;
    var pId = <?php echo json_encode($projectId) ?>;
    var curYear = <?php echo json_encode($curYear) ?>;
    var savePositionAssistant;

    if($('.ui-dialog-titlebar').attr('id') == 'ui-dialog-title-dialog_my_assistant'){
        $(this).css("display", n)
    }
    $('.burger-account').click(function(){
        $(this).toggleClass('active');
        $(this).next('ul.nav-account').toggleClass('active');

    });
    $('body').on('click', function(e){
        if($(e.target).hasClass('.nav-preview__item') || $('.nav-preview__item').find(e.target).length == 0){
            $('.burger-account').removeClass('active');
            $('ul.nav-account').removeClass('active');
        }
    });
    $('#nav-assitant').click(function(){
        $(this).next('#dialog_my_assistant').toggle();
        $.ajax({
            url : '<?php echo $html->url(array('controller' => 'my_assistants', 'action' => 'getMyAssistant')); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                // my task
                savePositionAssistant = data['position'];
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
                myAssis.set('select', 1);
                $.z0.History.save('my_assistants', myAssis);
                // $("#dialog_my_assistant").dialog('option',{title:''}).dialog('open');
            }
        });
    });
    $(".assistant-cancel").click(function(){
        myAssis.set('select', 0);
        $.z0.History.save('my_assistants', myAssis);
        $("#dialog_my_assistant").dialog('close');
    });
    // $('#dialog_my_assistant').dialog({
    //     position    :'right',
    //     autoOpen    : false,
    //     autoHeight  : true,
    //     modal       : false,
    //     width       : '15%',
    //     draggable   : true,
    //     dialogClass : "custom-dialog",
    //     show : function(e){
    //     },
    //     open : function(e){
    //         $('.ui-dialog.custom-dialog').find('div.ui-dialog-titlebar').css('background', '#eee');
    //         $('.ui-dialog.custom-dialog').css('background', '#eee');
    //         $('.ui-dialog.custom-dialog').find('.ui-dialog-titlebar-close').css('display', 'none');
    //         if(savePositionAssistant && savePositionAssistant != 'undefined'){
    //             var top = (savePositionAssistant.top > 0 && savePositionAssistant.top < 1000)? savePositionAssistant.top : 0;
    //             var left = (savePositionAssistant.left > 0 && savePositionAssistant.left < 1950) ? savePositionAssistant.left : 0;
    //             $('.ui-dialog.custom-dialog').css({'top': top + 'px','left': left + 'px'});
    //         }
    //     }
    // });
    $(function () {
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
    $('.ui-dialog.custom-dialog').draggable({
        // cancel: ".me-assistant",
        stop: function(event, ui){
            var position = $(".ui-dialog.custom-dialog").position();
            $.ajax({
                url : "/my_assistants/saveAssitantPositions",
                type: "POST",
                data: {
                    top: position.top,
                    left: position.left
                }
            });
        }
    });
	$(window).ready(function(){
		setTimeout(function(){
			$('#flashMessage').fadeOut('300');
		} , 3000);
		setTimeout(function(){
			$(window).trigger('resize');
		} , 3500);
	});
</script>