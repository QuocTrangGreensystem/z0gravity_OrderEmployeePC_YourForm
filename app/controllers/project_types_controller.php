<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectTypesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectTypes';

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
        $this->ProjectType->recursive = 0;
        $companies = $this->ProjectType->Company->find('list');
        $parent_companies = $this->ProjectType->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('ProjectType.project_type', 'ProjectType.company_id', 'ProjectType.display'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectType->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('ProjectType.project_type', 'ProjectType.company_id', 'ProjectType.display'),
                'limit' => 1005
            );
            $this->set('company_names', $this->ProjectType->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectTypes', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project type', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectType', $this->ProjectType->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectType->create();
            if ($this->ProjectType->save($this->data)) {
                $this->Session->setFlash(__('Saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true));
            }
        }
        $companies = $this->ProjectType->Company->find('list');
        $this->set(compact('companies'));
    }

    /**
     * check_dupplicate
     * Check duplicate
     *
     * @return void
     * @access public
     */
    function check_dupplicate($data) {
        if ($data['id'] == "") {
            // add new
            $rs = $this->ProjectType->find('count', array('conditions' => array(
                    'ProjectType.project_type' => $data['project_type'],
                    'ProjectType.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectType->find('count', array('conditions' => array(
                    'ProjectType.project_type' => $data['project_type'],
                    'ProjectType.company_id' => $data['company_id'],
                    'NOT' => array('ProjectType.id' => $data['id'])
                    )));
        }
        if ($rs == 0)
            return true; else
            return false;
    }

    /**
     * check_exists_data
     * Check project type exists
     *
     * @return void
     * @access public
     */
    function check_exists_data($data) {
        $data = !empty($data) ? $data : array();
        $result = false;
        if(!empty($data['id'])){
            $checkDatas = $this->ProjectType->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $data['company_id'],
                    'NOT' => array('ProjectType.id' => $data['id'])
                ),
                'fields' => array('id', 'project_type')
            ));
            if(!empty($checkDatas) && in_array($data['project_type'], $checkDatas)){
                $result = true;
            }
        } else {
            $checkDatas = $this->ProjectType->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $data['company_id'],
                    'ProjectType.project_type' => $data['project_type']
                )
            ));
            if($checkDatas != 0){
                $result = true;
            }
        }
        return $result;
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function update($id = null) {
		$result = false;
		$this->layout = false;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project type', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectType->save($this->data)) {
						$this->data = $this->ProjectType->read(null, $id);
						$this->data = $this->data['ProjectType'];
						$result = true;
                        $this->Session->setFlash(__('Saved', true), 'success');
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    }
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectType->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project type', true));
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectType'));
        $allowDeleteProjectType = $this->_projectTypeIsUsing($id);
        if($check && ($allowDeleteProjectType == 'true')){
            if ($this->ProjectType->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Deleted', true));
        $this->redirect(array('action' => 'index'));
    }
      /**
     *  Kiem tra project type da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectTypeIsUsing($id = null){
        $this->loadModel('Project');
        $this->loadModel('ProjectSubType');
        $checkProject = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.project_type_id' => $id)
            ));
        $checkProjectSubType = $this->ProjectSubType->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectSubType.project_type_id' => $id)
            ));
        $allowDeleteProjectType= 'true';
        if($checkProject != 0 || $checkProjectSubType != 0){
            $allowDeleteProjectType = 'false';
        }
        
        return $allowDeleteProjectType;
    }

}
?>