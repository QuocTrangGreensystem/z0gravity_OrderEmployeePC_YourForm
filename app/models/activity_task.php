<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityTask extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ActivityTask';
	var $actsAs = array('Lib');
    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The information is not blank!'
            )
        )
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

    /**
     *  Detailed list of belongsTo associations.
     *
     * @var array
     */
     public $belongsTo = array(
        'Activity' => array(
            'className' => 'Activity',
            'foreignKey' => 'activity_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    var $hasMany = array(
		'ActivityTaskEmployeeRefer' => array(
			'className' => 'ActivityTaskEmployeeRefer',
			'foreignKey' => 'activity_task_id',
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
    public function beforeFind($queryData) {
        $defaultConditions = array('ActivityTask.special' => array(0,1));
		if(empty($queryData['conditions']))
		{
			$queryData['conditions']=$defaultConditions;
		}
		else
		{
			if(!isset($queryData['conditions']['ActivityTask.special'])&&!isset($queryData['conditions']['special']))
			{
				$queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
			}
		}
        return $queryData;
    }


    /**
     * Tong hop Workload tu Project Task, Workload* = Worload + Overload.
     * Tong hop Consumed tu Activity Request. Consumed cua cac Task cua Project co linked voi Activity
     *
     * @access public
     * @return void
     * @author HuuPC
     *
     */
     public function staffingSystem($activity_id = null, $checkData = false){
        ini_set('memory_limit', '1024M');
		$HTML = '';
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $ProjectTeam = ClassRegistry::init('ProjectTeam');
        $ProjectFunctionEmployeeRefer = ClassRegistry::init('ProjectFunctionEmployeeRefer');
        $ProjectEmployeeProfitFunctionRefer = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
        $ProjectEmployeeManager = ClassRegistry::init('ProjectEmployeeManager');
        $TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
		$ProfitCenter = ClassRegistry::init('ProfitCenter');
		$Activity = ClassRegistry::init('Activity');
        $Period = ClassRegistry::init('Period');
        $CompanyConfig = ClassRegistry::init('CompanyConfig');
        //GET company ID
        $companyId = $Activity->find('first', array(
			'recursive' => -1,
			'conditions' => array('Activity.id' => $activity_id),
			'fields' => array('company_id','project')
		));
		if($companyId['Activity']['project'] && is_numeric($companyId['Activity']['project']))
		{
			ClassRegistry::init('ProjectTask')->staffingSystem($companyId['Activity']['project']);
			return;
		}
		$companyId = !empty($companyId) ? $companyId['Activity']['company_id'] : array();
        $workloadOfEmployees = $workloadOfProfitCenters = $consumedTasks = $consumedPreviousTasks = $consumedPreviousTaskEmployees = array();
        $totalConsumedTasks = $consumedTaskEmployees = array();
        if(!empty($activity_id)){
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
                'order' => array('profit_center_id')
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
			//debug($references);
            /**
             * Lay Tat ca cac Task Thuoc Activity nay
             */
            $activityTasks = $ActivityTask->find('all', array(
                'conditions' => array('ActivityTask.activity_id' => $activity_id,'ActivityTask.special' => 0, 'OR' => array('ActivityTask.is_nct IS NULL', 'ActivityTask.is_nct' => 0)),
                'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload', 'profile_id') //Apply staffing by profile
            ));
            //By QN 20150403
			//Apply staffing by profile, modify by ViN 6/6/2015
            $nctasks = $ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.activity_id' => $activity_id, 'ActivityTask.is_nct' => 1),
                'fields' => array('id','profile_id')
            ));
            $workloadModel = ClassRegistry::init('NctWorkload');
            $workloads = $workloadModel->find('all', array(
                'conditions' => array('activity_task_id' => array_keys($nctasks)),
                'order' => array('activity_task_id' => 'ASC')
            ));
			//END
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
                    'ActivityTask' => array(
                        'id' => $wl['activity_task_id'] . '-' . $wl['id'],
                        'task_start_date' => strtotime($nStart),
                        'task_end_date' => strtotime($nEnd),
                        'estimated' => $wl['estimated'],
                        'parent_id' => 0,
                        'overload' => 0,
                        'profile_id' => isset($nctasks[$wl['activity_task_id']]) ? $nctasks[$wl['activity_task_id']] : 0 
                    ),
                    'ActivityTaskEmployeeRefer' => array(
                        array(
                            'id' => 0,
                            'reference_id' => $wl['reference_id'],
                            'activity_task_id' => $wl['activity_task_id'],
                            'created' => 0,
                            'updated' => 0,
                            'estimated' => $wl['estimated'],
                            'is_profit_center' => $wl['is_profit_center']
                        )
                    )
                );
            }
            //attach fake tasks to Tasks
            $activityTasks = array_merge($activityTasks, $fakeTasks);

            /**
             * Lay consumed cua Previous Task(neu co).
             */
            $consumedPreviousTaskEmployees = $this->_consumedPreviousTask($activity_id, $mutilMonth, $periods);
            /**
             * 2 array can lay ra cuoi cung:
             * - Du lieu da tinh toan cho employee
             * - Du lieu da tinh toan cho profit center
             */
            $endDataEmployees = $endDataProfitCenters = array();
            $listPCAssignToActivity = $listEmpToActivity = $listEmpForPC = array();
            if(!empty($activityTasks)){
                /**
                 * Lay danh sach tat ca Id cua Activity Task
                 */
                $listActivityTaskIds = array_merge(Set::classicExtract($activityTasks, '{n}.ActivityTask.id'), $nctasks);
                /**
                 * List Lay total consumed cua cac Task va Lay consumed cua tung employee
                 */
                list($totalConsumedTasks, $consumedTaskEmployees) = $this->_consumedTask($listActivityTaskIds, $mutilMonth, $periods);
                /**
                 * Nhung Task nao la Parent thi xoa, khong quan tam den
                 */
                $parentIds = array_unique(Set::classicExtract($activityTasks, '{n}.ActivityTask.parent_id'));
                /**
                 * Phan chia workload cho tung employee va tung Profit Center theo tung Task
                 */
                foreach($activityTasks as $activityTask){
                    $_taskId = $activityTask['ActivityTask']['id'];
                    if(in_array($_taskId, $parentIds)){
                        // task parent, do nothing
                    } else {
                        $_endDate = !empty($activityTask['ActivityTask']['task_end_date']) ? $activityTask['ActivityTask']['task_end_date'] : time();
                        $_startDate = !empty($activityTask['ActivityTask']['task_start_date']) ? $activityTask['ActivityTask']['task_start_date'] : $_endDate;

                        $_workload = $activityTask['ActivityTask']['estimated'];
                        //$_overload = $activityTask['ActivityTask']['overload'];
    					//var_dump($activityTask['ActivityTaskEmployeeRefer']); echo '-';
                        if(!empty($activityTask['ActivityTaskEmployeeRefer'])){
                            $taskRefers = $activityTask['ActivityTaskEmployeeRefer'];
                            //$gOverload = $this->_caculateGlobal($_overload, count($taskRefers));
                            //$overloads = $gOverload['original'];
                           // $remainder = $gOverload['remainder'];
                            end($taskRefers);
                            $endKey = key($taskRefers);
                            foreach($taskRefers as $key => $taskRefer){
                                $referId = $taskRefer['reference_id'];
                                if($taskRefer['is_profit_center'] == 0){
    								//GET employees assign to project
    								$listEmpToActivity[] = $referId;
    								//echo $referId;
    								//GET PC assign to project case task assign to employees
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
									$workloadOfEmployees[$_taskId][$referId]['profile_id'] = $activityTask['ActivityTask']['profile_id'];
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
									$workloadOfProfitCenters[$_taskId][$referId]['profile_id'] = $activityTask['ActivityTask']['profile_id'];
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
							$workloadOfEmployees[$_taskId][999999999]['profile_id'] = $activityTask['ActivityTask']['profile_id'];
                        }
                    }
                }
            }
            else{
                $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $activity_id), false);
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
						$listPCAssignToActivity[$references[$employ]] = $references[$employ];
                        //Chuyen consumed employee cho profit center
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
					//echo $activity_id . ' | ' . $totalW;
					$HTML .= $activity_id . ' | ' . $totalW;
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
            }
            /**
             * Phan chia workload, consumed task, previous task cua employee cho tung thang, tung nam
             */
            // pr($workloadEmployees);
            // pr($consumedTaskEmployees);
            // pr($consumedPreviousTaskEmployees);
            // pr($totalConsumedTasks);
            // die;
            list($endDataEmployees, $datasSaveOfPCStepOne, $datasSaveOfPCNotAffected, $dataSaveProfile) = $this->_mergeDataSystem($workloadEmployees, $consumedTaskEmployees, $consumedPreviousTaskEmployees, $totalConsumedTasks, 'employee', 0, $activity_id, $mutilMonth, $periods);
            if(!empty($endDataEmployees)){
                $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $activity_id), false);
                $TmpStaffingSystem->saveAll($endDataEmployees);
				$TmpStaffingSystem->saveAll($dataSaveProfile);
            }
			/*---Insert staffing for profit center---*/
			$dataSavesOfPC = array();
			$project_id = 0;
			foreach($listPCAssignToActivity as $_val)
			{
				$employeeLists = array_keys($references,$_val);
				$_datasSaffingEmp = $TmpStaffingSystem->find('all',array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $project_id,
						'activity_id' => $activity_id,
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
						'activity_id' => $activity_id,
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
			//REMOVE REBUILD STAFFING
			$saved['rebuild_staffing'] = 0;
			$Activity->id = $activity_id;
			$Activity->save($saved);
			//END
			if($checkData)
			{
				return $HTML;
			}
        }
     }

	public function _mergeDataSystem($workloads = array(), $consumeds = array(), $previous = array(), $totalConsumedTasks = array(), $model = null, $project_id = null, $activity_id = null, $mutilMonth = 0, $periods = array()){
		return $this->Behaviors->Lib->mergeDataSystem($workloads, $consumeds, $previous, $totalConsumedTasks, $model, $project_id, $activity_id, $mutilMonth, $periods);
	}
	public function _recursiveTask($tasks = array(), $valueAdds = null, $tmpTasks = array()){
		return $this->Behaviors->Lib->recursiveTask($tasks,$valueAdds,$tmpTasks);
	}
	public function _resetRemainSystem($datas = array()){
		return $this->Behaviors->Lib->resetRemainSystem($datas);
	}
	public function caculateGlobal($total, $number, $exactlyAlgorithm = true){
		return $this->Behaviors->Lib->caculateGlobal($total, $number, $exactlyAlgorithm);
	}
	public function getWorkingDays($start, $end, $allowHolidayInMonth = false){
		return $this->Behaviors->Lib->getWorkingDays($start, $end, $allowHolidayInMonth);
	}
	public function getWorkingDaysByMonths($start, $end){
		return $this->Behaviors->Lib->getWorkingDaysByMonths($start, $end);
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
     private function _consumedTask($listActivityTaskIds = array(), $mutilMonth = 0, $periods = array()){
        $employeeName = CakeSession::read('Auth.employee_info.Company.id');
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $totalConsumedTasks = $consumedTaskEmployees = array();
        if(!empty($listActivityTaskIds)){
            $requests = $ActivityRequest->find('all',array(
                'recursive'     => -1,
                'fields'        => array('id', 'date', 'employee_id', 'task_id', 'value'),
                'conditions'    => array(
                    'status'        => 2,
                    'task_id'       => $listActivityTaskIds,
                    'company_id'    => $employeeName,
                    'NOT'           => array('value' => 0, "task_id" => null),
                )
            ));
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
                    $value = $dx['value'];
                    if(!isset($totalConsumedTasks[$taskId][$employ][$date])){
                        $totalConsumedTasks[$taskId][$employ][$date] = 0;
                    }
                    $totalConsumedTasks[$taskId][$employ][$date] += $value;

                    if(!isset($consumedTaskEmployees[$employ][$date]['consumed'])){
                        $consumedTaskEmployees[$employ][$date]['consumed'] = 0;
                    }
                    $consumedTaskEmployees[$employ][$date]['consumed'] += $value;
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
        $employeeName = CakeSession::read('Auth.employee_info.Company.id');
        $ActivityRequest = ClassRegistry::init('ActivityRequest');

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

	  function testBehavior()
	  {
		$this->Behaviors->Lib->testSession();
	  }
	function _getEmpoyee()
	{
		App::import('Component', 'SessionComponent');
		$session = new SessionComponent();
		$auth = $session->read("Auth.employee_info");
		if (empty($auth['Company']['id'])) {
			return false;
		}
		$auth['Employee']['company_id'] = $auth['Company']['id'];
		$auth['Employee']['day_established'] = !empty($auth['Company']['day_established']) ? $auth['Company']['day_established'] : '';
		return $auth['Employee'];
	}
 	 public function capacityFromForecastPC($profitCenterIds = array(), $startDate, $endDate, $applyForProject = false, $dateType = 'month', $listDays = null){
		$session = new SessionComponent();
		$companyConfigs = $session->read('companyConfigs');
		$showMonthMultiple = false;
        $listEmployees = array();
		if($dateType == 'month')
		{
			$startDate = mktime(0, 0, 0, date("m", $startDate)-1, date("d", $startDate), date("Y", $startDate));
			$endDate = mktime(0, 0, 0, date("m", $endDate)+1, date("d", $endDate), date("Y", $endDate));


			$listMonthPeriod = array();
            /**
             * Hide multiple month
             */
			
		}
		$ProjectEmployeeProfitFunctionRefer = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
		$AbsenceRequest = ClassRegistry::init('AbsenceRequest');
		$employeeName = $this->_getEmpoyee();
		$dayEstablished = $employeeName['day_established'];
		$results = array();
		if($dateType == 'month')
		{
			$TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
		}
		else
		{
			$ActivityRequest = ClassRegistry::init('ActivityRequest');
		}
		$empOfPc = array();
		if($dateType == 'month'){
			// Optimize time load in screen project staffing
			// Created by Viet Nguyen
			$_joins = array(
				array(
					'table' => 'employees',
					'alias' => 'Employee',
					'type' => 'LEFT',
					'foreignKey' => 'employee_id',
					'conditions'=> array(
						'ProjectEmployeeProfitFunctionRefer.employee_id = Employee.id'
					)
				)
			);
			unset($ProjectEmployeeProfitFunctionRefer->virtualFields['end_date']);
			$allEmp = $ProjectEmployeeProfitFunctionRefer->find('all', array(
					'recursive' => -1,
					'joins' => $_joins,
					'conditions' => array(
						'NOT' => $this->notConditions($startDate, $endDate),
						'ProjectEmployeeProfitFunctionRefer.profit_center_id'=> $profitCenterIds,
					),
					'fields' => array('DISTINCT employee_id','Employee.start_date','Employee.end_date','Employee.capacity_by_year', 'ProjectEmployeeProfitFunctionRefer.profit_center_id')
			));
			$listEmployees = array();
			
			foreach($allEmp as $key => $_data){
				$dx = $_data['ProjectEmployeeProfitFunctionRefer'];
				$_data = array_merge($_data['ProjectEmployeeProfitFunctionRefer'],$_data['Employee']);
				if(strtotime($_data['start_date']) < $dayEstablished)
				{
					$_data['start_date'] = $dayEstablished;
				}
				$startTmp = strtotime($_data['start_date']);
				$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
				$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
				
				
				if(empty($empOfPc[$dx['profit_center_id']])){
					$empOfPc[$dx['profit_center_id']] = array();
				}
				$empOfPc[$dx['profit_center_id']][$dx['employee_id']] = $_data;
				$listEmployees[] = $dx['employee_id'];
			}
			
			$totalConsumed = $TmpStaffingSystem->getConsumedByDateFromDataStaffingAllEmployees($startDate, $endDate, $employeeName['company_id'], array(), $listEmployees);

			$totalConsumedEmployee = array();
			foreach($totalConsumed as $key => $value){
				$dx = $value['TmpStaffingSystem'];
				if(empty($totalConsumedEmployee[$dx['model_id']])){
					$totalConsumedEmployee[$dx['model_id']] = array();
				}
				if(empty($totalConsumedEmployee[$dx['model_id']][$dx['date']])){
					$totalConsumedEmployee[$dx['model_id']][$dx['date']] = 0;
				}
				$totalConsumedEmployee[$dx['model_id']][$dx['date']] = $value[0]['consumed'];
			}
		}else{	
			foreach($profitCenterIds as $_index=>$profitCenterId){
				if($applyForProject)
				{
					//Project Staffing don't apply show staffing by Profit center and Tree structure
					$getAllChildrenOfPC = $profitCenterId;
				}
				else
				{
					$getAllChildrenOfPC = ClassRegistry::init('ProfitCenter')->children($profitCenterId);
					$getAllChildrenOfPC = Set::classicExtract($getAllChildrenOfPC,'{n}.ProfitCenter.id');
					$getAllChildrenOfPC[] = $profitCenterId;
				}
				//MODIFY BY VINGUYEN 2015-01-20 : apply start_date, end_date when select employee
				$_joins = array(
					array(
						'table' => 'employees',
						'alias' => 'Employee',
						'type' => 'LEFT',
						'foreignKey' => 'employee_id',
						'conditions'=> array(
							'ProjectEmployeeProfitFunctionRefer.employee_id = Employee.id'
						)
					)
				);
				unset($ProjectEmployeeProfitFunctionRefer->virtualFields['end_date']);
				$allEmp = $ProjectEmployeeProfitFunctionRefer->find('all', array(
						'recursive' => -1,
						'joins' => $_joins,
						'conditions' => array(
							'NOT' => $this->notConditions($startDate, $endDate),
							'ProjectEmployeeProfitFunctionRefer.profit_center_id'=>$getAllChildrenOfPC,
						),
						'fields' => array('DISTINCT employee_id','Employee.start_date','Employee.end_date','Employee.capacity_by_year')
				));
				//END
				foreach($allEmp as $_index=>$_data)
				{
					$_data = array_merge($_data['ProjectEmployeeProfitFunctionRefer'],$_data['Employee']);
					if(strtotime($_data['start_date']) < $dayEstablished)
					{
						$_data['start_date'] = $dayEstablished;
					}
					$startTmp = strtotime($_data['start_date']);
					$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
					$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
					$empOfPc[$profitCenterId][$_data['employee_id']] = $_data;
					$_data['capacity_by_year'] = $_data['capacity_by_year'];
					$listEmployees[] = $_data['employee_id'];
				}
				// GET DATA COMSUMED OF ALL ACTIVITY FOR PC
				
				$totalConsumed[$profitCenterId] = $ActivityRequest->getConsumedByDateFromDataRequest($startDate, $endDate, $employeeName['company_id'], $listEmployees, $dateType);
			}
		}
        //GET DATA
		$_startDate = $startDate;
		$_endDate = $endDate;
		if($dateType == 'day')
		{
			foreach($listDays as $time){
				//GET CONFIG CAPACITY MONTH
				$_startDate = $time;
				$getMonth = date('n',$_startDate);
				$keyCapacityByMonth = 'capacity_by_month_'.$getMonth;
				$theoreticalCapacity = isset($companyConfigs[$keyCapacityByMonth]) ? $companyConfigs[$keyCapacityByMonth]*0.01 : 0;
				//END
				$keyAbsence = date("d_m_Y", $_startDate);
				//WORKING DAY = DAY OF MONTH - DAY OFF - HOLIDAY : changed 2015/03/28
				//$workingDay = 1;
				$workingDay = $this->getWorkingDays($_startDate, $_startDate, true); //KHI NAO RANH, XAY DUNG FUNCTION CHECK 1 ngay co phai la working day k.
				foreach($profitCenterIds as $profitCenterId){
					$pros = 0 ;
					$capas = 0 ;
					$absens = 0;
					$capacityTheoretical = 0;
					$employeeCalFullAbsence = array();

					if(!empty($empOfPc[$profitCenterId]))
					{
						foreach( $empOfPc[$profitCenterId] as $key => $val )
						{
							$TEMP = 0;
							if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
							{
								$TEMP = 1;
								$pros ++;
								$capas ++;
								$employeeCalFullAbsence[] = $key;
							}
							else
							{
								if(date('d-m-Y',$val['start_date']) == date('d-m-Y',$_startDate))
								{
									$TEMP = 1;
									//start_date between of month
									$pros ++;
									$capas ++;
									//$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($key, $val['start_date'], $_startDate, 'validate', $dateType);
									//$absens += isset($absenTmp[$key][$keyAbsence]) ? $absenTmp[$key][$keyAbsence] : 0;
									$employeeCalFullAbsence[] = $key;
								}
							}
							if($TEMP ==  1)
							$capacityTheoretical += $val['capacity_by_year']*$theoreticalCapacity; //0.0833 is default value
						}
					}
					$absenTmp = $AbsenceRequest->sumAbsenceByDateForPC($employeeCalFullAbsence, $_startDate, $_startDate,'validated',$dateType);
					$absens += !empty($absenTmp[$keyAbsence]) ? $absenTmp[$keyAbsence] : 0;
					//$absens = !empty($absences[$profitCenterId][$keyAbsence]) ? $absences[$profitCenterId][$keyAbsence] : 0;
					if($workingDay == 0) $capas = 0; //Neu la ngay nghi thi capacity cua PC bang 0, hard fix
					$results[$profitCenterId][$_startDate]=array();
					if($capas < 0 || $capas == 0){
						$results[$profitCenterId][$_startDate]['capacity'] = 0;
						$results[$profitCenterId][$_startDate]['totalEmployee'] =0;
					} else {
						$results[$profitCenterId][$_startDate]['capacity'] = $capas - $absens;
						$results[$profitCenterId][$_startDate]['totalEmployee'] = $pros;
					}
					//$results[$profitCenterId][$_startDate]['working'] = $capas;
					$results[$profitCenterId][$_startDate]['working'] = $workingDay;
					$results[$profitCenterId][$_startDate]['absence'] = $absens;
					$results[$profitCenterId][$_startDate]['capacity_theoretical'] = round($capacityTheoretical,2);
					$results[$profitCenterId][$_startDate]['notValidated'] = $results[$profitCenterId][$_startDate]['capacity'];
					if(isset($totalConsumed[$profitCenterId][$_startDate]))
					{
						$results[$profitCenterId][$_startDate]['notValidated'] -= $totalConsumed[$profitCenterId][$_startDate];
					}
				}
			}
		}
		else
		{
            // phan filte week chua optimize time load 
            $mrCapacity = array();
            $EmployeeMultiResource = ClassRegistry::init('EmployeeMultiResource');
            $listMR = $EmployeeMultiResource->isMultipleResources($listEmployees);
            $dt = new DateTime();
            $dt->setTimestamp($startDate);
            $dt->modify('-1 month');
            $_start = $dt->getTimestamp();
            $dt->setTimestamp($endDate);
            $dt->modify('+1 month');
            $_end = $dt->getTimestamp();
            $mrCapacity = $EmployeeMultiResource->getCapacity($listMR, $_start, $_end, $dateType);
            unset($mrCapacity['total']);
            //
			if($dateType == 'week')
			{
				$listDays = array_values($listDays);
			}
			$i=0;
			$totalConsumedPc = array();
			while($_startDate <= $_endDate){
				if($dateType == 'week')
				{
					$keyWeek = $this->Behaviors->Lib->getDateByWeekday($_startDate);
					$_startDate = $keyWeek;
					if(isset($listWeeks[$keyWeek]))
					{
						$_startDate = isset($listDays[$i]) ? $listDays[$i] : $_endDate+1;
						$i++;
						continue;
						// break;
					}
					else
					{
						$listWeeks[$keyWeek] = $keyWeek;
						$_endDateTmpOfWeek = $this->Behaviors->Lib->getDateByWeekday($_startDate,'Saturday');
					}
				}
				//GET CONFIG CAPACITY MONTH
				$getMonth = date('n',$_startDate);
				$keyCapacityByMonth = 'capacity_by_month_'.$getMonth;
				$theoreticalCapacity = isset($companyConfigs[$keyCapacityByMonth]) ? $companyConfigs[$keyCapacityByMonth]*0.01 : 0;
				//END
                $keyConsumed  = '' ;
				if($dateType == 'week')
				{
					$keyAbsence = date('W_Y',$_startDate);
					$keyConsumed = date('W_m_Y',$_startDate);
				}
				else
				{
					$keyAbsence = date('d_m_Y',$_startDate);
				}
				//$keyAbsence = date("d_m_Y", $_startDate);
				$totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $_startDate), date("Y", $_startDate));
				//$_endDateTmp=strtotime($totalDayOfMonth.'-'.date("m-Y", $_startDate));
				$_endDateTmp= isset($_endDateTmpOfWeek) && $_endDateTmpOfWeek ? $_endDateTmpOfWeek : strtotime($totalDayOfMonth.'-'.date("m-Y", $_startDate));
				//WORKING DAY = DAY OF MONTH - DAY OFF - HOLIDAY : changed 2015/03/28
				$workingDay = $this->getWorkingDays($_startDate, $_endDateTmp, true);
				// debug($empOfPc[397]);
				foreach($profitCenterIds as $profitCenterId){
					$pros = 0 ;
					$capas = 0 ;
					$absens = 0;
					$capacityTheoretical = 0;
					$employeeCalFullAbsence = array();
					
					if(!empty($empOfPc[$profitCenterId]))
					{
						
						foreach( $empOfPc[$profitCenterId] as $key => $val )
						{
						
							if(empty($totalConsumedPc[$profitCenterId][$_startDate])){
								$totalConsumedPc[$profitCenterId][$_startDate] = 0;
							}
							$totalConsumedPc[$profitCenterId][$_startDate] += (isset($totalConsumedEmployee[$key][$_startDate]) ? $totalConsumedEmployee[$key][$_startDate] : 0);
                            if( !empty($mrCapacity[$key]) ){
                                $absens = 0;
                                foreach($mrCapacity[$key] as $time => $value){
                                    if( $_startDate <= $time && $time <= $_endDateTmp  ){
                                        $capas += $value;
                                    }
                                }
                            } else {
    							$TEMP = 0;
								
								
    							if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
    							{
									
                                    $TEMP = 1;
    								$pros ++;
    								//CHECK WEEK
    								if($dateType == 'week')
    								{
    									if($_endDateTmp > $val['end_date'])
    									{
    										$_endDateTmpCal = $val['end_date'];
    									}
    									else
    									{
    										$_endDateTmpCal = $_endDateTmp;
    									}
    									$capas += $this->getWorkingDays($_startDate, $_endDateTmpCal, true);
    									$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($key, $_startDate, $_endDateTmpCal, 'validated', $dateType);
    									$absens += isset($absenTmp[$key][$keyAbsence]) ? $absenTmp[$key][$keyAbsence] : 0;
    								}
    								else{
    									if(date('m-Y',$val['end_date']) == date('m-Y',$_startDate))
    									{
    										//end_date between of month,
    										//WORKING DAY = DAY OF MONTH - DAY OFF - HOLIDAY : changed 2015/03/28
    										$capas += $this->getWorkingDays($_startDate, $val['end_date'], true);
    										$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($key, $_startDate, $val['end_date']);
    										$absens += isset($absenTmp[$key][$keyAbsence]) ? $absenTmp[$key][$keyAbsence] : 0;
    									}
    									else
    									{
    										$capas += $workingDay;
    										$employeeCalFullAbsence[] = $key;
    									}
    								}
    							}
    							else
    							{
    								if(date('m-Y',$val['start_date']) == date('m-Y',$_startDate))
    								{
    									$TEMP = 1;
    									//start_date between of month
    									$pros ++;
    									//WORKING DAY = DAY OF MONTH - DAY OFF - HOLIDAY : changed 2015/03/28
    									$capas += $this->getWorkingDays($val['start_date'], $_endDateTmp, true);
    									$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($key, $val['start_date'], $_endDateTmp);
    									$absens += isset($absenTmp[$key][$keyAbsence]) ? $absenTmp[$key][$keyAbsence] : 0;
    								}
    							}
    							if($TEMP == 1)
    							$capacityTheoretical += $val['capacity_by_year']*$theoreticalCapacity; //0.0833 is default value
                            }
						}
					}
					
					$absenTmp = $AbsenceRequest->sumAbsenceByDateForPC($employeeCalFullAbsence, $_startDate, $_endDateTmp, 'validated', $dateType);
					$absens += !empty($absenTmp[$keyAbsence]) ? $absenTmp[$keyAbsence] : 0;
					//$absens = !empty($absences[$profitCenterId][$keyAbsence]) ? $absences[$profitCenterId][$keyAbsence] : 0;
					$results[$profitCenterId][$_startDate]=array();
					if($capas < 0 || $capas == 0){
						$results[$profitCenterId][$_startDate]['capacity'] = 0;
						$results[$profitCenterId][$_startDate]['totalEmployee'] =0;
					} else {
						$results[$profitCenterId][$_startDate]['capacity'] = $capas - $absens;
						$results[$profitCenterId][$_startDate]['totalEmployee'] = $pros;
					}
					//$results[$profitCenterId][$_startDate]['working'] = $capas;
					$results[$profitCenterId][$_startDate]['working'] = $workingDay;
					$results[$profitCenterId][$_startDate]['absence'] = $absens;
					$results[$profitCenterId][$_startDate]['capacity_theoretical'] = round($capacityTheoretical,2);
					$results[$profitCenterId][$_startDate]['notValidated'] = $results[$profitCenterId][$_startDate]['capacity'];
					if(isset($totalConsumedPc[$profitCenterId][$_startDate]) && $dateType == 'month')
					{
						$results[$profitCenterId][$_startDate]['notValidated'] -= $totalConsumedPc[$profitCenterId][$_startDate];
					}
					elseif(isset($totalConsumed[$profitCenterId][$keyConsumed]) && $dateType == 'week')
					{
						$results[$profitCenterId][$_startDate]['notValidated'] -= $totalConsumed[$profitCenterId][$keyConsumed];
					}
				}
				
				//$_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate));
				if($dateType == 'week')
				{
					$_startDate = isset($listDays[$i]) ? $listDays[$i] : $_endDate+1;
					$i++;
				}
				else
				{
					$_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate));
				}
			}
		}
		if($showMonthMultiple)
		{
			$results = $this->groupDataCapacityFromDayToMonth($results,$listMonthPeriod);
		}
        return array(array(), $results);
    }
	function getMaxValue($a,$b)
	{
		if($a > $b)
		return $a;
		else
		return $b;
	}
	function groupDataCapacityFromDayToMonth($results,$listMonthPeriod)
	{
		$_capacities = $results;
		$results = array();
		foreach($_capacities as $key => $_capacity)
		{
			foreach($listMonthPeriod as $time => $val)
			{
				foreach($_capacity as $_time => $_val)
				{
					if($val['start'] <= $_time &&  $_time <= $val['end'])
					{
						if(!isset($results[$key][$time]))
						{
							$results[$key][$time] = array(
								'capacity' => 0 ,
								'totalEmployee' => 0,
								'working' => 0,
								'absence' => 0,
								'capacity_theoretical'=> 0,
								'notValidated' => 0
							);
						}
						$results[$key][$time]['capacity'] += $_val['capacity'];
						$results[$key][$time]['totalEmployee'] = $this->getMaxValue($results[$key][$time]['totalEmployee'], isset($_val['totalEmployee']) ? $_val['totalEmployee'] : 0);
						$results[$key][$time]['working'] += $_val['working'];
						$results[$key][$time]['absence'] += $_val['absence'];
						$capacity_theoretical = 0 ;
						if(date('m-Y',$time) == date('m-Y',$_time))
						{
							$capacity_theoretical = $_val['capacity_theoretical'];
						}
						$results[$key][$time]['capacity_theoretical'] = $capacity_theoretical;
						$results[$key][$time]['notValidated'] += $_val['notValidated'];
						unset($_capacities[$key][$_time]); //data da duoc gan vao thang thi remove no di => tang performance.
					}
				}
			}
		}
		return $results;
	}
    public function capacityFromForecast($profitCenterIds = array(), $startDate, $endDate, $getTotal = true, $applyForProject = false, $dateType = 'month', $listDays = null){
		// debug(date('h:m:s', time()));
			// exit;
        if($dateType == 'month')
		{
			$startDate = mktime(0, 0, 0, date("m", $startDate)-1, date("d", $startDate), date("Y", $startDate));
			$endDate = mktime(0, 0, 0, date("m", $endDate)+1, date("d", $endDate), date("Y", $endDate));
		}
        $listEmployees = array();
		$employeeName = $this->_getEmpoyee();
		$dayEstablished = $employeeName['day_established'];
		// debug(date('h:m:s', time()));
        list($a,$results) = $this->capacityFromForecastPC($profitCenterIds, $startDate, $endDate, $applyForProject, $dateType, $listDays);
		// debug(date('h:m:s', time()));
		// exit;
		$totalWorkloads = $capacityAllEmployees = $capacityOfMonth = $totalAllEmployees = array();
		if($getTotal)
		{
			$TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
			/**
			 * Lay du lieu staffing cho profit center
			 */
			$getDatas = $TmpStaffingSystem->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'model' => 'profit_center',
					'model_id' => $profitCenterIds,
					'date BETWEEN ? AND ?' => array($startDate, $endDate),
					'company_id' => $employeeName['company_id'],
                    //'NOT' => array('activity_id' => 0)
				)
			));
            //$actiIds = array_unique(Set::classicExtract($getDatas, '{n}.TmpStaffingSystem.activity_id'));
            //debug($actiIds); exit;
			$totalWorkloads = array();
			if(!empty($getDatas)){
				foreach($getDatas as $getData){
					$dx = $getData['TmpStaffingSystem'];
					if(!isset($totalWorkloads[$dx['model_id']][$dx['date']])){
						$totalWorkloads[$dx['model_id']][$dx['date']] = 0;
					}
					$totalWorkloads[$dx['model_id']][$dx['date']] += $dx['estimated'];
				}
			}
			/**
			 * Tinh tong tat ca nhung employee cua he thong tru employee thuoc profit center DEFAULT
			 * Tinh capacity cua tat ca nhung employee tren.
			 */
			$Employee = ClassRegistry::init('Employee');
			//DISABLE FUNCTION HIDDEN PC DEFAULT
			// $defaults = $this->ProfitCenter->find('first', array(
				// 'recursive' => -1,
				// 'conditions' => array(
					// 'company_id' => $employeeName['company_id'],
					// 'name' => 'default'
				// ),
				// 'fields' => array('id')
			// ));
			// $defaults = !empty($defaults) ? $defaults['ProfitCenter']['id'] : 0;
			$_joins = array(
				array(
					'table' => 'employees',
					'alias' => 'Employee',
					'type' => 'LEFT',
					'foreignKey' => 'employee_id',
					'conditions'=> array(
						'CompanyEmployeeReference.employee_id = Employee.id'
					)
				),
                array(
                    'table' => 'project_employee_profit_function_refers',
                    'alias' => 'PCRef',
                    'type' => 'INNER',
                    'conditions'=> array(
                        'PCRef.employee_id = Employee.id',
                        'PCRef.profit_center_id' => $profitCenterIds
                    )
                )
			);
			$CompanyEmployeeReference = ClassRegistry::init('CompanyEmployeeReference');
			$allEmp = $CompanyEmployeeReference->find('all', array(
					'recursive' => -1,
					'joins' => $_joins,
					'conditions' => array(
						'NOT' => $this->notConditions($startDate, $endDate),
						'CompanyEmployeeReference.company_id'=>$employeeName['company_id'],
					),
					'fields' => array('DISTINCT employee_id','Employee.start_date','Employee.end_date')
			));
            $allEmployee = array();
			foreach($allEmp as $_index=>$_data)
			{
				$_data = array_merge($_data['CompanyEmployeeReference'],$_data['Employee']);
				if(strtotime($_data['start_date']) < $dayEstablished)
				{
					$_data['start_date'] = $dayEstablished;
				}
				$startTmp = strtotime($_data['start_date']);
				$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
				$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
				$allEmployee[$_data['employee_id']] = $_data;
                $listEmployees[] = $_data['employee_id'];
			}
			// debug(date('h:m:s', time()));
			list($_capacityAllEmployees,$employeeOfMonth) = $this->capacityForEmployee(array_keys($allEmployee), $startDate, $endDate, $allEmployee, null, $dateType, $listDays);
			// debug(date('h:m:s', time()));
			// exit;
			$capacityOfMonth = isset($employeeOfMonth['capacityOfMonth']) ? $employeeOfMonth['capacityOfMonth'] : array();
			$totalAllEmployees = isset($employeeOfMonth['totalEmployee']) ? $employeeOfMonth['totalEmployee'] : array();
			//debug($totalAllEmployees);
			if(!empty($_capacityAllEmployees)){
				$i=0;
                $mrCapacity = array();
                $EmployeeMultiResource = ClassRegistry::init('EmployeeMultiResource');
                $listMR = $EmployeeMultiResource->isMultipleResources($listEmployees);
                $dt = new DateTime();
                $dt->setTimestamp($endDate);
                $dt->modify('last day of next month');
                $_end = $dt->getTimestamp();
                $mrCapacity = $EmployeeMultiResource->getCapacity($listMR, $startDate, $_end);
				foreach($_capacityAllEmployees as $eid => $_capacityAllEmployee){
					$i++;
					foreach($_capacityAllEmployee as $times => $values){
						if(!isset($capacityAllEmployees[$times])){
							$capacityAllEmployees[$times]['capacity'] = 0;
							$capacityAllEmployees[$times]['working'] = 0;
							$capacityAllEmployees[$times]['absence'] = 0;
							$capacityAllEmployees[$times]['totalEmployee'] = 0;
							$capacityAllEmployees[$times]['notValidated'] = 0;
						}
                        //multiple Resource
                        if( isset($listMR[$eid]) ){
                            $values['capacity'] = isset($mrCapacity[$eid][$times]) ? $mrCapacity[$eid][$times] : 0;
                        }
						$capacityAllEmployees[$times]['capacity'] += $values['capacity'];
						$capacityAllEmployees[$times]['working'] += $values['working'];
						$capacityAllEmployees[$times]['absence'] += $values['absence'];
						$capacityAllEmployees[$times]['notValidated'] += $values['notValidated'];
						if($i==1)
						$capacityAllEmployees[$times]['totalEmployee'] += isset($employeeOfMonth['totalEmployee'][$times]) ? $employeeOfMonth['totalEmployee'][$times] : 0;
						//$capacityAllEmployees[$times]['capacity_theoretical'] += $values['capacity_theoretical'];
					}
				}
			}
		}
		// debug(date('h:m:s', time()));
			// exit;
        return array(array(), $results, $totalWorkloads, $capacityAllEmployees, $totalAllEmployees, $capacityOfMonth, $listEmployees);
    }
    public function getPeriod($company, $startDateFilter, $endDateFilter){
		
        $Period = ClassRegistry::init('Period');
        $listMonth = $Period->find('all',array(
            'recursive' => -1,
            'conditions' => array(
                'time BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                'company_id' => $company
            ),
            'order' => array('time ASC')
        ));
        $first = $listMonth[0]['Period']['time'];
        $listMonthPeriod = Set::combine($listMonth,'{n}.Period.time','{n}.Period');
        $SESSION['0'] = $listMonthPeriod ;
        $arr = $listMonthPeriod[$first]['start'];
        $arr = date('m-Y',$arr);
        $_startDateFilter = strtotime('01-'.$arr);
        $SESSION['1'] = $_startDateFilter ;
        $endMonthTmp = date('m',$endDateFilter);
        $endYearTmp = date('Y',$endDateFilter);
        $endDayTmp = cal_days_in_month(CAL_GREGORIAN, $endMonthTmp, $endYearTmp);
        $_endDateFilter = strtotime($endDayTmp.'-'.$endMonthTmp.'-'.$endYearTmp);
        $SESSION['2'] = $_endDateFilter ;
        return $SESSION;
    }
    public function capacityForEmployee($employeeIds = array(), $startDate, $endDate, $allEmployee = null, $opportunityNotApplyStaffing = null, $dateType = 'month', $listDays = null){
		$session = new SessionComponent();
		$companyConfigs = $session->read('companyConfigs');
        $employeeName = $this->_getEmpoyee();
		$showMonthMultiple = false;
		if($dateType == 'month')
		{
			$startDate = mktime(0, 0, 0, date("m", $startDate)-1, date("d", $startDate), date("Y", $startDate));
			$endDate = mktime(0, 0, 0, date("m", $endDate)+1, date("d", $endDate), date("Y", $endDate));

			$listMonthPeriod = array();
            /**
             * Hide multiple month
             */
			//if(isset($companyConfigs['staffing_by_month_multiple']) && $companyConfigs['staffing_by_month_multiple'] == 1)
//			{
//				$showMonthMultiple = true ;
//				$dateType = 'day';
//				list($listMonthPeriod,$startDate,$endDate) = $this->getPeriod($employeeName['company_id'], $startDate, $endDate);
//				$listDays = $this->Behaviors->Lib->getListWorkingDays($startDate,$endDate,true);
//			}
		}
		$dayEstablished = $employeeName['day_established'];
		$AbsenceRequest = ClassRegistry::init('AbsenceRequest');
		$absences = $AbsenceRequest->sumAbsenceByEmployeeAndDate($employeeIds, $startDate, $endDate, 'validated', $dateType);
		// debug(date('h:m:s', time()));
		
		// exit;
        $results = array();
		//MODIFY BY VINGUYEN 2015-02-13 : apply start_date, end_date when select employee
		// debug(date('h:m:s', time()));
		$Employee = ClassRegistry::init('Employee');
		if($dateType == 'month')
		{
			$TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
		}
		else
		{
			$ActivityRequest = ClassRegistry::init('ActivityRequest');
		}
		if($allEmployee == null)
		{
			//get employees by start/end date
			$allEmp = $Employee->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'NOT' => $this->notConditions($startDate, $endDate),
					'Employee.id'=>$employeeIds,
				),
				'fields' => array('Employee.id','Employee.start_date','Employee.end_date','Employee.capacity_by_year')
			));
			
			$listEmployeeId = !empty($allEmp) ? Set::combine($allEmp, '{n}.Employee.id', '{n}.Employee.id') : array();
			$getConsumedEmployees = $TmpStaffingSystem->getConsumedByDateFromDataStaffingForEmployees($startDate, $endDate, $employeeName['company_id'], array(), $listEmployeeId);
			
			$dataSumConsumed = array();
			if(!empty($getConsumedEmployees)){
				foreach($getConsumedEmployees as $key => $values){
					$tmpValue = $values['TmpStaffingSystem'];
					if(empty($dataSumConsumed[$tmpValue['model_id']])){
						$dataSumConsumed[$tmpValue['model_id']] = array();
					}
					if(empty($dataSumConsumed[$tmpValue['model_id']][$tmpValue['date']])){
						$dataSumConsumed[$tmpValue['model_id']][$tmpValue['date']] = 0;
					}
					$dataSumConsumed[$tmpValue['model_id']][$tmpValue['date']] += $values[0]['consumed'];
					
				}
			}
			$dataEmployee = array();

			foreach($allEmp as $_index=>$_data)
			{
				$_data = $_data['Employee'];
				if(strtotime($_data['start_date']) < $dayEstablished)
				{
					$_data['start_date'] = $dayEstablished;
				}
				$startTmp = strtotime($_data['start_date']);
				//if start date of employee < day established of company set start date =  day established
				$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
				$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
				$_data['capacity_by_year'] = $_data['capacity_by_year'];
				$dataEmployee[$_data['id']] = $_data;

				if($dateType == 'month')
				{
					//get data consumed by month from table Staffing
					// $totalConsumed[$_data['id']] = $TmpStaffingSystem->getConsumedByDateFromDataStaffing($startDate, $endDate, $employeeName['company_id'], array(), $_data['id']);
					$totalConsumed[$_data['id']] = !empty($dataSumConsumed[$_data['id']]) ? $dataSumConsumed[$_data['id']] : array();
				}
				else
				{
					//get data consumed by day directly from table activity request
					$totalConsumed[$_data['id']] = $ActivityRequest->getConsumedByDateFromDataRequest($startDate, $endDate, $employeeName['company_id'], $_data['id'], $dateType );
				}
			}
		}
		else
		{
			$dataEmployee = $allEmployee;
			if($dateType == 'month'){
				$listEmployees =  array_keys($dataEmployee);
				$totalConsumedOptimize =  $TmpStaffingSystem->getConsumedByDateFromDataStaffingAllEmployees($startDate, $endDate, $employeeName['company_id'], array(), $listEmployees);
				foreach($totalConsumedOptimize as $key=>$value){
					$dx = $value['TmpStaffingSystem'];
					if(empty($totalConsumed[$dx['model_id']])){
						$totalConsumed[$dx['model_id']] = array();
					}if(empty($totalConsumed[$dx['model_id']][$dx['model_id']])){
						$totalConsumed[$dx['model_id']][$dx['date']] = 0;
					}
					$totalConsumed[$dx['model_id']][$dx['date']] += $value[0]['consumed'];
				}
			}else{
				foreach($dataEmployee as $_index=>$_data)
				{
					//get data consumed by day directly from table activity request
					$totalConsumed[$_index] = $ActivityRequest->getConsumedByDateFromDataRequest($startDate, $endDate, $employeeName['company_id'], $_index, $dateType );
				}
			}
		}
		
		// debug($totalConsumed);
		
		//END
        $_startDate = $startDate;
		$_endDate = $endDate;
		$listWeeks = array();
		if($dateType == 'day')
		{
			foreach($listDays as $time){
				$_startDate = $time;
				//GET CONFIG CAPACITY MONTH,
				/*-----------------------------------------
				(1) Get config capacity by month from screen: Administrator/Absence/Configuration capacity
				-----------------------------------------*/
				$getMonth = date('n',$_startDate);
				$keyCapacityByMonth = 'capacity_by_month_'.$getMonth;
				$theoreticalCapacity = isset($companyConfigs[$keyCapacityByMonth]) ? $companyConfigs[$keyCapacityByMonth]*0.01 : 0;
				//END (1)
				$keyAbsence = date('d_m_Y',$_startDate);
				$totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $_startDate), date("Y", $_startDate));
				$_endDateTmp=strtotime($totalDayOfMonth.'-'.date("m-Y", $_startDate));
				$_workingDay = $this->getWorkingDays($_startDate, $_endDateTmp, true);
				$employeeOfMonth['capacityOfMonth'][$_startDate] = $_workingDay;
				$employeeOfMonth['totalEmployee'][$_startDate] = 0;
				//END
				$workingDayCheck = $this->getWorkingDays($_startDate, $_startDate, true); //KHI NAO RANH, XAY DUNG FUNCTION CHECK 1 ngay co phai la working day k.
				foreach($employeeIds as $employeeId){
					$workingDay = $workingDayCheck;
					$absens = 0;
					/*--------------------------------------------------------------------------
					(1.1) Check start_date, end_date to calculate capacity for employee
					--------------------------------------------------------------------------*/
					$val = isset($dataEmployee[$employeeId]) ? $dataEmployee[$employeeId] : null;
					if($val)
					{
						if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
						{
							$employeeOfMonth['totalEmployee'][$_startDate] ++ ;
							$absens = !empty($absences[$employeeId][$keyAbsence]) ? $absences[$employeeId][$keyAbsence] : 0;
						}
						else
						{
							if(date('d-m-Y',$val['start_date']) == date('d-m-Y',$_startDate))
							{
								$employeeOfMonth['totalEmployee'][$_startDate] ++ ;
								$absens = !empty($absences[$employeeId][$keyAbsence]) ? $absences[$employeeId][$keyAbsence] : 0;
							}
							else
							{
								$workingDay = 0;
							}
						}
					}
					else
					{
						$workingDay = 0; //if employee not exits, assign working day = 0
					}
					//END (1.1)

					$results[$employeeId][$_startDate]=array();
					$results[$employeeId][$_startDate]['capacity'] = $workingDay - $absens ;
					$results[$employeeId][$_startDate]['working'] = $workingDay;
					$results[$employeeId][$_startDate]['absence'] = $absens;
					$results[$employeeId][$_startDate]['capacity_theoretical'] = isset($val['capacity_by_year']) ? $val['capacity_by_year'] * $theoreticalCapacity : 0;
					$results[$employeeId][$_startDate]['capacity_theoretical'] = round($results[$employeeId][$_startDate]['capacity_theoretical'],2);
					$results[$employeeId][$_startDate]['notValidated'] = $results[$employeeId][$_startDate]['capacity'];
					if(isset($totalConsumed[$employeeId][$_startDate]))
					{
						$results[$employeeId][$_startDate]['notValidated'] -= $totalConsumed[$employeeId][$_startDate];
					}
				}
			}
		}
		else
		{
			if($dateType == 'week')
			{
				$listDays = array_values($listDays);
			}
			$i=0;
			while($_startDate <= $_endDate){
				//debug(date('W d-m-Y',$_startDate));
				if($dateType == 'week')
				{
					$keyWeek = $this->Behaviors->Lib->getDateByWeekday($_startDate);
					$_startDate = $keyWeek;
					if(isset($listWeeks[$keyWeek]))
					{
						$_startDate = isset($listDays[$i]) ? $listDays[$i] : $_endDate+1;
						$i++;
						continue;
					}
					else
					{
						$listWeeks[$keyWeek] = $keyWeek;
						$_endDateTmpOfWeek = $this->Behaviors->Lib->getDateByWeekday($_startDate,'Saturday');
					}
				}
				//GET CONFIG CAPACITY MONTH
				/*-----------------------------------------
				(2) Get config capacity by month from screen: Administrator/Absence/Configuration capacity
				-----------------------------------------*/
				$getMonth = date('n',$_startDate);
				$keyCapacityByMonth = 'capacity_by_month_'.$getMonth;
				$theoreticalCapacity = isset($companyConfigs[$keyCapacityByMonth]) ? $companyConfigs[$keyCapacityByMonth]*0.01 : 0;
				//END (2)
				$keyConsumed  = '' ;
				if($dateType == 'week')
				{
					$keyAbsence = date('W_Y',$_startDate);
					$keyConsumed = date('W_m_Y',$_startDate);
				}
				else
				{
					$keyAbsence = date('d_m_Y',$_startDate);
				}
				$totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $_startDate), date("Y", $_startDate));
				//MODIFY BY VINGUYEN 06/08/2014
				$_endDateTmp= isset($_endDateTmpOfWeek) && $_endDateTmpOfWeek ? $_endDateTmpOfWeek : strtotime($totalDayOfMonth.'-'.date("m-Y", $_startDate));
				//WORKING DAY = DAY OF MONTH - DAY OFF - HOLIDAY : changed 2015/03/28
				$_workingDay = $this->getWorkingDays($_startDate, $_endDateTmp, true);
				$employeeOfMonth['capacityOfMonth'][$_startDate] = $_workingDay;
				$employeeOfMonth['totalEmployee'][$_startDate] = 0;
				//END
				foreach($employeeIds as $employeeId){
					$workingDay = 0;
					$absens = 0;
					//MODIFY BY VINGUYEN 2015-02-13 : apply start_date, end_date when select employee
					$val = isset($dataEmployee[$employeeId]) ? $dataEmployee[$employeeId] : null;
					if($val)
					{
						if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
						{
							$employeeOfMonth['totalEmployee'][$_startDate] ++ ;
							//CHECK WEEK
							if($dateType == 'week')
							{
								if($_endDateTmp > $val['end_date'])
								{
									$_endDateTmpCal = $val['end_date'];
								}
								else
								{
									$_endDateTmpCal = $_endDateTmp;
								}
								$workingDay = $this->getWorkingDays($_startDate, $_endDateTmpCal, true);
								$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($employeeId, $_startDate, $_endDateTmpCal, 'validated', $dateType);
								$absens = isset($absenTmp[$employeeId][$keyAbsence]) ? $absenTmp[$employeeId][$keyAbsence] : 0;
							}
							else
							{

								if(date('m-Y',$val['end_date']) == date('m-Y',$_startDate))
								{
									//end_date between of month,
									//WORKING DAY = DAY OF MONTH - DAY OFF - HOLIDAY : changed 2015/03/28
									$workingDay = $this->getWorkingDays($_startDate, $val['end_date'], true);
									$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($employeeId, $_startDate, $val['end_date'], 'validated', $dateType);
									$absens = isset($absenTmp[$employeeId][$keyAbsence]) ? $absenTmp[$employeeId][$keyAbsence] : 0;
								}
								else
								{
									$workingDay = $_workingDay;
									$absens = !empty($absences[$employeeId][$keyAbsence]) ? $absences[$employeeId][$keyAbsence] : 0;
								}
							}
						}
						else
						{
							if(date('m-Y',$val['start_date']) == date('m-Y',$_startDate))
							{
								$employeeOfMonth['totalEmployee'][$_startDate] ++ ;
								//start_date between of month
								$workingDay = $this->getWorkingDays($val['start_date'], $_endDateTmp, true);
								$absenTmp = $AbsenceRequest->sumAbsenceByEmployeeAndDate($employeeId, $val['start_date'], $_endDateTmp, 'validated', $dateType);
								$absens = isset($absenTmp[$employeeId][$keyAbsence]) ? $absenTmp[$employeeId][$keyAbsence] : 0;
							}
						}
					}
					//END
					$results[$employeeId][$_startDate]=array();
					$results[$employeeId][$_startDate]['capacity'] = $workingDay - $absens ;
					//$results[$employeeId][$_startDate]['working'] = $workingDay;
					$results[$employeeId][$_startDate]['working'] = $workingDay;
					$results[$employeeId][$_startDate]['absence'] = $absens;
					if($workingDay == 0)
					{
						$results[$employeeId][$_startDate]['capacity_theoretical'] = 0.00 ;
					}
					else
					{
						$results[$employeeId][$_startDate]['capacity_theoretical'] = isset($val['capacity_by_year']) ? $val['capacity_by_year'] * $theoreticalCapacity : 0;
					}
					$results[$employeeId][$_startDate]['capacity_theoretical'] = round($results[$employeeId][$_startDate]['capacity_theoretical'],2);
					$results[$employeeId][$_startDate]['notValidated'] = $results[$employeeId][$_startDate]['capacity'];
					if(isset($totalConsumed[$employeeId][$_startDate]) && $dateType == 'month')
					{
						$results[$employeeId][$_startDate]['notValidated'] -= $totalConsumed[$employeeId][$_startDate];
					}
					elseif(isset($totalConsumed[$employeeId][$keyConsumed]) && $dateType == 'week')
					{
						$results[$employeeId][$_startDate]['notValidated'] -= $totalConsumed[$employeeId][$keyConsumed];
					}
				}
				//echo date("m - Y", $_startDate); echo '<br />'; echo $totalDayOfMonth; echo '<br />'; echo $num; echo '<br />'; echo $absens; echo '<br />'; echo $_countHoliday; echo '<br />'; echo $_endDateTmp;echo '<br />';
				if($dateType == 'week')
				{
					$_startDate = isset($listDays[$i]) ? $listDays[$i] : $_endDate+1;
					$i++;
				}
				else
				{
					$_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate));
				}
			}
		}
		$totalWorkloads = array();
		if($allEmployee == null)
		{
			$TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
			/**
			 * Lay du lieu staffing cho employee
			 */
			 $conditions = array(
				'model' => 'employee',
				'model_id' => $employeeIds,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'company_id' => $employeeName['company_id'],
			);
			if($opportunityNotApplyStaffing)
			{
				$conditions = array_merge($conditions, array('NOT' => $opportunityNotApplyStaffing));
			}
			$getDatas = $TmpStaffingSystem->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions
			));
			$totalWorkloads = array();
			if(!empty($getDatas)){
				foreach($getDatas as $getData){
					$dx = $getData['TmpStaffingSystem'];
					if(!isset($totalWorkloads[$dx['model_id']][$dx['date']])){
						$totalWorkloads[$dx['model_id']][$dx['date']] = 0;
					}
					$totalWorkloads[$dx['model_id']][$dx['date']] += $dx['estimated'];
				}
			}
		}
		if($showMonthMultiple)
		{
			$results = $this->groupDataCapacityFromDayToMonth($results,$listMonthPeriod);
		}
		if($allEmployee == null)
		{
			return array($totalWorkloads, $results);
		}
		else
		{
			return array($results,$employeeOfMonth);
		}
		
    }
	function notConditions($startDate, $endDate)
	{
		$not = array(
			'OR' => array(
				array(
					'UNIX_TIMESTAMP(Employee.end_date) < '=> $startDate,
					'Employee.end_date <>' => '0000-00-00',
					'Employee.end_date IS NOT NULL'
				),
				array(
					'UNIX_TIMESTAMP(Employee.start_date) > '=> $endDate,
					'Employee.start_date <>' => '0000-00-00',
					'Employee.start_date IS NOT NULL'
				)
			)
		);
		return $not;
	}
	/*-------------------
	@Get lists consumed by day
	INPUT : Array : Activities
	OUTPUT: Array : Tasks not special-nct-parent (parent = level 1 + exists children task)
	-------------------*/
	function getActivityTaskNotSpecialAndNct($activities,$getEstimated = false)
	{
		$taskLists = $this->find('all',array(
				'recursive' => -1,
				'conditions' => array(
					'activity_id' => $activities,
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
		$parentIds = array_unique(Set::classicExtract($taskLists, '{n}.ActivityTask.parent_id'));
		foreach($taskLists as $key => $task){
			foreach($parentIds as $parentId){
				if($task['ActivityTask']['id'] == $parentId){
					//Do nothing
				}
				else{
					if($getEstimated )
					{
						$childrenIds[$task['ActivityTask']['id']] = array( 'id' => $task['ActivityTask']['id'], 'estimated' => $task['ActivityTask']['estimated'] );
					}
					else
					{
						$childrenIds[] = $task['ActivityTask']['id'];
					}
				}
			}
		}
		$taskLists = $childrenIds;
		return $taskLists;
	}
	/*-------------------
	@Get lists consumed by day
	INPUT : Array : Project
	OUTPUT: Array : Tasks not special-nct-parent (parent = level 1 + exists children task)
	-------------------*/
	function getProjectTaskNotSpecialAndNct($project)
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
					'id','parent_id'
				)
			)
		);
		$childrenIds = array();
		$parentIds = array_unique(Set::classicExtract($taskLists, '{n}.ProjectTask.parent_id'));
		foreach($taskLists as $key => $task){
			foreach($parentIds as $parentId){
				if($task['ProjectTask']['id'] == $parentId){
					//Do nothing
				}
				else{
					$childrenIds[] = $task['ProjectTask']['id'];
				}
			}
		}
		$taskLists = $childrenIds;
		return $taskLists;
	}
	/*-------------------
	@Get lists activity task-project task
	INPUT : Array : Activities
	OUTPUT: Array : Tasks not special-nct-parent (parent = level 1 + exists children task)
	-------------------*/
	function getActivityNctTask($activities)
	{
		$activityTasks = $this->find('list',array(
				'recursive' => -1,
				'conditions' => array(
					'activity_id' => $activities,
					'special' => 0,
					'is_nct' => 1
				),
				'fields'=>array(
					'id','id'
				)
			)
		);
		$Activity = ClassRegistry::init('Activity');
		$projects = $Activity->getProjectLinkedActivities($activities);
		$Project = ClassRegistry::init('ProjectTask');
		$projectTasks = $Project->find('list',array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $projects,
					'special' => 0,
					'is_nct' => 1
				),
				'fields'=>array(
					'id','id'
				)
			)
		);
		return array('projectTasks' => array_values($projectTasks), 'activityTasks' => array_values($activityTasks));
	}
}
?>
