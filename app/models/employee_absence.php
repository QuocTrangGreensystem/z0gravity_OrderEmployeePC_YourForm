<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class EmployeeAbsence extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'EmployeeAbsence';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'begin' => array(
            'notempty' => array(
                'rule' => array('date'),
                'message' => 'Please supply a valid date format.',
                'allowEmpty' => false
            ),
        ),
        'total' => array(
            'notempty' => array(
                'rule' => array('numeric'),
                'message' => 'Please supply a valid number.',
                'allowEmpty' => false,
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
        'Absence' => array(
            'className' => 'Absence',
            'foreignKey' => 'absence_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    
}
?>