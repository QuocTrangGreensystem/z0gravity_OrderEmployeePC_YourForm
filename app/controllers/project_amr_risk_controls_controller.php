<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrRiskControlsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrRiskControls';

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
        $this->ProjectAmrRiskControl->recursive = 0;
        $companies = $this->ProjectAmrRiskControl->Company->find('list');
        $parent_companies = $this->ProjectAmrRiskControl->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_risk_control', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrRiskControl->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_risk_control', 'company_id'),
                'limit' => 1000,
            );
            $this->set('company_names', $this->ProjectAmrRiskControl->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrRiskControls', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr risk control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrRiskControl', $this->ProjectAmrRiskControl->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrRiskControl->create();
            if ($this->ProjectAmrRiskControl->save($this->data)) {
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
            }
        }
    }

    /**
     * check_dupplicate
     *
     * @return void
     * @access public
     */
    function check_dupplicate($data) {
        if ($data['id'] == "") {
            // add new
            $rs = $this->ProjectAmrRiskControl->find('count', array('conditions' => array(
                    'ProjectAmrRiskControl.amr_risk_control' => $data['amr_risk_control'],
                    'ProjectAmrRiskControl.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrRiskControl->find('count', array('conditions' => array(
                    'ProjectAmrRiskControl.amr_risk_control' => $data['amr_risk_control'],
                    'ProjectAmrRiskControl.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrRiskControl.id' => $data['id'])
                    )));
        }
        if ($rs == 0)
            return true; else
            return false;
    }

    /**
     * check_exists_data
     *
     * @return void
     * @access public
     */
    function check_exists_data($data) {
        $rs = $this->ProjectAmrRiskControl->find('first', array('conditions' => array('ProjectAmrRiskControl.id' => $data['id'])));
        if (($rs['ProjectAmrRiskControl']['amr_risk_control'] == $data['amr_risk_control']) && ($rs['ProjectAmrRiskControl']['company_id'] == $data['company_id']))
            return true;
        else
            return false;
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
            $this->Session->setFlash(__('Invalid project amr risk control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrRiskControl->save($this->data)) {
						$this->data = $this->ProjectAmrRiskControl->read(null, $id);
						$this->data = $this->data['ProjectAmrRiskControl'];
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
            $this->data = $this->ProjectAmrRiskControl->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project amr risk control', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectAmrRiskControl'));$allowDeleteProjectAmrRiskControl = $this->_projectAmrRiskControlIsUsing($id);
		if($check && ($allowDeleteProjectAmrRiskControl == 'true')){
            if ($this->ProjectAmrRiskControl->delete($id)) {
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
     *  Kiem tra PROJECT AMR risk control da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrRiskControlIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_risk_control_id' => $id)
            ));
        $allowDeleteProjectAmrRiskControl= 'true';
        if($checkProjectAmr != 0){
            $allowDeleteProjectAmrRiskControl = 'false';
        }
        
        return $allowDeleteProjectAmrRiskControl;
    }

}
?>