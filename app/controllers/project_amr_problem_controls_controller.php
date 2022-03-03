<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrProblemControlsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrProblemControls';

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
        $this->ProjectAmrProblemControl->recursive = 0;
        $companies = $this->ProjectAmrProblemControl->Company->find('list');
        $parent_companies = $this->ProjectAmrProblemControl->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                'fields' => array('amr_problem_control', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrProblemControl->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('amr_problem_control', 'company_id'),
                'limit' => 1000,
            );
            $this->set('company_names', $this->ProjectAmrProblemControl->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrProblemControls', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr problem control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrProblemControl', $this->ProjectAmrProblemControl->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrProblemControl->create();
            if ($this->ProjectAmrProblemControl->save($this->data)) {
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
            }
        }
    }

    /**
     * check_dupplicate
     * @param array $data
     * @return void
     * @access public
     */
    function check_dupplicate($data) {
        if ($data['id'] == "") {
            // add new
            $rs = $this->ProjectAmrProblemControl->find('count', array('conditions' => array(
                    'ProjectAmrProblemControl.amr_problem_control' => $data['amr_problem_control'],
                    'ProjectAmrProblemControl.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrProblemControl->find('count', array('conditions' => array(
                    'ProjectAmrProblemControl.amr_problem_control' => $data['amr_problem_control'],
                    'ProjectAmrProblemControl.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrProblemControl.id' => $data['id'])
                    )));
        }
        if ($rs == 0)
            return true; else
            return false;
    }

    /**
     * check_exists_data
     * @param array $data
     * @return void
     * @access public
     */
    function check_exists_data($data) {
        //debug($data);exit;
        $rs = $this->ProjectAmrProblemControl->find('first', array('conditions' => array('ProjectAmrProblemControl.id' => $data['id'])));
        if ($rs['ProjectAmrProblemControl']['amr_problem_control'] == $data['amr_problem_control']
                && ($rs['ProjectAmrProblemControl']['company_id'] == $data['company_id'])
        )
            return true;
        else
            return false;
    }

    /**
     * update
     * @param int $id
     * @return void
     * @access public
     */
    function update($id = null) {
		$result = false;
		$this->layout = false;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project amr problem control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrProblemControl->save($this->data)) {
						$this->data = $this->ProjectAmrProblemControl->read(null, $id);
						$this->data = $this->data['ProjectAmrProblemControl'];
						$result = true;
                        $this->Session->setFlash(__('Saved', true), 'success');
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    }
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAmrProblemControl->read(null, $id);
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     * @param int $id
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project amr problem control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectAmrProblemControl'));
        $allowDeleteProjectAmrProblemControl = $this->_projectAmrProblemControlIsUsing($id);
        if($check && ($allowDeleteProjectAmrProblemControl == 'true')){
            if ($this->ProjectAmrProblemControl->delete($id)) {
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
     *  Kiem tra PROJECT AMR PROBLEM CONTROL da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrProblemControlIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_problem_control_id' => $id)
            ));
        $allowDeleteProjectAmrProblemControl= 'true';
        if($checkProjectAmr != 0){
            $allowDeleteProjectAmrProblemControl = 'false';
        }
        
        return $allowDeleteProjectAmrProblemControl;
    }


}
?>