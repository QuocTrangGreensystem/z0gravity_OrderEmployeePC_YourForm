<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectTasksPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array3. ? project task (beta): start-date(phase) = start-date(task m) neu start-date(task m) nho nhat va khac NULL
     * @access public
     */
    var $name = 'ProjectTasksPreview';
    var $uses = array('ProjectTask', 'ProjectTeam');
    /**
     * Components
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'SlickExporter');
    var $_developersMode = false;
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Xml', 'Gantt', 'GanttSt','Number', 'GanttV2Preview', 'Time');
    var $_project = null;
    var $_phases = null;
    var $_statuses = null;
    var $_priority = null;
    var $_activityTasks = null;
    var $_activity = null;
    var $_activityTasksById = null;

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->fileTypes = 'jpg,jpeg,bmp,gif,png,txt,zip,gzip,tgz,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,xlsm,csv';
        $this->set('fileTypes', $this->fileTypes);
    }
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null, $new_gantt = null) {
        $useManual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        $this->_checkRole(false, $project_id);

        
        $this->_checkWriteProfile('task');
		$bg_currency = $this->getCurrencyOfBudget();
        $this->loadModel('Project');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $this->ProjectTask->Behaviors->attach('Containable');
        $this->ProjectTask->cacheQueries = true;
        // $this->_ganttFromPhase($project_id);
        $listPrioritiesJson=$this->listPrioritiesJson($project_id);
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->ProjectBudgetInternalDetail->virtualFields = array('total' => 'SUM(ProjectBudgetInternalDetail.budget_md)');
        $budgetInters = $this->ProjectBudgetInternalDetail->find('first', array(
            'fields' => array('total'),
            'recursive' => 1,
            'group' => array('ProjectBudgetInternalDetail.project_id'),
            'conditions' => array('project_id' => $project_id)
        ));
        $this->loadModel('ProjectBudgetExternal');
        $this->ProjectBudgetExternal->virtualFields = array('total' => 'SUM(ProjectBudgetExternal.man_day)');
        $budgetExters = $this->ProjectBudgetExternal->find('first', array(
            'fields' => array('total'),
            'recursive' => 1,
            'conditions' => array('project_id' => $project_id)
        ));
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special' => 1)
        ));

        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
        $varInter = $getDataProjectTasks['workload']-$budgetExters['ProjectBudgetExternal']['total'];
        $var =  $getDataProjectTasks['workload'] - $varE - $budgetInters['ProjectBudgetInternalDetail']['total'];
        $this->ProjectTask->virtualFields = array('s_over' => 'SUM(ProjectTask.overload)');
        $varO = $this->ProjectTask->find('first', array(
            'fields' => array('ProjectTask.s_over'),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id,'special'=>0)
        ));
        $workloadInter = $getDataProjectTasks['workload'] - $varE - $varO['ProjectTask']['s_over'];
        $workloadExter = $varE;
        
        // kiem tra role
        $myRole = $this->employee_info['Role']['name'];
        // kiem tra check freeze
        $settingP = $this->requestAction('/project_settings/get');
        $this->loadModels('Employee', 'Translation');
        $modifyF = $this->Employee->find('first',array('recusive'=>-1,'conditions'=>array(
            'Employee.id'=>$projectName['Project']['freeze_by']),'fields'=>array('Employee.fullname')));
        //get all employee company.
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    array('company_id' => $this->employee_info['Company']['id']),
                    array('company_id' => null)
                )
            ),
            'fields' => array('id', 'fullname')
        ));
        // tinh thoi gian delay du an
        $this->loadModel('ProjectPhasePlan');
        $projectPlan = $this->ProjectPhasePlan->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'NOT' => array('phase_planed_start_date' => '0000-00-00', 'phase_planed_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MAX(phase_planed_end_date) AS endDate'
            )
        ));
        $projectReal = $this->ProjectPhasePlan->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'NOT' => array('phase_planed_start_date' => '0000-00-00', 'phase_planed_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MAX(phase_real_end_date) AS endDate'
            )
        ));
        $timeDelay = strtotime($projectReal[0]['endDate']) - strtotime($projectPlan[0]['endDate']);
        $delay = intval($timeDelay/86400);
        $consumedInter = $getDataProjectTasks['consumed'];
        $remainExter = $varE - $externalConsumeds;
        $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
        $this->ProjectTask->virtualFields = array('consumedex' => 'SUM(ProjectTask.special_consumed)');
        $consumedExter = $this->ProjectTask->find('first', array(
            'fields' => array('ProjectTask.consumedex'),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id,'special'=>1)
        ));
        $this->loadModel('ProjectMilestone');
        $projectMilestones = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated'),
            'order' => array('milestone_date' => 'ASC')
        ));
        $projectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        // BudgetSetting
        $this->loadModel('BudgetSetting');
        $company_id=isset($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : '';
        $budget_settingst=$this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id ,
                'currency_budget' =>1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $workdays = $this->requestAction('/project_tasks/getWorkdays');
        //check if user is pm/tech/chief
        $this->set('canModify', $this->canModifyProject($project_id));
        $this->loadModel('ProjectEmployeeManager');
        $isEmployeeManager = $this->ProjectEmployeeManager->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            )
        ));
        $_isPmOfPj = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'project_manager_id' => $this->employee_info['Employee']['id'],
            ),
            'fields' => array('id', 'project_manager_id')
        ));
        if(!empty($isEmployeeManager) || !empty($_isPmOfPj)){
            $isEmployeeManager = true;
        } else {
            $isEmployeeManager = false;
        }
		$listTaskName = $this->ProjectTask->find('list', array(
            'fields' => array('task_title'),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        // Check avatar
        // $checkAvatar = $this->checkAvatar();
        $this->phase_vision($project_id, $new_gantt);
        $employee_id = $this->employee_info['Employee']['id'];
        $list_project_status = $this->list_projectStatus($project_id);
        $list_project_status = !empty($list_project_status) ? Set::combine($list_project_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus') : array();
        $listAssign = $this->_getTeamEmployeesAssigned($project_id);
		$this->loadModel('HistoryFilter');
        $_history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url'],
                'employee_id' => $this->employee_info['Employee']['id']
            )
        ));
		$_history = (array) @unserialize($_history['HistoryFilter']['params']);
		$showGannt = isset($_history['showGantt']) ? (  $_history['showGantt'] == 'false' ? 0 : 1 ) : 1;
        $this->set(compact('listAssign','workdays', 'remainInter', 'remainExter', 'projectName', 'project_id', 'listPrioritiesJson','budgetInters','getDataProjectTasks','var','myRole','settingP','modifyF','budgetExters','varInter','workloadInter','workloadExter','delay','consumedInter','consumedExter','projectMilestones', 'listEmployee','budget_settings', 'isEmployeeManager', 'employee_id', '_isPmOfPj','list_project_status', 'showGannt', 'bg_currency', 'listTaskName'));
		
		/* For popup add task */
		$listAllStatus = !empty($list_project_status) ? Set::combine($list_project_status, '{n}.id', '{n}.name') : array();
		//listPhases duoc lay o function $this->phase_vision
		$projectPriorities = $this->ProjectTask->ProjectPriority->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'priority'),
		));
		$projectProfiles = $this->Profile->find('list', array(
			'recursive' => -1,
			'order' => array('id' => 'ASC'),
			'conditions' => array(
				'company_id' => $company_id,
			),
			'fields' => array('id', 'name'),
		));
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $company_id
			),
			'recursive' => -1,
			'fields' => array('original_text', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
					'type' => 'left'
				)
			),
			'order' => array('TranslationSetting.setting_order' => 'ASC')
		));
		
		$e_history = $this->Employee->HistoryFilter->find('list', array(
			'recursive' => -1,
			'fields' => array( 'path', 'params'),
			'conditions' => array(
				'path' => array('setting_display_name_resource','auto_refresh_gantt'),
				'employee_id' => $this->employee_info['Employee']['id']
			)
		));
		// debug( $e_history); exit;
		$display_rescource_name = !empty($e_history['setting_display_name_resource']) ? (int) $e_history['setting_display_name_resource'] : 0;
		$autoRefreshGantt = !empty($e_history['auto_refresh_gantt']) ? (int) $e_history['auto_refresh_gantt'] : 0;
		
		// $workdays da duoc lay o tren
		// $project_id da duoc lay o tren
		$this->set(compact('listAllStatus', 'projectPriorities', 'projectProfiles', 'adminTaskSetting', 'display_rescource_name', 'autoRefreshGantt'));	
		/* END For popup add task */
		if( $this->params['isAjax'] ){
			$this->render('gantt_chart');
		}
    }

    public function canModifyProject($projects){
        if( $this->employee_info['Role']['id'] == 2 ) return true;
        if( $this->employee_info['Role']['id'] == 4 ) return false;
        $id = $this->employee_info['Employee']['id'];
        $result1 = ClassRegistry::init('Project')->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $projects,
                'OR' => array(
                    'project_manager_id' => $id,
                    'technical_manager_id' => $id,
                    'chief_business_id' => $id
                )
            )
        ));
        if( $result1 )return true;
        $result = ClassRegistry::init('ProjectEmployeeManager')->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projects,
                'project_manager_id' => $id,
                'type' => array('PM', 'TM', 'CB'),
                'is_profit_center' => 0
            )
        ));
        return $result > 0;
    }

    /**
    * get dinamic budget and var
    **/
    function budget_var($project_id = null){
        $this->layout = '';
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetInternalDetail');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $this->ProjectBudgetInternalDetail->virtualFields = array('total' => 'SUM(ProjectBudgetInternalDetail.budget_md)');
        $budgetInters = $this->ProjectBudgetInternalDetail->find('first', array(
            'fields' => array('total'),
            'recursive' => 1,
            'group' => array('ProjectBudgetInternalDetail.project_id'),
            'conditions' => array('project_id' => $project_id)
        ));
        $getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
        $engagedErro = 0;
        if(!empty($projectName['Project']['activity_id'])){
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $employees = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $id => $val) {
                    $reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
        }
        $var = $getDataProjectTasks['workload']-$budgetInters['ProjectBudgetInternalDetail']['total'];
        $this->ProjectTask->virtualFields = array('total' => 'SUM(ProjectTask.estimated)');
        $varE = $this->ProjectTask->find('first', array(
            'fields' => array('ProjectTask.total'),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id,'special'=>1)
        ));
        $this->ProjectTask->virtualFields = array('s_over' => 'SUM(ProjectTask.overload)');
        $varO = $this->ProjectTask->find('first', array(
            'fields' => array('ProjectTask.s_over'),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id,'special'=>0)
        ));
        $workloadInter = $getDataProjectTasks['workload'] - $varE['ProjectTask']['total']-$varO['ProjectTask']['s_over'];
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
                    'NOT' => array('value' => 0)
                )
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
                    'NOT' => array('value' => 0)
                )
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

    function _getTaskStartDay($task){
        return $task['ProjectTask']['task_start_date'];
    }

    function index1($project_id = null) {
        echo "Migrating script ...<br>";

        $this->ProjectTask->Project->recursive=-1;
        $linked_projects = $this->ProjectTask->Project->find("all",
            array(
                "conditions" => array(
                    'Project.activity_id <>' => null
                )
            )
        );
        echo "Project has links to Activities need to be checked: ";
        echo "<br>";
        foreach ($linked_projects as $key => $linked_project) {
            echo "Project name: " . $linked_project['Project']['project_name'] . " - - - - - - id: " . $linked_project['Project']['id'] . "<br>" ;
        }
        echo "<br>";
        echo "Checking Each project". "<br>";
        echo "<br>";
        $this->ProjectTask->Project->recursive=-1;
        $this->ProjectTask->recursive=-1;
        $this->ProjectTask->recursive=-1;
        $this->ProjectTask->recursive=-1;
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->ActivityRequest->recursive=-1;
        $this->ActivityTask->recursive=-1;
        foreach ($linked_projects as $key => $linked_project) {

            $linked_project_id = $linked_project['Project']['id'];
            echo "Checking Project: " . $linked_project['Project']['project_name'] . " - - - - - - id: " . $linked_project_id . "<br>" ;
            $project_tasks = $this->ProjectTask->find("list",
                array(
                    "conditions" => array(
                        'ProjectTask.project_id' => $linked_project_id
                    )
                )
            );
            echo "This project has tasks: " . implode(",", array_keys($project_tasks)) ."<br>";
            echo "<br>";
            $project_tasks = array_keys($project_tasks);
            echo "And has correspding Activity Tasks: <br>";
            //Get all activities request of this project, group by activity.
            $activities_tasks = $this->ActivityTask->find("list",
                array(
                    "conditions" => array(
                        'ActivityTask.project_task_id ' =>$project_tasks
                    )
                )
            );
            echo implode(",", array_keys($activities_tasks));
            //$activities_tasks = implode(",", array_keys($activities_tasks));
            echo "<br>";
            echo "Get the set of Activity Requests based on corresponding Activity Tasks <br>";
            $activities_tasks = $this->ActivityTask->find("all",
                array(
                    "conditions" => array(
                        'ActivityTask.project_task_id ' =>$project_tasks
                    )
                )
            );
            foreach ($activities_tasks as $key => $activities_task) {
                $activities_task = $activities_task['ActivityTask'];
                $activities_task_id = $activities_task['id'];
                $activities_task_project_task_id = $activities_task['project_task_id'];
                $activities_requests = $this->ActivityRequest->find("all",
                    array(
                        "conditions" => array(
                            'ActivityRequest.task_id ' => $activities_task_id
                        ),
                        'fields' => array('MAX(ActivityRequest.date) as max_date', 'MIN(ActivityRequest.date) as min_date'),
                        // 'group' => 'ActivityRequest.task_id'
                    )
                );
                $min_date = isset($activities_requests[0][0]['min_date'])?date('Y-m-d', $activities_requests[0][0]['min_date']):false;
                $max_date = isset($activities_requests[0][0]['max_date'])?date('Y-m-d', $activities_requests[0][0]['max_date']):false;
                if($min_date){
                    echo "Activity Requests for Activity Task: ". $activities_task_id . ' begin: ';
                    echo "From: " . $min_date . " To " . $max_date;
                    echo "<br>";
                    echo "Getting Infor for Project Task " . $activities_task_project_task_id . ": ";
                    $project_task = $this->_getProjectTaskById($activities_task_project_task_id);
                    $project_task = $project_task['ProjectTask'];
                    $project_task_start_date = date('Y-m-d', strtotime($project_task['task_start_date']));
                    $project_task_end_date = date('Y-m-d', strtotime($project_task['task_end_date']));
                    $project_task_duration = $project_task['duration'];
                    echo "Checking ProjectTask Start Date: " . $project_task_start_date ;
                    echo " - End Date: " . $project_task_end_date;
                    echo " - Duration: " . $project_task_duration . "<br>";
                    // Request Begin before the Task Has been set up !!!
                    if($project_task_start_date > $min_date){
                        echo "<p style='color:red'> Woop, this project task seems not right <p>";
                        echo "System will attemp to modify the date of task here <br>";
                        echo "Calculate ProjectTask start date" . $min_date . "<br>";
                        echo "Calculate ProjectTask end date: ". $this->_durationEndDate($min_date, '', $project_task_duration) . "<br>";
                    }
                    echo "<hr>";
                }
            }
            echo "<br>";
            echo "<hr>";
            echo "<br>";
        }
        die;
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
            if (!empty($this->data['id'])) {
                $this->ProjectTask->id = $this->data['id'];
            }
            $data = array();
            foreach (array('task_start_date', 'task_end_date', 'task_real_end_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectTask->convertTime($this->data[$key]);
                }
            }
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectTask->save(array_diff_key(array_merge($this->data, $data), array('employee_id' => '')))) {
                    $result = true;
                    $this->Session->setFlash(__('The Task has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Task could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectTask->id;
            }
            $this->loadModel('ProjectTaskEmployeeRefer');
            $saveds = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectTaskEmployeeRefer.project_task_id' => $this->data['id']),
                'fields' => array('id')
                    ));
            if (!empty($this->data['employee_id'])) {
                if (!empty($saveds)) {
                    foreach ($saveds as $key) {
                        $this->ProjectTaskEmployeeRefer->id = $key['ProjectTaskEmployeeRefer']['id'];
                        $this->ProjectTaskEmployeeRefer->delete();
                    }
                }
                foreach ($this->data['employee_id'] as $employee) {
                    $this->ProjectTaskEmployeeRefer->create();
                    $data = array(
                        'reference_id' => $employee,
                        'project_task_id' => $this->data['id']
                    );
                    $this->ProjectTaskEmployeeRefer->save($data);
                }
            } else {
                if (!empty($saveds)) {
                    foreach ($saveds as $key) {
                        $this->ProjectTaskEmployeeRefer->id = $key['ProjectTaskEmployeeRefer']['id'];
                        $this->ProjectTaskEmployeeRefer->delete();
                    }
                }
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($project_id = null) {
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $conditions = array('ProjectTask.project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectTask.id'] = $data;
            }
        }
        $this->ProjectTask->Behaviors->attach('Containable');
        $this->ProjectTask->cacheQueries = true;
        $projectTasks = $this->ProjectTask->find("all", array(
            'fields' => array('id', 'task_title', 'project_planed_phase_id', 'task_completed', 'task_start_date', 'task_end_date', 'task_real_end_date'),
            'contain' => array(
                'ProjectPriority' => array(
                    'id', 'priority'
                ),
                'ProjectStatus' => array(
                    'id', 'name'
                ),
                'Employee' => array(
                    'id', 'first_name', 'last_name'
                )
            ), "conditions" => $conditions));
        $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
        $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
            'fields' => array('id'),
            'contain' => array('ProjectPhase' => array('id', 'name')),
            'conditions' => array(
                "project_id" => $projectName['Project']['id'],
                'company_id' => $projectName['Project']['company_id']
            )
        ));
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name');
        $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectTasks[$id])) {
                    unset($data[$id]);
                    unset($projectTasks[$id]);
                    continue;
                }
                $data[$id] = $projectTasks[$id];
            }
            $projectTasks = $data;
            unset($data);
        }
        $this->set(compact('projectTasks', 'projectName', 'projectPhases'));
        $this->layout = '';
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project task', true), 'error');
            $this->redirect(array('action' => 'index', $this->data["ProjectTask"]["project_id"]));
        }
        if (!empty($this->data)) {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $this->data["ProjectTask"]["task_start_date"] = $str_utility->convertToSQLDate($this->data["ProjectTask"]["task_start_date"]);
            $this->data["ProjectTask"]["task_end_date"] = $str_utility->convertToSQLDate($this->data["ProjectTask"]["task_end_date"]);
            $this->data["ProjectTask"]["task_real_end_date"] = $str_utility->convertToSQLDate($this->data["ProjectTask"]["task_real_end_date"]);
            if ($this->ProjectTask->save($this->data)) {
                $this->Session->setFlash(sprintf(__('The project task %s has been saved.', true), '<b>' . $this->data["ProjectTask"]["task_title"] . '</b>'), 'success');
                $this->redirect(array('action' => 'index', $this->data["ProjectTask"]["project_id"]));
            } else {
                $this->Session->setFlash(__('The project task could not be saved. Please, try again.', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectTask->read(null, $id);
        }
    }

    /**
     * get_start_end_date_of_phase
     *
     * @return void
     * @access public
     */
    function get_start_end_date_of_phase($project_id = null, $project_planed_phase_id = null) {
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        $this->autoRender = false;
        if (($project_id != "") && ($project_planed_phase_id != "")) {
            $record = $this->ProjectTask->ProjectPhasePlan->find('first', array('conditions' => array('ProjectPhasePlan.project_id' => $project_id, 'ProjectPhasePlan.project_planed_phase_id' => $project_planed_phase_id)));
            if (!empty($record)) {
                echo $str_utility->convertToVNDate($record['ProjectPhasePlan']['phase_planed_start_date']) . "|" . $str_utility->convertToVNDate($record['ProjectPhasePlan']['phase_planed_end_date']) . "|" . "(from " . $str_utility->convertToVNDate($record['ProjectPhasePlan']['phase_planed_start_date']) . " to " . $str_utility->convertToVNDate($record['ProjectPhasePlan']['phase_planed_end_date']) . ") (*)";
            } else {
                echo __("(dd-mm-yyyy)(*)", true);
            }
        }
    }

    /**
     * check_duplicate
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate() {
        $project_id = $_POST['project_id'];
        $project_phase_id = $_POST['project_phase_id'];
        $title = $_POST['title'];
        $check = $this->ProjectTask->find('count', array('conditions' => array(
            "ProjectTask.project_id" => $project_id,
            "ProjectTask.project_planed_phase_id" => $project_phase_id,
            "ProjectTask.task_title" => $title,
        )));
        echo $check;
        exit;
    }

    /**
     * exportExcel
     * Export to Excel
     *
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        //$this->layout = 'excel';
        $typeExport = true;
        if(!empty($this->data['Export']['list'])){
            $typeExport = false;
        }
        $this->set('columns', $this->name_columna);
        $this->set('projectName', $this->ProjectTask->Project->find("first", array('conditions' => -1, "fields" => array('Project.project_name', 'off_freeze'),
            'conditions' => array('Project.id' => $project_id))));
        $projectTasks = $this->_dataExport($project_id);
        $settingP = $this->requestAction('/project_settings/get');
        $this->loadModel('Project');
        $checkP = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields'=>'is_freeze'
        ));
        $this->set(compact('projectTasks','typeExport','settingP','checkP'));
    }

    //viet funtion nay su dung cho project
    function getDataTasks($project_id = null){
        $projectTasks = $this->_dataExport($project_id);
        return $projectTasks;
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function detail($id = null) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->loadModel('ActivityRequest');
        $projectTaskName = $this->ProjectTask->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));
        if ($projectTaskName) {
            $projectTaskIds[] = $projectTaskName['ProjectTask']['id'];
            if($projectTaskName['ProjectTask']['parent_id'] == 0 || $projectTaskName['ProjectTask']['parent_id'] == ''){
                $taskChilds = $this->ProjectTask->find('list', array('fields' => array('id'), 'recursive' => -1, 'conditions' => array('parent_id' => $id)));
                $projectTaskIds = !empty($taskChilds) ? array_merge($projectTaskIds, $taskChilds) : $projectTaskIds;
            }
            $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'fields' => array('id'), 'conditions' => array('project_task_id' => $projectTaskIds)));
            foreach ($activityTasks as $activityTask) {
                $activityTaskId[] = $activityTask['ActivityTask']['id'];
            }
            if (!empty($activityTaskId)) {
                if (!empty($this->params['url']['start']) && !empty($this->params['url']['end'])) {
                    $_start = strtotime(@$this->params['url']['start']);
                    $_end = strtotime(@$this->params['url']['end']);
                    $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value', 'status'),
                        'conditions' => array(
                            'task_id' => $activityTaskId,
                            'date BETWEEN ? AND ?' => array($_start, $_end),
                            'NOT' => array('value' => 0)
                        )
                    ));
                    $this->set(compact('_start', '_end'));
                } else {
                    $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value', 'status'),
                        'conditions' => array('task_id' => $activityTaskId, 'NOT' => array('value' => 0))));
                }
            }
        }
        if (empty($projectTaskName) || empty($datas)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $activities = array();
        $months = array();
        foreach ($datas as $data) {
            $data = array_shift($data);
            list($y, $m) = explode('-', date('Y-n', $data['date']));
            if (!isset($activities[$data['employee_id']][$y . '-' . $m]['validated'])) {
                $activities[$data['employee_id']][$y . '-' . $m]['validated'] = 0;
            }
            if (!isset($activities[$data['employee_id']][$y . '-' . $m]['notValidated'])) {
                $activities[$data['employee_id']][$y . '-' . $m]['notValidated'] = 0;
            }
            if( $data['status'] == 2 )$activities[$data['employee_id']][$y . '-' . $m]['validated'] += floatval($data['value']);
            else $activities[$data['employee_id']][$y . '-' . $m]['notValidated'] += floatval($data['value']);
            $months[$y][$m] = $m;
        }
        $_minYear = min(array_keys($months));
        $_minMonth = min($months[$_minYear]);
        $_maxYear = max(array_keys($months));
        $_maxMonth = max($months[$_maxYear]);

        $months = array_unique(array($_minYear, $_maxYear));
        $this->ActivityRequest->Employee->unbindModelAll();
        $this->ActivityRequest->Employee->bindModel(array(
            'hasMany' => array('ProjectEmployeeProfitFunctionRefer')
        ));
        $employees = $this->ActivityRequest->Employee->find('all', array(
            'fields' => array('id', 'first_name', 'last_name'),
            'conditions' => array('Employee.id' => array_keys($activities)),
        ));
        foreach ($employees as $employee) {
            $idProfitCenters[] = $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'];
        }
        $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
            'conditions' => array('ProfitCenter.company_id' => $employeeName['company_id'], 'ProfitCenter.id' => $idProfitCenters),
            'group' => array('ProfitCenter.name')));
        $this->set(compact(array('_minYear', '_minMonth', '_maxYear', '_maxMonth', 'activities', 'employees', 'projectTaskName', 'profitCenters')));
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
                    if($element['parent_id'] == -1){
                        $_remains = $_overload = $_manualOverload = $_manualCons = $_unitPrice = $_consumedEuro = $_remainEuro = $_workloadEuro = $_estimatedEuro = $_estimated = $_initial_estimated = $_consumed = $_totalPrvious = $_totalEstimatedPr = $_totalInitialEstimatedPr = $_wait = 0;
                        $amount = $calculatedAmount = 0;
                        foreach($element['children'] as $id => $value) {
                            $amount += isset($value['amount']) ? $value['amount'] : 0;
                            $calculatedAmount += isset($value['progress_order_amount']) ? $value['progress_order_amount'] : 0;
                            $_remains += (float) $value['remain'];
                            $_wait += (float) $value['wait'];
                            $_estimated += (float) $value['estimated'];
                            $_initial_estimated += (float) $value['initial_estimated'];
                            $_overload += !empty($value['overload']) ? $value['overload'] : 0;
                            $_manualOverload += !empty($value['manual_overload']) ? $value['manual_overload'] : 0;
                            $_unitPrice += !empty($value['unit_price']) ? $value['unit_price'] : 0;
                            $_consumedEuro += !empty($value['consumed_euro']) ? $value['consumed_euro'] : 0;
                            $_remainEuro += !empty($value['remain_euro']) ? $value['remain_euro'] : 0;
                            $_workloadEuro += !empty($value['workload_euro']) ? $value['workload_euro'] : 0;
                            $_estimatedEuro += !empty($value['estimated_euro']) ? $value['estimated_euro'] : 0;
                            $_manualCons += !empty($value['manual_consumed']) ? $value['manual_consumed'] : 0;
                            if(isset($value['is_activity'])) {
                                if($value['is_activity'] == 'true') {
                                    $_totalPrvious = (float) $value['consumed'];
                                    $_totalEstimatedPr = (float) $value['estimated'];
                                    $_totalInitialEstimatedPr = (float) $value['initial_estimated'];
                                    // $_manualCons = $value['manual_consumed'];
                                }
                            }
                        }
                        $element['amount'] = $amount;
                        $element['progress_order_amount'] = $calculatedAmount;
                        $element['progress_order'] = $amount ? round($calculatedAmount / $amount * 100, 2) : 0;
                        $element['consumed'] = $element['consumed']+$_totalPrvious;
                        $element['estimated'] = $_estimated;
                        $element['initial_estimated'] = $_initial_estimated;
                        $element['remain'] = $_remains;
                        $element['wait'] = $_wait;
                        $element['overload'] = $_overload;
                        $element['manual_overload'] = $_manualOverload;
                        $element['manual_consumed'] = $_manualCons;
                        $element['unit_price'] = $_unitPrice;
                        $element['consumed_euro'] = $_consumedEuro;
                        $element['remain_euro'] = $_remainEuro;
                        $element['workload_euro'] = $_workloadEuro;
                        $element['estimated_euro'] = $_estimatedEuro;
                        if( !$useManual ) {
                            if($element['consumed'] == 0){
                                $element['completed'] = '0 %';
                            } else {
                                $element['completed'] = round(($element['consumed'] * 100)/($element['estimated']+$element['overload']),2).' %';
                            }
                        } else {
                            if($element['manual_consumed'] == 0){
                                $element['completed'] = '0 %';
                            } else {
                                $element['completed'] = round(($element['manual_consumed'] * 100)/($element['estimated']+$element['manual_overload']),2).' %';
                            }
                        }
                    } else {
                        $sumRemain = $sumEstimated = $sumInitialEstimated = $sumOverload = $sumWait = $sumManualOverload = $unitPrice = $consumedEuro = $remainEuro = $workloadEuro = $estimatedEuro = $sumManualCs = 0;

                        foreach ($children as $keys => $child){
                            if(isset($child['consumed'])){
                                $element['consumed'] += (float) $child['consumed'];
                                $element['manual_consumed'] += (float) $child['manual_consumed'];
                            }
                            $sumRemain += (float) $child['remain'];
                            $sumWait += (float) $child['wait'];
                            $sumEstimated += (float) $child['estimated'];
                            $unitPrice += !empty($child['unit_price']) ? $child['unit_price'] : 0;
                            $consumedEuro += !empty($child['consumed_euro']) ? $child['consumed_euro'] : 0;
                            $remainEuro += !empty($child['remain_euro']) ? $child['remain_euro'] : 0;
                            $workloadEuro += !empty($child['workload_euro']) ? $child['workload_euro'] : 0;
                            $estimatedEuro += !empty($child['estimated_euro']) ? $child['estimated_euro'] : 0;
                            $sumInitialEstimated += (float) $child['initial_estimated'];
                            $sumOverload += !empty($child['overload']) ? $child['overload'] : 0;
                            $sumManualOverload += !empty($child['manual_overload']) ? $child['manual_overload'] : 0;
                            $sumManualCs += !empty($child['manual_consumed']) ? $child['manual_consumed'] : 0;
                            $element['remain'] = $sumRemain;
                            $element['wait'] = $sumWait;
                            $element['estimated'] = $sumEstimated;
                            $element['initial_estimated'] = $sumInitialEstimated;
                            $element['overload'] = $sumOverload;
                            $element['manual_overload'] = $sumManualOverload;
                            $element['manual_consumed'] = $sumManualCs;
                            $element['unit_price'] = $unitPrice;
                            $element['consumed_euro'] = $consumedEuro;
                            $element['remain_euro'] = $remainEuro;
                            $element['workload_euro'] = $workloadEuro;
                            $element['estimated_euro'] = $estimatedEuro;
                            //uses "isset" to fix part/phase with no children
                            if( !isset($element['amount']) ){
                                $element['amount'] = 0;
                            }
                            if( !isset($element['progress_order_amount']) ){
                                $element['progress_order_amount'] = 0;
                            }
                            $element['amount'] += isset($child['amount']) ? $child['amount'] : 0;
                            $element['progress_order_amount'] += isset($child['progress_order_amount']) ? $child['progress_order_amount'] : 0;
                            $element['progress_order'] = $element['amount'] > 0 ? round($element['progress_order_amount'] / $element['amount'] * 100, 2) : 0;
                            if( !$useManual ){
                                if($element['consumed'] <= 0){
                                    $element['completed'] = '0 %';
                                } else {
                                    $completedPhase = round((($element['consumed']*100)/($sumEstimated+$sumOverload)), 2);
                                    if($completedPhase > 100){
                                        $element['completed'] = '100 %';
                                    } else {
                                        $element['completed'] = $completedPhase . ' %';
                                    }
                                }
                            } else {
                                if($element['manual_consumed'] <= 0){
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
                $branch[] = $element;
            } else {
            }
        }
        return $branch;
    }

    /**
    * Function:
    * @var     :
    * @return  :
    * @author : BANGVN
    **/
    private function _checkExistActivityTask($activity_id, $project_task_id){
        $activity_tasks = $this->_getActivityTasksByActivityIdAndProjectTaskId($activity_id, $project_task_id);
        if (isset($activity_tasks)) {
            if(count($activity_tasks) > 0) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    /**
    * Get Company By ID
    *
    * @return void
    * @access protected
    */
    private function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);

        return $this->employee_info['Employee'];
    }

    private function _getEmployees($project_id) {
        $project = $this->_getProject($project_id);
        $this->loadModel('ProjectTeam');
        $this->ProjectTeam->Behaviors->attach('Containable');
        $employees = $this->_getTeamEmployees($project_id);
        $projectTeams = $this->ProjectTeam->find("all", array(
            'fields' => array('id', 'price_by_date', 'work_expected', 'project_function_id', 'profit_center_id'),
            'contain' => array(
                'ProjectFunctionEmployeeRefer' => array(
                    'fields' => array('is_backup', 'profit_center_id', 'employee_id')
            )),
            "conditions" => array('project_id' => $project_id)
        ));

        foreach ($projectTeams as $projectTeam) {
            if (empty($projectTeam['ProjectFunctionEmployeeRefer'])) {
                $profitCenterId[] = $projectTeam['ProjectTeam']['profit_center_id'];
            }
        }
        if (!empty($profitCenterId)) {
            $profitCenters = $this->ProjectTeam->ProfitCenter->find('list', array(
                'conditions' => array('company_id' => $project['Project']['company_id'], 'ProfitCenter.id' => $profitCenterId)
                    ));
            foreach ($profitCenters as $ks => $profitCenter) {
                $profitCenters[$ks] = 'PC / ' . $profitCenter;
            }
        } else {

        }
        $employees = Set::pushDiff($employees, $profitCenters);
        return $employees;
    }

    private function _getProject($project_id) {
        if (!isset($this->_project)) {
            $project = $this->ProjectTask->Project->find("first",
                array(
                    'conditions' => array('Project.id' => $project_id)
                )
            );
            $this->_project = $project;
        }

        return $this->_project;
    }

    private function _getProjectPhasePlan($project_id, $project_planed_phase_id) {
        $project = $this->_getProject($project_id);
        $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
        $projectPhasePlan = $this->ProjectTask->ProjectPhasePlan->find(
                'all', array(
                    'conditions' => array(
                        // "ProjectPhasePlan.project_planed_phase_id" => $project_planed_phase_id,
                        "ProjectPhasePlan.id" => $project_planed_phase_id,
                        "ProjectPhasePlan.project_id" => $project_id
                    )
                )
        );
        return $projectPhasePlan;
    }

    private function _getProjectTaskById($project_task_id){
        $project_task = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                "ProjectTask.id" => $project_task_id
            ),
            'fields' => array('task_title', 'task_start_date', 'task_end_date', 'duration')
        ));
        return $project_task;
    }

    private function _getPhaseses($project_id) {
        if (!isset($this->_phases)) {
            $project = $this->_getProject($project_id);
            $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
            $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
                'fields' => array('id', 'phase_planed_start_date', 'phase_planed_end_date', 'project_part_id'),
                'contain' => array('ProjectPhase' => array('id', 'name'), 'ProjectPart' => array('id', 'title')),
                'conditions' => array(
                    "ProjectPhasePlan.project_id" => $project['Project']['id'],
                    'company_id' => $project['Project']['company_id']
                )
            ));
            $this->_phases = $projectPhases;
        }
        return $this->_phases;
    }

    private function _getPhasePlanCombinedPart($project_id){
        $projectPhases = $this->_getPhaseses($project_id);
        foreach ($projectPhases as $k => $projectPhase) {
            if (!empty($projectPhase['ProjectPart']['title'])) {
                $projectPhases[$k]['ProjectPhase']['name'] = $projectPhase['ProjectPart']['title'] . " / " . $projectPhase['ProjectPhase']['name'];
            } else {
                $projectPhases[$k]['ProjectPhase']['name'] = ($projectPhase['ProjectPhase']['name']);
            }
        }
        $projectPhasePlans = Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name');
        return $projectPhasePlans;
    }

    private function _getPhasesesCombinedPart($project_id) {
        $projectPhases = $this->_getPhaseses($project_id);
        foreach ($projectPhases as $k => $projectPhase) {
            if (!empty($projectPhase['ProjectPart']['title'])) {
                $projectPhases[$k]['ProjectPhase']['name'] = $projectPhase['ProjectPart']['title'] . " / " . $projectPhase['ProjectPhase']['name'];
            } else {
                $projectPhases[$k]['ProjectPhase']['name'] = ($projectPhase['ProjectPhase']['name']);
            }
        }
        $phaseInfo = Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhasePlan');
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name');
        return $projectPhases;
    }

    private function _getPhasesesCombined($project_id) {
        $projectPhases = $this->_getPhaseses($project_id);
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name');
        return $projectPhases;
    }

    /**
     * Function: Select all Project Phase Plans of Project
     * @var     : int $project_id
     * @return  : array $phasePlans: Project Phase Plans
     * @author : HUUPC
     */
    private function _getAllPojectPhasePlans($project_id) {
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ProjectPart');

        $projectPhasePlans = $this->ProjectPhasePlan->find("all", array(
            'fields' => array('id', 'project_part_id', 'project_planed_phase_id', 'project_phase_status_id',
                'phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date'),
            'contain' => array(
                'ProjectPhase' => array(
                    'id'
            )),
            'order' => array('weight' => 'asc'),
            'conditions' => array('project_id' => $project_id)
        ));

        $projectPhases = $this->ProjectPhasePlan->ProjectPhase->find('list', array(
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
                )
            ));

        $projectParts = $this->ProjectPart->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'title'),
            'order' => array('weight' => 'ASC')
            ));

        $phasePlans = array();
        foreach ($projectPhasePlans as $key => $projectPhasePlan) {
            // $phasePlans[$key]['ProjectPhase']['id']                         = $projectPhasePlan['ProjectPhase']['id'];
            $phasePlans[$key]['ProjectPhase']['phase_reference_id']         = $projectPhasePlan['ProjectPhase']['id'];
            $phasePlans[$key]['ProjectPhase']['id']                         = $projectPhasePlan['ProjectPhasePlan']['id'];
            //$part = isset($projectPhasePlan['ProjectPhasePlan']['project_part_id']) ? ' (' .$projectParts[$projectPhasePlan['ProjectPhasePlan']['project_part_id']]. ')' : '';
            $phasePlans[$key]['ProjectPhase']['name']                       = !empty($projectPhases[$projectPhasePlan['ProjectPhase']['id']]) ? $projectPhases[$projectPhasePlan['ProjectPhase']['id']] : '';
            $phasePlans[$key]['ProjectPhase']['project_part_id']            = $projectPhasePlan['ProjectPhasePlan']['project_part_id'];
            $phasePlans[$key]['ProjectPhase']['phase_planed_start_date']    = $projectPhasePlan['ProjectPhasePlan']['phase_planed_start_date'];
            $phasePlans[$key]['ProjectPhase']['phase_planed_end_date']      = $projectPhasePlan['ProjectPhasePlan']['phase_planed_end_date'];
            $phasePlans[$key]['ProjectPhase']['phase_real_start_date']      = $projectPhasePlan['ProjectPhasePlan']['phase_real_start_date'];
            $phasePlans[$key]['ProjectPhase']['phase_real_end_date']        = $projectPhasePlan['ProjectPhasePlan']['phase_real_end_date'];
        }
        $this->loadModel('Project');
        $project = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('activity_id')
            ));
        $_activityIdProject = $project['Project']['activity_id'];

        if(isset($_activityIdProject) && $_activityIdProject != '' && !empty($_activityIdProject) && $_activityIdProject !=0){
            $this->loadModel('ActivityRequest');
            $this->loadModel('ActivityTask');
            $activityRequests = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'SUM(value) as consumed',
                    'count(id) as records'
                ),
                'conditions' => array(
                    'activity_id' => $_activityIdProject,
                    'company_id' => $projectName['Project']['company_id'],
                    'status' => 2,
                    'NOT' => array('value' => 0)
                )
            ));
            if( $activityRequests[0][0]['records'] ){
                $activityPlans = array();
                foreach($activityRequests as $activityRequest){
                    $activityPlans['ProjectPhase']['id'] = '999999999';
                    $activityPlans['ProjectPhase']['id_activity'] = $_activityIdProject;
                    $activityPlans['ProjectPhase']['name'] = 'Previous Tasks';
                    $activityPlans['ProjectPhase']['estimated'] = $activityRequest[0]['consumed'];
                    $activityPlans['ProjectPhase']['initial_estimated'] = $activityRequest[0]['consumed'];
                    $activityPlans['ProjectPhase']['consumed'] = $activityRequest[0]['consumed'];
                    $activityPlans['ProjectPhase']['is_activity'] = 'true';
                }

                $data['name'] = $activityPlans['ProjectPhase']['name'];
                //$data['estimated'] = $activityPlans['ProjectPhase']['estimated'];
                $data['consumed'] = $activityPlans['ProjectPhase']['consumed'];
                $data['activity_id'] = $_activityIdProject;
                $data['previous'] = $project_id;
                if(!empty($data)){
                    $check = $this->ActivityTask->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('previous' => $project_id, 'activity_id' => $_activityIdProject)
                    ));
                    if(!empty($check)) {
                        //do nothing
                    } else {
                        $this->ActivityTask->create();
                        $this->ActivityTask->save($data);
                    }
                }
                $phasePlans[] = $activityPlans;
            }
        }
        return $phasePlans;
    }

    /**
     * Function:
     * @var     :
     * @return  :
     * @author : BANGVN
     * HuuPc da check
     * */
    private function _getAllProjectTasks($project_id) {
        $this->ProjectTask->Behaviors->attach('Containable');
        $this->ProjectTask->cacheQueries = true;
        //update weight for task
        $db = ConnectionManager::getDataSource('default');
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'id',
                'task_title',
                'parent_id',
                'project_planed_phase_id',
                'task_priority_id',
                'task_status_id',
                'task_assign_to',
                'task_completed',
                'task_start_date',
                'task_end_date',
                'task_real_end_date',
                'estimated',
                'overload',
                'predecessor',
                'duration',
                'weight',
                'special',
                'special_consumed',
                'initial_estimated',
                'initial_task_start_date',
                'initial_task_end_date',
                'is_nct',
                'profile_id',
                'manual_consumed',
                'manual_overload',
                'amount',
                'progress_order',
                'text_1',
                'attachment',
                'text_updater',
                'text_time',
                'slider',
                'unit_price',
                'milestone_id'
            ),
            'recursive' => -1,
            "conditions" => array('project_id' => $project_id),
            'order' => array('weight ASC')
        ));
		
		
		$list_task = !empty($projectTasks) ? ( Set::extract( $projectTasks, '{n}.ProjectTask.id') ) : array();
		$this->loadModel('ProjectTaskTxt');
		$projectTaskTxts = $this->ProjectTaskTxt->find('all', array(
				'recursive' => -1,
                'conditions' => array('project_task_id' => $list_task),
				'joins' => array(array(
					'table' => 'employees',
					'alias' => 'Employee',
					'type' => 'left',
					'conditions' => array(
						'ProjectTaskTxt.employee_id = Employee.id',
					)
				)),
				'fields' => array('ProjectTaskTxt.id','ProjectTaskTxt.project_task_id','ProjectTaskTxt.comment', 'Employee.first_name', 'Employee.last_name', 'ProjectTaskTxt.created')
		));
		$index = 0;
		foreach( $projectTasks as $projectTask){
			foreach( $projectTaskTxts as $taskTxt){
				if( $projectTask['ProjectTask']['id'] == $taskTxt['ProjectTaskTxt']['project_task_id']) {
					$new_update = strtotime($taskTxt['ProjectTaskTxt']['created']);
					$old_update = strtotime( !empty($projectTask['ProjectTask']['text_time']) ? $projectTask['ProjectTask']['text_time'] : 0 );
					if( $new_update > $old_update){
						$projectTasks[$index]['ProjectTask']['text_1'] = $taskTxt['ProjectTaskTxt']['comment'];
						$projectTasks[$index]['ProjectTask']['text_time'] = $taskTxt['ProjectTaskTxt']['created'];
						$projectTasks[$index]['ProjectTask']['text_updater'] = $taskTxt['Employee']['first_name'] . ' ' . $taskTxt['Employee']['last_name'] ;
					}
				}
			}
			$index++;
		}

        // foreach($listPhaseOfProject as $value1)
        // {
        //  $sql = "SET @i = 0;";
        //  $datas = $db->query($sql);
        //  $sql = "UPDATE `project_tasks` SET `weight` = @i:=@i+1 WHERE `project_planed_phase_id` = '".$value1['ProjectTask']['project_planed_phase_id']."' AND `parent_id` = 0 ORDER BY `weight` ASC";
        //  $datas = $db->query($sql);

        //  $listTaskOfPhase = $this->ProjectTask->find('all', array(
        //      'recursive' => -1,
        //      'fields' => array('ProjectTask.id'),
        //      'conditions' => array('ProjectTask.parent_id' => 0, 'ProjectTask.project_planed_phase_id' => $value1['ProjectTask']['project_planed_phase_id'])
        //  ));

        //  foreach($listTaskOfPhase as $value2)
        //  {
        //      $sql1 = "SET @j = 0;";
        //      $datas = $db->query($sql1);
        //      $sql1 = "UPDATE `project_tasks` SET `weight` = @j:=@j+1 WHERE `parent_id` = '".$value2['ProjectTask']['id']."' ORDER BY `weight` ASC";
        //      $datas = $db->query($sql1);
        //  }
        // }

        //END
        return $projectTasks;
    }

    /**
     * Function: _getAllProjectTasksByPhasePlan
     * @var: int $project_id
     * @var: int $project_planed_phase_id
     * @return: array Tasks in phase
     */
    private function _getPhaseScope($project_id, $project_planed_phase_id) {
        $this->ProjectTask->Behaviors->attach('Containable');
        $this->ProjectTask->cacheQueries = true;

        $conditions['OR'] = array(
            array('parent_id' => array(0)),
            array('parent_id' => null)
        );
        //'fields' => array('MAX(Yacht.price) as max_price', 'MIN(Yacht.price) as min_price', ...)
        $projectTasks = $this->ProjectTask->find("all", array(
            'fields' => array(
                'id',
                'task_title',
                'parent_id',
                'project_planed_phase_id',
                'task_priority_id',
                'task_status_id',
                'milestone_id',
                'task_assign_to',
                'task_completed',
                'task_start_date',
                'task_end_date',
                'task_real_end_date',
                'estimated',
                'MIN(task_start_date)',
                'MAX(task_end_date)'
                ),
            'recursive' => -1,
            "conditions" => array(
                'project_id' => $project_id,
                'project_planed_phase_id' => $project_planed_phase_id,
                'OR' => array(
                    'parent_id' => null,
                    'parent_id' => 0,
                ),
                'NOT' => array(
                    'task_start_date' => '0000-00-00',
                    'task_end_date' => '0000-00-00'
                )
            )
        ));
        return $projectTasks[0][0];
    }

    /**
     *
     * @var     : int $project_id
     * @return  :
     * @author : HUUPC
     */
    private function _updateParentTask($project_id) {
        $this->loadModel('ProjectTask');
        $caculates = $this->ProjectTask->find('all', array(
                'fields' => array(
                    'id',
                    'task_title',
                    'project_planed_phase_id',
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
                    'project_id' => $project_id,
                    'NOT' => array('task_start_date' => '0000-00-00', 'task_end_date' => '0000-00-00')
                ),
                'group' => array('parent_id', 'project_planed_phase_id')
            ));
        foreach($caculates as $caculate){
            if($caculate['ProjectTask']['parent_id'] != 0 && $caculate['ProjectTask']['parent_id'] < 999999999){
                $this->ProjectTask->id = $caculate['ProjectTask']['parent_id'];
                if(empty($caculate[0]['Esti'])){
                    $caculate[0]['Esti'] = 0;
                }
                $this->ProjectTask->saveField('estimated', $caculate[0]['Esti']);
                $this->ProjectTask->saveField('task_end_date', $caculate[0]['max_date']);
                $this->ProjectTask->saveField('task_start_date', $caculate[0]['min_date']);
                $this->ProjectTask->saveField('overload', $caculate[0]['s_over']);
                $this->ProjectTask->saveField('manual_overload', $caculate[0]['mover']);
                $this->ProjectTask->saveField('profile_id', 0);
                $this->ProjectTask->saveField('amount', 0);
                $this->ProjectTask->saveField('progress_order', 0);
                $this->ProjectTask->saveField('duration', $this->getWorkingDays($caculate[0]['min_date'], $caculate[0]['max_date'], ''));
            }
        }
    }

    /**
     *  caculate total estimated all project task of phase
     * @var     : int $project_id
     * @return  :
     * @author : HUUPC
     */
    private function _caculateEstimated($project_id) {
        $this->loadModel('ProjectTask');
        $_caculate = array();
        $caculates = $this->ProjectTask->find('all', array(
                'fields' => array(
                    'id',
                    'project_planed_phase_id',
                    'parent_id',
                    'estimated',
                    'SUM(estimated) as Esti'
                ),
                'recursive' => -1,
                "conditions" => array(
                    'project_id' => $project_id,
                    'parent_id' => array(0, null)
                ),
                'group' => array('project_planed_phase_id')
            ));

        foreach($caculates as $key => $caculate){
            $_caculate[$key]['ProjectTask']['id'] = $caculate['ProjectTask']['id'];
            $_caculate[$key]['ProjectTask']['project_planed_phase_id'] = $caculate['ProjectTask']['project_planed_phase_id'];
            $_caculate[$key]['ProjectTask']['Esti'] = $caculate[0]['Esti'];
        }

        return $_caculate;
    }

    /**
     * Function: Get Employees with in current Project
     * @var     : int $project_id
     * @return  : list of Employees and profit center not employee in project teams
     * @author : HUUPC
     */
    private function _getReferencedEmployees($project_id) {
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $this->ProjectTask->Behaviors->attach('Containable');
        $this->ProjectTask->cacheQueries = true;

        // List all employees followed in this project
        $employees = $this->ProjectTeam->find('all', array(
            'fields' => array('id', 'profit_center_id'),
            'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id')),
            'conditions' => array('project_id' => $project_id)));

        $employees = array_filter(Set::classicExtract($employees, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id'));
        if(!empty($employees)){
            foreach($employees as $employee){
                foreach($employee as $v){
                    $_employees[] = $v;
                }
            }
            $employees = array_unique($_employees);
        }
        $employees = $this->ProjectTask->Employee->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'recursive' => -1,
            'conditions' => array(
                'id' => array_merge($employees, array($projectName['Project']['project_manager_id'])),
				'Employee.last_name NOT' => 'NULL'
            ),
            'fields' => array('first_name', 'last_name', 'id')
        ));
        foreach($employees as $k => $v){
            $employees[$k]['Employee']['name'] = $v['Employee']['first_name'] .' '. $v['Employee']['last_name'];
            $employees[$k]['Employee']['is_profit_center'] = 0;
            unset($employees[$k]['Employee']['first_name']);
            unset($employees[$k]['Employee']['last_name']);
        }
        $employees = Set::combine($employees, '{n}.Employee.id', '{n}.Employee');
        if(!empty($projectName['Project']['project_manager_id'])){
            $employees = array($projectName['Project']['project_manager_id'] =>
                $employees[$projectName['Project']['project_manager_id']]) + $employees;
        }
        // select profit center
        $projectTeams = $this->ProjectTeam->find("all", array(
            'fields' => array('id', 'price_by_date', 'work_expected', 'project_function_id', 'profit_center_id'),
            'contain' => array(
                'ProjectFunctionEmployeeRefer' => array(
                    'fields' => array('is_backup', 'profit_center_id', 'employee_id')
            )),
            "conditions" => array('project_id' => $project_id)
        ));

        foreach ($projectTeams as $projectTeam) {
            if (empty($projectTeam['ProjectFunctionEmployeeRefer'])) {
                $profitCenterId[] = $projectTeam['ProjectTeam']['profit_center_id'];
            }
        }
        if (!empty($profitCenterId)) {
            $profitCenters = $this->ProjectTeam->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $projectName['Project']['company_id'], 'ProfitCenter.id' => $profitCenterId)
            ));
            $_profit = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $_profit[$ks]['id'] = $ks;
                $_profit[$ks]['name'] = 'PC / ' . $profitCenter;
                $_profit[$ks]['is_profit_center'] = 1;
            }
            if(isset($profitCenters)){
               $employees = array_merge($employees, $_profit);
            }
        }

        return $employees;
    }

    /**
     * Function: Get Employees with in current Project
     * @var     : int $project_id
     * @return  : array of Employees
     * @author : BANGVN
     */
    private function _getTeamEmployees($project_id ) {
        $this->loadModel('ProjectTeam');
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $this->ProjectTeam->Behaviors->attach('Containable');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('Menu');
        $company_id = $this->employee_info['Company']['id'];
        // lay menu project team. Neu not display thi cho hien thi all employee.
        $checkDisplayProjectTeam = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'controllers' => 'project_teams',
                'functions' => 'index',
                'widget_id' => 'team',
                'model' => 'project',
                // 'display' => 1
            ),
            'order' => array('id DESC')
        ));
        if(!empty($checkDisplayProjectTeam) && $checkDisplayProjectTeam['Menu']['display'] == 1){
            // List all employees followed in this project
            $teams = $this->ProjectTeam->find('all', array(
                'fields' => array('id', 'profit_center_id'),
                'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id', 'profit_center_id')),
                'conditions' => array('project_id' => $project_id)
            ));
            $project = $this->_getProject($project_id);
            $employeeIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id')) : array();
            if(!empty($employeeIds)){
                foreach($employeeIds as $employeeId){
                    foreach($employeeId as $v){
                        $_employeeId[] = $v;
                    }
                }
                $employeeIds = array_unique($_employeeId);
            }
            $employees = $this->ProjectTask->Employee->find('list', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_merge($employeeIds, array($project['Project']['project_manager_id'])),
                    'NOT' => array('is_sas' => 1),
                ),
                'fields' => array('id', 'fullname')
            ));
            $rDatas = array();
            if(!empty($employees)){
                $i = 0;
                foreach($employees as $id => $name){
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($name) ? $name : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $i++;
                }
            }
            $getEmploy = !empty($rDatas) ? $rDatas : array();
            $profitCenterIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.profit_center_id')) : array();
            if(!empty($profitCenterIds)){
                foreach($profitCenterIds as $profitCenterId){
                    foreach($profitCenterId as $v){
                        $_profitCenterId[] = $v;
                    }
                }
                $profitCenterIds = array_unique($_profitCenterId);
            }
            if (!empty($profitCenterId)) {
                $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'order' => array('ProfitCenter.name' => 'asc'),
                    'conditions' => array(
                        'company_id' => $projectName['Project']['company_id'],
                        'ProfitCenter.id' => $profitCenterIds
                    )
                ));
                $employ = array();
                foreach ($profitCenters as $ks => $profitCenter) {
                    $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                    $employ[$ks]['Employee']['is_profit_center'] = 1;
                    //$employ[$ks]['Employee']['profit_center_id'] = -1;
                    $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                }
                if(!empty($employ)){
                    $employees = array_merge($getEmploy , $employ);
                } else {
                    $employees = $getEmploy;
                }
            } else {
                $employees = $getEmploy;
            }
        } else {
            $curentDate = date('Y-m-d');
			$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$curentDate . '"), 1, 0)';
            $employees = $this->ProjectTask->Employee->find('all', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('is_sas' => 1),
                    'company_id' => $company_id
                ),
                 'fields' => array('id', 'fullname','actif', 'available')
            ));
			
            $rDatas = array();
            if (!empty($employees)) {
                $i = 0;
                foreach($employees as $emp){
					$id = $emp['Employee']['id'];
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($emp['Employee']['fullname']) ? $emp['Employee']['fullname'] : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $rDatas[$i]['Employee']['actif'] = intval($emp['Employee']['actif']) ? intval($emp['Employee']['available']) : 0;
                    $i++;
                }
            }
            $employees = $rDatas;
            // lay team
            $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                'recursive' => -1,
                'order' => array('ProfitCenter.name' => 'asc'),
                'conditions' => array(
                    'company_id' => $projectName['Project']['company_id']
                )
            ));
            $employ = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $employ[$ks]['Employee']['is_profit_center'] = 1;
                //$employ[$ks]['Employee']['profit_center_id'] = -1;
                $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
            }
            if( !empty($employ) ){
                $employees = array_merge($employees , $employ);
            }
        }
        return $employees;

    }
	/**
     * Function: Get Employees And PC assigned
     * @var     : int $project_id
     * @return  : array of Employees
     * @author : Huynh
	 * output format array:
	  [index] => Array
        (
            [Employee] => Array
                (
                    [id] => 807
                    [is_profit_center] => 1
                    [name] => PC / AESIO
                )
        )
     */
    private function _getTeamEmployeesAssigned($project_id ) {
        $this->loadModel('ProjectTaskEmployeeRefer', 'ProfitCenter');
        $company_id = $this->employee_info['Company']['id'];
        $taskIds = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array('project_id' => $project_id),
			'fields' => array('id','id')
		));
		$listAssigned = $this->ProjectTaskEmployeeRefer->find('list', array(
			'recursive' => -1,
			'conditions' => array('project_task_id' => $taskIds),
			'fields' => array('id','reference_id', 'is_profit_center')
		));
		if( !empty( $listAssigned['0']))
			$employees = $this->ProjectTask->Employee->find('list', array(
				'order' => array('Employee.fullname' => 'asc'),
				'recursive' => -1,
				'conditions' => array(
					'id' => array_values($listAssigned['0']), // list employees : is_profit_center = 0
					'NOT' => array('is_sas' => 1),
				),
				'fields' => array('id', 'fullname')
			));
		if( !empty( $listAssigned['1']))
			$profitCenters = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'order' => array('ProfitCenter.name' => 'asc'),
				'conditions' => array(
					'company_id' => $company_id,
					'ProfitCenter.id' => array_values($listAssigned['1']), // list profitCenters
				),
				'fields' => array('id', 'name')
			));
		$rDatas = array();
		if(!empty($employees)){
			foreach($employees as $id => $name){
				$rDatas[] = array(
					'Employee' => array(
						'id' => $id,
						'name' => $name,
						'is_profit_center' => 0,
					)
				);
			}
		}
		if(!empty($profitCenters)){
			foreach ($profitCenters as $id => $name) {
				$rDatas[] = array(
					'Employee' => array(
						'id' => $id,
						'name' => 'PC / ' . $name,
						'is_profit_center' => 1,
					)
				);
			}
		}
		return $rDatas;

    }

    /**
     * Activity list asssign
     */
    private function _getProfitCenterAndEmployee($activity_id){
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('Employee');
        $employeeName = $this->_getEmpoyee();
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
            if(!empty($employeeRefers)){
                $employeeRefers = array_unique($employeeRefers);
                $employees = $this->Employee->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Employee.id' => $employeeRefers,
                        'NOT' => array('is_sas' => 1),
                        'OR' => array(
                            array('end_date' => '0000-00-00'),
                            array('end_date IS NULL'),
                            array('end_date >=' => date('Y-m-d', time())),
                        )
                    ),
                    'fields' => array('id', 'fullname'),
                    'order' => array(
                        'fullname' => 'ASC'
                    )
                ));
                foreach ($employees as $k => $employee) {
                    $_employee[$k]['Employee']['id'] = $employee['Employee']['id'];
                    $_employee[$k]['Employee']['is_profit_center'] = 0;
                    $_employee[$k]['Employee']['name'] = $employee['Employee']['fullname'];
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
            $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('is_sas' => 1),
                    'OR' => array(
                        array('end_date' => '0000-00-00'),
                        array('end_date IS NULL'),
                        array('end_date >=' => date('Y-m-d', time())),
                    )
                ),
                'fields' => array('id', 'fullname')
            ));
            foreach ($employees as $k => $employee) {
                $_employee[$k]['Employee']['id'] = $employee['Employee']['id'];
                $_employee[$k]['Employee']['is_profit_center'] = 0;
                $_employee[$k]['Employee']['name'] = $employee['Employee']['fullname'];
            }
            $datas = array_merge($_employee, $_profit );
        }
        return $datas;

    }

  /**
     * Function: Get Employees with in current Project
     * @var     : int $project_id\
     * @var     : int $task_id
     * @return  : array of Employees
     * @author : Thach
     */
    private function _getTeamEmployeesForLoad($project_id, $task_id, $start = null, $end = null) {
        $this->loadModel('ProjectTeam');
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $this->ProjectTeam->Behaviors->attach('Containable');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('Menu');
        $listAssiged = array();
        if( is_numeric($task_id) ) {
            $listAssiged = $this->ProjectTaskEmployeeRefer->find('list',array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $task_id),
                'fields' => array('reference_id','id')
            ));
        }
        $company_id = isset( $this->employee_info['Company']['id'] ) ? $this->employee_info['Company']['id'] : '';
        // lay menu project team. Neu not display thi cho hien thi all employee.
        $checkDisplayProjectTeam = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'controllers' => 'project_teams',
                'functions' => 'index',
                'widget_id' => 'team',
                'model' => 'project',
                // 'display' => 1
            ),
            'order' => array('id DESC')
        ));
        if(!empty($checkDisplayProjectTeam) && $checkDisplayProjectTeam['Menu']['display'] == 1){
            // List all employees followed in this project
            $teams = $this->ProjectTeam->find('all', array(
                'fields' => array('id', 'profit_center_id'),
                'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id', 'profit_center_id')),
                'conditions' => array('project_id' => $project_id)
            ));
            $project = $this->_getProject($project_id);
            $employeeIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id')) : array();
            if(!empty($employeeIds)){
                foreach($employeeIds as $employeeId){
                    foreach($employeeId as $v){
                        $_employeeId[] = $v;
                    }
                }
                $employeeIds = array_unique($_employeeId);
            }
            if($start != -1 && $end != -1){
                $conditions = array(
                    'id' => array_merge($employeeIds, array($project['Project']['project_manager_id'])),
                    'NOT' => array('is_sas' => 1),
                    // 'OR' => array(
                    //     array('end_date' => '0000-00-00'),
                    //     array('end_date IS NULL'),
                    //     //array('end_date >=' => date('Y-m-d', time())),
                    //     'AND' => array(
                    //             'Employee.start_date <=' => $end,
                    //             'Employee.end_date >=' => $start,
                    //     )
                    // )
                );
            } else {
                $conditions = array(
                    'id' => array_merge($employeeIds, array($project['Project']['project_manager_id'])),
                    'NOT' => array('is_sas' => 1),
                    'OR' => array(
                        array('end_date' => '0000-00-00'),
                        array('end_date IS NULL'),
                        array('end_date >=' => date('Y-m-d')),
                    )
                );
            }
            $this->ProjectTask->Employee->virtualFields['available'] = 'IF((' . $start . ' = -1 AND ' . $end . ' = -1) OR (end_date IS NULL OR end_date = "0000-00-00" OR (start_date <= "' . $end . '" AND end_date >= "' .$start . '")), 1, 0)';
            $employees = $this->ProjectTask->Employee->find('all', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('id', 'fullname','actif', 'available')
            ));
            $employees = Set::combine($employees,'{n}.Employee.id','{n}.Employee');
            $rDatas = array();
            if(!empty($employees)){
                $i = 0;
                foreach($employees as $id => $emp){
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($emp['fullname']) ? $emp['fullname'] : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;

                    $rDatas[$i]['Employee']['actif'] = intval($emp['actif']) ? intval($emp['available']) : 0;

                    if( !empty($listAssiged[$id]) ){
                        $rDatas[$i]['Employee']['is_selected'] = 1;
                    } else {
                        $rDatas[$i]['Employee']['is_selected'] = 0;
                    }

                    $i++;
                }
            }
            $getEmploy = !empty($rDatas) ? $rDatas : array();
            $profitCenterIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.profit_center_id')) : array();
            if(!empty($profitCenterIds)){
                foreach($profitCenterIds as $profitCenterId){
                    foreach($profitCenterId as $v){
                        $_profitCenterId[] = $v;
                    }
                }
                $profitCenterIds = array_unique($_profitCenterId);
            }
            if (!empty($profitCenterId)) {
                $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'order' => array('ProfitCenter.name' => 'asc'),
                    'conditions' => array(
                        'company_id' => $projectName['Project']['company_id'],
                        'ProfitCenter.id' => $profitCenterIds
                    )
                ));
                $employ = array();
                foreach ($profitCenters as $ks => $profitCenter) {
                    $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                    $employ[$ks]['Employee']['is_profit_center'] = 1;
                    $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                    $employ[$ks]['Employee']['actif'] = 1;
                    if( !empty($listAssiged[$profitCenter['ProfitCenter']['id']]) ){
                        $employ[$ks]['Employee']['is_selected'] = 1;
                    } else {
                        $employ[$ks]['Employee']['is_selected'] = 0;
                    }
                }

                if( !empty($employ) ){
                    $employees = array_merge($getEmploy , $employ);
                } else {
                    $employees = $getEmploy;
                }
            } else {
                $employees = $getEmploy;
            }
        } else {
            $this->ProjectTask->Employee->virtualFields['available'] = 'IF((' . $start . ' = -1 AND ' . $end . ' = -1) OR (end_date IS NULL OR end_date = "0000-00-00" OR (start_date <= "' . $end . '" AND end_date >= "' .$start . '")), 1, 0)';
            $employees = $this->ProjectTask->Employee->find('all', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('is_sas' => 1),
                    // 'OR' => array(
                        // array('end_date' => '0000-00-00'),
                        // array('end_date IS NULL'),
                        // array('end_date >=' => date('Y-m-d')),
                    // ),
                    'actif' => 1,
                    'company_id' => $company_id
                ),
                'fields' => array('id', 'fullname','actif', 'available')
            ));
            $rDatas = array();
            if(!empty($employees)){
                $i = 0;
                foreach($employees as $emp){
                    $id = $emp['Employee']['id'];
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($emp['Employee']['fullname']) ? $emp['Employee']['fullname'] : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;

                    $rDatas[$i]['Employee']['actif'] = intval($emp['Employee']['actif']) ? intval($emp['Employee']['available']) : 0;

                    if( !empty($listAssiged[$id]) ){
                        $rDatas[$i]['Employee']['is_selected'] = 1;
                    } else {
                        $rDatas[$i]['Employee']['is_selected'] = 0;
                    }

                    $i++;
                }
            }
            $employees = $rDatas;
            // lay team
            $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                'recursive' => -1,
                'order' => array('ProfitCenter.name' => 'asc'),
                'conditions' => array(
                    'company_id' => $projectName['Project']['company_id']
                )
            ));
            $employ = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $employ[$ks]['Employee']['is_profit_center'] = 1;
                $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                $employ[$ks]['Employee']['actif'] = 1;
                if( !empty($listAssiged[$profitCenter['ProfitCenter']['id']]) ){
                    $employ[$ks]['Employee']['is_selected'] = 1;
                } else {
                    $employ[$ks]['Employee']['is_selected'] = 0;
                }
            }
            if( !empty($employ) ){
                $employees = array_merge($employees , $employ);
            }
        }
        return $employees;

    }
    /**
     * Function: Get activity
     * @var     : $project_id
     * @return  : activity object
     * @author : BANGVN
     **/
    private function _getActivity($project_id){
        if( isset($this->_activity) ){

        } else {
            $this->loadModel('Activity');
            $activity = ClassRegistry::init('Activity')->find('all',array(
                'recursive' => -1,
                'conditions' => array('Activity.project' => $project_id),
            ));
            $this->_activity = $activity;
        }

        return $this->_activity;
    }

    /**
     * Function: Get Activity Requests related to current project
     * @var     : int $project_id
     * @return  : array of Activity Requests
     * @author : HUUPC
     * @comment: need to invent the single ton for the current project.
     * HuuPc da check
     * */
    private function _getActivityRequests($project_id = null, $activityTasks = null) {
        $this->loadModel('ActivityRequest');
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                'SUM(CASE WHEN `status` = 2 THEN `value` ELSE 0 END) AS valid',
                'SUM(CASE WHEN `status` = -1 OR `status` = 0 OR `status` = 1 THEN `value` ELSE 0 END) AS wait',
                'COUNT(task_id) AS hasUsed'
            ),
            'group' => array('task_id'),
            'conditions' => array(
                'task_id' => $activityTasks,
            )
        ));
        $activityRequests = !empty($activityRequests) ? Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0') : array();
        return $activityRequests;
    }

    private function _getActivityTasksByActivityIdAndProjectTaskId($activity_id, $project_task_id){
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'conditions' => array(
                'NOT' => array("project_task_id" => null),
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id,
            )
        ));
        return $activityTasks;
    }

    private function _getActivityTasksByActivityIdAndProjectTaskIdPart2($activityIds, $taskIds){
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find(
            'first', array(
            'conditions' => array(
                'NOT' => array("project_task_id" => null),
                'activity_id' => $activityIds,
                'project_task_id' => $taskIds,
            ),
            'fields' => array('id')
        ));
        return $activityTasks;
    }

    /**
     * Function: Get all activity tasks by activity id
     * @var     :
     * @return  :
     * @author : BANGVN
     **/
    private function _getActivityTasksByActivityId($activity_id){
        if ($this->_activityTasksById == null) {
            $this->loadModel('ActivityTask');
            $activityTasks = $this->ActivityTask->find('all', array(
                'conditions' => array(
                    'NOT' => array("project_task_id" => null),
                    'activity_id' => $activity_id,
                )
            ));
            $this->_activityTasksById = $activityTasks;
        } else {
        }
        return $this->_activityTasksById;
    }

    /**
     * Function: Get All Activity Tasks
     * @var     : none
     * @return  : array of Activity Tasks
     * @author : HUUPC
     * HuuPc da check
     * */
    private function _getActivityTasks($listIdOfProjectTasks = null) {
        if ($this->_activityTasks == null) {
            $this->loadModel('ActivityTask');
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array("project_task_id" => $listIdOfProjectTasks),
                'fields' => array('project_task_id', 'id')
            ));
            $this->_activityTasks = $activityTasks;
        }
        return $this->_activityTasks;
    }

    /**
     * Function: Build Phase Plans Structure for the response
     * @var     : array Project Phase Plans
     * @return  : array of Project Phase Plans Structured
     * @author : HUUPC
     * */
    private function _attachPhasePartStructure($project_id) {
        $this->loadModel('ProjectPart');
        $this->loadModel('ProjectPhasePlan');
        $projectParts = $this->ProjectPart->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'title'),
            'order' => array('weight' => 'ASC')
        ));
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'conditions' => array(
                    'ProjectPhasePlan.project_id' => $project_id,
                    'NOT' => array(
                            'phase_real_start_date' => '0000-00-00',
                            'phase_real_end_date' => '0000-00-00',
                            'project_part_id' => null
                        )
                ),
            'fields' => array(
                    'MIN(ProjectPhasePlan.phase_real_start_date) as min_date',
                    'MAX(ProjectPhasePlan.phase_real_end_date) as max_date',
                    '*'
                ),
            'group' => array('ProjectPhasePlan.project_part_id')
        ));
        $projectPhasePlans = Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.project_part_id', '{n}.0');
        $children = array();
        foreach ($projectParts as $key => $projectPart) {
            $children[$key]['parent_id'] = 0;
            // simulate task id of ProjectPart = 999.999.999 + ProjectPart id
            $children[$key]['id']               = 999999999 + $projectPart['ProjectPart']['id'];
            $children[$key]['task_title']       = isset($projectPart['ProjectPart']['title']) ? $projectPart['ProjectPart']['title'] : '';
            $children[$key]['task_start_date']  = isset($projectPhasePlans[$projectPart['ProjectPart']['id']]) ? $projectPhasePlans[$projectPart['ProjectPart']['id']]['min_date'] : 0;
            $children[$key]['task_end_date']    = isset($projectPhasePlans[$projectPart['ProjectPart']['id']]) ? $projectPhasePlans[$projectPart['ProjectPart']['id']]['max_date'] : 0;
            $children[$key]['initial_task_start_date']  = isset($projectPhasePlans[$projectPart['ProjectPart']['id']]) ? $projectPhasePlans[$projectPart['ProjectPart']['id']]['min_date'] : 0;
            $children[$key]['initial_task_end_date']    = isset($projectPhasePlans[$projectPart['ProjectPart']['id']]) ? $projectPhasePlans[$projectPart['ProjectPart']['id']]['max_date'] : 0;
            $children[$key]['duration']         = $this->getWorkingDays($children[$key]['task_start_date'], $children[$key]['task_end_date'], '');
            $children[$key]['estimated']        = 0;
            $children[$key]['initial_estimated']        = 0;
            $children[$key]['consumed']         = 0;
            $children[$key]['wait']             = 0;
            $children[$key]['completed']        = 0;
            $children[$key]['remain']           = 0;
            $children[$key]['manual_consumed']           = 0;
            $children[$key]['manual_overload']           = 0;
            $children[$key]['leaf']             = 'false';
            $children[$key]['expanded']         = true;
            $children[$key]['children']         = array();
            $children[$key]['is_part']          = 'true';
        }
        return $children;
    }

    /**
     * Function: Build Phase Plans Structure for the response
     * @var     : array Project Phase Plans
     * @return  : array of Project Phase Plans Structured
     * @author : HUUPC
     * */
    private function _attachPhasePlansStructure($project_id) {
        $projectPhasePlans = $this->_getAllPojectPhasePlans($project_id);
        $_sumEstimates = $this->_caculateEstimated($project_id);
        $history = $this->getHistory($project_id);
        $rows = isset($history['rows']) ? $history['rows'] : array();
        $children = array();
        foreach ($projectPhasePlans as $key => $projectPhasePlan) {
            $children[$key]['parent_id'] = isset($projectPhasePlan['ProjectPhase']['project_part_id']) ? $projectPhasePlan['ProjectPhase']['project_part_id'] + 999999999 : 0;
            $children[$key]['phase_id'] = $projectPhasePlan['ProjectPhase']['id'];
            // simulate task id of phase = 999.999.999.999 + phase id
            $children[$key]['id'] = 999999999999 + $projectPhasePlan['ProjectPhase']['id'];
            $children[$key]['task_title'] = isset($projectPhasePlan['ProjectPhase']['name']) ? $projectPhasePlan['ProjectPhase']['name'] : '';
            $children[$key]['project_planed_phase_id'] = 0;
            $children[$key]['project_planed_phase_text'] = 0;
            $children[$key]['task_priority_id'] = 0;
            $children[$key]['task_priority_text'] = 0;
            $children[$key]['task_status_id'] = 0;
            $children[$key]['milestone_id'] = 0;
            $children[$key]['task_status_text'] = 0;
            $children[$key]['task_start_date'] = isset($projectPhasePlan['ProjectPhase']['phase_real_start_date']) ? $projectPhasePlan['ProjectPhase']['phase_real_start_date'] : 0;
            $children[$key]['task_end_date'] = isset($projectPhasePlan['ProjectPhase']['phase_real_end_date']) ? $projectPhasePlan['ProjectPhase']['phase_real_end_date'] : 0;
            $children[$key]['initial_task_start_date'] = isset($projectPhasePlan['ProjectPhase']['phase_real_start_date']) ? $projectPhasePlan['ProjectPhase']['phase_real_start_date'] : 0;
            $children[$key]['initial_task_end_date'] = isset($projectPhasePlan['ProjectPhase']['phase_real_end_date']) ? $projectPhasePlan['ProjectPhase']['phase_real_end_date'] : 0;
            $children[$key]['duration'] = $this->getWorkingDays($children[$key]['task_start_date'], $children[$key]['task_end_date'], '');
            foreach($_sumEstimates as $_sumEstimate){
                if($projectPhasePlan['ProjectPhase']['id'] == $_sumEstimate['ProjectTask']['project_planed_phase_id']){
                    $children[$key]['estimated'] = $_sumEstimate['ProjectTask']['Esti'];
                    break;
                }
            }
            $children[$key]['consumed'] = isset($projectPhasePlan['ProjectPhase']['consumed']) ? $projectPhasePlan['ProjectPhase']['consumed'] : 0;
            //$children[$key]['project_id'] = $project_id;
            $children[$key]['remain'] = 0;
            $children[$key]['wait'] = 0;
            $children[$key]['completed'] = 0;
            $children[$key]['manual_consumed'] = 0;
            $children[$key]['manual_overload'] = 0;
            $children[$key]['estimated'] = isset($projectPhasePlan['ProjectPhase']['estimated']) ? $projectPhasePlan['ProjectPhase']['estimated'] : '';
            if($children[$key]['phase_id'] == '999999999')
            {
                if($children[$key]['estimated'] != 0){
                    $children[$key]['completed'] = '100 %';
                }
            }
            $children[$key]['initial_estimated'] = isset($projectPhasePlan['ProjectPhase']['initial_estimated']) ? $projectPhasePlan['ProjectPhase']['initial_estimated'] : '';
            //$children[$key]['leaf'] = 'true'; //old
            $children[$key]['leaf'] = 'false';
            $children[$key]['children'] = array();
            //END
            $id = (string) $children[$key]['id'];
            $children[$key]['expanded'] = (bool) isset($rows[ $id ]) ? $rows[ $id ] : true;
            $children[$key]['is_phase'] = 'true';
            $children[$key]['is_activity'] = isset($projectPhasePlan['ProjectPhase']['is_activity']) ? 'true' : 'false';
            $children[$key]['id_activity'] = isset($projectPhasePlan['ProjectPhase']['id_activity']) ? $projectPhasePlan['ProjectPhase']['id_activity'] : 0;
        }

        return $children;
    }

    /**
     * Function: Attach Reference Employees to Project Tasks
     * @var     : array of Project Tasks
     * @return  : array of Project Tasks with Employees attached
     * @author : BANGVN
     * */
    private function _attachReferencedEmployees($projectTasks) {
        $this->loadModel('ProjectTaskEmployeeRefer');
        foreach ($projectTasks as $key => $projectTask) {
            $projectTaskId = $projectTask['ProjectTask']['id'];
            $referencedEmployees = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "project_task_id" => $projectTaskId
                )
            ));
            if (count($referencedEmployees) == 0) {

            } else {
                foreach ($referencedEmployees as $key1 => $referencedEmployee) {
                    $projectTasks[$key]['ProjectTask']['ProjectTaskEmployeeRefer'][] = $referencedEmployee['ProjectTaskEmployeeRefer'];
                    $employees = array(
                            'reference_id' => $referencedEmployee['ProjectTaskEmployeeRefer']['reference_id'],
                            'is_profit_center' => $referencedEmployee['ProjectTaskEmployeeRefer']['is_profit_center']
                        );
                    $projectTasks[$key]['ProjectTask']['assigned'][] = $employees;
                }
            }
        }
        return $projectTasks;
    }

    /**
     * Function: Attach Consumed
     * @var     : array $projectTasks
     * @var     : array $activityTask
     * @var     : array $activityRequests
     * @return  : array $projectTasks with consumed attached
     * @author : BANGVN
     * HuuPc da check
     * */
    private function _attachConsumed($projectTasks, $activityTasks, $activityRequests) {
        foreach($projectTasks as $key => $projectTask){
            $projectTaskId = $projectTask['ProjectTask']['id'];
            if($projectTasks[$key]['ProjectTask']['special']==1) {
                $projectTasks[$key]['ProjectTask']['consumed']=$projectTasks[$key]['ProjectTask']['special_consumed'];
            }
            if(isset($activityTasks[$projectTaskId])){
                $activityTaskId = $activityTasks[$projectTaskId];
                if($projectTasks[$key]['ProjectTask']['special']==1) {
                    $projectTasks[$key]['ProjectTask']['consumed']=$projectTasks[$key]['ProjectTask']['special_consumed'];
                } else {
                    $projectTasks[$key]['ProjectTask']['consumed'] = !empty($activityRequests[$activityTaskId]['valid']) ? $activityRequests[$activityTaskId]['valid'] : 0;
                }
                $projectTasks[$key]['ProjectTask']['hasUsed'] = !empty($activityRequests[$activityTaskId]['hasUsed']) ? $activityRequests[$activityTaskId]['hasUsed'] : 0;
                $projectTasks[$key]['ProjectTask']['wait'] = !empty($activityRequests[$activityTaskId]['wait']) ? $activityRequests[$activityTaskId]['wait'] : 0;
            }
			$projectTasks[$key]['ProjectTask']['eac'] = 0;
        }
        return $projectTasks;
    }

    /**
     * Function: Attach Consumed for Parent
     * @var     : array $projectTasks
     * @var     : array $activityTask
     * @var     : array $activityRequests
     * @return  : int $total with consumed attached for Parent
     * @author : HUUPC
     * HuuPc da check
     * */
    private function _attachConsumedForParent($projectTasks, $activityTasks, $activityRequests) {
        $total = 0;
        foreach ($projectTasks as $key => $projectTask) {
            $projectTaskId = 0;
            if ($projectTask['ProjectTask']['parent_id'] != 0 || $projectTask['ProjectTask']['parent_id'] == 0 || $projectTask['ProjectTask']['parent_id'] == null || $projectTask['ProjectTask']['parent_id'] == '') {
                $projectTaskId = $projectTask['ProjectTask']['id'];
            }
            // Check if Activity Task Existed
            if (isset($activityTasks[$projectTaskId])) {
                $activityTaskId = $activityTasks[$projectTaskId];
                $total += !empty($activityRequests[$activityTaskId]['valid']) ? $activityRequests[$activityTaskId]['valid'] : 0;
            }
        }
        return $total;
    }

    /**
     * Function: Attach Estimated for Parent
     * @var     : array $projectTasks
     * @var     : array $activityTask
     * @var     : array $activityRequests
     * @return  : int $total with Estimated attached for Parent
     * @author : HUUPC
     * */
    private function _attachEstimatedForParent($projectTasks) {
        $total = 0;
        $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
        foreach($projectTasks as $key => $projectTask){
            foreach($parentIds as $parentId){
                if($projectTask['ProjectTask']['id'] == $parentId){
                    unset($projectTasks[$key]);
                }
            }
        }
        foreach ($projectTasks as $key => $projectTask) {
            if (is_numeric($projectTask['ProjectTask']['estimated'])) {
                $total += $projectTask['ProjectTask']['estimated'];
            }
        }
        return $total;
    }
     /**
     * Function: Attach Estimated for Parent
     * @var     : array $projectTasks
     * @var     : array $activityTask
     * @var     : array $activityRequests
     * @return  : int $total with Estimated attached for Parent
     * @author : HUUPC
     * */
    private function _attachInitialEstimatedForParent($projectTasks) {
        $total = 0;
        $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
        foreach($projectTasks as $key => $projectTask){
            foreach($parentIds as $parentId){
                if($projectTask['ProjectTask']['id'] == $parentId){
                    unset($projectTasks[$key]);
                }
            }
        }
        foreach ($projectTasks as $key => $projectTask) {
            if (is_numeric($projectTask['ProjectTask']['initial_estimated'])) {
                $total += $projectTask['ProjectTask']['initial_estimated'];
            }
        }
        return $total;
    }
    /**
     *
     * @var     :
     * @return  : start date $startDate
     * @author : HUUPC
     * */
    private function _predecessor($startDate, $predecessor, $projectTaskId){
        if(!empty($predecessor)){
            $datas = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $projectTaskId),
                    'fields' => array('id', 'predecessor', 'parent_id')
                ));
            if(($datas['ProjectTask']['id'] != $predecessor) && ($predecessor != $datas['ProjectTask']['parent_id'])){
                $endDate = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $predecessor),
                    'fields' => array('id', 'predecessor', 'task_start_date', 'task_end_date', 'parent_id')
                ));
                if($endDate['ProjectTask']['parent_id'] != null || $endDate['ProjectTask']['parent_id'] != 0){
                    $idSave = $endDate['ProjectTask']['id'];
                    $getsStart = $this->ProjectTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('ProjectTask.id' => $idSave),
                        'fields' => array('task_end_date')
                    ));
                    $startDate = $getsStart['ProjectTask']['task_end_date'];
                    $startDate = ($startDate != '0000-00-00') ? strtotime($startDate) : 0;
                    if($startDate != 0){
                        $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                        $startDate = date('Y-m-d', $startDate);
                    } else {
                        $startDate = '0000-00-00';
                    }
                }
            }
        } else {
            $predecessor = "";
        }
        return $startDate;
    }

    /**
     *
     * @var     :
     * @return  : start date $startDate
     * @author : HUUPC
     * */
    private function _createPredecessor($startDate, $predecessor){
        $startDate = '';
        if(!empty($predecessor)){
            $datas = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $predecessor),
                'fields' => array('id', 'predecessor', 'parent_id', 'task_end_date')
            ));
            $startDate = $datas['ProjectTask']['task_end_date'];
            if(($datas['ProjectTask']['parent_id'] != null || $datas['ProjectTask']['parent_id'] != 0)){
               $startDate = $datas['ProjectTask']['task_end_date'];
               $startDate = strtotime($startDate);
               $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
               $startDate = date('Y-m-d', $startDate);
            }

        } else {
            $predecessor = "";
        }
        return $startDate;
    }

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
    public function getWorkingDays($startDate, $endDate, $duration){
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
    private function _durationEndDate($startDate, $endDate, $duration){
        $_durationEndDate = '';
        if((strtotime($endDate) == '' || $endDate == '0000-00-00') && !empty($duration)){
            $dates_ranges[]= $startDate;
            $_startDate = strtotime($startDate);
            $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+$duration, date("Y", $_startDate));
            $startDateCheck = strtotime($startDate);
            $_addDates = 0;
            while($startDateCheck <= $_startDate){
                $dates_range = $startDateCheck;
                $_dateFitter = strtolower(date("l", $dates_range));
                if($_dateFitter == 'saturday' || $_dateFitter == 'sunday'){
                    $_addDates++;
                }
                $startDateCheck = mktime(0, 0, 0, date("m", $startDateCheck), date("d", $startDateCheck)+1, date("Y", $startDateCheck));
            }
            if($_addDates <= 1){
                $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)-1, date("Y", $_startDate));
            } else {
                for($i = 1; $i < $_addDates; $i++){
                    $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+1, date("Y", $_startDate));
                    $_Fitter = strtolower(date("l", $_startDate));
                    if($_Fitter == 'saturday' || $_Fitter == 'sunday'){
                        $_addDates++;
                    }
                }
            }
            $_end = date("Y-m-d", $_startDate);
            $_durationEndDate = $_end;
        } else {
            $_durationEndDate = $endDate;
        }
        return $_durationEndDate;
    }

    /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _durationStartDate($startDate, $endDate, $duration){
        $_durationStartDate = '';
        if((strtotime($startDate) == '' || $startDate == '0000-00-00') && !empty($duration)){
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
    private function _updateStartDate($endDate, $projectTaskId){
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectTask.predecessor' => $projectTaskId),
            'fields' => array('id', 'task_start_date', 'task_end_date')
        ));
        foreach($projectTasks as $projectTask){
            $this->ProjectTask->id = $projectTask['ProjectTask']['id'];
            $this->ProjectTask->saveField('task_start_date', $endDate);
            $_duration = $this->getWorkingDays($endDate, $projectTask['ProjectTask']['task_end_date'], null);
            $this->ProjectTask->saveField('duration', $_duration);
        }
    }

     /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _getStartEndDateAllTask($project_id, $taks_id = null) {
        $this->_checkRole(false, $project_id, array(), $taks_id);
        $this->loadModel('ProjectPhasePlan');
        $data = array();
        $projectPlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MIN(phase_real_start_date) AS startDate',
                'MAX(phase_real_end_date) AS endDate'
            )
        ));
        $data['task_start_date'] = isset($projectPlans[0][0]['startDate']) ? $projectPlans[0][0]['startDate'] : 0;
        $data['task_end_date'] = isset($projectPlans[0][0]['endDate']) ? $projectPlans[0][0]['endDate'] : 0;
        $data['initial_task_start_date'] = isset($projectPlans[0][0]['startDate']) ? $projectPlans[0][0]['startDate'] : 0;
        $data['initial_task_end_date'] = isset($projectPlans[0][0]['endDate']) ? $projectPlans[0][0]['endDate'] : 0;
        return $data;
    }

     /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _saveStartEndDateAllTask($project_id, $task_id = null) {
        $this->_checkRole(false, $project_id, array(), $task_id);
        $this->loadModel('Project');
        $data = $this->_getStartEndDateAllTask($project_id, $task_id);
        if(!empty($data)){
            $_data['start_date'] = $data['task_start_date'];
            $_data['end_date'] = $data['task_end_date'];
            $this->Project->id = $project_id;
            $this->Project->save($_data);
        }
    }

    /**
     *
     * @var     :
     * @return  :
     * @author : HUUPC
     * */
    private function _updatePredeces($projectTaskId, $predecessor){
        $_predecessor = 0;
        $datas = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'conditions' => array('ProjectTask.id' => $projectTaskId),
            'fields' => array('id', 'predecessor')
        ));
        if($datas['ProjectTask']['id'] != $predecessor){
            $_predecessor = $predecessor;
        }
        return $_predecessor;
    }

    // ATTACH SECTION - END

     /**
     *
     * @var     :
     * @return  : is Predecessor
     * @author : huythang38
     * */
    private function _isPredecessor($project_id)  {
        $pTasks = $this->ProjectTask->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectTask.predecessor' => $project_id)
        ));
        $bool = 'false';
        if($pTasks != 0){
            $bool = 'true';
        }
        return $bool;
    }

    /**
     * Function: Build Json Structure for the response
     * @var     :
     * @return  : array of Project Tasks Structured
     * @author : BANGVN
     * HuuPc da check
     * */
    private function _buildJsonStructure($projectTasks, $project_id) {
        $useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        $project = $this->_getProject($project_id);

        //$projectPhases = $this->_getPhasePlanCombinedPart($project_id);
        $employees = $this->_getTeamEmployees($project_id);
        $projectPriorities = $this->ProjectTask->ProjectPriority->find('list', array(
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
            ),
            'fields' => array('id', 'priority')
        ));
        $projectStatus = $this->ProjectTask->ProjectStatus->find('all', array(
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
            ),
			'order' => 'weight',
            'fields' => array('id', 'name', 'status')
        ));
        $statusOfProjectStatus = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus.status') : array();
        $projectStatus = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
        // status of company
        $statusOfCompanies = $this->ProjectTask->ProjectStatus->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'name' => array('Ouvert', 'Open'),
                'company_id' => $project['Project']['company_id']
            ),
            'fields' => array('id', 'name')
        ));
        //milestone.
        $this->loadModel('ProjectMilestone');
        $milestoneCompanies = $this->ProjectMilestone->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_milestone')
        ));
        $this->set(compact('projectStatus', 'statusOfCompanies'));
        $profiles = ClassRegistry::init('Profile')->find('list', array(
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
            ),
            'fields' => array('id', 'name')
        ));
        $children = array();
        $listIdOfProjectTasks = array();
        $provider = ClassRegistry::init('BudgetProvider')->find('list',array(
                'fields' => array('id','name'),
            )
        );
        $consumedSpecial = 0;
        $manualConsumed = 0;
        $amount = 0;
        foreach ($projectTasks as $key => $projectTask) {
			// debug($projectTask);
			// exit;
            // danh sach project task id
            $listIdOfProjectTasks[] = !empty($projectTask['ProjectTask']['id']) ? $projectTask['ProjectTask']['id'] : 0;
            $phase_id = $projectTask['ProjectTask']['project_planed_phase_id'];
            $phase_text = '';
            $projectId = $projectTask['ProjectTask']['id'];
            $children[$key]['is_predecessor'] = $this->_isPredecessor($projectId);
            $priority_id = $projectTask['ProjectTask']['task_priority_id'];
            if( $priority_id ){
                $priority_text = isset($projectPriorities[$priority_id]) ? $projectPriorities[$priority_id] : '';
            } else {
                $priority_text = null;
            }
            if(empty($projectTask['ProjectTask']['task_status_id'])){
                $projectTask['ProjectTask']['task_status_id'] = !empty($statusOfCompanies['ProjectStatus']['id']) ? $statusOfCompanies['ProjectStatus']['id'] : '';
            }
            $status_id = $projectTask['ProjectTask']['task_status_id'];
            $milestone_id = !empty($projectTask['ProjectTask']['milestone_id']) ? $projectTask['ProjectTask']['milestone_id'] : 0;
            $statusOfStatus = 'IP';
            if( $status_id ){
                $status_text = isset($projectStatus[$status_id]) ? $projectStatus[$status_id] : '';
                $statusOfStatus = isset($statusOfProjectStatus[$status_id]) ? $statusOfProjectStatus[$status_id] : 'IP';
            } else {
                $status_text = null;
            }
            if( $milestone_id ){
                $milestone_text = isset($milestoneCompanies[$milestone_id]) ? $milestoneCompanies[$milestone_id] : '';
            } else {
                $milestone_text = null;
            }
            $children[$key]['id'] = $projectTask['ProjectTask']['id'];
            $children[$key]['task_title'] = $projectTask['ProjectTask']['task_title'];
            $children[$key]['project_planed_phase_id'] = $phase_id;
            $children[$key]['project_planed_phase_text'] = $phase_text;
            $children[$key]['weight'] = $projectTask['ProjectTask']['weight'];
            $children[$key]['special'] = $projectTask['ProjectTask']['special'];
            if (empty($projectTask['ProjectTask']['parent_id'])) {
                // Simulate task id of Phase. Task id = 999.999.999.999 + phase_id
                $children[$key]['parent_id'] = 999999999999 + $phase_id;
                //$children[$key]['leaf'] = 'true'; //old
                $children[$key]['leaf'] = 'false';
                $children[$key]['children'] = array();
                $children[$key]['expanded'] = true;
            } else {
                $children[$key]['parent_id'] = $projectTask['ProjectTask']['parent_id'];
                $children[$key]['leaf'] = 'true';
            }
            $children[$key]['task_priority_id'] = $priority_id;
            $children[$key]['task_priority_text'] = $priority_text;
            $children[$key]['task_status_id'] = $status_id;
            $children[$key]['task_status_text'] = $status_text;
            $children[$key]['milestone_id'] = $milestone_id;
            $children[$key]['milestone_text'] = $milestone_text;
            $children[$key]['task_status_st'] = $statusOfStatus;
            //profile text
            if( isset($profiles[$projectTask['ProjectTask']['profile_id']]) ){
                $profile_text = $profiles[$projectTask['ProjectTask']['profile_id']];
            } else {
                $profile_text = null;
            }
            $children[$key]['profile_text'] = $profile_text;
            $children[$key]['profile_id'] = $projectTask['ProjectTask']['profile_id'];
            $children[$key]['is_nct'] = isset($projectTask['ProjectTask']['is_nct']) ? $projectTask['ProjectTask']['is_nct'] : 0;
            if($projectTask['ProjectTask']['special'] == 1 && isset($provider[$projectTask['ProjectTask']['task_assign_to']])) {
                $children[$key]['task_assign_to_text'][] = $provider[$projectTask['ProjectTask']['task_assign_to']];
            } else {
                if (!empty($projectTask['ProjectTask']['ProjectTaskEmployeeRefer'])) {
                    unset($children[$key]['task_assign_to_id']);
                    foreach ($projectTask['ProjectTask']['ProjectTaskEmployeeRefer'] as $k => $value) {
                        $children[$key]['task_assign_to_id'][] = $value['reference_id'];
                        $children[$key]['is_profit_center'][] = $value['is_profit_center'] == null ? 0 : $value['is_profit_center'];
                        $children[$key]['estimated_detail'][] = $value['estimated'] == null ? 0 : $value['estimated'];
                        if (isset($value['reference_id'])) {
                            foreach($employees as $employee){
                                if($value['reference_id'] == $employee['Employee']['id'] && $value['is_profit_center'] == $employee['Employee']['is_profit_center']){
                                    if( !isset($children[$key]['task_assign_to_text']) || !in_array($employee['Employee']['name'], $children[$key]['task_assign_to_text']) )$children[$key]['task_assign_to_text'][] = $employee['Employee']['name'];
                                }
                            }
                        }
                    }
                }
            }
            $children[$key]['predecessor'] = $projectTask['ProjectTask']['predecessor'];
            $_startDate = $projectTask['ProjectTask']['task_start_date'];
            $_endDate = $projectTask['ProjectTask']['task_end_date'];
            $_initial_startDate = $projectTask['ProjectTask']['initial_task_start_date'];
            $_initial_endDate = $projectTask['ProjectTask']['initial_task_end_date'];
            $_duration = $projectTask['ProjectTask']['duration'];
            $children[$key]['task_start_date'] = $_startDate;
            $children[$key]['task_end_date'] = $_endDate;
            $children[$key]['initial_task_start_date'] = $_initial_startDate;
            $children[$key]['initial_task_end_date'] = $_initial_endDate;
            if(!empty($_duration)){
                $children[$key]['duration'] = $_duration;
            } else {
                $children[$key]['duration'] = $this->getWorkingDays($_startDate, $_endDate, '');
            }
            $children[$key]['estimated'] = $projectTask['ProjectTask']['estimated'];
            $children[$key]['eac'] = $projectTask['ProjectTask']['eac'];
            $children[$key]['manual_consumed'] = $projectTask['ProjectTask']['manual_consumed'];
            $children[$key]['manual_overload'] = $projectTask['ProjectTask']['manual_overload'];
            $children[$key]['initial_estimated'] = $projectTask['ProjectTask']['initial_estimated'];
            $children[$key]['consumed'] = (isset($projectTask['ProjectTask']['consumed'])) ? $projectTask['ProjectTask']['consumed'] : 0;
            $children[$key]['wait'] = (isset($projectTask['ProjectTask']['wait'])) ? $projectTask['ProjectTask']['wait'] : 0;
            $children[$key]['hasUsed'] = (isset($projectTask['ProjectTask']['hasUsed'])) ? $projectTask['ProjectTask']['hasUsed'] : 0;
            $children[$key]['project_id'] = $project_id;
            if(isset($projectTask['ProjectTask']['special']) && $projectTask['ProjectTask']['special'] == 1) {
                $consumedSpecial += isset($projectTask['ProjectTask']['special_consumed']) ? $projectTask['ProjectTask']['special_consumed'] : 0;
            }
            $_consumed = $_cons = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
            $_manualConsumed = isset($projectTask['ProjectTask']['manual_consumed']) ? $projectTask['ProjectTask']['manual_consumed'] : 0;
            $_estimated = isset($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
            //"auto" overload
            if( $_consumed > $_estimated ){
                $children[$key]['overload'] = $_consumed - $_estimated;
                $this->ProjectTask->id = $projectTask['ProjectTask']['id'];
                $_saved['overload'] = $children[$key]['overload'];
                $this->ProjectTask->save($_saved);
            } else {
                if(isset($projectTask['ProjectTask']['overload']) && $projectTask['ProjectTask']['overload']!=0) {
                    $this->ProjectTask->id = $projectTask['ProjectTask']['id'];
                    $_saved['overload'] = 0;
                    $this->ProjectTask->save($_saved);
                }
                $children[$key]['overload'] = 0;
            }
            //"manual" overload
            if( $_manualConsumed > $_estimated ){
                $children[$key]['manual_overload'] = $_manualConsumed - $_estimated;
                $this->ProjectTask->id = $projectTask['ProjectTask']['id'];
                $_saved['manual_overload'] = $children[$key]['manual_overload'];
                $this->ProjectTask->save($_saved);
            } else {
                if(isset($projectTask['ProjectTask']['manual_overload']) && $projectTask['ProjectTask']['manual_overload'] != 0) {
                    $this->ProjectTask->id = $projectTask['ProjectTask']['id'];
                    $_saved['manual_overload'] = 0;
                    $this->ProjectTask->save($_saved);
                }
                $children[$key]['manual_overload'] = 0;
            }
            //use setting "manual_overload" to calculate remain, completed
            if( $useManualConsumed ){
                $_overload = $children[$key]['manual_overload'];
                $_remain = $_estimated - $_consumed - $_manualConsumed;
                $_consumed = $_manualConsumed;
            } else {
                $_overload = !empty($children[$key]['overload']) ? $children[$key]['overload'] : 0;
                $_remain = $_estimated - $_consumed;
            }
            if($_consumed < 0){
                $children[$key]['remain'] = 'N/A';
                $children[$key]['estimated'] = 'N/A';
            } else {
                 if($_remain < 0){
                    $children[$key]['remain'] = 0;
                } else {
                    $children[$key]['remain'] = $_remain;
                }
            }
            if ($_consumed != 0) {
                $_completed = ($_estimated + $_overload) == 0 ? round(($_consumed * 100), 2) : round(($_consumed * 100) / ($_estimated + $_overload), 2);
            } else {
                $_completed = 0;
            }
            if(isset($projectTask['ProjectTask']['special']) && $projectTask['ProjectTask']['special'] == 1) {
                $children[$key]['completed'] = $_completed . '%';
            } else {
                if($_completed > 100){
                    $children[$key]['completed'] = 100 . '%';
                } else {
                    $children[$key]['completed'] = $_completed . '%';
                }
                if($_completed < 0){
                    $children[$key]['completed'] = 0 . '%';
                }
            }
            // unit price
            $children[$key]['unit_price'] = $projectTask['ProjectTask']['unit_price'] ? $projectTask['ProjectTask']['unit_price'] : 0;
            $children[$key]['consumed_euro'] = ($children[$key]['manual_consumed'] + $_cons) * $children[$key]['unit_price'];
            $children[$key]['remain_euro'] = (($children[$key]['estimated'] - ($_cons + $_manualConsumed)) > 0 ? ($children[$key]['estimated'] - $_cons) : 0) * $children[$key]['unit_price'];
            $children[$key]['workload_euro'] = $children[$key]['estimated'] * $children[$key]['unit_price'];
            $children[$key]['estimated_euro'] = $children[$key]['initial_estimated'] * $children[$key]['unit_price'];
            $manualConsumed += $_manualConsumed;
            $children[$key]['amount'] = $projectTask['ProjectTask']['amount'];
            $amount += $children[$key]['amount'];
            $children[$key]['progress_order'] = $projectTask['ProjectTask']['progress_order'];
            $children[$key]['text_1'] = $projectTask['ProjectTask']['text_1'];
            $children[$key]['text_updater'] = $projectTask['ProjectTask']['text_updater'];
            $children[$key]['text_time'] = $projectTask['ProjectTask']['text_time'];
            $children[$key]['progress_order_amount'] = round(($projectTask['ProjectTask']['progress_order'] * $projectTask['ProjectTask']['amount']) / 100, 2);
            $children[$key]['attachment'] = $projectTask['ProjectTask']['attachment'];
			$children[$key]['slider'] = $projectTask['ProjectTask']['slider'] ? $projectTask['ProjectTask']['slider'] : 0;
        }
        // Build the root child
        $root_child = array();
        $root_child['id'] = 'root';
        $root_child['task_title'] = $project['Project']['project_name'];
        $root_child['parent_id'] = -1;
        $root_child['leaf'] = 'false';
        $root_child['expanded'] = true;
        $root_child['children'] = array();
        /**
         * ----------------------------------------------------------------------->
         * HuuPc: Doan nay co van de, truoc mat note lai de kiem tra sau
         */
        $activityTasks      = $this->_getActivityTasks($listIdOfProjectTasks);
        $activityRequests   = $this->_getActivityRequests($project_id, $activityTasks);
        $consumed           = $this->_attachConsumedForParent($projectTasks, $activityTasks, $activityRequests);
        $estimated          = $this->_attachEstimatedForParent($projectTasks);
        $initial_estimated  = $this->_attachInitialEstimatedForParent($projectTasks);
        $buildPhases        = $this->_attachPhasePlansStructure($project_id);
        $buildParts         = $this->_attachPhasePartStructure($project_id);
        $startEndDates      = $this->_getStartEndDateAllTask($project_id);
        /**
         * HuuPc: Doan nay co van de, truoc mat note lai de kiem tra sau
         * ------------------------------------------------------------------------<
         */
        $_consumedPrevious = 0;

        foreach($buildPhases as $buildPhase){
            if(isset($buildPhase['is_activity']) && $buildPhase['is_activity'] == 'true'){
                $_consumedPrevious = $buildPhase['consumed'];
            }
        }
        $consumed = $consumed + $consumedSpecial;
        $consumed = round($consumed, 2);
        $root_child['consumed'] = $consumed;
        $root_child['estimated']  = $estimated;
        $root_child['initial_estimated'] = $initial_estimated;
        $root_child['remain'] = 0;
        $root_child['manual_consumed'] = $manualConsumed;
        $root_child['manual_overload'] = 0;
        $root_child['completed'] = 0;
        $root_child['amount'] = 0;
        $root_child['progress_order'] = 0;
        $root_child['progress_order_amount'] = 0;

        $root_child['task_start_date'] = $startEndDates['task_start_date'];
        $root_child['task_end_date'] = $startEndDates['task_end_date'];
        $root_child['initial_task_start_date'] = $startEndDates['initial_task_start_date'];
        $root_child['initial_task_end_date'] = $startEndDates['initial_task_end_date'];
        $root_child['duration'] = $this->getWorkingDays($root_child['task_start_date'], $root_child['task_end_date'], '');
        $root_child['id_activity'] = !empty($project['Project']['activity_id']) ? $project['Project']['activity_id'] : 0;

        $children[] = $root_child;
        $children = array_merge($buildParts, $buildPhases, $children);
        $children = $this->__buildTree($children);
        return $children;
    }

    /**
     * Function: create activity subtask
     * @var     :
     * @return  :
     * @author : BANGVN
     **/
    private function _createActivitySubTask($project_task, $activity_id, $project_task_id, $project_id, $parent_project_task_id){
        $dataActivityTask = array();
        //$phases = $this->_getPhasesesCombined($project_id);
        //$phase_name = $phases[$project_task['ProjectTask']['project_planed_phase_id']];
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id,$project_task['ProjectTask']['project_planed_phase_id']) : '';
        //parent_project_task_id
        //_getActivityTasksByActivityIdAndProjectTaskId
        $parent_task = $this->_getProjectTaskById($parent_project_task_id);
        $parent_activity_task = $this->_getActivityTasksByActivityIdAndProjectTaskIdPart2($activity_id, $parent_project_task_id);
        $task_title = !empty($parent_task['ProjectTask']['task_title']) ? $parent_task['ProjectTask']['task_title'] : '';
        $activity_task_name = $phase_name . "/" . $task_title . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        $result = array();
        if(!empty($parent_activity_task['ActivityTask']['id'])){
            if(!empty($checkTask)){
                $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
                $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            } else {
                $this->ActivityTask->create();
                $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
                $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
                $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
                $dataActivityTask['ActivityTask']['parent_id'] = $parent_activity_task['ActivityTask']['id'];
            }
            $dataActivityTask['ActivityTask']['task_status_id'] = @$project_task['ProjectTask']['task_status_id'];
            $dataActivityTask['ActivityTask']['milestone_id'] = @$project_task['ProjectTask']['milestone_id'];
            $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
            $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
            $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
            $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
            $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
            $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
            //update nctworkload activity task id
            $this->loadModel('NctWorkload');
            $this->NctWorkload->updateAll(array(
                'NctWorkload.activity_task_id' => $this->ActivityTask->id
            ), array(
                'NctWorkload.project_task_id' => $project_task_id
            ));
        }

        return $result;
    }

    private function _getPhaseNameByPhasePlanId($project_id, $project_planed_phase_id){
        $projectPhases = $this->_getPhaseses($project_id);
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhase.name');
        return $projectPhases[$project_planed_phase_id];
    }

    /**
     * Function: Create new activity task
     * @var     :
     * @return  :
     * @author : BANGVN
     **/
    private function _createActivityTask($project_task, $activity_id, $project_task_id, $project_id){
        $dataActivityTask = array();
        // $phases = $this->_getPhasesesCombined($project_id);
        // $phase_name = isset($project_task['ProjectTask']['project_planed_phase_id']) ? $phases[$project_task['ProjectTask']['project_planed_phase_id']] : '';
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id,$project_task['ProjectTask']['project_planed_phase_id']) : '';
        $activity_task_name = $phase_name . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        if(!empty($checkTask)){
            $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
        } else {
            $this->ActivityTask->create();
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
            $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
        }
        $dataActivityTask['ActivityTask']['task_status_id'] = @$project_task['ProjectTask']['task_status_id'];
        $dataActivityTask['ActivityTask']['milestone_id'] = @$project_task['ProjectTask']['milestone_id'];
        $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
        $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
        $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
        $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
        $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
        $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
        //update nctworkload activity task id
        $this->loadModel('NctWorkload');
        $this->NctWorkload->updateAll(array(
            'NctWorkload.activity_task_id' => $this->ActivityTask->id
        ), array(
            'NctWorkload.project_task_id' => $project_task_id
        ));
        return $result;
    }

    //create project task - update activity task deltail
    private function _syncActivityTask($project_id, $project_task, $project_task_id){
        $projects = $this->_getProject($project_id);
        $activity = $this->_getActivity($project_id);
        if (isset($activity)) {
            if (isset($activity[0])) {
                if(isset($activity[0]['Activity'])) {
                    if(isset($activity[0]['Activity']['id'])){
                        $activity_id = $activity[0]['Activity']['id'];
                        $is_exist_activity_task = $this->_checkExistActivityTask($activity_id, $project_task_id);
                        if ($project_task['ProjectTask']['parent_id'] == 0 || $project_task['ProjectTask']['parent_id'] == null || $project_task['ProjectTask']['parent_id'] == '') {
                            $this->_createActivityTask($project_task, $activity_id, $project_task_id, $project_id);
                        } else {
                            $this->_createActivitySubTask($project_task, $activity_id, $project_task_id, $project_id, $project_task['ProjectTask']['parent_id']);
                        }
                    }
                }
            }
        }

    }

    /**
     * @uses _syncPhaseTime Sync Phase's date and time.
     * @param type $project_task
     */
    private function _syncPhasePlanTime($project_task) {
        $project_id = $project_task["ProjectTask"]["project_id"];
        $project_planed_phase_id = $project_task["ProjectTask"]["project_planed_phase_id"];
        $task_end_date = strtotime($project_task["ProjectTask"]["task_end_date"]);
        $task_start_date = strtotime($project_task["ProjectTask"]["task_start_date"]);
        $project_phase_plan = $this->_getProjectPhasePlan($project_id, $project_planed_phase_id);
        if (isset($project_phase_plan[0]['ProjectPhasePlan'])) {
            $project_phase_plan = $project_phase_plan[0]['ProjectPhasePlan'];
            $phase_scope = $this->_getPhaseScope($project_id, $project_planed_phase_id);
            $min_date = $phase_scope['MIN(task_start_date)'];
            $max_date = $phase_scope['MAX(task_end_date)'];
            $_min_date = strtotime($min_date);
            $_max_date = strtotime($max_date);
            if(empty($_min_date) || $_min_date == "" || $_min_date == 0){
                if( $task_start_date )$project_phase_plan['phase_real_start_date'] = date('Y-m-d', $task_start_date);
            } else {
                if( $task_start_date && $task_start_date < $_min_date ){
                    $min_date = date('Y-m-d', $task_start_date);
                }
                $project_phase_plan['phase_real_start_date'] = $min_date;
            }
            if(empty($_max_date) || $_max_date == "" || $_max_date == 0){
                if( $task_end_date )$project_phase_plan['phase_real_end_date'] = date('Y-m-d', $task_end_date);
            } else {
                if( $task_end_date && $task_end_date > $_max_date ){
                    $max_date = date('Y-m-d', $task_end_date);
                }
                $project_phase_plan['phase_real_end_date'] = $max_date;
            }
            //$project_phase_plan[phase_planed_start_date]
            if( !$project_phase_plan['phase_planed_start_date'] || $project_phase_plan['phase_planed_start_date'] == '0000-00-00' ){
                $project_phase_plan['phase_planed_start_date'] = $project_phase_plan['phase_real_start_date'];
            }
            if( !$project_phase_plan['phase_planed_end_date'] || $project_phase_plan['phase_planed_end_date'] == '0000-00-00' ){
                $project_phase_plan['phase_planed_end_date'] = $project_phase_plan['phase_real_end_date'];
            }
            $project_phase_plan['planed_duration'] = $this->getWorkingDays($project_phase_plan['phase_planed_start_date'], $project_phase_plan['phase_planed_end_date'], 0);
            $this->ProjectTask->ProjectPhasePlan->save($project_phase_plan);
        } else {
            // Do nothing.
        }
    }

    /**
     * Function: Save Employees with in current Project
     * @var     : int $project_id
     * @var     : array $employess
     * @return  :
     * @author : HUUPC
     */
    private function _saveEmployees($employees, $profitCenters, $project_id, $workload, $tmp_estimated_details = array(), $callbacks = true) {
        $this->loadModel('ProjectTaskEmployeeRefer');
        $saveds = $this->ProjectTaskEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectTaskEmployeeRefer.project_task_id' => $project_id),
            'fields' => array('id')
        ));
        foreach($employees as $k => $v){
            if($v == '' || $v == null){
                unset($employees[$k]);
            }
        }
        if (!empty($employees)) {
            $_employees = array();
            if(!empty($profitCenters)) {
                $count = 0;
                foreach($employees as $id => $employee) {
                    foreach($profitCenters as $k => $profitCenter) {
                        if( $id == $k ){
                            $_employees[$id]['reference_id'] = $employee;
                            $_employees[$id]['is_profit_center'] = $profitCenter;
                            if( isset($tmp_estimated_details[$id]) ){
                                $_employees[$id]['estimated'] = $tmp_estimated_details[$id];
                            } else {
                                if( $count == 0 ){
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
                    $this->ProjectTaskEmployeeRefer->id = $key['ProjectTaskEmployeeRefer']['id'];
                    $this->ProjectTaskEmployeeRefer->delete();
                }
            }
            if(!empty($_employees)){
                if(count($_employees) == 1){
                    $this->ProjectTaskEmployeeRefer->create();
                    $data = array(
                        'reference_id' => $_employees[0]['reference_id'],
                        'is_profit_center' => $_employees[0]['is_profit_center'],
                        'project_task_id' => $project_id,
                        'estimated' => $workload
                    );
                    $this->ProjectTaskEmployeeRefer->save($data, array('validate' => true, 'callbacks' => $callbacks));
                } else {
                    foreach($_employees as $emp_key => $employee) {
                        $this->ProjectTaskEmployeeRefer->create();
                        $data = array(
                            'reference_id' => $employee['reference_id'],
                            'is_profit_center' => $employee['is_profit_center'],
                            'project_task_id' => $project_id,
                            'estimated' => $employee['estimated']
                        );
                        $this->ProjectTaskEmployeeRefer->save($data, array('validate' => true, 'callbacks' => $callbacks));
                    }
                }
            }
        } else {
            if (!empty($saveds)) {
                foreach ($saveds as $key) {
                    $this->ProjectTaskEmployeeRefer->id = $key['ProjectTaskEmployeeRefer']['id'];
                    $this->ProjectTaskEmployeeRefer->delete();
                }
            }
        }
    }

    private function list_projectStatus($project_id = null, $name = false){
        $this->loadModel('ProjectStatus');
        $company_id = $this->ProjectTask->Project->find("first", array("fields" => array("Project.company_id"),
            'conditions' => array('Project.id' => $project_id)));
        $company_id = $company_id['Project']['company_id'];
        $list_projectStatus = $this->ProjectStatus->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
				'order' => 'weight',
                'fields' => array('id', 'name')
            ));
        return $list_projectStatus;
    }
    private function _projectStatus($project_id = null, $name = false){
        $this->loadModel('ProjectStatus');
        $company_id = $this->ProjectTask->Project->find("first", array("fields" => array("Project.company_id"),
            'conditions' => array('Project.id' => $project_id)));
        $company_id = $company_id['Project']['company_id'];
        $projectStatus = $this->ProjectStatus->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        if( $name )return $projectStatus['ProjectStatus'];
        return isset($projectStatus['ProjectStatus']['id']) ? $projectStatus['ProjectStatus']['id'] : null;
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

    private function _hasPriority($p){
        $this->loadModel('ProjectPriority');
        $company_id = $this->employee_info['Company']['id'];
        return $this->ProjectPriority->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectPriority.id' => $p, 'company_id' => $company_id)
        ));
    }

    private function _hasMilestone($p){
        $this->loadModel('ProjectMilestone');
        return $this->ProjectMilestone->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectMilestone.id' => $p)
        ));
    }

    private function reorderPartPhase($p, $tree){
        $this->loadModel('ProjectPhasePlan');
        $phases = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_part_id'),
            'conditions' => array(
                'project_id' => $p
            ),
            'order' => array('weight' => 'ASC')
        ));
        $order = array();
        $weight = 0;
        $hasPart = false;
        foreach($phases as $d){
            if( $d['ProjectPhasePlan']['project_part_id'] ){
                $order['part-' . $d['ProjectPhasePlan']['project_part_id']] = $weight;
                $hasPart = true;
            } else {
                $order[$d['ProjectPhasePlan']['id']] = $weight;
            }
            $weight++;
        }
        //if dont have part -> dont reorder
        if( !$hasPart )return $tree;
        //a hack for "Previous task"
        $order[999999999] = 99999;
        //now reorder tree
        //$newOrder = array();
        $length = count($tree[0]['children']);
        for($i = 0; $i < $length-1; $i++){
            for($j = $i; $j < $length; $j++){
                $f1 = &$tree[0]['children'][$i];
                $f2 = &$tree[0]['children'][$j];
                if( isset($f1['is_part']) && $f1['is_part'] == 'true' ){
                    $k1 = 'part-' . ($f1['id'] - 999999999);
                } else {
                    $k1 = $f1['id'] - 999999999999;
                }
                if( isset($f2['is_part']) && $f2['is_part'] == 'true' ){
                    $k2 = 'part-' . ($f2['id'] - 999999999);
                } else {
                    $k2 = $f2['id'] - 999999999999;
                }
                if( isset($order[$k1]) && isset($order[$k2]) && $order[$k1] > $order[$k2] ){
                    //swap here
                    $t = $f1;
                    $f1 = $f2;
                    $f2 = $t;
                }
            }
        }
        return $tree;
    }
    // DATASYNC SECTION - END
    // JSON RESPONSE SECTION - BEGIN
    /**
     * List Tasks
     * @return json - list tasks
     * @author HUUPC
     * @access public
     * HuuPc da check
     */
    public function listTasksJson($project_id = null) {
        // set layout = ajax
        $this->layout = 'ajax';
        // kiem tra role
        $this->_checkRole(false, $project_id);
        // load tat cac cac task cua project task
        $projectTasks = $this->_getAllProjectTasks($project_id);
        // lay danh sach id cua project task
        $listIdOfProjectTasks = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
        // Load activity tasks lien ket voi project task
        $activityTasks = $this->_getActivityTasks($listIdOfProjectTasks);
        // Attach Consume Calculator from RMS
        $activityRequests = $this->_getActivityRequests($project_id, $activityTasks);
        // Attach Employees Assigned to Project Tasks Array
        $projectTasks = $this->_attachReferencedEmployees($projectTasks);

        // Attach Consumed to Project Tasks Array
        $projectTasks = $this->_attachConsumed($projectTasks, $activityTasks, $activityRequests);
		
		$projectTasks = $this->getValueEACTask($projectTasks, $activityTasks);
        // Build tree structure
		
        $children = $this->_buildJsonStructure($projectTasks, $project_id);
		
		
        // set result to extjx
        $result = $this->reorderPartPhase($project_id, $children);
        // $result = array("root" => $result);

        $this->set(compact('result'));
    }
	public function getValueEACTask($projectTasks, $act_task_id) {
		$es_end = $con_start = 0;
		$con_end = date('Y-m-d', strtotime('last day of last month'));
		$es_start =  date('Y-m-01', time()); // current month
		$es_task_id = array();
		$con_ptask_id = array();
		$con_atask_id = array();
		$es_nct_task_id = array();
		$eac_estimated = array();
		$eac_nct_estimated = array();
		$eac_consumed = array();
		
		foreach($projectTasks as $index => $value){
			$dx = $value['ProjectTask'];
			$task_id = $dx['id'];
			$estimated = $dx['estimated'];
			$consumed = !empty($dx['consumed']) ?  $dx['consumed'] : 0;
			$task_start =  $dx['task_start_date'];
			$task_end =  $dx['task_end_date'];
			
			if($estimated > 0 && strtotime($es_start) <= strtotime($task_end)){
				if(strtotime($es_start) <= strtotime($task_start)){
					$eac_estimated[$task_id] = $estimated;
				}else{
					if($dx['is_nct'] == 1){
						$es_nct_task_id[] = $task_id;
					}else{
						$es_task_id[] = $task_id;
						$duration = $dx['duration'];
						$workingday = $this->getWorkingDays($es_start, $task_end, 0);
						$eac_estimated[$task_id] = round($estimated * $workingday / $duration, 2);
						
					}
				}
			}
			
			if($consumed > 0 && strtotime($con_end) > strtotime($task_start) && !empty($act_task_id)){
				$con_start = (strtotime($task_start) > $con_start && $con_start > 0) ? $con_start : strtotime($task_start);
				$con_atask_id[] = $act_task_id[$task_id];
				$con_ptask_id[$act_task_id[$task_id]] = $task_id;
			}
		}
	
		if(!empty($es_nct_task_id)){
			
			$this->loadModel('NctWorkload');
			$workloads = $this->NctWorkload->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $es_nct_task_id,
					'task_date >=' =>  $es_start ,
				),
				'fields' => array('project_task_id', 'sum(estimated) as value'),
				'group' => array('project_task_id'),
			));
			
			$eac_nct_estimated = !empty($workloads) ? Set::combine($workloads, '{n}.NctWorkload.project_task_id', '{n}.0.value') : array();
			
		}
		if(!empty($con_atask_id)){
			$this->loadModel('ActivityRequest');
			$activityRequests = $this->ActivityRequest->find('all', array(
				'recursive' => -1,
				'fields' => array(
					'task_id',
					'SUM(`value`) AS valid',
				),
				'group' => array('task_id'),
				'conditions' => array(
					'task_id' => $con_atask_id,
					'date BETWEEN ? AND ?' => array($con_start, strtotime($con_end)),
					'status' => 2
				)
			));
			$eac_consumed = !empty($activityRequests) ? Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0') : array();
		}
			
		foreach($projectTasks as $key => $projectTask){
            $projectTaskId = $projectTask['ProjectTask']['id'];
			$projectTasks[$key]['ProjectTask']['eac'] = 0;
            if(!empty($eac_consumed) && !empty($act_task_id[$projectTaskId]) && !empty($eac_consumed[$act_task_id[$projectTaskId]])){
               $projectTasks[$key]['ProjectTask']['eac'] = $eac_consumed[$act_task_id[$projectTaskId]]['valid'];
            }
            if(!empty($eac_estimated) && !empty($eac_estimated[$projectTaskId])){
               $projectTasks[$key]['ProjectTask']['eac'] += $eac_estimated[$projectTaskId];
            }
            if(!empty($eac_nct_estimated) && !empty($eac_nct_estimated[$projectTaskId])){
               $projectTasks[$key]['ProjectTask']['eac'] += $eac_nct_estimated[$projectTaskId];
            }
			
        }
		return $projectTasks;
	
	}
    public function createTaskJson($project_id) {
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ProjectPhasePlan');
        $this->layout = 'ajax';
        if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }
            if (!is_null($jsonData) and $jsonData != false) {
                $this->data = $jsonData;
            }
        }
        if (!empty($this->data)) {
            $data["ProjectTask"] = $this->data;
            if(!isset($data["ProjectTask"]['special'])||$data["ProjectTask"]['special']==null)
            $data["ProjectTask"]['special']=0;
            $success = true;
            if($data["ProjectTask"]['parent_id'] != 0){
                $activityTasks = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $data["ProjectTask"]['parent_id']),
                    'fields' => array('id')
                ));
                $hasUsed = 0;
                if(!empty($activityTasks) && $activityTasks['ActivityTask']['id']){
                    $hasUsed = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('task_id' => $activityTasks['ActivityTask']['id'], 'value != 0')
                    ));
                }
                if($hasUsed != 0){
                    $success = false;
                    $result = sprintf(__('This task "%s" is in used/ has consumed', true), $data['parent_name']);
                    $data = null;
                }
            }
            if($success == true){
                if(!empty($data["ProjectTask"]['project_planed_phase_id'])){
                    if (is_int(intval($data["ProjectTask"]['project_planed_phase_id']))) {

                    } else {
                        unset($data["ProjectTask"]['project_planed_phase_id']);
                    }
                }

                if($data["ProjectTask"]['parent_id'] > 999999999999){
                    $data["ProjectTask"]['parent_id'] = 0;
                }
                if(!empty($data["ProjectTask"]['project_planed_phase_id'])){
                    if($data["ProjectTask"]['parent_id'] == 0){
                        //The task is created under a phase,
                        //Default value of start date and date of task = start date and end date of phase
                        $dateTmp = $this->ProjectPhasePlan->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'id' => $data["ProjectTask"]['project_planed_phase_id']
                            ),
                            'fields'=>array('phase_real_start_date','phase_real_end_date')
                        ));
                        $startDateTmp = $dateTmp['ProjectPhasePlan']['phase_real_start_date'];
                        $endDateTmp = $dateTmp['ProjectPhasePlan']['phase_real_end_date'];
                    } else {
                        //The task is created under a task parent,
                        //Default value of start date and date of task = start date and end date of task parent
                        $dateTmp = $this->ProjectTask->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'id' => $data["ProjectTask"]['parent_id']
                            ),
                            'fields'=>array('task_start_date','task_end_date')
                        ));
                        $startDateTmp = $dateTmp['ProjectTask']['task_start_date'];
                        $endDateTmp = $dateTmp['ProjectTask']['task_end_date'];
                    }
                }
                $conditions = array(
                    "ProjectTask.task_title" => $data["ProjectTask"]['task_title'],
                    "ProjectTask.parent_id" => $data["ProjectTask"]['parent_id'],
                    "ProjectTask.project_id" => $data["ProjectTask"]['project_id']
                );
                if(!empty($data["ProjectTask"]['project_planed_phase_id'])) $conditions['ProjectTask.project_planed_phase_id'] = $data["ProjectTask"]['project_planed_phase_id'];
                $flag = $this->ProjectTask->find('count', array(
                    'recursive' => -1,
                    'conditions' => $conditions,
                ));
                if($flag > 0){
                    $success = false;
                    $result = __('The task name already exists', true);
                    $data = null;
                } else {
                    if ( !$this->_hasPriority($data["ProjectTask"]['task_priority_id']) ) {
                        unset($data["ProjectTask"]['task_priority_id']);
                    }
                    if ( !$this->_hasMilestone($data["ProjectTask"]['milestone_id']) ) {
                        unset($data["ProjectTask"]['milestone_id']);
                    }
                    if ( !$this->_hasStatus($data['ProjectTask']['task_status_id']) ) {
                        $status = $this->_projectStatus($project_id, true);
                        $data["ProjectTask"]['task_status_id'] = $status['id'];
                        $data["ProjectTask"]['task_status_name'] = $status['name'];
                    }
                    $_predecessor   = $data["ProjectTask"]['predecessor'];
                    $_startDate     = ($data["ProjectTask"]['task_start_date']=="") ? $startDateTmp : $data["ProjectTask"]['task_start_date'];
                    $_endDate       = ($data["ProjectTask"]['task_end_date']=="") ? $endDateTmp : $data["ProjectTask"]['task_end_date'];
                    $_duration      = $data["ProjectTask"]['duration'];
                    if (isset($_predecessor) && $_predecessor != 0) {
                        $_startDate = $this->_createPredecessor($_startDate, $_predecessor);
                    }
                    if (isset($_duration) && $_duration != 0) {
                        if (isset($_startDate)) { // duration isset and start day is set
                            if (isset($_endDate)) { // duration isset and start day is set and enddate is set
                                $_duration = null;
                                $_duration = $this->getWorkingDays($_startDate, $_endDate, $_duration);
                            } else { // duration is set and start day is set and end date is not set
                                $_endDate = null;
                                $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                            }
                        } else { // duration isset and start day is not set
                            if (isset($_endDate)) { // duration isset and start day not set and enddate is set
                                $_startDate = null;
                                $_startDate = $this->_durationStartDate($_startDate, $_endDate, $_duration);
                            } else { // duration is set and start day is not set and end date is not set
                                // do nothing
                                $data["ProjectTask"]['duration'] = null;
                            }
                        }
                    } else {
                        // duration is not set
                        if (isset($_startDate)) {
                            if (isset($_endDate)) {
                                $_duration = null;
                                $_duration = $this->getWorkingDays($_startDate, $_endDate, $_duration);
                            } else {
                                // do nothing
                            }
                        } else {
                            if (isset($_endDate)) {
                                // do nothing
                            } else {
                                // do nothing
                            }
                        }
                    }
                    if (strtotime($_startDate) > strtotime($_endDate)) {
                        $_endDate = $_startDate;
                    }
                    $data["ProjectTask"]['task_start_date'] = $_startDate;
                    $data["ProjectTask"]['task_end_date'] = $_endDate;
                    $data["ProjectTask"]['duration'] = $_duration;
                    $data["ProjectTask"]['task_completed'] = 0;
                    if ($data["ProjectTask"]['parentId'] > 999999999999 || empty($data["ProjectTask"]['parentId'])) {
                        $data["ProjectTask"]['parentId'] = 0;
                    }
                    // $count = $this->ProjectTask->find('count', array(
                    //      'recursive' => -1,
                    //      'conditions' => array("ProjectTask.project_planed_phase_id" => $data["ProjectTask"]['project_planed_phase_id'], "ProjectTask.parent_id" => $data["ProjectTask"]['parent_id'])
                    //  ));
                    if(!empty($data["ProjectTask"]['project_planed_phase_id'])){
                        $data["ProjectTask"]['weight'] = $this->getWeight($project_id, $data["ProjectTask"]['project_planed_phase_id'], $data["ProjectTask"]['parent_id']);
 
                    }
                    //END
                    $tmp_task_assign_to_id = $tmp_task_assign_to_text = $is_profit_center = array();
                    $is_profit_center = @$data["ProjectTask"]['is_profit_center'];
                    $tmp_task_assign_to_text = $data["ProjectTask"]['task_assign_to_text'];
                    if (!empty($data["ProjectTask"]['task_assign_to_id'])) {
                        $tmp_task_assign_to_id = $data["ProjectTask"]['task_assign_to_id'];
                        if(is_array($data["ProjectTask"]['task_assign_to_id']) && (count($data["ProjectTask"]['task_assign_to_id']) >0)){
                            $data["ProjectTask"]['task_assign_to_id'] = implode(",", $data["ProjectTask"]['task_assign_to_id']);
                        }
                    }
                    unset($data["ProjectTask"]['is_profit_center']);
                    unset($data["ProjectTask"]['task_assign_to_id']);
                    unset($data["ProjectTask"]['id']);
                    unset($data['ProjectTask']['id_refer_flag']);
                    if ($data["ProjectTask"]['estimated'] == '' || $data["ProjectTask"]['estimated'] == 0 || $data["ProjectTask"]['estimated'] == null) {
                        $data["ProjectTask"]['estimated'] = 0;
                        $data["ProjectTask"]['allow_request'] = isset($remain[0]) ? $remain[0] : 0;
                    } else {
                        $data["ProjectTask"]['allow_request'] = 1;
                    }
                    //todo: default profile to (phase's profile) and task is new
                    if ( !$data['ProjectTask']['profile_id'] && !empty($data['ProjectTask']['project_planed_phase_id'])) {
                        $profile = $this->ProjectPhasePlan->find('first', array('recursive' => -1, 'conditions' => array('id' => $data['ProjectTask']['project_planed_phase_id'])));
                        if( $profile && $profile['ProjectPhasePlan']['profile_id'] ){
                            $data['ProjectTask']['profile_id'] = $profile['ProjectPhasePlan']['profile_id'];
                            $pname = ClassRegistry::init('Profile')->read(null, $data['ProjectTask']['profile_id']);
                        }
                    }
                    $this->ProjectTask->create();
                    $result = $this->ProjectTask->save($data);
                    unset($data["ProjectTask"]['task_assign_to_text']);
                    unset($data["ProjectTask"]['task_assign_to']);
                    $data['id'] = $this->ProjectTask->id;
                    if (!empty($tmp_task_assign_to_id)) {
                        if(is_array($tmp_task_assign_to_id) && (count($tmp_task_assign_to_id) >0)){
                            $employee = $tmp_task_assign_to_id;
                            $profitCenter = $is_profit_center;
                            $assign = $this->_saveEmployees($employee, $profitCenter, $data['id'], $data["ProjectTask"]['estimated'], null, false);
                        }
                    }
                    if ($result) {
                        // fix bug array of ids in combobox in view
                        $result['ProjectTask']['task_assign_to_id'] = $tmp_task_assign_to_id;
                        $result['ProjectTask']['task_assign_to_text'] = $tmp_task_assign_to_text;
                        $result['ProjectTask']['is_profit_center'] = $is_profit_center;
                        $result['ProjectTask']['id'] = $data['id'];
                        if( isset($pname) )$result['ProjectTask']['profile_text'] = $pname['Profile']['name'];
                        else $result['ProjectTask']['profile_text'] = '';
                        $this->_updateParentTask($data["ProjectTask"]['project_id']);
                        if(!empty($data["ProjectTask"]['project_planed_phase_id'])){
                            $this->_syncPhasePlanTime($result);
                        }
                        
                        $this->_saveStartEndDateAllTask($data["ProjectTask"]['project_id']);
                        $this->ProjectTask->staffingSystem($project_id);
                        $this->_deleteCacheContextMenu();
                        $project_task_id = $this->ProjectTask->getLastInsertID();
                        $this->_syncActivityTask($project_id, $result, $project_task_id);
                        //Tracking action
                        $projectName = $this->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array("Project.project_name"),
                        'conditions' => array('Project.id' => $project_id)));
                        $employ = $this->employee_info['Employee']['fullname'];
                        $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  sprintf('Create task `%s` under `%s`', $result['ProjectTask']['task_title'], $projectName['Project']['project_name']);
                        $this->writeLog($result, $this->employee_info, $message);
                    }
                }
            }
        }
        $result = array("success" => $success, "message" => $result, "data" => $data);

        $this->set(compact('result'));
    }

    public function updateTaskJson($project_id) {
        $useManual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        $this->layout = 'ajax';
        $failed='';
        if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }

            if (!is_null($jsonData) and $jsonData != false) {
                $this->data = $jsonData;
            }
        }
        if (!empty($this->data)) {
            $data = $this->data;
            $oldDataOfTask = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $data['id']),
            ));
            $this->loadModels('ProjectPriority', 'ProjectStatus', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter');
            $oldListAssign = $this->ProjectTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $data['id']),
                'fields' => array('reference_id', 'is_profit_center')
            ));

            $this->loadModel('Project');
            $data['special_consumed'] = 0;
            if (isset($data['special'])&&$data['special'] == 1) {
                $data['special_consumed'] = isset($data['consumed']) ? $data['consumed'] : 0;
                $data['consumed'] = 0;
            }
            if ($this->_developersMode) {
                $this->log("project_tasks_controller->updateTaskJson() :: \$this->data");
                $this->log($data);
            }
            if (is_int(intval($data['project_planed_phase_id']))) {

            } else {
                unset($data['project_planed_phase_id']);
            }

            if ( !$this->_hasPriority($data['task_priority_id']) ) {
                unset($data['task_priority_id']);
            }
            if ( !$this->_hasMilestone($data['milestone_id']) ) {
                unset($data['milestone_id']);
            }
            if ( !$this->_hasStatus($data['task_status_id']) ) {
                $status = $this->_projectStatus($project_id, true);
                $data['task_status_id'] = $status['id'];
                $data['task_status_name'] = $status['name'];
            }
            $tmp_task_assign_to_id = $tmp_task_assign_to_text = $is_profit_center = $tmp_estimated_detail = array();
            $is_profit_center = @$data['is_profit_center'];
            $tmp_task_assign_to_text = @$data['task_assign_to_text'];
            $tmp_estimated_detail = @$data['estimated_detail'];
            if ($data['estimated'] == 0) {
                $tmp_estimated_detail = array();
            }
            unset($data['task_assign_to_text']);
            unset($data['task_assign_to']);
            unset($data['estimated_detail']);
            $employee = $profitCenter = array();
            if (!empty($data['task_assign_to_id'])) {
                $tmp_task_assign_to_id = $data['task_assign_to_id'];
                if (is_array($data['task_assign_to_id']) && (count($data['task_assign_to_id']) >0)) {
                    //$employee = explode(",", $data['task_assign_to_id']);
                    $employee = $data['task_assign_to_id'];
                    $profitCenter = $data['is_profit_center'];
                    //$data['task_assign_to_id'] = implode(",", $data['task_assign_to_id']);
                }
            }
            $assign = $this->_saveEmployees($employee, $profitCenter, $data['id'],  $data['estimated'], $tmp_estimated_detail, false);
            unset($data['task_assign_to_id']);
            unset($data['is_profit_center']);
            $_predecessor   = $this->_updatePredeces($data['id'], $data['predecessor']);
            $_startDate     = (empty($data['task_start_date']) || $data['task_start_date']=="" ) ? null : $data['task_start_date'];
            $_endDate       = (empty($data['task_end_date']) || $data['task_end_date']=="") ? null : $data['task_end_date'];
            $_duration      = $data['duration'];
			$_keepDuration  = (!empty($data['keep_duration'])) ? $data['keep_duration'] : 0;
            $_taskId        = $data['id'];
           // isset duration
            if (isset($_duration) && $_duration != 0) {
                if (isset($_startDate)) { // duration isset and start day is set
                    if (isset($_endDate)) { // duration isset and start day is set and enddate is set
                        //$_duration = null;
						if($_keepDuration == 1){
							$_endDate = null;
                            $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
						}else if (($oldDataOfTask['ProjectTask']['duration'] == $_duration) || (($oldDataOfTask['ProjectTask']['duration'] == null) && (strtotime($_endDate) != strtotime($oldDataOfTask['ProjectTask']['task_end_date']))) || (($oldDataOfTask['ProjectTask']['duration'] == null) && (strtotime($_startDate) != strtotime($oldDataOfTask['ProjectTask']['task_start_date'])))) {
                            //do nothing
                            $_duration = null;
                            $_duration = $this->getWorkingDays($_startDate, $_endDate, $_duration);
                        } else {
                            $_endDate = null;
                            $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                        }
                        //$_endDate = null;
                        //$_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                    } else { // duration is set and start day is set and end date is not set
                        $_endDate = null;
                        $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                    }
                } else { // duration isset and start day is not set
                    if (isset($_endDate)) { // duration isset and start day not set and enddate is set
                        $_startDate = null;
                        $_startDate = $this->_durationStartDate($_startDate, $_endDate, $_duration);
                    } else { // duration is set and start day is not set and end date is not set
                        // do nothing
                        $data['duration'] = null;
                    }
                }
            } else {
                // duration is not set
                if (isset($_startDate)) {
                    if (isset($_endDate)) {
                        $_duration = null;
                        $_duration = $this->getWorkingDays($_startDate, $_endDate, $_duration);
                    } else {
                        // do nothing
                    }
                } else {
                    if (isset($_endDate)) {
                        // do nothing
                    } else {
                        // do nothing
                    }
                }
            }

            if (isset($_predecessor) && $_predecessor != 0) {
                if (!empty($oldDataOfTask['ProjectTask']['predecessor']) && $oldDataOfTask['ProjectTask']['predecessor'] == $_predecessor) {
                    //do nothing
                } else {
                    $_startDate = $this->_predecessor($_startDate, $_predecessor, $_taskId);
                    $_endDate = null;
                    $_endDate = $this->_durationEndDate($_startDate, $_endDate, $oldDataOfTask['ProjectTask']['duration']);
                }
            }

            $data['predecessor'] = $_predecessor;
            $data['task_start_date'] = $_startDate;
            $data['task_end_date'] = $_endDate;
            $data['duration'] = $_duration;
            if ($data['consumed'] < 0) {
                $data['estimated'] = 0;
            }

            if ($data['estimated'] == '' || $data['estimated'] == 0 || $data['estimated'] == null) {
                //$remain = $this->_remain();
                $data['estimated'] = 0;
                $data['allow_request'] = isset($remain[0]) ? $remain[0] : 0;
            } else {
                $data['allow_request'] = 1;
            }
            if ($data['parentId'] > 999999999999) {
                $data['parentId'] = 0;
            }
            if ($data['parent_id'] > 999999999999) {
                $data['parent_id'] = 0;
            }
            $flag = $this->ProjectTask->find('count', array(
                'recursive' => -1,
                'conditions' => array("ProjectTask.task_title" => $data['task_title'],"ProjectTask.project_planed_phase_id" => $data['project_planed_phase_id'], "ProjectTask.parent_id" => $data['parent_id'])
            ));
            if ($flag>0 && (strcmp($data['task_title'], $oldDataOfTask['ProjectTask']['task_title']) == 0)) {
                $data['task_title'] = $oldDataOfTask['ProjectTask']['task_title'];
                $failed = 'Update task name failed, because the task name already exists';
            }
            if (isset($data['consumed']) && $data['consumed'] > $data['estimated']) {
                $data['overload'] = $data['consumed'] - $data['estimated'];
            } else {
                $data['overload'] = 0;
            }
            if ( $useManual ) {
                $consume = $data['manual_consumed'];
                if(isset($data['manual_consumed']) && $data['manual_consumed'] > $data['estimated']){
                    $data['manual_overload'] = $data['manual_consumed'] - $data['estimated'];
                } else {
                    $data['manual_overload'] = 0;
                }
            }
            if (isset($data['is_phase']) && $data['is_phase'] == 'true') {
                $result = true;
                $parent = true;
                //do nothing
            } else {
                $oldEndDate = ($oldDataOfTask['ProjectTask']['task_end_date'] != '0000-00-00') ? strtotime($oldDataOfTask['ProjectTask']['task_end_date']) : 0;
                $newEndDate = ($data['task_end_date'] != '0000-00-00') ? strtotime($data['task_end_date']) : 0;

                $result = $this->ProjectTask->save($data);
                $parent = false;
                // Update Phase End Day
                if ($result) {
                    // fix bug array of ids in combobox in view
                    $result['ProjectTask']['task_assign_to_id'] = $tmp_task_assign_to_id;
                    $result['ProjectTask']['task_assign_to_text'] = $tmp_task_assign_to_text;
                    $result['ProjectTask']['is_profit_center'] = $is_profit_center;
                    $result['ProjectTask']['estimated_detail'] = $tmp_estimated_detail;
                    $result['ProjectTask']['is_linked'] = $this->ProjectTask->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('ProjectTask.predecessor' => $result['ProjectTask']['id'])
                    ));
                    $result['ProjectTask']['remain'] = $data['remain'];
                    $result['ProjectTask']['amount'] = $data['amount'];
                    $result['ProjectTask']['overload'] = $data['overload'];
                    $result['ProjectTask']['manual_overload'] = $data['manual_overload'];
                    $consume = $useManual ? $data['manual_consumed'] : $data['consumed'];
                    $overload = $useManual ? $data['manual_overload'] : $data['overload'];
                    $result['ProjectTask']['completed'] = ($data['estimated'] > 0) ? round($consume / ( $overload + $data['estimated'] ) * 100, 2) : 0;
                    $result['ProjectTask']['progress_order_amount'] = round($data['progress_order'] * $data['amount'] / 100, 2);
                    $i = 0;
                    if ($oldEndDate <= $newEndDate) {
                        while ($oldEndDate < $newEndDate) {
                            $i++;
                            $oldEndDate = mktime(0, 0, 0, date("m", $oldEndDate), date("d", $oldEndDate)+1, date("Y", $oldEndDate));
                        }
                    } else {
                        while ($newEndDate < $oldEndDate) {
                            $i--;
                            $newEndDate = mktime(0, 0, 0, date("m", $newEndDate), date("d", $newEndDate)+1, date("Y", $newEndDate));
                        }
                    }
                    $result['ProjectTask']['increase_endDate'] = $i;
                    $this->_updateParentTask($project_id);
                    $this->_syncPhasePlanTime($result);
                    $this->_saveStartEndDateAllTask($project_id);
                    $this->_syncActivityTask($project_id, $result, $_taskId);
                    $this->ProjectTask->staffingSystem($project_id);
                    $this->_deleteCacheContextMenu();
                    if (isset($data['parent_id'])) {
                        $this->ProjectTask->id = $data['parent_id'];
                        $parent = $this->ProjectTask->Read();
                    }
                    //Tracking action
                    $projectName = $this->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id)));
                    $employ = $this->employee_info['Employee']['fullname'];
                    $cId = $this->employee_info['Company']['id'];
                    $listProPriority = $this->ProjectPriority->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $cId),
                        'fields' => array('id', 'priority')
                    ));
                    $listProStatus = $this->ProjectStatus->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $cId),
						'order' => 'weight',
                        'fields' => array('id', 'name')
                    ));
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  __('Update', true) . ' ';
                    if (!empty($data['id_refer_flag'])) {
                        $listEmployee = $this->Employee->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $cId),
                            'fields' => array('id', 'fullname')
                        ));
                        $listPc = $this->ProfitCenter->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $cId),
                            'fields' => array('id', 'name')
                        ));
                        foreach ($data['id_refer_flag'] as $key => $value) {
                            if ($value === null) continue;
                            if (!empty($oldListAssign[$key]) && ($oldListAssign[$key] == $value) ) {
                                // do nothing
                            } else {
                                if($value == 0){
                                    $message .= ' ' . __('Affected to', true). ' ' . !empty($listEmployee[$key]) ? $listEmployee[$key] : '';
                                }else{
                                    $message .= ' ' . __('Affected to', true). ' ' . !empty($listPc[$key]) ? $listPc[$key] : '';
                                }
                            }
                            unset($oldListAssign[$key]);
                        }
                        if (!empty($oldListAssign)) {
                            foreach ($oldListAssign as $key => $value) {
                                if ($value == 0) {
                                    $message .= ' ' . __('Remove', true). ' ' . $listEmployee[$key];
                                } else {
                                    $message .= ' ' . __('Remove', true). ' ' . $listPc[$key];
                                }
                            }
                        }
                    }
                    $lists = array('task_title', 'task_priority_id', 'task_status_id', 'task_start_date', 'task_end_date', 'estimated', 'initial_estimated', 'attachment');
                    foreach($lists as $key) {
                        if (isset($oldDataOfTask['ProjectTask'][$key]) && isset($data[$key]) && ($oldDataOfTask['ProjectTask'][$key] != $data[$key])) {
                            switch ($key) {
                                case 'task_title':
                                    $message .= ' ' . __('Modify task name', true). ' ' .$data[$key];
                                    break;
                                case 'task_priority_id':
                                    $message .= ' ' . __('Modify priority', true). ' ' . $listProPriority[$data[$key]];
                                    break;
                                case 'task_status_id':
                                    $message .= ' ' . __('Modify status', true). ' ' . $listProStatus[$data[$key]];
                                    break;
                                case 'task_start_date':
                                    $message .= ' ' . __('Modify start date', true). ' ' .$data[$key];
                                    break;
                                case 'task_end_date':
                                    $message .= ' ' . __('Modify end date', true). ' ' .$data[$key];
                                    break;
                                case 'estimated':
                                    $message .= ' ' . __('Workload', true). ' '. $oldDataOfTask['ProjectTask'][$key].' '. __('To', true). ' ' .$data[$key];
                                    break;
                                case 'initial_estimated':
                                    $message .= ' ' . __('Initial workload', true). ' '. $oldDataOfTask['ProjectTask'][$key].' '. __('To', true). ' ' .$data[$key];
                                    break;
                                case 'attachment':
                                    $message .= ' ' . __('Modify attachment', true);
                                    break;

                                default:
                                    $message .= ' ' . __('Update', true). ' ' .$data[$key];
                                    break;
                            }
                        }
                    }
                    $this->writeLog($result, $this->employee_info, $message);
                }
            }
        } else {

        }
        $result = array("success" => true, "message" => $result, "parent" => $parent ,"failed" => $failed);
        $this->set(compact('result'));
    }
    public function staffingWhenUpdateTask($project_id = null) {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->layout = 'ajax';
        if($project_id != null){
            $this->ProjectTask->staffingSystem($project_id);
            echo $project_id;
        }
        exit;
    }
    // DATASYNC SECTION - END
    // JSON RESPONSE SECTION - BEGIN
    /**
     * List Tasks
     * @return json - list tasks
     * @author HUUPC
     * @access public
     */
    public function updateLinkedTasks() {
        $this->layout = 'ajax';
        $result = array();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            if(isset($_GET['change'])){
                $projectTasks = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('predecessor' => $id),
                    'fields' => array('id', 'task_start_date', 'duration', 'predecessor', 'task_end_date')
                ));
                $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
                $change = $_GET['change'];
                if ($change == 'true') {
                    if (isset($_GET['number'])) {
                        $number = $_GET['number'];
                        if (!empty($projectTasks)) {
                            foreach($projectTasks as $taskId => $projectTask) {
                                $startDate = ($projectTask['task_start_date'] != '0000-00-00') ? strtotime($projectTask['task_start_date']) : 0;
                                if ($startDate != 0) {
                                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+$number, date("Y", $startDate));
                                    $saved['task_start_date'] = date('Y-m-d', $startDate);
                                    if (!empty($projectTask['duration'])) {
                                        $saved['task_end_date'] = $this->_durationEndDate($saved['task_start_date'], null, $projectTask['duration']);
                                    } else {
                                        $saved['task_end_date'] = $this->_durationEndDate($saved['task_start_date'], null, $number);
                                    }
                                } else {
                                    if(isset($_GET['endDate'])){
                                        $endDate = $_GET['endDate'];
                                        if(!empty($endDate)){
                                            $endDate = strtotime($endDate);
                                            $endDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate)+1, date("Y", $endDate));
                                            $saved['task_start_date'] = date('Y-m-d', $endDate);
                                            if (!empty($projectTask['duration'])) {
                                                $saved['task_end_date'] = $this->_durationEndDate($saved['task_start_date'], null, $projectTask['duration']);
                                            } else {
                                                $saved['task_end_date'] = $this->_durationEndDate($saved['task_start_date'], null, 1);
                                            }
                                        } else {
                                            $saved['task_start_date'] = '0000-00-00';
                                            $saved['task_end_date'] = '0000-00-00';
                                        }
                                    }

                                }
                                $tmps = $this->ProjectTask->find('count', array(
                                    'recursive' => -1,
                                    'conditions' => array('ProjectTask.predecessor' => $taskId)
                                ));
                                if ($tmps != 0) {
                                    $i = 0;
                                    $oldEndDate = strtotime($projectTask['task_end_date']);
                                    $newEndDate = strtotime($saved['task_end_date']);
                                    if ($oldEndDate <= $newEndDate) {
                                        while ($oldEndDate < $newEndDate) {
                                            $i++;
                                            $oldEndDate = mktime(0, 0, 0, date("m", $oldEndDate), date("d", $oldEndDate)+1, date("Y", $oldEndDate));
                                        }
                                    } else {
                                        while($newEndDate < $oldEndDate){
                                            $i--;
                                            $newEndDate = mktime(0, 0, 0, date("m", $newEndDate), date("d", $newEndDate)+1, date("Y", $newEndDate));
                                        }
                                    }
                                    $this->_linkedChangeTasks($taskId, $i);

                                }
                                $this->ProjectTask->id = $taskId;
                                $this->ProjectTask->save($saved);

                            }
                            $result = array("success" => true, 'linked' => 'yes');
                        }
                    }
                } else {
                    if (!empty($projectTasks)) {
                        foreach($projectTasks as $taskId => $projectTask){
                            $this->ProjectTask->id = $taskId;
                            $saved['predecessor'] = '';
                            $this->ProjectTask->save($saved);
                        }
                        $result = array("success" => true, 'linked' => 'no');
                    }
                }
            }
        }

        $this->set(compact('result'));
    }

    private function _linkedChangeTasks($id, $number) {
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('predecessor' => $id),
            'fields' => array('id', 'task_start_date', 'duration', 'predecessor', 'task_end_date')
        ));
        $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        foreach($projectTasks as $taskId => $projectTask) {
            $startDate = ($projectTask['task_start_date'] != '0000-00-00') ? strtotime($projectTask['task_start_date']) : 0;
            if ($startDate != 0) {
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+$number, date("Y", $startDate));
                $saved['task_start_date'] = date('Y-m-d', $startDate);
                if (!empty($projectTask['duration'])) {
                    $saved['task_end_date'] = $this->_durationEndDate($saved['task_start_date'], null, $projectTask['duration']);
                } else {
                    $saved['task_end_date'] = $this->_durationEndDate($saved['task_start_date'], null, $number);
                }
            }
            $tmps = $this->ProjectTask->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.predecessor' => $taskId)
            ));
            if ($tmps != 0) {
                $i = 0;
                $oldEndDate = strtotime($projectTask['task_end_date']);
                $newEndDate = strtotime($saved['task_end_date']);
                if ($oldEndDate <= $newEndDate) {
                    while($oldEndDate < $newEndDate) {
                        $i++;
                        $oldEndDate = mktime(0, 0, 0, date("m", $oldEndDate), date("d", $oldEndDate)+1, date("Y", $oldEndDate));
                    }
                } else {
                    while($newEndDate < $oldEndDate){
                        $i--;
                        $newEndDate = mktime(0, 0, 0, date("m", $newEndDate), date("d", $newEndDate)+1, date("Y", $newEndDate));
                    }
                }
                $this->_linkedChangeTasks($taskId, $i);

            }
            $this->ProjectTask->id = $taskId;
            $this->ProjectTask->save($saved);
        }
    }
    /**
     * Xoa task
     */
    public function destroyTaskJson($id = null) {
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $this->layout = 'ajax';
        $success = true;
        if (!empty($id) && is_numeric($id) ) {
            /**
             * List task dc chon va task con
             */
            $allTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'ProjectTask.id' => $id,
                        'ProjectTask.parent_id' => $id
                    )
                ),
                'fields' => array('id', 'task_title', 'project_id')
            ));
			$project_id = !empty( $allTasks) ? $allTasks[0]['ProjectTask'] : 0;
			$this->_checkRole(false, $project_id);
            $listTasks = Set::classicExtract($allTasks, '{n}.ProjectTask.id');
            /**
             * Lay cac activity linked
             */
            $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $listTasks),
                'fields' => array('id', 'name')
            ));
            $listActivityTasks = Set::classicExtract($activityTasks, '{n}.ActivityTask.id');
            $hasUsed = 0;
            if (!empty($activityTasks)) {
                $hasUsed = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array('task_id' => $listActivityTasks, 'value != 0')
                ));
            }
            if ($hasUsed != 0) {
                $success = false;
                $result = sprintf(__('This task "%s" or its sub-tasks is in used/ has consumed', true), $this->data['task_title']);
                $data = null;
            } else {
                // xoa cac activity request luu dang 0.0
                $activity_tasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id
                    ),
                    'fields' => array('id', 'project_task_id')
                ));
                $activity_request = $this->ActivityRequest->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => array_keys($activity_tasks),
                        'status !=' => 2
                    ),
                    'fields' => array('id', 'id')
                ));
                if(!empty($activity_request)){
                    foreach ($activity_request as $_id) {
                        $this->ActivityRequest->delete($_id);
                    }
                }
                /**
                 * Xoa cac task duoc chon va cac task con
                 */

                foreach($listTasks as $taskId) {
                    $this->_deleteFile($taskId);
                }
                if ($this->ProjectTask->deleteAll(array('ProjectTask.id' => $listTasks), false)) {
                    /**
                     * Xoa cac assign.
                     */
                    $this->loadModel('ProjectTaskEmployeeRefer');
                    $this->loadModel('NctWorkload');
                    $this->NctWorkload->deleteAll(array('NctWorkload.project_task_id' => $listTasks), false);
                    $this->ProjectTaskEmployeeRefer->deleteAll(array('ProjectTaskEmployeeRefer.project_task_id' => $listTasks), false);
                    /**
                     * Xoa cac activity task linked voi project task
                     */
                    $this->ActivityTask->deleteAll(array('ActivityTask.project_task_id' => $listTasks), false);
                    //Tracking action
                    $list = array();
                    $project_id = 0;
                    foreach($allTasks as $task) {
                        $list[] = $task['ProjectTask']['task_title'];
                        $project_id = $task['ProjectTask']['project_id'];
                    }
                    $list2 = array();
                    foreach($activityTasks as $task) {
                        $list2[] = $task['ActivityTask']['name'];
                    }
                    $projectName = $this->ProjectTask->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id)));
                    //$this->writeLog(null, $this->employee_info, sprintf('Delete task `%s` under `%s` and activity task `%s` as well', implode('`, `', $list), @$projectName['Project']['project_name'], implode('`, `', $list2)));
                    $employ = $this->employee_info['Employee']['fullname'];
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  sprintf('Delete task `%s` under `%s` and activity task `%s` as well', implode('`, `', $list), @$projectName['Project']['project_name'], implode('`, `', $list2));
                    $this->writeLog(null, $this->employee_info, $message);
                }
                $this->ProjectTask->staffingSystem($this->data['project_id']);
                $this->_deleteCacheContextMenu();
                $success = $result = true;

            }
        }
        
        $result = array("success" => $success, "message" => $result);
        $this->set(compact('result'));
    }

    public function listPhasesJson($project_id) {
        $this->layout = 'ajax';

        $project = $this->_getProject($project_id);

        $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
        $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
            'fields' => array('id', 'phase_planed_start_date', 'phase_planed_end_date', 'project_part_id'),
            'contain' => array('ProjectPhase' => array('id', 'name'), 'ProjectPart' => array('id', 'title')),
            'conditions' => array(
                "ProjectPhasePlan.project_id" => $project_id,
                'company_id' => $project['Project']['company_id']
            )
        ));

        foreach ($projectPhases as $k => $projectPhase) {
            if (!empty($projectPhase['ProjectPart']['title'])) {
                $projectPhases[$k]['ProjectPhase']['name'] = $projectPhase['ProjectPart']['title'] . " / " . $projectPhase['ProjectPhase']['name'];
            } else {
                $projectPhases[$k]['ProjectPhase']['name'] = ($projectPhase['ProjectPhase']['name']);
            }
        }

        $result = $projectPhases;
        $this->set(compact('result'));
    }
    public function getHistory($project_id) {
        $this->loadModel('HistoryFilter');
        $settings = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'ProjectTaskSettings-' . $project_id,
                'employee_id' => $this->employee_info['Employee']['id']
            )
        ));
        if( empty($settings) ){
            $settings = $this->HistoryFilter->save(array(
                'id' => null,
                'path' => 'ProjectTaskSettings-' . $project_id,
                'employee_id' => $this->employee_info['Employee']['id'],
                'params' => '{}'
            ));
            $settings = array();
        }
        else $settings = json_decode($settings['HistoryFilter']['params'], true);
        return $settings;
    }
    public function handleFilterGantt() {
        $this->loadModel('HistoryFilter');
        $filter_gantt = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
            'conditions' => array(
                'path' => 'project_tasks_preview',
                'employee_id' => $this->employee_info['Employee']['id']
            )
        ));
		$type = 'month';
		$saved = array(
			'employee_id' => $this->employee_info['Employee']['id'],
			'path' => 'project_tasks_preview',
			'created' => time(),
			'updated' => time()
		);
        if( empty($filter_gantt) ){
			if(!empty($this->data)){
				$type = $this->data['params'];
				$saved['params'] =  $this->data['params'];
				$this->HistoryFilter->create();
			}
        }else{
			$filter_gantt = $filter_gantt['HistoryFilter'];
			$type = $filter_gantt['params'];
			if(!empty($this->data)){
				$type = $this->data['params'];
				$this->HistoryFilter->id = $filter_gantt['id'];
				$saved['params'] =  $this->data['params'];
				$saved['created'] =  $filter_gantt['created'];
			}
		}
		if(!empty($this->data)){
			$this->HistoryFilter->save($saved);
		}
		if(!empty($this->params['isAjax'])) die($type);
        return $type;
    }
    public function listPrioritiesJson($project_id) {
        $result = array();
        $project = $this->_getProject($project_id);
        //'company_id' => $projectName['Project']['company_id']
        $projectPriorities = $this->ProjectTask->ProjectPriority->find('all', array(
            'recursive' => -1,
			'order' => array('priority' => 'ASC'),
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
                )));

        foreach ($projectPriorities as $key => $priority) {
            $projectPriorities[$key]["ProjectPriority"]['id'] = $priority["ProjectPriority"]["id"];
            $projectPriorities[$key]["ProjectPriority"]['priority'] = $priority["ProjectPriority"]["priority"];
        }
        $result["Priorities"] = $projectPriorities;

        $projectStatus = $this->ProjectTask->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
                ),
			'order' => 'weight',
            ));

        $result["Statuses"] = $projectStatus;

        $listEmployee = $this->_getTeamEmployees($project_id);
        $result["Employees"] = $listEmployee;
        //todo: get profiles
        $this->loadModels('Profile', 'ProjectMilestone');
        $result['Profiles'] = $this->Profile->find('all', array(
            'recursive' => -1,
            'order' => array('name' => 'ASC'),
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
            )
        ));
        $projectMilestone = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'order' => array('project_milestone' => 'ASC'),
            'conditions' => array(
                'project_id' => $project_id
            )
        ));
		$result['Milestone'] = array();
		if(!empty($projectMilestone)){
			foreach($projectMilestone as $key => $miles){
				if(!(empty($miles['ProjectMilestone']['milestone_date']) || $miles['ProjectMilestone']['milestone_date'] == '0000-00-00')){
					$miles['ProjectMilestone']['project_milestone'] = $miles['ProjectMilestone']['project_milestone'] . ' ' . date('d-m-y',strtotime($miles['ProjectMilestone']['milestone_date']));
				}
				$result['Milestone'][$key] = $miles;
			}
		}
        $result['history'] = $this->getHistory($project_id);
        return json_encode($result);
    }
	public function updateStatusMilestone(){
		$this->layout = false;
		$this->loadModel('ProjectMilestone');
        $result = 0;
		if(!empty($this->data)){
			$project_id = $this->data['project_id'];
			$milestone_item = $this->data['id'];
			if( $this->_checkRole(true, $project_id)){
				$this_milestone = $this->ProjectMilestone->find("first", array(
					'recusive' => -1,
					"conditions" => array(
						"ProjectMilestone.id" => $milestone_item,
						"ProjectMilestone.project_id" => $project_id,
					),
					'fields' => array('*'),
				));
				if (!empty( $this_milestone )){
					$tmp_statu = 1; // not validated;
					$this_milestone['ProjectMilestone']['validated'] = intval(!$this_milestone['ProjectMilestone']['validated']);
					$this->data = $this_milestone;
					if( $this_milestone['ProjectMilestone']['validated'] ) {
						$tmp_statu = 2;
						$this_milestone['ProjectMilestone']['effective_date'] = time();
					}
					$this->data['ProjectMilestone']['effective_date'] = !empty($this_milestone['ProjectMilestone']['effective_date']) ? date('d-m-Y', $this_milestone['ProjectMilestone']['effective_date']) : '';
					unset($this_milestone['ProjectMilestone']['id']);
					$this->ProjectMilestone->id = $milestone_item;
					if ($this->ProjectMilestone->save($this_milestone)){
						$result = $tmp_statu;
					} else {
						$result = 0;
					}
				}
			}else{
				$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			}
		}
		die(json_encode($result));
	}

    public function listStatusesJson() {
        $this->layout = 'ajax';
        $projectStatus = $this->ProjectTask->ProjectStatus->find('all', array(
            'conditions' => array(
                'company_id' => $project['Project']['company_id']
            ),
			'order' => 'weight',
        ));
        foreach ($projectStatus as $key => $status) {
            $projectStatus[$key]["ProjectStatus"]['id'] = $status["ProjectStatus"]["id"];
            $projectStatus[$key]["ProjectStatus"]['status'] = $status["ProjectStatus"]["name"];
        }
        $result = $projectStatus;
        $this->set(compact('result'));
    }
    public function listEmployeesJson($project_id, $task_id, $start = null, $end = null) {
        $this->layout = 'ajax';
        $listEmployee = $this->_getTeamEmployeesForLoad($project_id, $task_id, $start, $end);
        $result = $listEmployee;

        $this->set(compact('result'));
    }

    public function saveEstimatedDetail(){
        $this->layout = 'ajax';
        $this->loadModel('ProjectTaskEmployeeRefer');
        if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }

            if (!is_null($jsonData) and $jsonData != false) {
                $this->data = $jsonData;
            }
        }

        if ($this->_developersMode) {
            $this->log("project_tasks_controller->saveEstimatedDetail() :: \$_POST");
            $this->log($_POST);
        }

        if (isset($_POST['Reference'])) {
            $tasksId = $_POST['ProjectTaskId'];
            $totalEstimated = $_POST['totalEstimated'];
            $estimateds = $_POST['Reference'];
            $referenceIds = array_keys($estimateds);
            $datas = array();
            foreach($referenceIds as $referenceId){
                $split = explode('_', $referenceId);
                 $taskReferences = $this->ProjectTaskEmployeeRefer->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $tasksId, 'reference_id' => $split[0], 'is_profit_center' => $split[1]),
                    // 'fields' => array('id', 'reference_id', 'is_profit_center')
                ));
                $datas[] = $taskReferences;
            }
            foreach($datas as $key => $data){
                $this->ProjectTaskEmployeeRefer->id = $data['ProjectTaskEmployeeRefer']['id'];
                $is_profit_center = $data['ProjectTaskEmployeeRefer']['is_profit_center'] === null ? 0 : $data['ProjectTaskEmployeeRefer']['is_profit_center'];
                $ids = $data['ProjectTaskEmployeeRefer']['reference_id'] .'_'. $data['ProjectTaskEmployeeRefer']['is_profit_center'];
                if($ids == '_') continue;
                $_data['estimated'] = $estimateds[$ids];
                $_result[] = $this->ProjectTaskEmployeeRefer->save($_data);
            }
        }
        $result = $_result;
        $result = array("success" => true, "message" => $result, "total" => $totalEstimated);
        $this->set(compact('result'));
    }

    // remain settings
    private function _remain(){
        $this->loadModel('ProjectTask');
        $project_id = $this->_projects();
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_id', 'estimated', 'allow_request')
        ));
        $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
        $taskRequests = $this->_projectRequest();
        foreach($projectTasks as $id => $projectTask){
            $projectTasks[$id]['consumed'] = isset($taskRequests[$id]) ? $taskRequests[$id] : 0;
            $projectTasks[$id]['estimated'] = isset($projectTask['estimated']) ? $projectTask['estimated'] : 0;
        }
        $remains = array();
        foreach($projectTasks as $id => $projectTask){
            $remains[$id]['id'] = $projectTask['id'];
            $remains[$id]['remain'] = $projectTask['estimated'] - $projectTask['consumed'];
            $remains[$id]['allow_request'] = $projectTask['allow_request'];
        }
        foreach($remains as $key => $remain){
            if($remain['remain'] != 0){
                unset($remains[$key]);
            }
        }
        $remains = array_unique(Set::classicExtract($remains, '{n}.allow_request'));

        return $remains;
    }

    private function _projectRequest(){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');

        $employee = $this->Session->read("Auth.employee_info");
        // $projectName = $this->viewVars['employee_info'];
        $projectName = $employee;

        $activityRequests = $this->ActivityRequest->find('all', array(
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
                'company_id' => $projectName['CompanyEmployeeReference']['company_id'],
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $activityRequests = Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0.value');
        $listId = array_keys($activityRequests);

        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.id' => $listId),
            'fields' => array('id', 'project_task_id')
        ));
        $requestTasks = array();
        foreach($activityTasks as $activityTask){
            foreach($activityRequests as $id => $activityRequest){
                if($id == $activityTask['ActivityTask']['id']){
                    $requestTasks[$id]['id'] = $activityTask['ActivityTask']['id'];
                    $requestTasks[$id]['project_task_id'] = $activityTask['ActivityTask']['project_task_id'];
                    $requestTasks[$id]['consumed'] = $activityRequest;
                }
            }
        }
        $requestTasks = Set::combine($requestTasks, '{n}.project_task_id', '{n}.consumed');
        return $requestTasks;
    }

    private function _projects(){
        $this->loadModel('Project');
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id')
        ));
        return $projects;
    }

    private function _dataExport($project_id = null) {
        $this->_checkRole(false, $project_id);
        // load tat cac cac task cua project task
        $projectTasks = $this->_getAllProjectTasks($project_id);
        // lay danh sach id cua project task
        $listIdOfProjectTasks = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
        // Load activity tasks lien ket voi project task
        $activityTasks = $this->_getActivityTasks($listIdOfProjectTasks);
        // Attach Consume Calculator from RMS
        $activityRequests = $this->_getActivityRequests($project_id, $activityTasks);
        // Attach Employees Assigned to Project Tasks Array
        $projectTasks = $this->_attachReferencedEmployees($projectTasks);
        // Attach Consumed to Project Tasks Array
        $projectTasks = $this->_attachConsumed($projectTasks, $activityTasks, $activityRequests);
        $children = $this->_buildJsonStructure($projectTasks, $project_id);
        $result = $children[0];
        return $result;
    }

    public function delete_data_null(){
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'task_title', 'parent_id')
        ));
        foreach($projectTasks as $projectTask){
            if (!empty($projectTask['ProjectTask']['task_title'])) {
                //do no thing
            } else {
                $this->ProjectTask->delete($projectTask['ProjectTask']['id']);
            }
            if ($projectTask['ProjectTask']['parent_id'] > 999999999) {
                $this->ProjectTask->id = $projectTask['ProjectTask']['id'];
                $data['parent_id'] = 0;
                $this->ProjectTask->save($data);
            }
        }
        pr('The data delete succefull!');
        exit;
    }

    /**
     * Get project task By ID
     *
     * @return void
     * @access private
     */
    private function _projectTask($project_id = null){
        $this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'estimated')
        ));
        return $projectTasks;
    }

    private function _caculateOverload($project_id = null){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('Project');
        $this->loadModel('ProjectTeam');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $this->loadModel('ProjectEmployeeManager');

        $project = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('activity_id')
        ));
        $activityId = Set::classicExtract($project, 'Project.activity_id');

        $employeeName = $this->_getEmpoyee();
        $projectTasks = $this->_projectTask($project_id);
        $idProjecTasks = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();

        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.project_task_id' => $idProjecTasks),
            'fields' => array('project_task_id', 'id')
        ));
        $datas = $this->ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array(
                'id', 'date',
                'employee_id',
                'task_id',
                'SUM(value) as consumed'
            ),
            'conditions'    => array(
                'status'        => 2,
                'task_id'       => $activityTasks,
                'company_id'    => $employeeName['company_id'],
                'NOT'           => array('value' => 0, "task_id" => null),
            ),
            'group' => array('task_id')
        ));
        $datas = !empty($datas) ? Set::combine($datas, '{n}.ActivityRequest.task_id', '{n}.0.consumed') : array();
        $consumeds = array();
        foreach($activityTasks as $projectId => $activityTask){
            $consumeds[$projectId] = !empty($datas[$activityTask]) ? $datas[$activityTask] : 0;
        }
        $results = array();
        foreach($projectTasks as $key => $projectTask){
            if (isset($consumeds[$projectTask['ProjectTask']['id']]) && $consumeds[$projectTask['ProjectTask']['id']] > $projectTask['ProjectTask']['estimated']) {
                $results[$projectTask['ProjectTask']['id']]['overload'] = $consumeds[$projectTask['ProjectTask']['id']] - $projectTask['ProjectTask']['estimated'];
            }
        }
        if (!empty($results)) {
            foreach($results as $id => $result){
                $this->ProjectTask->id = $id;
                $saved['overload'] = $result['overload'];
                $this->ProjectTask->save($saved);
            }
        }
    }

    public function add_over_load(){
        $this->loadModel('Project');
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        foreach($projects as $project){
            $this->_caculateOverload($project);
        }
        pr('succesfull!');
        exit;
    }

    //ham nay de chinh chua workload
    public function edit_workload($project_id){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $project = $this->_getProject($project_id);

        $projectTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id')
        ));
        $taskRequests = array();
        if (!empty($projectTasks)) {
            $projectTasks = isset($projectTasks) ? $projectTasks : array();
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $projectTasks),
                'fields' => array('project_task_id', 'id')
            ));
            $activityTasks = isset($activityTasks) ? $activityTasks : array();
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
                    'company_id' => $project['Project']['company_id'],
                    'NOT' => array('value' => 0, "task_id" => null),
                ))
            );
            if (!empty($activityRequests)) {
                $requests = Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0.value');
                foreach($activityTasks as $id => $activityTask){
                    if(in_array($activityTask, array_keys($requests))){
                        $taskRequests[$id] = $requests[$activityTask];
                    }
                }
            }
        }
        if(!empty($taskRequests)){
            $tasks = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => array_keys($taskRequests)),
                'fields' => array('id', 'estimated')
            ));
            $datas = array();
            foreach($tasks as $id => $task){
                foreach($taskRequests as $key => $taskRequest){
                    if($id == $key){
                        if($task >= $taskRequest){
                            $_datas['id'] = $id;
                            $_datas['estimated'] = $task;
                        } else {
                            $_datas['id'] = $id;
                            $_datas['estimated'] = $taskRequest;
                        }
                        $datas[$id] = $_datas;
                    }
                }
            }
        }
        return 'ham chua hoan chinh';
    }

    private function _capacityForTask($project_id, $projectTaskId) {
        $this->loadModel('ProjectTaskEmployeeRefer');

        $listAssiged = $this->ProjectTaskEmployeeRefer->find('list',array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $projectTaskId),
            'fields' => array('reference_id','id')
        ));
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'ProjectTask.id' => $projectTaskId),
            'fields' => array('id', 'task_start_date', 'task_end_date')
        ));
        $employeeAndPcs = $this->_getTeamEmployees($project_id);
        $currentWeek = $this->_currentWeek();
        $_datas = array();
        $tmpCreateTasks[] = array(
            'ProjectTask' => array(
                'id' => 999999999,
                'task_start_date' => $currentWeek['start'],
                'task_end_date' => $currentWeek['end']
            )
        );
        if (empty($projectTasks)) {
            $projectTasks = $tmpCreateTasks;
        }
        if (!empty($projectTasks)) {
            //$projectTasks = array_merge($projectTasks, $tmpCreateTasks);
            foreach($projectTasks as $projectTask){
                $start = !empty($projectTask['ProjectTask']['task_start_date']) && $projectTask['ProjectTask']['task_start_date'] != '0000-00-00' ? $projectTask['ProjectTask']['task_start_date'] : $currentWeek['start'];
                $end = !empty($projectTask['ProjectTask']['task_end_date']) && $projectTask['ProjectTask']['task_end_date'] != '0000-00-00' ? $projectTask['ProjectTask']['task_end_date'] : $currentWeek['end'];
                if(!empty($employeeAndPcs)){
                    foreach($employeeAndPcs as $key => $employeeAndPc){
                        if($employeeAndPc['Employee']['is_profit_center'] == 1){
                        //    $dataPcs = $this->_capacityProfitCenter($employeeAndPc['Employee']['id'], $start, $end);
                        //    $onPcs = $dataPcs['on'];
                        //    $employeeAndPcs[$key]['Employee']['capacity_on'] = $onPcs;
                        //    $offPcs = $dataPcs['off'];
                        //    $employeeAndPcs[$key]['Employee']['capacity_off'] = $offPcs;
                        //    $capaPcs = $dataPcs['capa'];
                        //    $employeeAndPcs[$key]['Employee']['capacity'] = $capaPcs;
                        //    $listPcs = $dataPcs['list'];
                        //    $employeeAndPcs[$key]['Employee']['listProfile'] = $listPcs;
                        } else {
                            $dataEmployee = $this->_capacityEmployee($employeeAndPc['Employee']['id'], $start, $end);
                            $onEmploy = $dataEmployee['on'];
                            $employeeAndPcs[$key]['Employee']['capacity_on'] = round($onEmploy,2);
                            $offEmploy = $dataEmployee['off'];
                            $employeeAndPcs[$key]['Employee']['capacity_off'] = round($offEmploy,2);
                            $capaEmploy = $dataEmployee['capa'];
                            $employeeAndPcs[$key]['Employee']['capacity'] = $capaEmploy;
                            $listEmploy = $dataEmployee['list'];
                            $employeeAndPcs[$key]['Employee']['listProfile'] = $listEmploy;
                        }
                        if (!empty($listAssiged[$employeeAndPc['Employee']['id']])) {
                            $employeeAndPcs[$key]['Employee']['is_selected'] = 1;
                        } else {
                            $employeeAndPcs[$key]['Employee']['is_selected'] = 0;
                        }
                    }
                    $_datas[$projectTask['ProjectTask']['id']] = $employeeAndPcs;
                }
            }
        }
        $datas = array();
        if (!empty($_datas)) {
            foreach($_datas as $taskId => $_data){
                foreach($_data as $key => $value){
                    $value['Employee']['listProfile'] = !empty($value['Employee']['listProfile']) ? array_unique($value['Employee']['listProfile']) : array();
                    $datas[$taskId][$key] = $value['Employee'];
                }
            }
        }

        $result = $datas;
        $this->set(compact('result'));
    }

    private function _currentWeek(){
        $startDate = date("Y-m-d",strtotime('monday this week'));
        $endDate = date("Y-m-d",strtotime("sunday this week"));
        $result['start'] = $startDate;
        $result['end'] = $endDate;
        return $result;
    }

    private function _capacityProfitCenter($profitId = null, $start = null, $end = null){
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $employees = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('profit_center_id' => $profitId),
            'fields' => array('employee_id', 'employee_id')
        ));
        $getDatas = array();
        if (!empty($employees)) {
            foreach($employees as $employee){
                $getDatas[$employee] = $this->_capacityEmployee($employee, $start, $end);
            }
        }
        $datas = array();
        $on = $off = $capa = 0;
        $listProjects = array();
        if (!empty($getDatas)) {
            foreach($getDatas as $getData){
                $on += $getData['on'];
                $off += $getData['off'];
                $capa += $getData['capa'];
                $listProjects[] = $getData['list'];
            }
        }
        $_projects = array();
        if (!empty($listProjects)) {
            foreach($listProjects as $listProject){
                foreach($listProject as $vl){
                    $_projects[] = $vl;
                }
            }
        }
        $datas['capa'] = $capa;
        $datas['on'] = $on;
        $datas['off'] = $off;
        $datas['list'] = $_projects;
        return $datas;
    }

    private function _capacityEmployee($employee_id = null, $start = null, $end = null){
        $rangeDate = $this->getWorkingDays($start, $end, '');
        $holiday = $this->_absenceAndHolidayOfEmployee($employee_id, strtotime($start), strtotime($end));
        $capacity = $rangeDate - $holiday;
        $getWorkload = $this->_workloadFromForecast($employee_id, $start, $end);
        $workload = $getWorkload['worload'];
        $listProject = $getWorkload['project'];
        $on = $capacity - $workload;
        if ($on < 0) {
            $on = 0;
        }
        $datas['capa'] = $capacity;
        $datas['on'] = $on;
        $datas['off'] = $workload;
        $datas['list'] = $listProject;
        return $datas;
    }

    private function _absenceAndHolidayOfEmployee($employee_id, $startDate, $endDate){
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ActivityForecast');
        $employeeName = $this->_getEmpoyee();
        $holidays   = ClassRegistry::init('Holiday')->getOptions($startDate, $endDate, $employeeName['company_id']);
        $requestQuery = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            "conditions" => array(
                'date BETWEEN ? AND ?' => array($startDate, $endDate),
                'employee_id' => $employee_id
            ),
            'fields' => array('date', 'absence_am', 'absence_pm', 'response_am', 'response_pm')
        ));
        $forcastsQuery = $this->ActivityForecast->find("all", array(
            'recursive' => -1,
            "conditions" => array(
                'date BETWEEN ? AND ?' => array($startDate, $endDate),
                'employee_id' => $employee_id
            )
        ));
        $total = $totalAbcense = $totalRequest = $totalHoliday = 0;
        foreach($requestQuery as $request){
            foreach(array('am', 'pm') as $value){
                if($request['AbsenceRequest']['absence_' . $value] && $request['AbsenceRequest']['response_' . $value] == 'validated'){
                    $totalAbcense += 0.5;
                }
            }
        }
        foreach($forcastsQuery as $request){
            foreach(array('am', 'pm') as $value){
                if($request['ActivityForecast']['activity_' . $value] && $request['ActivityForecast']['activity_' . $value] != 0){
                    $totalRequest += 0.5;
                }
            }
        }
        foreach($holidays as $holiday){
            if(!empty($holiday['am'])){
                $totalHoliday += 0.5;
            }
            if(!empty($holiday['pm'])){
                $totalHoliday += 0.5;
            }
        }
        $total = $totalAbcense + $totalHoliday;
        return $total;
    }

    private function _workloadFromForecast($employee = null, $star = null, $end = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('Project');
        $this->loadModel('ProjectPhase');
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ActivityTask');
        $this->loadModel('Activity');

        $employeeName = $this->_getEmpoyee();
        $company_id = $employeeName['company_id'];
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'employee_id', 'task_id', 'date', 'value'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $company_id,
                'employee_id' => $employee,
                'date BETWEEN ? AND ?'  => array($star, $end),
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $_requests = array();
        if(!empty($activityRequests)){
            $activityRequests = Set::combine($activityRequests, '{n}.ActivityRequest.id', '{n}.ActivityRequest');
            $employs = array_unique(Set::classicExtract($activityRequests, '{n}.employee_id'));
            $tasks = array_unique(Set::classicExtract($activityRequests, '{n}.task_id'));
            $times = array_unique(Set::classicExtract($activityRequests, '{n}.date'));
            foreach($employs as $employ){
                foreach($tasks as $task){
                    foreach($times as $time){
                        foreach($activityRequests as $activityRequest){
                            if ($employ == $activityRequest['employee_id'] && $task == $activityRequest['task_id'] && $time == $activityRequest['date'])
                            $_requests[$employ][$task][$time] = $activityRequest['value'];
                        }
                    }
                }
            }
        }
        $projectIds = ClassRegistry::init('Project')->find('list', array(
                'recursive' => -1,
                'conditions' => array('NOT' => array('activity_id' => null)),
                'fields' => array('id', 'id')
            ));
        $listIds = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ProjectTaskEmployeeRefer.reference_id' => $employee,
                'ProjectTaskEmployeeRefer.is_profit_center' => 0
            ),
            'fields' => array(
                'ProjectTask.id'
            )
        ));
        $listIds = !empty($listIds) ? Set::classicExtract($listIds, '{n}.ProjectTask.id') : array();
        $projectTasks = $this->ProjectTask->find('all', array(
            'conditions' => array('ProjectTask.id' => $listIds),
            'fields' => array('id', 'task_title', 'task_start_date', 'task_end_date', 'estimated', 'project_id', 'project_planed_phase_id')
        ));
        foreach($projectTasks as $key => $projectTask){
            $projectTask['ProjectTask']['task_start_date'] = !empty($projectTask['ProjectTask']['task_start_date']) ? strtotime($projectTask['ProjectTask']['task_start_date']) : 0;
            $projectTask['ProjectTask']['task_end_date'] = !empty($projectTask['ProjectTask']['task_end_date']) ? strtotime($projectTask['ProjectTask']['task_end_date']) : 0;
            //if(($star <= $projectTask['ProjectTask']['task_start_date'] && $projectTask['ProjectTask']['task_start_date'] <= $end) || ($star <= $projectTask['ProjectTask']['task_end_date'] && $projectTask['ProjectTask']['task_end_date'] <= $end)){
                if (!empty($projectTask['ProjectTaskEmployeeRefer'])) {
                    if (count($projectTask['ProjectTaskEmployeeRefer']) == 1) {
                        if ($projectTask['ProjectTaskEmployeeRefer'][0]['is_profit_center'] == 0) {
                            if ($projectTask['ProjectTaskEmployeeRefer'][0]['reference_id'] == $employee) {
                                $projectTasks[$key]['ProjectTaskEmployeeRefer'][0]['estimated'] = $projectTask['ProjectTask']['estimated'];
                                // chuan bi lam 1 viec gi do
                            } else {
                                unset($projectTasks[$key]);
                            }
                        } else {
                            unset($projectTasks[$key]['ProjectTaskEmployeeRefer'][0]);
                        }
                    } else {
                        foreach($projectTask['ProjectTaskEmployeeRefer'] as $k => $vl){
                            if ($vl['is_profit_center'] == 0) {
                                if ($vl['reference_id'] == $employee) {
                                     //chuan bi lam 1 viec gi do
                                } else {
                                    unset($projectTasks[$key]['ProjectTaskEmployeeRefer'][$k]);
                                }
                            } else {
                                unset($projectTasks[$key]['ProjectTaskEmployeeRefer'][$k]);
                            }
                        }
                    }
                } else {
                    unset($projectTasks[$key]);
                }
            //} else {
            //    unset($projectTasks[$key]);
            //}
        }
        $projectTaskIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.id'));
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $projectTaskIds),
            'fields' => array('project_task_id', 'id')
        ));
        $projectPhasePlans = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_planed_phase_id')
        ));
        $setupDatas = array();
        foreach($projectTasks as $key => $projectTask){
            if (!empty($projectTask['ProjectTaskEmployeeRefer'])) {
                $_start = !empty($projectTask['ProjectTask']['task_start_date']) ? ($projectTask['ProjectTask']['task_start_date']) : $star;
                $_end = !empty($projectTask['ProjectTask']['task_end_date']) ? ($projectTask['ProjectTask']['task_end_date']) : $end;
                foreach($projectTask['ProjectTaskEmployeeRefer'] as $k => $vl){
                    $setupDatas[$key][$k]['task_id'] = isset($activityTasks[$projectTask['ProjectTask']['id']]) ? $activityTasks[$projectTask['ProjectTask']['id']] : 0;
                    $setupDatas[$key][$k]['employee_id'] = $vl['reference_id'];
                    $setupDatas[$key][$k]['task_title'] = $projectTask['ProjectTask']['task_title'];
                    $setupDatas[$key][$k]['start'] = $_start;
                    $setupDatas[$key][$k]['end'] = $_end;
                    $setupDatas[$key][$k]['estimated'] = $vl['estimated'];
                    $setupDatas[$key][$k]['project_id'] = $projectTask['ProjectTask']['project_id'];
                    $phaseId = isset($projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key][$k]['phase_id'] = $phaseId;
                }
            } else {
                unset($projectTasks[$key]);
            }
        }
        //@Huupc: list activity task
        $listActivityIds = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ActivityTaskEmployeeRefer.reference_id' => $employee,
                'ActivityTaskEmployeeRefer.is_profit_center' => 0
            ),
            'fields' => array(
                'ActivityTask.id'
            )
        ));
        $listActivityIds = !empty($listActivityIds) ? Set::classicExtract($listActivityIds, '{n}.ActivityTask.id') : array();
        $actiTasks = $this->ActivityTask->find('all', array(
            'conditions' => array('ActivityTask.id' => $listActivityIds),
            'fields' => array('id', 'name', 'task_start_date', 'task_end_date', 'estimated', 'activity_id')
        ));
        foreach($actiTasks as $key => $actiTask){
            //if(($star <= $actiTask['ActivityTask']['task_start_date'] && $actiTask['ActivityTask']['task_start_date'] <= $end) || ($star <= $actiTask['ActivityTask']['task_end_date'] && $actiTask['ActivityTask']['task_end_date'] <= $end)){
                if(!empty($actiTask['ActivityTaskEmployeeRefer'])){
                    if(count($actiTask['ActivityTaskEmployeeRefer']) == 1){
                        if($actiTask['ActivityTaskEmployeeRefer'][0]['is_profit_center'] == 0){
                            if($actiTask['ActivityTaskEmployeeRefer'][0]['reference_id'] == $employee){
                                $actiTasks[$key]['ActivityTaskEmployeeRefer'][0]['estimated'] = $actiTask['ActivityTask']['estimated'];
                            } else {
                                unset($actiTasks[$key]);
                            }
                        } else {
                            unset($actiTasks[$key]['ActivityTaskEmployeeRefer'][0]);
                        }
                    } else {
                        foreach($actiTask['ActivityTaskEmployeeRefer'] as $k => $vl){
                            if($vl['is_profit_center'] == 0){
                                if($vl['reference_id'] == $employee){
                                    //do nothing
                                } else {
                                    unset($actiTasks[$key]['ActivityTaskEmployeeRefer'][$k]);
                                }
                            } else {
                                unset($actiTasks[$key]['ActivityTaskEmployeeRefer'][$k]);
                            }
                        }
                    }
                } else {
                    unset($actiTasks[$key]);
                }
            //} else {
            //    unset($actiTasks[$key]);
            //}
        }
        foreach($actiTasks as $key => $actiTask){
            if (!empty($actiTask['ActivityTaskEmployeeRefer'])) {
                $_startAT = !empty($actiTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_start_date']) : $star;
                $_endAT = !empty($actiTask['ActivityTask']['task_end_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_end_date']) : $end;
                foreach($actiTask['ActivityTaskEmployeeRefer'] as $k => $vl){
                    $setupDatasAT[$key][$k]['task_id'] = $actiTask['ActivityTask']['id'];
                    $setupDatasAT[$key][$k]['employee_id'] = $vl['reference_id'];
                    $setupDatasAT[$key][$k]['task_title'] = $actiTask['ActivityTask']['name'];
                    $setupDatasAT[$key][$k]['start'] = $_startAT;
                    $setupDatasAT[$key][$k]['end'] = $_endAT;
                    $setupDatasAT[$key][$k]['estimated'] = $vl['estimated'];
                    $setupDatasAT[$key][$k]['project_id'] = 0;
                    $setupDatasAT[$key][$k]['phase_id'] = 0;
                    $setupDatasAT[$key][$k]['activity_id'] = $actiTask['ActivityTask']['activity_id'];
                }
            } else {
                unset($actiTasks[$key]);
            }
        }
        $setupDatas = !empty($setupDatasAT) ? array_merge($setupDatas, $setupDatasAT) : $setupDatas;
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_name')
        ));
        $projectPhases = $this->ProjectPhase->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));
        $actiId = !empty($actiTasks) ? Set::classicExtract($actiTasks, '{n}.ActivityTask.activity_id') : array();
        $activities = $this->Activity->find('list', array(
                'conditions' => array('Activity.id' => $actiId),
                'fields' => array('id', 'name')
            ));
        $datas = array();
        if (!empty($setupDatas)) {
            foreach($setupDatas as $key => $setupData){
                foreach($setupData as $k => $value){
                    $dates = $this->getWorkingDays($value['start'], $value['end'], '');
                    $_start = !empty($value['start']) ? strtotime($value['start']) : 0;
                    $_end = !empty($value['end']) ? strtotime($value['end']) : 0;
                    while($_start <= $_end){
                        $cSHoliday = strtolower(date("l", $_start));
                        if ($cSHoliday == 'saturday' || $cSHoliday == 'sunday') {
                            //do nothing
                        } else {
                            $datas[$key][$k][$_start]['task_id'] = $value['task_id'];;
                            $datas[$key][$k][$_start]['date'] = $_start;
                            $datas[$key][$k][$_start]['employee_id'] = $value['employee_id'];
                            $namePR = !empty($projects[$value['project_id']]) ? $projects[$value['project_id']] : '';
                            $namePP = !empty($projectPhases[$value['phase_id']]) ? $projectPhases[$value['phase_id']] : '';
                            $nameTask = !empty($value['task_title']) ? $value['task_title'] : '';
                            if ($value['project_id'] == 0 && $value['phase_id'] == 0) {
                                $nameAC = !empty($activities[$value['activity_id']]) ? $activities[$value['activity_id']] : '';
                                $groupName = 'Activity: ' .$nameAC . ' / ' . $nameTask;
                            } else {
                                $groupName = 'Project: ' .$namePR . ' / ' . $namePP . ' / ' . $nameTask;
                            }
                            $datas[$key][$k][$_start]['name'] = $groupName;
                            if ($dates == 0) {
                                $datas[$key][$k][$_start]['workload'] = 0;
                            } else {
                                $datas[$key][$k][$_start]['workload'] = round($value['estimated']/$dates, 2);
                            }
                            if (!empty($_requests)) {
                                $datas[$key][$k][$_start]['consumed'] = isset($_requests[$value['employee_id']][$value['task_id']][$_start]) ? $_requests[$value['employee_id']][$value['task_id']][$_start] : 0;
                                $datas[$key][$k][$_start]['remain'] = $datas[$key][$k][$_start]['workload'] - $datas[$key][$k][$_start]['consumed'];
                            } else {
                                $datas[$key][$k][$_start]['remain'] = $datas[$key][$k][$_start]['workload'];
                            }

                            if($datas[$key][$k][$_start]['remain'] < 0){
                                $datas[$key][$k][$_start]['remain'] = 0;
                            }
                        }
                        $_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));
                    }
                }
            }
        }
        $rDatas = $results = array();
        if(!empty($datas)){
            foreach($datas as $k => $data){
                foreach($data as $key => $values){
                    foreach($values as $_time => $vl){
                        if (strtotime($star) <= $_time && $_time <= strtotime($end)) {
                            //do nothing
                            $cTimes[] = $vl['date'];
                            $cEmployees[] = $vl['employee_id'];
                            $rDatas[] = $vl;
                        } else {
                            unset($datas[$k][$key][$_time]);
                        }
                    }
                }
            }
            if (!empty($cTimes) && !empty($cEmployees)) {
                $cTimes = array_unique($cTimes);
                $cEmployees = array_unique($cEmployees);
                foreach($cEmployees as $cEmployee){
                    foreach($cTimes as $cTime){
                        foreach($rDatas as $rData){
                            if ($cEmployee == $rData['employee_id'] && $cTime == $rData['date']) {
                                $_rData = array(
                                    //'task_id' => $rData['task_id'],
                                    'date' => $rData['date'],
                                    //'employee_id' => $rData['employee_id'],
                                    'name' => $rData['name'],
                                    'workload' => $rData['remain']
                                    //'remain_am' => $rData['remain']/2,
                                    //'remain_pm' => $rData['remain']/2
                                );
                                $results[strtolower(date('l', $cTime))][] = $_rData;
                            }
                        }
                    }
                }
            }
        }
        $workload = 0;
        $listProjects = array();
        if(!empty($results)) {
            foreach($results as $result){
                foreach($result as $values){
                    $workload += $values['workload'];
                    $listProjects[] = $values['name'];
                }
            }
        }
        $_listProjects = !empty($listProjects) ? array_unique($listProjects) : array();
        $endData['worload'] = $workload;
        $endData['project'] = $_listProjects;
        return $endData;
    }

    /**
     * Ham nay dung de thay doi gia tri request cu the la:
     * O project task co 1 task id la: 802 -> tuong ung voi activity task id la: 405.
     * Task nay da co request o? activity request voi taks_id = 405.
     *
     * Task id là 802 co sub task la 803 -> tuong ung voi activity task id la: 826
     * Gio tien hanh thay doi nhung request cua id 405 thay bang 826.
     *
     * Muc dich la dua request cua 802 cho thang 803 dung
     *
     */
    public function changeRequest(){
        $this->loadModel('ActivityRequest');
        $requests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array('task_id' => 405),
            'fields' => array('id')
        ));
        if(!empty($requests)){
            foreach($requests as $request){
                $this->ActivityRequest->id = $request;
                $saved['task_id'] = 826;
                $this->ActivityRequest->save($saved);
            }
        }
        pr('Success!');
        exit;
    }

    public function setWorkload(){
        $this->ProjectTask->cacheQueries = true;
        $this->ProjectTask->Behaviors->attach('Containable');
        $datas = $this->ProjectTask->find('all', array(
            'contain' => array('ProjectTaskEmployeeRefer')
        ));
        foreach($datas as $key => $data){
            if(empty($data['ProjectTaskEmployeeRefer']) || count($data['ProjectTaskEmployeeRefer']) > 1){
                unset($datas[$key]);
            }
        }
        $this->loadModel('ProjectTaskEmployeeRefer');
        foreach($datas as $data){
            $this->ProjectTaskEmployeeRefer->id = $data['ProjectTaskEmployeeRefer'][0]['id'];
            $_data['estimated'] = $data['ProjectTask']['estimated'];
            $this->ProjectTaskEmployeeRefer->save($_data);
        }

    }

    private function _ganttFromPhase($project_id){
        $this->loadModel('Project');
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $projects = array();
        $projects = $this->Project->find('all', array(
            'conditions' => array('Project.id' => $project_id),
            'contain' => array(
                'ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ),
                'ProjectPhase' => array('name', 'color')
                )
            ),
            'fields' => array('project_name', 'start_date', 'end_date', 'planed_end_date')
        ));
        //set default real time
        $displayplan = isset($this->params['url']['displayplan']) ? (int) $this->params['url']['displayplan'] : 0;
        $displayreal = isset($this->params['url']['displayreal']) ? (int) $this->params['url']['displayreal'] : 1;
        $this->set(compact('projects', 'display', 'displayreal', 'displayplan'));
    }

    public function getprojecttask($id_project_task = null){
        $this->layout = 'ajax';
        $result  = $this->ProjectTask->find('first',array(
            'conditions' => array('ProjectTask.id' => $id_project_task),
            'recursive' => -1
        ));
        $this->set(compact('result'));

    }

    public function getAllProjectTaskForEmployee($id_employee = null, $stardate , $enddate){
        $this->layout = 'ajax';
        $this->loadModel('Holiday');
        $this->loadModel('Project');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectPriority');
        $projectPriority = $this->ProjectPriority->find('list');
        $listprojecttask = $this->ProjectTaskEmployeeRefer->find('all',array(
            'conditions' => array(
                'reference_id' => $id_employee,
                'is_profit_center' => 0
                ),
            'recursive' => -1
        ));
        $listProjectTaskFromDates = array();
        foreach($listprojecttask as $key => $value){
          $listProjectTaskFromDate =  $this->ProjectTask->find('first',array(
                'conditions' => array(
                    'AND' => array(
                        'OR' => array(
                            'ProjectTask.task_start_date BETWEEN ? AND ?' => array($stardate, $enddate),
                            'ProjectTask.task_end_date BETWEEN ? AND ?' => array($stardate, $enddate),
                            'AND' => array(
                                    'ProjectTask.task_start_date <' =>  $stardate ,
                                    'ProjectTask.task_end_date >' => $enddate
                            ),
                        )

                    ),
                   'ProjectTask.id' => $value['ProjectTaskEmployeeRefer']['project_task_id'],
                   'ProjectTask.task_start_date !=' => '0000-00-00'
                ),
                'recursive' => -1
            ));
            if (!empty($listProjectTaskFromDate)) {
                $listProjectTaskFromDate['ProjectTask']['employee_estimated'] = $value['ProjectTaskEmployeeRefer']['estimated'];
                if($listProjectTaskFromDate['ProjectTask']['task_end_date']=='0000-00-00'){
                    $listProjectTaskFromDate['ProjectTask']['task_end_date'] = $listProjectTaskFromDate['ProjectTask']['task_start_date'];
                }
                if($listProjectTaskFromDate['ProjectTask']['task_start_date'] =='0000-00-00'){
                    $listProjectTaskFromDate['ProjectTask']['task_start_date'] = $listProjectTaskFromDate['ProjectTask']['task_end_date'];
                }
                if($listProjectTaskFromDate['ProjectTask']['task_start_date'] > $listProjectTaskFromDate['ProjectTask']['task_end_date'] ){
                    $listProjectTaskFromDate['ProjectTask']['task_start_date'] = $listProjectTaskFromDate['ProjectTask']['task_end_date'];
                }


                if(!empty($projectPriority[$listProjectTaskFromDate['ProjectTask']['task_priority_id']])){
                    $listProjectTaskFromDate['ProjectTask']['priority_title'] =    $projectPriority[$listProjectTaskFromDate['ProjectTask']['task_priority_id']];
                }else{
                    $listProjectTaskFromDate['ProjectTask']['priority_title'] = "";
                }

                $_project  =  $this->Project->find('first',array(
                    'conditions' => array('Project.id' =>  $listProjectTaskFromDate['ProjectTask']['project_id']),
                    'recursive' => -1,
                    'fields' => array("project_name")
                ));
                $listProjectTaskFromDate['ProjectTask']['project_name'] = $_project['Project']['project_name'];
                $listProjectTaskFromDates[] = $listProjectTaskFromDate;
            }
        }
        $result = $this->_addWlForTask($listProjectTaskFromDates,$stardate,$enddate,$id_employee);
        $activitylist = $this->_getActitivyTask($id_employee, $stardate , $enddate);

        if (!empty($activitylist)) {
            foreach($activitylist as $__key => $__value){
                $result[] = $__value;
            }
        }

        $this->set(compact('result'));
    }
    private function _getActitivyTask($id_employee = null, $stardate , $enddate){
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ActivityTask');
        $this->loadModel('Activity');
        $this->loadModel('ProjectPriority');
        $this->loadModel('Activity');
        $projectPriority = $this->ProjectPriority->find('list');
         $listActivitytask = $this->ActivityTaskEmployeeRefer->find('all',array(
            'conditions' => array(
                'reference_id' => $id_employee,
                'is_profit_center' => 0
                ),
            'recursive' => 0
        ));
        $_start = strtotime($stardate) ;
        $_end = strtotime($enddate) ;
        $listActivityTaskFromDates = array();
        foreach($listActivitytask as $key => $value){
          $listActivityTaskFromDate =  $this->ActivityTask->find('first',array(
                'conditions' => array(
                    'AND' => array(
                         'OR' => array(
                            'ActivityTask.task_start_date BETWEEN ? AND ?' => array($_start, $_end),
                            'ActivityTask.task_end_date BETWEEN ? AND ?' => array($_start, $_end),
                            'AND' => array(
                                    'ActivityTask.task_start_date <' =>  $_start ,
                                    'ActivityTask.task_end_date >' => $_end
                            ),
                        ),
                        'ActivityTask.id' => $value['ActivityTaskEmployeeRefer']['activity_task_id'],
                        'ActivityTask.task_start_date !=' => '0000-00-00'
                    )
                ),
                'recursive' => -1
            ));
         if (!empty($listActivityTaskFromDate)) {
            $listActivityTaskFromDate['ActivityTask']['employee_estimated'] = $value['ActivityTaskEmployeeRefer']['estimated'];
            if ($listActivityTaskFromDate['ActivityTask']['task_end_date']=='') {
                $listActivityTaskFromDate['ActivityTask']['task_end_date'] = $listActivityTaskFromDate['ActivityTask']['task_start_date'];
            }
            if ($listActivityTaskFromDate['ActivityTask']['task_start_date'] =='') {
                $listActivityTaskFromDate['ActivityTask']['task_start_date'] = $listActivityTaskFromDate['ActivityTask']['task_end_date'];
            }
            if ($listActivityTaskFromDate['ActivityTask']['task_start_date'] > $listActivityTaskFromDate['ActivityTask']['task_end_date'] ) {
                $listActivityTaskFromDate['ActivityTask']['task_start_date'] = $listActivityTaskFromDate['ActivityTask']['task_end_date'];
            }
            $listActivityTaskFromDate['ActivityTask']['task_start_date'] = date('Y-m-d',$listActivityTaskFromDate['ActivityTask']['task_start_date']);
            $listActivityTaskFromDate['ActivityTask']['task_end_date'] = date('Y-m-d',$listActivityTaskFromDate['ActivityTask']['task_end_date']);

            if (!empty($projectPriority[$listActivityTaskFromDate['ActivityTask']['task_priority_id']])) {
                $listActivityTaskFromDate['ActivityTask']['priority_title'] =    $projectPriority[$listActivityTaskFromDate['ActivityTask']['task_priority_id']];
            } else {
                $listActivityTaskFromDate['ActivityTask']['priority_title'] = "";
            }

            $_activity  =  $this->Activity->find('first',array(
                    'conditions' => array('Activity.id' =>  $listActivityTaskFromDate['ActivityTask']['activity_id']),
                    'recursive' => -1,
                    'fields' => array('short_name', 'family_id')
            ));
            $listActivityTaskFromDate['ActivityTask']['family_id'] = !empty($_activity['Activity']['family_id']) ? $_activity['Activity']['family_id'] : -1;
            $listActivityTaskFromDate['ActivityTask']['activity_name'] = !empty($_activity['Activity']['short_name']) ? $_activity['Activity']['short_name'] : '';
            $listActivityTaskFromDates[] = $listActivityTaskFromDate;
            }
        }
        $result = $this->_addWlForActivityTask($listActivityTaskFromDates,$stardate,$enddate,$id_employee);
        return $result;
    }
    public function getVocation($id_employee,$stardate,$enddate){
        $this->layout = 'ajax';
        $this->loadModel('Holiday');
        $vocation;
        $otherTaskDate = $stardate;
        $_start = strtotime($stardate);
        $_end = strtotime($enddate);
        $employee = $this->_getEmpoyee();
        $holidays = $this->Holiday->getOptionHolidays(strtotime($stardate),strtotime($enddate),$employee['company_id']);
        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $id_employee)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        while($otherTaskDate <= $enddate) {
            $intdate = strtotime($otherTaskDate);
            if (strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday') {
                $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
                continue;
            }
            $vocation[$otherTaskDate] = 0;

            if (!empty($holidays[$intdate])) {
                $vocation[$otherTaskDate] = ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);
            }
            if (!empty($requests_absence[$intdate])) {
                $response = 0;
                if ($requests_absence[$intdate]['response_am'] == 'validated') {
                    $response += 0.5;
                }
                if ($requests_absence[$intdate]['response_pm'] == 'validated') {
                    $response += 0.5;
                }
                $vocation[$otherTaskDate] = $response;
            }
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        $result = $vocation;
        $this->set(compact('result'));
    }

    public function getVocationDetail($id_employee = null, $stardate = null, $enddate = null){
        $this->layout = 'ajax';
        $result =  $this->_getVocationDetail($id_employee, $stardate, $enddate);
        $this->set(compact('result'));
    }

    public function getManDay($model = null, $modeld = null, $taskId = null, $startDate = null, $endDate = null){
        $this->layout = false;
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        /**
         * Danh sach tra ve. Day la sanh sach mac dinh cua 1 project.
         */
        $listAssignReceiveds = array();
        if (!empty($_POST['received'])) {
            $listAssignReceiveds = $_POST['received'];
        } else {
            if ($model === 'project') {
                $listAssignReceiveds = $this->_getTeamEmployees($modeld);
                $assignsforTask =  $this->ProjectTaskEmployeeRefer->find('list',array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $taskId),
                    'fields' => array('reference_id','id')
                ));
            } else {
                $listAssignReceiveds = $this->_getProfitCenterAndEmployee($modeld);
                $assignsforTask =  $this->ActivityTaskEmployeeRefer->find('list',array(
                    'recursive' => -1,
                    'conditions' => array('activity_task_id' => $taskId),
                    'fields' => array('reference_id','id')
                ));
            }
            foreach($listAssignReceiveds as $key => $assign){
                if (!empty($assignsforTask[$assign['Employee']['id']])) {
                    $listAssignReceiveds[$key]['Employee']['is_selected'] = 1;
                } else {
                    $listAssignReceiveds[$key]['Employee']['is_selected'] = 0;
                }
            }
        }
        /**
         * Danh sach cac employee va profit center duoc gui tu client. (danh sach truyen vao)
         * Cu moi lan goi function nay, danh sach se giam 1
         */
        $listAssigns = array();
        if (!empty($_POST['send'])) {
            $listAssigns = $_POST['send'];
            foreach($listAssigns as $key => $listAssign){
                if (!empty($listAssign['capacity_on'])) {
                    $idAssign = $listAssign['task_assign_to_id'];
                    $isProfit = $listAssign['is_profit_center'];
                    $datas = $listAssign['capacity_on'];
                    foreach($listAssignReceiveds as $key => $listAssignReceived){
                        $dx = $listAssignReceived['Employee'];
                        if($dx['id'] == $idAssign && $dx['is_profit_center'] == $isProfit){
                            $listAssignReceiveds[$key]['Employee']['capacity_on'] = $datas;
                        }
                    }
                    unset($listAssigns[$key]);
                }
            }
        }
        /**
         * La 1 bien dung de check. neu $callBack = true thi goi lai function nay
         */
        $callBack = 'false';
        if (!empty($listAssigns)) {
            $callBack = 'true';
        }
        /**
         * Lay phan tu dau tien cua danh sach employee/pc duoc truyen vao
         */
        $elementFirst = array_shift($listAssigns);
        $idAssign = $elementFirst['task_assign_to_id'];
        $isProfit = $elementFirst['is_profit_center'];
        $manDays['id'] = '#li-'.$idAssign.'-'.$isProfit;
        $manDays['val'] = 0;
        if ($isProfit == 0) { //employee
            $datas = $this->_getVocationDetail($idAssign, $startDate, $endDate);
            $datas = array_sum($datas['avaiTotalYear']);
            if ($datas < 0) {
                $datas = 0;
            }
            $manDays['val'] = round($datas,2);
            foreach($listAssignReceiveds as $key => $listAssignReceived){
                $dx = $listAssignReceived['Employee'];
                if ($dx['id'] == $idAssign && $dx['is_profit_center'] == $isProfit) {
                    $listAssignReceiveds[$key]['Employee']['capacity_on'] = $datas;
                }
            }
        } else { //profit center
            $profitCenters = $this->ProjectEmployeeProfitFunctionRefer->find('list',array(
                'recursive' => -1,
                'conditions' => array('profit_center_id' => $idAssign),
                'fields' => array('employee_id', 'employee_id'),
                'order' => array('employee_id')
            ));
            $totalManday = 0;
            if (!empty($profitCenters)) {
                foreach($profitCenters as $employeeId){
                    $datas = $this->_getVocationDetail($employeeId, $startDate, $endDate);
                    $datas = array_sum($datas['avaiTotalYear']);
                    if($datas < 0){
                        $datas = 0;
                    }
                    $totalManday += $datas;
                }
            }
            $manDays['val'] = round($totalManday,2);
            foreach($listAssignReceiveds as $key => $listAssignReceived){
                $dx = $listAssignReceived['Employee'];
                if ($dx['id'] == $idAssign && $dx['is_profit_center'] == $isProfit) {
                    $listAssignReceiveds[$key]['Employee']['capacity_on'] = $totalManday;
                }
            }
        }
        $result['listAssignReceiveds'] = $listAssignReceiveds;
        $result['listAssigns'] = $listAssigns;
        $result['callBack'] = $callBack;
        $result['manDays'] = $manDays;
        $result['startDate'] = $startDate;
        $result['endDate'] = $endDate;
        echo json_encode($result);
        exit;
    }
    public function getManDayOfEmployee($model = null, $modeld = null, $taskId = null, $startDate = null, $endDate = null){
        $this->layout = false;
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');

        $listAssignReceiveds = array();
        if (!empty($_POST['received'])) {
            $listAssignReceiveds = $_POST['received'];
        } else {
            if($model === 'project'){
                $listAssignReceiveds = $this->_getTeamEmployees($modeld);
                $assignsforTask =  $this->ProjectTaskEmployeeRefer->find('list',array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $taskId),
                    'fields' => array('reference_id','id')
                ));
            } else {
                $listAssignReceiveds = $this->_getProfitCenterAndEmployee($modeld);
                $assignsforTask =  $this->ActivityTaskEmployeeRefer->find('list',array(
                    'recursive' => -1,
                    'conditions' => array('activity_task_id' => $taskId),
                    'fields' => array('reference_id','id')
                ));
            }
            foreach($listAssignReceiveds as $key => $assign){
                if (!empty($assignsforTask[$assign['Employee']['id']])) {
                    $listAssignReceiveds[$key]['Employee']['is_selected'] = 1;
                } else {
                    $listAssignReceiveds[$key]['Employee']['is_selected'] = 0;
                }
            }
        }

        $elementFirst = $_POST['employee'];
        $idAssign = $elementFirst[0];
        $isProfit = $elementFirst[1];
        $manDays['id'] = '#li-'.$idAssign.'-'.$isProfit;
        $manDays['val'] = 0;
        if ($isProfit == 0) { //employee
            $datas = $this->_getVocationDetail($idAssign, $startDate, $endDate);
            $datas = array_sum($datas['avaiTotalYear']);
            if($datas < 0){
                $datas = 0;
            }
            $manDays['val'] = round($datas,2);
            foreach($listAssignReceiveds as $key => $listAssignReceived){
                $dx = $listAssignReceived['Employee'];
                if($dx['id'] == $idAssign && $dx['is_profit_center'] == $isProfit){
                    $listAssignReceiveds[$key]['Employee']['capacity_on'] = $datas;
                }
            }
        } else { //profit center
            $profitCenters = $this->ProjectEmployeeProfitFunctionRefer->find('list',array(
                'recursive' => -1,
                'conditions' => array('profit_center_id' => $idAssign),
                'fields' => array('employee_id', 'employee_id'),
                'order' => array('employee_id')
            ));
            $totalManday = 0;
            if (!empty($profitCenters)) {
                foreach($profitCenters as $employeeId){
                    $datas = $this->_getVocationDetail($employeeId, $startDate, $endDate);
                    $datas = array_sum($datas['avaiTotalYear']);
                    if($datas < 0){
                        $datas = 0;
                    }
                    $totalManday += $datas;
                }
            }
            $manDays['val'] = round($totalManday,2);
            foreach($listAssignReceiveds as $key => $listAssignReceived){
                $dx = $listAssignReceived['Employee'];
                if ($dx['id'] == $idAssign && $dx['is_profit_center'] == $isProfit) {
                    $listAssignReceiveds[$key]['Employee']['capacity_on'] = $totalManday;
                }
            }
        }
        $result['listAssignReceiveds'] = $listAssignReceiveds;
        $result['manDays'] = $manDays;
        echo json_encode($result);
        exit;
    }
    private function _getVocationDetail($id_employee = null, $stardate = null, $enddate = null){
        $this->loadModel('Holiday');
        $vocation = array();
        $woking = array();
        $otherTaskDate = $stardate;
        $_start = strtotime($stardate . ' 00:00:00');
        $_end = strtotime($enddate . ' 00:00:00');
        $employee = $this->_getEmpoyee();
        $holidays = $this->Holiday->get($employee['company_id'], $_start, $_end, 'Y-m-d');

        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $id_employee)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        $intdate = $_start;
        while($intdate <= $_end){
            $otherTaskDate = date('Y-m-d', $intdate);
            if (strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday') {
                $intdate = strtotime('+ 1 day', $intdate);
                continue;
            }
            $vocation[$otherTaskDate] = 0;

   //          if( in_array($otherTaskDate, $holidays) ){
   //              $vocation[$otherTaskDate] = 1;
   //          }
            // if($vocation[$otherTaskDate] >1) $vocation[$otherTaskDate] = 1;
            if (!empty($requests_absence[$intdate])) {
                $response = 0;
                if ($requests_absence[$intdate]['response_am'] == 'validated') {
                    $response += 0.5;
                }
                if ($requests_absence[$intdate]['response_pm'] == 'validated') {
                    $response += 0.5;
                }
                $vocation[$otherTaskDate] = $response;
            }
            $intdate = strtotime('+ 1 day', $intdate);
        }

        $vocationDetails = $vocationDetailMonths = $vocationDetailYears = array();
        if (!empty($vocation)) {
            foreach($vocation as $day => $value){
                $time = strtotime($day);
                $year = date('Y', $time);
                $date = date('d-m', $time);
                $month = date('M', $time);

                if (!isset($vocationDetails[$year][$date])) {
                    $vocationDetails[$year][$date] = 0;
                }
                $vocationDetails[$year][$date] = $value;
                if (!isset($vocationDetailMonths[$year][$month])) {
                    $vocationDetailMonths[$year][$month] = 0;
                    $working[$year][$month] = 0;
                }
                $vocationDetailMonths[$year][$month] += $value;
                //working days

                if ( !in_array($day, $holidays) ) {
                    $working[$year][$month] += 1;
                }

                if (!isset($vocationDetailYears[$year])) {
                    $vocationDetailYears[$year] = 0;
                }
                $vocationDetailYears[$year] += $value;
            }
        }
        $listTaskDetails = $this->_getAllProjectTaskForEmployeeDetail($id_employee, $stardate, $enddate);
        //NCT Task
        $listNctProjectTaskDetails = $this->getNctWorkload($id_employee, $stardate, $enddate);
        $listNctActivityTaskDetails = $this->getNctWorkload($id_employee, $stardate, $enddate, 'ActivityTask');

        //END
        $datas = $dataActivities = $dataProjects = $dataProjectMonths = $dataActivityMonths = $dataProjectYears = $dataActivityYears = array();
        $listNameActivities = $listNameActivityTasks = $listNameProjects = $listNameProjectTasks = $listPrioritieProjectTasks = $listPrioritieActivityTasks = array();
        $totalWorkload = array();
        $listDateDatas = $listMonthDatas = $listYearDatas = $listIdFamilies = array();
        if (!empty($listTaskDetails)) {
            foreach($listTaskDetails as $listTaskDetail) {
                $dx = !empty($listTaskDetail['ProjectTask']) ? $listTaskDetail['ProjectTask'] : '';
                $ds = !empty($listTaskDetail['ActivityTask']) ? $listTaskDetail['ActivityTask'] : '';
                if (!empty($dx)) {
                    if($dx['is_nct'] == 1) {
                        if( isset($listNctProjectTaskDetails[$dx['id']]) )
                            $dx['Workingday'] = $listNctProjectTaskDetails[$dx['id']];
                        else $dx['Workingday'] = array();
                    }
                    $listIdFamilies[] = $dx['family_id'];
                    if (!isset($dataProjects[$dx['project_id']][$dx['id']])) {
                        $dataProjects[$dx['project_id']][$dx['id']] = array();
                    }
                    $dataProjects[$dx['project_id']][$dx['id']] = !empty($dx['Workingday']) ? $dx['Workingday'] : array();
                    // calcul follow date group by family
                    if ($dx['family_id'] != -1) {
                        if (!isset($listDateDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']])) {
                            $listDateDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']] = array();
                        }
                        $listDateDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']] = !empty($dx['Workingday']) ? $dx['Workingday'] : array();
                    }
                    if (!empty($dx['Workingday'])) {
                        foreach($dx['Workingday'] as $time => $values){
                            if (!isset($totalWorkload[$time])) {
                                $totalWorkload[$time] = 0;
                            }
                            $totalWorkload[$time] += $values;
                            $time = !empty($time) ? strtotime($time) : '';
                            $year = !empty($time) ? date('Y', $time) : 0;
                            $date_month = !empty($time) ? date('d-m', $time) : 0;
                            if (!empty($vocationDetails[$year][$date_month]) && $vocationDetails[$year][$date_month] == 1) {
                                $values = 0;
                            }
                            $time = !empty($time) ? date('Y-M', $time) : 0;
                            if (!isset($dataProjectMonths[$dx['project_id']][$dx['id']][$time])) {
                                $dataProjectMonths[$dx['project_id']][$dx['id']][$time] = 0;
                            }
                            $dataProjectMonths[$dx['project_id']][$dx['id']][$time] += $values;
                            if (!isset($dataProjectYears[$dx['project_id']][$dx['id']][$year])) {
                                $dataProjectYears[$dx['project_id']][$dx['id']][$year] = 0;
                            }
                            $dataProjectYears[$dx['project_id']][$dx['id']][$year] += $values;
                            // calcul follow month group by family
                            if ($dx['family_id'] != -1) {
                                if (!isset($listMonthDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']][$time])) {
                                    $listMonthDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']][$time] = 0;
                                }
                                $listMonthDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']][$time] += $values;
                                // calcul follow year group by family
                                if (!isset($listYearDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']][$year])) {
                                    $listYearDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']][$year] = 0;
                                }
                                $listYearDatas[$dx['family_id']]['pr-'.$dx['project_id']][$dx['id']][$year] += $values;
                            }
                        }
                    }
                    if (!empty($listNameProjects[$dx['project_id']])) {
                        //do nothing
                    }
                    $listNameProjects[$dx['project_id']] = !empty($dx['project_name']) ? $dx['project_name'] : '';
                    if (!empty($listNameProjectTasks[$dx['id']])) {
                        //do nothing
                    }
                    $listNameProjectTasks[$dx['id']] = !empty($dx['task_title']) ? $dx['task_title'] : '';
                    if (!empty($listPrioritieProjectTasks[$dx['id']])) {
                        //do nothing
                    }
                    $listPrioritieProjectTasks[$dx['id']] = !empty($dx['priority_title']) ? $dx['priority_title'] : '';
                }
                if (!empty($ds)) {
                    $listIdFamilies[] = $ds['family_id'];
                    if (!isset($dataActivities[$ds['activity_id']][$ds['id']])) {
                        $dataActivities[$ds['activity_id']][$ds['id']] = array();
                    }
                    $dataActivities[$ds['activity_id']][$ds['id']] = !empty($ds['Workingday']) ? $ds['Workingday'] : array();
                    // calcul follow date group by family
                    if ($ds['is_nct'] == 1) {
                        if ( isset($listNctActivityTaskDetails[$ds['id']]) ) {
                            $ds['Workingday'] = $listNctActivityTaskDetails[$ds['id']];
                        } else {
                            $ds['Workingday'] = array();
                        }
                    }

                    if ($ds['family_id'] != -1) {
                        if (!isset($listDateDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']])) {
                            $listDateDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']] = array();
                        }
                        $listDateDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']] = !empty($ds['Workingday']) ? $ds['Workingday'] : array();
                    }

                    if (!empty($ds['Workingday'])) {
                        foreach($ds['Workingday'] as $time => $values){
                            if (!isset($totalWorkload[$time])) {
                                $totalWorkload[$time] = 0;
                            }
                            $totalWorkload[$time] += $values;
                            $time = !empty($time) ? strtotime($time) : '';
                            $year = !empty($time) ? date('Y', $time) : 0;
                            $date_month = !empty($time) ? date('d-m', $time) : 0;
                            if (!empty($vocationDetails[$year][$date_month]) && $vocationDetails[$year][$date_month] == 1) {
                                $values = 0;
                            }
                            $time = !empty($time) ? date('Y-M', $time) : 0;
                            if (!isset($dataActivityMonths[$ds['activity_id']][$ds['id']][$time])){
                                $dataActivityMonths[$ds['activity_id']][$ds['id']][$time] = 0;
                            }
                            $dataActivityMonths[$ds['activity_id']][$ds['id']][$time] += $values;
                            if (!isset($dataActivityYears[$ds['activity_id']][$ds['id']][$year])) {
                                $dataActivityYears[$ds['activity_id']][$ds['id']][$year] = 0;
                            }
                            $dataActivityYears[$ds['activity_id']][$ds['id']][$year] += $values;
                            // calcul follow month group by family
                            if ($ds['family_id'] != -1) {
                                if (!isset($listMonthDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']][$time])) {
                                    $listMonthDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']][$time] = 0;
                                }
                                $listMonthDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']][$time] += $values;
                                // calcul follow year group by family
                                if (!isset($listYearDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']][$year])) {
                                    $listYearDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']][$year] = 0;
                                }
                                $listYearDatas[$ds['family_id']]['ac-'.$ds['activity_id']][$ds['id']][$year] += $values;
                            }
                        }
                    }
                    if (!empty($listNameActivities[$ds['activity_id']])) {
                        //do nothing
                    }
                    $listNameActivities[$ds['activity_id']] = !empty($ds['activity_name']) ? $ds['activity_name'] : '';
                    if (!empty($listNameActivityTasks[$ds['id']])) {
                        //do nothing
                    }
                    $listNameActivityTasks[$ds['id']] = !empty($ds['name']) ? $ds['name'] : '';
                    if (!empty($listPrioritieActivityTasks[$ds['id']])) {
                        //do nothing
                    }
                    $listPrioritieActivityTasks[$ds['id']] = !empty($ds['priority_title']) ? $ds['priority_title'] : '';
                }
            }
        }
        $listIdFamilies = !empty($listIdFamilies) ? array_unique($listIdFamilies) : array();
        $this->loadModel('Family');
        $families = $this->Family->find('list', array(
            'recursive' => -1,
            'conditions' => array('Family.id' => $listIdFamilies)
        ));
        $families[-1] = __('Opportunity',true);
        $dataDetails = $priorities = $groupNames = $groupNameTasks = $dataDetailMonths = $dataDetailYears = array();
        // data
        $dataDetails['project'] = $dataProjects;
        $dataDetails['activity'] = $dataActivities;
        // data month
        $dataDetailMonths['project'] = $dataProjectMonths;
        $dataDetailMonths['activity'] = $dataActivityMonths;
        // data year
        $dataDetailYears['project'] = $dataProjectYears;
        $dataDetailYears['activity'] = $dataActivityYears;
        // priority
        $priorities['project'] = $listPrioritieProjectTasks;
        $priorities['activity'] = $listPrioritieActivityTasks;
        // ten cua project va activity
        $groupNames['project'] = $listNameProjects;
        $groupNames['activity'] = $listNameActivities;
        // ten cua project task va activity task
        $groupNameTasks['project'] = $listNameProjectTasks;
        $groupNameTasks['activity'] = $listNameActivityTasks;
        $dayMaps = array(
            '01' => __('Jan', true),
            '02' => __('Feb', true),
            '03' => __('Mar', true),
            '04' => __('Apr', true),
            '05' => __('May', true),
            '06' => __('Jun', true),
            '07' => __('Jul', true),
            '08' => __('Aug', true),
            '09' => __('Sep', true),
            '10' => __('Oct', true),
            '11' => __('Nov', true),
            '12' => __('Dec', true)
        );
        //month
        $avaiTotals = $avaiTotalYears = array();
        if(!empty($totalWorkload)){
            foreach($totalWorkload as $time => $values){
                $time = !empty($time) ? strtotime($time) : '';
                $year = !empty($time) ? date('Y', $time) : 0;
                $day_month = !empty($time) ? date('d-m', $time) : 0;
                $year_month = !empty($time) ? date('Y-M', $time) : 0;
                if (!isset($avaiTotals[$year_month])) {
                    $avaiTotals[$year_month] = 0;
                }
                if (!empty($vocationDetails[$year][$day_month]) && $vocationDetails[$year][$day_month] == 1) {
                    $avaiTotals[$year_month] += 0;
                } else if (!empty($vocationDetails[$year][$day_month]) && $vocationDetails[$year][$day_month] == 0.5) {
                    $avais = round(0.5 - $values, 2);
                    $avaiTotals[$year_month] += $avais;
                } else {
                    $avais = round(1 - $values, 2);
                    $avaiTotals[$year_month] += $avais;
                }
                if (!isset($avaiTotalYears[$year])) {
                    $avaiTotalYears[$year] = 0;
                }
                if (!empty($vocationDetails[$year][$day_month]) && $vocationDetails[$year][$day_month] == 1) {
                    $avaiTotalYears[$year] += 0;
                } else if (!empty($vocationDetails[$year][$day_month]) && $vocationDetails[$year][$day_month] == 0.5) {
                    $avais = round(0.5 - $values, 2);
                    $avaiTotalYears[$year] += $avais;
                } else {
                    $avais = round(1 - $values, 2);
                    $avaiTotalYears[$year] += $avais;
                }
            }
        }
        $addAvaiMonths = $addAvaiYears = array();
        if (!empty($vocationDetails)) {
            foreach($vocationDetails as $year => $vocationDetail) {
                foreach($vocationDetail as $time => $values) {
                    if ($values == 1) {
                        //do nothing
                    } else {
                        $time = explode('-', $time);
                        $_time = strtotime($year.'-'.$time[1].'-'.$time[0]);
                        if (!empty($totalWorkload[date('Y-m-d', $_time)])) {
                            //do nothing
                        } else {
                            if (!isset($addAvaiMonths[date('Y-M', $_time)])) {
                                $addAvaiMonths[date('Y-M', $_time)] = 0;
                            }
                            $addAvaiMonths[date('Y-M', $_time)] += 1;

                            if (!isset($addAvaiYears[date('Y', $_time)])) {
                                $addAvaiYears[date('Y', $_time)] = 0;
                            }
                            $addAvaiYears[date('Y', $_time)] += 1;
                        }
                    }
                }
            }
        }
        if (!empty($addAvaiMonths)) {
            foreach($addAvaiMonths as $time => $addAvaiMonth){
                if (!isset($avaiTotals[$time])) {
                    $avaiTotals[$time] = $addAvaiMonth;
                } else {
                    $avaiTotals[$time] += $addAvaiMonth;
                }
            }
        }
        if (!empty($addAvaiYears)) {
            foreach($addAvaiYears as $time => $addAvaiYear){
                if (!isset($avaiTotalYears[$time])) {
                    $avaiTotalYears[$time] = $addAvaiYear;
                } else {
                    $avaiTotalYears[$time] += $addAvaiYear;
                }
            }
        }
        // general
        $result['priority'] = $priorities;
        $result['groupNames'] = $groupNames;
        $result['groupNameTasks'] = $groupNameTasks;
        $result['dayMaps'] = $dayMaps;
        $result['families'] = $families;
        // day
        $result['vocation'] = $vocationDetails;
        $result['dataDetail'] = $dataDetails;
        $result['listDateDatas'] = $listDateDatas;
        // month
        $result['vocationMonth'] = $vocationDetailMonths;
        $result['dataDetailMonth'] = $dataDetailMonths;
        $result['avaiTotalMonth'] = $avaiTotals;
        $result['listMonthDatas'] = $listMonthDatas;
        // year
        $result['vocationYear'] = $vocationDetailYears;
        $result['dataDetailYear'] = $dataDetailYears;
        $result['avaiTotalYear'] = $avaiTotalYears;
        $result['listYearDatas'] = $listYearDatas;
        $result['working'] = $working;
        return $result;

    }

    public function getNctWorkload($employee_id, $startdate, $enddate, $keyword = 'ProjectTask'){
        $this->loadModel('NctWorkload');
        if ($keyword == 'ProjectTask') {
            $keywordTask = 'project_task_id';
        } else {
            $keywordTask = 'activity_task_id';
        }
        $workloads = $this->NctWorkload->find('all', array(
            'conditions' => array(
                'reference_id' => $employee_id,
                'is_profit_center' => 0,
                'AND' => array(
                    'OR' => array(
                        'task_date BETWEEN ? AND ?' => array($startdate, $enddate),
                        'end_date BETWEEN ? AND ?' => array($startdate, $enddate),
                        'AND' => array(
                            'task_date <' =>  $startdate ,
                            'end_date >' => $enddate
                        ),
                    )
                ),
            ),
            'recursive' => -1
        ));
        $result = array();
        if( !empty($workloads) ){
            $ids = Set::classicExtract($workloads, '{n}.NctWorkload.' . $keywordTask);
            $tasks = $this->$keyword->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $ids
                ),
                'fields' => array('id', 'id')
            ));
            $lib = new LibBehavior();
            foreach($workloads as $val){
                $value = $val['NctWorkload'];
                // $task =  $this->$keyword->find('first',array(
                //     'conditions' => array(
                //         'AND' => array(
                //             'OR' => array(
                //                 $keyword.'.task_start_date BETWEEN ? AND ?' => array($startdate, $enddate),
                //                 $keyword.'.task_end_date BETWEEN ? AND ?' => array($startdate, $enddate),
                //                 'AND' => array(
                //                     $keyword.'.task_start_date <' =>  $startdate ,
                //                     $keyword.'.task_end_date >' => $enddate
                //                 ),
                //             )
                //         ),
                //        $keyword.'.id' => $value['NctWorkload'][$keywordTask],
                //        $keyword.'.task_start_date !=' => '0000-00-00',
                //        $keyword.'.is_nct' => 1
                //     ),
                //     'recursive' => -1
                // ));
                $taskID = $value[$keywordTask];
                if( isset($tasks[$taskID]) ){
                    if( $value['group_date'] ){
                        $range = explode('_', $value['group_date']);
                        $dateRange = $lib->getListWorkingDays(strtotime($range[1]), strtotime($range[2]), true);
                    } else {
                        $dateRange = array(strtotime($value['task_date'] . ' 00:00:00'));
                    }
                    $number = count($dateRange);
                    $workload = $lib->caculateGlobal($value['estimated'], $number);
                    $break = $workload['number'];
                    $i = 1;
                    foreach($dateRange as $date){
                        $est = $workload['original'];
                        if( $number - $i < $break ){
                            $est = $workload['original'] + $workload['remainder'];
                        }
                        $result[$taskID][date('Y-m-d', $date)] = floatval($est);
                        $i++;
                    }
                    //$date = strtotime($value['NctWorkload']['task_date'] . ' 00:00:00');
                    //$result[$task[$keyword]['id']][$value['NctWorkload']['task_date']] = floatval($value['NctWorkload']['estimated']);
                }
            }
        }
        return $result;
    }

    /**
     * HuuPc modify
     * Danh sach 1 list gom cac project task va activity task nam trong availability
     */
    private function _getAllProjectTaskForEmployeeDetail($id_employee = null, $stardate, $enddate){
        $this->layout = 'ajax';
        $this->loadModel('Holiday');
        $this->loadModel('Project');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectPriority');
        $projectPriority = $this->ProjectPriority->find('list');
        $listprojecttask = $this->ProjectTaskEmployeeRefer->find('all',array(
            'conditions' => array(
                'reference_id' => $id_employee,
                'is_profit_center' => 0
                ),
            'recursive' => -1
        ));
        $listProjectTaskFromDates = array();
        foreach($listprojecttask as $key => $value){
            $listProjectTaskFromDate =  $this->ProjectTask->find('first',array(
                'conditions' => array(
                    'AND' => array(
                        'OR' => array(
                            'ProjectTask.task_start_date BETWEEN ? AND ?' => array($stardate, $enddate),
                            'ProjectTask.task_end_date BETWEEN ? AND ?' => array($stardate, $enddate),
                            'AND' => array(
                                    'ProjectTask.task_start_date <' =>  $stardate ,
                                    'ProjectTask.task_end_date >' => $enddate
                            ),
                        )
                    ),
                   'ProjectTask.id' => $value['ProjectTaskEmployeeRefer']['project_task_id'],
                   'ProjectTask.task_start_date !=' => '0000-00-00'
                ),
                'recursive' => -1
            ));
             if (!empty($listProjectTaskFromDate)) {
                $listProjectTaskFromDate['ProjectTask']['employee_estimated'] = $value['ProjectTaskEmployeeRefer']['estimated'];
                if ($listProjectTaskFromDate['ProjectTask']['task_end_date']=='0000-00-00') {
                    $listProjectTaskFromDate['ProjectTask']['task_end_date'] = $listProjectTaskFromDate['ProjectTask']['task_start_date'];
                }
                if ($listProjectTaskFromDate['ProjectTask']['task_start_date'] =='0000-00-00') {
                    $listProjectTaskFromDate['ProjectTask']['task_start_date'] = $listProjectTaskFromDate['ProjectTask']['task_end_date'];
                }
                if ($listProjectTaskFromDate['ProjectTask']['task_start_date'] > $listProjectTaskFromDate['ProjectTask']['task_end_date'] ) {
                    $listProjectTaskFromDate['ProjectTask']['task_start_date'] = $listProjectTaskFromDate['ProjectTask']['task_end_date'];
                }
                if (!empty($projectPriority[$listProjectTaskFromDate['ProjectTask']['task_priority_id']])) {
                    $listProjectTaskFromDate['ProjectTask']['priority_title'] =    $projectPriority[$listProjectTaskFromDate['ProjectTask']['task_priority_id']];
                } else {
                    $listProjectTaskFromDate['ProjectTask']['priority_title'] = "";
                }

                $_project  =  $this->Project->find('first',array(
                    'conditions' => array('Project.id' =>  $listProjectTaskFromDate['ProjectTask']['project_id']),
                    'recursive' => -1,
                    'fields' => array('project_name', 'activity_id')
                ));
                if (!empty($_project) && !empty($_project['Project']['activity_id'])) {
                    $this->loadModel('Activity');
                    $familyId = $this->Activity->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Activity.id' => $_project['Project']['activity_id']),
                        'fields' => array('family_id')
                    ));
                    if (!empty($familyId) && !empty($familyId['Activity']['family_id'])) {
                        $listProjectTaskFromDate['ProjectTask']['family_id'] = $familyId['Activity']['family_id'];
                    } else {
                        $listProjectTaskFromDate['ProjectTask']['family_id'] = -1;
                    }
                } else {
                    $listProjectTaskFromDate['ProjectTask']['family_id'] = -1;
                }
                $listProjectTaskFromDate['ProjectTask']['project_name'] = !empty($_project['Project']['project_name']) ? $_project['Project']['project_name'] : '';
                $listProjectTaskFromDates[] = $listProjectTaskFromDate;
            }
        }
        $result = $this->_addWlForTask($listProjectTaskFromDates,$stardate,$enddate,$id_employee);
        $activitylist = $this->_getActitivyTask($id_employee, $stardate , $enddate);
        if (!empty($activitylist)) {
            foreach($activitylist as $__key => $__value){
                $result[] = $__value;
            }
        }
        return $result;
    }

     private function _getlistVocation($id_employee,$stardate,$enddate){
        $otherTaskDate = $stardate;
        $_start = strtotime($stardate);
        $_end = strtotime($enddate);
        //$employee = $this->_getEmpoyee();
        $numday = array();
        while($otherTaskDate <= $enddate){
            $intdate = strtotime($otherTaskDate);
            if(strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday'){
                $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
                continue;
            }
            $numday[$otherTaskDate] = $otherTaskDate;
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        return $numday;
    }
    private function _AvgWorkingThisTaskForEmployee($task,$id_employee){
        $this->loadModel('Holiday');
        $this->loadModel('ActivityTask');
        $employee = $this->_getEmpoyee();
        $_start = strtotime($task['task_start_date']);
        $_end = strtotime($task['task_end_date']);
        $holidays = $this->Holiday->getOptionHolidays($_start,$_end,$employee['company_id']);
        $holidaysA = $this->Holiday->getHolidaysBetweenTime($employee['company_id'],$_start,$_end);
        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $id_employee)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        $otherTaskDate = $task['task_start_date'];
        $duration = 0;
        $daysOff = array();
        while($otherTaskDate <= $task['task_end_date']){
            $duration++;
            $intdate = strtotime($otherTaskDate);
            if (strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday') {
                $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
                $duration--;
            }
            if (!empty($holidays[$intdate])) {
                $daysOff[$otherTaskDate]=1;
                //$duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']); //KHONG AP DUNG HOLIDAY 1/2 ngay
                $duration  -= 1;
            }
            $flag=0;
            if (!empty($requests_absence[$intdate])) {
                if ($requests_absence[$intdate]['response_am'] == 'validated') {
                    $duration -= 0.5;
                    $flag+=0.5;
                }
                if ($requests_absence[$intdate]['response_pm'] == 'validated') {
                    $duration -= 0.5;
                    $flag+=0.5;
                }
            }
            if ($flag!=0) {
                $daysOff[$otherTaskDate] = $flag;
            }
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        if ($duration == 0) $duration = 1;
        $avgWL = $this->ActivityTask->caculateGlobal($task['employee_estimated'],$duration*2);
        return  array('avgWl' => $avgWL['original'], 'remainder' => $avgWL['remainder'], 'number' => $avgWL['number'] ,'duration' => $duration, 'daysOff' => $daysOff);
    }
    private function _addWlForTask($listProjectTaskFromDates,$stardate,$enddate,$id_employee){
        return $this->_addWlForActivityTask($listProjectTaskFromDates,$stardate,$enddate,$id_employee,'ProjectTask');
    }
    private function _addWlForActivityTask($listProjectTaskFromDates,$stardate,$enddate,$id_employee,$keyword='ActivityTask'){
        $date = $this->_getlistVocation($id_employee,$stardate,$enddate);
        foreach($listProjectTaskFromDates as $key => $listProjectTaskFromDate){
            $avgWL = $this->_AvgWorkingThisTaskForEmployee($listProjectTaskFromDate[$keyword],$id_employee);
            if ($listProjectTaskFromDate[$keyword]['task_end_date'] == $listProjectTaskFromDate[$keyword]['task_start_date']) {
                $listProjectTaskFromDates[$key][$keyword]['Workingday'][$otherTaskDate] = $listProjectTaskFromDate[$keyword]['employee_estimated'];
                continue;
            }
            $otherTaskDate = $listProjectTaskFromDate[$keyword]['task_start_date'];
            $j=0;
            $daysOff = $avgWL['daysOff'];
            $is_nct = $listProjectTaskFromDate[$keyword]['is_nct'];
            while($listProjectTaskFromDate[$keyword]['task_end_date'] >= $otherTaskDate){
                if (isset($daysOff[$otherTaskDate])&&$daysOff[$otherTaskDate]==1) {
                    //do nothing
                } else {
                    // if($is_nct == 1)
                    // {
                    //     $_workload=0;
                    //     $listProjectTaskFromDates[$key][$keyword]['Workingday'][$otherTaskDate] = 0;
                    // }
                    // else
                    // {
                        if (isset($daysOff[$otherTaskDate])&&$daysOff[$otherTaskDate]==0.5) {
                            $_workload=$avgWL['avgWl'];
                        } else {
                            $_workload=$avgWL['avgWl']*2;
                        }
                        if (!empty($date[$otherTaskDate])) {
                            $listProjectTaskFromDates[$key][$keyword]['Workingday'][$otherTaskDate] = $_workload;
                            if($j < $avgWL['number']) {
                                $listProjectTaskFromDates[$key][$keyword]['Workingday'][$otherTaskDate] += $avgWL['remainder'];
                            }
                            $j++;
                        }
                    // }
                }
                $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
            }
        }
        return $listProjectTaskFromDates;
    }
    /**
     * capacityForTask
     *
     * @return MD for task
     * @access public
     */
    public function capacityForTask($project_id, $task_id){
        $this->layout = 'ajax';
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('Holiday');
        $assigns = $this->_getTeamEmployees($project_id);
        $assignsforTask =  $this->ProjectTaskEmployeeRefer->find('list',array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $task_id),
            'fields' => array('reference_id','id')
        ));
        foreach($assigns as $key => $assign){
            if(!empty($assignsforTask[$assign['Employee']['id']])){
                $assigns[$key]['Employee']['is_selected'] = 1;
            }else{
                $assigns[$key]['Employee']['is_selected'] = 0;
            }
        }
        $projectTasks = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'ProjectTask.id' => $task_id),
            'fields' => array('id', 'task_start_date', 'task_end_date')
        ));
        $result = $this->capacityForEmployee($assigns, $projectTasks['ProjectTask']['task_start_date'] , $projectTasks['ProjectTask']['task_end_date']);
        $this->set(compact('result'));
    }

    private function capacityForEmployee($arrayEmployee , $stardate , $endate){
        //tim cac task co employee lam tu stardate den enddate
        //tinh MD cho cac employee tru ra thu 7 CN, ngay le , ngay xin nghi
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $_end = $endate;
        $_start = $stardate;
        $endate = strtotime($endate);
        $stardate = strtotime($stardate);
        foreach($arrayEmployee as $key => $employee){
            $avgMD = 0;
            if ($employee['Employee']['is_profit_center'] == 0 ) {
                $listProjectTaskForEmployee = $this->ProjectTaskEmployeeRefer->find('all', array(
                            'conditions' => array(
                                //'ActivityTask.activity_id' => $activityActivatedYes,
                                //'ActivityTask.project_task_id' => null,
                                'OR' => array(
                                    'ProjectTask.task_start_date BETWEEN ? AND ?' => array($_start, $_end),
                                    'ProjectTask.task_end_date BETWEEN ? AND ?' => array($_start, $_end),
                                    'AND' => array(
                                        'ProjectTask.task_start_date < ?' => array($_start),
                                        'ProjectTask.task_end_date > ?' => array($_start)
                                    ),
                                    'AND' => array(
                                        'ProjectTask.task_start_date < ?' => array($_end),
                                        'ProjectTask.task_end_date > ?' => array($_end)
                                    )
                                ),
                                'AND' => array(
                                    'ProjectTaskEmployeeRefer.reference_id' => $employee['Employee']['id'],
                                    'ProjectTaskEmployeeRefer.is_profit_center' => 0
                                )
                            )
                ));
                if (empty($listProjectTaskForEmployee)) {
                    $arrayEmployee[$key]['Employee']['capacity_on']= 0;
                } else {
                    foreach($listProjectTaskForEmployee as $_key => $_listProjectTaskForEmployee){
                        if ($_listProjectTaskForEmployee['ProjectTaskEmployeeRefer']['estimated'] == 0) {
                            $avgMD += 0;
                        } else {
                            $avgMD += $this->_AvgProjectWorkingForEmployee($_listProjectTaskForEmployee,$employee['Employee']['id'], $stardate , $endate);
                        }
                    }
                }
                 $listActivitytaskForEmployee = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
                            'conditions' => array(
                                //'ActivityTask.activity_id' => $activityActivatedYes,
                                'ActivityTask.project_task_id' => null,
                                'OR' => array(
                                    'ActivityTask.task_start_date BETWEEN ? AND ?' => array($stardate, $endate),
                                    'ActivityTask.task_end_date BETWEEN ? AND ?' => array($stardate, $endate),
                                    'AND' => array(
                                        'ActivityTask.task_start_date < ?' => array($stardate),
                                        'ActivityTask.task_end_date > ?' => array($stardate)
                                    ),
                                    'AND' => array(
                                        'ActivityTask.task_start_date < ?' => array($endate),
                                        'ActivityTask.task_end_date > ?' => array($endate)
                                    )
                                ),
                                'AND' => array(
                                    'ActivityTaskEmployeeRefer.reference_id' => $employee['Employee']['id'],
                                    'ActivityTaskEmployeeRefer.is_profit_center' => 0
                                )
                            )
                        ));
                if (empty($listActivitytaskForEmployee)) {
                    $arrayEmployee[$key]['Employee']['capacity_on']= 0;
                } else {
                    foreach($listActivitytaskForEmployee as $_key => $_listActivityTaskFromDate){
                        if ($_listActivityTaskFromDate['ActivityTaskEmployeeRefer']['estimated'] == 0) {
                            $avgMD += 0;
                        } else {
                            $csthistask = $this->ActivityRequest->find('first',array(
                                        'conditions' => array(
                                            'status' => 2,
                                            'task_id' => $_listActivityTaskFromDate['ActivityTask']['id'],
                                        ),
                                        'recursive' => -1,
                                        'fields' => 'SUM(value) as est',
                                        'group' => 'task_id'
                                    ));
                            if (!empty($csthistask)) {
                                $_listActivityTaskFromDate['ActivityTaskEmployeeRefer']['estimated'] -= $csthistask[0]['est'];
                            }
                            $avgMD += $this->_AvgActivityWorkingForEmployee($_listActivityTaskFromDate,$employee['Employee']['id'], $stardate , $endate);
                        }
                    }
                }
            }else{
                $arrayEmployee[$key]['Employee']['capacity_on']= 0;
            }
            $workingday = $this->countdate($stardate ,$endate,$employee['Employee']['id']);
            $wd = $workingday - $avgMD;
            if ($wd < 0) {
                $wd = 0;
            }
            $arrayEmployee[$key]['Employee']['capacity_on'] = $wd;
            $arrayEmployee[$key]['Employee']['capacity_off'] = $avgMD;
        }
        return $arrayEmployee;
    }

     private function _AvgActivityWorkingForEmployee($task,$id_employee, $stardate , $endate){
        $this->loadModel('Holiday');
        $employee = $this->_getEmpoyee();
        $estimated = $task['ActivityTaskEmployeeRefer']['estimated'];
        $task = $this->ActivityTask->find('first',array(
            'conditions' => array('ActivityTask.id' => $task['ActivityTaskEmployeeRefer']['activity_task_id'] ),
            'recursive' => -1
        ));
        $_start = $task['ActivityTask']['task_start_date'];
        $_end = $task['ActivityTask']['task_end_date'];
        if (empty($_start)) {
            $_start = $_end;
        }
        if ($_start > $_end) {
            $_start = $_end;
        }
        if (empty($_start) && empty($_end)) {
           $_start = strtotime(date("Y-m-d"));
           $_end = strtotime(date("Y-m-d"));
        }

        $holidays = $this->Holiday->getOptionHolidays($_start,$_end,$employee['company_id']);
        $requests_absence = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                    'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $id_employee)))
                    , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest'
        );
        $otherTaskDate = date('Y-m-d',$_start);
        $duration = 0;
        $rageDate = array();
        while($otherTaskDate <=  date('Y-m-d',$_end)){
            $chk = true;
            $intdate = strtotime($otherTaskDate);
            if (strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday') {
                $duration--;
                $chk = false;
            }
            if (!empty($holidays[$intdate])) {
               $duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);
               $chk = false;
            }
            if (!empty($requests_absence[$intdate])) {
                if ($requests_absence[$intdate]['response_am'] == 'validated') {
                    $duration -= 0.5;
                }
                if ($requests_absence[$intdate]['response_pm'] == 'validated') {
                    $duration -= 0.5;
                }
                $chk = false;
            }
            if ($chk == true) {
                $rageDate[$otherTaskDate] = $otherTaskDate;
            }
            $duration++;
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        $__ragedate = $this->RageDate($stardate ,$endate) ;
        $count = 0;
        $endnote = false;
        foreach($__ragedate as $date){
            if (!empty($rageDate[$date])) {
                if (date('Y-m-d',$_end) == $date) {
                    $endnote = true;
                }
                $count++;
            }
        }
        if($duration == 0) {
            $duration = 1;
        }
        if ($endnote == true) {
            return round($estimated/$duration,2) * $count + ($estimated - round($estimated/$duration,2) * $duration);
        } else {
            return round($estimated/$duration,2) * $count;
        }
    }

    private function _AvgProjectWorkingForEmployee($task,$id_employee, $stardate , $endate){
        $this->loadModel('Holiday');
        $this->loadModel('ProjectTask');
        $employee = $this->_getEmpoyee();
        $estimated = $task['ProjectTaskEmployeeRefer']['estimated'];
        $task = $this->ProjectTask->find('first',array(
            'conditions' => array('ProjectTask.id' => $task['ProjectTaskEmployeeRefer']['project_task_id'] ),
            'recursive' => -1
        ));
        $_start = strtotime($task['ProjectTask']['task_start_date']);
        $_end = strtotime($task['ProjectTask']['task_end_date']);
        if (empty($_start) || $_start == '0000-00-00') {
            $_start = $_end;
        }
        if ($_start > $_end) {
            $_start = $_end;
        }
        if ($_start == '0000-00-00' && $_end == '0000-00-00') {
            $_start = date("Y-m-d");
            $_end = date("Y-m-d");
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
        while($otherTaskDate <= $task['ProjectTask']['task_end_date']){
            $chk = true;
            $intdate = strtotime($otherTaskDate);
            if (strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday') {
                $duration--;
                $chk = false;
            }
            if (!empty($holidays[$intdate])) {
               $duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);
               $chk = false;
            }
            if (!empty($requests_absence[$intdate])) {
                if ($requests_absence[$intdate]['response_am'] == 'validated') {
                    $duration -= 0.5;
                }
                if ($requests_absence[$intdate]['response_pm'] == 'validated') {
                    $duration -= 0.5;
                }
                $chk = false;
            }
            if ($chk == true) {
                $rageDate[$otherTaskDate] = $otherTaskDate;
            }
            $duration++;
            $otherTaskDate =  date('Y-m-d',strtotime($otherTaskDate. ' + 1 day'));
        }
        $__ragedate = $this->RageDate($stardate ,$endate) ;
        $count = 0;
        $endnote = false;
        foreach($__ragedate as $date){
            if (!empty($rageDate[$date])) {
                if ($task['ProjectTask']['task_end_date'] == $date) {
                    $endnote = true;
                }
                $count++;
            }
        }
        if ($duration == 0) {
            $duration = 1;
        }
        if ($endnote == true) {
            return round($estimated/$duration,2) * $count + ($estimated - round($estimated/$duration,2) * $duration);
        } else {
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
    private function countdate($startdate ,$enddate ,$employee_id){
        $employee_curr = $this->_getEmpoyee();
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
            if (strtolower(date('l',$intdate))=='sunday' || strtolower(date('l',$intdate)) == 'saturday') {
                $duration--;
            }
            if (!empty($holidays[$intdate])) {
               $duration  -= ($holidays[$intdate]['am'] + $holidays[$intdate]['pm']);
            }
            if (!empty($requests_absence[$intdate])) {
                if ($requests_absence[$intdate]['response_am'] == 'validated') {
                    $duration -= 0.5;
                }
                if ($requests_absence[$intdate]['response_pm'] == 'validated') {
                    $duration -= 0.5;
                }
            }
            $startdate =  date('Y-m-d',strtotime($startdate. ' + 1 day'));
        }
        return $duration;
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
            if ($_date == 'saturday' || $_date == 'sunday') {
                //do nothing
            } else {
                $count++;
            }
            $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
        }
        return $count;
    }

    /**
     *  Function skip value
     */
    public function skipValue($model = null, $modelId = null, $valueDay = 0, $valueWeek = 0, $valueMonth = 0){
        $result = 'false';
        $this->loadModels('ProjectPhasePlan', 'NctWorkload');
        $checked = !empty($_POST['checked']) ? $_POST['checked'] : 0;
        if($model == 'project') {
            if (!$this->_checkRole(true, $modelId, empty($valueDay) ? array('element' => 'warning') : array())) {
                $valueDay = 0;
            }
            if (!$this->_checkRole(true, $modelId, empty($valueWeek) ? array('element' => 'warning') : array())) {
                $valueWeek = 0;
            }
            if (!$this->_checkRole(true, $modelId, empty($valueMonth) ? array('element' => 'warning') : array())) {
                $valueMonth = 0;
            }
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $modelId, 'predecessor' => null),
                'fields' => array('id', 'project_planed_phase_id', 'task_start_date', 'duration', 'parent_id', 'is_nct')
            ));
            $idOfNctTasks = $nctTasks = array();
            if(!empty($projectTasks)){
                $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
                $listPhases = $saveYears = array();
                foreach($projectTasks as $taskId => $projectTask){
                    if(!empty( $projectTask['task_start_date']) && ($projectTask['task_start_date'] != '0000-00-00')){
                        $Y = date('Y', strtotime($projectTask['task_start_date']));
                        $saveYears[$Y] = $Y;
                    }
                    if($projectTask['is_nct']){
                        $idOfNctTasks[$taskId] = $taskId;
                        $isNCT = $this->NctWorkload->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('project_task_id' => $taskId),
                        ));
                        if(!empty($isNCT)){
                            $nctTasks[$taskId] = $projectTask;
                        }
                        
                    } else {
                        if(!empty( $projectTask['task_start_date']) && ($projectTask['task_start_date'] != '0000-00-00')){
                            $_start = strtolower(date("l", strtotime($projectTask['task_start_date'])));
                            $startDate = strtotime($projectTask['task_start_date']);
                            if($_start == 'sunday'){
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            } elseif($_start == 'saturday') {
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+2, date("Y", $startDate));
                            }
                            $projectTask['task_start_date'] = date('Y-m-d', $startDate);
                            if($valueDay >= 0){
                                $newStartDate = $this->_durationEndDate($projectTask['task_start_date'], '0000-00-00', $valueDay + 1);
                            } else {
                                $newStartDate = $this->_durationDiffEndDate($projectTask['task_start_date'], '0000-00-00', $valueDay);
                            }
                            $projectTask['duration'] = ($projectTask['duration'] == 0 || $projectTask['duration'] == '') ? 1 : $projectTask['duration'];
                            $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $projectTask['duration']);
                            $saved = array(
                                'task_start_date' => $newStartDate,
                                'task_end_date' => $newEndDate,
                            );
                            $Y = date('Y', strtotime($newStartDate));
                            $saveYears[$Y] = $Y;
                            $Y = date('Y', strtotime($newEndDate));
                            $saveYears[$Y] = $Y;
                            $this->ProjectTask->id = $taskId;
                            $this->ProjectTask->save($saved);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['start'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['start'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['end'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['end'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                        }
                    }
                }
                $startHoliday = strtotime('01-01-' . (min($saveYears) - 3));
                $endHoliday = strtotime('31-12-' . (max($saveYears) + 3));
                $company_id = $this->employee_info['Company']['id'];
                $holidays   = array_keys(ClassRegistry::init('Holiday')->getOptionHolidays($startHoliday, $endHoliday, $company_id));
                 /**
                  * Lay du lieu cua table nct tasks
                  */
                $nctWorkloads = $this->NctWorkload->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $idOfNctTasks),
                    'fields' => array('id', 'task_date', 'end_date', 'group_date', 'project_task_id')
                ));
                $taskDates = array();
                if(!empty($nctWorkloads)){
                    foreach($nctWorkloads as $nctWorkload){
                        $dx = $nctWorkload['NctWorkload'];
                        if(empty($dx['group_date'])){
                            if($valueDay >= 0){
                                $newStartDate = $this->_durationEndDate($dx['task_date'], '0000-00-00', $valueDay + 1);
                                $newStartDate = strtotime($newStartDate);
                                while(in_array($newStartDate, $holidays) || strtolower(date("l", $newStartDate)) == 'saturday' || strtolower(date("l", $newStartDate)) == 'sunday'){
                                    $newStartDate = mktime(0, 0, 0, date("m", $newStartDate), date("d", $newStartDate)+1, date("Y", $newStartDate));
                                }
                                $saved = array(
                                    'task_date' => date('Y-m-d', $newStartDate),
                                    'end_date' => date('Y-m-d', $newStartDate)
                                );
                            }else{
                                $newStartDate = $this->_durationDiffEndDate($dx['task_date'], '0000-00-00', $valueDay);
                                $newStartDate = strtotime($newStartDate);
                                while(in_array($newStartDate, $holidays) || strtolower(date("l", $newStartDate)) == 'saturday' || strtolower(date("l", $newStartDate)) == 'sunday'){
                                    $newStartDate = mktime(0, 0, 0, date("m", $newStartDate), date("d", $newStartDate)-1, date("Y", $newStartDate));
                                }
                                $saved = array(
                                    'task_date' => date('Y-m-d', $newStartDate),
                                    'end_date' => date('Y-m-d', $newStartDate)
                                );
                            }
                        } else {
                            $dateType = substr($dx['group_date'], 0, 1);
                            if($dateType == 1){
                                $startDate = strtotime($dx['task_date']);
                                $newStartDate = strtotime('+' . $valueWeek . ' week', $startDate);
                                $newEndDate = strtotime('friday this week', $newStartDate);
                            } else if($dateType == 2) {
                                $startDate = strtotime($dx['task_date']);
                                $newStartDate = strtotime('+' . $valueMonth . ' month', $startDate);
                                $newEndDate = strtotime('last day of this month', $newStartDate);
                            }
                            $saved = array(
                                'task_date' => date('Y-m-d', $newStartDate),
                                'end_date' => date('Y-m-d', $newEndDate),
                                'group_date' => $dateType . '_' . date('d-m-Y', $newStartDate) . '_' . date('d-m-Y', $newEndDate)
                            );
                        }
                        if(!isset($taskDates[$dx['project_task_id']])){
                            $taskDates[$dx['project_task_id']] = $newStartDate;
                        }
                        $taskDates[$dx['project_task_id']] = min($taskDates[$dx['project_task_id']], $newStartDate);
                        $this->NctWorkload->id = $dx['id'];
                        $this->NctWorkload->save($saved);
                    }
                }else if($valueDay == 0){
                    // for nct task haven't assign to
                    foreach($projectTasks as $taskId => $projectTask){
                        if(!empty( $projectTask['task_start_date']) && ($projectTask['task_start_date'] != '0000-00-00')){
                            $_start = strtolower(date("l", strtotime($projectTask['task_start_date'])));
                            $startDate = strtotime($projectTask['task_start_date']);

                            if($_start == 'sunday'){
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            } elseif($_start == 'saturday') {
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+2, date("Y", $startDate));
                            }
                            $projectTask['task_start_date'] = date('Y-m-d', $startDate);
                            $newStartDate = strtotime('+' . $valueMonth . ' month', $startDate);

                            $newStartDate = date('Y-m-d', $newStartDate);
                            
                            $projectTask['duration'] = ($projectTask['duration'] == 0 || $projectTask['duration'] == '') ? 1 : $projectTask['duration'];
                            $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $projectTask['duration']);
                           
                            $saved = array(
                                'task_start_date' => $newStartDate,
                                'task_end_date' => $newEndDate,
                            );
                            $Y = date('Y', strtotime($newStartDate));
                            $saveYears[$Y] = $Y;
                            $Y = date('Y', strtotime($newEndDate));
                            $saveYears[$Y] = $Y;
                            $this->ProjectTask->id = $taskId;
                            $this->ProjectTask->save($saved);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['start'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['start'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['end'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['end'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                        }
                    }
                }
            }
            if(!empty($taskDates) && !empty($nctTasks)){
                foreach($nctTasks as $pTaskId => $nctTask){
                    $newStartDate = date('Y-m-d', $taskDates[$pTaskId]);
                    $nctTask['duration'] = ($nctTask['duration'] == 0 || $nctTask['duration'] == '') ? 1 : $nctTask['duration'];
                    $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $nctTask['duration']);
                    $saved = array(
                        'task_start_date' => $newStartDate,
                        'task_end_date' => $newEndDate,
                    );
                    $this->ProjectTask->id = $pTaskId;
                    $this->ProjectTask->save($saved);
                    if(!isset($listPhases[$nctTask['project_planed_phase_id']]['start'])){
                        $listPhases[$nctTask['project_planed_phase_id']]['start'] = array();
                    }
                    $listPhases[$nctTask['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                    if(!isset($listPhases[$nctTask['project_planed_phase_id']]['end'])){
                        $listPhases[$nctTask['project_planed_phase_id']]['end'] = array();
                    }
                    $listPhases[$nctTask['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                }
            }
            /**
             * thao tac thuc hien luu cac predecessor
             */
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $modelId),
                'fields' => array('id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'duration', 'parent_id', 'predecessor')
            ));
            $listTaskPres = array();
            if(!empty($projectTasks)){
                foreach($projectTasks as $projectTask){
                    $dx = $projectTask['ProjectTask'];
                    if(!empty($dx['predecessor'])){
                        $listTaskPres[$dx['id']] = $dx;
                    }
                }
                $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
                if(!empty($listTaskPres)){
                    foreach($listTaskPres as $id => $listTaskPre){
                        $_start = !empty($projectTasks[$listTaskPre['predecessor']]['task_end_date']) ? $projectTasks[$listTaskPre['predecessor']]['task_end_date'] : '0000-00-00';
                        if(!empty( $_start) && ($_start != '0000-00-00')){
                            $startDate = strtotime($_start);
                            $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            $checkDay = strtolower(date("l", $startDate));
                            if($checkDay == 'sunday'){
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            } elseif($checkDay == 'saturday') {
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+2, date("Y", $startDate));
                            }

                            $newStartDate = date('Y-m-d', $startDate);
                            $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $listTaskPre['duration']);
                            $saved = array(
                                'task_start_date' => $newStartDate,
                                'task_end_date' => $newEndDate,
                            );
                            $this->ProjectTask->id = $id;
                            $this->ProjectTask->save($saved);
                            if(!isset($listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['start'])){
                                $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['start'] = array();
                            }
                            $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                            if(!isset($listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['end'])){
                                $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['end'] = array();
                            }
                            $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                        }
                    }
                }
            }
            if(!empty($listPhases)){
                foreach($listPhases as $planId => $list){
                    $realStart = min($list['start']);
                    $realEnd = max($list['end']);
                    $saved = array(
                        'phase_real_start_date' => date('Y-m-d', $realStart),
                        'phase_real_end_date' => date('Y-m-d', $realEnd)
                    );
                    $this->ProjectPhasePlan->id = $planId;
                    $this->ProjectPhasePlan->save($saved);
                }
            }
            // get real to plan date.
            if($checked == 1){
                $time_reset = $this->ProjectPhasePlan->find("all", array(
                    "fields"=> array('id','phase_real_start_date','phase_real_end_date'),
                    "conditions"=> array('project_id' => $modelId),
                    "recursive" => -1
                ));
                foreach($time_reset as $phase){
                    $this->ProjectPhasePlan->id = $phase['ProjectPhasePlan']['id'];
                    if($phase['ProjectPhasePlan']['phase_real_start_date'] != '0000-00-00'){
                        $this->ProjectPhasePlan->set('phase_planed_start_date',$phase['ProjectPhasePlan']['phase_real_start_date']);
                    }
                    if($phase['ProjectPhasePlan']['phase_real_end_date'] != '0000-00-00'){
                        $this->ProjectPhasePlan->set('phase_planed_end_date',$phase['ProjectPhasePlan']['phase_real_end_date']);
                    }
                    $this->ProjectPhasePlan->save();
                }
            }
            $this->_saveStartEndDateAllTask($modelId);
            $result = 'true';
        } else {

        }
        $this->set(compact('result'));
    }
    public function drapDrop() {
        $type = $_POST['type'];
        if (!empty($_POST['idCurrentNode'])) {
            //get task current detail
            $taskCurrentDetail = $this->ProjectTask->find("first", array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $_POST['idCurrentNode']))
            );
            $projectDetail = ClassRegistry::init('Project')->find("first", array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $taskCurrentDetail['ProjectTask']['project_id']))
            );
            if (!empty($taskCurrentDetail)) {
                $currentOldProjectPhase = $taskCurrentDetail['ProjectTask']['project_planed_phase_id'];
                //get phase detail of current task
                $oldPhasePlanDetail = ClassRegistry::init('ProjectPhasePlan')->find('first', array(
                    'recursive' => -1,
                    'conditions' => array("ProjectPhasePlan.id" => $taskCurrentDetail['ProjectTask']['project_planed_phase_id']),
                ));
                if ($taskCurrentDetail['ProjectTask']['parent_id'] == 0) {
                    //set task is parent task
                    $currentIsParentTask = true;
                    //get sub-task for parent task
                    $listSubtaskForTaskId = $this->ProjectTask->find("all", array(
                    'recursive' => -1,
                    'fields' => array('id','task_title','weight'),
                    'conditions' => array(
                        'ProjectTask.parent_id' => $_POST['idCurrentNode'],
                        'ProjectTask.project_id' =>  $taskCurrentDetail['ProjectTask']['project_id']
                    ))
                    );
                } else {
                    $currentIsParentTask = false;
                    $parentOfCurrentTask = $taskCurrentDetail['ProjectTask']['parent_id'];
                    //get detail parent of current task
                    $parentOfCurrentTaskDetail = $this->ProjectTask->find("first", array(
                        'recursive' => -1,
                        'conditions' => array('ProjectTask.id' => $taskCurrentDetail['ProjectTask']['parent_id']))
                    );
                }
            }
        }
        if (isset($_POST['idOverNode']) && isset($_POST['overNodeIsPhase']) && $_POST['overNodeIsPhase'] == 0) {
            //get task over detail
            $taskOverDetail = $this->ProjectTask->find("first", array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $_POST['idOverNode']))
            );
            if (isset($taskOverDetail)) {
                if ($taskOverDetail['ProjectTask']['parent_id'] == 0) {
                    //set task is parent task
                    $overIsParentTask = true;
                } else {
                    //set task is parent task
                    $overIsParentTask = false;
                    $parentTaskOfOver = $taskOverDetail['ProjectTask']['parent_id'];
                    //get task over detail
                    $parentTaskOfOverDetail = $this->ProjectTask->find("first", array(
                        'recursive' => -1,
                        'conditions' => array('ProjectTask.id' => $parentTaskOfOver))
                    );
                }
                //get phase detail
                $phasePlanDetail = ClassRegistry::init('ProjectPhasePlan')->find('first', array(
                    'recursive' => -1,
                    'conditions' => array("ProjectPhasePlan.id" => $taskOverDetail['ProjectTask']['project_planed_phase_id']),
                ));
            }
        } else {
            $taskOverDetail = null;
        }
        if (!empty($_POST['overNodeIsPhase']) && $_POST['overNodeIsPhase'] == 1) {
                //get phase detail
                $phasePlanDetail = ClassRegistry::init('ProjectPhasePlan')->find('first', array(
                    'recursive' => -1,
                    'conditions' => array("ProjectPhasePlan.id" => $_POST['idOverNode']),
                 ));
                $newTaskParent = 0;
                $newProjectPhase = $_POST['idOverNode'];
                if (isset($listSubtaskForTaskId) && !empty($listSubtaskForTaskId)) {
                    foreach($listSubtaskForTaskId as $value) {
                        $saved = array(
                            'project_planed_phase_id' => $_POST['idOverNode']
                        );
                        $this->ProjectTask->id = $value['ProjectTask']['id'];
                        $this->ProjectTask->save($saved);
                    }
                }
                $totalTaskParentOfPhase = $this->ProjectTask->find('count', array(
                    'recursive' => -1,
                    'conditions' => array("ProjectTask.project_planed_phase_id" => $_POST['idOverNode'],"ProjectTask.parent_id" => 0)
                ));
                //update start date and end date for phase
                if ($totalTaskParentOfPhase == 0) {
                    $this->updateStartDateForPhase($taskCurrentDetail['ProjectTask']['task_start_date'],$_POST['idOverNode']);
                    $this->updateEndDateForPhase($taskCurrentDetail['ProjectTask']['task_end_date'],$_POST['idOverNode']);
                } else {
                    if ($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$phasePlanDetail['ProjectPhasePlan']['phase_real_start_date']) == 1) {
                        $this->updateStartDateForPhase($taskCurrentDetail['ProjectTask']['task_start_date'],$_POST['idOverNode']);
                    }
                    if($this->compareDate($phasePlanDetail['ProjectPhasePlan']['phase_real_end_date'],$taskCurrentDetail['ProjectTask']['task_end_date']) == 1) {
                        $this->updateEndDateForPhase($taskCurrentDetail['ProjectTask']['task_end_date'],$_POST['idOverNode']);
                    }
                }
                $moveType = 1; //move task into phase
        } else {
            if($type == 'append') {
                $flag = 0;
                $newTaskParent = $taskOverDetail['ProjectTask']['id'];
                $newStartDayOfParentTask=$taskOverDetail['ProjectTask']['task_start_date'];
                $newEndDayOfParentTask=$taskOverDetail['ProjectTask']['task_end_date'];
                //check sub-task of task over
                $totalSubtaskOfOverTask = $this->ProjectTask->find('count', array(
                    'recursive' => -1,
                    'conditions' => array("ProjectTask.parent_id" =>$taskOverDetail['ProjectTask']['id'])
                ));
                if($totalSubtaskOfOverTask == 0) {
                    //update start date and end date for over task
                    $this->updateStartDateForParentTask($taskCurrentDetail['ProjectTask']['task_start_date'],$taskOverDetail['ProjectTask']['id']);
                    $newStartDayOfParentTask=$taskCurrentDetail['ProjectTask']['task_start_date'];
                    $this->updateEndDateForParentTask($taskCurrentDetail['ProjectTask']['task_end_date'],$taskOverDetail['ProjectTask']['id']);
                    $newEndDayOfParentTask = $taskCurrentDetail['ProjectTask']['task_end_date'];
                    $flag = 1;
                } else {
                    //update start date and end date for over task
                    if($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$taskOverDetail['ProjectTask']['task_start_date']) == 1) {
                        $this->updateStartDateForParentTask($taskCurrentDetail['ProjectTask']['task_start_date'],$taskOverDetail['ProjectTask']['id']);
                        $flag = 1;
                        $newStartDayOfParentTask=$taskCurrentDetail['ProjectTask']['task_start_date'];
                    }
                    if($this->compareDate($taskOverDetail['ProjectTask']['task_end_date'],$taskCurrentDetail['ProjectTask']['task_end_date'])==1) {
                        $this->updateEndDateForParentTask($taskCurrentDetail['ProjectTask']['task_end_date'],$taskOverDetail['ProjectTask']['id']);
                        $flag = 1;
                        $newEndDayOfParentTask=$taskCurrentDetail['ProjectTask']['task_end_date'];
                    }
                }
                //update duration for task
                if ($flag == 1) {
                    $this->updateDurationForParentTask($this->getWorkingDays($newStartDayOfParentTask,$newEndDayOfParentTask,''),$taskOverDetail['ProjectTask']['id']);
                }
                //update workload(estimated) + overload for task
                $newWorkloadForParentTask=$taskOverDetail['ProjectTask']['estimated'] + $taskCurrentDetail['ProjectTask']['estimated'];
                $newOverloadForParentTask=$taskOverDetail['ProjectTask']['overload'] + $taskCurrentDetail['ProjectTask']['overload'];
                $this->updateWorkloadOverloadForParentTask($newWorkloadForParentTask, $newOverloadForParentTask, $taskOverDetail['ProjectTask']['id']);
                $moveType = 2; //move task into task parent
            } else {
                if(!empty($overIsParentTask) && $overIsParentTask) {
                    //update start date and end date for phase of over task
                    /*if ($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$phasePlanDetail['ProjectPhasePlan']['phase_real_start_date']) == 1) {
                        $this->updateStartDateForPhase($taskCurrentDetail['ProjectTask']['task_start_date'],$phasePlanDetail['ProjectPhasePlan']['id']);
                    }
                    if ($this->compareDate($phasePlanDetail['ProjectPhasePlan']['phase_real_end_date'],$taskCurrentDetail['ProjectTask']['task_end_date']) == 1) {
                        $this->updateEndDateForPhase($taskCurrentDetail['ProjectTask']['task_end_date'],$phasePlanDetail['ProjectPhasePlan']['id']);
                    }*/
                    if($type=='before')
                        $moveType = 3; //move task before parent task
                    else
                        $moveType = 6; //move task after parent task
                } else {
                    $flag = 0;
                    $newStartDayOfParentTask = $parentTaskOfOverDetail['ProjectTask']['task_start_date'];
                    $newEndDayOfParentTask = $parentTaskOfOverDetail['ProjectTask']['task_end_date'];
                    //update start date and end date for over task
                    if($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$parentTaskOfOverDetail['ProjectTask']['task_start_date']) == 1) {
                        $this->updateStartDateForParentTask($taskCurrentDetail['ProjectTask']['task_start_date'],$parentTaskOfOverDetail['ProjectTask']['id']);
                        $flag=1;
                        $newStartDayOfParentTask=$taskCurrentDetail['ProjectTask']['task_start_date'];
                    }
                    if($this->compareDate($parentTaskOfOverDetail['ProjectTask']['task_end_date'],$taskCurrentDetail['ProjectTask']['task_end_date']) == 1) {
                        $this->updateEndDateForParentTask($taskCurrentDetail['ProjectTask']['task_end_date'],$parentTaskOfOverDetail['ProjectTask']['id']);
                        $flag = 1;
                        $newEndDayOfParentTask=$taskCurrentDetail['ProjectTask']['task_end_date'];
                    }
                    //update duration for task
                    if($flag == 1) {
                        $this->updateDurationForParentTask($this->getWorkingDays($newStartDayOfParentTask,$newEndDayOfParentTask,''),$parentTaskOfOverDetail['ProjectTask']['id']);
                    }
                    //update workload(estimated) + overload for task
                    $newWorkloadForParentTask=$parentTaskOfOverDetail['ProjectTask']['estimated']+$taskCurrentDetail['ProjectTask']['estimated'];
                    $newOverloadForParentTask=$parentTaskOfOverDetail['ProjectTask']['overload']+$taskCurrentDetail['ProjectTask']['overload'];
                    $this->updateWorkloadOverloadForParentTask($newWorkloadForParentTask,$newOverloadForParentTask,$parentTaskOfOverDetail['ProjectTask']['id']);
                    if($type == 'before')
                        $moveType = 4; //move task before sub-task
                    else
                        $moveType = 5; //move task after sub-task
                }
                $newTaskParent = $taskOverDetail['ProjectTask']['parent_id'];
            }
            $newProjectPhase = $taskOverDetail['ProjectTask']['project_planed_phase_id'];
            //update start date and end date for phase of over task
            if($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$phasePlanDetail['ProjectPhasePlan']['phase_real_start_date']) == 1) {
                $this->updateStartDateForPhase($taskCurrentDetail['ProjectTask']['task_start_date'],$phasePlanDetail['ProjectPhasePlan']['id']);
            }
            if($this->compareDate($phasePlanDetail['ProjectPhasePlan']['phase_real_end_date'],$taskCurrentDetail['ProjectTask']['task_end_date']) == 1) {
                $this->updateEndDateForPhase($taskCurrentDetail['ProjectTask']['task_end_date'],$phasePlanDetail['ProjectPhasePlan']['id']);
            }
        }
        //update parent and project phase for task
        $saved = array(
            'parent_id' => $newTaskParent,
            'project_planed_phase_id' => $newProjectPhase
        );
        $this->ProjectTask->id = $taskCurrentDetail['ProjectTask']['id'];
        $this->ProjectTask->save($saved);
        //update project phase for sub-task
        if(!empty($listSubtaskForTaskId) && !empty($listSubtaskForTaskId)) {
            foreach($listSubtaskForTaskId as $value) {
                $saved = array(
                    'project_planed_phase_id' => $newProjectPhase
                );
                $this->ProjectTask->id = $value['ProjectTask']['id'];
                $this->ProjectTask->save($saved);
            }
        }

        //update current task parent after move sub-task
        if (!empty($parentOfCurrentTaskDetail) && ((!empty($parentOfCurrentTask) && !$currentIsParentTask && !empty($taskOverDetail) && ($parentOfCurrentTask != $taskOverDetail['ProjectTask']['id'])) || $taskOverDetail === null || (!empty($parentOfCurrentTask) && !$currentIsParentTask && !empty($taskOverDetail) && ($type!='append') && ($parentOfCurrentTask == $taskOverDetail['ProjectTask']['id'])))) {
            $temp = 0;
            $newStartDayOfParentTask = $parentOfCurrentTaskDetail['ProjectTask']['task_start_date'];
            $newEndDayOfParentTask = $parentOfCurrentTaskDetail['ProjectTask']['task_end_date'];
            if($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$parentOfCurrentTaskDetail['ProjectTask']['task_start_date']) == -1) {
                //get min start date of project tasks by parent task id
                $minStartDate = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'fields' => 'task_start_date',
                    'order' => array('ProjectTask.task_start_date ASC'),
                    'conditions' => array("ProjectTask.parent_id" => $parentOfCurrentTask)
                ));
                if(!empty($minStartDate) && $minStartDate['ProjectTask']['task_start_date'] != 0 && $minStartDate['ProjectTask']['task_start_date'] != ''){
                    $this->updateStartDateForParentTask($minStartDate['ProjectTask']['task_start_date'],$parentOfCurrentTask);
                    $temp = 1;
                    $newStartDayOfParentTask = $minStartDate['ProjectTask']['task_start_date'];
                }
            }
            if ($this->compareDate($taskCurrentDetail['ProjectTask']['task_end_date'],$parentOfCurrentTaskDetail['ProjectTask']['task_end_date']) == -1) {
                //get max end date of project tasks by parent task id
                $maxEndDate = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'fields' => 'task_end_date',
                    'order' => array('ProjectTask.task_end_date DESC'),
                    'conditions' => array("ProjectTask.parent_id" => $parentOfCurrentTask)
                ));
                if (!empty($maxEndDate) && $maxEndDate['ProjectTask']['task_end_date'] != 0 && $maxEndDate['ProjectTask']['task_end_date'] != '') {
                    $this->updateEndDateForParentTask($maxEndDate['ProjectTask']['task_end_date'],$parentOfCurrentTask);
                    $temp = 1;
                    $newEndDayOfParentTask = $maxEndDate['ProjectTask']['task_end_date'];
                }
            }
            //update duration+workload(estimated)+overload
            if ($temp == 1) {
                $this->updateDurationForParentTask($this->getWorkingDays($newStartDayOfParentTask,$newEndDayOfParentTask,''),$parentOfCurrentTask);
            }
            //update workload(estimated) + overload for task
            $newWorkloadForParentTask = $parentOfCurrentTaskDetail['ProjectTask']['estimated'] - $taskCurrentDetail['ProjectTask']['estimated'];
            $newOverloadForParentTask = $parentOfCurrentTaskDetail['ProjectTask']['overload'] - $taskCurrentDetail['ProjectTask']['overload'];
            $this->updateWorkloadOverloadForParentTask($newWorkloadForParentTask, $newOverloadForParentTask, $parentOfCurrentTask);
        }

        //update phase current task after move task or sub-task
        if ($currentOldProjectPhase != $newProjectPhase) {
            //update start date and end date
            if ($this->compareDate($taskCurrentDetail['ProjectTask']['task_start_date'],$oldPhasePlanDetail['ProjectPhasePlan']['phase_real_start_date']) ==- 1){
                //get min start date of project tasks by phase id
                $minStartDate = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'fields' => 'task_start_date',
                    'order' => array('ProjectTask.task_start_date ASC'),
                    'conditions' => array("ProjectTask.project_planed_phase_id" => $currentOldProjectPhase)
                ));
                if (!empty($minStartDate) && $minStartDate['ProjectTask']['task_start_date'] !=0 && $minStartDate['ProjectTask']['task_start_date'] != '')
                $this->updateStartDateForPhase($minStartDate['ProjectTask']['task_start_date'],$currentOldProjectPhase);
            }
            if ($this->compareDate($taskCurrentDetail['ProjectTask']['task_end_date'],$oldPhasePlanDetail['ProjectPhasePlan']['phase_real_end_date']) ==- 1) {
                //get max end date of project tasks by phase id
                $maxEndDate = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'fields' => 'task_end_date',
                    'order' => array('ProjectTask.task_end_date DESC'),
                    'conditions' => array("ProjectTask.project_planed_phase_id" => $currentOldProjectPhase)
                ));
                if (!empty($maxEndDate) && $maxEndDate['ProjectTask']['task_end_date']!=0 && $maxEndDate['ProjectTask']['task_end_date']!='')
                $this->updateEndDateForPhase($maxEndDate['ProjectTask']['task_end_date'],$currentOldProjectPhase);
            }
            //update phase_planed_start_date=phase_real_end_date, phase_planed_end_date=phase_real_end_date if phase not containing task
            $totalTaskOfPhase = $this->ProjectTask->find('count', array(
                    'recursive' => -1,
                    'conditions' => array("ProjectTask.project_planed_phase_id" => $currentOldProjectPhase)
                ));
            if ($totalTaskOfPhase == 0) {
                ClassRegistry::init('ProjectPhasePlan')->updateAll(
                    array('ProjectPhasePlan.phase_real_start_date' => 'ProjectPhasePlan.phase_planed_start_date',
                            'ProjectPhasePlan.phase_real_end_date' => 'ProjectPhasePlan.phase_planed_end_date'),
                    array('ProjectPhasePlan.id' => $currentOldProjectPhase)
                );
            }
        }
        //update Name activity task if project link activity
        if ($projectDetail['Project']['activity_id'] != '' && $projectDetail['Project']['activity_id'] != 0) {
            $activityId = $projectDetail['Project']['activity_id'];
            if ($moveType == 1 || $moveType == 3 || $moveType == 6) {
                $parentIdActivityTask = '';
                $newParentId = null;
                $newNameActivityTask = $this->getNameActivityTask($taskCurrentDetail['ProjectTask']['task_title'],$newProjectPhase);
                if (!$currentIsParentTask) {
                    //$newNameActivityTask=$this->getNameActivityTask($taskCurrentDetail['ProjectTask']['task_title'],$newProjectPhase);
                } else {
                    //$newNameActivityTask=$this->getNameActivityTask($taskCurrentDetail['ProjectTask']['task_title'],$newProjectPhase);
                    if(isset($listSubtaskForTaskId) && !empty($listSubtaskForTaskId)) {
                        $newNameActivityTask = $this->getNameActivityTask($taskCurrentDetail['ProjectTask']['task_title'],$newProjectPhase);
                        foreach($listSubtaskForTaskId as $value) {
                            $newNameActivityTaskSubTask = $this->getNameActivityTask($taskCurrentDetail['ProjectTask']['task_title'].'/'.$value['ProjectTask']['task_title'],$newProjectPhase);
                            ClassRegistry::init('ActivityTask')->unbindModel(
                                array('belongsTo' => array('Activity'))
                            );
                            ClassRegistry::init('ActivityTask')->updateAll(
                                array('name' => "'$newNameActivityTaskSubTask'"),
                                array('activity_id' => $activityId,'project_task_id'=>$value['ProjectTask']['id'])
                            );
                        }
                    }
                }
            } else if ($moveType == 2) {
                //get id activity_task by task parent id + activity id.
                $activityByTaskIdAndActivityId = ClassRegistry::init('ActivityTask')->find('first', array(
                    'recursive' => -1,
                    'fields' => 'id',
                    'conditions' => array('activity_id' => $activityId,'project_task_id'=>$taskOverDetail['ProjectTask']['id'])));
                $newParentId = $activityByTaskIdAndActivityId['ActivityTask']['id'];
                $newNameActivityTask = $this->getNameActivityTask($taskOverDetail['ProjectTask']['task_title'].'/'.$taskCurrentDetail['ProjectTask']['task_title'],$newProjectPhase);
            } else {
                //get id activity_task by task parent id + activity id.
                $activityByTaskIdAndActivityId = ClassRegistry::init('ActivityTask')->find('first', array(
                    'recursive' => -1,
                    'fields'=>'id',
                    'conditions'=>array('activity_id' => $activityId,'project_task_id' => $parentTaskOfOverDetail['ProjectTask']['id'])));
                $newParentId = $activityByTaskIdAndActivityId['ActivityTask']['id'];
                $newNameActivityTask = $this->getNameActivityTask($parentTaskOfOverDetail['ProjectTask']['task_title'].'/'.$taskCurrentDetail['ProjectTask']['task_title'],$newProjectPhase);
            }
            ClassRegistry::init('ActivityTask')->unbindModel(
                array('belongsTo' => array('Activity'))
            );
            ClassRegistry::init('ActivityTask')->updateAll(
                array('name' => "'$newNameActivityTask'",'parent_id' => "'$newParentId'"),
                array('activity_id' => $activityId,'project_task_id' => $taskCurrentDetail['ProjectTask']['id'])
            );
        }
        if ($moveType == 1) {
            //update weight for task of phase
            $this->updateWeightAllTask($taskCurrentDetail['ProjectTask']['id'],$taskCurrentDetail['ProjectTask']['weight'],$newProjectPhase,-1,1);
        } else if ($moveType == 3) {
            //update weight for task of phase
            $this->updateWeightAllTask($taskCurrentDetail['ProjectTask']['id'],$taskCurrentDetail['ProjectTask']['weight'],$newProjectPhase,$taskOverDetail['ProjectTask']['weight'],1);
        } else if ($moveType == 6) {
            //update weight for task of phase
            $this->updateWeightAllTask($taskCurrentDetail['ProjectTask']['id'],$taskCurrentDetail['ProjectTask']['weight'],$newProjectPhase,$taskOverDetail['ProjectTask']['weight']+1,1);
        } else if ($moveType == 2) {
            //update weight for sub-task
            $this->updateWeightAllTask($taskCurrentDetail['ProjectTask']['id'],$taskCurrentDetail['ProjectTask']['weight'],$taskOverDetail['ProjectTask']['id'],-1,0);
        } else if ($moveType == 4) {
            //update weight for sub-task
            $this->updateWeightAllTask($taskCurrentDetail['ProjectTask']['id'],$taskCurrentDetail['ProjectTask']['weight'],$parentTaskOfOverDetail['ProjectTask']['id'],$taskOverDetail['ProjectTask']['weight'],0);
        } else {
            //update weight for sub-task
            $this->updateWeightAllTask($taskCurrentDetail['ProjectTask']['id'],$taskCurrentDetail['ProjectTask']['weight'],$parentTaskOfOverDetail['ProjectTask']['id'],$taskOverDetail['ProjectTask']['weight']+1,0);
        }
        //end
        $project_id = $taskCurrentDetail['ProjectTask']['project_id'];
        //$this->_checkRole(false, $project_id);
        // load tat cac cac task cua project task
        $projectTasks = $this->_getAllProjectTasks($project_id);
        // lay danh sach id cua project task
        $listIdOfProjectTasks = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
        // Load activity tasks lien ket voi project task
        $activityTasks = $this->_getActivityTasks($listIdOfProjectTasks);
        // Attach Consume Calculator from RMS
        $activityRequests = $this->_getActivityRequests($project_id, $activityTasks);
        // Attach Employees Assigned to Project Tasks Array
        $projectTasks = $this->_attachReferencedEmployees($projectTasks);
        // Attach Consumed to Project Tasks Array
        $projectTasks = $this->_attachConsumed($projectTasks, $activityTasks, $activityRequests);
        // Build tree structure
        $children = $this->_buildJsonStructure($projectTasks, $project_id);
        // set result to extjx
        $result = $children[0];
        echo json_encode($result);
        $this->_deleteCacheContextMenu();
        exit();
    }

    public function compareDate($child,$parent) {
        $childTemp = strtotime($child);
        $parentTemp = strtotime($parent);
        if ($childTemp == 0 || $parentTemp == 0) return 1;
        if ($childTemp < $parentTemp) {
            return 1;
        } else if($childTemp > $parentTemp) {
            return 0;
        } else {
            return -1;
        }
    }

    public function updateStartDateForPhase($newStartDate,$phaseId) {
        if($newStartDate == '0000-00-00' || $newStartDate === null) {
            return false;
        }
        $saved = array(
            'phase_real_start_date' => $newStartDate
        );
        ClassRegistry::init('ProjectPhasePlan')->id = $phaseId;
        ClassRegistry::init('ProjectPhasePlan')->save($saved);
    }

    public function updateEndDateForPhase($newEndDate,$phaseId) {
        if ($newEndDate == '0000-00-00' || $newEndDate === null) {
            return false;
        }
        $saved = array(
            'phase_real_end_date' => $newEndDate
        );
        ClassRegistry::init('ProjectPhasePlan')->id=$phaseId;
        ClassRegistry::init('ProjectPhasePlan')->save($saved);
    }

    public function updateStartDateForParentTask($newStartDate,$taskId) {
        if ($newStartDate == '0000-00-00' || $newStartDate === null) {
            return false;
        }
        $saved = array(
            'task_start_date' => $newStartDate
        );
        $this->ProjectTask->id = $taskId;
        $this->ProjectTask->save($saved);
    }

    public function updateEndDateForParentTask($newEndDate,$taskId) {
        if ($newEndDate == '0000-00-00' || $newEndDate === null) {
            return false;
        }
        $saved = array(
            'task_end_date' => $newEndDate
        );
        $this->ProjectTask->id = $taskId;
        $this->ProjectTask->save($saved);
    }

    public function updateDurationForParentTask($newDuration,$taskId) {
        $saved = array(
            'duration' => $newDuration
        );
        $this->ProjectTask->id = $taskId;
        $this->ProjectTask->save($saved);
    }

    public function updateWorkloadOverloadForParentTask($newWorkload,$newOverload,$taskId) {
        $saved = array(
            'estimated' => $newWorkload,
            'overload' => $newOverload
        );
        $this->ProjectTask->id = $taskId;
        $this->ProjectTask->save($saved);
    }
    public function getNameActivityTask($taskTitle,$phaseId) {
        $phaseDetail=ClassRegistry::init('ProjectPhasePlan')->find('first', array(
            'recursive' => 1,
            'conditions' => array("ProjectPhasePlan.id" => $phaseId)
        ));
        return $phaseDetail['ProjectPhase']['name'].'/'.$taskTitle;
    }
    public function updateWeightForTask($newWeight,$taskId) {
        $saved = array(
            'weight' => $newWeight
        );
        $this->ProjectTask->id = $taskId;
        $this->ProjectTask->save($saved);
    }
    function updateWeightAllTask($currentId,$oldOrder,$parentId,$newOrder,$type) {
        if ($type == 1) {
            $taskList = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "ProjectTask.project_planed_phase_id" => $parentId,
                    "ProjectTask.parent_id" => 0,
                    "ProjectTask.id <>" => $currentId
                ),
                'order' => array('ProjectTask.weight ASC')
            ));
        } else {
            $taskList = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "ProjectTask.parent_id" => $parentId,
                    "ProjectTask.id <>" => $currentId
                ),
                'order' => array('ProjectTask.weight ASC')
            ));
        }
        $countTaskList = count($taskList) + 1;
        if ($newOrder == -1) {
            $newOrder = $countTaskList;
        } else if ($newOrder==0) {
            $newOrder=1;
        }
        /*else {
            if($newOrder>$countTaskList) {
                $newOrder=$countTaskList;
            }
        }*/
        $this->updateWeightForTask($newOrder,$currentId);
        $current = 1;
        $current1 = $newOrder + 1;
        foreach($taskList as $value) {
            if ($value['ProjectTask']['weight'] < $newOrder) {
                $this->updateWeightForTask($current,$value['ProjectTask']['id']);
                $current = $current + 1;
            } else {
                $this->updateWeightForTask($current1,$value['ProjectTask']['id']);
                $current1 = $current1 + 1;
            }
        }
    }

    public function formatWeightAllTaskOfProject() {
        $db = ConnectionManager::getDataSource('default');
        $listProject = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('ProjectTask.project_id'),
                'group' => 'ProjectTask.project_id'
            ));
        foreach($listProject as $value) {
            $listPhaseOfProject = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('ProjectTask.project_planed_phase_id'),
                'group' => 'ProjectTask.project_planed_phase_id',
                'conditions' => array('ProjectTask.project_id' => $value['ProjectTask']['project_id'])
            ));
            foreach($listPhaseOfProject as $value1) {
                $sql = "SET @i = 0;";
                $datas = $db->query($sql);
                $sql = "UPDATE `project_tasks` SET `weight` = @i:=@i+1 WHERE `project_planed_phase_id` = '".$value1['ProjectTask']['project_planed_phase_id']."' AND `parent_id` = 0 ORDER BY `weight` ASC";
                $datas = $db->query($sql);
                $listTaskOfPhase = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'fields' => array('ProjectTask.id'),
                    'conditions' => array('ProjectTask.parent_id' => 0, 'ProjectTask.project_planed_phase_id' => $value1['ProjectTask']['project_planed_phase_id'])
                ));
                foreach($listTaskOfPhase as $value2) {
                    $sql1 = "SET @j = 0;";
                    $datas = $db->query($sql1);
                    $sql1 = "UPDATE `project_tasks` SET `weight` = @j:=@j+1 WHERE `parent_id` = '".$value2['ProjectTask']['id']."' ORDER BY `weight` ASC";
                    $datas = $db->query($sql1);
                }
            }
        }
        exit('Format completed');
    }
    /**
     * If case on exists then notify.
     *
     * @param int $project_id
     * @return void
     * @access public
     */
    function import_csv($project_id = null) {
        //$this->autoRender = false;
        $employeeName = $this->_getEmpoyee();
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
            $this->MultiFileUpload->_properties['MaxSize'] = 25 * 1024 * 1000;
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
                        'Part Name',
                        'Manual Consumed',
                        'Status'
                    );
                    $default = array();
                    if (!empty($csv->titles)) {
                        foreach($csv->titles as $headName) {
                            if (in_array($headName, $columnMandatory)) {
                                $default[$headName] = '';
                            }
                        }
                    }
                    $this->loadModel('ProjectPhasePlan');
                    $this->loadModel('ProjectPhase');
                    $this->loadModel('Employee');
                    $this->loadModel('ProjectStatus');
                    $this->loadModel('ProfitCenter');
                    $this->ProjectPhase->cacheQueries = true;
                    $this->Employee->cacheQueries = true;
                    $validate = array('Task Name');
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    // tach parent task va cac tham so can thiet
                    $groupNamePhaseInputs = $listTreeTasks = array();
                    foreach($csv->data as $val) {
                        if (!empty($val['Phase Name'])) {
                            $groupNamePhaseInputs[] = trim($val['Phase Name']);
                            $part = !empty($val['Part Name']) ? trim($val['Part Name']) : -1;
                            $phase = trim($val['Phase Name']);
                            $parent = isset($val['Parent Task Name']) ? $val['Parent Task Name'] : '';
                            $task = trim($val['Task Name']);
                            if (!empty($parent)) {
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
                    $this->ProjectStatus->virtualFields['lname'] = 'LCASE(ProjectStatus.name)';
                    $statuses = $this->ProjectStatus->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $employeeName['company_id']
                        ),
						'order' => 'weight',
                        'fields' => array('lname', 'id')
                    ));
                    //pc
                    $this->ProfitCenter->virtualFields['lname'] = 'LCASE(ProfitCenter.name)';
                    $profits = $this->ProfitCenter->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('lname', 'id')
                    ));
                    //part
                    $this->loadModel('ProjectPart');
                    $parts = $this->ProjectPart->find('list', array(
                        'recursive' => -1,
                        'fields' => array('title' , 'id'),
                        'conditions' => array('project_id' => $project_id)));
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
                            //trim
                            foreach($columnMandatory as $key){
                                if( isset($row[$key]) && is_string($row[$key]) ){
                                    $row[$key] = trim($row[$key]);
                                }
                            }
                            // get phase id and part id
                            $projectPhaseId = $projectPartId = null;
                            // Part
                            if( !empty($row['Part Name']) ){
                                if( !empty($parts[ $row['Part Name'] ]) ){
                                    $projectPartId = $parts[$row['Part Name']];
                                } else {
                                    //insert
                                    $this->ProjectPart->create();
                                    $this->ProjectPart->save(array(
                                        'project_id' => $project_id,
                                        'title' => $row['Part Name']
                                    ));
                                    $projectPartId = $this->ProjectPart->id;
                                    $parts[ $row['Part Name'] ] = $projectPartId;
                                }
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

                                if( empty($projectPhasePlans) ){
                                    //create part/phase combination data
                                    $this->ProjectPhasePlan->create();
                                    $this->ProjectPhasePlan->save(array(
                                        'project_id' => $project_id,
                                        'project_part_id' => $projectPartId,
                                        'project_planed_phase_id' => $projectPhaseId
                                    ));
                                    $projectPhasePlans['ProjectPhasePlan']['id'] = $this->ProjectPhasePlan->id;
                                }
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
                                            if(in_array($row['Parent Task Name'], $listEndChildrens)){
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
                                                        $this->loadModel('ActivityTask');
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
                                                            // $checkTaskImports = $this->ProjectTask->find('first', array(
                                                            //     'recursive' => -1,
                                                            //     'conditions' => array(
                                                            //         'project_id' => $project_id,
                                                            //         'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                            //         'task_title' => trim($row['Task Name']),
                                                            //         'parent_id' => $parentTasks['ProjectTask']['id']
                                                            //     ),
                                                            //     'fields' => array('id')
                                                            // ));
                                                            // if(!empty($checkTaskImports) && $checkTaskImports['ProjectTask']['id']){
                                                            //     $row['columnHighLight']['Task Name'] = '';
                                                            //     $row['error'][] = __('The sub task name has exists', true);
                                                            // } else {
                                                                $row['data']['task_title'] = $row['Task Name'];
                                                                $listSubTaskHaveImports[$projectPhasePlans['ProjectPhasePlan']['id']][trim($row['Parent Task Name'])][trim($row['Task Name'])] = trim($row['Task Name']);
                                                            // }
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
                                        if (in_array(trim($row['Task Name']), array_keys($listParentTaskImports)) && in_array($projectPhasePlans['ProjectPhasePlan']['id'], $listParentTaskImports)) {
                                            $row['columnHighLight']['Task Name'] = '';
                                            $row['error'][] = __('The task name has identical', true);
                                        } else {
                                            // $checkTasks = $this->ProjectTask->find('first', array(
                                            //     'recursive' => -1,
                                            //     'conditions' => array(
                                            //         'project_id' => $project_id,
                                            //         'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                            //         'task_title' => trim($row['Task Name'])
                                            //     ),
                                            //     'fields' => array('id')
                                            // ));
                                            // if(!empty($checkTasks) && $checkTasks['ProjectTask']['id']){
                                            //     $row['columnHighLight']['Task Name'] = '';
                                            //     $row['error'][] = __('The task name has exists', true);
                                            // } else {
                                            $listParentTaskImports[trim($row['Task Name'])] = $projectPhasePlans['ProjectPhasePlan']['id'];
                                            $row['data']['task_title'] = $row['Task Name'];
                                            // }
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
                                    if (!empty($row['Parent Task Name'])) {
                                        if (in_array(trim($row['Parent Task Name']), array_keys($listParentTaskImports)) && in_array($projectPhasePlans['ProjectPhasePlan']['id'], $listParentTaskImports)) {
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
                                            if (!empty($checkTasks) && $checkTasks['ProjectTask']['id']) {
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
                            if (!empty($row['Start Date']) || !empty($row['End Date'])) {
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
                                if($_start > $_end){
                                    $row['columnHighLight']['End Date'] = '';
                                    $row['error'][] = __('The end date must be greater than start date.', true);
                                } else {
                                    $row['data']['task_start_date'] = $_start;
                                    $row['data']['task_end_date'] = $_end;
                                    $row['data']['duration'] = $this->getWorkingDays(date('Y-m-d', $_start), date('Y-m-d', $_end), '');
                                }
                            } else {
                                $row['data']['task_start_date'] = 0;
                                $row['data']['task_end_date'] = 0;
                                $row['data']['duration'] = '';
                            }
                            // Workload
                            if(isset($row['Workload']) && is_numeric($row['Workload'])) {
                                $row['data']['estimated'] = $row['Workload'];
                            } else {
                                $row['data']['estimated'] = $row['Workload'] = 0;
                            }
                            //manual consumed
                            if( isset($row['Manual Consumed']) ){
                                $row['data']['manual_consumed'] = is_numeric($row['Manual Consumed']) ? $row['Manual Consumed'] : 0;
                            }
                            // Assign To
                            if(!empty($row['Assigned To'])){
                                $assigns = explode(',', $row['Assigned To']);
                                if(!empty($assigns)){
                                    $listAssigns = array();
                                    foreach($assigns as $assign){
                                        $assign = strtolower(trim($assign));
                                        //pc
                                        if( strpos($assign, '/') !== false ){
                                            $pc = explode('/', $assign);
                                            $pc = trim($pc[1]);
                                            if( isset($profits[$pc]) ){
                                                $listAssigns[] = $profits[$pc] . '-1';
                                            }
                                        } else {
                                            if(in_array($assign, array_keys($employeeIdCompanies))){
                                                $listAssigns[] = $employeeIdCompanies[$assign] . '-0';
                                            }
                                        }
                                    }
                                    if(!empty($listAssigns)){
                                        $row['data']['assign'] = implode(',', $listAssigns);
                                    }
                                }
                            }
                            // them project id va status id cho task duoc assign
                            $row['data']['project_id'] = $project_id;
                            if( isset($row['Status']) && isset($statuses[ strtolower($row['Status']) ])){
                                $row['data']['task_status_id'] = $statuses[ strtolower($row['Status']) ];
                            } else {
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
            $this->set(compact('project_id'));
        } else {
            $this->set(compact('project_id'));
            $this->redirect(array('action' => 'index', $project_id));
        }
    }
    /**
     * Save import of project task
     */
    function save_file_import($project_id = null) {
        set_time_limit(0);
        $this->loadModel('Project');
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
                    $this->redirect(array('action' => 'index', $project_id));
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
                $this->Session->setFlash(sprintf(__('Task(s) imported %s/%s.', true), $complete, $totalRecordImport));
                $this->redirect(array('action' => 'index', $project_id));
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
                    $this->redirect(array('action' => 'index', $project_id));
                }
            }
        } else {
            $this->redirect(array('action' => 'index', $project_id));
        }
        exit;
    }
    /**
     * Dung de luu cac phan import task.
     * 1. Luu cac task duoc import vao project task.
     * 2. Luu cac assign to cua task duoc import vao project task employee refer.
     * 3. Neu co linked voi activity thi luu cac project task qua ben activity task.
     * 4. Luu employee/pc vao team neu trong project team cua project chua co employee/pc nay
     */
    private function _saveImportDataDetail($data = null, $projects = null){
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
                $parentDuration = $this->getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
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
                        $this->loadModel('ActivityTask');
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
                        $parentDuration = $this->getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
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
        if (isset($data['manual_consumed'])) $saved['manual_consumed'] = $data['manual_consumed'];
        $assignedTos = !empty($data['assign']) ? explode(',', $data['assign']) : array();
        /**
         * Luu cac task va parent task thuoc project task nay
         */
        //
        $task = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'task_title' => $data['task_title'],
                'parent_id' => $parentId,
                'project_id' => $data['project_id'],
                'project_planed_phase_id' => $data['project_planed_phase_id']
            )
        ));
        if( empty($task) ){
            $this->ProjectTask->create();
        } else {
            $this->ProjectTask->id = $task['ProjectTask']['id'];
        }
        if($this->ProjectTask->save($saved)){
            $lastId = $this->ProjectTask->id;
            /**
             * Neu co linked thi tien hanh luu project task qua ben activity task
             */
            if(!empty($projects) && ($projects['Project']['activity_id'] != 0 || $projects['Project']['activity_id'] != '')){
                $this->loadModel('ActivityTask');
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
                $atask = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'name' => $aTaskName,
                    'parent_id' => $parentActivityTask,
                    'activity_id' => $projects['Project']['activity_id'],
                    'project_task_id' => $lastId
                ));
                if( empty($atask) ){
                    $this->ActivityTask->create();
                } else {
                    $this->ActivityTask->id = $atask['ActivityTask']['id'];
                }
                if($this->ActivityTask->save($savedActivityTask)){
                    $lastActivityTaskId = $this->ActivityTask->getLastInsertID();
                }
            }
            /**
             * Luu cac assigned cua task
             */
            $this->loadModel('ProjectTaskEmployeeRefer');
            $this->ProjectTaskEmployeeRefer->deleteAll(array(
                    'project_task_id' => $lastId
            ));
            if(!empty($assignedTos)){
                $savedAssigns = array();
                foreach($assignedTos as $key => $employeeId){
                    $estimated = ($key == 0) ? $data['estimated'] : 0;
                    $e = explode('-', $employeeId);
                    $savedAssigns[] = array(
                        'reference_id' => $e[0],
                        'project_task_id' => $lastId,
                        'estimated' => $estimated,
                        'is_profit_center' => intval($e[1])
                    );
                }
                if(!empty($savedAssigns)){
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
                        //employees
                        if( !$savedAssign['is_profit_center'] ){
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
                                    $lastTeamId = $this->ProjectTeam->id;
                                    $savedTeam = array(
                                        'employee_id' => $savedAssign['reference_id'],
                                        'profit_center_id' => $profit,
                                        'project_team_id' => $lastTeamId
                                    );
                                    $this->ProjectFunctionEmployeeRefer->create();
                                    $this->ProjectFunctionEmployeeRefer->save($savedTeam);
                                }
                            }
                        } else {
                            $tmps = $this->ProjectFunctionEmployeeRefer->find('count', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'profit_center_id' => $savedAssign['reference_id'],
                                    'project_team_id' => $teams
                                )
                            ));
                            if($tmps == 0){
                                $this->ProjectTeam->create();
                                if($this->ProjectTeam->save(array('project_id' => $data['project_id']))){
                                    $lastTeamId = $this->ProjectTeam->id;
                                    $savedTeam = array(
                                        'profit_center_id' => $savedAssign['reference_id'],
                                        'project_team_id' => $lastTeamId
                                    );
                                    $this->ProjectFunctionEmployeeRefer->create();
                                    $this->ProjectFunctionEmployeeRefer->save($savedTeam);
                                }
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
                    'fields' => array('phase_planed_start_date', 'phase_planed_end_date')
                ));
                if(!empty($projectPhases)){
                    $start = strtotime($projectPhases['ProjectPhasePlan']['phase_planed_start_date']);
                    $end = strtotime($projectPhases['ProjectPhasePlan']['phase_planed_end_date']);
                    if( $start <= 0 || $data['task_start_date'] < $start){
                        $start = $data['task_start_date'];
                    }
                    if($data['task_end_date'] > 0 && $data['task_end_date'] > $end){
                        $end = $data['task_end_date'];
                    }
                    $_end = date('Y-m-d', $end);
                    $_start = date('Y-m-d', $start);
                    $savePhased = array(
                        'phase_planed_start_date' => $_start,
                        'phase_planed_end_date' => $_end,
                        'phase_real_start_date' => $_start,
                        'phase_real_end_date' => $_end,
                        'planed_duration' => $this->getWorkingDays($_start, $_end, 0)
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
    function drapDropTest() {
        echo "vao day";
        exit();
    }
    function freeze($project_id = null){
        if ($project_id) {
            $this->loadModel('Project');
            $this->loadModel('Activity');
            $this->loadModel('ActivityTask');
            $this->Project->read(null,$project_id);
            $this->Project->set(array('is_freeze' =>1,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
            $this->Project->save();
            // dong bang
            $ptasks = $this->ProjectTask->find('all',array('recursive'=>-1,'conditions'=>array(
                'project_id'=>$project_id)
            ));
            $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $project_id)
                ));
            $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
            if($activityLinked){
                $this->Activity->read(null,$projectName['Project']['activity_id']);
                $this->Activity->set(array('is_freeze' =>1,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
                $this->Activity->save();
            }
            foreach($ptasks as $ptask){
                $_dataUpdate = array();
                $this->ProjectTask->id = $ptask['ProjectTask']['id'];
                $_dataUpdate['initial_estimated'] = $ptask['ProjectTask']['estimated'];
                $_dataUpdate['initial_task_start_date'] = $ptask['ProjectTask']['task_start_date'];
                $_dataUpdate['initial_task_end_date'] = $ptask['ProjectTask']['task_end_date'];
                $this->ProjectTask->save($_dataUpdate, false);
                if($activityLinked){
                    $_dataUpdateActivity = array();
                    $checkEdit = $this->ActivityTask->find('first',array('conditions'=>array(
                                'ActivityTask.project_task_id'=>$ptask['ProjectTask']['id']
                                ),
                                'recursive'=>-1,
                                'fields'=>'id'
                                ));
                    $this->ActivityTask->id =  $checkEdit['ActivityTask']['id'];
                    $_dataUpdateActivity['initial_estimated'] = $ptask['ProjectTask']['estimated'];
                    $_dataUpdateActivity['initial_task_start_date'] = $ptask['ProjectTask']['task_start_date'];
                    $_dataUpdateActivity['initial_task_end_date'] = $ptask['ProjectTask']['task_end_date'];
                    $this->ActivityTask->save($_dataUpdateActivity, false);
                }
            }
            // dong bang activity task

            $this->Session->setFlash(__('Saved', true), 'success');
            $this->redirect(array('action' => 'index',$project_id));
        }else{
            $this->Session->setFlash(__('Not saved', true), 'error');
            $this->redirect(array('action' => 'index',$project_id));
        }
    }
    function unfreeze($project_id = null){
        if ($project_id) {
            $this->loadModel('Project');
            $this->loadModel('Activity');
            $this->loadModel('ActivityTask');
            $this->Project->read(null,$project_id);
            $this->Project->set(array('is_freeze' =>0,'freeze_by'=>$this->employee_info['Employee']['id'],'freeze_time'=>strtotime("now")));
            $this->Project->save();
            // tat dong bang
            $ptasks = $this->ProjectTask->find('all',array('recursive'=>-1,'conditions'=>array(
                'project_id'=>$project_id),
                'fields'=>array()
            ));
            $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $project_id)
                ));
            $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
            if ($activityLinked) {
                $this->Activity->read(null,$projectName['Project']['activity_id']);
                $this->Activity->set(array('is_freeze' => 0, 'freeze_by' => $this->employee_info['Employee']['id'], 'freeze_time' => strtotime("now")));
                $this->Activity->save();
            }
            foreach($ptasks as $ptask){
                $_dataUpdate = array();
                $this->ProjectTask->id = $ptask['ProjectTask']['id'];
                $_dataUpdate['initial_estimated'] = 0;
                $_dataUpdate['initial_task_start_date'] = null;
                $_dataUpdate['initial_task_end_date'] = null;
                $this->ProjectTask->save($_dataUpdate);
                if ($activityLinked) {
                    $_dataUpdateActivity = array();
                    $checkEdit = $this->ActivityTask->find('first',array(
                        'conditions'=>array(
                            'ActivityTask.project_task_id'=>$ptask['ProjectTask']['id']
                        ),
                        'recursive'=>-1,
                        'fields'=>'id'
                    ));
                    $this->ActivityTask->id = $checkEdit['ActivityTask']['id'];
                    $_dataUpdateActivity['initial_estimated'] = 0;
                    $_dataUpdateActivity['initial_task_start_date'] = null;
                    $_dataUpdateActivity['initial_task_end_date'] = null;
                    $this->ActivityTask->save($_dataUpdateActivity);
                }
            }
            $this->Session->setFlash(__('Saved', true), 'success');
            $this->redirect(array('action' => 'index',$project_id));
        }else{
            $this->Session->setFlash(__('Not saved', true), 'error');
            $this->redirect(array('action' => 'index',$project_id));
        }
    }
    function update_initial($project_id = null, $check = null){
            $this->loadModel('Project');
            $this->Project->id = $project_id;
            $_dataAL['off_freeze'] = $check;
            $this->Project->save($_dataAL);
            $this->Session->setFlash(__('Saved', true), 'success');
            $this->redirect(array('action' => 'index',$project_id));
    }
    function taskExists($pid, $name, $parent, $phase, $part = null){
        $this->loadModels('ProjectPhasePlan');
        $plan = $this->ProjectPhasePlan->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $pid,
                'project_planed_phase_id' => $phase,
                'project_part_id' => $part ? $part : null,
                '1=1'
            )
        ));
        if( !empty($plan) ){
            $planId = $plan['ProjectPhasePlan']['id'];
            //lay parent
            $task = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectTask.task_title' => $name,
                    'ProjectTask.project_planed_phase_id' => $planId
                ),
                'joins' => array(
                    array(
                        'table' => 'project_tasks',
                        'alias' => 'Parent',
                        'type' => 'left',
                        'conditions' => array('Parent.id = ProjectTask.parent_id', 'Parent.task_title' => $parent)
                    )
                ),
                'fields' => array('ProjectTask.id', 'Parent.task_title', 'Parent.id')
            ));
            if( !empty($task) ){
                if( $parent == $task['Parent']['task_title'] )
                    return $task;
            }
        }
        return false;
    }
    function import_task_micro_project($project_id = null){
        if (!$this->_checkRole(true, $project_id, empty($_FILES['FileField']['name']['micro_file_attachment']) ? array('element' => 'warning') : array())) {
            $_FILES['FileField']['name']['micro_file_attachment'] = '';
        }
        if (!empty($_FILES['FileField']['name']['micro_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Tasks' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "xml";
            $this->MultiFileUpload->_properties['MaxSize'] = 25 * 1024 * 1000;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Tasks' . DS . $reVal['micro_file_attachment']['micro_file_attachment'];
            }
        } else {
            die('No file uploaded!');
        }
        $fp = fopen(TMP . 'uploads' . DS . 'Tasks' . DS . $reVal['micro_file_attachment']['micro_file_attachment'], 'r');
        $contents = fread($fp, filesize(TMP . 'uploads' . DS . 'Tasks' . DS . $reVal['micro_file_attachment']['micro_file_attachment']));
        //$contents = utf8_decode($contents);
        fclose($fp);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($contents);

         // tim nhung resource tuong ung task
        $resources = $dom->documentElement->getElementsByTagName('Resource');
        $resourceList = array();
        foreach($resources as $resource):
            $resourceUIDs = $resource->getElementsByTagName("UID");
            $resourceUID = !$resourceUIDs->length?'':$resourceUIDs->item(0)->nodeValue;
            $resourceNames = $resource->getElementsByTagName("Name");
            if( $resourceNames->length ){
                $resourceName = $resourceNames->item(0)->nodeValue;
                $resourceList[$resourceUID] = $resourceName;
            }
        endforeach;
        // tim nhung asign task
        $assigns = $dom->documentElement->getElementsByTagName('Assignment');
        $assignResource = array();
        foreach($assigns as $assign):
            $XMLtaskIDs = $assign->getElementsByTagName("TaskUID");
            $XMLtaskID = !$XMLtaskIDs->length?'':$XMLtaskIDs->item(0)->nodeValue;
            $XMLassignIDs = $assign->getElementsByTagName("ResourceUID");
            $val = !$XMLassignIDs->length?'':$XMLassignIDs->item(0)->nodeValue;
            if( !isset($resourceList[$val]) )continue;
            $assignResource[$XMLtaskID][] = $resourceList[$val];
        endforeach;

        $params = $dom->documentElement->getElementsByTagName('Task');
        $i=0;
        $tasks = array();
        $path = array();
        foreach($params as $param){
            $uids = $param->getElementsByTagName("UID");
            $uid = !($uids->length)?'':$uids->item(0)->nodeValue;
            $wbss = $param->getElementsByTagName("OutlineNumber");
            $outline = !$wbss->length?'':$wbss->item(0)->nodeValue;
            $names = $param->getElementsByTagName("Name");
            $name = !$names->length?'':$names->item(0)->nodeValue;
            $starts = $param->getElementsByTagName("Start");
            $start = !$starts->length?'':$starts->item(0)->nodeValue;
            $start = explode('T',$start);
            $start = $start[0];
            $ends = $param->getElementsByTagName("Finish");
            $end = !$ends->length?'':$ends->item(0)->nodeValue;
            $end = explode('T', $end);
            $end = $end[0];
            $predecesorParents = !($param->getElementsByTagName("PredecessorLink")->length)?array():$param->getElementsByTagName("PredecessorLink");
            $predecesor = null;
            foreach($predecesorParents as $p) {
                $predecesors = !isset($p->length) || !($p->length)?'':$p->getElementsByTagName("PredecessorUID");
                $predecesor = !isset($predecesors->length) || !($predecesors->length)?null:$predecesors->item(0)->nodeValue;
            }
            //neu la milestone
            if( $param->getElementsByTagName('Milestone')->item(0)->nodeValue ){
                $tasks[$outline] = array(
                    'TaskName' => $name,
                    'StartDate' => $start,
                    'EndDate' => $end,
                    'AssignedTo' => '',
                    'PartName' => '',
                    'PhaseName' => '',
                    'ParentName' => '',
                    'type' => 'Milestone'
                );
            } else {
                //kiem tra level cua task
                $level = (int) $param->getElementsByTagName('OutlineLevel')->item(0)->nodeValue;
                if(!$level) continue;
                $outlines = explode('.', $outline);
                array_pop($outlines);
                //neu co parent thi update so luong level con cua parents
                $part = null;
                $phase = null;
                if( $level > 1 ){
                    $path = array();
                    foreach($outlines as $p){
                        $path[] = $p;
                        $parentOutline = implode('.', $path);
                        $tasks[$parentOutline]['childrenDepth'] = $level - $tasks[$parentOutline]['level'];
                        $type = 'Task';
                        //else if( $tasks[$parentOutline]['level'] == 1 && $tasks[$parentOutline]['childrenDepth'] == 0 )$type = 'SingleTask';
                        if( ($tasks[$parentOutline]['level'] == 1 && $tasks[$parentOutline]['childrenDepth'] == 1) || ($tasks[$parentOutline]['level'] == 2 && ($tasks[$parentOutline]['childrenDepth'] == 1 || $tasks[$parentOutline]['childrenDepth'] == 2)) ){
                            $phase = $tasks[$parentOutline]['TaskName'];
                            $type = 'Phase';
                        }
                        else if( $tasks[$parentOutline]['level'] == 1 && ($tasks[$parentOutline]['childrenDepth'] == 2 || $tasks[$parentOutline]['childrenDepth'] == 3) ){
                            $type = 'Part';
                            $part = $tasks[$parentOutline]['TaskName'];
                        }
                        $tasks[$parentOutline]['type'] = $type;
                    }
                }
                //can cu vao (level & childrenDepth) de xac dinh task la part, phase, task, sub task hay la task le?
                /*
                Level   childrenDepth   Result
                1       0               Single task
                1       1               Phase
                1       2               Part
                1       3               Part
                2       0               Task
                2       1               Phase
                2       2               Phase
                3       *               Task
                4       0               Sub Task

                */
                $parent = implode('.', $outlines);
                $assigns = isset($assignResource[$uid]) ? $assignResource[$uid] : array();
                $tasks[$outline] = array(
                    'level' => $level,
                    'childrenDepth' => 0,
                    'TaskName' => $name,
                    'StartDate' => $start,
                    'EndDate' => $end,
                    'AssignedTo' => implode(';', $assigns),
                    'task_assign_to' => $assigns,
                    'type' => 'Task',
                    'ParentName' => $level > 1 ? $tasks[$parent]['TaskName'] : '',
                    'PartName' => $part,
                    'PhaseName' => $phase,
                    'percent_complete' => $param->getElementsByTagName('PercentComplete')->item(0)->nodeValue
                );
            }
        }
        // ket thuc nhap lieu sub task
        $this->set(compact('project_id'));
        $records = array(
            'Task' => array(),
            'Update' => array(),
            'Milestone' => array(),
            'SingleTask' => array(),
            'Phase' => array(),
        );
        $employeeName = $this->_getEmpoyee();
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ProjectPhase');
        $this->loadModel('ProjectPart');
        $this->loadModel('ProfitCenter');
        $this->loadModel('Employee');
        $this->loadModel('CompanyEmployeeReference');
        $this->loadModel('ProjectStatus');
        $this->loadModel('Profile');
        $this->Employee->virtualFields['full_name'] = 'UCASE(CONCAT(Employee.first_name, " ", Employee.last_name))';
        $checkAssigns = $this->Employee->find('list', array(
            'recursive' => -1,
            'joins'  => array(
                array(
                    'table' => 'company_employee_references',
                    'alias' => 'Refer',
                    'conditions' => array('Refer.employee_id = Employee.id')
                )
            ),
            'conditions' => array(
                'Refer.company_id' => $employeeName['company_id'],
                'NOT' => array('Employee.is_sas' => 1),
                'OR' => array(
                    array('Employee.end_date' => '0000-00-00'),
                    array('Employee.end_date IS NULL'),
                    array('Employee.end_date >=' => date('Y-m-d', time())),
                )
            ),
            'fields' => array('full_name', 'Employee.id')
        ));
        $this->ProfitCenter->virtualFields['upper'] = 'UCASE(ProfitCenter.name)';
        $checkPAssigns = $this->ProfitCenter->find('list', array(
            'conditions' => array(
                'ProfitCenter.company_id' => $employeeName['company_id']
            ),
            'fields' => array('upper', 'ProfitCenter.id')
        ));
        $phases = $this->ProjectPhase->find('list',array(
            'conditions'=>array('company_id'=>$employeeName['company_id']),
            'recursive'=>-1,
            'fields'=>array('name', 'id')
        ));
        $parts = $this->ProjectPart->find('list',array(
            'conditions'=>array('project_id'=>$project_id),
            'recursive'=>-1,
            'fields'=>array('title', 'id')
        ));
        $this->Profile->virtualFields['upper'] = 'UCASE(Profile.name)';
        $this->ProjectTask->virtualFields['path'] = 'CONCAT(CASE WHEN Part.title IS NULL THEN "" ELSE Part.title END, "|", Phase.name, "|", CASE WHEN Parent.task_title IS NULL THEN "" ELSE Parent.task_title END, "|", ProjectTask.task_title)';
        $allTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectTask.project_id' => $project_id
            ),
            'joins' => array(
                array(
                    'table' => 'project_phase_plans',
                    'alias' => 'Plan',
                    'type' => 'inner',
                    'conditions' => array('Plan.id = ProjectTask.project_planed_phase_id')
                ),
                array(
                    'table' => 'project_parts',
                    'alias' => 'Part',
                    'type' => 'left',
                    'conditions' => array('Part.id = Plan.project_part_id')
                ),
                array(
                    'table' => 'project_phases',
                    'alias' => 'Phase',
                    'type' => 'inner',
                    'conditions' => array('Phase.id = Plan.project_planed_phase_id')
                ),
                array(
                    'table' => 'project_tasks',
                    'alias' => 'Parent',
                    'type' => 'left',
                    'conditions' => array('Parent.id = ProjectTask.parent_id')
                )
            ),
            'order' => array('ProjectTask.id' => 'ASC'),
            'fields' => array(
                'path', 'ProjectTask.id',
                //'ProjectTask.id', 'ProjectTask.task_title', 'ProjectTask.parent_id', 'Parent.task_title', 'Part.title', 'Phase.name', 'Plan.id', 'Phase.id', 'Part.id'
            )
        ));
        $this->loadModel('ProjectMilestone');
        $this->ProjectMilestone->virtualFields['xname'] = 'CONCAT("milestone-", `project_milestone`)';
        $stones = $this->ProjectMilestone->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'fields' => array('xname', 'id')
        ));
        $allTasks = array_merge($allTasks, $stones);
        $this->set('tasks', $allTasks);
        $preparePhases = array();
        foreach($tasks as $key => $task):
            //a hack for html ID attr
            $key = 'task-' . str_replace('.', '-', $key);
            if( $task['type'] == 'Task' ){
                $task['data']['task_title'] = $task['TaskName'];
                $task['data']['task_start_date'] = $task['StartDate'];
                $task['data']['task_end_date'] = $task['EndDate'];
                $task['data']['project_id'] = $project_id;
                //if single task
                if( $task['level'] == 1 ){
                    $task['data']['plan_id'] = null;
                    $assignTo = array();
                    $taskProfile = array();
                    foreach($task['task_assign_to'] as $assign){
                        $assign = strtoupper($assign);
                        if( isset($checkAssigns[$assign]) ){
                            $assignTo[] = $checkAssigns[$assign] . '-0';
                        } else if( isset($checkPAssigns[$assign]) ){
                            $assignTo[] = $checkPAssigns[$assign] . '-1';
                        }
                    }
                    $task['data']['task_assign_to'] = implode(',', $assignTo);
                    $records['SingleTask'][$key] = $task;
                } else {
                    //insert phase if not exist
                    $phaseName = trim($task['PhaseName']);
                    if( !isset($phases[$phaseName]) ){
                        $this->ProjectPhase->create();
                        $this->ProjectPhase->save(array(
                            'name' => $phaseName,
                            'company_id' => $employeeName['company_id']
                        ));
                        $phases[$phaseName] = $this->ProjectPhase->id;
                        $phaseId = $this->ProjectPhase->id;
                    } else {
                        $phaseId = $phases[$phaseName];
                    }
                    //insert part if not exist
                    $partId = null;
                    $partName = trim($task['PartName']);
                    if( $partName ){
                        if( !isset($parts[$partName]) ){
                            $this->ProjectPart->create();
                            $this->ProjectPart->save(array(
                                'title' => $partName,
                                'project_id' => $project_id
                            ));
                            $parts[$partName] = $this->ProjectPart->id;
                            $partId = $this->ProjectPart->id;
                        } else {
                            $partId = $parts[$partName];
                        }
                    }
                    $plan = $this->ProjectPhasePlan->find('first',array(
                        'conditions' => array('project_id' => $project_id, 'project_planed_phase_id' => $phaseId, 'project_part_id' => $partId),
                        'recursive' => -1,
                        'fields' => array('id', 'profile_id')
                    ));
                    //loai bo parent neu la task
                    if( ( $task['level'] == 2 && $task['childrenDepth'] == 0 ) || $task['level'] == 3 )$task['ParentName'] = '';
                    $task['data']['project_planed_phase_id'] = isset($plan['ProjectPhasePlan']['id']) ? $plan['ProjectPhasePlan']['id'] : null;
                    $task['data']['profile_id'] = isset($plan['ProjectPhasePlan']['profile_id']) ? $plan['ProjectPhasePlan']['profile_id'] : null;
                    $task['data']['phase_id'] = $phaseId;
                    $task['data']['part_id'] = $partId;
                    $task['data']['parent'] = $task['ParentName'];
                    $assignTo = array();
                    $taskProfile = array();
                    foreach($task['task_assign_to'] as $assign){
                        $assign = strtoupper($assign);
                        if( isset($checkAssigns[$assign]) ){
                            $assignTo[] = $checkAssigns[$assign] . '-0';
                        } else if( isset($checkPAssigns[$assign]) ){
                            $assignTo[] = $checkPAssigns[$assign] . '-1';
                        }
                    }
                    $task['data']['task_assign_to'] = implode(',', $assignTo);
                    if( $this->taskExists($project_id, $task['TaskName'], $task['ParentName'], $phaseId, $partId) ){
                        $records['Update'][$key] = $task;
                    } else {
                        $records['Task'][$key] = $task;
                    }
                }
            } else if( $task['type'] == 'Milestone' ){
                $task['data']['task_title'] = $task['TaskName'];
                $task['data']['task_start_date'] = $task['StartDate'];
                $task['data']['task_end_date'] = $task['EndDate'];
                $task['data']['project_id'] = $project_id;
                $records['Milestone'][$key] = $task;
            } else if( $task['type'] == 'Phase' ){
                $preparePhases[$key] = $task;
            }
        endforeach;
        //setup phase to update
        foreach($preparePhases as $key => $task){
            $phaseName = trim($task['TaskName']);
            $partName = trim($task['ParentName']);
            $task['phase_id'] = $phases[$phaseName];
            $task['part_id'] = null;
            if( $partName && isset($parts[$partName]) )$task['part_id'] = $parts[$partName];
            $records['Phase'][$key] = $task;
        }
        $this->ProjectPhasePlan->virtualFields['plan_name'] = 'CASE WHEN (ProjectPhasePlan.project_part_id IS NOT NULL OR ProjectPhasePlan.project_part_id = "") THEN CONCAT(Part.title, "|", Phase.name) ELSE Phase.name END';
        $plans = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectPhasePlan.project_id' => $project_id
            ),
            'joins' => array(
                array(
                    'table' => 'project_parts',
                    'alias' => 'Part',
                    'type' => 'left',
                    'conditions' => array('Part.id = ProjectPhasePlan.project_part_id')
                ),
                array(
                    'table' => 'project_phases',
                    'alias' => 'Phase',
                    'type' => 'left',
                    'conditions' => array('Phase.id = ProjectPhasePlan.project_planed_phase_id')
                )
            ),
            'order' => array('ProjectPhasePlan.weight' => 'ASC'),
            'fields' => array('ProjectPhasePlan.id', 'plan_name')
        ));
        $this->set('phases', array_flip($phases));
        $this->set('plans', $plans);
        $this->set('records',$records);
    }
    private function save_multi_tasks($project_id, $data, $use_plan_id = false){
        $this->ProjectTask->cacheQueries = false;
        $this->ProjectPhasePlan->recursive = -1;
        $newPhases = array();
        foreach($data as $val):
            $data_S = array();
            $data_S['task_title'] = $val['task_title'];
            $data_S['project_id'] = $project_id;
            //hack for un-categorized tasks
            if( $use_plan_id ){
                if( !$val['plan_id'] )continue;
                $type = explode('-', $val['plan_id']);
                if( $type[1] == 0 ){
                    $val['project_planed_phase_id'] = $type[0];
                    $val['parent'] = null;
                } else {
                    $val['phase_id'] = $type[0];
                    $val['part_id'] = null;
                    $val['parent'] = null;
                    $val['project_planed_phase_id'] = null;
                }
            }
            if(!$val['project_planed_phase_id']){
                //CHECK BEFORE INSERT
                $checkPlan = $this->ProjectPhasePlan->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $project_id,
                        'project_planed_phase_id' => $val['phase_id'],
                        'project_part_id' => $val['part_id'] ? $val['part_id'] : null
                    )
                ));
                if( empty($checkPlan) ){
                    $this->ProjectPhasePlan->create();
                    $this->ProjectPhasePlan->save(array(
                        'project_id' => $project_id,
                        'project_planed_phase_id' => $val['phase_id'],
                        'project_part_id' => $val['part_id'] ? $val['part_id'] : null,
                        //'progress' => floatval($val['percent_complete'] ? $val['percent_complete'] : 0)
                    ));
                    $val['project_planed_phase_id'] = $data_S['project_planed_phase_id'] = $this->ProjectPhasePlan->id;
                    $data_S['profile_id'] = null;
                } else {
                    $val['project_planed_phase_id'] = $checkPlan['ProjectPhasePlan']['id'];
                    $data_S['profile_id'] = $checkPlan['ProjectPhasePlan']['profile_id'];
                    goto __save_plan__;
                }
            } else {
                __save_plan__:
                $this->ProjectPhasePlan->id = $data_S['project_planed_phase_id'] = $val['project_planed_phase_id'];
                //lay profile
                $checkPlan = $this->ProjectPhasePlan->read('profile_id');
                $data_S['profile_id'] = @$checkPlan['ProjectPhasePlan']['profile_id'];
                //update progress for phase
                // $this->ProjectPhasePlan->save(array(
                //     'progress' => floatval($val['percent_complete'] ? $val['percent_complete'] : 0)
                // ));
            }
            $data_S['task_start_date'] = $val['task_start_date'];
            $data_S['task_end_date'] = $val['task_end_date'];
            $data_S['parent_id'] = 0;
            //lay parent id cua task
            if( !empty($val['parent']) ){
                $parent = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_title' => $val['parent'],
                        'project_planed_phase_id' => $val['project_planed_phase_id'],
                        'project_id' => $project_id,
                        '1=1'   //thêm cái này vào d? tránh tru?ng h?p cakephp cache l?i result c?a query bên du?i (ph?n ki?m tra task t?n t?i chua)
                    )
                ));
                if( !empty($parent) ){
                    $data_S['parent_id'] = $parent['ProjectTask']['id'];
                }
            }
            //kiem tra task ton tai chua
            $exist = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_title' => $val['task_title'],
                    'project_planed_phase_id' => $val['project_planed_phase_id'],
                    'project_id' => $project_id
                )
            ));
            if( !empty($exist) ){
                $data_S['id'] = $exist['ProjectTask']['id'];
            } else {
                $this->ProjectTask->create();
            }
            if( $this->ProjectTask->save($data_S) ){
                $refer_id = $this->ProjectTask->id;
                $this->ProjectTaskEmployeeRefer->deleteAll(array(
                    'ProjectTaskEmployeeRefer.project_task_id' => $refer_id
                ), false);
                //save assign
                if( $val['task_assign_to'] ){
                    $assigns = array();
                    $ss = explode(',', $val['task_assign_to']);
                    foreach($ss as $assign){
                        $assign = explode('-', trim($assign));
                        $assigns[] = array(
                            'reference_id' => $assign[0],
                            'project_task_id' => $refer_id,
                            'is_profit_center' => $assign[1]
                        );
                    }
                    $this->ProjectTaskEmployeeRefer->saveAll($assigns);
                    //TODO: sync parent task, sync phase, add team member
                    $teams = $this->ProjectTeam->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('project_id' => $project_id),
                        'fields' => array('id')
                    ));
                    foreach($assigns as $savedAssign){
                        if( !$savedAssign['is_profit_center'] ){
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
                                if($this->ProjectTeam->save(array('project_id' => $project_id))){
                                    $lastTeamId = $this->ProjectTeam->id;
                                    $savedTeam = array(
                                        'employee_id' => $savedAssign['reference_id'],
                                        'profit_center_id' => $profit,
                                        'project_team_id' => $lastTeamId
                                    );
                                    $this->ProjectFunctionEmployeeRefer->create();
                                    $this->ProjectFunctionEmployeeRefer->save($savedTeam);
                                }
                            }
                        } else {
                            $tmps = $this->ProjectFunctionEmployeeRefer->find('count', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'profit_center_id' => $savedAssign['reference_id'],
                                    'project_team_id' => $teams
                                )
                            ));
                            if($tmps == 0){
                                $this->ProjectTeam->create();
                                if($this->ProjectTeam->save(array('project_id' => $project_id))){
                                    $lastTeamId = $this->ProjectTeam->id;
                                    $savedTeam = array(
                                        'profit_center_id' => $savedAssign['reference_id'],
                                        'project_team_id' => $lastTeamId
                                    );
                                    $this->ProjectFunctionEmployeeRefer->create();
                                    $this->ProjectFunctionEmployeeRefer->save($savedTeam);
                                }
                            }
                        }
                    }
                }
                //sync parent + phase
                $project_task = $this->ProjectTask->read(null);
                $this->_syncPhasePlanTime($project_task);
                $this->_syncActivityTask($project_id, $project_task, $refer_id);
                $this->_deleteCacheContextMenu();
            }
        endforeach;
    }
    /**
     * Save import of project task
     */
    function save_file_import_micro($project_id = null) {
        set_time_limit(0);
        $this->loadModels('ProjectPhasePlan', 'ProjectTaskEmployeeRefer', 'ProjectFunctionEmployeeRefer', 'ProjectTeam', 'ProjectEmployeeProfitFunctionRefer', 'ProjectMilestone');
        if (!empty($this->data)) {
            $types = explode(',', $this->data['type']);
            //import normal task
            if( isset($this->data['Task']) ){
                if( in_array('Task', $types) ){
                    $this->save_multi_tasks($project_id, $this->data['Task']);
                }
            }
            //update task
            if( isset($this->data['Update']) ){
                if( in_array('Update', $types) ){
                    $this->save_multi_tasks($project_id, $this->data['Update']);
                }
            }
            //import milestone
            if( isset($this->data['Milestone']) ){
                if( in_array('Milestone', $types) ){
                    foreach($this->data['Milestone'] as $val){
                        //check if milestone exists
                        $stone = $this->ProjectMilestone->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'project_id' => $project_id,
                                'project_milestone' => $val['task_title']
                            )
                        ));
                        if( !empty($stone) ){
                            $this->ProjectMilestone->id = $stone['ProjectMilestone']['id'];
                            $this->ProjectMilestone->saveField('milestone_date', $val['task_start_date']);
                        } else {
                            $this->ProjectMilestone->create();
                            $this->ProjectMilestone->save(array(
                                'project_milestone' => $val['task_title'],
                                'project_id' => $project_id,
                                'milestone_date' => $val['task_start_date'],
                                //'validated' => 1
                            ));
                        }
                    }
                }
            }
            //import uncategorized tasks
            if( isset($this->data['SingleTask']) ){
                if( in_array('SingleTask', $types) ){
                    $this->save_multi_tasks($project_id, $this->data['SingleTask'], true);
                }
            }
            //sau do update progress cho phase plan
            if( isset($this->data['Phase']) ){
                foreach($this->data['Phase'] as $phase){
                    $plan_id = $this->ProjectPhasePlan->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_id' => $project_id,
                            'project_part_id' => $phase['part_id'] ? $phase['part_id'] : null,
                            'project_planed_phase_id' => $phase['phase_id']
                        )
                    ));
                    if( $plan_id ){
                        $this->ProjectPhasePlan->id = $plan_id['ProjectPhasePlan']['id'];
                        $this->ProjectPhasePlan->saveField('progress', $phase['progress']);
                        $planStart = $plan_id['ProjectPhasePlan']['phase_planed_start_date'];
                        $planEnd = $plan_id['ProjectPhasePlan']['phase_planed_end_date'];
                        if( !$planStart || $planStart == '0000-00-00'){
                            $this->ProjectPhasePlan->saveField('phase_planed_start_date', $phase['start']);
                        }
                        if( !$planEnd || $planEnd == '0000-00-00'){
                            $this->ProjectPhasePlan->saveField('phase_planed_end_date', $phase['end']);
                        }
                        if( !$plan_id['ProjectPhasePlan']['planed_duration'] ){
                            $duration = $this->getWorkingDays($phase['start'], $phase['end'], 0);
                            $this->ProjectPhasePlan->saveField('planed_duration', $duration);
                        }
                    }
                }
            }
            //update phase plan
            $this->_updateParentTask($project_id);
            $this->_saveStartEndDateAllTask($project_id);
        }
        $this->Session->setFlash(__('Saved', true), 'success');
        //chay staffing o day
        $this->_saveStaffingAfterImportTask(array($project_id), array('action' => 'index', $project_id));
        //end
    }

    private function _saveStaffingAfterImportTask($projects = array(), $url){
        $redirect = Router::url($url, true);
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '.$redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        $this->ProjectTask->recursive = 1;
        foreach($projects as $p){
            $this->ProjectTask->staffingSystem($p);
        }
    }

    function reset_date($project_id = null){
        $cid = $this->employee_info['Company']['id'];
        $project = $this->ProjectTask->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $cid,
                'category' => 2
            )
        ));
        if( !empty($project) ){
            $plans = $this->ProjectTask->ProjectPhasePlan->find('all', array(
                'fields' => array(
                    'id',
                    '(CASE WHEN phase_real_start_date = \'0000-00-00\' THEN phase_planed_start_date ELSE phase_real_start_date END) as start_date',
                    '(CASE WHEN phase_real_end_date = \'0000-00-00\' THEN phase_planed_end_date ELSE phase_real_end_date END) as end_date',
                ),
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id
                )
            ));
            foreach( $plans as $plan ){
                $workingdays = $this->getWorkingDays($plan[0]['start_date'], $plan[0]['end_date'], 0);
                $db = $this->ProjectTask->getDataSource();
                $this->ProjectTask->updateAll(
                    array(
                        'task_start_date' => $db->value($plan[0]['start_date'], 'string'),
                        'task_end_date' => $db->value($plan[0]['end_date'], 'string'),
                        'ProjectTask.duration' => $workingdays
                    ),
                    array(
                        'ProjectTask.project_planed_phase_id' => $plan['ProjectPhasePlan']['id']
                    )
                );
            }
            $this->Session->setFlash(__('Reset!', true), 'success');
            $this->redirect('/project_tasks/index/' . $project_id);
        }
        $this->redirect('/projects');
    }
    public function getNcTask(){
        App::import("vendor", "str_utility");
        $str = new str_utility();
        $result = array('result' => false, 'task' => array(), 'columns' => array(), 'data' => array(), 'consumeResult' => array(), 'employees_actif' => array());
        if( isset($this->data['id']) ){
            $id = $this->data['id'];
            $task = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id,
                    'is_nct' => 1
                )
            ));
            if( !empty($task) ){
                $result['task'] = $task['ProjectTask'];
                $result['task']['task_start_date'] = $str->convertToVNDate($result['task']['task_start_date']);
                $result['task']['task_end_date'] = $str->convertToVNDate($result['task']['task_end_date']);
                $this->loadModels('ActivityTask', 'ActivityRequest', 'NctWorkload', 'ProjectTaskEmployeeRefer');
				$listEmployee = $this->_getTeamEmployeesForLoad($task['ProjectTask']['project_id'], $task['ProjectTask']['id'], $task['ProjectTask']['task_start_date'],  $task['ProjectTask']['task_end_date']);
				$result['employees_actif'] = $listEmployee;
                $aTask = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id,
                        'is_nct' => 1
                    )
                ));
                if(!empty($aTask)){
                    $listRequestDates = $this->ActivityRequest->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'task_id' => $aTask['ActivityTask']['id'],
                            'value != 0'
                        ),
                        'fields' => array('id', 'date')
                    ));
                } else {
                    $listRequestDates = array();
                }
                $taskEmployeeRefers = $this->ProjectTaskEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $id),
                    'fields' => array('reference_id', 'is_profit_center')
                ));
                // xu ly nhung truong hop khong co assign ma co consume. code cui.
                if(!empty($taskEmployeeRefers) && !empty($listRequestDates)){
                    foreach ($listRequestDates as $_date) {
                        if($_date > strtotime($result['task']['task_end_date']) || $_date < strtotime($result['task']['task_start_date'])){
                            if($task['ProjectTask']['date_type'] == 2){
                                $obj = new DateTime();
                                $obj->setTimestamp($_date);
                                $obj->modify('last day of this month');
                                $eDate = $obj->getTimestamp();
                                $obj->modify('first day of this month');
                                $sDate = $obj->getTimestamp();
                                foreach ($taskEmployeeRefers as $key => $value) {
                                    $check = $this->NctWorkload->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array(
                                            'project_task_id' => $id,
                                            'reference_id' => $key,
                                            'is_profit_center' => $value,
                                            'task_date' => date('Y-m-d', $sDate),
                                            'end_date' => date('Y-m-d', $eDate),
                                            'activity_task_id' => $aTask['ActivityTask']['id'],
                                            'group_date' => '2_' . date('d-m-Y', $sDate) . '_' . date('d-m-Y', $eDate)
                                        )
                                    ));
                                    if(!empty($check)){
                                        //do nothing.
                                    }else{
                                        $this->NctWorkload->create();
                                        $this->NctWorkload->save(array(
                                            'project_task_id' => $id,
                                            'reference_id' => $key,
                                            'is_profit_center' => $value,
                                            'task_date' => date('Y-m-d', $sDate),
                                            'end_date' => date('Y-m-d', $eDate),
                                            'activity_task_id' => $aTask['ActivityTask']['id'],
                                            'group_date' => '2_' . date('d-m-Y', $sDate) . '_' . date('d-m-Y', $eDate),
                                            'estimated' => 0
                                        ));
                                    }
                                }
                            } elseif ($task['ProjectTask']['date_type'] == 1){
                                $obj = new DateTime();
                                $obj->setTimestamp($_date);
                                $obj->modify('monday this week');
                                $eDate = $obj->getTimestamp();
                                $obj->modify('sunday this week');
                                $sDate = $obj->getTimestamp();
                                foreach ($taskEmployeeRefers as $key => $value) {
                                    $check = $this->NctWorkload->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array(
                                            'project_task_id' => $id,
                                            'reference_id' => $key,
                                            'is_profit_center' => $value,
                                            'task_date' => date('Y-m-d', $sDate),
                                            'end_date' => date('Y-m-d', $eDate),
                                            'activity_task_id' => $aTask['ActivityTask']['id'],
                                            'group_date' => '1_' . date('d-m-Y', $sDate) . '_' . date('d-m-Y', $eDate)
                                        )
                                    ));
                                    if(!empty($check)){
                                        //do nothing.
                                    }else{
                                        $this->NctWorkload->create();
                                        $this->NctWorkload->save(array(
                                            'project_task_id' => $id,
                                            'reference_id' => $key,
                                            'is_profit_center' => $value,
                                            'task_date' => date('Y-m-d', $sDate),
                                            'end_date' => date('Y-m-d', $eDate),
                                            'activity_task_id' => $aTask['ActivityTask']['id'],
                                            'group_date' => '1_' . date('d-m-Y', $sDate) . '_' . date('d-m-Y', $eDate),
                                            'estimated' => 0
                                        ));
                                    }
                                }
                            } elseif ($task['ProjectTask']['date_type'] == 0){
                                foreach ($taskEmployeeRefers as $key => $value) {
                                    $check = $this->NctWorkload->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array(
                                            'project_task_id' => $id,
                                            'reference_id' => $key,
                                            'is_profit_center' => $value,
                                            'task_date' => date('Y-m-d', $_date),
                                            'end_date' => date('Y-m-d', $_date),
                                            'activity_task_id' => $aTask['ActivityTask']['id'],
                                            'group_date is NULL'
                                        )
                                    ));
                                    if(!empty($check)){
                                        //do nothing.
                                    }else{
                                        $this->NctWorkload->create();
                                        $this->NctWorkload->save(array(
                                            'project_task_id' => $id,
                                            'reference_id' => $key,
                                            'is_profit_center' => $value,
                                            'task_date' => date('Y-m-d', $_date),
                                            'end_date' => date('Y-m-d', $_date),
                                            'activity_task_id' => $aTask['ActivityTask']['id'],
                                            'estimated' => 0
                                        ));
                                    }
                                }
                            }
                        }
                    }
                }
                $this->NctWorkload->virtualFields['temp_id'] = 'CONCAT(reference_id, "-", is_profit_center)';
                $this->NctWorkload->virtualFields['workload'] = 'estimated';
                $this->NctWorkload->virtualFields['group_key'] = 'CASE WHEN group_date IS NULL OR group_date = "" THEN CONCAT("0_", task_date) ELSE group_date END';
                $finds = array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id
                    ),
                    'fields' => array('temp_id', 'id', 'group_date', 'group_key', 'reference_id', 'estimated', 'task_date', 'is_profit_center', 'project_task_id', 'activity_task_id'),
                    'order' => array('task_date')
                );
                if( $task['ProjectTask']['date_type'] != 0 ){
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
                            'Refer.company_id' => $this->employee_info['Company']['id']
                        ),
                        'joins' => array(
                            array(
                                'table' => 'company_employee_references',
                                'alias' => 'Refer',
                                'conditions' => array('Refer.employee_id = Employee.id')
                            )
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
                        if( $x[1] == 0 )$name = !empty($listResources[$x[0]]) ? $listResources[$x[0]] : '';
                        else $name = 'PC / ' . (!empty($listPcs[ $x[0] ]) ? $listPcs[ $x[0] ] : '');
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
                                    $array['consumed'] = $consumed;
                                    $array['inUsed'] = $inUsed;
                                    $array['id'] = $fs[$id]['id'];
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
    public function taskHasConsumed($task_id, $type = 'project'){
        if( $type == 'project' ){
            $this->loadModel('ActivityTask');
            $aTask = $this->ActivityTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $task_id,
                    'is_nct' => 1
                )
            ));
            if( !empty($aTask) )$task_id = $aTask['ActivityTask']['id'];
        }
        $this->loadModel('ActivityRequest');
        return $this->ActivityRequest->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $task_id,
                'value != 0'
            )
        ));
    }
    public function saveNcTask(){
        $result = array('result' => false, 'data' => array());
        if( !empty($this->data) ){
            $pid = $this->data['id'];
            $this->loadModels('NctWorkload', 'ActivityRequest');
            $this->NctWorkload->virtualFields['group_key'] = 'CONCAT(reference_id, "-", is_profit_center)';
            if($this->data['type'] != 0){
                $oldata = $this->NctWorkload->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $this->data['task']['id']),
                    'fields' => array('reference_id', 'estimated', 'is_profit_center', 'group_date', 'group_key'),
                    'order' => array('task_date')
                ));
                $oldata = !empty($oldata) ? Set::combine($oldata, '{n}.NctWorkload.group_key', '{n}.NctWorkload','{n}.NctWorkload.group_date') : '';
            }else{
                $oldata = $this->NctWorkload->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $this->data['task']['id']),
                    'fields' => array('reference_id', 'estimated', 'is_profit_center', 'task_date', 'group_key'),
                    'order' => array('task_date')
                ));
                $oldata = !empty($oldata) ? Set::combine($oldata, '{n}.NctWorkload.group_key', '{n}.NctWorkload','{n}.NctWorkload.task_date') : '';
            }
            $task = $this->data['task'];
            $task['is_nct'] = 1;
            $dateType = $task['date_type'] = $this->data['type'];
            $workloads = isset($this->data['workloads']) ? $this->data['workloads'] : array();
            $cid = $this->employee_info['Company']['id'];
            $project = $this->ProjectTask->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $cid,
                    'id' => $pid
                )
            ));
            if( !empty($project) ){
                App::import("vendor", "str_utility");
                $str = new str_utility();
                $task['task_start_date'] = $str->convertToSQLDate($task['task_start_date']);
                $task['task_end_date'] = $str->convertToSQLDate($task['task_end_date']);
                $task['duration'] = $this->getWorkingDays($task['task_start_date'], $task['task_end_date'], '');
                $check = false;

                $this->loadModels('ProjectTaskEmployeeRefer', 'NctWorkload');

                if( $task['id'] ){
                    $_task = $this->ProjectTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $task['id'],
                            'project_id' => $pid
                        )
                    ));
                    if( !empty($_task) ){
                        //disable update task name and phase id
                        //as of new requirement from 04/02/2016, task-have-consumed's name can be changed
                        // if( $this->taskHasConsumed($task['id']) )
                        //     unset($task['task_title']);
                        // unset($task['project_planed_phase_id']);
                        //update task
                        $this->ProjectTask->id = $task['id'];
                    } else {
                        goto _end_;
                    }
                } else {
                    $this->ProjectTask->create();
                    $count = $this->ProjectTask->find('first', array(
                        'recursive' => -1,
                        'fields' => array('weight'),
                        'order' => array('weight' => 'DESC'),
                        'limit' => 1,
                        'conditions' => array("ProjectTask.project_planed_phase_id" => $task['project_planed_phase_id'], "ProjectTask.project_id" => $pid)
                    ));
                    if( empty($count) )$count = -1;
                    else $count = $count['ProjectTask']['weight'];
                    $task['weight'] = $count+1;
                    if( !@$task['profile_id'] ){
                        $profile = ClassRegistry::init('ProjectPhasePlan')->find('first', array('recursive' => -1, 'conditions' => array('id' => $task['project_planed_phase_id'])));
                        if( $profile && $profile['ProjectPhasePlan']['profile_id'] ){
                            $task['profile_id'] = $profile['ProjectPhasePlan']['profile_id'];
                        }
                    }
                    $check = true;
                }
                $task['project_id'] = $pid;
                $this->ProjectTask->save($task);
                $task['id'] = $this->ProjectTask->id;

                $total = 0;
                $final = array();
                $notDelete = array();

                //delete old workload
                $this->NctWorkload->deleteAll(array(
                    'NctWorkload.project_task_id' => $task['id']
                ), false);
                //update new workloads
                $this->ProjectTask->Behaviors->attach('Lib');
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
                            'project_task_id' => $task['id'],
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
                $assignText = array();
                if( $final ){
                    $listId = array_keys($final);
                    //delete
                    //$this->ProjectTaskEmployeeRefer->virtualFields['combine'] = 'CONCAT(ProjectTaskEmployeeRefer.reference_id, "-", ProjectTaskEmployeeRefer.is_profit_center)';
                    $this->ProjectTaskEmployeeRefer->deleteAll(array(
                        'ProjectTaskEmployeeRefer.project_task_id' => $task['id'],
                        'NOT' => array('CONCAT(ProjectTaskEmployeeRefer.reference_id, "-", ProjectTaskEmployeeRefer.is_profit_center)' => $listId)
                    ), false);
                    //save
                    foreach($final as $id => $est){
                        $id = explode('-', $id);
                        $exist = $this->ProjectTaskEmployeeRefer->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'project_task_id' => $task['id'],
                                'reference_id' => $id[0],
                                'is_profit_center' => $id[1]
                            )
                        ));
                        if( !empty($exist) ){
                            $this->ProjectTaskEmployeeRefer->id = $exist['ProjectTaskEmployeeRefer']['id'];
                        } else {
                            $this->ProjectTaskEmployeeRefer->create();
                        }
                        $this->ProjectTaskEmployeeRefer->save(array(
                            'project_task_id' => $task['id'],
                            'reference_id' => $id[0],
                            'is_profit_center' => $id[1],
                            'estimated' => $est
                        ));
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
                $this->ProjectTask->saveField('estimated', $total);
                //post-save
                $project_task = $this->ProjectTask->read();

                $project_task['ProjectTask']['task_assign_to_text'] = implode(',', $assignText);

                $stt = $this->getStatus($task['task_status_id']);
                $pri = $this->getPriority($task['task_priority_id']);
                $mil = $this->getMilestone($task['milestone_id']);
                if( $stt )$project_task['ProjectTask']['task_status_text'] = $stt['ProjectStatus']['name'];
                else $project_task['ProjectTask']['task_status_text'] = '';
                if( $pri )$project_task['ProjectTask']['task_priority_text'] = $pri['ProjectPriority']['priority'];
                else $project_task['ProjectTask']['task_priority_text'] = '';
                if( $mil )$project_task['ProjectTask']['milestone_text'] = $mil['ProjectMilestone']['project_milestone'];
                else $project_task['ProjectTask']['milestone_text'] = '';
                $pro = $this->getProfile($task['profile_id']);
                if( $pro )$project_task['ProjectTask']['profile_text'] = $pro['Profile']['name'];
                else $project_task['ProjectTask']['profile_text'] = '';
                // save lai end date va start date cho project.
                $start_date_projects = strtotime($project['Project']['start_date']);
                $end_date_projects = strtotime($project['Project']['end_date']);
                $start_date_tasks = strtotime($this->data['task']['task_start_date']);
                $end_date_tasks = strtotime($this->data['task']['task_end_date']);
                $start_date_projects = ( ($start_date_projects <= $start_date_tasks) && ($start_date_projects != null) && ($start_date_projects != '0000-00-00') ) ? $start_date_projects : $start_date_tasks;
                $end_date_projects = ( ($end_date_projects >= $end_date_tasks) && ($end_date_projects != null) && ($end_date_projects != '0000-00-00')) ? $end_date_projects : $end_date_tasks;
                $_saved = array(
                    'start_date' => date('Y-m-d', $start_date_projects),
                    'end_date' => date('Y-m-d', $end_date_projects)
                );
                $this->loadModel('Project');
                $this->Project->id = $pid;
                $this->Project->save($_saved);

                $this->_syncPhasePlanTime($project_task);
                $this->_syncActivityTask($pid, $project_task, $task['id']);
                $this->_deleteCacheContextMenu();
                $result['data'] = $project_task['ProjectTask'];
                $result['result'] = true;
                if( $check) {
                    $employ = $this->employee_info['Employee']['fullname'];
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  __('Create', true) . ' ' . $project_task['ProjectTask']['task_title'];
                    $this->writeLog($result, $this->employee_info, $message);
                } else {
                    $employ = $this->employee_info['Employee']['fullname'];
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  __('Update', true) . ' ';
                    $list = array('task_title', 'task_priority_id', 'task_status_id', 'task_start_date', 'task_end_date');
                    foreach ($list as $key) {
                        if( isset($_task[$key]) && isset($this->data['task'][$key]) && ($this->data['task'][$key] != $_task[$key]) ){
                            switch ($key) {
                                case 'task_title':
                                    $message .= ' ' . __('Modify task name', true). ' ' . $this->data['task'][$key];
                                    break;
                                case 'task_priority_id':
                                    $message .= ' ' . __('Modify priority', true). ' ' . $pri;
                                    break;
                                case 'task_status_id':
                                    $message .= ' ' . __('Modify status', true). ' ' . $stt;
                                    break;
                                case 'task_start_date':
                                    $message .= ' ' . __('Modify start date', true). ' ' . $this->data['task'][$key];
                                    break;
                                case 'task_end_date':
                                    $message .= ' ' . __('Modify end date', true). ' ' . $this->data['task'][$key];
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                    $_messages = '';
                    $first = false;
                    $newdata = $this->data['workloads'];
                    $newAssign = $oldASsign = array();
                    foreach ($newdata as $key => $value) {
                        if($this->data['type'] != 0){
                            $_key = $this->data['type'] . '_' . $key;
                        } else {
                            $_key = $key;
                        }
                        if( isset($oldata[$_key])){
                            foreach ($oldata[$_key] as $k => $v) {
                                if(isset($value[$k])){
                                    if($value[$k]['estimated'] != $v['estimated']){
                                        $_messages .= ' ' . __('Workload', true). ' '. $value[$k]['estimated'].' '. __('To', true). ' ' . $v['estimated'];
                                    }
                                }
                            }
                        }
                        if(!$first){
                            $newAssign = $value;
                            $oldASsign = !empty($oldata[$_key]) ? $oldata[$_key] : array();
                        }
                        $first = true;
                    }
                    $this->loadModels('Employee', 'ProfitCenter');
                    $cId = $this->employee_info['Company']['id'];
                    $listEmployee = $this->Employee->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $cId),
                        'fields' => array('id', 'fullname')
                    ));
                    $listPc = $this->ProfitCenter->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $cId),
                        'fields' => array('id', 'name')
                    ));
                    foreach ($newAssign as $key => $value) {
                        if(!isset($oldASsign[$key])){
                            $_key = explode('-', $key);
                            if($_key[1] == 0){
                                $message .= ' ' . __('Affected to', true). ' ' . $listEmployee[$_key[0]];
                            } else {
                                $message .= ' ' . __('Affected to', true). ' ' . $listPc[$_key[0]];
                            }
                        }
                        unset($oldASsign[$key]);
                    }
                    if(!empty($oldASsign)){
                        foreach ($oldASsign as $key => $value) {
                            $_key = explode('-', $key);
                            if($_key[1] == 0){
                                $message .= ' ' . __('Remove', true). ' ' . $listEmployee[$_key[0]];
                            } else {
                                $message .= ' ' . __('Remove', true). ' ' . $listPc[$_key[0]];
                            }
                        }
                    }
                    $message .= $_messages;
                    $this->writeLog($result, $this->employee_info, $message);
                }
            }
            $this->ProjectTask->staffingSystem($pid);
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
    private function getMilestone($id){
        if( !$id )return 0;
        $this->loadModel('ProjectMilestone');
        return $this->ProjectMilestone->find('first', array(
            'recursive' => -1,
            'conditions' => array('ProjectMilestone.id' => $id)
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
    public function getHolidays($startYear = null, $endYear = null){
        if( !$startYear || !$endYear ){
            $startYear = date('Y') - 1;
            $endYear = date('Y') + 1;
        }
        $start = mktime(0, 0, 0, 1, 1, $startYear);
        $end = mktime(0, 0, 0, 12, 31, $endYear);
        $raw = ClassRegistry::init('Holiday')->getOptions($start, $end, $this->employee_info['Company']['id']);
        $result = array();
        foreach($raw as $date => $data){
            $year = date('Y', $date);
            $result[$year][date('d-m-Y', $date)] = $data;
        }
        if( !empty($this->params['requested']) )
            return $result;
        die(json_encode($result));
    }
    public function getWorkdays(){
        $raw = ClassRegistry::init('Workday')->getOptions($this->employee_info['Company']['id']);
        foreach($raw as $day => $value){
            $result[] = ceil($value);
        }
        $sunday = array_pop($result);
        array_unshift($result, $sunday);
        if( !empty($this->params['requested']) )
            return $result;
        die(json_encode($result));
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
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = '*';
                $this->MultiFileUpload->_properties['MaxSize'] = 100000 * 1024 * 1024;
                $file = $this->MultiFileUpload->upload();
                if( !empty($file) ){
                    //begin save field
                    $save = 'file:' . $file['attachment']['attachment'];
                    $this->ProjectTask->id = $this->data['Upload']['id'];
                    //indicate what kind of field: file or url
                    $this->ProjectTask->saveField('attachment', $save);
                    $result['status'] = true;
                    $result['attachment'] = $save;
                    $result['file'] = $file['attachment']['attachment'];
                    $result['sync'] = $this->MultiFileUpload->otherServer;
                }
            } else if( !empty($this->data['Upload']['url']) ){
                $save = 'url:' . $this->data['Upload']['url'];
                $this->ProjectTask->id = $this->data['Upload']['id'];
                //indicate what kind of field: file or url
                $this->ProjectTask->saveField('attachment', $save);
                $result['status'] = true;
                $result['attachment'] = $save;
            }
        }
        die(json_encode($result));
    }

    public function view_attachment($task_id){
        $p = $this->ProjectTask->read('attachment', $task_id);
        if( !empty($p['ProjectTask']['attachment']) ){
            $data = explode(':', $p['ProjectTask']['attachment']);
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
                header('location: ' . substr($p['ProjectTask']['attachment'], 4));
                die;
            }
        }
        die('File not found!');
    }
    protected  function _deleteFile($task_id){
        $p = $this->ProjectTask->read('attachment', $task_id);
        if( !empty($p['ProjectTask']['attachment']) ){
            $data = explode(':', $p['ProjectTask']['attachment']);
            $path = $this->_getPath($task_id);
            if( $data[0] == 'file'){
                $fileInfo = pathinfo($data[1]);
                @unlink($path . $fileInfo['basename']);
                if($this->MultiFileUpload->otherServer){
                    $this->MultiFileUpload->deleteFileToServerOther($path, $fileInfo['basename']);
                }
            }
        }
        $this->ProjectTask->saveField('attachment', null);
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
        $path = FILES . 'projects' . DS . 'project_tasks' . DS . $company . DS;
        return $path;
    }

    public function update_text(){
        $result = array();
        if( !empty($this->data['id']) ){
            $employee = $this->employee_info['Employee']['id'];
            if($this->employee_info['Employee']['company_id'] == null){
                $this->loadModel('Employee');
                $_idEm = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'fullname' => $this->employee_info['Employee']['fullname'],
                        'company_id' => null
                    ),
                    'fields' => array('id', 'fullname')
                ));
                $employee = !empty($_idEm['Employee']['id']) ? $_idEm['Employee']['id'] : 0;
            }
            $result['_idEm'] = $employee;
            $result['text_updater'] = $this->employee_info['Employee']['fullname'];
            $result['text_time'] = date('Y-m-d H:i:s');
            $result['comment'] = $this->data['text_1'];
            $this->ProjectTask->id = $this->data['id'];
			//Fix loi duplicate comment ticket #511, disable doan nay dong bo voi function ben project_tasks_controller
            // $this->ProjectTask->save(array(
                // 'text_1' => $this->data['text_1'],
                // 'text_updater' => $result['text_updater'],
                // 'text_time' => $result['text_time']
            // ));
            $this->loadModel('ProjectTaskTxt');
            $this->ProjectTaskTxt->create();
            $this->ProjectTaskTxt->save(array(
                'project_task_id' => $this->data['id'],
                'employee_id' => $employee,
                'comment' => $this->data['text_1'],
                'creadted' => $result['text_time']
            ));
        }
        die(json_encode($result));
    }

    public function saveName(){
        $result = array();
        if( !empty($this->data['id']) ){
            $this->ProjectTask->save(array(
                'id' => $this->data['id'],
                'task_title' => $this->data['task_title']
            ));
            //sync activity task
            $tasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'id' => $this->data['id'],
                        'parent_id' => $this->data['id']
                    )
                )
            ));
            foreach($tasks as $task){
                if( $task['ProjectTask']['id'] == $this->data['id']){
                    $result = $task['ProjectTask'];
                }
                //need to replace this code below for better performance
                $this->_syncActivityTask($task['ProjectTask']['project_id'], $task, $task['ProjectTask']['id']);
            }
        }
        die(json_encode($result));
    }
    private function getWeight($project, $phase, $parent = 0){
        $count = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'fields' => array('weight'),
            'order' => array('weight' => 'DESC'),
            'limit' => 1,
            'conditions' => array("ProjectTask.project_planed_phase_id" => $phase, "ProjectTask.project_id" => $project, 'parent_id' => $parent)
        ));
        if( empty($count) )$count = -1;
        else $count = $count['ProjectTask']['weight'];
        return ++$count;
    }
    public function getCommentTxt(){
        $this->layout = false;
        $results = array();
        if(!empty($_POST)){
            $pTaskId = $_POST['pTaskId'];
            $results['id'] = $pTaskId;

            $task = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $pTaskId),
                'fields' => array('task_title'),
            ));
            $results['task_title'] = $task['ProjectTask']['task_title'];
            $this->loadModel('ProjectTaskTxt');
            $result = $this->ProjectTaskTxt->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $pTaskId),
                'fields' => array('id', 'employee_id', 'comment', 'created')
            ));
			
			$results['old_comment'] = '';
            $results['result'] = $result;
        }
		if( !empty($results['result']) ) $this->update_read_status($pTaskId);
        die(json_encode($results));
    }
	
	/* 
	* Function Update read status for task comment
	* @param: Project task ID
	*/
	public function update_read_status($pTaskId = null){
		if(!$pTaskId){
			$this->cakeError('error404');
		}
		$ProjectTask =  $this->ProjectTask->find('first', array(
			'conditions' => array(
				'ID' => $pTaskId,
			),
			'recursive' => -1,
		));
		$project_id = $ProjectTask['ProjectTask']['project_id'];
		$this->_checkRole(false, $project_id);
		// $employId = $this->Session->read('Auth.Employee.id');
		$isAjax = $this->params['isAjax'];
		$result = false;
		$data = '';
		if( $ProjectTask ){
			$this->loadModel('ProjectTaskTxtRefer');
			$data = $this->ProjectTaskTxtRefer->find('first', array(
				'conditions' => array(
				'task_id' => $pTaskId,
				'employee_id' => $this->employee_info['Employee']['id'] // wrong
				// 'employee_id' => $employId
				),
			));
			if( empty( $data)){
				$data = $this->ProjectTaskTxtRefer->create();
				$data['ProjectTaskTxtRefer']['task_id'] = $pTaskId;
				$data['ProjectTaskTxtRefer']['employee_id'] = $this->employee_info['Employee']['id'];
				// $data['ProjectTaskTxtRefer']['employee_id'] = $employId;
			}
			$data['ProjectTaskTxtRefer']['read_status'] = 1;
			$data = $this->ProjectTaskTxtRefer->save($data);
			if( $data) $result = true;
		}
		return;
		// if( $isAjax ){
			// die(json_encode(array(
				// 'result' => $result,
				// 'data' => $data,
			// )));
		// }
		// $this->Session->setFlash(__('Saved', true), 'success');
		// $this->redirect( array('action' => 'index',$project_id));		
	}
    public function getClassifyTask($task_id = ""){
        //check have day, week, month for skip.
        $this->loadModels('NctWorkload', 'Project');

        $start_date = $this->Project->find('first', array(
            'conditions' => array(
                'id' => $task_id,
            ),
            'recursive' => -1,
            'fields' => array('start_date'),
        ));
        $start_date = !empty($start_date) ? $start_date['Project']['start_date'] : '';
        if(!empty($_POST) && $_POST['type'] == 'task'){

            $task = $this->ProjectTask->find('first', array(
                'conditions' => array(
                    'id' => $task_id,
                ),
                'recursive' => -1,
                'fields' => array('id', 'is_nct')
            ));
            $classifyTask = array(
                'day' => 0,
                'week' => 0,
                'month' => 0
            );

            $classifyTask['start_date'] =  $start_date;
            if(!empty($task) && $task['ProjectTask']['is_nct'] != 1){
                $classifyTask['day'] = 1;
            } else {
                $classifyTask['week'] = $this->NctWorkload->find('count', array(
                'conditions' => array(
                    'project_task_id' => $task_id,
                    'group_date LIKE' => '1_%'
                ),
                'recursive' => -1,
                ));
                $classifyTask['month'] = $this->NctWorkload->find('count', array(
                    'conditions' => array(
                        'project_task_id' => $task_id,
                        'group_date LIKE' => '2_%'
                    ),
                    'recursive' => -1,
                ));
                $classifyTask['day'] += $this->NctWorkload->find('count', array(
                'conditions' => array(
                    'project_task_id' => $task_id,
                    'group_date is NULL',
                ),
                'recursive' => -1,
                ));
            }
            echo json_encode($classifyTask);
            exit;
        } else {
            $listTaskSkip = $this->ProjectTask->find('list', array(
                'conditions' => array(
                    'project_id' => $task_id,
                ),
                'recursive' => -1,
                'fields' => array('id', 'id')
            ));
            $classifyTask = array();
            $classifyTask['start_date'] =  $start_date;
            $classifyTask['day'] = $this->ProjectTask->find('count', array(
                'conditions' => array(
                    'project_id' => $task_id,
                    'is_nct' => 0
                ),
                'recursive' => -1,
            ));
            // nct task not assign to
            $tmpclassifyTask = 0;
            $tmpclassifyTask = $this->ProjectTask->find('count', array(
                'conditions' => array(
                    'project_id' => $task_id,
                    'is_nct' => 1
                ),
                'recursive' => -1,
            ));

            $classifyTask['week'] = $this->NctWorkload->find('count', array(
                'conditions' => array(
                    'project_task_id' => $listTaskSkip,
                    'group_date LIKE' => '1_%'
                ),
                'recursive' => -1,
            ));
            $classifyTask['month'] = $this->NctWorkload->find('count', array(
                'conditions' => array(
                    'project_task_id' => $listTaskSkip,
                    'group_date LIKE' => '2_%'
                ),
                'recursive' => -1,
            ));
            $classifyTask['day'] += $this->NctWorkload->find('count', array(
                'conditions' => array(
                    'project_task_id' => $listTaskSkip,
                    'group_date is NULL',
                ),
                'recursive' => -1,
            ));

            // set default case NCT task without assign to
            if($classifyTask['week'] == 0 && $classifyTask['month'] == 0 && $classifyTask['day'] == 0) $classifyTask['month'] = $tmpclassifyTask;
            echo json_encode($classifyTask);
            exit;
        }
    }
    private function _durationDiffEndDate($startDate, $endDate, $duration){
        $_durationEndDate = '';
        if((strtotime($endDate) == '' || $endDate == '0000-00-00') && !empty($duration)){
            $dates_ranges[]= $startDate;
            $_startDate = strtotime($startDate);
            $s = $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+$duration, date("Y", $_startDate));
            $startDateCheck = strtotime($startDate);
            $_diffDates = 0;
            while($_startDate <= $startDateCheck){
                $dates_range = $_startDate;
                $_dateFitter = strtolower(date("l", $dates_range));
                if($_dateFitter == 'saturday' || $_dateFitter == 'sunday'){
                    $_diffDates++;
                }
                $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+1, date("Y", $_startDate));
            }
            for($i = 0; $i < $_diffDates; $i++){
                $s = mktime(0, 0, 0, date("m", $s), date("d", $s)-1, date("Y", $s));
                $_Fitter = strtolower(date("l", $s));
                if($_Fitter == 'saturday' || $_Fitter == 'sunday'){
                    $_diffDates++;
                }
            }
            $_end = date("Y-m-d", $s);
            $_durationEndDate = $_end;
        } else {
            $_durationEndDate = $endDate;
        }
        return $_durationEndDate;
    }


    /**
     * view
     *
     * @return void
     * @access public
     */
    public function checkAvatar() {
        $this->loadModel('Employee');
        $checkAvata = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'avatar_resize')
        ));

        $checkAvata = Set::combine($checkAvata, '{n}.Employee.id', '{n}.Employee.avatar_resize');
        return $checkAvata;
    }

    /**
     * phase_vision
     * Phase project list
     *
     * @return void
     * @access public
     */
    function phase_vision($project_id = null, $new_gantt = null) {
        //$this->render('ajax');
        $this->loadModel('ProjectPhasePlan');

        $phase = isset($this->params['url']['phase']) ? $this->params['url']['phase'] : null;
        if($phase) {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id,'ProjectPhasePlan.id' => $phase);
            $condPhaseProject = array('Project.id' => $project_id, 'Project.project_phase_id' => $phase);
        } else {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id);
            $condPhaseProject = array('Project.id' => $project_id);
        }
        $this->_checkRole(false, $project_id);
        $projectPlanName = $this->viewVars['projectName'];
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $this->set('projectPlanName', $this->ProjectPhasePlan->Project->find("first", array(
                    'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'part_id', 'order' => 'milestone_date ASC', 'conditions' => array('ProjectMilestone.milestone_date !=' => '0000-00-00'))),
                    "fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'), 'conditions' => $condPhaseProject
                    )));
        $options = array(
            'limit' => 2000,
            'contain' => array(
                'ProjectPhase' => array('name', 'color')
            ),
            'conditions' => $condPhase,
            'order' => array('weight')
        );
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $phasePlans = $this->ProjectPhasePlan->find('all', $options);
        $phaseIds = Set::classicExtract($phasePlans, '{n}.ProjectPhasePlan.id');
        $parts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
			'conditions' => array('project_id' => $project_id),
			'order' => array('weight' => 'ASC')
		));
        $getDatas = $this->_taskCaculates($project_id, $projectPlanName['Project']['company_id'], $phasePlans);
        $taskCompleted = $getDatas['part'];
        $phaseCompleted = $getDatas['phase'];
        $projectTasks = $getDatas['task'];
        $onClickPhaseIds = array();
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
        $callProjects = isset($this->params['url']['call']) ? (int) $this->params['url']['call'] : 0;
        // save History.
        $this->loadModel('HistoryFilter');
        $default = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'path' => 'project_tasks_preview',
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('id', 'params')
        ));
        $type = $this->handleFilterGantt();
        $this->set(compact('phasePlans', 'display', 'project_id', 'parts', 'phaseCompleted', 'projectTasks', 'taskCompleted', 'onClickPhaseIds', 'callProjects', 'type'));
		
		/* For add popup */
		$listPhases = array();
		$phases = array();
		foreach ($phasePlans as $Phase) {
			$part_id = $Phase['ProjectPhasePlan']['project_part_id'];
			if( !empty($part_id) && !empty($parts[$part_id ]) ){
				$part_name = $parts[$Phase['ProjectPhasePlan']['project_part_id']];
				$Phase['ProjectPhase']['name'] = $Phase['ProjectPhase']['name'] . ' (' . $part_name . ')';
			}
			$listPhases[$Phase['ProjectPhasePlan']['id']] = array_merge($Phase['ProjectPhasePlan'], $Phase['ProjectPhase']);
		}
		$phases = !empty($listPhases) ? Set::combine($listPhases, '{n}.id', '{n}.name') : array();
		$this->set(compact('listPhases', 'phases'));
		/* END For add popup */

        // if(isset($this->params['url']['ajax']) && $this->params['url']['ajax'] == 1) {
        //     $this->render('phase_vision_ajax');
        // }
        // if( $new_gantt )$this->render('phase_vision_new');
    }
    private function _taskCaculates($project_id = null, $company_id = null, $phasePlans = array()){
        $manual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : false;
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ProjectStatus');

        $projectTasks = $this->ProjectTask->find('all', array(
            //'recursive' => -1,
            'conditions' => array('ProjectTask.project_id' => $project_id),
            'fields' => array('id', 'task_title', 'parent_id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'estimated', 'predecessor', 'special', 'special_consumed', 'manual_consumed', 'overload', 'manual_overload', 'task_status_id', 'initial_task_end_date', 'initial_task_start_date'),
            'order' => array('ProjectTask.weight' => 'ASC')
        ));
        $projectStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
			'order' => 'weight',
            'fields' => array('id', 'status'),

        ));
        $taskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');

        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $taskIds),
            'fields' => array('project_task_id', 'id')
        ));

        $activityRequests = $this->ActivityRequest->find('all', array(
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
            $dx = $projectTask['ProjectTask'];
            //manual consumed on 2015-08-05
            if( $manual ){
                $projectTasks[$key]['ProjectTask']['consumed'] = $projectTasks[$key]['ProjectTask']['manual_consumed'];
                $projectTasks[$key]['ProjectTask']['overload'] = $overload = $projectTasks[$key]['ProjectTask']['manual_overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
                    $projectTasks[$key]['ProjectTask']['completed'] = round($projectTasks[$key]['ProjectTask']['consumed'] * 100 / ($overload + $dx['estimated']), 2);
                }
            }
            else if(!empty($dx['special'])){
                $overload = $projectTasks[$key]['ProjectTask']['overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
                    $projectTasks[$key]['ProjectTask']['completed'] = round($dx['special_consumed'] * 100 / ($overload + $dx['estimated']), 2);
                }
                $projectTasks[$key]['ProjectTask']['consumed'] = $dx['special_consumed'];
            } else if(!empty($consumeds[$dx['id']])){
                $overload = $projectTasks[$key]['ProjectTask']['overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
                    $projectTasks[$key]['ProjectTask']['completed'] = round($consumeds[$dx['id']] * 100 / ($overload + $dx['estimated']), 2);
                }
                $projectTasks[$key]['ProjectTask']['consumed'] = $consumeds[$dx['id']];
            }
            if( !$dx['parent_id'] )$projectTasks[$key]['ProjectTask']['parent'] = true;
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
                            $dataParents[$parentId]['overload'][] = isset($projectTask['ProjectTask']['overload']) ? $projectTask['ProjectTask']['overload'] : 0;
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
                    $dataParents[$id]['overload'] = isset($dataParent['overload']) ? array_sum($dataParent['overload']) : 0;
                    if($_estimated == 0){
                        $dataParents[$id]['completed'] = 0;
                    } else{
                        $dataParents[$id]['completed'] = round($_consumed * 100 / ($dataParents[$id]['overload'] + $_estimated), 2);
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
                            $projectTasks[$key]['ProjectTask']['overload'] = $dataParent['overload'];
                            //$projectTasks[$key]['ProjectTask']['parent'] = true;
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
            $datas[$key]['rstart'] = isset($projectTask['ProjectTask']['task_start_date']) ? strtotime($projectTask['ProjectTask']['task_start_date']) : 0;
            $datas[$key]['rend'] = isset($projectTask['ProjectTask']['task_end_date']) ? strtotime($projectTask['ProjectTask']['task_end_date']) : 0;
            $datas[$key]['start'] = isset($projectTask['ProjectTask']['initial_task_start_date']) ? strtotime($projectTask['ProjectTask']['initial_task_start_date']) : 0;
            $datas[$key]['end'] = isset($projectTask['ProjectTask']['initial_task_end_date']) ? strtotime($projectTask['ProjectTask']['initial_task_end_date']) : 0;
            $datas[$key]['assign'] = !empty($projectTask['ProjectTask']['assign']) ? implode(', ', $projectTask['ProjectTask']['assign']) : '';
            $datas[$key]['task_status'] = !empty($projectStatus[$projectTask['ProjectTask']['task_status_id']]) ? $projectStatus[$projectTask['ProjectTask']['task_status_id']] : '';
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
        //$phases = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_planed_phase_id'));
        $this->loadModel('ProjectPhasePlan');
        $phases = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'id')
        ));
        $_phases = array();
        foreach($phases as $phase){
            foreach($projectTasks as $projectTask){
                if($phase == $projectTask['ProjectTask']['project_planed_phase_id'] && isset($projectTask['ProjectTask']['parent']) ){
                    $_phases[$phase]['estimated'][] = $projectTask['ProjectTask']['estimated'];
                    $_phases[$phase]['consumed'][] = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
                    $_phases[$phase]['overload'][] = isset($projectTask['ProjectTask']['overload']) ? $projectTask['ProjectTask']['overload'] : 0;
                }
            }
            $_phases[$phase]['completed'] = 0;
        }
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ?  true : false;
        $phaseProgress = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.progress') : array();
        $partPhases = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $groupPhaseFollowPart = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $progressPart = array();
        foreach($_phases as $id => $_phase){
            $_estimated = !empty($_phase['estimated']) ? array_sum($_phase['estimated']) : 0;
            $_consumed = !empty($_phase['consumed']) ? array_sum($_phase['consumed']) : 0;
            $overload = !empty($_phase['overload']) ? array_sum($_phase['overload']) : 0;
            $_phases[$id]['estimated'] = $_estimated;
            $_phases[$id]['consumed'] = $_consumed;
            $_phases[$id]['overload'] = $overload;
            if($_estimated == 0){
                $_phases[$id]['completed'] = 0;
            } else{
                $_phases[$id]['completed'] = round($_consumed * 100 / ($overload + $_estimated), 2);
            }
            if($manuallyAchievement){
                $_phases[$id]['completed'] = 0;
                if(!empty($phaseProgress) && !empty($phaseProgress[$id])){
                    $_phases[$id]['completed'] = $phaseProgress[$id];
                }
                $partId = !empty($partPhases[$id]) ? $partPhases[$id] : 0;
                if(!isset($progressPart[$partId])){
                    $progressPart[$partId] = 0;
                }
                $progressPart[$partId] += $_phases[$id]['completed'];
            }
        }
        // caculate completed of part
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
                        $parts[$phasePlan]['overload'][] = $_phase['overload'];
                    }
                }
            }
        }
        foreach($parts as $id => $part){
            $_estimated = array_sum($part['estimated']);
            $_consumed = array_sum($part['consumed']);
            $_overload = array_sum($part['overload']);
            $parts[$id]['estimated'] = $_estimated;
            $parts[$id]['consumed'] = $_consumed;
            $parts[$id]['overload'] = $_overload;
            if($_estimated == 0){
                $parts[$id]['completed'] = 0;
            } else{
                $parts[$id]['completed'] = round($_consumed * 100 / ($_overload + $_estimated), 2);
            }
            if($manuallyAchievement){
                $parts[$id]['completed'] = 0;
                if(!empty($progressPart) && !empty($progressPart[$id])){
                    $totalPhaseDependPart = !empty($groupPhaseFollowPart) && !empty($groupPhaseFollowPart[$id]) ? count($groupPhaseFollowPart[$id]) : 0;
                    $parts[$id]['completed'] = ($totalPhaseDependPart == 0) ? 0 : round($progressPart[$id]/$totalPhaseDependPart, 2);
                }
            }
        }
        $results['task'] = $datas;
        $results['phase'] = $_phases;
        $results['part'] = $parts;
        return $results;
    }
    function add_new_task_popup($project_id = null , $ajax = false){
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ProjectPhasePlan');
		$success = false;
		$result = '';
		// _checkRole
		$data = '';
		// debug($_POST['data']);
		// exit;
        if(!empty($_POST['data'])){
            $data = $_POST['data'];
            if( !empty( $project_id ) ) $data['ProjectTask']['project_id'] = $project_id;
			$project_id = $data['ProjectTask']['project_id'];
			$data["ProjectTask"]['task_start_date'] = !empty($data["ProjectTask"]['task_start_date']) ? date('Y-m-d', strtotime($data["ProjectTask"]['task_start_date'] )) : '0000-00-00';
			$data["ProjectTask"]['task_end_date'] = !empty($data["ProjectTask"]['task_end_date']) ? date('Y-m-d', strtotime($data["ProjectTask"]['task_end_date'] )) : '0000-00-00';
            $success = true;
            /**
             * Xu ly read assign resource
             */
			$list_empl_assign = array();
			$estimated = 0;
			if( !empty( $data['workloads'])){
				foreach( $data['workloads'] as $emp => $es){
					$val = explode('-',$emp.'-0');
					$list_empl_assign[] = array(
						'reference_id' => $val[0],
						'is_profit_center' => $val[1],
						'estimated' => $es['estimated']
					);
					$estimated +=  $es['estimated'];
				}				
			}elseif (!empty($data['task_assign_to_id'])) {
				foreach($data['task_assign_to_id'] as $key => $val){
					if(!empty($val)){
						$val = explode('-',$val.'-0');
						$list_empl_assign[] = array(
							'reference_id' => $val[0],
							'is_profit_center' => $val[1],
							'estimated' => 0
						);
					}
				}
			}
			$data['ProjectTask']['estimated'] = $estimated;
            unset($data['task_assign_to_id']);
            unset($data['workloads']);
            // default fields task

            $data["ProjectTask"]['parent_id'] = 0;
            $data["ProjectTask"]['duration'] = '';
            $data["ProjectTask"]['predecessor'] = '';

            //Default value of start date and date of task = start date and end date of phase
            $dateTmp = $this->ProjectPhasePlan->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $data["ProjectTask"]['project_planed_phase_id']
                ),
                'fields'=>array('phase_real_start_date','phase_real_end_date')
            ));
            $startDateTmp = $dateTmp['ProjectPhasePlan']['phase_real_start_date'];
            $endDateTmp = $dateTmp['ProjectPhasePlan']['phase_real_end_date'];
            $conditions = array(
                "ProjectTask.task_title" => $data["ProjectTask"]['task_title'],
                "ProjectTask.parent_id" => $data["ProjectTask"]['parent_id'],
                "ProjectTask.project_id" => $data["ProjectTask"]['project_id']
            );
			if(!empty($data["ProjectTask"]['id'])){
				 $conditions["ProjectTask.id !="] = $data["ProjectTask"]['id'];
			}
			
            if(!empty($data["ProjectTask"]['project_planed_phase_id'])) $conditions['ProjectTask.project_planed_phase_id'] = $data["ProjectTask"]['project_planed_phase_id'];
            $flag = $this->ProjectTask->find('count', array(
                'recursive' => -1,
                'conditions' => $conditions,
            ));
            if($flag > 0){
                $success = false;
                $result = __('The task name already exists', true);
                $this->Session->setFlash(__('The task name already exists', true), 'error');
                $data = null;
            } else {
              
                if ( !$this->_hasStatus($data['ProjectTask']['task_status_id']) ) {
                    $status = $this->_projectStatus($project_id, true);
                    $data["ProjectTask"]['task_status_id'] = $status['id'];
                    $data["ProjectTask"]['task_status_name'] = $status['name'];
                }
                $_predecessor   = $data["ProjectTask"]['predecessor'];
                $_startDate     = ($data["ProjectTask"]['task_start_date']=="") ? $startDateTmp : $data["ProjectTask"]['task_start_date'];
                $_endDate       = ($data["ProjectTask"]['task_end_date']=="") ? $endDateTmp : $data["ProjectTask"]['task_end_date'];
                $_duration      = $data["ProjectTask"]['duration'];
                if (isset($_predecessor) && $_predecessor != 0) {
                    $_startDate = $this->_createPredecessor($_startDate, $_predecessor);
                }
                if (isset($_duration) && $_duration != 0) {
                    if (isset($_startDate)) { // duration isset and start day is set
                        if (isset($_endDate)) { // duration isset and start day is set and enddate is set
                            $_duration = null;
                            $_duration = $this->getWorkingDays($_startDate, $_endDate, $_duration);
                        } else { // duration is set and start day is set and end date is not set
                            $_endDate = null;
                            $_endDate = $this->_durationEndDate($_startDate, $_endDate, $_duration);
                        }
                    } else { // duration isset and start day is not set
                        if (isset($_endDate)) { // duration isset and start day not set and enddate is set
                            $_startDate = null;
                            $_startDate = $this->_durationStartDate($_startDate, $_endDate, $_duration);
                        } else { // duration is set and start day is not set and end date is not set
                            // do nothing
                            $data["ProjectTask"]['duration'] = null;
                        }
                    }
                } else {
                    // duration is not set
                    if (isset($_startDate)) {
                        if (isset($_endDate)) {
                            $_duration = null;
                            $_duration = $this->getWorkingDays($_startDate, $_endDate, $_duration);
                        } else {
                            // do nothing
                        }
                    } else {
                        if (isset($_endDate)) {
                            // do nothing
                        } else {
                            // do nothing
                        }
                    }
                }
                if (strtotime($_startDate) > strtotime($_endDate)) {
                    $_endDate = $_startDate;
                }

                $data["ProjectTask"]['task_start_date'] = $_startDate;
                $data["ProjectTask"]['task_end_date'] = $_endDate;
                $data["ProjectTask"]['duration'] = $_duration;
                $data["ProjectTask"]['task_completed'] = 0;
                $data["ProjectTask"]['parentId'] = 0;
                if(!empty($data["ProjectTask"]['project_planed_phase_id'])){
                    $data["ProjectTask"]['weight'] = $this->getWeight($project_id, $data["ProjectTask"]['project_planed_phase_id'], $data["ProjectTask"]['parent_id']);

                }
				
                //END
                $tmp_task_assign_to_id = $is_profit_center = array();
                unset($data["ProjectTask"]['is_profit_center']);
                // unset($data["ProjectTask"]['id']);
                unset($data['ProjectTask']['id_refer_flag']);
                $data["ProjectTask"]['allow_request'] = isset($remain[0]) ? $remain[0] : 0;
				if(!empty($data["ProjectTask"]['id'])){
					$this->ProjectTask->id = $data["ProjectTask"]['id'];
				}else{
					$this->ProjectTask->create();
				}

                $result = $this->ProjectTask->save($data);
				$result['ProjectTask']['workload'] = $result['ProjectTask']['estimated'];
                unset($data["ProjectTask"]['task_assign_to_text']);
                unset($data["ProjectTask"]['task_assign_to']);
                $data['id'] = $this->ProjectTask->id;
				$taskID = $data['id'];
				$this->loadModel('ProjectTaskEmployeeRefer');
				$last_asigns = $this->ProjectTaskEmployeeRefer->find('all', array(
					'recursive' => -1,
					'conditions' => array('ProjectTaskEmployeeRefer.project_task_id' => $taskID),
					'fields' => array('*')
				));
				$empl_assign_data = array();
				if (!empty($list_empl_assign)){
					if (!empty($last_asigns)) {
						foreach ($last_asigns as $last_asign) {
							$delete = 1;
							foreach($list_empl_assign as $key => $empl){
								if( $empl['reference_id'] == $last_asign['ProjectTaskEmployeeRefer']['reference_id'] && $empl['is_profit_center'] == $last_asign['ProjectTaskEmployeeRefer']['is_profit_center'])  {
									$delete = 0;
									$list_empl_assign[$key]['id'] =  $last_asign['ProjectTaskEmployeeRefer']['id'];			
								}
								
							}
							if($delete){
								$this->ProjectTaskEmployeeRefer->id = $last_asign['ProjectTaskEmployeeRefer']['id'];
								$this->ProjectTaskEmployeeRefer->delete();
							}
						}
					}
					// debug( $list_empl_assign);exit;
					foreach ($list_empl_assign as $employee) {
						if(empty($employee['id'])) $this->ProjectTaskEmployeeRefer->create();
						else $this->ProjectTaskEmployeeRefer->id = $employee['id'];
						$refer_data = array(
							'reference_id' => $employee['reference_id'],
							'is_profit_center' => $employee['is_profit_center'],
							'project_task_id' => $taskID,
							'updated' => time(),
							'estimated' => !empty( $employee['estimated']) ? $employee['estimated'] : 0,
						);
						$tmp_task_assign_to_id[] = $employee['reference_id'];
						$is_profit_center[] = $employee['is_profit_center'];
						$empl_assign_data[] = $this->ProjectTaskEmployeeRefer->save($refer_data);
					}
					$this->updateEmployeeAssignedToTeam($project_id, $list_empl_assign);
				} else {
					if (!empty($last_asigns)) {
						foreach ($last_asigns as $last_asign) {
							$this->ProjectTaskEmployeeRefer->id = $last_asign['ProjectTaskEmployeeRefer']['id'];
							$this->ProjectTaskEmployeeRefer->delete();
						}
					}
				}
                if ($result) {
                    // fix bug array of ids in combobox in view
                    $result['ProjectTask']['task_assign_to_id'] = $tmp_task_assign_to_id;
                    $result['ProjectTask']['is_profit_center'] = $is_profit_center;
                    $result['ProjectTask']['id'] = $data['id'];
                    if( isset($pname) )$result['ProjectTask']['profile_text'] = $pname['Profile']['name'];
                    else $result['ProjectTask']['profile_text'] = '';
                    $this->_updateParentTask($data["ProjectTask"]['project_id']);
                    if(!empty($data["ProjectTask"]['project_planed_phase_id'])){
                        $this->_syncPhasePlanTime($result);
                    }
                    $this->_saveStartEndDateAllTask($data["ProjectTask"]['project_id'], $data['id']);
                    $this->ProjectTask->staffingSystem($project_id);
                    $this->_deleteCacheContextMenu();
                    $project_task_id = $this->ProjectTask->getLastInsertID();
                    $this->_syncActivityTask($project_id, $result, $project_task_id);
                    //Tracking action
                    $projectName = $this->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id)));
                    $employ = $this->employee_info['Employee']['fullname'];
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  sprintf('Create task `%s` under `%s`', $result['ProjectTask']['task_title'], $projectName['Project']['project_name']);
                    $this->writeLog($result, $this->employee_info, $message);
					$this->notifyForNewTask($project_id, $project_task_id, $result['ProjectTask']['task_title']); // Notify to users.
                }
            }

        }
		$att_info = array();
		if( !empty( $data['id']) && !empty($_FILES)) {
			$task_id = $data['id'];
			$this->loadModel('ProjectTaskAttachment');
			$_FILES['FileField'] = array();
            if(!empty($_FILES)){
                $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            }
            unset($_FILES['file']);
            if(!empty($_FILES['FileField'])){
                if (!empty($_FILES['FileField']['name']['attachment'])) {
                    App::import('Core', 'Folder');
                    $path = $this->_getPath($task_id);
                    new Folder($path, true, 0777);
                    $this->MultiFileUpload->encode_filename = false;
                    $this->MultiFileUpload->uploadpath = $path;
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = '*';
                    $this->MultiFileUpload->_properties['MaxSize'] = 100000 * 1024 * 1024;
                    $file = $this->MultiFileUpload->upload();
                    if( !empty($file) ){
                        //begin save field
                        $att_data = array(
                            'project_id'=> $project_id,
                            'task_id' => $task_id,
                            'attachment' =>  $file['attachment']['attachment'],
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'size' => $file['attachment']['size'],
                            'is_https' => 0,
                        );
                        $this->ProjectTaskAttachment->create();
                        $att_info = $this->ProjectTaskAttachment->save($att_data);
                       
                    }
                }
            }
			
			// Update read status
			if( !empty( $att_info) ){
				// set unread for other employee
				$this->requestAction( '/kanban/task_attachment_unread/'.$task_id);
			}
		}
        if($ajax || $this->params['isAjax']){
			if(!empty($result) && ( $flag == 0 ) ){
				
				$taskId = $data['id'];
				$task = $this->ProjectTask->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $taskId,
					),
					'fields' => array('id','project_id', 'task_status_id', 'task_title', 'updated', 'task_start_date', 'task_end_date', 'estimated', 'special', 'special_consumed', 'attachment', 'text_1', 'text_updater', 'text_time'),
				));
				$this->loadModels('ProjectTaskAttachments', 'ProjectTaskAttachmentView', 'ProjectTaskTxt', 'ProjectTaskTxtRefer');
				// attachment
				$projectTaskAttachments = $this->ProjectTaskAttachments->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'task_id' => $taskId,
						'attachment !=' => null
					),
					'fields' => array('task_id')
				));
				$projectTaskAttachmentRead = $this->ProjectTaskAttachmentView->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'task_id' => $taskId,
							'employee_id' => $this->employee_info['Employee']['id']
						),
					'fields' => array('task_id', 'read_status')
				));
				//comment
				$projectTaskComments = $this->ProjectTaskTxt->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $taskId,
						'comment !=' => null
					),
					'fields' => array('id', 'project_task_id')
				));
				$projectTaskCommentRead = $this->ProjectTaskTxtRefer->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'task_id' => $taskId,
							'employee_id' => $this->employee_info['Employee']['id']
						),
					'fields' => array('task_id', 'read_status')
				));
				
				// attachment
				$result['ProjectTask']['attachment_count'] = 0;
				$result['ProjectTask']['attach_read_status'] = 0;
				if( !empty($task['ProjectTask']['attachment'])) {
					$result['ProjectTask']['attachment_count'] += 1;
					$result['ProjectTask']['attach_read_status'] = 1;
				}
				foreach( $projectTaskAttachments as $_id => $_task_id){
					if( $_task_id == $taskId){
						$result['ProjectTask']['attachment_count'] += 1;
						$result['ProjectTask']['attach_read_status'] = 0;
					}
				}
				if ( array_key_exists($taskId, $projectTaskAttachmentRead )) $result['ProjectTask']['attach_read_status'] = intval($projectTaskAttachmentRead[$taskId]);
				
				// commment
				$result['ProjectTask']['comment_count'] = 0;
				$result['ProjectTask']['read_status'] = 0;
				
				foreach( $projectTaskComments as $_id => $_task_id){
					if( $_task_id == $taskId){
						$result['ProjectTask']['comment_count'] += 1;
						$result['ProjectTask']['read_status'] = 0;
					}
				}
				if ( array_key_exists($taskId, $projectTaskCommentRead )) $result['ProjectTask']['read_status'] = intval($projectTaskCommentRead[$taskId]);
				
				// format end date
				$result['ProjectTask']['task_end_date'] = !empty($result['ProjectTask']['task_end_date']) ? date('d F Y', strtotime($result['ProjectTask']['task_end_date'])) : '' ;
				$result['ProjectTask']['task_end_date_format'] = !empty($result['ProjectTask']['task_end_date']) ? date('d-F-Y', strtotime($result['ProjectTask']['task_end_date'])) : '' ;
				// Task late
				$result['ProjectTask']['late'] = 0;
				$result['ProjectTask']['assigned'] = array();
				if(strtotime(date('d-m-Y')) > strtotime($result['ProjectTask']['task_end_date'])){
					$result['ProjectTask']['late'] = 1;
				}
				if(!empty($result['ProjectTask']['task_assign_to_id'])){
					foreach($result['ProjectTask']['task_assign_to_id'] as $key =>$value){
						$result['ProjectTask']['assigned'][$key]['is_profit_center'] = $result['ProjectTask']['is_profit_center'][$key];
						$result['ProjectTask']['assigned'][$key]['reference_id'] = $result['ProjectTask']['task_assign_to_id'][$key];
						$result['ProjectTask']['assigned'][$key]['project_task_id'] = $data['id'];
					}
				}
				
				
			}
            $result = array("success" => $success, "message" => $result, "data" => $data, 'attachment' => $att_info);
			

            die(json_encode($result));
        }else {
			if( !empty($_POST['data']["ProjectTask"]["return"])){
				$this->redirect($_POST['data']["ProjectTask"]["return"]);
			}
			$this->redirect(array('action' => 'index', $project_id));
		}
    }
	function add_task_popup($project_id = null) {
		// exit;
		// debug( $this->employee_info); exit;
		if($project_id) $this->_checkRole(false, $project_id);
        $employee_info = $this->employee_info;
		$this->loadModel('Project');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
		// Check Role
		$profileName = array();
        if (!empty($employee_info['Employee']['profile_account'])) {
			$this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employee_info['Employee']['profile_account']
                )
            ));
        }
		$roleLogin = $employee_info['Role']['name'];
		$company_id = $employee_info["Company"]["id"];
		$employee_id = $employee_info['Employee']['id'];
		$this->loadModels('Project', 'ProjectEmployeeManager', 'ProfileProjectManager', 'Profile', 'Translation','ProfitCenter','ProjectPhasePlan');
		//Ticket 495 hien thi them part trong list phase (neu phase co part)
		$parts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
			'conditions' => array('project_id' => $project_id),
			'order' => array('weight' => 'ASC')
		));
		$listAllPhases = $this->ProjectPhasePlan->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
			),
			'joins' => array(
				array(
					'table' => 'project_phases',
					'alias' => 'ProjectsPhase',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectsPhase.id',
						'ProjectsPhase.company_id = ' . $company_id
					)
				)
			),
			'fields' => array('ProjectPhasePlan.id', 'ProjectPhasePlan.project_planed_phase_id', 'ProjectPhasePlan.project_id', 'ProjectPhasePlan.phase_planed_start_date', 'ProjectPhasePlan.phase_planed_end_date', 'ProjectPhasePlan.phase_real_start_date', 'ProjectPhasePlan.phase_real_end_date', 'ProjectsPhase.name', 'ProjectPhasePlan.project_part_id')
		));
		$listPhases = array();
		foreach ($listAllPhases as $Phase) {
			$part_id = $Phase['ProjectPhasePlan']['project_part_id'];
			if( !empty($part_id) && !empty($parts[$part_id ]) ){
				$part_name = $parts[$Phase['ProjectPhasePlan']['project_part_id']];
				$Phase['ProjectsPhase']['name'] = $Phase['ProjectsPhase']['name'] . ' (' . $part_name . ')';
			}
			$listPhases[$Phase['ProjectPhasePlan']['id']] = array_merge($Phase['ProjectPhasePlan'], $Phase['ProjectsPhase']);
		}
		if( empty ($listAllStatus)) $listAllStatus = $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id), 'order' => 'weight'));
		$projectPriorities = $this->ProjectTask->ProjectPriority->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'priority'),
		));
		
		$projectProfiles = $this->Profile->find('list', array(
			'recursive' => -1,
			'order' => array('id' => 'ASC'),
			'conditions' => array(
				'company_id' => $company_id,
			),
			'fields' => array('id', 'name'),
		));
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $company_id
			),
			'recursive' => -1,
			'fields' => array('original_text', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id'
					),
					'type' => 'left'
				)
			),
			'order' => array('TranslationSetting.setting_order' => 'ASC')
		));
		$workdays = $this->requestAction('/project_tasks/getWorkdays');
		// add Task
		$this->set(compact('listAllStatus', 'listPhases', 'projectPriorities', 'projectProfiles', 'adminTaskSetting', 'workdays', 'project_id'));

    }
	function edit_task_popup() {
		App::import("vendor", "str_utility");
		$str = new str_utility();
		$result = array('result' => false, 'task' => array(), 'columns' => array(), 'data' => array(), 'consumeResult' => array());
		if( isset($this->data['id']) ){
			$id = $this->data['id'];
            $task = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id,
                )
            ));
			// debug($task);
		
			if( !empty($task) ){
                $result['task'] = $task['ProjectTask'];
                $result['task']['task_start_date'] = $str->convertToVNDate($result['task']['task_start_date']);
                $result['task']['task_end_date'] = $str->convertToVNDate($result['task']['task_end_date']);
                $this->loadModels('ActivityTask', 'ActivityRequest', 'NctWorkload', 'ProjectTaskEmployeeRefer');
                $aTask = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id,
                        'is_nct' => 1
                    )
                ));
                if(!empty($aTask)){
                    $listRequestDates = $this->ActivityRequest->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'task_id' => $aTask['ActivityTask']['id'],
                            'value != 0'
                        ),
                        'fields' => array('id', 'date')
                    ));
                } else {
                    $listRequestDates = array();
                }
				
				// Employee assign
                $taskEmployeeRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $id),
                    'fields' => array('reference_id', 'is_profit_center')
                ));
				$result['task']['assigned'] = array();
				foreach ( $taskEmployeeRefers as $empl){
					$result['task']['assigned'][] = $empl['ProjectTaskEmployeeRefer'];
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
	public function task_move(){
		$list_tasks = array();
		if(!empty($this->data)){
			$list_tasks = $this->data;
		}
		$this->set(compact('list_tasks'));
	}
	public function import_excel($project_id){
		$this->_checkRole(false, $project_id);
		$this->loadModels('HistoryFilter', 'Translation', 'Project', 'ProjectTask', 'ProjectPhasePlan', 'ProjectPart', 'ProjectPhase', 'ProjectMilestone', 'Menu');
		$data = array();
		$original_text = array();
		if(!empty($_FILES)){
			App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
			App::import("vendor", "str_utility");
			$str_utility = new str_utility();
			$data_format = array();
			$data_columns = array();
			$path = FILES . 'uploads' . DS;
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
			if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
				$this->MultiFileUpload->encode_filename = false;
				$this->MultiFileUpload->uploadpath = $path;
				$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "xlsx";
				$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
				$reVal = $this->MultiFileUpload->upload();
				// debug($this->MultiFileUpload);
				// exit;
				if (!empty($reVal)) {
					$filename = $path . $reVal['csv_file_attachment']['csv_file_attachment'];
					$inputFileType = PHPExcel_IOFactory::identify($filename);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($filename);
					$objReader->setReadDataOnly(true);
					$objWorksheet = $objPHPExcel->getActiveSheet();

					$highestRow = $objWorksheet->getHighestRow(); 
					$highestColumn = $objWorksheet->getHighestColumn(); 
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
					$rows = array();
					$cols = array();
					for ($row = 1; $row <= $highestRow; ++$row) {
					  for ($col = 0; $col <= $highestColumnIndex; ++$col) {
						$cell = $objWorksheet->getCellByColumnAndRow($col, $row);
						if ($cell->getValue() instanceof PHPExcel_RichText) {
							$rows[$row][$col] = $cell->getValue()->getPlainText();
						} else {
							$rows[$row][$col] = $cell->getValue();
							if(!empty($rows[$row][$col]) && is_numeric($rows[$row][$col]) && PHPExcel_Shared_Date::isDateTime($cell) && strlen($rows[$row][$col])) {
								$rows[$row][$col] = date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
							}
						}
					  }
					}
					$data = $rows;
				}else{
					$this->Session->setFlash(__('Please select an Excel file', true), 'error');
				}
				if(!empty($filename)) unlink($filename);
			}
			if(!empty($data)){
				$flag = 0;
				foreach($data as $index => $values){
					if($flag == 0) $data_columns =  $values;
					else {
						$data_format[$index] = $values;
					}
					$flag = $index;
				}
				$originalText = $this->Translation->find('list', array(
					'conditions' => array(
						'page' => 'Project_Task',
						'TranslationSetting.company_id' => $this->employee_info['Company']['id'],
					),
					'recursive' => -1,
					'fields' => array('original_text', 'TranslationSetting.show'),
					'joins' => array(
						array(
							'table' => 'translation_settings',
							'alias' => 'TranslationSetting',
							'conditions' => array(
								'Translation.id = TranslationSetting.translation_id'
							),
							'type' => 'left'
						)
					),
					'order' => array('TranslationSetting.setting_order' => 'ASC')
				));
				$original_text = array();
				$ignore_field = array('id', 'eac', 'consumed', '%_progress_order_€', 'attachment', 'overload', 'unit_price', 'consumed_€', 'remain_€', 'workload_€', 'estimated_€', '+/-', 'profile', 'order', 'in_used', 'remain', 'initial_workload', 'initial_start_date', 'initial_end_date', 'amount_€', '%_progress_order', 'priority', 'completed', 'predecessor');
				
				$enable_part = $this->Menu->find('first',array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'model' => 'project',
						'controllers' => 'project_parts'
					),
					'fields' => array('display')
				));
				if(!empty($enable_part['Menu']['display'])) $original_text['project_planed_part_text'] = __('Part', true);
				$original_text['project_planed_phase_text'] = __('Phase', true);
				if(!empty($originalText)){
					foreach($originalText as $key => $display){
						if(!empty($display)){
							$idx =  strtolower(str_replace(' ', '_', $key));
							if(!in_array($idx, $ignore_field)) $original_text[$idx] = $key;
							if($idx == 'task'){
								$original_text['sub_task'] = __('Sub Task', true);
							}
							if($idx == 'predecessor' && !empty($originalText['ID']['display'])){
								$original_text['predecessor_name'] = $key;
							}
						}
					}
				}
				// Project
				$project = $this->Project->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $project_id,
					),
					'fields' => array('start_date', 'end_date'),
				));
				if(empty($project['Project']['start_date'])){
					$project['Project']['start_date'] = '0000-00-00';
					$project['Project']['end_date'] = '0000-00-00';
				}
				// Project task
				$task_name = $this->ProjectTask->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $project_id,
					),
					'fields' => array('id', 'task_title', 'project_planed_phase_id', 'parent_id'),
				));
				$task_name =  !empty($task_name) ? Set::classicExtract($task_name, '{n}.ProjectTask') : array();
			
				// Project phase
				$projectPhase = $this->ProjectPhase->find('all',array(
					'recursive' => -1,
					'conditions' => array('company_id' => $this->employee_info['Company']['id']),
					'fields' => array('name')
				));
				$projectPhase = !empty($projectPhase) ? Set::classicExtract($projectPhase, '{n}.ProjectPhase.name') : array();
				
				$projectStatus = $this->list_projectStatus($project_id);
				$projectStatus = !empty($projectStatus) ? Set::classicExtract($projectStatus, '{n}.ProjectStatus') : array();
				$listEmployee = $this->_getTeamEmployeesForLoad($project_id, null, $project['Project']['start_date'], $project['Project']['end_date']);
				$listEmployee = !empty($listEmployee) ? Set::classicExtract($listEmployee, '{n}.Employee') : array();

			}
		}
		$setting_matching = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'path' => 'tasks_importer_setting_matching_fields',
				'employee_id' => $this->employee_info['Employee']['id']
			),
			'fields' => array('params'),
		));
			
		$setting_matching = !empty($setting_matching) ? @unserialize($setting_matching['HistoryFilter']['params']) : array(); 
		$this->set(compact('data_format', 'data_columns', 'original_text', 'setting_matching', 'project_id', 'task_name', 'projectPhase', 'projectStatus', 'listEmployee'));
	}
	function save_setting_matching_fields() {
		$this->loadModels('HistoryFilter', 'Employee');
        if (!empty($_POST)) {
            extract($_POST);
            $path = rtrim($_POST['path'], '/');
            $params = $_POST['params'];
            $employId = $this->Session->read('Auth.Employee.id');
            $employId = isset($employId) ? $employId : null;
            $last = $this->Employee->HistoryFilter->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'id', 'params'
                ),
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $this->employee_info['Employee']['id']
            )));

            $this->Employee->HistoryFilter->create();
            if (!empty($last)) {
                $this->Employee->HistoryFilter->id = $last['HistoryFilter']['id'];
                unset($last);
            }
            if (empty($params)) {
                Configure::write('debug', 0);
                echo json_encode($_params);
                exit();
            }
            $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => serialize($params),
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false)
            );
            echo json_encode($params);
        }
        exit();
    }
	function export_excel_index(){
		 if (!empty($this->data)) {			
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                    ->setT('Project tasks imported') //auto translate
                    ->save($data, 'project_tasks_imported.xls');
        }
        die;
	}
}