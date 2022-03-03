<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class NctWorkload extends AppModel {
	public $name = 'NctWorkload';
	public $actsAs = array('Lib');

	public function getNctWorkloadByResource($startDate, $endDate, $tasks, $resources)
	{
		foreach(array('day','month') as $type)
		{
			$conditions = array(
				'OR' => array(
					'project_task_id' => $tasks['projectTasks'],
					'activity_task_id' => $tasks['activityTasks']
				),
				'UNIX_TIMESTAMP(task_date) BETWEEN ? AND ?' => array($startDate,$endDate)
			);
			if(!empty($resources['pc']) && !empty($resources['employee']))
			{
				$conditions['AND'] = array(
					'OR' => array(
						array(
							'reference_id' => $resources['pc'],
							'is_profit_center' => 1
						),
						array(
							'reference_id' => $resources['employee'],
							'is_profit_center' => 0
						)
					)
				);
			}
			elseif(!empty($resources['employee']))
			{
				$conditions['reference_id'] = $resources['employee'];
				$conditions['is_profit_center'] = 0;
			}
			elseif(!empty($resources['pc']))
			{
				$conditions['reference_id'] = $resources['pc'];
				$conditions['is_profit_center'] = 1;
			}
			$fieldResult = $type == 'month' ? "01-%m-%Y" : "%d-%m-%Y";
			$fieldGroup = $type == 'month' ? "%m-%Y" : "%d-%m-%Y";
			$dataWorkload = $this->find('all',array(
					'recursive' => -1,
					'conditions' => $conditions,
					'fields'=>array(
						//'SUM(NctWorkload.estimated) AS val','UNIX_TIMESTAMP(DATE_FORMAT(task_date, "01-%m-%Y")) AS date'
						'SUM(NctWorkload.estimated) AS val',"DATE_FORMAT(task_date, '$fieldResult') AS date"
					),
					'order' => array('NctWorkload.task_date'),
					'group'=>array(
						"DATE_FORMAT(task_date, '$fieldGroup')"
					)
				)
			);
			$dataWorkloads[$type] = Set::combine($dataWorkload,'{n}.0.date','{n}.0.val');
		}
		return $dataWorkloads;
	}

	public function divideWorkload($workload, $start, $end, $rStart = false, $rEnd = false, $format = false){
		$result = array();
		$dateRange = $this->Behaviors->Lib->getListWorkingDays($start, $end, true);
        $number = count($dateRange);
        $divided = $this->Behaviors->Lib->caculateGlobal($workload, $number);
        $break = $divided['number'];
        $i = 1;
        foreach($dateRange as $date){
            $est = $divided['original'];
            if( $number - $i < $break ){
                $est = $divided['original'] + $divided['remainder'];
            }

            if( !$rStart || ( $rStart <= $date && $date <= $rEnd ) ){
            	if( $format ){
            		$result[date($format, $date)] = floatval($est);
            	} else {
            		$result[$date] = floatval($est);
            	}
            }

            $i++;
        }
        return $result;
	}
	public function divideWorkloadForResource($workload, $resource_id, $is_profit_center, $start, $end, $rStart = false, $rEnd = false, $format = false){
		$result = array();
		$this->loadModels('AbsenceRequest');
		$dateRange = $this->Behaviors->Lib->getListWorkingDays($start, $end, true);
		
		$absences = array();
		if($is_profit_center){
			
		}else{
			$absences = $this->AbsenceRequest->sumAbsenceByEmployeeAndDate($resource_id, $start, $end);
		}
		$count_absence = !empty($absences) ? array_shift($absences[$resource_id]) : 0;
		
        $number = (count($dateRange) > $count_absence) ? (count($dateRange) - $count_absence) : count($dateRange);
		
        $divided = $this->Behaviors->Lib->caculateGlobal($workload, $number);
        $break = $divided['number'];
        $i = 1;
        foreach($dateRange as $date){
            $est = $divided['original'];
            if( $number - $i < $break ){
                $est = $divided['original'] + $divided['remainder'];
            }

            if( !$rStart || ( $rStart <= $date && $date <= $rEnd ) ){
            	if( $format ){
            		$result[date($format, $date)] = floatval($est);
            	} else {
            		$result[$date] = floatval($est);
            	}
            }

            $i++;
        }
        return $result;
	}
}