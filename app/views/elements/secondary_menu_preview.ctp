<div class="openMenu" onclick="openMenuLeft();"><img title="burger"  src="<?php echo $html->url('/img/new-icon/list-black.png'); ?>"/></div>
<div class="wd-content-left">
   <div class="content-left-inner">
<?php
echo $html->script('jquery.flexslider-min');
echo $html->css('flexslider');
?>
<?php
$AppStatusProject = $this->Session->read('App.status_oppor');
$employee_info = $this->Session->read('Auth.employee_info');
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
$role = ($is_sas != 1) ? $employee_info['Role']['name'] : '';
$employee_id = empty($employee_id) ? $employee_info['Employee']['id'] : $employee_id;
$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
$category = 0;
$check_budget_actis = $check_budget_actis_AC = $activatedRevieWhenClickDetail = $activatedViewWhenClickDetail = false;
$adminAudits = $createMenus = array();
$seeMenuAudit = $this->Session->read('seeMenuAudit');
$seeMenuBusiness = $this->Session->read('seeMenuBusiness');
$enablePMS = $this->Session->read('enablePMS');
$enableRMS = $this->Session->read('enableRMS');
$enableAudit = $this->Session->read('enableAudit');
$enableReport = $this->Session->read('enableReport');
$enableBusines = $this->Session->read('enableBusines');
$enableZogMsgs = $this->Session->read('enableZogMsgs');
$actedActiView = $this->Session->read('ActedActiFunc');
$actedActiView = !empty($actedActiView) ? $actedActiView : 'review';
$language = Configure::read('Config.language');
$this->is_pm = isset($employee_info['Role']['id']) && $employee_info['Role']['name'] =='pm' && $employee_info['CompanyEmployeeReference']['control_resource'] == '1';
$checkSeeResource = isset($employee_info['Role']['id']) && ($employee_info['Role']['name'] != "admin") && ($employee_info['Role']['name'] != "hr") && !$this->is_pm;
$checkSeePersonalizedViews = isset($employee_info['Role']['id']) && ($employee_info['Role']['name'] != "admin") && ($employee_info['Role']['name'] != "hr") && $this->is_pm;
if(!empty($project_id)){
    $category = ClassRegistry::init('Project')->find('first', array(
        'recursive' => -1,
        'fields' => array('category'),
        'conditions' => array('id' => $project_id)
    ));
    $category = !empty($category) ? $category['Project']['category'] : 0;
}
$menuAdmins = array('translations', 'versions', 'cities','colors', 'countries', 'companies', 'currencies', 'profit_centers', 'absences', 'workdays', 'response_constraints', 'holidays', 'activity_columns', 'activity_families','contract_types','liscenses', 'activity_settings', 'budget_settings', 'budget_customers', 'budget_providers', 'budget_types', 'budget_funders', 'security_settings', 'menus', 'activity_exports','staffing_systems','system_configs', 'action_logs', 'tasks', 'project_importers', 'project_acceptance_types','periods','admin_task', 'dependencies', 'kpi_settings', 'company_configs','sql_manager', 'externals', 'profile_project_managers',
    //tickets
    'ticket_profiles', 'ticket_profile_permissions', 'ticket_statuses', 'ticket_metas', 'vision_task_exports', 'expectation_datasets', 'expectation_colors', 'expectations', 'expectation_translations'
);

$menuProjects = array('projects', 'projects_preview' ,'project_teams', 'project_parts', 'project_phase_plans', 'project_phase_plans_preview', 'project_milestones', 'project_milestones_preview', 'project_tasks', 'project_tasks_preview', 'project_risks', 'project_risks_preview', 'project_issues', 'project_issues_preview', 'project_decisions', 'project_livrables', 'project_livrables_preview', 'project_evolutions', 'project_amrs', 'project_amrs_preview', 'project_staffings', 'project_staffings_preview', 'project_created_vals', 'project_created_vals_preview', 'project_global_views',  'project_global_views_preview', 'project_local_views', 'project_budget_internals', 'project_budget_internals_preview', 'project_budget_externals', 'project_budget_sales', 'project_budget_sales_preview', 'project_budget_purchases', 'project_budget_synthesis', 'project_images', 'project_images_preview', 'project_finances', 'project_finances_preview', 'project_budget_provisionals', 'project_acceptances', 'project_expectations', 'project_budget_fiscals', 'project_dependencies', 'project_dependencies_preview', 'video', 'video_preview', 'zog_msgs', 'kanban', 'project_local_views_preview','flash_info', 'project_budget_externals_preview', 'project_articles');
$menuActivities = array('activity_forecasts', 'activities', 'activity_tasks', 'activity_tasks_preview', 'activity_budget_internals', 'activity_budget_externals', 'activity_budget_sales', 'activity_budget_synthesis', 'activity_budget_provisionals', 'team_workloads');
$menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_purchases', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals','project_budget_externals_preview');
$menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_purchases', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
$menuBudgetActivities = array('activity_budget_internals', 'activity_budget_externals', 'activity_budget_sales', 'activity_budget_synthesis', 'activity_budget_provisionals');
$menuAudits = array('audit_admins', 'audit_settings', 'audit_missions', 'audit_recoms');
$menuSales = array('sale_settings', 'sale_roles', 'sale_customers', 'sale_customer_contacts', 'sale_leads', 'sale_expenses', 'categories', 'easyraps');
$menuReports = array('reports');

if(in_array($this->params['controller'], $menuBudgets)){
    $check_budget_actis = true;
}
if(in_array($this->params['controller'], $menuBudgetActivities)){
    $check_budget_actis_AC = true;
}
if($this->params['controller'] == 'activity_tasks' &&
($this->params['action'] == 'index' ||
 $this->params['action'] == 'visions' ||
 $this->params['action'] == 'dash_board' ||
 $this->params['action'] == 'teams' ||
 $this->params['action'] == 'teams_yes' ||
 $this->params['action'] == 'import_csv')
    || in_array($this->params['controller'], $menuBudgetActivities)
){
    if($role =='conslt'){
        $activatedViewWhenClickDetail = true;
    } else {
        if($actedActiView == 'review'){
            $activatedRevieWhenClickDetail = true;
        } else {
            $activatedViewWhenClickDetail = true;
        }
    }
}
/**
 * Set active for menu parent.
 */
if ($this->params['controller'] == 'pages')
    $active = 1;
if ($this->params['controller'] == 'projects' || $this->params['controller'] == 'projects_preview'){
    if($AppStatusProject == 2){
        $active = 7;
    } else {
        $active = 2;
    }
}
if ($this->params['controller'] == 'employees' || $this->params['controller'] == 'employee_absences' || $this->params['controller'] == 'absence_requests')
    $active = 3;
if ($this->params['controller'] == 'user_views' || $this->params['controller'] == 'user_views_preview')
    $active = 5;
if (($this->params['controller'] == 'projects' || $this->params['controller'] == 'projects_preview') && $this->params['action'] == 'edit')
    $active = 6;

if ($this->params['controller'] == 'employee_absences' || $this->params['controller'] == 'absence_requests')
    $active = 10;

if (in_array($this->params['controller'], $menuActivities)){
    $active = 11;
    /**
     * Lay Activity Settings
     */
    $activitySettings = ClassRegistry::init('ActivitySetting')->find('first');
}

if ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'my_diary')
    $active = 12;

if (in_array($this->params['controller'], $menuAdmins) || (strpos($this->params['controller'], 'project_') === 0) || ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'import_timesheet') || ($this->params['controller'] == 'employees' && $this->params['action'] == 'profile'))
    $active = 4;
if (in_array($this->params['controller'], $menuProjects)){
    $active = 2;
    if($AppStatusProject == 2 || $category == 2){
        $active = 7;
    }
    //auto insert newly menu (acceptance, provisional)
    //By QN
    //$this->requestAction('/menus/autoInsert', array('pass' => array(null)));
    // $createMenus = ClassRegistry::init('Menu')->find('all', array(
    //     'recursive' => -1,
    //     'conditions' => array('company_id' => $company_id, 'model' => 'project', 'display' => 1),
    //     'order' => array('weight' => 'ASC')
    // ));
    // $createMenus = !empty($createMenus) ? Set::combine($createMenus, '{n}.Menu.id', '{n}.Menu', '{n}.Menu.parent_id') : array();
    if($this->params['action'] != 'tasks_vision_new'){
        $menu = ClassRegistry::init('Menu')->find('threaded', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'display' => 1
            ),
            'order' => array('weight' => 'ASC')
        ));
        $profile_account = !empty($employee_info['Employee']['profile_account']) ? $employee_info['Employee']['profile_account'] : 0;
        if($profile_account){
            $menu = ClassRegistry::init('ProfileProjectManagerDetail')->find('threaded', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'display' => 1,
                    'model_id' => $profile_account
                ),
                'order' => array('weight' => 'ASC')
            ));
        }
    }
    // ob_clean();
    // debug( $menu); exit;
}

$ACBudgetSettings = isset($companyConfigs['budget_team']) && !empty($companyConfigs['budget_team']) ?  true : false;
$EPM_see_the_budget = isset($companyConfigs['EPM_see_the_budget']) && !empty($companyConfigs['EPM_see_the_budget']) ?  true : false;
$seeBudgetPM = ClassRegistry::init('CompanyEmployeeReference')->find('first', array(
    'recursive' => -1,
    'conditions' => array('employee_id' => $employee_id),
    'fields' => array('see_budget', 'role_id')
));
$resetRole = !empty($seeBudgetPM) && !empty($seeBudgetPM['CompanyEmployeeReference']['role_id']) ? $seeBudgetPM['CompanyEmployeeReference']['role_id'] : 4;
$seeBudgetPM = !empty($seeBudgetPM) && !empty($seeBudgetPM['CompanyEmployeeReference']['see_budget']) ? $seeBudgetPM['CompanyEmployeeReference']['see_budget'] : 0;
$passBussiness = 'cus';
if($this->params['controller'] == 'sale_customers'){
    if($this->params['action'] == 'index' && !empty($this->params['pass'][0])){
        $passBussiness = $this->params['pass'][0];
    }
    if($this->params['action'] == 'update' && !empty($this->params['pass'][1])){
        $passBussiness = $this->params['pass'][1];
    }

}
$activityScreen = ($this->params['controller'] == 'activity_forecasts'
|| $this->params['controller'] == 'activities'
|| $this->params['controller'] == 'activity_tasks'
|| $this->params['controller'] == 'activity_tasks_preview'
|| $this->params['controller'] == 'team_workloads'
) && ($this->params['action'] != 'my_diary' && $this->params['action'] != 'import_timesheet' && $this->params['action'] != 'import_csv');
function _se_recursive2($item, $pass, $_Model, $profile_account){
    $me = $item[$_Model];
    // ob_clean();
    // debug($item);
    // debug($pass['params']);
    // debug($_Model);
    // debug( $profile_account);
    // exit;
    $isBudgetScreen = in_array($me['controllers'], $pass['disableMenuBudgetAndFinance']);
    
    if($isBudgetScreen && $pass['resetRole'] == 3 && ((!$pass['EPM_see_the_budget'] && !$pass['seeBudgetPM']))){
        return '';
    }
    $class = '';
    if($me['controllers'] == 'projects_preview'){
        $class .= 'tooltip-pm-details';
    } elseif($me['controllers'] == 'project_amrs'){
        $class .= 'tooltip-pm-amrs';
    } elseif($me['controllers'] == 'zog_msgs' && $me['functions'] == 'detail' && $pass['params']['controller'] == $me['controllers']){
        $class = 'wd-current';
    }
    if( $pass['check_budget_actis'] && $isBudgetScreen ){
        $class = 'wd-current ';
    } else if($me['controllers'] == 'projects_view'){
        if($pass['params']['action'] == $me['functions']){
            $class = 'wd-current';
        }
    } else if($pass['params']['controller'] == $me['controllers'] && $pass['params']['action'] == $me['functions']) {
        $class = 'wd-current';
    }
    $is_this_controller =  trim( str_replace('_preview', '', $pass['params']['controller']), ' \t\n\r\0\x0B_') == trim( str_replace('_preview', '', $me['controllers']), ' \t\n\r\0\x0B_');
    if( $is_this_controller && $pass['params']['action'] == $me['functions']){
        $class = 'wd-current';   
    }
    $_pc = '';
    if(isset($me['controllers']) && $me['controllers'] == 'project_budget_fiscals' && $pass['role'] == 'admin'){
        $_pc = -1;
    }
    $html ='';
    $link = $pass['html']->url(array('controller' => $me['controllers'], 'action' => $me['functions'], $pass['project'], $_pc));

    $name = $pass['language'] == 'eng' ? $me['name_eng'] : $me['name_fre'];
    if($me['controllers'] == 'project_staffings' || $me['controllers'] == 'project_staffings_preview' ){
        $html = '<li class="' . $class . '"><a onclick="checkStaffing('. $pass['project'] .')" href="javascript:void(0)">' . $name . '</a>';
    } else {
        $html = '<li class="' . $class . '"><a href="' . $link . '">' . $name . '</a>';
    }
    //children recursive
    if( !empty($item['children']) ){
        $html .= '<ul>';
        foreach($item['children'] as $child){
            if($profile_account){
                $html .= _se_recursive2($child, $pass, 'ProfileProjectManagerDetail', $profile_account);
            } else {
                $html .= _se_recursive2($child, $pass, 'Menu', $profile_account);
            }
        }
        $html .= '</ul>';
    }
    $html .= '</li>';
    return $html;
}
function _se_recursiveMenu2($menu, $ul = true, $pass = array(), $profile_account){
    $html = '';
    if( $ul )$html .= '<ul>';
    foreach($menu as $item){
        if($profile_account){
            $html .= _se_recursive2($item, $pass, 'ProfileProjectManagerDetail', $profile_account);
        } else {
            $html .= _se_recursive2($item, $pass, 'Menu', $profile_account);
        }
    }
    if( $ul )$html .= '</ul>';
    echo $html;
}

?>
<div id="sub-nav">
    <!--reports-->
    <?php if($this->params['controller'] == 'reports'): ?>
        <ul id="sub-nav-report">
            <?php if($is_sas): ?>
            <li class="<?php echo ($this->params['controller'] == 'reports')&& $this->params['action'] == 'index' ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/reports/index/") ?>"><?php __("Dashboard") ?></a></li>
            <?php endif; ?>
            <li class="<?php echo ($this->params['controller'] == 'reports')&& $this->params['action'] == 'sql_report' ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/reports/sql_report/") ?>"><?php __("Report") ?></a></li>
        </ul>
    <?php endif; ?>
    <!--audit-->
    <?php
        if($this->params['controller'] == 'audit_missions' || $this->params['controller'] == 'audit_admins' || $this->params['controller'] == 'audit_recoms' || $this->params['controller'] == 'audit_settings'):
    ?>
    <ul id="sub-nav-audit">
        <li class="<?php echo ($this->params['controller'] == 'audit_missions') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/audit_missions/index/") ?>"><?php __("Mission") ?></a></li>
        <li class="<?php echo ($this->params['controller'] == 'audit_recoms') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/audit_recoms/index_follow_employ/") ?>"><?php __("Recommendation") ?></a></li-->
        <?php if ($is_sas || $role == "admin" || (!empty($adminAudits) && in_array($employee_id, $adminAudits))):?>
        <li class="<?php echo ($this->params['controller'] == 'audit_admins' || $this->params['controller'] == 'audit_settings') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/audit_settings/index/auditor_company/") ?>"><?php __("Administration") ?></a></li>
        <?php endif;?>
    </ul>
    <?php
        endif;
    ?>
    <!-- business -->
    <?php
        if($this->params['controller'] == 'sale_customers' || $this->params['controller'] == 'sale_customer_contacts' || $this->params['controller'] == 'sale_settings' || $this->params['controller'] == 'sale_roles' || $this->params['controller'] == 'sale_expenses' || $this->params['controller'] == 'sale_leads' || $this->params['controller'] == 'categories' || $this->params['controller'] == 'easyraps'):
    ?>
    <ul id="sub-nav-business">
        <li class="<?php echo ($this->params['controller'] == 'sale_leads' && ($this->params['action'] == 'index' || $this->params['action'] == 'update')) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_leads/index/") ?>"><?php __("Lead") ?></a></li>
        <li class="<?php echo ($this->params['controller'] == 'sale_leads' && ($this->params['action'] == 'deal' || $this->params['action'] == 'deal_update')) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_leads/deal/") ?>"><?php __("Deal") ?></a></li>
        <li class="<?php echo ($this->params['controller'] == 'sale_customers' && $passBussiness == 'pro') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_customers/index/pro/") ?>"><?php __("Provider") ?></a></li>
        <li class="<?php echo ($this->params['controller'] == 'sale_customers' && $passBussiness == 'cus') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_customers/index/cus/") ?>"><?php __("Customer") ?></a></li>
        <li class="<?php echo ($this->params['controller'] == 'sale_customer_contacts') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_customer_contacts/index/") ?>"><?php __("Contact") ?></a></li-->
        <?php if ($is_sas || $role == "admin" || (!empty($saleRoles) && ($saleRoles == 1))): //|| $saleRoles == 2 : ko co sale manager thay admin?>
        <li class="<?php echo ( $this->params['controller'] == 'categories' || $this->params['controller'] == 'sale_settings' || $this->params['controller'] == 'sale_roles' || $this->params['controller'] == 'sale_expenses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_settings/index/customer_status/") ?>"><?php __("Administration") ?></a></li>
        <?php endif;?>
        <li class="<?php echo ($this->params['controller'] == 'easyraps') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/easyraps/") ?>"><?php __("Easyrap") ?></a></li>
    </ul>
    <?php endif;?>
    <!-- project & opportunity -->
    <?php
    if (!(($this->params['controller'] == 'projects' || $this->params['controller'] == 'projects_preview') && in_array($this->params['action'], array('index', 'opportunity', 'projects_vision', 'map', 'index_plus'))) && (!empty($this->params['pass']) && $this->params['controller'] != 'user_views' && $this->params['controller'] != 'user_views_preview'
            && $this->params['controller'] != 'project_created_values'
            && $this->params['controller'] != 'employees' && $this->params['controller'] != 'absences'
            && $this->params['controller'] != 'response_constraints' && $this->params['controller'] != 'absence_requests'
            && $this->params['controller'] != 'holidays' && $this->params['controller'] != 'activities'
            && $this->params['controller'] != 'activity_columns' && $this->params['controller'] != 'activity_families'
            && $this->params['controller'] != 'activity_forecasts' && $this->params['controller'] != 'contract_types'
            && $this->params['controller'] != 'activity_tasks'
            && $this->params['controller'] != 'activity_budget_internals'
            && $this->params['controller'] != 'activity_budget_externals'
            && $this->params['controller'] != 'activity_budget_sales'
            && $this->params['controller'] != 'activity_budget_synthesis'
            && $this->params['controller'] != 'activity_budget_provisionals'
            && $this->params['controller'] != 'activity_settings'
            && $this->params['controller'] != 'activity_exports'
            && $this->params['controller'] != 'audit_settings'
            && $this->params['controller'] != 'audit_admins'
            && $this->params['controller'] != 'audit_missions'
            && $this->params['controller'] != 'audit_recoms'
            && $this->params['controller'] != 'sale_customers'
            && $this->params['controller'] != 'sale_settings'
            && $this->params['controller'] != 'sale_roles'
            && $this->params['controller'] != 'sale_customer_contacts'
            && $this->params['controller'] != 'sale_leads'
            && $this->params['controller'] != 'sale_expenses'
            && $this->params['controller'] != 'translations'
            && $this->params['controller'] != 'menus'
            && $this->params['controller'] != 'employee_absences' && $this->params['controller'] != 'workdays')) :
    ?>
    	<!-- <div id="carousel" class="flexslider secondary-nav"> -->
            <ul id="sub-nav-project">
	            <?php
	                if((($this->params['controller'] == 'project_tasks' || $this->params['controller'] == 'project_tasks_preview' || $this->params['controller'] == 'kanban') && $this->params['action'] == 'detail')){
	                    if(empty($project_id)){
	                        $id = $this->params['pass'];
	                        $projectTasks = ClassRegistry::init('ProjectTask')->find('first', array('recursive' => -1, 'conditions' => array('ProjectTask.id' => $id[0]), 'fields' => array('project_id')));
	                        $project_id = $projectTasks['ProjectTask']['project_id'];
	                    }
	                }
	                if($this->params['controller'] == 'zog_msgs' || $this->params['controller'] == 'kanban' || $this->params['action'] == 'flash_info'){
	                    $project_id = empty($project_id) ?  $this->params['pass'][0] : $project_id;
	                }

	                if(!empty($menu)){
	                    _se_recursiveMenu2($menu, false, array(
	                        'html' => $this->Html,
	                        'language' => $language,
	                        'project' => $project_id,
	                        'params' => $this->params,
	                        'role' => $role,
	                        'disableMenuBudgetAndFinance' => $menuBudgets,
	                        'resetRole' => $resetRole,
	                        'EPM_see_the_budget' => $EPM_see_the_budget,
	                        'seeBudgetPM' => $seeBudgetPM,
	                        'check_budget_actis' => $check_budget_actis
	                    ), $profile_account);
	                }
	            ?>
	       	</ul>
       	<!-- </div> -->
        <?php if ($this->params['controller'] != 'projects' || $this->params['controller'] != 'projects_preview' ) : ?>
            <?php
            $output = '';
            $titles = array(
                'primary_objectives' => __('Primary Objectives', true),
                'constraint' => __('Constraint', true),
                'remark' => __('Remark', true)
            );
            if(!empty($project_id)){
                $datax = ClassRegistry::getObject('Project')->find('first', array(
                    'recursive' => -1,
                    'fields' => array('primary_objectives', 'constraint', 'remark'),
                    'conditions' => array('id' => $project_id)
                        ));
                if ($datax && ($datax = array_filter($datax['Project']))) {
                    foreach ($datax as $k => $v) {
                        $output .= "<dt><b>{$titles[$k]}</b> : </dt><dd>$v</dd>";
                    }
                    $output = "<dl class='tooltip-pm-details'>$output</dl>";
                }
            }
            ?>
            <?php if (!empty($output)) : ?>
                <script type="text/javascript">
                    (function($){
                        $('.tooltip-pm-details').tooltip({
                            maxHeight : 500,
                            maxWidth : 400,
                            type : ['bottom','left'],
                            content:  <?php echo json_encode($output); ?>});
                    })(jQuery);
                </script>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->params['controller'] != 'project_amrs') : ?>
            <?php
            $output = '';
            $titles = array(
                'project_amr_solution' => __('General Comment', true)
            );
            if(!empty($project_id)){
                $datax = ClassRegistry::getObject('Project')->ProjectAmr->find('first', array(
                    'recursive' => -1,
                    'fields' => array('project_amr_solution'),
                    'conditions' => array('project_id' => $project_id)
                        ));
                if ($datax && ($datax = array_filter($datax['ProjectAmr']))) {
                    foreach ($datax as $k => $v) {
                        $output .= "<dt><b>{$titles[$k]}</b> : </dt><dd>$v</dd>";
                    }
                    $output = "<dl class='tooltip-pm-amrs'>$output</dl>";
                }
            }
            ?>
            <?php if (!empty($output)) : ?>
                <script type="text/javascript">
                    (function($){
                        $('.tooltip-pm-amrs').tooltip({
                            maxHeight : 500,
                            maxWidth : 400,
                            type : ['bottom','right'],
                            content:  <?php echo json_encode($output); ?>});
                    })(jQuery);
                </script>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <!-- absence -->
    <?php
        $canManageResource = $role == 'pm' && $employee_info['CompanyEmployeeReference']['control_resource'];
        $myPC = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('profit_center_id IS NULL'),
                'NOT' => array('profit_center_id' => 0),
                'employee_id' => $employee_id
            )
        ));
        $backupManagers = ClassRegistry::init('ProfitCenterManagerBackup')->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $conds = array(
            'manager_id' => $employee_id,
            'id' => $backupManagers
        );
        if( $canManageResource )$conds['id'][] = $myPC['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
        $profit = ClassRegistry::init('ProfitCenter')->find('first', array(
            'recursive' => -1,
            'conditions' => array('OR' => $conds)));
        $hasManager = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profit));
        if($hasManager){
            $profit = array_shift($profit);
        }
        $hasManagerMyDiary = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profitMyDiary = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id)))));
        if($hasManagerMyDiary){
            $profitMyDiary = array_shift($profitMyDiary);
        }
        $profitMyDiary = !empty($profitMyDiary['profit_center_id']) ? $profitMyDiary['profit_center_id'] : '';
        if(!empty($profit['id'])){
            $profit['id'] = $profit['id'];
        } else {
            $employee_profits = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'fields' => array('employee_id', 'profit_center_id'),
            'conditions' => array(
                'employee_id' => $employee_info['Employee']['id'],
                'AND' => array(
                    'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => null),
                    'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => 0)
                )),
            'group' => array('employee_id')
            ));
            if(!empty($employee_profits)){
                $profit['id'] = $employee_profits['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
            }
        }
        ?>
    <?php
    //get URL for absen available.
    $available_url = ClassRegistry::init('HistoryFilter')->find('first', array(
        'recursive' => -1,
        'conditions' => array(
            'path' => 'available',
            'employee_id' => $employee_id
        )
    ));
    $available_url = !empty($available_url) && !empty($available_url['HistoryFilter']['params']) ? $available_url['HistoryFilter']['params'] : '/absence_requests/available/&pro%5B%5D=' . $profit['id'];
    ?>
    <?php if ($this->params['controller'] == 'employee_absences' || $this->params['controller'] == 'absence_requests') : ?>
        <ul id="sub-nav-absence">
            <li class="<?php echo ($this->params['controller'] == 'employee_absences') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/employee_absences/index/" . @$employee_id . '/' . @$company_id) ?>"><?php __("Absences") ?></a></li>
            <li class="<?php echo ($this->params['controller'] == 'absence_requests' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/") ?>"><?php __("Requests") ?></a></li>
            <li class="<?php echo ($this->params['controller'] == 'absence_requests' && $this->params['action'] == 'review') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/review") ?>"><?php __("Your Absence Review") ?></a></li>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager) : ?>
                <li class="<?php echo ($this->params['controller'] == 'absence_requests' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/manage/?profit=" . $profit['id']) ?>"><?php __("Validation") ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'absence_requests' && $this->params['action'] == 'to_validated') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/manage/year/true/?profit=" .  $profit['id']) ?>"><?php __("To Validate") ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'absence_requests' && $this->params['action'] == 'reviews') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/reviews/?profit=" .  $profit['id']) ?>"><?php __("Absence Reviews") ?></a></li>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'pm' || $hasManager) : ?>
                <li class="<?php echo ($this->params['controller'] == 'absence_requests' && $this->params['action'] == 'available') ? "wd-current" : "" ?>"><a href="<?php echo $available_url ?>"><?php __("Available dashboard") ?></a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
    <!-- person view -->
    <?php if( isset($model) && ( $this->params['controller'] == 'user_views' || $this->params['controller'] == 'user_views_preview') ): ?>
    <ul id = "sub-nav-absence">
        <?php if($is_sas || (!$is_sas && $enablePMS == true && ($role !='conslt'))):?>
        <li class="<?php echo ($model == 'project') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/user_views?model=project") ?>"><?php __("Project") ?></a></li>
        <?php endif; ?>
        <?php if($is_sas || (!$is_sas && $enableRMS == true)):?>
        <li class="<?php echo ($model == 'activity') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/user_views?model=activity") ?>"><?php __("Activity") ?></a></li>
        <?php endif; ?>
        <?php if($is_sas || (!$is_sas && $role !='conslt' && $enableBusines == true)):?>
        <li class="<?php echo ($model == 'business') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/user_views?model=business") ?>"><?php __("Lead") ?></a></li>
        <li class="<?php echo ($model == 'deal') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/user_views?model=deal") ?>"><?php __("Deal") ?></a></li>
        <?php endif; ?>
        <?php if($is_sas || (!$is_sas && $enableTicket == true)):?>
        <li class="<?php echo ($model == 'ticket') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/user_views?model=ticket") ?>"><?php __("Ticket") ?></a></li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
    <!-- activity -->
    <?php
        $profit['id'] = !empty($profit['id']) ? $profit['id'] : -1;
        if(($this->params['controller'] == 'activity_tasks' && $this->params['action'] != 'visions_staffing')
            || ($this->params['controller'] == 'activity_tasks_preview' && $this->params['action'] != 'visions_staffing')
            || $this->params['controller'] == 'activity_budget_internals'
            || $this->params['controller'] == 'activity_budget_externals'
            || $this->params['controller'] == 'activity_budget_provisionals'
            || $this->params['controller'] == 'activity_budget_sales'
            || $this->params['controller'] == 'activity_budget_synthesis'
    ){
     $profitOfBudgetInActivity = ($role == 'admin') ? -1 : $profit['id'];
     ?>
        <ul id="sub-nav-activity">
            <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'request') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/request/") ?>"><?php __("Requests") ?></a></li>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $hasManager) : //($role == 'pm' && $hasManager)?>
                <?php /*<li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manage?profit=" .  $profit['id']) ?>"><?php __("Forecasts") ?></a></li> */ ?>
                <?php if($ACBudgetSettings):?>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'budget') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/budget?profit=" .  $profitOfBudgetInActivity) ?>"><?php __("Budget") ?></a></li>
                <?php endif;?>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'response') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/response?profit=" .  $profit['id']) ?>"><?php __("Validation") ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'manages') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manages?profit=" .  $profit['id']) ?>"><?php __("Forecasts+") ?></a></li>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr') : ?>
                <li class="<?php echo ($this->params['controller'] == 'activities' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/index/" . @$company_id ."/") ?>"><?php __("Management") ?></a></li>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm' || $hasManager) : ?>
                <!--li class="<?php //echo ($this->params['controller'] == 'activities' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php //echo $html->url("/activities/manage/" . @$company_id) ?>"><?php //__("View") ?></a></li-->
                <li class="<?php echo ($activatedViewWhenClickDetail == true) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/manage/" . @$company_id . "/") ?>"><?php __("View") ?></a>
                    <?php if($activatedViewWhenClickDetail == true):?>
                    <ul>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && ($this->params['action'] == 'teams_yes' || $this->params['action'] == 'teams')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/teams/" . @$activity_id) ?>"><?php __("Teams") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && ($this->params['action'] == 'index' || $this->params['action'] == 'import_csv')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/index/" . @$activity_id) ?>"><?php __("Tasks") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && $this->params['action'] == 'visions') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/visions/" . @$activity_id) ?>"><?php __("Staffing+") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && $this->params['action'] == 'dash_board') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/dash_board/" . @$activity_id) ?>"><?php __("DashBoard") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($check_budget_actis_AC == true) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Budget") ?></a>
                            <ul>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_synthesis') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Synthesis") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_sales') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_sales/index/" . @$activity_id) ?>"><?php __("Sales") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_internals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_internals/index/" . @$activity_id) ?>"><?php __("Internal Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_externals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_externals/index/" . @$activity_id) ?>"><?php __("External Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_provisionals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_provisionals/index/" . @$activity_id) ?>"><?php __("Provisional") ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <?php endif;?>
                </li>
            <?php endif; ?>
            <?php if (($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm') && (!empty($activitySettings) && $activitySettings['ActivitySetting']['show_activity_review'] == 1)) : ?>
                <li class="<?php echo ($activatedRevieWhenClickDetail == true) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/review/" . @$company_id . "/") ?>"><?php __("Review") ?></a>
                    <?php if($activatedRevieWhenClickDetail == true):?>
                    <ul>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && ($this->params['action'] == 'teams_yes' || $this->params['action'] == 'teams')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/teams/" . @$activity_id) ?>"><?php __("Teams") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && ($this->params['action'] == 'index' || $this->params['action'] == 'import_csv')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/index/" . @$activity_id) ?>"><?php __("Tasks") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && $this->params['action'] == 'visions') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/visions/" . @$activity_id) ?>"><?php __("Staffing+") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($this->params['controller'] == 'activity_tasks' && $this->params['action'] == 'dash_board') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/dash_board/" . @$activity_id) ?>"><?php __("DashBoard") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($check_budget_actis_AC == true) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Budget") ?></a>
                            <ul>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_synthesis') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Synthesis") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_sales') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_sales/index/" . @$activity_id) ?>"><?php __("Sales") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_internals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_internals/index/" . @$activity_id) ?>"><?php __("Internal Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_externals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_externals/index/" . @$activity_id) ?>"><?php __("External Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($this->params['controller'] == 'activity_budget_provisionals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_provisionals/index/" . @$activity_id) ?>"><?php __("Provisional") ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <?php endif;?>
                </li>
                <li class="<?php echo (($this->params['controller'] == 'activity_tasks' || $this->params['controller'] == 'activity_tasks_preview') && $this->params['action'] == 'visions_staffing')? "wd-current" : "" ?>"><a id="add_vision_staffing_news_menu"><?php __("Vision Staffing+") ?></a></li>
            <?php endif; ?>
            <?php if ((isset($companyConfigs['active_team_workload']) && $companyConfigs['active_team_workload']==1)&&($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($this->params['controller'] == 'team_workloads' && $this->params['action'] == 'index')? "wd-current" : "" ?>"><a id="add_team_workload_news_menu"><?php __("Team Workload") ?></a></li>
            <?php endif;?>
            <?php if ((isset($companyConfigs['active_team_workload_plus']) && $companyConfigs['active_team_workload_plus']==1) && ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($this->params['controller'] == 'team_workloads' && $this->params['action'] == 'plus')? "wd-current" : "" ?>"><a id="add_team_workload_plus"><?php __("Team Workload +") ?></a></li>
            <?php endif; ?>
        </ul>
    <?php } elseif ($activityScreen) {
                $profitOfBudgetInActivity = ($role == 'admin') ? -1 : $profit['id'];
      ?>
        <ul id="sub-nav-project">
            <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'request') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/request/") ?>"><?php __("Requests") ?></a></li>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager) : ?>
                <?php /* <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manage?profit=" .  $profit['id']) ?>"><?php __("Forecasts") ?></a></li> */?>
                <?php if($ACBudgetSettings):?>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'budget') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/budget?profit=" .  $profitOfBudgetInActivity) ?>"><?php __("Budget") ?></a></li>
                <?php endif; ?>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'response') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/response?profit=" .  $profit['id']) ?>"><?php __("Validation") ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'to_validate') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/to_validate?profit=" .  $profit['id']) ?>"><?php __("To Validate") ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'not_sent_yet') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/not_sent_yet?profit=" .  $profit['id']) ?>"><?php __("Not send yet") ?></a></li>
				<?php if(isset($companyConfigs['show_activity_forecast']) && $companyConfigs['show_activity_forecast']){ ?>
					<li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'manages') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manages?profit=" .  $profit['id']) ?>"><?php __("Forecasts+") ?></a></li>
				<?php } ?>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr') : ?>
				<?php if(isset($companyConfigs['show_activity_index']) && $companyConfigs['show_activity_index']): ?>
                <li class="<?php echo ($this->params['controller'] == 'activities' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/index/" . @$company_id . "/") ?>"><?php __("Management") ?></a></li>
            <?php endif; endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm' || $hasManager) : ?>
				<?php if(isset($companyConfigs['show_activity_view']) && $companyConfigs['show_activity_view']): ?>
					<li class="<?php echo ($this->params['controller'] == 'activities' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/manage/" . @$company_id . "/") ?>"><?php __("View") ?></a></li>
            <?php endif; endif;?>

            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm') : ?>
                <?php if(!empty($activitySettings) && $activitySettings['ActivitySetting']['show_activity_review'] == 1):?>
                    <li class="<?php echo (($this->params['controller'] == 'activities' && ($this->params['action'] == 'review' || $this->params['action'] == 'detail'))) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/review/" . @$company_id . "/") ?>"><?php __("Review") ?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm' || $hasManager) : ?>
                <li class="<?php echo (($this->params['controller'] == 'activity_tasks' || $this->params['controller'] == 'activity_tasks_preview') && $this->params['action'] == 'visions_staffing')? "wd-current" : "" ?>"><a id="add_vision_staffing_news_menu"><?php __("Vision Staffing+") ?></a></li>
            <?php endif; ?>
            <?php if ((isset($companyConfigs['active_team_workload'])&&$companyConfigs['active_team_workload']==1)&&($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($this->params['controller'] == 'team_workloads' && $this->params['action'] == 'index')? "wd-current" : "" ?>"><a id="add_team_workload_news_menu"><?php __("Team Workload") ?></a></li>
            <?php endif; ?>
            <?php if ((isset($companyConfigs['active_team_workload_plus']) && $companyConfigs['active_team_workload_plus']==1) && ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($this->params['controller'] == 'team_workloads' && $this->params['action'] == 'plus')? "wd-current" : "" ?>"><a id="add_team_workload_plus"><?php __("Team Workload +") ?></a></li>
            <?php endif; ?>
            <?php
                if( $role == 'admin' ): ?>
				<?php if(isset($companyConfigs) && isset($companyConfigs['show_activity_export_excel']) && $companyConfigs['show_activity_export_excel'] == 1):?>
                <li>
                    <a href="javascript:;" id="show-export-dialog" title=""><?php __('Export') ?></a>
                </li>
            <?php endif; endif; ?>
        </ul>
    <?php } ?>
    <div style="clear: both"></div>
</div>
<?php
if($isMobile || $isTablet)
{ ?>
<style>
#wd-container-header-main #wd-container-header h1.wd-logo a{
    margin-top:-5px !important;

}
/*#wd-top-nav{
    min-height:46px;
    padding-left:0 !important;
    background: url(<?php //echo $this->Html->url('/img/front/bg-nav-mobile.png') ?>) left 21px repeat-x;
}
#wd-top-nav ul{
    max-width:1024px;
}
#wd-top-nav > ul > li > ul{
    min-width:768px;
    max-width:1024px ;
}
#wd-top-nav ul li ul li{
    margin-bottom:5px;
}*/
@media only screen and (max-device-width: 1024px) {
    #wd-top-nav ul ul ul {
        z-index: 99999999;
        min-width: 120px !important;
        width: auto;
        left: 0 !important;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    }
    #wd-top-nav ul ul ul > li {
        float: none;
        display: block;
        margin: 0;
        padding: 5px;
    }
    #wd-top-nav ul ul ul > li a {
        float: none;
    }
}
</style>
<script>
var menuHeight = $('#wd-top-nav ul li ul').height();
if(menuHeight > 30) {
    $('#wd-top-nav').css({'height':'70px'});
}
</script>
<?php } ?>
<!-- dialog_vision_staffing++++++++++++++++++ -->
<?php
    // list cac controler se hien thi vision staffing+ o activity.
    $displayVisions = array(
        'activity_forecasts', 'activities', 'activity_tasks', 'activity_budget_synthesis', 'activity_budget_sales',
        'activity_budget_internals', 'activity_budget_externals', 'activity_budget_provisionals', 'team_workloads'
    );
    if(in_array($this->params['controller'], $displayVisions)):
    /**
     * Vision staffing+: code cho phan dialog
     */
    $menuListFamilies = ClassRegistry::init('Family')->find('list', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employee_info['Company']['id'],'parent_id'=>null)
                        ));
    $PCModel = ClassRegistry::init('ProfitCenter');
    $menuListProfitCenters = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']),null,null,' -- ',-1);
    //END
    // lay profit center cho team_workloads.
    $backupManagers = ClassRegistry::init('ProfitCenterManagerBackup')->find('list', array(
        'recursive' => -1,
        'conditions' => array('employee_id' => $employee_id),
        'fields' => array('profit_center_id', 'profit_center_id')
    ));
    $team_wl_profit = ClassRegistry::init('ProfitCenter')->generateTreeList(array(
        'OR' => array(
           'manager_id' => $employee_id,
           'id' => $backupManagers
       )
    ), null,null,'-- ',-1);
    $pcOfTeamWorkload = ($role != 'admin') ? $team_wl_profit : $menuListProfitCenters;
    // end
    $menuListEmployees = ClassRegistry::init('CompanyEmployeeReference')->find('all', array(
        'conditions' => array(
            'CompanyEmployeeReference.company_id' => $employee_info['Company']['id'],
            'NOT' => array('Employee.is_sas' => 1)
        ),
        'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
    ));
    $menuListEmployees = !empty($menuListEmployees) ? Set::combine($menuListEmployees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
    $menuListBudgetCustomers = ClassRegistry::init('BudgetCustomer')->find('list', array(
        'recursive' => -1,
        'conditions' => array('company_id' => $employee_info['Company']['id'])
    ));
?>
<!-- Export dialog for Activity Screen - Similar to dialog vision staffing below -->
<div id="activity-export-dialog" style="display: none" class="buttons" title="Export">
    <fieldset>
    <?php echo $this->Form->create('ActivityExport', array('type' => 'GET', 'id' => 'form-activity-export', 'url' => array('controller' => 'activity_forecasts', 'action' => 'exportActivityFollowEmployee'))); ?>
    <div class="wd-scroll-form">
        <div class="wd-left-content">
            <div class="wd-input">
                <label for=""><?php __('Start Date') ?></label>
                <?php echo $this->Form->input('start_date', array(
                    'div' => false,
                    'id' => 'export-start-date',
                    'class' => 'export-datepicker',
                    'value' => date('d-m-Y'),
                    'label' => false,
                    'readonly' => true
                )) ?>
            </div>
            <div class="wd-input">
                <label for=""><?php __('End Date') ?></label>
                <?php echo $this->Form->input('end_date', array(
                    'div' => false,
                    'id' => 'export-end-date',
                    'class' => 'export-datepicker',
                    'value' => date('d-m-Y', time() + 86400 * 7),
                    'label' => false,
                    'readonly' => true
                )) ?>
            </div>
            <div class="wd-input" id="export-profitCenter">
                <label><?php __("Profit Center") ?></label>
                <?php
                echo $this->Form->input('profit_center', array(
                    'type' => 'select',
                    'name' => 'profit_center_e',
                    'id' => 'export-list-profits',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'style' => 'width:69% !important',
                    "empty" => __("-- Any --", true),
                    "options" => !empty($menuListProfitCenters) ? $menuListProfitCenters : array(),
                    'selected' => isset($arrGetUrl['aPC']) ? $arrGetUrl['aPC'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="export-employee">
                <label><?php __("Employee") ?></label>
                <?php
                if(isset($employeeRorProfitCenterList)) $menuListEmployees=$employeeRorProfitCenterList;
                echo $this->Form->input('employee', array(
                    'type' => 'select',
                    'name' => 'employee_e',
                    'id' => 'export-list-employees',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'style' => 'width:69% !important',
                    "empty" => __("-- Any --", true),
                    "options" => array(),
                    //'selected' => isset($arrGetUrl['aEmployee']) ? $arrGetUrl['aEmployee'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_dayoff">
                <label for="project-manager"><?php __("DAY-OFF") ?></label>
                <?php
                    echo $this->Form->input('activated', array(
                    'type' => 'select',
                    'name' => 'dayOff',
                    'id' => 'export-list-dayOff',
                    'div' => false,
                    'label' => false,
                    'hiddenField' => false,
                    'style' => 'width:63% !important',
                    "options" => array(0 => 'No',1 => 'Yes')
                    ));
                ?>
            </div>
            <div class="wd-input" id="file_merge">
                <label for="project-manager"><?php __("Merge") ?></label>
                <?php
                    echo $this->Form->input('merge', array(
                    'type' => 'select',
                    'name' => 'merge',
                    'id' => 'export-list-merge',
                    'div' => false,
                    'label' => false,
                    'hiddenField' => false,
                    'style' => 'width:63% !important',
                    "options" => array(0 => 'No',1 => 'Yes')
                    ));
                ?>
            </div>
            <div class="wd-input" id="file_merge">
                <label for="export-display"><?php __("Display 0") ?></label>
                <?php
                    echo $this->Form->input('display', array(
                    'type' => 'select',
                    'name' => 'display',
                    'id' => 'export-display',
                    'div' => false,
                    'label' => false,
                    'hiddenField' => false,
                    'style' => 'width:63% !important',
                    "options" => array(0 => 'No',1 => 'Yes')
                    ));
                ?>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel" id="export-cancel-dialog"></a></li>
        <li><a href="javascript:void(0)" class="new" id="export-submit-dialog"></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="export-reset_sum"></a></li>
    </ul>
</div>
<div id="overlay-container" rels="1" style="display: none">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
<style>
.dateByDay .ui-state-default, .ui-widget-header .ui-state-default{
    border: none !important;
}
</style>
<!-- Export dialog for Activity Screen - Similar to dialog vision staffing below -->
<div id="activity-export-message-dialog" style="display: none" class="buttons" title="Report">
    <table id="message_exporting">
        <tr>
            <td><?php __("Exporting...") ?></td>
            <td><span id="fileExport">0</span>/<span id="totalFile">0</span></td>
        </tr>
        <tr>
            <td><?php __("Total File") ?></td>
            <td id="totalFiles">...</td>
        </tr>
        <tr>
            <td><?php __("Total Resources") ?></td>
            <td id="totalEmploys">...</td>
        </tr>
        <tr>
            <td><?php __("Total Record") ?></td>
            <td id="totalRecords">...</td>
        </tr>
    </table>
    <div style="clear: both;"></div>
    <p id="exporting_wait">Please wait, writing files...</p>
    <ul id="exporting_buttons" class="type_buttons" style="padding-right: 20px !important; display: none;">
        <li><a href="javascript:void(0)" class="cancel" id="export-report-cancel-dialog"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="down" id="export-report-download-dialog"><?php __("Download") ?></a></li>
    </ul>
</div>
<?php
    $dateType = isset($arrGetUrl['aDateType']) ? $arrGetUrl['aDateType'] : 3 ;
?>
<div id="dialog_vision_staffing_news_menu" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Activity', array('type' => 'GET', 'id' => 'form_vision_staffing_news_menu', 'target' => '_blank', 'url' => array('controller' => 'activity_tasks', 'action' => 'visions_staffing'))); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-left-content">
                <fieldset class="fieldset">
                    <!-- <legend class="legend"><?php __('Visibility settings'); ?></legend> -->
                    <div class="wd-input">
                        <label for="status"><?php __('Show staffing by'); ?></label>
                        <div id="group-showby">
                            <?php
                            if(isset($arrGetUrl['type'])) $dataType=$arrGetUrl['type'];
                            else $dataType=0;
                            echo $this->Form->radio('project_staffing_id', array(0 => __("Activity", true)), array(
                                'name' => 'type',
                                'fieldset' => false,
                                'legend' => false,
                                'rel' => 'no-history',
                                'value' => $dataType)
                            );
                            ?>
                            <?php
                            echo $this->Form->radio('project_staffing_id', array(1 => __("Profit center", true)), array(
                                'name' => 'type',
                                'fieldset' => false,
                                'legend' => false,
                                'rel' => 'no-history',
                                'value' => $dataType)
                            );
                            ?>
                            <?php
                            //Change to Profile, version date : PMS - 17/6/2015 - Enhancement vision staffing +
                            if(isset($companyConfigs['activate_profile']) && $companyConfigs['activate_profile']) {
                                echo $this->Form->radio('project_staffing_id', array(5 => __("Profile", true)), array(
                                    'name' => 'type',
                                    'fieldset' => false,
                                    'legend' => false,
                                    'rel' => 'no-history',
                                    'value' => $dataType)
                                );
                            }
                            ?>
                        </div>
                    </div>
                    <div class="wd-input">
                        <label for="status"><?php __('Show Summary'); ?></label>
                        <div id="show_summary">
                            <?php
                            if(isset($arrGetUrl['summary'])) $dataType=$arrGetUrl['summary'];
                            else $dataType=0;
                            echo $this->Form->radio('project_summary_id', array(__("No", true), __("Yes", true)), array(
                                'name' => 'summary',
                                'fieldset' => false,
                                'legend' => false,
                                'rel' => 'no-history',
                                'value' => $dataType));
                            ?>
                            <?php
                            // echo $this->Form->radio('project_summary_id', array(99 => __("Only Summary", true)), array(
                            // 	'name' => 'summary',
                            // 	'fieldset' => false,
                            // 	'legend' => false,
                            // 	'value' => $dataType));
                            ?>
                        </div>
                    </div>
                    <div class="wd-input">
                        <label for="status"><?php __('Not Affected') ?></label>
                        <div class="is-check-file">
                            <?php
                            echo $this->Form->radio('show_na', array(__("No", true), __("Yes", true)), array(
                                'name' => 'show_na',
                                'rel' => 'no-history',
                                'fieldset' => false,
                                'legend' => false,
                                'value' => 1)
                            );
                            ?>
                        </div>
                    </div>
                </fieldset>
            </div>
            <fieldset class="fieldset" style="margin-top: 10px;">
             <div class="wd-input" style="margin-top: 10px;">
                <label for="status"><?php __('Show by'); ?></label>
                <div id="group-date-type">
                    <?php
                        // echo $this->Form->radio('ActivityDateType', array(1 => __("Day", true)), array(
                        // 	'name' => 'aDateType',
                        // 	'fieldset' => false,
                        // 	'legend' => false,
                        // 	'rel' => 'no-history',
                        // 	'style' => 'display: none',
                        // 	'checked' => $dateType == 1 ? 'checked' : '',
                        // 	'value' => $dateType));
                        ?>
                        <?php
                        // echo $this->Form->radio('ActivityDateType', array(2 => __("Week", true)), array(
                        // 	'name' => 'aDateType',
                        // 	'fieldset' => false,
                        // 	'legend' => false,
                        // 	'rel' => 'no-history',
                        // 	'checked' => $dateType == 2 ? 'checked' : '',
                        // 	'value' => $dateType));
                        ?>
                        <?php
                        echo $this->Form->radio('aDateType', array(3 => __("Month", true)), array(
                            'name' => 'aDateType',
                            'fieldset' => false,
                            'legend' => false,
                            'rel' => 'no-history',
                            'checked' => $dateType == 3 ? 'checked' : '',
                            'value' => $dateType)
                        );
                    ?>
                 </div>
            </div>
            <?php
            if($dateType == 1 || $dateType == 2) {
            ?>
            <style>
            .dateByDay{ display:block; }
            .dateNotByDay{ display:none; }
            </style>
            <?php
            } else {
            ?>
            <style>
            .dateByDay{ display:none; }
            .dateNotByDay{ display:block; }
            </style>
            <?php } ?>
            <div class="dateByDay wd-input" style="margin-top: 10px; padding-left:105px;">
            <?php
                echo $this->Form->input(__('From', true), array(
                    'empty' => false,
                    'rel' => 'no-history',
                    'class' => 'activity-datepicker',
                    'value' => isset($arrGetUrl['aStartDate']) ? $arrGetUrl['aStartDate'] : '',
                    'id' => 'aStartDate',
                    'name' => 'aStartDate',
                    'style' => 'width: 101px;'
                ));
                echo $this->Form->input(__('To', true), array(
                    'empty' => false,
                    'rel' => 'no-history',
                    'class' => 'activity-datepicker',
                    'value' => isset($arrGetUrl['aEndDate']) ? $arrGetUrl['aEndDate'] : '',
                    'id' => 'aEndDate',
                    'name' => 'aEndDate',
                    'style' => 'width: 101px;'
                ));
            ?>
            </div>
            <div class="dateNotByDay wd-input" style="margin-top: 10px;" id="fromDate">
                <label for="status"><?php __('From'); ?></label>
                    <?php
                        if(isset($arrGetUrl['aStartMonth'])) $smonth=$arrGetUrl['aStartMonth'];
                        else $smonth= !empty($_start) ? date('m', $_start) : date('m', time());
                        if(isset($arrGetUrl['aStartYear'])) $syear=$arrGetUrl['aStartYear'];
                        else $syear= !empty($_start) ? date('Y', $_start) : date('Y', time());
                        $_start = !empty($_start) ? $_start : time();
                        echo $this->Form->month('smonth', $smonth, array(
                            'empty' => false,
                            'rel' => 'no-history',
                            'id' => 'aStartMonth',
                            'name' => 'aStartMonth',
                            'style' => 'width: 101px;'
                        ));
                        echo $this->Form->year('syear', date('Y', $_start) - 10, date('Y', $_start) + 10, $syear, array(
                            'empty' => false,
                            'rel' => 'no-history',
                            'id' => 'aStartYear',
                            'name' => 'aStartYear',
                            'style' => 'width: 77px; margin-left: 5px;'
                        ));
                ?>
            </div>
            <div class="dateNotByDay wd-input" style="margin-top: 10px;">
                <label for="status"><?php __('To'); ?></label>
                    <?php
                        if(isset($arrGetUrl['aEndMonth'])) $emonth=$arrGetUrl['aEndMonth'];
                        else $emonth= !empty($_start) ? date('m', $_start) : date('m', time());
                        if(isset($arrGetUrl['aEndYear'])) $eyear=$arrGetUrl['aEndYear'];
                        else $eyear=  !empty($_start) ? date('Y', $_start) : date('Y', time());
                        $_end = !empty($_end) ? $_end : time();
                        echo $this->Form->month('emonth', $emonth, array(
                            'empty' => false,
                            'rel' => 'no-history',
                            'id' => 'aEndMonth',
                            'name' => 'aEndMonth',
                            'style' => 'width: 101px;'
                        ));
                        echo $this->Form->year('eyear', date('Y', $_end) - 10, date('Y', $_end) + 10, $eyear, array(
                            'empty' => false,
                            'rel' => 'no-history',
                            'id' => 'aEndYear',
                            'name' => 'aEndYear',
                            'style' => 'width: 77px; margin-left: 5px;'
                        ));
                    ?>
            </div>
           </fieldset>
            <div class="wd-input" id="filter_activated" style="overflow: visible">
                <label for=""><?php __("Activated") ?></label>
                <?php
                    echo $this->Form->input('activated', array(
                    'type' => 'select',
                    'name' => 'aActivated',
                    'id' => 'aActivated',
                    'div' => false,
                    'label' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    "empty" => false,
                    'style' => 'width: 300px !important',
                    "options" => array(0 => 'No',1 => 'Yes'),
                    'selected'=>isset($arrGetUrl['aActivated']) ? $arrGetUrl['aActivated'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_activityName" style="overflow: visible">
                <label for=""><?php __("Activity Name") ?></label>
                <?php
                echo $this->Form->input('activity_name', array('div' => false, 'label' => false,
                    "empty" => false,
                    'name' => 'aName',
                    'id' => 'aName',
                    'multiple' => true,
                    'hiddenField' => false,
                    'style' => 'width: 300px !important',
                    "options" => isset($activityFilterList) ? $activityFilterList : array(),
                    'selected'=> isset($arrGetUrl['aName']) ? $arrGetUrl['aName'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_priority" style="overflow: visible">
                <label for=""><?php __("Priority") ?></label>
                <?php
                $priorities = ClassRegistry::init('ProjectPriority')->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employee_info['Company']['id']
                    ),
                    'fields' => array('id', 'priority')
                ));
                $selected = isset($this->params['url']['priority']) ? explode(',', $this->params['url']['priority']) : array();
                    echo $this->Form->input('priority', array(
                    'type' => 'select',
                    'name' => 'priority',
                    'id' => 'StaffingPriority',
                    'div' => false,
                    'label' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'rel' => 'no-history',
                    "empty" => false,
                    'style' => 'width: 300px !important',
                    "options" => $priorities,
                    'selected' => $selected
                    ));
                    echo $this->Form->hidden('asd', array('name' => 'priority', 'id' => 'hiddenPriority'));
                ?>
            </div>
            <div class="wd-input" id="filter_family" style="overflow: visible">
                <label for=""><?php __("Family") ?></label>
                <?php
                echo $this->Form->input('family', array(
                    'type' => 'select',
                    'name' => 'aFamily',
                    'id' => 'aFamily',
                    'rel' => 'no-history',
                    'div' => false,
                    'label' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    "empty" => false,
                    'style' => 'width: 300px !important',
                    "options" => $menuListFamilies,
                    'selected' => isset($arrGetUrl['aFamily']) ? $arrGetUrl['aFamily'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_subFamily" style="overflow: visible">
                <label for=""><?php __("Sub Family") ?></label>
                <?php
                echo $this->Form->input('sous_family', array(
                    'type' => 'select',
                    'name' => 'aSub',
                    'id' => 'aSub',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'rel' => 'no-history',
                    'style' => 'width: 300px !important',
                    "empty" => false,
                    "options" => isset($subFamilyList) ? $subFamilyList : array(),
                    'selected' => isset($arrGetUrl['aSub']) ? $arrGetUrl['aSub'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_profitCenter" style="overflow: visible">
                <label for=""><?php __("Profit Center") ?></label>
                <?php
                echo $this->Form->input('profit_center', array(
                    'type' => 'select',
                    'name' => 'aPC',
                    'id' => 'aPC',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'rel' => 'no-history',
                    'style' => 'width: 300px !important',
                    "empty" => false,
                    "options" => !empty($menuListProfitCenters) ? $menuListProfitCenters : array(),
                    'selected' => isset($arrGetUrl['aPC']) ? $arrGetUrl['aPC'] : array()
                    ));
                echo $this->Form->hidden('selectPCAll', array('value' => 'true'));
                ?>
            </div>
            <div class="wd-input" id="filter_employee" style="overflow: visible">
                <label for=""><?php __("Employee") ?></label>
                <?php
                if(isset($employeeRorProfitCenterList)) $menuListEmployees=$employeeRorProfitCenterList;
                echo $this->Form->input('employee', array(
                    'type' => 'select',
                    'name' => 'aEmployee',
                    'id' => 'aEmployee',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'rel' => 'no-history',
                    'style' => 'width: 300px !important',
                    "empty" => false,
                    "options" => !empty($menuListEmployees) ? $menuListEmployees : array(),
                    'selected' => isset($arrGetUrl['aEmployee']) ? $arrGetUrl['aEmployee'] : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_budgetCustomer" style="overflow: visible">
                <label for=""><?php __("Customer") ?></label>
                <?php
                echo $this->Form->input('budget_customer', array(
                    'type' => 'select',
                    'name' => 'aCustomer',
                    'id' => 'aCustomer',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'rel' => 'no-history',
                    'style' => 'width: 300px !important',
                    "empty" => false,
                    "options" => !empty($menuListBudgetCustomers) ? $menuListBudgetCustomers : array(),
                    'selected' => isset($arrGetUrl['aCustomer']) ? $arrGetUrl['aCustomer'] : array()
                    ));
                ?>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_sum"></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_sum"></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_export_file" style="display: none;"></a></li>
    </ul>
</div>
<div id="dialog_team_workload_news_menu" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Activity', array('type' => 'GET', 'id' => 'form_team_workload_news_menu', 'url' => array('controller' => 'team_workloads', 'action' => 'index'))); ?>
        <div class="dateNotByDay wd-input" style="margin-top: 10px;" id="fromDate_team">
            <label for="status"><?php __('From'); ?></label>
                <?php
                    // if(isset($arrGetUrl['smonth'])) $smonth=$arrGetUrl['smonth'];
                    // else $smonth= !empty($_start) ? date('m', $_start) : date('m', time());
                    // if(isset($arrGetUrl['syear'])) $syear=$arrGetUrl['syear'];
                    // else $syear= !empty($_start) ? date('Y', $_start) : date('Y', time());
                    // $_start = !empty($_start) ? $_start : time();
                    echo $this->Form->month('smonth', $smonth, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamStartMonth',
                        'name' => 'smonth',
                        'style' => 'width: 101px;'
                    ));
                    echo $this->Form->year('syear', date('Y', $_start) - 10, date('Y', $_start) + 10, $syear, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamStartYear',
                        'name' => 'syear',
                        'style' => 'width: 77px; margin-left: 5px;'
                    ));
            ?>
        </div>
        <div class="dateNotByDay wd-input" style="margin-top: 10px;" id="toDate_team">
            <label for="status"><?php __('To'); ?></label>
                <?php
                    // if(isset($arrGetUrl['emonth'])) $emonth=$arrGetUrl['emonth'];
                    // else $emonth= !empty($_start) ? date('m', $_start) : date('m', time());
                    // if(isset($arrGetUrl['eyear'])) $eyear=$arrGetUrl['eyear'];
                    // else $eyear=  !empty($_start) ? date('Y', $_start) : date('Y', time());
                    // $_end = !empty($_end) ? $_end : time();
                    echo $this->Form->month('emonth', $emonth, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamEndMonth',
                        'name' => 'emonth',
                        'style' => 'width: 101px;'
                    ));
                    echo $this->Form->year('eyear', date('Y', $_end) - 10, date('Y', $_end) + 10, $eyear, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamEndYear',
                        'name' => 'eyear',
                        'style' => 'width: 77px; margin-left: 5px;'
                    ));
                ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Team") ?></label>
            <?php
            echo $this->Form->input('team', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'team',
                'id' => 'aTeam',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($pcOfTeamWorkload) ? $pcOfTeamWorkload : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="filter_priority_team" style="overflow: visible">
            <label for=""><?php __("Priority") ?></label>
            <?php
                echo $this->Form->input('teampriority', array(
                'type' => 'select',
                'name' => 'teampriority',
                'id' => 'teamPriority',
                'div' => false,
                'label' => false,
                'multiple' => true,
                'hiddenField' => false,
                'rel' => 'no-history',
                "empty" => false,
                'style' => 'width: 300px !important',
                "options" => $priorities,
                ));
            ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_sum_team"><?php __('OK') ?></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_sum_team"><?php __('RESET') ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_export_file_team" style="display: none;"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="dialog_team_workload_plus" class="buttons" style="display: none; width: 500px">
    <fieldset>
        <?php echo $this->Form->create('Activity', array('type' => 'GET', 'id' => 'form_team_workload_plus', 'url' => array('controller' => 'team_workloads', 'action' => 'plus'))); ?>
        <div class="dateNotByDay wd-input" style="margin-top: 10px;" id="fromDate_team">
            <label for="status"><?php __('From'); ?></label>
                <?php
                    echo $this->Form->month('spmonth', $smonth, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamPSm',
                        'name' => 'spmonth',
                        'style' => 'width: 101px;'
                    ));
                    echo $this->Form->year('spyear', date('Y', $_start) - 10, date('Y', $_start) + 10, $syear, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamPSy',
                        'name' => 'spyear',
                        'style' => 'width: 77px; margin-left: 5px;'
                    ));
                ?>
        </div>
        <div class="dateNotByDay wd-input" style="margin-top: 10px;" id="toDate_team">
            <label for="status"><?php __('To'); ?></label>
                <?php
                    echo $this->Form->month('epmonth', $emonth, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamPEm',
                        'name' => 'epmonth',
                        'style' => 'width: 101px;'
                    ));
                    echo $this->Form->year('epyear', date('Y', $_end) - 10, date('Y', $_end) + 10, $eyear, array(
                        'empty' => false,
                        'rel' => 'no-history',
                        'id' => 'teamPEy',
                        'name' => 'epyear',
                        'style' => 'width: 77px; margin-left: 5px;'
                    ));
                ?>
        </div>
        <div class="wd-input" id="filter_team_plus" style="overflow: visible">
            <label for=""><?php __("Profit Center") ?></label>
            <?php
            echo $this->Form->input('teamP', array(
                'type' => 'select',
                'name' => 'teamP',
                'id' => 'teamP',
                'div' => false,
                'multiple' => true,
                'hiddenField' => false,
                'label' => false,
                'rel' => 'no-history',
                'style' => 'width: 300px !important',
                "empty" => false,
                "options" => !empty($menuListProfitCenters) ? $menuListProfitCenters : array(),
                'selected' => isset($arrGetUrl['teamP']) ? $arrGetUrl['teamP'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="filter_priority_team_plus" style="overflow: visible">
            <label for=""><?php __("Priority") ?></label>
            <?php
                echo $this->Form->input('teamPrio', array(
                'type' => 'select',
                'name' => 'teamPrio',
                'id' => 'teamPrio',
                'div' => false,
                'label' => false,
                'multiple' => true,
                'hiddenField' => false,
                'rel' => 'no-history',
                "empty" => false,
                'style' => 'width: 300px !important',
                "options" => $priorities,
                ));
            ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_sum_team_plus"></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_sum_team_plus"></a></li>
    </ul>
</div>
<div id="dialogVisionCalled" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Activity', array('type' => 'GET', 'id' => 'formVisionNew', 'target' => '_blank', 'url' => array('controller' => 'vision_staffings', 'action' => 'index'))); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-left-content">
                <fieldset class="fieldset">
                    <div class="wd-input">
                        <label for="status"><?php __('Show staffing by'); ?></label>
                        <div id="group-showby1">
                            <?php
                            $vsTypes = array(
                                'ac' => __("Activity", true),
                                'pc' => __("Profit center", true)
                            );
                            if(isset($companyConfigs['activate_profile']) && $companyConfigs['activate_profile']){
                                $vsTypes = array(
                                    'ac' => __("Activity", true),
                                    'pc' => __("Profit center", true),
                                    'pr' => __("Profile", true)
                                );
                            }
                            echo $this->Form->radio('project_staffing_id', $vsTypes, array(
                                'name' => 'vsType',
                                'fieldset' => false,
                                'legend' => false));
                            ?>
                        </div>
                    </div>
                    <div class="wd-input">
                        <label for="status"><?php __('Show Summary'); ?></label>
                        <div id="show_summary">
                            <?php
                            echo $this->Form->radio('project_summary_id', array('no' => __("No", true), 'yes' => __("Yes", true)), array(
                                'name' => 'vsSummary',
                                'fieldset' => false,
                                'legend' => false));
                            ?>
                        </div>
                    </div>
                </fieldset>
            </div>
            <fieldset class="fieldset" style="margin-top: 10px;">
             <div class="wd-input" style="margin-top: 10px;">
                <label for="status"><?php __('Show by'); ?></label>
                <div id="group-showby1">
                    <?php
                        // echo $this->Form->radio('ActivityDateType', array('day' => __("Day", true)), array(
                        // 	'name' => 'vsShowBy',
                        // 	'fieldset' => false,
                        // 	'legend' => false,
                        // 	//'checked' => $dateType == 1 ? 'checked' : '',
                        // 	'value' => $dateType));
                        ?>
                        <?php
                        // echo $this->Form->radio('ActivityDateType', array('week' => __("Week", true)), array(
                        // 	'name' => 'vsShowBy',
                        // 	'fieldset' => false,
                        // 	'legend' => false,
                        // 	//'checked' => $dateType == 2 ? 'checked' : '',
                        // 	'value' => $dateType));
                        ?>
                        <?php
                        echo $this->Form->radio('aDateType', array('month' => __("Month", true)), array(
                            'name' => 'vsShowBy',
                            'fieldset' => false,
                            'legend' => false,
                            //'checked' => $dateType == 3 ? 'checked' : '',
                            'value' => $dateType));
                    ?>
                 </div>
            </div>
            <?php
            if($dateType == 1 || $dateType == 2) {
                ?>
                <style>
                .dateByDay{ display:block; }
                .dateNotByDay{ display:none; }
                </style>
                <?php
            } else {
                ?>
                <style>
                .dateByDay{ display:none; }
                .dateNotByDay{ display:block; }
                </style>
                <?php
            }
            ?>
            <div class="dateByDay wd-input" style="margin-top: 10px; padding-left:105px;">
            <?php
                echo $this->Form->input(__('From', true), array(
                    'empty' => false,
                    'class' => 'activity-datepicker',
                    'id' => 'vsFrom',
                    'name' => 'vsFrom',
                    'style' => 'width: 101px;'
                ));

                echo $this->Form->input(__('To', true), array(
                    'empty' => false,
                    'class' => 'activity-datepicker',
                    'id' => 'vsTo',
                    'name' => 'vsTo',
                    'style' => 'width: 101px;'
                ));
            ?>
            </div>
            <div class="dateNotByDay wd-input" style="margin-top: 10px;" id="fromDate">
                <label for="status"><?php __('From'); ?></label>
                    <?php
                        $smonth= !empty($_start) ? date('m', $_start) : date('m', time());
                        $syear= !empty($_start) ? date('Y', $_start) : date('Y', time());
                        $_start = !empty($_start) ? $_start : time();
                        echo $this->Form->month('smonth', $smonth, array(
                            'empty' => false,
                            'id' => 'vsFromMonth',
                            'name' => 'vsFromMonth',
                            'style' => 'width: 101px;'
                        ));
                        echo $this->Form->year('syear', date('Y', $_start) - 10, date('Y', $_start) + 10, $syear, array(
                            'empty' => false,
                            'id' => 'vsFromYear',
                            'name' => 'vsFromYear',
                            'style' => 'width: 77px; margin-left: 5px;'
                        ));
                    ?>
            </div>
            <div class="dateNotByDay wd-input" style="margin-top: 10px;">
                <label for="status"><?php __('To'); ?></label>
                    <?php
                        $emonth= !empty($_start) ? date('m', $_start) : date('m', time());
                        $eyear=  !empty($_start) ? date('Y', $_start) : date('Y', time());
                        $_end = !empty($_end) ? $_end : time();
                        echo $this->Form->month('emonth', $emonth, array(
                            'empty' => false,
                            'id' => 'vsToMonth',
                            'name' => 'vsToMonth',
                            'style' => 'width: 101px;'
                        ));
                        echo $this->Form->year('eyear', date('Y', $_end) - 10, date('Y', $_end) + 10, $eyear, array(
                            'empty' => false,
                            'id' => 'vsToYear',
                            'name' => 'vsToYear',
                            'style' => 'width: 77px; margin-left: 5px;'
                        ));
                    ?>
            </div>
           </fieldset>
            <div class="wd-input" id="filter_activated1">
                <label for="project-manager"><?php __("Activated") ?></label>
                <?php
                    echo $this->Form->input('activated', array(
                        'type' => 'select',
                        'name' => 'vsActivated',
                        'id' => 'vsActivated',
                        'div' => false,
                        'label' => false,
                        'multiple' => true,
                        'hiddenField' => false,
                        "empty" => __("-- Any --", true),
                        'style' => 'width:69% !important',
                        "options" => array(0 => 'No',1 => 'Yes')
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_activityName1">
                <label for="program"><?php __("Activity Name") ?></label>
                <?php
                echo $this->Form->input('activity_name', array('div' => false, 'label' => false,
                        "empty" => __("-- Any --", true),
                        'name' => 'vsAcName',
                        'id' => 'vsAcName',
                        'multiple' => true,
                        'hiddenField' => false,
                        "options" => isset($activityFilterList) ? $activityFilterList : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_family1">
                <label for="project-manager"><?php __("Family") ?></label>
                <?php
                echo $this->Form->input('family', array(
                        'type' => 'select',
                        'name' => 'vsFamily',
                        'id' => 'vsFamily',
                        'div' => false,
                        'label' => false,
                        'multiple' => true,
                        'hiddenField' => false,
                        "empty" => __("-- Any --", true),
                        'style' => 'width:69% !important',
                        "options" => $menuListFamilies
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_subFamily1">
                <label for="status"><?php __("Sub Family") ?></label>
                <?php
                echo $this->Form->input('sous_family', array(
                        'type' => 'select',
                        'name' => 'vsSubFamily',
                        'id' => 'vsSubFamily',
                        'div' => false,
                        'multiple' => true,
                        'hiddenField' => false,
                        'label' => false,
                        'style' => 'width:69% !important',
                        "empty" => __("-- Any --", true),
                        "options" => isset($subFamilyList) ? $subFamilyList : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_profitCenter1">
                <label for="status"><?php __("Profit Center") ?></label>
                <?php
                echo $this->Form->input('profit_center', array(
                        'type' => 'select',
                        'name' => 'vsProfit',
                        'id' => 'vsProfit',
                        'div' => false,
                        'multiple' => true,
                        'hiddenField' => false,
                        'label' => false,
                        'style' => 'width:69% !important',
                        "empty" => __("-- Any --", true),
                        "options" => !empty($menuListProfitCenters) ? $menuListProfitCenters : array()
                    ));
                echo $this->Form->hidden('vsPcAll', array('value' => 'true'));
                ?>
            </div>
            <div class="wd-input" id="filter_employee1">
                <label for="status"><?php __("Employee") ?></label>
                <?php
                if(isset($employeeRorProfitCenterList)) $menuListEmployees=$employeeRorProfitCenterList;
                echo $this->Form->input('employee', array(
                        'type' => 'select',
                        'name' => 'vsEmploy',
                        'id' => 'vsEmploy',
                        'div' => false,
                        'multiple' => true,
                        'hiddenField' => false,
                        'label' => false,
                        'style' => 'width:69% !important',
                        "empty" => __("-- Any --", true),
                        "options" => !empty($menuListEmployees) ? $menuListEmployees : array()
                    ));
                ?>
            </div>
            <div class="wd-input" id="filter_budgetCustomer1">
                <label for="status"><?php __("Customer") ?></label>
                <?php
                echo $this->Form->input('budget_customer', array(
                    'type' => 'select',
                    'name' => 'vsCustomer',
                    'id' => 'vsCustomer',
                    'div' => false,
                    'multiple' => true,
                    'hiddenField' => false,
                    'label' => false,
                    'style' => 'width:69% !important',
                    "empty" => __("-- Any --", true),
                    "options" => !empty($menuListBudgetCustomers) ? $menuListBudgetCustomers : array()
                    ));
                ?>
            </div>
        </div>

        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="okVision"><?php __('OK') ?></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_sum"><?php __('RESET') ?></a></li>
    </ul>
</div>
<?php
echo $this->Form->create('ExportVision', array('url' => array('controller' => 'activity_tasks', 'action' => 'export_system'), 'type' => 'file', 'id' => 'ExportVisionsStaffingForm'));
echo $this->Form->hidden('showType');
echo $this->Form->hidden('summary');
echo $this->Form->hidden('from');
echo $this->Form->hidden('to');
echo $this->Form->hidden('activated');
echo $this->Form->hidden('activityName');
echo $this->Form->hidden('family');
echo $this->Form->hidden('subFamily');
echo $this->Form->hidden('profit_center');
echo $this->Form->hidden('employee');
echo $this->Form->hidden('budget_customer');
echo $this->Form->end();
?>
<?php endif;?>
<div id="dialog_select_content" class="buttons" style="display: none;">
    <div id="select_content_temp" style="margin-left: 10px; min-height: 50px;">
    </div>
    <p style="color: #000;margin-top: 20px;margin-left: 10px; margin-right: 10px;font-size: 11px;"><?php echo __("Please Ctrl+C to copy to clipboard", true); ?></p>
    <button class="btnSelect" style="display: none;" data-clipboard-target="#select_content_temp"></button>
</div>

<form method="POST" enctype="multipart/form-data" action="my_assistants/printScreen" id="myFormScreen">
    <input type="hidden" name="img_val" id="img_val" value="" />
</form>

<script type="text/javascript">
var companyConfigs = <?php echo json_encode($companyConfigs) ?>;
 function openMenuLeft(){
        $('.openMenu').toggleClass('active');
        $('.openMenu').next('.wd-content-left').toggleClass('active');
        // header_bottom.find('.header-bottom-image').toggleClass('active');
    }
(function($){
    
    // console.log(startTo);
    $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemMargin: 20,
        itemWidth: 120,
        minItems: 5,
        maxItems: 5,
        asNavFor: '#slider',
        pauseOnHover: true,
        prevText: '<i class="icon-arrow-left" aria-hidden="true"></i>',     
        nextText: '<i class="icon-arrow-right" aria-hidden="true"></i>',
    });

       
})(jQuery);
 function checkStaffing(_pjId){
    if(companyConfigs['run_staffing_before_display_staffing'] == 1){
        $.ajax({
            url : '<?php echo $html->url(array('controller' => 'projects', 'action' => 'checkStaffing')); ?>',
            async: true,
            type: 'POST',
            data: {
                id: _pjId
            },
            beforeSend: function(){
                $('#progressBar1').hide();
                $('#loading_w').show();
            },
            complete: function(){
                $('#loading_w').hide();
                window.location.href = '<?php echo $html->url(array('controller' => 'project_staffings', 'action' => 'visions')); ?>'+ '/' +_pjId;
            }
        });
    } else {
        window.location.href = '<?php echo $html->url(array('controller' => 'project_staffings', 'action' => 'visions')); ?>'+ '/' +_pjId;
    }
}

</script>
<?php echo $html->css('projects'); ?>
<!-- dialog_vision_staffing+++++++++++++++++.end -->
<script type="text/javascript">
    $(function(){

        //apply vs filter
        var vsFilter;
        var _controller = <?php echo json_encode($this->params['controller']) ?>;
        var _isActivityScreen = <?php echo json_encode($activityScreen) ?>;

        if( _isActivityScreen ){
            $.z0.History.load('vs_filter', function(data){
                vsFilter = data;
                //view by (0, 1, 5)
                var viewBy = data.get('view_by', 0);
                $(':input[name="type"][value="' + viewBy + '"]').prop('checked', true);
                //show summary (0, 1)
                var summary = data.get('summary', 0);
                $(':input[name="summary"][value="' + summary + '"]').prop('checked', true);
                var showNA = data.get('show_na', 1);
                $(':input[name="show_na"][value="' + showNA + '"]').prop('checked', true);
                //date type (1, 2, 3)
                var dateType = parseInt(data.get('date_type', 3));
                $(':input[name="aDateType"][value="' + dateType + '"]').prop('checked', true);
                switch( dateType ){
                    case 1:
                    case 2:
                        //date & week
                        var start = data.get('start_date', ''),
                            end = data.get('end_date', '');
                        $('#aStartDate').val(start);
                        $('#aEndDate').val(end);
                    break;
                    case 3:
                        //month
                        var today = new Date(),
                            month = today.getMonth() + 1,
                            sm = data.get('start_month', month < 10 ? '0' + month : month),
                            sy = data.get('start_year', today.getFullYear()),
                            em = data.get('end_month', month < 10 ? '0' + month : month),
                            ey = data.get('end_year', today.getFullYear());
                        $('#aStartMonth').val(sm);
                        $('#aStartYear').val(sy);
                        $('#aEndMonth').val(em);
                        $('#aEndYear').val(ey);
                    break;
                }

                //save for team workload
                var today = new Date(),
                    month = today.getMonth() + 1,
                    sm = data.get('smonth', month < 10 ? '0' + month : month),
                    sy = data.get('syear', today.getFullYear()),
                    em = data.get('emonth', month < 10 ? '0' + month : month),
                    ey = data.get('eyear', today.getFullYear());
                $('#teamStartMonth').val(sm);
                $('#teamStartYear').val(sy);
                $('#teamEndMonth').val(em);
                $('#teamEndYear').val(ey);

                var t = data.get('team', 0);
                $('#aTeam').val(t);

                var priority = data.get('teampriority', []);
                $('#teamPriority').multipleSelect('setSelects', priority);
                //end

                //activated?
                //filter_activated
                var activated = data.get('activated', []);
                $('#aActivated').multipleSelect(
                    'setSelects',
                    {
                        values: activated,
                        click: true,
                        complete: function(){
                            var activity = data.get('activity', []);
                            $('#aName').multipleSelect('setSelects', activity);
                        }
                    }
                );

                var priority = data.get('priority', []);
                $('#StaffingPriority').multipleSelect('setSelects', priority);

                // //filter_family
                var family = data.get('family', []);
                $('#aFamily').multipleSelect(
                    'setSelects',
                    {
                        values: family,
                        click: true,
                        //call after ajax loading sub-family complete and sub-family select is filled
                        complete: function(){
                            var sub_family = data.get('sub_family', []);
                            $('#aSub').multipleSelect('setSelects', sub_family);
                        }
                    }
                );

                var pc = data.get('pc', []);
                $('#aPC').multipleSelect(
                    'setSelects',
                    {
                        values: pc,
                        click: true,
                        complete: function(){
                            var resource = data.get('resource', []);
                            $('#aEmployee').multipleSelect('setSelects', resource);
                        }
                    }
                );

                //filter_budgetCustomer
                var customer = data.get('customer', []);
                $('#aCustomer').multipleSelect('setSelects', customer);

                if(_controller == 'activity_forecasts'
                            || _controller == 'activities'
                            || _controller == 'activity_tasks'
                            || _controller == 'team_workloads'
                            || _controller == 'activity_budget_synthesis'
                            || _controller == 'activity_budget_sales'
                            || _controller == 'activity_budget_internals'
                            || _controller == 'activity_budget_externals'
                            || _controller == 'activity_budget_provisionals'
                ){
                    var checkShowby = $('#group-showby').find('input:checked');
                    if(checkShowby[0].id=='ActivityProjectStaffingId0')
                    {
                        clickPId0();
                    }
                    else if(checkShowby[0].id=='ActivityProjectStaffingId1')
                    {
                        clickPId1();
                    }
                    else
                    {
                        clickPId5();
                    }
                }
            });
        }

        //store vs filter (data changed)
        //MODIFY + ADD CODE BY VINGUYEN 17/05/2014
        function clickPId0(){
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').removeAttr('disabled');
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').parent().removeClass('display-none');

            $('#aPC').removeAttr('disabled');
            $('#aPC').parent().removeClass('display-none');

            $('#aDateType1').show();
            $('#aDateType2').show();

            $('label[for=aDateType1]').show();
            $('label[for=aDateType2]').show();

            //vsFilter.set('view_by', 0);
        }
        function clickPId1(){
            $('#aName, #aEmployee').attr('disabled', 'disabled');
            $('#aName, #aEmployee').parent().addClass('display-none');

            $('#aEmployee').attr('disabled', 'disabled');
            $('#aEmployee').parent().addClass('display-none');

            $('#aActivated').removeAttr('disabled');
            $('#aActivated').parent().removeClass('display-none');

            $('#aPC').removeAttr('disabled');
            $('#aPC').parent().removeClass('display-none');

            $('#aDateType1').show();
            $('#aDateType2').show();

            $('label[for=aDateType1]').show();
            $('label[for=aDateType2]').show();

            //vsFilter.set('view_by', 1);
        }
        function clickPId5(){
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').removeAttr('disabled');
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').parent().removeClass('display-none');

            $('#aFamily, #aSub').removeAttr('disabled');
            $('#aFamily, #aSub').parent().removeClass('display-none');

            $('#aEmployee, #aPC').attr('disabled', 'disabled');
            $('#aEmployee, #aPC').parent().addClass('display-none');

            $('#aDateType1').hide();
            $('#aDateType2').hide();

            $('label[for=aDateType1]').hide();
            $('label[for=aDateType2]').hide();

            //vsFilter.set('view_by', 5);
        }
        function validateDate(oldValue,elm)
        {
            var startMonth = $('#aStartMonth').val();
            var startYear = $('#aStartYear').val();
            var endMonth = $('#aEndMonth').val();
            var endYear = $('#aEndYear').val();
            var $id = elm.id;
            var error = 0;
            //var oldValue = elm.options[elm.selectedIndex].value;
            if( endYear - startYear > 5)
            {
                error = 1;
            }
            else if(startYear > endYear)
            {
                error = 1;
            }
            else if(startMonth == endMonth && startYear > endYear)
            {
                error = 1;
            }
            else if(startMonth > endMonth && startYear >= endYear)
            {
                error = 1;
            }
            if(error == 1)
            {
                $('#'+$id).val(oldValue);
            }
        }
        var previous;
        $("#aStartMonth").on('focus', function () {
            previous = this.value;
        }).change(function() {
            validateDate(previous,this);
        });
        $("#aStartYear").on('focus', function () {
            previous = this.value;
        }).change(function() {
            validateDate(previous,this);
        });
        $("#aEndMonth").on('focus', function () {
            previous = this.value;
        }).change(function() {
            validateDate(previous,this);
        });
        $("#aEndYear").on('focus', function () {
            previous = this.value;
        }).change(function() {
            validateDate(previous,this);
        });

        $('input[name="aDateType"]').click(function(){
            if( $(this).val() == 1 || $(this).val() == 2 ){
                $('.dateByDay').show();
                $('.dateNotByDay').hide();
                var currentDate = new Date();
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() - currentDate.getDay() + 1); // First day is the day of the month - the day of the week
                var last = currentDate + 5; // last day is the first day + 6
                if($('#aStartDate').val() == '')
                    $('#aStartDate').datepicker('setDate',currentDate);
                if($('#aEndDate').val() == '')
                    $('#aEndDate').datepicker('setDate',currentDate);
                if($(this).val() == 2)
                {
                    if($('#aStartDate').val() != '')
                    {
                        var currentDateWeek = $('#aStartDate').datepicker('getDate');
                        currentDateWeek = currentDateWeek.getDate() - currentDateWeek.getDay(); // First day is the day of the month - the day of the week
                        $('#aStartDate').datepicker('setDate',currentDate);
                    }
                    if($('#aEndDate').val() != '')
                    {
                        var currentDateWeek = $('#aStartDate').datepicker('getDate');
                        currentDateWeek = currentDateWeek.getDate() - currentDateWeek.getDay() ; // First day is the day of the month - the day of the week
                        var last = currentDateWeek + 4;
                        $('#aEndDate').datepicker('setDate',last);
                    }
                    $('.activity-datepicker').datepicker('option', 'beforeShowDay', function(date){
                        var result;
                        if( this.id == 'aStartDate' ) result = date.getDay() == 1;
                        else result = date.getDay() == 0;
                        return [result, ''];
                    });
                }
                else
                {
                    $('.activity-datepicker').datepicker('option', 'beforeShowDay', '');
                }
            } else {
                $('.dateByDay').hide();
                $('.dateNotByDay').show();
            }
        });
        $('.activity-datepicker').datepicker({
                dateFormat : 'dd-mm-yy',
                showOn : 'focus',
                onSelect : function(){
                    var date1 = $('#aStartDate').datepicker('getDate'),
                        date2 = $('#aEndDate').datepicker('getDate');
                    validField = true;
                    if( date2 < date1 ){
                        validField = false;
                        $('#aEndDate').datepicker('setDate',date1);
                    }
                }
            }).prop({ readonly : true});
            <?php if(isset($dateType) && $dateType == 2)
            { ?>
            $('.activity-datepicker').datepicker('option', 'beforeShowDay', function(date){
                        var result;
                        if( this.id == 'aStartDate' ) result = date.getDay() == 1;
                        else result = date.getDay() == 0;
                        return [result, ''];
                    });
                    <?php } ?>

        if(_controller == 'activity_forecasts'
            || _controller == 'activities'
            || _controller == 'activity_tasks'
            || _controller == 'team_workloads'
            || _controller == 'activity_budget_synthesis'
            || _controller == 'activity_budget_sales'
            || _controller == 'activity_budget_internals'
            || _controller == 'activity_budget_externals'
            || _controller == 'activity_budget_provisionals'
        ){

            $('#ActivityProjectStaffingId0').click(function(){
                //$('#filter_activated div label').find('input').eq(1).prop('checked' , true);
                clickPId0();
            });
            $('#ActivityProjectStaffingId1').click(function(){
                clickPId1();
                //RESET ACTIVITY
                $('#aName').find('span').text('<?php __("-- Any --"); ?>');
                $('#filter_activityName div label').each(function(){
                    if($(this).find('input').is(':checked')){
                        $(this).removeAttr('class');
                        $(this).find('input').removeAttr('checked');
                    }
                });

                //RESET EMPLOYEE
                $('#aEmployee').find('span').text('<?php __("-- Any --"); ?>');
                $('#filter_employee div label').each(function(){
                    if($(this).find('input').is(':checked')){
                        $(this).removeAttr('class');
                        $(this).find('input').removeAttr('checked');
                    }
                });
            });
            $('#ActivityProjectStaffingId5').click(function(){
                clickPId5();
                //RESET FAMILY
                $('#aName').find('span').text('<?php __("-- Any --"); ?>');
                $('#filter_activityName div label').each(function(){
                    if($(this).find('input').is(':checked')){
                        $(this).removeAttr('class');
                        $(this).find('input').removeAttr('checked');
                    }
                });

            });

            //END


            $("#add_vision_staffing_news_menu").live('click',function(){
                $("#dialog_vision_staffing_news_menu").dialog('option',{title:''}).dialog('open');
                return false;
            });

            $(".cancel").live('click',function(){
                $("#dialog_vision_staffing_news_menu").dialog('close');
            });

            $("#add_team_workload_news_menu").live('click',function(){
                $("#dialog_team_workload_news_menu").dialog('option',{title:''}).dialog('open');
                return false;
            });

            $(".cancel").live('click',function(){
                $("#dialog_team_workload_news_menu").dialog('close');
            });
            var dialogWorkloadPlus = function(){
                $('#dialog_team_workload_plus').dialog({
                    position    :'center',
                    autoOpen    : false,
                    modal       : true,
                    width       : 500,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                dialogWorkloadPlus = $.noop;
            }
            dialogWorkloadPlus();
             $("#add_team_workload_plus").on('click',function(){
                $("#dialog_team_workload_plus").dialog().dialog('open');
                return false;
            });
            $(".cancel").on('click',function(){
                $("#dialog_team_workload_plus").dialog().dialog('close');
            });
            $("#ok_sum").click(function(){
                var list = $('#StaffingPriority'),
                    priorities = list.multipleSelect('getSelects');
                list.multipleSelect('disable');
                if( priorities.length ){
                    $('#hiddenPriority').val(priorities.join(','));
                } else {
                    $('#hiddenPriority').val('');
                }
                //save vs filter

                vsFilter.set('priority', priorities);
                vsFilter.set('view_by', $('#group-showby :checked').val());
                vsFilter.set('summary', $('#show_summary :checked').val());
                vsFilter.set('show_na', $('[name="show_na"]:checked').val());
                var dateType = parseInt($('#group-date-type :checked').val());
                vsFilter.set('date_type', dateType);
                switch( dateType ){
                    case 1:
                    case 2:
                        //date & week
                        vsFilter.set('start_date', $('#aStartDate').val());
                        vsFilter.set('end_date', $('#aEndDate').val());
                        vsFilter.unset('start_month', 'start_year', 'end_month', 'end_year');
                    break;
                    case 3:
                        //month
                        vsFilter.set('start_month', $('#aStartMonth').val());
                        vsFilter.set('start_year', $('#aStartYear').val());
                        vsFilter.set('end_month', $('#aEndMonth').val());
                        vsFilter.set('end_year', $('#aEndYear').val());
                        vsFilter.unset('start_date', 'end_date');
                    break;
                }
                var listpc = $('#aPC').multipleSelect('getSelects');
                vsFilter.set('activated', $('#aActivated').multipleSelect('getSelects'));
                vsFilter.set('family', $('#aFamily').multipleSelect('getSelects'));
                vsFilter.set('sub_family', $('#aSub').multipleSelect('getSelects'));
                vsFilter.set('pc', listpc);
                vsFilter.set('resource', $('#aEmployee').multipleSelect('getSelects'));
                vsFilter.set('customer', $('#aCustomer').multipleSelect('getSelects'));
                vsFilter.set('prority', $('#StaffingPriority').multipleSelect('getSelects'));
                vsFilter.set('activity', $('#aName').multipleSelect('getSelects'));
                //save filter
                $.z0.History.save('vs_filter', vsFilter);
                //select 1 pc and show staffing by pc:
                if( $('#ActivityProjectStaffingId1').prop('checked') ){
                    if( listpc.length == 1 ){
                        if( !$("#form_vision_staffing_news_menu .x-target").length ){
                            $('<input type="hidden" name="ItMe" value="" class="x-target x-pc"><input class="x-target" type="hidden" name="target" value="1">').appendTo("#form_vision_staffing_news_menu");
                        }
                        $('.x-pc').val(listpc[0]);
                    }
                } else {
                    $('.x-target').remove();
                }
                //submit
                $("#form_vision_staffing_news_menu").submit();
                list.multipleSelect('enable');
            });
            $('#ok_sum_team').click(function(){
                var list = $('#teamPriority');
                list.multipleSelect('disable');
                vsFilter.set('smonth', $('#teamStartMonth').val());
                vsFilter.set('syear', $('#teamStartYear').val());
                vsFilter.set('emonth', $('#teamEndMonth').val());
                vsFilter.set('eyear', $('#teamEndYear').val());
                vsFilter.set('team', $('#aTeam').val());
                vsFilter.set('teampriority', $('#teamPriority').multipleSelect('getSelects'));
                $.z0.History.save('vs_filter', vsFilter);
                //submit
                setTimeout(function(){
                    $("#form_team_workload_news_menu").submit();
                }, 1000);
                list.multipleSelect('enable');
            });

            $("#reset_sum").click(function(){
                //RESET Activated
                $('#aActivated').multipleSelect('setSelects', []);
                //RESET FAMILY
                $('#aFamily').multipleSelect('setSelects', []);
                //RESET SUB FAMILY
                $('#aSub').multipleSelect('setSelects', []);
                //RESET PC
                $('#aPC').multipleSelect('setSelects', []);
                //RESET resource
                $('#aEmployee').multipleSelect('setSelects', []);
                $('#aCustomer').multipleSelect('setSelects', []);
                $('#StaffingPriority').multipleSelect('setSelects', []);
                $('#aName').multipleSelect('setSelects', []);
                vsFilter.set('activated', []);
                vsFilter.set('family', []);
                vsFilter.set('sub_family', []);
                vsFilter.set('pc', []);
                vsFilter.set('resource', []);
                vsFilter.set('customer', []);
                vsFilter.set('prority', []);
                vsFilter.set('activity', []);
                return false;
            });

            $("#reset_sum_team").click(function(){
                //RESET
                $('#teamPriority').multipleSelect('setSelects', []);
                vsFilter.set('teampriority', []);
                return false;
            });
            $('#export-reset_sum').click(function(){
                $('#export-list-profits').find('span').text('<?php __("-- Any --"); ?>');
                $('#export-profitCenter div label').each(function(){
                    if($(this).find('input').is(':checked')){
                        $(this).removeAttr('class');
                        $(this).find('input').removeAttr('checked');
                    }
                });
                return false;
            });

            /*dialog export*/

            var validField = true;
            var eDialog = $('#activity-export-dialog').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 500,
                open : function(){
                    $('#export-submit-dialog').focus();
                }
            });
            var eMessageDialog = $('#activity-export-message-dialog').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 500,
                open : function(event, ui){
                    $(".ui-dialog-titlebar-close", ui.dialog || ui).hide();
                    //$('#export-submit-dialog').focus();
                }
            });


            $('.export-datepicker').datepicker({
                dateFormat : 'dd-mm-yy',
                showOn : 'focus',
                onSelect : function(){
                    var date1 = $('#export-start-date').datepicker('getDate'),
                        date2 = $('#export-end-date').datepicker('getDate');
                    validField = true;
                    if( date2 < date1){
                        validField = false;
                        alert('<?php __('End date must be greater than start date') ?>');
                    }
                }
            });
            $('#export-cancel-dialog').click(function(){
                eDialog.dialog('close');
            });
            $('#show-export-dialog').click(function(){
                eDialog.dialog('open');
                $('#export-start-date').datepicker("hide");
            });
            /**
             * Get Employee Of Profit Center
             */
            getEmployOfProfit = function(profitCenters){
                var results = '';
                $.ajax({
                    url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'getEmployeeOfProfitCenterUsingModuleExportActivity')); ?>',
                    cache : false,
                    type : 'POST',
                    async: false,
                    data: {
                        profit_center_id: profitCenters
                    },
                    success: function(data){
                        result = JSON.parse(data);
                    }
                });
                return result;
            };
            /**
             * Get Employee Of Profit Center
             */
            countRecordExporting = function(){
                var results = '';
                $.ajax({
                    url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'countModuleExportActivity')); ?>',
                    cache : false,
                    async: false,
                    success: function(data){
                        result = JSON.parse(data);
                    }
                });
                return result;
            };
            var mergeFile = 'no';
            $('#export-submit-dialog').click(function(){
                mergeFile = parseInt($('#export-list-merge').val()) == 1 ? 'yes' : 'no';
                display = parseInt($('#export-display').val()) == 1 ? 'yes' : 'no';
                if(validField){
                    var profitCenter = [];
                    $('#export-profitCenter div label').each(function(){
                        if($(this).find('input').is(':checked')){
                            var _val = $(this).find('input:checked').val();
                            profitCenter.push(_val);
                        }
                    });
                    var employee = [];
                    $('#export-employee div label').each(function(){
                        if($(this).find('input').is(':checked')){
                            var _val = $(this).find('input:checked').val();
                            employee.push(_val);
                        }
                    });
                    if( !profitCenter.length && !employee.length){
                        alert('<?php __('Please select at least a profit center or employee') ?>');
                        return;
                    }
                    eDialog.dialog('close');
                    $('#fileExport').html(0);
                    $('#totalFile').html(0);
                    $('#totalFiles').html(0);
                    $('#totalEmploys').html('...');
                    $('#totalRecords').html('...');
                    $('#exporting_buttons').hide();
                    eMessageDialog.dialog('open');
                    setTimeout(function(){
                        var startDate = $('#export-start-date').val().split('-');
                        startDate = new Date(startDate[2] + '-' + startDate[1] + '-' + startDate[0]).getTime();
                        var endDate = $('#export-end-date').val().split('-');
                        endDate = new Date(endDate[2] + '-' + endDate[1] + '-' + endDate[0]).getTime();
                        var timeDiff = Math.abs(endDate - startDate);
                        var diffDays = Math.ceil(timeDiff/(1000 * 3600 * 24));
                        var splitFileFollowEmploy = 5;
                        if(diffDays > 1095){ // 4 nam tro len
                            splitFileFollowEmploy = 5;
                        } else if(diffDays > 730){ // 3 nam
                            splitFileFollowEmploy = 5;
                        } else if(diffDays > 365){ // 2 nam
                            splitFileFollowEmploy = 10;
                        } else if(diffDays > 183){ // 6 thang den 1 nam
                            splitFileFollowEmploy = 20;
                        } else { // duoi 6 thang
                            splitFileFollowEmploy = 25;
                        }
                        // cho phep 100 employee 1 lan export
                        //splitFileFollowEmploy = 100;
                        var employIds = new Array();
                        if(employee.length){
                            employIds = employee;
                        } else {
                            employIds = getEmployOfProfit(profitCenter);
                            employIds = employIds ? $.map(employIds, function(el) { return el; }) : {};
                        }
                        var totalFile = 1;
                        if(employIds.length > splitFileFollowEmploy){
                            totalFile = Math.ceil(employIds.length/splitFileFollowEmploy);
                            $('#totalFile').html(totalFile);
                            $('#totalFiles').html(totalFile);
                            $('#totalEmploys').html(employIds.length);
                            $('#exporting_wait').show();
                            var employSends = new Array();
                            var employCount = employCheck = 0;
                            $.each(employIds, function(ind, val){
                                employCheck++;
                                if(employCheck > splitFileFollowEmploy){
                                    employCount++;
                                    employCheck = 1;
                                }
                                if(!employSends[employCount]){
                                    employSends[employCount] = [];
                                }
                                employSends[employCount].push(val);
                            });
                            if(employSends){
                                $.each(employSends, function(ind, val){
                                    setTimeout(function(){
                                        $.ajax({
                                            url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'exportActivityFollowEmployee')); ?>',
                                            cache : false,
                                            type : 'POST',
                                            async: true,
                                            data: {
                                                start_date: $('#export-start-date').val(),
                                                end_date: $('#export-end-date').val(),
                                                employee_id: val,
                                                day_of: $('#export-list-dayOff').val(),
                                                display :display
                                            },
                                            success: function(data){
                                                var _data = JSON.parse(data);
                                                if(_data){
                                                    var totalRecord = $('#totalRecords').html();
                                                    totalRecord = (totalRecord === '...') ? 0 : totalRecord;
                                                    setTimeout(function(){
                                                        //$('#totalRecords').html(parseInt(totalRecord) + parseInt(_data.totalRecord));
                                                        $('#totalRecords').html(parseInt(_data.totalRecord));
                                                        //window.location.href = _data.urlDownload;
                                                        $('#fileExport').html(parseInt($('#fileExport').html()) + 1);
                                                        var checkFile = parseInt($('#fileExport').html());
                                                        if(checkFile == totalFile){
                                                            $('#exporting_wait').hide();
                                                            $('#exporting_buttons').show();
                                                        }
                                                    }, 50);
                                                }
                                            }
                                        });
                                    }, 100);
                                });
                            }
                        } else {
                            $('#totalFile').html(totalFile);
                            $('#totalFiles').html(totalFile);
                            $('#totalEmploys').html(employIds.length);
                            $('#exporting_wait').show();
                            setTimeout(function(){
                                $.ajax({
                                    url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'exportActivityFollowEmployee')); ?>',
                                    cache : false,
                                    type : 'POST',
                                    async: true,
                                    data: {
                                        start_date: $('#export-start-date').val(),
                                        end_date: $('#export-end-date').val(),
                                        employee_id: employIds,
                                        day_of: $('#export-list-dayOff').val(),
                                        display :display
                                    },
                                    success: function(data){
                                        var _data = JSON.parse(data);
                                        if(_data){
                                            var totalRecord = $('#totalRecords').html();
                                            totalRecord = (totalRecord === '...') ? 0 : totalRecord;
                                            setTimeout(function(){
                                                $('#totalRecords').html(parseInt(_data.totalRecord));
                                                $('#fileExport').html(parseInt($('#fileExport').html()) + 1);
                                                var checkFile = parseInt($('#fileExport').html());
                                                if(checkFile == totalFile){
                                                    $('#exporting_wait').hide();
                                                    $('#exporting_buttons').show();
                                                }
                                            }, 50);
                                        }
                                    }
                                });
                            }, 100);
                        }
                    }, 100);
                    //return false;
                    //$('#form-activity-export').submit();
                } else {
                    alert('<?php __('End date must be greater than start date') ?>');
                    $('#export-end-date').focus();
                }
            });
            $('#export-report-cancel-dialog').click(function(){
                //alert('<?php __('The export file will be removed from system. Confirm ? (Y/N)') ?>');
                eMessageDialog.dialog('close');
                setTimeout(function(){
                    $.ajax({
                        url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'deleteFileModuleExportActivity')); ?>',
                        cache : false,
                        async: true,
                        success: function(data){
                        }
                    });
                }, 100);
            });
            $('#export-report-download-dialog').click(function(){
                eMessageDialog.dialog('close');
                $('#overlay-container').show();
                /**
                 * Tinh tong so record se export ra trong lan request nay.
                 */
                var totalRecordExport = countRecordExporting();
                var totalFileDownload = Math.ceil(totalRecordExport/10000);
                if(totalFileDownload != 0){
                    for(var i = 0; i < totalFileDownload; i++){
                        setTimeout(function(){
                            $.ajax({
                                url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'zipFileModuleExportActivity')); ?>' + '/' + mergeFile,
                                cache : false,
                                async: true,
                                success: function(data){
                                    var urlDown = JSON.parse(data);
                                    if(urlDown){
                                        window.location.href = urlDown;
                                        var _rels = $('#overlay-container').attr('rels');
                                        _rels = parseInt(_rels) + 1;
                                        $('#overlay-container').attr('check', _rels);
                                        if(_rels = totalFileDownload){
                                            $('#overlay-container').hide();
                                        }
                                    }
                                }
                            });
                        }, 100);
                    }
                } else {
                    $('#overlay-container').hide();
                }
            });
            $('#export-list-employees').multiSelect({
                noneSelected: '<?php __("-- Any --"); ?>',
                oneOrMoreSelected: '*',
                selectAll: false
            });
            $("#export-list-profits").multiSelect({
                noneSelected: '<?php __("-- Any --"); ?>',
                url :'<?php echo $html->url('/activities/get_employee_for_profit_center/') ?>',
                update : "#export-list-employees",
                parent : '#export-profitCenter',
                loadingClass : 'wd-disable',
                loadingText : 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });
            /*end dialog export*/
            /* table .end */
            var createDialog = function(){
                // $("#aFamily").multiSelect({
                //  noneSelected: '<?php __("-- Any --"); ?>',
                //  url :'<?php echo $html->url('/activities/get_sub_family/') ?>',
                //  update : "#aSub",
                //  loadingClass : 'wd-disable',
                //  loadingText : 'Loading...',
                //  oneOrMoreSelected: '*', selectAll: false });

                // $("#aPC").multiSelect({
                //  noneSelected: '<?php __("-- Any --"); ?>',
                //  url :'<?php echo $html->url('/activities/get_employee_for_profit_center/') ?>',
                //  update : "#aEmployee",
                //  loadingClass : 'wd-disable',
                //  loadingText : 'Loading...',
                //  oneOrMoreSelected: '*', selectAll: false });

                $('#dialog_vision_staffing_news_menu').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    show : function(e){

                    },
                    open : function(e){
                        // var $dialog = $(e.target);
                        // $("#aActivated").multiSelect({
                        //  noneSelected: '<?php __("-- Any --"); ?>',
                        //  url :'<?php echo $html->url('/activities/get_activity_filter/') ?>',
                        //  update : "#aName",
                        //  loadingClass : 'wd-disable',
                        //  loadingText : 'Loading...',
                        //  oneOrMoreSelected: '*', selectAll: false });
                        // $dialog.find('select').not('#aStartMonth, #aEndMonth, #aStartYear, #aEndYear').multiSelect({
                        //  noneSelected: '<?php __("-- Any --"); ?>',
                        //  oneOrMoreSelected: '*', selectAll: false });

                        // $dialog.dialog({open: $.noop});
                    }
                });

                $('#dialog_team_workload_news_menu').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    show : function(e){

                    },
                    open : function(e){
                    }
                });
                createDialog = $.noop;
            };
            createDialog();
            var timeout, timeout2, timeout3;
            var multiActivated = $('#aActivated').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>',
                onClick: function(view){
                    clearTimeout(timeout);
                    timeout = setTimeout(function(){
                        updateList('<?php echo $html->url('/activities/get_activity_filter/') ?>', multiActivated, $('#aName'), view.complete);
                    }, 750);
                }
            });

            var multiActivityName = $('#aName').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>'
            });

            var multiPriority = $('#StaffingPriority').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>'
            });

            var multiPriorityTeam = $('#teamPriority').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>'
            });

            var multiFamily = $('#aFamily').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>',
                onClick: function(view){
                    clearTimeout(timeout2);
                    timeout2 = setTimeout(function(){
                        updateList('<?php echo $html->url('/activities/get_sub_family/') ?>', multiFamily, $('#aSub'), view.complete);
                    }, 750);
                }
            });

            var multiSubfamily = $('#aSub').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>'
            });

            var multiPc = $('#aPC').multipleSelect({
                minimumCountSelected: 0,
                position: 'top',
                placeholder: '<?php __("-- Any --") ?>',
                onClick: function(view){
                    clearTimeout(timeout3);
                    timeout3 = setTimeout(function(){
                        updatePcAndResource(multiPc, $('#aEmployee'), view.complete);
                    }, 1000);
                }
                // userData: function(instance, li) {

       //              return {};
       //          }
            });

            var multiResource = $('#aEmployee').multipleSelect({
                minimumCountSelected: 0,
                position: 'top',
                placeholder: '<?php __("-- Any --") ?>'
            });

            var multiCustomer = $('#aCustomer').multipleSelect({
                minimumCountSelected: 0,
                position: 'top',
                placeholder: '<?php __("-- Any --") ?>'
            });
            var multiPriorityTeam = $('#teamPriority').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>'
            });
            var multiPriorityTeamplus = $('#teamPrio').multipleSelect({
                minimumCountSelected: 0,
                placeholder: '<?php __("-- Any --") ?>'
            });
            var multiPcTeamPlus = $('#teamP').multipleSelect({
                minimumCountSelected: 0,
                position: 'top',
                placeholder: '<?php __("-- Any --") ?>',
                onClick: function(view){
                    clearTimeout(timeout3);
                    timeout3 = setTimeout(function(){
                        updatePcAndResource(multiPcTeamPlus, $('#aEmployeteam'), view.complete);
                    }, 1000);
                }
            });

            /**
             * Created new vission staffing+ (demo)
             */
   //          var createDialogNewVision = function(){
            //  $("#vsFamily").multiSelect({
            //      noneSelected: '<?php __("-- Any --"); ?>',
            //      url :'<?php echo $html->url('/activities/get_sub_family/') ?>',
            //      update : "#vsSubFamily",
            //      loadingClass : 'wd-disable',
            //      loadingText : 'Loading...',
            //      oneOrMoreSelected: '*', selectAll: false });

            //  $("#vsProfit").multiSelect({
            //      noneSelected: '<?php __("-- Any --"); ?>',
            //      url :'<?php echo $html->url('/activities/get_employee_for_profit_center/') ?>',
            //      update : "#vsEmploy",
            //      loadingClass : 'wd-disable',
            //      loadingText : 'Loading...',
            //      oneOrMoreSelected: '*', selectAll: false });

            //  $('#dialogVisionCalled').dialog({
            //      position    :'center',
            //      autoOpen    : false,
            //      autoHeight  : true,
            //      modal       : true,
            //      width       : 500,
            //      show : function(e){

            //      },
            //      open : function(e){
            //          var $dialog = $(e.target);
            //          $("#vsActivated").multiSelect({
            //              noneSelected: '<?php __("-- Any --"); ?>',
            //              url :'<?php echo $html->url('/activities/get_activity_filter/') ?>',
            //              update : "#vsAcName",
            //              loadingClass : 'wd-disable',
            //              loadingText : 'Loading...',
            //              oneOrMoreSelected: '*', selectAll: false });
   //                          $dialog.find('select').not('#vsFromMonth, #vsFromYear, #vsToMonth, #vsToYear').multiSelect({
   //                              noneSelected: '<?php __("-- Any --"); ?>',
   //                              oneOrMoreSelected: '*', selectAll: false
   //                          });

            //          $dialog.dialog({open: $.noop});
            //      }
            //  });
            //  createDialogNewVision = $.noop;
            // };
            // createDialogNewVision();
   //          $("#callVision").live('click',function(){
            //  $("#dialogVisionCalled").dialog('option',{title:''}).dialog('open');
            //  return false;
            // });
   //          $("#okVision").click(function(){
            //  $("#formVisionNew").submit();
            // });
            // $(".cancel").live('click',function(){
            //  $("#dialogVisionCalled").dialog('close');
            // });
        }
    });
    var totalPC = <?php echo isset($menuListProfitCenters) ? json_encode(count($menuListProfitCenters)) : 0 ?>;
    function updatePcAndResource(loader, filler, callback){
        var placeholder = filler.multipleSelect('getPlaceholder'),
            list = loader.multipleSelect('getSelects');
        $.ajax({
            url: '<?php echo $html->url('/activities/get_employee_for_profit_center/') ?>',
            cache : true,
            data: {
                data: list
            },
            dataType: 'json',
            beforeSend: function(){
                placeholder.addClass('loading');
                loader.multipleSelect('disable');
                loader.multipleSelect('disableCheckboxes');
            },
            success: function(data){
                //update filler
                filler.html(data.html);
                filler.multipleSelect('refresh');
                //update loader
                if( $.isArray(data.pc) ){
                    data.pc = $.merge(list, data.pc);
                } else {
                    var pc = [];
                    $.each(data.pc, function(i, v){
                        pc.push(v);
                    });
                    data.pc = $.merge(list, pc);
                }
                loader.multipleSelect('setSelects', data.pc);
                if( $.isFunction(callback) ){
                    callback(loader, filler, data);
                }
            },
            complete: function(){
                placeholder.removeClass('loading');
                loader.multipleSelect('enable');
                loader.multipleSelect('enableCheckboxes');
                //set select pc all
                var instance = loader.multipleSelect('getInstance');
                var total = instance.$selectItems.filter(':checked').length;
                if(totalPC == total){
                    $('#ActivitySelectPCAll').val('true');
                } else {
                    $('#ActivitySelectPCAll').val('false');
                }
            }
        });
    }
    function updateList(url, loader, filler, callback, empty){
        var placeholder = filler.multipleSelect('getPlaceholder'),
            list = loader.multipleSelect('getSelects');
        if( !list.length && !empty ){
            filler.html('');
            filler.multipleSelect('refresh');
            return;
        }
        $.ajax({
            url: url,
            cache : true,
            data: {
                data: list
            },
            beforeSend: function(){
                placeholder.addClass('loading');
            },
            success: function(data){
                filler.html(data);
                filler.multipleSelect('refresh');
                if( $.isFunction(callback) ){
                    callback(loader, filler, data);
                }
            },
            complete: function(){
                placeholder.removeClass('loading');
            }
        });
    }
    $(document).ready(function () {
        if($(window).width() > 991){
            $('.wd-content-left').height($('.wd-content-left').closest('#layout').height());
        }else{
            $('.wd-content-left').css('height','');
        }

    });
    $(window).on('resize', function () {
        if($(window).width() > 991){
            var _menu_left = $('.wd-content-left');
            var _min_height = $(window).height() - _menu_left.offset().top;
            _menu_left.css('min-height',_min_height);
            _menu_left.height(_menu_left.closest('#layout').find('.wd-tab').height());
        }else{
            $('.wd-content-left').css({
                'height': '',
                'min-height':''
            });
        }


    });
    $(window).ready(function(){
        $('#sub-nav').addClass('ready');
        $('#sub-nav ul ul').addClass('sub-menu').hide().parent('li').addClass('has-sub');
        $('#sub-nav ul li').prepend('<span class="expand-button"></span');
        $('.wd-current').parent('.sub-menu').closest('li.has-sub').addClass('wd-current-parent open');
        $('.wd-current').closest('.sub-menu').show();
        $('.expand-button').on('click',function(){
           $(this).parent('li.has-sub').toggleClass('open').find('ul:first').slideToggle();
        });

    });
</script>

</div>
<div class="menu-employee-update">
<?php


$name = '';
$projects = array();
$has_data = 0;
$screens = array('edit', 'your_form');
if ((in_array($this->params['saveAction'], $screens) || ($this->params['controller'] == 'project_local_views_preview') || ($this->params['controller'] == 'project_tasks_preview') || ($this->params['controller'] == 'project_amrs' && $this->params['saveAction'] == 'index_plus')) && (strpos($this->params['controller'], 'project') === 0)){
    if(!empty($project_id)){
        $projects = ClassRegistry::init('Project')->find('first', array('recursive' => -1, 
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('update_by_employee', 'last_modified'))
        );
        $name_implode = explode(' ', $projects['Project']['update_by_employee']);
        $name = substr($name_implode[0], 0, 1).''.substr($name_implode[1], 0, 1);
        $has_data = 1;
    }
    
}else{
        $projects['Project'] = array();
        $path = $this->params['url']['url'];
        $projects = ClassRegistry::init('UserLastUpdated')->find('first', array(
            'recursive' => -1, 
            'conditions' => array(
                    'employee_id' => $employee_info['Employee']['id'],
                    'path' => $this->params['url']['url'],
                ),
            'fields' => array('employee_id', 'updated'))
        );
        $projects['Project']['last_modified'] = $projects['UserLastUpdated']['updated'];
        if(!empty($projects['UserLastUpdated'])){
            $employee_name = ClassRegistry::init('Employee')->find('first', array(
                'recursive' => -1, 
                'conditions' => array(
                    'id' => $projects['UserLastUpdated']['employee_id'],
                ),
                'fields' => array('first_name', 'last_name'))
            );
            $projects['Project']['update_by_employee'] = $employee_name['Employee']['first_name'] + ' ' + $employee_name['Employee']['last_name'];
            $name = substr($employee_name['Employee']['first_name'], 0, 1).''.substr($employee_name['Employee']['last_name'], 0, 1);
            $has_data = 1;
        }
}
if($has_data == 1){ ?>
    <span class="circle-name"><?php echo $name ?></span><span class="menu-employee-info"><?php echo sprintf(__("Modifié le %s à %sh%s par %s", true), date('d M. Y', $projects['Project']['last_modified']), date('H', $projects['Project']['last_modified']),date('i', $projects['Project']['last_modified']), $projects['Project']['update_by_employee']); ?>
    </span>
<?php } ?>
</div>
</div>