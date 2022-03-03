<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class StaffingSystemsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
	var $uses = array('ActivityTask','ProjectTask','TmpStaffingSystem','ActivityTaskEmployeeRefer','Activity','ProjectTaskEmployeeRefer','Project','NctWorkload','ActivityRequest');
    var $name = 'StaffingSystems';

    /**
     * Components
     *
     * @var array
     * @access public
     */
	function index($ajax = '')
	{
		$this->Session->write('ProjectSave.TMP', null);
		$this->Session->write('ActivitySave.TMP', null);
		if($ajax == 'ajax')
		{
			$this->layout = 'ajax';
			if(isset($this->data))
			{
				$keyword = $this->data['type'];
				list($count,$results) = $this->checkingStaffing($keyword);
				list($done,$progress,$count) = $this->checkProcess($keyword);
				echo $results = json_encode(array('keyword'=>$keyword,'results'=>$results,'count'=>$count,'done'=>$done,'progress'=>$progress));
				//$this->set(compact('keyword','results'));
				//echo $this->render('template');
			}
			
			// Save last date check staffing
			$is_sas = $this->employee_info['Employee']['is_sas'];
			if($is_sas == 0){
				$stf_updated = time();
				$this->loadModel('CompanyConfig');
				if( !isset($this->companyConfigs['milestone_check_staffing'])){
					$this->CompanyConfig->create();
					$this->CompanyConfig->save(array(
						'cf_name' => 'milestone_check_staffing',
						'cf_value' => $stf_updated,
						'company' => $this->employee_info['Company']['id']
					));
				}else{
					$this->CompanyConfig->updateAll(
						array('cf_value' => $stf_updated),
						array(
							'cf_name' => 'milestone_check_staffing',
							'company' => $this->employee_info['Company']['id']
						)
					);
				}
			}
			exit;
		}
		else
		{
			//First time : delete session
			//$this->Session->delete('RecordChecked');
		}
		$keyword = 'Project';


		//list($count,$results) = $this->checkingStaffing($keyword);
		//list($done,$progress,$count) = $this->checkProcess($keyword);
		$results = array();
		$done = 1;
		$progress = 0;
		$count = 0;
		/*if($count == 0)
		{
			$this->Session->setFlash("Done!","success");
		}
		else
		{
			$this->Session->setFlash("<a href='rebuilds/'$keyword target='_blank'>$count $keyword invalid</a>","warning");
		}*/
		//$this->Session->setFlash("&nbsp","success");
		$this->Session->setFlash(__("Select Project or Activity and click checking", true),"success");
		$this->set(compact('keyword','results','count','done','progress'));
	}
	function checkProcess($keyword)
	{
		$company = $this->employee_info['Company']['id'];
		if($keyword == 'Activity')
		{
			$countRecord = $this->$keyword->find('count',array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company,
					'project' => null
				)
			));
		}
		else
		{
			$countRecord = $this->$keyword->find('count',array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company,
				)
			));
		}
		if( empty($countRecord) ){
			$progress = 100;
		}else{
			$progress = round(((count($this->Session->read('RecordChecked')) / $countRecord) * 100 ),2);
		}
		$countRecordInvalid = 0;
		if($progress >= 100) {
			$done = true;
			//If done : reset sessions
			$this->Session->delete('RecordChecked');
			if($keyword == 'Activity')
			{
				$countRecordInvalid = $this->$keyword->find('count',array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company,
						'rebuild_staffing' => 1,
						'project' => null
					)
				));
			}
			else
			{
				$countRecordInvalid = $this->$keyword->find('count',array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company,
						'rebuild_staffing' => 1,
					)
				));
			}
		}
		else {
			$done = false;
		}
		return array($done,$progress,$countRecordInvalid);
	}
	function format($keyword='Activity'){
		if($keyword != 'Activity' && $keyword != 'Project')
		{
			$keyword = 'Activity';
		}
		$this->layout='ajax';
		ini_set('max_execution_time', 0);
		set_time_limit(0);
		ini_set('memory_limit', '512M');
		$keywordT = $keyword.'Task';
		$keywordS = $keyword.'Save';
		$company = $this->employee_info['Company']['id'];
        $projectSaves = $this->Session->read($keywordS.'.TMP');
        $projectSaves = !empty($projectSaves) ? $projectSaves : array();
		$_conditions = array(
			'company_id' => $company,
			'rebuild_staffing' => 1,
			'NOT' => array($keyword.'.id' => $projectSaves)
		);
		if($keyword == 'Activity')
		{
			$_conditions['project'] = null;
		}
        $projects = $this->$keyword->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id'),
            'conditions' => $_conditions,
            'order' => array('id' => 'ASC'),
            'limit' => 100
        ));
		$HTML = '';
        foreach($projects as $project){
			if($keyword == 'Project')
			{
				$this->syncWorkloadFormTaskVsWorkloadFromRefers($project);
			}
			else
			{
				$this->syncWorkloadFormTaskVsWorkloadFromRefersActivity($project);
			}
            $HTML .= 'Project : ' . $project . ' : ';
            $HTML .= $this->$keywordT->staffingSystem($project,true);
			$HTML .= "<br />";
        }

        $projectSaves = array_merge($projectSaves, $projects);
        $this->Session->write($keywordS.'.TMP', $projectSaves);
		$nextStep = empty($projects) ? 0 : 1;
		echo json_encode(array('html'=>$HTML,'next' => $nextStep));
        exit;
     }
	function rebuilds($keyword = 'Activity'){
	}
	function rebuild($keyword,$id){
		$this->layout = 'ajax';
		$keyword = $keyword.'Task';
		$this->$keyword->staffingSystem($id,false);
		echo 1;
		exit;
	}
	function syncWorkloadFormTaskVsWorkloadFromRefersActivity($id = null)
	{
		//$this->loadModel('ProjectTaskEmployeeRefer');
		//$this->loadModel('ProjectTask');
		$this->loadModel('ActivityTaskEmployeeRefer');
		$this->loadModel('ActivityTask');

		$taskList = array();
		$taskList = $this->ActivityTask->getActivityTaskNotSpecialAndNct($id,true);
		$estimatedFromRefers = $this->ActivityTaskEmployeeRefer->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'activity_task_id' => array_keys($taskList)
			),
			'fields' => array(
				'activity_task_id',
				'SUM(estimated) as estimated'
			),
			'group' => array('activity_task_id')
		));
		$estimatedFromRefers = Set::combine($estimatedFromRefers,'{n}.ActivityTaskEmployeeRefer.activity_task_id','{n}.0.estimated');
		foreach($taskList as $key=>$task)
		{
			$data = array();
			if(isset($estimatedFromRefers[$key]) && $estimatedFromRefers[$key] != $task['estimated'])
			{
				$data['ActivityTask']['id'] = $key ;
				$data['ActivityTask']['estimated'] = $estimatedFromRefers[$key] ;
				$this->ActivityTask->save($data);
			}
		}
		return;
	}
	function syncWorkloadFormTaskVsWorkloadFromRefers($id = null)
	{
		$this->loadModel('ProjectTaskEmployeeRefer');
		$this->loadModel('ProjectTask');
		//$this->loadModel('ActivityTaskEmployeeRefer');
		//$this->loadModel('ActivityTask');
		/*$projects = ClassRegistry::init('Project')->find('list', array(
            'recursive' => -1,
			'conditions' => array('Project.id' => $id)
            'fields' => array('id')
        ));*/
		$taskList = array();
		$taskList = $this->ProjectTask->getProjectTaskNotSpecialAndNct($id,true);
		$estimatedFromRefers = $this->ProjectTaskEmployeeRefer->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => array_keys($taskList)
			),
			'fields' => array(
				'project_task_id',
				'SUM(estimated) as estimated'
			),
			'group' => array('project_task_id')
		));
		$estimatedFromRefers = Set::combine($estimatedFromRefers,'{n}.ProjectTaskEmployeeRefer.project_task_id','{n}.0.estimated');
		foreach($taskList as $key=>$task)
		{
			$data = array();
			if(isset($estimatedFromRefers[$key]) && $estimatedFromRefers[$key] != $task['estimated'])
			{
				$data['ProjectTask']['id'] = $key ;
				$data['ProjectTask']['estimated'] = $estimatedFromRefers[$key] ;
				$this->ProjectTask->save($data);
			}
		}
		return;
	}
	function checkingStaffing($keyword)
	{
		ini_set('memory_limit', '512M');
		set_time_limit (0);
		$LIMIT = 120;
		if(!$this->Session->check('RecordChecked'))
		{
			$LIMIT = 20;
			$this->Session->write('RecordInvalid', 0);
			$this->Session->write('RecordChecked', array());
		}
		$company = $this->employee_info['Company']['id'];
		$keywordT = $keyword.'Task';

		$_conditions = array(
			'company_id' => $company
		);
		if($keyword == 'Activity')
		{
			$keywordNot = 'project';
			$keywordNotS = 'project_id';
			$_conditions['project'] = null;
			$_conditionsStaffing['project_id'] = 0;
			$_fields = array('id','name AS name','project AS linked', 'rebuild_staffing');
		}
		else
		{
			$keywordNot = 'activity_id';
			$keywordNotS = 'activity_id';
			$_fields = array('id','project_name AS name','activity_id AS linked', 'rebuild_staffing');
		}
		$_conditions['NOT'] = array(
			$keyword.'.id' => $this->Session->read('RecordChecked')
		);
		$keywordF = strtolower($keyword).'_id';
		$keywordNctF = strtolower($keyword).'_task_id';
		$activities = $this->$keyword->find('all',array(
			'limit' => $LIMIT,
			'recursive' => -1,
			'conditions' =>$_conditions,
			'fields' => $_fields,
			'order' => array('rebuild_staffing'=>'DESC')
		));
		$rebuildStaffings = Set::combine($activities, '{n}.'.$keyword.'.id', '{n}.'.$keyword.'.rebuild_staffing');
		$activitiesLinked = Set::combine($activities, '{n}.'.$keyword.'.id', '{n}.'.$keyword.'.linked');
		$activities = Set::combine($activities, '{n}.'.$keyword.'.id', '{n}.'.$keyword.'.name');

		$activityLists = array_keys($activities);
		//GET SESSIONS Activity/Project CHECKED
		$activityChecked = array_merge($this->Session->read('RecordChecked'),$activityLists);
		$this->Session->write('RecordChecked', $activityChecked);
		//END
		$activityTasks = $this->$keywordT->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				''.$keywordT.'.'.$keywordF.'' => $activityLists,
				''.$keywordT.'.special'=>0,
				//'OR' => array(''.$keywordT.'.is_nct IS NULL', ''.$keywordT.'.is_nct' => 0)
			),
			'fields' => array('id','parent_id',$keywordF)
		));

		//remove parent task (task level 1 and not exists task children)
		$listActivityTaskIds = Set::classicExtract($activityTasks, '{n}.'.$keywordT.'.id');
		//$listActivityTaskFollowActivity = Set::combine($activityTasks, '{n}.'.$keywordT.'.id', '{n}.'.$keywordT, '{n}.'.$keywordT.'.'.$keywordF);
		$listActivityTaskFollowActivity = Set::combine($activityTasks, '{n}.'.$keywordT.'.id', '{n}.'.$keywordT.'.id', '{n}.'.$keywordT.'.'.$keywordF);
		$parentIds = array_unique(Set::classicExtract($activityTasks, '{n}.'.$keywordT.'.parent_id'));
		foreach($activityTasks as $key => $activityTask){
			foreach($parentIds as $parentId){
				if($activityTask[$keywordT]['id'] == $parentId){
					unset($listActivityTaskIds[$key]);
				}
			}
		}
		$activityTasks = array_values($listActivityTaskIds);
		//GET CONSUMED
		foreach($listActivityTaskFollowActivity as $activityId=>$tasks)
		{
			$tasks=array_values($tasks);
			if($keyword == 'Project')
			{
				$activityTasksLinked = $this->ActivityTask->find('list', array(
					'recursive' => -1,
					'conditions' => array('ActivityTask.project_task_id' => $tasks),
					'fields' => array('project_task_id', 'id')
				));
			}
			else
			{
				$activityTasksLinked = $tasks;
			}
			$CONSUMED[$activityId] = $this->ActivityRequest->find('all',array(
				'recursive'     => -1,
				'fields'        => array('SUM(value) AS consumed'),
				'conditions'    => array(
					'status'    => 2,
					'task_id'   => $activityTasksLinked,
					'company_id'    => $company,
					'NOT'           => array('value' => 0, "task_id" => null)
				),
			));
			$CONSUMED[$activityId] = Set::classicExtract($CONSUMED[$activityId], '{n}.0.consumed');
		}
		if($keyword == 'Project')
		{
			$_previous = array_values($activitiesLinked);
		}
		else
		{
			$_previous = array_values($activityLists);
		}
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

		$previous = Set::combine($previous,'{n}.ActivityRequest.activity_id','{n}.0.consumed');
		//END
		$results = array();

		$workloadFromTask = $this->$keywordT->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				$keywordF => $activityLists,
				'id' => $activityTasks,
				'special' => 0
			),
			'fields' => array('SUM(estimated) as workload, '.$keywordF.''),
			'group' => $keywordF
		));
		$workloadFromTask = Set::combine($workloadFromTask,'{n}.'.$keywordT.'.'.$keywordF.'','{n}.0.workload');

		//NCT Task
		/*$nctasks = $this->$keywordT->find('list', array(
			'recursive' => -1,
			'conditions' => array(''.$keywordT.'.'.$keywordF.'' => $activityLists, ''.$keywordT.'.is_nct' => 1),
			'fields' => array('id')
		));
		$table = $keyword == 'Activity' ? 'activity_tasks' : 'project_tasks';
		$joins = array(
				array(
					'table' => $table,
					'alias' => 'PT',
					'type' => 'inner',
					'conditions' => array(
						'NctWorkload.'.$keywordNctF.' = PT.id'
					)
				)
			);
		$nctWorkloads = $this->NctWorkload->find('all', array(
			'conditions' => array($keywordNctF => $nctasks),
			'joins' => $joins,
			'fields' => array('SUM(NctWorkload.estimated) as workload, '.$keywordF.''),
			'group' => $keywordF
		));
		$nctWorkloads = Set::combine($nctWorkloads,'{n}.PT.'.$keywordF.'','{n}.0.workload');
		*/
		//END
		$_conditionsStaffing[$keywordF] = $activityLists;
		$staffings = $this->TmpStaffingSystem->find('all',array(
			'recursive' => -1,
			'conditions' => $_conditionsStaffing,
			'fields' => array('SUM(estimated) as workload, SUM(consumed) as consumed, model, '.$keywordF.''),
			'group' => array('model',$keywordF)
		));

		foreach($staffings as $index=>$data)
		{
			$_data = array_merge($data[0],$data['TmpStaffingSystem']);
			//DATA WORKLOAD
			if($_data['model'] == 'employee')
			{
				$staffingsE[$_data[$keywordF]] = $_data['workload'];
			}
			elseif($_data['model'] == 'skill')
			{
				$staffingsS[$_data[$keywordF]] = $_data['workload'];
			}
			elseif($_data['model'] == 'profile')
			{
				$staffingsP2[$_data[$keywordF]] = $_data['workload'];
			}
			else
			{
				$staffingsP[$_data[$keywordF]] = $_data['workload'];
			}
			//END
			//DATA CONSUMED
			if($_data['model'] == 'employee')
			{
				$staffingsConsumedE[$_data[$keywordF]] = $_data['consumed'];
			}
			elseif($_data['model'] == 'skill')
			{
				$staffingsConsumedS[$_data[$keywordF]] = $_data['consumed'];
			}
			else
			{
				$staffingsConsumedP[$_data[$keywordF]] = $_data['consumed'];
			}
			//END
		}
		$count = 0;
		$needToChange=array();
		foreach($activities as $id=>$name)
		{
			//DATA WORKLOAD (estimated)
			$error = 0;
			$_workload = isset($workloadFromTask[$id]) ? number_format($workloadFromTask[$id],2,'.','') : 0.00;
			$_staffingE = isset($staffingsE[$id]) ? number_format($staffingsE[$id],2,'.','') : 0.00;
			$_staffingP = isset($staffingsP[$id]) ? number_format($staffingsP[$id],2,'.','') : 0.00;
			$_staffingS = isset($staffingsS[$id]) ? number_format($staffingsS[$id],2,'.','') : 0.00;
			$_staffingP2 = isset($staffingsP2[$id]) ? number_format($staffingsP2[$id],2,'.','') : 0.00;
			if($_workload == 0.00)
			{
				//continue;
			}
			if($_staffingE != $_workload)
			{
				$error = 1;
			}
			if($_staffingP != $_workload)
			{
				$error = 2;
			}
			if($_staffingS != $_workload && $keyword == 'ProjectSkill')
			{
				$error = 3;
			}
			if($_staffingP2 != $_workload)
			{
				$error = 7;
			}
			//END
			//DATA CONSUMED
			if($keyword == 'Project')
			{
				$_previous = 0;
				if(isset($activitiesLinked[$id]) && is_numeric($activitiesLinked[$id]))
				{
					$_previous = $activitiesLinked[$id];
				}
			}
			else
			{
				$_previous = $id;
			}
			$_consumed = isset($previous[$_previous]) ? $previous[$_previous] : 0.00;
			$_consumed = isset($CONSUMED[$id]) ? $_consumed+$CONSUMED[$id][0] : $_consumed;
			$_consumed = number_format($_consumed,2,'.','');
			$_staffingConsumedE = isset($staffingsConsumedE[$id]) ? number_format($staffingsConsumedE[$id],2,'.','') : 0.00;
			$_staffingConsumedP = isset($staffingsConsumedP[$id]) ? number_format($staffingsConsumedP[$id],2,'.','') : 0.00;
			$_staffingConsumedS = isset($staffingsConsumedS[$id]) ? number_format($staffingsConsumedS[$id],2,'.','') : 0.00;
			
			if($_consumed == 0.00)
			{
				//continue;
			}
			if($_staffingConsumedE != $_consumed)
			{
				$error = 4;
			}
			if($_staffingConsumedP != $_consumed)
			{
				$error = 5;
			}
			if($_staffingConsumedS != $_consumed && $keyword == 'ProjectSkill')
			{
				$error = 6;
			}
			//END
			$rebuild = '';
			if($error > 0 || !empty($rebuildStaffings[$id]))
			{
				$rebuild = $id;
				$needToChange[] = $id;
				$count++;
			}
			//debug($error); 
			$results[$id] = array(
				'name' => $name,
				'workload' => $_workload,
				'staffingE' => $_staffingE,
				'cls_staffingE' => $_staffingE == $_workload ? '' : 'error',
				'staffingP' => $_staffingP,
				'cls_staffingP' => $_staffingP == $_workload ? '' : 'error',
				'staffingS' => $_staffingS,
				'cls_staffingS' => $_staffingS != $_workload && $keyword == 'Project' ? 'error' : '',
				'staffingP2' => $_staffingP2,
				'cls_staffingP2' => $_staffingP2 == $_workload ? '' : 'error',
				'consumed' => $_consumed,
				'staffingConsumedE' => $_staffingConsumedE,
				'staffingConsumedP' => $_staffingConsumedP,
				'staffingConsumedS' => $_staffingConsumedS,
				'rebuild' => $rebuild
			);
		}
		if(!empty($needToChange))
		{
			$this->$keyword->updateAll(
				array(
					$keyword.'.rebuild_staffing' => 1
				),
				array(
					$keyword.'.id' => $needToChange
				)
			);
		}
		return array($count,$results);
	}
	function temp()
	{
		$fields = array('SUM(ActivityTaskEmployeeRefer.estimated) as workload, At.activity_id');
		$joins = array(
			array(
				'table' => 'activity_tasks',
				'alias' => 'At',
				'type' => 'LEFT',
				'foreignKey' => 'id',
				'conditions'=> array(
					'ActivityTaskEmployeeRefer.activity_task_id = At.id',
				),

			)
		);
		$workloadFromTaskAsingn = $this->ActivityTaskEmployeeRefer->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'At.activity_id' => $activityLists,
			),
			'fields' => $fields,
			'joins' => $joins,
			'group' => 'At.activity_id'
		));
		$workloadFromTaskAsingn = Set::combine($workloadFromTaskAsingn,'{n}.At.activity_id','{n}.0.workload');
	}
	function archived($ajax = null)
	{
		$keyword = 'Activity';
		if($ajax == 'ajax')
		{
			$this->layout = 'ajax';
			if(isset($this->data))
			{
				$keyword = $this->data['type'];
			}
		}
		if($keyword == 'Activity'){
			//only check activity not activated
			$_conditions['activated'] = 0;
			//$_conditions['archived <>'] = 1;
			$_conditions['project'] = null;
		}else{
			//only check project inprogress & opportunity
			$_conditions['category'] = array(1,2);
		}
		$_conditions['company_id'] = $this->employee_info['Company']['id'];
		// debug( $_conditions); exit;
		$results = $this->$keyword->find('all',array(
			'recursive' => -1,
			'conditions' =>$_conditions,
			'order' => array('end_date' => 'DESC')
		));
		$results = Set::combine($results,'{n}.'.$keyword.'.id','{n}.'.$keyword);
		$results = $this->setDataview($keyword,$results);
		//$this->Session->setFlash("Archived","success");
		if($ajax == 'ajax')
		{
			echo $data = json_encode(array('results' => $results, 'keyword' => $keyword));
			exit;
		}
		//$this->Session->setFlash("Done","success");
		$this->set(compact('results','keyword'));
		//debug($results);
		//exit;
	}
	function setDataview($keyword,$results)
	{
		$currentYear = date('Y',time());
		$dataView = array();
		foreach ($results as $key => $result) {
			//$key = $key + 1;
			$data = array(
				'id' => $key,
				'no.' => $key,
				'MetaData' => array()
			);
			$data['archivedMe'] = false;
			if($keyword == 'Activity')
			{
				$yearEndDate = date('Y', $result['end_date']);
				$data['name'] = $result['name'];
				$data['status'] =  $result['activated'];
				//$data['archived'] =  $result['archived'];
				$data['start_date'] = $this->formatDate($result['start_date']);
				$data['end_date'] = $this->formatDate($result['end_date']);
			}
			else
			{
				$yearEndDate = date('Y', strtotime($result['end_date']));
				$data['name'] = $result['project_name'];
				$data['status'] =  $result['category'];
				//$data['archived'] =  $result['category'] == 3 ? 1 : 0;
				$data['start_date'] = $this->formatDate(strtotime($result['start_date']));
				$data['end_date'] = $this->formatDate(strtotime($result['end_date']));
			}
			$data['class'] = '';
			if($data['end_date'] == '01-01-1970' && $data['start_date'] == '01-01-1970')
			{
				//do nothing
			}
			else //if($currentYear > $yearEndDate)
			{
				$data['class'] = 'archived';
			}
			$data['action.'] = $data['class'];
			$dataView[] = $data;
		}
		return $dataView;
	}
	function formatDate($date)
	{
		return !empty($date) ? date('d-m-Y',$date) : '';
	}
	function archivedMe($keyword = 'Activity', $ids = null, $ajax = false)
	{
		$this->loadModel('Activity');
		$this->loadModel('Project');
		$fields = $keyword == 'Activity' ? 'activated' : 'category' ;
		$value = $keyword == 'Activity' ? 2 : 3 ;
		$results = false;
		if(is_numeric($ids))
		{
			$data = array();
			$data[$fields] = $value;
            $this->$keyword->id = $ids;
			$this->$keyword->save($data);
			$results = true;
		}
		if(!$ajax)
		{
			echo $results;
			exit;
		}
	}
	function archivedRecords($keyword = 'Activity')
	{
		$this->loadModel('Activity');
		$this->loadModel('Project');
		$fields = $keyword == 'Activity' ? 'activated' : 'category' ;
		$value = $keyword == 'Activity' ? 2 : 3 ;
		$results = false;
		if(isset($this->data))
		{
			$data = $this->data['data'];
			if(!empty($data))
			{
				foreach($data as $key){
					$this->archivedMe($keyword, $key, true);
				}
				$results = true ;
			}
		}
		echo $results;
		exit;
	}
	function getActivityBetweenTimes($time)
	{
		$activities = $this->ActivityTask->find('all',array(
			'recursive' => -1,
			'conditions' =>array(
				// 'task_start_date <=' => $time,
				// 'task_end_date >=' => $time,
				// 'project_task_id' => array(null)
			),
			'fields' => array('DISTINCT activity_id')
		));
		return $activities;
	}
}
?>
