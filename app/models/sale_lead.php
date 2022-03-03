<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleLead extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'SaleLead';
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
                'allowEmpty' => true
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Name is not blank!',
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
    
    /**
     * chinh sua lai name cua cac field
     */
    public $formatFields = array(
        'code' => 'ID',
        'customer_id' => 'Customer ID',
        'sale_customer_id' => 'Customer',
        'sale_customer_contact_id' => 'Contact',
        'sale_setting_lead_maturite' => 'Maturity',
        'sale_setting_lead_phase' => 'Phase'
    );
    
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
            $field = str_replace('Sale.', '', $field);
            if(in_array($field, array_keys($this->formatFields))){
                $fields = $this->formatFields[$field];
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
    
    /**
     * Get View Field 
     *
     * @return array fieldset
     * @access public
     */
    public function getViewFieldNames() {
        $fieldset = array();
        foreach (array_keys($this->schema()) as $name) {
            if($name != 'id' && $name != 'deal_status' && $name != 'deal_renewal_date'){
                if(in_array($name, array_keys($this->formatFields))){
                    $fieldset['Sale.' . $name] = $this->formatFields[$name];
                } else {
                    $fieldset['Sale.' . $name] = Inflector::humanize(preg_replace('/_id$/', '', $name));
                }
            }
        }
        $fieldset['Sale.salesman'] = 'Salesman';
        $fieldset['Sale.leadLog'] = 'Lead Log';
        return $fieldset;
    }
    
    /**
     * Get View Field 
     *
     * @return array fieldset
     * @access public
     */
    public function getViewFieldNameForDeals() {
        $fieldset = array();
        foreach (array_keys($this->schema()) as $name) {
            if($name != 'id' && $name != 'goal' && $name != 'status' && $name != 'sale_setting_lead_maturite' && $name != 'sale_setting_lead_phase'){
                if(in_array($name, array_keys($this->formatFields))){
                    $fieldset['Sale.' . $name] = $this->formatFields[$name];
                } else {
                    $fieldset['Sale.' . $name] = Inflector::humanize(preg_replace('/_id$/', '', $name));
                }
            }
        }
        $fieldset['Sale.salesman'] = 'Salesman';
        $fieldset['Sale.deal_manager'] = 'Deal Manager';
        $fieldset['Sale.dealLog'] = 'Deal Log';
        return $fieldset;
    }

}
?>