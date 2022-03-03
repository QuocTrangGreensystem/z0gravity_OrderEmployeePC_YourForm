<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectIssueSeveritiesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectIssueSeverities';

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
        $companies = $this->ProjectIssueSeverity->Company->find('list');
        $parent_companies = $this->ProjectIssueSeverity->Company->find('list', array('fields' => array('id', 'parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        $this->ProjectIssueSeverity->recursive = 0;
        if ($company_id != "") {
            $this->paginate = array(
                'fields' => array('issue_severity', 'company_id', 'color'),
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id)),
                'limit' => 1005,
            );
            $this->set('company_names', $this->ProjectIssueSeverity->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('issue_severity', 'company_id', 'color'),
                'limit' => 1005,
            );
            $this->set('company_names', $this->ProjectIssueSeverity->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectIssueSeverities', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project issue severity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectIssueSeverity', $this->ProjectIssueSeverity->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project issue severity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectIssueSeverity', 'issue_severity')){
                if ($this->ProjectIssueSeverity->save($this->data)) {
					$this->data = $this->ProjectIssueSeverity->read(null, $id);
					$this->data = $this->data['ProjectIssueSeverity'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true),'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
             }else{
                $this->Session->setFlash(__('Already exist', true), 'error');
             }
             // $this->redirect(array('action' => 'index'));
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectIssueSeverity->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project issue severity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectIssueSeverity'));
        $allowDeleteProjectIssueSeverity = $this->_projectIssueSeverityIsUsing($id);
        if($check && ($allowDeleteProjectIssueSeverity == 'true')){
            if ($this->ProjectIssueSeverity->delete($id)) {
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
     * duplicate
     *
     * @param int $ser, $com, $iss
     * @return void
     * @access public
     */
    function duplicate($ser = null, $com = null, $iss = null) {
        $this->autoRender = false;
        if ($ser != "" && $com != "") {
            $check = $this->ProjectIssueSeverity->find("count", array("conditions" => array(
                    "ProjectIssueSeverity.issue_severity" => $ser,
                    "ProjectIssueSeverity.company_id" => $com,
                    )));
            if ($iss != "") {
                $check = $this->ProjectIssueSeverity->find("count", array("conditions" => array(
                        "ProjectIssueSeverity.issue_severity" => $ser,
                        "ProjectIssueSeverity.company_id" => $com,
                        "NOT" => array("ProjectIssueSeverity.id" => $iss)
                        )));
            }
            echo $check;
        }
    }
    /**
     *  Kiem tra project issue severity da co su dung
     *  @return boolean
     *  @access private
     */

    private function _projectIssueSeverityIsUsing($id = null){
        $this->loadModel('ProjectIssue');
        $checkProjectIssue = $this->ProjectIssue->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectIssue.project_issue_severity_id' => $id)
            ));
        $allowDeleteProjectIssueSeverity= 'true';
        if($checkProjectIssue != 0){
            $allowDeleteProjectIssueSeverity = 'false';
        }

        return $allowDeleteProjectIssueSeverity;
    }
}
?>
