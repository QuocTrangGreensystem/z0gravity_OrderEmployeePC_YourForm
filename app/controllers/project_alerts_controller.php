<?php
/**
 * z0 Gravity�
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectAlertsController extends AppController {
	/**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAlerts';
	var $helpers = array('Validation');

	/**
     * Index
     *
     * @return void
     * @access public
     */
    function index() {
		$this->ProjectAlert->recursive = 0;
		$this->loadModels('Company');
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $companyId = $this->employee_info["Company"]["id"];
        } else{
            $companyId = "";
        }
        if ($companyId != "") {
            $this->paginate = array(
                //phan trang
                'conditions' => array('OR' => array('ProjectAlert.company_id' => $companyId)),
                'fields' => array('id', 'alert_name', 'company_id', 'display', 'number_of_day'),
                'order' => array('company_id ASC', 'alert_name ASC'),
                'limit' => 1000,
            );
            $this->set('companies', $this->Company->getTreeList($companyId));
        } else {
            $this->paginate = array(
                //phan trang
				'fields' => array('alert_name', 'company_id', 'display', 'number_of_day'),
                'order' => array('company_id ASC', 'alert_name ASC'),
                'limit' => 1000,
            );
            $this->set('companies', $this->Company->generateTreeList(null, null, null, '--'));
        }
        $projectAlerts = $this->paginate();
        $this->set(compact('companyId', 'projectAlerts'));
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
            $this->Session->setFlash(__('Invalid project alert', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
			// them company_id
			$this->data['company_id'] = $this->employee_info["Company"]["id"];
            if($this->_checkDuplicate($this->data, 'ProjectAlert', 'alert_name')){
                if ($this->ProjectAlert->save($this->data)) {
					$this->data = $this->ProjectAlert->read(null, $id);
					$this->data = $this->data['ProjectAlert'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true),'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
            // $this->redirect(array('action' => 'index'));
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAlert->read(null, $id);
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
			$this->Session->setFlash(__('Invalid id project alert', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectAlert'));
		if($check){
			if ($this->ProjectAlert->delete($id)) {
				$this->Session->setFlash(__('Deleted', true), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}
		$this->Session->setFlash(__('Not deleted', true), 'error');
		$this->redirect(array('action' => 'index'));
	}
}
