<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
/**
 * ProjectPhasePlansController
 *
 * @package Yourpmstrategy
 * @author testing
 * @copyright 2015
 * @version $Id$
 * @access public
 */
class ProjectPhasePlansPreviewController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectPhasePlansPreview';
    var $uses = array('ProjectPhasePlan');
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('GanttV2Preview', 'Time');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
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
        $phase = isset($this->params['url']['phase']) ? $this->params['url']['phase'] : null;
        if($phase) {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id,'ProjectPhasePlan.id' => $phase);
            $condPhaseProject = array('Project.id' => $project_id, 'Project.project_phase_id' => $phase);
        } else {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id);
            $condPhaseProject = array('Project.id' => $project_id);
        }
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
		$project_milestone = $this->ProjectPhasePlan->Project->find("first", array(
				'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'part_id', 'order' => 'milestone_date ASC')),
				"fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'), 'conditions' => $condPhaseProject
		));
        $this->set('projectName', $project_milestone);
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
        $parts = $this->ProjectPhasePlan->ProjectPart->find('list', array('order' => array('weight' => 'ASC')));
        $getDatas = $this->_taskCaculates($project_id, $projectName['Project']['company_id'], $phasePlans);
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
                'path' => 'phase_vision_view',
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('id', 'params')
        ));
        if(!empty($this->params['url']['type'])){
            $type = $this->params['url']['type'];
            if(!empty($default)){
                $this->HistoryFilter->id = $default['HistoryFilter']['id'];
            } else {
                $this->HistoryFilter->create();
            }
            $this->HistoryFilter->save(array('params' => $type, 'path' => 'phase_vision_view', 'employee_id' => $this->employee_info['Employee']['id']));
        } else {
            $type = !empty($default) ? $default['HistoryFilter']['params'] : 'year';
        }
		
		$this->loadModel('Menu');
		$lanPhase = $this->Menu->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'widget_id' => 'phase',
				'company_id' => $this->employee_info['Company']['id'],
				'model' => 'project'
			),
			'fields' => array('id', 'name_eng', 'name_fre')
		));
		$lanTask = $this->Menu->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'widget_id' => 'task',
				'company_id' => $this->employee_info['Company']['id'],
				'model' => 'project'
			),
			'fields' => array('id', 'name_eng', 'name_fre')
		));
        $this->set(compact('phasePlans', 'display', 'project_id', 'parts', 'phaseCompleted', 'projectTasks', 'taskCompleted', 'onClickPhaseIds', 'callProjects', 'type', 'lanPhase', 'lanTask'));
        if(isset($this->params['url']['ajax']) && $this->params['url']['ajax'] == 1) {
            $this->render('phase_vision_ajax');
        }
        if( $new_gantt )$this->render('phase_vision_new');
    }
	public function saveHistoryByAjax($type){
		$this->loadModel('HistoryFilter');
		if(!empty($type)){
			$default = $this->HistoryFilter->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'path' => 'phase_vision_view',
					'employee_id' => $this->employee_info['Employee']['id']
				),
				'fields' => array('id', 'params')
			));
			if(!empty($default)){
				$this->HistoryFilter->id = $default['HistoryFilter']['id'];
			} else {
				$this->HistoryFilter->create();
			}
			if($this->HistoryFilter->save(array('params' => $type, 'path' => 'phase_vision_view', 'employee_id' => $this->employee_info['Employee']['id']))){
				die('Saved');
			}else{
				die('Not save');
			}
			
		}else{
			die('Data submit is empty');
		}
	}
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function duration($project_id = null) {
        if ($project_id == 'ok' || $project_id == 'order' || $project_id == 'employee') {
            App::import('Component', 'PhasePlan');
            $PhasePlan = new PhasePlanComponent();

            if ($project_id == 'order') {
                $PhasePlan->parseWeight($this->ProjectPhasePlan);
            } elseif ($project_id == 'ok') {
                $PhasePlan->parseDuration($this->ProjectPhasePlan
                        , $this->ProjectPhasePlan->find('all', array('recursive' => -1)));
            } elseif ($project_id == 'employee') {
                $PhasePlan->duplicateEmployee(ClassRegistry::init('Employee'));
            }
            $this->showDebug();
        }
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        // debug( $project_id); exit;
        // redirect ve index neu ko phai la admin hoac pm
        $this->loadModels('ProjectEmployeeManager','CompanyEmployeeReference', 'HistoryFilter');
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('phase');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectPhasePlan->Behaviors->attach('Containable');
        $this->ProjectPhasePlan->cacheQueries = true;

        $projectPhasePlans = $this->ProjectPhasePlan->find("all", array(
            'fields' => array('id', 'project_part_id', 'predecessor', 'planed_duration', 'project_planed_phase_id', 'project_phase_status_id',
                'phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date', 'weight', 'ref1', 'ref2', 'ref3', 'ref4', 'profile_id', 'progress'),
            'contain' => array(
                'ProjectPhase' => array(
                    'id', 'color'
            )),
            'order' => array('weight' => 'asc'),
            'conditions' => array('project_id' => $project_id)));
        //auto format order
        $count=count($projectPhasePlans);
        for( $i = 0; $i < $count; $i++) {
            $newWeight = $i + 1;
            if($projectPhasePlans[$i]['ProjectPhasePlan']['weight']!="$newWeight") {
                $projectPhasePlans[$i]['ProjectPhasePlan']['weight']=$newWeight;
                $this->ProjectPhasePlan->id = $projectPhasePlans[$i]['ProjectPhasePlan']['id'];
                    $this->ProjectPhasePlan->save(array(
                        'weight' => intval($newWeight)), array('validate' => false, 'callbacks' => false));
            }
        }
        //END
        $listIDPhasePlans = !empty($projectPhasePlans) ? Set::classicExtract($projectPhasePlans, '{n}.ProjectPhasePlan.id') : array();
        $this->loadModel('ProjectTask');
        $countTaskOfPhases = $canNotRemoveDate = array();
        if(!empty($listIDPhasePlans)){
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_planed_phase_id' => $listIDPhasePlans, 'project_id' => $project_id),
                'fields' => array('id', 'project_planed_phase_id', 'COUNT(project_planed_phase_id) AS Total'),
                'group' => array('project_planed_phase_id')
            ));
            $countTaskOfPhases = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.project_planed_phase_id', '{n}.0.Total') : array();
			$canNotRemoveDate = $this->ProjectPhasePlan->_checkCanNotRemovedate($listIDPhasePlans);	
        }
        $projectPhases = $this->ProjectPhasePlan->ProjectPhase->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id'],
                // 'ProjectPhase.activated' => 1,
            ),
            'fields' => array('id', 'name', 'profile_id', 'activated'),
            'order' => array('phase_order' => 'asc')
        ));
        $x = $projectPhases1 = array();
        foreach ($projectPhases as $phase) {
            $p = $phase['ProjectPhase'];
            $projectPhases1[$p['id']] = $p['name'];
            if( $p['activated'] ){
                $x[$p['id']] = $p['name'];
            }
        }
        $projectPhases = $x;
        unset($x);
        // $projectPhases1 = $this->ProjectPhasePlan->ProjectPhase->find('all', array(
        //     'recursive' => -1,
        // 	'conditions' => array(
        // 		'company_id' => $projectName['Project']['company_id'],
        // 	),
        //     'fields' => array('id', 'name', 'profile_id'),
        // 	'order' => array('phase_order' => 'asc')
        // ));
        $phaseDefaults = !empty($projectPhases) ? Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.profile_id') : array();
        // $projectPhases = !empty($projectPhases) ? Set::combine($projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name') : array();
        // $projectPhases1 = !empty($projectPhases1) ? Set::combine($projectPhases1, '{n}.ProjectPhase.id', '{n}.ProjectPhase.name') : array();
        $projectParts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
                )));
        $projectPhaseStatuses = $this->ProjectPhasePlan->ProjectPhaseStatus->find('list', array(
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'phase_status')
                ));
        $this->loadModels('Profile', 'Menu');
        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id', 'name')
        ));
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $currentPhase = $this->ProjectPhasePlan->Project->find('first', array(
            'fields' => array('Project.id'), 'contain' => array(
                'ProjectPhase' => array('name'),
            ), 'conditions' => array('Project.id' => $project_id)));
        $this->set('currentPhase', isset($currentPhase['ProjectPhase']['id']) ? (int) $currentPhase['ProjectPhase']['id'] : 0);
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ?  true : false;
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ?  true : false;
        $displayPart = $this->Menu->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index',
				'display' => 1,
            )
        ));
		$lanTask = $this->Menu->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'widget_id' => 'task',
				'company_id' => $this->employee_info['Company']['id'],
				'model' => 'project'
			),
			'fields' => array('id', 'name_eng', 'name_fre')
		));
		
		$loadFilter = $this->HistoryFilter->loadFilter($this->params['url']['url']);
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $this->set(compact('projectName', 'projectPhases','projectPhases1', 'projectPhaseStatuses', 'projectPhasePlans', 'project_id', 'projectParts', 'countTaskOfPhases', 'profiles', 'activateProfile', 'phaseDefaults', 'manuallyAchievement', 'displayPart', 'lanTask', 'loadFilter', 'canNotRemoveDate'));
    }
    /**
     * Update
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        if (!empty($this->data)) {
            $this->ProjectPhasePlan->create();
			$canRemoveDate = true;
            if (!empty($this->data['id'])) {
                $this->ProjectPhasePlan->id = $this->data['id'];
				$canRemoveDate = !in_array($this->data['id'], $this->ProjectPhasePlan->_checkCanNotRemovedate($this->data['id']));
			}
            $data = array();
			
			if( isset( $this->data['phase_planed_start_date']) && ($this->data['phase_planed_start_date'] == '') && $canRemoveDate ){
				foreach (array('phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date') as $key) {
					$data[$key] = '';
					$this->data[$key] = '';
				}
			}else{
				foreach (array('phase_planed_start_date', 'phase_planed_end_date') as $key) {
				// foreach (array('phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date') as $key) {
					if (!empty($this->data[$key])) {
						$data[$key] = $this->ProjectPhasePlan->convertTime($this->data[$key]);
					}
				}
				//$canRemoveDate = 1 neu phase no task or task no date.
				if($canRemoveDate){
					$data['phase_real_start_date'] = $data['phase_planed_start_date'];
					$data['phase_real_end_date'] = $data['phase_planed_end_date'];
				}else{
					if (!empty($this->data['phase_real_start_date'])) {
						$data['phase_real_start_date'] = $this->ProjectPhasePlan->convertTime($this->data['phase_real_start_date']);
					}
					if (!empty($this->data['phase_real_end_date'])) {
						$data['phase_real_end_date'] = $this->ProjectPhasePlan->convertTime($this->data['phase_real_end_date']);
					}
				}
			}
			$project_id = @$this->data['project_id'];
            if ($this->_checkRole(false, $project_id)) {
                unset($this->data['id']);
                if ($this->ProjectPhasePlan->save(array_merge($this->data, $data))) {
                    $result = true;
                    $projectPhase = $this->ProjectPhasePlan->Project->ProjectPhase->find('first', array(
                        'recursive' => -1, 'fields' => array('color'), 'conditions' => array('id' => $this->data['project_planed_phase_id'])));
                    $this->data['color'] = !empty($projectPhase['ProjectPhase']['color']) ? $projectPhase['ProjectPhase']['color'] : '#004380';
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('Not saved', true), 'error');
                }
                $this->data['id'] = $this->ProjectPhasePlan->id;
            }
        } else {
            $this->Session->setFlash(__('Not saved', true), 'error');
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
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
        }
        $projectParts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
            'conditions' => array(
                'project_id' => $project_id
            )
        ));
        $projectPhases = $this->ProjectPhasePlan->ProjectPhase->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                // 'ProjectPhase.activated' => 1,
            ),
            'fields' => array('id', 'name', 'profile_id', 'activated'),
            'order' => array('phase_order' => 'asc')
        ));
        $x = $projectPhases1 = array();
        foreach ($projectPhases as $phase) {
            $p = $phase['ProjectPhase'];
            $projectPhases1[$p['id']] = $p['name'];
            if( $p['activated'] ){
                $x[$p['id']] = $p['name'];
            }
        }
        $projectPhases = $x;
        unset($x);
        $this->ProjectPhasePlan->Behaviors->attach('Containable');
        $this->ProjectPhasePlan->cacheQueries = true;
        $projectPhasePlans = $this->ProjectPhasePlan->find("all", array(
            'fields' => array('*'),
            'contain' => array(
                'ProjectPhase' => array(
                    'id', 'name', 'color'
                ),
                'ProjectPhaseStatus' => array(
                    'id', 'phase_status'
                ),
                'ProjectPart' => array(
                    'id', 'title'
                )
            ),
            "conditions" => array('ProjectPhasePlan.project_id' => $project_id),
            'order' => array('ProjectPhasePlan.weight' => 'ASC')
        ));
        $projectPhasePlans = Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectPhasePlans[$id])) {
                    unset($data[$id]);
                    unset($projectPhasePlans[$id]);
                    continue;
                }
                if(!empty($projectPhasePlans[$id]['ProjectPhasePlan']['phase_planed_end_date']) && !empty($projectPhasePlans[$id]['ProjectPhasePlan']['phase_real_end_date'])){
                    $projectPhasePlans[$id]['ProjectPhasePlan']['kpi'] = $projectPhasePlans[$id]['ProjectPhasePlan']['phase_planed_end_date'] < $projectPhasePlans[$id]['ProjectPhasePlan']['phase_real_end_date'] ? '#F00' : '#0F0';
                } else {
                    $projectPhasePlans[$id]['ProjectPhasePlan']['kpi'] = '#0F0';
                }
                if (isset($projectPhases1[$projectPhasePlans[$id]['ProjectPhasePlan']['project_planed_phase_id']])) {
                    $predecessors[$id] = $projectPhases1[$projectPhasePlans[$id]['ProjectPhasePlan']['project_planed_phase_id']]
                            . (isset($projectParts[$projectPhasePlans[$id]['ProjectPhasePlan']['project_part_id']]) ? ' (' . $projectParts[$projectPhasePlans[$id]['ProjectPhasePlan']['project_part_id']] . ')' : '');
                }
                $projectPhasePlans[$id]["ProjectPhasePlan"]["color"] = !empty($projectPhasePlans[$id]["ProjectPhase"]["color"]) ? $projectPhasePlans[$id]["ProjectPhase"]["color"] : '#004380';
                $data[$id] = $projectPhasePlans[$id];
            }
        }
        $this->loadModel('Menu');
        $displayParst = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Employee']['company_id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index'
            )
        ));
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ?  true : false;
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ?  true : false;
        $displayParst = !empty($displayParst) && !empty($displayParst['Menu']['display']) ?  $displayParst['Menu']['display'] : 0;
        $this->set(compact('data', 'projectPhasePlans', 'displayParst', 'manuallyAchievement', 'activateProfile', 'predecessors'));
        $this->layout = '';
    }

    /**
     * export_vision
     * Export vision
     *
     * @return void
     * @access public
     */
    function export_vision() {
        $this->layout = false;
        if (!empty($this->data['Export']) && !empty($this->data['Export']['rows'])) {
            extract($this->data['Export']);

            $canvas = explode(",", $canvas);

            // $tmpFile = TMP . 'page_' . time() . '.png';
            // file_put_contents($tmpFile, base64_decode($canvas[1]));

            // list($_width, $_height) = getimagesize($tmpFile);

            $image = imagecreatefromstring(base64_decode($canvas[1]));
            $_width = imagesx($image);
            $_height = imagesy($image);
            $crop = imagecreatetruecolor($_width - 69, $height);
            //set white background
            $white    = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            imagecopy($crop, $image, 0, 0, 69, 266, $_width, $height);
            //imagepng($crop, $tmpFile);
            $this->set(compact('tmpFile', 'height', 'project', 'rows'));
            $this->set('image', $crop);
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function getWeight($project_id = null) {
        $this->layout = 'ajax';
        $weights = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('MAX(weight) as maxWeight')
        ));
        $result = $weights[0][0]['maxWeight'];
        $this->set(compact('result'));
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function order($project_id = null) {
        $this->layout = false;
        if ($this->_checkRole(true, $project_id)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight!=0) {
                    $this->ProjectPhasePlan->id = $id;
                    $this->ProjectPhasePlan->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function sync($project_id = null, $isCheck = null) {
        $this->layout = false;
        $response = array();
        if ($this->_checkRole(true, $project_id)) {
            $endDate = $_endDate = strtotime($this->viewVars['projectName']['Project']['end_date']);
            if($isCheck === 'no'){
                if(!empty($this->data)){
                    foreach($this->data as $data){
                        $this->ProjectPhasePlan->id = $data['id'];
                        $saved = array(
                            'predecessor' => ''
                        );
                        $this->ProjectPhasePlan->save($saved);
                    }
                }
            } else {
                if(!empty($this->data)){
                    foreach($this->data as $data){
                        $this->ProjectPhasePlan->id = $data['id'];
                        $saved = array(
                            'phase_planed_end_date' => $this->ProjectPhasePlan->convertTime($data['phase_planed_end_date']),
                            'phase_planed_start_date' => $this->ProjectPhasePlan->convertTime($data['phase_planed_start_date']),
                            'phase_real_start_date' => $this->ProjectPhasePlan->convertTime($data['phase_real_start_date']),
                            'phase_real_end_date' => $this->ProjectPhasePlan->convertTime($data['phase_real_end_date'])
                        );
                        $this->ProjectPhasePlan->save($saved);
                        $data = strtotime($data['phase_planed_end_date']);
                        if ($data > $endDate) {
                            $endDate = $data;
                        }
                    }
                    if ($endDate != $_endDate) {
                        $this->ProjectPhasePlan->Project->id = $project_id;
                        $response = array('end_date' => date('d-m-Y', $endDate));
                        $this->ProjectPhasePlan->Project->save(array(
                            'end_date' => date('Y-m-d', $endDate)
                        ));
                    }
                }
            }
        }
        echo json_encode($response);
        exit(0);
    }

    /**
     * delete
     *
     * @param int $id, $project_id, $phase_id, $phase
     * @return void
     * @access public
     */
    function delete($id = null, $project_id = null, $phase_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project phase plan', true), 'error');
            $this->redirect(array('action' => 'index/', $project_id));
        }

        $plans = $this->ProjectPhasePlan->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id),
            'fields' => array('project_id')
        ));
        if ($this->_checkRole(true, $project_id) && !empty($plans) && $project_id == $plans['ProjectPhasePlan']['project_id']) {
            $this->loadModel('ProjectTask');
            $tasks = $this->ProjectTask->find('count', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id, 'project_planed_phase_id' => $id)
            ));
            if($tasks != 0){
                $this->Session->setFlash(__('Cannot be removed, tasks attached to the phase', true), 'error');
            } else {
                if ($this->ProjectPhasePlan->delete($id)) {
                    $this->ProjectPhasePlan->updateAll(array('predecessor' => null), array(
                        'predecessor' => $id
                    ));
                    $this->Session->setFlash(__('Deleted', true), 'success');
                } else {
                    $this->Session->setFlash(__('Not deleted', true), 'error');
                }
            }
        }
        $this->redirect(array('action' => 'index/', $project_id));
    }

    function loadt(){
         $this->loadModel('Employee');
         for($i = 1; $i <= 1000; $i++){

            $this->data["Employee"]['email'] = 'loadtest' .$i. '@appdev.vn';
            $this->data["Employee"]['password'] = md5('123456');
            $this->data["Employee"]['is_sas'] = 1;
            $this->Employee->create();
            $this->Employee->save($this->data["Employee"]);
         }
         exit;
    }
    private function _taskCaculates($project_id = null, $company_id = null, $phasePlans = array()){
        $manual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : false;
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModels('ActivityRequest', 'ProjectStatus');

        $projectTasks = $this->ProjectTask->find('all', array(
            //'recursive' => -1,
            'conditions' => array('ProjectTask.project_id' => $project_id),
            'fields' => array('id', 'task_title', 'parent_id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'estimated', 'predecessor', 'special', 'special_consumed', 'manual_consumed', 'overload', 'manual_overload', 'initial_task_end_date', 'initial_task_start_date', 'task_status_id'),
            'order' => array('ProjectTask.weight' => 'ASC')
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
		$task_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'status'),
        ));
		
		
        foreach($projectTasks as $key => $projectTask){
            $dx = $projectTask['ProjectTask'];
            //manual consumed on 2015-08-05
            if( $manual ){
                $projectTasks[$key]['ProjectTask']['consumed'] = $projectTasks[$key]['ProjectTask']['manual_consumed'];
                $projectTasks[$key]['ProjectTask']['overload'] = $overload = $projectTasks[$key]['ProjectTask']['manual_overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
                   if($projectTasks[$key]['ProjectTask']['consumed'] == 0 && !empty($task_status[$projectTasks[$key]['ProjectTask']['task_status_id']]) && $task_status[$projectTasks[$key]['ProjectTask']['task_status_id']] == 'CL'){
						$projectTasks[$key]['ProjectTask']['completed'] = 100;
					}else{
						$projectTasks[$key]['ProjectTask']['completed'] = round($projectTasks[$key]['ProjectTask']['consumed'] * 100 / ($overload + $dx['estimated']), 2);
					}
                }
            }
            else if(!empty($dx['special'])){
                $overload = $projectTasks[$key]['ProjectTask']['overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
                    if($dx['special_consumed'] == 0 && !empty($task_status[$projectTasks[$key]['ProjectTask']['task_status_id']]) && $task_status[$projectTasks[$key]['ProjectTask']['task_status_id']] == 'CL'){
						$projectTasks[$key]['ProjectTask']['completed'] = 100;
					}else{
						$projectTasks[$key]['ProjectTask']['completed'] = round($dx['special_consumed'] * 100 / ($overload + $dx['estimated']), 2);
					}
                }
                $projectTasks[$key]['ProjectTask']['consumed'] = $dx['special_consumed'];
            } else if(!empty($consumeds[$dx['id']])){
                $overload = $projectTasks[$key]['ProjectTask']['overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
                    if($consumeds[$dx['id']] == 0 && !empty($task_status[$projectTasks[$key]['ProjectTask']['task_status_id']]) && $task_status[$projectTasks[$key]['ProjectTask']['task_status_id']] == 'CL'){
						$projectTasks[$key]['ProjectTask']['completed'] = 100;
					}else{
						$projectTasks[$key]['ProjectTask']['completed'] = round($consumeds[$dx['id']] * 100 / ($overload + $dx['estimated']), 2);
					}
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
		// debug($results);
		// exit;
        return $results;
    }

    function format_order(){
        $plans = $this->ProjectPhasePlan->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'project_id', 'weight')
        ));
        $datas = Set::combine($plans, '{n}.ProjectPhasePlan.id','{n}.ProjectPhasePlan.weight','{n}.ProjectPhasePlan.project_id');
        foreach($datas as $key => $data){
            $count = 1;
            foreach($data as $k => $vl){
                $this->ProjectPhasePlan->id = $k;
                $_data['weight'] = $count++;
                $this->ProjectPhasePlan->save($_data);
            }
        }
        pr('Format data order successfully!');
        exit();
    }

    /**
     *
     *  1. Tim nhung phase bi xoa trong project phase plan
     *  2. Liet ke tat ca cac task khong thuoc phase plan.
     *  3. Gan task ko thuoc phase va 1 phase plan nao do
     *
     */
     public function taskHavePhaseDelete(){
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectPhasePlan');
        $tasks = $this->ProjectTask->find('all', array(
                'recursive' => -1
            ));
        $listPhasePlans = Set::combine($tasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_planed_phase_id');
        $phasePlans = $this->ProjectPhasePlan->find('list');
        $gTaskIds = $gPhaseIds = array();
        foreach($listPhasePlans as $pTaskId => $listPhasePlan){
            if(in_array($listPhasePlan, $phasePlans)){

            } else {
                $gTaskIds[] = $pTaskId;
                $gPhaseIds[] = $listPhasePlan;
            }
        }
        if(!empty($gTaskIds)){
            foreach($gTaskIds as $gTaskId){

            }
        }
        if(!empty($gTaskIds)){
            foreach($gTaskIds as $gTaskId){
                echo 'Project Task Id: ' . $gTaskId . '<br />';
            }
        }
        exit;
     }
     /**
      * Kiem tra xem co phase nao co lien ket voi phase hien tai ko?
      */
     public function checkPredecessor(){
        $this->layout = false;
        $check = $this->ProjectPhasePlan->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectPhasePlan.predecessor' => $this->data)
        ));
        $result = 'false';
        if($check != 0){
            $result = 'true';
        }
        echo json_encode($result);
        exit(0);
     }
     /**
      * Save Cac gia tri cua phase co linked
      */
     public function saveLinkedPredecessor(){
        $this->layout = false;
        if(!empty($this->data)){
            foreach($this->data as $data){
                $planStart = strtotime($data['phase_planed_start_date']);
                $planEnd = strtotime($data['phase_planed_end_date']);
                $this->ProjectPhasePlan->id = $data['id'];
                $saved = array(
                    'phase_planed_start_date' => date('Y-m-d', $planStart),
                    'phase_planed_end_date' => date('Y-m-d', $planEnd)
                );
                $this->ProjectPhasePlan->save($saved);
            }
        }
        echo json_encode('true');
        exit(0);
     }
     /**
      * Sao luu date o real sang plan
      * @author QUANNV
      */
     public function time_reset($project_id = null){
        $time_reset = $this->ProjectPhasePlan->find("all", array(
            "fields"=> array('id','phase_real_start_date','phase_real_end_date'),
            "conditions"=> array('project_id' => $project_id),
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
        $this->redirect(array('action'=> 'index', $project_id));
     }
     /**
      * Sao luu date o plan sang real
      * @author QUANNV
      */
     public function time_reset_plan_real($project_id = null){
        $this->loadModel('ProjectTask');
        $time_reset = $this->ProjectPhasePlan->find("all", array(
            "fields"=> array('id','phase_planed_start_date','phase_planed_end_date'),
            "conditions"=> array('project_id' => $project_id),
            "recursive" => -1
        ));
    //     $time_reset_task = $this->ProjectTask->find("all", array(
    //        "fields"=> array('id','task_start_date','task_end_date'),
    //        "conditions"=> array('project_id' => $project_id),
    //        "recursive" => -1
    //    ));
        foreach($time_reset as $phase){
                $this->ProjectPhasePlan->id = $phase['ProjectPhasePlan']['id'];
                $db = $this->ProjectTask->getDataSource();
                $v1 = $db->value($phase['ProjectPhasePlan']['phase_planed_start_date'],'string');
                $v2 = $db->value($phase['ProjectPhasePlan']['phase_planed_end_date'],'string');
                if($phase['ProjectPhasePlan']['phase_planed_start_date'] != '0000-00-00'){
                    $this->ProjectPhasePlan->set('phase_real_start_date',$phase['ProjectPhasePlan']['phase_planed_start_date']);
                    //$this->ProjectTask->set('task_start_date',$phase['ProjectPhasePlan']['phase_planed_start_date']);
                }
                if($phase['ProjectPhasePlan']['phase_planed_end_date'] != '0000-00-00'){
                    $this->ProjectPhasePlan->set('phase_real_end_date',$phase['ProjectPhasePlan']['phase_planed_end_date']);
                    //$this->ProjectTask->set('task_end_date',$phase['ProjectPhasePlan']['phase_planed_end_date']);
                }
                $this->ProjectPhasePlan->save();
                $this->ProjectTask->updateAll(array('task_start_date'=>$v1,'task_end_date'=>$v2), array('ProjectTask.project_planed_phase_id'=>$phase['ProjectPhasePlan']['id']));
                   $log = $this->ProjectTask->getDataSource()->getLog(false, false);
        }
        $this->redirect(array('action'=> 'index', $project_id));
    }
}
?>
