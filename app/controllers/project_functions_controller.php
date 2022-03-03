<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectFunctionsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectFunctions';

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
        $companies1 = $this->ProjectFunction->Company->find('list');
        $parent_companies = $this->ProjectFunction->Company->find('list', array('fields' => array('id', 'parent_id',)));
        $this->set(compact('companies1', 'parent_companies'));

        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $companyId = $this->employee_info["Company"]["id"];
        else
            $companyId = "";
        $this->set('company_id', $companyId);

        $this->ProjectFunction->recursive = 0;
        if ($companyId != "") {
            $this->paginate = array(
                'fields' => array('name', 'company_id'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $companyId, 'parent_id' => $companyId))
            );
            $this->set('companies', $this->ProjectFunction->Company->getTreeList($companyId));
        } else {
            $this->paginate = array(
                'fields' => array('name', 'company_id'),
                'limit' => 1005,
            );
            $this->set('companies', $this->ProjectFunction->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectFunctions', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project function', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectFunction', $this->ProjectFunction->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project function', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectFunction', 'name')){
                if ($this->ProjectFunction->save($this->data)) {
					$this->data = $this->ProjectFunction->read(null, $id);
					$this->data = $this->data['ProjectFunction'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
             }else{
                $this->Session->setFlash(__('Already exist', true), 'error');
             } 
             // $this->redirect(array('action' => 'index'));    
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectFunction->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project function', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectFunction'));
        $allowDeleteProjectFunction = $this->_projectFunctionIsUsing($id);
        if($check && ($allowDeleteProjectFunction == 'true')){
            if ($this->ProjectFunction->delete($id)) {
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
     *  Kiem tra project function da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectFunctionIsUsing($id = null){
        $this->loadModel('ProjectTeam');
        $checkProjectTeam = $this->ProjectTeam->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectTeam.project_function_id' => $id)
            ));
        $allowDeleteProjectFunction= 'true';
        if($checkProjectTeam != 0){
            $allowDeleteProjectFunction = 'false';
        }
        
        return $allowDeleteProjectFunction;
    }
}
?>