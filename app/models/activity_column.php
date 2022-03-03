<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityColumn extends AppModel {

    /**
     * Options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ActivityColumn';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'The information name is not blank!',
                'allowEmpty' => false
            ),
        ),
        'key' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'The information name is not blank!',
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
        
    protected function _initOptions($company,$getValueField=null) {
        if (!$this->_options) {
            $this->_options = array(
                'id' => array(
                    'name' => __('ID', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 1,
                    'code' => 1
                ),
                'name' => array(
                    'name' => __('Name', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 2,
                    'code' => 2
                ),
                'long_name' => array(
                    'name' => __('Long Name', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 3,
                    'code' => 3
                ),
                'short_name' => array(
                    'name' => __('Short Name', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 4,
                    'code' => 4
                ),
                'family_id' => array(
                    'name' => __('Family', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 5,
                    'code' => 5
                ),
                'subfamily_id' => array(
                    'name' => __('Subfamily', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 6,
                    'code' => 6
                ),
                'pms' => array(
                    'name' => __('PMS', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 7,
                    'code' => 7
                ),
                'accessible_profit' => array(
                    'name' => __('Profit Accessible', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 8,
                    'code' => 8
                ),
                'linked_profit' => array(
                    'name' => __('Profit Linked', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 9,
                    'code' => 9
                ),
                'code1' => array(
                    'name' => __('Code 1', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 10,
                    'code' => 10
                ),
                'code2' => array(
                    'name' => __('Code 2', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 11,
                    'code' => 11
                ),
                'code3' => array(
                    'name' => __('Code 3', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 12,
                    'code' => 12
                ),
                'start_date' => array(
                    'name' => __('Start date', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 13,
                    'code' => 13
                ),
                'end_date' => array(
                    'name' => __('End date', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 14,
                    'code' => 14
                ),
                'price' => array(
                    'name' => __('Price', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 15,
                    'code' => 15
                ),
                'md' => array(
                    'name' => __('M.D', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 16,
                    'code' => 16
                ),
                'consumed' => array(
                    'name' => __('Consumed', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 17,
                    'code' => 17
                ),
                'raf' => array(
                    'name' => __('RAF', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 18,
                    'code' => 18
                ),
                'forecast' => array(
                    'name' => __('Forecast', true),
                    'description' => '',
                    'calculate' => 'C17+C18',
                    'display' => true,
                    'weight' => 19,
                    'code' => 19
                ),
                'avancement' => array(
                    'name' => __('Avancement', true),
                    'description' => '',
                    'calculate' => 'C17/C19',
                    'display' => true,
                    'weight' => 20,
                    'code' => 20
                ),
                'real_price' => array(
                    'name' => __('Real price', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 21,
                    'code' => 21
                ),
                'actif' => array(
                    'name' => __('Open', true),
                    'description' => '',
                    'calculate' => '',
                    'display' => true,
                    'weight' => 22,
                    'code' => 22
                ),
                'workload' => array(
                    'name' => __('Workload', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 46,
                    'code' => 46
                ),
                'overload' => array(
                    'name' => __('Overload', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 47,
                    'code' => 47
                ),
                'completed' => array(
                    'name' => __('Completed', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 48,
                    'code' => 48
                ),
                'remain' => array(
                    'name' => __('Remain', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 49,
                    'code' => 49
                ),
                /**
                 * Them 25 record cho phan budget ngay 12/03/2013
                 * Va 2 record cho phan assign to profit center va employee o project task va activity task
                 */
                'sales_sold' => array(
                    'name' => __('Sales Sold', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 53,
                    'code' => 53
                ),
                'sales_to_bill' => array(
                    'name' => __('Sales To Bill', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 54,
                    'code' => 54
                ),
                'sales_billed' => array(
                    'name' => __('Sales Billed', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 55,
                    'code' => 55
                ),
                'sales_paid' => array(
                    'name' => __('Sales Paid', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 56,
                    'code' => 56
                ),
                'sales_man_day' => array(
                    'name' => __('Sales M.D', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 57,
                    'code' => 57
                ),
                'total_costs_budget' => array(
                    'name' => __('Total Costs Budget', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 58,
                    'code' => 58
                ),
                'total_costs_forecast' => array(
                    'name' => __('Total Costs Forecast', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 59,
                    'code' => 59
                ),
                'total_costs_var' => array(
                    'name' => __('Total Costs Var %', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 60,
                    'code' => 60
                ),
                'total_costs_engaged' => array(
                    'name' => __('Total Costs Engaged', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 61,
                    'code' => 61
                ),
                'total_costs_remain' => array(
                    'name' => __('Total Costs Remain', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 62,
                    'code' => 62
                ),
                'total_costs_man_day' => array(
                    'name' => __('Total Costs M.D', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 63,
                    'code' => 63
                ),
                'internal_costs_budget' => array(
                    'name' => __('Internal Costs Budget', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 64,
                    'code' => 64
                ),
                'internal_costs_forecast' => array(
                    'name' => __('Internal Costs Forecast', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 65,
                    'code' => 65
                ),
                'internal_costs_var' => array(
                    'name' => __('Internal Costs Var %', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 66,
                    'code' => 66
                ),
                'internal_costs_engaged' => array(
                    'name' => __('Internal Costs Engaged', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 67,
                    'code' => 67
                ),
                'internal_costs_remain' => array(
                    'name' => __('Internal Costs Remain', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 68,
                    'code' => 68
                ),
                'internal_costs_forecasted_man_day' => array(
                    'name' => __('Internal Costs Forecasted M.D', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 69,
                    'code' => 69
                ),
                'external_costs_budget' => array(
                    'name' => __('External Costs Budget', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 70,
                    'code' => 70
                ),
                'external_costs_forecast' => array(
                    'name' => __('External Costs Forecast', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 71,
                    'code' => 71
                ),
                'external_costs_var' => array(
                    'name' => __('External Costs Var %', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 72,
                    'code' => 72
                ),
                'external_costs_ordered' => array(
                    'name' => __('External Costs Ordered', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 73,
                    'code' => 73
                ),
                'external_costs_remain' => array(
                    'name' => __('External Costs Remain', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 74,
                    'code' => 74
                ),
                'external_costs_man_day' => array(
                    'name' => __('External Costs M.D', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 75,
                    'code' => 75
                ),
                'external_costs_progress' => array(
                    'name' => __('External Costs Progress %', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 76,
                    'code' => 76
                ),
                'external_costs_progress_euro' => array(
                    'name' => __('External Costs Progress', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 77,
                    'code' => 77
                ),
                'assign_to_profit_center' => array(
                    'name' => __('% Assigned to profit center', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 78,
                    'code' => 78
                ),
                'assign_to_employee' => array(
                    'name' => __('% Assigned to employee', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 79,
                    'code' => 79
                ),
                'consumed_current_year' => array(
                    'name' => __('Consumed current year', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 80,
                    'code' => 80
                ),
                'consumed_current_month' => array(
                    'name' => __('Consumed current month', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 81,
                    'code' => 81
                ),
                'budget_customer_id' => array(
                    'name' => __('Customer', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 82,
                    'code' => 82
                ),
                'project_manager_id' => array(
                    'name' => __('Poject Manager', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 83,
                    'code' => 83
                ),
                'internal_costs_budget_man_day' => array(
                    'name' => __('Internal Costs Budget M.D', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 84,
                    'code' => 84
                ),
                'internal_costs_average' => array(
                    'name' => __('Internal Costs Average', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 85,
                    'code' => 85
                ),
                'import_code' => array(
                    'name' => __('Import Code', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 86,
                    'code' => 86
                ),
                'code4' => array(
                    'name' => __('Code 4', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 87,
                    'code' => 87
                ),
                'code5' => array(
                    'name' => __('Code 5', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 88,
                    'code' => 88
                ),
                'code6' => array(
                    'name' => __('Code 6', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 89,
                    'code' => 89
                ),
                'code7' => array(
                    'name' => __('Code 7', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 90,
                    'code' => 90
                ),
                'code8' => array(
                    'name' => __('Code 8', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 91,
                    'code' => 91
                ),
                'code9' => array(
                    'name' => __('Code 9', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 92,
                    'code' => 92
                ),
                'code10' => array(
                    'name' => __('Code 10', true),
                    'description' => '',
                    'calculate' => false,
                    'display' => true,
                    'weight' => 93,
                    'code' => 93
                )
            );
			$_code = 94;
			$valueFields = array();
			if($getValueField||!$company)
			{
				$monthArray=array('Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec');
				foreach($monthArray as $value)
				{
					$id=strtolower($value);
					$valueFields['workload_'.$id]=array(
						'name'=>'Workload '.$value,
						'description' => 'In personalized view',
						'calculate' => false,
						'display' => false,
						'weight' => $_code,
						'code' => $_code);
					$_code+=1;
					$valueFields['consumed_'.$id]=array(
						'name'=>'Consumed '.$value,
						'description' => 'In personalized view',
						'calculate' => false,
						'display' => false,
						'weight' => $_code,
						'code' => $_code);
					$_code+=1;
				}
			}
			//debug($_code); exit;
			//add workload, consumed cua 3 nam truoc va 3 nam sau cua nam hien tai
            $listDatas = array(
                'workload_y' => __('Workload', true) . ' Y',
                'workload_last_one_y' => __('Workload', true) . ' Y-1',
                'workload_last_two_y' => __('Workload', true) . ' Y-2',
                'workload_last_thr_y' => __('Workload', true) . ' Y-3',
                'workload_next_one_y' => __('Workload', true) . ' Y+1',
                'workload_next_two_y' => __('Workload', true) . ' Y+2',
                'workload_next_thr_y' => __('Workload', true) . ' Y+3',
                'consumed_y' => __('Consumed', true) . ' Y',
                'consumed_last_one_y' => __('Consumed', true) . ' Y-1',
                'consumed_last_two_y' => __('Consumed', true) . ' Y-2',
                'consumed_last_thr_y' => __('Consumed', true) . ' Y-3',
                'consumed_next_one_y' => __('Consumed', true) . ' Y+1',
                'consumed_next_two_y' => __('Consumed', true) . ' Y+2',
                'consumed_next_thr_y' => __('Consumed', true) . ' Y+3',
            );
			//debug($listDatas); exit;
            foreach($listDatas as $keyList => $nameList){
                $valueFields[$keyList] = array(
					'name' => $nameList,
					'description' => '',
					'calculate' => false,
					'display' => false,
					'weight' => $_code,
					'code' => $_code);
				$_code+=1;
            }            
            //debug($_code); exit;
			//debug($valueFields);exit;
			$valueFields1 = array();
			//debug($valueFields);exit;
			/**
			* Update by QUANNGUYEN 19/02/2019
			*/
			if($getValueField||!$company)
			{
			$quaterArray=array('First','Second','Third','Fourth');
				foreach($quaterArray as $value)
				{
					$id=strtolower($value);
					$valueFields1['workload_'.$id]=array(
						'name'=>'Workload '.$value.' Quater',
						'description' => 'In personalized view',
						'calculate' => false,
						'display' => false,
						'weight' => $_code,
						'code' => $_code);
					$_code+=1;
					$valueFields1['consumed_'.$id]=array(
						'name'=>'Consumed '.$value.' Quater',
						'description' => 'In personalized view',
						'calculate' => false,
						'display' => false,
						'weight' => $_code,
						'code' => $_code);
					$_code+=1;
				}
			}
			
			$valueFields2 = array();
			if($getValueField||!$company)
			{
			$semesterArray=array('Firsts','Seconds');
				foreach($semesterArray as $value)
				{
					$id=strtolower($value);
					$valueFields2['workload_'.$id]=array(
						'name'=>'Workload '.$value.' Semester',
						'description' => 'In personalized view',
						'calculate' => false,
						'display' => false,
						'weight' => $_code,
						'code' => $_code);
					$_code+=1;
					$valueFields2['consumed_'.$id]=array(
						'name'=>'Consumed '.$value.' Semester',
						'description' => 'In personalized view',
						'calculate' => false,
						'display' => false,
						'weight' => $_code,
						'code' => $_code);
					$_code+=1;
					//debug( $_code);
				}
			}
			
			//debug($valueFields);
			$valueFields['manual_consumed'] = array(
                'name' => 'Manual Consumed',
                'description' => '',
                'display' => 0,
                'weight' => $_code,
                'code' => $_code
            );
            $_code+=1;
			//debug($valueFields);exit;
            foreach (range(23, 45) as $column) {
                $_opt = array(
                    'name' => 'C ' . $column,
                    'description' => '<input>',
                    'calculate' => false,
                    'display' => false,
                    'weight' => $column,
                    'code' => $column
                );
                if ($column < 32) {
                    $_opt['calculate'] = $_opt['description'] = '';
                }
                $this->_options['c' . $column] = $_opt;
            }
			$this->_options=array_merge($this->_options,$valueFields,$valueFields1,$valueFields2);
        }
			/**
			* End update by QUANNGUYEN 19/02/2019
			*/
        if ($company && $this->find('count', array('recursive' => -1,'order' => 'weight  ASC', 'conditions' => array('company_id' => $company))) != count($this->_options)) {
            foreach ($this->_options as $key => $value) {
                $data = array('key' => $key,'company_id' => $company);
                if (!$this->find('count', array('recursive' => -1, 'conditions' => $data))) {
					$data = array('key' => $key, 'name' => $value['name'], 'description' => $value['description'], 'display' => $value['display'], 'company_id' => $company);
                    $this->create();
                    $this->save(array_merge($data, array(
                                'weight' => intval($value['weight']))), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        return $this->_options;
    }

    public function getOptions($company=null,$getValueField=false) {
        $options = $this->_initOptions($company,$getValueField);
        if ($company) {
			$datas = $this->find('all', array(
					'order' => array('weight' => 'ASC'),
					'recursive' => -1,
					'conditions' => array('company_id' => $company)));
            foreach ($datas as $data) {
                $data = array_shift($data);
                if (!isset($options[$data['key']])) {
                    continue;
                }
                foreach (array('name', 'calculate', 'display', 'weight', 'description', 'key') as $field) {
                    if ((isset($data[$field]) && $field == 'display') || !empty($data[$field])) {
                        if ($field == 'calculate' && $options[$data['key']]['calculate'] === false) {
                            continue;
                        }
                        $options[$data['key']][$field] = $data[$field];
                    }
                }
            }
            $keys = array();
            foreach ($options as $key => $value) {
                $keys[$key] = $value['weight'];
            }
            asort($keys);
            $options = array_merge($keys, $options);
        }
        return $options;
    }
	public function getViewFieldNames($company) {
        $fieldDeletes = array(
        );
		$listField = $this->getOptions($company,true);
		$fieldDefault = array(
			'project'=>'Project',
			'project_manager_id'=>'Project Manager',
			'budget_customer_id'=>'Customer',
			//'workload_current_year'=>'Workload Current Year',
			'activated'=>'Activated',
        );
		foreach($listField as $key=>$value)
		{
			$listField[$key]=$listField[$key]['name'];
		}
		$listField = array_merge($listField,$fieldDefault);
        $fieldset = array();
        foreach (array_keys($listField) as $name) {
            if(in_array($name, $fieldDeletes)){
                //do nothing
            } else {
				$fieldset[$name] = $listField[$name];
            }
        }
        return $fieldset;
		
    }
	public function parseViewField($company,$columns) {

		$listField = $this->getOptions($company,true);
		$fieldDefault = array(
			'project'=>array('name'=>'Project'),
			'project_manager_id'=>array('name'=>'Project Manager'),
			'budget_customer_id'=>array('name'=>'Customer'),
			//'workload_current_year'=>array('name'=>'Workload Current Year'),
			'activated'=>array('name'=>'Activated')
        );
		$valueFields = array();
		/*$monthArray=array('Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec');
		foreach($monthArray as $value)
		{
			$id=strtolower($value);
			$valueFields['workload_'.$id]=array('name'=>'Workload','month'=>$value);
			$valueFields['consumed_'.$id]=array('name'=>'Consumed','month'=>$value);
		}*/
		$listField = array_merge($listField,$valueFields,$fieldDefault);
		$results=array();
		if(!empty($columns))
		{
			foreach($columns as $val)
			{
				foreach($listField as $key=>$value)
				{
					if($val==$key)
					{
						$results[$val]=$listField[$key];
					}
				}
			}
		}
        return $results;	
    }
}
?>