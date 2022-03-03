<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetSyn extends AppModel {

    var $name = 'ProjectBudgetSyn';

    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Activity' => array(
            'className' => 'Activity',
            'foreignKey' => 'activity_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $validate = array(
        //'title' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'The Part is not blank!',
//            )
//            //'isUnique' => array(
////                'rule' => 'isUnique',
////                'message' => 'The Part has already been exist.',
////            )
//        ),
//        'project_id' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'The Project is not blank!',
//            ),
//        )
    );

    /**
     * I18n validate
     *
     * @param string $field The name of the field to invalidate
     * @param mixed $value Name of validation rule that was not failed, or validation message to
     *    be returned. If no validation key is provided, defaults to true.
     *
     * @return void
     */
    public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }
    public $formatFields = array(
        'sales_sold' => 'Sold €',
        'sales_to_bill' => 'To Bill €',
        'sales_billed' => 'Billed €',
        'sales_paid' => 'Paid €',
        'sales_man_day' => 'M.D',

        'purchases_sold' => 'Sold €',
        'purchases_to_bill' => 'To Bill €',
        'purchases_billed' => 'Billed €',
        'purchases_paid' => 'Paid €',

        'internal_costs_budget' => 'Budget €',
        'internal_costs_budget_man_day' => 'Budget M.D',
        'internal_costs_forecast' => 'Forecast €',
        'internal_costs_var' => 'Var %',
        'internal_costs_engaged' => 'Engaged €',
        'internal_costs_remain' => 'Remain €',
        'internal_costs_engaged_md' => 'Engaged M.D',
        'internal_costs_forecasted_man_day' => 'Forecasted M.D',
        'internal_costs_average' => 'Average €',

        'external_costs_budget' => 'Budget €',
        'external_costs_forecast' => 'Forecast €',
        'external_costs_var' => 'Var %',
        'external_costs_ordered' => 'Ordered €',
        'external_costs_remain' => 'Remain €',
        'external_costs_man_day' => 'M.D',
        'external_costs_progress' => 'Progress %',
        'external_costs_progress_euro' => 'Progress €',

        'provisional_budget_md' => 'Budget Provisional M.D',
        'provisional_y' => 'Budget Provisional Y',
        'provisional_last_one_y' => 'Budget Provisional Y-1',
        'provisional_last_two_y' => 'Budget Provisional Y-2',
        'provisional_last_thr_y' => 'Budget Provisional Y-3',
        'provisional_next_one_y' => 'Budget Provisional Y+1',
        'provisional_next_two_y' => 'Budget Provisional Y+2',
        'provisional_next_thr_y' => 'Budget Provisional Y+3',

        'total_costs_budget' => 'Total Costs Budget €',
        'total_costs_forecast' => 'Total Costs Forecast €',
        'total_costs_var' => 'Total Costs Var %',
        'total_costs_engaged' => 'Total Costs Engaged €',
        'total_costs_remain' => 'Total Costs Remain €',
        'total_costs_man_day' => 'Total Costs M.D',
        'assign_to_profit_center' => '% Assigned to profit center',
        'assign_to_employee' => '% Assigned to employee',
        'roi' => 'ROI',
        'workload' => 'Workload',
        'overload' => 'Overload',
        'workload_y' => 'Workload Y',
        'workload_last_one_y' => 'Workload Y-1',
        'workload_last_two_y' => 'Workload Y-2',
        'workload_last_thr_y' => 'Workload Y-3',
        'workload_next_one_y' => 'Workload Y+1',
        'workload_next_two_y' => 'Workload Y+2',
        'workload_next_thr_y' => 'Workload Y+3',
        'consumed_y' => 'Consumed Y',
        'consumed_last_one_y' => 'Consumed Y-1',
        'consumed_last_two_y' => 'Consumed Y-2',
        'consumed_last_thr_y' => 'Consumed Y-3',
        'consumed_next_one_y' => 'Consumed Y+1',
        'consumed_next_two_y' => 'Consumed Y+2',
        'consumed_next_thr_y' => 'Consumed Y+3',
    );
    public $projectTaskFieldName = array(
        'Initialworkload' => 'Initial workload',
        'Workload' => 'Workload',
        'Overload' => 'Overload',
        'Consumed' => 'Consumed',
        'InUsed' => 'In Used',
        'ManualConsumed' => 'Manual Consumed',
        'Completed'  => 'Completed',
        'Remain' => 'Remain',
        'Amount€' => 'Amount €',
        '%progressorder' => '% progress order',
        '%progressorder€' => '% progress order €',
        'UnitPrice' => 'Unit Price',
        'Consumed€' => 'Consumed €',
        'Remain€' => 'Remain €',
        'Workload€' => 'Workload €',
        'Estimated€' => 'Estimated €',
    );
    public function getViewFieldNames() {
        $fieldset = array();
        foreach($this->formatFields as $key => $name){
            $fieldset['ProjectBudgetSyn.' . $key] = $name;
        }
        $data = $this->getPTaskFields();
        foreach ($data as $value) {
            if($value != 'Milestone')
            $fieldset['Task']['ProjectBudgetSyn.' . $value] = $this->projectTaskFieldName[$value];
        }
        return $fieldset;
    }
    public function get(){
        $fieldset = array();
        foreach($this->formatFields as $key => $name){
            //sales
            if( substr($key, 0, 5) == 'sales' ){
                $fieldset['sales']['ProjectBudgetSyn.' . $key] = $name;
            } else if( substr($key, 0, 9) == 'purchases' ){
                $fieldset['purchases']['ProjectBudgetSyn.' . $key] = $name;
            } else if( substr($key, 0, 8) == 'internal' ) {
                $fieldset['internal']['ProjectBudgetSyn.' . $key] = $name;
            } else if( substr($key, 0, 8) == 'external' ) {
                $fieldset['external']['ProjectBudgetSyn.' . $key] = $name;
            } else if( substr($key, 0, 11) == 'provisional' ){
                $fieldset['provisional']['ProjectBudgetSyn.' . $key] = $name;
            } else {
                $fieldset['others']['ProjectBudgetSyn.' . $key] = $name;
            }
        }
        $data = $this->getPTaskFields();
        foreach ($data as $value) {
            if($value != 'Milestone')
            $fieldset['Task']['ProjectBudgetSyn.' . $value] = !empty($this->projectTaskFieldName[$value]) ? $this->projectTaskFieldName[$value] : '' ;
        }
        return $fieldset;
    }
    public function getPTaskFields(){
        $company_id = CakeSession::read('Auth.employee_info.Company.id');
        $Translation = ClassRegistry::init('Translation');
        $defaultTaskFields = array(
            'ID|0',
            'Task|1',
            'Order|1',
            'Priority|1',
            'Status|1',
            'Profile|1',
            'AssignedTo|1',
            '+/-|0',
            'Startdate|1',
            'Enddate|1',
            'Duration|1',
            'Predecessor|1',
            'Workload|1',
            'Overload|1',
            'Consumed|1',
            'ManualConsumed|1',
            'InUsed|1',
            'Completed|1',
            'Remain|1',
            'Amount€|0',
            '%progressorder|0',
            '%progressorder€|0',
            'Text|0',
            'Attachment|0',
            'Initialworkload|1',
            'Initialstartdate|1',
            'Initialenddate|1',
            'UnitPrice|1',
            'Consumed€|1',
            'Remain€|1',
            'Workload€|1',
            'Estimated€|1'
        );
        // nhung columns khong cho hien thi
        $defaultNotUseFields = array(
            'ID',
            'Task',
            'Order',
            'Priority',
            'Status',
            'Profile',
            'AssignedTo',
            '+/-',
            'Startdate',
            'Enddate',
            'Duration',
            'Predecessor',
            'Text',
            'Attachment',
            'Initialstartdate',
            'Initialenddate',
        );
        $conds = array(
            'page' => 'Project_Task'
        );
        $raw = $Translation->find('all', array(
            'conditions' => $conds,
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id',
                        'TranslationSetting.company_id' => $company_id
                    ),
                    'type' => 'left'
                )
            ),
            'fields' => array('TranslationSetting.setting_order', 'CONCAT(REPLACE(original_text, " ", ""), "|", TranslationSetting.show) all_settings'),
            'order' => array('TranslationSetting.setting_order' => 'ASC')
        ));
        $raw = !empty($raw) ? Set::combine($raw, '{n}.TranslationSetting.setting_order', '{n}.0.all_settings') : array();
        $raw = array_filter($raw);
        $datas = array_unique($raw + $defaultTaskFields);
        $_data = array();
        foreach ($datas as $data) {
            $v = explode('|', $data);
            if($v[1] == 1 && !in_array($v[0], $defaultNotUseFields)) $_data[] = $v[0];
        }
        return $_data;
    }
	public function updateBudgetSynbyActivityTask($listActivityTasks){
		$ActivityTask = ClassRegistry::init('ActivityTask');
		$listActivities = $ActivityTask->find('list', array(
			'recursive' => -1,
			'fields' => array('activity_id', 'activity_id'), 
			'conditions' => array(
				'id' => $listActivityTasks,
			)
		));
		if( !empty( $listActivities)) $this->updateBudgetSynbyActivity(array_values($listActivities));
	}
	public function updateBudgetSynbyActivity($listActivities){
		$Activity = ClassRegistry::init('Activity');
		$projects = $Activity->find('list', array(
			'recursive' => -1,
			'fields' => array('project', 'project'), 
			'conditions' => array(
				'id' => $listActivities,
			)
		));
		if( !empty( $projects))
			foreach( $projects as $project_id){
				$this->updateBudgetSyn($project_id);
			}
	}
	/*
		Ticket #526 Apply new design for Synthesis screen	
		Create by Huynh 2020-04-10
		Update for the fields
			internal_costs_forecast
			internal_costs_var
			internal_costs_engaged
			internal_costs_remain
			internal_costs_forecasted_man_day		
	*/
	public function updateBudgetSyn($project_id){
		$ActivityRequest = ClassRegistry::init('ActivityRequest');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $Profiles = ClassRegistry::init('Profile');
        $ProjectTask = ClassRegistry::init('ProjectTask');
        $ProjectTaskEmployeeRefer = ClassRegistry::init('ProjectTaskEmployeeRefer');
        $Project= ClassRegistry::init('Project');
        $CompanyConfig = ClassRegistry::init('CompanyConfig');
        $ProjectBudgetSyn = ClassRegistry::init('ProjectBudgetSyn');
        $ProjectBudgetInternalDetail = ClassRegistry::init('ProjectBudgetInternalDetail');
		
		$activity_id = $Project->find('first', array(
			'recursive' => -1,
			'conditions' => array( 'id' => $project_id),
			'fields' => array('activity_id', 'company_id')
		));
		if( empty( $activity_id['Project']['company_id'] )) return;
		$company_id = $activity_id['Project']['company_id'];
		$companyConfigs = $CompanyConfig->find('list', array(
			'recursive' => -1,
			'fields' => array('cf_name', 'cf_value'), 
			'conditions' => array(
				'company' => $company_id,
				'cf_name' => array(
					'consumed_used_timesheet',
					'budget_euro_fill_manual',
					'average_euro_fill_manual',
					'average_cost_default'
				)
			)
		));
		$consumed_used_timesheet = !empty($companyConfigs['consumed_used_timesheet']);
		$budget_euro_fill_manual = !empty($companyConfigs['budget_euro_fill_manual']);
		$average_euro_fill_manual = !empty($companyConfigs['average_euro_fill_manual']);
		$average_cost_default = empty($budget_euro_fill_manual) && empty($average_euro_fill_manual)  ;
		$activity_id = !empty( $activity_id['Project']['activity_id'] ) ? $activity_id['Project']['activity_id'] : 0;
		$projectBudgetSyn = $this->find('first', array(
			'recursive' => -1,			
			'fields' => array('*'),
			'conditions' => array(
				'project_id' => $project_id
			)
		));
		$projectBudgetSyn = !empty($projectBudgetSyn['ProjectBudgetSyn']) ? $projectBudgetSyn['ProjectBudgetSyn'] : array();	
		
		/* Sumary from budget details * /
		internal_costs_budget
		internal_costs_budget_man_day
		internal_costs_average
		*/
		// debug(  $project_id);
		$budgets = $ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
		/* Update after change method : project_budget_internals_preview/setting/*/
		foreach( $budgets as $i => $budget){
			$budget = $budget['ProjectBudgetInternalDetail'];
			if( empty( $budget['budget_erro']) && !empty( $budget['budget_md']) && !empty( $budget['average'])){
				$budget['budget_erro'] = (float) $budget['budget_md'] * (float)$budget['average'];
				$ProjectBudgetInternalDetail->id = $budget['id'];
				$budgets[$i]['ProjectBudgetInternalDetail'] = $budget;
				// debug(  $budget);
				$ProjectBudgetInternalDetail->saveField('budget_erro', $budget['budget_erro']);
			}
			if( empty( $budget['budget_erro']) && empty( $budget['budget_md']) && !empty( $budget['average'])){
				$budget['average'] = 0;
				$ProjectBudgetInternalDetail->id = $budget['id'];
				$budgets[$i]['ProjectBudgetInternalDetail'] = $budget;
				$ProjectBudgetInternalDetail->saveField('average', $budget['average']);
			}
			
		}
		/* END Update after change method*/
		if( !empty( $budgets)){
			$totalBudgetMD = 0;
			$totalBudgetEu = 0;
			$sumTJM = 0;
			$i = 0;
			$tjm = 0;
			// debug(  $budgets);
			
			foreach( $budgets as $budget ){
				$budget = $budget['ProjectBudgetInternalDetail'];
				$i++;
				$totalBudgetMD += (float) $budget['budget_md'];
				$totalBudgetEu += (float) $budget['budget_erro'];
				$sumTJM += (float) $budget['average'];
			}
			if( $average_cost_default){
				$tjm = round($sumTJM/$i, 2);
			}else{
				$tjm = !empty($totalBudgetMD) ? round($totalBudgetEu/ $totalBudgetMD, 2) : 0;
			}
			$projectBudgetSyn['internal_costs_budget'] = $totalBudgetEu;
			$projectBudgetSyn['internal_costs_budget_man_day'] = $totalBudgetMD;
			$projectBudgetSyn['internal_costs_average'] = $tjm;
		}

		/* END Sumary from budget details */
		
		$tjm_project = !empty($projectBudgetSyn['internal_costs_average']) ? $projectBudgetSyn['internal_costs_average'] : 0; 
		
		/* Data Consumed */	
		if( !empty( $activity_id)){
			$activity_tasks = $ActivityTask->find('list', array(
				'recursive' => -1,
				'fields' => array('id'), 
				'conditions' => array(
					'activity_id' => $activity_id
				)
			));
			$requestValidated = $ActivityRequest->find('all', array(
				'recursive' => -1,
				'fields' => array(
					'employee_id', 
					'value',
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
							'task_id' => array_values($activity_tasks)
						),
					),
					'status' => 2,
				),
			));
			$totalConsumed = 0;
			$totalCostConsumed = 0;
			if( !empty( $requestValidated )){
				$listEmployeeID = Set::classicExtract($requestValidated, '{n}.ActivityRequest.employee_id');
				$listEmployeeID = array_values(array_unique($listEmployeeID));
				$tjm_employees = $this->getEmployeeTJM($listEmployeeID, $tjm_project);
				foreach ( $requestValidated as $request ){
					$request = $request['ActivityRequest'];
					$totalConsumed += $request['value']; // MD
					if( $consumed_used_timesheet ){
						if( (float) $request['cost_price_resource'] > 0){
							$totalCostConsumed += $request['cost_price_resource'];
						}elseif( (float) $request['cost_price_profil'] > 0){
							$totalCostConsumed += $request['cost_price_profil'];
						}elseif( (float) $request['cost_price_team'] > 0){
							$totalCostConsumed += $request['cost_price_team'];
						}else{
							$totalCostConsumed += $request['value']*$tjm_employees[$request['employee_id']]['tjm'];
						}
					}else{
						$totalCostConsumed += $request['value']*$tjm_employees[$request['employee_id']]['tjm'];
						
					}
				}
			}
			$projectBudgetSyn['internal_costs_engaged'] = $totalCostConsumed;
		
		}else{
			$projectBudgetSyn['internal_costs_engaged'] = 0;
		}
		/* END Data Consumed */
		
		/* Data Remain */
		$getDataProjectTasks = $Project->dataFromProjectTask($project_id, $company_id);
		// debug( $getDataProjectTasks );
		$remain = (float) $getDataProjectTasks['remain'];
		$remainEuro = $remain * $tjm_project;
		$projectBudgetSyn['internal_costs_remain'] = $remainEuro;
		/* END Data Remain */
		
		/* Data Forecast */
		//Fix loi tinh ca workload cua task parent trong internal. Ticket #1057
		$parentTasks = $ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $project_id,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$totalWorkload = $ProjectTask->find('all', array(
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
			),
		));
		$totalWorkload = !empty( $totalWorkload[0][0]['Total'] ) ? $totalWorkload[0][0]['Total'] : 0;
		// debug( $totalWorkload);
		$projectBudgetSyn['internal_costs_forecasted_man_day'] =$totalWorkload;
		$projectBudgetSyn['internal_costs_forecast'] = $projectBudgetSyn['internal_costs_engaged'] + $projectBudgetSyn['internal_costs_remain'];
		/* END Data Forecast */
		
		/* Cost Var */
		$inCostsBudget = !empty($projectBudgetSyn['internal_costs_budget']) ? $projectBudgetSyn['internal_costs_budget'] : 0;
		$inCostsVar = ($inCostsBudget != 0) ? ($totalWorkload / $inCostsBudget - 1) * 100 : 0;
		$projectBudgetSyn['internal_costs_var'] = $inCostsVar;
		/* END Cost Var */
		
		/* Save Data */ 
		if( empty($projectBudgetSyn['id'])){
			$this->create();
			$projectBudgetSyn['project_id'] = $project_id;
			$projectBudgetSyn['activity_id'] = $activity_id;
		}else{
			$this->id = $projectBudgetSyn['id'];
		}
		$projectBudgetSyn['updated'] = time();
		$this->save( $projectBudgetSyn);
		/* END Save Data */ 
		return $projectBudgetSyn;
	}
	/* Huynh 22/05/2020 
	* De tranh viec su dung gia tri TJM cu trong DB ( gia tri moi chua duoc updated), o day truyen vao tjm moi 
	**/
	public function getEmployeeTJM($employeeIDs, $tjm_project){
		$Employee = ClassRegistry::init('Employee');
		$ProfitCenter = ClassRegistry::init('ProfitCenter');
		$Profile = ClassRegistry::init('Profile');
		$employees = $Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $employeeIDs),
			'fields' => array('id', 'fullname', 'tjm', 'profile_id', 'profit_center_id'),
			'order' => array('Employee.id')
		));
		$employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
		$profiles = !empty($employees) ? Set::classicExtract($employees, '{n}.profile_id') : array();
		$profiles_tjm = array();
		if(!empty($profiles)){
			$profiles_tjm = $Profile->find(
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
			$team_tjm =  $ProfitCenter->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => array_filter($profit_centers)),
					'fields' => array('id', 'tjm'), 
				)
			);
		$team_tjm = !empty($team_tjm) ? Set::combine($team_tjm, '{n}.ProfitCenter.id', '{n}.ProfitCenter.tjm') : array();
		}
		// TJM of Project
		/* 
		$project_tjm = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average')
		));
		$project_tjm = (float)$project_tjm['ProjectBudgetSyn']['internal_costs_average'];	
		*/
		foreach ($employees as &$employee){
			if( empty( $employee['tjm'])){
				if( !empty( $profiles_tjm[$employee['profile_id']])) 
					$employee['tjm'] = $profiles_tjm[$employee['profile_id']];
				elseif( !empty( $team_tjm[$employee['profit_center_id']])) 
					$employee['tjm'] =  $team_tjm[$employee['profit_center_id']];
				else
					$employee['tjm'] = $tjm_project;
			}
		}
		return $employees;
	}
}
?>
