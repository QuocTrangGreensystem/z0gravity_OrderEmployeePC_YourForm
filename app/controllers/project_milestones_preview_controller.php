<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectMilestonesPreviewController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectMilestonesPreview';
    var $uses = 'ProjectMilestone';
	var $components = array('SlickExporter');

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel');

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
		$this->loadModel('HistoryFilter');
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('milestone');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectMilestone->Behaviors->attach('Containable');
        $this->ProjectMilestone->cacheQueries = true;

        $projectMilestones = $this->ProjectMilestone->find("all", array(
            'fields' => array('id', 'project_milestone', 'initial_date', 'milestone_date', 'effective_date', 'validated', 'part_id', 'weight'),
            'recursive' => -1,
            'order' => array('weight' => 'ASC'),
            "conditions" => array(
				'project_id' => $project_id,
			)
		));
		
        $projectMilestonesSlide = $this->ProjectMilestone->find("all", array(
            'fields' => array('id', 'project_milestone', 'initial_date', 'milestone_date', 'effective_date', 'validated', 'part_id', 'weight'),
            'recursive' => -1,
            'order' => array('weight' => 'ASC'),
            "conditions" => array(
				'project_id' => $project_id,
				'AND' => array(
					array('milestone_date !=' => '0000-00-00'),
					array('milestone_date !=' => 'NULL'),
				),
			)
		));
		
        $listprojectMilestones = !empty($projectMilestonesSlide) ? Set::combine($projectMilestonesSlide, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        $listprojectMilestones = Set::sort($listprojectMilestones, '{n}.milestone_date', 'asc');
        $this->loadModels('ProjectPart', 'ProjectAlert', 'Menu');
        $parts = $this->ProjectPart->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectPart.project_id' => $project_id
            ),
            'order' => array('ProjectPart.weight' => 'ASC'),
            'fields' => array('ProjectPart.id', 'title')
        ));
        $alert = $this->ProjectAlert->find('all', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'display' => 1
            ),
            'fields' => array('id', 'alert_name', 'number_of_day'),
            'order' => array('alert_name ASC')
        ));
		$nameColumn = $this->Menu->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'model' => 'project',
				'widget_id' => 'milestone'
			),
			'fields' => array('name_eng', 'name_fre')
		));
		$loadFilter = $this->HistoryFilter->loadFilter($this->params['url']['url']);
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
		
        $listAlert = !empty($alert) ? Set::combine($alert, '{n}.ProjectAlert.id', '{n}.ProjectAlert.alert_name') : array();
        $numberAlert = !empty($alert) ? Set::combine($alert, '{n}.ProjectAlert.id', '{n}.ProjectAlert.number_of_day') : array();
        $this->set(compact('projectName', 'projectMilestones', 'project_id', 'parts', 'listAlert', 'numberAlert','listprojectMilestones', 'nameColumn', 'loadFilter'));
    }
	/* End index */

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
			if( !empty( $this->data['Milestone'])) $this->data = $this->data['Milestone'];
            $data = array();
			if(empty($this->data['initial_date']) && !empty($this->data['milestone_date'])){
				$this->data['initial_date'] = $this->data['milestone_date'];
			}
			if(empty($this->data['milestone_date']) && !empty($this->data['initial_date'])){
				$this->data['milestone_date'] = $this->data['initial_date'];
			}
            foreach (array('milestone_date', 'initial_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectMilestone->convertTime($this->data[$key]);
                }
            }
			if(!empty($this->data['effective_date'])){
				$data['effective_date'] = strtotime( $this->data['effective_date'] );
			}elseif( !empty($this->data['validated']) && $this->data['validated'] == 'yes'){
				$data['effective_date'] = time();
				$this->data['effective_date'] = date('d-m-Y');
			}
			$listWeight = $this->ProjectMilestone->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $this->data['project_id']
				),
				'fields' => array('id', 'weight')
			));
			if(!empty($listWeight)){
				$maxWeight = max($listWeight);
				$data['weight'] = $maxWeight + 1;
			}
			$data['updated'] = time();
			$this->ProjectMilestone->create();
			if (!empty($this->data['id'])) {
				$this->ProjectMilestone->id = $this->data['id'];
			}
			unset($this->data['id']);
			$data['initial_date'] = strtotime($this->data['initial_date']);
			$data = array_merge($this->data, $data);
			
			if (isset($data['validated'])) {
				$data['validated'] = $data['validated'] == 'no' ? false : true;
			}
			$this->loadModel('Project');
			$cateProject = $this->Project->find('list',array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $this->data['project_id']
				),
				'fields' => array('category')
			));
			if (!empty($this->data['milestone_date']) && !empty($this->data['initial_date'])){
				if ($this->ProjectMilestone->save($data)) {
					$result = true;
					// $this->Session->setFlash(__('The Milestone has been saved.', true), 'success');
				} else {
					$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
				}
			}elseif(($cateProject) && ($cateProject[$this->data['project_id']] != 3)){
				if ($this->ProjectMilestone->save($data)) {
					$result = true;
				}
			}else{
				$this->Session->setFlash(__('The Milestone could not be saved. Please, try again.', true), 'error');
			}
			$this->data['id'] = $this->ProjectMilestone->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
	/* End update */

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectMilestone->id = $id;
                $this->ProjectMilestone->save(array(
                    'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($project_id = null) {
        $this->_checkRole(false, $project_id);
        $conditions = array('project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectMilestone.id'] = $data;
            }
        }

        $this->ProjectMilestone->Behaviors->attach('Containable');

        $this->ProjectMilestone->cacheQueries = true;

        $projectMilestones = $this->ProjectMilestone->find("all", array(
            'fields' => array('id', 'project_milestone', 'effective_date', 'milestone_date', 'validated', 'part_id'),
            'recursive' => -1,
            "conditions" => $conditions));

        $projectMilestones = Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}');
        $partId = Set::combine($projectMilestones, '{n}.ProjectMilestone.part_id', '{n}.ProjectMilestone.part_id');
        // lay Part name.
        $this->loadModels('ProjectPart', 'ProjectAlert', 'Menu');
        $partName = $this->ProjectPart->find('list', array(
            'fields' => array('id', 'title'),
            'recursive' => -1,
            'conditions' => array(
                'ProjectPart.id' => $partId
            )
        ));
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectMilestones[$id])) {
                    unset($data[$id]);
                    unset($projectMilestones[$id]);
                    continue;
                }
                $data[$id] = $projectMilestones[$id];
            }
            $projectMilestones = $data;
            unset($data);
        }
        $alert = $this->ProjectAlert->find('all', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Employee']['company_id'],
                'display' => 1
            ),
            'fields' => array('id', 'alert_name', 'number_of_day'),
            'order' => array('alert_name ASC')
        ));
        $listAlert = !empty($alert) ? Set::combine($alert, '{n}.ProjectAlert.id', '{n}.ProjectAlert.alert_name') : array();
        $numberAlert = !empty($alert) ? Set::combine($alert, '{n}.ProjectAlert.id', '{n}.ProjectAlert.number_of_day') : array();
        $displayParst = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Employee']['company_id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index'
            )
        ));
        $displayParst = !empty($displayParst) && !empty($displayParst['Menu']['display']) ?  $displayParst['Menu']['display'] : 0;
        $this->set(compact('projectMilestones', 'partName', 'listAlert', 'numberAlert', 'displayParst'));
        $this->layout = '';
    }

	
	/**
     * Export
     *
     * @return void
     * @access public
     */
	public function export_excel() {
        if (!empty($this->data)) {
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                    ->setT('Project Milestones List')  //Sheet title, auto translate
                    ->save($data, 'project_milestones_{date}.xls');
        }
        die;
    }
	
    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project milestone', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectMilestone', $this->ProjectMilestone->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectMilestone->create();
            if ($this->ProjectMilestone->save($this->data)) {
                $this->Session->setFlash(__('The project milestone has been saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The project milestone could not be saved. Please, try again.', true), 'error');
            }
        }
        $projects = $this->ProjectMilestone->Project->find('list');
        $this->set(compact('projects'));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project milestone', true), 'error');
            $this->redirect(array('action' => 'index', $this->data["ProjectMilestone"]["project_id"]));
        }
        if (!empty($this->data)) {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $this->data["ProjectMilestone"]["milestone_date"] = $str_utility->convertToSQLDate($this->data["ProjectMilestone"]["milestone_date"]);
            if ($this->ProjectMilestone->save($this->data)) {
                $this->Session->setFlash(sprintf(__('The project milestone %s has been saved', true), '<b>' . $this->data["ProjectMilestone"]["project_milestone"] . '</b>'), 'success');
                $this->redirect(array('action' => 'index', $this->data["ProjectMilestone"]["project_id"]));
            } else {
                $this->Session->setFlash(__('The project milestone could not be saved. Please, try again.', true), 'error');
                $this->redirect(array('action' => 'index', $this->data["ProjectMilestone"]["project_id"]));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectMilestone->read(null, $id);
        }
        $projects = $this->ProjectMilestone->Project->find('list');
        $this->set(compact('projects'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project milestone', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
		$checkMilestone = $this->ProjectMilestone->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $id,
			),
			'fields' => array('id', 'project_id')
		));
		$project_id = !empty($checkMilestone) ? $checkMilestone['ProjectMilestone']['project_id'] : 0;
        if ($this->_checkRole(false, $project_id)) {
			if ($this->ProjectMilestone->delete($id)) {
				$this->_functionStop(true, $id, __('Deleted', true), false, array('action' => 'index', $project_id));
			}
        }
		$this->_functionStop(false, $id, __('Project milestone was not deleted', true), false, array('action' => 'index', $project_id));
    }

    /**
     * check_milestone_of_project
     *
     * @return void
     * @access public
     */
    function check_milestone_of_project() {
        $project_id = $_POST['project_id'];
        $project_milestone_name = $_POST['project_milestone_name'];
        $this->layout = "ajax";
        $check = $this->ProjectMilestone->find("count", array("conditions" => array(
                "ProjectMilestone.project_id" => $project_id,
                "ProjectMilestone.project_milestone" => $project_milestone_name,
                )));
        echo $check;
        exit;
    }

    /**
     * check_milestone_of_project_edit
     *
     * @return void
     * @access public
     */
    function check_milestone_of_project_edit() {
        $project_id = $_POST['project_id'];
        $project_milestone_name = $_POST['project_milestone_name'];
        $milestone_to_edit = $_POST['milestone_to_edit'];
        $this->layout = "ajax";
        $check = $this->ProjectMilestone->find("count", array("conditions" => array(
                "ProjectMilestone.project_id" => $project_id,
                "ProjectMilestone.project_milestone" => $project_milestone_name,
                "ProjectMilestone.id <>" => $milestone_to_edit,
                )));
        echo $check;
        exit;
    }

    /**
     * exportExcel
     * Export to Excel
     *
     * @param int $project_id
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        //$this->layout = 'excel';
        $this->set('columns', $this->name_columna);
        $this->paginate = array("conditions" => array('Project.id' => $project_id));
        $this->set("project_id", $project_id);
        $this->set('projectMilestones', $this->paginate());
        $this->set('projectName', $this->ProjectMilestone->Project->find("first", array("fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));
    }
    function change_milestone_status($project_id=null, $milestone_item=null){
        $this->loadModel('UserLastUpdated');
        $result = false;
		if( $this->_checkRole(true, $project_id)){
			if( !$project_id || !$milestone_item){
				$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
				$this->set(compact('result'));
				return;
			}
			$this_milestone = $this->ProjectMilestone->find("first", 
				array(
					'recusive' => -1,
					"conditions" => array(
						"ProjectMilestone.project_id" => $project_id,
						"ProjectMilestone.id" => $milestone_item,
					),
					'fields' => array('*'),
				)
			);
			if (!empty( $this_milestone )){
				$this_milestone['ProjectMilestone']['validated'] = intval(!$this_milestone['ProjectMilestone']['validated']);
				$this->data = $this_milestone;
				if( $this_milestone['ProjectMilestone']['validated'] ) {
					$this_milestone['ProjectMilestone']['effective_date'] = time();
				}
				$this->data['ProjectMilestone']['effective_date'] = !empty($this_milestone['ProjectMilestone']['effective_date']) ? date('d-m-Y', $this_milestone['ProjectMilestone']['effective_date']) : '';
				unset($this_milestone['ProjectMilestone']['id']);
				$this->ProjectMilestone->id = $milestone_item;
				if ($this->ProjectMilestone->save($this_milestone)){
					$result = 1;
					$this->Session->setFlash(__('The Milestone has been saved.', true), 'success');
				} else {
					$result = 0;
					$this->Session->setFlash(__('The Milestone could not be saved. Please, try again.', true), 'error');
				}

				// User last updated project milestone
				if($result == 1){
					$user_last_updated = $this->UserLastUpdated->find('first', array(
						'recusive' => -1,
						'fields' => array('id', 'employee_id'),
						'conditions' => array(
							'employee_id' => $this->employee_info['Employee']['id'],
							'path' => 'project_milestones_preview/index/'. $project_id,
						),
					));
					if(!empty($user_last_updated)){
						$this->UserLastUpdated->id = $user_last_updated['UserLastUpdated']['id'];
						$this->UserLastUpdated->save(array(
							'employee_id' => $this->employee_info['Employee']['id'],
							'updated' => time()
						));
					}else{
						$this->UserLastUpdated->create();
						$this->UserLastUpdated->save(array(
							'id' => $this->UserLastUpdated->id,
							'company_id' => $this->employee_info['Company']['id'],
							'employee_id' => $this->employee_info['Employee']['id'],
							'path' => 'project_milestones_preview/index/'. $project_id,
							'action' => '',
							'created' => time(),
							'updated' => time()
						)); 
					}
				}
			}else{
				$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
			}
		}else{
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
		}
        $this->set(compact('result'));
        //die;
    }
	public function get_milestone_slider($project_id = null){
		$projectMilestones = $this->ProjectMilestone->find("all", array(
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated', 'part_id', 'weight'),
            'recursive' => -1,
            'order' => array('weight' => 'ASC'),
            "conditions" => array(
				'project_id' => $project_id,
				'AND' => array(
					array('milestone_date !=' => '0000-00-00'),
					array('milestone_date !=' => 'null'),
				),
			)));

        $listprojectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        $listprojectMilestones = Set::sort($listprojectMilestones, '{n}.milestone_date', 'asc');
		$this->set(compact('listprojectMilestones'));
	}
}
?>
