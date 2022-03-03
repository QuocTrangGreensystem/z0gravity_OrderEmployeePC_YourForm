<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ActivityForecastsPreviewController extends AppController {
	/**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityForecastsPreview';
    var $uses = array('ActivityForecasts');
	var $components = array('Lib');
	
	 function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $manage_multiple_resource = isset($this->companyConfigs['manage_multiple_resource']) && !empty($this->companyConfigs['manage_multiple_resource']) ?  true : false;
        $resource_see_him = isset($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) && !empty($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) ?  true : false;
        $this->set(compact('manage_multiple_resource', 'resource_see_him'));
        $this->fileTypes = 'jpg,jpeg,bmp,gif,png,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,xlsm,csv';
        $this->set('fileTypes', $this->fileTypes);
        $this->loadModel('ProfitCenter');
    }
	
	private function checkPermissionTimesheet($e_ID = null, $redirect = true) {
		$canWrite = $canRead = $canManage = 0;
		$isPCManager = 0;
		$userRole = $this->employee_info['CompanyEmployeeReference']['role_id'];
		$current_EID = $this->employee_info['Employee']['id'];
		$currentCompanyID = $this->employee_info['Company']['id'];
		if( empty($e_ID) ) $e_ID = $this->employee_info['Employee']['id'];
		$employee = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $e_ID),
			// 'fields' => array('id','profit_center_id', 'company_id'),
		));
		$company_id = !empty($employee['Employee']['company_id']) ? $employee['Employee']['company_id'] : '';
		// Access to other company data is denied
		if( $company_id != $currentCompanyID) {
			$this->Session->setFlash(__('Access denied', true), 'error');
			if( $this->params['isAjax']){ return array( 0,0,0);}
			$this->redirect(array( 'action' => 'request'));
		}
		$employee['Employee']['fullname'] = $employee['Employee']['first_name'] . ' ' . $employee['Employee']['last_name'];
		switch( $userRole){
			case 2: //Admin 
				$canWrite = 1;
				$canRead = 1;
				$canManage = 1;
			break;
			//PM and Consultant can edit their own timesheets
			case 4: //Consultant
			case 3: //PM
				$canWrite = $canRead = ( $e_ID == $current_EID) ? 1 : 0;
			break;
		}
		if ( $userRole  == 3){
			if( !empty( $this->employee_info['CompanyEmployeeReference']['control_resource'])){
				$canWrite = 1;
				$canRead = 1;
				$canManage = 1;
			}
		}
		$isPCManager = 0;
		if( !$canManage) {
			/*
			* Check PC manager 
			*/
			// Get PC and Manager of PC of  $e_ID
			$this->loadModel('ProfitCenter');
			$profit = !empty($employee['Employee']['profit_center_id']) ? $employee['Employee']['profit_center_id'] : '';
			$profits = $this->ProfitCenter->getPath($profit);
			if(!empty($profits)){
				$this->loadModel('ProfitCenterManagerBackup');
				//Get manager backup profit center
				$backupManagers = $this->ProfitCenterManagerBackup->find('list', array(
					'recursive' => -1,
					'conditions' => array('profit_center_id' => $profit),
					'fields' => array('employee_id', 'employee_id')
				));
				foreach($profits as $p){
					if( ($p['ProfitCenter']['manager_id'] == $this->employee_info['Employee']['id'] || in_array($this->employee_info['Employee']['id'], $backupManagers)) ){
						$canRead = 1;
						$canWrite = 1;
						$isPCManager = 1;
						break;
					}
				}
			}
			$canManage = $isPCManager;
		}
		$this->set(compact('canRead', 'canWrite', 'canManage')); 
		if( empty( $canRead)) {
			$this->Session->setFlash(__('Access denied', true), 'error');
			if( !$this->params['isAjax']){ 
				$this->redirect(array( 'controller' => 'activity_forecasts', 'action' => 'request'));
			}
		}
		return array($canRead, $canWrite, $canManage, $employee['Employee']);
	}
	
	// Huynh Function nay chi viet cho hien thi week timesheet
	// Function nay co 2 view
	// request.ctp
	// request_ajax.ctp
	public function request($fill_week = null){
		// debug($_GET);
		// exit;
		$mem = memory_get_usage();
		$a = time();
		$this->_getListEmployee(); //QUANNV add de lay listEmployees
		$loginEmployee = $this->employee_info;
		$loginEmployeeID = $this->employee_info['Employee']['id'];
		$idEmployee = !empty($_GET['id']) ? $_GET['id'] : $loginEmployeeID;
		list($canRead, $canWrite, $canManage, $employee) = $this->checkPermissionTimesheet($idEmployee); // Z0G 15/10/2019 Permission for timesheet screen
		$managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
		$sendTimesheetPartially = !$managerHour && isset($this->companyConfigs['send_timesheet_partially_filled']) && !empty($this->companyConfigs['send_timesheet_partially_filled']) ?  1 : 0;
		$display_absence = isset($this->companyConfigs['display_absence_tab']) ? $this->companyConfigs['display_absence_tab'] : 1;
		$this->loadModels('ProfitCenter', 'Workday', 'Holiday', 'ActivityRequest', 'ActivityRequestConfirm', 'ActivityRequestCopy', 'ActivityTask', 'Activity', 'ActivityFamily', 'ProjectTaskFavourite', 'AbsenceRequest');
		$this->loadModels('ProjectTask', 'ProjectPhasePlan', 'ProjectPhase', 'ProjectPart');
		$profitCenter = $this->ProfitCenter->find('first',array(
			'recursive' => -1,
			'conditions' => array('id' => $employee['profit_center_id'])
		));
		list($startdate, $enddate) = $this->_parseParams();
		// debug( $startdate);
		// debug( $enddate);
		/* Company data */
		$company_id = $employee['company_id'];
		$profit = $employee['profit_center_id'];
		$ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
		$workdays = $this->Workday->getOptions($company_id);
		// debug( $workdays);
		/* END Company data */
		
		$holidays = $this->Holiday->getOptionHolidays($startdate, $enddate, $company_id);
		// debug( $holidays);
        foreach($holidays as $time => $holiday){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
		$listWorkingDays = array();
		$_start = $startdate;
		while($_start <= $enddate){
			$day = strtolower(date('l', $_start));
			
			$listWorkingDays[$_start] = (int) $workdays[$day];
			$_start = strtotime('+1 day', $_start);
		}
		unset($_start);
		
		// Timesheet Status 
		$requestConfirm = $this->ActivityRequestConfirm->find('first', array(
			'recursive' => -1,
			'fields' => array('id', 'status', 'updated', 'employee_validate'),
			'conditions' => array(
				'employee_id' => $idEmployee, 
				'start' => $startdate, 
				'end' => $enddate
			)
		));
		// debug( $requestConfirm);exit;

		/* Autofill timesheet */
		$requestConfirmStatus = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['status'] : '-1';
		// debug($requestConfirmStatus);
		
		$auto_fill_week = 0;
		$auto_fill_favourite = 0;
		if( $requestConfirmStatus == -1 ){	
			if($fill_week == 'manual'){
				$fillTimesheet = 1;
			}else{
				$filled = $this->ActivityRequestCopy->find('first', array(
					'recursive' => -1,
					'conditions' => array('employee_id' => $idEmployee, 'start' => $startdate, 'end' => $enddate, 'company_id' => $employee['company_id']),
					'fields' => array('copy', 'auto_fill_week', 'auto_fill_favourite')
				));
				$activateCopy = !empty( $employee['activate_copy']) ?  $employee['activate_copy'] : 0;
				$auto_fill_week = !empty($filled) ? (!empty($filled['ActivityRequestCopy']['auto_fill_week']) ? $filled['ActivityRequestCopy']['auto_fill_week'] : 0) : $activateCopy;
				$auto_fill_favourite = !empty($filled) ? (!empty($filled['ActivityRequestCopy']['auto_fill_favourite']) ? $filled['ActivityRequestCopy']['auto_fill_favourite'] : 0) : $activateCopy;
				// $auto_fill_favourite = !empty($filled['ActivityRequestCopy']['auto_fill_favourite']) ? $filled['ActivityRequestCopy']['auto_fill_favourite'] : $activateCopy;

				$haveCopy = $activateCopy ? $filled : 1;
				// debug($haveCopy);
				$fillTimesheet = empty($haveCopy) ? 1 : 0;
			}
			if( $fillTimesheet){				
				$taskAutoFill = $this->dataCopyForecast($idEmployee, $profit, $startdate, $enddate, $isPC = false);
				$taskOfEmployee = !empty($taskAutoFill['results']) ? $taskAutoFill['results'] : array();
				$projectLinkedActivity = !empty($taskAutoFill['projectLinkedActivity']) ? $taskAutoFill['projectLinkedActivity'] : array();
				
				// chi copy o tuan
				/**
				 * Lay ngay copies
				 */
				$dateCopy = '';
				
				$checkDateBeforeUpdates = $this->ActivityRequest->find('count', array(
					'recursive' => -1,
					'conditions' => array(
						'date' => array_keys($listWorkingDays),
						'employee_id' => $idEmployee,
						'company_id' => $employee['company_id'],
					),
					'fields' => array('date', 'id')
				));

				if( empty($checkDateBeforeUpdates )){
					foreach($listWorkingDays as $day => $is_working){
						if($is_working == 1){
							$dateCopy = $day;
							break;
						}
					}
					$dataCopies = array();
					foreach( $taskOfEmployee as $_task){
						if( $_task['is_profit_center'] ) continue;
						if( !empty($_task['project_id'])) 
							$activityId = $projectLinkedActivity[$_task['project_id']];
						else
							$activityId = $_task['activity_id'];
						
						$taskId = $_task['task_id'];
						if(!isset($dataCopies[$activityId])){
							$dataCopies[$activityId] = array();
						}
						$dataCopies[$activityId][] = $taskId;
					}
					
					$saved = array();
					if(!empty($dataCopies)){
						foreach($dataCopies as $dataCopy){
							if(!empty($dataCopy)){
								$dataCopy = array_unique($dataCopy);
								foreach($dataCopy as $idTask){
										$saved[] = array(
											'date' => $startdate,
											'value' => 0,
											'employee_id' => $idEmployee,
											'company_id' => $employee['company_id'],
											'task_id' => $idTask,
											'status' => -1,
											'activity_id' => 0
										);
								}
							}
						}
					}
				}
				
				if(!empty($saved)){
					if($this->ActivityRequest->saveAll($saved)){ // sau khi copy thi lay lai cac request cua tuan
						/**
						 * Tuan nao da copy thi khong cho copy nua. Chi copy 1 lan
						 */
						$this->ActivityRequestCopy->create();
						$this->ActivityRequestCopy->save(array(
							'employee_id' => $idEmployee,
							'start' => $startdate,
							'end' => $enddate,
							'company_id' => $employee['company_id'],
							'auto_fill_week' => 1,
							'auto_fill_favourite' => 0,
						));
						  /** Sau khi save 
						 * Lay nhung request cua tuan nay
						 */
						$_activityRequests = $this->ActivityRequest->find("all", array(
							'recursive' => -1, "conditions" => array('date BETWEEN ? AND ?' => array(
									$startdate, $enddate), 'employee_id' => $idEmployee)));
						 /**
						 * Nhom lai cac gia tri da request
						 */
						$actRequestCopy = $actForecastCopy = $taskRequestCopy = $lisActRequestCopy = array();
						foreach ($_activityRequests as $_activity) {
							$_activity = array_shift($_activity);
							$id = $_activity['activity_id'] . (!empty($_activity['task_id']) ? '-' . $_activity['task_id'] : '');
							$actRequestCopy[$id][$_activity['date']] = $_activity;
							$taskRequestCopy[] = $_activity['task_id'];
							$lisActRequestCopy[] = $_activity['activity_id'];
						}
						$taskRequestCopy = !empty($taskRequestCopy) ? array_unique($taskRequestCopy) : array();
						$activityLists = $this->ActivityTask->find('list', array(
							'recursive' => -1,
							'conditions' => array('ActivityTask.id' => $taskRequestCopy),
							'fields' => array('id', 'activity_id')
						));
						$lisActRequestCopy = !empty($activityLists) ? array_unique(array_merge($lisActRequestCopy, $activityLists)) : array_unique($lisActRequestCopy);
					}
				}
				
			}
		}
		
		/* END Autofill timesheet */
		
		/**
         * Lay nhung request cua tuan nay
         */
		$activityRequests = $this->ActivityRequest->find("all", array(
            'recursive' => -1, 
			'conditions' => array(
				'date BETWEEN ? AND ?' => array($startdate, $enddate),
				'employee_id' => $idEmployee,				
				'company_id' => $company_id
			), 
			'fields' => array(
				'ActivityRequest.id',
				'ActivityRequest.date',
				'ActivityRequest.value',
				'ActivityRequest.task_id',
			)
		));
		 // lay cac thong tin lien quan den activityRequests
		$listTaskRequest = !empty($activityRequests) ? Set::classicExtract( $activityRequests, '{n}.ActivityRequest.task_id') : array();
		$activityRequests = !empty($activityRequests) ? Set::combine( $activityRequests, '{n}.ActivityRequest.date', '{n}.ActivityRequest.value', '{n}.ActivityRequest.task_id') : array(); 
		
		$consumed = $this->ActivityRequest->find('all', array(
				'recursive'     => -1,
				'fields'        => array(
					'id',
					'employee_id',
					'task_id',
					'SUM(value) as value'
				),
				'group'         => array('task_id'),
				'conditions'    => array(
					'status'	=> '2',
					'task_id'	=> $listTaskRequest,
					'company_id'	=> $company_id,
					'value !='	=> 0,
				)
			)
		);
		$consumed = !empty($consumed) ? Set::combine($consumed, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
		// debug( $consumed); exit;
		$activityTasks = $this->ActivityTask->find("all", array(
            'recursive' => -1, 
			'conditions' => array(
				'ActivityTask.id' =>  $listTaskRequest,
			), 
			'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id', 'estimated', 'task_status_id', 'special'),
		));
		
		$listActivities = !empty($activityTasks) ? Set::classicExtract( $activityTasks, '{n}.ActivityTask.activity_id') : array();
		$listProjectTask = !empty($activityTasks) ? Set::classicExtract( $activityTasks, '{n}.ActivityTask.project_task_id') : array();
		$taskFavourites = $this->ProjectTaskFavourite->find('list', array(
			'recursive' => -1, 
			'conditions' => array(
				'ProjectTaskFavourite.task_id' =>  $listProjectTask,
				'ProjectTaskFavourite.employee_id' => $loginEmployeeID,
				'ProjectTaskFavourite.favourite' => 1,
			), 
			'fields' => array(
				'ProjectTaskFavourite.task_id',
				'ProjectTaskFavourite.favourite',
			)
		));
		/* Get Part / Phase
		* input $listProjectTask
		* 
		*/
		// debug( $listProjectTask); 
		// exit;
		$projectTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $listProjectTask
			),
			'fields' => array('id', 'task_title', 'project_planed_phase_id', 'estimated'),
		));
		$projectPlanedPhases = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_planed_phase_id') : array();
		$workloads = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.estimated') : array();
		$project_task_name = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.task_title') : array();
		// debug( $workloads); 
		// debug( $projectPlanedPhases); 
		// exit;
		$projectPhaseParts = $this->ProjectPhasePlan->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($projectPlanedPhases)
			),
			'fields' => array('id', 'project_planed_phase_id', 'project_part_id')
		));
		// debug( $projectPhaseParts); 
		$listPhaseIDs = !empty($projectPhaseParts) ? Set::combine($projectPhaseParts, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id' ) : array();
		$listPartIDs = !empty($projectPhaseParts) ? Set::combine($projectPhaseParts, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id' ) : array();
		// debug( $listPhaseIDs); 
		// debug( $listPartIDs); 
		$listPhases = $this->ProjectPhase->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($listPhaseIDs)
			),
			'fields' => array('id', 'name')
		));
		$listParts = $this->ProjectPart->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($listPartIDs)
			),
			'fields' => array('id', 'title')
		));
		
		
		/* END Get Part / Phase */
		$activities = $this->Activity->find("all", array(
            'recursive' => -1, 
			'conditions' => array(
				'Activity.id' =>  $listActivities,
				'Activity.activated' => 1
			), 
			'fields' => array(
				'Activity.id',
				'Activity.name',
				'Activity.long_name',
				'Activity.short_name',
				'Activity.family_id',
				'Activity.subfamily_id',
				'Activity.activated',
			)
		));
		// debug( $activities);
		$listFamilies = !empty($activities) ? Set::classicExtract( $activities, '{n}.Activity.family_id') : array();		
		$listSubFamilies = !empty($activities) ? Set::classicExtract( $activities, '{n}.Activity.subfamily_id') : array();
		$listFamilies = array_merge( $listFamilies, $listSubFamilies);
		unset( $listSubFamilies);
		$activities = !empty($activities) ? Set::combine( $activities, '{n}.Activity.id', '{n}.Activity') : array();
		$families = $this->ActivityFamily->find('all', array(
			'recursive' => -1, 
			'conditions' => array(
				'ActivityFamily.id' =>  $listFamilies
			), 
			'fields' => array(
				'ActivityFamily.id',
				'ActivityFamily.name',
			)
		));
		$families = !empty($families) ? Set::combine( $families, '{n}.ActivityFamily.id', '{n}.ActivityFamily') : array();
		
		// debug( $listPhases);
		// debug( $taskFavourites); 
		$activityTasks = !empty($activityTasks) ? Set::combine( $activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask') : array();
		
		foreach($activityTasks as $id => $task){
			$project_task_id = !empty($task['project_task_id']) ? $task['project_task_id'] : '';
			$project_planed_phase_id = !empty($projectPlanedPhases[$project_task_id]) ? $projectPlanedPhases[$project_task_id] : '';
			$PhaseID = !empty($listPhaseIDs[$project_planed_phase_id]) ? $listPhaseIDs[$project_planed_phase_id] : '';
			$PartID = !empty($listPartIDs[$project_planed_phase_id]) ? $listPartIDs[$project_planed_phase_id] : '';
			$activityTasks[$id]['phase_name'] = !empty($listPhases[$PhaseID]) ? $listPhases[$PhaseID] : '';
			$activityTasks[$id]['part_name'] = !empty($listParts[$PartID]) ? $listParts[$PartID] : '';
			$activityTasks[$id]['estimated'] = !empty($workloads[$project_task_id]) ? $workloads[$project_task_id] : '';
			$activity_name = $activities[$task['activity_id']]['name'] . '/';
			$p_task_name = $project_task_name[$project_task_id];
			$activityTasks[$id]['name'] = $activity_name = $activities[$task['activity_id']]['name'] . '/' . $p_task_name;
		}
	
		// END lay cac thong tin lien quan den activityRequests
		
		/**
         * END Lay nhung request cua tuan nay
         */			
		// debug( $requestConfirm ); exit;
		/* Lay Capacity */
		$manage_multiple_resource = isset($this->companyConfigs['manage_multiple_resource']) && !empty($this->companyConfigs['manage_multiple_resource']) ?  true : false;
		$isMulti = $manage_multiple_resource && ($employee['external'] == 2);
		$exCapacity = array();
		if( $isMulti ){
			$this->loadModel('EmployeeMultiResource');
			$exCapacity = $this->EmployeeMultiResource->getCapacity($employee['id'], $startdate, $enddate, 'date');
			$exCapacity = !empty($exCapacity) ? $exCapacity[$employee['id']] : array();
		}
		$capacity = array();
		foreach ($listWorkingDays as $day => $is_working) {
			if( $is_working){
				$capacity[$day] = $isMulti ? $exCapacity[$day] : floatval($workdays[strtolower(date('l', $day))]*$ratio);
			}
		}
		
		/** QUANNV 31/12/2019. Ticket #523
		 * check show icon comment.
		*/
		$showActivityForecastComment = ( isset($this->companyConfigs['show_activity_forecast_comment']) ) ?  $this->companyConfigs['show_activity_forecast_comment'] : 0;
		/**
		 * End check.
		*/
		/* Get data for Absence */ 		$companyAbsences = array();
		$absenceRequests = array();
		$canRequestAbsence = array();
		if( $display_absence){
			$this->loadModels('AbsenceRequestConfirm');
			$this->loadModels('AbsenceComment');
			$this->loadModels('AbsenceAttachment');
			// $companyAbsences   = $this->AbsenceRequest->getAbsences($company_id);
			// debug($companyAbsences); 
			// exit;
			// one Week maybe in 2year;
			$currentYear = date('Y');
			$firstDateYear = date('Y', $startdate); // example: week 1 year 2020 then $firstDateYear is 2019 ( startdate = 30/12/2019)
			$avg = intval(($startdate + $enddate)/2);
			$requestYear = date('Y', $avg);
			// debug($currentYear); 
			// debug($firstDateYear); 
			// debug($requestYear); 
			// exit;
			$companyAbsences = $this->AbsenceRequest->getAbsences($company_id, $idEmployee, $firstDateYear);
			// debug($companyAbsences); 
			// exit;
			$absenceRequests = $this->AbsenceRequest->find("all", array(
				'recursive' => -1,
				"conditions" => array(
					'date BETWEEN ? AND ?' => array($startdate, $enddate),
					'employee_id' => $idEmployee
				)
			));
			// debug($absenceRequests); exit;
			// $absencesConstraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
			$absenceRequests = !empty($absenceRequests) ? Set::combine($absenceRequests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest') : array();
			/* max Absence request by type / year save in AbsenceHistory */
			$absenceHistories = $this->AbsenceHistory->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'year' => array($requestYear, $firstDateYear, $currentYear)
				),
				'fields' => array('absence_id', 'total', 'begin', 'year')
			));
			$absenceHistories = !empty($absenceHistories) ? Set::combine($absenceHistories, '{n}.AbsenceHistory.absence_id', '{n}.AbsenceHistory', '{n}.AbsenceHistory.year') : array();
			/* Get absences by year */
			// Kiem tra data trong khoang min-1 -> max + 1
			$minyear = min($requestYear, $firstDateYear, $currentYear)-1;
			$maxyear = max($requestYear, $firstDateYear, $currentYear)+1;
			$startFilter = strtotime($minyear.'-01-01');
			$endFilter = strtotime($maxyear.'-12-31');
			// debug($startFilter);
			// debug($endFilter);
			// exit;
			
			$absenceRequestsByYear = $this->AbsenceRequest->find("all", array(
				'recursive' => -1,
				"conditions" => array(
					'employee_id' => $idEmployee,
					'date BETWEEN ? AND ?' => array($startFilter, $endFilter),
				),
				'fields' => array('id', 'date', 'absence_pm', 'absence_am', 'response_am', 'response_pm')
			));
			$absenceRequestsByYear = !empty($absenceRequestsByYear) ? Set::combine($absenceRequestsByYear, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest') : array();
			// debug($absenceRequestsByYear); 
			// exit;
			/* END Get absences by year */
			
			// debug( $absenceHistories); exit;
			// debug( $listWorkingDays); exit;
			$i = 0;
			foreach( $listWorkingDays as $date => $is_working){
				$canRequestAbsence[$date] = array();
				$getYear = date('Y', $date);
				// debug($getYear);
				if( $is_working){
					// Cho nay k kiem tra "end_of_period" do logic cu k kiem tra. Anh Chuong da trao doi la bo qua phan nay 05.01.2020
					foreach( $companyAbsences as &$absenceType){
						if( empty($absenceType['display'])) continue;
						$ab = array(
							'id' => $absenceType['id'],
							'type' => $absenceType['type'],
							'print' => $absenceType['print'],
							'weight' => $absenceType['weight'],
							'document' => $absenceType['document'],
						);
						$requested = 0;
						
						/* Tinh so ngay da request cua absence type trong chuky 1 nam tinh tu $begin */
						$begin = $absenceType['begin']; // mm-dd
						$periodStartDate  = strtotime(  $getYear . '-' . $begin);
						if($periodStartDate){
							if( $date > $periodStartDate){ // Lay data sau ngay $begin
								$periodEndDate = strtotime('+1 year', $periodStartDate);
							}else{ //lay data truoc ngay $begin
								$periodEndDate = $periodStartDate;
								$periodStartDate = strtotime('-1 year', $periodStartDate);
							}
						}else{
							$periodStartDate = strtotime($getYear . '-01-01');
							$periodEndDate = strtotime('+1 year', $periodStartDate);
						}
						
						// debug( date('d-m-Y', $periodStartDate ));
						// debug( date('d-m-Y', $periodEndDate ));
						foreach($absenceRequestsByYear as $_date => $_request){
							if( $date >= $periodStartDate && $_date < $periodEndDate ){ // khong lay ngay cuoi cung
								foreach (array('am', 'pm') as $type) {
									if( $_request['absence_' . $type] == $absenceType['id'])
										$requested +=0.5;
								}
							} 
						}
						/* END Tinh so ngay da request trong chu ky 1 nam tinh tu $begin */
						$ab['request'] = $requested;
						if( $i == 0) $absenceType['request'] = $requested;
						$getYear = date('Y', $periodStartDate);
						$ab['total'] = !empty($absenceHistories[$getYear]['total']) ? $absenceHistories[$getYear]['total'] : !empty($absenceHistories[$currentYear]['total']) ? $absenceHistories[$currentYear]['total'] : 0 ;
						if($requested < $ab['total'] || $ab['total'] == 0){
							$canRequestAbsence[$date][$absenceType['id']] = $ab;
						}
					}
					$i++;
					// exit;
				}
			}
			// debug( time() - $a);
			// debug($canRequestAbsence);
			// exit;
			
		}
	
		/* END Get data for Absence */ 
		$this->set(compact('isMulti'));
		$this->set(compact('loginEmployeeID', 'idEmployee', 'startdate', 'enddate', 'canRead', 'canWrite','canManage', 'ratio', 'workdays', 'listWorkingDays', 'holidays', 'capacity', 'employee', 'profitCenter','requestConfirm'));
		$this->set(compact('display_absence', 'companyAbsences', 'absenceRequests', 'canRequestAbsence'));
		$this->set(compact('activityRequests', 'consumed', 'activityTasks','activities','families','taskFavourites'));
		$this->set(compact('showActivityForecastComment', 'managerHour', 'sendTimesheetPartially', 'auto_fill_week', 'auto_fill_favourite'));
		// debug( $this->convert(memory_get_usage() - $mem));
		// exit;
		if( $this->params['isAjax']) $this->render('request_ajax');
	}
	function convert($size){
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	
	public function contextMenu(){
		// debug( 1);
		$this->layout = false;
		extract(array_merge(array(
			'id' => $this->employee_info['Employee']['id'],
			'week' => date('W'),
			'year' => date('Y')
		),$_GET));
		list($canRead, $canWrite, $canManage) = $this->checkPermissionTimesheet($id, false);
		if( !$canWrite){
			$this->Session->setFlash(__('Access denied', true), 'error');
		}
		// debug( 2);
        $this->loadModels(
			'ActivityRequest',
			'AbsenceRequest',
			'ProjectPhaseStatus',
			'Employee',
			'Activity',
			'Project',
			'ProjectTask',
			'ProjectPhasePlan',
			'ProjectPhase',
			'ProjectPart',
			'ActivityTask',
			'ProjectStatus',
			'Family',
			'ProjectTeam',
			'ProjectEmployeeProfitFunctionRefer',
			'ProjectEmployeeManager',
			'ProjectTaskFavourite'
		);
        $employeeName = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $id,
				'company_id' => $this->employee_info['Company']['id']
			),
		));
		// debug( 3);
		$listTaskDisplay = $listActivityDisplay = array();
		if( $employeeName ) $employeeName = $employeeName['Employee'];
        $company = $employeeName['company_id'];
        $employeeId = $employeeName['id'];
	    $profitQuery = $this->ProjectEmployeeProfitFunctionRefer->find(
			'first', array(
				'fields' => array('profit_center_id'),
				'recursive' => -1,
				'conditions' => array('employee_id' => $employeeName['id'])));

		$profit = Set::classicExtract($profitQuery, 'ProjectEmployeeProfitFunctionRefer.profit_center_id');
		$resource_see_him = isset($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) && !empty($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) ?  true : false;
		$phaseStatusDisplay = $this->ProjectPhaseStatus->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'display' => 1,
				'company_id' => $company
			),
			'fields' => array('id','id')
		));
		$phaseStatusDisplay = !empty( $phaseStatusDisplay) ? $phaseStatusDisplay : array();
		$taskStatusDisplay = $this->ProjectStatus->find('list',array(
			'conditions' => array(
				'display' => 1,
				'company_id' => $company
			),
			'fields' => array('id','id')
		));
		$taskStatusDisplay = !empty( $taskStatusDisplay) ? $taskStatusDisplay : array();
		$projectLinked = $this->Activity->find('list',array(
			'conditions' => array(
				'project !=' => null,
				'company_id' => $company,
				'activated' => 1
			),
			'fields' => array('project','project')
		));
		// debug( $projectLinked); exit;
		$projectLinked = !empty( $projectLinked) ? $projectLinked : array();
		$projectPhasePlan  = $this->ProjectPhasePlan->find('list',array(
			'conditions' => array(
				'OR' => array(
					'project_phase_status_id' => $phaseStatusDisplay,
					'project_phase_status_id is NULL'
				),				
				'project_id' => array_values($projectLinked)
			),
			'recursive' => -1,
			'fields' => array('id','id')
		));
		// debug( 4);
		$projectPhasePlan = !empty( $projectPhasePlan) ? $projectPhasePlan : array();
		$projectTaskDisplayed = $this->ProjectTask->find('list',array(
			'conditions' => array(
				'project_planed_phase_id' => array_values($projectPhasePlan),
				'task_status_id' => array_values($taskStatusDisplay)
			),
			'recursive' => -1,
			'fields' => array('id','id')
		)); 
		$projectTaskDisplayed = !empty( $projectTaskDisplayed) ? $projectTaskDisplayed : array();
		// debug( $projectTaskDisplayed); exit;
		$activities = $this->Activity->find('all', array(
			'recursive'     => -1,
			'conditions'    => array(
				'company_id' => $company,
				'activated' => 1,
			),
			'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id', 'activated', 'pms','allow_profit','project'),
			'order' => array('name ASC')
		));
		// debug( $activities); exit;
		$activities = !empty( $activities) ? Set::combine( $activities, '{n}.Activity.id', '{n}.Activity') : array();
		$listActivities = array_keys($activities);
		// debug( implode(', ', array_keys($activities))); exit;
		$activityTasks = $this->ActivityTask->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id', 'estimated', 'task_status_id', 'special'),
			'conditions' => array(
				'ActivityTask.special' => 0,
				'ActivityTask.activity_id' => $listActivities,
				'OR' => array(
					array( 'project_task_id' => $projectTaskDisplayed),
					array( 'project_task_id' => null)
				)
			)
		));
		// debug( 5);
		if($resource_see_him){
			// debug( 5.1);
			$this->loadModels('ProjectTaskEmployeeRefer', 'ActivityTaskEmployeeRefer', 'ProjectFunctionEmployeeRefer', 'ActivityProfitRefer'); 
			$getListAssignPTasks = $this->ProjectTaskEmployeeRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						array(
							'reference_id' => $employeeName['id'],
							'is_profit_center' => 0
						),
						array(
							'reference_id' => $profit,
							'is_profit_center' => 1
						)
					),
					'project_task_id' => $projectTaskDisplayed
				),
				'fields' => array('project_task_id', 'project_task_id')
			));
			$getTaskLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array('project_task_id' => $getListAssignPTasks),
				'fields' => array('id', 'id')
			));
			$listActivityTasksDisplayed = !empty($activityTasks) ? Set::classicExtract( $activityTasks, '{n}.ActivityTask.id') : array();
			$getListAssignATasks = $this->ActivityTaskEmployeeRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						array(
							'reference_id' => $employeeName['id'],
							'is_profit_center' => 0
						),
						array(
							'reference_id' => $profit,
							'is_profit_center' => 1
						)
					),
					'activity_task_id' => $listActivityTasksDisplayed
				),
				'fields' => array('activity_task_id', 'activity_task_id')
			));
			$listTaskDisplay = array_merge($getTaskLinked, $getListAssignATasks);
			$teams = $this->ProjectFunctionEmployeeRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						'employee_id' => $employeeName['id'],
						'profit_center_id' => $profit
					)
				),
				'fields' => array('project_team_id', 'project_team_id')
			));
			$projectsByTeams = $this->ProjectTeam->find('list', array(
				'recursive' => -1,
				'conditions' => array('ProjectTeam.id' => $teams),
				'fields' => array('project_id', 'project_id')
			));
			/**
			 * Lay cac activity co trong accessible par _ linked profit
			 */
			$activityOne = $this->ActivityProfitRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'profit_center_id' => $profit,
					'activity_id' => $listActivities,
				),
				'fields' => array('activity_id', 'activity_id')
			));
			/**
			 * Lay activity theo asssign o task
			 */
			$activityTwo = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ActivityTask.id' => $listTaskDisplay,
					'activity_id' => $listActivities,
				),
				'fields' => array('activity_id', 'activity_id')
			));
			/**
			 * Lay activity theo project link
			 */
			$activityThree = $this->Activity->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project' => $projectsByTeams,
					'Activity.id' => $listActivities,
				),
				'fields' => array('id', 'id')
			));
			$listActivityDisplay = array_unique(array_merge($activityOne, $activityTwo, $activityThree));
			$listActivityDisplay = array_merge($listActivityDisplay);
			$listActivities = array_keys($activities);
			// debug( implode(', ', array_keys($activities))); exit;
			$activityTasks = $this->ActivityTask->find('all', array(
				'recursive' => -1,
				'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id', 'estimated', 'task_status_id', 'special'),
				'conditions' => array(
					'id' => $listTaskDisplay,
				)
			));
			// debug($activityTasks); exit;
			$listActivitiesByTask = !empty($activityTasks ) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.activity_id') : array();
			// debug($listActivitiesByTask); exit;
			
			$listActivityID = array();
			foreach( $listActivityDisplay as $key => $val){
				if( in_array( $val, $listActivitiesByTask)) $listActivityID[] = $val;
			}
			$activities = $this->Activity->find('all', array(
				'recursive'     => -1,
				'conditions'    => array( 'id' => $listActivityID ),
				'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id', 'activated', 'pms','allow_profit','project'),
				'order' => array('name ASC')
			));
			$activities = !empty( $activities) ? Set::combine( $activities, '{n}.Activity.id', '{n}.Activity') : array();
		}
		/* Add consumed to list task */
		/* Include timesheet sent but not validate */
		$listActivitiesTask = !empty($activityTasks ) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
		$requests = $this->ActivityRequest->find('all', array(
				'recursive'     => -1,
				'fields'        => array(
					'id',
					'employee_id',
					'task_id',
					'SUM(value) as value'
				),
				'group'         => array('task_id'),
				'conditions'    => array(
					'status'        => array( 2), // (0,2)
					'task_id'       => $listActivitiesTask,
					'company_id'    => $employeeName['company_id'],
					'NOT'           => array('value' => 0, "task_id" => null),
				)
			)
		);
		$requests = !empty($requests) ? Set::combine($requests, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
		$projectTaskLinked = !empty($activityTasks ) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.project_task_id') : array();
		$taskFavourites = $this->ProjectTaskFavourite->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $this->employee_info['Employee']['id'],
				'task_id' => array_values($projectTaskLinked),
				'favourite' => 1,
			),
			'fields' => array('task_id', 'favourite')
			
		));
		$projectTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($projectTaskLinked),
			),
			'fields' => array('id', 'project_planed_phase_id', 'estimated'),
		));
		$workloads = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.estimated') : array();
		$projectPlanedPhases = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_planed_phase_id') : array();
		$projectPhaseParts = $this->ProjectPhasePlan->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($projectPlanedPhases)
			),
			'fields' => array('id', 'project_planed_phase_id', 'project_part_id')
		));
		$listPhaseIDs = !empty($projectPhaseParts) ? Set::combine($projectPhaseParts, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id' ) : array();
		$listPartIDs = !empty($projectPhaseParts) ? Set::combine($projectPhaseParts, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id' ) : array();
		$listPhases = $this->ProjectPhase->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($listPhaseIDs)
			),
			'fields' => array('id', 'name')
		));
		$listParts = $this->ProjectPart->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_values($listPartIDs)
			),
			'fields' => array('id', 'title')
		));
		
		
		
		// debug( $activityTasks ); exit;
		$activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask') : array();
		foreach( $activityTasks as $task_id => $val){
			$project_task_id = !empty($val['project_task_id']) ? $val['project_task_id'] : '';
			$project_planed_phase_id = !empty($projectPlanedPhases[$project_task_id]) ? $projectPlanedPhases[$project_task_id] : '';
			$PhaseID = !empty($listPhaseIDs[$project_planed_phase_id]) ? $listPhaseIDs[$project_planed_phase_id] : '';
			$PartID = !empty($listPartIDs[$project_planed_phase_id]) ? $listPartIDs[$project_planed_phase_id] : '';
			$activityTasks[$task_id]['phase_name'] = !empty($listPhases[$PhaseID]) ? $listPhases[$PhaseID] : '';
			$activityTasks[$task_id]['part_name'] = !empty($listParts[$PartID]) ? $listParts[$PartID] : '';
			$activityTasks[$task_id]['favourite'] = !empty( $taskFavourites[$val['project_task_id']]) ? 1 : 0;
			$activityTasks[$task_id]['consumed'] = !empty( $requests[$task_id] ) ? $requests[$task_id] : 0;
			$activityTasks[$task_id]['estimated'] = !empty( $workloads[$project_task_id] ) ? $workloads[$project_task_id] : 0;
			if( !empty($val['parent_id']) && !empty($activityTasks[$val['parent_id']])) $activityTasks[$val['parent_id']]['is_parent'] = 1;
		}
		// debug( $activityTasks ); exit;
		/* End Add consumed to list task */
		
		// debug(array_values($activities));
		// debug( count($activities));
		$listSubFamilies = $listFamilies = array();
		foreach($activities as $a_id => $_act){
			if( !empty($_act['family_id']) && !in_array($_act['family_id'], $listFamilies) ){
				$listFamilies[] = $_act['family_id'];
			}
			if( !empty($_act['subfamily_id']) && !in_array($_act['subfamily_id'], $listSubFamilies) ){
				$listSubFamilies[] = $_act['subfamily_id'];
			}
		}
		// $listSubFamilies = array_unique($listSubFamilies);
		// $listFamilies = array_unique($listFamilies);
		// debug( $listFamilies);
		// debug( $listSubFamilies);
		// exit;
		$hasSubFamilies = false;
		if( !empty($listSubFamilies)){
			$hasSubFamilies = true;
		}
		$families = $this->Family->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => array_merge( $listFamilies, $listSubFamilies),
			),
			'fields'=> array('id', 'name', 'parent_id'),
		));
		$families = !empty($families) ? Set::combine($families, '{n}.Family.id', '{n}.Family') : array();
		// debug( $families);
		// exit;
		
        die( json_encode(array(
			'tasks' => $activityTasks,
			'activities' => $activities,
			'families' => $families,
			'hasSubFamilies' => $hasSubFamilies,
			
		)));
        exit;
	}
	public function delete_request($task_id = null) {
		extract($_GET);
		$result = false;
		$data = array();
		if( empty($task_id) || empty( $id)) {
			$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
		}else{
			list($canRead, $canWrite, $canManage) = $this->checkPermissionTimesheet($id, false);
			if( $canWrite ){
				list($_start, $_end) = $this->_parseParams();
				$this->loadModel('ActivityRequest');
				$last = $this->ActivityRequest->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'task_id' => $task_id ? $task_id : null,
						'date BETWEEN ? AND ?' => array($_start, $_end),
						'employee_id' => $id,
						'status' => array(-1,1)
					),
					'fields' => array('id', 'id'),
				));
				if(!empty($last)){
					$this->ActivityRequest->deleteAll(array('ActivityRequest.id' => array_values($last)), false);
					$result = true;
					$this->Session->setFlash(__('The data has been deleted', true), 'success');
				}else{
					$this->Session->setFlash(__('The data was not deleted', true), 'error');
				}
			}else{
				$this->Session->setFlash(__('Access denied', true), 'error');
			}
		}
		if( !$this->params['isAjax']){
			unset( $_GET['url']);
			$this->redirect( array(
				'action' => 'request',
				'?' => compact(array_keys($_GET))
			));
		}
		$this->set(compact('result', 'data'));
	}

	public function auto_fill_week(){
		$loginEmployee = $this->employee_info['Employee']['id'];
		$idEmployee = !empty($_GET['id']) ? $_GET['id'] : '';
		$filled = !empty($_GET['filled']) ? $_GET['filled'] : '';
		$conpany_id = $this->employee_info['Company']['id'];
		list($startdate, $enddate) = $this->_parseParams();
		if($loginEmployee == $idEmployee){
			$this->loadModels('ActivityRequest', 'ActivityRequestCopy');
			if($filled == 1){
				// filled ON
				// Update filled to OFF -> Delete all timesheet of this employee in this week
				$activityRequest = $this->ActivityRequest->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'date BETWEEN ? AND ?' => array($startdate, $enddate),
						'employee_id' => $idEmployee,
					),
					'fields' => array('id', 'id'),
				));
				if(!empty($activityRequest)){
					$this->ActivityRequest->deleteAll(array('ActivityRequest.id' => array_values($activityRequest)), false);
					
					// Auto fill of week Empty
					$fill_week = 0;
					$get_fill_week = $this->ActivityRequestCopy->find('first', array(
						'recursive' => -1,
						'conditions' => array('employee_id' => $idEmployee, 'start' => $startdate, 'end' => $enddate, 'company_id' => $conpany_id),
						'field' => 'id',
					));
					// Do not change the field: copy in activity_request_confirms
					// Only update field: manual_copy for this week.
					if( !empty($get_fill_week) && !empty($get_fill_week['ActivityRequestCopy']['id']) ){
						$this->ActivityRequestCopy->id = $get_fill_week['ActivityRequestCopy']['id'];
					} else {
						$this->ActivityRequestCopy->create();
					}
					$this->ActivityRequestCopy->save(array(
						'employee_id' => $idEmployee,
						'start' => $startdate,
						'end' => $enddate,
						'company_id' => $conpany_id,
						'auto_fill_week' => $fill_week,
						'auto_fill_favourite' => 0,
					));
					$result = true;
					$this->Session->setFlash(__('The data has been deleted', true), 'success');
				}else{
					$this->Session->setFlash(__('The data was not deleted', true), 'error');
				}
				$this->request();
			}else{
				// filled OFF
				// Update filled to ON -> Add tasks assigned to this employee in this weeks.
				// and change status copy timesheet on this week in activity_request_copies.
				
				// Clear old data
				$this->ActivityRequest->deleteAll(array(
					'date BETWEEN ? AND ?' => array($startdate, $enddate),
					'employee_id' => $idEmployee,
				));
				$this->ActivityRequestCopy->deleteAll(array(
					'employee_id' => $idEmployee,
					'start' => $startdate,
					'end' => $enddate,
					'company_id' => $conpany_id
				));
				
				$this->request('manual');
				
			}
		}else{
			// You haven't access permissions
			$this->Session->setFlash(__('You haven\'t access permissions', true), 'error');
			$this->request();
		}
	}

	public function auto_fill_favourite(){
		$loginEmployee = $this->employee_info['Employee']['id'];
		$idEmployee = !empty($_GET['id']) ? $_GET['id'] : '';
		$fill_favourite = !empty($_GET['fill_favourite']) ? $_GET['fill_favourite'] : '';
		$conpany_id = $this->employee_info['Company']['id'];
		list($startdate, $enddate) = $this->_parseParams();
		$this->loadModels('ActivityRequest', 'ActivityRequestCopy', 'ProjectTaskFavourite', 'ActivityTask');
		
		if($loginEmployee == $idEmployee){
			
			if($fill_favourite == 1){
				// filled ON
				// Update filled to OFF -> Delete all timesheet of this employee in this week
				
				// Get task favourite of employee on this week.
				$favourite_tasks = $this->ProjectTaskFavourite->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'employee_id' => $idEmployee,
						'favourite' => 1,
						'company_id' => $this->employee_info['Company']['id'],
					),
					'fields' => array('task_id')
					
				));
				$activity_tasks = $this->ActivityTask->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $favourite_tasks, 
					),
					'fields' => array('id'),
				));
				$activityRequest = $this->ActivityRequest->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'date BETWEEN ? AND ?' => array($startdate, $enddate),
						'employee_id' => $idEmployee,
						'task_id' => $activity_tasks,
					),
					'fields' => array('id', 'id'),
				));
				
				if(!empty($activityRequest)){
					$this->ActivityRequest->deleteAll(array('ActivityRequest.id' => array_values($activityRequest)), false);
					
					// Auto fill of week Empty
					$get_fill_favourite = $this->ActivityRequestCopy->find('first', array(
						'recursive' => -1,
						'conditions' => array('employee_id' => $idEmployee, 'start' => $startdate, 'end' => $enddate, 'company_id' => $conpany_id),
						'field' => 'id',
					));
					// Do not change the field: copy in activity_request_confirms
					// Only update field: manual_copy for this week.
					if( !empty($get_fill_favourite) && !empty($get_fill_favourite['ActivityRequestCopy']['id']) ){
						$this->ActivityRequestCopy->id = $get_fill_favourite['ActivityRequestCopy']['id'];
					} else {
						$this->ActivityRequestCopy->create();
					}
					$this->ActivityRequestCopy->save(array(
						'employee_id' => $idEmployee,
						'start' => $startdate,
						'end' => $enddate,
						'company_id' => $conpany_id,
						'auto_fill_week' => 0,
						'auto_fill_favourite' => 0,
					));
					$result = true;
					$this->Session->setFlash(__('The data has been deleted', true), 'success');
				}else{
					$this->Session->setFlash(__('The data was not deleted', true), 'error');
				}
				$this->request();
			}else{
				// filled OFF
				// Update filled favourite to ON -> Add tasks favourite of this employee on this weeks.
				// and change status filled favourite on this week in activity_request_copies.
				
				// Clear old data
				$this->ActivityRequest->deleteAll(array(
					'date BETWEEN ? AND ?' => array($startdate, $enddate),
					'employee_id' => $idEmployee,
				));
				$profit_center = $this->Employee->find('list', array(
					'recursive' => -1,
					'conditions' => array('id' => $idEmployee),
					'fields' => array('id','profit_center_id'),
				));
				$profit = null;
				$is_profit = false;
				if(!empty($profit_center)){
					$profit = $profit_center;
					$is_profit = true;
				}
				// Get data tasks favourite of employee in this week
				$datas_favourite = $this->dataCopyForecast($idEmployee, $profit , $startdate, $enddate, $is_profit, true);
				$taskOfEmployee = !empty($datas_favourite['results']) ? $datas_favourite['results'] : array();
				$projectLinkedActivity = !empty($datas_favourite['projectLinkedActivity']) ? $datas_favourite['projectLinkedActivity'] : array();
				
				$dataCopies = array();
				foreach( $taskOfEmployee as $_task){
					if( $_task['is_profit_center'] ) continue;
					if( !empty($_task['project_id'])) 
						$activityId = $projectLinkedActivity[$_task['project_id']];
					else
						$activityId = $_task['activity_id'];
					
					$taskId = $_task['task_id'];
					if(!isset($dataCopies[$activityId])){
						$dataCopies[$activityId] = array();
					}
					$dataCopies[$activityId][] = $taskId;
				}
				
				$saved = array();
				if(!empty($dataCopies)){
					foreach($dataCopies as $dataCopy){
						if(!empty($dataCopy)){
							$dataCopy = array_unique($dataCopy);
							foreach($dataCopy as $idTask){
									$saved[] = array(
										'date' => $startdate,
										'value' => 0,
										'employee_id' => $idEmployee,
										'company_id' => $conpany_id,
										'task_id' => $idTask,
										'status' => -1,
										'activity_id' => 0
									);
							}
						}
					}
				}
				if(!empty($saved)){
					if($this->ActivityRequest->saveAll($saved)){
						
						$get_copy_favourite = $this->ActivityRequestCopy->find('first', array(
							'recursive' => -1,
							'conditions' => array('employee_id' => $idEmployee, 'start' => $startdate, 'end' => $enddate, 'company_id' => $conpany_id),
							'field' => 'id',
						));
						
						if( !empty($get_copy_favourite) && !empty($get_copy_favourite['ActivityRequestCopy']['id']) ){
							$this->ActivityRequestCopy->id = $get_copy_favourite['ActivityRequestCopy']['id'];
						} else {
							$this->ActivityRequestCopy->create();
						}
						$this->ActivityRequestCopy->save(array(
							'employee_id' => $idEmployee,
							'start' => $startdate,
							'end' => $enddate,
							'company_id' => $conpany_id,
							'auto_fill_week' => 0,
							'auto_fill_favourite' => 1,
						));
					}
				}
				
				$this->request();
			}
		}else{
			// You haven't access permissions
			$this->Session->setFlash(__('You haven\'t access permissions', true), 'error');
			$this->request();
		}
	}

	/**
     * manages
     *
     * @return void
     * @access public
     */
	public function getDataCopyForecast(){
		$result = false;
		$data = array();
		$message = '';
		extract($_GET); 
		if( !(empty($idEmp) || empty($idPc) || empty($start) || empty($end) )){
			list($canRead, $canWrite, $canManage) = $this->checkPermissionTimesheet($idEmp);
			if ($canWrite){
				$data = $this->dataCopyForecast($idEmp, $idPc, $start, $end, true);
				$result = true;
			}else{
				$message = __('Access denied', true);
			}
		}
		/* 
		* get lisTaskRequests
		*/
		//Lay nhung request cua tuan nay
		$this->loadModel('ActivityRequest');
        $_activityRequests = $this->ActivityRequest->find("all", array(
            'recursive' => -1,
			"conditions" => array(
				'date BETWEEN ? AND ?' => array($start, $end),
				'employee_id' => $idEmp
			)
		));
		$lisTaskRequests = array();
		foreach ($_activityRequests as $_activity) {		
			$_activity = array_shift($_activity);
			$lisTaskRequests[] = $_activity['task_id'];		
		}
		die(json_encode($data));
	}
	public function dataCopyForecast($idEmp, $idPc, $start, $end, $isPC = true, $favourite = false){
		
		$this->loadModels('ProjectTask', 'ActivityRequest', 'Project', 'ProjectPhase', 'ProjectPhasePlan', 'ActivityTask', 'Activity', 'AbsenceRequest', 'ProjectPart', 'ProjectStatus', 'ProjectTaskFavourite', 'ActivityFamily');
		
		$task_start_date =  date('Y-m-d', $start);
		$task_end_date =  date('Y-m-d', $end);
		
		//Get project tasks of PC and employee with filter in [$start, $end]
		$projectStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'status NOT' => 'CL',
				'display' => '1'
		)));
		$favourite_tasks = array();
		if($favourite){
			$favourite_tasks = $this->ProjectTaskFavourite->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'employee_id' => $this->employee_info['Employee']['id'],
					'favourite' => 1,
				),
				'fields' => array('task_id')
				
			));
		}
		$taskFields = array(
			'ProjectTask.id',
			'ProjectTask.task_title',
			'ProjectTask.is_nct',
			'ProjectTask.project_id',
			'ProjectTask.task_start_date',
			'ProjectTask.task_end_date',
			'ProjectTask.parent_id',
			'ProjectTask.project_planed_phase_id',
			'ProjectTask.task_status_id',
			'ProjectTaskEmployeeRefer.reference_id',
			'ProjectTaskEmployeeRefer.estimated',
			'ProjectTaskEmployeeRefer.is_profit_center',
		);
		$p_condResource = array();
		if($isPC){
			$p_condResource['AND'] = array(
				'OR' => array(
					array(
						'ProjectTaskEmployeeRefer.reference_id' => $idEmp,
						'ProjectTaskEmployeeRefer.is_profit_center' => 0
					),
					array(
						'ProjectTaskEmployeeRefer.reference_id' => $idPc,
						'ProjectTaskEmployeeRefer.is_profit_center' => 1
					)
				)
			);
		}else{
			$p_condResource['AND'] = array(
				'ProjectTaskEmployeeRefer.reference_id' => $idEmp,
				'ProjectTaskEmployeeRefer.is_profit_center' => 0
			);
		}
		if($favourite){
			$p_condResource[] = array(
				'ProjectTask.id' => $favourite_tasks,
			);
		}
		$projectTasks = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ProjectTask.task_status_id' => $projectStatus,
				'NOT' => array(
                    'OR' => array(
                        'ProjectTask.task_start_date > ?' => array($task_end_date),
                        'ProjectTask.task_end_date < ?' => array($task_start_date)
                    )
                ),
                $p_condResource,
            ),
			'fields' => $taskFields,
			'order' => 'ProjectTaskEmployeeRefer.is_profit_center DESC'
        ));
		$projectTaskIds = $parentProjectIds = array();
		$listActivityIds = array();
		$setupDatas = array(); 
		$projectPhasePlans = array(); 
		$projectPartPlans = array(); 
		$projectLinkedActivity = array(); 
		
		if(!empty($projectTasks)){
			$projectTaskIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.id'));
			$parentProjectIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id') : array();
			$project_id = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_id'));
			$projectActivatedYes =  $this->Project->find('list', array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'activities',
						'alias'=> 'Activity',
						'conditions' => array(
							'Activity.project = Project.id', 
							'Activity.company_id' => $this->employee_info['Company']['id'],
							'Activity.project' => $project_id, 
							'Activity.activated' => 1
						)
					)
				),
				'fields' => array('id'),
			));
			$activity_tasks = $this->ActivityTask->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $projectTaskIds, 
				),
				'fields' => array('project_task_id', 'id', 'activity_id', 'name'),
			));
			$list_activity_id = !empty($activity_tasks) ? Set::combine($activity_tasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.activity_id') : array();
			$activity_task_id = !empty($activity_tasks) ? Set::combine($activity_tasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.id') : array();
			$activity_tasks_title =  !empty($activity_tasks) ? Set::combine($activity_tasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.name') : array();
			$listActivityIds = array_values($list_activity_id);
			$project_names = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'activity_id' => $listActivityIds, 
				),
				'fields' => array('activity_id', 'project_name'),
			));
			$_projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $projectActivatedYes, 
				),
				'fields' => array('id', 'project_planed_phase_id', 'project_part_id'),
			));
			
			$projectPhasePlans = !empty($_projectPhasePlans) ? Set::combine($_projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id') : array();
			$projectPartPlans = !empty($_projectPhasePlans) ? Set::combine($_projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
			$projectPhases = $this->ProjectPhase->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'name'),
				'conditions' => array(
					'id' => $projectPhasePlans
				)
			));
			$projectParts = $this->ProjectPart->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'title'),
				'conditions' => array(
					'id' => $projectPartPlans
				)
			));
			$taskFavourites = $this->ProjectTaskFavourite->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'employee_id' => $this->employee_info['Employee']['id'],
					'task_id' => $projectTaskIds,
					'favourite' => 1,
				),
				'fields' => array('task_id', 'favourite')
				
			));
		
			$consumed = $this->ActivityRequest->find('all', array(
				'recursive'     => -1,
				'fields'        => array(
					'id',
					'employee_id',
					'task_id',
					'SUM(value) as value'
				),
				'group'         => array('task_id'),
				'conditions'    => array(
					'status'	=> '2',
					'task_id'	=> $activity_task_id,
					'company_id'	=>  $this->employee_info['Company']['id'],
					'value !='	=> 0,
				)
			));
			$consumed = !empty($consumed) ? Set::combine($consumed, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
			foreach($projectTasks as $key => $projectTask){
				$_inPIds = $projectTask['ProjectTask']['id'];
				if(  !in_array($_inPIds, $parentProjectIds) ){
					$_start = !empty($projectTask['ProjectTask']['task_start_date']) ? ($projectTask['ProjectTask']['task_start_date']) : 0;
					$_end = !empty($projectTask['ProjectTask']['task_end_date']) ? ($projectTask['ProjectTask']['task_end_date']) : 0;
					$favourite = !empty($taskFavourites[$_inPIds]) ? ($taskFavourites[$_inPIds]) : 0;
					$checkTaskLink = isset($projectActivatedYes[$projectTask['ProjectTask']['project_id']]) ? $projectActivatedYes[$projectTask['ProjectTask']['project_id']] : 0;
					
					if($checkTaskLink != 0){
						$activity_id = $list_activity_id[$projectTask['ProjectTask']['id']];
						$task_id = $activity_task_id[$projectTask['ProjectTask']['id']];
						$setupDatas[$task_id]['task_id'] = $task_id;
						$setupDatas[$task_id]['activity_id'] = $activity_id;
						$projectLinkedActivity[$projectTask['ProjectTask']['project_id']] = $list_activity_id[$projectTask['ProjectTask']['id']];
						$setupDatas[$task_id]['employee_id'] = $projectTask['ProjectTaskEmployeeRefer']['reference_id'];
						$project_name = !empty($project_names[$activity_id]) ? $project_names[$activity_id] . '/' : '';
						$setupDatas[$task_id]['task_title'] = $project_name . $projectTask['ProjectTask']['task_title'];
						$setupDatas[$task_id]['start'] = $_start;
						$setupDatas[$task_id]['end'] = $_end;
						$setupDatas[$task_id]['estimated'] = $projectTask['ProjectTaskEmployeeRefer']['estimated'];
						$setupDatas[$task_id]['project_id'] = $projectTask['ProjectTask']['project_id'];
						$listProjectIDs[] = $projectTask['ProjectTask']['project_id'];
						$phaseId = isset($projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
						$setupDatas[$task_id]['phase_name'] = !empty($projectPhases[$phaseId]) ? $projectPhases[$phaseId] : '';
						$partId = isset($projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
						$setupDatas[$task_id]['part_name'] = !empty($projectParts[$partId]) ? $projectParts[$partId] : '';
						$setupDatas[$task_id]['favourite'] = $favourite;
						$setupDatas[$task_id]['nct'] = isset($projectTask['ProjectTask']['is_nct']) ? $projectTask['ProjectTask']['is_nct'] : 0;
						$listProjectTasks[$projectTask['ProjectTask']['id']] = $projectTask['ProjectTask']['task_title'];
						$setupDatas[$task_id]['is_profit_center'] = $projectTask['ProjectTaskEmployeeRefer']['is_profit_center'];
						$setupDatas[$task_id]['consumed'] = !empty($consumed[$activity_task_id[$projectTask['ProjectTask']['id']]]) ? $consumed[$activity_task_id[$projectTask['ProjectTask']['id']]] : '';
					}
				}
			}
		}
		// Get activity tasks of PC and employee with filter in [$start, $end]
		$a_condResource = array();
		$act_fields = array(
			'ActivityTask.id',
			'ActivityTask.task_status_id',
			'ActivityTask.task_start_date',
			'ActivityTask.task_end_date',
			'ActivityTask.activity_id',
			'ActivityTask.is_nct',
			'ActivityTask.name',
			'ActivityTaskEmployeeRefer.reference_id',
			'ActivityTaskEmployeeRefer.estimated'
		);
		if($isPC){
			$a_condResource['AND'] = array(
				'OR' => array(
					array(
						'ActivityTaskEmployeeRefer.reference_id' => $idEmp,
						'ActivityTaskEmployeeRefer.is_profit_center' => 0
					),
					array(
						'ActivityTaskEmployeeRefer.reference_id' => $idPc,
						'ActivityTaskEmployeeRefer.is_profit_center' => 1
					)
				)
			);
		}else{
			$a_condResource['AND'] = array(
				'ActivityTaskEmployeeRefer.reference_id' => $idEmp,
				'ActivityTaskEmployeeRefer.is_profit_center' => 0
			);
		}
		
		$actiTasks = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ActivityTask.project_task_id' => null,
				'ActivityTask.task_status_id' => $projectStatus,
                'NOT' => array(
                    'OR' => array(
                        'ActivityTask.task_start_date > ?' => array($task_end_date),
                        'ActivityTask.task_end_date < ?' => array($task_start_date)
                    )
                ),
                $a_condResource,
            ),
			'fields' => $act_fields,
			'order' => 'ActivityTaskEmployeeRefer.is_profit_center DESC'
        ));
		
		$parentActivityIds = array();
		$setupDatasAT = array();
		$listActivityTasks = array();
		if(!empty($actiTasks)){
			$activityIds = !empty($actiTasks) ? Set::classicExtract($actiTasks, '{n}.ActivityTask.activity_id') : array();
			$listActivityIds = array_merge($listActivityIds, $activityIds);
			$parentActivityIds = !empty($actiTasks) ? Set::classicExtract($actiTasks, '{n}.ActivityTask.parent_id') : array();
			$activityActivatedYes = $this->Activity->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $activityIds, 
					'activated' => 1,
				),
				'fields' => array('id'),
			));
			
			foreach($actiTasks as $key => $actiTask){
				$_inAIds = $actiTask['ActivityTask']['id'];
				if( !in_array($_inAIds, $parentActivityIds) && !empty($activityActivatedYes[$_inAIds])) {
					$_startAT = !empty($actiTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_start_date']) : 0;
					$_endAT = !empty($actiTask['ActivityTask']['task_end_date']) ? date('Y-m-d',$actiTask['ActivityTask']['task_end_date']) : 0;
					$task_id = $actiTask['ActivityTask']['id'];
					$setupDatasAT[$task_id]['task_id'] = $actiTask['ActivityTask']['id'];
					$setupDatasAT[$task_id]['employee_id'] = $actiTask['ActivityTaskEmployeeRefer']['reference_id'];
					$setupDatasAT[$task_id]['task_title'] = $actiTask['ActivityTask']['name'];
					$setupDatasAT[$task_id]['start'] = $_startAT;
					$setupDatasAT[$task_id]['end'] = $_endAT;
					$setupDatasAT[$task_id]['estimated'] = $actiTask['ActivityTaskEmployeeRefer']['estimated'];
					$setupDatasAT[$task_id]['project_id'] = 0;
					$setupDatasAT[$task_id]['part_name'] = '';
					$setupDatasAT[$task_id]['phase_name'] = '';
					$setupDatasAT[$task_id]['activity_id'] = $actiTask['ActivityTask']['activity_id'];
					$actiId[] = $actiTask['ActivityTask']['activity_id'];
					$setupDatasAT[$task_id]['nct'] = isset($actiTask['ActivityTask']['is_nct']) ? $actiTask['ActivityTask']['is_nct'] : 0;
					$listActivityTasks[$actiTask['ActivityTask']['id']] = $actiTask['ActivityTask']['name'];
					
				}
			}
		}
		$setupDatas = $setupDatas + $setupDatasAT;
		$activities = $this->Activity->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id'),
			'conditions' => array(
				'id' => $listActivityIds
			)
		));
		$familyIds = array_unique(Set::classicExtract($activities, '{n}.Activity.family_id'));
		$subFamilyIds = array_unique(Set::classicExtract($activities, '{n}.Activity.subfamily_id'));
		if(!empty($subFamilyIds)){
			$familyIds = array_merge($familyIds, $subFamilyIds);
		}
		
		$activities = !empty($activities) ? Set::combine($activities, '{n}.Activity.id', '{n}.Activity') : array();
		$families = $this->ActivityFamily->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'name', 'parent_id'),
			'conditions' => array(
				'id' => $familyIds
			)
		));
		$families = !empty($families) ? Set::combine($families, '{n}.ActivityFamily.id', '{n}.ActivityFamily') : array();
		$endDatas = array();
		$endDatas['results'] =!empty($setupDatas) ? $setupDatas : array();
		$endDatas['projectLinkedActivity'] = !empty($projectLinkedActivity) ? $projectLinkedActivity : array();
		$endDatas['activities'] =!empty($activities) ? $activities : array();
		$endDatas['families'] =!empty($families) ? $families : array();
		return $endDatas;
	}
    function manages($typeSelect=null, $pc_id = null, $date = null) {
		//dayMaps
		$pc_list = $this->_getProfits(false, true);	
		//Ticket #1176
		$this->loadModels('ProjectPhase','ProjectPhasePlan', 'Project','HistoryFilter', 'TmpStaffingSystem', 'Employee','Translation','TranslationSetting','ProjectPriority');
		$e_history = $this->Employee->HistoryFilter->find('list', array(
			'recursive' => -1,
			'fields' => array( 'path', 'params'),
			'conditions' => array(
				'path' => array('display_project_opportunity', 'display_task_wlzero','display_project_archived'),
				'employee_id' => $this->employee_info['Employee']['id']
			)
		));
		$displayProOppor = !empty($e_history['display_project_opportunity']) ? (int) $e_history['display_project_opportunity'] : 0;
		$displayWlzero = (!isset($e_history['display_task_wlzero']) || !empty($e_history['display_task_wlzero'])) ? 1 : 0;
		//Set gia tri default la 1.
		if(isset($e_history['display_project_archived'])){
			$displayProArchived = !empty($e_history['display_project_archived']) ? (int) $e_history['display_project_archived'] : 0;
		}else{
			$displayProArchived = 1;
		}
		$id_pro_oppor = $id_pro_archived = $id_pro_model = array();
		$id_pro_oppor = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 2,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$id_pro_archived = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$id_pro_model = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 4,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$pro_not_display = $lists_pro_pri = $list_priority = array();
		if(!empty($id_pro_model)){
			$pro_not_display = $id_pro_model;
		}
		if(!empty($id_pro_oppor)){
			if($displayProOppor == 0){
				if(!empty($pro_not_display)){
					$pro_not_display = array_merge($id_pro_oppor,$pro_not_display);
				}else{
					$pro_not_display = $id_pro_oppor;
				}
			}
		}
		if(!empty($id_pro_archived)){
			if($displayProArchived == 0){
				if(!empty($pro_not_display)){
					$pro_not_display = array_merge($id_pro_archived,$pro_not_display);
				}else{
					$pro_not_display = $id_pro_archived;
				}
			}
		}
		//List project va priority_id
		$lists_pro_pri = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'NOT' => array(
					array('id' => $pro_not_display),
					array('project_priority_id' => null),
				) 
			),
			'fields' => array('id','project_priority_id')
		));
		//List Priority
		$list_priority = $this->ProjectPriority->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id','priority')
		));
		$list_phase = $this->ProjectPhase->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'joins' => array(
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhase.id'
					)
				)
			),
			'fields' => array('ProjectPhasePlan.id', 'ProjectPhase.name')
		));
		$list_phase_color = $this->ProjectPhase->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'joins' => array(
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhase.id'
					)
				)
			),
			'fields' => array('ProjectPhasePlan.id', 'ProjectPhase.color')
		));
		
		if(!empty($pc_list)){
			$ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
			$profiltId = array();
			// $date = date('d-m-Y', time());
			if($this->data){
				$profiltId = !empty($this->data['pcList']) ? $this->data['pcList'] : array();
				$typeSelect = $this->data['date_type'];
				$date = !empty($this->data['date']) ? $this->data['date'] : date('d-m-Y', time());
				// debug($date);
			}else{
				// workdays
				if(!empty($typeSelect) && !empty($pc_id) && !empty($date)){
					$profiltId[] = $pc_id;
					$date = date('d-m-Y', $date);
				}else{
					$history = $this->HistoryFilter->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'employee_id' => $this->employee_info['Employee']['id'],
							'path' => $this->params['url']['url'],
						),
						'fields' => array('params')
					));
					$myPC[] = $this->employee_info['Employee']['profit_center_id'];
					$history = unserialize($history['HistoryFilter']['params']);
					$typeSelect = !empty($history['data[date_type]'][0]) ? $history['data[date_type]'][0] : 'week';
					$profiltId = !empty($history['data[pcList][]']) ? $history['data[pcList][]'] : $myPC;
					$date = !empty($history['data[date]']) ? $history['data[date]'] : date('d-m-Y', time());
				}
			}
			$date_selected = $date;
			// Get data for actions
			$getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;      
			$managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
			$workloads = array();
			$resources = array();
			$employees = array();
			$employOfPC = array();
			if($typeSelect =='week'){
				// list($_start, $_end) = $this->_parseParams();
				$_start = strtotime('monday this week', strtotime($date));
				$_end = strtotime('friday this week', strtotime($date));

			}else{
				// dayWorks
				$_start = strtotime('first day of this month', strtotime($date));
				if($typeSelect =='month'){
					$_end = strtotime('last day of this month', strtotime($date));
				}else{
					$c_month = date('m', strtotime($date));
					$c_year = date('Y', strtotime($date));
					$_end = ($c_month - 1 == 0 )? '12-'.$c_year : ($c_month - 1).'-'.($c_year + 1);
					$_end = strtotime('last day of this month', strtotime('01-'.$_end));
				}
				// Get data workload of resoures
				$listEmployees = array();
				$onlyEmployeeOfPC = array();
				if(!empty($profiltId)){
					foreach($profiltId as $key => $pcId){
						if ($params = $this->_getProfitFollowDates($pcId, $getDataByPath, $_start, $_end)) {
							list($profit, $paths, $allEmployee, $employeeName, $onlyEmpOfPC) = $params;
							$onlyEmployeeOfPC[$pcId] = $employOfPC[$pcId] = $onlyEmpOfPC;
							$resources[$pcId] = array_keys($allEmployee);
							$resources[$pcId][] = 'tNotAffec_'.$pcId;
							$employees['tNotAffec_'.$pcId] = __('Not Affected', true);
							array_push($listEmployees, $pcId);
							if(!empty($allEmployee)){
								foreach($allEmployee as $employID => $employName){
									array_push($listEmployees, $employID);
									$employees[$employID] = $employName;
								}
							}
						}
					}
				}
				$stfCondition = array(
					'company_id' => $this->employee_info['Company']['id'],
					'model_id' => ($typeSelect =='month') ? $listEmployees : $profiltId,
					'date BETWEEN ? AND ?' => array($_start, $_end),
					'NOT' => array('project_id' => $pro_not_display)
				);
				$tmpStaffing = array();
				if($typeSelect =='month'){
					$tmpStaffing = $this->TmpStaffingSystem->find('all', array(
						'recursive' => -1,
						'conditions' => $stfCondition,
						'fields' => array('model_id', 'estimated'),
						
					));
					if(!empty($tmpStaffing)){
						foreach($tmpStaffing as $key => $value){
							$dx = $value['TmpStaffingSystem'];
							if(empty($workloads[$dx['model_id']]['workload'])) $workloads[$dx['model_id']]['workload'] = 0;
							$workloads[$dx['model_id']]['workload'] += $dx['estimated'];
						}
					}
				}else{
					$tmpStaffing = $this->TmpStaffingSystem->find('all', array(
						'recursive' => -1,
						'conditions' => $stfCondition,
						'fields' => array('model_id', 'sum(estimated) as estimated', 'date'),
						'group' => array('model_id', 'date'),
					));
					if(!empty($tmpStaffing)){
						foreach($tmpStaffing as $key => $value){
							$dx = $value['TmpStaffingSystem'];
							$value = $value[0]['estimated'];
							if(empty($workloads[$dx['model_id']]['workload'])) $workloads[$dx['model_id']]['workload'] = array();
							if(empty($workloads[$dx['model_id']]['workload'][$dx['date']])) $workloads[$dx['model_id']]['workload'][$dx['date']] = 0;
							$workloads[$dx['model_id']]['workload'][$dx['date']]  += $value;
						}
					}
					
				}
			}
			$holidays = ClassRegistry::init('Holiday')->getOptionHolidays($_start, $_end, $this->employee_info['Company']['id']);
			
			$managerPC = array();
			$project_manager = array();
			if($typeSelect =='week'){
				if(!empty($profiltId)){
					$pc_workload = array();
					foreach($profiltId as $key => $pcId){
						//get data workload for PC/Employee
						if ($params = $this->_getProfitFollowDates($pcId, $getDataByPath, $_start, $_end)) {
							list($profit, $paths, $employees, $employeeName) = $params;
							$workload = $this->_workloadFromTaskForecasts($employees, $_start, $_end, 1, $typeSelect, $profit['id']);
							$workloads[$profit['id']] = $workload;
							$pc_workload[$profit['id']] = $workload;
							$workloads[$profit['id']]['employees'] = $employees;
							$employOfPC[$pcId] = $employees;
							$resources[$pcId] = array_keys($employees);
							$workloads[$profit['id']]['employees']['tNotAffec_'.$pcId] = __('Not Affected', true);
						}
					}
					list($managerPC, $project_manager) = $this->getPermisionForecast($pc_workload);
					
				}
				
			}
			$companyName = $this->employee_info['Company']['company_name'];
			
			$workdays = ClassRegistry::init('Workday')->getOptions($this->employee_info['Company']['id']);
			
			// remove sunday, saturday
			foreach($workdays as $key=>$val){
				if($val == 0) unset($workdays[$key]);
			}
			if($typeSelect == 'month'){
				/**
				 * Cac ngay lam viec trong tuan
				 */
				$dayWorksConfigRoundWeek = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
				$absenceEmployees = $this->_getAbsencesResources(array_keys($employees), $_start, $_end, $typeSelect);
				
				//Ticket #1115, Comment check $workloads vi PC moi tao la co data table TmpStaffingSystem => $workloads = empty.
				// if(!empty($workloads)){
					foreach($resources as $pc_id => $employee_ids ){
						$workloads[$pc_id]['capacity'] = 0;
						$workloads['tNotAffec_'.$pc_id]['workload'] = 0;
						$tmp_workload = 0;
						foreach($employee_ids as $key => $employee_id){
							//Ticket #1115: start_date and end_date cua resource.
							$time_resource = array();
							if(!empty($employee_id)){
								$time_resource = $this->Employee->find('first', array(
									'recursive' => -1,
									'conditions' => array(
										'id' => $employee_id
									),
									'fields' => array('id', 'start_date', 'end_date')
								));
								$time_resource['Employee']['start_date'] = (!empty($time_resource['Employee']['start_date']) && ($time_resource['Employee']['start_date'] !='0000-00-00')) ? strtotime($time_resource['Employee']['start_date'] . ' 00:00:00') : 0;
								$sta = !empty($time_resource['Employee']['start_date']) ? $time_resource['Employee']['start_date'] : 0;
								$time_resource['Employee']['end_date'] = (!empty($time_resource['Employee']['end_date']) && ($time_resource['Employee']['end_date'] !='0000-00-00')) ? strtotime($time_resource['Employee']['end_date'] . ' 00:00:00') : 0;
								$end = !empty($time_resource['Employee']['end_date']) ? $time_resource['Employee']['end_date'] : 0;
							}
							$worksDayOfUser = $dayWorksConfigRoundWeek;
							foreach($worksDayOfUser as $k_time => $v_time){
								if(($sta != 0) && ($end != 0) && (($v_time > $end) || ($v_time < $sta)))unset($worksDayOfUser[$k_time]);
								if(($sta != 0) && ($end == 0) && ($v_time < $sta))unset($worksDayOfUser[$k_time]);
								if(($sta == 0) && ($end != 0) && ($v_time > $end))unset($worksDayOfUser[$k_time]);
							}
							$countHoliday = 0;
							if(!empty($holidays) && !empty($worksDayOfUser)){
								foreach($holidays as $date => $holiday){
									if(!empty($worksDayOfUser[$date])) $countHoliday += (count($holiday) / 2);
								}
							}
							$countWorkingDay = count($worksDayOfUser);
							$workloads[$employee_id]['capacity'] = $countWorkingDay - $countHoliday;
							if(!empty($absenceEmployees[$employee_id])) $workloads[$employee_id]['capacity'] = ($workloads[$employee_id]['capacity'] - $absenceEmployees[$employee_id]);
							if($employee_id != 'tNotAffec_'.$pc_id){
								$workloads[$pc_id]['capacity'] += $workloads[$employee_id]['capacity'];
								if(in_array($employee_id, $onlyEmployeeOfPC[$pc_id])){
									$tmp_workload += !empty($workloads[$employee_id]['workload']) ? $workloads[$employee_id]['workload'] : 0;
								}
							}else{
								// unset($resources[$pc_id][$key]);
								
							}
						}
						if( empty( $workloads[$pc_id]['workload'])) $workloads[$pc_id]['workload'] = 0;
						$workloads['tNotAffec_'.$pc_id]['workload'] = $workloads[$pc_id]['workload'] - $tmp_workload;
						$workloads['tNotAffec_'.$pc_id]['capacity'] = 0;
					}
				// }
				$dayWorks=array();
				$i=0;
				foreach($dayWorksConfigRoundWeek as $key=>$val){
					$i++;
					$dayWorks[$i]=array(0=>date('l d M',$key),1=>strtolower(date('l',$key)),2=>$key);
				}
				$this->set('dayWorks',$dayWorks);
			}elseif($typeSelect=='year'){
				$this->loadModels('AbsenceRequest','Project');
				$allEmployees = array();
				
				foreach($resources as $pc_id => $employee_ids ){
					foreach($employee_ids as $key => $employee_id){
						$allEmployees[] = $employee_id;
					}
				}
				$employee_date = $this->Employee->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $allEmployees
					),
					'fields' => array('id', 'start_date', 'end_date')
				));
				$list_employees = !empty($employee_date) ? Set::combine($employee_date, '{n}.Employee.id', '{n}.Employee') : array();
				$list_employees_id = !empty($employee_date) ? Set::combine($employee_date, '{n}.Employee.id', '{n}.Employee.id') : array();
				$employee_absence = $this->AbsenceRequest->sumAbsenceByEmployeeAndDate($list_employees_id, $_start, $_end,'validated', 'month');
				$getListWorkingDays = $this->Project->getWorkingDays($_start, $_end, true);
				$month_working_day = array();
				if(!empty($getListWorkingDays)){
					foreach ($getListWorkingDays as $key => $date) {
						if(empty($month_working_day[date('01_m_Y', $date)])) $month_working_day[date('01_m_Y', $date)] = 0;
						$month_working_day[date('01_m_Y', $date)] += 1;
					}
				}
				foreach($resources as $pc_id => $employee_ids ){
					if(empty($workloads[$pc_id])) $workloads[$pc_id] = array();
					foreach($employee_ids as $key => $id){
						if(in_array($id, $list_employees_id)){
							foreach ($month_working_day as $date => $value) {
								$f_date = str_replace('_', '-', $date);
								$f_date = strtotime($f_date);
								$n_month = mktime(0, 0, 0, date("m", $f_date) + 1, date("d", $f_date), date("Y", $f_date));
								if(empty($workloads[$pc_id]['capacity'][$f_date])) $workloads[$pc_id]['capacity'][$f_date] = 0;
								$e_start = (!empty($list_employees[ $id]['start_date']) && ($list_employees[ $id]['start_date'] !='0000-00-00')) ? strtotime($list_employees[ $id]['start_date']) : 0;
								$e_end = (!empty($list_employees[ $id]['end_date']) && ($list_employees[ $id]['end_date'] !='0000-00-00')) ? strtotime($list_employees[ $id]['end_date']) : 0;
								$is_work = 1;
								if((!empty($e_start) && $e_start > $n_month )
									|| (!empty($e_end) && ($e_end < $f_date))){
									// do nothing
								}else{
									$val = $value ? $value : 0;
									$cal_date = !empty($f_date) ? $f_date : 0;
									if(!empty($e_start) && !empty($cal_date)){
										while($cal_date < $n_month){
											if(in_array($cal_date,$getListWorkingDays)){
												if(!empty($e_start) && $cal_date < $e_start){
													$val = ($val - 1);
												}
												if(!empty($e_end) && ($cal_date > $e_end)){
													$val = ($val - 1);
												}
											}
											$cal_date = mktime(0, 0, 0, date("m", $cal_date), date("d", $cal_date) + 1, date("Y", $cal_date));
										}
									}
									$absence_value = !empty($employee_absence) && !empty($employee_absence[$id]) && !empty($employee_absence[$id][$date]) ? $employee_absence[$id][$date] : 0;
									$workloads[$pc_id]['capacity'][$f_date] += ($val - $absence_value);
								}
							}
						}
					}
				}
			}
			$absences = $this->Absence->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
				),
				'fields' => array('id', 'print')
			));
		}
		
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $this->employee_info['Company']['id']
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
		$adminYourformSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Details',
				'original_text' => 'Priority',
				'TranslationSetting.company_id' => $this->employee_info['Company']['id']
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
		$this->set(compact('typeSelect', 'profit', 'pc_list', 'companyName', 'workloads', 'employOfPC', 'project_manager', 'managerPC','adminTaskSetting','adminYourformSetting'));
		$this->set(compact('workdays', 'employees', 'profiltId', '_start', '_end', 'holidays', 'absences', 'resources'));
		$this->set(compact('displayProOppor','id_pro_oppor','list_phase', 'list_phase_color', 'date_selected', 'displayWlzero','displayProArchived','id_pro_archived', 'lists_pro_pri', 'list_priority'));
		
	}
	/**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfits($profitId,$getDataByPath = true) {
        if (!($user = $this->_getEmpoyee())) {
            return false;
        }
        $Model = ClassRegistry::init('ProfitCenter');
        //modify by QN, enable control_resource for PM
        $canManageResource = $this->employee_info['CompanyEmployeeReference']['role_id'] == 3 && $this->employee_info['CompanyEmployeeReference']['control_resource'];
        $myPC = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'conditions' => array(
                'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id IS NULL'),
                'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => 0),
                'employee_id' => $user['id']
            )
        ));
        $conds = array('manager_id' => $user['id']);
        if( $canManageResource )$conds['id'] = $myPC['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
        $profit = $Model->find('list', array(
            'recursive' => -1, 'fields' => array('id'),
            'order' => array('lft' => 'ASC'),
            'conditions' => array(
                'company_id' => $user['company_id'],
                'OR' => $conds
            )
        ));
        $this->loadModels('ProfitCenterManagerBackup');
        $backups = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $user['id']),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $profit = array_unique(array_merge($profit, $backups));
		$can_see_forecast = !empty($this->employee_info['Employee']['can_see_forecast']) ? $this->employee_info['Employee']['can_see_forecast'] : 0;
        $see_all_forecast = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr' || $can_see_forecast;
        if ($see_all_forecast) {
            $paths = array();
			$pc_list = $Model->getTreeList($user['company_id']);
			if(!empty($pc_list)){
				foreach($pc_list as $id => $value){
					if(!empty($value['name'])){
						$name = explode("|",  $value['name']);
						$paths[$id] = str_replace('--', '&nbsp;&nbsp;&nbsp;|-&nbsp;', $name[0]);
					}
				}
			}
        } elseif (!empty($profit)) {
            $paths = array();
            foreach($profit as $_val)
            {
                $paths[] = $_val;
                $pathsTemp = $Model->children($_val);
                $pathsTemp = Set::classicExtract($pathsTemp,'{n}.ProfitCenter.id');
                $paths = array_merge($paths,$pathsTemp);
            }
            $paths = $Model->generatetreelist(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } else {
            return false;
        }
      
        return $paths;
    }

	private function _getAbsencesResources($employees, $startDate, $endDate, $viewBy = 'month'){
		$this->loadModels('AbsenceRequest');
		$this->AbsenceRequest->virtualFields['total_absence'] = 'IF(response_am = "validated", 0.5, 0) + IF(response_pm = "validated", 0.5, 0)';
		$absences = $this->AbsenceRequest->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $employees,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'OR' => array(
					'response_am' => 'validated',
					'response_pm' => 'validated'
				)
			),
			'fields' => array(
				'employee_id',
				'total_absence',
				'date'
			)
		));
		
		$result = array();
		foreach($absences as $absence){
			$eid = $absence['AbsenceRequest']['employee_id'];
			$value = $absence['AbsenceRequest']['total_absence'];
			$g = $absence['AbsenceRequest']['date'];
			
			if( !isset($result[$eid]) ){
				$result[$eid] = $value;
			} else {
				$result[$eid] += $value;
			}
		}
		return $result;
	}
	function get_forecast_employee(){
		$_start = $_POST['start'];
		$_end = $_POST['end'];
		//Ticket #1176
		$this->loadModels('Employee', 'ProjectPhase','ProjectPhasePlan', 'Project','HistoryFilter', 'TmpStaffingSystem', 'Employee','Translation','TranslationSetting','ProjectPriority');
		$e_history = $this->Employee->HistoryFilter->find('list', array(
			'recursive' => -1,
			'fields' => array( 'path', 'params'),
			'conditions' => array(
				'path' => array('display_project_opportunity'),
				'employee_id' => $this->employee_info['Employee']['id']
			)
		));
		$displayProOppor = !empty($e_history['display_project_opportunity']) ? (int) $e_history['display_project_opportunity'] : 0;
		//Set gia tri default la 1.
		if(isset($e_history['display_project_archived'])){
			$displayProArchived = !empty($e_history['display_project_archived']) ? (int) $e_history['display_project_archived'] : 0;
		}else{
			$displayProArchived = 1;
		}
		$id_pro_oppor = $id_pro_archived = $id_pro_model = array();
		$id_pro_oppor = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 2,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$id_pro_archived = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$id_pro_model = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 4,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$pro_not_display = $lists_pro_pri = $list_priority = array();
		if(!empty($id_pro_model)){
			$pro_not_display = $id_pro_model;
		}
		if(!empty($id_pro_oppor)){
			if($displayProOppor == 0){
				if(!empty($pro_not_display)){
					$pro_not_display = array_merge($id_pro_oppor,$pro_not_display);
				}else{
					$pro_not_display = $id_pro_oppor;
				}
			}
		}
		if(!empty($id_pro_archived)){
			if($displayProArchived == 0){
				if(!empty($pro_not_display)){
					$pro_not_display = array_merge($id_pro_archived,$pro_not_display);
				}else{
					$pro_not_display = $id_pro_archived;
				}
			}
		}
		//List project va priority_id
		$lists_pro_pri = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'NOT' => array(
					array('id' => $pro_not_display),
					array('project_priority_id' => null),
				) 
			),
			'fields' => array('id','project_priority_id')
		));
		//List Priority
		$list_priority = $this->ProjectPriority->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id','priority')
		));
		$list_phase = $this->ProjectPhase->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'joins' => array(
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhase.id'
					)
				)
			),
			'fields' => array('ProjectPhasePlan.id', 'ProjectPhase.name')
		));
		$list_phase_color = $this->ProjectPhase->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'joins' => array(
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhase.id'
					)
				)
			),
			'fields' => array('ProjectPhasePlan.id', 'ProjectPhase.color')
		));
		// End ticket #1176
		$_absences = !empty($_POST['absences'])? $_POST['absences'] : array();
		$_holidays = !empty($_POST['holidays']) ? $_POST['holidays'] : array();
		$_workdays = !empty($_POST['workdays'])? $_POST['workdays'] : array();
		$_dataFormat = !empty($_POST['dataFormat'])? $_POST['dataFormat'] : array();
		$_colspan = !empty($_POST['colspan'])? $_POST['colspan'] : 8;
		$_dayMaps = !empty($_POST['dayMaps'])? $_POST['dayMaps'] : array();
		$_pc_id = !empty($_POST['pc_id'])? $_POST['pc_id'] : null;
		$_typeSelect = 'month';
		list($profit, $paths, $employOfPC, $employeeName) = $this->_getProfitFollowDates($_pc_id, null , $_start, $_end);
		$employOfPC[$profit['id']] = $profit['name'];
		$_employees = array();
		$_employees[$_POST['employe_id']] = !empty($employOfPC[$_POST['employe_id']]) ? $employOfPC[$_POST['employe_id']] : __('Not Affected', true);
		//Ticket #1115: start_date and end_date cua resource.
		$time_resource = array();
		if(!empty($employOfPC[$_POST['employe_id']])){
			$time_resource = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $_POST['employe_id']
				),
				'fields' => array('id', 'start_date', 'end_date')
			));
			$time_resource['Employee']['start_date'] = (!empty($time_resource['Employee']['start_date']) && ($time_resource['Employee']['start_date'] !='0000-00-00')) ? strtotime($time_resource['Employee']['start_date'] . ' 00:00:00') : 0;
			$time_resource['Employee']['end_date'] = (!empty($time_resource['Employee']['end_date']) && ($time_resource['Employee']['end_date'] !='0000-00-00')) ? strtotime($time_resource['Employee']['end_date'] . ' 00:00:00') : 0;
		}
		//
		$workloads = $this->_workloadFromTaskForecasts($_employees, $_start, $_end, 1, $_typeSelect, $_pc_id);
		$pc_workload = array();
		$pc_workload[$_pc_id] = $workloads;
		list($managerPC, $project_manager) = $this->getPermisionForecast($pc_workload);
		
		$adminTaskSetting = $adminYourformSetting = array();
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $this->employee_info['Company']['id']
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
		$adminYourformSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Details',
				'original_text' => 'Priority',
				'TranslationSetting.company_id' => $this->employee_info['Company']['id']
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
		
		$this->set(compact('workloads','_start', '_end', '_employees', '_absences', '_workdays', '_holidays', '_colspan', '_dayMaps', '_dataFormat', 'employOfPC', '_pc_id', 'managerPC', 'project_manager'));
		$this->set(compact('displayProOppor','id_pro_oppor','list_phase', 'list_phase_color','displayProArchived', 'id_pro_archived', 'lists_pro_pri', 'list_priority','adminTaskSetting','adminYourformSetting'));
		$this->set(compact('time_resource'));
		
	}
	/**
     * my_diary
     *
     * @return void
     * @access public
     */
    function my_diary($typeSelect=null) {
		$this->loadModels('ProfitCenter', 'HistoryFilter', 'ProjectEmployeeProfitFunctionRefer', 'Workday');

		$employee_id = $this->employee_info['Employee']['id'];
		// MY DIARY
		$hasManagerMyDiary = (!empty($employee_id) && !empty($this->employee_info['Company']['id']) && ($profiltId = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array('employee_id' => $employee_id), 'fields' => array('profit_center_id')))));
		
		$profiltId = !empty($profiltId) ? array_unique(Set::extract($profiltId, '{n}.ProjectEmployeeProfitFunctionRefer.profit_center_id')) : array();
		$pc_list  = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'name'),
            'order' => array('lft' => 'ASC'),
            'conditions' => array(
				'id' => $profiltId,
            ),
			
        ));
		if($this->data){
			$typeSelect = $this->data['date_type'];
			$date = !empty($this->data['date']) ? $this->data['date'] : date('d-m-Y', time());
		}else{
			
			$history = $this->HistoryFilter->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'employee_id' => $this->employee_info['Employee']['id'],
					'path' => $this->params['url']['url'],
				),
				'fields' => array('params')
			));
			if(!empty($history)){
				$history = unserialize($history['HistoryFilter']['params']);
				$typeSelect = !empty($history['data[date]']) ? $history['data[date_type]'][0] : 'week';
				$date = !empty($history['data[date]']) ? $history['data[date]'] : date('d-m-Y', time());
			}else{
				$typeSelect = "week";
				$date = date('d-m-Y', time());
			}
		}
		$this->set('typeSelect',$typeSelect);
	
        if($typeSelect =='week'){
            $_start = strtotime('monday this week', strtotime($date));
			$_end = strtotime('friday this week', strtotime($date));
        }elseif($typeSelect =='month'){
            $this->loadModel('ActivitySetting');
            $showFullDay = $this->ActivitySetting->find('first',array('fields' => 'show_full_day_in_month'));
            $showFullDay=$showFullDay['ActivitySetting']['show_full_day_in_month']?false:true;
            $_start = strtotime('first day of this month', strtotime($date));
			$_end = strtotime('last day of this month', strtotime($date));
        }else{
            $_start = $this->viewVars['_start'] = strtotime("01/01/".date('Y',strtotime($date)));
            $_end = $this->viewVars['_end'] = strtotime("12/31/".date('Y',strtotime($date)));
        }
        if (!($params = $this->_getProfitMyDiarys($profiltId))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $result = $this->_workloadFromTaskForecasts($employees, $_start, $_end, 1, $typeSelect);
        $workloads[$profiltId[0]] = $result;
        $workloads[$profiltId[0]]['employees'] = $employees;
        $workdays = $this->Workday->getOptions($this->employee_info['Company']['id']);
		// remove sunday, saturday
		foreach($workdays as $key=>$val){
			if($val == 0) unset($workdays[$key]);
		}

        $this->_parse($_start, $_end, $employeeName, $employees, $profit['id']);
        $this->loadModels('ActivityRequestConfirm', 'Profile');
        $requestConfirms = $this->ActivityRequestConfirm->find('list', array('recursive' => -1,
            'group' => 'employee_id', 'fields' => array('employee_id', 'status'),
            'conditions' => array('employee_id' => array_keys($employees), 'status' => array(0, 2),
                'start' => $_start, 'end' => $_end)));
				
        $projectStatus = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
			'order' => array('weight'),
            'fields' => array('id', 'name', 'status')
        ));
        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
		$absences = $this->Absence->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'print')
		));
	
        $companyName = $this->employee_info['Company']['company_name'];
        $this->loadModel('CompanyConfig');
        
		$projectStatus = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus') : array();
		
		$is_admin = $this->employee_info['Role']['name'] ==  'admin' ? 1 : 0;
		$is_diaryModify = !empty($this->companyConfigs['diary_modify']) ? $this->companyConfigs['diary_modify'] : 0;
		$is_diaryStatus = !empty($this->companyConfigs['diary_status']) ? $this->companyConfigs['diary_modify'] : 0;
		$is_diaryOtherField = !empty($this->companyConfigs['diary_others_fields']) ? $this->companyConfigs['diary_others_fields'] : 0;
		$is_change = (($is_admin == 1) || (($is_diaryModify == 1) && ($is_diaryStatus == 1))) ? 1 : 0;
		$is_changeCommentAndFile = (($is_admin == 1) || (($is_diaryModify == 1) && ($is_diaryOtherField == 1))) ? 1 : 0;
		$api_key = $this->employee_info['Employee']['api_key'];
		$time_resource = array();
		if(!empty($employees)){
			$time_resource = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $employees
				),
				'fields' => array('id', 'start_date', 'end_date')
			));
			$time_resource['Employee']['start_date'] = (!empty($time_resource['Employee']['start_date']) && ($time_resource['Employee']['start_date'] !='0000-00-00')) ? strtotime($time_resource['Employee']['start_date'] . ' 00:00:00') : 0;
			$time_resource['Employee']['end_date'] = (!empty($time_resource['Employee']['end_date']) && ($time_resource['Employee']['end_date'] !='0000-00-00')) ? strtotime($time_resource['Employee']['end_date'] . ' 00:00:00') : 0;
		}
        $this->set(compact('profit', 'paths', 'employeeName', 'requestConfirms', 'workloads', 'profiles', 'companyName', 'absences', 'profiltId', 'pc_list', 'projectStatus', '_start', '_end', 'workdays', 'employees', 'is_change', 'api_key', 'is_changeCommentAndFile', 'time_resource'));
        if($typeSelect=='month'){

            $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
            $dayWorksConfigRoundWeek = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
            $dayWorks=array();
            $i=0;
            foreach($dayWorksConfigRoundWeek as $key=>$val)
            {
                $i++;
                $dayWorks[$i]=array(0=>date('l d M',$key),1=>strtolower(date('l',$key)),2=>$key);
            }
            $this->set('dayWorks',$dayWorks);
        }elseif($typeSelect=='year'){
            $dayWorks = $this->_showDayInYear(date('Y', $_start));
            $this->set('dayWorks',$dayWorks);
        }
        $this->set('isDiary',1);
        
    }
	private function _workloadFromTaskForecasts($employee, $start, $end,$config=null,$typeSelect=null, $profit = null){
		// Khi thay doi function nay can kiem tra lai man hinh my diary
		$a = time();
		$this->loadModels('Activity', 'Project', 'ProjectTask', 'ProjectTaskEmployeeRefer', 'ActivityTaskEmployeeRefer', 'ActivityTask', 'NctWorkload', 'AbsenceRequest', 'ProjectStatus', 'AbsenceRequest');
		
		list($isManage, $employeeName) = $this->_getProfitEmployee();
        $company_id = $employeeName['company_id'];
		$_startDateProject = date( 'Y-m-d', $start);
        $_endDateProject = date('Y-m-d', $end); 

		$activityActivatedYes = $this->Activity->find('list', array(
			'recursive' => -1,
			'conditions' => array('Activity.activated' => 1),
			'fields' => array('id', 'id')
		));
		//Ticket #1176. List project oppor
		$e_history = $this->Employee->HistoryFilter->find('list', array(
			'recursive' => -1,
			'fields' => array( 'path', 'params'),
			'conditions' => array(
				'path' => array('display_project_opportunity', 'display_task_wlzero','display_project_archived'),
				'employee_id' => $this->employee_info['Employee']['id']
			)
		));
		$displayProOppor = !empty($e_history['display_project_opportunity']) ? (int) $e_history['display_project_opportunity'] : 0;
		$displayWlzero = (!isset($e_history['display_task_wlzero']) || !empty($e_history['display_task_wlzero'])) ? 1 : 0;
		//Set gia tri default la 1.
		if(isset($e_history['display_project_archived'])){
			$displayProArchived = !empty($e_history['display_project_archived']) ? (int) $e_history['display_project_archived'] : 0;
		}else{
			$displayProArchived = 1;
		}
		$id_pro_oppor = $id_pro_archived = $id_pro_model = array();
		$id_pro_oppor = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 2,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$id_pro_archived = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$id_pro_model = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 4,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id')
		));
		$pro_not_display = array();
		if(!empty($id_pro_model)){
			$pro_not_display = $id_pro_model;
		}
		if(!empty($id_pro_oppor)){
			if($displayProOppor == 0){
				if(!empty($pro_not_display)){
					$pro_not_display = array_merge($id_pro_oppor,$pro_not_display);
				}else{
					$pro_not_display = $id_pro_oppor;
				}
			}
		}
		if(!empty($id_pro_archived)){
			if($displayProArchived == 0){
				if(!empty($pro_not_display)){
					$pro_not_display = array_merge($id_pro_archived,$pro_not_display);
				}else{
					$pro_not_display = $id_pro_archived;
				}
			}
		}
		
		$project_conds = array(
			'activity_id' => $activityActivatedYes
		);
        $projectActivatedYes = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => $project_conds,
			'fields' => array('activity_id', 'id')
		));
        $projectStatus = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'status', 'name'),
			'order' => array('weight'),
            'conditions' => array('company_id' => $company_id))
        );
		
		$status = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus.status') : array();
		$projectStatus = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus') : array();
		$project_ids = $activity_ids = array();	
		// workload = estimated - holiday - absence
		$dataProject = $dataActvity = array();
		// Get estimated in project task
		$projectTasks = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
			'ProjectTask.project_id' => $projectActivatedYes,
			'conditions' => array(
				array(
					'OR' => array(
						'ProjectTask.task_start_date BETWEEN ? AND ?' => array($_startDateProject, $_endDateProject),
						'ProjectTask.task_end_date BETWEEN ? AND ?' => array($_startDateProject, $_endDateProject),
						array(
							'ProjectTask.task_start_date < ?' => array($_startDateProject),
							'ProjectTask.task_end_date > ?' => array($_startDateProject)
						),
						array(
							'ProjectTask.task_start_date < ?' => array($_endDateProject),
							'ProjectTask.task_end_date > ?' => array($_endDateProject)
						)
					),
				),
				array(
					'OR' => array(
						array(
							'ProjectTaskEmployeeRefer.reference_id' => array_keys($employee),
							'ProjectTaskEmployeeRefer.is_profit_center' => 0
						),
						array(
							'ProjectTaskEmployeeRefer.reference_id' => $profit,
							'ProjectTaskEmployeeRefer.is_profit_center' => 1
						)
					)
				),
				array('NOT' => array('ProjectTask.project_id' => $pro_not_display))
			),
			'order'=>array('ProjectTask.project_id'),
			'fields' => array('ProjectTask.id','ProjectTask.project_id', 'ProjectTask.task_title','ProjectTask.task_start_date', 'ProjectTask.task_end_date','ProjectTask.is_nct','reference_id', 'is_profit_center','estimated', 'ProjectTask.task_status_id', 'ProjectTask.project_planed_phase_id', 'ProjectTask.parent_id', 'ProjectTask.task_priority_id')
		));
		
		$activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('project_task_id','id'),
            'conditions' => array('project_task_id <>' => null )
        ));
		$pTaskIds = array();
		$parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
		// Ticket #3481. Loi hien thi task cha khi task con duoc assign cho employee khac voi employee khac task cha.
		$listTaskIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.id'));
		if(!empty($listTaskIds)){
			$tasksHasChild = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => $listTaskIds
				),
				'fields' => array('parent_id', 'parent_id'),
				'group' => array('parent_id')
			));
		}
		foreach($projectTasks as $key => $projectTask){
			$pTaskIds[] = $projectTask['ProjectTask']['id'];
			$_taskId = $projectTask['ProjectTask']['id'];
            if(!in_array($_taskId, $parentIds) && !in_array($_taskId, $tasksHasChild)){
				$_start = !empty($projectTask['ProjectTask']['task_start_date']) ? ($projectTask['ProjectTask']['task_start_date']) : 0;
				$_end = !empty($projectTask['ProjectTask']['task_end_date']) ? ($projectTask['ProjectTask']['task_end_date']) : 0;
				$checkTaskLink = isset($activityTasks[$projectTask['ProjectTask']['id']]) ? $activityTasks[$projectTask['ProjectTask']['id']] : 0;
				$empId = ($projectTask['ProjectTaskEmployeeRefer']['is_profit_center'] == 1) ? 'tNotAffec_'.$profit : $projectTask['ProjectTaskEmployeeRefer']['reference_id'];
				
				$dataProject[$key]['task_id'] = $projectTask['ProjectTask']['id'];
				$dataProject[$key]['p_task_id'] = $projectTask['ProjectTask']['id'];
				$dataProject[$key]['employee_id'] = $empId;
				$dataProject[$key]['start'] = $_start;
				$dataProject[$key]['end'] = $_end;
				$dataProject[$key]['task_title'] = $projectTask['ProjectTask']['task_title'];
				$dataProject[$key]['project_id'] = $projectTask['ProjectTask']['project_id'];
				$project_ids[$key] = $projectTask['ProjectTask']['project_id'];
				$dataProject[$key]['nct'] = isset($projectTask['ProjectTask']['is_nct']) ? $projectTask['ProjectTask']['is_nct'] : 0;
				$dataProject[$key]['is_pc'] = $projectTask['ProjectTaskEmployeeRefer']['is_profit_center'];
				$dataProject[$key]['estimated'] = $projectTask['ProjectTaskEmployeeRefer']['estimated'];
				$dataProject[$key]['task_status'] = $projectTask['ProjectTask']['task_status_id'];
				$dataProject[$key]['project_planed_phase_id'] = $projectTask['ProjectTask']['project_planed_phase_id'];
				$dataProject[$key]['task_priority_id'] = $projectTask['ProjectTask']['task_priority_id'];
			}

		}
		// Get estimated in activity task
		$actiTasks = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ActivityTask.activity_id' => $activityActivatedYes,
                'ActivityTask.project_task_id' => null,
                array(
                    'OR' => array(
                        'ActivityTask.task_start_date BETWEEN ? AND ?' => array($start, $end),
                        'ActivityTask.task_end_date BETWEEN ? AND ?' => array($start, $end),
                        array(
                            'ActivityTask.task_start_date < ?' => array($start),
                            'ActivityTask.task_end_date > ?' => array($start)
                        ),
                        array(
                            'ActivityTask.task_start_date < ?' => array($end),
                            'ActivityTask.task_end_date > ?' => array($end)
                        )
                    ),
                ),
                array(
                    'OR' => array(
                        array(
                            'ActivityTaskEmployeeRefer.reference_id' => array_keys($employee),
                            'ActivityTaskEmployeeRefer.is_profit_center' => 0
                        ),
                        array(
                            'ActivityTaskEmployeeRefer.reference_id' => $profit,
                            'ActivityTaskEmployeeRefer.is_profit_center' => 1
                        )
                    )
                )
            ),
            'order'=>array('ActivityTask.activity_id'),
			'fields' => array('ActivityTask.id', 'ActivityTask.activity_id', 'ActivityTask.task_start_date', 'ActivityTask.task_start_date', 'ActivityTask.name','ActivityTask.is_nct', 'reference_id', 'is_profit_center','estimated')
        ));
		$aTaskIds = array();
		foreach($actiTasks as $key => $actiTask){
			$aTaskIds[] = $actiTask['ActivityTask']['id'];
			$_startAT = !empty($actiTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_start_date']) : 0;
			$_endAT = !empty($actiTask['ActivityTask']['task_end_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_end_date']) : 0;
			$empId = ($actiTask['ActivityTaskEmployeeRefer']['is_profit_center'] == 1) ? 'tNotAffec_'.$profit : $actiTask['ActivityTaskEmployeeRefer']['reference_id'];
			$dataActvity[$key]['task_id'] = $actiTask['ActivityTask']['id'];
			$dataActvity[$key]['employee_id'] = $empId;
			$dataActvity[$key]['start'] = $_startAT;
			$dataActvity[$key]['end'] = $_endAT;
			$dataActvity[$key]['estimated'] = $actiTask['ActivityTaskEmployeeRefer']['estimated'];
			$dataActvity[$key]['task_title'] = $actiTask['ActivityTask']['name'];
			$dataActvity[$key]['nct'] = isset($actiTask['ActivityTask']['is_nct']) ? $actiTask['ActivityTask']['is_nct'] : 0;
			$dataActvity[$key]['is_pc'] = $actiTask['ActivityTaskEmployeeRefer']['is_profit_center'];
			$activity_ids[$key] = $actiTask['ActivityTask']['activity_id'];
		}
		
		$datas = array();
		if(!empty($dataProject) && empty($dataActvity)){
            $datas = $dataProject;
        } elseif(empty($dataProject) && !empty($dataActvity)){
            $datas = $dataActvity;
        } else {
            $datas = array_merge($dataProject, $dataActvity);
        }

		//Get data estimated of nct task
		$NCTTasks = $this->NctWorkload->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				array(
                    'OR' => array(
                        'task_date BETWEEN ? AND ?' => array($_startDateProject, $_endDateProject),
                        'end_date BETWEEN ? AND ?' => array($_startDateProject, $_endDateProject),
                        'AND' => array(
                            'task_date <' =>  $_startDateProject ,
                            'end_date >' => $_endDateProject
                        ),
                    )
                ),
                'OR' => array(
                    'NctWorkload.project_task_id' => $pTaskIds,
                    'NctWorkload.activity_task_id' => $aTaskIds
                ),
                'AND' => array(
                    'OR' => array(
                        array(
                            'NctWorkload.reference_id' => array_keys($employee),
                            'NctWorkload.is_profit_center' => 0
                        ),
                        array(
                            'NctWorkload.reference_id' => $profit,
                            'NctWorkload.is_profit_center' => 1
                        )
                    )
                )
            )
        ));
		$exProjectTasks = !empty($projectTasks) ? Set::combine($projectTasks,'{n}.ProjectTask.id','{n}.ProjectTask') : array();
		
		$listkNcts = array();
        if(!empty($NCTTasks)){
            foreach($NCTTasks as $NCTTask){
                $dx = $NCTTask['NctWorkload'];
                $nStartDate = strtotime($dx['task_date']);
                $nEndDate = strtotime($dx['end_date']);
                $project_task_id = $dx['project_task_id'];
                if( !empty($dx['project_task_id']) ){
					if(!empty($exProjectTasks[$dx['project_task_id']]) && !empty($exProjectTasks[$dx['project_task_id']]['task_start_date'])){
						$nStartDate = strtotime($exProjectTasks[$dx['project_task_id']]['task_start_date']) > $nStartDate ? strtotime($exProjectTasks[$dx['project_task_id']]['task_start_date']) : $nStartDate;
					}
					if(!empty($exProjectTasks[$dx['project_task_id']]) && !empty($exProjectTasks[$dx['project_task_id']]['task_end_date'])){
						$nEndDate = strtotime($exProjectTasks[$dx['project_task_id']]['task_end_date']) < $nEndDate ? strtotime($exProjectTasks[$dx['project_task_id']]['task_end_date']) : $nEndDate;
					}
                }
				$resouce_id = !empty($dx['is_profit_center']) ? $profit : $dx['reference_id'];
                $dx['reference_id'] = !empty($dx['is_profit_center']) ? 'tNotAffec_'.$profit : $dx['reference_id'];
                if( !empty($listkNcts[$project_task_id][$dx['reference_id']]) ){
                    $listkNcts[$project_task_id][$dx['reference_id']] = $this->NctWorkload->divideWorkloadForResource($dx['estimated'], $resouce_id, $dx['is_profit_center'], $nStartDate, $nEndDate, $start, $end) + $listkNcts[$project_task_id][$dx['reference_id']];
				
                } else {
                    $listkNcts[$project_task_id][$dx['reference_id']] = $this->NctWorkload->divideWorkloadForResource($dx['estimated'], $resouce_id, $dx['is_profit_center'], $nStartDate, $nEndDate, $start, $end);
                }
            }
        }
		
		$project_ids = array_unique($project_ids);
		$project_names = array();
		$project_date = array();
		if(!empty($project_ids)){
			$project_list = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $project_ids,
				),
				'fields' => array('id', 'project_name', 'start_date', 'end_date')
			));
			$project_names = Set::combine($project_list,'{n}.Project.id','{n}.Project.project_name');
			$project_date = Set::combine($project_list,'{n}.Project.id','{n}.Project');
		}
		/* Get data workload and format data */
		$workdays = ClassRegistry::init('Workday')->getOptions($company_id);
		$dataFilter = array();
		
		$requestQuerys = $this->AbsenceRequest->find("all", array(
			'recursive' => -1,
			"conditions" => array(
				'date BETWEEN ? AND ?' => array($start, $end), 
				'employee_id' => array_keys($employee))
			)
		);
		
        $absenceRequest = array();
		$amAbsence = array();
		$pmAbsence = array();
		
        if(!empty($requestQuerys)){
            foreach($requestQuerys as $_requestQuery){
                $dx = $_requestQuery['AbsenceRequest'];
                $absenceRequest[$dx['employee_id']][$dx['date']]['am'] = $dx['response_am'];
                $absenceRequest[$dx['employee_id']][$dx['date']]['absence_am'] = $dx['absence_am'];
                $absenceRequest[$dx['employee_id']][$dx['date']]['pm'] = $dx['response_pm'];
                $absenceRequest[$dx['employee_id']][$dx['date']]['absence_pm'] = $dx['absence_pm'];
            }
        }
		
		$min_date = date('Y-m-d', $start);
		$max_date = date('Y-m-d', $end);
		$list_project_start = array();
		
		if(!empty($datas)){
			foreach($datas as $key => $data){
				$min_date = (strtotime($min_date) > strtotime($data['start'])) ? $data['start'] : $min_date;
				$max_date = (strtotime($max_date) < strtotime($data['end'])) ? $data['end'] : $max_date;
				if(empty($list_project_start[$data['project_id']])){
					$list_project_start[$data['project_id']] = $data['start'];
				}else{
					$list_project_start[$data['project_id']] = $min_date;
				}
			}
		}		
		$notWorking = $this->_getHoliday($min_date, $max_date);
		$workingdays = $this->_getWorkingDays($min_date, $max_date, $notWorking);
		// $holidays = ClassRegistry::init('Holiday')->getOptionHolidays(strtotime($min_date), strtotime($max_date), $company_id);
		$taskAbsencesAm = $this->AbsenceRequest->find("all", array(
			'recursive' => -1,
			'conditions' => array(
				'date BETWEEN ? AND ?' => array(strtotime($min_date), strtotime($max_date)), 
				'response_am' => 'validated', 
				'employee_id' => array_keys($employee)
			),
			'fields' => array('employee_id', 'date'),
		));
		$amAbsences = array();
		foreach($taskAbsencesAm as $key => $amAB){
			$dx = $amAB['AbsenceRequest'];
			if(empty($amAbsences[$dx['employee_id']])) $amAbsences[$dx['employee_id']] = array();
			$amAbsences[$dx['employee_id']][] = $dx['date'];
			
		}
		$taskAbsencesPm = $this->AbsenceRequest->find("all", array(
			'recursive' => -1,
			'conditions' => array(
				'date BETWEEN ? AND ?' => array(strtotime($min_date), strtotime($max_date)),
				'response_pm' => 'validated', 
				'employee_id' => array_keys($employee)
			),
			'fields' => array('employee_id', 'date'),
		));
		$pmAbsences = array();
		
		foreach($taskAbsencesPm as $key => $amAB){
			$dx = $amAB['AbsenceRequest'];
			if(empty($pmAbsences[$dx['employee_id']])) $pmAbsences[$dx['employee_id']] = array();
			$pmAbsences[$dx['employee_id']][] = $dx['date'];
		}
		$employee_absence_has_task = array();
		if(!empty($datas)){
			foreach($datas as $key => $data){
				
				$taskStart = !empty($data['start']) ? strtotime($data['start']) : 0;
                $taskEnd = !empty($data['end']) ? strtotime($data['end']) : 0;
				$holidays = ClassRegistry::init('Holiday')->getOptionHolidays($taskStart, $taskEnd, $company_id);
				$taskWorkingDays = 0;
				
				if(!empty($workingdays)){
					 foreach($workingdays as $index => $date){
						 if($date >= $taskStart && $date <= $taskEnd){
							 $taskWorkingDays++;
						 }
					 }
				 } 
				 $taskAbsencesAm = 0;
				 if(!empty($amAbsences[$data['employee_id']])){
					 foreach($amAbsences[$data['employee_id']] as $index => $date){
						  
						 if($date >= $taskStart && $date <= $taskEnd){
							  $taskAbsencesAm++;
						 }else{
							 unset($amAbsences[$data['employee_id']][$date]);						 
						 }
					 }
				 } 
				
				 $taskAbsencesPm = 0;
				 if(!empty($pmAbsences[$data['employee_id']])){
					 foreach($pmAbsences[$data['employee_id']] as $index => $date){
						 if($date >= $taskStart && $date <= $taskEnd){
							  $taskAbsencesPm++;
						 }else{
							 unset($pmAbsences[$data['employee_id']][$date]);						 
						 }
					 }
				 }

				$taskAbsences = $taskAbsencesAm + $taskAbsencesPm;
				
				$countTaskHoliday = 0;
				$holidayForNW=array();
			
				foreach($holidays as $day => $value){
					$date_text = strtolower(date("l", $day));
					if($day <= $taskEnd && $day >= $taskStart && $workdays[$date_text] != 0){
						if(isset($value['am']))$countTaskHoliday += 1;
						if(isset($value['pm']))$countTaskHoliday += 1;

						$holidayForNW[] = date('m-d-Y',$day);
					}
				}
				$taskNotWorking = array_merge($notWorking,$holidayForNW);
				$taskNotWorking = array_unique($taskNotWorking);
	
				$realTaskWorkingDays = $taskWorkingDays * 2 - $countTaskHoliday - $taskAbsences;
				
				// Absences and holiday during the task day
				
				$during_off = 0;
				if($realTaskWorkingDays == 0) {
					$realTaskWorkingDays = $taskWorkingDays * 2 - $countTaskHoliday; 
					$during_off = 1;
				}
				$taskWorkload1Day = array(
					'number' => 0,
					'original' => 0,
					'remainder' => 0,
				);
				
				$WORKLOADBYDAY = array();
				$workDaysByMonths = array();
				$allowCaculate = true;
				if( isset($data['estimated']) && $data['estimated'] == 0 ){
					$allowCaculate = false;
				}
				else
				{
					$am_absence = !empty($amAbsences[$data['employee_id']]) ? $amAbsences[$data['employee_id']] :  array();
					$pm_absence = !empty($pmAbsences[$data['employee_id']]) ? $pmAbsences[$data['employee_id']] :  array();
					$workDaysByMonths = $this->ProjectTask->Behaviors->Lib->getWorkingDaysByMonthsForEmployee($taskStart, $taskEnd, $am_absence, $pm_absence);
					$WORKDAYS = $workDaysByMonths['total'];
					$allowCaculate = true;
					$WORKLOADBYDAY = $this->ProjectTask->Behaviors->Lib->caculateGlobal($data['estimated'], $WORKDAYS * 2 , true);
					$workDaysByMonths = $workDaysByMonths['result'];
					$estis = $WORKLOADBYDAY['original'];
					$remainder = $WORKLOADBYDAY['remainder'];
					$numberOfRemainder = $WORKLOADBYDAY['number'];

				}
				// Get data task on time filter (week / month)
				$_start = $start;
                $_end = $end;
				$num_remaind = 0;
				if($allowCaculate){
					$TIME=strtotime(date('01-m-Y', $start));
					$wlindexPlus = 0;
					if(!empty($workDaysByMonths[$TIME])){
						$_workDaysByMonths = $workDaysByMonths[$TIME];
						$wlindexPlus = $_workDaysByMonths['indexPlus'];
					}
					$num_remaind = $numberOfRemainder - $wlindexPlus;
					$num_remaind = ($num_remaind > 0) ? $num_remaind : 0;
				}
				while($_start <= $_end){
					$ab_date = 0;
					$dateWorking = strtolower(date("l", $_start));
					if($workdays[$dateWorking] == 0){
						// Ngay khong lam viec
					}else{
						$is_working = 1;
						if(!empty($holidays[$_start])){
							$dataFilter[$key][$_start]['holiday'] = array();
							$dataFilter[$key][$_start]['holiday']['date'] = $_start;
							$dataFilter[$key][$_start]['holiday']['employee_id'] = $data['employee_id'];
							$is_working = 0;
							$ab_date = 1;
						}else if(!empty($absenceRequest[$data['employee_id']][$_start])){
							// $dataFilter[$key][$_start]['absence'] = array();
							$dataFilter[$key][$_start]['absence'] = $absenceRequest[$data['employee_id']][$_start];
							$dataFilter[$key][$_start]['absence']['date'] = $_start;
							$dataFilter[$key][$_start]['absence']['employee_id'] = $data['employee_id'];
							if($absenceRequest[$data['employee_id']][$_start]['am'] == 'validated') $ab_date += 0.5;
							if($absenceRequest[$data['employee_id']][$_start]['pm'] == 'validated') $ab_date += 0.5;
							$is_working = 0;
							if($during_off == 1) $is_working = 1;
							// unset($absenceRequest[$data['employee_id']][$_start]);
							$employee_absence_has_task[] = $data['employee_id'];
						} 
						
						// Get data task on date filter [start, end]
						if($taskStart <= $_start && $_start <= $taskEnd && (!empty($displayWlzero) || (empty($displayWlzero) && isset($data['estimated']) && $data['estimated'] > 0))) {
							// data for task
							$dataFilter[$key][$_start]['tasks'] = array();
							$dataFilter[$key][$_start]['tasks']['is_pc'] = $data['is_pc'];
							$dataFilter[$key][$_start]['tasks']['task_priority_id'] = $data['task_priority_id'];
							$dataFilter[$key][$_start]['tasks']['task_id'] = $data['task_id'];
							$dataFilter[$key][$_start]['tasks']['project_planed_phase_id'] = $data['project_planed_phase_id'];
							$dataFilter[$key][$_start]['tasks']['p_task_id'] = isset($data['p_task_id']) && !empty($data['p_task_id']) ? $data['p_task_id'] : 0;
							$dataFilter[$key][$_start]['tasks']['date'] = $_start;
							$dataFilter[$key][$_start]['tasks']['employee_id'] = $data['employee_id'];
							$dataFilter[$key][$_start]['tasks']['nameTask'] = !empty($data['task_title']) ? $data['task_title'] : '';
							$dataFilter[$key][$_start]['tasks']['project_name'] = !empty($project_names[$data['project_id']]) ? $project_names[$data['project_id']] : '';
							$dataFilter[$key][$_start]['tasks']['project_id'] = !empty($data['project_id']) ? $data['project_id'] : '';
							$dataFilter[$key][$_start]['tasks']['task_status'] = !empty($data['task_status']) ? $data['task_status'] : 'IP';
							$dataFilter[$key][$_start]['tasks']['task_start_date'] = !empty($data['start']) ? $data['start'] : '';
							$dataFilter[$key][$_start]['tasks']['task_end_date'] = !empty($data['end']) ? $data['end'] : '';
							$_workloadResidual = 0;
							$_workload = 0;
							if($taskWorkingDays == 0){
								$_workload = 0;
							} else if(!empty($WORKLOADBYDAY)){
								$_workload = $WORKLOADBYDAY['original'];
								$_workloadResidual = $WORKLOADBYDAY['remainder'];
							}
							$_real_worload = $_workload*2;
							$_real_residual = $_workloadResidual;
							if($ab_date == 0.5){
								$_real_worload = $_workload;
								$_real_residual = $_workloadResidual / 2;
							}
							if($num_remaind > 0){
								$_workloadDbl = round($_real_worload, 2) + $_real_residual;
								$num_remaind -= (1 - $ab_date);
							} else {
								$_workloadDbl = round($_real_worload, 2);
							}
							//Neu khong phai working day thi workload = 0
							if($is_working == 0 && $ab_date == 1) $_workloadDbl = 0;
							$dataFilter[$key][$_start]['tasks']['workload'] = $_workloadDbl;
							// task nct
							$dataFilter[$key][$_start]['tasks']['nct'] = 0;
							if(!empty($data['nct'])){
								if(isset($listkNcts[$data['p_task_id']][$data['employee_id']][$_start])){
									$workload = $listkNcts[$data['p_task_id']][$data['employee_id']][$_start];
									if($is_working == 0) $workload = 0;
									$dataFilter[$key][$_start]['tasks']['workload'] = $workload;
									
									$dataFilter[$key][$_start]['tasks']['nct'] = $data['nct'];
									
								} else {
									if(!empty($dataFilter[$key][$_start])){
										unset($dataFilter[$key][$_start]['tasks']);
									}
								}
							}
						}
					}
					$_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));
				}
			}
			
		}
		$rDatas = $results = array();
        if(!empty($dataFilter)){
            foreach($dataFilter as $key => $data){
                foreach($data as $_time => $value){
					foreach($value as $type => $val){
						if($start <= $_time && $_time <= $end && $value > 0){
							if(!isset($rDatas[$val['employee_id']][$val['date']])){
								$rDatas[$val['employee_id']][$val['date']] = array();
							}
							$rDatas[$val['employee_id']][$val['date']][] = $val;
						} else {
							//do nothing
						}
					}
                }
            }
			
            if(!empty($rDatas)){
                foreach($rDatas as $employ => $rData){
                    foreach($rData as $time => $values){
                        $results[$employ][$time] = $values;
                    }
                }
            }
        }
		// Data absence of employee has no task
		$absence_employee_no_task = array();
		if(!empty($absenceRequest)){
			foreach($absenceRequest as $_eid => $_absence){
				if(!empty($_absence) && !in_array($_eid, $employee_absence_has_task)){
					foreach($_absence as $ab_date => $data){
						$data['date'] =  $ab_date;
						$data['employee_id'] =  $_eid;
						$absence_employee_no_task[$_eid][$ab_date][] = $data;
					}
				}
			}
		}
		if(!empty($absence_employee_no_task)){
			$results = $results + $absence_employee_no_task;
		}
		$this->set(compact('projectStatus'));
		return $results;
	}
	/**
     * Get Start date, end date from URL
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
        if (!array_filter($params)) {
            $params['week'] = date('W');
            $params['year'] = date('Y');
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            $this->Cookie->write('currentWeek', $params['week']);
            if (($week == 1 && $params['week'] <= 52) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
            $currentWeek = $this->Cookie->read('currentWeek');
            // kiem tra de giu lai tuan neu thay doi team.
            // lay tuan cuoi va tuan dau cua 1 thang. neu $currentWeek o giua thi giu nguyen tuan do.
            $newFirstWeekOfMonth = intval(date('W', mktime(0, 0, 0, $this->params['url']['month'], 1, $this->params['url']['year'])));
            $newLastWeekOfMonth = intval(date('W', mktime(0, 0, 0, $this->params['url']['month'], date('d', strtotime('last day of last month', strtotime('01-' .$this->params['url']['month'].'-'.$this->params['url']['year'] ))), $this->params['url']['year'])));
            if(!empty($currentWeek) && ($currentWeek <= $newLastWeekOfMonth) && ($currentWeek >= $newFirstWeekOfMonth)){
                $date = new DateTime();
                $date->setISODate($params['year'], $currentWeek);
                $date = strtotime($date->format('Y-m-d'));
            }
        }
        if (empty($date)) {
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);

        $this->set(compact('_start', '_end'));
        $this->set('_year', $params['year']);
        $this->set('_week', $params['week']);
		
        return array($_start, $_end);
    }
	
	/**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfitFollowDates($profitId,$getDataByPath = true, $start = null, $end = null) {
		$this->loadModel('ActivityForecast');
        if (!($user = $this->_getEmpoyee())) {
            return false;
        }
        $Model = ClassRegistry::init('ProfitCenter');
        //modify by QN, enable control_resource for PM
        $canManageResource = $this->employee_info['CompanyEmployeeReference']['role_id'] == 3 && $this->employee_info['CompanyEmployeeReference']['control_resource'];
        $myPC = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'conditions' => array(
                'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id IS NULL'),
                'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => 0),
                'employee_id' => $user['id']
            )
        ));
        $conds = array('manager_id' => $user['id']);
        if( $canManageResource )$conds['id'] = $myPC['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
        $profit = $Model->find('list', array(
            'recursive' => -1,
            'fields' => array('id'),
            'order' => array('lft' => 'ASC'),
            'conditions' => array(
                'ProfitCenter.company_id' => $user['company_id'],
                'OR' => $conds
            )
        ));
        $this->loadModels('ProfitCenterManagerBackup');
        $backups = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $user['id']),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $profit = array_unique(array_merge($profit, $backups));
        $can_see_forecast = !empty($this->employee_info['Employee']['can_see_forecast']) ? $this->employee_info['Employee']['can_see_forecast'] : 0;
        $see_all_forecast = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr' || $can_see_forecast;
        if ($see_all_forecast) {
            $paths = $Model->generatetreelist(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } elseif (!empty($profit)) {
            $paths = array();
            foreach($profit as $_val)
            {
                $paths[] = $_val;
                $pathsTemp = $Model->children($_val);
                $pathsTemp = Set::classicExtract($pathsTemp,'{n}.ProfitCenter.id');
                $paths = array_merge($paths,$pathsTemp);
            }
			
            $paths = $Model->generatetreelist(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } else {
            return false;
        }
        if (empty($profitId)) {
            $profitId = !empty($profit['ProfitCenter']['id']) ? $profit['ProfitCenter']['id'] : 0;
        } elseif (!isset($paths[$profitId]) && $profitId != -1) {
            return false;
        }
        $profit = $Model->find('first', array('recursive' => -1, 'conditions' => array('id' => $profitId)));
        $profit = !empty($profit) ? array_shift($profit) : null;

        //APPLY FOR CASE GET DATA BY PATH
        $_pc = $profit['id'];
        if($getDataByPath)
        {
            $listManagers = ClassRegistry::init('ProfitCenter')->children($_pc);
            $listManagers = Set::combine($listManagers,'{n}.ProfitCenter.id','{n}.ProfitCenter.manager_id');
            $pathOfPC = array_merge(array($_pc),array_keys($listManagers));
            //$pathOfPC = array_keys($listManagers);
        }
        else
        {
            $pathOfPC = $_pc;
            $listManagers = $Model->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'manager_id'),
                'conditions' => array(
                    'NOT' => array('manager_id' => null),
                    'parent_id' => $pathOfPC,
                    'company_id' => $this->employee_info['Company']['id'])
            ));
        }
        $list = array_merge(array($profit['manager_id']), $listManagers);
        $list = array_unique(array_filter($list));
		$employeeOfPC = $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
                    'conditions' => array('profit_center_id' => $pathOfPC)));
		// debug($employeeOfPC);
		
        // $employees = array_merge($list, $employeeOfPC);
		/* Email : Z0G: Very important email
		Display only the members of the team not the manager if the manager is not member of the team
		*/
        $employees = $employeeOfPC;
        $end = strtotime('-2 days', $end);
		$employees = Set::combine($this->ActivityForecast->Employee->find('all', array(
							'order' => 'first_name DESC',
							'fields' => array('id', 'first_name', 'last_name'),
							'recursive' => -1,
                            'conditions' => array(
                                'id' => $employees,
                                'NOT' => array(
                                    'OR' => array(
                        				array(
                        					'Employee.end_date <>' => '0000-00-00',
                        					'Employee.end_date IS NOT NULL',
                        					'Employee.end_date < '=> date('Y-m-d', $start)
                        				),
                        				array(
                        					'Employee.start_date <>' => '0000-00-00',
                        					'Employee.start_date IS NOT NULL',
                        					'Employee.start_date > '=> date('Y-m-d', $end)
                        				)
                        			)
                                )
                            )
                    ))
                    , '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        //END
        $_managers = array();
        foreach ($list as $key) {
            if (isset($employees[$key])) {
                $_managers[$key] = '<p class="inlineblock">'. $employees[$key] . ' <span> (Manager)</span></p>';
            }
        }
        $employees = $_managers + $employees;
     
        if($profitId == -1){
            $employees = array();
            $profit['id'] = -1;
        }
        return array($profit, $paths, $employees, $user, $employeeOfPC);
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
        $this->employee_info['Employee']['role_name'] = !empty($this->employee_info['Role']['name']) ? $this->employee_info['Role']['name'] : 'conslt';
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }
	protected function _getProfitEmployee($getDataByPath = false) {
        if( isset($this->params['url']['get_path']) )$getDataByPath = $this->params['url']['get_path'];
        $isManage = !empty($this->params['url']['id']) && !empty($this->params['url']['profit']);
        $check = !($employeeName = $this->_getEmpoyee()) ||
                ($isManage && (!($params = $this->_getProfits($this->params['url']['profit'], true)) || !isset($params[2][$this->params['url']['id']])));
        if ($isManage) {
            $employee = $this->ActivityForecast->Employee->find('first', array(
                'recursive' => -1, 'fields' => array('id', 'first_name', 'last_name', 'external'), 'conditions' => array(
                    'id' => $this->params['url']['id']
                    )));
            $employeeName = array_merge($employeeName, $employee['Employee']);
        }

        return array($isManage, $employeeName);
    }
	/**
     * Lay employee cua PC
     */
    public function getEmployeeOfProfitCenterUsingModuleExportActivity(){
        $this->layout = false;
        $result = '';
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        if(!empty($_POST) && !empty($_POST['profit_center_id'])){
            $pcIds = $_POST['profit_center_id'];
            $result = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('profit_center_id' => $pcIds),
                'fields' => array('employee_id', 'employee_id')
            ));
            $result = !empty($result) ? array_unique(array_values($result)) : array();
        }
        echo json_encode($result);
        exit;
    }
	
	 /**
     *
     * @var     :
     * @return  : int date working date
     * @author : HUUPC
     * */
    private function _getWorkingDays($startDate, $endDate, $_holidayDays){
		$_wokingDays = array();
        $_startDate=strtotime($startDate);
        $_endDate=strtotime($endDate);
        if(strtotime($startDate) != '' && strtotime($endDate) != ''){
            if ($startDate < $endDate) {
                $startDate = strtotime($startDate);
                $endDate = strtotime($endDate);
                while ($startDate <= $endDate){
					if(!in_array(date('m-d-Y',$startDate), $_holidayDays)){
						$_wokingDays[] = $startDate;
					}
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                }
            }
            
        }
        return $_wokingDays;
    }
	 /**
     *
     * @var     :
     * @return  : array date holiday
     * @author : HUUPC
     * */
    private function _getHoliday($startDate, $endDate){
        $employeeName = $this->_getEmpoyee();
        $_holiday = array();
        if ($startDate < $endDate) {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);
			$workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);           
			while ($startDate <= $endDate){
				$_start = strtolower(date("l", $startDate));
				$_end = strtolower(date("l", $endDate));
				if($workdays[$_start] == 0){
					$_holiday[] = date("m-d-Y", $startDate);
				}
				$startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
            }
        }
        return $_holiday;
    }
	/**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParamsMonth($byWeek = false, $firstMonth = false) {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
		
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
		
        if (!array_filter($params)) {
            $params['week'] = date('W');
            $params['year'] = date('Y');
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
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        if($firstMonth == true){
            $date = strtotime('01-'.date('m-Y', $date));
        }

        if($byWeek == false){
            $_start = strtotime(date('m',$date).'/1/'.date('Y',$date));
            $_end = strtotime(date('m',$_start)."/".cal_days_in_month(CAL_GREGORIAN, date('m',$_start), date('Y',$_start))."/".date('Y',$_start));
        } else {
            $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
            /**
             * HuuPc add, calculate last day of month
             */
            $_month = date('m', $date) + 1;
            $_year = date('Y', $date);
            if($_month > 12){
                $_month = 1;
                $_year++;
            }
            $_date = mktime(0, 0, 0, $_month, 1, $_year);
            $mondayOfNextMonth = (date('w', $_date) == 1) ? $_date : strtotime('next monday', $_date);
            $_end = mktime(0, 0, 0, date('m', $mondayOfNextMonth), date('d', $mondayOfNextMonth) - 1, date('Y', $mondayOfNextMonth));
            //$_end = strtotime('next sunday', $_start);
        }

    
        //pr(date('m-d W', $_start) . ' ' . date('m-d W', $_end));
        return array($_start, $_end);
    }
	private function _workingDayFollowConfigAdmin($start = null, $end = null, $workdayAdmins = null){
        $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $day = strtolower(date('l', $start));
                if(!empty($workdayAdmins[$day]) && $workdayAdmins[$day] != 0){
                    $results[$start] = $start;
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return $results;
    }
	 /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfitMyDiarys($profitId) {
		$this->loadModels('ActivityForecast');
        if (!($user = $this->_getEmpoyee())) {
            return false;
        }
        $Model = ClassRegistry::init('ProfitCenter');
        $profit = $Model->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $user['company_id'], 'ProfitCenter.id' => $profitId)));
        if(!empty($profitId)) {
            $profitId = $profit['ProfitCenter']['id'];
        } else {
            return false;
        }
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
        $employees = !empty($employees[$user['id']]) ? array($user['id'] => $employees[$user['id']]) : array();
        $paths = $Model->generatetreelist(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        $paths = !empty($paths[$profitId]) ? array($profitId => $paths[$profitId]) : array();
        return array($profit, $paths, $employees, $user);
    }
	
    /**
     * Index
     *
     * @return void
     * @access protected
     */
    protected function _parse($_start = null, $_end = null, $employeeName, $employees, $profit, $listActivities = null,  $listTasks = null) {
        $this->loadModel('ActivityRequest');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ProjectPhaseStatus');
        $this->loadModel('Activity');
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectStatus');
        $this->loadModel('Family');
        $this->loadModel('EmployeeMultiResource');
        // Comment phan phase status de update len pro site ng?y 7/1/2014...chi pro site moi comment
        $status = $this->ProjectPhaseStatus->find('list',array(
            'conditions' => array(
                'display' => 0,
                'company_id' => $employeeName['company_id']
            ),
            'recursive' => -1,
            'fields' => array('id','id')
        ));
        $project_status = $this->ProjectStatus->find('list',array(
            'conditions' => array(
                'display' => 0,
                'company_id' => $employeeName['company_id']
            ),
            'fields' => array('id','id')
        ));
        $ProjectTaskNoDisplay = array();
        //validate project status
        $ProjectTaskHasDisplayYes = array();
        if(!empty($project_status)){
            $_ProjectTaskLink = $this->ActivityTask->find('list',array(
                'conditions' => array(
                    'NOT' => array(
                        'project_task_id' => null,
                    )
                ),
                'fields' => array('project_task_id','project_task_id')
            ));
            $ProjectTaskHasDisplayYes = $this->ProjectTask->find('list',array(
                'conditions' => array(
                    'id' => $_ProjectTaskLink,
                    'task_status_id' => $project_status
                ),
                'fields' => array('id','id')
            ));

        }
        if(!empty($status)){
            $project = $this->Activity->find('list',array(
                'conditions' => array(
                    'NOT' => array(
                        'project' => null
                    )
                ),
                'fields' => array('project','project')
            ));
            $ProjectPhasePlan  = $this->ProjectPhasePlan->find('list',array(
                'conditions' => array(
                    'project_phase_status_id' => $status,
                    'project_id' => $project
                ),
                'recursive' => -1,
                'fields' => array('id','id')
            ));
            $ProjectTaskNoDisplay = $this->ProjectTask->find('list',array(
                'conditions' => array(
                    'project_planed_phase_id' => $ProjectPhasePlan
                ),
                'recursive' => -1,
                'fields' => array('id','id')
            ));
        }
        if(!empty($ProjectTaskHasDisplayYes)){
            foreach($ProjectTaskHasDisplayYes as $k => $_ProjectTaskHasDisplayYes){
                if(!empty($ProjectTaskNoDisplay[$k])){

                }else{
                    $ProjectTaskNoDisplay[$k] = $_ProjectTaskHasDisplayYes;
                }

            }
        }
        /**
         * Lay cac ngay nghi cua employee trong tuan
         */
        $requestQuery = $this->AbsenceRequest->find("all", array(
            'recursive'     => -1,
            "conditions"    => array('date BETWEEN ? AND ?' => array($_start, $_end),'employee_id' => array_keys($employees))));
        $requests = Set::combine($requestQuery, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
        /**
         * Lay cac cong viec ma admin giao cho tung employee trong tuan
         */
        $forcastsQuery = $this->ActivityForecast->find("all", array(
            'recursive'     => -1,
            "conditions"    => array('date BETWEEN ? AND ?'=>array($_start, $_end), 'employee_id'=> array_keys($employees))));
        $forecasts = Set::combine($forcastsQuery, '{n}.ActivityForecast.date', '{n}.ActivityForecast', '{n}.ActivityForecast.employee_id');
        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays   = ClassRegistry::init('Holiday')->getOptions($_start, $_end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            if(strtolower(date("l", $time)) == 'saturday' || strtolower(date("l", $time)) == 'sunday'){
                unset($holidays[$time]);
            }
        }
        /**
         * Lay cac loai vang nghi cua cong ty
         */
        $absences   = $this->AbsenceRequest->getAbsences($employeeName['company_id']);
        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        /**
         * Lay cac family va sub_family cua mot cong ty
         */
        $families = $this->Family->find('all', array(
            'order' => array('name ASC'),
            'recursive' => -1,
            'fields'    => array('id', 'name', 'parent_id'),
            'conditions' => array('company_id' => $employeeName['company_id'])));
        /**
         * Lay tat ca cac activity co trang thai activated = YES. Va nhu~ng activity = NO nhung da co request o timesheet
         */
        $_activities = $this->Activity->find('all', array(
            'order' => array('name ASC'),
            'recursive'     => -1,
            'conditions'    => array(
                'company_id' => $employeeName['company_id'],
                'OR' => array(
                    'AND' => array(
                        'Activity.id' => $listActivities,
                        'activated' => 0
                    ),
                    'activated' => 1,
                )
            ),
            'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id', 'activated', 'pms','allow_profit','project')));
        /**
         * Nhom family, subfamily, activities lai voi nhau
         */
        $groupFamilies = $groupSubFamilies = $activities = array();
        if(!empty($_activities)){
            // o day co the dung Set::combine cua cakephp nhung dung ham nay se rat nang. nen su dung vong lap o day se nhe hon
            foreach($_activities as $activity){
                $dx = $activity['Activity'];
                if(!isset($groupFamilies[$dx['family_id']])){
                    $groupFamilies[$dx['family_id']] = array();
                }
                $groupFamilies[$dx['family_id']][] = $dx['id'];
                if(!isset($groupSubFamilies[$dx['subfamily_id']])){
                    $groupSubFamilies[$dx['subfamily_id']] = array();
                }
                $groupSubFamilies[$dx['subfamily_id']][] = $dx['id'];
                if(!isset($activities[$dx['id']])){
                    $activities[$dx['id']] = array();
                }
                $activities[$dx['id']] = $dx;
            }
        }
        /**
         * Dieu kien de loc ra cac task thuoc activity
         */
        $conditions = !empty($ProjectTaskNoDisplay) ? array('NOT' => array('ActivityTask.project_task_id' => $ProjectTaskNoDisplay)) : array();
        $_conditions = array('ActivityTask.activity_id' => array_keys($activities));
        $conditions = array_merge($conditions, $_conditions);
        /**
         * Lay tat ca cac task theo dieu kien tren
         */
        $_activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id'),
            'conditions' => $conditions));
        /**
         * Dinh dang lai mang cua activity task
         */
        $activityTasks = array();
        if(!empty($_activityTasks)){
            // o day co the dung Set::combine cua cakephp nhung dung ham nay se rat nang. nen su dung vong lap o day se nhe hon
            foreach($_activityTasks as $_activityTask){
                $dx = $_activityTask['ActivityTask'];
                if(!isset($activityTasks[$dx['activity_id']][$dx['id']])){
                    $activityTasks[$dx['activity_id']][$dx['id']] = array();
                }
                $activityTasks[$dx['activity_id']][$dx['id']] = $dx;
            }
        }
        /**
         * Tao ra 1 mang moi, 1 mang du lieu de dua ra view.
         */
        $mapeds = array('activity' => array());
        if(!empty($activities)){
            foreach($activities as $id => $activity){
                if(!isset($mapeds['activity'][$id])){
                    $mapeds['activity'][$id] = array();
                }
                $mapeds['activity'][$id] = array(
                    'name'          => $activity['name'],
                    'long_name'     => $activity['name'],
                    'short_name'    => $activity['short_name'],
                    'long_name'     => $activity['long_name'],
                    'family_id'     => $activity['family_id'],
                    'subfamily_id'  => $activity['subfamily_id'],
                    'activated'     => $activity['activated'],
                    'pms'           => $activity['pms'],
                    'is_project'    => $activity['project']
                );
                $mapeds['activity'][$id]['not_empty_task'] = 0;
                $mapeds['activity'][$id]['tasks'] = array();
                if(!empty($activityTasks[$id])){
                    $mapeds['activity'][$id]['not_empty_task'] = 1;
                    $mapeds['activity'][$id]['tasks'] = $activityTasks[$id];
                    foreach($activityTasks[$id] as $id => $value){
                        $mapeds['task'][$id] = $value;
                    }
                }
            }
        }
        /**
         * Nhung employee nao thuoc trong AccessibleProfit moi duoc request.
         */
        $allowActivity = $this->ActivityRequest->Activity->AccessibleProfit->find('list', array(
            'recursive' => -1,
            'fields' => array('activity_id', 'activity_id'),
            'conditions' => array('profit_center_id' => $profit, 'activity_id' => array_keys($activities))));
        $accessibleActivities = $this->ActivityRequest->Activity->AccessibleProfit->find('list', array(
            'recursive' => -1,
            'fields' => array('activity_id', 'activity_id'),
            'conditions' => array('activity_id' => array_keys($activities))
        ));
        /**
         * Kiem tra nhung activity nao thuoc family, sub family moi cho request
         */
        foreach ($families as $family) {
            $family = array_shift($family);
            $model = empty($family['parent_id']) ? 'family' : 'subfamily';
            if($model == 'subfamily') {
                $mapeds['family'][$family['parent_id']]['sub'][] = $family['id'];
                if(!empty($groupSubFamilies[$family['id']])){
                    foreach($groupSubFamilies[$family['id']] as $value){
                        if(in_array($value, $allowActivity) || empty($accessibleActivities[$value])){
                            $mapeds['subfamily'][$family['id']]['act'][] = $value;
                        }
                    }
                }
            } else {
                if(!empty($groupFamilies[$family['id']])){
                    foreach($groupFamilies[$family['id']] as $value){
                        if(in_array($value, $allowActivity) || empty($accessibleActivities[$value])){
                            $mapeds['family'][$family['id']]['act'][] = $value;
                        }
                    }
                }
            }
            $mapeds[$model][$family['id']]['name'] = $family['name'];

        }
        if (!empty($mapeds['family'])) {
            $keys = array_combine(array_keys($mapeds['family']), Set::extract($mapeds['family'], '{n}.name'));
            asort($keys);
            $mapeds['family'] = array_replace($keys, $mapeds['family']);
        }
        if(empty($mapeds['subfamily'])){
            $mapeds['subfamily'] = array();
        }
        $this->loadModel('ActivityComment');
        $_comments = $this->ActivityComment->find("all", array(
            'recursive' => -1,
            "conditions" => array('date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $employeeName['id'])));
        $comments = $userComments = array();
        foreach ($_comments as $comment) {
            $comment = array_shift($comment);
            $comments[$comment['employee_id']][$comment['date']][$comment['time'] ? 'pm' : 'am'][$comment['id']] = array(
                'user_id'   => $comment['user_id'],
                'created'   => date('d-m-Y H:i', $comment['created']),
                'text'      => $comment['text']);
            if (!isset($employees[$comment['user_id']])) {
                $userComments[$comment['user_id']] = $comment['user_id'];
            }
        }
        $employeeQuery = $this->ActivityForecast->Employee->find('all', array(
            'order' => 'id DESC',
            'fields' => array('id', 'first_name', 'last_name'),
            'recursive' => -1,
            'conditions'=> array('id' => $userComments)));
        $userComments = Set::combine($employeeQuery, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
       
        $a = $this->EmployeeMultiResource->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'date BETWEEN ? AND ?' => array($_start, $_end),
                'employee_id' => array_keys($employees),
                'NOT' => array('date' => array_keys($holidays))
            ),
            'fields' => array('employee_id', 'date', 'value'),
        ));
        $cpOfMulti = !empty($a) ? Set::combine($a, '{n}.EmployeeMultiResource.date', '{n}.EmployeeMultiResource.value', '{n}.EmployeeMultiResource.employee_id') : array();
        $capacityOfMultiResources = array();
		foreach($cpOfMulti as $id => $v){
            $sum = array_sum($v);
            $capacityOfMultiResources[$id] = $sum;
        }
        $externalEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('id' => array_keys($employees)),
            'fields' => array('id', 'external')
        ));
        //$capacityOfMultiResources = !empty($capacityOfMultiResources) ? Set::combine($capacityOfMultiResources, '{n}.EmployeeMultiResource.employee_id', '{n}.0.value') : array();
        $employees['tNotAffec'] = __('Not Affected', true);
        $this->set(compact('requests', 'absences', 'constraint', 'forecasts', 'workdays', 'employees', 'userComments', 'employeeName', 'holidays', 'mapeds', 'comments', 'capacityOfMultiResources', 'cpOfMulti', 'externalEmployee'));
    }
	/*
	* Function exportActivityFollowEmployee
	* Write csv file by employee
	* need enhancement
		* clear export folder for first time export 
		* Save shared data to section 
		* clear after finish 
	*/
	public function exportActivityFollowEmployee(){
		set_time_limit(0);
		$company_id = $this->employee_info['Company']['id'];
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $employeeLoginId;
        $path = SHARED . $employeeLoginId;
		$results = array();
		App::import('Core', 'Folder');
        new Folder($path, true, 0777);
		if(!empty($_POST) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['employee_id'])){
            $this->loadModels(
				'Employee',
				'AbsenceRequest',
				'Absence',
				'ProfitCenter',
				'ActivityRequest',
				'ActivityTask',
				'ProjectPhasePlan',
				'ProjectTask',
				'Activity',
				'ActivityRequestConfirm',
				'ActivityExport',
				'Family',
				'ProjectEmployeeProfitFunctionRefer',
				'Project',
				'ProjectPhase',
				'ProjectAmrProgram',
				'ActivityForecastComment',
				'Holiday'
			);
			$startDate = strtotime($_POST['start_date']);
            $endDate = strtotime($_POST['end_date']);
            $dayOff = !empty($_POST['day_of']) ? $_POST['day_of'] : 0;
            $display = !empty($_POST['display']) ? $_POST['display'] : 0;
            $mergeFile = !empty($_POST['merge']) ? $_POST['merge'] : 0;
			$listEmployees = !empty($_POST['employee_id']) ? $_POST['employee_id'] : array();
			$totalRecord = 0;
			$export_file = !empty($_POST['filename']) ? $_POST['filename'] : 'activity_export_'.date('H_i_s_d_m_Y') . '.csv';
			$filePath = $path . DS . $export_file;
			/**
			 * List field by setting 
			 */
			$_activityExports = $this->ActivityExport->find('all', array(
				'recursive' => -1,
				'conditions' => array('company_id' => $company_id, 'display' => 1),
				'fields' => array('name', 'english', 'setting'),
				'order' => array('weight' => 'ASC')
			));
			$activityExports = $settingOfExports = array();
			$fieldset = array();
			if(!empty($_activityExports)){
				foreach($_activityExports as $_activityExport){
					$dx = $_activityExport['ActivityExport'];
					$name = !empty($dx['name']) ? $dx['name'] : '';
					$val = !empty($dx['english']) ? $dx['english'] : '';
					$activityExports[$name] = $val;
					$fieldset[strtolower(str_replace(array(' ', '/'), '_', trim($name)))] = $val;
					$settingOfExports[strtolower(str_replace(array(' ', '/'), '_', trim($name)))] = $dx['setting'];
				}
			}
			/* END List field by setting  */
			
			/**
			 * Lay employee theo danh sach input
			 */
			$this->Employee->recursive = -1;
			$this->Employee->Behaviors->attach('Containable');
			$employees = $this->Employee->find('all', array(
				//'recursive' => -1,
				'conditions' => array('Employee.id' => $listEmployees),
				'contain' => array(
					'ProjectEmployeeProfitFunctionRefer' => array('id', 'profit_center_id')
				),
				'fields' => array('id', 'first_name', 'last_name', 'code_id', 'identifiant', 'id3', 'id4', 'id5', 'id6', 'tjm'),
				'order' => array('first_name' => 'ASC'),
				'joins' => array(
					array(
						'table' => 'company_employee_references',
						'alias' => 'Ref',
						'conditions' => array(
							'Ref.company_id' => $company_id,
							'Ref.employee_id = Employee.id'
						)
					)
				)
			));
			/**
			 * Lay tat ca profit center cua 1 cong ty
			 */
			$profitCenters = $this->ProfitCenter->find('all', array(
				'recursive' => -1,
				'conditions' => array('company_id' => $company_id),
				'fields' => array('id', 'name', 'analytical')
			));
			$idOfTeam = !empty($profitCenters) ? Set::combine($profitCenters, '{n}.ProfitCenter.id', '{n}.ProfitCenter.analytical') : array();
			$profitCenters = !empty($profitCenters) ? Set::combine($profitCenters, '{n}.ProfitCenter.id', '{n}.ProfitCenter.name') : array();
			/**
			 * Lay tat ca program cua 1 cong ty
			 */
			$projectAmrProgram = $this->ProjectAmrProgram->find('list', array(
				'recursive' => -1,
				'conditions' => array('company_id' => $company_id),
				'fields' => array('id', 'amr_program')
			));
			/**
			 * Lay holiday
			 */
			$getHolidays = $this->Holiday->getOptions($startDate, $endDate, $company_id);
			// debug( $getHolidays); exit;
			$holidays = array();
			if(!empty($getHolidays)){
				foreach($getHolidays as $time => $getHoliday){
					$hlAm = isset($getHoliday['am']) ? 0.5 : 0;
					$hlPm = isset($getHoliday['pm']) ? 0.5 : 0;
					$holidays[$time] = $hlAm + $hlPm;
				}
			}
			/**
			 * Lay Comment of day
			 */
			 
			$commentRequest = $this->ActivityForecastComment->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'date BETWEEN ? AND ?' => array($startDate, $endDate),
					'employee_id' => $listEmployees,
					'company_id' => $company_id,
					'is_timesheet_msg' => 0,
				),
				'fields' => array('id', 'date', 'employee_id', 'comment', 'created')
			));
			$comments_request = array();
			if(!empty($commentRequest)){
				foreach($commentRequest as $key => $value){
					if(empty($comments_request[$value['ActivityForecastComment']['date']])) $comments_request[$value['ActivityForecastComment']['date']] = array();
					$comments_request[$value['ActivityForecastComment']['date']][] = $value['ActivityForecastComment']['comment'];
				}
			}
			$commentRequest = Set::combine($commentRequest, '{n}.ActivityForecastComment.date', '{n}.ActivityForecastComment');
			
			/**
			 * Lay Comment of week
			*/
			$startWeekDate = strtotime('last monday', $startDate);
			$endWeekDate = strtotime('next sunday', $endDate);
			$weekCommentRequests = $this->ActivityForecastComment->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'date BETWEEN ? AND ?' => array($startWeekDate, $endWeekDate),
					'employee_id' => $listEmployees,
					'company_id' => $company_id,
					'is_timesheet_msg' => 1,
				),
				'fields' => array('id', 'date', 'employee_id', 'comment', 'created')
			));
			$weekComments = array();
			if( !empty($weekCommentRequests)){
				$startWeek = $startWeekDate;
				$endWeek = strtotime('next sunday', $startWeek);
				while($endWeek <= $endWeekDate){
					foreach($weekCommentRequests as $weekCommentRequest){
						$date = $weekCommentRequest['ActivityForecastComment']['date'];
						if( ($startWeek <= $date) && ($endWeek >= $date)){
							$weekComments[] = array(
								'start_day' => $startWeek,
								'end_day' => $endWeek,
								'comment' => $weekCommentRequest['ActivityForecastComment']['comment'],
							);
						}
					}
					$startWeek = strtotime('next monday', $startWeek);
					$endWeek = strtotime('next sunday', $startWeek);
				}
			}
			/**
			 * Lay danh sach absence cua employee trong khoang thoi gian input
			 */
			$absencesRequests = $this->AbsenceRequest->find("all", array(
				'recursive' => -1,
				'fields' => array('id', 'date', 'absence_pm', 'absence_am', 'response_am', 'response_pm', 'employee_id', 'updated'),
				"conditions" => array('date BETWEEN ? AND ?' => array($startDate, $endDate), 'employee_id' => $listEmployees)));
			$absencesForEmployees = $listIdAbsences = $listAbsenceCodes = $absencesDates = array();
			if(!empty($absencesRequests)){
				foreach($absencesRequests as $absencesRequest){
					$absencesRequest = array_shift($absencesRequest);
					foreach (array('am', 'pm') as $type) {
						if ($absencesRequest['absence_' . $type] && $absencesRequest['absence_' . $type] != '-1' && $absencesRequest['response_' . $type] == 'validated') {
							$absencesDates[$absencesRequest['employee_id']][$absencesRequest['date']] = $absencesRequest['updated'];
						}
						$checkDayOff = $dayOff ? true : $absencesRequest['response_' . $type] == 'validated';
						if ($absencesRequest['absence_' . $type] && $absencesRequest['absence_' . $type] != '-1' && $checkDayOff){
							if (!isset($absencesForEmployees[$absencesRequest['employee_id']][$absencesRequest['date']][$absencesRequest['absence_' . $type]])) {
								$absencesForEmployees[$absencesRequest['employee_id']][$absencesRequest['date']][$absencesRequest['absence_' . $type]] = 0;
							}
							$absencesForEmployees[$absencesRequest['employee_id']][$absencesRequest['date']][$absencesRequest['absence_' . $type]] += 0.5;
							$listAbsenceCodes[$absencesRequest['employee_id']][$absencesRequest['date']] = $absencesRequest['absence_' . $type];
							$listIdAbsences[$absencesRequest['absence_' . $type]] = $absencesRequest['absence_' . $type];
						}
					}
				}
			}
			/**
			 * Lay danh sach ten cac danh sach absence
			 */
			$absences = $this->Absence->find('all', array(
				'recursive' => -1,
				'conditions' => array('Absence.id' => $listIdAbsences),
				'fields' => array('id', 'name', 'code1', 'code2', 'code3')
			));
			$absences = !empty($absences) ? Set::combine($absences, '{n}.Absence.id', '{n}.Absence') : array();
			/**
			 * Lay activity request
			 */
			$doffConditions = ($dayOff == 1) ? array() : array('NOT' => array('value' => 0));
			$activityRequests = $this->ActivityRequest->find("all", array(
				'recursive' => -1,
				"conditions" => array(
					'date BETWEEN ? AND ?' => array($startDate, $endDate),
					'employee_id' => $listEmployees,
					'status' => $dayOff ? array(-1, 0, 1, 2) : 2,
					$doffConditions
				)
			));
			$activityIdOnes = !empty($activityRequests) ? Set::classicExtract($activityRequests, '{n}.ActivityRequest.activity_id') : array();
			$listTaskIds = !empty($activityRequests) ? Set::classicExtract($activityRequests, '{n}.ActivityRequest.task_id') : array();
			$activityRequests = !empty($activityRequests) ? Set::combine($activityRequests, '{n}.ActivityRequest.id', '{n}.ActivityRequest', '{n}.ActivityRequest.employee_id') : array();
			/**
			 * Lay request confirm
			 */
			$_startTMP = strtotime('last monday', $startDate);
			$_endTMP = strtotime('next sunday', $endDate);
			$requestConfirms = $this->ActivityRequestConfirm->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						'start BETWEEN ? AND ?' => array($_startTMP, $_endTMP),
						'end BETWEEN ? AND ?' => array($_startTMP, $_endTMP)
					),
					'employee_id' => $listEmployees,
					'status' => 2
				)
			));
			$listRequestConfirms = array();
			if(!empty($requestConfirms)){
				foreach($requestConfirms as $requestConfirm){
					$dx = $requestConfirm['ActivityRequestConfirm'];
					if(!empty($dx['start']) && !empty($dx['end']) && $dx['start'] <= $dx['end']){
						$_start = $dx['start'];
						$_end = $dx['end'];
						 while ($_start <= $_end){
							$listRequestConfirms[$dx['employee_id']][$_start] = $dx['updated'];
							$_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));
						}
					}
				}
			}
			/**
			 * Lay activity theo task
			 */
			$activityTasks = $this->ActivityTask->find('all', array(
				'recursive' => -1,
				'conditions' => array('ActivityTask.id' => $listTaskIds),
				'fields' => array('id', 'project_task_id', 'activity_id')
			));
			$activityIdTwos = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.activity_id') : array();
			$projectTaskIds = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.project_task_id') : array();
			
			$ATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.project_task_id') : array();
			$ATaskOfActivity = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
			/**
			 * Lay cac phase cua project task id
			 */
			$projectTasks = $this->ProjectTask->find('all', array(
				'recursive' => -1,
				'conditions' => array('ProjectTask.id' => $projectTaskIds),
				'fields' => array('id', 'project_planed_phase_id', 'task_title', 'project_id')
			));
			$projectTasksIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.project_planed_phase_id') : array();
			$projectIds =  !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.project_id') : array();
			$projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
			
			$projects = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $projectIds,
				),
				'fields' => array('id','project_name', 'long_project_name', 'project_amr_program_id','project_code_1', 'project_code_2'),
			));
			$projects = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project') : array();
			// exit;
			/**
			 * Lay Phase cua cac task
			 */
			$phasePlans = $this->ProjectPhasePlan->find('all', array(
				'recursive' => -1,
				'conditions' => array('ProjectPhasePlan.id' => $projectTasksIds),
				'fields' => array('id', 'project_planed_phase_id', 'ref1', 'ref2', 'ref3', 'ref4')
			));
			$phasePlansID = !empty($phasePlans) ? Set::classicExtract($phasePlans, '{n}.ProjectPhasePlan.project_planed_phase_id') : array();
			$planIDToPhase = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id','{n}.ProjectPhasePlan.project_planed_phase_id') : array();
			$phasePlans = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan') : array();
			$phase_ids = $this->ProjectPhase->find('all', array(
				'recursive' => -1,
				'conditions' => array('ProjectPhase.id' => $phasePlansID),
				'fields' => array('id', 'name')
			));
			$phaseName = !empty($phase_ids) ? Set::combine($phase_ids, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name') : array();
			// debug($phaseName); exit;
			/**
			 * Lay activity cua cac task/activity request
			 */
			$activityIds = array_unique(array_merge($activityIdOnes, $activityIdTwos));
			$activities = $this->Activity->find('all', array(
				'recursive' => -1,
				'conditions' => array('Activity.id' => $activityIds),
				'fields' => array('id', 'code1', 'code2', 'code3', 'code4', 'code5', 'code6', 'family_id', 'subfamily_id', 'project')
			));
			$activities = !empty($activities) ? Set::combine($activities, '{n}.Activity.id', '{n}.Activity') : array();
			/**
			 * Lay danh sach family cua cong ty
			 */
			$families = $this->Family->find('list', array(
				'order' => array('name' => 'ASC'),
				'recursive' => -1,
				'fields'    => array('id', 'name'),
				'conditions' => array('company_id' => $company_id)
			));
			/**
			 * Build data and inset to table tmp_module_activity_exports
			 */
			try{
				$file = fopen($filePath, 'a'); // ghi tiep
			}catch(Exception $e){
				die( 'Caught exception: '.  $e->getMessage(). "\n");
			}
			$save = array();
			$no = 0;
			foreach ($employees as $employee) {
				$employID = $employee['Employee']['id'];
				$pcId = !empty($employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : '';
				/**
				 * Writing holiday
				 */
				if(!empty($holidays) && $dayOff == 1){
					foreach($holidays as $date => $value){
						$no++;
						$comment = (!empty($comments_request) && !empty($comments_request[$date])) ? $comments_request[$date] : '';
						$wcomment = array();
						foreach ($weekComments as $weekComment){
							if( $weekComment['start_day'] <= $date &&  $weekComment['end_day'] >= $date ){
								$wcomment[] = $weekComment['comment'];
							}
						}
						$e_holiday = array(
							'no' => $no,
							'first_name' => !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '',
							'last_name' => !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '',
							'profit_center_id' => !empty($idOfTeam[$pcId]) ? $idOfTeam[$pcId] : '',
							'profit_center' => !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '',
							'id1' => !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '',
							'id2' => !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '',
							'id3' => !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '',
							'id4' => !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '',
							'id5' => !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '',
							'id6' => !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '',
							'family' => '',
							'sub_family' => '',
							'code_1' => '',
							'code_2' => '',
							'code_3' => '',
							'code_4' => '',
							'code_5' => '',
							'code_6' => '',
							'ref_1' => '',
							'ref_2' => '',
							'ref_3' => '',
							'ref_4' => '',
							'quantity' => !empty($value) ? $value : '',
							'date_activity_absence' => !empty($date) ? $this->export_format_date($date, $settingOfExports['date_activity_absence']) : '',
							'validation_date' => '',
							'extraction_date' => $this->export_format_date(time(), $settingOfExports['extraction_date']),
							'company_id' => $company_id,
							'project_name' => 'ABSENCE',
							'message' => !empty($comment) ? json_encode($comment) : '',
							'week_message' => !empty( $wcomment) ? json_encode($wcomment) : '',
						);
						$e_data = array();
						foreach($fieldset as $key => $val){
							$e_data[$key] = isset($e_holiday[$key]) ? $e_holiday[$key] : '';
						}
						// Write to SCV
						fputcsv($file, $e_data);
					}
				}
				/**
				 * END Writing holiday
				 */
				
				/**
				 * Export Absence
				 */
				if(!empty($absencesForEmployees[$employID])&& $dayOff == 1){
					foreach($absencesForEmployees[$employID] as $date => $value){
						$absenceIdOfEmploy = !empty($listAbsenceCodes[$employID][$date]) ? $listAbsenceCodes[$employID][$date] : '';
						$no++;
						$comment = (!empty($comments_request) && !empty($comments_request[$date])) ? $comments_request[$date] : '';
						$wcomment = array();
						foreach ($weekComments as $weekComment){
							if( $weekComment['start_day'] <= $date &&  $weekComment['end_day'] >= $date ){
								$wcomment[] = $weekComment['comment'];
							}
						}
						$e_absences = array(
							'no' => $no,
							'first_name' => !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '',
							'last_name' => !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '',
							'profit_center_id' => !empty($idOfTeam[$pcId]) ? $idOfTeam[$pcId] : '',
							'profit_center' => !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '',
							'id1' => !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '',
							'id2' => !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '',
							'id3' => !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '',
							'id4' => !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '',
							'id5' => !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '',
							'id6' => !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '',
							'family' => '',
							'sub_family' => '',
							'code_1' => !empty($absences[$absenceIdOfEmploy]['code1']) ? $absences[$absenceIdOfEmploy]['code1'] : '',
							'code_2' => !empty($absences[$absenceIdOfEmploy]['code2']) ? $absences[$absenceIdOfEmploy]['code2'] : '',
							'code_3' => !empty($absences[$absenceIdOfEmploy]['code3']) ? $absences[$absenceIdOfEmploy]['code3'] : '',
							'code_4' => '',
							'code_5' => '',
							'code_6' => '',
							'ref_1' => !empty($absences[$absenceIdOfEmploy]['code1']) ? $absences[$absenceIdOfEmploy]['code1'] : '',
							'ref_2' => !empty($absences[$absenceIdOfEmploy]['code2']) ? $absences[$absenceIdOfEmploy]['code2'] : '',
							'ref_3' => '',
							'ref_4' => '',
							'quantity' => !empty($value) ? array_shift($value) : '',
							'date_activity_absence' => !empty($date) ? $this->export_format_date($date, $settingOfExports['date_activity_absence']) : '',
							'validation_date' => !empty($absencesDates[$employee['Employee']['id']][$date]) ? $this->export_format_date($absencesDates[$employee['Employee']['id']][$date], $settingOfExports['validation_date']) : '',
							'extraction_date' => $this->export_format_date(time(), $settingOfExports['extraction_date']),
							'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
							'date_export' => strtotime(date('d-m-Y', time())),
							'company_id' => $company_id,
							'project_name' => 'ABSENCE',
							'message' => !empty($comment) ? json_encode($comment) : '',
							'week_message' => !empty( $wcomment) ? json_encode($wcomment) : '',
						);
						$e_data = array();
						foreach($fieldset as $key => $val){
							$e_data[$key] = isset($e_absences[$key]) ? $e_absences[$key] : '';
						}
						// Write to SCV
						fputcsv($file, $e_data);
						// $save[] = $e_data;
					}
				}
				/**
				 * END Export Absence
				 */
				
				/**
				 * Export Activity
				 */
				
				if(!empty($activityRequests[$employID])){
					/**
					 * GROUP BY DATE WITH VALUE > 0
					 */
					$dateHaveValueLargerZero = array();
					foreach($activityRequests[$employID] as $key => $value){
						$val = !empty($value['value']) ? $value['value'] : 0;
						if($val > 0){
							$dateHaveValueLargerZero[$value['task_id']][$value['date']] = $val;
						}
					}
					/**
					 * Build du lieu
					 */
					$taskHaveWriteZero = array();
					foreach($activityRequests[$employID] as $key => $value){
						/**
						 * Lay phase id cua task
						 */
						$comment = (!empty($comments_request) && !empty($comments_request[$value['date']])) ? $comments_request[$value['date']] : '';
						$wcomment = array();
						foreach ($weekComments as $weekComment){
							if( $weekComment['start_day'] <= $value['date'] &&  $weekComment['end_day'] >= $value['date'] ){
								$wcomment[] = $weekComment['comment'];
							}
						}
						$PTaskId = !empty($ATaskLinkPTasks[$value['task_id']]) ? $ATaskLinkPTasks[$value['task_id']] : '';
						$PhasePlanId = !empty($projectTasks[$PTaskId]) ? $projectTasks[$PTaskId] : '';
						$project_planed_phase_id = !empty($projectTasks[$PTaskId]['project_planed_phase_id']) ? $projectTasks[$PTaskId]['project_planed_phase_id'] : '';
						$PTaskTitle = isset($projectTasks[$PTaskId]['task_title']) ? $projectTasks[$PTaskId]['task_title'] : '';
						$projectID = !empty($projectTasks[$PTaskId]['project_id'])? $projectTasks[$PTaskId]['project_id'] : '';
						$project = isset( $projects[$projectID]) ? $projects[$projectID] : '';
						/**
						 * Lay activity id cua task
						 */
						$activityId = 0;
						if(!empty($value['task_id'])){
							$activityId = !empty($ATaskOfActivity[$value['task_id']]) ? $ATaskOfActivity[$value['task_id']] : 0;
						} else {
							$activityId = $value['activity_id'];
						}
						$familyId = !empty($activities[$activityId]['family_id']) ? $activities[$activityId]['family_id'] : '';
						$subfamilyId = !empty($activities[$activityId]['subfamily_id']) ? $activities[$activityId]['subfamily_id'] : '';
						$linkedProject = !empty($activities[$activityId]['project']) ? true : false;
						$project_program = (!empty($project['project_amr_program_id']) && !empty($projectAmrProgram[$project['project_amr_program_id']])) ? $projectAmrProgram[$project['project_amr_program_id']] : '';
						$valDate = '';
						if(!empty($value['status']) && $value['status'] == 2){
							$confirmForEmploy = !empty($listRequestConfirms[$employee['Employee']['id']]) ? $listRequestConfirms[$employee['Employee']['id']] : array();
							if(!empty($confirmForEmploy) && !empty($confirmForEmploy[$value['date']])){
								$valDate = $confirmForEmploy[$value['date']];
							}
						}
						$PhasePlanId = $project_planed_phase_id;
						$code1 = !empty($activities[$activityId]['code1']) ? $activities[$activityId]['code1'] : '';
						$code2 = !empty($activities[$activityId]['code2']) ? $activities[$activityId]['code2'] : '';
						$ref1 = !empty($phasePlans[$PhasePlanId]['ref1']) ? $phasePlans[$PhasePlanId]['ref1'] : '';
						$ref2 = !empty($phasePlans[$PhasePlanId]['ref2']) ? $phasePlans[$PhasePlanId]['ref2'] : '';
						$val = !empty($value['value']) ? $value['value'] : 0;

						$first = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
						$last = !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '';
						if( ((!empty($value['value']) && $value['value']!=0)&& $display == 'no') ||  $display == 'yes' ){
							$no++;
							$e_activity = array(
								'no' => $no,
								'first_name' => !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '',
								'last_name' => !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '',
								'profit_center_id' => !empty($idOfTeam[$pcId]) ? $idOfTeam[$pcId] : '',                                        
								'profit_center' => !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '',
								'id1' => !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '',
								'id2' => !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '',
								'id3' => !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '',
								'id4' => !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '',
								'id5' => !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '',
								'id6' => !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '',
								'family' => !empty($families[$familyId]) ? $families[$familyId] : '',
								'sub_family' => !empty($families[$subfamilyId]) ? $families[$subfamilyId] : '',
								'project_name' => isset( $project['project_name'] ) ? $project['project_name'] : '',
								'project_program' => $project_program,
								'phase_name' => (!empty($planIDToPhase[$project_planed_phase_id]) && !empty($phaseName[$planIDToPhase[$project_planed_phase_id]])) ? $phaseName[$planIDToPhase[$project_planed_phase_id]] : '',
								'task_name' => $PTaskTitle,
								'code_1' => $code1,
								'code_2' => $code2,
								'code_3' => !empty($activities[$activityId]['code3']) ? $activities[$activityId]['code3'] : '',
								'code_4' => !empty($activities[$activityId]['code4']) ? $activities[$activityId]['code4'] : '',
								'code_5' => !empty($activities[$activityId]['code5']) ? $activities[$activityId]['code5'] : '',
								'code_6' => !empty($activities[$activityId]['code6']) ? $activities[$activityId]['code6'] : '',
								'ref_1' => $linkedProject ? $ref1 : $code1,
								'ref_2' => $linkedProject ? $ref2 : $code2,
								'ref_3' => !empty($phasePlans[$PhasePlanId]['ref3']) ? $phasePlans[$PhasePlanId]['ref3'] : '',
								'ref_4' => !empty($phasePlans[$PhasePlanId]['ref4']) ? $phasePlans[$PhasePlanId]['ref4'] : '',
								'quantity' => !empty($value['value']) ? $value['value'] : 0,
								'date_activity_absence' => !empty($value['date']) ? $this->export_format_date($value['date'], $settingOfExports['date_activity_absence']) : '',
								'validation_date' => !empty($valDate) ? $this->export_format_date($valDate, $settingOfExports['date_activity_absence']) : '',
								'extraction_date' => $this->export_format_date(time(), $settingOfExports['date_activity_absence']),
								'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
								'date_export' => strtotime(date('d-m-Y', time())),
								'company_id' => $company_id,
								'employee_id' => !empty($employee['Employee']['id']) ? $employee['Employee']['id'] : 0,
								'fullname' => $first . ' ' . $last,
								// 'phase_plan_id' => $PhasePlanId,
								'phase_plan_id' => !empty($project_planed_phase_id) ? $project_planed_phase_id : '',
								'project_code_1' => isset( $project['project_code_1'] ) ? $project['project_code_1'] : '',
								'tjm' => !empty($employee['Employee']['tjm']) ? $employee['Employee']['tjm'] : '',
								'message' => !empty($comment) ? json_encode($comment) : '',
								'week_message' => !empty( $wcomment) ? json_encode($wcomment) : '',
								'project_id' => $projectID,
							);
							$e_data = array();
							$write = 0;
							
							foreach($fieldset as $key => $val){
								if( isset($e_activity[$key]) && $e_activity[$key] != '') 
									$write = 1;
								$e_data[$key] = isset($e_activity[$key]) ? $e_activity[$key] : '';
							}
							// Write to SCV
							if( $write) {
								fputcsv($file, $e_data);
							}else{
								$no--;
							}
						}
					}
				}
				/**
				 * END Export Activity
				 */
			}
			fclose($file);
			/**
			 * END Build data and inset to table tmp_module_activity_exports
			 */
			$results = array(
				'result' => true,
				'totalRecord' => $no,
				'filename' => $export_file
			);
		} // if $_POST
        echo json_encode($results);
        exit;
	exit;
	}
	private function export_format_date($timestamp, $setting=null){
		switch($setting){
			case 'date_two': {$format = 'd.m.Y';break;}
			case 'date_three': {$format = 'd/m/Y';break;}
			case 'date_one':
			default: {$format = 'd-m-Y';break;}
		}
		return date($format, $timestamp);
	}
	public function createExportFile(){
		$data = array(
			'result' => false,
			'filename' => '',
			'message' => '',
		);
		$export_file = !empty($this->data['filename']) ? $this->data['filename'] : '';
		if( $export_file ){
			$employeeLoginId = $this->employee_info['Employee']['id'];
			$company_id = $this->employee_info['Company']['id'];
			$path = SHARED . $employeeLoginId;
			App::import('Core', 'Folder');
			new Folder($path, true, 0777);
			// clear export folder here
			$filePath = $path . DS . $export_file;
			copy(TEMPLATES . 'module_ex_activity.csv', $filePath);
			$this->loadModel('ActivityExport');
			$curLang = !empty( $this->employee_info['Employee']['language']) ? $this->employee_info['Employee']['language'] : 'en';
			$curLang = $curLang == 'fr' ? 'france' : 'english';
			$_activityExports = $this->ActivityExport->find('all', array(
				'recursive' => -1,
				'conditions' => array('company_id' => $company_id, 'display' => 1),
				'fields' => array('name', $curLang . ' as text', 'setting'),
				'order' => array('weight' => 'ASC')
			));
			$fieldset = array();
			if(!empty($_activityExports)){
				foreach($_activityExports as $_activityExport){
					$dx = $_activityExport['ActivityExport'];
					$name = !empty($dx['name']) ? $dx['name'] : '';
					$val = !empty($dx['text']) ? $dx['text'] : '';
					$fieldset[strtolower(str_replace(array(' ', '/'), '_', trim($name)))] = $val;
				}
			}
			try{
				$file = fopen($filePath, 'w'); // ghi vao dau file
				fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
				fputcsv($file, array_values($fieldset));
				$data['result'] = true;
				$data['filename'] = $export_file;
				fclose($file);
			}catch(Exception $e){
				$data['message'] = 'Caught exception: '.  $e->getMessage();
			}
		}else{
			$data['message'] = 'Missing file name';
		}
		die(json_encode($data));
	}
	public function createExcelFile(){
		$data = array(
			'result' => false,
			'downloadURL' => '',
			'message' => '',
		);
		if( empty($_GET['filename'])){
			die(json_encode($data));
		}
		
		set_time_limit(0);
        ini_set('memory_limit', '1024M');
		$employeeLoginId = $this->employee_info['Employee']['id'];
		$company_id = $this->employee_info['Company']['id'];
		$part = SHARED . $employeeLoginId . DS;
		$filename = !empty($_GET['filename']) ? $_GET['filename'] : '';
		if( !file_exists( $part.$filename)){
			$data['message'] = __('File not exist on server', true);
			$data['filename'] = $filename;
			die(json_encode($data));
		}
		$curLang = !empty( $this->employee_info['Employee']['language']) ? $this->employee_info['Employee']['language'] : 'en';
		$curLang = $curLang == 'fr' ? 'france' : 'english';
		$this->loadModel('ActivityExport');
		$_activityExports = $this->ActivityExport->find('all', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $company_id, 'display' => 1),
			'fields' => array('name', $curLang . ' as text', 'setting'),
			'order' => array('weight' => 'ASC')
		));
		$fieldset = array();
		if(!empty($_activityExports)){
			foreach($_activityExports as $_activityExport){
				$dx = $_activityExport['ActivityExport'];
				$name = !empty($dx['name']) ? $dx['name'] : '';
				$val = !empty($dx['text']) ? $dx['text'] : '';
				$fieldset[strtolower(str_replace(array(' ', '/'), '_', trim($name)))] = $val;
			}
		}
		
		$this->set(compact('data', 'fieldset', 'filename', 'part', 'employeeLoginId'));
	}
	public function getForecastTeam(){

		$profit_id = $_POST['pc_id'];
		$_start = $_POST['start'];
		$_end = $_POST['end'];
		$_employees = $_POST['resources'];
		$_colspan = $_POST['colspan'];
		$_dayMaps = $_POST['dayMaps'];
		$_workdays = $_POST['workdays'];
		$_holiday = !empty($_POST['holidays']) ? $_POST['holidays'] : array();
		$_pc_name = $_POST['pc_name'];
		
		$_employees = array_combine($_employees, $_employees);
		
		if(!empty($profit_id)){
			$workloads = $this->_workloadFromTaskForecasts($_employees, $_start, $_end, 1, 'month', $profit_id);
		
		}
		
		$_employees['tNotAffec_'.$profit_id] = 'tNotAffec_'.$profit_id;
		$this->set(compact('workloads', '_start', '_end', '_employees', '_workdays', '_colspan', '_dayMaps', '_holiday', 'profit_id', '_pc_name'));
	}
	public function refreshStaffing($project_id){
		$this->loadModel('ProjectTask');
		if(!empty($project_id)){
			 $this->ProjectTask->staffingSystem($project_id);
			 die('OK');
		}
		die('KO');
	}
	public function reAssignTask(){
		$this->loadModels('NctWorkload', 'ProjectTaskEmployeeRefer');
		$employee_id = $_POST['employee_id'];
		$assigned = !empty($_POST['assigned']) ? $_POST['assigned'] : '';
		$is_nct = $_POST['is_nct'];
		$is_pc = $_POST['is_pc'];
		$task_id = $_POST['task_id'];
		$result = false;
		if(!empty($assigned)){
			$employeeRefer = $this->ProjectTaskEmployeeRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $task_id,
					'reference_id' => $assigned,
				),
				'fields' => array('id', 'id'),
			));
			if(!empty($employeeRefer)){
				$this->ProjectTaskEmployeeRefer->updateAll(
					array(
						'ProjectTaskEmployeeRefer.reference_id' => $employee_id,
						'ProjectTaskEmployeeRefer.is_profit_center' => $is_pc
					),
					array('ProjectTaskEmployeeRefer.id' => array_values($employeeRefer))
				);
				$result = true;
			}
			if($is_nct && $result){
				$nctWorkload = $this->NctWorkload->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $task_id,
						'reference_id' => $assigned,
					),
					'fields' => array('id', 'id'),
				));
				if(!empty($nctWorkload)){
					$this->NctWorkload->updateAll(
						array(
							'NctWorkload.reference_id' => $employee_id,
							'NctWorkload.is_profit_center' => $is_pc
						),
						array('NctWorkload.id' => array_values($nctWorkload))
					);
				}
			}
		}
		die(json_encode($result));
	}
	public function getEmployeeAssignToTasks(){
		$this->loadModels('ProjectTaskEmployeeRefer', 'ProjectTask', 'Employee', 'ProfitCenter');
		$task_id = $_POST['task_id'];
		// Employee assign
		$listEmployees = array();
		$listProfitCenters = array();
		$listAssigned = array();
		$taskEmployeeRefers = $this->ProjectTaskEmployeeRefer->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => $task_id,
			),
			'fields' => array('reference_id', 'is_profit_center')
		));
		die(json_encode($taskEmployeeRefers));
	}
	/**
	* QUANNV 31/12/2019. Ticket #523
	*/
	function getCommentRequest(){
        $result = array();
        if( !empty($this->data) ){
			$this->loadModel('ActivityForecastComment');
            $date = $this->data['date'];
			$employee_id = $this->employee_info['Employee']['id'];
			$company_id = $this->employee_info['Company']['id'];
            $commentRequest = $this->ActivityForecastComment->find('all', array(
                'recursive' => -1,
                'conditions' => array(
					'date' => $date,
					'employee_id' => $employee_id,
					'company_id' => $company_id,
					'is_timesheet_msg' => ($this->data['type']=='week'),
				),
                'fields' => array('id', 'employee_id', 'comment', 'created')
            ));
			$commentRequest = !empty($commentRequest) ? Set::combine($commentRequest, '{n}.ActivityForecastComment.id', '{n}.ActivityForecastComment') : array();
            $results = $commentRequest;
        }
        die(json_encode($results));
    }
	
	public function update_task_favourite($task_id = null, $is_favourite = true){
		$this->loadModels('ActivityTask', 'ProjectTaskFavourite', 'ProjectTask');
		$task_id = !empty( $this->data['task_id'] ) ? $this->data['task_id'] : '';
		$is_favourite = isset($this->data['favourite']) ? $this->data['favourite'] : (!empty($is_favourite) ? $is_favourite : true);
		$data = array();
		$result = false;
		if( !empty($task_id)){
			$activityTask = $this->ActivityTask->find('first', array(
				'recursive' => -1,
				'conditions' => array( 'id' => $task_id),
				'fields' => array('project_task_id')
			));
			$project_task_id = !empty($activityTask) ? $activityTask['ActivityTask']['project_task_id'] : '';
			
			$info = $this->ProjectTask->find('first', array(
				'recursive' => -1,
				'conditions' => array( 
					'id' => $project_task_id,
				),
				'fields' => array('id', 'project_id'),
			));
			if( empty( $info)){
				$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
			}else{
				$saveData = array(
					'task_id' => $project_task_id,
					'employee_id' => $this->employee_info['Employee']['id'],
					'favourite' => $is_favourite ? 1 : 0,
					'company_id' => $this->employee_info['Company']['id'],
					'project_id' => $info['ProjectTask']['project_id'],
					'created' => time(),
					'updated' => time(),
				);
				// debug( $saveData); exit;
				//this table save project_task_id
				$last = $this->ProjectTaskFavourite->find('first', array(
					'recursive' => -1,
					'conditions' => array( 
						'task_id' => $project_task_id,
						'employee_id' => $this->employee_info['Employee']['id']
					),
					'fields' => array('*'),
				));	
				if( !empty($last)){
					$this->ProjectTaskFavourite->id = $last['ProjectTaskFavourite']['id'];
					if( $is_favourite == 'toggle' ) $saveData['favourite'] = $last['ProjectTaskFavourite']['favourite'] == 1 ? 0 : 1;
					$saveData['created'] = $last['ProjectTaskFavourite']['created'];
				}else{
					$this->ProjectTaskFavourite->create();
				}
				$data = $this->ProjectTaskFavourite->save($saveData);
				if( !empty( $data )){
					$result = true;
					$data['activity_task_id'] = $task_id; // activity_task_id
					$this->Session->setFlash(__('Saved', true), 'success');
				}else{
					$this->Session->setFlash(__('Not saved', true), 'error');
				}
			}
		}
		$this->set(compact('data', 'result'));
	}
	function addCommentRequest(){
		$result = array();
        if( !empty($this->data) ){
            $employee_id = $this->employee_info['Employee']['id'];
			$company_id = $this->employee_info['Company']['id'];
            $result['employee_id'] = $employee_id;
            $result['company_id'] = $company_id;
            $result['created'] = date('Y-m-d', time());
            $result['updated'] = date('Y-m-d', time());
            $result['comment'] = $this->data['comment'];
            $result['date'] = $this->data['date'];
            $this->loadModel('ActivityForecastComment');
            $this->ActivityForecastComment->create();
            $this->ActivityForecastComment->save(array(
                'employee_id' => $employee_id,
                'company_id' => $company_id,
                'comment' => $this->data['comment'],
				'date' => $this->data['date'],
                'created' => time(),
                'updated' => time(),
				'is_timesheet_msg' => ($this->data['type']=='week'),
				
            ));
        }
        die(json_encode($result));
    }
	function getPermisionForecast($workloads){
		$this->loadModels('ProfitCenter', 'ProfitCenterManagerBackup');
		$employee_logged = $this->employee_info['Employee']['id'];
		$projects = array();
		$managerPC = array();
		foreach($workloads as $pc_id => $workload ){
			foreach($workload as $employee_id => $values){
				foreach($values as $date => $value){
					foreach($value as $index => $val){
						if(!empty($val['project_id'])) $projects[$val['project_id']] = $val['project_id'];
					}
				}
			}
			$listManagers = $this->ProfitCenter->getPath($pc_id, array('id',
            'IF(manager_id IS NOT NULL OR manager_id != "", manager_id, IF(manager_backup_id IS NOT NULL OR manager_backup_id != "", manager_backup_id, "")) as manager'), -1);
			$listPCRefer = array();
			if(!empty($listManagers)){
				foreach($listManagers as $index => $values){
					$managerPC[$pc_id][] = $values[0]['manager'];
					$listPCRefer[] = $values['ProfitCenter']['id'];
				}
			}
			if(!empty($listPCRefer)){
				$listBKPCManager = $this->ProfitCenterManagerBackup->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'profit_center_id' => $listPCRefer,
					),
					'fields' => array('id', 'employee_id'),
				));
				if(!empty($listBKPCManager)){
					foreach($listBKPCManager as $index => $employee_id){
						$managerPC[$pc_id][] = $employee_id;
					}
				}
			}
		}
		$project_manager = array();
		if(!empty($projects)){
			$this->loadModels('ProjectEmployeeManager', 'Project');
			$project_manager = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $projects,
					'project_manager_id' => $employee_logged,
				),
				'fields' => array('id', 'project_manager_id'),
			));
			$project_manager_refer = $this->ProjectEmployeeManager->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $projects,
					'project_manager_id' => $employee_logged,
					'NOT' => array('type' => 'RA')
				),
				'fields' => array('project_id', 'project_manager_id'),
			));
			$project_manager = $project_manager + $project_manager_refer;
		}
		return array($managerPC, $project_manager);
	}
	public function switchDisplayTask(){
		$success = false;
		if(!empty($_POST)){
			$this->loadModel('HistoryFilter');
            $display_name = (isset($this->data['value']) && $this->data['value'] == 'true') ? 1 : 0;
			$path = isset($this->data['key']) ? $this->data['key'] : '';
            if( empty( $path )) die(json_encode(false));			
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
            $saved = $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => $display_name,
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false)
            );
			if($saved){
				$success = true;
			}
		}
		die(json_encode($success));
	}
	/**
	* End
	*/

}