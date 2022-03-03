<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrSubCategoriesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrSubCategories';
    //var $layout = 'administrator'; 

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
        $this->ProjectAmrSubCategory->recursive = 2;
        $projectAmrSubCategories = array();
        $this->set('projectPrograms', $this->ProjectAmrSubCategory->ProjectAmrCategory->find("list", array(
                    "fields" => array("ProjectAmrCategory.id",
                        "ProjectAmrCategory.amr_category"))));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        if ($company_id != "") {
            $companies = $this->ProjectAmrSubCategory->ProjectAmrCategory->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $company_id, 'Company.parent_id' => $company_id))));
            foreach ($companies as $company) {
                $ProjectAmrCategorys = $this->ProjectAmrSubCategory->ProjectAmrCategory->find('all', array('conditions' => array('OR' => array('ProjectAmrCategory.company_id' => $company['Company']['id']))));
                foreach ($ProjectAmrCategorys as $ProjectAmrCategory) {
                    $projectAmrSubCategories = array_merge($projectAmrSubCategories, $this->ProjectAmrSubCategory->find('all', array('conditions' => array('ProjectAmrSubCategory.project_amr_category_id' => $ProjectAmrCategory['ProjectAmrCategory']['id']))));
                }
            }
            $this->set('company_names', $this->ProjectAmrSubCategory->ProjectAmrCategory->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('amr_category', 'company_id'),
                'limit' => 1005
            );
            $projectAmrSubCategories = $this->ProjectAmrSubCategory->find('all');
            $this->set('company_names', $this->ProjectAmrSubCategory->ProjectAmrCategory->Company->generateTreeList(null, null, null, '--'));
        }
		$this->loadModel('ProjectAmrCategory');
		$displayAMRCategory = $this->ProjectAmrCategory->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'amr_category')
		));
        $this->set('projectAmrSubCategories', $projectAmrSubCategories);
        $this->set('displayAMRCategory', $displayAMRCategory);
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr sub category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrSubCategory', $this->ProjectAmrSubCategory->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrSubCategory->create();
            if ($this->ProjectAmrSubCategory->save($this->data)) {
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
            $rs = $this->ProjectAmrSubCategory->find('count', array('conditions' => array(
                    'ProjectAmrSubCategory.amr_sub_category' => $data['amr_sub_category'],
                    'ProjectAmrSubCategory.project_amr_category_id' => $data['project_amr_category_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrSubCategory->find('count', array('conditions' => array(
                    'ProjectAmrSubCategory.amr_sub_category' => $data['amr_sub_category'],
                    'ProjectAmrSubCategory.project_amr_category_id' => $data['project_amr_category_id'],
                    'NOT' => array('ProjectAmrSubCategory.id' => $data['id'])
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
        $rs = $this->ProjectAmrSubCategory->find('first', array('conditions' => array('ProjectAmrSubCategory.id' => $data['id'])));
        if ($rs['ProjectAmrSubCategory']['amr_sub_category'] == $data['amr_sub_category']
                && ($rs['ProjectAmrCategory']['company_id'] == $data['company_id'])
                && ($rs['ProjectAmrSubCategory']['project_amr_category_id'] == $data['project_amr_category_id']))
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
            $this->Session->setFlash(__('Invalid project amr sub category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectAmrSubCategory->save($this->data)) {
						$this->data = $this->ProjectAmrSubCategory->read(null, $id);
						$this->data = $this->data['ProjectAmrSubCategory'];
						$result = true;
                        $this->Session->setFlash(__('Saved', true),'success');
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    }
                } else {
                    // $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
                // $this->redirect(array('action' => 'index'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAmrSubCategory->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project amr sub category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $item = $this->ProjectAmrSubCategory->find('first', array(
			'recursive' => -1,
			'conditions' => array( 
				'ProjectAmrSubCategory.id' => $id,
			),
			'fields' => array('project_amr_category_id')
		));
		$check = ($this->is_sas || $this->_isBelongToCompany($item['ProjectAmrSubCategory']['project_amr_category_id'], 'ProjectAmrCategory'));
		$allowDeleteProjectAmrSubCategory = $this->_projectAmrSubCategoryIsUsing($id);
        if($check && ($allowDeleteProjectAmrSubCategory == 'true')){
            if ($this->ProjectAmrSubCategory->delete($id)) {
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
     * get_amr
     *
     * @return void
     * @access public
     */
    function get_amr($company_id = null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $CountryIds = $this->ProjectAmrSubCategory->ProjectAmrCategory->find('all', array('conditions' => array('ProjectAmrCategory.company_id' => $company_id)));
            if (!empty($CountryIds)) {

                foreach ($CountryIds as $CountryId) {
                    echo "<option value='" . $CountryId['ProjectAmrCategory']['id'] . "'>" . $CountryId['ProjectAmrCategory']['amr_category'] . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option value="">', '</option>');
            }
        }
    }

    /**
     * get_2amr
     * get sub category
     * @param int $amr_id
     * @return void
     * @access public
     */
    function get_2amr($amr_id = null) {
        $this->autoRender = false;
        if ($amr_id != "") {
            $CountryIds = $this->ProjectAmrSubCategory->ProjectAmrCategory->find('all', array('conditions' => array('ProjectAmrCategory.id' => $amr_id)));
            if (!empty($CountryIds)) {

                foreach ($CountryIds as $CountryId) {
                    echo $CountryId['ProjectAmrCategory']['company_id'];
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }
    /**
     *  Kiem tra Project Amr Sub Category da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrSubCategoryIsUsing($id = null){
        $this->loadModel('ProjectAmr');
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_sub_category_id' => $id)
            ));
        $allowDeleteProjectAmrSubCategory= 'true';
        if($checkProjectAmr != 0){
            $allowDeleteProjectAmrSubCategory = 'false';
        }
        
        return $allowDeleteProjectAmrSubCategory;
    }

}
?>