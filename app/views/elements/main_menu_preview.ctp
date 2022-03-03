<?php
echo $html->script('jquery.multiSelect');
echo $html->css('jquery.multiSelect');
echo $html->css('slick_grid/slick.edit');
echo $this->element('dialog_projects');
echo $this->Html->script('multiple-select');
echo $this->Html->css('multiple-select');
echo $html->css('projects');
echo $this->Html->script('progress/nanobar1');
echo $this->Html->script('html2canvas-0.5/html2canvas05');
echo $this->Html->script('html2canvas-0.5/html2canvas05.svg');
// echo $this->Html->script('clipboard');
echo $this->Html->script('clipboard.min');
?>
<script type="text/javascript" src="/js/jquery-1.12.4/jquery.min.js"></script>
<script type="text/javascript">
var j$ = $.noConflict(true);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
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
$menuAdmins = array('translations', 'versions', 'cities', 'countries', 'companies', 'currencies', 'profit_centers', 'absences', 'workdays', 'response_constraints', 'holidays', 'activity_columns', 'activity_families','contract_types','liscenses', 'activity_settings', 'budget_settings', 'budget_customers', 'budget_providers', 'budget_types', 'budget_funders', 'security_settings', 'menus', 'activity_exports','staffing_systems','system_configs', 'action_logs', 'tasks', 'project_importers', 'project_acceptance_types','periods','admin_task', 'dependencies', 'kpi_settings', 'company_configs','sql_manager', 'externals', 'profile_project_managers',
    //tickets
    'ticket_profiles', 'ticket_profile_permissions', 'ticket_statuses', 'ticket_metas', 'vision_task_exports', 'expectation_datasets', 'expectation_colors', 'expectations', 'expectation_translations'
);
$menuProjects = array('projects', 'projects_preview', 'project_teams', 'project_parts', 'project_phase_plans', 'project_phase_plans', 'project_milestones', 'project_tasks', 'project_risks', 'project_issues', 'project_decisions', 'project_livrables', 'project_evolutions', 'project_amrs', 'project_staffings', 'project_created_vals', 'project_global_views', 'project_local_views', 'project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_purchases', 'project_budget_synthesis', 'project_images', 'project_finances', 'project_budget_provisionals', 'project_acceptances', 'project_expectations', 'project_budget_fiscals', 'project_dependencies', 'video', 'zog_msgs');
$menuActivities = array('activity_forecasts', 'activities', 'activity_tasks', 'activity_budget_internals', 'activity_budget_externals', 'activity_budget_sales', 'activity_budget_synthesis', 'activity_budget_provisionals', 'team_workloads');
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
$active = 0;
if ($this->params['controller'] == 'pages')
    $active = 1;
if ($this->params['controller'] == 'projects_preview'){
    if($AppStatusProject == 2){
        $active = 7;
    } else {
        $active = 2;
    }
}
if ($this->params['controller'] == 'employees' || $this->params['controller'] == 'employee_absences' || $this->params['controller'] == 'absence_requests')
    $active = 3;
if ($this->params['controller'] == 'user_views')
    $active = 5;
if ($this->params['controller'] == 'projects_preview' && $this->params['action'] == 'edit')
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
}
function _pre_recursive2($item, $pass, $_Model, $profile_account){
    $me = $item[$_Model];
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
    $_pc = '';
    if(isset($me['controllers']) && $me['controllers'] == 'project_budget_fiscals' && $pass['role'] == 'admin'){
        $_pc = -1;
    }
    $html ='';
    $link = $pass['html']->url(array('controller' => $me['controllers'], 'action' => $me['functions'], $pass['project'], $_pc));

    $name = $pass['language'] == 'eng' ? $me['name_eng'] : $me['name_fre'];
    if($me['controllers'] == 'project_staffings'){
        $html = '<li class="' . $class . '"><a onclick="checkStaffing('. $pass['project'] .')" href="javascript:void(0)">' . $name . '</a>';
    } else {
        $html = '<li class="' . $class . '"><a href="' . $link . '">' . $name . '</a>';
    }
    //children recursive
    if( !empty($item['children']) ){
        $html .= '<ul>';
        foreach($item['children'] as $child){
            if($profile_account){
                $html .= _pre_recursive2($child, $pass, 'ProfileProjectManagerDetail', $profile_account);
            } else {
                $html .= _pre_recursive2($child, $pass, 'Menu', $profile_account);
            }
        }
        $html .= '</ul>';
    }
    $html .= '</li>';
    return $html;
}
function _pre_recursiveMenu2($menu, $ul = true, $pass = array(), $profile_account){
    $html = '';
    if( $ul )$html .= '<ul>';
    foreach($menu as $item){
        if($profile_account){
            $html .= _pre_recursive2($item, $pass, 'ProfileProjectManagerDetail', $profile_account);
        } else {
            $html .= _pre_recursive2($item, $pass, 'Menu', $profile_account);
        }
    }
    if( $ul )$html .= '</ul>';
    echo $html;
}

if (in_array($this->params['controller'], $menuAudits)){
    $active = 8;
    /**
     * Lay danh sach Admin Audit
     */
    $adminAudits = ClassRegistry::init('AuditAdmin')->find('list', array(
        'recursive' => -1,
        'conditions' => array('company_id' => $company_id),
        'fields' => array('employee_id', 'employee_id')
    ));
}
if(in_array($this->params['controller'], $menuSales)){
    $active = 9;
    /**
     * Kiem tra xem employee dang nhap co quyen gi
     */
    $saleRoles = ClassRegistry::init('SaleRole')->find('first', array(
        'recursive' => -1,
        'conditions' => array('company_id' => $company_id, 'employee_id' => $employee_id),
        'fields' => array('sale_role')
    ));
    $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
}
if(in_array($this->params['controller'], $menuReports)){
    $active = 13;
}
if( $this->params['controller'] == 'zog_msgs' && !empty($this->params['saveAction']) && $this->params['saveAction'] == 'index'){
    $active = 14;
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
|| $this->params['controller'] == 'team_workloads'
) && ($this->params['action'] != 'my_diary' && $this->params['action'] != 'import_timesheet' && $this->params['action'] != 'import_csv');

if( $this->params['controller'] == 'tickets' ){
    $active = 15;
}
?>
<?php
$_class= '';
$employee = $employee_info['Employee']['id'];
if($employee_info['Employee']['company_id'] == null){
    $_idEm = ClassRegistry::init('Employee')->find('first', array(
        'recursive' => -1,
        'conditions' => array(
            'fullname' => $employee_info['Employee']['fullname'],
            'company_id' => null
        ),
        'fields' => array('id', 'fullname')
    ));
    $employee = !empty($_idEm['Employee']['id']) ? $_idEm['Employee']['id'] : 0;
} 
$dissable_menu = ClassRegistry::init('HistoryFilter')->find('first', array(
    'recursive' => -1,
    'fields' => array('id', 'params'),
    'conditions' => array(
        'path' => 'dissable_menu',
        'employee_id' => $employee
    )
));

// do trong database co 2 gia tri khi disable menu 1 va "1"

if(!empty($dissable_menu) && ( $dissable_menu['HistoryFilter']['params'] == '"1"' || $dissable_menu['HistoryFilter']['params'] == 1)){
    $_class = 'dissable-menu';
}
$grid_url_rollback = ClassRegistry::init('HistoryFilter')->find('first', array(
    'recursive' => -1,
    'fields' => array('id', 'params'),
    'conditions' => array(
        'path' => 'project_grid_url',
        'employee_id' => $employee_info['Employee']['id']
    )
));
$grid_url_rollback = !empty($grid_url_rollback) ? $grid_url_rollback['HistoryFilter']['params'] : '';
$display_my_assistant = Configure::read('Config.displayAssistant');
?>

<div id="wd-top-nav" class="<?php echo $_class ?>">
    <ul>
        <?php if($is_sas || (!$is_sas && $role !='conslt' && $enableBusines == true)):?>
        <li>
            <a href="<?php echo $html->url("/sale_leads/"); ?>">
                <span class="tab-center <?php if ($active == 9) { ?>wd-current-center<?php } ?>"><?php __('Business') ?></span>
                <!-- sub menu -->
            </a>
        </li>
        <?php endif;?>
        <?php if($is_sas || (!$is_sas && $enablePMS == true && $role !='conslt')):?>
        <li>
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1){ ?>
            <a href="<?php echo $html->url("/projects_preview?cate=2"); ?>">
            <?php } else { ?>
             <a href="<?php echo $html->url("/projects?cate=2"); ?>">
            <?php } ?>
                <span class="tab-center <?php if ($active == 7) { ?>wd-current-center<?php } ?>"><?php __('Opportunity') ?></span>
            </a>
            
            <!-- sub menu -->
        </li>
         <li>
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1){ ?>
            <a href="<?php
                if(isset($cate) && !empty($cate) && $cate == 2){
                    echo $html->url("/projects_preview?cate=1");
                } else {
                    echo $html->url("/projects_preview");
                }
                ?>">
            <?php } else { ?>
            <a href="<?php
                if(isset($cate) && !empty($cate) && $cate == 2){
                    echo $html->url("/projects?cate=1");
                } else {
                    echo $html->url("/projects");
                }
                ?>">    
            <?php } ?>
                <span class="tab-center <?php if ($active == 2) { ?>wd-current-center<?php } ?>"><?php __('Projects') ?></span>
            </a>
            
            <!-- sub menu -->
        </li>
        <?php endif; ?>
        <?php if($role !='conslt'):?>
        <?php if (!$checkSeePersonalizedViews) : ?>
            <li >
                <a href="<?php echo $html->url("/user_views/"); ?>">
                    <span class="tab-center <?php if ($active == 5) { ?> wd-current-center<?php } ?>"><?php __('Personalized Views') ?></span>
                </a>
            </li>
        <?php endif; ?>
        <?php endif; ?>
       
        <?php if (!$checkSeeResource) : ?>
        <li>
            <a href="<?php echo $html->url("/employees/"); ?>">
                <span class="tab-center <?php if ($active == 3) { ?>wd-current-center<?php } ?>"><?php __('Employees') ?></span>
            </a>
        </li>
        <?php endif; ?>
        <?php if ($is_sas || $role == "admin" || $role == 'hr') : ?>
            <li >
                <a href="<?php echo $html->url((isset($role) && $role == 'hr') ? '/absences/' : "/administrators/"); ?>">
                    <span class="tab-center <?php if ($active == 4) { ?>wd-current-center<?php } ?>"><?php __('Administration') ?></span>
                </a>
            </li>
        <?php endif; ?>
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
        <li>
            <a href="<?php echo $html->url("/activity_forecasts_preview/my_diary"); ?>">
                <span class="tab-center <?php if ($active == 12) { ?>wd-current-center<?php } ?>"><?php __('My Diary') ?></span>
            </a>
        </li>
		<?php $display_absence_tab = isset($companyConfigs['display_absence_tab']) ? $companyConfigs['display_absence_tab'] : 1;
		if($display_absence_tab){ ?>
        <li>
            <a href="<?php echo $html->url("/absence_requests/") ?>">
                <span class="tab-center <?php if ($active == 10) { ?> wd-current-center<?php } ?>"><?php __('Absence') ?></span>
            </a>
            <!-- sub menu -->
        </li>
		<?php  } ?>
        <?php if($is_sas || (!$is_sas && $enableRMS == true)):?>
        <li>
            <a href="<?php echo $html->url("/activity_forecasts/request/") ?>">
                <span class="tab-center <?php if ($active == 11) { ?> wd-current-center<?php } ?>"><?php __('Activity') ?></span>
            </a>
            <!-- sub menu -->
        </li>
        <?php endif; ?>
        <?php if($is_sas || (!$is_sas && $enableAudit == true && $seeMenuAudit == true)):?>
        <li>
            <a href="<?php echo $html->url("/audit_missions/"); ?>">
                <span class="tab-center <?php if ($active == 8) { ?>wd-current-center<?php } ?>"><?php __('Audit') ?></span>
            </a>
        </li>
        <?php endif;?>
        <?php if($is_sas || (!$is_sas && $role !='conslt' && $enableReport == true)):?>
        <li>
            <a href="<?php echo $html->url("/reports/"); ?>">
                <span class="tab-center <?php if ($active == 13) { ?>wd-current-center<?php } ?>"><?php __('Reports') ?></span>
            </a>
        </li>
        <?php endif;?>
        <?php if(($is_sas) || (!$is_sas && $role !='conslt' && $enableZogMsgs == true)): ?>
            <li>
                <a href="<?php echo $html->url("/zog_msgs/"); ?>">
                    <span class="tab-center <?php if ($active == 14) { ?>wd-current-center<?php } ?>"><?php __('ZogMsg') ?></span>
                </a>
            </li>
        <?php endif; ?>
        <?php if( $enableTicket ): ?>
            <li>
                <a href="<?php echo $html->url("/tickets/"); ?>">
                    <span class="tab-center <?php if ($active == 15) { ?>wd-current-center<?php } ?>"><?php __('Tickets') ?></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
