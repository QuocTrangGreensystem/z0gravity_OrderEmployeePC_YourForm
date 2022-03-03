<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityRequest extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ActivityRequest';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'date' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The date is not blank!'
            ),
        ),
        'employee_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Employee is not blank!'
            )
        )
    );
	/*-------------------
	@Get lists consumed by day
	INPUT : conditions
	OUTPUT: Array values
	-------------------*/
	function getConsumedByDate($startDate, $endDate, $company, $activities, $employees = null, $tasks = null)
	{
		//return $this->getConsumedByDateUDonotUseSUM($startDate, $endDate, $company, $activities, $employees, $tasks);
		if(!empty($tasks))
		{
			$taskLists = $tasks;
		}
		else
		{
			$ActivityTask = ClassRegistry::init('ActivityTask');
			$taskLists = $ActivityTask->getActivityTaskNotSpecialAndNct($activities);
		}
		$taskLists = array_values($taskLists);
		$conditions = array(
			'OR' => array(
				array(
				'task_id' => $taskLists,
				'activity_id' => 0
				),
				array(
				'activity_id' => $activities,
				"task_id" => null
				)
			),
			'status' => 2,
			// 'activity_id' => $activities,
			// "task_id" => null,
			'date BETWEEN ? AND ?' => array($startDate, $endDate),
			'company_id' => $company
		);
		
		if(!empty($employees))
		{
			$conditions['employee_id'] = $employees;
		}
		$dataConsumed = $this->find('all',array(
				'recursive' => -1,
				'conditions' => $conditions,
				'fields'=>array(
					'SUM(ActivityRequest.value) AS val','date'
				),
				'order' => array('ActivityRequest.date'),
				'group'=>array(
					'ActivityRequest.date'
				)
			)
		);
		$dataConsumed = Set::combine($dataConsumed,'{n}.ActivityRequest.date','{n}.0.val');
		return $dataConsumed;
	}
	function getConsumedByDateFromDataRequest($startDate, $endDate, $company, $employees, $type = 'day')
	{
		if($type == 'day') {
			$_fields = array(
				'SUM(ActivityRequest.value) AS val','date'
			);
			$_groups = array('ActivityRequest.date');
		} else {
			$_fields = array(
				'SUM(ActivityRequest.value) AS val','FROM_UNIXTIME(`date`, "%v_%m_%Y") as `date`'
			);
			$_groups = array('FROM_UNIXTIME(`date`, "%v_%m_%Y")');
		}
		$conditions = array(
			'status' => 2,
			'date BETWEEN ? AND ?' => array($startDate, $endDate),
			'company_id' => $company
		);
		$conditions['employee_id'] = $employees;
		$dataConsumed = $this->find('all',array(
				'recursive' => -1,
				'conditions' => $conditions,
				'fields'=>$_fields,
				'order' => array('ActivityRequest.date'),
				'group'=>$_groups
			)
		);
		if($type == 'day')
		{
			$dataConsumed = Set::combine($dataConsumed,'{n}.ActivityRequest.date','{n}.0.val');
		}
		else
		{
			$dataConsumed = Set::combine($dataConsumed,'{n}.0.date','{n}.0.val');
		}
		return $dataConsumed;
	}
	function getConsumedByDateUDonotUseSUM($startDate, $endDate, $company, $activities, $employees = null, $tasks = null)
	{
		$ActivityTask = ClassRegistry::init('ActivityTask');
		$data = array();
		foreach($activities as $activity)
		{
			
			$taskLists = $ActivityTask->getActivityTaskNotSpecialAndNct($activity);
			$conditions = array(
				'OR' => array(
					'task_id' => $taskLists,
					'activity_id' => $activity
				),
				'status' => 2,
				//'NOT' => array('value' => 0, "task_id" => null),
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'company_id' => $company
			);
			if(!empty($employees))
			{
				$conditions['employee_id'] = $employees;
			}
			$dataConsumed = $this->find('all',array(
					'recursive' => -1,
					'conditions' => $conditions,
					'fields'=>array(
						'SUM(ActivityRequest.value) AS val','date'
					),
					'order' => array('ActivityRequest.date'),
					'group'=>array(
						'ActivityRequest.date'
					)
				)
			);
			$data = array_merge($data,$dataConsumed);
		}
		$results= array();
		foreach($data as $_datas)
		{
			$_data = array_merge($_datas[0],$_datas['ActivityRequest']);
			if(!isset($results[$_data['date']]))
			{
				$results[$_data['date']] = 0;
			}
			$results[$_data['date']] += $_data['val'];
		}
		return $results;
	}
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
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
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
        'ActivityTask' => array(
            'className' => 'ActivityTask',
            'foreignKey' => 'task_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    ); 
}
?>