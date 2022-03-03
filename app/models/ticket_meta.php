<?php
class TicketMeta extends AppModel {
	public $validate = array(
		'meta_value' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'Cannot be empty'
            )
        )
	);

	public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }
}