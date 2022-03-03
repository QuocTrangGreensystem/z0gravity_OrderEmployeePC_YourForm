<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ActivityTasksController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityTasks';

    /**
     * Components
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'Lib');

     /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Gantt', 'GanttSt','GanttV2','Number');

    function _getParentTask($taskArray, $task_id){
        if(isset($taskArray[$task_id])){

            $task = $taskArray[$task_id];

            if(isset($task['parent_id'])){
                $parent_task_id = $task['parent_id'];
                return $parent_task_id;

            }else{

                return false;

            }
        }else{

            return false;
        }
    }

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;

        $this->fileTypes = 'jpg,jpeg,bmp,gif,png,txt,zip,rar,gzip,tgz,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,xlsm,csv';
        $this->set('fileTypes', $this->fileTypes);
        $this->loadModels('ActivityRequest', 'ProjectTaskEmployeeRefer', 'ActivityTaskEmployeeRefer');
        // add settings in case other companies want to display empty employee (workload = 0 and consume = 0)
        // by default yes (display all employee in pc)
        $this->settings['show_empty_resources'] = 1;
        // display not affected in case workload = 0 & consume = 0
        $this->settings['show_empty_na'] = 0;
        // display or not the "not affected"
        $this->settings['show_na'] = intval(isset($_GET['show_na']) ? $_GET['show_na'] : 1);
        //assign to view (should replace with companyConfigs maybe?)
        $this->set('settings', $this->settings);
    }
	function testBehavior()
	{
		$this->ActivityTask->testBehavior();
		exit;
	}
    function _updateTaskConsume($taskArray, $task_id, $consume){
        if(isset($taskArray[$task_id])){

            $task = $taskArray[$task_id];

            if(isset($task['consumed'])){
                $consumed = $task['consumed'] + $consume;
                $taskArray[$task_id]['consumed'] = $consumed;

                return $taskArray;

            }else{
                $consumed = $consume;
                $taskArray[$task_id]['consumed'] = $consumed;
                return $taskArray;
            }

        }else{

            return $taskArray;
        }
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($activity_id = null) {
        $useManual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        $activityName = $this->ActivityTask->Activity->find(
            'first',
            array(
                'recursive'     => -1,
                'conditions'    => array('id' => $activity_id)));

        if (empty($activityName)) {
            $this->Session->setFlash(sprintf(__('The activity "#%s" was not found, please try again', true), $activity_id), 'error');
            $this->redirect(array('action' => 'index', $activity_id));
        }
        $activity_id = $activityName['Activity']['id'];
        $activity_pms = $activityName['Activity']['pms'];
        // $this->ActivityTask->cacheQueries = true;

        // Main data for the view
		
		// 1
        $activityTaskQuery = $this->ActivityTask->find(
            "all",
            array(
                'recursive'     => -1,
                "conditions"    => array('activity_id' => $activity_id)
            )
        );
		
		$previous = Set::combine($activityTaskQuery, '{n}.ActivityTask.previous', '{n}.ActivityTask');
		
        $activityTasks =Set::combine($activityTaskQuery, '{n}.ActivityTask.id', '{n}.ActivityTask');
        $task_id = Set::combine($activityTaskQuery, '{n}.ActivityTask.id', '{n}.ActivityTask.id');
        if (!($employeeName = $this->_getEmployee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->loadModel('ActivityRequest');
        $employees = $sumEmployees = $sumActivities = array();
		
		/* Updated by VietNguyen
		   No group by, No sum in query to server sql.
		   Fix time out with big data
		   Date: 2018/11/09
		*/
		
        // Filter data from Activity Requests

        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive'     => -1,
                'fields'        => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'value'
                ),
                'conditions'    => array(
                    'status'        => 2,
                    'company_id'    => $employeeName['company_id'],
                    'task_id'       => $task_id,
                    'NOT'           => array('value' => 0),
                )
            )
        );
		
		$sum = array();
		foreach($datas as $key => $act){
			$emp_id = $act['ActivityRequest']['employee_id'];
			$task_id = $act['ActivityRequest']['task_id'];
			$value = $act['ActivityRequest']['value'];
			$val = 0;
			if(!isset($sum[$task_id][$emp_id])) $sum[$task_id][$emp_id] = 0;
			$sum[$task_id][$emp_id] += $value;
			
		}

		
        $assignedTasks = array();
        $assignedEmployees = array();

        foreach ($sum as $task_id => $tasks) {
			foreach ($tasks as $employee_id => $value) {

				// $dx = $data['ActivityRequest'];
				// $dy = $data['0'];
				$employee_value = array();
				if(isset($task_id)){

					// $value          = $dy['value'];
					// $task_id        = $dx['task_id'];
					// $employee_id    = $dx['employee_id'];
					$employee_value = array('employee_id' => $employee_id, 'value' => $value);

					$assignedTasks[$task_id][] = $employee_value;

					// build the assigned_to array
					if(in_array($employee_id, $assignedEmployees)){

					}else{
						$assignedEmployees[] = $employee_id;
					}

					// update consum for this task
					if(isset($value)){
						$activityTasks = $this->_updateTaskConsume($activityTasks, $task_id, $value);
					}

					// build the total consume for parent task
					if($parent_task_id = $this->_getParentTask($activityTasks, $task_id)){
						if(isset($value)){
							$activityTasks = $this->_updateTaskConsume($activityTasks, $parent_task_id, $value);
						}
					}

				}
				// $data = $data['0']['value'];

				if (!isset($sumActivities[$task_id])) {
					$sumActivities[$task_id] = 0;
				}

				$sumActivities[$task_id] += $value;

				if (!isset($sumEmployees[$task_id][$employee_id])) {
					$sumEmployees[$task_id][$employee_id] = 0;
				}

				$sumEmployees[$task_id][$employee_id] += $value;
				$employees[$employee_id] = $employee_id;
			}
        }
        $employees = $this->ActivityRequest->Employee->find(
            'all',
            array(
                'fields' => array(
                    'id',
                    'first_name',
                    'last_name'
                ),
                'recursive' => -1,
                'conditions' => array('id' => $assignedEmployees)
            )
        );
        $employees =Set::combine($employees, '{n}.Employee.id', '{n}.Employee');

        foreach ($activityTasks as $key => $activity) {
            if(isset($assignedTasks)){
                if(isset($assignedTasks[$key])){
                    $employeesInTask = $assignedTasks[$key];
                    $assigned_to = "";
                    foreach ($employeesInTask as $employee_key => $employee_value) {
                        if ($employee_value) {
                            $employee = $employees[$employee_value['employee_id']];

                            $assigned_to .= $employee['first_name'] . ' ' . $employee['last_name'] . ', ';
                        }
                    }
                    $activityTasks[$key]['assigned_to'] = $assigned_to;
                }else{
                    $activityTasks[$key]['assigned_to'] = "";
                }
            }

        }
		// 2
        $_previous = array();
        foreach($previous as $previou){
            $_previous[] = $previou['previous'];
        }
        $checkPrevious = 'false';
        if(in_array('1', $_previous, true)){
            $checkPrevious = 'true';
        } else {
            $checkPrevious = 'false';
        }
		//MODIFY BY VINGUYEN 14/06/2014
		$projectRefers = null;
		if($activityName['Activity']['project'] != '') $projectRefers = $activityName['Activity']['project'];
		$listPrioritiesJson=$this->listPrioritiesJson1($activity_id,$projectRefers);
        if(!empty($activityName['Activity']['project']) || $activityName['Activity']['project'] != 0 || $activityName['Activity']['project'] != ''){
            $this->loadModel('Project');
            $_project_id = $this->Project->find('all',array(
                'fields' => array('id'),
                'conditions' => array(
                    'activity_id' => $activityName['Activity']['id'],
                ),
                'recursive' => -1
            ));
            //debug($_project_id); exit;
            $project_id = !empty($_project_id[0]['Project']['id']) ? $_project_id[0]['Project']['id'] : 0;
            $this->set(compact('activityTasks', 'activity_id', 'activityName', 'activity_pms', 'checkPrevious','project_id','listPrioritiesJson'));
        }else{

            $this->set(compact('activityTasks', 'activity_id', 'activityName', 'activity_pms', 'checkPrevious','listPrioritiesJson'));
        }
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $projectLinked = !empty($activityName['Activity']['project']) ? $activityName['Activity']['project'] : 0;

        $this->loadModel('ProjectBudgetInternalDetail');
        $this->ProjectBudgetInternalDetail->virtualFields = array('total' => 'SUM(ProjectBudgetInternalDetail.budget_md)');

        $this->loadModel('ProjectBudgetInternal');
        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        $varE = $externalConsumeds = 0;
        if($projectLinked != 0){
			$budgetInters = $this->ProjectBudgetInternalDetail->find('first', array(
				'fields' => array('total'),
				'recursive' => 1,
				'group' => array('ProjectBudgetInternalDetail.project_id'),
				'conditions' => array('ProjectBudgetInternalDetail.project_id' => $projectLinked)
			));
            $this->_checkRole(false, $projectLinked);
            $getDataProjectTasks = $this->Project->dataFromProjectTask($projectLinked, $activityName['Activity']['company_id']);
            $this->loadModel('ProjectTask');
            $projectTasks = $this->ProjectTask->find('all', array(
                'fields' => array(
                    'SUM(ProjectTask.estimated) AS Total',
                    'SUM(ProjectTask.special_consumed) AS exConsumed'
                ),
                'recursive' => -1,
                'conditions' => array('project_id' => $projectLinked, 'special' => 1)
            ));
            $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
            $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        } else {
			$budgetInters = $this->ProjectBudgetInternalDetail->find('first', array(
				'fields' => array('total'),
				'recursive' => 1,
				'group' => array('ProjectBudgetInternalDetail.activity_id'),
				'conditions' => array('ProjectBudgetInternalDetail.activity_id' => $activity_id)
			));
			
			// debug(date('m : s',time()));
			$getDataProjectTasks = $this->valueSumActivityTask($activity_id, $activityTaskQuery, $sum);
			// debug(date('m : s',time())); exit;
			// 3	
			$_varE = $_externalConsumeds = 0;
			if(!empty($activityTaskQuery)){
				foreach($activityTaskQuery as $key => $actis){
					if(!empty($actis['ActivityTask']['special']) && $actis['ActivityTask']['special'] >= 1){
						if(!empty($actis['ActivityTask']['estimated'])) $_varE +=  $actis['ActivityTask']['estimated'];
						if(!empty($actis['ActivityTask']['special_consumed'])) $_externalConsumeds +=  $actis['ActivityTask']['special_consumed'];
					}
				}
			}
        }
        // BudgetSetting
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
        //
        $this->loadModel('ProjectBudgetExternal');
        $this->ProjectBudgetExternal->virtualFields = array('total' => 'SUM(ProjectBudgetExternal.man_day)');
        $budgetExters = $this->ProjectBudgetExternal->find('first', array(
                            'fields' => array('total'),
                            'recursive' => 1,
                            'conditions' => array('ProjectBudgetExternal.activity_id' => $activity_id)
        ));
		// 4
		$varO = 0;
		if(!empty($activityTaskQuery)){
			foreach($activityTaskQuery as $key => $actis){
				if(isset($actis['ActivityTask']['special']) && $actis['ActivityTask']['special'] >= 0){
					if(!empty($actis['ActivityTask']['overload'])) $varO +=  $actis['ActivityTask']['overload'];
				}
			}
		}
        $varInter = $getDataProjectTasks['workload']-$budgetExters['ProjectBudgetExternal']['total'];
        $var =  $getDataProjectTasks['workload'] - $varE - $budgetInters['ProjectBudgetInternalDetail']['total'];
        $workloadInter = $getDataProjectTasks['workload'] - $varE - $varO;
        $workloadExter = $varE;
        // kiem tra role
        $myRole = $this->employee_info['Role']['name'];
        // kiem tra check freeze
        //$this->loadModel('ProjectSetting');
        $settingP = $this->requestAction('/project_settings/get');
        $this->loadModel('Employee');
        $modifyF =  $this->Employee->find('first',array('recusive'=>-1,'conditions'=>array(
                        'Employee.id'=>$activityName['Activity']['freeze_by']),'fields'=>array('Employee.fullname')));
        $consumedInter = !empty($getDataProjectTasks['consumed']) ? $getDataProjectTasks['consumed'] : 0;
        $remainExter = $varE - $externalConsumeds;
        $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
		
		// 5
		$consumedExter['ActivityTask']['consumedex'] = 0;
		if(!empty($activityTaskQuery)){
			foreach($activityTaskQuery as $key => $actis){
				if(isset($actis['ActivityTask']['special']) && $actis['ActivityTask']['special'] >= 1){
					if(!empty($actis['ActivityTask']['special_consumed'])) $_consumedExter['ActivityTask']['consumedex'] +=  $actis['ActivityTask']['special_consumed'];
				}
			}
		}
        if($projectLinked != 0){
            $this->ProjectTask->virtualFields = array('consumedex' => 'SUM(ProjectTask.special_consumed)');
            $consumedExterPr = $this->ProjectTask->find('first', array(
                'fields' => array('ProjectTask.consumedex'),
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id,'special'=>1)
            ));
            if(!empty($consumedExterPr) && !empty($consumedExterPr['ProjectTask']['consumedex'])){
                $consumedExter['ActivityTask']['consumedex'] = $consumedExterPr['ProjectTask']['consumedex'];
            }
        }

        $workdays = $this->requestAction('/project_tasks/getWorkdays');

        $this->set(compact('workdays', 'remainInter', 'remainExter', 'activity_id','activityName','budgetInters','getDataProjectTasks','var','myRole','settingP','modifyF','budgetExters','varInter','workloadInter','workloadExter','consumedInter','consumedExter', 'budget_settings'));
		//END
        $this->_parseParams();
    }
    /**
    * get dinamic budget and var
    **/
    function budget_var($activity_id = null){
        $this->layout = 'ajax';
        $activityName = $this->ActivityTask->Activity->find(
            'first',
            array(
                'recursive'     => -1,
                'conditions'    => array('id' => $activity_id)));

        if (empty($activityName)) {
            $this->Session->setFlash(sprintf(__('The activity "#%s" was not found, please try again', true), $activity_id), 'error');
            $this->redirect(array('action' => 'index', $activity_id));
        }
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $projectLinked = !empty($activityName['Activity']['project']) ? $activityName['Activity']['project'] : 0;
		$this->loadModel('ProjectBudgetInternalDetail');
        $this->ProjectBudgetInternalDetail->virtualFields = array('total' => 'SUM(ProjectBudgetInternalDetail.budget_md)');
        if($projectLinked != 0){
			$budgetInters = $this->ProjectBudgetInternalDetail->find('first', array(
				'fields' => array('total'),
				'recursive' => 1,
				'group' => array('ProjectBudgetInternalDetail.activity_id'),
				'conditions' => array('ProjectBudgetInternalDetail.project_id' => $projectLinked)
			));
            $this->_checkRole(false, $projectLinked);
            $getDataProjectTasks = $this->Project->dataFromProjectTask($projectLinked, $activityName['Activity']['company_id']);
        } else {
			$budgetInters = $this->ProjectBudgetInternalDetail->find('first', array(
				'fields' => array('total'),
				'recursive' => 1,
				'group' => array('ProjectBudgetInternalDetail.activity_id'),
				'conditions' => array('ProjectBudgetInternalDetail.activity_id' => $activity_id)
			));
            $getDataProjectTasks = $this->Activity->dataFromActivityTask($activity_id, $activityName['Activity']['company_id']);
        }


        $this->loadModel('ProjectBudgetInternal');

        $engagedErro = 0;
        $getDataActivities = $this->_parse($activity_id);
        $sumEmployees = $getDataActivities['sumEmployees'];
        $employees = $getDataActivities['employees'];
        if (isset($sumEmployees[$activity_id])) {
            foreach ($sumEmployees[$activity_id] as $id => $val) {
                $reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
                $engagedErro += $val * $reals;
            }
        }
        $var = $getDataProjectTasks['workload']-$budgetInters['ProjectBudgetInternalDetail']['total'];
        $this->ActivityTask->virtualFields = array('total' => 'SUM(ActivityTask.estimated)');
        $varE = $this->ActivityTask->find('first', array(
                            'fields' => array('ActivityTask.total'),
                            'recursive' => -1,
                            'conditions' => array('activity_id' => $activity_id,'special'=>1)
        ));
        $this->ActivityTask->virtualFields = array('s_over' => 'SUM(ActivityTask.overload)');
        $varO = $this->ActivityTask->find('first', array(
                            'fields' => array('ActivityTask.s_over'),
                            'recursive' => -1,
                            'conditions' => array('activity_id' => $activity_id,'special'=>0)
        ));
        $workloadInter = $getDataProjectTasks['workload'] - $varE['ActivityTask']['total']-$varO['ActivityTask']['s_over'];
        if($budgetInters['ProjectBudgetInternalDetail']['total']!=0){
            $data['var'] = round(($workloadInter/$budgetInters['ProjectBudgetInternalDetail']['total']-1)*100,2);
            $data['budget'] = round($budgetInters['ProjectBudgetInternalDetail']['total'],2);
        }else{
           $data['var'] = '0,00';
           $data['budget'] = '0,00';
        }
        $this->set('data',$data);

    }
    /**
     * Index
     *
     * @return void
     * @access public
     */
     protected function _parse($activity_id) {
        if (!($employeeName = $this->_getEmployee())) {
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
        if(!empty($sumEmployGroups)){
            foreach($sumEmployGroups as $key => $sumEmployGroup){
                foreach($sumEmployGroup as $acId => $values){
                    foreach($values as $employs => $value){
                        if(!isset($sumEmployees[$acId][$employs])){
                            $sumEmployees[$acId][$employs] = 0;
                        }
                        $sumEmployees[$acId][$employs] += $value;
                    }
                }
            }
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                            )), '{n}.Employee.id', '{n}.Employee');

        $setDatas = array();
        $setDatas['sumEmployees'] = !empty($sumEmployees) ? $sumEmployees : array();
        $setDatas['employees'] = !empty($employees) ? $employees : array();

        return $setDatas;
    }
    private function _sumPrevious($activity_id){
        $this->loadModel('ActivityRequest');
        $_sum = 0;
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'SUM(value) as consumed'
            ),
            'conditions' => array(
                'activity_id' => $activity_id,
                //'company_id' => $projectName['Project']['company_id'],
                'status' => 2,
                'NOT' => array('value' => 0)
            )
        ));
        if(isset($activityRequests[0][0]['consumed'])){
            $_sum = $activityRequests[0][0]['consumed'];
        }

        return $_sum;
    }


    /**
	 * Caculated two number
	 *
	 * @return float
	 * @access private
	 */

    private function _checkActivity($activity_id){
        $consumed = $this->_sumPrevious($activity_id);
        $data = array();
        if($consumed > 0){
            $data[0]['ActivityTask'] = array(
                    'id' => 999999999999,
                    'name' => 'Previous Tasks',
                    'parent_id' => '',
                    'task_priority_id' => '',
                    'task_status_id' => '',
                    'task_start_date' => 0,
                    'task_end_date' => 0,
                    'duration' =>  '',
                    'predecessor' => '',
                    'estimated' => '',
                    'overload' => 0,
                    'consumed' => $consumed,
                    'previous' => 1,
                    'activity_id' => $activity_id,
                    'is_previousTask' => true
                );
        }

        return $data;
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
        if (!empty($this->data)) {
            if($this->data['previous'] == '1'){
                //do nothing
                $this->Session->setFlash(__('The Previous Tasks could not be changed. The Previous Tasks is read only. Please, not interacting.', true), 'error');
            } else {
                unset($this->data['previous']);
                $this->ActivityTask->create();
                if (!empty($this->data['id'])) {
                    $this->ActivityTask->id = $this->data['id'];
                }
                unset($this->data['id']);
                if ($this->ActivityTask->save($this->data)) {
                    $result = true;
                    $this->Session->setFlash(__('The Activity Tasks has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Activity Tasks could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ActivityTask->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));

    }


    /**
     * Index
     *
     * @return void
     * @access public
     */
    function detail($id = null) {

        if (!($employeeName = $this->_getEmployee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->loadModel('ActivityRequest');
        $activityTaskName = $this->ActivityTask->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));
        if ($activityTaskName) {
            $activityTaskId[] = $activityTaskName['ActivityTask']['id'];
            if($activityTaskName['ActivityTask']['parent_id'] == 0 || $activityTaskName['ActivityTask']['parent_id'] == ''){
                $taskChilds = $this->ActivityTask->find('list', array('fields' => array('id'), 'recursive' => -1, 'conditions' => array('parent_id' => $activityTaskName['ActivityTask']['id'])));
                $activityTaskId = !empty($taskChilds) ? array_merge($activityTaskId, $taskChilds) : $activityTaskId;
            }
            if (!empty($activityTaskId)) {
                if (!empty($this->params['url']['start']) && !empty($this->params['url']['end'])) {
                    $_start = strtotime(@$this->params['url']['start']);
                    $_end = strtotime(@$this->params['url']['end']);
                    $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value','status'),
                        'conditions' => array(
                            'status' => array(0, 1, 2),
                            'task_id' => $activityTaskId,
                            'date BETWEEN ? AND ?' => array($_start, $_end),
                            'NOT' => array('value' => 0))));
                    $this->set(compact('_start', '_end'));
                } else {
                    $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value','status'),
                        'conditions' => array('status' => array(0, 1, 2), 'task_id' => $activityTaskId, 'NOT' => array('value' => 0))));
                }
            }
        }
        if (empty($activityTaskName) || empty($datas)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $activities = array();
        $activityNotValidate = array();
        $months = array();
        foreach ($datas as $data) {
            $data = array_shift($data);
            list($y, $m) = explode('-', date('Y-n', $data['date']));
            if (!isset($activities[$data['employee_id']][$y . '-' . $m])) {
                $activities[$data['employee_id']][$y . '-' . $m] = 0;
                $activityNotValidate[$data['employee_id']][$y . '-' . $m] = 0;
            }
            if($data['status'] == 2){
                    $activities[$data['employee_id']][$y . '-' . $m] += floatval($data['value']);
            }
            if($data['status'] == 0 || $data['status'] == 1){
                $activityNotValidate[$data['employee_id']][$y . '-' . $m] += floatval($data['value']);
            }
            $months[$y][$m] = $m;
        }
        $_minYear = min(array_keys($months));
        $_minMonth = min($months[$_minYear]);
        $_maxYear = max(array_keys($months));
        $_maxMonth = max($months[$_maxYear]);

        $months = array_unique(array($_minYear, $_maxYear));

        $employees = $this->ActivityRequest->Employee->find('all', array('fields' => array(
                'id', 'first_name', 'last_name'),
            'conditions' => array('Employee.id' => array_keys($activities))
            //'group' => array('Employee.first_name')
                ));

        foreach ($employees as $employee) {
            $idProfitCenters[] = $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'];
        }

        $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
            'conditions' => array('ProfitCenter.company_id' => $employeeName['company_id'], 'ProfitCenter.id' => $idProfitCenters),
            'group' => array('ProfitCenter.name')));


        $this->set(compact(array('_minYear', '_minMonth', '_maxYear', '_maxMonth', 'activities', 'employees', 'activityTaskName', 'profitCenters','activityNotValidate')));
    }


    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = $this->ActivityFamily->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmployee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
		$this->employee_info['Employee']['day_established'] = $this->employee_info['Company']['day_established'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }

    /**
     * Chinh sua lai du lieu
     *
     * @return void
     * @access protected
     */
    function delete_data($activityId) {
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        // get id of project.
        $project = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activityId),
                'fields' => array('id')
            ));
        $projectId = !empty($project) ? $project['Project']['id'] : '';
        // get id of all project task follow id project
        $projectTasks = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectId),
                'fields' => array('id')
            ));
        // get all activity task
        $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityId,
                ),
                'fields' => array('id', 'activity_id', 'project_task_id', 'previous')
            ));
        //$activityTasks = Set::classicExtract($activityTasks, '{n}.ActivityTask.project_task_id');
        foreach($activityTasks as $activityTask){
            if($activityTask['ActivityTask']['previous'] != '' || $activityTask['ActivityTask']['previous'] != 0){
                // do nothing
            } else {
                if(in_array($activityTask['ActivityTask']['project_task_id'], $projectTasks)){
                    // do nothing
                } else {
                    $this->ActivityTask->delete($activityTask['ActivityTask']['id']);
                }
            }
        }
        pr('The data delete succefull!');
        exit;
    }
    // all API for activity review
    public function get_id_project($activity_id){
        $this->loadModel('Project');
        $projects = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id),
                'fields' => array('id')
            ));

        $result = !empty($projects) ? (integer) $projects['Project']['id'] : 0;
        $this->set(compact('result'));
    }

    public function get_pms_activity($activity_id){
        $this->loadModel('Activity');
        $activities = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id),
                'fields' => array('pms', 'project')
            ));
        $result = 0;
        if((!empty($activities['Activity']['project']) || $activities['Activity']['project'] != '' || $activities['Activity']['project'] != 0)){
            $result = 1;
        }
        $this->set(compact('result'));
    }


    /** *******************************************************HANDLE TASK********************************************************************/
    /**
     *
     * @var     :
     * @return  : array date holiday
     * @author : HUUPC
     * */
    private function _getHoliday($startDate, $endDate){
        $this->loadModel('Holiday');
        $company_id = $this->employee_info['Company']['id'];
        $holidays = $this->Holiday->getOptionHolidays(strtotime($startDate), strtotime($endDate), $company_id);
        $_holiday = array();
        if ($startDate < $endDate) {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);

            while ($startDate <= $endDate){
                $_start = strtolower(date("l", $startDate));
                $_end = strtolower(date("l", $endDate));
                if($_start == 'saturday' || $_start == 'sunday' || in_array($startDate, array_keys($holidays))){
                    $_holiday[] = date("m-d-Y", $startDate);
                }
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
            }
        }
        return $_holiday;
    }

     /**
     *
     * @var     :
     * @return  : int date working date
     * @author : HUUPC
     * */
    private function _getWorkingDays($startDate, $endDate, $duration){
        $_durationDate = '';
        if($startDate != '0000-00-00' && $endDate != '0000-00-00'){
            if ($startDate <= $endDate) {
                $_holiday = $this->_getHoliday($startDate, $endDate);
                $_holiday = count($_holiday);

                $dates_range[]= $startDate;

                $startDate = strtotime($startDate);
                $endDate = strtotime($endDate);

                $_date = 0;
                while ($startDate <= $endDate){
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                        $dates_range[]=date('Y-m-d', $startDate);
                        $_date++;
                }
                if($_holiday != 0){
                    $_date = $_date - $_holiday;
                }
            } else {
                $_date = 0;
            }
            $_durationDate = $_date;
        } else {
            $_durationDate = $duration;
        }

        return $_durationDate;
    }

    /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _durationStartDate($startDate, $endDate, $duration){
        $_durationStartDate = '';
        if(strtotime($startDate) == '' && !empty($duration)){
            $dates_ranges[]= $endDate;
            $_endDate = strtotime($endDate);
            for($_count = 1; $_count < $duration; $_count++){
                $_endDate= mktime(0, 0, 0, date("m", $_endDate), date("d", $_endDate)-1, date("Y", $_endDate));
                $dates_ranges[] = date("Y-m-d", $_endDate);
            }
            $_addDates = 0;
            foreach($dates_ranges as $key => $dates_range){
                $dates_range = strtotime($dates_range);
                $_dateFitter = strtolower(date("l", $dates_range));
                if($_dateFitter == 'saturday' || $_dateFitter == 'sunday'){
                    $_addDates++;
                }
            }
            $_addDates = count($dates_ranges) + $_addDates;

            $_startDates[] = $endDate;

            $endDate = strtotime($endDate);
            for($_count = 1; $_count < $_addDates; $_count++){
                $endDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate)-1, date("Y", $endDate));
                $_startDates[] = date("Y-m-d", $endDate);
            }

            $_popDate = array_pop($_startDates);
            $_popDate = strtotime($_popDate);
            $_checkDate = strtolower(date("l", $_popDate));
            if($_checkDate == 'sunday'){
                $_start = mktime(0, 0, 0, date("m", $_popDate), date("d", $_popDate)-2, date("Y", $_popDate));
            } else if($_checkDate == 'saturday'){
                $_start = mktime(0, 0, 0, date("m", $_popDate), date("d", $_popDate)-1, date("Y", $_popDate));
            } else {
                $_start = $_popDate;
            }
            $_start = date("Y-m-d", $_start);

            $_durationStartDate = $_start;
        } else {
            $_durationStartDate = $startDate;
        }

        return $_durationStartDate;
    }


    /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _durationEndDate($startDate, $endDate, $duration){
        $_durationEndDate = '';
        if((strtotime($endDate) == '' || $endDate == '0000-00-00') && !empty($duration)){
            $dates_ranges[]= $startDate;
            $_startDate = strtotime($startDate);
            for($_count = 1; $_count < $duration; $_count++){
                $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+1, date("Y", $_startDate));
                $dates_ranges[] = date("Y-m-d", $_startDate);
            }
            $_addDates = 0;
            foreach($dates_ranges as $key => $dates_range){
                $dates_range = strtotime($dates_range);
                $_dateFitter = strtolower(date("l", $dates_range));
                if($_dateFitter == 'saturday' || $_dateFitter == 'sunday'){
                    $_addDates++;
                }
            }
            $_addDates = count($dates_ranges) + $_addDates;
            $_endDates[] = $startDate;
            $startDate = strtotime($startDate);
            for($_count = 1; $_count < $_addDates; $_count++){
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                $_endDates[] = date("Y-m-d", $startDate);
            }

            $_popDate = array_pop($_endDates);
            $_popDate = strtotime($_popDate);
            $_checkDate = strtolower(date("l", $_popDate));
            if($_checkDate == 'sunday'){
                $_end = mktime(0, 0, 0, date("m", $_popDate), date("d", $_popDate)+1, date("Y", $_popDate));
            } else if($_checkDate == 'saturday'){
                $_end = mktime(0, 0, 0, date("m", $_popDate), date("d", $_popDate)+2, date("Y", $_popDate));
            } else {
                $_end = $_popDate;
            }
            $_end = date("Y-m-d", $_end);

            $_durationEndDate = $_end;
        } else {
            $_durationEndDate = $endDate;
        }
        return $_durationEndDate;
    }

    /**
     *
     * @var     :
     * @return  : start date $startDate
     * @author : HUUPC
     * */
    private function _predecessor($startDate, $predecessor, $activityTaskId, $slider = 0){
        if(!empty($predecessor)){
            $datas = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $activityTaskId),
                    'fields' => array('id', 'predecessor', 'parent_id')
                ));
            if(($datas['ActivityTask']['id'] != $predecessor) && ($predecessor != $datas['ActivityTask']['parent_id'])){
                $endDate = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $predecessor),
                    'fields' => array('id', 'predecessor', 'task_start_date', 'task_end_date', 'parent_id')
                ));
                if($endDate['ActivityTask']['parent_id'] != null || $endDate['ActivityTask']['parent_id'] != 0){
                    $idSave = $endDate['ActivityTask']['id'];
                    $getsStart = $this->ActivityTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('ActivityTask.id' => $idSave),
                        'fields' => array('task_end_date')
                    ));
                    $startDate = $getsStart['ActivityTask']['task_end_date'];
                    if($slider != 0){
                        $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+$slider, date("Y", $startDate));
                    } else {
                        $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                    }
                    $startDate = date('Y-m-d', $startDate);
                }
            }

        } else {
            $predecessor = "";
        }

        return $startDate;
    }

    // PREDECATED CODE SECTION - END
    // NEW CODE SECTION
    private function __buildTree(array &$elements, $parentId = -1) {
        $useManual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        $branch = array();
        foreach ($elements as $key => $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->__buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                    $element['leaf'] = 'false';
                    $element['expanded'] = 'false';
                    if($element['parent_id'] == -1){
                        //do nothing
                    }else{
                        $sumRemain = $sumEstimated = $sumInitialEstimated = $sumOverload = $sumWait = $sumManualOverload = 0;
                        foreach ($children as $keys => $child){
                            if(isset($child['consumed'])){
                                $element['consumed'] += $child['consumed'];
                                $element['manual_consumed'] += $child['manual_consumed'];
                            }
                            $sumRemain += $child['remain'];
                            $sumWait += !empty($child['wait']) ? $child['wait'] : 0;
                            $sumEstimated += $child['estimated'];
                            $sumInitialEstimated += $child['initial_estimated'];
                            $sumOverload += !empty($child['overload']) ? $child['overload'] : 0;
                            $sumManualOverload += !empty($child['manual_overload']) ? $child['manual_overload'] : 0;
                            $element['remain'] = $sumRemain;
                            $element['wait'] = $sumWait;
                            $element['estimated'] = $sumEstimated;
                            $element['initial_estimated'] = $sumInitialEstimated;
                            $element['overload'] = $sumOverload;
                            $element['manual_overload'] = $sumManualOverload;
                            if( !$useManual ){
                                if($sumEstimated == 0){
                                    $element['completed'] = '0 %';
                                } else {
                                    if($element['consumed'] < 0){
                                        $element['completed'] = '0 %';
                                    } else {
                                        $completedPhase = round((($element['consumed']*100)/($sumEstimated+$sumOverload)), 2);
                                        if($completedPhase > 100){
                                            $element['completed'] = '100 %';
                                        } else {
                                            $element['completed'] = $completedPhase . ' %';
                                        }
                                    }
                                }
                            } else {
                                if($sumEstimated == 0){
                                    $element['completed'] = '0 %';
                                } else {
                                    if($element['manual_consumed'] < 0){
                                        $element['completed'] = '0 %';
                                    } else {
                                        $completedPhase = round((($element['manual_consumed']*100)/($sumEstimated+$sumManualOverload)), 2);
                                        if($completedPhase > 100){
                                            $element['completed'] = '100 %';
                                        } else {
                                            $element['completed'] = $completedPhase . ' %';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $branch[] = $element;
            } else {

            }
        }
        return $branch;
    }

    private function _getProfitCenterAndEmployee($activity_id,$project_id=null){
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('Employee');

         $this->loadModel('CompanyEmployeeReference');
        $employeeName = $this->_getEmployee();
        $ProfitRefer = ClassRegistry::init('ActivityProfitRefer');
        $profits = $ProfitRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id),
                'fields' => array('id', 'profit_center_id')
            ));
		$employeeIds = array();
		if($project_id !== null)
		{
			$this->loadModel('ProjectTeam');
			$teams = $this->ProjectTeam->find('all', array(
				'fields' => array('id', 'profit_center_id'),
				'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id', 'profit_center_id')),
				'conditions' => array('project_id' => $project_id)));
			$employeeIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id')) : array();

			if(!empty($employeeIds)){
				foreach($employeeIds as $employeeId){
					foreach($employeeId as $v){
						$_employeeId[] = $v;
					}
				}
				$employeeIds = array_unique($_employeeId);
			}
		}
        $datas = array();
        if(!empty($profits)){
            $profitCenters = $this->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ProfitCenter.id' => $profits,
                        'company_id' => $employeeName['company_id']
                    ),
                    'order' => array(
                            'name' => 'ASC'
                    )
                ));
            $_profit = $_employee = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $_profit[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $_profit[$ks]['Employee']['is_profit_center'] = 1;
                $_profit[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
            }
            $employeeRefers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $profits),
                    'fields' => array('id', 'employee_id')
                ));
			$employeeRefers = array_merge($employeeIds,$employeeRefers);
            if(!empty($employeeRefers)){
                $employeeRefers = array_unique($employeeRefers);
                 $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'conditions' => array(
                        'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                        'NOT' => array('Employee.is_sas' => 1),
                        'OR' => array(
                            array('Employee.end_date' => '0000-00-00'),
                            array('Employee.end_date IS NULL'),
                            array('Employee.end_date >=' => date('Y-m-d', time())),
                        ),
                        'Employee.id' => $employeeRefers,
                        //'Employee.actif' => 1
                    ),
                    'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
                    'order' => array(
                            'first_name' => 'ASC',
                            'last_name' => 'ASC'
                        )
                 ));
                foreach ($employees as $k => $employee) {

                    $_employee[$k]['Employee']['id'] = $employee['Employee']['id'];
                    $_employee[$k]['Employee']['is_profit_center'] = 0;
                    $_employee[$k]['Employee']['name'] = $employee['Employee']['first_name'].' '.$employee['Employee']['last_name'];
                }
            }
            $datas = array_merge($_employee ,$_profit);
        } else {
            $profitCenters = $this->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employeeName['company_id']
                    )
                ));
            $_profit = $_employee = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $_profit[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $_profit[$ks]['Employee']['is_profit_center'] = 1;
                $_profit[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
            }
            $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'conditions' => array(
                        'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                        'NOT' => array('Employee.is_sas' => 1),
                        'OR' => array(
                            array('Employee.end_date' => '0000-00-00'),
                            array('Employee.end_date IS NULL'),
                            array('Employee.end_date >=' => date('Y-m-d', time())),
                        )
                    ),
                    'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
                    'order' => array(
                            'first_name' => 'ASC',
                            'last_name' => 'ASC'
                        )
                 ));
            foreach ($employees as $k => $employee) {
                $_employee[$k]['Employee']['id'] = $employee['Employee']['id'];
                $_employee[$k]['Employee']['is_profit_center'] = 0;
                $_employee[$k]['Employee']['name'] = $employee['Employee']['first_name'].' '.$employee['Employee']['last_name'];
            }
            $datas = array_merge($_employee, $_profit );
        }
        return $datas;

    }
    public function getHistory($activity_id){
        //history filter by QN on 2015-07-31
        $this->loadModel('HistoryFilter');
        $settings = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'ActivityTaskSettings-' . $activity_id,
                'employee_id' => $this->employee_info['Employee']['id']
            )
        ));
        if( empty($settings) ){
            $settings = $this->HistoryFilter->save(array(
                'id' => null,
                'path' => 'ActivityTaskSettings-' . $activity_id,
                'employee_id' => $this->employee_info['Employee']['id'],
                'params' => '{}'
            ));
            $settings = array();
        }
        else $settings = json_decode($settings['HistoryFilter']['params'], true);
        return $settings;
    }
	public function listPrioritiesJson($activity_id) {
		$this->layout = 'ajax';
        $this->loadModel('ProjectPriority');
        $this->loadModel('ProjectStatus');
        $result = array();
        $priorities = $this->ProjectPriority->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id']
                    ),
                'fields' => array('id', 'priority')
            ));
        $status = $this->ProjectStatus->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id', 'name')
            ));
        $assigns = $this->_getProfitCenterAndEmployee($activity_id);
        $result['priorities'] = $priorities;
        $result['status'] = $status;
        $result['Employees'] = $assigns;
        $this->loadModel('Profile');
        $result['Profiles'] = $this->Profile->find('all', array(
            'recursive' => -1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            )
        ));
        $result['history'] = $this->getHistory($activity_id);
        $this->set(compact('result'));
    }
	//MODIFY BY VINGUYEN 14/06/2014
	public function listPrioritiesJson1($activity_id,$projectRefer=null) {
        $this->loadModel('ProjectPriority');
        $this->loadModel('ProjectStatus');
        $result = array();
        $priorities = $this->ProjectPriority->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id']
                    ),
                'fields' => array('id', 'priority')
            ));
        $status = $this->ProjectStatus->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id', 'name')
            ));
        $assigns = $this->_getProfitCenterAndEmployee($activity_id,$projectRefer);
        $result['Priorities'] = $priorities;
        $result['Statuses'] = $status;
        $result['Employees'] = $assigns;

        $this->loadModel('Profile');
        $result['Profiles'] = $this->Profile->find('all', array(
            'recursive' => -1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            )
        ));
        if ($projectRefer) {
            $this->loadModel('ProjectMilestone');
            $result['Milestone'] = $this->ProjectMilestone->find('all', array(
                'recursive' => -1,
                'order' => array('id' => 'ASC'),
                'conditions' => array(
                    'project_id' => $projectRefer
                )
            ));
        }
        $result['history'] = $this->getHistory($activity_id);
        return json_encode($result);
    }
	//END

    /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _getStartEndDateAllTask($activity_id) {

        $data = array();
        $tasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activity_id,
                    'NOT' => array('task_start_date' => '0000-00-00', 'task_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'MIN(task_start_date) AS startDate',
                    'MAX(task_end_date) AS endDate'
                )
            ));
        $initial_tasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activity_id,
                    'NOT' => array('initial_task_start_date' => '0000-00-00', 'initial_task_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'MIN(initial_task_start_date) AS initial_startDate',
                    'MAX(initial_task_end_date) AS initial_endDate'
                )
            ));
        $data['task_start_date'] = isset($tasks[0][0]['startDate']) ? $tasks[0][0]['startDate'] : 0;
        $data['task_end_date'] = isset($tasks[0][0]['endDate']) ? $tasks[0][0]['endDate'] : 0;
        $data['initial_task_start_date'] = isset($initial_tasks[0][0]['initial_startDate']) ? $initial_tasks[0][0]['initial_startDate'] : '0000-00-00';
        $data['initial_task_end_date'] = isset($initial_tasks[0][0]['initial_endDate']) ? $initial_tasks[0][0]['initial_endDate'] : '0000-00-00';
        return $data;
    }

    private function _statusForCompany($name = false){
        $this->loadModel('ProjectStatus');
        $projectStatus = $this->ProjectStatus->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id',  'name')
            ));
        if( $name )return $projectStatus['ProjectStatus'];
        return $projectStatus['ProjectStatus']['id'];
    }

    /**
     *
     * @var     :
     * @return  : start date $startDate
     * @author : HUUPC
     * */
    private function _createPredecessor($startDate, $predecessor,  $slider = 0){
        $startDate = '';
        if(!empty($predecessor)){
            $datas = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $predecessor),
                    'fields' => array('id', 'predecessor', 'parent_id', 'task_end_date')
                ));
            $startDate = $datas['ActivityTask']['task_end_date'];
            if(($datas['ActivityTask']['parent_id'] != null || $datas['ActivityTask']['parent_id'] != 0)){
                if($slider != 0){
                    $startDate = mktime(0, 0, 0, date("m", $datas['ActivityTask']['task_end_date']), date("d", $datas['ActivityTask']['task_end_date'])+$slider, date("Y", $datas['ActivityTask']['task_end_date']));
                } else {
                    $startDate = mktime(0, 0, 0, date("m", $datas['ActivityTask']['task_end_date']), date("d", $datas['ActivityTask']['task_end_date'])+1, date("Y", $datas['ActivityTask']['task_end_date']));
                }

                $startDate = date('Y-m-d',$startDate );
            }

        } else {
            $predecessor = "";
        }

        return $startDate;
    }

    /**
     *
     * @var     : int $project_id
     * @return  :
     * @author : HUUPC
     */
    private function _updateParentTask($activity_id) {
        $caculates = $this->ActivityTask->find('all', array(
                'fields' => array(
                                'id',
                                'name',
                                'parent_id',
                                'estimated',
                                'SUM(estimated) as Esti',
                                'MIN(task_start_date) as min_date',
                                'MAX(task_end_date) as max_date',
                                'SUM(overload) as s_over',
                                'SUM(manual_overload) as mover'
                            ),
                'recursive' => -1,
                "conditions" => array(
                        'activity_id' => $activity_id,
                        'NOT' => array('task_start_date' => null, 'task_end_date' => null)
                    ),
                'group' => array('parent_id')
            ));
        foreach($caculates as $caculate){
            if($caculate['ActivityTask']['parent_id'] != 0 && $caculate['ActivityTask']['parent_id'] < '999999999999'){
                $this->ActivityTask->id = $caculate['ActivityTask']['parent_id'];
                if(empty($caculate[0]['Esti'])){
                    $caculate[0]['Esti'] = 0;
                }
                $this->ActivityTask->saveField('estimated', $caculate[0]['Esti']);
                $this->ActivityTask->saveField('task_end_date', $caculate[0]['max_date']);
                $this->ActivityTask->saveField('task_start_date', $caculate[0]['min_date']);
                $this->ActivityTask->saveField('overload', $caculate[0]['s_over']);
                $this->ActivityTask->saveField('manual_overload', $caculate[0]['mover']);
                $this->ActivityTask->saveField('profile_id', 0);
            }
        }
    }

    /**
     * Function: Save Employees with in current Project
     * @var     : int $project_id
     * @var     : array $employess
     * @return  :
     * @author : HUUPC
     */
    private function _saveEmployees($employees, $profitCenters, $activity_id, $workload, $tmp_estimated_details, $callbacks=true) {
        $this->loadModel('ActivityTaskEmployeeRefer');

        $saveds = $this->ActivityTaskEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array('ActivityTaskEmployeeRefer.activity_task_id' => $activity_id),
            'fields' => array('id')
                ));
        foreach($employees as $k => $v){
            if($v == '' || $v == null){
                unset($employees[$k]);
            }
        }
        if (!empty($employees)) {
            $_employees = array();
            if(!empty($profitCenters)){
                $count = 0;
                foreach($employees as $id => $employee){
                    foreach($profitCenters as $k => $profitCenter){
                        if($id == $k){
                            $_employees[$id]['reference_id'] = $employee;
                            $_employees[$id]['is_profit_center'] = $profitCenter;
                            if(isset($tmp_estimated_details[$id])){
                                $_employees[$id]['estimated'] = $tmp_estimated_details[$id];
                            }else{
                                if($count == 0){
                                    $_employees[$id]['estimated'] = $workload;
                                } else {
                                    $_employees[$id]['estimated'] = 0;
                                }
                            }
                        }
                    }
                    $count++;
                }
            }
            if (!empty($saveds)) {
                foreach ($saveds as $key) {
                    $this->ActivityTaskEmployeeRefer->id = $key['ActivityTaskEmployeeRefer']['id'];
                    $this->ActivityTaskEmployeeRefer->delete();
                }
            }
            if(!empty($_employees)){
                if(count($_employees) == 1){
                    $this->ActivityTaskEmployeeRefer->create();
                    $data = array(
                        'reference_id' => $_employees[0]['reference_id'],
                        'is_profit_center' => $_employees[0]['is_profit_center'],
                        'activity_task_id' => $activity_id,
                        'estimated' => $workload
                    );
                    $this->ActivityTaskEmployeeRefer->save($data, array('validate' => true, 'callbacks' => $callbacks));
                } else {
                    foreach($_employees as $emp_key => $employee) {
                        $this->ActivityTaskEmployeeRefer->create();
                        $data = array(
                            'reference_id' => $employee['reference_id'],
                            'is_profit_center' => $employee['is_profit_center'],
                            'activity_task_id' => $activity_id,
                            'estimated'        => $employee['estimated']
                        );
                        $this->ActivityTaskEmployeeRefer->save($data, array('validate' => true, 'callbacks' => $callbacks));
                    }
                }

            }
        } else {
            if (!empty($saveds)) {
                foreach ($saveds as $key) {
                    $this->ActivityTaskEmployeeRefer->id = $key['ActivityTaskEmployeeRefer']['id'];
                    $this->ActivityTaskEmployeeRefer->delete();
                }
            }
        }
    }

    /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
     private function _updatePredeces($activityTaskId, $predecessor){
        $_predecessor = 0;
        $datas = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $activityTaskId),
                    'fields' => array('id', 'predecessor')
                ));
        if($datas['ActivityTask']['id'] != $predecessor){
            $_predecessor = $predecessor;
        }
        return $_predecessor;
     }

     /**
     *
     * @var     :
     * @return  : is Predecessor
     * @author : huythang38
     * */
     private function _isPredecessor($activity_id)
     {
        // Huu comment 30/10/2013
        //$bool = 'false';
//         # code...
//        if (!empty($activityTasks) && !empty($activity_id)) {
//            # code...
//            foreach ($activityTasks as $key => $activityTask) {
//                # code...
//                if ($activity_id == $activityTask['ActivityTask']['predecessor']) {
//                    # code...
//                    $bool = 'true';
//                    break;
//                }else{
//                    $bool = 'false';
//                }
//            }
//        }
        // Huu change ngay 30/10/2013
        $aTasks = $this->ActivityTask->find('count', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.predecessor' => $activity_id)
        ));
        $bool = 'false';
        if($aTasks != 0){
            $bool = 'true';
        }
        return $bool;
     }
    /**
     * HuuPc da check
     */
    private function _getAllActivityTasks($activity_id){
        $this->loadModel('Activity');
        $this->loadModel('ProjectPriority');
        $this->loadModel('ProjectStatus');
        $this->loadModel('ActivityRequest');
        $useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        $activities = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id),
                'fields' => array('id', 'name')
            ));
        $priorities = $this->ProjectPriority->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id']
                    ),
                'fields' => array('id', 'priority')
            ));
        $status = $this->ProjectStatus->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id', 'name', 'status')
            ));
        $statusOfProjectStatus = !empty($status) ? Set::combine($status, '{n}.ProjectStatus.id', '{n}.ProjectStatus.status') : array();
        $status = !empty($status) ? Set::combine($status, '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
        //added by qn on 2015-06-04
        $profiles = ClassRegistry::init('Profile')->find('list', array(
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $activityTasks = $this->ActivityTask->find('all', array(
                //'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id),
                'fields' => array(
                    'id',
                    'name',
                    'parent_id',
                    'task_priority_id',
                    'task_status_id',
					'task_assign_to',
                    'estimated',
                    'activity_id',
                    'previous',
                    'task_start_date',
                    'task_end_date',
                    'duration',
                    'estimated',
                    'overload',
                    'consumed',
                    'predecessor',
					'special',
					'special_consumed',
                    'initial_estimated',
                    'initial_task_start_date',
                    'initial_task_end_date',
                    'is_nct',
                    'profile_id',
                    'manual_consumed',
                    'manual_overload',
                    'text_1',
                    'attachment',
                    'text_updater',
                    'text_time',
                    'slider'
                )
            ));
        $previous = $this->_checkActivity($activity_id);
        if(!empty($previous)){
            $activityTasks = array_merge($activityTasks, $previous);
        }
        $taskIds = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        // Filter data from Activity Requests
        
        // $activityRequests = $this->ActivityRequest->find('all', array(
            // 'recursive' => -1,
            // 'fields' => array(
                // 'id',
                // 'employee_id',
                // 'task_id',
                // 'SUM(CASE WHEN `status` = 2 THEN `value` ELSE 0 END) AS valid',
                // 'SUM(CASE WHEN `status` = -1 OR `status` = 0 OR `status` = 1 THEN `value` ELSE 0 END) AS wait',
                // 'COUNT(task_id) AS hasUsed'
            // ),
            // 'group' => array('task_id'),
            // 'conditions' => array(
                // 'task_id' => $taskIds,
                // 'company_id' => $this->employee_info['Company']['id'],
                // 'NOT' => array('value' => 0, "task_id" => null),
            // ))
        // );
        // $activityRequests = !empty($activityRequests) ? Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0') : array();
		
		/* Updated by VietNguyen
		   No group by, No sum in query to server sql.
		   Fix time out with big data
		   Date: 2018/11/09
		*/
		$activityRequests = $this->ActivityRequest->find(
            'all',
            array(
                'recursive'     => -1,
                'fields'        => array(
                    'id',
                    'task_id',
                    'value',
					'status',
                ),
                'conditions'    => array(
                    'company_id'    => $this->employee_info['Company']['id'],
                    'task_id'       => $taskIds,
                )
            )
        );
		
		$sum = array();
		foreach($activityRequests as $key => $act){
			$task_id = $act['ActivityRequest']['task_id'];
			$value = $act['ActivityRequest']['value'];
			$status = $act['ActivityRequest']['status'];
			if(!isset($sum[$task_id]['valid'])) $sum[$task_id]['valid'] = 0;
			if(!isset($sum[$task_id]['wait'])) $sum[$task_id]['wait'] = 0;
			if(!isset($sum[$task_id]['hasUsed'])) $sum[$task_id]['hasUsed'] = 0;
			$sum[$task_id]['valid'] = ($status == 2) ? $sum[$task_id]['valid'] += $value : $sum[$task_id]['valid'];
			$sum[$task_id]['wait'] = ($status == -1 || $status == 0 || $status == 1) ? $sum[$task_id]['wait'] += $value : $sum[$task_id]['wait'];
			$sum[$task_id]['hasUsed'] += 1;
			
		}
		// lay start date va end date cua all task
        $startEndDates      = $this->_getStartEndDateAllTask($activity_id);
        $employees = $this->_getProfitCenterAndEmployee($activity_id);
        $parent = array();
        if(!empty($activities)){
            $parent[0]['phase_id']         = $activities['Activity']['id'];
            $parent[0]['parent_id']        = 0;
            $parent[0]['id']               = 999999999999 + $activities['Activity']['id'];
            $parent[0]['task_title']       = isset($activities['Activity']['name']) ? $activities['Activity']['name'] : '';
            $parent[0]['task_start_date']  = !empty($startEndDates['task_start_date']) ? date('Y-m-d', $startEndDates['task_start_date']): '0000-00-00';
            $parent[0]['task_end_date']    = !empty($startEndDates['task_end_date']) ? date('Y-m-d', $startEndDates['task_end_date']): '0000-00-00';
            $parent[0]['initial_task_start_date']  = !empty($startEndDates['task_start_date']) ? date('Y-m-d', $startEndDates['task_start_date']): '0000-00-00';
            $parent[0]['initial_task_end_date']    = !empty($startEndDates['task_end_date']) ? date('Y-m-d', $startEndDates['task_end_date']): '0000-00-00';
            $parent[0]['duration']         = $this->_getWorkingDays($parent[0]['task_start_date'], $parent[0]['task_end_date'], '');
            $parent[0]['estimated']        = 0;
            $parent[0]['initial_estimated']        = 0;
            $parent[0]['consumed']         = 0;
            $parent[0]['hasUsed']           = 0;
            $parent[0]['wait']             = 0;
            $parent[0]['completed']        = 0;
            $parent[0]['remain']           = 0;
            $parent[0]['manual_consumed']  = 0;
            $parent[0]['manual_overload']  = 0;
            if(count($activityTasks) == 0){
                $parent[0]['leaf']             = 'true';
                $parent[0]['expanded']         = 'false';
            } else {
                $parent[0]['leaf']             = 'false';
                $parent[0]['expanded']         = 'true';
            }
            $parent[0]['is_phase']         = 'true';
        }
        $children = array();
        if(!empty($activityTasks)){
			$provider = ClassRegistry::init('BudgetProvider')->find('list',array(
					'fields' => array('id','name'),
				)
			);
            foreach($activityTasks as $key => $activityTask){

                // 26-10-2013 huythang38
                $activityId = $activityTask['ActivityTask']['id'];
                // $projectTask['ProjectTask']['is_predecessor'] = $this->_isPredecessor($projectTasks, $projectId);
                $children[$key]['is_predecessor'] = $this->_isPredecessor($activityId);

                $children[$key]['id']               = $activityTask['ActivityTask']['id'];
                if (empty($activityTask['ActivityTask']['parent_id'])) {
                    $children[$key]['parent_id'] = 999999999999 + $activityTask['ActivityTask']['activity_id'];
                    $children[$key]['leaf'] = 'true';
                } else {
                    $children[$key]['parent_id']    = $activityTask['ActivityTask']['parent_id'];
                    $children[$key]['leaf'] = 'true';
                }
                $children[$key]['task_title']       = isset($activityTask['ActivityTask']['name']) ? $activityTask['ActivityTask']['name'] : '';

                $priority_id = $activityTask['ActivityTask']['task_priority_id'];
                if( $priority_id ){
                    $priority_text = !empty($priorities[$priority_id]) ? $priorities[$priority_id] : '';
                } else {
                    $priority_text = null;
                }
                $children[$key]['task_priority_id'] = $priority_id;
                $children[$key]['task_priority_text'] = $priority_text;

                $status_id = $activityTask['ActivityTask']['task_status_id'];
                $statusOfStatus = 'IP';
                if( $status_id ){
                    $status_text = !empty($status[$status_id]) ? $status[$status_id] : '';
                    $statusOfStatus = isset($statusOfProjectStatus[$status_id]) ? $statusOfProjectStatus[$status_id] : 'IP';
                } else {
                    $status_text = null;
                }

                $children[$key]['task_status_id'] = $status_id;
                $children[$key]['task_status_text'] = $status_text;
                $children[$key]['task_status_st'] = $statusOfStatus;
                //profile text
                if( isset($activityTask['ActivityTask']['profile_id']) && isset($profiles[$activityTask['ActivityTask']['profile_id']]) ){
                    $profile_text = $profiles[$activityTask['ActivityTask']['profile_id']];
                    $children[$key]['profile_id'] = $activityTask['ActivityTask']['profile_id'];
                } else {
                    $profile_text = null;
                }
                $children[$key]['profile_text'] = $profile_text;

                if(isset($activityTask['ActivityTask']['special'])&&$activityTask['ActivityTask']['special']==1)
				{
					$children[$key]['task_assign_to_text'][] = $provider[$activityTask['ActivityTask']['task_assign_to']];
				}
				else
				{
					if(!empty($activityTask['ActivityTaskEmployeeRefer'])) {
						unset($children[$key]['task_assign_to_id']);
						foreach ($activityTask['ActivityTaskEmployeeRefer'] as $k => $value) {
							$children[$key]['task_assign_to_id'][] = $value['reference_id'];
							$children[$key]['is_profit_center'][] = $value['is_profit_center'];
							$children[$key]['estimated_detail'][] = $value['estimated'];
							if (isset($value['reference_id'])) {
								foreach($employees as $employee){
									if($value['reference_id'] == $employee['Employee']['id'] && $value['is_profit_center'] == $employee['Employee']['is_profit_center']){
										//$task_assign_to_text = isset($value['reference_id']) ? $employees[$value['reference_id']] : '';
										$children[$key]['task_assign_to_text'][] = $employee['Employee']['name'];

									}  else {
										// handle error here
									}
								}
							} else {
								// handle error here
							}
						}
					}
				}

                $children[$key]['predecessor'] = $activityTask['ActivityTask']['predecessor'];

                $start = !empty($activityTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $activityTask['ActivityTask']['task_start_date']) : '0000-00-00';
                $end = !empty($activityTask['ActivityTask']['task_end_date']) ? date('Y-m-d', $activityTask['ActivityTask']['task_end_date'])  : '0000-00-00';
                $_duration = $activityTask['ActivityTask']['duration'];

                $children[$key]['task_start_date']  = $start;
                $children[$key]['task_end_date']    = $end;
                $children[$key]['is_nct']    = isset($activityTask['ActivityTask']['is_nct']) ? $activityTask['ActivityTask']['is_nct'] : 0;
                $children[$key]['initial_task_start_date']  = !empty($activityTask['ActivityTask']['initial_task_start_date']) ? $activityTask['ActivityTask']['initial_task_start_date'] : '0000-00-00';
                $children[$key]['initial_task_end_date']    = !empty($activityTask['ActivityTask']['initial_task_end_date']) ? $activityTask['ActivityTask']['initial_task_end_date']  : '0000-00-00';
                if(!empty($_duration)){
                    $children[$key]['duration'] = $_duration;
                } else {
                    $children[$key]['duration'] = $this->_getWorkingDays($start, $end, '');
                }
                $consumed = $estimated = $initial_estimated = 0;

                if($activityTask['ActivityTask']['previous'] == 1){
                    //consumed for previous
                    $cons_previous = isset($activityTask['ActivityTask']['consumed']) ? $activityTask['ActivityTask']['consumed'] : 0;
                    $children[$key]['consumed'] = $cons_previous;
                    $children[$key]['manual_consumed'] = 0;
                    $children[$key]['manual_overload'] = 0;
                    $consumed = $cons_previous;
                    //estiamted for previous
                    $children[$key]['estimated'] = $cons_previous;
                    $estimated = $cons_previous;
                    $children[$key]['initial_estimated'] = $cons_previous;
                    $initial_estimated = $cons_previous;
                } else {
                    //consumed task
					/*if(isset($activityTask['ActivityTask']['special'])&&$activityTask['ActivityTask']['special']==1)
					{
						$consumed=$activityTask['ActivityTask']['special_consumed'];
					}
					else
					{
						$consumed = !empty($activityRequests[$activityTask['ActivityTask']['id']]['valid']) ? $activityRequests[$activityTask['ActivityTask']['id']]['valid'] : 0;
					}*/
					$consumed = !empty($sum[$activityTask['ActivityTask']['id']]['valid']) ? $sum[$activityTask['ActivityTask']['id']]['valid'] : 0;
                    $children[$key]['consumed'] = $consumed;
                    $children[$key]['manual_consumed'] = $activityTask['ActivityTask']['manual_consumed'];
                    $children[$key]['manual_overload'] = $activityTask['ActivityTask']['manual_overload'];
                    $children[$key]['hasUsed'] = !empty($sum[$activityTask['ActivityTask']['id']]['hasUsed']) ? $sum[$activityTask['ActivityTask']['id']]['hasUsed'] : 0;
                    $children[$key]['wait'] = !empty($sum[$activityTask['ActivityTask']['id']]['wait']) ? $sum[$activityTask['ActivityTask']['id']]['wait'] : 0;
                    //estimated task
                    $est = isset($activityTask['ActivityTask']['estimated']) ? $activityTask['ActivityTask']['estimated'] : 0;
                    $children[$key]['estimated'] = $est;
                    $estimated = $est;
                     //initial estimated task
                    $initial_est = isset($activityTask['ActivityTask']['initial_estimated']) ? $activityTask['ActivityTask']['initial_estimated'] : 0;
                    $children[$key]['initial_estimated'] = $initial_est;
                    $initial_estimated = $initial_est;
                }
                    //$children[$key]['initial_estimated'] = isset($activityTask['ActivityTask']['initial_estimated']) ? $activityTask['ActivityTask']['initial_estimated'] : 0;
                if($consumed >= $estimated){
                    if(isset($activityTask['ActivityTask']['is_previousTask']) && $activityTask['ActivityTask']['is_previousTask'] == true){
                        //do nothing
                    } else {
                        $children[$key]['overload'] = $consumed - $estimated;
                        $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                        $_saved['overload'] = $children[$key]['overload'];
                        $this->ActivityTask->save($_saved);
                    }
                } else {
					if(isset($activityTask['ActivityTask']['overload'])&&$activityTask['ActivityTask']['overload']!=0)
					{
						$this->ActivityTask->id = $activityTask['ActivityTask']['id'];
						$_saved['overload'] = 0;
						$this->ActivityTask->save($_saved);
					}
                    $children[$key]['overload'] = 0;
                }
                //manual consumed
                $manual_consumed = isset($activityTask['ActivityTask']['manual_consumed']) ? $activityTask['ActivityTask']['manual_consumed'] : 0;
                if($manual_consumed >= $estimated){
                    if(isset($activityTask['ActivityTask']['is_previousTask']) && $activityTask['ActivityTask']['is_previousTask'] == true){
                        //do nothing
                    } else {
                        $children[$key]['manual_overload'] = $manual_consumed - $estimated;
                        $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                        $_saved['manual_overload'] = $children[$key]['manual_overload'];
                        $this->ActivityTask->save($_saved);
                    }
                } else {
                    if(isset($activityTask['ActivityTask']['manual_overload'])&&$activityTask['ActivityTask']['manual_overload']!=0)
                    {
                        $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                        $_saved['manual_overload'] = 0;
                        $this->ActivityTask->save($_saved);
                    }
                    $children[$key]['manual_overload'] = 0;
                }

                $overload = !empty($children[$key]['overload']) ? $children[$key]['overload'] : 0;

                if( $useManualConsumed ){
                    $consumed = $manual_consumed;
                    $overload = !empty($children[$key]['manual_overload']) ? $children[$key]['manual_overload'] : 0;
                }

                $remain = $estimated - $consumed;

                if($consumed < 0){
                    $children[$key]['remain'] = 'N/A';
                    $children[$key]['estimated'] = 'N/A';
                    $children[$key]['initial_estimated'] = 'N/A';
                } else {
                     if($remain < 0){
                        $children[$key]['remain'] = 0;
                    } else {
                        $children[$key]['remain'] = $remain;
                    }
                }

                if ($estimated != 0) {
                    $completed = round(($consumed * 100) / ($estimated+$overload), 2);
                } else {
                    $completed = 0;
                }
                if($completed > 100){
                    $children[$key]['completed'] = '100%';
                } elseif($completed < 0) {
                    $children[$key]['completed'] = '0.00%';
                } else {
                    $children[$key]['completed'] = $completed . '%';
                }
                $children[$key]['is_previous']      = ($activityTask['ActivityTask']['previous'] == 1) ? 'true' : 'false';
                $children[$key]['activity_id']      = $activity_id;
                $children[$key]['text_1'] = !empty($activityTask['ActivityTask']['text_1']) ? $activityTask['ActivityTask']['text_1'] : '';
                $children[$key]['text_updater'] = !empty($activityTask['ActivityTask']['text_updater']) ? $activityTask['ActivityTask']['text_updater'] : '';
                $children[$key]['text_time'] = !empty($activityTask['ActivityTask']['text_time']) ? $activityTask['ActivityTask']['text_time'] : '';
                $children[$key]['attachment'] = !empty($activityTask['ActivityTask']['attachment']) ? $activityTask['ActivityTask']['attachment'] : '';
                $children[$key]['slider'] = !empty($activityTask['ActivityTask']['slider']) ? $activityTask['ActivityTask']['slider'] : 0;
            }
        }
        // Build the root child

        $root_child = array();
        $root_child['id']           = 0;
        $root_child['task_title']   = 'Summary';
        $root_child['parent_id']    = -1;
        $root_child['leaf']         = 'false';
        $root_child['expanded']     = 'true';
        $root_child['task_start_date']   = !empty($startEndDates['task_start_date']) ? date('Y-m-d', $startEndDates['task_start_date']): '0000-00-00';
        $root_child['task_end_date']     = !empty($startEndDates['task_end_date']) ? date('Y-m-d', $startEndDates['task_end_date']): '0000-00-00';
        $root_child['initial_task_start_date']   = !empty($startEndDates['initial_task_start_date']) ? $startEndDates['initial_task_start_date']: '0000-00-00';
        $root_child['initial_task_end_date']     = !empty($startEndDates['initial_task_end_date']) ? $startEndDates['initial_task_end_date']: '0000-00-00';
        $root_child['consumed']     = 0;
        $root_child['hasUsed']       = 0;
        $root_child['wait']         = 0;
        $root_child['estimated']    = 0;
        $root_child['initial_estimated']    = 0;
        $root_child['remain']       = 0;
        $root_child['completed']    = 0;
        $root_child['duration']     = 0;
        $root_child['manual_consumed']    = 0;
        $root_child['manual_overload']     = 0;
        $children[] = $root_child;
        $children = array_merge($parent, $children);
        $children = $this->__buildTree($children);
        return $children;
    }

    public function listTasksJson($activity_id){
        $this->layout = 'ajax';
        $children = $this->_getAllActivityTasks($activity_id);
        $result = $children[0];
        $this->set(compact('result'));
    }

    public function createTaskJson($activity_id) {
        $this->loadModel('ActivityRequest');
        $this->layout = 'ajax';
        if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }

            if (!is_null($jsonData) and $jsonData !== false) {
                $this->data = $jsonData;
            }
        }
        $result = array();
        if (!empty($this->data)) {
            $data = $this->data;
			if(!isset($data['special'])||$data['special']==null)
			$data['special']=0;
            $success = true;
            if($data['parent_id'] != 0){
                $hasUsed = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array('task_id' => $data['parent_id'], 'value != 0')
                ));
                if($hasUsed != 0){
                    $success = false;
                    $result = sprintf(__('This task "%s" is in used/ has consumed', true), $data['parent_name']);
                    $data = null;
                }
				//The task is created under a task parent,
				//Default value of start date and date of task = start date and end date of task parent
				$dateTmp = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $data['parent_id']
					),
					'fields'=>array('task_start_date','task_end_date')
				));
				$startDateTmp = $dateTmp['ActivityTask']['task_start_date'];
				$endDateTmp = $dateTmp['ActivityTask']['task_end_date'];
            }
			else{
				//The task is created under a phase,
				//Default start date = min start date of task into activity
				//Default end date = max end date of task into activity
				$startDateTmp = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'activity_id' => $data['activity_id']
					),
					'fields'=>array('MIN(task_start_date) as task_start_date')
				));
				$startDateTmp = empty($startDateTmp) ? null : $startDateTmp[0]['task_start_date'];

				$endDateTmp = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'activity_id' => $data['activity_id']
					),
					'fields'=>array('MAX(task_end_date) as task_end_date')
				));
				$endDateTmp = empty($endDateTmp) ? null : $endDateTmp[0]['task_end_date'];
			}
            if($success == true){
                $data['name'] = isset($data['task_title']) ? $data['task_title'] : '';
                if (is_int(intval($data['task_priority_id']))) {
                    $data['task_priority_id'] = 0;
                } else {
                    unset($data['task_priority_id']);
                }
                if ( !$this->_hasStatus($data['task_status_id']) ) {
                    $__status = $this->_statusForCompany(true);
                    $data['task_status_id'] = $__status['id'];
                    $data['task_status_name'] = $__status['name'];
                }
                $flag = $this->ActivityTask->find('count', array(
    				'recursive' => -1,
    				'conditions' => array(
                        "ActivityTask.name" => $data['name'],
                        "ActivityTask.parent_id" => $data['parent_id'],
                        "ActivityTask.activity_id" => $activity_id
                    )
    			));
    			if($flag > 0){
                    $success = false;
                    $result = __('The task name already exists', true);
                    $data = null;
    			} else {
                    $_predecessor   = $data['predecessor'];
                    $_startDate     = ($data['task_start_date']=="") ? null : $data['task_start_date'];
                    $_endDate       = ($data['task_end_date']=="") ? null : $data['task_end_date'];
                    $_duration      = $data['duration'];
                    $_slider        = !empty($data['slider']) ? $data['slider'] : 0;
                    if(!empty($data['keep_duration'])){
                        $_endDate = null;
                    } else {
                        $_duration = null;
                    }
                    if(isset($_predecessor) && $_predecessor != 0){
                        $_startDate = $this->_createPredecessor($_startDate, $_predecessor, $_slider);
                    }
                    if(isset($_duration) && $_duration != 0){
                        if(isset($_startDate)){ // duration isset and start day is set
                            if(isset($_endDate)){ // duration isset and start day is set and enddate is set
                                $_duration = null;
                                $_duration = $this->_getWorkingDays($_startDate, $_endDate, $_duration);
                            }else{ // duration is set and start day is set and end date is not set
                                $_endDate = null;
                                $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                            }
                        }else{ // duration isset and start day is not set
                            if(isset($_endDate)){ // duration isset and start day not set and enddate is set
                                $_startDate = null;
                                $_startDate = $this->_durationStartDate($_startDate, $_endDate, $_duration);
                            }else{ // duration is set and start day is not set and end date is not set
                                // do nothing
                                $data['duration'] = null;
                            }
                        }
                    }else{
                        // duration is not set
                        if(isset($_startDate)){
                            if(isset($_endDate)){
                                $_duration = null;
                                $_duration = $this->_getWorkingDays($_startDate, $_endDate, $_duration);
                            }else{
                                // do nothing
                            }
                        }else{
                            if(isset($_endDate)){
                                // do nothing
                            }else{
                                // do nothing
                            }
                        }
                    }
                    $data['task_start_date'] = isset($_startDate) ? strtotime($_startDate) : $startDateTmp;
                    $data['task_end_date'] = isset($_endDate) ? strtotime($_endDate) : $endDateTmp;
					if($data['task_start_date'] > $data['task_end_date'])
					{
						$data['task_end_date'] = $data['task_start_date'];
					}
                    $data['duration'] = $_duration;
                    if($data['parentId'] > 999999999999){
                        $data['parentId'] = 0;
                    }
                    if($data['parent_id'] > 999999999999){
                        $data['parent_id'] = 0;
                    }
                    $data['slider'] = $_slider;
                    $data['activity_id'] = $activity_id;
                    $tmp_task_assign_to_id = $tmp_task_assign_to_text = $is_profit_center = array();
                    $is_profit_center = @$data['is_profit_center'];
                    $tmp_task_assign_to_text = @$data['task_assign_to_text'];
                    $tmp_task_assign_to_id = @$data['task_assign_to_id'];
                    unset($data['is_profit_center']);
                    unset($data['task_assign_to_id']);
                    unset($data['task_assign_to_text']);
                    unset($data['id']);
                    $this->ActivityTask->create();
                    if(!empty($data['name'])){
                        $result = $this->ActivityTask->save($data);
                    }
                    if($result){
                        $data['id'] = $this->ActivityTask->id;
                        if (!empty($tmp_task_assign_to_id)) {
                            if(is_array($tmp_task_assign_to_id) && (count($tmp_task_assign_to_id) >0)){
                                $employee = $tmp_task_assign_to_id;
                                $profitCenter = $is_profit_center;
                                $assign = $this->_saveEmployees($employee, $profitCenter, $data['id'], $data['estimated'], null, false);
                            }
                        }
                        $result['ActivityTask']['task_assign_to_id'] = $tmp_task_assign_to_id;
                        $result['ActivityTask']['task_assign_to_text'] = $tmp_task_assign_to_text;
                        $result['ActivityTask']['is_profit_center'] = $is_profit_center;
                        $result['ActivityTask']['id'] = $data['id'];
                        //$this->ActivityTask->staffingSystem($activity_id);
                        $this->_deleteCacheContextMenu();
                        $actName = $this->ActivityTask->Activity->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'id' => $activity_id
                            )
                        ));
                        $this->writeLog($result, $this->employee_info, sprintf('Create activity task `%s` under `%s`', $result['ActivityTask']['name'], $actName['Activity']['name']), $actName['Activity']['company_id']);
                    }
                }
            }
        }
        $result = array("success" => $success, "message" => $result, "data" => $data);
        $this->set(compact('result'));
    }

    public function updateTaskJson($activity_id) {
        $this->layout = 'ajax';
        if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }

            if (!is_null($jsonData) and $jsonData !== false) {
                $this->data = $jsonData;
            }
        }
        if (!empty($this->data)) {
            $data = $this->data;
			$data['special_consumed']=0;
			if(isset($data['special'])&&$data['special']==1)
			{
				$data['special_consumed']=isset($data['consumed'])?$data['consumed']:0;
				$data['consumed']=0;
			}
            $this->log($data);
            // fix long id update
            if( (isset($data['is_phase']) && ($data['is_phase'] == "true")) || (isset($data['id']) && ($data['id'] == "root")) ){
                // Skip update phase tasks
                $result = array("success" => true, "message" => "Can not update phase task");
                $this->set(compact('result'));
                return;
            }
        }
        if (!empty($this->data)) {
            $data = $this->data;
			$oldDataOfTask = $this->ActivityTask->find('first', array(
				'recursive' => -1,
				'conditions' => array('ActivityTask.id' => $data['id']),
				'fields' => array('id', 'duration')
			));
            $data['name'] = isset($data['task_title']) ? $data['task_title'] : '';
            if (is_int(intval($data['task_priority_id']))) {

            } else {
                unset($data['task_priority_id']);
            }
            if ( !$this->_hasStatus($data['task_status_id']) ) {
                $__status = $this->_statusForCompany(true);
                $data['task_status_id'] = $__status['id'];
                $data['task_status_name'] = $__status['name'];
            }

            $tmp_task_assign_to_id = $tmp_task_assign_to_text = $tmp_estimated_detail = $is_profit_center = $employee = $profitCenter = array();
            $is_profit_center = @$data['is_profit_center'];
            $tmp_task_assign_to_text = @$data['task_assign_to_text'];
            $tmp_estimated_detail = @$data['estimated_detail'];
            if($data['estimated'] == 0){
                $tmp_estimated_detail = array();
            }
            unset($data['task_assign_to_text']);

            if (!empty($data['task_assign_to_id'])) {
                $tmp_task_assign_to_id = $data['task_assign_to_id'];
                if(is_array($data['task_assign_to_id']) && (count($data['task_assign_to_id']) >0)){
                    $employee = $data['task_assign_to_id'];
                    $profitCenter = $data['is_profit_center'];
                }
            }
            $assign = $this->_saveEmployees($employee, $profitCenter, $data['id'], $data['estimated'], $tmp_estimated_detail, false);
            unset($data['task_assign_to_id']);
            unset($data['is_profit_center']);
            unset($data['estimated_detail']);

            if($data['parentId'] > 999999999999){
                $data['parentId'] = 0;
            }
            if($data['parent_id'] > 999999999999){
                $data['parent_id'] = 0;
            }

            $_predecessor   = $this->_updatePredeces($data['id'], $data['predecessor']);
            $_startDate     = !empty($data['task_start_date']) && ($data['task_start_date'] > 0) ? $data['task_start_date'] : null;
            $_endDate       = !empty($data['task_end_date']) && ($data['task_end_date'] > 0) ? $data['task_end_date'] : null;

            $_duration      = $data['duration'];
            $_slider = !empty($data['slider']) ? $data['slider'] : 0;
            $_taskId        = $data['id'];
            if(!empty($data['keep_duration'])){
                $_endDate = null;
            } else {
                $_duration = null;
            }
            if(isset($_duration) && $_duration != 0){
                if(isset($_startDate)){
                    if(isset($_endDate)){
                        if($oldDataOfTask['ActivityTask']['duration'] == $_duration){
                            $_duration = null;
                            $_duration = $this->_getWorkingDays($_startDate, $_endDate, $_duration);
                        } else {
                            $_endDate = null;
                            $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                        }
                    }else{
                        $_endDate = null;
                        $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                    }
                }else{
                    if(isset($_endDate)){
                        $_startDate = null;
                        $_startDate = $this->_durationStartDate($_startDate, $_endDate, $_duration);
                    }else{
                        $data['duration'] = null;
                    }
                }
            }else{
                if(isset($_startDate)){
                    if(isset($_endDate)){
                        $_duration = null;
                        $_duration = $this->_getWorkingDays($_startDate, $_endDate, $_duration);
                    }else{
                        // do nothing
                    }
                }else{
                    if(isset($_endDate)){
                        // do nothing
                    }else{
                        // do nothing
                    }
                }
            }

            if(isset($_predecessor) && $_predecessor != 0){
                //if(!empty($oldDataOfTask['ActivityTask']['predecessor']) && $oldDataOfTask['ActivityTask']['predecessor'] == $_predecessor){
//                    //do nothing
//                } else {
//                    $_startDate = $this->_predecessor($_startDate, $_predecessor, $_taskId);
//                }
                $_startDate = $this->_predecessor($_startDate, $_predecessor, $_taskId, $_slider);
                if(!empty($data['keep_duration'])){
                    $_endDate = null;
                } else {
                    $_duration = null;
                }
                $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                //$_startDate = $this->_predecessor($_startDate, $_predecessor, $_taskId);
            }
            $data['predecessor'] = $_predecessor;
            $data['task_start_date'] = isset($_startDate) ? strtotime($_startDate) : '';
            $data['task_end_date'] = isset($_endDate) ? strtotime($_endDate) : '';
            $data['duration'] = $_duration;
            $data['slider'] = $_slider;
            if(isset($data['consumed']) && $data['consumed'] > $data['estimated']){
                $data['overload'] = $data['consumed'] - $data['estimated'];
            } else {
                $data['overload'] = 0;
            }
            if(!empty($data['name'])){
                $result = $this->ActivityTask->save($data);
            }
            if($result){
                $result['ActivityTask']['task_assign_to_id'] = $tmp_task_assign_to_id;
                $result['ActivityTask']['task_assign_to_text'] = $tmp_task_assign_to_text;
                $result['ActivityTask']['is_profit_center'] = $is_profit_center;
                $result['ActivityTask']['estimated_detail'] = $tmp_estimated_detail;
                $this->_updateParentTask($activity_id);
                //$this->ActivityTask->staffingSystem($activity_id);
                $this->_deleteCacheContextMenu();
                $actName = $this->ActivityTask->Activity->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'id' => $activity_id
                    )
                ));
                $this->writeLog($result, $this->employee_info, sprintf('Update activity task `%s` under `%s`', $result['ActivityTask']['name'], $actName['Activity']['name']), $actName['Activity']['company_id']);
            }

            $result = array("success" => true, "message" => $result);

            $this->set(compact('result'));
        }
    }
	public function staffingWhenUpdateTask($activity_id = null)
	{
	    set_time_limit(0);
		$this->layout = 'ajax';
		if($activity_id != null)	$this->ActivityTask->staffingSystem($activity_id);
		exit;
	}
    /**
     * Xoa task
     */
    public function destroyTaskJson($id = null) {
        $this->loadModel('ActivityRequest');
        $this->layout = 'ajax';
        $success = true;
        if(!empty($id)){
            /**
             * Lay tak duoc chon va cac task con
             */
            $allATasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'ActivityTask.id' => $id,
                        'ActivityTask.parent_id' => $id
                    )
                ),
                'fields' => array('id', 'name', 'activity_id')
            ));
            $allTasks = Set::classicExtract($allATasks, '{n}.ActivityTask.id');
            $hasUsed = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array('task_id' => $allTasks, 'value != 0')
            ));
            if($hasUsed != 0){
                $success = false;
                $result = sprintf(__('This task "%s" or its sub-tasks is in used/ has consumed', true), $this->data['task_title']);
                $data = null;
            } else {
                /**
                 * Xoa cac task duoc chon va cac task con
                 */
                if($this->ActivityTask->deleteAll(array('ActivityTask.id' => $allTasks), false)){
                    /**
                     * Xoa cac assign.
                     */
                    $list = array();
                    $activity_id = 0;
                    foreach($allATasks as $t){
                        $list[] = $t['ActivityTask']['name'];
                        $activity_id = $t['ActivityTask']['activity_id'];
                    }
                    $actName = $this->ActivityTask->Activity->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $activity_id
                        )
                    ));
                    $this->loadModel('ActivityTaskEmployeeRefer');
                    $this->loadModel('NctWorkload');
                    $this->NctWorkload->deleteAll(array('NctWorkload.activity_task_id' => $allTasks), false);
                    $this->ActivityTaskEmployeeRefer->deleteAll(array('ActivityTaskEmployeeRefer.activity_task_id' => $allTasks), false);
                    $this->writeLog(null, $this->employee_info, sprintf('Delete activity task `%s` under `%s`', implode('`, `', $list), $actName['Activity']['name']), $actName['Activity']['company_id']);
                }
                //$this->ActivityTask->staffingSystem($this->data['activity_id']);
                $this->_deleteCacheContextMenu();
                $success = $result = true;
            }
        }
        $result = array("success" => $success, "message" => $result);
        $this->set(compact('result'));
    }

    public function saveEstimatedDetail(){
        //$this->log("ActivityTasksController->saveEstimatedDetail()");

        $this->layout = 'ajax';
        $this->loadModel('ActivityTaskEmployeeRefer');
        if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }

            if (!is_null($jsonData) and $jsonData !== false) {
                $this->data = $jsonData;
            }
        }
        if(isset($_POST['Reference'])){
            $tasksId = $_POST['ProjectTaskId'];
            $totalEstimated = $_POST['totalEstimated'];
            $estimateds = $_POST['Reference'];
            $referenceIds = array_keys($estimateds);
            foreach($referenceIds as $referenceId){
                $split = explode('_', $referenceId);
                 $taskReferences = $this->ActivityTaskEmployeeRefer->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('activity_task_id' => $tasksId, 'reference_id' => $split[0], 'is_profit_center' => $split[1]),
                    'fields' => array('id', 'reference_id', 'is_profit_center')
                ));
                $datas[] = $taskReferences;
            }
            foreach($datas as $key => $data){
                $this->ActivityTaskEmployeeRefer->id = $data['ActivityTaskEmployeeRefer']['id'];
                $ids = $data['ActivityTaskEmployeeRefer']['reference_id'] .'_'. $data['ActivityTaskEmployeeRefer']['is_profit_center'];
                $_data['estimated'] = $estimateds[$ids];
                $_result[] = $this->ActivityTaskEmployeeRefer->save($_data);
            }
        }
        $result = $_result;
        $result = array("success" => true, "message" => $result, "total" => $totalEstimated);
        $this->set(compact('result'));
    }

    private function _hasStatus($sid){
        if( !$sid )return 0;
        $this->loadModel('ProjectStatus');
        $company_id = $this->employee_info['Company']['id'];
        return $this->ProjectStatus->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectStatus.id' => $sid, 'company_id' => $company_id)
        ));
    }

    /** *******************************************************END HANDLE TASK********************************************************************/

    /** *******************************************************HANDLE STAFFING+ ********************************************************************/
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

    private function _getWorkloadActivity($activity_id = null){
        $this->loadModel('ActivityTaskEmployeeRefer');
        $activityTasks = $this->ActivityTask->find('all', array(
                'conditions' => array(
                        'ActivityTask.activity_id' => $activity_id
                    ),
                'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload')
            ));
        $endDatas = array();
        if(!empty($activityTasks)){
            $parentIds = array_unique(Set::classicExtract($activityTasks, '{n}.ActivityTask.parent_id'));
            $startDates = $endDates = array();
            foreach($activityTasks as $key => $activityTask){
                foreach($parentIds as $parentId){
                    if($activityTask['ActivityTask']['id'] == $parentId){
                        unset($activityTasks[$key]);
                    }
                }
                if(!empty($activityTask['ActivityTask']['task_start_date'])){
                    $startDates[] = $activityTask['ActivityTask']['task_start_date'];
                }
                if(!empty($activityTask['ActivityTask']['task_end_date'])){
                    $endDates[] = $activityTask['ActivityTask']['task_end_date'];
                }
            }
            $startDates = !empty($startDates) ? min($startDates): 0;
            if($startDates >= time()){
                $startDates = time();
            }
            $endDates = !empty($endDates) ? max($endDates): 0;
            if($endDates <= time()){
                $endDates = time();
            }
            $datas = array();
            foreach($activityTasks as $key => $activityTask){
                $datas[$key]['task_start_date'] = !empty($activityTask['ActivityTask']['task_start_date']) ? $activityTask['ActivityTask']['task_start_date'] : $startDates;
                $datas[$key]['task_end_date'] = !empty($activityTask['ActivityTask']['task_end_date']) ? $activityTask['ActivityTask']['task_end_date'] : $endDates;
                $workload = !empty($activityTask['ActivityTask']['estimated']) ? $activityTask['ActivityTask']['estimated'] : 0;
                $overload = !empty($activityTask['ActivityTask']['overload']) ? $activityTask['ActivityTask']['overload'] : 0;
                $datas[$key]['estimated'] = $workload + $overload;
            }
            $resetDatas = array();
            if(!empty($datas)){
                foreach($datas as $id => $data){
                        $minMonth = !empty($data['task_start_date']) ? date('m', $data['task_start_date']) : date('m', time());
                        $minYear = !empty($data['task_start_date']) ? date('Y', $data['task_start_date']) : date('Y', time());

                        $maxMonth = !empty($data['task_end_date']) ? date('m', $data['task_end_date']) : date('m', time());
                        $maxYear = !empty($data['task_end_date']) ? date('Y', $data['task_end_date']) : date('Y', time());
                        $diffDate = $this->_diffDate($data['task_start_date'], $data['task_end_date']);
                        if($diffDate == 0){
                            $estis = $data['estimated'];
                        } else {
                            $estis = $data['estimated']/$diffDate;
                        }
                        $rDatas = array();
                        while($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                            $resetDatas[$id][strtotime('01-'. $minMonth .'-'. $minYear)]['estimated'] = $estis;
                            $minMonth++;
                            if ($minMonth == 13) {
                                $minMonth = 1;
                                $minYear++;
                            }
                        }
                }
            }
            if(!empty($resetDatas)){
                $setArrayTimes = array();
                foreach($resetDatas as $resetData){
                    foreach($resetData as $time => $vl){
                        $setArrayTimes[] = $time;
                    }
                }
                $setArrayTimes = !empty($setArrayTimes) ? array_unique($setArrayTimes) : array();
                if(!empty($setArrayTimes)){
                    foreach($setArrayTimes as $setArrayTime){
                        foreach($resetDatas as $resetData){
                            foreach($resetData as $time => $vl){
                                if($setArrayTime == $time){
                                    $endDatas[$setArrayTime]['date'] = $setArrayTime;
                                    $endDatas[$setArrayTime]['estimated'][] = $vl['estimated'];
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($endDatas)){
                foreach($endDatas as $time => $endData){
                    $est = array_sum($endData['estimated']);
                    $endDatas[$time]['estimated'] = $est;
                }
            }
        }
        return $endDatas;
    }

    private function _getConsumedActivity($activity_id = null){
        $this->loadModel('ActivityRequest');

        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id')
        ));
        $consumed = $consumedPrevious = array();
        if(!empty($activityTasks)){
            $taskRequests = $this->ActivityRequest->find('all',array(
                'recursive'     => -1,
                'fields'        => array('id', 'date', 'value'),
                'conditions'    => array(
                    'status'        => 2,
                    'task_id'       => $activityTasks,
                    'company_id'    => $this->employee_info['Company']['id'],
                    'NOT'           => array('value' => 0, "task_id" => null),
                )
            ));
            $groupTimes = array();
            if(!empty($taskRequests)){
                $groupTimes = array_unique(Set::classicExtract($taskRequests, '{n}.ActivityRequest.date'));
                if(!empty($groupTimes)){
                    foreach($groupTimes as $groupTime){
                        $_groupTimes[] = date('m-Y', $groupTime);
                    }
                    $groupTimes = array_unique($_groupTimes);
                }
            }
            if(!empty($groupTimes)){
                foreach($groupTimes as $groupTime){
                    $total = 0;
                    foreach($taskRequests as $taskRequest){
                        if($groupTime == date('m-Y', $taskRequest['ActivityRequest']['date'])){
                            $total += $taskRequest['ActivityRequest']['value'];
                            $consumed[strtotime('01-'.$groupTime)]['date'] = strtotime('01-'.$groupTime);
                            $consumed[strtotime('01-'.$groupTime)]['consumed'] = $total;
                        }
                    }
                }
            }
        }

        $previous = $this->ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array('id', 'date', 'value'),
            'conditions'    => array(
                'status'        => 2,
                'activity_id'   => $activity_id,
                'company_id'    => $this->employee_info['Company']['id'],
                'NOT'           => array('value' => 0),
            )
        ));

        if(!empty($previous)){
            $groupTimePrevious = array();
            $groupTimePrevious = array_unique(Set::classicExtract($previous, '{n}.ActivityRequest.date'));
            if(!empty($groupTimePrevious)){
                foreach($groupTimePrevious as $groupTimePreviou){
                    $_groupTimePrevious[] = date('m-Y', $groupTimePreviou);
                }
                $groupTimePrevious = array_unique($_groupTimePrevious);
            }
            if(!empty($groupTimePrevious)){
                foreach($groupTimePrevious as $groupTimePreviou){
                    $total = 0;
                    foreach($previous as $previou){
                        if($groupTimePreviou == date('m-Y', $previou['ActivityRequest']['date'])){
                            $total += $previou['ActivityRequest']['value'];
                            $consumedPrevious[strtotime('01-'.$groupTimePreviou)]['date'] = strtotime('01-'.$groupTimePreviou);
                            $consumedPrevious[strtotime('01-'.$groupTimePreviou)]['consumed'] = $total;
                        }
                    }
                }
            }
        }
        $results['cons'] = $consumed;
        $results['pres'] = $consumedPrevious;

        return $results;
     }

     private function _mergeData($consumeds, $previous, $estimateds){
        $dates = array();
        $dateCons = !empty($consumeds) ? Set::classicExtract($consumeds, '{n}.date') : array();
        $datePres = !empty($previous) ? Set::classicExtract($previous, '{n}.date') : array();
        $dateEtis = !empty($estimateds) ? Set::classicExtract($estimateds, '{n}.date') : array();
        $dates = array_merge($dateCons, $datePres, $dateEtis);
        $dates = !empty($dates) ? array_unique($dates) : array();
        $minDates = !empty($dates) ? min($dates) : time();
        $maxDates = !empty($dates) ? max($dates) : time();
        if($minDates > time()) $minDates = time();
        if($maxDates < time()) $maxDates = time();
        $datas = array();
        if(!empty($minDates) && !empty($maxDates)){
            $minMonth = date('m', $minDates);
            $minYear = date('Y', $minDates);

            $maxMonth = date('m', $maxDates);
            $maxYear = date('Y', $maxDates);
            while ($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                $datas[strtotime('01-'. $minMonth . '-' . $minYear)]['date'] = strtotime('01-'. $minMonth . '-' . $minYear);
                $datas[strtotime('01-'. $minMonth . '-' . $minYear)]['estimation'] = 0;
                $cons = isset($consumeds[strtotime('01-'. $minMonth . '-' . $minYear)]) ? $consumeds[strtotime('01-'. $minMonth . '-' . $minYear)]['consumed'] : 0;
                $conPrs = isset($previous[strtotime('01-'. $minMonth . '-' . $minYear)]) ? $previous[strtotime('01-'. $minMonth . '-' . $minYear)]['consumed'] : 0;
                $datas[strtotime('01-'. $minMonth . '-' . $minYear)]['consumed'] = $cons + $conPrs;
                $workload = isset($estimateds[strtotime('01-'. $minMonth . '-' . $minYear)]) ? $estimateds[strtotime('01-'. $minMonth . '-' . $minYear)]['estimated'] : 0;
                $datas[strtotime('01-'. $minMonth . '-' . $minYear)]['validated'] = $workload + $conPrs;
                $datas[strtotime('01-'. $minMonth . '-' . $minYear)]['remains'] = $workload - $cons;
                $minMonth++;
                if ($minMonth == 13) {
                    $minMonth = 1;
                    $minYear++;
                }
            }
        }
        $currentDate = strtotime(date('01-m-Y', time()));
        if(!empty($datas)){
            $_total = $monthValidates = array();
            $works = $cons = $remains = 0;
            foreach($datas as $time => $data){
                if($time < $currentDate){
                    $works += $data['validated'];
                    $cons += $data['consumed'];
                    $remains += $data['remains'];
                    $_total['validated'] = $works;
                    $_total['consumed'] = $cons;
                    $_total['remain'] = $remains;
                    $datas[$time]['remains'] = 0;
                } else {
                    if(!empty($data['validated'])){
                        $monthValidates[] = $time;
                    }
                }
            }
            foreach($datas as $time => $data){
                if($time < $currentDate){
                    $datas[$time]['remains'] = 0;
                } else {
                    $count = !empty($monthValidates) ? count($monthValidates) : 1;
                    $remain = !empty($_total) ? $_total['remain'] : 0;
                    if($count == 0){
                        $_remain = 0;
                    } else {
                        $_remain = $remain/$count;
                    }
                    if(!empty($data['validated']) || $time == $currentDate){
                        $datas[$time]['remains'] = $data['remains'] + $_remain;
                    } else {
                        if(!empty($datas[$currentDate])){
                            if($datas[$currentDate]['validated'] && $datas[$currentDate]['validated'] == 0){
                                $datas[$currentDate]['remains'] = $_remain;
                            }
                        }
                    }
                }
            }
        }

        return $datas;
     }

     private function _mergeActivity($activity_id = null){
        $_getConsumeds = $this->_getConsumedActivity($activity_id);
        $consumeds = $_getConsumeds['cons'];
        $previous = $_getConsumeds['pres'];
        $estimateds = $this->_getWorkloadActivity($activity_id);

        $datas[$activity_id] = $this->_mergeData($consumeds, $previous, $estimateds);

        return $datas;
     }

     private function _getProfitCenterFollowParent($profitCenters = array(), $results = array()){
        $this->loadModel('ProfitCenter');
        if(!empty($profitCenters)){
            $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProfitCenter.parent_id' => $profitCenters),
                'fields' => array('id', 'id')
            ));
            if(!empty($profitCenters)){
                $results = array_merge($results, $profitCenters);
                $results = $this->_getProfitCenterFollowParent($profitCenters, $results);
            }
        }
        return $results;
     }

     public function visions_staffing() {
        // debug($this->params);exit; die; return;

        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            if( !((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))){
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : 'activity_tasks';
                $_controller_preview =  trim( str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_').'_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'visions_staffing';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                if( isset($_url_param['url'])) unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    '?' => $_url_param,

                ));
            }
        }
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '256M');
		$listDays = array();
        $this->loadModel('Activity');
		$this->loadModel('ProfitCenter');
        $employeeName = $this->_getEmployee();
		$filterByProfile = false ;
        $budgetTeam = isset($this->companyConfigs['budget_team']) && !empty($this->companyConfigs['budget_team']) ?  true : false;
		if($this->params['url']['type'] == 5){
			$filterByProfile = true ;
			if(isset($this->params['url']['aPC'])){
				unset($this->params['url']['aPC']);
			}
			if(isset($this->params['url']['aEmployee'])){
				unset($this->params['url']['aEmployee']);
			}
		}
		$arrGetUrl=$this->params['url'];
        if( isset( $arrGetUrl['aPC'])){
            foreach( $arrGetUrl['aPC'] as $key => $val){
                if( !$val) unset($arrGetUrl['aPC'][$key]);
            }
        }
		if(!isset($arrGetUrl['ajax'])){
			unset($_SESSION['graph']);
			unset($_SESSION['graphMonth']);
		}
        $typeUrl = array(
            'type' => '',
            'aStartMonth' => '',
            'aStartYear' => '',
            'aEndMonth' => '',
            'aEndYear' => '',
            'selectPCAll' => ''
        );
        $filterType = array_intersect_key(array_merge($typeUrl, $this->params['url']), $typeUrl);
		$dateType = $arrGetUrl['aDateType'];
		if($dateType == 1)
		  $dateType = 'day';
		elseif($dateType == 2)
		  $dateType = 'week';
		else
		  $dateType = 'month';
		$this->Session->write('MonthMultiple', array());
		if($dateType == 'day' || $dateType == 'week'){
			$endDateCheck = strtotime('+6 months',strtotime($arrGetUrl['aStartDate'])); //start date + 3 month
			$_endDateFilterTmp = strtotime($arrGetUrl['aEndDate']);
			if($_endDateFilterTmp > $endDateCheck){
				$_endDateFilterTmp = $endDateCheck;
				$arrGetUrl['aEndDate'] = date('d-m-Y',$endDateCheck);
			}
			$startDateFilterSummary = strtotime($arrGetUrl['aStartDate']);
			$endDateFilterSummary = strtotime($arrGetUrl['aEndMonth']);
			$arr = explode('-',$arrGetUrl['aStartDate']);
			$dayStartDate = $arr[0];
			$filterType['aStartMonth'] = $arr[1];
			$filterType['aStartYear'] = $arr[2];
			$arr = explode('-',$arrGetUrl['aEndDate']);
			$dayEndDate = $arr[0];
			$filterType['aEndMonth'] = $arr[1];
			$filterType['aEndYear'] = $arr[2];
			$lib = new LibBehavior();
			$endDayTmp = cal_days_in_month(CAL_GREGORIAN, $filterType['aEndMonth'], $filterType['aEndYear']);
			$startDateFilter = strtotime('01-'.$filterType['aStartMonth'].'-'.$filterType['aStartYear']);
			$endDateFilter = strtotime($endDayTmp.'-'.$filterType['aEndMonth'].'-'.$filterType['aEndYear']);
			$listDays = $lib->getListWorkingDays($startDateFilter,$endDateFilter,true);
		} else {
			$startDateFilter = strtotime('01-'.$filterType['aStartMonth'].'-'.$filterType['aStartYear']);
			$endDateFilter = strtotime('01-'.$filterType['aEndMonth'].'-'.$filterType['aEndYear']);
			$showMonthMultiple = false;
			$listMonthPeriod = array();
		}
		$listWeeks = array();
		if($dateType == 'week') {
			$_startDateTmp = $startDateFilter;
			$_startDateFilterTmp = strtotime($arrGetUrl['aStartDate']);
			$_startDateFilterTmpBetweenWeek = $_startDateFilterTmp+86400*7;
			if($_startDateFilterTmpBetweenWeek > $_endDateFilterTmp){
				$_endDateFilterTmp = $_startDateFilterTmpBetweenWeek;
			}
			$i = 0;
			$listDaysTmp = $listDays;
			$listDaysTmp = array_values($listDaysTmp);
			while($_startDateTmp <= $_endDateFilterTmp){
				$keyWeek = $lib->getDateByWeekday($_startDateTmp); //key week = first day of month
				$_startDateTmp = $keyWeek;
				if(isset($listWeeks[$keyWeek]) || $keyWeek < $_startDateFilterTmp ){
					$_startDateTmp = isset($listDaysTmp[$i]) ? $listDaysTmp[$i] : $_endDateFilterTmp+1;
					$i++;
					continue;
				} else {
					$listWeeks[$keyWeek] = $keyWeek;
				}
				$_startDateTmp = isset($listDaysTmp[$i]) ? $listDaysTmp[$i] : $_endDateFilterTmp+1;
				$i++;
			}
		}
        $type = (int) $filterType['type'];
        $default = array();
        $conditions = array();
        $isCheck = false;
        $isConditions = array();
        $newDataStaffings = array();
        $treeShowPc = array();
        $_conditionBudgetCustomers = array();
		if(!empty($arrGetUrl['aName'])){
			$activitiesFilter = $arrGetUrl['aName']; //CHECK IF FILTER BY ACTIVITY
		}
        switch($type){
            case 1:{
                $default = array(
                    'aPC' => ''
                );
                $_conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);
                $keys = array(
                    'profit_center_id'
                );
                $_conditions = Set::filter(array_combine($keys, $_conditions));
                if(!empty($_conditions['profit_center_id'])){
                    $treeShowPc = $_conditions['profit_center_id'];
                } else {
                    $treeShowPc = ClassRegistry::init('ProfitCenter')->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $employeeName['company_id']),
                        'fields' => array('id', 'id')
                    ));
                }
                $setConditions = array();
                if(!empty($_conditions['profit_center_id'])){
                    $setConditions['Activity.id'] = $this->_getActivityFromPC($_conditions['profit_center_id']);
                }
                $_conditions['profit_center_id'][] = $treeShowPc[] = '999999999';
                $_defaultBudgetCustomer = array(
                    'aCustomer' => ''
                );
                $_conditionBudgetCustomers = array_intersect_key(array_merge($_defaultBudgetCustomer, $this->params['url']), $_defaultBudgetCustomer);
                $_keyBudgetCustomers = array(
                    'budget_customer_id'
                );
                $_conditionBudgetCustomers = Set::filter(array_combine($_keyBudgetCustomers, $_conditionBudgetCustomers));
                $addDefault = array(
                    'aActivated' => '',
                    'aFamily' => '',
                    'aSub' => ''
                );
                $addConditions = array_intersect_key(array_merge($addDefault, $this->params['url']), $addDefault);
                $addKeys = array(
                    'activated',
                    'family_id',
                    'subfamily_id'
                );
                $addConditions = Set::filter(array_combine($addKeys, $addConditions));
                $conditions = array_merge($setConditions, $addConditions);
                if(empty($_conditions) && empty($addConditions)){
                    $conditions = array();
                }
                break;
            }
            case 5:{
                $_default = array(
                    'aPC' => '',
                    'aEmployee' => ''
                );
                $_conditions = array_intersect_key(array_merge($_default, $this->params['url']), $_default);
                $_keys = array(
                    'profit_center_id',
                    'employee_id'
                );
                $_conditions = Set::filter(array_combine($_keys, $_conditions));
                //condition budget customer
                $_defaultBudgetCustomer = array(
                    'aCustomer' => ''
                );
                $_conditionBudgetCustomers = array_intersect_key(array_merge($_defaultBudgetCustomer, $this->params['url']), $_defaultBudgetCustomer);
                $_keyBudgetCustomers = array(
                    'budget_customer_id'
                );
                $_conditionBudgetCustomers = Set::filter(array_combine($_keyBudgetCustomers, $_conditionBudgetCustomers));
                // end condition
                $_datas = array();
                $isProfitCenter = $isEmployee = false;
                if(!empty($_conditions)){
                    $_getDatas = $setIds = $_setIds = array();
                    if(!empty($_conditions['employee_id'])){
                        $isEmployee = true;
                        $_datas = $this->_getActivityFromResource($_conditions['employee_id']);
                    } else {
						if(!empty($_conditions['profit_center_id'])){
						  $_datas = $this->_getActivityFromPC($_conditions['profit_center_id']);
                        }
					}
                }
                if($isEmployee == true){
                    $isConditions = !empty($_conditions['employee_id']) ? $_conditions['employee_id'] : array();
                    $isCheck = 1; // co employee
                } else{
                    if($isProfitCenter == true){
                        $isConditions = !empty($_conditions['profit_center_id']) ? $_conditions['profit_center_id'] : array();
                        $isCheck = 2; //co profit center
                    }
                }
                $default = array(
                    'aFamily' => '',
                    'aSub' => ''
                );
                $conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);
                $keys = array(
                    'family_id',
                    'subfamily_id'
                );
                $conditions = Set::filter(array_combine($keys, $conditions));
                $mergeIds = array();
                if(!empty($_datas)){
                    if(!empty($conditions['Activity.id'])){
                        $mergeIds = array_merge($conditions['Activity.id'], $_datas);
                    } else {
                        $mergeIds = $_datas;
                    }
                }
                if(!empty($mergeIds)){
                   $conditions['Activity.id'] = $mergeIds;
                }
                break;
            }
            case 0:
            default:{
                $_default = array(
                    'aPC' => '',
                    'aEmployee' => ''
                );
                $_conditions = array_intersect_key(array_merge($_default, $this->params['url']), $_default);
                $_keys = array(
                    'profit_center_id',
                    'employee_id'
                );
                $_conditions = Set::filter(array_combine($_keys, $_conditions));
                $_defaultBudgetCustomer = array(
                    'aCustomer' => ''
                );
                $_conditionBudgetCustomers = array_intersect_key(array_merge($_defaultBudgetCustomer, $this->params['url']), $_defaultBudgetCustomer);
                $_keyBudgetCustomers = array(
                    'budget_customer_id'
                );
                $_conditionBudgetCustomers = Set::filter(array_combine($_keyBudgetCustomers, $_conditionBudgetCustomers));
                $_datas = array();
                $isProfitCenter = $isEmployee = false;
                if(!empty($_conditions)){
                    $_getDatas = $setIds = $_setIds = array();
                    if(!empty($_conditions['employee_id'])){
                        $isEmployee = true;
                        $_datas = $this->_getActivityFromResource($_conditions['employee_id']);
                    } else {
						if(!empty($_conditions['profit_center_id'])){
						  $_datas = $this->_getActivityFromPC($_conditions['profit_center_id']);
                        }
					}
                }
				if(isset($arrGetUrl['ajax'])&&$arrGetUrl['ajax']){
					if(isset($arrGetUrl['getActivity'])&&$arrGetUrl['getActivity']) {
						$_conditions['profit_center_id']=array();
						$_conditions['profit_center_id'][]=$arrGetUrl['ItMe'];
						$pathOfPCAjax = ClassRegistry::init('ProfitCenter')->children($arrGetUrl['ItMe']);
						$_conditions['profit_center_id'] = array_merge($_conditions['profit_center_id'],Set::classicExtract($pathOfPCAjax,'{n}.ProfitCenter.id'));
						$getPCAjax=$_conditions['profit_center_id'];
						$isEmployee=false;
					} elseif(isset($arrGetUrl['getResource'])&&$arrGetUrl['getResource']) {
						$getEmployeesAjax = array();
						$getEmployeesAjax = ClassRegistry::init('TmpStaffingSystem')->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'TmpStaffingSystem.activity_id' => $arrGetUrl['ItMe'],
                                'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                                'model' => 'employee',
                                'TmpStaffingSystem.company_id' => $employeeName['company_id']
                            ),
							'fields'=>array('model_id','model_id'),
                        ));
						$_conditions['employee_id']=array_values($getEmployeesAjax);
						if(!empty($arrGetUrl['aPC'])){
							$_getEmployeesAjax = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
									'recursive' => -1,
									'conditions' => array('profit_center_id'=>$arrGetUrl['aPC']),
									'fields' => array('employee_id', 'employee_id')
							));
							$_getEmployeesAjax = array_values($_getEmployeesAjax);
							$_conditions['employee_id'] = array_intersect($_conditions['employee_id'],$_getEmployeesAjax);
							$getEmployeesAjax = $_conditions['employee_id'];
						}
						$isEmployee=true;
					} else {
						// $_managerPC= ClassRegistry::init('ProfitCenter')->find('first', array(
						// 		'recursive' => -1,
						// 		'conditions' => array('ProfitCenter.id'=>$arrGetUrl['ItMe']),
						// 		'fields' => array('manager_id')
						// ));
						$getEmployeesAjax=array();
						// if($_managerPC['ProfitCenter']['manager_id']!='')
						// {
						// 	$getEmployeesAjax[] = $_managerPC['ProfitCenter']['manager_id'];
						// }
						$this->loadModel('Employee');
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
						unset(ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->virtualFields['end_date']);
						$_getEmployeesAjax = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
								//'recursive' => -1,
								'joins' => $_joins,
								'conditions' => array(
									'NOT' => array(
										'OR' => array(
											array(
												'UNIX_TIMESTAMP(Employee.end_date) < '=> $startDateFilter,
												'Employee.end_date <>' => '0000-00-00',
												'Employee.end_date IS NOT NULL'
											),
											array(
												'UNIX_TIMESTAMP(Employee.start_date) > '=> $endDateFilter,
												'Employee.start_date <>' => '0000-00-00',
												'Employee.start_date IS NOT NULL'
											)
										)
									),
									'ProjectEmployeeProfitFunctionRefer.profit_center_id'=>$arrGetUrl['ItMe']
								),
								'fields' => array('employee_id', 'employee_id'),
						));
						$getEmployeesAjax=array_values(array_unique(array_merge($getEmployeesAjax,$_getEmployeesAjax)));
						$_conditions['employee_id']=$getEmployeesAjax;
						$isEmployee=true;
					}
				}
                if($isEmployee == true){
                    $isConditions = !empty($_conditions['employee_id']) ? $_conditions['employee_id'] : array();
                    $isCheck = 1; // co employee
                } else{
                    if($isProfitCenter == true){
                        $isConditions = !empty($_conditions['profit_center_id']) ? $_conditions['profit_center_id'] : array();
                        $isCheck = 2; //co profit center
                    }
                }
                $default = array(
                    'aActivated' => '',
                    'aName' => '',
                    'aFamily' => '',
                    'aSub' => ''
                );
                $conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);
                $keys = array(
                    'Activity.activated',
                    'Activity.id',
                    'Activity.family_id',
                    'Activity.subfamily_id'
                );
                $conditions = Set::filter(array_combine($keys, $conditions));
                $mergeIds = array();
                if(!empty($_datas)){
                    if(!empty($conditions['Activity.id'])){
                        $mergeIds = array_merge($conditions['Activity.id'], $_datas);
                    } else {
                        $mergeIds = $_datas;
                    }
                }

                if(!empty($mergeIds)){
                   $conditions['Activity.id'] = $mergeIds;
                }
                break;
            }
        }

        // debug($conditions); 
		$conditions = array_merge($conditions,$_conditionBudgetCustomers); //ADD CONDITIONS BUGET CUSTOMER
        // debug($conditions); exit;

        if(isset($filterType['selectPCAll']) && $filterType['selectPCAll'] === 'true'){
            unset($conditions['Activity.id']);
        }
		if(isset($activitiesFilter)){
			$conditions['Activity.id'] = $activitiesFilter;
		}
		$onlyEmployee = false;
		$conditions['Activity.company_id'] = $employeeName['company_id'];
        if($type==0){
			if(isset($arrGetUrl['getResource'])&&$arrGetUrl['getResource']){
				$conditions['Activity.id'] = $arrGetUrl['ItMe'];
				$onlyEmployee = true;
			}
            $args = array(
                'recursive' => -1,
                'order' => array('family_id','subfamily_id','id'),
                'conditions' => $conditions,
                'fields' => array('Activity.id', 'Activity.name', 'Activity.family_id','Activity.subfamily_id', 'Activity.pms')//MODIFY BY VINGUYEN 01/08/2014
            );
		} else {
			$args = array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('Activity.id', 'Activity.name', 'Activity.family_id','Activity.subfamily_id', 'Activity.pms')//MODIFY BY VINGUYEN 01/08/2014
            );
		}
        //filter by priority
        if( !empty($this->params['url']['priority']) ){
            $priorities = explode(',', $this->params['url']['priority']);
        }
        if( !empty($priorities) ){
            $args['joins'] = array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'type' => 'RIGHT',
                    'conditions' => array(
                        'Project.activity_id = Activity.id',
                        'Project.activity_id IS NOT NULL'
                    )
                )
            );
            $args['conditions']['Project.project_priority_id'] = $priorities;
        }
        if((isset($args['conditions']['id']) && empty($args['conditions']['id'])) || (isset($args['conditions']['id'][0]) && empty($args['conditions']['id'][0]) && count($args['conditions']['id']) == 1)){
            unset($args['conditions']['id']);
        }
        $activities = $this->Activity->find('all', $args);

        $staffings = array();
        if(!empty($activities)){
            //$activities[] = array(
//                'Activity' => array(
//                    'id' => 0,
//                    'name' => 0,
//                    'family_id' => 0,
//                    'subfamily_id' => 0,
//                    'pms' => 0
//                )
//            );
            $activityIds = !empty($activities) ? Set::classicExtract($activities, '{n}.Activity.id') : array();
            $activityTasks = $this->ActivityTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'activity_id' => $activityIds
                    ),
                    'fields' => array('activity_id', 'MIN(task_start_date) as minDate', 'MAX(task_end_date) as maxDate'),
                    'group' => array('activity_id')
                ));
            if(!empty($activityTasks)){
                $activityTasks = Set::combine($activityTasks, '{n}.ActivityTask.activity_id', '{n}.0');
            }
            foreach($activities as $key => $activitie){
                $activities[$key]['Activity']['start_date'] = !empty($activityTasks[$activitie['Activity']['id']]) ? $activityTasks[$activitie['Activity']['id']]['minDate'] : time();
                $activities[$key]['Activity']['end_date'] = !empty($activityTasks[$activitie['Activity']['id']]) ? $activityTasks[$activitie['Activity']['id']]['maxDate'] : time();
            }
            $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
            //loc key employee_id
            $_filterEmployee = array(
                'aPC' => '',
                'aEmployee' => ''
            );
            $filterEmployee = array_intersect_key(array_merge($_filterEmployee, $this->params['url']), $_filterEmployee);
            $_keyFilters = array(
                'profit_center_id',
                'employee_id'
            );
            $filterEmployee = Set::filter(array_combine($_keyFilters, $filterEmployee));
			if(isset($getEmployeesAjax))
			{
				$filterEmployee['employee_id']=$getEmployeesAjax;
			}
			if(isset($getPCAjax))
			{
				$filterEmployee['profit_center_id']=$getPCAjax;
			}
			else
			{
				//ADD CODE FROM VER 30/07/2014
				if(!empty($filterEmployee['profit_center_id'])){
					$checkProfitChildrens = $this->_getProfitCenterFollowParent($filterEmployee['profit_center_id']);
					if(!empty($checkProfitChildrens)){
						$filterEmployee['profit_center_id'] = array_unique(array_merge($filterEmployee['profit_center_id'], $checkProfitChildrens));
					}
				}
			}
            $newDataStaffings = array();
			$endDateFilterTmp = $endDateFilter;
			$startDateFilterTmp = $startDateFilter;
			if($dateType == 'day' || $dateType == 'week')
			{
				$startDateFilterByDay = strtotime($dayStartDate.'-'.$filterType['aStartMonth'].'-'.$filterType['aStartYear']);
				$endDateFilterByDay = strtotime($dayEndDate.'-'.$filterType['aEndMonth'].'-'.$filterType['aEndYear']);
				$startDateFilterTmp	= $startDateFilterByDay	;
				$endDateFilterTmp = $endDateFilterByDay;
			}
            /**
             * Neu checkAll profit center thi chon het
             */
            $tmpListPc = array();
            if(isset($filterEmployee['employee_id']) && !empty($filterEmployee['employee_id'])){
                //co filter employee khong lam gi ca
            } else {
                if(isset($filterType['selectPCAll']) && $filterType['selectPCAll'] === 'true'){
                    if(isset($filterEmployee['profit_center_id']) && !empty($filterEmployee['profit_center_id'])){
                        unset($filterEmployee['profit_center_id']);
                    }
                    if( ($showType == 0 || $showType == 1) && $isCheck == 0){
                        $tmpListPc = $this->ProfitCenter->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']),
                            'fields' => array('id', 'id')
                        ));
                    }
                }
            }
            switch ($showType) {
                case 0 : {
                        $staffings = $this->_visionsStaffingActivity($activities, $filterEmployee, $startDateFilter, $endDateFilter, $onlyEmployee, $dateType);
                        // pr($staffings);die;
                        break;
                    }
                case 1 : {
                        $staffings = $this->_visionsStaffingProfitCenterPC($activities, $treeShowPc, $startDateFilter, $endDateFilter);
                        break;
                    }
                case 5 : {
						$staffings = $this->_visionsStaffingProfile($activities, $startDateFilter, $endDateFilter);
                        $this->loadModel('TmpStaffingSystem');
                        $newDataStaffings = $this->TmpStaffingSystem->caculateCapacityAllProfile($startDateFilter, $endDateFilter, $employeeName);
						break;
					}
                default : {
                        $staffings = $this->_visionsStaffingFamily($activities, $filterEmployee, $startDateFilter, $endDateFilter);
                        break;
                    }
            }
        }
        // debug($staffings); exit;
        $employee_info = $this->employee_info;
        $this->set(compact('newDataStaffings', 'isCheck', 'employee_info'));
        $showGantt = isset($this->params['url']['gantt']) ? (bool) $this->params['url']['gantt'] : false;
        $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
        $this->_parseParams();
        $this->helpers = array_merge($this->helpers, array(
                'GanttVs'));
        $this->action = 'visions_staffing';
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;


		/*--------------------------
		CHECK CAC TRUONG HOP FILTER THEO Activity
		case 0: khong chon profit center v� employee
		case 1: chon employee
		case 2: chon profit center
		--------------------------*/

		$activityType=null;
		if($showType==0)
		{
			if($isCheck==1)
			{
				$activityType=(isset($filterEmployee['employee_id']))?1:2;
			}
			else
			{
				$activityType=0;
			}
		}

		//ADD CODE + MODIFY BY VINGUYEN 17/05/2014 : HISTORY FILTER

		$subFamilyList=array();
		$activityFilterList=array();
		$employeeRorProfitCenterList=array();
		if(isset($arrGetUrl['aActivated']))
				{
					$activityFilterList = ClassRegistry::init('Activity')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Activity.activated' => $arrGetUrl['aActivated']
                ),
                'fields' => array('id', 'name')
            ));
		}
		if(isset($arrGetUrl['aFamily']))
				{
					$subFamilyList = ClassRegistry::init('ActivityFamily')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'parent_id' => $arrGetUrl['aFamily']
                ),
                'fields' => array('id', 'name')
            ));
		}

			$this->loadModel('Employee');
        	$this->loadModel('ProjectEmployeeProfitFunctionRefer');
			if (isset($arrGetUrl['aPC'])) {

				$refers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
						'recursive' => -1,
						'conditions' => array('profit_center_id' => $arrGetUrl['aPC']),
						'fields' => array('id', 'employee_id')
					));
				$employees = $this->Employee->CompanyEmployeeReference->find('all', array(
					'conditions' => array(
						//'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
						'NOT' => array('Employee.is_sas' => 1),
                        'OR' => array(
                            array('Employee.end_date' => '0000-00-00'),
                            array('Employee.end_date IS NULL'),
                            array('Employee.end_date >=' => date('Y-m-d', time())),
                        ),
						'Employee.id' => $refers
					),
					'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
				));
				$employeeRorProfitCenterList= !empty($employees) ? Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();

			} else {
                $company_id = isset($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : 0;
				$employees = $this->Employee->CompanyEmployeeReference->find('all', array(
					'conditions' => array(
						//'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                        'NOT' => array('Employee.is_sas' => 1),
                        'OR' => array(
                            array('Employee.end_date' => '0000-00-00'),
                            array('Employee.end_date IS NULL'),
                            array('Employee.end_date >=' => date('Y-m-d', time())),
                        ),
                        'CompanyEmployeeReference.company_id' => $company_id
					),
					'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
				));
				$employeeRorProfitCenterList = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
			}
			if($dateType == 'day')
			{
				$listDays = $lib->getListWorkingDays($startDateFilterByDay,$endDateFilterByDay,true);
			}

            // debug($activities); exit;
			$this->set(compact('activities', 'showGantt', 'showType', 'display', 'staffings', 'startDateFilter', 'endDateFilter','activityType','arrGetUrl','subFamilyList','activityFilterList','employeeRorProfitCenterList','listDays','dateType','listWeeks','filterByProfile', 'budgetTeam'));
			//END
            if((isset($arrGetUrl['target']) && $arrGetUrl['target'] == 1) && $showType == 1){
                //profit ko lam gi ca
            } elseif(isset($arrGetUrl['ajax'])&&$arrGetUrl['ajax']){
                $this->layout = '';
                $this->render('visions_staffing_ajax');
                return;
			}
			// if($arrGetUrl['pr_file']==1){
			// 	$this->layout = false;
			// 	$summary = $arrGetUrl['summary'];
			// 	$start = $end = 0;
			// 	if(!empty($startDateFilter) && !empty($endDateFilter)){
			// 		$start = $startDateFilter;
			// 		$end = $endDateFilter;

			// 		$startTmp = $startDateFilter;
			// 		$endTmp = $endDateFilter;
			// 		while($startTmp <= $endTmp){
			// 			$totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $startTmp), date("Y", $startTmp));
			// 			$_datas = array(
			// 				0 => $totalDayOfMonth,
			// 				1 => date("m", $startTmp),
			// 				2 => date("Y", $startTmp)
			// 			);
			// 			$months[] = $_datas;
			// 			$startTmp = mktime(0, 0, 0, date("m", $startTmp)+1, date("d", $startTmp), date("Y", $startTmp));
			// 		}
			// 	}
			// 	$this->set('start',$startDateFilter);
			// 	$this->set('end',$endDateFilter);
			// 	$this->set(compact('tmpFile', 'height', 'data', 'months', 'summary'));
			// 	if($showType == 1){
			// 		$this->render('export_system_profit_center');
			// 	}
   //              else if( $showType == 5 ){
   //                  $this->render('export_staffing_by_profile');
   //              }
			// 	else
			// 	{
			// 		$this->render('export_system');
			// 	}
			// }

    }

     private function _visionsStaffingProfile($activities = null, $startDateFilter = null, $endDateFilter = null){
		//ini_set('memory_limit', '-1');
		$this->loadModel('Activity');
		$this->loadModel('ActivityFamily');
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmployee();
		//load data month from Period
		$showMonthMultiple = false;
		$listMonthPeriod = array();
        /**
         * Hide multiple month
         */
		//if(isset($this->companyConfigs['staffing_by_month_multiple']) && $this->companyConfigs['staffing_by_month_multiple'] == 1 && $this->params['url']['aDateType']==3)
//		{
//			$showMonthMultiple = true ;
//			list($listMonthPeriod,$startDateFilter,$endDateFilter) = $this->Session->read('MonthMultiple');
//		}
        //End
        if(!empty($activities)){
            $listIdActivitys = Set::classicExtract($activities, '{n}.Activity.id');
			$_fields=array(
						'TmpStaffingSystem.model_id',
						'TmpStaffingSystem.model',
						'project_id',
						'activity_id',
						'SUM(TmpStaffingSystem.consumed) as consumed',
						'TmpStaffingSystem.company_id',
						'TmpStaffingSystem.date',
						'SUM(TmpStaffingSystem.estimated) as estimated',
						'Activity.id',
						'Activity.name',
						'Activity.family_id',
						'Activity.subfamily_id'
					);
			$_joins = array(
						array(
							'table' => 'activities',
							'alias' => 'Activity',
							'type' => 'LEFT',
							'foreignKey' => 'activity_id',
							'conditions'=> array(
								'TmpStaffingSystem.activity_id = Activity.id',
							)
						)
					);
			$_group=array('TmpStaffingSystem.activity_id','TmpStaffingSystem.date');
            $getDatas = $this->TmpStaffingSystem->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'TmpStaffingSystem.activity_id' => $listIdActivitys,
					'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
					'model' => 'profile',
					'TmpStaffingSystem.company_id' => $employeeName['company_id']
				),
				'fields'=>$_fields,
				'joins' => $_joins,
				'order' => array('TmpStaffingSystem.model_id','Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
				'group'=>$_group
			));
			$datas = array();
			if(!empty($getDatas)){
				$valueOfActivity=array();
				foreach($getDatas as $key=>$getData){
					$dx = array_merge($getData['Activity'],$getData['TmpStaffingSystem'],$getData[0]);
					$_itemIndex=$dx['model_id'].'-'.$dx['activity_id'];
					 //date
					$datas[$_itemIndex]['val'][$dx['date']]['date'] = $dx['date'];
					// workload
					$datas[$_itemIndex]['val'][$dx['date']]['validated'] = $dx['estimated'];
					// consumed
					$datas[$_itemIndex]['val'][$dx['date']]['consumed'] = $dx['consumed'];
					//$datas[$_itemIndex]['val'][$dx['date']]['remains'] = round($datas[$dx['activity_id']]['val'][$dx['date']]['validated'] - $datas[$dx['activity_id']]['val'][$dx['date']]['consumed'], 2);

					if(!isset($valueOfActivity[$dx['activity_id']]))
					{
						$valueOfActivity[$dx['activity_id']]=array();
						$valueOfActivity[$dx['activity_id']]['estimated']=0;
						$valueOfActivity[$dx['activity_id']]['consumed']=0;
					}
					$valueOfActivity[$dx['activity_id']]['estimated']+= $dx['estimated'];
					$valueOfActivity[$dx['activity_id']]['consumed']+= $dx['consumed'];

					//if($typeIs==1)
					$datas[$_itemIndex]['data']['model_id'] = isset($dx['activity_id']['model_id'])?$dx['model_id']:0;
					$datas[$_itemIndex]['data']['name'] = $dx['name'];
					$datas[$_itemIndex]['data']['family_id'] = $dx['family_id'];
					$datas[$_itemIndex]['data']['subfamily_id'] = $dx['subfamily_id'];
					$datas[$_itemIndex]['data']['family_id'] = isset($dx['activity_id']['family_id'])?$dx['family_id']:0;
					$datas[$_itemIndex]['data']['subfamily_id'] = isset($dx['activity_id']['subfamily_id'])?$dx['subfamily_id']:0;
				}
				//remove activity not available estiamted & consumed
				foreach($valueOfActivity as $_acti=>$_val)
				{
					if($_val['estimated']==0&&$_val['consumed']==0)
					{
						unset($datas[$_acti]);
					}
				}
			}
            /*if(!empty($datas)){
                $tmpDatas = $datas;
                $datas = $this->_resetRemainSystem($tmpDatas);
            }*/
			//debug($datas);exit();
        }
        $staffings = array();
		//IF BY DAY
		$sharedEqually = false;
		if($this->params['url']['aDateType']==1 || $this->params['url']['aDateType']==2 || $showMonthMultiple)
		{
			/*---------------------------------------------------------------
			if filter by day-week-month multiple (get data from Period table)
			apply algorithm shared equally workload form month to day.
			----------------------------------------------------------------*/
			$sharedEqually = true;
		}
		if($sharedEqually)
		{
			$this->loadModel('ActivityRequest');
			$this->loadModel('ActivityTask');
			$this->loadModel('Activity');
			$this->loadModel('NctWorkload');
			$this->loadModel('ProjectEmployeeProfitFunctionRefer');
			$lib = new LibBehavior();
			/*--------------OPTIMIZE-------------*/
			//get working day for months
			$listWorkingDays = array();
			foreach($listMonthPeriod as $key => $val)
			{
				$listWorkingDays[$key] = $lib->getWorkingDaysInMonth($key,true);
			}
			//end
		}
		//END

		//CAPACITY BY PROFILES
		$listProfiles = array();
		$capacities = $this->TmpStaffingSystem->calculateStaffingByProfiles($listProfiles, $startDateFilter, $endDateFilter, $employeeName);
		
		// FTE by year for profile filter
		// 27-10-2018
		// Edit by Viet Nguyen
		$fte_by_year = $this->calculateFteByYearProfiles($listProfiles, $startDateFilter, $endDateFilter, $employeeName);

		//END
        if(!empty($datas)){
            foreach($datas as $id => $data){
				$valOfActivity=$data['val'] ? $data['val'] : array();
				//IF BY DAY
				$this->params['url']['aDateType'] = 3;
				if($sharedEqually)
				{
					$dateTypeTmp = $this->params['url']['aDateType']==1  || $showMonthMultiple ? 'day' : 'week' ;
					$arrValue = explode('-',$id);
					if(isset($arrValue[1]))
					{
						$_activityId = $arrValue[1];
					}
					else
					{
						$_activityId = $arrValue[0];
					}

					$dataConsumed = array();


					/*----------FIX BUG nct workload shared equally for workdays,
					according to the principle : nct workload assign to resources by day
					------------------------------------------------------------*/
					//version 5 : get nct task, group by activity-resource-date
					//1 get nct tasks, includes : project task and activity task
					$listNctTasks = $this->ActivityTask->getActivityNctTask($_activityId);
					//2 SUM workload value group by date
					$resources = array(
						//'pc' => $_pcRefers,
						//'employee' => $listEmployees
					);

					$nctWorkloads = $this->NctWorkload->getNctWorkloadByResource($startDateFilter, $endDateFilter, $listNctTasks, $resources);

					$nctWorkloadsByDay = $nctWorkloads['day'];
					$nctWorkloads = $nctWorkloads['month'];
					//end
					$dataByDay = array();
					foreach($valOfActivity as $time => $_DATA)
					{
						$keyNct = date('d-m-Y',$time);
						$nctWorkload = isset($nctWorkloads[$keyNct]) ? $nctWorkloads[$keyNct] : 0 ;
						$_DATA['validated'] = isset($_DATA['validated']) ? $_DATA['validated'] : 0 ;
						$VALIDATE =$_DATA['validated'] - $nctWorkload;
						$workingDays = isset($listWorkingDays[$time]) ? $listWorkingDays[$time] : $lib->getWorkingDaysInMonth($time,true) ;
						$_dataByDay = $this->estimatedDataFromMonthToDay(array('validate'=>$VALIDATE,'nct' => $nctWorkloadsByDay),$dataConsumed,$workingDays,$lib, $dateTypeTmp);
						$dataByDay = $dataByDay + $_dataByDay;
					}
					$valOfActivity = $dataByDay;
					if($showMonthMultiple)
					{
						$valOfActivity = $this->groupDataFromDayToMonth($valOfActivity,$listMonthPeriod);
					}
				}
				//END
				$_family_id=$data['data']['family_id'];
				$_subfamily_id= $data['data']['subfamily_id'] == 0 || $data['data']['subfamily_id'] == null ? null : $data['data']['subfamily_id'] ;
				$_activity_id=$id;

				$_model_id=$data['data']['model_id'];
				//$_model_id=0;
				$this->loadModel('Profile');
				$profiles = $this->Profile->find('first', array(
					'recursive' => -1,
					'fields' => array('id','name'),
					'conditions' => array('id'=>$_model_id)
				));
				if(empty($profiles))
				{
					$profiles=__('Not Affected', true);
				}
				else
				{
					$profiles=$profiles['Profile']['name'];
				}

				$family = $this->ActivityFamily->find('first', array(
					'recursive' => -1,
					'fields' => array('id','name'),
					'conditions' => array('id'=>$_family_id)
				));
				$subFamily = $this->ActivityFamily->find('first', array(
					'recursive' => -1,
					'fields' => array('id','name'),
					'conditions' => array('ActivityFamily.id'=>$_subfamily_id)
				));
				$indexEmployee='employee-'.$_model_id;
				$indexFamily='family-'.$_model_id.'-'.$_family_id;
				$indexSubFamily='subfamily-'.$_model_id.'-'.$_family_id.'-'.$_subfamily_id;
				/*$indexEmployee = 'undefined';
				$indexFamily='family-'.$_family_id;
				$indexSubFamily='subfamily-'.$_family_id.'-'.$_subfamily_id;*/

				if(!isset($staffings[$indexEmployee]))
				$staffings[$indexEmployee] = array(
					'id' => $indexEmployee,
					'isEmployee' => 1,
					'employee_id' => $_model_id,
					'family' => '',
					'subfamily' => '',
					'family_id' => -1,
					'subfamily_id' => -1,
					'name' => !empty($profiles) ? $profiles : 'No Name',
					'profile_by_year' => (!empty($fte_by_year) && !empty($fte_by_year[$_model_id])) ?  $fte_by_year[$_model_id] : array(),
					'data' => array()
				);

				if(!isset($staffings[$indexFamily]))
				$staffings[$indexFamily] = array(
					'id' => $indexFamily,
					'employee_id' => $_model_id,
					'isFamily' => 1,
					'family' => '',
					'subfamily' => '',
					'family_id' => -1,
					'subfamily_id' => -1,
					'name' => !empty($family['ActivityFamily']['name']) ? $family['ActivityFamily']['name'] : 'No Family Name',
					'data' => array()
				);

				if(!isset($staffings[$indexSubFamily]))
				$staffings[$indexSubFamily] = array(
					'id' => $indexSubFamily,
					'employee_id' => $_model_id,
					'family' => !empty($family['ActivityFamily']['name']) ? $family['ActivityFamily']['name'] : 'No Family Name',
					'isSubfamily'=>1,
					'subfamily' => '',
					'family_id' => !empty($_family_id) ? $_family_id : null,
					'subfamily_id' => -1,
					'name' => !empty($subFamily['ActivityFamily']['name']) ? $subFamily['ActivityFamily']['name'] : 'No Sub-Family Name',
					'data' => array()
				);

				$staffings[] = array(
					'id' => $id,
					'isActivity'=> 1,
					'employee_id' => $_model_id,
					'family' => !empty($family['ActivityFamily']['name']) ? $family['ActivityFamily']['name'] : 'No Family Name',
					'subfamily' => !empty($subFamily['ActivityFamily']['name']) ? $subFamily['ActivityFamily']['name'] : 'No Sub-Family Name',
					'family_id' => !empty($_family_id) ? $_family_id : null,
					'subfamily_id' => !empty($_subfamily_id) ? $_subfamily_id : null,
					'name' => !empty($data['data']['name']) ? $data['data']['name'] : 'No Name',
					'data' => $valOfActivity
				);
				$default = array(
						'validated' => 0,
						'consumed' => 0,
						'totalWorkload' => 0,
						'resource' => 0,
						'resource_theoretical' => 0,
						'fte' => 0,
						
				);
				$keyProfile = str_replace('employee-','',$indexEmployee);
				foreach($valOfActivity as $_date=>$_data)
				{
					if(!isset($staffings[$indexEmployee]['data'][$_date]))
					{
						//$staffings[$indexEmployee]['data'][$_date] = $default ;
						$staffings[$indexEmployee]['data'][$_date] = isset($capacities[$keyProfile]['data'][$_date]) ? $capacities[$keyProfile]['data'][$_date] : $default ;
						$staffings[$indexEmployee]['data'][$_date]['date'] = $_date;
					}
					$staffings[$indexEmployee]['data'][$_date]['validated']+=$_data['validated'];
					$staffings[$indexEmployee]['data'][$_date]['consumed']+=$_data['consumed'];

					//DO NOT APPLY WHEN CLICK ACTIVITY
					if(!isset($staffings[$indexFamily]['data'][$_date]))
					{
						$staffings[$indexFamily]['data'][$_date] = $default ;
						$staffings[$indexFamily]['data'][$_date]['date'] = $_date;
					}
					$staffings[$indexFamily]['data'][$_date]['validated']+=$_data['validated'];
					$staffings[$indexFamily]['data'][$_date]['consumed']+=$_data['consumed'];


					if(!isset($staffings[$indexSubFamily]['data'][$_date]))
					{
						$staffings[$indexSubFamily]['data'][$_date] = $default ;
						$staffings[$indexSubFamily]['data'][$_date]['date'] = $_date;
					}
					$staffings[$indexSubFamily]['data'][$_date]['validated']+=$_data['validated'];
					$staffings[$indexSubFamily]['data'][$_date]['consumed']+=$_data['consumed'];
				}
            }
        }
		if(!empty($capacities))
		{
			foreach($capacities as $_profile => $_DATA)
			{
				$keyProfile = 'employee-'.$_profile;
				if(!empty($_DATA['data']))
				{
					foreach($_DATA['data'] as $_TIME => $_VAL)
					{
						if(!isset($staffings[$keyProfile]['data'][$_TIME]) && isset($staffings[$keyProfile]))
						{
							$staffings[$keyProfile]['data'][$_TIME] = $_VAL ;
						}
					}
				}
			}
		}
		return $staffings;
     }
     private function _visionsStaffingActivity($activities = null, $conditions = null, $startDateFilter = null, $endDateFilter = null, $onlyEmployee = false, $dateType = null){
		//ini_set('memory_limit', '-1');
		$this->loadModel('Activity');
		$this->loadModel('ActivityFamily');
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ActivityBudget');
		$employeeName = $this->_getEmployee();
		//load data month from Period
		$showMonthMultiple = false;
		$listMonthPeriod = array();
        $budgetFamilies = $budgetSubFamilies = $listDayOfBudgets = array();
        /**
         * Hide multiple month
         */
		//if(isset($this->companyConfigs['staffing_by_month_multiple']) && $this->companyConfigs['staffing_by_month_multiple'] == 1 && $this->params['url']['aDateType']==3)
//		{
//			$showMonthMultiple = true ;
//			list($listMonthPeriod,$startDateFilter,$endDateFilter) = $this->Session->read('MonthMultiple');
//		}
        //End
        $budgetPcs = 0;
        if(!empty($conditions['profit_center_id'])){
            $parentOfPCs = array();
            $checkDup = false;
            foreach($conditions['profit_center_id'] as $id){
                $node = $this->ProfitCenter->getParentNode($id);
                if(in_array($node['ProfitCenter']['id'], $parentOfPCs)){
                    $checkDup = true;
                } else {
                    $parentOfPCs[$node['ProfitCenter']['id']] = $node['ProfitCenter']['id'];
                }
            }
            if($checkDup == true){
                $budgetPcs = !empty($parentOfPCs) ? min($parentOfPCs) : 0;
            } else {
                $budgetPcs = min($conditions['profit_center_id']);
            }
        }
        if(!empty($activities)){
            $listIdActivitys = Set::classicExtract($activities, '{n}.Activity.id');
			$_fields=array(
						'TmpStaffingSystem.model_id',
						'TmpStaffingSystem.model',
						'project_id',
						'activity_id',
						'SUM(TmpStaffingSystem.consumed) as consumed',
						'TmpStaffingSystem.company_id',
						'TmpStaffingSystem.date',
						'SUM(TmpStaffingSystem.estimated) as estimated',
						'Activity.id',
						'Activity.name',
						'Activity.family_id',
						'Activity.subfamily_id'
					);
			$_joins = array(
						array(
							'table' => 'activities',
							'alias' => 'Activity',
							'type' => 'LEFT',
							'foreignKey' => 'activity_id',
							'conditions'=> array(
								'TmpStaffingSystem.activity_id = Activity.id',
							)
						)
					);
			$_group=array('TmpStaffingSystem.activity_id','TmpStaffingSystem.date');
			$employeeManager=-1;
            if(!empty($conditions)){
                if(!empty($conditions['employee_id'])){
					$typeIs=1;
					if($onlyEmployee)
					{
						$_getDatasNotAffected = array();
						$_getDatasManager = array();
						$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
						$employeeOther=$conditions['employee_id'];
						if(empty($conditions['profit_center_id']))
						{
							$employeeOther[] = 999999999;
						}
						else
						{
							$_getDatasNotAffected = $this->TmpStaffingSystem->find('all', array(
								'recursive' => -1,
								'conditions' => array(
									'TmpStaffingSystem.activity_id' => $listIdActivitys,
									'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
									'TmpStaffingSystem.model_id' => $conditions['profit_center_id'],
									'model' => 'profit_center',
									'TmpStaffingSystem.company_id' => $employeeName['company_id']
								),
								'fields'=>$_fields,
								'joins' => $_joins,
								'order' => array('TmpStaffingSystem.model_id','Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
								'group'=>$_group
							));
						}
					}
					else
					{
						$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.activity_id','TmpStaffingSystem.date');
						$employeeManager=$conditions['employee_id'][0];
						$employeeOther=$conditions['employee_id'];
						array_shift($employeeOther);
						$_getDatasManager = $this->TmpStaffingSystem->find('all', array(
							'recursive' => -1,
							'conditions' => array(
								'TmpStaffingSystem.activity_id' => $listIdActivitys,
								'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
								'TmpStaffingSystem.model_id' => $employeeManager,
								'model' => 'employee',
								'TmpStaffingSystem.company_id' => $employeeName['company_id']
							),
							'fields'=>$_fields,
							'joins' => $_joins,
							'order' => array('TmpStaffingSystem.model_id','Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
							'group'=>$_group
						));
						$_getDatasNotAffected=array();
						if(isset($this->params['url']['ItMe']))
						{
							//SELECT ALL DATA OF PC CURRENT : cach nay do, nhung do cach luu staffing, phan nay se sua lai khi format staffing
							$_getDatasNotAffected = $this->TmpStaffingSystem->find('all', array(
								'recursive' => -1,
								'conditions' => array(
									'TmpStaffingSystem.activity_id' => $listIdActivitys,
									'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
									'TmpStaffingSystem.model_id' => $this->params['url']['ItMe'],
									'model' => 'profit_center',
									'TmpStaffingSystem.company_id' => $employeeName['company_id']
								),
								'fields'=>$_fields,
								'joins' => $_joins,
								'order' => array('TmpStaffingSystem.model_id','Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
								'group'=>$_group
							));
						}
					}
					$_getDatas = $this->TmpStaffingSystem->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'TmpStaffingSystem.activity_id' => $listIdActivitys,
                            'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                            'TmpStaffingSystem.model_id' => $employeeOther,
                            'model' => 'employee',
                            'TmpStaffingSystem.company_id' => $employeeName['company_id']
                        ),
						'fields'=>$_fields,
						'joins' => $_joins,
						'order' => array('TmpStaffingSystem.model_id','Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
						'group'=>$_group
                    ));

					$getDatas=array_merge($_getDatasManager,$_getDatas,$_getDatasNotAffected);
                } else {
					$typeIs=2;
                    if(!empty($conditions['profit_center_id'])){
						$getDatas = $this->TmpStaffingSystem->find('all', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'TmpStaffingSystem.activity_id' => $listIdActivitys,
                                'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                                'TmpStaffingSystem.model_id' => $conditions['profit_center_id'],
                                'model' => 'profit_center',
                                'TmpStaffingSystem.company_id' => $employeeName['company_id']
                            ),
							'fields'=>$_fields,
							'joins' => $_joins,
							'order' => array('Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
							'group'=>$_group
                        ));
                    }
                }
            } else {
				$typeIs=0;
                $getDatas = $this->TmpStaffingSystem->find('all', array(
					'recursive' => -1,
                    'conditions' => array(
                        'TmpStaffingSystem.activity_id' => $listIdActivitys,
                        'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                        'model' => 'profit_center', //replace employee by profit_center , date : 08/07/2015
                        'TmpStaffingSystem.company_id' => $employeeName['company_id']
                    ),
					'fields'=>$_fields,
					'joins' => $_joins,
					'order' => array('Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
					'group'=>$_group
                ));
            }
			//$getDatasOrder=Set::combine($activityNames, '{n}.Activity.id','{n}.Activity.id')
			if($typeIs==1)
			{
				$datas = array();
				if(!empty($getDatas)){
					$valueOfActivity=array();
					$datasAffected = array();
                    //$xConsume = $this->getConsumeFromActivity($listIdActivitys, $startDateFilter, strtotime('last day of this month', $endDateFilter), true);
					foreach($getDatas as $key=>$getData){
						$dx = array_merge($getData['Activity'],$getData['TmpStaffingSystem'],$getData[0]);
						//SUM DATA ASSIGNTO EMPLOYEE
						if(!isset($datasAffected[$dx['activity_id']][$dx['date']]))
						{
							$datasAffected[$dx['activity_id']][$dx['date']]=array();
							$datasAffected[$dx['activity_id']][$dx['date']]['estimated']=0.00;
						}
						if($dx['model'] == 'profit_center')
						{
							//SET DATA FOR NOT AFFECTED
							$_itemIndex='999999999-'.$dx['activity_id'];
							// workload
							$datas[$_itemIndex]['val'][$dx['date']]['validated'] = number_format(($dx['estimated'] - $datasAffected[$dx['activity_id']][$dx['date']]['estimated']),2);
							// consumed
							$datas[$_itemIndex]['val'][$dx['date']]['consumed'] = 0;
						}
						else
						{
							$_itemIndex=$dx['model_id'].'-'.$dx['activity_id'];
                            //modified by qn
                            //$dx['consumed'] = isset($xConsume[$_itemIndex][$dx['date']]) ? $xConsume[$_itemIndex][$dx['date']] : 0;
							// workload
							$datas[$_itemIndex]['val'][$dx['date']]['validated'] = $dx['estimated'];
							// consumed
							$datas[$_itemIndex]['val'][$dx['date']]['consumed'] = $dx['consumed'];
						}
						//date
						$datas[$_itemIndex]['val'][$dx['date']]['date'] = $dx['date'];
						//remain
						$datas[$_itemIndex]['val'][$dx['date']]['remains'] = round($datas[$_itemIndex]['val'][$dx['date']]['validated'] - $datas[$_itemIndex]['val'][$dx['date']]['consumed'], 2);

						if(!isset($valueOfActivity[$_itemIndex]))
						{
							$valueOfActivity[$_itemIndex]=array();
							$valueOfActivity[$_itemIndex]['estimated']=0;
							$valueOfActivity[$_itemIndex]['consumed']=0;
						}
						$valueOfActivity[$_itemIndex]['estimated']+= $datas[$_itemIndex]['val'][$dx['date']]['validated'];
						$valueOfActivity[$_itemIndex]['consumed']+= $datas[$_itemIndex]['val'][$dx['date']]['consumed'];

						//SUM DATA ASSIGNTO EMPLOYEE
						$datasAffected[$dx['activity_id']][$dx['date']]['estimated']+= $dx['estimated'];

						//if($typeIs==1)
						if($dx['model'] == 'profit_center')
						$datas[$_itemIndex]['data']['model_id'] = 999999999;
						else
						$datas[$_itemIndex]['data']['model_id'] = isset($dx['activity_id']['model_id'])?$dx['model_id']:0;
						$datas[$_itemIndex]['data']['name'] = $dx['name'];
						$datas[$_itemIndex]['data']['family_id'] = $dx['family_id'];
						$datas[$_itemIndex]['data']['subfamily_id'] = $dx['subfamily_id'];
						$datas[$_itemIndex]['data']['family_id'] = isset($dx['activity_id']['family_id'])?$dx['family_id']:0;
						$datas[$_itemIndex]['data']['subfamily_id'] = isset($dx['activity_id']['subfamily_id'])?$dx['subfamily_id']:0;
					}

					//remove activity not available estiamted & consumed
					foreach($valueOfActivity as $_acti=>$_val)
					{
						if($_val['estimated']==0&&$_val['consumed']==0)
						{
							unset($datas[$_acti]);
						}
					}
				}
			}
			else
			{
				$datas = array();
				if(!empty($getDatas)){
				    /**
                     * Lay activity budget of cac family, subfamily
                     *
                     */
                    if($dateType == 'month'){
                        $familyIds = !empty($getDatas) ? array_filter(array_unique(Set::classicExtract($getDatas, '{n}.Activity.family_id'))) : array();
                        $subFamilyIds = !empty($getDatas) ? array_filter(array_unique(Set::classicExtract($getDatas, '{n}.Activity.subfamily_id'))) : array();
                        $conditionOfActivityBudget = array(
                            'OR' => array(
                                'family_id' => $familyIds,
                                'subfamily_id' => $subFamilyIds
                            ),
                            'profit_id' => $budgetPcs,
                            'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                            'company_id' => $employeeName['company_id'],
                            'type' => 'md'
                        );

                        $activityBudgets = $this->ActivityBudget->find('all', array(
                            'recursive' => -1,
                            'conditions' => $conditionOfActivityBudget,
                            'fields' => array('SUM(value) AS Total','family_id', 'subfamily_id', 'date'),
                            'group' => array('family_id', 'subfamily_id', 'date')
                        ));
                        if(!empty($activityBudgets)){
                            foreach($activityBudgets as $activityBudget){
                                $dx = $activityBudget['ActivityBudget'];
                                $val = $activityBudget[0]['Total'];
                                if(!empty($dx['subfamily_id'])){
                                    if(!isset($budgetSubFamilies[$dx['subfamily_id']][$dx['date']])){
                                        $budgetSubFamilies[$dx['subfamily_id']][$dx['date']] = 0;
                                    }
                                    $budgetSubFamilies[$dx['subfamily_id']][$dx['date']] = $val;
                                } else {
                                    if(!isset($budgetFamilies[$dx['family_id']][$dx['date']])){
                                        $budgetFamilies[$dx['family_id']][$dx['date']] = 0;
                                    }
                                    $budgetFamilies[$dx['family_id']][$dx['date']] = $val;
                                }
                                $listDayOfBudgets[$dx['date']] = $dx['date'];
                            }
                        }
                    }
					$valueOfActivity=array();
                    //quyet modified here
                    //$xConsume = $this->getConsumeFromActivity($listIdActivitys, $startDateFilter, strtotime('last day of this month', $endDateFilter), false);

					foreach($getDatas as $key=>$getData){
						$dx = array_merge($getData['Activity'],$getData['TmpStaffingSystem'],$getData[0]);
                        //quyet modified here
                        //$dx['consumed'] = isset($xConsume[$dx['activity_id']][$dx['date']]) ? $xConsume[$dx['activity_id']][$dx['date']] : 0;
						//date
						$datas[$dx['activity_id']]['val'][$dx['date']]['date'] = $dx['date'];
						// workload
						$datas[$dx['activity_id']]['val'][$dx['date']]['validated'] = $dx['estimated'];
						// consumed
						$datas[$dx['activity_id']]['val'][$dx['date']]['consumed'] = $dx['consumed'];
                        // if( $dx['activity_id'] == 3035 ){
                        //     pr($dx['date'] . ' ' . $dx['consumed']);
                        // }
						$datas[$dx['activity_id']]['val'][$dx['date']]['remains'] = round($datas[$dx['activity_id']]['val'][$dx['date']]['validated'] - $datas[$dx['activity_id']]['val'][$dx['date']]['consumed'], 2);

						if(!isset($valueOfActivity[$dx['activity_id']]))
						{
							$valueOfActivity[$dx['activity_id']]=array();
							$valueOfActivity[$dx['activity_id']]['estimated']=0;
							$valueOfActivity[$dx['activity_id']]['consumed']=0;
						}
						$valueOfActivity[$dx['activity_id']]['estimated'] += $dx['estimated'];
						$valueOfActivity[$dx['activity_id']]['consumed'] += $dx['consumed'];

						//if($typeIs==1)
						$datas[$dx['activity_id']]['data']['model_id'] = isset($dx['activity_id']['model_id'])?$dx['model_id']:0;
						$datas[$dx['activity_id']]['data']['name'] = $dx['name'];
						$datas[$dx['activity_id']]['data']['family_id'] = $dx['family_id'];
						$datas[$dx['activity_id']]['data']['subfamily_id'] = $dx['subfamily_id'];
						$datas[$dx['activity_id']]['data']['family_id'] = isset($dx['activity_id']['family_id'])?$dx['family_id']:0;
						$datas[$dx['activity_id']]['data']['subfamily_id'] = isset($dx['activity_id']['subfamily_id'])?$dx['subfamily_id']:0;

					}
					//remove activity not available estiamted & consumed

					foreach($valueOfActivity as $_acti=>$_val)
					{
						if($_val['estimated'] == 0 && $_val['consumed'] == 0)
						{
							unset($datas[$_acti]);
						}
					}
				}
			}
            /*if(!empty($datas)){
                $tmpDatas = $datas;
                $datas = $this->_resetRemainSystem($tmpDatas);
            }*/
			//debug($datas);exit();
        }
        $staffings = array();
		//IF BY DAY
		$sharedEqually = false;
		if($this->params['url']['aDateType']==1 || $this->params['url']['aDateType']==2 || $showMonthMultiple)
		{
			/*---------------------------------------------------------------
			if filter by day-week-month multiple (get data from Period table)
			apply algorithm shared equally workload form month to day.
			----------------------------------------------------------------*/
			$sharedEqually = true;
		}

		if($sharedEqually)
		{
			$this->loadModel('ActivityRequest');
			$this->loadModel('ActivityTask');
			$this->loadModel('Activity');
			$this->loadModel('NctWorkload');
			$this->loadModel('ProjectEmployeeProfitFunctionRefer');
			$lib = new LibBehavior();
			$listEmployees = null;
			$pcRefers = array();
			if(isset($conditions['profit_center_id']))
			{
				$listEmployees = $this->ProjectEmployeeProfitFunctionRefer->getEmployeesOfPCs($conditions['profit_center_id']);
				$pcRefers = $conditions['profit_center_id'];
			}
			/*--------------OPTIMIZE-------------*/
			//get working day for months
			$listWorkingDays = array();
			foreach($listMonthPeriod as $key => $val)
			{
				$listWorkingDays[$key] = $lib->getWorkingDaysInMonth($key,true);
			}
			//end
			//filter records from table Activity Requests => limited query => optimize execute time
			//$recordsRequest = $this->ActivityRequest->filterRecordsByDateAndCompany($startDateFilter, $endDateFilter, $employeeName['company_id']);
			//end
		}
		//END
        // show empty resources?
        $presenseResources = array();
        if(!empty($datas)){
            foreach($datas as $id => $data){
				$valOfActivity = !empty($data['val']) ? $data['val'] : array();
				//IF BY DAY
				if($sharedEqually)
				{
					$dateTypeTmp = $this->params['url']['aDateType']==1 || $showMonthMultiple ? 'day' : 'week' ;
					$arrValue = explode('-',$id);
					if(isset($arrValue[1]))
					{
						$_activityId = $arrValue[1];
					}
					else
					{
						$_activityId = $arrValue[0];
					}
					$_pcRefers = array();
					if( $typeIs == 1 ){
						$listEmployees = $arrValue[0];
						if($listEmployees == 999999999)
						{
							$_pcRefers = $pcRefers;
						}
					}
					else
					{
						$_pcRefers = $pcRefers;
					}
					//debug($listEmployees);
					$dataConsumed = array();
					$dataConsumed = $this->ActivityRequest->getConsumedByDate($startDateFilter, $endDateFilter, $employeeName['company_id'], $_activityId, $listEmployees);

					/*----------FIX BUG nct workload shared equally for workdays,
					according to the principle : nct workload assign to resources by day
					------------------------------------------------------------*/
					//version 5 : get nct task, group by activity-resource-date
					//1 get nct tasks, includes : project task and activity task
					$listNctTasks = $this->ActivityTask->getActivityNctTask($_activityId);
					//2 SUM workload value group by date
					$resources = array(
						'pc' => $_pcRefers,
						'employee' => $listEmployees
					);
					//debug($resources);
					$nctWorkloads = $this->NctWorkload->getNctWorkloadByResource($startDateFilter, $endDateFilter, $listNctTasks, $resources);
					//debug($nctWorkloads);
					$nctWorkloadsByDay = $nctWorkloads['day'];
					$nctWorkloads = $nctWorkloads['month'];
					//end
					$dataByDay = array();
					foreach($valOfActivity as $time => $_DATA)
					{
						$keyNct = date('d-m-Y',$time);
						$nctWorkload = isset($nctWorkloads[$keyNct]) ? $nctWorkloads[$keyNct] : 0 ;
						$_DATA['validated'] = isset($_DATA['validated']) ? $_DATA['validated'] : 0 ;
						$VALIDATE =$_DATA['validated'] - $nctWorkload;
						$workingDays = isset($listWorkingDays[$time]) ? $listWorkingDays[$time] : $lib->getWorkingDaysInMonth($time,true);
						$_dataByDay = $this->estimatedDataFromMonthToDay(array('validate'=>$VALIDATE,'nct' => $nctWorkloadsByDay),$dataConsumed,$workingDays,$lib, $dateTypeTmp);
						$dataByDay = $dataByDay + $_dataByDay;
					}
					$valOfActivity = $dataByDay;
					if($showMonthMultiple)
					{
						$valOfActivity = $this->groupDataFromDayToMonth($valOfActivity,$listMonthPeriod);
					}
				}
                $listDayOfActivities = !empty($valOfActivity) ? Set::classicExtract($valOfActivity, '{n}.date') : array();
                if(!empty($listDayOfBudgets)){
                    foreach($listDayOfBudgets as $time){
                        if(!in_array($time, $listDayOfActivities)){
                            $valOfActivity[$time] = array();
                            //$valOfActivity
                        }
                    }
                }
				//END
				$_family_id=$data['data']['family_id'];
				$_subfamily_id=$data['data']['subfamily_id']==0?null:$data['data']['subfamily_id'];
				$_activity_id=$id;
				$_isManager=null;

				if($typeIs==1)
				{
					$_model_id=$data['data']['model_id'];
					//$_model_id=0;
					$this->loadModel('Employee');
					$employee = $this->Employee->find('first', array(
						'recursive' => -1,
						'fields' => array('id','first_name','last_name'),
						'conditions' => array('id'=>$_model_id)
					));
					if(empty($employee))
					{
						$employee=__('Not affected to a resource', true);
					}
					else
					{
						$employee=$employee['Employee']['first_name'].' '.$employee['Employee']['last_name'];
					}
				}
				else
				{
					$_model_id=0;
				}
				if($employeeManager==$_model_id)
				{
					$_isManager=1;
				}
				$family = $this->ActivityFamily->find('first', array(
					'recursive' => -1,
					'fields' => array('id','name'),
					'conditions' => array('id'=>$_family_id)
				));
				$subFamily = $this->ActivityFamily->find('first', array(
					'recursive' => -1,
					'fields' => array('id','name'),
					'conditions' => array('ActivityFamily.id'=>$_subfamily_id)
				));
				if($typeIs==1)
				{
					$indexEmployee='employee-'.$_model_id;
                    $presenseResources[] = $_model_id;
					$indexFamily='family-'.$_model_id.'-'.$_family_id;
					$indexSubFamily='subfamily-'.$_model_id.'-'.$_family_id.'-'.$_subfamily_id;
				}
				elseif($typeIs==0)
				{
					$indexFamily='family-'.$_family_id;
					$indexSubFamily='subfamily-'.$_family_id.'-'.$_subfamily_id;
				}
				elseif($typeIs==2)
				{
					$indexFamily='family-'.$_family_id;
					$indexSubFamily='subfamily-'.$_family_id.'-'.$_subfamily_id;
				}
				if($typeIs==1)
				{
					if(!isset($staffings[$indexEmployee]))
					$staffings[$indexEmployee] = array(
						'id' => $indexEmployee,
						'isEmployee' => 1,
						'isManager' => $_isManager,
						'employee_id' => $_model_id,
						'family' => '',
						'subfamily' => '',
						'family_id' => -1,
						'subfamily_id' => -1,
						'name' => !empty($employee) ? $employee : 'No Name',
						'data' => array()
					);
				}
				if(!$onlyEmployee)
				{
					//DO NOT APPLY WHEN CLICK ACTIVITY
					if(!isset($staffings[$indexFamily]))
					$staffings[$indexFamily] = array(
						'id' => $indexFamily,
						'employee_id' => $_model_id,
						'isManager' => $_isManager,
						'isFamily' => 1,
						'family' => '',
						'subfamily' => '',
						'family_id' => -1,
						'subfamily_id' => -1,
						'name' => !empty($family['ActivityFamily']['name']) ? $family['ActivityFamily']['name'] : 'No Family Name',
						'data' => array()
					);
					//if(isset($_subfamily_id))
					//{
					//debug($indexSubFamily);
						if(!isset($staffings[$indexSubFamily]))
						$staffings[$indexSubFamily] = array(
							'id' => $indexSubFamily,
							'employee_id' => $_model_id,
							'isManager' => $_isManager,
							'family' => !empty($family['ActivityFamily']['name']) ? $family['ActivityFamily']['name'] : 'No Family Name',
							'isSubfamily'=>1,
							'subfamily' => '',
							'family_id' => !empty($_family_id) ? $_family_id : null,
							'subfamily_id' => -1,
							'name' => !empty($subFamily['ActivityFamily']['name']) ? $subFamily['ActivityFamily']['name'] : 'No Sub-Family Name',
							'data' => array()
						);
					//}
					$staffings[] = array(
						'id' => $id,
						'isActivity'=> 1,
						'employee_id' => $_model_id,
						'isManager' => $_isManager,
						'family' => !empty($family['ActivityFamily']['name']) ? $family['ActivityFamily']['name'] : 'No Family Name',
						'subfamily' => !empty($subFamily['ActivityFamily']['name']) ? $subFamily['ActivityFamily']['name'] : 'No Sub-Family Name',
						'family_id' => !empty($_family_id) ? $_family_id : null,
						'subfamily_id' => !empty($_subfamily_id) ? $_subfamily_id : null,
						'name' => !empty($data['data']['name']) ? $data['data']['name'] : 'No Name',
						'data' => $valOfActivity
					);
				}
				foreach($valOfActivity as $_date=>$_data)
				{
				    $valid = !empty($_data['validated']) ? $_data['validated'] : 0;
                    $consum = !empty($_data['consumed']) ? $_data['consumed'] : 0;
					if($typeIs==1)
					{
						if(isset($staffings[$indexEmployee]['data'][$_date]))
						{
							$staffings[$indexEmployee]['data'][$_date]['validated']+=$valid;
							$staffings[$indexEmployee]['data'][$_date]['consumed']+=$consum;
						}
						else
						{
							$staffings[$indexEmployee]['data'][$_date]['validated']=$valid;
							$staffings[$indexEmployee]['data'][$_date]['consumed']=$consum;
						}
					}
					if(!$onlyEmployee)
					{
						//DO NOT APPLY WHEN CLICK ACTIVITY
						if(isset($staffings[$indexFamily]['data'][$_date]))
						{
							$staffings[$indexFamily]['data'][$_date]['validated']+=$valid;
							$staffings[$indexFamily]['data'][$_date]['consumed']+=$consum;
						}
						else
						{
							$staffings[$indexFamily]['data'][$_date]['validated']=$valid;
							$staffings[$indexFamily]['data'][$_date]['consumed']=$consum;
						}

						//if(isset($_subfamily_id))
						//{
						if(isset($staffings[$indexSubFamily]['data'][$_date]))
						{
							$staffings[$indexSubFamily]['data'][$_date]['validated']+=$valid;
							$staffings[$indexSubFamily]['data'][$_date]['consumed']+=$consum;
						}
						else
						{
							$staffings[$indexSubFamily]['data'][$_date]['validated']=$valid;
							$staffings[$indexSubFamily]['data'][$_date]['consumed']=$consum;
						}
						//}
					}
                    if($dateType == 'month'){
				        if(isset($staffings[$indexFamily]['data'][$_date])){
                            if(!isset($staffings[$indexFamily]['data'][$_date]['budget'])){
                                $staffings[$indexFamily]['data'][$_date]['budget'] = 0;
                            }
                            if(!empty($budgetFamilies) && !empty($budgetFamilies[$_family_id]) && !empty($budgetFamilies[$_family_id][$_date])){
                                $staffings[$indexFamily]['data'][$_date]['budget'] = $budgetFamilies[$_family_id][$_date];
                            }
                        }
                        if(isset($staffings[$indexSubFamily]['data'][$_date])) {
                            if(!isset($staffings[$indexSubFamily]['data'][$_date]['budget'])){
                                $staffings[$indexSubFamily]['data'][$_date]['budget'] = 0;
                            }
                            if(!empty($budgetSubFamilies) && !empty($budgetSubFamilies[$_subfamily_id]) && !empty($budgetSubFamilies[$_subfamily_id][$_date])){
                                $staffings[$indexSubFamily]['data'][$_date]['budget'] = $budgetSubFamilies[$_subfamily_id][$_date];
                            }
                        }
				    }
				}
            }
        }
        if( $this->settings['show_empty_resources'] && isset($conditions['profit_center_id']) && $typeIs == 1 ){
            //get list of resource of PC
            $conds = array(
                'profit_center_id' => $conditions['profit_center_id'],
                'NOT' => array('id' => $presenseResources)
            );
            if( !empty($conditions['employee_id']) ){
                $conds['id'] = $conditions['employee_id'];
            }
            $resources = $this->Employee->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname'),
                'conditions' => $conds
            ));
            foreach ($resources as $res) {
                $d = $res['Employee'];
                if( !in_array($d['id'], $presenseResources) ){
                    $eid = 'employee-' . $d['id'];
                    $staffings[$eid] = array(
                        'id' => $eid,
                        'isEmployee' => 1,
                        'employee_id' => $d['id'],
                        'name' => $d['fullname'],
                        'family' => '',
                        'subfamily' => '',
                        'family_id' => -1,
                        'subfamily_id' => -1,
                        'data' => array()
                    );
                    // $fid = $eid . '-0';
                    // $sf = $fid . '-0';
                    // $staffings[$fid] = array(
                    //     'id' => $fid,
                    //     'employee_id' => $d['id'],
                    //     'isFamily' => 1,
                    //     'family' => '',
                    //     'subfamily' => '',
                    //     'family_id' => -1,
                    //     'subfamily_id' => -1,
                    //     'name' => __('No Family Name', true),
                    //     'data' => array()
                    // );
                    // $staffings[$sf] = array(
                    //     'id' => $sf,
                    //     'employee_id' => $d['id'],
                    //     'family' => __('No Family Name', true),
                    //     'isSubfamily'=>1,
                    //     'subfamily' => '',
                    //     'family_id' => !empty($_family_id) ? $_family_id : null,
                    //     'subfamily_id' => -1,
                    //     'name' => __('No Sub-Family Name', true),
                    //     'data' => array()
                    // );
                    // //}
                    // $staffings[] = array(
                    //     'id' => 0,
                    //     'isActivity'=> 1,
                    //     'employee_id' => $d['id'],
                    //     'family' => __('No Family Name', true),
                    //     'subfamily' => __('No Sub-Family Name', true),
                    //     'family_id' => null,
                    //     'subfamily_id' => null,
                    //     'name' => __('No Name', true),
                    //     'data' => array()
                    // );
                }
            }
        }
		return $staffings;
     }
     private function _visionsStaffingFamily($activities = null, $conditions = null, $startDateFilter = null, $endDateFilter = null){
        ini_set('memory_limit', '512M');
        $this->loadModel('Activity');
        $this->loadModel('Project');
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ActivityFamily');
        $employeeName = $this->_getEmployee();

        $listFamilies = array();
        $staffings = array();

		$_fields=array(
					'TmpStaffingSystem.model_id',
					'project_id',
					'activity_id',
					'SUM(TmpStaffingSystem.consumed) as consumed',
					'TmpStaffingSystem.company_id',
					'TmpStaffingSystem.date',
					'SUM(TmpStaffingSystem.estimated) as estimated',
					'Activity.id',
					'Activity.name',
					'Activity.family_id',
					//'Activity.subfamily_id'
				);
		$_joins = array(
					array(
						'table' => 'activities',
						'alias' => 'Activity',
						'type' => 'LEFT',
						'foreignKey' => 'activity_id',
						'conditions'=> array(
							'TmpStaffingSystem.activity_id = Activity.id',
						)
					)
				);
		$_group=array('Activity.family_id','TmpStaffingSystem.date');
		$arrTmp = array();
        if(!empty($activities)){
            //$listFamilies = Set::combine($activities, '{n}.Activity.id', '{n}.Activity.family_id');
            $listIdActivitys = Set::classicExtract($activities, '{n}.Activity.id');
            if(!empty($conditions)){
                if(!empty($conditions['employee_id'])){
                    $getDatas = $this->TmpStaffingSystem->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'TmpStaffingSystem.activity_id' => $listIdActivitys,
                            'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                            'TmpStaffingSystem.model_id' => $conditions['employee_id'],
                            'model' => 'employee',
                            'TmpStaffingSystem.company_id' => $employeeName['company_id']
                        ),
						'fields' => $_fields,
						'joins' => $_joins,
						'group' => $_group
                    ));
                } else {
                    if(!empty($conditions['profit_center_id'])){
                        $getDatas = $this->TmpStaffingSystem->find('all', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'TmpStaffingSystem.activity_id' => $listIdActivitys,
                                'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                                'TmpStaffingSystem.model_id' => $conditions['profit_center_id'],
                                'model' => 'profit_center',
                                'TmpStaffingSystem.company_id' => $employeeName['company_id']
                            ),
							'fields' => $_fields,
							'joins' => $_joins,
							'group' => $_group
                        ));

                    }
                }
				//IF BY DAY
				if($this->params['url']['aDateType']==1 || $this->params['url']['aDateType']==2)
				{
					$arrTmp = $this->TmpStaffingSystem->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'TmpStaffingSystem.activity_id' => $listIdActivitys,
							'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
							'model' => 'employee',
							//'consumed <>' => 0,
							'TmpStaffingSystem.company_id' => $employeeName['company_id']
						),
						'fields'=> array('activity_id','activity_id'),
					));
				}
            } else {
                $getDatas = $this->TmpStaffingSystem->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'TmpStaffingSystem.activity_id' => $listIdActivitys,
                        'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                        'model' => 'profit_center',
                        'TmpStaffingSystem.company_id' => $employeeName['company_id']
                    ),
					'fields' => $_fields,
					'joins' => $_joins,
					'group' => $_group
                ));
				//IF BY DAY
				if($this->params['url']['aDateType']==1 || $this->params['url']['aDateType']==2)
				{
					$arrTmp = $this->TmpStaffingSystem->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'TmpStaffingSystem.activity_id' => $listIdActivitys,
							'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
							'model' => 'employee',
							//'consumed <>' => 0,
							'TmpStaffingSystem.company_id' => $employeeName['company_id']
						),
						'fields'=> array('activity_id','activity_id'),
					));
				}
            }
            /*if(!empty($getDatas)){
                foreach($getDatas as $key => $getData){
                    $dx = $getData['TmpStaffingSystem'];
                    $idFamily = !empty($listFamilies[$dx['activity_id']]) ? $listFamilies[$dx['activity_id']] : 0;
                    $getDatas[$key]['TmpStaffingSystem']['family_id'] = $idFamily;
                }
            }*/
            $datas = array();
			//debug($getDatas);exit;
            if(!empty($getDatas)){
                foreach($getDatas as $getData){
                    $dx = array_merge($getData['TmpStaffingSystem'],$getData['Activity'],$getData[0]);
                     //date
                    $datas[$dx['family_id']][$dx['date']]['date'] = $dx['date'];
                    // workload
                    $datas[$dx['family_id']][$dx['date']]['validated'] = $dx['estimated'];
                    // consumed
                    $datas[$dx['family_id']][$dx['date']]['consumed'] = $dx['consumed'];
                    $datas[$dx['family_id']][$dx['date']]['remains'] = round($datas[$dx['family_id']][$dx['date']]['validated'] - $datas[$dx['family_id']][$dx['date']]['consumed'], 2);
                }
            }
            /*if(!empty($datas)){
                $tmpDatas = $datas;
                $datas = $this->_resetRemainSystem($tmpDatas);
            }*/
            $activityFamilies = $this->ActivityFamily->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id']),
                'fields' => array('id', 'name')
            ));
            $activityFamilies['999999999'] = __('Not Affected', true);
            $activityFamilies[''] = 'No Name';
			//IF BY DAY
			if($this->params['url']['aDateType'] == 1 || $this->params['url']['aDateType'] == 2)
			{
				$lib = new LibBehavior();
				$this->loadModel('ProjectEmployeeProfitFunctionRefer');
				$this->loadModel('ActivityRequest');
				$this->loadModel('ActivityTask');
				$this->loadModel('Activity');
				$this->loadModel('NctWorkload');
				$pcRefers = array();
				if(!empty($conditions)){
					if(!empty($conditions['employee_id'])){
						$listEmployees = $conditions['employee_id'];
					} else {
						if(!empty($conditions['profit_center_id'])){
							$listEmployees = $this->ProjectEmployeeProfitFunctionRefer->getEmployeesOfPCs($conditions['profit_center_id']);
							$pcRefers = $conditions['profit_center_id'];
						}
					}
				}
				else
				{
					$listEmployees = array();
				}
			}
            if(!empty($datas)){
                foreach($datas as $id => $data){
					//IF BY DAY
					if($this->params['url']['aDateType'] == 1 || $this->params['url']['aDateType'] == 2)
					{
						$dateTypeTmp = $this->params['url']['aDateType']==1 ? 'day' : 'week' ;
						$listIdActivitysTmp = $this->Activity->find('list',array(
								'recursive' => -1,
								'conditions' => array(
									'family_id' => $id,
									'Activity.id' => $arrTmp,
								),
								'fields'=>array(
									'id','id'
								)
							)
						);
						$listIdActivitysTmp = array_values($listIdActivitysTmp);
						$dataConsumed = $this->ActivityRequest->getConsumedByDate($startDateFilter, $endDateFilter, $employeeName['company_id'], $listIdActivitysTmp, $listEmployees);
						/*----------FIX BUG nct workload shared equally for workdays,
						according to the principle : nct workload assign to resources by day
						------------------------------------------------------------*/
						//version 5 : get nct task, group by activity-resource-date
						//1 get nct tasks, includes : project task and activity task
						$listNctTasks = $this->ActivityTask->getActivityNctTask($listIdActivitysTmp);
						//2 SUM workload value group by date with conditions
						$resources = array(
							'pc' => $pcRefers,
							'employee' => $listEmployees
						);
						$nctWorkloads = $this->NctWorkload->getNctWorkloadByResource($startDateFilter, $endDateFilter, $listNctTasks, $resources);
						$nctWorkloadsByDay = $nctWorkloads['day'];
						$nctWorkloads = $nctWorkloads['month'];
						//end
						$dataByDay = array();
						/*---------------------------------
						workload by month
						---------------------------------*/
						foreach($data as $time => $_DATA)
						{
							$keyNct = date('d-m-Y',$time);
							$nctWorkload = isset($nctWorkloads[$keyNct]) ? $nctWorkloads[$keyNct] : 0 ;
							$_DATA['validated'] = isset($_DATA['validated']) ? $_DATA['validated'] : 0 ;
							$VALIDATE =$_DATA['validated'] - $nctWorkload;
							$_dataByDay = $this->estimatedDataFromMonthToDay(array('validate'=>$VALIDATE,'nct' => $nctWorkloadsByDay),$dataConsumed,$lib->getWorkingDaysInMonth($time,true),$lib,$dateTypeTmp);
							$dataByDay = $dataByDay + $_dataByDay;
						}
						$data = $dataByDay ;
					}
					//END
                    $staffings[] = array(
                        'id' => $id,
                        'name' => isset($activityFamilies[$id]) ? $activityFamilies[$id] : 'No Name',
                        'data' => $data
                    );
                }
            }
        }
        return $staffings;
     }
     private function _visionsStaffingProfitCenter($activities = null, $conditions = null, $startDateFilter = null, $endDateFilter = null){
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProfitCenter');
        $this->loadModel('Project');
        $employeeName = $this->_getEmployee();

        if(!empty($activities)){
            $listIdActivitys = Set::classicExtract($activities, '{n}.Activity.id');
            if(!empty($conditions)){
                $getDatas = $this->TmpStaffingSystem->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'TmpStaffingSystem.activity_id' => $listIdActivitys,
                        'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                        'TmpStaffingSystem.model_id' => $conditions,
                        'model' => 'profit_center',
                        'TmpStaffingSystem.company_id' => $employeeName['company_id']
                    )
                ));
            } else {
                $getDatas = $this->TmpStaffingSystem->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'TmpStaffingSystem.activity_id' => $listIdActivitys,
                        'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                        'model' => 'profit_center',
                        'TmpStaffingSystem.company_id' => $employeeName['company_id']
                    )
                ));
            }
            $datas = array();
            if(!empty($getDatas)){
                foreach($getDatas as $getData){
                    $dx = $getData['TmpStaffingSystem'];
                     //date
                    $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                    // workload
                    if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                        $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                    }
                    $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                    // consumed
                    if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                        $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                    }
                    $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                    $datas[$dx['model_id']][$dx['date']]['remains'] = round($datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'], 2);
                }
            }
            if(!empty($datas)){
                $tmpDatas = $datas;
                $datas = $this->_resetRemainSystem($tmpDatas);
            }
        }
        $profitCenter = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        $profitCenter['999999999'] = __('Not Affected', true);
        $profitCenter[''] = 'No Name';
        $staffings = array();
        if(!empty($datas)){
            foreach($datas as $id => $data){
                $staffings[] = array(
                    'id' => $id,
                    'name' => isset($profitCenter[$id]) ? $profitCenter[$id] : 'No Name',
                    'data' => $data
                );
            }
        }
        return $staffings;
     }
	 private function _visionsStaffingProfitCenterPC($activities = null, $conditions = null, $startDateFilter = null, $endDateFilter = null){
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProfitCenter');
        $this->loadModel('Project');
        $employeeName = $this->_getEmployee();
        //load data month from Period
		$showMonthMultiple = false;
		$listMonthPeriod = array();
        /**
         * Hide multiple month
         */
		//if(isset($this->companyConfigs['staffing_by_month_multiple']) && $this->companyConfigs['staffing_by_month_multiple'] == 1 && $this->params['url']['aDateType']==3)
//		{
//			$showMonthMultiple = true ;
//			list($listMonthPeriod,$startDateFilter,$endDateFilter) = $this->Session->read('MonthMultiple');
//		}
        //End
		$sharedEqually = false;
		if($this->params['url']['aDateType']==1 || $this->params['url']['aDateType']==2 || $showMonthMultiple)
		{
			/*---------------------------------------------------------------
			if filter by day-week-month multiple (get data from Period table)
			apply algorithm shared equally workload form month to day.
			----------------------------------------------------------------*/
			$sharedEqually = true;
		}
		//end
        if(!empty($activities)){
            $listIdActivitys = Set::classicExtract($activities, '{n}.Activity.id');
			$_fieldsSum=array(
						'TmpStaffingSystem.model_id',
						'project_id',
						'activity_id',
						'SUM(TmpStaffingSystem.consumed) as consumed',
						'TmpStaffingSystem.company_id',
						'TmpStaffingSystem.date',
						'SUM(TmpStaffingSystem.estimated) as estimated'
					);
			$_joins = array(
						array(
							'table' => 'profit_centers',
							'alias' => 'PC',
							'type' => 'LEFT',
							'foreignKey' => 'id',
							'conditions'=> array(
								'TmpStaffingSystem.model_id = PC.id',
							)
						)
					);
			$_order = array('pc.lft');
			$_group=array('TmpStaffingSystem.date');
			$arrTmp = array();
			foreach($conditions as $_index=>$_pc)
			{
				$getAllChildrenOfPC[$_pc] = $this->ProfitCenter->children($_pc);
				$getAllChildrenOfPC[$_pc] = Set::classicExtract($getAllChildrenOfPC[$_pc],'{n}.ProfitCenter.id');
				$getAllChildrenOfPC[$_pc][] = $_pc;
				if($_pc=='999999999')
				{
					$_pc=999999999;
					$getAllChildrenOfPC[$_pc]=999999999;
				}
				$getDatas[$_pc] = $this->TmpStaffingSystem->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
						'TmpStaffingSystem.activity_id' => $listIdActivitys, // van ne nam o cho nay
                        'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
                        'TmpStaffingSystem.model_id' => $getAllChildrenOfPC[$_pc], // va cho nay
                        'model' => 'profit_center',
                        'TmpStaffingSystem.company_id' => $employeeName['company_id']
                    ),
					'fields'=>$_fieldsSum,
					'group'=>$_group
                ));
				//IF BY DAY
				if($sharedEqually)
				{
					$getDatasTmp = $this->TmpStaffingSystem->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'TmpStaffingSystem.activity_id' => $listIdActivitys, // van ne nam o cho nay
							'date BETWEEN ? AND ?' => array($startDateFilter, $endDateFilter),
							'TmpStaffingSystem.model_id' => $getAllChildrenOfPC[$_pc], // va cho nay
							'model' => 'profit_center',
							//'consumed <>' => 0,
							'TmpStaffingSystem.company_id' => $employeeName['company_id']
						),
						'fields'=> array('activity_id','activity_id'),
					));
					$arrTmp += $getDatasTmp;
				}

			}
			$arrTmp = array_values($arrTmp);
			$datas = array();
            if(!empty($getDatas)){
                foreach($getDatas as $_pc=>$_getData){
					foreach($_getData as $_m=>$getData)
					{
						$dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);

						$datas[$_pc]['val'][$dx['date']]['date'] = $dx['date'];
						// workload
						if(!isset($datas[$_pc]['val'][$dx['date']]['validated'])){
							$datas[$_pc]['val'][$dx['date']]['validated'] = 0;
						}
						$datas[$_pc]['val'][$dx['date']]['validated'] = $dx['estimated'];
						// consumed
						if(!isset($datas[$_pc]['val'][$dx['date']]['consumed'])){
							$datas[$_pc]['val'][$dx['date']]['consumed'] = 0;
						}
						$datas[$_pc]['val'][$dx['date']]['consumed'] = $dx['consumed'];
						$datas[$_pc]['val'][$dx['date']]['remains'] = round($datas[$_pc]['val'][$dx['date']]['validated'] - $datas[$_pc]['val'][$dx['date']]['consumed'], 2);
					}
					//GET INFORMATION FOR PC
					$dataOfPC=ClassRegistry::init('ProfitCenter')->find(
						'first',
						array(
							'recursive' => -1,
							'conditions' => array(
								'id'=>$_pc
							),
							'fields'=>array('name','parent_id'),
						)
					);
					$datas[$_pc]['data']['name'] = $dataOfPC['ProfitCenter']['name'];
					$datas[$_pc]['data']['parent'] = $dataOfPC['ProfitCenter']['parent_id'];
                }
            }
            /*if(!empty($datas)){
                $tmpDatas = $datas;
                $datas = $this->_resetRemainSystem($tmpDatas);
            }*/
        }

		//GET LEVEL FOR PC
		$listProfitCenters = ClassRegistry::init('ProfitCenter')->generateTreeList(array('company_id' => $employeeName['company_id']),null,null,' [--] ',-1);

		$listProfitCenters[999999999]='ABC';
		//debug($listProfitCenters);
		$_strFind=']--[';
		$levelForPC=array();
		foreach($listProfitCenters as $key=>$text)
		{
			$_position=0;
			$_position=strlen(strstr(strrev($text), $_strFind));
			switch($_position){
                case 0:
					$level=0;
					break;
				case 5:
					$level=1;
					break;
				case 11:
					$level=2;
					break;
				case 17:
					$level=3;
					break;
				case 23:
					$level=4;
					break;
				case 29:
					$level=5;
					break;
				case 35:
					$level=6;
					break;
				case 41:
					$level=7;
					break;
				default:
					$level=8;
					break;
			}
			$levelForPC[$key]=$level;
			if(isset($datas[$key]))
			{
				$datasTmp[$key]=$datas[$key];
			}
		}
		//debug($levelForPC);
		$datas=isset($datasTmp)?$datasTmp:null;
		//END
		//CALL FUNCTION BEHAVIOR
		if($sharedEqually)
		{
			$this->loadModel('ActivityTask');
			$this->loadModel('NctWorkload');
			$this->loadModel('ActivityRequest');
			$this->loadModel('ProjectEmployeeProfitFunctionRefer');
			$lib = new LibBehavior();
			$taskLists = $this->ActivityTask->getActivityTaskNotSpecialAndNct($arrTmp);
			/*--------------OPTIMIZE-------------*/
			//get working day for months
			$listWorkingDays = array();
			foreach($listMonthPeriod as $key => $val)
			{
				$listWorkingDays[$key] = $lib->getWorkingDaysInMonth($key,true);
			}
			//end
			//filter records from table Activity Requests => limited query => optimize execute time
			//$recordsRequest = $this->ActivityRequest->filterRecordsByDateAndCompany($startDateFilter, $endDateFilter, $employeeName['company_id']);
			//end
		}
		//END
        $staffings = array();
        if(!empty($datas)){
            // $isFirst = true;
            foreach($datas as $id => $_data){
                // if( $isFirst ){
                //     $isFirst = false;
                // }
                $data = isset($_data['val'])?$_data['val']:array();
                $parent = isset($_data['data']['parent']) ? $_data['data']['parent'] : 0;
                //quyet fixed this
                $isParent = !isset($datas[$parent]) ? 1 : 0;
				$listParentOfPC = array();
				$listParentOfPC = ClassRegistry::init('ProfitCenter')->getPath($id);
				$listParentOfPC = Set::classicExtract($listParentOfPC,'{n}.ProfitCenter.id');
				if(!empty($listParentOfPC))
				{
					array_pop($listParentOfPC);
				}

				//IF BY DAY
				if($sharedEqually)
				{
					$dateTypeTmp = $this->params['url']['aDateType']==1 || $showMonthMultiple ? 'day' : 'week' ;
					$listEmployees = $this->ProjectEmployeeProfitFunctionRefer->getEmployeesOfPCs($getAllChildrenOfPC[$id]);
					$dataConsumed = array();
					if(!empty($listEmployees))
					{
						$dataConsumed = $this->ActivityRequest->getConsumedByDate($startDateFilter, $endDateFilter, $employeeName['company_id'], $arrTmp , $listEmployees, $taskLists);
					}
					/*----------FIX BUG nct workload shared equally for workdays,
					according to the principle : nct workload assign to resources by day
					------------------------------------------------------------*/
					//version 5 : get nct task, group by activity-resource-date
					//1 get nct tasks, includes : project task and activity task
					$listNctTasks = $this->ActivityTask->getActivityNctTask($arrTmp);
					//2 SUM workload value group by date
					$resources = array(
						'pc' => $getAllChildrenOfPC[$id],
						'employee' => $listEmployees
					);
					$nctWorkloads = $this->NctWorkload->getNctWorkloadByResource($startDateFilter, $endDateFilter, $listNctTasks, $resources);
					$nctWorkloadsByDay = $nctWorkloads['day'];
					$nctWorkloads = $nctWorkloads['month'];
					//end
					$dataByDay = array();
					foreach($data as $time => $_DATA)
					{
						$keyNct = date('d-m-Y',$time);
						$nctWorkload = isset($nctWorkloads[$keyNct]) ? $nctWorkloads[$keyNct] : 0 ;
						$_DATA['validated'] = isset($_DATA['validated']) ? $_DATA['validated'] : 0 ;
						$VALIDATE =$_DATA['validated'] - $nctWorkload;
						$workingDays = isset($listWorkingDays[$time]) ? $listWorkingDays[$time] : $lib->getWorkingDaysInMonth($time,true) ;
						$_dataByDay = $this->estimatedDataFromMonthToDay(array('validate'=>$VALIDATE,'nct' => $nctWorkloadsByDay),$dataConsumed,$workingDays,$lib,$dateTypeTmp);
						$dataByDay = $dataByDay + $_dataByDay;
					}
					$data = $dataByDay;
					if($showMonthMultiple)
					{
						$data = $this->groupDataFromDayToMonth($data,$listMonthPeriod);
					}
				}
				//END
                $staffings[] = array(
                    'id' => 'pc-' . $id,
					'isFirst' => $isParent, //quyet fixed this: use $parent
					'arrParent'=> $listParentOfPC,
					'level'=>isset($levelForPC[$id])?$levelForPC[$id]:0,
					'parent' => $parent,
                    'name' => isset($_data['data']['name']) ? $_data['data']['name'] : __('Not Affected', true),
                    'data' => $data
                );
            }
        }
        $e = end($staffings);
        reset($staffings);

        if( $e['id'] == 'pc-999999999' ){
            if( !$this->settings['show_na'] || ( !$this->settings['show_empty_na'] && empty($e['data']) ) ){
                array_pop($staffings);
            }
        }
        //a few markup: get ItMe PC's name
        if( isset($_GET['ItMe']) ){
            $p = $this->ProfitCenter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $_GET['ItMe']
                )
            ));
            $this->set('pcName', $p['ProfitCenter']['name']);
        }
        return $staffings;
     }
	 function groupDataFromDayToMonth($valOfActivity,$listMonthPeriod)
	 {
		$_valOfActivity = $valOfActivity;
		$valOfActivity = array();
		foreach($listMonthPeriod as $time => $val)
		{
			foreach($_valOfActivity as $_time => $_val)
			{
				if($val['start'] <= $_time &&  $_time <= $val['end'])
				{
					if(!isset($valOfActivity[$time]))
					{
						$valOfActivity[$time] = array(
							'validated' => 0 ,
							'consumed' => 0,
							'remain' => 0,
							'date' => $time
						);
					}
					$valOfActivity[$time]['validated'] += $_val['validated'];
					$valOfActivity[$time]['consumed'] += $_val['consumed'];
					$valOfActivity[$time]['date'] += $_val['date'];
					unset($_valOfActivity[$_time]); //data da duoc gan vao thang thi remove no di => tang performance.
				}
			}
		}
		return $valOfActivity;
	 }
     function estimatedDataFromMonthToDay($workload,$consumed,$dates,$lib,$dateType = 'day', $number=2)
	 {
		if(isset($workload['nct']))
		{
			$nctWorkload = $workload['nct'];
			$workload = $workload['validate'];
		}
		$count = count($dates);
		$count = $count*$number;
		$workloadByDay=$lib->caculateGlobal($workload, $count, true);
		$estisW = $workloadByDay['original'] * $number;
		$remainderW = $workloadByDay['remainder'];
		$numberOfRemainderW = $workloadByDay['number'];
		$data = array();
		$i=1;
		foreach($dates as $_date)
		{
			$_data['validated'] = $estisW;
			if($numberOfRemainderW >= $i)
			{
				$_data['validated'] += $remainderW;
			}
			//ADD nct workload
			$keyNct = date('d-m-Y', $_date);
			if(isset($nctWorkload[$keyNct]))
			{
				$_data['validated'] += $nctWorkload[$keyNct];
			}
			//END
			$_data['consumed'] = isset($consumed[$_date]) ? $consumed[$_date] : 0;
			$_data['remains'] = 0;
			$_data['date'] = $_date;
			if($dateType == 'week')
			{
				$keyWeek = $lib->getDateByWeekday($_date);
				if(!isset($data[$keyWeek]))
				{
					$data[$keyWeek]['validated'] = $data[$keyWeek]['consumed'] = $data[$keyWeek]['remains'] = 0;
					$data[$keyWeek]['date'] = $keyWeek;
				}
				$data[$keyWeek]['validated'] += $_data['validated'];
				$data[$keyWeek]['consumed'] += $_data['consumed'];
			}
			else
			{
				$data[$_date] = $_data;
			}
			$i++;
		}
		return $data;
	 }
    /*********************************************************END HANDLE STAFFING+ ********************************************************************/


    /**
     * Huupc xay dung lai staffing+ cho activity doi voi truong hop pms = no
     *
     *
     *
     */
    private function _worloadFromTasks($activity_id = null){
        $activityTasks = $this->ActivityTask->find('all', array(
                'conditions' => array(
                        'ActivityTask.activity_id' => $activity_id
                    ),
                'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload', 'name')
            ));
        $parentIds = array_unique(Set::classicExtract($activityTasks, '{n}.ActivityTask.parent_id'));
        foreach($activityTasks as $key => $activityTask){
            foreach($parentIds as $parentId){
                if($activityTask['ActivityTask']['id'] == $parentId){
                    unset($activityTasks[$key]);
                }
            }
        }
        $notAssignTos = $formats = $formatPCs = array();
        foreach($activityTasks as $key => $activityTask){
            if(empty($activityTask['ActivityTaskEmployeeRefer'])){
                $notAssignTos[$key][999999999]['task_start_date'] = $activityTask['ActivityTask']['task_start_date'];
                $notAssignTos[$key][999999999]['task_end_date'] = $activityTask['ActivityTask']['task_end_date'];
                $notAssignTos[$key][999999999]['estimated'] = $activityTask['ActivityTask']['estimated'] + $activityTask['ActivityTask']['overload'];
            } else {
                if(count($activityTask['ActivityTaskEmployeeRefer']) == 1){
                    if($activityTask['ActivityTaskEmployeeRefer'][0]['is_profit_center'] == 0){
                        $formats[$key][$activityTask['ActivityTaskEmployeeRefer'][0]['reference_id']]['task_start_date'] = $activityTask['ActivityTask']['task_start_date'];
                        $formats[$key][$activityTask['ActivityTaskEmployeeRefer'][0]['reference_id']]['task_end_date'] = $activityTask['ActivityTask']['task_end_date'];
                        $formats[$key][$activityTask['ActivityTaskEmployeeRefer'][0]['reference_id']]['estimated'] = $activityTask['ActivityTask']['estimated'] + $activityTask['ActivityTask']['overload'];
                    } else {
                        $formatPCs[$key][$activityTask['ActivityTaskEmployeeRefer'][0]['reference_id']]['task_start_date'] = $activityTask['ActivityTask']['task_start_date'];
                        $formatPCs[$key][$activityTask['ActivityTaskEmployeeRefer'][0]['reference_id']]['task_end_date'] = $activityTask['ActivityTask']['task_end_date'];
                        $formatPCs[$key][$activityTask['ActivityTaskEmployeeRefer'][0]['reference_id']]['estimated'] = $activityTask['ActivityTask']['estimated'] + $activityTask['ActivityTask']['overload'];
                    }
                } else {
                    //$overloads = $activityTask['ActivityTask']['overload']/count($activityTask['ActivityTaskEmployeeRefer']);

                    $gOverload = $this->Lib->caculateGlobal($activityTask['ActivityTask']['overload'], count($activityTask['ActivityTaskEmployeeRefer']));
                    $overloads = $gOverload['original'];
                    $remainder = $gOverload['remainder'];
                    end($activityTask['ActivityTaskEmployeeRefer']);
                    $endKey = key($activityTask['ActivityTaskEmployeeRefer']);

                    foreach($activityTask['ActivityTaskEmployeeRefer'] as $keys => $value){
                        if($value['is_profit_center'] == 0){
                            $formats[$key][$value['reference_id']]['task_start_date'] = $activityTask['ActivityTask']['task_start_date'];
                            $formats[$key][$value['reference_id']]['task_end_date'] = $activityTask['ActivityTask']['task_end_date'];
                            $formats[$key][$value['reference_id']]['estimated'] = $value['estimated'] + $overloads;
                            if($endKey == $keys){
                                $formats[$key][$value['reference_id']]['estimated'] = $value['estimated'] + $overloads + $remainder;
                            }
                        } else {
                            $formatPCs[$key][$value['reference_id']]['task_start_date'] = $activityTask['ActivityTask']['task_start_date'];
                            $formatPCs[$key][$value['reference_id']]['task_end_date'] = $activityTask['ActivityTask']['task_end_date'];
                            $formatPCs[$key][$value['reference_id']]['estimated'] = $value['estimated'] + $overloads;
                            if($endKey == $keys){
                                $formatPCs[$key][$value['reference_id']]['estimated'] = $value['estimated'] + $overloads + $remainder;
                            }
                        }
                    }
                }
            }
        }
        /** ************************************* Xu ly employee ********************************************/
        $attachmentEmploys = $formats;
        $formats = array_merge($formats, $notAssignTos);
        if(!empty($formatPCs)){
            $notPCs = array();
            foreach($formatPCs as $key => $formatPC){
                $while = 0;
                foreach($formatPC as $id => $vl){
                    $notPCs[$key][$while][999999999]['task_start_date'] = $vl['task_start_date'];
                    $notPCs[$key][$while][999999999]['task_end_date'] = $vl['task_end_date'];
                    $notPCs[$key][$while][999999999]['estimated'] = $vl['estimated'];
                    $while++;
                }
            }
            if(!empty($notPCs)){
                foreach($notPCs as $notPC){
                    foreach($notPC as $val){
                        $merData[] = $val;
                    }
                }
            }
            if(!empty($merData)){
                $formats = array_merge($formats, $merData);
            }
        }

        $_formats = array();
        if(!empty($formats)){
            foreach($formats as $format){
                $while = 0;
                foreach($format as $id => $values){
                    $_start = !empty($values['task_start_date']) ? $values['task_start_date'] : time();
                    $_end = !empty($values['task_end_date']) ? $values['task_end_date'] : time();
                    $minMonth = !empty($_start) ? date('m', $_start) : '';
                    $minYear = !empty($_start) ? date('Y', $_start) : '';
                    $maxMonth = !empty($_end) ? date('m', $_end) : '';
                    $maxYear = !empty($_end) ? date('Y', $_end) : '';
                    $diffDate = $this->_diffDate($_start, $_end);
                    if($diffDate == 0){
                        $estis = $values['estimated'];
                    } else {
                        $gEstis = $this->Lib->caculateGlobal($values['estimated'], $diffDate);
                        $estis = $gEstis['original'];
                        $remainder = $gEstis['remainder'];
                        //$estis = $values['estimated']/$diffDate;
                    }
                    $rDatas = array();
                    while($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                        $rDatas[$id][strtotime('01-'. $minMonth .'-'. $minYear)]['estimated'] = $estis;
                        if($minYear == $maxYear && $minMonth == $maxMonth){
                            $rDatas[$id][strtotime('01-'. $minMonth .'-'. $minYear)]['estimated'] = $estis + $remainder;
                        }
                        $minMonth++;
                        if ($minMonth == 13) {
                            $minMonth = 1;
                            $minYear++;
                        }
                    }
                    $_formats[] = $rDatas;
                }
            }
        }
        foreach($_formats as $_format){
            foreach($_format as $id => $values){
                foreach($values as $time => $value){
                    $setArrayTimes[] = $time;
                }
            }
        }
        $setArrayTimes = !empty($setArrayTimes) ? array_unique($setArrayTimes) : array();
        if(!empty($setArrayTimes)){
            foreach($setArrayTimes as $setArrayTime){
                foreach($_formats as $_format){
                    foreach($_format as $id => $values){
                        foreach($values as $time => $value){
                            if($setArrayTime == $time){
                                $dataResets[$id][$setArrayTime]['estimated'][] = $value['estimated'];
                            }
                        }
                    }
                }
            }
        }
        if(!empty($dataResets)){
            foreach($dataResets as $id => $dataReset){
                foreach($dataReset as $time => $vls){
                    $_consumeds = array_sum($vls['estimated']);
                    $dataResets[$id][$time]['employee_id'] = $id;
                    $dataResets[$id][$time]['date'] = $time;
                    //@Huupc: Change 12/10/2013
                    $dataResets[$id][$time]['estimated'] = $_consumeds;
                }
            }
        }

        $estimated = isset($dataResets) ? $dataResets : array();
        /** ************************************* end Xu ly employee ********************************************/

         /** ************************************* Xu ly profit center ********************************************/
        if(!empty($attachmentEmploys)){
            foreach($attachmentEmploys as $attachmentEmploy){
                $employee_ids[] = array_keys($attachmentEmploy);
            }
            $getId = array();
            if(!empty($employee_ids)){
                foreach($employee_ids as $employee_id){
                    foreach($employee_id as $key => $value){
                        $getId[] = $value;
                    }
                }
            }
            $getId = array_unique($getId);
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $references = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'employee_id' => $getId,
                        'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                    ),
                    'fields' => array('employee_id', 'profit_center_id'),
                    'order' => array('employee_id')
                ));
            foreach($getId as $employee){
                if(in_array($employee, array_keys($references))){
                    //do nothing
                } else {
                    $references[$employee] = 999999999;
                }
            }
            $_dataPCs = array();
            if(!empty($references)){
                foreach($attachmentEmploys as $key => $data){
                    $while = 0;
                    foreach($data as $id => $vl){
                        $_dataPCs[$key][$while][$references[$id]]['task_start_date'] = $vl['task_start_date'];
                        $_dataPCs[$key][$while][$references[$id]]['task_end_date'] = $vl['task_end_date'];
                        $_dataPCs[$key][$while][$references[$id]]['estimated'] = $vl['estimated'];
                        $while++;
                    }
                }
            }
            if(!empty($_dataPCs)){
                foreach($_dataPCs as $_dataPC){
                    foreach($_dataPC as $val){
                        $merDataPCs[] = $val;
                    }
                }
            }
            if(!empty($merDataPCs)){
                $formatPCs = array_merge($formatPCs, $merDataPCs);
            }
        }
        $formatPCs = array_merge($formatPCs, $notAssignTos);
        $_formatPCs = array();
        if(!empty($formatPCs)){
            foreach($formatPCs as $formatPC){
                $while = 0;
                foreach($formatPC as $id => $values){
                    $_start = !empty($values['task_start_date']) ? $values['task_start_date'] : time();
                    $_end = !empty($values['task_end_date']) ? $values['task_end_date'] : time();
                    $minMonth = !empty($_start) ? date('m', $_start) : '';
                    $minYear = !empty($_start) ? date('Y', $_start) : '';
                    $maxMonth = !empty($_end) ? date('m', $_end) : '';
                    $maxYear = !empty($_end) ? date('Y', $_end) : '';
                    $diffDate = $this->_diffDate($_start, $_end);
                    if($diffDate == 0){
                        $estis = $values['estimated'];
                    } else {
                        //$estis = $values['estimated']/$diffDate;
                        $gEstis = $this->Lib->caculateGlobal($values['estimated'], $diffDate);
                        $estis = $gEstis['original'];
                        $remainder = $gEstis['remainder'];
                    }
                    $rDatas = array();
                    while($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                        $rDatas[$id][strtotime('01-'. $minMonth .'-'. $minYear)]['estimated'] = $estis;
                        if($minYear == $maxYear && $minMonth == $maxMonth){
                            $rDatas[$id][strtotime('01-'. $minMonth .'-'. $minYear)]['estimated'] = $estis + $remainder;
                        }
                        $minMonth++;
                        if ($minMonth == 13) {
                            $minMonth = 1;
                            $minYear++;
                        }
                    }
                    $_formatPCs[] = $rDatas;
                }
            }
        }
        foreach($_formatPCs as $_formatPC){
            foreach($_formatPC as $id => $values){
                foreach($values as $time => $value){
                    $_setArrayTimes[] = $time;
                }
            }
        }
        $_setArrayTimes = !empty($_setArrayTimes) ? array_unique($_setArrayTimes) : array();
        if(!empty($_setArrayTimes)){
            foreach($_setArrayTimes as $_setArrayTime){
                foreach($_formatPCs as $_formatPC){
                    foreach($_formatPC as $id => $values){
                        foreach($values as $time => $value){
                            if($_setArrayTime == $time){
                                $_dataResets[$id][$_setArrayTime]['estimated'][] = $value['estimated'];
                            }
                        }
                    }
                }
            }
        }
        if(!empty($_dataResets)){
            foreach($_dataResets as $id => $_dataReset){
                foreach($_dataReset as $time => $vls){
                    $_consumeds = array_sum($vls['estimated']);
                    $_dataResets[$id][$time]['employee_id'] = $id;
                    $_dataResets[$id][$time]['date'] = $time;
                    //@Huupc: Change 12/10/2013
                    $_dataResets[$id][$time]['estimated'] = $_consumeds;
                }
            }
        }
        $estimatedPcs = isset($_dataResets) ? $_dataResets : array();
        /** ************************************* end Xu ly profit center ********************************************/
        $results['employee'] = $estimated;
        $results['profit'] = $estimatedPcs;

        return $results;
    }

    private function _consumedFromTasks($activity_id = null){
        $this->loadModel('ActivityRequest');

        $taskIds = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id),
                'fields' => array('id', 'id')
            ));
        $datas = array();
        if(!empty($taskIds)){
            $datas = $this->ActivityRequest->find('all',array(
                'recursive'     => -1,
                'fields'        => array('id', 'date', 'employee_id', 'task_id', 'value'),
                'conditions'    => array(
                    'status'        => 2,
                    'task_id'       => $taskIds,
                    'company_id'    => $this->employee_info['Company']['id'],
                    'NOT'           => array('value' => 0),
                )
            ));
        }
        $previous = $this->ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array('id', 'date', 'employee_id', 'value'),
            'conditions'    => array(
                'status'        => 2,
                'activity_id'   => $activity_id,
                'company_id'    => $this->employee_info['Company']['id'],
                'NOT'           => array('value' => 0),
            )
        ));
        /** ***************************** xu ly previous employee***************************************************/
        $consumedPr = array();
        if(!empty($previous)){
            $employPrevious = array_unique(Set::classicExtract($previous, '{n}.ActivityRequest.employee_id'));
            $formatPrs = array();
            foreach($employPrevious as $vl){
                foreach($previous as $key => $data){
                    if($vl == $data['ActivityRequest']['employee_id']){
                        $formatPrs[$vl][$key]['date'] = $data['ActivityRequest']['date'];
                        $formatPrs[$vl][$key]['value'] = $data['ActivityRequest']['value'];
                    }
                }
            }
            $filterKeyPrs = array_unique(Set::classicExtract($previous, '{n}.ActivityRequest.date'));

            if(!empty($filterKeyPrs)){
                foreach($filterKeyPrs as $filterKey){
                    $_filterKeys[] = date('m-Y', $filterKey);
                }
                $filterKeyPrs = array_unique($_filterKeys);
            }
            if(!empty($filterKeyPrs)){
                foreach($filterKeyPrs as $filterKey){
                    foreach($formatPrs as $id => $format){
                        $count = 0;
                        foreach($format as $value){
                            if($filterKey == date('m-Y', $value['date'])){
                                $consumedPr[$id][strtotime('01-'.$filterKey)]['date'] = strtotime('01-'.$filterKey);
                                $count += $value['value'];
                                $consumedPr[$id][strtotime('01-'.$filterKey)]['consumed'] = $count;
                            }
                        }
                    }
                }
            }
        }
        /** ***************************** end xu ly previous employee***************************************************/

        /** ***************************** xu ly employee ***************************************************/
        if(!empty($datas)){
            $employee_id = array_unique(Set::classicExtract($datas, '{n}.ActivityRequest.employee_id'));
            $formats = array();
            foreach($employee_id as $vl){
                foreach($datas as $key => $data){
                    if($vl == $data['ActivityRequest']['employee_id']){
                        $formats[$vl][$key]['date'] = $data['ActivityRequest']['date'];
                        $formats[$vl][$key]['value'] = $data['ActivityRequest']['value'];
                    }
                }
            }
            $filterKeys = array_unique(Set::classicExtract($datas, '{n}.ActivityRequest.date'));
        }
        if(!empty($filterKeys)){
            foreach($filterKeys as $filterKey){
                $_filterKeys[] = date('m-Y', $filterKey);
            }
            $filterKeys = array_unique($_filterKeys);
        }
        $consumed = array();
        if(!empty($filterKeys)){
            foreach($filterKeys as $filterKey){
                foreach($formats as $id => $format){
                    $count = 0;
                    foreach($format as $value){
                        if($filterKey == date('m-Y', $value['date'])){
                            $consumed[$id][strtotime('01-'.$filterKey)]['date'] = strtotime('01-'.$filterKey);
                            $count += $value['value'];
                            $consumed[$id][strtotime('01-'.$filterKey)]['consumed'] = $count;
                        }
                    }
                }
            }
        }
        /** ***************************** end xu ly employee ***************************************************/

        /** ***************************** xu ly profit center ***************************************************/
        $PCrefers = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
            'fields' => array('employee_id', 'profit_center_id'),
            'conditions' => array('NOT' => array('profit_center_id' => null, 'profit_center_id' => 0))
        ));
        if(!empty($datas)){
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $employee_ids = array_unique(Set::classicExtract($datas, '{n}.ActivityRequest.employee_id'));
            $references = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'employee_id' => $employee_ids,
                        'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                    ),
                    'fields' => array('employee_id', 'profit_center_id'),
                    'order' => array('employee_id')
                ));
            foreach($datas as $key => $data){
                $datas[$key]['ActivityRequest']['profit_center_id'] = isset($PCrefers[$data['ActivityRequest']['employee_id']]) ? $PCrefers[$data['ActivityRequest']['employee_id']] : '999999999';
                unset($datas[$key]['ActivityRequest']['employee_id']);
            }
        }
        $profit_center_id = array_unique(Set::classicExtract($datas, '{n}.ActivityRequest.profit_center_id'));
        $profit_center_id['999999999'] = '999999999';
        $formats = array();
        foreach($profit_center_id as $vl){
            foreach($datas as $key => $data){
                if($vl == $data['ActivityRequest']['profit_center_id']){
                    $formats[$vl][$key]['date'] = $data['ActivityRequest']['date'];
                    $formats[$vl][$key]['value'] = $data['ActivityRequest']['value'];
                }
            }
        }
        if(!empty($datas)){
            $filterKeyPCs = array_unique(Set::classicExtract($datas, '{n}.ActivityRequest.date'));
        }
        if(!empty($filterKeyPCs)){
            foreach($filterKeyPCs as $filterKeyPC){
                $_filterKeyPCs[] = date('m-Y', $filterKeyPC);
            }
            $filterKeyPCs = array_unique($_filterKeyPCs);
        }
        $consumedPcs = array();
        if(!empty($filterKeys)){
            foreach($filterKeys as $filterKey){
                foreach($formats as $id => $format){
                    $count = 0;
                    foreach($format as $value){
                        if($filterKey == date('m-Y', $value['date'])){
                            $consumedPcs[$id][strtotime('01-'.$filterKey)]['date'] = strtotime('01-'.$filterKey);
                            $count += $value['value'];
                            $consumedPcs[$id][strtotime('01-'.$filterKey)]['consumed'] = $count;
                        }
                    }
                }
            }
        }
        /** ***************************** end xu ly profit center ***************************************************/

        /** ***************************** xu ly previous profit center ***************************************************/
        $consumedPrPcs = array();
        if(!empty($previous)){
            foreach($previous as $key => $data){
                //debug($PCrefers[$data['ActivityRequest']['employee_id']]);
                $previous[$key]['ActivityRequest']['profit_center_id'] = isset($PCrefers[$data['ActivityRequest']['employee_id']]) ? $PCrefers[$data['ActivityRequest']['employee_id']] : '999999999';
                unset($previous[$key]['ActivityRequest']['employee_id']);
            }
            $_profit_center_ids = array_unique(Set::classicExtract($previous, '{n}.ActivityRequest.profit_center_id'));
            $_profit_center_ids['999999999'] = '999999999';
            $formatPrPcs = array();
            foreach($_profit_center_ids as $vl){
                foreach($previous as $key => $data){
                    if($vl == $data['ActivityRequest']['profit_center_id']){
                        $formatPrPcs[$vl][$key]['date'] = $data['ActivityRequest']['date'];
                        $formatPrPcs[$vl][$key]['value'] = $data['ActivityRequest']['value'];
                    }
                }
            }
            if(!empty($previous)){
                $filterKeyPrPCs = array_unique(Set::classicExtract($previous, '{n}.ActivityRequest.date'));
            }
            if(!empty($filterKeyPrPCs)){
                foreach($filterKeyPrPCs as $filterKeyPrPC){
                    $_filterKeyPrPCs[] = date('m-Y', $filterKeyPrPC);
                }
                $filterKeyPrPCs = array_unique($_filterKeyPrPCs);
            }
            if(!empty($filterKeyPrPCs)){
                foreach($filterKeyPrPCs as $filterKeyPrPC){
                    foreach($formatPrPcs as $id => $formatPrPc){
                        $count = 0;
                        foreach($formatPrPc as $value){
                            if($filterKeyPrPC == date('m-Y', $value['date'])){
                                $consumedPrPcs[$id][strtotime('01-'.$filterKeyPrPC)]['date'] = strtotime('01-'.$filterKeyPrPC);
                                $count += $value['value'];
                                $consumedPrPcs[$id][strtotime('01-'.$filterKeyPrPC)]['consumed'] = $count;
                            }
                        }
                    }
                }
            }
        }
        /** ***************************** end xu ly previous profit center ***************************************************/
        $results['employee'] = $consumed;
        $results['profit'] = $consumedPcs;
        $results['previous'] = $consumedPr;
        $results['previousPc'] = $consumedPrPcs;

        return $results;
    }


    /**
     * Merge estimated, consumed, remain
     *
     *
     * @return
     * @access private
     */
     private function _mergeDataFromTasks($consumeds, $previous, $estimateds, $getEstimations){
        $mergeKeys = array_merge(array_keys($consumeds), array_keys($estimateds));
        if(!empty($previous)){
            $mergeKeys = array_merge($mergeKeys, array_keys($previous));
        }
        $mergeKeys = array_unique($mergeKeys);
        $keyDateCons = $keyDateRemain = array();
        foreach($consumeds as $consumed){
            $keyDateCons[] = array_keys($consumed);
        }
        $keyDateEsti = !empty($estimateds) ? Set::classicExtract($estimateds, '{n}.{n}.date') : array();
        $keyDateConPrs = !empty($previous) ? Set::classicExtract($previous, '{n}.{n}.date') : array();
        $totals = array_merge($keyDateCons, $keyDateEsti, $keyDateConPrs);
        $mergeDates = array();
        foreach($totals as $total){
            foreach($total as $vl){
                $mergeDates[] = $vl;
            }
        }
        $mergeDates = array_unique($mergeDates);
        $minDate = $maxDate = time();
        if(!empty($mergeDates)){
           $minDate = min($mergeDates);
           $maxDate = max($mergeDates);
           foreach($getEstimations as $getEstimation){
                foreach($getEstimation as $date => $value){
                    $getDates[] = $date;
                }
           }
           if(!empty($getDates)){
               $getDates = array_unique($getDates);
               $_minDate = isset($getDates) ? min($getDates) : '';
               $_maxDate = isset($getDates) ? max($getDates) : '';
               if($_minDate <= $minDate){
                    $minDate = $_minDate;
               }
               if($_maxDate >= $maxDate){
                    $maxDate = $_maxDate;
               }
           }
           if($maxDate < time()){
                $maxDate = time();
            }
        }
        //$capacities = $this->_capacityFromForecast($mergeKeys, $minDate, $maxDate);
        $datas = array();
        if(!empty($mergeKeys) && !empty($minDate) && !empty($maxDate)){
            foreach($mergeKeys as $mergeKey){
                $minMonth = date('m', $minDate);
                $maxMonth = date('m', $maxDate);
                $minYear = date('Y', $minDate);
                $maxYear = date('Y', $maxDate);
                while ($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                    $datas[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['date'] = strtotime('01-'. $minMonth . '-' . $minYear);
                    $datas[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['estimation'] = isset($getEstimations[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]) ? $getEstimations[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['estimation'] : 0;
                    $cons = isset($consumeds[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]) ? $consumeds[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['consumed'] : 0;
                    $conPrs = isset($previous[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]) ? $previous[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['consumed'] : 0;
                    $datas[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['consumed'] = $cons + $conPrs;
                    $workload = isset($estimateds[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]) ? $estimateds[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['estimated'] : 0;
                    $datas[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['validated'] = $workload + $conPrs;
                    $datas[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['remains'] = $workload - $cons;
                    //$datas[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['capacity'] = !empty($capacities[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['capacity']) ? $capacities[$mergeKey][strtotime('01-'. $minMonth . '-' . $minYear)]['capacity'] : 0;
                    $minMonth++;
                    if ($minMonth == 13) {
                        $minMonth = 1;
                        $minYear++;
                    }
                }
            }
        }
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
                        $_remain = 0;
                    } else {
                        $_remain = $remain/$count;
                    }
                    if(!empty($value['validated']) || $time == $currentDate){
                        $datas[$id][$time]['remains'] = $value['remains'] + $_remain;
                    } else {
                        if(!empty($datas[$id][$currentDate])){
                            if($datas[$id][$currentDate]['validated'] && $datas[$id][$currentDate]['validated'] == 0){
                                $datas[$id][$currentDate]['remains'] = $_remain;
                            }
                        }
                    }
                }
            }
        }

        return $datas;
     }

      /**
     * Merge estimated, consumed, remain of employee
     *
     *
     * @return
     * @access private
     */
     private function _mergeEmployeeFromTasks($activity_id = null){
        $_getConsumeds = $this->_consumedFromTasks($activity_id);
        $consumeds = $_getConsumeds['employee'];
        $previous = $_getConsumeds['previous'];
        //$estimateds = $this->_worloadFromTasks($activity_id);
//        $estimateds = $estimateds['employee'];
//        debug($estimateds);
        $estimateds = $this->_totalTaskOfActivity($activity_id, 0);
        //debug($estimateds); exit;
        $getEstimations = $this->_getAllEstimatedEmployeeFromTasks($activity_id);
        $datas = $this->_mergeDataFromTasks($consumeds, $previous, $estimateds, $getEstimations);

        return $datas;
     }

      /**
     * Staffing of employee
     *
     *
     * @return
     * @access private
     */
     private function _staffingEmloyeeFromTasks($activity_id = null){
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmployee();
        /**
         * Lay du lieu staffing cho employee
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'NOT' => array(
					'AND' => array(
						'estimated' => 0,
						'consumed' => 0
					)
				),
                'activity_id' => $activity_id,
                'model' => 'employee',
                'company_id' => $employeeName['company_id']
            )
        ));
        $datas = array();
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
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        /*if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }*/
        $ids = array_keys($datas);
        $this->loadModel('Employee');
        $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $ids),
                'fields' => array('id', 'fullname'),
                'order' => array('Employee.id')
            ));
        if(!empty($datas['999999999'])){
            $notAffected = array(
                'Employee' => array(
                    'id' => '999999999',
                    'fullname' => __('Not Affected', true)
                )
            );
            $employees[] = $notAffected;
        }
        $staffings = array();
        foreach($employees as $key => $employee){
            $staffings[$key]['id'] = $employee['Employee']['id'];
            $staffings[$key]['is_check'] = 1;
            $staffings[$key]['name'] = $employee['Employee']['fullname'];
            $staffings[$key]['data'] = isset($datas[$employee['Employee']['id']]) ? $datas[$employee['Employee']['id']] : '';
        }

        return $staffings;
     }

      /**
     * Merge estimated, consumed, remain of profit center
     *
     *
     * @return
     * @access private
     */
     private function _mergeProfitCenterFromTasks($activity_id = null){
        $_getConsumeds = $this->_consumedFromTasks($activity_id);
        $consumeds = $_getConsumeds['profit'];
        $previous = $_getConsumeds['previousPc'];
        //$estimateds = $this->_worloadFromTasks($activity_id);
//        $estimateds = $estimateds['profit'];
        $estimateds = $this->_totalTaskOfActivity($activity_id, 1);
        $getEstimations = $this->_getAllEstimatedProfitCenterFromTasks($activity_id);
        $datas = $this->_mergeDataFromTasks($consumeds, $previous, $estimateds, $getEstimations);
        return $datas;
     }

      /**
     * Staffing of employee
     *
     *
     * @return
     * @access private
     */
     private function _staffingProfitCenterFromTasks($activity_id = null){
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmployee();
        /**
         * Lay du lieu staffing cho profit center
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'NOT' => array(
					'AND' => array(
						'estimated' => 0,
						'consumed' => 0
					)
				),
                'activity_id' => $activity_id,
                'model' => 'profit_center',
                'company_id' => $employeeName['company_id']
            )
        ));
        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * profit_center => array(
         *      'time' => array()
         * )
         */
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
                // date
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        /*if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }*/
        $ids = array_keys($datas);
        $this->loadModel('ProfitCenter');
        $profitCenters = $this->ProfitCenter->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProfitCenter.id' => $ids),
                'fields' => array('id', 'name'),
                'order' => array('ProfitCenter.id')
            ));
        if(!empty($datas['999999999'])){
            $notAffected = array(
                'ProfitCenter' => array(
                    'id' => '999999999',
                    'name' => __('Not Affected', true)
                )
            );
            $profitCenters[] = $notAffected;
        }
        $staffings = array();
        foreach($profitCenters as $key => $profitCenter){
            $staffings[$key]['id'] = $profitCenter['ProfitCenter']['id'];
            $staffings[$key]['is_check'] = 2;
            $staffings[$key]['name'] = $profitCenter['ProfitCenter']['name'];
            $staffings[$key]['data'] = isset($datas[$profitCenter['ProfitCenter']['id']]) ? $datas[$profitCenter['ProfitCenter']['id']] : '';
        }

        return $staffings;
     }

     /**
     * Vision gantt chart, view staffings+
     *
     * @return void
     * @access public
     */
    public function visions($activity_id = null, $category = null) {
        $ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
		if(isset($this->companyConfigs['activate_profile']) && $this->companyConfigs['activate_profile'])
		{
			//DO NOTHING
		}
		else
		{
			if($category == 'profile') $category = 'employee' ;
		}
        $this->loadModel('Activity');
        unset($this->helpers['Gantt']);
        $this->helpers['GanttSt'] = null;

        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        /** Edit by Huynh 
        * old: save in section $this->Session->write("Staffing.Cate", $category);
        * New: save in HistoryFilter, key: project_staffing_last_display
        * email: Z0G 25/7/2019 Prod4 task 4.
        *
        */
       $this->loadModel('HistoryFilter');
        if($category != null){
            if( !in_array( $category, array('employee', 'profit', 'profit_plus', 'profile'))) $category = 'employee';
            $category_history = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'path' => 'project_staffing_last_display'
                ),
            ));
            if( !empty( $category_history)) {
                $this->HistoryFilter->id = $category_history['HistoryFilter']['id'];
                $data = $this->HistoryFilter->save(array('params' => $category));
            }else{
                $this->HistoryFilter->create();
                $data = $this->HistoryFilter->save(array(
                    'params' => $category,
                    'path' => 'project_staffing_last_display',
                    'employee_id' => $this->employee_info['Employee']['id']
                ));
            }
        } else {
            $category_history = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'path' => 'project_staffing_last_display'
                ),
            ));
            if( !empty( $category_history)) {
                $category = $category_history['HistoryFilter']['params'];
            }else{
                $category = 'employee';
            }
        }
        /* END Edit by Huynh */
        $startEmployees = $endEmployees = 0;
        if(empty($activityName['Activity']['project']) || $activityName['Activity']['project'] == 0 || $activityName['Activity']['project'] == ''){
            if($category == 'employee'){
                $staffings = $this->_staffingEmloyeeFromTasks($activity_id);
                $employeeIds = !empty($staffings) ? Set::classicExtract($staffings, '{n}.id') : array();
                $getDates = !empty($staffings) ? Set::classicExtract($staffings, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }

                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDateFilter, $endDateFilter);
                foreach($staffings as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                // 2016-11-04 tinh capacity working and absence theo ratio.
                                $staffings[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffings[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffings[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffings[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffings[$key]['data'][$times]['capacity'] = 0;
								$staffings[$key]['data'][$times]['working'] = 0;
								$staffings[$key]['data'][$times]['absence'] = 0;
                                $staffings[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = false;
                $startEmployees = !empty($startDateFilter) ? mktime(0, 0, 0, date("m", $startDateFilter)-1, date("d", $startDateFilter), date("Y", $startDateFilter)) : '';
                $endEmployees = !empty($endDateFilter) ? mktime(0, 0, 0, date("m", $endDateFilter)+1, date("d", $endDateFilter), date("Y", $endDateFilter)) : '';
                $startEmployees = !empty($startEmployees) ? explode('-', date('d-m-Y', $startEmployees)) : array();
                $endEmployees = !empty($endEmployees) ? explode('-', date('t-m-Y', $endEmployees)) : array();
            } elseif($category == 'profit'){
                $staffings = $this->_staffingProfitCenterFromTasks($activity_id);
                $profitCenterIds = !empty($staffings) ? Set::classicExtract($staffings, '{n}.id') : array();
                $getDates = !empty($staffings) ? Set::classicExtract($staffings, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalEmployees, $capacities, $totalWorkloads) = $this->_capacityFromForecast($profitCenterIds, $startDateFilter, $endDateFilter);
                foreach($staffings as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                // tinh capacity woking absence theo ratio.
                                $staffings[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffings[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffings[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffings[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffings[$key]['data'][$times]['capacity'] = 0;
								$staffings[$key]['data'][$times]['working'] = 0;
								$staffings[$key]['data'][$times]['absence'] = 0;
                                $staffings[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = 1;
            }elseif ($category == 'profile'){
				$this->loadModel('TmpStaffingSystem');
				$employeeName = $this->_getEmployee();
				$staffings = $this->TmpStaffingSystem->staffingProfile($activity_id,$employeeName, true);
				$getDates = !empty($staffings) ? Set::classicExtract($staffings, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
				$showType = 2;
			} else {
                $staffings = $this->_staffingEmloyeeFromTasks($activity_id);
                $employeeIds = !empty($staffings) ? Set::classicExtract($staffings, '{n}.id') : array();
                $getDates = !empty($staffings) ? Set::classicExtract($staffings, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDateFilter, $endDateFilter);
                foreach($staffings as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                $staffings[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffings[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffings[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffings[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffings[$key]['data'][$times]['capacity'] = 0;
								$staffings[$key]['data'][$times]['working'] = 0;
								$staffings[$key]['data'][$times]['absence'] = 0;
                                $staffings[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = false;
            }
            $this->action = 'visions';
            $this->set(compact('activityName', 'activity_id', 'staffings', 'showType'));
        } else {
            $this->loadModel('Project');
            $getProjectId = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $activity_id),
                    'fields' => array('id')
                ));
            $project_id = !empty($getProjectId) ? $getProjectId['Project']['id'] : 0;
            $this->_checkRole(false, $project_id);
            $this->loadModel('ProjectPhasePlan');
            unset($this->helpers['Gantt']);
            $this->helpers['GanttSt'] = null;
            $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
            $this->set('projectName', $this->ProjectPhasePlan->Project->find("first", array(
                        'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'order' => 'milestone_date ASC')),
                        "fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'), 'conditions' => array('Project.id' => $project_id))));
            $options = array(
                'limit' => 2000,
                'contain' => array(
                    'ProjectPhase' => array('name', 'color')
                ),
                'order' => array('ProjectPhasePlan.weight' => 'asc'),
                'conditions' => array('ProjectPhasePlan.project_id' => $project_id));
            $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
            $phasePlans = $this->ProjectPhasePlan->find('all', $options);
            $getDatas = $this->_taskCaculates($project_id, $activityName['Activity']['company_id']);
            $taskCompleted = $getDatas['part'];
            $phaseCompleted = $getDatas['phase'];
            $projectTasks = $getDatas['task'];
            $onClickPhaseIds = array();
            $options = array(
                'limit' => 2000,
                'contain' => array(
                    'ProjectPhase' => array('name', 'color')
                ),
                'order' => array('ProjectPhasePlan.weight' => 'asc'),
                'conditions' => array('ProjectPhasePlan.project_id' => $project_id)
            );
            $phasePlans = $this->ProjectPhasePlan->find('all', $options);
            $phaseIds = Set::classicExtract($phasePlans, '{n}.ProjectPhasePlan.id');
            foreach($projectTasks as $projectTask){
                foreach($phaseIds as $phaseId){
                    if($phaseId == $projectTask['project_part_id']){
                        $onClickPhaseIds['wd-'.$phaseId][] = $projectTask['id'];
                    }
                    if(!empty($projectTask['children'])){
                        foreach($projectTask['children'] as $vl){
                            if($projectTask['id'] == $vl['project_part_id']){
                                $onClickPhaseIds['wd-'.$projectTask['id']][] = $vl['id'];
                            }
                        }
                    }
                }
            }
            if(!empty($onClickPhaseIds)){
                foreach($onClickPhaseIds as $key => $onClickPhaseId){
                    $onClickPhaseIds[$key] = array_unique($onClickPhaseId);
                }
            }

            $parts = $this->ProjectPhasePlan->ProjectPart->find('list');

            if($category == 'employee'){
                $staffingss = $this->_staffingEmloyeeFromProjectStaffing($project_id);
                $employeeIds = !empty($staffingss) ? Set::classicExtract($staffingss, '{n}.id') : array();
                $getDates = !empty($staffingss) ? Set::classicExtract($staffingss, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDateFilter, $endDateFilter);
                foreach($staffingss as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                $staffingss[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffingss[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffingss[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffingss[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffingss[$key]['data'][$times]['capacity'] = 0;
								$staffingss[$key]['data'][$times]['working'] = 0;
								$staffingss[$key]['data'][$times]['absence'] = 0;
                                $staffingss[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = false;
                $startEmployees = !empty($startDateFilter) ? mktime(0, 0, 0, date("m", $startDateFilter)-1, date("d", $startDateFilter), date("Y", $startDateFilter)) : '';
                $endEmployees = !empty($endDateFilter) ? mktime(0, 0, 0, date("m", $endDateFilter)+1, date("d", $endDateFilter), date("Y", $endDateFilter)) : '';
                $startEmployees = !empty($startEmployees) ? explode('-', date('d-m-Y', $startEmployees)) : array();
                $endEmployees = !empty($endEmployees) ? explode('-', date('t-m-Y', $endEmployees)) : array();
            } elseif($category == 'profit'){
                $staffingss = $this->_staffingProfitCenterFromProjectStaffing($project_id);
                $profitCenterIds = !empty($staffingss) ? Set::classicExtract($staffingss, '{n}.id') : array();
                $getDates = !empty($staffingss) ? Set::classicExtract($staffingss, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalEmployees, $capacities, $totalWorkloads) = $this->_capacityFromForecast($profitCenterIds, $startDateFilter, $endDateFilter);

                foreach($staffingss as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                $staffingss[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffingss[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffingss[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffingss[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffingss[$key]['data'][$times]['capacity'] = 0;
								$staffingss[$key]['data'][$times]['working'] = 0;
								$staffingss[$key]['data'][$times]['absence'] = 0;
                                $staffingss[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = 1;
            }elseif ($category == 'profile'){
				$this->loadModel('TmpStaffingSystem');
				$employeeName = $this->_getEmployee();
				$staffingss = $this->TmpStaffingSystem->staffingProfile($project_id,$employeeName);
				$getDates = !empty($staffingss) ? Set::classicExtract($staffingss, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
				$showType = 2;
			} else {
                $staffingss = $this->_staffingFunctionFromProjectStaffing($project_id);
                $getDates = !empty($staffingss) ? Set::classicExtract($staffingss, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                $showType = 2;
            }

            $project = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $project_id),
                    'fields' => array('activity_id')
                ));
            $activityId = Set::classicExtract($project, 'Project.activity_id');
            $employeeName = $this->_getEmployee();
            $this->loadModel('ActivityRequest');
            $minMaxDate = $this->ActivityRequest->find('all',array(
                'recursive'     => -1,
                'fields'        => array('MIN(date) as min_date', 'MAX(date) as max_date'),
                'conditions'    => array(
                    'status'        => 2,
                    'activity_id'   => $activityId,
                    'company_id'    => $employeeName['company_id'],
                    'NOT'           => array(
                            'value' => 0
                        ),
                )
            ));
            $minMaxDate = $minMaxDate[0][0];
            if(!empty($minMaxDate['min_date']) && !empty($minMaxDate['max_date'])){
                $this->set(compact('minMaxDate'));
            }
            $minMax = $this->_getMinAndMaxDate($project_id);
            $this->action = 'visions_yes';
            $this->set(compact('activityName', 'activity_id', 'phasePlans', 'display', 'project_id', 'parts', 'staffingss', 'showType', 'minMax', 'projectTasks', 'taskCompleted', 'phaseCompleted', 'onClickPhaseIds', 'startDateFilter', 'endDateFilter'));
        }
        $this->_parseParams();
        $this->set(compact('startEmployees', 'endEmployees'));
        $this->set('staffingCate', $category);
		//CHECK REBUILD STAFFING
		$lib = new LibBehavior();
		if(isset($project_id) && $project_id)
		$rebuildStaffing = $lib->checkRebuildStaffing('Project',$project_id);
		else
		$rebuildStaffing = $lib->checkRebuildStaffing('Activity',$activity_id);
		$this->set('rebuildStaffing', $rebuildStaffing);
		//END
    }

    /**
     *
     * Staffing+: copy to project staffing controller
     *
     *
     *
     *
     */

     /**
	 * Get project task By ID
	 *
	 * @return void
	 * @access private
	 */

    private function _projectTask($project_id = null){
        $this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id')
            ));
        return $projectTasks;
    }

    /**
	 * Get min date and max date of project task
	 *
	 * @return void
	 * @access private
	 */

    private function _getMinAndMaxDate($project_id = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectPhasePlan');

        $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                    'NOT' => array(
                            'task_start_date' => '0000-00-00',
                            'task_end_date' => '0000-00-00'
                        )
                ),
                'fields' => array(
                    'MIN(task_start_date) as min_date',
                    'MAX(task_end_date) as max_date'
                )
            ));
        if(!empty($projectTasks[0][0]['min_date']) && !empty($projectTasks[0][0]['max_date'])){
            $result = $projectTasks[0][0];
        } else {
            $projectPhasePlan = $this->ProjectPhasePlan->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                    'NOT' => array(
                            'phase_planed_start_date' => '0000-00-00',
                            'phase_planed_end_date' => '0000-00-00'
                        )
                ),
                'fields' => array(
                    'MIN(phase_planed_start_date) as min_date',
                    'MAX(phase_planed_end_date) as max_date'
                )
            ));
            $result = $projectPhasePlan[0][0];
        }
        return $result;
    }

    /**
	 * Get activity task request
	 *
	 * @return void
	 * @access private
	 */
    private function _activityRequest(){
        $this->loadModel('ActivityRequest');
        $employeeName = $this->_getEmployee();

        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'employee_id', 'task_id', 'SUM(value) as value'),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $requests = array();
        foreach($activityRequests as $key => $activityRequest){
            $requests[$key]['employee_id'] = $activityRequest['ActivityRequest']['employee_id'];
            $requests[$key]['task_id'] = $activityRequest['ActivityRequest']['task_id'];
            $requests[$key]['consumed'] = $activityRequest[0]['value'];
        }
        if(!empty($requests)){
            $requests = Set::combine($requests, '{n}.task_id', '{n}');
        }

        return $requests;
    }

    /**
	 * Get activity task By ID
	 *
	 * @return void
	 * @access private
	 */
    private function _activityTask($taskId = null){
        $this->loadModel('ActivityTask');
        $activityTask = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.id' => $taskId),
                'fields' => array('id', 'project_task_id')
            ));
        return $activityTask;
    }
      /**
     * Staffing of employee
     *
     *
     * @return
     * @access private
     */
     private function _staffingEmloyeeFromProjectStaffing($project_id = null){
       $this->loadModel('TmpStaffingSystem');
       $employeeName = $this->_getEmployee();
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
        $datas = array();
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
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }
        $ids = !empty($datas) ? array_keys($datas) : array();
        $this->loadModel('Employee');
        $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $ids),
                'fields' => array('id', 'fullname'),
                'order' => array('Employee.id')
            ));
        if(!empty($datas['999999999'])){
            $notAffected = array(
                'Employee' => array(
                    'id' => '999999999',
                    'fullname' => __('Not Affected', true)
                )
            );
            $employees[] = $notAffected;
        }
        $staffings = array();
        foreach($employees as $key => $employee){
            $staffings[$key]['id'] = $employee['Employee']['id'];
            $staffings[$key]['is_check'] = 1;
            $staffings[$key]['name'] = $employee['Employee']['fullname'];
            $staffings[$key]['data'] = isset($datas[$employee['Employee']['id']]) ? $datas[$employee['Employee']['id']] : '';
        }
        return $staffings;
     }
     /**
     * Staffing of profit center
     *
     *
     * @return
     * @access private
     */
     private function _staffingProfitCenterFromProjectStaffing($project_id = null){
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmployee();
        /**
         * Lay du lieu staffing cho profit center
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'profit_center',
                'company_id' => $employeeName['company_id']
            )
        ));
        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * profit_center => array(
         *      'time' => array()
         * )
         */
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
                // date
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }

        $ids = array_keys($datas);
        $this->loadModel('ProfitCenter');
        $profitCenters = $this->ProfitCenter->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProfitCenter.id' => $ids),
                'fields' => array('id', 'name'),
                'order' => array('ProfitCenter.id')
            ));
        if(!empty($datas['999999999'])){
            $notAffected = array(
                'ProfitCenter' => array(
                    'id' => '999999999',
                    'name' => __('Not Affected', true)
                )
            );
            $profitCenters[] = $notAffected;
        }
        if(!empty($datas['0'])){
            $noName = array(
                'ProfitCenter' => array(
                    'id' => '0',
                    'name' => 'No Name'
                )
            );
            $profitCenters[] = $noName;
        }
        $staffings = array();
        foreach($profitCenters as $key => $profitCenter){
            $staffings[$key]['id'] = $profitCenter['ProfitCenter']['id'];
            $staffings[$key]['is_check'] = 2;
            $staffings[$key]['name'] = $profitCenter['ProfitCenter']['name'];
            $staffings[$key]['data'] = isset($datas[$profitCenter['ProfitCenter']['id']]) ? $datas[$profitCenter['ProfitCenter']['id']] : '';
        }
        return $staffings;
     }

     /**
     * Staffing of function
     *
     *
     * @return
     * @access private
     */
     private function _staffingFunctionFromProjectStaffing($project_id = null){
        $this->loadModel('ProjectFunction');
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProjectTeam');
        $employeeName = $this->_getEmployee();
        /**
         * Lay du lieu staffing cho profit center
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'skill',
                'company_id' => $employeeName['company_id']
            )
        ));
        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * profit_center => array(
         *      'time' => array()
         * )
         */
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
                // date
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }
        $_projectTeams = $this->ProjectTeam->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('project_function_id', 'profit_center_id'),
                'order' => array('ProjectTeam.id' => 'DESC')
            ));
        $projectFunctions = $this->ProjectFunction->find('list', array(
                'recursive' => -1,
                'conditions' => array(),
                'fields' => array('id', 'name')
            ));
        if(!empty($datas['999999999'])){
            $_projectTeams['999999999'] = '999999999';
            $projectFunctions['999999999'] = __('Not Affected', true);
        }
        $staffings = array();
        $check = 0;
        foreach($_projectTeams as $key => $_projectTeam){
            $staffings[$check]['id'] = $key;
            $staffings[$check]['is_check'] = 3;
            $staffings[$check]['name'] = isset($projectFunctions[$key]) ? $projectFunctions[$key] : 'NO NAME';
            $staffings[$check]['data'] = isset($datas[$key]) ? $datas[$key] : array();
            $check++;
        }
        return $staffings;
     }
    /**
     * Tinh so ngay lam viec cua 1 thang
     */
    private function _countWorkingDate($date = null, $totalDate = null){
        $startDate = strtotime('01-'.date('m-Y', $date));
        $endDate = strtotime($totalDate.'-'.date('m-Y', $date));
        $count = 0;
        while($startDate <= $endDate){
            $_date = strtolower(date("l", $startDate));
            if($_date == 'saturday' || $_date == 'sunday'){
                //do nothing
            } else {
                $count++;
            }
            $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
        }
        return $count;
    }
    private function _capacityFromForecastPC($profitCenterIds = array(), $startDate, $endDate, $applyForProject = false, $dateType = 'month', $listDays = null){
		return $this->ActivityTask->capacityFromForecastPC($profitCenterIds, $startDate, $endDate, $applyForProject, $dateType, $listDays);
    }
    private function _capacityFromForecast($profitCenterIds = array(), $startDate, $endDate, $getTotal = true, $applyForProject = false , $dateType = 'month', $listDays = null){
		return $this->ActivityTask->capacityFromForecast($profitCenterIds, $startDate, $endDate, $getTotal, $applyForProject , $dateType, $listDays);
    }
    private function _capacityForEmployee($employeeIds = array(), $startDate, $endDate, $allEmployee = null, $applyForProject = false , $dateType = 'month', $listDays = null){
		return $this->ActivityTask->capacityForEmployee($employeeIds, $startDate, $endDate, $allEmployee, $applyForProject , $dateType, $listDays);
    }

    public function setWorkload(){
        $this->ActivityTask->cacheQueries = true;
        $this->ActivityTask->Behaviors->attach('Containable');
        $datas = $this->ActivityTask->find('all', array(
            'contain' => array('ActivityTaskEmployeeRefer')
        ));
        foreach($datas as $key => $data){
            if(empty($data['ActivityTaskEmployeeRefer']) || count($data['ActivityTaskEmployeeRefer']) > 1){
                unset($datas[$key]);
            }
        }
        $this->loadModel('ActivityTaskEmployeeRefer');
        foreach($datas as $data){
            $this->ActivityTaskEmployeeRefer->id = $data['ActivityTaskEmployeeRefer'][0]['id'];
            $_data['estimated'] = $data['ActivityTask']['estimated'];
            $this->ActivityTaskEmployeeRefer->save($_data);
        }
        debug('finish!'); exit;
    }

    /**
     * Export vision system
     *
     * @return void
     * @access public
     */

   //edit by Thach
    /**
     * Export
     *
     * @return void
     * @access public
     */

    public function export($activity_id = null) {
        $activity_id = $this->data['Export']['list'];
        $children = $this->_getAllActivityTasks($activity_id);
        $result = $children[0];
        $this->set('activityTask',$result);
        $this->set('activityName', $this->ActivityTask->Activity->find("first", array("fields" => array("Activity.name", 'Activity.off_freeze'),
                    'conditions' => array('Activity.id' => $activity_id))));
        //$this->loadModel('ProjectSetting');
        $settingP = $this->requestAction('/project_settings/get');
        $this->loadModel('Activity');
        $checkP = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id),
                'fields'=>'is_freeze'
            ));
        $this->set('columns', $this->name_columna);
        $this->set(compact('settingP','checkP'));
    }
    public function getlocale(){
        $this->layout = 'ajax';
        $result['Locale'] =  Configure::read('Config.language');
        $this->set(compact('result'));
    }

    /**
     * Lay danh sach tat ca cac activity khong linked voi project
     * Goi function _staffingSystem luu tat ca workload cua employee, profit center cua cac project do
     *
     * @access public
     * @return void
     * @author HuuPC
     *
     */
    public function staffingSystem($_index=null, $reBuildALL = false){
        $this->loadModel('Activity');
		$this->layout=false;
		ini_set('max_execution_time', 0);
		set_time_limit(0);
		ini_set('memory_limit', '512M');
		$start = microtime(true);
        $activitySaves = $this->Session->read('ActivitySave.TMP');
        $activitySaves = !empty($activitySaves) ? $activitySaves : array();
        if($reBuildALL == true){
            $activitys = $this->Activity->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id'),
                'conditions' => array(
                    'project' => null,
                    'NOT' => array('Activity.id' => $activitySaves)
                ),
                'order' => array('id' => 'ASC'),
    			'limit' => 30
            ));
        } else {
            $activitys = $this->Activity->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id'),
                'conditions' => array(
                    //'project' => null,
    				'id' => $_index,
                    //'NOT' => array('Activity.id' => $activitySaves)
                ),
                'order' => array('id' => 'ASC'),
    			'limit' => 200
            ));
        }

        foreach($activitys as $activity){
            echo 'Activity: ' . $activity . ' : ';
            $this->ActivityTask->staffingSystem($activity,true);
			echo '<br />';
        }
        $activitySaves = array_merge($activitySaves, $activitys);
        $this->Session->write('ActivitySave.TMP', $activitySaves);
		$end = microtime(true);
		$timeExecute=$end-$start;
		echo 'Time execute: '.$timeExecute;echo '<br />';
		if(empty($activitys))
		{
			echo 'Finish!'; echo '<br />';
		}
		else
		{
			$_link='<a href="https://'.$_SERVER['HTTP_HOST'].'/activity_tasks/staffingSystem">Next step</a>';
			echo $_link;
			echo '<br />';
		}

        exit;
     }

	private function _mergeDataSystem($workloads = array(), $consumeds = array(), $previous = array(), $totalConsumedTasks = array(), $model = null, $project_id = null, $activity_id = null){
		return $this->ActivityTask->_mergeDataSystem($workloads, $consumeds, $previous, $totalConsumedTasks, $model, $project_id, $activity_id);
	}
	private function _recursiveTask($tasks = array(), $valueAdds = null, $tmpTasks = array()){
		return $this->ActivityTask->_recursiveTask($tasks, $valueAdds, $tmpTasks);
	}
	private function _resetRemainSystem($datas = array()){
		return $this->ActivityTask->_resetRemainSystem($datas);
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
        $employeeName = $this->_getEmployee();
        $totalConsumedTasks = $consumedTaskEmployees = array();
        if(!empty($listActivityTaskIds)){
            $requests = $this->ActivityRequest->find('all',array(
                'recursive'     => -1,
                'fields'        => array('id', 'date', 'employee_id', 'task_id', 'value'),
                'conditions'    => array(
                    'status'        => 2,
                    'task_id'       => $listActivityTaskIds,
                    'company_id'    => $employeeName['company_id'],
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
        $employeeName = $this->_getEmployee();
        $previous = $this->ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array('id', 'date', 'employee_id', 'value'),
            'conditions'    => array(
                'status'        => 2,
                'activity_id'   => $activityId,
                'company_id'    => $employeeName['company_id'],
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

      public function dash_board($activity_id){
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $filterId = isset($this->params['url']['filter']) ? $this->params['url']['filter'] : 1;
        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        $staffings = array();
        if(!empty($activityName['Activity']['project']) || $activityName['Activity']['project'] != 0 || $activityName['Activity']['project'] != ''){
            //linked project
            if($filterId == 1){
                $staffings = $this->_staffingEmloyeeFromProjectStaffing($activityName['Activity']['project']);
            } elseif($filterId == 2){
                $staffings = $this->_staffingProfitCenterFromProjectStaffing($activityName['Activity']['project']);
            } else {
                $staffings = $this->_staffingFunctionFromProjectStaffing($activityName['Activity']['project']);
            }
        } else {
            //not linked project
            if($filterId == 1){
                $staffings = $this->_staffingEmloyeeFromTasks($activity_id);
            } else {
                $staffings = $this->_staffingProfitCenterFromTasks($activity_id);
            }
        }
        $result = array();
        //$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        //$months = range(1, 12);
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        if(!empty($staffings)){
            $datas = $this->_totalStaffing($staffings);
            $_setYear = $years = $estimatedNextMonths = array();
            $consumed = $validated = 0;
            foreach($datas as $m => $data){
                $_setYear[] = date("Y", $data['set_date']);
                $_yeas = explode('/', $m);
                $years[] = $_yeas[1];
                $nextMonths = mktime(0, 0, 0, date("m", $data['set_date'])+1, date("d", $data['set_date']), date("Y", $data['set_date']));
                //$estimatedNextMonths[date('m/y', $nextMonths)]['estimation'] = $data['estimation'];
                unset($datas[$m]['set_date']);

                $consumed += $data['consumed'];
                if($datas[$m]['consumed'] != 0){
                    $datas[$m]['consumed'] = $consumed;
                }

                $validated += $data['validated'];
                $datas[$m]['validated'] = $validated;
            }
            $years = !empty($years) ? array_unique($years) : array();
            asort($years);
            foreach($years as $year){
                foreach($months as $month){
                    if(!empty($datas[$month.'/'.$year])){
                        if($datas[$month.'/'.$year]['consumed'] == 0){
                            unset($datas[$month.'/'.$year]['consumed']);
                        }
                        $result[] = $datas[$month.'/'.$year];
                   } else {
                        $result[] = array(
                            'date' => $month.'/'.$year
                        );
                   }
                }
            }
            $manDays = !empty($result) ? Set::classicExtract($result, '{n}.validated') : array();
            $consumed_manDays = !empty($result) ? Set::classicExtract($result, '{n}.consumed') : array();
            $manDays = !empty($manDays) ? (integer)max($manDays) : 0;
            $consumed_manDays = !empty($consumed_manDays) ? (integer)max($consumed_manDays) : 0;
            if($manDays<$consumed_manDays){$manDays=$consumed_manDays;}
            /*if($manDays >= 0 && $manDays <= 100) {$manDays = 100;}
            elseif($manDays > 100 && $manDays <= 200) {$manDays = 200;}
            elseif($manDays > 200 && $manDays <= 500) {$manDays = 500;}
            elseif($manDays > 500 && $manDays <= 1000) {$manDays = 1000;}
            elseif($manDays > 1000 && $manDays <= 5000) {$manDays = 5000;}
            elseif($manDays > 5000) {$manDays = 10000;}
            else {$manDays = 100000;}*/
			$manDays=$manDays+round($manDays*0.1,2);
        }
        $dataSets = array();
        if(!empty($result)){
            foreach($result as $key => $values){
                if(empty($values['consumed']) && empty($values['validated'])){
                    unset($result[$key]);
                } else {
                    $dataSets[] = $values;
                }
            }
        }
        $_setYear = !empty($_setYear) ? array_unique($_setYear) : array();
        asort($_setYear);
        $setYear = !empty($_setYear) ? implode('-' ,$_setYear) : '';
        $display = isset($this->params['url']['ControlDisplay']) ? $this->params['url']['ControlDisplay'] : range(1, 3);
        if(in_array(1, $display)){
            $showEstimation = true;
        } else {$showEstimation = false;}
        if(in_array(2, $display)){
            $showConsumed = true;
        } else {$showConsumed = false;}
        if(in_array(3, $display)){
            $showValidated = true;
        } else {$showValidated = false;}
        $this->set(compact('showEstimation', 'showConsumed', 'showValidated'));
        $this->set(compact('dataSets', 'filterId', 'activity_id', 'manDays', 'activityName', 'setYear', 'display'));
        $this->_parseParams();
     }


     /**
     *  Tinh toan staffing ho tro cho bo loc va Dashboard generating
     *
     */
     private function _totalStaffing($staffings){
        $staffings = !empty($staffings) ? Set::combine($staffings, '{n}.id', '{n}.data') : array();
        $datas = array();
        if(!empty($staffings)){
            foreach($staffings as $staffing){
                foreach($staffing as $time => $values){
                    $_time = date('m/y', $values['date']);
                    $datas[$time]['date'] = $_time;
                    $datas[$time]['set_date'] = $time;
                    if(!isset($datas[$time]['consumed'])){
                        $datas[$time]['consumed'] = 0;
                    }
                    $datas[$time]['consumed'] += $values['consumed'];
                    if(!isset($datas[$time]['validated'])){
                        $datas[$time]['validated'] = 0;
                    }
                    $datas[$time]['validated'] += $values['validated'];
                    if(!isset($datas[$time]['remains'])){
                        $datas[$time]['remains'] = 0;
                    }
                    $datas[$time]['remains'] += $values['remains'];
                }
            }
        }
        ksort($datas);
        $results = array();
        if(!empty($datas)){
            foreach($datas as $key => $data){
                $_time = date('m/y', $key);
                $results[$_time] = $data;
            }
        }
        return $results;
     }

     /**
     * teams
     *
     * @return void
     * @access public
     */
    public function teams($activity_id = null) {
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $this->loadModel('ProjectTeam');
        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        if (!$this->is_sas) {
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array(
                'company_id' => $activityName['Activity']['company_id']), null, null, '--');
        } else {
            $profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, '--');
        }
        $projectFunctions = $this->ProjectTeam->ProjectFunction->find('list', array(
            'fields' => array(
                'ProjectFunction.id', 'ProjectFunction.name'
            ),
            "conditions" => array('ProjectFunction.company_id' => $activityName['Activity']['company_id'])));

        $employees = $this->ProjectTeam->Project->Employee->CompanyEmployeeReference->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $activityName['Activity']['company_id'])));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));

        $employeeRefers = $assignEmployees = $assignProfitCenters = array();
        if(!empty($activityName['Activity']['project']) || $activityName['Activity']['project'] != 0 || $activityName['Activity']['project'] != ''){
            $project_id = $activityName['Activity']['project'];
            $this->_checkRole(false, $project_id);
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
            $this->action = 'teams_yes';
        } else {
            $this->loadModel('ActivityProfitRefer');
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $profitRefers = $this->ActivityProfitRefer->find('all', array(
                'conditions' => array('activity_id' => $activity_id)
            ));
            $listPcs = !empty($profitRefers) ? Set::classicExtract($profitRefers, '{n}.ActivityProfitRefer.profit_center_id') : array();
            $references = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0),
                    'profit_center_id' => $listPcs
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('profit_center_id')
            ));
            $resetRefers = array();
            if(!empty($references)){
                foreach($references as $employ => $profit){
                    if(!isset($resetRefers[$profit])){
                        //do nothing
                    }
                    $resetRefers[$profit][] = $employ;
                }
            }
            $canModified = false;
            $this->set(compact('listPcs', 'resetRefers', 'canModified'));
        }
        $this->_parseParams();
        $this->set(compact('projectName', 'projectFunctions', 'projectTeams', 'profitCenters', 'activity_id', 'employees', 'employeeRefers', 'assignEmployees', 'assignProfitCenters', 'activityName'));
    }

    /**
     * export_vision
     * Export vision
     *
     * @return void
     * @access public
     */
    function export_visions() {
        $ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
        //$this->helpers = array_merge($this->helpers, array(
               // 'GanttSt'));
        set_time_limit(120);
        $this->layout = false;
        if (!empty($this->data['Export'])) {
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);

            $tmpFile = TMP . 'page_' . time() . '.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));

            list($_width, $_height) = getimagesize($tmpFile);

            $image = imagecreatefrompng($tmpFile);
            $crop = imagecreatetruecolor($_width - 69, $height);

            imagecopy($crop, $image, 0, 0, 69, 232, $_width, $height);
            imagepng($crop, $tmpFile);

            $months = unserialize($months);
            /* Edit by Huynh
             old: $category = $this->Session->read("Staffing.Cate");
             new: get from history
             * email: Z0G 25/7/2019 Prod4 task 4.
             */
            $category_history = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'path' => 'project_staffing_last_display'
                ),
            ));
            if( !empty( $category_history)) {
                $category = $category_history['HistoryFilter']['params'];
            }else{
                $category = 'employee';
            }
            /* END Edit by Huynh */
            $startDateFilter = $endDateFilter = array();
			//debug("xxxx");
            if($category == 'employee'){
                $staffings = $this->_staffingEmloyeeFromTasks($activity_id);
                $employeeIds = !empty($staffings) ? Set::classicExtract($staffings, '{n}.id') : array();
                $getDates = !empty($staffings) ? Set::classicExtract($staffings, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();

                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDateFilter, $endDateFilter);
                foreach($staffings as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                $staffings[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffings[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffings[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffings[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffings[$key]['data'][$times]['capacity'] = 0;
								$staffings[$key]['data'][$times]['working'] = 0;
								$staffings[$key]['data'][$times]['absence'] = 0;
                                $staffings[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = 0;
            } elseif($category == 'profit'){
                $staffings = $this->_staffingProfitCenterFromTasks($activity_id);
                $profitCenterIds = !empty($staffings) ? Set::classicExtract($staffings, '{n}.id') : array();
                $getDates = !empty($staffings) ? Set::classicExtract($staffings, '{n}.data.{n}.date') : array();
                $setDates = array();
                if(!empty($getDates)){
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }
                }
                $setDates = !empty($setDates) ? array_unique($setDates) : array();
                $startDateFilter = !empty($setDates) ? min($setDates) : array();
                $endDateFilter = !empty($setDates) ? max($setDates) : array();
                $this->set(compact('startDateFilter', 'endDateFilter'));
                list($totalEmployees, $capacities, $totalWorkloads) = $this->_capacityFromForecast($profitCenterIds, $startDateFilter, $endDateFilter);
                foreach($staffings as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                $staffings[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								$staffings[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffings[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
                                $staffings[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
                            }
                            if($staffing['id'] == '999999999'){
                                $staffings[$key]['data'][$times]['capacity'] = 0;
								$staffings[$key]['data'][$times]['working'] = 0;
								$staffings[$key]['data'][$times]['absence'] = 0;
                                $staffings[$key]['data'][$times]['totalWorkload'] = 0;
                            }
                        }
                    }
                }
                $showType = 1;
            }
            $start = $startDateFilter;
            $end = $endDateFilter;
            $this->set(compact('tmpFile', 'height', 'project', 'rows', 'activity_id', 'start', 'end', 'months', 'displayFields', 'staffings', 'showType'));
            // $this->set('activity_page', 1);
            // $this->render('/project_staffings/export_visions');
        } else {
            $this->redirect(array('controller' => 'activity_tasks', 'action' => 'index', $activity_id));
        }
    }
 /**
     * capacityForTask
     *
     * @return MD for task
     * @access public
     */
    public function capacityForTask($activity_id, $task_id){
        $this->layout = 'ajax';
        $this->loadModel('ActivityTaskEmployeeRefer');
        //$assigns = Set::combine($this->_getProfitCenterAndEmployee($activity_id),'{n}.Employee.id','{n}.Employee');
        $assigns = $this->_getProfitCenterAndEmployee($activity_id);

        $assignsforTask =  $this->ActivityTaskEmployeeRefer->find('list',array(
            'recursive' => -1,
            'conditions' => array('activity_task_id' => $task_id),
            'fields' => array('reference_id','id')
        ));
        foreach($assigns as $key => $assign){
            if(!empty($assignsforTask[$assign['Employee']['id']])){
                $assigns[$key]['Employee']['is_selected'] = 1;

            }else{
                $assigns[$key]['Employee']['is_selected'] = 0;
            }
        }
        $activityTasks = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id, 'ActivityTask.id' => $task_id),
            'fields' => array('id', 'task_start_date', 'task_end_date')
        ));
        $result = $this->capacityForEmployee($assigns, $activityTasks['ActivityTask']['task_start_date'] , $activityTasks['ActivityTask']['task_end_date']);
        $this->set(compact('result'));
    }
   private function capacityForEmployee($arrayEmployee , $stardate , $endate){
        //debug($stardate.' | '.$endate);
        //exit;
        //tim cac task co employee lam tu stardate den enddate
        //tinh MD cho cac employee tru ra thu 7 CN, ngay le , ngay xin nghi
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('Holiday');
        $_end = date('Y-m-d',$endate);
        $_start = date('Y-m-d',$stardate);

        foreach($arrayEmployee as $key => $employee){
            $avgMD = 0;
            if($employee['Employee']['is_profit_center'] == 0 ){
                $listProjectTaskForEmployee = $this->ProjectTaskEmployeeRefer->find('all',array(
                    'conditions' => array(
                        'AND' => array(
                            'reference_id' => $employee['Employee']['id'],
                            'is_profit_center' => 0,
                            'ProjectTask.task_start_date !=' => '0000-00-00',
                            'OR' => array(
                                'ProjectTask.task_start_date BETWEEN ? AND ?' => array($_start, $_end),
                                'ProjectTask.task_end_date BETWEEN ? AND ?' => array($_start, $_end),
                                'AND' => array(
                                        'ProjectTask.task_start_date <' =>  $_start ,
                                        'ProjectTask.task_end_date >' => $_end
                                ),
                            )
                            )
                        ),
                    'recursive' => 0
                ));
                if($employee['Employee']['id'] == 834){



                if(empty($listProjectTaskForEmployee)){
                    $arrayEmployee[$key]['Employee']['capacity_on']= 0;
                }else{
                    foreach($listProjectTaskForEmployee as $_key => $_listProjectTaskForEmployee){
                        if($_listProjectTaskForEmployee['ProjectTaskEmployeeRefer']['estimated'] == 0  ){
                            $avgMD += 0;
                        }
                        else{
                            $avgMD += $this->_AvgProjectWorkingForEmployee($_listProjectTaskForEmployee,$employee['Employee']['id'], $stardate , $endate);
                        }
                    }
                }
                }
                $listActivitytaskForEmployee = $this->ActivityTaskEmployeeRefer->find('all',array(
                    'conditions' => array(
                        'AND' => array(
                            'reference_id' => $employee['Employee']['id'],
                            'is_profit_center' => 0,
                            'ActivityTask.task_start_date !=' => '0000-00-00',
                            'OR' => array(
                                'ActivityTask.task_start_date BETWEEN ? AND ?' => array($stardate, $endate),
                                'ActivityTask.task_end_date BETWEEN ? AND ?' => array($stardate, $endate),
                                'AND' => array(
                                        'ActivityTask.task_start_date <' =>  $stardate ,
                                        'ActivityTask.task_end_date >' => $endate
                                ),
                            )
                        )
                    ),
                    'recursive' => 0
                ));

                foreach($listActivitytaskForEmployee as $k => $v){

                    $listActivitytaskForEmployee[$k]['ActivityTask']['task_start_date'] = date('Y-m-d',$v['ActivityTask']['task_start_date']);
                    $listActivitytaskForEmployee[$k]['ActivityTask']['task_end_date'] = date('Y-m-d',$v['ActivityTask']['task_end_date']);
                }
                if(empty($listActivitytaskForEmployee)){
                    $arrayEmployee[$key]['Employee']['capacity_on']= 0;
                }else{
                    foreach($listActivitytaskForEmployee as $_key => $_listActivityTaskFromDate){

                        if($_listActivityTaskFromDate['ActivityTaskEmployeeRefer']['estimated'] == 0  ){
                            $avgMD += 0;
                        }
                        else{
                            $avgMD += $this->_AvgActivityWorkingForEmployee($_listActivityTaskFromDate,$employee['Employee']['id'], $stardate , $endate);
                            //debug($this->_AvgActivityWorkingForEmployee($_listActivityTaskFromDate,$employee['Employee']['id'], $stardate , $endate));
                        }
                    }
                }
            }else{
                $profitCenters = $this->ProjectEmployeeProfitFunctionRefer->find('all',array(
                    'conditions' => array(
                        'profit_center_id' => $employee['Employee']['id']
                    ),
                    'recursive' => -1
                ));
                if(!empty($profitCenters)){
                    foreach($profitCenters as $profitCenter){
                        //MD cho PC

                    $listProjectTaskForEmployee = $this->ProjectTaskEmployeeRefer->find('all',array(
                        'conditions' => array(
                            'AND' => array(
                                'reference_id' => $profitCenter['ProjectEmployeeProfitFunctionRefer']['employee_id'],
                                'is_profit_center' => 0,
                                'OR' => array(
                                    'ProjectTask.task_start_date BETWEEN ? AND ?'   => array($_start, $_end),
                                    'ProjectTask.task_end_date BETWEEN ? AND ?'     => array($_start, $_end),
                                    'AND' => array(
                                           'ProjectTask.task_start_date <' =>  $_start,
                                           'ProjectTask.task_start_date >' =>  $_end
                                        )
                                    )
                                )
                            ),
                        'recursive' => 0
                    ));
                    if(empty($listProjectTaskForEmployee)){
                        $arrayEmployee[$key]['Employee']['capacity_on']= 0;
                    }else{
                        foreach($listProjectTaskForEmployee as $_key => $_listProjectTaskForEmployee){
                            if($_listProjectTaskForEmployee['ProjectTaskEmployeeRefer']['estimated'] == 0  ){
                                $avgMD += 0;
                            }
                            else{
                                $avgMD += $this->_AvgProjectWorkingForEmployee($_listProjectTaskForEmployee,$employee['Employee']['id'], $stardate , $endate);
                            }
                        }
                    }
                    $listActivitytaskForEmployee = $this->ActivityTaskEmployeeRefer->find('all',array(
                        'conditions' => array(
                            'AND' => array(
                                'reference_id' => $profitCenter['ProjectEmployeeProfitFunctionRefer']['employee_id'],
                                'is_profit_center' => 0,
                                'OR' => array(
                                    'ActivityTask.task_start_date BETWEEN ? AND ?'  => array($stardate, $endate),
                                    'ActivityTask.task_start_date BETWEEN ? AND ?'  => array($stardate, $endate),
                                    'AND' => array(
                                           'ActivityTask.task_start_date <' =>  $_start,
                                           'ActivityTask.task_start_date >' =>  $_end
                                        )
                                )
                            )
                        ),
                        'recursive' => 0
                    ));

                    if(empty($listActivitytaskForEmployee)){
                        $arrayEmployee[$key]['Employee']['capacity_on']= 0;
                    }else{
                        foreach($listActivitytaskForEmployee as $_key => $_listActivityTaskFromDate){

                            if($_listActivityTaskFromDate['ActivityTaskEmployeeRefer']['estimated'] == 0  ){
                                $avgMD += 0;
                            }
                            else{
                                $avgMD += $this->_AvgActivityWorkingForEmployee($_listActivityTaskFromDate,$employee['Employee']['id'], $stardate , $endate);


                            }
                        }
                    }
                    }
                }else{
                    $avgMD += 0;
                }
            }


            $workingday = $this->countdate($stardate,$endate,$arrayEmployee[$key]['Employee']['id']);
            $wd = $workingday - $avgMD;
            if($wd < 0){
                $wd = 0;
            }
            $arrayEmployee[$key]['Employee']['capacity_on'] = $wd;
            $arrayEmployee[$key]['Employee']['capacity_off'] =   $avgMD;
        }

        return $arrayEmployee;
    }
     private function _AvgActivityWorkingForEmployee($task,$id_employee, $stardate , $endate){
        $this->loadModel('Holiday');
        $employee = $this->_getEmployee();
        $estimated = $task['ActivityTaskEmployeeRefer']['estimated'];
        $task = $this->ActivityTask->find('first',array(
            'conditions' => array('ActivityTask.id' => $task['ActivityTaskEmployeeRefer']['activity_task_id'] ),
            'recursive' => -1
        ));
        $_start = $task['ActivityTask']['task_start_date'];
        $_end = $task['ActivityTask']['task_end_date'];

        $holidays = $this->Holiday->getOptionHolidays($_start,$_end,$employee['company_id']);
        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $id_employee)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        $otherTaskDate = date('Y-m-d',$task['ActivityTask']['task_start_date']);
        $duration = 0;
        $rageDate = array();
        while($otherTaskDate <=  date('Y-m-d',$task['ActivityTask']['task_end_date'])){
            $chk = true;
            $intdate = strtotime($otherTaskDate);
            if(strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday'){
                $duration--;
                $chk = false;
            }
            if(!empty($holidays[$intdate])){
               $duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);
               $chk = false;
            }
            if(!empty($requests_absence[$intdate])){
                if($requests_absence[$intdate]['response_am'] == 'validated'){
                    $duration -= 0.5;
                }
                if($requests_absence[$intdate]['response_pm'] == 'validated'){
                    $duration -= 0.5;
                }
                 $chk = false;
            }
            if($chk == true){
                $rageDate[$otherTaskDate] = $otherTaskDate;
            }
            $duration++;
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        $__ragedate = $this->RageDate($stardate ,$endate) ;
        $count = 0;
        $endnote = false;
        foreach($__ragedate as $date){
            if(!empty($rageDate[$date])){
                if(date('Y-m-d',$task['ActivityTask']['task_end_date']) == $date){
                    $endnote = true;
                }
                $count++;
            }
        }
        if($duration == 0){
            $duration = 1;

        }
        if($endnote == true){
            return round($estimated/$duration,3) * $count + ($estimated - round($estimated/$duration,3) * $duration)  ;
        }else{
            return round($estimated/$duration,3) * $count;
        }
    }
    private function _AvgProjectWorkingForEmployee($task,$id_employee, $stardate , $endate){
        $this->loadModel('Holiday');
        $this->loadModel('ProjectTask');
        $employee = $this->_getEmployee();
        $estimated = $task['ProjectTaskEmployeeRefer']['estimated'];

        $task = $this->ProjectTask->find('first',array(
            'conditions' => array('ProjectTask.id' => $task['ProjectTaskEmployeeRefer']['project_task_id'] ),
            'recursive' => -1
        ));
        $_start = strtotime($task['ProjectTask']['task_start_date']);
        $_end = strtotime($task['ProjectTask']['task_end_date']);
        if(empty($_start) || $_start == '0000-00-00' ){
            $_start = $_end;
        }
        if($_start > $_end){
            $_start = $_end;
        }
        if($_start == '0000-00-00' && $_end == '0000-00-00'){

        }
        $holidays = $this->Holiday->getOptionHolidays($_start,$_end,$employee['company_id']);
        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $id_employee)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        $otherTaskDate = $task['ProjectTask']['task_start_date'];
        $duration = 0;
        $rageDate = array();
        while($otherTaskDate <=  $task['ProjectTask']['task_end_date']){
            $chk = true;
            $intdate = strtotime($otherTaskDate);
            if(strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday'){
                $duration--;
                $chk = false;
            }
            if(!empty($holidays[$intdate])){
               $duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);
               $chk = false;
            }
            if(!empty($requests_absence[$intdate])){
                if($requests_absence[$intdate]['response_am'] == 'validated'){
                    $duration -= 0.5;
                }
                if($requests_absence[$intdate]['response_pm'] == 'validated'){
                    $duration -= 0.5;
                }
                 $chk = false;
            }
            if($chk == true){
                $rageDate[$otherTaskDate] = $otherTaskDate;
            }
            $duration++;
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        $__ragedate = $this->RageDate($stardate ,$endate) ;
        $count = 0;
        $endnote = false;
        foreach($__ragedate as $date){
            if(!empty($rageDate[$date])){
                if($task['ProjectTask']['task_end_date'] == $date){
                    $endnote = true;
                }
                $count++;
            }
        }
        if($endnote == true){
            return round($estimated/$duration,2) * $count + ($estimated - round($estimated/$duration,2) * $duration)  ;
        }else{
            return round($estimated/$duration,2) * $count;
        }
    }
    private function RageDate($startdate ,$enddate){
        $arrayRagedate = array();
        $startdate = date('Y-m-d',$startdate);
        while($startdate <= date('Y-m-d',$enddate)){
            $arrayRagedate[$startdate] = $startdate;
           // $startdate += 86400;
             $startdate =  date('Y-m-d',strtotime($startdate. ' + 1 day'));
        }
        return $arrayRagedate;
    }
    public function getactivitytask($task_id = null){
        $this->layout = 'ajax';
        $result  = $this->ActivityTask->find('first',array(
            'conditions' => array('ActivityTask.id' => $task_id),
            'recursive' => -1
        ));
        $result['ActivityTask']['task_start_date'] = date('Y-m-d', $result['ActivityTask']['task_start_date']);
        $result['ActivityTask']['task_end_date'] = date('Y-m-d', $result['ActivityTask']['task_end_date']);
        $this->set(compact('result'));
    }
    public function listEmployeesJson($activity_id ,$task_id, $start = null, $end = null) {
        $this->layout = 'ajax';
        $listEmployee = $this->__getProfitCenterAndEmployee($activity_id,$task_id, $start, $end);
        $result = $listEmployee;
        $this->set(compact('result'));
    }
    private function __getProfitCenterAndEmployee($activity_id,$task_id, $start = null, $end = null){
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('Employee');
        $this->loadModel('CompanyEmployeeReference');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $employeeName = $this->_getEmployee();
		$listAssiged = array();
		if(is_numeric($task_id))
		{
			$listAssiged = $this->ActivityTaskEmployeeRefer->find('list',array(
				'recursive' => -1,
				'conditions' => array('activity_task_id' => $task_id),
				'fields' => array('reference_id','id')
			));
		}
        $ProfitRefer = ClassRegistry::init('ActivityProfitRefer');
        $profits = $ProfitRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id),
                'fields' => array('id', 'profit_center_id')
            ));
        $datas = array();

        if(!empty($profits)){
            $profitCenters = $this->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ProfitCenter.id' => $profits),
                    'order' => array(
                            'name' => 'ASC'
                    )
                ));
            $_profit = $_employee = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $_profit[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $_profit[$ks]['Employee']['is_profit_center'] = 1;
				$_profit[$ks]['Employee']['actif'] = 1;
                $_profit[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                if(!empty($listAssiged[$profitCenter['ProfitCenter']['id']])){
                    $_profit[$ks]['Employee']['is_selected'] = 1;
                }else{
                    $_profit[$ks]['Employee']['is_selected'] = 0;
                }
            }
            $employeeRefers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $profits),
                    'fields' => array('id', 'employee_id')
                ));
            if($start != -1 && $end != -1){
                $conditions = array(
                    'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    'NOT' => array('Employee.is_sas' => 1),
                    'OR' => array(
                        array('Employee.end_date' => '0000-00-00'),
                        array('Employee.end_date IS NULL'),
                        'AND' => array(
                                'Employee.start_date <=' => $end,
                                'Employee.end_date >=' => $start,
                        ),
                    'Employee.id' => $employeeRefers,
                    )
                );
            } else {
                $conditions = array(
                    'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    'NOT' => array('Employee.is_sas' => 1),
                    'OR' => array(
                        array('Employee.end_date' => '0000-00-00'),
                        array('Employee.end_date IS NULL'),
                        array('Employee.end_date >=' => date('Y-m-d', time())),
                    ),
                    'Employee.id' => $employeeRefers,
                );
            }
            if(!empty($employeeRefers)){
                $employeeRefers = array_unique($employeeRefers);
                 $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                    // 'conditions' => array(
                    //     'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    //     'NOT' => array('Employee.is_sas' => 1),
                    //     'OR' => array(
                    //         array('Employee.end_date' => '0000-00-00'),
                    //         array('Employee.end_date IS NULL'),
                    //         array('Employee.end_date >=' => date('Y-m-d', time())),
                    //     ),
                    //     'Employee.id' => $employeeRefers,
                    //     //'Employee.actif' => 1
                    // ),
                    'conditions' => $conditions,
                    'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name', 'Employee.actif'),
                    'order' => array(
                            'first_name' => 'ASC',
                            'last_name' => 'ASC'
                        )
                 ));
                foreach ($employees as $k => $employee) {
                    $_employee[$k]['Employee']['id'] = $employee['Employee']['id'];
                    $_employee[$k]['Employee']['is_profit_center'] = 0;
                    $_employee[$k]['Employee']['name'] = $employee['Employee']['first_name'].' '.$employee['Employee']['last_name'];
					//$_employee[$k]['Employee']['actif'] = $employee['Employee']['actif']; // luc truoc: cho nay neu ma not actif thi khong hien thi ra.
                    $_employee[$k]['Employee']['actif'] = 1; // bay gio fix lai. khach hang yeu cau not actif van hien thi ra, chi khi end date < current date moi an thoi

                    if(!empty($listAssiged[$employee['Employee']['id']])){
                        $_employee[$k]['Employee']['is_selected'] = 1;
                    }else{
                        $_employee[$k]['Employee']['is_selected'] = 0;
						/*if($employee['Employee']['actif']==0)
						{
							unset($_employee[$k]);
						}*/
                    }
                }
            }
            $datas = array_merge($_employee ,$_profit);
        } else {
            $profitCenters = $this->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employeeName['company_id']
                    )
                ));
            $_profit = $_employee = array();
			foreach ($profitCenters as $ks => $profitCenter) {
                $_profit[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $_profit[$ks]['Employee']['is_profit_center'] = 1;
                //$employ[$ks]['Employee']['profit_center_id'] = -1;
				$_profit[$ks]['Employee']['actif'] = 1;
                $_profit[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                if(!empty($listAssiged[$profitCenter['ProfitCenter']['id']])){
                    $_profit[$ks]['Employee']['is_selected'] = 1;
                }else{
                    $_profit[$ks]['Employee']['is_selected'] = 0;
                }
            }
              $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'conditions' => array(
                        'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                        'NOT' => array('Employee.is_sas' => 1),
                        'OR' => array(
                            array('Employee.end_date' => '0000-00-00'),
                            array('Employee.end_date IS NULL'),
                            array('Employee.end_date >=' => date('Y-m-d', time())),
                        )
                        //'Employee.actif' => 1
                    ),
                    'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name', 'Employee.actif'),
                    'order' => array(
                            'first_name' => 'ASC',
                            'last_name' => 'ASC'
                        )
                 ));
			foreach ($employees as $k => $employee) {
				$_employee[$k]['Employee']['id'] = $employee['Employee']['id'];
				$_employee[$k]['Employee']['is_profit_center'] = 0;
				//$_employee[$k]['Employee']['actif'] = $employee['Employee']['actif']; // luc truoc: cho nay neu ma not actif thi khong hien thi ra.
                $_employee[$k]['Employee']['actif'] = 1; // bay gio fix lai. khach hang yeu cau not actif van hien thi ra, chi khi end date < current date moi an thoi
				$_employee[$k]['Employee']['name'] = $employee['Employee']['first_name'].' '.$employee['Employee']['last_name'];
				if(!empty($listAssiged[$employee['Employee']['id']])){
					$_employee[$k]['Employee']['is_selected'] = 1;
				}else{
					$_employee[$k]['Employee']['is_selected'] = 0;
					/*if($employee['Employee']['actif']==0)
					{
						unset($_employee[$k]);
					}*/
				}
			}
            $datas = array_merge($_employee, $_profit );
        }
        return $datas;

    }
    private function countdate($startdate ,$enddate ,$employee_id){
        $employee_curr = $this->_getEmployee();
        $holidays = $this->Holiday->getOptionHolidays($startdate,$enddate,$employee_curr['company_id']);
        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($startdate, $enddate), 'employee_id' => $employee_id)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        $startdate = date('Y-m-d',$startdate);
        $duration = 0;
        while($startdate <= date('Y-m-d',$enddate)){
            $duration++;
            $intdate = strtotime($startdate);
            if(strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday'){
                $duration--;
            }
            if(!empty($holidays[$intdate])){
               $duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);

            }
            if(!empty($requests_absence[$intdate])){
                if($requests_absence[$intdate]['response_am'] == 'validated'){
                    $duration -= 0.5;
                }
                if($requests_absence[$intdate]['response_pm'] == 'validated'){
                    $duration -= 0.5;
                }

            }
            $startdate =  date('Y-m-d',strtotime($startdate. ' + 1 day'));
        }

        return $duration;
    }
     /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 52) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        }

        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

    /**
     * Ham xu ly va lay cac gia tri cua task thuoc 1 project
     *
     * @return void
     * @access private
     */
    private function _taskCaculates($project_id = null, $company_id = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $projectTasks = $this->ProjectTask->find('all', array(
            //'recursive' => -1,
            'conditions' => array('ProjectTask.project_id' => $project_id),
            'fields' => array('id', 'task_title', 'parent_id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'estimated', 'predecessor')
        ));
        $taskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');

        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $taskIds),
            'fields' => array('project_task_id', 'id')
        ));
        $activityRequests = $this->ActivityRequest->find(
                'all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $activityTasks,
                'company_id' => $company_id,
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $activityRequests = Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0.value');
        $consumeds = array();
        foreach($activityTasks as $id => $activityTask){
            if(in_array($activityTask, array_keys($activityRequests))){
                $consumeds[$id] = $activityRequests[$activityTask];
            } else {
                $consumeds[$id] = 0;
            }
        }
        foreach($projectTasks as $key => $projectTask){
            foreach($consumeds as $id => $consumed){
                if($projectTask['ProjectTask']['id'] == $id){
                    if($projectTask['ProjectTask']['estimated'] == 0){
                        $projectTasks[$key]['ProjectTask']['completed'] = 0;
                    } else{
                        $projectTasks[$key]['ProjectTask']['completed'] = round((($consumed*100)/$projectTask['ProjectTask']['estimated']), 2);
                    }
                    $projectTasks[$key]['ProjectTask']['consumed'] = $consumed;
                }
            }
        }
        $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
        if(!empty($parentIds)){
            foreach($parentIds as $key => $parentId){
                if($parentId == 0 || $parentId == ''){
                    unset($parentIds[$key]);
                }
            }
            $dataParents = array();
            if(!empty($parentIds)){
                foreach($parentIds as $parentId){
                    foreach($projectTasks as $projectTask){
                        if($parentId == $projectTask['ProjectTask']['parent_id']){
                            $dataParents[$parentId]['estimated'][] = $projectTask['ProjectTask']['estimated'];
                            $dataParents[$parentId]['consumed'][] = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
                        }
                    }
                }
            }
            if(!empty($dataParents)){
                foreach($dataParents as $id => $dataParent){
                    $_estimated = array_sum($dataParent['estimated']);
                    $_consumed = array_sum($dataParent['consumed']);
                    $dataParents[$id]['estimated'] = $_estimated;
                    $dataParents[$id]['consumed'] = $_consumed;
                    if($_estimated == 0){
                        $dataParents[$id]['completed'] = 0;
                    } else{
                        $dataParents[$id]['completed'] = round((($_consumed*100)/$_estimated), 2);
                    }
                }
            }
            if(!empty($dataParents)){
                foreach($projectTasks as $key => $projectTask){
                    foreach($dataParents as $id => $dataParent){
                        if($projectTask['ProjectTask']['id'] == $id){
                            $projectTasks[$key]['ProjectTask']['estimated'] = $dataParent['estimated'];
                            $projectTasks[$key]['ProjectTask']['consumed'] = $dataParent['consumed'];
                            $projectTasks[$key]['ProjectTask']['completed'] = $dataParent['completed'];
                        }
                    }
                }
            }
        }
        //xu ly phan assign employee
        $this->loadModel('Employee');
        $this->loadModel('ProfitCenter');
        $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname')
            ));
        $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        foreach($projectTasks as $key => $projectTask){
            if(!empty($projectTask['ProjectTaskEmployeeRefer'])){
                foreach($projectTask['ProjectTaskEmployeeRefer'] as $k => $vl){
                    if($vl['is_profit_center'] == 0){
                        $projectTasks[$key]['ProjectTask']['assign'][] = !empty($employees[$vl['reference_id']]) ? $employees[$vl['reference_id']] : '';
                    } else {
                        $projectTasks[$key]['ProjectTask']['assign'][] = !empty($profitCenters[$vl['reference_id']]) ? 'PC / ' . $profitCenters[$vl['reference_id']] : '';
                    }
                }
                unset($projectTasks[$key]['ProjectTaskEmployeeRefer']);
            } else {
                unset($projectTasks[$key]['ProjectTaskEmployeeRefer']);
            }
        }
        // the data of task
        $datas = array();
        foreach($projectTasks as $key => $projectTask){
            $datas[$key]['id'] = 'task-' . $projectTask['ProjectTask']['id'];
            if($projectTask['ProjectTask']['parent_id'] != 0){
                $datas[$key]['project_part_id'] = 'task-' . $projectTask['ProjectTask']['parent_id'];
            } else {
                $datas[$key]['project_part_id'] = $projectTask['ProjectTask']['project_planed_phase_id'];
            }
            $datas[$key]['name'] = $projectTask['ProjectTask']['task_title'];
            $datas[$key]['predecessor'] = !empty($projectTask['ProjectTask']['predecessor']) ? 'task-' . $projectTask['ProjectTask']['predecessor'] : '';
            $datas[$key]['completed'] = isset($projectTask['ProjectTask']['completed']) ? $projectTask['ProjectTask']['completed'] : 0;
            $datas[$key]['color'] = '';
            $datas[$key]['start'] = isset($projectTask['ProjectTask']['task_start_date']) ? strtotime($projectTask['ProjectTask']['task_start_date']) : 0;
            $datas[$key]['end'] = isset($projectTask['ProjectTask']['task_end_date']) ? strtotime($projectTask['ProjectTask']['task_end_date']) : 0;
            $datas[$key]['rstart'] = 0;
            $datas[$key]['rend'] = 0;
            $datas[$key]['assign'] = !empty($projectTask['ProjectTask']['assign']) ? implode(', ', $projectTask['ProjectTask']['assign']) : '';
        }
        $parentTaskIds = array_unique(Set::classicExtract($datas, '{n}.project_part_id'));
        foreach($parentTaskIds as $key => $parentTaskId){
            if(is_numeric($parentTaskId)){
                unset($parentTaskIds[$key]);
            }
        }
        $rChilds = array();
        foreach($datas as $key => $data){
            foreach($parentTaskIds as $parentTaskId){
                if($data['project_part_id'] == $parentTaskId){
                    $rChilds[$parentTaskId][] = $data;
                    unset($datas[$key]);
                }
            }
        }
        if(!empty($rChilds)){
            foreach($datas as $key => $data){
                foreach($rChilds as $id => $rChild){
                    if($data['id'] == $id){
                        foreach($rChild as $vl){
                            $vl['start'] = !empty($vl['start']) && $vl['start']>0 ? $vl['start'] : $data['start'];
                            $datas[$key]['children'][] = $vl;
                        }
                    }
                }
            }
        }
        // caculate completed of phase
        $phases = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_planed_phase_id'));
        $_phases = array();
        foreach($phases as $phase){
            foreach($projectTasks as $projectTask){
                if($phase == $projectTask['ProjectTask']['project_planed_phase_id']){
                    $_phases[$phase]['estimated'][] = $projectTask['ProjectTask']['estimated'];
                    $_phases[$phase]['consumed'][] = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
                }
            }
        }
        foreach($_phases as $id => $_phase){
            $_estimated = array_sum($_phase['estimated']);
            $_consumed = array_sum($_phase['consumed']);
            $_phases[$id]['estimated'] = $_estimated;
            $_phases[$id]['consumed'] = $_consumed;
            if($_estimated == 0){
                $_phases[$id]['completed'] = 0;
            } else{
                $_phases[$id]['completed'] = round((($_consumed*100)/$_estimated), 2);
            }
        }
        // caculate completed of part
        $this->loadModel('ProjectPhasePlan');
        $phasePlans = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'NOT' => array('project_part_id' => null)
            ),
            'fields' => array('id', 'project_part_id')
        ));
        $parts = array();
        if(!empty($phasePlans)){
            foreach($phasePlans as $phaseId => $phasePlan){
                foreach($_phases as $id => $_phase){
                    if($phaseId == $id){
                        $parts[$phasePlan]['estimated'][] = $_phase['estimated'];
                        $parts[$phasePlan]['consumed'][] = $_phase['consumed'];
                    }
                }
            }
        }
        foreach($parts as $id => $part){
            $_estimated = array_sum($part['estimated']);
            $_consumed = array_sum($part['consumed']);
            $parts[$id]['estimated'] = $_estimated;
            $parts[$id]['consumed'] = $_consumed;
            if($_estimated == 0){
                $parts[$id]['completed'] = 0;
            } else{
                $parts[$id]['completed'] = round((($_consumed*100)/$_estimated), 2);
            }
        }
        $results['task'] = $datas;
        $results['phase'] = $_phases;
        $results['part'] = $parts;

        return $results;
    }

    /**
     * Phan Staffing danh cho cac manager, project manager filter cac employee cua ho
     */
    public function staffing_manager($profitId = null){
        if (!($params = $this->_getProfits($profitId))) {
			$this->Session->setFlash(__('The data was not found, please try again', true), 'error');
			$this->redirect(array('controller' => 'employees', 'action' => 'index'));
		}
		list($profit, $paths, $employees, $employeeName) = $params;
        echo 'Module Building!';
        exit;
    }

    /**
	 * Get Company By ID
	 *
	 * @return void
	 * @access protected
	 */
	function _getProfits($profitId) {
	   $this->loadModel('ActivityForecast');
		if (!($user = $this->_getEmployee())) {
			return false;
		}
		$Model = ClassRegistry::init('ProfitCenter');
		$profit = $Model->find('first', array(
			'recursive' => -1, 'fields' => array('id', 'lft', 'rght'),
			'conditions' => array('company_id' => $user['company_id'], 'manager_id' => $user['id'])));
		$isAdmin = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr';
		if ($isAdmin) {
			$paths = $Model->generatetreelist(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
		} elseif (!empty($profit)) {
			$paths = $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array(
					'lft >=' => $profit['ProfitCenter']['lft'],
					'rght <=' => $profit['ProfitCenter']['rght']
					)));
			$paths = $Model->generatetreelist(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
		} else {
			return false;
		}
		if (empty($profitId)) {
			$profitId = $profit['ProfitCenter']['id'];
		} elseif (!isset($paths[$profitId])) {
			return false;
		}
		$profit = $Model->find('first', array('recursive' => -1, 'conditions' => array('id' => $profitId)));
		$profit = !empty($profit) ? array_shift($profit) : null;
		$list = array_merge(array($profit['manager_id']), $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'manager_id'), 'conditions' => array(
						'NOT' => array('manager_id' => null), 'parent_id' => $profit['id']))));
		$list = array_unique(array_filter($list));
		$employees = array_merge($list, $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
					'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
					'conditions' => array('profit_center_id' => $profit['id']))));
		$employees = Set::combine($this->ActivityForecast->Employee->find('all', array(
							'order' => 'id DESC',
							'fields' => array('id', 'first_name', 'last_name'),
							'recursive' => -1, 'conditions' => array('id' => $employees)))
						, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
		$_managers = array();
		foreach ($list as $key) {
			if (isset($employees[$key])) {
				$_managers[$key] = $employees[$key] . '<strong> (Manager)</strong>';
			}
		}
		$employees = $_managers + $employees;
		if (!$isAdmin) {
			//unset($employees[$user['id']]);
		}
		return array($profit, $paths, $employees, $user);
	}


    /**
     * Import CSV
     *
     * @param int $activity_id
     * @return void
     * @access public
     */
    function import_csv($activity_id = null) {
        set_time_limit(0);
        $this->loadModel('Activity');
        $activities = $this->Activity->find('first', array(
            'recursive' => -1,
            'conditions' => array('Activity.id' => $activity_id),
            'fields' => array('project')
        ));
        if(!empty($activities) && !empty($activities['Activity']['project'])){
            $this->_importCsvProject($activities['Activity']['project'], $activity_id);
        } else {
            $this->_importCsvActivity($activity_id);
        }
    }
    /**
     * Import CSV for Activity
     */
    private function _importCsvActivity($activity_id = null) {
        set_time_limit(0);
        //$this->autoRender = false;
        $employeeName = $this->_getEmployee();
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Tasks' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Tasks' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        //'Update' => array(),
                        'Error' => array()
                    );
                    $columnMandatory = array(
                        'Task Name',
                        'Parent Task Name',
                        'Assigned To',
                        'Start Date',
                        'End Date',
                        'Workload'
                    );
                    $default = array();
                    if(!empty($csv->titles)){
                        foreach($csv->titles as $headName){
                            if(in_array($headName, $columnMandatory)){
                                $default[$headName] = '';
                            }
                        }
                    }
                    $this->loadModel('Employee');
                    $this->loadModel('ProjectStatus');
                    $this->Employee->cacheQueries = true;

                    $validate = array('Task Name');
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    // employee of company
                    $employeeCompanies = $this->Employee->CompanyEmployeeReference->find('all', array(
                        'conditions' => array(
                            'CompanyEmployeeReference.company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
                    ));
                    $employeeCompanies = !empty($employeeCompanies) ? Set::combine($employeeCompanies, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
                    $employeeIdCompanies = !empty($employeeCompanies) ? array_flip($employeeCompanies) : array();
                    $employeeIdCompanies = !empty($employeeIdCompanies) ? array_change_key_case($employeeIdCompanies, CASE_LOWER) : array();
                    // status of company
                    $statusOfCompanies = $this->ProjectStatus->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'name' => array('Ouvert', 'Open'),
                            'company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('id')
                    ));
                    // mac dinh cac task import vao co status la OPEN
                    $statusOpen = !empty($statusOfCompanies) ? $statusOfCompanies['ProjectStatus']['id'] : '';
                    $listParentTaskImports = $listSubTaskHaveImports = $listEndChildrens = array();
                    foreach ($csv->data as $row) {
                        if(isset($row['#']) || isset($row['No.'])){
                            unset($row['#']);
                            unset($row['No.']);
                        }
                        foreach(array_keys($row) as $name){
                            if(!in_array($name, $columnMandatory)){
                                unset($row[$name]);
                            }
                        }
                        $error = false;
                        $row = array_merge(array_combine($defaultKeys, array_slice(array_map('trim', array_values($row))
                                                + array_fill(0, $count, ''), 0, $count)), array(
                            'data' => array(),
                            'error' => array()));

                        foreach ($validate as $key => $value) {
                            $row[$value] = trim($row[$value]);
                            if (empty($row[$value])) {
                                $row['columnHighLight'][$value] = '';
                                $row['error'][] = sprintf(__('The %s is not blank', true), $value);
                            }
                        }
                        if(empty($row['error'])) {
                            //Task Name
                            if(!empty($row['Task Name'])) {
                                $parentTasks = null;
                                if(!empty($row['Parent Task Name'])){ // sub task
                                    if(!empty($listSubTaskHaveImports[trim($row['Parent Task Name'])][trim($row['Task Name'])])){
                                        $row['columnHighLight']['Task Name'] = '';
                                        $row['error'][] = __('The sub task name has identical', true);
                                    } else {
                                        if(in_array($row['Parent Task Name'], $listEndChildrens)){ // HuuPC
                                            $row['columnHighLight']['Parent Task Name'] = '';
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('Don\'t allow create sub - sub task.', true);
                                        } else {
                                            $parentTasks = $this->ActivityTask->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array(
                                                    'activity_id' => $activity_id,
                                                    'name' => trim($row['Parent Task Name'])
                                                ),
                                                'fields' => array('id', 'parent_id')
                                            ));
                                            $_parentIds = !empty($parentTasks['ActivityTask']['parent_id']) ? $parentTasks['ActivityTask']['parent_id'] : 0;
                                            if(!empty($parentTasks) && $_parentIds != 0){
                                                $row['columnHighLight']['Parent Task Name'] = '';
                                                $row['columnHighLight']['Task Name'] = '';
                                                $row['error'][] = __('Don\'t allow create sub - sub task.', true);
                                            } else {
                                                 if(!empty($parentTasks) && $parentTasks['ActivityTask']['id']){ // da co parent task trong du lieu
                                                    $haveConsumed = 0;
                                                    $this->loadModel('ActivityRequest');
                                                    $haveConsumed = $this->ActivityRequest->find('count', array(
                                                        'recursive' => -1,
                                                        'conditions' => array(
                                                            'task_id' => $parentTasks['ActivityTask']['id'],
                                                            'status' => 2
                                                        )
                                                    ));
                                                    if($haveConsumed == 0){
                                                        $row['data']['parent_id'] = $row['Parent Task Name'];
                                                        /**
                                                         * Kiem tra xem task import da ton tai trong parent nay chua
                                                         */
                                                        $checkTaskImports = $this->ActivityTask->find('first', array(
                                                            'recursive' => -1,
                                                            'conditions' => array(
                                                                'activity_id' => $activity_id,
                                                                'name' => trim($row['Task Name']),
                                                                'parent_id' => $parentTasks['ActivityTask']['id']
                                                            ),
                                                            'fields' => array('id')
                                                        ));
                                                        if(!empty($checkTaskImports) && $checkTaskImports['ActivityTask']['id']){
                                                            $row['columnHighLight']['Task Name'] = '';
                                                            $row['error'][] = __('The sub task name has exists', true);
                                                        } else {
                                                            $row['data']['name'] = $row['Task Name'];
                                                            $listSubTaskHaveImports[trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                        }
                                                    } else {
                                                        $row['columnHighLight']['Parent Task Name'] = '';
                                                        $row['error'][] = __('The parent task has consumed', true);
                                                    }
                                                 } else { // chua co parent task trong database
                                                    $row['data']['parent_id'] = $row['Parent Task Name'];
                                                    $row['data']['name'] = $row['Task Name'];
                                                    $listSubTaskHaveImports[trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                    $listEndChildrens[trim($row['Task Name'])] = trim($row['Task Name']);
                                                }
                                            }
                                        }
                                    }
                                } else { // task
                                    if(in_array(trim($row['Task Name']), $listParentTaskImports)){
                                        $row['columnHighLight']['Task Name'] = '';
                                        $row['error'][] = __('The task name has identical', true);
                                    } else {
                                        $checkTasks = $this->ActivityTask->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'activity_id' => $activity_id,
                                                'name' => trim($row['Task Name'])
                                            ),
                                            'fields' => array('id')
                                        ));
                                        if(!empty($checkTasks) && $checkTasks['ActivityTask']['id']){
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('The task name has exists', true);
                                        } else {
                                            $listParentTaskImports[trim($row['Task Name'])] = trim($row['Task Name']);
                                            $row['data']['name'] = $row['Task Name'];
                                        }
                                    }
                                }
                            } else { // khong co task name, nhung co parent task
                                if(!empty($row['Parent Task Name'])){
                                    if(in_array(trim($row['Parent Task Name']), $listParentTaskImports)){
                                        $row['columnHighLight']['Parent Task Name'] = '';
                                        $row['error'][] = __('The parent task name has identical', true);
                                    } else {
                                        $checkTasks = $this->ActivityTask->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'activity_id' => $activity_id,
                                                'name' => trim($row['Parent Task Name'])
                                            ),
                                            'fields' => array('id')
                                        ));
                                        if(!empty($checkTasks) && $checkTasks['ActivityTask']['id']){
                                            $row['columnHighLight']['Parent Task Name'] = '';
                                            $row['error'][] = __('The task name has exists', true);
                                        } else {
                                            $listParentTaskImports[trim($row['Parent Task Name'])] = trim($row['Parent Task Name']);
                                            $row['data']['name'] = $row['Parent Task Name'];
                                        }
                                    }
                                } else {
                                    $row['columnHighLight']['Task Name'] = '';
                                    $row['columnHighLight']['Parent Task Name'] = '';
                                    $row['error'][] = __('The task name and parent task name are not blank!', true);
                                }
                            }
                            // Start Date And End Date
                            if(!empty($row['Start Date']) || !empty($row['End Date'])){
                                $_start = $_end = 0;
                                if(!empty($row['Start Date'])){
                                    $_start = $this->_formatDateCustom($row['Start Date']);
                                }
                                if(!empty($row['End Date'])){
                                    $_end = $this->_formatDateCustom($row['End Date']);
                                }
                                if($_start == 0){
                                    $_start = $this->_durationStartDate('', date('Y-m-d', $_end), 2);
                                    $_start = ($_start != '0000-00-00') ? strtotime($_start) : 0;
                                }
                                if($_end == 0){
                                    $_end = $this->_durationEndDate(date('Y-m-d', $_start), '', 2);
                                    $_end = ($_end != '0000-00-00') ? strtotime($_end) : 0;
                                }
                                //debug($row['Task Name'] . ' - ' . $row['Parent Task Name'] . ' - ' . date('d-m-Y', $_start));
                                if($_start > $_end){
                                    $row['columnHighLight']['End Date'] = '';
                                    $row['error'][] = __('The end date must be greater than start date.', true);
                                } else {
                                    $row['data']['task_start_date'] = $_start;
                                    $row['data']['task_end_date'] = $_end;
                                    $row['data']['duration'] = $this->_getWorkingDays(date('Y-m-d', $_start), date('Y-m-d', $_end), '');
                                }
                            } else {
                                $row['data']['task_start_date'] = 0;
                                $row['data']['task_end_date'] = 0;
                                $row['data']['duration'] = '';
                            }
                            // Workload
                            if($row['Workload'] == null){
                                $row['data']['estimated'] = 0;
                            } elseif($row['Workload'] == 0){
                                $row['data']['estimated'] = 0;
                            } elseif(is_numeric($row['Workload']) && $row['Workload'] > 0) {
                                $row['data']['estimated'] = $row['Workload'];
                            } else {
                                $row['data']['estimated'] = 0;
                            }
                            // Assign To
                            if(!empty($row['Assigned To'])){
                                $assigns = explode(',', $row['Assigned To']);
                                if(!empty($assigns)){
                                    $listAssigns = array();
                                    foreach($assigns as $assign){
                                        $assign = strtolower(trim($assign));
                                        if(in_array($assign, array_keys($employeeIdCompanies))){
                                            $listAssigns[] = $employeeIdCompanies[$assign];
                                        }
                                    }
                                    if(!empty($listAssigns)){
                                        $row['data']['assign'] = implode(', ', $listAssigns);
                                    }
                                }
                            }
                            // them project id va status id cho task duoc assign
                            $row['data']['activity_id'] = $activity_id;
                            $row['data']['task_status_id'] = $statusOpen;
                        }
                        if (!empty($row['error'])) {
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            $records['Create'][] = $row;
                        }
                    }
                }
                unlink($filename);
            }
            $this->set('records', $records);
            $this->set('default', $default);
            $this->set(compact('project_id', 'activity_id'));
        } else {
            $this->set(compact('project_id', 'activity_id'));
            $this->redirect(array('action' => 'index', $activity_id));
        }
    }

    /**
     * Import CSV for project
     */
    private function _importCsvProject($project_id = null, $activity_id = null) {
        set_time_limit(0);
        //$this->autoRender = false;
        $employeeName = $this->_getEmployee();
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!$this->_checkRole(true, $project_id, empty($_FILES['FileField']['name']['csv_file_attachment']) ? array('element' => 'warning') : array())) {
            $_FILES['FileField']['name']['csv_file_attachment'] = '';
        }
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Tasks' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Tasks' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        //'Update' => array(),
                        'Error' => array()
                    );
                    $columnMandatory = array(
                        'Task Name',
                        'Parent Task Name',
                        'Assigned To',
                        'Start Date',
                        'End Date',
                        'Workload',
                        'Phase Name',
                        'Part Name'
                    );
                    $default = array();
                    if(!empty($csv->titles)){
                        foreach($csv->titles as $headName){
                            if(in_array($headName, $columnMandatory)){
                                $default[$headName] = '';
                            }
                        }
                    }
                    $this->loadModel('ProjectPhasePlan');
                    $this->loadModel('ProjectPhase');
                    $this->loadModel('Employee');
                    $this->loadModel('ProjectStatus');
                    $this->loadModel('ProjectTask');
                    $this->ProjectPhase->cacheQueries = true;
                    $this->Employee->cacheQueries = true;

                    $validate = array('Task Name');
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    // tach parent task va cac tham so can thiet
                    $groupNamePhaseInputs = $listTreeTasks = array();
                    foreach($csv->data as $val){
                        if(!empty($val['Phase Name'])){
                            $groupNamePhaseInputs[] = $val['Phase Name'];
                            $part = !empty($val['Part Name']) ? $val['Part Name'] : -1;
                            $phase = $val['Phase Name'];
                            $parent = $val['Parent Task Name'];
                            $task = $val['Task Name'];
                            if(!empty($parent)){
                                $listTreeTasks[$part][$phase][$parent][] = $task;
                            } else {
                                $listTreeTasks[$part][$phase][$task] = array();
                            }
                        }
                    }
                    $groupNamePhaseInputs = !empty($groupNamePhaseInputs) ? array_unique($groupNamePhaseInputs) : array();
                    // phase of company
                    $phaseOfCompanies = $this->ProjectPhase->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $employeeName['company_id'],
                            'name' => $groupNamePhaseInputs
                        ),
                        'fields' => array('id', 'name')
                    ));
                    // employee of company
                    $employeeCompanies = $this->Employee->CompanyEmployeeReference->find('all', array(
                        'conditions' => array(
                            'CompanyEmployeeReference.company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
                    ));
                    $employeeCompanies = !empty($employeeCompanies) ? Set::combine($employeeCompanies, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
                    $employeeIdCompanies = !empty($employeeCompanies) ? array_flip($employeeCompanies) : array();
                    $employeeIdCompanies = !empty($employeeIdCompanies) ? array_change_key_case($employeeIdCompanies, CASE_LOWER) : array();
                    // status of company
                    $statusOfCompanies = $this->ProjectStatus->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'name' => array('Ouvert', 'Open'),
                            'company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('id')
                    ));
                    // mac dinh cac task import vao co status la OPEN
                    $statusOpen = !empty($statusOfCompanies) ? $statusOfCompanies['ProjectStatus']['id'] : '';
                    $listParentTaskImports = $listSubTaskHaveImports = $listEndChildrens = array();
                    foreach ($csv->data as $row) {
                        if(isset($row['#']) || isset($row['No.'])){
                            unset($row['#']);
                            unset($row['No.']);
                        }
                        foreach(array_keys($row) as $name){
                            if(!in_array($name, $columnMandatory)){
                                unset($row[$name]);
                            }
                        }
                        $error = false;
                        $row = array_merge(array_combine($defaultKeys, array_slice(array_map('trim', array_values($row))
                                                + array_fill(0, $count, ''), 0, $count)), array(
                            'data' => array(),
                            'error' => array()));

                        foreach ($validate as $key => $value) {
                            $row[$value] = trim($row[$value]);
                            if (empty($row[$value])) {
                                $row['columnHighLight'][$value] = '';
                                $row['error'][] = sprintf(__('The %s is not blank', true), $value);
                            }
                        }
                        if(empty($row['error'])) {
                            // get phase id and part id
                            $projectPhaseId = $projectPartId = null;
                            // Part
                            if(!empty($row['Part Name'])){
                                $this->loadModel('ProjectPart');
                                $projectPartId = $this->ProjectPart->find('first', array(
                                    'recursive' => -1,
                                    'fields' => array('id'),
                                    'conditions' => array('title' => $row['Part Name'])));
                                $projectPartId = !empty($projectPartId) ? $projectPartId['ProjectPart']['id'] : -1;
                            }
                            // Phase
                            $projectPhasePlans = array();
                            if(!empty($row['Phase Name'])){
                                if(in_array($row['Phase Name'], $phaseOfCompanies)){
                                    $tmpPhases = array_flip($phaseOfCompanies);
                                    $projectPhaseId = $tmpPhases[$row['Phase Name']];
                                } else {
                                    $row['columnHighLight']['Phase Name'] = '';
                                    $row['error'][] = __('The Phase name not found in company', true);
                                }
                                $projectPhasePlans = $this->ProjectPhasePlan->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array('project_part_id' => $projectPartId, 'project_planed_phase_id' => $projectPhaseId, 'project_id' => $project_id),
                                    'fields' => array('id')
                                ));
                            } else {
                                $projectPhasePlans = $this->ProjectPhasePlan->find('first', array(
                                    'recursive' => -1,
                                    'order' => array('weight' => 'asc'),
                                    'conditions' => array('project_id' => $project_id),
                                    'limit' => 1,
                                    'fields' => array('id')
                                ));
                            }

                            //Task Name
                            if (!empty($row['Task Name'])) {
                                if(!empty($projectPhasePlans) && $projectPhasePlans['ProjectPhasePlan']['id']){
                                    $row['data']['project_planed_phase_id'] = $projectPhasePlans['ProjectPhasePlan']['id'];
                                    $row['data']['phase_name'] = $row['Phase Name'];
                                    // Parent Task
                                    $parentTasks = null;
                                    if(!empty($row['Parent Task Name'])){ // sub task
                                        if(!empty($listSubTaskHaveImports[$projectPhasePlans['ProjectPhasePlan']['id']][trim($row['Parent Task Name'])][trim($row['Task Name'])])){
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('The sub task name has identical', true);
                                        } else {
                                            if(in_array($row['Parent Task Name'], $listEndChildrens)){ // HuuPC
                                                $row['columnHighLight']['Parent Task Name'] = '';
                                                $row['columnHighLight']['Task Name'] = '';
                                                $row['error'][] = __('Don\'t allow create sub - sub task.', true);
                                            } else {
                                                $parentTasks = $this->ProjectTask->find('first', array(
                                                    'recursive' => -1,
                                                    'conditions' => array(
                                                        'project_id' => $project_id,
                                                        'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                        'task_title' => trim($row['Parent Task Name'])
                                                    ),
                                                    'fields' => array('id', 'parent_id')
                                                ));
                                                if(!empty($parentTasks) && $parentTasks['ProjectTask']['parent_id'] != 0){
                                                    $row['columnHighLight']['Parent Task Name'] = '';
                                                    $row['columnHighLight']['Task Name'] = '';
                                                    $row['error'][] = __('Don\'t allow create sub - sub task.', true);
                                                } else {
                                                    if(!empty($parentTasks) && $parentTasks['ProjectTask']['id']){ // da co parent task trong du lieu
                                                        $aTasks = $this->ActivityTask->find('first', array(
                                                            'recursive' => -1,
                                                            'conditions' => array('project_task_id' => $parentTasks['ProjectTask']['id']),
                                                            'fields' => array('id')
                                                        ));
                                                        $haveConsumed = 0;
                                                        if(!empty($aTasks) && $aTasks['ActivityTask']['id']){
                                                            $this->loadModel('ActivityRequest');
                                                            $haveConsumed = $this->ActivityRequest->find('count', array(
                                                                'recursive' => -1,
                                                                'conditions' => array(
                                                                    'task_id' => $aTasks['ActivityTask']['id'],
                                                                    'status' => 2
                                                                )
                                                            ));
                                                        }
                                                        if($haveConsumed == 0){
                                                            $row['data']['parent_id'] = $row['Parent Task Name'];
                                                            /**
                                                             * Kiem tra xem task import da ton tai trong parent nay chua
                                                             */
                                                            $checkTaskImports = $this->ProjectTask->find('first', array(
                                                                'recursive' => -1,
                                                                'conditions' => array(
                                                                    'project_id' => $project_id,
                                                                    'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                                    'task_title' => trim($row['Task Name']),
                                                                    'parent_id' => $parentTasks['ProjectTask']['id']
                                                                ),
                                                                'fields' => array('id')
                                                            ));
                                                            if(!empty($checkTaskImports) && $checkTaskImports['ProjectTask']['id']){
                                                                $row['columnHighLight']['Task Name'] = '';
                                                                $row['error'][] = __('The sub task name has exists', true);
                                                            } else {
                                                                $row['data']['task_title'] = $row['Task Name'];
                                                                $listSubTaskHaveImports[$projectPhasePlans['ProjectPhasePlan']['id']][trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                            }
                                                        } else {
                                                            $row['columnHighLight']['Parent Task Name'] = '';
                                                            $row['error'][] = __('The parent task has consumed', true);
                                                        }
                                                    } else { // chua co parent task trong database
                                                        $row['data']['parent_id'] = $row['Parent Task Name'];
                                                        $row['data']['task_title'] = $row['Task Name'];
                                                        $listEndChildrens[trim($row['Task Name'])] = trim($row['Task Name']);
                                                        $listSubTaskHaveImports[$projectPhasePlans['ProjectPhasePlan']['id']][trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                    }
                                                }
                                            }
                                        }
                                    } else { // task
                                        if(in_array(trim($row['Task Name']), array_keys($listParentTaskImports))
                                        && in_array($projectPhasePlans['ProjectPhasePlan']['id'], $listParentTaskImports)
                                        ){
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('The task name has identical', true);
                                        } else {
                                            $checkTasks = $this->ProjectTask->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array(
                                                    'project_id' => $project_id,
                                                    'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                    'task_title' => trim($row['Task Name'])
                                                ),
                                                'fields' => array('id')
                                            ));
                                            if(!empty($checkTasks) && $checkTasks['ProjectTask']['id']){
                                                $row['columnHighLight']['Task Name'] = '';
                                                $row['error'][] = __('The task name has exists', true);
                                            } else {
                                                $listParentTaskImports[trim($row['Task Name'])] = $projectPhasePlans['ProjectPhasePlan']['id'];
                                                $row['data']['task_title'] = $row['Task Name'];
                                            }
                                        }

                                    }
                                } else {
                                    $row['columnHighLight']['Phase Name'] = '';
                                    $row['columnHighLight']['Part Name'] = '';
                                    $row['error'][] = __('The Phase name or Part name not found in project', true);
                                }
                            } else { // khong co task name, nhung co parent task
                                if(!empty($projectPhasePlans) && $projectPhasePlans['ProjectPhasePlan']['id']){
                                    $row['data']['project_planed_phase_id'] = $projectPhasePlans['ProjectPhasePlan']['id'];
                                    $row['data']['phase_name'] = $row['Phase Name'];
                                    if(!empty($row['Parent Task Name'])){
                                        if(in_array(trim($row['Parent Task Name']), array_keys($listParentTaskImports))
                                        && in_array($projectPhasePlans['ProjectPhasePlan']['id'], $listParentTaskImports)
                                        ){
                                            $row['columnHighLight']['Parent Task Name'] = '';
                                            $row['error'][] = __('The task name has identical', true);
                                        } else {
                                            $checkTasks = $this->ProjectTask->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array(
                                                    'project_id' => $project_id,
                                                    'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                    'task_title' => trim($row['Parent Task Name'])
                                                ),
                                                'fields' => array('id')
                                            ));
                                            if(!empty($checkTasks) && $checkTasks['ProjectTask']['id']){
                                                $row['columnHighLight']['Parent Task Name'] = '';
                                                $row['error'][] = __('The task name has exists', true);
                                            } else {
                                                $listParentTaskImports[trim($row['Parent Task Name'])] = $projectPhasePlans['ProjectPhasePlan']['id'];
                                                $row['data']['task_title'] = $row['Parent Task Name'];
                                            }
                                        }
                                    } else {
                                        $row['columnHighLight']['Task Name'] = '';
                                        $row['columnHighLight']['Parent Task Name'] = '';
                                        $row['error'][] = __('The task name and parent task name are not blank!', true);
                                    }
                                } else {
                                    $row['columnHighLight']['Phase Name'] = '';
                                    $row['columnHighLight']['Part Name'] = '';
                                    $row['error'][] = __('The Phase name or Part name not found in project', true);
                                }
                            }
                            // Start Date And End Date
                            if(!empty($row['Start Date']) || !empty($row['End Date'])){
                                $_start = $_end = 0;
                                if(!empty($row['Start Date'])){
                                    $_start = $this->_formatDateCustom($row['Start Date']);
                                }
                                if(!empty($row['End Date'])){
                                    $_end = $this->_formatDateCustom($row['End Date']);
                                }
                                if($_start == 0){
                                    $_start = $this->_durationStartDate('', date('Y-m-d', $_end), 2);
                                    $_start = ($_start != '0000-00-00') ? strtotime($_start) : 0;
                                }
                                if($_end == 0){
                                    $_end = $this->_durationEndDate(date('Y-m-d', $_start), '', 2);
                                    $_end = ($_end != '0000-00-00') ? strtotime($_end) : 0;
                                }
                                //debug($row['Task Name'] . ' - ' . $row['Parent Task Name'] . ' - ' . date('d-m-Y', $_start));
                                if($_start > $_end){
                                    $row['columnHighLight']['End Date'] = '';
                                    $row['error'][] = __('The end date must be greater than start date.', true);
                                } else {
                                    $row['data']['task_start_date'] = $_start;
                                    $row['data']['task_end_date'] = $_end;
                                    $row['data']['duration'] = $this->_getWorkingDays(date('Y-m-d', $_start), date('Y-m-d', $_end), '');
                                }
                            } else {
                                $row['data']['task_start_date'] = 0;
                                $row['data']['task_end_date'] = 0;
                                $row['data']['duration'] = '';
                            }
                            // Workload
                            if($row['Workload'] == null){
                                $row['data']['estimated'] = 0;
                            } elseif($row['Workload'] == 0){
                                $row['data']['estimated'] = 0;
                            } elseif(is_numeric($row['Workload']) && $row['Workload'] > 0) {
                                $row['data']['estimated'] = $row['Workload'];
                            } else {
                                $row['data']['estimated'] = 0;
                            }
                            // Assign To
                            if(!empty($row['Assigned To'])){
                                $assigns = explode(',', $row['Assigned To']);
                                if(!empty($assigns)){
                                    $listAssigns = array();
                                    foreach($assigns as $assign){
                                        $assign = strtolower(trim($assign));
                                        if(in_array($assign, array_keys($employeeIdCompanies))){
                                            $listAssigns[] = $employeeIdCompanies[$assign];
                                        }
                                    }
                                    if(!empty($listAssigns)){
                                        $row['data']['assign'] = implode(', ', $listAssigns);
                                    }
                                }
                            }
                            // them project id va status id cho task duoc assign
                            $row['data']['project_id'] = $project_id;
                            $row['data']['task_status_id'] = $statusOpen;
                        }
                        if (!empty($row['error'])) {
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            $records['Create'][] = $row;
                        }
                    }
                }
                unlink($filename);
            }
            $this->set('records', $records);
            $this->set('default', $default);
            $this->set(compact('project_id', 'activity_id'));
        } else {
            $this->set(compact('project_id', 'activity_id'));
            $this->redirect(array('action' => 'index', $activity_id));
        }
    }
    /**
     * Save import of project task
     */
    function save_file_import($activity_id = null) {
        set_time_limit(0);
        $this->loadModel('Activity');
        $activities = $this->Activity->find('first', array(
            'recursive' => -1,
            'conditions' => array('Activity.id' => $activity_id),
            'fields' => array('project')
        ));
        if(!empty($activities) && !empty($activities['Activity']['project'])){
            $this->_saveFileImportProject($activities['Activity']['project'], $activity_id);
        } else {
            $this->_saveFileImportActivity($activity_id);
        }
    }
    /**
     * Save import activity
     */
    private function _saveFileImportActivity($activity_id = null) {
        set_time_limit(0);
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {//export
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    $this->redirect(array('action' => 'index', $activity_id));
                }
                $complete = 0;
                $totalRecordImport = count($import);
                foreach($import as $key => $data){
                    /**
                     * Luu cac task va parent task
                     */
                    if(empty($data['parent_id'])){
                        $this->_saveImportDataDetailActivity($data);
                        $complete++;
                        unset($import[$key]);
                    }
                }
                /**
                 * Luu cac Sub-Task
                 */
                if(!empty($import)){
                    foreach($import as $key => $data){
                        $this->_saveImportDataDetailActivity($data);
                        $complete++;
                        unset($import[$key]);
                    }
                }
                $this->ActivityTask->staffingSystem($activity_id);
                $this->Session->setFlash(sprintf(__('The task has been imported %s/%s.', true), $complete, $totalRecordImport));
                $this->redirect(array('action' => 'index', $activity_id));
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
                if (!empty($type)){
                    $_listEmployee = array();
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                        $header[] = __($key , true);
                    }
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
                } else {
                    $this->redirect(array('action' => 'index', $activity_id));
                }
            }
        } else {
            $this->redirect(array('action' => 'index', $activity_id));
        }
        exit;
    }
    /**
     * Save import project
     */
    private function _saveFileImportProject($project_id = null, $activity_id = null) {
        set_time_limit(0);
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {//export
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    $this->redirect(array('action' => 'index', $activity_id));
                }
                /**
                 * kiem tra xem project nay co link voi activity ko?
                 */
                $projects = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $project_id),
                    'fields' => array('activity_id')
                ));
                $complete = 0;
                $totalRecordImport = count($import);
                foreach($import as $key => $data){
                    /**
                     * Luu cac task va parent task
                     */
                    if(empty($data['parent_id'])){
                        $this->_saveImportDataDetail($data, $projects);
                        $complete++;
                        unset($import[$key]);
                    }
                }
                /**
                 * Luu cac Sub-Task
                 */
                if(!empty($import)){
                    foreach($import as $key => $data){
                        $this->_saveImportDataDetail($data, $projects);
                        $complete++;
                        unset($import[$key]);
                    }
                }
                $this->ProjectTask->staffingSystem($project_id);
                $this->Session->setFlash(sprintf(__('The task has been imported %s/%s.', true), $complete, $totalRecordImport));
                $this->redirect(array('action' => 'index', $activity_id));
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
                if (!empty($type)){
                    $_listEmployee = array();
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                        $header[] = __($key , true);
                    }
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
                } else {
                    $this->redirect(array('action' => 'index', $activity_id));
                }
            }
        } else {
            $this->redirect(array('action' => 'index', $activity_id));
        }
        exit;
    }

    /**
     * Dung de luu cac phan import task.
     * 1. Luu cac task duoc import vao activity task.
     * 2. Luu cac assign to cua task duoc import vao activity task employee refer.
     */
    private function _saveImportDataDetailActivity($data = null){
        set_time_limit(0);
        $parentId = 0;
        /**
         * Kiem tra xem co su ton tai cua parent id ko
         * Neu co thi lay thong tin cua parent id nay
         */
        $parentStart = $parentEnd = $parentDuration = 0;
        if(!empty($data['parent_id'])){
            $parentId = $this->ActivityTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $data['activity_id'],
                    'name' => $data['parent_id']
                ),
                'fields' => array('id', 'name', 'task_start_date', 'task_end_date')
            ));
            if(!empty($parentId)){
                $parentStart = !empty($parentId['ActivityTask']['task_start_date']) ? $parentId['ActivityTask']['task_start_date'] : 0;
                $parentEnd = !empty($parentId['ActivityTask']['task_end_date']) ? $parentId['ActivityTask']['task_end_date'] : 0;
                if($parentStart == 0){
                    $parentStart = $data['task_start_date'];
                } else {
                    if($data['task_start_date'] > 0 && $data['task_start_date'] < $parentStart){
                        $parentStart = $data['task_start_date'];
                    }
                }
                if($data['task_end_date'] > 0 && $data['task_end_date'] > $parentEnd){
                    $parentEnd = $data['task_end_date'];
                }
                $parentDuration = $this->_getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
                $parentId = $parentId['ActivityTask']['id'];
            } else {
                $saveParentTask = array(
                    'name' => $data['parent_id'],
                    'parent_id' => 0,
                    'activity_id' => $data['activity_id'],
                    'task_status_id' => $data['task_status_id'],
                    'task_start_date' => $data['task_start_date'] ? $data['task_start_date'] : '',
                    'task_end_date' => $data['task_end_date'] ? $data['task_end_date'] : '',
                    'estimated' => $data['estimated'],
                    'duration' => $data['duration']
                );
                $this->ActivityTask->create();
                if($this->ActivityTask->save($saveParentTask)){
                    $lastIdAc = $this->ActivityTask->getLastInsertID();
                    $parentId = $this->ActivityTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'ActivityTask.id' => $lastIdAc
                        ),
                        'fields' => array('id', 'name', 'task_start_date', 'task_end_date')
                    ));
                    if(!empty($parentId)){
                        $parentStart = !empty($parentId['ActivityTask']['task_start_date']) ? $parentId['ActivityTask']['task_start_date'] : 0;
                        $parentEnd = !empty($parentId['ActivityTask']['task_end_date']) ? $parentId['ActivityTask']['task_end_date'] : 0;
                        if($parentStart == 0){
                            $parentStart = $data['task_start_date'];
                        } else {
                            if($data['task_start_date'] > 0 && $data['task_start_date'] < $parentStart){
                                $parentStart = $data['task_start_date'];
                            }
                        }
                        if($data['task_end_date'] > 0 && $data['task_end_date'] > $parentEnd){
                            $parentEnd = $data['task_end_date'];
                        }
                        $parentDuration = $this->_getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
                        $parentId = $parentId['ActivityTask']['id'];
                    }
                }

            }
        }
        $saved = array(
            'name' => $data['name'],
            'parent_id' => $parentId,
            'activity_id' => $data['activity_id'],
            'task_status_id' => $data['task_status_id'],
            'task_start_date' => $data['task_start_date'] ? $data['task_start_date'] : '',
            'task_end_date' => $data['task_end_date'] ? $data['task_end_date'] : '',
            'estimated' => $data['estimated'],
            'duration' => $data['duration']
        );
        $assignedTos = !empty($data['assign']) ? explode(', ', $data['assign']) : array();
        /**
         * Luu cac task va parent task thuoc project task nay
         */
        $this->ActivityTask->create();
        if($this->ActivityTask->save($saved)){
            $lastId = $this->ActivityTask->getLastInsertID();
            /**
             * Luu cac assigned cua task
             */
            if(!empty($assignedTos)){
                $savedAssigns = array();
                foreach($assignedTos as $key => $employeeId){
                    $estimated = ($key == 0) ? $data['estimated'] : 0;
                    $savedAssigns[] = array(
                        'reference_id' => $employeeId,
                        'activity_task_id' => $lastId,
                        'estimated' => $estimated,
                        'is_profit_center' => 0
                    );
                }
                if(!empty($savedAssigns)){
                    $this->loadModel('ActivityTaskEmployeeRefer');
                    $this->ActivityTaskEmployeeRefer->saveAll($savedAssigns);
                    /**
                     * Kiem tra profit center cua tung employee nay co trong accessible par chua. Neu chua co thi luu vao accessible par
                     */
                    $this->loadModel('ProjectEmployeeProfitFunctionRefer');
                    $this->loadModel('ActivityProfitRefer');
                    foreach($savedAssigns as $savedAssign){
                        $profit = $this->ProjectEmployeeProfitFunctionRefer->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('employee_id' => $savedAssign['reference_id']),
                            'fields' => array('profit_center_id')
                        ));
                        $profit = !empty($profit) ? $profit['ProjectEmployeeProfitFunctionRefer']['profit_center_id'] : '';
                        $tmps = $this->ActivityProfitRefer->find('count', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'type' => 0,
                                'activity_id' => $data['activity_id'],
                                'profit_center_id' => $profit
                            )
                        ));
                        if($tmps == 0){
                            $saveAccessiblePar = array(
                                'type' => 0,
                                'activity_id' => $data['activity_id'],
                                'profit_center_id' => $profit
                            );
                            $this->ActivityProfitRefer->create();
                            $this->ActivityProfitRefer->save($saveAccessiblePar);
                        }
                    }
                }
            }
            /**
             * Neu co parent id, kiem tra va luu lai cac
             */
            if(!empty($parentId)){
                 $estimatedParents = $this->ActivityTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'parent_id' => $parentId
                    ),
                    'fields' => array('SUM(estimated) as workload')
                ));
                $estimatedParents = !empty($estimatedParents) ? array_shift(Set::classicExtract($estimatedParents, '{n}.0.workload')) : 0;
                $savedParent = array(
                    'task_start_date' => $parentStart,
                    'task_end_date' => $parentEnd,
                    'estimated' => $estimatedParents,
                    'duration' => $parentDuration
                );
                $this->ActivityTask->id = $parentId;
                $this->ActivityTask->save($savedParent);
            }
        }
    }

    /**
     * Dung de luu cac phan import task.
     * 1. Luu cac task duoc import vao project task.
     * 2. Luu cac assign to cua task duoc import vao project task employee refer.
     * 3. Neu co linked voi activity thi luu cac project task qua ben activity task.
     * 4. Luu employee/pc vao team neu trong project team cua project chua co employee/pc nay
     */
    private function _saveImportDataDetail($data = null, $projects = null){
        set_time_limit(0);
        $this->loadModel('ProjectTask');
        $parentId = 0;
        $parentTitle = '';
        /**
         * Kiem tra xem co su ton tai cua parent id ko
         * Neu co thi lay thong tin cua parent id nay
         */
        $parentStart = $parentEnd = $parentDuration = 0;
        if(!empty($data['parent_id'])){
            $parentId = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $data['project_id'],
                    'task_title' => $data['parent_id'],
                    'project_planed_phase_id' => $data['project_planed_phase_id']
                ),
                'fields' => array('id', 'task_title', 'task_start_date', 'task_end_date')
            ));
            if(!empty($parentId)){
                $parentStart = ($parentId['ProjectTask']['task_start_date'] != '0000-00-00') ? strtotime($parentId['ProjectTask']['task_start_date']) : 0;
                $parentEnd = ($parentId['ProjectTask']['task_end_date'] != '0000-00-00') ? strtotime($parentId['ProjectTask']['task_end_date']) : 0;
                $parentTitle = !empty($parentId['ProjectTask']['task_title']) ? $parentId['ProjectTask']['task_title'] : '';
                if($parentStart == 0){
                    $parentStart = $data['task_start_date'];
                } else {
                    if($data['task_start_date'] > 0 && $data['task_start_date'] < $parentStart){
                        $parentStart = $data['task_start_date'];
                    }
                }
                if($data['task_end_date'] > 0 && $data['task_end_date'] > $parentEnd){
                    $parentEnd = $data['task_end_date'];
                }
                $parentDuration = $this->_getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
                $parentId = $parentId['ProjectTask']['id'];
            } else {
                $saveParentTask = array(
                    'task_title' => $data['parent_id'],
                    'parent_id' => 0,
                    'project_id' => $data['project_id'],
                    'project_planed_phase_id' => $data['project_planed_phase_id'],
                    'task_status_id' => $data['task_status_id'],
                    'task_start_date' => $data['task_start_date'] ? date('Y-m-d', $data['task_start_date']) : '0000-00-00',
                    'task_end_date' => $data['task_end_date'] ? date('Y-m-d', $data['task_end_date']) : '0000-00-00',
                    'estimated' => $data['estimated'],
                    'duration' => $data['duration']
                );
                $this->ProjectTask->create();
                if($this->ProjectTask->save($saveParentTask)){
                    $lastIdPr = $this->ProjectTask->getLastInsertID();
                    /**
                     * Neu co linked thi tien hanh luu project task qua ben activity task
                     */
                    if(!empty($projects) && ($projects['Project']['activity_id'] != 0 || $projects['Project']['activity_id'] != '')){
                        $PhaseName = !empty($data['phase_name']) ? $data['phase_name'] : '';
                        $pTaskName = !empty($data['parent_id']) ? $data['parent_id'] : '';
                        $aTaskName = $PhaseName . '/' . $pTaskName;
                        $savedActivityTask = array(
                            'name' => $aTaskName,
                            'activity_id' => $projects['Project']['activity_id'],
                            'project_task_id' => $lastIdPr
                        );
                        $this->ActivityTask->create();
                        $this->ActivityTask->save($savedActivityTask);
                    }
                    $parentId = $this->ProjectTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'ProjectTask.id' => $lastIdPr
                        ),
                        'fields' => array('id', 'task_title', 'task_start_date', 'task_end_date')
                    ));
                    if(!empty($parentId)){
                        $parentStart = ($parentId['ProjectTask']['task_start_date'] != '0000-00-00') ? strtotime($parentId['ProjectTask']['task_start_date']) : 0;
                        $parentEnd = ($parentId['ProjectTask']['task_end_date'] != '0000-00-00') ? strtotime($parentId['ProjectTask']['task_end_date']) : 0;
                        $parentTitle = !empty($parentId['ProjectTask']['task_title']) ? $parentId['ProjectTask']['task_title'] : '';
                        if($parentStart == 0){
                            $parentStart = $data['task_start_date'];
                        } else {
                            if($data['task_start_date'] > 0 && $data['task_start_date'] < $parentStart){
                                $parentStart = $data['task_start_date'];
                            }
                        }
                        if($data['task_end_date'] > 0 && $data['task_end_date'] > $parentEnd){
                            $parentEnd = $data['task_end_date'];
                        }
                        $parentDuration = $this->_getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
                        $parentId = $parentId['ProjectTask']['id'];
                    }
                }

            }
        }
        $saved = array(
            'task_title' => $data['task_title'],
            'parent_id' => $parentId,
            'project_id' => $data['project_id'],
            'project_planed_phase_id' => $data['project_planed_phase_id'],
            'task_status_id' => $data['task_status_id'],
            'task_start_date' => $data['task_start_date'] ? date('Y-m-d', $data['task_start_date']) : '0000-00-00',
            'task_end_date' => $data['task_end_date'] ? date('Y-m-d', $data['task_end_date']) : '0000-00-00',
            'estimated' => $data['estimated'],
            'duration' => $data['duration']
        );
        $assignedTos = !empty($data['assign']) ? explode(', ', $data['assign']) : array();
        /**
         * Luu cac task va parent task thuoc project task nay
         */
        $this->ProjectTask->create();
        if($this->ProjectTask->save($saved)){
            $lastId = $this->ProjectTask->getLastInsertID();
            /**
             * Neu co linked thi tien hanh luu project task qua ben activity task
             */
            if(!empty($projects) && ($projects['Project']['activity_id'] != 0 || $projects['Project']['activity_id'] != '')){
                $parentActivityTask = 0;
                if(!empty($parentId)){
                    $parentActivityTask = $this->ActivityTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('project_task_id' => $parentId),
                        'fields' => array('id')
                    ));
                    $parentActivityTask = !empty($parentActivityTask) ? $parentActivityTask['ActivityTask']['id'] : 0;
                }

                $PhaseName = !empty($data['phase_name']) ? $data['phase_name'] : '';
                $pTaskName = !empty($data['task_title']) ? $data['task_title'] : '';
                if(!empty($parentTitle)){
                    $aTaskName = $PhaseName . '/' . $parentTitle . '/' . $pTaskName;
                } else {
                    $aTaskName = $PhaseName . '/' . $pTaskName;
                }
                $savedActivityTask = array(
                    'name' => $aTaskName,
                    'parent_id' => $parentActivityTask,
                    'activity_id' => $projects['Project']['activity_id'],
                    'project_task_id' => $lastId
                );
                $this->ActivityTask->create();
                if($this->ActivityTask->save($savedActivityTask)){
                    $lastActivityTaskId = $this->ActivityTask->getLastInsertID();
                }
            }
            /**
             * Luu cac assigned cua task
             */
            if(!empty($assignedTos)){
                $savedAssigns = array();
                foreach($assignedTos as $key => $employeeId){
                    $estimated = ($key == 0) ? $data['estimated'] : 0;
                    $savedAssigns[] = array(
                        'reference_id' => $employeeId,
                        'project_task_id' => $lastId,
                        'estimated' => $estimated,
                        'is_profit_center' => 0
                    );
                }
                if(!empty($savedAssigns)){
                    $this->loadModel('ProjectTaskEmployeeRefer');
                    $this->ProjectTaskEmployeeRefer->saveAll($savedAssigns);
                    /**
                     * Kiem tra employee nay co trong project team chua. Neu chua co thi luu vao team
                     */
                    $this->loadModel('ProjectFunctionEmployeeRefer');
                    $this->loadModel('ProjectTeam');
                    $this->loadModel('ProjectEmployeeProfitFunctionRefer');
                    $teams = $this->ProjectTeam->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('project_id' => $data['project_id']),
                        'fields' => array('id')
                    ));
                    foreach($savedAssigns as $savedAssign){
                        $tmps = $this->ProjectFunctionEmployeeRefer->find('count', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'employee_id' => $savedAssign['reference_id'],
                                'project_team_id' => $teams
                            )
                        ));
                        if($tmps == 0){
                            $profit = $this->ProjectEmployeeProfitFunctionRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => array('employee_id' => $savedAssign['reference_id']),
                                'fields' => array('profit_center_id')
                            ));
                            $profit = !empty($profit) ? $profit['ProjectEmployeeProfitFunctionRefer']['profit_center_id'] : '';
                            $this->ProjectTeam->create();
                            if($this->ProjectTeam->save(array('project_id' => $data['project_id']))){
                                $lastTeamId = $this->ProjectTeam->getLastInsertID();
                                $savedTeam = array(
                                    'employee_id' => $savedAssign['reference_id'],
                                    'profit_center_id' => $profit,
                                    'project_team_id' => $lastTeamId
                                );
                                $this->ProjectFunctionEmployeeRefer->create();
                                $this->ProjectFunctionEmployeeRefer->save($savedTeam);
                            }
                        }
                    }
                }
            }
            /**
             * Neu co parent id, kiem tra va luu lai cac
             */
            if(!empty($parentId)){
                 $estimatedParents = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'parent_id' => $parentId
                    ),
                    'fields' => array('SUM(estimated) as workload')
                ));
                $estimatedParents = !empty($estimatedParents) ? array_shift(Set::classicExtract($estimatedParents, '{n}.0.workload')) : 0;
                $savedParent = array(
                    'task_start_date' => date('Y-m-d', $parentStart),
                    'task_end_date' => date('Y-m-d', $parentEnd),
                    'estimated' => $estimatedParents,
                    'duration' => $parentDuration
                );
                $this->ProjectTask->id = $parentId;
                $this->ProjectTask->save($savedParent);
            }
            /**
             * Luu start date va end date cua phase.
             */
            if(!empty($data['project_planed_phase_id'])){
                $this->loadModel('ProjectPhasePlan');
                $projectPhases = $this->ProjectPhasePlan->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ProjectPhasePlan.id' => $data['project_planed_phase_id']
                    ),
                    'fields' => array('phase_real_start_date', 'phase_real_end_date')
                ));
                if(!empty($projectPhases)){
                    $start = strtotime($projectPhases['ProjectPhasePlan']['phase_real_start_date']);
                    $end = strtotime($projectPhases['ProjectPhasePlan']['phase_real_end_date']);
                    if($data['task_start_date'] > 0 && $data['task_start_date'] < $start){
                        $start = $data['task_start_date'];
                    }
                    if($data['task_end_date'] > 0 && $data['task_end_date'] > $end){
                        $end = $data['task_end_date'];
                    }
                    $savePhased = array(
                        'phase_real_start_date' => date('Y-m-d', $start),
                        'phase_real_end_date' => date('Y-m-d', $end),
                        'planed_duration' => $this->_getWorkingDays(date('Y-m-d', $start), date('Y-m-d', $end), 0)
                    );
                    $this->ProjectPhasePlan->id = $data['project_planed_phase_id'];
                    $this->ProjectPhasePlan->save($savePhased);
                }
            }
        }
    }

    private function _mix_coloumn($input){
        $result = array();
        foreach($input as $value){
            $result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    private function _utf8_encode_mix($input){
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }

    /**
     * Ham dung de nhan dang va dinh dang lai date time
     */
    private function _formatDateCustom($date = null){
        $date = str_replace('/', '-', $date);
        $date = preg_replace('/[^\d-]/i', '', $date);
        $date = explode('-', $date);
        $currentDate = date('Y', time());
        $century = substr($currentDate, 0, 2);
        $day = $month = $year = 0;
        $day = !empty($date[0]) ? preg_replace('/\D/i', '', $date[0]) : '00';
        $month = !empty($date[1]) ? preg_replace('/\D/i', '', $date[1]) : '00';
        $year = !empty($date[2]) ? preg_replace('/\D/i', '', $date[2]) : '0000';
        $result = $year . '-' . $month . '-' . $day;
        $result = strtotime($result);
        return $result;
    }

    /**
     * Import task CSV from Activity Management
     */
    public function import_csv_task() {
        set_time_limit(0);
        //$this->autoRender = false;
        $employeeName = $this->_getEmployee();
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileFieldTask']['name']['csv_file_attachment'])) {
            $_FILES['FileField'] = $_FILES['FileFieldTask'];
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Tasks' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Tasks' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        //'Update' => array(),
                        'Error' => array()
                    );
                    $columnMandatory = array(
                        'Import Code',
                        'Task Name',
                        'Parent Task Name',
                        'Assigned To',
                        'Start Date',
                        'End Date',
                        'Workload'
                    );
                    $default = array();
                    if(!empty($csv->titles)){
                        foreach($csv->titles as $headName){
                            if(in_array($headName, $columnMandatory)){
                                $default[$headName] = '';
                            }
                        }
                    }
                    $this->loadModel('Employee');
                    $this->loadModel('ProjectStatus');
                    $this->loadModel('Activity');
                    $this->Employee->cacheQueries = true;

                    $validate = array('Import Code', 'Task Name');
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    // employee of company
                    $employeeCompanies = $this->Employee->CompanyEmployeeReference->find('all', array(
                        'conditions' => array(
                            'CompanyEmployeeReference.company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
                    ));
                    $employeeCompanies = !empty($employeeCompanies) ? Set::combine($employeeCompanies, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
                    $employeeIdCompanies = !empty($employeeCompanies) ? array_flip($employeeCompanies) : array();
                    $employeeIdCompanies = !empty($employeeIdCompanies) ? array_change_key_case($employeeIdCompanies, CASE_LOWER) : array();
                    // status of company
                    $statusOfCompanies = $this->ProjectStatus->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'name' => array('Ouvert', 'Open'),
                            'company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('id')
                    ));
                    // mac dinh cac task import vao co status la OPEN
                    $statusOpen = !empty($statusOfCompanies) ? $statusOfCompanies['ProjectStatus']['id'] : '';
                    $listParentTaskImports = $listSubTaskHaveImports = $listEndChildrens = array();
                    foreach ($csv->data as $row) {
                        if(isset($row['#']) || isset($row['No.'])){
                            unset($row['#']);
                            unset($row['No.']);
                        }
                        foreach(array_keys($row) as $name){
                            if(!in_array($name, $columnMandatory)){
                                unset($row[$name]);
                            }
                        }
                        $error = false;
                        $row = array_merge(array_combine($defaultKeys, array_slice(array_map('trim', array_values($row))
                                                + array_fill(0, $count, ''), 0, $count)), array(
                            'data' => array(),
                            'error' => array()));

                        foreach ($validate as $key => $value) {
                            $row[$value] = trim($row[$value]);
                            if (empty($row[$value])) {
                                $row['columnHighLight'][$value] = '';
                                $row['error'][] = sprintf(__('The %s is not blank', true), $value);
                            }
                        }
                        if(empty($row['error'])) {
                            // Import Code: kiem tra cac activity co ton tai hay ko? Kiem tra cac activity co linked project hay ko?
                            $activities = $this->Activity->find('first', array(
                                'recursive' => -1,
                                'conditions' => array('import_code' => trim($row['Import Code'])),
                                'fields' => array('id', 'project')
                            ));
                            $activity_id = 0;
                            if(!empty($activities)){
                                if($activities['Activity']['project'] == '' || $activities['Activity']['project'] == 0){
                                    $activity_id = $activities['Activity']['id'];
                                    $row['data']['activity_id'] = $activity_id;
                                } else {
                                    $row['columnHighLight']['Import Code'] = '';
                                    $row['error'][] = __('The Activity have linked with project.', true);
                                }
                            } else {
                                $row['columnHighLight']['Import Code'] = '';
                                $row['error'][] = __('The Import Code not exist!', true);
                            }
                            if($activity_id != 0){
                                //Task Name
                                if(!empty($row['Task Name'])) {
                                    $parentTasks = null;
                                    if(!empty($row['Parent Task Name'])){ // sub task
                                        if(!empty($listSubTaskHaveImports[$activity_id][trim($row['Parent Task Name'])][trim($row['Task Name'])])){
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('The sub task name has identical', true);
                                        } else {
                                            if(!empty($listEndChildrens[$activity_id][trim($row['Parent Task Name'])])){ // HuuPC
                                                $row['columnHighLight']['Parent Task Name'] = '';
                                                $row['columnHighLight']['Task Name'] = '';
                                                $row['error'][] = __('Don\'t allow create sub - sub task.', true);
                                            } else {
                                                $parentTasks = $this->ActivityTask->find('first', array(
                                                    'recursive' => -1,
                                                    'conditions' => array(
                                                        'activity_id' => $activity_id,
                                                        'name' => trim($row['Parent Task Name'])
                                                    ),
                                                    'fields' => array('id', 'parent_id')
                                                ));
                                                $_parentIds = !empty($parentTasks['ActivityTask']['parent_id']) ? $parentTasks['ActivityTask']['parent_id'] : 0;
                                                if(!empty($parentTasks) && $_parentIds != 0){
                                                    $row['columnHighLight']['Parent Task Name'] = '';
                                                    $row['columnHighLight']['Task Name'] = '';
                                                    $row['error'][] = __('Don\'t allow create sub - sub task.', true);
                                                } else {
                                                     if(!empty($parentTasks) && $parentTasks['ActivityTask']['id']){ // da co parent task trong du lieu
                                                        $haveConsumed = 0;
                                                        $this->loadModel('ActivityRequest');
                                                        $haveConsumed = $this->ActivityRequest->find('count', array(
                                                            'recursive' => -1,
                                                            'conditions' => array(
                                                                'task_id' => $parentTasks['ActivityTask']['id'],
                                                                'status' => 2
                                                            )
                                                        ));
                                                        if($haveConsumed == 0){
                                                            $row['data']['parent_id'] = $row['Parent Task Name'];
                                                            /**
                                                             * Kiem tra xem task import da ton tai trong parent nay chua
                                                             */
                                                            $checkTaskImports = $this->ActivityTask->find('first', array(
                                                                'recursive' => -1,
                                                                'conditions' => array(
                                                                    'activity_id' => $activity_id,
                                                                    'name' => trim($row['Task Name']),
                                                                    'parent_id' => $parentTasks['ActivityTask']['id']
                                                                ),
                                                                'fields' => array('id')
                                                            ));
                                                            if(!empty($checkTaskImports) && $checkTaskImports['ActivityTask']['id']){
                                                                $row['columnHighLight']['Task Name'] = '';
                                                                $row['error'][] = __('The sub task name has exists', true);
                                                            } else {
                                                                $row['data']['name'] = $row['Task Name'];
                                                                $listSubTaskHaveImports[$activity_id][trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                            }
                                                        } else {
                                                            $row['columnHighLight']['Parent Task Name'] = '';
                                                            $row['error'][] = __('The parent task has consumed', true);
                                                        }
                                                     } else { // chua co parent task trong database
                                                        $row['data']['parent_id'] = $row['Parent Task Name'];
                                                        $row['data']['name'] = $row['Task Name'];
                                                        $listSubTaskHaveImports[$activity_id][trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                        $listEndChildrens[$activity_id][trim($row['Task Name'])] = trim($row['Task Name']);
                                                    }
                                                }
                                            }
                                        }
                                    } else { // task
                                        if(!empty($listParentTaskImports[$activity_id][trim($row['Task Name'])])){
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('The task name has identical', true);
                                        } else {
                                            $checkTasks = $this->ActivityTask->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array(
                                                    'activity_id' => $activity_id,
                                                    'name' => trim($row['Task Name'])
                                                ),
                                                'fields' => array('id')
                                            ));
                                            if(!empty($checkTasks) && $checkTasks['ActivityTask']['id']){
                                                $row['columnHighLight']['Task Name'] = '';
                                                $row['error'][] = __('The task name has exists', true);
                                            } else {
                                                $listParentTaskImports[$activity_id][trim($row['Task Name'])] = trim($row['Task Name']);
                                                $row['data']['name'] = $row['Task Name'];
                                            }
                                        }
                                    }
                                } else { // khong co task name, nhung co parent task
                                    if(!empty($row['Parent Task Name'])){
                                        if(!empty($listParentTaskImports[$activity_id][trim($row['Parent Task Name'])])){
                                            $row['columnHighLight']['Parent Task Name'] = '';
                                            $row['error'][] = __('The parent task name has identical', true);
                                        } else {
                                            $checkTasks = $this->ActivityTask->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array(
                                                    'activity_id' => $activity_id,
                                                    'name' => trim($row['Parent Task Name'])
                                                ),
                                                'fields' => array('id')
                                            ));
                                            if(!empty($checkTasks) && $checkTasks['ActivityTask']['id']){
                                                $row['columnHighLight']['Parent Task Name'] = '';
                                                $row['error'][] = __('The task name has exists', true);
                                            } else {
                                                $listParentTaskImports[$activity_id][trim($row['Parent Task Name'])] = trim($row['Parent Task Name']);
                                                $row['data']['name'] = $row['Parent Task Name'];
                                            }
                                        }
                                    } else {
                                        $row['columnHighLight']['Task Name'] = '';
                                        $row['columnHighLight']['Parent Task Name'] = '';
                                        $row['error'][] = __('The task name and parent task name are not blank!', true);
                                    }
                                }
                                // Start Date And End Date
                                if(!empty($row['Start Date']) || !empty($row['End Date'])){
                                    $_start = $_end = 0;
                                    if(!empty($row['Start Date'])){
                                        $_start = $this->_formatDateCustom($row['Start Date']);
                                    }
                                    if(!empty($row['End Date'])){
                                        $_end = $this->_formatDateCustom($row['End Date']);
                                    }
                                    if($_start == 0){
                                        $_start = $this->_durationStartDate('', date('Y-m-d', $_end), 2);
                                        $_start = ($_start != '0000-00-00') ? strtotime($_start) : 0;
                                    }
                                    if($_end == 0){
                                        $_end = $this->_durationEndDate(date('Y-m-d', $_start), '', 2);
                                        $_end = ($_end != '0000-00-00') ? strtotime($_end) : 0;
                                    }
                                    //debug($row['Task Name'] . ' - ' . $row['Parent Task Name'] . ' - ' . date('d-m-Y', $_start));
                                    if($_start > $_end){
                                        $row['columnHighLight']['End Date'] = '';
                                        $row['error'][] = __('The end date must be greater than start date.', true);
                                    } else {
                                        $row['data']['task_start_date'] = $_start;
                                        $row['data']['task_end_date'] = $_end;
                                        $row['data']['duration'] = $this->_getWorkingDays(date('Y-m-d', $_start), date('Y-m-d', $_end), '');
                                    }
                                } else {
                                    $row['data']['task_start_date'] = 0;
                                    $row['data']['task_end_date'] = 0;
                                    $row['data']['duration'] = '';
                                }
                                // Workload
                                if($row['Workload'] == null){
                                    $row['data']['estimated'] = 0;
                                } elseif($row['Workload'] == 0){
                                    $row['data']['estimated'] = 0;
                                } elseif(is_numeric($row['Workload']) && $row['Workload'] > 0) {
                                    $row['data']['estimated'] = $row['Workload'];
                                } else {
                                    $row['data']['estimated'] = 0;
                                }
                                // Assign To
                                if(!empty($row['Assigned To'])){
                                    $assigns = explode(',', $row['Assigned To']);
                                    if(!empty($assigns)){
                                        $listAssigns = array();
                                        foreach($assigns as $assign){
                                            $assign = strtolower(trim($assign));
                                            if(in_array($assign, array_keys($employeeIdCompanies))){
                                                $listAssigns[] = $employeeIdCompanies[$assign];
                                            }
                                        }
                                        if(!empty($listAssigns)){
                                            $row['data']['assign'] = implode(', ', $listAssigns);
                                        }
                                    }
                                }
                                // them status id cho task duoc assign
                                $row['data']['task_status_id'] = $statusOpen;
                            }
                        }
                        if (!empty($row['error'])) {
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            $records['Create'][] = $row;
                        }
                    }
                }
                unlink($filename);
            }
            $this->set('records', $records);
            $this->set('default', $default);
        } else {
            $this->redirect(array('controller' => 'activities', 'action' => 'index'));
        }
    }

    /**
     * Save import task, nut button nam trong activity management
     */
    public function save_file_import_task() {
        set_time_limit(0);
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {//export
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    $this->redirect(array('controller' => 'activities', 'action' => 'index'));
                }
                $complete = 0;
                $totalRecordImport = count($import);
                $listActivities = array();
                foreach($import as $key => $data){
                    /**
                     * Luu cac task va parent task
                     */
                    if(empty($data['parent_id'])){
                        $this->_saveImportDataDetailActivity($data);
                        $complete++;
                        $listActivities[$data['activity_id']] = $data['activity_id'];
                        unset($import[$key]);
                    }
                }
                /**
                 * Luu cac Sub-Task
                 */
                if(!empty($import)){
                    foreach($import as $key => $data){
                        $this->_saveImportDataDetailActivity($data);
                        $complete++;
                        $listActivities[$data['activity_id']] = $data['activity_id'];
                        unset($import[$key]);
                    }
                }
                /**
                 * Luu cac activity vao tmp staffing
                 */
                if(!empty($listActivities)){
                    foreach($listActivities as $activityId){
                        $this->ActivityTask->staffingSystem($activityId);
                    }
                }
                $this->Session->setFlash(sprintf(__('The task has been imported %s/%s.', true), $complete, $totalRecordImport));
                $this->redirect(array('controller' => 'activities', 'action' => 'index'));
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
                if (!empty($type)){
                    $_listEmployee = array();
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                        $header[] = __($key , true);
                    }
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
                } else {
                    $this->redirect(array('controller' => 'activities', 'action' => 'index'));
                }
            }
        } else {
            $this->redirect(array('controller' => 'activities', 'action' => 'index'));
        }
        exit;
    }
	function checkNumSum($st=null,$pc=null,$em=null){
        if(($st == 0)&&($pc == '')&&($em == '')){
            $_row = 2;
        }
        if(($st == 0)&&($pc !='')&&($em == '')){
            $_row = 4;
        }
        if(($st == 0)&&($em != '')){
            $_row = 3;
        }
        if(($st == 5)&&($pc == '')&&($em == '')){
            $_row = 8;
        }
        if(($st == 5)&&($pc != '')&&($em == '')){
            $_row = 4;
        }
        if(($st == 5)&&($em != '')){
            $_row = 3;
        }
        if($st == 1){
            $_row = 4;
        }
        return $_row;
    }
    function freeze($activity_id = null){
        if ($activity_id) {
            $this->loadModel('Activity');
            $this->loadModel('Project');
            $this->loadModel('ProjectTask');
            $this->Activity->read(null,$activity_id);
            $this->Activity->set(array('is_freeze' =>1,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
            $this->Activity->save();
            // dong bang
            $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.activity_id' => $activity_id)
                ));
            $activityLinked = !empty($projectName['Project']['id']) ? $projectName['Project']['id'] : 0;
            if($activityLinked){
                $this->Project->read(null,$projectName['Project']['id']);
                $this->Project->set(array('is_freeze' =>1,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
                $this->Project->save();
            }
            if($activityLinked){
                $ptasks = $this->ProjectTask->find('all',array('recursive'=>-1,'conditions'=>array(
                     'project_id'=>$projectName['Project']['id'])
                ));
                $_dataUpdate = array();
                foreach($ptasks as $ptask){
                    $this->ProjectTask->id = $ptask['ProjectTask']['id'];
                    $_dataUpdate['initial_estimated'] = $ptask['ProjectTask']['estimated'];
                    $_dataUpdate['initial_task_start_date'] = $ptask['ProjectTask']['task_start_date'];
                    $_dataUpdate['initial_task_end_date'] = $ptask['ProjectTask']['task_end_date'];
                    $this->ProjectTask->save($_dataUpdate);
                }
            }else{
                $atasks = $this->ActivityTask->find('all',array('recursive'=>-1,'conditions'=>array(
                    'activity_id'=>$activity_id)
                    ));
                $_dataUpdateProject = array();
                foreach($atasks as $atask){
                    $this->ActivityTask->id =  $atask['ActivityTask']['id'];
                    $_dataUpdateProject['initial_estimated'] = $atask['ActivityTask']['estimated'];
                    $_dataUpdateProject['initial_task_start_date'] = date('Y-m-d',$atask['ActivityTask']['task_start_date']);
                    $_dataUpdateProject['initial_task_end_date'] = date('Y-m-d',$atask['ActivityTask']['task_end_date']);
                    $this->ActivityTask->save($_dataUpdateProject);
                }
            }
            $this->Session->setFlash(__('Saved', true), 'success');
            $this->redirect(array('action' => 'index',$activity_id));
        }else{
            $this->Session->setFlash(__('Not saved', true), 'error');
            $this->redirect(array('action' => 'index',$activity_id));
        }
    }
    function unfreeze($activity_id = null){
        if ($activity_id) {
            $this->loadModel('Activity');
            $this->loadModel('Project');
            $this->loadModel('ProjectTask');
            $this->Activity->read(null,$activity_id);
            $this->Activity->set(array('is_freeze' =>0,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
            $this->Activity->save();
             // mo dong bang
            $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.activity_id' => $activity_id)
                ));
            $activityLinked = !empty($projectName['Project']['id']) ? $projectName['Project']['id'] : 0;
            if($activityLinked){
                $this->Project->read(null,$projectName['Project']['id']);
                $this->Project->set(array('is_freeze' =>0,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
                $this->Project->save();
            }
            if($activityLinked){
                $ptasks = $this->ProjectTask->find('all',array('recursive'=>-1,'conditions'=>array(
                     'project_id'=>$projectName['Project']['id'])
                ));
                $_dataUpdate = array();
                foreach($ptasks as $ptask){
                    $this->ProjectTask->id = $ptask['ProjectTask']['id'];
                    $_dataUpdate['initial_estimated'] = 0;
                    $_dataUpdate['initial_task_start_date'] = null;
                    $_dataUpdate['initial_task_end_date'] = null;
                    $this->ProjectTask->save($_dataUpdate);
                }
            }else{
                $atasks = $this->ActivityTask->find('all',array('recursive'=>-1,'conditions'=>array(
                    'activity_id'=>$activity_id)
                    ));
                $_dataUpdateProject = array();
                foreach($atasks as $atask){
                    $this->ActivityTask->id =  $atask['ActivityTask']['id'];
                    $_dataUpdateProject['initial_estimated'] = 0;
                    $_dataUpdateProject['initial_task_start_date'] = '0000-00-00';
                    $_dataUpdateProject['initial_task_end_date'] = '0000-00-00';
                    $this->ActivityTask->save($_dataUpdateProject);
                }
            }
            $this->Session->setFlash(__('Saved', true), 'success');
            $this->redirect(array('action' => 'index',$activity_id));
        }else{
            $this->Session->setFlash(__('Not saved', true), 'error');
            $this->redirect(array('action' => 'index',$activity_id));
        }
    }
    function update_initial($activity_id = null, $check = null){
        $this->loadModel('Activity');
        $this->Activity->id = $activity_id;
        $_dataAL['off_freeze'] = $check;
        $this->Activity->save($_dataAL);
        $this->Session->setFlash(__('Saved', true), 'success');
        $this->redirect(array('action' => 'index',$activity_id));
    }

    function updateTaskStatus(){
        ini_set('max_execution_time', 120);
        $this->loadModel('Company');
        $this->loadModel('ProjectTask');
        $defaultStatuses = $this->Company->find('list', array(
            'recursive' => -1,
            'fields' => array('Company.id', 'ProjectStatus.id'),
            'conditions' => array(
                'ProjectStatus.name IS NOT NULL',
                'TRIM(`ProjectStatus.name`) !=' => ''
            ),
            'joins' => array(
                array(
                    'table' => 'project_statuses',
                    'alias' => 'ProjectStatus',
                    'type' => 'inner',
                    'conditions' => array(
                        'Company.id = ProjectStatus.company_id',
                        //'ProjectStatus.name' => array('open', 'ouvert')
                    )
                )
            ),
            'group' => 'Company.id'
        ));
        //pr($defaultStatuses);
        $cids = array_keys($defaultStatuses);
        $tasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                //only load tasks whoes company has default status
                'Activity.company_id' => $cids,
                'OR' => array(
                    'ActivityTask.task_status_id IS NULL',
                    'ActivityTask.task_status_id' => 0
                )
            ),
            'fields' => 'ActivityTask.id, ActivityTask.project_task_id, ProjectTask.task_status_id, ActivityTask.task_status_id, Activity.company_id',
            'joins' => array(
                array(
                    'table' => 'project_tasks',
                    'alias' => 'ProjectTask',
                    'type' => 'left',
                    'conditions' => array('ProjectTask.id = ActivityTask.project_task_id')
                ),
                array(
                    'table' => 'activities',
                    'alias' => 'Activity',
                    'type' => 'right',
                    'conditions' => array('Activity.id = ActivityTask.activity_id')
                )
            )
        ));
        foreach($tasks as $task){
            $status = $task['ProjectTask']['task_status_id'];
            if( !$status )
                $status = $defaultStatuses[ $task['Activity']['company_id'] ];
            if( $task['ActivityTask']['id'] ){
                $this->ActivityTask->id = $task['ActivityTask']['id'];
                $this->ActivityTask->save(array(
                    'task_status_id' => $status
                ));
            }
            if( !$task['ProjectTask']['task_status_id'] && $task['ActivityTask']['project_task_id'] ){
                $this->ProjectTask->id = $task['ActivityTask']['project_task_id'];
                $this->ProjectTask->save(array(
                    'task_status_id' => $status
                ));
            }
        }
        //update project tasks
        $tasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                //only load tasks whoes company has default status
                'Project.company_id' => $cids,
                'OR' => array(
                    'ProjectTask.task_status_id IS NULL',
                    'ProjectTask.task_status_id' => 0
                )
            ),
            'fields' => 'ProjectTask.id, ProjectTask.task_status_id, Project.company_id',
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'type' => 'right',
                    'conditions' => array('Project.id = ProjectTask.project_id')
                )
            )
        ));
        foreach($tasks as $task){
            $status = $defaultStatuses[ $task['Project']['company_id'] ];
            $this->ProjectTask->id = $task['ProjectTask']['id'];
            $this->ProjectTask->save(array(
                'task_status_id' => $status
            ));
        }


        die('ok');
    }

    //added by QN
    public function getNcTask(){
        App::import("vendor", "str_utility");
        $str = new str_utility();
        $result = array('result' => false, 'task' => array(), 'columns' => array(), 'data' => array(), 'consumeResult' => array());
        if( isset($this->data['id']) ){
            $aid = $this->data['id'];
            $aTask = $this->ActivityTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $aid,
                    'is_nct' => 1
                )
            ));
            if( !empty($aTask) ){
                $result['task'] = $aTask['ActivityTask'];
                $result['task']['task_start_date'] = date('d-m-Y', $result['task']['task_start_date']);
                $result['task']['task_end_date'] = date('d-m-Y', $result['task']['task_end_date']);
                $result['task']['task_title'] = $result['task']['name'];
                $this->loadModel('NctWorkload');
                $this->NctWorkload->virtualFields['temp_id'] = 'CONCAT(reference_id, "-", is_profit_center)';
                $this->NctWorkload->virtualFields['workload'] = 'estimated';
                $this->NctWorkload->virtualFields['group_key'] = 'CASE WHEN group_date IS NULL OR group_date = "" THEN CONCAT("0_", task_date) ELSE group_date END';
                $finds = array(
                    'recursive' => -1,
                    'conditions' => array(
                        'activity_task_id' => $aid
                    ),
                    'fields' => array('temp_id', 'group_date', 'group_key', 'id', 'reference_id', 'estimated', 'task_date', 'is_profit_center', 'project_task_id', 'activity_task_id'),
                    'order' => array('task_date')
                );
                if( $aTask['ActivityTask']['date_type'] != 0 ){
                    // $finds['group'] = array('group_date', 'temp_id');
                    $finds['fields'][] = 'workload';
                }
                $assigns = $this->NctWorkload->find('all', $finds);

                if( !empty($assigns) ){
                    $this->loadModel('Employee');
                    $this->loadModel('ProfitCenter');
                    $this->Employee->virtualFields['fullname'] = 'CONCAT(first_name, " ", last_name)';
                    $listResources = $this->Employee->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $this->employee_info['Company']['id']
                        ),
                        'fields' => array('id', 'fullname')
                    ));

                    $listPcs = $this->ProfitCenter->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $this->employee_info['Company']['id']
                        ),
                        'fields' => array('id', 'name')
                    ));

                    $list = Set::classicExtract($assigns, '{n}.NctWorkload.temp_id'); //this will be columns
                    if( !empty($list) )$list = array_unique($list);
                    foreach($list as $id){
                        $x = explode('-', $id);
                        if( $x[1] == 0 )$name = $listResources[$x[0]];
                        else $name = 'PC / ' . $listPcs[ $x[0] ];
                        $result['columns'][] = array('id' => $id, 'name' => $name);
                        $result['consumeResult'][$id] = false;
                    }

                    //build
                    $final = Set::combine($assigns, '{n}.NctWorkload.temp_id', '{n}.NctWorkload', '{n}.NctWorkload.group_key');
                    if( !empty($final) ){
                        foreach($final as $date => $fs){
                            $type = substr($date, 0, 1);
                            $dd = explode('_', $date);
                            if( $type != 0 ){
                                $startDate = $dd[1];
                                $endDate = $dd[2];
                            } else {
                                $startDate = $endDate = $str->convertToVNDate($dd[1]);
                            }
                            if( isset($aTask['ActivityTask']) ){
                                $result['request'][$date] = $this->getRequest2($aTask['ActivityTask']['id'], 'activity', $startDate, $endDate);
                            } else {
                                $result['request'][$date] = array(0, 0);
                            }
                            foreach($list as $id){
                                $x = explode('-', $id);
                                // if( $x[1] == 0 )$name = $listResources[$x[0]];
                                // else $name = 'PC / ' . $listPcs[ $x[0] ];
                                $array = array(
                                    'id' => 0,
                                    'reference_id' => $x[0],
                                    'estimated' => 0,
                                    'consumed' => 0,
                                    'is_profit_center' => $x[1],
                                    'inUsed' => 0,
                                    'type' => $type
                                );
                                if( isset($fs[$id]) ){
                                    //get consumed
                                    if( isset($aTask['ActivityTask']) )list($consumed, $inUsed) = $this->getRequest($aTask['ActivityTask']['id'], 'activity', $x[0], $x[1], $startDate, $endDate);
                                    else {
                                        $consumed = 0; $inUsed = 0;
                                    }
                                    $array['id'] = $fs[$id]['id'];
                                    $array['consumed'] = $consumed;
                                    $array['inUsed'] = $inUsed;
                                    if( $consumed || $inUsed ){
                                        $result['consumeResult'][$id] = true;
                                    }
                                    $array['estimated'] = isset($fs[$id]['workload']) ? floatVal($fs[$id]['workload']) : floatval($fs[$id]['estimated']);
                                }
                                $result['data'][$date][] = $array;
                            }
                        }
                    }
                }
                if( isset($aTask['ActivityTask']) ){
                    $result['request']['all'] = $this->getAllRequest($aTask['ActivityTask']['id']);
                } else {
                    $result['request']['all'] = array(0, 0);
                }
                $result['result'] = true;
            }
        }
        die(json_encode($result));
    }
    public function getAllRequest($task_id){
        $this->loadModel('ActivityRequest');
        $result = array(0, 0);
        $r = $this->ActivityRequest->find('first', array(
            'recursive' => -1,
            'fields' => array(
                'SUM(IF(`status` = 2, `value`, 0)) as consume',
                'SUM(IF(`status` != 2, `value`, 0)) as inUsed',
            ),
            'conditions' => array(
                'task_id' => $task_id
            )
        ));
        if( !empty($r) ){
            $result[0] = $r[0]['consume'];
            $result[1] = $r[0]['inUsed'];
        }
        return $result;
    }
    public function getRequest2($task_id, $f = 'project', $startDate, $endDate = ''){
        $p = explode('-', $startDate);
        $start = mktime(0,0,0,$p[1], $p[0], $p[2]);
        if( !$endDate )$endDate = $start;
        else {
            $p = explode('-', $endDate);
            $end = mktime(0,0,0,$p[1], $p[0], $p[2]);
        }
        $result = array(0, 0);
        $this->loadModel('ActivityTask');
        if( $f == 'project' ){
            $aTask = $this->ActivityTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $task_id,
                    //'is_nct' => 1
                )
            ));
            if( !empty($aTask) )$task_id = $aTask['ActivityTask']['id'];
        }
        $this->loadModel('ActivityRequest');
        $r = $this->ActivityRequest->find('first', array(
            'recursive' => -1,
            'fields' => array(
                'SUM(IF(`status` = 2, `value`, 0)) as consume',
                'SUM(IF(`status` != 2, `value`, 0)) as inUsed',
            ),
            'conditions' => array(
                'task_id' => $task_id,
                'date BETWEEN ? AND ?' => array($start, $end)
            )
        ));
        if( !empty($r) ){
            $result[0] = $r[0]['consume'];
            $result[1] = $r[0]['inUsed'];
        }
        return $result;
    }
    public function getRequest($task_id, $f = 'project', $reference_id, $type = 0, $startDate, $endDate = ''){
        $p = explode('-', $startDate);
        $start = mktime(0,0,0,$p[1], $p[0], $p[2]);
        if( !$endDate )$endDate = $start;
        else {
            $p = explode('-', $endDate);
            $end = mktime(0,0,0,$p[1], $p[0], $p[2]);
        }
        $result = array(0, 0);
        $this->loadModel('ActivityRequest');
        if( $type == 0 ){
            $requests = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => $task_id,
                    'employee_id' => $reference_id,
                    'date BETWEEN ? AND ?' => array($start, $end)
                )
            ));
        } else {
            //$this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $this->loadModel('ProfitCenter');
            $tmp = $this->ProfitCenter->children($reference_id);
            $pcs = array($reference_id);
            if( !empty($tmp) ){
                $tmp = Set::classicExtract($tmp, '{n}.ProfitCenter.id');
                if( !empty($tmp) )$pcs = array_merge($pcs, $tmp);
            }
            $listEmployees = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'profit_center_id' => $pcs
                ),
                'fields' => array('id', 'id')
            ));
            $requests = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => $task_id,
                    'employee_id' => $listEmployees,
                    'date BETWEEN ? AND ?' => array($start, $end)
                )
            ));
        }
        if( !empty($requests) ){
            foreach($requests as $req){
                if( $req['ActivityRequest']['status'] == 2 )$result[0] += $req['ActivityRequest']['value'];
                else $result[1] += $req['ActivityRequest']['value'];
            }
        }
        if( isset($this->data['ajax']) )
            die(json_encode($result));
        return $result;
    }
    public function taskHasConsumed($task_id){
        $this->loadModel('ActivityRequest');
        return $this->ActivityRequest->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $task_id
            )
        ));
    }
    private function toInt($vnDate){
        $date = explode('-', $vnDate);
        return mktime(0, 0, 0, $date[1], $date[0], $date[2]);
    }
    public function saveNcTask(){
        $result = array('result' => false, 'data' => array());
        if( !empty($this->data) ){
            $pid = $this->data['activity_id'];
            $task = $this->data['task'];
            $task['is_nct'] = 1;
            $task['name'] = $task['task_title'];
            $task['parent_id'] = 0;
            $dateType = $task['date_type'] = $this->data['type'];
            $workloads = isset($this->data['workloads']) ? $this->data['workloads'] : array();
            $cid = $this->employee_info['Company']['id'];
            $activity = $this->ActivityTask->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $cid,
                    'id' => $pid
                )
            ));
            if( !empty($activity) ){
                App::import("vendor", "str_utility");
                $str = new str_utility();
                $task['duration'] = $this->_getWorkingDays($str->convertToSQLDate($task['task_start_date']), $str->convertToSQLDate($task['task_end_date']), '');
                //convert to int
                $task['task_start_date'] = $this->toInt($task['task_start_date']);
                $task['task_end_date'] = $this->toInt($task['task_end_date']);
                $this->loadModel('ActivityTaskEmployeeRefer');
                $this->loadModel('NctWorkload');
                if( $task['id'] ){
                    $_task = $this->ActivityTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $task['id'],
                            'activity_id' => $pid
                        )
                    ));
                    if( !empty($_task) ){
                        //disable update task name and phase id
                        // if( $this->taskHasConsumed($task['id']) )
                        //     unset($task['name']);
                        // unset($task['project_planed_phase_id']);
                        //update task
                        $this->ActivityTask->id = $task['id'];
                    } else {
                        goto _end_;
                    }
                } else {
                    $this->ActivityTask->create();
                    $count = $this->ActivityTask->find('count', array(
                        'recursive' => -1,
                        'conditions' => array("ActivityTask.activity_id" => $pid)
                    ));
                    $task['weight'] = $count+1;
                }
                $task['activity_id'] = $pid;
                $this->ActivityTask->save($task);
                $task['id'] = $this->ActivityTask->id;

                //delete old workload
                $this->NctWorkload->deleteAll(array(
                    'NctWorkload.activity_task_id' => $task['id']
                ), false);
                //update new workloads
                $total = 0;
                $final = array();
                $notDelete = array();
                $lib = new LibBehavior();
                foreach($workloads as $range => $assigns){
                    if( $dateType != 0 ){
                        $groupDate = $dateType . '_' . $range;
                        $taskDate = explode('_', $range);
                        $endDate = date('Y-m-d', strtotime($taskDate[1]));
                        $taskDate = date('Y-m-d', strtotime($taskDate[0]));
                    } else {
                        $groupDate = null;
                        $taskDate = $endDate = date('Y-m-d', strtotime($range));
                    }
                    foreach ($assigns as $assign) {
                        $this->NctWorkload->create();
                        $this->NctWorkload->save(array(
                            'activity_task_id' => $task['id'],
                            'task_date' => $taskDate,
                            'reference_id' => $assign['reference_id'],
                            'is_profit_center' => $assign['is_profit_center'],
                            'estimated' => $assign['estimated'],
                            'group_date' => $groupDate,
                            'end_date' => $endDate
                        ));

                        $total += $assign['estimated'];
                        $iiiid = $assign['reference_id'] . '-' . $assign['is_profit_center'];
                        if( isset($final[$iiiid]) )$final[$iiiid] += $assign['estimated'];
                        else $final[$iiiid] = $assign['estimated'];
                    }
                }
                //save total workload in main refer table
                $this->loadModel('Employee');
                $this->loadModel('ProjectEmployeeProfitFunctionRefer');
                $assignText = $assignIds = $assignTypes = array();
                if( $final ){
                    $listId = array_keys($final);
                    //delete
                    $this->ActivityTaskEmployeeRefer->deleteAll(array(
                        'ActivityTaskEmployeeRefer.activity_task_id' => $task['id'],
                        'NOT' => array('CONCAT(ActivityTaskEmployeeRefer.reference_id, "-", ActivityTaskEmployeeRefer.is_profit_center)' => $listId)
                    ), false);
                    //save
                    foreach($final as $id => $est){
                        $id = explode('-', $id);
                        $exist = $this->ActivityTaskEmployeeRefer->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'activity_task_id' => $task['id'],
                                'reference_id' => $id[0],
                                'is_profit_center' => $id[1]
                            )
                        ));
                        if( !empty($exist) ){
                            $this->ActivityTaskEmployeeRefer->id = $exist['ActivityTaskEmployeeRefer']['id'];
                        } else {
                            $this->ActivityTaskEmployeeRefer->create();
                        }
                        $this->ActivityTaskEmployeeRefer->save(array(
                            'activity_task_id' => $task['id'],
                            'reference_id' => $id[0],
                            'is_profit_center' => $id[1],
                            'estimated' => $est
                        ));
                        $assignIds[] = $id[0];
                        $assignTypes[] = $id[1];
                        if( $id[1] == 0 ){
                            $this->Employee->recursive = -1;
                            $emp = $this->Employee->read('first_name, last_name', $id[0]);
                            $assignText[] = $emp['Employee']['first_name'] . ' ' . $emp['Employee']['last_name'];
                        } else {
                            $pcss = $this->ProjectEmployeeProfitFunctionRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'profit_center_id' => $id[0]
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'profit_centers',
                                        'alias' => 'PC',
                                        'type' => 'inner',
                                        'conditions' => array(
                                            'ProjectEmployeeProfitFunctionRefer.profit_center_id = PC.id'
                                        )
                                    )
                                ),
                                'fields' => array('PC.name')
                            ));
                            $assignText[] = 'PC / ' . $pcss['PC']['name'];
                        }
                    }
                }
                $this->ActivityTask->saveField('estimated', $total);
                //post-save
                $activity_task = $this->ActivityTask->read();

                $activity_task['ActivityTask']['task_assign_to_text'] = implode(',', $assignText);
                $activity_task['ActivityTask']['task_assign_to_id'] = $assignIds;
                $activity_task['ActivityTask']['is_profit_center'] = $assignTypes;
                unset($activity_task['ActivityTask']['consumed']);
                $stt = $this->getStatus($task['task_status_id']);
                $pri = $this->getPriority($task['task_priority_id']);
                if( $stt )$activity_task['ActivityTask']['task_status_text'] = $stt['ProjectStatus']['name'];
                else $activity_task['ActivityTask']['task_status_text'] = '';
                if( $pri )$activity_task['ActivityTask']['task_priority_text'] = $pri['ProjectPriority']['priority'];
                else $activity_task['ActivityTask']['task_priority_text'] = '';
                $activity_task['ActivityTask']['task_title'] = $activity_task['ActivityTask']['name'];
                $activity_task['ActivityTask']['task_start_date'] = date('Y-m-d', $activity_task['ActivityTask']['task_start_date']);
                $activity_task['ActivityTask']['task_end_date'] = date('Y-m-d', $activity_task['ActivityTask']['task_end_date']);

                $pro = $this->getProfile($task['profile_id']);
                if( $pro )$project_task['ProjectTask']['profile_text'] = $pro['Profile']['name'];
                else $project_task['ProjectTask']['profile_text'] = '';

                //$this->_syncPhasePlanTime($activity_task);
                $this->_updateParentTask($task['id']);
                $this->_deleteCacheContextMenu();
                $result['data'] = $activity_task['ActivityTask'];
                $result['result'] = true;
            }
        }
        _end_:
        die(json_encode($result));
    }
    private function getStatus($sid){
        if( !$sid )return 0;
        $this->loadModel('ProjectStatus');
        $company_id = $this->employee_info['Company']['id'];
        return $this->ProjectStatus->find('first', array(
            'recursive' => -1,
            'conditions' => array('ProjectStatus.id' => $sid, 'company_id' => $company_id)
        ));
    }
    private function getPriority($id){
        if( !$id )return 0;
        $this->loadModel('ProjectPriority');
        $company_id = $this->employee_info['Company']['id'];
        return $this->ProjectPriority->find('first', array(
            'recursive' => -1,
            'conditions' => array('ProjectPriority.id' => $id, 'company_id' => $company_id)
        ));
    }
    private function getProfile($id){
        if( !$id )return 0;
        $this->loadModel('Profile');
        $company_id = $this->employee_info['Company']['id'];
        return $this->Profile->find('first', array(
            'recursive' => -1,
            'conditions' => array('Profile.id' => $id, 'company_id' => $company_id)
        ));
    }
    public function update_document(){
        $result = array(
            'status' => false,
            'attachment' => ''
        );
        if(!empty($this->data)){
            if (!empty($_FILES['FileField']['name']['attachment'])) {
                App::import('Core', 'Folder');
                $path = $this->_getPath($this->data['Upload']['id']);
                new Folder($path, true, 0777);
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->fileTypes;
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $file = $this->MultiFileUpload->upload();
                if( !empty($file) ){
                    //begin save field
                    $save = 'file:' . $file['attachment']['attachment'];
                    $this->ActivityTask->id = $this->data['Upload']['id'];
                    //indicate what kind of field: file or url
                    $this->ActivityTask->saveField('attachment', $save);
                    $result['status'] = true;
                    $result['attachment'] = $save;
                    $result['file'] = $file['attachment']['attachment'];
                    $result['sync'] = $this->MultiFileUpload->otherServer;
                }
            } else if( !empty($this->data['Upload']['url']) ){
                $save = 'url:' . $this->data['Upload']['url'];
                $this->ActivityTask->id = $this->data['Upload']['id'];
                //indicate what kind of field: file or url
                $this->ActivityTask->saveField('attachment', $save);
                $result['status'] = true;
                $result['attachment'] = $save;
            }
        }
        die(json_encode($result));
    }

    public function view_attachment($task_id){
        $p = $this->ActivityTask->read('attachment', $task_id);
        if( !empty($p['ActivityTask']['attachment']) ){
            $data = explode(':', $p['ActivityTask']['attachment']);
            if( $data[0] == 'file'){
                $fileInfo = pathinfo($data[1]);
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"{$fileInfo['basename']}\"");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                if( @readfile($this->_getPath($task_id) . $fileInfo['basename']) )
                    die;
            } else {
                header('location: ' . substr($p['ActivityTask']['attachment'], 4));
                die;
            }
        }
        die('File not found!');
    }
    protected  function _deleteFile($task_id){
        $p = $this->ActivityTask->read('attachment', $task_id);
        if( !empty($p['ActivityTask']['attachment']) ){
            $data = explode(':', $p['ActivityTask']['attachment']);
            $path = $this->_getPath($task_id);
            if( $data[0] == 'file'){
                $fileInfo = pathinfo($data[1]);
                @unlink($path . $fileInfo['basename']);
                if($this->MultiFileUpload->otherServer){
                    $this->MultiFileUpload->deleteFileToServerOther($path, $fileInfo['basename']);
                }
            }
        }
        $this->ActivityTask->saveField('attachment', null);
        return;
    }
    public function delete_attachment($task_id){
        $this->_deleteFile($task_id);
        die(1);
    }
    //use for load-balancing to upload file to other servers after ->update_document, ajax only
    public function syncX(){
        if( isset($this->data['file']) ){
            $path = $this->_getPath(1);
            if($this->MultiFileUpload->otherServer){
                $this->MultiFileUpload->uploadFileToServerOther($path, $this->data['file']);
            }
        }
        die(1);
    }

    protected function _getPath($task_id) {
        $company = $this->employee_info['Company']['id'];
        $path = FILES . 'activities' . DS . 'activity_tasks' . DS . $company . DS;
        return $path;
    }

    public function update_text(){
        $result = array();
        if( !empty($this->data['id']) ){
            $result['text_updater'] = $this->employee_info['Employee']['fullname'];
            $result['text_time'] = date('Y-m-d H:i:s');
            $this->ActivityTask->id = $this->data['id'];
            $this->ActivityTask->save(array(
                'text_1' => $this->data['text_1'],
                'text_updater' => $result['text_updater'],
                'text_time' => $result['text_time']
            ));
        }
        die(json_encode($result));
    }

    public function saveName(){
        $result = array();
        if( !empty($this->data['id']) ){
            $this->ActivityTask->save(array(
                'id' => $this->data['id'],
                'name' => $this->data['task_title']
            ));
            $result = $this->ActivityTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $this->data['id']
                )
            ));
            $result = $result['ActivityTask'];
        }
        die(json_encode($result));
    }

    private function _getActivityFromResource($employees = array()){
        $result = array();
        $company = $this->employee_info['Company']['id'];

        #1. Get data from request table

        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company,
                'employee_id' => $employees,
                'status' => 2
            ),
            'fields' => array('activity_id', 'task_id'),
            'group' => array('activity_id', 'task_id')
        ));

        $task_ids = array();

        #1.1. Get activity, first result
        foreach($requests as $request){
            $d = $request['ActivityRequest'];
            if( $d['activity_id'] ){
                $result[] = $d['activity_id'];
            }
            if( $d['task_id'] ){
                $task_ids[] = $d['task_id'];
            }
        }

        $consume = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $task_ids
            ),
            'fields' => array('activity_id', 'activity_id')
        ));

        $result = array_merge($result, $consume);

        #2. Get data from assignment

        $assign1 = $this->ActivityTaskEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    array(
                        'reference_id' => $employees,
                        'is_profit_center' => 0
                    )
                )
            ),
            'fields' => array('Task.activity_id', 'Task.activity_id'),
            'group' => array('Task.activity_id'),
            'joins' => array(
                array(
                    'table' => 'activity_tasks',
                    'type' => 'inner',
                    'alias' => 'Task',
                    'conditions' => array(
                        'Task.id = ActivityTaskEmployeeRefer.activity_task_id'
                    )
                )
            )
        ));

        $assign2 = $this->ProjectTaskEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    array(
                        'reference_id' => $employees,
                        'is_profit_center' => 0
                    )
                )
            ),
            'fields' => array('A.activity_id', 'A.activity_id'),
            'group' => array('A.activity_id'),
            'joins' => array(
                array(
                    'table' => 'project_tasks',
                    'type' => 'inner',
                    'alias' => 'Task',
                    'conditions' => array(
                        'Task.id = ProjectTaskEmployeeRefer.project_task_id'
                    )
                ),
                array(
                    'table' => 'projects',
                    'type' => 'inner',
                    'alias' => 'A',
                    'conditions' => array(
                        'A.id = Task.project_id'
                    )
                )
            )
        ));

        $result = array_unique(array_merge($result, $assign1, $assign2));

        return $result;
    }

    private function _getActivityFromPC($pcs = array()){
        $this->loadModels('ActivityRequest', 'ActivityTaskEmployeeRefer', 'ProjectTaskEmployeeRefer');
        $result = array();
        $company = $this->employee_info['Company']['id'];

        //get employees
        $employees = array();
        if( !empty($pcs) ){
            $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'profit_center_id' => $pcs
                ),
                'fields' => array('id', 'id')
            ));
        }

        #1. Get data from request table

        $cond = array(
            'company_id' => $company,
        );
        if( !empty($employees) ){
            $cond['employee_id'] = $employees;
        }

        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => $cond,
            'fields' => array('activity_id', 'task_id', 'employee_id'),
            'group' => array('activity_id', 'task_id', 'employee_id')
        ));

        $task_ids = array();

        #1.1. Get activity, first result
        foreach($requests as $request){
            $d = $request['ActivityRequest'];
            if( $d['activity_id'] ){
                $result[] = $d['activity_id'];
            }
            if( $d['task_id'] ){
                $task_ids[] = $d['task_id'];
            }
        }

        $consume = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $task_ids
            ),
            'fields' => array('activity_id', 'activity_id')
        ));

        $result = array_merge($result, $consume);

        #2. Get data from assignment

        $assign1 = $this->ActivityTaskEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    array(
                        'reference_id' => $pcs,
                        'is_profit_center' => 1
                    ),
                    array(
                        'reference_id' => $employees,
                        'is_profit_center' => 0
                    )
                )
            ),
            'fields' => array('Task.activity_id', 'Task.activity_id'),
            'group' => array('Task.activity_id'),
            'joins' => array(
                array(
                    'table' => 'activity_tasks',
                    'type' => 'inner',
                    'alias' => 'Task',
                    'conditions' => array(
                        'Task.id = ActivityTaskEmployeeRefer.activity_task_id'
                    )
                )
            )
        ));

        $assign2 = $this->ProjectTaskEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    array(
                        'reference_id' => $pcs,
                        'is_profit_center' => 1
                    ),
                    array(
                        'reference_id' => $employees,
                        'is_profit_center' => 0
                    )
                )
            ),
            'fields' => array('A.activity_id', 'A.activity_id'),
            'group' => array('A.activity_id'),
            'joins' => array(
                array(
                    'table' => 'project_tasks',
                    'type' => 'inner',
                    'alias' => 'Task',
                    'conditions' => array(
                        'Task.id = ProjectTaskEmployeeRefer.project_task_id'
                    )
                ),
                array(
                    'table' => 'projects',
                    'type' => 'inner',
                    'alias' => 'A',
                    'conditions' => array(
                        'A.id = Task.project_id'
                    )
                )
            )
        ));

        #3. Get activity
        $this->loadModels('ActivityProfitRefer');
        $activity = $this->ActivityProfitRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'profit_center_id' => $pcs
            ),
            'fields' => array('activity_id', 'activity_id')
        ));

        $result = array_unique(array_merge($result, $assign1, $assign2, $activity));

        return $result;
    }

    /*
     * Get consumed directly from table activity requests (not staffing system)
     *
     * @param   array     $activities         An array of activity ids
     * @param   int       $start              the start day in integer format, must be the begining of month (day 01)
     * @param   int       $end                the end day in integer format, must be the end of month (day 28, 29, 30 or 31)
     * @param   bool      $groupByEmployee    wheather the result should be grouped by employeeID-activityID or just activityID
     *
     * @return  array       the result array
     *
     * @access  private
     *
     * @author  QN
     */

    private function getConsumeFromActivity($activities, $start, $end, $groupByEmployee = false, $pc = array()){
        $maps = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activities
            ),
            'fields' => array('id', 'activity_id')
        ));
        $taskIds = array();
        $list = array();
        foreach ($maps as $x) {
            $dx = $x['ActivityTask'];
            if( !isset($list[$dx['activity_id']]) ){
                $list[$dx['activity_id']] = array();
            }
            $list[$dx['activity_id']][] = $dx['id'];
            $taskIds[] = $dx['id'];
        }
        $field = 'CASE WHEN task_id = 0 THEN activity_id ';
        foreach($list as $id => $task){
            if( empty($task) )continue;
            $field .= sprintf('WHEN task_id IN (%s) THEN %s ', implode($task, ','), $id);
        }
        $field .= ' ELSE NULL END';
        $this->ActivityRequest->virtualFields['month'] = 'FROM_UNIXTIME(`date`, "%Y-%m-01 00:00:00")';
        $this->ActivityRequest->virtualFields['consume'] = 'SUM(`value`)';
        $this->ActivityRequest->virtualFields['activity'] = $field;
        if( !$groupByEmployee ){
            $data = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'status' => 2,
                    'date BETWEEN ? AND ?' => array($start, $end),
                    'OR' => array(
                        'activity_id' => $activities,
                        'task_id' => $taskIds
                    )
                ),
                'fields' => array('month', 'activity', 'consume'),
                'group' => array('month', 'activity')
            ));
            $result = array();
            foreach($data as $x){
                $y = $x['ActivityRequest'];
                $id = $y['activity'];
                $time = strtotime($y['month']);
                $result[$id][$time] = $y['consume'];
            }
        } else {
            $data = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'status' => 2,
                    'date BETWEEN ? AND ?' => array($start, $end),
                    'OR' => array(
                        'activity_id' => $activities,
                        'task_id' => $taskIds
                    )
                ),
                'fields' => array('month', 'activity', 'employee_id', 'consume'),
                'group' => array('month', 'activity', 'employee_id')
            ));
            $result = array();
            foreach($data as $x){
                $y = $x['ActivityRequest'];
                $id = $y['employee_id'] . '-' . $y['activity'];
                $time = strtotime($y['month']);
                $result[$id][$time] = $y['consume'];
            }
        }
        return $result;
    }
	private function calculateFteByYearProfiles($listProfiles, $startDate, $endDate, $employeeName){
		$this->loadModels('Profile', 'ProfileValue');
		$dayEstablished = $employeeName['day_established'];
		//CAPACITY
		$lastYear = date('Y', time()) - 1;
		$nextYear = date('Y', time()) + 5;
		$profiles = $this->Profile->find('all', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $employeeName['company_id']),
			'fields' => array('id', 'name', 'capacity_by_year')
		));
		$profileIds = !empty($profiles) ? Set::classicExtract($profiles, '{n}.Profile.id') : array();
		$profiles = Set::combine($profiles, '{n}.Profile.id', '{n}.Profile');
		$profileValues = $this->ProfileValue->find('list', array(
			'recursive' => -1,
			'conditions' => array('profile_id' => $profileIds, 'year BETWEEN ? AND ?' => array($lastYear, $nextYear)),
			'fields' => array('year', 'value', 'profile_id'),
			'group' => array('profile_id', 'year')
		));
		$fte_by_year = array();
		if(!empty($profileValues)){
			foreach($profileValues as $key => $values ){
			 foreach($values as $year => $value ){
				$fte_by_year[$key][$year]['capacity_by_year']  = (!empty($profiles[$key]) && !empty($profiles[$key]['capacity_by_year'])) ? ($value * $profiles[$key]['capacity_by_year']) : 0;
				$fte_by_year[$key][$year]['fte_by_year']  = !empty($value) ? $value : 0;
			 }
			}
		}
		return $fte_by_year;
	}
		private function valueSumActivityTask($id, $activityTasks , $values){
		$this->loadModels('ActivityRequest', 'ActivityTask', 'ActivityTaskEmployeeRefer', 'Activity');
		$_parentIds = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.parent_id', '{n}.ActivityTask.parent_id') : array();
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        foreach($_parentIds as $k => $value){
            if($value == 0){
                unset($_parentIds[$k]);
            }
        }
		$newActivityRequests = array();
		foreach($values as $task_id => $value){
			$newActivityRequests[$task_id] = array_sum($value);
		}
        $activityRequests = $newActivityRequests;
        $total = 0;
        $sumEstimated = $sumRemain = $sumRemainConsumed = $sumRemainNotConsumed = $t = 0;
		// $referEmployees = $this->ActivityTaskEmployeeRefer->find('all', array(
			// 'recursive' => -1,
			// 'conditions' => array(
				// "activity_task_id" => $_activityTaskId
			// )
		// ));
		// debug($referEmployees); 		
        foreach ($activityTasks as $key => $activityTask) {
			if(in_array($activityTask['ActivityTask']['id'], $_parentIds)){
				unset($activityTasks[$key]);
			}
			else{
				$activityTaskId = $activityTask['ActivityTask']['id'];
				$referencedEmployees = $this->ActivityTaskEmployeeRefer->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						"activity_task_id" => $activityTaskId
					)
				));
				if(count($referencedEmployees) == 0){} 
				else {
					foreach ($referencedEmployees as $key1 => $referencedEmployee) {
						$activityTask[$key]['ActivityTask']['ActivityTaskEmployeeRefer'][] = $referencedEmployee['ActivityTaskEmployeeRefer'];
					}
				}
				$estimated = isset($activityTask['ActivityTask']['estimated']) ? $activityTask['ActivityTask']['estimated'] : 0;
				// Check if Activity Task Existed
				if (isset($activityTaskId)) {
					// Check if Request Existed
					if (isset($activityRequests[$activityTaskId])) {
						$consumed = $activityRequests[$activityTaskId];
						$completed = $estimated - $consumed;
						if($completed < 0){
							$completed = 0;
						}
						$sumRemainConsumed += $completed;
						$total += $consumed;
					} else {
						if(in_array($activityTask['ActivityTask']['id'], $_parentIds, true)){
							//unset($projectTask);
						} else {
							$sumRemainNotConsumed += $estimated;
						}
						$total += 0;
					}
				} else {
					// Error Handle
					$sumRemainNotConsumed += $estimated;
					$total += 0;
				}
			}
		}
		// exit;
        if(!empty($_parentIds)){
            foreach($_parentIds as $_parentId){
                $_consumed = !empty($activityRequests[$_parentId]) ? $activityRequests[$_parentId] : 0;
                $total += $_consumed;
            }
        }
		

        $sumRemain = $sumRemainConsumed + $sumRemainNotConsumed;
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'SUM(value) as consumed'
            ),
            'conditions' => array(
                'activity_id' => $id,
                'company_id' => $this->employee_info['Company']['id'],
                'status' => 2,
                'NOT' => array('value' => 0)
            )
        ));
        $_engaged = $_progression = 0;
        $_engaged = isset($activityRequests[0][0]['consumed']) ? $total + $activityRequests[0][0]['consumed'] : $total;
        $validated = $_engaged+$sumRemain;
        if($validated == 0){
            $_progression = 0;
        } else {
            $_progression = round((($_engaged*100)/$validated), 2);
        }
        if($_progression > 100){
            $_progression = 100;
        } else {
            $_progression = $_progression;
        }
        $_activityTask = array();
        $_activityTask['consumed'] = $_engaged;
        $_activityTask['remain'] = $sumRemain;
        $_activityTask['workload'] = $validated;
        $_activityTask['progression'] = $_progression;
        
        return $_activityTask;
    }
	
}
?>
