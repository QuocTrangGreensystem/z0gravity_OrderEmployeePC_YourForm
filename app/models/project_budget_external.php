<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectBudgetExternal extends AppModel {

    var $name = 'ProjectBudgetExternal';
    
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
        'BudgetProvider' => array(
            'className' => 'BudgetProvider',
            'foreignKey' => 'budget_provider_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'BudgetType' => array(
            'className' => 'BudgetType',
            'foreignKey' => 'budget_type_id',
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
            $this->saveExternalToSyns($this->data[$this->alias]);
        }
    }
    
    /**
     * Save external into table project_budget_syns
     * 
     */
    public function saveExternalToSyns($datas = array()){
        $External = ClassRegistry::init('ProjectBudgetExternal');
        $BudgetSyn = ClassRegistry::init('ProjectBudgetSyn');
        if(!empty($datas) && (!empty($datas['project_id']) || !empty($datas['activity_id']))){
            if($datas['activity_id'] != 0){
                $conditions = array('activity_id' => $datas['activity_id']);
            } else {
                $conditions = array('project_id' => $datas['project_id']);
            }
            $externals = $External->find('all', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md')
            ));
            $budgetEuro = $forecastEuro = $varEuro = $orderEuro = $remainEuro = $manDay = $progressMd = $progressEuro = 0;
            $count = 0;
            if(!empty($externals)){
                foreach($externals as $external){
                    $dx = $external['ProjectBudgetExternal'];
                    $budgetEuro += !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                    $orderEuro += !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                    $remainEuro += !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    // tinh forecast
                    $_orderEuro = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                    $_remainEuro = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    $forecastEuro += ($_orderEuro + $_remainEuro);
                    $manDay += !empty($dx['man_day']) ? $dx['man_day'] : 0;
                    $progressMd += !empty($dx['progress_md']) ? $dx['progress_md'] : 0;
                    $count++;
                    // tinh progress euro
                    $_progressMd = !empty($dx['progress_md']) ? $dx['progress_md'] : 0;
                    $progressEuro += round(($_orderEuro*$_progressMd)/100, 2);
                }
            }
            //$progressMd = ($count == 0) ? 0 : round($progressMd/$count, 2);
            $progressMd = ($orderEuro == 0) ? 0 : round(($progressEuro/$orderEuro)*100, 2);
            // tinh var euro
            $varEuro = ($budgetEuro == 0) ? -100 : round(((($orderEuro + $remainEuro)/$budgetEuro)-1)*100, 2);
            $saved = array(
                'project_id' => $datas['project_id'],
                'activity_id' => $datas['activity_id'],
                'external_costs_budget' => $budgetEuro,
                'external_costs_forecast' => $forecastEuro,
                'external_costs_var' => $varEuro,
                'external_costs_ordered' => $orderEuro,
                'external_costs_remain' => $remainEuro,
                'external_costs_man_day' => $manDay,
                'external_costs_progress' => $progressMd,
                'external_costs_progress_euro' => $progressEuro
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