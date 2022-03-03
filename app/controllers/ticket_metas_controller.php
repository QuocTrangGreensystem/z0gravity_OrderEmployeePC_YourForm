<?php

class TicketMetasController extends AppController {
	private $metaNames = array(
		'type' => 'Type',
		'function' => 'Function',
		'priority' => 'Priority',
		'version' => 'Version'
	);
	public $helpers = array('Validation');

	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function index($name = null){
		if( !isset($this->metaNames[$name]) ){
			$name = 'type';
		}
		$cid = $this->employee_info['Company']['id'];
		$data = $this->TicketMeta->find('all', array(
			'conditions' => array(
				'company_id' => $cid,
				'meta_name' => $name
			),
			// 'fields' => array('id', 'meta_value')
		));
		$this->set('title', __($this->metaNames[$name], true));
		$this->set(compact('cid', 'name', 'data'));
	}

	public function update(){
		$result = false;
		$this->layout = false;
		if( !empty($this->data) ){
			if( !empty($this->data['id'])){
				$data = $this->TicketMeta->read(null, $this->data['id']);
				if( empty($data) || ($data['TicketMeta']['company_id'] != $this->employee_info['Company']['id'] )){
					$this->Session->setFlash(__('Permission denied', true), 'error');
					$this->redirect($this->referer());
				}
			}
			$save = array(
				'company_id' => $this->employee_info['Company']['id'],
				'meta_value' => $this->data['name'],
				'meta_name' => $this->data['meta_name'],
				'enable_for_customer' => $this->data['enable_for_customer'],
				'category' => $this->data['category']
			);
			if( !$this->data['id'] ){
				$this->TicketMeta->create();
			} else {
				$this->TicketMeta->id = $this->data['id'];
			}
			if ($this->TicketMeta->save($save)){
				$this->data = $this->TicketMeta->read(null, $this->TicketMeta->id);
				$this->data = $this->data['TicketMeta'];
				$result = true;
				$this->Session->setFlash(__('Saved', true), 'success');
			} else {
				$this->Session->setFlash(__('NOT SAVED', true), 'error');
			}
		}
		$this->set(compact('result'));
	}

	public function delete($name, $id){
		$data = $this->TicketMeta->read(null, $id);
		if( !empty($data) && $data['TicketMeta']['company_id'] == $this->employee_info['Company']['id'] ){
			//do delete
			$this->Session->setFlash(__('Deleted', true), 'success');
			$this->TicketMeta->delete($id);
			$this->redirect(array('action' => 'index', $name));
		}
		$this->Session->setFlash(__('Not deleted', true), 'error');
		$this->redirect(array('action' => 'index', $name));
	}
	//api
	public function getAll(){
		return $this->metaNames;
	}
}