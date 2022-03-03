<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrCategoriesController extends AppController {

    var $amr_category = 'ProjectAmrCategories';

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
        $this->ProjectAmrCategory->recursive = 0;
        $companies = $this->ProjectAmrCategory->Company->find('list');
        $parent_companies = $this->ProjectAmrCategory->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_category', 'company_id'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrCategory->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_category', 'company_id'),
                'limit' => 1005
            );
            $this->set('company_names', $this->ProjectAmrCategory->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrCategories', $this->paginate());
    }

    /**
     * view
     * @param int $id
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$conditions = array();
		if ($this->employee_info["Employee"]["is_sas"] != 1)
            $conditions['company_id'] = $this->employee_info["Company"]["id"];

		$projectAmrCategory = $this->ProjectAmrCategory->find('all',  array(
            'recursive' => -1, 
            'conditions' => $conditions,
			'fields' => array('*'),
		));
	    $this->set('projectAmrCategory');
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
			if($this->employee_info["Employee"]["is_sas"] == 1 || ($this->employee_info["Employee"]["is_sas"] != 1 && ($this->employee_info["Employee"]['company_id'] == $this->data['ProjectAmrCategory']['company_id']))){
				$this->ProjectAmrCategory->create();
				if ($this->ProjectAmrCategory->save($this->data)) {
					$this->Session->setFlash(__('Saved', true), 'success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('NOT SAVED', true), 'error');
					$this->redirect(array('action' => 'index'));
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
            $rs = $this->ProjectAmrCategory->find('count', array('conditions' => array(
                    'ProjectAmrCategory.amr_category' => $data['amr_category'],
                    'ProjectAmrCategory.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrCategory->find('count', array('conditions' => array(
                    'ProjectAmrCategory.amr_category' => $data['amr_category'],
                    'ProjectAmrCategory.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrCategory.id' => $data['id'])
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
        $rs = $this->ProjectAmrCategory->find('first', array('conditions' => array('ProjectAmrCategory.id' => $data['id'], $conditions)));
        if (!empty($rs) && $rs['ProjectAmrCategory']['amr_category'] == $data['amr_category'] && ($rs['ProjectAmrCategory']['company_id'] == $data['company_id']))
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
            $this->Session->setFlash(__('Invalid project amr category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrCategory->save($this->data)) {
						$this->data = $this->ProjectAmrCategory->read(null, $id);
						$this->data = $this->data['ProjectAmrCategory'];
						$result = true;
                        $this->Session->setFlash(sprintf(__('Saved', true), '<strong>' . $this->data["amr_category"] . '</strong>'), 'success');
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    }
                } else {
                    // $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAmrCategory->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project amr category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $allowDeleteProjectAmrCategory = $this->_projectAmrCategoryIsUsing($id);
		$conditions = array();
		$conditions['id'] = $id;
		if ($this->employee_info["Employee"]["is_sas"] != 1)
            $conditions['company_id'] = $this->employee_info["Company"]["id"];

		$projectAmrCategory = $this->ProjectAmrCategory->find('first',  array(
            'recursive' => -1, 
            'conditions' => $conditions,
			'fields' => array('id'),
		));
        if($allowDeleteProjectAmrCategory == 'true' && !empty($projectAmrCategory)){
            if ($this->ProjectAmrCategory->delete($id)) {
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
     *  Kiem tra Project Amr Category da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrCategoryIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $this->loadModel('ProjectAmrSubCategory');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_category_id' => $id)
            ));
        $checkProjectAmrSubCategory = $this->ProjectAmrSubCategory->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmrSubCategory.project_amr_category_id' => $id)
            ));
        $allowDeleteProjectAmrCategory= 'true';
        if($checkProjectAmr != 0 || $checkProjectAmrSubCategory != 0){
            $allowDeleteProjectAmrCategory = 'false';
        }
        
        return $allowDeleteProjectAmrCategory;
    }
}
?>