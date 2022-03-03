<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleCustomerIban extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'SaleCustomerIban';
    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Company is not blank!',
                'allowEmpty' => false
            ),
        ),
        'sale_customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer ID is not blank!',
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
        'SaleCustomer' => array(
            'className' => 'SaleCustomer',
            'foreignKey' => 'sale_customer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

}
?>