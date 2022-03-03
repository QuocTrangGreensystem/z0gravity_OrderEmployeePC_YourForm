<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectPhaseStatusesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectPhaseStatuses';
    //var $layout = 'administrators';

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
        $companies1 = $this->ProjectPhaseStatus->Company->find('list');
        $parent_companies = $this->ProjectPhaseStatus->Company->find('list', array('fields' => array('id', 'parent_id')));
        $this->set(compact('companies1', 'parent_companies'));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $companyId = $this->employee_info["Company"]["id"];
        else
            $companyId = "";
        $this->set('company_id', $companyId);
        $this->ProjectPhaseStatus->recursive = 0;
        if ($companyId != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('phase_status', 'company_id','display'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $companyId, 'parent_id' => $companyId))
            );
            $this->set('companies', $this->ProjectPhaseStatus->Company->getTreeList($companyId));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('phase_status', 'company_id','display'),
                'limit' => 1005,
            );
            $this->set('companies', $this->ProjectPhaseStatus->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectPhaseStatuses', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project phase status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectPhaseStatus', $this->ProjectPhaseStatus->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project phase status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectPhaseStatus', 'phase_status')){
                if ($this->ProjectPhaseStatus->save($this->data)) {
					$this->data = $this->ProjectPhaseStatus->read(null, $id);
					$this->data = $this->data['ProjectPhaseStatus'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true),  'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
             }else{
                $this->Session->setFlash(__('Already exist', true), 'error');
             }
             // $this->redirect(array('action' => 'index'));
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectPhaseStatus->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project phase status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectPhaseStatus'));
        $allowDeleteProjectPhaseStatus = $this->_projectPhaseStatusIsUsing($id);
        if($check && ($allowDeleteProjectPhaseStatus == 'true')){
            if ($this->ProjectPhaseStatus->delete($id)) {
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
     *  Kiem tra project phase status da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectPhaseStatusIsUsing($id = null){
        $this->loadModel('ProjectPhasePlan');
        $checkProjectPhasePlan = $this->ProjectPhasePlan->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectPhasePlan.project_phase_status_id' => $id)
            ));
        $allowDeleteProjectPhaseStatus= 'true';
        if($checkProjectPhasePlan != 0){
            $allowDeleteProjectPhaseStatus = 'false';
        }
        
        return $allowDeleteProjectPhaseStatus;
    }
        /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->ProjectPhaseStatus->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "phase_status" => strtolower($name)
                )));
        return !$check;
    }

}
?>