<?php

class TicketStatusesController extends AppController {
    
	public $helpers = array('Validation');
    
	public function beforeFilter(){
		parent::beforeFilter();
        $this->Auth->autoRedirect = false;
	}
    
	public function index(){
	    $this->loadModels('TicketProfile', 'TicketProfileStatusReference'); 
		$company_id = $this->employee_info['Company']['id'];
		$ticketStatuses = $this->TicketStatus->find('all', array(
			'conditions' => array(
				'company_id' => $company_id
			),
			'order' => array('weight' => 'ASC')
		));
        if(!empty($ticketStatuses)){
            $i = 1;
            foreach($ticketStatuses as $key => $ticketStatus){
                $dx = $ticketStatus['TicketStatus'];
                $this->TicketStatus->id = $dx['id'];
                $this->TicketStatus->save(array('weight' => $i));
                $ticketStatuses[$key]['TicketStatus']['weight'] = $i;
                $i++;
            }
        }
        $ticketProfiles = $this->TicketProfile->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name', 'role')
        ));
        $roleOfProfiles = !empty($ticketProfiles) ? Set::combine($ticketProfiles, '{n}.TicketProfile.id', '{n}.TicketProfile.role') : array();
        $ticketProfiles = !empty($ticketProfiles) ? Set::combine($ticketProfiles, '{n}.TicketProfile.id', '{n}.TicketProfile.name') : array();
        $statusIds = !empty($ticketStatuses) ? Set::classicExtract($ticketStatuses, '{n}.TicketStatus.id') : array();
        $profileOfStatuses = $this->TicketProfileStatusReference->find('list', array(
            'recursive' => -1,
            'conditions' => array('ticket_status_id' => $statusIds),
            'fields' => array('id', 'ticket_profile_id', 'ticket_status_id'),
            'group' => array('ticket_status_id', 'id')
        ));
		$this->set(compact('company_id', 'ticketStatuses', 'ticketProfiles', 'profileOfStatuses', 'roleOfProfiles'));
	}
    
    public function get_ticket_profile($company_id = null){
        $this->loadModels('TicketProfile');  
        $ticketProfiles = $this->TicketProfile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $this->layout = false;
        $this->set(compact('ticketProfiles'));
    }
    
    /**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        $this->layout = false;
        if (!empty($this->data)) {
            foreach ($this->data as $id => $weight) {
                $last = $this->TicketStatus->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('TicketStatus.id' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->TicketStatus->id = $last['TicketStatus']['id'];
                    $this->TicketStatus->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }
    
    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        $this->loadModels('TicketProfileStatusReference');
        if (!empty($this->data)) {
            $ticketProfiles = !empty($this->data['ticket_profile_id']) ? $this->data['ticket_profile_id'] : array();
            unset($this->data['ticket_profile_id']);
            $data = array(
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes'),
                'is_default' => (isset($this->data['is_default']) && $this->data['is_default'] == 'yes'),
                'send_sms' => (isset($this->data['send_sms']) && $this->data['send_sms'] == 'yes'),
                'company_id' => $this->data['company_id']
            );
            $this->TicketStatus->create();
            if (!empty($this->data['id'])) {
                $this->TicketStatus->id = $this->data['id'];
            }
            $data['acffected_cus'] = 0;
            $data['acffected_dep'] = 0;
            if(!empty($this->data['acffected_to'])){
                if(in_array('developer', $this->data['acffected_to'])){
                    $data['acffected_dep'] = 1;
                }
                if(in_array('customer', $this->data['acffected_to'])){
                    $data['acffected_cus'] = 1;
                }
                unset($this->data['acffected_to']);
            }
            if($this->TicketStatus->save(array_merge($this->data, $data))){
                $id = $this->TicketStatus->id;
                if(!empty($data['is_default'])){
                    $savedDefault = $this->TicketStatus->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $data['company_id'], 
                            'NOT' => array('TicketStatus.id' => $id)
                        ),
                        'fields' => array('id', 'id')
                    ));
                    $this->TicketStatus->updateAll(array('TicketStatus.is_default' => 0), array('TicketStatus.id' => $savedDefault));
                }
                /**
                 * Lay tat ca status reference
                 */
                $oldTickerProfiles = $this->TicketProfileStatusReference->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ticket_status_id' => $id),
                    'fields' => array('id', 'ticket_profile_id')
                ));
                if(!empty($ticketProfiles)){
                    foreach($ticketProfiles as $ticket_profile_id){
                        $check = $this->TicketProfileStatusReference->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('ticket_profile_id' => $ticket_profile_id, 'ticket_status_id' => $id),
                            'fields' => array('id')
                        ));
                        $this->TicketProfileStatusReference->create();
                        if(!empty($check) && !empty($check['TicketProfileStatusReference']['id'])){
                            $this->TicketProfileStatusReference->id = $check['TicketProfileStatusReference']['id'];
                        }
                        $saved = array(
                            'ticket_profile_id' => $ticket_profile_id, 
                            'ticket_status_id' => $id
                        );
                        if($this->TicketProfileStatusReference->save($saved)){
                            $lastId = $this->TicketProfileStatusReference->id;
                            unset($oldTickerProfiles[$lastId]);
                        }
                    }
                }
                if(!empty($oldTickerProfiles)){
                    foreach($oldTickerProfiles as $reId => $oldTickerProfile){
                        $this->TicketProfileStatusReference->delete($reId);
                    }
                }
                $this->data['id'] = $this->TicketStatus->id;
            }
            $result = true;
            $this->Session->setFlash(__('Saved', true), 'success');
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
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
        $this->loadModels('TicketProfileStatusReference');
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = $this->_isBelongToCompany($id, 'TicketStatus');
        if ($check && $this->TicketStatus->delete($id)) {
            $this->TicketProfileStatusReference->deleteAll(array('TicketProfileStatusReference.ticket_status_id' => $id), false);
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
    }
}