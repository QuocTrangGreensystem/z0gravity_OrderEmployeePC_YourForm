<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Report extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'Report';
    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'data' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Data is not blank!',
                'allowEmpty' => false
            ),
        ),
        'type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Type is not blank!',
                'allowEmpty' => false
            ),
        )
    );
    
     /**
     * I18n validate
     *
     * @param string $field The name of the field to invalidate
     * @param mixed $value Name of validation rule that was not failed, or validation message to
     *    be returned. If no validation key is provided, defaults to true.
     * 
     * @return void
     */
    public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }

    /**
     *  Detailed list of belongsTo associations.
     *
     * @var array
     */
    public $belongsTo = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
}
?>