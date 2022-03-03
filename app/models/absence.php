<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Absence extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'Absence';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'The absence name is not blank!',
                'allowEmpty' => false
            ),
        ),
        'begin' => array(
            'notempty' => array(
                'rule' => array('date'),
                'message' => 'Please supply a valid date format.',
                'allowEmpty' => true
            ),
        ),
        'total' => array(
            'notempty' => array(
                'rule' => array('numeric'),
                'message' => 'Please supply a valid number.',
                'allowEmpty' => true,
            ),
        ),
        'print' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please supply a valid number.'
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
    public $hasMany = array(
        'EmployeeAbsence' => array(
            'className' => 'EmployeeAbsence',
            'foreignKey' => 'absence_id',
            'dependent' => true,
        ),
        'AbsenceHistory' => array(
            'className' => 'AbsenceHistory',
            'foreignKey' => 'absence_id',
            'dependent' => true,
        )
    );

    public function beforeValidate($options = array()) {
        if (!empty($this->data[$this->alias]['total']) && empty($this->data[$this->alias]['begin'])) {
            return false;
        }
        return parent::beforeValidate($options);
    }

}
?>