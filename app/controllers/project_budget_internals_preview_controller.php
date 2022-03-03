<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetInternalsPreviewController extends AppController {
    /**
     *
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ activity_budget_internals_controller --------------------------
     * 2 CONTROLLER NAY CO LIEN KET VOI NHAU
     * ----------CHU Y-----CHU Y-----VA CHU Y-----------------------
     *
     */
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    //var $layout = 'administrators';

    var $name = 'ProjectBudgetInternalsPreview';
    var $uses = array('ProjectBudgetInternals');

  
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'Lib');

     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
		// $this->loadModel('ProjectBudgetSyn');
		// $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
        $this->loadModels('ProjectBudgetInternal', 'CompanyConfigs', 'Profile', 'ProfitCenter');
		$usCanWrite = ($this->employee_info['Role']['name'] == 'admin') || $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('internal_cost');
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetInternalDetail');
		$cf_name = array('budget_euro_fill_manual', 'average_euro_fill_manual', 'consumed_used_timesheet', 'average_cost_default', 'manual_consumed');
		$companyConfigs = $this->CompanyConfigs->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $this->employee_info['Company']['id'],
				'cf_name' => $cf_name,
            ),
            'fields' => array('cf_name', 'cf_value')
        ));
		$consumed_used_timesheet = isset($companyConfigs['consumed_used_timesheet']) ? $companyConfigs['consumed_used_timesheet'] : 0;
		$useManualConsumed = isset($companyConfigs['manual_consumed']) ? intval($companyConfigs['manual_consumed']) : 0;
		$is_use_tjm_project = 0;
        $projectName = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $project_id)
		));
        $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
		$budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
		
		//Get data average (TJM project)
		$this->loadModel('ProjectBudgetSyn');
		$valBudgetSynsInternal = $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
		$valBudgetSyns = !empty($valBudgetSynsInternal['internal_costs_average']) ? ( (float)$valBudgetSynsInternal['internal_costs_average']) : 0;
		
        $this->loadModel('ProjectBudgetExternal');
        $externalBudgets = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('SUM(man_day) AS exBudget')
        ));
        $externalBudgets = !empty($externalBudgets) && !empty($externalBudgets[0][0]['exBudget']) ? $externalBudgets[0][0]['exBudget'] : 0;
        $engagedErro = 0;
		// Update value consumed euro when update tjm project in slickgid view
		
		if( $useManualConsumed){
			$getDataProjectTasks = $this->Project->dataFromProjectTaskManualConsumed($project_id);
			$externalConsumeds = $getDataProjectTasks['exConsumed'];
			$engagedErro = $getDataProjectTasks['consumed'] * $valBudgetSyns;
		}else{
			$getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
			// debug(  $getDataProjectTasks ); exit;
			$this->loadModel('ProjectTask');
			$projectTasks = $this->ProjectTask->find('all', array(
				'fields' => array(
					'SUM(ProjectTask.estimated) AS Total',
					'SUM(ProjectTask.special_consumed) AS exConsumed'
				),
				'recursive' => -1,
				'conditions' => array('project_id' => $project_id, 'special'=>1)
			));
			$externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
			/* do not take into account overload 
			Ticket 526 https://nextversion.z0gravity.com/tickets/view/526#comment-5675
			Huynh Edit 2020-04-08
			*/
			// $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
			// $getDataProjectTasks['workload'] -= $varE;
			//Fix loi tinh ca workload cua task parent trong internal. Ticket #1002
			$parentTasks = $this->ProjectTask->find('list', array(
			   'recursive' => -1,
			   'conditions' => array(
				   'project_id' => $project_id,
				   'parent_id >' => 0,
			   ),
			   'fields' => array('parent_id', 'parent_id'),
			   'group' => array('parent_id')
			));
			$parentTasks = !empty($parentTasks) ? array_values($parentTasks) : array();
			
			$totalWorkload = $this->ProjectTask->find('all', array(
				'fields' => array(
					'SUM(ProjectTask.estimated) AS Total',
				),
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
					'special'=> 0,
					'NOT' => array(
						'id' => $parentTasks
					)
				)
			));
			$getDataProjectTasks['workload'] = !empty($totalWorkload[0][0]['Total']) ? $totalWorkload[0][0]['Total'] : 0;
			
			 /* END do not take into account overload */
			if(!empty($projectName['Project']['activity_id'])){
				$activityId = $projectName['Project']['activity_id'];
				if($consumed_used_timesheet){
					/*
						Created by Viet Nguyen 
						Ticket #526
						Sum all lines one by one 
						If cost_price_resource > 0 then use cost_price_resource
						else if cost_price_profil use cost_price_profil
						else if cost price team > 0 then use cost price team
						else use the TJM of project ( TJM of project * comsumed M.D )
					*/
					$costConsumedOfProject = $this->_parseCost($activityId);
					$cost_price_resource = !empty($costConsumedOfProject['cost_price_resource']) ? $costConsumedOfProject['cost_price_resource'] : 0;
					$cost_price_profil = !empty($costConsumedOfProject['cost_price_profil']) ? $costConsumedOfProject['cost_price_profil'] : 0;
					$cost_price_team = !empty($costConsumedOfProject['cost_price_team']) ? $costConsumedOfProject['cost_price_team'] : 0;
					$consumed_md = !empty($costConsumedOfProject['consumed_md']) ? $costConsumedOfProject['consumed_md'] : 0;
									
					if($cost_price_resource > 0){
						$engagedErro = $cost_price_resource;
					}else if($cost_price_profil > 0){
						$engagedErro = $cost_price_profil;
					}else if($cost_price_team > 0){
						$engagedErro = $cost_price_team;
					}else{
						// TJM of Project
						$project_tjm = $valBudgetSyns;
						$engagedErro = $consumed_md * $project_tjm;
						$is_use_tjm_project = 1;
					}
				}else{
					/*
						Modified Viet Nguyen 
						Ticket #526
						If TJM of resource is filled then use the TJM of the resource
						else if TJM of profil is filled then use TJM of profil
						else if TJM of TEAM is filled then used TJM of TEAM
						else use the TJM of the project 
					*/
					$getDataActivities = $this->_parse($activityId);
					$sumEmployees = $getDataActivities['sumEmployees'];
					// TJM of resource
					$resources = $getDataActivities['employees'];
					$employees = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee') : array();
					
					// TJM of profil
					$profiles = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee.profile_id') : array();
					$profiles_tjm = array();
					if(!empty($profiles)){
						$profiles_tjm =  $this->Profile->find(
							'all', array(
								'recursive' => -1,
								'conditions' => array('id' => $profiles),
								'fields' => array('id', 'tjm'), 
							)
						);
						$profiles_tjm = !empty($profiles_tjm) ? Set::combine($profiles_tjm, '{n}.Profile.id', '{n}.Profile.tjm') : array();
					}
					// TJM of TEAM
					$profit_centers = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee.profit_center_id') : array();
					$team_tjm = array();
					if(!empty($profit_centers)){
						$team_tjm =  $this->ProfitCenter->find(
							'all', array(
								'recursive' => -1,
								'conditions' => array('id' => $profit_centers),
								'fields' => array('id', 'tjm'), 
							)
						);
						$team_tjm = !empty($team_tjm) ? Set::combine($team_tjm, '{n}.ProfitCenter.id', '{n}.ProfitCenter.tjm') : array();
					}
				
					// TJM of Project
					$project_tjm = $valBudgetSyns;
					$count = 0;
					$engagedErroEmp = $engagedErroProfile = $engagedErroTeam = $totalValProject = 0;
					if (isset($sumEmployees[$activityId])) {
						foreach ($sumEmployees[$activityId] as $id => $val) {
							$reals = 0;
							if(!empty($employees[$id]['tjm'])){
								$reals = $employees[$id]['tjm'];
								$engagedErroEmp += $val * $reals;
							}else if(!empty($profiles_tjm[$employees[$id]['profile_id']])){
								$reals = $profiles_tjm[$employees[$id]['profile_id']];
								$engagedErroProfile += $val * $reals;
							}else if(!empty($team_tjm[$employees[$id]['profit_center_id']])){
								$reals = $team_tjm[$employees[$id]['profit_center_id']];
								$engagedErroTeam += $val * $reals;
							}else{
								$reals = $project_tjm;
								$totalValProject += $val;
							}
							$engagedErro += $val * $reals;
						}
					}
				}
			}
		}
        if($projectName && $projectName['Project']['category'] == 2){
        }
        $this->loadModel('ProfitCenter');
        $profits = $this->ProfitCenter->generateTreeList(array('company_id' => $projectName['Project']['company_id']), null, null, '');

        $this->loadModel('BudgetFunder');
        $funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id','name')
        ));
		
		$this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
		
        //Lay Budget_settings
        $this->loadModel('BudgetSetting');
        $company_id=$this->employee_info['Company']['id'];
        $budget_settingst=$this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id ,
                'currency_budget' =>1,
            ),
            'fields' => array('name',)
        ));
         $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $employee_info = $this->employee_info;
		$this->progress_line($project_id);
        // kiem tra xem PM dc chinh sua budget internal hay ko?
        $modifyBudget = $this->checkModifyBudget();
        $modifyBudget = $modifyBudget && $usCanWrite;
		// var_dump($getDataProjectTasks); exit;
        $this->set(compact('useManualConsumed', 'projectName', 'budgets', 'project_id', 'engagedErro', 'getDataProjectTasks', 'activityLinked', 'externalBudgets', 'externalConsumeds','profits','funders', 'modifyBudget', 'employee_info','budget_settings', 'is_use_tjm_project', 'engagedErroEmp', 'engagedErroProfile', 'engagedErroTeam', 'totalValProject', 'consumed_used_timesheet', 'valBudgetSyns', 'valBudgetSynsInternal', 'history'));
    }

    /**
     * Update
     *
     * @return void
     * @access public
     */
    function update($id = null) {
        if ($this->data) {
            $last = $this->ProjectBudgetInternal->find('first', array(
                'fields' => 'id',
                'conditions' => array('project_id' => $this->data['project_id']),
                'recursive' => -1));
            $this->ProjectBudgetInternal->create();
            if ($last) {
                $this->ProjectBudgetInternal->id = $last['ProjectBudgetInternal']['id'];
            }
            $this->data['average_daily_rate'] = floatval(str_replace(",", ".", $this->data['average_daily_rate']));
            $this->ProjectBudgetInternal->save($this->data);
        }
        exit();
    }

    /**
     * update detail
     *
     * @return void
     * @access public
     */
    public function update_detail() {
        $this->loadModel('ProjectBudgetInternalDetail');
        $result = false;
        $this->layout = false;
		$dataSync = array();
        if (!empty($this->data)) {
            $this->ProjectBudgetInternalDetail->create();
            if (!empty($this->data['id'])) {
                $this->ProjectBudgetInternalDetail->id = $this->data['id'];
                $provision = ClassRegistry::init('ProjectBudgetProvisional');
                $provision->virtualFields['total_value'] = 'SUM(value)';
                $total = $provision->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $this->data['project_id'],
                        'model' => 'Internal',
                        'model_id' => $this->data['id']
                    ),
                    'fields' => array('view', 'total_value'),
                    'group' => array('view')
                ));
                if( isset($total['man-day']) ){
                    $md = floatval($this->data['budget_md']);
                    if( $md < $total['man-day'] ){
                        $result = false;
                        $this->set(compact('result', 'dataSync'));
                        $this->Session->setFlash(
                            sprintf(__('Budget man day (%s) < provisional budget man day (%s)', true), $md, $total['man-day']),
                            'error'
                        );
                        $this->render();
                        return;
                    }
                }
                if( isset($total['euro']) ){
                    if( $this->data['average'] && $this->data['budget_md'] ){
                        $eur = floatval( $this->data['average'] * $this->data['budget_md'] );
                        if( $eur < $total['euro'] ){
                            $result = false;
							$this->set(compact('result', 'dataSync'));
                            $this->Session->setFlash(
                                sprintf(__('Budget euro (%s) < provisional budget euro (%s)', true), $eur, $total['euro']),
                                'error'
                            );
                            $this->render();
                            return;
                        }
                    }
                }
            }
            $data = array();
            foreach (array('validation_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectBudgetInternalDetail->convertTime($this->data[$key]);
                }
            }
			
            unset($this->data['id']);
            if ($this->ProjectBudgetInternalDetail->save(array_merge($this->data, $data))) {
                $result = true;
                //Added by QN on 2015/02/09
                $projectName = $this->ProjectBudgetInternalDetail->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $this->data['project_id'])));
				$this->loadModel('ProjectBudgetSyn');
				$dataSync = $this->ProjectBudgetSyn->updateBudgetSyn($this->data['project_id']);
                $this->writeLog($this->data, $this->employee_info, sprintf('Update internal budget `%s` for project `%s`', $this->data['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->data['id'] = $this->ProjectBudgetInternalDetail->id;
        } else {
            $this->Session->setFlash(__('Not saved', true), 'error');
        }
        $this->set(compact('result', 'dataSync'));
    }

    public function updateBudgetSyn($project_id) {
		$this->loadModel('ProjectBudgetSyn');
		$result = $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
		die( json_encode($result));
		exit;
	}
    public function delete($id = null, $project_id = null) {

        $this->loadModels('ProjectBudgetInternalDetail', 'ProjectBudgetProvisional', 'Project', 'ProjectBudgetInternal', 'ProjectBudgetSyn');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project budget internal costs', true));
            $this->redirect(array('action' => 'index', $project_id));
        }
        $last = $this->ProjectBudgetInternalDetail->read(null, $id);
		$project_id = !empty( $last) ? $last['ProjectBudgetInternalDetail']['project_id'] : 0;
		$usCanWrite = $this->_checkRole(false, $project_id);
        $modifyBudget = $this->checkModifyBudget();
        if(!($usCanWrite && $modifyBudget)){
            $this->Session->setFlash(__('You have not permission to access this function', true));
            $this->redirect(array('action' => 'index', $project_id));
        }
        $this->ProjectBudgetInternalDetail->recursive = -1;
        $checkProvisionals = $this->ProjectBudgetProvisional->find('count', array(
            'recursive' => -1,
            'conditions' => array('model' => 'Internal', 'model_id' => $id, 'NOT' => array('value IS NULL'))
        ));

        if($checkProvisionals != 0){
            $this->Session->setFlash(__('Data filled in provisional screen', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if ($this->ProjectBudgetInternalDetail->delete($id)) {
            $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.model' => 'Internal', 'ProjectBudgetProvisional.model_id' => $id), false);
            /**
             * kiem tra xem project co linked voi activity ko. Co thi lay id
             */
            $activity_id = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('activity_id')
            ));
            $datas = array(
                'project_id' => $project_id,
                'activity_id' => !empty($activity_id) && !empty($activity_id['Project']['activity_id']) ? $activity_id['Project']['activity_id'] : 0
            );
            $this->ProjectBudgetInternalDetail->saveInternalDetailToSyns($datas);
            $projectName = $this->ProjectBudgetInternalDetail->Project->find("first", array(
                'recursive' => -1,
                "fields" => array('project_name', 'company_id'),
                'conditions' => array('Project.id' => $project_id)));
            $this->writeLog($this->data, $this->employee_info, sprintf('Delete internal budget `%s` of project `%s`', $last['ProjectBudgetInternalDetail']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
			$this->ProjectBudgetSyn->updateBudgetSyn($project_id);
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect(array('action' => 'index', $project_id));
        }
        $this->Session->setFlash(__('The Budget Internal Costs was not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $project_id));
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
     protected function _parseCost($activity_id) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->loadModels('ActivityRequest', 'ActivityTask');
        $employees = $sumEmployees = $sumActivities = array();
		// Get data consumed of only activity
        $data_activity = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
					'SUM(value) as value',
                    'SUM(cost_price_resource) as cost_price_resource',
                    'SUM(cost_price_profil) as cost_price_profil',
                    'SUM(cost_price_team) as cost_price_team',
                ),
            'group' => array('activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $activity_id,
                'company_id' => $employeeName['company_id'],
            ))
        );
		
		// Get data consumed of only activity tasks
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'activity_id')
        ));
        $groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $_data_activity_tasks = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
					'SUM(value) as value',
                    'SUM(cost_price_resource) as cost_price_resource',
                    'SUM(cost_price_profil) as cost_price_profil',
                    'SUM(cost_price_team) as cost_price_team',
                ),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'task_id' => $groupTaskId,
                'company_id' => $employeeName['company_id'])
            )
        );
		$setDatas = array();
		if(!empty($data_activity)){
			foreach($data_activity as $key => $activity){
				$values = $activity[0];
				//sum cost_price_resource
				if(!isset($setDatas['cost_price_resource'])) $setDatas['cost_price_resource'] = 0;
				$setDatas['cost_price_resource'] += $values['cost_price_resource'];
				
				// sum cost_price_profil
				if(!isset($setDatas['cost_price_profil'])) $setDatas['cost_price_profil'] = 0;
				$setDatas['cost_price_profil'] += $values['cost_price_profil'];
				
				// sum cost_price_team
				if(!isset($setDatas['cost_price_team'])) $setDatas['cost_price_team'] = 0;
				$setDatas['cost_price_team'] += $values['cost_price_team'];
				
				// sum consumed_md
				if(!isset($setDatas['consumed_md'])) $setDatas['consumed_md'] = 0;
				$setDatas['consumed_md'] += $values['value'];
			}
		}
		
		if(!empty($_data_activity_tasks)){
			foreach($_data_activity_tasks as $key => $activity_task){
				$values = $activity_task[0];
				//sum cost_price_resource
				if(!isset($setDatas['cost_price_resource'])) $setDatas['cost_price_resource'] = 0;
				$setDatas['cost_price_resource'] += $values['cost_price_resource'];
				
				// sum cost_price_profil
				if(!isset($setDatas['cost_price_profil'])) $setDatas['cost_price_profil'] = 0;
				$setDatas['cost_price_profil'] += $values['cost_price_profil'];
				
				// sum cost_price_team
				if(!isset($setDatas['cost_price_team'])) $setDatas['cost_price_team'] = 0;
				$setDatas['cost_price_team'] += $values['cost_price_team'];
				
				// sum consumed_md
				if(!isset($setDatas['consumed_md'])) $setDatas['consumed_md'] = 0;
				$setDatas['consumed_md'] += $values['value'];
			}
		}
        return $setDatas;
    }
	/**
     * Index
     *
     * @return void
     * @access public
     */
     protected function _parse($activity_id) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->loadModel('ActivityRequest');
        $employees = $sumEmployees = $sumActivities = array();
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $activity_id,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'activity_id')
        ));
        $groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $_datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'task_id' => $groupTaskId,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $_sumActivitys = $_sumEmployees = array();
        foreach($_datas as $_data){
            foreach($activityTasks as $activityTask){
                if($_data['ActivityRequest']['task_id'] == $activityTask['ActivityTask']['id']){
                    $_sumActivitys[$activityTask['ActivityTask']['activity_id']][] = $_data[0]['value'];
                }
            }
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach($activityTasks as $activityTask){
            foreach($_sumEmployees as $id => $_sumEmployee){
                if($activityTask['ActivityTask']['id'] == $id){
                    $dataFromEmployees[$activityTask['ActivityTask']['activity_id']][] = $_sumEmployee;
                }
            }
        }
        $rDatas = array();
        if(!empty($dataFromEmployees)){
            foreach($dataFromEmployees as $id => $dataFromEmployee){
                foreach($dataFromEmployee as $values){
                    foreach($values as $employ => $value){
                        if(!isset($rDatas[$id][$employ])){
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
        }
        foreach($_sumActivitys as $k => $_sumActivity){
            $_sumActivitys[$k] = array_sum($_sumActivitys[$k]);
        }
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $_dataFromEmployees = array();
        if(!empty($rDatas)){
            foreach($rDatas as $id => $rData){
                if(in_array($id, array_keys($sumEmployees))){

                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if(!empty($sumEmployees)){
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if(!empty($rDatas)){
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
		$listEmployee = array();
        if(!empty($sumEmployGroups)){
            foreach($sumEmployGroups as $key => $sumEmployGroup){
                foreach($sumEmployGroup as $acId => $values){
                    foreach($values as $employs => $value){
                        if(!isset($sumEmployees[$acId][$employs])){
                            $sumEmployees[$acId][$employs] = 0;
							$listEmployee[] =  $employs;
                        }
                        $sumEmployees[$acId][$employs] += $value;
                    }
                }
            }
        }
		$employees = $this->ActivityRequest->Employee->find(
			'all', array(
				'recursive' => -1,
				'conditions' => array('id' => $listEmployee),
				'fields' => array('id', 'tjm', 'profile_id', 'profit_center_id'), 
			)
		);
		
        $setDatas = array();
        $setDatas['sumEmployees'] = !empty($sumEmployees) ? $sumEmployees : array();
        $setDatas['employees'] = !empty($employees) ? $employees : array();

        return $setDatas;
    }
	function setting(){
		$this->loadModel('BudgetSetting');
		$currency = $this->BudgetSetting->find("first", array(
			'recursive' => -1,
			"conditions" => array(
				'company_id' => $this->employee_info['Company']['id'],
				'currency_budget' => 1,
			),
			'fields' => array('name')
		));
		$currency = !empty($currency) ? $currency['BudgetSetting']['name'] : '&euro;';

		$this->set(compact('currency'));
	}
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }
	
	function update_average_daily(){
		$result = false;
		$this->loadModel('ProjectBudgetInternalDetails');
		if(!empty($_POST)){
			$average_val = $_POST['average_val'];
			$project_id = $_POST['project_id'];
			$listBudgets = $this->ProjectBudgetInternalDetails->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('budget_md')
			));
			foreach ($listBudgets as $key => $val) {
				$this->ProjectBudgetInternalDetails->updateAll(
					array('ProjectBudgetInternalDetails.average' => $average_val,
						'ProjectBudgetInternalDetails.budget_erro' => $average_val * $val
					),
					array(
						'ProjectBudgetInternalDetails.id' => $key
					)
				);
			}
			$result = true;
		}
		die(json_encode($result));
	}
	function update_value_project_budget_sync(){
		//dong bo cac gia tri trong man hinh budget Internal khi thuc hien thay doi vao table project_budget_sync
		// project list lay gia data tu table project_budget_sync de hien thi.
		$result = false;
		$this->loadModels('ProjectBudgetSyns', 'ProjectBudgetInternalDetail');
		if(!empty($_POST)){
			$options = 1;
			if(!empty($_POST['budget_id'])){
				$provision = ClassRegistry::init('ProjectBudgetProvisional');
                $provision->virtualFields['total_value'] = 'SUM(value)';
                $total = $provision->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $_POST['project_id'],
                        'model' => 'Internal',
                        'model_id' => $_POST['budget_id']
                    ),
                    'fields' => array('view', 'total_value'),
                    'group' => array('view')
                ));
				if( isset($total['man-day']) ){
                    $md = floatval($_POST['budget_md_val']);
                    if( $md < $total['man-day'] ){
                        $options = 0;
                    }
                }
				if( isset($total['euro']) ){
                    if( $_POST['budget_ave_val'] && $_POST['budget_md_val'] ){
                        $eur = floatval( $_POST['budget_ave_val'] * $_POST['budget_md_val'] );
                        if( $eur < $total['euro'] ){
                            $options = 0;
                        }
                    }
                }
			}
			if( $options == 1){
				$totalBudgetEuros = $_POST['totalBudgetEuros'];
				$totalForecastEuro = $_POST['totalForecastEuro'];
				$totalVarEuros = $_POST['totalVarEuros'];
				$totalEngagedEuro = $_POST['totalEngagedEuro'];
				$totalRemainEuro = $_POST['totalRemainEuro'];
				$totalBudgetMds = $_POST['totalBudgetMds'];
				$totalForecastMd = $_POST['totalForecastMd'];
				// $totalVarMds = $_POST['totalVarMds'];
				$totalVarMds = ($_POST['totalVarMds']) ? $_POST['totalVarMds'] : 0;
				$totalEngagedMd = $_POST['totalEngagedMd'];
				$totalRemainMd = $_POST['totalRemainMd'];
				$totalAverDailyRate = $_POST['totalAverDailyRate'];
				$project_id = $_POST['project_id'];
				$listBudgetSyns = $this->ProjectBudgetSyns->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $project_id
					),
					'fields' => array('project_id')
				));
				foreach ($listBudgetSyns as $key => $val) {
					$this->ProjectBudgetSyns->updateAll(
						array(
							'ProjectBudgetSyns.internal_costs_budget' => $totalBudgetEuros,
							'ProjectBudgetSyns.internal_costs_forecast' => $totalForecastEuro,
							'ProjectBudgetSyns.internal_costs_var' => $totalVarEuros,
							'ProjectBudgetSyns.internal_costs_engaged' => $totalEngagedEuro,
							'ProjectBudgetSyns.internal_costs_remain' => $totalRemainEuro,
							'ProjectBudgetSyns.internal_costs_budget_man_day' => $totalBudgetMds,
							'ProjectBudgetSyns.internal_costs_forecasted_man_day' => $totalForecastMd,
							// 'ProjectBudgetSyns.internal_costs_engaged_md' => $totalEngagedMd,
							'ProjectBudgetSyns.internal_costs_average' => $totalAverDailyRate
						),
						array(
							'ProjectBudgetSyns.project_id' => $val
						)
					);
				}
			}
			$result = true;
		}
		die(json_encode($result));
	}
	private function progress_line($project_id){
		if( !empty($this->companyConfigs['manual_consumed']) && $this->companyConfigs['manual_consumed'] != '0'){
			$internal_data = $this->ProjectBudgetInternal->progress_line_manual_consumed($project_id, $this->employee_info['Company']['id']);
			$this->set($internal_data);
			return true;
		}
        $staffings = array();
		$staffings = $this->_staffingEmloyee($project_id);
		$dataset_internals = array();
		$dataset_internals = $this->_totalStaffing($staffings, $project_id);
		$this->loadModel('ProjectBudgetInternalDetail', 'ProjectBudgetSyn');
		$valueInternals = $this->ProjectBudgetInternalDetail->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('validation_date', 'budget_md', 'budget_erro')
		));
		$projectBudgetSyn = $this->ProjectBudgetSyn->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average','internal_costs_forecast')
		));
		$project_tjm = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_average'];	
		$internal_costs_forecast = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_forecast'];		
		// Add value budget MD/Euro to data staffing 
		if( !empty( $valueInternals)){
			foreach($valueInternals as $interValue){
				$date = $interValue['ProjectBudgetInternalDetail']['validation_date'];
				if( empty( $date) || ($date == '0000-00-00')) continue;
				$date = strtotime( $date );
				$date = strtotime( date( 'Y-m-01', $date));
				if( empty( $dataset_internals[$date]['budget_md'])) $dataset_internals[$date]['budget_md'] = 0;
				$dataset_internals[$date]['budget_md'] += (float)$interValue['ProjectBudgetInternalDetail']['budget_md'];
				if( empty( $dataset_internals[$date]['budget_price'])) $dataset_internals[$date]['budget_price'] = 0;
				$dataset_internals[$date]['budget_price'] += (float)$interValue['ProjectBudgetInternalDetail']['budget_erro'];
			}
		}
		unset($valueInternals);
		$list_month = array_filter(array_keys($dataset_internals));
		$date = !empty($list_month) ? min($list_month) : strtotime(date('Y').'-02-01');
		$maxDate = !empty($list_month) ? max($list_month) : strtotime(date('Y').'-12-01');
		$first_month = strtotime('previous month', $date);
		$lastValue = array(
			'date' => $first_month,
			'date_format' => date( 'm/y' ,$first_month),
			'validated' => 0,
			'validated_price' => $internal_costs_forecast,
			'consumed' => 0,
			'consumed_price' => 0,
			'budget_md' => 0,
			'budget_price' => 0,
		);
		$dataset_internals[$first_month] = $lastValue;
		$current = time();
		while( $date <= $maxDate){
			$data = isset($dataset_internals[$date]) ? $dataset_internals[$date] : array();
			$lastValue['date'] = $date;
			$lastValue['date_format'] = date('m/y', $date);
			$lastValue['validated'] += !empty($data['validated']) ? $data['validated'] : 0;
			// $lastValue['validated_price'] = $data['validated_price'] ;
			$lastValue['consumed'] += !empty($data['consumed']) ? $data['consumed'] : 0;
			$lastValue['consumed_price'] += !empty($data['consumed_price']) ? $data['consumed_price'] : 0;
			$lastValue['budget_md'] += !empty($data['budget_md']) ? $data['budget_md'] : 0;
			$lastValue['budget_price'] += !empty($data['budget_price']) ? $data['budget_price'] : 0;
			$dataset_internals[$date] = $lastValue;
			$date = strtotime('next month', $date);
		}
		ksort($dataset_internals);
		//get max value for display ( + 10%)
		// if( empty($lastValue['validated_price'])) $lastValue['validated_price'] = 0;
		$max_internal_md = round( max( $lastValue['validated'], $lastValue['consumed'], $lastValue['budget_md'])*1.1, 2); 
		$max_internal_euro = round( max( $lastValue['validated_price'], $lastValue['consumed_price'], $lastValue['budget_price'])*1.1, 2);
        if(!empty($dataset_internals)) $countLineIn = count($dataset_internals);
        $this->set(compact('dataset_internals', 'max_internal_md', 'max_internal_euro', 'countLineIn'));
    }
	private function _staffingEmloyee($project_id = null){
        $this->loadModels('TmpStaffingSystem', 'Profile', 'ProfitCenter');
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay du lieu staffing cho employee
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'employee',
                'company_id' => $employeeName['company_id']
            )
        ));
		
        $staffing_data = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * Employee => array(
         *      'time' => array()
         * )
         */
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
                // date
                $staffing_data[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($staffing_data[$dx['model_id']][$dx['date']]['validated'])){
                    $staffing_data[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $staffing_data[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($staffing_data[$dx['model_id']][$dx['date']]['consumed'])){
                    $staffing_data[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $staffing_data[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                // $staffing_data[$dx['model_id']][$dx['date']]['remains'] = $staffing_data[$dx['model_id']][$dx['date']]['validated'] - $staffing_data[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        $ids = !empty($staffing_data) ? array_keys($staffing_data) : array();
        $this->loadModel('Employee');
		
        $employees = $this->Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $ids),
			'fields' => array('id', 'fullname', 'tjm', 'profile_id', 'profit_center_id'),
			'order' => array('Employee.id')
		));
		$employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
		
		/* Get Employee TJM */
		// TJM of profil		
		$profiles = !empty($employees) ? Set::classicExtract($employees, '{n}.profile_id') : array();
		$profiles_tjm = array();
		if(!empty($profiles)){
			$profiles_tjm =  $this->Profile->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => array_filter($profiles)),
					'fields' => array('id', 'tjm'), 
				)
			);
			$profiles_tjm = !empty($profiles_tjm) ? Set::combine($profiles_tjm, '{n}.Profile.id', '{n}.Profile.tjm') : array();
		}
		// TJM of TEAM
		$profit_centers = !empty($employees) ? Set::classicExtract($employees, '{n}.profit_center_id') : array();
		$team_tjm = array();
		if(!empty($profit_centers)){
			$team_tjm =  $this->ProfitCenter->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => array_filter($profit_centers)),
					'fields' => array('id', 'tjm'), 
				)
			);
		$team_tjm = !empty($team_tjm) ? Set::combine($team_tjm, '{n}.ProfitCenter.id', '{n}.ProfitCenter.tjm') : array();
		}
		// TJM of Project
		$this->loadModel('ProjectBudgetSyn');
		$project_tjm = $this->ProjectBudgetSyn->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average')
		));
		$project_tjm = (float)$project_tjm['ProjectBudgetSyn']['internal_costs_average'];	
		foreach ($employees as &$employee){
			if( empty( $employee['tjm'])){
				if( !empty( $profiles_tjm[$employee['profile_id']])) 
					$employee['tjm'] = $profiles_tjm[$employee['profile_id']];
				elseif( !empty( $team_tjm[$employee['profit_center_id']])) 
					$employee['tjm'] =  $team_tjm[$employee['profit_center_id']];
				else
					$employee['tjm'] = $project_tjm;
			}
		}
		$this->employee_tjms = $employees;
		// debug( $employees); exit;
		/* END Get Employee TJM */
		
		/* Caculate Consumed and workload by Price ( Default Euro)*/
        if(!empty($staffing_data['999999999'])){
            $notAffected = array(
				'id' => '999999999',
				'fullname' => 'Not Affected',
				'tjm' => $project_tjm
            );
            $employees['999999999'] = $notAffected;
        }
        $staffings = array();
		
		// Phan nay se kiem tra $companyConfigs['consumed_used_timesheet'] o function totalStaffing;		
		$consumed_used_timesheet = !empty($this->companyConfigs['consumed_used_timesheet']);
		if( !empty( $staffing_data)){
			foreach( $staffing_data as $e_id => $data ){
				foreach($data as $date => $date_data){
					if( !$consumed_used_timesheet){
						$staffing_data[$e_id][$date]['consumed_price'] = (float)$date_data['consumed'] * $employees[$e_id]['tjm'];
					}
				}
			}
		}
        return $staffing_data;
    }
	private function _totalStaffing($staffings, $project_id=null){
        $totalStaffing = array();
		$consumed_used_timesheet = !empty($this->companyConfigs['consumed_used_timesheet']);
        $projectBudgetSyn = $this->ProjectBudgetSyn->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average','internal_costs_forecast')
		));
		$project_tjm = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_average'];	
		$internal_costs_forecast = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_forecast'];		
		$consumeds = array();
		if( $consumed_used_timesheet) {
			$this->loadModels('ActivityRequest', 'Project', 'ActivityTask', 'ProjectBudgetSyn');
			
			$activity_id = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $project_id),
				'fields' => 'activity_id'
			));
			$activity_id = !empty( $activity_id ) ? $activity_id['Project']['activity_id'] : 0;
			$activityTasks = $this->ActivityTask->find('all', array(
				'recursive' => -1,
				'conditions' => array('activity_id' => $activity_id),
				'fields' => array('id', 'activity_id')
			));
			$groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
			$data_activity = $this->ActivityRequest->find('all', array(
				'recursive' => -1,
				'fields' => array(
					'employee_id', 
					'value',
					'date',
					'cost_price_resource',  
					'cost_price_team', 
					'cost_price_profil'
				),  
				'conditions' => array(
					'OR' => array(
						array(
							'activity_id' => $activity_id,
							'task_id' => 0
						),
						array(
							'activity_id' => 0,
							'task_id' => array_values($groupTaskId)
						),
					),
					'status' => 2,
				),
			));
			$employees = $this->employee_tjms;
			if(!empty($data_activity)){
				foreach($data_activity as $key => $activity){
					$activity = $activity['ActivityRequest'];
					$date = strtotime( date( 'Y-m', $activity['date']) . '-01');
					$e_id = $activity['employee_id'];
					$e_tjm = !empty( $employees[$e_id]['tjm'] ) ? $employees[$e_id]['tjm'] : $project_tjm;
					if((float)$activity['cost_price_resource'] > 0) {
						$consumed = $activity['cost_price_resource'];
					}elseif((float)$activity['cost_price_profil'] > 0){
						$consumed = $activity['cost_price_profil'];
					}elseif((float)$activity['cost_price_team'] > 0){
						$consumed = $activity['cost_price_team'];
					}else{
						$consumed = $activity['value']*$e_tjm;
					}
					if( empty( $consumeds[$date]['consumed'])) $consumeds[$date]['consumed'] = 0;
					if( !isset( $consumeds[$date]['consumed_price'])) $consumeds[$date]['consumed_price'] = 0;
					$consumeds[$date]['consumed'] += $activity['value'];
					$consumeds[$date]['consumed_price'] += $consumed;
				}
			}
		}
		if(!empty($staffings)){
            foreach($staffings as $employee_id => $staffing){
                foreach($staffing as $time => $values){
                    $_time = date('m/y', $values['date']);
                    $totalStaffing[$time]['date_format'] = $_time;
                    $totalStaffing[$time]['date'] = $time;
					if( $consumed_used_timesheet) {
						$totalStaffing[$time]['consumed'] = 0;
						$totalStaffing[$time]['consumed_price'] = 0;
					}else{
						if(!isset($totalStaffing[$time]['consumed'])){
							$totalStaffing[$time]['consumed'] = 0;
						}
						$totalStaffing[$time]['consumed'] += $values['consumed'];
						if(!isset($totalStaffing[$time]['consumed_price'])){
							$totalStaffing[$time]['consumed_price'] = 0;
						}
						$totalStaffing[$time]['consumed_price'] += $values['consumed_price'];
					}
                    if(!isset($totalStaffing[$time]['validated'])){
                        $totalStaffing[$time]['validated'] = 0;
                    }
                    $totalStaffing[$time]['validated'] += $values['validated'];
                }
            }
        }
		if( $consumed_used_timesheet) {
			foreach( $consumeds as $date => $consumed){
				if( !isset( $totalStaffing[$date])){
					$totalStaffing[$date] = array(
						'date_format' => date('m/y',$date),
						'date' => $date,
						'validated' => 0,
						// 'validated_price' => 0,
						// 'remains' => 0,
						'consumed' => $consumed['consumed'],
						'consumed_price' => $consumed['consumed_price'],
					);
				}else{
					$totalStaffing[$date]['consumed'] = $consumed['consumed'];
					$totalStaffing[$date]['consumed_price'] = $consumed['consumed_price'];
				}
			}
		}
        ksort($totalStaffing);
		// debug( $totalStaffing); exit;
		return $totalStaffing;
    }
	public function order($project_id = null) {
        $this->layout = false;
        if ($this->_checkRole(true, $project_id)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight!=0) {
                    $this->ProjectPhasePlan->id = $id;
                    $this->ProjectPhasePlan->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }
}
?>