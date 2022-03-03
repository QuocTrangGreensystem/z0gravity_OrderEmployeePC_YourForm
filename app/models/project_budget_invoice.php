<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectBudgetInvoice extends AppModel {

    var $name = 'ProjectBudgetInvoice';
    
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
        'ProjectBudgetSale' => array(
            'className' => 'ProjectBudgetSale',
            'foreignKey' => 'project_budget_sale_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    var $validate = array(
        //'title' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'The Part is not blank!',
//            )
//            //'isUnique' => array(
////                'rule' => 'isUnique',
////                'message' => 'The Part has already been exist.',
////            )
//        ),
//        'project_id' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'The Project is not blank!',
//            ),
//        )
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
    
    public function afterSave(){
        if(!empty($this->data[$this->alias])){
            $this->saveInvoiceToSyns($this->data[$this->alias]);
        }
    }
    
    /**
     * Save invoice into table project_budget_syns
     * 
     */
    public function saveInvoiceToSyns($datas = array()){
        $Invoice = ClassRegistry::init('ProjectBudgetInvoice');
        $BudgetSyn = ClassRegistry::init('ProjectBudgetSyn');
        if(!empty($datas) && (!empty($datas['project_id']) || !empty($datas['activity_id']))){
            if($datas['activity_id'] != 0){
                $conditions = array('activity_id' => $datas['activity_id']);
            } else {
                $conditions = array('project_id' => $datas['project_id']);
            }
            $invoices = $Invoice->find('all', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('billed', 'paid', 'effective_date')
            ));
            $toBillEuro = $billedEuro = $paidEuro = 0;
            if(!empty($invoices)){
                foreach($invoices as $invoice){
                    $dx = $invoice['ProjectBudgetInvoice'];
                    $toBillEuro += !empty($dx['billed']) ? $dx['billed'] : 0;
                    $paidEuro += !empty($dx['paid']) ? $dx['paid'] : 0;
                    if(!empty($dx['effective_date']) && $dx['effective_date'] != '0000-00-00'){
                        $billedEuro += !empty($dx['billed']) ? $dx['billed'] : 0;
                    }
                }
            }
            $saved = array(
                'project_id' => $datas['project_id'],
                'activity_id' => $datas['activity_id'],
                'sales_to_bill' => $toBillEuro,
                'sales_billed' => $billedEuro,
                'sales_paid' => $paidEuro
            );
            $last = $BudgetSyn->find('list', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('id', 'id')
            ));
            if( !empty($last) ){
                $BudgetSyn->updateAll($saved, array(
                    'ProjectBudgetSyn.id' => $last
                ));
            } else {
                $BudgetSyn->create();
                $BudgetSyn->save($saved);
            }
        }
    }

}
?>