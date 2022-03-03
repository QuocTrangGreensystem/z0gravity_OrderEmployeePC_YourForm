<?php 
$_this_controller =  trim(str_replace('_preview', '', $this->params['controller'] ));
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

$companyConfigs = isset( $companyConfigs ) ? $companyConfigs : array();

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
$menuProjects = array('projects', 'project_teams', 'project_parts', 'project_phase_plans', 'project_phase_plans', 'project_milestones', 'project_tasks', 'project_risks', 'project_issues', 'project_decisions', 'project_livrables', 'project_evolutions', 'project_amrs', 'project_staffings', 'project_created_vals', 'project_global_views', 'project_local_views', 'project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_purchases', 'project_budget_synthesis', 'project_images', 'project_finances', 'project_budget_provisionals', 'project_acceptances', 'project_expectations', 'project_budget_fiscals', 'project_dependencies', 'video', 'zog_msgs', 'kanban', 'project_powerbi_dashboards', 'project_communications');
$menuActivities = array('activity_forecasts', 'activities', 'activity_tasks', 'activity_budget_internals', 'activity_budget_externals', 'activity_budget_sales', 'activity_budget_synthesis', 'activity_budget_provisionals', 'team_workloads');
$menuBudgets = (empty($canSeeBudget) && !empty($budget_actions)) ? array_keys($budget_actions) : array();
$menuBudgetActivities = array('activity_budget_internals', 'activity_budget_externals', 'activity_budget_sales', 'activity_budget_synthesis', 'activity_budget_provisionals');
$menuAudits = array('audit_admins', 'audit_settings', 'audit_missions', 'audit_recoms');
$menuSales = array('sale_settings', 'sale_roles', 'sale_customers', 'sale_customer_contacts', 'sale_leads', 'sale_expenses', 'categories', 'easyraps');
$menuReports = array('reports');

$svg_icons = array(
	'sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.sun-a{fill:none;}.sun-b{fill:#ee8845;fill-rule:evenodd;}</style></defs><rect class="sun-a" width="24" height="24"/><path class="sun-b" d="M19.143,10.857H17.429a.857.857,0,0,1,0-1.714h1.714a.857.857,0,0,1,0,1.714ZM16.32,4.891a.857.857,0,0,1-1.212-1.212l1.143-1.143a.857.857,0,1,1,1.212,1.212ZM10,16a6,6,0,1,1,6-6A6.007,6.007,0,0,1,10,16ZM10,5.714A4.286,4.286,0,1,0,14.286,10,4.29,4.29,0,0,0,10,5.714Zm0-2.286a.857.857,0,0,1-.857-.857V.857a.857.857,0,1,1,1.714,0V2.571A.857.857,0,0,1,10,3.429ZM4.286,5.143a.855.855,0,0,1-.606-.251L2.537,3.749A.857.857,0,0,1,3.749,2.537L4.891,3.679a.857.857,0,0,1-.606,1.463ZM3.429,10a.857.857,0,0,1-.857.857H.857a.857.857,0,1,1,0-1.714H2.571A.857.857,0,0,1,3.429,10Zm.251,5.108A.857.857,0,1,1,4.891,16.32L3.749,17.463a.857.857,0,0,1-1.212-1.212ZM10,16.571a.857.857,0,0,1,.857.857v1.714a.857.857,0,0,1-1.714,0V17.429A.857.857,0,0,1,10,16.571Zm5.714-1.714a.854.854,0,0,1,.606.251l1.143,1.143a.857.857,0,1,1-1.212,1.212L15.108,16.32a.857.857,0,0,1,.606-1.463Z" transform="translate(2 2)"/></svg>',
	'cloud' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.cloud-a{fill:none;}.cloud-b{fill:#79b2da;fill-rule:evenodd;}</style></defs><rect class="cloud-a" width="24" height="24"/><path class="cloud-b" d="M19.268,9.257H17.8a.773.773,0,0,1,0-1.543h1.463a.773.773,0,0,1,0,1.543Zm-2.9-4.341a.709.709,0,0,1-.517.226.752.752,0,0,1-.732-.771.793.793,0,0,1,.214-.546L16.312,2.8a.713.713,0,0,1,.518-.226.752.752,0,0,1,.732.771.788.788,0,0,1-.215.545Zm-1.576,6.74a3.353,3.353,0,0,1,1.791,3A3.26,3.26,0,0,1,13.415,18H3.659A3.761,3.761,0,0,1,0,14.143a3.884,3.884,0,0,1,1.967-3.418A5.242,5.242,0,0,1,7.073,5.657,4.852,4.852,0,0,1,8.6,5.912a4.073,4.073,0,0,1,3.347-1.8A4.264,4.264,0,0,1,16.1,8.486,4.46,4.46,0,0,1,14.795,11.657ZM7.073,7.2a3.749,3.749,0,0,0-3.646,3.619l-.05.87-.732.4a2.326,2.326,0,0,0-1.181,2.05,2.26,2.26,0,0,0,2.2,2.314h9.756a1.757,1.757,0,0,0,1.707-1.8A1.8,1.8,0,0,0,14.071,13l-.9-.4V11.571a2.249,2.249,0,0,0-2.122-2.309l-.68-.023-.421-.564A3.575,3.575,0,0,0,7.073,7.2Zm4.878-1.543a2.61,2.61,0,0,0-1.993.949A5.291,5.291,0,0,1,11.1,7.721,3.653,3.653,0,0,1,14.267,9.9a2.9,2.9,0,0,0,.367-1.41A2.762,2.762,0,0,0,11.951,5.657Zm0-2.571a.752.752,0,0,1-.732-.771V.771a.733.733,0,1,1,1.463,0V2.314A.752.752,0,0,1,11.951,3.086Zm-3.9,2.057a.711.711,0,0,1-.518-.226L6.556,3.888a.79.79,0,0,1-.214-.545.753.753,0,0,1,.732-.771A.711.711,0,0,1,7.59,2.8l.976,1.029a.791.791,0,0,1,.215.546A.753.753,0,0,1,8.049,5.143Z" transform="translate(2 3)"/></svg>',
	'rain' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.rain-a{fill:none;}.rain-b{fill:#f05352;fill-rule:evenodd;}</style></defs><rect class="rain-a" width="24" height="24"/><path class="rain-b" d="M16.176,14.4a.9.9,0,0,1,0-1.8,2.107,2.107,0,0,0,.792-4.039L15.882,8.1V6.9a2.661,2.661,0,0,0-2.559-2.693L12.5,4.18l-.508-.658A4.359,4.359,0,0,0,8.529,1.8a4.452,4.452,0,0,0-4.4,4.222L4.072,7.036l-.883.472A2.708,2.708,0,0,0,4.412,12.6a.9.9,0,0,1,0,1.8A4.456,4.456,0,0,1,0,9.9,4.508,4.508,0,0,1,2.372,5.912,6.225,6.225,0,0,1,8.529,0,6.117,6.117,0,0,1,13.38,2.408,4.456,4.456,0,0,1,17.647,6.9a3.913,3.913,0,0,1-1.471,7.5ZM7.353,10.2a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V11.1A.891.891,0,0,1,7.353,10.2Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V14.7A.891.891,0,0,1,7.353,13.8Zm2.941-2.4a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V12.3A.891.891,0,0,1,10.294,11.4Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V15.9A.891.891,0,0,1,10.294,15Zm2.941-4.8a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V11.1A.891.891,0,0,1,13.235,10.2Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V14.7A.891.891,0,0,1,13.235,13.8Z" transform="translate(2 3)"/></svg>',
);

if(in_array($_this_controller, $menuBudgets)){
    $check_budget_actis = true;
}
if(in_array($_this_controller, $menuBudgetActivities)){
    $check_budget_actis_AC = true;
}
if($_this_controller == 'activity_tasks' &&
($this->params['action'] == 'index' ||
 $this->params['action'] == 'visions' ||
 $this->params['action'] == 'dash_board' ||
 $this->params['action'] == 'teams' ||
 $this->params['action'] == 'teams_yes' ||
 $this->params['action'] == 'import_csv')
    || in_array($_this_controller, $menuBudgetActivities)
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
$active = '';
if ($_this_controller == 'pages')
    $active = 1;
if ($_this_controller == 'projects'){
    if($AppStatusProject == 2){
        $active = 7;
    } else {
        $active = 2;
    }
}
if ($_this_controller == 'employees' || $_this_controller == 'employee_absences' || $_this_controller == 'absence_requests')
    $active = 3;
if ($_this_controller == 'user_views')
    $active = 5;
if ($_this_controller == 'projects' && $this->params['action'] == 'edit')
    $active = 6;

if ($_this_controller == 'employee_absences' || $_this_controller == 'absence_requests')
    $active = 10;

if (in_array($_this_controller, $menuActivities)){
    $active = 11;
    /**
     * Lay Activity Settings
     */
    $activitySettings = ClassRegistry::init('ActivitySetting')->find('first');
}

if ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'my_diary')
    $active = 12;

if (in_array($_this_controller, $menuAdmins) || (strpos($_this_controller, 'project_') === 0) || ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'import_timesheet') || ($_this_controller == 'employees' && $this->params['action'] == 'profile'))
    $active = 4;
if (in_array($_this_controller, $menuProjects)){
    $active = 2;
    if($AppStatusProject == 2 || $category == 2){
        $active = 7;
    }
    if(!($this->params['action'] == 'tasks_vision_new' || $this->params['action'] == 'kanban_vision')){
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
		$_isAdmin = $employee_info['Role']['name'] == 'admin';
		$_canCommunication =  isset($employee_info['Employee']['can_communication']) && ($employee_info['Employee']['can_communication'] == '1');
		$_canCommunication = $_canCommunication && !empty($canModified); 
		if( !( $_isAdmin || $_canCommunication)){
			$menu = removeCommunication($menu);
		}	
		if( empty($employee_info['Color']['is_new_design'] )){
			$menu = removeFlashInfo($menu);
		}		
    }
}

function removeFlashInfo($menus){
	foreach( $menus as $k => $item){
		if( ($item['Menu']['widget_id'] == 'flash_info') && !empty($item['children'])){
			$menus = array_merge( $menus, $item['children']);
			$menus[$k]['children'] = array();
		}
	}
	return $menus;
}
function removeCommunication($menu){
	foreach(  $menu as $k => $m){
		if( !empty( $m['Menu']) && ($m['Menu']['controllers'] == 'project_communications')){
			unset( $menu[$k]['Menu']);
			if( empty($menu[$k])) unset( $menu[$k]);
		}
		if( !empty( $m['children'])){
			$m['children'] = removeCommunication($m['children'] );
		}
	}
	return $menu;
}
function _recursive2($item, $pass, $_Model, $profile_account){
	if( empty( $item[$_Model])) return;
    $me = $item[$_Model];
    if($me['controllers'] == 'projects_preview' && $me['functions'] == 'flash_info')  { return ;}
    $isBudgetScreen = in_array($me['controllers'], $pass['disableMenuBudgetAndFinance']);
    $_pass_controller =  trim( str_replace('_preview', '', $pass['params']['controller'] ), ' \t\n\r\0\x0B_');
	$_me_controller =  trim( str_replace('_preview', '', $me['controllers'] ), ' \t\n\r\0\x0B_');
    if($isBudgetScreen && $pass['resetRole'] == 3 && ((!$pass['EPM_see_the_budget'] || !$pass['seeBudgetPM']))){
        return '';
    }
    $class = '';
    if($_me_controller == 'projects'){
        $class .= 'tooltip-pm-details';
    } elseif($_me_controller == 'project_amrs'){
        $class .= 'tooltip-pm-amrs';
		
    } elseif($_me_controller == 'zog_msgs' && $me['functions'] == 'detail' && $pass['params']['controller'] == $me['controllers']){
        $class = 'wd-current';
    }
    if( $pass['check_budget_actis'] && $isBudgetScreen ){
        $class = 'wd-current ';
    } else if($_me_controller == 'projects'){
        if($pass['params']['action'] == $me['functions']){
            $class = 'wd-current';
        }
    } else if($_pass_controller == $_me_controller && $pass['params']['action'] == $me['functions']) {
        $class = 'wd-current';
    }
    $_pc = '';
    if(isset($me['controllers']) && $me['controllers'] == 'project_budget_fiscals' && $pass['role'] == 'admin'){
        $_pc = -1;
    }
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
                $html .= _recursive2($child, $pass, 'ProfileProjectManagerDetail', $profile_account);
            } else {
                $html .= _recursive2($child, $pass, 'Menu', $profile_account);
            }
        }
        $html .= '</ul>';
    }
    $html .= '</li>';
    return $html;
}
function recursiveMenu2($menu, $ul = true, $pass = array(), $profile_account){
    $html = '';
    if( $ul )$html .= '<ul>';
    foreach($menu as $item){
        if($profile_account){
            $html .= _recursive2($item, $pass, 'ProfileProjectManagerDetail', $profile_account);
        } else {
            $html .= _recursive2($item, $pass, 'Menu', $profile_account);
        }
    }
    if( $ul )$html .= '</ul>';
    echo $html;
}

if (in_array($_this_controller, $menuAudits)){
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
if(in_array($_this_controller, $menuSales)){
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
if(in_array($_this_controller, $menuReports)){
    $active = 13;
}
if( $_this_controller == 'zog_msgs' && !empty($this->params['saveAction']) && $this->params['saveAction'] == 'index'){
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
if($_this_controller == 'sale_customers'){
    if($this->params['action'] == 'index' && !empty($this->params['pass'][0])){
        $passBussiness = $this->params['pass'][0];
    }
    if($this->params['action'] == 'update' && !empty($this->params['pass'][1])){
        $passBussiness = $this->params['pass'][1];
    }

}
$activityScreen = ($_this_controller == 'activity_forecasts'
|| $_this_controller == 'activities'
|| $_this_controller == 'activity_tasks'
|| $_this_controller == 'team_workloads'
) && ($this->params['action'] != 'my_diary' && $this->params['action'] != 'import_timesheet' && $this->params['action'] != 'import_csv' && $this->params['action'] != 'manages');

if( $_this_controller == 'tickets' ){
    $active = 15;
}
?>
<?php 
	$employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
	$employeeName = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
	$companyName = !empty($employee_info['Company']['company_name']) ? $employee_info['Company']['company_name'] : '';
	$urlAvatar = $this->UserFile->avatar($employeeIdLogin);
?>
	
<?php
$_class= '';
$employee = $employee_info['Employee']['id'];
/* Khong biet phan nay de lam gi. Chay loi
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
*/
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
<div id="sub-nav" class="menu-list clear-fix">
    <!--reports-->
    <?php if($_this_controller == 'reports'): ?>
        <div id="menu-wrapper" class="menu-wrapper">
            <ul id="sub-nav-report" class="menu-list">
                <?php if($is_sas): ?>
                <li class="<?php echo ($_this_controller == 'reports')&& $this->params['action'] == 'index' ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/reports/index/") ?>"><?php __("Dashboard") ?></a></li>
                <?php endif; ?>
                <li class="<?php echo ($_this_controller == 'reports')&& $this->params['action'] == 'sql_report' ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/reports/sql_report/") ?>"><?php __("Report") ?></a></li>
            </ul>
        </div>
    <?php endif; ?>
    <!--audit-->
    <?php
        if($_this_controller == 'audit_missions' || $_this_controller == 'audit_admins' || $_this_controller == 'audit_recoms' || $_this_controller == 'audit_settings'):
    ?>
    <div id="menu-wrapper" class="menu-wrapper ">
        <ul id="sub-nav-audit" class="menu-list">
            <li class="<?php echo ($_this_controller == 'audit_missions') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/audit_missions/index/") ?>"><?php __("Mission") ?></a></li>
            <li class="<?php echo ($_this_controller == 'audit_recoms') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/audit_recoms/index_follow_employ/") ?>"><?php __("Recommendation") ?></a></li-->
            <?php if ($is_sas || $role == "admin" || (!empty($adminAudits) && in_array($employee_id, $adminAudits))):?>
            <li class="<?php echo ($_this_controller == 'audit_admins' || $_this_controller == 'audit_settings') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/audit_settings/index/auditor_company/") ?>"><?php __("Administration") ?></a></li>
            <?php endif;?>
        </ul>
    </div>
    <?php
        endif;
    ?>
    <!-- business -->
    <?php
        if($_this_controller == 'sale_customers' || $_this_controller == 'sale_customer_contacts' || $_this_controller == 'sale_settings' || $_this_controller == 'sale_roles' || $_this_controller == 'sale_expenses' || $_this_controller == 'sale_leads' || $_this_controller == 'categories' || $_this_controller == 'easyraps'):
    ?>
    <div id="menu-wrapper" class="menu-wrapper ">
        <ul id="sub-nav-business" class="menu-list">
            <li class="<?php echo ($_this_controller == 'sale_leads' && ($this->params['action'] == 'index' || $this->params['action'] == 'update')) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_leads/index/") ?>"><?php __("Lead") ?></a></li>
            <li class="<?php echo ($_this_controller == 'sale_leads' && ($this->params['action'] == 'deal' || $this->params['action'] == 'deal_update')) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_leads/deal/") ?>"><?php __("Deal") ?></a></li>
            <li class="<?php echo ($_this_controller == 'sale_customers' && $passBussiness == 'pro') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_customers/index/pro/") ?>"><?php __("Provider") ?></a></li>
            <li class="<?php echo ($_this_controller == 'sale_customers' && $passBussiness == 'cus') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_customers/index/cus/") ?>"><?php __("Customer") ?></a></li>
            <li class="<?php echo ($_this_controller == 'sale_customer_contacts') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_customer_contacts/index/") ?>"><?php __("Contact") ?></a></li-->
            <?php if ($is_sas || $role == "admin" || (!empty($saleRoles) && ($saleRoles == 1))): //|| $saleRoles == 2 : ko co sale manager thay admin?>
            <li class="<?php echo ( $_this_controller == 'categories' || $_this_controller == 'sale_settings' || $_this_controller == 'sale_roles' || $_this_controller == 'sale_expenses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/sale_settings/index/customer_status/") ?>"><?php __("Administration") ?></a></li>
            <?php endif;?>
            <li class="<?php echo ($_this_controller == 'easyraps') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/easyraps/") ?>"><?php __("Easyrap") ?></a></li>
        </ul>
    </div>
    <?php endif;?>
    <!-- project & opportunity -->
    <?php
    if (!($_this_controller == 'projects' && in_array($this->params['action'], array('index', 'opportunity', 'projects_vision', 'map', 'index_plus'))) && (!empty($this->params['pass']) && $_this_controller != 'user_views'
            && $_this_controller != 'project_created_values'
            && $_this_controller != 'employees' && $_this_controller != 'absences'
            && $_this_controller != 'response_constraints' && $_this_controller != 'absence_requests'
            && $_this_controller != 'holidays' && $_this_controller != 'activities'
            && $_this_controller != 'activity_columns' && $_this_controller != 'activity_families'
            && $_this_controller != 'activity_forecasts' && $_this_controller != 'contract_types'
            && $_this_controller != 'activity_tasks'
            && $_this_controller != 'activity_budget_internals'
            && $_this_controller != 'activity_budget_externals'
            && $_this_controller != 'activity_budget_sales'
            && $_this_controller != 'activity_budget_synthesis'
            && $_this_controller != 'activity_budget_provisionals'
            && $_this_controller != 'activity_settings'
            && $_this_controller != 'activity_exports'
            && $_this_controller != 'audit_settings'
            && $_this_controller != 'audit_admins'
            && $_this_controller != 'audit_missions'
            && $_this_controller != 'audit_recoms'
            && $_this_controller != 'sale_customers'
            && $_this_controller != 'sale_settings'
            && $_this_controller != 'sale_roles'
            && $_this_controller != 'sale_customer_contacts'
            && $_this_controller != 'sale_leads'
            && $_this_controller != 'sale_expenses'
            && $_this_controller != 'translations'
            && $_this_controller != 'menus'
            && $_this_controller != 'employee_absences' && $_this_controller != 'workdays')) :
    ?>
		<?php 
		if($_this_controller == 'kanban'){
			$project_id = empty($project_id) ?  $this->params['pass'][0] : $project_id;
		}
		$extra_class = '';
		if(!empty($project_id)){
			$project_info = ClassRegistry::init('Project')->find('first', array(
				'recursive' => -1,
				'fields' => array('project_name', 'start_date', 'end_date'),
				'conditions' => array('id' => $project_id)
			));

			$projectArms = ClassRegistry::init('ProjectAmr')->find('first', array(
				'recursive' => -1,
				'fields' => array('weather'),
				'conditions' => array('project_id' => $project_id)
			));

			$projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($project_info['Project']['end_date']));
			$projectDate = abs(strtotime($project_info['Project']['end_date']) - strtotime($project_info['Project']['start_date']));

		?>
		<script>
		function goBack() {
			window.history.back();
		}
		</script>
		<div class="wd-layout-heading">
			<ul>
				<?php
					$icon_weather = !empty($projectArms['ProjectAmr']['weather']) ? $projectArms['ProjectAmr']['weather'] : 'sun';
					$url = (!empty($icon_weather)) ? $svg_icons[$icon_weather] : '';
					// ob_clean();
					// debug($url);
					// exit;
				 ?>
				<li><div class="heading-back"><a href="javascript:void(0)" onclick="goBack()"><i class="icon-arrow-left"></i><span><?php echo __('Back', true);?></span></a></div></li>
				<li><div class="heading-weather icon-weather"><?php echo $url; ?></div></li>
				<li><div class="heading-project-title" title="<?php echo sprintf(__("%s", true), $project_info['Project']['project_name']); ?>"><?php echo sprintf(__("%s", true), $project_info['Project']['project_name']); ?></div></li>
			</ul>
		</div>
		
		<?php } else {
			$extra_class = 'undefined-project';
		} ?>
		<div id = 'menu-wrapper' class="menu-wrapper <?php echo $extra_class; ?>">
			<span onclick="onPrevous()" class="scroll-menu scroll-left">
				<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				<defs>
					<style>
					  .cls-1 {
						fill-rule: evenodd;
					  }
					</style>
				</defs>
				<path id="Forme_8" data-name="Forme 8" class="cls-1" d="M1563.65,144.758l-5.24,5.24,5.31,5.308h0A1,1,0,0,1,1563,157a0.988,0.988,0,0,1-.69-0.28h0l-6-6h0A0.991,0.991,0,0,1,1556,150v0a1,1,0,0,1,.31-0.719h0l6-6v0A0.991,0.991,0,0,1,1563,143,1,1,0,0,1,1563.65,144.758Z" transform="translate(-1556 -143)"/>
				</svg>
			</span>
			<div class="menu-list-container">
				<ul id="sub-nav-project" class="menu-list clearfix">
				
					<?php
						if(($_this_controller == 'project_tasks' && $this->params['action'] == 'detail')){
							if(empty($project_id)){
								$id = $this->params['pass'];
								$projectTasks = ClassRegistry::init('ProjectTask')->find('first', array('recursive' => -1, 'conditions' => array('ProjectTask.id' => $id[0]), 'fields' => array('project_id')));
								$project_id = $projectTasks['ProjectTask']['project_id'];
							}
						}
						if($_this_controller == 'zog_msgs'){
							$project_id = empty($project_id) ?  $this->params['pass'][0] : $project_id;
						}
						if(!empty($menu)){
							recursiveMenu2($menu, false, array(
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
			</div>
			<span onclick="onNext()" class="scroll-menu scroll-right">
				<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				  <defs>
					<style>
					  .cls-1 {
						fill: #666;
						fill-rule: evenodd;
					  }
					</style>
				  </defs>
				  <path id="arrow" class="cls-1" d="M1916.35,94.758l5.24,5.239-5.31,5.307h0A1,1,0,0,0,1917,107a0.985,0.985,0,0,0,.69-0.281h0l6-6h0A0.993,0.993,0,0,0,1924,100v0a1,1,0,0,0-.31-0.72h0l-6-6v0A0.99,0.99,0,0,0,1917,93a1,1,0,0,0-1,1A0.977,0.977,0,0,0,1916.35,94.758Z" transform="translate(-1916 -93)"/>
				</svg>
			</span>
		</div>
		<!-- <div class="space-empty"></div> -->
        <?php if ($_this_controller != 'projects') : ?>
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
                    // (function($){
                        // $('.tooltip-pm-details').tooltip({
                            // maxHeight : 500,
                            // maxWidth : 400,
                            // type : ['bottom','left'],
                            // content:  <?php echo json_encode($output); ?>});
                    // })(jQuery);
                </script>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($_this_controller != 'project_amrs') : ?>
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
                    // (function($){
                        // $('.tooltip-pm-amrs').tooltip({
                            // maxHeight : 500,
                            // maxWidth : 400,
                            // type : ['bottom','right'],
                            // content:  <?php echo json_encode($output); ?>});
                    // })(jQuery);
                </script>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <!-- absence -->
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
    <?php if ($_this_controller == 'employee_absences' || $_this_controller == 'absence_requests') : ?>
	<div id="menu-wrapper" class="menu-wrapper not-boxed">
			<span onclick="onPrevous()" class="scroll-menu scroll-left">
				<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				<defs>
					<style>
					  .cls-1 {
						fill-rule: evenodd;
					  }
					</style>
				  </defs>
				  <path id="Forme_8" data-name="Forme 8" class="cls-1" d="M1563.65,144.758l-5.24,5.24,5.31,5.308h0A1,1,0,0,1,1563,157a0.988,0.988,0,0,1-.69-0.28h0l-6-6h0A0.991,0.991,0,0,1,1556,150v0a1,1,0,0,1,.31-0.719h0l6-6v0A0.991,0.991,0,0,1,1563,143,1,1,0,0,1,1563.65,144.758Z" transform="translate(-1556 -143)"/>
				</svg>
			</span>
			<span onclick="onNext()" class="scroll-menu scroll-right">
				<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				  <defs>
					<style>
					  .cls-1 {
						fill: #666;
						fill-rule: evenodd;
					  }
					</style>
				  </defs>
				  <path id="arrow" class="cls-1" d="M1916.35,94.758l5.24,5.239-5.31,5.307h0A1,1,0,0,0,1917,107a0.985,0.985,0,0,0,.69-0.281h0l6-6h0A0.993,0.993,0,0,0,1924,100v0a1,1,0,0,0-.31-0.72h0l-6-6v0A0.99,0.99,0,0,0,1917,93a1,1,0,0,0-1,1A0.977,0.977,0,0,0,1916.35,94.758Z" transform="translate(-1916 -93)"/>
				</svg>
			</span>
		<ul id="sub-nav-absence" class="menu-list">
			<?php if((isset($companyConfigs['absences_show_absences'])) && $companyConfigs['absences_show_absences']==1){?>
				<li class="<?php echo ($_this_controller == 'employee_absences') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/employee_absences/index/" . @$employee_id . '/' . @$company_id) ?>"><?php __("Absences") ?></a></li>
			<?php }?>
            <li class="<?php echo ($_this_controller == 'absence_requests' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/") ?>"><?php __("Requests") ?></a></li>
            <?php if((isset($companyConfigs['absences_show_your_absence_review'])) && $companyConfigs['absences_show_your_absence_review']==1){?>
			<li class="<?php echo ($_this_controller == 'absence_requests' && $this->params['action'] == 'review') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/review") ?>"><?php __("Your Absence Review") ?></a></li>
			<?php }?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager) : ?>
                <li class="<?php echo ($_this_controller == 'absence_requests' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/manage/?profit=" . $profit['id']) ?>"><?php __("Validation") ?></a></li>
                <li class="<?php echo ($_this_controller == 'absence_requests' && $this->params['action'] == 'to_validated') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/manage/year/true/?profit=" .  $profit['id']) ?>"><?php __("To Validate") ?></a></li>
				<?php if((isset($companyConfigs['absences_show_absence_reviews'])) && $companyConfigs['absences_show_absence_reviews']==1){?>
					<li class="<?php echo ($_this_controller == 'absence_requests' && $this->params['action'] == 'reviews') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/absence_requests/reviews/?profit=" .  $profit['id']) ?>"><?php __("Absence Reviews") ?></a></li>
				<?php }?>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'pm' || $hasManager) : ?>
                <li class="<?php echo ($_this_controller == 'absence_requests' && $this->params['action'] == 'available') ? "wd-current" : "" ?>"><a href="<?php echo $available_url ?>"><?php __("Available dashboard") ?></a></li>
            <?php endif; ?>
        </ul>
	</div>
    <?php endif; ?>
    <!-- person view -->
    <?php if( isset($model) && $_this_controller == 'user_views'): ?>
	<div id="menu-wrapper" class="menu-wrapper">
		
		<ul class="menu-list">
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
	</div>
    <?php endif; ?>
    <!-- activity -->
    <?php
        $profit['id'] = !empty($profit['id']) ? $profit['id'] : -1;
        if(($_this_controller == 'activity_tasks' && $this->params['action'] != 'visions_staffing')
            || $_this_controller == 'activity_budget_internals'
            || $_this_controller == 'activity_budget_externals'
            || $_this_controller == 'activity_budget_provisionals'
            || $_this_controller == 'activity_budget_sales'
            || $_this_controller == 'activity_budget_synthesis'
            || $_this_controller == 'team_workloads'
    ){
     $profitOfBudgetInActivity = ($role == 'admin') ? -1 : $profit['id'];
     ?>
	<div id = 'menu-wrapper' class="menu-wrapper not-boxed">
		<span onclick="onPrevous()" class="scroll-menu scroll-left">
			<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
			<defs>
				<style>
				  .cls-1 {
					fill-rule: evenodd;
				  }
				</style>
			  </defs>
			  <path id="Forme_8" data-name="Forme 8" class="cls-1" d="M1563.65,144.758l-5.24,5.24,5.31,5.308h0A1,1,0,0,1,1563,157a0.988,0.988,0,0,1-.69-0.28h0l-6-6h0A0.991,0.991,0,0,1,1556,150v0a1,1,0,0,1,.31-0.719h0l6-6v0A0.991,0.991,0,0,1,1563,143,1,1,0,0,1,1563.65,144.758Z" transform="translate(-1556 -143)"/>
			</svg>
		</span>
		<span onclick="onNext()" class="scroll-menu scroll-right">
			<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				  <defs>
					<style>
					  .cls-1 {
						fill: #666;
						fill-rule: evenodd;
					  }
					</style>
				  </defs>
				  <path id="arrow" class="cls-1" d="M1916.35,94.758l5.24,5.239-5.31,5.307h0A1,1,0,0,0,1917,107a0.985,0.985,0,0,0,.69-0.281h0l6-6h0A0.993,0.993,0,0,0,1924,100v0a1,1,0,0,0-.31-0.72h0l-6-6v0A0.99,0.99,0,0,0,1917,93a1,1,0,0,0-1,1A0.977,0.977,0,0,0,1916.35,94.758Z" transform="translate(-1916 -93)"/>
				</svg>
		</span>
        <ul id="sub-nav-activity" class="menu-list">
            <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'request') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/request") ?>"><?php __("Requests") ?></a></li>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $hasManager) : //($role == 'pm' && $hasManager)?>
                <?php /*<li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manage?profit=" .  $profit['id']) ?>"><?php __("Forecasts") ?></a></li> */ ?>
                <?php if($ACBudgetSettings):?>
                <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'budget') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/budget?profit=" .  $profitOfBudgetInActivity) ?>"><?php __("Budget") ?></a></li>
                <?php endif;?>
                <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'response') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/response?profit=" .  $profit['id']) ?>"><?php __("Validation") ?></a></li>
				<li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'to_validate') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/to_validate?profit=" .  $profit['id']) ?>"><?php __("To Validate") ?></a></li>
				 <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'not_sent_yet') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/not_sent_yet?profit=" .  $profit['id']) ?>"><?php __("Not send yet") ?></a></li>
                <?php if(isset($companyConfigs['show_activity_forecast']) && $companyConfigs['show_activity_forecast']){ ?>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'manages') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manages?profit=" .  $profit['id']) ?>"><?php __("Forecasts+") ?></a></li>
				<?php } ?>
				<?php if(!(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast'] == 1)) { ?>
				<?php if(isset($companyConfigs['show_activity_forecast_plus']) && $companyConfigs['show_activity_forecast_plus']){ ?>
				<li class="<?php echo ($this->params['controller'] == 'activity_forecasts_preview' && $this->params['action'] == 'manages') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts_preview/manages") ?>"><?php __("Forecasts++") ?></a></li>
				<?php }
				} ?>
				
            <?php
			endif; ?>
           <?php if ($is_sas || $role == "admin" || $role == 'hr') : ?>
				<?php if(isset($companyConfigs['show_activity_index']) && $companyConfigs['show_activity_index']): ?>
                <li class="<?php echo ($_this_controller == 'activities' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/index/" . @$company_id . "/") ?>"><?php __("Management") ?></a></li>
            <?php endif; endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm' || $hasManager) : ?>
                <!--li class="<?php //echo ($_this_controller == 'activities' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php //echo $html->url("/activities/manage/" . @$company_id) ?>"><?php //__("View") ?></a></li-->
                <li class="<?php echo ($activatedViewWhenClickDetail == true) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/manage/" . @$company_id . "/") ?>"><?php __("View") ?></a>
                    <?php if($activatedViewWhenClickDetail == true):?>
                    <ul>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && ($this->params['action'] == 'teams_yes' || $this->params['action'] == 'teams')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/teams/" . @$activity_id) ?>"><?php __("Teams") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && ($this->params['action'] == 'index' || $this->params['action'] == 'import_csv')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/index/" . @$activity_id) ?>"><?php __("Tasks") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && $this->params['action'] == 'visions') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/visions/" . @$activity_id) ?>"><?php __("Staffing+") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && $this->params['action'] == 'dash_board') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/dash_board/" . @$activity_id) ?>"><?php __("DashBoard") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($check_budget_actis_AC == true) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Budget") ?></a>
                            <ul>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_synthesis') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Synthesis") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_sales') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_sales/index/" . @$activity_id) ?>"><?php __("Sales") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_internals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_internals/index/" . @$activity_id) ?>"><?php __("Internal Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_externals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_externals/index/" . @$activity_id) ?>"><?php __("External Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_provisionals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_provisionals/index/" . @$activity_id) ?>"><?php __("Provisional") ?></a>
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
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && ($this->params['action'] == 'teams_yes' || $this->params['action'] == 'teams')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/teams/" . @$activity_id) ?>"><?php __("Teams") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && ($this->params['action'] == 'index' || $this->params['action'] == 'import_csv')) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/index/" . @$activity_id) ?>"><?php __("Tasks") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && $this->params['action'] == 'visions') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/visions/" . @$activity_id) ?>"><?php __("Staffing+") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($_this_controller == 'activity_tasks' && $this->params['action'] == 'dash_board') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_tasks/dash_board/" . @$activity_id) ?>"><?php __("DashBoard") ?></a>
                        </li>
                        <li>
                            <a class="<?php echo ($check_budget_actis_AC == true) ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Budget") ?></a>
                            <ul>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_synthesis') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_synthesis/index/" . @$activity_id) ?>"><?php __("Synthesis") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_sales') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_sales/index/" . @$activity_id) ?>"><?php __("Sales") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_internals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_internals/index/" . @$activity_id) ?>"><?php __("Internal Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_externals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_externals/index/" . @$activity_id) ?>"><?php __("External Cost") ?></a>
                                </li>
                                <li>
                                    <a class="<?php echo ($_this_controller == 'activity_budget_provisionals') ? "wd-current-child" : "" ?>" href="<?php echo $html->url("/activity_budget_provisionals/index/" . @$activity_id) ?>"><?php __("Provisional") ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <?php endif;?>
                </li>
            <?php endif; ?>
            <?php if ((isset($companyConfigs['active_team_workload']) && $companyConfigs['active_team_workload']==1)&&($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($_this_controller == 'team_workloads' && $this->params['action'] == 'index')? "wd-current" : "" ?>"><a id="add_team_workload_news_menu"><?php __("Team Workload") ?></a></li>
            <?php endif;?>
            <?php if ((isset($companyConfigs['active_team_workload_plus']) && $companyConfigs['active_team_workload_plus']==1) && ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($_this_controller == 'team_workloads' && $this->params['action'] == 'plus')? "wd-current" : "" ?>"><a id="add_team_workload_plus"><?php __("Team Workload +") ?></a></li>
            <?php endif; ?>
			 <?php
                if( $role == 'admin' ): ?>
				<?php if(isset($companyConfigs) && isset($companyConfigs['show_activity_export_excel']) && $companyConfigs['show_activity_export_excel'] == 1):?>
                <li>
                    <a href="javascript:;" id="show-export-dialog" title=""><?php __('Export') ?></a>
                </li>
            <?php endif; endif; ?>
        </ul>
	</div>
    <?php } elseif ($activityScreen && $enableRMS == true) {
                $profitOfBudgetInActivity = ($role == 'admin') ? -1 : $profit['id'];
      ?>
	<div id = 'menu-wrapper' class="menu-wrapper not-boxed">
		<span onclick="onPrevous()" class="scroll-menu scroll-left">
				<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				<defs>
					<style>
					  .cls-1 {
						fill-rule: evenodd;
					  }
					</style>
				  </defs>
				  <path id="Forme_8" data-name="Forme 8" class="cls-1" d="M1563.65,144.758l-5.24,5.24,5.31,5.308h0A1,1,0,0,1,1563,157a0.988,0.988,0,0,1-.69-0.28h0l-6-6h0A0.991,0.991,0,0,1,1556,150v0a1,1,0,0,1,.31-0.719h0l6-6v0A0.991,0.991,0,0,1,1563,143,1,1,0,0,1,1563.65,144.758Z" transform="translate(-1556 -143)"/>
				</svg>
			</span>
			<span onclick="onNext()" class="scroll-menu scroll-right">
				<svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14">
				  <defs>
					<style>
					  .cls-1 {
						fill: #666;
						fill-rule: evenodd;
					  }
					</style>
				  </defs>
				  <path id="arrow" class="cls-1" d="M1916.35,94.758l5.24,5.239-5.31,5.307h0A1,1,0,0,0,1917,107a0.985,0.985,0,0,0,.69-0.281h0l6-6h0A0.993,0.993,0,0,0,1924,100v0a1,1,0,0,0-.31-0.72h0l-6-6v0A0.99,0.99,0,0,0,1917,93a1,1,0,0,0-1,1A0.977,0.977,0,0,0,1916.35,94.758Z" transform="translate(-1916 -93)"/>
				</svg>
			</span>
		<ul id="sub-nav-activity" class="menu-list clear-fix">
            <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'request') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/request/?id=". $employee_id."&profit=" .  $profit['id']) ?>"><?php __("Requests") ?></a></li>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager) : ?>
                <?php /* <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manage?profit=" .  $profit['id']) ?>"><?php __("Forecasts") ?></a></li> */?>
                <?php if($ACBudgetSettings):?>
                <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'budget') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/budget?profit=" .  $profitOfBudgetInActivity) ?>"><?php __("Budget") ?></a></li>
                <?php endif; ?>
                <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'response') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/response?profit=" .  $profit['id']) ?>"><?php __("Validation") ?></a></li>
                <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'to_validate') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/to_validate?profit=" .  $profit['id']) ?>"><?php __("To Validate") ?></a></li>
                <li class="<?php echo ($_this_controller == 'activity_forecasts' && $this->params['action'] == 'not_sent_yet') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/not_sent_yet?profit=" .  $profit['id']) ?>"><?php __("Not send yet") ?></a></li>
				<?php if(isset($companyConfigs['show_activity_forecast']) && $companyConfigs['show_activity_forecast']){ ?>
                <li class="<?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'manages') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts/manages?profit=" .  $profit['id']) ?>"><?php __("Forecasts+") ?></a></li>
				<?php } ?>
				<?php if(!(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast'] == 1)) { ?>
				<?php if(isset($companyConfigs['show_activity_forecast_plus']) && $companyConfigs['show_activity_forecast_plus']){ ?>
				<li class="<?php echo ($this->params['controller'] == 'activity_forecasts_preview' && $this->params['action'] == 'manages') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activity_forecasts_preview/manages") ?>"><?php __("Forecasts++") ?></a></li>
				<?php }
				} ?>
            <?php endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr') : ?>
				<?php if(isset($companyConfigs['show_activity_index']) && $companyConfigs['show_activity_index']): ?>
                <li class="<?php echo ($_this_controller == 'activities' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/index/" . @$company_id . "/") ?>"><?php __("Management") ?></a></li>
            <?php endif; endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm' || $hasManager) : ?>
				<?php if(isset($companyConfigs['show_activity_view']) && $companyConfigs['show_activity_view']): ?>
					<li class="<?php echo ($_this_controller == 'activities' && $this->params['action'] == 'manage') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/manage/" . @$company_id . "/") ?>"><?php __("View") ?></a></li>
            <?php endif; endif; ?>
            <?php if ($is_sas || $role == "admin" || $role == 'hr' || $role == 'pm') : ?>
                <?php if(!empty($activitySettings) && $activitySettings['ActivitySetting']['show_activity_review'] == 1):?>
                    <li class="<?php echo (($_this_controller == 'activities' && ($this->params['action'] == 'review' || $this->params['action'] == 'detail'))) ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/activities/review/" . @$company_id . "/") ?>"><?php __("Review") ?></a></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ((isset($companyConfigs['active_team_workload'])&&$companyConfigs['active_team_workload']==1)&&($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($_this_controller == 'team_workloads' && $this->params['action'] == 'index')? "wd-current" : "" ?>"><a id="add_team_workload_news_menu"><?php __("Team Workload") ?></a></li>
            <?php endif; ?>
            <?php if ((isset($companyConfigs['active_team_workload_plus']) && $companyConfigs['active_team_workload_plus']==1) && ($is_sas || $role == "admin" || $role == 'hr' || ($role == 'pm' && $hasManager) || $hasManager )) : ?>
                <li class="<?php echo ($_this_controller == 'team_workloads' && $this->params['action'] == 'plus')? "wd-current" : "" ?>"><a id="add_team_workload_plus"><?php __("Team Workload +") ?></a></li>
            <?php endif; ?>
            <?php
                if( $role == 'admin' ): ?>
				<?php if(isset($companyConfigs) && isset($companyConfigs['show_activity_export_excel']) && $companyConfigs['show_activity_export_excel'] == 1):?>
                <li>
                    <a href="javascript:;" id="show-export-dialog" title=""><?php __('Export') ?></a>
                </li>
            <?php endif; endif; ?>
        </ul>
	</div>
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
#form-activity-export .wd-input .multiSelect  span{
	width: calc(100% - 40px);
}
</style>
<script>
var menuHeight = $('#wd-top-nav ul li ul').height();
if(menuHeight > 30) {
    $('#wd-top-nav').css({'height':'70px'});
}
</script>

<?php } ?>
<style>
#form-activity-export .wd-input .multiSelect  span{
	width: calc(100% - 40px) !important;
}
</style>
<!-- dialog_vision_staffing++++++++++++++++++ -->
<?php
    // list cac controler se hien thi vision staffing+ o activity.
    $displayVisions = array(
        'activity_forecasts', 'activities', 'activity_tasks', 'activity_budget_synthesis', 'activity_budget_sales',
        'activity_budget_internals', 'activity_budget_externals', 'activity_budget_provisionals', 'team_workloads'
    );
    if(in_array($_this_controller, $displayVisions)):
    /**
     * Vision staffing+: code cho phan dialog
     */
    $menuListFamilies = ClassRegistry::init('Family')->find('list', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employee_info['Company']['id'],'parent_id'=>null)
                        ));
    $PCModel = ClassRegistry::init('ProfitCenter');
	$isAdmin = $employee_info['Role']['name'] == 'admin' || $employee_info['Role']['name'] == 'hr' || $is_sas;
	$menuListProfitCenters = array();
	if ($isAdmin) {
            $menuListProfitCenters = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']),null,null,' -- ',-1);
	} elseif (!empty($profit)) {
		$pdcBKProfit = ClassRegistry::init('ProfitCenterManagerBackup')->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $employee_id
			),
			'fields' => array('profit_center_id', 'profit_center_id')
		));
		$pdcProfit = ClassRegistry::init('ProfitCenter')->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'manager_id' => $employee_id,
			),
			'fields' => array('id')
		));
		$pdcProfit = array_unique(array_merge($pdcProfit, $pdcBKProfit));
		$pathsTemp = array();
		foreach ( $pdcProfit as $pr){
			$pcChild = $PCModel->children($pr);
			if(!empty($pcChild)){
				$pcChild = Set::classicExtract($pcChild,'{n}.ProfitCenter.id');
				$pathsTemp = array_merge($pathsTemp, $pcChild);
			}
		}	
		$pdcProfit = array_unique(array_merge($pdcProfit, $pathsTemp));
		$menuListProfitCenters = $PCModel->generatetreelist(array('id' => $pdcProfit), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
	}
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
                    // 'value' => '01-10-2019',
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
					// 'value' => '31-10-2019',
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
<div id="overlay-container" rels="1">
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
<div id="activity-export-message-dialog" style="display: none" class="buttons form" title="Report">
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
    <p id="exporting_wait"><?php __('Please wait, writing files...');?></p>
    <div id="exporting_buttons" class="type_buttons wd-submit wd-submit-row" style="padding: 24px !important; display: none;">
		<a href="javascript:void(0)" class="btn-form-action btn-cancel" id="export-report-cancel-dialog"><?php __("Cancel") ?></a>
		<a href="javascript:void(0)" class="btn-form-action btn-ok btn-right" id="csv-download-dialog" title="<?php __("Download CSV");?>"><?php __("CSV") ?></a>
		<a href="javascript:void(0)" class="btn-form-action btn-right" id="export-report-download-dialog" title="<?php __("Download Excel");?>"><?php __("Excel"); ?></a>
    </div>
</div>
<?php
    $dateType = isset($arrGetUrl['aDateType']) ? $arrGetUrl['aDateType'] : 3 ;
	$_start = !empty($_start) ? $_start : time();
	$_end = !empty($_end) ? $_end : time();
	if(isset($arrGetUrl['smonth'])) $smonth=$arrGetUrl['smonth'];
	else $smonth= !empty($_start) ? date('m', $_start) : date('m', time());
	if(isset($arrGetUrl['syear'])) $syear=$arrGetUrl['syear'];
	else $syear= !empty($_start) ? date('Y', $_start) : date('Y', time());
	$emonth= !empty($_start) ? date('m', $_end) : date('m', time());
	$eyear=  !empty($_start) ? date('Y', $_end) : date('Y', time());
	$priorities = ClassRegistry::init('ProjectPriority')->find('list', array(
		'recursive' => -1,
		'conditions' => array(
			'company_id' => $employee_info['Company']['id']
		),
		'fields' => array('id', 'priority')
	));
	$priorities = empty( $priorities) ? array() : $priorities;
?>
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
<div id="dialog_team_workload_plus" class="buttons" style="display: none;">
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
			<div class="form-multiselect-alert" style="display: none">
				<i class="icon-info"></i>
				<span class="team-plus-alert-content">
					<?php echo __("For a better experience and reactivity, it's better to select less than 10 teams"); ?>
				</span>
			</div>
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
                        //  'name' => 'vsShowBy',
                        //  'fieldset' => false,
                        //  'legend' => false,
                        //  //'checked' => $dateType == 1 ? 'checked' : '',
                        //  'value' => $dateType));
                        ?>
                        <?php
                        // echo $this->Form->radio('ActivityDateType', array('week' => __("Week", true)), array(
                        //  'name' => 'vsShowBy',
                        //  'fieldset' => false,
                        //  'legend' => false,
                        //  //'checked' => $dateType == 2 ? 'checked' : '',
                        //  'value' => $dateType));
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
<script type="text/javascript">
    var activeMenu = <?php echo json_encode($active) ?>;
    var companyConfigs = <?php echo json_encode($companyConfigs) ?>;
    var nanobar1 = new Nanobar1();
    var auto;
    var done = false;
	var share_uri = <?php echo json_encode($this->Html->url('/shared/'.$employee_id.'/')); ?>;
    $(function(){
		var filter_default = {
			spmonth: "",
			spyear: "",
			epmonth: "",
			epyear: "",
			date_type: "",
			end_month: "",
			end_year: "",
			show_na: "",
			start_month: "",
			start_year: "",
			summary: "",
			view_by: ""
		};
		var vs_filter = JSON.parse(<?php echo json_encode(!empty($vs_filter) ? $vs_filter : "[]");?>);
		if((typeof vs_filter == "undefined")||(vs_filter == null) ||((!vs_filter.length)&&(vs_filter.length == 0))){
			// console.log(1);
			var vsFilter = new $.z0.data(filter_default);
		}else{
			// console.log(2);
			var vsFilter = new $.z0.data(vs_filter);
		}
		// console.log(vs_filter);
		// console.log(vsFilter);
        if( activeMenu == 4 ){
            $('#nav-guide').prop('href', '/admin_guides/');
        }
        var _hasSub = false;
        $('.menu-list >li').hover(
			function(){
				_hasSub = $(this).find('ul').length;
				if( _hasSub ){
					$(this).addClass('hover');
					$(this).closest('.menu-list-container').stop().animate( {height: ($(this).find('ul:first').height() + $(this).height()) }, 100);
				}
			}, function(){
				$(this).removeClass('hover');
				$(this).closest('.menu-list-container').stop().css('height', '');
				_hasSub = false;
			}
		);
        //apply vs filter
        var _controller = <?php echo json_encode($_this_controller) ?>;
        //store vs filter (data changed)
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
        function validateDate(oldValue,elm) {
            var startMonth = $('#aStartMonth').val();
            var startYear = $('#aStartYear').val();
            var endMonth = $('#aEndMonth').val();
            var endYear = $('#aEndYear').val();
            var $id = elm.id;
            var error = 0;
            //var oldValue = elm.options[elm.selectedIndex].value;
            if( endYear - startYear > 5) {
                error = 1;
            } else if(startYear > endYear) {
                error = 1;
            } else if(startMonth == endMonth && startYear > endYear) {
                error = 1;
            } else if(startMonth > endMonth && startYear >= endYear) {
                error = 1;
            }
            if(error == 1) {
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
                if($(this).val() == 2) {
                    if($('#aStartDate').val() != '') {
                        var currentDateWeek = $('#aStartDate').datepicker('getDate');
                        currentDateWeek = currentDateWeek.getDate() - currentDateWeek.getDay(); // First day is the day of the month - the day of the week
                        $('#aStartDate').datepicker('setDate',currentDate);
                    }
                    if($('#aEndDate').val() != '') {
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
                } else {
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

            $(".cancel").on('click',function(){
                $("#dialog_team_workload_news_menu").dialog().dialog('close');
            });
            $("#add_team_workload_plus").on('click',function(){
                $("#dialog_team_workload_plus").dialog().dialog({
					  open: function( event, ui ) {
							var team_workload_selected = $('#filter_team_plus').find('li.selected').length;
							if(team_workload_selected > 10){
								$('#dialog_team_workload_plus').find('.form-multiselect-alert').show();
							}
					  }
				});
				
                return false;
            });
            $(".cancel").on('click',function(){
                $("#dialog_team_workload_plus").dialog().dialog('close');
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
            $('#ok_sum_team_plus').click(function(){
                var list = $('#teamPrio');
                list.multipleSelect('disable');
                vsFilter.set('spmonth', $('#teamPSm').val());
                vsFilter.set('spyear', $('#teamPSy').val());
                vsFilter.set('epmonth', $('#teamPEm').val());
                vsFilter.set('epyear', $('#teamPEy').val());
                var listpc = $('#teamP').multipleSelect('getSelects');
                vsFilter.set('teamP', listpc);
                vsFilter.set('teamPrio', $('#teamPrio').multipleSelect('getSelects'));
				console.log(vsFilter);
                $.z0.History.save('vs_filter', vsFilter);
                //submit
                setTimeout(function(){
                    $("#form_team_workload_plus").submit();
                }, 1000);
                list.multipleSelect('enable');
            });
            $("#reset_sum").on('click', function(){
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
                $("#dialog_vision_staffing_news_menu").dialog().dialog('open');
                return false;
            });
            $("#reset_sum_team").on('click', function(){
                //RESET
                $('#teamPriority').multipleSelect('setSelects', []);
                vsFilter.set('teampriority', []);
                $("#dialog_team_workload_news_menu").dialog().dialog('open');
                return false;
            });
            $("#reset_sum_team_plus").on('click', function(){
                //RESET
                $('#teamP').multipleSelect('setSelects', []);
                $('#teamPrio').multipleSelect('setSelects', []);
                vsFilter.set('teamPrio', []);
                $("#dialog_team_workload_plus").dialog().dialog('open');
                return false;
            });
            $('#export-reset_sum').on('click',function(){
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
                    url : '<?php echo $html->url(array('controller' => 'activity_forecasts_preview', 'action' => 'getEmployeeOfProfitCenterUsingModuleExportActivity')); ?>',
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
             * create  CSV File for export
             */
            createExportFile = function(filename){
                var results = '';
                $.ajax({
                    url : '<?php echo $html->url(array('controller' => 'activity_forecasts_preview', 'action' => 'createExportFile')); ?>',
                    cache : false,
                    type : 'POST',
                    async: false,
                    data: {
                        data: {
							filename: filename
						}
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
			/* 
			*
			*/
			function exportActivityFollowEmployee(list_employees, index, options){
				// return;
				$.ajax({
					url : '<?php echo $html->url(array('controller' => 'activity_forecasts_preview', 'action' => 'exportActivityFollowEmployee')); ?>',
					cache : false,
					type : 'POST',
					async: true,
					dataType: 'json',
					data: {
						start_date: $('#export-start-date').val(),
						end_date: $('#export-end-date').val(),
						employee_id: list_employees[index],
						day_of: $('#export-list-dayOff').val(),
						display : options.display,
						merge: options.mergeFile,
						filename: options.filename,
					},
					success: function(data){
						if(data.result){
							var totalRecord = $('#totalRecords').html();
							totalRecord = (totalRecord === '...') ? 0 : parseInt(totalRecord);
							totalRecord = parseInt(totalRecord) + parseInt(data.totalRecord);
							setTimeout(function(){
								$('#totalRecords').html(parseInt(totalRecord));
								$('#fileExport').html(parseInt($('#fileExport').html()) + 1);
								var checkFile = parseInt($('#fileExport').html());
								if(checkFile == list_employees.length){
									$('#exporting_wait').hide();
									$('#exporting_buttons').show();
									var count_records = parseInt($('#totalRecords').text());
									if( count_records){
										$('#csv-download-dialog').show();
										$('#export-report-download-dialog').show();
									}else{
										$('#csv-download-dialog').hide();
										$('#export-report-download-dialog').hide();
									}
								}
							}, 50);
							if( index) exportActivityFollowEmployee(list_employees, --index, options);
						}
					},
				});
			}
			
            var mergeFile = 'no';
            $('#export-submit-dialog').click(function(){
				if( $(this).hasClass('disabled')) return;
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
						var _now = new Date();
						var filename = 'activity_export_' + _now.getHours() + '_' +_now.getMinutes() + '_' +_now.getSeconds() + '_' +_now.getDate() + '_' + (parseInt(_now.getMonth())+1) + '_' +_now.getFullYear() + '.csv';
						$('#export-report-download-dialog').data('filename', filename);
						_file = createExportFile(filename);
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
								var options = {
									display : display,
									merge: mergeFile,
									filename: filename,
								}
								exportActivityFollowEmployee(employSends, employCount, options);
                            }
                        } else {
                            $('#totalFile').html(totalFile);
                            $('#totalFiles').html(totalFile);
                            $('#totalEmploys').html(employIds.length);
                            $('#exporting_wait').show();
							$.ajax({
								url : '<?php echo $html->url(array('controller' => 'activity_forecasts_preview', 'action' => 'exportActivityFollowEmployee')); ?>',
								cache : false,
								type : 'POST',
								async: true,
								dataType: 'json',
								data: {
									start_date: $('#export-start-date').val(),
									end_date: $('#export-end-date').val(),
									employee_id: employIds,
									day_of: $('#export-list-dayOff').val(),
									display :display,
									filename: filename,
								},
								success: function(data){
									// var _data = JSON.parse(data);
									var _data = data;
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
												var count_records = parseInt($('#totalRecords').text());
												if( count_records){
													$('#csv-download-dialog').show();
													$('#export-report-download-dialog').show();
												}else{
													$('#csv-download-dialog').hide();
													$('#export-report-download-dialog').hide();
												}
											}
										}, 50);
									}
								}
							});
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
            function writeFileInServer(countFileDownload){
                 countFileDownload = countFileDownload - 1;
                 setTimeout(function(){
                    $.ajax({
                        url : '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'zipFileModuleExportActivity')); ?>' + '/' + countFileDownload + '/' + mergeFile,
                        cache : false,
                        async: true,
                        success: function(data){
							if(data){
								var urlDown = JSON.parse(data);
								if(urlDown && countFileDownload == 0){
									window.location.href = urlDown;
									$('#overlay-container').hide();
								} else {
									writeFileInServer(countFileDownload);
								}
							}
                        }
                    });
                }, 100);
            }
			function downloadExcelFile(filename){
				$.ajax({
					url : '<?php echo $html->url(array('controller' => 'activity_forecasts_preview', 'action' => 'createExcelFile')); ?>',
					cache : false,
					// async: true,
					dataType: 'json',
					data: {
						filename: filename,
					},
					success: function(data){
						console.log(data.result);
						if(data.result == true){
							window.location.href = data.downloadURL;
						}else{
							alert(data.message);
						}
					},					
					complete: function(){
						$('#overlay-container').hide();
					}
				});
			}
            $('#csv-download-dialog').on('click', function(){
				var filename = $('#export-report-download-dialog').data('filename');
				if(filename){
					eMessageDialog.dialog('close');
					window.location.href = share_uri + filename;
				}
			});
            $('#export-report-download-dialog').click(function(){
                eMessageDialog.dialog('close');
                $('#overlay-container').show();
				var filename = $(this).data('filename');
				if( filename ) downloadExcelFile(filename);
				
                /**
                 * Tinh tong so record se export ra trong lan request nay.
                 */
                // var totalRecordExport = countRecordExporting();
                // var totalFileDownload = Math.ceil(totalRecordExport/10000);
                // if(totalFileDownload != 0){
                    // writeFileInServer(totalFileDownload);
                // } else {
                    // $('#overlay-container').hide();
                // }
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
				ajaxLoading: function(){
					$('#export-submit-dialog').addClass('disabled');
				},
				ajaxLoaded: function(){
					$('#export-submit-dialog').removeClass('disabled');
				},
                loadingText : 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });
            /*end dialog export*/
            /* table .end */
            var createDialog = function(){

                $('#dialog_vision_staffing_news_menu').dialog({
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
                $('#dialog_team_workload_plus').dialog({
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
                        updatePcAndResource(multiPcTeamPlus, $('#aEmployeteam'), verifyLimitTeam);
                    }, 1000);
                },
				
            });
			
			function verifyLimitTeam(loader, filler, data){
				var _form_alert = $(loader).closest('form').find('.form-multiselect-alert');
				var arrDistinct = [];
				if(data && data.pc){
					 $(data.pc).each(function(index, item) {
						if ($.inArray(item, arrDistinct) == -1) arrDistinct.push(item);
					});
					if(arrDistinct.length > 10){
						_form_alert.show();
					}else{
						_form_alert.hide();
					}
				}else{
					_form_alert.hide();
				}
			}
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
    $('#open-main-menu').click(function(){
        if( $('#wd-top-nav').hasClass('dissable-menu') ){
            $('#wd-top-nav').removeClass('dissable-menu');
            $.ajax({
                url: '<?php echo $html->url('/menus/saveHistoryForMenu') ?>',
                type: 'POST',
                data: {
                    data: 2
                },
            });
        } else {
            $('#wd-top-nav').addClass('dissable-menu');
            $.ajax({
                url: '<?php echo $html->url('/menus/saveHistoryForMenu') ?>',
                type: 'POST',
                data: {
                    data: 1
                },
            });
        }
    });
	
	var sub_item = $('.menu-wrapper .menu-list > li');
	if(sub_item.length > 0){
		sub_width = 0;
		$.each(sub_item , function(){
			sub_width += $(this).width();
		});
		$('.menu-wrapper .menu-list').width(sub_width + 5);
	}
	
</script>
<script>
	var sub_menu = $('#menu-wrapper .menu-list');
	$('.scroll-left').hide();
	_w_sub_menu_wrap = $('#menu-wrapper').width();
	_w_sub_menu = sub_menu.width();
	if(_w_sub_menu_wrap >= _w_sub_menu){
		$('.scroll-right').hide();
	}
	
	function onNext() {
		sub_menu = $('#menu-wrapper .menu-list');
		_w_sub_menu_wrap = $('#menu-wrapper').width();
		_w_sub_menu = sub_menu.width();
		var offset = sub_menu.offset();
		var position = sub_menu.position();
		if(_w_sub_menu - _w_sub_menu_wrap + position.left > -100){
			sub_menu.offset({left: offset.left - 100});
			$('.scroll-left').show();
		}
		else{
			$('.scroll-right').hide();
			$('.scroll-left').show();
		}
	}

	function onPrevous() {
		sub_menu = $('#menu-wrapper .menu-list');
		var offset = sub_menu.offset();
		var position = sub_menu.position();
		if(position.left < 0){ 
			sub_menu.offset({left: offset.left + 100});
			$('.scroll-right').show();
		}else{
			$('.scroll-right').show();
			$('.scroll-left').hide();
		}
	}
</script>