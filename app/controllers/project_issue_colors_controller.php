<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectIssueColorsController extends AppController {
    public function beforeFilter(){
        parent::beforeFilter();
    }
    public $helpers = array('Validation');
    public function index(){
        $list = $this->ProjectIssueColor->find('all', array(
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            )
        ));
        $this->set('list', $list);
    }
    public function update($id = null){
		$result = false;
		$this->layout = false;
		if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project phase', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if( !empty($this->data) ){
            // xoa default neu co default = 1.
            if($this->data['default'] == 1){
                $last = $this->ProjectIssueColor->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'default' => 1
                    )
                ));
                if(!empty($last)){
                    $this->ProjectIssueColor->id = $last['ProjectIssueColor']['id'];
                    $this->ProjectIssueColor->save(array('default' => 0));
                }
            }
			
            if(empty($this->data['id'])){
				unset($this->data['id']);
                $this->ProjectIssueColor->create();
            } else {
				$id = $this->data['id'];
                $this->ProjectIssueColor->id = $this->data['id'];
            }
			
            if($this->ProjectIssueColor->save($this->data)){
				$this->data = $this->ProjectIssueColor->read(null, $id);
				$this->data = $this->data['ProjectIssueColor'];
				$result = true;
				$this->Session->setFlash(__('Saved', true), 'success');
			}else{
				$this->Session->setFlash(__('Not saved', true), 'error');
			}
		}
		$this->set(compact('result'));
    }

    public function delete($id = null){
		if (!$id) {
			$this->Session->setFlash(__('Invalid id project alert', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
        $data = $this->ProjectIssueColor->read(null, $id);
		$isUsing = $this->_projectIssueColorIsUsing($id);
        if( !empty($data) && !$isUsing && ($data['ProjectIssueColor']['company_id'] == $this->employee_info['Company']['id'] )){
            //do delete
            $message = '';
            $employ = $this->employee_info['Employee']['fullname'];
            $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Deleted', true) . ' ' .  __('Blocking Issue', true);
            $this->writeLog('', $this->employee_info, $message);
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->ProjectIssueColor->delete($id);
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
	private function _projectIssueColorIsUsing($id){
		$this->loadModels('ProjectIssue');
        return  $this->ProjectIssue->find('count', array(
            'recursive' => -1,
            'conditions' => array('ProjectIssue.project_issue_color_id' => $id)
        ));
	}
}