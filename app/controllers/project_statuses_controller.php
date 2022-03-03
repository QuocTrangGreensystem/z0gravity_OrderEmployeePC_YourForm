<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectStatusesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectStatuses';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    var $paginate = array('limit' => 1000);

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $this->ProjectStatus->recursive = 0;
        $companies = $this->ProjectStatus->Company->find('list');
        $parent_companies = $this->ProjectStatus->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                'fields' => array('name', 'company_id','display', 'status'),
                'limit' => 1005,
				'order' => array('weight' => 'ASC'),
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectStatus->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('name', 'company_id','display'),
                'limit' => 1005,
				'order' => array('weight' => 'ASC'),
            );
            $this->set('company_names', $this->ProjectStatus->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectStatuses', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project status', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectStatus', $this->ProjectStatus->read(null, $id));
    }
    /**
     * update
     *
     * @return void
     * @access public
     */
    function update($id = null) {
		$result = false;
		$this->layout = false;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
			$maxWeight = $this->ProjectStatus->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					
				),
				'fields' => array('MAX(weight) as weight'),
			));
			$maxWeight = $maxWeight['0']['weight'] + 1;
			if(empty($this->data['company_id'])){
				$company_id = $this->employee_info["Company"]["id"];
				$this->data['company_id'] = $company_id;
			}
            if($this->_checkDuplicate($this->data, 'ProjectStatus', 'name')){
				$this->data['weight'] = $maxWeight;
                if ($this->ProjectStatus->save($this->data)) {
					$this->data = $this->ProjectStatus->read(null, $id);
					$this->data = $this->data['ProjectStatus'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectStatus->read(null, $id);
        }
		$this->set(compact('result'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectStatus'));
        $allowDeleteProjectStatus = $this->_projectStatusIsUsing($id);
        if($check && ($allowDeleteProjectStatus == 'true')){
            if ($this->ProjectStatus->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash('<a id="open-reference-popup" data-id = "'. $id .'" href="#">' . __('Cannot delete Project Status. View all project reference', true) .'</a>', 'error');
			 $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
    /**
     *  Kiem tra project status da co su dung
     *  @return boolean
     *  @access private
     */

    private function _projectStatusIsUsing($id = null){
        $this->loadModels('Project', 'ProjectTask');
        $checkProjectStatus = $this->Project->find('count', array(
            'recursive' => -1,
            'conditions' => array('Project.project_status_id' => $id)
        ));
        // $allowDeleteProjectStatus = 'true';
        if($checkProjectStatus != 0){
            return 'false';
        }
		$checkProjectTasksStatus = $this->ProjectTask->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectTask.task_status_id' => $id)
        ));
		if($checkProjectTasksStatus != 0){
            return 'false';
        }
        return 'true';
    }
	public function projectStatusRefers($id = null){
		$this->loadModels('Project', 'ProjectTask');
		$data = array();
		$projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('Project.project_status_id' => $id),
			'fields' => array('id','project_name')
        ));
		$data['projects'] = $projects;
		$projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectTask.task_status_id' => $id),
			'fields' => array('id','task_title','project_id')
        ));
		$projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask', '{n}.ProjectTask.project_id') : array();
		$data['projectTasks'] = $projectTasks;
		die(json_encode($data));
	}
      /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->ProjectStatus->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "name" => strtolower($name)
                )));
        return !$check;
    }
	 public function saveOrder(){
		$this->layout = 'ajax';
        $this->autoRender = false;
        foreach ($this->data as $id => $value_order) {
            if (!empty($id) && !empty($value_order) && $value_order!=0) {
                $this->ProjectStatus->id = $id;
                $this->ProjectStatus->save(array(
                    'weight' => intval($value_order)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }
}
?>
