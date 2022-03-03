<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectBudgetSale extends AppModel {

    var $name = 'ProjectBudgetSale';
    
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
        'ProjectBudgetInvoice' => array(
            'className' => 'ProjectBudgetInvoice',
            'foreignKey' => 'project_budget_sale_id',
            'dependent' => true,
        )
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
        if(!empty($this->data[$this->alias]) && !empty($this->data[$this->alias]['project_id'])){
            $this->saveSaleToSyns($this->data[$this->alias]);
        }
    }
    
    /**
     * Save sales into table project_budget_syns
     * 
     */
    public function saveSaleToSyns($datas = array()){
        $Sale = ClassRegistry::init('ProjectBudgetSale');
        $BudgetSyn = ClassRegistry::init('ProjectBudgetSyn');
        if(!empty($datas) && (!empty($datas['project_id']) || !empty($datas['activity_id']))){
            if($datas['activity_id'] != 0){
                $conditions = array('activity_id' => $datas['activity_id']);
            } else {
                $conditions = array('project_id' => $datas['project_id']);
            }
            $sales = $Sale->find('all', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('sold', 'man_day')
            ));
            $soldEuro = $manDay = 0;
            if(!empty($sales)){
                foreach($sales as $sale){
                    $dx = $sale['ProjectBudgetSale'];
                    $soldEuro += !empty($dx['sold']) ? $dx['sold'] : 0;
                    $manDay += !empty($dx['man_day']) ? $dx['man_day'] : 0;
                }
            }
            $saved = array(
                'project_id' => $datas['project_id'],
                'activity_id' => $datas['activity_id'],
                'sales_sold' => $soldEuro,
                'sales_man_day' => $manDay
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