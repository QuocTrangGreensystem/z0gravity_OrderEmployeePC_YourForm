<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectBudgetInternal extends AppModel {

    var $name = 'ProjectBudgetInternal';
    
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
        if(!empty($this->data[$this->alias])){
            $this->saveInternalToSyns($this->data[$this->alias]);
        }
    }
    
    /**
     * Save project internal into table project_budget_syns
     * 
     */
    public function saveInternalToSyns($datas = array()){
        $InternalDetail = ClassRegistry::init('ProjectBudgetInternalDetail');
        $BudgetSyn = ClassRegistry::init('ProjectBudgetSyn');
        if(!empty($datas) && (!empty($datas['project_id']) || !empty($datas['activity_id']))){
            //$average = !empty($datas['average_daily_rate']) ? $datas['average_daily_rate'] : 0;
            if($datas['activity_id'] != 0){
                $conditions = array('activity_id' => $datas['activity_id']);
            } else {
                $conditions = array('project_id' => $datas['project_id']);
            }
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
	/**
     * Save project internal into table project_budget_syns
     * $company_id use for check Permission
     */
    public function progress_line_manual_consumed($project_id = null, $company_id = null){
		$this->loadModels('CompanyConfig', 'ProjectTask', 'ProjectBudgetInternalDetail', 'ProjectBudgetSyn');
		/* Check conditions */
		$project = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $project_id),
			'fields' => array('id', 'company_id'),
		));
		if( empty($project)) return false;
		if( empty($company_id) || ($company_id != $project['Project']['company_id'])) return false;
		$is_manual_consumed = $this->CompanyConfig->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'CompanyConfig.cf_name' => 'manual_consumed',
				'CompanyConfig.company' => $company_id,
				'CompanyConfig.cf_value' => 1
				
			),
		));
		if( empty( $is_manual_consumed)) return false;
		/* END Check conditions */
		
		/* Get data */
		$valueInternals = $this->ProjectBudgetInternalDetail->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('validation_date', 'budget_md', 'budget_erro')
		));
		$projectBudgetSyn = $this->ProjectBudgetSyn->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average','internal_costs_forecast')
		));
		$project_tjm = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_average'];
		// debug($project_tjm); exit;
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $project_id,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$taskData = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array(
				'task_end_date',
				'estimated',
				'manual_consumed',
				'special_consumed',
			),
		));
		/* End Get data*/
		
		/* Parse Data */
		$dataset_internals = array();
		$default_data = array(
			'date' => 0,
			'date_format' => 0,
			'validated' => 0,
			// 'validated_price' => 0,
			'consumed' => 0,
			'consumed_price' => 0,
			'budget_md' => 0,
			'budget_price' => 0,
		);
		$remain = 0;
		$manual_consumed = 0;
		if( !empty( $taskData)){
			foreach($taskData as $task){
				$task = $task['ProjectTask'];
				$date_str = $task['task_end_date'];
				if( empty( $date_str) || ($date_str == '0000-00-00')) continue;
				$date = date_create_from_format('Y-m-d', $date_str);
				$date->modify('first day of this month');
				$timestamp = $date->getTimestamp();
				$data_month = (empty($dataset_internals[$timestamp]) ? $default_data : $dataset_internals[$timestamp]);
				$data_month['date'] = $timestamp;
				$data_month['date_format'] = $date->format('m/y');
				$data_month['validated'] += $task['estimated'];
				$data_month['consumed'] += $task['manual_consumed'];
				$data_month['consumed_price'] += ($task['manual_consumed'] * $project_tjm);
				$dataset_internals[$timestamp] = $data_month;
				$manual_consumed += $task['manual_consumed'];
				if( $task['estimated'] > $task['manual_consumed']){
					$remain += ($task['estimated'] - $task['manual_consumed']);
				}
			}
		}
		$validated_price = ($manual_consumed + $remain) * $project_tjm;
		// debug( $validated_price); exit;
		if( !empty( $valueInternals)){
			foreach($valueInternals as $interValue){
				$interValue = $interValue['ProjectBudgetInternalDetail'];
				$date_str = $interValue['validation_date'];
				if( empty( $date_str) || ($date_str == '0000-00-00')) continue;
				$date = date_create_from_format('Y-m-d', $date_str);
				$date->modify('first day of this month');
				$timestamp = $date->getTimestamp();
				$data_month = (empty($dataset_internals[$timestamp]) ? $default_data : $dataset_internals[$timestamp]);
				$data_month['date'] = $timestamp;
				$data_month['date_format'] = $date->format('m/y');
				$data_month['budget_md'] += (float)$interValue['budget_md'];
				$data_month['budget_price'] += (float)$interValue['budget_erro'];
				$dataset_internals[$timestamp] = $data_month;
			}
		}
		unset($valueInternals);
		// Cong don thang truoc sang thang sau
		$list_month = array_filter(array_keys($dataset_internals));
		$date = !empty($list_month) ? min($list_month) : strtotime(date('Y').'-02-01');
		$maxDate = !empty($list_month) ? max($list_month) : strtotime(date('Y').'-12-01');
		$first_month = strtotime('previous month', $date);
		$lastValue = array(
			'date' => $first_month,
			'date_format' => date( 'm/y' ,$first_month),
			'validated' => 0,
			'validated_price' => $validated_price,
			'consumed' => 0,
			'consumed_price' => 0,
			'budget_md' => 0,
			'budget_price' => 0,
		);
		// debug( $dataset_internals);
		// exit;
		$dataset_internals[$first_month] = $lastValue;
		$current = time();
		while( $date <= $maxDate){
			$data = isset($dataset_internals[$date]) ? $dataset_internals[$date] : array();
			$lastValue['date'] = $date;
			$lastValue['date_format'] = date('m/y', $date);
			$lastValue['validated'] += !empty($data['validated']) ? $data['validated'] : 0;
			$lastValue['consumed'] += !empty($data['consumed']) ? $data['consumed'] : 0;
			$lastValue['consumed_price'] += !empty($data['consumed_price']) ? $data['consumed_price'] : 0;
			$lastValue['budget_md'] += !empty($data['budget_md']) ? $data['budget_md'] : 0;
			$lastValue['budget_price'] += !empty($data['budget_price']) ? $data['budget_price'] : 0;
			$dataset_internals[$date] = $lastValue;
			$date = strtotime('next month', $date);
		}
		ksort($dataset_internals);
		// END Cong don thang truoc sang thang sau
		
		//get max value for display ( + 10%)
		$max_internal_md = round( max( $lastValue['validated'], $lastValue['consumed'], $lastValue['budget_md'])*1.1, 2); 
		$max_internal_euro = round( max( $lastValue['validated_price'], $lastValue['consumed_price'], $lastValue['budget_price'])*1.1, 2);
		$countLineIn = 0;
        if(!empty($dataset_internals)) $countLineIn = count($dataset_internals);
		/* END Parse Data */
		$internal_costs_forecast = $validated_price;
        return compact('dataset_internals', 'max_internal_md', 'max_internal_euro', 'countLineIn', 'internal_costs_forecast');
		
	}
}