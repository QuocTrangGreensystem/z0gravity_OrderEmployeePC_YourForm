<?php
class TicketStatus extends AppModel {

    public function getAll($company_id){
        $result = array();
        $l = $this->find('all', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'order' => array('weight' => 'ASC')
        ));
        if( !empty($l) ){
        	$result = Set::combine($l, '{n}.TicketStatus.id', '{n}.TicketStatus');
        }
        return $result;
    }

    public function getVisible($company_id, $profile){
        $result = array();
        $l = $this->find('all', array(
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1,
                'ticket_status_id IS NOT NULL'
            ),
            'joins' => array(
                array(
                    'table' => 'ticket_profile_status_references',
                    'alias' => 'Refer',
                    'type' => 'left',
                    'conditions' => array(
                        'ticket_profile_id' => $profile,
                        'ticket_status_id = TicketStatus.id'
                    )
                )
            ),
            'order' => array('weight' => 'ASC')
        ));
        if( !empty($l) ){
            $result = Set::combine($l, '{n}.TicketStatus.id', '{n}.TicketStatus');
        }
        return $result;
    }
}