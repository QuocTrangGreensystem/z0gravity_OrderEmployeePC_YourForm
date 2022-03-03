<?php
class WsController extends ZAuthAppController {
    // some components inherited from AppController (app_controller.php) so do not include them here
    public $components = array('ZAuth.ZAuth');

    public $uses = array('Employee');

    protected $roles = array(2, 3, 4);

    public function beforeFilter() {
        parent::beforeFilter();
        $this->ZAuth->authenticate = array(
            'userModel' => 'Employee',
            'fields' => array('username' => 'email', 'password' => 'password'),
            'authCode' => 'email.first_name.last_name'
        );
        $this->Auth->allow('*');
        // $this->ZAuth->allow('login');
        $this->parseRequest();

        // $this->ZAuth->addValidator('afterAuthValidation', array($this, 'validateIP'));
    }

    public function validateIP($auth){
        // check user
        $myIP = $_SERVER['REMOTE_ADDR'];
        $userIP = $auth->user('ws_ip');
        if( $auth->user() && (!$auth->user('ws_account') || $userIP != $myIP ) ){
            // logout
            //$auth->unauthorize();
            $auth->respond('ip_denied', null,'',0);
        }
    }

    /*
    *
    * auth methods
    *
    */

    public function login(){
        // post
        if( $this->RequestHandler->isPost() ){
            $data = $this->data;
            $data['password'] = Security::hash($data['password'], null, false);

            $loginDetail = array('Employee' => $data);

            if( !$this->ZAuth->user() && $this->Auth->login($loginDetail) ){
                // get user info
                $user = $this->Session->read('Auth.Employee');
                $employee_info = $this->Employee->CompanyEmployeeReference->find('first', array(
                    'conditions' => array(
                        'employee_id' => $user['id']
                    )
                ));
                $myColor = ClassRegistry::init('Color')->find('first',array(
                    'recursive' => -1,
                    'conditions' => array(
						'company_id' => $user['company_id'],
                    ),
                ));
				$is_new_design = isset( $employee_info['Color']['is_new_design'] ) ? $employee_info['Color']['is_new_design'] : null;
				if( !empty($myColor) ) $employee_info['Color'] = $myColor['Color'];
				if( isset($is_new_design )) $employee_info['Color']['is_new_design'] = $is_new_design;
                $this->Session->write('Auth.employee_info', $employee_info);

                // logout Auth
                $this->Auth->logout();

                // validate company and user role
                if( ($this->ZAuth->accessToken['company_id'] && $user['company_id'] != $this->ZAuth->accessToken['company_id']) || !in_array($employee_info['Role']['id'], $this->roles) ){
                    $this->unauthorized();
                }
				
                // validate Role
                // if( !$user['ws_account'] ){
					// $this->ZAuth->unauthorize();
					// $this->login_failed();
				// }
				
                // validate IP
				if( !empty($user['ws_ip'])){
					if( isset( $_SERVER['HTTP_X_FORWARDED_FOR']) ){
						if( !in_array( $user['ws_ip'], explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ){
							$this->ZAuth->respond('ip_denied', null,'',0);
						}
					}else if($user['ws_ip'] != $_SERVER['REMOTE_ADDR'] ){
						$this->ZAuth->respond('ip_denied', null,'',0);
					}
				}
                // register auth
                $this->ZAuth->authorize($user);
                // output result
                $this->ZAuth->respond('login_success', array(
                    'user' => $user,
                    'auth_code' => $this->ZAuth->user('auth_code')
                ));
            }
        }
        $this->login_failed();
    }

    public function logout(){
        $this->ZAuth->unauthorize();
        $this->ZAuth->respond('logout_success');
    }

    /*
    *
    * Main methods
    *
     */

    public function workload_by_month(){
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        $this->loadModels('Project', 'ProjectTask', 'NctWorkload', 'ProfitCenter', 'ProjectPhasePlan');
        $result = array();
        $user = $this->ZAuth->user();

        // parse params
        $start = @$this->params['url']['start'];
        $end = @$this->params['url']['end'];

        $start = date('Y-m-d', strtotime('01-' . $start));
        $end = date('Y-m-d', strtotime('01-' . $end));

        // get all projects whose status is "in progress"
        $listProjects = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                // 'Status.status' => 'IP',
                'Project.company_id' => $user['company_id']
            ),
            // 'joins' => array(
            // 	array(
            // 		'table' => 'project_statuses',
            // 		'alias' => 'Status',
            // 		'type' => 'inner',
            // 		'conditions' => array('Status.id = Project.project_status_id')
            // 	)
            // ),
            'fields' => array('id', 'project_name', 'project_code_1')
        ));
        $listProjects = Set::combine($listProjects, '{n}.Project.id', '{n}.Project');
        $pids = array_keys($listProjects);
        // phases, parts
        $this->ProjectPhasePlan->virtualFields['pp'] = 'CONCAT(Phase.name, "|", IF(Part.title IS NULL, "", Part.title))';
        $phases = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectPhasePlan.project_id' => $pids,
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
                    'type' => 'inner',
                    'conditions' => array('Phase.id = ProjectPhasePlan.project_planed_phase_id')
                ),
            ),
            'fields' => array(
                'ProjectPhasePlan.id', 'ProjectPhasePlan.pp'
            ),
            'order' => array('ProjectPhasePlan.project_id' => 'ASC', 'ProjectPhasePlan.weight' => 'ASC')
        ));

        $f = $phases;

        // get pcs, employees
        $pcs = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $user['company_id']
            ),
            'fields' => array('id', 'name')
        ));
        $aresources = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $user['company_id']
            ),
            'fields' => array('id', 'fullname', 'code_id')
        ));
        $resources = Set::combine($aresources, '{n}.Employee.id', '{n}.Employee');
        // get all ncts between start, end and their workloads
        $nctWorkloads = $this->NctWorkload->find('all', array(
            'conditions' => array(
                'task_date BETWEEN ? AND ?' => array($start, $end),
                'project_id' => $pids,
                'is_nct' => 1,
                // 'LEFT(group_date, 1)' => '2',
                // optimize
                // 'NctWorkload.estimated > 0'
            ),
            // use joins to sort
            'joins' => array(
                array(
                    'table' => 'project_tasks',
                    'alias' => 'Task',
                    'conditions' => array('Task.id = NctWorkload.project_task_id')
                )
            ),
            'fields' => array(
                'Task.*', 'NctWorkload.estimated', 'is_profit_center', 'reference_id', 'task_date'
            ),
            'order' => array('Task.weight' => 'ASC')
        ));
        // sort here
        foreach ($nctWorkloads as $wl) {
            $plan = $wl['Task']['project_planed_phase_id'];
            $phase = isset($phases[$plan]) ? explode('|', $phases[$plan]) : array();
            if( !$phase )continue;
            $time = explode('-', $wl['NctWorkload']['task_date']);
            $data = array(
                'project_id' => $wl['Task']['project_id'],
                'project_code_1' => $listProjects[$wl['Task']['project_id']]['project_code_1'],
                'project_name' => $listProjects[$wl['Task']['project_id']]['project_name'],
                'part' => isset($phase[1]) ? $phase[1] : '',
                'phase' => $phase[0],
                'Task' => $wl['Task'],
                'task_id' => $wl['Task']['id'],
                'task_name' => $wl['Task']['task_title'],
                'total_workload' => $wl['Task']['estimated'],
                'resource_workload' => $wl['NctWorkload']['estimated'],
                'resource' => $wl['NctWorkload']['is_profit_center'] ? $pcs[$wl['NctWorkload']['reference_id']] : $resources[$wl['NctWorkload']['reference_id']]['fullname'],
                'resource_type' => $wl['NctWorkload']['is_profit_center'] ? 'PC' : 'R',
                'resource_id' => $wl['NctWorkload']['reference_id'],
                'resource_code_id' => $wl['NctWorkload']['is_profit_center'] ? null : $resources[$wl['NctWorkload']['reference_id']]['code_id'],
                'month' => $time[1],
                'year' => $time[0]
            );
            if( !is_array($f[$plan]) ){
                $f[$plan] = array();
            }
            $f[$plan][] = $data;
        }
        // now filter
        foreach($f as $data){
            if( is_array($data) ){
                $result = array_merge($result, $data);
            }
        }
        $this->ZAuth->respond('success', $result);
    }
	
	/*
	* Function Consume 
	* Created by Huynh 22-11-2018
	*/
	public function consumed(){
		 /* Prepare params */
		$user = $this->ZAuth->user();
		$data = array(
			'startdate' => '',			
			'enddate' => '',
			'team' => 'R_RESOURCE',
			'resource' => '',
			'day_of' => 0,
			'merge' => 0,
			'display' => 0,
		);
		
		foreach ($data as $key => $value){
			$data[$key] = isset($this->data[$key] ) ? $this->data[$key]  : $data[$key] ;
		}
		$result = array();
		$company_id = $user['company_id'];
		$this->loadModel('Companie');
		$company_name = $this->Companie->find('first', array(
			'conditions' => array(
				'id' => $company_id
			),
			'fields' => array('company_name'),
		));
		$company_name = isset( $company_name['Companie']['company_name'] ) ? $company_name['Companie']['company_name'] : '';
		$employeeLoginName = $employeeLoginId = $user['id'];
		if ( empty($data['startdate']) || empty($data['enddate']) ){
			$this->ZAuth->respond('failed', $result, 'startdate or enddate was not provided', 1);
		}
		$listEmployees = array();
		$this->loadModels('ProfitCenter', 'Employee');
		if ( $data['team'] != 'R_RESOURCE' && $data['team'] != 'r_resource' &&  !empty($data['team']) ) {
			/* Phần này viết thêm do thay đổi yêu cầu */
			$list_pc_sap = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						'analytical' => $data['team'],
					),
					'company_id' => $company_id,
				),
				'fields' => array('id'),
			));
			/* End Phần này viết thêm do thay đổi yêu cầu */
			// get list resource by ProfitCenter ID
			if( empty ( $list_pc_sap) ) {
				$this->ZAuth->respond('failed', $result, 'ID of the team doesn’t exist', 3);
			}else{
				$listPC = $this->ProfitCenter->find('list',array(
					'recursive' => -1,
					'conditions' => array(
						'OR' => array(
							// 'id' => $data['team'],
							// 'parent_id' => $data['team']
							'id' => $list_pc_sap,
							'parent_id' => $list_pc_sap
						),
						'company_id' => $company_id,
					),
					'fields' => array('id'),
				));
				$listEmployees = $this->Employee->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'Employee.profit_center_id' => $listPC, 
						'company_id' => $company_id,
					),
					'fields' => array('Employee.id'),
				));
			}
			if( empty($listEmployees) ){
				$this->ZAuth->respond('failed', $result, 'Empty list employee', 3);
			}
		}
		if (  ($data['team'] == 'R_RESOURCE' || $data['team'] == 'r_resource' || empty($data['team']) ) &&  !empty($data['resource']) ){
			/* Phần này viết thêm do thay đổi yêu cầu */
			$listEmployees = $this->Employee->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Employee.code_id' => $data['resource'], 
					'company_id' => $company_id,
				),
				'fields' => array('Employee.id'),
			));
			/* End Phần này viết thêm do thay đổi yêu cầu */
			
			// $listEmployees = $this->Employee->find('list', array(
				// 'recursive' => -1,
				// 'conditions' => array(
					// 'Employee.id' => $data['resource'], 
					// 'company_id' => $company_id,
				// ),
				// 'fields' => array('Employee.id'),
			// ));
		}
		if( empty($listEmployees) ){
			$this->ZAuth->respond('failed', $result, 'Empty list employee', 4);
		}
		$startDate = strtotime($data['startdate']);
		$endDate = strtotime($data['enddate']);
		$dayOff = in_array( strtolower($data['day_of']), array('yes', 'y', 1)) ? 1 : 0;
		$merge = in_array( strtolower($data['merge']), array('yes', 'y', 1)) ? 'yes' : 'no';
		$display = in_array( strtolower($data['display']), array('yes', 'y', 1)) ? 'yes' : 'no';
		
		// check endDate <= startDate + 42 day
		if( ($endDate < $startDate) || (($endDate - $startDate)-(42*24*3600) > 0) ){
			$this->ZAuth->respond('failed', $result, 'The time allowed is 42 days', 2);
		}
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        $this->loadModels('Project', 'AbsenceRequest', 'Absence', 'ActivityRequest', 'ActivityTask', 'ProjectPhasePlan', 'ProjectTask', 'Activity', 'ActivityRequestConfirm', 'Family', 'ProjectPhasePlan', 'ProjectEmployeeProfitFunctionRefer', 'ActivityExport', 'TmpModuleActivityExport', 'ProjectPhase', 'ActivityForecastComment');	
		$employeeLoginExportName = Inflector::slug($user['fullname']);
		$totalRecord = 0;
		/* End Prepare params */
		
		
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
			'fields' => array('id', 'first_name', 'last_name', 'code_id', 'identifiant', 'id3', 'id4', 'id5', 'id6'),
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
		$profitCenters = $this->ProfitCenter->find('list', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $company_id),
			'fields' => array('id', 'name')
		));
		/**
		 * Lay holiday
		 */
		$getHolidays = ClassRegistry::init('Holiday')->getOptions($startDate, $endDate, $company_id);
		// debug($getHolidays); exit;
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
		// debug($commentRequest);
		// exit;
		
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
		// debug( $weekComments); exit;
		
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
			'fields' => array('id', 'name', 'print', 'code1', 'code2', 'code3')
		));
		$absences = !empty($absences) ? Set::combine($absences, '{n}.Absence.id', '{n}.Absence') : array();
		/**
		 * Lay activity request
		 */
		$activityRequests = $this->ActivityRequest->find("all", array(
			'recursive' => -1,
			"conditions" => array(
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'employee_id' => $listEmployees,
				'status' => $dayOff ? array(-1, 0, 1, 2) : 2
				//'NOT' => array('value' => 0)
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
		 * Lay actvity theo task
		 */
		$activityTasks = $this->ActivityTask->find('all', array(
			'recursive' => -1,
			'conditions' => array('ActivityTask.id' => $listTaskIds),
			'fields' => array('id', 'name', 'project_task_id', 'activity_id')
		));
		
		$activityIdTwos = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.activity_id') : array();
		$projectTaskIds = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.project_task_id') : array();
		$ATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.project_task_id') : array();
		$ATaskOfActivity = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
		$ATaskNameOfActivity = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.name') : array();
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
			'fields' => array('id','project_name', 'long_project_name', 'project_code_1', 'project_code_2'),
		));
		$projects = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project') : array(); 
		/**
		 * Lay Phase cua cac task
		 */
		$phasePlans = $this->ProjectPhasePlan->find('all', array(
			'recursive' => -1,
			'conditions' => array('ProjectPhasePlan.id' => $projectTasksIds),
			'fields' => array('id', 'project_planed_phase_id','ref1', 'ref2', 'ref3', 'ref4')
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
			'fields' => array('id', 'code1', 'code2', 'code3', 'code4', 'code5', 'code6', 'family_id', 'subfamily_id', 'project', 'name')
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
		$sum = $sum_absence = $sum_activity = 0;
		foreach ($employees as $employee) {
			$employID = $employee['Employee']['id'];
			$pcId = !empty($employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : '';
			/**
			 * Writing holiday
			 */
			if(!empty($holidays) && $dayOff){
				foreach($holidays as $date => $value){
					$no++;
					$value = !empty($value) ? $value : 0;
					$sum_absence += $value;
					$comment = (!empty($comments_request) && !empty($comments_request[$date])) ? $comments_request[$date] : array();
					$wcomment = array();
					foreach ($weekComments as $weekComment){
						if( $weekComment['start_day'] <= $date &&  $weekComment['end_day'] >= $date ){
							$wcomment[] = $weekComment['comment'];
						}
					}
					$save[] = array(
						// 'debug' => 'holiday',
						'no' => $no,
						'employee_id' =>$employID ,
						'first_name' => !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '',
						'last_name' => !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '',
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
						'date_activity_absence' => !empty($date) ? date('d-m-Y', $date) : '',
						'validation_date' => '',
						'extraction_date' => date('d-m-Y', time()),
						'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
						'date_export' => date('d-m-Y', time()),
						'company_id' => $company_id,
						'company_name' => $company_name,
						'project_code' => 'ABSENCE',
						'absence' => !empty($date) ? date('d F', $date) : '',
						'message_of_the_week' => $wcomment,
						'message_stored_in_timesheet' => $comment,
						'project_id' => isset( $project['id'] ) ? $project['id'] : '',
					);
				}
			}
			/**
			 * Export Absence
			 */
			if(!empty($absencesForEmployees[$employID]) && $dayOff){
				foreach($absencesForEmployees[$employID] as $date => $value){
					$absenceIdOfEmploy = !empty($listAbsenceCodes[$employID][$date]) ? $listAbsenceCodes[$employID][$date] : '';
					$no++;
					$value = !empty($value) ? array_shift($value) : 0;
					$sum_absence += $value;
					$comment = (!empty($comments_request) && !empty($comments_request[$date])) ? $comments_request[$date] : array();
					$wcomment = array();
					foreach ($weekComments as $weekComment){
						if( $weekComment['start_day'] <= $date &&  $weekComment['end_day'] >= $date ){
							$wcomment[] = $weekComment['comment'];
						}
					}
					$save[] = array(
						// 'debug' => 'Absence',
						'no' => $no,
						'employee_id' =>$employID ,
						'first_name' => !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '',
						'last_name' => !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '',
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
						'quantity' => $value,
						'date_activity_absence' => !empty($date) ? date('d-m-Y', $date) : '',
						'validation_date' => !empty($absencesDates[$employee['Employee']['id']][$date]) ? date('d-m-Y',$absencesDates[$employee['Employee']['id']][$date]) : '',
						'extraction_date' => date('d-m-Y'),
						'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
						'date_export' => date('d-m-Y'),
						'company_id' => $company_id,
						'company_name' => $company_name,
						'project_code' => 'ABSENCE',
						'absence' => !empty($absences[$absenceIdOfEmploy]['print']) ? $absences[$absenceIdOfEmploy]['print'] : '',
						'message_of_the_week' => $wcomment,
						'message_stored_in_timesheet' => $comment,
						'project_id' => isset( $project['id'] ) ? $project['id'] : '',
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
					$comment = (!empty($comments_request) && !empty($comments_request[$value['date']])) ? $comments_request[$value['date']] : array();
					$wcomment = array();
					foreach ($weekComments as $weekComment){
						if( $weekComment['start_day'] <= $value['date'] &&  $weekComment['end_day'] >= $value['date'] ){
							$wcomment[] = $weekComment['comment'];
						}
					}
					$PTaskId = !empty($ATaskLinkPTasks[$value['task_id']]) ? $ATaskLinkPTasks[$value['task_id']] : '';
					$PhasePlanId = !empty($projectTasks[$PTaskId]['id']) ? $projectTasks[$PTaskId]['id'] : '';
					$project_planed_phase_id = !empty($projectTasks[$PTaskId]['project_planed_phase_id']) ? $projectTasks[$PTaskId]['project_planed_phase_id'] : '';
					$PTaskTitle = isset($projectTasks[$PTaskId]['task_title']) ? $projectTasks[$PTaskId]['task_title'] : '';
					$projectID = !empty($projectTasks[$PTaskId]['project_id'])? $projectTasks[$PTaskId]['project_id'] : '';
					$project = isset( $projects[$projectID]) ? $projects[$projectID] : '';
					// $project
					
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
					$valDate = '';
					if(!empty($value['status']) && $value['status'] == 2){
						$confirmForEmploy = !empty($listRequestConfirms[$employee['Employee']['id']]) ? $listRequestConfirms[$employee['Employee']['id']] : array();
						if(!empty($confirmForEmploy) && !empty($confirmForEmploy[$value['date']])){
							$valDate = $confirmForEmploy[$value['date']];
						}
					}
					$no++;
					$code1 = !empty($activities[$activityId]['code1']) ? $activities[$activityId]['code1'] : '';
					$code2 = !empty($activities[$activityId]['code2']) ? $activities[$activityId]['code2'] : '';
					$ref1 = !empty($phasePlans[$PhasePlanId]['ref1']) ? $phasePlans[$PhasePlanId]['ref1'] : '';
					$ref2 = !empty($phasePlans[$PhasePlanId]['ref2']) ? $phasePlans[$PhasePlanId]['ref2'] : '';
					$val = !empty($value['value']) ? $value['value'] : 0;

					$sum_activity += $val;
					$first = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
					$last = !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '';
					if( ((!empty($value['value']) && $value['value']!=0)&& $display == 'no') ||  $display == 'yes' ){
						$save[] = array(
							// 'debug' => 'activityRequests',
							'no' => $no,
							'employee_id' =>$employID ,
							'first_name' => !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '',
							'last_name' => !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '',
							'profit_center' => !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '',
							'id1' => !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '',
							'id2' => !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '',
							'id3' => !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '',
							'id4' => !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '',
							'id5' => !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '',
							'id6' => !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '',
							'family' => !empty($families[$familyId]) ? $families[$familyId] : '',
							'sub_family' => !empty($families[$subfamilyId]) ? $families[$subfamilyId] : '',
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
							'date_activity_absence' => !empty($value['date']) ? date('d-m-Y' , $value['date']) : '',
							'validation_date' => !empty($valDate) ? date('d-m-Y', $valDate) : '',
							'extraction_date' => date('d-m-Y'),
							'employee_export' => !empty($employeeLoginExportName) ? $employeeLoginExportName : '',
							'date_export' => date('d-m-Y'),
							'company_id' => $company_id,
							'company_name' => $company_name,
							'employee_id' => !empty($employee['Employee']['id']) ? $employee['Employee']['id'] : 0,
							'fullname' => $first . ' ' . $last,
							'activity_name' => !empty($activities[$activityId]['name']) ? $activities[$activityId]['name'] : '',
							'phase_name' => (!empty($planIDToPhase[$project_planed_phase_id]) && !empty($phaseName[$planIDToPhase[$project_planed_phase_id]])) ? $phaseName[$planIDToPhase[$project_planed_phase_id]] : '',
							'task_name' => $PTaskTitle,
							'message_of_the_week' => $wcomment,
							'message_stored_in_timesheet' => $comment,
							'project_code_1' => isset( $project['project_code_1'] ) ? $project['project_code_1'] : '',
							'project_code_2' => isset( $project['project_code_2'] ) ? $project['project_code_2'] : '',
							'project_id' => isset( $projectID ) ? $projectID : '',
							
						);
					}
				}	
				// exit;
			}
		}
		$totalRecord = $no;
		$sum = $sum_absence + $sum_activity;
		$save_sum['sum'] = $sum;
		if($dayOff){
			$save_sum['sum_absence'] = $sum_absence;
		}
		$save_sum['sum_activity'] = $sum_activity;
        $this->ZAuth->respond_multi('success', $save_sum, $save);
    }
	/* END Function Consume */
	
	/* Function Request absence */
	public function request_absence(){
		/* Validate data input */
		if( !$this->RequestHandler->isPost() ){
			$auth->respond('data_empty', null,'',0);
		}
		$user = $this->ZAuth->user();
		$result = array();
		$company_id = $user['company_id'];
		$this->loadModels('Absence', 'AbsenceRequest', 'ActivityRequestConfirm', 'ActivityForecast', 'AbsenceRequestConfirm');
		$data = array(
			'startdate' => '',			
			'enddate' => '',
			'id' => '',
			'type' => '',
			'd_start' => '',
			'd_end' => '',
			'v_r' => '',
			'validation_date' => '',
		);
		foreach ($data as $key => $value){
			$data[$key] = isset($this->data[$key] ) ? $this->data[$key] : $data[$key] ;
		}
		if ( empty($data['startdate']) || empty($data['enddate']) || empty($data['validation_date']) ){
			$this->ZAuth->respond('failed', $result, 'Input date was not provided', 4);
		}
		$startDate = strtotime($data['startdate']);
		$endDate = strtotime($data['enddate']);
		$request_date = time();
		if(!empty($data['validation_date'])){
			$request_date = strtotime($data['validation_date']);
		}
		if( $startDate >   $endDate ){
			$this->ZAuth->respond('failed', $result, 'startday after enddate', 4);
		}
		$employee = '';
		if( !empty($data['id'])){
			$employee = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'code_id' => $data['id'], 
					'company_id' => $company_id,
				),
				'fields' => array('id', 'code_id', 'first_name', 'last_name'),
			));
		}
		if( empty($employee) ){
			$this->ZAuth->respond('failed', $result, 'Employee not found', 1);
		}
		$employee_id =  $employee['Employee']['id'];
		$absence = $this->Absence->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'type' => $data['type'], 
				'company_id' => $company_id,
			),
			'fields' => array('id','name','type','print','begin','end_of_period','display','updated'),
		));
		if( empty($absence) ){
			$this->ZAuth->respond('failed', $result, 'Type of Absence not found', 2);
		}
		$type = $absence['Absence']['type'];
		$absence_id = $absence['Absence']['id'];
		
		$check_am_pm = 0;
		$am_pm = array('am', 'pm', 'd');
		if( in_array( strtolower($data['d_start']) , $am_pm) && in_array( strtolower($data['d_end']) , $am_pm) ){
			$check_am_pm = 1;
		}
		if( !$check_am_pm ){
			$this->ZAuth->respond('failed', $result, 'd_start or d_end not found', 5);
		}
		$d_start = strtolower($data['d_start']);
		$d_end = strtolower($data['d_end']);
		$check_v_r = 0;
		$v_r  = array('v', 'r');
		if( in_array( strtolower($data['v_r']) , $v_r)){
			$check_v_r = 1;
		}
		if( !$check_v_r ){
			$this->ZAuth->respond('failed', $result, 'v_r not found', 6);
		}
		$is_validation = (strtolower($data['v_r']) == 'v');
		$is_reject = (strtolower($data['v_r']) == 'r');
		unset($check_v_r, $check_am_pm, $am_pm, $v_r);
		/* END Validate data input */
		
		/* Check holiday */
		
		/**
         * Cac ngay lam viec trong tuan
         */
        $workdays = ClassRegistry::init('Workday')->getOptions($company_id);
        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->get2($company_id, $startDate, $endDate);
        foreach($holidays as $time => $h){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
		 /**
         * Lay ngay nghi phep cua employee
         */
		$requests = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            "conditions" => array('date BETWEEN ? AND ?' => array($startDate, $endDate), 'employee_id' => $employee_id)));
		$requests = $tmp_requests = Set::combine($requests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
		/**
         * Lay ngay nghi phep cua employee + holiday
         */
		foreach ($tmp_requests as $date => $request) {
			if (intval($request['absence_pm']) > 0 && intval($request['absence_am']) > 0) {}else{
				if($tmp_requests[$date]) unset($tmp_requests[$date]);
			}
		}
		/**
         * Lay ngay nghi phep cua employee + holiday
         */
		// $absenceOfEmployee = $tmp_requests + $holidays;
		$absenceOfEmployee = $holidays;

        /**
         * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
         */
        list($multiWeeks, $listWorkingDays) = $this->_workingDayFollowConfigAdmin($startDate, $endDate, $workdays);
		/**
         * get date working validated
         */
		$listWeekOfMonths = $this->_splitWeekOfMonth($startDate, $endDate);
		
		/**
         * Kiem tra so ngay nhap vao co it nhat 1 ngay lam viec
		 * 
         */
		foreach($absenceOfEmployee as $date => $value){
			unset($listWorkingDays[$date]);
		}
		if( empty($listWorkingDays) ){
			$this->ZAuth->respond('failed', $result, 'THERE IS AT LEAST A WORKING DAY', 7);
		}
		// Reject timeshet before reject/validated absence
		$this->_rejectTimeSheet($employee_id, $startDate, $endDate);
		// Get data request
		$data_request = array();
		if($is_validation){
			foreach($listWorkingDays as $date => $value){
				$apm = array('am', 'pm');
				if($date == $startDate && $d_start == 'pm') $apm = array('pm');
				else if ($date == $endDate && $d_end == 'am') $apm = array('am');
				if(empty($data_request[$date])) $data_request[$date] = array();
				$data_request[$date] = array(
					'date' => $date,
					'employee_id' => $employee_id,
				);
			}
		}
		if($is_validation && !empty($data_request)){
			$data_ok = array();
			 foreach ($data_request as $data) {
				if (!empty($data['date'])) {
					$data['employee_id'] = $employee_id;
					$last = $this->AbsenceRequest->find('first', array(
						'recursive' => -1, 
						'conditions' => array(
							'employee_id' => $employee_id,
							'date' => $data['date']
						),
						'fields' => array('id', 'absence_am', 'absence_pm', 'history'),
					));
					
					$history = array();
					if ($last) {
						$this->AbsenceRequest->id = $last['AbsenceRequest']['id'];
						
						if (!empty($last['AbsenceRequest']['history'])) {
							$history = unserialize($last['AbsenceRequest']['history']);
						}
						
						$except_absence = $this->AbsenceRequest->find('all', array(
							'recursive' => -1, 
							'conditions' => array(
								'employee_id' => $employee_id,
								'date' => $data['date'],
								'NOT' => array('id' => $last['AbsenceRequest']['id'])
							),
							'fields' => array('id'),
						));
						$except_absence = !empty($except_absence) ? Set::combine($except_absence, '{n}.AbsenceRequest.id', '{n}.AbsenceRequest.id') : array();
						if(!empty($except_absence)){
							$this->AbsenceRequest->deleteAll(array('id' => $except_absence));
						}
					} else {
						$this->AbsenceRequest->create();
					}
					$date = $data['date'];
					$apm = array('am', 'pm');
					if($date == $startDate && $d_start == 'pm') $apm = array('pm');
					else if ($date == $endDate && $d_end == 'am') $apm = array('am');
					foreach ($apm as $type) {
						$data['absence_' . $type] = $absence_id;
						$data['response_' . $type] = 'waiting';
						$data['created'] = $request_date;
						$history['rq_' . $type] = date('d-m-Y H:i');
					}
					$data['history'] = $history;
					$this->AbsenceRequest->save(array_merge(
											$data, array('history' => serialize($history))));
					
				}
			}
		}
		
		/*
			Get data request sau khi thuc hien send request
		*/
		$requests = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            "conditions" => array('date BETWEEN ? AND ?' => array($startDate, $endDate), 'employee_id' => $employee_id)));
		$requests = Set::combine($requests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
		/**
         * Reject or validated timesheet
         */
		 $dataView = array();
		 foreach ($listWorkingDays as $day => $time) {
			$default = array(
				'date' => $time,
				'absence_am' => 0,
				'absence_pm' => 0,
				'response_am' => 0,
				'response_pm' => 0,
				'employee_id' => $employee_id
			);
			if (isset($requests[$time])) {
				unset($requests[$time]['date'], $requests[$time]['employee_id']);
				$default = array_merge($default, array_filter($requests[$time]));
				if (!empty($default['history'])) {
					$default['history'] = unserialize($default['history']);
				}
			}
			$_dataView[$day] = $default;
		}
		$dataView[] = $_dataView;
		
		
		$response = '';
		if($is_validation) $response = 'validated';
		if($is_reject) $response = 'rejetion';
		$last_save = array();
		$message = '';
		if($response){
			foreach ($requests as $date => $request) {
				$checkDate = date('W-Y', $date);
				$history = array();
				$this->AbsenceRequest->id = $request['id'];
				if (!empty($request['history'])) {
					$history = unserialize($request['history']);
				}
				$save = array();
				$apm = array('am', 'pm');
				if($date == $startDate && $d_start == 'pm') $apm =array('pm');
				else if ($date == $endDate && $d_end == 'am') $apm =array('am');
				foreach ($apm as $type) {
					if (intval($request['absence_' . $type]) > 0 && $request['response_' . $type] != $response) {
						$save['response_' . $type] = $response;
						if ($response == 'rejetion') {
							$save['absence_' . $type] = 0;
						} elseif ($response == 'validated') {
							$this->ActivityForecast->updateAll(array('activity_' . $type => 0), array(
								'employee_id' => $employee_id, 'date' => $date, 'NOT' => array('activity_' . $type => 0)));

						}
						$history[($response === 'validated' ? 'rv_' : 'rj_') . $type] = date('d-m-Y H:i');
					}
				}
				$save && $save['history'] = serialize($history);
				// debug($save); 
				if ($save && $this->AbsenceRequest->save($save)) {
					if($response == 'rejetion') $message = 'ABSENCE CANCELED';
					if($response == 'validated') $message = 'ABSENCE STORED';
					$this->AbsenceRequestConfirm->deleteAll(array('employee_id' => $employee_id, 'start' => $startDate, 'end' => $endDate), false, false);
					$save['date'] = date('l d M', $date);
					$last_save[] = $save;
				}
			}
			$this->ZAuth->respond('suscess', $last_save, $message, 0);
		}
		ini_set('memory_limit', '1024M');
        set_time_limit(0);
	}
	/* End Function Request absence */
	
	/**
     * Lay cac ngay lam viec theo config working day o admin
	 * Copy từ absence_requests_controller
     */
    private function _workingDayFollowConfigAdmin($start = null, $end = null, $workdayAdmins = null, $listWeeks = array()){
        $weeks = $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $day = strtolower(date('l', $start));
                if($workdayAdmins[$day] != 0){
                    if(!empty($listWeeks)){
                        $_week = date('W-Y', $start);
                        if(!empty($listWeeks[$_week])){
                            $results[$start] = $start;
                            if(!isset($weeks[$_week][$start])){
                                $weeks[$_week][$start] = 0;
                            }
                            $weeks[$_week][$start] = $start;
                        }
                    } else {
                        $results[$start] = $start;
                    }
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return array($weeks, $results);
    }

    public function check_staffing(){
        if( $this->RequestHandler->isPost() ){
            $data = $this->data;
            $rebuild = $data['rebuild'];
            unset($data['rebuild']);
			$this->loadModels('Project', 'ProjectTask', 'NctWorkload', 'ProfitCenter', 'ProjectPhasePlan', 'ActivityTask' , 'ActivityRequest', 'TmpStaffingSystem', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter', 'ActionLog');
			$user = $this->ZAuth->user();
			$company = $user['company_id'];
			$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user['id'])));
			$employee_all_info = $employee_all_info[0];
			$employee_all_info["Employee"]["is_sas"] = 0;
			$this->employee_info = $employee_all_info;
			$this->Session->write('Auth.employee_info', $employee_all_info);
			$projects = $this->Project->find('all', array(
				'recursive'  => -1,
				'conditions' => array(
					'company_id' => $company,
					'category'   => array(1, 3)
				),
				'fields' => array('id','project_name','activity_id', 'rebuild_staffing'),
				'order' => array('project_name')
			));
			$rebuildStaffings = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.rebuild_staffing') : array();
			$activitiesLinked = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.linked') : array();
			$projects = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.project_name') : array();
			$listEmploy = $this->Employee->find('list', array(
				'recursive'  => -1,
				'conditions' => array('company_id' => $company),
				'fields'     => array('id', 'fullname')
			));
			$listProfitCenter = $this->ProfitCenter->find('list', array(
				'recursive'  => -1,
				'conditions' => array('company_id' => $company),
				'fields'     => array('id', 'name')
			));
			$projectListIds = array_keys($projects);
			$projectTasks = $this->ProjectTask->find('all', array(
				'recursive'  => -1,
				'conditions' => array(
					'ProjectTask.project_id' => $projectListIds,
					'ProjectTask.special'    => 0,
				),
				'fields' => array('id','parent_id', 'project_id')
			));
			$listProjectTaskIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
			$listActivityTaskFollowProject = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
			$parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id')) : array();
			foreach($projectTasks as $key => $projectTask){
				foreach($parentIds as $parentId){
					if($projectTask['ProjectTask']['id'] == $parentId){
						unset($listProjectTaskIds[$key]);
					}
				}
			}
			$projectTasks = array_values($listProjectTaskIds);
			//GET CONSUMED
			foreach($listActivityTaskFollowProject as $projectId => $tasks) {
				$tasks = array_values($tasks);
				$activityTasksLinked = $this->ActivityTask->find('list', array(
					'recursive'  => -1,
					'conditions' => array('ActivityTask.project_task_id' => $tasks),
					'fields'     => array('project_task_id', 'id')
				));
				$consumed[$projectId] = $this->ActivityRequest->find('all',array(
					'recursive'  => -1,
					'fields'     => array('SUM(value) AS consumed'),
					'conditions' => array(
						'status'     => 2,
						'task_id'    => $activityTasksLinked,
						'company_id' => $company,
						'NOT'        => array('value' => 0, "task_id" => null)
					),
				));
				$consumed[$projectId] = Set::classicExtract($consumed[$projectId], '{n}.0.consumed');
			}
			$_previous = array_values($activitiesLinked);
			$previous = $this->ActivityRequest->find('all',array(
				'recursive'     => -1,
				'fields'        => array('SUM(value) AS consumed','activity_id'),
				'conditions'    => array(
					'status'        => 2,
					'activity_id'   => $_previous,
					'company_id'    => $company,
					'NOT'           => array('value' => 0),
				),
				'group' => 'activity_id'
			));
			$previous = !empty($previous) ? Set::combine($previous,'{n}.ActivityRequest.activity_id','{n}.0.consumed') : array();
			$results = array();

			$workloadFromTask = $this->ProjectTask->find('all',array(
				'recursive'  => -1,
				'conditions' => array(
					'project_id' => $projectListIds,
					'id'         => $projectTasks,
					'special'    => 0
				),
				'fields' => array('SUM(estimated) as workload', 'project_id'),
				'group'  => 'project_id'
			));
			$workloadFromTask = !empty($workloadFromTask) ? Set::combine($workloadFromTask,'{n}.ProjectTask.project_id','{n}.0.workload') : array();
			$staffings = $this->TmpStaffingSystem->find('all',array(
				'recursive'  => -1,
				'conditions' => array(
					'project_id' => $projectListIds
				),
				'fields' => array('SUM(estimated) as workload, SUM(consumed) as consumed, model', 'project_id'),
				'group'  => array('model', 'project_id')
			));
			foreach($staffings as $index => $data) {
				$_data = array_merge($data[0],$data['TmpStaffingSystem']);
				//DATA WORKLOAD
				if($_data['model'] == 'employee') {
					$staffingsE[$_data['project_id']] = $_data['workload'];
				} elseif($_data['model'] == 'skill') {
					$staffingsS[$_data['project_id']] = $_data['workload'];
				} elseif($_data['model'] == 'profile') {
					$staffingsP2[$_data['project_id']] = $_data['workload'];
				} else {
					$staffingsP[$_data['project_id']] = $_data['workload'];
				}
				//DATA CONSUMED
				if($_data['model'] == 'employee') {
					$staffingsConsumedE[$_data['project_id']] = $_data['consumed'];
				} elseif($_data['model'] == 'skill') {
					$staffingsConsumedS[$_data['project_id']] = $_data['consumed'];
				} else {
					$staffingsConsumedP[$_data['project_id']] = $_data['consumed'];
				}
			}
			$listIdRebuilds = array();
			foreach($projects as $id => $name) {
				$error = 0;
				$_workload   = isset($workloadFromTask[$id]) ? $workloadFromTask[$id] : 0.00;
				$_staffingE  = isset($staffingsE[$id]) ? $staffingsE[$id] : 0.00;
				$_staffingP  = isset($staffingsP[$id]) ? $staffingsP[$id] : 0.00;
				$_staffingP2 = isset($staffingsP2[$id]) ? $staffingsP2[$id] : 0.00;
				if( ($_staffingE != $_workload) || ($_staffingP != $_workload) || ($_staffingP2 != $_workload) ) {
					$error = 1;
				}
				$_previous = 0;
				if(isset($activitiesLinked[$id]) && is_numeric($activitiesLinked[$id])) {
					$_previous = $activitiesLinked[$id];
				}
				$_consumed = isset($previous[$_previous]) ? $previous[$_previous] : 0.00;
				$_consumed = isset($consumed[$id]) ? $_consumed + $consumed[$id][0] : $_consumed;
				$_consumed = number_format($_consumed, 2, '.', '');
				$_staffingConsumedE = isset($staffingsConsumedE[$id]) ? $staffingsConsumedE[$id] : 0.00;
				$_staffingConsumedP = isset($staffingsConsumedP[$id]) ? $staffingsConsumedP[$id] : 0.00;
				if( ($_staffingConsumedE != $_consumed) || ($_staffingConsumedP != $_consumed) ) {
					$error = 1;
				}
				if($error > 0 || !empty($rebuildStaffings[$id])) {
					$listIdRebuilds[] = $id;
				}
			}
			// doan nay show nhung project task loi.
			$result = $workloadForTask = $consumeForTask = array();
			foreach ($listIdRebuilds as $pid) {
				$result[$pid]['project_name'] = $projects[$pid];
				$tmp_staffing = $this->TmpStaffingSystem->find('all', array(
					'recursive'  => -1,
					'conditions' => array(
						'project_id' => $pid
					),
					'fields'     => array('model', 'model_id', 'SUM(estimated) as workload', 'SUM(consumed) as consume'),
					'group'      => array('model_id')
				));
				$tmp_staffing = !empty($tmp_staffing) ? Set::combine($tmp_staffing, '{n}.TmpStaffingSystem.model_id', '{n}.0') : array();
				//check assign and workload.
				$date_modify = $this->TmpStaffingSystem->find('first', array(
					'recursive'  => -1,
					'conditions' => array(
						'project_id' => $pid
					),
					'fields'     => array('updated')
				));
				if(!empty($date_modify) && !empty($date_modify['TmpStaffingSystem']['updated'])){
					//TH 1 vai task loi. Da build staffing.
					$date_modify = $date_modify['TmpStaffingSystem']['updated'];
					$_listProjectTaskEr = $this->ProjectTask->find('all', array(
						'recursive'  => -1,
						'conditions' => array(
							'project_id' => $pid,
							'updated >=' => $date_modify
						),
						'fields' => array('id', 'task_title', 'updated')
					));
					if(empty($_listProjectTaskEr)){
						$_listProjectTaskEr = $this->ProjectTask->find('all', array(
							'recursive'  => -1,
							'conditions' => array(
								'project_id' => $pid
							),
							'fields' => array('id', 'task_title', 'updated')
						));
					}
				} else {
					//TH chua build staffing. tat ca tast loi.
					$_listProjectTaskEr = $this->ProjectTask->find('all', array(
						'recursive'  => -1,
						'conditions' => array(
							'project_id' => $pid
						),
						'fields' => array('id', 'task_title', 'updated')
					));
				}
				$listProjectTaskEr = !empty($_listProjectTaskEr) ? Set::combine($_listProjectTaskEr, '{n}.ProjectTask.id', '{n}.ProjectTask.task_title') : array();
				$listModify = !empty($_listProjectTaskEr) ? Set::combine($_listProjectTaskEr, '{n}.ProjectTask.id', '{n}.ProjectTask.updated') : array();
				$listAssignOfTask = $this->ProjectTaskEmployeeRefer->find('all', array(
					'recursive'  => -1,
					'conditions' => array(
						'project_task_id' => array_keys($listProjectTaskEr)
					),
					'fields' => array('id', 'reference_id', 'project_task_id', 'estimated', 'is_profit_center')
				));
				$r = $t = array();
				foreach ($listAssignOfTask as $_listAssignOfTask) {
					$dx = $_listAssignOfTask['ProjectTaskEmployeeRefer'];
					$employeeModify = $this->ActionLog->find('first', array(
						'recursive'  => -1,
						'conditions' => array(
							'company_id' => $company,
							'AND'        => array(
								'OR'     => array(
									array(
										'url'       => 'project_tasks/saveNcTask',
										'method'    => 'POST',
										'data LIKE' => '%'. $dx['project_task_id'] .'"%',
									),
									array(
										'url'    => 'project_tasks/updateTaskJson/' . $pid . '/' . $dx['project_task_id'],
										'method' => 'PUT'
									)
								)
							),
						),
						'order' => array('created DESC')
					));
					$modify = $_d = '';
					if(!empty($employeeModify)){
						$_d = $employeeModify['ActionLog']['created'];
						$employeeModify = $employeeModify['ActionLog']['employee_id'];
						$modify = !empty($listEmploy[$employeeModify]) ? $listEmploy[$employeeModify] : '';
					}
					if(!empty($listProjectTaskEr[$dx['project_task_id']])){
						// workload
						$t['workload'] = $dx['estimated'];
						//assign.
						if($dx['is_profit_center'] == 1){
							$t['assign'] = !empty($dx['reference_id']) && !empty($listProfitCenter[$dx['reference_id']]) ? $listProfitCenter[$dx['reference_id']] : '';
						} else {
							$t['assign'] = !empty($dx['reference_id']) && !empty($listEmploy[$dx['reference_id']]) ? $listEmploy[$dx['reference_id']] : '';
						}
						//date updated and modify by
						$t['modify_by'] = $modify;
						$t['date_modify'] = !empty($_d) ? $_d : date('Y-m-d H:i:s', $listModify[$dx['project_task_id']]);
						$r[ $listProjectTaskEr[$dx['project_task_id']] ][] = $t;
					}
				}
				$result[$pid]['task'] = $r;
				// build lai staffing
				if($rebuild == 1){
					$this->ProjectTask->staffingSystem($pid,false);
				}
			}
			if($rebuild == 1){
				$this->loadModel('CompanyConfig');
				$stf_updated = time();
				$last = $this->CompanyConfig->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'cf_name' => 'milestone_check_staffing',
						'company' => $user['company_id']
					),
					'fields'=> array('id')
				));
				if( empty($last)){
					$this->CompanyConfig->create();
					$this->CompanyConfig->save(array(
						'cf_name' => 'milestone_check_staffing',
						'cf_value' => $stf_updated,
						'company' => $user['company_id']
					));
				}else{
					$this->CompanyConfig->updateAll(
						array('cf_value' => $stf_updated),
						array(
							'cf_name' => 'milestone_check_staffing',
							'company' => $user['company_id']
						)
					);
				}
			}
			$this->ZAuth->respond('success', $result);
        }
        $this->unauthorized();
    }
    /*
    *
    * Testing methods
    *
    */

    public function test(){
        $this->ZAuth->respond('test_ok', $this->data);
    }

    /*
    *
    * Helper Methods
    *
    */

    private function login_failed(){
        $this->ZAuth->respond('login_failed', null, 'login failed', '0');
    }

    private function unauthorized(){
        $this->ZAuth->respond('unauthorized', null, 'unauthorized', '0');
    }

    public function appError(){
        $this->unauthorized();
    }

    private function parseRequest(){
        if ($this->RequestHandler->requestedWith('json')) {
            // force array
            $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);

            if (!is_null($jsonData) and $jsonData != false) {
                $this->data = $jsonData;
            }
        } else if( $this->RequestHandler->isPost() ){

        } else if( $this->RequestHandler->isPut() ){

        }
    }
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
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParamsMonth($byWeek = false, $firstMonth = false, $firstYear = false) {
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
            if (($week == 1 && $params['week'] <= 53) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        } else if( $params['year'] ){
            $_start = mktime(0, 0, 0, 1, 1, $params['year']);
            $_end = mktime(0, 0, 0, 12, 31, $params['year']);
            $this->set(compact('_start', '_end'));
            return array($_start, $_end);
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
        if($firstYear == true){
            $date = strtotime('01-01-'.date('Y', $date));
            $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
            $_year = date('Y', $date) + 1;
            $_date = mktime(0, 0, 0, 1, 1, $_year);
            $mondayOfNextMonth = (date('w', $_date) == 1) ? $_date : strtotime('next monday', $_date);
            $_end = mktime(0, 0, 0, date('m', $mondayOfNextMonth), date('d', $mondayOfNextMonth) - 1, date('Y', $mondayOfNextMonth));
        }
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }
	/**
     * Reject timesheet
     *
     * @return void
     * @access protected
     */
    function _rejectTimeSheet($employee_id, $startDate, $endDate){
		$this->loadModels('ActivityRequestConfirm', 'ActivityRequest');
		$status = 1;
		$requestConfirms = $this->ActivityRequestConfirm->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'start', 'end', 'status'),
			'conditions' => array(
				'status' => array(0, 2),
				'employee_id' => $employee_id,
				'OR' => array(
					'start BETWEEN ? AND ?' => array($startDate, $endDate),
					'end BETWEEN ? AND ?' => array($startDate, $endDate),
					'? BETWEEN start AND end' => $startDate,
					'? BETWEEN start AND end' => $endDate,
				),
			)
		));
		if(!empty($requestConfirms)){
			foreach($requestConfirms as $key => $confirms){
				 $this->ActivityRequestConfirm->create();
				 $start = $confirms['ActivityRequestConfirm']['start'];
				 $end = $confirms['ActivityRequestConfirm']['end'];
				 if($confirms['ActivityRequestConfirm']['id']){
					$this->ActivityRequestConfirm->id = $confirms['ActivityRequestConfirm']['id'];
				 }
				 $savedConfirms = array(
					'status' => $status
				 );
				 if($this->ActivityRequestConfirm->save($savedConfirms)){
					// $this->_cacheRequest($_start, $_end, $id, $status, $profit['id'], true, $employeeName['company_id'], $employeeValidate);
					$this->ActivityRequest->unbindModelAll();
					$this->ActivityRequest->updateAll(array('status' => $status), array(
						'date BETWEEN ? AND ?' => array($start, $end),
						'employee_id' => $employee_id
					));
				 }
			 }
		}
	}
	public function execute_view(){
		$result = array();
		$message = '';
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'EV_DATA_EMPTY');
		}
		$user = $this->ZAuth->user();		
		$data = array(
			'view_name' => '',			
			'list' => ''
		);
		$data = array_merge( $data, $this->data);
		if( $data['list'] == 'list'){ // show list view
			$db = ConnectionManager::getDataSource('default');
			$query = 'SHOW FULL TABLES WHERE TABLE_TYPE = "VIEW"';
			$list = $db->query($query);
			if( empty( $list)){
				$message = __('There are no views on your database', true);
			}else{
				$key = array_keys($list['0']['TABLE_NAMES']);
				$key = $key[0];
				$list = Set::classicExtract( $list, '{n}.TABLE_NAMES.'.$key);
			}
			$this->ZAuth->respond('success', $list, $message);
		}elseif( !empty($data['view_name'])){ // show data 1 view
			$company_id = $user['company_id'];
			// check view is exist
			$db = ConnectionManager::getDataSource('default');
			$check_qr = 'SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = "'.$data['view_name'].'"';
			$check = $db->query($check_qr);
			if( !empty($check)){ // has view
				$view_name = $data['view_name'];
				$qr = 'SELECT * FROM `'.$view_name.'` where `Company ID` = '.$company_id;
				$result = $db->query($qr);
				$result = !empty($result) ? Set::extract($result, '{n}.'.$view_name) : array();
				$this->ZAuth->respond('success', $result, $message);
			}else{ //there is no view
				$message = sprintf( __('View name: %s not found'), $view_name);
				$this->ZAuth->respond('error', null, $message, 'EV_NOT_FOUND');
			}
		}else{
			$message = __('The data has been submited to server is invaild.', true);
			$this->ZAuth->respond('data_empty', null, $message, 'EV_DATA_INVAILD');
		}
		$this->ZAuth->respond('data_empty', null, $message, 'EV_DATA_INVAILD');
	}
	/* Huynh 04-11-2020
	 * Map filed for employee table
	 * Only user list field defined by Yann!
	 */
	// private function map_user_fields($input){
		// $data = array();
		// foreach( $input as $key => $value){
			// switch( $key ){
				// case 'FirstName':
				// case 'LastName':
				// case 'Email':
				// case 'FirstName':
				// case 'FirstName':
				// case 'FirstName':
				// case 'FirstName':
					// $key = Inflector::underscore($key);
					// $data[$key] = $value;
					// break;
			// }
		// }
		// debug( $data); exit;
	// }
	private function validate_employee_input($data, $is_update = false){
		$user = $this->ZAuth->user();
		$res = array();
		$company_id = $user['company_id'];
		//check empty
		if( $is_update){
			$notEmptyFields = array('first_name', 'last_name', 'email', 'role', 'profit_center', 'external', 'password');
			foreach( $notEmptyFields as $i => $key ){
				if( isset( $data[$key]) && empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'EMP03'.( $i+1) );
			}
		}else{
			$notEmptyFields = array('first_name', 'last_name', 'email', 'role', 'profit_center', 'external');
			foreach( $notEmptyFields as $i => $key ){
				if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'EMP03'.( $i+1) );
			}
		}
		$this->loadModels('Employee');
		foreach( $data as $key => $value){
			$value = is_array($value) ? array_map('trim',$value) : trim($value);
			if($value  == ''){
				if( $is_update){
					$map_keys = array(
						'role' => 'role_id',
						'profit_center' => 'profit_center_id',
						'city' => 'city_id',
						'country' => 'country_id',
						'type_of_contract' => 'contract_type_id',
						'skill' => 'function_id',
						'profile_account' => 'profile_account',
					);
					if( !empty(  $map_keys[$key])) $res[$map_keys[$key]] = '';
					else  $res[$key] = '';
				}
				continue;
			}
			switch( $key ){
				case 'password':
					// User default by company
					$res[$key] = $value;
					break;
				case 'email':
					// check format
					$email_pattern = '/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/';
					if( !preg_match($email_pattern, $value)) $this->ZAuth->respond('error', $value, __('Email must be in the correct format', true),'EMP040');
					// Check unique
					if( $is_update){
						$email = $this->Employee->find('count', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'email' => $value,
								'id !=' => $data['id']
							),
						));
					}else{
						$email = $this->Employee->find('count', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'email' => $value
							),
						));
					}
					if( $email ) $this->ZAuth->respond('error', $value, __('Email already exist', true), 'EMP040');
					$res[$key] = $value;
					break;
				case 'code_id':
					if( $key == 'code_id') $err_code = 'EMP021';
				case 'identifiant':
					if( $key == 'identifiant') $err_code = 'EMP022';
				case 'id3':
					if( $key == 'id3') $err_code = 'EMP023';
				case 'id4':
					if( $key == 'id4') $err_code = 'EMP024';
				case 'id5':
					if( $key == 'id5') $err_code = 'EMP025';
				case 'id6':
					if( $key == 'id6') $err_code = 'EMP026';
					$value = strip_tags($value);
					// Check unique
					$conditions = array('company_id' => $company_id);
					$conditions[$key] = $value;
					
					if( $is_update){
						$conditions['id !='] = $data['id'];
					}
					$code_id = $this->Employee->find('count', array(
						'recursive' => -1,
						'conditions' => $conditions,
					));
					if( $code_id ) $this->ZAuth->respond('error', $value, sprintf(__('%1$s already exists', true), $key),$err_code);
					$res[$key] = $value;
					break;
					
				case 'first_name':
				case 'last_name':
				case 'address':
					$value = strip_tags($value);
					$res[$key] = $value;
					break;
				
				case 'capacity_by_year':
					if( is_numeric($value) && ($value > 365)){
						$this->ZAuth->respond('error', null, sprintf(__('%1$s more than 365 MD', true), $key),'EMP050');
					}
					// break;
				case 'tjm':
					if( is_numeric($value)){
						$value = round($value, 2);
					}
				case 'post_code':
				case 'work_phone':
				case 'mobile_phone':
				case 'home_phone':
				case 'fax':
					if( !is_numeric($value))
						$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key),'EMP049');
					$res[$key] = $value;
					break;
					
				case 'start_date':
					$err_code = 'EMP041';
				case 'end_date':
					if( $key == 'end_date') $err_code = 'EMP042';
					
					// pattern check dd-mm-yyyy
					// Good pattern /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]|(?:Jan|Mar|May|Jul|Aug|Oct|Dec)))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2]|(?:Jan|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)(?:0?2|(?:Feb))\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9]|(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep))|(?:1[0-2]|(?:Oct|Nov|Dec)))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('%1$s format DD-MM-YYYY', true), $key), $err_code);
					
					
					// time will be converted to SQL format in Model
					$res[$key] = $value;
					break;
					
				case 'budget':
					$value = strtolower(Inflector::camelize($value));
					switch( $value){
						case 'notdisplaybudget':
							$res['see_budget'] = 0;
							$res['update_budget'] = 0;
							break;
						case 'readonlybudget':
							$res['see_budget'] = 1;
							$res['update_budget'] = 0;
							break;
						case 'updatebudget':
							$res['see_budget'] = 1;
							$res['update_budget'] = 1;
							break;
						default:
							$this->ZAuth->respond('error', $value, sprintf(__('%1$s has to be "Not Display Budget", "Readonly Budget" or "Update Budget"', true), $key), 'EMP066');
					}
					break;
				// case 'see_budget':
				// case 'update_budget':
				// case 'ws_account':
				case 'external':
				case 'actif':
				case 'control_resource':
				case 'see_all_projects':
				case 'update_your_form':
				case 'create_a_project':
				case 'delete_a_project':
				case 'change_status_project':
				case 'can_communication':
				case 'can_see_forecast':
				case 'email_receive':
				case 'activate_copy':
				case 'is_enable_popup':
				case 'auto_timesheet':
				case 'auto_absence':
				case 'auto_by_himself':
					if( ( strtolower($value) == 'yes') || ( strtolower($value) == 'y') || ($value == 'true') || ($value == '1')) $value = 1;
					elseif( ( strtolower($value) == 'no') || ( strtolower($value) == 'n') || ($value == 'false') || ($value == '0')) $value = 0;
					else{
						$this->ZAuth->respond('error', $value, sprintf(__('%1$s has to be  YES or NO', true), $key), 'EMP048');
					}
					$res[$key] = $value;
					if( ($key == 'external') && ($value == 1) ){
						if( empty( $data['external_company'])) $this->ZAuth->respond('error', null, __('Missing field external_company', true),'EMP037' );
						$model = 'External';
						$this->loadModel($model);
						$vals = $this->$model->find('all', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'name' => $data['external_company']
							),
							'fields' => array('id', 'name') 
						));
						if( !count( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), 'external_company'), 'EMP065');
						$val = array_shift($vals);
						$res['external_id'] = $val[$model]['id'];
					}
					break;
				// String input
				// case 'role_id':
				// case 'ws_account':
				case 'role':
					$camelize = Inflector::camelize($value);
					$lower = strtolower($camelize);
					$value = (($lower == 'projectmanager') || ($lower == 'pm')) ? 3 : (($lower == 'consultant') ? 4 : '');
					if( empty( $value)){
						$this->ZAuth->respond('error', $value, sprintf(__('%1$s has to be  PROJECTMANAGER  or CONSULTANT', true), $key), 'EMP060');
					}
					$res['role_id'] = $value;
					break;
					
				// case 'profit_center_id':
				case 'profit_center':
					$model = 'ProfitCenter';
					$k = 'profit_center_id';
					$err_code = 'EMP061';
					
				// case 'city_id':
				case 'city':
					if( $key ==  'city'){
						$model = 'City';
						$k = 'city_id';
						$err_code = 'EMP062';
					}
					
				// case 'country_id':
				case 'country':
					if( $key ==  'country'){
						$model = 'Country';
						$k = 'country_id';
						$err_code = 'EMP063';
					}
					
				// case 'contract_type_id':
				case 'type_of_contract':
					if( $key ==  'type_of_contract'){
						$model = 'ContractType';
						$k = 'contract_type_id';
						$err_code = 'EMP064';
					}
					$this->loadModel($model);
					$vals = $this->$model->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'name' => $value
						),
						'fields' => array('id', 'name') 
					));
					if( !count( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), $key), $err_code);
					$val = array_shift($vals);
					$res[$k] = $val[$model]['id'];
					break;					
				
				// project_functions
				// case 'function_id':
				case 'skill':
					if( $key ==  'skill'){
						$model = 'ProjectFunction';
						$k = 'function_id';
						$err_code = 'EMP064';
					}
					$this->loadModel($model);
					$vals = $this->$model->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'name' => $value
						),
						'fields' => array('id', 'name') 
					));
					if( !count( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), $key), $err_code);
					$skills = array_values( $vals);
					$skills = array_map('strtolower', $skills);
					if( is_array($value)){
						foreach( $value as $sk){
							if( !in_array( strtolower($sk), $skills))
								$this->ZAuth->respond('error', $sk, sprintf(__("Skill `%s` doesn't exist", true), $sk), $err_code);
						}
					}
					$res[$k] = array_keys( $vals);
					break;
				
				case 'profile_account':
					if( $key ==  'profile_account'){
						$model = 'ProfileProjectManager';
						$k = 'profile_account';
						$err_code = 'EMP066';
					}
					$this->loadModel($model);
					$vals = $this->$model->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'profile_name' => $value
						),
						'fields' => array('id', 'profile_name') 
					));
					if( !count( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), $key), $err_code);
					$val = array_shift($vals);
					$res[$k] = $val[$model]['id'];
					break;	
			}
		}
		// debug( $res ); exit;
		return $res;
	}
	public function createuser(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Employee');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'EV_DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$canAddMoreMax = $this->Employee->canAddMoreMax($company_id);
		if(!$canAddMoreMax){
			$this->ZAuth->respond('error', null, __('Cannot create more than actif max.', true), 'NOT_SAVED');
		}
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user_id)));
        $employee_all_info = $employee_all_info[0];
        $employee_all_info["Employee"]["is_sas"] = 0;
        $this->employee_info = $employee_all_info;
		$company_default = $this->Employee->default_user_profile($company_id);
		$default = array( // default value by Yann request
			'actif' => 1,
			'control_resource' => 1,
			'see_all_projects' => 1,
			'update_your_form' => 1,
			'create_a_project' => 1,
			'delete_a_project' => 1,
			'change_status_project' => 1,
			'see_budget' => 0,
			'update_budget' => 0,
			'can_see_forecast' => 0,
			'can_communication' => 0,
		);
		if(isset($company_default['sl_budget'])) {
			if(($company_default['sl_budget']) == 0){
				$default['update_budget'] = 0;
				$default['see_budget'] = 0;
			} elseif(($company_default['sl_budget']) == 1) {
				$default['update_budget'] = 0;
				$default['see_budget'] = 1;
			} else {
				$default['update_budget'] = 1;
				$default['see_budget'] = 1;
			}
		}
		$data = $this->validate_employee_input($this->data);
		$data = array_merge( $company_default, $default, $data);
		$data['password'] = md5( $data['password']);
		$data['company_id'] = $company_id ;
		$unUserKeys = array(
			'first_letter_first_name',
			'sperator',
			'first_letter_last_name',
			'domain_name',
		);
		foreach( $data as $k => $v){
			if( in_array( $k, $unUserKeys)) unset( $data[$k]);
		}
		if( !empty( $data['start_date'] ) && !empty($data['end_date'])){
			$_start = $this->Employee->convertTime($data['start_date']);
			$_start = strtotime($_start);
			$_end = $this->Employee->convertTime($data['end_date']);
			$_end = strtotime($_end);
			if( $_start > $_end){
				$this->ZAuth->respond('error', array('start_date' => $data['start_date'], 'end_date' => $data['end_date']), __('The end date must be greater than start date.', true), 'EMP043');
			}
		}
		$this->data = $data;
		$saved = $this->Employee->save($this->data);
		if ($saved ) {
			$employee_id = $this->Employee->id;
			$role_id = $data['role_id'];
			
			// company Reference
			$this->Employee->CompanyEmployeeReference->AddCompanyEmployeeRefer($company_id, $employee_id, $role_id, $data['control_resource'], $data['see_all_projects'], $data['see_budget']);
			
			// save function id
			$data['function_id'] = !empty( $data['function_id']) ? $data['function_id'] : array();
			$this->Employee->ProjectEmployeeProfitFunctionRefer->saveFunctionEmployee($data['profit_center_id'], $employee_id, $data['function_id']);
			$this->Employee->generateEmployeeAvatar($employee_id, true);
			// write Log
			$this->writeLog($this->data, array( 'Employee' => $user), sprintf('Add new resource `%s %s` %s by Web Services', $data["first_name"], $data["last_name"], $company_id));
			$return = $this->Employee->find('first', array(
				'conditions' => array(
					'Employee.id' => $employee_id,
					'Employee.company_id' => $company_id
				),
				'contain' => array(
					'CompanyEmployeeReference',
					'City',
					'Country',
					'ProjectEmployeeProfitFunctionRefer',
				)
				
			));
			if( !empty($return['CompanyEmployeeReference'][0]) ) unset($return['CompanyEmployeeReference'][0]);
			unset($return['Employee']['password']);
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save employee', true), 'NOT_SAVED');
	}
	public function modifyuser(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Employee','ProjectEmployeeManager');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user_id)));
        $employee_all_info = $employee_all_info[0];
        $employee_all_info["Employee"]["is_sas"] = 0;
        $this->employee_info = $employee_all_info;
		$update_refer = isset($this->data['control_resource']) || isset($this->data['see_all_projects']) || isset($this->data['budget']) || isset($this->data['role']);
		$update_skill = isset($this->data['skill']);
		$update_pc = isset($this->data['profit_center']);
		// Ticket #1036. Updated by QuanNV. 17/02/2021
		if( empty( $this->data['id'])){
			$emp_info_from_email = array();
			$count_emp = 0;
			if(!empty($this->data['email'])){
				$emp_info_from_email = $this->Employee->find("list", array(
					'recursive' => -1,
					'conditions' => array(
						'email' => $this->data['email'],
						'company_id' => $company_id,
					),
					'fields' => array('id')
				));
				$count_emp = count($emp_info_from_email); //dem so account co cung email.
			}
			if( (empty($this->data['email'])) || (empty($emp_info_from_email)) || ($count_emp > 1) ){
				if( (empty($this->data['email'])) || (empty($emp_info_from_email)) ){
					$this->ZAuth->respond('error', null, __('Employee not found', true), 'EMP010' );
				}else if($count_emp > 1){
					$this->ZAuth->respond('error', null, __('Many companies have the same user as email: ', true). $this->data['email'], 'EMP030' );
				}else{
					$this->ZAuth->respond('error', null, __('Missing field id', true), 'EMP030' );
				}
			}else{
				$this->data['id'] = array_values($emp_info_from_email);
				$this->data['id'] = $this->data['id'][0];
			}
		}
		//kiem tra co phai admin, admin thi bao loi.
		$info_user_update = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $this->data['id'])));
		if(empty($info_user_update )){
			$this->ZAuth->respond('error', null, __('Employee not found', true), 'EMP010' );
		}
		$info_user_update = $info_user_update[0];
		if($info_user_update['CompanyEmployeeReference']['role_id'] < 3){
			$this->ZAuth->respond('error', sprintf(__('The role has to be a project manager or consultant', true)), 'EMP060');
		}
		// End ticket #1036
		$employee = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $this->data['id'],
				'company_id' => $company_id,
			),
			'fields' => '*'
		));
		if( empty( $employee)){
			$this->ZAuth->respond('error', null, __('Employee not found', true), 'EMP010' );
		}
		if( !empty( $employee['Employee']['start_date'])) $employee['Employee']['start_date'] = date('d-m-Y', strtotime( $employee['Employee']['start_date']));
		if( !empty( $employee['Employee']['end_date'])) $employee['Employee']['end_date'] = date('d-m-Y', strtotime( $employee['Employee']['end_date']));
		$data = $this->validate_employee_input($this->data, true);
		$data = array_merge( $employee['Employee'], $data);
		if( !empty($this->data['password'])) $data['password'] = Security::hash($data['password'], null, false);;
		$_start = $data['start_date'];
		$_end = $data['end_date'];
		if( !empty( $_start ) && !empty( $_end )){
			$_start = $this->Employee->convertTime($_start);
			$_end = $this->Employee->convertTime($_end);
			$_start = strtotime($_start);
			$_end = strtotime($_end);
			if( $_start > $_end){
				$this->ZAuth->respond('error', array('start_date' => date('d-m-Y', $_start), 'end_date' => date('d-m-Y', $_end)), __('The end date must be greater than start date.', true), 'EMP043');
			}
		}
		$this->data = $data;
		$employee_id = $employee['Employee']['id'];
		$this->Employee->id = $employee_id;
		$saved = $this->Employee->save($this->data);
		if ($saved ) {
			if( $update_refer){
				// company Reference
				$refer = $this->Employee->CompanyEmployeeReference->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'employee_id' => $employee_id
					)
					// 'fields' => array('id')
				));
				if( !empty($refer)){
					$refer = $refer['CompanyEmployeeReference'];
					$refer_id = $refer['id'];
					$role_id = !empty( $data['role_id'] ) ? $data['role_id'] : $refer['role_id'];
					//kiem tra neu la change role tu PM->CS, can check PM co assign project ko
					$checkPMassignProject = array();
					$checkPMassignProject = $this->ProjectEmployeeManager->find("first", array(
						'recursive' => -1,
						'conditions' => array(
							'project_manager_id' => $employee_id
						),
						'fields' => array('id')
					));
					if(!empty($checkPMassignProject) && ($role_id > 3)){
						$this->ZAuth->respond('error', sprintf(__('Project has rights on one or several projects', true)), 'EMP060');
					}
					$control_resource = isset( $data['control_resource'] ) ? $data['control_resource'] : $refer['control_resource'];
					$see_all_projects = isset( $data['see_all_projects'] ) ? $data['see_all_projects'] : $refer['see_all_projects'];
					$see_budget = isset( $data['see_budget'] ) ? $data['see_budget'] : $refer['see_budget'];
					$this->Employee->CompanyEmployeeReference->saveCompanyEmployeeRefer($refer_id, $company_id, $employee_id, $role_id, $control_resource, $see_all_projects, $see_budget);
				}
			}
			if( $update_skill){
				// save function id
				$employee = $this->Employee->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $employee_id,
					),
					'fields' => '*'
				));
				$function_id = !empty( $data['function_id']) ? $data['function_id'] : array();
				$profit_center_id = !empty( $employee['Employee']['profit_center_id']) ? $employee['Employee']['profit_center_id'] : '';
				$this->Employee->ProjectEmployeeProfitFunctionRefer->editFunctionEmployee($profit_center_id, $employee_id, $function_id);
			}elseif( $update_pc){
				$profit_center_id = !empty( $data['profit_center_id']) ? $data['profit_center_id'] : array();
				$this->Employee->ProjectEmployeeProfitFunctionRefer->updateAll(
					array('profit_center_id' => $profit_center_id), // value
					array('employee_id' => $employee_id) // conditions
				);
			}
			$this->Employee->generateEmployeeAvatar($employee_id, true);
			// write Log
			$this->writeLog($this->data, array( 'Employee' => $user), sprintf('Add new resource `%s %s` %s by Web Services', $employee['Employee']["first_name"], $employee['Employee']["last_name"], $company_id));
			$return = $this->Employee->find('first', array(
				'conditions' => array(
					'Employee.id' => $employee_id,
					'Employee.company_id' => $company_id
				),
				'contain' => array(
					'CompanyEmployeeReference',
					'City',
					'Country',
					'ProjectEmployeeProfitFunctionRefer',
				)
				
			));
			if( !empty($return['CompanyEmployeeReference'][0]) ) unset($return['CompanyEmployeeReference'][0]);
			unset($return['Employee']['password']);
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save employee', true), '2');
	}
	public function deactivateuser(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Employee');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user_id)));
        $employee_all_info = $employee_all_info[0];
        $employee_all_info["Employee"]["is_sas"] = 0;
        $this->employee_info = $employee_all_info;
		// if( empty( $this->data['id'])) $this->ZAuth->respond('error', null, __('Missing field id', true), 'EMP030' );
		
		if( empty( $this->data['id'])){
			$emp_info_from_email = array();
			$count_emp = 0;
			if(!empty($this->data['email'])){
				$emp_info_from_email = $this->Employee->find("list", array(
					'recursive' => -1,
					'conditions' => array(
						'email' => $this->data['email'],
						'company_id' => $company_id,
					),
					'fields' => array('id')
				));
				$count_emp = count($emp_info_from_email); //dem so account co cung email.
			}
			if( (empty($this->data['email'])) || (empty($emp_info_from_email)) || ($count_emp > 1) ){
				if( (empty($this->data['email'])) || (empty($emp_info_from_email)) ){
					$this->ZAuth->respond('error', null, __('Employee not found', true), 'EMP010' );
				}else if($count_emp > 1){
					$this->ZAuth->respond('error', null, __('Many companies have the same user as email: ', true). $this->data['email'], 'EMP030' );
				}else{
					$this->ZAuth->respond('error', null, __('Missing field id', true), 'EMP030' );
				}
			}else{
				$this->data['id'] = array_values($emp_info_from_email);
				$this->data['id'] = $this->data['id'][0];
			}
		}
		// debug($this->data['id']);exit;
		
		$employee = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $this->data['id'],
			),
			'fields' => array( 'id', 'end_date', 'actif', 'first_name', 'last_name'), // first_name, first_name for Log write
		));
		if( empty( $employee)){
			$this->ZAuth->respond('error', null, __('Employee not found', true), 'EMP010' );
		}
		if( !empty( $employee['Employee']['end_date'])) $employee['Employee']['end_date'] = date('d-m-Y', strtotime( $employee['Employee']['end_date']));
		// debug( $employee); 
		// exit;
		$default = array( //default value by Yann request
			// 'end_date' => '', // time will be converted to SQL format 
			'actif' => 0,
		);
		// Han che input fields
		$data = array(
			'id' => $this->data['id'],
			'actif' => isset($this->data['actif']) ? $this->data['actif'] : 0,
		);
		if( isset($this->data['end_date'])) $data['end_date'] = $this->data['end_date'];
		$data = $this->validate_employee_input($data, true);
		// debug( $data);
		$data = array_merge( $default, $employee['Employee'], $data);
		// var_dump( $data['actif']); exit;
		if( ($data['actif'] == 0) && empty($data['end_date'])) $data['end_date'] = date('d-m-Y');
		
		$this->data = $data;
		$employee_id = $data['id'];
		// debug( $this->data); exit;
		$this->Employee->id = $employee_id;
		$saved = $this->Employee->save($this->data);
		if ($saved ) {
			// write Log
			$this->writeLog($this->data, array( 'Employee' => $user), sprintf('Update Employee Active status `%s %s` %s by Web Services', $data["first_name"], $data["last_name"], $company_id));
			$return = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $this->data['id'],
				),
				'fields' => array( 'id', 'end_date', 'actif'),
			));
			if( !empty($return['CompanyEmployeeReference'][0]) ) unset($return['CompanyEmployeeReference'][0]);
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save employee', true), '2');
	}
	private function validate_externalbudget_input($data){
		$res = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		$this->loadModels('Employee', 'Project' , 'ProjectBudgetExternal' );
		$notEmptyFields = array('name');
	
		foreach( $notEmptyFields as $i => $key ){
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'EXB02'.( $i+1) );
		}
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'EXB023');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'EXB022');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'EXB022');
			$project = $project[0];
		}else{
			 $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), 'project_name'),'EXB022');
		}
		$project_id = $project['Project']['id'];
		$res['project_id'] = $project_id;
		$res['activity_id'] = $project['Project']['activity_id'];
		$res['name'] = $data['name'];;
		$external_item = $this->ProjectBudgetExternal->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'name' => $data['name']
			),
			'fields' => array('*') 
		));
		if( !empty( $external_item)){
			$external_item = $external_item['ProjectBudgetExternal'];
			$res['id'] = $external_item['id'];
			if( (floatval($external_item['progress_md']) > 0) && !empty($data['man_day']) ){
				$this->ZAuth->respond('error', null, __('Can not update progress_md for this external item (man_day is exists)', true),'EXB050');
			}
			if( (floatval($external_item['man_day']) > 0)&& !empty($data['progress_md']) ){
				$this->ZAuth->respond('error', null, __('Can not update man_day for this external item (progress_md is exists)', true),'EXB050');
			}
			
		}else{	
			$res['created'] = time();
		}
		$check_empty = 1;
		$res['capex_id'] = null;
		$capex_opex = array(
			'opex' => 0, 
			'capex' => 1,
			null => null
		);
		foreach( $data as $key => $value){
			if($key != 'capex_opex' && $value  == ''){
				continue;
			}
			$value = trim(strip_tags($value));
			switch( $key){
				case 'provider':
					if( $key ==  'provider'){
						$model = 'BudgetProvider';
						$k = 'budget_provider_id';
						$err_code = 'EXB041';
					}	
				case 'type':
					if( $key ==  'type'){
						$model = 'BudgetType';
						$k = 'budget_type_id';
						$err_code = 'EXB042';
					}
					
				case 'profit_center':
					if( $key ==  'profit_center'){
						$model = 'ProfitCenter';
						$k = 'profit_center_id';
						$err_code = 'EXB043';
					}
				case 'funder':
					if( $key ==  'funder'){
						$model = 'BudgetFunder';
						$k = 'funder_id';
						$err_code = 'EXB044';
					}
					$this->loadModel($model);
					$val = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'name' => $value
						),
						'fields' => array('id', 'name') 
					));
					if( $check_empty && empty( $val) ){
						$this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), $key), $err_code);
					}
					$res[$k] = $val[$model]['id'];
					if( $key == 'type' && !isset($data['capex_opex'])){
						$this->loadModel('BudgetType');
						$_budgetTypes = $this->BudgetType->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'id' => $val[$model]['id'],
							),
							'fields' => array('id', 'name', 'capex')
						));
						$res['capex_id'] = intval($_budgetTypes['BudgetType']['capex']);
					}
					break;
					
				case 'capex_opex':
					$value = strtolower($value);
					$res['capex_id'] = @$capex_opex[$value];
					if( !in_array($value, array_keys($capex_opex))) $this->ZAuth->respond('error', $value, __('capex_opex has to be "capex" or "opex" or empty', true), $err_code);
					break;
				case 'order_date': 
					$err_code = 'EXB031';					
				case 'expected_date':
					if( $key == 'expected_date') $err_code = 'EXB032';					
				case 'due_date':
					if( $key == 'due_date') $err_code = 'EXB033';
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('%1$s format DD-MM-YYYY', true), $key), $err_code);					
					$res[$key] = $this->ProjectBudgetExternal->convertTime($value);			
					break;
					
				case 'budget_euro':
					if( $key == 'budget_euro'){
						$k = 'budget_erro';
						$err_code = 'EXB034';
					}
				case 'ordered_euro':
					if( $key == 'ordered_euro'){
						$k = 'ordered_erro';
						$err_code = 'EXB035';
					}
				case 'remain_euro':
					if( $key == 'remain_euro') {
						$k = 'remain_erro';
						$err_code = 'EXB036';
					}
				case 'man_day':
					if( $key == 'man_day'){
						$k = 'man_day';
						$err_code = 'EXB037';
					}
				case 'progress_md':
					if( $key == 'progress_md'){
						$k = 'progress_md';
						$err_code = 'EXB038';
					}
					if( !is_numeric($value))
						$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					$res[$k] = $value;
					break;
				case 'reference':
					if( $key == 'reference'){
						$k = 'reference';
					}
				case 'reference2':
					if( $key == 'reference2'){
						$k = 'reference2';
					}
				case 'reference3':
					if( $key == 'reference3'){
						$k = 'reference3';
					}
				case 'reference4':
					if( $key == 'reference4'){
						$k = 'reference4';
					}
					$res[$k] = $value;
					break;
			}
		}
		if( !empty( $external_item)){
			$external_id = $external_item['id'];
			$provision = ClassRegistry::init('ProjectBudgetProvisional');
			$provision->virtualFields['total_value'] = 'SUM(value)';
			$total = $provision->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
					'model' => 'External',
					'model_id' => $external_id
				),
				'fields' => array('view', 'total_value'),
				'group' => array('view')
			));
			if( isset($total['man-day']) && !empty($data['man_day'])){
				$md = floatval($data['man_day']);
				if( $md < $total['man-day'] ){
					$this->ZAuth->respond('error', null, sprintf(__('Budget man day (%s) < provisional budget man day (%s)', true), $md, $total['man-day']),'EXB051');
				}
			}
			if( isset($total['euro']) && !empty($data['budget_euro'])){
				$eur = floatval( $data['budget_euro'] );
				if( $eur < $total['euro'] ){
					$this->ZAuth->respond('error', null, sprintf(__('Budget euro (%s) < provisional budget euro (%s)', true), $eur, $total['euro']),'EXB052');
				}
			}
		}
		$res['updated'] = time();
		return $res;
	}
	public function update_external_budget(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Employee');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user_id)));
        $employee_all_info = $employee_all_info[0];
        $employee_all_info["Employee"]["is_sas"] = 0;
        $this->employee_info = $employee_all_info;
		$data = $this->validate_externalbudget_input($this->data, true);
		$log = 'Update Budget External item `%s` by %s use Web Services';
		if( empty($data['id'])){
			$this->ProjectBudgetExternal->create();
			$log = 'Create Budget External item `%s` by %s use Web Services';
		}
		$this->data = $data;
		$saved = $this->ProjectBudgetExternal->save($this->data);
		if ($saved ) {
			// write Log
			$ex_id = $this->ProjectBudgetExternal->id;
			$this->writeLog($this->data, array( 'Employee' => $user), sprintf($log, $ex_id, $user["fullname"]), $company_id);
			$this->ProjectBudgetExternal->recursive = -1;
			$this->ProjectBudgetExternal->Behaviors->attach('Containable');
			$return = $this->ProjectBudgetExternal->find('first', array(
				// 'recursive' => -1,
				'conditions' => array(
					'ProjectBudgetExternal.id' => $ex_id,
				),
				'contain' => array(
					'BudgetProvider' => array('id', 'name'),
					'BudgetType' => array('id', 'name'),
					'ProfitCenter' => array('id', 'name'),
					'BudgetFunder' => array('id', 'name'),
					'Project' => array('id', 'project_name')
				)
				
			));
			$capex_opex = array(
				'opex', 
				'capex', 
			);
			$return['ProjectBudgetExternal']['capex_opex'] = @$capex_opex[$return['ProjectBudgetExternal']['capex_id']];
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save Budget External item', true), 'NOT_SAVED');
	}
	
	public function delete_external_budget(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ActivityTask', 'ProjectBudgetProvisional', 'Project', 'ProjectTask', 'ProjectBudgetExternal');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$data = $this->data;
		
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'EXB010');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_name'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'EXB010');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'EXB012');
			$project = $project[0];
		}else{
			 $this->ZAuth->respond('error', null, sprintf(__('Missing field project_name', true)),'EXB020');
		}
		$project_id = $project['Project']['id'];
		$isDeleteAll = false;
		$delete = false;
		if( !empty( $data['deleteall'])){
			$isDeleteAll = in_array( strtolower(trim($data['deleteall'])), array('yes', 'y', 1)) ? 1 : 0;
		}
		$list_deleted = array();
		if( $isDeleteAll){
			$cond = array(
				'project_id' => $project_id,
			);			
			$list_deleted = $this->ProjectBudgetExternal->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
				),
				'fields' => array('id', 'id')
			));
			$this->checkBeforeDeleteExternal($project_id, $list_deleted);
			$deleted = $this->ProjectBudgetExternal->deleteAll($cond, true);
			$list_deleted = array_values($list_deleted);
			if( !$deleted){
				$this->ZAuth->respond('failed', null, __('Error when delete external budget item', true), 'NOT_SAVED');
			}else{
				foreach($list_deleted as $external_id){
					$this->refreshAfterDeleteExternal($project_id, $external_id);
				}
				$log = 'Deleted Finance Plus items by %s use Web Services';
				$log = sprintf($log, $user["fullname"]);
			}
		}else{
			if(empty($data['name'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field name', true)),'EXB010');
			$external_item['project_id'] = $project_id;
			$external_item['name'] = $data['name'];
			$last = $this->ProjectBudgetExternal->find('first', array(
				'recursive' => -1,
				'conditions' => $external_item,
				'fields' => array('*') 
			));
			if( empty( $last)){
				$this->ZAuth->respond('error', $data['name'],sprintf( __('External budget item "%s" does not exist', true), $data['name']), 'EXB010');
			}else{
				$this->checkBeforeDeleteExternal($project_id, $last['ProjectBudgetExternal']['id']);
				$deleted = $this->ProjectBudgetExternal->delete($last['ProjectBudgetExternal']['id'], true);
				if( !$deleted){
					$this->ZAuth->respond('failed', null, __('Error when delete external budget item', true), 'NOT_SAVED');
				}else{
					$this->refreshAfterDeleteExternal($project_id, $last['ProjectBudgetExternal']['id']);
				}
				$list_deleted = $last;
			}
			$log = 'Deleted external budget item `%s` by %s use Web Services';
			$log = sprintf($log, $last['ProjectBudgetExternal']['name'], $user["fullname"]);
		}
		if ($deleted ) {
			// write Log
			$this->writeLog($list_deleted, array( 'Employee' => $user), $log, $company_id);
			$this->ZAuth->respond('success', $list_deleted);
		}
		$this->ZAuth->respond('failed', null, __('Error when delete external budget item', true), 'NOT_SAVED');
	}
	private function validate_internalbudget_input($data){
		$res = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		$this->loadModels('Employee', 'Project' , 'ProjectBudgetInternalDetail', 'ProfitCenter' );
		
		if( empty( $data['name'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), 'name'),'INB020' );
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'INB023');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'INB022');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'INB022');
			$project = $project[0];
		}else{
			 $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), 'project_name'),'INB022');
		}
		$project_id = $project['Project']['id'];
		$res['project_id'] = $project_id;
		$res['activity_id'] = $project['Project']['activity_id'];
		$res['name'] = $data['name'];;
		$internal_item = $this->ProjectBudgetInternalDetail->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'name' => $data['name']
			),
			'fields' => array('*') 
		));
		if( !empty( $internal_item)){
			$internal_item = $internal_item['ProjectBudgetInternalDetail'];
			$res['id'] = $internal_item['id'];
		}else{	
			$res['created'] = time();
		}
		$check_empty = 1;
		foreach( $data as $key => $value){
			if($value  == ''){
				continue;
			}
			switch( $key){
				case 'profit_center':
					if( $key ==  'profit_center'){
						$model = 'ProfitCenter';
						$k = 'profit_center_id';
						$err_code = 'INB041';
					}
				case 'funder':
					if( $key ==  'funder'){
						$model = 'BudgetFunder';
						$k = 'funder_id';
						$err_code = 'INB042';
					}
					$this->loadModel($model);
					$val = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'name' => $value
						),
						'fields' => array('id', 'name') 
					));
					if( $check_empty && empty( $val) ){
						$this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), $key), $err_code);
					}
					$res[$k] = $val[$model]['id'];
					break;
					
				case 'validation_date':
					if( $key == 'validation_date') $err_code = 'INB032';
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('%1$s format DD-MM-YYYY', true), $key), $err_code);					
					$res[$key] = $this->ProjectBudgetInternalDetail->convertTime($value);			
					break;
					
				case 'budget_md':
					if( $key == 'budget_md'){
						$k = 'budget_md';
						$err_code = 'INB033';
					}
				case 'average':
					if( $key == 'average'){
						$k = 'average';
						$err_code = 'INB034';
					}
					if( !is_numeric($value))
						$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					$res[$k] = $value;
					break;
			}
		}
		if( !empty( $internal_item)){
			$internal_id = $internal_item['id'];
			$provision = ClassRegistry::init('ProjectBudgetProvisional');
			$provision->virtualFields['total_value'] = 'SUM(value)';
			$total = $provision->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
					'model' => 'Internal',
					'model_id' => $internal_id
				),
				'fields' => array('view', 'total_value'),
				'group' => array('view')
			));
			if( isset($total['man-day']) && !empty($data['budget_md'])){
				$md = floatval($data['budget_md']);
				if( $md < $total['man-day'] ){
					$this->ZAuth->respond('error', null, sprintf(__('Budget man day (%s) < provisional budget man day (%s)', true), $md, $total['man-day']),'INB051');
				}
			}
			if( isset($total['euro']) && !empty($data['average']) && !empty($data['budget_md'])){
				$eur = floatval( $data['average'] ) * floatval($data['budget_md']);
				if( $eur < $total['euro'] ){
					$this->ZAuth->respond('error', null, sprintf(__('Budget euro (%s) < provisional budget euro (%s)', true), $eur, $total['euro']),'INB052');
				}
			}
		}
		$res['updated'] = time();
		return $res;
	}
	public function update_internal_budget(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Employee');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user_id)));
        $employee_all_info = $employee_all_info[0];
        $employee_all_info["Employee"]["is_sas"] = 0;
        $this->employee_info = $employee_all_info;
		$data = $this->validate_internalbudget_input($this->data, true);
		$log = 'Update Budget Internal item `%s` by %s use Web Services';
		if( empty($data['id'])){
			$this->ProjectBudgetInternalDetail->create();
			$log = 'Create Budget Internal item `%s` by %s use Web Services';
		}
		$this->data = $data;
		$saved = $this->ProjectBudgetInternalDetail->save($this->data);
		if ($saved ) {
			// write Log
			$this->loadModel('ProjectBudgetSyn');
			$this->ProjectBudgetSyn->updateBudgetSyn($data['project_id']);
			$ex_id = $this->ProjectBudgetInternalDetail->id;
			$this->writeLog($this->data, array( 'Employee' => $user), sprintf($log, $ex_id, $user["fullname"]), $company_id);
			$this->ProjectBudgetInternalDetail->recursive = -1;
			$this->ProjectBudgetInternalDetail->Behaviors->attach('Containable');
			$return = $this->ProjectBudgetInternalDetail->find('first', array(
				// 'recursive' => -1,
				'conditions' => array(
					'ProjectBudgetInternalDetail.id' => $ex_id,
				),
				'contain' => array(
					'ProfitCenter' => array('id', 'name'),
					'BudgetFunder' => array('id', 'name'),
					'Project' => array('id', 'project_name')
				)
				
			));
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save Budget Internal item', true), 'NOT_SAVED');
	}
	public function delete_internal_budget(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ProjectBudgetInternalDetail', 'ProjectBudgetProvisional', 'Project', 'ProjectBudgetInternal', 'ProjectBudgetSyn');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$data = $this->data;
		
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'INB010');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_name'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'INB010');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'INB012');
			$project = $project[0];
		}else{
			 $this->ZAuth->respond('error', null, sprintf(__('Missing field project_name', true)),'INB020');
		}
		
		$project_id = $project['Project']['id'];
		$isDeleteAll = false;
		$delete = false;
		if( !empty( $data['deleteall'])){
			$isDeleteAll = in_array( strtolower(trim($data['deleteall'])), array('yes', 'y', 1)) ? 1 : 0;
		}
		$list_deleted = array();
		if( $isDeleteAll){
			$cond = array(
				'project_id' => $project_id,
			);			
			$list_deleted = $this->ProjectBudgetInternalDetail->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
				),
				'fields' => array('id', 'id')
			));
			$this->checkBeforeDeleteInternal($project_id, $list_deleted);
			$deleted = $this->ProjectBudgetInternalDetail->deleteAll($cond, true);
			$list_deleted = array_values($list_deleted);
			if( !$deleted){
				$this->ZAuth->respond('failed', null, __('Error when delete internal budget item', true), 'NOT_SAVED');
			}else{
				$log = 'Deleted Finance Plus items by %s use Web Services';
				$log = sprintf($log, $user["fullname"]);
			}
		}else{
			if(empty($data['name'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field name', true)),'INB010');
			$external_item['project_id'] = $project_id;
			$external_item['name'] = $data['name'];
			$last = $this->ProjectBudgetInternalDetail->find('first', array(
				'recursive' => -1,
				'conditions' => $external_item,
				'fields' => array('*') 
			));
			if( empty( $last)){
				$this->ZAuth->respond('error', $data['name'],sprintf( __('Internal budget item "%s" does not exist', true), $data['name']), 'INB010');
			}else{
				$this->checkBeforeDeleteInternal($project_id, $last['ProjectBudgetInternalDetail']['id']);
				$deleted = $this->ProjectBudgetInternalDetail->delete($last['ProjectBudgetInternalDetail']['id'], true);
				if( !$deleted){
					$this->ZAuth->respond('failed', null, __('Error when delete internal budget item', true), 'NOT_SAVED');
				}
				$list_deleted = $last;
			}
			$log = 'Deleted external budget item `%s` by %s use Web Services';
			$log = sprintf($log, $last['ProjectBudgetInternalDetail']['name'], $user["fullname"]);
		}
		if ($deleted ) {
			 $activity_id = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('activity_id')
            ));
            $datas = array(
                'project_id' => $project_id,
                'activity_id' => !empty($activity_id) && !empty($activity_id['Project']['activity_id']) ? $activity_id['Project']['activity_id'] : 0
            );
			$this->ProjectBudgetInternalDetail->saveInternalDetailToSyns($datas);
			$this->ProjectBudgetSyn->updateBudgetSyn($project_id);
			// write Log
			$this->writeLog($list_deleted, array( 'Employee' => $user), $log, $company_id);
			$this->ZAuth->respond('success', $list_deleted);
		}
		$this->ZAuth->respond('failed', null, __('Error when delete external budget item', true), 'NOT_SAVED');
	}
	private function checkBeforeDeleteInternal($project_id, $id){
		$this->loadModels('ProjectBudgetProvisional');
		
		if(empty($id)) $this->ZAuth->respond('error', $id,sprintf( __('Internal budget item does not exist', true), 'INB010'));
	
		 $checkProvisionals = $this->ProjectBudgetProvisional->find('count', array(
            'recursive' => -1,
            'conditions' => array('model' => 'Internal', 'model_id' => $id, 'NOT' => array('value IS NULL'))
        ));
		if($checkProvisionals != 0){
			$this->ZAuth->respond('failed', null, __('Internal buget item filled in provisional screen', true), 'INB060');
		}
			
	}
	private function checkBeforeDeleteExternal($project_id, $id){
		$this->loadModels('ActivityTask', 'ProjectBudgetProvisional', 'ProjectTask');
		
		if(empty($id)) $this->ZAuth->respond('error', $id,sprintf( __('External budget item does not exist', true), 'EXB010'));
		
		$checkP = $this->ProjectTask->find('count',array('recursive'=>-1,'conditions'=>array(
			'ProjectTask.external_id'=>$id,'ProjectTask.project_id'=>$project_id,'ProjectTask.special_consumed <>'=>0),
			'fields'=>'id'
		));
		$checkT = $this->ActivityTask->find('count',array('recursive'=>-1,'conditions'=>array(
			'ActivityTask.external_id'=>$id,'ActivityTask.special_consumed <>'=>0),
			'fields'=>'id'
		));
		
		if($checkP != 0 || $checkT != 0){
			$this->ZAuth->respond('failed', null, __('Can\'t delete external budget item used in task has consumed', true), 'NOT_SAVED');
		}
		
		$checkProvisionals = $this->ProjectBudgetProvisional->find('count', array(
			'recursive' => -1,
			'conditions' => array('model' => 'External', 'model_id' => $id, 'NOT' => array('value IS NULL'))
		));
		if($checkProvisionals != 0){
			$this->ZAuth->respond('failed', null, __('External buget item filled in provisional screen', true), 'EXB060');
		}
			
	}
	
	private function refreshAfterDeleteExternal($project_id, $id){
		$this->loadModels('ProjectBudgetProvisional', 'Project', 'ProjectTask', 'ActivityTask', 'ProjectBudgetExternal');
		 $last = $this->ProjectBudgetExternal->find('first', array(
			'recursive' => -1,
			'fields' => array('name', 'project_id','file_attachement'),
			'conditions' => array('ProjectBudgetExternal.id' => $id))
		);
				
		$this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.model' => 'External', 'ProjectBudgetProvisional.model_id' => $id), false);
		@unlink(trim($this->_getPathExternal($last['ProjectBudgetExternal']['project_id'])
				. $last['ProjectBudgetExternal']['file_attachement']));
		/**
		 * kiem tra xem project co linked voi activity ko. Co thi lay id
		 */
		$activity_id = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $project_id),
			'fields' => array('activity_id')
		));
		$datas = array(
			'project_id' => $project_id,
			'activity_id' => !empty($activity_id) && !empty($activity_id['Project']['activity_id']) ? $activity_id['Project']['activity_id'] : 0
		);
		// Xoa project task và activity task
		$checkProTasks = $this->ProjectTask->find('all',array('recursive'=>-1,'conditions'=>array(
			'ProjectTask.external_id'=>$id,'ProjectTask.project_id'=>$project_id),
			'fields'=>'id'
			));
		$checkActTasks = $this->ActivityTask->find('all',array('recursive'=>-1,'conditions'=>array(
			'ActivityTask.external_id'=>$id),
			'fields'=>'id'
			));
		foreach($checkProTasks as $checkProTask){
			$this->ProjectTask->delete($checkProTask['ProjectTask']['id']);
		}
		foreach($checkActTasks as $checkActTask){
			$this->ActivityTask->delete($checkActTask['ActivityTask']['id']);
		}
		$this->ProjectBudgetExternal->saveExternalToSyns($datas);
	}
	private function _getPathExternal($project_id) {
        $this->loadModel('Project');
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ),
            'conditions' => array('Project.id' => $project_id)
        ));
        $pcompany = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'project_budgets' . DS . 'externals' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS . $project_id . DS;
        }
        $path .= $company['Company']['dir'] . DS . $project_id . DS;

        return $path;
    }
	private function validate_finance_input($data){
		$res = $finance_item =  $details = $msg = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		$this->loadModels('Employee', 'Project' , 'ProjectFinancePlus', 'ProjectFinancePlusDetail' );
		$notEmptyFields = array('type', 'project_code_1', 'name', 'year'); // 0 1 2 3
		$requireFields = array('4' => 'budget'); // 4
		if( !empty( $data['project_name'])) {
			unset($notEmptyFields[1]);
		}
		foreach( $requireFields as $i => $key ){
			if( !isset( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'FIN02'.( $i+1) );
		}
		
		foreach( $notEmptyFields as $i => $key ){
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'EXB02'.( $i+1) );
		}
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'FIN010');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'FIN010');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'FIN012');
			$project = $project[0];
		}
		$project_id = $project['Project']['id'];
		$finance_item['company_id'] = $company_id;
		$finance_item['project_id'] = $project_id;
		$finance_item['name'] = trim($data['name']);
		$finance_item['type'] = strtolower($data['type']);
		$last = $this->ProjectFinancePlus->find('first', array(
			'recursive' => -1,
			'conditions' => $finance_item,
			'fields' => array('*') 
		));
		if( !empty( $last)){
			$finance_item = $last['ProjectFinancePlus'];
		}
		$finance_item['activity_id'] = $project['Project']['activity_id'];
		$check_empty = 1;
		$model = array();
		foreach( $data as $key => $value){
			if($value == ''){
				continue;
			}
			$value = trim(strip_tags($value));
			switch( $key){
				case 'msg':
					$msg = array(
						'project_id' => $project_id,
						'employee_id' => $user['id'],
						'is_ws_comment' => 1,
						'comment' => $value,
						'created' => time(),
						'uppdated' => time()
					);
					break;
				case 'type':
					$value = strtolower($value);
					$allow_types = array('inv', 'fon', 'finaninv', 'finanfon');
					if( !in_array(strtolower($value), $allow_types) ){
						$this->ZAuth->respond('error', $value, sprintf(__('%1$s has to be INV, FON, FINANINV or FINANFON', true), $key), 'FIN040');
					}
					$res[$key] = $value;
					break;
				case 'year':
					$err_code = 'FIN043';if( !is_numeric($value))
					$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					if( ($value <= 2015) || ($value > 2050)){
						$this->ZAuth->respond('error', null, sprintf(__('%1$s must be greater than 2015 and less than 2050', true), $key), 'FIN047');
					}
					$res[$key] = $value;
					break;
				case 'budget':
				case 'avancement':
					$model[] = $key;
					if( $key ==  'budget') $err_code = 'FIN044';
					if( $key ==  'avancement') $err_code = 'FIN045';
					if( !is_numeric($value))
						$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					if($value > 1e16)
						$this->ZAuth->respond('error', null, sprintf(__('%1$s must be less than 1E16', true), $key), $err_code);
					$res[$key] = $value;
					break;	
				case 'date':
				case 'finance_date': // 2 cai nay la 1
					$err_code = 'FIN042';
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), $err_code);		
					App::import("vendor", "str_utility");
					$finance_item['finance_date'] = $this->ProjectFinancePlus->convertTime($value);			
					break;
			}
		}
		if( !empty( $finance_item['id'])){
			$details = $this->ProjectFinancePlusDetail->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'year' => $res['year'],
					'project_finance_plus_id' => $finance_item['id'],
					'type' => $res['type'],
					'model' => $model
				),
				'fields' => array('*'),
			));
			if( !empty( $details)) $details = Set::combine( $details, '{n}.ProjectFinancePlusDetail.model', '{n}.ProjectFinancePlusDetail');
		}
		foreach($model as $k){
			if( !empty( $details[$k])){
				$details[$k]['value'] = $res[$k];
			}else{
				$details[$k] = array(
					'company_id' => $company_id,
					'project_id' => $project_id,
					'activity_id' => $finance_item['activity_id'],
					// 'project_finance_plus_id' => $finance_item['id'], // update sau khi luu finance_item
					'type' => $res['type'],
					'model' => $k,
					'year' => $res['year'],
					'value' =>  $res[$k],
					'created' => date('Y-m-d h:i:m', time()),
					'updated' => date('Y-m-d h:i:m', time())
				);
			}
		}
		return compact( 'finance_item', 'details', 'msg');
	}
	public function update_financement(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ProjectFinancePlus', 'ProjectFinancePlusDetail');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];		
		$data = $this->validate_finance_input($this->data, true);
		$finance_item = $data['finance_item'];
		$finance_item['updated'] = date('Y-m-d h:i:m', time() );
		$details = $data['details'];
		if( empty( $finance_item['id'])){
			$this->ProjectFinancePlus->create();
			$finance_item['created'] = date('Y-m-d h:i:m', time() );
		}else{
			$this->ProjectFinancePlus->id = $finance_item['id'];
		}
		$result = $this->ProjectFinancePlus->save($finance_item);
		if( !$result){
			$this->ZAuth->respond('failed', null, __('Error when save Finance Plus item', true), 'NOT_SAVED');
		}
		$finance_id = $this->ProjectFinancePlus->id;
		if( !empty( $data['msg'])){
			$this->loadModels('ProjectFinancePlusTxt', 'ProjectFinancePlusTxtView');
			$msg = $data['msg'];
			$msg['project_finance_plus_id'] = $finance_id;
			$this->ProjectFinancePlusTxt->create();
			$result = $this->ProjectFinancePlusTxt->save($msg);
			$read_status = $this->ProjectFinancePlusTxtView->find('first', array(
				'conditions' => array(
					'project_finance_plus_id' => $finance_id,
					'employee_id' => $user_id,
				),
			));
			if( !empty( $read_status)){
				$read_status = $read_status['ProjectFinancePlusTxtView'];
				$read_status['read_status'] = 1;
				$this->ProjectFinancePlusTxtView->id = $read_status['id'];;
				$this->ProjectFinancePlusTxtView->save(array(
					'read_status' => 1,
				));
			}else{
				$this->ProjectFinancePlusTxtView->create();
				$this->ProjectFinancePlusTxtView->save(array(
					'project_finance_plus_id' => $finance_id,
					'employee_id' => $user_id,
					'read_status' => 1,
				));
			}
		}
		foreach( $details as $finance_detail_item){
			$finance_detail_item['project_finance_plus_id'] = $finance_id;
			if( empty( $finance_detail_item['id'])){
				$this->ProjectFinancePlusDetail->create();
				$finance_detail_item['created'] = date('Y-m-d h:i:m', time() );
			}else{
				$this->ProjectFinancePlusDetail->id = $finance_detail_item['id'];
			}
			$finance_detail_item['updated'] = date('Y-m-d h:i:m', time() );
			$result = $this->ProjectFinancePlusDetail->save($finance_detail_item);
			if( !$result){
				$this->ZAuth->respond('failed', null, __('Error when save Finance Plus item', true), 'NOT_SAVED');
			}
		}
		if ($result ) {
			// write Log
			$log = 'Update Project Finance Plus item `%s` by %s use Web Services';
			$this->writeLog($data, array( 'Employee' => $user), sprintf($log, $finance_id, $user["fullname"]), $company_id);
			$this->ProjectFinancePlus->recursive = -1;
			$this->ProjectFinancePlus->Behaviors->attach('Containable');
			$return = $this->ProjectFinancePlus->find('first', array(
				'conditions' => array(
					'ProjectFinancePlus.id' => $finance_id,
				),
				'contain' => array(
					'ProjectFinancePlusTxt' => array(
						'fields' => array('id', 'project_finance_plus_id', 'comment', 'created', 'employee_id'),
						'order' => array('created'=> 'DESC')
					),
					'ProjectFinancePlusDetail' => array(
						'fields' => array('model', 'year', 'value'),
						'order' => array(
							'year' => 'ASC',
							'model'=> 'DESC'
						),
					),
				),
			));
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save Finance Plus item', true), 'NOT_SAVED');
	}
	public function delete_financement(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Project', 'ProjectFinancePlus', 'ProjectFinancePlusDetail');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$data = $this->data;
		$requireFields = array('type', 'project_code_1');
		if( !empty( $data['project_name'])) {
			unset($requireFields[1]);
		}
		foreach( $requireFields as $i => $key ){
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'FIN02'.( $i+1) );
		}
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'FIN010');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'FIN010');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'FIN012');
			$project = $project[0];
		}
		$project_id = $project['Project']['id'];
		$type = strtolower($data['type']);
		$isDeleteAll = false;
		if( !empty( $data['deleteall'])){
			$isDeleteAll = in_array( strtolower($data['deleteall']), array('yes', 'y', 1)) ? 1 : 0;
		}
		$list_deleted = array();
		if( $isDeleteAll){
			$cond = array(
				'company_id' => $company_id,
				'project_id' => $project_id,
			);			
			if( trim(strtolower($type)) != 'all')
				$cond['type'] = explode(',', str_replace(' ', '', strtolower($data['type'])));
			$list_deleted = $this->ProjectFinancePlus->find('list', array(
				'recursive' => -1,
				'conditions' => $cond,
				'fields' => array('id', 'id')
			));
			$deleted = $this->ProjectFinancePlus->deleteAll($cond, true);
			$list_deleted = array_values($list_deleted);
			if( !$deleted){
				$this->ZAuth->respond('failed', null, __('Error when delete Finance Plus item', true), 'NOT_SAVED');
			}
			$log = 'Deleted Finance Plus items by %s use Web Services';
			$log = sprintf($log, $user["fullname"]);
		}else{
			$finance_item['company_id'] = $company_id;
			$finance_item['project_id'] = $project_id;
			$finance_item['name'] = $data['name'];
			$finance_item['type'] = $type;
			$last = $this->ProjectFinancePlus->find('first', array(
				'recursive' => -1,
				'conditions' => $finance_item,
				'fields' => array('*') 
			));
			if( empty( $last)){
				$this->ZAuth->respond('error', $finance_item, __('Finance item does not exist', true), 'FIN011');
			}else{
				$deleted = $this->ProjectFinancePlus->deleteAll($finance_item, true);
				if( !$deleted){
					$this->ZAuth->respond('failed', null, __('Error when delete Finance Plus item', true), 'NOT_SAVED');
				}
				$list_deleted = $last;
			}
			$log = 'Deleted Finance Plus item `%s` by %s use Web Services';
			$log = sprintf($log, $last['ProjectFinancePlus']['name'], $user["fullname"]);
		}
		if ($deleted ) {
			// write Log
			$this->writeLog($list_deleted, array( 'Employee' => $user), $log, $company_id);
			$this->ZAuth->respond('success', $list_deleted);
		}
		$this->ZAuth->respond('failed', null, __('Error when delete Finance Plus item', true), 'NOT_SAVED');
	}
	private function validate_phase_input($data){
		$res = $msg = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		$this->loadModels( 'Project', 'ProjectPhasePlan', 'ProjectPhase');
		$notEmptyFields = array('phase_name', 'project_code_1', 'start_date');
		if( !empty( $data['project_name'])) {
			unset($notEmptyFields[1]);
		}
		foreach( $notEmptyFields as $i => $key ){
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'PH02'.( $i+1) );
		}
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'PH010');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'UPH011');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'PH012');
			$project = $project[0];
		}
		$project_id = $project['Project']['id'];
		$companyPhases = $this->ProjectPhasePlan->ProjectPhase->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'ProjectPhase.activated' => 1,
            ),
            'fields' => array('id', 'name', 'color'),
            'order' => array('phase_order' => 'asc')
        ));
		$listPhases = !empty($companyPhases) ? ( Set::combine($companyPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name' ) ) : array();
		$companyPhases = !empty($companyPhases) ? (Set::combine($companyPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase' )) : array();
		$phase_id = array_search($data['phase_name'], $listPhases);
		if (false === $phase_id) {
			$this->ZAuth->respond('error', $data['phase_name'],sprintf( __('The Phase named "%s" does not exist or has been disabled', true), $data['phase_name']), 'PH013');
		} else {
			$item['project_planed_phase_id'] = $phase_id;
		}
		$item['project_id'] = $project_id;
		$last = $this->ProjectPhasePlan->find('first', array(
			'recursive' => -1,
			'conditions' => $item,
			'fields' => array('*') 
		));
		if( !empty( $last)){
			$item = $last['ProjectPhasePlan'];
		}else{
			//created
			$item['created'] = time();
			//weight
			$max_weight = $this->ProjectPhasePlan->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
				),
				'fields' => array('id', 'weight'),
				'order' => array('weight' => 'desc'),
			));
			$item['weight'] = !empty($max_weight) ?  $max_weight['ProjectPhasePlan']['weight'] + 1 : 0;
		}
		$item['color'] = $companyPhases[$phase_id]['color'];
		$model = array();
		foreach( $data as $key => $value){
			if(empty($value)){
				continue;
			}
			$value = trim(strip_tags($value));
			switch( $key){
				case 'ref1':
				case 'ref2':
				case 'ref3':
				case 'ref4':
					$item[$key] = $value;
					break;
				case 'progress':
					$err_code = 'PH043';
					if( !is_numeric($value))
					$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					if( ($value < 0) || ($value > 100)){
						$this->ZAuth->respond('error', null, sprintf(__('%1$s must be from 0 to 100', true), $key), 'PH047');
					}
					$item[$key] = number_format($value, 2);
					break;
				case 'part_name':
				case 'status':
				// case 'profile_id': // Khong tim thay logic trong man hinh cu
					if( $key ==  'part_name'){
						$model = 'ProjectPart';
						$k = 'project_part_id';
						$err_code = 'PH044';
						$conditions =  array(
							'project_id' => $project_id,
							'title' => $value
						);
					}
					if( $key ==  'status'){
						$model = 'ProjectPhaseStatus';
						$k = 'project_phase_status_id';
						$err_code = 'PH045';
						$conditions =  array(
							'company_id' => $company_id,
							'phase_status' => $value,
							'display' => 1
						);
					}
					$this->loadModel($model);
					$vals = $this->$model->find('all', array(
						'recursive' => -1,
						'conditions' => $conditions,
						'fields' => array('id') 
					));
					if( !count( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__("%s doesn't exist", true), $key), $err_code);
					$val = array_shift($vals);
					$item[$k] = $val[$model]['id'];
					break;
				case 'start_date':
					$err_code = 'PH041';
					$k = 'phase_planed_start_date';
				case 'end_date': 
					if( $key == 'end_date') {
						$err_code = 'PH042';
						$k = 'phase_planed_end_date';
					}
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), $err_code);
					$item[$k] = $this->ProjectPhasePlan->convertTime($value);			
					break;
			}
		}
		// check phase_planed_start_date > phase_planed_end_date
		if( !empty( $item['phase_planed_end_date']) && ($item['phase_planed_end_date'] < $item['phase_planed_start_date'])){
			$this->ZAuth->respond('error', array('phase_planed_start_date' => $item['phase_planed_start_date'], 'phase_planed_end_date' => $item['phase_planed_end_date']), __('The end date must be greater than start date.', true), 'PH046');
		}
		// END check phase_planed_start_date > phase_planed_end_date
		// update end_date
		if( empty($item['phase_planed_end_date'])){
			$date = date_create_from_format('Y-m-d', $item['phase_planed_start_date']);
			date_add($date, date_interval_create_from_date_string('1 day'));
			$item['phase_planed_end_date'] = date_format($date, 'Y-m-d');
		}
		// update real date, weight
		foreach( array('start', 'end') as $is_max => $key){
			if( empty( $item['phase_real_'.$key.'_date'])){
				$item['phase_real_'.$key.'_date'] = $item['phase_planed_'.$key.'_date'];
			}else{
				$plan_date = date_create_from_format('Y-m-d', $item['phase_planed_'.$key.'_date']);
				$plan_date = $plan_date->format('U');
				$real_date = date_create_from_format('Y-m-d', $item['phase_real_'.$key.'_date']);
				$real_date = $real_date->format('U');
				$real_date = $is_max ? max($plan_date, $real_date) : min($plan_date, $real_date);
				$item['phase_real_'.$key.'_date'] = date('Y-m-d', $real_date);
			}
		}
		// END update real date
		$item['updated'] = time();
		return $item;
	}
	public function update_phase(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ProjectPhasePlan', 'ProjectPhase');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];		
		$data = $this->validate_phase_input($this->data);
		if( empty( $data['id'])){
			$this->ProjectPhasePlan->create();
			$log = 'Create new Project Phase Plan item `%s` by %s use Web Services';
		}else{
			$this->ProjectPhasePlan->id = $data['id'];
			$log = 'Update Project Phase Plan item `%s` by %s use Web Services';
		}
		$result = $this->ProjectPhasePlan->save($data);
		if( !$result){
			$this->ZAuth->respond('failed', null, __('Error when save Project Phase Plan item', true), 'NOT_SAVED');
		}else{
			$data = $result['ProjectPhasePlan'];
			$phase_id = $this->ProjectPhasePlan->id;
			$data['id'] = $phase_id;
			// write Log
			$this->writeLog($data, array( 'Employee' => $user), sprintf($log, $phase_id, $user["fullname"]), $company_id);
			$this->ProjectPhasePlan->belongsTo['Project']['fields'] = array('id', 'project_name', 'project_code_1');
			$return = $this->ProjectPhasePlan->find('first', array(
				'conditions' => array(
					'ProjectPhasePlan.id' => $phase_id,
				),
			));
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save Project Phase Plan item', true), 'NOT_SAVED');
	}
	public function delete_phase(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels( 'Project', 'ProjectPhasePlan', 'ProjectPhase', 'ProjectTask');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$data = $this->data;
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'PH010');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'PH010');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'FIN011');
			$project = $project[0];
		}else{
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s" or "%2$s"', true), 'project_code_1', 'project_name'),'PH012');
		}
		$project_id = $project['Project']['id'];
		$isDeleteAll = false;
		if( !empty( $data['deleteall'])){
			$isDeleteAll = in_array( strtolower($data['deleteall']), array('yes', 'y', 1)) ? 1 : 0;
		}
		$list_deleted = array();
		if( $isDeleteAll){
			$cond = array(
				'ProjectPhasePlan.project_id' => $project_id,
			);
			$list_deleted = $this->ProjectPhasePlan->find('list', array(
				'recursive' => -1,
				'conditions' => $cond,
				'fields' => array('id', 'id')
			));
			$list_deleted = !empty($list_deleted) ? array_values($list_deleted) : array();
			$tasks = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
					'project_planed_phase_id' => $list_deleted
				)
			));
			if( count($tasks)){
				$this->ZAuth->respond('error', $tasks,__('Cannot be removed, tasks attached to the phase', true),'PH051');
            } else {
				$deleted = $this->ProjectPhasePlan->deleteAll($cond, true);
				if( !$deleted){
					$this->ZAuth->respond('failed', null, __('Error when delete Project Phase Plan item', true), 'NOT_SAVED');
				}
                $this->ProjectPhasePlan->updateAll(array('predecessor' => null), array(
					'predecessor' => $list_deleted
				));
            }
			
			$log = 'Deleted all Project Phase Plan items in project "%1$s" by %2$s use Web Services';
			$log = sprintf($log, $project_id, $user["fullname"]);
		}else{
			$last = $this->ProjectPhasePlan->find('first', array(
                'recursive' => -1,
                'conditions' => array(
					'ProjectPhase.name' => $data['phase_name'], 
					'ProjectPhasePlan.project_id' => $project_id
				),
				'joins' => array(
					array(
						'table' => 'project_phases',
						'alias' => 'ProjectPhase',
						'type' => 'inner',
						'conditions' => array('ProjectPhase.id = ProjectPhasePlan.project_planed_phase_id')
					)
				),
				'fields' => array(
					'ProjectPhasePlan.id',
					// 'ProjectPhase.name',
				),
            ));
			if( empty( $last)){
				$this->ZAuth->respond('error', $data['phase_name'], __('Project Phase Plan item does not exist', true), 'PH011');
			}else{
				$phase_plan_id = $last['ProjectPhasePlan']['id'];
				$tasks = $this->ProjectTask->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $project_id,
						'project_planed_phase_id' => $phase_plan_id
					)
				));
				if(count( $tasks) ){
					$this->ZAuth->respond('error', $tasks,__('Cannot be removed, tasks attached to the phase', true),'PH051');
				} 
				$deleted = $this->ProjectPhasePlan->delete($phase_plan_id);
				if( !$deleted){
					$this->ZAuth->respond('failed', null, __('Error when delete Project Phase Plan item', true), 'NOT_SAVED');
				}
				$this->ProjectPhasePlan->updateAll(
					array('predecessor' => null), 
					array('predecessor' => $phase_plan_id)
				);
				$list_deleted = array($phase_plan_id);
			}
			$log = 'Deleted Project Phase Plan item `%s` by %s use Web Services';
			$log = sprintf($log, $data['phase_name'], $user["fullname"]);
		}
		if ($deleted ) {
			// write Log
			$this->writeLog($list_deleted, array( 'Employee' => $user), $log, $company_id);
			$this->ZAuth->respond('success', $list_deleted);
		}
		$this->ZAuth->respond('failed', null, __('Error when delete Project Phase Plan item', true), 'NOT_SAVED');
	}
	public function update_task(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ProjectPhasePlan', 'ProjectPhase', 'ProjectTask', 'ProjectPart', 'ProjectTaskEmployeeRefer');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user['id'])));
		$employee_all_info = $employee_all_info[0];
		$employee_all_info["Employee"]["is_sas"] = 0;
		$this->employee_info = $employee_all_info;
		$this->Session->write('Auth.employee_info', $employee_all_info);	
		$data = $this->validate_task_input($this->data);
		$_duration = $this->getWorkingDays($data['task_start_date'], $data['task_end_date'], null);
		$data['duration'] = $_duration;
		if( empty( $data['id'])){
			$this->ProjectTask->create();
			$log = 'Create new Project Tasks item `%s` by %s use Web Services';
		}else{
			$this->ProjectTask->id = $data['id'];
			$log = 'Update Project Tasks item `%s` by %s use Web Services';
		}
		$result = $this->ProjectTask->save($data);
		if( !$result){
			$this->ZAuth->respond('failed', null, __('Error when save Project Tasks item', true), 'NOT_SAVED');
		}else{
			$task_id = $this->ProjectTask->id;
			$project_id = $data['project_id'] ;
			// write Log
			$project_part_id = $data['part_id'];
			if($data['part_id'] == 0 && !empty($data['part_name'])){
				$this->ProjectPart->create();
				$this->ProjectPart->save(array(
					'project_id' => $project_id,
					'title' => $data['part_name'],
					'weight' => 0,
				));
				$project_part_id = $this->ProjectPart->id;
				
			}
			if($data['project_planed_phase_id'] == 0 && !empty($data['phase_name'])){
				$project_phase_id = $data['project_phase_id'];
				$project_planed_phase_id = $data['project_planed_phase_id'];
				if($data['project_phase_id'] == 0){
					$phase_last_order = $this->ProjectPhase->find("first", array(
						'recursive' => -1,
						"conditions" => array(
							"company_id" => $company_id,
						),
						"fields" => array("(Max(ProjectPhase.phase_order)+1) phase_last_order")
					));
					$phase_order = $phase_last_order[0]["phase_last_order"];
					$this->ProjectPhase->create();
					$project_phase = $this->ProjectPhase->save(array(
						'name' => $data['phase_name'],
						'project_id' => $project_id,
						'add_when_create_project' => 0,
						'activated' => 1,
						'tjm' => null,
						'phase_order' => $phase_order,
						'phase_order' => '#004380',
						'profile_id' =>  null,
						'company_id' => $company_id,
					));
					
					$project_phase_id = $this->ProjectPhase->id;
				
				}
			
				$phase_plan_last_order = $this->ProjectPhasePlan->find("first", array(
					'recursive' => -1,
					"conditions" => array(
						'project_id' => $project_id,
					)
				));
				
				$phase_plan_last_order = !empty($phase_plan_last_order[0]["phase_plan_last_order"]) ? $phase_plan_last_order[0]["phase_plan_last_order"] : 1;
				$this->ProjectPhasePlan->create();
				$save_phase = $this->ProjectPhasePlan->save(array(
					'project_id' => $project_id,
					'planed_duration' => null,
					'project_planed_phase_id' => $project_phase_id,
					'project_phase_status_id' => null,
					'project_part_id' => !empty($project_part_id) ? $project_part_id : null,
					'color' => $data['project_phase_color'],
					'phase_planed_start_date' =>  $data['task_start_date'],
					'phase_real_start_date' =>  $data['task_start_date'],
					'phase_planed_end_date' =>  $data['task_end_date'],
					'phase_real_end_date' =>  $data['task_end_date'],
					'predecessor' =>  null,
					'weight' =>  $phase_plan_last_order,
					'ref1' =>  '',
					'ref2' =>  '',
					'ref3' =>  '',
					'ref4' =>  '',
					'profile_id' => null,
					'progress' =>  0,
				));
				
				if($save_phase){
					$project_planed_phase_id = $this->ProjectPhasePlan->id;
					$this->ProjectTask->id = $task_id;
					$this->ProjectTask->saveField('project_planed_phase_id', $project_planed_phase_id);
					
				}
			}
			$this->_syncPhasePlanTime($result);
			$this->_saveStartEndDateAllTask($project_id);
			$this->_syncActivityTask($project_id, $result, $task_id);
			
			// Delete the old employee assigned before save new assigned
			if(!empty($data['erase_current_affectation'])){
				$this->ProjectTaskEmployeeRefer->deleteAll(array('ProjectTaskEmployeeRefer.project_task_id' => $task_id), true);
			}
			// save workload for employee
			if(!empty($data['assigned_to'])){
				foreach($data['assigned_to'] as $index => $employee_id){
					$is_profit_center = 
					$employeeRefer = $this->ProjectTaskEmployeeRefer->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'project_task_id' => $task_id,
							'reference_id' => $employee_id,
							'is_profit_center' => $data['is_profit_center'][$index],
						),
						'fields' => array('id', 'created')
					));
					if(!empty($employeeRefer)){
						$refer_id = $employeeRefer['ProjectTaskEmployeeRefer']['id'];
						$this->ProjectTaskEmployeeRefer->id = $employeeRefer['ProjectTaskEmployeeRefer']['id'];
					}else{
						$this->ProjectTaskEmployeeRefer->create();
					}
					$this->ProjectTaskEmployeeRefer->save(array(
						'estimated' => $data['workload'][$index],
						'project_task_id' => $task_id,
						'reference_id' => $employee_id,
						'is_profit_center' => $data['is_profit_center'][$index],
						'updated' => time(),
						'created' => !empty($employeeRefer) ? $employeeRefer['ProjectTaskEmployeeRefer']['created'] : time(),
					));
				}
			}
			// Save comment
			if(!empty($data['text'])){
				$this->loadModel('ProjectTaskTxt');
				$this->ProjectTaskTxt->create();
				$this->ProjectTaskTxt->save(array(
					'project_task_id' => $task_id,
					'employee_id' => $user_id,
					'comment' => $data['text'],
					'creadted' => date('Y-m-d H:i:s'),
				));
			}
			$this->ProjectTask->staffingSystem($project_id);
			$this->writeLog($data, array( 'Employee' => $user), sprintf($log, $task_id, $user["fullname"]), $company_id);
		
			$return = $this->ProjectTask->find('first', array(
				'conditions' => array(
					'ProjectTask.id' => $task_id,
				),
			));
			unset($return['Employee']);
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save project task item', true), 'NOT_SAVED');
	}
	public function validate_task_input($data){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels( 'Project', 'ProjectPhasePlan', 'ProjectPhase', 'ProjectTask', 'ProjectPart', 'ProfitCenter', 'Employee', 'ProjectStatus', 'ActivityRequest', 'ActivityTask');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'UT001');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_name'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'UT002');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'UT003');
			$project = $project[0];
		}else{
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s" or "%2$s"', true), 'project_code_1', 'project_name'),'UT004');
		}
		$project_id = $project['Project']['id'];
		$item['project_id'] = $project_id;
		// Part
		$create_part = 0;
		if( !empty( $data['create_part'])){
			$create_part = in_array( strtolower($data['create_part']), array('yes', 'y', 1)) ? 1 : 0;
		}
		$part_id = 0;
		if( !empty( $data['part'])){
			$project_part = $this->ProjectPart->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'title' => $data['part'],
					'project_id' => $project_id
				),
				'fields' => array('id')
			));
			if( empty($project_part) && $create_part == 0)  $this->ZAuth->respond('error', $data['part'], sprintf(__('Project part with name "%s" does not exist', true), $data['part']),'UT005');
			$part_id = !empty($project_part['ProjectPart']['id']) ? $project_part['ProjectPart']['id'] : 0;
			$item['part_name'] = $data['part'];
		}
		$item['part_id'] = $part_id;
		
		// Phase
		$create_phase = 0;
		if( !empty( $data['create_phase'])){
			$create_phase = in_array( strtolower($data['create_phase']), array('yes', 'y', 1)) ? 1 : 0;
		}
		$project_phase_id = 0;
		$project_phase_planed_id = 0;
		$item['project_phase_color'] = '#004380';
		if( !empty( $data['phase'])){
			$project_phase = $this->ProjectPhase->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'name' => $data['phase'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'color')
			));
			
			if(!empty($project_phase)){
				$project_phase_id = $project_phase['ProjectPhase']['id'];
				$item['project_phase_color'] = $project_phase['ProjectPhase']['color'];
				$phase_conditions = array();
				if(!empty($part_id)){
					$phase_conditions = array(
						'project_planed_phase_id' => $project_phase_id,
						'project_id' => $project_id,
						'project_part_id' => $part_id
					);
				}else{
					$phase_conditions = array(
						'project_planed_phase_id' => $project_phase_id,
						'project_id' => $project_id,
						'project_part_id IS NULL'
					);
				}
				// Check if created new part do not find project phase plane
				$project_phase_plan = array();
				if(!(!empty($data['part']) && empty($part_id) && $create_part == 1)){
					$project_phase_plan = $this->ProjectPhasePlan->find('first', array(
						'recursive' => -1,
						'conditions' => $phase_conditions,
						'fields' => array('id')
					));
				}
				if( empty($project_phase_plan)  && !empty($part_id) && $create_phase == 0)  $this->ZAuth->respond('error', $data['phase'], sprintf(__('Project phase with name "%s" in the part "%s" does not exist', true), $data['phase'], $data['part']),'UT006');
				if( empty($project_phase_plan) && $create_phase == 0)  $this->ZAuth->respond('error', $data['phase'], sprintf(__('Project phase with name "%s" does not exist', true), $data['phase']),'UT006');
				$project_phase_planed_id  = !empty($project_phase_plan['ProjectPhasePlan']['id']) ? $project_phase_plan['ProjectPhasePlan']['id'] : 0;
				
			}else if($create_phase == 0) {
				$this->ZAuth->respond('error', $data['phase'], sprintf(__('Project phase with name "%s" does not exist', true), $data['phase']),'UT007');
			}
			
		}else{
			$this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'phase'),'UT008');
		}
		$item['project_phase_id'] = $project_phase_id;
		$item['project_planed_phase_id'] = $project_phase_planed_id;
		$item['phase_name'] = $data['phase'];
				
		// Task
		$task_id = 0;
		$task_estimated = 0;
		$create_task = 0;
		if( !empty( $data['create_task'])){
			$create_task = in_array( strtolower($data['create_task']), array('yes', 'y', 1)) ? 1 : 0;
		}
		
		$item['erase_current_affectation'] = (!empty($data['erase_current_affectation']) && in_array( strtolower($data['erase_current_affectation']), array('yes', 'y', 1))) ? 1 : 0;
		
		$current_workload = array();
		$workload_not_affected = 0;
		if( !empty( $data['task'])){
			$project_task = $this->ProjectTask->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'task_title' => $data['task'],
					'project_id' => $project_id
				),
				'fields' => array('id', 'is_nct', 'estimated', 'task_status_id', 'milestone_id', 'project_planed_phase_id')
			));
			
			if( empty($project_task) && $create_task == 0)  $this->ZAuth->respond('error', $data['task'], sprintf(__('Task with name "%s" does not exist', true), $data['task']),'UT009');
			
			if( !empty($project_task) && $project_task['ProjectTask']['is_nct'] == 1)  $this->ZAuth->respond('error', $data['task'], sprintf(__('Task with name "%s" is NCT task. This API Service only update the normal task', true), $data['task']),'UT010');
			
			if(!empty($project_task)){
				$task_id = $project_task['ProjectTask']['id'];
				
				$activity_task = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $task_id,
					),
					'fields' => array('id')
				));
				if(!empty($activity_task)){
					$activityRequests = $this->ActivityRequest->find("count", array(
						'recursive' => -1,
						"conditions" => array(
							'task_id' => $activity_task['ActivityTask']['id']
						)
					));
					if($activityRequests > 0){
						if($item['project_planed_phase_id'] != $project_task['ProjectTask']['project_planed_phase_id']){
							$this->ZAuth->respond('error', $data['task'], sprintf(__('Do not update Phase of task with name "%s" because task is used in a timesheet.', true), $data['task']),'UT011');
						}
					}
				}
				// if($activityRequests > 0)  $this->ZAuth->respond('error', $data['task'], sprintf(__('Task with name "%s" is used in a timesheet.', true), $data['task']),'UT011');
				if(empty($item['erase_current_affectation'])){
					$workload_refer = $this->ProjectTaskEmployeeRefer->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'project_task_id' => $task_id
						),
						'fields' => array('reference_id', 'estimated', 'is_profit_center')
					));
					
					if(!empty($workload_refer)){
						foreach($workload_refer as $index => $w_refer){
							$refer = $w_refer['ProjectTaskEmployeeRefer'];
							$current_workload[$refer['reference_id']] = $refer['estimated'];
							$task_estimated += $refer['estimated'];
						}
					}else{
						if($project_task['ProjectTask']['estimated'] > 0){
							$workload_not_affected = $project_task['ProjectTask']['estimated'];
						}
					}
				}
			}
		}else{
			$this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'task'),'UT012');
		}
		
		if( empty($data['start_date'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'start_date'),'UT013');
		if( empty($data['end_date'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'end_date'),'UT014');
		
		$item['id'] = $task_id;
		$item['task_status_id'] = $project_task['ProjectTask']['task_status_id'];
		$item['milestone_id'] = $project_task['ProjectTask']['milestone_id'];
		$item['task_title'] = $data['task'];
		if(!empty($data['assigned_to'])){
			foreach($data['assigned_to'] as $index => $employee_name){
				// workload no affected se default cong vao cho resouce dau tien trong list assigned
				if(!empty($employee_name)){
					if(strpos($employee_name, 'PC / ') === false){
						$employeeActive = $this->Employee->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'CONCAT(first_name," ",last_name)' => $employee_name,
								'actif' => 1,
								'OR' => array(
									'start_date IS NULL',
									'start_date' => '0000-00-00',
									'start_date <=' => date('Y-m-d', time())
								),
								'AND' => array(
									'OR' => array(
										'end_date IS NULL',
										'end_date' => '0000-00-00',
										'end_date >=' => date('Y-m-d', time())
									)
								)
							),
							'fields' => array('id')
						));
						if(empty($employeeActive)) $this->ZAuth->respond('error', null, sprintf(__('Employee "%1$s" not exist or not active', true), $employee_name ),'UT015');
						$item['assigned_to'][$index] = $employeeActive['Employee']['id'];
						$item['is_profit_center'][$index] = 0;
						$item['workload'][$index] = !empty($data['workload']) && !empty($data['workload'][$index]) ? $data['workload'][$index] : 0;
						if(!empty($current_workload) && !empty($current_workload[$employeeActive['Employee']['id']])){
							$task_estimated -= $current_workload[$employeeActive['Employee']['id']];
						}
						$task_estimated += $item['workload'][$index];
					}else{
						$pc_name = trim(str_replace('PC / ', '', $employee_name));
						$profitCenterActive = $this->ProfitCenter->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'name' => $pc_name,
							),
							'fields' => array('id', 'id')
						));
						if(empty($profitCenterActive)) $this->ZAuth->respond('error', null, sprintf(__('Profit center "%1$s" not exist', true), $employee_name ),'UT017');
						$item['assigned_to'][$index] = $profitCenterActive['ProfitCenter']['id'];
						$item['is_profit_center'][$index] = 1;
						$item['workload'][$index] = !empty($data['workload']) && !empty($data['workload'][$index]) ? $data['workload'][$index] : 0;
						if(!empty($current_workload) && !empty($current_workload[$profitCenterActive['ProfitCenter']['id']])){
							$task_estimated -= $current_workload[$profitCenterActive['ProfitCenter']['id']];
						}
						$task_estimated += $item['workload'][$index];
					}
				
				}
			}
		}else{
			if(!empty($data['workload'])){
				$task_estimated += $data['workload'][0];
			}else{
				$task_estimated += $workload_not_affected;
			}
		}
		$item['estimated'] = $task_estimated;
		unset($data['assigned_to']);
		unset($data['workload']);
		
		if(empty($item['id']) || !empty($data['status'])){
			$status_conditions =  array(
				'company_id' => $company_id,
				'display' => 1,
			);
			if(!empty($data['status'])){
				$status_conditions['name'] = $data['status'];
			}
			$task_status = $this->ProjectStatus->find('first', array(
				'recursive' => -1,
				'conditions' => $status_conditions,
				'fields' => array('id'),
				'order' => array('weight' => 'ASC')
			));
			if( empty($task_status) ) $this->ZAuth->respond('error', $data['status'], sprintf(__("%s doesn't exist", true), $data['status']), 'UT018');
			$item['task_status_id'] = $task_status['ProjectStatus']['id'];
		}
		foreach( $data as $key => $value){
			if(empty($value)){
				continue;
			}
			$value = trim(strip_tags($value));
			switch( $key){
				case 'text':
					$item[$key] = $value;
					break;
				case 'manual_consumed':
					$err_code = 'UT019';
					if( !is_numeric($value))
					$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					$item[$key] = number_format($value, 2);
					break;
				case 'start_date':
					if( $key ==  'start_date'){
						$k = 'task_start_date';
						$err_code = 'UT020';
					}
				case 'end_date': 
					if( $key ==  'end_date'){
						$k = 'task_end_date';
						$err_code = 'UT021';
					}
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if(($key == 'start_date' || $key == 'end_date') && $value == '00-00-0000'){
						$item[$k] = '0000-00-00'; 
					}elseif(!preg_match($date_pattern, $value, $matches)){
						$this->ZAuth->respond('error', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), $err_code);
					}else{
						$item[$k] = $this->ProjectPhasePlan->convertTime($value);
					}				
					break;
			}
		}
		if($item['task_start_date'] != '0000-00-00' && $item['task_end_date'] != '0000-00-00'){
			if(strtotime($item['task_start_date']) > strtotime($item['task_end_date'])) $this->ZAuth->respond('error', $item['task_start_date'] , sprintf(__('The start_date must not be greater than the end_date', true)), 'UT022');
			if(abs(date('Y', strtotime($item['task_start_date'])) - date('Y', time())) > 5){
				$this->ZAuth->respond('error', $item['task_start_date'] , sprintf(__('The start_date %1$s is not more than five years in the past or the future from today', true), $item['task_start_date']), 'UT023');
			}
			if(abs(date('Y', strtotime($item['task_end_date'])) - date('Y', time())) > 5){
				$this->ZAuth->respond('error', $item['task_end_date'] , sprintf(__('The end_date %1$s is not more than five years in the past or the future from today', true), $item['task_end_date']), 'UT024');
			}
		}
		return $item;
	}
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
            $_durationDate = 0;
        }
        return $_durationDate;
    }
	private function _getHoliday($startDate, $endDate){
        $this->loadModels('Holiday', 'Workday');
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];	
        $holidays = $this->Holiday->getOptionHolidays(strtotime($startDate), strtotime($endDate), $company_id);
		$workdays = $this->Workday->getOptions($company_id);
        $_holiday = array();
        if ($startDate < $endDate) {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);
            while ($startDate <= $endDate){
                $_start = strtolower(date("l", $startDate));
                $_end = strtolower(date("l", $endDate));
                if((!empty($workdays) && $workdays[$_start] == 0) || in_array($startDate, array_keys($holidays))){
                    $_holiday[] = date("m-d-Y", $startDate);
                }
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
            }
        }
        return $_holiday;
    }
	private function _syncPhasePlanTime($project_task) {
		$this->loadModel('ProjectTask');
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
	private function _getProjectPhasePlan($project_id, $project_planed_phase_id) {
		$this->loadModel('ProjectTask');
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
	private function _saveStartEndDateAllTask($project_id) {
        $this->loadModel('Project');
		$_duration = 0;
        $data = $this->_getStartEndDateAllTask($project_id);
        if(!empty($data)){
            $_data['start_date'] = $data['task_start_date'];
            $_data['end_date'] = $data['task_end_date'];
            $this->Project->id = $project_id;
            if($this->Project->save($_data)){
				$_duration = $this->getWorkingDays($_data['start_date'], $_data['end_date'], null);
			}
        }
		return $_duration;
    }
	 private function _getStartEndDateAllTask($project_id) {
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
	 private function _syncActivityTask($project_id, $project_task, $project_task_id){
		$this->loadModel('Activity');
		$activity = $this->Activity->find('all',array(
			'recursive' => -1,
			'conditions' => array('Activity.project' => $project_id),
		));
        if (!empty($activity)) {
            if (isset($activity[0])) {
                if(isset($activity[0]['Activity'])) {
                    if(isset($activity[0]['Activity']['id'])){
                        $activity_id = $activity[0]['Activity']['id'];
                        $this->_createActivityTask($project_task, $activity_id, $project_task_id, $project_id);
                    }
                }
            }
        }

    }
	private function _createActivityTask($project_task, $activity_id, $project_task_id, $project_id){
        $dataActivityTask = array();
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
        $dataActivityTask['ActivityTask']['task_status_id'] = $project_task['ProjectTask']['task_status_id'];
        $dataActivityTask['ActivityTask']['milestone_id'] = $project_task['ProjectTask']['milestone_id'];
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
	 private function _getPhaseNameByPhasePlanId($project_id, $project_planed_phase_id){
        $projectPhases = $this->_getPhaseses($project_id);
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhase.name');
        return $projectPhases[$project_planed_phase_id];
    }
	 private function _getPhaseses($project_id) {
        if (!isset($this->_phases)) {
			$user = $this->ZAuth->user();
			$company_id = $user['company_id'];
            $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
            $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
                'fields' => array('id', 'phase_planed_start_date', 'phase_planed_end_date', 'project_part_id'),
                'contain' => array('ProjectPhase' => array('id', 'name'), 'ProjectPart' => array('id', 'title')),
                'conditions' => array(
                    "ProjectPhasePlan.project_id" => $project_id,
                    'company_id' => $company_id
                )
            ));
            $this->_phases = $projectPhases;
        }
        return $this->_phases;
    }
	public function update_consumed(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ActivityRequest', 'ActivityRequestConfirm', 'CompanyEmployeeReference', 'ActivityForecastComment', 'AbsenceRequest', 'Activity');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user['id'])));
		$employee_all_info = $employee_all_info[0];
		$employee_all_info["Employee"]["is_sas"] = 0;
		$this->employee_info = $employee_all_info;
		$this->Session->write('Auth.employee_info', $employee_all_info);	
		
		$data = $this->validate_consumed_input($this->data);
		$old_item = $this->ActivityRequest->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'date' => strtotime($data['date_of_consumed']),
				'employee_id' => $data['employee_id'],
				'task_id' => $data['activity_task_id'],
				'company_id' => $company_id
			),
			'fields' => array('id')
		));
		
		if(!empty($old_item)){
			$this->ActivityRequest->id = $old_item['ActivityRequest']['id'];
			$log = 'Update a timesheet item `%s` by %s use Web Services';
		}else{
			$this->ActivityRequest->create();
			$log = 'Create a new timesheet item `%s` by %s use Web Services';
		}
		
		$result = $this->ActivityRequest->save(array(
			'date' => strtotime($data['date_of_consumed']),
			'value' => $data['consumed'],
			'employee_id' => $data['employee_id'],
			'company_id' => $company_id,
			'task_id' => $data['activity_task_id'],
			'status' => $data['item_status'],
			'activity_id' => 0
		));
		if(!$result){
			$this->ZAuth->respond('failed', null, __('Error when save Acitvity request item', true), 'NOT_SAVED');
		}else{
			$status_array = array(
				'inprogress' => -1,
				'sent' => 0,
				'rejected' => 1,
				'validated' => 2,
				
			);
			if(isset($status_array[$data['validated_timesheet']])){
				$data['validated_timesheet'] = $status_array[$data['validated_timesheet']];
			}else{
				unset($data['validated_timesheet']);
			}
			
			if(isset($data['validated_timesheet']) && $data['validated_timesheet'] != $data['item_status']){
				$this->saveConfirmTimesheet($data);
			}	
			// Save comment timesheet
			if(!empty($data['msg'])){
				$this->ActivityForecastComment->create();
				$result_msg = $this->ActivityForecastComment->save(array(
					'update_by' => $user_id,
					'employee_id' => $data['employee_id'],
					'company_id' => $company_id,
					'comment' => $data['msg'],
					'date' => strtotime($data['date_of_consumed']),
					'created' => time(),
					'updated' => time(),
					'is_timesheet_msg' => 0,
					
				));
				
			}
			
			$item_id = $this->ActivityRequest->id;
			$this->writeLog($data, array( 'Employee' => $user), sprintf($log, $item_id, $user["fullname"]), $company_id);
			$this->ActivityRequest->belongsTo['Employee']['fields'] = array('id', 'profit_center_id', 'fullname', 'actif');
			$return = $this->ActivityRequest->find('first', array(
				'conditions' => array(
					'ActivityRequest.id' => $item_id,
				),
			));
			unset($return['Activity']);
			$this->ZAuth->respond('success', $return);
		}

		$this->ZAuth->respond('failed', null, __('Error when save value item timesheet', true), 'NOT_SAVED');
	}
	public function validate_consumed_input($data){
		$result = array();
		$message = '';
		$return = array();
		
		$this->loadModels( 'Project', 'ProjectTask', 'ActivityTask', 'Activity','ProjectTaskEmployeeRefer', 'Employee', 'CompanyConfig', 'Workday', 'AbsenceRequest', 'ActivityRequest', 'Holiday', 'ActivityRequestConfirm', 'ProjectPhasePlan', 'ProjectPhase', 'ProjectPart');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$activty_id = 0;
		$is_activated = 0;
	
		if( !empty( $data['project_code_1'])){
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			if( empty($project))  $this->ZAuth->respond('error', $data['project_code_1'],sprintf( __('Project with code "%s" does not exist', true), $data['project_code_1']), 'UTI001');
		}elseif(!empty( $data['project_name'])){
			$project = $this->Project->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_name' => $data['project_name'],
					'company_id' => $company_id
				),
				'fields' => array('id', 'activity_id')
			));
			
			if( empty($project))  $this->ZAuth->respond('error', $data['project_name'],sprintf( __('Project with name "%s" does not exist', true), $data['project_name']), 'UTI002');
			if( count( $project) > 1 ) $this->ZAuth->respond('error', $data['project_name'],sprintf( __('There is more than 1 project named "%s"', true), $data['project_name']), 'UTI003');
			$project = $project[0];
		}else{
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s" or "%2$s"', true), 'project_code_1', 'project_name'),'UTI004');
		}
		$is_activated = 0;
		if(!empty( $project['Project']['activity_id'])){
			$activity = $this->Activity->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $project['Project']['activity_id']),
				'fields' => array('activated')
			));
			if(!empty($activity)) {
				$is_activated = $activity['Activity']['activated'];
			}
		}else{
			$this->ZAuth->respond('error', $data['project_name'],sprintf( __('Project named "%s" is not linked to an actvity', true), $data['project_name']), 'UTI005');
		}
		if($is_activated == 0){
			$this->ZAuth->respond('error', $data['project_name'],sprintf( __('Project named "%s" is not an activated project ', true), $data['project_name']), 'UTI006');
		}
		
		$project_id = $project['Project']['id'];
		$activity_id = $project['Project']['activity_id'];
	
		$item['project_id'] = $project_id;
		$project_phase_id = 0;
		if( !empty( $data['phase_name'])){
			$companyPhases = $this->ProjectPhasePlan->ProjectPhase->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'ProjectPhase.activated' => 1,
				),
				'fields' => array('id', 'name', 'color'),
				'order' => array('phase_order' => 'asc')
			));
			$listPhases = !empty($companyPhases) ? ( Set::combine($companyPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name' ) ) : array();
			$phase_id = array_search($data['phase_name'], $listPhases);
			if (false === $phase_id) {
				$this->ZAuth->respond('error', $data['phase_name'],sprintf( __('The Phase named "%s" does not exist or has been disabled', true), $data['phase_name']), 'UTI013');
			}
			$project_part_id = null;
			if( !empty( $data['part_name'])){
				$item_part = $this->ProjectPart->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $project_id,
						'title' => $data['part_name'],
					),
					'fields' => array('id') 
				));
			
				if (empty($item_part)) {
					$this->ZAuth->respond('error', $data['part_name'],sprintf( __('The part named "%s" does not exist or has been disabled', true), $data['part_name']), 'UTI013');
				}
				$project_part_id = $item_part['ProjectPart']['id'];
			}
			$project_phase_cond = array(
				'project_id' => $project_id,
				'project_planed_phase_id' => $phase_id,
				'project_part_id' => $project_part_id
			);
			$project_phase_plan = $this->ProjectPhasePlan->find('first', array(
				'recursive' => -1,
				'conditions' => $project_phase_cond,
				'fields' => array('id') 
			));
			if(empty($project_phase_plan)){
				if(!empty($project_part_id)){
					$this->ZAuth->respond('error', $data['phase_name'],sprintf( __('The Phase named "%s" liked with Part name "%s" does not exist in project', true), $data['phase_name'], $data['part_name']), 'UTI013');
				}
				$this->ZAuth->respond('error', $data['phase_name'],sprintf( __('The Phase named "%s" does not exist in project', true), $data['phase_name']), 'UTI013');
			}else{
				$project_phase_id = $project_phase_plan['ProjectPhasePlan']['id'];
			}
		}
		if( !empty( $data['task'])){
			$cons_find_task = array(
				'task_title' => $data['task'],
				'project_id' => $project_id
			);
			if(!empty($project_phase_id)){
				$cons_find_task['project_planed_phase_id'] = $project_phase_id;
			}
			$project_task = $this->ProjectTask->find('first', array(
				'recursive' => -1,
				'conditions' => $cons_find_task,
				'fields' => array('id', 'is_nct', 'estimated', 'task_start_date', 'task_end_date')
			));
			
			if(!empty($project_task)){
				$task_id = $project_task['ProjectTask']['id'];
				$task_start_date = $project_task['ProjectTask']['task_start_date'];
				$task_end_date = $project_task['ProjectTask']['task_end_date'];
				$activity_task = $this->ActivityTask->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $task_id,
					),
					'fields' => array('id')
				));
				if(empty($activity_task)) $this->ZAuth->respond('error', null, sprintf(__('Not found the task named "%1$s" linked in activity task', true), $data['task']),'UTI007');
				$item['activity_task_id'] = $activity_task['ActivityTask']['id'];
			}else{
				$this->ZAuth->respond('error', null, sprintf(__('Task named "%1$s" not exists in project', true), $data['task']),'UTI008');
			}
		}else{
			$this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'task'),'UTI009');
		}
		$item['project_task_id'] = $task_id;
		if(!empty($data['assigned_to'])){
			$employeeActive = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'CONCAT(first_name," ",last_name)' => $data['assigned_to'],
					'actif' => 1,
					'OR' => array(
						'start_date IS NULL',
						'start_date' => '0000-00-00',
						'start_date <=' => date('Y-m-d', time())
					),
					'AND' => array(
						'OR' => array(
							'end_date IS NULL',
							'end_date' => '0000-00-00',
							'end_date >=' => date('Y-m-d', time())
						)
					)
				),
				'fields' => array('id')
			));
			if(empty($employeeActive)) $this->ZAuth->respond('error', null, sprintf(__('Employee "%1$s" not exist or not active', true), $data['assigned_to']),'UTI010');
			$employee_id = $employeeActive['Employee']['id'];
			$item['employee_id'] = $employee_id;
		}else{
			$this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'assigned_to'),'UTI012');
		}
		if( empty($data['date_of_consumed'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'date_of_consumed'),'UTI012');
		$update_if_validated = 0;
		if( !empty( $data['update_if_validated'])){
			$update_if_validated = in_array( strtolower($data['update_if_validated']), array('yes', 'y', 1)) ? 1 : 0;
		}
		$validated_timesheet = '';
		if( !empty( $data['validated_timesheet'])){
			$status_timesheet = strtolower($data['validated_timesheet']);
			switch( $status_timesheet){
				case 'validated':
				case 'rejected':
				case 'inprogress':
				case 'sent': 
					$validated_timesheet = $status_timesheet;
					break;
				default: $validated_timesheet = '';
					break;
			}
		}
		$item['update_if_validated'] = $update_if_validated;
		$item['validated_timesheet'] = $validated_timesheet;
		if( !isset($data['consumed'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field "%1$s"', true), 'consumed'),'UTI013');
		$fill_more_than_capacity_day = 0;
		$companyConfig = $this->CompanyConfig->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'cf_name' => 'fill_more_than_capacity_day',
				'company' => $company_id
			),
			'fields'=> array('cf_value')
		));
		
		if(!empty($companyConfig)){
			$fill_more_than_capacity_day = !empty($companyConfig['CompanyConfig']['cf_value']) ? $companyConfig['CompanyConfig']['cf_value'] : 0;
		}
		$workdays = $this->Workday->getOptions($company_id);
		$value_in_date = 0;
		foreach( $data as $key => $value){
			if(empty($value) && $key != 'consumed'){
				continue;
			}
			$value = trim(strip_tags($value));
			switch( $key){
				case 'consumed':
					$err_code = 'UTI014';
					if( !is_numeric($value))
					$this->ZAuth->respond('error', null, sprintf(__('%1$s has to be a number', true), $key), $err_code);
					$value_in_date = number_format($value, 3);
					if($value_in_date < 0) $this->ZAuth->respond('error', null, sprintf(__('Consumed has to be => 0', true)), $err_code);
					$item[$key] = $value_in_date;
					break;
				case 'msg':
					$item[$key] = $value;
					break;
				case 'date_of_consumed': 
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), 'UTI015');
					$year = strtolower(date('Y', strtotime($value)));
					if($year < (date('Y', time()) - 2)) $this->ZAuth->respond('error', $value, sprintf(__('The year of date_of_consumed %1$s cannot be less than %2$s', true), $value, date('Y', time()) - 2), 'UTI018');
					if($year > (date('Y', time()) + 1)) $this->ZAuth->respond('error', $value, sprintf(__('The year of date_of_consumed %1$s cannot greater than %2$s', true), $value, date('Y', time()) + 1), 'UTI019');
					$day = strtolower(date('l', strtotime($value)));
					if(!empty($workdays) && $workdays[$day] == 0) $this->ZAuth->respond('error', $value, sprintf(__('The date_of_consumed %1$s is not a workday', true), $value), 'UTI020');
					$item[$key] = $value;	
					break;
			}
		}
		$next_sunday = strtotime('next sunday', strtotime($item['date_of_consumed']));
		$last_monday = strtotime('last monday', $next_sunday);
		$requestConfirm = $this->ActivityRequestConfirm->find('first', array(
			'recursive' => -1, 
			'fields' => array('id','status'),
            'conditions' => array('employee_id' => $employee_id, 'start' => $last_monday, 'end' => $next_sunday)
		));
		// status = -1: in-progress
		$item_status = -1; 
		if(!empty($requestConfirm)){
			$confirm = $requestConfirm['ActivityRequestConfirm']['status'];
			$confirm_id = $requestConfirm['ActivityRequestConfirm']['id'];
			if($update_if_validated == 0 && ($confirm == 2 || $confirm == 0))  $this->ZAuth->respond('error', $value, sprintf(__('Cannot update the timesheet already validated or sent', true)), 'UTI021');
			$item_status = $confirm;
			$item['confirm_id'] = $confirm_id;	
		}
		$item['start_date'] = $last_monday;
		$item['end_date'] = $next_sunday;
		$item['item_status'] = $item_status;		
		$getHolidays = $this->Holiday->getOptions($last_monday, $next_sunday, $company_id);
		$hl_in_date = 0;
		if(!empty($getHolidays)){
			foreach($getHolidays as $date => $hlday){
				if($date == strtotime($item['date_of_consumed']) && isset($hlday['am'])){
					$hl_in_date += 0.5;
				}
				if($date == strtotime($item['date_of_consumed']) && isset($hlday['pm'])){
					$hl_in_date += 0.5;
				}
			}
		}
	
		if($hl_in_date == 1)  $this->ZAuth->respond('error', $value, sprintf(__('The date_of_consumed %1$s is a holiday', true), $item['date_of_consumed']), 'UTI022');
	
		return $item;
	}
	public function saveConfirmTimesheet($data){
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		if(!empty($data['confirm_id'])){
			$this->ActivityRequestConfirm->id = $data['confirm_id'];
		}else{
			$this->ActivityRequestConfirm->create();
		}
		$data_confirm = array(
			'employee_id' => $data['employee_id'],
			'start' => $data['start_date'],
			'end' => $data['end_date'],
			'status' => $data['validated_timesheet'],
			'company_id' => $company_id,
			'employee_validate' => $this->employee_info['Employee']['fullname'],
		);
		$result_confirm = $this->ActivityRequestConfirm->save($data_confirm);
		
		// Update all item timesheet status after change the status of timesheet.
		if($result_confirm){
			 $this->ActivityRequest->updateAll(array('status' => $data['validated_timesheet']),array(
				'date BETWEEN ? AND ?' => array($data['start_date'], $data['end_date']),
				'employee_id' => $data['employee_id'],
			));
		}
		// If validated or rejected refresh staffing
		if($result_confirm && ($data['validated_timesheet'] == 2 || $data['item_status'] == 2)){
			// Rejection all employee absence waiting in this week 
			if($data['validated_timesheet'] == 2){
				$abs_employee =$this->AbsenceRequest->find("all", array(
					'recursive' => -1,
					"conditions" => array(
						'date BETWEEN ? AND ?' => array($data['start_date'], $data['end_date']), 
						'employee_id' => $data['employee_id']
					),
					'fields' => array('id', 'response_am' ,'response_pm'),
				));
				$abs_employee =  !empty($abs_employee) ? Set::combine($abs_employee, '{n}.AbsenceRequest.id', '{n}.AbsenceRequest') : array();
				$tmps = array();
				if(!empty($abs_employee)){
					foreach($abs_employee as $ab_date => $abs){
						if(!empty($abs['response_am']) && $abs['response_am'] == 'waiting'){
							$tmps['response_am'] = 'rejetion';
							$tmps['absence_am'] = 0;
							$this->AbsenceRequest->id = $abs['id'];
							$this->AbsenceRequest->save($tmps);
						}
						if(!empty($abs['response_pm']) && $abs['response_pm'] == 'waiting'){
							$tmps['response_pm'] = 'rejetion';
							$tmps['absence_pm'] = 0;
							$this->AbsenceRequest->id = $abs['id'];
							$this->AbsenceRequest->save($tmps);
						}
					}
				}
			}
			// Refresh staffing
			$requestOfWeeks = $this->ActivityRequest->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'date BETWEEN ? AND ?' => array($data['start_date'], $data['end_date']), 
					'employee_id' => $data['employee_id'],
					'company_id' => $company_id,
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
							'company_id' => $company_id
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
				$activities = !empty($activities) ? array_unique($activities) : array();
				$this->loadModel('Activity');
				$linkeds = $this->Activity->find('list', array(
					'recursive' => -1,
					'conditions' => array('Activity.id' => $activities),
					'fields' => array('id', 'project')
				));
				foreach($activities as $id){
					if(!empty($linkeds[$id])){
						$this->ProjectTask->staffingSystem($linkeds[$id]);
					} else {
						$this->ActivityTask->staffingSystem($id);
					}
				}
			}
			
		}
	}
	public function update_milestone(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('ProjectMilestone');
		if( !$this->RequestHandler->isPost() ){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		$data = $this->validate_milestone_input($this->data);
		if( $data['has_milestone'] == 0){
			$this->ProjectMilestone->create();
			unset($data['id']);
			unset($data['has_milestone']);
			$log = 'Create new Project Milestone item %s by %s use Web Services';
			$result = $this->ProjectMilestone->save($data);
		}else{
			$dataUpdate['id'] = $data['id'];
			$dataUpdate['initial_date'] = $data['initial_date']; 
			$dataUpdate['milestone_date'] = $data['milestone_date']; 
			$dataUpdate['effective_date'] = $data['effective_date'];
			$dataUpdate['validated'] = $data['validated'];
			$dataUpdate['updated'] = $data['updated'];
			$result = $this->ProjectMilestone->save($dataUpdate);
		}
		if( !$result){
			$this->ZAuth->respond('failed', null, __('Error when save Project Milestone item', true), 'NOT_SAVED');
		}else{
			$data = $result['ProjectMilestone'];
			$milestone_id = $this->ProjectMilestone->id;
			$data['id'] = $milestone_id;
			// write Log
			$this->writeLog($data, array( 'Employee' => $user), sprintf($log, $milestone_id, $user["fullname"]), $company_id);
			$this->ProjectMilestone->belongsTo['Project']['fields'] = array('id', 'project_name', 'project_code_1');
			$return = $this->ProjectMilestone->find('first', array(
				'conditions' => array(
					'ProjectMilestone.id' => $milestone_id,
				),
			));
			$this->ZAuth->respond('success', $return);
		}
		$this->ZAuth->respond('failed', null, __('Error when save Project Milestone item', true), 'NOT_SAVED');
	}
	private function validate_milestone_input($data){
		$res = $msg = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		$this->loadModels( 'ProjectMilestone','Project');
		$projectId = array();
		if(!empty($data['project_code_1']) && !empty($data['project_name'])){
			$projectId = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_code_1' => $data['project_code_1'],
					'project_name' => $data['project_name']
				),
				'fields' => array('id')
			));
			if(empty($projectId)){
				$this->ZAuth->respond('failed', null, __('Project not exist', true), 'NOT_SAVED');
			}
		}
		if(!empty($data['project_code_1']) || !empty($data['project_name'])){
			if(empty($data['milestone_name'])){
				$this->ZAuth->respond('failed', null, __('Missing milestone name', true), 'NOT_SAVED');
			}
			$data['project_milestone'] = $data['milestone_name'];
			unset($data['milestone_name']);
			if(!empty($data['project_code_1']) && empty($projectId)){
				$projectId = $this->Project->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_code_1' => $data['project_code_1']
					),
					'fields' => array('id')
				));
			}elseif(!empty($data['project_name']) && empty($projectId)){
				$projectId = $this->Project->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_name' => $data['project_name']
					),
					'fields' => array('id')
				));
			}
			if(!empty($projectId)){
				$data['project_id'] = $projectId['Project']['id'];
			}else{
				$this->ZAuth->respond('failed', null, __('Project not exist', true), 'NOT_SAVED');
			}
			$milestoneExist = array();
			$milestoneExist = $this->ProjectMilestone->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_milestone' => $data['project_milestone'],
					'project_id' => $data['project_id']
				),
				'fields' => array('id', 'initial_date', 'milestone_date', 'effective_date')
			));
			$create_mile = 0;
			if(!empty($data['create_milestone']) && $data['create_milestone'] == 1 ){
				$create_mile = 1;
			}
			$data['milestone_date'] = !empty($data['planned_date']) ? $data['planned_date'] : '';
			$data['has_milestone'] = 0;
			unset($data['planned_date']);
			if(empty($milestoneExist)){
				if($create_mile == 1){
					if(empty($data['initial_date']) && !empty($data['milestone_date'])){
						$data['initial_date'] = $data['milestone_date'];
					}
					if(empty($data['milestone_date']) && !empty($data['initial_date'])){
						$data['milestone_date'] = $data['initial_date'];
					}
					$listWeight = $this->ProjectMilestone->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'project_id' => $data['project_id']
						),
						'fields' => array('id', 'weight')
					));
					$data['weight'] = 0;
					if(!empty($listWeight)){
						$maxWeight = max($listWeight);
						$data['weight'] = $maxWeight + 1;
					}
				}else{
					$this->ZAuth->respond('failed', null, __('Can not create milestone', true), 'NOT_SAVED');
				}
			}else{
				$data['has_milestone'] = 1;
				$data['id'] = $milestoneExist['ProjectMilestone']['id'];
				if(empty($data['initial_date']) && !empty($data['milestone_date'])){
					$data['initial_date'] = $data['milestone_date'];
				}
				if(empty($data['milestone_date']) && !empty($data['initial_date'])){
					$data['milestone_date'] = $data['initial_date'];
				}
				if(empty($data['milestone_date'])){
					unset($data['milestone_date']);
				}
				if(empty($data['initial_date'])){
					unset($data['initial_date']);
				}
				if(empty($data['effective_date'])){
					$data['effective_date'] = $milestoneExist['ProjectMilestone']['effective_date'];
				}else{
					$data['effective_date'] = $this->ProjectMilestone->convertTime($data['effective_date']);
					$data['effective_date'] = strtotime($data['effective_date']);
				}
			}
			$data['created'] = time();
			$data['updated'] = time();
			$data['validated'] = $data['validated'];
			$data['milestone_date'] = !empty($data['milestone_date']) ? $this->ProjectMilestone->convertTime($data['milestone_date']) : '';
			$data['initial_date'] = !empty($data['initial_date']) ? $this->ProjectMilestone->convertTime($data['initial_date']) : '';
			$data['initial_date'] = !empty($data['initial_date']) ? strtotime($data['initial_date']) : '';
			if(empty($milestoneExist['ProjectMilestone']['effective_date']) && ($data['validated'] == 1)){
				$data['effective_date'] = $data['updated'];
			}
		}
		return $data;
	}
	private function validate_input_create_project($data = array()){
		$res = $msg = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		//key_to_lowser
		$data = array_combine( array_map( function($key){
			return strtolower( str_replace(' ', '_', $key));
		}, array_keys($data)), array_values( $data));
		
		// map key
		$map_keys = array(
			'project_code' => 'project_code_1',
			'program' => 'project_amr_program_id',
		);
		foreach( $map_keys as $k => $new_key){
			if(!empty($data[$k])) $data[$new_key] = $data[$k];
		}
		// debug( $data);
		$allowkeys = array('project_name', 'project_amr_program_id', 'project_manager', 'project_code_1');
		foreach( $data as $k => $v){
			if( !in_array( $k, $allowkeys)) unset( $data[$k]);
		}
		$notEmptyFields = array('project_name', 'project_amr_program_id', 'project_manager');
		foreach( $notEmptyFields as $i => $key ){
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'PR03'.( $i+1) );
		}
		foreach( $data as $key => $value){
			$value = is_string($value) ? trim(strip_tags($value)) : array_map( function($v){ return trim(strip_tags($v));}, $value);
			switch( $key){
				case 'project_name': // check not exists
					if( $key ==  'project_name') $err_code = 'PR001';
				case 'project_code_1': // check not exists
					if( $key ==  'project_code_1') $err_code = 'PR002';
					$model = 'Project';
					$vals = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							$key => $value,
							'company_id' => $company_id
						),
						'fields' => array('id') 
					));
					if( !empty( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__('Project with %s "%s" exists', true), $key, $value), $err_code);
					$res['Project'][$key] = $value;
				break;
				case 'project_amr_program_id': // check not exists
					$err_code = 'PR011';
					$model = 'ProjectAmrProgram';
					$conditions =  array(
						'company_id' => $company_id,
						'amr_program' => $value
					);
					$vals = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => $conditions,
						'fields' => array('id') 
					));
					if( empty( $vals) ) $this->ZAuth->respond('error', $value, sprintf(__('%s "%s" doesn\'t exist', true), $key, $value), $err_code);
					$res['Project'][$key] = $vals['ProjectAmrProgram']['id'];
				break;
				case 'project_manager': 
					/* check for project_manager
					 * is exists
					 * is available
					 * check role
					 */
					$err_code = 'PR013';
					$model = 'Employee';
					$availablePM =  $this->Employee->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'Employee.company_id' => $company_id,
							'CompanyEmployeeReference.role_id' => array( 2,3), // admin or PM
							'NOT' => array(
								// Remove employee with empty name
								'Employee.last_name' => 'NULL'
								
							),
							'OR' => array( // available
								'Employee.end_date is NULL',
								'Employee.end_date' => '0000-00-00',
								'Employee.end_date >' => date('Y-m-d'),
								
							),
							'Employee.actif' => 1, // available
							// 'Employee.fullname' => $value,
							
						),
						'fields' => array( 'Employee.fullname', 'Employee.id'),
						'joins' => array(
							array(
								'table' => 'company_employee_references',
								'alias' => 'CompanyEmployeeReference',
								'type' => 'inner',
								'conditions' => array('Employee.id = CompanyEmployeeReference.employee_id')
							),
						)
					));
					if( is_string($value)){
						$value = array($value);
					}
					foreach( $value as $fullname){
						if( !empty($availablePM[$fullname])){
							$res['ProjectEmployeeManager'][] = array(
								'project_manager_id' => $availablePM[$fullname],
								'is_backup' => 0,
								'type' => 'PM',
								'is_profit_center' => 0,
								'activity_id' => null,
							);
						}else{
							$this->ZAuth->respond('error', $value, sprintf(__('%s "%s" is not available', true), $key, $fullname), $err_code);
						}
					}
				break;
			}
		};
		
		/* Apply default data for project */
			$res['Project']['company_id'] = $company_id;
			$res['Project']['category'] = 2;
			$res['Project']['weather'] =  'sun';
			$res['Project']['update_by_employee'] = $user['first_name'] . ' ' . $user['last_name'];
			$res['Project']['rank'] = 'mid';
			$res['ProjectAmr']['weather'] = 'sun';
			$res['Project']['last_modified'] = time();
			$res['ProjectAmr']['cost_control_weather'] = 'sun';
			$res['ProjectAmr']['planning_weather'] = 'sun';
			$res['ProjectAmr']['risk_control_weather'] = 'sun';
			$res['ProjectAmr']['organization_weather'] = 'sun';
			$res['ProjectAmr']['perimeter_weather'] = 'sun';
			$res['ProjectAmr']['issue_control_weather'] = 'sun';
			
			// Default Phase
			$listPhaseDefault = $this->ProjectPhase->find('list',array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'add_when_create_project' => 1,
					'activated' => 1
				),
				'order' => 'ProjectPhase.phase_order ASC',
				'fields' => array('id')
			));
			if(!empty($listPhaseDefault)){
				$i = 1;
				foreach ($listPhaseDefault as $phase_id){
					$res['ProjectPhasePlan'][] = array(
						'project_planed_phase_id' => $phase_id,
						'progress' => 0,
						'weight' => $i++,
						'phase_planed_start_date' => '0000-00-00',
						'phase_real_start_date' => '0000-00-00',
						'phase_planed_end_date' => '0000-00-00',
						'phase_real_end_date' => '0000-00-00',
						'created' => time(),
						'updated' => time(),
						
					);
				}
			}
		/* END Apply default data for project */
		return $res;
		
	}
	public function create_project(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Project', 'ProjectEmployeeManager', 'ProjectAmrProgram', 'Employee', 'ProjectAmr', 'ProjectPhase', 'ProjectPhasePlan', 'ProjectTask');
		if( !$this->RequestHandler->isPost() || empty($this->data)){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		
		/* Update session */
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user['id'])));
		$employee_all_info = $employee_all_info[0];
		$employee_all_info["Employee"]["is_sas"] = 0;
		$this->employee_info = $employee_all_info;
		$this->Session->write('Auth.employee_info', $employee_all_info);
		
		$data = $this->validate_input_create_project($this->data);
		$this->Project->create();
		$result = $this->Project->save($data['Project']);
		if( empty( $result)) $this->ZAuth->respond('error', null, __('Cannot save project', true), 'NOT_SAVED');
		$project_id = $this->Project->id;
		if( empty($data['Project']['project_code_1'])){
			$this->Project->saveField('project_code_1', $project_id);
		}
		$assoc_data = $data;
		unset( $assoc_data['Project']);
		foreach( $assoc_data as $model => $model_data){
			if( empty( $this->$model)) $this->loadModel($model);
			$first_key = array_keys( $model_data);
			$first_key = $first_key[0];
			if( !is_integer( $first_key)){ 
				$model_data = array($model_data); // single item
			}
			// save multi items
			foreach( $model_data as $value){
				$value['project_id'] = $project_id;
				$this->$model->create();
				$this->$model->save($value);
			}			
		}
		$this->ProjectTask->staffingSystem($project_id);
		$log = 'Create new Project item `%s-$s` by %s use Web Services';
		$this->writeLog($data, array( 'Employee' => $user), sprintf($log, $project_id, $data['Project']['project_name'], $user["fullname"]), $company_id);
		
		//return data
		$this->Project->recursive = -1;
		$this->Project->Behaviors->attach('Containable');
		
		$return = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array( 'Project.id' => $project_id),
			'fields' => array('project_name', 'project_code_1'),
		));
		$return['Project']['program'] = $this->data['program'];
	
		$this->ZAuth->respond('success', $return);
		
	}
	protected function get_company_dataset($company_id){
		$this->loadModel('ProjectDataset');
		$datasets = array();
		$company_datasets = $this->ProjectDataset->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				// 'dataset_name' => $key,
				// 'name' => $value,
			),
			'order' => array('dataset_name'),
			'fields' => array('*')
		));
		foreach( $company_datasets as $k => $v){
			$v = $v['ProjectDataset'];
			$k = $v['dataset_name'];
			$n = strtolower($v['name']);
			$datasets[$k][$n] = $v['id'];
		}
		return $datasets;
	}
	protected function after_create_program($id, $company_id, $programName){
		$this->loadModels('ActivityFamily');
		$fams = $this->ActivityFamily->find('first', array(
            'recursive' => - 1,
            'conditions' => array(
				'company_id' => $company_id, 
				'name' => $programName, 
				'parent_id IS NULL'
			),
            'fields' => array('id')
        ));
		 $family_id = null;
		if(!empty($fams) && !empty($fams['ActivityFamily']['id'])){
            $family_id = $fams['ActivityFamily']['id'];
        } else {
            $saved = array(
                'name' => $programName,
                'company_id' => $company_id
            );
            $this->ActivityFamily->create();
            if($this->ActivityFamily->save($saved)){
                $family_id = $this->ActivityFamily->id;
            }
        }
		$this->ProjectAmrProgram->id = $id;
		$this->ProjectAmrProgram->saveField('family_id', $family_id);
	}
	protected function validate_input_update_project($data = array()){
		$assoc = $will_delete = $save = $input_assoc = array();
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];
		//key_to_lowser
		$data = array_combine( array_map( function($key){
			return strtolower( str_replace(' ', '_', $key));
		}, array_keys($data)), array_values( $data));
		// map key
		$map_keys = array(
			'0/1_1' => 'bool_1',
			'0/1_2' => 'bool_2',
			'0/1_3' => 'bool_3',
			'0/1_4' => 'bool_4',
			'yes/no_1' => 'yn_1',
			'yes/no_2' => 'yn_2',
			'yes/no_3' => 'yn_3',
			'yes/no_4' => 'yn_4',
			'yes/no_5' => 'yn_5',
			'yes/no_6' => 'yn_6',
			'yes/no_7' => 'yn_7',
			'yes/no_8' => 'yn_8',
			'yes/no_9' => 'yn_9',
			'budget_customer' => 'customer',
			'project_code' => 'project_code_1',
			'program' => 'project_amr_program',
			'implementation_complexity' => 'complexity',
			'implementation_complexity' => 'complexity',
			'implementation_complexity' => 'complexity',
		);
		foreach( $map_keys as $k => $new_key){
			if(!empty($data[$k])) $data[$new_key] = $data[$k];
		}
		
		$notEmptyFields = array('allow_new_list_values', 'update_or_replace');
		$i = 0;
		foreach( $notEmptyFields as $i => $key ){
			if( empty( $data[$key])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), $key),'PR03'.( $i+1) );
		}
		$project_id = '';
		$cond = array(
			'company_id' => $company_id
		);
		if( !empty( $data['id'])){
			$cond['id'] = $data['id'];
		}elseif( !empty($data['project_code_1'])){
			$cond['project_code_1'] = $data['project_code_1'];
		
		}else{
			if( empty( $data['project_name'])) $this->ZAuth->respond('error', null, sprintf(__('Missing field %1$s', true), 'ID or project_code or project_name'),'PR035' );
			$cond['project_name'] = $data['project_name'];
		}
		// map key again
		$map_keys = array(
			'project_name_new' => 'project_name',
			'project_code_new' => 'project_code_1',
		);
		foreach( $map_keys as $k => $new_key){
			if(!empty($data[$k])) $data[$new_key] = $data[$k];
		}
		$canCreateNew = in_array( strtolower($data['allow_new_list_values']), array('yes', 'y', 1)) ? 1 : 0;
		$isReplace = ($data['update_or_replace'] == 'replace') ? 1 : 0;
		$isUpdate = !$isReplace;
		$project = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => $cond,
			'fields' => array('id', 'project_name', 'project_type_id', 'project_sub_type_id', 'project_sub_sub_type_id', 'project_amr_program_id' ,'project_amr_sub_program_id', 'activity_id')
		));
		if( empty( $project)){
			if( empty( $project)) $this->ZAuth->respond('error', null, __('Project not found', true),'PR001' );
		}
		$project_id = $project['Project']['id'];
		$save['Project']['id'] = $project_id;
		$project_schema =  $this->Project->_schema;
		App::import("vendor", "str_utility");
		$str_utility = new str_utility();
		foreach( $data as $key => $value){
			$value = is_string($value) ? trim(strip_tags($value)) : ( is_array($value) ? array_map( function($v){ return trim(strip_tags($v));}, $value) : $value );
			$model = $conds = array();
			
			switch( $key){
				/* Project fields */
				case 'project_name':
					if( $value == '') { 
						$this->ZAuth->respond('error', $value, sprintf(__('"%s" can not be EMPTY', true), $key), 'EMPTY_PROJECT_NAME');
					}
				case 'address':
				case 'free_1':
				case 'free_2':
				case 'free_3':
				case 'free_4':
				case 'free_5':
				case 'issues':
				case 'address':
				case 'latlng':
				case 'primary_objectives':
				case 'project_objectives':
				case 'long_project_name':
				case 'remark':
				case 'constraint':
				case 'text_one_line_1':
				case 'text_one_line_2':
				case 'text_one_line_3':
				case 'text_one_line_4':
				case 'text_one_line_5':
				case 'text_one_line_6':
				case 'text_one_line_7':
				case 'text_one_line_8':
				case 'text_one_line_9':
				case 'text_one_line_10':
				case 'text_one_line_11':
				case 'text_one_line_12':
				case 'text_one_line_13':
				case 'text_one_line_14':
				case 'text_one_line_15':
				case 'text_one_line_16':
				case 'text_one_line_17':
				case 'text_one_line_18':
				case 'text_one_line_19':
				case 'text_one_line_20':
				case 'text_two_line_1':
				case 'text_two_line_2':
				case 'text_two_line_3':
				case 'text_two_line_4':
				case 'text_two_line_5':
				case 'text_two_line_6':
				case 'text_two_line_7':
				case 'text_two_line_8':
				case 'text_two_line_9':
				case 'text_two_line_10':
				case 'text_two_line_11':
				case 'text_two_line_12':
				case 'text_two_line_13':
				case 'text_two_line_14':
				case 'text_two_line_15':
				case 'text_two_line_16':
				case 'text_two_line_17':
				case 'text_two_line_18':
				case 'text_two_line_19':
				case 'text_two_line_20':
				case 'editor_1':
				case 'editor_2':
				case 'editor_3':
				case 'editor_4':
				case 'editor_5':
					if( empty( $project_schema[$key])){
						$this->ZAuth->respond('error', $value, sprintf(__('Can not validate for field "%s"', true), $key, $value), 'VALIDATE_FAILED');
					}
					$_schema = $project_schema[$key];
					if( (!empty($_schema['length'])) && (strlen( $value ) > $_schema['length']) ){
						$this->ZAuth->respond('error', $value, sprintf(__('Data for "%s" is longer than the limit %s characters', true), $key, $_schema['length']), 'VALIDATE_FAILED');
					}
					$save['Project'][$key] = $value;
				break;
				/* END Project fields */
				
				/* Project Manager */
				case 'project_manager':
				case 'read_access':
				case 'chief_business':
				case 'functional_leader':
				case 'uat_manager':
				case 'technical_manager':
					$model = 'ProjectEmployeeManager';
					$this->loadModels($model, 'ProfitCenter');
					$types = array(
						'project_manager' => 'PM',
						'read_access' => 'RA',
						'chief_business' => 'CB',
						'functional_leader' => 'FL',
						'uat_manager' => 'UM',
						'technical_manager' => 'TM',
					);
					if( empty ( $availablePM )){
						$input_assoc[] = $model;
						$availablepms =  $this->Employee->find('list', array(
							'recursive' => -1,
							'conditions' => array(
								'Employee.company_id' => $company_id,
								'CompanyEmployeeReference.role_id' => array( 2,3), // admin or PM
								'NOT' => array(
									// Remove employee with empty name
									'Employee.last_name' => 'NULL'
									
								),
								'OR' => array( // available
									'Employee.end_date is NULL',
									'Employee.end_date' => '0000-00-00',
									'Employee.end_date >' => date('Y-m-d'),
									
								),
								'Employee.actif' => 1, // available
								// 'Employee.fullname' => $value,
								
							),
							'fields' => array( 'Employee.fullname', 'Employee.id'),
							'joins' => array(
								array(
									'table' => 'company_employee_references',
									'alias' => 'CompanyEmployeeReference',
									'type' => 'inner',
									'conditions' => array('Employee.id = CompanyEmployeeReference.employee_id')
								),
							)
						));
						$pcs = $this->ProfitCenter->find('list', array(
							'recursive' => -1,
							'conditions' => array(
								'ProfitCenter.company_id' => $company_id,
							),
							'fields' => array( 'ProfitCenter.name', 'ProfitCenter.id'),
						));
						foreach( $availablepms as $k => $v){
							$availablePM[strtolower($k)] = $v;
						}
						foreach( $pcs as $k => $v){
							$availablePC[strtolower($k)] = $v;
						}
					}
					if( empty( $currentPMs)){
						$curentPMs = $this->$model->find('all', array(
							'recursive' => -1,
							'conditions' => array(
								'project_id' => $project_id,
								// 'is_profit_center' => 0,
							),
							'fields' => array($model.'.*', 'Employee.first_name', 'Employee.last_name', 'ProfitCenter.name'),
							'joins' => array(
								array(
									'table' => 'employees',
									'alias' => 'Employee',
									'type' => 'left',
									'conditions' => array('Employee.id = ' .$model.'.project_manager_id')
								),
								array(
									'table' => 'profit_centers',
									'alias' => 'ProfitCenter',
									'type' => 'left',
									'conditions' => array('ProfitCenter.id = ' .$model.'.project_manager_id')
								),
							)
						));
						if( !empty($curentPMs) ){
							$tmp = array();
							$nameCurPMs = array();
							foreach( $curentPMs as $pm){
								$pm[$model]['fullname'] = ($pm[$model]['is_profit_center'] ==0 ) ? strtolower($pm['Employee']['first_name'] . ' ' . $pm['Employee']['last_name']) : strtolower('pc/' . $pm['ProfitCenter']['name']);
								$pm = $pm[$model];
								$tmp[ $pm['type'] ][$pm['is_profit_center']][] = $pm;
								$nameCurPMs[ $pm['type'] ] [ $pm['fullname'] ] = $pm['id'];
							}
							$curentPMs = $tmp;
							unset($tmp);
							// debug( $nameCurPMs); exit;
							/* Output 
							$curentPMs = array( 'type' => array(is_pc => array(PM data)));
							
							*/
						}
						
					}
					if( is_string($value)){
						$value = array($value);
					}
					//strtolower
					foreach( $value as $k => $fullname){
						$value[$k] = preg_replace( '/^pc(\s){0,}\/(\s){0,}/i', 'pc/', strtolower($fullname));
					}
					foreach( $value as $fullname){
						// debug( $fullname);
						// debug( $nameCurPMs[$types[$key]]);
						if( empty( $fullname)){
							if( $types[$key] == 'PM'){
								$this->ZAuth->respond('error', $value, sprintf(__('%s can not be empty', true), $key), "EMPTY_PROJECT_MANAGER");
							}else{
								continue;
							}
						}
						if( empty( $nameCurPMs[$types[$key]][$fullname] )){ // khong co thi them vao
							if( ($key == 'read_access') && preg_match('/^pc(\s){0,}\/(\s){0,}/i', $fullname, $matches)){ // isPC
								$pcName = strtolower(str_replace( $matches[0], '', $fullname));
								if( !empty($availablePC[$pcName])){
									$assoc['ProjectEmployeeManager'][] = array(
										'project_manager_id' => $availablePC[$pcName],
										'is_backup' => 0,
										'project_id' => $project_id,
										'type' => $types[$key],
										'is_profit_center' => 1,
										'activity_id' => $project['Project']['activity_id'],
									);
								}else{
									$this->ZAuth->respond('error', $value, sprintf(__('%s "%s" is not available', true), 'Profit Center', $fullname), "PROFIT_CENTER_NOT_FOUND");
								}
							}else{ // is Employee
								if( !empty($availablePM[$fullname])){
									$assoc['ProjectEmployeeManager'][] = array(
										'project_id' => $project_id,
										'project_manager_id' => $availablePM[$fullname],
										'is_backup' => 0,
										'type' => $types[$key],
										'is_profit_center' => 0,
										'activity_id' => $project['Project']['activity_id'],
									);
								}else{
									$this->ZAuth->respond('error', $value, sprintf(__('%s "%s" is not available', true), $key, $fullname), "EMPLOYEE_NOT_FOUND");
								}
							}
						}
					}
					if( $isReplace && !empty($nameCurPMs[$types[$key]]) ){ //co roi thi xoa di
						foreach ( $nameCurPMs[$types[$key]] as $name => $id) {
							if( !in_array($name, $value)) $will_delete['ProjectEmployeeManager'][] = $id;
						}
					}
					
					
				break;
				/* END Project Manager fields */
				
				/* Select fields*/
				case 'list_1':
				case 'list_2':
				case 'list_3':
				case 'list_4':
				case 'list_5':
				case 'list_6':
				case 'list_7':
				case 'list_8':
				case 'list_9':
				case 'list_10':
				case 'list_11':
				case 'list_12':
				case 'list_13':
				case 'list_14':
					if( empty( $model)) {
						$model = 'ProjectDataset';
						$conds = array(
							'company_id' => $company_id,
							'dataset_name' => $key,
						);
					}
				case 'project_type':
					if( empty( $model)) {
						$key = $key.'_id';
						$model = 'ProjectType';
						$input_assoc[] = $model;
						$conds = array(
							'company_id' => $company_id,
							'display' => 1,
						);
					}
				// case 'project_sub_type_id':
				// case 'project_sub_sub_type_id':
					// if( empty( $model)) {
						// $model = 'ProjectSubType';
						// $conds = array(
							// 'display' => 1,
						// );
					// }
				case 'complexity':
					if( empty( $model)) {
						$key = $key.'_id';
						$model = 'ProjectComplexity';
						$input_assoc[] = $model;
						$conds = array(
							'company_id' => $company_id,
							'display' => 1,
						);
					}
				case 'budget_customer':
					if( empty( $model)) {
						$key = $key.'_id';
						$model = 'BudgetCustomer';
						$input_assoc[] = $model;
						$conds = array(
							'company_id' => $company_id,
						);
					}
				case 'project_priority':
					if( empty( $model)) {
						$key = $key.'_id';
						$model = 'ProjectPriority';
						$input_assoc[] = $model;
						$conds = array(
							'company_id' => $company_id,
						);
					}
				case 'project_amr_program':
					if( empty( $model)) {
						$key = $key.'_id';
						$model = 'ProjectAmrProgram';
						$input_assoc[] = $model;
						$conds = array(
							'company_id' => $company_id,
						);
					}
				// case 'project_amr_sub_program_id':
					// if( empty( $model)) {
						// $model = 'ProjectAmrSubProgram';
						// $conds = array(
							// 'company_id' => $company_id,
						// );
					// }
				case 'project_status':
					if( empty( $model)) {
						$key = $key.'_id';
						$model = 'ProjectStatus';
						$input_assoc[] = $model;
						$conds = array(
							'company_id' => $company_id,
						);
					}
				case 'profit_center':
				case 'team':
					if( empty( $model)) {
						$key = 'team';
						$model = 'ProfitCenter';
						$conds = array(
							'company_id' => $company_id,
						);
					}
					$this->loadModel($model);
					$displayField = $this->$model->displayField;
					$conds[$displayField] = $value;
					$$key = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => $conds,
						'fields' => array('*')
					));
					if( empty( $$key)) {
						if( !$canCreateNew){
							$this->ZAuth->respond('error', $value, sprintf(__('"%s" with value is not exists', true), $key, $value), strtoupper( str_replace(' ', '_', $key)) . '_NOT_FOUND');
						}else{
							$this->$model->create();
							$this->$model->save(array(
								'company_id' =>  $company_id,
								$displayField =>  $value,
								'dataset_name' => $key,
							));
							if( $model == 'ProjectAmrProgram') $this->after_create_program($this->$model->id, $company_id, $value);
							$save['Project'][$key] = $this->$model->id;
						}
					}else{
						$assoc_data = $$key;
						$save['Project'][$key] = $assoc_data[$model]['id'];
						unset( $assoc_data);
					}					
				break;
				/* END Select fiels */
				
				/* Multi-Select fields*/
				case 'list_muti_1':
				case 'list_muti_2':
				case 'list_muti_3':
				case 'list_muti_4':
				case 'list_muti_5':
				case 'list_muti_6':
				case 'list_muti_7':
				case 'list_muti_8':
				case 'list_muti_9':
				case 'list_muti_10':
					$model = 'ProjectDataset';
					$this->loadModel($model);
					if( empty( $projectListMultiData)){
						$this->loadModel('ProjectListMultiple');
						$projectListMultiData = $this->ProjectListMultiple->find('all', array(
							'recursive' => -1,
							'conditions' => array('project_id' => $project_id),
							'fields' => array('*')
						));
						$projectListMultiData = !empty( $projectListMultiData) ? Set::combine($projectListMultiData, '{n}.ProjectListMultiple.id', '{n}.ProjectListMultiple.project_dataset_id',  '{n}.ProjectListMultiple.key') : array();					
					}
					if(empty( $datasets )){
						$datasets = $this->get_company_dataset($company_id);
					}
					if( is_string($value)){
						$value = array($value);
					}
					
					$k = 'project_' . str_replace('muti', 'multi', $key);
					$currentList = !empty($projectListMultiData[$k]) ? $projectListMultiData[$k] : array(); // project data
					// debug( $currentList); exit;
					$dataset = $datasets[$key]; // company_data
					foreach( $value as $i => $name){
						if(empty( $dataset[strtolower($name)]) && $canCreateNew){ // Create new then assign
							$this->$model->create();
							$this->$model->save( array(
								'company_id' => $company_id,
								'dataset_name' => $key,
								'name' => $name,
								'display' => 1,
							));
							$dataset[strtolower($name)] = $this->$model->id;
						}
						$name = strtolower($name);
						if(empty( $dataset[$name]) && !$canCreateNew){
							$this->ZAuth->respond('error', $value, sprintf(__('"%s" with value "%s" is not exists', true), $key, $name), strtoupper( str_replace(' ', '_', $key)) . '_NOT_FOUND');
						}
						$project_dataset_id = $dataset[$name];
						$project_list_multi_id = array_search($project_dataset_id, $currentList);
						if( $project_list_multi_id === false){
							$assoc['ProjectListMultiple'][] = array(
								'project_id' => $project_id,
								'project_dataset_id' => $project_dataset_id,
								'key' => $key,
							);
						}
						$value[$i] = $name; //lowercase
					}
					if( $isReplace && !empty($currentList) ){ //co roi thi xoa di
						foreach ( $currentList as $project_list_multi_id => $project_dataset_id){
							$name = array_search($project_dataset_id, $dataset);
							if( !in_array($name, $value)) $will_delete['ProjectListMultiple'][] = $project_list_multi_id;
						}
					}
				break;
				case 'current_phases':
					$model = 'ProjectPhase';
					$this->loadModels('ProjectPhaseCurrent', $model );
					$_phases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC'));
					if( empty( $_phases)) $this->ZAuth->respond('error', $value, sprintf(__('Company has no phases', true), $key, $name), strtoupper( str_replace(' ', '_', $key)) . '_NOT_FOUND');
					
					foreach( $_phases as $_id => $_name){
						$companyPhases[ strtolower($_name) ] = $_id;
					}
					if( is_string($value)){
						$value = array($value);
					}
					$currentPhases = $this->ProjectPhaseCurrent->find('list', array(
						'recursive' => -1,
						'conditions' => array('project_id' => $project_id),
						'fields' => array('id', 'project_phase_id')
					));
					$max_phase_order = $this->ProjectPhase->find('first', array(
						'recursive' => -1,
						'conditions' => array('company_id' => $company_id),
						'fields' => array('id', 'phase_order'),
						'order' => array('phase_order desc')
					));
					$max_phase_order = (!empty($max_phase_order)) ? $max_phase_order['ProjectPhase']['phase_order'] : 0;
					foreach( $value as $i => $name){
						if(empty( $companyPhases[strtolower($name)])){
							if($canCreateNew){ // Create new then assign
								$this->$model->create();
								$this->$model->save( array(
									'company_id' => $company_id,
									'name' => $name,
									'display' => 1,
									'color' => '#004280', // default color
									'phase_order' => $max_phase_order++, // default color
								));
								$companyPhases[strtolower($name)] = $this->$model->id;
							}else{
								$this->ZAuth->respond('error', $value, sprintf(__('"%s" with value "%s" is not exists', true), $key, $name), strtoupper( str_replace(' ', '_', $key)) . '_NOT_FOUND');
							}
						}
						$name = strtolower($name);
						$phase_id = $companyPhases[$name];
						$phase_curr_id = array_search($phase_id, $currentPhases);
						if( $phase_curr_id === false){
							$assoc['ProjectPhaseCurrent'][] = array(
								'project_id' => $project_id,
								'project_phase_id' => $phase_id,
							);
						}
						$value[$i] = $name; //lowercase
					}
					if( $isReplace && !empty($currentPhases) ){ //co roi thi xoa di
						foreach ( $currentPhases as $phase_curr_id => $phase_id){
							$name = array_search($phase_id, $companyPhases);
							if( !in_array($name, $value)) $will_delete['ProjectPhaseCurrent'][] = $phase_curr_id;
						}
					}
				break;
				/* END Multi-Select fiels */
				
				/* Number fields  decimal(15,2) */
				case 'price_1':
				case 'price_2':
				case 'price_3':
				case 'price_4':
				case 'price_5':
				case 'price_6':
				case 'number_1':
				case 'number_2':
				case 'number_3':
				case 'number_4':
				case 'number_5':
				case 'number_6':
				case 'number_7':
				case 'number_8':
				case 'number_9':
				case 'number_10':
				case 'number_11':
				case 'number_12':
				case 'number_13':
				case 'number_14':
				case 'number_15':
				case 'number_16':
				case 'number_17':
				case 'number_18':
					if( $value == '') { $save['Project'][$key] = 0; break; }
					$_schema = $project_schema[$key];
					$len = (int)$_schema['length'];
					if( !is_numeric($value) || ($value >= '10e'.$len)){
						$this->ZAuth->respond('error', $value, sprintf(__('Data for field "%s" requires "%s(%s)" format', true), $key, $_schema['type'], $_schema['length']), 'ERROR_NUMBER_FORMAT');
					}
					$save['Project'][$key] = $value;
				break;
				/* END Number fiels */
				
				/* Unique fields*/
				case 'project_code':
				case 'project_code_1':
					$key = 'project_code_1';
					if( $value == '') { $save['Project'][$key] = $value; break; }
					$check_code = $projectCode = $this->Project->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'project_code_1' => $value,
							'not' => array( 'id' => $project_id),
							'project_code_1 IS NOT NULL'							
						),
						'fields' => array('project_name')
					));
					if (!empty($projectCode)) {
						$this->ZAuth->respond('error', $value, sprintf(__('%s is exists', true), $key), 'PROJECT_CODE_EXISTS');
					}
					$save['Project'][$key] = $value;
				break;
				/* END Unique fiels */
				
				
				/* Boolean fields 0/1 */
				case 'bool_1':
				case 'bool_2':
				case 'bool_3':
				case 'bool_4':
				case 'yn_1':
				case 'yn_2':
				case 'yn_3':
				case 'yn_4':
				case 'yn_5':
				case 'yn_6':
				case 'yn_7':
				case 'yn_8':
				case 'yn_9':
					if( $value == '') { $save['Project'][$key] = 0; break; }
					$allow_values = array('yes', 'y', 1, 'no', 'n', 0);
					if( !in_array( strtolower($value), $allow_values)){
						$this->ZAuth->respond('error', $value, sprintf(__('Value for %s is not allow', true), $key), 'DATA_INCORRECT');
					}
					$value = in_array( strtolower($value), array('yes', 'y', 1)) ? 1 : 0;
					$save['Project'][$key] = $value;
				break;
				/* END Boolean fiels */
				
				/* Date fields '0000-00-00' */ 
				case 'date_1':
				case 'date_2':
				case 'date_3':
				case 'date_4':
				case 'date_5':
				case 'date_6':
				case 'date_7':
				case 'date_8':
				case 'date_9':
				case 'date_10':
				case 'date_11':
				case 'date_12':
				case 'date_13':
				case 'date_14':
					if( $value == '') { $save['Project'][$key] = $value; break; }
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('%1$s format DD-MM-YYYY', true), $key), 'ERROR_DATE_FORMAT');
					$save['Project'][$key] = $this->Project->convertTime($value);	
				break;
				/* END Date fiels */
				
				/* Date MMYY fields '00-0000' */ 
				case 'date_mm_yy_1':
				case 'date_mm_yy_2':
				case 'date_mm_yy_3':
				case 'date_mm_yy_4':
				case 'date_mm_yy_5':
					if( $value == '') { $save['Project'][$key] = $value; break; }
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					$m_y_pattern = '/^(1[0-2]|0[1-9]|\d)(-)(20\d{2}|19\d{2})$/';
					
					if( preg_match($m_y_pattern, $value, $matches)){
						$save['Project'][$key] = $value;
					}elseif(preg_match($date_pattern, $value, $matches)){
						$value = $str_utility->convertToMMYY($value);
					}else{
						$this->ZAuth->respond('error', $value, sprintf(__('%1$s format DD-MM-YYYY or MM-YYYY is required', true), $key), 'ERROR_MM_YYYY_FORMAT');
					}
					$save['Project'][$key] = $value;
				break;
				/* END Date fiels */
				
				/* Date yy fields '0000' */ 
				case 'date_yy_1':
				case 'date_yy_2':
				case 'date_yy_3':
				case 'date_yy_4':
				case 'date_yy_5':
					if( $value == '') { $save['Project'][$key] = $value; break; }
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					$yyyy_pattern = '/^(20\d{2}|19\d{2})$/';
					if( preg_match($yyyy_pattern, $value, $matches)){
						$save['Project'][$key] = $value;
					}elseif(preg_match($date_pattern, $value, $matches)){
						$value = $str_utility->convertToYYYY($value);
					}else{
						$this->ZAuth->respond('error', $value, sprintf(__('%1$s format DD-MM-YYYY or YYYY is required', true), $key), 'ERROR_YYYY_FORMAT');
					}
					$save['Project'][$key] = $value;
				break;
				/* END Date fiels */
				
				/* Other fields*/
				/* END Other fiels */
			}
		}
		if( isset($data['sub_program'])){
			if( empty($data['sub_program'])){
				$save['Project']['project_amr_sub_program_id'] = null;
			}else{
				$cur_program = isset( $save['Project']['project_amr_program_id'] ) ? $save['Project']['project_amr_program_id'] : $project['Project']['project_amr_program_id'];
				if( empty($cur_amr)) $this->ZAuth->respond('error', $data['sub_program'], sprintf(__('Error when save sub_program. No sub_program found', true), $key), 'EMPTY_PROJECT_PROGRAM');
				$this->loadModels('ProjectAmrSubProgram', 'ProjectAmrProgram', 'ActivityFamily');
				$sub_program = $this->ProjectAmrSubProgram->find('first', array(
					'recursive' => -1,
					'conditions' => array('amr_sub_program' => $data['sub_program']),
					// 'project_amr_program_id' => $cur_program,
				));
				if( !empty( $sub_program ) && $sub_program['ProjectAmrSubProgram']['project_amr_program_id'] != $sub_program){
					$this->ZAuth->respond('error', $data['sub_program'], sprintf(__('Sub program "%s" does not belong to current program', true), $data['sub_program']), 'SUB_PROGRAM_NOT_FOUND');
				}
				if( empty($sub_program)){
					if($canCreateNew){ // Create new then assign
						$this->ProjectAmrSubProgram->create();
						$this->ProjectAmrSubProgram->save( array(
							'amr_sub_program' => $data['sub_program'],
							'project_amr_program_id' => $cur_program,
							'sub_family_id' => '',
							'create' => time(),
							'updated' => time()
						));
						$new_id = $this->ProjectAmrSubProgram->id;
						$save['Project']['project_amr_sub_program_id'] = $new_id;
						$linkedFamily = $this->ProjectAmrProgram->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'id' => $cur_program,
							),
							'fields' => array('family_id')
						));
						$linkedFamily = !empty($linkedFamily) ? $linkedFamily['ProjectAmrProgram']['family_id'] : '';
						$subFamily = $this->ActivityFamily->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'name' => $data['sub_program'],
							),
							'fields' =>array('id', 'parent_id')
						));
						if(!empty($subFamily)){
							$sub_family_id = $subFamily['ActivityFamily']['id'];
						}else{
							if(!empty($linkedFamily)){
								$saved = array(
									'name' => $data['sub_program'],
									'company_id' => $company_id,
									'parent_id' => $linkedFamily,
								);
								$this->ActivityFamily->create();
								if ($this->ActivityFamily->save($saved)) {
									 $sub_family_id = $this->ActivityFamily->id;
								}
							}
						}
						if( !empty($sub_family_id)){
							$this->ProjectAmrSubProgram->id = $sub_program_id;
							$this->ProjectAmrSubProgram->saveField('sub_family_id', $sub_family_id);
						}
						
					}else{
						$this->ZAuth->respond('error', $data['sub_program'], sprintf(__('"%s" with value "%s" is not exists', true), 'sub_program', $data['sub_program']), 'SUB_PROGRAM_NOT_FOUND');
					}
				}else{
					$save['Project']['project_amr_sub_program_id'] = $sub_program['ProjectAmrSubProgram']['id'];
				}
			}
		}
		if( isset($data['project_sub_type'])){
			if( empty($data['project_sub_type'])){
				$save['Project']['project_sub_type_id'] = null;
			}else{
				$cur_type = isset( $save['Project']['project_type_id'] ) ? $save['Project']['project_type_id'] : $project['Project']['project_type_id'];
				if( empty($cur_type)) $this->ZAuth->respond('error', $data['project_sub_type'], sprintf(__('Error when save sub_type. No Project type found', true), $key), 'EMPTY_PROJECT_TYPE');
				$this->loadModels('ProjectSubType');
				$sub_type = $this->ProjectSubType->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_sub_type' => $data['project_sub_type']
					),
				));
				if( !empty( $sub_type ) && $sub_type['ProjectSubType']['project_type_id'] != $cur_type){
					$this->ZAuth->respond('error', $data['project_sub_type'], sprintf(__('Sub type "%s" does not belong to current type', true), $data['project_sub_type']), 'SUB_TYPE_NOT_FOUND');
				}
				if( empty($sub_type)){
					if($canCreateNew){ // Create new then assign
						$this->ProjectSubType->create();
						$this->ProjectSubType->save( array(
							'project_sub_type' => $data['project_sub_type'],
							'project_type_id' => $cur_type,
							'parent_id' => '',
							'display' => 1,
							'create' => time(),
							'updated' => time()
						));
						$new_id = $this->ProjectSubType->id;
						$save['Project']['project_sub_type_id'] = $new_id;
						
					}else{
						$this->ZAuth->respond('error', $data['project_sub_type'], sprintf(__('"%s" with value "%s" is not exists', true), 'sub_type', $data['project_sub_type']), 'SUB_TYPE_NOT_FOUND');
					}
				}else{
					$save['Project']['project_sub_type_id'] = $sub_type['ProjectSubType']['id'];
				}
			}
		}
		if( isset($data['project_sub_sub_type'])){
			if( empty($data['project_sub_sub_type'])){
				$save['Project']['project_sub_sub_type_id'] = null;
			}else{
				$cur_sub_type = isset( $save['Project']['project_sub_type_id'] ) ? $save['Project']['project_sub_type_id'] : $project['Project']['project_sub_type_id'];
				if( empty($cur_sub_type)) $this->ZAuth->respond('error', $data['project_sub_sub_type'], sprintf(__('Error when save sub_sub_type. No Project Sub type found', true), $key), 'EMPTY_PROJECT_SUB_TYPE');
				$this->loadModels('ProjectSubType');
				$sub_sub_type = $this->ProjectSubType->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'project_sub_type' => $data['project_sub_sub_type']
					),
				));
				
				if( !empty( $sub_sub_type ) && $sub_sub_type['ProjectSubType']['parent_id'] != $cur_sub_type){
					$this->ZAuth->respond('error', $data['project_sub_sub_type'], sprintf(__('Sub sub type "%s" does not belong to current Sub type', true), $data['project_sub_sub_type']), 'SUB_SUB_TYPE_NOT_FOUND');
				}
				if( empty($sub_sub_type)){
					if($canCreateNew){ // Create new then assign
						$this->ProjectSubType->create();
						$this->ProjectSubType->save( array(
							'project_sub_type' => $data['project_sub_sub_type'],
							'parent_id' => $cur_sub_type,
							'display' => 1,
							'create' => time(),
							'updated' => time()
						));
						$new_id = $this->ProjectSubType->id;
						$save['Project']['project_sub_sub_type_id'] = $new_id;
					}else{
						$this->ZAuth->respond('error', $data['project_sub_sub_type'], sprintf(__('"%s" with value "%s" is not exists', true), 'sub_type', $data['project_sub_type']), 'SUB_TYPE_NOT_FOUND');
					}
				}else{
					$save['Project']['project_sub_type_id'] = $sub_sub_type['ProjectSubType']['id'];
				}
			}
		}
		$save['Project']['last_modified'] = time();
		$save['Project']['update_by_employee'] = $user['fullname'];
		$this->Project->set( $save);
		if( !$this->Project->validates()){
			$this->ZAuth->respond('error', $save, __($this->Project->validationErrors, true), 'VALIDATE_FAILED');
		}
		return compact( 'assoc', 'will_delete', 'save', 'input_assoc');
		
	}
	public function update_project(){
		$result = array();
		$message = '';
		$return = array();
		$this->loadModels('Project', 'ProjectEmployeeManager', 'ProjectAmrProgram', 'Employee', 'ProjectAmr', 'ProjectPhase', 'ProjectPhasePlan', 'ProjectTask');
		if( !$this->RequestHandler->isPost() || empty($this->data)){
			$this->ZAuth->respond('data_empty', null, __('Empty data'),'DATA_EMPTY');
		}
		$user = $this->ZAuth->user();
		$user_id = $user['id'];
		$company_id = $user['company_id'];
		/* Update session */
		$employee_all_info = $this->Employee->CompanyEmployeeReference->find("all", array("conditions" => array("Employee.id" => $user['id'])));
		$employee_all_info = $employee_all_info[0];
		$employee_all_info["Employee"]["is_sas"] = 0;
		$this->employee_info = $employee_all_info;
		$this->Session->write('Auth.employee_info', $employee_all_info);
		
		$data = $this->validate_input_update_project($this->data);
	
		$result = $this->Project->save($data['save']);
		if( empty( $result)) $this->ZAuth->respond('error', null, __('Cannot save project', true), 'NOT_SAVED');
		$project_id = $this->Project->id;
		$assoc_data = $data['assoc'];
		foreach( $assoc_data as $model => $model_data){
			if( empty( $this->$model)) $this->loadModel($model);
			
			foreach( $model_data as $value){
				if( empty( $value['id']))
					$this->$model->create();
				else{
					$this->$model->id = $value['id'];
				}
				$this->$model->save($value);
			}			
		}
		foreach( $data['will_delete'] as $model => $IDs){
			if( empty( $this->$model)) $this->loadModel($model);
			$this->$model->deleteAll(array(
				"$model.id" => $IDs
			), false, false);		
		}
		
		$this->ProjectTask->staffingSystem($project_id);
		$log = 'Update Project item `%s-$s` by %s use Web Services';
		
		//Get project for return data
		$this->Project->recursive = -1;
		$this->Project->Behaviors->attach('Containable');
		$return = $this->Project->find('first', array(
			'conditions' => array( 'Project.id' => $project_id),
			'contain' => $data['input_assoc'],
		));
		$this->writeLog($data['save'], array( 'Employee' => $user), sprintf($log, $project_id, $return['Project']['project_name'], $user["fullname"]), $company_id);
		
		// Chi return nhung dong da input
		if( !empty($return['Project'])){
			foreach( $return['Project'] as  $k => $v){
				if( !isset($data['save']['Project'][$k]) ) unset( $return['Project'][$k]);
			}
		}
		$this->ZAuth->respond('success', $return);
		
	}
}