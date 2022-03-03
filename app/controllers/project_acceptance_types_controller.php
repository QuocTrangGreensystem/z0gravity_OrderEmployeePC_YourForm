<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAcceptanceTypesController extends AppController {
    var $helpers = array('Validation');
	public function beforeFilter(){
		parent::beforeFilter();
		$this->loadModels('Company');
	}
	public function index(){
		if ($this->employee_info["Employee"]["is_sas"] != 1){
            $company_id = $this->employee_info["Company"]["id"];
	        $this->set('company_id', $company_id);
	        $this->set('types', $this->ProjectAcceptanceType->find('all', array(
	        	'conditions' => array('company_id' => $company_id),
	        	'order' => array('id' => 'ASC')
	        )));
	    } else {
	    	$this->Session->setFlash(__('SAS is not allowed.', true), 'error');
	    	$this->redirect('/administrators');
	    }
	}
	public function update($id = null){
		$result = false;
		$this->layout = false;
		if( isset($this->data) ){
			$data = $this->data;
			$data['company_id'] = $this->employee_info["Company"]["id"];
			if( $this->ProjectAcceptanceType->save($data) ){
						$this->data = $this->ProjectAcceptanceType->read(null, $id);
						$this->data = $this->data['ProjectAcceptanceType'];
						$result = true;
				$this->Session->setFlash(__('Saved', true), 'success');
			} else {
				$this->Session->setFlash(__('Not saved.', true), 'error');
			}
		}
		$this->set(compact('result'));
	}
	public function delete($id = null){
		$cid = $this->employee_info["Company"]["id"];
		$data = $this->ProjectAcceptanceType->read(null, $id);
		if( !empty($data) && $data['ProjectAcceptanceType']['company_id'] == $cid ){
			$this->ProjectAcceptanceType->delete($id);
			$this->Session->setFlash(__('Deleted', true), 'success');
		} else {
			$this->Session->setFlash(__('Unauthorized deleting data', true), 'error');
		}
		$this->redirect(array('action' => 'index'));
	}
}