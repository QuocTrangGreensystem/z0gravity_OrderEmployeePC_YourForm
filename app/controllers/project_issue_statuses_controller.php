<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectIssueStatusesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectIssueStatuses';

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
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $companies = $this->ProjectIssueStatus->Company->find('list');
        $parent_companies = $this->ProjectIssueStatus->Company->find('list', array('fields' => array('id', 'parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        $this->ProjectIssueStatus->recursive = 0;
        if ($company_id != "") {
            $this->paginate = array(
                'fields' => array('issue_status', 'company_id', 'status'),
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id)),
                'limit' => 1005,
            );
            $this->set('company_names', $this->ProjectIssueStatus->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('issue_status', 'company_id', 'status'),
                'limit' => 1005,
            );
            $this->set('company_names', $this->ProjectIssueStatus->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectIssueStatuses', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project issue status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectIssueStatus', $this->ProjectIssueStatus->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project issue status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectIssueStatus', 'issue_status')){
                if ($this->ProjectIssueStatus->save($this->data)) {
					$this->data = $this->ProjectIssueStatus->read(null, $id);
					$this->data = $this->data['ProjectIssueStatus'];
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
            $this->data = $this->ProjectIssueStatus->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project issue status', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectIssueStatus'));
        $allowDeleteProjectIssueStatus = $this->_projectIssueStatusIsUsing($id);
        if($check && ($allowDeleteProjectIssueStatus == 'true')){
            if ($this->ProjectIssueStatus->delete($id)) {
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
     *  Kiem tra project issue status da co su dung
     *  @return boolean
     *  @access private
     */

    private function _projectIssueStatusIsUsing($id = null){
        $this->loadModel('ProjectIssue');
        $checkProjectIssue = $this->ProjectIssue->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectIssue.project_issue_status_id' => $id)
            ));
        $allowDeleteProjectIssueStatus= 'true';
        if($checkProjectIssue != 0){
            $allowDeleteProjectIssueStatus = 'false';
        }

        return $allowDeleteProjectIssueStatus;
    }
}
?>
