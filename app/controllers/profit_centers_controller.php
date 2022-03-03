<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProfitCentersController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProfitCenters';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Organization');
    var $paginate = array('limit' => 1000);
    var $components = array('MultiFileUpload');

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        $this->loadModels('Employee', 'ProfitCenterManagerBackup', 'Profile');
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $companyId = $this->employee_info["Company"]["id"];
        else
            $companyId = "";
        $this->set('company_id', $companyId);
        $this->ProfitCenter->recursive = 0;
        $tree = array();
        if ($companyId != "") {
            $this->set('companies', $this->ProfitCenter->Company->getTreeList($companyId));
            $tree = $this->ProfitCenter->getTreeList($companyId);
            $this->set('profits', $profits = $this->ProfitCenter->generateTreeList(array('company_id' => $companyId), null, null, '--'));
            $allEmployees = $this->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
            $employeeIds = $this->Employee->CompanyEmployeeReference->find('all', array(
                'recursive' => 0,
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $companyId
                ),
                'fields' => array('CompanyEmployeeReference.employee_id', 'Role.name')));
            $employees = array();
            foreach ($employeeIds as $value) {
                foreach ($allEmployees as $key2 => $value2) {
                    if ($value['CompanyEmployeeReference']['employee_id'] == $key2) {
                        $employees[$key2] = $value2;
                        break;
                    }
                }
            }
			$profile = $this->Profile->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' =>  $companyId
				),
				'fields' => array('name')
			));
        } else {
            $allEmployees = $this->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
            $employeeIds = $this->Employee->CompanyEmployeeReference->find('all', array(
                'recursive' => 0,
                'fields' => array('CompanyEmployeeReference.employee_id', 'Role.name')));
            $employees = array();
            foreach ($employeeIds as $value) {
                foreach ($allEmployees as $key2 => $value2) {
                    if ($value['CompanyEmployeeReference']['employee_id'] == $key2) {
                        $employees[$key2] = $value2;
                        break;
                    }
                }
            }
			
            $tree = $this->ProfitCenter->getTreeList();
				
            $this->set('companies', $this->ProfitCenter->Company->generateTreeList(null, null, null, '--'));
            $this->set('profits', $this->ProfitCenter->generateTreeList(null, null, null, '--'));
			$profile = $this->Profile->find('list', array(
				'recursive' => -1,
				'fields' => array('name')
			));
        }
        $backupOfProfitcenters = array();

        if(!empty($tree)){
            $profitIds = array_keys($tree);
            $backupOfProfitcenters = $this->ProfitCenterManagerBackup->find('list', array(
                'recursive' => -1,
                'conditions' => array('profit_center_id' => $profitIds),
                'fields' => array('id', 'employee_id', 'profit_center_id'),
                'group' => array('profit_center_id', 'id', 'employee_id')
            ));
        }
        $managerBackupLists = $this->_getEmployeeOfCP($companyId);
		
        $this->set(compact('managerBackupLists', 'employees', 'tree', 'backupOfProfitcenters', 'profile'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid profit center', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('profitCenter', $this->ProfitCenter->read(null, $id));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function update($id = null) {
       $this->loadModels('ProfitCenterManagerBackup');
	   $success = false;
	   $this->layout = false;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid profit center', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$last_item =  $this->ProfitCenter->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $this->data['id']
			),
			'fields' => array('company_id')
		 ));
        if (!empty($this->data)) {
			$can_save = 1;
			if(!empty($this->data['id']) && !empty($this->data['company_id'])){
				 if(!empty($last_item) && !empty($last_item['ProfitCenter']['company_id'])){
					if($this->data['company_id'] != $last_item['ProfitCenter']['company_id']){
						$count_employee_refer =  $this->Employee->find('count', array(
							'recursive' => -1,
							'conditions' => array(
								'profit_center_id' => $this->data['id'],
								'company_id' => $last_item['ProfitCenter']['company_id']
							)
						));
						if($count_employee_refer > 0){
							$this->Session->setFlash(__('Can not update profit center has employee reference', true), 'error');
                            $can_save = 0;
						}
					}
				 }
			}	
			if (!empty($this->data['parent_id']) &&  $can_save == 1) {
				$parent_cons = array(
					'id' => $this->data['parent_id'],
				);
				if(!empty($last_item) && !empty($last_item['ProfitCenter']['company_id'])){
					$parent_cons ['company_id'] = $last_item['ProfitCenter']['company_id'];
				}
				$parent_pc = $this->ProfitCenter->find('first', array(
					'recursive' => -1,
					'fields' => array('id', 'lft', 'rght'),
					'conditions' => $parent_cons
				));
				if(empty($parent_pc)){
					$can_save = 0;
					$this->Session->setFlash(__('Can not update parent profit center is not same company', true), 'error');
				}else if (!empty($parent_pc) && $parent_pc['ProfitCenter']['lft'] == null && $parent_pc['ProfitCenter']['rght'] == null) {
					$cc = $this->ProfitCenter->find('first', array(
						'recursive' => -1,
						'fields' => array('rght'),
						'order' => array("ProfitCenter.rght" => "desc")
							));
					if (!empty($cc)) {
						$this->ProfitCenter->id = $this->data['parent_id'];
						$this->ProfitCenter->saveField('lft', $cc['ProfitCenter']['rght'] + 1);
						$this->ProfitCenter->saveField('rght', $cc['ProfitCenter']['rght'] + 2);
					}
				}
			}
			/**
			 * Lay manager cu cua profit center
			 */
			$managerIdOld = '';
			if(!empty($this->data['id']) &&  $can_save == 1 && !empty($this->data['manager_id'])){
				$tmp_manager_id = array();
				$tmp_manager_id[0] = $this->data['manager_id'];
				if(!$this->checkEmployeeExist($tmp_manager_id, $last_item['ProfitCenter']['company_id'])){
					$can_save = 0;
					$this->Session->setFlash(__('The employee manager is not same company', true), 'error');
				}
			}
			if(!empty($this->data['manager_backup'])){
				$this->data['manager_backup'] = array_unique($this->data['manager_backup']);
				if(($key = array_search(0, $this->data['manager_backup'])) !== false) {
					unset($this->data['manager_backup'][$key]);
				}
			}

			$backups = !empty($this->data['manager_backup']) ? $this->data['manager_backup'] : array();
			if(!empty($backups) && $can_save == 1){
				if(!$this->checkEmployeeExist($backups, $last_item['ProfitCenter']['company_id'])){
					$can_save = 0;
					$this->Session->setFlash(__('The employee backup manager is not same company', true), 'error');
				}
			}
			unset($this->data['manager_backup']);
			if($this->data['parent_id'] == "null"){
				$this->data['parent_id'] = 0;
			}

			if($can_save == 1){
				if (!empty($this->data['id'])) {
					$this->ProfitCenter->id = $this->data['id'];
				}else{
					$this->ProfitCenter->create();
				}
				$data['ProfitCenter'] = $this->data;
				$result = $this->ProfitCenter->save($data);
				if ($result) {
					$_id = $this->ProfitCenter->id;
					$this->data = $this->ProfitCenter->read(null, $id);
					
					$this->data = $this->data['ProfitCenter'];
					 /**
					  * Lay danh sach tat ca backup cua profit center
					  */
					 $managerBackups = $this->ProfitCenterManagerBackup->find('list', array(
						'recursive' => -1,
						'conditions' => array('profit_center_id' => $_id),
						'fields' => array('id', 'id')
					 ));
					 if(!empty($backups)){
						foreach($backups as $empId){
							$saved = array(
								'profit_center_id' => $_id,
								'company_id' => $this->data['company_id'],
								'employee_id' => $empId
							);
							/**
							 * Kiem tra xem no ton tai trong table nay chua
							 */
							$check = $this->ProfitCenterManagerBackup->find('list', array(
								'recursive' => -1,
								'conditions' => $saved,
								'fields' => array('id')
							 ));
							 $this->ProfitCenterManagerBackup->create();
							 if(!empty($check) && !empty($check['ProfitCenterManagerBackup']['id'])){
								$this->ProfitCenterManagerBackup->id = $check['ProfitCenterManagerBackup']['id'];
							 }
							 if($this->ProfitCenterManagerBackup->save($saved)){
								$thisId = $this->ProfitCenterManagerBackup->id;
								unset($managerBackups[$thisId]);
							 }
						}
					 }
					 if(!empty($managerBackups)){
						$this->ProfitCenterManagerBackup->deleteAll(array('ProfitCenterManagerBackup.id' => $managerBackups), false);
					 }
					 unset($managerBackups);
					 /**
					  * Save lai manager cua profit center vao table activity budget
					  */
					$this->loadModels('ActivityBudget');
					$this->ActivityBudget->updateAll(array('ActivityBudget.manager_id' => $this->data['manager_id']), array('ActivityBudget.profit_id' => $_id));
					 $success = true;
					 $this->Session->setFlash(__('Saved', true), 'success');
				} else {
					$this->Session->setFlash(__('NOT SAVED', true), 'error');
				}
			}	
        }
        
		$this->set(compact('success'));
    }
	function checkEmployeeExist($employee_id, $company_id){
		$this->loadModel('Employee');
		$countE = $this->Employee->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $employee_id,
				'company_id' => $company_id
			)
		));
		return ($countE == count($employee_id)) ? true : false;
	}
    function get_employees($company_id = null) {
        $employees = $this->_getEmployeeOfCP($company_id);
        $this->set('employees', $employees);
        $this->layout = false;
    }

    function get_employee_backup($company_id = null) {
        $employees = $this->_getEmployeeOfCP($company_id);
        $this->set('employees', $employees);
        $this->layout = false;
    }

    private function _getEmployeeOfCP($company_id = null) {
        $this->loadModel('Employee');
        if ($this->employee_info["Employee"]["is_sas"] != 1) {
            $companies = $this->Employee->CompanyEmployeeReference->Company->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $this->employee_info["Company"]["id"])));
            $companies = $this->Employee->CompanyEmployeeReference->Company->find('list', array(
                'conditions' => array('lft >=' => $companies['Company']['lft'], 'rght <=' => $companies['Company']['rght'])
                    ));
            if (!isset($companies[$company_id])) {
                $company_id = $this->employee_info["Company"]["id"];
            }
        }
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $employees = $this->Employee->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        return $employees;
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for profit center', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$data = array();
		if((!$this->is_sas) && (!$this->_isBelongToCompany($id, 'ProfitCenter'))){
			$this->_functionStop(false, $id, __('You have not permission to access this function', true), false, array('action' => 'index'));
		}
        $allowDeleteProfitCenter = $this->_profitCenterIsUsing($id);
        if($allowDeleteProfitCenter['allowDeleteProfitCenter'] == 'true'){
            if ($this->ProfitCenter->delete($id)) {
                // delete in TmpCaculateProfitCenter
                $this->loadModel('TmpCaculateProfitCenter');
                $this->TmpCaculateProfitCenter->deleteAll(array('TmpCaculateProfitCenter.profit_center_id'=>$id));
                $this->Session->setFlash(__('Deleted', true), 'success');
				if( $this->params['isAjax']){
					$result = 'success';
					$this->set(compact('result', 'data'));
					$this->render();
				}
                $this->redirect(array('action' => 'index'));
            }
			
        } else {
            $this->Session->setFlash('<a id="list_delete" href="#" data_id = "'.$id.'"">' . __('Cannot delete Profit Center.  View all project reference', true) .'</a>', 'error');
			if( $this->params['isAjax']){
				$result = 'failed';
				$data = $allowDeleteProfitCenter;
				$this->set(compact('result', 'data'));
				$this->render();
			}
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }

    function organization() {
        $external = (int) isset($this->companyConfigs['display_external_in_organization']) ? $this->companyConfigs['display_external_in_organization'] : 1;
        $data = '';
        if (!empty($this->params['url']['link'])) {
            $data = explode('|', $this->params['url']['link']);
            $this->set('link', $data[0]);
            $this->set('title', $data[1]);
        }
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $companyId = $this->employee_info["Company"]["id"];
        else
            $companyId = "";
        if ($companyId != "") {
            $tree = $this->ProfitCenter->getTreeListPro($companyId, $external);
            $this->set('company_name', $this->employee_info["Company"]['company_name']);
        } else {
            $companies = $this->ProfitCenter->Company->generateTreeList(null, null, null, '--');
            $this->set('companies', $companies);
            if (!empty($this->params['url']['limit'])) {
                $tree = $this->ProfitCenter->getTreeListPro($this->params['url']['limit'], $external);
                $name = $this->ProfitCenter->Company->getName($this->params['url']['limit']);
                $this->set('limit', $this->params['url']['limit']);
                $this->set('company_name', $name['Company']['company_name']);
            } else {
                $first_key = array_shift(array_keys($companies));
                $first_value = array_shift(array_values($companies));
                $tree = $this->ProfitCenter->getTreeListPro($first_key, $external);
                $this->set('limit', $first_key);
                $this->set('company_name', $first_value);
            }
        }
        $this->loadModel('CompanyConfigs');
        $companyConfigs = $this->CompanyConfigs->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $companyId
            ),
            'fields' => array('cf_name', 'cf_value')
        ));
        $this->set('company_id', $companyId);
        $this->set('tree', $tree);
        $this->set('companyConfigs', $companyConfigs);
    }

    function export_organization() {
        $this->layout = false;
        ini_set('memory_limit', '2048M');
        if (!empty($this->data['Export'])) {
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);
            $tmpFile = TMP . 'page_' . time() . '.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));
            list($_width, $_height) = getimagesize($tmpFile);
            $image = imagecreatefrompng($tmpFile);
            $crop = imagecreatetruecolor($_width - 28, $height);

            imagecopy($crop, $image, 0, 0, 28, 195, $_width, $height);
            imagepng($crop, $tmpFile);

            $this->set(compact('tmpFile', 'height', 'rows'));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function check_name($name) {
        $this->autoRender = false;
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        //$this->CompanyEmployeeReference->Behaviors->attach('Containable');
        $check = '';
        if ($company_id != "") {
            $check = $this->ProfitCenter->find('all', array(
                'fields' => array('company_id'),
                //'contain' => array('Employee' => array('code_id')),
                'conditions' => array(
                    'ProfitCenter.company_id' => $company_id,
                    'ProfitCenter.name LIKE' => strtolower($name)),
                'recursive' => 0
                    ));
        } else {
            $check = $this->ProfitCenter->find('all', array(
                'fields' => array('company_id'),
                //'contain' => array('Employee' => array('code_id')),
                'conditions' => array('ProfitCenter.name LIKE' => strtolower($name)),
                'recursive' => 0
                    ));
        }
        if (!empty($check)) {
            echo "1";
        }
        exit;
    }

    function update_lft_rght() {
        $this->autoRender = false;
        $profits = $this->ProfitCenter->find('all', array(
            'recursive' => -1
                ));
        if (!empty($profits)) {

            $i = 1;
            $j = 2;
            foreach ($profits as $value) {
                $this->ProfitCenter->id = $value['ProfitCenter']['id'];
                $this->ProfitCenter->saveField('lft', $i);
                $this->ProfitCenter->saveField('rght', $j);
                $this->ProfitCenter->saveField('parent_id', null);
                $i = $i + 2;
                $j = $j + 2;
            }

            $this->Session->setFlash(__('Profit centers was updte lft and rght', true), 'success');
            $this->redirect(array('action' => 'index'));
        }
    }
      /**
     *  Kiem tra profit center da co su dung
     *  @return boolean
     *  @access private
     */

    private function _profitCenterIsUsing($id = null){
        $this->loadModel('ActivityProfitRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $this->loadModel('ProjectTeam');
        $this->loadModel('TmpCaculateProfitCenter');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('Activity');
        $this->loadModel('Employee');
        $this->loadModel('Project');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectEmployeeManager');
        $checkActivityProfitRefer = $this->ActivityProfitRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityProfitRefer.profit_center_id' => $id),
                'fields' => array('activity_id')
            ));

        $checkProjectEmployeeProfitFunctionRefer = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => $id),
                'fields' => array('employee_id')
            ));
        $checkProjectFunctionEmployeeRefer = $this->ProjectFunctionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectFunctionEmployeeRefer.profit_center_id' => $id),
                'fields' => array('project_team_id')
            ));
        $checkProjectTeam = $this->ProjectTeam->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectTeam.profit_center_id' => $id),
                'fields' => array('project_id')
            ));
		$checkPM = $this->ProjectEmployeeManager->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_manager_id' => $id,
				'is_profit_center' => 1
			),
			'fields' => array('project_id')
		));
        $checkActivityTaskEmployeeRefer = $this->ActivityTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTaskEmployeeRefer.is_profit_center' => 1, 'ActivityTaskEmployeeRefer.reference_id' => $id),
                'fields' => array('activity_task_id')
            ));
        $checkProjectTaskEmployeeRefer = $this->ProjectTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectTaskEmployeeRefer.is_profit_center' => 1, 'ProjectTaskEmployeeRefer.reference_id' => $id ),
                'fields' => array('project_task_id')
            ));
        $activities = $employees = $projects = $activitytasks = $activitytasks = $projectIDs = $projectasks = array();
        if(!empty($checkActivityProfitRefer))
        {
            $activities = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $checkActivityProfitRefer),
                'fields' => array('id','name')
            ));
        }
        if(!empty($checkProjectEmployeeProfitFunctionRefer))
        {
            $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'Employee.id' => $checkProjectEmployeeProfitFunctionRefer,
                        'profit_center_id' => $id
                    )
                ),
                'fields' => array('id','fullname')
            ));
        }

        if(!empty($checkProjectFunctionEmployeeRefer))
        {
            $projectIDs = $this->ProjectTeam->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectTeam.id' => $checkProjectFunctionEmployeeRefer),
                'fields' => array('project_id')
            ));
        }
        $projectIDs = array_merge($projectIDs, $checkProjectTeam, $checkPM);

        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $projectIDs),
            'fields' => array('id','project_name')
        ));

        if(!empty($checkActivityTaskEmployeeRefer))
        {
            $activitytasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.id' => $checkActivityTaskEmployeeRefer),
                'fields' => array('id', 'name', 'activity_id')
            ));
            if( !empty($activitytasks) ){
                $activitytasks = Set::combine($activitytasks, '{n}.ActivityTask.id', '{n}.ActivityTask');
            }
        }
        if(!empty($checkProjectTaskEmployeeRefer))
        {
            $projectasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $checkProjectTaskEmployeeRefer),
                'fields' => array('id', 'task_title', 'project_id')
            ));
            if( !empty($projectasks) ){
                $projectasks = Set::combine($projectasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
            }
        }
		$childPC = $this->ProfitCenter->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'parent_id' => $id
			),
			'fields' => array('id', 'name')
		));
        $allowDeleteProfitCenter= true;
        if(count($activities) != 0 || count($employees) != 0 || count($projects) != 0 ||
         count($projectIDs) != 0 || count($activitytasks) || count($projectasks) || count($childPC)){
            $allowDeleteProfitCenter = false;
        }
        $data['activities']=$activities;
        $data['employees']=$employees;
        $data['projects']=$projects;
        $data['activitytasks']=$activitytasks;
        $data['projectasks']=$projectasks;
        $data['childPC']=$childPC;
        $data['allowDeleteProfitCenter'] = $allowDeleteProfitCenter;
        return $data;
    }
    public function profitCenterIsAjax($id = null){
        $data = $this->_profitCenterIsUsing($id);
        //debug($data);exit;
        die(json_encode($data));
    }
	private function moveEmployees($old_pc, $new_pc){
		$this->loadModels('Employee', 'ProjectEmployeeProfitFunctionRefer');
		$result = $this->Employee->updateAll(array('profit_center_id' => $new_pc), array('profit_center_id' => $old_pc) );
		$result = $this->ProjectEmployeeProfitFunctionRefer->updateAll(array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => $new_pc), array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => $old_pc) );
		
		$result = $this->ProjectFunctionEmployeeRefer->updateAll(array('ProjectFunctionEmployeeRefer.profit_center_id' => $new_pc), array('ProjectFunctionEmployeeRefer.profit_center_id' => $old_pc) );
		
		return $result; 
	}
	private function moveProjects($old_pc, $new_pc){
		$this->loadModels('Projects', 'ProjectTeam', 'ProjectEmployeeManager', 'ProjectFunctionEmployeeRefer');
		
		/* ProjectTeam */
		$listProjectsNewPC = $this->ProjectTeam->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'profit_center_id' => $new_pc,
			),
			'fields' => array('id', 'project_id')
		));
		$listProjectsOldPC = $this->ProjectTeam->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'profit_center_id' => $old_pc,
				'NOT' => array(
					'project_id' => array_values($listProjectsNewPC)
				)
			),
			'fields' => array('id')
		));
		$result = $this->ProjectTeam->updateAll(array('ProjectTeam.profit_center_id' => $new_pc), array('ProjectTeam.id' => array_values($listProjectsOldPC)));
		$this->ProjectTeam->deleteAll(array('ProjectTeam.profit_center_id' => $old_pc), false);
		/* END ProjectTeam */
		
		/* ProjectFunctionEmployeeRefer */
		/*
		$refers = $this->ProjectFunctionEmployeeRefer->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'profit_center_id' => $new_pc
			),
			'fields' => array('*')
		));
		if( !empty($refers)){
			// Sau khi update project team
			$projectTeams = $this->ProjectTeam->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'profit_center_id' => $new_pc,
				),
				'fields' => array('profit_center_id', 'id')
			));
			foreach( $refers as $refer){
				$refer['profit_center_id'] = $new_pc,
				if( !empty( $projectTeams[] $refer['project_team_id'] = $new_pc,
				
			}
			*/
		/* END ProjectFunctionEmployeeRefer */
		
		/* ProjectEmployeeManager */
		$listPM = $this->ProjectEmployeeManager->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_manager_id' => array($old_pc, $new_pc),
				'is_profit_center' => 1
			),
		));
		$listPM = !empty($listPM) ? Set::combine($listPM, '{n}.ProjectEmployeeManager.id', '{n}.ProjectEmployeeManager', '{n}.ProjectEmployeeManager.project_manager_id') : array();
		
		
		// delete duplicate fields
		if( !empty($listPM[$old_pc]) && !empty($listPM[$new_pc]) ){
			$listDelete = array();
			foreach($listPM[$old_pc] as $id => $PM){
				foreach($listPM[$new_pc] as $idn => $PMn){
					if( ($PMn['project_manager_id'] == $PM['project_manager_id']) && ($PMn['project_id'] == $PM['project_id']) && ($PMn['type'] == $PM['type'])){
						$listDelet[] = $id; 
					}
				}
			}
			if( !empty($listDelete)) $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listDelete), false);
		}
		// END delete duplicate fields
		
		// Update to new PC
		$result = $this->ProjectEmployeeManager->updateAll(
			array( // data
				'ProjectEmployeeManager.project_manager_id' => $new_pc
			), 
			array( // 'conditions'
				'ProjectEmployeeManager.project_manager_id' => $old_pc,
				'ProjectEmployeeManager.is_profit_center' => 1,
				
			)
		);
		return $result; 
	}
	private function moveActivities($old_pc, $new_pc ){
		$this->loadModels('ActivityProfitRefer');
		$result = $this->ActivityProfitRefer->updateAll(
			array( 'ActivityProfitRefer.profit_center_id' => $new_pc), 
			array( 'ActivityProfitRefer.profit_center_id' =>  $old_pc)
		);
		return $result;
	}
	private function moveActivityTasks($old_pc, $new_pc ){
		$this->loadModels('ActivityTasks', 'ActivityTaskEmployeeRefer');
		$refers = $this->ActivityTaskEmployeeRefer->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'reference_id' => array( $old_pc, $new_pc),
				'is_profit_center' => 1
			)
		));
		$refers = !empty($refers) ? Set::combine($refers, '{n}.ActivityTaskEmployeeRefer.activity_task_id', '{n}.ActivityTaskEmployeeRefer', '{n}.ActivityTaskEmployeeRefer.reference_id') : array();
		if( !empty($refers[$old_pc])){
			$listDelete = $listUpdate = array();
			foreach( $refers[$old_pc] as $at_id => $refer){
				if( !empty($refers[$new_pc][$at_id])){
					// Neu co thi cong don estimated
					$listDelete[] = $refer['id'];
					$refers[$new_pc][$at_id]['estimated'] += $refer['estimated'];
					$listUpdate[] = $refers[$new_pc][$at_id];
				}
			}
			if( !empty($listDelete)){
				$this->ActivityTaskEmployeeRefer->deleteAll(array(
					'ActivityTaskEmployeeRefer.id' => $listDelete
				), false);
			}
			if( !empty($listUpdate)){
				foreach( $listUpdate as $elm){
					$this->ActivityTaskEmployeeRefer->id = $elm['id'];
					$this->ActivityTaskEmployeeRefer->save($elm);
					
				}
			}
		}
		$result = $this->ActivityTaskEmployeeRefer->updateAll(
			array( 'ActivityTaskEmployeeRefer.reference_id' => $new_pc), 
			array( 
				'ActivityTaskEmployeeRefer.reference_id' =>  $old_pc,
				'ActivityTaskEmployeeRefer.is_profit_center' => 1
			)
		);
		return $result;
	}
	private function moveProjectTasks($old_pc, $new_pc ){
		$this->loadModels('ProjectTasks', 'ProjectTaskEmployeeRefer');
		$refers = $this->ProjectTaskEmployeeRefer->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'reference_id' => array( $old_pc, $new_pc),
				'is_profit_center' => 1
			)
		));
		$refers = !empty($refers) ? Set::combine($refers, '{n}.ProjectTaskEmployeeRefer.activity_task_id', '{n}.ProjectTaskEmployeeRefer', '{n}.ProjectTaskEmployeeRefer.reference_id') : array();
		$result =  true;
		if( !empty($refers[$old_pc])){
			$listDelete = $listUpdate = array();
			foreach( $refers[$old_pc] as $at_id => $refer){
				if( !empty($refers[$new_pc][$at_id])){
					// Neu co thi cong don estimated
					$listDelete[] = $refer['id'];
					$refers[$new_pc][$at_id]['estimated'] += $refer['estimated'];
					$listUpdate[] = $refers[$new_pc][$at_id];
				}
			}
			if( !empty($listDelete)){
				$this->ProjectTaskEmployeeRefer->deleteAll(array(
					'ProjectTaskEmployeeRefer.id' => $listDelete
				), false);
			}
			if( !empty($listUpdate)){
				foreach( $listUpdate as $elm){
					$this->ProjectTaskEmployeeRefer->id = $elm['id'];
					$this->ProjectTaskEmployeeRefer->save($elm);
				}
			}
		}
		
		$result = $this->ProjectTaskEmployeeRefer->updateAll(
			array( 'ProjectTaskEmployeeRefer.reference_id' => $new_pc), 
			array( 
				'ProjectTaskEmployeeRefer.reference_id' =>  $old_pc,
				'ProjectTaskEmployeeRefer.is_profit_center' => 1
			)
		);
		/* Move NCT workload */
		$this->loadModels('NctWorkload');
		$refers = $this->NctWorkload->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'reference_id' => array( $old_pc, $new_pc),
				'is_profit_center' => 1
			)
		));
		$refers = !empty($refers) ? Set::combine($refers, '{n}.NctWorkload.id', '{n}.NctWorkload', '{n}.NctWorkload.reference_id') : array();
		if( !empty($refers[$old_pc])){
			if( !empty($refers[$new_pc])){
				$listDelete = $listUpdate = array();
				foreach( $refers[$old_pc] as $refer){
					foreach( $refers[$new_pc] as $n_refer){
						if( $refer['task_date'] == $n_refer['task_date'] 
							&& $refer['project_task_id'] == $n_refer['project_task_id']
							&& $refer['end_date'] == $n_refer['end_date'] 
						){
							// Neu co thi cong don estimated
							$listDelete[] = $refer['id'];
							$n_refer['estimated'] += $refer['estimated'];
							$listUpdate[] = $n_refer;
						}
					}
				}
				if( !empty($listDelete)){
					$this->NctWorkload->deleteAll(array(
						'NctWorkload.id' => $listDelete
					), false);
				}
				if( !empty($listUpdate)){
					foreach( $listUpdate as $elm){
						$this->NctWorkload->id = $elm['id'];
						$this->NctWorkload->save($elm);
					}
				}
			}
			$result = $this->NctWorkload->updateAll(
				array( 'NctWorkload.reference_id' => $new_pc), 
				array( 
					'NctWorkload.reference_id' =>  $old_pc,
					'NctWorkload.is_profit_center' => 1
				)
			);
		}
		/* END Move NCT workload */
		return $result;
	}
	private function moveChildPC($old_pc, $new_pc ){
		$old_childs = $this->ProfitCenter->find('list', array(
			'recursive' => -1,
			'conditions' => array('ProfitCenter.parent_id' => $old_pc),
			'fields' => array('id')
		));
		if( !empty($old_childs)){
			foreach( $old_childs as $child){
				$this->ProfitCenter->id = $child;
				$result = $this->ProfitCenter->save(array('parent_id' => $new_pc));
			}
		}
		return $result;
	}
	private function caculateProfitCenter($profitCenters = null){
        $references = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id')
            ));
        $employees = array();
        if(!empty($references)){
            foreach($references as $employ => $profit){
                if(!isset($employees[$profit])){
                    //do nothing
                }
                $employees[$profit][] = $employ;
            }
        }
        $TmpCaculateProfitCenter = ClassRegistry::init('TmpCaculateProfitCenter');
        $company = $this->employee_info['Company']['id'];
        if(!empty($employees)){
            foreach($employees as $profitId => $employee){
                $tmp = $TmpCaculateProfitCenter->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $profitId),
                    'fields' => array('id')
                ));
                if(!empty($tmp) && $tmp['TmpCaculateProfitCenter']['id']){
                    $TmpCaculateProfitCenter->id = $tmp['TmpCaculateProfitCenter']['id'];
                    $saved['total_employee'] = count($employee);
                    $TmpCaculateProfitCenter->save($saved);
                } else {
                    $saved['profit_center_id'] = $profitId;
                    $saved['total_employee'] = count($employee);
                    $saved['company_id'] = $company;
                    $TmpCaculateProfitCenter->create();
                    $TmpCaculateProfitCenter->save($saved);
                }
            }
        }
        return true;
    }
	public function movedata(){
		$a = time();
		set_time_limit(0);
		ignore_user_abort(true);
		$old_pc = (int)$this->data['oldpc'];
		$new_pc = (int)$this->data['newpc'];
		if( !($this->is_sas && ($this->_isItemsSameCompany( array($old_pc, $new_pc), 'ProfitCenter')) ) &&  (count( array($old_pc, $new_pc)) != $this->_isBelongToCompany(array($old_pc, $new_pc), 'ProfitCenter')) ){
			$this->_functionStop(false, $this->data, __('You have not permission to access this function', true), false, array('action' => 'index'));
		}
		$allUsing = $this->_profitCenterIsUsing($old_pc);
		$message = __('Move data failed', true);
		if( !empty($old_pc) && !empty($new_pc) ){
			$result = true;
			if( !empty($allUsing['employees'])){
				if( !$this->moveEmployees($old_pc, $new_pc)) $result = false;
			}
			if( !empty($allUsing['projects'])){
				if( !$this->moveProjects($old_pc, $new_pc)) $result = false;
			}
			if( !empty($allUsing['activities'])){
				if( !$this->moveActivities($old_pc, $new_pc)) $result = false;
			}
			if( !empty($allUsing['activitytasks'])){
				if( !$this->moveActivityTasks($old_pc, $new_pc)) $result = false;
			}
			if( !empty($allUsing['projectasks'])){
				if( !$this->moveProjectTasks($old_pc, $new_pc)) $result = false;
			}
			if( !empty($allUsing['childPC'])){
				if( !$this->moveChildPC($old_pc, $new_pc)) $result = false;
			}
			$this->caculateProfitCenter();
			$this->Session->setFlash( $message,  'error');
			if( $result ) {
				$message = __('Saved', true);
				$this->Session->setFlash( $message,  'success');
			}
		}
		die(json_encode(array(
			'result' => !empty($result) ? 'success' : 'failed',
			'data' => $allUsing,
			'message' => $message,
		)));
	}
    public function import(){
        if( !isset($this->employee_info['Company']['id']) ){
            $this->Session->setFlash(__('You do not belong to any companies to do this', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'importers' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $this->loadModels('Employee', 'Profile');
                $filename = TMP . 'uploads' . DS . 'importers' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Update' => array(),
                        'Error' => array()
                    );
                    $default = array(
                        'No.' => '',
                        'Profit Center' => '',
                        'Parent' => '',
                        'Analytical Reference' => '',
                        'Manager' => '',
                        'Backup Manager' => '',
                        'Average Daily Rate' => '',
                        'Profile' => '',
                    );
                    $validate = array('Profit Center');
                    $company_id = $this->employee_info['Company']['id'];
                    $profits = $this->ProfitCenter->find('list', array(
                        'conditions' => array(
                            'company_id' => $company_id
                        ),
                        'fields' => array('id', 'name')
                    ));
                    $nameRef = array_flip($profits);
                    $profiles = $this->Profile->find('list', array(
                        'conditions' => array(
                            'company_id' => $company_id
                        ),
                        'fields' => array('id', 'name')
                    ));
                    $profiles = array_flip($profiles);
                    $this->Employee->virtualFields = array('full_name' => "CONCAT(first_name, ' ', last_name)");

                    $data = array();

                    $resources = $this->Employee->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id
                        ),
                        'fields' => array('id', 'full_name')
                    ));
                    $resRef = array_flip($resources);
                    foreach ($csv->data as $row) {
                        $row = $this->changeKeys($row, $default);
                        array_walk($row, array($this, 'sstrim'));
                        $error = false;
                        $row = array_merge($default, $row, array('data' => array(), 'columnHighLight' => array(), 'error' => array(), 'description' => array()));
                        foreach ($validate as $key) {
                            if (empty($row[$key])) {
                                $row['error'][] = sprintf(__('The %s is not blank', true), $key);
                                $error = true;
                            }
                        }
                        if (!$error) {
                            $row['data']['company_id'] = $company_id;
                            //check profit center
                            if( in_array($row['Profit Center'], $profits) ){
                                $row['data']['id'] = $nameRef[ $row['Profit Center'] ];
                            }
                            $row['data']['name'] = $row['Profit Center'];
                            //check parent exists
                            $parentName = trim($row['Parent']);
                            if( $parentName ){
                                if( in_array($parentName, $profits) ){
                                    $row['data']['parent_id'] = $nameRef[ $parentName ];
                                }
                                else if( $parentName === $row['Profit Center'] ) {
                                    $error = true;
                                    $row['error'][] = __('Profit center and their parents must be unique', true);
                                    $row['columnHighLight']['Profit Center'] = '';
                                    $row['columnHighLight']['Parent'] = '';
                                } else {
                                    $row['data']['parent'] = $parentName;
                                    $row['error'][] = sprintf(__('Parent profit center \'%s\' will be created', true), $parentName);
                                }
                            }
                            //check profile
                            $profile = trim($row['Profile']);
                            if( !empty($profile) ){
                                if( !empty($profiles[$profile]) ){
                                    $row['data']['profile_id'] = $profiles[$profile];
                                }
                                //no profile found, indicate an error
                                else {
                                    $error = true;
                                    $row['columnHighLight']['Profile'] =  '';
                                    $row['error'][] = sprintf(__('The profile \'%s\' does not exist', true), $profile);
                                }
                            }
                            //check tjm 
                            $tjm = str_replace(array(',', ' ','\n','\r','\t','\v','\0'),'', trim($row['Average Daily Rate']));
                            if( is_numeric( $tjm) || $tjm == ''){
								$tjm = round( $tjm, 2 );
								$row['data']['tjm'] = $tjm;
								$row['Average Daily Rate'] = number_format($tjm, 2, '.', ' ');
							}
							else {
								$error = true;
								$row['columnHighLight']['Average Daily Rate'] =  '';
								$row['error'][] = sprintf(__('%s has to be a number ', true), __('Average Daily Rate', true)  );
							}
                            //check manager
                            $manager = trim($row['Manager']);
                            if( !empty($manager) ){
                                if( in_array($manager, $resources) ){
                                    $row['data']['manager_id'] = $resRef[ $manager ];
                                }
                                //no manager found, indicate an error
                                else {
                                    $error = true;
                                    $row['columnHighLight']['Manager'] =  '';
                                    $row['error'][] = sprintf(__('The manager \'%s\' does not exist', true), $manager);
                                }
                            }
                            //check backup
                            // $manager = trim($row['Backup Manager']);
							$managers = trim($row['Backup Manager']);
							if( !empty( $managers)){
								$managers = explode( ', ', $managers);
								foreach( $managers as $manager){
									if( !empty($manager) ){
										if( in_array($manager, $resources) ){
											$row['data']['manager_backup'][] = $resRef[ $manager ];
										}
										//no manager found, indicate an error
										else {
											$error = true;
											$row['columnHighLight']['Backup Manager'] =  '';
											$row['error'][] = sprintf(__('The backup manager \'%s\' does not exist', true), $manager);
										}
									}
								}
							}
                            //other values
                            $row['data']['analytical'] = $row['Analytical Reference'];
                        }
                        if( $error ){
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            if( !isset($row['data']['id']) ){
                                $records['Create'][] = $row;
                            } else {
                                $records['Update'][] = $row;
                            }
                        }
                    }
                }
				// exit;
                unlink($filename);
                $this->set('records', $records);
                $this->set('default', $default);
                return $this->render('import');
            }
        }
        $this->Session->setFlash(__('Wrong csv data', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
    public function get_import_template(){
		$default = array(
			__('No.', true),
			__('Profit Center', true),
			__('Parent', true),
			__('Analytical Reference' , true),
			__('Manager', true),
			__('Backup Manager', true),
			__('Average Daily Rate', true),
			__('Profile', true),
		);
		App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
		$csv = new parseCSV();
		$data = array();
		$this->loadModels('Employee', 'ProfitCenterManagerBackup', 'Profile');
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $companyId = $this->employee_info["Company"]["id"];
			$this->ProfitCenter->Behaviors->attach('Containable');
			$profits = $this->ProfitCenter->find('all', array(
				'conditions' => array(
					'ProfitCenter.company_id' => $companyId
				),
				'order' => array('ProfitCenter.name'),
				'contain' => array(
					'Profile',
					'ProfitCenterManagerBackup'
				),
				'fields' => array(
					'ProfitCenter.id',
					'ProfitCenter.name',
					'ProfitCenter.parent_id',
					'ProfitCenter.manager_id',
					'ProfitCenter.manager_name_firstname',
					'ProfitCenter.manager_name_lastname',
					'ProfitCenter.tjm',
					'ProfitCenter.profile_id',
					'ProfitCenter.analytical',
					'Profile.id',
					'Profile.name',
				)
			));
			$named = !empty( $profits)? Set::combine( $profits, '{n}.ProfitCenter.id', '{n}.ProfitCenter.name') : array();
			$parents = !empty( $profits)? Set::classicExtract( $profits, '{n}.ProfitCenter.parent_id') : array();
			$managerBackupLists = array();
			foreach($profits as $profit){
				if( !empty( $profit['ProfitCenterManagerBackup'])){
					foreach($profit['ProfitCenterManagerBackup'] as $e){
						$managerBackupLists[] = $e['employee_id'];
					}
				}
			}
			$managerBackups = $this->Employee->find('list', array(
				'recursive' => -1,
				'conditions' => array('id' => $managerBackupLists),
				'fields' => array('id', 'fullname')
			));
			$i = 0;
			foreach($profits as $profit){
				$_backups = array();
				if( !empty( $profit['ProfitCenterManagerBackup'])){
					foreach($profit['ProfitCenterManagerBackup'] as $e){
						$_backups[] = $managerBackups[$e['employee_id']];
					}
					
				}
				$_backups = implode( ', ', $_backups);
				$data[] = $this->_utf8_encode_mix(array(
					$i++, //No.
					$profit['ProfitCenter']['name'], //Profit Center
					$profit['ProfitCenter']['parent_id'] ? $named[$profit['ProfitCenter']['parent_id']] : '', //Parent
					$profit['ProfitCenter']['analytical'] ? $profit['ProfitCenter']['analytical'] : '', //__('Analytical Reference
					$profit['ProfitCenter']['manager_name_firstname']. ' ' .$profit['ProfitCenter']['manager_name_lastname'], //Manager
					$_backups, //Backup Manager
					$profit['ProfitCenter']['tjm'], //tjm
					$profit['Profile']['name'] ? $profit['Profile']['name'] : '', //profile_id
					
				));
			}
		}
		
		$csv->output("inport_profit_sample.csv", $data, $this->_mix_coloumn($default), ",");
		exit;
	}
    public function save_import(){
        if (!empty($this->data)) {
            extract($this->data['Import']);
			$err = array();
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
                $company_id = $this->employee_info['Company']['id'];
				$this->loadModel('Profile');
				$profiles = $this->Profile->find('list', array(
					'conditions' => array(
						'company_id' => $company_id
					),
					'fields' => array('id', 'name')
				));
                //load all profit
                foreach ($import as $data) {
                    //do save here
					if( empty( $data['profile_id']) ||( !empty($data['profile_id']) && !empty($profiles[$data['profile_id']]))){
						if( $this->savePC($data, $company_id) )$complete++;
					}else{
						$err[] = sprintf( __('The profile %s does not exist'));
					}
                }
                $this->Session->setFlash(sprintf(__('The profit centers have been imported %s/%s.', true) . ( !empty( $err) ? '<p>' . implode('</p><p>', $err) . '</p>' : ''), $complete, count($import)));
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

        // pr('-----------------------------------------------------');
        // pr($this->data);
        exit;
    }

    private function savePC($data, $cid){
        //kiem tra parent
        if( isset($data['parent']) ){
            //tim parent
            $this->ProfitCenter->recursive = -1;
            $parent = $this->ProfitCenter->find('first', array(
                'conditions' => array('name' => $data['parent'], 'company_id' => $cid)
            ));
            if( empty($parent) ){
                //tao parent
                $this->ProfitCenter->create();
                $this->ProfitCenter->save(array(
                    'name' => $data['parent'],
                    'company_id' => $cid
                ));
                $parent = $this->ProfitCenter->read(null);
            }
            $data['parent_id'] = $parent['ProfitCenter']['id'];
        }
        //luu PC
        if( !isset($data['id']) ){
            $this->ProfitCenter->create();
        } else {
            $this->ProfitCenter->id = $data['id'];
        }
        $res = $this->ProfitCenter->save($data);
		$p_id = $this->ProfitCenter->id;
		if( $res && !empty($data['manager_backup'])){
			$this->loadModels( 'ProfitCenterManagerBackup');
			$old_backup = $this->ProfitCenterManagerBackup->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $cid,
					'profit_center_id' => $p_id,
				),
				'fields' => array('id', 'employee_id')
			));
			$data_backup = $data['manager_backup'];
			// delete old
			foreach( $old_backup as $id => $b_manager){
				if( !in_array( $b_manager, $data_backup)){
					$this->ProfitCenterManagerBackup->id = $id;
					$this->ProfitCenterManagerBackup->delete();
				}
			}
			// Create new 
			$old_backup = array_values($old_backup);
			foreach( $data_backup as $b_manager){
				if( !in_array( $b_manager, $old_backup)){
					$this->ProfitCenterManagerBackup->create();
					$this->ProfitCenterManagerBackup->save(array(
						'profit_center_id' => $p_id,
						'company_id' => $cid,
						'employee_id' => $b_manager,
					));
				}
			}
		}
		return $res;
    }

    private function changeKeys($o, $d){
        $newKeys = array_keys($d);
        $oldKeys = array_keys($o);
        $result = array();
        for($i = 0; $i < count($oldKeys); $i++){
            $result[ $newKeys[$i] ] = $o[ $oldKeys[$i] ];
        }
        return $result;
    }
    public function sstrim(&$v){
        if( is_string($v) )$v = trim($v);
        return;
    }
    private function _utf8_encode_mix($input)
    {
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    private function _mix_coloumn($input){
        $result = array();
        foreach($input as $value){
            $result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
}