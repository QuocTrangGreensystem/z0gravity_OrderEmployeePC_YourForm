<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetPurchase extends AppModel {

    var $name = 'ProjectBudgetPurchase';

    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Activity' => array(
            'className' => 'Activity',
            'foreignKey' => 'activity_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'BudgetCustomer' => array(
            'className' => 'BudgetCustomer',
            'foreignKey' => 'budget_customer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
	var $hasMany = array(
        'ProjectBudgetPurchaseInvoice' => array(
            'className' => 'ProjectBudgetPurchaseInvoice',
            'foreignKey' => 'project_budget_purchase_id',
            'dependent' => true,
        )
    );
    var $validate = array();

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
}
?>
