<?php
class ProjectsController extends ApiAppController {
    public $uses = array('Project');
    protected $roles = array(2, 3, 4);
    public $allowedFiles = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";
    public $allowedImageFiles = "jpg,jpeg,bmp,gif,png";

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');

    public function beforeFilter(){
        parent::beforeFilter();
        // $this->get_employee_info(); Da dung trong api_app_controller.
        // $this->getAllCompanyConfigs();
    }
    // This function used for user check having permission with project.
    // Permission included write_project,... please refer user_utility.php funciton user_can.
    public function user_can(){
        if (!empty($this->data['project_id']) && !empty($this->data['permission'])) {
            $permission = $this->data['permission'];
            $projectId = $this->data['project_id'];
            $result = $this->UserUtility->user_can($permission, $projectId);
            $this->ZAuth->respond('success', !!$result);
            return;
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    public function project_milestones($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModel('ProjectMilestone');
		$projectMilestones = $this->ProjectMilestone->find("all", array(
            'recursive' => -1,
            "conditions" => array('ProjectMilestone.project_id' => $project_id),
            'fields' => array(
				'ProjectMilestone.id', 
				'ProjectMilestone.project_milestone', 
				'ProjectMilestone.initial_date', 
				'ProjectMilestone.milestone_date', 
				'ProjectMilestone.effective_date', 
				'ProjectMilestone.validated', 
				'ProjectMilestone.part_id', 
				'ProjectMilestone.weight',
				'ProjectPart.title as part_title',
			),
            'order' => array('milestone_date' => 'ASC'),
            'joins' => array(
				array(
					'table' => 'project_parts',
					'alias' => 'ProjectPart',
					'conditions' => array(
						'ProjectMilestone.part_id = ProjectPart.id',
					),
					'type'=>'left'
				),
			),
		));
		foreach ($projectMilestones as $key => $item) {
			$item = $this->z_merge_all_key($item);
			foreach( array('initial_date','effective_date','milestone_date') as $k){
				if( !empty($item[$k]) ){
					$time = (($k == 'milestone_date') ?  strtotime($item[$k]) : $item[$k]);
					$item[$k] = date(API_DATE_FORMAT, $time);
				}
			}
			$projectMilestones[$key] = $item;
		}
		$this->ZAuth->respond('success', $projectMilestones);
		
	}
	
	/*
	 * function delete_milestone
	 * allow get/post
	 * input( ID/milestone_id )
	 */
    public function delete_milestone($milestone_id = null) {
		if( !empty( $this->data['id'] )) $milestone_id = $this->data['id'];
		if( !empty( $this->data['milestone_id'] )) $milestone_id = $this->data['milestone_id'];
		$user = $this->get_user();
		$this->loadModel('ProjectMilestone');
		$this->ProjectMilestone->id = $milestone_id;
		$project_milestone = $this->ProjectMilestone->read();
		if( empty($project_milestone) ) $this->data_incorrect('milestone item not found');
		$project_id = $project_milestone['ProjectMilestone']['project_id'];
		if( !$this->UserUtility->user_can('write_project', $project_id)) $this->data_incorrect('milestone_id not found');
        $this->ProjectMilestone->id = $milestone_id;
		$result = $this->ProjectMilestone->delete();
		if( $result) $this->ZAuth->respond('success', $milestone_id);
		$this->ZAuth->respond('failed', $milestone_id, 'delete_failed', 'NOT_SAVED');
		exit;
	}
    private function _milestone_validate_input($data = null) {
		unset($data['access_token']);
		unset($data['auth_code']);
		$this->loadModels('ProjectMilestone');
		if( empty($data['id']) && empty($data['project_id'])) $this->data_incorrect('Missing ID or project_id');
		$project_id = '';
		if( !empty($data['id'])){
			$item = $this->ProjectMilestone->find('first', array(
				'recursive' => -1,
				"conditions" => array('ProjectMilestone.id' => $data['id']),
			));
			$project_id = !empty($item['ProjectMilestone']['project_id']) ? $item['ProjectMilestone']['project_id'] : 'no_project';
			if( !$this->UserUtility->user_can('write_project', $project_id)) $this->data_incorrect('milestone_id not found');
		}
		if( !empty($data['project_id'])){
			$project_id = $data['project_id'];
			if( empty($data['id'])){
				//check project
				if( !$this->UserUtility->user_can('write_project', $project_id)) $this->data_incorrect();
			}else{ // khong cho phep update project
				if( empty($item)){
					$item = $this->ProjectMilestone->find('first', array(
						'recursive' => -1,
						"conditions" => array('ProjectMilestone.id' => $data['id']),
					));
				}
				if( $project_id != @$item['ProjectMilestone']['project_id'])
					$this->data_incorrect('Not allowed to change project_id');
			}
		}
		// $res = array();
		foreach( $data as $key => $value){
			$value = trim(strip_tags($value));
			if( empty( $value)) continue;
			switch( $key){
				// case 'id':
				// case 'project_id':
					// break;
				case 'initial_date':
				case 'milestone_date':
				case 'effective_date':
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('error', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), 'ERR_DATE_FORMAT');		
					if( $key == 'milestone_date')
						$value = $this->ProjectMilestone->convertTime($value);
					else
						$value = strtotime($this->ProjectMilestone->convertTime($value));
					break;
				case 'validated':
					$value = in_array( strtolower($value), array('yes', 'y', 1)) ? 1 : 0;
					break;
				case 'part_id':
					// if( $value){
						$this->loadModels('ProjectPart');
						$part = $this->ProjectPart->find('list', array(
							'recursive' => -1,
							'conditions' => array(
								'project_id' => $project_id,
								'id' => $value
							),
							'fields' => array('id', 'title') 
						));
						if( empty( $part)) $this->data_incorrect('part_id incorrect');
					// }
					break;
				case 'weight': 
					if( !is_numeric($value))
						 $this->data_incorrect('weight has to be a number');
					$value = intval($value);
					break;
			}
			$data[$key] = $value;
		}
		if( empty($data['weight'])){
			$listWeight = $this->ProjectMilestone->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('id', 'weight')
			));
			$maxWeight = 0;
			if(!empty($listWeight)){
				$maxWeight = max($listWeight);
			}
			$data['weight'] = $maxWeight + 1;
		}
		return $data;
	}
    public function update_milestone() {
		$user = $this->get_user();
		$this->loadModel('ProjectMilestone');
		$this->data = $this->_milestone_validate_input($this->data);
		// debug($this->data);
		// exit;
		if( empty($this->data['id'])){
			$this->ProjectMilestone->create();
		}
		$result = $this->ProjectMilestone->save($this->data);
		if( $result){
			$this->ProjectMilestone->recursive = -1;
			$item = $this->ProjectMilestone->read();
			$this->ZAuth->respond('success', $item['ProjectMilestone']);
		}
		$this->ZAuth->respond('failed', null, 'save_failed', 'NOT_SAVED');
		
	}
    public function project_phases($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		// if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModel('ProjectPhasePlan');
		$listPhasesbyProject = $this->ProjectPhasePlan->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectPhasePlan.project_id' => $project_id,
				// 'ProjectsPhase.activated' => 1,
			),
			'joins' => array(
				array(
					'table' => 'project_phases',
					'alias' => 'ProjectsPhase',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectsPhase.id',
					),
					'type'=>'inner'
				),
				array(
					'table' => 'project_phase_statuses',
					'alias' => 'ProjectsPhaseStatus',
					'conditions' => array(
						'ProjectPhasePlan.project_phase_status_id = ProjectsPhaseStatus.id',
					),
					'type'=>'left'
				),
				array(
					'table' => 'project_parts',
					'alias' => 'ProjectPart',
					'conditions' => array(
						'ProjectPhasePlan.project_part_id = ProjectPart.id',
					),
					'type'=>'left'
				)
			),
			'fields' => array(
				'ProjectPhasePlan.id', 
				'ProjectPhasePlan.project_planed_phase_id', 
				'ProjectPhasePlan.project_id', 
				'ProjectPhasePlan.phase_planed_start_date', 
				'ProjectPhasePlan.phase_planed_end_date', 
				'ProjectPhasePlan.phase_real_start_date', 
				'ProjectPhasePlan.phase_real_end_date', 
				'ProjectsPhase.activated', 
				'ProjectsPhase.name as project_phase_name', 
				'ProjectPhasePlan.project_part_id',
				'ProjectPhasePlan.project_phase_status_id',
				'ProjectsPhaseStatus.phase_status',
				'ProjectPhasePlan.project_part_id',
				'ProjectPart.title as project_part_name',
			)
		));
		// debug( $listPhasesbyProject); exit;
		foreach ($listPhasesbyProject as $key => $phase) {
			$phase = $this->z_merge_all_key($phase);
            //Ticket 495 hien thi them part trong list phase (neu phase co part)
            if($phase['project_part_name']!= null) {
                $phase['project_phase_name'] = $phase['project_phase_name']. ' ('.$phase['project_part_name'].')';
            }
			foreach( array('phase_planed_start_date','phase_real_start_date','phase_planed_end_date','phase_real_end_date') as $k){
				if( !empty($phase[$k]) ) 
					$phase[$k] = date(API_DATE_FORMAT, strtotime($phase[$k]));
			}
			$listPhasesbyProject[$key] = $phase;
		}
		$this->ZAuth->respond('success', $listPhasesbyProject);
		
	}
    public function current_phases($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('ProjectPhasePlan', 'ProjectPhaseCurrent');
		$projectCurrentPhases = $this->ProjectPhaseCurrent->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectPhaseCurrent.project_id' => $project_id,
				'ProjectPhase.activated' => 1,
			),
			'joins' => array(
				array(
					'table' => 'project_phases',
					'alias' => 'ProjectPhase',
					'conditions' => array(
						'ProjectPhaseCurrent.project_phase_id = ProjectPhase.id',
					),
					'type'=>'inner'
				),
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhaseCurrent.project_phase_id',
						'ProjectPhasePlan.project_id' => $project_id // de day chu k de len tren duoc
					),
					'type'=>'left'
				)
				
			),
			'fields' => array(
				'ProjectPhaseCurrent.project_id', 
				'ProjectPhase.name as project_phase_name', 
				'ProjectPhasePlan.phase_planed_end_date', 
				'ProjectPhasePlan.phase_real_end_date', 
			)
		));
		foreach ($projectCurrentPhases as $key => $phase) {
			$phase = $this->z_merge_all_key($phase);
			$phase['date_diff'] = 0;
			if( !empty($phase['phase_planed_start_date']) && !empty($phase['phase_planed_end_date'])) {
				$phase['date_diff'] = date_diff(
					date_create($phase['phase_real_end_date']), 
					date_create($phase['phase_planed_end_date'])
				);
				$phase['date_diff'] = $phase['date_diff']->format("%R%a");
			}
			foreach( array('phase_planed_start_date','phase_real_start_date','phase_planed_end_date','phase_real_end_date') as $k){
				if( !empty($phase[$k]) ) 
					$phase[$k] = date(API_DATE_FORMAT, strtotime($phase[$k]));
			}
			$projectCurrentPhases[$key] = $phase;
		}
		$this->ZAuth->respond('success', $projectCurrentPhases);
		
	}
    public function project_budget_synthesis($project_id = null) {
		$user = $this->get_user();
		// $company_id = $this->employee_info['Company']['id'];
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('Menu');
		
		$this->loadModel('ProjectBudgetSyn');
		$defaultVals = array(
			'internal_costs_budget' => 0,
			'external_costs_budget' => 0,
			'internal_costs_forecast' => 0,
			'external_costs_forecast' => 0,
		);
		$valBudgetSyns = $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
		$valBudgetSyns = array_merge($defaultVals, $valBudgetSyns);
		$budgetSyns = array(
			'total_costs_budget' => $valBudgetSyns['internal_costs_budget'] + $valBudgetSyns['external_costs_budget'],
			'total_costs_forecast' => $valBudgetSyns['internal_costs_forecast'] + $valBudgetSyns['external_costs_forecast'],
		);
		$this->ZAuth->respond('success', $budgetSyns);
	}
    public function project_budget_finance_plus($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('ProjectFinancePlusDetail' );
		
		$totalBudgetFinancePlus = $this->ProjectFinancePlusDetail->find('all', array(
			'recursive' => -1,
			'conditions'=> array(
				'ProjectFinancePlusDetail.project_id' => $project_id,
			),
			'fields' => array(
				'model',
				'sum(ProjectFinancePlusDetail.value) as total',
				'type',
			),
			'group' => array('type', 'model')
		));
		$budgetFinan = array(
			'inv' => array('budget' => 0, 'avancement' => 0 ),
			'fon' => array('budget' => 0, 'avancement' => 0 ),
			'finaninv' => array('budget' => 0, 'avancement' => 0 ),
			'finanfon' => array('budget' => 0, 'avancement' => 0 )
		);
		foreach( $totalBudgetFinancePlus as $k => $v){
			$type = $v['ProjectFinancePlusDetail']['type'];
			$model = $v['ProjectFinancePlusDetail']['model'];
			$total = $v['0']['total'];
			$budgetFinan[$type][$model] = $total;
		}
		$this->ZAuth->respond('success', $budgetFinan);
	}
    public function current_milestones($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('ProjectMilestone' );
		$miles_first_late = $this->ProjectMilestone->find('first',array(
			'recursive' => -1,
			'conditions'=> array(
				'ProjectMilestone.project_id' => $project_id,
				'ProjectMilestone.validated' => 0,
				'ProjectMilestone.milestone_date <' => date('Y-m-d'),
			),
			'fields' => array(
				'project_milestone', 
				'DATE_FORMAT(milestone_date, "%d-%m-%Y")as "milestone_date"',

			),
			'order' => array('ProjectMilestone.milestone_date' => 'DESC')
		));
		// debug($miles_first_late);
		$miles_first_late = !empty($miles_first_late) ? $this->z_merge_all_key($miles_first_late) : array();
		// debug($miles_first_late); exit;
		
		$miles_next = $this->ProjectMilestone->find('first',array(
			'recursive' => -1,
			'conditions'=> array(
				'ProjectMilestone.project_id' => $project_id,
				'ProjectMilestone.validated' => 0,
				'ProjectMilestone.milestone_date >=' => date('Y-m-d'),
			),
			'fields' => array(
				'project_milestone', 
				// 'milestone_date', 
				'DATE_FORMAT(milestone_date, "%d-%m-%Y")as "milestone_date"',

			),
			'order' => array('ProjectMilestone.milestone_date' => 'ASC')
		));
		$miles_next = !empty($miles_next) ? $this->z_merge_all_key($miles_next) : array();
		$result = array(
			'late_milestone' => $miles_first_late,
			'next_milestone' => $miles_next,
			'current' => date(API_DATE_FORMAT),
		);
		$this->ZAuth->respond('success', $result);
		
	}
    public function project_count_task_by_status($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$company_id = $this->employee_info['Company']['id'];
		$this->loadModels('ProjectTask','ProjectStatus');
		$this->ProjectTask->virtualFields['ed_unix'] = 'UNIX_TIMESTAMP(task_end_date)';
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $project_id,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$list_project_tasks = $this->ProjectTask->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array('id', 'task_title', 'project_id', 'task_status_id', 'task_end_date', 'ed_unix')
		));
		$project_task_id =  !empty($list_project_tasks) ? Set::classicExtract($list_project_tasks, '{n}.ProjectTask.id') : array();
		$task_refer_project = !empty($list_project_tasks) ? Set::combine($list_project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
		$project_used = array();
		$typeTaskStatus = $this->ProjectStatus->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'status')
		)); 
		$list_project_tasks = !empty($list_project_tasks) ? Set::combine($list_project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask', '{n}.ProjectTask.project_id') : array();
		$summary_tasks = array();
		$current_date = strtotime( date('Y-m-d', time()));
		foreach($list_project_tasks as $p_id => $list_tasks){
			$summary_tasks[$p_id]['count_task'] = count( $list_tasks );
			$summary_tasks[$p_id]['count_task_intime'] = 0;
			$summary_tasks[$p_id]['count_task_late'] = 0;
			$summary_tasks[$p_id]['count_by_stt'] = array();
			foreach( $list_tasks as $t_id => $task){
				$stt = $task['task_status_id'];
				if( empty( $stt)) continue;
				if( empty( $summary_tasks[$p_id]['count_by_stt'][$stt])) $summary_tasks[$p_id]['count_by_stt'][$stt] = 0;
				$summary_tasks[$p_id]['count_by_stt'][$stt] +=1;
				if( !empty( $typeTaskStatus[$stt]) &&  ($typeTaskStatus[$stt] == 'IP') && $task['ed_unix'] < $current_date){
					$summary_tasks[$p_id]['count_task_late'] += 1;
				} else{
					$summary_tasks[$p_id]['count_task_intime'] += 1;
				}
			}
		}
		$this->ZAuth->respond('success', $summary_tasks);
	}
    public function project_location($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$location = $this->Project->find('first',array(
			'recursive' => -1,
			'conditions'=> array(
				'Project.id' => $project_id
			),
			'fields' => array('id', 'project_name', 'address', 'latlng')
		));
		$location = !empty( $location) ? $location['Project'] :  array();
		$location['latlng'] = !empty( $location['latlng']) ? json_decode($location['latlng']) :  array();
		$this->ZAuth->respond('success', $location);
	}
    public function project_budget($project_id = null) {
		// $user = $this->get_user();
		$company_id = $this->employee_info['Company']['id'];
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('see_budget', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('Menu');
		$is_enable = $this->Menu->find('list', array(
			'recursive' => -1,
			'conditions'=> array(
				'Menu.display' => 1,
				'Menu.company_id' => $company_id ,
				'Menu.widget_id' => array('synthesis', 'finance_plus'),
				'Menu.model' => 'project',
				
			),
			'fields' => array(
				'Menu.widget_id',
				'Menu.display',
			)
		));
		if( !empty($is_enable['synthesis'])){
			$this->project_budget_synthesis($project_id);
		}elseif( !empty($is_enable['finance_plus'])){
			$this->project_budget_finance_plus($project_id);
		}else{
			$this->ZAuth->respond('budget_disabled', null, 'Budget is disabled', 'PR0001');
		}
	}
    public function list_pm_project($project_id = null) {
		$user = $this->get_user();
		// debug( $this->data);
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( $this->UserUtility->user_can('read_project', $project_id)){
			$pm_project = $this->Project->Employee->find('list', array(
				'recursive' => -1,
				'conditions'=> array(
					'Project.id' => $project_id
				),
				'fields' => array(
					'Project.project_manager_id',
					'Employee.full_name',
				),
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array('Employee.id = Project.project_manager_id'),
						'type' => 'right',
					),
				)
			));
			$pm_refer_project = $this->Project->Employee->find('list', array(
				'recursive' => -1,
				'conditions'=> array(
					'ProjectEmployeeManager.project_id' => $project_id
				),
				'fields' => array(
					'ProjectEmployeeManager.project_manager_id',
					'Employee.full_name',
					'ProjectEmployeeManager.type',
				),
				'joins' => array(
					array(
						'table' => 'project_employee_managers',
						'alias' => 'ProjectEmployeeManager',
						'conditions' => array('Employee.id = ProjectEmployeeManager.project_manager_id'),
						'type' => 'right',
					),
				)
			));
			if( empty( $pm_refer_project)) $pm_refer_project = array();
			if( empty( $pm_refer_project['PM'])) $pm_refer_project['PM'] = array();
			if( empty( $pm_refer_project['PM'])) $pm_refer_project['PM'] = array();
			$pm_refer_project['PM'] = $pm_refer_project['PM'] + $pm_project;
			$this->ZAuth->respond('success', $pm_refer_project);
		}
		$this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
    }

    // API get list of  multi-projects used in app's ListProjectScreen
    public function get_list_projects() {
        if( $this->RequestHandler->isPost() ){
            $data = $_POST;
            $user = $this->get_user();
			$company_id = $this->employee_info['Company']['id'];
            $role = $this->employee_info['Role']['name'];
            // validate company and user role
            if(in_array($role, array('admin','pm', 'conslt'))){
                
                $query = array(
                    'conditions'=> array(
                        'Project.company_id' => $company_id
                    ),
                    'fields' => array(
                        'Project.id', 
                        'Project.project_name',
                        'Project.manual_progress', 
                        // 'LogSystem.description', 
                        // 'LogSystem.updated', 
                    ),
                    'contain' => array(
                        'ProjectGlobalView'=> array('id', 'attachment'),//1
                        'ProjectEmployeeManager' => array('project_manager_id'), //2
                        'ProjectAmr' => array('id', 'weather'), //3
                        'ProjectAmrProgram' => array('id', 'amr_program', 'color'), //4
                        'MFavorite' => array(
                            'conditions' => array(
                                'MFavorite.owner_id' => $user['id'],
                                'MFavorite.modelType' => 'Project'
                            ),
                            'fields' => array('id'),
                            'limit' => 1,
                        ),
                        'MLike' => array(
                            'conditions' => array(
                                'MLike.owner_id' => $user['id'],
                                'MLike.modelType' => 'Project'
                            ),
                            'fields' => array('id'),
                            'limit' => 1,
                        ),
                    ),
                    // 'joins' => array(
                    //     array(
                    //         'table' => 'log_systems',
                    //         'alias' => 'LogSystem',
                    //         'conditions' => array('Project.id = LogSystem.model_id'),
                    //         'type' => 'inner',
                    //         'order' => array('LogSystem.updated' => 'DESC'),
                    //         // 'limit' => 1,
                    //     ),
                    // ),
                    // 'order' => array('LogSystem.updated' => 'DESC'),
                    // 'group' => array('Project.id'),
                );
                
                $seeAllProject = !!$this->companyConfigs['see_all_projects'] || !!$this->employee_info['CompanyEmployeeReference']['see_all_projects'];
                // Debug(!$seeAllProject);
                // Debug($role);
                $employee_id = $user['id']; 
                $profitCenterId = $this->employee_info['Employee']['profit_center_id'];   
                $listProjectIds = array();
                //them dieu kien check de hien thi project doi voi user co quyen Read Access project do. Update by QuanNV 29/06/2019
                if ($role == 'pm' && !$seeAllProject) {                    
                    $listProjectIds = $this->getProjectsByPM();                   
                }
                if ($role == 'conslt') {                   
                    $listProjectIds = $this->getProjectsByConslt();
                }
                // Debug(array_unique($listProjectIds));
                if (!$seeAllProject && $role != 'admin') {
                    $query['conditions']['Project.id'] = array_unique($listProjectIds);
                //     // $totalQuery['conditions']['Project.id'] = $listProjectIds;
                } 

                // Filter project by category
                if(isset($data['category'])) {
                    $cate = $data['category'];
                    $query['conditions']['Project.category'] = $cate==5? array(1,2): $cate;
                }
                // Filter project by program
                if(isset($data['amr_program_id'])) {
                    $amrProgram = $data['amr_program_id'];
                    $query['conditions']['Project.project_amr_program_id'] = $amrProgram;
                }
                $this->Project->recursive =-1;               
                $this->Project->Behaviors->attach('Containable');

                // $total = $this->Project->find('count', $totalQuery);
                $listProjects = $this->Project->find('all', $query);
                
                $listProjects = $this->getPathImages($this->employee_info['Company'], $listProjects); 
                // debug($listProjects);

                // exit;
                // Count like number of project
                $projectIDs = !empty($listProjects) ? (Set::classicExtract($listProjects, '{n}.Project.id') ): array();
                $projectIDs = array_unique($projectIDs);
                // Get Log of projects
                $this->loadModel('LogSystem');
                $listLogSystem = $this->LogSystem->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'model_id' => $projectIDs,
                    ),
                    'fields' => array(
                        'LogSystem.updated', 
                        'LogSystem.description', 
                        'LogSystem.model_id', 
                    ),
                    'order' => array('LogSystem.updated' => 'DESC')

                ));
                // Debug($lis tLogSystem);
                // exit;
                $this->loadModel('MLike');
                $countLike = $this->MLike->find('all', array(
                    'conditions' => array(
                        'modelType' => 'Project',
                        'modelId' => $projectIDs
                    ),
                    'fields'=> array('Mlike.modelId', "count('Mlike.id') as count"), 
                    'group' => array('modelId'), 
                ));
                $countLike = !empty($countLike) ? (Set::combine($countLike, '{n}.MLike.modelId', '{n}.0.count')) : array();

                // Count favorite number of project
                $this->loadModel('MFavorite');
                $countFavorite = $this->MFavorite->find('all', array(
                    'conditions' => array(
                        'modelType' => 'Project',
                        'modelId' => $projectIDs
                    ),
                    'fields' => array('MFavorite.modelId', "count('MFavorite.id') as count"),
                    'group' => array('modelId')
                ));
                $countFavorite = !empty($countFavorite) ? (Set::combine($countFavorite, '{n}.MFavorite.modelId', '{n}.0.count')): array();

                // Get Project progress
                // $this->getAllCompanyConfigs(); Remove because include in api_app_controller.beforfilter

                $progress = $this->getProjectProgress($projectIDs);
                //Lay duy nhat 1 comment cua moi project.
                $result = $this->merge_project_comment($listProjects, $listLogSystem);
                $total = count($result); //get all projects of user.
                
                // Response data to client
                $this->ZAuth->respond('success', array(
                    'total' => $total,
                    'projects' => $result,
                    'countLike' => $countLike,
                    'countFavorite' => $countFavorite,
                    'progress' => $progress,
                    'progressMethod' => $this->companyConfigs['project_progress_method'],
                    // 'manualConsumed' => $this->companyConfigs['manual_consumed']
                ));
            }

			
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    // API get infor of one project by project_id used in app's ProjectDetail
    public function get_project() {
        if($this->RequestHandler->isPost()){
            $data = $_POST;
            $user = $this->get_user();
            $company_id = $this->employee_info['Company']['id'];
            $project_id = $data['project_id'];
            if(in_array($this->employee_info['Role']['name'], array('admin', 'pm'))){
                
                $query = array(

                    'conditions'=> array(
                        'Project.company_id' => $company_id,
                        'Project.id' => $project_id
                    ),
                    'fields' => array(
                        'Project.id', 
                        'Project.project_name',
                        'Project.manual_progress' 
                    ),
                    'contain' => array(
                        'ProjectGlobalView'=> array('id', 'attachment'),//1
                        'ProjectEmployeeManager' => array(
                            'conditions' => array(
                                'ProjectEmployeeManager.project_id' => $project_id,
                                'ProjectEmployeeManager.type' => 'PM',
                                'ProjectEmployeeManager.is_profit_center' => 0
                            ),
                            'fields'=> array('project_manager_id')), //2
                        'ProjectAmr' => array('id', 'weather'), //3
                        'ProjectAmrProgram' => array('id', 'amr_program', 'color'), //4
                        'MFavorite' => array(
                            'conditions' => array(
                                'MFavorite.owner_id' => $user['id'],
                                'MFavorite.modelType' => 'Project'
                            ),
                            'fields' => array('id'),
                            'limit' => 1,
                        ),
                        'MLike' => array(
                            'conditions' => array(
                                'MLike.owner_id' => $user['id'],
                                'MLike.modelType' => 'Project'
                            ),
                            'fields' => array('id'),
                            'limit' => 1,
                        ),
                    ),
                );
            }

            $this->Project->recursive = 0;
            $this->Project->Behaviors->attach('Containable');
            $project = $this->Project->find('first', $query);
           
            // Insert full path of Image to Project
            $project = $this->getPathImage($this->employee_info['Company'], $project);

            $this->loadModel('MLike');
            $countLike = $this->MLike->find('count', array(
                'conditions' => array(
                    'modelType' => 'Project',
                    'modelId' => $project_id
                ),
                // 'fields'=> array('Mlike.modelId', "count('Mlike.id') as count"), 
                // 'group' => array('modelId'), 
            ));

            // Count favorite number of project
            $this->loadModel('MFavorite');
            $countFavorite = $this->MFavorite->find('count', array(
                'conditions' => array(
                    'modelType' => 'Project',
                    'modelId' => $project_id
                ),
                // 'fields' => array('MFavorite.modelId', "count('MFavorite.id') as count"),
                // 'group' => array('modelId')
            ));
            // Get project progress
            // $this->getAllCompanyConfigs();

            $progress = $this->getProjectProgress($project_id);
            // Response data to client
            $this->ZAuth->respond('success', array(
                'project' => $project,
                'countLike' => $countLike,
                'countFavorite' => $countFavorite,
                'completed' => $progress[$project_id]['Completed']
            ));
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    // Get all permission of user in a project
    public function get_permission_in_project() {
        if( $this->RequestHandler->isPost() ){
            $data = $_POST;
            if(empty($data['project_id'])) {
                $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
                return;
            }
            $project_id = $data['project_id'];
            $user = $this->get_user();
            $permission = $this->UserUtility->get_permission($project_id);

            // Debug($permission);
            // Check permission voi project truoc -> Permission deny;
            // function here.


            // Tam thoi xu ly de chay, sau se dung bien $permission toan cuc.
            // $permission['write_project'] = $this->UserUtility->user_can('write_project', $project_id);
            // $permission['read_project'] = $this->UserUtility->user_can('read_project', $project_id);

            $this->ZAuth->respond('success', $permission, 'Permission in project-'.$project_id, '0');
            return;
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    // API get list comments of  multi-projects used in app's ListProjectScreen
    public function get_list_comments() {
        if( $this->RequestHandler->isPost() ){
            $data = $_POST;
            $user = $this->get_user();

			$company_id = $this->employee_info['Company']['id'];
            $comment_model = $data['comment_model'];
            $role = $this->employee_info['Role']['name'];
            // validate company and user role
            if(in_array($this->employee_info['Role']['name'], array('admin','pm'))){
                $query = array(
                    'recursive' => -1,
                    'conditions'=> array(
                        'Project.company_id' => $company_id,
                        'Project.project_name is not NULL',
                        'LogSystem.description is not NULL',
                        // 'LogSystem.model'=> $comment_model
                    ),
                    'fields' => array(
                        'Project.id', 
                        'Project.project_name', //10
                        // 'Project.updated', //

                        'LogSystem.description', //12
                        'LogSystem.updated', //11 & 13
                        'LogSystem.model',
                        'LogSystem.employee_id', //9
                        'LogSystem.id', // key

                    ),
                    // 'contain' => array(
                    //     'ProjectEmployeeManager' => array('ProjectEmployeeManager.id','project_manager_id'), //2
                    // ),
                    'joins' => array(
                        array(
                            'table' => 'log_systems',
                            'alias' => 'LogSystem',
                            // 'type' => 'LEFT',
                            'conditions' => array(
                                'Project.id = LogSystem.model_id',
                                'LogSystem.model'=> $comment_model
                            ),
                            'type' => 'inner',
                            // 'order' => array('LogSystem.updated DESC'),
                            'order' => array('LogSystem.updated' => 'DESC'),
                            // 'limit' => 1,
                        ),
                    ),
                    // 'limit' => 1,
                    'order' => array('LogSystem.updated' => 'DESC'),
                    // 'group' => array('Project.id'),
                );
                
              

                // if (isset($data['limit']) && $data['limit']>0 && $data['limit']<=20) {
                //     $query['limit']=$data['limit'];
                // } else {
                //     $query['limit']=20;
                // }
                // if (isset($data['page'])) {
                //     $query['page']=$data['page'];
                // }
                // if (isset($data['offset'])) {
                //     $query['offset']=$data['offset'];
                // }
                $seeAllProject = !!$this->companyConfigs['see_all_projects'] || !!$this->employee_info['CompanyEmployeeReference']['see_all_projects'];
                // Debug($seeAllProject);
                // Debug($role);
                $employee_id = $user['id']; 
                $profitCenterId = $this->employee_info['Employee']['profit_center_id'];   
                $listProjectIds = array();
                //them dieu kien check de hien thi project doi voi user co quyen Read Access project do. Update by QuanNV 29/06/2019
                if ($role == 'pm' && !$seeAllProject) {                    
                    $listProjectIds = $this->getProjectsByPM();                   
                }
                if ($role != 'admin' && !$seeAllProject) {
                    $query['conditions']['Project.id'] = $listProjectIds;
                } 
                // $query['conditions']['Project.id'] = $listProjectIds;
                if(isset($data['category'])) {
                    $cate = $data['category'];
                    $query['conditions']['Project.category'] = $cate==5? array(1,2): $cate;
                }
                $this->Project->recursive =-1;               

                $listComments = $this->Project->find('all', $query);
                //Lay duy nhat 1 comment cua moi project.
                $result = $this->filter_uni_comment($listComments);
                
                $this->ZAuth->respond('success', array(
                    'Total' => count($result),
                    'Comments'=>$result
                ));
                return;
            }

            $this->ZAuth->respond('error', array() ,__('Permission deney'),301);
        }
    }
    // Ham nay dung de lay duy nhat 1 comment cua moi project.
    private function filter_uni_comment($listComments) {
        $id = array();
        $result = array();
        foreach($listComments as $k => $v) {
            if(!in_array($v['Project']['id'], $id)) {
                array_push( $id, $v['Project']['id']);
                array_push($result, $v);
            }
            // Debug($v['Project']['id']);
        }
        return $result;
    }
    // Ham nay dung de noi comment vao project.
    private function merge_project_comment($listProjects, $listComments) {
        $result = array();
        foreach($listProjects as $k => $v) {
            if(empty($listComments[$v['Project']['id']])) {
                $v['LogSystem']['description'] = 'N/A';
                $v['LogSystem']['updated'] = 'N/A';
            } else {
                $v['LogSystem']['updated'] = key($listComments[$v['Project']['id']]);
                // $v['LogSystem']['description'] = array_values($listComments[$v['Project']['id']][0]);
                $v['LogSystem']['description'] = $listComments[$v['Project']['id']][$v['LogSystem']['updated']];
            }
            
            array_push($result, $v);
            // Debug($v['Project']['id']);
        }
        return $result;
    }
    // API toggle like  a project
    public function toggle_like(){
		if($this->RequestHandler->isPost()) {
            $data = $_POST;
            $employee_id = $this->employee_info['Employee']['id'];
            if(isset($data['project_id']) && isset($employee_id)) {
                $project_id = $data['project_id'];
                $this->loadModel('MLike');
                // if($this->checkRole(false, $project_id)) {
                if ($this->UserUtility->user_can('read_project', $project_id)) {
                    $result = $this->MLike->toggle_like($employee_id, $project_id);
                    $this->ZAuth->respond('success', array('liked'=> $result['liked'], 'countLikes' => $result['countLikes']));
                } else {
                    $this->ZAuth->respond('fail', null, 'permission_deny', '301');
    
                }

            }
        }
		$this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
	}
    // API toggle favorite  a project
	public function toggle_favorite(){
        if($this->RequestHandler->isPost()) {
            $data = $_POST;
            $employee_id = $this->employee_info['Employee']['id'];
            if(isset($data['project_id']) && isset($employee_id)) {
                $project_id = $data['project_id'];
                $this->loadModel('MFavorite');
                // Debug($this->checkRole(false, $project_id)); exit;
                // if ($this->checkRole(false, $project_id)) {
                if ($this->UserUtility->user_can('read_project', $project_id)) {
                    $result = $this->MFavorite->toggle_favorite($employee_id, $project_id);
                    $this->ZAuth->respond('success', array('favorite'=> $result));
    
                } else {
                    $this->ZAuth->respond('fail', null, 'permission_deny', '301');
                }
            }

        }
        $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
	}
    // API get progress of a project
    public function get_project_progress(){
        if($this->RequestHandler->isPost()) {
            $data = $_POST;
            $company_id = $this->employee_info['Company']['id'];
            if(isset($data['project_id']) && isset($company_id)) {
                $project_id = $data['project_id'];
                $this->getAllCompanyConfigs();
                $result = $this->getProjectProgress($project_id);
                $this->ZAuth->respond('success', $result);
            }
        }
        $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');

	}
    /* Add new Project 
	 * Re-written from function add_new_project_popup file projects_controller.php
	 */
	public function new_project(){
        if($this->RequestHandler->isPost() && isset($_POST['project_name']) && isset($_POST['project_amr_program_id'])) {
            $data = $_POST;
            $user = $this->get_user();;
            $employee_info = $this->employee_info;
            $canAddProject = (($employee_info['Role']['id'] == 2) || (!empty($employee_info['Employee']['create_a_project'])));
            $company_id = $employee_info["Company"]["id"];
            // Check Role
           if(!$canAddProject || empty($data) || empty( $company_id) ) {
               $this->ZAuth->respond('fail', null, 'permission deny', 0);
               return;
           }
           $this->loadModels('ProjectEmployeeManager');
           /**
            * Xu ly project manager. Xoa cac dong = 0
            */
            if (!empty($data['project_employee_manager'])) {
                $data['project_employee_manager'] = array_unique($data['project_employee_manager']);
                if (($key = array_search(0, $data['project_employee_manager'])) !== false) {
                    unset($data['project_employee_manager'][$key]);
                }
            }
            // $this->getAllCompanyConfigs();
            // $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
            
            // Debug($employee_info);
            // Debug($canAddProject);
            // Debug($this->data);
            // Debug($this->companyConfigs);
            // Debug($adminSeeAllProjects);
            // exit;

            /**
             * Xu ly read access. Xoa cac dong = 0
             */
            // if (!empty($this->data['read_access'])) {
            //     $this->data['read_access'] = array_unique($this->data['read_access']);
            //     if (($key = array_search(0, $this->data['read_access'])) !== false) {
            //         unset($this->data['read_access'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list. Xoa cac dong = 0
             */
            // if (!empty($this->data['technical_manager_list'])) {
            //     $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
            //     if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
            //         unset($this->data['technical_manager_list'][$key]);
            //     }
            // }


            /**
             * Xu ly project manager, technical_manager_list.
             */
            // Gan gia tri dau tien cho field project_manager_id.
            $first_pm = reset($data['project_employee_manager']);
            if(!empty($employee_info['Employee']['create_a_project'])){
                $data_project['Project']['project_manager_id'] = $employee_info['Employee']['id'];
            }else{
                $data_project['Project']['project_manager_id'] = !empty($data['project_employee_manager']) ? $first_pm : '';
            }

            //Gan gia tri dau tien cho field technical_manager_id. Cho nay gay loi luu thieu gia tri vao table project_employee_manager khi moi tao project. Quan comment lại. QuanNV 16/07/2019.
            // $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
            //Khong luu Technical_manager vào table project. Chuyen sang luu truc tiep vao table project_employee_manager. QuanNV 16/07/2019
            // $this->data["Project"]["technical_manager_id"] = '';

            //Save to Project
            $this->Project->create();
            App::import("vendor", "str_utility");
            $data_project["Project"]["project_name"] = $data['project_name'];
            $data_project["Project"]["company_id"] = $company_id;
            $data_project["Project"]["project_amr_program_id"] = $data['project_amr_program_id'];
            // $data["Project"]["start_date"] = isset($this->data["Project"]["start_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["start_date"]) : null;
            // $data["Project"]["end_date"] = isset($this->data["Project"]["end_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["end_date"]) : null;
            $data_project['Project']['update_by_employee'] = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
            $data_project['Project']['category'] = 2; // when create project, default project status = 2 (Opportunity).
            $data_project['Project']['weather'] = 'sun';
            $data_project['Project']['rank'] = 'mid';
            $data_project['Project']['last_modified'] = time();
    
            $result = $this->Project->save($data_project);
            // Debug($this->Project->id);
            // Debug($result);
            // Debug($data_project);      
            // Debug($data);      
            // exit;

            if( $result){
                // $this->writeLog($this->data, $this->employee_info, 'Create project #' . $this->Project->id, $company_id);
                $pid = $this->Project->id;
                 //Begin Create arm project
                $data_arm['ProjectAmr']['project_id'] = $pid;
                $data_arm['ProjectAmr']['weather'] = 'sun';
                $data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['planning_weather'] = 'sun';
                $data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['organization_weather'] = 'sun';
                $data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
                $data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
                $this->loadModel('ProjectAmr');
                $this->ProjectAmr->create();
                $this->ProjectAmr->save($data_arm);
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                if (!empty($data['project_employee_manager'])) {
                    $this->loadModel('ProjectEmployeeManager');
                    foreach ($data['project_employee_manager'] as $value) {                     
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            'project_id' => $pid,
                            'type' => 'PM',
                            'activity_id' => 0
                        );
                        // if ($value != $data_project['Project']['project_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $result[] = $this->ProjectEmployeeManager->save($dataRefers);
                        // }
                    }   
                }
                // if (!$adminSeeAllProjects) {
                //     if (!empty($this->data['read_access'])) {
                //         foreach ($this->data['read_access'] as $value) {
                //             $value = explode('-', $value);
                //             $is_profit = empty($value[1]) ? 0 : 1;
                //             $dataRefers = array(
                //                 'project_manager_id' => $value[0],
                //                 'project_id' => $pid,
                //                 'type' => 'RA',
                //                 'activity_id' => 0,
                //                 'is_profit_center' => $is_profit
                //             );
                //             $this->ProjectEmployeeManager->create();
                //             $result[] = $this->ProjectEmployeeManager->save($dataRefers);
                //         }
                //     }
                // }
                // if (!empty($this->data['technical_manager_list'])) {
                //     foreach ($this->data['technical_manager_list'] as $value) {
                //         $dataRefers = array(
                //             'project_manager_id' => $value,
                //             'project_id' => $pid,
                //             'type' => 'TM',
                //             'activity_id' => 0
                //         );
                //         if ($value != $this->data['Project']['technical_manager_id']) {
                //             $this->ProjectEmployeeManager->create();
                //             $result[] = $this->ProjectEmployeeManager->save($dataRefers);
                //         }
                //     }
                // }
            
                /*** Check setting Phase de auto tao Phase. Ticket #801 */
 //Comment to re-open              
                $this->loadModel('ProjectPhase');
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
                    $this->loadModel('ProjectPhasePlan');
                    $i = 1;
                    foreach ($listPhaseDefault as $keyPhase => $idPhaseDefault){
                        $newRecord['project_id'] = $pid;
                        $newRecord['project_planed_phase_id'] = $idPhaseDefault;
                        $newRecord['progress'] = 0;
                        $newRecord['weight'] = $i;
                        $newRecord['phase_planed_start_date'] = '0000-00-00';
                        $newRecord['phase_real_start_date'] = '0000-00-00';
                        $newRecord['phase_planed_end_date'] = '0000-00-00';
                        $newRecord['phase_real_end_date'] = '0000-00-00';
                        $this->ProjectPhasePlan->create();
                        $this->ProjectPhasePlan->save($newRecord);
                        $i++;
                    }
                    // Debug($i);
                }
                /*** End ticket #801 */
            }

            if( !empty($pid) && !empty($_FILES)) {
                
                // $path = $this->_getPathProjectGlobalView($pid);
                $path = $this->getPath('globalviews', $this->employee_info['Company']);
                
                App::import('Core', 'Folder');
                new Folder($path, true, 0777);
                if (file_exists($path)) {
                    $_FILES['FileField'] = array();
                    if(!empty($_FILES)){
                        $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                        $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                        $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                        $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                        $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
                    }
                    unset($_FILES['file']);
                    $this->MultiFileUpload->encode_filename = false;
                    $this->MultiFileUpload->uploadpath = $path;
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedImageFiles;
                    $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                    $attachment = $this->MultiFileUpload->upload();
                } else {
                    $attachment = "";
                    // $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
                }
                if (!empty($attachment)) {
                    $this->loadModel('ProjectGlobalView');
                    $attachment = $attachment['attachment']['attachment'];
                    $this->ProjectGlobalView->create();
                    $last = $this->ProjectGlobalView->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'attachment'),
                        'conditions' => array('project_id' => $pid)));
                    if ($last) {
                        $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                        @unlink($path . $last['ProjectGlobalView']['attachment']);
                    }
                    $result_upload = $this->ProjectGlobalView->save(array(
                        'project_id' => $pid,
                        'attachment' => $attachment,
                        'is_file' => 1
                    ));
                    // if ($result_upload) {
                    //     if($this->MultiFileUpload->otherServer == true){
                    //         $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['ProjectGlobalView']['attachment'], $attachment, '/project_global_views/index/' . $pid);
                    //     }
                    //     // $this->Session->setFlash(__('Saved', true), 'success');
                    // } else {
                    //     @unlink($path . $attachment);
                    //     // $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                    //     if($this->MultiFileUpload->otherServer == true){
                    //         $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_global_views/index/' . $pid);
                    //     }
                    // }
                } else {
                    // $this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
                }
                
            }
            $this->ZAuth->respond('success', array(
                'success' => !empty( $result) ? 'success' : 'failed',
                'data' => !empty( $result) ? $result : '',
                'upload' => !empty( $result_upload) ? 'success' : 'failed',
                'data_upload' => !empty( $result_upload) ? $result_upload : '',
            ));

        }
    
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');			
    }
    public function attachment($project_id = null) {
        
        $this->loadModel('ProjectGlobalView');
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'fields' => array('id', 'project_id', 'attachment','is_file','is_https'),
            "conditions" => array('project_id' => $project_id)));
        // Debug($projectGlobalView);
        if ($projectGlobalView) {
            $link = $this->getPath('globalviews', $this->employee_info['Company'])
            . $projectGlobalView['ProjectGlobalView']['attachment'];
            // Debug($link);
            if (empty($link)) {
                $link = '';
            } else {
                if (!file_exists($link) || !is_file($link)) {
                    $link = '';
                }
                $info = pathinfo($link);
                // Debug($info);
                $this->view = 'Media';
                $params = array(
                    'id' => !empty($info['basename']) ? $info['basename'] : '',
                    'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
                    'name' => !empty($info['filename']) ? $info['filename'] : '',
                    'mimeType' => array(
                        'bmp' => 'image/bmp',
                        'ppt' => 'application/vnd.ms-powerpoint',
                        'pps' => 'application/vnd.ms-powerpoint',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                    ),
                    'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
                );
                // if (!empty($this->params['url']['download'])) {
                //     $params['download'] = true;
                // }
                $this->set($params);
                return;
            }
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    public function get_project_arm_programs() {
        if($this->RequestHandler->isPost()) {
            $data = $_POST;
            $results = $this->Project->ProjectAmrProgram->find('all', array(
                'recursive' => -1,
                'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program', 'ProjectAmrProgram.color'),
                'conditions' => array('ProjectAmrProgram.company_id' => $this->employee_info["Company"]["id"])));
            $returnResults = array();
                foreach( $results as $k => $value){
                array_push( $returnResults, $value['ProjectAmrProgram']);
            }
                $this->ZAuth->respond('success', $returnResults);
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }

    // Ref from line: 4365 projects_controller.php
    public function get_company_pm() {
        if($this->RequestHandler->isPost()) {
            $data = $_POST;
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $this->employee_info["Company"]["id"],
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            // $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $pm = array();
            foreach( $projectManagers as $k => $employee){
                if($employee['Employee']['id'] != null ) {
                    array_push($pm, array_merge(
                        $employee['Employee'], 
                        $employee['0']
                    ));
                }
            }
            $this->ZAuth->respond('success', $pm);
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    // Utility Get path image of one project
    protected function getPathImage($company, $project) {
        $path = $this->getPath('globalviews', $company);
        if (!empty($project)) {

            $link = '';
            $project_id = $project['Project']['id'];
            $att = !empty($project['ProjectGlobalView'][0]['attachment']) ? ($path.$project['ProjectGlobalView'][0]['attachment']) : '';
            if (preg_match('/\.(jpg|jpeg|bmp|gif|png)$/i', $att) && file_exists($att)) {
                $link = Router::url(array(
                    // 'plugin' => false, 
                    'controller' => $this->params['controller'], 
                    'action' => 'attachment', 
                    $project_id
                ), true);
            }
            
            $project['ProjectGlobalView'][0]['link'] = $link;
			
        }
        return $project;
    }
    // Utility Get path image of multi-projects
    protected function getPathImages($company, $projects = null) {
        // debug($projects);
        $path = $this->getPath('globalviews', $company);
        // debug($path);

        if (!empty($projects)) {
            foreach( $projects as $k => $project){
				$link = '';
				$project_id = $projects[$k]['Project']['id'];
				$att = !empty($projects[$k]['ProjectGlobalView'][0]['attachment']) ? ($path.$projects[$k]['ProjectGlobalView'][0]['attachment']) : '';
				if (preg_match('/\.(jpg|jpeg|bmp|gif|png)$/i', $att) && file_exists($att)) {
					$link = Router::url(array(
						// 'plugin' => false, 
						// 'controller' => 'project_global_views', 
						'controller' => $this->params['controller'], 
						'action' => 'attachment', 
						$project_id
					), true);
				}
				
                $projects[$k]['ProjectGlobalView'][0]['link'] = $link;
			}
        }
        return $projects;
    }
    // Utility get path progress of multi-projects or a project by prọjectId.
    protected function getProjectProgress($projectIds){
		$company_id = $this->employee_info['Company']['id'];
		$result = $this->Project->caculateProgress($projectIds, $this->companyConfigs, $company_id);
		return $result;
	}
	/* 
	 * Follow: project_tasks_preview_controller.php => function _getTeamEmployees
	 * input: project_id, end_date
	 */
	public function project_resources($project_id=null){
		$user = $this->get_user();
        $company_id = $this->employee_info['Company']['id'];
		if( !empty( $this->data['project_id'] )) $project_id = trim($this->data['project_id']);
		$start_date = !empty($this->data['start_date']) ? trim($this->data['start_date']) : '';
		$end_date = !empty($this->data['end_date']) ? trim($this->data['end_date']) : '';
		// if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('ProjectTask', 'Menu', 'ProjectTeam', 'ProjectTaskEmployeeRefer');
		$this->ProjectTeam->Behaviors->attach('Containable');
		$project = $this->Project->find("first",
			array(
				'conditions' => array('Project.id' => $project_id)
			)
		);
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
			$endDate = date('Y-m-d');
			if( !empty($end_date)){
				$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $end_date, $matches)) $this->ZAuth->respond('error', $end_date, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $end_date), 'ERR_DATE_FORMAT');
					$endDate = $this->ProjectTask->convertTime($end_date);
					// debug( $endDate); exit;
			}
			$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$endDate . '"), 1, 0)';
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
                    'company_id' => $company_id
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
        // return $employees;
		foreach ($employees as $key => $item) {
			$item = $this->z_merge_all_key($item);
			$employees[$key] = $item;
		}
		$this->ZAuth->respond('success', $employees);
	}
}