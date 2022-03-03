<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrCostControlsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrCostControls';

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
        $this->ProjectAmrCostControl->recursive = 0;
        $companies = $this->ProjectAmrCostControl->Company->find('list');
        $parent_companies = $this->ProjectAmrCostControl->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_cost_control', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrCostControl->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_cost_control', 'company_id'),
                'limit' => 1000,
            );
            $this->set('company_names', $this->ProjectAmrCostControl->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrCostControls', $this->paginate());
    }

    /**
     * view
     * @param int $id
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr cost control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrCostControl', $this->ProjectAmrCostControl->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
			if($this->employee_info["Employee"]["is_sas"] == 1 || ($this->employee_info["Employee"]["is_sas"] != 1 && ($this->employee_info["Employee"]['company_id'] == $this->data['ProjectAmrCostControl']['company_id']))){
				$this->ProjectAmrCostControl->create();
				if ($this->ProjectAmrCostControl->save($this->data)) {
					$this->Session->setFlash(__('Saved', true), 'success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('NOT SAVED.', true), 'error');
				}
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
            $rs = $this->ProjectAmrCostControl->find('count', array('conditions' => array(
                    'ProjectAmrCostControl.amr_cost_control' => $data['amr_cost_control'],
                    'ProjectAmrCostControl.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrCostControl->find('count', array('conditions' => array(
                    'ProjectAmrCostControl.amr_cost_control' => $data['amr_cost_control'],
                    'ProjectAmrCostControl.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrCostControl.id' => $data['id'])
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
		$conditions = array();
		if($this->employee_info["Employee"]["is_sas"] != 1) $conditions['company_id'] = $this->employee_info["Company"]["id"];
        $rs = $this->ProjectAmrCostControl->find('first', array('conditions' => array('ProjectAmrCostControl.id' => $data['id'], $conditions)));
        if (!empty($rs) && $rs['ProjectAmrCostControl']['amr_cost_control'] == $data['amr_cost_control'] && ($rs['ProjectAmrCostControl']['company_id'] == $data['company_id']))
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
            $this->Session->setFlash(__('Invalid project amr cost control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrCostControl->save($this->data)) {
						$this->data = $this->ProjectAmrCostControl->read(null, $id);
						$this->data = $this->data['ProjectAmrCostControl'];
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
            $this->data = $this->ProjectAmrCostControl->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project amr cost control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $allowDeleteProjectAmrCostControl = $this->_projectAmrCostControlIsUsing($id);
		$conditions = array();
		$conditions['id'] = $id;
		if ($this->employee_info["Employee"]["is_sas"] != 1)
            $conditions['company_id'] = $this->employee_info["Company"]["id"];
		$projectAmrCostControl = $this->ProjectAmrCostControl->find('first',  array(
            'recursive' => -1, 
            'conditions' => $conditions,
			'fields' => array('id'),
		));
        if($allowDeleteProjectAmrCostControl == 'true' && !empty($projectAmrCostControl)){
            if ($this->ProjectAmrCostControl->delete($id)) {
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
     *  Kiem tra Project Amr Cost Control da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrCostControlIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_cost_control_id' => $id)
            ));
        $allowDeleteProjectAmrCostControl= 'true';
        if($checkProjectAmr != 0){
            $allowDeleteProjectAmrCostControl = 'false';
        }
        
        return $allowDeleteProjectAmrCostControl;
    }

}
?>