<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrPlansController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrPlans';

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
        $this->ProjectAmrPlan->recursive = 0;
        $companies = $this->ProjectAmrPlan->Company->find('list');
        $parent_companies = $this->ProjectAmrPlan->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_plan', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrPlan->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_plan', 'company_id'),
            );
            $this->set('company_names', $this->ProjectAmrPlan->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrPlans', $this->paginate());
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr plan', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrPlan', $this->ProjectAmrPlan->read(null, $id));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrPlan->create();
            if ($this->ProjectAmrPlan->save($this->data)) {
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
                $this->redirect(array('action' => 'index'));
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
            $rs = $this->ProjectAmrPlan->find('count', array('conditions' => array(
                    'ProjectAmrPlan.amr_plan' => $data['amr_plan'],
                    'ProjectAmrPlan.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrPlan->find('count', array('conditions' => array(
                    'ProjectAmrPlan.amr_plan' => $data['amr_plan'],
                    'ProjectAmrPlan.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrPlan.id' => $data['id'])
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
        $rs = $this->ProjectAmrPlan->find('first', array('conditions' => array('ProjectAmrPlan.id' => $data['id'])));
        if ($rs['ProjectAmrPlan']['amr_plan'] == $data['amr_plan'] && ($rs['ProjectAmrPlan']['company_id'] == $data['company_id']))
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
            $this->Session->setFlash(__('Invalid project amr plan', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrPlan->save($this->data)) {
						$this->data = $this->ProjectAmrPlan->read(null, $id);
						$this->data = $this->data['ProjectAmrPlan'];
						$result = true;
                        $this->Session->setFlash(__('Saved', true),'success');
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    }
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAmrPlan->read(null, $id);
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     *
     * @param int $id
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project amr plan', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectAmrPlan'));
        $allowDeleteProjectAmrPlan = $this->_projectAmrPlanIsUsing($id);
        if($check && ($allowDeleteProjectAmrPlan == 'true')){
            if ($this->ProjectAmrPlan->delete($id)) {
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
     *  Kiem tra PROJECT AMR PLAN da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrPlanIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_plan_id' => $id)
            ));
        $allowDeleteProjectAmrPlan= 'true';
        if($checkProjectAmr != 0){
            $allowDeleteProjectAmrPlan = 'false';
        }
        
        return $allowDeleteProjectAmrPlan;
    }

}
?>