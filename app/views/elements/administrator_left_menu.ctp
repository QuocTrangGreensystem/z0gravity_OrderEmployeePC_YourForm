<div class="wd-aside-left">
    <div id="accordion">
        <?php
        $emp_list = array('cities', 'colors','countries', 'companies', 'activities', 'contract_types','profit_centers', 'company_configs', 'externals', 'profile_project_managers', 'user_views', 'employees', 'project_amrs_preview', 'company_column_defaults', 'customer_logos');
        $liscen_list = array('liscenses');
        $budget_list = array('budget_settings', 'budget_providers', 'budget_types','budget_funders');
        $security_list = array('security_settings', 'action_logs', 'tasks', 'auth_codes', 'access_tokens', 'project_utilities', 'sso_logins');
        $absence_list = array('absences', 'workdays', 'response_constraints', 'holidays', 'attached_documents', 'config_capacities');
        $activity_list = array('activity_columns', 'activity_families', 'activity_settings', 'activity_exports', 'periods');
        $audit_list = array('audit_admins', 'audit_settings');
        $sale_list = array('sale_settings', 'sale_roles', 'sale_expenses', 'categories');
        $ticket_list = array('ticket_profiles', 'ticket_profile_permissions', 'ticket_statuses', 'ticket_metas', 'tickets');
        $controller = $this->params['controller'];
        $action = $this->params['action'];
        $is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
        $role = ($is_sas != 1) ? $employee_info['Role']['name'] : '';
		$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
		$langCode = Configure::read('Config.langCode');
		if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
			$isAdminSas = 1;
		}else{
			$isAdminSas = 0;
		}
		
		$displayCreatedValue = ClassRegistry::init('Menu')->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_created_vals',
                'functions' => 'index',
				'display' => 1,
            )
        ));
		$displayActivityExportExcel = ClassRegistry::init('CompanyConfig')->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company_id,
                'cf_name' => 'show_activity_export_excel',
				'cf_value' => 1
            )
        ));
		$displayImportTaskExcel = ClassRegistry::init('CompanyConfig')->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company_id,
                'cf_name' => 'can_import_task',
				'cf_value' => 1
            )
        ));
        if (in_array($controller, $ticket_list)) {
            $metaList = $this->requestAction('/ticket_metas/getAll');
        ?>
            <h3 class="head-title <?php echo ($controller == 'ticket_profiles') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/ticket_profiles/'); ?>"><?php __("Profiles"); ?></a></h3>
        <?php foreach($metaList as $key => $text): ?>
            <h3 class="head-title <?php echo ($controller == 'ticket_metas' && $action == 'index' && (isset($this->params['pass'][0]) && $this->params['pass'][0] == $key)) ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/ticket_metas/index/' . $key); ?>"><?php __($text) ?></a></h3>
        <?php endforeach ?>
            <h3 class="head-title <?php echo ($controller == 'ticket_statuses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/ticket_statuses/'); ?>"><?php __("Statuses"); ?></a></h3>
            <h3 class="head-title <?php echo ($controller == 'tickets') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/tickets/ticket_phone_number/'); ?>"><?php __("Phone numbers"); ?></a></h3>
        <?php
        } else if (in_array($this->params['controller'], $budget_list)
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Total_Cost')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Internal_Cost')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'External_Cost')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Finance')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Budget_Investment')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Budget_Operation')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Finance_Investment')
			||($this->params['controller'] == 'translations' && $this->params['pass'][0] == 'Finance_Operation')
		){
			
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
					'TranslationSetting.company_id' => $company_id,
					'TranslationSetting.show' => 1,
					'field' => 'funder_id',
					'OR' => array(
						'page' => 'Internal_Cost',
						'page' => 'External_Cost',
					),
				),
				'fields' => array('*'),
				'joins' => array(
					array(
						'table' => 'translation_settings',
						'alias' => 'TranslationSetting',
						'conditions' => array(
							'Translation.id = TranslationSetting.translation_id',
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
        ?>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'budget_settings' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/budget_settings/'); ?>"><?php __("Settings"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayProvider && $displayExternal == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'budget_providers' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/budget_providers/'); ?>"><?php __("Provider"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayType && $displayExternal == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'budget_types' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/budget_types/'); ?>"><?php __("Type of purchase"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayFunder && $displayInternal == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'budget_funders' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/budget_funders/'); ?>"><?php __("Funder"); ?></a></h3>
			<?php } ?>
			
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'budget_settings' && $this->params['action'] == 'fiscal') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/budget_settings/fiscal/'); ?>"><?php __("Fiscal year"); ?></a></h3>
			<?php } ?>
			
			<?php if($displaySynthesis == 1){?>
				<h3 class="head-title <?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'Total_Cost') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Total_Cost'); ?>"><?php __("Synthesis"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayInternal == 1){?>
				<h3 class="head-title <?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'Internal_Cost') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Internal_Cost'); ?>"><?php __("Internal Cost"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayExternal == 1){?>
				<h3 class="head-title <?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'External_Cost') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/External_Cost'); ?>"><?php __("External Cost"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayFinancement == 1){?>
				<h3 class="head-title"><?php __('Financement')?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'translations' && $this->params['action'] == 'index' && $this->params['pass'][0] == 'Finance') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Finance'); ?>"><?php __d(sprintf($_domain, 'Finance'), "Finance"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'translations' && $this->params['action'] == 'index' && $this->params['pass'][0] == 'Budget_Investment') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Budget_Investment'); ?>"><?php __d(sprintf($_domain, 'Budget_Investment'), "Budget Investment"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'translations' && $this->params['action'] == 'index' && $this->params['pass'][0] == 'Budget_Operation') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Budget_Operation'); ?>"><?php __d(sprintf($_domain, 'Budget_Operation'), "Budget Operation"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'translations' && $this->params['action'] == 'index' && $this->params['pass'][0] == 'Finance_Investment') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Finance_Investment'); ?>"><?php __d(sprintf($_domain, 'Finance_Investment'), "Finance Investment"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'translations' && $this->params['action'] == 'index' && $this->params['pass'][0] == 'Finance_Operation') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Finance_Operation'); ?>"><?php __d(sprintf($_domain, 'Finance_Operation'), "Finance Operation"); ?></a></li>
				</ul>
			<?php } ?>
			
        <?php
        } else if( $this->params['controller'] == 'translations' && $this->params['pass'][0] != 'Details' && $this->params['pass'][0] != 'Created_Value'){
			
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
			
            $company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
            $langCode = Configure::read('Config.langCode');
            $fields = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
			if($isAdminSas == 0){
				$translationPages = array();
			}else{
				// ob_clean();debug($translationPages);exit;
				unset($translationPages[1]);//not display Details
				unset($translationPages[21]);//not display Created_Value
				unset($translationPages[11]);//not display Synthesis
				unset($translationPages[12]);//not display Internal
				unset($translationPages[13]);//not display External
				unset($translationPages[19]);//not display Financement
				unset($translationPages[22]);//not display Budget_Investment
				unset($translationPages[23]);//not display Budget_Operation
				unset($translationPages[24]);//not display Finance_Investment
				unset($translationPages[25]);//not display Finance_Operation
			}
            foreach( $translationPages as $page ){
                if(strpos($page, 'Details') !== false || ($page == 'FY_Budget') || ($page == 'Purchase') || ($page == 'Sales') || ($page == 'Total_Cost')){
                    $pageSlug = Inflector::slug($page);
                    $_pageSlug = str_replace('Details', 'your_form', $pageSlug);
                    $_controllers = 'projects';
                    if(($page == 'FY_Budget') || ($page == 'Purchase') || ($page == 'Sales') || ($page == 'Total_Cost')){
                        $_pageSlug = 'index';
                        if($page == 'Sales') $_controllers = 'project_budget_sales';
                        if($page == 'FY_Budget') $_controllers = 'project_budget_fiscals';
                        if($page == 'Purchase') $_controllers = 'project_budget_purchases';
                        if($page == 'Total_Cost') $_controllers = 'project_budget_synthesis';
                    }
                    $menu = ClassRegistry::init('Menu')->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'model' => 'project',
                            'controllers' => $_controllers,
                            'functions' => $_pageSlug,
                        ),
                        'fields' => array('id', 'name_eng', 'name_fre'),
                        'order' => array('id' => 'DESC')
                    ));
                    $menu = !empty($menu) ? $menu['Menu'][$fields] : str_replace('_', ' ', $pageSlug);
                    ?>
                    <h3 class="head-title <?php echo $pageSlug == $currentPage ? 'wd-current' : '' ?>"><a href="<?php echo $html->url('/translations/index/' . $pageSlug); ?>"><?php echo $menu ?></a></h3>
                    <?php
                } else {
                    $pageSlug = Inflector::slug($page);
                    ?>
                    <h3 class="head-title <?php echo $pageSlug == $currentPage ? 'wd-current' : '' ?>"><?php echo $html->link(__(Inflector::humanize($page), true), '/translations/index/' . $pageSlug) ?></h3>
                    <?php
                }
            }
        } else if (in_array($this->params['controller'], $liscen_list)) {
        ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'liscenses' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/liscenses/'); ?>"><?php __("Project"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'liscenses' && $this->params['action'] == 'absence') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/liscenses/absence/'); ?>"><?php __("Absence"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'liscenses' && $this->params['action'] == 'activity') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/liscenses/activity/'); ?>"><?php __("Activity"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'liscenses' && $this->params['action'] == 'budget') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/liscenses/budget/'); ?>"><?php __("Budget"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'liscenses' && $this->params['action'] == 'audit') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/liscenses/audit/'); ?>"><?php __("Audit"); ?></a></h3>
        <?php
    } else if ((in_array($this->params['controller'], $emp_list) && ($this->params['action'] != 'icon')) || ($this->params['controller'] == 'employees' && $this->params['action'] == 'profile')) {
		
		$activityProfile = ClassRegistry::init('CompanyConfig')->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'company' => $company_id,
				'cf_name' => 'activate_profile',
				'cf_value' => '1'
			)
		));
            ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'cities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/cities/'); ?>"><?php __("Cities"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'countries') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/countries/'); ?>"><?php __("Countries"); ?></a></h3>
			<?php if($isAdminSas == 1){ ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'companies' && $this->params['action'] != 'clone_data') ? "wd-current" : "" ?>"><a href="javascript:void(0);"><?php __("Companies"); ?></a></h3>

				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'companies' && $this->params['action'] != 'clone_data') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/companies/'); ?>"><?php __("Companies"); ?></a></li>
					<?php if (($employee_info['Employee']['is_sas'] == 1)){ ?> 
						<li class="<?php echo ($this->params['controller'] == 'colors' && $this->params['action'] == 'login_setting') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url(array('controller' => 'colors', 'action' => 'login_setting')) ?>"><?php __("Picture login"); ?></a></li>
						<li class="<?php echo ($this->params['controller'] == 'colors' && $this->params['action'] == 'about_company') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url(array('controller' => 'colors', 'action' => 'about_company')) ?>"><?php __("About company"); ?></a></li>
						<li class="<?php echo ($this->params['controller'] == 'colors' && $this->params['action'] == 'testimonial') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url(array('controller' => 'colors', 'action' => 'testimonial')) ?>"><?php __("Testimonials"); ?></a></li>
					<?php } else { ?>
						<?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design']) { ?>
							<li class="<?php echo ($this->params['controller'] == 'colors' && $this->params['action'] == 'login_setting') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url(array('controller' => 'colors', 'action' => 'login_setting')) ?>"><?php __("Picture top menu"); ?></a></li>
						<?php } ?>
						<li class="<?php echo ($this->params['controller'] == 'colors'  && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/colors/') ?>"><?php __("Colors"); ?></a></li>
					<?php } ?>
				</ul>
				<?php if (($employee_info['Employee']['is_sas'] == 1)){ ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'companies' && $this->params['action'] == 'clone_data') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/companies/clone_data'); ?>"><?php __("Clone Data"); ?></a></h3>
				<?php } ?>
			<?php }?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'contract_types') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/contract_types/'); ?>"><?php __("Contract Types"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'profit_centers') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/profit_centers/') ?>"><?php __("Profit Center"); ?></a></h3>
            
			<?php if( ($isAdminSas == 1) || ($activityProfile)){ ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'employees' && $this->params['action'] == 'profile') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/employees/profile/') ?>"><?php __("Profile"); ?></a></h3>
			<?php }?>
			
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'company_configs' && $this->params['action'] == 'resource') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/company_configs/resource/') ?>"><?php __("Settings"); ?></a></h3>
			<?php } ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'externals') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/externals/') ?>"><?php __("External"); ?></a></h3>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'profile_project_managers') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/profile_project_managers/') ?>"><?php __("Profile project manager"); ?></a></h3>
			<?php } ?>
			<h3 class="head-title <?php echo ($this->params['controller'] == 'user_views' && $this->params['action'] == 'company_default_view') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/user_views/company_default_view/') ?>"><?php __("Default view"); ?></a></h3>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'company_column_defaults' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/company_column_defaults/') ?>"><?php __("Default values of width of columns"); ?></a></h3>
			<?php } ?>
			<h3 class="head-title <?php echo ($this->params['controller'] == 'employees' && $this->params['action'] == 'default_user_profile') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/employees/default_user_profile/') ?>"><?php __("User settings"); ?></a></h3>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_amrs_preview' && $this->params['action'] == 'admin') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amrs_preview/admin/') ?>"><?php __("Dashboard by default"); ?></a></h3>
			<?php } ?>
			<?php if(!empty($is_sas) && $is_sas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'customer_logos' ) ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/customer_logos') ?>"><?php __("Customer logo"); ?></a></h3>
			<?php } ?>
            
        <?php } else if (in_array($this->params['controller'], $security_list) || ($this->params['controller'] == 'project_importers' && $this->params['action'] == 'import_excel' )) {?>
			<?php
				$hasToken = ClassRegistry::init('AccessToken')->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						array('OR' => array(
							'company_id' => $company_id,
							'company_id is NULL'
						)),
						array('OR' => array(
							'expires IS NULL',
							'expires' => '',
							'expires >' => date('Y-m-d H:i:s')
						)),
					),
					'fields' => array('*')
				));
			?>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'security_settings' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/security_settings/'); ?>"><?php __("Security"); ?></a></h3>
			<?php } ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'action_logs' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/action_logs'); ?>"><?php __("Action logs"); ?></a></h3>
            <?php if($employee_info['Employee']['is_sas'] == 1){ ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'action_logs' && $this->params['action'] == 'read_config') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/action_logs/read_config'); ?>"><?php __("Read Config"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'action_logs' && $this->params['action'] == 'server_file') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/action_logs/server_file'); ?>"><?php __("Server File"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'action_logs' && $this->params['action'] == 'download_log') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/action_logs/download_log'); ?>"><?php __("Download Log"); ?></a></h3>
            <?php } ?>
			
			<h3 style= "<?php  if(empty($companyConfigs['can_import_task'])){ echo "display: none";}?>" class="head-title can_import_task <?php echo ($this->params['controller'] == 'tasks' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/tasks/index'); ?>"><?php __("Import Tasks"); ?></a></h3>
			
			<h3 style= "<?php  if(empty($companyConfigs['can_import_task_by_excel'])){ echo "display: none";}?>" class="head-title can_import_task_by_excel <?php echo ($this->params['controller'] == 'tasks' && $this->params['action'] == 'import_excel') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/tasks/import_excel'); ?>"><?php __("Import Tasks by Excel"); ?></a></h3>
			
			<?php if($displayActivityExportExcel){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_exports') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_exports/'); ?>"><?php __("Export"); ?></a></h3>
			<?php } ?>
			<?php if( !empty( $hasToken)) { ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'auth_codes') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/auth_codes/'); ?>"><?php __("API Manager"); ?></a></h3>
			<?php } ?> 
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'access_tokens') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/access_tokens/'); ?>"><?php __("Access Tokens Manager"); ?></a></h3>
			<?php } ?> 
			
			<h3 style= "<?php  if(empty($companyConfigs['delete_archived_project_consumed'])){ echo "display: none";}?>" class="head-title delete_archived_project_consumed <?php echo ($this->params['controller'] == 'project_utilities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_utilities/delete_archived_project'); ?>"><?php __("Delete archived project with consumed"); ?></a></h3>
			
			<h3 style= "<?php  if(empty($companyConfigs['can_import_model_project'])){ echo "display: none";}?>" class="head-title can_import_model_project <?php echo ($this->params['controller'] == 'project_importers' && $this->params['action'] == 'import_model_project' ) ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_importers/import_model_project'); ?>"><?php __("Projects import from models"); ?></a></h3>
			
			<h3 style= "<?php  if(empty($companyConfigs['can_import_project'])){ echo "display: none";}?>" class="head-title can_import_project <?php echo ($this->params['controller'] == 'project_importers' && $this->params['action'] == 'import_excel' ) ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_importers/import_excel'); ?>"><?php __("Projects import"); ?></a></h3>
			
			<h3 style= "<?php  if(empty($companyConfigs['enable_sso'])){ echo "display: none";}?>" class="head-title enable_sso <?php echo ($this->params['controller'] == 'sso_logins' && $this->params['action'] == 'sso_config' ) ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sso_logins/sso_config'); ?>"><?php __("SSO"); ?></a></h3>
			
			<h3 style= "<?php  if(empty($companyConfigs['enable_sso'])){ echo "display: none";}?>" class="head-title enable_sso <?php echo ($this->params['controller'] == 'sso_logins' && $this->params['action'] == 'sso_information' ) ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sso_logins/sso_information'); ?>"><?php __("Z0G Information"); ?></a></h3>
			
			
        <?php } else if (in_array($this->params['controller'], $absence_list)) { ?>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'absences' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/absences/'); ?>"><?php __("Absences"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'workdays') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/workdays/'); ?>"><?php __("Workdays"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'response_constraints') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/response_constraints/'); ?>"><?php __("Validation Constraints"); ?></a></h3>
			<?php } ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'holidays') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/holidays/'); ?>"><?php __("Holidays"); ?></a></h3>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'absences' && $this->params['action'] == 'attached_documents') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/absences/attached_documents/'); ?>"><?php __("Attached documents"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'absences' && $this->params['action'] == 'config_capacities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/absences/config_capacities/'); ?>"><?php __("Configuration capacity"); ?></a></h3>
			<?php } ?>
        <?php } else if (in_array($this->params['controller'], $activity_list) || ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'import_timesheet')) { ?>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_columns') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_columns/'); ?>"><?php __("Activity columns"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_families') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_families/'); ?>"><?php __("Activity Families"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activities/'); ?>"><?php __("Activity Management"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_settings' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_settings/index/'); ?>"><?php __("Activity Settings"); ?></a></h3>
			<?php } ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_exports') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_exports/'); ?>"><?php __("Export"); ?></a></h3>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_forecasts' && $this->params['action'] == 'import_timesheet') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_forecasts/import_timesheet/'); ?>"><?php __("Import Timesheet"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'periods') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/periods/'); ?>"><?php __("Period"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_settings' && $this->params['action'] == 'forecasts') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_settings/forecasts/'); ?>"><?php __("Forecasts"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_settings' && $this->params['action'] == 'diary') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_settings/diary/'); ?>"><?php __("Setup Agenda"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'activity_settings' && $this->params['action'] == 'team_workload') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/activity_settings/team_workload/'); ?>"><?php __("Team Workload"); ?></a></h3>
			<?php } ?>
        <?php } else if (in_array($this->params['controller'], $audit_list)) { ?>
            <?php
                $pass = $this->params['pass'];
                if(empty($pass)){
                    $pass = 'auditor_company';
                } else {
                    $pass = $pass[0];
                }
            ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'auditor_company') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/auditor_company/'); ?>"><?php __("Auditor Company"); ?></a></h3>
            <!--h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'auditor') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/auditor/'); ?>"><?php __("Auditor"); ?></a></h3-->
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'mission_status') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/mission_status/'); ?>"><?php __("Mission Status"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'mission_type') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/mission_type/'); ?>"><?php __("Mission Type"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'mission_manager') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/mission_manager/'); ?>"><?php __("Recommendation Status (Mission Manager)"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'recom_priority') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/recom_priority/'); ?>"><?php __("Recommendation Priority"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_settings' && $this->params['action'] == 'index' && $pass == 'recom_manager') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_settings/index/recom_manager/'); ?>"><?php __("Recommendation Status (Recommendation Manager)"); ?></a></h3>
            <?php
                $pass = $this->params['pass'];
                if(empty($pass)){
                    $pass = 'audit_mission';
                } else {
                    $pass = $pass[0];
                }
            ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'sale_settings') && $this->params['action'] == 'audit_log' && in_array($pass, array('audit_mission', 'audit_recom')) ? "wd-current" : "" ?>"><?php __("Log System"); ?></h3>
            <ul class="wd-sub-menu">
                <li class="<?php echo ($this->params['controller'] == 'audit_admins' && $this->params['action'] == 'audit_log' && $pass == 'audit_mission') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_admins/audit_log/audit_mission/'); ?>"><?php __("Mission"); ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'audit_admins' && $this->params['action'] == 'audit_log' && $pass == 'audit_recom') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_admins/audit_log/audit_recom/'); ?>"><?php __("Recommendation"); ?></a></li>
            </ul>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'audit_admins') && $this->params['action'] == 'index' ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/audit_admins/'); ?>"><?php __("Administrator AUDIT"); ?></a></h3>
        <?php } else if (in_array($this->params['controller'], $sale_list)) { ?>
            <?php
                $pass = $this->params['pass'];
                if(empty($pass)){
                    $pass = 'customer_status';
                } else {
                    $pass = $pass[0];
                }
                $customer = array('customer_status', 'customer_industry', 'customer_payment', 'customer_country');
                $lead = array('lead_status', 'lead_maturite', 'lead_phase', 'lead_product', 'lead_billing_period', 'lead_type_of_expense');
            ?>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'sale_settings') && $this->params['action'] == 'index' && in_array($pass, $customer) ? "wd-current" : "" ?>"><?php __("Customer/Provider"); ?></h3>
            <ul class="wd-sub-menu">
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'customer_status') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/customer_status/'); ?>"><?php __("Status"); ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'customer_industry') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/customer_industry/'); ?>"><?php __("Industry"); ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'customer_payment') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/customer_payment/'); ?>"><?php __("Payment delay type"); ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'customer_country') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/customer_country/'); ?>"><?php __("Countries"); ?></a></li>
            </ul>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'sale_settings') && $this->params['action'] == 'index' && in_array($pass, $lead) ? "wd-current" : "" ?>"><?php __("Lead"); ?></h3>
            <ul class="wd-sub-menu">
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'lead_maturite') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/lead_maturite/'); ?>"><?php __("Maturite"); ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'lead_phase') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/lead_phase/'); ?>"><?php __("Phase"); ?></a></li>
                <li class="<?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'lead_product') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/lead_product/'); ?>"><?php __("Product"); ?></a></li>
            </ul>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'sale_settings' && $this->params['action'] == 'index' && $pass == 'currency') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_settings/index/currency/'); ?>"><?php __("Business Currencies"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'sale_expenses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_expenses/'); ?>"><?php __("Business Expenses"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'sale_roles') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/sale_roles/'); ?>"><?php __("Business Permission"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['controller'] == 'categories') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/categories/'); ?>"><?php __("Category"); ?></a></h3>
        <?php } else if($this->params['controller'] == 'staffing_systems') { ?>
            <h3 class="head-title <?php echo ($this->params['action'] == 'index') ? "wd-current" : "" ?>">
            <a href="<?php echo $html->url('/staffing_systems/'); ?>"><?php __("Staffing system"); ?></a></h3>
            <h3 class="head-title <?php echo ($this->params['action'] == 'archived') ? "wd-current" : "" ?>">
            <a href="<?php echo $html->url('/staffing_systems/archived'); ?>"><?php __("Archive"); ?></a></h3>
        <?php } else {
			
			$list_fields = array(
				0 => 'project_amr_program_id',
				1 => 'project_amr_sub_program_id',
				2 => 'project_type_id',//Types
				3 => 'project_sub_type_id',
				4 => 'project_sub_sub_type_id',
				5 => 'complexity_id',
				6 => 'list_2',
				7 => 'list_3',
				8 => 'list_4',
				9 => 'list_5',
				10 => 'list_6',
				11 => 'list_7',
				12 => 'list_8',
				13 => 'list_9',
				14 => 'list_10',
				15 => 'list_11',
				16 => 'list_12',
				17 => 'list_13',
				18 => 'list_14',
				19 => 'list_muti_1',
				20 => 'list_muti_2',
				21 => 'list_muti_3',
				22 => 'list_muti_4',
				23 => 'list_muti_5',
				24 => 'list_muti_6',
				25 => 'list_muti_7',
				26 => 'list_muti_8',
				27 => 'list_muti_9',
				28 => 'list_muti_10',
				29 => 'list_1',
				30 => 'list',
				31 => 'budget_customer_id',
				32 => 'team',
				33 => 'project_priority_id',
				34 => 'project_status_id',
				35 => 'project_phase_id',
			);
			$listMenuProjectTabs = ClassRegistry::init('Translation')->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'page' => 'Details',
					'field' => $list_fields
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
						'order' => array('TranslationSetting.setting_order' => 'ASC'),
						'type' => 'inner'
					)
				)
			));
			$displayPrograms = $displaySubPrograms = $displayTypes = $displaySubTypes = $displaySubSubTypes = $displayComple = $displayList2 = $displayList3 = $displayList4 = $displayList5 = $displayList6 = $displayList7 = $displayList8 = $displayList9 = $displayList10 = $displayList11 = $displayList12 = $displayList13 = $displayList14 = $displayListMulti1 = $displayListMulti2 = $displayListMulti3 = $displayListMulti4 = $displayListMulti5 = $displayListMulti6 = $displayListMulti7 = $displayListMulti8 = $displayListMulti9 = $displayListMulti10 = $displayList1 = $displayList = $displayCustomer = $displayTeamInYourform = $displayPriority = $displayStatus = $displayPhases = 0;
			$listTest = array();
			foreach($listMenuProjectTabs as $key => $value){
				$listTest[$value['TranslationSetting']['setting_order']] = $value['Translation']['field'];
				if(($value['Translation']['field'] == 'budget_customer_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayCustomer = 1;
				}
				
				if(($value['Translation']['field'] == 'project_status_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayStatus = 1;
				}
				
				if(($value['Translation']['field'] == 'project_phase_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayPhases = 1;
				}
				
				if(($value['Translation']['field'] == 'team') && ($value['TranslationSetting']['show'] == 1)){
					$displayTeamInYourform = 1;
				}
				
				if(($value['Translation']['field'] == 'project_priority_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayPriority = 1;
				}
				
				if(($value['Translation']['field'] == 'project_amr_program_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayPrograms = 1;
				}
				
				if(($value['Translation']['field'] == 'project_amr_sub_program_id') && ($value['TranslationSetting']['show'] == 1)){
					$displaySubPrograms = 1;
				}
				
				if(($value['Translation']['field'] == 'project_type_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayTypes = 1;
				}
				
				if(($value['Translation']['field'] == 'project_sub_type_id') && ($value['TranslationSetting']['show'] == 1)){
					$displaySubTypes = 1;
				}
				
				if(($value['Translation']['field'] == 'project_sub_sub_type_id') && ($value['TranslationSetting']['show'] == 1)){
					$displaySubSubTypes = 1;
				}
				
				if(($value['Translation']['field'] == 'complexity_id') && ($value['TranslationSetting']['show'] == 1)){
					$displayComple = 1;
				}
				
				if(($value['Translation']['field'] == 'list') && ($value['TranslationSetting']['show'] == 1)){
					$displayList = 1;
				}
				
				if(($value['Translation']['field'] == 'list_1') && ($value['TranslationSetting']['show'] == 1)){
					$displayList1 = 1;
				}
				if(($value['Translation']['field'] == 'list_2') && ($value['TranslationSetting']['show'] == 1)){
					$displayList2 = 1;
				}
				if(($value['Translation']['field'] == 'list_3') && ($value['TranslationSetting']['show'] == 1)){
					$displayList3 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_4') && ($value['TranslationSetting']['show'] == 1)){
					$displayList4 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_5') && ($value['TranslationSetting']['show'] == 1)){
					$displayList5 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_6') && ($value['TranslationSetting']['show'] == 1)){
					$displayList6 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_7') && ($value['TranslationSetting']['show'] == 1)){
					$displayList7 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_8') && ($value['TranslationSetting']['show'] == 1)){
					$displayList8 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_9') && ($value['TranslationSetting']['show'] == 1)){
					$displayList9 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_10') && ($value['TranslationSetting']['show'] == 1)){
					$displayList10 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_11') && ($value['TranslationSetting']['show'] == 1)){
					$displayList11 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_12') && ($value['TranslationSetting']['show'] == 1)){
					$displayList12 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_13') && ($value['TranslationSetting']['show'] == 1)){
					$displayList13 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_14') && ($value['TranslationSetting']['show'] == 1)){
					$displayList14 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_muti_1') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti1 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_muti_2') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti2 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_muti_3') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti3 = 1;
				}
				
				if(($value['Translation']['field'] == 'list_muti_4') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti4 = 1;
				}
				if(($value['Translation']['field'] == 'list_muti_5') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti5 = 1;
				}
				if(($value['Translation']['field'] == 'list_muti_6') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti6 = 1;
				}
				if(($value['Translation']['field'] == 'list_muti_7') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti7 = 1;
				}
				if(($value['Translation']['field'] == 'list_muti_8') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti8 = 1;
				}
				if(($value['Translation']['field'] == 'list_muti_9') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti9 = 1;
				}
				if(($value['Translation']['field'] == 'list_muti_10') && ($value['TranslationSetting']['show'] == 1)){
					$displayListMulti10 = 1;
				}
			}
			ksort($listTest);
			$langCode = Configure::read('Config.langCode');
            $fields = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
			$list = $this->requestAction('/project_datasets/getList');
			$pass = !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';
			
			$tranYourForm = ClassRegistry::init('Menu')->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'model' => 'project',
					'controllers' => 'projects',
					'functions' => 'your_form',
				),
				'fields' => array('id', 'name_eng', 'name_fre'),
				'order' => array('id' => 'DESC')
			));
			$tranYourForm = !empty($tranYourForm) ? $tranYourForm['Menu'][$fields] : 'Your Form';
		?>
			
			<h3 class="head-title <?php echo ($this->params['controller'] == 'project_phases') ? "wd-current" : "" ?>"><?php __('Lists Of Your Form') ?></h3>
			<ul class="wd-sub-menu">
				<?php foreach($listTest as $keyField => $fieldName){ ?>
					<?php if($displayPrograms == 1 && $fieldName == 'project_amr_program_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_amr_programs') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_programs/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Program"); ?></a></li>
					<?php } ?>
					
					<?php if($displayStatus == 1 && $fieldName == 'project_status_id'){
						if($isAdminSas == 1){?>
							<li class="<?php echo ($this->params['controller'] == '') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/project_statuses/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Status"); ?></a></li>
						<?php }else{ ?>
							<li class="<?php echo ($this->params['controller'] == 'project_statuses') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/project_statuses/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Status"); ?></a></li>
						<?php }?>
					<?php } ?>
					
					<?php if($displayPhases == 1 && $fieldName == 'project_phase_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_phases' && $this->params['action'] == 'index') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/project_phases/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Current Phase"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList1 == 1 && $fieldName == 'list_1'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_1') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_1'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 1"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList2 == 1 && $fieldName == 'list_2'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_2') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_2'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 2"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList3 == 1 && $fieldName == 'list_3'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_3') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_3'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 3"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList4 == 1 && $fieldName == 'list_4'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_4') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_4'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 4"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList7 == 1 && $fieldName == 'list_7'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_7') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_7'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 7"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList8 == 1 && $fieldName == 'list_8'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_8') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_8'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 8"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti1 == 1 && $fieldName == 'list_muti_1'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_1') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_1'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 1"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti2 == 1 && $fieldName == 'list_muti_2'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_2') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_2'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 2"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti3 == 1 && $fieldName == 'list_muti_3'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_3') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_3'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 3"); ?></a></li>
					<?php } ?>
					
					<?php if($displayTypes == 1 && $fieldName == 'project_type_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_types') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_types/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Project type"); ?></a></li>
					<?php } ?>
					
					<?php if($displaySubPrograms == 1 && $fieldName == 'project_amr_sub_program_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_amr_sub_programs') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_sub_programs/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Sub program"); ?></a></li>
					<?php } ?>
					
					<?php if($displaySubTypes == 1 && $fieldName == 'project_sub_type_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_sub_types' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_sub_types/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Sub type"); ?></a></li>
					<?php } ?>
					
					<?php if($displaySubSubTypes == 1 && $fieldName == 'project_sub_sub_type_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_sub_types' && $this->params['action'] == 'child') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_sub_types/child/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Sub sub type"); ?></a></li>
					<?php } ?>
					
					<?php if($displayComple == 1 && $fieldName == 'complexity_id'){?>
						<li class="<?php echo ($this->params['controller'] == 'project_complexities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_complexities/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Implementation Complexity"); ?></a></li>
					<?php } ?>
					
					<?php if($displayPriority == 1 && $fieldName == 'project_priority_id'){ ?>
						<li class="<?php echo ($this->params['controller'] == 'project_priorities' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_priorities/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Priority"); ?></a></li>
					<?php } ?>
					
					<?php if($displayTeamInYourform ==  1 && $fieldName == 'team'){ ?>
						<li class="<?php echo ($this->params['controller'] == 'profit_centers' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/profit_centers/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Team"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList5 == 1 && $fieldName == 'list_5'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_5') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_5'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 5"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList6 == 1 && $fieldName == 'list_6'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_6') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_6'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 6"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList9 == 1 && $fieldName == 'list_9'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_9') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_9'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 9"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList10 == 1 && $fieldName == 'list_10'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_10') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_10'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 10"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList11 == 1 && $fieldName == 'list_11'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_11') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_11'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 11"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList12 == 1 && $fieldName == 'list_12'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_12') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_12'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 12"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList13 == 1 && $fieldName == 'list_13'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_13') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_13'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 13"); ?></a></li>
					<?php } ?>
					
					<?php if($displayList14 == 1 && $fieldName == 'list_14'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_14') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_14'); ?>"><?php __d(sprintf($_domain, 'Details'), "List 14"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti4 == 1 && $fieldName == 'list_muti_4'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_4') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_4'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 4"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti5 == 1 && $fieldName == 'list_muti_5'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_5') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_5'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 5"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti6 == 1 && $fieldName == 'list_muti_6'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_6') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_6'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 6"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti7 == 1 && $fieldName == 'list_muti_7'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_7') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_7'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 7"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti8 == 1 && $fieldName == 'list_muti_8'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_8') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_8'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 8"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti9 == 1 && $fieldName == 'list_muti_9'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_9') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_9'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 9"); ?></a></li>
					<?php } ?>
					
					<?php if($displayListMulti10 == 1 && $fieldName == 'list_muti_10'){?>
						<li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'list_muti_10') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list_muti_10'); ?>"><?php __d(sprintf($_domain, 'Details'), "List(multiselect) 10"); ?></a></li>
					<?php } ?>
				
					<?php if($displayCustomer == 1 && $fieldName == 'budget_customer_id'){?>
						<li class="<?php echo (($this->params['controller'] == 'budget_customers') && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/budget_customers/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Customer"); ?></a></li>
					<?php } ?>
				<?php }?>
			</ul>
			
			<h3 class="head-title <?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'Details') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Details'); ?>"><?php echo $tranYourForm ?></a></h3>
			
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_phases') ? "wd-current" : "" ?>"><?php __('Projects') ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'project_phases' && $this->params['action'] == 'fields') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/project_phases/fields'); ?>"><?php __("Phases"); ?> / <?php __('Fields') ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_alerts') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_alerts/'); ?>"><?php __("Milestone"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'currencies') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/currencies/'); ?>"><?php __("Currencies"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_importers' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_importers/'); ?>"><?php __("Import Projects"); ?></a></li>
					
				</ul>
			<?php } ?>
			
			<?php if($isAdminSas == 1){?>
			<h3 class="head-title"><?php __('Task') ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'admin_task' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/admin_task/index/Project_Task'); ?>"><?php __("Fields"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_statuses') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/project_statuses/'); ?>"><?php __d(sprintf($_domain, 'Details'), "Status"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'admin_task' && $this->params['action'] == 'consumed') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/admin_task/consumed'); ?>"><?php __("Consumed"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'admin_task' && $this->params['action'] == 'setting') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/admin_task/setting'); ?>"><?php __("Setting"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'vision_task_exports') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/vision_task_exports'); ?>"><?php __("Vision task exports"); ?></a></li>
				</ul>
			<?php } ?>
			
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title"><?php __('Expectations') ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'expectation_translations') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectation_translations/'); ?>"><?php __("Translation"); ?></a></li>
					<?php
					$pass = !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';
					?>
					<li class="<?php echo ($this->params['controller'] == 'expectations' && $pass == 'expectation_1') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectations/index/expectation_1'); ?>"><?php echo __("Expectations", true) . ' 1'; ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'expectations' && $pass == 'expectation_2') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectations/index/expectation_2'); ?>"><?php echo __("Expectations", true) . ' 2'; ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'expectations' && $pass == 'expectation_3') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectations/index/expectation_3'); ?>"><?php echo __("Expectations", true) . ' 3'; ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'expectations' && $pass == 'expectation_4') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectations/index/expectation_4'); ?>"><?php echo __("Expectations", true) . ' 4'; ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'expectations' && $pass == 'expectation_5') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectations/index/expectation_5'); ?>"><?php echo __("Expectations", true) . ' 5'; ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'expectations' && $pass == 'expectation_6') ? "wd-current" : "" ?>" ><a href="<?php echo $html->url('/expectations/index/expectation_6'); ?>"><?php echo __("Expectations", true) . ' 6'; ?></a></li>
					<?php
					$list = $this->requestAction('/expectation_datasets/getList');
					$pass = !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';
					$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
					$lang_expec = ($langCode == 'fr') ? 'fre' : 'eng';
					$tran = ClassRegistry::init('ExpectationTranslation')->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
						),
						'fields' => $lang_expec
					));
					foreach($list as $Id => $name):
					?>
					<li class="<?php echo ( $this->params['controller'] == 'expectation_datasets' && $pass == $Id ) ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url('/expectation_datasets/index/' . $Id); ?>">
						<?php echo (!empty($tran[$name])) ? $tran[$name] : $name ?>
						</a>
					</li>
					<?php endforeach ?>
					<!-- list color -->
					<?php
					$list = $this->requestAction('/expectation_colors/getList');
					$pass = !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';
					foreach($list as $Id => $name):
					?>
					<li class="<?php echo ( $this->params['controller'] == 'expectation_colors' && $pass == $Id ) ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url('/expectation_colors/index/' . $Id); ?>">
						<?php echo (!empty($tran[$name])) ? $tran[$name] : $name ?>
						</a>
					</li>
					<?php endforeach ?>
				</ul>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_settings' && $this->params['action'] == 'index') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_settings/index/'); ?>"><?php __("Freeze"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_settings' && $this->params['action'] == 'security') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_settings/security/'); ?>"><?php __("Security"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayList == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_datasets') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_datasets/index/list'); ?>"><?php __("Lists"); ?></a></h3>
			<?php } ?>
			
			<?php if($displayCreatedValue){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_created_values') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'project_created_values', 'action' => 'index', '/financial')) ?>"><?php __("Created Value"); ?></a></h3>
				<h3 class="head-title <?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'Created_Value') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/translations/index/Created_Value'); ?>"><?php __("Created Value Title"); ?></a></h3>
			<?php } ?>
				
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_functions') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_functions/') ?>"><?php __("Project Functions"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_phase_statuses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_phase_statuses/') ?>"><?php __("Project Phase Status"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_budget_internals_preview' && $this->params['action'] == 'setting') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_budget_internals_preview/setting/') ?>"><?php __("Project budget internal"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_risk_severities') ? "wd-current" : "" ?>"><?php __('Project Risks') ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'project_risk_severities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_risk_severities/'); ?>"><?php __("Risk Severities"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_risk_occurrences') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_risk_occurrences/'); ?>"><?php __("Risk Occurrences"); ?></a></li>
				</ul>
				<h3 class="head-title"><?php __('Project Issues') ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'project_issue_severities') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_issue_severities/'); ?>"><?php __("Issue Severities"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_issue_statuses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_issue_statuses/'); ?>"><?php __("Issue Status"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_issue_colors') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_issue_colors/'); ?>"><?php __("Blocking"); ?></a></li>
				</ul>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_livrable_categories') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_livrable_categories/') ?>"><?php __("Project Deliverable Categories"); ?></a></h3>
				<h3 class="head-title"><?php __('Project Evolutions') ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'project_evolution_types') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_evolution_types/'); ?>"><?php __("Evolution Types"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_evolution_impacts') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_evolution_impacts/'); ?>"><?php __("Evolution Impacts"); ?></a></li>
				</ul>
			<?php } ?>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title"><?php __('KPI & Program Management'); ?></h3>
				<ul class="wd-sub-menu">
					<li class="<?php echo ($this->params['controller'] == 'project_amr_categories') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_categories/'); ?>"><?php __("Categories"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_sub_categories') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_sub_categories/'); ?>"><?php __("Sub Categories"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_statuses') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_statuses/'); ?>"><?php __("KPI Status"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_cost_controls') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_cost_controls/'); ?>"><?php __("KPI Cost Controls"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_organizations') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_organizations/'); ?>"><?php __("KPI Organizations"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_plans') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_plans/'); ?>"><?php __("KPI Plans"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_perimeters') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_perimeters/'); ?>"><?php __("KPI Perimeters"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_risk_controls') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_risk_controls/'); ?>"><?php __("KPI Risk Controls"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'project_amr_problem_controls') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/project_amr_problem_controls/'); ?>"><?php __("KPI Problem Controls"); ?></a></li>
					<li class="<?php echo ($this->params['controller'] == 'kpi_settings') ? "wd-current" : "" ?>"><a href="<?php echo $html->url('/kpi_settings/'); ?>"><?php __("Screen Settings"); ?></a></li>
				</ul>
			<?php } ?>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'project_acceptance_types') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'project_acceptance_types', 'action' => 'index')) ?>" style="display: block"><?php __("Acceptance"); ?></a></h3>
			<?php } ?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'menus') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'menus', 'action' => 'index', 'project')) ?>"><?php __("Screen Settings"); ?></a></h3>
			<?php if($isAdminSas == 1){?>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'dependencies') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'dependencies', 'action' => 'index')) ?>" style="display: block"><?php __("Dependency"); ?></a></h3>
				<h3 class="head-title <?php echo ($this->params['controller'] == 'company_configs') ? "wd-current" : "" ?>"><a href="<?php echo $html->url(array('controller' => 'company_configs', 'action' => 'icon')) ?>" style="display: block"><?php __("Icon"); ?></a></h3>
			<?php } ?>
        <?php } ?>
    </div>
</div>

<script>
    $("#accordion h3.head-title").click(function(){
        $(this).addClass("wd-current").next("ul.wd-sub-menu").slideToggle(300).siblings("ul.wd-sub-menu").slideUp("slow");
        $(this).siblings().removeClass("wd-current");

        if ($(this).siblings("ul.wd-sub-menu").length == 0) {
            window.location = $(this).find("a").attr("href");
        }
    });
    var current_selected = $("ul.wd-sub-menu li").each(function(){
        check_class = $(this).attr("class");
        if (check_class=="wd-current"){
            $(this).parent().attr("id", "ul_current");
            a = $("#ul_current").prev().addClass("wd-current");
            $(this).parent().show();
        }
    });
</script>