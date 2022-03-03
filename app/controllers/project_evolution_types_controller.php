<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectEvolutionTypesController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $name = 'ProjectEvolutionTypes';
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
        $this->ProjectEvolutionType->recursive = 0;
        $companies = $this->ProjectEvolutionType->Company->find('list');
        $parent_companies = $this->ProjectEvolutionType->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('project_type_evolution', 'company_id'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            //$this->set('company_names',$this->ProjectStatus->Company->find('list',array('fields'=>array('Company.company_name'),'conditions'=>array('Company.id'=>$company_id)))) ;
            $this->set('company_names', $this->ProjectEvolutionType->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('project_type_evolution', 'company_id'),
                'limit' => 1005
            );
            //$this->set('company_names',$this->ProjectStatus->Company->find('list',array('fields'=>array('Company.company_name')))) ;
            $this->set('company_names', $this->ProjectEvolutionType->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectEvolutionTypes', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project evolution type', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectEvolutionType', $this->ProjectEvolutionType->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project evolution type', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectEvolutionType', 'project_type_evolution')){
                if ($this->ProjectEvolutionType->save($this->data)) {
					$this->data = $this->ProjectEvolutionType->read(null, $id);
					$this->data = $this->data['ProjectEvolutionType'];
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
            $this->data = $this->ProjectEvolutionType->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project evolution type', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectEvolutionType'));
        $allowDeleteProjectEvolutionType = $this->_projectEvolutionTypeIsUsing($id);
        if($check && ($allowDeleteProjectEvolutionType == 'true')){
            if ($this->ProjectEvolutionType->delete($id)) {
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
     *  Kiem tra city da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectEvolutionTypeIsUsing($id = null){
        $this->loadModel('ProjectEvolution');
        $checkProjectEvolution = $this->ProjectEvolution->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectEvolution.project_evolution_type_id' => $id)
            ));
        $allowDeleteProjectEvolutionType= 'true';
        if($checkProjectEvolution != 0){
            $allowDeleteProjectEvolutionType = 'false';
        }
        
        return $allowDeleteProjectEvolutionType;
    }
}
?>