<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectStaffingsController extends AppController {

    var $uses = array();
	var $components = array("Lib", 'CommonExporter');
    function beforeFilter() {
        parent::beforeFilter('getVocationDetailByMonth');
        $this->Auth->autoRedirect = false;
    }

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectStaffings';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Gantt', 'GanttSt','GanttV2');
    /**
     * Vision gantt chart
     *
     * @return void
     * @access public
     */

	//FIX BUG APPLY is_staffing FOR STAFFING SYSTEM
	var $projectIdCurrent = null;
    function vision($project_id = null) {
        $this->_checkRole(false, $project_id);
        $this->loadModel('ProjectPhasePlan');
        unset($this->helpers['Gantt']);
        //$this->helpers['GanttV2'] = null;
        //$this->helpers['GanttST'] = null;


        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $this->set('projectName', $this->ProjectPhasePlan->Project->find("first", array(
                    'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'order' => 'milestone_date ASC')),
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
        $parts = $this->ProjectPhasePlan->ProjectPart->find('list');
        $getDatas = $this->_taskCaculates($project_id, $projectName['Project']['company_id']);
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
        $this->set(compact('phasePlans', 'display', 'project_id', 'parts','onClickPhaseIds'));
    }

    /**
     * Vision gantt chart, view staffings+
     *
     * @return void
     * @access public
     */
    public function visions($project_id = null, $category = null) {
		// debug(date('h:m:s', time()));
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            if( !((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))){
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview =  trim( str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_').'_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/'.$value;
                }
                if( isset($_url_param['url'])) unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            }
        }
        $this->loadModel('EmployeeMultiResource');
        $employee_info = $this->employee_info;
        $ratio = !empty($employee_info['Company']['ratio']) ? $employee_info['Company']['ratio'] : 1;
        // debug($category); exit;
		if(empty($this->companyConfigs['activate_profile']) && $category == 'profile'){ 	$category = 'employee' ;
		}
		$this->projectIdCurrent = $project_id;
        $this->_checkRole(false, $project_id);
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('Project');
        $this->loadModel('ProjectTeam');
        unset($this->helpers['Gantt']);
        $this->helpers['GanttSt'] = null;
        //edit by thach 2014-01-16
		// debug(date('h:m:s', time()));
        $projectName = $this->ProjectPhasePlan->Project->find("first", array(
                    'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'order' => 'milestone_date ASC')),
                    "fields" => array('*'), 'conditions' => array('Project.id' => $project_id)));
					
        $this->set('projectName', $projectName);
        //quyet modified this
        $dt = new DateTime($projectName['Project']['start_date']);
        $dt->modify('-1 month');
        $dt->modify('first day of this month');
        $dt->setTime(0, 0, 0);
        $projectStart = $dt->getTimestamp();
        $dt = new DateTime($projectName['Project']['end_date']);
        $dt->modify('+1 month');
        $dt->modify('last day of this month');
        $dt->setTime(0, 0, 0);
        $projectEnd = $dt->getTimestamp();
// debug(date('h:m:s', time()));
        $getDatas = $this->_taskCaculates($project_id, $projectName['Project']['company_id']);
		// debug(date('h:m:s', time()));
        $taskCompleted = $getDatas['part'];
        $phaseCompleted = $getDatas['phase'];
        $projectTasks = $getDatas['task'];
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $options = array(
            'limit' => 2000,
            'contain' => array(
                'ProjectPhase' => array('name', 'color')
            ),
            'order' => array('ProjectPhasePlan.weight' => 'asc'),
            'conditions' => array('ProjectPhasePlan.project_id' => $project_id));
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $phasePlans = $this->ProjectPhasePlan->find('all', $options);
        $parts = $this->ProjectPhasePlan->ProjectPart->find('list');
        //change 05/02/2017
        $this->loadModel('CompanyConfigs');
        $displayTeamPlus = $this->CompanyConfigs->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $employee_info['Company']['id'],
                'cf_name' => 'display_staffing_team_plus'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $displayTeamPlus = !empty($displayTeamPlus) ? $displayTeamPlus['CompanyConfigs']['cf_value'] : 0;
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
        $projectTeams = $this->ProjectTeam->find("all", array(
            'fields' => array('id', 'price_by_date', 'work_expected', 'project_function_id', 'profit_center_id'),
            'contain' => array(
                'ProjectFunctionEmployeeRefer' => array(
                    'fields' => array('is_backup', 'profit_center_id', 'employee_id')
            )),
            "conditions" => array('project_id' => $project_id)));
        $teamEmployees = array();
        if(!empty($projectTeams)){
            foreach($projectTeams as $projectTeam){
                $dxs = $projectTeam['ProjectFunctionEmployeeRefer'];
                if(!empty($dxs)){
                    foreach($dxs as $dx){
                        if( !empty($dx['employee_id']) && !empty($dx['vtual_create_by_firstname']) ){
                            if(!isset($teamEmployees[$dx['employee_id']])){
                                $teamEmployees[$dx['employee_id']] = '';
                            }
                            $teamEmployees[$dx['employee_id']] = $dx['vtual_create_by_firstname'] . ' ' . $dx['vtual_create_by_lastname'];
                        }
                    }
                }
            }
        }
        $startEmployees = $endEmployees = 0;
        if($category == 'employee'){
            $staffingss = $this->_staffingEmloyee($project_id);
		
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
            if(!empty($teamEmployees)){
                foreach($teamEmployees as $id => $name){
                    if(in_array($id, $employeeIds)){
                        //do nothing
                    } else {
                        $addEmployeeInTeams[] = array(
                            'id' => $id,
                            'is_check' => 1,
                            'name' => $name,
                            'data' => array()
                        );
                        $employeeIds[] = $id;
                    }
                }
                if(!empty($addEmployeeInTeams)){
                    $staffingss = array_merge($addEmployeeInTeams, $staffingss);
                }
            }
			
            if( !empty($startDateFilter) && !empty($endDateFilter)){
                list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDateFilter, $endDateFilter);
				$totalWorkloadNotAffected = $this->totalWorkloadNotAffected($project_id, $startDateFilter, $endDateFilter);
            } else {
                $totalWorkloads = array();
                $totalWorkloadNotAffected = array();
                $capacities = array();
            }
			// exit;
            $mrCapacity = array();
            $listMR = $this->EmployeeMultiResource->isMultipleResources($employeeIds);
            $mrCapacity = $this->EmployeeMultiResource->getCapacity($listMR, $projectStart, $projectEnd);

            foreach($staffingss as $key => $staffing){
                foreach($capacities as $id => $capacity){
                    foreach($capacity as $times => $value){
						if($times <= $endDateFilter){
							if($staffing['id'] == $id){
								// 2016-11-04 sua capacity and adsence, woking theo ratio.
								if( in_array($id, $listMR) ){
									$staffingss[$key]['data'][$times]['capacity'] = isset($mrCapacity[$id][$times]) ? $mrCapacity[$id][$times]*$ratio : 0;
								} else {
									$staffingss[$key]['data'][$times]['capacity'] = $value['capacity']*$ratio;
								}
								$staffingss[$key]['data'][$times]['working'] = $value['working']*$ratio;
								$staffingss[$key]['data'][$times]['absence'] = $value['absence']*$ratio;
								$staffingss[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
								if($staffing['id'] == '999999999'){
									$staffingss[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloadNotAffected[$id][$times]) ? $totalWorkloadNotAffected[$id][$times] : 0;
								}else{
									$staffingss[$key]['data'][$times]['totalWorkload'] = !empty($totalWorkloads[$id][$times]) ? $totalWorkloads[$id][$times] : 0;
								}
							}
							else if($staffing['id'] == '999999999'){
								$staffingss[$key]['data'][$times]['capacity'] = 0;
								$staffingss[$key]['data'][$times]['working'] = 0;
								$staffingss[$key]['data'][$times]['absence'] = 0;
								$staffingss[$key]['data'][$times]['totalWorkload'] = 0;
							}
						}
                    }
                }
            }
            $showType = false;
            $startEmployees = !empty($startDateFilter) ? mktime(0, 0, 0, date("m", $startDateFilter)-1, date("d", $startDateFilter), date("Y", $startDateFilter)) : '';
            $endEmployees = !empty($endDateFilter) ? mktime(0, 0, 0, date("m", $endDateFilter), date("d", $endDateFilter), date("Y", $endDateFilter)) : '';
            $startEmployees = !empty($startEmployees) ? explode('-', date('d-m-Y', $startEmployees)) : array();
            $endEmployees = !empty($endEmployees) ? explode('-', date('t-m-Y', $endEmployees)) : array();
        } elseif($category == 'profit' || $category == 'profit_plus'){
			// debug(date('h:m:s', time()));
            $staffingss = $this->_staffingProfitCenter($project_id);

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
			// debug(date('h:m:s', time()));
            if( !empty($startDateFilter) && !empty($endDateFilter)){
                list($totalEmployees, $capacities, $totalWorkloads) = $this->_capacityFromForecast($profitCenterIds, $startDateFilter, $endDateFilter);
            } else {
                $totalEmployees = array();
                $capacities = array();
                $totalWorkloads = array();
            }
			// debug(date('h:m:s', time()));
            //pr($listEmployees);
            //
            // $mrCapacity = array();
            // $listMR = $this->EmployeeMultiResource->isMultipleResources($listEmployees);
            // $mrCapacity = $this->EmployeeMultiResource->getCapacity($listMR, $projectStart, $projectEnd);

            foreach($staffingss as $key => $staffing){
                foreach($capacities as $id => $capacity){
                    foreach($capacity as $times => $value){
                        if($staffing['id'] == $id){
                            // 2016-11-04 sua capacity and adsence, woking theo ratio.
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
            $showType = ($category == 'profit_plus') ? 3 : 1;

        } elseif ($category == 'profile'){
            $this->loadModel('TmpStaffingSystem');
            $employeeName = $this->_getEmpoyee();
            $staffingss = $this->TmpStaffingSystem->staffingProfile($project_id,$employeeName);
            $showType = 2;
        // }elseif($category == 'profit_plus') {
        //     $staffingss = $this->_staffingProfitCenter($project_id);
        //     debug($staffingss);
        //     exit;
        }else{
            $staffingss = $this->_staffingFunction($project_id);
            $showType = 2;
        }
        $this->loadModel('Project');

        $project = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('activity_id')
            ));
        $activityId = Set::classicExtract($project, 'Project.activity_id');
        $employeeName = $this->_getEmpoyee();
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
        $showAllPicture = $this->CompanyConfig->find('first', array(
            'recursive' => 1,
            'conditions' => array(
                'company' => $this->employee_info['Company']['id'],
                'cf_name' => 'display_picture_all_resource'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showAllPicture = !empty($showAllPicture) ? $showAllPicture['CompanyConfig']['cf_value'] : 0;
        // lay engeged.
        $budgetMdTeams = $freezeTeams = $_freezeTeams = array();
        $this->loadModels('ProjectBudgetInternalDetail', 'ProfitCenter','ProjectTask', 'ProjectTaskEmployeeRefer');
        if($category == 'profit_plus'){
            $this->ProjectBudgetInternalDetail->virtualFields['budget'] = 'SUM(ProjectBudgetInternalDetail.budget_md)';
            $_budgetMdTeams = $this->ProjectBudgetInternalDetail->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('profit_center_id', 'budget'),
                'group' => array('profit_center_id')
            ));
            $total = 0;
            foreach ($_budgetMdTeams as $_pid => $value) {
                $total += $value;
                $temp = $this->ProfitCenter->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProfitCenter.parent_id' => $_pid),
                    'fields' => array('id', 'id')
                ));
                if(!empty($temp)){
                    $_v = 'sum-' . $_pid;
                    $budgetMdTeams[$_v] = 0;
                    $budgetMdTeams[$_v] += $value;
                    foreach($temp as $_id) {
                        $budgetMdTeams[$_v] += !empty($_budgetMdTeams[$_id]) ? $_budgetMdTeams[$_id] : 0;
                    }
                    $budgetMdTeams[$_v] = number_format($budgetMdTeams[$_v], 2);
                }
                $budgetMdTeams[$_pid] = number_format($value, 2);;
            }
            $budgetMdTeams['summary'] = number_format($total, 2);
            //get freeze(initial workload).
            $total = 0;
            foreach ($projectTasks as $projectTask) {
                $id = explode('-', $projectTask['id']);
                // check xem co phai task do assign cho 1 team khong. nhieu thi loai.
                $count = $this->ProjectTaskEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id[1],
                        'is_profit_center' => 1
                    ),
                    'fields' => array('id', 'reference_id')
                ));
                if( count($count) > 1 ) continue;
                if( count($count) != 0){
                    $teamId = array_shift($count);
                } else {
                    // doan nay check xem truong hop khong assign team ma chi assgin resource.
                    $_emId = $this->ProjectTaskEmployeeRefer->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $id[1],
                            'is_profit_center' => 0
                        ),
                        'fields' => array('id', 'reference_id')
                    ));
                    if(empty($_emId)) continue;
                    $_emId = $_emId['ProjectTaskEmployeeRefer']['reference_id'];
                    $teamId = $this->Employee->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $_emId),
                        'fields' => array('id', 'profit_center_id')
                    ));
                    $teamId = !empty($teamId) ? $teamId['Employee']['profit_center_id'] : 0;
                }
                // check xem task do co assign cho resource khac team khong. neu co loai.
                $emOfPc = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'profit_center_id' => $teamId
                    ),
                    'fields' => array('id', 'id')
                ));
                $assignEm = $this->ProjectTaskEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id[1],
                        'is_profit_center' => 0
                    ),
                    'fields' => array('id', 'reference_id')
                ));
                $check = false;
                foreach ($assignEm as $emId) {
                    if(!in_array($emId, $emOfPc)){
                        $check = true;
                    }
                }
                if ($check == true) continue;
                // tinh freeze tuc la initial_estimated.
                if(empty($_freezeTeams[$teamId])) $_freezeTeams[$teamId] = 0;
                $_freezeTeams[$teamId] += $projectTask['initial_estimated'];
            }
            $total = 0;
            foreach ($_freezeTeams as $_pid => $value) {
                $total += $value;
                $temp = $this->ProfitCenter->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProfitCenter.parent_id' => $_pid),
                    'fields' => array('id', 'id')
                ));
                if(!empty($temp)){
                    $_v = 'sum-' . $_pid;
                    $freezeTeams[$_v] = 0;
                    $freezeTeams[$_v] += $value;
                    foreach($temp as $_id) {
                        $freezeTeams[$_v] += !empty($_freezeTeams[$_id]) ? $_freezeTeams[$_id] : 0;
                    }
                    $freezeTeams[$_v] = number_format($freezeTeams[$_v], 2);
                }
                $freezeTeams[$_pid] = number_format($value, 2);
            }
            $freezeTeams['summary'] = number_format($total, 2);
        }
        // end engeged
        $this->set(compact('startEmployees', 'endEmployees','phaseCompleted', 'projectTasks', 'taskCompleted','onClickPhaseIds', 'showAllPicture', 'startDateFilter', 'endDateFilter'));
        $this->set('staffingCate', $category);
        $this->set(compact('phasePlans', 'display', 'project_id', 'parts', 'staffingss', 'showType', 'minMax', 'dateStaffing', 'employee_info', 'budgetMdTeams', 'freezeTeams', 'displayTeamPlus'));
        // pr($staffingss);
        // die;
        //CHECK REBUILD STAFFING
        $lib = new LibBehavior();
		// debug(date('h:m:s', time()));
			// exit;
        $rebuildStaffing = $lib->checkRebuildStaffing('Project',$project_id);
        $this->set('rebuildStaffing', $rebuildStaffing);
        //END
    }
	public function totalWorkloadNotAffected($project_id, $startDate, $endDate){
		// New request 
		// For non affected , take into account only the workload none affected of the project no all the projects
		$totalWorkloads = array();
		$this->loadModel('TmpStaffingSystem');
		/**
		 * Lay du lieu staffing cho employee
		 */
		 $conditions = array(
			'model' => 'employee',
			'model_id' => '999999999',
			'date BETWEEN ? AND ?' => array($startDate, $endDate),
			'company_id' => $this->employee_info['Company']['id'],
			'project_id' => $project_id,
		);
	
		$getDatas = $this->TmpStaffingSystem->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions
		));
		if(!empty($getDatas)){
			foreach($getDatas as $getData){
				$dx = $getData['TmpStaffingSystem'];
				if(!isset($totalWorkloads[$dx['model_id']][$dx['date']])){
					$totalWorkloads[$dx['model_id']][$dx['date']] = 0;
				}
				$totalWorkloads[$dx['model_id']][$dx['date']] += $dx['estimated'];
			}
		}
		return $totalWorkloads;
	}
    function checkRebuildStaffing($keyword,$project_id)
    {
        $lib = new LibBehavior();
        $rebuildStaffing = $lib->checkRebuildStaffing($keyword,$project_id);
        echo $rebuildStaffing;
        exit;
    }
    /**
     * To Time
     *
     * @return void
     * @access protected
     */
    protected function _toTime($value) {
        return intval(strtotime($value));
    }
    /**
     * Csv
     *
     * @return void
     * @access protected
     */
    protected function _defaultCsv() {
        return array(
            'type_activity' => __('Type Activity', true),
            'project_code' => __('Project Code', true),
            'project_name' => __('Project Name', true),
            'date' => __('Date', true),
            'profit_center' => __('Profit Center', true),
            'analytical' => __('Analytical', true),
            'employee' => __('Employee ID', true),
            'manday' => __('Man Day', true),
            'type' => __('Type', true),
        );
    }

    /**
     * Phase range date
     *
     * @return void
     * @access protected
     */
    protected function _phaseRange($project_id) {
        $phase = $this->Project->ProjectPhasePlan->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array(
                'MIN(phase_planed_start_date) as start',
                'MAX(phase_planed_end_date) as end',
                'MIN(phase_real_start_date) as rstart',
                'MAX(phase_real_end_date) as rend'
                )));
        $phase = array_map(array($this, '_toTime'), array_shift($phase));
        if ($phase['rstart'] > 0) {
            $phase['start'] = min($phase['start'], $phase['rstart']);
        }
        $phase['end'] = max($phase['end'], $phase['rend']);
        if ($phase['end'] > 0) {
            list($_m, $_y) = explode('-', date('n-Y', $phase['end']));
            $phase['end'] = strtotime("$_y-$_m-" . date("t", $phase['end']));
        }
        return array(
            strtotime(date('Y-n', $phase['start']) . '-1'),
            $phase['end']
        );
    }

    /**
     * export_vision
     * Export vision
     *
     * @return void
     * @access public
     */
    function export_vision() {
        set_time_limit(120);
        $this->layout = false;
        if (!empty($this->data['Export']) && !empty($this->data['Export']['rows'])) {
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
            $this->set(compact('tmpFile', 'height', 'project', 'rows', 'project_id', 'start', 'end', 'months', 'displayFields'));
        } else {
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
    }

    /**
     * export_vision
     * Export vision
     *
     * @return void
     * @access public
     */
    function export_visions() {
		$this->loadModel('HistoryFilter');
        set_time_limit(0);
        $this->layout = false;
        if (!empty($this->data['Export']) && !empty($this->data['Export']['rows'])) {
            extract($this->data['Export']);

            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);

            //$tmpFile = TMP . 'page_' . time() . '.png';
            //file_put_contents($tmpFile, base64_decode($canvas[1]));
            $image = imagecreatefromstring(base64_decode($canvas[1]));

            //list($_width, $_height) = getimagesize($tmpFile);
            $_width = imagesx($image);
            $_height = imagesy($image);

            $crop = imagecreatetruecolor($_width - 69, $height);

            imagecopy($crop, $image, 0, 0, 69, 232, $_width, $height);
            $this->set('image', $crop);
            //imagepng($crop, $tmpFile);

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
            $this->loadModel('ProjectTeam');
            $projectTeams = $this->ProjectTeam->find("all", array(
                'fields' => array('id', 'price_by_date', 'work_expected', 'project_function_id', 'profit_center_id'),
                'contain' => array(
                    'ProjectFunctionEmployeeRefer' => array(
                        'fields' => array('is_backup', 'profit_center_id', 'employee_id')
                )),
                "conditions" => array('project_id' => $project_id)));
            $teamEmployees = array();
            if(!empty($projectTeams)){
                foreach($projectTeams as $projectTeam){
                    $dxs = $projectTeam['ProjectFunctionEmployeeRefer'];
                    if(!empty($dxs)){
                        foreach($dxs as $dx){
                            if(!empty($dx['employee_id'])){
                                if(!isset($teamEmployees[$dx['employee_id']])){
                                    $teamEmployees[$dx['employee_id']] = '';
                                }
                                $teamEmployees[$dx['employee_id']] = $dx['vtual_create_by_firstname'] . ' ' . $dx['vtual_create_by_lastname'];
                            }
                        }
                    }
                }
            }
            if($category == 'employee'){
                $staffingss = $this->_staffingEmloyee($project_id);
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
                if(!empty($teamEmployees)){
                    foreach($teamEmployees as $id => $name){
                        if(in_array($id, $employeeIds)){
                            //do nothing
                        } else {
                            $addEmployeeInTeams[] = array(
                                'id' => $id,
                                'is_check' => 1,
                                'name' => $name,
                                'data' => array()
                            );
                            $employeeIds[] = $id;
                        }
                    }
                    if(!empty($addEmployeeInTeams)){
                        $staffingss = array_merge($addEmployeeInTeams, $staffingss);
                    }
                }
                list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDateFilter, $endDateFilter);
                foreach($staffingss as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                               $staffingss[$key]['data'][$times]['capacity'] = $value['capacity'];
                                $staffingss[$key]['data'][$times]['working'] = $value['working'];
                                $staffingss[$key]['data'][$times]['absence'] = $value['absence'];
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
                $showType = 0;
            } elseif($category == 'profit'){
                $staffingss = $this->_staffingProfitCenter($project_id);
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
                list($totalEmployees, $capacities, $totalWorkloads) = $this->_capacityFromForecast($profitCenterIds, $startDateFilter, $endDateFilter);
                foreach($staffingss as $key => $staffing){
                    foreach($capacities as $id => $capacity){
                        foreach($capacity as $times => $value){
                            if($staffing['id'] == $id){
                                $staffingss[$key]['data'][$times]['capacity'] = $value['capacity'];
                                $staffingss[$key]['data'][$times]['working'] = $value['working'];
                                $staffingss[$key]['data'][$times]['absence'] = $value['absence'];
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
            } else {
                $staffingss = $this->_staffingFunction($project_id);
                $showType = 2;
            }
            $start = mktime(0, 0, 0, date("m", $start)-1, date("d", $start), date("Y", $start));
            // $end = mktime(0, 0, 0, date("m", $end)+1, date("d", $end), date("Y", $end));
            // echo date("Y ", $start);
            // echo date("d-m-Y", $end);

            $this->set(compact('height', 'project', 'rows', 'project_id', 'start', 'end', 'months', 'displayFields', 'staffingss', 'showType'));
        } else {
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
    }

    /**
     * Show staffing
     *
     * View Detail
     *  - project : normal display
     *  - project2 : hidden gantt chart
     *  - project3 : show profit & project and hidden gantt chart
     *  - project4 : show profit & project and show gantt chart
     *  - project5 : show function & project and show gantt chart
     *  - project6 : show function & project and show gantt chart
     *  - project7 : show profit & function and show gantt chart
     *  - project8 : show profit & function and show gantt chart
     *
     * @return void
     * @access public
     */
    public function project() {
        $this->loadModel('Project');
        $default = array(
            'ProjectProjectAmrProgramId' => '',
            'ProjectProjectAmrSubProgramId' => '',
            'ProjectProjectManagerId' => '',
            'ProjectProjectStatusId' => ''
        );
        $conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);
        $keys = array(
            'project_amr_program_id',
            'project_amr_sub_program_id',
            'project_manager_id',
            'project_status_id'
        );
        $conditions = Set::filter(array_combine($keys, $conditions));


        $_filter = array();
        if (!empty($this->params['url']['ProjectProfitCenterId'])) {
            $_filter['profit_center_id'] = $this->params['url']['ProjectProfitCenterId'];
        }
        if (!empty($this->params['url']['ProjectProjectFunctionId'])) {
            $_filter['project_function_id'] = $this->params['url']['ProjectProjectFunctionId'];
        }

        if ($_filter) {
            $_filter['NOT'] = array('project_function_id' => null);
            $conditions['Project.id'] = $this->Project->ProjectTeam->find('list', array('conditions' => $_filter, 'fields' => array('id', 'project_id')));
        }

        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $limit = isset($this->params['url']['limit']) ? (int) $this->params['url']['limit'] : 10;
        $limit = 10000;
        $projects = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array('ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ), 'ProjectPhase' => array('name', 'color'))), 'limit' => $limit, 'fields' => array('project_name', 'start_date', 'end_date', 'planed_end_date'));
        if ($this->is_sas)
            $projects = $this->Project->find('all', $this->paginate);
        else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
                $projects = $this->paginate('Project', array('Project.company_id' => $sub_companies));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('list', array('recursive' => -1, 'fields' => array('id', 'project_id'), 'conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $projects = $this->paginate('Project', array('Project.id' => $employee_project_teams));
            }
        }
        $showGantt = isset($this->params['url']['gantt']) ? (bool) $this->params['url']['gantt'] : false;
        $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;

        $option = array();
        switch ($showType) {
            case 2 : {
                    if ($showGantt) {
                        $option = array(4, 4);
                    } else {
                        $option = array(3, 3);
                    }
                    break;
                }
            case 3 : {
                    if ($showGantt) {
                        $option = array(6, 4);
                    } else {
                        $option = array(5, 3);
                    }
                    break;
                }
            case 4 : {
                    if ($showGantt) {
                        $option = array(8, 4);
                    } else {
                        $option = array(7, 3);
                    }
                    break;
                }
            case 5 :
            default : {
                    if (!$showGantt) {
                        $option = array(2, 2);
                    }
                }
        }
        if ($option) {
            $this->helpers = array_merge($this->helpers, array(
                'Gantt' . $option[1] => array()));
            $this->action = 'project' . $option[0];
            unset($this->helpers['Gantt']);
        }
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $this->set(compact('projects', 'limit', '_filter', 'showGantt', 'showType', 'display'));
    }

    /**
     * Export vision project
     *
     * View Detail
     *  - export_project : normal display
     *  - export_project2 : show group by profit & project
     *  - export_project3 : show group by function & project
     *  - export_project4 : show group by profit & function
     *
     * @return void
     * @access public
     */
    function export_project() {
        set_time_limit(120);
        $this->layout = false;
        if (!empty($this->data['Export'])) {
            extract($this->data['Export']);

            if ($showGantt) {
                $canvas = explode(";", $canvas);
                $type = $canvas[0];
                $canvas = explode(",", $canvas[1]);

                $tmpFile = TMP . 'page_' . time() . '.png';
                file_put_contents($tmpFile, base64_decode($canvas[1]));

                list($_width, $_height) = getimagesize($tmpFile);

                $image = imagecreatefrompng($tmpFile);
                $crop = imagecreatetruecolor($_width - 29, $height);

                imagecopy($crop, $image, 0, 0, 29, 190, $_width, $height);
                imagepng($crop, $tmpFile);
            }
            $conditions = unserialize($conditions);
            $months = unserialize($months);

            if (isset($projectId)) {
                $projectId = unserialize($projectId);
            }
            if (isset($functions)) {
                $functions = unserialize($functions);
            }
            switch ($showType) {
                case 2: {
                        $this->action = 'export_project2';
                        break;
                    }
                case 3: {
                        $this->action = 'export_project3';
                        break;
                    }
                case 4: {
                        $this->action = 'export_project4';
                        break;
                    }
            }

            $this->set(compact('tmpFile', 'height', 'project', 'projectId', 'functions', 'rows', 'start', 'end', 'conditions', 'summary', 'showGantt', 'showType', 'months', 'displayFields'));
        } else {
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
    }

    /** ****************************************BUILD STAFFING START 06/05/2013********************************************************************* */
    private function _getProjectAndPhase($conditions = array()){
        $this->loadModel('Project');
        $_filter = array();
        if (!empty($conditions['profit_center_id'])) {
            $_filter['profit_center_id'] = $conditions['profit_center_id'];
        }
        if (!empty($conditions['project_function_id'])) {
            $_filter['project_function_id'] = $conditions['project_function_id'];
        }
        if (!empty($_filter)) {
            $_filter['NOT'] = array('project_function_id' => null);
            $conditions['Project.id'] = $this->Project->ProjectTeam->find('list', array('conditions' => $_filter, 'fields' => array('id', 'project_id')));
        }
        unset($conditions['profit_center_id']);
        unset($conditions['project_function_id']);
        if(empty($conditions['project_amr_program_id'])){
            unset($conditions['project_amr_program_id']);
        }
        if(empty($conditions['project_amr_sub_program_id'])){
            unset($conditions['project_amr_sub_program_id']);
        }
        if(empty($conditions['project_manager_id'])){
            unset($conditions['project_manager_id']);
        }
        if(empty($conditions['project_status_id'])){
            unset($conditions['project_status_id']);
        }
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $limit = 10000;
        $projects = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array('ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ), 'ProjectPhase' => array('name', 'color'))), 'limit' => $limit, 'fields' => array('project_name', 'start_date', 'end_date', 'planed_end_date'));
        if ($this->is_sas){
            $projects = $this->Project->find('all', $this->paginate);
        }
        else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
                $projects = $this->paginate('Project', array('Project.company_id' => $sub_companies));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('list', array('recursive' => -1, 'fields' => array('id', 'project_id'), 'conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $projects = $this->paginate('Project', array('Project.id' => $employee_project_teams));
            }
        }
        return $projects;
    }
    /**
     * Export vision system
     *
     * @return void
     * @access public
     */
    function export_system() {
        set_time_limit(0);
        $this->layout = false;
        if (!empty($this->data['ExportVision'])) {
            $conditions = array(
                'project_amr_program_id' => !empty($this->data['ExportVision']['program']) ? explode(',', $this->data['ExportVision']['program']) : array(),
                'project_amr_sub_program_id' => !empty($this->data['ExportVision']['sub_program']) ? explode(',', $this->data['ExportVision']['sub_program']) : array(),
                'project_manager_id' => !empty($this->data['ExportVision']['manager']) ? explode(',', $this->data['ExportVision']['manager']) : array(),
                'project_status_id' => !empty($this->data['ExportVision']['status']) ? explode(',', $this->data['ExportVision']['status']) : array(),
                'profit_center_id' => !empty($this->data['ExportVision']['profit_center']) ? explode(',', $this->data['ExportVision']['profit_center']) : array(),
                'project_function_id' => !empty($this->data['ExportVision']['function']) ? explode(',', $this->data['ExportVision']['function']) : array()
            );
            $showGantt = !empty($this->data['ExportVision']['showGantt']) ? $this->data['ExportVision']['showGantt'] : 0;
            $showType = !empty($this->data['ExportVision']['showType']) ? $this->data['ExportVision']['showType'] : 0;
            $summary = !empty($this->data['ExportVision']['summary']) ? $this->data['ExportVision']['summary'] : 0;
            $projects = $this->_getProjectAndPhase($conditions);
            $start = $end = 0;
            $data = $projectId = array();
            if(!empty($projects)){
                foreach ($projects as $project) {
                    $_data = array(
                        'name' => $project['Project']['project_name'],
                        'phase' => array(),
                    );
                    $projectId[$project['Project']['id']] = $project['Project']['project_name'];
                    if (!empty($project['ProjectPhasePlan'])) {
                        foreach ($project['ProjectPhasePlan'] as $phace) {
                            $_phase = array(
                                'name' => !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '',
                                'start' => !empty($phace['phase_planed_start_date']) ? intval(strtotime($phace['phase_planed_start_date'])) : 0,
                                'end' => !empty($phace['phase_planed_end_date']) ? intval(strtotime($phace['phase_planed_end_date'])) : 0,
                                'rstart' => !empty($phace['phase_real_start_date']) ? intval(strtotime($phace['phase_real_start_date'])) : 0,
                                'rend' => !empty($phace['phase_real_end_date']) ? intval(strtotime($phace['phase_real_end_date'])) : 0,
                                'color' => !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380'
                            );
                            if ($_phase['rstart'] > 0) {
                                $_start = min($_phase['start'], $_phase['rstart']);
                            } else {
                                $_start = $_phase['start'];
                            }
                            if (!$start || ($_start > 0 && $_start < $start)) {
                                $start = $_start;
                            }
                            $_end = max($_phase['end'], $_phase['rend']);
                            if (!$end || $_end > $end) {
                                $end = $_end;
                            }
                            $_data['phase'][] = $_phase;
                        }
                    }
                    $data[] = $_data;
                }
            }
            //if ($showGantt) {
//                $canvas = explode(";", $canvas);
//                $type = $canvas[0];
//                $canvas = explode(",", $canvas[1]);
//
//                $tmpFile = TMP . 'page_' . time() . '.png';
//                file_put_contents($tmpFile, base64_decode($canvas[1]));
//
//                list($_width, $_height) = getimagesize($tmpFile);
//
//                $image = imagecreatefrompng($tmpFile);
//                $crop = imagecreatetruecolor($_width - 29, $height);
//
//                imagecopy($crop, $image, 0, 0, 29, 190, $_width, $height);
//                imagepng($crop, $tmpFile);
//            }
            $listIdProjects = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : null;
            switch ($showType) {
                case 0 : {
                        $staffingss = $this->_visionsStaffingSkills($listIdProjects);
                        break;
                    }
                case 1 : {
                        $staffingss = $this->_visionsStaffingProfitCenter($listIdProjects);
                        $idProfits = Set::classicExtract($staffingss, '{n}.id');
                        $getDates = Set::classicExtract($staffingss, '{n}.data.{n}.date');
                        $setDates = array();
                        foreach($getDates as $getDate){
                            foreach($getDate as $getD){
                                $setDates[] = $getD;
                            }
                        }
                        $setDates = !empty($setDates) ? array_unique($setDates) : array();
                        $startDateFilter = !empty($setDates) ? min($setDates) : array();
                        $endDateFilter = !empty($setDates) ? max($setDates) : array();
                        // date from phase
                        $startDatePhases = !empty($projects) ? Set::classicExtract($projects, '{n}.ProjectPhasePlan.{n}.phase_planed_start_date') : array();
                        $setStartDatePhases = array();
                        foreach($startDatePhases as $startDatePhase){
                            foreach($startDatePhase as $startD){
                                $setStartDatePhases[] = strtotime($startD);
                            }
                        }
                        $setStartDatePhases = !empty($setStartDatePhases) ? min($setStartDatePhases) : array();
                        $endDatePhases = !empty($projects) ? Set::classicExtract($projects, '{n}.ProjectPhasePlan.{n}.phase_planed_end_date') : array();
                        $setEndDatePhases = array();
                        foreach($endDatePhases as $endDatePhase){
                            foreach($endDatePhase as $endD){
                                $setEndDatePhases[] = strtotime($endD);
                            }
                        }
                        $setEndDatePhases = !empty($setEndDatePhases) ? max($setEndDatePhases) : array();
                        $setStartDatePhases = !empty($setStartDatePhases) ? strtotime('01-'.date('m-Y', $setStartDatePhases)) : array();
                        $setEndDatePhases = !empty($setEndDatePhases) ? strtotime('01-'.date('m-Y', $setEndDatePhases)) : array();
                        if($setStartDatePhases <= $startDateFilter){
                            $startDateFilter = $setStartDatePhases;
                        }
                        if($setEndDatePhases > $endDateFilter){
                            $endDateFilter = $setEndDatePhases;
                        }
                        list($totalEmployees, $capacities) = $this->_capacityFromForecast($idProfits, $startDateFilter, $endDateFilter, false);
                        foreach($staffingss as $key => $staffing){
                            foreach($capacities as $id => $capacity){
                                foreach($capacity as $times => $value){
                                    if($staffing['id'] == $id){
                                        $staffingss[$key]['data'][$times]['capacity'] = $value['capacity'];
                                        $staffingss[$key]['data'][$times]['working'] = $value['working'];
                                        $staffingss[$key]['data'][$times]['absence'] = $value['absence'];
                                        $staffingss[$key]['data'][$times]['employee'] = !empty($value['totalEmployee']) ? $value['totalEmployee'] : 0;
                                    }
                                }
                            }
                        }
                        break;
                    }
                case 5 :
                default : {
                        $staffingss = $this->_projectVisionData($listIdProjects);
                        break;
                    }
            }
            $months = array();
            if(!empty($start) && !empty($end)){
                $startTmp = $start;
                $endTmp = $end;
                while($startTmp <= $endTmp){
                    $totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $startTmp), date("Y", $startTmp));
                    $_datas = array(
                        0 => $totalDayOfMonth,
                        1 => date("m", $startTmp),
                        2 => date("Y", $startTmp)
                    );
                    $months[] = $_datas;
                    $startTmp = mktime(0, 0, 0, date("m", $startTmp)+1, date("d", $startTmp), date("Y", $startTmp));
                }
            }
            if($showType == 5 || $showType == 0){
                $this->action = 'export_system';
            } else {
                $this->action = 'export_system_profit_center';
            }
            $this->helpers = array_merge($this->helpers, array(
                'GanttVs'));
            $this->set(compact('tmpFile', 'height', 'start', 'end', 'data', 'projectId', 'months', 'projects', 'summary', 'showGantt', 'showType', 'staffingss'));
        } else {
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
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
        $this->employee_info['Employee']['day_established'] = !empty($this->employee_info['Company']['day_established']) ? $this->employee_info['Company']['day_established'] : '';
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);

        return $this->employee_info['Employee'];
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
     * Get min date and max date of project task
     *
     * @return void
     * @access private
     */

    private function _getMinAndMaxDate($project_id = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectPhasePlan');
        // check oppotunity
        $this->loadModel('Project');
        $checkOppotunity  = $this->Project->find('first',array('conditions'=>array(
                                                'Project.id'=>$project_id,
                                                'OR'=>array('Project.category <>'=>2,
                                                                  'AND'=>array(
                                                                        'Project.category'=>2,
                                                                        'Project.is_staffing'=>1
                                                                    )
                                                            )
            )));
        if(!empty($checkOppotunity)){
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
        }else{
            $projectTasks = array();
        }
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
     * Staffing of employee
     *
     *
     * @return array()
     * @access private
     */
     private function _staffingEmloyee($project_id = null){
        $this->loadModel('TmpStaffingSystem');
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
     private function _staffingProfitCenter($project_id = null){
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay du lieu staffing cho profit center
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'profit_center',
                'company_id' => $employeeName['company_id'],
                'NOT' => array(
                    'project_id' => $this->getOpportunityNotApplyStaffing($employeeName['company_id'], null, null),
                )
            )
        ));
        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * profit_center => array(
         *      'time' => array()
         * )
         */
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
        $isPlus = ( $category  == 'profit_plus' );
        /* END Edit by Huynh */
        if( $isPlus ){

            $this->loadModel('ProjectFunctionEmployeeRefer');
            $pcs = $this->ProjectFunctionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectFunctionEmployeeRefer.project_team_id IN (SELECT id FROM project_teams WHERE project_id = ' . $project_id . ')'
                ),
                'fields' => array('ProjectFunctionEmployeeRefer.profit_center_id', 'ProjectFunctionEmployeeRefer.profit_center_id'),
                // 'joins' => array(
                //     array(
                //         'table' => 'project_teams',
                //         'alias' => 'Team',
                //         'type' => 'inner',
                //         'conditions' => array(
                //             'Team.id = ProjectFunctionEmployeeRefer.project_team_id',
                //             'Team.project_id' => $project_id
                //         )
                //     )
                // )
            ));
            foreach($pcs as $pc){
                $datas[$pc] = array();
            }
        }
        if(!empty($getDatas)){
            // $modelSum = array();
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
                // sum by model
                // if( !isset($modelSum[$dx['model_id']]) ){
                //     $modelSum[$dx['model_id']] = 0;
                // }
                // $modelSum[$dx['model_id']] += $dx['consumed'] + $dx['estimated'];
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
        if( $isPlus ){
            // get parents
            $list = array();
            $parents = array();
            foreach($profitCenters as $pc){
                $pcID = $pc['ProfitCenter']['id'];
                $list[$pcID] = $pc['ProfitCenter']['name'];
                $d = $this->ProfitCenter->getpath($pcID);
                if( $d ){
                    $parents[$pcID] = Set::combine($d, '{n}.ProfitCenter.id', '{n}.ProfitCenter.name');
                    unset($parents[$pcID][$pcID]);
                }
            }
            $order = $this->ProfitCenter->generatetreelist(array('id' => array_keys($list)), null, null, '', -1);

            $result = array();
            $groups = array();
            $result2 = array();
            $result2[0] = array(
                'id' => 'summary',
                'name' => __('Total', true),
                'is_check' => 2,
                'data' => array()
            );
            foreach($order as $id => $name){
                // if( isset($modelSum[$id]) && !$modelSum[$id] )continue;
                // if i have children, sum me
                $data = isset($datas[$id]) ? $datas[$id] : array();
                // creating node
                $result[$id] = array(
                    'id' => $id,
                    'name' => $name,
                    'is_check' => 2,
                    'data' => $data
                );
                // summary
                $result2[0]['data'] = $this->plusData($result2[0]['data'], $data);
                // if my parent exists in the list
                if( isset($parents[$id]) ){
                    foreach($parents[$id] as $parent => $parentName){
                        if( isset($list[$parent]) ){
                            $parentKey = 'sum-' . $parent;
                            if( !isset($groups[$parent]) ){
                                $groups[$parent] = array(
                                    'id' => $parentKey,
                                    'name' => __('Total', true) . ' ' . $parentName,
                                    'is_check' => 2,
                                    'data' => array(),
                                    'children' => array($parent)
                                );
                            }
                            // plus the child to the parent
                            $groups[$parent]['data'] = $this->plusData($groups[$parent]['data'], $data);
                            $groups[$parent]['children'][] = $id;
                        }
                    }
                }
            }
            foreach($list as $id => $name){
                $data = isset($datas[$id]) ? $datas[$id] : array();
                if( isset($groups[$id]) ){
                    $groups[$id]['data'] = $this->plusData($groups[$id]['data'], $data);
                    $groups[$id]['children'] = array_unique($groups[$id]['children']);
                }
            }
            // order
            foreach($order as $id => $name){
                if( isset($result[$id]) ){
                    // $parent = @$parents[$id][0];
                    if( isset($groups[$id]) ){
                        $result2[] = $groups[$id];
                        unset($groups[$id]);
                    }
                    $result2[] = $result[$id];
                }
            }
            // pr($result2);
            // die;
            return $result2;
        } else {
            $staffings = array();
            $key = 0;
            foreach($profitCenters as $profitCenter){
                $pc = $profitCenter['ProfitCenter']['id'];
                if( isset($modelSum[$pc]) && !$modelSum[$pc] )continue;
                $staffings[$key]['id'] = $pc;
                $staffings[$key]['is_check'] = 2;
                $staffings[$key]['name'] = $profitCenter['ProfitCenter']['name'];
                $staffings[$key]['data'] = isset($datas[$pc]) ? $datas[$pc] : '';
                $key++;
            }
            return $staffings;
        }
    }

    private function traverse($root, &$result, &$data){
        $myChildren = array();
        foreach($root as $node){
            $info = $node['ProfitCenter'];
            $children = $node['children'];
            if( !empty($children) ){
                $myChildren = $this->traverse($children, $result, $data);
            }
            // plus children
            $list = array();
            foreach ($children as $child) {
                $childID = $child['ProfitCenter']['id'];
                if( !isset($data[$info['id']]) ){
                    $data[$info['id']] = array();
                }
                $data[$info['id']] = $this->plusData($data[$info['id']], $data[$childID]);
                $list[] = $childID;
            }
            $myChildren = array_merge($myChildren, $list);
            // is leaf
            // build result
            $result[$info['id']] = array(
                'id' => $info['id'],
                'is_check' => 2,
                'name' => $info['name'],
                'direct_children' => $list,
                'children' => $myChildren,
                'data' => isset($data[$info['id']]) ? $data[$info['id']] : array()
            );
        }
        return $myChildren;
    }

    private function plusData($current, $new){
        foreach ($new as $date => $value) {
            if( !isset($current[$date]) ){
                // unset($value['remains']);
                $current[$date] = $value;
            } else {
                $current[$date]['date'] = $date;
                $current[$date]['consumed'] += $value['consumed'];
                $current[$date]['validated'] += $value['validated'];
            }
        }
        return $current;
    }

     /**
     * Staffing of function
     *
     *
     * @return
     * @access private
     */
     private function _staffingFunction($project_id = null){
        $this->loadModel('ProjectFunction');
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProjectTeam');
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay du lieu staffing cho profit center
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'skill',
                'company_id' => $employeeName['company_id'],
                'NOT' => array(
                    'project_id' => $this->getOpportunityNotApplyStaffing($employeeName['company_id'], null, null),
                ),
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
     * Show staffing
     *
     * View Detail
     *  - project : normal display
     *  - project2 : hidden gantt chart
     *  - project3 : show profit & project and hidden gantt chart
     *  - project4 : show profit & project and show gantt chart
     *  - project5 : show function & project and show gantt chart
     *  - project6 : show function & project and show gantt chart
     *  - project7 : show profit & function and show gantt chart
     *  - project8 : show profit & function and show gantt chart
     *
     * @return void
     * @access public
     */
    public function visions_staffing() {
        $this->loadModel('Project');
        $default = array(
            'ProjectProjectAmrProgramId_sum' => '',
            'ProjectProjectAmrSubProgramId_sum' => '',
            'ProjectProjectManagerId' => '',
            'ProjectProjectStatusId' => ''
        );
        $conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);
        $keys = array(
            'project_amr_program_id',
            'project_amr_sub_program_id',
            'project_manager_id',
            'project_status_id'
        );
        $conditions = Set::filter(array_combine($keys, $conditions));
        $_filter = array();
        if (!empty($this->params['url']['ProjectProfitCenterId'])) {
            $_filter['profit_center_id'] = $this->params['url']['ProjectProfitCenterId'];
        }
        if (!empty($this->params['url']['ProjectProjectFunctionId'])) {
            $_filter['project_function_id'] = $this->params['url']['ProjectProjectFunctionId'];
        }

        if ($_filter) {
            $_filter['NOT'] = array('project_function_id' => null);
            $conditions['Project.id'] = $this->Project->ProjectTeam->find('list', array('conditions' => $_filter, 'fields' => array('id', 'project_id')));
        }
        $conditions['OR']['Project.category <>'] = 2;
        $conditions['OR']['Project.is_staffing <>'] = 0;
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $limit = isset($this->params['url']['limit']) ? (int) $this->params['url']['limit'] : 10;
        $limit = 10000;
        $projects = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array('ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ), 'ProjectPhase' => array('name', 'color'))), 'limit' => $limit, 'fields' => array('project_name', 'start_date', 'end_date', 'planed_end_date'));
        if ($this->is_sas)
            $projects = $this->Project->find('all', $this->paginate);
        else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
                $projects = $this->paginate('Project', array('Project.company_id' => $sub_companies));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('list', array('recursive' => -1, 'fields' => array('id', 'project_id'), 'conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $projects = $this->paginate('Project', array('Project.id' => $employee_project_teams));
            }
        }
        $showGantt = isset($this->params['url']['gantt']) ? (bool) $this->params['url']['gantt'] : false;
        $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;

        $option = array();
        $listIdProjects = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : null;
        switch ($showType) {
            case 0 : {
                    $staffingss = $this->_visionsStaffingSkills($listIdProjects);
                    break;
                }
            case 1 : {
                    $staffingss = $this->_visionsStaffingProfitCenter($listIdProjects);
                    $idProfits = Set::classicExtract($staffingss, '{n}.id');
                    $getDates = Set::classicExtract($staffingss, '{n}.data.{n}.date');
                    $setDates = array();
                    foreach($getDates as $getDate){
                        foreach($getDate as $getD){
                            $setDates[] = $getD;
                        }
                    }

                    $setDates = !empty($setDates) ? array_unique($setDates) : array();
                    $startDateFilter = !empty($setDates) ? min($setDates) : array();
                    $endDateFilter = !empty($setDates) ? max($setDates) : array();
                    // date from phase
                    $startDatePhases = !empty($projects) ? Set::classicExtract($projects, '{n}.ProjectPhasePlan.{n}.phase_planed_start_date') : array();
                    $setStartDatePhases = array();
                    foreach($startDatePhases as $startDatePhase){
                        foreach($startDatePhase as $startD){
                            $setStartDatePhases[] = strtotime($startD);
                        }
                    }
                    $setStartDatePhases = !empty($setStartDatePhases) ? min($setStartDatePhases) : array();
                    $endDatePhases = !empty($projects) ? Set::classicExtract($projects, '{n}.ProjectPhasePlan.{n}.phase_planed_end_date') : array();
                    $setEndDatePhases = array();
                    foreach($endDatePhases as $endDatePhase){
                        foreach($endDatePhase as $endD){
                            $setEndDatePhases[] = strtotime($endD);
                        }
                    }
                    $setEndDatePhases = !empty($setEndDatePhases) ? max($setEndDatePhases) : array();
                    $setStartDatePhases = !empty($setStartDatePhases) ? strtotime('01-'.date('m-Y', $setStartDatePhases)) : array();
                    $setEndDatePhases = !empty($setEndDatePhases) ? strtotime('01-'.date('m-Y', $setEndDatePhases)) : array();
                    if($setStartDatePhases <= $startDateFilter){
                        $startDateFilter = $setStartDatePhases;
                    }
                    if($setEndDatePhases > $endDateFilter){
                        $endDateFilter = $setEndDatePhases;
                    }

                    list($totalEmployees, $capacities) = $this->_capacityFromForecast($idProfits, $startDateFilter, $endDateFilter, false);
                    foreach($staffingss as $key => $staffing){
                        foreach($capacities as $id => $capacity){
                            foreach($capacity as $times => $value){
                                if($staffing['id'] == $id){
                                    $staffingss[$key]['data'][$times]['capacity'] = $value['capacity'];
                                    $staffingss[$key]['data'][$times]['working'] = $value['working'];
                                    $staffingss[$key]['data'][$times]['absence'] = $value['absence'];
                                    $staffingss[$key]['data'][$times]['employee'] = !empty($value['totalEmployee']) ? $value['totalEmployee'] : 0;
                                }
                            }
                        }
                    }
                    break;
                }
            case 5 :
            default : {
                    $staffingss = $this->_projectVisionData($listIdProjects);
                    break;
                }
        }
        $this->helpers = array_merge($this->helpers, array(
                'GanttVs'));
        $this->action = 'visions_staffing';
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $this->set(compact('projects', 'limit', '_filter', 'showGantt', 'showType', 'display', 'staffingss'));
    }
    /**
     * Chuan bi data cho employee
     *
     * View Detail
     *
     * @return array
     * @access private
     */
    private function _projectVisionData($listProjects = null){

        $this->loadModel('Project');
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmpoyee();
        if(empty($listProjects)){
            $projects = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id')
            ));
        } else {
            $projects = $listProjects;
        }
        $staffings = array();
        if(!empty($projects)){
            $_fields=array(
                'TmpStaffingSystem.model_id',
                'project_id',
                'activity_id',
                'SUM(TmpStaffingSystem.consumed) as consumed',
                'TmpStaffingSystem.company_id',
                'TmpStaffingSystem.date',
                'SUM(TmpStaffingSystem.estimated) as estimated'
            );
            $getDatas = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'TmpStaffingSystem.project_id' => $projects,
                    'model' => 'employee',
                    'company_id' => $employeeName['company_id']
                ),
                'group' => array('TmpStaffingSystem.project_id','TmpStaffingSystem.date'),
                'fields' => $_fields,
            ));
            $datas = array();
            if(!empty($getDatas)){
                foreach($getDatas as $getData){
                    //$dx = $getData['TmpStaffingSystem'];
                    $dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);
                     //date
                    $datas[$dx['project_id']][$dx['date']]['date'] = $dx['date'];
                    // workload
                    if(!isset($datas[$dx['project_id']][$dx['date']]['validated'])){
                        $datas[$dx['project_id']][$dx['date']]['validated'] = 0;
                    }
                    $datas[$dx['project_id']][$dx['date']]['validated'] = $dx['estimated'];
                    // consumed
                    if(!isset($datas[$dx['project_id']][$dx['date']]['consumed'])){
                        $datas[$dx['project_id']][$dx['date']]['consumed'] = 0;
                    }
                    $datas[$dx['project_id']][$dx['date']]['consumed'] = $dx['consumed'];
                    //$datas[$dx['project_id']][$dx['date']]['remains'] = round($datas[$dx['project_id']][$dx['date']]['validated'] - $datas[$dx['project_id']][$dx['date']]['consumed'], 2);
                }
            }
            $projectNames = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $projects),
                'fields' => array('id', 'project_name')
            ));
            foreach($projectNames as $id => $name){
                $staffings[] = array(
                    'id' => $id,
                    'name' => $name,
                    'data' => !empty($datas[$id]) ? $datas[$id] : array()
                );
            }
        }
        return $staffings;
    }

    private function _visionsStaffingSkills($listProjects = null){
        $this->loadModel('Project');
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProjectFunction');
        $employeeName = $this->_getEmpoyee();

        $results = array();
        if(empty($listProjects)){
            $projects = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id')
            ));
        } else {
            $projects = $listProjects;
        }
        $staffings = array();
        if(!empty($projects)){
            $getDatas = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'TmpStaffingSystem.project_id' => $projects,
                    'model' => 'skill',
                    'company_id' => $employeeName['company_id']
                )
            ));
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
                    //$datas[$dx['model_id']][$dx['date']]['remains'] = round($datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'], 2);
                }
            }
            //if(!empty($datas)){
//                $tmpDatas = $datas;
//                $datas = $this->_resetRemainSystem($tmpDatas);
//            }
            $functions = $this->ProjectFunction->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id']),
                'fields' => array('id', 'name')
            ));
            $functions['999999999'] = __('Not Affected', true);
            $functions[''] = 'No Name';
            if(!empty($datas)){
                foreach($datas as $id => $data){
                    $staffings[] = array(
                        'id' => $id,
                        'isFamily' => true,
                        'employee_id' => 0,
                        'name' => isset($functions[$id]) ? $functions[$id] : 'No Name',
                        'data' => $data
                    );
                }
            }
        }
        return $staffings;
     }

     private function _visionsStaffingProfitCenter($listProjects = null){
       $this->loadModel('Project');
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProfitCenter');
        $employeeName = $this->_getEmpoyee();

        $results = array();
        if(empty($listProjects)){
            $projects = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id')
            ));
        } else {
            $projects = $listProjects;
        }
        $staffings = array();
        if(!empty($projects)){
            $_fields=array(
                        'TmpStaffingSystem.model_id',
                        'project_id',
                        'activity_id',
                        'SUM(TmpStaffingSystem.consumed) as consumed',
                        'TmpStaffingSystem.company_id',
                        'TmpStaffingSystem.date',
                        'SUM(TmpStaffingSystem.estimated) as estimated'
                    );
            $getDatas = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'TmpStaffingSystem.project_id' => $projects,
                    'model' => 'profit_center',
                    'TmpStaffingSystem.company_id' => $employeeName['company_id']
                ),
                'group' => array('TmpStaffingSystem.model_id','TmpStaffingSystem.date'),
                'fields' => $_fields,
                //'joins' => $_joins
            ));
            $datas = array();
            if(!empty($getDatas)){
                foreach($getDatas as $getData){
                    //$dx = $getData['TmpStaffingSystem'];
                    $dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);
                     //date
                    $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                    // workload
                    if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                        $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                    }
                    $datas[$dx['model_id']][$dx['date']]['validated'] = $dx['estimated'];
                    // consumed
                    if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                        $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                    }
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = $dx['consumed'];
                    //$datas[$dx['model_id']][$dx['date']]['remains'] = round($datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'], 2);
                }
            }
            //if(!empty($datas)){
//                $tmpDatas = $datas;
//                $datas = $this->_resetRemainSystem($tmpDatas);
//            }
            $profitCenter = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id']),
                'fields' => array('id', 'name')
            ));
            $profitCenter['999999999'] = __('Not Affected', true);
            $profitCenter[''] = 'No Name';
            if(!empty($datas)){
                foreach($datas as $id => $data){
                    $staffings[] = array(
                        'id' => $id,
                        'isFirst' => true,
                        'name' => isset($profitCenter[$id]) ? $profitCenter[$id] : 'No Name',
                        'data' => $data
                    );
                }
            }
        }

        return $staffings;
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

     public function dash_board($project_id){
        $this->loadModel('Project');
        $filterId = isset($this->params['url']['filter']) ? $this->params['url']['filter'] : 1;
        list($_start, $_end) = $this->_parseParams();
        $staffings = array();
        if($filterId == 1){
            $staffings = $this->_staffingEmloyee($project_id);
        } elseif($filterId == 2){
            $staffings = $this->_staffingProfitCenter($project_id);
        } else {
            $staffings = $this->_staffingFunction($project_id);
        }
        $result = $manDays = array();
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
            $manDays = !empty($manDays) ? (integer)max($manDays) : 0;
            if($manDays >= 0 && $manDays <= 100) {$manDays = 100;}
            elseif($manDays > 100 && $manDays <= 200) {$manDays = 200;}
            elseif($manDays > 200 && $manDays <= 500) {$manDays = 500;}
            elseif($manDays > 500 && $manDays <= 1000) {$manDays = 1000;}
            elseif($manDays > 1000 && $manDays <= 5000) {$manDays = 5000;}
            elseif($manDays > 5000) {$manDays = 10000;}
            else {$manDays = 100000;}
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
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('project_name')
            ));
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
        // get data from project risks.
        $this->_dash_board_of_risk($project_id);
        $this->set(compact('dataSets', 'filterId', 'project_id', 'manDays', 'projectName', 'setYear', 'display'));
     }

     private function _dash_board_of_risk($project_id){
        $this->loadModel('Project');
        $this->loadModel('Employee');
        $this->loadModel('ProjectRisk');
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('project_name', 'company_id')
            ));

        $risks = $this->ProjectRisk->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                    'OR' => array(
                        'risk_close_date' => '0000-00-00',
                        'risk_close_date >' => date('Y-m-d', time())
                    )
                ),
                'fields' => array('project_risk', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_assign_to')
            ));
        $listEmployee = !empty($risks) ? array_unique(Set::classicExtract($risks, '{n}.ProjectRisk.risk_assign_to')) : array();
        $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $listEmployee),
                'fields' => array('id', 'fullname')
            ));
        /**
         * Lay cac level risk severity
         * Muc 1: Forte
         * Muc 2: Moyenne
         * Muc 3: Faible
         */
        // Forte
        $forteSeverity = $this->ProjectRisk->ProjectRiskSeverity->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskSeverity.risk_severity LIKE \'%for%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%strong%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%high%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_severity'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $forteSeverity = !empty($forteSeverity['ProjectRiskSeverity']) ? array($forteSeverity['ProjectRiskSeverity']['id'] => $forteSeverity['ProjectRiskSeverity']['risk_severity']) : array('1' => 'Forte');
        // Moyenne
        $moyenneSeverity = $this->ProjectRisk->ProjectRiskSeverity->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskSeverity.risk_severity LIKE \'%moy%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%ave%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%med%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_severity'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $moyenneSeverity = !empty($moyenneSeverity['ProjectRiskSeverity']) ? array($moyenneSeverity['ProjectRiskSeverity']['id'] => $moyenneSeverity['ProjectRiskSeverity']['risk_severity']) : array('2' => 'Moyenne');
        // Faible
        $faibleSeverity = $this->ProjectRisk->ProjectRiskSeverity->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskSeverity.risk_severity LIKE \'%fai%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%low%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%small%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_severity'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $faibleSeverity = !empty($faibleSeverity['ProjectRiskSeverity']) ? array($faibleSeverity['ProjectRiskSeverity']['id'] => $faibleSeverity['ProjectRiskSeverity']['risk_severity']) : array('3' => 'Faible');
        $riskSeverities = Set::pushDiff($forteSeverity, $moyenneSeverity);
        $riskSeverities = Set::pushDiff($riskSeverities, $faibleSeverity);
        /**
         * Lay cac level risk Occurrence
         * Muc 1: Forte
         * Muc 2: Moyenne
         * Muc 3: Faible
         */
        // Forte
        $forteOccur = $this->ProjectRisk->ProjectRiskOccurrence->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%for%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%strong%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%high%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_occurrence'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $forteOccur = !empty($forteOccur['ProjectRiskOccurrence']) ? array($forteOccur['ProjectRiskOccurrence']['id'] => $forteOccur['ProjectRiskOccurrence']['risk_occurrence']) : array('1' => 'Forte');
        // Moyenne
        $moyenneOccur = $this->ProjectRisk->ProjectRiskOccurrence->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%moy%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%ave%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%med%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_occurrence'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $moyenneOccur = !empty($moyenneOccur['ProjectRiskOccurrence']) ? array($moyenneOccur['ProjectRiskOccurrence']['id'] => $moyenneOccur['ProjectRiskOccurrence']['risk_occurrence']) : array('2' => 'Moyenne');
        // Faible
        $faibleOccur = $this->ProjectRisk->ProjectRiskOccurrence->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%fai%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%low%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%small%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_occurrence'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $faibleOccur = !empty($faibleOccur['ProjectRiskOccurrence']) ? array($faibleOccur['ProjectRiskOccurrence']['id'] => $faibleOccur['ProjectRiskOccurrence']['risk_occurrence']) : array('3' => 'Faible');
        $riskOccurrences = Set::pushDiff($forteOccur, $moyenneOccur);
        $riskOccurrences = Set::pushDiff($riskOccurrences, $faibleOccur);

        $datas = array();
        foreach($riskOccurrences as $id => $riskOccurrence){
            foreach($risks as $risk){
                if($id == $risk['ProjectRisk']['project_risk_occurrence_id']){
                    $_datas = array(
                        'occur' => trim($riskOccurrence),
                        'severity' => $risk['ProjectRisk']['project_risk_severity_id'],
                        'name' => $risk['ProjectRisk']['project_risk'],
                        'assign' => !empty($employees[$risk['ProjectRisk']['risk_assign_to']]) ? $employees[$risk['ProjectRisk']['risk_assign_to']] : ''
                    );
                    $datas[trim($riskOccurrence)][] = $_datas;
                }
            }
        }
        $listSeverities = array();
        /**
         * Lay value Forte Occur
         */
        $forteOccur = array_values($forteOccur);
        $forteOccur = $forteOccur[0];
        /**
         * Lay value Moyenne Occur
         */
        $moyenneOccur = array_values($moyenneOccur);
        $moyenneOccur = $moyenneOccur[0];
        /**
         * Lay value Faible Occur
         */
        $faibleOccur = array_values($faibleOccur);
        $faibleOccur = $faibleOccur[0];
        /**
         * Lay value Forte Severity
         */
        $forteSeverity = array_values($forteSeverity);
        $listSeverities = array_merge($listSeverities, $forteSeverity);
        $forteSeverity = $forteSeverity[0];
        /**
         * Lay value Moyenne Severity
         */
        $moyenneSeverity = array_values($moyenneSeverity);
        $listSeverities = array_merge($listSeverities, $moyenneSeverity);
        $moyenneSeverity = $moyenneSeverity[0];
        /**
         * Lay value Faible Severity
         */
        $faibleSeverity = array_values($faibleSeverity);
        $listSeverities = array_merge($listSeverities, $faibleSeverity);
        $faibleSeverity = $faibleSeverity[0];
        $severities = array(
                $forteSeverity => array(140, 135, 130, 125, 120, 115, 110, 105),
                $moyenneSeverity => array(95, 90, 85, 80, 75, 70, 65, 60, 55),
                $faibleSeverity => array(50, 45, 40, 35, 30, 25, 20, 15, 10, 5)
            );
        $radius = array(
                $forteOccur => array(
                        $faibleSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FF3333'
                        ),
                        $moyenneSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FF3333'
                        ),
                        $forteSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FF3333'
                        )
                    ),
                $moyenneOccur => array(
                        $faibleSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFA953'
                        ),
                        $moyenneSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFA953'
                        ),
                        $forteSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFA953'
                        )
                    ),
                $faibleOccur => array(
                        $faibleSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFFF79'
                        ),
                        $moyenneSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFFF79'
                        ),
                        $forteSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFFF79'
                        )
                    ),
            );
        $resetDatas = array();
        if(!empty($datas)){
            foreach($datas as $key => $data){
                foreach($data as $k => $vl){
                    foreach($riskSeverities as $id => $riskSeveritie){
                        $riskSeveritie = trim($riskSeveritie);
                        if($id == $vl['severity']){
                            $vl['severity'] = $severities[$riskSeveritie][$k];
                            $vl['minRadius'] = $radius[$key][$riskSeveritie]['minRadius'];
                            $vl['maxRadius'] = $radius[$key][$riskSeveritie]['maxRadius'];
                            $vl['color'] = $radius[$key][$riskSeveritie]['color'];
                            $resetDatas[$key][$riskSeveritie][] = $vl;
                        }
                    }
                }
            }
        }
        $results = array();
        if(!empty($resetDatas)){
            foreach($resetDatas as $resetData){
                foreach($resetData as $_datas){
                    foreach($_datas as $_data){
                        $results[] = $_data;
                    }
                }
            }
        }

        $endDatas = $series = array();
        foreach($results as $key => $result){
            if($result['occur'] == $faibleOccur){
                $endDatas[0]['occur'] = $faibleOccur;
                $endDatas[0]['SalesQ'.$key] = $result['severity'];
                $endDatas[0]['YoYGrowthQ'.$key] = 1;
                $endDatas[0]['minRadius'.$key] = $result['minRadius'];
                $endDatas[0]['maxRadius'.$key] = $result['maxRadius'];
                $endDatas[0]['name'.$key] = $result['name'];
                $endDatas[0]['assign'.$key] = $result['assign'];
            } else {
                $endDatas[0]['occur'] = $faibleOccur;
            }
            if($result['occur'] == $moyenneOccur){
                $endDatas[1]['occur'] = $moyenneOccur;
                $endDatas[1]['SalesQ'.$key] = $result['severity'];
                $endDatas[1]['YoYGrowthQ'.$key] = 1;
                $endDatas[1]['minRadius'.$key] = $result['minRadius'];
                $endDatas[1]['maxRadius'.$key] = $result['maxRadius'];

                $endDatas[1]['name'.$key] = $result['name'];
                $endDatas[1]['assign'.$key] = $result['assign'];
            } else {
                $endDatas[1]['occur'] = $moyenneOccur;
            }
            if($result['occur'] == $forteOccur){
                $endDatas[2]['occur'] = $forteOccur;
                $endDatas[2]['SalesQ'.$key] = $result['severity'];
                $endDatas[2]['YoYGrowthQ'.$key] = 1;
                $endDatas[2]['minRadius'.$key] = $result['minRadius'];
                $endDatas[2]['maxRadius'.$key] = $result['maxRadius'];

                $endDatas[2]['name'.$key] = $result['name'];
                $endDatas[2]['assign'.$key] = $result['assign'];
            } else {
                $endDatas[2]['occur'] = $forteOccur;
            }
            $series[$key]['occur'] = $result['occur'];
            $series[$key]['SalesQ'.$key] = $result['severity'];
            $series[$key]['YoYGrowthQ'.$key] = 1;
            $series[$key]['minRadius'.$key] = $result['minRadius'];
            $series[$key]['maxRadius'.$key] = $result['maxRadius'];
            $series[$key]['name'.$key] = $result['name'];
            $series[$key]['assign'.$key] = $result['assign'];
            $series[$key]['color'.$key] = $result['color'];
        }
        if(empty($endDatas)){
           $endDatas = array(
                0 => array(
                    'occur' => $faibleOccur,
                ),
                1 => array(
                    'occur' => $moyenneOccur,
                ),
                2 => array(
                    'occur' => $forteOccur,
                )
            );
        }
        $lenght = count($results);
        $this->set(compact('projectName', 'project_id', 'endDatas', 'lenght', 'series', 'listSeverities'));
     }

     /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams() {
        $year = date('Y');
        if (!empty($this->params['url']['year'])) {
            $year = intval($this->params['url']['year']);
        }
        $_start = strtotime($year . '-1-1');
        $_end = strtotime($year . '-12-31');

        if (empty($_start) || empty($_end)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

     /**
     *  End Tinh toan staffing ho tro cho bo loc va Dashboard generating
     *
     */

    /**
     * Tinh so ngay lam viec cua 1 thang
     */
    private function _countWorkingDate($date = null, $totalDate = null){
		$lib = new LibBehavior();
        $startDate = strtotime('01-'.date('m-Y', $date));
        $endDate = strtotime($totalDate.'-'.date('m-Y', $date));
		return $lib->getWorkingDays($startDate, $endDate, false);
    }
    private function _capacityFromForecast($profitCenterIds = array(), $startDate, $endDate, $getTotal = true){
        $this->loadModel('ActivityTask');
        return $this->ActivityTask->capacityFromForecast($profitCenterIds, $startDate, $endDate, $getTotal, true);
    }
    private function _capacityForEmployee($employeeIds = array(), $startDate, $endDate){
		
        $this->loadModel('ActivityTask');
        $employeeName = $this->employee_info['Employee'];
        $opportunityNotApplyStaffing = array(
            'project_id' => $this->getOpportunityNotApplyStaffing($employeeName['company_id'], $startDate, $endDate)
        );
        return $this->ActivityTask->capacityForEmployee($employeeIds, $startDate, $endDate, null, $opportunityNotApplyStaffing);
    }
    private function getOpportunityNotApplyStaffing($company, $startDate, $endDate)
    {
        $this->loadModel('Project');
        $listProjectOppotunity = $this->Project->find('list',array(
            'recursive' => -1,
            'conditions' => array(
                'Project.category'=> 2,
                'Project.is_staffing'=> 0,
                'Project.company_id'=> $company,
            ),
            'fields'=>array('id','id')
        ));
        if($this->projectIdCurrent != null && !empty($listProjectOppotunity) && isset($listProjectOppotunity[$this->projectIdCurrent]))
        {
            unset($listProjectOppotunity[$this->projectIdCurrent]);
        }
        return $listProjectOppotunity;
    }
    /**
     * Lay danh sach tat ca cac project
     * Goi function _staffingSystem luu tat ca workload cua employee, profit center, skill cua cac project do
     *
     * @access public
     * @return void
     * @author HuuPC
     *
     */
    public function staffingSystem($_index=null, $reBuildALL = false){
        $this->layout=false;
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $start = microtime(true);
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $projectSaves = $this->Session->read('ProjectSave.TMP');
        $projectSaves = !empty($projectSaves) ? $projectSaves : array();
        if($reBuildALL == true){
            $projects = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id'),
                'conditions' => array(
                    'NOT' => array('Project.id' => $projectSaves)
                ),
                'order' => array('id' => 'ASC'),
                'limit' => 300
            ));
        } else {
            $projects = $this->Project->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'id'),
                'conditions' => array(
                    'id' => $_index
                    //'NOT' => array('Project.id' => $projectSaves)
                ),
                'order' => array('id' => 'ASC'),
                'limit' => 200
            ));
        }
        foreach($projects as $project){
            echo 'Project: ' . $project . ' : ';
            $this->ProjectTask->staffingSystem($project,true);
            echo "<br />";
        }
        $projectSaves = array_merge($projectSaves, $projects);
        $this->Session->write('ProjectSave.TMP', $projectSaves);
        $end = microtime(true);

        echo 'Finish!'; echo '<br />';
        $timeExecute=$end-$start;
        echo 'Time execute: '.$timeExecute;
        if(empty($projects))
        {
            echo 'Finish!'; echo '<br />';
        }
        else
        {
            $_link='<a href="https://'.$_SERVER['HTTP_HOST'].'/project_staffings/staffingSystem">Next step</a>';
            echo $_link;
            echo '<br />';
        }

        exit;
     }
    private function _mergeDataSystem($workloads = array(), $consumeds = array(), $previous = array(), $totalConsumedTasks = array(), $model = null, $project_id = null, $activity_id = null){
        $this->loadModel('ActivityTask');
        return $this->ActivityTask->_mergeDataSystem($workloads, $consumeds, $previous, $totalConsumedTasks, $model, $project_id, $activity_id);
    }
    private function _recursiveTask($tasks = array(), $valueAdds = null, $tmpTasks = array()){
        $this->loadModel('ActivityTask');
        return $this->ActivityTask->_recursiveTask($tasks, $valueAdds, $tmpTasks);
    }
    private function _resetRemainSystem($datas = array()){
        $this->loadModel('ActivityTask');
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
     private function _consumedTask($listProjectTaskIds = array()){
        $employeeName = $this->_getEmpoyee();
        $totalConsumedTasks = $consumedTaskEmployees = array();
        if(!empty($listProjectTaskIds)){
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.project_task_id' => $listProjectTaskIds),
                'fields' => array('project_task_id', 'id')
            ));
            if(!empty($activityTasks)){
                $requests = $this->ActivityRequest->find('all',array(
                    'recursive'     => -1,
                    'fields'        => array('id', 'date', 'employee_id', 'task_id', 'value'),
                    'conditions'    => array(
                        'status'        => 2,
                        'task_id'       => $activityTasks,
                        'company_id'    => $employeeName['company_id'],
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
	public function _consumedPreviousTask($activityId = null){
        $employeeName = $this->_getEmpoyee();
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
	private function _taskCaculates($project_id = null, $company_id = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        // Kiem tra project co phai la project oppotunity khong checkbox
        $this->loadModel('Project');
        // $checkOppotunity  = $this->Project->find('first',array('conditions'=>array(
			// 'Project.id'=>$project_id,
			// 'OR'=>array('Project.category <>'=>2,
				  // 'AND'=>array(
						// 'Project.category'=>2,
						// 'Project.is_staffing'=>1
					// )
			// )
        // )));
        // if(!empty($checkOppotunity)){
            $projectTasks = $this->ProjectTask->find('all', array(
                //'recursive' => -1,
                'conditions' => array('ProjectTask.project_id' => $project_id),
                'fields' => array('id', 'task_title', 'parent_id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'estimated', 'predecessor', 'initial_estimated')
            ));
        // }else{
        //     $projectTasks = array();
        // }
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
            $datas[$key]['initial_estimated'] = isset($projectTask['ProjectTask']['initial_estimated']) ? $projectTask['ProjectTask']['initial_estimated'] : 0;
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
     * Ham availability cho staffing
     */
	 // listPTasks
    private function _availabilityDetail($employee_id = null, $start = null, $end = null, $company_id = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ActivityRequest');
        /**
         * Lay tat ca nhung request cua employee trong khoang thoi gian tu
         * start den end date tren tat ca cac task.
         */
        $requests = $this->ActivityRequest->find('list',array(
            'recursive'     => -1,
            'fields'        => array('id', 'task_id'),
            'conditions'    => array(
                'employee_id'   => $employee_id,
                'date BETWEEN ? AND ? ' => array(strtotime($start), strtotime($end)),
                'status'        => 2,
                'activity_id'   => 0,
                'company_id'    => $company_id,
                'NOT'           => array('value' => 0, "task_id" => null),
            )
        ));
        $requests = !empty($requests) ? array_unique($requests) : array();
        /**
         * Lay id cac task co request va kiem tra xem task nao thuoc project task (PMS = YES).
         */
        $taskHaveConsumeds = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.id' => $requests),
            'fields' => array('id', 'project_task_id')
        ));
        unset($requests);
        /**
         * $idProjectTasks: id cac project task ma employee request trong khoang thoi gian $start -> $end date.
         * $idActivityTasks: id cac activity task ma employee request trong khoang thoi gian $start -> $end date.
         */
        $idProjectTasks = $idActivityTasks = array();
        if(!empty($taskHaveConsumeds)){
            foreach($taskHaveConsumeds as $index => $value){
                if(!empty($value)){
                    $idProjectTasks[] = $value;
                } else {
                    $idActivityTasks[] = $index;
                }
            }
            unset($taskHaveConsumeds);
        }
        /**
         * $idProjectTaskAssigns: danh sach cac projet task ma employee duoc assign
         * $idActivityTaskAssigns: danh sach cac activity task ma employee duoc assign
         */
        $idProjectTaskAssigns = $idActivityTaskAssigns = array();
        $idProjectTaskAssigns = $this->ProjectTaskEmployeeRefer->find('list',array(
            'conditions' => array('reference_id' => $employee_id, 'is_profit_center' => 0),
            'recursive' => -1,
            'fields' => array('id', 'project_task_id')
        ));
        $idActivityTaskAssigns = $this->ActivityTaskEmployeeRefer->find('list',array(
            'conditions' => array('reference_id' => $employee_id, 'is_profit_center' => 0),
            'recursive' => -1,
            'fields' => array('id', 'activity_task_id')
        ));
        /**
         * Merge project task duoc consumed va project task duoc assign
         */
        $idProjectTasks = array_merge($idProjectTasks, $idProjectTaskAssigns);
        $idProjectTasks = !empty($idProjectTasks) ? array_unique($idProjectTasks) : array();
        /**
         * Merge activity task duoc consumed va activity task duoc assign
         */
        $idActivityTasks = array_merge($idActivityTasks, $idActivityTaskAssigns);
        $idActivityTasks = !empty($idActivityTasks) ? array_unique($idActivityTasks) : array();
        unset($idProjectTaskAssigns);
        unset($idActivityTaskAssigns);
        /** ********************************* BAT DAU XU LY O DAY ****************************************************************/
        /**
         * Tu project Task id...lay tat ca cac id cua activity task linked....
         */
        $idTaskLinked = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $idProjectTasks),
            'fields' => array('project_task_id', 'id')
        ));
        /**
         * List cac task id co request va assign lien quan de employee
         */
        $groupIdTasks = array_merge($idActivityTasks, $idTaskLinked);
        unset($idTaskLinked);
        /**
         * Lay tat ca nhung request cua cac task id nay
         */
        $requests = $this->ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array('id', 'date', 'employee_id', 'task_id', 'value'),
            'conditions'    => array(
                'status'        => 2,
                'activity_id'   => 0,
                'company_id'    => $company_id,
                'task_id'       => $groupIdTasks,
                'NOT'           => array('value' => 0, "task_id" => null),
            )
        ));
        /**
         * $totalConsumedOfTasks: tinh tong consumed cua 1 task theo tung employee va ngay thang nam
         */
        $totalConsumedOfTasks = array();
        if(!empty($requests)){
            foreach($requests as $request){
                $dx = $request['ActivityRequest'];
                if(!isset($totalConsumedOfTasks[$dx['task_id']][$dx['employee_id']][$dx['date']])){
                    $totalConsumedOfTasks[$dx['task_id']][$dx['employee_id']][$dx['date']] = 0;
                }
                $totalConsumedOfTasks[$dx['task_id']][$dx['employee_id']][$dx['date']] += $dx['value'];
            }
            unset($requests);
        }
        /**
         * $keyTasks: Lay cac Id cua Activity Task. Kiem tra xem Id nao co linked voi Project Task
         */
        $keyTasks = !empty($totalConsumedOfTasks) ? array_keys($totalConsumedOfTasks) : array();
        $getActivityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.id' => $keyTasks),
            'fields' => array('id', 'project_task_id')
        ));
        $totalConsumedOfProjectTasks = $totalConsumedOfActivityTasks = array();
        if(!empty($getActivityTasks)){
            foreach($getActivityTasks as $index => $value){
                if(!empty($value)){
                    $totalConsumedOfProjectTasks[$value] = !empty($totalConsumedOfTasks[$index]) ? $totalConsumedOfTasks[$index] : 0;
                } else {
                    $totalConsumedOfActivityTasks[$index] = !empty($totalConsumedOfTasks[$index]) ? $totalConsumedOfTasks[$index] : 0;
                }
            }
            unset($getActivityTasks);
        }
        /**
         * Lay tat ca nhung request cua cac activity.
         */
        $previous = $this->ActivityRequest->find('all',array(
            'recursive'     => -1,
            'fields'        => array('id', 'date', 'employee_id', 'value', 'activity_id'),
            'conditions'    => array(
                'employee_id' => $employee_id,
                'status'        => 2,
                'date BETWEEN ? AND ? ' => array(strtotime($start), strtotime($end)),
                'company_id'    => $company_id,
                'NOT'           => array('value' => 0, 'activity_id' => 0),
            )
        ));
        /**
         * $previousConsumedOfEmployees: tinh tong request cua activity cua employee theo ngay thang nam
         */
        $previousConsumedOfEmployees = $tmpAcHaveConsumeds = $previousConsumedOfEmployeeByYears = array();
        if(!empty($previous)){
            foreach($previous as $previou){
                $dx = $previou['ActivityRequest'];
                if(!isset($tmpAcHaveConsumeds[$dx['date']])){
                    $tmpAcHaveConsumeds[$dx['date']] = 0;
                }
                $tmpAcHaveConsumeds[$dx['date']] += $dx['value'];
                $time = !empty($dx['date']) ? date('Y-M', $dx['date']) : 0;
                if(!isset($previousConsumedOfEmployees[$dx['activity_id']][$time])){
                    $previousConsumedOfEmployees[$dx['activity_id']][$time] = 0;
                }
                $previousConsumedOfEmployees[$dx['activity_id']][$time] += $dx['value'];

                $year = !empty($dx['date']) ? date('Y', $dx['date']) : 0;
                 if(!isset($previousConsumedOfEmployeeByYears[$dx['activity_id']][$year])){
                    $previousConsumedOfEmployeeByYears[$dx['activity_id']][$year] = 0;
                }
                $previousConsumedOfEmployeeByYears[$dx['activity_id']][$year] += $dx['value'];
            }
        }
        /**
         * Lay thong tin cac Project Task co lien quan den employee trong khoang thoai gian $start -> $end nay
         */
        $this->loadModel('Project');
        $listProjectOppotunity = $this->Project->find('list',array(
            'recursive' => -1,
            'conditions' => array(
                'OR'=>array('Project.id' => $this->data['projectId'],'Project.category <>'=>2,
                              'AND'=>array(
                                    'Project.category'=>2,
                                    'Project.is_staffing'=>1
                                )
                        )
            ),
            'fields'=>array('id','id')
        ));
        $projectTasks = $this->ProjectTask->find('all', array(
            'conditions' => array(
                'ProjectTask.id' => $idProjectTasks,
                'ProjectTask.project_id'=>$listProjectOppotunity,
                'OR' => array(
                    'ProjectTask.task_start_date BETWEEN ? AND ?' => array($start, $end),
                    'ProjectTask.task_end_date BETWEEN ? AND ?' => array($start, $end),
                    'AND' => array(
                        'ProjectTask.task_start_date < ?' => array($start),
                        'ProjectTask.task_end_date > ?' => array($start)
                    ),
                    'AND' => array(
                        'ProjectTask.task_start_date < ?' => array($end),
                        'ProjectTask.task_end_date > ?' => array($end)
                    ),
                    'AND' => array(
                        'ProjectTask.task_start_date < ?' => array($start),
                        'ProjectTask.task_end_date > ?' => array($end)
                    )
                ),
            ),
            'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload', 'project_id','is_nct')
        ));
        $groupIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
        $parentIds = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('parent_id' => $groupIds),
            'fields' => array('id', 'parent_id')
        ));
        $parentIds = !empty($parentIds) ? array_unique($parentIds) : array();
        /**
         * Phan chia workload cho tung employee va tung Profit Center theo tung Task
         */
        $workloadOfEmployees = $workloadOfProfitCenters = array();
        foreach($projectTasks as $projectTask){
            $_taskId = $projectTask['ProjectTask']['id'];
            if(in_array($_taskId, $parentIds)){
                // do nothing
            } else {
                $_endDate = ($projectTask['ProjectTask']['task_end_date'] != '0000-00-00') ? strtotime($projectTask['ProjectTask']['task_end_date']) : time();
                $_startDate = ($projectTask['ProjectTask']['task_start_date'] != '0000-00-00') ? strtotime($projectTask['ProjectTask']['task_start_date']) : $_endDate;
                $_workload = $projectTask['ProjectTask']['estimated'];
                $_overload = $projectTask['ProjectTask']['overload'];
                $is_nct = $projectTask['ProjectTask']['is_nct'];
                if(!empty($projectTask['ProjectTaskEmployeeRefer'])){
                    $taskRefers = $projectTask['ProjectTaskEmployeeRefer'];
                    $gOverload = $this->Lib->caculateGlobal($_overload, count($taskRefers));
                    $overloads = $gOverload['original'];
                    $remainder = $gOverload['remainder'];
                    $numberOfRemainder = $gOverload['number'];
                    end($taskRefers);
                    $endKey = key($taskRefers);
                    $i=0;
                    foreach($taskRefers as $key => $taskRefer){
                        $referId = $taskRefer['reference_id'];
                        if($taskRefer['is_profit_center'] == 0){
                            $workloadOfEmployees[$_taskId][$referId]['startDate'] = $_startDate;
                            $workloadOfEmployees[$_taskId][$referId]['endDate'] = $_endDate;
                            $workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads;
                            if($i < $numberOfRemainder){
                                $workloadOfEmployees[$_taskId][$referId]['workload'] = $taskRefer['estimated'] + $overloads + $remainder;
                            }
                            $workloadOfEmployees[$_taskId][$referId]['is_nct'] = $is_nct;
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
        unset($workloadOfEmployees);
        unset($workloadOfProfitCenters);
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
            unset($dataEmployeesNotAffecteds);
        }
        /**
         * Dua consumed vao task
         */
        if(!empty($totalConsumedOfProjectTasks)){
            foreach($totalConsumedOfProjectTasks as $taskId => $totalConsumedOfProjectTask){
                foreach($totalConsumedOfProjectTask as $employ => $values){
                    $dataEmployees[$taskId][$employ]['consumed'] = !empty($values) ? array_sum($values) : 0;
                }
            }
        }
        $_workloadEmployees = $this->_recursiveTask($dataEmployees, null, array());
        unset($dataEmployees);
        $workloadEmployees = array();
        if(!empty($_workloadEmployees)){
            foreach($_workloadEmployees as $taskId => $workloadEmployee){
                foreach($workloadEmployee as $employ => $values){
                    $_end = !empty($values['endDate']) ? $values['endDate'] : time();
                    $_start = !empty($values['startDate']) ? $values['startDate'] : $_end;
                    if($_start > $_end){
                        $_end = $_start;
                    }
                    $_workloadEmployees[$taskId][$employ]['startDate'] = $_start;
                    $_workloadEmployees[$taskId][$employ]['endDate'] = $_end;
                    if($employ == $employee_id){
                        $workloadEmployees[$taskId][$employ] = $_workloadEmployees[$taskId][$employ];
                    }
                }
            }
        }
        $endDataPorjects = $this->_calculAvailability($workloadEmployees, $totalConsumedOfProjectTasks);

        /**
         * Lay thong tin cac Acitity Task co lien quan den employee trong khoang thoai gian $start -> $end nay
         */
        $activityTasks = $this->ActivityTask->find('all', array(
            'conditions' => array(
                'ActivityTask.id' => $idActivityTasks,
                'OR' => array(
                    'ActivityTask.task_start_date BETWEEN ? AND ?' => array(strtotime($start), strtotime($end)),
                    'ActivityTask.task_end_date BETWEEN ? AND ?' => array(strtotime($start), strtotime($end)),
                    'AND' => array(
                        'ActivityTask.task_start_date < ?' => array(strtotime($start)),
                        'ActivityTask.task_end_date > ?' => array(strtotime($start))
                    ),
                    'AND' => array(
                        'ActivityTask.task_start_date < ?' => array(strtotime($end)),
                        'ActivityTask.task_end_date > ?' => array(strtotime($end))
                    ),
                    'AND' => array(
                        'ActivityTask.task_start_date < ?' => array(strtotime($start)),
                        'ActivityTask.task_end_date > ?' => array(strtotime($end))
                    )
                ),
            ),
            'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'parent_id', 'overload', 'activity_id')
        ));
        $totalIdTasks = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $parentIdTasks =  $this->ActivityTask->find('list', array(
            'recursive' => 1,
            'conditions' => array('parent_id' => $totalIdTasks),
            'fields' => array('id', 'parent_id')
        ));
        $parentIdTasks = !empty($parentIdTasks) ? array_unique($parentIdTasks) : array();
        /**
         * Phan chia workload cho tung employee va tung Profit Center theo tung Task
         */
        $workloadOfEmployees = $workloadOfProfitCenters = array();
        foreach($activityTasks as $activityTask){
            $_taskId = $activityTask['ActivityTask']['id'];
            if(in_array($_taskId, $parentIdTasks)){
                // do nothing
            } else {
                $_endDate = !empty($activityTask['ActivityTask']['task_end_date']) ? $activityTask['ActivityTask']['task_end_date'] : time();
                $_startDate = !empty($activityTask['ActivityTask']['task_start_date']) ? $activityTask['ActivityTask']['task_start_date'] : $_endDate;

                $_workload = $activityTask['ActivityTask']['estimated'];
                $_overload = $activityTask['ActivityTask']['overload'];
                if(!empty($activityTask['ActivityTaskEmployeeRefer'])){
                    $taskRefers = $activityTask['ActivityTaskEmployeeRefer'];
                    $gOverload = $this->Lib->caculateGlobal($_overload, count($taskRefers));
                    $overloads = $gOverload['original'];
                    $remainder = $gOverload['remainder'];
                    $numberOfRemainder = $gOverload['number'];
                    end($taskRefers);
                    $endKey = key($taskRefers);
                    $i=0;
                    foreach($taskRefers as $key => $taskRefer){
                        $referId = $taskRefer['reference_id'];
                        //MODIFY BY VINGUYEN 18/10/2014 : APPLY FOR NEW FUNCTION CACULATE WITH ALGORITHM EXACTLY
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
                        //END
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
            unset($dataEmployeesNotAffecteds);
        }
        /**
         * Dua consumed vao task
         */
        if(!empty($totalConsumedOfActivityTasks)){
            foreach($totalConsumedOfActivityTasks as $taskId => $totalConsumedOfActivityTask){
                foreach($totalConsumedOfActivityTask as $employ => $values){
                    $dataEmployees[$taskId][$employ]['consumed'] = !empty($values) ? array_sum($values) : 0;
                }
            }
        }
        $_workloadEmployeeActivityTasks = $this->_recursiveTask($dataEmployees, null, array());
        unset($dataEmployees);
        $workloadEmployeeActivityTasks = array();
        if(!empty($_workloadEmployeeActivityTasks)){
            foreach($_workloadEmployeeActivityTasks as $taskId => $_workloadEmployeeActivityTask){
                foreach($_workloadEmployeeActivityTask as $employ => $values){
                    $_end = !empty($values['endDate']) ? $values['endDate'] : time();
                    $_start = !empty($values['startDate']) ? $values['startDate'] : $_end;
                    if($_start > $_end){
                        $_end = $_start;
                    }
                    $_workloadEmployeeActivityTasks[$taskId][$employ]['startDate'] = $_start;
                    $_workloadEmployeeActivityTasks[$taskId][$employ]['endDate'] = $_end;
                    if($employ == $employee_id){
                        $workloadEmployeeActivityTasks[$taskId][$employ] = $_workloadEmployeeActivityTasks[$taskId][$employ];
                    }
                }
            }
        }
        $endDataActivities = $this->_calculAvailability($workloadEmployeeActivityTasks, $totalConsumedOfActivityTasks, 'ActivityTask');
        return array($endDataPorjects, $endDataActivities, $previousConsumedOfEmployees, $tmpAcHaveConsumeds, $previousConsumedOfEmployeeByYears);
    }

    /**
      * Tinh workload, consumed va previous task cua tung employee theo tung thang
      *
      * @access private
      * @return array()
      * @author HuuPC
      */
     public function getNctWorkload($taskId, $employee_id, $startdate, $enddate, $keyword = 'ProjectTask'){
        $this->loadModel('NctWorkload');
        if($keyword == 'ProjectTask')
        {
            $keywordTask = 'project_task_id';
        } else {
            $keywordTask = 'activity_task_id';
        }
        $workloads = $this->NctWorkload->find('all', array(
            'conditions' => array(
                'reference_id' => $employee_id,
                $keywordTask => $taskId,
                'is_profit_center' => 0,
                'estimated <>' => 0,
                'AND' => array(
                    'OR' => array(
                        'NctWorkload.task_date BETWEEN ? AND ?' => array($startdate, $enddate),
                        'AND' => array(
                            'NctWorkload.task_date <=' => $startdate ,
                            'NctWorkload.task_date >=' => $enddate
                        ),
                    )
                ),
            ),
            'fields' => array('SUM(NctWorkload.estimated) as estimated','DATE_FORMAT(NctWorkload.task_date, "01-%m-%Y") as date'),
            'group' => 'DATE_FORMAT(NctWorkload.task_date, "%m-%Y") ;',
            'recursive' => -1
        ));

        $_results = Set::combine($workloads,'{n}.0.date','{n}.0.estimated');
        $results = array();
        foreach($_results as $time=>$val)
        {
            $results[strtotime($time)]['estimated'] = floatval($val);
        }
        return $results;
    }
     private function _calculAvailability($workloads = array(), $totalConsumedTasks = array(), $keyword = 'ProjectTask'){
        $datas = array();
        if(!empty($workloads)){
            $dataFirsts = $dataSeconds = array();
            foreach($workloads as $taskId => $workload){

                foreach($workload as $id => $values){
                    $_end = !empty($values['endDate']) ? $values['endDate'] : time();
                    $_start = !empty($values['startDate']) ? $values['startDate'] : $_end;
                    if($_start > $_end){
                        $_end = $_start;
                    }
                    $minDate = !empty($_start) ? date('d', $_start) : '';
                    $minMonth = !empty($_start) ? date('m', $_start) : '';
                    $minYear = !empty($_start) ? date('Y', $_start) : '';
                    $maxMonth = !empty($_end) ? date('m', $_end) : '';
                    $maxYear = !empty($_end) ? date('Y', $_end) : '';
                    //IF NCT
                    if(isset($values['is_nct']) && $values['is_nct'] == 1)
                    {
                        $workloadFirsts = array();
                        $workloadFirsts[$id] = $this->getNctWorkload($taskId, $id,date('Y-m-d',$_start),date('Y-m-d',$_end),$keyword);
                    }
                    else
                    {
                        $workloadSeconds = array();
                        if(!isset($values['workload'])||empty($values['workload'])) $values['workload'] = 0;
                        /*$values['workload'] = $values['workload'] - array_sum($totalConsumedTasks[$taskId][$id]);
                        foreach($totalConsumedTasks[$taskId][$id] as $time => $val){
                            $workloadSeconds[$id][$time]['estimated'] = $val;
                        }*/
                        $values['workload'] = $values['workload'];

                        /*$diffDate = $this->_diffDate($_start, $_end);
                        if($diffDate == 0){
                            $estis = $values['workload'];
                        } else {
                            $gEstis = $this->Lib->caculateGlobal($values['workload'], $diffDate);
                            $estis = $gEstis['original'];
                            $remainder = $gEstis['remainder'];
                            $numberOfRemainder = $gEstis['number'];
                        }*/

                        $workDaysByMonths=$this->ActivityTask->getWorkingDaysByMonths($_start, $_end);

                        //$WORKDAYS=$this->ActivityTask->getWorkingDays($_start, $_end);
                        $WORKDAYS = $workDaysByMonths['total'];
                        $workDaysByMonths = $workDaysByMonths['result'];
                        $WORKLOADBYDAY=$this->ActivityTask->caculateGlobal($values['workload'], $WORKDAYS*2);

                        $estis = $WORKLOADBYDAY['original'];
                        $remainder = $WORKLOADBYDAY['remainder'];
                        $numberOfRemainder = $WORKLOADBYDAY['number'];


                        $workloadFirsts = array();
                        $i=0;
                        while($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
                            //MODIFY BY VINGUYEN 18/10/2014 : APPLY FOR NEW FUNCTION CACULATE WITH ALGORITHM EXACTLY
                            $TIME=strtotime('01-'. $minMonth .'-'. $minYear);
                            /*$workloadFirsts[$id][$_DATE]['estimated'] = $estis;
                            if($minYear == $maxYear && $minMonth == $maxMonth){
                                $workloadFirsts[$id][$_DATE]['estimated'] = $estis + $remainder;
                            }*/
                            $_workDaysByMonths = $workDaysByMonths[$TIME];
                            $workloadFirsts[$id][$TIME]['estimated'] = $estis*$_workDaysByMonths['workDaysInMonth']*2;
                            if($_workDaysByMonths['indexPlus'] <= $numberOfRemainder)
                            {
                                $temp = $numberOfRemainder - $_workDaysByMonths['indexPlus'];
                                if($temp >= $_workDaysByMonths['workDaysInMonth'])
                                {
                                    $workloadFirsts[$id][$TIME]['estimated'] += $_workDaysByMonths['workDaysInMonth']*$remainder;
                                }
                                elseif($temp <= $_workDaysByMonths['workDaysInMonth'])
                                {
                                    $workloadFirsts[$id][$TIME]['estimated'] += $temp*$remainder;
                                }
                            }
                            $minMonth++;
                            if ($minMonth == 13) {
                                $minMonth = 1;
                                $minYear++;
                            }
                            $i++;
                            //END
                        }
                    }
                    // $dataSeconds[$taskId] = $workloadSeconds;
                    $dataFirsts[$taskId] = $workloadFirsts;
                }
            }
            if(!empty($dataFirsts)){
                foreach($dataFirsts as $taskId => $dataFirst){
                    foreach($dataFirst as $emp => $values){
                        foreach($values as $time => $value){
                            if(!isset($datas[$taskId][$time])){
                                $datas[$taskId][$time] = 0;
                            }
                            $datas[$taskId][$time] += $value['estimated'];
                        }
                    }
                }
            }
            // if(!empty($dataSeconds)){
            //     foreach($dataSeconds as $taskId => $dataSecond){
            //         foreach($dataSecond as $emp => $values){
            //             foreach($values as $time => $value){
            //                 if(!isset($datas[$taskId][$time])){
            //                     $datas[$taskId][$time] = 0;
            //                 }
            //                 $datas[$taskId][$time] += $value['estimated'];
            //             }
            //         }
            //     }
            // }
        }
        return $datas;
     }
     /**
      * Phan availability chi tiet theo thang
      */
    public function getVocationDetailByMonth($employee_id = null, $start = null, $end = null){
        $this->layout = 'ajax';
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $this->loadModel('Activity');
        $this->loadModel('ActivityTask');
        $this->loadModel('Family');
        $result = array();
		// listYearDatas
        $employee = $this->_getEmpoyee();
        $ratio = $this->employee_info['Company']['ratio'];
		if(empty($ratio)) $ratio = 1;
        list($listPTasks, $listATasks, $listActivityHaveConsumeds, $tmpAcHaveConsumeds, $previousConsumedOfEmployeeByYears) = $this->_availabilityDetail($employee_id, $start, $end, $employee['company_id']);
        $dayOffs = $this->_availabilityHolidays($employee_id, $start, $end, $employee['company_id']);
		// var_dump($ratio); 
        $vocations = $vocationByYears = $vocationByWeeks = array();
        if(!empty($dayOffs)){
            /**
             * Tinh toan vacation/ ngay nghi cua 1 thang.
             */
            ksort($dayOffs);
            foreach($dayOffs as $time => $dayOff){
                //2016-11-04 tinh ngay nghi theo raito.
                $dayOff = $dayOff* $ratio;
                $week = date('W', $time);
                $years = !empty($time) ? date('Y', $time) : '';
                $months = !empty($time) ? date('M', $time) : '';
                if(!isset($vocations[$years][$months])){
                    $vocations[$years][$months] = 0;
                }
                $vocations[$years][$months] += $dayOff;
                if(!isset($vocationByYears[$years])){
                    $vocationByYears[$years] = 0;
                }
                $vocationByYears[$years] += $dayOff;

                if(!isset($vocationByWeeks[$months . '/' . $years][$week])){
                    $vocationByWeeks[$months . '/' . $years][$week] = 0;
                }
                $vocationByWeeks[$months . '/' . $years][$week] += $dayOff;
            }
        }
		
        $listProjects = $listActivities = $endDataProjects = $endDataProjectYears = $endDataActivities = $endDataActivityYears = $nameTasks = $nameATasks = $priorityTasks = $priorityATasks = array();
        $projectHaveLinkeds = $resetATasks = $resetPTasks = array();
        $projectOfTasks = $dataPTasks = $dataPTaskByYears = array();
        if(!empty($listPTasks)){
            /**
             * Lay ten cua project task theo $listPTasks
             * Lay cac project cua $listPTasks
             */
            $nameTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => array_keys($listPTasks)),
                'fields' => array('id', 'task_title', 'project_id', 'task_priority_id')
            ));
			// debug($nameTasks); 
			// exit;
            $projectIds = !empty($nameTasks) ? array_unique(Set::classicExtract($nameTasks, '{n}.ProjectTask.project_id')) : array();
            $projectOfTasks = !empty($nameTasks) ? Set::combine($nameTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
            $priorityTasks = !empty($nameTasks) ? Set::combine($nameTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.task_priority_id') : array();
            $nameTasks = !empty($nameTasks) ? Set::combine($nameTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.task_title') : array();
            $listProjects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $projectIds),
                'fields' => array('id', 'project_name', 'activity_id')
            ));
            $listProjectLs = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $projectIds,'Project.category <>' =>2),
                'fields' => array('id', 'project_name', 'activity_id')
            ));
            $projectHaveLinkeds = !empty($listProjectLs) ? array_unique(Set::combine($listProjectLs, '{n}.Project.id', '{n}.Project.activity_id')) : array();
            $listProjects = !empty($listProjects) ? Set::combine($listProjects, '{n}.Project.id', '{n}.Project.project_name') : array();
            foreach($listPTasks as $taskId => $listPTask){
                foreach($listPTask as $time => $values){
                    if(strtotime($start) <= $time && $time <= strtotime($end)){
                        $resetATasks[$time] = $values;
                        $_year = !empty($time) ? date('Y', $time) : 0;
                        $time = !empty($time) ? date('Y-M', $time) : 0;
                        if(!isset($dataPTasks[$taskId][$time])){
                            $dataPTasks[$taskId][$time] = 0;
                        }
                        $dataPTasks[$taskId][$time] += $values;

                        if(!isset($dataPTaskByYears[$taskId][$_year])){
                            $dataPTaskByYears[$taskId][$_year] = 0;
                        }
                        $dataPTaskByYears[$taskId][$_year] += $values;
                    }
                }
            }
        }
        $activityIds = !empty($listActivityHaveConsumeds) ? array_keys($listActivityHaveConsumeds) : array();
        $activityOfTasks = array();
        $dataATasks = $dataATaskYears = array();
        if(!empty($listATasks)){
            $nameATasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.id' => array_keys($listATasks)),
                'fields' => array('id', 'name', 'activity_id', 'task_priority_id')
            ));
            $_activityIds = !empty($nameATasks) ? array_unique(Set::classicExtract($nameATasks, '{n}.ActivityTask.activity_id')) : array();
            $activityIds = !empty($_activityIds) ? array_merge($activityIds, $_activityIds) : $activityIds;
            $activityOfTasks = !empty($nameATasks) ? Set::combine($nameATasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
            $priorityATasks = !empty($nameATasks) ? Set::combine($nameATasks, '{n}.ActivityTask.id', '{n}.ActivityTask.task_priority_id') : array();
            $nameATasks = !empty($nameATasks) ? Set::combine($nameATasks, '{n}.ActivityTask.id', '{n}.ActivityTask.name') : array();
            foreach($listATasks as $taskId => $listATask){
                foreach($listATask as $time => $values){
                    if(strtotime($start) <= $time && $time <= strtotime($end)){
                        $resetPTasks[$time] = $values;
                        $_year = !empty($time) ? date('Y', $time) : 0;
                        $time = !empty($time) ? date('Y-M', $time) : 0;
                        if(!isset($dataATasks[$taskId][$time])){
                            $dataATasks[$taskId][$time] = 0;
                        }
                        $dataATasks[$taskId][$time] += $values;
                        if(!isset($dataATaskYears[$taskId][$_year])){
                            $dataATaskYears[$taskId][$_year] = 0;
                        }
                        $dataATaskYears[$taskId][$_year] += $values;
                    }
                }
            }
        }
        $activityIds = !empty($projectHaveLinkeds) ? array_unique(array_merge($activityIds, $projectHaveLinkeds)) : $activityIds;
        $getFamilyOfActivities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array('Activity.id' => $activityIds),
            'fields' => array('id', 'family_id')
        ));
        $families = $this->Family->find('list', array(
            'recursive' => -1,
            'conditions' => array('Family.id' => $getFamilyOfActivities)
        ));
        $families[-1] = __('Opportunity',true);
        /**
         *
         */
        $listMonthDatas = $listYearDatas = array();
        /**
         * Nhom cac task theo project va family
         */
        foreach($projectOfTasks as $taskId => $project){
            $activityLined = !empty($projectHaveLinkeds[$project]) ? $projectHaveLinkeds[$project] : 0;
            $familyId = !empty($getFamilyOfActivities[$activityLined]) ? $getFamilyOfActivities[$activityLined] : '-1';
            // data month
            $_data = !empty($dataPTasks[$taskId]) ? $dataPTasks[$taskId] : array();
            $endDataProjects[$project][$taskId] = $_data;
            if(!isset($listMonthDatas[$familyId]['pr-'.$project][$taskId])){
                $listMonthDatas[$familyId]['pr-'.$project][$taskId] = array();
            }
            $listMonthDatas[$familyId]['pr-'.$project][$taskId] = $_data;
            // data year
            $_dataYear = !empty($dataPTaskByYears[$taskId]) ? $dataPTaskByYears[$taskId] : array();
            $endDataProjectYears[$project][$taskId] = $_dataYear;
            if(!isset($listYearDatas[$familyId]['pr-'.$project][$taskId])){
                $listYearDatas[$familyId]['pr-'.$project][$taskId] = array();
            }
            $listYearDatas[$familyId]['pr-'.$project][$taskId] = $_dataYear;
        }
        /**
         * Nhom cac task theo activity va family
         */
        foreach($activityOfTasks as $taskId => $acti){
            $familyId = !empty($getFamilyOfActivities[$acti]) ? $getFamilyOfActivities[$acti] : '-1';
            // data month
            $_data = !empty($dataATasks[$taskId]) ? $dataATasks[$taskId] : array();
            $endDataActivities[$acti][$taskId] = $_data;
            if(!isset($listMonthDatas[$familyId]['ac-'.$acti][$taskId])){
                $listMonthDatas[$familyId]['ac-'.$acti][$taskId] = array();
            }
            $listMonthDatas[$familyId]['ac-'.$acti][$taskId] = $_data;
            // data year
            $_dataYear = !empty($dataATaskYears[$taskId]) ? $dataATaskYears[$taskId] : array();
            $endDataActivityYears[$acti][$taskId] = $_dataYear;
            if(!isset($listYearDatas[$familyId]['ac-'.$acti][$taskId])){
                $listYearDatas[$familyId]['ac-'.$acti][$taskId] = array();
            }
            $listYearDatas[$familyId]['ac-'.$acti][$taskId] = $_dataYear;
        }
        /**
         * Activity consumed of month
         */
        if(!empty($listActivityHaveConsumeds)){
            foreach($listActivityHaveConsumeds as $acti => $listActivityHaveConsumed){
                $familyId = !empty($getFamilyOfActivities[$acti]) ? $getFamilyOfActivities[$acti] : '-1';
                if(!isset($listMonthDatas[$familyId]['cs-'.$acti])){
                    $listMonthDatas[$familyId]['cs-'.$acti] = array();
                }
                $listMonthDatas[$familyId]['cs-'.$acti] = $listActivityHaveConsumed;
            }
        }
		// debug($listMonthDatas); 
		// exit;
        /**
         * Activity consumed of year
         */
        if(!empty($previousConsumedOfEmployeeByYears)){
            foreach($previousConsumedOfEmployeeByYears as $acti => $previousConsumedOfEmployeeByYear){
                $familyId = !empty($getFamilyOfActivities[$acti]) ? $getFamilyOfActivities[$acti] : '-1';
                if(!isset($listYearDatas[$familyId]['cs-'.$acti])){
                    $listYearDatas[$familyId]['cs-'.$acti] = array();
                }
                $listYearDatas[$familyId]['cs-'.$acti] = $previousConsumedOfEmployeeByYear;
            }
        }
        /**
         * Danh sach activity name
         */
        $listActivities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array('Activity.id' => $activityIds),
            'fields' => array('id', 'short_name')
        ));
        /**
         * Lay danh sach priority cua project cua moi cong ty
         */
        $this->loadModel('ProjectPriority');
        $projectPriorities = $this->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employee['company_id']),
            'fields' => array('id', 'priority')
        ));
        /**
         * Tinh toan availability
         */
        $availabilities = array();
        if(!empty($dayOffs)){
            foreach($dayOffs as $time => $dayOff){
                if(strtolower(date('l', $time)) == 'sunday' || strtolower(date('l', $time)) == 'saturday'){
                    //do nothing
                } else {
                    if($dayOff == 1){
                        $availabilities[$time] = 0;
                    } else {
                        $aTask = !empty($resetATasks[$time]) ? $resetATasks[$time] : 0;
                        $pTask = !empty($resetPTasks[$time]) ? $resetPTasks[$time] : 0;
                        $actis = !empty($tmpAcHaveConsumeds[$time]) ? $tmpAcHaveConsumeds[$time] : 0;
                        $sums = $aTask + $pTask + $actis;
                        if($dayOff == 0){
                            $availabilities[$time] = round(1-$sums, 2);
                        } else {
                            $availabilities[$time] = round(0.5-$sums, 2);
                        }
                    }
                }
            }
        }
        $availabilityMonths = $availabilityYears = array();
        if(!empty($availabilities)){
            foreach($availabilities as $time => $availability){
                if(strtolower(date('l', $time)) == 'sunday' || strtolower(date('l', $time)) == 'saturday'){
                    //do nothing
                } else {
                    $_year = !empty($time) ? date('Y', $time) : '';
                    if(!isset($availabilityYears[$_year])){
                        $availabilityYears[$_year] = 0;
                    }
                    $availabilityYears[$_year] += $availability;

                    $time = !empty($time) ? date('Y-M', $time) : '';
                    if(!isset($availabilityMonths[$time])){
                        $availabilityMonths[$time] = 0;
                    }
                    $availabilityMonths[$time] += $availability;
                }
            }
        }
        /**
         * Tinh working day of month and working day of year
         */
        $startDate = !empty($start) ? strtotime($start . ' 00:00:00') : 0;
        $endDate = !empty($end) ? strtotime($end . ' 00:00:00') : 0;
		// $startDate = mktime(0, 0, 0, date("m", $start)-1, date("d", $start), date("Y", $start));
		// $endDate = mktime(0, 0, 0, date("m", $end)+1, date("d", $end), date("Y", $end));
		$employeeIds = array($employee_id);

		list($totalWorkloads, $capacities) = $this->_capacityForEmployee($employeeIds, $startDate, $endDate);
		
		$capacities = !empty($capacities) && !empty($capacities[$employee_id]) ? $capacities[$employee_id] : array();
        $this->loadModel('AbsenceRequest');
        $absences = $this->AbsenceRequest->sumAbsenceByEmployeeAndDate($employee_id, $startDate, $endDate);
        $_startDate = $startDate;
        $_endDate = $endDate;
        $theWorkingDays = $theWorkingDayOfYears = array();
        $this->loadModel('Employee');
        $employeeInfo = $this->Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id'=>$employee_id
                ),
                'fields' => array('Employee.id','Employee.start_date','Employee.end_date')
            ));
        $dayEstablished = $employee['day_established'];
        $val = $employeeInfo['Employee'];
        if(strtotime($val['start_date']) < $dayEstablished)
        {
            $val['start_date'] = $dayEstablished;
        }
        $startTmp = strtotime($val['start_date']);
        $val['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
        $val['end_date'] = $val['end_date'] == '0000-00-00' || $val['end_date'] == '' ?  $endDate : strtotime($val['end_date']);

        while($_startDate <= $_endDate){
            $keyAbsence = date('d_m_Y',$_startDate);
            $totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $_startDate), date("Y", $_startDate));
            //$absens = !empty($absences[$employee_id][$keyAbsence]) ? $absences[$employee_id][$keyAbsence] : 0;
            $_theWorking = 0;
            $absens = 0;
            $num  = 0;
            $_endDateTmp = strtotime('00:00:00 ' . $totalDayOfMonth . '-' . date("m-Y", $_startDate));
            $_countHoliday = count($this->Holiday->get($employee['company_id'], $_startDate, $_endDateTmp));
            $numTmp = $this->_countWorkingDate($_startDate, $totalDayOfMonth);
			$num = !empty($capacities) && !empty($capacities[$_startDate]) ? $capacities[$_startDate]['working'] : 0;
            // 2016-11-04 tinh working day theo raito.
            $_theWorking = $num * $ratio;
            //END
            $years = !empty($_startDate) ? date('Y', $_startDate) : '';
            $months = !empty($_startDate) ? date('M', $_startDate) : '';
            if(!isset($theWorkingDays[$years][$months])){
                $theWorkingDays[$years][$months] = 0;
            }
            $theWorkingDays[$years][$months] += $_theWorking;

            if(!isset($theWorkingDayOfYears[$years])){
                $theWorkingDayOfYears[$years] = 0;
            }
            $theWorkingDayOfYears[$years] += $_theWorking;

            $_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate));
        }
        /**
         * Result by month.
         */
        $result['MonthVocations'] = $vocations;
        $result['MonthWorkingDays'] = $theWorkingDays;
        $result['MonthListProjectTasks'] = $endDataProjects;
        $result['MonthListActivityTasks'] = $endDataActivities;
        $result['MonthActivityHaveConsumeds'] = $listActivityHaveConsumeds;
        $result['MonthAvailabilities'] = $availabilityMonths;
        $result['listMonthDatas'] = $listMonthDatas;
        /**
         * Result by year.
         */
        $result['YearVocations'] = $vocationByYears;
        $result['YearWorkingDays'] = $theWorkingDayOfYears;
        $result['YearListProjectTasks'] = $endDataProjectYears;
        $result['YearListActivityTasks'] = $endDataActivityYears;
        $result['YearActivityHaveConsumeds'] = $previousConsumedOfEmployeeByYears;
        $result['YearAvailabilities'] = $availabilityYears;
        $result['listYearDatas'] = $listYearDatas;
        /**
         * Result global
         */
        $result['ListProjects'] = $listProjects;
        $result['ListActivities'] = $listActivities;
        $result['NameProjectTasks'] = $nameTasks;
        $result['NameActivityTasks'] = $nameATasks;
        $result['PriorityProjectTasks'] = $priorityTasks;
        $result['PriorityActivityTasks'] = $priorityATasks;
        $result['ProjectPriorities'] = $projectPriorities;
        $result['families'] = $families;

        $this->set(compact('result'));
    }
     /**
      * Lay ngay nghi phep va nhung ngay xin nghi cua employee trong 1 khoang thoi gian
      */
     private function _availabilityHolidays($employee_id = null, $start = null, $end = null, $company_id = null){
        $this->loadModel('Holiday');
        $_start = strtotime($start . ' 00:00:00');
        $_end = strtotime($end . ' 00:00:00');
        // $holidays = $this->Holiday->get($company_id, $_start, $_end);
        $absenceRequests = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                'date BETWEEN ? AND ?' => array(strtotime($start), strtotime($end)), 'employee_id' => $employee_id))
            ), '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
        $dayOffOfDays = array();
        while($_start <= $_end){
            if(!isset($dayOffOfDays[$_start])){
                $dayOffOfDays[$_start] = 0;
            }
            if(strtolower(date('l', $_start)) == 'sunday' || strtolower(date('l', $_start)) == 'saturday'){
                //do nothing
            } else {
                // if(in_array($_start, $holidays)){
                //     $dayOffOfDays[$_start] += 1;
                // }
                // else
                if(!empty($absenceRequests[$_start])){
                    if($absenceRequests[$_start]['response_am'] == 'validated'){
                        $dayOffOfDays[$_start] += 0.5;
                    }
                    if($absenceRequests[$_start]['response_pm'] == 'validated'){
                        $dayOffOfDays[$_start] += 0.5;
                    }
                }
            }
            $_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));
        }
        return $dayOffOfDays;
     }

    function export_team_plus(){
        if( !empty($this->data) ){
            $this->CommonExporter->init();
            $data = json_decode($this->data['data'], true);

            // an example of formatter
            $this->CommonExporter->addFormatter('HasBG', function($exporter, $phpExcelSet, $colName, $row, $column){
                $exporter->activeSheet->getStyle($colName . $row)->applyFromArray(array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => trim($column['bg'], '#'))
                    ),
                    'font'  => array(
                        'color' => array('rgb' => $column['color'])
                    )
                ));
                $exporter->apply('decimal', $column, $phpExcelSet, $colName, $row);
                return $column['value'];
            });

            // save
            $this->CommonExporter
                ->setT('Profit center +')   //auto translate
                ->save($data, 'pc_{date}.xls');
        }
        die;
    }
}
?>
