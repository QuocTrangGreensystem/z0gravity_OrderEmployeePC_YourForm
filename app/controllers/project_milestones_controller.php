<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectMilestonesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectMilestones';

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
        
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('milestone');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectMilestone->Behaviors->attach('Containable');
        $this->ProjectMilestone->cacheQueries = true;

        $projectMilestones = $this->ProjectMilestone->find("all", array(
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated', 'part_id', 'weight'),
            'recursive' => -1,
            'order' => array('weight' => 'ASC'),
            "conditions" => array('project_id' => $project_id)));
        $this->loadModels('ProjectPart', 'ProjectAlert', 'Menu', 'HistoryFilter');
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
        $listAlert = !empty($alert) ? Set::combine($alert, '{n}.ProjectAlert.id', '{n}.ProjectAlert.alert_name') : array();
        $numberAlert = !empty($alert) ? Set::combine($alert, '{n}.ProjectAlert.id', '{n}.ProjectAlert.number_of_day') : array();
        $displayParst = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index'
            )
        ));
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $displayParst = !empty($displayParst) && !empty($displayParst['Menu']['display']) ?  $displayParst['Menu']['display'] : 0;
        $this->set(compact('projectName', 'projectMilestones', 'project_id', 'parts', 'listAlert', 'numberAlert', 'displayParst', 'loadFilter'));
    }

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
            $this->ProjectMilestone->create();
            if (!empty($this->data['id'])) {
                $this->ProjectMilestone->id = $this->data['id'];
            }
            $data = array();
            foreach (array('milestone_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectMilestone->convertTime($this->data[$key]);
                }
            }
			if( !empty($this->data['validated']) && $this->data['validated'] == 'yes'){
				if(!empty($this->data['effective_date'])){
					$data['effective_date'] = strtotime( $this->data['effective_date'] );
				}else{
					$data['effective_date'] = time();
					$this->data['effective_date'] = date('d-m-Y');
				}
			}
			$data['updated'] = time();
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                $data = array_merge($this->data, $data);
                if (isset($data['validated'])) {
                    $data['validated'] = $data['validated'] == 'no' ? false : true;
                }
                if ($this->ProjectMilestone->save($data)) {
                    $result = true;
                    // $this->Session->setFlash(__('The Milestone has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Milestone could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectMilestone->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

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
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated', 'part_id'),
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
    function delete($id = null, $project_id) {
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
				$this->Session->setFlash(__('Deleted', true), 'success');
				$this->redirect(array('action' => 'index', $project_id));
			}
            $this->Session->setFlash(__('Project milestone was not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'index', $project_id));
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
        $result = false;
        if( !$project_id || !$milestone_item){
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            $this->set(compact('result'));
            return;
        }
        $this_milestone = $this->ProjectMilestone->find("first", 
            array(
                "conditions" => array(
                    "ProjectMilestone.project_id" => $project_id,
                    "ProjectMilestone.id" => $milestone_item,
                ),
                'fields' => array(
                    'id', 
                    'project_id', 
                    'milestone_date', 
                    'validated', 
                ),
            )
        );
        $data = $this_milestone;
        $data['ProjectMilestone']['validated'] = intval(!$data['ProjectMilestone']['validated']);
        $this->ProjectMilestone->id = $milestone_item;
        if ($this->ProjectMilestone->saveField('validated', $data['ProjectMilestone']['validated'])){
            $result = 1;
            $this->Session->setFlash(__('The Milestone has been saved.', true), 'success');
        } else {
            $result = 0;
            $this->Session->setFlash(__('The Milestone could not be saved. Please, try again.', true), 'error');
        }
        $this->set(compact('result'));
        die;
    }
	
	function get_list_milestone($project_id = null){
		$milestones = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'project_id' => $project_id
            )
        ));
		$milestones = !empty( $milestones) ? $milestones : array();
		die(json_encode($milestones));
	}

}
?>
