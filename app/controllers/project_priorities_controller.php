<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectPrioritiesController extends AppController {

    var $priority = 'ProjectPriorities';
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

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
        $this->ProjectPriority->recursive = 0;
        $companies = $this->ProjectPriority->Company->find('list');
        $parent_companies = $this->ProjectPriority->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {

            $this->paginate = array(
                // phan trang
                'fields' => array('priority', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_prioritys', $this->ProjectPriority->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('priority', 'company_id'),
                'limit' => 1000
            );
            $this->set('company_prioritys', $this->ProjectPriority->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectPriorities', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project priority', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectPriority', $this->ProjectPriority->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project priority', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectPriority', 'priority')){
                if ($this->ProjectPriority->save($this->data)) {
					$this->data = $this->ProjectPriority->read(null, $id);
					$this->data = $this->data['ProjectPriority'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true),'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true),'error');
                }
            } else {
                $this->Session->setFlash(__('Already exist', true),'error');
            }
            // $this->redirect(array('action' => 'index'));
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectPriority->read(null, $id);
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project priority', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectPriority'));
        $allowDeleteProjectPriority = $this->_projectPriorityIsUsing($id);
        if($check && ($allowDeleteProjectPriority == 'true')){
            if ($this->ProjectPriority->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
    /**
     *  Kiem tra project priority da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectPriorityIsUsing($id = null){
        $this->loadModel('Project');
        $checkProjectPriority = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.project_priority_id' => $id)
            ));
        $allowDeleteProjectPriority = 'true';
        if($checkProjectPriority != 0){
            $allowDeleteProjectPriority = 'false';
        }
        
        return $allowDeleteProjectPriority;
    }
  /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->ProjectPriority->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "priority" => strtolower($name)
                )));
        return !$check;
    }
}
?>