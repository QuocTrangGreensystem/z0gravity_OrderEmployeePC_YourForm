<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class EmployeesPreviewController extends AppController {

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Html');

     /**
     * Layout used by the Controller
     *
     * @var array
     * @access public
     */
    var $layout = 'default';

    /**
     * This controller does not use a model
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectFunction', 'ProfitCenter', 'ProjectTeam', 'Employee', 'Company', 'CompanyEmployeeReference', 'City', 'Country', 'ProjectFunctionEmployeeRefer', 'ProjectEmployeeProfitFunctionRefer', 'Profile', 'External');

    /**
     * Components
     *
     * @var array
     * @access public
     */
    var $components = array('ControllerList', 'MultiFileUpload', 'PImage', 'LogSystem');

    /**
     * Before executing controller actions
     *
     * @return void
     * @access public
     */
    public $is_pm = false;
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('recovery', 'token_sent', 'change_password');
        $this->Auth->autoRedirect = false;
        //get security options
        $this->loadModel('SecuritySetting');
        if( isset($this->employee_info ) ){
            $this->SecuritySetting->recursive = -1;
            $default = array(
                'SecuritySetting' => array(
                    'id' => 0,
                    'cookie' => 1,
                    'multi_click' => 0,
                    'complex_password' => 0,
                    'password_min_length' => 8,
                    'password_special_characters' => 0,
                    'password_ban_list' => 0
                )
            );
            if( $this->employee_info['Employee']['is_sas'] != '1' )
                $this->set('security', $this->SecuritySetting->find('first', array(
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id']
                    )
                )));
            else
                $this->set('security', $default);
            $this->is_pm = isset($this->employee_info['Role']['id']) && $this->employee_info['Role']['name'] =='pm' && $this->employee_info['CompanyEmployeeReference']['control_resource'] == '1';

            if(!empty($this->employee_info['Role']) && $this->employee_info['Role']['name'] =='pm' && !$this->is_pm && in_array($this->params['action'], array('edit', 'add')) )
                $this->redirect('/');
        }
		if( !empty($this->employee_info['Company']['id'])){
			$isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
			$LANG = Configure::read('Config.language');
			if($isProfileManager != 0 ){
				$enabled_menus = $this->ProfileProjectManagerDetail->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'display' => 1,
					),
					'fields' => array('widget_id',  'name_'.$LANG)
				));
			}else{
				$enabled_menus = $this->Menu->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'display' => 1,
						'model' => 'project',
					),
					'fields' => array('widget_id', 'name_'.$LANG)
				));
			}
			$this->set(compact('enabled_menus'));
		}
        $manage_multiple_resource = isset($this->companyConfigs['manage_multiple_resource']) && !empty($this->companyConfigs['manage_multiple_resource']) ?  true : false;
        $this->set(compact('manage_multiple_resource'));
        $this->set('is_pm', $this->is_pm);
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        $conditions = array();
        $isProfile = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
        $this->loadModel('ProfileProjectManager');
        $profileName = array();
        if(!empty($isProfile)){
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $isProfile
                )
            ));
        }
        if (    isset($this->employee_info['Role']['id'])
                && ($this->employee_info['Role']['name'] != "admin")
                && ($this->employee_info['Role']['name'] != "hr")
                && !$this->is_pm
                && !$isProfile ) {
            $this->redirect("/employees/my_profile");
        } else {
            if ($this->is_sas != 1) {
                $references = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'group' => 'employee_id',
                    'fields' => array( 'CompanyEmployeeReference.id', 'role_id', 'company_id', 'employee_id' ),
                    'recursive' => 1,
                    'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'],
                        'Company.parent_id' => $this->employee_info['Company']['id'])
                    )
                ));
                $references = Set::combine($references, '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
                $conditions['Employee.id'] = array_keys($references);
                $profit_centers = $this->ProjectTeam->ProfitCenter->find('list', array(
                    'conditions' => array('company_id' => $this->employee_info['Company']['id'])
                ));
                $contract_types = ClassRegistry::init('ContractType')->find('list', array(
                    'conditions' => array('company_id' => $this->employee_info['Company']['id'])
                ));
            } else {
                $profit_centers = $this->ProjectTeam->ProfitCenter->find('list');
                $contract_types = ClassRegistry::init('ContractType')->find('list');
                $references = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'group' => 'employee_id',
                    'fields' => array( 'CompanyEmployeeReference.id', 'role_id', 'company_id', 'employee_id' )
                ));
                $references = Set::combine($references, '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
                $conditions['Employee.is_sas'] = 0;
            }
        }
        $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
        $companies = $this->Employee->CompanyEmployeeReference->Company->find('list', array('fields' => array('Company.id', 'Company.company_name')));
        $reference_ids = $this->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id')));

        $this->Employee->Behaviors->attach('Containable');
        $employees = $this->Employee->find('all', array(
            'fields' => array('id', 'first_name', 'last_name', 'email', 'work_phone', 'mobile_phone', 'identifiant', 'code_id', 'id3', 'id4', 'id5', 'id6', 'contract_type_id'),
            'contain' => array(
                'City' => array('name'),
                'Country' => array('name')
            ),
            'conditions' => $conditions
        ));
        $employee_profits = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
            'fields' => array('employee_id', 'profit_center_id'),
            'conditions' => array(
                'employee_id' => Set::extract($employees, '{n}.Employee.id'),
                'AND' => array(
                    'NOT' => array('profit_center_id' => null),
                    'NOT' => array('profit_center_id' => 0)
                )
            ),
            'group' => array('employee_id')
        ));
        $this->loadModel('ProfileProjectManager');
        $_listNameProfile = $this->ProfileProjectManager->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'profile_name')
        ));
        $listNameProfile = array();
        foreach ($_listNameProfile as $key => $value) {
            $listNameProfile[$key] = $value;
            $roles ['profile_' . $key] = $value;
        }
        $employeeProfile = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('profile_account' => array_keys($listNameProfile)),
            'fields' => array('id', 'profile_account')
        ));
        $this->set(compact('employee_profits', 'employees', 'roles', 'companies', 'references', 'reference_ids', 'profit_centers', 'contract_types', 'profileName', 'employeeProfile'));
    }

    /**
     * view
     * @param int $id
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid employee', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('employee', $this->Employee->read(null, $id));
    }

    /**
     *     add
     *
     * @return void
     * @access public
     */
    function add() {
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        $profiles = $externals = array();
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ?  true : false;
        $tree = null;
        if ($this->employee_info['Employee']['is_sas'] == 1) {
            $tree = $this->Employee->CompanyEmployeeReference->Company->generateTreeList(null, null, null, '->');
            $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
            $profitCenters=array();
            $projectFunctions=array();
        } else {
            $role = $this->employee_info['Role']['id'];
            $company = $this->employee_info['Company']['id'];
			$canAddMoreMax = $this->Employee->canAddMoreMax($company);
			if(!$canAddMoreMax){
				$this->Session->setFlash(__('Please contact z0gravity team to add more users', true), 'error');
				$message = __('Please contact z0gravity team to add more users', true);
                $this->redirect(array('action' => 'index'));
			}
            // lay list ticket profile
            $this->loadModel('TicketProfile');
            $listTicketProfiles = $this->TicketProfile->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company,
                ),
                'fields' => array('id', 'name', 'description_eng', 'description_fre')
            ));
            $langCode = Configure::read('Config.language');
            if( $langCode == 'en' ){
                $listTicketProfiles = !empty($listTicketProfiles) ? Set::combine($listTicketProfiles, '{n}.TicketProfile.id', '{n}.TicketProfile.description_eng') : array();
            } else {
                $listTicketProfiles = !empty($listTicketProfiles) ? Set::combine($listTicketProfiles, '{n}.TicketProfile.id', '{n}.TicketProfile.description_fre') : array();
            }
            $this->set('listTicketProfiles', $listTicketProfiles);
            $this->Employee->recursive = 2;
            $tree = $this->Company->generateTreeList(array('OR' => array('Company.id' => $company, 'Company.parent_id' => $company)), null, null, '->');
            $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc'), 'conditions' => array()));
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company), null, null, '--');
            $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
                "conditions" => array('ProjectFunction.company_id' => $company)));
            $profiles = $this->Profile->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company),
                'fields' => array('id', 'name')
            ));
            $externals = $this->External->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company),
                'fields' => array('id', 'name')
            ));
        }
        if (!$this->enabledModule('rms')) {
            unset($roles[6]);
        }
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ?  true : false;
        $EPM_see_the_budget = isset($this->companyConfigs['EPM_see_the_budget']) && !empty($this->companyConfigs['EPM_see_the_budget']) ?  true : false;
		
		$default_user_profile = $this->Employee->default_user_profile($company);
		$this->set(compact('default_user_profile'));
        $this->set(compact('tree', 'roles', 'projectFunctions', 'profitCenters', 'adminSeeAllProjects', 'EPM_see_the_budget'));
        if (!empty($this->data)) {
			$message = '';
            $this->Employee->create();
            if (empty($this->data["Employee"]['password']) || ( isset( $this->data["Employee"] ['password']) && $this->data["Employee"]['password'] == md5("")) ) {
                $this->data["Employee"]['password'] = md5($default_user_profile["password"]);
            }
            if (empty($this->data["Employee"] ['confirm_password']) || ( isset( $this->data["Employee"] ['confirm_password']) && $this->data["Employee"] ['confirm_password'] == md5("")) ) {
                $this->data["Employee"]['confirm_password'] = $default_user_profile["password"];
            }
            $company_id = $this->employee_info['Company']['id'];
			$this->data["Employee"]['company_id'] = $company_id;
            if (($this->employee_info['Employee']['is_sas'] != 1)&&($this->employee_info['CompanyEmployeeReference']['role_id'] != 2)&&($this->data["Employee"]["role_id"]==2)) {
                $this->Session->setFlash(__('You do not have permission to add new Admin Company', true), 'error');
				$message = __('You do not have permission to add new Admin Company', true);
                $this->redirect(array('action' => 'index'));
            }
            $role_id = !empty($this->data["Employee"]["role_id"]) ? $this->data["Employee"]["role_id"] : 3;
            if( $role_id == 'ws' ){
                $this->data['Employee']['ws_account'] = 1;
                $role_id = 2;
            } else if( strpos($role_id, 'rofile') == 1){
                $_role_id = explode('_', $role_id);
                $this->data['Employee']['profile_account'] = $_role_id[1];
                $role_id = 3;
            }  else {
                $this->data['Employee']['ws_account'] = 0;
            }
            $actif = isset($this->data['Employee']['actif']) ? $this->data['Employee']['actif'] : 1;
            $endDate = $this->data['Employee']['end_date'];
            if($actif == 0 && empty($endDate)){
                $this->data['Employee']['end_date'] = date('d-m-Y', time());
            }
            if(!empty($this->data['Employee']['start_date'])){
                /**
                 * Lay ngay thanh lap cong ty
                 */
                $dayEstablished = $this->Company->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Company.id' => $company_id),
                    'fields' => array('day_established')
                ));
                if(!empty($dayEstablished) && !empty($dayEstablished['Company']['day_established'])){
                    $dayEstablished = $dayEstablished['Company']['day_established'];
                    $start = strtotime($this->data['Employee']['start_date']);
                    if($start < $dayEstablished){
                        $this->data['Employee']['start_date'] = date('d-m-Y', $dayEstablished);
                    }
                }
            }
			
            $this->data['Employee']['tjm'] = !empty($this->data['Employee']['tjm']) ? str_replace(',', '.', $this->data['Employee']['tjm']) : str_replace(',', '.', $default_user_profile['tjm']);
            $this->data['Employee']['capacity_by_year'] = !empty($this->data['Employee']['capacity_by_year']) ? $this->data['Employee']['capacity_by_year'] : $default_user_profile['capacity_by_year'];
            $this->data['Employee']['email_receive'] = isset($this->data['Employee']['email_receive']) ? $this->data['Employee']['email_receive'] : $default_user_profile['email_receive'];
            $this->data['Employee']['activate_copy'] = isset($this->data['Employee']['activate_copy']) ? $this->data['Employee']['activate_copy'] : $default_user_profile['activate_copy'];
            $this->data['Employee']['is_enable_popup'] = isset($this->data['Employee']['is_enable_popup']) ? $this->data['Employee']['is_enable_popup'] : $default_user_profile['is_enable_popup'];
            $this->data['Employee']['auto_timesheet'] = isset($this->data['Employee']['auto_timesheet']) ? $this->data['Employee']['auto_timesheet'] : $default_user_profile['auto_timesheet'];
            $this->data['Employee']['auto_absence'] = isset($this->data['Employee']['auto_absence']) ? $this->data['Employee']['auto_absence'] : $default_user_profile['auto_absence'];
            $this->data['Employee']['auto_by_himself'] = isset($this->data['Employee']['auto_by_himself']) ? $this->data['Employee']['auto_by_himself'] : $default_user_profile['auto_by_himself'];
			$this->data['control_resource'] = isset($this->data['control_resource']) ? $this->data['control_resource'] : intval($role_id ==3 && $default_user_profile['control_resource']);
			if( $role_id ==3){
				$this->data['Employee']['update_your_form'] = isset( $this->data['update_your_form'] ) ? $this->data['update_your_form'] : $default_user_profile['update_your_form'];
				$this->data['Employee']['create_a_project'] = isset($this->data['create_a_project'] ) ? $this->data['create_a_project'] : $default_user_profile['create_a_project'];
				$this->data['Employee']['delete_a_project'] = isset( $this->data['delete_a_project']) ?  $this->data['delete_a_project'] : $default_user_profile['delete_a_project'];				
				$this->data['Employee']['can_communication'] = isset( $this->data['can_communication']) ?  $this->data['can_communication'] : 0;				
				$this->data['Employee']['can_see_forecast'] = isset( $this->data['can_see_forecast']) ?  $this->data['can_see_forecast'] : 0;				
				$this->data['Employee']['change_status_project'] = isset( $this->data['change_status_project'] ) ? $this->data['change_status_project'] : $default_user_profile['change_status_project'];
			}
			
			// $this->data['see_all_projects'] = !empty($this->companyConfigs['see_all_projects']) ? 1 : 0; 
			$this->data['see_all_projects'] = isset($this->data['see_all_projects']) ? $this->data['see_all_projects'] : 0;
            // xu ly 2 bien budget nay
			$this->data['sl_budget'] = isset( $this->data['sl_budget']) ? $this->data['sl_budget'] : 0;
            if(isset($this->data['sl_budget'])) {
                if(($this->data['sl_budget']) == 0) {
                    $this->data['Employee']['update_budget'] = 0;
                    $this->data['see_budget'] = 0;
                } else if(($this->data['sl_budget']) == 1) {
                    $this->data['Employee']['update_budget'] = 0;
                    $this->data['see_budget'] = 1;
                } else {
                    $this->data['Employee']['update_budget'] = 1;
                    $this->data['see_budget'] = 1;
                }
            }
            
			//default language la fr.
            $this->data['Employee']['language'] = !empty($this->data['language']) ? $this->data['language'] : 'fr';
            $this->data['Employee']['hour'] = !empty($this->data['Employee']['capacity_in_hour']) ? $this->data['Employee']['capacity_in_hour'] : 0;
            $this->data['Employee']['minutes'] = !empty($this->data['Employee']['capacity_in_minute']) ? $this->data['Employee']['capacity_in_minute'] : 0;
            if(empty($this->data['Employee']['external'])){
                $this->data['Employee']['external_id'] = 0;
            }
			
			$saved = $this->Employee->save($this->data);
            if ($saved ) {
                $employee_id = $this->Employee->getLastInsertID();
				$saved['Employee']['id'] = $employee_id;
                $this->Employee->CompanyEmployeeReference->AddCompanyEmployeeRefer($company_id, $employee_id, $role_id, $this->data['control_resource'], $this->data['see_all_projects'], $this->data['see_budget']);
				$saved['employee_id'] = $employee_id;
				$saved['control_resource'] = $this->data['control_resource'];
				$saved['see_all_projects'] = $this->data['see_all_projects'];
				$saved['see_budget'] = $this->data['see_budget'];
                if (!empty($this->params['form']['EmployeeProjectFunctionId'])) {
                    $data_functions = $this->params['form']['EmployeeProjectFunctionId'];
                    $this->Employee->ProjectEmployeeProfitFunctionRefer->saveFunctionEmployee($this->data["Employee"]['profit_center_id'], $employee_id, $data_functions);
                } else {
                    $this->Employee->ProjectEmployeeProfitFunctionRefer->saveFunctionEmployee($this->data["Employee"]['profit_center_id'], $employee_id);
                }
                $this->writeLog($this->data, $this->employee_info, sprintf('Add new resource `%s %s`', $this->data["Employee"]["first_name"], $this->data["Employee"]["last_name"]), $company_id);
                $this->Session->setFlash(__('Saved', true), 'success');
				$message = __('Saved', true);
				$this->generateEmployeeAvatar($saved, true);
				if( $this->params['isAjax']) die(json_encode(array(
					'success' => !empty( $saved ) ? 'success' : 'failed',
					'message' => $message,
					'data' => $saved,
				)));
				if( !empty( $this->data['Employee']['return'] )){
					$this->redirect(  $this->data['Employee']['return']);
				}
                $this->redirect(array('action' => 'edit', $employee_id, $company_id));
            } else {
                $this->data["Employee"]["password"] = "";
                $this->data["Employee"]["confirm_password"] = "";
                $this->Session->setFlash(__('Not saved', true), 'error');
				if( $this->params['isAjax']) die(json_encode(array(
					'success' => !empty( $saved ) ? 'success' : 'failed',
					'message' => $message,
					'data' => $saved,
				)));
				if( !empty( $this->data['Employee']['return'] )){
					$this->redirect(  $this->data['Employee']['return']);
				}
            }
			
        }
        if ($this->employee_info['Employee']['is_sas'] == 1) {
            $cities = $this->Employee->City->find('list');
            $contract_types = ClassRegistry::init('ContractType')->find('list');
        } else {
            $contract_types = ClassRegistry::init('ContractType')->find('list', array(
                'conditions' => array('company_id' => $this->employee_info['Company']['id'])));
            $cities = $this->Employee->City->find('list', array('fields' => array('City.id', 'City.name'), 'conditions' => array('City.company_id' => $this->employee_info['Company']['id'])));
        }
        if ($this->employee_info['Employee']['is_sas'] == 1)
            $countries = $this->Employee->Country->find('list');
        else
            $countries = $this->Employee->Country->find('list', array('fields' => array('Country.id', 'Country.name'), 'conditions' => array('Country.company_id' => $this->employee_info['Company']['id'])));
        $company_id = !empty($company_id) ? $company_id : $this->employee_info['Company']['id'];
        $manage_hour = $this->Company->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'manage_hours'),
            'conditions' => array('Company.id' => $company_id)
        ));
        $manage_hour = !empty($manage_hour) && !empty($manage_hour['Company']['manage_hours']) ? $manage_hour['Company']['manage_hours'] : 0;
        $this->loadModels('CompanyConfigs', 'ProfileProjectManager');
        $fill_more_than_capacity_day = $this->CompanyConfigs->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'cf_value'),
            'conditions' => array('company' => $company_id, 'cf_name' => 'fill_more_than_capacity_day')
        ));
        $list_profile_name = $this->ProfileProjectManager->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info["Company"]["id"])
        ));
        $profile_name = !empty($list_profile_name) ? Set::combine($list_profile_name, '{n}.ProfileProjectManager.id', '{n}.ProfileProjectManager.profile_name') : array();
        $list_profile_name = !empty($list_profile_name) ? Set::combine($list_profile_name, '{n}.ProfileProjectManager.id', '{n}.ProfileProjectManager') : array();
        $fill_more_than_capacity_day = !empty($fill_more_than_capacity_day) ? $fill_more_than_capacity_day['CompanyConfigs']['cf_value'] : 0;
        $this->set(compact('cities', 'countries', 'contract_types', 'profiles', 'activateProfile', 'externals', 'manage_hour', 'fill_more_than_capacity_day', 'list_profile_name', 'profile_name'));
    }

    public function checkWorkload($id, $newDate){
        $originalData = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id)
        ));
        $this->loadModels('ActivityTask', 'ProjectTask');
        $oldStartDate = $originalData['Employee']['start_date'] && $originalData['Employee']['start_date'] != '0000-00-00' ? strtotime($originalData['Employee']['start_date']) : 0;
        $oldEndDate = $originalData['Employee']['end_date'] && $originalData['Employee']['end_date'] != '0000-00-00' ? strtotime($originalData['Employee']['end_date']) : 0;
        $newStartDate = isset($newDate['start_date']) && $newDate['start_date'] != '00-00-0000' ? strtotime($newDate['start_date']) : 0;    //int or false
        $newEndDate = isset($newDate['end_date']) && $newDate['end_date'] != '00-00-0000' ? strtotime($newDate['end_date']) : 0;    //int or false
        $conditions = $conditions2 = array(
            'Refer.reference_id' => $id,
            'is_profit_center' => 0
        );
        //right de check: neu user thay doi start/end date thi moi thuc hien query de check
        $right = true;
        if( $newStartDate && $newStartDate != $oldStartDate ){
            $conditions2['OR']['task_start_date <='] = date('Y-m-d', $newStartDate);
            $conditions['OR']['task_start_date <='] = $newStartDate;
            $right = false;
        }
        if( $right )return array();
        $tasks['Projects'] = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'fields' => array('P.project_name', 'P.id'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'project_task_employee_refers',
                    'alias' => 'Refer',
                    'type' => 'inner',
                    'conditions' => array('ProjectTask.id = Refer.project_task_id', 'Refer.estimated > 0')
                ),
                array(
                    'table' => 'projects',
                    'alias' => 'P',
                    'type' => 'inner',
                    'conditions' => array('P.id = ProjectTask.project_id')
                )
            ),
            'group' => array('P.project_name', 'P.id')
        ));
        $conditions2['project'] = array(null, 0);
        $tasks['Activities'] = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('P.name', 'P.id'),
            'conditions' => $conditions2,
            'joins' => array(
                array(
                    'table' => 'activity_task_employee_refers',
                    'alias' => 'Refer',
                    'type' => 'inner',
                    'conditions' => array('ActivityTask.id = Refer.activity_task_id', 'Refer.estimated > 0')
                ),
                array(
                    'table' => 'activities',
                    'alias' => 'P',
                    'type' => 'inner',
                    'conditions' => array('P.id = ActivityTask.activity_id')
                )
            ),
            'group' => array('P.name', 'P.id')
        ));
        return $tasks;
    }

    function checkConsume($id, $newDate){
        $originalData = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id)
        ));
        $this->loadModels('ActivityRequest', 'ActivityTask', 'ProjectTask');
        App::import('vendor', 'str_utility');
        $oldStartDate = $originalData['Employee']['start_date'] && $originalData['Employee']['start_date'] != '0000-00-00' ? strtotime($originalData['Employee']['start_date']) : 0;
        $oldEndDate = $originalData['Employee']['end_date'] && $originalData['Employee']['end_date'] != '0000-00-00' ? strtotime($originalData['Employee']['end_date']) : 0;
        $newStartDate = isset($newDate['start_date']) && $newDate['start_date'] != '0000-00-00' ? strtotime($newDate['start_date']) : 0;    //int or false
        $newEndDate = isset($newDate['end_date']) && $newDate['end_date'] != '0000-00-00' ? strtotime($newDate['end_date']) : 0;    //int or false

        $request = $this->ActivityRequest->find('first', array(
            'conditions' => array(
                'employee_id' => $id,
                'value !=' => 0
            ),
            'fields' => 'MAX(`date`) as maxDate, MIN(`date`) as minDate',
            'group' => array('employee_id')
        ));
        $minDate = !empty($request) ? $request[0]['minDate'] : 0;
        $maxDate = !empty($request) ? $request[0]['maxDate'] : 0;
        $valid = array(true, true);
        //check start_date
        if( $minDate && $newStartDate && $newStartDate != $oldStartDate && $newStartDate > $minDate ){
            $valid[0] = false;
        }
        //check end_date
        if( $maxDate && $newEndDate && $newEndDate != $oldEndDate && $newEndDate < $maxDate ){
            $valid[1] = false;
        }
        return $valid;
    }

    /**
     * edit
     * @param int $id, $refer_id
     * @return void
     * @access public
     */
    function getRecordsReferEmployee($id,$company_id) {
        $Model = $this->Employee;
        $Model->recursive = -1;
        $Model->contain(array('ProjectEmployeeProfitFunctionRefer'));
        $saved = $Model->find('first', array(
            'conditions' => array('Employee.id' => $id)
        ));
        $results = array(
            'profile' => $saved['Employee']['profile_id'],
            'profit_center' => $saved['ProjectEmployeeProfitFunctionRefer']['0']['profit_center_id']
        );
        return $results;
    }
    function edit($id = null, $company_id = null) {
        $this->loadModels('ProjectFunctionEmployeeRefer', 'EmployeeMultiResource');
        $employee_date = $this->Employee->find('first', array(
            'fields' => array('start_date', 'end_date', 'company_id', 'hour', 'minutes', 'anonymous'),
            'recursive' => -1,
            'conditions' => array('Employee.id' => $id)
        ));
        $start = (!empty($employee_date['Employee']['start_date']) && $employee_date['Employee']['start_date'] != '0000-00-00') ? strtotime($employee_date['Employee']['start_date']) : 0;
        $end = (!empty($employee_date['Employee']['end_date']) && $employee_date['Employee']['end_date'] != '0000-00-00') ? strtotime($employee_date['Employee']['end_date']) : 0;
        $companyId = !empty($employee_date['Employee']['company_id']) ? $employee_date['Employee']['company_id'] : 0;
        $capacity_hour = !empty($employee_date['Employee']['hour']) ? $employee_date['Employee']['hour'] : 0;
        $capacity_minutes = !empty($employee_date['Employee']['minutes']) ? $employee_date['Employee']['minutes'] : 0;
        $dates = array();
        //holiday settings
        $Holiday = ClassRegistry::init('Holiday');
        //$companyId = CakeSession::read('Auth.employee_info.Employee.company_id');
        $days = array_keys($Holiday->getOptions($start, $end, $companyId));
        // tinh muti resource.
        $employee_multi = $this->EmployeeMultiResource->find('all', array(
            'fields' => array('id', 'date', 'value', 'by_day'),
            'recursive' => -1,
            'conditions' => array('EmployeeMultiResource.employee_id' => $id)
        ));
        $employee_multi = Set::combine($employee_multi, '{n}.EmployeeMultiResource.date', '{n}.EmployeeMultiResource');
        $sumYearOfMutis = $sumMonthOfMutis = array();
        if(!empty($employee_multi)){
            foreach($employee_multi as $date => $value){
                if( !in_array($date, $days) && ($date <= $end) && ($date >= $start) ){
                    if(!isset($sumYearOfMutis[date('Y', $date)])){
                        $sumYearOfMutis[date('Y', $date)] = 0;
                    }
                    $sumYearOfMutis[date('Y', $date)] += $value['value'];
                    if(!isset($sumMonthOfMutis[date('m-Y', $date)])){
                        $sumMonthOfMutis[date('m-Y', $date)] = 0;
                    }
                    $sumMonthOfMutis[date('m-Y', $date)] += $value['value'];
                }
            }
        }
        //end.
        $count = 1;
        $month = date('m', $start);
        $first = $year = date('Y', $start);
        if($start > 0 && $end > 0){
            while ($start <= $end) {
                if((date('m', $start) > $month) || (date('Y', $start) > $year)){
                    $count ++;
                    $month = date('m', $start);
                    $year = date('Y', $start);
                }
                $dates[$year][$month][$start] = $start;
                $start = mktime(0, 0, 0, date('m', $start), date('d', $start)+1, date('Y', $start));
            }
        }
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        $profiles = $externals = array();
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ?  true : false;
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        }
        $company_id_of_employee = $this->CompanyEmployeeReference->find("first", array(
            'conditions' => array(
                'CompanyEmployeeReference.employee_id' => $id,
                'CompanyEmployeeReference.company_id' => $company_id
            )
        ));
        if (!$company_id_of_employee && !$this->is_sas) {
            $this->Session->setFlash(__('The employee was not found.', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('employee_id', $id);
        if ($company_id_of_employee) {
            $refer_id = $company_id_of_employee['CompanyEmployeeReference']['id'];
        } else {
            $refer_id = 0;
        }
        if ($this->is_sas != 1) {
            $contract_types = ClassRegistry::init('ContractType')->find('list', array(
                'conditions' => array('company_id' => $this->employee_info['Company']['id'])));
            $is_sas_of_this_employee = $this->Employee->find("list", array("fields" => array("Employee.is_sas"), "conditions" => array("Employee.id" => $id)));
            if (isset($is_sas_of_this_employee[$id]))
                $is_sas_of_this_employee = $is_sas_of_this_employee[$id];
            $this->set('readOnly', 0);
            if($this->employee_info['CompanyEmployeeReference']['role_id']==2 || $this->is_pm) {
                //read only mode is pm visiting an admin profile
                if( $this->is_pm && $company_id_of_employee['Role']['name'] == "admin" )
                    $this->set('readOnly', 1);
            } else {
                if ($this->employee_info['Employee']['id'] != $id) {
                    if (($is_sas_of_this_employee == 1) || (($this->employee_info['Employee']['id'] != $id) && ($company_id_of_employee['Role']['name'] == "admin"))) {
                        $this->Session->setFlash(__('You are not allowed editing this employee', true), 'error');
                        $this->redirect(array('controller' => 'employees', 'action' => 'index'));
                    }
                }
            }
            //END
            $company_id_of_employee = $this->CompanyEmployeeReference->find("first", array("fields" => array("company_id", "employee_id"), 'conditions' => array('employee_id' => $id)));
            $company_id_of_employee = $company_id_of_employee['CompanyEmployeeReference']['company_id'];

            $parent_id_of_company_id_of_employee = $this->CompanyEmployeeReference->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_employee)));
            if ($parent_id_of_company_id_of_employee['Company']['parent_id'] != "") {
                $parent_id_of_company_id_of_employee = $parent_id_of_company_id_of_employee['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_employee = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_employee)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_employee == "" || $company_id_of_admin != $parent_id_of_company_id_of_employee) {
                    $this->cakeError('error404', array(array('url' => $id . "/" . $refer_id)));
                }
            }
            $employee_info = $this->employee_info;
        } else {
            $this->set('readOnly', 0);
            $employee_info = $company_id_of_employee;
            $contract_types = ClassRegistry::init('ContractType')->find('list');
        }
        // security
        $tree = null;
        if ($this->employee_info['Employee']['is_sas'] == 1) {
            $tree = $this->Employee->CompanyEmployeeReference->Company->generateTreeList(null, null, null, '->');
            $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company_id_of_employee['CompanyEmployeeReference']['company_id']), null, null, '--');
            $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
                "conditions" => array('ProjectFunction.company_id' => $company_id_of_employee['CompanyEmployeeReference']['company_id'])));
            if ($refer_id == 0)
                $this->set("isclear_db", true);
        } else {
            $role = $this->employee_info['Role']['id'];
            $company = $this->employee_info['Company']['id'];
            if ($role == 2)
                $tree = $this->Company->generateTreeList(array('OR' => array('Company.id' => $company, 'Company.parent_id' => $company)), null, null, '->');
            else if( $this->is_pm ){
                $tree = $this->Company->generateTreeList(array('Company.id' => $company), null, null, '->');
            }
            $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company), null, null, '--');
            $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
                "conditions" => array('ProjectFunction.company_id' => $company)));
            $profiles = $this->Profile->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company),
                'fields' => array('id', 'name')
            ));
            $externals = $this->External->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company),
                'fields' => array('id', 'name')
            ));
        }
        if (!$this->enabledModule('rms')) {
            unset($roles[6]);
        }
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ?  true : false;
        $EPM_see_the_budget = isset($this->companyConfigs['EPM_see_the_budget']) && !empty($this->companyConfigs['EPM_see_the_budget']) ?  true : false;
        $this->set(compact('tree', 'roles', 'projectFunctions', 'profitCenters', 'adminSeeAllProjects', 'EPM_see_the_budget'));
        $ReferRecord = $this->Employee->CompanyEmployeeReference->find('first', array('conditions' => array('CompanyEmployeeReference.id' => $refer_id)));
        $company_id = $ReferRecord['CompanyEmployeeReference']['company_id'];
        $role_id = $ReferRecord['CompanyEmployeeReference']['role_id'];
        // lay list ticket profile
        $this->loadModel('TicketProfile');
        $listTicketProfiles = $this->TicketProfile->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
            ),
            'fields' => array('id', 'name', 'description_eng', 'description_fre')
        ));
        $langCode = Configure::read('Config.language');
        if( $langCode == 'eng' ){
            $listTicketProfiles = !empty($listTicketProfiles) ? Set::combine($listTicketProfiles, '{n}.TicketProfile.id', '{n}.TicketProfile.description_eng') : array();
        } else {
            $listTicketProfiles = !empty($listTicketProfiles) ? Set::combine($listTicketProfiles, '{n}.TicketProfile.id', '{n}.TicketProfile.description_fre') : array();
        }
        $manage_hour = $this->Company->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'manage_hours'),
            'conditions' => array('Company.id' => $company_id)
        ));
        $manage_hour = !empty($manage_hour) && !empty($manage_hour['Company']['manage_hours']) ? $manage_hour['Company']['manage_hours'] : 0;
        $this->loadModel('CompanyConfigs');
        $fill_more_than_capacity_day = $this->CompanyConfigs->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'cf_value'),
            'conditions' => array('company' => $company_id, 'cf_name' => 'fill_more_than_capacity_day')
        ));
        $see_all_projects =  $role_id == 3 && !empty($ReferRecord['CompanyEmployeeReference']['see_all_projects']) ? $ReferRecord['CompanyEmployeeReference']['see_all_projects'] : 0;
        $see_budget = $role_id == 3 && !empty($ReferRecord['CompanyEmployeeReference']['see_budget']) ? $ReferRecord['CompanyEmployeeReference']['see_budget'] : 0;
        $fill_more_than_capacity_day = !empty($fill_more_than_capacity_day) ? $fill_more_than_capacity_day['CompanyConfigs']['cf_value'] : 0;
        $control_resource = $role_id == 3 && !empty($ReferRecord['CompanyEmployeeReference']['control_resource']) ? $ReferRecord['CompanyEmployeeReference']['control_resource'] : 0;
        $this->set(compact('manage_hour', 'refer_id', 'role_id', 'company_id', 'listTicketProfiles', 'capacity_hour', 'capacity_minutes', 'fill_more_than_capacity_day', 'see_all_projects', 'see_budget', 'control_resource'));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid employee', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->data["Employee"]['password'] == md5("")) {
            unset($this->data["Employee"]['password']);
        }
        if (!empty($this->data)) {
			//Update ticket 896
			$this->loadModel('Employee');
			$hasAvatar = $this->Employee->find('first',array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $this->data['Employee']['id']
				),
				'fields' => array('id','avatar')
			));
			if(empty($hasAvatar['Employee']['avatar'])){
				$this->generateEmployeeAvatar($this->data, true);
			}
			//End ticket 896
            $company_id = !empty($this->data["Employee"]["company_id"]) ? $this->data["Employee"]["company_id"] : $company_id;
            $role_id = $this->data["Employee"]["role_id"];
            if( $role_id == 'ws' ){
                $this->data['Employee']['ws_account'] = 1;
                $role_id = 2;
            } else if( strpos($role_id, 'rofile') == 1){
                $_role_id = explode('_', $role_id);
                $this->data['Employee']['profile_account'] = $_role_id[1];
                $this->data['Employee']['ws_account'] = 0;
                $role_id = 3;
            } else {
                $this->data['Employee']['profile_account'] = 0;
                $this->data['Employee']['ws_account'] = 0;
            }
            $refer_id = $this->data["Employee"]["refer_id"];
            $actif = $this->data['Employee']['actif'];
            $endDate = $this->data['Employee']['end_date'];
            if($actif == 0 && empty($endDate)){
                $this->data['Employee']['end_date'] = date('d-m-Y', time());
            }
            if (($this->employee_info['Employee']['is_sas'] != 1)&&($this->employee_info['CompanyEmployeeReference']['role_id'] != 2)&&($this->data["Employee"]["role_id"]==2)) {
                $this->Session->setFlash(__('You do not have permission to add new Admin Company', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            //Add checks on staffing
            $checkResult = $this->checkConsume($id, $this->data['Employee']);
            if( !$checkResult[0] ){
                $this->Session->setFlash(__('Cannot modify start date because this resource already has request/consumed data before that day.', true), 'error');
                $this->redirect(array('action' => 'edit/' . $id . '/' . $company_id));
            }
            //check end_date
            if( !$checkResult[1] ){
                $this->Session->setFlash(__('Cannot modify end date because this resource already has request/consumed data after that day.', true), 'error');
                $this->redirect(array('action' => 'edit/' . $id . '/' . $company_id));
            }
            //check workload
            $workloads = $this->checkWorkload($id, $this->data['Employee']);
            //project that in used
            if( !empty($workloads['Projects']) || !empty($workloads['Activities']) ){
                $this->Session->setFlash(__('Cannot not modify start date. %s', true), 'error');
                $this->set('activities', $workloads['Activities']);
                $this->set('projects', $workloads['Projects']);
                goto __FORWARD__;
            }

            $this->data['Employee']['tjm'] = str_replace(',', '.', $this->data['Employee']['tjm']);
            $this->data['Employee']['capacity_by_year'] = isset($this->data['Employee']['capacity_by_year']) && !empty($this->data['Employee']['capacity_by_year']) ? $this->data['Employee']['capacity_by_year'] : '0.00';
            $this->Employee->id = $this->data['Employee']['id'];
            unset($this->data['Employee']['id']);
            //get data, check staffing of employee
            $oldRecordsReferEmployee = $this->getRecordsReferEmployee($id,$company_id);
            //end
            if(!empty($this->data['Employee']['start_date'])){
                /**
                 * Lay ngay thanh lap cong ty
                 */
                $dayEstablished = $this->Company->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Company.id' => $company_id),
                    'fields' => array('day_established')
                ));
                if(!empty($dayEstablished) && !empty($dayEstablished['Company']['day_established'])){
                    $dayEstablished = $dayEstablished['Company']['day_established'];
                    $start = strtotime($this->data['Employee']['start_date']);
                    if($start < $dayEstablished){
                        $this->data['Employee']['start_date'] = date('d-m-Y', $dayEstablished);
                    }
                }
            }
            if(isset($this->data['sl_budget'])) {
                if(($this->data['sl_budget']) == 0){
                    $this->data['Employee']['update_budget'] = 0;
                    $this->data['see_budget'] = 0;
                } elseif(($this->data['sl_budget']) == 1) {
                    $this->data['Employee']['update_budget'] = 0;
                    $this->data['see_budget'] = 1;
                } else {
                    $this->data['Employee']['update_budget'] = 1;
                    $this->data['see_budget'] = 1;
                }
            }
            $this->data['Employee']['update_your_form'] = $this->data['update_your_form'];
            $this->data['Employee']['create_a_project'] = $this->data['create_a_project'];
            $this->data['Employee']['delete_a_project'] = $this->data['delete_a_project'];
            $this->data['Employee']['change_status_project'] = $this->data['change_status_project'];
            $this->data['Employee']['can_communication'] = !empty($this->data['can_communication']) ? $this->data['can_communication'] : 0;
            $this->data['Employee']['can_see_forecast'] = !empty($this->data['can_see_forecast']) ? $this->data['can_see_forecast'] : 0;
            if($this->data['Employee']['external'] != 1){
                $this->data['Employee']['external_id'] = 0;
            }
            $this->data['Employee']['hour'] = !empty($this->data['Employee']['capacity_in_hour']) ? $this->data['Employee']['capacity_in_hour'] : 0;
            $this->data['Employee']['minutes'] = !empty($this->data['Employee']['capacity_in_minute']) ? $this->data['Employee']['capacity_in_minute'] : 0;
			$this->data['Employee']['update_by'] = $this->employee_info['Employee']['id'];
            if ($this->Employee->save($this->data)) {
                $this->writeLog($this->data, $this->employee_info, sprintf('Modify resource `%s %s` (change password: %s)', $this->data["Employee"]["first_name"], $this->data["Employee"]["last_name"],
                    isset($this->data["Employee"]['password']) ? 'no' : 'yes'), $company_id);
                $this->data['Employee']['id'] = $this->Employee->id;
                if (!$this->Employee->field('is_sas', array('id' => $id))) {
                    $this->Employee->CompanyEmployeeReference->saveCompanyEmployeeRefer($refer_id, $company_id, $id, $role_id, $this->data['control_resource'], $this->data['see_all_projects'], $this->data['see_budget']);
                    if (!empty($this->params['form']['EmployeeProjectFunctionId'])) {
                        $data_functions = $this->params['form']['EmployeeProjectFunctionId'];
                        $this->Employee->ProjectEmployeeProfitFunctionRefer->editFunctionEmployee($this->data["Employee"]['profit_center_id'], $id, $data_functions);
                    } else {
                        $this->Employee->ProjectEmployeeProfitFunctionRefer->editFunctionEmployee($this->data["Employee"]['profit_center_id'], $id);
                    }
                } else {
                    $Model = $this->Employee->ProjectEmployeeProfitFunctionRefer;
                    $saved = $Model->find('list', array('fields' => array('id', 'id'), 'recursive' => -1, 'conditions' => array('employee_id' => $id)));
                    foreach ($saved as $save) {
                        $Model->delete($save);
                    }
                }
                $this->ProjectFunctionEmployeeRefer->updateAll(
                    array('ProjectFunctionEmployeeRefer.profit_center_id' => $this->data['Employee']['profit_center_id']),
                    array('ProjectFunctionEmployeeRefer.employee_id' => $this->data['Employee']['id'])
                );
                $this->Session->setFlash(__('Saved', true), 'success');
                $newRecordsReferEmployee = array(
                    'profile' => isset($this->data["Employee"]['profile_id']) ? $this->data["Employee"]['profile_id'] : null,
                    'profit_center' => isset($this->data["Employee"]['profit_center_id']) ? $this->data["Employee"]['profit_center_id'] : null
                );
                if( $id == $this->employee_info['Employee']['id'] )$this->rebuildSessionWhenUpdatedEmployeeInfo($id);
                //CHECK DATA, RESET STAFFING
                if(array_diff_assoc($oldRecordsReferEmployee,$newRecordsReferEmployee)) {
                    //if change profile, profit center : format staffing
                    $this->loadModel('TmpStaffingSystem');
                    $data = $this->TmpStaffingSystem->checkStaffingOfEmployee($id,$company_id,true);
                    if($data['count'] < 150) {
                        //if record < 200, run script auto format staffing
                        $this->autoFormatStaffing($data,$id,$company_id);
                    } else {
                        //if record > 200, don't run script auto format staffing, display notification/send email for sas let admin do format staffing
                        //$this->_sendEmail('vinguyen.fitsgu@gmail.com', __('[Azuree] Need format staffing system.', true), 'staffing_systems');
                        $this->set('Employee',$this->data["Employee"]["first_name"].' '.$this->data["Employee"]["last_name"]);
                        $listEmail = array();
                        $listEmail = array_values($this->getFirstAdminAndSasOfCompany($company_id));
                        $this->_sendEmail($listEmail, __('[Azuree] Need format staffing system.', true), 'staffing_systems');
                        $this->redirect(array('action' => 'edit', $this->data['Employee']['id'], $company_id));
                    }
                } else {
                    $this->redirect(array('action' => 'edit', $this->data['Employee']['id'], $company_id));
                }
                //END
            } else {
                // $this->Session->setFlash(sprintf(__('The employee %s %s could not be saved. Please, try again.', true), '<b>' . $this->data["Employee"]['first_name'] . ' ', $this->data["Employee"]['last_name'] . '</b>', true), 'error');
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
                $this->redirect(array('action' => 'edit/' . $id . '/' . $company_id));
            }
        }
        if (empty($this->data)) {
            __FORWARD__:
            $this->data = $this->Employee->read(null, $id);
            $oldProfitCenter = !empty($this->data['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $this->data['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : 0;

            $this->set(compact('oldProfitCenter'));
        }
        $this->loadModels('ProfileProjectManager');
        $list_profile_name = $this->ProfileProjectManager->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info["Company"]["id"])
        ));
        $profile_name = !empty($list_profile_name) ? Set::combine($list_profile_name, '{n}.ProfileProjectManager.id', '{n}.ProfileProjectManager.profile_name') : array();
        $list_profile_name = !empty($list_profile_name) ? Set::combine($list_profile_name, '{n}.ProfileProjectManager.id', '{n}.ProfileProjectManager') : array();
        $cities = $this->Employee->City->find('list', array('fields' => array('City.id', 'City.name'), 'conditions' => array('City.company_id' => $company_id)));
        $countries = $this->Employee->Country->find('list', array('fields' => array('Country.id', 'Country.name'), 'conditions' => array('Country.company_id' => $company_id)));
        $employeeLogin = $this->employee_info;
		if ($this->is_sas != 1) {
			$references = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'group' => 'employee_id',
                    'fields' => array( 'CompanyEmployeeReference.id', 'role_id', 'company_id', 'employee_id' ),
                    'recursive' => 1,
                    'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'],
                        'Company.parent_id' => $this->employee_info['Company']['id'])
                    )
                ));
                $references = Set::combine($references, '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
                $conditions['Employee.id'] = array_keys($references);
		}else{
			$conditions['Employee.is_sas'] = 0;
		}
		$employees = array();
		if(!empty($references)){
			$employees = $this->Employee->find('all', array(
				'recursive' => -1,
				'fields' => array('id', 'CONCAT(first_name, " ", last_name) AS full_name'),
				'conditions' => $conditions
			));
			$employees = !empty($employees) ?  Set::combine($employees, '{n}.Employee.id', '{n}.0.full_name') : array();
		}
		
        $this->set(compact('sumYearOfMutis', 'sumMonthOfMutis', 'cities', 'countries', 'employee_info', 'id', 'company_id', 'contract_types', 'employeeLogin', 'profiles', 'activateProfile', 'employee_multi', 'dates', 'count', 'employee_date', 'first', 'days', 'externals', 'profile_name', 'list_profile_name', 'employees'));
    }
	
	function reset2fa( $employee_id = null){
		$result = false;
		$message = __('Permission denied', true);
		$data = array();
		$canManagerResource = ($this->employee_info['Role']['name'] == 'admin') || (($this->employee_info['Role']['name'] == 'pm') && $this->employee_info['CompanyEmployeeReference']['control_resource'] != 0);
		if( !empty($employee_id) && $canManagerResource){
			$this->loadModel('TwoFactorAuthen');
			//Thuc te chi can find first nhung o day viet find all cho truong hop du thua data
			$employee = $this->Employee->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'Employee.id' => $employee_id,
					'Employee.company_id' => $this->employee_info['Company']['id']
				),
				'joins' => array(
					array(
						'table' => 'two_factor_authens',
						'alias' => 'TwoFactorAuthen',
						'type' => 'left',
						'conditions' => array('Employee.id = TwoFactorAuthen.employee_id')
					)
				),
				'fields' => array(
					'Employee.id', 'TwoFactorAuthen.id'
				)
			));
			if( !empty( $employee)){
				$data = Set::classicExtract($employee, '{n}.TwoFactorAuthen.id');
				$result = $this->TwoFactorAuthen->deleteAll(array(
					'id' => $data
				), false);
				$message = $result ? __('Deleted', true) : __("Submit failed, please correct data before submit.", true);
			}
		}
		die(json_encode(array(
			'result' => !empty( $result ) ? 'success' : 'failed',
			'message' => $message,
			'data' => $data,
		)));
	}
    /*------------------------------
    @author : ViN
    @date : 23/06/2015
    @content : auto format staffing
    ------------------------------*/
    function getFirstAdminAndSasOfCompany($company_id)
    {
        $records = $this->Employee->CompanyEmployeeReference->find('list', array(
            'fields' => array(
                'role_id','employee_id'
            ),
            'recursive' => 1,
            'order' => 'employee_id DESC',
            'conditions' => array(
                'role_id' => 2,
                'actif' => 1,
                'OR' => array(
                    'Company.id' => $company_id,
                    'Company.parent_id' => $company_id
                )
            )
        ));
        $admin = $this->Employee->CompanyEmployeeReference->find('list', array(
            'fields' => array(
                'Employee.id','Employee.email'
            ),
            'recursive' => 1,
            'conditions' => array(
                'Employee.id' => $records,
            )
        ));

        $sas = $this->Employee->find('list', array(
            //'group' => 'employee_id',
            'fields' => array(
                'Employee.id','Employee.email'
            ),
            'limit' => 1,
            'order' => 'id DESC',
            'recursive' => -1,
            'conditions' => array(
                'Employee.is_sas' => 1,
            )
        ));
        $results = array(
            'sas' => !empty($sas) ? array_values($sas) : array('support@azuree-app.com'),
            'admin' => !empty($admin) ? array_values($admin) : array('support@azuree-app.com')
        );
        $results['sas'] = $results['sas'][0];
        $results['admin'] = $results['admin'][0];
        return $results;
    }
    /*------------------------------
    @author : ViN
    @date : 19/06/2015
    @content : auto format staffing
    ------------------------------*/
    function autoFormatStaffing($data,$id,$company_id) {
        $redirect = '/employees/edit/' . $id . '/' . $company_id;
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '.$redirect);
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        unset($data['count']);
        foreach($data as $keyword => $_data) {
            $keyword = $keyword.'Task';
            if(!empty($_data)) {
                foreach($_data as $id) {
                    $this->$keyword->staffingSystem($id,false);
                }
            }
        }
    }
    /**
     * my_profile
     *
     * @return void
     * @access public
     */
    function my_profile_bak() {
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        if (!$this->is_sas) {
            $this->set('company_id', $this->employee_info['Company']['id']);

        }
        if ($this->employee_info['Employee']['is_sas'] == 1) {

            $this->set('companyName', 'Pilot');
            $this->set('role_id', 'SAS');
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company_id_of_employee['CompanyEmployeeReference']['company_id']), null, null, '--');

        } else {
            $role = $this->employee_info['Role']['id'];
            $company = $this->employee_info['Company']['id'];
            $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
            $this->set('roles', $roles);
            $Companies = $this->Employee->CompanyEmployeeReference->Company->find('all', array('conditions' => array('Company.id' => $this->employee_info['Company']['id'])));
            $this->set('companyName', $Companies[0]['Company']['company_name']);
            $Roles = $this->Employee->CompanyEmployeeReference->Role->find('all', array('conditions' => array('Role.id' => $this->employee_info['Role']['id'])));
            $this->set('role_id', $Roles[0]['Role']['desc']);
            $this->set('roleId', $Roles[0]['Role']['id']);
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company), null, null, '--');
        }
        $id = $this->Session->read("Auth.Employee.id");
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid employee', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        // if ($this->data["Employee"]['password'] == md5("")) {
        //     $this->data["Employee"]['password'] = $this->data["Employee"]['password_old'];
        // }
        if (!empty($this->data)) {
            $this->data['Employee']['is_sas'] = !empty($this->employee_info['Employee']['is_sas']) ? $this->employee_info['Employee']['is_sas'] : 0;
            /*
            * security - 2015-03-11
            * remove sas, id
            */
            //if( isset($this->data['Employee']['is_sas']) )unset($this->data['Employee']['is_sas']);
            if( isset($this->data['Employee']['id']) )unset($this->data['Employee']['id']);
            $this->Employee->id = $this->employee_info['Employee']['id'];
            if ($this->Employee->save($this->data)) {
                //SAVE SESSION WHEN UPDATE
                $this->rebuildSessionWhenUpdatedEmployeeInfo($id);
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'my_profile'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
                $data = $this->Employee->CompanyEmployeeReference->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'employee_id' => $id
                    )
                ));
                $this->data['CompanyEmployeeReference'][0] = $data['CompanyEmployeeReference'];
            }
        }
        //END
        // $oldProfitCenter = !empty($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : 0;
        // $this->loadModel('ProjectFunctionEmployeeRefer');
        // $profit_center = $this->ProjectFunctionEmployeeRefer->find('first', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'employee_id' => $this->employee_info['Employee']['id'],
        //     ),
        //     'field' => array('profit_center_id')
        // ));
        // $oldProfitCenter = !empty($profit_center) ? $profit_center['ProjectFunctionEmployeeRefer']['profit_center_id'] : 0;

        $thisProfitCenter = $this->Employee->read(null, $id);
        $oldProfitCenter = !empty($thisProfitCenter['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $thisProfitCenter['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : 0;

        if (empty($this->data)) {
            $this->data = $this->Employee->read(null, $id);
        }
        $cities = $this->Employee->City->find('list', array(
            'recursive' => -1,
            'conditions' => array('City.company_id' => $this->employee_info['Company']['id'])
        ));
        $countries = $this->Employee->Country->find('list', array(
            'recursive' => -1,
            'conditions' => array('Country.company_id' => $this->employee_info['Company']['id'])
        ));
        if(!empty($this->employee_info['Employee']['profile_account'])){
            $this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $this->employee_info['Employee']['profile_account'])
            ));
            $profileName = !empty($profileName) ? $profileName['ProfileProjectManager']['profile_name'] : '';
            $this->set(compact('profileName'));
        }
        $this->set(compact('cities', 'countries', 'oldProfitCenter', 'profitCenters'));
    }
    /**
     * my_profile
     *
     * @return void
     * @access public
     */
    function my_profile() {
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        if (!$this->is_sas) {
            $this->set('company_id', $this->employee_info['Company']['id']);

        }
        if ($this->employee_info['Employee']['is_sas'] == 1) {
            $this->set('companyName', 'Pilot');
            $this->set('role_id', 'SAS');
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company_id_of_employee['CompanyEmployeeReference']['company_id']), null, null, '--');
        } else {
            $role = $this->employee_info['Role']['id'];
            $company = $this->employee_info['Company']['id'];
            $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
            $this->set('roles', $roles);
            $Companies = $this->Employee->CompanyEmployeeReference->Company->find('all', array('conditions' => array('Company.id' => $this->employee_info['Company']['id'])));
            $this->set('companyName', $Companies[0]['Company']['company_name']);
            $Roles = $this->Employee->CompanyEmployeeReference->Role->find('all', array('conditions' => array('Role.id' => $this->employee_info['Role']['id'])));
            $this->set('role_id', $Roles[0]['Role']['desc']);
            $this->set('roleId', $Roles[0]['Role']['id']);
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company), null, null, '--');
        }
        $id = $this->Session->read("Auth.Employee.id");
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid employee', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data['Employee'])) {
			$allow_fields = array(
				'avatar_color',
				'password',
				'confirm_password',
				'email_receive',
				'activate_copy',
				'is_enable_popup',
				'auto_absence',			
				'auto_timesheet'			
			);
			$update_data = array();
			foreach ( $allow_fields as $v){
				if( isset($this->data['Employee'][$v]) )
					$update_data[$v] = $this->data['Employee'][$v];
			}
			$company_id = $this->employee_info['Company']['id'];
			if( empty($update_data['password'] ) || ($update_data['password'] == md5('')) ){
				unset($update_data['password']);
			}
			if( !empty($update_data['avatar_color'])){
				$update_data['avatar'] = '';
				$update_data['avatar_resize'] = '';
				// Delete old avatar
				$path = $this->_getPath($company_id, $id);
				App::import('Core', 'Folder');
				new Folder($path, true, 0777);
				$oldImages = $this->employee_info['Employee']['avatar'];
				$oldImageResize = $this->employee_info['Employee']['avatar_resize'];
				if(!empty($oldImages) && file_exists($path . $oldImages) ){
					unlink($path . $oldImages);
					
					$info = pathinfo($oldImages);
					$oldImages = explode('_resize_bk_', $oldImages);
					$oldImages = $oldImages[0] . '.' . $info['extension'];
					if(!empty($oldImages) && is_file($path . $oldImages)){
						unlink($path . $oldImages);
					}
				}
				if(!empty($oldImageResize) && file_exists($path . $oldImageResize) ){
					unlink($path . $oldImageResize);
				}
			}
            $this->Employee->id = $id;
			$this->data = array(
				'Employee' => $update_data
			);
			/* END Edit by Huynh */
            if ($this->Employee->save($this->data)) {
                //SAVE SESSION WHEN UPDATE				
                //$this->rebuildSessionWhenUpdatedEmployeeInfo($id);
				$employee_all_info = $this->Session->read("Auth.employee_info");
				$employee_all_info['Employee'] = array_merge($employee_all_info['Employee'], $this->data['Employee']) ;
				unset($employee_all_info['Employee']['password']);
				$employee_all_info['Employee']['is_sas'] = $this->employee_info['Employee']['is_sas'];
				$this->Session->write('Auth.employee_info', $employee_all_info);
				$this->Session->setFlash(__('Saved', true), 'success');
				$this->generateEmployeeAvatar($employee_all_info, true);
				
                $this->redirect(array('action' => 'my_profile'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
                $data = $this->Employee->CompanyEmployeeReference->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'employee_id' => $id
                    )
                ));
                $this->data['CompanyEmployeeReference'][0] = $data['CompanyEmployeeReference'];
            }
        }
        //END

        $thisProfitCenter = $this->Employee->read(null, $id);
        $oldProfitCenter = !empty($thisProfitCenter['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $thisProfitCenter['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : 0;

        if (empty($this->data)) {
            $this->data = $this->Employee->read(null, $id);
        }
        $cities = $this->Employee->City->find('list', array(
            'recursive' => -1,
            'conditions' => array('City.company_id' => $this->employee_info['Company']['id'])
        ));
        $countries = $this->Employee->Country->find('list', array(
            'recursive' => -1,
            'conditions' => array('Country.company_id' => $this->employee_info['Company']['id'])
        ));
        if(!empty($this->employee_info['Employee']['profile_account'])){
            $this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $this->employee_info['Employee']['profile_account'])
            ));
            $profileName = !empty($profileName) ? $profileName['ProfileProjectManager']['profile_name'] : '';
            $this->set(compact('profileName'));
        }
        $this->set(compact('cities', 'countries', 'oldProfitCenter', 'profitCenters'));
    }
    function rebuildSessionWhenUpdatedEmployeeInfo($id) {
        $employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $id)));
        if (empty($employee_all_info) || count($employee_all_info) > 1 || $this->Auth->user('is_sas') == 1) {
            $this->redirect("/employees/my_profile");
        }
        $employee_all_info = $employee_all_info[0];
        $employee_all_info["Employee"]["is_sas"] = 0;
        $this->Session->write('Auth.employee_info', $employee_all_info);
        $this->ApiKey->generate($this->Session);
		$employee_info = $this->Auth->user();
        return;
    }
    
    /**
     * login
     *
     * @return void
     * @access public
     */
    function login() {
        $this->layout = "login";
        if ($this->Auth->user()) {
            $employee_all_info = $this->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.email" => $this->Auth->user('email'))));
            if (empty($employee_all_info) || count($employee_all_info) > 1 || $this->Auth->user('is_sas') == 1) {
                $this->redirect("/employees/get_company_role");
            }
            if( $this->_is('POST') )$this->writeLog($this->data, $employee_all_info[0], 'Logon to system');
            $employee_all_info = $employee_all_info[0];
            $employee_all_info["Employee"]["is_sas"] = 0;
            //find pc
            $myPc = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
                'conditions' => array('employee_id' => $this->Auth->user('id'))
            ));
            $employee_all_info['Employee']['profit_center_id'] = $myPc['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
            $this->Session->write('Auth.employee_info', $employee_all_info);
            //set api key to use in global/local view
            $this->ApiKey->generate($this->Session);
            $this->loadModel('HistoryFilter');
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'rollback_url_employee_when_login',
                    'employee_id' => $employee_all_info['Employee']['id']
                )
            ));
            $url = '/';
            if(!empty($check)){
                $url = $check['HistoryFilter']['params'];
            } elseif (!empty($this->params['url']['continue'])) {
                $url = $this->params['url']['continue'];
            }
            $this->redirect($url);
        }
    }

    /**
     * login
     *
     * @return void
     * @access public
     */
    function recovery() {
        $this->layout = "login";
        $maxResend = 2;
        $lifeTime = 24 * 60 * 60;
        if (empty($this->data)) {
            $this->Session->setFlash(__('Please enter your email was register to get new password!', true), 'warning');
        } else {
            if (empty($this->data['Employee']['email']) || !($employee = $this->Employee->find('first', array(
                'recursive' => -1, 'conditions' => array('email' => $this->data['Employee']['email'])
                    )))) {
                $this->Session->setFlash(__('Sorry but that email address is not in our records, please try again.', true), 'error');
            } else {
                $employee = array_shift($employee);
                $this->loadModel('RecoveryEmployee');
                $this->RecoveryEmployee->deleteAll(array('updated <' => time() - $lifeTime));
                $last = $this->RecoveryEmployee->find('first', array('fields' => array('id', 'resend'), 'conditions' => array('name' => $employee['email'])));
                if ($last && $last['RecoveryEmployee']['resend'] >= $maxResend) {
                    // $this->Session->setFlash(sprintf(__('Sorry but due to security reasons you are limited to the number of temporary codes received each day. Please try again in %s hours.', true), $lifeTime / 60 / 60), 'error');
                    $this->Session->setFlash(__('Cannot be sent again for safety reason, contact your administrator.', true), 'error');
                } else {
                    if ($last) {
                        $this->RecoveryEmployee->id = $last['RecoveryEmployee']['id'];
                    } else {
                        $last['RecoveryEmployee']['resend'] = 0;
                        $this->RecoveryEmployee->create();
                    }
                    $data = array(
                        'name' => $employee['email'],
                        'token' => strtoupper(substr($this->Auth->password(strtolower($employee['email']) . '-' . String::uuid()), 0, 32)),
                        'resend' => $last['RecoveryEmployee']['resend'] + 1
                    );
                    $this->set(compact('employee', 'data', 'lifeTime'));
                    if ($this->RecoveryEmployee->save($data) ) {
                        $this->_sendEmail($employee['email'], __('[Z0 Gravity] Recovery Password', true), 'recovery');
                        $this->Session->write('Recovery.' . md5($employee['email']), $employee);
                        $this->redirect('/password-token-sent?email=' . urlencode($employee['email']));
                    } else {
                        $this->Session->setFlash(__('Can not recovery your password, please try again.', true), 'error');
                    }
                }
            }
        }
    }

    function token_sent() {
        $lifeTime = 24 * 60 * 60;
        $this->layout = "login";
        if (empty($this->params['url']['email']) || !($employee = $this->Session->read('Recovery.' . md5($this->params['url']['email'])))) {
            $this->Session->setFlash(__('Invalid token, please try again.', true), 'error');
            $this->redirect('/login');
        }
        $this->set(compact('lifeTime', 'employee'));
    }

    function change_password() {
        $lifeTime = 24 * 60 * 60;
        $this->layout = "login";
        $this->loadModel('RecoveryEmployee');
        $this->RecoveryEmployee->deleteAll(array('updated <' => time() - $lifeTime));
        if ( !empty($this->params['url']['token']) && ($last = $this->RecoveryEmployee->find('first', array( 'fields' => array('id', 'name'), 'conditions' => array('token' => $this->params['url']['token']))))
            && ($employee = $this->Employee->find('first', array('conditions' => array('email' => $last['RecoveryEmployee']['name'])))) ) {
            //get security settings
            $this->loadModel('SecuritySetting');
            $security = $this->SecuritySetting->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $employee['CompanyEmployeeReference'][0]['company_id']
                )
            ));
            $this->set(compact('employee', 'security'));
            if ( !empty($this->data['Employee']['password']) && strlen($this->data['Employee']['password']) >= 4 ) {
                $this->Employee->id = $employee['Employee']['id'];
                if ($this->Employee->saveField('password', $this->Employee->hashPasswords($this->data['Employee']['password']))) {
                    $this->Session->setFlash(__('The password has been changed.', true), 'success', array(), 'resource');
                    $this->RecoveryEmployee->delete($last['RecoveryEmployee']['id']);
                    $this->redirect('/login');
                } else {
                    $this->Session->setFlash(__('The password could not be changed.', true), 'error', array(), 'resource');
                }
            } else {
                $this->Session->setFlash(__('Change your password.', true), 'warning', array(), 'resource');
            }
        } else {
            $this->Session->setFlash(__('Invalid token, please try again.', true), 'error', array(), 'resource');
            $this->redirect('/login');
        }
    }

    /**
     * get_company_role
     *
     * @return void
     * @access public
     */
    function get_company_role() {
        $this->layout = "login";
        $employee_info = $this->Auth->user();
        if (empty($employee_info)) {
            $this->redirect("/login");
        }
        $employee_all_info = $this->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.email" => $employee_info["Employee"]["email"])));
        $companies = array();
        if($employee_info["Employee"]["is_sas"]==1) {
            $this->loadModel('Company');
            $companies = $this->Company->generateTreeList(null, null, null, '--');
            $this->set('companies',$companies);
        }
        $this->set("employee_info", $employee_info);
        $this->set("employee_all_info", $employee_all_info);

        if (!empty($this->data)) {
            if (isset($this->data["Employee"]["is_sas"]) && $this->data["Employee"]["is_sas"] == 1 && empty($this->data["Employee"]["company_role"])) {
                $this->Session->write('Auth.employee_info', $employee_info);
                $this->ApiKey->generate($this->Session);
                $this->writeLog($this->data, $employee_info, 'Logon as SAS');
                $this->redirect("/index");
            } elseif(!isset($this->data["Employee"]["is_sas"])) {
                if (!empty($this->data["Employee"]["company_role"])) {
                    if( $employee_info["Employee"]["is_sas"] == 1) {
                        $company = $this->Company->find('first',array(
                            'recursive' => -1,
                            'conditions' => array(
                                'Company.id' => $this->data["Employee"]["company_role"]
                            )
                        ));
                        $admin_of_company = $this->Employee->CompanyEmployeeReference->find("first", array(
                            "recursive" => -1,
                            "conditions" => array(
                                "CompanyEmployeeReference.company_id" => $company['Company']['id'],
                                "CompanyEmployeeReference.role_id" => 2
                            ),
                            'fields' => 'employee_id'
                        ));
                        $info_sas_login_to_company = array(
                            'CompanyEmployeeReference' => array(
                                'id' => 0,
                                'company_id' => $company['Company']['id'],
                                'employee_id' => $admin_of_company['CompanyEmployeeReference']['employee_id'] ? $admin_of_company['CompanyEmployeeReference']['employee_id'] : $employee_info['Employee']['id'],
                                'role_id' => 2,
                                'control_resource' => 0,
                                'see_all_projects' => 0,
                                'see_budget' => 0,
                                'created' => '',
                                'updated' => ''
                            ),
                            'Role' => array(
                                'id' => 2,
                                'name' => 'admin',
                                'desc' => 'Admin Company',
                                'created' => '',
                                'updated' => ''
                            )
                        );
                        $employee_info['Employee']["is_sas"] = 0;
                        $employee_info['Employee']['id'] = $admin_of_company['CompanyEmployeeReference']['employee_id'] ? $admin_of_company['CompanyEmployeeReference']['employee_id'] : $employee_info['Employee']['id'];
                        $info_sas_login_to_company = array_merge($info_sas_login_to_company,$company,$employee_info);
                        $employee_all_info = $info_sas_login_to_company;
                        $employee_info = $info_sas_login_to_company;
                        $myPC = ClassRegistry::init('ProfitCenter')->find('first', array('recursive' => -1, 'conditions' => array('company_id' => $company['Company']['id']), 'order' => array('id' => 'ASC')));
                        $employee_all_info['Employee']['profit_center_id'] = $myPC['ProfitCenter']['id'];
                    } else {
                        $employee_all_info = $this->Employee->CompanyEmployeeReference->find("first", array("conditions" => array(
                                "CompanyEmployeeReference.id" => $this->data["Employee"]["company_role"])));
                    }
                    $employee_all_info["Employee"]["is_sas"] = 0;
                    $this->Session->write('Auth.employee_info', $employee_all_info);
                    $this->ApiKey->generate($this->Session);
                    $this->writeLog($this->data, $employee_all_info, sprintf('Logon as %s to %s', $employee_all_info["Role"]["desc"], $employee_all_info['Company']['company_name']));
                    $this->redirect("/index");
                } else {
                    $this->Session->setFlash(__('Please choose you company role to continues!', true));
                    $this->redirect("/employees/get_company_role");
                }
            }
        }
    }

    /**
     * logout
     *
     * @return void
     * @access public
     */
    function logout() {
        $this->loadModel('HistoryFilter');
        $sessions = $this->Session->read('Auth.employee_info');
        $this->writeLog('', $sessions, 'Logout');
        $company = !empty($sessions['Company']) ? $sessions['Company']['id'] : '';
        $employeeId = !empty($sessions['Employee']) ? $sessions['Employee']['id'] : '';
        if(!empty($_SERVER['HTTP_REFERER'])){
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'rollback_url_employee_when_login',
                    'employee_id' =>$employeeId
                )
            ));
			$_params = explode( $_SERVER['HTTP_HOST'], $_SERVER['HTTP_REFERER'], 2);
			$_params = $_params[1];// Lấy phần sau của $_SERVER['HTTP_HOST']
            if(!empty($check)){
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array('params' => $_params));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'params' => $_params,
                    'path' => 'rollback_url_employee_when_login',
                    'employee_id' =>$employeeId
                ));
            }
        }
        $cacheName = $company . '_' . $employeeId . '_context_menu';
        Cache::delete($cacheName);
        $cacheNameMenu = $company . '_' . $employeeId . '_context_menu_cache';
        Cache::delete($cacheNameMenu);
        $this->_deleteFileModuleExportActivity();
        $this->ApiKey->destroy($this->Session);
        $this->Session->destroy();
        $this->Auth->logout();
        $this->redirect($this->Auth->logoutRedirect);
    }

    /**
     * Xoa cac file da export
     */
    private function _deleteFileModuleExportActivity(){
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = str_replace(' ', '_', trim(strtolower($this->employee_info['Employee']['fullname']))) . '_' . $employeeLoginId;
        foreach (array(UPLOADS . $employeeLoginName . DS) as $path) {
            $normalFiles = glob($path . '*');
            $hiddenFiles = glob($path . '\.?*');
            $normalFiles = $normalFiles ? $normalFiles : array();
            $hiddenFiles = $hiddenFiles ? $hiddenFiles : array();
            $files = array_merge($normalFiles, $hiddenFiles);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (preg_match('/(\.|\.\.)$/', $file)) {
                        continue;
                    }
                    if (is_file($file) === true) {
                        @unlink($file);
                    }
                }
            }
        }
        $this->loadModel('TmpModuleActivityExport');
        $employeeLoginExportName = str_replace(' ', '_', trim(strtolower($this->employee_info['Employee']['fullname'])));
        $this->TmpModuleActivityExport->deleteAll(array('employee_export' => $employeeLoginExportName, 'date_export' => strtotime(date('d-m-Y', time()))), false);
    }

    /**
     * list_acos
     *
     * @return void
     * @access public
     */
    function list_acos() {
        //    $this->layout = 'ajax';
        $ctrlList = $this->ControllerList->get();
        $arrListCtrlNames = array();
        foreach ($ctrlList as $ctrl => $actions) {
            $arrListCtrlNames[] = $ctrl;
        }
        $this->set("ctrlList", $ctrlList);
        $this->set("arrListCtrlNames", $arrListCtrlNames);
    }

    /**
     * test
     *
     * @return void
     * @access public
     */
    function test() {
        $this->layout = "test_layout";
    }

    /**
     * get_role_child_company
     * @param int $company_id
     * @return void
     * @access public
     */
    function get_role_child_company($company_id = null) {
        $child_companies = $this->Company->find("list", array("conditions" => array("Company.parent_id" => $company_id)));
        // debug($child_companies); exit;
        if (count($child_companies) > 0) {

        }
    }

    /**
     * get_city
     * @param int $company_id
     * @return void
     * @access public
     */
    function get_city($company_id = null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $CityIds = $this->City->find('all', array('conditions' => array('City.company_id' => $company_id)));
            if (!empty($CityIds)) {
                foreach ($CityIds as $CityId) {
                    echo "<option value='" . $CityId['City']['id'] . "'>" . $CityId['City']['name'] . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }
    function get_profit_center($company_id=null) {
        $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
            "conditions" => array('ProjectFunction.company_id' => $company)
        ));
        $this->autoRender = false;
        if ($company_id != "") {
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company_id), null, null, '--');
            if (!empty($profitCenters)) {
                foreach ($profitCenters as $_key=>$_val) {
                    echo "<option value='" . $_key . "'>" . $_val . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }
    function get_skill($company_id=null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
                "conditions" => array('ProjectFunction.company_id' => $company_id)));
            echo "<select id='EmployeeProjectFunctionId' name='data[Employee][project_function_id]' >";
            if (!empty($projectFunctions)) {
                foreach ($projectFunctions as $_key=>$_val) {
                    echo "<option value='" . $_key . "'>" . $_val . "</option>";
                    //echo "<label class=''><input type=\"checkbox\"  name=\"EmployeeProjectFunctionId[]\" id=\"check-$_key\" value=\"$_key\" />$_val</label>";
                }
            } else {
                //echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
            echo "</select>";
        }
    }

    /**
     * get_country
     * @param int $company_id
     * @return void
     * @access public
     */
    function get_country($company_id = null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $CountryIds = $this->Country->find('all', array('conditions' => array('Country.company_id' => $company_id)));
            if (!empty($CountryIds)) {
                foreach ($CountryIds as $CountryId) {
                    echo "<option value='" . $CountryId['Country']['id'] . "'>" . $CountryId['Country']['name'] . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * exportExcel
     * @param int $project_id
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('No data to export!', true));
            $this->redirect("/employees/");
        }
        $data = array_filter(explode(',', $this->data['Export']['list']));
        $this->Employee->cacheQueries = true;
        $employees = $this->Employee->find("all", array(
            'recursive' => -1,
            "conditions" => array('Employee.id' => $data)));
        $employees = Set::combine($employees, '{n}.Employee.id', '{n}');
        $employeeReferences = Set::combine($this->Employee->CompanyEmployeeReference->find('all', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $data))), '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
        $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array(
            'fields' => array('id', 'desc')));
        $companies = $this->Employee->CompanyEmployeeReference->Company->find('list', array(
            'fields' => array('id', 'company_name')));
        $countries = $this->Employee->Country->find('list', array(
            'fields' => array('id', 'name')));
        $cities = $this->Employee->City->find('list', array(
            'fields' => array('id', 'name')));
        $contractTypes = ClassRegistry::init('ContractType')->find('list');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($employees[$id])) {
                    unset($data[$id]);
                    unset($employees[$id]);
                    continue;
                }
                $data[$id] = $employees[$id];
            }
            $employees = $data;
            unset($data);
        }
        $this->set(compact('employees', 'contractTypes', 'companies', 'roles', 'employeeReferences', 'countries', 'cities'));
        $this->layout = '';
    }
    /**
     * exportExcelplus
     * @param int $project_id
     * @return void
     * @access public
     */
    function exportExcelplus($project_id = null) {
        $this->loadModel('ProfitCenter');
        if (empty($this->data['Exportplus']['list'])) {
            $this->Session->setFlash(__('No data to export!', true));
            $this->redirect("/employees/");
        }
        $data = array_filter(explode(',', $this->data['Exportplus']['list']));

        $joins = array();
        if( isset($this->employee_info['Company']['id']) ){
            $joins[0] = array(
                'table' => 'company_employee_references',
                'alias' => 'Ref',
                'conditions' => array(
                    'Ref.employee_id = Employee.id',
                    'Ref.company_id' => $this->employee_info['Company']['id']
                )
            );
        }
        $this->Employee->cacheQueries = true;
        $employees = $this->Employee->find("all", array(
            'recursive' => -1,
            "conditions" => array('Employee.id' => $data),
            'joins' => $joins
        ));
        if( count($employees) != count($data) ){
            $this->Session->setFlash(__('Warning: you are not allowed to export unauthorized resources'), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $employees = Set::combine($employees, '{n}.Employee.id', '{n}');
        $employeeReferences = Set::combine($this->Employee->CompanyEmployeeReference->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('employee_id' => $data))), '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
        $roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array(
            'fields' => array('id', 'desc')));
        $companies = $this->Employee->CompanyEmployeeReference->Company->find('list', array(
            'fields' => array('id', 'company_name')));
        $countries = $this->Employee->Country->find('list', array(
            'fields' => array('id', 'name')));
        $cities = $this->Employee->City->find('list', array(
            'fields' => array('id', 'name')));
        $contractTypes = ClassRegistry::init('ContractType')->find('list');
        $profitCentes = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));

        $profitCenterReferEmployees = $this->Employee->ProjectEmployeeProfitFunctionRefer->find('list', array(
            'fields' => array('employee_id', 'profit_center_id'),
            'conditions' => array(
                'employee_id' => $data,
                'AND' => array(
                    'NOT' => array('profit_center_id' => null),
                    'NOT' => array('profit_center_id' => 0)
                )),
            'group' => array('employee_id')
            ));
        $listNameProfitCenters = array();
        if(!empty($profitCenterReferEmployees)){
            foreach($profitCenterReferEmployees as $employeeId => $profitCenterId){
                $_nameProfitCenter = !empty($profitCentes[$profitCenterId]) ? $profitCentes[$profitCenterId] : '';
                $listNameProfitCenters[$employeeId] = $_nameProfitCenter;
            }
        }

        // lay function tat ca function
        $projectFunction = $this->ProjectFunction->find('list',array(
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));

        // lay  function_id theo employee_id
        $projectFunctionReferEmployees = $this->Employee->ProjectEmployeeProfitFunctionRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array ('ProjectEmployeeProfitFunctionRefer.employee_id' => $data),
            'fields' => array('employee_id' ,'function_id')
        ));
        $listNameFunction = array();
        if(!empty($projectFunctionReferEmployees)){
            foreach($projectFunctionReferEmployees as $projectFunctionReferEmployee){
                $dx = $projectFunctionReferEmployee['ProjectEmployeeProfitFunctionRefer'];
                if(!isset($listNameFunction[$dx['employee_id']])){
                    //do nothing
                }
                $listNameFunction[$dx['employee_id']][] = !empty($projectFunction[$dx['function_id']]) ? $projectFunction[$dx['function_id']] : '';
            }
        }
        //
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($employees[$id])) {
                    unset($data[$id]);
                    unset($employees[$id]);
                    continue;
                }
                $data[$id] = $employees[$id];
            }
            $employees = $data;
            unset($data);
        }
        $this->set(compact('employees', 'contractTypes', 'companies', 'roles', 'employeeReferences', 'countries', 'cities','listNameProfitCenters','listNameFunction'));
        $this->layout = '';
    }
    /**
     * change keys for import
     * in case importing data whose headers are in languages other than english
     */
    function _changeKeys($default, $row){
        $newKeys = array_key($default);
        $oldKeys = array_keys($row);
        array_unshift($newKeys, 'No.');
        $result = array();
        $i = 0;
        foreach($newKeys as $key){
            $result[$key] = $row[ $oldKeys[$i] ];
            $i++;
        }
        return $result;
    }
    /*
     * If case on exists then notify.
     *
     * @param int $project_id
     * @return void
     * @access public
     */
    function import_csv() {
        //$this->autoRender = false;
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Employee' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Employee' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Update' => array(),
                        'Error' => array()
                    );
                    $default = array(
                        'First name'=> '',
                        'Last name' => '',
                        //'name' => '',
                        'Email' => '', // r
                        'Company' => '', // r
                        'Role' => '', // r
                        'Profit center' => '', //r
                        'Skill' => '',
                        'Start date' => '',
                        'End date' => '',
                        'Average Daily Rate' => '',
                        'ID' => '',
                        'ID2' => '',
                        'ID3' => '',
                        'ID4' => '',
                        'ID5' => '',
                        'ID6' => '',
                        'Actif' => '',
                        'External' => '',
                        'Work phone' => '',
                        'Mobile phone' => '',
                        'Home phone' => '',
                        'Fax number' => '',
                        'City' => '',
                        'Post code' => '',
                        'Address' => '',
                        'Country' => '',
                        'Type Of Contract' => '',
                        'Capacity/Year' => ''
                    );
                    $this->loadModel('City');
                    $this->loadModel('Country');
                    $this->loadModel('ContractType');
                    $this->City->cacheQueries = $this->Country->cacheQueries = true;

                    $validate = array('First name', 'Last name', 'Email', 'Company');
                    $emails = array();
                    $code_id = array();
                    if ($this->employee_info['Employee']['is_sas'] == 1) {
                        foreach ($csv->data as $row) {
                            $row = $this->change_keys($row);
                            $error = false;
                            $row = array_merge($default, $row, array('data' => array(), 'error' => array(), 'description' => array()));
                            foreach ($validate as $key) {
                                $row[$key] = trim($row[$key]);
                                if (empty($row[$key])) {
                                    $row['error'][] = sprintf(__('The %s is not blank', true), $key);
                                    $error = true;
                                }
                            }
                            if (!$error) {
                                // Company
                                $tmp = $this->Company->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array('company_name' => $row['Company']),
                                    'fields' => array('id', 'company_name')));
                                if (empty($tmp)) {
                                    $row['error'][] = __('The Company ID does not exist', true);
                                    $error = true;
                                } else {
                                    $row['data']['company_id'] = $tmp['Company']['id'];
                                    $row['data']['company_name'] = strtolower(trim($row['Company']));
                                    $company_id = $tmp['Company']['id'];
                                    // Function
                                    $tmp = array_filter(explode(',', trim($row['Skill'])));
                                    if (!empty($tmp)) {
                                        foreach ($tmp as $value) {
                                            $_value = $this->ProjectFunction->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array('name' => $value, 'company_id' => $company_id),
                                                'fields' => array('id')));
                                            if (empty($_value)) {
                                                $row['error'][] = sprintf(__('The Skill \'%s\' does not exist', true), $value);
                                                $error = true;
                                                break;
                                            }
                                            $row['data']['function_id'][] = $_value['ProjectFunction']['id'];
                                        }
                                    }
                                    if (!empty($row['data']['company_id'])) {
                                        // Profit
                                        $name = trim($row['Profit center']);
                                        $tmp = $this->ProfitCenter->find('list', array(
                                            'recursive' => -1,
                                            'conditions' => array('company_id' => $row['data']['company_id']),
                                            'fields' => array('id', 'name')));
                                        $list = array_values($tmp);
                                        $rev = array_flip($tmp);
                                        if (!empty($tmp) && in_array($name, $list)) {
                                            $row['data']['profit_id'] = $rev[$name];
                                        } elseif (!empty($tmp) && empty($row['Profit center'])) {
                                            $row['data']['profit_id'] = reset($rev);
                                        } else {
                                            $row['error'][] = sprintf(__('The Profit Center \'%s\' does not exist', true), $row['Profit center']);
                                            $error = true;
                                        }
                                    }
                                    // Email
                                    $row['Email'] = strtolower(trim($row['Email']));
                                    if (isset($emails[$row['Email']]) || !Validation::email($row['Email'])) {
                                        $row['error'][] = __('The Email ID is invalid or already exists in the file.', true);
                                        $error = true;
                                    } elseif (!$error) {
                                        $emails[$row['Email']] = '';
                                        $tmp = $this->Employee->CompanyEmployeeReference->find('first', array(
                                            'recursive' => 0,
                                            'conditions' => array(
                                                'Employee.email' => $row['Email'],
                                                'CompanyEmployeeReference.company_id' => $row['data']['company_id']),
                                            'fields' => array('Employee.id')));
                                        if (!empty($tmp)) {
                                            $row['data']['id'] = $tmp['Employee']['id'];
                                        }
                                    }

                                    if( $row['Start date'] == '0000-00-00' )$row['Start date'] = '';
                                    if( $row['End date'] == '0000-00-00' )$row['End date'] = '';

                                    // Start date
                                    if (!empty($row['Start date']) && isset($row['Start date']) && strtotime(str_replace('/', '-', $row['Start date'])) != 0) {
                                        $row['data']['start_date'] = date('d-m-Y', strtotime(str_replace('/', '-', $row['Start date'])));
                                        if (!$row['data']['start_date']) {
                                            $row['error'][] = __('The start date is invalid.', true);
                                            $error = true;
                                        }
                                    }
                                    // End date
                                    if (!empty($row['End date']) && isset($row['End date']) && strtotime(str_replace('/', '-', $row['End date'])) != 0) {
                                        $row['data']['end_date'] = date('d-m-Y', strtotime(str_replace('/', '-', $row['End date'])));
                                        if (!$row['data']['end_date']) {
                                            $row['error'][] = __('The end date is invalid.', true);
                                            $error = true;
                                        } elseif (!empty($row['data']['start_date']) && strtotime($this->ProfitCenter->convertTime($row['data']['start_date'])) > strtotime($this->ProfitCenter->convertTime($row['data']['end_date']))) {
                                            $row['error'][] = __('The start date conflict with end date.', true);
                                            $error = true;
                                        }
                                    }

                                    // Tim
                                    if (!empty($row['Average Daily Rate'])) {
                                        $row['data']['tjm'] = intval($row['Average Daily Rate']);
                                        if (!$row['data']['tjm']) {
                                            $row['error'][] = sprintf(__('The Average Daily Rate is invalid.', true), $row['Profit center']);
                                            $error = true;
                                        }
                                    }
                                    // ID
                                    $row['data']['code_id'] = $row['ID'];
                                    // ID2
                                    $row['data']['identifiant'] = $row['ID2'];
                                    $row['data']['id3'] = $row['ID3'];
                                    $row['data']['id4'] = $row['ID4'];
                                    $row['data']['id5'] = $row['ID5'];
                                    $row['data']['id6'] = $row['ID6'];
                                    // Actif
                                    if (isset($row['Actif']) && strlen($row['Actif']) > 0) {
                                        $row['data']['actif'] = intval($row['Actif']) ? 1 : 0;
                                    }
                                    // External
                                    if (isset($row['External']) && strlen($row['External']) > 0) {
                                        $row['data']['external'] = intval($row['External']);
                                    }
                                    // Home phone
                                    if (!empty($row['Home phone'])) {
                                        if (!is_numeric($row['Home phone'])) {
                                            $row['error'][] = sprintf(__('The %s must be number', true), __('Home phone', true));
                                            $error = true;
                                        } else {
                                            $row['data']['home_phone'] = $row['Home phone'];
                                        }
                                    }
                                    // Work phone
                                    if(empty($row['Work phone'])){
                                        $row['description'][] =  sprintf(__('%s', true), __('Work phone', true));
                                    }
                                    else{
                                        if (!is_numeric($row['Work phone'])) {
                                            $row['error'][] = sprintf(__('The %s must be number', true), __('Work phone', true));
                                            $error = true;
                                        } else {
                                            $row['data']['work_phone'] = $row['Work phone'];
                                        }
                                    }
                                    // Mobile phone
                                    if (!empty($row['Mobile phone'])) {
                                        if (!is_numeric($row['Mobile phone'])) {
                                            $row['error'][] = sprintf(__('The %s must be number', true), __('Mobile phone', true));
                                            $error = true;
                                        } else {
                                            $row['data']['mobile_phone'] = $row['Mobile phone'];
                                        }
                                    }
                                    // Fax number
                                    if (!empty($row['Fax number'])) {
                                        if (!is_numeric($row['Fax number'])) {
                                            $row['error'][] = sprintf(__('The %s must be number', true), __('Fax number', true));
                                            $error = true;
                                        } else {
                                            $row['data']['fax'] = $row['Fax number'];
                                        }
                                    }
                                    // Post code
                                    if (!empty($row['Post code'])) {
                                        if (!is_numeric($row['Post code'])) {
                                            $row['error'][] = sprintf(__('The %s must be number', true), __('Post code', true));
                                            $error = true;
                                        } else {
                                            $row['data']['post_code'] = $row['Post code'];
                                        }
                                    }
                                    // Address
                                    if (!empty($row['Address'])) {
                                        $row['data']['address'] = $row['Address'];
                                    }
                                    // capacity_by_year
                                    if (!empty($row['Capacity/Year'])) {
                                        $row['data']['capacity_by_year'] = $row['Capacity/Year'];
                                    }
                                    // City
                                    if (!empty($row['City'])) {
                                        $tmp = $this->City->find('first', array('fields' => array('id'), 'recursive' => -1
                                            , 'conditions' => array('name' => trim($row['City']))));
                                        if ($tmp) {
                                            $row['data']['city_id'] = $tmp['City']['id'];
                                        } else {
                                            $row['error'][] = __('The City ID does not exist', true);
                                            $error = true;
                                        }
                                    }
                                    // Country
                                    if (!empty($row['Country'])) {
                                        $tmp = $this->Country->find('first', array('fields' => array('id'), 'recursive' => -1
                                            , 'conditions' => array('name' => trim($row['Country']))));
                                        if ($tmp) {
                                            $row['data']['country_id'] = $tmp['Country']['id'];
                                        } else {
                                            $row['error'][] = __('The Country ID does not exist', true);
                                            $error = true;
                                        }
                                    }
                                    // Type Of Contract
                                    if (!empty($row['Type Of Contract'])) {
                                        $tmp = $this->ContractType->find('first', array('fields' => array('id'), 'recursive' => -1
                                            , 'conditions' => array('name' => trim($row['Type Of Contract']))));
                                        if ($tmp) {
                                            $row['data']['contract_type_id'] = $tmp['ContractType']['id'];
                                        } else {
                                            $row['error'][] = __('Type Of Contract ID does not exist', true);
                                            $error = true;
                                        }
                                    }
                                    if (!empty($row['data']['company_id'])) {
                                        // Role
                                        if (empty($row['Role'])) {
                                            if (empty($row['data']['id'])) {
                                                $row['data']['role_id'] = 4;
                                            }
                                        } else {
                                            $tmp = $this->Employee->CompanyEmployeeReference->find('first', array(
                                                'recursive' => 0,
                                                'conditions' => array(
                                                    'Role.desc' => $row['Role'],
                                                    'CompanyEmployeeReference.company_id' => $row['data']['company_id']),
                                                'fields' => array('Role.id')));
                                            if (empty($tmp)) {
                                                $row['error'][] = __('The Role ID does not exist', true);
                                                $error = true;
                                            } else {
                                                $row['data']['role_id'] = $tmp['Role']['id'];
                                            }
                                        }
                                    }
                                }
                            }
                            if ($error) {
                                unset($row['data']);
                                $records['Error'][] = $row;
                            } else {
                                $row['data']['first_name'] = $row['First name'];
                                $row['data']['last_name'] = $row['Last name'];
                                $row['data']['email'] = $row['Email'];
                                if (!empty($row['data']['id'])) {
                                    //check date
                                    $test = array(
                                        'start_date' => $row['data']['start_date'],
                                        'end_date' => $row['data']['end_date'],
                                    );
                                    if( $test['start_date'] == '0000-00-00' )unset($test['start_date']);
                                    if( $test['end_date'] == '0000-00-00' )unset($test['end_date']);
                                    $check = $this->checkConsume($row['data']['id'], $test);
                                    if( $check[0] && $check[1] ) {
                                        $records['Update'][] = $row;
                                    } else {
                                        if( !$check[0] ){
                                            $row['error'][] = __('Can not modify start date because this resource already has request/consumed data before that day', true);
                                            $row['columnHighLight']['Start date'] =  '';
                                        }
                                        if( !$check[1] ){
                                            $row['error'][] = __('Can not modify end date because this resource already has request/consumed data after that day', true);
                                            $row['columnHighLight']['End date'] =  '';
                                        }
                                        unset($row['data']);
                                        $records['Error'][] = $row;
                                    }
                                } else {
                                    $records['Create'][] = $row;
                                }
                            }
                        }
                    } else {
                        // is_sas !=1
                        $company = $this->employee_info['Company']['id'];
                        $company = array_merge(array($company), $this->Company->find('list', array('conditions' => array('parent_id' => $company))));
                        $defaultProfitCenter = $this->ProfitCenter->find('first', array(
                            'conditions' => array('company_id' => $company[0], 'or' => array('name' => 'DEFAULT')),
                            'recursive' => -1,
                            'fields' => array('id', 'name')));

                        $validate = array('Email');
                        foreach ($csv->data as  $row) {
                            $row = $this->change_keys($row);
                            $error = false;
                            $row = array_merge($default, $row, array('data' => array(), 'error' => array(),'description' => array(), 'columnHighLight' => array()));

                            foreach ($validate as $key) {
                                $row[$key] = trim($row[$key]);
                                if (empty($row[$key])) {
                                    $row['columnHighLight']['Email'] =  '';
                                    $row['error'][] = sprintf(__('The %s is not blank', true), $key);
                                    $error = true;
                                }
                            }
                            if (!$error) {

                                if (isset($code_id[$row['ID']])&&$row['ID']!='') {
                                    $row['error'][]=__('The ID is invalid or already exists in the file.', true);
                                    $error = true;
                                    //$row['data']['code_id'] = $row['ID'];
                                } elseif(!$error){
                                    $code_id[$row['ID']] = '';
                                    if($row['ID']!=''){
                                        $tmp = $this->Employee->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'code_id' => $row['ID'],
                                                ),
                                            'fields' => array('id','code_id', 'is_sas','email')));
                                        if(!empty($tmp)){
                                            $chkID = $this->Employee->find('first',array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'code_id' => $row['ID'],
                                                'id !=' => $tmp['Employee']['id']
                                                ),
                                            'fields' => array('id','code_id', 'is_sas','email')
                                            ));
                                            if (!empty($chkID)) {
                                            $row['columnHighLight']['ID'] =  '';
                                            $row['error'][]=__('The ID already exists.', true);
                                            $error = true;
                                            }
                                        }
                                    }
                                }
                                // Email
                                $row['Email'] = strtolower(trim($row['Email']));
                                if (isset($emails[$row['Email']]) || !Validation::email($row['Email'])) {
                                    $row['error'][] = __('The Email ID is invalid or already exists in the file.', true);
                                    $error = true;
                                } elseif (!$error) {
                                    $emails[$row['Email']] = '';
                                    $tmp = $this->Employee->CompanyEmployeeReference->find('first', array(
                                        'recursive' => 0,
                                        'conditions' => array(
                                            'Employee.email' => $row['Email'],
                                            'CompanyEmployeeReference.company_id' => $company),
                                        'fields' => array('Employee.id', 'Employee.is_sas')));
                                    if (!empty($tmp)) {
                                        if (!empty($tmp['Employee']['is_sas'])) {
                                            $row['error'][] = __('You are not allowed to edit this employee.', true);
                                            $error = true;
                                        }
                                        if(!empty($tmp['Employee']['email'])){
                                            $row['error'][] = __('The Email ID already exists.', true);
                                            $error = true;
                                        }
                                            $row['data']['id'] = $tmp['Employee']['id'];
                                    }
                                }
                                // Company
                                $row['data']['company_id'] = $this->employee_info['Company']['id'];
                                //safe utf-8 strtolower
                                $row['data']['company_name'] = strtolower(trim($this->employee_info['Company']['company_name']));

                                // Function
                                if (!empty($row['Skill'])) {
                                    $tmp = array_filter(preg_split('/[;,]+/', trim($row['Skill'])));
                                    if (!empty($tmp)) {
                                        foreach ($tmp as $value) {
                                            $_value = $this->ProjectFunction->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array('name' => $value, 'company_id' => $company),
                                                'fields' => array('id')));
                                            if (empty($_value)) {
                                                $row['columnHighLight']['Skill'] =  '';
                                                $row['error'][] = sprintf(__('The Skill \'%s\' does not exist', true), $value);
                                                $error = true;
                                                break;
                                            }
                                            $row['data']['function_id'][] = $_value['ProjectFunction']['id'];
                                        }
                                    }
                                }
                                // else{
                                //     $row['columnHighLight']['Skill'] =  '';
                                //     $row['description'][] = sprintf(__('%s', true), __('Skill', true));
                                // }

                                // Profit
                                $tmp = $this->ProfitCenter->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array('name' => trim($row['Profit center']), 'company_id' => $company),
                                    'fields' => array('id')));

                                if (!empty($tmp)) {
                                    $row['data']['profit_id'] = $tmp['ProfitCenter']['id'];
                                } elseif (empty($row['Profit center'])) {
                                    $row['data']['profit_id'] = $defaultProfitCenter['ProfitCenter']['id'];
                                    $row['columnHighLight']['Profit center'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Profit center', true));
                                } else {
                                    $row['error'][] = sprintf(__('The Profit Center \'%s\' does not exist', true), $row['Profit center']);
                                    $error = true;
                                }

                                if( $row['Start date'] == '0000-00-00' )$row['Start date'] = '';
                                if( $row['End date'] == '0000-00-00' )$row['End date'] = '';

                                // Start date
                                if (!empty($row['Start date']) && isset($row['Start date']) && strtotime(str_replace('/', '-', $row['Start date'])) != 0) {
                                    $row['data']['start_date'] = date('d-m-Y', strtotime(str_replace('/', '-', $row['Start date'])));
                                    if (!$row['data']['start_date']) {
                                        $row['columnHighLight']['Start date'] =  '';
                                        $row['error'][] = __('The start date is invalid.', true);
                                        $error = true;
                                    }
                                } else{
                                    // $row['columnHighLight']['Start date'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Start date', true));
                                }
                                // End date
                                if (!empty($row['End date']) && isset($row['End date']) && strtotime(str_replace('/', '-', $row['End date'])) != 0) {
                                    $row['data']['end_date'] = date('d-m-Y', strtotime(str_replace('/', '-', $row['End date'])));
                                    if (!$row['data']['end_date']) {
                                        $row['columnHighLight']['End date'] =  '';
                                        $row['error'][] = __('The end date is invalid.', true);
                                        $error = true;
                                    } elseif (!empty($row['data']['start_date']) && strtotime($this->ProfitCenter->convertTime($row['data']['start_date'])) > strtotime($this->ProfitCenter->convertTime($row['data']['end_date']))) {
                                        $row['columnHighLight']['End date'] =  '';
                                        $row['error'][] = __('The start date conflict with end date.', true);
                                        $error = true;
                                    }
                                }
                                // Tim
                                if (!empty($row['Average Daily Rate'])) {
                                    $row['data']['tjm'] = intval($row['Average Daily Rate']);
                                    if (!$row['data']['tjm']) {
                                        $row['columnHighLight']['Average Daily Rate'] =  '';
                                        $row['error'][] = sprintf(__('The Average Daily Rate is invalid.', true), $row['Profit center']);
                                        $error = true;
                                    }
                                } else{
                                    // $row['columnHighLight']['Average Daily Rate'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Average Daily Rate', true));
                                }

                                // ID
                                if (!empty($row['ID'])) {
                                    $row['data']['code_id'] = $row['ID'];
                                } else {
                                    // $row['columnHighLight']['ID'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('ID', true));
                                }

                                // ID2
                                if (!empty($row['ID2'])) {
                                    $row['data']['identifiant'] = $row['ID2'];
                                } else {
                                    // $row['columnHighLight']['ID2'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('ID2', true));
                                }

                                if( !empty($row['ID3']) ){
                                    $row['data']['id3'] = $row['ID3'];
                                } else {
                                    // $row['columnHighLight']['ID2'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('ID3', true));
                                }

                                //id4 -> 6: added on 20/12/2014
                                if( !empty($row['ID4']) ){
                                    $row['data']['id4'] = $row['ID4'];
                                } else {
                                    $row['description'][] = sprintf(__('%s', true), __('ID4', true));
                                }

                                if( !empty($row['ID5']) ){
                                    $row['data']['id5'] = $row['ID5'];
                                } else {
                                    $row['description'][] = sprintf(__('%s', true), __('ID5', true));
                                }

                                if( !empty($row['ID6']) ){
                                    $row['data']['id6'] = $row['ID6'];
                                } else {
                                    $row['description'][] = sprintf(__('%s', true), __('ID6', true));
                                }

                                // Actif
                                if (isset($row['Actif']) && strlen($row['Actif']) > 0) {
                                    $row['data']['actif'] = intval($row['Actif']) ? 1 : 0;
                                } else {
                                    $row['description'][] = sprintf(__('%s', true), __('Actif', true));
                                }

                                // External
                                if (isset($row['External']) && strlen($row['External']) > 0) {
                                    $row['data']['external'] = intval($row['External']);
                                } else {
                                    // $row['columnHighLight']['External'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('External', true));
                                }
                                // Home phone
                                if (!empty($row['Home phone'])) {
                                    if (!is_numeric($row['Home phone'])) {
                                        $row['columnHighLight']['Home phone'] =  '';
                                        $row['error'][] = sprintf(__('The %s must be number', true), __('Home phone', true));
                                        $error = true;
                                    } else {
                                        $row['data']['home_phone'] = $row['Home phone'];
                                    }
                                } else {
                                    // $row['columnHighLight']['Home phone'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Home phone', true));
                                }

                                // Work phone
                                if (!empty($row['Work phone'])) {
                                    if (!is_numeric($row['Work phone'])) {
                                        $row['columnHighLight']['Work phone'] =  '';
                                        $row['error'][] = sprintf(__('The %s must be number', true), __('Work phone', true));
                                        $error = true;
                                    } else {
                                        $row['data']['work_phone'] = $row['Work phone'];
                                    }
                                } else {
                                    // $row['columnHighLight']['Work phone'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Work phone', true));

                                }

                                // Mobile phone
                                if (!empty($row['Mobile phone'])) {
                                    if (!is_numeric($row['Mobile phone'])) {
                                        $row['columnHighLight']['Mobile phone'] =  '';
                                        $row['error'][] = sprintf(__('The %s must be number', true), __('Mobile phone', true));
                                        $error = true;
                                    } else {
                                        $row['data']['mobile_phone'] = $row['Mobile phone'];
                                    }
                                } else {
                                    // $row['columnHighLight']['Mobile phone'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Mobile phone', true));
                                }

                                // Fax number
                                if (!empty($row['Fax number'])) {
                                    if (!is_numeric($row['Fax number'])) {
                                        $row['columnHighLight']['Fax number'] =  '';
                                        $row['error'][] = sprintf(__('The %s must be number', true), __('Fax number', true));
                                        $error = true;
                                    } else {
                                        $row['data']['fax'] = $row['Fax number'];
                                    }
                                } else {
                                    // $row['columnHighLight']['Fax number'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Fax number', true));
                                }

                                // Post code
                                if (!empty($row['Post code'])) {
                                    if (!is_numeric($row['Post code'])) {
                                        $row['error'][] = sprintf(__('The %s must be number', true), __('Post code', true));
                                        $row['columnHighLight']['Post code'] =  '';
                                        $error = true;
                                    } else {
                                        $row['data']['post_code'] = $row['Post code'];
                                    }
                                } else {
                                    // $row['columnHighLight']['Post code'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Post code', true));
                                }

                                // Address
                                if (!empty($row['Address'])) {
                                    $row['data']['address'] = $row['Address'];
                                } else {
                                    // $row['columnHighLight']['Address'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Address', true));
                                }

                                // capacity_by_year
                                if (!empty($row['Capacity/Year'])) {
                                    $row['data']['capacity_by_year'] = $row['Capacity/Year'];
                                }

                                // City
                                if (!empty($row['City'])) {
                                    $tmp = $this->City->find('first', array('fields' => array('id'), 'recursive' => -1
                                        , 'conditions' => array('name' => trim($row['City']))));
                                    if ($tmp) {
                                        $row['data']['city_id'] = $tmp['City']['id'];
                                    } else {
                                        $row['columnHighLight']['City'] =  '';
                                        $row['error'][] = __('The City ID does not exist', true);
                                        $error = true;
                                    }
                                } else {
                                    // $row['columnHighLight']['City'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('City', true));
                                }

                                // Country
                                if (!empty($row['Country'])) {
                                    $tmp = $this->Country->find('first', array('fields' => array('id'), 'recursive' => -1
                                        , 'conditions' => array('name' => trim($row['Country']))));
                                    if ($tmp) {
                                        $row['data']['country_id'] = $tmp['Country']['id'];
                                    } else {
                                        $row['columnHighLight']['Country'] =  '';
                                        $row['error'][] = __('The Country ID does not exist', true);
                                        $error = true;
                                    }
                                } else {
                                    // $row['columnHighLight']['Country'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Country', true));
                                }
                                // Type Of Contract
                                if (!empty($row['Type Of Contract'])) {
                                    $tmp = $this->ContractType->find('first', array('fields' => array('id'), 'recursive' => -1
                                        , 'conditions' => array('name' => trim($row['Type Of Contract']))));
                                    //debug($tmp);exit;
                                    if ($tmp) {
                                        $row['data']['contract_type_id'] = $tmp['ContractType']['id'];
                                    } else {
                                        $row['error'][] = __('Type Of Contract ID does not exist', true);
                                        $row['columnHighLight']['Type Of Contract'] =  '';
                                        $error = true;
                                    }
                                } else {
                                    // $row['columnHighLight']['Type Of Contract'] =  '';
                                    $row['description'][] = sprintf(__('%s', true), __('Type Of Contract', true));
                                }
                                // Role

                                if (empty($row['Role'])) {
                                    if (empty($row['data']['id'])) {
                                        $row['data']['role_id'] = 4;
                                    } else {
                                        $row['data']['role_id'] = array();
                                    }
                                } else {
                                    $tmp = $this->Employee->CompanyEmployeeReference->find('first', array(
                                        'recursive' => 0,
                                        'conditions' => array(
                                            'Role.desc' => $row['Role'],
                                            'CompanyEmployeeReference.company_id' => $company),
                                        'fields' => array('Role.id')));
                                    //edit by Thach 10/31/2013
                                    //Do not allow to modify the role  admin company
                                    if($tmp['Role']['id'] == 2){
                                        $row['columnHighLight']['Role'] =  '';
                                        $row['error'][] = __('Do not allow to modify the role  admin company', true);
                                        $error = true;
                                    } else {
                                        if(empty($tmp)){
                                            $tmp = ClassRegistry::init('Role')->find('first', array(
                                                'recursive' => 0,
                                                'conditions' => array(
                                                    'Role.desc' => $row['Role']),
                                                'fields' => array('Role.id')
                                            ));
                                        }
                                        if (empty($tmp)) {
                                            $row['columnHighLight']['Role'] =  '';
                                            $row['error'][] = __('The Role ID does not exist', true);
                                            $error = true;
                                        } else {
                                            $row['data']['role_id'] = $tmp['Role']['id'];
                                        }
                                    }
                                }
                            }//end if error

                            if ($error) {
                                unset($row['data']);
                                $records['Error'][] = $row;
                            } else {
                                $row['data']['first_name'] = $row['First name'];
                                $row['data']['last_name'] = $row['Last name'];
                                $row['data']['email'] = $row['Email'];
                                if (!empty($row['data']['id'])) {
                                    //check date
                                    $test = array(
                                        'start_date' => !empty($row['data']['start_date']) ? $row['data']['start_date'] : '0000-00-00',
                                        'end_date' => !empty($row['data']['end_date']) ? $row['data']['end_date'] : '0000-00-00',
                                    );
                                    if( $test['start_date'] == '0000-00-00' )unset($test['start_date']);
                                    if( $test['end_date'] == '0000-00-00' )unset($test['end_date']);
                                    $check = $this->checkConsume($row['data']['id'], $test);
                                    if( $check[0] && $check[1] ) {
                                        $records['Update'][] = $row;
                                    } else {
                                        if( !$check[0] ){
                                            $row['error'][] = __('Can not modify start date because this resource already has request/consumed data before that day', true);
                                            $row['columnHighLight']['Start date'] =  '';
                                        }
                                        if( !$check[1] ){
                                            $row['error'][] = __('Can not modify end date because this resource already has request/consumed data after that day', true);
                                            $row['columnHighLight']['End date'] =  '';
                                        }
                                        unset($row['data']);
                                        $records['Error'][] = $row;
                                    }
                                } else {
                                    $records['Create'][] = $row;
                                }

                            }
                        }
                    }
                }
                unlink($filename);
            }
            $this->set('records', $records);
            $this->set('default', $default);
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function save_file_import() {
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {//import
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    $this->redirect(array('action' => 'index'));
                }

                $complete = 0;
                //$password = md5('pm123');
                foreach ($import as $data) {
                    $function = array(null);
                    if (!empty($data['function_id'])) {
                        $function = $data['function_id'];
                        unset($data['function_id']);
                    }
                    if (!empty($data['id'])) {
                        $this->Employee->id = $data['id'];
                        unset($data['id']);
                    } else {
                        $this->Employee->create();
                        $data['password'] = md5(strtolower(trim($data['company_name'])));
                    }
                    $data['profit_center_id'] = !empty($data['profit_id']) ? $data['profit_id'] : '';
                    if ($this->Employee->save($data)) {
                        $this->Employee->id = $this->Employee->id ? $this->Employee->id : $this->Employee->getInsertID();
                        $Model = $this->Employee->ProjectEmployeeProfitFunctionRefer;
                        $saved = array();
                        foreach ($function as $fun) {
                            $_data = array(
                                'profit_center_id' => $data['profit_id'],
                                'employee_id' => $this->Employee->id,
                                'function_id' => $fun
                            );
                            $last = $Model->find('first', array('conditions' => $_data, 'fields' => array('id'), 'recursive' => -1));
                            if (!$last) {
                                $Model->create();
                                $Model->save($_data);
                                $saved[] = $Model->getInsertID();
                            } else {
                                $saved[] = $last['ProjectEmployeeProfitFunctionRefer']['id'];
                            }
                        }
                        $Model->deleteAll(array($Model->alias . '.employee_id' => $this->Employee->id, 'not' => array($Model->alias . '.id' => $saved)));
                        $Model = $this->Employee->CompanyEmployeeReference;

                        if (empty($data['role_id'])) {
                            $data['role_id'] = array();
                        }
                        foreach ((array) $data['role_id'] as $fun) {
                            $_data = array(
                                'employee_id' => $this->Employee->id
                            );
                            if (!empty($data['company_id'])) {
                                $_data['company_id'] = $data['company_id'];
                            }
                            $last = $Model->find('first', array('conditions' => $_data, 'recursive' => -1));
                            if ($last) {
                                $Model->id = $last['CompanyEmployeeReference']['id'];
                            } else {
                                $Model->create();
                            }
                            $Model->save(array_merge($_data, array('role_id' => $fun)));
                        }
                        $complete++;
                    }
                }
                $this->Session->setFlash(sprintf(__('The employees has been imported %s/%s.', true), $complete, count($import)));
                $this->redirect(array('action' => 'index'));
            } else { // export csv
                App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
                $csv = new parseCSV();
                header("Content-Type: text/html; charset=ISO-8859");
                // export
                $header = array();
                $type = '';
                if ($this->data['Import']['type'] == 'Error' && !empty($this->data['Error']['export']))
                    $type = 'Error';
                if ($this->data['Import']['type'] == 'Create' && !empty($this->data['Create']['export']))
                    $type = 'Create';
                if ($this->data['Import']['type'] == 'Update' && !empty($this->data['Update']['export']))
                    $type = 'Update';
                if (!empty($type)) {
                    $_listEmployee = array();
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                            $header[] = __($key , true);
                        };
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
        exit;
    }
    private function _mix_coloumn($input){
        $result = array();
        foreach($input as $value){
            $result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
     /**
    * Encodes an ISO-8859-1 mixed variable to UTF-8 (PHP 4, PHP 5 compat)
    * @param    mixed    $input An array, associative or simple
    * @param    boolean  $encode_keys optional
    * @return    mixed     ( utf-8 encoded $input)
    */

    private function change_keys($input){
        $result = array();
        $key = array('No.','First name','Last name','Email','Company','Role','Profit center','Skill','Start date',
                    'End date','Average Daily Rate','ID','ID2', 'ID3', 'ID4','ID5', 'ID6', 'Actif','External','Work phone','Mobile phone',
                    'Home phone','Fax number','City','Post code','Address','Country','Type Of Contract', 'Capacity/Year'
        );
        $i=0;
        foreach($input as $value){
            $result[$key[$i]] = $value;
            $i++;
        }
        return $result;
    }
    private function _translate_array($input){
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding(__($key, true),'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    private function _utf8_encode_mix($input) {
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }

    /**
     * update_id. Update table "project_employee_profit_function_refers" with Employee ID does not exists a profit center.
     * @param
     * @return void
     * @access public
     */
    function update_id() {
        $this->autoRender = false;
        $employee = $this->Employee->CompanyEmployeeReference->find('all', array('recursive' => -1));
        $profit = $this->ProfitCenter->find('all', array('recursive' => -1, 'group' => 'company_id'));
        foreach ($employee as $empl) {
            $abc = $this->ProjectEmployeeProfitFunctionRefer->find('all', array('group' => 'employee_id', 'recursive' => -1, 'conditions' => array('employee_id' => $empl['CompanyEmployeeReference']['employee_id'])));
            if (empty($abc)) {
                foreach ($profit as $pro) {
                    if ($empl['CompanyEmployeeReference']['company_id'] == $pro['ProfitCenter']['company_id']) {
                        $this->ProjectEmployeeProfitFunctionRefer->create();
                        $this->data['ProjectEmployeeProfitFunctionRefer']['profit_center_id'] = $pro['ProfitCenter']['id'];
                        $this->data['ProjectEmployeeProfitFunctionRefer']['employee_id'] = $empl['CompanyEmployeeReference']['employee_id'];
                        $this->ProjectEmployeeProfitFunctionRefer->save($this->data['ProjectEmployeeProfitFunctionRefer']);
                    }
                }
            }
        }
        $this->Session->setFlash(__('The employees has been created with the corresponding profit center!', true));
        $this->redirect("/employees/index");
    }

    protected function _getPath($company_id = null, $employeeId = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'avatar_employ' . DS;
        $path .= $company['Company']['dir'] . DS . $employeeId . DS;
        return $path;
    }

    /**
     * Update Avatar
     */
    public function update_avatar($company_id = null, $id = null, $check = 'true'){
        $this->layout = false;
        $result = '';
        if(!empty($_FILES)){
			if( !empty( $_FILES['file'])){
				$_FILES['FileField'] = array();
				$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
				unset($_FILES['file']);
			}
            $path = $this->_getPath($company_id, $id);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                        , true), $path), 'error');
            }
            if (!empty($attachment)) {
                /**
                 * Lay File Image Old And Delete
                 */
                $employee = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Employee.id' => $id),
					'fields' => array('avatar', 'avatar_resize', 'id', 'company_id', 'first_name', 'last_name', 'avatar_color')
                ));
                $oldDatas = array();
                if(!empty($employee) && $employee['Employee']['avatar']){
                    $imageSaves = $employee['Employee']['avatar'];
                    $oldImageBusiness = $employee['Employee']['avatar_resize'];
                    if(!empty($imageSaves) && is_file($path . $imageSaves)){
                        unlink($path . $imageSaves);
                    }
                    if(!empty($oldImageBusiness) && is_file($path . $oldImageBusiness)){
                        unlink($path . $oldImageBusiness);
                    }
                    $oldImages = $employee['Employee']['avatar'];
                    $info = pathinfo($oldImages);
                    $oldImages = explode('_resize_bk_', $oldImages);
                    if(!empty($oldImages)){
                        $oldImages = $oldImages[0] . '.' . $info['extension'];
                        if(!empty($oldImages) && is_file($path . $oldImages)){
                            unlink($path . $oldImages);
                        }
                    }
                    $oldDatas = array(
                        0 => array(
                            'path' => $path,
                            'file' => $imageSaves
                        ),
                        1 => array(
                            'path' => $path,
                            'file' => $oldImageBusiness
                        ),
                        2 => array(
                            'path' => $path,
                            'file' => $oldImages
                        )
                    );
                }
                $attachment = $attachment['attachment']['attachment'];
                $info = pathinfo($attachment);
                /**
                 * Resize 100px x 116px. Save Avatar for employee
                 */
                $newNameEmploy = basename($attachment, '.' . $info['extension']) . '_resize_bk_' . time() . '.' . $info['extension'];
                $this->PImage->resizeImage('resizeCrop', $attachment, $path, $newNameEmploy, 200, 200, 80);
				
                /**
                 * Resize 30px x 30px. Save Avatar for business Lead
                 */
                $newNameBusiness = basename($attachment, '.' . $info['extension']) . '_resize_bk_bus_' . time() . '.' . $info['extension'];
                $this->PImage->resizeImage('resizeCrop', $attachment, $path, $newNameBusiness, 50, 50, 80);
                $this->Employee->id = $id;
				//Quan update ticket #431. Date 22/08/2019. Set avatar_color = null, xoa database avatar_color khi update avatar.
                if ($this->Employee->save(array(
                    'avatar' => $newNameEmploy,
                    'avatar_resize' => $newNameBusiness,
                    'avatar_color' => ''
                    ))) {
                    $this->Session->write('Auth.employee_info.Employee.avatar_resize', $newNameBusiness);
                    $this->Session->write('Auth.employee_info.Employee.avatar', $newNameEmploy);
                    $this->Session->write('Auth.employee_info.Employee.avatar_color', '');
                    $this->employee_info['Employee']['avatar_resize'] = $newNameBusiness;
                    $employee['Employee']['avatar_resize'] = $newNameBusiness;
                    $this->employee_info['Employee']['avatar'] = $newNameEmploy;
                    $employee['Employee']['avatar'] = $newNameEmploy;
                    $result = $newNameEmploy;
                    $this->Session->setFlash(__('Saved', true), 'success');
                    if($this->MultiFileUpload->otherServer == true){
                        $datas = array(
                            0 => array(
                                'path' => $path,
                                'file' => $attachment
                            ),
                            1 => array(
                                'path' => $path,
                                'file' => $newNameEmploy
                            ),
                            2 => array(
                                'path' => $path,
                                'file' => $newNameBusiness
                            )
                        );
                        $redirect = '/employees/edit/' . $id . '/' . $company_id;
                        if($check == 'false'){
                            $redirect = '/employees/my_profile/';
                        }
                        $this->MultiFileUpload->uploadMultipleFileToServerOther($datas, $redirect, $oldDatas);
                    }
					// unlink($path . $attachment);
					$this->generateEmployeeAvatar($employee, true);
                } else {
                    unlink($path . $newNameEmploy);
                    unlink($path . $newNameBusiness);
                    unlink($path . $attachment);
                    if($this->MultiFileUpload->otherServer == true){
                        $datas = array(
                            0 => array(
                                'path' => $path,
                                'file' => $attachment
                            ),
                            1 => array(
                                'path' => $path,
                                'file' => $newNameEmploy
                            ),
                            2 => array(
                                'path' => $path,
                                'file' => $newNameBusiness
                            )
                        );
                        $redirect = '/employees/edit/' . $id . '/' . $company_id;
                        if($check == 'false'){
                            $redirect = '/employees/my_profile/';
                        }
                        $this->MultiFileUpload->deleteMultipleFileToServerOther($datas, $redirect);
                    }
                }
            } else {
                $this->Employee->id = $id;
                /**
                 * Lay File Image Old And Delete
                 */
                $employee = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Employee.id' => $id),
                    'fields' => array('avatar', 'avatar_resize', 'id', 'company_id', 'first_name', 'last_name', 'avatar_color')
                ));
                $oldDatas = array();
                if(!empty($employee) && $employee['Employee']['avatar']){
                    $imageSaves = $employee['Employee']['avatar'];
                    $oldImageBusiness = $employee['Employee']['avatar_resize'];
                    unlink($path . $imageSaves);
                    unlink($path . $oldImageBusiness);
                    $oldImages = $employee['Employee']['avatar'];
                    $info = pathinfo($oldImages);
                    $oldImages = explode('_resize_bk_', $oldImages);
                    if(!empty($oldImages)){
                        $oldImages = $oldImages[0] . '.' . $info['extension'];
                        if(!empty($oldImages)){
                            unlink($path . $oldImages);
                        }
                    }
                    $oldDatas = array(
                        0 => array(
                            'path' => $path,
                            'file' => $imageSaves
                        ),
                        1 => array(
                            'path' => $path,
                            'file' => $oldImageBusiness
                        ),
                        2 => array(
                            'path' => $path,
                            'file' => $oldImages
                        )
                    );
                }
                if($this->Employee->save(array('avatar' => '', 'avatar_resize' => ''))){
                    $result = '';
                    if($this->MultiFileUpload->otherServer == true){
                        $redirect = '/employees/edit/' . $id . '/' . $company_id;
                        if($check == 'false'){
                            $redirect = '/employees/my_profile/';
                        }
                        $this->MultiFileUpload->deleteMultipleFileToServerOther($oldDatas, $redirect);
                        
                    }
					
					$this->generateEmployeeAvatar($employee, true);
                }
            }
        }
        if($check == 'false'){
            $this->redirect(array('action' => 'my_profile'));
        }
        $this->redirect(array('action' => 'edit', $id, $company_id));
    }
	private function generateEmployeeAvatar($employee, $overwrite = false){
		// if( empty( $employee['Employee']['company_id']) ) return 0;
		$company_id = !empty($employee['Employee']['company_id']) ? $employee['Employee']['company_id'] : $this->employee_info['Company']['id'];
		$employee_id = $employee['Employee']['id'];
		$avatar = !empty($employee['Employee']['avatar']) ? $employee['Employee']['avatar'] : '';
		$company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
		$old_path = '';
		if( !empty ($avatar) && !empty($company_id)) $old_path = FILES . 'avatar_employ' . DS . $company['Company']['dir'] . DS . $employee_id . DS . $avatar;
		// $path = IMAGES . DS . $company_id . DS . $employee_id . DS;
		$new_path = IMAGES . 'avatar' . DS ;
		if (!file_exists($new_path )) {
			mkdir($new_path , 0777, true);
		}
		$count = 0;
		if( $old_path && file_exists($old_path)){
			try {
				App::import("vendor", "resize");
				//resize image for thumbnail login image
				$resize = new ResizeImage($old_path);
				$resize->resizeTo(200, 200, 'maxwidth');
				
				if( file_exists($new_path . $employee_id . '_avatar.png') && $overwrite) {
					@unlink($new_path . $employee_id . '_avatar.png');
				}
				if( !file_exists($new_path . $employee_id . '_avatar.png') ) {
					$resize->saveImage($new_path . $employee_id . '_avatar.png');
					$count++;
				}
				$resize->resizeTo(40, 40, 'maxwidth');
				if( file_exists($new_path . $employee_id . '.png') && $overwrite) {
					@unlink($new_path . $employee_id . '.png');
				}
				if( !file_exists($new_path . $employee_id . '.png') ){
					$resize->saveImage($new_path . $employee_id . '.png');
					$count++;
				}
			} catch (Exception $ex) {
				//wrong image, dont save
				@unlink($new_path . $employee_id . '_avatar.png');
				@unlink($new_path . $employee_id . '.png');
				die(json_encode(array(
					'status' => 'error',
					'hint' => __('Error when create avatar image for employee', true). ' ' .$employee_id,
				)));
			}	
		}else{// draw image
			if( !IMG_PNG ) return 0;
			$avatar_color = !empty( $employee['Employee']['avatar_color']) ? $employee['Employee']['avatar_color'] : '#6dabd4';
			$first_name = $employee['Employee']['first_name'];
			$last_name = $employee['Employee']['last_name'];
			$name_avatar = strtoupper( substr($first_name,0,1).substr($last_name,0,1) );
			if( empty( $name_avatar ) ) $name_avatar = 'AV';
			// header('Content-Type: image/png');
			foreach( array( '_avatar', '') as $type){
				list( $w, $h) = array( 40, 40);
				$font_size = 14;
				if( $type == '_avatar') { list( $w, $h) = array( 200, 200); $font_size = 72; }
				$im = imagecreatetruecolor($w, $h);
				// Create some colors
				list($r, $g, $b) = sscanf($avatar_color, "#%02x%02x%02x");
				$avt_backgr = imagecolorallocate($im, $r, $g, $b);
				imagefilledrectangle($im, 0, 0, $w-1, $h-1, $avt_backgr);
				$white = imagecolorallocate($im, 255, 255, 255);
				$font = APP. 'webroot' . DS . 'fonts'. DS . 'opensans'. DS . 'OpenSans-SemiBold.ttf';
				$bbox = imagettfbbox ($font_size , 0, $font, $name_avatar);
				$t_w = $bbox[2] - $bbox[0];
				$t_h = $bbox[5] - $bbox[3];
				// Add the text
				imagettftext($im, $font_size, 0, ($w/2 - $t_w/2), ($h - $t_h)/2, $white, $font, $name_avatar);
				// Using imagepng() results in clearer text compared with imagejpeg()
				try{
					imagepng($im, $new_path . $employee_id . $type . '.png', 3);
					$count++;
				}catch (Exception $ex){
					
				}
				imagedestroy($im);
			}
		}
		return $count;
	}

    /**
     * Update Group Information
     */
    public function update_group_infor(){
        $this->layout = false;
        $result = '';
        if($_POST){
            $this->Employee->id = $_POST['id'];
            unset($_POST['company_id']);
            unset($_POST['id']);
            foreach($_POST as $key => $values){
                $_POST[$key] = $this->LogSystem->cleanHttpString($values);
            }
            if($this->Employee->save($_POST)){
                $result = $_POST;
            }
        }
        echo json_encode($result);
        exit;
    }

    function history_filter() {
        if (!empty($this->data)) {
            extract($this->data);
            $path = rtrim($path, '/');
            $employId = $this->Session->read('Auth.Employee.id');
            $employId = isset($employId) ? $employId : null;
            $last = $this->Employee->HistoryFilter->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'id', 'params'
                ),
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $employId
                    //'employee_id' => $this->employee_info['Employee']['id']
                    )));
            $_params = array();

            $this->Employee->HistoryFilter->create();
            if (!empty($last)) {
                $this->Employee->HistoryFilter->id = $last['HistoryFilter']['id'];
                if (!empty($last['HistoryFilter']['params'])) {
                    $_params = (array) @unserialize($last['HistoryFilter']['params']);
                }
                unset($last);
            }
            if (empty($params)) {
                Configure::write('debug', 0);
                echo json_encode($_params);
                exit();
            } else {
                $params = @unserialize($params);
            }
            if($params != '{}'){
                $params = serialize(array_merge($_params, $params));
            }

            $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => $params,
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false));
        }
        exit();
    }

    public function profile($company_id = null){
        $this->loadModels('Company', 'Profile', 'ProfileValue');
        $profiles = $profileValues = array();
        $lastYear = date('Y', time()) - 1;
        $nextYear = date('Y', time()) + 5;
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
            $this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $profiles = $this->Profile->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name', 'capacity_by_year')
            ));
            $profileIds = !empty($profiles) ? Set::classicExtract($profiles, '{n}.Profile.id') : array();
            $profileValues = $this->ProfileValue->find('list', array(
                'recursive' => -1,
                'conditions' => array('profile_id' => $profileIds, 'year BETWEEN ? AND ?' => array($lastYear, $nextYear)),
                'fields' => array('year', 'value', 'profile_id'),
                'group' => array('profile_id', 'year')
            ));
			$this->set(compact('company_id', 'companyName', 'profiles', 'profileValues', 'lastYear', 'nextYear'));
        }
    }

    public function update_profile(){
        $this->loadModels('Company', 'Profile', 'ProfileValue');
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $datas = $this->data;
            $profiles = array(
                'id' => $datas['id'],
                'company_id' => $datas['company_id'],
                'name' => $datas['name'],
                'capacity_by_year' => $datas['capacity_by_year']
            );
            unset($datas['id']);
            unset($datas['company_id']);
            unset($datas['name']);
            unset($datas['capacity_by_year']);
            $this->Profile->create();
            if (!empty($profiles['id'])) {
                $this->Profile->id = $profiles['id'];
            }
            unset($profiles['id']);
            $profile_id = 0;
            if($this->Profile->save($profiles)){
                $profile_id = $this->Profile->id;
                if(!empty($datas)){
                    foreach($datas as $key => $val){
                        $key = str_replace('year_', '', $key);
                        if(!empty($datas)){
                            foreach($datas as $key => $val){
                                $year = str_replace('year_', '', $key);
                                $last = $this->ProfileValue->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'profile_id' => $profile_id,
                                        'year' => $year
                                    ),
                                    'fields' => array('id')
                                ));
                                if(!empty($val)){
                                    $this->ProfileValue->create();
                                    if(!empty($last) && !empty($last['ProfileValue']['id'])){
                                        $this->ProfileValue->id = $last['ProfileValue']['id'];
                                    }
                                    $_data = array('profile_id' => $profile_id, 'year' => $year, 'value' => $val);
                                    $this->ProfileValue->save($_data);
                                } else {
                                    if(!empty($last) && !empty($last['ProfileValue']['id'])){
                                        $this->ProfileValue->delete($last['ProfileValue']['id']);
                                    }
                                }
                            }
                        }
                    }
                }
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $profile_id = $this->data['id'];
                $this->Session->setFlash(__('The Profile could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $profile_id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete_profile($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Profile', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'profile', $company_id));
        }
        $this->loadModels('Profile', 'ProfileValue');
		if((!$this->is_sas) && (!$this->_isBelongToCompany($id, 'Profile'))){
			$this->_functionStop(false, $id, __('You have not permission to access this function', true), false, array('controller' => 'employees', 'action' => 'profile', $company_id));
		}
        if ($this->Profile->delete($id)) {
            $this->ProfileValue->deleteAll(array('ProfileValue.profile_id' => $id), false);
            $this->Session->setFlash(__('Deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Profile was not deleted', true), 'error');
        }
        $this->redirect(array('controller' => 'employees', 'action' => 'profile', $company_id));
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

    function check_code_id($code_id) {
        $this->autoRender = false;
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $this->CompanyEmployeeReference->Behaviors->attach('Containable');
        $check = '';
        if ($company_id != "") {
            $check = $this->Employee->CompanyEmployeeReference->find('all', array(
                'fields' => array('company_id'),
                'contain' => array('Employee' => array('code_id')),
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $company_id,
                    'Employee.code_id LIKE' => $code_id),
                'recursive' => 0
                    ));
        } else {
            $check = $this->Employee->CompanyEmployeeReference->find('all', array(
                'fields' => array('company_id'),
                'contain' => array('Employee' => array('code_id')),
                'conditions' => array('Employee.code_id LIKE' => $code_id),
                'recursive' => 0
                    ));
        }
        if (!empty($check)) {
            echo "1";
        }
        exit;
    }
    /**
     * Co nhung employee da bi xoa khoi he thong, nhung he thong van con
     * luu giu cac van de, cac du lieu lien quan de employee do.
     * Ham nay dung de: tim cac du lieu con ton dong do. Va xoa khoi he thong.
     *
     */
     public function findDataOfEmployeeHaveDeleted(){
        $this->loadModel('AbsenceComment');
        $this->loadModel('AbsenceRequestConfirm');
        $this->loadModel('AbsenceRequest');
        //Total Employee
        $marginCenter = 'style="margin: 0 auto; width: 500px;"';
        $boldFont = 'style="font-weight: bold; font-size: 20px; color: blue;"';
        $borderTD = 'style="border: 1px solid grey;"';
        echo '<h1 '.$marginCenter.'>Report Employees</h1>';
        echo '<h3>Model: Employee. And Table: employees</h3>';
        $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id')
            ));
        echo 'Total: <span '.$boldFont.'>' . count($employees) . '</span> Employee.';
        echo '<div style="border: 2px solid black; width: 1024px; margin: 0 auto;">';
        echo '<h3>Total tables(models) existing data, related to Employee which were deleted.</h3>';
        // Absence Comment
        echo '<h4>Model: AbsenceComment. And Table: absence_comments</h4>';
        $absenceComments = $this->AbsenceComment->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($absenceComments)){
            echo 'Total: <span '.$boldFont.'>' . count($absenceComments) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($absenceComments as $absenceComment){
                $dx = $absenceComment['AbsenceComment'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->AbsenceComment->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Absence Request Confirm
        echo '<h4>Model: AbsenceRequestConfirm. And Table: absence_request_confirms</h4>';
        $absenceRequestConfirms = $this->AbsenceRequestConfirm->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($absenceRequestConfirms)){
            echo 'Total: <span '.$boldFont.'>' . count($absenceRequestConfirms) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($absenceRequestConfirms as $absenceRequestConfirm){
                $dx = $absenceRequestConfirm['AbsenceRequestConfirm'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->AbsenceRequestConfirm->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Absence Request
        echo '<h4>Model: AbsenceRequest. And Table: absence_requests</h4>';
        $absenceRequests = $this->AbsenceRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($absenceRequests)){
            echo 'Total: <span '.$boldFont.'>' . count($absenceRequests) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($absenceRequests as $absenceRequest){
                $dx = $absenceRequest['AbsenceRequest'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->AbsenceRequest->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Activity Comment
        $this->loadModel('ActivityComment');
        echo '<h4>Model: ActivityComment. And Table: activity_comments</h4>';
        $activityComments = $this->ActivityComment->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($activityComments)){
            echo 'Total: <span '.$boldFont.'>' . count($activityComments) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($activityComment as $activityComment){
                $dx = $activityComment['ActivityComment'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ActivityComment->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Activity Forecast
        $this->loadModel('ActivityForecast');
        echo '<h4>Model: ActivityForecast. And Table: activity_forecasts</h4>';
        $activityForecasts = $this->ActivityForecast->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($activityForecasts)){
            echo 'Total: <span '.$boldFont.'>' . count($activityForecasts) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($activityForecasts as $activityForecast){
                $dx = $activityForecast['ActivityForecast'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ActivityForecast->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Activity Request Confirm
        $this->loadModel('ActivityRequestConfirm');
        echo '<h4>Model: ActivityRequestConfirm. And Table: activity_request_confirms</h4>';
        $activityRequestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($activityRequestConfirms)){
            echo 'Total: <span '.$boldFont.'>' . count($activityRequestConfirms) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($activityRequestConfirms as $activityRequestConfirm){
                $dx = $activityRequestConfirm['ActivityRequestConfirm'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ActivityRequestConfirm->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Activity Request
        $this->loadModel('ActivityRequest');
        echo '<h4>Model: ActivityRequest. And Table: activity_requests</h4>';
        $activityRequests = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($activityRequests)){
            echo 'Total: <span '.$boldFont.'>' . count($activityRequests) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($activityRequests as $activityRequest){
                $dx = $activityRequest['ActivityRequest'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ActivityRequest->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Activity Task Employee Refer
        $this->loadModel('ActivityTaskEmployeeRefer');
        echo '<h4>Model: ActivityTaskEmployeeRefer. And Table: activity_task_employee_refers</h4>';
        $activityTaskEmployeeRefers = $this->ActivityTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('reference_id' => $employees),
                    'is_profit_center' => 0
                )
            ));
        if(!empty($activityTaskEmployeeRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($activityTaskEmployeeRefers) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($activityTaskEmployeeRefers as $activityTaskEmployeeRefer){
                $dx = $activityTaskEmployeeRefer['ActivityTaskEmployeeRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['reference_id']. '</td>';
                echo '</tr>';
                $this->ActivityTaskEmployeeRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Company Employee Reference
        $this->loadModel('CompanyEmployeeReference');
        echo '<h4>Model: CompanyEmployeeReference. And Table: company_employee_references</h4>';
        $companyEmployeeReferences = $this->CompanyEmployeeReference->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($companyEmployeeReferences)){
            echo 'Total: <span '.$boldFont.'>' . count($companyEmployeeReferences) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($companyEmployeeReferences as $companyEmployeeReference){
                $dx = $companyEmployeeReference['CompanyEmployeeReference'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->CompanyEmployeeReference->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Employee Absence
        $this->loadModel('EmployeeAbsence');
        echo '<h4>Model: EmployeeAbsence. And Table: employee_absences</h4>';
        $employeeAbsences = $this->EmployeeAbsence->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($employeeAbsences)){
            echo 'Total: <span '.$boldFont.'>' . count($employeeAbsences) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($employeeAbsences as $employeeAbsence){
                $dx = $employeeAbsence['EmployeeAbsence'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->EmployeeAbsence->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Employee Absence
        $this->loadModel('EmployeeAbsence');
        echo '<h4>Model: EmployeeAbsence. And Table: employee_absences</h4>';
        $employeeAbsences = $this->EmployeeAbsence->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($employeeAbsences)){
            echo 'Total: <span '.$boldFont.'>' . count($employeeAbsences) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($employeeAbsences as $employeeAbsence){
                $dx = $employeeAbsence['EmployeeAbsence'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->EmployeeAbsence->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Employee Profit Function Refer
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        echo '<h4>Model: ProjectEmployeeProfitFunctionRefer. And Table: project_employee_profit_function_refers</h4>';
        $projectEmployeeProfitFunctionRefers = $this->ProjectEmployeeProfitFunctionRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($projectEmployeeProfitFunctionRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($projectEmployeeProfitFunctionRefers) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectEmployeeProfitFunctionRefers as $projectEmployeeProfitFunctionRefer){
                $dx = $projectEmployeeProfitFunctionRefer['ProjectEmployeeProfitFunctionRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ProjectEmployeeProfitFunctionRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Function Employee Refer
        $this->loadModel('ProjectFunctionEmployeeRefer');
        echo '<h4>Model: ProjectFunctionEmployeeRefer. And Table: project_function_employee_refers</h4>';
        $projectFunctionEmployeeRefers = $this->ProjectFunctionEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($projectFunctionEmployeeRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($projectFunctionEmployeeRefers) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectFunctionEmployeeRefers as $projectFunctionEmployeeRefer){
                $dx = $projectFunctionEmployeeRefer['ProjectFunctionEmployeeRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ProjectFunctionEmployeeRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Issue
        $this->loadModel('ProjectIssue');
        echo '<h4>Model: ProjectIssue. And Table: project_issues</h4>';
        $projectIssues = $this->ProjectIssue->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('issue_assign_to' => $employees)
                )
            ));
        if(!empty($projectIssues)){
            echo 'Total: <span '.$boldFont.'>' . count($projectIssues) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectIssues as $projectIssue){
                $dx = $projectIssue['ProjectIssue'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['issue_assign_to']. '</td>';
                echo '</tr>';
                $this->ProjectIssue->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Livrable Actor
        $this->loadModel('ProjectLivrableActor');
        echo '<h4>Model: ProjectLivrableActor. And Table: project_livrable_actors</h4>';
        $projectLivrableActors = $this->ProjectLivrableActor->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($projectLivrableActors)){
            echo 'Total: <span '.$boldFont.'>' . count($projectLivrableActors) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectLivrableActors as $projectLivrableActor){
                $dx = $projectLivrableActor['ProjectLivrableActor'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ProjectLivrableActor->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Risk
        $this->loadModel('ProjectRisk');
        echo '<h4>Model: ProjectRisk. And Table: project_risks</h4>';
        $projectRisks = $this->ProjectRisk->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('risk_assign_to' => $employees)
                )
            ));
        if(!empty($projectRisks)){
            echo 'Total: <span '.$boldFont.'>' . count($projectRisks) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectRisks as $projectRisk){
                $dx = $projectRisk['ProjectRisk'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['risk_assign_to']. '</td>';
                echo '</tr>';
                $this->ProjectRisk->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Task Employee Refer
        $this->loadModel('ProjectTaskEmployeeRefer');
        echo '<h4>Model: ProjectTaskEmployeeRefer. And Table: project_task_employee_refers</h4>';
        $projectTaskEmployeeRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('reference_id' => $employees),
                    'is_profit_center' => 0
                )
            ));
        if(!empty($projectTaskEmployeeRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($projectTaskEmployeeRefers) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectTaskEmployeeRefers as $projectTaskEmployeeRefer){
                $dx = $projectTaskEmployeeRefer['ProjectTaskEmployeeRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['reference_id']. '</td>';
                echo '</tr>';
                $this->ProjectTaskEmployeeRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project Team
        $this->loadModel('ProjectTeam');
        echo '<h4>Model: ProjectTeam. And Table: project_teams</h4>';
        $projectTeams = $this->ProjectTeam->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($projectTeams)){
            foreach($projectTeams as $key => $projectTeam){
                if($projectTeam['ProjectTeam']['employee_id'] == 0 || $projectTeam['ProjectTeam']['employee_id'] == ''){
                    unset($projectTeams[$key]);
                }
            }
            echo 'Total: <span '.$boldFont.'>' . count($projectTeams) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projectTeams as $projectTeam){
                $dx = $projectTeam['ProjectTeam'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->ProjectTeam->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // Project
        $this->loadModel('Project');
        echo '<h4>Model: Project. And Table: projects</h4>';
        $projects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('project_manager_id' => $employees)
                )
            ));
        if(!empty($projects)){
            echo 'Total: <span '.$boldFont.'>' . count($projects) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($projects as $project){
                $dx = $project['Project'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['project_manager_id']. '</td>';
                echo '</tr>';
                $this->Project->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // User Default View
        $this->loadModel('UserDefaultView');
        echo '<h4>Model: UserDefaultView. And Table: user_default_views</h4>';
        $userDefaultViews = $this->UserDefaultView->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($userDefaultViews)){
            echo 'Total: <span '.$boldFont.'>' . count($userDefaultViews) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($userDefaultViews as $userDefaultView){
                $dx = $userDefaultView['UserDefaultView'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->UserDefaultView->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}
        // User View
        $this->loadModel('UserView');
        echo '<h4>Model: UserView. And Table: user_views</h4>';
        $userViews = $this->UserView->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('employee_id' => $employees)
                )
            ));
        if(!empty($userViews)){
            echo 'Total: <span '.$boldFont.'>' . count($userViews) . ' record related to Employee which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Employee ID</td>';
            echo '</tr>';
            foreach($userViews as $userView){
                $dx = $userView['UserView'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['employee_id']. '</td>';
                echo '</tr>';
                $this->UserView->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        } else {echo 'Not data!';}

        echo '</div>';
        exit;
     }

     /**
      * Kiem tra cac profit center da xoa khoi he thong.
      * Nhung nhung~ thong tin lien quan de profit center van con ton tai trong he thong.
      *
      */
     public function findDateOfProfitCenterHaveDeleted(){
        $this->loadModel('ProfitCenter');
        $this->loadModel('ActivityProfitRefer');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectTeam');
        //Total Profit Center
        $marginCenter = 'style="margin: 0 auto; width: 500px;"';
        $boldFont = 'style="font-weight: bold; font-size: 20px; color: blue;"';
        $borderTD = 'style="border: 1px solid grey;"';
        echo '<h1 '.$marginCenter.'>Report Profit Centers</h1>';
        $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id')
            ));
        echo '<h3>Model: ProfitCenter. And Table: profit_centers</h3>';
        echo 'Total: <span '.$boldFont.'>' . count($profitCenters) . '</span> Profit Center.';
        echo '<div style="border: 2px solid black; width: 1024px; margin: 0 auto;">';
        echo '<h3>Total tables(models) existing data, related to Profit Center which were deleted.</h3>';
        // Activity Task Employee Refer
        echo '<h4>Model: ActivityTaskEmployeeRefer. And Table: activity_task_employee_refers</h4>';
        $activityTaskEmployeeRefers = $this->ActivityTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('reference_id' => $profitCenters),
                    'is_profit_center' => 1
                )
            ));
        if(!empty($activityTaskEmployeeRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($activityTaskEmployeeRefers) . ' record related to Profit Center which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Profit Center ID</td><td '.$borderTD.'>Activity Task ID</td>';
            echo '</tr>';
            foreach($activityTaskEmployeeRefers as $activityTaskEmployeeRefer){
                $dx = $activityTaskEmployeeRefer['ActivityTaskEmployeeRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['reference_id']. '</td>
                    <td '.$borderTD.'>' .$dx['activity_task_id']. '</td>';
                echo '</tr>';
                $this->ActivityTaskEmployeeRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        }
        // Project Employee Profit Function Refer
        echo '<h4>Model: ProjectEmployeeProfitFunctionRefer. And Table: project_employee_profit_function_refers</h4>';
        $projectEmployeeProfitFunctionRefers = $this->ProjectEmployeeProfitFunctionRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => $profitCenters)
                )
            ));
        if(!empty($projectEmployeeProfitFunctionRefers)){
            foreach($projectEmployeeProfitFunctionRefers as $key => $projectEmployeeProfitFunctionRefer){
                if($projectEmployeeProfitFunctionRefer['ProjectEmployeeProfitFunctionRefer']['profit_center_id'] == 0 || $projectEmployeeProfitFunctionRefer['ProjectEmployeeProfitFunctionRefer']['profit_center_id'] == ''){
                    unset($projectEmployeeProfitFunctionRefers[$key]);
                }
            }
            echo 'Total: <span '.$boldFont.'>' . count($projectEmployeeProfitFunctionRefers) . ' record related to Profit Center which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Profit Center ID</td>';
            echo '</tr>';
            foreach($projectEmployeeProfitFunctionRefers as $projectEmployeeProfitFunctionRefer){
                $dx = $projectEmployeeProfitFunctionRefer['ProjectEmployeeProfitFunctionRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['profit_center_id']. '</td>';
                echo '</tr>';
                $this->ProjectEmployeeProfitFunctionRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        }
        // Project Function Employee Refer
        echo '<h4>Model: ProjectFunctionEmployeeRefer. And Table: project_function_employee_refers</h4>';
        $projectFunctionEmployeeRefers = $this->ProjectFunctionEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => $profitCenters)
                )
            ));
        if(!empty($projectFunctionEmployeeRefers)){
            foreach($projectFunctionEmployeeRefers as $key => $projectFunctionEmployeeRefer){
                if($projectFunctionEmployeeRefer['ProjectFunctionEmployeeRefer']['profit_center_id'] == 0 || $projectFunctionEmployeeRefer['ProjectFunctionEmployeeRefer']['profit_center_id'] == ''){
                    unset($projectFunctionEmployeeRefers[$key]);
                }
            }
            echo 'Total: <span '.$boldFont.'>' . count($projectFunctionEmployeeRefers) . ' record related to Profit Center which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Profit Center ID</td><td '.$borderTD.'>Function ID</td>';
            echo '</tr>';
            foreach($projectFunctionEmployeeRefers as $projectFunctionEmployeeRefer){
                $dx = $projectFunctionEmployeeRefer['ProjectFunctionEmployeeRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['profit_center_id']. '</td>
                    <td '.$borderTD.'>' .$dx['function_id']. '</td>';
                echo '</tr>';
                $this->ProjectFunctionEmployeeRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        }
        // Project Task Employee Refer
        echo '<h4>Model: ProjectTaskEmployeeRefer. And Table: project_task_employee_refers</h4>';
        $projectTaskEmployeeRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('reference_id' => $profitCenters),
                    'is_profit_center' => 1
                )
            ));
        if(!empty($projectTaskEmployeeRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($projectTaskEmployeeRefers) . ' record related to Profit Center which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Profit Center ID</td><td '.$borderTD.'>Project Task ID</td>';
            echo '</tr>';
            foreach($projectTaskEmployeeRefers as $projectTaskEmployeeRefer){
                $dx = $projectTaskEmployeeRefer['ProjectTaskEmployeeRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['reference_id']. '</td>
                    <td '.$borderTD.'>' .$dx['project_task_id']. '</td>';
                echo '</tr>';
                $this->ProjectTaskEmployeeRefer->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        }
        // Project Team
        echo '<h4>Model: ProjectTeam. And Table: project_teams</h4>';
        $projectTeams = $this->ProjectTeam->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => $profitCenters)
                )
            ));
        if(!empty($projectTeams)){
            foreach($projectTeams as $key => $projectTeam){
                if($projectTeam['ProjectTeam']['profit_center_id'] == 0 || $projectTeam['ProjectTeam']['profit_center_id'] == ''){
                    unset($projectTeams[$key]);
                }
            }
            echo 'Total: <span '.$boldFont.'>' . count($projectTeams) . ' record related to Profit Center which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Profit Center ID</td><td '.$borderTD.'>Project ID</td>';
            echo '</tr>';
            foreach($projectTeams as $projectTeam){
                $dx = $projectTeam['ProjectTeam'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['profit_center_id']. '</td>
                    <td '.$borderTD.'>' .$dx['project_id']. '</td>';
                echo '</tr>';
                $this->ProjectTeam->delete($dx['id']);
            }
            echo '</table>';
            echo '<hr style="border: 1px solid black;"/>';
        }
        // Activity Profit Refers
        echo '<h4>Model: ActivityProfitRefer. And Table: activity_profit_refers</h4>';
        $activityProfitRefers = $this->ActivityProfitRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => $profitCenters)
                )
            ));
        if(!empty($activityProfitRefers)){
            echo 'Total: <span '.$boldFont.'>' . count($activityProfitRefers) . ' record related to Profit Center which were deleted.</span>';
            echo '<table style="width: 800px; margin: 10px auto;" cellspacing="0" cellpadding="0">';
            echo '<tr style="background-color: #CDCDD1; color: white;">';
            echo '<td '.$borderTD.'>ID</td><td '.$borderTD.'>Profit Center ID</td><td '.$borderTD.'>Activity ID</td>';
            echo '</tr>';
            foreach($activityProfitRefers as $activityProfitRefer){
                $dx = $activityProfitRefer['ActivityProfitRefer'];
                echo '<tr>';
                echo
                    '<td '.$borderTD.'>' .$dx['id']. '</td>
                    <td '.$borderTD.'>' .$dx['profit_center_id']. '</td>
                    <td '.$borderTD.'>' .$dx['activity_id']. '</td>';
                echo '</tr>';
                $this->ActivityProfitRefer->delete($dx['id']);
            }
            echo '</table>';
        }
        echo '</div>';
        exit;
    }
    /**
     * Update Data Multi Resource
     */
    public function update_multi_resource(){
        $this->loadModel('EmployeeMultiResource');
        if(!empty($_POST)){
            $data = $this->EmployeeMultiResource->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $_POST['id']
                ),
                'fields' => array('id', 'date', 'value', 'by_day')
            ));
            $data = Set::combine($data, '{n}.EmployeeMultiResource.date', '{n}.EmployeeMultiResource');
            $start = $_POST['start'];
            while ($start <= $_POST['end']) {
                if(strtolower(date('l', $start)) == 'saturday' || strtolower(date('l', $start)) == 'sunday'){
                    //do nothing
                }else{
                    if(!empty($data[$start])){
                        $saved = array(
                            'value' => $_POST['by_day'],
                            'by_day' => $_POST['by_day'],
                        );
                        $this->EmployeeMultiResource->id = $data[$start]['id'];
                    }else{
                        $saved = array(
                            'date' => $start,
                            'value' => $_POST['by_day'],
                            'employee_id' => $_POST['id'],
                            'by_day' => $_POST['by_day'],
                        );
                        $this->EmployeeMultiResource->create();
                    }
                    $this->EmployeeMultiResource->save($saved);
                }
                $start = mktime(0, 0, 0, date('m', $start), date('d', $start)+1, date('Y', $start));
            }
            die;
        }
        die;
    }
    /*
    * Update data multi resource for date
    */
    public function update_value_multi_resource(){
        $this->loadModel('EmployeeMultiResource');
        if(!empty($_POST)){
            $data = $this->EmployeeMultiResource->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $_POST['id'],
                    'date' => $_POST['date']
                ),
                'fields' => array('id','value')
            ));
            if(!empty($data)){
                $data['EmployeeMultiResource']['value'] = $_POST['value'];
                $this->EmployeeMultiResource->save($data);
            }else{
                $saved = array(
                    'date' => $_POST['date'],
                    'employee_id' => $_POST['id'],
                    'value' => $_POST['value']
                );
                $this->EmployeeMultiResource->create();
                $this->EmployeeMultiResource->save($saved);
            }
            die;
        }
        die;
    }
    public function checkStartDate(){
        $this->loadModel('EmployeeMultiResource');
        if(!empty($_POST)){
            $data = $this->EmployeeMultiResource->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $_POST['id']
                ),
                'fields' => array('date','value')
            ));
            if( !empty($data) ){
                foreach ($data as $key => $value) {
                    if(($key < strtotime($_POST['date'])) && $value != '' && $value != 0){
                        die('true');
                    }
                }
            }
        }
        die('false');
    }
    public function checkEndDate(){
        $this->loadModel('EmployeeMultiResource');
        if(!empty($_POST)){
            $data = $this->EmployeeMultiResource->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $_POST['id']
                ),
                'fields' => array('date','value')
            ));
            if( !empty($data) ){
                foreach ($data as $key => $value) {
                    if(($key > strtotime($_POST['date'])) && $value != '' && $value != 0 ){
                        die('true');
                    }
                }
            }
        }
        die('false');
    }
    public function checkChangeEndDate(){
        $this->loadModels('ProjectTaskEmployeeRefer', 'AbsenceRequest', 'ActivityTask', 'ActivityTaskEmployeeRefer', 'ProjectTask');
        $result = true;
        if(!empty($_POST)){
            $date = !empty($_POST['date']) ? strtotime($_POST['date']) : 0;
            $date1 = !empty($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : '0000-00-00';
            if($date != 0){
                $data_activity = $this->ActivityTaskEmployeeRefer->find('count', array(
                    'conditions' => array(
                        'ActivityTaskEmployeeRefer.reference_id' => $_POST['id'], // id cua employee
                        'ActivityTaskEmployeeRefer.is_profit_center' => 0,
                        'ActivityTask.task_end_date >' => $date
                    )
                ));
                $data_project = $this->ProjectTaskEmployeeRefer->find('count', array(
                    'conditions' => array(
                        'ProjectTaskEmployeeRefer.reference_id' => $_POST['id'], // id cua employee
                        'ProjectTaskEmployeeRefer.is_profit_center' => 0,
                        'ProjectTask.task_end_date >' => $date1
                    )
                ));
                $data_absence = $this->AbsenceRequest->find('all', array(
                    'conditions' => array(
                        'employee_id' => $_POST['id'],
                        'date >' => $date
                    )
                ));
                if($data_activity != 0 || $data_project != 0 || $data_absence != 0 ){
                    $result = false;
                    //khong cho thay doi
                }
            } else {
                // cho thay doi thoai mai
            }
        }
        echo json_encode($result);
        exit;
    }
    public function getChangeEndDate(){
        $this->loadModels('ProjectTaskEmployeeRefer', 'AbsenceRequest', 'ActivityTask', 'ActivityTaskEmployeeRefer', 'ProjectTask');
        $data_activity = $data_project = $data_absence = array();
        if(!empty($_POST)){
            $date = !empty($_POST['date']) ? strtotime($_POST['date']) : 0;
            $date1 = !empty($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : '0000-00-00';
            if($date != 0){
                $data_activity = $this->ActivityTaskEmployeeRefer->find('all', array(
                    'conditions' => array(
                        'ActivityTaskEmployeeRefer.reference_id' => $_POST['id'], // id cua employee
                        'ActivityTaskEmployeeRefer.is_profit_center' => 0,
                        'ActivityTask.task_end_date >' => $date
                    )
                ));
                $data_project = $this->ProjectTaskEmployeeRefer->find('all', array(
                    'conditions' => array(
                        'ProjectTaskEmployeeRefer.reference_id' => $_POST['id'], // id cua employee
                        'ProjectTaskEmployeeRefer.is_profit_center' => 0,
                        'ProjectTask.task_end_date >' => $date1
                    )
                ));
                $data_absence = $this->AbsenceRequest->find('all', array(
                    'conditions' => array(
                        'employee_id' => $_POST['id'],
                        'date >' => $date
                    )
                ));
            }
        }
        $data['activities'] = $data_activity;
        $data['projects'] = $data_project;
        $data['absences'] = $data_absence;
        echo json_encode($data);
        exit;
    }
	public function updateEmpAnonymous(){
		// $company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : 0;
		$idEmpEdit = !empty($_POST['employee_id']) ? $_POST['employee_id'] : 0;
		$anonymous = !empty($_POST['anonymous']) ? $_POST['anonymous'] : 0;
		$dataEmpAnony = array();
		$company_id = $this->employee_info['Company']['id'];
		$canManagerResource = ($this->employee_info['Role']['name'] == 'admin') || ( $this->employee_info['CompanyEmployeeReference']['control_resource'] == '1');
		if((!$canManagerResource) || (!$this->_isBelongToCompany($idEmpEdit, 'Employee'))){
			$this->_functionStop(false, $_POST, __('You have not permission to access this function', true), false, array('action' => 'index'));
		}
		if($company_id != 0 && $idEmpEdit != 0 && $anonymous != 0){
			$default_user_profile = $this->Employee->default_user_profile($company_id);
			$infoEmpEdit = $this->Employee->find('first', array(
				'fields' => array('id', 'first_name', 'last_name', 'email', 'work_phone'),
				'recursive' => -1,
				'conditions' => array('Employee.id' => $idEmpEdit)
			));
			$dataEmpAnony['id'] = $idEmpEdit;
			$dataEmpAnony['first_name'] = 'AVA-' . (10000 + $infoEmpEdit['Employee']['id']);
			$dataEmpAnony['last_name'] = 'AVA-' . (50000 + $infoEmpEdit['Employee']['id']);
			$dataEmpAnony['email'] = $dataEmpAnony['last_name'] . $default_user_profile['domain_name'];
			$dataEmpAnony['work_phone'] = '';
			$dataEmpAnony['anonymous'] = $anonymous;
			
			$this->deleteAvatarAnonymous($company_id, $dataEmpAnony);
			$this->Employee->save($dataEmpAnony);
		}elseif($company_id != 0 && $idEmpEdit != 0 && $anonymous == 0){
			$dataEmpAnony['id'] = $idEmpEdit;
			$dataEmpAnony['anonymous'] = $anonymous;
			$this->Employee->save($dataEmpAnony);
		}
		echo json_encode($dataEmpAnony);
        exit;
	}
	public function deleteAvatarAnonymous($company_id, $dataEmpAnony){
		$company_id = !empty($company_id) ? $company_id : 0;
		$idEmpEdit = !empty($dataEmpAnony) ? $dataEmpAnony['id'] : 0;
		$path = $this->_getPath($company_id, $idEmpEdit);
		App::import('Core', 'Folder');
		new Folder($path, true, 0777);
		$this->MultiFileUpload->encode_filename = false;
		$this->MultiFileUpload->uploadpath = $path;
		$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png";
		$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
		
		$employee = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $idEmpEdit),
			'fields' => array('avatar', 'avatar_resize', 'id', 'company_id', 'first_name', 'last_name', 'avatar_color')
		));
		
		if(!empty($employee)){
			if(!empty($employee['Employee']['avatar'])){
				$imageSaves = $employee['Employee']['avatar'];
				$oldImageBusiness = $employee['Employee']['avatar_resize'];
				if(!empty($imageSaves) && is_file($path . $imageSaves)){
					unlink($path . $imageSaves);
				}
				if(!empty($oldImageBusiness) && is_file($path . $oldImageBusiness)){
					unlink($path . $oldImageBusiness);
				}
				$oldImages = $employee['Employee']['avatar'];
				$info = pathinfo($oldImages);
				$oldImages = explode('_resize_bk_', $oldImages);
				if(!empty($oldImages)){
					$oldImages = $oldImages[0] . '.' . $info['extension'];
					if(!empty($oldImages) && is_file($path . $oldImages)){
						unlink($path . $oldImages);
					}
				}
			}
			$new_path = IMAGES . 'avatar' . DS ;
			if (!file_exists($new_path )) {
				mkdir($new_path , 0777, true);
			}
			if( file_exists($new_path . $idEmpEdit . '_avatar.png')) {
				@unlink($new_path . $idEmpEdit . '_avatar.png');
			}
			if( file_exists($new_path . $idEmpEdit . '.png')) {
				@unlink($new_path . $idEmpEdit . '.png');
			}
			
			if( !IMG_PNG ) return 0;
			$avatar_color = !empty( $employee['Employee']['avatar_color']) ? $employee['Employee']['avatar_color'] : '#6dabd4';
			$first_name = $dataEmpAnony['first_name'];
			$last_name = $dataEmpAnony['last_name'];
			$name_avatar = strtoupper( substr($first_name,0,1).substr($last_name,0,1) );
			if( empty( $name_avatar ) ) $name_avatar = 'AV';
			// header('Content-Type: image/png');
			foreach( array( '_avatar', '') as $type){
				list( $w, $h) = array( 40, 40);
				$font_size = 14;
				if( $type == '_avatar') { list( $w, $h) = array( 200, 200); $font_size = 72; }
				$im = imagecreatetruecolor($w, $h);
				// Create some colors
				list($r, $g, $b) = sscanf($avatar_color, "#%02x%02x%02x");
				$avt_backgr = imagecolorallocate($im, $r, $g, $b);
				imagefilledrectangle($im, 0, 0, $w-1, $h-1, $avt_backgr);
				$white = imagecolorallocate($im, 255, 255, 255);
				$font = APP. 'webroot' . DS . 'fonts'. DS . 'opensans'. DS . 'OpenSans-SemiBold.ttf';
				$bbox = imagettfbbox ($font_size , 0, $font, $name_avatar);
				$t_w = $bbox[2] - $bbox[0];
				$t_h = $bbox[5] - $bbox[3];
				// Add the text
				imagettftext($im, $font_size, 0, ($w/2 - $t_w/2), ($h - $t_h)/2, $white, $font, $name_avatar);
				// Using imagepng() results in clearer text compared with imagejpeg()
				try{
					imagepng($im, $new_path . $idEmpEdit . $type . '.png', 3);
				}catch (Exception $ex){
					
				}
				imagedestroy($im);
			}
			
		}
		return;		
	}
}