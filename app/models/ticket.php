<?php
class Ticket extends AppModel {
    public $validate = array(
        'name' => array(
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

    public $fieldsets = array(
        'name' => 'Name',
        'type_id' => 'Type',
        'company_id' => 'Company',
        'delivery_date' => 'Delivery Date',
        'priority_id' => 'Priority',
        'type_id' => 'Type',
        'affections' => 'Affected to',
        'function_id' => 'Function',
        'version_id' => 'Version',
        'ticket_status_id' => 'Status',
        'employee_id' => 'Opened By',
        //'open_date' => 'Open Date',
        //'content' => 'Content',
        'created' => 'Open Date',
        'updated' => 'Updated',
        'employee_updated_id' => 'Updated by'
    );

    public $ignore = array(
        'open_date',
        'updated',
        'content'
    );
	/**
     * Get View Field 
     *
     * @return array fieldset
     * @access public
     */
    public function getViewFieldNames() {
        return $this->fieldsets;
    }
	public function saveUpdated($ticket_id, $employee_updated_id, $updated){
		if(!empty($employee_updated_id) && !empty($updated) && !empty($ticket_id)){
			$this->id = $ticket_id;
			$this->save(array(
				'updated' => date('Y-m-d h:i:s', $updated),
				'employee_updated_id' => $employee_updated_id
			));
		}
	}
    /**
     * Parse View Field 
     *
     * @param array $fields the fields map to read
     * @return array, fieldset the mapping config for extract data,
     *  field and contain option of Model::find
     * @access public
     */
    public function parseViewField($fields) {
        $fieldset = $contain = array();
        foreach ((array) $fields as $field) {
            // if( in_array($field, $this->ignore) ){
            //     continue;
            // }
            if(in_array($field, array_keys($this->fieldsets))){
                $fields = $this->fieldsets[$field];
                $fieldset[] = array(
                    'key' => $field,
                    'name' => $fields
                );
                 
            } else {
                $fieldset[] = array(
                    'key' => $field,
                    'name' => Inflector::humanize(preg_replace('/_id$/', '', $field))
                );
            }
        }
        return $fieldset;
    }

}