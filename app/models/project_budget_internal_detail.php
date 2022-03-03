<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectBudgetInternalDetail extends AppModel {

    var $name = 'ProjectBudgetInternalDetail';
    
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
		'ProfitCenter' => array(
            'className' => 'ProfitCenter',
            'foreignKey' => 'profit_center_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'BudgetFunder' => array(
            'className' => 'BudgetFunder',
            'foreignKey' => 'funder_id',
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
        if(!empty($this->data[$this->alias]) && !empty($this->data[$this->alias]['project_id'])){
            $this->saveInternalDetailToSyns($this->data[$this->alias]);
        }
    }
    /**
     * Save project internal detail into table project_budget_syns
     * 
     */
    public function saveInternalDetailToSyns($datas = array()){
        $Internal = ClassRegistry::init('ProjectBudgetInternal');
        $InternalDetail = ClassRegistry::init('ProjectBudgetInternalDetail');
        $BudgetSyn = ClassRegistry::init('ProjectBudgetSyn');
        if(!empty($datas) && (!empty($datas['project_id']) || !empty($datas['activity_id']))){
            if($datas['activity_id'] != 0){
                $conditions = array('activity_id' => $datas['activity_id']);
            } else {
                $conditions = array('project_id' => $datas['project_id']);
            }
            //$internals = $Internal->find('first', array(
//                'recursive' => -1,
//                'conditions' => $conditions,
//                'fields' => array('average_daily_rate')
//            ));
//            $average = !empty($internals['ProjectBudgetInternal']['average_daily_rate']) ? $internals['ProjectBudgetInternal']['average_daily_rate'] : 0;
            $details = $InternalDetail->find('all', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('budget_md', 'average')
            ));
            $InternalBudget = 0;
            $count = $average = $internalBudgetManDay = 0;
            if(!empty($details)){
                foreach($details as $detail){
                    $dx = $detail['ProjectBudgetInternalDetail'];
                    if(empty($dx['budget_md'])){
                        $dx['budget_md'] = 0;
                    }
                    $internalBudgetManDay += $dx['budget_md'];
                    if(empty($dx['average'])){
                        $dx['average'] = 0;
                    }
                    $average += $dx['average'];
                    $InternalBudget += ($dx['budget_md']*$dx['average']);
                    $count++;
                }
            }
            $average = ($count == 0) ? 0 : round($average/$count, 2);
            $saved = array(
                'project_id' => $datas['project_id'],
                'activity_id' => $datas['activity_id'],
                // 'internal_costs_average' => $average,
                'internal_costs_budget' => $InternalBudget,
                'internal_costs_budget_man_day' => $internalBudgetManDay
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