<?php
class ApiAppController extends AppController {
    public $components = array('Api.ZAuth', 'UserUtility');
    var $employee_info = array();
    var $companyConfigs = array();
    var $user = array();
	function beforeFilter(){
		parent::beforeFilter();
		if( !defined('API_DATE_FORMAT')) define('API_DATE_FORMAT', 'd-m-Y');
		$this->Auth->allow('*');
		$this->layout = 'ajax';
        $this->parseRequest();
		$this->user = $this->ZAuth->user();
        $this->get_employee_info();
        $this->getAllCompanyConfigs();
	}
    protected function parseRequest(){
		if( $this->RequestHandler->isPost()){
			$this->data = $_POST;
		}
        if ($this->RequestHandler->requestedWith('json')) {
            $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            if (!is_null($jsonData) and $jsonData != false) {
                $this->data = $jsonData;
            }
        } else if( $this->RequestHandler->isPost() ){

        } else if( $this->RequestHandler->isPut() ){

        }
    }

    protected function get_user(){
		return $this->user;
	}
    protected function get_employee_info(){
        $this->loadModel('Employee');
        if(empty($this->user)) return;
		$user_id = $this->user['id'];
		$company_id = $this->user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("first", array("conditions" => array("Employee.id" => $this->user['id'])));
		$this->employee_info = $employee_all_info;
    }
    public function getProjectsByConslt() {
        $user = $this->get_user();
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];  
        $role = $this->employee_info['Role']['name']; 
        $listProjectIds = array();
        if ($role == 'conslt') {
            $this->loadModel('ProjectEmployeeManager');
            $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_manager_id' => $profitCenterId,
                    'is_profit_center' => 1
                ),
                'fields' => array('project_id', 'project_id')
            ));
            $listProjectIds = $listProjectIdOfPcs;
        }
        return $listProjectIds;
    }
    public function getProjectsByPM() {
        $user = $this->get_user();
        $employee_id = $user['id']; 
        $company_id = $this->employee_info['Company']['id'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];  
        $role = $this->employee_info['Role']['name']; 
        $listProjectIds = array();
        //them dieu kien check de hien thi project doi voi user co quyen Read Access project do. Update by QuanNV 29/06/2019
        if ($role == 'pm') {
            $this->loadModel('ProjectEmployeeManager');
            $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_manager_id' => $employee_id,
                    'or' => array (
                        'is_profit_center' => 0,
                        'is_profit_center is NULL'
                    )
                ),
                'fields' => array('project_id', 'project_id')
            ));
            $this->loadModel('Project');
            $listProjectOfEmployManager = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'project_manager_id' => $employee_id
                ),
                'fields' => array('id', 'id')
            ));
            $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_manager_id' => $profitCenterId,
                    'is_profit_center' => 1
                ),
                'fields' => array('project_id', 'project_id')
            ));
            $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
            
        }
        return $listProjectIds;
    }
	public function logout(){
        $this->ZAuth->unauthorize();
        $this->ZAuth->respond('logout_success');
    }
	protected function login_failed(){
        $this->ZAuth->respond('login_failed', null, 'login failed', '0');
    }

    protected function unauthorized(){
        $this->ZAuth->respond('unauthorized', null, 'unauthorized', '0');
    }

    public function data_incorrect($message="data_incorrect", $error_code="0", $data=null){
       $this->ZAuth->respond('data_incorrect', $data, $message, $error_code);
    }
	
    public function appError(){
        $this->unauthorized();
    }
    // Re-written from function _getPathProjectGlobalView file projects_controller.php
    protected function getPath($type = 'globalviews', $company) {
        switch ($type) {
            case 'globalviews':
            case 'livrable':
                
                $this->loadModel('Company');
                $pcompany = $this->Company->find('first', array(
                    'recursive' => -1, 'conditions' => array('Company.id' => $company['parent_id'])));
                
                $path = FILES . 'projects' . DS . $type . DS;
                if ($pcompany) {
                    $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
                }
                $path .= $company['dir'] . DS;
                // $path .= $company['Company']['dir'];
                return $path;
            case 'project_tasks':
                // $company = $this->employee_info['Company']['id'];
                $path = FILES . 'projects' . DS . 'project_tasks' . DS . $company['id'] . DS;
                // Debug($path);
                return $path;
            default:
                # code...
                break;
        }
    }
    // This function is re-Written from app_controller.php - _getAllConfigs()
    protected function getAllCompanyConfigs(){
		if( empty( $this->employee_info )) return false;
        $infos = $this->employee_info;
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
            $this->set('companyConfigs', $this->companyConfigs);
        }
    }
    /**
     * Check role
     *
     * @return void
     * @access protected
     */
    protected function checkRole($isChange, $projectId = null, $option = array(), $project_task_id = null) {
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
            // $employeeInfo = $this->Session->read("Auth.employee_info");
            $employeeInfo = $this->employee_info;
            $canModified = true;
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
                    $changeStatusPJ = !empty($employInfors) && !empty($employInfors['Employee']['change_status_project']) ? true : false;
                    $updatePJ = !empty($employInfors) && !empty($employInfors['Employee']['update_your_form']) ? true : false;
                    $deletePJ = !empty($employInfors) && !empty($employInfors['Employee']['delete_a_project']) ? true : false;
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
                return false;
                // $this->cakeError('error404');
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
	
	protected function z_merge_all_key($arr){
		$arr_k = array();
		foreach( $arr as $k => $v){
			if( is_integer($k)){
				$_k = '____'.rand().'_'.$k;
				$arr[$_k] = $v;
				$arr_k[] = $_k;
			}else{
				$arr_k[] = $k;
			}
		}
		extract( $arr);
		return call_user_func_array('array_merge_recursive', compact($arr_k));
		
	}
}