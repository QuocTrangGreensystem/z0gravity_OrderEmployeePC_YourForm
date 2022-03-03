<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrOrganizationsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrOrganizations';

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
        $this->ProjectAmrOrganization->recursive = 0;
        $companies = $this->ProjectAmrOrganization->Company->find('list');
        $parent_companies = $this->ProjectAmrOrganization->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_organization', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrOrganization->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_organization', 'company_id'),
                'limit' => 1000,
            );
            $this->set('company_names', $this->ProjectAmrOrganization->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrOrganizations', $this->paginate());
    }

    /**
     * view
     * @param int $id
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr organization', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrOrganization', $this->ProjectAmrOrganization->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrOrganization->create();
            if ($this->ProjectAmrOrganization->save($this->data)) {
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
            $rs = $this->ProjectAmrOrganization->find('count', array('conditions' => array(
                    'ProjectAmrOrganization.amr_organization' => $data['amr_organization'],
                    'ProjectAmrOrganization.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrOrganization->find('count', array('conditions' => array(
                    'ProjectAmrOrganization.amr_organization' => $data['amr_organization'],
                    'ProjectAmrOrganization.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrOrganization.id' => $data['id'])
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
        $rs = $this->ProjectAmrOrganization->find('first', array('conditions' => array('ProjectAmrOrganization.id' => $data['id'])));
        if ($rs['ProjectAmrOrganization']['amr_organization'] == $data['amr_organization'] && ($rs['ProjectAmrOrganization']['company_id'] == $data['company_id']))
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
            $this->Session->setFlash(__('Invalid project amr organization', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrOrganization->save($this->data)) {
						$this->data = $this->ProjectAmrOrganization->read(null, $id);
						$this->data = $this->data['ProjectAmrOrganization'];
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
            $this->data = $this->ProjectAmrOrganization->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project amr organization', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectAmrOrganization'));
        $allowDeleteProjectAmrOgranization = $this->_projectAmrOgranizationIsUsing($id);
        if($check && ($allowDeleteProjectAmrOgranization == 'true')){
            if ($this->ProjectAmrOrganization->delete($id)) {
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
     *  Kiem tra PROJECT AMR OGRANINATION da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrOgranizationIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_organization_id' => $id)
            ));
        $allowDeleteProjectAmrOgranization= 'true';
        if($checkProjectAmr != 0){
            $allowDeleteProjectAmrOgranization = 'false';
        }
        
        return $allowDeleteProjectAmrOgranization;
    }

}
?>