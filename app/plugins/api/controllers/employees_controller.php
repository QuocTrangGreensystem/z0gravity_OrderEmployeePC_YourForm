<?php
class EmployeesController extends ApiAppController {
    // some components inherited from AppController (app_controller.php) so do not include them here
    public $components = array('Api.ZAuth');

    public $uses = array('Employee');

    protected $roles = array(2, 3, 4);

    public function beforeFilter() {
        parent::beforeFilter();
        // $this->ZAuth->addValidator('afterAuthValidation', array($this, 'validateIP'));
    }

	public function login(){
        if( $this->RequestHandler->isPost() ){
            $data = $_POST;
            $conditions = array();

            if( isset($data['company_id'])){
                // debug($data['id'] ); 
                $conditions['Employee.company_id'] = $data['company_id'];
            }
            // debug($data['password'] ); 
            // exit;
            $data['password'] = Security::hash($data['password'], null, false);
            $loginDetail = array('Employee' => $data);
            // $this->Auth->logout();

            if( !$this->ZAuth->user() && $this->Auth->login($loginDetail)){
                // get user info
                $conditions['Employee.email'] = $data['email'];
                $employee_info = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'conditions' => $conditions,
                    'fields' => array(
                        'CompanyEmployeeReference.employee_id',
                        'CompanyEmployeeReference.company_id', 
                        'Company.company_name', 'Company.dir',
                        'Employee.avatar', 'Employee.is_sas', 'Employee.id', 'Employee.company_id', 'Employee.create_a_project', 'Employee.first_name', 'Employee.last_name',
                        'Role.name as role_name',
                        'Role.desc as role_desc',
                    )
                ));
                // debug($employee_info ); exit;
                $count_company = count($employee_info);
                if($count_company==1){
                    // register auth
					$user = $employee_info[0]['Employee'];
                    $this->ZAuth->authorize($user);
					// debug( $user); exit;
                    $employee_info[0] = array_merge(
                        $employee_info[0]['Employee'], 
                        $employee_info[0]['CompanyEmployeeReference'], 
                        $employee_info[0]['Company'], 
                        $employee_info[0]['Role']
                    );
                    $this->ZAuth->respond('success', array(
                        // 'user' => $user,
                        'auth_code' => $this->ZAuth->user('auth_code'),
                        'user' => $employee_info
                    ), __('Login success', true));
                    return;
                } elseif($count_company>1) { // >1
                    // $list_roles = Set::combine($employee_info, '{n}.Employee.id', '{n}.Role.name', '{n}.Company.company_name');
                    foreach( $employee_info as $k => $employee){
                        $employee_info[$k] = array_merge(
                            $employee['Employee'], 
                            $employee['CompanyEmployeeReference'], 
                            $employee['Company'], 
                            $employee['Role']
                        );
                    }
                    // $employee_info = array_merge($employee['Employee'], $employee_info[0]['CompanyEmployeeReference'], $employee_info[0]['Company'], $employee_info[0]['Role']);
                    $this->ZAuth->respond('success', 
                        // 'users' => $employee_info,
                        $employee_info,
                     __('Please select your company', true));
                }
                
            }
        }
        // $this->login_failed();
        $this->ZAuth->respond('fail', array('data' => null), 'login failed', '0');
	}
	public function employees_for_login_nav(){
        if( $this->RequestHandler->isPost() ){
            $data = $_POST;
            $conditions = array();
            $user = $this->get_user();

            if(!!$user['is_sas']){
                // Check data request is ok?
                if (isset($data['check_only']) && $data['check_only']==1) {
                    $this->ZAuth->respond('success', 
                    null,
                    'Data is ok', true);
                    return;
                }
                // process condition of query
                $conditions['NOT'] = array ("Employee.email" => null);
                if (isset($data['from']) && $data['from']>0) {
                    $conditions['Employee.updated >'] = strtotime($data['from']);
                }

                $employee_info = $this->Employee->find('all', array(
                    'conditions' => $conditions,
                    'recursive' => 0,
                    'fields' => array(
                        'Employee.id as employee_id',
                        'Employee.email',
                        'Employee.company_id',
                        'Employee.updated as updated_on_host',
                    ),
                    'order'=>array('Employee.updated DESC')
                ));
                $count = count($employee_info);

                foreach ($employee_info as $key => $value) { // For security: encrypt email
                    $employee_info[$key]['Employee']['email'] = md5(strtolower($value['Employee']['email']));
                }
                
                $this->ZAuth->respond('success', 
                    array(
                        'total' => $count,
                        'employees' => $employee_info,
                    ),
                    'Get data success', true);
            }
                
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
	}
// CustomLogin is Used to login with SAS user.
    public function customLogin() {
        if ($this->RequestHandler->isPost()) {
            $data = $_POST;
            $data['password'] = Security::hash($data['password'], null, false);
            $loginDetail = array('Employee' => $data);
            if(!$this->ZAuth->user()&& $this->Auth->login($loginDetail)) {
                $employee_info = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'email' => $data['email']
                    )
                    ));
                // debug($employee_info);
                $user = $employee_info['Employee'];
                if ($user['is_sas']) {
                    $this->ZAuth->authenticate['expires'] = null; // SAS usertoken will never expires
                    $this->ZAuth->authorize($user);
                    $this->ZAuth->respond('success', array(
                        'userId'=>$user['id'],
                        'id'=> $this->ZAuth->user('auth_code'),

                    ));
                }
            }
        }
        // $this->ZAuth->respond('fail', array('data' => 'null'), 'login failed', '0');
        $this->ZAuth->respond('fail', null, 'login failed', '0');
    }
    // public function logout() {
    //     $this->ZAuth->unauthorize();
    //     $this->ZAuth->respond('logout_success');
    // }
    // Ham get_synthesis:: lay cac cot trong systhesis va thu tu cac cot.
    public function get_dashboard_setting() {
        if($this->RequestHandler->isPost()) {
            $this->loadModels('CompanyDefaultSetting', 'ProjectIndicatorSetting');
            $popup_setting = $indicatorSetting = array();
            // $widget_name = $this->_widget_name();
            $popup_setting = $this->ProjectIndicatorSetting->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                ),
                'fields' => array('widget_setting') 
            ));
            $popup_setting = !empty($popup_setting) ? @unserialize($popup_setting['ProjectIndicatorSetting']['widget_setting']) : array();
            if(!empty($popup_setting)){
                $popup_setting = Set::combine($popup_setting, '{n}.widget', '{n}');
                // Debug($popup_setting["project_synthesis"]);
                if(!empty($popup_setting["project_synthesis"])) {
                    $this->ZAuth->respond('success', $popup_setting, 'User Setting', true); 

                }
            }
            $companyDefaultSetting = $this->CompanyDefaultSetting->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'df_key' => 'dashboard_default_setting',
                ),
                'fields' => array('df_value')
            ));
            
            $companyDefaultSetting = !empty($companyDefaultSetting) ? @unserialize($companyDefaultSetting['CompanyDefaultSetting']['df_value']) : array();
            if(!empty($companyDefaultSetting)){
                $companyDefaultSetting = Set::combine($companyDefaultSetting, '{n}.widget', '{n}');
                $this->ZAuth->respond('success', $companyDefaultSetting, 'Default Setting', true); 
            }
           
            // Debug($popup_setting);
            // Debug($companyDefaultSetting);

            $this->ZAuth->respond('success', $companyDefaultSetting, 'Get data empty', true); 
        }
        
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
        
    }
}
