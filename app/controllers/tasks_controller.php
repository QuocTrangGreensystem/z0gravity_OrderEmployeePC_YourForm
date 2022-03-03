<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class TasksController extends AppController {
	public $uses = array('ProjectTask', 'ActivityTask', 'ProjectPart', 'ProfitCenter', 'ProjectPhase', 'ProjectPhasePlan', 'Project', 'ProjectStatus', 'ProjectPriority', 'ProjectTeam', 'ProjectEmployeeProfitFunctionRefer', 'ProjectFunctionEmployeeRefer', 'ActivityTaskEmployeeRefer', 'ProjectTaskEmployeeRefer');
	public $components = array('MultiFileUpload', 'SlickExporter');
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Project->recursive = -1;
		$this->ProjectTask->recursive = -1;
		$this->ActivityTask->recursive = -1;
	}
	public function index(){

		if( !isset($this->employee_info['Company']['id']) ){
			$this->Session->setFlash(__('You do not belong to any companies to do this', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
		$csv = new parseCSV();
		if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
			$this->MultiFileUpload->encode_filename = false;
			$this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'importers' . DS;
			$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
			$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
			$reVal = $this->MultiFileUpload->upload();
			if (!empty($reVal)) {
				$this->loadModel('Employee');
				$filename = TMP . 'uploads' . DS . 'importers' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
				$csv->auto($filename);
				if (!empty($csv->data)) {
					$records = array(
						'Update' => array(),
						'Error' => array()
					);
					$merges = array();
					$default = array(
						'PROJECT CODE1' => '',
						'PART' => '',
						'PHASE NAME' => '',
						'TASK NAME' => '',
						'PARENT TASK NAME' => '',
						'ASSIGNED TO' => '',
						'START DATE' => '',
						'END DATE' => '',
						'WORKLOAD' => 0,
						'PRIORITY' => '',
						'STATUS' => ''
					);
					App::import("vendor", "str_utility");
					$str = new str_utility();
					//prepair data
					$cid = $this->employee_info['Company']['id'];
					//resource
					$this->Employee->virtualFields = array('full_name' => "CONCAT(first_name, ' ', last_name)");
					$resourceList = $this->Employee->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $cid
						),
						'fields' => array('id', 'full_name')
					));
					//pc
					$pcList = $this->ProfitCenter->find('list', array(
						'conditions' => array('company_id' => $cid),
						'fields' => array('id', 'name')
					));
					//status
					$statusList = $this->ProjectStatus->find('list', array(
						'conditions' => array('company_id' => $cid),
						'fields' => array('id', 'name')
					));
					//priority
					$priorityList = $this->ProjectPriority->find('list', array(
						'conditions' => array('company_id' => $cid),
						'fields' => array('id', 'priority')
					));

					$validate = array('PROJECT CODE1', 'PHASE NAME', 'TASK NAME');
					$projects = array();
					$mergedTasks = 0;
					foreach ($csv->data as $row) {
						$row = $this->changeKeys($row, $default);
						//remove space
						array_walk($row, array($this, 'sstrim'));
						$error = false;                            
						$row = array_merge($default, $row, array('data' => array(), 'columnHighLight' => array(), 'error' => array(), 'description' => array()));
						foreach ($validate as $key) {
							$row[$key] = trim($row[$key]);
							if (empty($row[$key])) {
								$row['error'][] = sprintf(__('The %s is not blank', true), $key);
								$row['columnHighLight'][$key] = '';
								$error = true;
							}
						}
						if( !$error ){
							//check project code1
							$project = $this->Project->find('first', array(
								'conditions' => array(
									'company_id' => $cid,
									'project_code_1' => $row['PROJECT CODE1']
								),
								'recursive' => -1
							));
							if( !empty($project) ){
								$project = $project['Project'];
								$row['error'][] = "Project: <b>{$project['project_name']}</b>";
								$row['data']['project_id'] = $pid = $project['id'];

								if( $row['PART'] )$row['data']['part_id'] = $this->getPartId($row['PART'], $pid);
								else $row['data']['part_id'] = 0;
								$row['data']['phase_id'] = $this->getPhaseId($row['PHASE NAME'], $cid);

								//check parent task
								if( $row['PARENT TASK NAME'] ){
									$parent = $this->getTaskId($row['PARENT TASK NAME'], $pid, $row['data']['part_id'], $row['data']['phase_id']);
									if( $parent ){
										if( !$parent['ProjectTask']['parent_id'] )$row['data']['parent_id'] = $parent['ProjectTask']['id'];
										else {
											$row['error'][] = __('Exceeded maximum level of sub task', true);
											$row['columnHighLight']['PARENT TASK NAME'] = '';
											$error = true;
										}
									}
									else $row['data']['parent'] = $row['PARENT TASK NAME'];
								}
								//task
								$task = $this->getTaskId($row['TASK NAME'], $pid, $row['data']['part_id'], $row['data']['phase_id']);
								if( $task ){
									$row['data']['id'] = $task['ProjectTask']['id'];
									$row['data']['project_planed_phase_id'] = $task['ProjectTask']['project_planed_phase_id'];
								}
								else {
									$row['data']['task_title'] = $row['TASK NAME'];
								}

								//workload
								if( $row['WORKLOAD'] ){
									$row['data']['estimated'] = floatval($row['WORKLOAD']);
								} else $row['data']['estimated'] = 0;

								//assign
								if( $row['ASSIGNED TO'] ){
									$names = explode(',', $row['ASSIGNED TO']);
									$pc = $rs = 0;
									foreach($names as $name){
										$name = explode('/', $name);
										//PC
										if( count($name) > 1 ){
											$name = $name[1];
											if( ($id = array_search($name, $pcList)) !== false ){
												$row['data']['pcs'][$id] = 0;
												$pc++;
											} else {
												$row['error'][] = sprintf(__('The profit center %s does not exist', true), $name);
												$row['columnHighLight']['ASSIGNED TO'] = '';
												$error = true;
											}
										} else {
											//resource
											$name = $name[0];
											if( ($id = array_search($name, $resourceList)) !== false ){
												$row['data']['resources'][$id] = 0;
												$rs++;
											} else {
												$row['error'][] = sprintf(__('The resource %s does not exist', true), $name);
												$row['columnHighLight']['ASSIGNED TO'] = '';
												$error = true;
											}
										}
									}
									//assign workload case multi pcs or resources
									$total = $pc + $rs;
									if( $total && $row['data']['estimated'] ){
										$int = intval($row['data']['estimated']);
										$mod = $row['data']['estimated'] - $int;
										$workloadPerAssign = intval($int / $total);
										$last = $workloadPerAssign + $int % $total + $mod;
										//pc
										if( $pc )
										foreach($row['data']['pcs'] as $id => &$workload){
											if( $last ){
												$workload = $last;
												$last = 0;
											}
											else $workload = $workloadPerAssign;
										}
										//rs
										if( $rs )
										foreach($row['data']['resources'] as $id => &$w){
											if( $last ){
												$w = $last;
												$last = 0;
											}
											else $w = $workloadPerAssign;
										}
									}
								} //end assign check

								//format start/end date
								if( $row['START DATE'] ){
									$row['data']['task_start_date'] = $str->convertToSQLDate($row['START DATE']);
								} else $row['data']['task_start_date'] = '0000-00-00';
								if( $row['END DATE'] ){
									$row['data']['task_end_date'] = $str->convertToSQLDate($row['END DATE']);
								} else $row['data']['task_end_date'] = '0000-00-00';
								//priority
								if( $row['PRIORITY'] ){
									if( ($id = array_search($row['PRIORITY'], $priorityList)) !== false ){
										$row['data']['task_priority_id'] = $id;
									} else {
										$row['error'][] = sprintf(__('The priority %s does not exist', true), $row['PRIORITY']);
										$row['columnHighLight']['PRIORITY'] = '';
										$error = true;
									}
								}
								//priority
								if( $row['STATUS'] ){
									if( ($id = array_search($row['STATUS'], $statusList)) !== false ){
										$row['data']['task_status_id'] = $id;
									} else {
										$row['error'][] = sprintf(__('The status %s does not exist', true), $row['STATUS']);
										$row['columnHighLight']['STATUS'] = '';
										$error = true;
									}
								}
								//
								if( $error ){
									unset($row['data']);
									$records['Error'][] = $row;
								} else {
									$unique = $pid . '/' . $row['data']['part_id'] . '/' . $row['data']['phase_id'] . '/' . $row['PARENT TASK NAME'] . '/' . $row['TASK NAME'];
									if( isset($merges[$unique]) ){
										//do MERGE
										//one more check: if same task has assigned but this task's `assigned to` is null
										if( !$row['ASSIGNED TO'] && (isset($merges[$unique]['data']['pcs']) || isset($merges[$unique]['data']['resources'])) ){
											$row['error'][] = __('Workload not assigned', true);
											$row['columnHighLight']['ASSIGNED TO'] = '';
											unset($row['data']);
											$records['Error'][] = $row;
											continue;
										}
										//assigned to
										if( isset($row['data']['pcs']) && isset($merges[$unique]['data']['pcs']) ){
											foreach($row['data']['pcs'] as $id => $workload){
												if( isset($merges[$unique]['data']['pcs'][$id]) )
													$merges[$unique]['data']['pcs'][$id] += $workload;
												else $merges[$unique]['data']['pcs'][$id] = $workload;
											}
										}
										if( isset($row['data']['resources']) && isset($merges[$unique]['data']['resources']) ){
											foreach($row['data']['resources'] as $id => $workload){
												if( isset($merges[$unique]['data']['resources'][$id]) )
													$merges[$unique]['data']['resources'][$id] += $workload;
												else $merges[$unique]['data']['resources'][$id] = $workload;
											}
										}
										//sum workload
										$merges[$unique]['data']['estimated'] += $row['data']['estimated'];

										//merge days
										$oldStart = $merges[$unique]['data']['task_start_date'] != '0000-00-00' ? DateTime::createFromFormat('Y-m-d', $merges[$unique]['data']['task_start_date'])->getTimestamp() : false;
										$newStart = $row['data']['task_start_date'] != '0000-00-00' ? DateTime::createFromFormat('Y-m-d', $row['data']['task_start_date'])->getTimestamp() : false;
										if( $oldStart && $newStart )$start = date('Y-m-d', min($oldStart, $newStart));
										else if( $oldStart )$start = date('Y-m-d', $oldStart);
										else if( $newStart )$start = date('Y-m-d', $newStart);
										else $start = '0000-00-00';
										$merges[$unique]['data']['task_start_date'] = $start;

										//end date
										$oldStart = $merges[$unique]['data']['task_end_date'] != '0000-00-00' ? DateTime::createFromFormat('Y-m-d', $merges[$unique]['data']['task_end_date'])->getTimestamp() : false;
										$newStart = $row['data']['task_end_date'] != '0000-00-00' ? DateTime::createFromFormat('Y-m-d', $row['data']['task_end_date'])->getTimestamp() : false;
										$start = max($oldStart, $newStart);

										$merges[$unique]['data']['task_end_date'] = $start ? date('Y-m-d', $start) : '0000-00-00';

										//modify row
										$merges[$unique]['START DATE'] = $merges[$unique]['data']['task_start_date'];
										$merges[$unique]['END DATE'] = $merges[$unique]['data']['task_end_date'];
										$mergedTasks++;
									} else {
										$merges[$unique] = $row;
										unset($merges[$unique]['error'],$merges[$unique]['description']);
									}
									$records['Update'][] = $row;
									if( !in_array($pid, $projects) )$projects[] = $pid;
								}
							} else {
								$row['error'][] = sprintf(__('The project with code %s does not exist', true), $row['PROJECT CODE1']);
								$row['columnHighLight']['PROJECT CODE1'] = '';
								unset($row['data']);
								$records['Error'][] = $row;
							}
						}

					} //end foreach
					$this->set('records', $records);
					$this->set('default', $default);
					$this->set('projects', $projects);
					$this->set('merges', $merges);
					$this->set('mergedTasks', $mergedTasks);
				}//end csv check
				unlink($filename);
				//return $this->render('import');
			}//end check file
		}//end check method
		// $log = $this->ProjectTask->getDataSource()->getLog(false, false);
		// debug($log);
		$this->render('import');
	}

	private function getTaskId($name, $pid, $part, $phase){
		$conditions = array(
			'ProjectTask.task_title' => $name,
			'ProjectTask.project_id' => $pid,
			'Plan.project_planed_phase_id' => $phase,
			//'Plan.project_part_id' => $part
		);
		if( !$part ){
			$conditions['OR'] = array(
				'Plan.project_part_id IS NULL',
				'Plan.project_part_id' => $part
			);
		} else $conditions['Plan.project_part_id'] = $part;
		$check = $this->ProjectTask->find('first', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'project_phase_plans',
					'alias' => 'Plan',
					'type' => 'inner',
					'conditions' => array(
						'Plan.id = ProjectTask.project_planed_phase_id'
					)
				)
			),
			'recursive' => -1,
			'fields' => '*'
		));
		return $check;
	}

	private function getPLan($pid, $part, $phase){
		$conditions = array(
			'project_id' => $pid,
			'project_planed_phase_id' => $phase
		);
		if( $part ){
			$conditions['project_part_id'] = $part;
		} else {
			$conditions['OR'] = array('project_part_id IS NULL', 'project_part_id' => 0);
		}
		$data = $this->ProjectPhasePlan->find('first', array(
			'recursive' => -1,
			'conditions' => $conditions
		));
		return $data;
	}

	private function getPhaseId($name, $cid){
		$check = $this->ProjectPhase->find('first', array(
			'conditions' => array(
				'name' => $name,
				'company_id' => $cid
			),
			'recursive' => -1
		));
		if( !empty($check) )return $check['ProjectPhase']['id'];
		else {
			$this->ProjectPhase->create();
			$this->ProjectPhase->save(array(
				'name' => $name,
				'company_id' => $cid,
				'activated' => 1,
				'color' => $this->rand_color()
			));
			return $this->ProjectPhase->id;
		}
	}
	private function getPartId($name, $pid){
		if( $name ){
			$check = $this->ProjectPart->find('first', array(
				'conditions' => array(
					'title' => $name,
					'project_id' => $pid
				),
				'recursive' => -1
			));
			if( !empty($check) )return $check['ProjectPart']['id'];
			else {
				$this->ProjectPart->create();
				$this->ProjectPart->save(array(
					'title' => $name,
					'project_id' => $pid
				));
				return $this->ProjectPart->id;
			}
		}
		return null;
	}
	private function changeKeys($o, $d){
		$newKeys = array_keys($d);
		$oldKeys = array_keys($o);
		$result = array();
		for($i = 0; $i < count($oldKeys); $i++){
			$result[ $newKeys[$i] ] = $o[ $oldKeys[$i] ];
		}
		return $result;
	}
	public function sstrim(&$v){
		if( is_string($v) )$v = trim($v);
		return;
	}
	private function _utf8_encode_mix($input)
	{
		$result = array();
		foreach($input as $key => $value){
			$result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
		}
		return $result;
	}
	private function _mix_coloumn($input){
		$result = array();
		foreach($input as $value){
			$result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
		}
		return $result;
	}
	private function _saveStaffingAfterImportTask($projects = array()){
		$redirect = Router::url('/tasks/', true);
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
		$this->syncPhases($projects);
		$this->syncParentTasks($projects, true);
		$this->ProjectTask->recursive = 1;
		foreach($projects as $p){
			$this->ProjectTask->staffingSystem($p);
		}
	}
	public function save_import(){
		set_time_limit(0);
		ini_set('memory_limit', '512M');
		if (!empty($this->data)) {
			extract($this->data['Import']);
			if ($task === 'do') {//export
				$import = json_decode($merges, true);
				if (empty($import)) {
					$this->Session->setFlash(__('The data to import was not found. Please try again.', true));
					$this->redirect(array('action' => 'index'));
				}
				$projects = explode(',', $projects);
				$complete = 0;
				$totalRecordImport = count($import);
				$start_time = microtime(TRUE);
				foreach($import as $key => $data){
					if( $this->saveTask($data) )
						$complete++;
				}
				$end_time = microtime(TRUE);

				$totalTime = $end_time - $start_time;
				//run staffing

				$this->Session->setFlash(sprintf(__('The task has been imported %s/%s (%s task(s) merged) (took %s seconds)', true), $complete, $totalRecordImport, $mergedTasks, $totalTime));
				$this->_saveStaffingAfterImportTask($projects);
				
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
					$this->redirect(array('action' => 'index'));
				}
			}
		} else {
			$this->redirect(array('action' => 'index'));
		}
		exit;
	}
	private function saveTask($raw){
		$cid = $this->employee_info['Company']['id'];
		$data = $raw['data'];
		$duration = $this->getWorkingDays($data['task_start_date'], $data['task_end_date'], 0);
		$data['duration'] = $duration;
		//kiem tra parent task
		if( isset($data['parent']) ){
			//parent task ko ton tai
			$parent = $this->getTaskId($data['parent'], $data['project_id'], $data['part_id'], $data['phase_id']);
			if( empty($parent) ){
				//insert?

			} 
			else if( !empty($parent) && $parent['ProjectTask']['parent_id'] ){
				//kiem tra parent co parent khac ko
				//neu co thi bo qua, ko import
				return false;
			}
			//neu ko thi them vao data
			else {
				$data['parent_id'] = $parent['ProjectTask']['id'];
				$activityTaskName = $raw['PHASE NAME'] . '/' . $data['parent'] . '/' . $raw['TASK NAME'];
			}
		} else if( isset($data['parent_id']) ) {
			//
			$parent = $this->ProjectTask->read(null, $data['parent_id']);
			$activityTaskName = $raw['PHASE NAME'] . '/' . $parent['ProjectTask']['task_title'] . '/' . $raw['TASK NAME'];
		} else {
			//empty parent
			$activityTaskName = $raw['PHASE NAME'] . '/' . $raw['TASK NAME'];
		}

		//inherit phase plan
		if( !isset($data['id']) ){
			$this->ProjectTask->create();
			//neu co parent
			if( isset($parent) ){
				$data['project_planed_phase_id'] = $parent['ProjectTask']['project_planed_phase_id'];
			}
		}

		//phase plan
		if( !isset($data['project_planed_phase_id']) ){
			//case: phase plan existed (pid, part, phase)
			$plan = $this->getPLan($data['project_id'], $data['part_id'], $data['phase_id']);
			if( !empty($plan) )$data['project_planed_phase_id'] = $plan['ProjectPhasePlan']['id'];
			else {
				//tao project phase plan?
				$this->ProjectPhasePlan->create();
				$this->ProjectPhasePlan->save(array(
					'project_id' => $data['project_id'],
					'project_part_id' => $data['part_id'],
					'project_planed_phase_id' => $data['phase_id']
				));
				$data['project_planed_phase_id'] = $this->ProjectPhasePlan->id;
				//start & end date se update sau
			}
		}

		//$this->ProjectPhasePlan->id = $data['project_planed_phase_id'];

		if( $this->ProjectTask->save($data) ){
			$taskId = $this->ProjectTask->id;
			$project = $this->Project->read(null, $data['project_id']);
			//save activity task
			if( $project['Project']['activity_id'] ){
				$aParent = 0;
				if( isset($parent) ){
					$parentActivityTask = $this->ActivityTask->find('first', array(
						'conditions' => array(
							'project_task_id' => $parent['ProjectTask']['id']
						)
					));
					$aParent = !empty($parentActivityTask) ? $parentActivityTask['ActivityTask']['id'] : 0;
				}
				//kiem tra activity task co noi voi task hien tai ko
				$aTask = $this->ActivityTask->find('first', array(
					'conditions' => array(
						'project_task_id' => $taskId
					)
				));
				//neu co thi update, ko thi create
				$saveTask = array(
					'name' => $activityTaskName,
					'parent_id' => $aParent,
					'task_status_id' => @$data['task_status_id'],
					'task_priority_id' => @$data['task_priority_id'],
					'activity_id' => $project['Project']['activity_id'],
					'project_task_id' => $taskId,
					'estimated' => $data['estimated'],
					'duration' => $data['duration']
				);
				if( !empty($aTask) ){
					$aTask = $aTask['ActivityTask'];
					$saveTask = array_merge($aTask, $saveTask);
				} else {
					$this->ActivityTask->create();
				}
				//save
				$this->ActivityTask->save($saveTask);
			}
			//save assign
			$assigns = array();
			if( isset($data['resources']) ){
				foreach($data['resources'] as $id => $workload){
					$assigns[] = array(
						'reference_id' => $id,
						'project_task_id' => $taskId,
						'activity_task_id' => $this->ActivityTask->id ? $this->ActivityTask->id : null,
						'estimated' => $workload,
						'is_profit_center' => 0
					);
				}
			}
			//tuong tu cho pc
			if( isset($data['pcs']) ){
				foreach($data['pcs'] as $id => $workload){
					$assigns[] = array(
						'reference_id' => $id,
						'project_task_id' => $taskId,
						'activity_task_id' => $this->ActivityTask->id ? $this->ActivityTask->id : null,
						'estimated' => $workload,
						'is_profit_center' => 1
					);
				}
			}
			//xoa cac assign cu
			if( $this->ActivityTask->id ){
				$this->ActivityTaskEmployeeRefer->deleteAll(array(
					'ActivityTaskEmployeeRefer.activity_task_id' => $this->ActivityTask->id
				));
				if( !empty($assigns) )$this->ActivityTaskEmployeeRefer->saveAll($assigns);
			}
			//luu assign
			if( !empty($assigns) ){
				$this->ProjectTaskEmployeeRefer->deleteAll(array(
					'ProjectTaskEmployeeRefer.project_task_id' => $taskId
				));
				$this->ProjectTaskEmployeeRefer->saveAll($assigns);
				//them team va resource vao project
				$teams = $this->ProjectTeam->find('list', array(
					'recursive' => -1,
					'conditions' => array('project_id' => $data['project_id']),
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
			// cap nhat lai start, end date cua parent
			// $taskStart = $data['task_start_date'] != '0000-00-00' ? strtotime($data['task_start_date']) : 0;
			// $taskEnd = $data['task_end_date'] != '0000-00-00' ? strtotime($data['task_end_date']) : 0;
			// if(!empty($data['parent_id'])){
			// 	$estimatedParents = $this->ProjectTask->find('all', array(
			// 		'conditions' => array(
			// 			'parent_id' => $data['parent_id']
			// 		),
			// 		'fields' => array('SUM(estimated) as workload')
			// 	));
			// 	$parentStart = ($parent['ProjectTask']['task_start_date'] != '0000-00-00') ? strtotime($parent['ProjectTask']['task_start_date']) : 0;
			// 	$parentEnd = ($parent['ProjectTask']['task_end_date'] != '0000-00-00') ? strtotime($parent['ProjectTask']['task_end_date']) : 0;
			// 	if( $parentStart == 0 || ($taskStart > 0 && $taskStart < $parentStart) ){
			// 		$parentStart = $taskStart;
			// 	}
			// 	if($taskEnd > 0 && $taskEnd > $parentEnd){
			// 		$parentEnd = $taskEnd;
			// 	}
			// 	$parentDuration = $this->getWorkingDays(date('Y-m-d', $parentStart), date('Y-m-d', $parentEnd), '');
			// 	$estimatedParents = !empty($estimatedParents) ? array_shift(Set::classicExtract($estimatedParents, '{n}.0.workload')) : 0;
			// 	$savedParent = array(
			// 		'task_task_start_date' => date('Y-m-d', $parentStart),
			// 		'task_task_end_date' => date('Y-m-d', $parentEnd),
			// 		'estimated' => $estimatedParents,
			// 		'duration' => $parentDuration
			// 	);
			// 	$this->ProjectTask->id = $data['parent_id'];
			// 	$this->ProjectTask->save($savedParent);
			// }
			//cap nhat lai start, end date cua phase plan
			//code sau se lam cham qua trinh import
			//dung syncPhases de sync lai tat ca phase cua cac project dc import

			// $projectPhases = $this->ProjectPhasePlan->find('first', array(
			// 	'recursive' => -1,
			// 	'conditions' => array(
			// 		'ProjectPhasePlan.id' => $data['project_planed_phase_id']
			// 	),
			// 	'fields' => array('phase_real_start_date', 'phase_real_end_date')
			// ));
			// if(!empty($projectPhases)){
			// 	$start = strtotime($projectPhases['ProjectPhasePlan']['phase_real_start_date']);
			// 	$end = strtotime($projectPhases['ProjectPhasePlan']['phase_real_end_date']);
			// 	if( ($taskStart > 0 && $taskStart < $start) || $start == 0 ){
			// 		$start = $taskStart;
			// 	}
			// 	if($taskEnd > 0 && $taskEnd > $end){
			// 		$end = $taskEnd;
			// 	}
			// 	//add: planed start/end date
			// 	$savePhased = array(
			// 		'phase_planed_start_date' => date('Y-m-d', $start),
			// 		'phase_planed_end_date' => date('Y-m-d', $end),
			// 		'phase_real_start_date' => date('Y-m-d', $start),
			// 		'phase_real_end_date' => date('Y-m-d', $end),
			// 		'planed_duration' => $this->getWorkingDays(date('Y-m-d', $start), date('Y-m-d', $end), 0)
			// 	);
			// 	$this->ProjectPhasePlan->id = $data['project_planed_phase_id'];
			// 	$this->ProjectPhasePlan->save($savePhased);
			// }
			//end
			return true;
		}
		return false;
	}

	public function syncPhases($project_id, $parentOnly = false){
		if( is_string($project_id) )$project_id = explode(',', $project_id);
		$conds = array(
			'project_id' => $project_id
		);
		if( $parentOnly ){
			$conds[0] = 'parent_id IS NOT NULL';
			$conds[1] = 'parent_id != "0000-00-00"';
		}
		$result = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => $conds,
			'fields' => array(
				'project_id',
				'project_planed_phase_id',
				'MIN(IF(task_start_date != "0000-00-00", task_start_date, CURDATE())) as start_date',
				'MAX(task_end_date) as end_date'
			),
			'group' => array('project_id', 'project_planed_phase_id')
		));
		foreach ($result as $r) {
			$start = $r[0]['start_date'];
			$end = $r[0]['end_date'];
			$savePhased = array(
				'project_id' => $r['ProjectTask']['project_id'],
				'phase_planed_start_date' => $start,
				'phase_planed_end_date' => $end,
				'phase_real_start_date' => $start,
				'phase_real_end_date' => $end,
				'planed_duration' => $this->getWorkingDays($start, $end, 0)
			);
			$this->ProjectPhasePlan->id = $r['ProjectTask']['project_planed_phase_id'];
			$this->ProjectPhasePlan->save($savePhased);
		}
		return;
	}

	public function syncParentTasks($project_id){
		if( is_string($project_id) )$project_id = explode(',', $project_id);
		$result = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'parent_id IS NOT NULL',
				'parent_id != 0',
				'task_start_date IS NOT NULL',
				'task_start_date !=' => '0000-00-00',
				'task_end_date IS NOT NULL',
				'task_end_date !=' => '0000-00-00'
			),
			'fields' => array(
				'project_id',
				'parent_id',
				'MIN(task_start_date) as start_date',
				'MAX(task_end_date) as end_date'
			),
			'group' => array('project_id', 'parent_id')
		));
		foreach ($result as $r) {
			$start = $r[0]['start_date'];
			$end = $r[0]['end_date'];
			$save = array(
				'project_id' => $r['ProjectTask']['project_id'],
				'task_start_date' => $start,
				'task_end_date' => $end,
				'duration' => $this->getWorkingDays($start, $end, 0)
			);
			$this->ProjectTask->id = $r['ProjectTask']['parent_id'];
			$this->ProjectTask->save($save);
		}
		return;
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
	private function rand_color() {
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}
	private function _getHoliday($startDate, $endDate){
        $_holiday = array();
        if ($startDate < $endDate) {
            $startDate = strtotime($startDate); 
            $endDate = strtotime($endDate);
            
            while ($startDate <= $endDate){
                $_start = strtolower(date("l", $startDate));
                $_end = strtolower(date("l", $endDate));
                if($_start == 'saturday' || $_start == 'sunday'){
                    $_holiday[] = date("m-d-Y", $startDate);
                }
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
            }
        }
        return $_holiday;
    }
	public function import_excel(){
		$this->loadModels('HistoryFilter', 'Translation', 'Project', 'ProjectTask', 'ProjectPhasePlan', 'ProjectPart', 'ProjectPhase', 'ProjectMilestone', 'Menu');
		$data = array();
		$original_text = array();
		$task_name = array();
		$project_id = array();
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
				$original_text['project_name'] = __('Project Name', true);
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
				foreach($data_format as $k => $v){
					if(!empty($v[0])){
						$project = $this->Project->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'project_name' => $v[0],
								'company_id' => $this->employee_info['Company']['id']
							),
							'fields' => array('id', 'start_date', 'end_date'),
						));
						if(empty($project['Project']['start_date'])){
							$project['Project']['start_date'] = '0000-00-00';
							$project['Project']['end_date'] = '0000-00-00';
						}
						// Project task
						$list_task_name = $this->ProjectTask->find('all', array(
							'recursive' => -1,
							'conditions' => array(
								'project_id' => $project['Project']['id'],
							),
							'fields' => array('id', 'task_title', 'project_planed_phase_id', 'parent_id', 'project_id'),
						));
						$list_task_name =  !empty($list_task_name) ? Set::classicExtract($list_task_name, '{n}.ProjectTask') : array();
						$task_name = array_merge($task_name,$list_task_name);
						$project_id[] = $project['Project']['id'];
						
						// Project phase
						$projectPhase = $this->ProjectPhase->find('all',array(
							'recursive' => -1,
							'conditions' => array('company_id' => $this->employee_info['Company']['id']),
							'fields' => array('name')
						));
						$projectPhase = !empty($projectPhase) ? Set::classicExtract($projectPhase, '{n}.ProjectPhase.name') : array();
						
						// Project Milestone
						$this->loadModel('ProjectMilestone');
						$projectMilestones = $this->ProjectMilestone->find('all', array(
							'recursive' => -1,
							'conditions' => array('project_id' => $project['Project']['id']),
							'fields' => array('id', 'project_milestone', 'milestone_date', 'validated'),
							'order' => array('milestone_date' => 'ASC')
						));
						$projectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
						
						$projectStatus = $this->list_projectStatus($project['Project']['id']);
						$projectStatus = !empty($projectStatus) ? Set::classicExtract($projectStatus, '{n}.ProjectStatus') : array();
						$listEmployee = $this->_getTeamEmployeesForLoad($project['Project']['id'], null, $project['Project']['start_date'], $project['Project']['end_date']);
						$listEmployee = !empty($listEmployee) ? Set::classicExtract($listEmployee, '{n}.Employee') : array();
						$data_format[$k]['project_id'] = $project['Project']['id'];
					}else{
						unset($data_format[$k]);
					}
				}
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
		if(!empty($project_id)){
			$project_id = array_unique($project_id);
		}
		$this->set(compact('data_format', 'data_columns', 'original_text', 'setting_matching', 'project_id', 'task_name', 'projectPhase', 'projectStatus', 'listEmployee', 'projectMilestones', 'list_project_name'));
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
}