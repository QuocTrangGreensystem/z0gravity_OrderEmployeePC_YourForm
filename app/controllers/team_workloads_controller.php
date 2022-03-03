<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class TeamWorkloadsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'TeamWorkloads';
    /**
     * Controller using model
     * @var array
     * @access public
     */
    var $uses = array();

    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModels('NctWorkload','Project', 'ProjectTask', 'ProjectPhasePlan', 'ProjectPhase', 'ActivityTask', 'Project', 'Employee', 'Activity', 'ProfitCenter', 'ActivityRequest', 'ActivityTaskEmployeeRefer', 'ProjectTaskEmployeeRefer', 'ProjectPriority');
        $this->loadModels('ProjectFunctionEmployeeRefer', 'ProjectFunction', 'ProjectEmployeeProfitFunctionRefer', 'ProjectTeam');
    }

    public function index(){
        if (!empty($this->params['url'])  && !empty($this->employee_info['CompanyEmployeeReference']['company_id'])) {
            $params =  $this->params['url'];
            if(!empty($params['teampriority'])){
                $priorities = $params['teampriority'];
            }
            $company_id = $this->employee_info['CompanyEmployeeReference']['company_id'];
            $start = strtotime('01-'.$params['smonth'].'-'.$params['syear']);
            $end = strtotime('01-'.$params['emonth'].'-'.$params['eyear']);
            $end = strtotime('last day of this month', $end);
            $mStart = $params['smonth'];
            $yStart = $params['syear'];
            $mEnd = $params['emonth'];
            $yEnd = $params['eyear'];
            /**
             *  lay cac employee cua profit center truyen vao
             */
            $resources = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname'),
                'conditions' => array('profit_center_id' => $params['team'])
            ));
            $employees = array_keys($resources);
            $pcName = $this->ProfitCenter->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array('ProfitCenter.id' => $params['team'])
            ));
            $pcName = $pcName['ProfitCenter']['name'];
            /**
            * xet project theo dieu kien priority
            */
            $_conditions['company_id'] = $company_id;
            $_conditions['category'] = array(1, 3);
            if(!empty($priorities)){
                $_conditions['project_priority_id'] = $priorities;
            }
            $projectPriority = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_priority_id'),
                'conditions' => $_conditions
            ));
            $projectLists = array_keys($projectPriority);
            if(!empty($projectLists)){
                $taskLists = $this->ProjectTask->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id'),
                    'conditions' => array(
                        'project_id' => $projectLists,
                        'is_nct' => 1,
                    )
                ));
            }
            /**
             * Lay task tu NCT Workload theo profit center filter, and priority
             */
            // lay nhung task thuoc NCT trong project task.
            $conditions = array(
                'task_date >= ' => date('Y-m-d', $start),
                'end_date <=' => date('Y-m-d', $end),
                'project_task_id' => $taskLists,
                'AND' => array(
                    'OR' => array(
                        array(
                            'reference_id' => $employees,
                            'is_profit_center' => 0
                        ),
                        array(
                            'reference_id' => $params['team'],
                            'is_profit_center' => 1
                        )
                    )
                ),
                'group_date LIKE' => '2%'
            );
            $listNctTasks = $this->NctWorkload->find('all', array(
                'recursive' => -1,
                'fields' => array('*'),
                'conditions' => $conditions
            ));
            $taskOfProjects = $taskOfActivities = $workloadOfPTask = $workloadOfATask = $listIdsPr = $listPTids = array();
            $assigns = $listAssign = array();
            $_listId = array();
            $i = 1;
            if(!empty($listNctTasks)){
                foreach($listNctTasks as $listNctTask){
                    $dx = $listNctTask['NctWorkload'];
                    $key = sprintf('%s-%s', $dx['is_profit_center'], $dx['reference_id']);
                    //sum
                    if(!in_array($dx['project_task_id'], $taskOfProjects)){
                        $taskOfProjects[$dx['id']] = $dx['project_task_id'];
                    }
                    if(!in_array($dx['activity_task_id'], $taskOfActivities)){
                        $taskOfActivities[$dx['id']] = $dx['activity_task_id'];
                    }
                    $listIdsPr[$dx['activity_task_id']] = $dx['project_task_id'];
                    $listPTids[ $dx['project_task_id'] ] = $dx['project_task_id'];
                    $monthYear = explode('-', $dx['task_date']);
                    $month = $monthYear[1] . '-' . $monthYear[0];

                    //assign information
                    if($dx['project_task_id'] == 0){
                        if(!isset($workloadOfATask[$dx['activity_task_id']][$month])){
                            $workloadOfATask[$dx['activity_task_id']][$month] = 0;
                        }
                        $workloadOfATask[$dx['activity_task_id']][$month] += $dx['estimated'];
                        $taskKey = 'a-' . $dx['activity_task_id'];
                        $assigns[$taskKey][$month][$key] = $dx['estimated'];
                    } else {
                        if(!isset( $workloadOfPTask[$dx['project_task_id']][$month])){
                            $workloadOfPTask[$dx['project_task_id']][$month] = 0;
                        }
                        $workloadOfPTask[$dx['project_task_id']][$month] += $dx['estimated'];
                        $taskKey = 'p-' . $dx['project_task_id'];
                        $assigns[$taskKey][$month][$key] = $dx['estimated'];
                    }
                    //list assign
                    if( !isset($listAssign[$taskKey][$key]) ){
                        $listAssign[$taskKey][$key] = 0;
                    }
                    if(empty($_listId[$i])){
                    $_listId[$i] = $dx['project_task_id'];
                    $i++;
                    }
                }
            }
            $taskMap = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'fields' => array('project_task_id', 'id'),
                'conditions' => array(
                    'project_task_id' => $listPTids
                )
            ));
            /**
             * Lay thong tin cua project task theo $taskOfProjects
             * Out put: id, name (task_title), phase_plan_id, project_id
             */
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'task_title', 'project_id', 'project_planed_phase_id'),
                'conditions' => array('ProjectTask.id' => $taskOfProjects),
                'order' => array('project_planed_phase_id' => 'ASC', 'task_title' => 'ASC')
            ));
            $listTasksIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.id')) : array();
            $projectOfPTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
             /**
              * Dung Set::classicExtract lay danh sach project_id ra
              * Dung Set::classicExtract lay danh sach phase_plan_id ra
              */
            $listProjectIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_id')) : array();
            $listProjectPlanedPhaseIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_planed_phase_id')) : array();
             /**
              * Lay thong tin cua activity task theo $taskOfActivities
              * Out put: id, name (task_title), activity_id
              */
            $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'name', 'activity_id', 'project_task_id'),
                'conditions' => array('ActivityTask.id' => $taskOfActivities),
                'order' => array('id' => 'ASC')
            ));
             /**
              * Dung Set::classicExtract lay danh sach activity_id ra
              */
            $listActivityIds = !empty($activityTasks) ? array_unique(Set::classicExtract($activityTasks, '{n}.ActivityTask.activity_id')) : array();
            $activityOfATasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
             /**
              * Vao project_phase_plan....lay danh sach project phase id ra
              * find theo list, lay id va project_phase_id
              */
            $plans = $this->ProjectPhasePlan->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'project_id', 'project_planed_phase_id', 'Phase.name'),
                'conditions' => array(
                    'ProjectPhasePlan.id' => $listProjectPlanedPhaseIds
                ),
                'joins' => array(
                    array(
                        'table' => 'project_phases',
                        'alias' => 'Phase',
                        'type' => 'LEFT',
                        'conditions' => array('Phase.id = ProjectPhasePlan.project_planed_phase_id')
                    )
                ),
                'order' => array('Phase.name' => 'ASC')
            ));
             // tao data da sap xep
            $datas = $projectPhaseIds = array();
            foreach($plans as $plan){
                $dx = $plan['ProjectPhasePlan'];
                $projectPhaseIds[ $dx['id'] ] = $dx['project_planed_phase_id'];
                $datas[ 'p-' . $dx['project_id'] ][ $dx['project_planed_phase_id'] ] = array();
            }
             /**
              * tu danh sach $projectPhaseIds lay name cua project phase
              * out put: id, name
              */
            $projectPhases = $this->ProjectPhase->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array(
                    'ProjectPhase.id' => $projectPhaseIds
                )
            ));
            $priotityName = $this->ProjectPriority->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'priority'),
                'conditions' => array( 'company_id' => $company_id)
            ));
            $listProjects = $this->Project->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'project_name', 'activity_id'),
                'conditions' => array(
                    'Project.id' => $listProjectIds
                )
            ));
            $idOfPriorityProject = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_priority_id'),
                'conditions' => array(
                    'Project.id' => $listProjectIds
                )
            ));
            /**
             * Lay danh sach project theo $listProjectIds
             * out put: id va project name
             */
            $projectNames = !empty($listProjects) ? Set::combine($listProjects, '{n}.Project.id', '{n}.Project.project_name') : array();
            /**
             * Lay danh sach activity theo $listActivityIds
             * out put: id va activity name
             */
             $listActivities = $this->Activity->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array('Activity.id' => $listActivityIds)
             ));
             /**
              * Xay dung array du lieu truyen xuong view
              */
            $listTaskLinkeds = $keyOfIdOfTask = $priorityNamePJ = array();
            $taskCount = array();
            if(!empty($projectTasks)){
                foreach($projectTasks as $projectTask){
                    $dx = $projectTask['ProjectTask'];
                    $projectId = 'p-' . $dx['project_id'];
                    $phaseId = $projectPhaseIds[$dx['project_planed_phase_id']];
                    $taskID = $dx['id'];
                    $listTaskLinkeds[] = $taskID;
                    // if(!isset($datas[$projectId][$phaseId][$taskID])){
                    //     $datas[$projectId][$phaseId][$taskID] = '';
                    // }
                    $datas[$projectId][$phaseId][$taskID] = $dx['task_title'];
                    //count
                    if( !isset($taskCount[$projectId]) ){
                        $taskCount[$projectId] = 0;
                    }
                    $taskCount[$projectId]++;
                    // buil priority name from project id.
                    $priorityNamePJ[$projectId] = !empty($priotityName[ $idOfPriorityProject[$dx['project_id']] ]) ? $priotityName[ $idOfPriorityProject[$dx['project_id']] ] . ' - ' . $projectNames[$dx['project_id']] : '' . ' - ' . $projectNames[$dx['project_id']];
                }
            }
            asort($priorityNamePJ);
            if(!empty($activityTasks)){
                foreach($activityTasks as $activityTask){
                    $dx = $activityTask['ActivityTask'];
                    $activity = 'a-' . $dx['activity_id'];
                    if(in_array($dx['project_task_id'], $listTaskLinkeds)){
                        continue;
                    }
                    if(!isset($datas[$activity][0][$dx['id']])){
                        $datas[$activity][0][$dx['id']] = '';
                    }
                    $datas[$activity][0][$dx['id']] = $dx['name'];
                    if( !isset($taskCount[$activity]) ){
                        $taskCount[$activity] = 0;
                    }
                    $taskCount[$activity]++;
                    $priorityNamePJ[ $activity ] = '';
                }
            }
            //lay consume theo task
            $this->ActivityRequest->virtualFields['month_date'] = 'FROM_UNIXTIME(ActivityRequest.date, "%m-%Y")';
            $this->ActivityRequest->virtualFields['consumed'] = 'SUM(ActivityRequest.value)';
            //doan nay lay consume cua mau den. NCT task.
            $dataConsume = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => $taskOfActivities,
                    'employee_id' => $employees,
                    'status' => 2,
                    'value !=' => 0,
                ),
                'fields' => array('month_date', 'consumed', 'task_id', 'employee_id'),
                'group' => array('task_id', 'month_date', 'employee_id')
            ));
            // $resourceConsume la consume cho tung resource khi ma 1 task co nhieu assign.
            $consumeOfTasks = $resourceConsume = $totalCsOfTasks = array();
            foreach($dataConsume as $consume){
                $dx = $consume['ActivityRequest'];
                // lam the nay de lay consume cua nhung task tu thang start den end.
                $_month = '01-' . $dx['month_date'];
                $_month = strtotime($_month);
                // xac dinh la task id cua PJ hay Ac.
                if( !empty($listIdsPr[ $dx['task_id'] ]) ){
                    //is project task
                    $taskId = 'p-' . $listIdsPr[ $dx['task_id'] ];
                } else {
                    $taskId = 'a-' . $dx['task_id'];
                }
                // lay consume theo start va end.
                if( ($start <= $_month) && ($end >= $_month)  ){
                    if( !isset($consumeOfTasks[ $dx['task_id'] ][ $dx['month_date'] ]) ){
                        $consumeOfTasks[ $dx['task_id'] ][ $dx['month_date'] ] = 0;
                    }
                    $consumeOfTasks[ $dx['task_id'] ][ $dx['month_date'] ] += $dx['consumed'];
                    //consume by resource
                    $resourceKey = '0-' . $dx['employee_id'];
                    $resourceConsume[ $dx['task_id'] ][ $resourceKey ][ $dx['month_date'] ] = $dx['consumed'];
                    //consume of pc = consume of employees NOT ASSIGNED to the task
                    $teamId = '1-' . $params['team'];
                    if( !isset($resourceConsume[ $dx['task_id'] ][ $teamId ][ $dx['month_date'] ]) ){
                        $resourceConsume[ $dx['task_id'] ][ $teamId ][ $dx['month_date'] ] = 0;
                    }
                    if( !isset($listAssign[$taskId][ $resourceKey ]) ){
                        $resourceConsume[ $dx['task_id'] ][ $teamId ][ $dx['month_date'] ] += $dx['consumed'];
                    }
                }
                // lay total consume cua task
                if( !isset( $totalCsOfTasks[ $taskId ] ) ){
                    $totalCsOfTasks[ $taskId ] = 0;
                }
                $totalCsOfTasks[ $taskId ] += $dx['consumed'];
            }
            $listTaskOfProjects = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_id'),
                'conditions' => array('project_id' => $listProjectIds)
            ));
            // lay tat ca cac task cua PJ. tinh consume cho PJ.
            $listAllTaskOfActivities = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_task_id'),
                'conditions' => array(
                    'OR' => array(
                        'activity_id' => $listActivityIds,
                        'project_task_id' => array_keys($listTaskOfProjects)
                    )
                )
            ));
            // lay tat ca consume cho PJ.
            $allCsOfPjTasks = $this->ActivityRequest->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => array_keys($listAllTaskOfActivities),
                    'employee_id' => $employees,
                    'status' => 2,
                    'value !=' => 0
                ),
                'fields' => array('task_id', 'consumed'),
                'group' => array('task_id')
            ));
            // buil lai theo PJ.
            $allDataCs = array();
            if( !empty($allCsOfPjTasks) ){
                foreach ($allCsOfPjTasks as $_id => $allCsOfPjTask) {
                    $dx = !empty($listAllTaskOfActivities[ $_id ]) ? $listAllTaskOfActivities[ $_id ] : 0;
                    $parentId = !empty($listTaskOfProjects[ $dx ]) ? $listTaskOfProjects[ $dx ] : 0;
                    if( empty($allDataCs[ $parentId ]) ){
                        $allDataCs[ $parentId ] = 0;
                    }
                    $allDataCs[ $parentId ] += $allCsOfPjTask;
                }
            }
            // end for all consume of PJ -----------
            // unset nhung NCT task. tinh consume mau cam.( vi chi tinh consme cho task thuong)
            foreach ($listPTids as $_id) {
                if(isset($listTaskOfProjects[$_id])){
                    unset($listTaskOfProjects[$_id]);
                }
            }
            $listTaskOfActivities = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'activity_id', 'project_task_id'),
                'conditions' => array(
                    'OR' => array(
                        'activity_id' => $listActivityIds,
                        'project_task_id' => array_keys($listTaskOfProjects)
                    )
                )
            ));
            // tinh consume cho project(consume mau cam(task thuong)).
            $totalConsumedOfMonth = array();
            $listAllTasks = $activityOfATasks = array();
            if(!empty($listTaskOfActivities)){
                foreach($listTaskOfActivities as $listTaskOfActivity){
                    $dx = $listTaskOfActivity['ActivityTask'];
                    $listAllTasks[] = $dx['id'];
                    if(empty($dx['project_task_id'])){
                        $activityOfATasks[$dx['id']] = $dx['activity_id'];
                    } else {
                        $listIdsPr[$dx['id']] = $dx['project_task_id'];
                    }
                }
                // lay request ra theo $listAllTasks( task thuong.)
                $totalConsumes = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'employee_id' => $employees,
                        'task_id' => $listAllTasks,
                        'status' => 2,
                        'value !=' => 0,
                        'date BETWEEN ? AND ?' => array($start, $end)
                    ),
                    'fields' => array('month_date', 'consumed', 'task_id'),
                    'group' => array('task_id', 'month_date')
                ));
                foreach($totalConsumes as $totalConsume){
                    $dx = $totalConsume['ActivityRequest'];
                    // loai nhung id task van con trung. cho id = 0 va unset no.
                    if(!empty($listIdsPr[$dx['task_id']])){ // project task
                        $pTask = $listIdsPr[$dx['task_id']];
                        $_id = !empty($listTaskOfProjects[$pTask]) ? 'p-' .  $listTaskOfProjects[$pTask] : '0';
                    } else {
                        $_id = !empty($activityOfATasks[$dx['task_id']]) ? 'a-' . $activityOfATasks[$dx['task_id']] : '0';
                    }
                    if(!isset($totalConsumedOfMonth[$_id][$dx['month_date']])){
                        $totalConsumedOfMonth[$_id][$dx['month_date']] = 0;
                    }
                    $totalConsumedOfMonth[$_id][$dx['month_date']] += $dx['consumed'];
                    if(!isset($totalConsumedOfMonth[$_id]['total'])){
                        $totalConsumedOfMonth[$_id]['total'] = 0;
                    }
                    $totalConsumedOfMonth[$_id]['total'] += $dx['consumed'];
                }
                unset($totalConsumedOfMonth[0]);
            }
            // lay total workload theo task.
            $this->NctWorkload->virtualFields['sum_workload'] = 'SUM(NctWorkload.estimated)';
            $totalWorkload = $this->NctWorkload->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $listTasksIds,
                    'AND' => array(
                        'OR' => array(
                            array(
                                'reference_id' => $employees,
                                'is_profit_center' => 0
                            ),
                            array(
                                'reference_id' => $params['team'],
                                'is_profit_center' => 1
                            )
                        )
                    ),
                ),
                'fields' => array('project_task_id', 'sum_workload'),
                'group' => array('project_task_id')
            ));
            // lay workload for activity
            $workloadActivity = $this->NctWorkload->find('list', array(
                'recusive' => -1,
                'conditions' => array(
                    'activity_task_id' => array_keys($activityOfATasks),
                    'project_task_id' => 0
                ),
                'fields' => array('activity_task_id', 'sum_workload'),
                'group' => array('activity_task_id')
            ));
            // lay freeze for project
            $projectFreeze = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $listTasksIds
                ),
                'fields' => array('id', 'initial_estimated')
            ));
            $activityFreeze = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_keys($activityOfATasks),
                    'OR' => array(
                        array('project_task_id' => 0),
                        array('project_task_id is NULL')
                    )
                ),
                'fields' => array('id', 'initial_estimated')
            ));
            $this->set(compact('start', 'end', 'datas', 'listActivities', 'projectNames', 'projectPhases', 'projectTasks', 'workloadOfPTask', 'workloadOfATask', 'projectOfPTasks', 'activityOfATasks', 'pcName', 'consumeOfTasks', 'listIdsPr', 'totalWorkload', 'projectFreeze', 'activityFreeze', 'allDataCs', 'totalCsOfTasks'));
            $this->set(compact('resources', 'assigns', 'params', 'listAssign', 'idOfPriorityProject', 'priotityName', 'taskCount', 'resourceConsume', 'taskMap', 'totalConsumedOfMonth', 'workloadActivity', 'listProjectIds', 'mStart', 'yStart', 'mEnd', 'yEnd', 'priorityNamePJ'));
            $_SESSION['staffing'] = !empty($_SESSION['staffing']) ? $_SESSION['staffing'] : array();
            $this->set('staffing', $_SESSION['staffing']);
        }
    }

    public function saveWorkload(){
        $result = array();
        if( !empty($this->data) ){
            $taskId = $this->data['id'];
            $type = $this->data['type'];
            $month = $this->data['month'];
            $workloads = $this->data['workload'];
            $employ = $this->employee_info['Employee']['fullname'];
            $_month = explode('-', $month);
            $_month = $_month[1] . '-' . $_month[0] . '-01';
            // save action log
            if($this->data['type'] == 'p'){
                $taskName = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ProjectTask.id' => $taskId
                    ),
                    'fields' => array('id', 'task_title', 'project_id')
                ));
                $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Project.id' => !empty($taskName) ? $taskName['ProjectTask']['project_id'] : ''
                    ),
                    'fields' => array('id', 'project_name')
                ));
                $project_id = !empty($projectName) ? $projectName['Project']['id'] : 0;
                $taskName = !empty($taskName) ? $taskName['ProjectTask']['task_title'] : '';
                $projectName = !empty($projectName) ? $projectName['Project']['project_name'] : '';
                $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('PDC', true) . ' ' .  sprintf('Update task `%s` `%s`', $taskName, $projectName);
                foreach ($workloads as $key => $value) {
                    $key = explode('-', $key);
                    $_wload = $this->NctWorkload->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $taskId,
                            'reference_id' => $key[1],
                            'is_profit_center' => $key[0],
                            'task_date' => $_month
                        ),
                        'fields' => array('id', 'estimated')
                    ));
                    $_wload = !empty($_wload) ? $_wload['NctWorkload']['estimated'] : 0;
                    if($_wload != $value){
                        $message .= ' ' . __('Workload', true). ' '. $_wload . ' ' . __('To', true). ' ' . $value;
                    }
                }
            } else{
                $taskName = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ProjectTask.id' => $taskId
                    ),
                    'fields' => array('id', 'name')
                ));
                $ActivityName = $this->Activity->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Project.id' => !empty($taskName) ? $taskName['ActivityTask']['activity_id'] : ''
                    ),
                    'fields' => array('id', 'name')
                ));
                $activity_id = !empty($ActivityName) ? $ActivityName['Project']['id'] : 0;
                $taskName = !empty($taskName) ? $taskName['ActivityTask']['name'] : '';
                $ActivityName = !empty($ActivityName) ? $ActivityName['Project']['name'] : '';
                $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('PDC', true) . ' ' . sprintf('Update activity task `%s` `%s`', $taskName, $ActivityName);
                foreach ($workloads as $key => $value) {
                    $key = explode('-', $key);
                    $_wload = $this->NctWorkload->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'activity_task_id' => $taskId,
                            'reference_id' => $key[1],
                            'is_profit_center' => $key[0],
                            'task_date' => $_month
                        ),
                        'fields' => array('id', 'estimated')
                    ));
                    $_wload = !empty($_wload) ? $_wload['NctWorkload']['estimated'] : 0;
                    if($_wload != $value){
                        $message .= ' ' . __('Workload', true). ' '. $_wload . ' ' . __('To', true). ' ' . $value;
                    }
                }
            }
            $result = $this->saveWls($taskId, $type, $month, $workloads);
            $this->writeLog($result, $this->employee_info, $message);
            // if($this->data['type'] == 'p'){
            //     if($project_id != null)  $this->ProjectTask->staffingSystem($project_id);
            // }else{
            //     if($activity_id != null)  $this->ActivityTask->staffingSystem($activity_id);
            // }
        }
        die(json_encode($result));
    }

    function export(){
        if( !empty($this->data) ){
            $this->layout = '';
            $this->data['header'] = json_decode($this->data['header'], true);
            $this->data['data'] = json_decode($this->data['data'], true);
            $this->set('data', $this->data);
        }
        else {
            die;
        }
    }
    public function updateWorkload(){
        set_time_limit(0);
        if( !isset($_SESSION['staffing']) )$_SESSION['staffing'] = array();
        $result = array();
        $_datas = json_decode($_POST['workload'], true);
        $mStart = $_datas['mStart'];
        $yStart = $_datas['yStart'];
        $mEnd = $_datas['mEnd'];
        $yEnd = $_datas['yEnd'];
        if(!empty($_datas['consume'])){
            $datas = $_datas['consume'];
            $month = $_datas['month'];
            $pc = $_datas['pc'];
            foreach ($datas as $id => $workloads) {
                $idT = explode('-', $id);
                $type = $idT[0];
                $taskId = $idT[1];
                // lay start date and end date cua task.
                if( $type == 'p' ){
                    $_task = $this->ProjectTask->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'task_start_date', 'task_end_date', 'project_id'),
                        'conditions' => array(
                            'ProjectTask.id' => $taskId
                        )
                    ));
                    $sDate = strtotime($_task['ProjectTask']['task_start_date']);
                    $eDate = strtotime($_task['ProjectTask']['task_end_date']);
                    $_SESSION['staffing'][] = 'p-' . $_task['ProjectTask']['project_id'];
                } else {
                    $_task = $this->ActivityTask->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'task_start_date', 'task_end_date', 'activity_id'),
                        'conditions' => array(
                            'ActivityTask.id' => $taskId,
                            'OR' => array(
                                'project_task_id is NULL',
                                'project_task_id' => 0
                            )
                        )
                    ));
                    $sDate = $_task['ActivityTask']['task_start_date'];
                    $eDate = $_task['ActivityTask']['task_end_date'];
                    $_SESSION['staffing'][] = 'a-' . $_task['ActivityTask']['activity_id'];
                }
                // xu ly nhung truong hop khong co workload nhung co consume.
                if(isset($workloads['none'])){
                    // $cs = $workloads[0];
                    $start = strtotime('01-' . $month);
                    $end = strtotime('last day of this month', $start);
                    $this->ActivityRequest->virtualFields['month_date'] = 'FROM_UNIXTIME(ActivityRequest.date, "%m-%Y")';
                    $this->ActivityRequest->virtualFields['consumed'] = 'SUM(ActivityRequest.value)';
                    $activityTaskId = $this->ActivityTask->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'project_task_id'),
                        'conditions' => array('project_task_id' => $taskId)
                    ));
                    $activityTaskId = $activityTaskId['ActivityTask']['id'];
                    $csRecord = $this->ActivityRequest->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'task_id' => $activityTaskId,
                                'status' => 2,
                                'value !=' => 0,
                                'date BETWEEN ? AND ?' => array($start, $end)
                            ),
                            'fields' => array('employee_id', 'consumed'),
                            'group' => array('employee_id')
                    ));
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
                    $_workload = array();
                    foreach ($csRecord as $idEm => $cs) {
                        $workloads['0-'.$idEm] = $cs;
                        $_workload['0-'.$idEm] = 0;
                        // lay pj id de save project team.
                        if($type == 'p'){
                            $projectId = $this->ProjectTask->find('first', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'ProjectTask.id' => $taskId
                                ),
                                'fields' => array('id', 'project_id')
                            ));
                            $projectId = $projectId['ProjectTask']['project_id'];
                            $saved = array(
                                'project_id' => $projectId,
                                'price_by_date' => 0,
                                'allow_request' => 0
                            );
                            $projectTeam = $this->ProjectTeam->find('list', array(
                                'recursive' => -1,
                                'fields' => array('id', 'id'),
                                'conditions' => array(
                                    'project_id' => $projectId
                                )
                            ));
                            if(!empty($projectTeam)){
                                $r = $this->ProjectFunctionEmployeeRefer->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'employee_id' => $idEm,
                                        'project_team_id' => $projectTeam
                                    ),
                                    'fields' => array('id', 'id')
                                ));
                                if(empty($r)){
                                    $this->ProjectTeam->create();
                                    $this->ProjectTeam->save($saved);
                                    $newPT = $this->ProjectTeam->find('first', array(
                                        'recursive' => -1,
                                        'fields' => array('id', 'id'),
                                        'conditions' => array(
                                            'project_id' => $projectId
                                        )
                                    ));
                                    $newPT = $newPT['ProjectTeam']['id'];
                                    $s = array(
                                        'employee_id' => $idEm,
                                        'profit_center_id' => !empty($employeeRefers[$idEm]) ? $employeeRefers[$idEm] : '',
                                        'project_team_id' => $newPT,
                                        'is_backup' => 0,
                                        'allow_request' => 0
                                    );
                                    $this->ProjectFunctionEmployeeRefer->create();
                                    $this->ProjectFunctionEmployeeRefer->save($s);
                                }
                            } else{
                                $this->ProjectTeam->create();
                                $this->ProjectTeam->save($saved);
                                $newPT = $this->ProjectTeam->find('first', array(
                                    'recursive' => -1,
                                    'fields' => array('id', 'id'),
                                    'conditions' => array(
                                        'project_id' => $projectId
                                    )
                                ));
                                $newPT = $newPT['ProjectTeam']['id'];
                                $s = array(
                                    'employee_id' => $idEm,
                                    'profit_center_id' => !empty($employeeRefers[$idEm]) ? $employeeRefers[$idEm] : '',
                                    'project_team_id' => $newPT,
                                    'is_backup' => 0,
                                    'allow_request' => 0
                                );
                                $this->ProjectFunctionEmployeeRefer->create();
                                $this->ProjectFunctionEmployeeRefer->save($s);
                            }
                        }
                    }
                    // save NCT task for employee with workload = 0;
                    while ($sDate <= $eDate) {
                        $_sDate = date('m-Y', $sDate);
                        // kiem tra xem da co workload chua? neu co thi giu nguyen, khong thi gan = 0
                        $_sD = date('Y-m', $sDate) . '-' . '01';
                        $_eD = strtotime('last day of this month', $sDate);
                        $_eD = date('Y-m-d', $end);
                        foreach ($_workload as $_key => $_value) {
                            $_wls = array();
                            $_k = explode('-', $_key);
                            if ($_k == 0){
                                $record = $this->NctWorkload->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'task_date' => $_sD,
                                        'end_date' => $_eD,
                                        'reference_id' => $_k[1],
                                        'is_profit_center' => 0,
                                        'group_date LIKE' => '2%',
                                        'project_task_id' => $taskId
                                    ),
                                    'fields' => array('id', 'estimated')
                                ));
                                $record = !empty($record) ? $record['NctWorkload']['estimated'] : 0;
                                $_wls[$_key] = $record;
                                $rs = $this->saveWls($taskId, $type, $_sDate, $_wls);
                            }
                        }
                        $sDate = mktime(0, 0, 0, date("m", $sDate)+1, date("d", $sDate), date("Y", $sDate));
                    }
                    //save NCT task for team with workload = 0.
                    $_pc = '1-' . $pc;
                    $_wld = array(
                        $_pc => 0
                    );
                    $rs = $this->saveWls($taskId, $type, $month, $_wld);
                    unset($workloads['none']);
                    $rs = $this->saveWls($taskId, $type, $month, $workloads);
                } else {
                    if(!empty($workloads)){
                        // tinh workload assign
                        $rs = $this->saveWls($taskId, $type, $month, $workloads);
                        // $result[$id] = $rs;
                    }
                }
            }
        }
        $this->Session->setFlash(__('Saved', true), 'success');
        $actions = '/team_workloads?smonth=' . $mStart . '&syear=' . $yStart . '&emonth=' . $mEnd . '&eyear=' . $yEnd . '&team=' . $pc;
        $_SESSION['staffing'] = array_unique($_SESSION['staffing']);
        $this->redirect($actions);
    }
    public function staffings(){
        set_time_limit(0);
        ignore_user_abort(true);
        $_SESSION['staffing'] = array();
        if(!empty($_POST['list'])){
            $ids = $_POST['list'];
            foreach ($ids as $id) {
                $id = explode('-', $id);
                if($id[0] == 'p'){
                    if($id[1] != null)    $this->ProjectTask->staffingSystem($id[1]);
                }else{
                    if($id[1] != null)    $this->ActivityTask->staffingSystem($id[1]);
                }
            }
            echo 'done';
        }
        exit;
    }
    // save wl dung chung
    public function saveWls($taskId, $type, $month, $workloads){
        $start = strtotime('01-' . $month);
        $end = strtotime('last day of this month', $start);
        $group = '2_' . date('d-m-Y', $start) . '_' . date('d-m-Y', $end);
        //doi nguoc start, end
        $start = date('Y-m-d', $start);
        $end = date('Y-m-d', $end);
        $taskField = $type == 'a' ? 'activity_task_id' : 'project_task_id';
        $total = 0;
        $this->NctWorkload->virtualFields['sum_workload'] = 'SUM(NctWorkload.estimated)';
        foreach($workloads as $res => $workload){
            $resource = explode('-', $res);    //[0] = is_profit_center, [1] = reference_id
            //find
            $save = array(
                $taskField => $taskId,
                'group_date' => $group,
                'is_profit_center' => $resource[0],
                'reference_id' => $resource[1]
            );
            $record = $this->NctWorkload->find('first', array(
                'recursive' => -1,
                'conditions' => $save
            ));
            if($type == 'p'){
                $activityTaskId = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'fields' => array('id', 'project_task_id'),
                    'conditions' => array('project_task_id' => $taskId)
                ));
                $activityTaskId = $activityTaskId['ActivityTask']['id'];
                $save['activity_task_id'] = $activityTaskId;
            }
            //save
            $save['estimated'] = $workload;
            $save['task_date'] = $start;
            $save['end_date'] = $end;
            if( !empty($record) ){
                $this->NctWorkload->id = $record['NctWorkload']['id'];
            } else {
                $this->NctWorkload->create();
            }
            $this->NctWorkload->save($save);
            $result['assign'][$res] = $workload;
            $total += $workload;
        }
        $result['total'] = $total;
        //adjust task start/end date
        $Model = $type == 'a' ? 'ActivityTask' : 'ProjectTask';
        $task = $this->$Model->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $taskId
            ),
            'fields' => array('task_start_date', 'task_end_date')
        ));
        $task = $task[$Model];
        $wl = $this->NctWorkload->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                $taskField => $taskId
            ),
            'fields' => array('SUM(estimated) as workload', 'MAX(CAST(end_date AS CHAR)) as max_date', 'MIN(CAST(task_date AS CHAR)) as min_date'),
            'group' => array($taskField)
        ));
        $wl = $wl[0];
        $wl['max_date'] = strtotime($wl['max_date'] . ' 00:00:00');
        $wl['min_date'] = strtotime($wl['min_date'] . ' 00:00:00');
        if( $type == 'p' ){
            $task['task_start_date'] = strtotime($task['task_start_date'] . ' 00:00:00');
            $task['task_end_date'] = strtotime($task['task_end_date'] . ' 00:00:00');
        }
        $newStart = $wl['min_date'] < $task['task_start_date'] ? $wl['min_date'] : $task['task_start_date'];
        $newEnd = $wl['max_date'] > $task['task_end_date'] ? $wl['max_date'] : $task['task_end_date'];
        //save task
        $this->$Model->id = $taskId;
        $this->$Model->save(array(
            'task_start_date' => $type == 'p' ? date('Y-m-d', $newStart) : $newStart,
            'task_end_date' => $type == 'p' ? date('Y-m-d', $newEnd) : $newEnd,
            'estimated' => $wl['workload']
        ));

        //adjust employee refers
        $Model .= 'EmployeeRefer';
        $wl = $this->NctWorkload->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                $taskField => $taskId
            ),
            'fields' => array('SUM(estimated) as workload', 'reference_id', 'is_profit_center'),
            'group' => array('reference_id', 'is_profit_center')
        ));
        foreach($wl as $data){
            $d = $data['NctWorkload'];
            $record = $this->$Model->find('first', array(
                'recusive' => -1,
                'conditions' => array(
                    $taskField => $taskId,
                    'reference_id' => $d['reference_id'],
                    'is_profit_center' => $d['is_profit_center']
                )
            ));
            if( !empty($record) ){
                $this->$Model->id = $record[$Model]['id'];
            } else {
                $this->$Model->create();
            }
            $this->$Model->save(array(
                $taskField => $taskId,
                'reference_id' => $d['reference_id'],
                'is_profit_center' => $d['is_profit_center'],
                'estimated' => $data[0]['workload']
            ));
        }
        // tinh workload total the task.
        if( $type == 'p'){
            $sum = $this->NctWorkload->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $taskId
                ),
                'fields' => array('project_task_id', 'sum_workload'),
                'group' => array('project_task_id')
            ));
        }else{
            $sum = $this->NctWorkload->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => 0,
                    'activity_task_id' => $taskId
                ),
                'fields' => array('project_task_id', 'sum_workload'),
                'group' => array('project_task_id')
            ));
        }
        // buil lai du lieu
        $sum = $sum['NctWorkload']['sum_workload'];
        $result['sum_wl'] = $sum;
        return($result);
    }
    public function plus(){
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        if (!empty($this->params['url'])) {
            $company_id = $this->employee_info['CompanyEmployeeReference']['company_id'];
            $employee_id = $this->employee_info['Employee']['id'];
            $this->loadModels('ProfitCenter', 'Employee', 'Project', 'ProjectTask', 'ProjectPhasePlan', 'ActivityRequest', 'ProjectPhase', 'ProjectTaskEmployeeRefer', 'ProjectAmrProgram', 'ProjectAmrSubProgram', 'ProjectStatus', 'ProjectPriority', 'NctWorkload', 'ActivityTask', 'Translation','TranslationSetting');
            $listAllTeams = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id
                ),
                'fields' => array('id','name')
            ));
			$PCModel = ClassRegistry::init('ProfitCenter');
			$isAdmin = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr' || $this->employee_info['Employee']['is_sas'];
			$listPC = array();
			// $profits = $PCModel->find('list', array(
				// 'recursive' => -1,
				// 'conditions' => array(
					// 'company_id' => $company_id,
					// 'OR' => array(
						// 'manager_id' => $employee_id,
						// 'manager_backup_id' => $employee_id,
					// ),
				// ),
				// 'fields' => array('id')
			// ));
	
			if ($isAdmin) {
					$listPC = $PCModel->generateTreeList(array('company_id' => $this->employee_info['Company']['id']),null,null,' -- ',-1);
			} else{
				$pdcBKProfit = ClassRegistry::init('ProfitCenterManagerBackup')->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
						'employee_id' => $employee_id
					),
					'fields' => array('profit_center_id', 'profit_center_id')
				));
				$pdcProfit = ClassRegistry::init('ProfitCenter')->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
						'manager_id' => $employee_id,
					),
					'fields' => array('id')
				));
				$pdcProfit = array_unique(array_merge($pdcProfit, $pdcBKProfit));
				$pathsTemp = array();
				foreach ( $pdcProfit as $pr){
					$pcChild = $PCModel->children($pr);
					if(!empty($pcChild)){
						$pcChild = Set::classicExtract($pcChild,'{n}.ProfitCenter.id');
						$pathsTemp = array_merge($pathsTemp, $pcChild);
					}
				}	
				$pdcProfit = array_unique(array_merge($pdcProfit, $pathsTemp));
				
				$listPC = $PCModel->generatetreelist(array('id' => $pdcProfit), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
			}
				
            $listTeamIds = $listTeam = array();
            if(!empty($this->params['url']['teamP'])){
                foreach ($this->params['url']['teamP'] as $_id) {
					if( isset( $listPC[$_id])){
						$listTeamIds[$_id] = $_id;
						$listTeam[$_id] = $listAllTeams[$_id];
					}
                }
            } else {
                $listTeamIds = array_keys($listPC );
				foreach( $listTeamIds as $_id){
					$listTeam[$_id] = $listAllTeams[$_id];
				}
            }
            $listEmployee = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'company_id' => $company_id,
                        'company_id IS NULL',
                        'profit_center_id' => $listTeamIds
                    )
                ),
                'fields' => array('id', 'fullname')
            ));
            $listEmployeeIds = array_keys($listEmployee);
            $listProjectPrograms = $this->ProjectAmrProgram->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'amr_program'),
                'conditions' => array(
                    'company_id' => $company_id
                )
            ));
            $listProjectSubPrograms = $this->ProjectAmrSubProgram->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'amr_sub_program'),
                'conditions' => array(
                    'project_amr_program_id' => array_keys($listProjectPrograms)
                )
            ));
            $listProjectStatus= $this->ProjectStatus->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array(
                    'company_id' => $company_id
                )
            ));
            $listProjectPriorities = $this->ProjectPriority->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'priority'),
                'conditions' => array(
                    'company_id' => $company_id
                )
            ));
            //_period.
            $startDate = $start_date = '01-' . $this->params['url']['spmonth'] . '-' . $this->params['url']['spyear'];
            $_startDate = strtotime($startDate);
            $_startDate = $_sDate = strtotime('first day of this month', $_startDate);
            $endDate = '01-' . $this->params['url']['epmonth'] . '-' . $this->params['url']['epyear'];
            $_endDate = strtotime($endDate);
            $_endDate = strtotime('last day of this month', $_endDate);
            $endDate = $end_date = date('d-m-Y', $_endDate);
            $_period = array();
            while($_startDate <= $_endDate){
                $_period[] = date('Y-m', $_startDate);
                $_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate));
            }
            // get rawPjList.
            $listTasksIds = $this->ProjectTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'fields' => array('project_task_id', 'project_task_id'),
                'conditions' => array(
                    'OR' => array(
                        array(
                            'reference_id' => $listEmployeeIds,
                            'is_profit_center' => 0
                        ),
                        array(
                            'reference_id' => $listTeamIds,
                            'is_profit_center' => 1
                        )
                    )
                )
            ));
            $listProjectIds = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'fields' => array('project_id', 'project_id'),
                'conditions' => array(
                    'ProjectTask.id' => $listTasksIds,
                    'ProjectTask.id IS NOT NULL',
                    'is_nct' => 1
                )
            ));
            $conditions = array(
                'company_id' => $company_id,
                'Project.id' => $listProjectIds,
                'category' => array(1,3)
            );
            if(!empty($this->params['url']['teamPrio'])){
                $conditions['project_priority_id'] = $this->params['url']['teamPrio'];
            }
            $listProject = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('id', 'project_name', 'project_code_1', 'project_manager_id', 'project_amr_program_id', 'project_amr_sub_program_id', 'project_priority_id', 'start_date', 'end_date')
            ));
            $projectName = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id' , '{n}.Project.project_name') : array();
            $listProjectIds = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id' , '{n}.Project.id') : array();
            $stringPj = '';
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            foreach ($listProject as $key => $value) {
                $dx = $value['Project'];
                $stringPj .= $dx['id'] . ';' . $dx['project_name'] . ';' . $dx['project_code_1']
                            . ';' . ( !empty( $listEmployee[$dx['project_manager_id']] ) ? $listEmployee[$dx['project_manager_id']] : '')
                            . ';' . (!empty($listProjectPrograms[$dx['project_amr_program_id']]) ? $listProjectPrograms[$dx['project_amr_program_id']] : ' ')
                            . ';' . (!empty($listProjectSubPrograms[$dx['project_amr_sub_program_id']]) ? $listProjectSubPrograms[$dx['project_amr_sub_program_id']] : ' ')
                            . ';' . (!empty($listProjectPriorities[$dx['project_priority_id']]) ? $listProjectPriorities[$dx['project_priority_id']] : '')
                            . ';' . (!empty($listProjectSubPrograms[$dx['project_amr_sub_program_id']]) ? $listProjectSubPrograms[$dx['project_amr_sub_program_id']] : ' ')
                            . ';' . $str_utility->convertToVNDate($dx['start_date']) . ';' . $str_utility->convertToVNDate($dx['end_date']) . "\n";
            }
            // get rawData.
            $listTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'task_title', 'project_planed_phase_id', 'project_id'),
                'conditions' => array('project_id' => $listProjectIds)
            ));
            $listTaskTitle = !empty($listTasks) ? Set::combine($listTasks, '{n}.ProjectTask.id' , '{n}.ProjectTask.task_title') : array();
            $listTaskPhase = !empty($listTasks) ? Set::combine($listTasks, '{n}.ProjectTask.id' , '{n}.ProjectTask.project_planed_phase_id') : array();
            $listPjIdOfTask = !empty($listTasks) ? Set::combine($listTasks, '{n}.ProjectTask.id' , '{n}.ProjectTask.project_id') : array();
            $listTaskIds = !empty($listTasks) ? Set::combine($listTasks, '{n}.ProjectTask.id' , '{n}.ProjectTask.id') : array();
            $projectPhase = $this->ProjectPhase->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array('company_id' => $company_id)
            ));
            $projectPhasePlans = $this->ProjectPhasePlan->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_planed_phase_id'),
                'conditions' => array('ProjectPhasePlan.id' => $listTaskPhase)
            ));
            $rawData = $assignWorkloads = $assignConsume = array();
            $listPJTaskIdOfTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_task_id'),
                'conditions' => array('project_task_id' => $listTaskIds)
            ));
            // lay list task_id of Pj de build all consume(Nct+task thuong).
            $listTaskIdOfPjTaskId = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'project_task_id'),
                'conditions' => array('project_task_id' => $listTaskIds)
            ));
            $_totalConsumeOfPj = $consumeCapa = array();
            foreach ($listTeam as $pcId => $pcName) {
                // doan nay lay task cua tung profit center.
                $listEmOfTeamIds = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'id'),
                    'conditions' => array(
                        'company_id' => $company_id,
                        'profit_center_id' => $pcId
                    )
                ));
                $conditions = array(
                    'task_date >= ' => date('Y-m-d', strtotime($startDate)),
                    'end_date <=' => date('Y-m-d', $_endDate),
                    'AND' => array(
                        'OR' => array(
                            array(
                                'reference_id' => $listEmOfTeamIds,
                                'is_profit_center' => 0
                            ),
                            array(
                                'reference_id' => $pcId,
                                'is_profit_center' => 1
                            )
                        )
                    ),
                    'group_date LIKE' => '2%'
                );
                $listNctTasks = $this->NctWorkload->find('all', array(
                    'recursive' => -1,
                    'fields' => array('*'),
                    'conditions' => $conditions
                ));
                //lay consume theo task
                $this->ActivityRequest->virtualFields['month_date'] = 'FROM_UNIXTIME(ActivityRequest.date, "%m-%Y")';
                $this->ActivityRequest->virtualFields['consumed'] = 'SUM(ActivityRequest.value)';
                //doan nay lay consume.
                $listConsume = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'employee_id' => $listEmOfTeamIds,
                        'status' => 2,
                        'value !=' => 0,
                        // 'date BETWEEN ? AND ?' => array($start, $end)
                    ),
                    'fields' => array('month_date', 'consumed', 'task_id', 'employee_id'),
                    'group' => array('task_id', 'employee_id', 'month_date')
                ));
                $_consume = $consume = $totalConsumeOfPj = array();
                foreach ($listConsume as $value) {
                    $dx = $value['ActivityRequest'];
                    $_date = explode('-', $dx['month_date']);
                    $date = $_date[1] . '-' . $_date[0];
                    if(empty($_consume[ $dx['task_id'] ][ $dx['month_date'] ])){
                        $_consume[ $dx['task_id'] ][ $dx['month_date'] ] = 0;
                    }
                    $_consume[ $dx['task_id'] ][ $dx['month_date'] ] += $dx['consumed'];
                    // total consume of pj (task thuong + Nct).
                    if(isset($listTaskIdOfPjTaskId[$dx['task_id']]) && isset($listPjIdOfTask[ $listTaskIdOfPjTaskId[$dx['task_id']] ])){
                        if(empty($totalConsumeOfPj[ $listPjIdOfTask[ $listTaskIdOfPjTaskId[$dx['task_id']] ] ][ $dx['month_date'] ])){
                            $totalConsumeOfPj[ $listPjIdOfTask[ $listTaskIdOfPjTaskId[$dx['task_id']] ] ][ $dx['month_date'] ] = 0;
                        }
                        $totalConsumeOfPj[ $listPjIdOfTask[ $listTaskIdOfPjTaskId[$dx['task_id']] ] ][ $dx['month_date'] ] += $dx['consumed'];
                    }
                    // consume for employee.
                    if(empty($listPJTaskIdOfTasks[$dx['task_id']])) continue;
                    if(empty($assignConsume[$pcId][ $listPJTaskIdOfTasks[$dx['task_id']] ][ $date ][ $dx['employee_id'] ])){
                        $assignConsume[$pcId][ $listPJTaskIdOfTasks[$dx['task_id']] ][ $date ][ $dx['employee_id'] ] = 0;
                    }
                    $assignConsume[$pcId][ $listPJTaskIdOfTasks[$dx['task_id']] ][ $date ][ $dx['employee_id'] ] += $dx['consumed'];
                }
                $_starts = $_sDate;
                // consume = 0. truong hop task k có consume
                $_consu = '';
                $zeroCapa = $zeroCsCapa = array();
                while($_starts < $_endDate){
                    $_consu .= ' ;';
                    $zeroCapa[$_starts] = 0;
                    $zeroCsCapa[$_starts] = 0;
                    $_starts = mktime(0, 0, 0, date("m", $_starts)+1, date("d", $_starts), date("Y", $_starts));
                }
                // total consumed.
                $totalCsCapa = 0;
                foreach ($totalConsumeOfPj as $_pjid => $value) {
                    $_starts = $_sDate;
                    $_t = 0;
                    while($_starts < $_endDate){
                        $date = date('m-Y', $_starts);
                        if(empty($consumeCapa[$pcName][$date])){
                            $consumeCapa[$pcName][$date] = 0;
                        }
                        if( !empty($value[$date]) ){
                            $_totalConsumeOfPj[ $pcName ][ $_pjid ][$date] = $value[$date];
                            $consumeCapa[$pcName][$date] += $value[$date];
                            $_t += $value[$date];
                            $totalCsCapa += $value[$date];
                        } else {
                            $_totalConsumeOfPj[ $pcName ][ $_pjid ][$date] = 0;
                        }
                        $_starts = mktime(0, 0, 0, date("m", $_starts)+1, date("d", $_starts), date("Y", $_starts));
                    }
                    //reset key.
                    $_totalConsumeOfPj[ $pcName ][ $_pjid ] = array_values($_totalConsumeOfPj[ $pcName ][ $_pjid ]);
                    $_totalConsumeOfPj[ $pcName ][ $_pjid ]['total'] = $_t;
                }
                $consumeCapa[$pcName] = !empty($consumeCapa[$pcName]) ? array_values($consumeCapa[$pcName]) : array_values($zeroCsCapa);
                $consumeCapa[$pcName]['total'] = $totalCsCapa;
                $listActivityTaskIds = array();
                foreach ($_consume as $task_id => $value) {
                    $_starts = $_sDate;
                    $listActivityTaskIds[$task_id] = $task_id;
                    while($_starts < $_endDate){
                        if(empty($consume[ $task_id ])){
                            $consume[ $task_id ] = '';
                        }
                        $date = date('m-Y', $_starts);
                        if( !empty($value[$date]) ){
                            $consume[ $task_id ] .= $value[$date] . ';';
                        } else {
                            $consume[ $task_id ] .= ' ;';
                        }
                        $_starts = mktime(0, 0, 0, date("m", $_starts)+1, date("d", $_starts), date("Y", $_starts));
                    }
                }
                $listAcIdOfTaskIds = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_task_id', 'id'),
                    'conditions' => array('ActivityTask.id' => $listActivityTaskIds)
                ));
                // workload
                $_workloads = $workloads = array();
                foreach ($listNctTasks as $value) {
                    $dx = $value['NctWorkload'];
                    if( empty($_workloads[ $dx['project_task_id'] ][ date('m-Y', strtotime($dx['task_date'])) ]) ){
                        $_workloads[ $dx['project_task_id'] ][ date('m-Y', strtotime($dx['task_date'])) ] = 0;
                    }
                    $_workloads[ $dx['project_task_id'] ][ date('m-Y', strtotime($dx['task_date'])) ] += $dx['estimated'];
                    // workload cho tung nguoi.
                    if( empty($assignWorkloads[$pcId][ $dx['project_task_id'] ][ date('Y-m', strtotime($dx['task_date'])) ][ $dx['is_profit_center'] . '-' . $dx['reference_id'] ]) ){
                        $assignWorkloads[$pcId][ $dx['project_task_id'] ][ date('Y-m', strtotime($dx['task_date'])) ][ $dx['is_profit_center'] . '-' . $dx['reference_id'] ] = 0;
                    }
                    $assignWorkloads[$pcId][ $dx['project_task_id'] ][ date('Y-m', strtotime($dx['task_date'])) ][ $dx['is_profit_center'] . '-' . $dx['reference_id'] ] += $dx['estimated'];
                }
                // get Ref data.
                $RefOfTaskIds = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $listTaskIds),
                    'fields' => array('id', 'initial_estimated', 'task_start_date', 'task_end_date')
                ));
                $RefOfTaskIds = !empty($RefOfTaskIds) ? Set::combine($RefOfTaskIds, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
                $_raw = '';
                foreach ($_workloads as $task_id => $value) {
                    $_starts = $_sDate;
                    while($_starts < $_endDate){
                        if(empty($workloads[ $task_id ])){
                            $workloads[ $task_id ] = '';
                        }
                        $date = date('m-Y', $_starts);
                        if( !empty($value[$date]) ){
                            $workloads[ $task_id ] .= $value[$date] . ';';
                        } else {
                            $workloads[ $task_id ] .= ' ;';
                        }
                        $_starts = mktime(0, 0, 0, date("m", $_starts)+1, date("d", $_starts), date("Y", $_starts));
                    }
                    // buil raw Data
                    $_raw .= $task_id . ';' . ( !empty($listPjIdOfTask[$task_id]) && !empty($projectName[$listPjIdOfTask[$task_id]]) ? $projectName[$listPjIdOfTask[$task_id]] : ' ')
                        . ';' . ( (!empty($listTaskPhase[$task_id]) && !empty($projectPhasePlans[ $listTaskPhase[$task_id] ]) && !empty($projectPhase[ $projectPhasePlans[ $listTaskPhase[$task_id] ] ]) ) ? $projectPhase[ $projectPhasePlans[ $listTaskPhase[$task_id] ] ] : ' ')
                        . ';' . ( !empty($listTaskTitle[ $task_id ]) ? $listTaskTitle[ $task_id ] : ' ' ) . ';' . $pcId . ';' . (!empty($RefOfTaskIds[$task_id]['initial_estimated']) ? $RefOfTaskIds[$task_id]['initial_estimated'] : 0)
                        . ';' . (!empty($RefOfTaskIds[$task_id]) && !empty($RefOfTaskIds[$task_id]['task_start_date']) ? $RefOfTaskIds[$task_id]['task_start_date'] : '')
                        . ';' . (!empty($RefOfTaskIds[$task_id]) && !empty($RefOfTaskIds[$task_id]['task_end_date']) ? $RefOfTaskIds[$task_id]['task_end_date'] : '')
                        . ';' . $workloads[ $task_id ]
                        . ';' . (!empty($listAcIdOfTaskIds[$task_id]) && !empty($consume[ $listAcIdOfTaskIds[$task_id] ]) ? $consume[ $listAcIdOfTaskIds[$task_id] ] : $_consu) . "\n";
                }
                $rawData[$pcName] = $_raw;
            }
			$workdays = $this->requestAction('/project_tasks/getWorkdays');
			$priorities = $this->ProjectPriority->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
                )
			));
			$projectStatus = $this->ProjectStatus->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
                )
			));
			$this->loadModel('Profile');
			$profiles = $this->Profile->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
                )
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
            $this->set(compact('profiles','projectStatus','priorities','workdays','listTeam', '_period', 'stringPj', 'rawData', 'employee_id', 'listEmployee', 'assignWorkloads', 'assignConsume', 'listTeamIds', 'start_date', 'end_date', 'zeroCapa', '_totalConsumeOfPj', 'consumeCapa', 'adminTaskSetting'));
        }
    }
    public function getTxtOfTask(){
        if(!empty($_POST)){
            $taskId = $_POST['id'];
            $idEmployee = $this->employee_info['Employee']['id'];
            $this->loadModel('ProjectTaskTxt');
            $comments = $this->ProjectTaskTxt->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $taskId)
            ));
            $listComments = !empty($comments) ? Set::combine($comments, '{n}.ProjectTaskTxt.id', '{n}.ProjectTaskTxt') : array();
            $listIdEm = !empty($comments) ? array_unique(Set::classicExtract($comments, '{n}.ProjectTaskTxt.employee_id')) : array();
            $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id' => $listIdEm
                ),
                'fields' => array('id', 'avatar', 'first_name', 'last_name')
            ));
            $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
            $data = $_employee = array();
            foreach ($listComments as $_comment) {
                $_id = $_comment['employee_id'];
                $_comment['employee_id'] = $employees[$_id];
                $data[] = $_comment;
            } 
            die(json_encode($data));
        }
        exit;
    }
    public function saveTxtOfTask(){
        if(!empty($_POST)){
            $taskId = $_POST['id'];
            $content = $_POST['content'];
            $this->loadModel('ProjectTaskTxt');
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
            $result['name'] = $this->employee_info['Employee']['fullname'];
            $time = date('Y-m-d H:i:s');
            $this->ProjectTaskTxt->create();
            $this->ProjectTaskTxt->save(array(
                'project_task_id' => $taskId,
                'employee_id' => $employee,
                'comment' => $content,
                'created' => $time
            ));
            $result['content'] =  $content;
            $result['employee_id'] = $employee;
            $result['time'] = $time;
            die(json_encode($result));
        }
        die;
    }
}
?>
