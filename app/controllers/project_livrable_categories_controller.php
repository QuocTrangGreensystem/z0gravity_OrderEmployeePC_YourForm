<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectLivrableCategoriesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectLivrableCategories';
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
    function index($view = "") {
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $this->ProjectLivrableCategory->recursive = 0;
        $companies = $this->ProjectLivrableCategory->Company->find('list');
        $parent_companies = $this->ProjectLivrableCategory->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('livrable_cat', 'company_id', 'livrable_icon'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            //$this->set('company_names',$this->ProjectStatus->Company->find('list',array('fields'=>array('Company.company_name'),'conditions'=>array('Company.id'=>$company_id)))) ;
            $this->set('company_names', $this->ProjectLivrableCategory->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('livrable_cat', 'company_id', 'livrable_icon'),
                'limit' => 1005
            );
            //$this->set('company_names',$this->ProjectStatus->Company->find('list',array('fields'=>array('Company.company_name')))) ;
            $this->set('company_names', $this->ProjectLivrableCategory->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectLivrableCategories', $this->paginate());
        $this->set(compact('company_id', 'view'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project livrable category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectLivrableCategory', $this->ProjectLivrableCategory->read(null, $id));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project livrable category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            $view = !empty($this->data['ProjectLivrableCategory']['view']) ? $this->data['ProjectLivrableCategory']['view'] : '';
            unset($this->data['ProjectLivrableCategory']['view']);
            if($this->_checkDuplicate($this->data, 'ProjectLivrableCategory', 'livrable_cat')){
                if ($this->ProjectLivrableCategory->save($this->data)) {
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
            }else{
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
            $this->redirect(array('action' => 'index', $view));
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectLivrableCategory->read(null, $id);
        }
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project livrable category', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $allowDeleteProjectLivrableCategory = $this->_projectLivrableCategoryIsUsing($id);
        if($allowDeleteProjectLivrableCategory == 'true'){
            if ($this->ProjectLivrableCategory->delete($id)) {
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
     *  Kiem tra project livrable category da co su dung
     *  @return boolean
     *  @access private
     */

    private function _projectLivrableCategoryIsUsing($id = null){
        $this->loadModel('ProjectLivrable');
        $checkProjectLivrable = $this->ProjectLivrable->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectLivrable.project_livrable_category_id' => $id)
            ));
        $allowDeleteProjectLivrableCategory= 'true';
        if($checkProjectLivrable != 0){
            $allowDeleteProjectLivrableCategory = 'false';
        }

        return $allowDeleteProjectLivrableCategory;
    }
}
?>
