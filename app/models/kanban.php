<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class Kanban extends AppModel {
	var $name = 'ProjectTask';
	var $actsAs = array('Lib');
	var $displayField = 'task_title';
	function __construct(){
		parent::__construct();
		$this->validate = array(
			'task_title' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('The task title is not blank',true),
				),
			),
            'project_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('The phase is not blank',true),
				),
			),
	        'project_planed_phase_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('The phase is not blank',true),
				),
			)
		);
    }

	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectPhasePlan' => array(
			'className' => 'ProjectPhasePlan',
			'foreignKey' => 'project_planed_phase_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectPriority' => array(
			'className' => 'ProjectPriority',
			'foreignKey' => 'task_priority_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectStatus' => array(
			'className' => 'ProjectStatus',
			'foreignKey' => 'task_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Employee' => array(
			'className' => 'Employee',
			'foreignKey' => 'task_assign_to',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    var $hasMany = array(
		'ProjectTaskEmployeeRefer' => array(
			'className' => 'ProjectTaskEmployeeRefer',
			'foreignKey' => 'project_task_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
    );

    /**
     * Tong hop Workload tu Project Task, Workload* = Worload + Overload.
     * Tong hop Consumed tu Activity Request. Consumed cua cac Task cua Project co linked voi Activity
     *
     * @access public
     * @return void
     * @author HuuPC
     *
     */
	 public function beforeFind($queryData) {
        $defaultConditions = array('ProjectTask.special' => array(0,1));
		if(empty($queryData['conditions']))
		{
			$queryData['conditions']=$defaultConditions;
		}
		else
		{
			if(!isset($queryData['conditions']['ProjectTask.special'])&&!isset($queryData['conditions']['special']))
			{
				$queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
			}
		}
        return $queryData;
    }

    public function staffing4ncTasks($project_id = null, $checkData = false){

    }
     public function staffingSystem($project_id = null, $checkData = false){
        ini_set('memory_limit', '1024M');
		set_time_limit(0);
        ignore_user_abort(true);
		$HTML = '';
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $ProjectTask = ClassRegistry::init('ProjectTask');
        $Project = ClassRegistry::init('Project');
        $ProjectTeam = ClassRegistry::init('ProjectTeam');
        $ProjectFunctionEmployeeRefer = ClassRegistry::init('ProjectFunctionEmployeeRefer');
        $ProjectEmployeeProfitFunctionRefer = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
        $ProjectEmployeeManager = ClassRegistry::init('ProjectEmployeeManager');
        $ProjectEmployeeManager = ClassRegistry::init('ProjectEmployeeManager');
        $TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
		$ProfitCenter = ClassRegistry::init('ProfitCenter');
        $Period = ClassRegistry::init('Period');
        $CompanyConfig = ClassRegistry::init('CompanyConfig');
		//GET company ID
        $companyId = ClassRegistry::init('Project')->find('first', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $project_id),
			'fields' => array('company_id')
		));
		$companyId = !empty($companyId) ? $companyId['Project']['company_id'] : array();
        $workloadOfEmployees = $workloadOfProfitCenters = $consumedTasks = $consumedPreviousTasks = $consumedPreviousTaskEmployees = array();
        $idActivityLinked = 0;
        if(!empty($project_id)){
            /**
             * Lay danh sach $Period
             */
            $periods = $Period->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $companyId),
                'fields' => array('time', 'end'),
                //'group' => array('time', 'start')
            ));
            /**
             * Get Multiple confirm month
             */
            $mutilMonth = $CompanyConfig->find('first', array(
                'recursive' => -1,
                'conditions' => array('cf_name' => 'staffing_by_month_multiple', 'company' => $companyId),
                'fields' => array('cf_value')
            ));
            $mutilMonth = !empty($mutilMonth) && !empty($mutilMonth['CompanyConfig']['cf_value']) ? $mutilMonth['CompanyConfig']['cf_value'] : 0;
            if($mutilMonth == 0){
                $periods = array();
            }
            /**
             * Lay danh sach cac profit center cua employee
             */
            $references = $ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('profit_center_id'),
            ));
			$referencesManagerPC = $ProfitCenter->find('list', array(
                'recursive' => -1,
				'conditions' => array(
                    'NOT' => array('manager_id' => null, 'manager_id' => 0)
                ),
                'fields' => array('manager_id', 'id'),
                'order' => array('id'),
            ));

			$references = $references + $referencesManagerPC;
            /**
             * Lay activity id linked voi project nay.
             */
            $projects = $Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('id', 'activity_id', 'project_manager_id')
            ));
            $idActivityLinked = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
            /**
             * Lay Tat ca cac Task Thuoc Project nay
             */
            $projectTasks = $ProjectTask->find('all', array(
                'conditions' => array('ProjectTask.project_id' => $project_id,'ProjectTask.special'=>0, 'OR' => array('ProjectTask.is_nct IS NULL', 'ProjectTask.is_nct' => 0)),
                'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload', 'profile_id') //Apply staffing by profile
            ));
            //By QN 20150403
			//Apply staffing by profile, modify by ViN 6/6/2015
            $nctasks = $ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.project_id' => $project_id, 'ProjectTask.is_nct' => 1),
                'fields' => array('id','profile_id')
            ));
            $workloadModel = ClassRegistry::init('NctWorkload');
            $workloads = $workloadModel->find('all', array(
                'conditions' => array('project_task_id' => array_keys($nctasks))
            ));
            $fakeTasks = array();
            App::import('Vendor', 'str_utility');
            $str = new str_utility();
            foreach($workloads as $workload){
                $wl = $workload['NctWorkload'];
                if( $wl['group_date'] ){
                    list($nType, $nStart, $nEnd) = explode('_', $wl['group_date']);
                    $nStart = $str->convertToSQLDate($nStart);
                    $nEnd = $str->convertToSQLDate($nEnd);
                } else {
                    $nStart = $nEnd = $wl['task_date'];
                }
                $fakeTasks[] = array(
                    'ProjectTask' => array(
                        'id' => $wl['project_task_id'] . '-' . $wl['id'],
                        'task_start_date' => $nStart,
                        'task_end_date' => $nEnd,
                        'estimated' => $wl['estimated'],
                        'parent_id' => 0,
                        'overload' => 0,
						'profile_id' => isset($nctasks[$wl['project_task_id']]) ? $nctasks[$wl['project_task_id']] : 0
                    ),
                    'ProjectTaskEmployeeRefer' => array(
                        array(
                            'id' => 0,
                            'reference_id' => $wl['reference_id'],
                            'project_task_id' => $wl['project_task_id'],
                            'created' => 0,
                            'updated' => 0,
                            'estimated' => $wl['estimated'],
                            'is_profit_center' => $wl['is_profit_center']
                        )
                    )
                );
            }
            //attach fake tasks to projectTasks
            $projectTasks = array_merge($projectTasks, $fakeTasks);

			$listPCAssignToActivity = $listEmpToActivity = $listEmpForPC = array();
            if(!empty($projectTasks)){
                /**
                 * 3 array can lay ra cuoi cung:
                 * - Du lieu da tinh toan cho employee
                 * - Du lieu da tinh toan cho profit center
                 * - Du lieu da tinh toan cho skill(function)
                 */
                $endDataEmployees = $endDataProfitCenters = $endDataSkills = array();
                /**
                 * Lay danh sach tat ca Id cua Project Task
                 */
                $listProjectTaskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');
                /**
                 * List Lay total consumed cua cac Task va Lay consumed cua tung employee
                 */
                list($totalConsumedTasks, $consumedTaskEmployees) = $this->_consumedTask($listProjectTaskIds, $mutilMonth, $periods);
                /**
                 * Lay consumed cua Previous Task(neu co).
                 */
                if(isset($idActivityLinked) && $idActivityLinked != 0){
                    $consumedPreviousTaskEmployees = $this->_consumedPreviousTask($idActivityLinked, $mutilMonth, $periods);
                }
                /**
                 * Nhung Task nao la Parent thi xoa, khong quan tam den
                 */
                $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
                /*
                foreach($projectTasks as $key => $projectTask){
                    foreach($parentIds as $parentId){
                        if($projectTask['ProjectTask']['id'] == $parentId){
                            unset($projectTasks[$key]);
                        }
                    }
                }
                */
                /**
                 * Phan chia workload cho tung employee va tung Profit Center theo tung Task
                 */

                foreach($projectTasks as $projectTask){
                    $_taskId = $projectTask['ProjectTask']['id'];
                    if(in_array($_taskId, $parentIds)){
                        // task parent, do nothing
                    } else {
                        $_endDate = ($projectTask['ProjectTask']['task_end_date'] != '0000-00-00') ? strtotime($projectTask['ProjectTask']['task_end_date']) : time();
                        $_startDate = ($projectTask['ProjectTask']['task_start_date'] != '0000-00-00') ? strtotime($projectTask['ProjectTask']['task_start_date']) : $_endDate;
                        $_workload = $projectTask['ProjectTask']['estimated'];
                        //$_overload = $projectTask['ProjectTask']['overload'];
                        if(!empty($projectTask['ProjectTaskEmployeeRefer'])){
                            $taskRefers = $projectTask['ProjectTaskEmployeeRefer'];
                            //$gOverload = $this->_caculateGlobal($_overload, count($taskRefers));
                            //$overloads = $gOverload['original'];
                            //$remainder = $gOverload['remainder'];
                            end($taskRefers);
                            $endKey = key($taskRefers);
                            foreach($taskRefers as $key => $taskRefer){
                                $referId = $taskRefer['reference_id'];
                                if($taskRefer['is_profit_center'] == 0){
    								//GET employees assign to project
    								$listEmpToActivity[] = $referId;
    								//GET PC assign to project case task assign to employees
									if(isset($references[$referId]))
    								$listPCAssignToActivity[$references[$referId]] = $references[$referId];
                                    $workloadOfEmployees[$_taskId][$referId]['startDate'] = $_startDate;
                                    $workloadOfEmployees[$_taskId][$referId]['endDate'] = $_endDate;
                                    //$workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads;
    								if(!isset($workloadOfEmployees[$_taskId][$referId]['workload']))
									$workloadOfEmployees[$_taskId][$referId]['workload'] = 0 ;
    								$workloadOfEmployees[$_taskId][$referId]['workload'] += $taskRefer['estimated'];
                                    /*if($key == $endKey){
                                        //$workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads + $remainder;
    									$workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] ;
                                    }*/
									//Apply staffing by profile, modify by ViN 6/6/2015
									$workloadOfEmployees[$_taskId][$referId]['profile_id'] = $projectTask['ProjectTask']['profile_id'];
                                } else {
    								//GET PC assign to project case task assign to PC
    								$listPCAssignToActivity[$referId] = $referId;
                                    $workloadOfProfitCenters[$_taskId][$referId]['startDate'] = $_startDate;
                                    $workloadOfProfitCenters[$_taskId][$referId]['endDate'] = $_endDate;
									if(!isset($workloadOfProfitCenters[$_taskId][$referId]['workload']))
									$workloadOfProfitCenters[$_taskId][$referId]['workload'] = 0;
                                    $workloadOfProfitCenters[$_taskId][$referId]['workload'] += $taskRefer['estimated'];
                                    /*if($key == $endKey){
                                        //$workloadOfProfitCenters[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads + $remainder;
    									$workloadOfProfitCenters[$_taskId][$referId]['workload'] = $taskRefer['estimated'] ;
                                    }*/
									//Apply staffing by profile, modify by ViN 6/6/2015
									$workloadOfProfitCenters[$_taskId][$referId]['profile_id'] = $projectTask['ProjectTask']['profile_id'];
                                }
                            }
                        } else {
                            $workloadOfEmployees[$_taskId][999999999]['startDate'] = $_startDate;
                            $workloadOfEmployees[$_taskId][999999999]['endDate'] = $_endDate;
                            //$workloadOfEmployees[$_taskId][999999999]['workload'] = $_workload + $_overload;
							if(!isset($workloadOfEmployees[$_taskId][999999999]['workload']))
							$workloadOfEmployees[$_taskId][999999999]['workload'] = 0;
    						$workloadOfEmployees[$_taskId][999999999]['workload'] += $_workload;
							//Apply staffing by profile, modify by ViN 6/6/2015
							$workloadOfEmployees[$_taskId][999999999]['profile_id'] = $projectTask['ProjectTask']['profile_id'];
                        }
                    }
                }
                /**
                 * Gan lai du lieu duoc lay ra tu Project Task cho employee
                 */
                $dataEmployees = $workloadOfEmployees;
                $dataEmployeesNotAffecteds = $workloadOfProfitCenters;
                /**
                 * Tinh toan workload cho Employee.
                 * Chuyen tat ca gia tri duoc Assign To cho Profit Center qua Employee va mac dinh
                 * nhung gia tri nay co id la 999.999.999. Tat ca gia tri nay se dua ra Not Affected.
                 */
                if(!empty($dataEmployeesNotAffecteds)){
                    foreach($dataEmployeesNotAffecteds as $taskId => $dataEmployeesNotAffected){
                        foreach($dataEmployeesNotAffected as $pc=>$values){
                            if(isset($values['startDate'])){
                                $_starts = $values['startDate'];
                            }
                            if(isset($values['endDate'])){
                                $_ends = $values['endDate'];
                            }
							$_index = '999999999'.$pc;
                            $dataEmployees[$taskId][$_index]['startDate'] = $_starts;
                            $dataEmployees[$taskId][$_index]['endDate'] = $_ends;
                            if(!isset($dataEmployees[$taskId][$_index]['workload'])){
                                $dataEmployees[$taskId][$_index]['workload'] = 0;
                            }
                            $dataEmployees[$taskId][$_index]['workload'] += $values['workload'];
							//Apply staffing by profile, modify by ViN 6/6/2015
							$dataEmployees[$taskId][$_index]['profile_id'] = $values['profile_id'];
                        }
                    }
                }
                /**
                 * Them consumed cua cac employee vao Task.
                 * Chuyen total consumed task cua employee qua profit center
                 */
                $totalConsumedTaskProfitCenters = array();
                if(!empty($totalConsumedTasks)){
                    foreach($totalConsumedTasks as $taskId => $totalConsumedTask){
                        foreach($totalConsumedTask as $employ => $values){
                            $_values = !empty($values) ? array_sum($values) : 0;
                            $dataEmployees[$taskId][$employ]['consumed'] = $_values;
							//GET PC assign to project case task assign to employees
							if(empty($references[$employ])){
								$references[$employ] = 999999999;
							}
							$listPCAssignToActivity[$references[$employ]] = $references[$employ];
                            //Chuyen consumed employee cho profit center
                            /*foreach($values as $time => $value){
                                $profitCenter = !empty($references[$employ]) ? $references[$employ] : 999999999;
                                if(!isset($totalConsumedTaskProfitCenters[$taskId][$profitCenter][$time])){
                                    $totalConsumedTaskProfitCenters[$taskId][$profitCenter][$time] = 0;
                                }
                                $totalConsumedTaskProfitCenters[$taskId][$profitCenter][$time] += $value;
                            }*/
                        }
                    }
                }

				if(!empty($consumedPreviousTaskEmployees)){
					foreach($consumedPreviousTaskEmployees as $employ => $values){
						//GET PC assign to project case task assign to employees
						$listPCAssignToActivity[$references[$employ]] = $references[$employ];
					}
                }
                $workloadEmployees = array();
                if(!empty($dataEmployees)){
                    /**
                     * De quy tinh toan lai workload cho cac employee
                     */
					if($checkData)
					{
						$totalW = 0;
						foreach($dataEmployees as $values){
							foreach($values as $value){
								if(empty($value['workload'])){
									$value['workload'] = 0;
								}
								$totalW += $value['workload'];
							}
						}
						$HTML .= $project_id . ' | ' . $totalW;
						$workloadEmployees = $this->_recursiveTask($dataEmployees, null, array());
						$totalW = 0;
						foreach($workloadEmployees as $values){
							foreach($values as $value){
								if(empty($value['workload'])){
									$value['workload'] = 0;
								}
								$totalW += $value['workload'];
							}
						}
						$HTML .= ' -- ' . $totalW;
					}
					else
					{
						$workloadEmployees = $this->_recursiveTask($dataEmployees, null, array());
					}
                    /**
                     * Phan chia workload, consumed task, previous task cua employee cho tung thang, tung nam
                     */

                    list($endDataEmployees, $datasSaveOfPCStepOne, $datasSaveOfPCNotAffected, $dataSaveProfile) = $this->_mergeDataSystem($workloadEmployees, $consumedTaskEmployees, $consumedPreviousTaskEmployees, $totalConsumedTasks, 'employee', $project_id, $idActivityLinked);
                }
				if(!empty($endDataEmployees)){
                    $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $project_id), false);
                    if($idActivityLinked != 0){
                        $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $idActivityLinked), false);
                    }
					$TmpStaffingSystem->saveAll($endDataEmployees);
					$TmpStaffingSystem->saveAll($dataSaveProfile);
                }
				/*---Insert staffing for profit center---*/
				$dataSavesOfPC = array();
				foreach($listPCAssignToActivity as $_val)
				{
					$employeeLists = array_keys($references,$_val);
					$_datasSaffingEmp = $TmpStaffingSystem->find('all',array(
						'recursive' => -1,
						'conditions' => array(
							'project_id' => $project_id,
							'activity_id' => $idActivityLinked,
							'model' => 'employee',
							'model_id' => $employeeLists,
							'company_id' => $companyId
						),
						'fields' => array('date,SUM(estimated) AS estimated,SUM(consumed) AS consumed'),
						'group' => array('FROM_UNIXTIME(`date`, "%m-%Y")')
					));
					$_datasSaffingEmp = Set::combine($_datasSaffingEmp, '{n}.TmpStaffingSystem.date', '{n}.0');
					foreach($_datasSaffingEmp as $time=>$values)
					{
						$idTemp = $_val.'_'.$time;
						$_estimated = 0 ;
						if(isset($datasSaveOfPCStepOne[$idTemp]))
						{
							$_estimated += $datasSaveOfPCStepOne[$idTemp]['estimated'];
							unset($datasSaveOfPCStepOne[$idTemp]);
						}
						$_estimated += $values['estimated'] ;
						$dataSavesOfPC[] = array(
							'project_id' => $project_id,
							'activity_id' => $idActivityLinked,
							'model' => 'profit_center',
							'model_id' => $_val,
							'date' => $time,
							'estimated' => $_estimated,
							'consumed' => !empty($values['consumed']) ? $values['consumed'] : 0,
							'company_id' => $companyId
						);
					}
				}
				$dataSavesOfPC = array_merge($dataSavesOfPC,$datasSaveOfPCNotAffected,array_values($datasSaveOfPCStepOne));
				if(!empty($dataSavesOfPC)){
                    $TmpStaffingSystem->saveAll($dataSavesOfPC);
                }
				/*-------------------------------------------------
				Remove staffing by skill, apply from VER 06/2015
				-------------------------------------------------*/
				/*---Get list PC refers skill---*/
				/*$projectTeams = $ProjectTeam->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTeam.project_id' => $project_id),
                    'fields' => array('id', 'id'),
                    'order' => array('id')
                ));
				$functionRefers = $ProjectFunctionEmployeeRefer->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_team_id' => $projectTeams),
                    'fields' => array('id', 'function_id','profit_center_id'),
                    'order' => array('function_id' => 'ASC'),
                ));
				$profitCenterRefers = !empty($functionRefers) ? Set::combine($functionRefers, '{n}.ProjectFunctionEmployeeRefer.profit_center_id', '{n}.ProjectFunctionEmployeeRefer.function_id') : array();
				$dataSavesOfSkill = array();
				foreach($dataSavesOfPC as $values)
				{
					$idPC = $values['model_id'];
					$time = $values['date'];
					if($idPC == 999999999 || !isset($profitCenterRefers[$idPC]))
					$idSkill = 999999999;
					elseif(isset($profitCenterRefers[$idPC]))
					$idSkill = ($profitCenterRefers[$idPC] == 0 || $profitCenterRefers[$idPC] == '') ? 999999999 : $profitCenterRefers[$idPC];
					$idSkillByTime = $idSkill.'_'.$time;
					if(!isset($dataSavesOfSkill[$idSkillByTime] ))
					{
						$dataSavesOfSkill[$idSkillByTime] = $values;
						$dataSavesOfSkill[$idSkillByTime]['model'] = 'skill';
						$dataSavesOfSkill[$idSkillByTime]['model_id'] = $idSkill;
					}
					else
					{
						$dataSavesOfSkill[$idSkillByTime]['estimated'] += $values['estimated'];
						$dataSavesOfSkill[$idSkillByTime]['consumed'] += $values['consumed'];
					}
				}
				$dataSavesOfSkill = array_values($dataSavesOfSkill);
				if(!empty($dataSavesOfSkill)){
                    $TmpStaffingSystem->saveAll($dataSavesOfSkill);
                }*/
            }
			else {
                $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $project_id), false);
                if($idActivityLinked != 0){
                    $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $idActivityLinked), false);
                }
            }
            // else {
               // xoa staffing vi ko co task
                // $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $project_id), false);
                // if($idActivityLinked != 0){
                    // $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $idActivityLinked), false);
                // }
            // }

            //By QN - 2015/04/03
            // $nctasks = $ProjectTask->find('all', array(
            //     'recursive' => -1,
            //     'conditions' => array('ProjectTask.project_id' => $project_id, 'ProjectTask.is_nct' => 1),
            //     'fields' => array('id', 'overload')
            // ));
            // $workloadModel = ClassRegistry::init('NctWorkload');
            // if( !empty($nctasks) ){
            //     //attach workload from nct_workloads
            //     foreach($nctasks as $task){
            //         $task['Workload'] = $workloadModel->find('all', array(
            //             'conditions' => array(
            //                 'NctWorkload.project_task_id' => $task['ProjectTask']['id']
            //             )
            //         ));
            //     }
            // }
            // else {
            //     //xoa staffing vi ko co task
            //     $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $project_id), false);
            //     if($idActivityLinked != 0){
            //         $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $idActivityLinked), false);
            //     }
            // }
			//REMOVE REBUILD STAFFING
			$Project->id = $project_id;
			$saved['rebuild_staffing'] = 0;
			$Project->save($saved);

			if($idActivityLinked != 0)
			{
				$Activity = ClassRegistry::init('Activity');
				$Activity->id = $idActivityLinked;
				$Activity->save($saved);
			}
			if($checkData)
			{
				return $HTML;
			}
			//END
        }
     }

	public function _mergeDataSystem($workloads = array(), $consumeds = array(), $previous = array(), $totalConsumedTasks = array(), $model = null, $project_id = null, $activity_id = null){
		return $this->Behaviors->Lib->mergeDataSystem($workloads, $consumeds, $previous, $totalConsumedTasks, $model, $project_id, $activity_id);
	}
	public function _recursiveTask($tasks = array(), $valueAdds = null, $tmpTasks = array()){
		return $this->Behaviors->Lib->recursiveTask($tasks,$valueAdds,$tmpTasks);
	}
	public function _resetRemainSystem($datas = array()){
		return $this->Behaviors->Lib->resetRemainSystem($datas);
	}

     /**
      * Lay consumed cua tung Project Task.
      * Dau vao:
      * - 1 array cac Project Task thuoc 1 Project nao do.
      * Dau ra:
      * - 1 array co gia tri nhu sau:
      * array(
      *     'Task_Id' => array(
      *         'Employee_id' => array(
      *             'Date' => value_consumed,
      *             'Date' => value_consumed
      *         )
      *     )
      * )
      *
      * - 1 array co gia tri nhu sau:
      * array(
      *     'Employee_id' => array(
      *         'Date' => array(
      *             'consumed' => value_consumed
      *         ),
      *         'Date' => array(
      *             'consumed' => value_consumed
      *         )
      *     )
      * )
      * @access private
      * @return array()
      * @author HuuPC
      *
      */
     private function _consumedTask($listProjectTaskIds = array(), $mutilMonth = 0, $periods = array()){
        $employeeName = CakeSession::read('Auth.employee_info.Company.id');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $ActivityRequest = ClassRegistry::init('ActivityRequest');

        $totalConsumedTasks = $consumedTaskEmployees = array();
        if(!empty($listProjectTaskIds)){
            $activityTasks = $ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.project_task_id' => $listProjectTaskIds),
                'fields' => array('project_task_id', 'id')
            ));
            if(!empty($activityTasks)){
                $requests = $ActivityRequest->find('all',array(
                    'recursive'     => -1,
                    'fields'        => array('id', 'date', 'employee_id', 'task_id', 'value'),
                    'conditions'    => array(
                        'status'        => 2,
                        'task_id'       => $activityTasks,
                        'company_id'    => $employeeName,
                        'NOT'           => array('value' => 0, "task_id" => null),
                    )
                ));
                $_totalConsumedTasks = array();
                if(!empty($requests)){
                    foreach($requests as $request){
                        $dx = $request['ActivityRequest'];
                        $taskId = $dx['task_id'];
                        $employ = $dx['employee_id'];
                        $date = strtotime('01-'. date('m-Y', $dx['date']));
                        if(!empty($periods) && $mutilMonth == 1){
                            $moc = !empty($periods[$date]) ? $periods[$date] : '';
                            if(!empty($moc)){
                                if($dx['date'] > $moc){
                                    $date = mktime(0, 0, 0, date("m", $date)+1, date("d", $date), date("Y", $date));
                                } else {

                                }
                            }
                        }
						//$date = $dx['date'];
                        $value = $dx['value'];
                        if(!isset($_totalConsumedTasks[$taskId][$employ][$date])){
                            $_totalConsumedTasks[$taskId][$employ][$date] = 0;
                        }
                        $_totalConsumedTasks[$taskId][$employ][$date] += $value;

                        if(!isset($consumedTaskEmployees[$employ][$date]['consumed'])){
                            $consumedTaskEmployees[$employ][$date]['consumed'] = 0;
                        }
                        $consumedTaskEmployees[$employ][$date]['consumed'] += $value;
                    }
                }
                $activityTasks = array_flip($activityTasks);
                if(!empty($_totalConsumedTasks)){
                    foreach($_totalConsumedTasks as $aTaskId => $_totalConsumedTask){
                        $pTaskId = !empty($activityTasks[$aTaskId]) ? $activityTasks[$aTaskId] : 0;
                        $totalConsumedTasks[$pTaskId] = $_totalConsumedTask;
                    }
                }
            }
        }
        return  array($totalConsumedTasks, $consumedTaskEmployees);
     }

     /**
      * Lay consumed cua Previous Task.
      * Dau vao:
      * - 1 activity id. Doi voi project thi truong hop project co linked voi activity(PMS = YES)
      * Dau ra:
      * - 1 array co gia tri nhu sau:
      * array(
      *     'Employee_id' => array(
      *         'Date' => array(
      *             'consumed' => value_consumed
      *         ),
      *         'Date' => array(
      *             'consumed' => value_consumed
      *         )
      *     )
      * )
      *
      * @access private
      * @return array()
      * @author HuuPC
      */
     public function _consumedPreviousTask($activityId = null, $mutilMonth = 0, $periods = array()){
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $employeeName = CakeSession::read('Auth.employee_info.Company.id');
        $previous = $ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array('id', 'date', 'employee_id', 'value'),
            'conditions'    => array(
                'status'        => 2,
                'activity_id'   => $activityId,
                'company_id'    => $employeeName,
                'NOT'           => array('value' => 0),
            )
        ));
        $previousTaskEmployees = array();
        if(!empty($previous)){
            foreach($previous as $previou){
                $dx = $previou['ActivityRequest'];
                $employ = $dx['employee_id'];
                $date = strtotime('01-'.date('m-Y', $dx['date']));
                if(!empty($periods) && $mutilMonth == 1){
                    $moc = !empty($periods[$date]) ? $periods[$date] : '';
                    if(!empty($moc)){
                        if($dx['date'] > $moc){
                            $date = mktime(0, 0, 0, date("m", $date)+1, date("d", $date), date("Y", $date));
                        } else {

                        }
                    }
                }
                $value = $dx['value'];
                if(!isset($previousTaskEmployees[$employ][$date]['consumed'])){
                    $previousTaskEmployees[$employ][$date]['consumed'] = 0;
                }
                $previousTaskEmployees[$employ][$date]['consumed'] += $value;
            }
        }

        return $previousTaskEmployees;
     }

	 /*-------------------
	@Get lists consumed by day
	INPUT : Array : Project
	OUTPUT: Array : Tasks not special-nct-parent (parent = level 1 + exists children task)
	-------------------*/
	function getProjectTaskNotSpecialAndNct($project,$getEstimated = false)
	{
		$taskLists = $this->find('all',array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project,
					'special' => 0,
					'OR' => array(
						'is_nct IS NULL',
						'is_nct' => 0
					),
				),
				'fields'=>array(
					'id','parent_id','estimated'
				)
			)
		);
		$childrenIds = array();
		$parentIds = array_unique(Set::classicExtract($taskLists, '{n}.ProjectTask.parent_id'));
		$estimated = array_unique(Set::classicExtract($taskLists, '{n}.ProjectTask.estimated'));
		foreach($taskLists as $key => $task){
			foreach($parentIds as $parentId){
				if($task['ProjectTask']['id'] == $parentId){
					//Do nothing
				}
				else{
					if($getEstimated )
					{
						$childrenIds[$task['ProjectTask']['id']] = array( 'id' => $task['ProjectTask']['id'], 'estimated' => $task['ProjectTask']['estimated'] );
					}
					else
					{
						$childrenIds[$task['ProjectTask']['id']] = $task['ProjectTask']['id'];
					}

				}
			}
		}
		$taskLists = $childrenIds;
		return $taskLists;
	}
}
?>
