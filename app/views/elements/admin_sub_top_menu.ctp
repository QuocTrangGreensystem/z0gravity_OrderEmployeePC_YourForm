<?php
    $controller = $this->params['controller'];
    $menuEmployees = $menuProjects = $menuLicenses = $menuBudget = $menuSecurity = $menuAbsence = $menuActivity = $menuVersion = $menuTranslation = $menuStaffing = $menuUpload = $menuSql = $menuTicket = false;
    $listMenuEmployees = array(
        'cities','colors', 'countries', 'companies', 'contract_types', 'profit_centers', 'company_configs', 'externals', 'profile_project_managers', 'employees', 'user_views', 'company_column_defaults', 'project_amrs_preview', 'customer_logos'
    );
    $listMenuProjects = array(
        'project_phases', 'project_statuses', 'project_priorities', 'currencies', 'project_types', 'project_sub_types',
        'project_complexities', 'project_created_values', 'project_functions', 'project_phase_statuses',
        'project_risk_severities', 'project_risk_occurrences', 'project_issue_severities', 'project_issue_statuses',
        'project_livrable_categories', 'project_evolution_types', 'project_evolution_impacts', 'project_amr_programs',
        'project_amr_sub_programs', 'project_amr_categories', 'project_amr_sub_categories', 'project_amr_statuses',
        'project_amr_cost_controls', 'project_amr_organizations', 'project_amr_plans', 'project_amr_perimeters',
        'project_amr_risk_controls', 'project_amr_problem_controls', 'menus', 'project_settings', 'project_acceptance_types',
        'admin_task', 'dependencies', 'kpi_settings', 'project_datasets', 'vision_task_exports', 'expectation_translations', 'expectations', 'expectation_datasets', 'expectation_colors'
    );
    $listMenuLicenses = array(
        'liscenses'
    );
    $listMenuBudgets = array(
        'budget_settings', 'budget_customers', 'budget_providers', 'budget_types', 'budget_funders'
    );
	$listBudgetPass = array('Total_Cost', 'Internal_Cost', 'External_Cost', 'Budget_Investment', 'Budget_Operation', 'Finance_Investment', 'Finance_Operation');
    $listSecurity = array('security_settings', 'action_logs', 'tasks', 'auth_codes', 'access_tokens', 'sso_logins');
    $listAbsences = array('absences', 'workdays', 'response_constraints', 'holidays');
    $listActivities = array('activity_columns', 'activity_families', 'activity_settings', 'activity_exports' ,'periods');
    $listVersions = array('versions');
	
	$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
	if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
		$isAdminSas = 1;
	}else{
		$isAdminSas = 0;
	}
	if(!empty($employee_info['CompanyEmployeeReference'])){
		$company_id = $employee_info['CompanyEmployeeReference']['company_id'];
	}else{
		$company_id = '';
	}
	if( (in_array($controller, $listMenuEmployees) || ($controller == 'employees' && $this->params['action'] == 'profile')) && ($this->params['action'] != 'icon') ){
        $menuEmployees = true;
    }
    if( in_array($controller, $listMenuProjects) || ($this->params['controller'] == 'project_importers' && $this->params['action'] == 'index') || ($this->params['controller'] == 'company_configs' && $this->params['action'] == 'icon') || ($controller == 'translations' && ($this->params['pass'][0] == 'Details'))){
        $menuProjects = true;
    }
    if(in_array($controller, $listMenuLicenses)){
        $menuLicenses = true;
    }
    if(in_array($controller, $listMenuBudgets) || (!empty($this->params['pass']) && in_array($this->params['pass'][0],$listBudgetPass))){
        $menuBudget = true;
    }
    if(in_array($controller, $listSecurity)  || ($this->params['controller'] == 'project_importers' && $this->params['action'] == 'import_excel') ){
        $menuSecurity = true;
    }
    if(in_array($controller, $listAbsences)){
        $menuAbsence = true;
    }
    if(in_array($controller, $listActivities) || ($controller == 'activity_forecasts' && $this->params['action'] == 'import_timesheet')){
        $menuActivity = true;
    }
    if(in_array($controller, $listVersions)){
        $menuVersion = true;
    }
    if( $controller == 'translations' && (($this->params['pass'][0] != 'Details') && !in_array($this->params['pass'][0],$listBudgetPass))){
        $menuTranslation = true;
    }
    if( $controller == 'staffing_systems'){
        $menuStaffing = true;
    }

    if( $controller == 'system_configs'){
        $menuUpload = true;
    }
    if( $controller == 'sql_manager'){
        $menuSql = true;
    }
    if( substr($controller, 0, 6) == 'ticket' && $controller != 'tickets' ){
        $menuTicket = true;
    }
	$displayProvider = ClassRegistry::init('Translation')->find('all', array(
		'recursive' => -1,
		'conditions' => array(
			'page' => 'External_Cost',
			'field' => 'budget_provider_id'
		),
		'fields' => array('*'),
		'joins' => array(
			array(
				'table' => 'translation_settings',
				'alias' => 'TranslationSetting',
				'conditions' => array(
					'Translation.id = TranslationSetting.translation_id',
					'TranslationSetting.company_id' => $company_id,
					'TranslationSetting.show' => 1
				),
				'type' => 'inner'
			)
		)
	));
	$displayFunder = ClassRegistry::init('Translation')->find('all', array(
		'recursive' => -1,
		'conditions' => array(
			'page' => 'Internal_Cost',
			'field' => 'funder_id'
		),
		'fields' => array('*'),
		'joins' => array(
			array(
				'table' => 'translation_settings',
				'alias' => 'TranslationSetting',
				'conditions' => array(
					'Translation.id = TranslationSetting.translation_id',
					'TranslationSetting.company_id' => $company_id,
					'TranslationSetting.show' => 1
				),
				'type' => 'inner'
			)
		)
	));
	$displayType = ClassRegistry::init('Translation')->find('all', array(
		'recursive' => -1,
		'conditions' => array(
			'page' => 'External_Cost',
			'field' => 'budget_type_id'
		),
		'fields' => array('*'),
		'joins' => array(
			array(
				'table' => 'translation_settings',
				'alias' => 'TranslationSetting',
				'conditions' => array(
					'Translation.id = TranslationSetting.translation_id',
					'TranslationSetting.company_id' => $company_id,
					'TranslationSetting.show' => 1
				),
				'type' => 'inner'
			)
		)
	));
	$displaySynthesis = ClassRegistry::init('Menu')->find('count', array(
		'recursive' => -1,
		'conditions' => array(
			'company_id' => $company_id,
			'model' => 'project',
			'controllers' => 'project_budget_synthesis',
			'functions' => 'index',
			'display' => 1,
		)
	));
	$displayInternal = ClassRegistry::init('Menu')->find('count', array(
		'recursive' => -1,
		'conditions' => array(
			'company_id' => $company_id,
			'model' => 'project',
			'controllers' => 'project_budget_internals',
			'functions' => 'index',
			'display' => 1,
		)
	));
	$displayExternal = ClassRegistry::init('Menu')->find('count', array(
		'recursive' => -1,
		'conditions' => array(
			'company_id' => $company_id,
			'model' => 'project',
			'controllers' => 'project_budget_externals',
			'functions' => 'index',
			'display' => 1,
		)
	));
	$displayFinancement = ClassRegistry::init('Menu')->find('count', array(
		'recursive' => -1,
		'conditions' => array(
			'company_id' => $company_id,
			'model' => 'project',
			'controllers' => 'project_finances',
			'functions' => 'index_plus',
			'display' => 1,
		)
	));
	if($isAdminSas == 1){
		$link = '/budget_settings/';
	}elseif($displayProvider && $displayExternal == 1){
		$link = '/budget_providers/';
	}elseif($displayType && $displayExternal == 1){
		$link = '/budget_types/';
	}elseif($displayFunder && $displayInternal == 1){
		$link = '/budget_funders/';
	}elseif($displaySynthesis == 1){
		$link = '/translations/index/Total_Cost';
	}elseif($displayInternal == 1){
		$link = '/translations/index/Internal_Cost';
	}elseif($displayExternal == 1){
		$link = '/translations/index/External_Cost';
	}elseif($displayFinancement == 1){
		$link = '/translations/index/Finance';
	}
?>
<?php echo $this->Html->css('admin') ?>
<ul class="wd-item">
    <li class="<?php echo ($menuEmployees == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/cities/') ?>"><?php __('Employees') ?></a></li>
	
	<li class="<?php echo ($menuProjects == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/project_amr_programs/') ?>"><?php __('Projects') ?></a></li>
    
	<?php if ( ($employee_info['Employee']['is_sas'] == 1) || ($employee_info['Company']['module_license'] == 1) ) { ?>
		<li class="<?php echo ($menuLicenses == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/liscenses/') ?>"><?php __('Licenses') ?></a></li>
    <?php } ?>
	
	<?php if($isAdminSas == 1){?>
		<li class="<?php echo ($menuBudget == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/budget_settings/') ?>"><?php __('Budget') ?></a></li>
	<?php }elseif(isset($link)){?>
		<li class="<?php echo ($menuBudget == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url($link) ?>"><?php __('Budget') ?></a></li>
	<?php }?>
	
	<?php if($isAdminSas == 1){?>
		<li class="<?php echo ($menuSecurity == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/security_settings/') ?>"><?php __('Security') ?></a></li>
	<?php }else{?>
		<li class="<?php echo ($menuSecurity == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/action_logs/') ?>"><?php __('Security') ?></a></li>
	<?php }?>
	
	<?php if($isAdminSas == 1){?>
		<li class="<?php echo ($menuAbsence == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/absences/') ?>"><?php __('Absence') ?></a></li>
	<?php }else{?>
		<li class="<?php echo ($menuAbsence == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/holidays/') ?>"><?php __('Absence') ?></a></li>
	<?php }?>
	
    <?php if($isAdminSas == 1){?>
		<li class="<?php echo ($menuActivity == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/activity_columns/') ?>"><?php __('Activity') ?></a></li>
        <li class="<?php echo ($menuVersion == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/versions/') ?>"><?php __('Version') ?></a></li>
    <?php }?>
	
	<?php if($isAdminSas == 1){?>
		<li class="<?php echo ($menuTranslation == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/translations/index/KPI') ?>"><?php __('Translation') ?></a></li>
    <?php }else{}?>
	
	<?php if ( $enableTicket ){?>
        <li class="<?php echo ($menuTicket == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/ticket_profiles/') ?>"><?php __('Tickets') ?></a></li>
    <?php }?>
	
    <?php if (($employee_info['Employee']['is_sas'] == 1)){?>
        <li class="<?php echo ($menuUpload == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/system_configs/') ?>"><?php __('System Configuration') ?></a></li>
    <?php }?>
	
    <?php if($isAdminSas == 1){?>
        <li class="<?php echo ($menuStaffing == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/staffing_systems/') ?>"><?php __('Staffing system') ?></a></li>
    <?php }?>
	
    <?php if($isAdminSas == 1){?>
        <li class="<?php echo ($menuSql == true) ? 'wd-current' : '';?>"><a href="<?php echo $html->url('/sql_manager/') ?>"><?php __('SQL') ?></a></li>
    <?php }?>
</ul>
