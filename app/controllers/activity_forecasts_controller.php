<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ActivityForecastsController extends AppController {

    var $helpers = array('ICal');
    var $components = array("Lib", 'MultiFileUpload', 'ZEmail');

    function beforeFilter() {
        parent::beforeFilter('deleteFileModuleExportActivity', 'getEmployeeOfProfitCenterUsingModuleExportActivity', 'exportActivityFollowEmployee', 'zipFileModuleExportActivity', 'countModuleExportActivity', 'get_infor_task', 'checking_absence', 'contextMenu', 'contextMenuCache', 'cleanupCacheMenu');
        $this->Auth->autoRedirect = false;
        $manage_multiple_resource = isset($this->companyConfigs['manage_multiple_resource']) && !empty($this->companyConfigs['manage_multiple_resource']) ?  true : false;
        $resource_see_him = isset($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) && !empty($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) ?  true : false;
        $this->set(compact('manage_multiple_resource', 'resource_see_him'));
        $this->fileTypes = 'jpg,jpeg,bmp,gif,png,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,xlsm,csv';
        $this->set('fileTypes', $this->fileTypes);
        
        $this->loadModel('ProfitCenter');
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    private function _reDel($path){
        $files = array_diff(scandir($path), array('.', '..'));
        foreach($files as $file){
            if(is_dir($path . DS . $file)){
                $this->_reDel($path . DS . $file);
            }
            if(is_file($path . DS . $file)){
                unlink($path . DS . $file);
            }
        }
        if(is_dir($path)){
            rmdir($path);
        }
    }
    function testDate($date = null){
        debug(strtotime('01-01-2016'));
        debug(strtotime('31-01-2016'));
        exit;
    }
    
	
	private function checkPermissionTimesheet($e_ID = null, $is_redirect= true) {
		$canWrite = $canRead = 0;
		$isPCManager = 0;
		$userRole = $this->employee_info['CompanyEmployeeReference']['role_id'];
		$current_EID = $this->employee_info['Employee']['id'];
		$currentCompanyID = $this->employee_info['Company']['id'];
		if( empty($e_ID) ) $e_ID = $this->employee_info['Employee']['id'];
		$employee = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $e_ID),
			'fields' => array('id','profit_center_id', 'company_id'),
		));
		$company_id = !empty($employee['Employee']['company_id']) ? $employee['Employee']['company_id'] : '';
		// Access to other company data is denied
		if( $company_id != $currentCompanyID) {
			$this->Session->setFlash(__('Access denied', true), 'error');
			if( $is_redirect) $this->redirect(array( 'action' => 'request'));
		}
		
		switch( $userRole){
			case 2: //Admin 
				$canWrite = 1;
				$canRead = 1;
			break;
			//PM and Consultant can edit their own timesheets
			case 4: //Consultant
			case 3: //PM
				$canWrite = $canRead = ( $e_ID == $current_EID) ? 1 : 0;
			break;
		}
		if ( $userRole  == 3){
			$canRead = !empty( $this->employee_info['CompanyEmployeeReference']['control_resource']) ? $this->employee_info['CompanyEmployeeReference']['control_resource'] : $canRead; //can Manage Resource
			$canWrite = !empty( $this->employee_info['CompanyEmployeeReference']['control_resource']) ? $this->employee_info['CompanyEmployeeReference']['control_resource'] : $canWrite; 
		}
		/*
		* Check PC manager 
		*/
		// Get PC and Manager of PC of  $e_ID
		$this->loadModel('ProfitCenter');
		$profit = !empty($employee['Employee']['profit_center_id']) ? $employee['Employee']['profit_center_id'] : '';
		$profits = $this->ProfitCenter->getPath($profit);
		$isPCManager = 0;
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
		$this->set(compact('canRead', 'canWrite', 'isPCManager')); 
		if( empty( $canRead)) {
			$this->Session->setFlash(__('Access denied', true), 'error');
			if( $is_redirect) $this->redirect(array( 'controller' => 'activity_forecasts', 'action' => 'request'));
		}
		return array($canRead, $canWrite, $isPCManager);
	}
	function request($typeSelect = null) {
        $this->loadModel('Activity');
        $this->loadModel('ActivitySetting');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $this->loadModel('ProjectTeam');
        $this->loadModel('ActivityRequest');
        $this->loadModel('Family');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ActivityRequestConfirm');
        $this->loadModel('ActivityRequestConfirmMonth');
        $this->loadModel('Employee');
        $this->loadModel('HistoryFilter');
        $this->loadModel('ActivityRequestCopy');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ActivityProfitRefer');
		$idEmployee = !empty($_GET['id']) ? $_GET['id'] : $this->employee_info['Employee']['id'];
		list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($idEmployee); // Z0G 15/10/2019 Permission for timesheet screen
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        $ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
        $employeeName = $this->_getEmpoyee();
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        list($isManage, $employeeName) = $this->_getProfitEmployee($getDataByPath);
        // if( !empty($this->params['url']['profit']) ){
            // $_idPc = $this->params['url']['profit'];
        // } else {
            $_idPc = $employeeName['profit_center_id'];
        // }
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true);
        }else{
            list($_start, $_end) = $this->_parseParamsMonth();
            $_start = $this->viewVars['_start'] = strtotime("01/01/".date('Y',$_start));
            $_end = $this->viewVars['_end'] = strtotime("12/31/".date('Y',$_end));
        }
        /**
         * Cac ngay lam viec trong tuan
         */
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($_start, $_end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
        /**
         * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
         */
        $listWorkingDays = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
        /**
         * Lay profit center dang duoc chon
         */
		/*
			Clean cache menu before load page
		*/
		$this->beforeCleanupCacheMenu($idEmployee);
		$idEmp = $idEmployee;
		// employee displayed
		$employee = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $idEmp),
            'fields' => array('id', 'company_id', 'profit_center_id', 'first_name', 'last_name', 'email', 'tjm', 'actif', 'email_receive', 'profile_id', 'activate_copy', 'capacity_by_year', 'auto_timesheet', 'auto_by_himself', 'profile_account'),
        ));
        // if ($isManage) {
            // $profit = $this->params['url']['profit'];
        // } else {
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $profitQuery = $this->ProjectEmployeeProfitFunctionRefer->find(
            'first', array(
            'fields' => array('profit_center_id'),
            'recursive' => -1,
            'conditions' => array('employee_id' => $idEmp)));
            $profit = Set::classicExtract($profitQuery, 'ProjectEmployeeProfitFunctionRefer.profit_center_id');
        // }
        /**
         * Lay nhung request cua tuan nay
         */
        $_activityRequests = $this->ActivityRequest->find("all", array(
            'recursive' => -1, "conditions" => array('date BETWEEN ? AND ?' => array(
                    $_start, $_end), 'employee_id' => $idEmp)));
         /**
         * Nhom lai cac gia tri da request
         */
        $activityRequests = $activityForecasts = $lisTaskRequests = $lisHistoryTasks = $lisActivityRequests = $status = $checkFullDays = $checkFullDayActivities = $checkFullDayTasks = array();
        $lisHistoryTasks = $this->HistoryFilter->find('first',array(
            'conditions' => array('HistoryFilter.employee_id'=>$idEmp,'HistoryFilter.path'=>'activity_request_history'),
            'recursive' => -1,
        ));

        if (!empty($lisHistoryTasks)) {
            $lisHistoryTasks = $lisHistoryTasks['HistoryFilter']['params'];
        } else {
            $lisHistoryTasks = '{}';
        }
        $taskNotAccessDeletes = $activityNotAccessDeletes = $totalColumnRequests = array();
        foreach ($_activityRequests as $_activity) {
            $_activity = array_shift($_activity);
            $id = $_activity['activity_id'] . (!empty($_activity['task_id']) ? '-' . $_activity['task_id'] : '');
            $activityRequests[$id][$_activity['date']] = $_activity;
            $lisTaskRequests[] = $_activity['task_id'];
            $lisActivityRequests[] = $_activity['activity_id'];
            $status[$_activity['status']] = $_activity['status'];
            if($_activity['status'] == 0 || $_activity['status'] == 2){
                if($_activity['activity_id'] != 0){
                    $activityNotAccessDeletes[$_activity['activity_id']] = $_activity['activity_id'];
                    $checkFullDayActivities[$_activity['activity_id']][$_activity['date']] = $_activity['status'];
                }
                if($_activity['task_id'] != 0){
                    $taskNotAccessDeletes[$_activity['task_id']] = $_activity['task_id'];
                    $checkFullDayTasks[$_activity['task_id']][$_activity['date']] = $_activity['status'];
                }
                $checkFullDays[$_activity['date']] = $_activity['date'];
            }
            $totalColumnRequests[$_activity['date']] = $_activity['date'];
        }
        if($typeSelect == 'week'){
            $requestConfirm = $this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status', 'updated', 'employee_validate'),
                            'conditions' => array('employee_id' => $idEmp, 'start' => $_start, 'end' => $_end)));

            $requestConfirmDate = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['updated'] : '';
            $requestConfirmName = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['employee_validate'] : '';
            $requestConfirm = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['status'] : '-1';
        }elseif($typeSelect == 'month'){
            //COMMENT OLD
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            $countWeek=count($listWeekOfMonths);
            $countValidate=array();
            $countValidate[2]=$countValidate[1]=$countValidate[0]=$countValidate[-1]=0;
            foreach($listWeekOfMonths as $key=>$value){
                $requestConfirmTemp = $this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status', 'updated', 'employee_validate'),
                            'conditions' => array('employee_id' => $idEmp, 'start' => $key, 'end' => $value)));
                $requestConfirmDate = !empty($requestConfirmTemp) ? $requestConfirmTemp['ActivityRequestConfirm']['updated'] : '';
                $requestConfirmName = !empty($requestConfirmTemp) ? $requestConfirmTemp['ActivityRequestConfirm']['employee_validate'] : '';
                if($requestConfirmTemp['ActivityRequestConfirm']['status'] == ''){
                    $requestConfirmTemp['ActivityRequestConfirm']['status']=-1;
                }
                if(!isset($countValidate[$requestConfirmTemp['ActivityRequestConfirm']['status']]))
                {
                    $countValidate[$requestConfirmTemp['ActivityRequestConfirm']['status']]=0;
                }
                $countValidate[$requestConfirmTemp['ActivityRequestConfirm']['status']]++;

            }
            if($countValidate[2]==$countWeek)
            {
                $requestConfirm=2;
            }
            elseif($countValidate[1]==$countWeek)
            {
                $requestConfirm=1;
            }
            elseif($countValidate[0]==$countWeek)
            {
                $requestConfirm=0;
            }
            elseif($countValidate[-1]==0)
            {
                $requestConfirm=0;
            }
            elseif($countValidate[-1]==0&&$countValidate[0]==0)
            {
                $requestConfirm=1;
            }
            else
            {
                $requestConfirm=-1;
            }
        } else {
            $requestConfirm = 2;
        }
        $lisTaskRequests = !empty($lisTaskRequests) ? array_unique($lisTaskRequests) : array();
        $activityLists = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.id' => $lisTaskRequests),
            'fields' => array('id', 'activity_id')
        ));
        $lisActivityRequests = !empty($activityLists) ? array_unique(array_merge($lisActivityRequests, $activityLists)) : array_unique($lisActivityRequests);
        $checkEmployees = $this->_checkEmployee();
		
		/* Z0G 23/10/2019 - Get data popup copy forecasts by ajax function
		* by Huynh Le
		*/ 

		// Get Autofill timesheet setting 
		$activateCopy = !empty( $employee['Employee']['activate_copy']) ?  $employee['Employee']['activate_copy'] : 0;
		// check timesheet copied or not. If "Autofill timesheet" setting == 0, deemed as copied
		$haveCopy = ($activateCopy ? $this->ActivityRequestCopy->find('count', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $idEmp, 'start' => $_start, 'end' => $_end, 'company_id' => $employeeName['company_id'])
        )) : 1 ) ;
		$fillTimesheet = empty($haveCopy) ? 1 : 0;
		$getDatas = $activityFromTasks = $projectFromTasks = $projectLinkedActivity = $projectPhaseFromTasks = $projectPartFromTasks = $dataForecasts = array();
		if( $fillTimesheet){
			$getDatas = $this->_workloadFromTask($idEmp, $_idPc, $_start, $_end);
			$activityFromTasks = !empty($getDatas['activities']) ? $getDatas['activities'] : array();
			$projectFromTasks = !empty($getDatas['projects']) ? $getDatas['projects'] : array();
			$projectLinkedActivity = !empty($getDatas['projectLinkedActivity']) ? $getDatas['projectLinkedActivity'] : array();
			$projectPhaseFromTasks = !empty($getDatas['projectPhases']) ? $getDatas['projectPhases'] : array();
			$projectPartFromTasks = !empty($getDatas['projectParts']) ? $getDatas['projectParts'] : array();
			$dataForecasts = !empty($getDatas['results']) ? $getDatas['results'] : array();
			
			/**
			 * Chi copy tuan
			 */
			if (empty($isManage) || ($isManage && ($requestConfirm == -1 || $requestConfirm == 1))){
				if($requestConfirm == -1 && $haveCopy == 0 && $typeSelect === 'week' && $activateCopy == 1){ // chi copy o tuan
					/**
					 * Lay ngay copies
					 */
					$dateCopy = '';
					$checkDateBeforeUpdates = $this->ActivityRequest->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'date' => $listWorkingDays,
							'employee_id' => $idEmp,
							'company_id' => $employeeName['company_id'],
							'status' => array(0, 2)
						),
						'fields' => array('date', 'id')
					));
					foreach($listWorkingDays as $day => $time){
						if(empty($checkDateBeforeUpdates[$time])){
							$dateCopy = $time;
							break;
						}
					}
					$dataCopies = array();
					foreach( $dataForecasts as $_task){
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
									if(in_array($idTask, $lisTaskRequests)){
										// da co request
									} else {
										$saved[] = array(
											'date' => $dateCopy,
											'value' => 0,
											'employee_id' => $idEmp,
											'company_id' => $employeeName['company_id'],
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
								'employee_id' => $idEmp,
								'start' => $_start,
								'end' => $_end,
								'company_id' => $employeeName['company_id']
							));
							  /** Sau khi save 
							 * Lay nhung request cua tuan nay
							 */
							$_activityRequests = $this->ActivityRequest->find("all", array(
								'recursive' => -1, "conditions" => array('date BETWEEN ? AND ?' => array(
										$_start, $_end), 'employee_id' => $idEmp)));
							 /**
							 * Nhom lai cac gia tri da request
							 */
							$activityRequests = $activityForecasts = $lisTaskRequests = $lisActivityRequests = array();
							foreach ($_activityRequests as $_activity) {
								$_activity = array_shift($_activity);
								$id = $_activity['activity_id'] . (!empty($_activity['task_id']) ? '-' . $_activity['task_id'] : '');
								$activityRequests[$id][$_activity['date']] = $_activity;
								$lisTaskRequests[] = $_activity['task_id'];
								$lisActivityRequests[] = $_activity['activity_id'];
							}
							$lisTaskRequests = !empty($lisTaskRequests) ? array_unique($lisTaskRequests) : array();
							$activityLists = $this->ActivityTask->find('list', array(
								'recursive' => -1,
								'conditions' => array('ActivityTask.id' => $lisTaskRequests),
								'fields' => array('id', 'activity_id')
							));
							$lisActivityRequests = !empty($activityLists) ? array_unique(array_merge($lisActivityRequests, $activityLists)) : array_unique($lisActivityRequests);
						}
					}
				}
			}
		}
		
		/* Move from view to controller */
		$listTaskDisplay = $listActivityDisplay = array();
		$resource_see_him = isset($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) && !empty($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) ?  true : false;
		if($resource_see_him){
			/**
			 * Check cac task cua ong nay
			 */
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
					)
				),
				'fields' => array('project_task_id', 'project_task_id')
			));
			$getTaskLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array('project_task_id' => $getListAssignPTasks),
				'fields' => array('id', 'id')
			));
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
					)
				),
				'fields' => array('activity_task_id', 'activity_task_id')
			));
			$listTaskDisplay = array_merge($getTaskLinked, $getListAssignATasks);
			/**
			 * Lay cac team co profit center va employee join vao
			 */
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
			$teams = $this->ProjectTeam->find('list', array(
				'recursive' => -1,
				'conditions' => array('ProjectTeam.id' => $teams),
				'fields' => array('project_id', 'project_id')
			));
			/**
			 * Lay cac activity co trong accessible par _ linked profit
			 */
			$activityOne = $this->ActivityProfitRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array('profit_center_id' => $profit),
				'fields' => array('activity_id', 'activity_id')
			));
			/**
			 * Lay activity theo asssign o task
			 */
			$activityTwo = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array('ActivityTask.id' => $listTaskDisplay),
				'fields' => array('activity_id', 'activity_id')
			));
			/**
			 * Lay activity theo project link
			 */
			$activityThree = $this->Activity->find('list', array(
				'recursive' => -1,
				'conditions' => array('project' => $teams),
				'fields' => array('id', 'id')
			));
			$listActivityDisplay = array_unique(array_merge($activityOne, $activityTwo, $activityThree));
			$listActivityDisplay = array_merge($listActivityDisplay);
		}
		/* END Move from view to controller */
		/*
		 * END Z0G 23/10/2019 - Get data popup copy forecasts by ajax function
		 */
        $this->_parseRequest($employeeName, $lisActivityRequests, $lisTaskRequests, array($idEmp => $employeeName['first_name'] . ' ' . $employeeName['last_name']), $_start, $_end);
        //Only team defined in project/team can consume in project
        $profitRefers = $this->ProjectFunctionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('profit_center_id' => $profit),
            'fields' => array('id', 'project_team_id')
        ));
        $profitRefers = !empty($profitRefers) ? array_unique($profitRefers) : array();
        $profitHasProject = $this->ProjectTeam->find('list',array(
             'recursive' => -1,
             'conditions' => array('ProjectTeam.id' => $profitRefers),
             'fields' => array('project_id', 'project_id')
        ));
        //Only employee defined in project/team can consume in project
        $employRefers = $this->ProjectFunctionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $idEmp),
            'fields' => array('id', 'project_team_id')
        ));
        $employRefers = !empty($employRefers) ? array_unique($employRefers) : array();
        $employeeHasProject = $this->ProjectTeam->find('list',array(
             'recursive' => -1,
             'conditions' => array('ProjectTeam.id' => $employRefers),
             'fields' => array('project_id', 'project_id')
        ));
        $allowRequestRemain  = $this->ActivitySetting->find('first');
        $lisTaskRequests = !empty($lisTaskRequests) ? array_unique($lisTaskRequests) : array();
        $company = $employeeName['company_id'];
        $employeeId = $idEmp;
        $cacheName = $company . '_' . $employeeId . '_context_menu_cache';
        $cacheMenu = Cache::read($cacheName);
        if( $employeeName['external'] == 2 ){
            //get Capacity
            $mCapacity = ClassRegistry::init('EmployeeMultiResource')->getCapacity($employeeName['id'], $_start, $_end, 'date');
            if( count($mCapacity) > 1 ){
                $this->set('mCapacity', $mCapacity[$employeeName['id']]);
            } else {
                $this->set('mCapacity', $mCapacity);
            }
            $this->set('isMulti', true);
        } else {
            $this->set('isMulti', false);
        }
        //get width resize of foreemploy column
        $foreEmployWidth = $this->HistoryFilter->find('first', array(
            'conditions' => array('HistoryFilter.employee_id' => $this->employee_info['Employee']['id'],
                                  'path'=>'activity_forecasts/request',
                            ),
            'fields' => array('params')
        ));
        $screenSettings = array('feWidth' => 0, 'sort' => true);
        if(!empty($foreEmployWidth)){
            $newSettings = json_decode($foreEmployWidth['HistoryFilter']['params'], true);
            if(!empty($newSettings)){
                $screenSettings = array_merge($screenSettings, $newSettings);
            }
        }
        $copyActivity = isset($this->companyConfigs['copy_in_activity_list']) && !empty($this->companyConfigs['copy_in_activity_list']) ?  1 : 0;
        $getHour = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeName['id']),
            'fields' => array('hour', 'minutes')
        ));
        $sendTimesheetPartially = !$managerHour && isset($this->companyConfigs['send_timesheet_partially_filled']) && !empty($this->companyConfigs['send_timesheet_partially_filled']) ?  1 : 0;
        $fillMoreThanCapacity = !$managerHour && isset($this->companyConfigs['fill_more_than_capacity_day']) && !empty($this->companyConfigs['fill_more_than_capacity_day']) ?  1 : 0;
        $showActivityForecastComment = ( isset($this->companyConfigs['show_activity_forecast_comment']) && !empty($this->companyConfigs['fill_more_than_capacity_day']) ) ?  $this->companyConfigs['show_activity_forecast_comment'] : 0;
		$this->_getListEmployee();
		$profit_name = $this->ProfitCenter->find('list', array(
			'conditions' => array(
				'id' => $profit,
             ),
            'fields' => array('id', 'name')
        ));
		$profit_name = !empty($profit_name) ? $profit_name[$profit] : '';
        $this->set(compact('fillTimesheet', 'managerHour', 'holidays', 'taskNotAccessDeletes', 'activityNotAccessDeletes', 'checkFullDays', 'checkFullDayActivities', 'checkFullDayTasks', 'requestConfirm', 'typeSelect', 'listWorkingDays', 'workdays', 'activityFromTasks', 'projectFromTasks', 'lisHistoryTasks', 'profit_name'));
        $this->set(compact('cacheMenu', 'cacheName', 'dataForecasts', 'lisTaskRequests','allowRequestRemain'));
        $this->set(compact('projectPhaseFromTasks', 'projectPartFromTasks', 'copyActivity', 'activateCopy', 'activityRequests', 'isManage', 'profit', 'activityForecasts', 'requestConfirmDate', 'requestConfirmName', 'checkEmployees', 'employeeHasProject', 'profitHasProject', 'getDataByPath'));
        $this->set(compact('screenSettings', 'getHour', 'sendTimesheetPartially', 'ratio', 'fillMoreThanCapacity', 'showActivityForecastComment'));
		$this->set(compact('listActivityDisplay', 'listTaskDisplay'));
	
    }
	public function request_ajax($typeSelect = 'week') {
		$this->loadModel('Activity');
        $this->loadModel('ActivitySetting');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $this->loadModel('ProjectTeam');
        $this->loadModel('ActivityRequest');
        $this->loadModel('Family');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ActivityRequestConfirm');
        $this->loadModel('ActivityRequestConfirmMonth');
        $this->loadModel('Employee');
        $this->loadModel('HistoryFilter');
        $this->loadModel('ActivityRequestCopy');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ActivityProfitRefer');
		$idEmployee = !empty($_GET['id']) ? $_GET['id'] : $this->employee_info['Employee']['id'];
		list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($idEmployee); // Z0G 15/10/2019 Permission for timesheet screen
		$managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
		$ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
        $employeeName = $this->_getEmpoyee();
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        list($isManage, $employeeName) = $this->_getProfitEmployee($getDataByPath);
        // if( !empty($this->params['url']['profit']) ){
            // $_idPc = $this->params['url']['profit'];
        // } else {
            $_idPc = $employeeName['profit_center_id'];
        // }
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true);
		}
		/**
         * Cac ngay lam viec trong tuan
         */
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($_start, $_end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
        /**
         * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
         */
        $listWorkingDays = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
        /**
         * Lay profit center dang duoc chon
         */
		/*
			Clean cache menu before load page
		*/
		$idEmp = $idEmployee;
		// employee displayed
		$employee = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $idEmp),
            'fields' => array('id', 'company_id', 'profit_center_id', 'first_name', 'last_name', 'email', 'tjm', 'actif', 'email_receive', 'profile_id', 'activate_copy', 'capacity_by_year', 'auto_timesheet', 'auto_by_himself', 'profile_account'),
        ));
        // if ($isManage) {
            // $profit = $this->params['url']['profit'];
        // } else {
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $profitQuery = $this->ProjectEmployeeProfitFunctionRefer->find(
            'first', array(
            'fields' => array('profit_center_id'),
            'recursive' => -1,
            'conditions' => array('employee_id' => $idEmp)));
            $profit = Set::classicExtract($profitQuery, 'ProjectEmployeeProfitFunctionRefer.profit_center_id');
        // }
		if($typeSelect == 'week'){
            $requestConfirm = $this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status', 'updated', 'employee_validate'),
                            'conditions' => array('employee_id' => $idEmp, 'start' => $_start, 'end' => $_end)));

            $requestConfirmDate = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['updated'] : '';
            $requestConfirmName = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['employee_validate'] : '';
			$requestConfirmInfo = $requestConfirm['ActivityRequestConfirm'];
            $requestConfirm = !empty($requestConfirm) ? $requestConfirm['ActivityRequestConfirm']['status'] : '-1';
        }elseif($typeSelect == 'month'){
            //COMMENT OLD
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            $countWeek=count($listWeekOfMonths);
            $countValidate=array();
            $countValidate[2]=$countValidate[1]=$countValidate[0]=$countValidate[-1]=0;
            foreach($listWeekOfMonths as $key=>$value){
                $requestConfirmTemp = $this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status', 'updated', 'employee_validate'),
                            'conditions' => array('employee_id' => $idEmp, 'start' => $key, 'end' => $value)));
                $requestConfirmDate = !empty($requestConfirmTemp) ? $requestConfirmTemp['ActivityRequestConfirm']['updated'] : '';
                $requestConfirmName = !empty($requestConfirmTemp) ? $requestConfirmTemp['ActivityRequestConfirm']['employee_validate'] : '';
                if($requestConfirmTemp['ActivityRequestConfirm']['status'] == ''){
                    $requestConfirmTemp['ActivityRequestConfirm']['status']=-1;
                }
                if(!isset($countValidate[$requestConfirmTemp['ActivityRequestConfirm']['status']]))
                {
                    $countValidate[$requestConfirmTemp['ActivityRequestConfirm']['status']]=0;
                }
                $countValidate[$requestConfirmTemp['ActivityRequestConfirm']['status']]++;

            }
            if($countValidate[2]==$countWeek)
            {
                $requestConfirm=2;
            }
            elseif($countValidate[1]==$countWeek)
            {
                $requestConfirm=1;
            }
            elseif($countValidate[0]==$countWeek)
            {
                $requestConfirm=0;
            }
            elseif($countValidate[-1]==0)
            {
                $requestConfirm=0;
            }
            elseif($countValidate[-1]==0&&$countValidate[0]==0)
            {
                $requestConfirm=1;
            }
            else
            {
                $requestConfirm=-1;
            }
        } else {
            $requestConfirm = 2;
        }
		// Get Autofill timesheet setting 
		$activateCopy = !empty( $employee['Employee']['activate_copy']) ?  $employee['Employee']['activate_copy'] : 0;
		// check timesheet copied or not. If "Autofill timesheet" setting == 0, deemed as copied
		$haveCopy = ($activateCopy ? $this->ActivityRequestCopy->find('count', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $idEmp, 'start' => $_start, 'end' => $_end, 'company_id' => $employeeName['company_id'])
        )) : 1 ) ;
		$fillTimesheet = empty($haveCopy) ? 1 : 0;
		$getDatas = $activityFromTasks = $projectFromTasks = $projectLinkedActivity = $projectPhaseFromTasks = $projectPartFromTasks = $dataForecasts = array();
		if( $fillTimesheet){			
			$getDatas = $this->_workloadFromTask($idEmp, $_idPc, $_start, $_end);
			$activityFromTasks = !empty($getDatas['activities']) ? $getDatas['activities'] : array();
			$projectFromTasks = !empty($getDatas['projects']) ? $getDatas['projects'] : array();
			$projectLinkedActivity = !empty($getDatas['projectLinkedActivity']) ? $getDatas['projectLinkedActivity'] : array();
			$projectPhaseFromTasks = !empty($getDatas['projectPhases']) ? $getDatas['projectPhases'] : array();
			$projectPartFromTasks = !empty($getDatas['projectParts']) ? $getDatas['projectParts'] : array();
			$dataForecasts = !empty($getDatas['results']) ? $getDatas['results'] : array();
			/**
			 * Chi copy tuan
			 */
			if (empty($isManage) || ($isManage && ($requestConfirm == -1 || $requestConfirm == 1))){
				if($requestConfirm == -1 && $haveCopy == 0 && $typeSelect === 'week' && $activateCopy == 1){ // chi copy o tuan
					/**
					 * Lay ngay copies
					 */
					$dateCopy = '';
					$checkDateBeforeUpdates = $this->ActivityRequest->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'date' => $listWorkingDays,
							'employee_id' => $idEmp,
							'company_id' => $employeeName['company_id'],
							'status' => array(0, 2)
						),
						'fields' => array('date', 'id')
					));
					foreach($listWorkingDays as $day => $time){
						if(empty($checkDateBeforeUpdates[$time])){
							$dateCopy = $time;
							break;
						}
					}
					$dataCopies = array();
					foreach( $dataForecasts as $_task){
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
					/** 
					 * Lay nhung request cua tuan nay
					 */
					$lisTaskRequests = $this->ActivityRequest->find("list", array(
						'recursive' => -1, 
						"conditions" => array(
							'date BETWEEN ? AND ?' => array($_start, $_end),
							'employee_id' => $idEmp
						),
						'fields' => array('task_id', 'task_id')
					));
					$lisTaskRequests = !empty($lisTaskRequests) ? array_values($lisTaskRequests) : array(); 
					$saved = array();
					if(!empty($dataCopies)){
						foreach($dataCopies as $dataCopy){
							if(!empty($dataCopy)){
								$dataCopy = array_unique($dataCopy);
								foreach($dataCopy as $idTask){
									if(in_array($idTask, $lisTaskRequests)){
										// da co request
									} else {
										$saved[] = array(
											'date' => $dateCopy,
											'value' => 0,
											'employee_id' => $idEmp,
											'company_id' => $employeeName['company_id'],
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
								'employee_id' => $idEmp,
								'start' => $_start,
								'end' => $_end,
								'company_id' => $employeeName['company_id']
							));
						}
					}
				}
			}
		}
		/** 
		 * Lay lai sau khi copy nhung request cua tuan nay
		 */
		$_activityRequests = $this->ActivityRequest->find("all", array(
			'recursive' => -1, 
			"conditions" => array(
				'date BETWEEN ? AND ?' => array($_start, $_end),
				'employee_id' => $idEmp
			)
		));
		 /**
		 * Nhom lai cac gia tri da request
		 */
		$activityRequests = $activityForecasts = $lisTaskRequests = $lisActivityRequests = $status = $checkFullDays = $checkFullDayActivities = $checkFullDayTasks = array();
		$taskNotAccessDeletes = $activityNotAccessDeletes = $totalColumnRequests = array();
		foreach ($_activityRequests as $_activity) {
			$_activity = array_shift($_activity);
            $id = $_activity['activity_id'] . (!empty($_activity['task_id']) ? '-' . $_activity['task_id'] : '');
            $activityRequests[$id][$_activity['date']] = $_activity;
            if( !empty($_activity['task_id'])) $lisTaskRequests[] = $_activity['task_id'];
            if( !empty($_activity['activity_id'])) $lisActivityRequests[] = $_activity['activity_id'];
            $status[$_activity['status']] = $_activity['status'];
            if($_activity['status'] == 0 || $_activity['status'] == 2){
                if($_activity['activity_id'] != 0){
                    $activityNotAccessDeletes[$_activity['activity_id']] = $_activity['activity_id'];
                    $checkFullDayActivities[$_activity['activity_id']][$_activity['date']] = $_activity['status'];
                }
                if($_activity['task_id'] != 0){
                    $taskNotAccessDeletes[$_activity['task_id']] = $_activity['task_id'];
                    $checkFullDayTasks[$_activity['task_id']][$_activity['date']] = $_activity['status'];
                }
                $checkFullDays[$_activity['date']] = $_activity['date'];
            }
            $totalColumnRequests[$_activity['date']] = $_activity['date'];
		}
		$lisTaskRequests = !empty($lisTaskRequests) ? array_unique($lisTaskRequests) : array();
		$activityLists = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array('ActivityTask.id' => $lisTaskRequests),
			'fields' => array('id', 'activity_id')
		));
		$lisActivityRequests = !empty($activityLists) ? array_unique(array_merge($lisActivityRequests, $activityLists)) : array_unique($lisActivityRequests);
		
		
		/* Move from view to controller */
		$listTaskDisplay = $listActivityDisplay = array();
		$resource_see_him = isset($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) && !empty($this->companyConfigs['a_resource_see_only_task_assigned_to_him']) ?  true : false;
		if($resource_see_him){
			/**
			 * Check cac task cua ong nay
			 */
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
					)
				),
				'fields' => array('project_task_id', 'project_task_id')
			));
			$getTaskLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array('project_task_id' => $getListAssignPTasks),
				'fields' => array('id', 'id')
			));
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
					)
				),
				'fields' => array('activity_task_id', 'activity_task_id')
			));
			$listTaskDisplay = array_merge($getTaskLinked, $getListAssignATasks);
			/**
			 * Lay cac team co profit center va employee join vao
			 */
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
			$teams = $this->ProjectTeam->find('list', array(
				'recursive' => -1,
				'conditions' => array('ProjectTeam.id' => $teams),
				'fields' => array('project_id', 'project_id')
			));
			/**
			 * Lay cac activity co trong accessible par _ linked profit
			 */
			$activityOne = $this->ActivityProfitRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array('profit_center_id' => $profit),
				'fields' => array('activity_id', 'activity_id')
			));
			/**
			 * Lay activity theo asssign o task
			 */
			$activityTwo = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array('ActivityTask.id' => $listTaskDisplay),
				'fields' => array('activity_id', 'activity_id')
			));
			/**
			 * Lay activity theo project link
			 */
			$activityThree = $this->Activity->find('list', array(
				'recursive' => -1,
				'conditions' => array('project' => $teams),
				'fields' => array('id', 'id')
			));
			$listActivityDisplay = array_unique(array_merge($activityOne, $activityTwo, $activityThree));
			$listActivityDisplay = array_merge($listActivityDisplay);
		}
		/* END Move from view to controller */
		$this->_parseRequest($employeeName, $lisActivityRequests, $lisTaskRequests, array($idEmp => $employeeName['first_name'] . ' ' . $employeeName['last_name']), $_start, $_end);
        //Only team defined in project/team can consume in project
        $profitRefers = $this->ProjectFunctionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('profit_center_id' => $profit),
            'fields' => array('id', 'project_team_id')
        ));
        $profitRefers = !empty($profitRefers) ? array_unique($profitRefers) : array();
        $profitHasProject = $this->ProjectTeam->find('list',array(
             'recursive' => -1,
             'conditions' => array('ProjectTeam.id' => $profitRefers),
             'fields' => array('project_id', 'project_id')
        ));
        //Only employee defined in project/team can consume in project
        $employRefers = $this->ProjectFunctionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $idEmp),
            'fields' => array('id', 'project_team_id')
        ));
        $employRefers = !empty($employRefers) ? array_unique($employRefers) : array();
        $employeeHasProject = $this->ProjectTeam->find('list',array(
             'recursive' => -1,
             'conditions' => array('ProjectTeam.id' => $employRefers),
             'fields' => array('project_id', 'project_id')
        ));
        $allowRequestRemain  = $this->ActivitySetting->find('first');
        $lisTaskRequests = !empty($lisTaskRequests) ? array_unique($lisTaskRequests) : array();
        $employeeId = $idEmp;
        if( $employeeName['external'] == 2 ){
            //get Capacity
            $mCapacity = ClassRegistry::init('EmployeeMultiResource')->getCapacity($employeeName['id'], $_start, $_end, 'date');
            if( count($mCapacity) > 1 ){
                $this->set('mCapacity', $mCapacity[$employeeName['id']]);
            } else {
                $this->set('mCapacity', $mCapacity);
            }
            $this->set('isMulti', true);
        } else {
            $this->set('isMulti', false);
        }
        $copyActivity = isset($this->companyConfigs['copy_in_activity_list']) && !empty($this->companyConfigs['copy_in_activity_list']) ?  1 : 0;
        $getHour = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeName['id']),
            'fields' => array('hour', 'minutes')
        ));
		$this->_getListEmployee();
        $this->set(compact('fillTimesheet', 'managerHour', 'holidays', 'taskNotAccessDeletes', 'activityNotAccessDeletes', 'checkFullDays', 'checkFullDayActivities', 'checkFullDayTasks', 'requestConfirm', 'requestConfirmInfo', 'typeSelect', 'listWorkingDays', 'workdays', 'activityFromTasks', 'projectFromTasks'));
        $this->set(compact('dataForecasts', 'lisTaskRequests','allowRequestRemain'));
        $this->set(compact('projectPhaseFromTasks', 'projectPartFromTasks', 'copyActivity', 'activateCopy', 'activityRequests', 'isManage', 'profit', 'activityForecasts', 'requestConfirmDate', 'requestConfirmName', 'employeeHasProject', 'profitHasProject', 'getDataByPath'));
        $this->set(compact('getHour', 'ratio'));
		$this->set(compact('listActivityDisplay', 'listTaskDisplay'));
	}
	public function getDataCopyForecast(){
		$result = false;
		$data = array();
		$message = '';
		extract($_GET); 
		if( !(empty($idEmp) || empty($idPc) || empty($start) || empty($end) )){
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($idEmp);
			if ($canWrite){
				$data = $this->_workloadFromTask($idEmp, $idPc, $start, $end);
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
		$data['lisTaskRequests'] = $lisTaskRequests;
		/* 
		* END get lisTaskRequests
		*/
		die(json_encode(array(
			'result' => $result,
			'data' => $data,
			'message' => $message,
		)));
	}

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function response($typeSelect = null, $validInAdmin = 'false') {
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityRequestConfirm');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTask');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true);
        }else{
            list($_start, $_end) = $this->_parseParamsMonth();
            $_start = $this->viewVars['_start'] = strtotime("01/01/".date('Y',$_start));
            $_end = $this->viewVars['_end'] = strtotime("12/31/".date('Y',$_end));
        }
        if (!($params = $this->_getProfitFollowDates($profiltId, $getDataByPath, $_start, $_end))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $avg = intval(($_start + $_end) / 2);
        // get all end date of employee
        $emp = $this->Employee->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employeeName['company_id']),
                                'fields' => array('id','end_date','start_date', 'hour', 'minutes')
                            ));
        $emp_end = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.end_date') : array();
        $emp_start = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.start_date') : array();
        $hourDayOfEmployees = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', array('{0}: {1}', '{n}.Employee.hour', '{n}.Employee.minutes')) : array();
        $dee = date("Y-m-d H:i:s", $_start);
        // End get all end date of employee
        /**
         * Holidays va cac ngay xin nghi
         */
        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($_start, $_end, $employeeName['company_id']);
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id']);
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        $requests = Set::combine($this->AbsenceRequest->find("all", array(
                            'recursive' => -1,
                            "conditions" => array(
                                // 'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                                'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => array_keys($employees))))
                        , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
        /**
         * Cac ngay lam viec trong tuan
         */
        //$workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        /**
         * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
         */
        $allDays = array();
        $listWorkingDays = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
        // Danh sach cac ngay lam viec
        $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
        if($typeSelect == 'week'){
            $numWeek = 1;
        }else{
            $numWeek = isset($listWeekOfMonths)?count($listWeekOfMonths):4;
        }
        $numDays = 0; //so ngay lam viec cua 1 tuan
        foreach($workdays as $key=>$workday):
            if($workday!=0){
                   $numDays++;
            }
        endforeach;
        $k = 1; $m =1;
        //Check : neu holiday khong nam trong list working day thi unset no
        foreach($holidays as $_key => $_val)
        {
            if(!isset($listWorkingDays[$_key]))
            {
                unset($holidays[$_key]);
            }
        }
        foreach($listWorkingDays as $key=>$listWorkingDay):
           if (array_key_exists($key,$holidays)){
              $allDays[$key] = $k;
           }else{
              $allDays[$key] = 'v-'.$k;
           }

           if($m%$numDays==0){$k++;}
           $m++;
        endforeach;
        // dem so ngay le cua tung tuan
        $tmp = array_count_values($allDays);
        $holidayNum = array();
        for($n=1;$n<=$numWeek+1;$n++){
            if(isset($tmp[$n])){
                $holidayNum[$n] = $tmp[$n];
            }
        }
        $this->ActivityRequest->Behaviors->attach('Containable');
        $activityRequests = Set::combine($this->ActivityRequest->find("all", array(
                            'fields' => array('id', 'activity_id', 'value', 'date', 'employee_id', 'task_id', 'status', 'value_hour'),
                            'contain' => array('Activity' => array('name'), 'ActivityTask' => array('name')),
                            "conditions" => array(
                                'NOT' => array('value' => 0),
                                'status' => array(-1, 0, 1, 2),
                                'date BETWEEN ? AND ?' => array(
                                    $this->viewVars['_start'], $this->viewVars['_end']),
                                'employee_id' => array_keys($employees))))
                        , '{n}.ActivityRequest.id', '{n}', '{n}.ActivityRequest.employee_id');
        $listStatus = $listTotalRequestOfEmployees = array();
        foreach($employees as $id => $employee){
            if (isset($activityRequests[$id])) {
                $checkHaved = 3;
                foreach ($activityRequests[$id] as $key => $request) {
                    if(!empty($request['ActivityTask']['id'])){
                        $activityTaskId[] = $request['ActivityTask']['id'];
                    }
                    if(!isset($listStatus[$request['ActivityRequest']['employee_id']])){
                        $listStatus[$request['ActivityRequest']['employee_id']] = array();
                    }
                    if(!in_array($request['ActivityRequest']['status'], $listStatus[$request['ActivityRequest']['employee_id']])||($checkHaved != $request['ActivityRequest']['status'])){
                        $listStatus[$request['ActivityRequest']['employee_id']][] = $request['ActivityRequest']['status'];
                    }
                    if(!isset($listTotalRequestOfEmployees[$request['ActivityRequest']['employee_id']])){
                        $listTotalRequestOfEmployees[$request['ActivityRequest']['employee_id']] = 0;
                    }
                    $listTotalRequestOfEmployees[$request['ActivityRequest']['employee_id']] += $request['ActivityRequest']['value'];
                $checkHaved = $request['ActivityRequest']['status'];
                }
            }
            // tuan da validated bang ngay nghi va ngay le
            $validatedAfter[$id] = array();
            if(isset($requests[$id])){
                foreach($requests[$id] as $requestValidated){
                  if(($requestValidated['response_am']=='validated')&&($requestValidated['response_am']=='validated')){
                        if(array_key_exists($requestValidated['date'],$allDays)){
                                array_push($validatedAfter[$id], $allDays[$requestValidated['date']]);
                        }
                  }
                }
            }
        }

        if($typeSelect === 'week'){
            $this->loadModel('ActivityRequestConfirm');
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array('recursive' => -1,
                    'group' => 'employee_id', 'fields' => array('employee_id', 'status', 'id'),
                     'conditions' => array('employee_id' => array_keys($employees), 'start' => $this->viewVars['_start'], 'end' => $this->viewVars['_end'])));
            $requestConfirms = Set::combine($requestConfirms, '{n}.ActivityRequestConfirm.employee_id', '{n}.ActivityRequestConfirm.status');
        } elseif($typeSelect === 'month') {
            $this->loadModel('ActivityRequestConfirm');
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'status', 'id', 'start'),
                'conditions' => array(
                    'employee_id' => array_keys($employees),
                    'start BETWEEN ? AND ?' => array($_start, $_end),
                    'end BETWEEN ? AND ?' => array($_start, $_end)
                )
            ));
            $requestConfirms = !empty($requestConfirms) ? Set::combine($requestConfirms, '{n}.ActivityRequestConfirm.start', '{n}.ActivityRequestConfirm.status', '{n}.ActivityRequestConfirm.employee_id') : array();
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
        }

        if(!empty($activityTaskId)){
            $activityTaskId = array_unique($activityTaskId);
            $activityTasks = ClassRegistry::init('ActivityTask')->find('list', array('recursive' => -1, 'fields' => array('activity_id'), 'conditions' => array('ActivityTask.id' => $activityTaskId)));
            foreach($activityTasks as $activityTask){
                $activityId[] = $activityTask;
            }
            if(!empty($activityId)){
                $activities = ClassRegistry::init('Activity')->find('list', array('recursive' => -1, 'fields' => array('id', 'short_name'), 'conditions' => array('Activity.id' => $activityId)));
            }

            foreach($employees as $id => $employee){
                if (isset($activityRequests[$id])) {
                    foreach ($activityRequests[$id] as $key => $request) {
                        if(!empty($request['ActivityRequest']['task_id'])){
                            foreach($activityTasks as $k => $activityTask){
                                if($k == $request['ActivityRequest']['task_id']){
                                    foreach($activities as $ks => $activity){
                                        if($activityTask == $ks){
                                            $activityRequests[$id][$key]['Activity']['name'] = $activity;
                                            $activityRequests[$id][$key]['Activity']['id'] = $ks;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        /**
         * Lay cac external cua employee
         */
        $this->loadModels('Employee');
        $listExternalOfEmps = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => array_keys($employees)),
            'fields' => array('id', 'external')
        ));
        /**
         * Save Data
         */
        if (!empty($this->data['id'])) {
            if($validInAdmin == 'true'){
                $this->data['id'] = unserialize($this->data['id']);
                $this->loadModel('CompanyEmployeeReference');
                $employees = $this->Employee->CompanyEmployeeReference->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id']),
                    'fields' => array('CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.employee_id')
                ));
                $employees = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('Employee.id' => $employees),
                    'fields' => array('id', 'fullname')
                ));
            }
            $newEmployees = array();
            $employees = array_intersect_key($employees, $this->data['id']);
			
			// Check current user can validate / reject timesheet
			$userRole = $this->employee_info['CompanyEmployeeReference']['role_id'];
			$e_canManager = 0; 
			if( $userRole == 2 || !empty( $this->employee_info['CompanyEmployeeReference']['control_resource'])){
				$e_canManager = 1;
			}
			if( $e_canManager == 0){
				$currentCompanyID = $this->employee_info['Company']['id'];
				$list_emp_info = $this->Employee->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => array_keys($employees),
						'company_id' => $this->employee_info['Company']['id'] // check company
					),
					'fields' => array('id','profit_center_id', 'company_id'),
				));
				$list_emp_info = !empty( $list_emp_info) ? (Set::combine($list_emp_info, '{n}.Employee.id','{n}.Employee') ) : array();
				foreach($employees as $e_id => $e_name){
					if( empty($list_emp_info[$e_id]) ) break;
					$emp = $list_emp_info[$e_id]; 
					$e_profit = !empty($emp['profit_center_id']) ? $emp['profit_center_id'] : '';
					$e_profits = $this->ProfitCenter->getPath($profit);
					if(!empty($e_profits)){
						$this->loadModel('ProfitCenterManagerBackup');
						//Get manager backup profit center
						$ls_backupManagers = $this->ProfitCenterManagerBackup->find('list', array(
							'recursive' => -1,
							'conditions' => array('profit_center_id' => $profit),
							'fields' => array('employee_id', 'employee_id')
						));
						$e_canManager = 0;
						foreach($e_profits as $p){
							if($p['ProfitCenter']['manager_id'] == $this->employee_info['Employee']['id'] || in_array($this->employee_info['Employee']['id'], $ls_backupManagers)){
								$e_canManager = 1;
								break;
							}
						}
					}
					if($e_canManager == 0) break;
				}
			}
			if( $e_canManager == 0){
				$this->Session->setFlash(__('Access denied', true), 'error');
				$this->redirect(array( 'controller' => 'activity_forecasts', 'action' => 'request'));
			}
			// END Check current user can validate / reject timesheet */
            if(!empty($this->data['Request']['selected'])){

                $pos = strpos($this->data['Request']['selected'], ',');
                if($pos){
                    $empSelects = explode(',',$this->data['Request']['selected']);
                }else{
                    $empSelects[0] = $this->data['Request']['selected'];
                }
                foreach($empSelects as $empSelect){
                    if(!empty($empSelect)){
                        $keyTmp = explode('-',$empSelect);
                        $newEmployees[$empSelect][0] = $keyTmp[0];
                        $newEmployees[$empSelect][1] = $keyTmp[1];
                        $newEmployees[$empSelect][2] = $keyTmp[1]+518400;
                    }
                }
            } else {
                $idOfEmployValid = !empty($employees) ? array_keys($employees) : 0;
                $idOfEmployValid = !empty($idOfEmployValid) ? $idOfEmployValid[0] : 0;
                if($typeSelect == 'week'){
                    $newEmployees[$idOfEmployValid . '-' . $_start] = array(
                        $idOfEmployValid, $_start, $_end
                    );
                } else {
                    foreach($listWeekOfMonths as $start => $end){
                        $newEmployees[$idOfEmployValid . '-' . $start] = array(
                            $idOfEmployValid, $start, $end
                        );
                    }
                }
            }
            if (!$employees) {

                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                if($validInAdmin == 'true'){
                    $this->redirect(array('action' => 'import_timesheet'));
                } else {
                    if( isset($this->params['url']['return']) ){
                        $this->redirect($this->params['url']['return']);
                    }
                    if($typeSelect == 'week'){
                         $this->redirect(array('action' => 'response','week', '?' => array('week' => date('W', $avg), 'year' => date('Y', $avg),'profit' => $profit['id'], 'get_path' => $getDataByPath ? 1 : 0)));
                    }elseif($typeSelect == 'month'){
                        $this->redirect(array('action' => 'response','month', '?' => array('month' => date('m', $_start), 'year' => date('Y', $_start),'profit' => $profit['id'], 'get_path' => $getDataByPath ? 1 : 0)));
                    }else{
                         $this->redirect(array('action' => 'response','year', '?' => array('month' => date('m', $_end), 'year' => date('Y', $_end),'profit' => $profit['id'], 'get_path' => $getDataByPath ? 1 : 0)));
                    }
                }
            } else {
                $saveStatus = $status = !empty($this->data['validated']) ? 2 : 1;
                $listEmployees = array_keys($employees);
                if($typeSelect == 'week'){
                    foreach (array_keys($employees) as $id) {
                        /**
                         * Kiem tra xem da co cot confirm trong table activity request confirm chua?
                         */
                         $confirms = $this->ActivityRequestConfirm->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'employee_id' => $id,
                                'company_id' => $employeeName['company_id'],
                                'start' => $_start,
                                'end' => $_end
                            ),
                            'fields' => array('id')
                         ));
                         $this->ActivityRequestConfirm->create();
                         if(!empty($confirms) && $confirms['ActivityRequestConfirm']['id']){
                            $this->ActivityRequestConfirm->id = $confirms['ActivityRequestConfirm']['id'];
                         }
                         $employeeValidate= $this->Session->read('Auth.Employee.fullname');
                        $savedConfirms = array(
                            'employee_id' => $id,
                            'company_id' => $employeeName['company_id'],
                            'start' => $_start,
                            'end' => $_end,
                            'status' => $status,
                            'employee_validate' => $employeeValidate
                        );
                         if($this->ActivityRequestConfirm->save($savedConfirms)){
							/* Update TJM & cost if validated timesheet 
								#517 
								update inside _cacheRequest()
								use for: 
									Auto validate
									Validate on timesheet screen
									validate on manager screen
								
							*/
                            $this->_cacheRequest($_start, $_end, $id, $status, $profit['id'], true, $employeeName['company_id'], $employeeValidate);
                            if($status == 2){
                                $absens = Set::combine($this->AbsenceRequest->find("all", array(
                                    'recursive' => -1,
                                    "conditions" => array(
                                        'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => array_keys($employees)))),
                                    '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
                                $tmps = array();
                                foreach($absens as $absen){
                                    foreach($absen as $abs){
                                        if($abs['response_am'] == 'waiting'){
                                            $tmps['response_am'] = 'rejetion';
                                            $this->AbsenceRequest->id = $abs['id'];
                                            $this->AbsenceRequest->save($tmps);
                                        }
                                        if($abs['response_pm'] == 'waiting'){
                                            $tmps['response_pm'] = 'rejetion';
                                            $this->AbsenceRequest->id = $abs['id'];
                                            $this->AbsenceRequest->save($tmps);
                                        }
                                    }
                                }
                                $this->_deleteCacheContextMenu();
                            }
                         }
                    }
                } elseif($typeSelect == 'month'){
                    $employeeValidate= $this->Session->read('Auth.Employee.fullname');
                    foreach ($newEmployees as $newEmployee) {
                        $this->_cacheRequestMonth($newEmployee[1], $newEmployee[2], $newEmployee[0], $status, $profit['id'], true, $employeeName['company_id'], $employeeValidate);
                        if($status == 2){
                            $absens = Set::combine($this->AbsenceRequest->find("all", array(
                                'recursive' => -1,
                                "conditions" => array(
                                    'date BETWEEN ? AND ?' => array($newEmployee[1], $newEmployee[2]), 'employee_id' => array_keys($employees)))),
                                '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
                            $tmps = array();
                            foreach($absens as $absen){
                                foreach($absen as $abs){
                                    if($abs['response_am'] == 'waiting'){
                                        $tmps['response_am'] = 'rejetion';
                                        $this->AbsenceRequest->id = $abs['id'];
                                        $this->AbsenceRequest->save($tmps);
                                    }
                                    if($abs['response_pm'] == 'waiting'){
                                        $tmps['response_pm'] = 'rejetion';
                                        $this->AbsenceRequest->id = $abs['id'];
                                        $this->AbsenceRequest->save($tmps);
                                    }
                                }
                            }
                            $this->_deleteCacheContextMenu();
                        }
                    }
                }
                // $this->Session->setFlash(__('The data has been saved.', true), 'success');
                $status = !empty($this->data['validated']) ? __('validated', true) : __('rejected', true);
                $employees = array_values($this->ActivityRequest->Employee->find('list', array(
                            'fields' => array('email', 'email'), 'recursive' => -1, 'conditions' => array('email_receive' => true, 'id' => array_keys($employees)))));
                //if ($employees) {
                //  $this->set('isValidated', !empty($this->data['validated']));
                //  $this->_sendEmail($employees, sprintf(__('[Azuree] Timesheet has been %s.', true), $status), 'activity_response');
                //}
                $avg = intval(($_start + $_end)/2);
                if($validInAdmin == 'true'){
                    $address = '';
                } else {
                    if( isset($this->params['url']['return']) ){
                        $this->redirect($this->params['url']['return']);
                    }
                    if($typeSelect=='week'){
                         $address = array('action' => 'response','week', '?' => array('week' => date('W', $avg), 'year' => date('Y', $avg),'profit' => $profit['id'],'get_path' => $getDataByPath ? 1 : 0));
                    }elseif($typeSelect=='month'){
                        $address = array('action' => 'response','month', '?' => array('month' => date('m', $_start), 'year' => date('Y', $_start),'profit' => $profit['id'],'get_path' => $getDataByPath ? 1 : 0));
                    }else{
                         $address = array('action' => 'response','year', '?' => array('month' => date('m', $avg), 'year' => date('Y', $avg),'profit' => $profit['id'],'get_path' => $getDataByPath ? 1 : 0));
                    }
                }
                $this->_saveAfterValidateAndReject($_start, $_end,  $profit['id'], $saveStatus, $listEmployees, $address, $typeSelect, $this->data['validated'], $employees, $validInAdmin);

            }
        }
        $this->loadModel('CompanyConfig');
        $showAllPicture = $this->CompanyConfig->find('first', array(
            'recursive' => 1,
            'conditions' => array(
                'company' => $employeeName['company_id'],
                'cf_name' => 'display_picture_all_resource'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showAllPicture = !empty($showAllPicture) ? $showAllPicture['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('numDays','listStatus','numWeek','listWorkingDays', 'typeSelect', 'profit', 'paths', 'employeeName', 'requests', 'employees', 'constraint', 'activityRequests', 'holidays', 'absences', 'requestConfirms', 'workdays', 'getDataByPath', 'listWeekOfMonths', 'listExternalOfEmps', 'showAllPicture', 'managerHour', 'hourDayOfEmployees'));
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
        // Comment phan phase status de update len pro site ng�y 7/1/2014...chi pro site moi comment
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
        $capacityOfMultiResources = array();
        // $capacityOfMultiResources = $this->EmployeeMultiResource->find('all', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'date BETWEEN ? AND ?' => array($_start, $_end),
        //         'employee_id' => array_keys($employees),
        //         'NOT' => array('date' => array_keys($holidays))
        //     ),
        //     'fields' => array('employee_id', 'SUM(value) as value'),
        //     'group' => array('employee_id')
        // ));
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

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function manage() {
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
        list($_start, $_end) = $this->_parseParams();
        if (!($params = $this->_getProfits($profiltId))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $emp = $this->Employee->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employeeName['company_id']),
                                'fields' => array('id','end_date','start_date')
                            ));
        $emp_end = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.end_date') : array();
        $emp_start = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.start_date') : array();
        $this->_parse($_start, $_end, $employeeName, $employees, $profit['id']);
        $this->loadModel('ActivityRequestConfirm');
        $requestConfirms = $this->ActivityRequestConfirm->find('list', array('recursive' => -1,
            'group' => 'employee_id', 'fields' => array('employee_id', 'status'),
            'conditions' => array('employee_id' => array_keys($employees), 'status' => array(0, 2),
                'start' => $this->viewVars['_start'], 'end' => $this->viewVars['_end'])));

        $this->set(compact('profit', 'paths', 'employeeName', 'requestConfirms'));
    }

    /**
     * manages
     *
     * @return void
     * @access public
     */
    function manages($typeSelect=null) {
        $typeSelect = ($typeSelect==null)?'week':$typeSelect;
        $this->set('typeSelect',$typeSelect);
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
           if($typeSelect =='week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect =='month'){
            $this->loadModel('ActivitySetting');
            $showFullDay = $this->ActivitySetting->find('first',array('fields' => 'show_full_day_in_month'));
            $showFullDay=$showFullDay['ActivitySetting']['show_full_day_in_month']?false:true;
            list($_start, $_end) = $this->_parseParamsMonth($showFullDay);
        }else{
            list($_start, $_end) = $this->_parseParamsMonth();
            $_start = $this->viewVars['_start'] = strtotime("01/01/".date('Y',$_start));
            $_end = $this->viewVars['_end'] = strtotime("12/31/".date('Y',$_end));
        }
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        if($typeSelect =='year') $getDataByPath = false;
        if (!($params = $this->_getProfitFollowDates($profiltId,$getDataByPath, $_start, $_end))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $emp = $this->Employee->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employeeName['company_id']),
                                'fields' => array('id','end_date','start_date')
                            ));
        $emp_end = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.end_date') : array();
        $emp_start = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.start_date') : array();
        $getDatas = $this->_workloadFromTaskForecasts($employees, $_start, $_end, 1, $typeSelect, $profit['id']);
        $workloads = $getDatas['results'];
        $activities = $getDatas['activities'];
        $projects = $getDatas['projects'];
        $projectPhases = $getDatas['projectPhases'];
        $projectParts = $getDatas['projectParts'];
        $allActivies = !empty($getDatas['allActivities']) ? $getDatas['allActivities'] : array();
        $allActivies = !empty($allActivies) ? Set::combine($allActivies, '{n}.Activity.id', '{n}.Activity') : array();
        $families = !empty($getDatas['families']) ? $getDatas['families'] : array();

        $this->loadModels('ActivityRequestConfirm', 'Profile');
        $requestConfirms = $this->ActivityRequestConfirm->find('list', array('recursive' => -1,
            'group' => 'employee_id', 'fields' => array('employee_id', 'status'),
            'conditions' => array('employee_id' => array_keys($employees), 'status' => array(0, 2),
                'start' => $this->viewVars['_start'], 'end' => $this->viewVars['_end'])));
        $this->_parse($_start, $_end, $employeeName, $employees, $profit['id']);

        $this->set('projectStatuses', ClassRegistry::init('ProjectStatus')->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'name', 'status')
        )));

        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $companyName = $this->employee_info['Company']['company_name'];
        $this->loadModel('CompanyConfig');
        $showAllPicture = $this->CompanyConfig->find('first', array(
            'recursive' => 1,
            'conditions' => array(
                'company' => $employeeName['company_id'],
                'cf_name' => 'display_picture_all_resource'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showAllPicture = !empty($showAllPicture) ? $showAllPicture['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('profit', 'paths', 'employeeName', 'requestConfirms', 'workloads', 'activities', 'projects', 'projectPhases', 'projectParts', 'allActivies', 'families', 'profiles', 'companyName', 'showAllPicture'));
        if($typeSelect=='month'){
            //$dayWorks = $this->_showDayInMonth(date('M', $_start),date('Y', $_start));
            /**
             * Cac ngay lam viec trong tuan
             */
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
    }
    /**
     * my_diary
     *
     * @return void
     * @access public
     */
    function my_diary($typeSelect=null) {
		// Apply new design for my diary
		// if (isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']) {
            // if (!((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))) {
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview = trim(str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_') . '_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/' . $value;
                }
                if (isset($_url_param['url']))
                    unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            // }
        // }
        $typeSelect= ($typeSelect==null)?'week':$typeSelect;
        $this->set('typeSelect',$typeSelect);
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
         if($typeSelect =='week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect =='month'){
            $this->loadModel('ActivitySetting');
            $showFullDay = $this->ActivitySetting->find('first',array('fields' => 'show_full_day_in_month'));
            $showFullDay=$showFullDay['ActivitySetting']['show_full_day_in_month']?false:true;
            list($_start, $_end) = $this->_parseParamsMonth($showFullDay);
        }else{
            list($_start, $_end) = $this->_parseParamsMonth();
            $_start = $this->viewVars['_start'] = strtotime("01/01/".date('Y',$_start));
            $_end = $this->viewVars['_end'] = strtotime("12/31/".date('Y',$_end));
        }
        if (!($params = $this->_getProfitMyDiarys($profiltId))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $getDatas = $this->_workloadFromTaskForecasts($employees, $_start, $_end,1,$typeSelect);
        $workloads = $getDatas['results'];
        $activities = $getDatas['activities'];
        $projects = $getDatas['projects'];
        $projectPhases = $getDatas['projectPhases'];
        $projectParts = $getDatas['projectParts'];
        $allActivies = !empty($getDatas['allActivities']) ? $getDatas['allActivities'] : array();
        $allActivies = !empty($allActivies) ? Set::combine($allActivies, '{n}.Activity.id', '{n}.Activity') : array();
        $families = !empty($getDatas['families']) ? $getDatas['families'] : array();

        $this->_parse($_start, $_end, $employeeName, $employees, $profit['id']);
        $this->loadModels('ActivityRequestConfirm', 'Profile');
        $requestConfirms = $this->ActivityRequestConfirm->find('list', array('recursive' => -1,
            'group' => 'employee_id', 'fields' => array('employee_id', 'status'),
            'conditions' => array('employee_id' => array_keys($employees), 'status' => array(0, 2),
                'start' => $this->viewVars['_start'], 'end' => $this->viewVars['_end'])));
        $this->set('projectStatuses', ClassRegistry::init('ProjectStatus')->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'name', 'status')
        )));
        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id'], $employeeName['id']);
        $companyName = $this->employee_info['Company']['company_name'];
        $this->loadModel('CompanyConfig');
        $showAllPicture = $this->CompanyConfig->find('first', array(
            'recursive' => 1,
            'conditions' => array(
                'company' => $this->employee_info['Company']['id'],
                'cf_name' => 'display_picture_all_resource'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showAllPicture = !empty($showAllPicture) ? $showAllPicture['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('profit', 'paths', 'employeeName', 'requestConfirms', 'workloads', 'activities', 'projects', 'projectPhases', 'projectParts', 'allActivies', 'families', 'profiles', 'companyName', 'absences', 'showAllPicture'));
        if($typeSelect=='month'){
            //$dayWorks = $this->_showDayInMonth(date('M', $_start),date('Y', $_start));
            /**
             * Cac ngay lam viec trong tuan
             */
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
        if( !$this->isMobile() ){
            $this->set('isDiary',1);
            $this->render('manages');
        }
    }
   /**
     * export_my_diary
     *
     * @return void
     * @access public
     */
    function export_my_diary($typeSelect=null) {
        $this->layout = '';
        $typeSelect= ($typeSelect==null)?'week':$typeSelect;
        $this->set('typeSelect',$typeSelect);
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
         if($typeSelect =='week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect =='month'){
            $this->loadModel('ActivitySetting');
            $showFullDay = $this->ActivitySetting->find('first',array('fields' => 'show_full_day_in_month'));
            $showFullDay=$showFullDay['ActivitySetting']['show_full_day_in_month']?false:true;
            list($_start, $_end) = $this->_parseParamsMonth($showFullDay);
        }else{
            list($_start, $_end) = $this->_parseParamsMonth();
            $_start = $this->viewVars['_start'] = strtotime("01/01/".date('Y',$_start));
            $_end = $this->viewVars['_end'] = strtotime("12/31/".date('Y',$_end));
        }
        if (!($params = $this->_getProfitMyDiarys($profiltId))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $getDatas = $this->_workloadFromTaskForecasts($employees, $_start, $_end,1,$typeSelect);
        $workloads = $getDatas['results'];
        $activities = $getDatas['activities'];
        $projects = $getDatas['projects'];
        $projectPhases = $getDatas['projectPhases'];
        $projectParts = $getDatas['projectParts'];

        $this->_parse($_start, $_end, $employeeName, $employees, $profit['id']);
        $this->loadModel('ActivityRequestConfirm');
        $requestConfirms = $this->ActivityRequestConfirm->find('list', array('recursive' => -1,
            'group' => 'employee_id', 'fields' => array('employee_id', 'status'),
            'conditions' => array('employee_id' => array_keys($employees), 'status' => array(0, 2),
                'start' => $this->viewVars['_start'], 'end' => $this->viewVars['_end'])));
        $urlExport =str_replace('my_diary','export_my_diary',$_SERVER['REQUEST_URI']);
        $this->set(compact('urlExport','profit', 'paths', 'employeeName', 'requestConfirms', 'workloads', 'activities', 'projects', 'projectPhases', 'projectParts'));
       if($typeSelect=='month'){
            $dayWorksConfigRoundWeek = $this->_workingDayFollowConfigAdmin($_start, $_end, $getDatas['workdays']);
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
        $this->loadModel('AbsenceRequest');
        $absenHaft = $this->AbsenceRequest->find('all',array('conditions'=>array(
                    'AbsenceRequest.employee_id'=>$employeeName['id'],
                    'AbsenceRequest.date BETWEEN ? AND ?' => array($_start, $_end),
                    'OR'=>array(
                       array('AbsenceRequest.response_am'=>'validated','AbsenceRequest.response_pm <>'=>'validated'),
                        array('AbsenceRequest.response_am <>'=>'validated','AbsenceRequest.response_pm'=>'validated')
                    )
                ),
                'fields'=>array('AbsenceRequest.date','AbsenceRequest.response_am','AbsenceRequest.response_pm','AbsenceAm.name',
                                'AbsencePm.name','AbsenceRequest.employee_id')
        ));
        $absenFull = $this->AbsenceRequest->find('all',array('conditions'=>array(
                    'AbsenceRequest.employee_id'=>$employeeName['id'],
                    'AbsenceRequest.date BETWEEN ? AND ?' => array($_start, $_end),
                    'AbsenceRequest.response_am'=>'validated',
                    'AbsenceRequest.response_pm'=>'validated'
                ),
                'fields'=>array('AbsenceRequest.date','AbsenceRequest.response_am','AbsenceRequest.response_pm','AbsenceAm.name',
                                'AbsencePm.name','AbsenceRequest.employee_id')
        ));
         $this->set(compact('absenHaft','absenFull'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update($profitId = null) {
        $this->loadModel('ActivityRequest');
        $this->ActivityForecast->cacheQueries = false;
        $this->layout = false;
        $success = $total = 0;

        if (($params = $this->_getProfits($profitId))) {
            list(,, $employees, $employeeName) = $params;
        }
        $this->_parseParams();
        if ($params && !empty($this->data) && (empty($this->data['request'])
                || (!empty($this->data['request']) && !empty($this->data['model'])))) {
            if (empty($this->data['request'])) {
                list($success, $total) = $this->_remove($employees);
            } else {
                $request = $this->data['request'];
                $model = $this->data['model'];
                unset($this->data['request'], $this->data['model']);
                $editable = true;
                foreach ($this->data as &$post) {
                    foreach ($post as &$data) {
                        $total++;
                        if (!empty($data['date']) && isset($data['employee_id']) && isset($employees[$data['employee_id']])) {

                            if (!$this->_checkEditable($data['employee_id'])) {
                                $editable = false;
                                continue;
                            }

                            $last = $this->ActivityForecast->find('first', array('recursive' => -1, 'fields' => array(
                                    'id', 'activity_am', 'activity_pm', 'am_model', 'pm_model'),
                                'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'])));
                            if ($last) {
                                $last = array_shift($last);
                                $this->ActivityForecast->id = $last['id'];
                            } else {
                                $this->ActivityForecast->create();
                            }
                            foreach (array('am', 'pm') as $type) {
                                if (!empty($data[$type])) {
                                    if(is_numeric($request)){
                                        $data['activity_' . $type] = $request;
                                        $data[$type . '_model'] = ucfirst($model);
                                    } else {
                                        $split = explode('-', $request);
                                        $data['activity_' . $type] = isset($split[1]) ? $split[1] : 0;
                                        $data[$type . '_model'] = isset($split[0]) ? ucfirst($split[0]) : 'Task';
                                    }
                                }
                                if (($last && empty($data[$type]))) {
                                    $last['activity_' . $type] = '0';
                                }
                                unset($data[$type]);
                            }
                            if (($data['result'] = (bool) $this->ActivityForecast->save($data))) {
                                $success++;
                            }
                        }
                    }
                }
            }
            if ($success == $total) {
                // $this->Session->setFlash(__('The data has been saved.', true), 'success');
            } else {
                if (isset($editable) && $editable === false) {
                    $this->Session->setFlash(__('Some data could not be saved because the time sheets have been requested."', true), 'warning');
                } else {
                    $this->Session->setFlash(__('Some data could not be saved because invalidate."', true), 'warning');
                }
            }
        } else {
            if (isset($editable) && $editable === false) {
                $editable = $editable == 0 ? __('Requested', true) : __('Validated', true);
                $this->Session->setFlash(sprintf(__('The request is invalid because the time sheet is "%s"', true), $editable), 'error');
            } else {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            }
        }
    }

    /**
     * Chia tuan theo thang.
     * Phan chia cac tuan tu thu 2 -> chu nhat cho 1 thang
     */
    private function _splitWeekOfMonth($start = null, $end = null){
        $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $checkDay = strtolower(date('l', $start));
                if($checkDay === 'monday'){
                    $_end = strtotime('next sunday', $start);
                    $results[$start] = $_end;
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return $results;
    }

    /**
     * Luu confirm nhieu tuan lien tiep
     */
    private function _saveMultipleConfirmWeek($weeks = null, $employee_id = null, $company_id = null, $employeeValidation = null, $status, $skips = array()){
        $this->loadModel('ActivityRequestConfirm');
        if(!empty($weeks)){
            foreach($weeks as $start => $end){
                //if(!in_array($start, $skips)){
                    // tuan dang o trang thai reject or dang cho send for validation. bo qua cac tuan nay
                //} else {
                    $confirms = $this->ActivityRequestConfirm->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'employee_id' => $employee_id,
                            'company_id' => $company_id,
                            'start' => $start,
                            'end' => $end
                        ),
                        'fields' => array('id')
                     ));
                     $this->ActivityRequestConfirm->create();
                     if(!empty($confirms) && $confirms['ActivityRequestConfirm']['id']){
                        $this->ActivityRequestConfirm->id = $confirms['ActivityRequestConfirm']['id'];
                     }
                     $savedConfirms = array(
                        'employee_id' => $employee_id,
                        'company_id' => $company_id,
                        'start' => $start,
                        'end' => $end,
                        'status' => $status,
                        'employee_validate' => $employeeValidation
                     );
                     $this->ActivityRequestConfirm->save($savedConfirms);
                //}
            }
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function confirm_request($typeSelect = null, $profit_id = null) {
        // pr($this->params);die;
        $this->loadModel('ActivityRequestConfirm');
        $this->loadModel('ActivityRequestConfirmMonth');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ProfitCenter');
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        $ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
        $pcPath = isset($_GET['get_path']) ? $_GET['get_path'] : 0;
        list($isManage, $employeeName) = $this->_getProfitEmployee($pcPath);
		//check permission
		list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($employeeName['id']);
		if( empty( $canWrite)){
			$this->Session->setFlash(__('Access denied', true), 'error');
			$this->redirect(array( 'controller' => 'activity_forecasts', 'action' => 'request'));
		}
		// end check permission
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true);
        }
		// Create by VN
		// Check absence, if absence not validated return false
		$abse = $this->_checkAbsenceWaiting($_start, $_end, $employeeName['id']);
		if($abse > 0){
			$this->Session->setFlash(__('Absence has to be validated before sending the timesheet', true), 'error');
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		
        $data = array(
            'employee_id' => $employeeName['id'],
            'start' => $_start,
            'end' => $_end
        );
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        $listWorkingDays = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
        /**
         * Tim nhung record  trong khoang start date den end date.
         * Kiem tra record trong activity request thuoc ngay nghi thi xoa no di
         */
        $tmpStartDate = $_start;
        $listRequestDeletes = $dayOffs = array();
        while($tmpStartDate <= $_end){
            $checkDay = strtolower(date('l', $tmpStartDate));
            if(!empty($workdays[$checkDay]) && $workdays[$checkDay] == 0){
                $tmp = $this->ActivityRequest->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('date' => $tmpStartDate, 'employee_id' => $employeeName['id']),
                    'fields' => array('id')
                ));
                if(!empty($tmp) && $tmp['ActivityRequest']['id']){
                    $listRequestDeletes[$tmp['ActivityRequest']['id']] = $tmp['ActivityRequest']['id'];
                }
            }
            $tmpStartDate = mktime(0, 0, 0, date("m", $tmpStartDate), date("d", $tmpStartDate)+1, date("Y", $tmpStartDate));
        }
        if(!empty($listRequestDeletes)){
            $this->ActivityRequest->deleteAll(array('ActivityRequest.id' => $listRequestDeletes), false);
        }
        $activityRequest = $capacity = 0;
        if($managerHour){
            $_activityRequests = $this->ActivityRequest->find("all", array(
                    'fields' => 'value_hour',
                    'recursive' => -1, "conditions" => array('date BETWEEN ? AND ?' => array(
                            $_start, $_end), 'employee_id' => $employeeName['id'])));
            $activityRequest = 0;
            if(!empty($_activityRequests)){
                foreach($_activityRequests as $_activityRequest){
                    $dx = $_activityRequest['ActivityRequest'];
                    $tHour = explode(':', $dx['value_hour']);
                    $tHour = $tHour[0] * 60 + $tHour[1];
                    $activityRequest += $tHour;
                }
            }
        } else {
            $activityRequest = Set::classicExtract($this->ActivityRequest->find("first", array(
                    'fields' => 'SUM(value) as value',
                    'recursive' => -1,
                    'conditions' => array(
                        'date BETWEEN ? AND ?' => array($_start, $_end),
                        'employee_id' => $employeeName['id']
                    )
                )), '0.value');
        }
        /**
         * get external of employee
         */
        $getExternal = ClassRegistry::init('Employee')->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeName['id']),
            'fields' => array('external', 'hour', 'minutes')
        ));
        $gHour = !empty($getExternal) && !empty($getExternal['Employee']['hour']) ? $getExternal['Employee']['hour'] : 0;
        $gMinutes = !empty($getExternal) && !empty($getExternal['Employee']['minutes']) ? $getExternal['Employee']['minutes'] : 0;
        $getExternal = !empty($getExternal) && !empty($getExternal['Employee']['external']) ? $getExternal['Employee']['external'] : 0;

        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($_start, $_end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            if($workdays[strtolower(date("l", $time))] == 0){
                unset($holidays[$time]);
            }
        }
        $dayMaps = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $requests = Set::combine(ClassRegistry::init('AbsenceRequest')->find("all", array(
                            'recursive' => -1,
                            "conditions" => array(
                                'or' => array('response_am' => 'validated', 'response_pm' => 'validated'),
                                'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $employeeName['id'])))
                        , '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
        if($managerHour){
            $totalHourOfDay = $gHour * 60 + $gMinutes;
            foreach ($listWorkingDays as $day => $time) {
                $capacity += $totalHourOfDay;
                foreach (array('am', 'pm') as $type) {
                    $tHour = round($totalHourOfDay/2, 0);
                    if (isset($holidays[$time][$type])) {
                        $capacity -= $tHour;
                    } else if (!empty($requests[$time]['absence_' . $type]) && $requests[$time]['response_' . $type] === 'validated') {
                        $capacity -= $tHour;
                    }
                }
            }
        } else {
            foreach ($listWorkingDays as $day => $time) {
                $capacity += floatval($workdays[strtolower(date("l", $time))])*$ratio;
                //2016-11-04 ratio
                foreach (array('am', 'pm') as $type) {
                    if (isset($holidays[$time][$type])) {
                        $capacity -= 0.5*$ratio;
                    } else if (!empty($requests[$time]['absence_' . $type]) && $requests[$time]['response_' . $type] === 'validated') {
                        $capacity -= 0.5*$ratio;
                    }
                }
            }
        }

        $manage_multiple_resource = isset($this->companyConfigs['manage_multiple_resource']) && !empty($this->companyConfigs['manage_multiple_resource']) ?  true : false;
        if($manage_multiple_resource && $getExternal == 2){
            $this->loadModels('AbsenceRequest', 'AbsenceRequestConfirm');
            $this->AbsenceRequest->deleteAll(array(
                'date BETWEEN ? AND ?' => array($_start, $_end),
                'employee_id' => $employeeName['id']
            ), false);
            $this->AbsenceRequestConfirm->deleteAll(array('employee_id' => $employeeName['id'], 'start' => $_start, 'end' => $_end), false, false);
            $activityRequest = $capacity = 0;
        }
        $sendTimesheetPartially = !$managerHour && isset($this->companyConfigs['send_timesheet_partially_filled']) && !empty($this->companyConfigs['send_timesheet_partially_filled']) ?  1 : 0;
        if($sendTimesheetPartially){
            $activityRequest = $capacity = 0;
        }
        $capacity = number_format($capacity, 2);
        $activityRequest = number_format($activityRequest, 2);
		$fillMoreThanCapacity = !$managerHour && isset($this->companyConfigs['fill_more_than_capacity_day']) && !empty($this->companyConfigs['fill_more_than_capacity_day']) ?  1 : 0;
        if (!$fillMoreThanCapacity && $activityRequest != $capacity ) {
            //$this->Session->setFlash(sprintf(__('Absence not yet validation or you have to declare %s days before sending your timesheet', true), $capacity - $activityRequest), 'error');
            $this->Session->setFlash(__('Absence not yet validated, you cannot send your timesheet', true), 'error');
            $this->_backProfitEmployee($isManage, $_start, $_end, $typeSelect);
        }
        $haveSaved = false;
        if($typeSelect === 'week'){
            /**
             * Kiem tra tuan nay co co ton tai trong activity request confirm hay chua?
             */
            $last = $this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status'),
                'conditions' => $data));
            /**
             * Neu co roi thi tien hanh update khong thi tao moi
             */
            if ($last) {
                $last = array_shift($last);
                $this->ActivityRequestConfirm->id = $last['id'];
                //if ($last['status'] == 0 || $last['status'] == 2) { // 1 la reject
                if ($last['status'] == 2) {
                    $this->Session->setFlash(__('You can not change the status, because it already sent', true), 'error');
                    $this->_backProfitEmployee($isManage, $_start, $_end, $typeSelect);
                    $this->_deleteCacheContextMenu();
                }
            } else {
                $this->ActivityRequestConfirm->create();
            }
            $data['status'] = 0;
            $data['company_id'] = $employeeName['company_id'];
            if ($this->ActivityRequestConfirm->save($data)) {
                $haveSaved = true;
            }
        } elseif($typeSelect == 'month'){
            /**
             * Kiem tra thang nay co co ton tai trong activity request confirm hay chua?
             */
            $last = $this->ActivityRequestConfirmMonth->find('first', array('recursive' => -1, 'fields' => array('id', 'status'),
                'conditions' => $data));
            /**
             * Neu co roi thi tien hanh update khong thi tao moi
             */
            if ($last) {
                $last = array_shift($last);
                $this->ActivityRequestConfirmMonth->id = $last['id'];
                //if ($last['status'] == 0 || $last['status'] == 2) { // 1 la reject
                if ($last['status'] == 2) {
                    $this->Session->setFlash(__('You can not change the status, because it already sent', true), 'error');
                    $this->_backProfitEmployee($isManage, $_start, $_end, $typeSelect);
                    $this->_deleteCacheContextMenu();
                }
            } else {
                $this->ActivityRequestConfirmMonth->create();
            }
            $data['status'] = 0;
            $data['company_id'] = $employeeName['company_id'];
            if ($this->ActivityRequestConfirmMonth->save($data)) {
                $haveSaved = true;
            }
        }
        if ($haveSaved == true) {
            $this->_cacheRequestWhenSendForValidation($_start, $_end, $employeeName['id'], $data['status'], $data['company_id'], $typeSelect);
            $profit = Set::classicExtract(ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array('recursive' => -1,
                                'conditions' => array('employee_id' => $employeeName['id']))), 'ProjectEmployeeProfitFunctionRefer.profit_center_id');
            // Get mangager PC
			$list_manager_pc = $this->get_manager_pc($profit);
			
			if(empty($list_manager_pc)){
				// Get manager parent PC
				$has_manager = 0;
				$tmp_profit = $profit;
				while( $has_manager == 0 ){
					$parent_profit = $this->ProfitCenter->find('list', array(
						'recursive' => -1, 
						'conditions' => array(
							'id' => $tmp_profit,
							'parent_id IS NOT NULL'
						),
						'fields' => array('id', 'parent_id'),
					));
					$list_manager_pc = $this->get_manager_pc($parent_profit);
					
					if(empty($list_manager_pc) && !empty($parent_profit)){
						$tmp_profit = $parent_profit;
					}else{
						$has_manager = 1;
					}
				}
			}
			
			if (empty($list_manager_pc)) {
				// Get admin company
				foreach (array(6, 2) as $role) {
					$admin_company = ClassRegistry::init('CompanyEmployeeReference')->find('list', array(
						'recursive' => -1, 'fields' => array('employee_id', 'employee_id'),
						'conditions' => array('role_id' => $role, 'CompanyEmployeeReference.company_id' => $employeeName['company_id'])));
					
					if ($admin_company) {
						break;
					}
				}
				$list_manager_pc = $admin_company;
			}
			$list_managers = array_values($this->AbsenceRequest->Employee->find('list', array(
								'fields' => array('email', 'email'), 'recursive' => -1, 'conditions' => array('email_receive' => true, 'id' => $list_manager_pc))));
			/**
             * Kiem tra manager cua profit center nay neu co check auto validate timesheet thi goi ham de valiadte timesheet
             */
            $auto = $this->ProfitCenter->checkAutoValidOfProfitCenter($profit_id);
            $isAuto = $auto['timesheet'] || ($employeeName['id'] == $this->employee_info['Employee']['id'] && !empty($this->employee_info['Employee']['auto_by_himself']));
            $result = false;
			if ($list_managers) {
                //$typeSelect;
                $this->set(compact('employeeName', 'profit', 'typeSelect'));
                if( !$isAuto ){
                    if($auto['email_receive'] == 1){
                        $to = array_shift($list_managers);
						/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug) */
						$result = $this->_z0GSendEmail($to, null, $list_managers, __('[Azuree] Timesheet has to be validated.', true), 'activity_request');
                    }
                }
            }
            if( $isAuto ){
                if( !$auto['manager'] ){
                    $auto['manager'] = $this->ProfitCenter->getParentManager($profit_id);
                }
                $employeeName['manager'] = $auto['manager'];
                $this->_autoValidate($typeSelect, $employeeName, $_start, $_end, $profit_id);

                // $this->Session->setFlash(__('Saved', true), 'success');
                goto _redirection_;
            }
            if ($result) {
                $this->Session->setFlash(__('The request validate has been sent.', true), 'success');
            } else {
                $this->Session->setFlash(__('The request validate has been sent, but email message could not be sent to your manager.', true), 'warning');
            }
        } else {
            $this->Session->setFlash(__('The request validate could not sent, please try again', true), 'error');
        }
        _redirection_:
        if( isset($_GET['return']) ){
            $this->redirect($_GET['return']);
        }
        if($typeSelect === 'week'){
            $this->_backProfitEmployee($isManage, $_start, $_end, $typeSelect);
        } else {
            $this->_backProfitEmployee($isManage, $_start, $_end, $typeSelect);
        }

    }
	function get_manager_pc($profit){
		$this->loadModels('ProfitCenter', 'ProfitCenterManagerBackup');
		$manager_pc = $this->ProfitCenter->find('list', array(
			'recursive' => -1, 
			'conditions' => array('id' => $profit, 'manager_id IS NOT NULL'),
			'fields' => array('manager_id'),
		));

		$manager_pc_bk = $this->ProfitCenterManagerBackup->find('list', array(
		   'recursive' => -1, 
		   'conditions' => array(
				'profit_center_id' => $profit,
		   ),
		   'fields' => array('employee_id'),
		));
		
		$list_manager_pc = array_merge($manager_pc, $manager_pc_bk);
		
		return $list_manager_pc;
	}
    private function _checkAbsenceWaiting($start, $end, $employ){
		$this->loadModel('AbsenceRequest');
        $requestQuery = $this->AbsenceRequest->find("count", array(
			'recursive'     => -1,
			"conditions"    => array(
				'date BETWEEN ? AND ?' => array($start, $end),
				'employee_id' => $employ,
				'OR' => array(
					'response_am' => 'waiting',
					'response_pm' => 'waiting'
				),
			))
        );
		return $requestQuery;
	}
    private function _autoValidate($typeSelect = 'week', $employeeName = array(), $_start = null, $_end = null, $profit_id = null){
        $this->loadModels('ActivityRequestConfirm', 'ActivityRequest', 'AbsenceRequest');
        $saveStatus = $status = 2;
        if($typeSelect == 'week'){
            /**
             * Kiem tra xem da co cot confirm trong table activity request confirm chua?
             */
             $confirms = $this->ActivityRequestConfirm->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employeeName['id'],
                    'company_id' => $employeeName['company_id'],
                    'start' => $_start,
                    'end' => $_end
                ),
                'fields' => array('id')
             ));
             $this->ActivityRequestConfirm->create();
             if(!empty($confirms) && $confirms['ActivityRequestConfirm']['id']){
                $this->ActivityRequestConfirm->id = $confirms['ActivityRequestConfirm']['id'];
             }
             $employeeValidate = $employeeName['manager'] ? $employeeName['manager'] : $employeeName['fullname'];
             $savedConfirms = array(
                'employee_id' => $employeeName['id'],
                'company_id' => $employeeName['company_id'],
                'start' => $_start,
                'end' => $_end,
                'status' => $status,
                'employee_validate' => $employeeValidate
             );
             if($this->ActivityRequestConfirm->save($savedConfirms)){
                $this->_cacheRequest($_start, $_end, $employeeName['id'], $status, $profit_id, true, $employeeName['company_id'], $employeeValidate);
                $absens = Set::combine($this->AbsenceRequest->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                        'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $employeeName['id']))),
                    '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
                $tmps = array();
                foreach($absens as $absen){
                    foreach($absen as $abs){
                        if($abs['response_am'] == 'waiting'){
                            $tmps['response_am'] = 'rejetion';
                            $this->AbsenceRequest->id = $abs['id'];
                            $this->AbsenceRequest->save($tmps);
                        }
                        if($abs['response_pm'] == 'waiting'){
                            $tmps['response_pm'] = 'rejetion';
                            $this->AbsenceRequest->id = $abs['id'];
                            $this->AbsenceRequest->save($tmps);
                        }
                    }
                }
                $this->_deleteCacheContextMenu();
             }
        } elseif($typeSelect == 'month'){
            $employeeValidate= $this->Session->read('Auth.Employee.fullname');
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            foreach ($listWeekOfMonths as $st => $en) {
                $this->_cacheRequestMonth($st, $en, $employeeName['id'], $status, $profit_id, true, $employeeName['company_id'], $employeeValidate);
                $absens = Set::combine($this->AbsenceRequest->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                        'date BETWEEN ? AND ?' => array($st, $en), 'employee_id' => $employeeName['id']))),
                    '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
                $tmps = array();
                foreach($absens as $absen){
                    foreach($absen as $abs){
                        if($abs['response_am'] == 'waiting'){
                            $tmps['response_am'] = 'rejetion';
                            $this->AbsenceRequest->id = $abs['id'];
                            $this->AbsenceRequest->save($tmps);
                        }
                        if($abs['response_pm'] == 'waiting'){
                            $tmps['response_pm'] = 'rejetion';
                            $this->AbsenceRequest->id = $abs['id'];
                            $this->AbsenceRequest->save($tmps);
                        }
                    }
                }
            }
            $this->_deleteCacheContextMenu();
        }
        // $this->Session->setFlash(__('Saved', true), 'success');
        $employees = array_values($this->ActivityRequest->Employee->find('list', array(
                    'fields' => array('email', 'email'), 'recursive' => -1, 'conditions' => array('email_receive' => true, 'id' => $employeeName['id']))));
        $address = '';
        $this->_saveAfterValidateAndReject($_start, $_end,  $profit_id, $saveStatus, $employeeName['id'], $address, $typeSelect, true, $employees, true, true);
    }


    protected function _backProfitEmployee($isManage, $_start, $_end, $typeSelect = null) {
        list($isManage, $employeeName) = $this->_getProfitEmployee();
        $params = array();
        if ($isManage) {
            $params = array(
                'profit' => $this->params['url']['profit'],
                'id' => $this->params['url']['id'],
                'get_path' => 1
            );
        }
        $avg = intval(($_start + $_end)/2);
        if($typeSelect === 'week'){
            $this->redirect(array('action' => 'request','week', '?' => $params + array(
            'week' => date('W', $avg), 'year' => date('Y', $avg))));
        } else{
            $this->redirect(array('action' => 'request','month', '?' => $params + array(
            'month' => date('m', $avg), 'year' => date('Y', $avg))));
        }
    }

    protected function _getProfitEmployee($getDataByPath = false) {
        if( isset($this->params['url']['get_path']) )$getDataByPath = $this->params['url']['get_path'];
        $isManage = !empty($this->params['url']['id']) && !empty($this->params['url']['profit']);
        $check = !($employeeName = $this->_getEmpoyee()) ||
                ($isManage && (!($params = $this->_getProfits($this->params['url']['profit'], true)) || !isset($params[2][$this->params['url']['id']])));
        if ($isManage) {
            $employee = $this->ActivityForecast->Employee->find('first', array(
                'recursive' => -1, 'fields' => array('*'), 'conditions' => array(
                    'id' => $this->params['url']['id']
                    )));
            $employeeName = array_merge($employeeName, $employee['Employee']);
        }
		if( !empty($employeeName['Employee']['password'])) unset($employeeName['Employee']['password']);

        return array($isManage, $employeeName);
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update_request($typeSelect = null) {
        $this->loadModel('ActivityRequest');
        $result = false;
        $this->layout = false;
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true);
        }
        list(, $employeeName) = $this->_getProfitEmployee();
        if ($employeeName && !empty($this->data) && (!empty($this->data['activity']) || !empty($this->data['task_id']))) {
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($employeeName['id']);
			if( empty( $canWrite)){
				$this->Session->setFlash(__('Access denied', true), 'error');
				$this->redirect(array( 'controller' => 'activity_forecasts', 'action' => 'request'));
			}
            $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
            $listWorkingDays = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
            if(!empty($listWorkingDays)){
                foreach($listWorkingDays as $day => $time){
                    /**
                     * kiem tra xem ngay hien tai dang o trang thai nao?
                     * Neu dang waiting or validation thi khong cho phep thay doi ngay do nua.
                     */
                    $checkDateBeforeUpdates = $this->ActivityRequest->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'date' => $day,
                            'employee_id' => $employeeName['id'],
                            'company_id' => $employeeName['company_id'],
                            'status' => array(0, 2)
                        ),
                        'fields' => array('id')
                    ));
                    if($managerHour){
                        if (!isset($this->data[$day]) || (!empty($checkDateBeforeUpdates) && $checkDateBeforeUpdates['ActivityRequest']['id'])) {
                            continue;
                        }
                    } else {
                        if (!isset($this->data[$day]) || !is_numeric($this->data[$day]) || (!empty($checkDateBeforeUpdates) && $checkDateBeforeUpdates['ActivityRequest']['id'])) {
                            continue;
                        }
                    }
                    $data = array(
                        'date' => $day,
                        'employee_id' => $employeeName['id'],
                        'activity_id' => $this->data['activity']
                    );
                    if(!empty($this->data['task_id'])){
                        $data = array(
                            'date' => $day,
                            'employee_id' => $employeeName['id'],
                            'task_id' => $this->data['task_id'],
							'activity_id' => 0
                        );
                    }
                    $last = $this->ActivityRequest->find('first', array('recursive' => -1
                        , 'conditions' => $data));
                    $this->ActivityRequest->create();
                    if ($last) {
                        $this->ActivityRequest->id = $last['ActivityRequest']['id'];
                    } else {
                        if($managerHour){
                            if(isset($this->data[$day])){
                                $valueHour = 0;
                                $valueHour = $this->data[$day];
                                $valueHour = explode(':', $valueHour);
                                $valueHour = $valueHour[0] * 60 + $valueHour[1];
                                if($valueHour == 0){
                                    unset($this->data[$day]);
                                    continue;
                                }
                            }
                        } else {
                            if(isset($this->data[$day]) && $this->data[$day] == 0){
                                unset($this->data[$day]);
                                continue;
                            }
                        }
                    }
                    if($managerHour){
                        $getHour = $this->Employee->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('Employee.id' => $employeeName['id']),
                            'fields' => array('hour', 'minutes')
                        ));
                        $hour = !empty($getHour) && !empty($getHour['Employee']['hour']) ? $getHour['Employee']['hour'] : 0;
                        $minute = !empty($getHour) && !empty($getHour['Employee']['minutes']) ? $getHour['Employee']['minutes'] : 0;
                        $totalMinute = $hour * 60 + $minute;
                        $valueHour = 0;
                        $valueHour = $this->data[$day];
                        $valueHour = explode(':', $valueHour);
                        $valueHour = $valueHour[0] * 60 + $valueHour[1];
                        $this->ActivityRequest->save(array_merge($data,
                            array(
                                'status' => -1,
                                'company_id' => $employeeName['company_id'],
                                'value' => $valueHour/$totalMinute,
                                'manager_by' => 'hour',
                                'value_hour' => $this->data[$day]
                            )
                        ));
                    } else {
                        $this->ActivityRequest->save(array_merge($data, array('status' => -1, 'company_id' => $employeeName['company_id'], 'manager_by' => 'day', 'value_hour' => '00:00', 'value' => floatval($this->data[$day]))));
                    }
                }
            }
            if (!empty($this->data['task_id'])) {
                # code...
                $curr_id = implode('-', array($this->data['activity'], $this->data['task_id']));
            }else{
                $curr_id = $this->data['activity'];
            }

            if (!empty($this->data['last']) && $this->data['last'] != $curr_id) {
                list($activity, $task) = explode('-', $this->data['last'] . '-', 2);
                $conditions = array(
                    'activity_id' => $activity,
                    'task_id' => $task ? $task : null,
                    'employee_id' => $employeeName['id'],
                    'date BETWEEN ? AND ?' => array($_start, $_end));
                $list = $this->ActivityRequest->find('list', array('fields' => array('id'), 'conditions' => $conditions));
                foreach ($list as $id) {
                    $this->ActivityRequest->delete($id);
                }
            }
            $this->data['last'] = $curr_id;
			$result = true;
            // $this->Session->setFlash(__('The data has been saved.', true), 'success');
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function delete_request($id = null , $task = null, $typeSelect = null) {
        $this->loadModel('ActivityRequest');
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true);
        }
        list(, $employeeName) = $this->_getProfitEmployee();
		
		$result = false;
        if ($employeeName) {
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($employeeName['id']);
			if( empty( $canWrite)){
				$this->Session->setFlash(__('Access denied', true), 'error');
				$this->redirect(array( 'controller' => 'activity_forecasts', 'action' => 'request'));
			}
            if($task != -1){
                $id = 0;
            } else {
                $task = null;
            }
            $last = $this->ActivityRequest->find('list', array('recursive' => -1, 'fields' => array('id', 'id'),
                'conditions' => array(
                    'activity_id' => $id,
                    'task_id' => $task ? $task : null,
                    'date BETWEEN ? AND ?' => array($_start, $_end),
                    'employee_id' => $employeeName['id'])));
            if(!empty($last)){
                $this->ActivityRequest->deleteAll(array('ActivityRequest.id' => $last), false);
            }
			$result = true;
            $this->Session->setFlash(__('The data has been deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('The data was not deleted', true), 'error');
        }
		
		if( $this->params['isAjax']){
			die(json_encode($result));
		}
        if($typeSelect === 'week'){
            $this->_backProfitEmployee(@$isManage, $_start, $_end, $typeSelect);
        } else {
            $this->_backProfitEmployee(@$isManage, $_start, $_end, $typeSelect);
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function comment_update() {
        $this->loadModel('ActivityComment');
        $this->ActivityComment->cacheQueries = false;
        $this->layout = false;
        $success = $total = 0;
        if (!empty($this->data)) {
            foreach ($this->data as &$post) {
                foreach ($post as &$data) {
                    $total++;
                    if (!empty($data['date']) && !empty($data['employee_id']) && ($employeeName = $this->_getEmpoyee())) {
                        foreach (array('am', 'pm') as $type) {
                            $save = array(
                                'date' => $data['date'],
                                'employee_id' => $data['employee_id'],
                                'user_id' => $employeeName['id'],
                                'time' => ($type == 'pm')
                            );
                            if (!empty($data[$type])) {
                                $save['text'] = str_replace("\n", "<br/>", trim($data[$type]));
                                $this->ActivityComment->create();
                                if (!($data['result'] = (bool) $this->ActivityComment->save($save))) {
                                    break;
                                }
                                $data['id_' . $type] = $this->ActivityComment->id;
                                $data[$type] = $save['text'];
                                $data['created'] = date('d-m-Y H:i');
                            }
                        }
                        if (!empty($data['result'])) {
                            $success++;
                        }
                    }
                }
            }
            if ($success == $total) {
                // $this->Session->setFlash(__('The comment has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('Some comment could not be saved because invalidate.', true), 'warning');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function comment_delete() {
        $this->layout = false;
        $this->loadModel('ActivityComment');
        if (isset($this->params['url']['id']) && ($employeeName = $this->_getEmpoyee())) {
            if ($this->ActivityComment->find('count', array('recursive' => -1, 'conditions' => array(
                            'id' => $this->params['url']['id'],
                            'user_id' => $employeeName['id']
                            )))) {
                $this->ActivityComment->delete($this->params['url']['id']);
            }
        }
        $this->_stop();
    }

    private function _testPerformence(){
        set_time_limit(0); // ko gioi han thoi gian su dung
        ob_start();  // khoi tao 1 bo dem
        session_write_close(); // optional, this will close the session.
        header('Location: /activity_forecasts/response?profit=176&week=19&year=2014'); // nhay den 1 trang nao do. redirect page
        header('Content-Length: 0'); // do dai cua 1 noi dung
        header('Connection: close'); // ngat ket noi
        ob_end_flush(); // dong bo dem
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        for($i = 0; $i < 20; $i++){
            $this->log('_testPerformence: ' . $i);
            sleep(1);
        }
        /*
        $redirect = '/activity_forecasts/'.$address['action'].'?profit='.$address['?']['profit'].'&week='.$address['?']['week'].'&year='.$address['?']['year'];
        set_time_limit(0); // ko gioi han thoi gian su dung
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: 0'); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '.$redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
         */
    }

    /**
     * Code chuan
     * $this->_saveAfterValidateAndReject($_start, $_end,  $profit['id'], $status, array_keys($employees), $address);
     */
    private function _saveAfterValidateAndReject($start = null, $end = null, $profit = null, $status = null, $employees = null, $address = null,$typeSelect = null, $isValidated = null, $lisEmployEmail = array(), $validInAdmin = 'false', $autoValid = false){
        CakeLog::write('staffing', '==Staffing initiated: ' . (!empty($isValidated) ? 'validate' : 'reject') . '==');
        if($validInAdmin == 'true'){
            if($autoValid){
                list($isManage, $employeeName) = $this->_getProfitEmployee();
                $params = array();
                if ($isManage) {
                    $params = array(
                        'profit' => $this->params['url']['profit'],
                        'id' => $this->params['url']['id'],
                        'get_path' => 1
                    );
                }
                $AVGDate = ($start+$end)/2;
                if($typeSelect === 'week'){
                    if($isManage){
                        $redirect = '/activity_forecasts/request/week?profit='.$this->params['url']['profit'].'&id='.$this->params['url']['id'].'&get_path=1&week='.date('W', $end).'&year='.date('Y', $AVGDate);
                    } else {
                        $redirect = '/activity_forecasts/request/week?week='.date('W', $end).'&year='.date('Y', $AVGDate);
                    }
                } else{
                    if($isManage){
                        $redirect = '/activity_forecasts/request/month?profit='.$this->params['url']['profit'].'&id='.$this->params['url']['id'].'&get_path=1&month='.date('m', $AVGDate).'&year='.date('Y', $AVGDate);
                    } else {
                        $redirect = '/activity_forecasts/request/month?month='.date('m', $AVGDate).'&year='.date('Y', $AVGDate);
                    }
                }
            } else {
                $redirect = '/activity_forecasts/import_timesheet/';
            }
        } else {
            if($typeSelect == 'week'){
                $redirect = '/activity_forecasts/'.$address['action'].'/week?profit='.$address['?']['profit'].'&week='.$address['?']['week'].'&year='.$address['?']['year'].'&get_path='.$address['?']['get_path'];
            }elseif($typeSelect == 'month'){
                $redirect = '/activity_forecasts/'.$address['action'].'/month?profit='.$address['?']['profit'].'&month='.$address['?']['month'].'&year='.$address['?']['year'].'&get_path='.$address['?']['get_path'];
            }else{
                $redirect = '/activity_forecasts/'.$address['action'].'/year?profit='.$address['?']['profit'].'&month='.$address['?']['month'].'&year='.$address['?']['year'].'&get_path='.$address['?']['get_path'];
            }
        }
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        if( !isset($_GET['no_output']) )
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
        /**
         * Send Mail
         */
        if ($lisEmployEmail) {
            $status = !empty($isValidated) ? __('validated', true) : __('rejected', true);
            $this->set('isValidated', !empty($isValidated));
            $this->set('typeSelect', $typeSelect);
            if( !$autoValid )$this->_sendEmail($lisEmployEmail, sprintf(__('[Azuree] Timesheet has been %s.', true), $status), 'activity_response');
        }
        /**
         * Lay tat ca request cua employees trong start -> end date.
         */
        $this->loadModels('ActivityRequest', 'ActivityTask', 'ProjectTask', 'Activity');
        $employeeName = $this->_getEmpoyee();
        $requestOfWeeks = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'date BETWEEN ? AND ?' => array($start, $end),
                'employee_id' => $employees,
                'company_id' => $employeeName['company_id']
            ),
            'fields' => array('activity_id', 'task_id', 'employee_id')
        ));
        if(!empty($requestOfWeeks)){
            $activities = $tasks = $taskOfEmployees = array();
            foreach($requestOfWeeks as $requestOfWeek){
                $dx = $requestOfWeek['ActivityRequest'];
                if($dx['activity_id'] != 0){
                    $activities[] = $dx['activity_id'];
                }
                if($dx['task_id'] != 0 || $dx['task_id'] != ''){
                    $tasks[] = $dx['task_id'];
                    if(!isset($taskOfEmployees[$dx['employee_id']])){
                        $taskOfEmployees[$dx['employee_id']] = array();
                    }
                    $taskOfEmployees[$dx['employee_id']][] = $dx['task_id'];
                }
            }
            $tasks = !empty($tasks) ? array_unique($tasks) : array();
            if(!empty($tasks)){
                /**
                 * Lay tat cac request thuoc cac task tren
                 */
                $requests = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => $tasks,
                        'status' => 2,
                        'company_id' => $employeeName['company_id']
                    ),
                    'fields' => array(
                        'SUM(value) as value',
                        'task_id'
                    ),
                    'group' => array('task_id')
                ));
                $requests = !empty($requests) ? Set::combine($requests, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
                $activityTasks = $this->ActivityTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $tasks),
                    'fields' => array('id', 'project_task_id', 'activity_id', 'estimated')
                ));
                if(!empty($activityTasks)){
                    $projectTasks = $consumedOfProjectTasks = array();
                    /**
                     * Luu lai overload cho activity task
                     */
                    $taskOfActivities = array();
                    foreach($activityTasks as $activityTask){
                        $dx = $activityTask['ActivityTask'];
                        $activities[] = $dx['activity_id'];
                        $taskOfActivities[$dx['id']] = $dx['activity_id'];
                        $consumed = !empty($requests[$dx['id']]) ? $requests[$dx['id']] : 0;
                        if($dx['project_task_id'] != '' || $dx['project_task_id'] != 0){
                            $projectTasks[] = $dx['project_task_id'];
                            $consumedOfProjectTasks[$dx['project_task_id']] = $consumed;
                        } else {
                            $overLoad = 0;
                            if($consumed > $dx['estimated']){
                                $overLoad = $consumed - $dx['estimated'];
                            }
                            $this->ActivityTask->id = $dx['id'];
                            $this->ActivityTask->saveField('overload', $overLoad);
                        }
                    }
                    /**
                     * Luu lai overload cho project task
                     */
                    $pTasks = $this->ProjectTask->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('ProjectTask.id' => $projectTasks),
                        'fields' => array('id', 'estimated')
                    ));
                    if(!empty($pTasks)){
                        foreach($pTasks as $id => $estimated){
                            $consumed = !empty($consumedOfProjectTasks[$id]) ? $consumedOfProjectTasks[$id] : 0;
                            $overLoad = 0;
                            if($consumed > $estimated){
                                $overLoad = $consumed - $estimated;
                            }
                            $this->ProjectTask->id = $id;
                            $this->ProjectTask->saveField('overload', $overLoad);
                        }
                    }
                }
            }
            //Quyet disabled this, we don't need to run staffing after timesheet validation or rejection because consumes are taken directly from activity requests in staffing+ screen
            // $activityOfEmployees = array();
            // if(!empty($taskOfEmployees)){
            //     foreach($taskOfEmployees as $employ => $taskOfEmployee){
            //         if(!empty($taskOfEmployee)){
            //             $taskOfEmployee = array_unique($taskOfEmployee);
            //             foreach($taskOfEmployee as $taskId){
            //                 $acti = $taskOfActivities[$taskId] ? $taskOfActivities[$taskId] : 0;
            //                 $activityOfEmployees[$acti][] = $employ;
            //             }
            //         }
            //     }
            // }
            $activities = !empty($activities) ? array_unique($activities) : array();
            CakeLog::write('staffing', '=For: ' . json_encode($employeeName));
            CakeLog::write('staffing', '=Activities: ' . implode(' ', $activities));
            $this->loadModel('Activity');
            $linkeds = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activities),
                'fields' => array('id', 'project')
            ));
            foreach($activities as $id){
                if(!empty($linkeds[$id])){
                    $this->ProjectTask->staffingSystem($linkeds[$id]);
                    CakeLog::write('staffing', 'Finished project: ' . $linkeds[$id]);
                } else {
                    $this->ActivityTask->staffingSystem($id);
                    CakeLog::write('staffing', 'Finished activity: ' . $id);
                }
            }
            CakeLog::write('staffing', "==End staffing\n");
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _cacheRequestMonth($start, $end, $employee_id, $status, $profit = null, $validation = false, $company_id = null, $employeeValidate = null) {
        $this->loadModel('ActivityRequest');
        /**
         * Update request cua 1 thang lam viec
         */
        $this->ActivityRequest->unbindModelAll();
        $this->ActivityRequest->updateAll(array('status' => $status), array(
            'date BETWEEN ? AND ?' => array($start, $end),
            'employee_id' => $employee_id,
            'NOT' => array('status' => array(-1, 1))
        ));
        /**
         * Lay cac tuan khong dang o trang thai cho` send for validation
         * va cac tuan da reject trong thang
         */
        $_statusDk = array(2);
        if($status == 1){
            $_statusDk = array(1, 2);
        }
        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'date BETWEEN ? AND ?' => array($start, $end),
                'employee_id' => $employee_id,
                'status' => $_statusDk
            ),
            'fields' => array('date')
        ));
        $requests = !empty($requests) ? array_unique(Set::classicExtract($requests, '{n}.ActivityRequest.date')) : array();
        /**
         * Update confirm cua 1 thang lam viec
         */
        $this->_saveConfirmFollowMonth($start, $end, $employee_id, $company_id, $employeeValidate);
        /**
         * Update cac tuan cua 1 thang lam viec
         */
        $listWeekOfMonths = $this->_splitWeekOfMonth($start, $end);
        $this->_saveMultipleConfirmWeek($listWeekOfMonths, $employee_id, $company_id, $employeeValidate, $status, array());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _cacheRequest($start, $end, $employee_id, $status, $profit = null, $validation = false, $company_id = null, $employeeValidate = null) {
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityRequestConfirmMonth');
        $this->ActivityRequest->unbindModelAll();
		/* Update TJM & cost if validated timesheet 
			#517
		*/ 
		if( $status == 2){
			$this->loadModels('Employee', 'ProfitCenter', 'Profile');
			$employee = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Employee.id' => $employee_id,
				),
				'joins' => array(
					array(
						'table' => 'profit_centers',
						'alias' => 'ProfitCenter',
						'conditions' => array('Employee.profit_center_id = ProfitCenter.id')
					)
				),
				'fields' => array(
					'Employee.id',
					'Employee.tjm',
					'Employee.first_name',
					'Employee.last_name',
					'ProfitCenter.id',
					'ProfitCenter.name',
					'ProfitCenter.tjm',
					'ProfitCenter.profile_id',
				),
			));
			$e_profile = array();
			if( !empty($employee['ProfitCenter']['profile_id'])){
				$e_profile = $this->Profile->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'Profile.id' => $employee['ProfitCenter']['profile_id'],
					),
					'fields' => array(
						'Profile.id',
						'Profile.name',
						'Profile.tjm',
					),
				));
			}
			$pc_tjm = !empty($employee['ProfitCenter']['tjm']) ? (float)$employee['ProfitCenter']['tjm'] : 0;
			$pf_tjm = !empty($e_profile['Profile']['tjm']) ? (float)$e_profile['Profile']['tjm'] : 0;
			$e_tjm = !empty($employee['Employee']['tjm']) ? (float)$employee['Employee']['tjm'] : 0;
			$e_name = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
			$e_name .= ' ' . (!empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '');
			$e_name .= ' ' . $employee['Employee']['id'];
			
			$requests = $this->ActivityRequest->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'date BETWEEN ? AND ?' => array($start, $end),
					'employee_id' => $employee_id
				),
				'fields' => array('*'),
			));
			if( !empty( $requests)){
				foreach( $requests as $ind => $val){
					$val = $val['ActivityRequest'];
					$value = (float) $val['value'];
					$val['price_resource'] = $e_tjm;
					$val['price_team'] = $pc_tjm;
					$val['price_profil'] = $pf_tjm;
					$val['cost_price_resource'] = $e_tjm * $value;
					$val['cost_price_team'] = $pc_tjm * $value;
					$val['cost_price_profil'] = $pf_tjm * $value;
					$val['employee_name'] = $e_name;
					$val['profit_center_name'] = !empty($employee['ProfitCenter']['name']) ? $employee['ProfitCenter']['name'] : '';
					$val['status'] = $status;
					$this->ActivityRequest->id = $val['id'];
					unset($val['id']);
					$result = $this->ActivityRequest->save($val);
				}
			}
		}
		/* END update TJM & cost if validated timesheet */ 
		else{
			$this->ActivityRequest->updateAll(array('status' => $status), array(
				'date BETWEEN ? AND ?' => array($start, $end),
				'employee_id' => $employee_id
			));
		}
        /**
         * Luu lai confirm cua thang
         */
        list($startDayMonth, $endDayMonth) = $this->_parseParamsMonth(true, true);
        $this->_saveConfirmFollowMonth($startDayMonth, $endDayMonth, $employee_id, $company_id, $employeeValidate);
    }

    private function _saveConfirmFollowMonth($start, $end, $employee_id, $company_id, $employeeValidate){
        $this->loadModel('ActivityRequestConfirmMonth');
        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'date BETWEEN ? AND ?' => array($start, $end)
            ),
            'fields' => array('id', 'status', 'date')
        ));
        $listWeekOfMonths = $this->_splitWeekOfMonth($start, $end);
        if(!empty($requests)){
            $listDayRequests = $_requests = array();
            foreach($requests as $request){
                $dx = $request['ActivityRequest'];
                $listDayRequests[$dx['date']] = $dx['date'];
                $_requests[$dx['id']] = $dx['status'];
            }
            $status = -1;
            $_requests = array_unique($_requests);
            if(count($_requests) == 1){
                $status = array_shift($_requests);
            }
            if(!empty($listDayRequests)){
                foreach($listWeekOfMonths as $day => $time){
                    if(!in_array($day, $listDayRequests)){
                        $status = -1;
                    }
                }
            } else {
                $status = -1;
            }
            $confirms = $this->ActivityRequestConfirmMonth->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'start' => $start,
                    'end' => $end,
                    'employee_id' => $employee_id,
                    'company_id' => $company_id
                ),
                'fields' => array('id')
            ));
            $this->ActivityRequestConfirmMonth->create();
            if(!empty($confirms) && $confirms['ActivityRequestConfirmMonth']['id']){
                $this->ActivityRequestConfirmMonth->id = $confirms['ActivityRequestConfirmMonth']['id'];
            }
            $saved = array(
                'start' => $start,
                'end' => $end,
                'employee_id' => $employee_id,
                'company_id' => $company_id,
                'employee_validate' => $employeeValidate,
                'status' => $status
            );
            $this->ActivityRequestConfirmMonth->save($saved);
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _cacheRequestWhenSendForValidation($start, $end, $employee_id, $status, $company_id, $typeSelect) {
        $this->loadModel('ActivityRequest');
        $this->ActivityRequest->unbindModelAll();
        $this->ActivityRequest->updateAll(array('status' => $status), array(
            'date BETWEEN ? AND ?' => array($start, $end),
            'employee_id' => $employee_id,
            'NOT' => array('status' => 2)
        ));
		if( $status === 0){
			$this->ActivityRequest->deleteAll(array(
				'date BETWEEN ? AND ?' => array($start, $end),
				'employee_id' => $employee_id,
				'status' => 0,
				'value' => 0,
			));
		}
        if($typeSelect === 'month'){
            /**
             * Lay cac tuan khong dang o trang thai cho` send for validation
             * va cac tuan da reject trong thang
             */
            $requests = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'date BETWEEN ? AND ?' => array($start, $end),
                    'employee_id' => $employee_id,
                    'status' => array(-1, 0, 1)
                ),
                'fields' => array('date')
            ));
            $requests = !empty($requests) ? array_unique(Set::classicExtract($requests, '{n}.ActivityRequest.date')) : array();
            $weeks = $this->_splitWeekOfMonth($start, $end);
            $this->_saveMultipleConfirmWeek($weeks, $employee_id, $company_id, null, $status, $requests);
        } else {
            list($firstDayMonthOfStart, $firstDayMonthOfEnd) = $this->_parseParamsMonth(true, true);
            $this->_saveConfirmFollowMonth($firstDayMonthOfStart, $firstDayMonthOfEnd, $employee_id, $company_id, null);
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _checkEditable($id) {
        if (!isset($this->ActivityRequestConfirm)) {
            $this->loadModel('ActivityRequestConfirm');
            $this->ActivityRequestConfirm->cacheQueries = true;
        }
        $requestConfirm = Set::classicExtract($this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status'),
                            'conditions' => array('employee_id' => $id, 'start' => $this->viewVars['_start'], 'end' => $this->viewVars['_end']))), 'ActivityRequestConfirm.status');

        $this->set('requestConfirm', $requestConfirm);
        return ($requestConfirm == 1 || $requestConfirm === false);
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _remove($employees) {
        $success = $total = 0;
        $model = $this->data['model'];
        unset($this->data['request'], $this->data['model']);
        foreach ($this->data as &$post) {
            foreach ($post as &$data) {
                $total++;
                if (!empty($data['date']) && isset($data['employee_id']) && isset($employees[$data['employee_id']])) {
                    $last = $this->ActivityForecast->find('first', array('recursive' => -1, 'fields' => array(
                            'id', 'activity_am', 'activity_pm', 'am_model', 'pm_model'),
                        'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'])));
                    if (!$last) {
                        continue;
                    }
                    $last = array_shift($last);
                    $this->ActivityForecast->id = $last['id'];
                    $remove = 0;
                    foreach (array('am', 'pm') as $type) {
                        if (!empty($data[$type])) {
                            $remove++;
                            $data['activity_' . $type] = '0';
                        }
                        if ($last && empty($data[$type])) {
                            $last['activity_' . $type] = '0';
                        }
                        unset($data[$type]);
                    }
                    if (($remove == 2 && ($data['result'] = (bool) $this->ActivityForecast->delete($this->ActivityForecast->id))) ||
                            ($remove == 1 && ($data['result'] = (bool) $this->ActivityForecast->save($data)))) {
                        $success++;
                    }
                }
            }
        }
        return array($success, $total);
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
		if( isset( $this->employee_info['Employee']['password'])) unset( $this->employee_info['Employee']['password']);
        return $this->employee_info['Employee'];
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfitFollowDates($profitId,$getDataByPath = true, $start = null, $end = null) {
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
        if(empty($profit)){
            $profit = $Model->find('list', array(
                'recursive' => -1, 'fields' => array('id'),
                'order' => array('lft' => 'ASC'),
                'conditions' => array(
                    'company_id' => $user['company_id']
                )
            ));
        }
        $isAdmin = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr';
        if ($isAdmin) {
            $paths = $Model->generatetreelist(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } elseif (!empty($profit)) {
            // $paths = $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array(
            //      'lft >=' => $profit['ProfitCenter']['lft'],
            //      'rght <=' => $profit['ProfitCenter']['rght']
            //      )));
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

        $employees =  $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
                    'conditions' => array('profit_center_id' => $pathOfPC)));
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
                $_managers[$key] = $employees[$key] . '<strong> (Manager)</strong>';
            }
        }
        $employees = $_managers + $employees;
        if (!$isAdmin) {
            //unset($employees[$user['id']]);
        }
        if($profitId == -1){
            $employees = array();
            $profit['id'] = -1;
        }
        return array($profit, $paths, $employees, $user);
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
        if(empty($profit)){
            $profit = $Model->find('list', array(
                'recursive' => -1, 'fields' => array('id'),
                'order' => array('lft' => 'ASC'),
                'conditions' => array(
                    'company_id' => $user['company_id']
                )
            ));
        }
        $isAdmin = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr';
        if ($isAdmin) {
            $paths = $Model->generatetreelist(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } elseif (!empty($profit)) {
            // $paths = $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array(
            //      'lft >=' => $profit['ProfitCenter']['lft'],
            //      'rght <=' => $profit['ProfitCenter']['rght']
            //      )));
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

        $employees = $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
                    'conditions' => array('profit_center_id' => $pathOfPC)));
        $employees = Set::combine($this->ActivityForecast->Employee->find('all', array(
                            'order' => 'first_name DESC',
                            'fields' => array('id', 'first_name', 'last_name'),
                            'recursive' => -1, 'conditions' => array('id' => $employees)))
                        , '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        //END
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
        if($profitId == -1){
            $employees = array();
            $profit['id'] = -1;
        }
        return array($profit, $paths, $employees, $user);
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfitMyDiarys($profitId) {
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
            /*
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'request'));
            }
            */
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

        $this->set(compact('_start', '_end'));
        $this->set('_month', $params['month']);
        $this->set('_year', $params['year']);
        //pr(date('m-d W', $_start) . ' ' . date('m-d W', $_end));
        return array($_start, $_end);
    }
    // edit 22/05/2013

    public function get_consumed(){
        //$this->layout = 'ajax';
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTask');
        $employeeName = $this->_getEmpoyee();
        // Filter data from Activity Requests
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive'     => -1,
                'fields'        => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
                'group'         => array('task_id'),
                'conditions'    => array(
                    'status'        => array(0, 2),
                    'company_id'    => $employeeName['company_id'],
                    'NOT'           => array('value' => 0, "task_id" => null),
                )
            )
        );
        $activityTasks = $this->ActivityTask->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id', 'estimated', 'project_task_id')
                ));
        $activityTasks = Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask');
        $_activityTasks = array();
        foreach($activityTasks as $k => $activityTask){
                $_activityTasks[$k]['ActivityRequest']['employee_id'] = '';
                $_activityTasks[$k]['ActivityRequest']['task_id'] = $activityTask['id'];
                $_activityTasks[$k]['ActivityRequest']['consumed'] = 0;
                $_activityTasks[$k]['ActivityRequest']['estimated'] = $activityTask['estimated'];
                $_activityTasks[$k]['ActivityRequest']['project_task_id'] = $activityTask['project_task_id'];
        }

        if(!empty($datas)){
            $_data = array();
            foreach($datas as $key => $data){
                $_data[$key]['ActivityRequest']['employee_id'] = $data['ActivityRequest']['employee_id'];
                $_data[$key]['ActivityRequest']['task_id'] = $data['ActivityRequest']['task_id'];
                $_data[$key]['ActivityRequest']['consumed'] = $data[0]['value'];
            }
            $filterId = Set::combine($_data, '{n}.ActivityRequest.task_id', '{n}.ActivityRequest.task_id');
            $activityRequests = Set::combine($_data, '{n}.ActivityRequest.task_id', '{n}.ActivityRequest');

            foreach($activityRequests as $key => $activityRequest){
                foreach($_activityTasks as $k => $activityTask){
                    if($key == $k){
                        $_activityTasks[$key]['ActivityRequest']['employee_id'] = $activityRequest['employee_id'];
                        $_activityTasks[$key]['ActivityRequest']['task_id'] = $activityRequest['task_id'];
                        $_activityTasks[$key]['ActivityRequest']['consumed'] = $activityRequest['consumed'];
                        $_activityTasks[$key]['ActivityRequest']['estimated'] = $activityTask['ActivityRequest']['estimated'];
                        $_activityTasks[$key]['ActivityRequest']['project_task_id'] = $activityTask['ActivityRequest']['project_task_id'];
                    }
                }
            }

            $projectTaskId = Set::combine($_activityTasks, '{n}.ActivityRequest.project_task_id', '{n}.ActivityRequest.project_task_id');
            $projectTasks = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $projectTaskId),
                    'fields' => array('id', 'estimated')
                ));
            $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.estimated');

            foreach($_activityTasks as $key => $value){
                if($value['ActivityRequest']['estimated'] == ''){
                    $_activityTasks[$key]['ActivityRequest']['estimated'] = 0;
                }
                if(!empty($value['ActivityRequest']['project_task_id'])){
                    $_activityTasks[$key]['ActivityRequest']['estimated'] = isset($projectTasks[$value['ActivityRequest']['project_task_id']]) ? $projectTasks[$value['ActivityRequest']['project_task_id']] : 0;
                }
            }

            $tasks = array();
            foreach($_activityTasks as $key => $v){
               $tasks[$key]['task_id'] = $v['ActivityRequest']['task_id'];
               $tasks[$key]['employee_id'] = $v['ActivityRequest']['employee_id'];
               $tasks[$key]['estimated'] = $v['ActivityRequest']['estimated'];
               $tasks[$key]['consumed'] = $v['ActivityRequest']['consumed'];
               $tasks[$key]['remain'] = $v['ActivityRequest']['estimated'] - $v['ActivityRequest']['consumed'];
            }
            $result = $tasks;
        } else {
            $projectTaskId = Set::combine($_activityTasks, '{n}.ActivityRequest.project_task_id', '{n}.ActivityRequest.project_task_id');
            $projectTasks = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $projectTaskId),
                    'fields' => array('id', 'estimated')
                ));
            $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.estimated');

            foreach($_activityTasks as $key => $value){
                if($value['ActivityRequest']['estimated'] == ''){
                    $_activityTasks[$key]['ActivityRequest']['estimated'] = 0;
                }
                if(!empty($value['ActivityRequest']['project_task_id'])){
                    $_activityTasks[$key]['ActivityRequest']['estimated'] = isset($projectTasks[$value['ActivityRequest']['project_task_id']]) ? $projectTasks[$value['ActivityRequest']['project_task_id']] : 0;
                }
            }
            $tasks = array();
            foreach($_activityTasks as $key => $v){
               $tasks[$key]['task_id'] = $v['ActivityRequest']['task_id'];
               $tasks[$key]['employee_id'] = $v['ActivityRequest']['employee_id'];
               $tasks[$key]['estimated'] = $v['ActivityRequest']['estimated'];
               $tasks[$key]['consumed'] = $v['ActivityRequest']['consumed'];
               $tasks[$key]['remain'] = $v['ActivityRequest']['estimated'] - $v['ActivityRequest']['consumed'];
            }
            $result = $tasks;
        }
        return $result;
    }

    private function _projectTeam(){
        $this->loadModel('ProjectTeam');
        $project_id = $this->_projects();
        $projectTeams = $this->ProjectTeam->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'project_function_id', 'allow_request'),
                'order' => array('id')
            ));
        return $projectTeams;
    }

    private function _employee(){
        $this->loadModel('ProjectFunctionEmployeeRefer');

        $projectTeams = $this->_projectTeam();
        $projectTeamId = Set::combine($projectTeams, '{n}.ProjectTeam.id', '{n}.ProjectTeam.id');

        $employees = $this->ProjectFunctionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_team_id' => $projectTeamId),
                'fields' => array('employee_id', 'allow_request')
            ));
        return $employees;
    }

    private function _checkEmployee(){
        $check = $this->_employee();
        $employee = $this->Session->read("Auth.employee_info");
        $employee = $employee['Employee']['id'];
        $result = 'no';
        if(in_array($employee, array_keys($check)) && isset($check[$employee]) && $check[$employee] == 1){
            $result = 'yes';
        }
        return $result;
    }

    private function _projectTask(){
        $this->loadModel('ProjectTask');
        $project_id = $this->_projects();
        $projectTasks = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'allow_request')
            ));
        return $projectTasks;
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

    private function _checkAbsence($date, $employs){
        $this->loadModel('AbsenceRequest');

        $requestQuery = $this->AbsenceRequest->find("count", array(
                'recursive'     => -1,
                "conditions"    => array(
                    'date'      => $date,
                    'employee_id' => $employs
                )
        ));

        return $requestQuery;
    }

    private function _checkHoliday($date){
        list($_start, $_end) = $this->_parseParams();
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
        if (!($params = $this->_getProfits($profiltId))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $this->loadModel('Holiday');

        $results = $this->Holiday->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                        'OR' => array(
                            'date' => $date,
                            'repeat' => $date
                        ),
                        'company_id' => $employeeName['company_id']
                    )
            ));

        return $results;
    }

    private function _getDataCurrent($weeks = null){
        list($_start, $_end) = $this->_parseParams();
        $this->loadModel('ActivityRequest');
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;

        if (!($params = $this->_getProfits($profiltId))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;


        $forcastsQuery = $this->ActivityForecast->find(
            "all",
            array(
                'recursive'     => -1,
                "conditions"    => array(
                    'date BETWEEN ? AND ?'  => array($_start, $_end),
                    'employee_id'           => array_keys($employees)
                )
            )
        );
        $forecasts = Set::combine(
            $forcastsQuery,
            '{n}.ActivityForecast.date',
            '{n}.ActivityForecast',
            '{n}.ActivityForecast.employee_id'
        );
        $results = $setDatas = array();
        foreach($forecasts as $id => $forecast){
            foreach($forecast as $time => $data){
                if(empty($weeks)){
                    $sDate = mktime(0, 0, 0, date("m", $data['date']), date("d", $data['date'])+7, date("Y", $data['date']));
                    $setDatas['date'] = $sDate;
                    $setDatas['activity_am'] = $data['activity_am'];
                    $setDatas['activity_pm'] = $data['activity_pm'];
                    $setDatas['am_model'] = $data['am_model'];
                    $setDatas['pm_model'] = $data['pm_model'];
                    $setDatas['employee_id'] = $data['employee_id'];
                    $results[] = $setDatas;
                } else {
                    $sDate = $data['date'];
                    for($i = 0; $i < $weeks; $i++){
                        $sDate = isset($sDate) ? $sDate : $data['date'];
                        $sDate = mktime(0, 0, 0, date("m", $sDate), date("d", $sDate)+7, date("Y", $sDate));
                        $setDatas['date'] = $sDate;
                        $setDatas['activity_am'] = $data['activity_am'];
                        $setDatas['activity_pm'] = $data['activity_pm'];
                        $setDatas['am_model'] = $data['am_model'];
                        $setDatas['pm_model'] = $data['pm_model'];
                        $setDatas['employee_id'] = $data['employee_id'];
                        $results[] = $setDatas;
                    }
                }
            }
        }
        return $results;
    }

    public function init_week(){
        $result = 'true';
        extract(array_merge(array(
                    'number' => null,), $this->params['url']));
        if (!empty($number)) {
            $datas = $this->_getDataCurrent($number);
        } else {
            $datas = $this->_getDataCurrent();
        }
        if(!empty($datas)){
            foreach($datas as $data){
                $check = $this->ActivityForecast->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'date' => $data['date'],
                        'employee_id' => $data['employee_id']
                    )
                ));
                if($check != 0){
                    $result = 'false';
                }
            }
        }
        $this->set(compact('result'));
    }

    public function dup_week(){
        $result = array();
        extract(array_merge(array(
                    'number' => null,), $this->params['url']));
        if (!empty($number)) {
            $datas = $this->_getDataCurrent($number);
        } else {
            $datas = $this->_getDataCurrent();
        }
        if(!empty($datas)){
            foreach($datas as $data){
                $absences = $this->_checkAbsence($data['date'], $data['employee_id']);
                $holiday = $this->_checkHoliday($data['date']);
                if($holiday == 0){
                    if($absences == 0){
                        $check = $this->ActivityForecast->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'date' => $data['date'],
                                'employee_id' => $data['employee_id']
                            ),
                            'fields' => array('id')
                        ));
                        $check = isset($check) ? $check['ActivityForecast']['id'] : '';
                        if(!empty($check)){
                            $this->ActivityForecast->id = $check;
                            $result[] = $this->ActivityForecast->save($data);
                        } else {
                            $this->ActivityForecast->create();
                            $result[] = $this->ActivityForecast->save($data);
                        }
                    } else {
                        //do no thing
                        //Nhung ngay da co absence request nen k cho luu
                    }
                } else {
                    //do nothing
                }
            }
        }
        $this->set(compact('result'));
    }

    private function _workloadFromTaskNew($employees = array(), $startDates = null, $endDate = null){
        echo 'Lam sau';
        exit;
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('AbsenceRequest');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        list($isManage, $employeeName) = $this->_getProfitEmployee($getDataByPath);
        /**
         * Get activity has activated = YES
         */
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array('Activity.activated' => 1, 'company_id' => $employeeName['company_id']),
            'fields' => array('id', 'project')
        ));
        /**
         * Format Start Date and End Date
         */
        $_startDates = date('Y-m-d', $startDates);
        $_endDate = date('Y-m-d', $endDate);
        /**
         * Lay cac project task trong khoang start date to end date va thuoc employee dang view
         * Va cac project dang hoat dong.
         */
        $projectTasks = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ProjectTask.project_id' => $activities,
                'NOT' => array(
                    'OR' => array(
                        'ProjectTask.task_start_date > ?' => $_endDate,
                        'ProjectTask.task_end_date < ?' => $_startDates
                    )
                ),
                'AND' => array(
                    'ProjectTaskEmployeeRefer.reference_id' => $employees,
                    'ProjectTaskEmployeeRefer.is_profit_center' => 0
                )
            ),
            'joins' => array(
                array(
                    'table' => 'activity_tasks',
                    'alias' => 'ActivityTask',
                    'conditions' => array('ActivityTask.project_task_id = ProjectTaskEmployeeRefer.project_task_id')
                )
            ),
            'fields' => array('*'),
            'order'=>array('ProjectTask.project_id')
        ));
        $taskIdOnes = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ActivityTask.id') : array();
        /**
         * Lay cac activity task trong khoang start date to end date va thuoc employee dang view
         * Va cac activity dang hoat dong.
         */
        $actvityTasks = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ActivityTask.activity_id' => array_keys($activities),
                'ActivityTask.project_task_id' => null,
                'NOT' => array(
                    'OR' => array(
                        'ActivityTask.task_start_date > ?' => $endDate,
                        'ActivityTask.task_end_date < ?' => $startDates
                    )
                ),
                'AND' => array(
                    'ActivityTaskEmployeeRefer.reference_id' => $employees,
                    'ActivityTaskEmployeeRefer.is_profit_center' => 0
                )
            ),
            'order'=>array('ActivityTask.activity_id')
        ));
        $taskIdTwos = !empty($actvityTasks) ? Set::classicExtract($actvityTasks, '{n}.ActivityTask.id') : array();
        $taskIdOnes = array_merge($taskIdOnes, $taskIdTwos);
        /**
         * Lay consumed cua cac task tim duoc
         */
         /*
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'employee_id', 'task_id', 'date', 'value'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $employeeName['company_id'],
                'employee_id' => $employees,
                'date BETWEEN ? AND ?'  => array($startDates, $endDate),
                'NOT' => array('value' => 0, "task_id" => null),
                'task_id' => $taskIdOnes
            )
        ));
        */
        /**
         * Lay absence cua employee dang view
         */
        $absenceRequests = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            "conditions" => array(
                'date BETWEEN ? AND ?' => array($startDates, $endDate),
                'employee_id' => $employees
            ),
            'fields' => array('id', 'date', 'absence_pm', 'absence_am', 'response_am', 'response_pm', 'employee_id')
        ));
        $absenceRequests = !empty($absenceRequests) ? Set::combine($absenceRequests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id') : array();

    }


    private function _workloadFromTask($employee_id, $_idPc, $star, $end,$config=null,$typeSelect=null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('Project');
        $this->loadModel('ProjectPhase');
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ActivityTask');
        $this->loadModel('Activity');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ProjectPart');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        list($isManage, $employeeName) = $this->_getProfitEmployee($getDataByPath);
        $company_id = $employeeName['company_id'];
        $activityActivatedYes = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('Activity.activated' => 1, 'company_id' => $company_id),
                'fields' => array('id', 'id')
            ));
        $projectActivatedYes = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activityActivatedYes),
                'fields' => array('activity_id', 'id')
            ));
        //@Huupc: list project Task
        $_startDateProject = date('Y-m-d', $star);
        $_endDateProject = date('Y-m-d', $end);
        $listPrId = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id'),
            'conditions' => array('company_id' => $company_id),
            'order' => array('project_name')
        ));
		
		
        //GET PROJECT STATUS
        $this->loadModel('ProjectStatus');
        $projectStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => array(
				'company_id' => $company_id,
				'status NOT' => 'CL',
				'display' => '1'
		)));
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
		
        $projectTasks = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ProjectTask.project_id' => $projectActivatedYes,
                'ProjectTask.task_status_id' => $projectStatus,
                'NOT' => array(
                    'OR' => array(
                        'ProjectTask.task_start_date > ?' => array($_endDateProject),
                        'ProjectTask.task_end_date < ?' => array($_startDateProject)
                    )
                ),
                'AND' => array(
                    'OR' => array(
                        array(
                            'ProjectTaskEmployeeRefer.reference_id' => $employee_id,
                            'ProjectTaskEmployeeRefer.is_profit_center' => 0
                        ),
                        array(
                            'ProjectTaskEmployeeRefer.reference_id' => $_idPc,
                            'ProjectTaskEmployeeRefer.is_profit_center' => 1
                        )
                    )
                )
            ),
			'fields' => $taskFields,
            'order'=>array('FIELD(ProjectTask.project_id, '. implode(',', $listPrId).')', 'ProjectTask.task_title' => 'ASC')
        ));
		
        $projectTaskIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.id'));
        $parentProjectIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id') : array();
		$project_id = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_id'));
        // thay doi ngay 18/02/2014: lay danh sach tat ca cac activity task
        $listIdActivityTasks = array();
        $listIdActivityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_task_id'),
            'joins' => array(
                array(
                    'table' => 'activities',
                    'alias'=> 'Activity',
                    'conditions' => array('Activity.id = ActivityTask.activity_id', 'company_id' => $company_id)
                )
            )
        ));
        $activityTasks = array();
        if(!empty($listIdActivityTasks)){
            foreach($listIdActivityTasks as $index => $value){
                if(!empty($value)){
                    $activityTasks[$value] = $index;
                }
            }
        }
		
        //@Huupc: Change 01-11-2013
        $_projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_planed_phase_id', 'project_part_id'),
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias'=> 'Project',
                    'conditions' => array('Project.id = ProjectPhasePlan.project_id', 'company_id' => $company_id)
                )
            )
        ));
        $projectPhasePlans = !empty($_projectPhasePlans) ? Set::combine($_projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id') : array();
        $projectPartPlans = !empty($_projectPhasePlans) ? Set::combine($_projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $setupDatas = array();
        // danh sach cac ProjectTask
        $listProjectTasks = array();
		$listProjectIDs = array();
        foreach($projectTasks as $key => $projectTask){
            $_inPIds = $projectTask['ProjectTask']['id'];
            if(  !in_array($_inPIds, $parentProjectIds) ){
                $_start = !empty($projectTask['ProjectTask']['task_start_date']) ? ($projectTask['ProjectTask']['task_start_date']) : 0;
                $_end = !empty($projectTask['ProjectTask']['task_end_date']) ? ($projectTask['ProjectTask']['task_end_date']) : 0;
                $checkTaskLink = isset($activityTasks[$projectTask['ProjectTask']['id']]) ? $activityTasks[$projectTask['ProjectTask']['id']] : 0;
                if($checkTaskLink != 0){
                    //$setupDatas[$key]['p_task_id'] = $projectTask['ProjectTask']['id'];
					// Phần này không thể lấy key là task_id vì 1 task có thể vừa assign cho employee, vừa assign cho PC của employee đó
                    $setupDatas[$key]['task_id'] = $checkTaskLink;
                    $setupDatas[$key]['employee_id'] = $projectTask['ProjectTaskEmployeeRefer']['reference_id'];
                    $setupDatas[$key]['task_title'] = $projectTask['ProjectTask']['task_title'];
                    $setupDatas[$key]['start'] = $_start;
                    $setupDatas[$key]['end'] = $_end;
                    $setupDatas[$key]['estimated'] = $projectTask['ProjectTaskEmployeeRefer']['estimated'];
                    $setupDatas[$key]['project_id'] = $projectTask['ProjectTask']['project_id'];
					$listProjectIDs[] = $projectTask['ProjectTask']['project_id'];
                    $phaseId = isset($projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key]['phase_id'] = $phaseId;
                    $partId = isset($projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key]['part_id'] = $partId;
                    $setupDatas[$key]['nct'] = isset($projectTask['ProjectTask']['is_nct']) ? $projectTask['ProjectTask']['is_nct'] : 0;
                    $listProjectTasks[$projectTask['ProjectTask']['id']] = $projectTask['ProjectTask']['task_title'];
                    $setupDatas[$key]['is_profit_center'] = $projectTask['ProjectTaskEmployeeRefer']['is_profit_center'];
                }
            }
        }
		$listPrId = !empty( $listProjectIDs ) ?  array_unique( $listProjectIDs) : $listPrId;
        //@Huupc: list activity task
        $listAcId = $this->Activity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id'),
            'conditions' => array('company_id' => $company_id),
            'order' => array('name')
        ));
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
		
        $actiTasks = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ActivityTask.activity_id' => $activityActivatedYes,
                'ActivityTask.project_task_id' => null,
				'ActivityTask.task_status_id' => $projectStatus,
                'NOT' => array(
                    'OR' => array(
                        'ActivityTask.task_start_date > ?' => array($end),
                        'ActivityTask.task_end_date < ?' => array($star)
                    )
                ),
                'AND' => array(
                    'OR' => array(
                        array(
                            'ActivityTaskEmployeeRefer.reference_id' => $employee_id,
                            'ActivityTaskEmployeeRefer.is_profit_center' => 0
                        ),
                        array(
                            'ActivityTaskEmployeeRefer.reference_id' => $_idPc,
                            'ActivityTaskEmployeeRefer.is_profit_center' => 1
                        )
                    )
                )
            ),
			'fields' => $act_fields,
            'order'=>array('FIELD(ActivityTask.activity_id, '. implode(',', $listAcId).')', 'ActivityTask.name' => 'ASC')
        ));
        $parentActivityIds = !empty($actiTasks) ? Set::classicExtract($actiTasks, '{n}.ActivityTask.parent_id') : array();
        $setupDatasAT = array();
        // danh sach cac ActivityTask
        $listActivityTasks = array();
		$actiId = array();
        foreach($actiTasks as $key => $actiTask){
            $_inAIds = $actiTask['ActivityTask']['id'];
            if( !in_array($_inAIds, $parentActivityIds)) {
                $_startAT = !empty($actiTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_start_date']) : 0;
                $_endAT = !empty($actiTask['ActivityTask']['task_end_date']) ? date('Y-m-d',$actiTask['ActivityTask']['task_end_date']) : 0;
                $setupDatasAT[$key]['task_id'] = $actiTask['ActivityTask']['id'];
                $setupDatasAT[$key]['employee_id'] = $actiTask['ActivityTaskEmployeeRefer']['reference_id'];
                $setupDatasAT[$key]['task_title'] = $actiTask['ActivityTask']['name'];
                $setupDatasAT[$key]['start'] = $_startAT;
                $setupDatasAT[$key]['end'] = $_endAT;
                $setupDatasAT[$key]['estimated'] = $actiTask['ActivityTaskEmployeeRefer']['estimated'];
                $setupDatasAT[$key]['project_id'] = 0;
                $setupDatasAT[$key]['phase_id'] = 0;
                $setupDatasAT[$key]['activity_id'] = $actiTask['ActivityTask']['activity_id'];
				$actiId[] = $actiTask['ActivityTask']['activity_id'];
                $setupDatasAT[$key]['nct'] = isset($actiTask['ActivityTask']['is_nct']) ? $actiTask['ActivityTask']['is_nct'] : 0;
                $listActivityTasks[$actiTask['ActivityTask']['id']] = $actiTask['ActivityTask']['name'];
				
            }
        }
        $setupDatas = array_merge($setupDatas, $setupDatasAT);
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_name'),
            'conditions' => array(
				'company_id' => $company_id,
				'id' => $listPrId // Huyynh them
			)
        ));
        $projectPhases = $this->ProjectPhase->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'name'),
            'conditions' => array(
				'company_id' => $company_id
			)
        ));
        $projectParts = $this->ProjectPart->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'title'),
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias'=> 'Project',
                    'conditions' => array('Project.id = ProjectPart.project_id', 'company_id' => $company_id)
                )
            )
        ));
		
        // $actiId = !empty($projectActivatedYes) ? array_merge($actiId, array_keys($projectActivatedYes)) : $actiId;
        $actiId = !empty($actiId) ? array_unique($actiId) : array();
        $_activities = $this->Activity->find('all', array(
                'recursive' => -1,
                'conditions' => array(
					'OR' => array( 
						'Activity.id' => $actiId,
						'Activity.project' => $listPrId // Huyynh them
					),	
				),
                'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id')
        ));
        $activities = $activityGroups = $familyIds = array();
        if(!empty($_activities)){
            foreach($_activities as $_activity){
                $dx = $_activity['Activity'];
                if(!empty($dx['family_id'])){
                    $familyIds[$dx['family_id']] = $dx['family_id'];
                }
                if(!empty($dx['subfamily_id'])){
                    $familyIds[$dx['subfamily_id']] = $dx['subfamily_id'];
                }
                $activities[$dx['id']] = !empty($dx['short_name']) ? $dx['short_name'] : '';
                $activityGroups[$dx['id']] = $dx;
            }
        }
        /**
         * Lay thong tin danh sach family cua company
         */
        $this->loadModel('Family');
        $families = $this->Family->find('list', array(
            'recursive' => -1,
            'conditions' => array('Family.id' => $familyIds),
            'fields' => array('id', 'name')
        ));

        $endDatas = array();
        $endDatas['results'] = !empty($setupDatas) ? $setupDatas : array();
        $endDatas['families'] = !empty($families) ? $families : array();
        $endDatas['activityGroups'] = !empty($activityGroups) ? $activityGroups : array();
        $endDatas['projectLinkedActivity'] = !empty($projectActivatedYes) ? array_flip($projectActivatedYes) : array();
        $endDatas['activities'] = !empty($activities) ? $activities : array();
        $endDatas['projects'] = !empty($projects) ? $projects : array();
        $endDatas['projectPhases'] = !empty($projectPhases) ? $projectPhases : array();
        $endDatas['projectParts'] = !empty($projectParts) ? $projectParts : array();
        $endDatas['listProjectTasks'] = !empty($listProjectTasks) ? $listProjectTasks : array();
        $endDatas['listActivityTasks'] = !empty($listActivityTasks) ? $listActivityTasks : array();
        $endDatas['listIdActivityTasks'] = !empty($listIdActivityTasks) ? $listIdActivityTasks : array();

        return $endDatas;
    }
    //CREATED BY VINGUYEN 10/06/2014//
    private function _workloadFromTaskForecasts($employee, $star, $end,$config=null,$typeSelect=null, $profit = null){
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('Project');
        $this->loadModel('ProjectPhase');
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ActivityTask');
        $this->loadModel('Activity');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('ProjectPart');
        $this->loadModel('ProjectStatus');

        list($isManage, $employeeName) = $this->_getProfitEmployee();
        $company_id = $employeeName['company_id'];
        if($config==null){
            $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'employee_id', 'task_id', 'date', 'value'),
            'conditions' => array('status' => 2, 'company_id' => $company_id, 'employee_id' => array_keys($employee), 'date BETWEEN ? AND ?'    => array($star, $end), 'NOT' => array('value' => 0, "task_id" => null))));
        }else{
             $activityRequests = array();
        }
        $requestQuerys = $this->AbsenceRequest->find("all", array(
                'recursive' => -1,
                "conditions" => array('date BETWEEN ? AND ?' => array($star, $end), 'employee_id' => array_keys($employee))));
        $requestQuery = array();
        if(!empty($requestQuerys)){
            foreach($requestQuerys as $_requestQuery){
                $dx = $_requestQuery['AbsenceRequest'];
                $requestQuery[$dx['employee_id']][$dx['date']]['am'] = $dx['response_am'];
                $requestQuery[$dx['employee_id']][$dx['date']]['pm'] = $dx['response_pm'];
            }
        }

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
                            if($employ == $activityRequest['employee_id'] && $task == $activityRequest['task_id'] && $time == $activityRequest['date'])
                            $_requests[$employ][$task][$time] = $activityRequest['value'];
                        }
                    }
                }
            }
        }
        $activityActivatedYes = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('Activity.activated' => 1),
                'fields' => array('id', 'id')
            ));

        $projectActivatedYes = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activityActivatedYes),
                'fields' => array('activity_id', 'id')
            ));
        $_startDateProject = date('Y-m-d', $star);
        $_endDateProject = date('Y-m-d', $end);
       
        $projectTasks = $this->ProjectTask->ProjectTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                //'ProjectTask.project_id' => $projectActivatedYes, // cai nay
                //'ProjectTask.project_id' => $projectIds,
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
                )
            ),
            'order'=>array('ProjectTask.project_id')
        ));
        $projectTaskIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.id'));
        // thay doi ngay 18/02/2014: lay danh sach tat ca cac activity task
        $listIdActivityTasks = array();
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('project_task_id','id'),
            'conditions' => array('project_task_id <>' => null )
        ));
        //@Huupc: Change 01-11-2013
        $_projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_planed_phase_id', 'project_part_id')
        ));
        $projectPhasePlans = !empty($_projectPhasePlans) ? Set::combine($_projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id') : array();
        $projectPartPlans = !empty($_projectPhasePlans) ? Set::combine($_projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $setupDatas = $setupDatasNT = array();
        //GET PROJECT STATUS
        $this->loadModel('ProjectStatus');
        $projectStatus = array();
        $projectStatus = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id','display', 'status'),
            'conditions' => array('company_id' => $company_id))
        );
        $status = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus.status') : array();
        $projectStatus = !empty($projectStatus) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus.display') : array();
        //ONLY ALLOW PROJECT TASK STATUS FOR SCREEN MY DIARY
        $checkMyDiary = false;
        if($this->params['url']=='activity_forecasts/my_diary')
        {
            $checkMyDiary = true ;
        }
        //END
        // LAY CAC TASK KHONG ASSIGN BEN PROJECT TASK
        $pTaskNotAssigns = array();
        if($profit == -1){
            $pTaskNotAssigns = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectTask.project_id' => $projectActivatedYes,
                    //'ProjectTask.project_id' => $projectIds,
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
                    'ProjectTaskEmployeeRefer.project_task_id IS NULL',
                    'Task.parent_id IS NULL',
                    'Project.company_id' => $company_id
                ),
                'joins' => array(
                    array(
                        'table' => 'project_task_employee_refers',
                        'alias' => 'ProjectTaskEmployeeRefer',
                        'type' => 'LEFT',
                        'conditions' => array('ProjectTaskEmployeeRefer.project_task_id = ProjectTask.id')
                    ),
                    array(
                        'table' => 'project_tasks',
                        'alias' => 'Task',
                        'type' => 'LEFT OUTER',
                        'conditions' => array('Task.parent_id = ProjectTask.id')
                    ),
                    array(
                        'table' => 'projects',
                        'alias' => 'Project',
                        'type' => 'LEFT',
                        'conditions' => array('Project.id = ProjectTask.project_id')
                    ),
                ),
                'order'=>array('ProjectTask.project_id')
            ));
        }

        // danh sach cac ProjectTask
        $pTaskIds = array();
        if($checkMyDiary)
        {
            foreach($projectTasks as $key => $projectTask){
                //ONLY ALLOW PROJECT TASK STATUS = 0
                if(isset($projectStatus[$projectTask['ProjectTask']['task_status_id']])&&$projectStatus[$projectTask['ProjectTask']['task_status_id']]==1)
                {
                    continue;
                }
                $_start = !empty($projectTask['ProjectTask']['task_start_date']) ? ($projectTask['ProjectTask']['task_start_date']) : 0;
                $_end = !empty($projectTask['ProjectTask']['task_end_date']) ? ($projectTask['ProjectTask']['task_end_date']) : 0;
                $checkTaskLink = isset($activityTasks[$projectTask['ProjectTask']['id']]) ? $activityTasks[$projectTask['ProjectTask']['id']] : 0;
                $st = !empty($status[$projectTask['ProjectTask']['task_status_id']]) ? $status[$projectTask['ProjectTask']['task_status_id']] : 'IP';
                $pTaskIds[] = $projectTask['ProjectTask']['id'];
                $empId = ($projectTask['ProjectTaskEmployeeRefer']['is_profit_center'] == 1) ? 'tNotAffec' : $projectTask['ProjectTaskEmployeeRefer']['reference_id'];
                if($checkTaskLink != 0){
                    $setupDatas[$key]['task_id'] = $checkTaskLink;
                    $setupDatas[$key]['employee_id'] = $empId;
                    $setupDatas[$key]['task_title'] = $projectTask['ProjectTask']['task_title'];
                    $setupDatas[$key]['start'] = $_start;
                    $setupDatas[$key]['end'] = $_end;
                    $setupDatas[$key]['estimated'] = $projectTask['ProjectTaskEmployeeRefer']['estimated'];
                    $setupDatas[$key]['project_id'] = $projectTask['ProjectTask']['project_id'];
                    $phaseId = isset($projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key]['phase_id'] = $phaseId;
                    $partId = isset($projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key]['part_id'] = $partId;
                    $setupDatas[$key]['nct'] = isset($projectTask['ProjectTask']['is_nct']) ? $projectTask['ProjectTask']['is_nct'] : 0;
                    $setupDatas[$key]['st'] = $st;
                    $setupDatas[$key]['is_pc'] = $projectTask['ProjectTaskEmployeeRefer']['is_profit_center'];
                    $setupDatas[$key]['p_task_id'] = $projectTask['ProjectTask']['id'];
                }
            }
            foreach($pTaskNotAssigns as $key => $pTaskNotAssign){
                $dx = $pTaskNotAssign['ProjectTask'];
                if(isset($projectStatus[$dx['task_status_id']])&&$projectStatus[$dx['task_status_id']]==1)
                {
                    continue;
                }
                $_start = !empty($dx['task_start_date']) ? ($dx['task_start_date']) : 0;
                $_end = !empty($dx['task_end_date']) ? ($dx['task_end_date']) : 0;
                $checkTaskLink = isset($activityTasks[$dx['id']]) ? $activityTasks[$dx['id']] : 0;
                $st = !empty($status[$dx['task_status_id']]) ? $status[$dx['task_status_id']] : 'IP';
                $pTaskIds[] = $dx['id'];
                if($checkTaskLink != 0){
                    $setupDatasNT[$key]['task_id'] = $checkTaskLink;
                    $setupDatasNT[$key]['employee_id'] = 'tNotAffec';
                    $setupDatasNT[$key]['task_title'] = $dx['task_title'];
                    $setupDatasNT[$key]['start'] = $_start;
                    $setupDatasNT[$key]['end'] = $_end;
                    $setupDatasNT[$key]['estimated'] = $dx['estimated'];
                    $setupDatasNT[$key]['project_id'] = $dx['project_id'];
                    $phaseId = isset($projectPhasePlans[$dx['project_planed_phase_id']]) ? $projectPhasePlans[$dx['project_planed_phase_id']] : 0;
                    $setupDatasNT[$key]['phase_id'] = $phaseId;
                    $partId = isset($projectPartPlans[$dx['project_planed_phase_id']]) ? $projectPartPlans[$dx['project_planed_phase_id']] : 0;
                    $setupDatasNT[$key]['part_id'] = $partId;
                    $setupDatasNT[$key]['nct'] = isset($dx['is_nct']) ? $dx['is_nct'] : 0;
                    $setupDatasNT[$key]['st'] = $st;
                    $setupDatasNT[$key]['is_pc'] = 0;
                    $setupDatasNT[$key]['p_task_id'] = $dx['id'];
                }
            }
        }
        else
        {
            foreach($projectTasks as $key => $projectTask){
                $_start = !empty($projectTask['ProjectTask']['task_start_date']) ? ($projectTask['ProjectTask']['task_start_date']) : 0;
                $_end = !empty($projectTask['ProjectTask']['task_end_date']) ? ($projectTask['ProjectTask']['task_end_date']) : 0;
                $checkTaskLink = isset($activityTasks[$projectTask['ProjectTask']['id']]) ? $activityTasks[$projectTask['ProjectTask']['id']] : 0;
                $st = !empty($status[$projectTask['ProjectTask']['task_status_id']]) ? $status[$projectTask['ProjectTask']['task_status_id']] : 'IP';
                $pTaskIds[] = $projectTask['ProjectTask']['id'];
                $empId = ($projectTask['ProjectTaskEmployeeRefer']['is_profit_center'] == 1) ? 'tNotAffec' : $projectTask['ProjectTaskEmployeeRefer']['reference_id'];
                if($checkTaskLink != 0){
                    $setupDatas[$key]['task_id'] = $checkTaskLink;
                    $setupDatas[$key]['employee_id'] = $empId;
                    $setupDatas[$key]['task_title'] = $projectTask['ProjectTask']['task_title'];
                    $setupDatas[$key]['start'] = $_start;
                    $setupDatas[$key]['end'] = $_end;
                    $setupDatas[$key]['estimated'] = $projectTask['ProjectTaskEmployeeRefer']['estimated'];
                    $setupDatas[$key]['project_id'] = $projectTask['ProjectTask']['project_id'];
                    $phaseId = isset($projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPhasePlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key]['phase_id'] = $phaseId;
                    $partId = isset($projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']]) ? $projectPartPlans[$projectTask['ProjectTask']['project_planed_phase_id']] : 0;
                    $setupDatas[$key]['part_id'] = $partId;
                    $setupDatas[$key]['nct'] = isset($projectTask['ProjectTask']['is_nct']) ? $projectTask['ProjectTask']['is_nct'] : 0;
                    $setupDatas[$key]['st'] = $st;
                    $setupDatas[$key]['is_pc'] = $projectTask['ProjectTaskEmployeeRefer']['is_profit_center'];
                    $setupDatas[$key]['p_task_id'] = $projectTask['ProjectTask']['id'];
                }
            }
            foreach($pTaskNotAssigns as $key => $pTaskNotAssign){
                $dx = $pTaskNotAssign['ProjectTask'];
                $_start = !empty($dx['task_start_date']) ? ($dx['task_start_date']) : 0;
                $_end = !empty($dx['task_end_date']) ? ($dx['task_end_date']) : 0;
                $checkTaskLink = isset($activityTasks[$dx['id']]) ? $activityTasks[$dx['id']] : 0;
                $st = !empty($status[$dx['task_status_id']]) ? $status[$dx['task_status_id']] : 'IP';
                $pTaskIds[] = $dx['id'];
                if($checkTaskLink != 0){
                    $setupDatasNT[$key]['task_id'] = $checkTaskLink;
                    $setupDatasNT[$key]['employee_id'] = 'tNotAffec';
                    $setupDatasNT[$key]['task_title'] = $dx['task_title'];
                    $setupDatasNT[$key]['start'] = $_start;
                    $setupDatasNT[$key]['end'] = $_end;
                    $setupDatasNT[$key]['estimated'] = $dx['estimated'];
                    $setupDatasNT[$key]['project_id'] = $dx['project_id'];
                    $phaseId = isset($projectPhasePlans[$dx['project_planed_phase_id']]) ? $projectPhasePlans[$dx['project_planed_phase_id']] : 0;
                    $setupDatasNT[$key]['phase_id'] = $phaseId;
                    $partId = isset($projectPartPlans[$dx['project_planed_phase_id']]) ? $projectPartPlans[$dx['project_planed_phase_id']] : 0;
                    $setupDatasNT[$key]['part_id'] = $partId;
                    $setupDatasNT[$key]['nct'] = isset($dx['is_nct']) ? $dx['is_nct'] : 0;
                    $setupDatasNT[$key]['st'] = $st;
                    $setupDatasNT[$key]['is_pc'] = 0;
                    $setupDatasNT[$key]['p_task_id'] = $dx['id'];
                }
            }
        }
        //@Huupc: list activity task
        $actiTasks = $this->ActivityTask->ActivityTaskEmployeeRefer->find('all', array(
            'conditions' => array(
                'ActivityTask.activity_id' => $activityActivatedYes,
                'ActivityTask.project_task_id' => null,
                array(
                    'OR' => array(
                        'ActivityTask.task_start_date BETWEEN ? AND ?' => array($star, $end),
                        'ActivityTask.task_end_date BETWEEN ? AND ?' => array($star, $end),
                        array(
                            'ActivityTask.task_start_date < ?' => array($star),
                            'ActivityTask.task_end_date > ?' => array($star)
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
            'order'=>array('ActivityTask.activity_id')
        ));
        // LAY CAC TASK KHONG ASSIGN BEN ACTIVITY TASK
        $aTaskNotAssigns = array();
        if($profit == -1){
            $aTaskNotAssigns = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'ActivityTask.activity_id' => $activityActivatedYes,
                    'ActivityTask.project_task_id' => null,
                    'OR' => array(
                        'ActivityTask.task_start_date BETWEEN ? AND ?' => array($star, $end),
                        'ActivityTask.task_end_date BETWEEN ? AND ?' => array($star, $end),
                        array(
                            'ActivityTask.task_start_date < ?' => array($star),
                            'ActivityTask.task_end_date > ?' => array($star)
                        ),
                        array(
                            'ActivityTask.task_start_date < ?' => array($end),
                            'ActivityTask.task_end_date > ?' => array($end)
                        )
                    ),
                    'ActivityTaskEmployeeRefer.activity_task_id IS NULL',
                    'Task.parent_id IS NULL',
                    'Activity.company_id' => $company_id
                ),
                'joins' => array(
                    array(
                        'table' => 'activity_task_employee_refers',
                        'alias' => 'ActivityTaskEmployeeRefer',
                        'type' => 'LEFT',
                        'conditions' => array('ActivityTaskEmployeeRefer.activity_task_id = ActivityTask.id')
                    ),
                    array(
                        'table' => 'activity_tasks',
                        'alias' => 'Task',
                        'type' => 'LEFT OUTER',
                        'conditions' => array('Task.parent_id = ActivityTask.id')
                    ),
                    array(
                        'table' => 'activities',
                        'alias' => 'Activity',
                        'type' => 'LEFT',
                        'conditions' => array('Activity.id = ActivityTask.activity_id')
                    ),
                ),
                'order'=>array('ActivityTask.activity_id')
            ));
        }
        $setupDatasAT = $setupDatasATNS = array();
        // danh sach cac ActivityTask
        $actiId=array();
        $aTaskIds = array();
        if($checkMyDiary)
        {
            foreach($actiTasks as $key => $actiTask){
                //ONLY ALLOW PROJECT TASK STATUS = 0
                $aTaskIds[] = $actiTask['ActivityTask']['id'];
                if(isset($projectStatus[$actiTask['ActivityTask']['task_status_id']])&&$projectStatus[$actiTask['ActivityTask']['task_status_id']]==1)
                {
                    continue;
                }
                $st = !empty($status[$actiTask['ActivityTask']['task_status_id']]) ? $status[$actiTask['ActivityTask']['task_status_id']] : 'IP';
                $_startAT = !empty($actiTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_start_date']) : 0;
                $_endAT = !empty($actiTask['ActivityTask']['task_end_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_end_date']) : 0;
                $empId = ($actiTask['ActivityTaskEmployeeRefer']['is_profit_center'] == 1) ? 'tNotAffec' : $actiTask['ActivityTaskEmployeeRefer']['reference_id'];
                $setupDatasAT[$key]['task_id'] = $actiTask['ActivityTask']['id'];
                $setupDatasAT[$key]['employee_id'] = $empId;
                $setupDatasAT[$key]['task_title'] = $actiTask['ActivityTask']['name'];
                $setupDatasAT[$key]['start'] = $_startAT;
                $setupDatasAT[$key]['end'] = $_endAT;
                $setupDatasAT[$key]['estimated'] = $actiTask['ActivityTaskEmployeeRefer']['estimated'];
                $setupDatasAT[$key]['project_id'] = 0;
                $setupDatasAT[$key]['phase_id'] = 0;
                $setupDatasAT[$key]['activity_id'] = $actiTask['ActivityTask']['activity_id'];
                $setupDatasAT[$key]['nct'] = isset($actiTask['ActivityTask']['is_nct']) ? $actiTask['ActivityTask']['is_nct'] : 0;
                if(!in_array($actiTask['ActivityTask']['activity_id'],$actiId))
                {
                    $actiId[]=$actiTask['ActivityTask']['activity_id'];
                }
                $setupDatasAT[$key]['st'] = $st;
                $setupDatasAT[$key]['is_pc'] = $actiTask['ActivityTaskEmployeeRefer']['is_profit_center'];
            }
            foreach($aTaskNotAssigns as $key => $aTaskNotAssign){
                $dx = $aTaskNotAssign['ActivityTask'];
                $aTaskIds[] = $dx['id'];
                if(isset($projectStatus[$dx['task_status_id']])&&$projectStatus[$dx['task_status_id']]==1)
                {
                    continue;
                }
                $_startAT = !empty($dx['task_start_date']) ? date('Y-m-d', $dx['task_start_date']) : 0;
                $_endAT = !empty($dx['task_end_date']) ? date('Y-m-d', $dx['task_end_date']) : 0;
                $st = !empty($status[$dx['task_status_id']]) ? $status[$dx['task_status_id']] : 'IP';

                $setupDatasATNS[$key]['task_id'] = $dx['id'];
                $setupDatasATNS[$key]['employee_id'] = 'tNotAffec';
                $setupDatasATNS[$key]['task_title'] = $dx['name'];
                $setupDatasATNS[$key]['start'] = $_startAT;
                $setupDatasATNS[$key]['end'] = $_endAT;
                $setupDatasATNS[$key]['estimated'] = $dx['estimated'];
                $setupDatasATNS[$key]['project_id'] = 0;
                $setupDatasATNS[$key]['phase_id'] = 0;
                $setupDatasATNS[$key]['activity_id'] = $dx['activity_id'];
                $setupDatasATNS[$key]['nct'] = isset($dx['is_nct']) ? $dx['is_nct'] : 0;
                if(!in_array($dx['activity_id'],$actiId))
                {
                    $actiId[]=$dx['activity_id'];
                }
                $setupDatasATNS[$key]['st'] = $st;
                $setupDatasATNS[$key]['is_pc'] = 0;
            }
        }
        else
        {
            foreach($actiTasks as $key => $actiTask){
                $aTaskIds[] = $actiTask['ActivityTask']['id'];
                $_startAT = !empty($actiTask['ActivityTask']['task_start_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_start_date']) : 0;
                $_endAT = !empty($actiTask['ActivityTask']['task_end_date']) ? date('Y-m-d', $actiTask['ActivityTask']['task_end_date']) : 0;
                $st = !empty($status[$actiTask['ActivityTask']['task_status_id']]) ? $status[$actiTask['ActivityTask']['task_status_id']] : 'IP';
                $empId = ($actiTask['ActivityTaskEmployeeRefer']['is_profit_center'] == 1) ? 'tNotAffec' : $actiTask['ActivityTaskEmployeeRefer']['reference_id'];
                $setupDatasAT[$key]['task_id'] = $actiTask['ActivityTask']['id'];
                $setupDatasAT[$key]['employee_id'] = $empId;
                $setupDatasAT[$key]['task_title'] = $actiTask['ActivityTask']['name'];
                $setupDatasAT[$key]['start'] = $_startAT;
                $setupDatasAT[$key]['end'] = $_endAT;
                $setupDatasAT[$key]['estimated'] = $actiTask['ActivityTaskEmployeeRefer']['estimated'];
                $setupDatasAT[$key]['project_id'] = 0;
                $setupDatasAT[$key]['phase_id'] = 0;
                $setupDatasAT[$key]['activity_id'] = $actiTask['ActivityTask']['activity_id'];
                $setupDatasAT[$key]['nct'] = isset($actiTask['ActivityTask']['is_nct']) ? $actiTask['ActivityTask']['is_nct'] : 0;
                if(!in_array($actiTask['ActivityTask']['activity_id'],$actiId))
                {
                    $actiId[]=$actiTask['ActivityTask']['activity_id'];
                }
                $setupDatasAT[$key]['st'] = $st;
                $setupDatasAT[$key]['is_pc'] = $actiTask['ActivityTaskEmployeeRefer']['is_profit_center'];
            }
            foreach($aTaskNotAssigns as $key => $aTaskNotAssign){
                $dx = $aTaskNotAssign['ActivityTask'];
                $aTaskIds[] = $dx['id'];

                $_startAT = !empty($dx['task_start_date']) ? date('Y-m-d', $dx['task_start_date']) : 0;
                $_endAT = !empty($dx['task_end_date']) ? date('Y-m-d', $dx['task_end_date']) : 0;
                $st = !empty($status[$dx['task_status_id']]) ? $status[$dx['task_status_id']] : 'IP';

                $setupDatasATNS[$key]['task_id'] = $dx['id'];
                $setupDatasATNS[$key]['employee_id'] = 'tNotAffec';
                $setupDatasATNS[$key]['task_title'] = $dx['name'];
                $setupDatasATNS[$key]['start'] = $_startAT;
                $setupDatasATNS[$key]['end'] = $_endAT;
                $setupDatasATNS[$key]['estimated'] = $dx['estimated'];
                $setupDatasATNS[$key]['project_id'] = 0;
                $setupDatasATNS[$key]['phase_id'] = 0;
                $setupDatasATNS[$key]['activity_id'] = $dx['activity_id'];
                $setupDatasATNS[$key]['nct'] = isset($dx['is_nct']) ? $dx['is_nct'] : 0;
                if(!in_array($dx['activity_id'],$actiId))
                {
                    $actiId[]=$dx['activity_id'];
                }
                $setupDatasATNS[$key]['st'] = $st;
                $setupDatasATNS[$key]['is_pc'] = 0;
            }
        }
		
        if(!empty($setupDatas) && empty($setupDatasAT)){
            $setupDatas = $setupDatas;
        } elseif(empty($setupDatas) && !empty($setupDatasAT)){
            $setupDatas = $setupDatasAT;
        } else {
            $setupDatas = array_merge($setupDatas, $setupDatasAT);
        }
        $setupDatas = array_merge($setupDatas, $setupDatasNT, $setupDatasATNS);
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_name')
        ));
        $projectPhases = $this->ProjectPhase->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));
        $projectParts = $this->ProjectPart->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'title')
        ));
        $_activities = $this->Activity->find('all', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $actiId),
                'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id')
            ));
        $activities = $familyIds = array();
        if(!empty($_activities)){
            foreach($_activities as $_activity){
                $dx = $_activity['Activity'];
                if(!empty($dx['family_id'])){
                    $familyIds[$dx['family_id']] = $dx['family_id'];
                }
                if(!empty($dx['subfamily_id'])){
                    $familyIds[$dx['subfamily_id']] = $dx['subfamily_id'];
                }
                $activities[$dx['id']] = !empty($dx['short_name']) ? $dx['short_name'] : '';
            }
        }
        $this->loadModels('ActivityFamily');
        $families = $this->ActivityFamily->find('list', array(
            'recursive' => -1,
            'conditions' => array('ActivityFamily.id' => $familyIds),
            'fields' => array('id', 'name')
        ));
        /**
         * Get Task NCT - forecast screen
         */
        $this->loadModel('NctWorkload');
        // $NCTTasks = $this->NctWorkload->find('all', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'task_date BETWEEN ? AND ?' => array($_startDateProject, $_endDateProject),
        //         'NctWorkload.estimated <>' => 0,
        //         'OR' => array(
        //             'NctWorkload.project_task_id' => $pTaskIds,
        //             'NctWorkload.activity_task_id' => $aTaskIds
        //         ),
        //         array(
        //             'OR' => array(
        //                 array(
        //                     'NctWorkload.reference_id' => array_keys($employee),
        //                     'NctWorkload.is_profit_center' => 0
        //                 ),
        //                 array(
        //                     'NctWorkload.reference_id' => $profit,
        //                     'NctWorkload.is_profit_center' => 1
        //                 )
        //             )
        //         )
        //     )
        // ));
        // $listkNcts = array();
        // if(!empty($NCTTasks)){
        //     foreach($NCTTasks as $NCTTask){
        //         $dx = $NCTTask['NctWorkload'];
        //         $date = strtotime($dx['task_date']);
        //         $checkTaskLink = $dx['activity_task_id'];
        //         if(isset($dx['project_task_id']) && !empty($dx['project_task_id'])){
        //             $checkTaskLink = isset($activityTasks[$dx['project_task_id']]) ? $activityTasks[$dx['project_task_id']] : 0;
        //         }
        //         $dx['reference_id'] = !empty($dx['is_profit_center']) ? 'tNotAffec' : $dx['reference_id'];
        //         if(!isset($listkNcts[$checkTaskLink][$dx['reference_id']][$date])){
        //             $listkNcts[$checkTaskLink][$dx['reference_id']][$date] = 0;
        //         }
        //         $listkNcts[$checkTaskLink][$dx['reference_id']][$date] = $dx['estimated'];
        //     }
        // }
		
        $NCTTasks = $this->NctWorkload->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'NctWorkload.estimated <>' => 0,
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
                    // 'NctWorkload.reference_id' => array_keys($employee),
                    // 'NctWorkload.is_profit_center' => 0
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
		
        $listkNcts = array();
        if(!empty($NCTTasks)){
            foreach($NCTTasks as $NCTTask){
                $dx = $NCTTask['NctWorkload'];
                $nStartDate = strtotime($dx['task_date']);
                $nEndDate = strtotime($dx['end_date']);
                $checkTaskLink = $dx['activity_task_id'];
                if( !empty($dx['project_task_id']) ){
                    $checkTaskLink = isset($activityTasks[$dx['project_task_id']]) ? $activityTasks[$dx['project_task_id']] : 0;
                }
                $dx['reference_id'] = !empty($dx['is_profit_center']) ? 'tNotAffec' : $dx['reference_id'];
                if( !empty($listkNcts[$checkTaskLink][$dx['reference_id']]) ){
                    $listkNcts[$checkTaskLink][$dx['reference_id']] = $this->NctWorkload->divideWorkload($dx['estimated'], $nStartDate, $nEndDate, $star, $end) + $listkNcts[$checkTaskLink][$dx['reference_id']];
					
                } else {
                    $listkNcts[$checkTaskLink][$dx['reference_id']] = $this->NctWorkload->divideWorkload($dx['estimated'], $nStartDate, $nEndDate, $star, $end);
					
                }
            }
        }
        $datas = array();
        $workdays = ClassRegistry::init('Workday')->getOptions($company_id);
        if(!empty($setupDatas)){
            $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($star, $end, $company_id);
            //$workdays = ClassRegistry::init('Workday')->getOptions($company_id);
            foreach($setupDatas as $key => $setupData){
                $dates = $this->_getWorkingDays($setupData['start'], $setupData['end']);
                ///$dateTemps = $dates;
                $_startVl = !empty($setupData['start']) ? strtotime($setupData['start']) : 0;
                $_endVl = !empty($setupData['end']) ? strtotime($setupData['end']) : 0;
                $holidaysTmp = ClassRegistry::init('Holiday')->getOptionHolidays($_startVl, $_endVl, $company_id);
                //GET ABSENCE OF Employee
                $getAbsencesAm = $this->AbsenceRequest->find("count", array(
                'recursive' => -1,
                "conditions" => array('date BETWEEN ? AND ?' => array($_startVl, $_endVl), 'response_am'=>'validated', 'employee_id' => $setupData['employee_id'])));
                $getAbsencesPm = $this->AbsenceRequest->find("count", array(
                'recursive' => -1,
                "conditions" => array('date BETWEEN ? AND ?' => array($_startVl, $_endVl), 'response_pm'=>'validated', 'employee_id' => $setupData['employee_id'])));
                $getAbsences=($getAbsencesAm+$getAbsencesPm);
                //END
                //MODIFY BY VINGUYEN 31/10/2014
                $countHoliday=0;
                $holidayForNW=array();
                foreach($holidaysTmp as $_key=>$_value)
                {
                    if($_key<=$_endVl&&$_key>=$_startVl)
                    {
                        $countHoliday+=1;
                        $holidayForNW[]=date('m-d-Y',$_key);
                    }
                }
                $countHoliday=$countHoliday*2;
                //END
                $dateTemps=$dates*2-$countHoliday-$getAbsences;//MODIFY BY VINGUYEN 31/10/2014
                $dates = $dates * 2;
                $_workload2Feild=$this->Lib->caculateGlobal($setupData['estimated'],$dateTemps);
                $temp=0;
                $notWorking=$this->_getHoliday($setupData['start'],$setupData['end']);
                $notWorking=array_merge($notWorking,$holidayForNW);
                $notWorking=array_unique($notWorking);
                $limitTimeJoin=$_startVl;
                $startNW=$_startVl;
                while($startNW <= $_endVl){
                    $startNWConvert=date('m-d-Y',$startNW);
                    $_temp=0;
                    foreach($notWorking as $keyNW=>$valueNW)
                    {
                        if($startNWConvert==$valueNW)
                        {
                            $_temp++;
                            break;
                        }
                    }
                    if($temp==$_workload2Feild['number'])
                    {
                        $limitTimeJoin=$startNW+1;
                    }
                    if($_temp==0) $temp++;
                    $startNW = mktime(0, 0, 0, date("m", $startNW), date("d", $startNW)+1, date("Y", $startNW));
                }
                $_start = $star;
                $_end = $end;
                $isBegin=true;
                $count=0;
                while($_start <= $_end){
                    $cSHoliday = strtolower(date("l", $_start));
                    if($workdays[$cSHoliday] == 0){
                        //do nothing
                    } else {
                        if($_startVl <= $_start && $_start <= $_endVl){
                            //foreach(array('am', 'pm') as $tmp){
                                if(!empty($holidays[$_start])){
                                    // do nothing
                                    $datas[$key][$_start]=array();
                                    $datas[$key][$_start]['vacation']=-1;
                                    $datas[$key][$_start]['date'] = $_start;
                                    $datas[$key][$_start]['employee_id'] = $setupData['employee_id'];
                                } else {
                                    $_workloadTemp=0;
                                    $_remainTemp=0;
                                    /*-----DESCRIPTION-----
                                    vacation = -1 : ngay binh thuong
                                    vacation = 1 : nghi ca ngay
                                    vacation = 3 : nghi buoi sang da validate
                                    vacation = 5 : nghi buoi sang da validate, buoi chieu dang waiting
                                    vacation = 7 : nghi buoi chieu da validate
                                    vacation = 9 : nghi buoi chieu da validate, buoi sang dang waiting
                                    vacation = 11 : buoi sang dang waiting, nghi buoi chieu da validate
                                    vacation = 13 : buoi chieu dang waiting, nghi buoi sang da validate
                                    vacation = 2 : ca ngay dang waiting
                                    vacation = 4 : nghi buoi sang dang waiting
                                    vacation = 6 : nghi buoi chieu dang waiting
                                    ---------------------*/
                                    $datas[$key][$_start]['vacation']=-1;
                                    if(!empty($requestQuery[$setupData['employee_id']])){
                                        foreach($requestQuery[$setupData['employee_id']] as $abTime => $checkDate){
                                            if(($abTime == $_start && $checkDate['am'] == 'validated')&&($abTime == $_start && $checkDate['pm'] == 'validated')){
                                                $datas[$key][$_start]['workload'] = 0;
                                                $datas[$key][$_start]['remain'] = 0;
                                                $dateTemps-=1;
                                                $datas[$key][$_start]['vacation']=1;
                                            }
                                            else if(($abTime == $_start && $checkDate['am'] == 'waiting')&&($abTime == $_start && $checkDate['pm'] == 'waiting')){
                                                $datas[$key][$_start]['vacation']=2;
                                            }
                                            else if($abTime == $_start && $checkDate['am'] == 'validated'){
                                                $dateTemps-=0.5;
                                                $datas[$key][$_start]['vacation']=3;
                                                if($abTime == $_start && $checkDate['pm'] == 'waiting')
                                                {
                                                    $datas[$key][$_start]['vacation']=5;
                                                }
                                            }
                                            else if($abTime == $_start && $checkDate['pm'] == 'validated'){
                                                $dateTemps-=0.5;
                                                $datas[$key][$_start]['vacation']=7;
                                                if($abTime == $_start && $checkDate['am'] == 'waiting')
                                                {
                                                    $datas[$key][$_start]['vacation']=9;
                                                }
                                            }
                                            else if($abTime == $_start && $checkDate['am'] == 'waiting'){
                                                $datas[$key][$_start]['vacation']=4;
                                                if($abTime == $_start && $checkDate['pm'] == 'validated')
                                                {
                                                    $dateTemps-=0.5;
                                                    $datas[$key][$_start]['vacation']=11;
                                                }
                                            }
                                            else if($abTime == $_start && $checkDate['pm'] == 'waiting'){
                                                $datas[$key][$_start]['vacation']=6;
                                                if($abTime == $_start && $checkDate['am'] == 'validated')
                                                {
                                                    $dateTemps-=0.5;
                                                    $datas[$key][$_start]['vacation']=13;
                                                }
                                            }
                                        }
                                    }

                                    // get data vacation

                                    $datas[$key][$_start]['st'] = $setupData['st'];
                                    $datas[$key][$_start]['is_pc'] = $setupData['is_pc'];
                                    $datas[$key][$_start]['task_id'] = $setupData['task_id'];
                                    $datas[$key][$_start]['p_task_id'] = isset($setupData['p_task_id']) && !empty($setupData['p_task_id']) ? $setupData['p_task_id'] : 0;
                                    $datas[$key][$_start]['date'] = $_start;
                                    $datas[$key][$_start]['employee_id'] = $setupData['employee_id'];

                                    $namePR = !empty($projects[$setupData['project_id']]) ? $projects[$setupData['project_id']] : '';
                                    $namePP = !empty($projectPhases[$setupData['phase_id']]) ? $projectPhases[$setupData['phase_id']] : '';
                                    $nameTask = !empty($setupData['task_title']) ? $setupData['task_title'] : '';
                                    if($setupData['project_id'] == 0 && $setupData['phase_id'] == 0){
                                        $nameAC = !empty($activities[$setupData['activity_id']]) ? $activities[$setupData['activity_id']] : '';
                                        //$nameAP = !empty($projectPhases[$setupData['phase_id']]) ? $projectPhases[$setupData['phase_id']] : '';
                                        $nameTask = !empty($setupData['task_title']) ? $setupData['task_title'] : '';
                                        /*$groupName = array(
                                            'idAc' => $setupData['activity_id'],
                                            'nameAc' => $nameAC,
                                            'nameTask' => $nameTask,
                                        );*/
                                        $datas[$key][$_start]['idAc'] = $setupData['activity_id'];
                                        $datas[$key][$_start]['nameAc'] = $nameAC;
                                        $datas[$key][$_start]['nameTask'] = $nameTask;
                                    } else {
                                        /*$groupName = array(
                                            'idPr' => $setupData['project_id'],
                                            'namePr' => $namePR,
                                            'idPart' => $setupData['part_id'],
                                            'namePart' => !empty($projectParts[$setupData['part_id']]) ? $projectParts[$setupData['part_id']] : '',
                                            'idPhase' => $setupData['phase_id'],
                                            'namePhase' => $namePP,
                                            'nameTask' => $nameTask
                                        );*/
                                        $datas[$key][$_start]['idPr'] = $setupData['project_id'];
                                        $datas[$key][$_start]['namePr'] = $namePR;
                                        $datas[$key][$_start]['idPart'] = $setupData['part_id'];
                                        $datas[$key][$_start]['namePart'] = !empty($projectParts[$setupData['part_id']]) ? $projectParts[$setupData['part_id']] : '';
                                        $datas[$key][$_start]['idPhase'] = $setupData['phase_id'];
                                        $datas[$key][$_start]['namePhase'] = $namePP;
                                        $datas[$key][$_start]['nameTask'] = $nameTask;
                                    }
                                    //$datas[$key][$_start]['name'] = $groupName;
                                    $_workloadResidual = 0;
                                    if($dates == 0){
                                        $_workload = 0;
                                    } else {
                                        //$_workload = round($setupData['estimated']/$dateTemps, 2);
                                        //$_workloadResidual = ($setupData['estimated']*100)% $dateTemps),2);
                                        //$_workload2Feild=$this->Lib->caculateGlobal($setupData['estimated'],$dateTemps);
                                        $_workload = $_workload2Feild['original'];
                                        $_workloadResidual = $_workload2Feild['remainder'];
                                        $_workloadNumberOfResidual = $_workload2Feild['number'];
                                    }

                                    if(in_array($datas[$key][$_start]['vacation'],array(-1,2,4,6)))
                                    {
                                        // if($isBegin)
                                            // $_workloadDbl=round(($_workload*2), 2)+$_workloadResidual;
                                        // else
                                            // $_workloadDbl=round(($_workload*2), 2);
                                        if($_start<$limitTimeJoin)
                                        {
                                            $_workloadDbl=round(($_workload*2), 2)+$_workloadResidual;
                                        }
                                        else
                                            $_workloadDbl=round(($_workload*2), 2);
                                        $datas[$key][$_start]['workload'] = $_workloadDbl;
                                        if(!empty($_requests)){
                                            $datas[$key][$_start]['consumed'] = isset($_requests[$setupData['employee_id']][$setupData['task_id']][$_start]) ? $_requests[$setupData['employee_id']][$setupData['task_id']][$_start] : 0;
                                            $datas[$key][$_start]['remain'] = $_workloadDbl - $datas[$key][$_start]['consumed'];
                                        } else {
                                            $datas[$key][$_start]['remain'] = $_workloadDbl;
                                        }
                                    }
                                    else if(in_array($datas[$key][$_start]['vacation'],array(3,5,7,9,11,13)))
                                    {
                                        if($_start<$limitTimeJoin)
                                        {
                                            $datas[$key][$_start]['workload'] = round($_workload, 2)+$_workloadResidual;
                                        }
                                        else
                                        $datas[$key][$_start]['workload'] = round($_workload, 2);
                                        if(!empty($_requests)){
                                            $datas[$key][$_start]['consumed'] = isset($_requests[$setupData['employee_id']][$setupData['task_id']][$_start]) ? $_requests[$setupData['employee_id']][$setupData['task_id']][$_start] : 0;
                                            $datas[$key][$_start]['remain'] = round($_workload, 2) - $datas[$key][$_start]['consumed'];
                                        } else {
                                            $datas[$key][$_start]['remain'] = round($_workload, 2);
                                        }
                                    }
                                    
                                    if($datas[$key][$_start]['remain'] < 0){
                                        $datas[$key][$_start]['remain'] = 0;
                                    }
									
									$datas[$key][$_start]['nct'] = 0;
									
									if(!empty($setupData['nct'])){
										if(isset($listkNcts[$setupData['task_id']][$setupData['employee_id']][$_start])){
											$workload = $listkNcts[$setupData['task_id']][$setupData['employee_id']][$_start];
											$datas[$key][$_start]['workload'] = $workload;
											// if($datas[$key][$_start]['vacation'] == 1){
												// $datas[$key][$_start]['workload'] = 0;
											// }
											if(in_array($datas[$key][$_start]['vacation'],array(3,5,7,9,11,13))){
												$datas[$key][$_start]['workload'] = round(($workload / 2), 2);
											}
											$datas[$key][$_start]['nct'] = $setupData['nct'];
										} else {
											if(!empty($datas[$key][$_start])){
												unset($datas[$key][$_start]);
											}
										}
									}
									
								}
                                $isBegin=false;
                                $count++;
                           // }
                        }

                    }
					
                    $_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));

                }
				
            }
		
        }
        $rDatas = $results  = $valRemains = array();
        if(!empty($datas)){
            foreach($datas as $key => $data){
                foreach($data as $_time => $value){
                    if($star <= $_time && $_time <= $end && $value > 0){
                        if(!isset($rDatas[$value['employee_id']][$value['date']])){
                            $rDatas[$value['employee_id']][$value['date']] = array();
                        }
                        $rDatas[$value['employee_id']][$value['date']][] = $value;
                    } else {
                        //do nothing
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
        //GET DATA FOR EMPLOYEE NOT EXISTS DATA

        foreach($employee as $_key=>$_data)
        {
            if(!isset($results[$_key]))
            {
                $_start = $star;
                $_end = $end;
                $key=0;
                $i=0;
                while($_start <= $_end){
                    $cSHoliday = strtolower(date("l", $_start));
                    if($workdays[$cSHoliday] == 0){
                        //do nothing
                    } else {
                        //$time = $_start;
                        $_time = $_start;
                        if(!empty($holidays[$_start])){
                            $results[$_key][$_time][0]=array();
                            $results[$_key][$_time][0]['vacation']=-1;
                            $results[$_key][$_time][0]['date'] = $_start;
                            $results[$_key][$_time][0]['employee_id'] = $_key;
                        } else {
                            $_workloadTemp=0;
                            $_remainTemp=0;
                            $results[$_key][$_time][0]['vacation']=-1;
                            if(!empty($requestQuery[$_key])){
                                foreach($requestQuery[$_key] as $abTime => $checkDate){
                                    if(($abTime == $_start && $checkDate['am'] == 'validated')&&($abTime == $_start && $checkDate['pm'] == 'validated')){
                                        $results[$_key][$_time][0]['workload'] = 0;
                                        $results[$_key][$_time][0]['remain'] = 0;
                                        $results[$_key][$_time][0]['vacation']=1;
                                    }
                                    else if(($abTime == $_start && $checkDate['am'] == 'waiting')&&($abTime == $_start && $checkDate['pm'] == 'waiting')){
                                        $results[$_key][$_time][0]['vacation']=2;
                                    }
                                    else if($abTime == $_start && $checkDate['am'] == 'validated'){
                                        $results[$_key][$_time][0]['vacation']=3;
                                        if($abTime == $_start && $checkDate['pm'] == 'waiting')
                                        {
                                            $results[$_key][$_time]['vacation']=5;
                                        }
                                    }
                                    else if($abTime == $_start && $checkDate['pm'] == 'validated'){
                                        $results[$_key][$_time][0]['vacation']=7;
                                        if($abTime == $_start && $checkDate['am'] == 'waiting')
                                        {
                                            $results[$_key][$_time]['vacation']=9;
                                        }
                                    }
                                    else if($abTime == $_start && $checkDate['am'] == 'waiting'){
                                        $results[$_key][$_time][0]['vacation']=4;
                                        if($abTime == $_start && $checkDate['pm'] == 'validated')
                                        {
                                            $results[$_key][$_time][0]['vacation']=11;
                                        }
                                    }
                                    else if($abTime == $_start && $checkDate['pm'] == 'waiting'){
                                        $results[$_key][$_time][0]['vacation']=6;
                                        if($abTime == $_start && $checkDate['am'] == 'validated')
                                        {
                                            $results[$_key][$_time][0]['vacation']=13;
                                        }
                                    }
                                }
                            }
                            $results[$_key][$_time][0]['date'] = $_start;
                            $results[$_key][$_time][0]['employee_id'] = $_key;
                        }
                        $i++;
                    }


                    $_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));

                }
            }
        }
        //AND
        $endDatas = array();
        $endDatas['results'] = !empty($results) ? $results : array();
        $endDatas['workdays'] = !empty($workdays) ? $workdays : array();
        $endDatas['activities'] = !empty($activities) ? $activities : array();
        $endDatas['projects'] = !empty($projects) ? $projects : array();
        $endDatas['projectPhases'] = !empty($projectPhases) ? $projectPhases : array();
        $endDatas['projectParts'] = !empty($projectParts) ? $projectParts : array();
        $endDatas['allActivities'] = !empty($_activities) ? $_activities : array();
        $endDatas['families'] = !empty($families) ? $families : array();

        return $endDatas;
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
     *
     * @var     :
     * @return  : int date working date
     * @author : HUUPC
     * */
    private function _getWorkingDays($startDate, $endDate){
        $_durationDate = 0;
        $_startDate=strtotime($startDate);
        $_endDate=strtotime($endDate);
        if(strtotime($startDate) != '' && strtotime($endDate) != ''){
            if ($startDate < $endDate) {
                $_holiday = $this->_getHoliday($startDate, $endDate);
                $_holiday = count($_holiday);

                $startDate = strtotime($startDate);
                $endDate = strtotime($endDate);

                $_date = 0;
                while ($startDate <= $endDate){
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                    $_date++;
                }
                if($_holiday != 0){
                    $_date = $_date - $_holiday;
                }
            } else {
                $_date = 0;
            }
            $_durationDate = $_date;
        }
        return $_durationDate;
    }

    /**
     *  Function copy value of forecast.
     *
     *
     *
     */
     public function copy_forecasts($typeSelect = null, $start = null, $end = null){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityRequestCopy');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        //list($isManage, $employeeName) = $this->_getProfitEmployee(1);
        $employeeName = $this->employee_info['Employee'];
        if(!empty($this->data['id'])){
            $employeId = !empty($this->data['Copy']) && !empty($this->data['Copy']['id']) ? $this->data['Copy']['id'] : $employeeName['id'];
            foreach($this->data['id'] as $taskId => $val){
                $data = array(
                    'date' => $start,
                    'employee_id' => $employeId,
                    'task_id' => $taskId
                );
                $last = $this->ActivityRequest->find('first', array('recursive' => -1, 'conditions' => $data));
                $saved = array(
                    'date' => $start,
                    'value' => 0,
                    'employee_id' => $employeId,
                    'company_id' => $this->employee_info['Company']['id'],
                    'task_id' => $taskId,
                    'status' => -1,
                    'activity_id' => 0
                );
                $this->ActivityRequest->create();
                if(!empty($last) && $last['ActivityRequest']['id']) {
                    $this->ActivityRequest->id = $last['ActivityRequest']['id'];
                }
                if($this->ActivityRequest->save($saved)){
                    //neu la mobile thi tao them request cho cac ngay con lai
                    if(isset($this->data['mobile'])){
                        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
                        $endOfWeek = 'monday';
                        foreach($workdays as $name => $amount){
                            if( floatval($amount) > 0 ){
                                $endOfWeek = $name;
                            }
                        }
                        $dto = new DateTime();
                        $dto->setTimestamp($start);
                        $dto->setTime(0, 0, 0);
                        $dto->modify($endOfWeek . ' this week');
                        $rEnd = $dto->getTimestamp();
                        for($date = $start + 86400; $date <= $rEnd; $date += 86400){
                            $request = array(
                                'date' => $date,
                                'value' => 0,
                                'employee_id' => $employeId,
                                'company_id' => $employeeName['company_id'],
                                'task_id' => $taskId,
                                'status' => -1,
                                'activity_id' => 0
                            );
                            $conditions = array(
                                'date' => $date,
                                'employee_id' => $employeId,
                                'company_id' => $employeeName['company_id'],
                                'task_id' => $taskId,
                            );
                            $exists = $this->ActivityRequest->find('first', array('recursive' => -1, 'conditions' => $conditions));
                            if( !empty($exists) )continue;
                            $this->ActivityRequest->create();
                            $this->ActivityRequest->save($request);
                        }
                        //save history (cho mobile)
                        $this->saveHistory($taskId, 'task');
                    }
                    /**
                     * Tuan nao da copy thi khong cho copy nua. Chi copy 1 lan
                     */
                    $checkWeekCopies = $this->ActivityRequestCopy->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'employee_id' => $employeId,
                            'start' => $start,
                            'end' => $end,
                            'company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('id')
                    ));
                    $this->ActivityRequestCopy->create();
                    if(!empty($checkWeekCopies) && $checkWeekCopies['ActivityRequestCopy']['id']){
                        $this->ActivityRequestCopy->id = $checkWeekCopies['ActivityRequestCopy']['id'];
                    }
                    $this->ActivityRequestCopy->save(array(
                        'employee_id' => $employeId,
                        'start' => $start,
                        'end' => $end,
                        'company_id' => $employeeName['company_id']
                    ));
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('Not Saved. Please, try again.', true), 'error');
                }
            }
        } else {
            $this->Session->setFlash(__('Not Saved. Please, try again.', true), 'error');
        }
        if(!empty($this->data['Copy'])){
            if($typeSelect == 'month'){
                $this->redirect(array('action' => 'request', $typeSelect, '?' => array('year' => $this->data['Copy']['year'], 'profit' => $this->data['Copy']['profit'], 'id' => $this->data['Copy']['id'], 'month' => $this->data['Copy']['month'], 'get_path' => 1)));
            }
            $this->redirect(array('action' => 'request', $typeSelect, '?' => array('week' => $this->data['Copy']['week'], 'year' => $this->data['Copy']['year'], 'profit' => $this->data['Copy']['profit'], 'id' => $this->data['Copy']['id'], 'get_path' => 1)));
        } else {
            $this->redirect(array('action' => 'request', $typeSelect, '?' => array('get_path' => 1)));
        }
     }

     /**
      * Function support display request
      */
     private function _parseRequest($employeeName, $lisActivityRequests, $lisTaskRequests, $employees, $_start = null, $_end = null){
        /**
         * Lay cac ngay nghi cua employee trong tuan
         */
        $requestQuery = $this->AbsenceRequest->find("all", array(
            'recursive'     => -1,
            'fields' => array('id', 'date', 'absence_pm', 'absence_am', 'response_am', 'response_pm', 'employee_id'),
            "conditions"    => array('date BETWEEN ? AND ?' => array($_start, $_end),'employee_id' => array_keys($employees))));
        $requests = Set::combine($requestQuery, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
        //$requests = array();
        /**
         * Lay cac loai vang nghi cua cong ty
         */
        $absences   = $this->AbsenceRequest->getAbsences($employeeName['company_id']);
        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        /**
         * Lay cac activity co request
         */
        $_activities = $this->Activity->find('all', array(
            'order' => array('name ASC'),
            'recursive'     => -1,
            'conditions'    => array(
                'company_id' => $employeeName['company_id'],
                'Activity.id' => $lisActivityRequests
            ),
            'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id', 'activated', 'pms','allow_profit','project')));
        /**
         * Nhom family, subfamily, activities lai voi nhau
         */
        $groupFamilies = $activities = array();
        if(!empty($_activities)){
            // o day co the dung Set::combine cua cakephp nhung dung ham nay se rat nang. nen su dung vong lap o day se nhe hon
            foreach($_activities as $activity){
                $dx = $activity['Activity'];
                $groupFamilies[] = $dx['family_id'];
                $groupFamilies[] = $dx['subfamily_id'];
                if(!isset($activities[$dx['id']])){
                    $activities[$dx['id']] = array();
                }
                $activities[$dx['id']] = $dx;
            }
        }
        $groupFamilies = !empty($groupFamilies) ? array_unique($groupFamilies) : array();
        /**
         * Lay tat ca cac task theo dieu kien tren
         */
        $_activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id'),
            'conditions' => array('ActivityTask.id' => $lisTaskRequests
        )));
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
        $mapeds['family'] = $mapeds['subfamily'] = $mapeds['task'] = array();
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
         * Lay cac family va sub_family cua mot cong ty
         */
        $families = $this->Family->find('all', array(
            'order' => array('name ASC'),
            'recursive' => -1,
            'fields'    => array('id', 'name', 'parent_id'),
            'conditions' => array('company_id' => $employeeName['company_id'], 'Family.id' => $groupFamilies)));

        /**
         * Kiem tra nhung activity nao thuoc family, sub family moi cho request
         */
        foreach ($families as $family) {
            $family = array_shift($family);
            $model = empty($family['parent_id']) ? 'family' : 'subfamily';
            $mapeds[$model][$family['id']]['name'] = $family['name'];

        }
        $this->set(compact('requests', 'absences', 'constraint', 'employees', 'mapeds', 'lisActivityRequests', 'lisTaskRequests', 'employeeName'));
     }

     /**
     * Index
     *
     * @return void
     * @access protected
     */
    public function contextMenu($idCheck = null) {
        $this->layout = false;
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
        $this->loadModel('ProjectEmployeeManager');
        $listActivities = $listTasks = array();
        if(!empty($_POST)){
            $listActivities = !empty($_POST['listActi']) ? $_POST['listActi'] : array();
            $listTasks = !empty($_POST['listTask']) ? $_POST['listTask'] : array();
            $employeeName = !empty($_POST['employeeName']) ? $_POST['employeeName'] : array();
        }
        //$employeeName = $this->_getEmpoyee();
        $company = $employeeName['company_id'];
        $employeeId = $employeeName['id'];
        if(!empty($idCheck)){
            $employeeId = $idCheck;
        }
        $cacheName = $company . '_' . $employeeId . '_context_menu';
        $checkContextMenu = Cache::read($cacheName);
        if(!empty($checkContextMenu)){
            $mapeds = $checkContextMenu;
        } else {
            list($isManage, $employeeName_bak) = $this->_getProfitEmployee();
            list($_start, $_end) = $this->_parseParams();
            /**
             * Lay profit center dang duoc chon
             */
            if ($isManage) {
               $profit = $this->params['url']['profit'];
            } else {
               $this->loadModel('ProjectEmployeeProfitFunctionRefer');
               $profitQuery = $this->ProjectEmployeeProfitFunctionRefer->find(
                    'first', array(
                        'fields' => array('profit_center_id'),
                        'recursive' => -1,
                        'conditions' => array('employee_id' => $employeeName['id'])));

                $profit = Set::classicExtract($profitQuery, 'ProjectEmployeeProfitFunctionRefer.profit_center_id');
            }
            //$listActivities = $listTasks = array();
//            if(!empty($_POST)){
//                $listActivities = !empty($_POST['listActi']) ? $_POST['listActi'] : array();
//                $listTasks = !empty($_POST['listTask']) ? $_POST['listTask'] : array();
//            }
            // Comment phan phase status de update len pro site ng�y 7/1/2014...chi pro site moi comment
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
             * Lay cac status cua 1 company
             */
            $_status = $this->ProjectStatus->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $employeeName['company_id']
                ),
                'fields' => array('id', 'status')
            ));
            //
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
            $groupFamilies = $groupSubFamilies = $activities = $projectLinkeds = array();
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
                    if(!empty($dx['project'])){
                        $projectLinkeds[$dx['id']] = $dx['project'];
                    }
                }
            }
            $managerOfProjects = array();
            if(!empty($projectLinkeds)){
                $projectManagers = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $projectLinkeds),
                    'fields' => array('id', 'project_manager_id')
                ));
                $managerBackups = $this->ProjectEmployeeManager->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectEmployeeManager.project_id' => $projectLinkeds, 'is_profit_center' => 0),
                    'fields' => array('project_id', 'project_manager_id')
                ));
                $backups = array();
                if(!empty($managerBackups)){
                    foreach($managerBackups as $managerBackup){
                        $dx = $managerBackup['ProjectEmployeeManager'];
                        if(!isset($backups[$dx['project_id']])){
                            $backups[$dx['project_id']] = array();
                        }
                        $backups[$dx['project_id']][] = $dx['project_manager_id'];
                    }
                }
                foreach($projectLinkeds as $activityId => $projectId){
                    $_manager = !empty($projectManagers[$projectId]) ? $projectManagers[$projectId] : -1;
                    $managerOfProjects[$activityId][] = $_manager;
                    if(!empty($backups[$projectId])){
                        foreach($backups[$projectId] as $value){
                            $managerOfProjects[$activityId][] = $value;
                        }
                    }

                }
            }
            /**
             * Dieu kien de loc ra cac task thuoc activity
             */
            $conditions = !empty($ProjectTaskNoDisplay) ? array('NOT' => array('ActivityTask.project_task_id' => $ProjectTaskNoDisplay)) : array();
            $_conditions = array('ActivityTask.activity_id' => array_keys($activities));
            //$conditions = array_merge($conditions, $_conditions);
            /**
             * Lay tat ca cac task theo dieu kien tren
             */
            $_activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id', 'estimated', 'task_status_id', 'special'),
                'conditions' => array(
                    //'NOT' => array('ActivityTask.project_task_id' => $ProjectTaskNoDisplay),
                    'ActivityTask.special' => 0,
                    'OR' => array(
                        'ActivityTask.activity_id' => array_keys($activities),
                        'ActivityTask.id' => $listTasks
                    )
                )
            ));
            /**
             * Lay request cua cac task tren
             */
            $idTask = !empty($_activityTasks) ? Set::classicExtract($_activityTasks, '{n}.ActivityTask.id') : array();
            $requests = $this->ActivityRequest->find(
                'all',
                array(
                    'recursive'     => -1,
                    'fields'        => array(
                        'id',
                        'employee_id',
                        'task_id',
                        'SUM(value) as value'
                    ),
                    'group'         => array('task_id'),
                    'conditions'    => array(
                        'status'        => array(0, 2),
                        'task_id'       => $idTask,
                        'company_id'    => $employeeName['company_id'],
                        'NOT'           => array('value' => 0, "task_id" => null),
                    )
                )
            );
            $requests = !empty($requests) ? Set::combine($requests, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
            /**
             * Dinh dang lai mang cua activity task
             */
            $activityTasks = $projectTaskListIds = array();
            if(!empty($_activityTasks)){
                $parents = !empty($_activityTasks) ? array_unique(Set::classicExtract($_activityTasks, '{n}.ActivityTask.parent_id')) : array();
                // o day co the dung Set::combine cua cakephp nhung dung ham nay se rat nang. nen su dung vong lap o day se nhe hon
                foreach($_activityTasks as $key => $_activityTask){
                    $dx = $_activityTask['ActivityTask'];
                    if($dx['project_task_id'] != null){
                        $projectTaskListIds[$dx['id']] = $dx['project_task_id'];
                    }
                    if(in_array($dx['id'], $parents)){
                        $_activityTasks[$key]['ActivityTask']['is_parent'] = 'true';
                    } else {
                        $_activityTasks[$key]['ActivityTask']['is_parent'] = 'false';
                    }
                    $_activityTasks[$key]['ActivityTask']['status'] = !empty($_status[$dx['task_status_id']]) ? strtolower($_status[$dx['task_status_id']]) : 'ip';
                }
            }
            /**
             * Lay estimated tu project task
             */
            $parents = $estimated = $parentIds = $pTaskStatus = array();
            if(!empty($projectTaskListIds)){
                $projectTasks = $this->ProjectTask->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $projectTaskListIds),
                    'fields' => array('id', 'estimated', 'parent_id', 'task_status_id')
                ));
                $parents = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id')) : array();
                $estimated = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.estimated') : array();
                foreach($projectTasks as $id => $projectTask){
                    $dx = $projectTask['ProjectTask'];
                    if(in_array($dx['id'], $parents)){
                        $projectTasks[$id]['ProjectTask']['is_parent'] = 'true';
                    } else {
                        $projectTasks[$id]['ProjectTask']['is_parent'] = 'false';
                    }
                }
                $parentIds = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.is_parent') : array();
                $pTaskStatus = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.task_status_id') : array();
            }
            foreach($_activityTasks as $_activityTask){
                $dx = $_activityTask['ActivityTask'];
                if($dx['project_task_id'] != null){
                    $projectId = !empty($projectTaskListIds[$dx['id']]) ? $projectTaskListIds[$dx['id']] : 0;
                    $dx['estimated'] = !empty($estimated[$projectId]) ? $estimated[$projectId] : 0;
                    $dx['is_parent'] = !empty($parentIds[$projectId]) ? $parentIds[$projectId] : 0;
                    $idStatus = !empty($pTaskStatus[$projectId]) ? $pTaskStatus[$projectId] : 0;
                    $dx['status'] = !empty($_status[$idStatus]) ? strtolower($_status[$idStatus]) : 'ip';
                }
                $dx['consumed'] = !empty($requests[$dx['id']]) ? $requests[$dx['id']] : 0;
                if(in_array($dx['id'], $listTasks)){
                    if(!isset($activityTasks[$dx['activity_id']][$dx['id']])){
                        $activityTasks[$dx['activity_id']][$dx['id']] = array();
                    }
                    $activityTasks[$dx['activity_id']][$dx['id']] = $dx;
                } else {
                    if(!empty($ProjectTaskNoDisplay)){
                        if(in_array($dx['project_task_id'], $ProjectTaskNoDisplay)){
                            // do nothing
                        } else {
                            if(in_array($dx['task_status_id'], $project_status)){
                                // do nothing
                            } else {
                                if(!isset($activityTasks[$dx['activity_id']][$dx['id']])){
                                    $activityTasks[$dx['activity_id']][$dx['id']] = array();
                                }
                                $activityTasks[$dx['activity_id']][$dx['id']] = $dx;
                            }

                        }
                    } else {
                        if(in_array($dx['task_status_id'], $project_status)){
                            // do nothing
                        } else {
                            if(!isset($activityTasks[$dx['activity_id']][$dx['id']])){
                                $activityTasks[$dx['activity_id']][$dx['id']] = array();
                            }
                            $activityTasks[$dx['activity_id']][$dx['id']] = $dx;
                        }
                    }
                }
            }
            /**
             * Tao ra 1 mang moi, 1 mang du lieu de dua ra view.
             */
            $mapeds = array('activity' => array());
            if(!empty($activities)){
                $countTasks = $this->ActivityTask->find('all', array(
                    'recursive' => -1,
                    'fields' => array('count(id) as value', 'activity_id'),
                    'conditions' => array(
                        'ActivityTask.activity_id' => array_keys($activities),
                        'ActivityTask.previous' => null
                    ),
                    'group' => array('activity_id')
                ));
                $countTasks = !empty($countTasks) ? Set::combine($countTasks, '{n}.ActivityTask.activity_id', '{n}.0.value') : array();
                foreach($activities as $id => $activity){
                    if(!isset($mapeds['activity'][$id])){
                        $mapeds['activity'][$id] = array();
                    }
                    $haveTask = !empty($countTasks[$id]) ? $countTasks[$id] : 0;
                    $mapeds['activity'][$id] = array(
                        'name'          => $activity['name'],
                        'long_name'     => $activity['name'],
                        'short_name'    => $activity['short_name'],
                        'long_name'     => $activity['long_name'],
                        'family_id'     => $activity['family_id'],
                        'subfamily_id'  => $activity['subfamily_id'],
                        'activated'     => $activity['activated'],
                        'pms'           => $activity['pms'],
                        'is_project'    => $activity['project'],
                        'have_task'     => ($haveTask == 0) ? 'false' : 'true'
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
                            if($employeeName['role_name'] === 'admin'){
                                $mapeds['subfamily'][$family['id']]['act'][] = $value;
                            } else {
                                $checkManager = !empty($managerOfProjects[$value]) ? $managerOfProjects[$value] : '';
                                if(!empty($checkManager)){
                                    if(in_array($employeeName['id'], $checkManager)){
                                        $mapeds['subfamily'][$family['id']]['act'][] = $value;
                                    } else {
                                        if(in_array($value, $allowActivity) || empty($accessibleActivities[$value])){
                                            $mapeds['subfamily'][$family['id']]['act'][] = $value;
                                        }
                                    }
                                } else {
                                    if(in_array($value, $allowActivity) || empty($accessibleActivities[$value])){
                                        $mapeds['subfamily'][$family['id']]['act'][] = $value;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if(!empty($groupFamilies[$family['id']])){
                        foreach($groupFamilies[$family['id']] as $value){
                            if($employeeName['role_name'] === 'admin'){
                                $mapeds['family'][$family['id']]['act'][] = $value;
                            } else {
                                $checkManager = !empty($managerOfProjects[$value]) ? $managerOfProjects[$value] : '';
                                if(!empty($checkManager)){
                                    if(in_array($employeeName['id'], $checkManager)){
                                        $mapeds['family'][$family['id']]['act'][] = $value;
                                    } else {
                                        if(in_array($value, $allowActivity) || empty($accessibleActivities[$value])){
                                            $mapeds['family'][$family['id']]['act'][] = $value;
                                        }
                                    }
                                } else {
                                    if(in_array($value, $allowActivity) || empty($accessibleActivities[$value])){
                                        $mapeds['family'][$family['id']]['act'][] = $value;
                                    }
                                }
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
            $mapeds['alltask'] = array();
            if(!empty($activityTasks)){
                $mapeds['alltask'] = $activityTasks;
            }
            Cache::write($cacheName, $mapeds);
        }
        echo json_encode($mapeds);
        exit;
    }
    /**
     * Index
     *
     * @return void
     * @access protected
     */
    public function contextMenuCache($idCheck = null) {
        $this->layout = false;
        $employeeName = $this->_getEmpoyee();
        $company = $employeeName['company_id'];
        $employeeId = $employeeName['id'];
        if(!empty($idCheck)){
            $employeeId = $idCheck;
        }
        $cacheName = $company . '_' . $employeeId . '_context_menu_cache';
        if($_POST){
            $content = !empty($_POST['content']) ? $_POST['content'] : '';
            if(!empty($content)){
                Cache::write($cacheName, $content);
            }
        }
        echo 'Finish!';
        exit;
    }

    /**
     * Index
     *
     * @return void
     * @access protected
     */
    public function refreshDataMenu($activityId = null, $taskId = null) {
        $this->layout = false;
        $this->loadModel('Activity');
        $this->loadModel('Family');
        $this->loadModel('ActivityTask');
        $employeeName = $this->_getEmpoyee();

        $mapeds = array();
        if(!empty($_POST)){
            $mapeds = $_POST['mapeds'];
        }
        $activities = $this->Activity->find('first', array(
            'recursive'     => -1,
            'conditions'    => array(
                'Activity.id' => $activityId
            ),
            'fields' => array('id', 'name', 'long_name', 'short_name', 'family_id', 'subfamily_id', 'activated', 'pms','allow_profit','project')));
        $listIdActivities = array();
        if(!empty($mapeds['activity'])){
            foreach($mapeds['activity'] as $id => $val){
                $listIdActivities[] = $id;
                if(!isset($val['tasks'])){
                    $mapeds['activity'][$id]['tasks'] = array();
                }
            }
        }
        if(in_array($activities['Activity']['id'], $listIdActivities)){
            //do nothing
        } else {
            $newActivity = $activities['Activity']['id'];
            $activities['Activity']['tasks'] = array();
            $mapeds['activity'][$newActivity] = $activities['Activity'];
        }
        $family = !empty($activities['Activity']['family_id']) ? $activities['Activity']['family_id'] : 0;
        $subFamily = !empty($activities['Activity']['subfamily_id']) ? $activities['Activity']['subfamily_id'] : 0;
        $families = $this->Family->find('all', array(
            'recursive' => -1,
            'fields'    => array('id', 'name'),
            'conditions' => array(
                'Family.id' => array($family, $subFamily),
                'company_id' => $employeeName['company_id']
            )
        ));
        $families = !empty($families) ? Set::combine($families, '{n}.Family.id', '{n}.Family') : array();
        if(!empty($family)){
            $mapeds['family'][$family] = array('name' => $families[$family]['name']);
        }
        if(!empty($subFamily)){
            $mapeds['subfamily'][$subFamily] = array('name' => $families[$subFamily]['name']);
        }
        if($taskId != 0){
            $activityTasks = $this->ActivityTask->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'name', 'previous','parent_id','project_task_id','activity_id'),
                'conditions' => array(
                    'ActivityTask.id' => $taskId
                )
            ));
            $listIdTasks = array();
            if(!empty($mapeds['task'])){
                foreach($mapeds['task'] as $id => $val){
                    $listIdTasks[] = $id;
                }
            }
            if(in_array($activities['Activity']['id'], $listIdActivities)){
                //do nothing
            } else {
                $newTask = $activityTasks['ActivityTask']['id'];
                $mapeds['task'][$newTask] = $activityTasks['ActivityTask'];
                $newIdAc = $activityTasks['ActivityTask']['activity_id'];
                $mapeds['activity'][$newIdAc]['tasks'][$newTask] = $activityTasks['ActivityTask'];
            }
        }
        //echo json_encode($mapeds);
        exit;
    }

    /**
     * Index
     *
     * @return void
     * @access protected
     */
    public function cleanupCacheMenu($idCheck = null) {
        $this->layout = false;
        $employeeName = $this->_getEmpoyee();
        $company = $employeeName['company_id'];
        $employeeId = $employeeName['id'];
        if(!empty($idCheck)){
            $employeeId = $idCheck;
        }
        $cacheName = $company . '_' . $employeeId . '_context_menu';
        $cacheNameMenu = $company . '_' . $employeeId . '_context_menu_cache';
        Cache::delete($cacheName);
        Cache::delete($cacheNameMenu);
        echo 'OK';
        exit;
    }
    public function beforeCleanupCacheMenu($idCheck = null) {
        // $this->layout = false;
        $employeeName = $this->_getEmpoyee();
        $company = $employeeName['company_id'];
        $employeeId = $employeeName['id'];
        if(!empty($idCheck)){
            $employeeId = $idCheck;
        }
        $cacheName = $company . '_' . $employeeId . '_context_menu';
        $cacheNameMenu = $company . '_' . $employeeId . '_context_menu_cache';
        Cache::delete($cacheName);
        Cache::delete($cacheNameMenu);
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
                'fields' => array('id', 'employee_id')
            ));
            $result = !empty($result) ? array_unique($result) : array();
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Xoa cac file da export
     */
    public function deleteFileModuleExportActivity(){
        $_SESSION['ModuleExportActivity'] = array();
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $employeeLoginId;
        foreach (array(SHARED . $employeeLoginName . DS) as $path) {
            $normalFiles = glob($path . '*');
            $hiddenFiles = glob($path . '\.?*');
            $normalFiles = $normalFiles ? $normalFiles : array();
            $hiddenFiles = $hiddenFiles ? $hiddenFiles : array();
            $files = array_merge($normalFiles, $hiddenFiles);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (preg_match('/(\.|\.\.)$/', $file)) {
                        continue;
                    }
                    if (is_file($file) === true) {
                        @unlink($file);
                    }
                }
            }
        }
        $this->loadModel('TmpModuleActivityExport');
        $employeeLoginExportName = Inflector::slug($this->employee_info['Employee']['fullname']);
        $this->TmpModuleActivityExport->deleteAll(array('employee_export' => $employeeLoginExportName, 'date_export' => strtotime(date('d-m-Y', time()))), false);
        echo json_encode('true');
        exit;
    }

    /**
     * Count record exporting
     */
    public function countModuleExportActivity(){
        $this->layout = false;
        $this->loadModel('TmpModuleActivityExport');
        $employeeLoginExportName = Inflector::slug($this->employee_info['Employee']['fullname']);
        $tmpModuleActivityExports = $this->TmpModuleActivityExport->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_export' => $employeeLoginExportName,
                'date_export' => strtotime(date('d-m-Y', time()))
            )
        ));
        echo !empty($tmpModuleActivityExports) ? json_encode($tmpModuleActivityExports) : json_encode(0);
        exit;
    }
    private function _sizeofvar($var) {
        $start_memory = memory_get_usage();
        $tmp = unserialize(serialize($var));
        return memory_get_usage() - $start_memory;
    }
    /**
     * zIP FILE
     */
    public function zipFileModuleExportActivity($merge = 'no'){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $this->loadModel('ActivityExport');
        $this->loadModel('TmpModuleActivityExport');
        $company_id = $this->employee_info['Company']['id'];
        $_activityExports = $this->ActivityExport->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'display' => 1),
            'fields' => array('name', 'english', 'setting'),
            'order' => array('weight' => 'ASC')
        ));
        $activityExports = $settingOfExports = array();
        $fieldset = array('id' => 'id');
        if(!empty($_activityExports)){
            foreach($_activityExports as $_activityExport){
                $dx = $_activityExport['ActivityExport'];
                $name = !empty($dx['name']) ? $dx['name'] : '';
                $val = !empty($dx['english']) ? $dx['english'] : '';
                $activityExports[$name] = $val;
                $fieldset[strtolower(str_replace(array(' ', '/'), '_', trim($name)))] = strtolower(str_replace(array(' ', '/'), '_', trim($name)));
                $settingOfExports[strtolower(str_replace(array(' ', '/'), '_', trim($name)))] = $dx['setting'];
            }
        }
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $employeeLoginId;
        $employeeLoginExportName = Inflector::slug($this->employee_info['Employee']['fullname']);
        $part = SHARED . $employeeLoginName . DS . 'activity_export_' . date('H_i_s_d_m_Y') . '.csv';
        $savePart = $part;
        //$part = str_replace('\\', '/', $part);
        if(!empty($fieldset)){
            copy(TEMPLATES . 'module_ex_activity.csv', $savePart);
            $file = fopen($savePart, 'w');
            $tmpModuleActivityExports = $this->TmpModuleActivityExport->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_export' => $employeeLoginExportName,
                    'date_export' => strtotime(date('d-m-Y', time())),
                    'company_id' => $company_id
                ),
                'fields' => $fieldset,
                //'ORDER' => array('first_name'),
                'limit' => 10000
            ));
            $idDels = !empty($tmpModuleActivityExports) ? Set::classicExtract($tmpModuleActivityExports, '{n}.TmpModuleActivityExport.id') : array();
            $tmpModuleActivityExports = !empty($tmpModuleActivityExports) ? Set::combine($tmpModuleActivityExports, '{n}.TmpModuleActivityExport.id', '{n}.TmpModuleActivityExport') : array();
            if($merge == 'yes'){
                $fieldOne = $fieldset;
                $fieldTwo = $fieldset;
                /**
                 * Lay tat cac cac task co phase
                 */
                //$fieldOne['sum'] = 'SUM(quantity) AS Total';
                $fieldOne['employee_id'] = 'employee_id';
                $fieldOne['phase_plan_id'] = 'phase_plan_id';
                if(!in_array('date_activity_absence', $fieldOne)){
                    $fieldOne['date_activity_absence'] = 'date_activity_absence';
                }
                $fieldOne['employee_id'] = 'employee_id';
                $this->TmpModuleActivityExport->virtualFields['Total'] = 'SUM(quantity)';
                $fieldOne['Total'] = 'Total';
                $getDataOnes = $getDataTwos = array();
                $getDataOnes = $this->TmpModuleActivityExport->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'employee_export' => $employeeLoginExportName,
                        'date_export' => strtotime(date('d-m-Y', time())),
                        'company_id' => $company_id,
                        'NOT' => array(
                            'phase_plan_id IS NULL'
                        )
                    ),
                    'fields' => $fieldOne,
                    'group' => array('employee_id', 'date_activity_absence', 'ref_2'),
                    'limit' => 5000
                ));
                /**
                 * Lay tat cac cac task ko co phase
                 */
                $this->TmpModuleActivityExport->virtualFields = array();
                $this->TmpModuleActivityExport->virtualFields['Total'] = 'quantity';
                $fieldTwo['Total'] = 'Total';
                $getDataTwos = $this->TmpModuleActivityExport->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'employee_export' => $employeeLoginExportName,
                        'date_export' => strtotime(date('d-m-Y', time())),
                        'company_id' => $company_id,
                        'phase_plan_id IS NULL'
                    ),
                    'fields' => $fieldTwo,
                    'limit' => 5000
                ));
                $tmpModuleActivityExports = array();
                $tmpModuleActivityExports = array_merge($getDataOnes, $getDataTwos);
                $tmpModuleActivityExports = !empty($tmpModuleActivityExports) ? Set::combine($tmpModuleActivityExports, '{n}.TmpModuleActivityExport.id', '{n}.TmpModuleActivityExport') : array();
            }
            $chartDateABAC = $chartValid = $chartExtrac = '-';
            if(isset($settingOfExports['date_activity_absence']))
            switch($settingOfExports['date_activity_absence']){
                case 'date_two': {$chartDateABAC = '.';break;}
                case 'date_three': {$chartDateABAC = '/';break;}
                case 'date_one':
                default: {$chartDateABAC = '-';break;}
            }
            if(isset($settingOfExports['validation_date']))
            switch($settingOfExports['validation_date']){
                case 'date_two': {$chartValid = '.';break;}
                case 'date_three': {$chartValid = '/';break;}
                case 'date_one':
                default: {$chartValid = '-';break;}
            }
            if(isset($settingOfExports['extraction_date']))
            switch($settingOfExports['extraction_date']){
                case 'date_two': {$chartExtrac = '.';break;}
                case 'date_three': {$chartExtrac = '/';break;}
                case 'date_one':
                default: {$chartExtrac = '-';break;}
            }
            foreach ($tmpModuleActivityExports as $id => $tmpModuleActivityExport) {
                unset($tmpModuleActivityExport['id']);
                if(!empty($tmpModuleActivityExport['date_activity_absence'])){
                    $tmpModuleActivityExport['date_activity_absence'] = date('d' . $chartDateABAC . 'm' . $chartDateABAC . 'Y', $tmpModuleActivityExport['date_activity_absence']);
                }
				if(!empty($tmpModuleActivityExport['message']) && $tmpModuleActivityExport['message'] != '""'){
					$content="";
					$n=1; 
					if(!empty($tmpModuleActivityExport['message'])){
						foreach(json_decode($tmpModuleActivityExport['message']) as $message){
							$content .= $n. ' - '.$message.' ';
							$n++;
						}
					}
					$tmpModuleActivityExport['message'] = $content;
                }else{
					$tmpModuleActivityExport['message'] = '';
				}
				if(!empty($tmpModuleActivityExport['week_message']) && $tmpModuleActivityExport['week_message'] != '""'){
					$content="";
					$n=1; 
					if(!empty($tmpModuleActivityExport['week_message'])){
						foreach(json_decode($tmpModuleActivityExport['week_message']) as $message){
							$content .= $n. ' - '.$message.' ';
							$n++;
						}
					}
					$tmpModuleActivityExport['week_message'] = $content;
                }else{
					$tmpModuleActivityExport['week_message'] = '';
				}
                if(!empty($tmpModuleActivityExport['validation_date'])){
                    $tmpModuleActivityExport['validation_date'] = date('d' . $chartValid . 'm' . $chartValid . 'Y', $tmpModuleActivityExport['validation_date']);
                }
                if(!empty($tmpModuleActivityExport['extraction_date'])){
                    $tmpModuleActivityExport['extraction_date'] = date('d' . $chartExtrac . 'm' . $chartExtrac . 'Y', $tmpModuleActivityExport['extraction_date']);
                }
                if(isset($tmpModuleActivityExport['quantity']) && $merge == 'yes'){
                    $tmpModuleActivityExport['quantity'] = $tmpModuleActivityExport['Total'];
                }
                if(isset($tmpModuleActivityExport['employee_id'])){
                    unset($tmpModuleActivityExport['employee_id']);
                }
                if(isset($tmpModuleActivityExport['phase_plan_id'])){
                    unset($tmpModuleActivityExport['phase_plan_id']);
                }
                if(isset($tmpModuleActivityExport['Total'])){
                    unset($tmpModuleActivityExport['Total']);
                }
                fputcsv($file, $tmpModuleActivityExport);
            }
			
            fclose($file);
            $this->TmpModuleActivityExport->deleteAll(array('TmpModuleActivityExport.id' => $idDels), false);
            // $db = ConnectionManager::getDataSource('default');
            // $setAuto = 'ALTER TABLE tmp_module_activity_exports AUTO_INCREMENT = 1';
            // $db->query($setAuto);
            $this->set(compact('activityExports', 'employeeLoginName', 'savePart'));
        } else {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'activity_forecasts', 'action' => 'request'));
        }

    }
    /**
     * Activity Export Data in Request Follow Employee
     */
    public function exportActivityFollowEmployee(){
        $_SESSION['ModuleExportActivity'] = array();
        set_time_limit(0);
        $this->layout = false;
        $company_id = $this->employee_info['Company']['id'];
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $employeeLoginId;
        $path = SHARED . $employeeLoginName;
        App::import('Core', 'Folder');
        new Folder($path, true, 0777);
        $employeeLoginExportName = Inflector::slug($this->employee_info['Employee']['fullname']);
        $totalRecord = 0;
        if(!empty($_POST) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['employee_id'])){
            $this->loadModel('Employee');
            $this->loadModel('AbsenceRequest');
            $this->loadModel('Absence');
            $this->loadModel('ProfitCenter');
            $this->loadModel('ActivityRequest');
            $this->loadModel('ActivityTask');
            $this->loadModel('ProjectPhasePlan');
            $this->loadModel('ProjectTask');
            $this->loadModel('Activity');
            $this->loadModel('ActivityRequestConfirm');
            $this->loadModel('Family');
            $this->loadModels('ProjectEmployeeProfitFunctionRefer', 'Project', 'ProjectPhase', 'ProjectAmrProgram', 'ActivityForecastComment', 'TmpModuleActivityExport');
            $startDate = strtotime($_POST['start_date']);
            $endDate = strtotime($_POST['end_date']);
            $dayOff = $_POST['day_of'];
            $display = $_POST['display'];
            $listEmployees = !empty($_POST['employee_id']) ? $_POST['employee_id'] : array();
            if(!empty($listEmployees)){
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
                $getHolidays = ClassRegistry::init('Holiday')->getOptions($startDate, $endDate, $company_id);
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

                            $save[] = array(
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
                                'date_activity_absence' => !empty($date) ? $date : '',
                                'validation_date' => '',
                                'extraction_date' => time(),
                                'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
                                'date_export' => strtotime(date('d-m-Y', time())),
                                'company_id' => $company_id,
								'project_name' => 'ABSENCE',
								'message' => json_encode($comment),
								'week_message' => !empty( $wcomment) ? json_encode($wcomment) : '',
                            );
                        }
                    }
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

                            $save[] = array(
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
                                'date_activity_absence' => !empty($date) ? $date : '',
                                'validation_date' => !empty($absencesDates[$employee['Employee']['id']][$date]) ? $absencesDates[$employee['Employee']['id']][$date] : '',
                                'extraction_date' => time(),
                                'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
                                'date_export' => strtotime(date('d-m-Y', time())),
                                'company_id' => $company_id,
								'project_name' => 'ABSENCE',
								'message' => json_encode($comment),
								'week_message' => !empty( $wcomment) ? json_encode($wcomment) : '',
                            );
                        }
                    }
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
                            $no++;
							$PhasePlanId = $project_planed_phase_id;
                            $code1 = !empty($activities[$activityId]['code1']) ? $activities[$activityId]['code1'] : '';
                            $code2 = !empty($activities[$activityId]['code2']) ? $activities[$activityId]['code2'] : '';
                            $ref1 = !empty($phasePlans[$PhasePlanId]['ref1']) ? $phasePlans[$PhasePlanId]['ref1'] : '';
                            $ref2 = !empty($phasePlans[$PhasePlanId]['ref2']) ? $phasePlans[$PhasePlanId]['ref2'] : '';
                            $val = !empty($value['value']) ? $value['value'] : 0;

                                $first = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
                                $last = !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '';
                                if( ((!empty($value['value']) && $value['value']!=0)&& $display == 'no') ||  $display == 'yes' ){
                                    $save[] = array(
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
                                        'date_activity_absence' => !empty($value['date']) ? $value['date'] : '',
                                        'validation_date' => $valDate,
                                        'extraction_date' => time(),
                                        'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
                                        'date_export' => strtotime(date('d-m-Y', time())),
                                        'company_id' => $company_id,
                                        'employee_id' => !empty($employee['Employee']['id']) ? $employee['Employee']['id'] : 0,
                                        'fullname' => $first . ' ' . $last,
                                        // 'phase_plan_id' => $PhasePlanId,
                                        'phase_plan_id' => !empty($project_planed_phase_id) ? $project_planed_phase_id : '',
										'project_code_1' => isset( $project['project_code_1'] ) ? $project['project_code_1'] : '',
										'tjm' => !empty($employee['Employee']['tjm']) ? $employee['Employee']['tjm'] : '',
										'message' => json_encode($comment),
										'week_message' => !empty( $wcomment) ? json_encode($wcomment) : '',
										'project_id' => $projectID,
                                    );
                                }
                            //}
                        }
                    }
                }
                $this->loadModel('TmpModuleActivityExport');
                if(!empty($save)){
                    $this->TmpModuleActivityExport->saveAll($save);
                }
                $totalRecord = $no;
           }
        }
        $results = array(
            'totalRecord' => $totalRecord
        );
        echo json_encode($results);
        exit;
    }

    /**
     * Lay cac ngay lam viec theo config working day o admin
     */
    private function _workingDayFollowConfigAdmin($start = null, $end = null, $workdayAdmins = null){
        $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $day = strtolower(date('l', $start));
                if($workdayAdmins[$day] != 0){
                    $results[$start] = $start;
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return $results;
    }

    function import_timesheet($valid = 0, $startDate = 0, $endDate = 0, $employees = ''){
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $requests = '';
        $listEmployees = array();
        $buildDatas = $buildEmploys = array();
        if($valid == 1 && !empty($startDate) && !empty($endDate) && !empty($employees)){
            $listWeekOfMonths = $this->_splitWeekOfMonth($startDate, $endDate);
            $employees = explode('-', $employees);
            foreach($employees as $employee){
                foreach($listWeekOfMonths as $start => $end){
                    $requests .= $employee . '-' . $start . ',';
                    $buildDatas[] = array(
                        'employee_id' => $employee,
                        'start' => $start,
                        'end' => $end
                    );
                }
                $listEmployees[$employee] = 1;
            }
            $this->loadModel('Employee');
            $buildEmploys = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $employees),
                'fields' => array('id', 'fullname')
            ));
        }
        $this->set(compact('valid', 'listEmployees', 'requests', 'buildDatas', 'buildEmploys'));
    }

    /**
     * If case on exists then notify.
     *
     * @param int $project_id
     * @return void
     * @access public
     */
    function import_csv() {
        set_time_limit(0);
        //$this->autoRender = false;
        $setValid = $setStartDate = $setEndDate = 0;
        $setEmployee = '';
        $employeeName = $this->_getEmpoyee();
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
                        'Resource',
                        'Code1',
                        'Project Name or Activity Name',
                        'Part',
                        'Phase',
                        'Task',
                        'Date',
                        'Consumed'
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
                    $this->loadModel('Activity');
                    $this->loadModel('Project');
                    $this->loadModel('ProjectTask');
                    $this->loadModel('ActivityTask');
                    $this->loadModel('Family');
                    $this->loadModel('ProjectPart');
                    $this->loadModel('AbsenceRequest');
                    $this->loadModel('ActivityRequest');
                    $this->ProjectPhase->cacheQueries = true;
                    $this->Employee->cacheQueries = true;

                    $validate = array('Resource', 'Code1', 'Project Name or Activity Name', 'Task', 'Date');
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    // phase of company
                    $phaseOfCompanies = $this->ProjectPhase->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $employeeName['company_id']
                        ),
                        'fields' => array('id', 'name')
                    ));
                    /**
                     * Kiem tra xem he thong da co Activity OUT va Task OUT chua?
                     * Neu chua co thi tao task out va activity out
                     */
                    $checkActis = $this->Activity->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $employeeName['company_id'],
                            'name' => 'OUT'
                        ),
                        'fields' => array('id')
                    ));
                    $checkActiId = 0;
                    if(!empty($checkActis) && !empty($checkActis['Activity']['id'])){
                        // co activity roi, khong lam gi nua ca
                        $checkActiId = $checkActis['Activity']['id'];
                    } else {
                        $families = $this->Family->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'company_id' => $employeeName['company_id']
                            ),
                            'fields' => array('id', 'name'),
                            'order' => array('id ASC')
                        ));
                        if(!empty($families) && !empty($families['Family']['id'])){
                            $this->Activity->create();
                            $saveActis = array(
                                'name' => 'OUT',
                                'long_name' => 'OUT',
                                'short_name' => 'OUT',
                                'family_id' => $families['Family']['id'],
                                'pms' => 0,
                                'company_id' => $employeeName['company_id'],
                                'activated' => 1
                            );
                            if($this->Activity->save($saveActis)){
                                $lastActiId = $this->Activity->getLastInsertID();
                                $checkActiId = $lastActiId;
                            }
                        }
                    }
                    $taskOutId = 0;
                    if(!empty($checkActiId)){
                        $checkActiTasks = $this->ActivityTask->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'activity_id' => $checkActiId,
                                'name' => 'OUT'
                            ),
                            'fields' => array('id')
                        ));
                        if(!empty($checkActiTasks) && !empty($checkActiTasks['ActivityTask']['id'])){ // co task OUT roi
                            $taskOutId = $checkActiTasks['ActivityTask']['id'];
                        } else { // chua co task OUT thi tao 1 task out
                            $this->ActivityTask->create();
                            if($this->ActivityTask->save(array('name' => 'OUT', 'activity_id' => $checkActiId))){
                                $taskOutId = $this->ActivityTask->getLastInsertID();
                            }
                        }
                    }
                    $cosumedFollowDates = $listTaskHaveImports = $listEmployees = array();
                    foreach ($csv->data as $key => $row) {
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
                        $date = $this->_formatDateCustom($row['Date']);
                        $consumed = $row['Consumed'] = !empty($row['Consumed']) ? (float) str_replace(',', '.', $row['Consumed']) : 0;
                        if(empty($row['error'])) {
                            $resourceId = 0;
                            /**
                             * Check holiday, consumed, absence, ngay nghi
                             */
                            $allowImport = true;
                            if($consumed > 1){
                                $allowImport = false;
                                $row['columnHighLight']['Consumed'] = '';
                                $row['error'][] = __('The Consumed of day > 1', true);
                            }
                            $getDay = strtolower(date("l", $date));
                            $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($date, $date, $employeeName['company_id']);
                            if($getDay == 'saturday' || $getDay == 'sunday' || !empty($holidays)){
                                $allowImport = false;
                                $row['columnHighLight']['Date'] = '';
                                $row['error'][] = __('The DAY-OFF or WEEKEND', true);
                            }
                            // kiem tra resource
                            if(!empty($row['Resource'])){
                                $employeeCompanies = $this->Employee->CompanyEmployeeReference->find('all', array(
                                    'conditions' => array(
                                        'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                                        'CONCAT(Employee.first_name, " ", Employee.last_name)' => $row['Resource']
                                    ),
                                    'fields' => array('Employee.id', 'Employee.code_id', 'CONCAT(Employee.first_name, " ", Employee.last_name) as full_name')
                                ));
                                $saveEmployees = !empty($employeeCompanies) ? Set::combine($employeeCompanies, '{n}.Employee.id', '{n}.0.full_name') : array();
                                $employeeCompanies = !empty($employeeCompanies) ? Set::combine($employeeCompanies, '{n}.Employee.id', '{n}.Employee.code_id') : array();
                                $code_id = !empty($row['Code1']) ? $row['Code1'] : '';
                                if(!empty($employeeCompanies) && in_array($code_id, $employeeCompanies)){
                                    $employeeCompanies = array_flip($employeeCompanies);
                                    $row['data']['employee_id'] = !empty($employeeCompanies[$code_id]) ? $employeeCompanies[$code_id] : '';
                                    $resourceId = $row['data']['employee_id'];
                                    $listEmployees[$resourceId] = array(
                                        'id' => $resourceId,
                                        'fullname' => !empty($saveEmployees[$resourceId]) ? $saveEmployees[$resourceId] : '',
                                        'code_id' => $code_id
                                    );
                                } else {
                                    $row['columnHighLight']['Resource'] = '';
                                    $row['columnHighLight']['Code1'] = '';
                                    $row['error'][] = __('The Resource name not found in company', true);
                                }
                            }
                            /**
                             * Xoa task OUT cua ngay nay neu co
                             */
                            $deleteOUT = $this->ActivityRequest->find('first', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $date, 'employee_id' => $resourceId, 'task_id' => $taskOutId),
                                'fields' => array('id', 'status')
                            ));
                            if(!empty($deleteOUT) && !empty($deleteOUT['ActivityRequest']['id'])){
                                if($deleteOUT['ActivityRequest']['status'] == 2){
                                    $allowImport = false;
                                    $row['columnHighLight']['Date'] = '';
                                    $row['error'][] = __('Date Validated.', true);
                                } else {
                                    $this->ActivityRequest->delete($deleteOUT['ActivityRequest']['id']);
                                }

                            }
                            /**
                             * Kiem tra tong consumed trong request
                             */
                            $requests = $this->ActivityRequest->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $date, 'employee_id' => $resourceId),
                                'fields' => array('SUM(value) AS Total')
                            ));
                            $requests = !empty($requests) && !empty($requests[0]) && !empty($requests[0][0]) && isset($requests[0][0]['Total']) ? $requests[0][0]['Total'] : 0;
                            $requests = (float) round($requests + $consumed, 2);
                            if($requests > 1){
                                $allowImport = false;
                                $row['columnHighLight']['Consumed'] = '';
                                $row['error'][] = __('The Consumed of day > 1', true);
                            }
                            /**
                             * Absence
                             */
                            $absenceRequests = $this->AbsenceRequest->find("all", array(
                                'recursive' => -1,
                                'fields' => array('id', 'date', 'absence_pm', 'absence_am', 'response_am', 'response_pm', 'employee_id'),
                                "conditions"    => array('date' => $date, 'employee_id' => $resourceId)));
                            $totalAbsence = 0;
                            if(!empty($absenceRequests)){
                                foreach($absenceRequests as $absenceRequest){
                                    $absenceRequest = array_shift($absenceRequest);
                                    foreach (array('am', 'pm') as $type) {
                                        if ($absenceRequest['absence_' . $type] && $absenceRequest['absence_' . $type] != '-1'){
                                            $totalAbsence += 0.5;
                                            if(!isset($cosumedFollowDates[$resourceId][$date])){
                                                $cosumedFollowDates[$resourceId][$date] = 0;
                                            }
                                            $cosumedFollowDates[$resourceId][$date] += 0.5;
                                        }
                                    }
                                }
                            }
                            if($totalAbsence >= 1){
                                $allowImport = false;
                                $row['columnHighLight']['Date'] = '';
                                $row['error'][] = __('The DAY-OFF or WEEKEND', true);
                            }
                            if($allowImport == true) {
                                // Kiem tra part va phase
                                if(empty($row['Part'])){
                                    if(empty($row['Phase'])){ // Activity
                                        /**
                                         * Kiem tra xem co Activity ko?
                                         */
                                        $activities = $this->Activity->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'company_id' => $employeeName['company_id'],
                                                'REPLACE(name, "\'", "")' => str_replace(array('\'', '"'), '', trim($row['Project Name or Activity Name']))
                                            ),
                                            'fields' => array('id', 'activated')
                                        ));
                                        if(!empty($activities) && !empty($activities['Activity']['id'])){ //co activity
                                            if($activities['Activity']['activated']){
                                                if(!empty($row['Task'])){
                                                    $activityTasks = $this->ActivityTask->find('first', array(
                                                        'recursive' => -1,
                                                        'conditions' => array(
                                                            'activity_id' => $activities['Activity']['id'],
                                                            'name' => trim($row['Task'])
                                                        ),
                                                        'fields' => array('id')
                                                    ));
                                                    if(!empty($activityTasks) && $activityTasks['ActivityTask']['id']){ // co task roi thi lay id
                                                        if(!empty($listTaskHaveImports[$resourceId][$date][trim($row['Task'])])){
                                                            $row['columnHighLight']['Task'] = '';
                                                            $row['error'][] = __('The Task name has identical', true);
                                                        } else {
                                                            $checkTaskInRequests = $this->ActivityRequest->find('first', array(
                                                                'recursive' => -1,
                                                                'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $date, 'employee_id' => $resourceId, 'task_id' => $activityTasks['ActivityTask']['id']),
                                                                'fields' => array('id')
                                                            ));
                                                            if(!empty($checkTaskInRequests) && !empty($checkTaskInRequests['ActivityRequest']['id'])){
                                                                $row['columnHighLight']['Task'] = '';
                                                                $row['error'][] = __('The Task name has exist in timesheet.', true);
                                                            } else {
                                                                $row['data']['task_id'] = $activityTasks['ActivityTask']['id'];
                                                                $listTaskHaveImports[$resourceId][$date][trim($row['Task'])] = trim($row['Task']);
                                                            }
                                                        }
                                                    } else { // chua co thi tao task moi
                                                        $this->ActivityTask->create();
                                                        if($this->ActivityTask->save(array('name' => trim($row['Task']), 'activity_id' => $activities['Activity']['id']))){
                                                            $lastTaskId = $this->ActivityTask->getLastInsertID();
                                                            if(!empty($listTaskHaveImports[$resourceId][$date][trim($row['Task'])])){
                                                                $row['columnHighLight']['Task'] = '';
                                                                $row['error'][] = __('The Task name has identical', true);
                                                            } else {
                                                                $checkTaskInRequests = $this->ActivityRequest->find('first', array(
                                                                    'recursive' => -1,
                                                                    'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $date, 'employee_id' => $resourceId, 'task_id' => $lastTaskId),
                                                                    'fields' => array('id')
                                                                ));
                                                                if(!empty($checkTaskInRequests) && !empty($checkTaskInRequests['ActivityRequest']['id'])){
                                                                    $row['columnHighLight']['Task'] = '';
                                                                    $row['error'][] = __('The Task name has exist in timesheet.', true);
                                                                } else {
                                                                    $row['data']['task_id'] = $lastTaskId;
                                                                    $listTaskHaveImports[$resourceId][$date][trim($row['Task'])] = trim($row['Task']);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                $row['columnHighLight']['Project Name or Activity Name'] = '';
                                                $row['error'][] = __('The Project or Activity Not Activated', true);
                                            }
                                        } else { // kiem tra xem co project ko?
                                            $projects = $this->Project->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => array(
                                                    'company_id' => $employeeName['company_id'],
                                                    'REPLACE(project_name, "\'", "")' => str_replace(array('\'', '"'), '', trim($row['Project Name or Activity Name']))
                                                ),
                                                'fields' => array('id', 'activity_id')
                                            ));
                                            if(!empty($projects) && !empty($projects['Project']['id'])){
                                                if(!empty($projects['Project']['activity_id'])){
                                                    $row['columnHighLight']['Phase'] = '';
                                                    $row['columnHighLight']['Part'] = '';
                                                    $row['error'][] = __('The Phase And Part Not Created', true);
                                                } else {
                                                    $row['columnHighLight']['Project Name or Activity Name'] = '';
                                                    $row['error'][] = __('The Project or Activity Not Created', true);
                                                }
                                            } else {
                                                $row['columnHighLight']['Project Name or Activity Name'] = '';
                                                $row['error'][] = __('The Project or Activity Not Created', true);
                                            }
                                        }
                                    } else {
                                        $projects = $this->Project->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'company_id' => $employeeName['company_id'],
                                                'REPLACE(project_name, "\'", "")' => str_replace(array('\'', '"'), '', trim($row['Project Name or Activity Name']))
                                            ),
                                            'fields' => array('id', 'activity_id')
                                        ));
                                        if(!empty($projects) && !empty($projects['Project']['id'])){
                                            if(!empty($projects['Project']['activity_id'])){
                                                if(in_array($row['Phase'], $phaseOfCompanies)){ // kiem tra xem phase co trong he thong cong ty ko?
                                                    $_phaseOfCompanies = array_flip($phaseOfCompanies);
                                                    $phasId = !empty($_phaseOfCompanies[$row['Phase']]) ? $_phaseOfCompanies[$row['Phase']] : 0;
                                                    $_phaseOfCompanies = array();
                                                    $projectPhasePlans = $this->ProjectPhasePlan->find('first', array(
                                                        'recursive' => -1,
                                                        'conditions' => array('project_planed_phase_id' => $phasId, 'project_id' => $projects['Project']['id']),
                                                        'fields' => array('id')
                                                    ));
                                                    if(!empty($projectPhasePlans) && !empty($projectPhasePlans['ProjectPhasePlan']['id'])){
                                                        $projectTasks = $this->ProjectTask->find('first', array(
                                                            'recursive' => -1,
                                                            'conditions' => array(
                                                                'project_id' => $projects['Project']['id'],
                                                                'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                                'task_title' => trim($row['Task'])
                                                            ),
                                                            'fields' => array('id')
                                                        ));
                                                        if(!empty($projectTasks) && !empty($projectTasks['ProjectTask']['id'])){
                                                            if(!empty($listTaskHaveImports[$resourceId][$date][trim($row['Task'])])){
                                                                $row['columnHighLight']['Task'] = '';
                                                                $row['error'][] = __('The Task name has identical', true);
                                                            } else {
                                                                /**
                                                                 * Tim activity task linked voi project task
                                                                 */
                                                                $actiTasks = $this->ActivityTask->find('first', array(
                                                                    'recursive' => -1,
                                                                    'conditions' => array('project_task_id' => $projectTasks['ProjectTask']['id']),
                                                                    'fields' => array('id')
                                                                ));
                                                                if(!empty($actiTasks) && !empty($actiTasks['ActivityTask']['id'])){
                                                                    $checkTaskInRequests = $this->ActivityRequest->find('first', array(
                                                                        'recursive' => -1,
                                                                        'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $date, 'employee_id' => $resourceId, 'task_id' => $actiTasks['ActivityTask']['id']),
                                                                        'fields' => array('id')
                                                                    ));
                                                                    if(!empty($checkTaskInRequests) && !empty($checkTaskInRequests['ActivityRequest']['id'])){
                                                                        $row['columnHighLight']['Task'] = '';
                                                                        $row['error'][] = __('The Task name has exist in timesheet.', true);
                                                                    } else {
                                                                        $row['data']['task_id'] = $actiTasks['ActivityTask']['id'];
                                                                        $listTaskHaveImports[$resourceId][$date][trim($row['Task'])] = trim($row['Task']);
                                                                    }
                                                                } else {
                                                                    $row['columnHighLight']['Task'] = '';
                                                                    $row['error'][] = __('The Task Not Created', true);
                                                                }
                                                            }
                                                        } else {
                                                            $this->ProjectTask->create();
                                                            $pTaskSave = array(
                                                                'task_title' => trim($row['Task']),
                                                                'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                                'project_id' => $projects['Project']['id']
                                                            );
                                                            if($this->ProjectTask->save($pTaskSave)){
                                                                $pTaskId = $this->ProjectTask->getLastInsertID();
                                                                $this->ActivityTask->create();
                                                                if($this->ActivityTask->save(array('name' => trim($row['Phase'] .'/'. $row['Task']), 'activity_id' => $projects['Project']['activity_id'], 'project_task_id' => $pTaskId))){
                                                                    $lastTaskId = $this->ActivityTask->getLastInsertID();
                                                                    if(!empty($listTaskHaveImports[$resourceId][$date][trim($row['Task'])])){
                                                                        $row['columnHighLight']['Task'] = '';
                                                                        $row['error'][] = __('The Task name has identical', true);
                                                                    } else {
                                                                        $checkTaskInRequests = $this->ActivityRequest->find('first', array(
                                                                            'recursive' => -1,
                                                                            'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $date, 'employee_id' => $resourceId, 'task_id' => $lastTaskId),
                                                                            'fields' => array('id')
                                                                        ));
                                                                        if(!empty($checkTaskInRequests) && !empty($checkTaskInRequests['ActivityRequest']['id'])){
                                                                            $row['columnHighLight']['Task'] = '';
                                                                            $row['error'][] = __('The Task name has exist in timesheet.', true);
                                                                        } else {
                                                                            $row['data']['task_id'] = $lastTaskId;
                                                                            $listTaskHaveImports[$resourceId][$date][trim($row['Task'])] = trim($row['Task']);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $row['columnHighLight']['Phase'] = '';
                                                        $row['error'][] = __('The Phase Not Created', true);
                                                    }
                                                } else {
                                                    $row['columnHighLight']['Phase'] = '';
                                                    $row['error'][] = __('The Phase name not found in company', true);
                                                }
                                            } else {
                                                $row['columnHighLight']['Project Name or Activity Name'] = '';
                                                $row['error'][] = __('The Project or Activity Not Created', true);
                                            }
                                        } else {
                                            $row['columnHighLight']['Project Name or Activity Name'] = '';
                                            $row['error'][] = __('The Project or Activity Not Created', true);
                                        }
                                    }
                                } else { // else part
                                    if(empty($row['Phase'])){
                                        $row['columnHighLight']['Phase'] = '';
                                        $row['error'][] = __('The Phase Not Created', true);
                                    } else {
                                        $projects = $this->Project->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array(
                                                'company_id' => $employeeName['company_id'],
                                                'REPLACE(project_name, "\'", "")' => str_replace(array('\'', '"'), '', trim($row['Project Name or Activity Name']))
                                            ),
                                            'fields' => array('id', 'project_name', 'activity_id')
                                        ));
                                        if(!empty($projects) && !empty($projects['Project']['id'])){
                                            if(!empty($projects['Project']['activity_id'])){
                                                $projectParts = $this->ProjectPart->find('first', array(
                                                    'recursive' => -1,
                                                    'fields' => array('id'),
                                                    'conditions' => array(
                                                        'title' => trim($row['Part']),
                                                        'project_id' => $projects['Project']['id']
                                                    )
                                                ));
                                                if(!empty($projectParts) && !empty($projectParts['ProjectPart']['id'])){
                                                    if(in_array($row['Phase'], $phaseOfCompanies)){ // kiem tra xem phase co trong he thong cong ty ko?
                                                        $_phaseOfCompanies = array_flip($phaseOfCompanies);
                                                        $phasId = !empty($_phaseOfCompanies[$row['Phase']]) ? $_phaseOfCompanies[$row['Phase']] : 0;
                                                        $_phaseOfCompanies = array();
                                                        $projectPhasePlans = $this->ProjectPhasePlan->find('first', array(
                                                            'recursive' => -1,
                                                            'conditions' => array('project_part_id' => $projectParts['ProjectPart']['id'], 'project_planed_phase_id' => $phasId, 'project_id' => $projects['Project']['id']),
                                                            'fields' => array('id')
                                                        ));
                                                        if(!empty($projectPhasePlans) && !empty($projectPhasePlans['ProjectPhasePlan']['id'])){
                                                            $projectTasks = $this->ProjectTask->find('first', array(
                                                                'recursive' => -1,
                                                                'conditions' => array(
                                                                    'project_id' => $projects['Project']['id'],
                                                                    'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                                    'task_title' => trim($row['Task'])
                                                                ),
                                                                'fields' => array('id')
                                                            ));
                                                            if(!empty($projectTasks) && !empty($projectTasks['ProjectTask']['id'])){
                                                                if(!empty($listTaskHaveImports[$resourceId][$date][trim($row['Task'])])){
                                                                    $row['columnHighLight']['Task'] = '';
                                                                    $row['error'][] = __('The Task name has identical', true);
                                                                } else {
                                                                    /**
                                                                     * Tim activity task linked voi project task
                                                                     */
                                                                    $actiTasks = $this->ActivityTask->find('first', array(
                                                                        'recursive' => -1,
                                                                        'conditions' => array('project_task_id' => $projectTasks['ProjectTask']['id']),
                                                                        'fields' => array('id')
                                                                    ));
                                                                    if(!empty($actiTasks) && !empty($actiTasks['ActivityTask']['id'])){
                                                                        $row['data']['task_id'] = $actiTasks['ActivityTask']['id'];
                                                                        $listTaskHaveImports[$resourceId][$date][trim($row['Task'])] = trim($row['Task']);
                                                                    } else {
                                                                        $row['columnHighLight']['Task'] = '';
                                                                        $row['error'][] = __('The Task Not Created', true);
                                                                    }
                                                                }
                                                            } else {
                                                                $this->ProjectTask->create();
                                                                $pTaskSave = array(
                                                                    'task_title' => trim($row['Task']),
                                                                    'project_planed_phase_id' => $projectPhasePlans['ProjectPhasePlan']['id'],
                                                                    'project_id' => $projects['Project']['id']
                                                                );
                                                                if($this->ProjectTask->save($pTaskSave)){
                                                                    $pTaskId = $this->ProjectTask->getLastInsertID();
                                                                    $this->ActivityTask->create();
                                                                    if($this->ActivityTask->save(array('name' => trim($row['Phase'] .'/'. $row['Task']), 'activity_id' => $projects['Project']['activity_id'], 'project_task_id' => $pTaskId))){
                                                                        $lastTaskId = $this->ActivityTask->getLastInsertID();
                                                                        if(!empty($listTaskHaveImports[$resourceId][$date][trim($row['Task'])])){
                                                                            $row['columnHighLight']['Task'] = '';
                                                                            $row['error'][] = __('The Task name has identical', true);
                                                                        } else {
                                                                            $row['data']['task_id'] = $lastTaskId;
                                                                            $listTaskHaveImports[$resourceId][$date][trim($row['Task'])] = trim($row['Task']);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $row['columnHighLight']['Phase'] = '';
                                                            $row['error'][] = __('The Phase Not Created', true);
                                                        }
                                                    } else {
                                                        $row['columnHighLight']['Phase'] = '';
                                                        $row['error'][] = __('The Phase name not found in company', true);
                                                    }
                                                } else {
                                                    $row['columnHighLight']['Part'] = '';
                                                    $row['error'][] = __('The Part Not Created', true);
                                                }
                                            } else {
                                                $row['columnHighLight']['Project Name or Activity Name'] = '';
                                                $row['error'][] = __('The Project or Activity Not Created', true);
                                            }
                                        } else {
                                            $row['columnHighLight']['Project Name or Activity Name'] = '';
                                            $row['error'][] = __('The Project or Activity Not Created', true);
                                        }
                                    }
                                }
                                if(empty($row['error'])){
                                    if(!isset($cosumedFollowDates[$resourceId][$date])){
                                        $cosumedFollowDates[$resourceId][$date] = 0;
                                    }
                                    $cosumedFollowDates[$resourceId][$date] += $requests;
                                }
                                $row['data']['value'] = $consumed;
                                $row['data']['date'] = $date;
                                $row['data']['company_id'] = $employeeName['company_id'];
                                if(!empty($this->data) && !empty($this->data['Import']) && !empty($this->data['Import']['start_date']) && !empty($this->data['Import']['end_date']) && (!empty($this->data['Import']['auto_valid']) || $this->data['Import']['auto_valid'] == 1)){
                                    if(strtotime($this->data['Import']['start_date']) <= $date && $date <= strtotime($this->data['Import']['end_date'])){
                                        $row['data']['status'] = 0;
                                    } else {
                                        $row['data']['status'] = -1;
                                    }
                                } else {
                                    $row['data']['status'] = -1;
                                }
                                $row['data']['activity_id'] = 0;
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
                if(!empty($records['Create'])){
                    foreach($records['Create'] as $key => $value){
                        $val = $value['data'];
                        $_employee_id = $val['employee_id'];
                        $_date = $val['date'];
                        $_value = $val['value'];
                        $totalConsumed = !empty($cosumedFollowDates) && !empty($cosumedFollowDates[$_employee_id]) && !empty($cosumedFollowDates[$_employee_id][$_date]) ? $cosumedFollowDates[$_employee_id][$_date] : 0;
                        if($totalConsumed > 1){
                            $value['columnHighLight']['Date'] = '';
                            $value['columnHighLight']['Consumed'] = '';
                            $value['error'][] = __('The Consumed of day > 1', true);
                            unset($records['Create'][$key]);
                            $records['Error'][] = $value;
                        } else {
                            //$records['Create'][] = $row;
                        }
                    }
                }
                if(!empty($cosumedFollowDates)){
                    foreach($cosumedFollowDates as $employee_id => $cosumedFollowDate){
                        foreach($cosumedFollowDate as $date => $value){
                            if($value < 1){
                                $status = -1;
                                if(!empty($this->data) && !empty($this->data['Import']) && !empty($this->data['Import']['start_date']) && !empty($this->data['Import']['end_date']) && (!empty($this->data['Import']['auto_valid']) || $this->data['Import']['auto_valid'] == 1)){
                                    if(strtotime($this->data['Import']['start_date']) <= $date && $date <= strtotime($this->data['Import']['end_date'])){
                                        $status = 0;
                                    }
                                }
                                $records['Create'][] = array(
                                    'Resource' => !empty($listEmployees[$employee_id]) && !empty($listEmployees[$employee_id]['fullname']) ? $listEmployees[$employee_id]['fullname'] : '',
                                    'Code1' => !empty($listEmployees[$employee_id]) && !empty($listEmployees[$employee_id]['code_id']) ? $listEmployees[$employee_id]['code_id'] : '',
                                    'Project Name or Activity Name' => 'OUT',
                                    'Part' => '',
                                    'Phase' => '',
                                    'Task' => 'OUT',
                                    'Date' => date('d/m/Y', $date),
                                    'Consumed' => round(1-$value, 2),
                                    'data' => array(
                                            'employee_id' => $employee_id,
                                            'task_id' => $taskOutId,
                                            'value' => round(1-$value, 2),
                                            'date' => $date,
                                            'company_id' => $employeeName['company_id'],
                                            'status' => $status,
                                            'activity_id' => 0
                                    ),
                                    'error' => array()
                                );
                                if(!isset($cosumedFollowDates[$employee_id][$date])){
                                    $cosumedFollowDates[$employee_id][$date] = 0;
                                }
                                $cosumedFollowDates[$employee_id][$date] += round(1-$value, 2);
                            }
                        }
                    }
                }
                if(!empty($this->data) && !empty($this->data['Import']) && !empty($this->data['Import']['start_date']) && !empty($this->data['Import']['end_date'])){
                    foreach($listEmployees as $employee_id => $listEmployee){
                        $setEmployee[] = $employee_id;
                        $_start = strtotime($this->data['Import']['start_date']);
                        $_end = strtotime($this->data['Import']['end_date']);
                        $setValid = $this->data['Import']['auto_valid'];
                        $setStartDate = $_start;
                        $setEndDate = $_end;
                        while($_start <= $_end){
                            if(!empty($cosumedFollowDates) && !empty($cosumedFollowDates[$employee_id]) && !empty($cosumedFollowDates[$employee_id][$_start])){
                                // da co trong file import va timesheet.Phan' di.
                            } else {
                                $requests = $this->ActivityRequest->find('all', array(
                                    'recursive' => -1,
                                    'conditions' => array('company_id' => $employeeName['company_id'], 'date' => $_start, 'employee_id' => $employee_id),
                                    'fields' => array('SUM(value) AS Total')
                                ));
                                $requests = !empty($requests) && !empty($requests[0]) && !empty($requests[0][0]) && isset($requests[0][0]['Total']) ? (float) $requests[0][0]['Total'] : 0;
                                if($requests < 1){
                                    $records['Create'][] = array(
                                        'Resource' => !empty($listEmployees[$employee_id]) && !empty($listEmployees[$employee_id]['fullname']) ? $listEmployees[$employee_id]['fullname'] : '',
                                        'Code1' => !empty($listEmployees[$employee_id]) && !empty($listEmployees[$employee_id]['code_id']) ? $listEmployees[$employee_id]['code_id'] : '',
                                        'Project Name or Activity Name' => 'OUT',
                                        'Part' => '',
                                        'Phase' => '',
                                        'Task' => 'OUT',
                                        'Date' => date('d/m/Y', $_start),
                                        'Consumed' => round(1-$requests, 2),
                                        'data' => array(
                                                'employee_id' => $employee_id,
                                                'task_id' => $taskOutId,
                                                'value' => round(1-$requests, 2),
                                                'date' => $_start,
                                                'company_id' => $employeeName['company_id'],
                                                'status' => (!empty($setValid) || $setValid == 1) ? 0 : -1,
                                                'activity_id' => 0
                                        ),
                                        'error' => array()
                                    );
                                }
                            }
                            $_start = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+1, date("Y", $_start));
                        }
                    }
                }
                $setEmployee = !empty($setEmployee) ? implode('-', $setEmployee) : '';
                unlink($filename);
            }
            $this->set(compact('setValid', 'setStartDate', 'setEndDate', 'setEmployee'));
            $this->set('records', $records);
            $this->set('default', $default);
        } else {
            $this->redirect(array('action' => 'request'));
        }
    }

    /**
     * Save import of project task
     */
    function save_file_import($setValid = 0, $setStartDate = 0, $setEndDate = 0, $setEmployee = '') {
        set_time_limit(0);
        $this->loadModel('Project');
        $employeeName = $this->_getEmpoyee();
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
                    $this->redirect(array('action' => 'import_timesheet'));
                }
                /**
                 * Send request
                 */
                if($setValid == 1 && !empty($setStartDate) && !empty($setEndDate) && !empty($setEmployee)){
                    $this->loadModel('ActivityRequestConfirm');
                    $this->loadModel('ActivityRequest');
                    $listWeekOfMonths = $this->_splitWeekOfMonth($setStartDate, $setEndDate);
                    $employees = explode('-', $setEmployee);
                    foreach($employees as $employee){
                        if(!empty($employee)){
                            foreach($listWeekOfMonths as $start => $end){
                                $data = array(
                                    'employee_id' => $employee,
                                    'start' => $start,
                                    'end' => $end
                                );
                                /**
                                 * Kiem tra tuan nay co co ton tai trong activity request confirm hay chua?
                                 */
                                $last = $this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'status'),
                                    'conditions' => $data));
                                /**
                                 * Neu co roi thi tien hanh update khong thi tao moi
                                 */
                                if ($last) {
                                    $last = array_shift($last);
                                    $this->ActivityRequestConfirm->id = $last['id'];
                                } else {
                                    $this->ActivityRequestConfirm->create();
                                }
                                $data['status'] = 0;
                                $data['company_id'] = $employeeName['company_id'];
                                $this->ActivityRequestConfirm->save($data);
                                $this->ActivityRequest->updateAll(array('status' => 0), array(
                                    'date BETWEEN ? AND ?' => array($start, $end),
                                    'employee_id' => $employee,
                                    'NOT' => array('status' => 2)
                                ));
                            }
                        }
                    }
                }
                $this->loadModel('ActivityRequest');
                $this->ActivityRequest->create();
                $this->ActivityRequest->saveAll($import);
                $this->Session->setFlash(__('Saved', true));
                if($setValid == 1){
                    $this->redirect(array('action' => 'import_timesheet', $setValid, $setStartDate, $setEndDate, $setEmployee));
                } else {
                    $this->redirect(array('action' => 'import_timesheet'));
                }

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

    /*
    new enhancment: to validate screen: list all request not validated by employee by week
    */
    public function to_validate(){
        $this->loadModels('ActivityRequest', 'ActivityRequestConfirm', 'ActivityTask', 'Activity', 'AbsenceRequest', 'Absence', 'Employee');
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        //parse query
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;

        if (!($params = $this->_getProfits($profiltId, $getDataByPath))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'response'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;

        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        $endOfWeek = 'monday';
        $totalWorkDaysInWeek = 0;
        foreach($workdays as $name => $amount){
            if( floatval($amount) > 0 ){
                $endOfWeek = $name;
                $totalWorkDaysInWeek++;
            }
        }
        $dto = new DateTime();
        $dto->setISODate($year, 1);
        $dto->setTime(0, 0, 0);
        $dto->modify('monday this week');
        $start = $dto->getTimestamp();

        $dto->setDate($year, 12, 28);
        $curWeek = $dto->format('W');
        $dto->setISODate($year, $curWeek);
        $dto->modify($endOfWeek . ' this week');
        $end = $dto->getTimestamp();

        $employeeIds = array_keys($employees);

        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employeeIds,
                'status' => 0,
                'value !=' => 0,
                'date BETWEEN ? AND ?' => array($start, $end)
            ),
            'order' => array('date' => 'ASC')
        ));
        $this->ActivityRequestConfirm->virtualFields['year'] = 'FROM_UNIXTIME(ROUND((start+end)/2), "%Y")';
        $this->ActivityRequestConfirm->virtualFields['week'] = 'WEEKOFYEAR(FROM_UNIXTIME(ROUND((start+end)/2)))';
        $confirms = $this->ActivityRequestConfirm->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employeeIds,
                'company_id' => $employeeName['company_id'],
                'status' => 0,
                'year' => $year
            )
        ));
        //$this->AbsenceRequest->virtualFields['absence_value'] = '(CASE WHEN response_am = "validated" THEN 0.5 ELSE 0 END) + (CASE WHEN response_pm = "validated" THEN 0.5 ELSE 0 END)';
        $this->AbsenceRequest->virtualFields['week'] = 'WEEKOFYEAR(FROM_UNIXTIME(`date`))';


        $activityIds = $requests ? array_unique(Set::extract($requests, '{n}.ActivityRequest.activity_id')) : array();;
        $taskIds = $requests ? array_unique(Set::extract($requests, '{n}.ActivityRequest.task_id')) : array();
        //get activity name by id/task id
        $activitiesByTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ActivityTask.id' => $taskIds
            ),
            'joins' => array(
                array(
                    'table' => 'activities',
                    'alias' => 'Activity',
                    'conditions' => array('Activity.id = ActivityTask.activity_id')
                )
            ),
            'fields' => array('ActivityTask.id', 'Activity.name')
        ));
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'Activity.id' => $activityIds
            ),
            'fields' => array('Activity.id', 'Activity.name')
        ));

        $data = array();
        //attach absence here
        foreach($confirms as $confirm){
            $request = $confirm['ActivityRequestConfirm'];
            $id = $request['employee_id'];
            $week = $request['week'];
            $data[$week][$id] = array();
            $absence = $this->AbsenceRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $id,
                    'date BETWEEN ? AND ?' => array($start, $end),
                    'OR' => array(
                        'response_am' => 'validated',
                        'response_pm' => 'validated'
                    ),
                    'week' => $week
                )
            ));
            if( $absence )$absence = Set::combine($absence, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
            $data[$week][$id]['absences'] = $absence;
            $data[$week][$id]['requests'] = array();
            $data[$week][$id]['name'] = $employees[$id];
        }
        //attach request here
        foreach($requests as $request){
            $week = intval(date('W', $request['ActivityRequest']['date'])); //fix issue with 01 / 1
            $id = $request['ActivityRequest']['employee_id'];
            $date = $request['ActivityRequest']['date'];
            if( !isset($data[$week]) ){
                $data[$week] = array();
            }
            if( !isset($data[$week][$id]) ){
                $data[$week][$id] = array();
            }
            if( !isset($data[$week][$id]['requests'][$date]) ){
                $data[$week][$id]['requests'][$date] = array();
            }
            $data[$week][$id]['requests'][$date][] = $request['ActivityRequest'];
        }

        $absences = $this->Absence->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $employeeName['company_id']
            ),
            'fields' => array('id', 'print')
        ));

        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($start, $end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        ksort($data);
        $this->loadModel('CompanyConfig');
        $showAllPicture = $this->CompanyConfig->find('first', array(
            'recursive' => 1,
            'conditions' => array(
                'company' => $employeeName['company_id'],
                'cf_name' => 'display_picture_all_resource'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showAllPicture = !empty($showAllPicture) ? $showAllPicture['CompanyConfig']['cf_value'] : 0;
        $hourOfEmployees = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeIds),
            'fields' => array('id', 'hour', 'minutes')
        ));
        $hourOfEmployees = !empty($hourOfEmployees) ? Set::combine($hourOfEmployees, '{n}.Employee.id', '{n}.Employee') : array();
        $this->set(compact('profit', 'paths', 'year', 'getDataByPath', 'employees', 'employeeName', 'data', 'absences', 'workdays', 'holidays', 'constraint', 'activities', 'activitiesByTasks', 'endOfWeek', 'totalWorkDaysInWeek', 'showAllPicture', 'managerHour', 'hourOfEmployees'));
    }
    public function not_sent_yet(){
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $this->loadModels('ActivityRequest', 'ActivityRequestConfirm', 'ActivityTask', 'Activity', 'AbsenceRequest', 'Absence','MailNotSendYet','Employee');
        $company_id_mail = !empty($this->employee_info['CompanyEmployeeReference']['company_id']) ? $this->employee_info['CompanyEmployeeReference']['company_id'] : 0;
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        $info_mail = $this->MailNotSendYet->find('first', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $company_id_mail),
                                'fields' => array('id','subject','content', 'employee_id', 'updated')
                            ));
        if( $info_mail ){
            $employee_name_mail = $this->Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id' => $info_mail['MailNotSendYet']['employee_id']
                ),
                'fields' => array('id', 'first_name', 'last_name')
            ));
            $employee_name_mail = $employee_name_mail['Employee']['first_name'] . ' ' . $employee_name_mail['Employee']['last_name'];
        }
        //parse query
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
        $emp = $this->Employee->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $company_id_mail),
                                'fields' => array('id','end_date','start_date', 'hour', 'minutes')
                            ));
        $emp_end = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.end_date') : array();
        $emp_start = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.start_date') : array();
        $hourDayOfEmployees = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', array('{0}: {1}', '{n}.Employee.hour', '{n}.Employee.minutes')) : array();
        if (!($params = $this->_getProfits($profiltId, $getDataByPath))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'response'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;

        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        $endOfWeek = 'monday';
        $totalWorkDaysInWeek = 0;
        foreach($workdays as $name => $amount){
            if( floatval($amount) > 0 ){
                $endOfWeek = $name;
                $totalWorkDaysInWeek++;
            }
        }
        $dto = new DateTime();
        $dto->setISODate($year, 1);
        $dto->setTime(0, 0, 0);
        $dto->modify('monday this week');
        $start = $dto->getTimestamp();

        $dto->setDate($year, 12, 28);
        $curWeek = $dto->format('W');
        $dto->setISODate($year, $curWeek);
        $dto->modify($endOfWeek . ' this week');
        $end = $dto->getTimestamp();

        if( $year == date('Y') ){
            $end = time();
        }

        $employeeIds = array_keys($employees);
        //toi uu hoa cho nay: chi lay status != 2 vi neu lay tat ca => out of memory
        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employeeIds,
                'company_id' => $employeeName['company_id'],
                'date BETWEEN ? AND ?' => array($start, $end),
                'status != 2'
            ),
            'order' => array('date' => 'ASC')
        ));
        $this->ActivityRequestConfirm->virtualFields['year'] = 'FROM_UNIXTIME(ROUND((start+end)/2), "%Y")';
        $this->ActivityRequestConfirm->virtualFields['week'] = 'WEEKOFYEAR(FROM_UNIXTIME(ROUND((start+end)/2)))';
        $_confirms = $this->ActivityRequestConfirm->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employeeIds,
                'company_id' => $employeeName['company_id'],
                'year' => $year
            ),
            'order' => array('start')
        ));
        $confirms = array();
        if(!empty($_confirms)){
            foreach($_confirms as $_confirm){
                $dx = $_confirm['ActivityRequestConfirm'];
                $weekOfYear = $dx['year'] . '-' . $dx['week'];
                if(!isset($confirms[$dx['employee_id']][$dx['week']])){
                    $confirms[$dx['employee_id']][$dx['week']] = array();
                }
                $confirms[$dx['employee_id']][$dx['week']] = $dx['status'];
            }
        }
        //$this->AbsenceRequest->virtualFields['absence_value'] = '(CASE WHEN response_am = "validated" THEN 0.5 ELSE 0 END) + (CASE WHEN response_pm = "validated" THEN 0.5 ELSE 0 END)';
        $this->AbsenceRequest->virtualFields['week'] = 'WEEKOFYEAR(FROM_UNIXTIME(`date`))';
        $aRequests = $this->AbsenceRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employeeIds,
                'date BETWEEN ? AND ?' => array($start, $end)
            ),
            'fields' => array('date', 'employee_id', 'absence_am', 'absence_pm', 'response_am', 'response_pm', 'week')
        ));
        $absenceRequests = array();
        foreach($aRequests as $a){
            $d = $a['AbsenceRequest'];
            $absenceRequests[$d['employee_id']][$d['week']][$d['date']] = $d;
        }
        //$absenceRequests = !empty($aRequests) ? Set::combine($aRequests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id') : array();

        $activityIds = $taskIds = $listRequests = array();
        if(!empty($requests)){
            foreach($requests as $request){
                $dx = $request['ActivityRequest'];
                $week = intval(date('W', $dx['date']));
                if(!in_array($dx['activity_id'], $activityIds)){
                    $activityIds[] = $dx['activity_id'];
                }
                if(!in_array($dx['task_id'], $taskIds)){
                    $taskIds[] = $dx['task_id'];
                }
                $listRequests[$dx['employee_id']][$week][$dx['date']][] = $dx;
            }
        }
        $data = array();
        // The Vu code ngu chỗ này: for từng ngày/cả năm (ko dừng lại khi đến ngày hiện tại), mỗi ngày lại for từng nhân viên
        while($start <= $end){
            $monday = (strtolower(date('l', $start)) == 'monday') ? $start : strtotime('last monday', $start);
            $sunday = (strtolower(date('l', $start)) == 'sunday') ? $start : strtotime('next sunday', $start);
            $AVGDay = ($monday+$sunday)/2;
            $week = intval(date('W', $AVGDay));
            foreach($employeeIds as $id){
                $employee_endday = strtotime($emp_end[$id]);
                $employee_endday = !empty($employee_endday) ? $employee_endday : 0;
                $employee_startday = strtotime($emp_start[$id]);
                $employee_startday = !empty($employee_startday) ? $employee_startday : 0;
                if(($employee_endday >= $start || $employee_endday == 0 || (!empty($emp_end[$id]) && $emp_end[$id]  == '0000-00-00')) && ($employee_startday < $sunday || $employee_startday == 0 || (!empty($emp_start[$id]) && $emp_start[$id]  == '0000-00-00')) )
                {
                    // $dayHaveUsings = array_keys($checkInputDays[$id]);
                    // $status = !empty($checkInputDays[$id]) && !empty($checkInputDays[$id][$start]) ? $checkInputDays[$id][$start] : -1;
                    if(isset($confirms[$id][$week]))
                    {
                        $statusConfirm = $confirms[$id][$week];
                    }
                    else
                    {
                        $statusConfirm = -1;
                    }
                    if($statusConfirm == 2 || $statusConfirm == 0){
                        // ko xu ly
                    } else {
                        $data[$week][$id] = array(
                            'absences' => isset($absenceRequests[$id][$week]) ? $absenceRequests[$id][$week] : array(),
                            'requests' => isset($listRequests[$id][$week]) ? $listRequests[$id][$week] : array(),
                            'name' => $employees[$id],
                            'status_confirm' => $statusConfirm
                        );
                    }
                }
            }
            $start = strtotime('next monday', $start);
            //$end = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
        }
        //get activity name by id/task id
        $activitiesByTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ActivityTask.id' => $taskIds
            ),
            'joins' => array(
                array(
                    'table' => 'activities',
                    'alias' => 'Activity',
                    'conditions' => array('Activity.id = ActivityTask.activity_id')
                )
            ),
            'fields' => array('ActivityTask.id', 'Activity.name')
        ));
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'Activity.id' => $activityIds
            ),
            'fields' => array('Activity.id', 'Activity.name')
        ));
        $absences = $this->Absence->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $employeeName['company_id']
            ),
            'fields' => array('id', 'print')
        ));

        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($start, $end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        $this->loadModel('CompanyConfig');
        $showAllPicture = $this->CompanyConfig->find('first', array(
            'recursive' => 1,
            'conditions' => array(
                'company' => $employeeName['company_id'],
                'cf_name' => 'display_picture_all_resource'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showAllPicture = !empty($showAllPicture) ? $showAllPicture['CompanyConfig']['cf_value'] : 0;
		
		$order_time_sheet = $this->Employee->HistoryFilter->find('first', array(
			'recursive' => -1,
			'fields' => array( 'params'),
			'conditions' => array(
				'path' => 'timesheet_not_sent_yet_order',
				'employee_id' => $this->employee_info['Employee']['id']
		)));
		$order_time_sheet = !empty($order_time_sheet) ? $order_time_sheet['HistoryFilter']['params'] : 0;

        $this->set(compact('employee_name_mail', 'info_mail','profit', 'paths', 'year', 'getDataByPath', 'employees', 'employeeName', 'data', 'absences', 'workdays', 'holidays', 'constraint', 'activities', 'activitiesByTasks', 'endOfWeek', 'totalWorkDaysInWeek', 'showAllPicture', 'hourDayOfEmployees', 'managerHour', 'order_time_sheet'));
    }
    public function not_sent(){
        $this->loadModels('ActivityRequest', 'ActivityRequestConfirm', 'ActivityTask', 'Activity', 'AbsenceRequest', 'Absence');

        //parse query
        $year = isset($this->params['url']['year']) ? $this->params['url']['year'] : date('Y');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;

        if (!($params = $this->_getProfits($profiltId, $getDataByPath))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'response'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;

        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        $endOfWeek = 'monday';
        $totalWorkDaysInWeek = 0;
        foreach($workdays as $name => $amount){
            if( floatval($amount) > 0 ){
                $endOfWeek = $name;
                $totalWorkDaysInWeek++;
            }
        }

        $dto = new DateTime();
        $dto->setISODate($year, 1);
        $dto->setTime(0, 0, 0);
        $dto->modify('monday this week');   //ensure start = monday of week 1 of $year
        $start = $dto->getTimestamp();

        if( $year < date('Y') ){
            $dto->setDate($year, 12, 28);
            $curWeek = $dto->format('W');
            $dto->setISODate($year, $curWeek);
        } else if( $year == date('Y') ) {
            $dto->setDate($year, date('m'), date('d'));
            //exclude this week
            $dto->modify('-1 week');
            $curWeek = $dto->format('W');
            $dto->setISODate($year, $curWeek);
        } else {
            $end = 0;
        }
        if( !isset($end) ){
            $dto->modify($endOfWeek . ' this week');
            $end = $dto->getTimestamp();
            if( $end < $start )$end = 0;
        }
        if( $end === 0 )$this->set('unavailable', 1);

        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->getOptionHolidays($start, $end, $employeeName['company_id']);
        foreach($holidays as $time => $holiday){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }

        $data = range(date('W', $start), date('W', $end));

        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        $this->set(compact('profit', 'paths', 'year', 'getDataByPath', 'employees', 'employeeName', 'endOfWeek', 'totalWorkDaysInWeek', 'start', 'end', 'data'));
    }
    private function saveHistory($id, $type = 'task'){
        $this->loadModel('HistoryFilter');
        $employee_id = $this->employee_info['Employee']['id'];
        $history = $this->HistoryFilter->find('first',array(
            'conditions' => array('HistoryFilter.employee_id'=>$employee_id,'HistoryFilter.path'=>'activity_request_history'),
            'recursive' => -1,
        ));
        if (!$history) {
            $history = $this->HistoryFilter->create();
            $history['HistoryFilter']['path'] = 'activity_request_history';
            $history['HistoryFilter']['employee_id'] = $employee_id;
        }
        $params = array();
        if (isset($history['HistoryFilter']['params'])) {
            $params = json_decode($history['HistoryFilter']['params'],true);
        }

        $newElement = array('id'=>$id,'type'=>$type);
        $exist = false;
        foreach ($params as $key => $param) {
            if ($param['id']==$id&&$param['type']==$type) {
                array_unshift($params,$param);
                $params = array_unique($params, SORT_REGULAR);
                //$this->move_to_top($params,$key);
                $exist = true;
                break;
            }
        }
        if (!$exist) {
            if (count($params)>=10) {
                array_pop($params);
                array_unshift($params,$newElement);
            } else {
                array_unshift($params,$newElement);
            }
        }
        $history['HistoryFilter']['params'] = json_encode($params);
        $history = $this->HistoryFilter->save($history);
        return;
    }
    // save history task
    public function save_history_task() {
        $this->loadModel('HistoryFilter');
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $type = $_POST['type'];
            $employee = $this->Auth->user();
            $employee_id = $employee['Employee']['id'];
            $history = $this->HistoryFilter->find('first',array(
                'conditions' => array('HistoryFilter.employee_id'=>$employee_id,'HistoryFilter.path'=>'activity_request_history'),
                'recursive' => -1,
            ));
            if (!$history) {
                $history = $this->HistoryFilter->create();
                $history['HistoryFilter']['path'] = 'activity_request_history';
                $history['HistoryFilter']['employee_id'] = $employee_id;
            }
            $params = array();
            if (isset($history['HistoryFilter']['params'])) {
                $params = json_decode($history['HistoryFilter']['params'],true);
            }

            $newElement = array('id'=>$id,'type'=>$type);
            $exist = false;
            foreach ($params as $key => $param) {
                if ($param['id']==$id&&$param['type']==$type) {
                    array_unshift($params,$param);
                    $params = array_unique($params, SORT_REGULAR);
                    //$this->move_to_top($params,$key);
                    $exist = true;
                    break;
                }
            }
            if (!$exist) {
                if (count($params)>=10) {
                    array_pop($params);
                    array_unshift($params,$newElement);
                } else {
                    array_unshift($params,$newElement);
                }
            }
            $history['HistoryFilter']['params'] = json_encode($params);
            $history = $this->HistoryFilter->save($history);
            echo $history['HistoryFilter']['params'];
        } else {
            $employee = $this->Auth->user();
            $employee_id = $employee['Employee']['id'];
            $history = $this->HistoryFilter->find('first',array(
                'conditions' => array('HistoryFilter.employee_id'=>$employee_id,'HistoryFilter.path'=>'activity_request_history'),
                'recursive' => -1,
            ));
            if ($history) {
                echo $history['HistoryFilter']['params'];
            } else {
                echo '[]';
            }
        }
        exit;
    }

    public function budget(){
        $this->loadModels('Activity', 'ProjectEmployeeManager', 'Family', 'ActivityBudget', 'ActivityFamily');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $employee_info = $this->employee_info;
        $roles = $this->employee_info['Role']['name'];
        $companyName = $this->employee_info['Company']['company_name'];
        /**
         * Lay profit center va sub profit center cua ong admin/manager nay
         */
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
        $tmpProfit = false;
        if($profiltId == -1){
            $tmpProfit = true;
            $profiltId = $this->employee_info['Employee']['profit_center_id'];
        }
        if (!($params = $this->_getProfits($profiltId, $getDataByPath))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $manager = !empty($params[0]) && !empty($params[0]['manager_id']) ? $params[0]['manager_id'] : 0;
        $company_id = !empty($params[0]) && !empty($params[0]['company_id']) ? $params[0]['company_id'] : '';
        //$managerBackup = !empty($params[0]) && !empty($params[0]['manager_backup_id']) ? $params[0]['manager_backup_id'] : '';
        $managerBackup = '';
        list($profit, $paths, $employees, $employeeName) = $params;
        /**
         * Lay cac activities ma ong nay quan ly. Admin thi lay het
         */
        $groupFamilyAdmins = $groupFamilies = $families = array();
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        /**
         * Get list group family
         */
        $groupFamilyAdmins = $this->ActivityFamily->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'parent_id', 'name'),
            'group' => array('id', 'parent_id'),
            'order' => array('id', 'parent_id')
        ));
        /**
         * Lay danh sach name family
         */
        $families = $this->Family->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        /**
         * Lay danh sach activity budget theo profit va manager cua profit nay
         */
        $_conditions = array('year' => $year, 'company_id' => $company_id, 'profit_id' => $profiltId, 'manager_id' => $manager, 'is_admin' => 0);
        if($tmpProfit == true){
            $_conditions = array('year' => $year, 'company_id' => $company_id, 'is_admin' => 1);
        }
        $activityBudgets = $this->ActivityBudget->find('all', array(
            'recursive' => -1,
            'conditions' => $_conditions,
            'fields' => array('family_id', 'subfamily_id', 'value', 'type', 'date')
        ));
        $budgetFamilies = $budgetSubFamilies = array();
        if(!empty($activityBudgets)){
            foreach($activityBudgets as $activityBudget){
                $dx = $activityBudget['ActivityBudget'];
                if(!empty($dx['subfamily_id'])){
                    if(!isset($budgetSubFamilies[$dx['subfamily_id']][$dx['type']][$dx['date']])){
                        $budgetSubFamilies[$dx['subfamily_id']][$dx['type']][$dx['date']] = '';
                    }
                    $budgetSubFamilies[$dx['subfamily_id']][$dx['type']][$dx['date']] = $dx['value'];
                } else {
                    if(!isset($budgetFamilies[$dx['family_id']][$dx['type']][$dx['date']])){
                        $budgetFamilies[$dx['family_id']][$dx['type']][$dx['date']] = '';
                    }
                    $budgetFamilies[$dx['family_id']][$dx['type']][$dx['date']] = $dx['value'];
                }
            }
        }
        $this->loadModel('BudgetSetting');
         $company_id = $this->employee_info['Company']['id'];
         $budget_settingst=$this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id,
                'currency_budget' =>1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings=!empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '€';
        $profiltId = ($tmpProfit == true) ? -1 : $profiltId;
        $this->set(compact('groupFamilies', 'families', 'year', 'profiltId', 'company_id', 'manager', 'managerBackup', 'budgetFamilies', 'budgetSubFamilies', 'paths', 'profit', 'roles', 'companyName', 'tmpProfit', 'groupFamilyAdmins', 'employee_info','budget_settings'));
    }

    public function update_budget(){
        $this->loadModel('ActivityBudget');
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $datas = $this->data;
            $setDatas = array(
                'company_id' => $datas['company_id'],
                'profit_id' => $datas['profit_id'],
                'manager_id' => $datas['manager_id'],
                'family_id' => $datas['family_id'],
                'subfamily_id' => $datas['subfamily_id'] ? $datas['subfamily_id'] : 0,
                'type' => $datas['type'],
                'is_admin' => $datas['is_admin']
            );
            unset($datas['id']);
            unset($datas['company_id']);
            unset($datas['profit_id']);
            unset($datas['manager_id']);
            unset($datas['family_id']);
            unset($datas['subfamily_id']);
            unset($datas['type']);
            unset($datas['is_admin']);
            if(!empty($datas)){
                $count = 0;
                foreach($datas as $date => $value){
                    $conditions = array('date' => $date, 'year' => date('Y', $date), 'month' => date('m', $date));
                    $conditions = array_merge($conditions, $setDatas);
                    $last = $this->ActivityBudget->find('first', array(
                        'recursive' => -1,
                        'conditions' => $conditions,
                        'fields' => array('id')
                    ));
                    $this->ActivityBudget->create();
                    if(!empty($last) && !empty($last['ActivityBudget']['id'])){
                        $this->ActivityBudget->id = $last['ActivityBudget']['id'];
                    }
                    $saved = array_merge($conditions, array('value' => $value));
                    if ($this->ActivityBudget->save($saved)) {
                        $count++;
                    }
                }
                if($count == count($datas)){
                    $result = true;
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Activity Budget could not be saved. Please, try again.', true), 'error');
                }
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * Export data activity budget
     */
    public function export_budget($year = null, $company_id = null, $profiltId = null, $manager = null, $tmpProfit = false, $managerBackup = null){
        $this->loadModels('ActivityBudget', 'Family', 'Activity', 'ProjectEmployeeManager', 'ActivityFamily');
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('No data to export!', true));
            $this->redirect(array('action' => 'budget', '?' => array('profit' => $profiltId)));
        }
        $datas = array_filter(explode(',', $this->data['Export']['list']));

        $sets = array();
        foreach($datas as $data){
            list($family, $sub, $type) = explode('-', $data);
            $sets[] = '(' . $family . ',' . ($sub ? $sub : 0) . ',"' . $type . '")';
        }
        if($profiltId == -1){
            $tmpProfit = true;
            $profiltId = $this->employee_info['Employee']['profit_center_id'];
        }

        /**
         * Lay danh sach name family
         */
        $families = $this->Family->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        /**
         * Lay danh sach activity budget theo profit va manager cua profit nay
         */
        $_conditions = array('year' => $year, 'company_id' => $company_id, 'profit_id' => $profiltId, 'manager_id' => $manager, 'is_admin' => 0);
        if($tmpProfit == 'yes'){
            $_conditions = array('year' => $year, 'company_id' => $company_id, 'is_admin' => 1);
        }
        //$ds = $this->ActivityBudget->getDatasource();
        $_conditions[] = '(family_id, subfamily_id, type) IN (' . implode(',', $sets) . ')';

        $activityBudgets = $this->ActivityBudget->find('all', array(
            'recursive' => -1,
            'conditions' => $_conditions,
            'fields' => array('family_id', 'subfamily_id', 'value', 'type', 'date')
        ));

        $budgets = array();
        if(!empty($activityBudgets)){
            foreach($activityBudgets as $activityBudget){
                $dx = $activityBudget['ActivityBudget'];

                $key = $dx['family_id'] . '-' . $dx['subfamily_id'] . '-' . $dx['type'];
                $budgets[$key][$dx['date']] = $dx['value'];
                if( !isset($budgets[$key][$year]) )$budgets[$key][$year] = 0;
                $budgets[$key][$year] += $dx['value'];
            }
        }
        $this->set(compact('datas', 'budgets', 'families', 'year', 'profiltId', 'company_id', 'manager'));
        $this->layout = '';
    }

    /**
     * Lay thong tin cua task
     */
    public function get_infor_task(){
        $this->layout = false;
        $this->loadModels('ProjectTask', 'Project', 'ActivityTask', 'ProjectStatus', 'Profile', 'ProjectPriority', 'ActivityRequest');
        $results = array();
        if($_POST){
            if(!empty($_POST['type']) && !empty($_POST['task_id'])){
                /**
                 * Kiem tra list status cua task.
                 * Neu chua co lay tu du lieu ra.
                 * Neu co roi lay tu session ra
                 */
                $status = $this->Session->read('FC_status');
                if(empty($status)){
                    $status = $this->ProjectStatus->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('ProjectStatus.company_id' => $_POST['company_id']),
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name')
                    ));
                    $this->Session->write('FC_status', $status);
                }
                /**
                 * Kiem tra list profile cua task.
                 * Neu chua co lay tu du lieu ra.
                 * Neu co roi lay tu session ra
                 */
                $profiles = $this->Session->read('FC_profiles');
                if(empty($profiles)){
                    $profiles = $this->Profile->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('Profile.company_id' => $_POST['company_id']),
                        'fields' => array('Profile.id', 'Profile.name')
                    ));
                    $this->Session->write('FC_profiles', $profiles);
                }
                /**
                 * Kiem tra list priority cua task.
                 * Neu chua co lay tu du lieu ra.
                 * Neu co roi lay tu session ra
                 */
                $priorities = $this->Session->read('FC_priorities');
                if(empty($priorities)){
                    $priorities = $this->ProjectPriority->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('ProjectPriority.company_id' => $_POST['company_id']),
                        'fields' => array('ProjectPriority.id', 'ProjectPriority.priority')
                    ));
                    $this->Session->write('FC_priorities', $priorities);
                }
                /**
                 * Lay status open cua 1 cong ty
                 */
                $openStatus = $this->Session->read('FC_open_status');
                if(empty($openStatus)){
                    $openStatus = $this->ProjectStatus->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'name' => array('Ouvert', 'Open'),
                            'company_id' => $_POST['company_id']
                        ),
                        'fields' => array('id')
                    ));
                    $openStatus = !empty($openStatus) && !empty($openStatus['ProjectStatus']['id']) ? $openStatus['ProjectStatus']['id'] : 0;
                }
                /**
                 * Lay thong tin cua Activity task
                 */
                $tasks = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $_POST['task_id']),
                    'fields' => array('id', 'name', 'task_priority_id', 'task_status_id', 'profile_id', 'task_start_date', 'task_end_date', 'estimated', 'project_task_id', 'text_1', 'attachment', 'profile_id')
                ));
                $pTaskIds = !empty($tasks) && !empty($tasks['ActivityTask']['project_task_id']) ? $tasks['ActivityTask']['project_task_id'] : 0;

                $results['role'] = $this->employee_info['Role']['id'];

                if($_POST['type'] == 'pr'){
                    /**
                     * Lay thong tin cua project task
                     */
                    $tasks = $this->ProjectTask->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('ProjectTask.id' => $pTaskIds),
                        'fields' => array('id', 'task_title', 'project_id', 'task_priority_id', 'task_status_id', 'profile_id', 'task_start_date', 'task_end_date', 'estimated', 'text_1', 'attachment', 'profile_id')
                    ));
                    $tasks = !empty($tasks['ProjectTask']) ? $tasks['ProjectTask'] : array();
                    /*
                    * Check permission if user is PM
                    */
                    if( !empty($tasks) && $results['role'] == 3 ){
                        $results['can_modify'] = $this->canModifyProject($tasks['project_id']);
                    }
                } else {
                    $tasks = !empty($tasks['ActivityTask']) ? $tasks['ActivityTask'] : array();
                    $tasks['task_title'] = $tasks['name'];
                    $tasks['task_start_date'] = date('d-m-Y', $tasks['task_start_date']);
                    $tasks['task_end_date'] = date('d-m-Y', $tasks['task_end_date']);
                    $results['can_modify'] = 1;
                }
                /**
                 * Tinh consumed va in used cua task/employee/date
                 */
                $request = $this->ActivityRequest->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'task_id',
                        'SUM(CASE WHEN `status` = 2 THEN `value` ELSE 0 END) AS valid',
                        'SUM(CASE WHEN `status` = -1 OR `status` = 0 OR `status` = 1 THEN `value` ELSE 0 END) AS wait'
                    ),
                    'group' => array('task_id'),
                    'conditions' => array(
                        'task_id' => $_POST['task_id']
                    ))
                );
                $request = !empty($request[0]) ? $request[0] : array();
                $results['consume'] = !empty($request['valid']) ? $request['valid'] : 0;
                /**
                 * TASK
                 */
                $results['tasks']['id'] = !empty($tasks['id']) ? $tasks['id'] : '';
                $results['tasks']['name'] = !empty($tasks['task_title']) ? $tasks['task_title'] : '';
                $results['tasks']['workload'] = !empty($tasks['estimated']) ? $tasks['estimated'] : '';
                $results['tasks']['priority'] = !empty($tasks['task_priority_id']) ? $tasks['task_priority_id'] : '';
                $results['tasks']['status'] = !empty($tasks['task_status_id']) ? $tasks['task_status_id'] : $openStatus;
                $results['tasks']['profile'] = !empty($tasks['profile_id']) ? $tasks['profile_id'] : '';
                $results['tasks']['start'] = !empty($tasks['task_start_date']) ? date('d-m-Y', strtotime($tasks['task_start_date'])) : '';
                $results['tasks']['end'] = !empty($tasks['task_end_date']) ? date('d-m-Y', strtotime($tasks['task_end_date'])) : '';
                $results['tasks']['consumed'] = !empty($request['valid']) ? $request['valid'] : '';
                $results['tasks']['used'] = !empty($request['wait']) ? $request['wait'] : '';
                $results['tasks']['text_1'] = !empty($tasks['text_1']) ? $tasks['text_1'] : '';
                $results['tasks']['attachment'] = !empty($tasks['attachment']) ? $tasks['attachment'] : '';
                $results['tasks']['profile_id'] = !empty($tasks['profile_id']) ? $tasks['profile_id'] : '';
                /**
                 * Cac thu lien quan khac
                 */
                $results['status'] = $status;
                $results['profiles'] = $profiles;
                $results['priorities'] = $priorities;
            }
        }
        $results['forecast_modify'] = isset($this->companyConfigs['forecast_modify']) && !empty($this->companyConfigs['forecast_modify']) ?  true : false;
        $results['forecast_assigned_to'] = isset($this->companyConfigs['forecast_assigned_to']) && !empty($this->companyConfigs['forecast_assigned_to']) ?  true : false;
        $results['forecast_status'] = isset($this->companyConfigs['forecast_status']) && !empty($this->companyConfigs['forecast_status']) ?  true : false;
        $results['forecast_others_fields'] = isset($this->companyConfigs['forecast_others_fields']) && !empty($this->companyConfigs['forecast_others_fields']) ?  true : false;

        $results['diary_modify'] = isset($this->companyConfigs['diary_modify']) && !empty($this->companyConfigs['diary_modify']) ?  true : false;
        $results['diary_status'] = isset($this->companyConfigs['diary_status']) && !empty($this->companyConfigs['diary_status']) ?  true : false;
        $results['diary_others_fields'] = isset($this->companyConfigs['diary_others_fields']) && !empty($this->companyConfigs['diary_others_fields']) ?  true : false;
        echo json_encode($results);
        exit;
    }
    public function saveTask(){
        $result = array(
            'status' => false
        );
        if( !empty($this->data) ){
            extract($this->data);
            $name = trim($name);
            list($type, $containerId) = explode('-', $container);
            if( $type == 'pr' ){
                $this->loadModel('ProjectTask');
                $this->ProjectTask->recursive = -1;
                $task = $this->ProjectTask->read(null, $id);
                $check = $this->ProjectTask->find('count', array(
                    'conditions' => array(
                        'task_title' => $name,
                        'NOT' => array('ProjectTask.id' => $id),
                        'project_id' => $containerId,
                        'parent_id' => $task['ProjectTask']['parent_id'],
                        'project_planed_phase_id' => $task['ProjectTask']['project_planed_phase_id'],
                    )
                ));
                if( !$check ){
                    //ok, start saving
                    $this->ProjectTask->save(array(
                        'id' => $id,
                        'task_title' => $name,
                        'task_status_id' => $status,
                        'profile_id' => $profile_id
                    ));
                    $result['status'] = true;
                    $this->loadModel('Project');
                    $projectName = $this->Project->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Project.id' => $containerId),
                        'fields' => array('id', 'project_name')
                    ));
                    $projectName = !empty($projectName) ? $projectName['Project']['project_name'] : '';
                    $this->writeLog(array('id' => $id, 'task_title' => $name, 'task_status_id' => $status, 'profile_id' => $profile_id), $this->employee_info, sprintf('Update task `%s` `%s`', $this->data['name'], $projectName));
                } else {
                    $result['message'] = __('Task name existed', true);
                }
            } else {
                $this->loadModel('ActivityTask');
                $this->ActivityTask->recursive = -1;
                $task = $this->ActivityTask->read(null, $id);
                $check = $this->ActivityTask->find('count', array(
                    'conditions' => array(
                        'name' => $name,
                        'NOT' => array('ActivityTask.id' => $id),
                        'activity_id' => $containerId,
                        'parent_id' => $task['ActivityTask']['parent_id']
                    )
                ));
                if( !$check ){
                    //ok, start saving
                    $this->ActivityTask->save(array(
                        'id' => $id,
                        'name' => $name,
                        'task_status_id' => $status,
                        'profile_id' => $profile_id
                    ));
                    $result['status'] = true;
                    $this->loadModel('Activity');
                    $ActivityName = $this->Activity->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Activity.id' => $containerId),
                        'fields' => array('id', 'name')
                    ));
                    $ActivityName = !empty($ActivityName) ? $ActivityName['Activity']['name'] : '';
                    $this->writeLog(array('id' => $id, 'task_title' => $name, 'task_status_id' => $status, 'profile_id' => $profile_id), $this->employee_info, sprintf('Update activity task `%s` `%s`', $this->data['name'], $ActivityName));
                } else {
                    $result['message'] = __('Task name existed', true);
                }
            }
        }
        die(json_encode($result));
    }
    private function canModifyProject($projects){
        if( $this->employee_info['Role']['id'] == 2 )return true;
        if( $this->employee_info['Role']['id'] == 4 )return false;
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
    private function _updateEmpToTeamProjectOrActivity($type = null, $id = null, $profit_id = null, $employee_id = null){
        $this->loadModels('ProjectTeam', 'ProjectFunctionEmployeeRefer', 'ActivityProfitRefer');
        if($type == 'pr'){
            // lay cac team cua project
            $teams = $this->ProjectTeam->find('list', array(
                'recursive' => -1,
                'conditions' => array('ProjectTeam.project_id' => $id),
                'fields' => array('id', 'id')
            ));
            // lay pc va employee cua team thuoc project
            $listPcAndEmpOfTeams = $this->ProjectFunctionEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_team_id' => $teams),
                'fields' => array('id', 'employee_id', 'profit_center_id', 'project_team_id')
            ));
            $emps = !empty($listPcAndEmpOfTeams) ? array_unique(Set::classicExtract($listPcAndEmpOfTeams, '{n}.ProjectFunctionEmployeeRefer.employee_id')) : array();
            //$teamOfEmps = !empty($listPcAndEmpOfTeams) ? Set::combine($listPcAndEmpOfTeams, '{n}.ProjectFunctionEmployeeRefer.employee_id', '{n}.ProjectFunctionEmployeeRefer.project_team_id') : array();
            $teamOfPcs = !empty($listPcAndEmpOfTeams) ? Set::combine($listPcAndEmpOfTeams, '{n}.ProjectFunctionEmployeeRefer.profit_center_id', '{n}.ProjectFunctionEmployeeRefer.project_team_id') : array();
            // kiem tra xem employee da co trong team chua. neu chua thi them vao
            if(!in_array($employee_id, $emps)){
                if(!empty($teamOfPcs) && !empty($teamOfPcs[$profit_id])){
                    // da co team id, them employee vao team do
                    $saved = array(
                        'employee_id' => $employee_id,
                        'profit_center_id' => $profit_id,
                        'project_team_id' => $teamOfPcs[$profit_id]
                    );
                    $this->ProjectFunctionEmployeeRefer->create();
                    $this->ProjectFunctionEmployeeRefer->save($saved);
                } else {
                    // ne ko co team thi tao team cho project truoc
                    $this->ProjectTeam->create();
                    $sTeam = array(
                        'project_id' => $id
                    );
                    if($this->ProjectTeam->save($sTeam)){
                        $team_id = $this->ProjectTeam->id;
                        $saved = array(
                            'employee_id' => $employee_id,
                            'profit_center_id' => $profit_id,
                            'project_team_id' => $team_id
                        );
                        $this->ProjectFunctionEmployeeRefer->save($saved);
                    }
                }
            }
        } else {
            // lay danh sach cac profit center duoc phep thuc hien thay doi trong activity
            $activityProfits = $this->ActivityProfitRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $id),
                'fields' => array('profit_center_id', 'profit_center_id')
            ));
            $check = false;
            if(!empty($activityProfits)){
                if(!empty($activityProfits[$profit_id])){
                    $check = true;
                }
            }
            if($check == false){
                $this->ActivityProfitRefer->create();
                $saved = array(
                    'activity_id' => $id,
                    'profit_center_id' => $profit_id,
                    'type' => 0
                );
                $this->ActivityProfitRefer->save($saved);
            }
        }
    }
    /**
     * Kiem tra xem trong thoi gian start date - end date cua task.
     * Employee dang keo va employee dang chuyen toi c� nghi ngay nao ko
     */
    function checking_absence(){
        set_time_limit(0);
        $this->layout = false;
        $this->loadModels('ProjectTask', 'ActivityTask', 'AbsenceRequest', 'ProjectTaskEmployeeRefer', 'ActivityTaskEmployeeRefer', 'NctWorkload', 'Project', 'Activity', 'Employee');
        $results = array();
        $employ = $this->employee_info['Employee']['fullname'];
        $cId = $this->employee_info['Company']['id'];
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $cId),
            'fields' => array('id', 'fullname')
        ));
        if($_POST){
            if(isset($_POST['isNCT']) && $_POST['isNCT'] == 1){
                /**
                 * Lay thong tin cua Activity task
                 */
                $tasks = $this->ActivityTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $_POST['task_id']),
                    'fields' => array('id', 'project_task_id', 'activity_id')
                ));
                $results['activity_id'] = !empty($tasks) && !empty($tasks['ActivityTask']['activity_id']) ? $tasks['ActivityTask']['activity_id'] : 0;
                $pTaskIds = !empty($tasks) && !empty($tasks['ActivityTask']['project_task_id']) ? $tasks['ActivityTask']['project_task_id'] : 0;
                /**
                 * Lay thong tin cua project task
                 */
                $pTasks = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTask.id' => $pTaskIds),
                    'fields' => array('id', 'project_id', 'task_title')
                ));
                $taskName = !empty($pTasks) && !empty($pTasks['ProjectTask']['task_title']) ? $pTasks['ProjectTask']['task_title'] : '';
                $results['project_id'] = !empty($pTasks) && !empty($pTasks['ProjectTask']['project_id']) ? $pTasks['ProjectTask']['project_id'] : 0;
                $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $results['project_id']),
                    'fields' => array('id', 'project_name')
                ));
                $projectName = !empty($projectName['Project']['project_name']) ? $projectName['Project']['project_name'] : '';
                /**
                 * Lay thong tin cac NCT task duoc keo di
                 */
                $isProfitCenter = ($_POST['from_emp'] == 'tNotAffec') ? 1 : 0;
                $_POST['from_emp'] = ($_POST['from_emp'] == 'tNotAffec') ? $_POST['profit_id'] : $_POST['from_emp'];
                $conditions = array(
                    'reference_id' => $_POST['from_emp'],
                    'is_profit_center' => $isProfitCenter
                );
                if($_POST['type'] == 'ac'){
                    $conditions['activity_task_id'] = $_POST['task_id'];
                    $this->_updateEmpToTeamProjectOrActivity($_POST['type'], $tasks['ActivityTask']['activity_id'], $_POST['profit_id'], $_POST['to_emp']);
                    /**
                     * Lay assign task
                     */
                    $assigns = $this->ActivityTaskEmployeeRefer->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'reference_id' => $_POST['from_emp'],
                            'is_profit_center' => $isProfitCenter,
                            'activity_task_id' => $tasks['ActivityTask']['id']
                        ),
                        'fields' => array('id')
                    ));
                    if(!empty($assigns) && !empty($assigns['ActivityTaskEmployeeRefer']['id'])){
                        $this->ActivityTaskEmployeeRefer->id = $assigns['ActivityTaskEmployeeRefer']['id'];
                        $saveAssign = array(
                            'reference_id' => $_POST['to_emp'],
                            'is_profit_center' => 0
                        );
                        $this->ActivityTaskEmployeeRefer->save($saveAssign);
                    }
                } else {
                    $conditions['project_task_id'] = $pTaskIds;
                    $this->_updateEmpToTeamProjectOrActivity($_POST['type'], $pTasks['ProjectTask']['project_id'], $_POST['profit_id'], $_POST['to_emp']);
                    /**
                     * Lay assign task
                     */
                    $assigns = $this->ProjectTaskEmployeeRefer->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'reference_id' => $_POST['from_emp'],
                            'is_profit_center' => $isProfitCenter,
                            'project_task_id' => $pTasks['ProjectTask']['id']
                        ),
                        'fields' => array('id')
                    ));
                    if(!empty($assigns) && !empty($assigns['ProjectTaskEmployeeRefer']['id'])){
                        $this->ProjectTaskEmployeeRefer->id = $assigns['ProjectTaskEmployeeRefer']['id'];
                        $saveAssign = array(
                            'reference_id' => $_POST['to_emp'],
                            'is_profit_center' => 0
                        );
                        $this->ProjectTaskEmployeeRefer->save($saveAssign);
                    }
                }
                $nctTasks = $this->NctWorkload->find('all', array(
                    'recursive' => -1,
                    'conditions' => $conditions
                ));
                if(!empty($nctTasks)){
                    foreach($nctTasks as $nctTask){
                        $dx = $nctTask['NctWorkload'];
                        $date = strtotime($dx['task_date']);
                        $results['data'][$date] = $dx['estimated'];
                        $this->NctWorkload->id = $dx['id'];
                        $saved = array(
                            'reference_id' => $_POST['to_emp'],
                            'is_profit_center' => 0
                        );
                        $this->NctWorkload->save($saved);
                    }
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Forecast', true) . ' ' .  sprintf('Update task `%s` `%s`', $taskName, $projectName);
                    $message .= ' ' . __('Affected to', true). ' ' . $listEmployee[$_POST['to_emp']];
                    if($_POST['from_emp'] != 'tNotAffec'){
                        $message .= ' ' . __('Remove', true). ' ' . $listEmployee[$_POST['from_emp']];
                    }
                    $this->writeLog($_POST, $this->employee_info, $message);
                }
            } else {
                /**
                 * Lay thong tin cua Activity task
                 */
                $tasks = $this->ActivityTask->find('first', array(
                    //'recursive' => -1,
                    'conditions' => array('ActivityTask.id' => $_POST['task_id']),
                    'fields' => array('id', 'task_start_date', 'task_end_date', 'project_task_id', 'activity_id', 'estimated', 'name')
                ));
                $results['activity_id'] = !empty($tasks) && !empty($tasks['ActivityTask']['activity_id']) ? $tasks['ActivityTask']['activity_id'] : 0;
                $pTaskIds = !empty($tasks) && !empty($tasks['ActivityTask']['project_task_id']) ? $tasks['ActivityTask']['project_task_id'] : 0;
                $start = !empty($tasks) && !empty($tasks['ActivityTask']['task_start_date']) ? $tasks['ActivityTask']['task_start_date'] : 0;
                $end = !empty($tasks) && !empty($tasks['ActivityTask']['task_end_date']) ? $tasks['ActivityTask']['task_end_date'] : 0;
                if($_POST['from_emp'] == 'tNotAffec'){
                    $refers[0][$_POST['from_emp']] = !empty($tasks) && !empty($tasks['ActivityTask']['estimated']) ? $tasks['ActivityTask']['estimated'] : 0;
                    $saved = array(
                        'activity_task_id' => !empty($tasks) && !empty($tasks['ActivityTask']['id']) ? $tasks['ActivityTask']['id'] : 0,
                        'reference_id' => $_POST['to_emp'],
                        'is_profit_center' => 0,
                        'estimated' => !empty($tasks) && !empty($tasks['ActivityTask']['estimated']) ? $tasks['ActivityTask']['estimated'] : 0
                    );
                    if(!empty($_POST['type']) && $_POST['type'] == 'ac'){
                        if($_POST['isPc']){
                            $_POST['from_emp'] = $_POST['profit_id'];
                            $refers = !empty($tasks) && !empty($tasks['ActivityTaskEmployeeRefer']) ? Set::combine($tasks['ActivityTaskEmployeeRefer'], '{n}.reference_id', '{n}.estimated', '{n}.is_profit_center') : array();
                            $listIds = !empty($tasks) && !empty($tasks['ActivityTaskEmployeeRefer']) ? Set::combine($tasks['ActivityTaskEmployeeRefer'], '{n}.reference_id', '{n}.id', '{n}.is_profit_center') : array();
                            if(!empty($refers[0][$_POST['to_emp']])){
                                $workloadOfFromEmp = !empty($refers[1][$_POST['from_emp']]) ? $refers[1][$_POST['from_emp']] : 0;
                                $workloadOfToEmp = !empty($refers[0][$_POST['to_emp']]) ? $refers[0][$_POST['to_emp']] : 0;
                                $refers[0][$_POST['from_emp']] = $workloadOfFromEmp + $workloadOfToEmp;
                            } else {
                                $refers[0][$_POST['from_emp']] = !empty($refers[1][$_POST['from_emp']]) ? $refers[1][$_POST['from_emp']] : 0;
                            }
                            if(!empty($listIds) && !empty($listIds[1])){
                                $idReferOfFromEmp = !empty($listIds[1][$_POST['from_emp']]) ? $listIds[1][$_POST['from_emp']] : 0;
                                if($idReferOfFromEmp != 0){
                                    $est = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                                    $this->ActivityTaskEmployeeRefer->id = $idReferOfFromEmp;
                                    $this->ActivityTaskEmployeeRefer->save(array('reference_id' => $_POST['to_emp'], 'estimated' => $est, 'is_profit_center' => 0));
                                }
                                $idReferOfToEmp = !empty($listIds[0][$_POST['to_emp']]) ? $listIds[0][$_POST['to_emp']] : 0;
                                if($idReferOfToEmp != 0){
                                    $this->ActivityTaskEmployeeRefer->delete($idReferOfToEmp);
                                }
                            }
                        } else {
                            $this->ActivityTaskEmployeeRefer->create();
                            $this->ActivityTaskEmployeeRefer->save($saved);
                        }
                        // save team
                        $this->_updateEmpToTeamProjectOrActivity($_POST['type'], $tasks['ActivityTask']['activity_id'], $_POST['profit_id'], $_POST['to_emp']);
                        // write log
                        $taskName = !empty($tasks['ActivityTask']['name']) ? $tasks['ActivityTask']['name'] : '';
                        $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Forecast', true) . ' ' .  __('Update', true) . ' ' . $taskName;
                        $message .= ' ' . __('Affected to', true). ' ' . $listEmployee[$_POST['to_emp']];
                        if($_POST['from_emp'] != 'tNotAffec'){
                            $message .= ' ' . __('Remove', true). ' ' . $listEmployee[$_POST['from_emp']];
                        }
                        $this->writeLog($_POST, $this->employee_info, $message);
                    }
                } else {
                    $refers = !empty($tasks) && !empty($tasks['ActivityTaskEmployeeRefer']) ? Set::combine($tasks['ActivityTaskEmployeeRefer'], '{n}.reference_id', '{n}.estimated', '{n}.is_profit_center') : array();
                    $listIds = !empty($tasks) && !empty($tasks['ActivityTaskEmployeeRefer']) ? Set::combine($tasks['ActivityTaskEmployeeRefer'], '{n}.reference_id', '{n}.id', '{n}.is_profit_center') : array();
                    if(!empty($refers[0][$_POST['to_emp']])){
                        $workloadOfFromEmp = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                        $workloadOfToEmp = !empty($refers[0][$_POST['to_emp']]) ? $refers[0][$_POST['to_emp']] : 0;
                        $refers[0][$_POST['from_emp']] = $workloadOfFromEmp + $workloadOfToEmp;
                    } else {
                        $refers[0][$_POST['from_emp']] = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                    }
                    if(!empty($listIds) && !empty($listIds[0])){
                        $idReferOfFromEmp = !empty($listIds[0][$_POST['from_emp']]) ? $listIds[0][$_POST['from_emp']] : 0;
                        if($idReferOfFromEmp != 0){
                            $est = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                            $this->ActivityTaskEmployeeRefer->id = $idReferOfFromEmp;
                            $this->ActivityTaskEmployeeRefer->save(array('reference_id' => $_POST['to_emp'], 'estimated' => $est));
                        }
                        $idReferOfToEmp = !empty($listIds[0][$_POST['to_emp']]) ? $listIds[0][$_POST['to_emp']] : 0;
                        if($idReferOfToEmp != 0){
                            $this->ActivityTaskEmployeeRefer->delete($idReferOfToEmp);
                        }
                    }
                }
                if(!empty($_POST['type']) && $_POST['type'] == 'pr'){
                    /**
                     * Lay thong tin cua project task
                     */
                    $tasks = $this->ProjectTask->find('first', array(
                        //'recursive' => -1,
                        'conditions' => array('ProjectTask.id' => $pTaskIds),
                        'fields' => array('id', 'task_start_date', 'task_end_date', 'project_id', 'estimated', 'task_title')
                    ));
                    $results['project_id'] = !empty($tasks) && !empty($tasks['ProjectTask']['project_id']) ? $tasks['ProjectTask']['project_id'] : 0;
                    // write log
                    $taskName = !empty($tasks['ProjectTask']['task_title']) ? $tasks['ProjectTask']['task_title'] : '';
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  __('Update', true) . ' ' . $taskName;
                    $message .= ' ' . __('Affected to', true). ' ' . $listEmployee[$_POST['to_emp']];
                    if($_POST['from_emp'] != 'tNotAffec'){
                        $message .= ' ' . __('Remove', true). ' ' . $listEmployee[$_POST['from_emp']];
                    }
                    $this->writeLog($_POST, $this->employee_info, $message);

                    $start = !empty($tasks) && !empty($tasks['ProjectTask']['task_start_date']) && $tasks['ProjectTask']['task_start_date'] != '0000-00-00' ? strtotime($tasks['ProjectTask']['task_start_date']) : 0;
                    $end = !empty($tasks) && !empty($tasks['ProjectTask']['task_end_date']) && $tasks['ProjectTask']['task_end_date'] != '0000-00-00' ? strtotime($tasks['ProjectTask']['task_end_date']) : 0;
                    if($_POST['from_emp'] == 'tNotAffec'){
                        $refers[0][$_POST['from_emp']] = !empty($tasks) && !empty($tasks['ProjectTask']['estimated']) ? $tasks['ProjectTask']['estimated'] : 0;
                        $saved = array(
                            'project_task_id' => !empty($tasks) && !empty($tasks['ProjectTask']['id']) ? $tasks['ProjectTask']['id'] : 0,
                            'reference_id' => $_POST['to_emp'],
                            'is_profit_center' => 0,
                            'estimated' => !empty($tasks) && !empty($tasks['ProjectTask']['estimated']) ? $tasks['ProjectTask']['estimated'] : 0
                        );
                        if($_POST['isPc']){
                            $_POST['from_emp'] = $_POST['profit_id'];
                            $refers = !empty($tasks) && !empty($tasks['ProjectTaskEmployeeRefer']) ? Set::combine($tasks['ProjectTaskEmployeeRefer'], '{n}.reference_id', '{n}.estimated', '{n}.is_profit_center') : array();
                            $listIds = !empty($tasks) && !empty($tasks['ProjectTaskEmployeeRefer']) ? Set::combine($tasks['ProjectTaskEmployeeRefer'], '{n}.reference_id', '{n}.id', '{n}.is_profit_center') : array();
                            if(!empty($refers[0][$_POST['to_emp']])){
                                $workloadOfFromEmp = !empty($refers[1][$_POST['from_emp']]) ? $refers[1][$_POST['from_emp']] : 0;
                                $workloadOfToEmp = !empty($refers[0][$_POST['to_emp']]) ? $refers[0][$_POST['to_emp']] : 0;
                                $refers[0][$_POST['from_emp']] = $workloadOfFromEmp + $workloadOfToEmp;
                            } else {
                                $refers[0][$_POST['from_emp']] = !empty($refers[1][$_POST['from_emp']]) ? $refers[1][$_POST['from_emp']] : 0;
                            }
                            //profit_id
                            if(!empty($listIds) && !empty($listIds[1])){
                                $idReferOfFromEmp = !empty($listIds[1][$_POST['from_emp']]) ? $listIds[1][$_POST['from_emp']] : 0;
                                if($idReferOfFromEmp != 0){
                                    $est = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                                    $this->ProjectTaskEmployeeRefer->id = $idReferOfFromEmp;
                                    $this->ProjectTaskEmployeeRefer->save(array('reference_id' => $_POST['to_emp'], 'estimated' => $est, 'is_profit_center' => 0));
                                }
                                $idReferOfToEmp = !empty($listIds[0][$_POST['to_emp']]) ? $listIds[0][$_POST['to_emp']] : 0;
                                if($idReferOfToEmp != 0){
                                    $this->ProjectTaskEmployeeRefer->delete($idReferOfToEmp);
                                }
                            }
                        } else {
                            $this->ProjectTaskEmployeeRefer->create();
                            $this->ProjectTaskEmployeeRefer->save($saved);
                        }
                    } else {
                        $refers = !empty($tasks) && !empty($tasks['ProjectTaskEmployeeRefer']) ? Set::combine($tasks['ProjectTaskEmployeeRefer'], '{n}.reference_id', '{n}.estimated', '{n}.is_profit_center') : array();
                        $listIds = !empty($tasks) && !empty($tasks['ProjectTaskEmployeeRefer']) ? Set::combine($tasks['ProjectTaskEmployeeRefer'], '{n}.reference_id', '{n}.id', '{n}.is_profit_center') : array();
                        if(!empty($refers[0][$_POST['to_emp']])){
                            $workloadOfFromEmp = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                            $workloadOfToEmp = !empty($refers[0][$_POST['to_emp']]) ? $refers[0][$_POST['to_emp']] : 0;
                            $refers[0][$_POST['from_emp']] = $workloadOfFromEmp + $workloadOfToEmp;
                        } else {
                            $refers[0][$_POST['from_emp']] = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                        }
                        if(!empty($listIds) && !empty($listIds[0])){
                            $idReferOfFromEmp = !empty($listIds[0][$_POST['from_emp']]) ? $listIds[0][$_POST['from_emp']] : 0;
                            if($idReferOfFromEmp != 0){
                                $est = !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                                $this->ProjectTaskEmployeeRefer->id = $idReferOfFromEmp;
                                $this->ProjectTaskEmployeeRefer->save(array('reference_id' => $_POST['to_emp'], 'estimated' => $est));
                            }
                            $idReferOfToEmp = !empty($listIds[0][$_POST['to_emp']]) ? $listIds[0][$_POST['to_emp']] : 0;
                            if($idReferOfToEmp != 0){
                                $this->ProjectTaskEmployeeRefer->delete($idReferOfToEmp);
                            }
                        }
                    }
                    // save team
                    $this->_updateEmpToTeamProjectOrActivity($_POST['type'], $tasks['ProjectTask']['project_id'], $_POST['profit_id'], $_POST['to_emp']);
                }
                if($start != 0 && $end != 0){
                    $holidays = ClassRegistry::init('Holiday')->getOptions($start, $end, $_POST['company_id']);
                    foreach($holidays as $time => $holiday){
                        if(strtolower(date("l", $time)) == 'saturday' || strtolower(date("l", $time)) == 'sunday'){
                            unset($holidays[$time]);
                        } else {
                            $holidays[$time] = $time;
                        }
                    }
                    $workingDays = $this->_getWorkingDays(date('Y-m-d', $start), date('Y-m-d', $end));
                    $absences = $this->AbsenceRequest->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'employee_id' => $_POST['to_emp'],
                            'date BETWEEN ? AND ?'  => array($start, $end)
                        ),
                        'fields' => array(
                            'employee_id',
                            'response_am',
                            'response_pm',
                            'absence_am',
                            'absence_pm',
                            'date',
                            'SUM(CASE WHEN `response_am` = "validated" AND `response_pm` = "validated" THEN 1 WHEN `response_am` = "validated" AND `response_pm` <> "validated" THEN 0.5 WHEN `response_am` <> "validated" AND `response_pm` = "validated" THEN 0.5 ELSE 0 END) AS val',
                        ),
                        'group' => array('date')
                    ));
                    $absences = !empty($absences) ? Set::combine($absences, '{n}.AbsenceRequest.date', '{n}.0.val') : array();
                    $totalAbsences = !empty($absences) ? array_sum($absences) : 0;
                    $workingDays = ($workingDays - $totalAbsences - count($holidays)) * 2; // ngay lam viec * 2 = buoi lam viec
                    $workloadOfTasks = !empty($refers) && !empty($refers[0]) && !empty($refers[0][$_POST['from_emp']]) ? $refers[0][$_POST['from_emp']] : 0;
                    $WORKLOADBYDAY=$this->ActivityTask->caculateGlobal($workloadOfTasks, $workingDays, true);
                    while($start <= $end){
                        $wl = 0;
                        if((!empty($absences[$start]) && $absences[$start] == 1) || (!empty($holidays[$start]))){
                            $wl = 'ab-holiday';
                            //do nothing
                        } elseif(!empty($absences[$start]) && $absences[$start] == 0.5){
                            $wl = $WORKLOADBYDAY['original'] + $WORKLOADBYDAY['remainder'];
                        } else {
                            $wl = ($WORKLOADBYDAY['original']*2) + $WORKLOADBYDAY['remainder'];
                        }
                        if(!isset($results['data'][$start])){
                            $results['data'][$start] = 0;
                        }
                        $results['data'][$start] = $wl;
                        $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
                    }
                }
            }
        }
        echo json_encode($results);
        exit;
    }

   public function sendMailToEmployee(){
        if(!empty($_POST)){
            $data = $_POST['employee'];
            /**
             * Format du lieu truyen vao
             */
            $listSends = array();
            foreach($data as $date => $employe){
                $employe = explode('-', $employe);
                foreach($employe as $id){
                    if(!isset($listSends[$id][$date])){
                        $listSends[$id][$date] = 0;
                    }
                    $listSends[$id][$date] = $date;
                }
            }
            /**
             * Lay email employee
             */
            $this->loadModels('Employee');
            if(!empty($listSends)){
                $emailEmploys = $this->Employee->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('Employee.id' => array_keys($listSends)),
                    'fields' => array('id', 'email', 'profit_center_id')
                ));
                $content = !empty($data['content']) ? $data['content'] : '';
                $title = !empty($data['subject']) ? $data['subject'] : __('[Azuree] Reminder complete timesheet.', true);
                //Lưu lại vao MailNotSendYet
                $this->loadModels('MailNotSendYet');
                if(!empty($this->employee_info)){
                    $company_id = !empty($this->employee_info['CompanyEmployeeReference']['company_id']) ? $this->employee_info['CompanyEmployeeReference']['company_id'] : 0;
                    $employee_id = !empty($this->employee_info['CompanyEmployeeReference']['employee_id']) ? $this->employee_info['CompanyEmployeeReference']['employee_id'] : 0;
                    $last = $this->MailNotSendYet->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $company_id),
                        'fields' => array('id')
                    ));
                    $saved = array(
                        'employee_id' => $employee_id,
                        'company_id' => $company_id,
                        'subject' => $title,
                        'content' => $content
                    );
                    $this->MailNotSendYet->create();
                    if(!empty($last) && !empty($last['MailNotSendYet']['id'])){
                         $this->MailNotSendYet->id = $last['MailNotSendYet']['id'];
                    }
                    $this->MailNotSendYet->save($saved);
                }
                //send mail
                $emailEmploys = Set::combine($emailEmploys, '{n}.Employee.id', '{n}.Employee');
                $to = array();
                $count_eml = 1;
                foreach($listSends as $id => $times){
                    if(is_numeric($id)){
                        $to[] = !empty($emailEmploys[$id]['email']) ? $emailEmploys[$id]['email'] : '';
					}
                }
                $profits = '';
                $saveContent = $content;
                $this->set(compact('profits', 'times', 'saveContent'));
				/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug) */
				foreach( $to as $e){
					$this->_z0GSendEmail($e, null, null, $title, 'complete_timesheet');
				}
            }
        }
        echo json_encode('true');
        exit;
    }
	function getCommentRequest(){
        $result = array();
        if( !empty($this->data) && (($this->data['type']) != 'task') ){
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($this->data['employee_id'], false);
			if( $canRead ) {
				$this->loadModel('ActivityForecastComment');
				$date = $this->data['date'];
				$employee_id = $this->data['employee_id'];
				$company_id = $this->employee_info['Company']['id'];
				$commentRequest = $this->ActivityForecastComment->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'date' => $date,
						'employee_id' => $this->data['employee_id'],
						'company_id' => $company_id,
						'is_timesheet_msg' => ($this->data['type']=='week'),
					),
					// 'fields' => array('id', 'employee_id', 'comment', 'created')
				));
				$commentRequest = !empty($commentRequest) ? Set::combine($commentRequest, '{n}.ActivityForecastComment.id', '{n}.ActivityForecastComment') : array();
				$results = $commentRequest;
			}
        }elseif( $this->data['type'] == 'task' ){
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($this->data['employee_id'], false);
			if( $canRead ) {
				$this->loadModels('ProjectTaskTxts', 'ActivityTask');
				$activity_task_id = $this->data['task_id'];
				$project_task_id = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'fields' => array('project_task_id'),
					'conditions' => array(
						'id' => $activity_task_id
					)
				));
				$commentTask = $this->ProjectTaskTxts->find('all', array(
					'recursive' => -1,
					'fields' => array('id', 'employee_id', 'comment', 'created'),
					'conditions' => array(
						'project_task_id' => $project_task_id['ActivityTask']['project_task_id']
					)
				));
				$commentTask = !empty($commentTask) ? Set::combine($commentTask, '{n}.ProjectTaskTxts.id', '{n}.ProjectTaskTxts') : array();
				$results = $commentTask;
			}
		}
        die(json_encode($results));
    }
	function addCommentRequest(){
		$result = false;
		$message = __('Saved', true);
		$data = array();
        if( !empty($this->data) && (($this->data['type']) != 'task')  ){
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($this->data['employee_id'], false);
			if( $canWrite ) {
				$employee_id = $this->employee_info['Employee']['id'];
				$company_id = $this->employee_info['Company']['id'];
				$this->loadModel('ActivityForecastComment');
				$this->ActivityForecastComment->create();
				$result = $this->ActivityForecastComment->save(array(
					'update_by' => $employee_id,
					'employee_id' => $this->data['employee_id'],
					'company_id' => $company_id,
					'comment' => $this->data['comment'],
					'date' => $this->data['date'],
					'created' => time(),
					'updated' => time(),
					'is_timesheet_msg' => ($this->data['type']=='week'),
					
				));
				if( $result){
					$data = $this->ActivityForecastComment->find('first', array(
						'recursive' => -1,
						'conditions' =>  array('id' => $this->ActivityForecastComment->id),
					));
					$data = !empty( $data ) ? $data['ActivityForecastComment'] : array();
				}else{
					$message = __('Not saved', true);
				}
			}			
        }elseif($this->data['type'] == 'task'){
			list($canRead, $canWrite, $isPCManager) = $this->checkPermissionTimesheet($this->data['employee_id'], false);
			if( $canWrite ) {
				$employee_id = $this->employee_info['Employee']['id'];
				$company_id = $this->employee_info['Company']['id'];
				$this->loadModels('ProjectTaskTxts', 'ActivityTask');
				$activity_task_id = $this->data['task_id'];
				$project_task_id = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'fields' => array('project_task_id'),
					'conditions' => array(
						'id' => $activity_task_id
					)
				));
				$this->ProjectTaskTxts->create();
				$result = $this->ProjectTaskTxts->save(array(
					'project_task_id' => $project_task_id['ActivityTask']['project_task_id'],
					'employee_id' => $employee_id,
					'comment' => $this->data['comment'],
					'created' => date('Y-m-d h:m:s', time()),
				));
				if( $result){
					$data = $this->ProjectTaskTxts->find('first', array(
						'recursive' => -1,
						'conditions' =>  array('id' => $this->ProjectTaskTxts->id),
					));
					$data = !empty( $data ) ? $data['ProjectTaskTxts'] : array();
				}else{
					$message = __('Not saved', true);
				}
			}
		}
		$result = $result ? 'success' : 'failed';
        die(json_encode(compact('result', 'data', 'message')));
    }
	 function save_history_order_timesheet() {
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
           
            $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => $params,
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false)
            );
            echo json_encode(explode('-', $params));
        }
        exit();
    }
}
