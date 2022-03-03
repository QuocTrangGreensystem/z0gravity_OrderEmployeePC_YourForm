<?php

class TicketProfilesController extends AppController {
	public $helpers = array('Validation');
	public $permissions = array(
		'can_create' => array(
			1 => 'Create tickets'
		),
		'can_view' => array(
			1 => 'View only my tickets',
			2 => 'View all my company\'s tickets'
		),
		'can_update' => array(
			1 => 'Update only my tickets',
			2 => 'Update all my company\'s tickets'
		)
	);
	public function beforeFilter(){
		parent::beforeFilter();
		$this->roles = array('customer' => __('Customer', true), 'developer' => __('Developer', true));
		$this->set('roles', $this->roles);
		$this->set('permissions', $this->permissions);
	}

	public function index(){
		$cid = $this->employee_info['Company']['id'];
		$data = $this->TicketProfile->find('all', array(
			'conditions' => array(
				'company_id' => $cid
			)
		));
		$this->set(compact('cid', 'data'));
	}

	public function update($name){
		if( !empty($this->data) ){
			$save = array(
				'company_id' => $this->employee_info['Company']['id'],
				'name' => $this->data['TicketProfile']['name'],
				'role' => $this->data['TicketProfile']['role'],
				'can_view' => $this->data['TicketProfile']['can_view'],
				'can_update' => $this->data['TicketProfile']['can_update'],
				'can_create' => $this->data['TicketProfile']['can_create'],
				'description_eng' => $this->data['TicketProfile']['description_eng'],
				'description_fre' => $this->data['TicketProfile']['description_fre']
			);
			if( !$this->data['TicketProfile']['id'] ){
				$this->TicketProfile->create();
			} else {
				$this->TicketProfile->id = $this->data['TicketProfile']['id'];
			}
			$this->TicketProfile->save($save);
			$this->Session->setFlash(__('Saved', true), 'success');
			$this->redirect(array('action' => 'index', $name));
		}
		$this->Session->setFlash(__('Not saved', true), 'error');
		$this->redirect(array('action' => 'index', $name));
	}

	public function delete($id){
		$data = $this->TicketProfile->read(null, $id);
		if( !empty($data) && $data['TicketProfile']['company_id'] == $this->employee_info['Company']['id'] ){
			//do delete
			$this->Session->setFlash(__('Deleted', true), 'success');
			$this->TicketProfile->delete($id);
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Not deleted', true), 'error');
		$this->redirect(array('action' => 'index'));
	}
}