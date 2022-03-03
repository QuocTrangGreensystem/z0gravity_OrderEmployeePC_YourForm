<?php

class TicketCommentsController extends AppController {
	public function update(){
		$data = array();
		if( !empty($this->data) ){
			$this->data['employee_id'] = $this->employee_info['Employee']['id'];
			if( empty($this->data['id']) ){
				$this->TicketComment->create();
			} else {
				$this->TicketComment->id = $this->data['id'];
			}
			$this->TicketComment->save($this->data);
			$data = $this->TicketComment->read();
			$data = $data['TicketComment'];
			if(!empty($data)){
				$this->loadModel('Ticket');
				$this->Ticket->saveUpdated($data['ticket_id'], $this->employee_info['Employee']['id'], time());
			}
			// time
			$data['time'] = date('H:i, d-m-Y', strtotime($data['created']));
		}
		die(json_encode($data));
	}

	public function delete(){
		if( !empty($this->data) ){
			if($this->TicketComment->delete($this->data['id'])){
				$this->loadModel('Ticket');
				$this->Ticket->saveUpdated($this->data['ticket_id'], $this->employee_info['Employee']['id'], time());
				die('1');
			}
		}
		die('0');
	}
}