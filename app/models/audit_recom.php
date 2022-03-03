<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AuditRecom extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'AuditRecom';
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
        //'id_recommendation' => array(
//            'Unique'=>array(
//       		 	'rule' => 'isUnique',
//       		 	'message' => 'This ID Recommendation has already been taken.'
//    		)
//        ),
        'recommendation' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Recommendation is not blank!',
                'allowEmpty' => false
            ),
        ),
        //'audit_setting_recom_priority' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'Priority Recommendation is not blank!',
//                'allowEmpty' => false
//            ),
//        ),
//        'implement_date' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'Initial Implementation Date is not blank!',
//                'allowEmpty' => false
//            ),
//        ),
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