<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectTeamsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectTeams';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Excel');

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectTeam');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    public function index($project_id = null) {
        $this->_checkRole(false, $project_id);
		$this->loadModels('HistoryFilter');
        $this->_checkWriteProfile('team');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectTeam->Behaviors->attach('Containable');

        $this->ProjectTeam->cacheQueries = true;
        $this->ProjectTeam->ProjectFunctionEmployeeRefer->cacheQueries = true;
        $this->ProjectTeam->ProjectFunctionEmployeeRefer->Employee->cacheQueries = true;

        $projectTeams = $this->ProjectTeam->find("all", array(
            'fields' => array('id', 'price_by_date', 'work_expected', 'project_function_id', 'profit_center_id'),
            'contain' => array(
                'ProjectFunctionEmployeeRefer' => array(
                    'fields' => array('is_backup', 'profit_center_id', 'employee_id')
            )),
            "conditions" => array('project_id' => $project_id)));
	
        if (!$this->is_sas) {
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array(
                'company_id' => $projectName['Project']['company_id']), null, null, '--');
        } else {
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, '--');
        }
        $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
            'fields' => array(
                'ProjectFunction.id', 'ProjectFunction.name'
            ),
            "conditions" => array('ProjectFunction.company_id' => $projectName['Project']['company_id'])));

        $employees = $this->ProjectTeam->Project->Employee->CompanyEmployeeReference->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $projectName['Project']['company_id'])));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $employeeRefers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'AND' => array(
                        'NOT' => array('profit_center_id' => null),
                        'NOT' => array('profit_center_id' => 0)
                    )
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id'),
                'group' => array('employee_id')
            ));
		
        // profit center and employee of team duoc assign in task.
        $this->loadModel('ProjectTask');
        $getDatas = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
            'conditions' => array('ProjectTask.project_id' => $project_id),
            'fields' => array('reference_id', 'is_profit_center')
        ));
        $assignEmployees = $assignProfitCenters = array();
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['ProjectTaskEmployeeRefer'];
                if($dx['is_profit_center'] == 0){
                    //employee
                    $assignEmployees[] = $dx['reference_id'];
                } else {
                    //profit center
                    $assignProfitCenters[] = $dx['reference_id'];
                }
            }
        }
        $this->loadModel('CompanyConfig');
        $company_configs = $this->CompanyConfig->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'cf_name', 'cf_value'),
            'conditions' => array(
                'cf_name' => 'display_picture_all_resource',
                'company' =>  $projectName['Project']['company_id']
            )
        ));
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $display_picture = !empty($company_configs['CompanyConfig']['cf_value']) ? $company_configs['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('projectName', 'projectFunctions', 'projectTeams', 'profitCenters', 'project_id', 'employees', 'employeeRefers', 'assignEmployees', 'assignProfitCenters', 'display_picture', 'loadFilter'));
    }

    public function export($project_id = null) {
        $projectName = $this->ProjectTeam->Project->find('first', array(
            'recusive' => -1,
            'fields' => array('project_name', 'company_id'),
            'conditions' => array('Project.id' => $project_id)));
        if (empty($projectName)) {
            $this->Session->setFlash(sprintf(__('The project "#%s" was not found, please try again', true), $project_id), 'error');
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
        $conditions = array('project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectTeam.id'] = $data;
            }
        }

        $this->ProjectTeam->Behaviors->attach('Containable');

        $this->ProjectTeam->cacheQueries = true;
        $this->ProjectTeam->ProjectFunctionEmployeeRefer->cacheQueries = true;
        $this->ProjectTeam->ProjectFunctionEmployeeRefer->Employee->cacheQueries = true;

        $projectTeams = $this->ProjectTeam->find("all", array(
            'fields' => array('id', 'price_by_date', 'work_expected'),
            'contain' => array(
                'ProjectFunction' => array(
                    'id', 'name'
                ),
                'ProfitCenter' => array(
                    'id', 'name'
                ),
                'ProjectFunctionEmployeeRefer' => array(
                    'Employee' => array('id', 'first_name', 'last_name'), 'fields' => array('is_backup', 'profit_center_id')
            )),
            "conditions" => $conditions));
        $projectTeams = Set::combine($projectTeams, '{n}.ProjectTeam.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectTeams[$id])) {
                    unset($data[$id]);
                    unset($projectTeams[$id]);
                    continue;
                }
                $data[$id] = $projectTeams[$id];
            }
            $projectTeams = $data;
            unset($data);
        }
        $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array(
                'company_id' => $projectName['Project']['company_id']), null, null, '--');
        $this->set(compact('projectTeams', 'profitCenters'));
        $this->layout = '';
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $employeeRefers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'AND' => array(
                    'NOT' => array('profit_center_id' => null),
                    'NOT' => array('profit_center_id' => 0)
                )
            ),
            'fields' => array('employee_id', 'profit_center_id'),
            'order' => array('employee_id'),
            'group' => array('employee_id')
        ));
        if (!empty($this->data) && (!empty($this->data['profit_center_id']) && $this->data['profit_center_id'] != 0) ) {
            $this->ProjectTeam->create();
            if (!empty($this->data['id'])) {
                $this->ProjectTeam->id = $this->data['id'];
            }
			unset($this->data['id']);
			/**
			 * Tu ngay 16/11/2013 khong luu field profit_center_id vao tables Project team. Truoc do, ko luu field employee_id roi.
			 * Vi profit_center_id va employee_id la multiple selected trong combobox nen se duoc luu vao table ProjectFunctionEmployeeRefer
			 * Cot proft_center_id va employee_id trong table project_teams bo?
			 */
			if ($this->ProjectTeam->save(array_intersect_key($this->data, array('project_id' => '', 'project_function_id' => '', 'price_by_date' => '', 'work_expected' => '')))) {
				$result = true;
				$this->data['id'] = $this->ProjectTeam->id;
				$datas = $arrProfitCenters = array();
				if(!empty($this->data['employee_id'])){
					foreach ($this->data['employee_id'] as $employee) {
						$datas[] = array(
							'employee_id' => $employee,
							'profit_center_id' => !empty($employeeRefers[$employee]) ? $employeeRefers[$employee] : '',
							'function_id' => $this->data['project_function_id'],
							'project_team_id' => $this->ProjectTeam->id,
							'is_backup' => !empty($this->data['backup'][$employee])
						);
						$arrProfitCenters[] = !empty($employeeRefers[$employee]) ? $employeeRefers[$employee] : '';
					}
				}
				if(!empty($this->data['profit_center_id'])){
					foreach($this->data['profit_center_id'] as $profit){
						if(in_array($profit, $arrProfitCenters)){
							//do nothing
						} else {
							$datas[] = array(
								'profit_center_id' => $profit,
								'function_id' => $this->data['project_function_id'],
								'project_team_id' => $this->ProjectTeam->id
							);
						}
					}
				}
				$saveOlds = $this->ProjectTeam->ProjectFunctionEmployeeRefer->find('all', array(
					'fields' => array('id', 'function_id', 'employee_id', 'profit_center_id', 'is_backup', 'project_team_id'),
					'conditions' => array(
						'project_team_id' => $this->ProjectTeam->id
					),
					'recursive' => -1));
				$saved = Set::combine($saveOlds, '{n}.ProjectFunctionEmployeeRefer.employee_id', '{n}.ProjectFunctionEmployeeRefer');
				if(!empty($saveOlds)){
					foreach($saveOlds as $saveOld){
						$this->ProjectTeam->ProjectFunctionEmployeeRefer->delete($saveOld['ProjectFunctionEmployeeRefer']['id']);
					}
				}
				$this->ProjectEmployeeProfitFunctionRefer->cacheQueries = false;
				if(!empty($datas)){
					foreach($datas as $data){
						$this->ProjectTeam->ProjectFunctionEmployeeRefer->create();
						$this->ProjectTeam->ProjectFunctionEmployeeRefer->save($data);
						if(!empty($data['employee_id'])){
							unset($saved[$data['employee_id']]);
							$data = array_intersect_key($data, array('employee_id' => '', 'profit_center_id' => '', 'function_id' => ''));
							if(!empty($data['function_id'])){
								if (!$this->ProjectEmployeeProfitFunctionRefer->find('count', array(
											'conditions' => $data, 'recursive' => -1))) {
									$this->ProjectEmployeeProfitFunctionRefer->create();
									$this->ProjectEmployeeProfitFunctionRefer->save($data);
								}
							}
						}
					}
				}
				foreach($saved as $_save) {
					if($_save['employee_id'] != ''){
						if($_save['function_id'] != ''){
							$_save = array_intersect_key($_save, array('employee_id' => '', 'profit_center_id' => '', 'function_id' => ''));
							$this->ProjectEmployeeProfitFunctionRefer->deleteAll($_save);
						} else {
							//do nothing
						}
					}
				}
			} else {
				$this->Session->setFlash(__('The Project Team could not be saved. Please, try again.', true), 'error');
			}
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
    function delete($id = null, $project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project team', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if ($this->_checkRole(true, $project_id)) {
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $getData = $this->ProjectTeam->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTeam.id' => $id, 'project_id' => $project_id),
                    'fields' => array('project_function_id')
                ));
            $checkTeamFunctionReferEmployees = array();
            if(!empty($getData)){
                $projectFunctionId = $getData['ProjectTeam']['project_function_id'];
                $employees = $this->ProjectEmployeeProfitFunctionRefer->find('all', array(
                        'recursive' => -1,
                        'conditions' => array('function_id' => $projectFunctionId),
                        'fields' => array('id', 'employee_id', 'profit_center_id', 'function_id')
                    ));
                $checkTeamFunctionReferEmployees = $employees;
            }
            $this->loadModel('ProjectFunctionEmployeeRefer');
            $datas = $this->ProjectFunctionEmployeeRefer->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_team_id' => $id),
                    'fields' => array('id', 'employee_id', 'profit_center_id', 'function_id')
                ));
            $listEmploys = !empty($datas) ? Set::classicExtract($datas, '{n}.ProjectFunctionEmployeeRefer.employee_id') : array();
            $listPcs = !empty($datas) ? Set::classicExtract($datas, '{n}.ProjectFunctionEmployeeRefer.profit_center_id') : array();
            $this->loadModel('ProjectTaskEmployeeRefer');
            $this->loadModel('ProjectTask');
            $tasks = $this->ProjectTask->find('list', array(
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id')
            ));
            $profits = $this->ProjectTaskEmployeeRefer->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $tasks,
                        'OR' => array(
                            'AND' => array(
                                'reference_id' => $listPcs,
                                'is_profit_center' => 1
                            )
                        )
                    )
                ));
             $employes = $this->ProjectTaskEmployeeRefer->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $tasks,
                        'OR' => array(
                            'AND' => array(
                                'reference_id' => $listEmploys,
                                'is_profit_center' => 0
                            )
                        )
                    )
                ));
            if($profits != 0 || $employes != 0){
                $this->Session->setFlash(__('Resource/Profit Center assigned to a task', true), 'error');
            } else {
                if ($this->ProjectTeam->delete($id)) {
                    if(!empty($checkTeamFunctionReferEmployees)){
                        foreach($checkTeamFunctionReferEmployees as $checkTeamFunctionReferEmployee){
                            $dx = $checkTeamFunctionReferEmployee['ProjectEmployeeProfitFunctionRefer'];
                            if(!empty($dx['function_id']) && !empty($dx['employee_id'])){
                                //$this->ProjectEmployeeProfitFunctionRefer->delete($dx['id']); // cho nay chua hieu ne. Nhung yeu cau cua khach hang khong xoa skill ben employee profile nen comment lai
                            }
                        }
                    }
                    $this->Session->setFlash(__('Project team deleted', true), 'success');
                    $this->redirect(array('action' => 'index', $project_id));
                }
                $this->Session->setFlash(__('Project team was not deleted', true), 'error');
            }
        }
        $this->redirect(array('action' => 'index', $project_id));
    }

    /**
     * get employee by profit center and project functions
     * Export to Excel
     *
     * @return void
     * @access public
     */
    function get_employees($project_id = null) {
        $this->_checkRole(false, $project_id);
        extract(array_merge(array(
                    'profit' => null,
                    'funcs' => null,
                    'id' => null), $this->params['url']));
        $conditions = array();
        if (!empty($funcs)) {
            $conditions['ProjectEmployeeProfitFunctionRefer.function_id'] = $funcs;
        }
        if (!empty($profit)) {
            $conditions['ProjectEmployeeProfitFunctionRefer.profit_center_id'] = $profit;
        }
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('CompanyEmployeeReference');

        if (empty($conditions)) {
            $employees = $this->CompanyEmployeeReference->find('all', array(
                'order' => array('Employee.last_name' => 'asc'),
                'fields' => array('employee_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $this->viewVars['projectName']['Project']['company_id'])));
            $employees = Set::classicExtract($employees, '{n}.CompanyEmployeeReference.employee_id');
            $conditions['ProjectEmployeeProfitFunctionRefer.employee_id'] = $employees;
        } elseif (!empty($id)) {
            $conditions = array(
                'or' => array(
                    $conditions,
                    'ProjectEmployeeProfitFunctionRefer.employee_id' => $id
                )
            );
        }
        $datas = $this->ProjectEmployeeProfitFunctionRefer->find('all', array(
                    'conditions' => $conditions,
                    'fields' => array('Employee.id,Employee.first_name,Employee.last_name', 'profit_center_id'),
                    'group' => 'employee_id',
                    'order' => array('Employee.first_name,Employee.last_name' => 'ASC')
                ));
        $this->set('datas', $datas);
        if (!empty($profit)) {
            $this->set('profitName', $this->ProjectTeam->ProfitCenter->find('first', array(
                        'conditions' => array('id' => $profit),
                        'recursive' => -1,
                        'fields' => array('name'),
                        'order' => array('name' => 'ASC')
                    )));
        }
        $this->layout = false;
        $this->set('funcs', $funcs);
    }

    /**
     *
     *
     *
     * @return void
     * @access public
     */

     public function getProfitCenter($project_id = null){
        $this->layout = 'ajax';
        $this->_checkRole(false, $project_id);
        extract(array_merge(array(
                    'employ' => null,
                    'funcs' => null,
                    'id' => null), $this->params['url']));
		$projectTeams = $this->ProjectTeam->find("all", array(
            'fields' => array('id'),
            'contain' => array(
                'ProjectFunctionEmployeeRefer' => array(
                    'field' => array('ProjectFunctionEmployeeRefer.profit_center_id')
            )),
            "conditions" => array('project_id' => $project_id)));
		
		$profitCenter = array();
		if(!empty($projectTeams)){
			foreach($projectTeams as $key => $teams){
				$teamRefers = $teams['ProjectFunctionEmployeeRefer'];
				if(!empty($teamRefers)){
					foreach($teamRefers as $index => $teamRefer){
						$profitCenter[] = $teamRefer['profit_center_id'];
					}
				}
			}
		}
		$profitCenter = array_unique($profitCenter);
		
        $projectName = $this->viewVars['projectName'];
        $datas = array();
        if (!$this->is_sas) {
            $datas = $this->ProjectTeam->ProfitCenter->generateTreeList(array(
                'company_id' => $projectName['Project']['company_id']), null, null, '--');
        } else {
            $datas = $this->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, '--');
        }
		
        $this->set(compact('datas', 'profitCenter'));
        $this->layout = false;
        $this->set('funcs', $funcs);
     }


    /**
     * Trong Project Team co mot so employee thuoc nhieu profit center khac nhau.
     * Tim cac employee do va dieu chinh lai cho dung profit center: 1 employee chi co 1 profit center.
     * Dieu chinh table: project_function_employee_refers
     * Thay doi ngay: 16/11/2013
     *
     * @author HuuPC
     * @access public
     * @return void
     */
     public function updateEmployeeOfProfitCenter(){
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $employeeRefers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'AND' => array(
                        'NOT' => array('profit_center_id' => null),
                        'NOT' => array('profit_center_id' => 0)
                    )
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id'),
                'group' => array('employee_id')
            ));
        if(!empty($employeeRefers)){
            foreach($employeeRefers as $employee => $profitCenter){
                $this->ProjectFunctionEmployeeRefer->updateAll(
                    array('ProjectFunctionEmployeeRefer.profit_center_id' => $profitCenter),
                    array('ProjectFunctionEmployeeRefer.employee_id' => $employee)
                );
            }
        }
        echo 'Finish!';
        exit;
     }
}
?>
