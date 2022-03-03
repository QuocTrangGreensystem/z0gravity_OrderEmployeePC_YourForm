<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityRequestConfirm extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ActivityRequestConfirm';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'start' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The date is not blank!'
            ),
        ),
        'end' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The date is not blank!'
            ),
        ),
        'employee_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Employee is not blank!'
            )
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
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
?>