<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class MyAssistantsController extends AppController {
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Html');

    function beforeFilter() {
        parent::beforeFilter('getMyAssistant', 'editFollow');
    }
    /**
     * index
     *
     * @return void
     * @access public
     */
    function getMyAssistant(){
        $this->loadModels('Project', 'ProjectTask', 'ProjectStatus', 'ActivityRequest', 'ProjectEmployeeManager', 'AbsenceRequest', 'ProfitCenter', 'ProfitCenterManagerBackup', 'Employee', 'ProjectTaskEmployeeRefer', 'Menu');
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $profit_center_id = $this->employee_info['Employee']['profit_center_id'];
        $roles = $this->employee_info['Role']['name'];
        $list_follow_assistants = $this->MyAssistant->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id
            )
        ));
        $manager = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));
        $manager_backup = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id
            ),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $list_profit_center_managers = array_unique(array_merge($manager, $manager_backup));
        $manager = (!empty($manager) || !empty($manager_backup)) ? 1 : 0;
        $list_employee_of_team_manager = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'profit_center_id' => $list_profit_center_managers
            ),
            'fields' => array('id')
        ));
        $list_employee_of_team = $list_employee_of_team_manager;
        $list_employee_of_team_manager[$employee_id] = $employee_id;
        $list_project = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => 1
            ),
            'fields' => array('id', 'id')
        ));
        $list_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status !=' => 'CL',
                'display' => 1
            ),
            'fields' => array('id', 'id')
        ));
        $datas = array();
		$tasksHasChild = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => array_values($list_project),
				'parent_id >' => 0,
			),
			'fields' => array('parent_id', 'parent_id'),
			'group' => array('parent_id')
		));
		$tasksHasChild = !empty($tasksHasChild) ? array_values($tasksHasChild) : array();
        // My task.
        $task_in_progress = $this->ProjectTask->find('list', array(
            'conditions' => array(
                'ProjectTask.project_id' => $list_project,
                'OR' => array(
                    'ProjectTask.task_status_id' => $list_status,
                    'ProjectTask.task_status_id IS NULL'
                )
            ),
            'joins' => array(
                array(
                    'table' => 'project_task_employee_refers',
                    'alias' => 'TaskRefer',
                    'conditions' => array(
                        'TaskRefer.project_task_id = ProjectTask.id',
                        'TaskRefer.reference_id' => $employee_id,
                        'TaskRefer.is_profit_center' => 0
                    )
                )
            ),
			'fields' => array('ProjectTask.id', 'ProjectTask.id')
        ));
        $task_late = $this->ProjectTask->find('list', array(

            'conditions' => array(
                'ProjectTask.project_id' => $list_project,
                'OR' => array(
                    'ProjectTask.task_status_id' => $list_status,
                    'ProjectTask.task_status_id IS NULL'
                ),
				'NOT' => array('ProjectTask.task_end_date' => '0000-00-00'),
                'ProjectTask.task_end_date <=' => date('Y-m-d')
            ),
            'joins' => array(
                array(
                    'table' => 'project_task_employee_refers',
                    'alias' => 'TaskRefer',
                    'conditions' => array(
                        'TaskRefer.project_task_id = ProjectTask.id',
                        'TaskRefer.reference_id' => $employee_id,
                        'TaskRefer.is_profit_center' => 0
                    )
                )
            ),
			'fields' => array('ProjectTask.id', 'ProjectTask.id')
        ));
        $task_overload = $this->ProjectTask->find('list', array(
            'conditions' => array(
                'ProjectTask.overload >' => 0
            ),
            'joins' => array(
                array(
                    'table' => 'project_task_employee_refers',
                    'alias' => 'TaskRefer',
                    'conditions' => array(
                        'TaskRefer.project_task_id = ProjectTask.id',
                        'TaskRefer.reference_id' => $employee_id,
                        'TaskRefer.is_profit_center' => 0
                    )
                ),
				array(
                    'table' => 'project_statuses',
                    'alias' => 'StatusRefer',
                    'conditions' => array(
                        'StatusRefer.id = ProjectTask.task_status_id',
                        'StatusRefer.status' => 'IP'
                    )
                )
            ),
			'fields' => array('ProjectTask.id', 'ProjectTask.id')
        ));
		
		// Xóa các task cha
		$vars = array('task_in_progress', 'task_late', 'task_overload');
		foreach( $vars as $variable){
			if( !empty( $$variable)) {
				foreach ( $$variable as  $k => $task_id){
					if( in_array($task_id, $tasksHasChild)){ $v = $$variable ;unset( $v[$k]); $$variable = $v;}
				}
			}
		}
        $datas['my_task'] = array(
            'in_progress' => array(
                'number' => count($task_in_progress),
                'follow' => !empty($list_follow_assistants['MyAssistant']['task_in_progress']) ? $list_follow_assistants['MyAssistant']['task_in_progress'] : 0
            ),
            'late' => array(
                'number' => count($task_late),
                'follow' => !empty($list_follow_assistants['MyAssistant']['task_late']) ? $list_follow_assistants['MyAssistant']['task_late'] : 0
            ),
            'overload' => array(
                'number' => count($task_overload),
                'follow' => !empty($list_follow_assistants['MyAssistant']['task_overload']) ? $list_follow_assistants['MyAssistant']['task_overload'] : 0
            )
        );
        //My time sheet.
		$enableRMS = $this->Session->read('enableRMS');
		/* Huynh Modified 17-08-2019 
		#439
		* Chỗ này cần kiểm tra 2 điều kiện  [$is_sas || (!$is_sas && $enableRMS == true)] nhưng mà SAS không sử dụng popup assitant nên k cần check 
		*/
		if($enableRMS){
			$emp_info = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'id' => $employee_id),
                'fields' => array('id','end_date','start_date')
            ));
            $emp_end = !empty($emp_info) ? Set::combine($emp_info, '{n}.Employee.id', '{n}.Employee.end_date') : array();
            $emp_start = !empty($emp_info) ? Set::combine($emp_info, '{n}.Employee.id', '{n}.Employee.start_date') : array();
			$year = date('Y', time());
            $dto = new DateTime();
            $dto->setISODate($year, 1);
            $dto->setTime(0, 0, 0);
            $dto->modify('monday this week');
            $start = $dto->getTimestamp();
            $end = (strtolower(date('l')) == 'sunday') ? time() : strtotime('last sunday', time());
			$this->loadModels('ActivityRequestConfirm');
            $this->ActivityRequestConfirm->virtualFields['year'] = 'FROM_UNIXTIME(ROUND((start+end)/2), "%Y")';
            $this->ActivityRequestConfirm->virtualFields['week'] = 'WEEKOFYEAR(FROM_UNIXTIME(ROUND((start+end)/2)))';
            $emp_confirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'company_id' => $company_id,
                    'year' => $year
                ),
                'order' => array('start')
            ));
            $confirms_request = array();
            if(!empty($emp_confirms)){
                foreach($emp_confirms as $_confirm){
                    $dx = $_confirm['ActivityRequestConfirm'];
                    $weekOfYear = $dx['year'] . '-' . $dx['week'];
                    if(!isset($confirms_request[$dx['employee_id']][$dx['week']])){
                        $confirms_request[$dx['employee_id']][$dx['week']] = array();
                    }
                    $confirms_request[$dx['employee_id']][$dx['week']] = $dx['status'];
                }
            }
			$_data =array();
            while($start <= $end){
                $monday = (strtolower(date('l', $start)) == 'monday') ? $start : strtotime('last monday', $start);
                $sunday = (strtolower(date('l', $start)) == 'sunday') ? $start : strtotime('next sunday', $start);
                $AVGDay = ($monday+$sunday)/2;
                $week = intval(date('W', $AVGDay));
                // foreach($employee_id as $id){
                    $employee_endday = (!empty( $emp_end[$employee_id]) && ($emp_end[$employee_id] != '0000-00-00')) ? strtotime($emp_end[$employee_id]) : 0;
                    $employee_startday = (!empty( $emp_start[$employee_id]) && ($emp_start[$employee_id] != '0000-00-00')) ? strtotime($emp_start[$employee_id]) : 0;
                    if(($employee_endday >= $start || $employee_endday == 0) && ($employee_startday < $sunday || $employee_startday == 0) ){
                        if(isset($confirms_request[$employee_id][$week])){
                            $statusConfirm = $confirms_request[$employee_id][$week];
                        } else {
                            $statusConfirm = -1;
                        }
                        if($statusConfirm == 2 || $statusConfirm == 0){
                            // ko xu ly
                        } else {
                            $_data[$week][$employee_id] = $employee_id;
                        }
                    }
                // }
                $start = strtotime('next monday', $start);
            }
            $time_sheet_late = count($_data);
			$week_lates = array_keys($_data);
			$first = !empty($week_lates) ? $week_lates[0] : 0;
			$datas['my_timesheet'] = array(
				'late' => array(
					'number' => $time_sheet_late,
					'follow' => !empty($list_follow_assistants['MyAssistant']['timesheet_late']) ? $list_follow_assistants['MyAssistant']['timesheet_late'] : 0
				),
				'week' => $first
			);
		}
        //My project.
        $list_project_my_manager = array();
        $list_project_my_manager = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'project_manager_id' => $employee_id,
                'category' => 1
            ),
            'fields' => array('Project.id', 'Project.id')
        ));
        $list_project_my_manager_bakcup = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectEmployeeManager.project_manager_id' => $employee_id,
                'ProjectEmployeeManager.is_profit_center' => 0
            ),
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Project.id = ProjectEmployeeManager.project_id',
                        'Project.category' => 1
                    )
                )
            ),
            'fields' => array('ProjectEmployeeManager.project_id', 'ProjectEmployeeManager.project_id')
        ));
        $list_project_my_manager = array_unique(array_merge($list_project_my_manager, $list_project_my_manager_bakcup));
        $count_project = count($list_project_my_manager);
        $project_task_in_progress = $project_task_late = $project_task_overload = array();
        if(!empty($list_project_my_manager)){
            $project_task_in_progress = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $list_project_my_manager,
                    'OR' => array(
                        'task_status_id' => $list_status,
                        'task_status_id IS NULL'
                    ),
                ),
				'fields' => array('ProjectTask.id', 'ProjectTask.id')
            ));
            $project_task_late = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $list_project_my_manager,
                    'OR' => array(
                        'task_status_id' => $list_status,
                        'task_status_id IS NULL'
                    ),
                    'task_end_date <=' => date('Y-m-d')
                ),
				'fields' => array('ProjectTask.id', 'ProjectTask.id')
            ));
            $project_task_overload = $this->ProjectTask->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $list_project_my_manager,
                    'OR' => array(
                        'task_status_id' => $list_status,
                        'task_status_id IS NULL'
                    ),
                    'overload >' => 0
                ),
				'fields' => array('ProjectTask.id', 'ProjectTask.id')
            ));
        }
		// Xóa các task cha
		$vars = array('project_task_in_progress', 'project_task_late', 'project_task_overload');
		foreach( $vars as $variable){
			if( !empty( $$variable)) {
				foreach ( $$variable as  $k => $task_id){
					if( in_array($task_id, $tasksHasChild)){ $v = $$variable ;unset( $v[$k]); $$variable = $v;}
				}
			}
		}
        $datas['my_project'] = array(
            'count' => $count_project,
            'in_progress' => array(
                'number' => count($project_task_in_progress),
                'follow' => !empty($list_follow_assistants['MyAssistant']['project_in_progress']) ? $list_follow_assistants['MyAssistant']['project_in_progress'] : 0
            ),
            'late' => array(
                'number' => count($project_task_late),
                'follow' => !empty($list_follow_assistants['MyAssistant']['project_late']) ? $list_follow_assistants['MyAssistant']['project_late'] : 0
            ),
            'overload' => array(
                'number' => count($project_task_overload),
                'follow' => !empty($list_follow_assistants['MyAssistant']['project_overload']) ? $list_follow_assistants['MyAssistant']['project_overload'] : 0
            )
        );
        //My team.
        if($manager == 1){
            $_project = $this->ProjectTask->find('list', array(
                'joins' => array(
                    array(
                        'table' => 'project_task_employee_refers',
                        'alias' => 'TaskRefer',
                        'conditions' => array(
                            'TaskRefer.project_task_id = ProjectTask.id',
                            'OR' => array(
                                array(
                                    'TaskRefer.reference_id' => $list_profit_center_managers,
                                    'TaskRefer.is_profit_center' => 1
                                ),
                                array(
                                    'TaskRefer.reference_id' => $list_employee_of_team_manager,
                                    'TaskRefer.is_profit_center' => 0
                                )
                            )
                        )
                    )
                ),
                'fields' => array('ProjectTask.project_id', 'ProjectTask.project_id')
            ));

            $count_project = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $_project,
                    'category' => 1
                )
            ));
	
            $listTaskAssign = $this->ProjectTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        array(
                            'reference_id' => $list_profit_center_managers,
                            'is_profit_center' => 1
                        ),
                        array(
                            'reference_id' => $list_employee_of_team_manager,
                            'is_profit_center' => 0
                        )
                    )
                ),
                'fields' => array('id', 'project_task_id')
            ));
            $team_task_in_progress = array();
            if( !empty($listTaskAssign) ){
				$team_task_in_progress = $this->ProjectTask->find('list', array(
					'conditions' => array(
						'ProjectTask.project_id' => $list_project,
						'ProjectTask.id' => $listTaskAssign,
						'OR' => array(
							'ProjectTask.task_status_id' => $list_status,
							'ProjectTask.task_status_id IS NULL'
						),
					),
					'fields' => array('ProjectTask.id', 'ProjectTask.id')
				));
            }
            $team_task_late = $this->ProjectTask->find('list', array(
                'conditions' => array(
                    'ProjectTask.project_id' => $list_project,
                    'OR' => array(
                        'ProjectTask.task_status_id' => $list_status,
                        'ProjectTask.task_status_id IS NULL'
                    ),
					'NOT' => array('ProjectTask.task_end_date' => '0000-00-00'),
                    'ProjectTask.task_end_date <=' => date('Y-m-d')
                ),
                'joins' => array(
                    array(
                        'table' => 'project_task_employee_refers',
                        'alias' => 'TaskRefer',
                        'conditions' => array(
                            'TaskRefer.project_task_id = ProjectTask.id',
                            'OR' => array(
                                array(
                                    'TaskRefer.reference_id' => $list_profit_center_managers,
                                    'TaskRefer.is_profit_center' => 1
                                ),
                                array(
                                    'TaskRefer.reference_id' => $list_employee_of_team_manager,
                                    'TaskRefer.is_profit_center' => 0
                                )
                            )
                        )
                    )
                ),
				'fields' => array('ProjectTask.id', 'ProjectTask.id')
            ));
            $team_task_overload = $this->ProjectTask->find('list', array(
                'conditions' => array(
                    'ProjectTask.project_id' => $list_project,
                    'OR' => array(
                        'ProjectTask.task_status_id' => $list_status,
                        'ProjectTask.task_status_id IS NULL'
                    ),
                    'ProjectTask.overload >' => 0
                ),
                'joins' => array(
                    array(
                        'table' => 'project_task_employee_refers',
                        'alias' => 'TaskRefer',
                        'conditions' => array(
                            'TaskRefer.project_task_id = ProjectTask.id',
                            'OR' => array(
                                array(
                                    'TaskRefer.reference_id' => $list_profit_center_managers,
                                    'TaskRefer.is_profit_center' => 1
                                ),
                                array(
                                    'TaskRefer.reference_id' => $list_employee_of_team_manager,
                                    'TaskRefer.is_profit_center' => 0
                                )
                            )
                        )
                    )
                ),
				'fields' => array('ProjectTask.id', 'ProjectTask.id')
            ));
			
			// Xóa các task cha
			$vars = array('team_task_in_progress', 'team_task_late', 'team_task_overload');
			foreach( $vars as $variable){
				if( !empty( $$variable)) {
					foreach ( $$variable as  $k => $task_id){
						if( in_array($task_id, $tasksHasChild)){ $v = $$variable ;unset( $v[$k]); $$variable = $v;}
					}
				}
			}
            $datas['my_team'] = array(
                'count' => $count_project,
                'in_progress' => array(
                    'number' => count($team_task_in_progress),
                    'follow' => !empty($list_follow_assistants['MyAssistant']['team_in_progress']) ? $list_follow_assistants['MyAssistant']['team_in_progress'] : 0
                ),
                'late' => array(
                    'number' => count($team_task_late),
                    'follow' => !empty($list_follow_assistants['MyAssistant']['team_late']) ? $list_follow_assistants['MyAssistant']['team_late'] : 0
                ),
                'overload' => array(
                    'number' => count($team_task_overload),
                    'follow' => !empty($list_follow_assistants['MyAssistant']['team_overload']) ? $list_follow_assistants['MyAssistant']['team_overload'] : 0
                )
            );
        }
        // To validate.
        if($manager == 1){
            $time_sheet_validates = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'status' => 0,
                    'value !=' => 0,
                    'date BETWEEN ? AND ?' => array(strtotime(date('Y') . '-01-01'), strtotime(date('Y') . '-12-31')),
                )
            ));
            $_number_time_sheet_validate = array();
            foreach($time_sheet_validates as $time_sheet_validate){
                $week = intval(date('W', $time_sheet_validate['ActivityRequest']['date'])); //fix issue with 01 / 1
                $_number_time_sheet_validate[$week] = $week;
            }
            $number_time_sheet_validate = count($_number_time_sheet_validate);
            $absence_validates = $this->AbsenceRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'date BETWEEN ? AND ?' => array(strtotime(date('Y') . '-01-01'), strtotime(date('Y') . '-12-31')),
                    'OR' => array(
                        'response_am' => 'waiting',
                        'response_pm' => 'waiting'
                    )
                )
            ));
            $_number_absence_validate = array();
            foreach($absence_validates as $absence_validate){
                $week = intval(date('W', $absence_validate['AbsenceRequest']['date'])); //fix issue with 01 / 1
                $_number_absence_validate[$week] = $week;
            }
            $number_absence_validate = count($_number_absence_validate);
            $datas['to_validated'] = array(
                'timesheet' => array(
                    'number' => $number_time_sheet_validate,
                    'follow' => !empty($list_follow_assistants['MyAssistant']['timesheet_validate']) ? $list_follow_assistants['MyAssistant']['timesheet_validate'] : 0
                ),
                'absence' => array(
                    'number' => $number_absence_validate,
                    'follow' => !empty($list_follow_assistants['MyAssistant']['absence_validate']) ? $list_follow_assistants['MyAssistant']['absence_validate'] : 0
                )
            );
        }
        //Late.
        if($manager == 1){
            $emp = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id','end_date','start_date')
            ));
            $emp_end = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.end_date') : array();
            $emp_start = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.start_date') : array();
            $year = date('Y', time());
            $dto = new DateTime();
            $dto->setISODate($year, 1);
            $dto->setTime(0, 0, 0);
            $dto->modify('monday this week');
            $start = $dto->getTimestamp();
            $end = (strtolower(date('l')) == 'sunday') ? time() : strtotime('last sunday', time());
            $this->loadModels('ActivityRequestConfirm');
            $this->ActivityRequestConfirm->virtualFields['year'] = 'FROM_UNIXTIME(ROUND((start+end)/2), "%Y")';
            $this->ActivityRequestConfirm->virtualFields['week'] = 'WEEKOFYEAR(FROM_UNIXTIME(ROUND((start+end)/2)))';
            $_confirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $list_employee_of_team,
                    'company_id' => $company_id,
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
            $_data =array();
            while($start <= $end){
                $monday = (strtolower(date('l', $start)) == 'monday') ? $start : strtotime('last monday', $start);
                $sunday = (strtolower(date('l', $start)) == 'sunday') ? $start : strtotime('next sunday', $start);
                $AVGDay = ($monday+$sunday)/2;
                $week = intval(date('W', $AVGDay));
                foreach($list_employee_of_team as $id){
                    $employee_endday = (!empty( $emp_end[$id]) && ($emp_end[$id] != '0000-00-00')) ? strtotime($emp_end[$id]) : 0;
                    $employee_startday = (!empty( $emp_start[$id]) && ($emp_start[$id] != '0000-00-00')) ? strtotime($emp_start[$id]) : 0;
                    if(($employee_endday >= $start || $employee_endday == 0) && ($employee_startday < $sunday || $employee_startday == 0) ){
                        if(isset($confirms[$id][$week])){
                            $statusConfirm = $confirms[$id][$week];
                        } else {
                            $statusConfirm = -1;
                        }
                        if($statusConfirm == 2 || $statusConfirm == 0){
                            // ko xu ly
                        } else {
                            $_data[$week][$id] = $id;
                        }
                    }
                }
                $start = strtotime('next monday', $start);
            }
			$number_timesheet_not_send = 0;
			foreach( $_data as $_w => $_emps)  $number_timesheet_not_send += count($_emps);
            $datas['late'] = array(
                'timesheet' => array(
                    'number' => $number_timesheet_not_send,
                    'follow' => !empty($list_follow_assistants['MyAssistant']['timesheet_late_team']) ? $list_follow_assistants['MyAssistant']['timesheet_late_team'] : 0
                )
            );
        }
        $this->loadModel('HistoryFilter');
        $savePositionAssistant = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'path' => 'my_assistants/assistant_position'
            )
        ));
        $savePositionAssistant = !empty($savePositionAssistant) ? $savePositionAssistant['HistoryFilter']['params'] : 'undefined';
        if($savePositionAssistant != 'undefined'){
            $_savePositionAssistant = explode('-', $savePositionAssistant);
            $savePositionAssistant = array();
            $savePositionAssistant['top'] = $_savePositionAssistant[0];
            $savePositionAssistant['left'] = $_savePositionAssistant[1];
        }
        $datas['position'] = $savePositionAssistant;
		//display overload in Project/Task. Ticket #614 07/07/2020
		$this->loadModels('Translation');
		$displayOverload = $this->Translation->find('first', array(
			'recursive' => -1,
            'conditions' => array(
				'Translation.page' => 'Project_Task',
				'Translation.original_text' => 'Overload'
			),
            'fields' => array('id'),
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id',
                        'TranslationSetting.company_id' => $company_id,
						'TranslationSetting.show' => 1
                    ),
                    'type' => 'right'
                )
            )
        ));
		$datas['displayOverload'] = !empty($displayOverload) ? 1 : 0;
        echo json_encode($datas);
        exit;
    }
    function editFollow(){
        if(!empty($_POST)){
            $company_id = $this->employee_info['Company']['id'];
            $employee_id = $this->employee_info['Employee']['id'];
            $key = $_POST['key'];
            $value = ($_POST['checked'] == true) ? 1 : 0;
            $checked = $this->MyAssistant->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'employee_id' => $employee_id
                ),
                'fields' => array('id')
            ));
            if(!empty($checked)){
                $this->MyAssistant->id = $checked['MyAssistant']['id'];
                $this->MyAssistant->save(array($key => $value));
            } else {
                $this->MyAssistant->create();
                $this->MyAssistant->save(
                    array(
                        'company_id' => $company_id,
                        'employee_id' => $employee_id,
                        $key => $value
                    )
                );
            }
            echo 'Done';
            die;
        }
        echo 'Error';
        die;
    }
    public function saveAssitantPositions(){
        if(!empty($_POST)){
            $top = !empty($_POST['top']) ? $_POST['top'] : 0;
            $left = !empty($_POST['left']) ? $_POST['left'] : 0;
            $data = $top . '-' . $left;
            $this->loadModel('HistoryFilter');
            $employee_id = $this->employee_info['Employee']['id'];
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'my_assistants/assistant_position',
                    'employee_id' => $employee_id
                )
            ));
            if(!empty($check)){
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array(
                    'path' => 'my_assistants/assistant_position',
                    'params' => $data
                ));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => 'my_assistants/assistant_position',
                    'employee_id' => $employee_id,
                    'params' => $data
                ));
            }
            echo 'Done';
            die;
        }
        echo 'Error';
        die;
    }
    public function convertBase64(){
        if(!empty($_POST)){
            unlink(APP. 'webroot' . DS . 'shared' . DS . 'saveClipboard.png');
            $tmpFile = APP. 'webroot' . DS . 'shared' . DS . 'saveClipboard.png';
            file_put_contents($tmpFile, base64_decode($_POST['base64']));
            echo $tmpFile;
            die;
        }
    }
}
?>
