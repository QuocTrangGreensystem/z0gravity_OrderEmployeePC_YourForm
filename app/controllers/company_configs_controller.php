<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class CompanyConfigsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $uses = array('CompanyConfig');
    var $name = 'CompanyConfigs';

    /**
     * Components
     *
     * @var array
     * @access public
     */
    function beforeFilter()
    {
        parent::beforeFilter();
        // if($this->employee_info['Employee']['is_sas'])
        // {
            // $this->redirect(array('controller' => 'administrators', 'action' => 'index'));
        // }
    }
    function index($ajax = '')
    {
        //do nothing
    }
    public function editMe($field = null, $value = null, $private = false){
        $value = $this->data['value'];
        $field = $this->data['field'];
		$success = 1;
        $company = $this->employee_info['Company']['id'];
        $check = $this->CompanyConfig->find('first',array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company,
                'cf_name' => $field
            )
        ));
        if($check)
        {
            $data = array(
                'id'=> $check['CompanyConfig']['id'],
                'cf_value'=> $value
            );
            
            $success = $this->CompanyConfig->save($data);
        }
        else
        {
            $this->CompanyConfig->create();
            $data = array(
                'company'=>$company,
                'cf_name'=> $field,
                'cf_value'=> $value
            );
            $success = $this->CompanyConfig->save($data);
            /**
             * Rebuild all staffing
             */
            if($data['cf_name'] === 'staffing_by_month_multiple'){
                $this->loadModels('Project', 'Activity');
                $this->Project->updateAll(array('Project.rebuild_staffing' => 1), array('OR' => array('Project.rebuild_staffing' => 0, 'Project.rebuild_staffing IS NULL')));
                $this->Activity->updateAll(array('Activity.rebuild_staffing' => 1), array('OR' => array('Activity.rebuild_staffing' => 0, 'Activity.rebuild_staffing IS NULL')));
            }
            // $success = 1;
        }
		if( !empty($this->params['isAjax'])){
			die(json_encode( $success));
		}
        echo $success;
        exit;
    }
    public function saveAll($array){
        foreach($array as $key => $value){
            $this->save($key, $value);
        }
        return;
    }
    public function save($field, $value){
        //$value = $this->data['value'];
        //$field = $this->data['field'];
        $company = $this->employee_info['Company']['id'];
        $check = $this->CompanyConfig->find('first',array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company,
                'cf_name' => $field
            )
        ));
        
        if($check)
        {
            $data = array(
                'id'=> $check['CompanyConfig']['id'],
                'cf_value'=> $value
            );
            $this->CompanyConfig->save($data);
            $success = 1;
        }
        else
        {
            $this->CompanyConfig->create();
            $data = array(
                'company'=>$company,
                'cf_name'=> $field,
                'cf_value'=> $value
            );
            $this->CompanyConfig->save($data);
            $success = 1;
        }
        return;
    }
	public function settingBudget(){
        $value = $this->data['value'];
        $field = $this->data['field'];
		$success = 1;
        $company = $this->employee_info['Company']['id'];
        $check = $this->CompanyConfig->find('first',array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company,
                'cf_name' => $field
            )
        ));
        if($check){
            $data = array(
                'id'=> $check['CompanyConfig']['id'],
                'cf_value'=> $value
            );
            $success = $this->CompanyConfig->save($data);
        }else{
            $this->CompanyConfig->create();
            $data = array(
                'company'=>$company,
                'cf_name'=> $field,
                'cf_value'=> $value
            );
            $success = $this->CompanyConfig->save($data);
        }
		
		$this->loadModels('ProjectBudgetSyns', 'Project', 'ProjectBudgetInternalDetail', 'CompanyConfigs', 'Project', 'ProjectBudgetExternal', 'ProjectTask', 'Profile', 'ProfitCenter');
		//Get setting budget.
		$listProjectId = $this->ProjectBudgetSyns->find('list', array(
			'recursive' => -1,
			'fields' => array('project_id')
		));
		$listProjectIdCompany = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.id' => $listProjectId,
				// 'Project.id' => 3241,
				'Project.company_id' => $company,
			),
			'fields' => array('Project.id')
		));
		$cf_name = array('budget_euro_fill_manual', 'average_euro_fill_manual', 'consumed_used_timesheet', 'average_cost_default');
		$companyConfigs = $this->CompanyConfigs->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company,
				'cf_name' => $cf_name,
            ),
            'fields' => array('cf_name', 'cf_value')
        ));
		$consumed_used_timesheet = isset($companyConfigs['consumed_used_timesheet']) ? $companyConfigs['consumed_used_timesheet'] : 0;
		$budget_euro_fill_manual = isset($companyConfigs['budget_euro_fill_manual']) ? $companyConfigs['budget_euro_fill_manual'] : 0;
		$average_euro_fill_manual = isset($companyConfigs['average_euro_fill_manual']) ? $companyConfigs['average_euro_fill_manual'] : 0;
		$average_cost_default = isset($companyConfigs['average_cost_default']) ? $companyConfigs['average_cost_default'] : 0;
		
		//Tính Value Average and Save.
		if($field != 'consumed_used_timesheet'){
			if($value != 0){
				foreach($listProjectIdCompany as $key => $project_id){
					//Get value budget euro, budget m.d
					$valBudgetInternal = $this->ProjectBudgetSyns->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'project_id' => $project_id
						),
						'fields' => array('project_id', 'internal_costs_budget', 'internal_costs_budget_man_day', 'internal_costs_average')
					));
					$valBudgetInternal = $valBudgetInternal['0']['ProjectBudgetSyns'];
					//Tinh Average
					$totalAverages = 0;
					if($field == 'average_cost_default'){
						$budgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
							'recursive' => -1,
							'conditions' => array('project_id' => $project_id),
							'fields' => array('id', 'budget_md', 'budget_erro', 'project_id', 'average')
						));
						$count = 0;
						if(!empty($budgetInternals)){
							foreach($budgetInternals as $budgetInternal ){
								$averages = isset($budgetInternal['ProjectBudgetInternalDetail']['average']) ? (float)$budgetInternal['ProjectBudgetInternalDetail']['average'] : 0;
								$totalAverages += $averages;
								$count++;
							}
							$totalAverages = round($totalAverages/$count, 2);
						}
					}elseif($field == 'budget_euro_fill_manual'){
						if( isset($valBudgetInternal) && ((float)$valBudgetInternal['internal_costs_budget_man_day'] != 0)){
							$totalAverages = (float)$valBudgetInternal['internal_costs_budget']/(float)$valBudgetInternal['internal_costs_budget_man_day'];
						}
					}else{
						$totalAverages = (float)$valBudgetInternal['internal_costs_average'];
					}
					$this->ProjectBudgetSyns->updateAll(
						array(
							'ProjectBudgetSyns.internal_costs_average' => (string)$totalAverages
						),
						array(
							'ProjectBudgetSyns.project_id' => $project_id
						)
					);
				}
			}
		}
		
		//Tinh Engaged Euro and Save
		if($field == 'consumed_used_timesheet' || $value != 0){
			$engagedErro = 0;
			if($consumed_used_timesheet != 0){
				foreach ($listProjectIdCompany as $key => $project_id) {
					$projectName = $this->Project->find('first', array(
						'recursive' => -1,
						'conditions' => array('Project.id' => $project_id)
					));
					$valueBudgetInternalAve = $this->ProjectBudgetSyns->find('first', array(
						'recursive' => -1,
						'conditions' => array('project_id' => $project_id),
						'fields' => array('internal_costs_average')
					));
					$valueBudgetInternalAve = (float)$valueBudgetInternalAve['ProjectBudgetSyns']['internal_costs_average'];
					if(!empty($projectName['Project']['activity_id'])){
						$activityId = $projectName['Project']['activity_id'];
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
							// TJM of Project.$engagedErro = $consumed_md * Average. Average se them vao phia duoi
							$engagedErro = $consumed_md * $valueBudgetInternalAve;
						}
						$this->ProjectBudgetSyns->updateAll(
							array(
								'ProjectBudgetSyns.internal_costs_engaged' => (string)$engagedErro
							),
							array(
								'ProjectBudgetSyns.project_id' => $project_id
							)
						);
					}
				}
			}else{
				foreach ($listProjectIdCompany as $key => $project_id) {
					$projectName = $this->Project->find('first', array(
						'recursive' => -1,
						'conditions' => array('Project.id' => $project_id)
					));
					if(!empty($projectName['Project']['activity_id'])){
						$valueBudgetInternalAve = $this->ProjectBudgetSyns->find('first', array(
							'recursive' => -1,
							'conditions' => array('project_id' => $project_id),
							'fields' => array('internal_costs_average')
						));
						$valueBudgetInternalAve = (float)$valueBudgetInternalAve['ProjectBudgetSyns']['internal_costs_average'];
						$activityId = $projectName['Project']['activity_id'];
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
						if (isset($sumEmployees[$activityId])) {
							$engagedErro = 0;
							foreach ($sumEmployees[$activityId] as $id => $val) {
								$reals = 0;
								if(!empty($employees[$id]['tjm'])){
									$reals = $employees[$id]['tjm'];
									$engagedErro += $val * $reals;
								}else if(!empty($profiles_tjm[$employees[$id]['profile_id']])){
									$reals = $profiles_tjm[$employees[$id]['profile_id']];
									$engagedErro += $val * $reals;
								}else if(!empty($team_tjm[$employees[$id]['profit_center_id']])){
									$reals = $team_tjm[$employees[$id]['profit_center_id']];
									$engagedErro += $val * $reals;
								}else{
									$engagedErro += $val * $valueBudgetInternalAve;
								}
							}
							$this->ProjectBudgetSyns->updateAll(
								array(
									'ProjectBudgetSyns.internal_costs_engaged' => $engagedErro
								),
								array(
									'ProjectBudgetSyns.project_id' => $project_id
								)
							);
						}
					}
				}
			}
		}
		//Change and save other value.
		if(($value != 0) || ($value == 0 && $field == 'consumed_used_timesheet')){
			foreach ($listProjectIdCompany as $key => $project_id) {
				//Get remain m.d
				$getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $company);
				$projectTasks = $this->ProjectTask->find('all', array(
					'fields' => array(
						'SUM(ProjectTask.estimated) AS Total',
						'SUM(ProjectTask.special_consumed) AS exConsumed'
					),
					'recursive' => -1,
					'conditions' => array('project_id' => $project_id, 'special'=>1)
				));
				$externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
				$externalBudgets = $this->ProjectBudgetExternal->find('all', array(
					'recursive' => -1,
					'conditions' => array('project_id' => $project_id),
					'fields' => array('SUM(man_day) AS exBudget')
				));
				$externalBudgets = !empty($externalBudgets) && !empty($externalBudgets[0][0]['exBudget']) ? $externalBudgets[0][0]['exBudget'] : 0;
				$remain_md = !empty($getDataProjectTasks['remain']) ? ((float)$getDataProjectTasks['remain'] - (float)$externalBudgets - (float)$externalConsumeds) : 0;
				
				//Get value budget
				$valBudgetInternal = $this->ProjectBudgetSyns->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $project_id
					),
					'fields' => array('project_id', 'internal_costs_budget', 'internal_costs_budget_man_day', 'internal_costs_average', 'internal_costs_forecast', 'internal_costs_var', 'internal_costs_engaged', 'internal_costs_remain', 'internal_costs_forecasted_man_day')
				));
				$valBudgetInternal = $valBudgetInternal['0']['ProjectBudgetSyns'];
				
				//Tinh value
				$totalAverages = (float)$valBudgetInternal['internal_costs_average'];
				$engagedErro = (float)$valBudgetInternal['internal_costs_engaged'];
				
				$totalRemainEuro = round($totalAverages * $remain_md, 2);
				$totalForecastEuro = round($engagedErro + $totalRemainEuro, 2);
				if(((float)$valBudgetInternal['internal_costs_budget']) != 0){
					$totalVarEuros = round(($totalForecastEuro / (float)$valBudgetInternal['internal_costs_budget'] - 1) * 100, 2);
				}else{
					$totalVarEuros = round(0, 2);
				}
				//Save data
				$totalRemainEuro = (string)$totalRemainEuro;
				$totalForecastEuro = (string)$totalForecastEuro;
				$totalVarEuros = (string)$totalVarEuros;
				
				$this->ProjectBudgetSyns->updateAll(
					array(
						'ProjectBudgetSyns.internal_costs_forecast' => $totalForecastEuro,
						'ProjectBudgetSyns.internal_costs_var' => $totalVarEuros,
						'ProjectBudgetSyns.internal_costs_remain' => $totalRemainEuro
					),
					array(
						'ProjectBudgetSyns.project_id' => $project_id
					)
				);
			}
		}
		// if( !empty($this->params['isAjax'])){
			// die(json_encode( $success));
		// }
        echo $success;
        exit;
    }
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
	function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }
    public function resource(){

    }
    public function icon(){
        
    }
}
?>