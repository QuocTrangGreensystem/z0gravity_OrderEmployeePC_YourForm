<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityTaskEmployeeRefer extends AppModel {

    var $name = 'ActivityTaskEmployeeRefer';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
	var $actsAs = array('Lib');
    var $belongsTo = array(
        'ActivityTask' => array(
            'className' => 'ActivityTask',
            'foreignKey' => 'activity_task_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
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
     public function staffingSystem($activity_id = null){
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $ProjectTeam = ClassRegistry::init('ProjectTeam');
        $ProjectFunctionEmployeeRefer = ClassRegistry::init('ProjectFunctionEmployeeRefer');
        $ProjectEmployeeProfitFunctionRefer = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
        $ProjectEmployeeManager = ClassRegistry::init('ProjectEmployeeManager');
        $TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
        
        $workloadOfEmployees = $workloadOfProfitCenters = $consumedTasks = $consumedPreviousTasks = $consumedPreviousTaskEmployees = array();
        $totalConsumedTasks = $consumedTaskEmployees = array();
        if(!empty($activity_id)){
            /**
             * Lay danh sach cac profit center cua employee
             */
            $references = $ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id')
            ));
            /**
             * Lay Tat ca cac Task Thuoc Activity nay
             */
            $activityTasks = $ActivityTask->find('all', array(
                'conditions' => array('ActivityTask.activity_id' => $activity_id),
                'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload')
            ));
            /**
             * Lay consumed cua Previous Task(neu co).
             */
            $consumedPreviousTaskEmployees = $this->_consumedPreviousTask($activity_id);
            /**
             * 2 array can lay ra cuoi cung:
             * - Du lieu da tinh toan cho employee
             * - Du lieu da tinh toan cho profit center
             */
            $endDataEmployees = $endDataProfitCenters = array();
            
            if(!empty($activityTasks)){
                /**
                 * Lay danh sach tat ca Id cua Activity Task 
                 */
                
                $listActivityTaskIds = Set::classicExtract($activityTasks, '{n}.ActivityTask.id');
                /**
                 * List Lay total consumed cua cac Task va Lay consumed cua tung employee
                 */
                list($totalConsumedTasks, $consumedTaskEmployees) = $this->_consumedTask($listActivityTaskIds);
                /**
                 * Nhung Task nao la Parent thi xoa, khong quan tam den
                 */
                $parentIds = array_unique(Set::classicExtract($activityTasks, '{n}.ActivityTask.parent_id'));
                foreach($activityTasks as $key => $activityTask){
                    foreach($parentIds as $parentId){
                        if($activityTask['ActivityTask']['id'] == $parentId){
                            unset($activityTasks[$key]);
                        }
                    }
                }
                /**
                 * Phan chia workload cho tung employee va tung Profit Center theo tung Task 
                 */
                foreach($activityTasks as $activityTask){
                    $_taskId = $activityTask['ActivityTask']['id'];
                    $_endDate = !empty($activityTask['ActivityTask']['task_end_date']) ? $activityTask['ActivityTask']['task_end_date'] : time();
                    $_startDate = !empty($activityTask['ActivityTask']['task_start_date']) ? $activityTask['ActivityTask']['task_start_date'] : $_endDate;
                    
                    $_workload = $activityTask['ActivityTask']['estimated'];
                    $_overload = $activityTask['ActivityTask']['overload'];
                    if(!empty($activityTask['ActivityTaskEmployeeRefer'])){
                        $taskRefers = $activityTask['ActivityTaskEmployeeRefer'];
                        $gOverload = $this->Behaviors->Lib->caculateGlobal($_overload, count($taskRefers));
                        $overloads = $gOverload['original'];
                        $remainder = $gOverload['remainder'];
						$numberOfRemainder = $gOverload['number'];
                        end($taskRefers);
                        $endKey = key($taskRefers);
						$i=0;
                        foreach($taskRefers as $key => $taskRefer){
							//MODIFY BY VINGUYEN 18/10/2014 : APPLY FOR NEW FUNCTION CACULATE WITH ALGORITHM EXACTLY
                            $referId = $taskRefer['reference_id'];
                            if($taskRefer['is_profit_center'] == 0){
                                $workloadOfEmployees[$_taskId][$referId]['startDate'] = $_startDate;
                                $workloadOfEmployees[$_taskId][$referId]['endDate'] = $_endDate;
                                $workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads;
                                if($i < $numberOfRemainder){
                                    $workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads + $remainder;
                                }
                            } else {
                                $workloadOfProfitCenters[$_taskId][$referId]['startDate'] = $_startDate;
                                $workloadOfProfitCenters[$_taskId][$referId]['endDate'] = $_endDate;
                                $workloadOfProfitCenters[$_taskId][$referId]['workload'] = $taskRefer['estimated'];
                                if($i < $numberOfRemainder){
                                    $workloadOfProfitCenters[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads + $remainder;
                                }
                            }
							$i++;
                        }
                    } else {
                        $workloadOfEmployees[$_taskId][999999999]['startDate'] = $_startDate;
                        $workloadOfEmployees[$_taskId][999999999]['endDate'] = $_endDate;
                        $workloadOfEmployees[$_taskId][999999999]['workload'] = $_workload + $_overload;
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
                    foreach($dataEmployeesNotAffected as $values){
                        if(isset($values['startDate'])){
                            $_starts = $values['startDate'];
                        }
                        if(isset($values['endDate'])){
                            $_ends = $values['endDate'];
                        }
                        $dataEmployees[$taskId][999999999]['startDate'] = $_starts;
                        $dataEmployees[$taskId][999999999]['endDate'] = $_ends;
                        if(!isset($dataEmployees[$taskId][999999999]['workload'])){
                            $dataEmployees[$taskId][999999999]['workload'] = 0;
                        }
                        $dataEmployees[$taskId][999999999]['workload'] += $values['workload'];
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
                        //Chuyen consumed employee cho profit center
                        foreach($values as $time => $value){
                            $profitCenter = !empty($references[$employ]) ? $references[$employ] : 999999999;
                            if(!isset($totalConsumedTaskProfitCenters[$taskId][$profitCenter][$time])){
                                $totalConsumedTaskProfitCenters[$taskId][$profitCenter][$time] = 0;
                            }
                            $totalConsumedTaskProfitCenters[$taskId][$profitCenter][$time] += $value;
                        }
                    }
                }
            }
            $workloadEmployees = array();
            if(!empty($dataEmployees)){
                /**
                 * De quy tinh toan lai workload cho cac employee 
                 */
                $workloadEmployees = $this->_recursiveTask($dataEmployees, null, array());
            }
            /**
             * Phan chia workload, consumed task, previous task cua employee cho tung thang, tung nam
             */
            $endDataEmployees = $this->_mergeDataSystem($workloadEmployees, $consumedTaskEmployees, $consumedPreviousTaskEmployees, $totalConsumedTasks, 'employee', 0, $activity_id);
            /**
             * Gan lai du lieu duoc lay ra tu Project Task cho profit center
             */
            $dataProfitCenters = $workloadOfProfitCenters;
            $dataProfitCenterNotAffecteds = $workloadOfEmployees;
            $consumedTaskProfitCenters = $consumedTaskEmployees;
            $consumedPreviousTaskProfitCenters = $consumedPreviousTaskEmployees;
            /**
             * Tinh toan workload cho Profit center.
             * Nhung employee nao thuoc Profit center thi chuyen workload cho Profit center do.
             */
            if(!empty($dataProfitCenterNotAffecteds)){
                foreach($dataProfitCenterNotAffecteds as $taskId => $dataProfitCenterNotAffected){
                    foreach($dataProfitCenterNotAffected as $emp => $values){
                        if(isset($values['startDate'])){
                            $_starts = $values['startDate'];
                        }
                        if(isset($values['endDate'])){
                            $_ends = $values['endDate'];
                        }
                        $profitId = !empty($references[$emp]) ? $references[$emp] : 999999999;
                        
                        $dataProfitCenters[$taskId][$profitId]['startDate'] = $_starts;
                        $dataProfitCenters[$taskId][$profitId]['endDate'] = $_ends;
                        
                        if(!isset($dataProfitCenters[$taskId][$profitId]['workload'])){
                            $dataProfitCenters[$taskId][$profitId]['workload'] = 0;
                        }
                        $dataProfitCenters[$taskId][$profitId]['workload'] += $values['workload'];
                    }
                }
            }
            /**
             * Gan consumed vao data profit center
             */
            if(!empty($totalConsumedTaskProfitCenters)){
                foreach($totalConsumedTaskProfitCenters as $taskId => $totalConsumedTaskProfitCenter){
                    foreach($totalConsumedTaskProfitCenter as $profit => $values){
                        $_values = !empty($values) ? array_sum($values) : 0;
                        $dataProfitCenters[$taskId][$profit]['consumed'] = $_values;
                    }
                }
            }
            /**
             * Chuyen consumed, consumed previous task cua employee qua profit center.
             */
            $_consumedTaskProfitCenters = array();
            if(!empty($consumedTaskProfitCenters)){
                foreach($consumedTaskProfitCenters as $emp => $consumedTaskProfitCenter){
                    foreach($consumedTaskProfitCenter as $time => $values){
                        $profitId = !empty($references[$emp]) ? $references[$emp] : 999999999;
                        if(!isset($_consumedTaskProfitCenters[$profitId][$time]['consumed'])){
                            $_consumedTaskProfitCenters[$profitId][$time]['consumed'] = 0;
                        }
                        $_consumedTaskProfitCenters[$profitId][$time]['consumed'] += $values['consumed'];
                    }
                }
            }
            $_consumedPreviousTaskProfitCenters = array();
            if(!empty($consumedPreviousTaskProfitCenters)){
                foreach($consumedPreviousTaskProfitCenters as $emp => $consumedPreviousTaskProfitCenter){
                    foreach($consumedPreviousTaskProfitCenter as $time => $values){
                        $profitId = !empty($references[$emp]) ? $references[$emp] : 999999999;
                        if(!isset($_consumedPreviousTaskProfitCenters[$profitId][$time]['consumed'])){
                            $_consumedPreviousTaskProfitCenters[$profitId][$time]['consumed'] = 0;
                        }
                        $_consumedPreviousTaskProfitCenters[$profitId][$time]['consumed'] += $values['consumed'];
                    }
                }
            }
            $workloadProfitCenters = array();   
            if(!empty($dataProfitCenters)){
                /**
                 * De quy tinh toan lai workload cho cac profti center 
                 */
                $workloadProfitCenters = $this->_recursiveTask($dataProfitCenters, null, array());
            }
            /**
             * Phan chia workload, consumed task, previous task cua profit center cho tung thang, tung nam
             */
            $endDataProfitCenters = $this->_mergeDataSystem($workloadProfitCenters, $_consumedTaskProfitCenters, $_consumedPreviousTaskProfitCenters, $totalConsumedTaskProfitCenters, 'profit_center', 0, $activity_id);
            /**
             * Neu co du lieu cuoi cung cua  employee, profit center va skill thi
             * Luu tat ca vao table tmp_staffing_systems
             */
            $TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $activity_id), false);
            if(!empty($endDataEmployees)){
                $TmpStaffingSystem->saveAll($endDataEmployees);
            }
            if(!empty($endDataProfitCenters)){
                $TmpStaffingSystem->saveAll($endDataProfitCenters);
            }
        }
     }
     
     /**
     * Caculate month
     * 
     * 
     * @return 
     * @access private
     */
     
    private function _diffDate($start = null, $end = null){
        $datas = array();
        if(!empty($start) && !empty($start)){
            $minMonth = date('m', $start);
            $maxMonth = date('m', $end);
            $minYear = date('Y', $start); 
            $maxYear = date('Y', $end);
            while ($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                $datas[] = $minMonth;
                $minMonth++;
                if ($minMonth == 13) {
                    $minMonth = 1;
                    $minYear++;
                }
            }
        }
        $datas = count($datas);
        
        return $datas;
    }
     
     /**
      * Tinh workload, consumed va previous task cua tung employee theo tung thang 
      * 
      * @access private
      * @return array()
      * @author HuuPC
      */
     private function _mergeDataSystem($workloads = array(), $consumeds = array(), $previous = array(), $totalConsumedTasks = array(), $model = null, $project_id = null, $activity_id = null){
        $datas = array();
        if(!empty($workloads)){
            $dataFirsts = $dataSeconds = array();
            foreach($workloads as $taskId => $workload){
                foreach($workload as $id => $values){
                    $workloadSeconds = array();
                    if(!empty($totalConsumedTasks[$taskId][$id])){
                        $values['workload'] = number_format($values['workload'], 2) - number_format(array_sum($totalConsumedTasks[$taskId][$id]), 2);
                        foreach($totalConsumedTasks[$taskId][$id] as $time => $val){
                            $workloadSeconds[$id][$time]['estimated'] = $val;
                        }
                    }
                    $_end = !empty($values['endDate']) ? $values['endDate'] : time();
                    $_start = !empty($values['startDate']) ? $values['startDate'] : $_end;
                    if($_start > $_end){
                        $_end = $_start;
                    }
                    $minMonth = !empty($_start) ? date('m', $_start) : '';
                    $minYear = !empty($_start) ? date('Y', $_start) : ''; 
                    $maxMonth = !empty($_end) ? date('m', $_end) : '';
                    $maxYear = !empty($_end) ? date('Y', $_end) : '';
                    
                    $diffDate = $this->_diffDate($_start, $_end);
                    if($diffDate == 0){
                        $estis = $values['workload'];
                    } else {
                        $gEstis = $this->Behaviors->Lib->caculateGlobal($values['workload'], $diffDate);
                        $estis = $gEstis['original'];
                        $remainder = $gEstis['remainder'];
						$numberOfRemainder = $gEstis['number'];						
                    }
                    $workloadFirsts = array();
					$i=0;
                    while($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
						//MODIFY BY VINGUYEN 18/10/2014 : APPLY FOR NEW FUNCTION CACULATE WITH ALGORITHM EXACTLY
						$_DATE=strtotime('01-'. $minMonth .'-'. $minYear);
                        $workloadFirsts[$id][$_DATE]['estimated'] = $estis;
                        if($i < $numberOfRemainder){
                            $workloadFirsts[$id][$_DATE]['estimated'] = $estis + $remainder;
                        }
                        $minMonth++;
                        if ($minMonth == 13) {
                            $minMonth = 1;
                            $minYear++;
                        }
						$i++;
						//END
                    }
                    $dataSeconds[] = $workloadSeconds;
                    $dataFirsts[] = $workloadFirsts;
                }
            }
            $dataFirsts = array_merge($dataFirsts, $dataSeconds);
            if(!empty($dataFirsts)){
                foreach($dataFirsts as $dataFirst){
                    foreach($dataFirst as $emp => $values){
                        foreach($values as $time => $value){
                            if(!isset($datas[$emp][$time]['estimated'])){
                                $datas[$emp][$time]['estimated'] = 0;
                            }
                            $datas[$emp][$time]['estimated'] += $value['estimated'];
                        }
                    }
                }
                if(!empty($consumeds)){
                    foreach($consumeds as $emp => $consumed){
                        foreach($consumed as $time => $values){
                            $datas[$emp][$time]['consumed'] = $values['consumed'];
                        }
                    }
                }
            }
        }
        if(!empty($previous)){
            foreach($previous as $emp => $previou){
                foreach($previou as $time => $values){
                    if(!isset($datas[$emp][$time]['estimated'])){
                        $datas[$emp][$time]['estimated'] = $values['consumed'];
                    } else {
                        $datas[$emp][$time]['estimated'] += $values['consumed'];
                    }
                    
                    
                    if(!isset($datas[$emp][$time]['consumed'])){
                        $datas[$emp][$time]['consumed'] = $values['consumed'];
                    } else {
                        $datas[$emp][$time]['consumed'] += $values['consumed'];
                    }
                    
                }
            }
        }
        $employeeName = CakeSession::read('Auth.employee_info.Company.id');
        if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = array();
            foreach($tmpDatas as $id => $tmpData){
                foreach($tmpData as $time => $values){
                    $_datas = array(
                        'project_id' => $project_id,
                        'activity_id' => $activity_id,
                        'model' => $model,
                        'model_id' => $id,
                        'date' => $time,
                        'estimated' => !empty($values['estimated']) ? $values['estimated'] : 0,
                        'consumed' => !empty($values['consumed']) ? $values['consumed'] : 0,
                        'company_id' => $employeeName
                    );
                    $datas[] = $_datas;
                }
            }
        }
        return $datas;
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
     private function _consumedTask($listActivityTaskIds = array()){
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
     public function _consumedPreviousTask($activityId = null){
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
                $value = $dx['value'];
                if(!isset($previousTaskEmployees[$employ][$date]['consumed'])){
                    $previousTaskEmployees[$employ][$date]['consumed'] = 0;
                }
                $previousTaskEmployees[$employ][$date]['consumed'] += $value;
            }
        }
        return $previousTaskEmployees;
     }
     
     /**
      * Ham de quy Task.
      * Example:
      * Ten Task---Assign To---------------------------Start-------End---------Worload-----Overload-----Consumed-----Remain
      * Task 1     EMPLOYEE 1, EMPLOYEE 2, EMPLOYEE3   1/10/2013   30/12/2013   48         0            30           18
      *
      * Hien tai la thang 10
      * 
      * Theo Ke Hoach:
      * Ten Employee --------- Thang 10 ----------Thang 11 ------- Thang 12--------Total
      *
      * -------------Workload-    10                 10               10             30
      * -EMPLOYEE 1--Consumed-     0                  0                0              0
      * -------------Remain---    10                 10               10             30
      * 
      * -------------Workload-     5                  5                5             15
      * -EMPLOYEE 2--Consumed-     0                  0                0              0
      * -------------Remain---     5                  5                5             15
      * 
      * -------------Workload-     1                  1                1              3
      * -EMPLOYEE 3--Consumed-     0                  0                0              0
      * -------------Remain---     1                  1                1              3
      * 
      * -> EMPLOYEE 1 co tong workload = 30.
      * -> EMPLOYEE 2 co tong workload = 15.
      * -> EMPLOYEE 3 co tong workload =  3.
      * --------------------------------------
      * -> Total:                        48.
      * 
      * Theo Thuc Te:
      * Ten Employee --------- Thang 10 ----------Thang 11 ------- Thang 12--------Total
      *
      * -------------Workload-    15                6.99             7.01            29
      * -EMPLOYEE 1--Consumed-    15                0                0               15
      * -------------Remain---     0                6.99             7.01            14
      * 
      * -------------Workload-    10                1.99             2.01            14
      * -EMPLOYEE 2--Consumed-    10                0                0               10
      * -------------Remain---     0                1.99             2.01             4
      * 
      * -------------Workload-     5                  0                0              5
      * -EMPLOYEE 3--Consumed-     5                  0                0              0
      * -------------Remain---     0                  0                0              5
      * 
      * -> EMPLOYEE 1 co tong workload = 29.    =>>>>>> Giam 1
      * -> EMPLOYEE 2 co tong workload = 14.    =>>>>>> Giam 1
      * -> EMPLOYEE 3 co tong workload =  5.    =>>>>>> Tang 2
      * --------------------------------------
      * -> Total:                        48.
      * 
      * Dau vao:
      * - 1 Array tat ca cac task co trong 1 project/activity va da them consumed cua cac employee/pc thuoc task do
      * Dau ra: 1 mang gia tri gom:
      * - Tong workload cua employee/profit center/ skill(function)
      * - Ngay bat dau
      * - Ngay ket thuc
      * 
      * 
      * @access private
      * @return array()
      * @author HuuPC
      */
      private function _recursiveTask($tasks = array(), $valueAdds = null, $tmpTasks = array()){
         if(empty($tmpTasks)){
            $tmpTasks = $tasks;
         }
         $v1 = $v2 = false;
         $_v1 = $_v2 = array();
         foreach($tasks as $taskId => $task){
            end($task);
            $lastId = key($task);
            $countEmploy = count($task);
            $calculs = array();
            if(!empty($valueAdds[$taskId])){
                if($valueAdds[$taskId] != 0 || $valueAdds[$taskId] != ''){
                    $_calculs = $this->Behaviors->Lib->caculateGlobal($valueAdds[$taskId], $countEmploy, false);
                    $calculs[$taskId] = $_calculs;
                    $valueAdds[$taskId] = 0;
                }
            } else {
                $valueAdds[$taskId] = 0;
            }
            foreach($task as $emp => $values){
                if(empty($values['consumed'])){
                    $values['consumed'] = 0;
                }
                if(empty($values['workload'])){
                    $values['workload'] = 0;
                }
                if(!empty($calculs[$taskId])){
                    $totalCalculs[$taskId] = round($calculs[$taskId]['original'] + $calculs[$taskId]['remainder'], 2);
                    if($totalCalculs[$taskId] != 0){
                        if($values['workload'] - $totalCalculs[$taskId] < 0){
                            if($totalCalculs[$taskId] < 0){
                                if($emp == $lastId){
                                    $valueAdds[$taskId] += round(($values['workload'] + $totalCalculs[$taskId]), 2);
                                } else {
                                    $valueAdds[$taskId] += round(($values['workload'] + $calculs[$taskId]['original']), 2);
                                }
                            } else {
                                if($emp == $lastId){
                                    $valueAdds[$taskId] += round(($values['workload'] - $totalCalculs[$taskId]), 2);
                                } else {
                                    $valueAdds[$taskId] += round(($values['workload'] - $calculs[$taskId]['original']), 2);
                                }
                            }
                            $values['workload'] = 0;
                            $tmpTasks[$taskId][$emp] = $values;
                            unset($tasks[$taskId][$emp]);
                            $_v1[] = true;
                        } else {
                            if($totalCalculs[$taskId] < 0){
                                if($emp == $lastId){
                                    $values['workload'] = round(($values['workload'] + $totalCalculs[$taskId]), 2);
                                } else {
                                    $values['workload'] = round(($values['workload'] + $calculs[$taskId]['original']), 2);
                                }
                            } else {
                                if($emp == $lastId){
                                    $values['workload'] = round(($values['workload'] - $totalCalculs[$taskId]), 2);
                                } else {
                                    $values['workload'] = round(($values['workload'] - $calculs[$taskId]['original']), 2);
                                }
                            }
                            $tasks[$taskId][$emp] = $values;
                            $tmpTasks[$taskId][$emp] = $values;
                            $_v1[] = false;
                        }
                    }
                }
                if($values['consumed'] == $values['workload']){
                    $tmpTasks[$taskId][$emp] = $values;
                    unset($tasks[$taskId][$emp]);
                }
                if($values['consumed'] > $values['workload']){
                    $_tmpCals = round(($values['consumed'] - $values['workload']), 2);
                    $valueAdds[$taskId] += round(($values['workload'] - $values['consumed']), 2);
                    $values['workload'] = $values['consumed'];
                    $tmpTasks[$taskId][$emp] = $values;
                    unset($tasks[$taskId][$emp]);
                    $_v2[] = true;
                } else {
                    $_v2[] = false;
                }
            }
         }
         $v1 = (in_array(1, $_v1)) ? true : false;
         $v2 = (in_array(1, $_v2)) ? true : false;
         if($v1||$v2){
            $tmpTasks = $this->_recursiveTask($tasks, $valueAdds, $tmpTasks);
         }
         return $tmpTasks;
      }
      
      /**
       * Ham tinh toan lai remain
       * Neu nhung thang la qua khu thi remain = 0
       * Remain nhung thang qua khu se duoc chia dieu cho thang hien tai va cac thang trong tuong lai
       * Ham cui bap - co thoi gian se viet lai
       */
      private function _resetRemainSystem($datas = array()){
        $currentDate = strtotime(date('01-m-Y', time()));
        $totalEstimated = $_total = $monthValidates = array();   
        foreach($datas as $id => $data){
            $works = $cons = $remains = 0; 
            foreach($data as $time => $value){
                if($time < $currentDate){ 
                    $works += $value['validated'];
                    $cons += $value['consumed'];
                    $remains += $value['remains'];
                    $_total[$id]['validated'] = $works;
                    $_total[$id]['consumed'] = $cons;
                    $_total[$id]['remain'] = $remains;
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    if(!empty($value['validated'])){
                        $monthValidates[$id][] = $time;
                    }
                }
            }
        }
        if(!empty($monthValidates)){
            foreach($monthValidates as $id => $monthValidate){
                if(in_array($currentDate, $monthValidate)){
                    //do nothing
                } else {
                    $monthValidates[$id][] = $currentDate;
                }
            }
        }
        foreach($datas as $id => $data){
            foreach($data as $time => $value){
                if($time < $currentDate){
                    $datas[$id][$time]['remains'] = 0;
                } else { 
                    $count = !empty($monthValidates[$id]) ? count($monthValidates[$id]) : 1;
                    $remain = !empty($_total[$id]) ? $_total[$id]['remain'] : 0;
                    if($count == 0){
                        $remainFirst = 0;
                        $remainSecond = 0;
                    } else {
                        $getRemain = $this->Behaviors->Lib->caculateGlobal($remain, $count, false);
                        $remainFirst = $getRemain['original'];
                        $remainSecond = $getRemain['remainder'];
                    }
                    if(!empty($value['validated']) || $time == $currentDate){
                        $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst;
                        if(!empty($monthValidates[$id]) && $time == max($monthValidates[$id])){
                            $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst +$remainSecond ;
                        }
                    } else {
                        if(!empty($datas[$id][$currentDate])){
                            if($datas[$id][$currentDate]['validated'] && $datas[$id][$currentDate]['validated'] == 0){
                                $datas[$id][$currentDate]['remains'] = $remainFirst + $remainSecond;
                            }
                        }
                    }
                }
            }
        }
        return $datas;
      }
}
?>