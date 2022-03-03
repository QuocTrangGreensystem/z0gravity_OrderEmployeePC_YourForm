<?php
class TicketProfile extends AppModel {
	public $validate = array(
		'name' => array(
            'allowEmpty' => false,
            'rule' => array('notempty'),
            'message' => 'Cannot be empty'
        ),
        'description_fre' => array(
            'allowEmpty' => false,
            'rule' => array('notempty'),
            'message' => 'Cannot be empty'
        ),
        'description_eng' => array(
            'allowEmpty' => false,
            'rule' => array('notempty'),
            'message' => 'Cannot be empty'
        )
	);

	public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }
}