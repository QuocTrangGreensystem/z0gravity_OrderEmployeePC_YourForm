<?php

class TicketSubscription extends AppModel {
	public function subscribe($resource, $ticket_id){
		$e = $this->find('all', array(
			'conditions' => array(
				'ticket_id' => $ticket_id,
				'employee_id' => $resource
			)
		));
		if( empty($e) ){
			$this->create();
			$this->save(array(
				'ticket_id' => $ticket_id,
				'employee_id' => $resource
			));
		}
	}
	public function unsubscribe($resource, $ticket_id){
		$e = $this->find('first', array(
			'conditions' => array(
				'ticket_id' => $ticket_id,
				'employee_id' => $resource
			)
		));
		if( !empty($e) ){
			$this->delete($e['TicketSubscription']['id']);
		}
	}

	public function getResourceSubscription($ticket_id){
		return $this->find('list', array(
			'conditions' => array(
				'ticket_id' => $ticket_id
			),
			'fields' => array('employee_id', 'employee_id')
		));
	}

	public function isSubscribed($resource, $ticket_id){
		
		return $this->find('count', array(
			'conditions' => array(
				'ticket_id' => $ticket_id,
				'employee_id' => $resource
			)
		)) > 0;
	}
}