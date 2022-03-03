<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Activity extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'Activity';
    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The information is not blank!'
            ),
            //'unique' => array(
//                'rule' => array('_uniqueName'),
//                'message' => 'The name is avaiable, please enter another!'
//            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The information is not blank!'
            )
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
        'Family' => array(
            'className' => 'ActivityFamily',
            'foreignKey' => 'family_id',
        ),
        'Subfamily' => array(
            'className' => 'ActivityFamily',
            'foreignKey' => 'subfamily_id'
        )
    );
    public $hasMany = array(
        'AccessibleProfit' => array(
            'className' => 'ActivityProfitRefer',
            'foreignKey' => 'activity_id',
            'conditions' => array(
                'AccessibleProfit.type' => 0
            ),
        ),
        'LinkedProfit' => array(
            'className' => 'ActivityProfitRefer',
            'foreignKey' => 'activity_id',
            'conditions' => array(
                'LinkedProfit.type' => 1
            ),
        ),
        'ActivityTask' => array(
            'className' => 'ActivityTask',
            'foreignKey' => 'activity_id',
            'dependent' => true,
        ),
        'TmpProfitCenterOfActivity' => array(
            'className' => 'TmpProfitCenterOfActivity',
            'foreignKey' => 'activity_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function _uniqueName() {
        if (empty($this->data[$this->alias]['name']) || empty($this->data[$this->alias]['company_id'])) {
            return false;
        }
        return $this->isUnique(array(
                    'name' => $this->data[$this->alias]['name'],
                    'company_id' => $this->data[$this->alias]['company_id']), false);
    }
    
    public function dataFromActivityTask($id, $company_id){
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $Activity = ClassRegistry::init('Activity');
        $ActivityTaskEmployeeRefer = ClassRegistry::init('ActivityTaskEmployeeRefer');
        //$ProjectTask = ClassRegistry::init('ProjectTask');
        //$ProjectTaskEmployeeRefer = ClassRegistry::init('ProjectTaskEmployeeRefer');
        //$Project= ClassRegistry::init('Project');
        $activityTasks = $ActivityTask->find("all", array(
            'fields' => array('id', 'estimated', 'parent_id'),
            'recursive' => -1,
            "conditions" => array('activity_id' => $id)
                )
        );
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $_parentIds = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.parent_id', '{n}.ActivityTask.parent_id') : array();
        foreach($_parentIds as $k => $value){
            if($value == 0){
                unset($_parentIds[$k]);
            }
        }
        $activityRequests = $ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'employee_id', 'task_id', 'SUM(value) as value'),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $_activityTaskId,
                'company_id' => $company_id,
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $newActivityRequests = array();
        foreach ($activityRequests as $key => $activityRequest) {
            $newActivityRequests[$activityRequest['ActivityRequest']['task_id']] = $activityRequest[0]['value'];
        }
        $activityRequests = $newActivityRequests;
        $total = 0;
        $sumEstimated = $sumRemain = $sumRemainConsumed = $sumRemainNotConsumed = $t = 0;
        foreach ($activityTasks as $key => $activityTask) {
			if(in_array($activityTask['ActivityTask']['id'], $_parentIds)){
				unset($activityTasks[$key]);
			}
			else{
				$activityTaskId = $activityTask['ActivityTask']['id'];
				//if($activityTask['ActivityTask']['parent_id'] == 0){
					//$sumEstimated += $activityTask['ActivityTask']['estimated'];
				//}
				$referencedEmployees = $ActivityTaskEmployeeRefer->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						"activity_task_id" => $activityTaskId
					)
				));
				if(count($referencedEmployees) == 0){} 
				else {
					foreach ($referencedEmployees as $key1 => $referencedEmployee) {
						$activityTasks[$key]['ActivityTask']['ActivityTaskEmployeeRefer'][] = $referencedEmployee['ActivityTaskEmployeeRefer'];
					}
				}
				$estimated = isset($activityTask['ActivityTask']['estimated']) ? $activityTask['ActivityTask']['estimated'] : 0;
				// Check if Activity Task Existed
				if (isset($activityTaskId)) {
					// Check if Request Existed
					if (isset($activityRequests[$activityTaskId])) {
						$consumed = $activityRequests[$activityTaskId];
						$completed = $estimated - $consumed;
						if($completed < 0){
							$completed = 0;
						}
						$sumRemainConsumed += $completed;
						$total += $consumed;
					} else {
						if(in_array($activityTask['ActivityTask']['id'], $_parentIds, true)){
							//unset($projectTask);
						} else {
							$sumRemainNotConsumed += $estimated;
						}
						$total += 0;
					}
				} else {
					// Error Handle
					$sumRemainNotConsumed += $estimated;
					$total += 0;
				}
			}
		}
        if(!empty($_parentIds)){
            foreach($_parentIds as $_parentId){
                $_consumed = !empty($activityRequests[$_parentId]) ? $activityRequests[$_parentId] : 0;
                $total += $_consumed;
            }
        }
        $sumRemain = $sumRemainConsumed + $sumRemainNotConsumed;
        $activityRequests = $ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'SUM(value) as consumed'
            ),
            'conditions' => array(
                'activity_id' => $id,
                'company_id' => $company_id,
                'status' => 2,
                'NOT' => array('value' => 0)
            )
        ));
        $_engaged = $_progression = 0;
        $_engaged = isset($activityRequests[0][0]['consumed']) ? $total + $activityRequests[0][0]['consumed'] : $total;
        $validated = $_engaged+$sumRemain;
        if($validated == 0){
            $_progression = 0;
        } else {
            $_progression = round((($_engaged*100)/$validated), 2);
        }
        if($_progression > 100){
            $_progression = 100;
        } else {
            $_progression = $_progression;
        }
        $_activityTask = array();
        $_activityTask['consumed'] = $_engaged;
        $_activityTask['remain'] = $sumRemain;
        $_activityTask['workload'] = $validated;
        $_activityTask['progression'] = $_progression;
        
        return $_activityTask;
    }
	public function getProjectLinkedActivities($activities)
	{
		$data = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $activities,
				'project IS NOT null'
			),
			'fields' => array('id','project')
		));
		return array_values($data);
	}
}
?>