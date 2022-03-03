<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectComplexitiesController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $name = 'ProjectComplexities';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * Paginate
     *
     * @var string
     * @access public
     */
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
        $this->ProjectComplexity->recursive = 0;
        $companies = $this->ProjectComplexity->Company->find('list');
        $parent_companies = $this->ProjectComplexity->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                'fields' => array('name', 'company_id', 'display'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectComplexity->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('name', 'company_id', 'display'),
                'limit' => 1005
            );
            $this->set('company_names', $this->ProjectComplexity->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectComplexities', $this->paginate());
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
        $this->set('projectStatus', $this->ProjectComplexity->read(null, $id));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Implementation Complexity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectComplexity', 'name')){
                if ($this->ProjectComplexity->save($this->data)) {
                    $this->Session->setFlash(__('Saved', true), 'success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    $this->redirect(array('action' => 'index'));
                }
             }else{
                $this->Session->setFlash(__('Already exist', true), 'error');
                $this->redirect(array('action' => 'index'));
             }     
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectComplexity->read(null, $id);
        }
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for implementation complexity', true), 'error');
            $this->redirect(array('action' => 'index/'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectComplexity'));
        $allowDeleteProjectComplexity = $this->_projectComplexityIsUsing($id);
        if($check && ($allowDeleteProjectComplexity == 'true')){
            if ($this->ProjectComplexity->delete($id)) {
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
     *  Kiem tra project complexity da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectComplexityIsUsing($id = null){
        $this->loadModel('Project');
        $checkProject = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.complexity_id' => $id)
            ));
        $allowDeleteProjectComplexity= 'true';
        if($checkProject != 0){
            $allowDeleteProjectComplexity = 'false';
        }
        
        return $allowDeleteProjectComplexity;
    }
    /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->ProjectComplexity->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "name" => strtolower($name)
                )));
        return !$check;
    }

}
?>