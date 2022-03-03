<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleLeadProductExpense extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'SaleLeadProductExpense';
    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'sale_lead_product_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Sale Lead Product ID is not blank!',
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
        'SaleLeadProduct' => array(
            'className' => 'SaleLeadProduct',
            'foreignKey' => 'sale_lead_product_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

}
?>