<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectRiskSeveritiesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectRiskSeverities';

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
        $companies1 = $this->ProjectRiskSeverity->Company->find('list');
        $parent_companies = $this->ProjectRiskSeverity->Company->find('list', array('fields' => array('id', 'parent_id',)));
        $this->set(compact('companies1', 'parent_companies'));

        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $companyId = $this->employee_info["Company"]["id"];
        else
            $companyId = "";

        $this->set('company_id', $companyId);
        $this->ProjectRiskSeverity->recursive = 0;
        if ($companyId != "") {
            $this->paginate = array(
                'fields' => array('risk_severity', 'company_id'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $companyId, 'parent_id' => $companyId))
            );
            $this->set('companies', $this->ProjectRiskSeverity->Company->getTreeList($companyId));
        } else {
            $this->paginate = array(
                'fields' => array('risk_severity', 'company_id'),
                'limit' => 1005,
            );
            $this->set('companies', $this->ProjectRiskSeverity->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectRiskSeverities', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project risk severity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectRiskSeverity', $this->ProjectRiskSeverity->read(null, $id));
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
            $this->Session->setFlash(__('Invalid project risk severity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'ProjectRiskSeverity', 'risk_severity')){
				$this->loadModel('ProjectRiskSeverity');
				
				if (empty($this->data["id"])) {
					$this->ProjectRiskSeverity->recursive = -1;
					$last_order = $this->ProjectRiskSeverity->find("first", array(
						"fields" => array("(Max(ProjectRiskSeverity.value_risk_severitie)+1) risk_last_value"),
						"conditions" => array("ProjectRiskSeverity.company_id" => $this->data["company_id"])));
						$this->data["value_risk_severitie"] = $last_order[0]["risk_last_value"];
				}else{
					$listValueRisk = $this->ProjectRiskSeverity->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $this->data["company_id"],
							'value_risk_severitie is NULL'
						),
						'fields' => array('id')
					));
					$countRisk = count($listValueRisk);
					if($countRisk > 0){
						$this->data["value_risk_severitie"] = $countRisk - 1;
					}
				}
                if ($this->ProjectRiskSeverity->save($this->data)) {
					$this->data = $this->ProjectRiskSeverity->read(null, $id);
					$this->data = $this->data['ProjectRiskSeverity'];
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
            $this->data = $this->ProjectRiskSeverity->read(null, $id);
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
            $this->Session->setFlash(__('Invalid id for project risk severity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectRiskSeverity'));
        $allowDeleteProjectRiskSeverity = $this->_projectRiskSeverityIsUsing($id);
        if($check && ($allowDeleteProjectRiskSeverity == 'true')){
            if ($this->ProjectRiskSeverity->delete($id)) {
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
     * Duplicate project risk severities
     * 
     * @param int $ser, $com, $risk
     * @return void
     * @access public
     */
    function duplicate($ser = null, $com = null, $risk = null) {
        $this->autoRender = false;
        if ($ser != "" && $com != "") {
            $check = $this->ProjectRiskSeverity->find("count", array("conditions" => array(
                    "ProjectRiskSeverity.risk_severity" => $ser,
                    "ProjectRiskSeverity.company_id" => $com,
                    )));
            if ($risk != "") {
                $check = $this->ProjectRiskSeverity->find("count", array("conditions" => array(
                        "ProjectRiskSeverity.risk_severity" => $ser,
                        "ProjectRiskSeverity.company_id" => $com,
                        "NOT" => array("ProjectRiskSeverity.id" => $risk)
                        )));
            }
            echo $check;
        }
    }
     /**
     *  Kiem tra project risk severity da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectRiskSeverityIsUsing($id = null){
        $this->loadModel('ProjectRisk');
        $checkProjectRisk = $this->ProjectRisk->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectRisk.project_risk_severity_id' => $id)
            ));
        $allowDeleteProjectRiskSeverity= 'true';
        if($checkProjectRisk != 0){
            $allowDeleteProjectRiskSeverity = 'false';
        }
        
        return $allowDeleteProjectRiskSeverity;
    }
}
?>