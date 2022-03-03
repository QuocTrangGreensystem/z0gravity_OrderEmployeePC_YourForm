<?php

class TicketNotesController extends AppController {
	public function update(){
		$data = array();
		if( !empty($this->data) ){
			// if(  ){
			// 	$this->data['TicketNote']['employee_id'] = $this->employee_info['Employee']['id'];
			// }
			if( empty($this->data['id']) ){
				$this->TicketNote->create();
			} else {
				$this->TicketNote->id = $this->data['id'];
			}
			$this->TicketNote->save($this->data);
			$data = $this->TicketNote->read();
			$data = $data['TicketNote'];

			// time
			$data['time'] = date('H:i, d-m-Y', strtotime($data['created']));
		}
		die(json_encode($data));
	}

	public function delete(){
		if( !empty($this->data) ){
			$note = $this->TicketNote->read(null, $this->data['id']);
			$ticket_id = @$note['TicketNote']['ticket_id'];
			$check = $this->_isBelongToCompany($ticket_id, 'Ticket');
			if( $check && $this->TicketNote->delete($this->data['id'])){
				die('1');
			}
		}
		die('0');
	}
}