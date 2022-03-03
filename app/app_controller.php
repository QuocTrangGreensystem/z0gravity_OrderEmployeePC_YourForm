<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
Configure::write('App.modules', array(__('PMS', true), __('RMS', true), __('PMS & RMS', true)));

class AppController extends Controller {

    /**
     * This controller does not use a model
     *
     * @var array
     * @access public
     */
    //var $uses = array('Employee');

    /**
     * Components
     *
     * @var array
     * @access public
     */
    var $components = array(
        'Auth',
        'Session',
        'Cookie' => array(
            'name' => 'ProjectManager'
        ),
        'RequestHandler',
        'ZNotifyExpo',
        'ZPermission'
    );

    /**
     * Actions public
     *
     *
     * @var string
     * @access public
     */
    var $public_actions = array(
		"employees" => array('recovery', 'token_sent', 'change_password',"login", "logout", "list_acos", "get_company_role", "otp_authentication"), 
		"colors"=> array("attachment" , "logo_client"),
		"versions"=> array("version")
	);

    /**
     * Code languages
     *
     *
     * @var string
     * @access public
     */
    var $available_langs = array(
        'en', 'fr', 'vi', 'eng', 'fre', 'vie'
    );

    /**
     * Symbol column
     *
     *
     * @var string
     * @access public
     */
    var $name_columna = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'A', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',);

    var $__is_debuging = true;
	var $a;
	var $mem;
	var $z_debug;
	var $canSeeBudget = false;
	var $listEmployeeName = array();
	var $filter_render = array();

    /**
     * Before executing controller actions
     *
     * @return void
     * @access public
     */
    public $detector;
    public $enable_newdesign;
    public $isAdminSAS; // SAS OR SAS login as Admin 

    function beforeFilter() {
		$this->a = time();
		$this->mem = memory_get_usage();
		$this->z_debug = $this->Session->read('z_debug');;
		if( !empty($this->z_debug )){
			if( !in_array($this->z_debug, array(0,1,2)) ) $this->z_debug = 0;
			Configure::write('debug', $this->z_debug);
		}
		// debug( $employee_info); exit;
		// debug( $this->params); exit;
		// check enable New design 
		$_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
		$_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
		$this->loadModels('Menu', 'TwoFactorAuthen');
		$infos = $this->Session->read('Auth.employee_info');
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
		$_menu = $this->Menu->find( 'all', array(
			'conditions' => array(
				'controllers' => $_controller,
				'functions' => $_action,
				'company_id' => $company_id,
				'model' => 'project'
			),
			'fields' => array('enable_newdesign')
		));
		// debug( $_menu); exit;
		$_enable_newdesign = 0;
		$no_redirect = !empty($_GET['no_redirect']) ? $_GET['no_redirect'] : 0;
		foreach($_menu as $val){
			if( $val['Menu']['enable_newdesign'] == 1) $_enable_newdesign = 1;
		}
		if($_enable_newdesign && !strpos($_controller, '_preview') && !$no_redirect){
			$_controller_preview =  trim( str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_').'_preview';
			$_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
			$_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
			$_pass = '';
			foreach ($_pass_arr as $value) {
				$_pass .= '/'.$value;
			}
			if( isset($_url_param['url'])) unset($_url_param['url']);
			$this->redirect(array(
				'controller' => $_controller_preview,
				'action' => $_action,
				$_pass,
				'?' => $_url_param,
			));
		}
		if( strpos($_controller, '_preview') ) $_enable_newdesign = 1;
		$_enable_newdesign = (int) ( (!empty($infos['Color']['is_new_design']) && $infos['Color']['is_new_design'] == 1) || $_enable_newdesign );
		$this->enable_newdesign = $_enable_newdesign;
		$this->set('enable_newdesign', $_enable_newdesign);
		
		// End check enable New design 		
        // $this->helpers[] = 'UserFile';
        App::import('Vendor', 'Mobile_Detect', array('file' => 'Mobile_Detect.php'));
        $this->detector = new Mobile_Detect();

        if ((!Configure::read('Install.installed') || !Configure::read('Install.secured')) && $this->params['controller'] != 'installs'){
            $this->redirect(array('controller' => 'installs', 'action' => 'index'));
        }
        if (!env('HTTPS')) {
            $this->redirect('https://' . env('HTTP_HOST') . $this->here);
        }
        $this->_setLocale();

        Security::setHash('md5');
        $this->Auth->authenticate = ClassRegistry::init('Employee');
        $this->Auth->userModel = 'Employee';

        $this->Auth->fields = array(
            'username' => 'email',
            'password' => 'password'
        );
        $this->Auth->authorize = 'controllers';
        $this->Auth->userScope = array(
            array(
                'OR' => array(
                    'is_sas' => 1,
                    'actif' => 1
                ),
            ),
            array(
                'OR' => array(
                    array('end_date' => '0000-00-00'),
                    array('end_date IS NULL'),
                    array('end_date >=' => date('Y-m-d', time())),
                )
            )
        );
        $query = array();
        if ($this->params['controller'] != 'employees' && $this->params['action'] != 'login' && $this->params['controller'] != 'new_staffing' ) {
            $query = $this->params['url']['url'] . Router::queryString(array_diff_key(
                                    $this->params['url'], array('url' => '', 'ext' => '')), array());
            $query = array('continue' => '/' . $query);
        }
        $this->Auth->loginAction = array('controller' => 'employees', 'action' => 'login', 'plugin' => false, 'admin' => false, '?' => $query);
        $this->Auth->loginRedirect = array('controller' => 'employees', 'action' => 'get_company_role');
        $this->Auth->logoutRedirect = array('controller' => 'employees', 'action' => 'login');
        $this->Auth->loginError = __('Email or password is not match!', true);
        $this->Auth->authError = __('You have not permission to access this function', true);

        if( !empty($this->params['plugin']) && ($this->params['plugin'] == 'api')) return parent::beforeFilter();
        $this->isAuthorized();
        $this->disableCache();
		
        /**
         * Save action
         */
        $this->params['saveAction'] = !empty($this->params['action']) ? $this->params['action'] : '';
        /*
		* Tạm thời tắt chức năng check key
		* Z0G - 26/8/2019 - Change mode of validate of license check - turn check license of site OFF
        * Bật tắt tương tự như đối với Web services ws_controller.php
		* /
        if($this->params['controller'] == 'projects'){
            $this->initProjectFile();
        }

        if($this->params['controller'] == 'employee_absences' || $this->params['controller'] == 'absence_requests'){
            $this->initAbsencesFile();
        }

        if($this->params['controller'] == 'activity_forecasts' || $this->params['controller'] == 'activities'){
            $this->initActivityFile();
        }

        if($this->params['controller'] == 'project_budget_synthesis'
            || $this->params['controller'] == 'project_budget_sales'
            || $this->params['controller'] == 'project_budget_internals'
            || $this->params['controller'] == 'project_budget_externals'
            || $this->params['controller'] == 'activity_budget_synthesis'
            || $this->params['controller'] == 'activity_budget_sales'
            || $this->params['controller'] == 'activity_budget_internals'
            || $this->params['controller'] == 'activity_budget_externals'
        ){
            $this->initBudgetFile();
        }
        if($this->params['controller'] == 'audit_missions'
            || $this->params['controller'] == 'audit_recoms'
            || $this->params['controller'] == 'audit_settings'
            || $this->params['controller'] == 'audit_admins'
            || $this->params['controller'] == 'audit_logs'
            ){
            $this->initAuditFile();
        }
		/*
		* END Tạm thời tắt chức năng check key
		*/
		
        $this->_changeBeginOfPeriodFollowYear();
        $employee_info = $this->Session->read('Auth.employee_info');
        $is_sas = $employee_info['Employee']['is_sas'];
		$isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
		if($isProfileManager != 0 ){
			$this->loadModel('ProfileProjectManagerDetail');
		}
        /**
         * Check Session Cho phan Hien thi cac Module
         */
        list($seeMenuAudit, $seeMenuBusiness) = $this->_checkPermissionMenuAudit();
        list($enablePMS, $enableRMS, $enableAudit, $enableReport, $enableBusines, $enableZogMsgs, $enableTicket) = $this->_enableModule();
        if(($this->params['controller'] == 'projects'
            || $this->params['controller'] == 'project_images'
            || $this->params['controller'] == 'project_budget_sales'
            || $this->params['controller'] == 'project_budget_internals'
            || $this->params['controller'] == 'project_budget_externals'
            || $this->params['controller'] == 'project_created_vals'
            || $this->params['controller'] == 'project_teams'
            || $this->params['controller'] == 'project_parts'
            || $this->params['controller'] == 'project_phase_plans'
            || $this->params['controller'] == 'project_tasks'
            || $this->params['controller'] == 'project_milestones'
            || $this->params['controller'] == 'project_staffings'
            || $this->params['controller'] == 'project_budget_synthesis'
            || $this->params['controller'] == 'project_acceptances'
            || $this->params['controller'] == 'project_dependencies'
            || $this->params['controller'] == 'project_finances'
            || $this->params['controller'] == 'project_risks'
            || $this->params['controller'] == 'project_local_views'
            || $this->params['controller'] == 'project_budget_fiscals'
            || $this->params['controller'] == 'project_budget_provisionals'
            ) && ($enablePMS == false) && $is_sas != 1){
            $this->redirect("/");
        }
        if((($this->params['controller'] == 'activity_forecasts' && $this->action != 'my_diary')
            || $this->params['controller'] == 'activities'
            ) && ($enableRMS == false) && $is_sas != 1){
            $this->redirect("/");
        }
        if(($this->params['controller'] == 'audit_missions'
            || $this->params['controller'] == 'audit_recoms'
            || $this->params['controller'] == 'audit_settings'
            || $this->params['controller'] == 'audit_admins'
            || $this->params['controller'] == 'audit_logs'
            ) && ($enableAudit == false) && $is_sas != 1){
            $this->redirect("/");
        }
        $enableReport = $enableReport && $this->employeeHasReport();
        if(($this->params['controller'] == 'reports') && ($enableReport == false) && $is_sas != 1){
            $this->redirect("/");
        }
        if(($this->params['controller'] == 'sale_leads'
            || $this->params['controller'] == 'sale_customers'
            || $this->params['controller'] == 'sale_settings'
            || $this->params['controller'] == 'sale_customer_contacts'
            ) && ($enableBusines == false) && $is_sas != 1){
            $this->redirect("/");
        }
        // doan nay phan quyen cho controller sql_manager.
        if(($this->params['controller'] == 'sql_manager'
            )  && $is_sas != 1 && !in_array($this->params['action'], array('index', 'export_excel', 'excutesql'))){
            $this->redirect("/");
        }
        if( ($this->params['controller'] == 'reports') && ($this->params['action'] == 'index') && $is_sas !=1 ){
            $this->redirect("/reports/sql_report");
        }
        // zog_msgs
        if( ($this->params['controller'] == 'zog_msgs') && ($this->employee_info['Role']['name'] == 'conslt') ){
            $this->redirect("/");
        }
        $tickets = array();
        if( in_array($this->params['controller'], $tickets) && !$enableTicket && !$is_sas ){
            $this->redirect('/');
        }
        $this->set('enableTicket', $enableTicket);
        $this->Session->write('seeMenuAudit', $seeMenuAudit);
        $this->Session->write('seeMenuBusiness', $seeMenuBusiness);
        $this->Session->write('enablePMS', $enablePMS);
        $this->Session->write('enableRMS', $enableRMS);
        $this->Session->write('enableAudit', $enableAudit);
        $this->Session->write('enableReport', $enableReport);
        $this->Session->write('enableBusines', $enableBusines);
        $this->Session->write('enableZogMsgs', $enableZogMsgs);
        $this->Session->write('checkModuleDisplay', true);
        //setting domain
        $this->_loadTranslate();
        if(!empty($this->employee_info)) $this->getHistoryFilter();

        $this->loadModels('ActionLog', 'ApiKey');
        if( ($key = $this->Session->read('Auth.employee_info.Employee.api_key')) ){
            $this->set('api_key', $key);
			$new = $this->ApiKey->updateExpireTime($key);
		} else $this->set('api_key', '');
        $this->_trackAdmin();
        $this->set('projectNameLength', 124);
		$this->checkDeviceOTP();
		if( !empty( $this->employee_info['Company']['id'])) $this->_getListEmployee();
		$this->helpers['UserFile']['variables']['listEmployeeName'] = $this->listEmployeeName;
		// debug( $this->UserFile);
    }
	protected function checkDeviceOTP(){
		if( !empty($this->employee_info["Employee"]['two_factor_auth']) && !empty($this->companyConfigs['company_two_factor_auth'])){
			if( !@in_array($this->action, @$this->public_actions[$this->viewPath])){
				$this->Cookie->__values = $this->Cookie->__decrypt($_COOKIE[$this->Cookie->name]);
				$cookie_key = '2FA_'. $this->employee_info['Employee']['id'] . '_' . $this->employee_info['Company']['id'];
				$otp_cookie = $this->Cookie->read($cookie_key);
				$check = true;
				$last_item =  $this->TwoFactorAuthen->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'employee_id' => $this->employee_info['Employee']['id'],
					),
					'fields' => array('*'),
				));
				if(!empty($last_item)){
					$cookie_time = $otp_cookie['time'];
					$cookie_value = $otp_cookie['value'];
					$otp_duration_expired = !empty($this->companyConfigs['otp_duration_expired']) ? $this->companyConfigs['otp_duration_expired'] : 'week';
					$duration_expired = '+1 '. $otp_duration_expired;
					$next_send = strtotime($duration_expired, $last_item['TwoFactorAuthen']['updated']);
					if($next_send <= time()){
						$_redirect = 1;
						$this->Session->setFlash( __('OPT has expired. To continue, please enter OTP', true), 'default', array(), 'auth');
					}elseif (!empty($last_item['TwoFactorAuthen']['current_cookie']) && ($cookie_value != $last_item['TwoFactorAuthen']['current_cookie'])){
						$_redirect = 1;
						$this->Session->setFlash( __('You have signed in to a new device. To continue, please enter OTP', true), 'default', array(), 'auth');
					}
					if( !empty($_redirect)){
						$this->Cookie->delete($cookie_key);
						$this->Session->delete('Auth.employee_info');
						$this->Session->write('Auth.employee_otp', $this->employee_info);
						
						$this->redirect(array(
							'controller' => 'employees',
							'action' => 'otp_authentication',
							'?' => array(
								'continue' => $_SERVER["REQUEST_URI"]
							)
						));
					}
				}
			}
		}
		return true;
	}
	function getHistoryFilter(){
		$this->loadModel('HistoryFilter');
		$path = !empty($this->params['url']['url']) ? rtrim($this->params['url']['url'], '/') : '';
		$filter_render = array();
		$filter_render = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
			'fields' => array('params'),
			'conditions' => array(
				'path' => $path,
				'employee_id' => $this->employee_info['Employee']['id']
			))
		);
		$filter_render = !empty($filter_render) ? (array) @unserialize($filter_render['HistoryFilter']['params']) :  array();
		$this->filter_render = $filter_render;
		$this->set('filter_render', $filter_render);
		$paths = array(
			'vs_filter',
			'my_assistants',
		);
		$history = $this->HistoryFilter->getSettings($paths);
		$this->set($history);
	}
	function convert($size){
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
    protected function saveLanguage($code){
        $E = ClassRegistry::init('Employee');
        //save preference
        $emp = $this->Session->read('Auth.employee_info.Employee');
        if( !empty($emp) ){
            $E->save(array(
                'id' => $emp['id'],
                'language' => $code
            ));
            $this->Session->write('Auth.employee_info.Employee.language', $code);
        }
    }

    protected function _setLocale() {
        $languages = Configure::read('Config.languages');
        $emp = $this->Session->read('Auth.employee_info.Employee');
        if (isset($this->params['url']['hl'])) {
            $langCode = $this->params['url']['hl'];
            $this->saveLanguage($langCode);
        } else if( !empty($emp) ) {
            $langCode = !empty($emp['language']) ? $emp['language'] : 'fr';
        } else if ($this->Cookie->read('hl')) {
            $langCode = $this->Cookie->read('hl');
        }
        if (empty($langCode) || empty($languages[$langCode])) {
            $langCode = Configure::read('Config.defaultLanguage');
        }
        $this->Cookie->write('hl', $langCode);
        $language = $languages[$langCode];
        Configure::write('Config.language', $language);
        Configure::write('Config.langCode', $langCode);
        $Model = ClassRegistry::init('CompanyConfigs');
        $Model->cacheQueries = false;
        $employee_info = $this->Session->read('Auth.employee_info');
        $company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
        $display_my_assistant = $Model->find('first', array(
            'recursive' => -1,
            'fields' => array('cf_name', 'cf_value'),
            'conditions' => array(
                'CompanyConfigs.cf_name' => 'display_my_assistant',
                'CompanyConfigs.company' => $company_id
            )));
        $display_my_assistant = !empty($display_my_assistant['CompanyConfigs']['cf_value']) ? $display_my_assistant['CompanyConfigs']['cf_value'] : 0;
        Configure::write('Config.displayAssistant', $display_my_assistant);
        $avatar_assistant = $Model->find('first', array(
            'recursive' => -1,
            'fields' => array('cf_name', 'cf_value'),
            'conditions' => array(
                'CompanyConfigs.cf_name' => 'avatar_assistant',
                'CompanyConfigs.company' => $company_id
            )));
        $avatar_assistant = !empty($avatar_assistant['CompanyConfigs']['cf_value']) ? $avatar_assistant['CompanyConfigs']['cf_value'] : 0;
        Configure::write('Config.avatar_assistant', $avatar_assistant);
        $list = array(
            'fr' => 'fre',
            'en' => 'eng',
            'vi' => 'vie'
        );
        $this->set('longCode', $list[$langCode]);
        $this->set('langCode', $langCode);
        //load configs from db
        $this->_getAllConfigs();
    }

    /**
     * Disable Cache
     *
     * @return void
     * @access public
     */
    function disableCache() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store");
    }

    function isMobile(){
        if( !class_exists('Mobile_Detect') ){
            App::import('Vendor', 'Mobile_Detect', array('file' => 'Mobile_Detect.php'));
        }
        $this->detector = new Mobile_Detect();
        $this->set('isMobileOnly', !$this->detector->isTablet() && $this->detector->isMobile());
        $this->set('isTablet', $this->detector->isTablet());
        $this->set('_detector', $this->detector);
        return $this->detector->isMobile() && !$this->detector->isTablet();
    }

    function isTouch(){
        if( !class_exists('Mobile_Detect') ){
            App::import('Vendor', 'Mobile_Detect', array('file' => 'Mobile_Detect.php'));
        }
        return $this->detector->isMobile() || $this->detector->isTablet();
    }

    /**
     * Before executing controller render layout
     *
     * @return void
     * @access public
     */
    function beforeRender() {
        if( isset($this->params['url']['mobile']) ){
            $this->Session->write('mobile', $this->params['url']['mobile']);
        }
        if( !$this->Session->check('mobile') && $this->isMobile() ){
            $this->Session->write('mobile', 1);
        }
        $mobileEnabled = $this->Session->read('mobile');
        $isMobile = $this->isMobile();
        $this->set('isMobile', $isMobile);
        $this->set('mobileEnabled', $mobileEnabled);
        $this->set('isTouch', $this->isTouch());
        $this->_setErrorLayout();
        //danh sach responsive screen
        $employees = array('login', 'get_company_role', 'change_password', 'recovery', 'token_sent');
        $currentAction = $this->params['controller'] . '/' . $this->params['action'];
        $this->set('currentAction', $currentAction);
    }

    public function enabledModule($name = 'both') {
        $options = array_combine(array('pms', 'rms', 'both'), array_keys(Configure::read('App.modules')));
        if ($this->is_sas || (isset($options[$name]) && $this->employee_info['Company']['module'] == $options[$name])
                || $this->employee_info['Company']['module'] == $options['both']) {
            return true;
        }
        return false;
    }
	// chỉ check cho PM k check PM profile
	function permissionBudget($checkCanView=true){
		$acos_permissions = new acos_permissions();
		$budget_actions = $acos_permissions->budget_actions;
		if( @in_array($this->action, @$budget_actions[$this->viewPath]) && !@in_array($this->action, @$this->public_actions[$this->viewPath])){
			if( ( !isset($this->companyConfigs['EPM_see_the_budget']) || ($this->companyConfigs['EPM_see_the_budget'] == 0)) ) return false;
			if( $checkCanView){
				if( $this->employee_info['Role']['name'] == 'pm' && isset($this->employee_info['CompanyEmployeeReference']['see_budget']) && ($this->employee_info['CompanyEmployeeReference']['see_budget'] == '0')){
					$this->Auth->authError = sprintf(__('You do not have permission to access %s', true), 'budget');
					return false;
				}
			}else{ // check can edit
				if( ($this->employee_info['Role']['name'] == 'pm') &&( !isset($this->employee_info['Employee']['update_budget']) || ($this->employee_info['Employee']['update_budget'] == 0))){
					return false;
				}
			}
		}
		return true;
	}
    /**
     * Authorized
     *
     * @return void
     * @access public
     */
    function isAuthorized(){
        if ($this->Auth->user()) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
            $this->employee_info = $this->Session->read('Auth.employee_info');
            $this->is_sas = $this->employee_info["Employee"]['is_sas'];
			$this->isAdminSAS = ($this->employee_info['Employee']['is_sas'] == '1') || empty($this->employee_info['Employee']['company_id']);
            $this->set('employee_info', $this->employee_info);
            $this->set('is_sas', $this->is_sas);
            $this->set('isAdminSAS', $this->isAdminSAS);
			$this->canSeeBudget = false;
			$this->canModifyBudget = false;
            if ($this->is_sas) {
                $this->Auth->allow("*");
            } else {
                $user_role = $this->employee_info["Role"]['name'];
                App::import("vendor", "acos_permissions");
                $acos_permissions = new acos_permissions();
                switch ($user_role) {
                    case "admin":
						$this->canSeeBudget = true;
                        $this->Auth->allow('*');
                        break;
                    case "pm":
                        $arr_alows = $acos_permissions->pm_actions;
                        $arr_alows = array_merge_recursive($arr_alows, $this->public_actions);
                        if(@in_array($this->action, @$arr_alows[$this->viewPath]) && $this->permissionBudget()) {
                            $this->Auth->allow();
                        } else {
                            if ($this->RequestHandler->isAjax()) {
                                if($this->params['controller'] == 'activity_tasks' || $this->params['controller'] == 'project_tasks'){
                                    $this->Auth->allow();
                                }else{
                                    $this->Auth->deny();
                                    exit('<div class="message error">' . $this->Auth->authError . '</div>');
                                }
                            }else{
                                $this->Auth->deny();
                                $this->cakeError('accessDenied', array('msg' => $this->Auth->authError));
                            }
                        }
						$this->canSeeBudget = (isset($this->companyConfigs['EPM_see_the_budget']) && ($this->companyConfigs['EPM_see_the_budget'] != 0)) && ( !empty($this->employee_info['CompanyEmployeeReference']['see_budget']));
						$this->set('budget_actions',$acos_permissions->budget_actions);
                        break;
                    case "tech":
                        $arr_alows = $acos_permissions->pm_actions;
                        $arr_alows = array_merge_recursive($arr_alows, $this->public_actions);
                        if (@in_array($this->action, @$arr_alows[$this->viewPath])) {
                            $this->Auth->allow();
                        } else {
                            $this->Auth->deny();
                            if (!$this->RequestHandler->isAjax()) {
                                $this->cakeError('accessDenied', array('msg' => $this->Auth->authError));
                            } else {
                                exit('<div class="message error">' . $this->Auth->authError . '</div>');
                            }
                        }
						
                        break;
                    case "conslt":
                        $arr_alows = $acos_permissions->consultant_actions;
                        $arr_alows = array_merge_recursive($arr_alows, $this->public_actions);
                        if (@in_array($this->action, @$arr_alows[$this->viewPath])) {
                            $this->Auth->allow();
                        } else {
                            $this->Auth->deny();
                            if (!$this->RequestHandler->isAjax()) {
                                $this->cakeError('accessDenied', array('msg' => $this->Auth->authError));
                            } else {
                                exit('<div class="message error">' . $this->Auth->authError . '</div>');
                            }
                        }
                        break;
                    case "hr":
                        $arr_alows = $acos_permissions->hr_actions;
                        $arr_alows = array_merge_recursive($arr_alows, $this->public_actions);
                        if (@in_array($this->action, @$arr_alows[$this->viewPath])) {
                            $this->Auth->allow();
                        } else {
                            $this->Auth->deny();
                            if (!$this->RequestHandler->isAjax()) {
                                $this->cakeError('accessDenied', array('msg' => $this->Auth->authError));
                            } else {
                                exit('<div class="message error">' . $this->Auth->authError . '</div>');
                            }
                        }
                        break;
                    default:
                        $arr_alows = array("employees" => array("get_company_role", "otp_authentication"));
                        $arr_alows = array_merge_recursive($arr_alows, $this->public_actions);
                        if ($this->is_sas) {
                            $this->Auth->allow("*");
                        } else {
                            if (@in_array($this->action, @$arr_alows[@$this->viewPath])) {
                                $this->Auth->allow();
                            } else {
                                $this->Auth->deny();
                                $this->Session->setFlash(__('You do not have enough privileges to access this page.', true));
                                $this->redirect("/employees/login");
                            }
                        }
                        break;
                }
            }
			$this->set('canSeeBudget', $this->canSeeBudget);
        } else {
            $this->Auth->authError = __(' ', true);
        }
    }

    function _setErrorLayout() {
        if ($this->name == 'CakeError') {
            $this->layout = 'login';
        }
    }

    /**
     * Pages Error
     *
     * @return void
     * @access protected
     */
    function appError() {
        $this->render('error404');
        $this->afterFilter();
        echo $this->output;
        $this->_stop();
    }
	/* Huynh 18-02-2020 
	Function _userCanView
	Check curen user can View Project 
	User this function to check current can open a screen when canModified = 0
	DO NOT USER THIS FUNCTION FOR YOUR FORM (PREVIEW ) SCREEN 	
	return boolean 	
	*/
	private function _userCanView($project_id=null){
		$canView = 1;
		if( empty($project_id)) $canView = 0;
		if( !empty($this->companyConfigs['see_all_projects']) || !empty( $this->employee_info['CompanyEmployeeReference']['see_all_projects'])) return 1;
		$role = $this->employee_info['Role']['id'];
		// Phan nay khong kiem tra cho consultant vi da duoc kiem tra o acos_permissions
		if($role == 3 && $canView){ // PM  	
			$this->loadModels('ProjectEmployeeManager','Project');
			$read_access = $this->ProjectEmployeeManager->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'project_manager_id' => $this->employee_info['Employee']['id'],
					'project_id' => $project_id,
					'OR' => array(
						'is_profit_center' => 0,
						'is_profit_center is NULL'
					),
					// any type
				),
			));
			$pmOfProject = $this->Project->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'project_manager_id' => $this->employee_info['Employee']['id'],
					'id' => $project_id
				)
			));
			$readAccessByTeam = array();
			$readAccessByTeam = $this->ProjectEmployeeManager->find('count',array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
					'is_profit_center' => 1,
					'project_manager_id' => $this->employee_info['Employee']['profit_center_id']
				)
			));
			$canView = (!empty($read_access) || !empty($pmOfProject) || !empty($readAccessByTeam)) ? 1 : 0;
			// Neu user canView thi kiem tra tiep. khong thi bo qua
			if( $canView && !empty($this->employee_info['Employee']['profile_account'])){
				//Phan nay chi kiem tra quyen View, Quyen Edit duoc kiem tra o tung function
				$profile_id = $this->employee_info['Employee']['profile_account'];
				$this->loadModels('ProfileProjectManagerDetail');
				$canView = $this->ProfileProjectManagerDetail->find('count', array(
					'recursive' => -1,
					'conditions' => array(
						'model_id' => $profile_id,
						'display' => 1,
						'controllers' => array(
							$this->params['controller'],
							str_replace( '_preview', '', $this->params['controller'])
						)
					)
				));
				$canView = !empty($canView) ? 1 : 0;
			}
		}
		if( !$canView){
			if( $this->params['isAjax']){
				die(json_encode(array(
					'result' => 'failed',
					'message' => __('You have not permission to access this function', true),
					'data' => array()
				)));
			}else{
				$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
				$this->redirect(array('controller' => 'projects', 'action' => 'index'));
			}
		}
		return $canView;
	}
    /**
     * Check role
     *
     * @return void
     * @access protected
     */
    protected function _checkRole($isChange, $projectId = null, $option = array(), $project_task_id = null) {
        if ($isChange && !empty($this->data['project_id']) && empty($projectId)) {
            $projectId = $this->data['project_id'];
        }
		if (!empty($this->data['taskid']) && empty($project_task_id)) {
            $project_task_id = $this->data['id'];
        }
        $option = array_merge(array('element' => 'error'), $option);
        $Model = ClassRegistry::init('Project');
        $Model->cacheQueries = false;
        $Model2 = ClassRegistry::init('ProjectEmployeeManager');
        $Model2->cacheQueries = false;
        $Model3 = ClassRegistry::init('ProjectEmployeeManager');
        $Model3->cacheQueries = false;
        $projectName = $Model->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'project_name', 'company_id', 'start_date',
                'end_date', 'planed_end_date', 'project_manager_id', 'technical_manager_id', 'category', 'off_freeze', 'address', 'chief_business_id'),
            'conditions' => array('Project.id' => $projectId)));
        $managerBakups = $Model3->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectId,
                'type !=' => 'RA'
            ),
            'fields' => array('project_manager_id')
        ));
        $managerBakups = Set::combine($managerBakups, '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager.project_manager_id');
        if (empty($projectName)) {
            $this->Session->setFlash(sprintf(__('The project "#%s" was not found, please try again', true), $projectId), 'error');
            $isChange = false;
        } else {
            $checkIsChang = 'true';
            $employeeInfo = $this->Session->read("Auth.employee_info");
            $canModified = true;
			// $this->employee_info['Employee']['update_budget'] = 1;
            if (!$employeeInfo || empty($employeeInfo['Role']) || $employeeInfo['Role']['name'] == 'conslt'
                    || ($employeeInfo['Role']['name'] != 'admin'
                    && ($employeeInfo['Employee']['id'] != $projectName['Project']['project_manager_id']
                    && $employeeInfo['Employee']['id'] != $projectName['Project']['technical_manager_id']
                    && $employeeInfo['Employee']['id'] != $projectName['Project']['chief_business_id']
                    && !in_array($employeeInfo['Employee']['id'], $managerBakups)
                    ))) {
                $canModified = false;
                if ($isChange) {
                    $checkIsChang = 'false';
                    $Model4 = ClassRegistry::init('Employee');
                    $employInfors = $Model4->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
                        'fields' => array('update_your_form', 'delete_a_project', 'change_status_project')
                    ));
                    $permissionArray=$this->ZPermission->get_permission($projectId);
                    
                    $changeStatusPJ =$permissionArray['change_status_project'];
                    $updatePJ = $permissionArray['update_your_form'];
                    $deletePJ= $permissionArray['delete_a_project'];

                    // $changeStatusPJ = !empty($employInfors) && !empty($employInfors['Employee']['change_status_project']) ? true : false;
                    // $updatePJ = !empty($employInfors) && !empty($employInfors['Employee']['update_your_form']) ? true : false;
                    // $deletePJ = !empty($employInfors) && !empty($employInfors['Employee']['delete_a_project']) ? true : false;
                    if(($this->params['controller'] == 'projects' && $this->params['action'] == 'delete')){
                        if($this->employee_info["Role"]["name"] == 'pm' && $deletePJ){
                            $canModified = true;
                            $checkIsChang = 'true';
                        }
                    } else {
                        if($this->employee_info["Role"]["name"] == 'pm' && ($updatePJ || $changeStatusPJ) ){
                            $canModified = true;
                            $checkIsChang = 'true';
                        }
                    }
                    if($canModified == false){
                        $this->Session->setFlash(__('Read only', true));
                    }
                }
            }
			if( $employeeInfo['Role']['name'] == 'pm' && $employeeInfo['Employee']['profile_account'] ){
				$this->loadModel('ProfileProjectManagerDetail');
				$profile_id = $this->employee_info['Employee']['profile_account'];
				$canModified = 0;
				if( $employeeInfo['Employee']['id'] == $projectName['Project']['project_manager_id']
                    || $employeeInfo['Employee']['id'] == $projectName['Project']['technical_manager_id']
                    || $employeeInfo['Employee']['id'] == $projectName['Project']['chief_business_id']
                    || in_array($employeeInfo['Employee']['id'], $managerBakups)){
						$canModified = 1;
					}
				if( $canModified){
					$can_modified = $this->ProfileProjectManagerDetail->find('count', array(
						'recursive' => -1,
						'conditions' => array(
							'model_id' => $profile_id,
							'display' => 1,
							'read_write' => 1,
							'controllers' => array(
								$this->params['controller'],
								str_replace( '_preview', '', $this->params['controller'])
							)
						)
					));
					$canModified = !empty($can_modified) ? 1 : 0;
				}
			}elseif($canModified && $employeeInfo['Role']['name'] == 'pm' && !$employeeInfo['Employee']['profile_account']){
				$canModified = $this->permissionBudget(false);
			}
			if(empty($canModified) && !empty($project_task_id)){
				$this->loadModels('ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenterManagerBackup', 'ProfitCenter');
				$list_employee_assigned = $this->ProjectTaskEmployeeRefer->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $project_task_id,
					),
					'fields' => array('reference_id', 'is_profit_center')
				));
				$pc_assigned = array();
				$employee_assigned = array();
				if(!empty($list_employee_assigned)){
					foreach($list_employee_assigned as $resource_id => $is_profit_center){
						if(!empty($is_profit_center)){
							$pc_assigned[] = $resource_id;
						}else{
							$employee_assigned[] = $resource_id;
						}
					}
				}
				if(!empty($employee_assigned)){
					$pc_list = $this->Employee->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $employee_assigned,
						),
						'fields' => array('profit_center_id', 'profit_center_id')
					));
					if(!empty($pc_list)){
						$pc_assigned = array_merge($pc_assigned, $pc_list);
					}
				}
				if(!empty($pc_assigned)){
					$managerPC = array();
					$listPCRefer = array();
					foreach($pc_assigned as $index => $pc_id){
						$listManagers = $this->ProfitCenter->getPath($pc_id, array('id', 'IF(manager_id IS NOT NULL OR manager_id != "", manager_id, IF(manager_backup_id IS NOT NULL OR manager_backup_id != "", manager_backup_id, "")) as manager'), -1);
						if(!empty($listManagers)){
							foreach($listManagers as $index => $values){
								$managerPC[] = $values[0]['manager'];
								$listPCRefer[] = $values['ProfitCenter']['id'];
							}
						}
					}
					
					if(!empty($managerPC) && in_array($employeeInfo['Employee']['id'], $managerPC)){
						$canModified = 1;
					}else{
						if(!empty($listPCRefer)){
							$bk_manager = $this->ProfitCenterManagerBackup->find('count', array(
								'recursive' => -1,
								'conditions' => array(
									'profit_center_id' => $listPCRefer,
									'employee_id' => $employeeInfo['Employee']['id']
								)
							));
							if($bk_manager > 0) $canModified = 1;
						}
					}
				}
			}
            if(!empty($this->employee_info["Company"]["id"]) && $projectName['Project']['company_id'] != $this->employee_info["Company"]["id"]){
                $this->cakeError('error404');
            }
            $this->set(compact('checkIsChang'));
            $this->set(compact('projectName', 'canModified'));
			// debug( $canModified); exit;
			if( !$canModified){
				/* check CanView */
				/* Truong hop canModified == false */
				$this->_userCanView($projectId );
				
				/* End check canView*/
			}
            return $canModified;
        }
        if (!$isChange) {
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
        return false;
    }
    
    protected function _pmCanModify($widget, $project_id) {
        $_isProfile = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
        $is_modify = $this->_checkRole(false, $project_id);
        $Model = ClassRegistry::init('ProfileProjectManagerDetail');
        $_canWrite = $Model->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'widget_id' => $widget,
                'model_id' => $_isProfile
            ),
            'fields' => array('read_write', 'model_id')
        ));
        $_canWrite = !empty($_canWrite) ? $_canWrite['ProfileProjectManagerDetail']['read_write'] : 0;
        $has_permission = $_canWrite || ($is_modify && !$_isProfile);
        return $has_permission;
    }
    protected function _canComment($project_id = null) {
        if( empty($project_id) ) return false;
        if( $this->_checkRole( false, $project_id) ) return true;
        $listAssign = $this->_getEmployeeAssign($project_id);
        foreach( $listAssign as $id => $emp){
            if( $emp['id'] == $this->employee_info['Employee']['id'] ) return true;
        }
        return false;
    }
    private function _getEmployeeAssign($project_id ) {
        $this->loadModels('ProjectTask','ProjectTaskEmployeeRefer', 'ProjectEmployeeManager', 'Employee', 'ProjectTeam');
        $list_tasks_ids = $resource_ids = $project_manager_ids = $list_assign_to = array();
        $list_tasks_ids = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id'),
        ));
        // get resource assign to 
        if(!empty($list_tasks_ids)){
            $resource_ids = $this->ProjectTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $list_tasks_ids,
                ),
                'fields' => array('reference_id', 'is_profit_center'),
            ));
        }
        $project_manager_ids  = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'type' => 'PM'
            ),
            'fields' => array('project_manager_id', 'is_profit_center'),
        ));

        $list_assign_to = $resource_ids + $project_manager_ids;
        $list_assigns = $employees_name = $profitCenters = array();
        if(!empty($list_assign_to)){
            $employee_ids = $pc_ids = array();
            foreach ($list_assign_to as $id => $is_profit_center) {
                if($is_profit_center == 1){
                    $pc_ids[$id] = $id;
                }else{
                    $employee_ids[$id] = $id;
                }
            }
            if($employee_ids){
                $employees_name = $this->Employee->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'id' => $employee_ids,
                        'NOT' => array('is_sas' => 1),
                    ),
                    'fields' => array('id', 'fullname')
                ));
                $employees_name = !empty($employees_name) ? Set::combine($employees_name, '{n}.Employee.id', '{n}.Employee.fullname') : array();
            }
            if($pc_ids){
                $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'order' => array('ProfitCenter.name' => 'asc'),
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'ProfitCenter.id' => $pc_ids
                    ),
                    'fields' => array('id', 'name')
                ));
                $profitCenters = !empty($profitCenters) ? Set::combine($profitCenters, '{n}.ProfitCenter.id', '{n}.ProfitCenter.name') : array();
            }
            foreach ($list_assign_to as $id => $is_profit_center) {
                if($is_profit_center){
                    $list_assigns[$id]['id'] = $id;
                    $list_assigns[$id]['is_profit_center'] = $is_profit_center;
                    if($profitCenters[$id]) $list_assigns[$id]['fullname'] = $profitCenters[$id];
                }else{
                    $list_assigns[$id]['id'] = $id;
                    $list_assigns[$id]['is_profit_center'] = $is_profit_center;
                    if( !empty($employees_name[$id])) $list_assigns[$id]['fullname'] = !empty($employees_name[$id]) ? $employees_name[$id] : '';
                }
            }
        }
        return $list_assigns;
    }
    
    
    protected function _getListEmployee($company_id = null, $return=false) {
		if( !$company_id) $company_id = $this->employee_info['Company']['id'];
		$Model = ClassRegistry::init('Employee');
		if( !empty( $this->listEmployeeName )) return $this->listEmployeeName;
		$listEmployeeName = array();
		$allEmployee = $Model->find('all', array(
			'recursive' => -1,
            'conditions' => array(
				'OR' => array(
					'company_id' => $company_id,
					'company_id is NULL',
				),
            ),
			'fields' => array('id', 'first_name', 'last_name', 'company_id', 'updated'),
			'order' => array('first_name')
		));
		foreach( $allEmployee as $emp ){
			$emp['Employee']['fullname'] = $emp['Employee']['first_name'] . ' ' . $emp['Employee']['last_name'];
			$listEmployeeName[$emp['Employee']['id']] = $emp['Employee'];
		}
		$PCModel = ClassRegistry::init('ProfitCenter');
		$listPC = $PCModel->find('list', array(
			'recursive' => -1,
            'conditions' => array(
				'company_id' => $company_id,
            ),
            'fields' => array('id', 'name'),
		));
		foreach($listPC as $pc_id => $pc_name){
			$listEmployeeName[$pc_id.'-1']['name'] = 'PC / ' . $pc_name;
			$listEmployeeName[$pc_id.'-1']['is_pc'] = 1;
		}
		$this->listEmployeeName = $listEmployeeName;
		$this->set(compact('listEmployeeName'));
		if( $return) return $listEmployeeName;
	}
    protected function _checkWriteProfile($widget) {
        $_isProfile = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
        $Model = ClassRegistry::init('ProfileProjectManagerDetail');
        $_canWrite = $Model->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'widget_id' => $widget,
                'model_id' => $_isProfile,
				'display' => 1,
            ),
            'fields' => array('read_write', 'model_id')
        ));
        $_canWrite = !empty($_canWrite) ? $_canWrite['ProfileProjectManagerDetail']['read_write'] : 0;
        $this->set(compact('_isProfile', '_canWrite'));
    }
    /**
     * Check duplicate
     * $data: mang du lieu day du thong tin. (array)
     * $model: model can kiem tra (string)
     * $field: field can kiem tra khong trung nhau (string)
     * @return void
     * @access public
     */
    protected function _checkDuplicate($data = array(), $Model = null, $field = null) {
        $this->loadModel($Model);
        $result = 0;
        $data = !empty($data[$Model] ) ? $data[$Model] : $data;
        if(empty($data['id'])){ // add
            $result = $this->$Model->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    $field => $data[$field],
                    'company_id' => $data['company_id']
                )
            ));
        } else { // edit
            $result = $this->$Model->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    $field => $data[$field],
                    'company_id' => $data['company_id'],
                    'NOT' => array(
                        $Model.'.id' => $data['id']
                    )
                )
            ));
        }
        if($result == 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Kiem tra phan quyen hien thi thu muc menu cho tung cong ty va tung employee
     */
    protected function _checkPermissionMenuAudit(){
        $this->loadModel('Employee');
        $this->loadModel('AuditAdmin');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditRecomEmployeeRefer');
        $this->loadModel('SaleLeadEmployeeRefer');
        $this->loadModel('SaleRole');
        $infos = $this->Session->read('Auth.employee_info');
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        /**
         * Lay danh sach Admin Company
         */
        $adminOfCompanies = $this->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'employee_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id, 'role_id' => 2)));
        /**
         * Lay danh sach Admin Audit
         */
        $adminAudits = $this->AuditAdmin->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('employee_id', 'employee_id')
        ));
        /**
         * Lay danh sach Mission Manager
         * Type = 1: Mission Manager
         * Type = 0: Readable by (chi dc read/view)
         */
        $missionAudits = $this->AuditMissionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
            ),
            'fields' => array('employee_id', 'employee_id')
        ));
        /**
         * Lay danh sach Recommendation Manager
         */
        $recomAudits = $this->AuditRecomEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'type' => 0
            ),
            'fields' => array('employee_id', 'employee_id')
        ));
        /**
         * Lay danh sach Sale Employee Refer
         */
        $saleEmployeeRefers = $this->SaleLeadEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('employee_id', 'employee_id')
        ));
        /**
         * Lay cac ong employee co quyen trong sale
         */
        $saleRoles = $this->SaleRole->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'NOT' => array(
                    'sale_role' => 0
                )
            ),
            'fields' => array('employee_id', 'employee_id')
        ));
        /**
         * Danh sach cac employee duoc quyen thay menu audit
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Mission Manager in Mission
         */
        $authorSeeAudits = array_unique(array_merge($adminOfCompanies, $adminAudits, $missionAudits, $recomAudits));
        /**
         * Danh sach cac employee duoc quyen thay menu business
         * Danh sach gom:
         * - Admin Company
         * - Sale Man
         * - Deal Manager
         */
        $authorSeeBusiness = array_unique(array_merge($adminOfCompanies, $saleEmployeeRefers, $saleRoles));
        $seeMenuAudits = $seeMenuBusiness = false;
        if(in_array($employeeLogin, $authorSeeAudits)){
            $seeMenuAudits = true;
        }
        if(in_array($employeeLogin, $authorSeeBusiness)){
            $seeMenuBusiness = true;
        }
        return array($seeMenuAudits, $seeMenuBusiness);
    }

    /**
     * Enable module of company
     */
    protected function _enableModule(){
        $this->loadModel('Company');
        $infos = $this->Session->read('Auth.employee_info');
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $modules = $this->Company->find('first', array(
            'recursive' => -1,
            'conditions' => array('Company.id' => $company_id),
            'fields' => array('*')
        ));
        $enablePMS = $enableRMS = $enableAudit = $enableReport = $enableBusines = $enableZogMsgs = $enableTicket = false;
        if($modules['Company']['module_pms'] == 1 &&  $modules['Company']['module_rms'] == 0){
            $enablePMS = true;
        }elseif($modules['Company']['module_pms'] == 0 &&  $modules['Company']['module_rms'] == 1){
            $enableRMS = true;
        }else{
            $enablePMS = true;
            $enableRMS = true;
        }
        if(!empty($modules['Company']['module_audit']) && $modules['Company']['module_audit'] == 1){
            $enableAudit = true;
        }
        if(!empty($modules['Company']['module_report']) && $modules['Company']['module_report'] == 1){
            $enableReport = true;
        }
        if(!empty($modules['Company']['module_busines']) && $modules['Company']['module_busines'] == 1){
            $enableBusines = true;
        }
        if(!empty($modules['Company']['module_zogmsgs']) && $modules['Company']['module_zogmsgs'] == 1){
            $enableZogMsgs = true;
        }
        if(!empty($modules['Company']['module_ticket']) && $modules['Company']['module_ticket'] == 1){
            $enableTicket = true;
        }
        return array($enablePMS, $enableRMS, $enableAudit, $enableReport, $enableBusines, $enableZogMsgs, $enableTicket);
    }
    protected function employeeHasReport(){
		if( empty($this->employee_info)) return false;
        $employee = $this->employee_info;
        $company_id = $employee['Company']['id'];
        $this->loadModel('Company');
        //check for company
        $enableReport = $this->Company->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'Company.id' => $company_id,
                'Company.module_report' => 1
            ),
        ));
        if( !$enableReport) return false;
        $this->loadModel('SqlManagerEmployee');
        $employeeHasReport = $this->SqlManagerEmployee->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'employee_id' => $employee['Employee']['id']
            ),
        ));
        $employeeHasReport = (boolean) $employeeHasReport;
        return $employeeHasReport;

    }

    /**
     * Delete File Cache Of Context Menu In Folder: app/tmp/cache
     */
    protected function _deleteCacheContextMenu(){
        $files = glob(CACHE . '*');
        foreach ($files as $file) {
            if (preg_match('/(\.|\.\.)$/', $file)) {
                continue;
            }
            if (is_file($file) === true) {
                @unlink($file);
            }
        }
    }
	/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug)
	* Require 'ZEmail' component
	*/
    protected function _z0GSendEmail($to=array(), $cc=array(), $bcc=array(), $subject, $element, $fileAttach = null, $debug = false){
		if( !class_exists( 'ZEmailComponent')){
			return false;
		}
		$Email = new ZEmailComponent();
		$Email->initialize($this);
		if(file_exists(CONFIGS . 'mail_setting.php')){
            include_once CONFIGS . 'mail_setting.php';
            $mails = new MailSetting();
            $_data = $mails->mailConfig;
            if( $_data['host'] == '{default_host}' ||
                $_data['port'] == '{default_port}' ||
                $_data['username'] == '{default_username}' ||
                $_data['password'] == '{default_password}' ||
                $_data['transport'] == '{default_transport}'){
					return false;
            } else {
                $Email->smtpOptions = $_data;
                // $Email->from = $_data['username'];
				$Email->from = 'noreply@z0gravity.com';
            }
        }else{
            return false;
			// die('mail_setting is not exists');
		}
		$Email->delivery = 'smtp';
        $Email->to = $to;
        $Email->cc = $cc;
        $Email->bcc = $bcc;
        $Email->subject = $subject;
        $Email->sendAs = 'html';
        $Email->template = $element;
		$Email->attachments = array($fileAttach);
		$Email->showDebug = $debug;
        /* End */
        try {
            $result = $Email->send();
        } catch (Exception $e) {
        }
		if( !empty($Email->zEmailError)) $this->Session->setFlash( sprintf(__('Error sending email to: %s', true), implode(', ', array_keys($Email->zEmailError))), 'warning');
		// debug( $Email->zEmailError); exit;
		return $result;		
	}
	
	protected function _sendEmail($to, $subject, $element, $debug = false, $cc = array(), $fileAttach = null) {
        /* Send mail */
        //if (env('REMOTE_ADDR') === '127.0.0.1') {
//            return true;
//        }
        App::import('Component', 'Email');
        $Email = new EmailComponent();
        $Email->initialize($this);
        if(file_exists(CONFIGS . 'mail_setting.php')){
            include_once CONFIGS . 'mail_setting.php';
            $mails = new MailSetting();
            $_data = $mails->mailConfig;
            if( $_data['host'] == '{default_host}' ||
                $_data['port'] == '{default_port}' ||
                $_data['username'] == '{default_username}' ||
                $_data['password'] == '{default_password}' ||
                $_data['transport'] == '{default_transport}'){
					return false;
            } else {
                $Email->smtpOptions = $_data;
                // $Email->from = $_data['username'];
				$Email->from = 'noreply@z0gravity.com';
            }
        }else{
			return false;
		}
        $Email->delivery = 'smtp';
        $Email->to = $to;
        $Email->cc = $cc;
        // $Email->bcc = array('info@greensystem.vn');
        $Email->subject = $subject;
        $Email->sendAs = 'html';
        $Email->template = $element;
        /**
         * $fileAttach la 1 chuoi string
         */
        $Email->attachments = array($fileAttach);
        /* End */
        $Email->showDebug = $debug;
        try {
            $result = $Email->send();
        } catch (Exception $e) {

        }
        return $result;
    }

    public function showDebug() {
        include LIBS . 'view' . DS . 'elements' . DS . 'sql_dump.ctp';
        exit();
    }

    public function initProjectFile(){
        if(file_exists(KEYS . 'projectkey.php')){
            include KEYS . 'projectkey.php';
            $checkKeyPM = new checkKeyPM();
            $check = $checkKeyPM->validatePM();
            if($check == 'true'){
                //do nothing
            } else {
                $this->render('/pages/error');
                $this->afterFilter();
                echo $this->output;
                $this->_stop();
            }
        } else {
            $this->render('/pages/error');
            $this->afterFilter();
            echo $this->output;
            $this->_stop();
        }

    }

    public function initAbsencesFile(){
        if(file_exists(KEYS . 'absenceskey.php')){
            include KEYS . 'absenceskey.php';
            $checkKeyAB = new checkKeyAB();
            $check = $checkKeyAB->validateAB();
            if($check == 'true'){
                //do nothing
            } else {
                $this->render('/pages/error');
                $this->afterFilter();
                echo $this->output;
                $this->_stop();
            }
        } else {
            $this->render('/pages/error');
            $this->afterFilter();
            echo $this->output;
            $this->_stop();
        }

    }

    public function initActivityFile(){
        if(file_exists(KEYS . 'activitykey.php')){
            include KEYS . 'activitykey.php';
            $checkKeyAC = new checkKeyAC();
            $check = $checkKeyAC->validateAC();
            if($check == 'true'){
                //do nothing
            } else {
                $this->render('/pages/error');
                $this->afterFilter();
                echo $this->output;
                $this->_stop();
            }
        } else {
            $this->render('/pages/error');
            $this->afterFilter();
            echo $this->output;
            $this->_stop();
        }

    }

    public function initBudgetFile(){
        if(file_exists(KEYS . 'budgetkey.php')){
            include KEYS . 'budgetkey.php';
            $checkKeyBG = new checkKeyBG();
            $check = $checkKeyBG->validateBG();
            if($check == 'true'){
                //do nothing
            } else {
                $this->render('/pages/error');
                $this->afterFilter();
                echo $this->output;
                $this->_stop();
            }
        } else {
            $this->render('/pages/error');
            $this->afterFilter();
            echo $this->output;
            $this->_stop();
        }

    }

    public function initAuditFile(){
        if(file_exists(KEYS . 'auditkey.php')){
            include KEYS . 'auditkey.php';
            $checkKeyAC = new checkKeyAU();
            $check = $checkKeyAC->validateAU();
            if($check == 'true'){
                //do nothing
            } else {
                $this->render('/pages/error');
                $this->afterFilter();
                echo $this->output;
                $this->_stop();
            }
        } else {
            $this->render('/pages/error');
            $this->afterFilter();
            echo $this->output;
            $this->_stop();
        }
    }

    /**
     * Ham dung de thay doi ngay bat dau duoc nghi cua cac loai vang nghi theo nam
     * Cac truong hop thay doi va luu history gom:
     * - Loai vang nghi phai ton tai begin of period
     * - Nam hien tai phai lon hon nam trong begin of period
     *
     */
    private function _changeBeginOfPeriodFollowYear(){
        $this->loadModel('Absence');
        $this->loadModel('AbsenceHistory');
        $absences = $this->Absence->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('begin' => array('', '0000-00-00'))
            ),
            'fields' => array('id', 'begin', 'total', 'company_id')
        ));
        $currentYear = date('Y', time());
        if(!empty($absences)){
            $absences = Set::combine($absences, '{n}.Absence.id', '{n}.Absence');
            foreach($absences as $id => $absen){
                $begin = !empty($absen['begin']) ? strtotime($absen['begin']) : '';
                if($currentYear > date('Y', $begin)){
                    $data['begin'] = $currentYear.'-'.date('m', $begin).'-'.date('d', $begin);
                    $this->Absence->id = $id;
                    if($this->Absence->save($data)){
                        $saved = array(
                            'absence_id' => $id,
                            'total' => $absen['total'],
                            'begin' => $data['begin'],
                            'day' => date('d', $begin),
                            'month' => date('m', $begin),
                            'year' => $currentYear,
                            'company_id' => $absen['company_id']
                        );
                        $tmp = $this->AbsenceHistory->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'AbsenceHistory.absence_id' => $id,
                                'AbsenceHistory.year' => $currentYear,
                                'AbsenceHistory.company_id' => $absen['company_id']
                            ),
                            'fields' => array('id')
                        ));
                        $this->AbsenceHistory->create();
                        if(!empty($tmp) && !empty($tmp['AbsenceHistory']['id'])){
                            $this->AbsenceHistory->id = $tmp['AbsenceHistory']['id'];
                        }
                        $this->AbsenceHistory->save($saved);
                    }
                }
            }
        }
    }
    /**
     * Ham dung de lay ngay theo thang
     *
     */
    public function _showDayInMonth($month,$year){
        $day = array();
        switch ($month) {
            case 'Jan':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Jan '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Jan '.$year)));
                }
                break;
            case 'Feb':
                $limit=($year%4)?28:29;
                for($i=1;$i<=$limit;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Feb '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Feb '.$year)));
                }
                break;
            case 'Mar':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Mar '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Mar '.$year)));
                }
                break;
            case 'Apr':
                for($i=1;$i<=30;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Apr '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Apr '.$year)));
                }
                break;
            case 'May':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' May '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' May '.$year)));
                }
                break;
            case 'Jun':
                for($i=1;$i<=30;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Jun '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Jun '.$year)));
                }
                break;
            case 'Jul':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Jul '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Jul '.$year)));
                }
                break;
            case 'Aug':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Aug '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Aug '.$year)));
                }
                break;
            case 'Sep':
                for($i=1;$i<=30;$i++) {
                   $day[$i][0] = date('l d M',strtotime($i.' Sep '.$year));
                   $day[$i][1] = strtolower(date('l',strtotime($i.' Sep '.$year)));
                }
                break;
            case 'Oct':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Oct '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Oct '.$year)));
                }
                break;
            case 'Nov':
                for($i=1;$i<=30;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Nov '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Nov '.$year)));
                }
                break;
            case 'Dec':
                for($i=1;$i<=31;$i++) {
                    $day[$i][0] = date('l d M',strtotime($i.' Dec '.$year));
                    $day[$i][1] = strtolower(date('l',strtotime($i.' Dec '.$year)));
                }
                break;
        }
        return $day;
    }
     /**
     * Ham dung de lay ngay theo nam
     */
    public function _showDayInYear($year){
        $day = array();
            for($i=1;$i<31;$i++) {
                $day[$i][0] = date('l d M',strtotime($i.' Jan '.$year));
                $day[$i][1] = strtolower(date('l',strtotime($i.' Jan '.$year)));
                $day[$i][2] = strtotime($i.' Jan '.$year);
            }
            $limit=($year%4)?28:29;
            for($j=$i;$j<$limit+31;$j++) {
                $day[$j][0] = date('l d M',strtotime($j-$i.' Feb '.$year));
                $day[$j][1] = strtolower(date('l',strtotime($j-$i.' Feb '.$year)));
                $day[$j][2] = strtotime($j-$i.' Feb '.$year);
            }
            for($k=$j;$k<31+$j;$k++) {
                $day[$k][0] = date('l d M',strtotime($k-$j.' Mar '.$year));
                $day[$k][1] = strtolower(date('l',strtotime($k-$j.' Mar '.$year)));
                $day[$k][2] = strtotime($k-$j.' Mar '.$year);
            }
            for($l=$k;$l<30+$k;$l++) {
                $day[$l][0] = date('l d M',strtotime($l-$k.' Apr '.$year));
                $day[$l][1] = strtolower(date('l',strtotime($l-$k.' Apr '.$year)));
                $day[$l][2] = strtotime($l-$k.' Apr '.$year);
            }
            for($m=$l;$m<31+$l;$m++) {
                $day[$m][0] = date('l d M',strtotime($m-$l.' May '.$year));
                $day[$m][1] = strtolower(date('l',strtotime($m-$l.' May '.$year)));
                $day[$m][2] = strtotime($m-$l.' May '.$year);
            }
            for($n=$m;$n<30+$m;$n++) {
                $day[$n][0] = date('l d M',strtotime($n-$m.' Jun '.$year));
                $day[$n][1] = strtolower(date('l',strtotime($n-$m.' Jun '.$year)));
                $day[$n][2] = strtotime($n-$m.' Jun '.$year);
            }
            for($o=$n;$o<31+$n;$o++) {
                $day[$o][0] = date('l d M',strtotime($o-$n.' Jul '.$year));
                $day[$o][1] = strtolower(date('l',strtotime($o-$n.' Jul '.$year)));
                $day[$o][2] = strtotime($o-$n.' Jul '.$year);
            }
            for($p=$o;$p<31+$o;$p++) {
                $day[$p][0] = date('l d M',strtotime($p-$o.' Aug '.$year));
                $day[$p][1] = strtolower(date('l',strtotime($p-$o.' Aug '.$year)));
                $day[$p][2] = strtotime($p-$o.' Aug '.$year);
            }
            for($q=$p;$q<30+$p;$q++) {
                $day[$q][0] = date('l d M',strtotime($q-$p.' Sep '.$year));
                $day[$q][1] = strtolower(date('l',strtotime($q-$p.' Sep '.$year)));
                $day[$q][2] = strtotime($q-$p.' Sep '.$year);
            }
            for($r=$q;$r<31+$q;$r++) {
                $day[$r][0] = date('l d M',strtotime($r-$q.' Oct '.$year));
                $day[$r][1] = strtolower(date('l',strtotime($r-$q.' Oct '.$year)));
                $day[$r][2] = strtotime($r-$q.' Oct '.$year);
            }
            for($s=$r;$s<30+$r;$s++) {
                $day[$s][0] = date('l d M',strtotime($s-$r.' Nov '.$year));
                $day[$s][1] = strtolower(date('l',strtotime($s-$r.' Nov '.$year)));
                $day[$s][2] = strtotime($s-$r.' Nov '.$year);
            }
            for($t=$s;$t<=31+$s;$t++) {
                $day[$t][0] = date('l d M',strtotime($t-$s.' Dec '.$year));
                $day[$t][1] = strtolower(date('l',strtotime($t-$s.' Dec '.$year)));
                $day[$t][2] = strtotime($t-$s.' Dec '.$year);
            }
        return $day;
    }
    public $systemConfigs;
    public $companyConfigs;
    function _getAllConfigs(){
        $infos = $this->Session->read('Auth.employee_info');
        if($infos['Employee']['is_sas'] != 1) {
            $this->loadModel('CompanyConfig');
            $this->companyConfigs = $this->CompanyConfig->find('list',array(
                'recursive' => -1,
                'conditions' => array('CompanyConfig.company' => $infos['Company']['id']),
                'fields' => array('cf_name', 'cf_value')
            ));
			if( empty($this->companyConfigs) ) $this->companyConfigs = array();
			$config_default = array(			
				'capacity_by_month_1' => '8.33',
				'capacity_by_month_2' => '8.33',
				'capacity_by_month_3' => '8.33',
				'capacity_by_month_4' => '8.33',
				'capacity_by_month_5' => '8.33',
				'capacity_by_month_6' => '8.33',
				'capacity_by_month_7' => '8.33',
				'capacity_by_month_8' => '8.33',
				'capacity_by_month_9' => '8.33',
				'capacity_by_month_10' => '8.33',
				'capacity_by_month_11' => '8.33',
				'capacity_by_month_12' => '8.37', // Default theo Innovation
			);
			$this->companyConfigs = array_merge($config_default, $this->companyConfigs);
            $this->Session->write('companyConfigs',$this->companyConfigs);
            $this->set('companyConfigs', $this->companyConfigs);
        }
        $this->loadModel('SystemConfig');
        $this->systemConfigs = $this->SystemConfig->find('list',array(
            'recursive' => -1,
            'conditions' => array(),
            'fields' => array('cf_name', 'cf_value')
        ));
        $this->set('systemConfigs', $this->systemConfigs);
    }
    function getCompanyConfig($name){
        return isset($this->companyConfigs[$name]) ? $this->companyConfigs[$name] : array();
    }
    function getConfig($name){
        return isset($this->systemConfigs[$name]) ? $this->systemConfigs[$name] : null;
    }
    protected function _is($type = 'POST'){
        return $_SERVER['REQUEST_METHOD'] == $type;
    }
    protected function writeLog($data, $user = array(), $message = '', $cid = null){
        //check setting to ignore
        $settingLogin = isset($this->companyConfigs['action_dont_store_login']) ? $this->companyConfigs['action_dont_store_login'] : 0;
        if( $settingLogin && $this->params['controller'] == 'employees' && ($this->params['action'] == 'login' || $this->params['action'] == 'logout' || $this->params['action'] == 'get_company_role' ) )
            return;
        //for security reason:
        if( is_array($data) ){
            if( isset($data['Employee']['password']) )unset($data['Employee']['password']);
            if( isset($data['Employee']['password_old']) )unset($data['Employee']['password_old']);
            if( isset($data['Employee']['confirm_password']) )unset($data['Employee']['confirm_password']);
        }
        if( !$cid ){
            // $cid = isset($user['Company']['id']) ? $user['Company']['id'] : 0;
			// Ticket #981 Log cua SAS khong ghi vao company
			$cid = isset($user['Employee']['company_id']) ? $user['Employee']['company_id'] : 0;
        }
        //clean last 2 months
        // $this->ActionLog->deleteAll(array(
        //     'company_id' => $cid,
        //     'created <' => date('Y-m-d H:i:s', strtotime('2 months ago'))
        // ));
		$realIP = $this->RequestHandler->getClientIP(false);
		$safeIP = $this->RequestHandler->getClientIP(true);
		$loginIP = ($realIP == $safeIP) ? $realIP : $realIP . '/' . $safeIP;
        $this->loadModels('ActionLog');
        $this->ActionLog->create();
        $this->ActionLog->save(array(
            'url' => $this->params['url']['url'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'data' => is_array($data) ? json_encode($data) : $data,
            'employee_id' => isset($user['Employee']['id']) ? $user['Employee']['id'] : 0,
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $loginIP,
            'company_id' => $cid,
            'what' => $message
        ));
    }
    private function _trackAdmin(){
        $list = array(
            'cities' => array('edit' => 'POST', 'delete' => 'GET'),
            'countries' => array('edit' => 'POST', 'delete' => 'GET'),
            'companies' => array('edit' => 'POST'),
            'profit_centers' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_phases' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_statuses' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_priorities' => array('edit' => 'POST', 'delete' => 'GET'),
            'currencies' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_types' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_sub_types' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_complexities' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_settings' => array('index' => 'POST'),
            'project_created_values' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_functions' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_phase_statuses' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_risk_severities' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_risk_occurrences' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_issue_severities' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_issue_statuses' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_livrable_categories' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_evolution_types' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_evolution_impacts' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_programs' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_sub_programs' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_categories' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_sub_categories' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_statuses' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_cost_controls' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_organizations' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_plans' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_perimeters' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_risk_controls' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_problem_controls' => array('edit' => 'POST', 'delete' => 'GET'),
            'project_amr_organizations' => array('edit' => 'POST', 'delete' => 'GET'),
            'budget_settings' => array('update' => 'POST', 'delete' => 'GET'),
            'budget_customers' => array('update' => 'POST', 'delete' => 'GET'),
            'budget_providers' => array('update' => 'POST', 'delete' => 'GET'),
            'budget_types' => array('update' => 'POST', 'delete' => 'GET'),
            'security_settings' => array('index' => 'POST'),
            'absences' => array('update' => 'POST', 'delete' => 'GET', 'edit_attachment' => 'POST'), //
            'workdays' => array('update' => 'POST'),
            'response_constraints' => array('update' => 'POST', 'delete' => 'GET'),
            'holidays' => array('update' => 'POST'),
            'activity_columns' => array('update' => 'POST'),
            'activity_families' => array('update' => 'POST'),
            'activity_columns' => array('update' => 'POST'),
            'activity_settings' => array('index' => 'POST'),
            'activity_exports' => array('update' => 'POST'),
            'translations' => array('save' => 'POST', 'saveSettings' => 'POST', 'saveOrder' => 'POST'),
            'liscenses' => array(
                'index' => 'POST',
                'absence' => 'POST',
                'activity' => 'POST',
                'budget' => 'POST',
                'audit' => 'POST'
            ),
            'menus' => array('update' => 'POST'),
            'contract_types' => array('update' => 'POST', 'delete' => 'GET')
        );
        $controller = $this->params['controller'];
        $action = $this->params['action'];
        $method = $_SERVER['REQUEST_METHOD'];
        $passes = $this->params['pass'];
        //hook the current controller
        if( isset($list[$controller]) ){
            //hook the current action
            $actions = $list[$controller];
            if( isset($actions[$action]) ){
                //hook the current method
                if( $actions[$action] == $method ){
                    //ok, log it
                    if( $method == 'GET' ){
                        $data = $passes;
                    } else {
                        $data= $this->data;
                    }
                    //build the message
                    switch($controller){
                        case 'translations':
                            $message = 'Update translation';
                        break;
                        case 'liscenses':
                            if( $action == 'index' )$action = 'project';
                            $message = sprintf('Update liscense `%s`', $action);
                        break;
                        case 'absences':
                            if( $action == 'edit_attachment' ){
                                $message = sprintf('Update absence attachment setting');
                                $data = $passes;
                            } else {
                                goto _default_;
                            }
                            break;
                        break;
                        default:
                            _default_:
                            if( $method == 'POST' && in_array($action, array('index', 'update', 'edit')) ){
                                $message = sprintf('Update %s', Inflector::humanize(Inflector::singularize($controller)));
                            } else {
                                $message = sprintf('%s %s', Inflector::humanize($action), Inflector::humanize(Inflector::singularize($controller)));
                            }
                        break;
                    }
                    $this->writeLog($data, $this->employee_info, $message);
                }
            }
        }
    }
    private function _loadTranslate(){
        if( isset($this->employee_info["Employee"]) ){
            //sync
            $folder = new Folder(UPLOADS . 'locale');
            if( $folder->path != null ){
                //copy all files in this folders
                $folder->copy(array(
                    'to' => APP . 'locale',
                    'mode' => 0777,
                    'scheme' => Folder::MERGE
                ));
                //attempt to delete
                $this->rmdir(UPLOADS . 'locale' . DS);
            }
            if ($this->employee_info["Employee"]["is_sas"] != 1){
                $company_id = $this->employee_info["Company"]["id"];
                $this->set('_domain', '%s_'.$company_id);
            }
            else {
                $this->set('_domain', 'default');
            }
        }
    }
    private function rmdir($path){
        $files = array_diff(scandir($path), array('.', '..'));
        foreach($files as $file){
            if(is_dir($path . DS . $file)){
                $this->rmdir($path . DS . $file);
            }
            if(is_file($path . DS . $file)){
                unlink($path . DS . $file);
            }
        }
        if(is_dir($path)){
           rmdir($path);
        }
    }
    public function loadModels(){
        $models = func_get_args();
        foreach($models as $model){
            $this->loadModel($model);
        }
    }

    public function checkModifyBudget(){
        $Employee = ClassRegistry::init('Employee');
        $employInfors = $Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $this->employee_info["Employee"]["id"]),
            'fields' => array('update_budget')
        ));
        $employInfors = !empty($employInfors) && !empty($employInfors['Employee']['update_budget']) ? true : false;
        $allowEdit = false;
        $EPM_see_the_budget = isset($this->companyConfigs['EPM_see_the_budget']) && !empty($this->companyConfigs['EPM_see_the_budget']) ?  true : false;
        if( ($this->employee_info["Role"]["name"] == 'pm' && $employInfors) || $this->employee_info["Role"]["name"] == 'admin'){
            $allowEdit = true;
        }
        return $allowEdit;
    }
	public function getCurrencyOfBudget(){
        $BudgetSetting = ClassRegistry::init('BudgetSetting');
		$company_id = $this->employee_info["Company"]["id"];
		$budget_settings= $BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id ,
                'currency_budget' =>1,
            ),
            'fields' => array('name')
        ));
        $budget_settings = !empty($budget_settings) ? $budget_settings['BudgetSetting']['name'] : '&euro;';
		return $budget_settings;
	}
	
	
	/* Function updateEmployeeAssignedToTeam
	* z0G 17/9/2019 - Issue when activate Team screen
	* z0G 17/9/2019 - Issue when assign to resource not defined in project team
	* $list_empl_assign =  Array (
			[0] => Array (
				[reference_id] => 2337
				[is_profit_center] => 0
			),
		)
	*/
	function updateEmployeeAssignedToTeam($project_id = null, $list_empl_assign = null){
		// debug( $project_id);
		// debug( $list_empl_assign); exit;
		if( empty($list_empl_assign) || empty($project_id) ) return false;
		$this->loadModels('ProjectTeam', 'Employee', 'ProjectFunctionEmployeeRefer');
		$assignToEmp = $assignToPC = array();
		foreach ($list_empl_assign as $employee) {
			if($employee['is_profit_center']){
				$assignToPC[] = $employee['reference_id'];
			}else{
				$assignToEmp[] = $employee['reference_id'];
				
			}
		}
		// debug( $assignToEmp); exit;
		// Get list PC of Employees
		$listPC = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array('id' => $assignToEmp),
			'fields' => array('id', 'profit_center_id')
		));
		// debug( $listPC); exit;
		// debug( $listPC);exit;
		// lay cac team cua project
		$teams = $this->ProjectTeam->find('list', array(
			'recursive' => -1,
			'conditions' => array('ProjectTeam.project_id' => $project_id),
			'fields' => array('id', 'id')
		));
		
		// lay pc va employee cua team thuoc project
		$allPcAndEmpOfTeams = $this->ProjectFunctionEmployeeRefer->find('all', array(
			'recursive' => -1,
			'conditions' => array('project_team_id' => $teams),
			'fields' => array('id', 'employee_id', 'profit_center_id', 'project_team_id')
		));
		// debug( $allPcAndEmpOfTeams);exit;
		$listPcAndEmpOfTeams = array();
		$listPCofTeam = $listEmofTeam = array();
		foreach( $allPcAndEmpOfTeams as $_pFunc){
			$_pFunc = $_pFunc['ProjectFunctionEmployeeRefer'];
			$listPCofTeam[] = $_pFunc['profit_center_id'];
			if( empty( $listPcAndEmpOfTeams[$_pFunc['profit_center_id']])) $listPcAndEmpOfTeams[$_pFunc['profit_center_id']] = array();
			if( !empty($_pFunc['employee_id'])){
				// debug( $_pFunc);
				$listEmofTeam[] = $_pFunc['employee_id'];
				$listPcAndEmpOfTeams[$_pFunc['profit_center_id']][$_pFunc['employee_id']] = $_pFunc;
			}
		}
		// debug($listPcAndEmpOfTeams);
		// debug($listPCofTeam);
		// debug($listEmofTeam);
		// exit;
		
		foreach( $list_empl_assign as $employee){
			$teamId ='';
			if( $employee['is_profit_center']){
				// Check pc is exist
				if( !in_array( $employee['reference_id'], $listPCofTeam)){
					// Create team item
					$this->ProjectTeam->create();
					$this->ProjectTeam->save(array(
						'project_id' => $project_id,
						'created' => time(),
						'updated' => time(),
					));
					// debug($this->ProjectTeam->id); exit;
					$teamId = $this->ProjectTeam->id;
					$this->ProjectFunctionEmployeeRefer->create();
					$this->ProjectFunctionEmployeeRefer->save(array(
						'function_id' => 0,
						'profit_center_id' => $employee['reference_id'],
						'project_team_id' => $teamId,
						'is_backup' => 0,
						'allow_request' => 0,
						'created' => time(),
						'updated' => time(),
					));
					$listPCofTeam[] = $employee['reference_id'];
				}
			}else{
				if( !in_array( $employee['reference_id'], $listEmofTeam)){
					// Check has PC 
					$_pc = $listPC[$employee['reference_id']];
					// if( empty( $_pc) 
					if( !in_array( $_pc, $listPCofTeam)){
						// Create team item
						$this->ProjectTeam->create();
						$this->ProjectTeam->save(array(
							'project_id' => $project_id,
							'created' => time(),
							'updated' => time(),
						));
						// debug($this->ProjectTeam->id); exit;
						$teamId = $this->ProjectTeam->id;
						$this->ProjectFunctionEmployeeRefer->create();
						$this->ProjectFunctionEmployeeRefer->save(array(
							'function_id' => 0,
							'profit_center_id' => $_pc,
							'employee_id' => $employee['reference_id'],
							'project_team_id' => $teamId,
							'is_backup' => 0,
							'allow_request' => 0,
							'created' => time(),
							'updated' => time(),
						));
						$listPCofTeam[] = $_pc;
						$listEmofTeam[] = $employee['reference_id'];
					}else{
						$teamId = $this->ProjectFunctionEmployeeRefer->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'profit_center_id' => $_pc,
							),
							'fields' => array('id', 'project_team_id', 'employee_id'),
						));
						$empty_Emp = empty($teamId['ProjectFunctionEmployeeRefer']['employee_id']);
						if($empty_Emp){
							$this->ProjectFunctionEmployeeRefer->id = $teamId['ProjectFunctionEmployeeRefer']['id'];
						}else{
							$this->ProjectFunctionEmployeeRefer->create();
						}
						$teamId = $teamId['ProjectFunctionEmployeeRefer']['project_team_id'];
						$this->ProjectFunctionEmployeeRefer->save(array(
							'function_id' => 0,
							'profit_center_id' => $_pc,
							'employee_id' => $employee['reference_id'],
							'project_team_id' => $teamId,
							'is_backup' => 0,
							'allow_request' => 0,
							'created' => time(),
							'updated' => time(),
						));
						$listEmofTeam[] = $employee['reference_id'];
					}
				}
			}
		}
		return true;
	}
	/*
	* END z0G 17/9/2019 - Issue when activate Team screen
	*/

    // Functions dung cho Notification

    // $type = 'Message' or 'Comment' or 'New_Task' or 'Update_Task'
    private function getTokensFor($type='Message', $company_id=null, $project_id=null, $task_id=null) {
		$this->loadModels('Project', 'NotifyToken', 'Employee');
        $listUsers = array();
        $listAdmin=array();
        $listPM=array();

        $roles = array('PM');
        $queryTokens = array(
			'recursive' => -1,
			'conditions'=> array(
                // 'employee_id' => array_unique($listUsers)
            ),
            'fields' => array(
                'token'
            )
			);
        switch ($type) {
            case 'Message':
                if($type == 'Message') $queryTokens['conditions']['notification_message'] = 1;
            case 'Comment':
                if($type == 'Comment') $queryTokens['conditions']['notification_message_project'] = 1;
                $roles = array_merge($roles,array('TM', 'RA'));
                // $listAdmin = $this->Project->listAdminCompany($company_id); // Admin ko nhan notify khi co message & comment
                $listPM = $this->Project->listPMProject($project_id, $roles);
                break;
            case 'New_Task':
                if($type == 'New_Task') $queryTokens['conditions']['notification_task_new'] = 1;
            case 'Update_Task':
                if($type == 'Update_Task') $queryTokens['conditions']['notification_task_update'] = 1;
            case 'Delete_Task':
                if($type == 'Delete_Task') $queryTokens['conditions']['notification_task_update'] = 1;
                $roles = array_merge($roles,array('TM'));
                $assigned = $this->Project->listAssignedTask($task_id);
                $listUsers = array_merge($listUsers, $assigned);
                break;
            
            default:
                # code...
                break;
        }

        if(empty($listAdmin)) $listAdmin=array();
		// $listPM = $this->Project->listPMProject($project_id, $roles);
        // Debug($listPM);
        // Debug($listUsers);
        // exit;
        if(empty($listPM)) $listPM=array();
        $listUsers = array_merge($listUsers,$listPM, $listAdmin);

        // $listUsersByLanguage = $this->Employee->find('list', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'id' => $listUsers
        //     ),
        //     'fields' => array('id', 'language')
        // ));
        // $listUser_en = array_filter($listUsersByLanguage, function ($v, $k){ return $v=="en"; }, ARRAY_FILTER_USE_BOTH); 
        // $listUser_fr = array_filter($listUsersByLanguage, function ($v, $k){ return $v=="fr"; }, ARRAY_FILTER_USE_BOTH); 

        $queryTokens['conditions']['employee_id'] = array_unique($listUsers);
        $queryTokens['conditions']['language'] = 'en';
        
        $tokens['en'] = $this->NotifyToken->find('list', $queryTokens);
        
        // $queryTokens['conditions']['employee_id'] = array_unique($listUsers);
        $queryTokens['conditions']['language'] = 'fr';
        $tokens['fr'] = $this->NotifyToken->find('list', $queryTokens);
        
        

        // Debug($tokens);
        // Debug($listUsers);
        // exit;
        return $tokens;
    }
    // protected function notifyForMessage($project_id=null, $title=null, $body=null, $arr_data=null) {
    protected function notifyForMessage($project_id=null, $message='message') {
        if(!MAppNotifyExpo::isEnableNotifyModule()) return 'Notify module is disable';

		$company_id = $this->employee_info['Company']['id'];
        $tokens = $this->getTokensFor('Message', $company_id, $project_id);
        
        // Chuan bi noi dung de gui:
        $this->loadModel('Project');
        $PjName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        
        $fullName = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
        $PjName = !empty($PjName) ? $PjName['Project']['project_name'] : '';
        $title = 'New: Message "'.$message.'"';
        $body = 'Project '.$PjName.' from '.$fullName;

        $title_fr = 'Nouveau: message "'.$message.'"';
        $body_fr = 'Projet '.$PjName.' de '.$fullName;
        $arr_data['model'] = 'Message';
        $arr_data['project_id'] = $project_id;
        
        // Gui Notify:
        $result_en = $this->ZNotifyExpo->send_notify_expo($tokens['en'], $title, $body, $arr_data);
        $result_fr = $this->ZNotifyExpo->send_notify_expo($tokens['fr'], $title_fr, $body_fr, $arr_data);

        $this->writeLog(json_encode($result_en), $this->employee_info, sprintf('Send Notify-Message-en'), $company_id);
        $this->writeLog(json_encode($result_fr), $this->employee_info, sprintf('Send Notify-Message-fr'), $company_id);
        
        $result['en'] = $result_en;
        $result['fr'] = $result_fr;

        return $result;
	}

    // protected function notifyForComment($project_id=null, $title=null, $body=null, $arr_data=null) {
    protected function notifyForComment($project_id=null, $comment_model='ProjectAmr', $comment='', $arr_data=null) {
		if(!MAppNotifyExpo::isEnableNotifyModule()) return 'Notify module is disable';
        $company_id = $this->employee_info['Company']['id'];
        $tokens = $this->getTokensFor('Comment', $company_id, $project_id);

        // Chuan bi noi dung de gui:
        $this->loadModel('Project');
        $PjName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        $fullName = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
        $PjName = !empty($PjName) ? $PjName['Project']['project_name'] : '';

        switch ($comment_model) {
            case 'ProjectAmr':
                $title = 'New: progress "'.$comment.'"';
                $title_fr = 'Nouveau: avancement "'.$comment.'"';
                break;
            case 'Done':
                $title = 'New: done "'.$comment.'"';
                $title_fr = 'Nouveau: décisions "'.$comment.'"';
                break;
            case 'ProjectRisk':
                $title = 'New: risk "'.$comment.'"';
                $title_fr = 'Nouveau: risque "'.$comment.'"';
                break;
            case 'ToDo':
                $title = 'New: todo "'.$comment.'"';
                $title_fr = 'Nouveau: todo "'.$comment.'"';
                break;
            
            default:
                $title = 'New: comment "'.$comment.'"';
                $title_fr = 'Nouveau: commentaire "'.$comment.'"';
                break;
        }

        $body = 'Project '.$PjName.' from '.$fullName;
        $body_fr = 'Projet '.$PjName.' de '.$fullName;

        $arr_data['model'] = 'Comment';
        $arr_data['type'] = $comment_model;
        $arr_data['project_id'] = $project_id;

        // Gui Notify:
        $result_en = $this->ZNotifyExpo->send_notify_expo($tokens['en'], $title, $body, $arr_data);
        $result_fr = $this->ZNotifyExpo->send_notify_expo($tokens['fr'], $title_fr, $body_fr, $arr_data);

        $this->writeLog(json_encode($result_en), $this->employee_info, sprintf('Send Notify-Comment-en'), $company_id);
        $this->writeLog(json_encode($result_fr), $this->employee_info, sprintf('Send Notify-Comment-fr'), $company_id);
        
        $result['en'] = $result_en;
        $result['fr'] = $result_fr;

        return $result;
	}
    // protected function notifyForNewTask($project_id=null, $task_id=null, $title=null, $body=null, $arr_data=null) {
    protected function notifyForNewTask($project_id=null, $task_id=null, $task_name='task name') {
		if(!MAppNotifyExpo::isEnableNotifyModule()) return 'Notify module is disable';
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('Project');
        $project_category = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'category')
        ));
        // Debug($project_category);
        // exit;
        if($project_category['Project']['category'] != 1) return; // #1481 Chi gui khi project la in progress (category = 1)
        $tokens = $this->getTokensFor('New_Task', $company_id, $project_id, $task_id);
        // Chuan bi noi dung de gui:
        $PjName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        $fullName = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
        $PjName = !empty($PjName) ? $PjName['Project']['project_name'] : '';
        $title = 'New: Task "'.$task_name.'"';
        $body = 'Project '.$PjName.' from '.$fullName;

        $title_fr = 'Nouveau: Tâche "'.$task_name.'"';
        $body_fr = 'Projet '.$PjName.' de '.$fullName;
        $arr_data['model'] = 'Task';
        $arr_data['project_id'] = $project_id;
        $arr_data['task_id'] = $task_id;

        // Gui Notify:
        $result_en = $this->ZNotifyExpo->send_notify_expo($tokens['en'], $title, $body, $arr_data);
        $result_fr = $this->ZNotifyExpo->send_notify_expo($tokens['fr'], $title_fr, $body_fr, $arr_data);

        $this->writeLog(json_encode($result_en), $this->employee_info, sprintf('Send Notify-New_Task-en'), $company_id);
        $this->writeLog(json_encode($result_fr), $this->employee_info, sprintf('Send Notify-New_Task-fr'), $company_id);
        
        $result['en'] = $result_en;
        $result['fr'] = $result_fr;

        return $result;
	}
    // protected function notifyForUpdateTask($project_id=null, $task_id=null, $title=null, $body=null, $arr_data=null) {
    protected function notifyForUpdateTask($project_id=null, $task_id=null, $task_name='task name') {
		if(!MAppNotifyExpo::isEnableNotifyModule()) return 'Notify module is disable';
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('Project');
        $project_category = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'category')
        ));
        // Debug($project_category);
        // exit;
        if($project_category['Project']['category'] != 1) return; // #1481 Chi gui khi project la in progress (category = 1)
        $tokens = $this->getTokensFor('Update_Task', $company_id, $project_id, $task_id);
        // Chuan bi noi dung de gui:
        $this->loadModel('Project');
        $PjName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        $fullName = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
        $PjName = !empty($PjName) ? $PjName['Project']['project_name'] : '';
        $title = 'Update: Task "'.$task_name.'"';
        $body = 'Project '.$PjName.' from '.$fullName;

        $title_fr = 'Modification: Tâche "'.$task_name.'"';
        $body_fr = 'Projet '.$PjName.' de '.$fullName;
        $arr_data['model'] = 'Task';
        $arr_data['project_id'] = $project_id;
        $arr_data['task_id'] = $task_id;
        // Gui Notify:
        $result_en = $this->ZNotifyExpo->send_notify_expo($tokens['en'], $title, $body, $arr_data);
        $result_fr = $this->ZNotifyExpo->send_notify_expo($tokens['fr'], $title_fr, $body_fr, $arr_data);

        $this->writeLog(json_encode($result_en), $this->employee_info, sprintf('Send Notify-Update_Task-en'), $company_id);
        $this->writeLog(json_encode($result_fr), $this->employee_info, sprintf('Send Notify-Update_Task-fr'), $company_id);
        
        $result['en'] = $result_en;
        $result['fr'] = $result_fr;

        return $result;
	}
    // public function notifyForDeleteTask($project_id=null, $task_id=null, $title=null, $body=null, $arr_data=null) {
    protected function notifyForDeleteTask($project_id=null, $task_id=null) {
		if(!MAppNotifyExpo::isEnableNotifyModule()) return 'Notify module is disable';
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('Project');
        $project_category = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'category')
        ));
        // Debug($project_category);
        // exit;
        if($project_category['Project']['category'] != 1) return; // #1481 Chi gui khi project la in progress (category = 1)
        $tokens = $this->getTokensFor('Delete_Task', $company_id, $project_id, $task_id);
        // Chuan bi noi dung de gui:
        $this->loadModel('Project');
        $PjName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        $fullName = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
        $PjName = !empty($PjName) ? $PjName['Project']['project_name'] : '';
        $title = 'Delete Task in Project:' . ' "' . $PjName . '" ';
        $body = $fullName . ' delete a Task on' . ' ' . date('Y-m-d h:i:sa');
        $arr_data['param'] = null;

        // Gui Notify:
        $result = $this->ZNotifyExpo->send_notify_expo($tokens, $title, $body, $arr_data);
        
        $this->writeLog(json_encode($result), $this->employee_info, sprintf('Send Notify-Delete_Task'), $company_id);
        return $result;
	}
	protected function _functionStop( $result = false, $data = array(), $message = '', $return = false, $redirect = array()){
		if( $return ){
			if( $message ) $this->Session->setFlash( $message, ($result ? 'success' : 'error'));
			return $result;
		}
		if( $this->params['isAjax']){
			die(json_encode(array(
				'success' => $result,
				'data' => $data,
				'message' => $message,
			)));
		}
		if( !empty( $redirect)){
			if( $message ) $this->Session->setFlash( $message, ($result ? 'success' : 'error'));
			$this->redirect($redirect);
			die();
		}
		return $result;
	}
	/**
     * Check item is belong to current company (login user)
     * conditions: table has column "company_id"
     * @return void
     * @access protected
     */
    protected function _isBelongToCompany($id, $model) {
        $this->loadModel($model);
		return $this->$model->find( 'count', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => @$this->employee_info['Company']['id'],
				'id' => $id
			)
		));
    }
	/**
     * Check item is belong to 1 company
	 * in case when SAS update data
     * conditions: table has column "company_id"
     * @return void
     * @access protected
     */
    protected function _isItemsSameCompany($ids, $model) {
        $this->loadModel($model);
		$companies = $this->$model->find( 'list', array(
			'recursive' => -1,
			'conditions' => array(
				'NOT' => array('company_id' => null),
				'id' => $ids
			),
			'fields' => array('company_id', 'id')
		));
		return count($companies) == 1;
    }
}