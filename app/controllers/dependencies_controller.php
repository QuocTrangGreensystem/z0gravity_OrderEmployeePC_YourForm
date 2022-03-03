<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class DependenciesController extends AppController {
    public $helpers = array('Validation');
    public function index($view = ''){
        $this->set('data', $this->Dependency->find('all', array(
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            )
        )));
        $this->set(compact('view'));
    }
    public function update($id = null){
		$result = false;
		$this->layout = false;
		// debug($this->data);
		// exit;
        if( isset($this->data) ){
            $data = $this->data;
            $data['company_id'] = $this->employee_info["Company"]["id"];
            if( $this->Dependency->save($data) ){
				$this->data = $this->Dependency->read(null, $id);
				$this->data = $this->data['Dependency'];
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
        $data = $this->Dependency->read(null, $id);
        if( !empty($data) && $data['Dependency']['company_id'] == $cid ){
            $this->Dependency->delete($id);
            $this->Session->setFlash(__('Deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Unauthorized deleting data', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
    }
}
