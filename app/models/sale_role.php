<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleRole extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'SaleRole';
    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'employee_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Employee is not blank!',
                'allowEmpty' => false
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Company is not blank!',
                'allowEmpty' => true
            ),
        ),
        'sale_role' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Role is not blank!',
                'allowEmpty' => true
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